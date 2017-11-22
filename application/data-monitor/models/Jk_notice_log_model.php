<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * +-----------------------------------
 *  接口警告日志模型
 * +-----------------------------------
 *
 * @Author: yuanyu <yuanyu@feeyo.com>
 */

class Jk_notice_log_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function get_total($where = [])
    {
        $this->db->from('jk_notice_log');
        if(isset($where['airport_iata']) && !empty($where['airport_iata']))
        {
            $this->db->where('airport_iata', $where['airport_iata']);
        }
        if(isset($where['start_timestamp']) && !empty($where['end_timestamp']))
        {
            $this->db->where("stoppage_time between {$where['start_timestamp']} AND {$where['end_timestamp']}");
        }
        return $this->db->count_all_results();
    }

    public function get_list_with_staff($where = array(), $page = '', $limit = '', $order_by = '', $direction = 'ASC')
    {
        $this->db->select('jnl.*,fs.name AS staff_name,fs.mobile AS staff_mobile,fs.email AS staff_email');
        $this->db->from('jk_notice_log AS jnl');
        $this->db->join('feeyo_staff AS fs', 'jnl.author_staff = fs.feeyo_staff_id', 'LEFT');
        if(isset($where['airport_iata']) && !empty($where['airport_iata']))
        {
            $this->db->where('jnl.airport_iata', $where['airport_iata']);
        }
        if (!empty($order_by))
        {
            $_orderby = isset($order_by) ? $order_by : "{$this->table_name}_id";
            $this->db->order_by($_orderby, $direction);
        }
        if (!empty($page) && !empty($limit))
        {
            $_offset = ($page - 1) * $limit;
            $this->db->limit($limit, $_offset);
        }
        $return = $this->db->get()->result_array();
        return $return;
    }

    public function get_info_by_id($id)
    {
        $this->db->select('jnl.*,fs.name AS staff_name,fs.mobile AS staff_mobile,fs.email AS staff_email,fs.department AS staff_department');
        $this->db->from('jk_notice_log AS jnl');
        $this->db->join('feeyo_staff AS fs', 'jnl.author_staff = fs.feeyo_staff_id', 'LEFT');
        $this->db->where('jnl.jk_notice_log_id', $id);
        $return = $this->db->get()->row_array();
        return $return;
    }

    public function get_last_log($ident)
    {
        $this->db->select('*');
        $this->db->from('jk_notice_log');
        $this->db->where('ident', $ident);
        $this->db->where('recover_time', 0);
        $this->db->order_by('stoppage_time', 'DESC');
        $this->db->limit(1);
        $return = $this->db->get()->row_array();
        return $return;
    }
}