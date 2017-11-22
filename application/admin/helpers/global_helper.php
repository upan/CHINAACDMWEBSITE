<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

if (! function_exists('getRedis')) {
    function getRedis($name, $key){
        $cache = new Redis();
        $r_config = get_config_item($name)[$key];
        $cache->pconnect($r_config['host'], $r_config['port']);
        $cache->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_NONE);
        return $cache;
    }
}

// 生成a标签按钮
if (! function_exists('gen_a_button')) {
    function gen_a_button($uri, $css, $label, $params){
        global $CI;
        $CI ?  : $CI = &get_instance();
        return $CI->m_admin->gen_a_button($uri, $css, $label, $params);
    }
}
// 生成a标签按钮
if (! function_exists('gen_button')) {
    function gen_button($uri, $css, $label, $call){
        global $CI;
        $CI ?  : $CI = &get_instance();
        return $CI->m_admin->gen_button($uri, $css, $label, $call);
    }
}

if (! function_exists('get_config_item')) {
    function get_config_item($item){
        global $CI;
        $CI ?  : $CI = &get_instance();
        return $CI->config->item($item);
    }
}

// 页面js提示框 swal,dialog
if (! function_exists('tips')) {
    function tips($type, $msg, $title = ""){
        $script = "<script>";
        switch ($type) {
            case 'swal':
                break;
            case 'dialog':
                break;
            default:
                break;
        }
        $script .= "</script>";
        echo $script;
    }
}

if (! function_exists('output')) {
    function output($response){
        $respLog = array();
        isset($response['code']) ? $respLog['code'] = $response['code'] : $respLog['code'] = 0;
        isset($response['sub_code']) ? $respLog['sub_code'] = $response['sub_code'] : $respLog['sub_code'] = 0;
        isset($response['desc']) ? $respLog['desc'] = $response['desc'] : $respLog['desc'] = '';
        
        $resp = http_build_query($respLog);
        
        header('Cache-Control: no-cache, must-revalidate');
        header("Content-Type: text/plain; charset=utf-8");
        Header("Log: $resp");
        ob_start('ob_gzip');
        echo json_encode($response);
        ob_end_flush(); // 输出压缩成果
        exit();
    }
}

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

if (! function_exists('writeLog')) {
    function writeLog($fileName, $msg, $log_type = "d"){
        $itime = time();
        $outputMsg = date('[H:i:s] ', $itime);
        gettype($msg) == "string" ? $outputMsg .= $msg : $outputMsg .= var_export($msg, true);
        $outputMsg .= "\r\n";
        $dirPath = LOG_PATH . DS . date('Ymd', $itime);
        $fileName = $log_type === "d" ? $fileName . "_" . date('Ymd', $itime) . ".log" : $fileName . "_" . date('Ymd_H', $itime) . ".log";
        if (! is_dir($dirPath)) {
            $rc1 = mkdir($dirPath, 0777);
            $rc2 = chmod($dirPath, 0777);
        }
        if (! file_exists("{$dirPath}/{$fileName}")) {
            $rc3 = touch("{$dirPath}/{$fileName}");
            $rc4 = chmod("{$dirPath}/{$fileName}", 0777);
        }
        
        $fp = fopen("{$dirPath}/{$fileName}", "a");
        fwrite($fp, $outputMsg);
        fclose($fp);
    }
}

// 校验参数是否合法;
if (!function_exists('check_params_validity')) {
    function check_params_validity($required_params = array(),$input_data = array(),$return_lost = FALSE){
        $data = $forbid = $lost = $return = array();
        empty($required_params) && $required_params = array();
        empty($input_data) && $input_data = array();
        foreach ($input_data as $key => $value) {
            $index = array_search($key, $required_params);
            if ($index === FALSE) {
                $forbid[] = $key;
            }else{
                $data[$key] = $value;
                unset($required_params[$index]);
            }
        }
        empty($required_params) ? :$lost = $required_params;
        if ($return_lost) {
            return array("lost"=>$lost,"data"=>$data);
        }else{
            return $lost ? FALSE : $data;
        }
    }
}

if (! function_exists('template')) {
    function template($tplfile = '', $data = array()){
        if (empty($tplfile)) {
            return FALSE;
        }
        global $CI;
        $CI ?  : $CI = &get_instance();
        return $CI->parser->parse($tplfile, $data, true);
    }
}

