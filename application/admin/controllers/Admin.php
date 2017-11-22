<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author MAGENJIE(magenjie@feeyo.com)
 * @datetime 2017-08-23T15:50:18+0800
 */
class Admin extends Admin_Controller {

    public function __construct(){
        parent::__construct();
    }

    //站点信息 登录用户信息;
    public function index(){	
		$this->view();
	}
	
	public function icon(){
		$this->view();
	}

	public function nav_list(){
		$info = $this->m_admin->simple_select(array('status'=>1),'sort asc','admin_nav');
		$list = array();
		foreach ($info as $value) {
			if ($value['pid'] == 0) {
				foreach ($info as $val) {
					if ($val['pid'] == $value['id']) $value['child'][] = $val;
				}
				$list[] = $value;
			}
		}
		$this->view(array("list"=>$list));
	}

	public function nav_add($id = 0){
		$id = (int)$id;
		if ($this->input->is_ajax_request()) {
			$post = $this->input->post();
			$id = isset($post['id']) ? (int)$post['id'] : 0;
			unset($post['id']);
			if($id){
				$tag = $this->m_admin->update($post,array('id'=>$id),'admin_nav');
			}else{
				$tag = $this->m_admin->insert($post,'admin_nav');
			}
	        $response = $this->_format_response($tag,"导航{$post['name']}",$id);
	        $this->_log_desc = $response["msg"];
			$this->json_exit($response);
		}else{
			if($id){
				$info = $this->m_admin->get_one(array('id'=>$id),'admin_nav');
			}else{
				$info = array("name"=>"","status"=>1,"icon"=>"","sort"=>255,"pid"=>0,"id"=>0);
			}
			empty($info) && redirect(base_url('admin/admin_list'));
			$first_nav = $this->m_admin->simple_select(array('pid'=>0,'status'=>1),'sort asc','admin_nav');
			$data = array(
				"info" => $info,
				"first_nav" => $first_nav,
			);
			$this->view($data);
		}
	}

	public function nav_del($id=0){
		$id = (int)$id;
		empty($id) && $this->json_exit();
		$nav = $this->m_admin->get_one(array("id"=>$id),"admin_nav");
		empty($nav) && $this->json_exit(array("status"=>FALSE,"msg"=>"导航不存在"));
		$tag = $this->m_admin->delete(array('id'=>$id),'admin_nav');
        $response = $this->_format_response($tag,"删除导航 {$nav["name"]}");
        $this->_log_desc = $response["msg"]." 导航ID: {$id}";
		$this->json_exit($response);
	}

	//隐藏 显示导航
	public function nav_disabled($id = 0){
		$id = (int)$id;
		!$id && $this->json_exit();
		$navInfo = $this->m_admin->get_one(array('id'=>$id),'admin_nav');
		empty($navInfo) && $this->json_exit();
		$status = abs(1-$navInfo['status']);
		$tag = $this->m_admin->update(array('status'=>$status),array('id'=>$id),'admin_nav');
		$this->_log_desc = array(
			"desc" => "更改{$navInfo['name']} 导航状态值 为{$status}",
			"status" => $tag
		);
		$this->json_exit(array("status"=>$tag));
	}

	public function admin_list(){
		$admin_users = $this->m_admin->whole_select('admin_user');
		$role_groups = $this->m_admin->whole_select('admin_group','id');
		$list = array();
		foreach ($admin_users as $key => $value) {
			// 超管判断
			if($value['id'] == 1 && $value['gid'] == 0) continue;
			$value['group_name'] = $role_groups[$value['gid']]['name'];
			$list[$value['id']] = $value;
		}
		$this->view(array("list"=>$list));
	}
	
	public function admin_add($id = 0){
		$id = (int)$id;
		if ($this->input->is_ajax_request()) {
			$post = $this->input->post();
			$id = isset($post['id']) ? (int)$post['id'] : 0;
			unset($post['id']);
			if($id){
				if (empty($post["password"])) {
					unset($post["password"]);
				}else{
					$post['password'] = md5($post['password']);		
				}
				$tag = $this->m_admin->update($post,array('id'=>$id),'admin_user');
			}else{
				$post['password'] = md5($post['password']);
				$post['create_time'] = time();
				$tag = $this->m_admin->insert($post,'admin_user');
			}
	       	$response = $this->_format_response($tag,"用户{$post['username']} ",$id);
	        $this->_log_desc = $response["msg"];
			$this->json_exit($response);
		}else{
			$group = $this->m_admin->whole_select('admin_group','id');
			if($id){
				$info = $this->m_admin->get_one(array('id'=>$id),'admin_user');
				unset($info['password']);
			}else{
				$info = array("username"=>"","status"=>1,"password"=>"","gid"=>1,"id"=>0,"company"=>"","photo"=>"");
			}
			empty($info) && redirect(base_url('admin/admin_list'));
			$data = array(
				"group" => $group,
				"info"	=> $info,
			);
			$this->view($data);
		}
	}

