<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author MAGENJIE(magenjie@feeyo.com)
 * @datetime 2017-08-23T15:50:18+0800
 */
class Category_model extends ACDM_Model {
    public function __construct(){
        parent::__construct();
        $this->table = 'category';
        //$this->show_query = "WRITE";
        $this->icon = array('│','├',' └');
    }

    //将数据格式化成树形结构
    function genTree($items,$id = FALSE) {
        foreach ($items as $item){
            $items[$item['pid']]['son'][$item['id']] = &$items[$item['id']];
        }
        $tree = isset($items[0]['son']) ? $items[0]['son'] : array();
        if ($id === FALSE) {
            return $tree;
        }else{
            return isset($tree[$id]) ? $tree[$id] : array();
        }
    }
    
    public function _default(){
        return array(
            "id"        => 0,
            "name"      => "",
            "link"      => "",
            "type"      => 2,
            "status"    => 1,
            "sort"      => 255,
            "update_time" => 0,
        );
    }
    
}