// curl 请求;
if (! function_exists('curl_http')) {
    function curl_http($url,$data = array(),$method = "POST",$params = array()){
        extract($params);
        isset($account) && !empty($account) ? : $account = FALSE;
        $is_ssl = isset($is_ssl) && !empty($is_ssl) ?  0 : 1;
        isset($isFollowLocation) && !empty($isFollowLocation) ? : $isFollowLocation = FALSE;
        
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, $is_ssl); // 对认证证书来源的检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, $is_ssl); // 从证书中检查SSL加密算法是否存在
        
        if ($account) {
            curl_setopt($curl, CURLOPT_USERPWD, $account);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        }
        
        $isFollowLocation && curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        if ($method == 'POST') {
            curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
            empty($data) ? : curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data)); // Post提交的数据包
        }
        set_time_limit(0);
        curl_setopt($curl, CURLOPT_TIMEOUT, 120); // 设置超时限制防止死循环 1分钟
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $tmpInfo = curl_exec($curl); // 执行操作
        if (curl_errno($curl)) {
            throw new Exception(curl_error($curl));
        }
        curl_close($curl); // 关闭CURL会话
        return $tmpInfo; // 返回数据
    }
}

if (! function_exists('convert_url_query')) {
    function convert_url_query($query){
        $queryParts = explode('&', $query);
        
        $params = array();
        foreach ($queryParts as $param) {
            $item = explode('=', $param);
            $params[$item[0]] = htmlentities($item[1]);
        }
        
        return $params;
    }
}


/**
 * 截取字符串
 * @param string $string 字符串
 * @param int $length 字符长度
 * @param string $dot 截取后是否添加...
 * @param string $charset编码            
 * @return string
 */
if (! function_exists('cutstr')) {
    function cutstr($string, $length, $dot = ' ...', $charset = 'utf-8'){
        if (strlen($string) <= $length) {
            return $string;
        }
        $string = str_replace(array(
            '&',
            '"',
            '<',
            '>'
        ), array(
            '&',
            '"',
            '<',
            '>'
        ), $string);
        $strcut = '';
        if (strtolower($charset) == 'utf-8') {
            $n = $tn = $noc = 0;
            while ($n < strlen($string)) {
                $t = ord($string[$n]); // ASCIIֵ
                if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
                    $tn = 1;
                    $n ++;
                    $noc ++;
                } elseif (194 <= $t && $t <= 223) {
                    $tn = 2;
                    $n += 2;
                    $noc += 2;
                } elseif (224 <= $t && $t < 239) {
                    $tn = 3;
                    $n += 3;
                    $noc += 2;
                } elseif (240 <= $t && $t <= 247) {
                    $tn = 4;
                    $n += 4;
                    $noc += 2;
                } elseif (248 <= $t && $t <= 251) {
                    $tn = 5;
                    $n += 5;
                    $noc += 2;
                } elseif ($t == 252 || $t == 253) {
                    $tn = 6;
                    $n += 6;
                    $noc += 2;
                } else {
                    $n ++;
                }
                if ($noc >= $length) {
                    break;
                }
            }
            if ($noc > $length) {
                $n -= $tn;
            }
            $strcut = substr($string, 0, $n);
        } else {
            for ($i = 0; $i < $length; $i ++) {
                $strcut .= ord($string[$i]) > 127 ? $string[$i] . $string[++ $i] : $string[$i];
            }
        }
        $strcut = str_replace(array(
            '&',
            '"',
            '<',
            '>'
        ), array(
            '&',
            '"',
            '<',
            '>'
        ), $strcut);
        return $strcut . $dot;
    }
}

/**
 * 字符串截取，支持中文和其他编码
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 * @return string
 */
if (! function_exists('msubstr')) {
    function msubstr($str, $length, $start = 0, $charset = "utf-8", $suffix = true){
        if (strlen($str) <= $length) {
            return $str;
        }
        if (function_exists("mb_substr"))
            $slice = mb_substr($str, $start, $length, $charset);
        elseif (function_exists('iconv_substr')) {
            $slice = iconv_substr($str, $start, $length, $charset);
            if (false === $slice) {
                $slice = '';
            }
        } else {
            $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
            $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
            $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
            $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
            preg_match_all($re[$charset], $str, $match);
            $slice = join("", array_slice($match[0], $start, $length));
        }
        return $suffix ? $slice . '...' : $slice;
    }
}

