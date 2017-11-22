<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * +-----------------------------------
 * Extending Core Class
 * +-----------------------------------
 *
 * @Author: zhaobin <zhaobin@feeyo.com>
 */

class ACDM_Controller extends CI_Controller {

	protected $ErrCode;
	protected $ApiPublicKey;
    
	public function __construct()
	{
	    parent::__construct();
	    
	    // 打开输出控制缓冲
	    ob_start();
	    header('Content-type: application/json; charset=utf-8');
	    
		// 统一响应代码集合数组
		$this->ErrCode = $this->config->item('err_code');
		// 接口公共密钥
		$this->ApiPublicKey = $this->config->item('api_public_key');

		// 接口参数配置集合数组
		$this->ApiParam = $this->config->item('api_param');
		$this->ApiParamCommon = $this->config->item('api_param_common');
	}

	/**
	 * 统一请求验证方法
	 *
	 * @Author: zhaobin <zhaobin@feeyo.com>
	 */
	protected function validation($request_key = '', $api = '')
	{
		// 接收参数
		$request_data = $this->I();
		// 获取请求密钥
		if(empty($request_key) && isset($request_data['key']))
		{
			$request_key = $request_data['key'];
		}
		if(empty($request_key))
		{
			$this->response(400);
		}

		// 接收请求接口
		if(empty($api)) {
			$api = uri_string();
		}
		
		if ( ! isset($this->ApiParam[$api]))
		{
		    // 接口不存在
		    $this->response(404);
		}
		
		// 获取接口下对应的版本的参数和公共的参数
		if (isset($this->ApiParam[$api]['param']))
		{
		    $param_arr = array_merge($this->ApiParamCommon['common'], $this->ApiParam[$api]['param']);
		}
		else
		{
		    $param_arr = $this->ApiParamCommon['common'];
		}
		
		// 将参数进行排序
		ksort($param_arr);
		// 验证是否存在多余参数
		foreach ($request_data as $key => $value)
		{
			if (($key != 'key') && ! array_key_exists($key, $param_arr))
			{

				$this->response(403);
			}

		}

		// 组装验证密钥
		$param_string = $this->ApiPublicKey;
		$param_tmp = array();
		foreach ($param_arr as $key => $row)
		{
			if ($row['ismust'] === TRUE)
			{
				// 验证必传参数是否缺失
				if ( ! isset($request_data[$key]))
				{
					$this->response(405);
				}
				$param_string .= '&'.$key.'='.$request_data[$key];

			}
		}

		$verify_key = md5($param_string);
		// 密钥验证
		if($request_key !== $verify_key) {
			// 密钥错误
			$this->response(402);
		}

		// 在传递user_id时 需要校验unique_id
		if (isset($request_data['user_id']) && ! empty($request_data['user_id']) && $api != 'v4/account/verify/logindo' && isset($request_data['unique_id']))
		{
			$this->load->model("user_model");
			$user = $this->user_model->get_user_by_id($request_data['user_id']);
			if (empty($user))
			{
				$this->response(101);
			}

			// 比对设备，更换设备时提示，存库
			if ($user['unique_id'] != $request_data['unique_id'])
			{
				$this->response(105);
			}
		}
        unset($request_data);
		
		return TRUE;
	}
	
	/**
	 * 统一接口响应处理
	 *
	 * @Author: zhaobin <zhaobin@feeyo.com>
	 */
	protected function response($code = NULL, $data = NULL, $showMsg = TRUE, $standard_json_flg = FALSE) 
	{
	    // 初始化响应数据
	    $response = array(
	        'code'	=> -1,
	        'data'	=> NULL,
	    );
	    if (($showMsg === TRUE) && isset($this->ErrCode[$code])) 
	    {
	        $response['msg'] = $this->ErrCode[$code];
	    }
	    elseif ($showMsg !== FALSE)
	    {
	        $response['msg'] = $showMsg;
	    }

	    if(!is_null($code))
	    {
	        $response['code'] = $code;
	        $response['data'] = $standard_json_flg ? $this->standard_json_encode_array($data) : $data;
	        if(($showMsg === TRUE) && isset($this->ErrCode[$code]))
	        {
	            $response['msg'] = $this->ErrCode[$code];
	        }
	    }
	    $response_json = json_encode($response);
	    // 加载RSA和AES加密类
	    $this->load->library('aes');
	    $this->load->library('rsa');
	    // 随机计算请求结果数据包加密密钥
	    $enctyptKey = generate_password(32);
	    // 使用AES加密算法加密并使用 MIME base64 对数据进行编码
	    $encrypted_data = $this->aes->encode($enctyptKey, $response_json);
	    // 使用RSA公钥加密用于数据包解密的密钥，并使用 MIME base64 对数据进行编码
	    $encrypted_key = $this->rsa->public_encode($enctyptKey);
	
	    // 响应数据包密文
	    $response = array(
	        'data' => $encrypted_data['data'],
	        'iv' => $encrypted_data['iv'],
	        'key' => $encrypted_key
	    );
	    echo json_encode($response);
	
	    // 捕获输出缓冲区的内容
	    $response = ob_get_contents();
	    ob_end_clean();
	
	    // 接口调用相关日志记录
	    //$this->_log($response);
	
	    // 将响应内容输出
	    echo $response;
	
	    unset($response);
	    exit;
	}
	
