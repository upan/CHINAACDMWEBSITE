<?php

/**
 * +-----------------------------------
 * 用户数据模型
 * +-----------------------------------
 *
 * @Author: zhaobin <zhaobin@feeyo.com>
 */

class User_model extends CI_Model {
    
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * 根据手机号获取用户列表
     * 
     * @param   $mobile string  手机号
     *
     * @Author: zhaobin <zhaobin@feeyo.com>
     */
    public function get_by_mobile($mobile)
    {
        return $this->db->from('user')->where('mobile', $mobile)->get()->result_array();
    }
    
    /**
     * 更新一条记录
     * 
     * @param   $user_id    string      用户ID
     * @param   $data       array       待更新数据
     *
     * @Author: zhaobin <zhaobin@feeyo.com>
     */
    public function update_row($user_id, $data)
    {
        $data['update_time'] = time();
        $this->db->where('user_id', $user_id)->update('user', $data);
        return $this->db->affected_rows();
    }
    
    /**
     * 清除非指定用户使用相同的设备推送标识
     *
     * @param   $uid    int     用户ID
     * @param   $token  string  设备标识
     *
     * @Author: zhaobin <zhaobin@feeyo.com>
     */
    public function reset_conflict_device_token($user_id = 0, $token = '')
    {
        if (empty($user_id) OR empty($token))
        {
            return FALSE;
        }
        
        $this->db->where('user_id !=', $user_id)->where('device_token', $token)->update('user', array('device_token' => ''));
        return $this->db->affected_rows();
    }

    /**
     * 根据用户ID获取用户数据
     *
     * @param   $uid    string  用户ID
     * @return  array   用户数据
     *
     * @Author: zhaobin <zhaobin@feeyo.com>
     */
    public function get_user_by_id($user_id)
    {
        if (empty($user_id))
        {
            return array();
        }

        $this->db->select("u.airport_iata,u.unique_id,u.mobile,u.truename,u.department_1,u.department_2,u.organization,u.image");
        $this->db->from("user as u");
        $this->db->where("u.user_id", $user_id);
        $result = $this->db->get()->row_array();
        return $result;
    }
}