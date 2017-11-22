<?php
/**
 * 数据监控本身故障监控
 *
 * @package pc/controllers/monitor
 * @access  public
 * @author  yuanyu <yuanyu@feeyo.com>
 * @link    https://
 */

class System_breakdown extends ACDM_Controller{

    private $comm_sms_model;
    public function __construct()
    {
        parent::__construct(FALSE);
        $this->load->model('Basic_table_model');
        $this->comm_sms_model = & load_class('Sms_model', 'common_model');
    }

    // 执行 故障时告警
    public function exec()
    {
        $curr_time = time();
        $curr_date = date('Y/m/d H:i:s', $curr_time);
        //短信告警队列
        $sms_notice_queue = [];

        //------------------------------------
        //1. 监控接收各机场ACDM数据源状态同步
        //------------------------------------
        $member_airports = $this->Basic_table_model->bas_get_record_list('member_airport');
        $redis = redis_connect();
        $redis_keys = $this->config->item('redis_key');
        // 超时时间 10分钟 最后数据接收时间如果在10分钟之前 代表数据中断
        $timeout_sec = 600;
        $notice_airport = [];

        foreach($member_airports as $item)
        {
            $redis_key = str_replace('[AIRPORT_IATA]', $item['airport_iata'], $redis_keys['airport_data_source_status']);
            $redis_data = $redis->get($redis_key);
            if(empty($redis_data))
            {
                array_push($notice_airport, $item['airport_iata']);
            }
            else
            {
                $redis_data = json_decode($redis_data, TRUE);
                if(($curr_time - $redis_data['last_time']) > $timeout_sec)
                {
                    array_push($notice_airport, $item['airport_iata']);
                }
            }
        }
        if(!empty($notice_airport))
        {
            $param = ['#TIME#' => $curr_date, '#CONTENT#' => "三字码为" . implode(',', $notice_airport) . "的ACDM至数据监控基站的数据源状态同步出现问题，数据已中断。"];
            $sms_notice_queue[] = ['cla' => 'notice_for_data_input', 'param' => $param];
        }




        //发送告警短信
        if(!empty($sms_notice_queue))
        {
            //监控系统责任人
            $where = ['is_system_author' => 1, 'is_valid' => 1, 'is_delete' => 0];
            $system_authors = $this->Basic_table_model->bas_get_record_list('feeyo_staff', $where);
            foreach($sms_notice_queue as $sms)
            {
                foreach($system_authors as $staff)
                {
                    $this->comm_sms_model->send_by_feeyo_api($staff['mobile'], $sms['cla'], $sms['param']);
                }
            }
        }
        $redis->close();
    }
}