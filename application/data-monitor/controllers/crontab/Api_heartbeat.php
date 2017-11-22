<?php
/**
 * 机场通用API心跳监控
 *
 * @package pc/controllers/crontab
 * @access  public
 * @author  yuanyu <yuanyu@feeyo.com>
 * @link    https://
 */

class Api_heartbeat extends ACDM_Controller{

    public function __construct()
    {
        parent::__construct(FALSE);
        $this->load->model('Jk_api_model');
        $this->load->model('Jk_notice_log_model');
    }

    // 执行 中断时告警
    public function exec()
    {
        $redis = redis_connect();
        $redis_keys = $this->config->item('redis_key');
        $this->load->model('Basic_table_model');
        $api_list = $this->Jk_api_model->get_list_with_staff(['is_enable' => 1]);
        // 已离职和已删除的不作为通知对象
        $where = array('is_delete' => 0, 'is_valid' => 1);
        $staff_list = $this->Basic_table_model->bas_get_record_list('feeyo_staff', $where);
        $staff_list = array_column($staff_list, NULL, 'feeyo_staff_id');
        if (empty($api_list) || empty($staff_list))
        {
            exit;
        }
        $email_queue = $sms_queue = $insert_logs = $update_logs = [];
        //获取需要告警的API
        $api_list = $this->Jk_api_model->api_status_handler($api_list);
        foreach($api_list as $item)
        {
            $redis_key = str_replace('[API_ID]', $item['jk_api_id'], $redis_keys['api_heartbeat_monitor_status']);
            $monitor_status = $redis->get($redis_key);
            if($item['is_normal'] === FALSE)
            {
                //如果接口异常立即邮件告警通知责任人，其他人员作为抄送发送。
                if($monitor_status != 'abnormal')
                {
                    //如果没有告警才需通知，如果已经告警则无需再次告警
                    $relevants = explode(',', $item['relevant_staff']);
                    $email_queue[] = [
                        'to' => $staff_list[$item['author_staff']]['email'],
                        'to_name' => $staff_list[$item['author_staff']]['name'],
                        'copy_to' => $this->_get_staff_field($staff_list, $relevants, $item['author_staff'], 'email'),
                        'subject' => $this->config->item('system_name') . ' - API异常',
                        'content' => $item['basic_error_msg'] . $item['error_msg'],
                    ];
                    //如果需要额外短信通知
                    if(!empty($item['sms_cla']) && !empty($item['sms_param']))
                    {
                        $sms_queue[] = [
                            'to' => $staff_list[$item['author']]['mobile'],
                            'cla' => $item['sms_cla'],
                            'param' => $item['sms_param']
                        ];
                    }
                    //在ACDM数据监控基站记录日志
                    $insert_logs[] = [
                        'airport_iata' => $item['airport_iata'],
                        'ident' => $item['jk_api_id'],
                        'name' => $item['name'],
                        'url' => $item['url'],
                        'author_staff' => $item['author_staff'],
                        'info' => $item['error_msg'],
                        'content' => htmlspecialchars(addslashes($item['basic_error_msg'] . $item['error_msg'])),
                        'type' => 'api',
                        'stoppage_time' => $item['check_time'],
                    ];
                }
                $redis->setex($redis_key, 600, 'abnormal');
            }
            else
            {
                if($monitor_status == 'abnormal')
                {
                    //如果接口恢复
                    $last_log = $this->Jk_notice_log_model->get_last_log($item['jk_api_id']);
                    if(!empty($last_log))
                    {
                        $update_logs[$last_log['jk_notice_log_id']] = ['recover_time' => $item['check_time']];
                    }
                }
                $redis->setex($redis_key, 600, 'normal');
            }
        }
        $redis->close();

        //发送告警短信
        if(!empty($sms_queue))
        {
            $comm_sms_model = & load_class('Sms_model', 'common_model');
            foreach($sms_queue as $item)
            {
                $result = $comm_sms_model->send_by_feeyo_api($item['to'], $item['cla'], $item['param']);
                if($result === FALSE)
                {
                    //write_log('飞友短信发送失败', date('Y-m-d'), 'sms_send');
                }
            }
        }
        //发送告警邮件
        foreach($email_queue as $item)
        {
            $result = goms_send_mail($item['to'], $item['to_name'], $item['subject'], $item['content'], NULL, $item['copy_to']);
            /*
            $msg = "TO: {$item['to']}".PHP_EOL;
            $msg .= "CC: ".json_encode($item['copy_to']).PHP_EOL;
            $msg .= "TITLE: {$item['subject']}".PHP_EOL;
            $msg .= "CONTENT: {$item['content']}".PHP_EOL;
            if ($result === TRUE)
            {
                $msg .= "STATUS: \033[1;36mSUCCESS\033[0m";
            } else {
                $msg .= "STATUS: \033[1;31mError Info: {$result}\033[0m";
            }
            write_log($msg, date('Y-m-d'), 'email_sent');
            */
        }
        //记录中断告警日志
        if(!empty($insert_logs))
        {
            $result = $this->Basic_table_model->bas_batch_add_record('jk_notice_log', $insert_logs);
            if(!$result)
            {
                //write_log('notice log insert failed at api heartbeat', date('Y-m-d'), 'db_operation_error');
            }
        }
        //更新告警日志
        if(!empty($update_logs))
        {
            foreach($update_logs as $id => $item)
            {
                $result = $this->Basic_table_model->bas_update_record('jk_notice_log', $id, $item);
                if(!$result)
                {
                    //write_log('notice log insert failed at api heartbeat', date('Y-m-d'), 'db_operation_error');
                }
            }
        }

    }

    /**
     * 从用户列表中取出需要的键值 作为数组返回
     * @param $staff_list
     * @param $choose_staff
     * @param $author
     * @param $get
     */
    private function _get_staff_field($staff_list, $choose_staff, $author, $get)
    {
        $return = [];
        foreach($staff_list as $item)
        {
            if(in_array($item['feeyo_staff_id'], $choose_staff) && isset($item[$get]) && $item['feeyo_staff_id'] != $author)
            {
                array_push($return, $item[$get]);
            }
        }
        return $return;
    }
}