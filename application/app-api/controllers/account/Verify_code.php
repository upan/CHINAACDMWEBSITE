<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * +-----------------------------------
 * 验证码相关
 * +-----------------------------------
 *
 * @Author: zhaobin <zhaobin@feeyo.com>
 */

class Verify_code extends ACDM_Controller {
    
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model('user_model');
        $this->load->model('airport_model');
    }

    /**
     * 发送短信验证码
     *
     * @Author: zhaobin <zhaobin@feeyo.com>
     */
    public function send()
    {
        $this->validation();
        
        $mobile = $this->I("mobile");
        $from_client = $this->I('from_client');
        
        $user_list = $this->user_model->get_by_mobile($mobile);
        if ( ! empty($user_list))
        {
            foreach ($user_list as $account_key => $account_row)
            {
                // 账号已被关闭
                if (intval($account_row['status']) == 0)
                {
                    unset($user_list[$account_key]);
                    continue;
                }
        
                // 账号已被删除
                if (intval($account_row['is_delete']) == 1)
                {
                    unset($user_list[$account_key]);
                    continue;
                }
        
                // 账号已过期
                if ( ! empty($account_row['expiry_date']))
                {
                    $expiry_date = strtotime($account_row['expiry_date']);
                    if (time() > $expiry_date)
                    {
                        unset($user_list[$account_key]);
                        continue;
                    }
                }
        
                // WGS账号验证
                if ($from_client == 'wgs')
                {
                    if (intval($account_row['wgs_is_enabled']) == 0)
                    {
                        unset($user_list[$account_key]);
                        continue;
                    }
                }
            }
        }
        
        if (empty($user_list))
        {
            $this->response(103);
        }

        // 获取验证码
        $code = rand(100000,999999);

        // 发送验证码
        $tpl_param = array(
            '#CODE#' => $code
        );
        $sms_send_result = send_msg_by_feeyo_api($mobile, 'verify_code', $tpl_param);
        if ($sms_send_result === TRUE)
        {
            // 获取验证码KEY
            $redis_key = $this->config->item('REDISKEY');
//            $redis = redis_connect();
            $redis = redis_connect_new();
            $redis_key['APP_Verify_Code'] = str_replace('[replace_key]', $mobile . $code, $redis_key['APP_Verify_Code']);
            $redis->setex($redis_key['APP_Verify_Code'], 300, md5($this->ApiPublicKey . $mobile . $code));
            $redis->close();
            
            $this->response(0);
        }
        else
        {
            $this->response(115);
        }
    }

    /**
     * 验证验证码
     *
     * @Author: zhaobin <zhaobin@feeyo.com>
     */
    public function verify()
    {
        $this->validation();
        
        $code = $this->I("code");
        $mobile = $this->I("mobile");
        $from_client = $this->I('from_client');
        $publicKey = $this->ApiPublicKey;

        //获取验证码
        $redis_key = $this->config->item('REDISKEY');
        //$redis = redis_connect();
        $redis = redis_connect_new();
        $verify_code = md5($this->ApiPublicKey . $mobile . $code);
        $redis_key['APP_Verify_Code'] = str_replace('[replace_key]', $mobile . $code, $redis_key['APP_Verify_Code']);
        $verify_code_bak = $redis->get($redis_key['APP_Verify_Code']);
        $redis->close();
        
        if($verify_code == $verify_code_bak)
        {
            $this->load->model('user_model');
            $user_list = $this->user_model->get_by_mobile($mobile);
            if ( ! empty($user_list))
            {
                foreach ($user_list as $account_key => $account_row)
                {
                    // 账号已被关闭
                    if (intval($account_row['status']) == 0)
                    {
                        unset($user_list[$account_key]);
                        continue;
                    }
            
                    // 账号已被删除
                    if (intval($account_row['is_delete']) == 1)
                    {
                        unset($user_list[$account_key]);
                        continue;
                    }
            
                    // 账号已过期
                    if ( ! empty($account_row['expiry_date']))
                    {
                        $expiry_date = strtotime($account_row['expiry_date']);
                        if (time() > $expiry_date)
                        {
                            unset($user_list[$account_key]);
                            continue;
                        }
                    }
            
                    // WGS账号验证
                    if ($from_client == 'wgs')
                    {
                        if (intval($account_row['wgs_is_enabled']) == 0)
                        {
                            unset($user_list[$account_key]);
                            continue;
                        }
                    }
                }
            }
            
            if (empty($user_list))
            {
                $this->response(103);
            }
            
            // 加载ACDM机场列表配置
            $this->config->load('acdm_airport_list');
            $acdm_list = $this->config->item('acdm_list');
            
            // 整理用户账号相关信息
            $account_list = array();
            foreach ($user_list as $account_row)
            {
                $account_info = array();
                
                $airport_system_api_domain = 'https://'.strtolower($acdm_list[$account_row['airport_iata']]['system_prefix'].'-app.goms.com.cn');
                
                // 用户总ID
                $account_info['user_id'] = $account_row['user_id'];
                // 用户真实ID
                $account_info['uid'] = intval($account_row['uid']);
                // 用户所属机场三字码
                $account_info['airport_iata'] = $account_row['airport_iata'];
                // 用户手机号
                $account_info['mobile'] = $account_row['mobile'];
                // 用户真实姓名
                $account_info['truename'] = $account_row['truename'];
                // 用户头像
                $account_info['image'] = $airport_system_api_domain.'/'.$account_row['image'];
                // 用户性别
                $account_info['sex'] = intval($account_row['sex']);
                // 用户密码是否为旧密码（未加盐）
                $account_info['is_psw'] = empty($account_row['salt']) ? 1 : 0;
                // 账号所属机场相关信息
                $account_info['airport_info'] = $this->airport_model->get_airport_info($account_row['airport_iata']);
                if (isset($acdm_list[$account_row['airport_iata']]))
                {
                    // 机场名称
                    $account_info['airport_info']['airport_name'] = $acdm_list[$account_row['airport_iata']]['airport_name'];
                    // 机场跑道号
                    $account_info['airport_info']['airport_runway'] = $acdm_list[$account_row['airport_iata']]['airport_runway'];
                    // 机场系统名称
                    $account_info['airport_info']['airport_system_name'] = $acdm_list[$account_row['airport_iata']]['system_name'];
                    // 机场系统密钥
                    $account_info['airport_info']['airport_system_key'] = $acdm_list[$account_row['airport_iata']]['api_public_key'];
                    // 机场系统前缀
                    $account_info['airport_info']['airport_system_prefix'] = $acdm_list[$account_row['airport_iata']]['system_prefix'];
                    
                    // APP接口域名
                    $account_info['api_domain'] = $airport_system_api_domain;
                    // APP接口密钥
                    $account_info['api_public_key'] = $acdm_list[$account_row['airport_iata']]['api_public_key'];
                    // 可切换机场列表
                    $account_info['can_switch_airport_list'] = array();
                    foreach ($acdm_list as $acdm_airport_row)
                    {
                        if ($acdm_airport_row['system_prefix'] == $acdm_list[$account_row['airport_iata']]['system_prefix'])
                        {
                            $account_info['can_switch_airport_list'][] = array(
                                'airport_name' => $acdm_airport_row['airport_name'],
                                'airport_system_name' => $acdm_airport_row['system_name'],
                                'api_domain' => $airport_system_api_domain,
                            );
                        }
                    }
                }
                
                $account_list[] = $account_info;
            }
            
            $this->response(0, $account_list);
        }
        else
        {
            $this->response(102);
        }
    }
}