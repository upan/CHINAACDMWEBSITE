<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author MAGENJIE(magenjie@feeyo.com)
 * @datetime 2017-08-23T15:50:18+0800
 */
class System_setting_model extends ACDM_Model {
    public function __construct(){
        parent::__construct();
        $this->table = "system_config";
    }
    
    public function _default_system_config($title = "",$name = "",$is_object = TRUE){
        return array(
            "id"            => 0,
            "title"         => $title,
            "name"          => $name,
            "value"         => $is_object ? (object)array() : array(),
            "update_time"   => 0,
        );
    }

    public function _default_friend_link(){
        return array(
            "id"            => 0,
            "name"          => "",
            "url"           => "",
            "status"        => 1,
            "sort"          => 255,
            "start_time"    => "",
            "end_time"      => "",
            "update_time"   => 0,
        );
    }
}