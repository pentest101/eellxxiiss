<?php 
/**
* @version		$Id: captcha.helper.php 1870 2016-07-18 15:54:27Z sannosi $
* @package		Elxis
* @subpackage	Helpers/Captcha
* @copyright	Copyright (c) 2006-2019 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisCaptchaHelper {

	private $errormsg = '';


	/***************/
	/* CONSTRUCTOR */
	/***************/
	public function __construct() {
	}


	/*********************/
	/* GET ERROR MESSAGE */
	/*********************/
	public function getError() {
		return $this->errormsg;
	}


	/***********************/
	/* RESET ERROR MESSAGE */
	/***********************/
	public function resetError() {
		$this->errormsg = '';
	}


	/***********************************/
	/* GENERATE CAPTCHA KEY (NO ROBOT) */
	/***********************************/
	public function generate($custom='') {
		$all_elements = array(
			'CONFIG_DB_HOST', 
			'CONFIG_DB_USER', 
			'CONFIG_DEFAULT_ROUTE', 
			'CONFIG_DEFENDER', 
			'CONFIG_MAIL_EMAIL', 
			'CONFIG_MAIL_MANAGER_EMAIL', 
			'CONFIG_REPO_PATH',
			'CONFIG_SITELANGS',
			'CONFIG_TIMEZONE', 
			'CONFIG_MAIL_SMTP_HOST', 
			'CONFIG_SESSION_HANDLER',
			'IP',
			'AGENT',
			'HTLANGUAGE',
			'PATH',
			'REVISION',
			'RELDATE',
		);

		shuffle($all_elements);

		$elxis = eFactory::getElxis();

		$elements = array();
		$num = rand(6, 10);
		$unenc = '';
		foreach ($all_elements as $element) {
			if (count($elements) >= $num) { break; }
			if (strpos($element, 'CONFIG_') === 0) {//Elxis configuration option
				$x = str_replace('CONFIG_', '', $element);
				$v = $elxis->getConfig($x);
			} else if ($element == 'IP') {
				$v = $this->getUserIP();
			} else if ($element == 'AGENT') {
				$v = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
			} else if ($element == 'HTLANGUAGE') {
				$v = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '';
			} else if ($element == 'PATH') {
				$v = ELXIS_PATH;
			} else if ($element == 'REVISION') {
				$v = (string)$elxis->fromVersion('REVISION');
			} else if ($element == 'RELDATE') {
				$v = (string)$elxis->fromVersion('RELDATE');
			} else {
				$v = '';
			}
			if ($v == '') { continue; }

			$elements[] = $element;

			if ($unenc == '') {
				$unenc = $v;
			} else {
				$unenc .= ','.$v;
			}
		}

		if ($custom != '') {
			if (strpos($custom, ',') === false) {//comma is not allowed
				$elements[] = 'CUSTOM_'.$custom;
				$unenc .= ','.$custom;
			}
		}

		$ts = time();
		$elements[] = 'TIME_'.$ts;
		$unenc .= ','.$ts.','.$elxis->getConfig('ENCRYPT_KEY');
		$captchakey = sha1($unenc);

		$db = eFactory::getDB();

		$elem_str = implode(',', $elements);
		$sql = "INSERT INTO ".$db->quoteId('#__captcha')." (".$db->quoteId('cid').", ".$db->quoteId('ckey').", ".$db->quoteId('elements').", ".$db->quoteId('keytime').")"
		."\n VALUES (NULL, :xkey, :xelem, :xtime)";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':xkey', $captchakey, PDO::PARAM_STR);
		$stmt->bindParam(':xelem', $elem_str, PDO::PARAM_STR);
		$stmt->bindParam(':xtime', $ts, PDO::PARAM_INT);
		$stmt->execute();

		//5% probability to delete past generated keys (half hour old or more)
		if (rand(1, 20) == 10) {
			$ts2 = $ts - 1800;
			$sql = "DELETE FROM ".$db->quoteId('#__captcha')." WHERE ".$db->quoteId('keytime')." < :xkt";
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':xkt', $ts2, PDO::PARAM_INT);
			$stmt->execute();
		}

		return $captchakey;
	}


	/**********************************************/
	/* VALIDATE SUBMITTED CAPTCHA (NO ROBOT/MATH) */
	/**********************************************/
	public function validate($method, $math_sesname, $math_postname, $robot_keyname, $robot_customname='') {
		if ($method == 'NONE') { return true; }
		if ($method == 'MATH') {
			$ok = $this->validateMath($math_sesname, $math_postname);
			if (!$ok) {
				$this->errormsg = eFactory::getLang()->get('INVALIDSECCODE');
			}
			return $ok;
		}

		//NOROBOT
		if (isset($_POST[$robot_keyname])) {
			$captchakey = trim(filter_input(INPUT_POST, $robot_keyname, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		} else {
			$captchakey = '';
		}
		if ($captchakey == '') {
			$this->errormsg = eFactory::getLang()->get('VERIFY_NOROBOT');
			return false;
		}

		$recheck_custom = '';
		if (($robot_customname != '') && (isset($_POST[$robot_customname]))) {
			$recheck_custom = trim(filter_input(INPUT_POST, $robot_customname, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		}

		$ok = $this->validateNoRobot($captchakey, $recheck_custom);
		if (!$ok) {
			$this->errormsg = eFactory::getLang()->get('VERIFY_NOROBOT');
		} else {
			$this->deleteKey($captchakey);
		}

		return $ok;
	}


	/*********************************************/
	/* VALIDATE SUBMITTED CAPTCHA KEY (NO ROBOT) */
	/*********************************************/
	public function validateNoRobot($captchakey, $recheck_custom='') {
		if (trim($captchakey) == '') { return false; }

		$filtered = preg_replace("/[^A-Za-z0-9 ]/", '', $captchakey);
		if ($filtered != $captchakey) { return false; }

		$db = eFactory::getDB();
		$sql = "SELECT ".$db->quoteId('elements')." FROM ".$db->quoteId('#__captcha')." WHERE ".$db->quoteId('ckey')." = :xk";
		$stmt = $db->prepareLimit($sql, 0, 1);
		$stmt->bindParam(':xk', $captchakey, PDO::PARAM_STR);
		$stmt->execute();
		$elements_str = $stmt->fetchResult();
		if (!$elements_str) { return false; }
		if (trim($elements_str) == '') { return false; }

		$elxis = eFactory::getElxis();
		$elements = explode(',', $elements_str);

		$time = 0;
		$unenc = '';
		foreach ($elements as $element) {
			if (strpos($element, 'CONFIG_') === 0) {//Elxis configuration option
				$x = str_replace('CONFIG_', '', $element);
				$v = $elxis->getConfig($x);
			} else if ($element == 'IP') {
				$v = $this->getUserIP();
			} else if ($element == 'AGENT') {
				$v = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
			} else if ($element == 'HTLANGUAGE') {
				$v = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '';
			} else if ($element == 'PATH') {
				$v = ELXIS_PATH;
			} else if ($element == 'REVISION') {
				$v = (string)$elxis->fromVersion('REVISION');
			} else if ($element == 'RELDATE') {
				$v = (string)$elxis->fromVersion('RELDATE');
			} else if (strpos($element, 'TIME_') === 0) {
				$v = intval(str_replace('TIME_', '', $element));
				$time = $v;
			} else if (strpos($element, 'CUSTOM_') === 0) {
				$v = str_replace('CUSTOM_', '', $element);
				if ($recheck_custom != '') {
					if ($v != $recheck_custom) { return false; }
				}
			} else {
				$v = '';
			}

			if ($unenc == '') {
				$unenc = $v;
			} else {
				$unenc .= ','.$v;
			}
		}

		if ($time == 0) { return false; }
		if (time() - $time > 900) { return false; } //maximum lifetime 15 minutes

		$unenc .= ','.$elxis->getConfig('ENCRYPT_KEY');
		$captchakey2 = sha1($unenc);

		return ($captchakey == $captchakey2) ? true : false;
	}


	/*********************************************/
	/* VALIDATE SUBMITTED CAPTCHA KEY (MATH) */
	/*********************************************/
	public function validateMath($session_name, $post_name) {
		if (($session_name == '') || ($post_name == '')) { return false; }

		$eSession = eFactory::getSession();

		$sess_captcha = trim($eSession->get($session_name));
		if (isset($_POST[$post_name])) {
			$seccode = trim(filter_input(INPUT_POST, $post_name, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		} else {
			$seccode = '';
		}

		if (($sess_captcha == '') || ($seccode == '') || ($seccode != $sess_captcha)) {
			return false;
		}
		return true;
	}


	/****************************************************************/
	/* DELETE NOROBOT CAPTCHA FROM DB (AFTER SUCESSFULL VALIDATION) */
	/****************************************************************/
	public function deleteKey($captchakey) {
		$db = eFactory::getDB();
		$sql = "DELETE FROM ".$db->quoteId('#__captcha')." WHERE ".$db->quoteId('ckey')." = :xk";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':xk', $captchakey, PDO::PARAM_STR);
		$stmt->execute();
	}


	/***********************/
	/* GET USER IP ADDRESS */
	/***********************/
	private function getUserIP() {
		if (isset($_SERVER['HTTP_CLIENT_IP']) && ($_SERVER['HTTP_CLIENT_IP'] != '')) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && ($_SERVER['HTTP_X_FORWARDED_FOR'] != '')) {
			$n = strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',');//Required in case we have multiple IPs
			if ($n === false) {
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
				$ip = substr($_SERVER['HTTP_X_FORWARDED_FOR'], 0, $n);//get the first IP in the list
			}
		} elseif (isset($_SERVER['REMOTE_ADDR']) && ($_SERVER['REMOTE_ADDR'] != '')) {
			$ip = $_SERVER['REMOTE_ADDR'];
		} else {
			$ip = '';
		}

		return $ip;
	}

}

?>