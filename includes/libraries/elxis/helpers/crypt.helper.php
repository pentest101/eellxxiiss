<?php 
/**
* @version		$Id: crypt.helper.php 1965 2018-08-19 11:39:50Z IOS $
* @package		Elxis
* @subpackage	Helpers / Encryption
* @copyright	Copyright (c) 2006-2018 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisCryptHelper {
	
	private $method = 'xor';//mcrypt/xor/openssl
	private $key = '';
	private $hash_type = 'sha1'; //sha1 or md5
	private $openssl_method = 'AES-256-CBC';//openssl_get_cipher_methods()


	/***************************************/
	/* DETERMINE ENCRYPTION METHOD AND KEY */
	/***************************************/
	public function __construct($params=null) {
		if ($params && is_array($params) && isset($params['method']) && isset($params['key'])) {
			$method = trim($params['method']);
			$this->key = trim($params['key']);
			if (isset($params['openssl_method']) && (trim($params['openssl_method']) != '')) { $this->openssl_method = $params['openssl_method']; }
		} else {
			$elxis = eFactory::getElxis();
			$method = $elxis->getConfig('ENCRYPT_METHOD');
			$this->key = trim($elxis->getConfig('ENCRYPT_KEY'));
		}

		if (($method == '') || ($method == 'auto') || ($method == 'mcrypt') || ($method == 'openssl')) {
			if (function_exists('mcrypt_encrypt')) {//php 5.x, php 7.0
				$this->method = 'mcrypt';
			} else if (function_exists('openssl_encrypt')) {//php 5.3.3+, php 7.1+
				$this->method = 'openssl';
			} else {
				$this->method = 'xor';
			}
		} else {
			$this->method = 'xor';
		}

		if ($this->key == '') {
			trigger_error('For security reasons an encryption key must be set in configuration.php file (parameter ENCRYPT_KEY).', E_USER_ERROR);
		}
	}


	/********************/
	/* ENCRYPT A STRING */
	/********************/
	public function encrypt($string, $key='') {
		$hashkey = $this->getMD5Key($key);
		$enc = $this->xor_encode($string, $hashkey);
		if ($this->method == 'mcrypt') {
			$enc = $this->mcrypt_encode($enc, $hashkey);
		} else if ($this->method == 'openssl') {
			$enc = $this->openssl_encode($enc, $hashkey);
		}

		return base64_encode($enc);
	}


	/********************/
	/* DECRYPT A STRING */
	/********************/
	public function decrypt($string, $key='') {
		$hashkey = $this->getMD5Key($key);
		if (preg_match('/[^a-zA-Z0-9\/\+=]/', $string)) { return false; }

		$dec = base64_decode($string);
		if ($this->method == 'mcrypt') {
			$dec = $this->mcrypt_decode($dec, $hashkey);
			if ($dec === false) { return false; }
		} else if ($this->method == 'openssl') {
			$dec = $this->openssl_decode($dec, $hashkey);
			if ($dec === false) { return false; }
		}

		$decrypted = $this->xor_decode($dec, $hashkey);
		return $decrypted;
	}


	/*****************************************/
	/* GET THE 128 BIT LENGTH ENCRYPTION KEY */
	/*****************************************/
	private function getMD5Key($key='') {
		if (trim($key) == '') { return md5($this->key); }
		return md5($key);
	}


	/******************************/
	/* ENCRYPT A STRING USING XOR */
	/******************************/
	private function xor_encode($string, $key) {
		$rand = '';
		while (strlen($rand) < 32) {
			$rand .= mt_rand(0, mt_getrandmax());
		}
		$rand = $this->hash($rand);
		$enc = '';
		for ($i = 0; $i < strlen($string); $i++) {			
			$enc .= substr($rand, ($i % strlen($rand)), 1).(substr($rand, ($i % strlen($rand)), 1) ^ substr($string, $i, 1));
		}

		return $this->xor_merge($enc, $key);
	}


	/********************************/
	/* DECRYPT A XOR ENCODED STRING */
	/********************************/
	private function xor_decode($string, $key) {
		$string = $this->xor_merge($string, $key);
		$dec = '';
		for ($i = 0; $i < strlen($string); $i++) {
			$dec .= (substr($string, $i++, 1) ^ substr($string, $i, 1));
		}
		return $dec;
	}


	/***********************************************************/
	/* COMPUTE THE DIFFERENCE BETWEEN STRING AND KEY USING XOR */
	/***********************************************************/
	private function xor_merge($string, $key) {
		$hash = $this->hash($key);
		$str = '';
		for ($i = 0; $i < strlen($string); $i++) {
			$str .= substr($string, $i, 1) ^ substr($hash, ($i % strlen($hash)), 1);
		}
		return $str;
	}


	/************************/
	/* HASH ENCODE A STRING */
	/************************/
	private function hash($string) {
		return ($this->hash_type == 'sha1') ? sha1($string) : md5($string);
	}


	/**********************************/
	/* ENCRYPT A STRING USING OPENSSL */
	/**********************************/
	private function openssl_encode($data, $key) {
		$iv_size = openssl_cipher_iv_length($this->openssl_method);
		$iv = openssl_random_pseudo_bytes($iv_size);
		$encrypted = openssl_encrypt($data, $this->openssl_method, $key, OPENSSL_RAW_DATA, $iv);
		return $this->addCipherNoise($iv.$encrypted, $key);
	}


	/*************************/
	/* DECRYPT USING OPENSSL */
	/*************************/
	private function openssl_decode($data, $key) {
		$data = $this->removeCipherNoise($data, $key);
		$iv_size = openssl_cipher_iv_length($this->openssl_method);
		if ($iv_size > strlen($data)) { return false; }
		$iv = substr($data, 0, $iv_size);
		$data = substr($data, $iv_size);
		$text = rtrim(openssl_decrypt($data, $this->openssl_method, $key, OPENSSL_RAW_DATA, $iv), "\0");
		return $text;
	}


	/*********************************/
	/* ENCRYPT A STRING USING MCRYPT */
	/*********************************/
	private function mcrypt_encode($data, $key) {
		$init_size = mcrypt_get_iv_size($this->getCipher(), $this->getMode());
		$init_vect = mcrypt_create_iv($init_size, MCRYPT_RAND);
		return $this->addCipherNoise($init_vect.mcrypt_encrypt($this->getCipher(), $key, $data, $this->getMode(), $init_vect), $key);
	}


	/************************/
	/* DECRYPT USING MCRYPT */
	/************************/
	private function mcrypt_decode($data, $key) {
		$data = $this->removeCipherNoise($data, $key);
		$init_size = mcrypt_get_iv_size($this->getCipher(), $this->getMode());
		if ($init_size > strlen($data)) { return false; }
		$init_vect = substr($data, 0, $init_size);
		$data = substr($data, $init_size);
		return rtrim(mcrypt_decrypt($this->getCipher(), $key, $data, $this->getMode(), $init_vect), "\0");
	}


	/*************************/
	/* GET CIPHER FOR MCRYPT */
	/*************************/
	private function getCipher() {
		return MCRYPT_RIJNDAEL_256;
	}


	/*************************/
	/* GET MCRYPT MODE VALUE */
	/*************************/
	private function getMode() {
		return MCRYPT_MODE_ECB;
	}


	/**********************************************/
	/* ADD PERMUTTED NOISE TO IV + ENCRYPTED DATA */
	/**********************************************/
	private function addCipherNoise($data, $key) {
		$keyhash = $this->hash($key);
		$keylen = strlen($keyhash);
		$str = '';
		for ($i = 0, $j = 0, $len = strlen($data); $i < $len; ++$i, ++$j) {
			if ($j >= $keylen) { $j = 0; }
			$str .= chr((ord($data[$i]) + ord($keyhash[$j])) % 256);
		}
		return $str;
	}


	/***************************************************/
	/* REMOVE PERMUTTED NOISE FROM IV + ENCRYPTED DATA */
	/***************************************************/
	private function removeCipherNoise($data, $key) {
		$keyhash = $this->hash($key);
		$keylen = strlen($keyhash);
		$str = '';
		for ($i = 0, $j = 0, $len = strlen($data); $i < $len; ++$i, ++$j) {
			if ($j >= $keylen) { $j = 0; }
			$temp = ord($data[$i]) - ord($keyhash[$j]);
			if ($temp < 0) { $temp = $temp + 256; }
			$str .= chr($temp);
		}
		return $str;
	}


	/****************************/
	/* GENERATE A PASSWORD SALT */
	/****************************/
	public function getEncryptedPassword($text) {
		$text = trim($text);
		if (strlen($text) < 4) { //not acceptable password, randomize it and make it useless
			$salt = $this->key.rand(10000000, 99999999);
			return $this->hash($salt);
		}
		$kparts = str_split($this->key);
		$tparts = str_split($text);
		$salt = '';
		if (count($kparts) > count($tparts)) {
			foreach ($kparts as $k => $char) {
				$salt .= $char;
				$salt .= (isset($tparts[$k])) ? $tparts[$k] : '';
			}
		} else {
			foreach ($tparts as $k => $char) {
				$salt .= $char;
				$salt .= (isset($kparts[$k])) ? $kparts[$k] : '';
			}
		}

		return $this->hash($salt);
	}

}

?>