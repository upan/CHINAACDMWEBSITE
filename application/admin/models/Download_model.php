<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author MAGENJIE(magenjie@feeyo.com)
 * @datetime 2017-08-25T14:51:08+0800
 */
class Download_model extends ACDM_Model {

    public function __construct(){
        parent::__construct();
        $this->table = "download";
    }
    
    public function _default(){
        return array(
            "id"        => 0,
            "catid"     => 0,
            "title"     => "",
            "thumb"     => "",
            "author"    => "",
            "path"      => "",
            "down"      => 0,
            "size"      => 0,
            "type"      => 1,
            "status"    => 1,
            "sort"      => 0,
            "description"   => "",
            "create_time"   => 0,
            "update_time"   => 0,
            "create_uid"    => 0,
            "update_uid"    => 0,
        );
    }
}