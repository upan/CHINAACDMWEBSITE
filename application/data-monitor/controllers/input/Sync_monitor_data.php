<?php
/**
 * 用于同步每个机场ACDM监控数据的脚本
 *
 * @package path
 * @access  public
 * @author  yuanyu <yuanyu@feeyo.com>
 * @link    https://
 */

class Sync_monitor_data extends ACDM_Controller{

    function __construct()
    {
        parent::__construct(FALSE);
    }

    private function _check_ip($ip)
    {
        $allow_ip_array = array(
            '10.34.11.50',      // PROXY
            '10.34.11.51',      // PROXY
            '121.42.225.159',   // BETA
            '120.24.210.11',    // KMG
            '120.24.213.113',   // ZUH SWA HKG
            '123.57.8.127',     // KWE CSX
            '123.57.229.209',   // YIN NLT MIG
            '115.29.170.161',   // WUX HUZ
            '115.29.167.137',   // NGB NKG
            '121.40.74.88',     // PVG
            '121.42.225.187',   // TSN SHE LHW
            '118.31.237.75',    // HGH
            '120.25.88.76',     // HNA SHA
        );
        $response = ['success' => 0, 'msg' =>  'Access denied:' . $ip];
        if (!in_array($ip, $allow_ip_array))
        {
            echo json_encode($response);
            exit;
        }
    }

    //接收各机场ACDM推过来的数据源状态
    public function data_source()
    {
        $this->load->model('Jk_notice_log_model');
        $this->load->model('Basic_table_model');
        ini_set('memory_limit', '1024M');
        set_time_limit(300);
        $ip = $this->input->ip_address();
        //开发中 暂时不验证IP
        //$this->_check_ip($ip);
        $response = ['success' => 0, 'msg' => ''];
        $post_data = get_post_data('json');
        $currtime = time();
        //存入REDIS
        if(!empty($post_data))
        {
            $redis = redis_connect();
            $redis_keys = $this->config->item('redis_key');
            $redis_key = str_replace('[AIRPORT_IATA]', $post_data['airport_iata'], $redis_keys['airport_data_source_status']);
            $curr_json = $redis->get($redis_key);
            $curr_data = [];
            $insert_logs = [];
            $update_logs = [];
            if(!empty($curr_json))
            {
                $curr_data = json_decode($curr_json, TRUE);
            }
            $data = ['last_time' => $currtime, 'data' => []];
            //各机场数据源监控的处理方式是：只有中断或者中断后恢复才进行REDIS SET，所以当没有值的时候 代表接口是正常的。
            foreach($post_data['source_data_update'] as $key => $item)
            {
                $data['data'][$key] = [
                    'key' => $key, //数据源KEY
                    'name' => $item['name'], //数据源名
                    'send_notice' => $item['notice'], //中断时是否告警
                    'last_time' => $item['last_time'], //最后一次数据源接收时间
                    'status' => $item['status']//数据源状态 没有值：正常  has_been_restored:恢复正常  has_been_interrupted:中断
                ];
                // 如果中断或再次中断 需要记录告警日志，如果记录恢复 记录恢复时间
                if($item['status'] == 'has_been_interrupted')
                {
                    // 只记录首次中断或再次中断
                    if(!isset($curr_data['data'][$key]) || $curr_data['data'][$key]['status'] != 'has_been_interrupted')
                    {
                        $last_time = !empty($item['last_time']) ? date("Y/m/d H:i:s", $item['last_time']) : '-';
                        $insert_logs[] = [
                            'ident' => $key,
                            'name' => $item['name'],
                            'airport_iata' => $post_data['airport_iata'],
                            'info' => "{$post_data['airport_iata']}-{$key}数据中断",
                            'content' => "ACDM数据中断: {$post_data['airport_iata']}系统{$item['name']}发生中断，最后一条数据停留在{$last_time}",
                            'type' => 'data_source',
                            'stoppage_time' => $currtime
                        ];
                    }
                }
                elseif($item['status'] == 'has_been_restored')
                {
                    // 记录恢复时间
                    if(!isset($curr_data['data'][$key]) || $curr_data['data'][$key]['status'] != 'has_been_restored')
                    {
                        $last_log = $this->Jk_notice_log_model->get_last_log($key);
                        if(!empty($last_log))
                        {
                            $update_logs[$last_log['jk_notice_log_id']] = ['recover_time' => $currtime];
                        }
                    }
                }
            }
            $return = $redis->set($redis_key, json_encode($data));
            $redis->close();
            $response['success'] = $return ? 1 : 0;

            // 记录日志执行
            if(!empty($insert_logs))
            {
                $this->Basic_table_model->bas_batch_add_record('jk_notice_log', $insert_logs);
            }
            if(!empty($update_logs))
            {
                foreach($update_logs as $id => $update_log)
                {
                    $this->Basic_table_model->bas_update_record('jk_notice_log', $id, $update_log);
                }
            }
        }
        echo json_encode($response);
    }
}