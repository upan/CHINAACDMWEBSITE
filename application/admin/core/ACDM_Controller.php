<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @author MAGENJIE(magenjie@feeyo.com)
 * @datetime 2017-08-23T15:50:18+0800
 */
class ACDM_Controller extends CI_Controller{
    protected $_controller;
    protected $_method;
    public $_uri;
    protected $_uri_string;//完整的路由
    protected $_nav_uri;
    protected $_uri_name = "";//记录日志需要
    protected $enable_profiler = FALSE;
    protected $_log_desc = FALSE;
    
    public function __construct(){
        parent::__construct();
        $this->_controller      = $this->router->fetch_class();
        $this->_method          = $this->router->fetch_method();
        $this->_uri_controller  = $this->uri->segment(1);
        $this->_uri_method      = $this->uri->segment(2);
        $this->_uri_method ? : $this->_uri_method = 'index';
        $this->_uri             = strtolower($this->_uri_controller.'/'.$this->_uri_method);
        $this->_nav_uri         = strtolower($this->_controller.'/'.$this->_method);
        $this->_uri_string      = $this->uri->uri_string();
        // $this->_uri: 'auth/auth_edit'
        // $this->_nav_uri: 'auth/auth_add'
        // $this->_uri_string: 'auth/auth_edit/4'
        // 是否开启CI框架程序分析器
        $this->enable_profiler && $this->output->enable_profiler(TRUE);
    }

    protected function view($data = array(), $filename = ""){
        $filename ? : $filename = strtolower($this->_method);
        $path = strtolower($this->_controller).DS.$filename;
        $this->load->view("header",$data);
        $this->load->view($path);
        $this->load->view("footer");
    }

    //自定义获取请求参数
    protected function I($var = '', $default = NULL){
        $get = $this->input->get(NULL, TRUE);
        $post = $this->input->post(NULL, TRUE);
        if(empty($var)){
            if(!empty($get)){
                return $get;
            }elseif(!empty($post)){
                return $post;
            }else{
                return $default;
            }
        }else{
            if(array_key_exists($var, $get)){
                return $get[$var];
            }elseif(array_key_exists($var, $post)){
                return $post[$var];
            }else{
                return $default;
            }
        }
    }
    
    //默认中文转义
    public function json_exit($response = array("status"=>FALSE,"msg"=>"操作失败！"),$is_unescaped = FALSE,$gzip = FALSE){
        if (empty($response["msg"]) && isset($response["status"])) {
            $response["msg"] = $response["status"] ? "操作成功！" : "操作失败！";
        }
        header('Cache-Control: no-cache, must-revalidate');
        header("Content-Type: text/plain; charset=utf-8");
        $gzip && ob_start('ob_gzip');
        if ($is_unescaped) {
            echo json_encode($response,JSON_UNESCAPED_UNICODE);
        }else{
            echo json_encode($response);
        }
        $gzip && ob_end_flush();//输出压缩成果
        exit;
    }
    
    public function __destruct(){}
}

// Admin 管理后台控制器;
class Admin_Controller extends ACDM_Controller{
    public $_thirdId;
    public $_secondId;
    public $_firstId;
    public $_forbidden_msg = "非法访问,操作不存在";
    public $_no_auth_msg = "您没有当前操作的权限！";
    public $cache; //redis 实例
    public function __construct(){
        parent::__construct();
        // $this->cache = new Redis();
        // $this->redis_config = $this->config->item('redis')['default'];
        // $this->cache->connect($this->redis_config['host'], $this->redis_config['port']);
        $this->init();
    }
    
    public function init(){
        if(is_null($this->session->uid)){
            $this->noAuthRedirect("会话已过期,请重新登录！",TRUE);
        }
        //var_dump($this->m_admin->checkAuth($this->_uri),$this->_uri);
        if ($this->m_admin->checkAuth($this->_uri) === FALSE) { //提示没有权限或权限不存在
            $this->noAuthRedirect();
        }
        
        $third_r = $this->m_admin->get_one(array('uri'=>$this->_uri),'admin_auth');
        if (!$third_r || (int)$third_r['status'] === 0) { //没有此方法时 提示非法访问 状态为0时 此权限已被禁用
            $this->noAuthRedirect(empty($third_r) ? $this->_forbidden_msg : $this->_no_auth_msg);
        }
        
        $this->_thirdId = $third_r['id'];
        $second_r = $this->m_admin->get_one(array('id'=>$third_r['pid']),'admin_nav');
        if (!$second_r || (int)$second_r['status'] === 0) {
            $this->noAuthRedirect(empty($third_r) ? $this->_forbidden_msg : $this->_no_auth_msg);
        }
        
        $this->_secondId = $second_r['id'];
        $first_r = $this->m_admin->get_one(array('id'=>$second_r['pid']),'admin_nav');
        if (!$first_r || (int)$first_r['status'] === 0) {
            $this->noAuthRedirect(empty($third_r) ? $this->_forbidden_msg : $this->_no_auth_msg);
        }
        $this->_firstId = $first_r['id'];
    }
    
    public function _format_response($status,$prefix_msg = "操作",$id = FALSE){
        $msg = $id === FALSE ? $prefix_msg.($status ? "成功！" : "失败！") : ($id ? "编辑" : "添加").$prefix_msg.($status ? "成功！" : "失败！");
        return array("status"=>$status,"msg"=>$msg);
    }

    // 无权限跳转
    public function noAuthRedirect($msg = FALSE, $loginout = FALSE){
        if ($this->session->is_super && $msg != $this->_forbidden_msg) {
            return TRUE;
        }
        $msg ? : $msg = "您没有当前操作的权限！";
        if($this->input->is_ajax_request()){
            $this->json_exit(array("status"=>FALSE,"msg"=>$msg,"loginout"=>$loginout));
        }else{
            redirect(base_url("login"));
        }
    }

    //更新 session 信息
    public function refresh(){
        is_null($this->session->uid) && redirect(base_url('login'));
        $r = $this->m_admin->get_one(array('username'=>$username),'admin_user');
        if ($r) {
            $data['uid'] = $r['id'];
            $data['gid'] = $r['gid'];
            $data['username'] = $r['username'];
            // $data['password'] = $r['password'];
            $data['last_login_time'] = $r['last_login_time'];
            $data['last_login_ip'] = $r["last_login_ip"];
            
            $data['nav'] = $this->m_login->getNav($r['gid']);
            $group = $this->m_admin->get_one(array('id'=>$r['gid']),'admin_group');
            $data['auth'] = $group['auth'];
            $data['group_name'] = $group['name'];
            $this->session->set_userdata($data);
            $this->json_exit(array('status'=>true,'msg'=>'缓存刷新成功！'));
        }else{
            redirect(base_url('login'));
        }
    }

    //记录操作日志到mysql
    public function __destruct(){
        if ($this->_log_desc) {
            if (is_array($this->_log_desc)) {
                $log_desc = $this->_log_desc["desc"].($this->_log_desc["status"] ? "成功" : "失败");
            }else{
                $log_desc = $this->_log_desc;
            }
            $this->m_admin->write_log($this->_uri_string,$log_desc,$_REQUEST);
        }
    }
}

// 通用接口控制器
class API_Controller extends ACDM_Controller{
    const ERROR_CODE = 1;
    const SUCCESS_CODE = 0;
    protected $response = array('code' => 0);

    //token验证 防止非法盗用api
    public function __construct(){
        parent::__construct();
    }

    //暂不能处理 直接跳转404
    public function __call($func,$args){
        output(array("code"=>self::ERROR_CODE,"desc"=>"not find FUNCTION [ {$func} ],please check your params!"));
    }
}
