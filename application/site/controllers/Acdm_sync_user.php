<?php

/**
 * +-----------------------------------
 * ACDM系统用户同步
 * +-----------------------------------
 *
 * @Author: zhaobin <zhaobin@feeyo.com>
 */

class Acdm_sync_user extends CI_Controller {
    
    /**
     * 允许客户端IP请求
     *
     * @var bool
     */
    private $allow_ip_array = array(
        '10.34.11.55',      // PROXY
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
    
    function __construct()
    {
        parent::__construct();
        
        ini_set('memory_limit', '1024M');
        set_time_limit(300);
    }
    
    /**
     * 接收来自各A-CDM成员机场系统用户的同步请求
     *
     * @Author: zhaobin <zhaobin@feeyo.com>
     */
    public function receive()
    {
        // 初始化响应数据
        $response_data = array(
            'code' => 1,
            'msg' => '',
            'total_num' => 0,
            'handled_num' => 0
        );
        
        // 请求来源客户端IP验证
        $ip = $this->input->ip_address();
        if ( ! in_array($ip, $this->allow_ip_array))
        {
            $response_data['msg'] = 'Access denied:'.$ip;
            echo json_encode($response_data);
            exit;
        }
        
        // 提取接收到的POST数据
        $post_data = file_get_contents('php://input', 'r');
        $post_data = json_decode($post_data, TRUE);
        
        if ( ! empty($post_data))
        {
            $response_data['total_num'] = count($post_data);
            
            // 获取数据接收日志数据表字段列表
            $desc_table = $this->db->query("DESC {$this->db->dbprefix}user")->result_array();
            $field_list = array_column($desc_table, 'Field');
            
            foreach ($post_data as $row)
            {
                foreach ($row as $field => $value)
                {
                    // 检测字段是否存在，若不存在则自动创建字段
                    if ( ! in_array($field, $field_list))
                    {
                        $query_result = $this->db->query("ALTER TABLE `{$this->db->dbprefix}user` ADD `{$field}` VARCHAR(254) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''");
                        if ($query_result !== FALSE)
                        {
                            $field_list[] = $field;
                        }
                    }
                    
                    if ( ! in_array($field, $field_list))
                    {
                        $response_data['msg'] = "Database field is missing:{$field}";
                        echo json_encode($response_data);
                        exit;
                    }
                    
                    if ($value === NULL)
                    {
                        $row[$field] = '';
                    }
                }
                
                // 组装用户中心的用户ID（MD5(机场三字码+手机号)）
                $user_id = md5($row['airport_iata'].$row['mobile']);
                
                // 检测用户是否已存在
                $exists = $this->db->select('user_id')->from('user')->where('user_id', $user_id)->get()->row_array();
                if (empty($exists))
                {
                    $row['user_id'] = $user_id;
                    $this->db->insert('user', $row);
                    
                    $result = $this->db->insert_id();
                }
                else
                {
                    $this->db->where('user_id', $user_id)->update('user', $row);
                    
                    $result = $this->db->affected_rows();
                }
                
                if ($result === FALSE)
                {
                    $response_data['msg'] = "User data save failed".PHP_EOL.json_encode($row);
                    echo json_encode($response_data);
                    exit;
                }
                else
                {
                    $response_data['handled_num']++;
                }
            }
        }
        
        $response_data['code'] = 0;
        $response_data['msg'] = 'Data synchronization is successful';
        echo json_encode($response_data);
        exit;
    }
}