<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @author MAGENJIE(magenjie@feeyo.com)
 * @datetime 2017-08-23T15:50:18+0800
 */
class ACDM_Controller extends CI_Controller{
    protected $_controller;
    protected $_method;
    public $_uri;
    public $_uri_string;//完整的路由
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
}

// 前端页面控制器
class Front_Controller extends ACDM_Controller{
    public $categorys = array();
    public $friend_links = array();
    public $member_airports = array();
    public $now_catid = 0;
    public function __construct(){
        parent::__construct();
        if ($this->_uri_controller === "mobile") {
        }else{
            $redirect_mobile = $this->m_home->check_is_mobile() && $this->_uri_controller !== "mobile" && $this->_uri_string != "home/try_use_apply";
            if ($redirect_mobile) {
                redirect('mobile/index/');
            }else{
                $this->init();
            }
        }
    }

    public function init(){
        //成员机场列表;
        $member_airports_result = $this->m_home->simple_select(array("status"=>1),"member_airport_id asc","member_airport");
        $this->member_airports = array_column($member_airports_result,"airport_iata");
        //栏目
        $categorys_result = $this->m_home->simple_select(array("status"=>1,"pid"=>0),"sort asc","category");
        foreach ($categorys_result as $key => $value) {
            switch ((int)$value["type"]) {
                case 1:
                    $url = preg_match('/(http:\/\/)|(https:\/\/)/i',$value["link"]) ? $value["link"] : "/home/page/{$value['link']}";
                    break;
                case 2:
                    $url = "/category/index/{$value['id']}";
                    break;
                case 3:
                    $url = "/category/index/{$value['id']}";
                    break;
                default:
                    $url = FALSE;
                    break;
            }
            if ($url) {
                $this->categorys[] = array(
                    "id"    => $value["id"],
                    "url"   => $url,
                    "name"  => $value["name"],
                );    
            }
        }
        $cur_time = time();
        $friend_links_result = $this->m_home->simple_select("status = 1 AND ((start_time = 0 AND end_time > $cur_time) OR (end_time = 0 AND start_time > $cur_time) OR (start_time = 0 AND end_time = 0) )","sort desc","friend_link");
        foreach ($friend_links_result as $key => $value) {
            $this->friend_links[] = array(
                "url"   => $value["url"],
                "name"  => $value["name"],
            );
        }
    }
}

// 前端会员中心控制器
class Member_Controller extends Front_Controller{   
    public function __construct(){
        parent::__construct();
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