/**
 * 浏览器友好的变量输出
 * @param mixed $var 变量
 * @param boolean $echo 是否输出 默认为True 如果为false 则返回输出字符串
 * @param string $label 标签 默认为空
 * @param boolean $strict 是否严谨 默认为true
 * @return void|string
 */
if (! function_exists('dump')) {
    function dump($var, $echo = true, $label = null, $strict = true){
        $label = ($label === null) ? '' : rtrim($label) . ' ';
        if (! $strict) {
            if (ini_get('html_errors')) {
                $output = print_r($var, true);
                $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
            } else {
                $output = $label . print_r($var, true);
            }
        } else {
            ob_start();
            var_dump($var);
            $output = ob_get_clean();
            if (! extension_loaded('xdebug')) {
                $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
                $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
            }
        }
        if ($echo) {
            echo ($output);
            return null;
        } else
            return $output;
    }
}

/**
 * 创建目录
 * @param string $path 路径
 * @param string $mode 属性
 * @return string 如果已经存在则返回true，否则为flase
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
            @mkdir($cur_dir, 0777, true);
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

// 判断数组中的值 是否在 另一个数组范围内 一般用来验证参数是否合法
if (! function_exists('array_params_compare')) {
    function array_params_compare($a, $b){
        $tag = TRUE;
        foreach ($a as $_) {
            if (! in_array($_, $b)) {
                $tag = FALSE;
                break;
            }
        }
        return $tag;
    }
}

/**
* 获取请求ip
* @return ip地址
*/
if (! function_exists('ip')) {
    function ip() {
        if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
            $ip = getenv('REMOTE_ADDR');
        } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return preg_match ( '/[\d\.]{7,15}/', $ip, $matches ) ? $matches [0] : '';
    }
}

