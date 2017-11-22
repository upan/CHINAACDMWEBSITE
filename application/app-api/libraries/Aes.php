<?php

/**
 * +-----------------------------------
 * AES加密类
 * +-----------------------------------
 *
 * @Author: zhaobin <zhaobin@feeyo.com>
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Aes {
	
	// 算法,另外还有256和192两种长度 
	const CIPHER = MCRYPT_RIJNDAEL_128;
	
	// 模式
	const MODE = MCRYPT_MODE_CBC;
	
	/**
	 * 加密
	 * 
	 * @param	string	$key	密钥
	 * @param	string	$data	需加密的字符串
	 * @Author: zhaobin <zhaobin@feeyo.com>
	 */
	public function encode($key, $data) {
		$td = mcrypt_module_open(self::CIPHER, '', self::MODE, '');
		$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
		mcrypt_generic_init($td, $key, $iv);
		$encrypted = mcrypt_generic($td, $data);
		mcrypt_generic_deinit($td);
		
		return array(
				'data' => base64_encode($encrypted),
				'iv' => base64_encode($iv)
		);
	}
	 
	public function decode($key, $iv, $data) {
		$data = base64_decode($data);
		$iv = base64_decode($iv);
		$td = mcrypt_module_open(self::CIPHER, '', self::MODE, '');
		mcrypt_generic_init($td, $key, $iv);
		$data = mdecrypt_generic($td, $data);
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);
		 
		return trim($data);
	}
}