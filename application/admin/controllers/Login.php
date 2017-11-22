<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author MAGENJIE(magenjie@feeyo.com)
 * @datetime 2017-08-23T15:50:18+0800
 */
class Login extends ACDM_Controller { 
	protected $url;

    public function __construct(){
        parent::__construct();
        $this->url = base_url('admin');//登录成功 跳转地址
    }

    public function index(){
		$r = array();
        $post = $this->input->post();
		if($post){
			$username = trim($post['username']);
			$password = md5(trim($post['password']));
			$r = $this->m_login->login($username,$password);
			$r['status'] ? $r['url'] = base_url('admin') : $r['url'] = base_url('login');
			$r['status'] ? $r['status'] = "success" : $r['status'] = "error";
			$r['status'] && $this->_log_desc = "登录系统";
		}
		$this->load->view("login/index",$r);
	}

	public function loginOut(){   
        $this->_log_desc = "退出系统";
        $this->session->sess_destroy();
        redirect(base_url("login"));
    }

    //修改密码 html页面
    public function changepwd(){
    	$data['default'] = array(
    		"old_password"        => "",
    		"new_password"        => "",
    		"confirm_password"    => "",
    	);
    	$this->load->view("changepwd",$data);
    }
}