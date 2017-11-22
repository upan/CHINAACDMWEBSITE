<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author MAGENJIE(magenjie@feeyo.com)
 * @datetime 2017-08-23T15:50:18+0800
 */
class Login_model extends ACDM_Model { 
	const maxfailedtimes = 5;
    public function __construct(){
		parent::__construct();
		$this->table = 'admin_user';
	}

	public function login($username,$password){
		$r = $this->check($username,$password);
		if((int)$r['status'] === 1){
			$nav = $this->getNav($r['gid'],$r['id'],$r['is_system']);
			if (!$nav) {
				return array('status'=>FALSE,'msg'=>'用户所属权限异常!请联系管理员');
			}
			$r['nav'] = $nav;
			if ($r['id'] == 1 && $r['gid'] == 0 && $r['is_system'] == 1) { // 超管判断
				$r['auth'] = $this->getSuperAuth();
				$r['group_name'] = "超级管理员";
				$r['is_super'] = 1;
			}else{
				if ($r['is_system'] == 1) {//系统管理员
					$group = array('name'=>'系统管理员','auth'=>$this->getSysAdminAuth("string"));
				}else{
					$group = $this->get_one(array('id'=>$r['gid']),'admin_group');	
				}
				$r['auth'] = $this->merge_auth($group['auth']);
				$r['group_name'] = $group['name'];
				$r['is_super'] = 0;
			}
			$r['uid'] = $r['id'];
			unset($r['id']);
			$this->session->set_userdata($r);
			//$this->session->mark_as_temp('auth',86400);
			return array('status'=>TRUE,'msg'=>'登录成功');
		}
		return $r;
	}

	public function check($username,$password){
		$r = $this->get_one(array('username'=>$username));
		if(!$r) return array('status'=>FALSE,'msg'=>'用户名不正确');
		if((int)$r['status'] == 0) return array('status'=>FALSE,'msg'=>'账号已被禁用！具体信息请联系管理员');
		$_ = time() - (int)$r['last_login_time'];
		if($_ > 3600){
			$this->update(array('today_login_fail'=>0,'last_login_time'=>time()),array('username'=>$username)) && $r['today_login_fail'] = 0;
		}
		if($r['password'] === $password) {
			if((int)$r['today_login_fail'] < self::maxfailedtimes) {
				$r['last_login_ip'] = $this->input->ip_address();
				$r['last_login_time'] = time();
				$this->update(array('last_login_time'=>$r['last_login_time'],'last_login_ip'=>$r['last_login_ip']),array('username'=>$username));
				return $r;
			}else{
				return $this->unLockUser($r['last_login_time'],$username);
			}
		}else{
			if((int)$r['today_login_fail'] < self::maxfailedtimes) {
				$today_login_fail = (int)$r['today_login_fail'] + 1;
				$times = self::maxfailedtimes - $today_login_fail;
				$this->update(array('today_login_fail'=>$today_login_fail),array('username'=>$username),'',FALSE,TRUE);
				$tip = $times ? "密码错误您还有{$times}次机会" : "帐号已被锁定,请1小时后再尝试登录!";
				return array('status'=>FALSE,'msg'=>$tip);
			}else{
				return $this->unLockUser($r['last_login_time'],$username);
			}
		}
	}

	/**
	 * [unLockUser 解锁或锁定用户 因尝试多次错误密码而锁定的账号]
	 * @author MAGENJIE(1299234033@qq.com)
	 * @datetime 2016-10-30T14:58:36+0800
	 */
	public function unLockUser($last_login_time,$username){
		$_ = time() - (int)$last_login_time;
		if($_ > 3600){
			$this->update(array('today_login_fail'=>1),array('username'=>$username));
			return array('status'=>FALSE,'msg'=>' 密码错误您还有4次机会');
		}
		return array('status'=>FALSE,'msg'=>'帐号已被锁定,请1小时后再尝试登录!');
	}

	/**
	 * [systemDefaultAuth 系统默认权限]
	 * @author MAGENJIE(1299234033@qq.com)
	 * @datetime 2016-11-02T22:17:13+0800
	 */
	public function systemDefaultAuth(){
		$auth = $this->simple_select(array('status'=>1,'auth_limit'=>0),'id desc','admin_auth');
        return array_column($auth,'id');
	}

	public function merge_auth($group_auth){
		$group_auth = array_filter(explode(",",$group_auth));
		$default_auth = $this->systemDefaultAuth();
		$auth = array_unique(array_merge($group_auth,$default_auth));
		return $auth;
	}

