<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| 所有通用设置放置于此
|--------------------------------------------------------------------------
*/

//系统名
$config['system_name'] = 'A-CDM数据监控';

//REDIS服务器
$config['redis_server'] = array(
    'host' => '10.59.113.30',
    'port' => '8080',
    'password' => 'toa_gxq_acdm_da786098c7c95271',
    'pconnect' => FALSE,
    'database' => 3,
);

//REDIS键
$config['redis_key'] = array(
    // 各API监控警告状态
    'api_heartbeat_monitor_status' => 'monitor:api_heartbeat:[API_ID]',
    // 各机场ACDM数据源状态
    'airport_data_source_status' => 'monitor:airport_data_source:[AIRPORT_IATA]',
);