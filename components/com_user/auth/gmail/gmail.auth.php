<?php 
/**
* @version		$Id: gmail.auth.php 2290 2019-05-17 16:43:02Z IOS $
* @package		Elxis
* @subpackage	Component User / Authentication
* @copyright	Copyright (c) 2006-2019 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class gmailAuthentication {


	/********************/
	/* MAGIC CONTRUCTOR */
	/********************/
	public function __construct($params) {
	}


	/*****************************/
	/* AUTHENTICATE A GMAIL USER */
	/*****************************/
	public function authenticate(&$response, $options=array()) {
		$uname = (isset($options['uname'])) ? trim($options['uname']) : '';
		$pword = (isset($options['pword'])) ? trim($options['pword']) : '';
		if ($uname == '') {
			$eLang = eFactory::getLang();
			$response->errormsg = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('EMAIL'));
			return false;
		}
		if ($pword == '') {
			$eLang = eFactory::getLang();
			$response->errormsg = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('PASSWORD'));
			return false;
		}
		if (strlen($pword) < 4) {
			$response->errormsg = eFactory::getLang()->get('PASSTOOSHORT');
			return false;
		}

		if (!function_exists('curl_init')) {
			$response->errormsg = 'CURL is not supported by the web server!';
			return false;
		}

		//remove ascii chars 0-31
		$uname_san = eUTF::trim(filter_var($uname, FILTER_SANITIZE_EMAIL));
		$pword_san = eUTF::trim(filter_var($pword, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		if ($uname !== $uname_san) {
			$eLang = eFactory::getLang();
			$response->errormsg = sprintf($eLang->get('FIELDNOACCCHAR'), $eLang->get('EMAIL'));
			return false;
		}
		if ($pword !== $pword_san) {
			$eLang = eFactory::getLang();
			$response->errormsg = sprintf($eLang->get('FIELDNOACCCHAR'), $eLang->get('PASSWORD'));
			return false;
		}

		$curl = curl_init('https://mail.google.com/mail/feed/atom');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curl, CURLOPT_USERPWD, $uname.':'.$pword);
		$result = @curl_exec($curl);
		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$result = false;
		switch($code) {
			case 200: $result = true; break;
			case 401: $response->errormsg = eFactory::getLang()->get('AUTHFAILED'); break;
			default: $response->errormsg = eFactory::getLang()->get('AUTHFAILED'); break;
		}

		if ($result == true) {
			$parts = preg_split('/\@/', $uname, 2, PREG_SPLIT_NO_EMPTY);
			$response->uname = $parts[0];
			$response->email = $uname;
		}

		return $result;
	}


	/*************************/
	/* SHOW GMAIL LOGIN FORM */
	/*************************/
	public function loginForm() {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$eDoc = eFactory::getDocument();
		$eAuth = eRegistry::get('eAuth');

		$action = $elxis->makeURL('user:login/gmail.html', '', true, false);
		$return = base64_encode($elxis->makeURL('user:/'));
		elxisLoader::loadFile('includes/libraries/elxis/form5.class.php');

		$data = $eAuth->getAuthData('gmail');
		$title = sprintf($eLang->get('LOGIN_WITH'), $data['title']);
		$eDoc->setTitle($eLang->get('LOGIN').' - '.$data['title']);
		$eDoc->setDescription($title);

		$form = new elxis5Form(array('idprefix' => 'ulog', 'labelclass' => 'elx5_labelblock', 'sideclass' => 'elx5_zero'));
		$form->openForm(array('name' => 'fmuserlogin', 'method' => 'post', 'action' => $action, 'id' => 'fmuserlogin'));
		$form->openFieldset($title);
		$form->addEmail('uname', '', $eLang->get('EMAIL'), array('required' => 'required', 'autocomplete' => 'off'));
		$form->addPassword('pword', '', $eLang->get('PASSWORD'), array('required' => 'required', 'maxlength' => 60, 'autocomplete' => 'off'));
		$form->addHidden('return', $return);
		$form->addHidden('auth_method', 'gmail');
		$form->addToken('fmuserlogin');
		$form->addHTML('<div class="elx5_dspace">');
		$form->addButton('sbmlog', $eLang->get('LOGIN'), 'submit');
		$form->addHTML('</div>');
		$form->closeFieldset();
		$form->closeForm();
		unset($form);
	}


	/***********************/
	/* EXECUTE CUSTOM TASK */
	/***********************/
	public function runTask($etask) {
		if (ob_get_length() > 0) { @ob_end_clean(); }
		header('content-type:text/plain; charset=utf-8');
		echo 'Invalid request';
		exit;
	}


	/***************************************/
	/* CUSTOM ACTIONS TO PERFORM ON LOGOUT */
	/***************************************/
	public function logout() {
	}

}

?>