<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author MAGENJIE(magenjie@feeyo.com)
 * @datetime 2017-08-25T14:51:08+0800
 */
class Article_model extends ACDM_Model {

    public function __construct(){
        parent::__construct();
        $this->table = "article";
    }
    
    public function _default(){
        return array(
            "id"        => 0,
            "catid"     => 0,
            "title"     => "",
            "thumb"     => "",
            "from"      => "",
            "author"    => "",
            "keywords"  => "",
            "content"   => "",
            "read"      => 0,
            "like"      => 0,
            "status"    => 1,
            "sort"      => 0,
            "description"   => "",
            "create_time"   => 0,
            "update_time"   => 0,
            "create_uid"    => 0,
            "update_uid"    => 0,
        );
    }

    public function _default_article_pics(){
        return array(
            "id"        => 0,
            "path"      => "",
            "article_id"=> 0,
            "create_time"=>0,
        );   
    }

    public function _default_show_config(){
        return array(
            "id"        => 0,
            "name"      => "",
            "field"     => "",
            "description" => "",
            "update_time" => 0,
        );
    }
}