<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author MAGENJIE(magenjie@feeyo.com)
 * @datetime 2017-08-23T15:50:18+0800
 */
class Admin_model extends ACDM_Model {
    public function __construct(){
        parent::__construct();
    }

    /**
	 * [write_operation_log 记录管理员操作日志]
	 * @author MAGENJIE(1299234033@qq.com)
	 * @datetime 2016-10-30T14:40:45+0800
	 */
    public function write_log($uri,$desc,$params){
        $uid = $this->session->uid;
        $uid ? : $uid = 0;
        $username = $this->session->username;
        $username ? : $username = "";
        $ip = $this->session->last_login_ip;
        $ip ? : $ip = "";
        $data = array(
            "uid"           =>$uid,
            "uri"           =>$uri,
            "ip"            =>$ip,
            "time"          =>time(),
            "username"      =>$username,
            "params"        =>json_encode($params),//提交的参数 json格式
            "description"   =>$desc,
        );
        $this->insert($data,"admin_log");
	}

    //编辑/删除 以及其他操作按钮 根据是否有权限来生成
    //css: btn-default btn-primary
    public function gen_a_button($uri,$css,$label,$params=''){
        $params ? $url = base_url("{$uri}/{$params}") : $url = base_url($uri);
        $a_button = $this->checkAuth($uri) ? sprintf("<a href=\"%s\" class=\"btn %s\" >%s</a>", $url, $css, $label) : "";
        return $a_button;
    }

    public function gen_button($uri,$css,$label,$call){
        $button = $this->checkAuth($uri) ? sprintf("<button class=\"btn %s\" onclick=\"%s\" >%s</button>",$css,$call,$label) : "";
        return $button;
    }

    /**
     * [checkAuth 根据uri判断是否有权限访问]
     * @author MAGENJIE(magenjie@feeyo.com)
     * @datetime 2016-10-31T09:57:58+0800
     * uri:路由段 tag:true:检查uri 是否有权限;
     */
    public function checkAuth($uri){
        $gid = $this->session->gid;
        $session_auth = $this->session->auth;
        !$session_auth && $this->noAuthRedirect("登录已过期,请重新登录！",TRUE);
        $info = $this->get_one(array('uri'=>$uri,'status'=>1),'admin_auth');
        if (!$info){
            return FALSE;
        }
        //权限继承;
        $parent_node_id = empty($info["parent_node"]) ? array() : explode(",",$info["parent_node"]); 
        if (!empty($parent_node_id)) {
            $parent_node_result = $this->db->select("*")->from("admin_auth")->where(array("status"=>1))->where_in("id",$parent_node_id)->get()->result_array();
            if (empty($parent_node_result)) {//父节点是否存在;
                return FALSE;
            }
        }
        $group_r = array();
        if ($this->session->is_super) { // 超管判断
            $group_r['auth'] = $this->m_login->getSuperAuth("string");
        }else{
            $group_r = $this->get_one(array('id'=>$gid),'admin_group');
            //系统级的管理员
            $this->session->is_system && $group_r['auth'] = $this->m_login->getSysAdminAuth("string");
        }
        empty($group_r) && $this->noAuthRedirect("用户角色权限异常,请重新登录！",TRUE);
        $auth = $this->m_login->merge_auth($group_r['auth']);//合并系统默认权限 重新获取的权限数组
        // 已知问题：add edit 页面都有 upload 方法,但upload 只继承edit节点,如何限制 upload 在add中使用？ auth 数组中 有 edit 节点 与 upload 节点;
        // 解决1：将当前使用upload 方法的页面url 传入check_auth; 解决2：add edit 中的upload 各自使用方法;
        if (empty(array_diff($session_auth,$auth))) { //判断当前节点是否有权限
            $tag = in_array($info['id'],$auth);
            //var_dump($tag,"BEFORE",$info['id'],$auth,$uri);
            foreach ($parent_node_id as $pid) {// 判断父节点是否在用户权限节点中;
                $tag = $tag || in_array($pid,$auth);
            }
            //var_dump($tag,"AFTER");
        }else{
            $tag = FALSE;
        }
        return $tag;
    }

}