// excel_export_basic 基本导表设置
if (! function_exists('excel_export_basic')) {
    function excel_export_basic($title, $data, $filename = "", $merge = "", $width = 12, $height = 22){
        global $CI;
        if (! isset($CI))
            $CI = &get_instance();
        $CI->load->library("PHPExcel");
        $CI->load->library('PHPExcel/IOFactory');
        
        $php_excel = new PHPExcel();
        $php_excel->getDefaultStyle()
            ->getFont()
            ->setName('Arial');
        $cols_array = array(
            "A",
            "B",
            "C",
            "D",
            "E",
            "F",
            "G",
            "H",
            "I",
            "J",
            "K",
            "L",
            "M",
            "N",
            "O",
            "P",
            "Q",
            "R",
            "S",
            "T",
            "U",
            "V",
            "W",
            "X",
            "Y",
            "Z",
            "AA",
            "AB",
            "AC",
            "AD",
            "AE",
            "AF",
            "AG",
            "AH",
            "AI",
            "AJ",
            "AK",
            "AL",
            "AM",
            "AN",
            "AO",
            "AP",
            "AQ",
            "AR",
            "AS",
            "AT",
            "AU",
            "AV",
            "AW",
            "AX",
            "AY",
            "AZ",
            "BA",
            "BB",
            "BC",
            "BD",
            "BE",
            "BF",
            "BG",
            "BH",
            "BI",
            "BJ",
            "BK",
            "BL",
            "BM",
            "BN",
            "BO",
            "BP",
            "BQ",
            "BR",
            "BS",
            "BT",
            "BU",
            "BV",
            "BW",
            "BX",
            "BY",
            "BZ",
            "CA",
            "CB",
            "CC",
            "CD",
            "CE",
            "CF",
            "CG",
            "CH",
            "CI",
            "CJ",
            "CK",
            "CL",
            "CM",
            "CN",
            "CO",
            "CP",
            "CQ",
            "CR",
            "CS",
            "CT",
            "CU",
            "CV",
            "CW",
            "CX",
            "CY",
            "CZ",
            "DA",
            "DB",
            "DC",
            "DD",
            "DE",
            "DF",
            "DG",
            "DH",
            "DI",
            "DJ",
            "DK",
            "DL",
            "DM",
            "DN",
            "DO",
            "DP",
            "DQ",
            "DR",
            "DS",
            "DT",
            "DU",
            "DV",
            "DW",
            "DX",
            "DY",
            "DZ",
            "EA",
            "EB",
            "EC",
            "ED",
            "EE",
            "EF",
            "EG",
            "EH",
            "EI",
            "EJ",
            "EK",
            "EL",
            "EM",
            "EN",
            "EO",
            "EP",
            "EQ",
            "ER",
            "ES",
            "ET",
            "EU",
            "EV",
            "EW",
            "EX",
            "EY",
            "EZ",
            "FA",
            "FB",
            "FC",
            "FD",
            "FE",
            "FF",
            "FG",
            "FH",
            "FI",
            "FJ",
            "FK",
            "FL",
            "FM",
            "FN",
            "FO",
            "FP",
            "FQ",
            "FR",
            "FS",
            "FT",
            "FU",
            "FV",
            "FW",
            "FX",
            "FY",
            "FZ",
            "GA",
            "GB",
            "GC",
            "GD",
            "GE",
            "GF",
            "GG",
            "GH",
            "GI",
            "GJ",
            "GK",
            "GL",
            "GM",
            "GN",
            "GO",
            "GP",
            "GQ",
            "GR",
            "GS",
            "GT",
            "GU",
            "GV",
            "GW",
            "GX",
            "GY",
            "GZ",
            "HA",
            "HB",
            "HC",
            "HD",
            "HE",
            "HF",
            "HG",
            "HH",
            "HI",
            "HJ",
            "HK",
            "HL",
            "HM",
            "HN",
            "HO",
            "HP",
            "HQ",
            "HR",
            "HS",
            "HT",
            "HU",
            "HV",
            "HW",
            "HX",
            "HY",
            "HZ",
            "IA",
            "IB",
            "IC",
            "ID",
            "IE",
            "IF",
            "IG",
            "IH",
            "II",
            "IJ",
            "IK",
            "IL",
            "IM",
            "IN",
            "IO",
            "IP",
            "IQ",
            "IR",
            "IS",
            "IT",
            "IU",
            "IV",
            "IW",
            "IX",
            "IY",
            "IZ",
            "JA",
            "JB",
            "JC",
            "JD",
            "JE",
            "JF",
            "JG",
            "JH",
            "JI",
            "JJ",
            "JK",
            "JL",
            "JM",
            "JN",
            "JO",
            "JP",
            "JQ",
            "JR",
            "JS",
            "JT",
            "JU",
            "JV",
            "JW",
            "JX",
            "JY",
            "JZ",
            "KA",
            "KB",
            "KC",
            "KD",
            "KE",
            "KF",
            "KG",
            "KH",
            "KI",
            "KJ",
            "KK",
            "KL",
            "KM",
            "KN",
            "KO",
            "KP",
            "KQ",
            "KR",
            "KS",
            "KT",
            "KU",
            "KV",
            "KW",
            "KX",
            "KY",
            "KZ",
            "LA",
            "LB",
            "LC",
            "LD",
            "LE",
            "LF",
            "LG",
            "LH",
            "LI",
            "LJ",
            "LK",
            "LL",
            "LM",
            "LN",
            "LO",
            "LP",
            "LQ",
            "LR",
            "LS",
            "LT",
            "LU",
            "LV",
            "LW",
            "LX",
            "LY",
            "LZ",
            "MA",
            "MB",
            "MC",
            "MD",
            "ME",
            "MF",
            "MG",
            "MH",
            "MI",
            "MJ",
            "MK",
            "ML",
            "MM",
            "MN",
            "MO",
            "MP",
            "MQ",
            "MR",
            "MS",
            "MT",
            "MU",
            "MV",
            "MW",
            "MX",
            "MY",
            "MZ",
            "NA",
            "NB",
            "NC",
            "ND",
            "NE",
            "NF",
            "NG",
            "NH",
            "NI",
            "NJ",
            "NK",
            "NL",
            "NM",
            "NN",
            "NO",
            "NP",
            "NQ",
            "NR",
            "NS",
            "NT",
            "NU",
            "NV",
            "NW",
            "NX",
            "NY",
            "NZ",
            "OA",
            "OB",
            "OC",
            "OD",
            "OE",
            "OF",
            "OG",
            "OH",
            "OI",
            "OJ",
            "OK",
            "OL",
            "OM",
            "ON",
            "OO",
            "OP",
            "OQ",
            "OR",
            "OS",
            "OT",
            "OU",
            "OV",
            "OW",
            "OX",
            "OY",
            "OZ",
            "PA",
            "PB",
            "PC",
            "PD",
            "PE",
            "PF",
            "PG",
            "PH",
            "PI",
            "PJ",
            "PK",
            "PL",
            "PM",
            "PN",
            "PO",
            "PP",
            "PQ",
            "PR",
            "PS",
            "PT",
            "PU",
            "PV",
            "PW",
            "PX",
            "PY",
            "PZ",
            "QA",
            "QB",
            "QC",
            "QD",
            "QE",
            "QF",
            "QG",
            "QH",
            "QI",
            "QJ",
            "QK",
            "QL",
            "QM",
            "QN",
            "QO",
            "QP",
            "QQ",
            "QR",
            "QS",
            "QT",
            "QU",
            "QV",
            "QW",
            "QX",
            "QY",
            "QZ",
            "RA",
            "RB",
            "RC",
            "RD",
            "RE",
            "RF",
            "RG",
            "RH",
            "RI",
            "RJ",
            "RK",
            "RL",
            "RM",
            "RN",
            "RO",
            "RP",
            "RQ",
            "RR",
            "RS",
            "RT",
            "RU",
            "RV",
            "RW",
            "RX",
            "RY",
            "RZ",
            "SA",
            "SB",
            "SC",
            "SD",
            "SE",
            "SF",
            "SG",
            "SH",
            "SI",
            "SJ",
            "SK",
            "SL",
            "SM",
            "SN",
            "SO",
            "SP",
            "SQ",
            "SR",
            "SS",
            "ST",
            "SU",
            "SV",
            "SW",
            "SX",
            "SY",
            "SZ",
            "TA",
            "TB",
            "TC",
            "TD",
            "TE",
            "TF",
            "TG",
            "TH",
            "TI",
            "TJ",
            "TK",
            "TL",
            "TM",
            "TN",
            "TO",
            "TP",
            "TQ",
            "TR",
            "TS",
            "TT",
            "TU",
            "TV",
            "TW",
            "TX",
            "TY",
            "TZ",
            "UA",
            "UB",
            "UC",
            "UD",
            "UE",
            "UF",
            "UG",
            "UH",
            "UI",
            "UJ",
            "UK",
            "UL",
            "UM",
            "UN",
            "UO",
            "UP",
            "UQ",
            "UR",
            "US",
            "UT",
            "UU",
            "UV",
            "UW",
            "UX",
            "UY",
            "UZ",
            "VA",
            "VB",
            "VC",
            "VD",
            "VE",
            "VF",
            "VG",
            "VH",
            "VI",
            "VJ",
            "VK",
            "VL",
            "VM",
            "VN",
            "VO",
            "VP",
            "VQ",
            "VR",
            "VS",
            "VT",
            "VU",
            "VV",
            "VW",
            "VX",
            "VY",
            "VZ",
            "WA",
            "WB",
            "WC",
            "WD",
            "WE",
            "WF",
            "WG",
            "WH",
            "WI",
            "WJ",
            "WK",
            "WL",
            "WM",
            "WN",
            "WO",
            "WP",
            "WQ",
            "WR",
            "WS",
            "WT",
            "WU",
            "WV",
            "WW",
            "WX",
            "WY",
            "WZ",
            "XA",
            "XB",
            "XC",
            "XD",
            "XE",
            "XF",
            "XG",
            "XH",
            "XI",
            "XJ",
            "XK",
            "XL",
            "XM",
            "XN",
            "XO",
            "XP",
            "XQ",
            "XR",
            "XS",
            "XT",
            "XU",
            "XV",
            "XW",
            "XX",
            "XY",
            "XZ",
            "YA",
            "YB",
            "YC",
            "YD",
            "YE",
            "YF",
            "YG",
            "YH",
            "YI",
            "YJ",
            "YK",
            "YL",
            "YM",
            "YN",
            "YO",
            "YP",
            "YQ",
            "YR",
            "YS",
            "YT",
            "YU",
            "YV",
            "YW",
            "YX",
            "YY",
            "YZ",
            "ZA",
            "ZB",
            "ZC",
            "ZD",
            "ZE",
            "ZF",
            "ZG",
            "ZH",
            "ZI",
            "ZJ",
            "ZK",
            "ZL",
            "ZM",
            "ZN",
            "ZO",
            "ZP",
            "ZQ",
            "ZR",
            "ZS",
            "ZT",
            "ZU",
            "ZV",
            "ZW",
            "ZX",
            "ZY",
            "ZZ"
        );
        $param = array();
        foreach ($title as $key => $value) {
            // 表的第一行标题填充
            $param['style']['setvalue'][$cols_array[$key] . "1"] = $value;
            $php_excel->getActiveSheet()
                ->getColumnDimension($cols_array[$key])
                ->setWidth($width);
        }
        
        $title_count = count($title);
        $last_siffux = $title_count - 1;
        $data_count = count($data);
        $all_count = $data_count + 1;
        // $php_excel->getActiveSheet()->getStyle("A1:{$cols_array[$last_siffux]}{$all_count}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $param['style']["A1:{$cols_array[$last_siffux]}1"] = array(
            'font' => array(
                'bold' => TRUE
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
        $param['style']["A1:{$cols_array[$last_siffux]}{$all_count}"] = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN, // 细边框
                    'color' => array(
                        'argb' => 'FF555555'
                    )
                )
            )
        );
        $php_excel->getActiveSheet()
            ->getRowDimension(1)
            ->setRowHeight(28);
        $i = 2;
        foreach ($data as $key => $val) {
            $php_excel->getActiveSheet()
                ->getRowDimension($i)
                ->setRowHeight($height);
            $j = 0;
            foreach ($val as $sub_val) {
                $param['style']['setvalue'][$cols_array[$j] . $i] = $sub_val;
                $j ++;
            }
            $i ++;
        }
        
        if (! empty($merge)) {
            $param['style']["merge"] = array(
                $merge
            );
        }
        
        foreach ($param['style'] as $key => $style) {
            // 多个连续合并
            if ($key == 'merge' && is_array($style)) {
                foreach ($style as $merge) {
                    foreach ($merge as $val) {
                        if (preg_match('/(([A-Z]+)(\d+)(:)([A-Z]+)(\d+)$)/', $val)) {
                            // echo $val;
                            $php_excel->getActiveSheet()->mergeCells($val);
                            continue;
                        }
                    }
                }
            } else {
                // 单个合并
                if ($key == 'merge' && preg_match('/(([A-Z]+)(\d+)(:)([A-Z]+)(\d+)$)/', $style)) {
                    $php_excel->getActiveSheet()->mergeCells($style);
                    continue;
                }
            }
            
            if (preg_match('/([A-Z]+)(\d*)(:([A-Z]+)(\d*))?$/', $key)) {
                $php_excel->getActiveSheet()
                    ->getStyle($key)
                    ->applyFromArray($style);
                continue;
            }
            
            if ($key == "setvalue") {
                foreach ($style as $k => $val) {
                    $php_excel->getActiveSheet()->setCellValue($k, $val);
                }
                continue;
            }
            
            if ($key == "setheight") {
                foreach ($style as $k => $val) {
                    if (intval($k) > 0 && intval($val) > 0) {
                        $php_excel->getActiveSheet()
                            ->getRowDimension($k)
                            ->setRowHeight($val);
                    }
                }
            }
            
            if ($key == "setwidth") {
                foreach ($style as $k => $val) {
                    if ($val === TRUE) {
                        $php_excel->getActiveSheet()
                            ->getColumnDimension($k)
                            ->setAutoSize(TRUE);
                    } else 
                        if (intval($val) > 0) {
                            $php_excel->getActiveSheet()
                                ->getColumnDimension($k)
                                ->setWidth($val);
                        }
                }
            }
            
            if ($key == "header") {
                for ($i = 0; $i < count($style['cols']); $i ++) {
                    // 设置单元格值
                    $php_excel->getActiveSheet()->setCellValue($style['cols'][$i] . "1", $style['value'][$i]);
                    // 设置单元格样式
                    $php_excel->getActiveSheet()
                        ->getStyle($style['cols'][$i] . "1")
                        ->applyFromArray($style['style']);
                }
                foreach ($style['merge'] as $value) {
                    // 合并单元格
                    $php_excel->getActiveSheet()->mergeCells($value);
                }
            }
        }
        
        // $objWriter = IOFactory::createWriter($php_excel, 'Excel2007');
        // / $objWriter->save('php://output');
        $objWriter = IOFactory::createWriter($php_excel, 'Excel5');
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header("Content-Disposition: attachment; filename={$filename}");
        header("Content-Transfer-Encoding: binary");
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        $objWriter->save('php://output');
    }
}