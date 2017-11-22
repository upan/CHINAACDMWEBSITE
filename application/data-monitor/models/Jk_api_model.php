<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * +-----------------------------------
 *  接口模型
 * +-----------------------------------
 *
 * @Author: yuanyu <yuanyu@feeyo.com>
 */

class Jk_api_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取带有作者信息的接口列表
     * @param array $where
     * @return mixed
     */
    public function get_list_with_staff($where = array())
    {
        $this->db->select('ja.*,fs.name AS staff_name,fs.mobile AS staff_mobile,fs.email AS staff_email,fs.department AS staff_department');
        $this->db->from('jk_api AS ja');
        $this->db->join('feeyo_staff AS fs', 'ja.author_staff = fs.feeyo_staff_id', 'LEFT');
        if(isset($where['airport_iata']) && !empty($where['airport_iata']))
        {
            $this->db->where('ja.airport_iata', $where['airport_iata']);
        }
        if(isset($where['is_enable']) && !empty($where['is_enable']))
        {
            $this->db->where('ja.is_enable', $where['is_enable']);
        }
        $this->db->where('ja.is_delete', 0);
        $return = $this->db->get()->result_array();
        return $return;
    }

    /**
     * 返回传入的接口状态信息
     * @param $api 接口信息
     * @return array
     */
    public function api_status_handler($api_list)
    {
        //不管有多少个，首先转为列表形式,如果本身即为二组数组则无需处理
        $single_flg = FALSE;
        if(count($api_list) == count($api_list, 1))
        {
            $single_flg = TRUE;
            $api_list = array($api_list);
        }
        $notice_api = [];
        if(!empty($api_list))
        {
            $curr_time = time();
            $curr_date = date('Y/m/d H:i:s', $curr_time);
            /*
            // 基础短信模板参数数据
            $basic_sms_param = [
                '#AIRPORT#' => '',
                '#TIME#' => $curr_date,
            ];
            */
            $ch = curl_init();
            $timeout = 20;
            // 20秒没接受到数据的也视为有问题
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout - 2);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            foreach($api_list as $key => $item)
            {
                $item['check_time'] = $curr_time;
                $item['check_date'] = $curr_date;
                $item['sms_cla'] = $item['notice_sms_cla'];
                $item['sms_param'] = [];
                $item['url'] = $this->_url_stitch($item);
                $item['basic_error_msg'] = $this->config->item('system_name') . "在{$item['check_date']}监测到”{$item['name']}”发生故障。请立即处理。<br />【监控请求URL】{$item['url']}<br />【监控请求时间】{$item['check_date']}<br />【判定结果】";
                $item['is_normal'] = TRUE;
                switch ($item['type'])
                {
                    case 'interface':
                        //一般接口监测
                        $SSL = substr($item['url'], 0, 8) == "https://" ? TRUE : FALSE;
                        if ($SSL) {
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // 信任任何证书
                            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // 检查证书中是否设置域名
                        }
                        curl_setopt($ch, CURLOPT_URL, $item['url']);
                        $curl_result  = curl_exec($ch);
                        $curl_result = @json_decode($curl_result, TRUE);
                        $curl_info = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        if($curl_info != 200 || !is_array($curl_result))
                        {
                            $item['is_normal'] = FALSE;
                            $item['error_msg'] = '返回数据非JSON格式或状态不为200，解析失败';
                        }
                        else
                        {
                            if($item['check_type'] == 'status')
                            {
                                if(isset($curl_result[$item['status_key']]))
                                {
                                    if($curl_result[$item['status_key']] != $item['status_normal_value'])
                                    {
                                        $item['is_normal'] = FALSE;
                                        $item['error_msg'] = '返回状态“' . $item['status_key'] . '”，正常应返回“' . $item['status_normal_value'] . '”，' . '实际返回“' . $curl_result[$item['status_key']] . '”';
                                    }
                                }
                                else
                                {
                                    $item['is_normal'] = FALSE;
                                    $item['error_msg'] = '返回数据解析后未找到状态键“' . $item['status_key'] . '“';
                                }
                            }
                            elseif($item['check_type'] == 'not_empty')
                            {
                                if(empty($curl_result))
                                {
                                    $item['is_normal'] = FALSE;
                                    $item['error_msg'] = '返回数据为空，没有数据。';
                                }
                            }
                        }
                        break;
                    case 'socket':
                        //TODO
                        //SCOKET监测
                        break;
                }
                //处理短信模板
                /*
                if(!empty($item['notice_sms_cla']) && !empty($item['notice_sms_param_index']))
                {
                    $notice_sms_param_index = explode(',', $item['notice_sms_param_index']);
                    foreach ($notice_sms_param_index as $_index)
                    {
                        //基础参数直接赋值，否则特殊处理
                        $item['sms_param'][$_index] = isset($basic_sms_param[$_index]) ? $basic_sms_param[$_index] : $this->_get_sms_param($_index, $item);
                    }
                }
                */
                $notice_api[] = $item;
            }
            curl_close($ch);
        }
        if($single_flg)
        {
            $notice_api = isset($notice_api[0]) ? $notice_api[0] : $notice_api;
        }
        return $notice_api;
    }

    /*
     * 返回加入参数（包括加密）后的完整URL
     */
    public function _url_stitch($api, $timestamp = 0)
    {
        $config_model = & load_class('Config_model', 'common_model');
        $timestamp = !empty($timestamp) ? $timestamp : time();
        $api_verify_types = $config_model->api_verify_type();
        $url = $api['protocol'] . '://' . $api['url'];
        $param_array = json_decode($api['url_param'], TRUE);
        if(isset($param_array['SPECIAL_PARAM_REQUEST_TIME']))
        {
            if(!empty($param_array['SPECIAL_PARAM_REQUEST_TIME']))
            {
                $param_array[$param_array['SPECIAL_PARAM_REQUEST_TIME']] = $timestamp;
            }
            unset($param_array['SPECIAL_PARAM_REQUEST_TIME']);
        }
        switch ($api['url_verify'])
        {
            case '1':
                ksort($param_array);
                $key = md5($api_verify_types[$api['url_verify']]['secret'] . http_build_query($param_array));
                $param_array["key"] = $key;
                break;
        }
        $request_param_string = http_build_query($param_array);
        $return = !empty($request_param_string) ? "{$url}?{$request_param_string}" : $url;
        return $return;
    }
}