	public function getSuperAuth($return_type = "array"){
		$ids = $this->simple_select(array('status'=>1),'id desc','admin_auth');
		$auth = $return_type === "string" ? join(",",array_column($ids,'id')) : array_column($ids,'id');
		return $auth;
	}

	public function getSysAdminAuth($return_type = "array"){
		$second_nav = $this->simple_select(array('status'=>1,'pid<>'=>0),'id desc','admin_nav');
        $result = $this->simple_select(array('status'=>1,),'id desc','admin_auth','id');
        $auth = $loop = $auth_list = array();
        foreach ($result as $key => $value) {
        	$pid = $value['pid'];
            foreach (explode(",",$value['parent_node']) as $parent_node_id) {
        	   $auth_list[$pid][$parent_node_id][] = $value; 
            }
        }
        foreach ($second_nav as $key => $value) {
        	$id = $value['id'];
            // 超管判断 权限管理不显示在分组权限中 只有超级管理员才有权限
            if ($value['pid'] == 2 || $value['id'] == 2 || $value['status'] == 0) {
                continue;
            }
            $node_list = isset($auth_list[$id]) ? $auth_list[$id] : array();
            foreach ($node_list as $parent_node_id => $node) { // 与关联节点权限绑定
            	$n = array_column($node,"id");
                if (empty($parent_node_id)) {
                	$auth = array_merge($auth,$n);
                }else{
                	if (in_array($parent_node_id, $auth)) {
                		$auth = array_merge($auth,$n);
                	}else{
                		$loop[$parent_node_id] = isset($loop[$parent_node_id]) ? array_merge($loop[$parent_node_id],$n) : $n;
                	}
                }
            }
        }
        foreach ($loop as $pid => $node) {
        	if (in_array($pid, $auth)) {
        		$auth = array_merge($auth,$node);
        		unset($loop[$pid]);
        	}
        }
        if ($return_type === "array") {
        	return $auth;
        }else{
        	return $auth_string = join(",",$auth);
        }
	}
	
	public function getNav($gid,$uid,$is_system = FALSE){
		$r = FALSE;
		if ($gid == 0 && $uid == 1 && $is_system == 1) {
			$r = array('status'=>1,'auth'=>$this->getSuperAuth("string")); //超管判断
		}else{
			if ($is_system) {
				$r = array('status'=>1,'auth'=>$this->getSysAdminAuth("string"));
			}else{
				$r = $this->get_one(array('id'=>$gid),'admin_group');
			}
		}
        !$r && $this->json_exit(array('status'=>FALSE,'msg'=>'没有找到该用户'));
		if ($r['status'] == 0 || empty($r['auth'])) {//系统默认权限
			$auth = $this->systemDefaultAuth();
		}else{
			$auth = array_filter(explode(",",$r['auth']));
			$auth = array_merge($auth,$this->systemDefaultAuth());
		}
		if (empty($auth)) {
			return FALSE;
		}
		$first = $second = $third = array();
		$auth = array_unique($auth);
		foreach ($auth as $key => $value) {
			$_third = $this->get_one(array('id'=>$value),'admin_auth');
			if ($_third['status'] == 0) continue;
			$third[$_third['id']] = $_third;
			$_second = $this->get_one(array('id'=>$_third['pid']),'admin_nav');
			if ($_second['status'] == 0) continue;
			$second[$_second['id']] = $_second;
			$_first = $this->get_one(array('id'=>$_second['pid']),'admin_nav');
			if ($_first['status'] == 0) continue;
			$first[$_first['id']] = $_first;
		}
		$third_sort = array();
		foreach ($third as $key => $value) {
			$second[$value['pid']]['third'][] = $value;
			$third_sort[$value['pid']][] = $value['sort'];
		}
		$second_sort = array();
		foreach ($second as $key => $value) {
			array_multisort($third_sort[$key],SORT_ASC,SORT_NUMERIC ,$value['third']);
			$second[$key]['third'] = $value['third'];
			$first[$value['pid']]['second'][] = $value;
			$second_sort[$value['pid']][] = $value['sort'];
		}
		$first_sort = array();
		foreach ($first as $key => $value) {
			array_multisort($second_sort[$key],SORT_ASC,SORT_NUMERIC ,$value['second']);
			$first[$key]['second'] = $value['second'];
			$first_sort[] = $value['sort'];
		}
		array_multisort($first_sort,SORT_ASC,SORT_NUMERIC ,$first);
		$first = array_column($first,NULL,'id');
		return $first;
    }
}