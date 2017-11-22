<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Operate extends ACDM_Controller {

    public function __construct()
    {
        parent::__construct(FALSE);
        $this->load->model('Account_model');
    }


	public function login()
    {
        //判断是否有session数据
        if($this->account_auth())
        {
            redirect($this->bas_var['system_index_url']);
        }
	    $this->load->view('account/operate/login', $this->bas_var);
	}

	public function login_exec()
    {
        $mobile = $this->I('mobile');
        $password = $this->I('password');
        $success = 0;
        $msg = "用户名不存在或密码错误";
        if(!empty($mobile) && !empty($password))
        {
            $account = $this->Account_model->get_by_mobile($mobile);
            if(!empty($account))
            {
                $password_handler = $this->Account_model->_generate_password_cipher($password, $account['salt']);
                if($password_handler === $account['password'])
                {
                    if($account['is_delete'] == 1)
                    {
                        $msg = "用户名不存在或密码错误";
                    }
                    elseif($account['is_valid'] == 0)
                    {
                        $msg = "该用户已离职，无法登录系统";
                    }
                    else
                    {
                        $success = 1;
                        $msg = "登录成功";
                    }
                }
            }
        }
        else
        {
            $msg = "用户名和密码必须填写";
        }
        if($success === 1)
        {
            //登录成功 设置session
            $this->session->set_userdata('user_auth', $account);
            $this->session->set_userdata('user_auth_sign', data_build_string($account));
        }
        echo json_encode(array("success" => $success, "text" => $msg));
        exit;
    }

    public function logout()
    {
        //删除cookie session
        //delete_cookie('auto');
        $this->session->unset_userdata('user_auth_sign');
        $this->session->unset_userdata('user_auth');
        redirect($this->bas_var['system_login_url']);
    }
}
