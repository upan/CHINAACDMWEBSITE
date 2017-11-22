<?php

/**
 * +-----------------------------------
 * 
 * +-----------------------------------
 *
 * @Author: zhaobin <zhaobin@feeyo.com>
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Rsa {
	
	private $Private_key_path;			// 私钥路径
	private $Private_key_pkcs8_path;	// 私钥路径(pkcs8格式)
	private $Public_key_path;			// 公钥路径
	private $PublicKey;					// 公钥内容
	private $PrivateKey;				// 私钥内容
	private $PrivateKeyPkcs8;			// 私钥(pkcs8格式)内容
	private $Return;					// 公共返回数据
	
	public function __construct() {
		// 初始化返回数据
		$this->Return = array(
			'err' => 1,
			'data' => NULL
		);
		
		// 私钥路径
		$this->Private_key_path = APPPATH . "/rsa_key/rsa_private_key.pem";
		// 私钥路径(pkcs8格式)
		$this->Private_key_pkcs8_path = APPPATH . "/rsa_key/rsa_private_key_pkcs8.pem";
		// 公钥路径
		$this->Public_key_path = APPPATH . "/rsa_key/rsa_public_key.pem";
		
		// 获取密钥数据并验证可用性
		$result = $this->_init_key();
		if($result !== TRUE) {
			$this->Return['data'] = $result;
			
			return $this->Return;
			exit;
		}
	}
	
	/**
	 * 私钥加密
	 * -----------------------------------------
	 * 加密后的内容通常含有特殊字符，需要base64编码转换下
	 * 
	 * @param	$data	string	需加密的字符串
	 * @Author: zhaobin <zhaobin@feeyo.com>
	 */
	public function private_encode($data) {
		$encrypted = '';
		if(!openssl_private_encrypt($data, $encrypted, $this->PrivateKey)) {
			return FALSE;
		}else{
			return base64_encode($encrypted);
		}
	}
	
	/**
	 * 私钥解密
	 *
	 * @param	$data	string	需解密的字符串
	 * @Author: zhaobin <zhaobin@feeyo.com>
	 */
	public function private_decode($encrypted) {
		$decrypted = '';
		if(!openssl_private_decrypt(base64_decode($encrypted), $decrypted, $this->PrivateKey)) {
			return FALSE;
		}else{
			return $decrypted;
		}
	}
	
	/**
	 * 公钥加密
	 * -----------------------------------------
	 * 加密后的内容通常含有特殊字符，需要base64编码转换下
	 *
	 * @param	$data	string	需加密的字符串
	 * @Author: zhaobin <zhaobin@feeyo.com>
	 */
	public function public_encode($data) {
		$encrypted = '';
		if(!openssl_public_encrypt($data, $encrypted, $this->PublicKey)) {
			return FALSE;
		}else{
			return base64_encode($encrypted);
		}
	}
	
	/**
	 * 公钥解密
	 *
	 * @param	$data	string	需解密的字符串
	 * @Author: zhaobin <zhaobin@feeyo.com>
	 */
	public function public_decode($encrypted) {
		$decrypted = '';
		if(!openssl_public_decrypt(base64_decode($encrypted), $decrypted, $this->PublicKey)) {
			return FALSE;
		}else{
			return $decrypted;
		}
	}
	
	/**
	 * 初始化密钥数据
	 *
	 * @Author: zhaobin <zhaobin@feeyo.com>
	 */
	private function _init_key() {
		// 公钥内容
		$this->PublicKey = $this->_get_key_data($this->Public_key_path);
		// 获取公钥资源ID
		$this->PublicKey = openssl_pkey_get_public($this->PublicKey);
		if($this->PublicKey === FALSE) {
			return "The public key is not available";
		}
		
		// 私钥内容
		$this->PrivateKey = $this->_get_key_data($this->Private_key_path);
		// 获取私钥资源ID
		$this->PrivateKey = openssl_pkey_get_private($this->PrivateKey);
		if($this->PrivateKey === FALSE) {
			return "The private key is not available";
		}
		
		// 私钥(pkcs8格式)内容
// 		$this->PrivateKeyPkcs8 = $this->_get_key_data($this->Private_key_pkcs8_path);
// 		$this->PrivateKeyPkcs8 = openssl_pkey_get_private($this->PrivateKeyPkcs8);
// 		if($this->PrivateKeyPkcs8 === FALSE) {
// 			return "The pkcs8 private key is not available";
// 		}
		
		return TRUE;
	}
	
	/**
	 * 获取密钥数据
	 * 
	 * @param $filepath string 密钥文件路径
	 * @Author: zhaobin <zhaobin@feeyo.com>
	 */
	private function _get_key_data($filepath) {
		$fp = fopen($filepath, "r");
		$key_data = fread($fp, filesize($filepath));
		fclose($fp);
		
		return $key_data;
	}
}