<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * +-----------------------------------
 *  成员机场模型
 * +-----------------------------------
 *
 * @Author: yuanyu <yuanyu@feeyo.com>
 */

class Member_airport_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }


    //获取成员机场ACDM中的主机场 比如云南机场集团下的机场的主入口都是KMG
    public function get_main_airport_iata($where = [])
    {
        $return = [];
        $this->db->select('system_prefix');
        $this->db->from('member_airport');
        if(isset($where['airport_iata']) && !empty($where['airport_iata']))
        {
            $this->db->where('airport_iata', $where['airport_iata']);
        }
        $this->db->group_by('system_prefix');
        $result = $this->db->get()->result_array();
        foreach($result as $item)
        {
            $return[] = $item['system_prefix'];
        }
        return $return;
    }
}