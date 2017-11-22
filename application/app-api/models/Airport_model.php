<?php

/**
 * +-----------------------------------
 * 机场数据模型
 * +-----------------------------------
 *
 * @Author: zhaobin <zhaobin@feeyo.com>
 */

class Airport_model extends CI_Model {
    
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * 根据机场三字码获取指定机场信息
     *
     * @Author: zhaobin <zhaobin@feeyo.com>
     */
    public function get_airport_info($airport_iata)
    {
        return $this->db->select('airport_iata,airport_icao,airport_lon,airport_lat,is_foreign')->from('airport')->where('airport_iata', $airport_iata)->get()->row_array();
    }
}