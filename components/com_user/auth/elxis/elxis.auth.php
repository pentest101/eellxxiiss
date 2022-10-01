<?php 
/**
* @version		$Id: elxis.auth.php 2392 2021-04-04 18:32:42Z IOS $
* @package		Elxis
* @subpackage	Component User / Authentication
* @copyright	Copyright (c) 2006-2021 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisAuthentication {


	/********************/
	/* MAGIC CONTRUCTOR */
	/********************/
	public function __construct($params) {
	}


	/******************************/
	/* AUTHENTICATE AN ELXIS USER */
	/******************************/
	public function authenticate(&$response, $options=array()) {
		$db = eFactory::getDB();

		$uname = (isset($options['uname'])) ? trim($options['uname']) : '';
		$pword = (isset($options['pword'])) ? trim($options['pword']) : '';
		if ($uname == '') {
			$eLang = eFactory::getLang();
			$response->errormsg = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('USERNAME'));
			return false;
		}
		if ($pword == '') {
			$eLang = eFactory::getLang();
			$response->errormsg = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('PASSWORD'));
			return false;
		}

		if (strlen($pword) < 6) {
			$response->errormsg = eFactory::getLang()->get('PASSTOOSHORT');
			return false;
		}

		//apply elxis uname/pword security policy
		$uname_san = trim(preg_replace('/[^a-z0-9\_\-\.+]/i', '', $uname));
		$pword_san = trim(preg_replace('/[^a-z0-9\_\-\.\!\@\#\$\&\(\)\{\}\[\]\?\<\>+]/i', '', $pword));
		if ($uname !== $uname_san) {
			$eLang = eFactory::getLang();
			$response->errormsg = sprintf($eLang->get('FIELDNOACCCHAR'), $eLang->get('USERNAME'));
			return false;
		}
		if ($pword !== $pword_san) {
			$eLang = eFactory::getLang();
			$response->errormsg = sprintf($eLang->get('FIELDNOACCCHAR'), $eLang->get('PASSWORD'));
			return false;
		}

		$sql = "SELECT ".$db->quoteId('uid').", ".$db->quoteId('firstname').", ".$db->quoteId('lastname').","
		."\n ".$db->quoteId('pword').", ".$db->quoteId('block').", ".$db->quoteId('gid').", ".$db->quoteId('email').","
		."\n ".$db->quoteId('expiredate')." FROM ".$db->quoteId('#__users')." WHERE ".$db->quoteId('uname')." = :username";
		$stmt = $db->prepareLimit($sql, 0, 1);
		$stmt->execute(array(':username' => $uname));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if (!$row) {
			$eLang = eFactory::getLang();
			//$response->errormsg = $uname.' - '.eFactory::getLang()->get('USERNOTFOUND');
			$response->errormsg = $eLang->get('USERNOTFOUND').' / '.eFactory::getLang()->get('INVALIDPASS');
			return false;
		}
		$encpass = eFactory::getElxis()->obj('crypt')->getEncryptedPassword($pword);
		if ($encpass != $row['pword']) {
			//$response->errormsg = eFactory::getLang()->get('INVALIDPASS');
			$response->errormsg = eFactory::getLang()->get('USERNOTFOUND').' / '.eFactory::getLang()->get('INVALIDPASS');
			return false;
		}
		if (intval($row['block']) == 1) {
			$response->errormsg = eFactory::getLang()->get('YACCBLOCKED');
			return false;
		}
		$expiredate = trim($row['expiredate']);
		if (($expiredate != '') && ($expiredate < eFactory::getDate()->getDate())) {
			$uid = (int)$row['uid'];
			$stmt = $db->prepare("UPDATE ".$db->quoteId('#__users')." SET ".$db->quoteId('block')." = 1 WHERE ".$db->quoteId('uid')." = :userid");
			$stmt->bindParam(':userid', $uid, PDO::PARAM_INT);
			$stmt->execute();
			$response->errormsg = eFactory::getLang()->get('YACCEXPIRED');
			return false;
		}

		$response->firstname = $row['firstname'];
		$response->lastname = $row['lastname'];
		$response->email = $row['email'];
		$response->uid = (int)$row['uid'];
		$response->uname = $uname;
		$response->gid = (int)$row['gid'];
		return true;
	}


	/**********************/
	/* DISPLAY LOGIN FORM */
	/**********************/
	public function loginForm() {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$eDoc = eFactory::getDocument();

		elxisLoader::loadFile('includes/libraries/elxis/form5.class.php');

		$action = $elxis->makeURL('user:login/elxis.html', '', true, false);
		$return = base64_encode($elxis->makeURL('user:/'));

		$form = new elxis5Form(array('idprefix' => 'ulog', 'labelclass' => 'elx5_labelblock', 'sideclass' => 'elx5_zero'));
		$form->openForm(array('name' => 'fmuserlogin', 'method' => 'post', 'action' => $action, 'id' => 'fmuserlogin'));
		$form->openFieldset($eLang->get('LOGIN'));
		$form->addNote($eLang->get('LOGINOWNACC'), 'elx5_sminfo elx5_dspace');
		$form->addText('uname', '', $eLang->get('USERNAME'), array('required' => 'required', 'dir' => 'ltr', 'autocomplete' => 'off'));
		$form->addPassword('pword', '', $eLang->get('PASSWORD'), array('required' => 'required', 'maxlength' => 60, 'autocomplete' => 'off'));
		$options = array();
		$options[] = $form->makeOption(1, $eLang->get('REMEMBER_ME'));
		$form->addCheckbox('remember', '', null, $options);
		$form->addHidden('return', $return);
		$form->addHidden('auth_method', 'elxis');
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