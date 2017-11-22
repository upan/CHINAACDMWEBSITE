<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 密码生成器
 *
 * @Author: zhaobin <zhaobin@feeyo.com>
 */
if ( ! function_exists('generate_password'))
{
    function generate_password($length = 8)
    {
        // 密码字符集，可任意添加你需要的字符
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_ []{}<>~`+=,.;:/?|';

        $password = '';
        for($i = 0; $i < $length; $i++)
        {
            // 这里提供两种字符获取方式
            // 第一种是使用 substr 截取$chars中的任意一位字符；
            // 第二种是取字符数组 $chars 的任意元素
            // $password .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
			$password .= $chars[ mt_rand(0, strlen($chars) - 1) ];
		}

		return $password;
	}
}

/**
 * Redis服务连接
 *
 * @param   $param  array   参数数组，包括是否使用长连接、地址、端口号、数据库等
 * @return  $redis  object
 *
 * @Author: zhaobin <zhaobin@feeyo.com>
 */
if ( ! function_exists('redis_connect'))
{
    function redis_connect($param = array())
    {
        // 是否使用长连接
        $pconnect = FALSE;
        if (isset($param['pconnect']))
        {
            $pconnect = $param['pconnect'];
        }

        // redis服务地址
        $host = '127.0.0.1';
        if (isset($param['host']))
        {
            $host = $param['host'];
        }

        // redis服务端口
        $port = '6379';
        if (isset($param['port']))
        {
            $port = $param['port'];
        }

        // 数据库
        $database = 0;
        if (isset($param['database']))
        {
            $database = intval($param['database']);
        }

        $redis = new Redis();
        if ($pconnect === TRUE)
        {
            $redis->pconnect($host, $port);
        }
        else
        {
            // 未能完美的处理redis连接数持续处于很高的值的问题，即使手动close也并不会及时释放TCP连接
            // 系统依然会保持这个TCP连接一段时间，时间是msl的2倍，通常是1分钟。使用命令 cat /proc/sys/net/ipv4/tcp_fin_timeout可查看系统保持时间。
            // 此处暂时强制使用长连接，因长连接可被重用，暂可控制6379端口TCP连接数居高不下的问题。
            //$redis->connect($host, $port);
            $redis->pconnect($host, $port);
        }
        $redis->select($database);

        return $redis;
    }
}

/**
 * 使用redis中间件
 * @Author: guoxiaoqiang <guoxiaoqiang@variflight.com>
 */
if(! function_exists('redis_connect_new'))
{
    function redis_connect_new()
    {
        $redis = new Redis();
        $redis->connect('10.59.113.30','8080',3);
        $redis->auth('toa_gxq_acdm_da786098c7c95271');
        $redis->select(0);
        return $redis;
    }
}

/**
 * CURL POST
 *
 * @Author: zhaobin <zhaobin@feeyo.com>
 */
if ( ! function_exists('curl_post'))
{
    function curl_post($url, $content)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_TIMEOUT, 50);
        $result = curl_exec($ch);
        
        if ($result === FALSE)
        {
            $return_data = array(
                'code' => 1,
                'msg' => curl_error($ch),
                'data' => array()
            );
        }
        else
        {
            $return_data = json_decode($result, TRUE);
        }
        curl_close($ch);
        
        return $return_data;
    }
}

/**
 * 发送短信【飞友短信接口】
 *
 * @Author: zhaobin <zhaobin@feeyo.com>
 */
if ( ! function_exists('send_msg_by_feeyo_api'))
{
    function send_msg_by_feeyo_api($mobile, $tpl_cla, $tpl_param)
    {
        $template = array(
            // 短信验证码
            'verify_code' => '您正在修改系统账号密码，验证码：#CODE#，5分钟内有效，请勿泄露！',
        );
    
        if (isset($template[$tpl_cla]))
        {
            $content = urlencode(mb_convert_encoding(strtr($template[$tpl_cla], $tpl_param), 'gb2312', 'utf-8'));
    
            $url = "http://biz.feeyo.com/106/MmsSendEx2.asp?tel=" . $mobile . "&content=" . $content;
    
            $ch = curl_init();
            curl_setopt ($ch, CURLOPT_URL, $url);
            curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT,10);
            $send_result = curl_exec($ch);
            curl_close($ch);
    
            $send_result = mb_convert_encoding($send_result, 'utf-8', 'gb2312');
    
            if ($send_result == '发送成功')
            {
                return TRUE;
            }
            else
            {
                return FALSE;
            }
        }
        else
        {
            return FALSE;
        }
    }
}