<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * +-----------------------------------
 * 用户验证相关
 * +-----------------------------------
 *
 * @Author: zhaobin <zhaobin@feeyo.com>
 */

class Verify extends ACDM_Controller {

    public function __construct()
    {
        parent::__construct();
        
        $this->load->model('user_model');
        $this->load->model('airport_model');
    }
    
    /**
     * 账号登陆
     *
     * @Author: zhaobin <zhaobin@feeyo.com>
     */
    public function login()
    {
        $this->validation();
        $mobile = $this->I('mobile');
        $psw = $this->I('psw');
        $from_client = $this->I('from_client');
        $ratio = $this->I('ratio');
        $device = $this->I('device');

        if ( ! in_array($from_client, array('acdm', 'wgs')))
        {
            $this->response(407);
        }
        
        // 获取手机号为当前手机号的所有账号并验证其账号是否有效
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
                
                // 验证密码是否正确
                if (empty($account_row['salt']))
                {
                    // 老加密验证
                    if (md5($psw) !== $account_row['password'])
                    {
                        unset($user_list[$account_key]);
                        continue;
                    }
                }
                else
                {
                    // 新加密验证
                    $correct_cipher = hash('sha256', $account_row['salt'].$psw);
                    if ($correct_cipher != $account_row['password'])
                    {
                        unset($user_list[$account_key]);
                        continue;
                    }
                }
            }
        }
        
        if (empty($user_list))
        {
            $this->response(101);
        }
        
        // 加载ACDM机场列表配置
        $this->config->load('acdm_airport_list');
        $acdm_list = $this->config->item('acdm_list');
        
        // 整理用户账号相关信息
        $account_list = array();
