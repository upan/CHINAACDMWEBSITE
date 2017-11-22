<?php
/**
 * 账户模型
 *
 * @package path
 * @access  public
 * @author  yuanyu <yuanyu@feeyo.com>
 * @link    https://
 */

class Account_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function get_by_mobile($mobile)
    {
        $return = $this->db->select('feeyo_staff_id,name,mobile,password,salt,email,department,post_id,is_valid,is_delete')->from('feeyo_staff')->where('mobile', $mobile)->get()->row_array();
        return $return;
    }

    /**
     * 密码加密
     *
     * @param   string  $password   密码明文
     * @return  array   $psw_info   成功时返回密码信息数组，包括cipher(密文)和salt(盐)，失败时返回FALSE
     *
     * @Author: zhaobin <zhaobin@feeyo.com>
     */
    public function password_encryption($password = '')
    {
        if (empty($password))
        {
            return FALSE;
        }

        $psw_info = array();

        // 使用字符串辅助函数产生一个字符串，用于生成密码所用到的“盐”
        $this->load->helper('string');
        $psw_info['salt'] = random_string('alnum', 8);

        // 将随机盐加上密码明文，使用sha256哈希算法得出其散列值用于密码密文
        $psw_info['cipher'] = $this->_generate_password_cipher($password, $psw_info['salt']);

        return $psw_info;
    }

    /**
     * 生成密码密文
     *
     * @param   string  $password_plaintext 密码明文
     * @param   string  $salt               盐
     * @return  array   $psw_info   成功时返回密码密文，失败时返回FALSE
     *
     * @Author: zhaobin <zhaobin@feeyo.com>
     */
    public function _generate_password_cipher($password_plaintext = '', $salt = '')
    {
        if (empty($password_plaintext) OR empty($salt))
        {
            return FALSE;
        }

        return hash('sha256', $salt . $password_plaintext);
    }
}