	private function _log($response)
	{
	    // 设备分类(1：IOS，2：android)
	    $device = $this->I('device');
	    // 设备名称
	    $device_info = $this->I('device_info');
	    // 设备唯一ID
	    $unique_id = $this->I('unique_id');
	    // APP版本
	    $version = $this->I('version');
	    // 总执行时间
	    $elapse_time = $this->benchmark->elapsed_time('total_execution_time_start', 'total_execution_time_end');
	    // 总内存消耗
	    $memory_usage = round(memory_get_usage() / 1024 / 1024, 2).'MB';
	    // HTTP USER AGENT
	    $http_user_agent = $this->input->user_agent();
	    // 请求头信息
	    $headers = json_encode($this->input->request_headers());
	    // 获取来源IP
	    $from_ip = $this->input->ip_address();
	    // 请求接口
	    $request_api = $_SERVER['PATH_INFO'];
	    // 请求参数
	    if (isset($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == 'POST'))
	    {
	        $request_param = file_get_contents('php://input', 'r');
	        $request_method = 'POST';
	    }
	    else
	    {
	        $request_param = $_SERVER['QUERY_STRING'];
	        $request_method = 'GET';
	    }
	
	    $log_data = array(
	        'device' => empty($device) ? '' : $device,
	        'device_info' => empty($device_info) ? '' : $device_info,
	        'unique_id' => empty($unique_id) ? '' : $unique_id,
	        'version' => empty($version) ? '' : $version,
	        'request_api' => $request_api,
	        'request_param' => $request_param,
	        'request_method' => $request_method,
	        'response' => $response,
	        'elapse_time' => $elapse_time,
	        'memory_usage' => $memory_usage,
	        'http_user_agent' => $http_user_agent,
	        'ip' => $from_ip,
	        'headers' => $headers,
	        'time' => time()
	    );
	    
	    return TRUE;
	}
	
	/**
	 * 自定义请求参数获取函数
	 *
	 * @Author: zhaobin <zhaobin@feeyo.com>
	 */
	protected function I($var = '', $default = NULL)
	{
	    $get = $this->input->get(NULL, TRUE);
	    $post = $this->input->post(NULL, TRUE);
	
	    if (empty($var))
	    {
	        if (!empty($get))
	        {
	            return $get;
	        }
	        elseif ( ! empty($post))
	        {
	            return $post;
	        }
	        else
	        {
	            return $default;
	        }
	    }
	    else
	    {
	        if (array_key_exists($var, $get))
	        {
	            return $get[$var];
	        }
	        elseif (array_key_exists($var, $post))
	        {
	            return $post[$var];
	        }
	        else
	        {
	            return $default;
	        }
	    }
	}
	
	/**
	 * 为JSON格式标准化数组,空数组作为对象
	 * 
	 * @param  array   $data_array 原始数组
	 * @return array
	 *
	 * @Author: zhaobin <zhaobin@feeyo.com>
	 */
	protected function standard_json_encode_array($data_array)
	{
		if (empty($data_array)) 
		{
			return $data_array;
		}
		
		$std_class = new stdClass();
		
		$return = array();
		foreach($data_array as $key=>$val) 
		{
			$return[$key] = (is_array($val) && empty($val)) ? $std_class : $val;
		}
		
		return $return;
	}
}
