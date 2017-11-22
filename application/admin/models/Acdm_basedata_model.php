<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author MAGENJIE(magenjie@feeyo.com)
 * @datetime 2017-09-30T14:42:03+0800
 */
class Acdm_basedata_model extends ACDM_Model {
    public function __construct(){
        parent::__construct();
    }
    
    public function _default_member_airport(){
        return array(
            "airport_iata"      => "",
            "airport_icao"      => "",
            "airport_name"      => "",
            "status"            => 1,
            "runway"            => "",
            "cn_name"           => "",
            "cn_name_short"     => "",
            "system_name"       => "",
            "api_public_key"    => "",
            "system_prefix"     => "",
        );
    }

    public function _default_app_version(){
        return array(
            "type"      => "",
            "status"    => 1,
            "name"      => "",
            "code"      => "",
            "url"       => "",
            "is_must"   => 0,
            "airport_iata"  => "",
            "description"   => "",
        );
    }
    
    public function _default_app_type(){
        return array(
            "id"            => "",     //客户端类型标识.禁止更改;
            "name"          => "",   //客户端名称 中文名称;
            "description"   => "",// 客户端描述;
        );
    }
}