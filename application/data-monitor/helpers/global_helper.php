<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
// ob_gzip压缩
if (! function_exists('ob_gzip')) {
    function ob_gzip($content){
        if (! headers_sent() && extension_loaded("zlib") && isset($_SERVER["HTTP_ACCEPT_ENCODING"]) && strstr($_SERVER["HTTP_ACCEPT_ENCODING"], "gzip")) {
            $content = gzencode($content, 9);
            header("Content-Encoding: gzip");
            header("Vary: Accept-Encoding");
            header("Content-Length: " . strlen($content));
        }
        return $content;
    }
}

/**
 * 创建目录
 * @param string $path 路径
 * @param string $mode 属性
 * @return string 如果已经存在则返回TRUE，否则为flase
 */
if (! function_exists('dir_create')) {
    function dir_create($path, $mode = 0777){
        if (is_dir($path))
            return TRUE;
        $ftp_enable = 0;
        $path = dir_path($path);
        $temp = explode('/', $path);
        $cur_dir = '';
        $max = count($temp) - 1;
        for ($i = 0; $i < $max; $i ++) {
            $cur_dir .= $temp[$i] . '/';
            if (@is_dir($cur_dir))
                continue;
            @mkdir($cur_dir, 0777, TRUE);
            @chmod($cur_dir, 0777);
        }
        return is_dir($path);
    }
}

/**
 * 删除目录及目录下面的所有文件
 * @param string $dir 路径
 * @return bool 如果成功则返回 TRUE，失败则返回 FALSE
 */
if (! function_exists('dir_delete')) {
    function dir_delete($dir){
        $dir = dir_path($dir);
        if (! is_dir($dir))
            return FALSE;
        $list = glob($dir . '*');
        foreach ($list as $v) {
            is_dir($v) ? dir_delete($v) : @unlink($v);
        }
        return @rmdir($dir);
    }
}

/**
 * 写日志
 *
 * @param	$msg          内容
 * @param	$file_name    文件名
 * $param   $directory    目录名 application/log/?
 *
 * @Author: zhaobin <zhaobin@feeyo.com>
 */
if ( ! function_exists('write_log')) {
    function write_log($msg = '', $file_name = '', $directory = 'flight_dynamic_update')
    {
        $CI =& get_instance();

        $log_path = $CI->config->item('log_path');
        $log_file_extension = $CI->config->item('log_file_extension');
        if (empty($log_path))
        {
            $log_path = 'logs/';
        }
        $log_path = APPPATH . $log_path . $directory . DIRECTORY_SEPARATOR . date('Y-m-d');

        if ( ! file_exists($log_path))
        {
            mkdir($log_path, 0755, TRUE);
        }

        if (empty($file_name))
        {
            $file_name = date('Ymd');
        }
        $file_path = $log_path . DIRECTORY_SEPARATOR . $file_name . $log_file_extension;

        // 打开文件并写入内容
        $fp = fopen($file_path, 'a+');
        fwrite($fp, date('Y-m-d H:i:s') . PHP_EOL . $msg . PHP_EOL . PHP_EOL);
        fclose($fp);
    }
}

//redis连接
if (!function_exists('redis_connect')) {
    function redis_connect($param = array())
    {
        $CI = &get_instance();
        $redis_set = $CI->config->item('redis_server');
        // 是否使用长连接
        $pconnect = isset($param['pconnect']) ? $param['pconnect'] : $redis_set['pconnect'];
        // HOST
        $host = isset($param['host']) ? $param['host'] : $redis_set['host'];
        // 端口
        $port = isset($param['port']) ? $param['port'] : $redis_set['port'];
        // 密码
        $password = isset($param['password']) ? $param['password'] : $redis_set['password'];
        // 库号
        $database = isset($param['database']) ? $param['database'] : $redis_set['database'];
        $redis = new Redis();
        if($pconnect === TRUE)
        {
            $redis->pconnect($host, $port, $database);
        }
        else
        {
            $redis->connect($host, $port, $database);
        }
        if(!empty($password))
        {
            $redis->auth($password);
        }
        return $redis;
    }
}

