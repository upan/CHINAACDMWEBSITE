<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
* CHINA-ACDM 首页;
* @author MAGENJIE(magenjie@feeyo.com)
* @datetime 2017-08-31T14:47:30+0800
*/
class Mobile extends Front_Controller {
    
    public function __construct(){
        parent::__construct();
    }

    public function index(){
        $this->load->view("mobile/index");
    }

    // 申请页面;
    public function apply(){
        $this->load->view("mobile/apply");
    }
}