//        var_dump($account_list);exit;
        foreach ($user_list as $account_row)
        {
            $account_info = array();
            
//            $airport_system_api_domain = 'http://beta.'.strtolower($acdm_list[$account_row['airport_iata']]['system_prefix'].'-app.goms.com.cn');
            $airport_system_api_domain = $this->config->item('base_http').strtolower($acdm_list[$account_row['airport_iata']]['system_prefix'].'-app.goms.com.cn');
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
            if($account_row['image'])
            {
                $account_info['image'] = $airport_system_api_domain.'/'.$account_row['image'];
            }
            else
            {
                //无头像默认为空，客户端提供默认图片
                $account_info['image'] = "";
            }
            // 用户性别
            $account_info['sex'] = intval($account_row['sex']);
            // 用户密码是否为旧密码（未加盐）
            $account_info['is_psw'] = empty($account_row['salt']) ? 1 : 0;
            // 账号所属机场相关信息
            $account_info['airport_info'] = $this->airport_model->get_airport_info($account_row['airport_iata']);
            $account_info['splash_screen'] =  $this->_get_airport_splashScreen($account_row['user_id'],$device,$ratio);
            if (isset($acdm_list[$account_row['airport_iata']]))
            {
                // 机场名称
                $account_info['airport_info']['airport_name'] = $acdm_list[$account_row['airport_iata']]['airport_name'];
                // 机场跑道号
                $account_info['airport_info']['airport_runway'] = $acdm_list[$account_row['airport_iata']]['airport_runway'];
                // 机场系统名称
                $account_info['airport_info']['airport_system_name'] = $acdm_list[$account_row['airport_iata']]['system_name'];
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

        
        if (count($account_list) == 1)
        {
            $this->system_selection(FALSE, $account_list[0]['user_id'], $account_list[0]['uid'], $from_client, $account_list[0]['api_domain']);
        }
        $this->response(0, $account_list);
    }
    
    /**
     * 系统选择
     * 
     * @param   $user_id        ACDM用户中心的用户ID
     * @param   $uid            用户所在机场的用户ID
     * @param   $from_client    来源端：acdm/wgs
     * @param   $api_domain     接口域名
     *
     * @Author: zhaobin <zhaobin@feeyo.com>
     */
    public function system_selection($response = TRUE, $user_id = '', $uid = 0, $from_client = '', $api_domain = '')
    {
        if (empty($user_id))
        {
            $user_id = $this->I('user_id');
        }
        
        if (empty($uid))
        {
            $uid = $this->I('uid');
        }
        
        if (empty($from_client))
        {
            $from_client = $this->I('from_client');
        }
        
        if (empty($api_domain))
        {
            $api_domain = $this->I('api_domain');
        }

        
        // 登录成功更新用户设备信息
        $account_update_data = array();
        $account_update_data['uid'] = intval($uid);
        $account_update_data['from_client'] = $from_client;
        $account_update_data['device'] = intval($this->I("device"));
        $account_update_data['device_info'] = $this->I("device_info");
        $account_update_data['unique_id'] = $this->I("unique_id");
        $account_update_data['device_token'] = $this->I("device_token");
        $account_update_data['version'] = $this->I("version");
        $account_update_data['appid'] = intval($this->I('appid'));
        
        // 对应机场更新用户设备信息接口地址
        $api_url = $api_domain.'/v4/account/open_api/update_user_device_info';

        // 请求对应机场接口更新用户在其机场的相关设备信息
        $result = curl_post($api_url, json_encode($account_update_data));
        if ($result['code'] == 1)
        {
            $this->response(-2, NULL, $result['msg']);
        }
        else
        {
            unset($account_update_data['from_client'], $account_update_data['uid']);
            
            $account_update_data['update_time'] = time();
            if ($from_client == 'acdm')
            {
                $affected_rows = $this->user_model->update_row($user_id, $account_update_data);
            }
            else if ($from_client == 'wgs')
            {
                foreach ($account_update_data as $field => $value)
                {
                    $account_update_data['wgs_'.$field] = $value;
                }
                $affected_rows = $this->user_model->update($user_id, $account_update_data);
            }
            
            if (empty($affected_rows))
            {
                $msg = 'Update user information equipment failed[11]';
                $this->response(-2, NULL, $msg);
            }
            
            // 清除非本用户使用相同的设备推送标识
            if ($from_client == 'acdm')
            {
                $where = "user_id != '{$user_id}' AND device_token = '{$account_update_data["device_token"]}'";
                $this->db->where($where)->update('user', array('device_token' => ''));
            }
            else if ($from_client == 'wgs')
            {
                $where = "user_id != '{$user_id}' AND wgs_device_token = '{$account_update_data["device_token"]}'";
                $this->db->where($where)->update('user', array('wgs_device_token' => ''));
            }
        }


        if ($response === TRUE)
        {
            $this->response(0);
        }
        else
        {
            return TRUE;
        }
    }

    /**
     * 获取广告图
     * @param $user_id
     * @param $device
     * @param $ratio
     * @return string
     */
    private function _get_airport_splashScreen($user_id,$device,$ratio)
    {
        $path = "splashscreens/";
        $this->config->load('acdm_airport_list');
        $acdm_list = $this->config->item('acdm_list');
        $user_info = $this->user_model->get_user_by_id($user_id);
        if($user_info)
        {
            $airport_iata = $user_info['airport_iata'];
            if(array_key_exists($airport_iata,$acdm_list))
            {
                $system_prefix = $acdm_list[$airport_iata]['system_prefix'];
                if($system_prefix == "KWE" || $system_prefix == "HNA" || $system_prefix == "KMG")
                {
                    //黔程在握
                    $airport_dir = $system_prefix;
                }
                else
                {
                    $airport_dir = $airport_iata;
                }
                if($device == 1)
                {
                    $device_type = "iOS";
                }
                elseif($device == 2)
                {
                    $device_type = "android";
                }
                else
                {
                    return "";
                }
                $path .= $airport_dir."/".$device_type."/".$ratio.".jpg";
                $url = base_url($path);
                return $url;
            }
        }
        return "";
    }

    /**
     * 检查图片是否存在
     * @param $url
     * @return bool
     */
    public function _check_image_exists($url)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        $result = curl_exec($curl);
        $flag = false;
        if ($result !== false)
        {
            $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if ($statusCode == 200)
            {
                $flag = true;
            }
        }
        curl_close($curl);
        return $flag;
    }

    /**
     * 修改密码
     *
     * @Author: zhaobin <zhaobin@feeyo.com>
     */
    public function change_psw()
    {
        $this->validation();
        
        $user_id = $this->I('user_id');
        $uid = $this->I('uid');
        $mobile = $this->I("mobile");
        $code = $this->I("code");
        $psw = $this->I('psw');
        $api_domain = $this->I('api_domain');
    
        if (strlen($psw) < 8)
        {
            $this->response(113);
        }
    
        //从缓存获取验证码
        $redis_key = $this->config->item('REDISKEY');
//        $redis = redis_connect();
        $redis = redis_connect_new();
        $verify_code = md5($this->ApiPublicKey . $mobile . $code);
        $redis_key['APP_Verify_Code'] = str_replace('[replace_key]', $mobile . $code, $redis_key['APP_Verify_Code']);
        $verify_code_bak = $redis->get($redis_key['APP_Verify_Code']);
        $redis->close();
    
        if ($verify_code !== $verify_code_bak)
        {
            $this->response(102);
        }
        
        // 对应机场更新用户设备信息接口地址
        $api_url = $api_domain.'/v4/account/open_api/update_user_password';
        
        // 请求对应机场接口更新用户密码
        $update_user_password = array(
            'uid' => $uid,
            'psw' => $psw
        );
        $result = curl_post($api_url, json_encode($update_user_password));
        if ($result['code'] == 1)
        {
            $this->response(-2, NULL, $result['msg']);
        }
        else
        {
            $affected_rows = $this->user_model->update_row($user_id, $result['data']);
            if (empty($affected_rows))
            {
                $msg = 'Modify the user password failure[11]';
                $this->response(-2, NULL, $msg);
                exit;
            }
            else 
            {
                $this->response(0);
            }
        }
    }
    
    /**
     * 用户退出
     *
     * @Author: zhaobin <zhaobin@feeyo.com>
     */
    public function logout()
    {
        $this->validation();
        $user_id = $this->I('user_id');
        $uid = $this->I('uid');
        $api_domain = $this->I('api_domain');
        $from_client = $this->I('from_client');
        
        if ( ! in_array($from_client, array('acdm', 'wgs')))
        {
            $this->response(407);
        }
        
        // 对应机场更新用户设备信息接口地址
        $api_url = $api_domain.'/v4/account/open_api/logout';
        
        // 请求对应机场接口更新用户在其机场的相关设备信息
        $logout_param = array(
            'uid' => intval($uid),
            'from_client' => $from_client
        );
        $result = curl_post($api_url, json_encode($logout_param));
        if ($result['code'] == 1)
        {
            $this->response(-2, NULL, $result['msg']);
        }
        else
        {
            if ($from_client == 'acdm')
            {
                // 极光用户ID清空
                $update_data = array(
                    'registration_id' => ''
                );
            }
            else if ($from_client == 'wgs')
            {
                // 极光用户ID清空
                $update_data = array(
                    'wgs_registration_id' => ''
                );
            }
            
            $affected_rows = $this->user_model->update_row($user_id, $update_data);
            if (empty($affected_rows))
            {
                $msg = 'Log out abnormal';
                $this->response(-2, NULL, $msg);
                exit;
            }
            else
            {
                $this->response(0);
            }
        }
    }
}