	// public function upload_photo(){
 //        list($name,$ext) = explode(".",$_FILES['photo']['name']);
 //        $file = date('YmdHis').rand(10,99).".{$ext}";
 //        $path = "/upload/admin_userphoto/{$file}";
 //        //===========================================
 //        $config['file_name'] = $file;
 //        $config['upload_path'] = "upload/admin_userphoto/";
 //        $config['allowed_types'] = 'jpg|gif|png';
 //        $config['remove_spaces'] = true;
 //        $config['max_size'] = 10240;//1M
 //        //===========================================
 //        $this->load->library('upload', $config);
 //        if (!$this->upload->do_upload("photo")){
 //            $response = array("status"=>FALSE,"msg"=>strip_tags($this->upload->display_errors()));
 //        }else{
 //            $response = array("status"=>TRUE,"path"=>$path);
 //        }
 //        $this->json_exit($response);
 //    }

	public function upload_photo(){
		if (empty($_FILES) || !isset($_FILES['photo'])) {
            $this->json_exit();
        }
        $this->load->library("Riak_server");
        list($name,$ext) = explode(".",$_FILES['photo']['name']);
        if (!in_array(strtolower($ext), array("jpg","gif","png","jpeg"))) {
            $this->json_exit(array("status"=>TRUE,"msg"=>"请上传图片类型文件！"));
        }
        $size = $_FILES["photo"]["size"]/1024;
        if ($size > 1024) {
            $this->json_exit(array("status"=>TRUE,"msg"=>"图片超过1M！"));   
        }
        $file_name = "admin_userphoto_".date('YmdHis').rand(1000,9999).".{$ext}";
        if ($this->riak_server->upload($file_name,file_get_contents($_FILES["photo"]["tmp_name"]))) {
            $response = array("status"=>TRUE,"path"=>base_url("file/riak_get/{$file_name}"));
        }else{
            $response = array("status"=>FALSE,"msg"=>strip_tags($this->riak_server->error));
        }
        $this->json_exit($response);
	}

	public function admin_del($id=0){
		$id = (int)$id;
		$id === 0 && $this->json_exit($response);
		$admin_user = $this->m_admin->get_one(array('id'=>$id),'admin_user');
		empty($admin_user) && $this->json_exit();
		$tag = $this->m_admin->delete(array('id'=>$id),'admin_user');
        $response = $this->_format_response($tag,"删除用户");
		$this->_log_desc = $response['msg']." 用户ID: {$id}";
		$this->json_exit($response);
	}
	
	//检查管理员用户名称是否已经存在
	public function check_username_exists(){
		$username = $this->input->post('username',true);
		$userInfo = $this->m_admin->get_one(array('username'=>$username),'admin_user');
		$valid = empty($userInfo) ? true : false;
		$this->json_exit(array('valid'=>$valid));
	}

	//用户修改密码
	public function changepwd(){
		$uid = $this->session->uid;
		!$uid && $this->json_exit(array("status"=>FALSE,"msg"=>"当前登录状态异常！请重新登录"));
		$old_password = $this->I("old_password");
		$new_password = $this->I("new_password");
		$userInfo = $this->m_admin->get_one(array('id'=>$uid),'admin_user');
		if ($userInfo['password'] != md5($old_password)) {
			$this->json_exit(array("status"=>FALSE,"msg"=>"输入的旧密码不正确！"));
		}
		$password = md5($new_password);
		$tag = $this->m_admin->update(array('password'=>$password),array('id'=>$uid),'admin_user');
        $response = $this->_format_response($tag,"修改密码");
		$this->_log_desc = $response["msg"]." 用户ID: {$uid}";
		$this->json_exit($response);
	}

	//禁用 启用用户
	public function admin_disabled(){
		$uid = (int)$this->session->uid;
		$userInfo = $this->m_admin->get_one(array('id'=>$uid),'admin_user');
		empty($userInfo) && $this->json_exit();
		// 判断是否统一分组;
		if ($userInfo["gid"] != $this->session->gid) {
			$this->json_exit(array("status"=>FALSE,"msg"=>"用户角色不同！禁止更改！"));
		}
		$status = abs(1 - $userInfo['status']);
		$tag = $this->m_admin->update(array('status'=>$status),array('id'=>$uid),'admin_user');
        $response = $this->_format_response($tag);
		$this->_log_desc = "将用户{$userInfo['username']}的状态置为{$status}".$response['msg'];
		$this->json_exit($response);
	}

	// 管理员操作日志
	public function admin_log(){
		$info = $this->m_admin->whole_select("admin_log");
		$list = array();
		foreach ($info as $key => $value) {
			// 超管判断
			if ($value['uid'] == 1 && $this->session->is_super == 0) continue;
			$list[] = $value;
		}
		$this->view(array("list"=>$list));
	}
	
}