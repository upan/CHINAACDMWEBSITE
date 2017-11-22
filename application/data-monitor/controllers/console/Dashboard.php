<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends ACDM_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Jk_api_model');
        $this->load->model('Jk_notice_log_model');
        $this->load->model('Member_airport_model');
    }


	public function index()
    {
        $date = date('Y-m-d');
        //基站监控
        $this->bas_var['status_total'] = ['normal' => 0, 'abnormal' => 0, 'uncheck' => 0, 'notice' => 0];
        $this->bas_var['api_list'] = $this->Jk_api_model->get_list_with_staff(['is_enable' => 1]);
        //获取今日告警数
        $where = get_init_timestamp($date, $date);
        $this->bas_var['status_total']['notice'] = $this->Jk_notice_log_model->get_total($where);
        //只显示启用接口的监控信息
        $redis = redis_connect();
        $redis_keys = $this->config->item('redis_key');
        foreach($this->bas_var['api_list'] as $key => $item)
        {
            $redis_key = str_replace('[API_ID]', $item['jk_api_id'], $redis_keys['api_heartbeat_monitor_status']);
            $monitor_status = $redis->get($redis_key);
            $_status = !empty($monitor_status) ? $monitor_status : 'uncheck';
            $this->bas_var['api_list'][$key]['monitor_status'] = $_status;
            $this->bas_var['status_total'][$_status] ++;
        }


        //各ACDM成员集团主机场监控
        $this->bas_var['data_source'] = [];
        $main_iatas = $this->Member_airport_model->get_main_airport_iata();
        foreach($main_iatas as $iata)
        {
            //各机场数据源监控BEGIN
            $redis_key = str_replace('[AIRPORT_IATA]', $iata, $redis_keys['airport_data_source_status']);
            $data_source = $redis->get($redis_key);
            if($data_source === FALSE)
            {
                continue;
            }
            $data = json_decode($data_source, TRUE);
            if(is_array($data['data']) && !empty($data['data']))
            {
                foreach($data['data'] as $item)
                {
                    $item['airport_iata'] = $iata;
                    $_status = $item['status'] == 'has_been_interrupted' ? 'abnormal' : 'normal';
                    $item['monitor_status'] = $_status;
                    $this->bas_var['status_total'][$_status] ++;
                    $this->bas_var['data_source'][$item['key']] = $item;
                }
            }
            //各机场数据源监控END
        }
        $redis->close();
	    $this->load->view('console/dashboard/index', $this->bas_var);
	}
}
