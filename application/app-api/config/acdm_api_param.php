<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * +-----------------------------------
 * 接口对应参数配置
 * +-----------------------------------
 *
 * @Author: zhaobin <zhaobin@feeyo.com>
 */

// 公共参数中必传项[除key以外]
$config['api_param_common'] = array(
	'common' => array(
	    'v'            => array('name' => '接口版本号', 'ismust' => FALSE),
	    'device'       => array('name' => '设备类型，1：IOS，2：android ', 'ismust' => FALSE),
	    'device_info'  => array('name' => '设备信息', 'ismust' => FALSE),
	    'device_token' => array('name' => '推送使用的ID', 'ismust' => FALSE),
	    'unique_id'    => array('name' => '设备唯一id，用于标示设备唯一性 ', 'ismust' => FALSE),
	    'version'      => array("name" => "软件版本",'ismust'=>FALSE),
	)
);

$config['api_param'] = array(
    
	'account/verify/login' => array(
		'name'=>'用户登录',
		'param' => array(
		    'mobile'          => array('name' => '手机号', 'ismust' => TRUE),
		    'psw'             => array('name' => '密码', 'ismust' => TRUE),
		    'device_token'    => array('name' => '推送使用的ID', 'ismust' => TRUE),
		    'device'          => array('name' => '设备类型，1：IOS，2：android ', 'ismust' => TRUE),
		    'device_info'     => array('name' => '设备信息', 'ismust' => TRUE),
		    'unique_id'       => array('name' => '设备唯一id，用于标示设备唯一性 ', 'ismust' => TRUE),
		    'from_client'     => array('name' => '来源终端：acdm\wgs', 'ismust' => TRUE),
		    'version'         => array("name" => "软件版本",'ismust'=>TRUE),
		    'verifiy_code'    => array('name' => '验证码', 'ismust' => FALSE),
			'ratio'			  => array('name' => '设备分辨率','ismust'=>TRUE),
		    'appid'           => array('name' => 'APPID,合并版或具体机场APP', 'ismust' => FALSE),
		),
	),
    
    'account/verify/system_selection' => array(
        'name'=>'登陆系统选择',
        'param' => array(
            'user_id'         => array('name' => 'ACDM用户中心的用户ID', 'ismust' => TRUE),
            'uid'             => array('name' => '对应机场的用户ID', 'ismust' => TRUE),
            'from_client'     => array('name' => '来源终端：acdm/wgs', 'ismust' => TRUE),
            'api_domain'      => array("name" => "机场接口域名", 'ismust' => TRUE),
            'device_token'    => array('name' => '推送使用的ID', 'ismust' => TRUE),
            'device'          => array('name' => '设备类型，1：IOS，2：android ', 'ismust' => TRUE),
            'device_info'     => array('name' => '设备信息', 'ismust' => TRUE),
            'unique_id'       => array('name' => '设备唯一id，用于标示设备唯一性 ', 'ismust' => TRUE),
            'version'         => array("name" => "软件版本",'ismust'=>TRUE),
            'ratio'           => array("name" => "设备屏幕分辨率",'ismust'=>TRUE),
            'appid'           => array('name' => 'APPID,合并版或具体机场APP', 'ismust' => FALSE),
        ),
    ),
    
    'account/verify/logout' => array(
	   'name'=>'用户退出登录',
	   'param' => array(
            'user_id'       => array('name' => 'ACDM用户中心的用户ID', 'ismust' => TRUE),
            'uid'           => array('name' => '对应机场的用户ID', 'ismust' => TRUE),
            'api_domain'    => array("name" => "机场接口域名", 'ismust' => TRUE),
            'from_client'   => array('name' => '来源终端：acdm/wgs', 'ismust' => TRUE),
		),
	),
    
    'account/verify_code/send' => array(
        'name'=>'验证码发送',
        'param' => array(
            'mobile'        => array("name"=>"手机号", "ismust"=>TRUE),
            'from_client'   => array('name' => '来源终端：acdm\wgs', 'ismust' => TRUE),
        ),
    ),
    
    'account/verify_code/verify' 	=> array(
        'name'=>'验证码验证',
        'param' => array(
            'code'          => array("name" => "验证码", "ismust" => TRUE),
            'mobile'        => array("name" => "手机号", "ismust" => TRUE),
            'from_client'   => array('name' => '来源终端：acdm\wgs', 'ismust' => TRUE),
        ),
    ),
    
    'account/verify/change_psw' 	=> array(
        'name'=>'修改密码',
        'param' => array(
            'user_id'   => array('name' => 'ACDM用户中心的用户ID', 'ismust' => TRUE),
            'uid'       => array('name' => '对应机场的用户ID', 'ismust' => TRUE),
            'mobile'    => array("name" => "手机号", "ismust" => TRUE),
            'api_domain'=> array("name" => "机场接口域名", 'ismust' => TRUE),
            'code'      => array("name" => "验证码", "ismust" => TRUE),
            'psw'       => array("name" => "密码", "ismust" => TRUE),
        ),
    ),
);
?>