//字符串截取 多余部门省略号代替
if (!function_exists('mb_subtext')) {
    function mb_subtext($text, $length = 50)
    {
        $text = mb_strlen($text, 'utf8') > $length ? mb_substr($text, 0, $length, 'utf8') . '...' : $text;
        return $text;
    }
}

/**
 * 获取跨日/天/月查询WHERE参数的初始化的时间戳方法
 * @param string $start_date 起始日期
 * @param string $end_date 结束日期
 * @param string $date_type 统计日期方式 h 小时 d 天 m 月
 * @param int $timerange_pattern 时间间隔模式 0 生产日 1 自然日
 * @return array
 */
if (!function_exists('get_init_timestamp')) {
    function get_init_timestamp($start_date, $end_date, $date_type = "d", $timerange_pattern = 1, $auto_hour_set = TRUE)
    {
        switch ($date_type) {
            case "h":
                $start_date_timestamp = $auto_hour_set ? strtotime($start_date . ":00:00") : strtotime($start_date);
                $end_date_timestamp = $auto_hour_set ? strtotime($end_date . ":59:59") : strtotime($end_date);
                break;
            case "d":
                $start_date_timestamp = $timerange_pattern == 0 ? strtotime($start_date . " 06:00:00") : strtotime($start_date . " 00:00:00");
                $end_date_timestamp = $timerange_pattern == 0 ? strtotime($end_date . " 05:59:59 +1 day") : strtotime($end_date . " 23:59:59");
                break;
            case "m":
                $start_date_timestamp = $timerange_pattern == 0 ? strtotime($start_date . "-01 06:00:00") : strtotime($start_date . "-01 00:00:00");
                $end_date_timestamp = $timerange_pattern == 0 ? strtotime(date("Y-m-t", strtotime($end_date . "-01")) . " 05:59:59 +1 day") : strtotime(date("Y-m-t", strtotime($end_date . "-01")) . " 23:59:59");
                break;
        }
        return array("start_timestamp" => $start_date_timestamp, "end_timestamp" => $end_date_timestamp);
    }
}

if(! function_exists('curl_post')) {
    /**
     * curl POST
     *
     * @param   string  url
     * @param   array   数据
     * @param   int     请求超时时间
     * @param   bool    CA证书路径 如果指定后 将会用此证书进行严格认证
     * @return  string
     */
    function curl_post($url, $data = array(), $timeout = 30, $cacert_path = '')
    {
        $SSL = substr($url, 0, 8) == "https://" ? TRUE : FALSE;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout - 2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if($SSL)
        {
            if(!empty($cacert_path))
            {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);   // 只信任CA颁布的证书
                curl_setopt($ch, CURLOPT_CAINFO, $cacert_path); // CA根证书（用来验证的网站证书是否是CA颁布）
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // 检查证书中是否设置域名，并且是否与提供的主机名匹配
            }
            else
            {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // 信任任何证书
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // 检查证书中是否设置域名
            }
        }
        if(!empty($data))
        {
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:')); //避免data数据过长问题
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        $ret = curl_exec($ch);
        curl_close($ch);
        return $ret;
    }
}

if ( ! function_exists('get_post_data')) {
    /**
     * @param string $format 接收数据格式
     * @return array|string
     */
    function get_post_data($format = 'json')
    {
        $post_data = file_get_contents('php://input', 'r');
        if ($format === 'json')
        {
            $array_data = json_decode($post_data, TRUE);
            if (empty($array_data))
            {
                return array();
            }
            else
            {
                return $array_data;
            }
        }
        else
        {
            return $post_data;
        }
    }
}

if ( ! function_exists('data_build_string')) {
    /**
     *  数据转为STRING后SHA1作为签名
     * @param $data
     * @return string
     */
    function data_build_string($data)
    {
        //数据类型检测
        if (!is_array($data)) {
            $data = (array)$data;
        }
        ksort($data); //排序
        $code = http_build_query($data); //url编码并生成query字符串
        $sign = sha1($code); //生成签名
        return $sign;
    }
}

