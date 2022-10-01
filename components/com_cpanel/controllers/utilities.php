<?php 
/**
* @version		$Id: utilities.php 2443 2022-03-08 18:43:05Z IOS $
* @package		Elxis
* @subpackage	CPanel component
* @copyright	Copyright (c) 2006-2022 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class utilitiesCPController extends cpanelController {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null, $model=null) {
		parent::__construct($view, $model);
	}


	/*****************************/
	/* CHECK FTP SETTINGS (AJAX) */
	/*****************************/
	public function checkftp() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$response = array('success' => 0, 'message' => '');

		$host = trim(filter_input(INPUT_POST, 'fho', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$port = intval(filter_input(INPUT_POST, 'fpo', FILTER_SANITIZE_NUMBER_INT));
		$user = trim(filter_input(INPUT_POST, 'fus', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$pass = trim(filter_input(INPUT_POST, 'fpa', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$root = trim(filter_input(INPUT_POST, 'fro', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));

		if ($host == '') {
			$response['message'] = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('HOST'));
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}
		if ($port < 1) {
			$response['message'] = $eLang->get('PORT').': '.$eLang->get('INVALID_NUMBER');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}
		if ($user == '') {
			$response['message'] = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('USER'));
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}
		if ($pass == '') {
			$pass = $elxis->getConfig('FTP_PASS');
			if ($pass == '') {
				$response['message'] = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('PASSWORD'));
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit;
			}
		}
		if ($root == '') {
			$response['message'] = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('PATH'));
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		elxisLoader::loadFile('includes/libraries/elxis/ftp.class.php');
		$params = array('ftp_host' => $host, 'ftp_port' => $port, 'ftp_user' => $user, 'ftp_pass' => $pass);
		$ftp = new elxisFTP($params);
		if ($ftp->getStatus() != 'connected') {
			$response['message'] = $ftp->getError();
			if ($response['message'] == '') { $response['message'] = 'Could not connect to FTP server!'; }
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$rfiles = $ftp->nlist($root);
		$ftp->disconnect();
		if ($rfiles && is_array($rfiles) && (count($rfiles) > 0)) {
			$ok = 0;
			foreach ($rfiles as $rfile) {
				if (strpos($rfile, 'inner.php') !== false) { $ok++; }
				if (strpos($rfile, 'configuration.php') !== false) { $ok++; }
			}
			if ($ok == 2) {
				$response['success'] = 1;
				$response['message'] = $eLang->get('FTP_CON_SUCCESS').' '.$eLang->get('ELXIS_FOUND_FTP');
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit;
			}
		}

		$response['message'] = $eLang->get('FTP_CON_SUCCESS').' '.$eLang->get('ELXIS_NOT_FOUND_FTP');
		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/**************************/
	/* SEND TEST EMAIL (AJAX) */
	/**************************/
	public function mailtest() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$response = array('success' => 0, 'message' => '');

		$options = array();
		$options['MAIL_METHOD'] = trim(filter_input(INPUT_POST, 'mmeth', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		if ($options['MAIL_METHOD'] == 'gmail') { $options['MAIL_METHOD'] = 'smtp'; }
		$options['MAIL_NAME'] = eUTF::trim(filter_input(INPUT_POST, 'mname', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		$options['MAIL_FROM_NAME'] = eUTF::trim(filter_input(INPUT_POST, 'mfname', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		$options['MAIL_MANAGER_NAME'] = eUTF::trim(filter_input(INPUT_POST, 'mmname', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		$options['MAIL_EMAIL'] = isset($_POST['memail']) ? trim($_POST['memail']) : '';
		$options['MAIL_FROM_EMAIL'] = isset($_POST['mfemail']) ? trim($_POST['mfemail']) : '';
		$options['MAIL_MANAGER_EMAIL'] = isset($_POST['mmemail']) ? trim($_POST['mmemail']) : '';
		$options['MAIL_SMTP_HOST'] = trim(filter_input(INPUT_POST, 'mhost', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$options['MAIL_SMTP_USER'] = trim(filter_input(INPUT_POST, 'muser', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$options['MAIL_SMTP_PASS'] = trim(filter_input(INPUT_POST, 'mpass', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		$options['MAIL_SMTP_SECURE'] = trim(filter_input(INPUT_POST, 'msecure', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$options['MAIL_SMTP_PORT'] = isset($_POST['mport']) ? (int)$_POST['mport'] : 0;
		$options['MAIL_SMTP_AUTH'] = isset($_POST['mauth']) ? (int)$_POST['mauth'] : 0;
		$options['MAIL_AUTH_METHOD'] = trim(filter_input(INPUT_POST, 'mauthmeth', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));

		if (($options['MAIL_METHOD'] == '') || !in_array($options['MAIL_METHOD'], array('mail', 'smtp', 'sendmail'))) {
			$response['message'] = 'Invalid email dispatch method!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}
		if ($options['MAIL_NAME'] == '') {
			$response['message'] = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('RCPT_NAME'));
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}
		if ($options['MAIL_FROM_NAME'] == '') {
			$response['message'] = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('SENDER_NAME'));
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}
		if ($options['MAIL_MANAGER_NAME'] == '') {
			$response['message'] = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('TECHNICAL_MANAGER'));
			exit;
		}
		if (($options['MAIL_EMAIL'] == '') || !filter_var($options['MAIL_EMAIL'], FILTER_VALIDATE_EMAIL)) {
			$response['message'] = sprintf($eLang->get('FIELDNOEMPTY'), 'Recipient Email');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}
		if (($options['MAIL_FROM_EMAIL'] == '') || !filter_var($options['MAIL_FROM_EMAIL'], FILTER_VALIDATE_EMAIL)) {
			$response['message'] = sprintf($eLang->get('FIELDNOEMPTY'), 'Sender Email');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}
		if (($options['MAIL_MANAGER_EMAIL'] == '') || !filter_var($options['MAIL_MANAGER_EMAIL'], FILTER_VALIDATE_EMAIL)) {
			$response['message'] = sprintf($eLang->get('FIELDNOEMPTY'), 'Technical Manager Email');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}
		if ($options['MAIL_SMTP_PORT'] < 1) { $options['MAIL_SMTP_PORT'] = 25; }
		if (!in_array($options['MAIL_SMTP_SECURE'], array('ssl', 'tls', 'starttls'))) { $options['MAIL_SMTP_SECURE'] = ''; }
		if ($options['MAIL_SMTP_PASS'] == '') { $options['MAIL_SMTP_PASS'] = $elxis->getConfig('MAIL_SMTP_PASS'); }

		if ($options['MAIL_METHOD'] == 'smtp') {
			if ($options['MAIL_SMTP_HOST'] == '') {
				$response['message'] = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('HOST'));
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit;
			}
			if ($options['MAIL_SMTP_AUTH'] == 1) {
				if ($options['MAIL_SMTP_USER'] == '') {
					$response['message'] = sprintf($eLang->get('FIELDNOEMPTY'), 'SMTP '.$eLang->get('USERNAME'));
					$this->ajaxHeaders('application/json');
					echo json_encode($response);
					exit;
				}
				if ($options['MAIL_SMTP_PASS'] == '') {
					$response['message'] = sprintf($eLang->get('FIELDNOEMPTY'), 'SMTP '.$eLang->get('PASSWORD'));
					$this->ajaxHeaders('application/json');
					echo json_encode($response);
					exit;
				}
			}
		}

		require_once(ELXIS_PATH.'/includes/libraries/swift/swift_required.php');

		$subject = 'Test message from '.$elxis->getConfig('SITENAME');
		$msg = 'Your email settings are OK!'."\r\n\r\n";
		$msg .= 'Method used to send email: '.$options['MAIL_METHOD']."\r\n";

		if ($options['MAIL_METHOD'] == 'smtp') {
			$msg .= 'SMTP host: '.$options['MAIL_SMTP_HOST']."\r\n";
			$msg .= 'SMTP port: '.$options['MAIL_SMTP_PORT']."\r\n";
			$msg .= 'SMTP security: ';
			$msg .= ($options['MAIL_SMTP_SECURE'] == '') ? 'No' : $options['MAIL_SMTP_SECURE'];
			$msg .= "\r\n";
			if ($options['MAIL_SMTP_AUTH'] == 1) {
				$msg .= 'SMTP authentication as '.$options['MAIL_SMTP_USER'].' was successfull';
				if ($options['MAIL_AUTH_METHOD'] != '') {
					$msg .= "\r\n";
					$msg .= 'SMTP Authentication method: '.$options['MAIL_AUTH_METHOD'];
				}
			}
		} else {
			$msg .= 'Elxis Team recommends using SMTP with user authentication!'."\r\n";
		}
		$msg .= "\r\n\r\n";
		$msg .= "Sent by Elxis CMS\r\n";
		$msg .= 'http://www.elxis.org';

		$message = Swift_Message::newInstance();
		$message->setCharset('UTF-8');
		$message->setPriority(3);
		$message->setSubject($subject);
		$message->setBody($msg, 'text/plain');
		$message->addTo($options['MAIL_EMAIL'], $options['MAIL_NAME']);
		if ($options['MAIL_MANAGER_EMAIL'] != $options['MAIL_EMAIL']) {
			$message->addCc($options['MAIL_MANAGER_EMAIL'], $options['MAIL_MANAGER_NAME']);
		}
		$message->setFrom(array($options['MAIL_FROM_EMAIL'] => $options['MAIL_FROM_NAME']));

		$headers = $message->getHeaders();
		$headers->addTextHeader('X-Mailer', 'Elxis');

		switch ($options['MAIL_METHOD']) {
			case 'smtp':
				$transport = Swift_SmtpTransport::newInstance($options['MAIL_SMTP_HOST'], $options['MAIL_SMTP_PORT'], $options['MAIL_SMTP_SECURE']);
				if ($options['MAIL_SMTP_AUTH'] == 1) {
					if ($options['MAIL_AUTH_METHOD'] != '') {
						$transport->setAuthMode($options['MAIL_AUTH_METHOD']);
					}
					$transport->setUsername($options['MAIL_SMTP_USER']);
					$transport->setPassword($options['MAIL_SMTP_PASS']);
				}
			break;
			case 'sendmail': $transport = Swift_SendmailTransport::newInstance(); break;
			case 'mail': default: $transport = Swift_MailTransport::newInstance(); break;
		}

		$mailer = Swift_Mailer::newInstance($transport);
		try {
			$result = $mailer->send($message);
		} catch (\Swift_TransportException $Ste) {
			$result = 0;
			$response['message'] = $Ste->getMessage();
			if ($response['message'] != '') {
				if (strpos($response['message'], 'Connection could not be established') !== false) {
					$n = strpos($response['message'], '[');//remove the rest of the message because it contains unprintable characters
					if ($n !== false) {
						$response['message'] = substr($response['message'], 0, $n).' (check host settings and username)';
					}
				}
			}
		}

		$totxt = $options['MAIL_EMAIL'];
		if ($options['MAIL_MANAGER_EMAIL'] != $options['MAIL_EMAIL']) { $totxt .= ' and to '.$options['MAIL_MANAGER_EMAIL']; }

		if (!$result) {
			if ($response['message'] == '') {
				$response['message'] = 'Sending email to '.$totxt.' failed!';
			}
		} else {
			$response['success'] = 1;
			$response['message'] = 'Email sent successfully to '.$totxt;
		}

		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/******************************************/
	/* SHOW SYSTEM TIME - NULL REQUEST (AJAX) */
	/******************************************/
	public function heartbeat() {
		$this->ajaxHeaders('text/plain');
		echo eFactory::getDate()->getTS();
		exit;
	}


	/****************************************/
	/* GENERIC AJAX REQUEST - Elxis 4.x/5.x */
	/****************************************/
	public function genericajax() {
		$f = '';
		$format = 'plain';//Elxis 5.x response headers on error. json/plain/none/"empty", defaults to "plain" for Elxis 4.x compatibility.

		if (isset($_POST['format'])) {
			$format = trim(strtolower($_POST['format']));
			if (($format != '') && !in_array($format, array('plain', 'json', 'none'))) { $format = ''; }
		}

		$valid = false;
		if (isset($_POST['f'])) {
			$pat = "#([\']|[\!]|[\(]|[\)]|[\;]|[\"]|[\$]|[\#]|[\<]|[\>]|[\*]|[\%]|[\~]|[\`]|[\^]|[\|]|[\{]|[\}]|[\\\])#u";
			$f = trim(filter_input(INPUT_POST, 'f', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
			$f = preg_replace('@^(\/)@', '', $f);

			$f2 = trim(strip_tags(preg_replace($pat, '', $f)));
			$f2 = str_replace('..', '', $f2);
			$f2 = str_replace('\/\/', '', $f2);

			if (($f != '') && ($f2 == $f)) {
				if (strpos($f, 'modules/') === 0) {
					$pathok = true;
				} else if (strpos($f, 'components/com_content/plugins/') === 0) {
					$pathok = true;
				} else if (strpos($f, 'components/com_user/auth/') === 0) {
					$pathok = true;
				} else if (strpos($f, 'components/com_search/engines/') === 0) {
					$pathok = true;
				} else {
					$pathok = false;
				}

				if ($pathok) {
					if (preg_match('@(\.php)$@', $f)) {
						if (is_file(ELXIS_PATH.'/'.$f) && file_exists(ELXIS_PATH.'/'.$f)) {
							$valid = true;
						}
					}
				}
			}
		}

		if (!$valid) {
			$response = array('success' => 0, 'message' => 'Request dropped by Elxis!');
			if ($format == 'json') {
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit;
			}

			$this->ajaxHeaders('text/plain');
			echo $response['message'];
			exit;
		}

		if ($format == 'plain') {//Elxis 4.x compatibility: display headers even on success
			$this->ajaxHeaders('text/plain');
		}
		include(ELXIS_PATH.'/'.$f);
		exit;
	}


	/*************************************/
	/* NO ROBOT CAPTCHA GENERATOR (AJAX) */
	/*************************************/
	public function captchagenerator() {
		if (isset($_GET['custom'])) {
			$custom = $_GET['custom'];
		} else if (isset($_POST['custom'])) {
			$custom = $_POST['custom'];
		} else {
			$custom = '';
		}

		$response = array('success' => 0, 'errormsg' => '', 'captchakey' => '');

		if ($custom != '') {
			$filtered = trim(preg_replace("/[^A-Za-z0-9 ]/", '', $custom));
			if ($filtered != $custom) {
				$response['errormsg'] = 'Not acce[ptable custom parameter!';
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit;
			}
		}

		$captcha = eFactory::getElxis()->obj('captcha');
		$captchakey = $captcha->generate($custom);

		$response['success'] = 1;
		$response['captchakey'] = $captchakey;

		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/*************************/
	/* BAN IP ADDRESS (AJAX) */
	/*************************/
	public function banip() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eFiles = eFactory::getFiles();

		$response = array('success' => 0, 'message' => '');

		//Elxis 4.x vs 5.x incompatibility: In Elxis 4.x the IP was provided base64 encoded
		$ip = trim(filter_input(INPUT_POST, 'ip', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));

		$this->ajaxHeaders('text/plain');
		if ($elxis->user()->gid <> 1) {
			$response['message'] = $eLang->get('ONLY_ADMINS_ACTION');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		if ($elxis->getConfig('DEFENDER') == '') {
			$response['message'] = $eLang->get('BAN_IP_REQ_DEF');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$myip = eFactory::getSession()->getIP();
		if ($myip == $ip) {
			$response['message'] = $eLang->get('BAN_YOURSELF');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$ok = false;
		$ipv6 = '';
		if ($ip != '') {
			if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
				$ok = true;
				$ipv6 = $ip;
				$ip = $elxis->obj('IP')->ipv6tov4($ipv6);
			} else if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
				$ok = true;
			}
		}

		if (!$ok) {
			$response['message'] = 'Invalid IP address!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$ipstr = str_replace('.', 'x', $ip);
		$ipstr = str_replace(':', 'y', $ipstr);

		$repo_path = $eFiles->elxisPath('', true);
		$file = $repo_path.'logs/defender_ban.php';

		$buffer = '<?php '._LEND._LEND;
		$buffer .= '//Elxis Defender - Banned IPs - Last updated on '.gmdate('Y-m-d H:i:s').' (UTC) by '.$elxis->user()->uname.''._LEND._LEND;
		$buffer .= 'defined(\'_ELXIS_\') or die (\'Protected by Elxis Defender\');'._LEND._LEND;
		$buffer .= '$ban = array('._LEND;
		if (!file_exists($file)) {
			$buffer .= '\''.$ipstr.'\' => array(\'times\' => 10, \'refcode\' => \'SEC-CPBAN-0001\', \'date\' => \''.gmdate('Y-m-d H:i:s').'\'),'._LEND;
		} else {
			include($file);
			$found = false;
			if (isset($ban) && is_array($ban) && (count($ban) > 0)) {
				foreach ($ban as $key => $row) {
					if ($key == $ipstr) {
						if ($row['times'] >= 3) {
							$response['success'] = 1;
							$response['message'] = $eLang->get('IP_AL_BANNED');
							$this->ajaxHeaders('application/json');
							echo json_encode($response);
							exit;
						}
						$found = true;
						$buffer .= '\''.$ipstr.'\' => array(\'times\' => 10, \'refcode\' => \'SEC-CPBAN-0002\', \'date\' => \''.gmdate('Y-m-d H:i:s').'\'),'._LEND;
					} else {
						$buffer .= '\''.$key.'\' => array(\'times\' => '.$row['times'].', \'refcode\' => \''.$row['refcode'].'\', \'date\' => \''.$row['date'].'\'),'._LEND;
					}
				}
			}
			unset($ban);

			if (!$found) {
				$buffer .= '\''.$ipstr.'\' => array(\'times\' => 10, \'refcode\' => \'SEC-CPBAN-0003\', \'date\' => \''.gmdate('Y-m-d H:i:s').'\'),'._LEND;
			}
		}

		$buffer .= ');'._LEND._LEND;
		$buffer .= '?>';

		$ok = $eFiles->createFile('logs/defender_ban.php', $buffer, true, true);
		if ($ok) {
			$this->model->removeSessionIP($ip);
			if ($ipv6 != '') {
				$this->model->removeSessionIP($ipv6);
			}
			$response['message'] = sprintf($eLang->get('IP_BANNED'), $ip);
			$response['success'] = 1;
		} else {
			$response['message'] = $eLang->get('BAN_FAILED_NOWRITE');
		}

		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/***********************/
	/* FORCE LOGOUT (AJAX) */
	/***********************/
	public function forcelogout() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : -1;
		$gid = isset($_POST['gid']) ? (int)$_POST['gid'] : -1;
		$lmethod = trim(filter_input(INPUT_POST, 'lmethod', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		//Elxis 4.x vs 5.x incompatibility: In Elxis 4.x the IP was provided base64 encoded
		$ip = trim(filter_input(INPUT_POST, 'ip', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));

		$response = array('success' => 0, 'message' => '');

		if (($uid < 0) || ($gid < 1) || ($lmethod == '') || ($ip == '')) {
			$response['message'] = 'Invalid request!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		if ($uid > 0) {
			if ($lmethod != 'elxis') {
				$response['message'] = 'Invalid request!';
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit;
			}
			if (($gid == 1) && ($elxis->user()->gid <> 1)) {
				$response['message'] = $eLang->get('CNOT_LOGOUT_ADMIN');
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit;
			}
			$this->model->removeSessionUser($uid);
			$response['success'] = 1;
			$response['message'] = $eLang->get('USER_LOGGED_OUT');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		} else if ($gid == 6) {
			if ($lmethod == 'elxis') {
				$response['message'] = 'Invalid request!';
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit;
			}
			$fact = isset($_POST['fact']) ? (int)$_POST['fact'] : 0;
			if ($fact < 1) {
				$response['message'] = 'Invalid request!';
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit;
			}
			$ok = $this->model->removeSessionXUser($lmethod, $ip, $fact);
			if ($ok) {
				$response['success'] = 1;
				$response['message'] = $eLang->get('USER_LOGGED_OUT');
			} else {
				$response['message'] = 'Action failed!';
			}
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		} else {
			$response['message'] = 'Invalid request!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}
	}


	/***************/
	/* LOGOUT USER */
	/***************/
	public function logout() {
		$elxis = eFactory::getElxis();

		$elxis->logout();
		$return = $elxis->makeURL();
		$elxis->redirect($return);
	}


	/***************************/
	/* PREPARE TO LIST BACKUPS */
	/***************************/
	public function listbackup() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eDoc = eFactory::getDocument();
		$eFiles = eFactory::getFiles();

		if ($elxis->acl()->check('com_cpanel', 'backup', 'edit') < 1) {
			$url = $elxis->makeAURL('cpanel:/');
			$elxis->redirect($url, $eLang->get('NOTALLOWACCPAGE'), true);
		}

		$rows = $this->model->fetchBackups();

		$folders = array();
		$folders[] = 'components/';
		$items = $eFiles->listFolders('components/');
		if ($items) {
			foreach ($items as $item) { $folders[] = 'components/'.$item.'/'; }
		}
		$folders[] = 'includes/';
		$folders[] = 'language/';
		$folders[] = 'media/';
		$folders[] = 'media/audio/';
		$folders[] = 'media/images/';
		$items = $eFiles->listFolders('media/images/');
		if ($items) {
			foreach ($items as $item) { $folders[] = 'media/images/'.$item.'/'; }
		}
		$folders[] = 'media/video/';
		$folders[] = 'modules/';
		$folders[] = 'templates/';
		$folders[] = ELXIS_ADIR.'/';

		$tables = eFactory::getDB()->listTables();

		$eDoc->setTitle($eLang->get('BACKUP').' - '.$eLang->get('ADMINISTRATION'));
		$eDoc->addFontAwesome();
		$eDoc->addScriptLink($elxis->secureBase().'/components/com_cpanel/js/cpanel.js');
		if ($rows) {
			$eDoc->addNativeDocReady('elx5DataTable(\'backupstbl\', true); elx5SortableTable(\'backupstbl\');');
		}

		$this->view->listBackups($rows, $folders, $tables, $elxis, $eLang);
	}


	/********************************/
	/* DELETE BACKUP FILE(S) (AJAX) */
	/********************************/
	public function deletebackup() {
		$elxis = eFactory::getElxis();
		$eFiles = eFactory::getFiles();

		$response = array('success' => 0, 'message' => '');

		if ($elxis->acl()->check('com_cpanel', 'backup', 'edit') < 1) {
			$response['message'] = eFactory::getLang()->get('NOTALLOWACCPAGE');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$elids = isset($_POST['elids']) ? trim($_POST['elids']) : '';//multiple select
		if ($elids != '') {
			$parts = explode(',', $elids);
			foreach ($parts as $part) {
				$f = trim(strip_tags(base64_decode($part)));
				$f = str_replace('/', '', $f);
				$f = str_replace('..', '', $f);
				if (($f != '') && preg_match('/(\.zip)$/i', $f)) {
					$ok = $eFiles->deleteFile('backup/'.$f, true);
					if (!$ok) {
						$response['message'] = ' Could not delete file '.$f;
						break;
					}
				}
			}
		}

		if ($response['message'] == '') { $response['success'] = 1; }
		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/****************************/
	/* TAKE A NEW BACKUP (AJAX) */
	/****************************/
	public function makebackup() {
		$elxis = eFactory::getElxis();

		$response = array('success' => 0, 'message' => '');

		if ($elxis->acl()->check('com_cpanel', 'backup', 'edit') < 1) {
			$response['message'] = eFactory::getLang()->get('NOTALLOWACCPAGE');
			$this->ajaxHeaders('text/plain');
			echo json_encode($response);
			exit;
		}

		$type = isset($_POST['type']) ? $_POST['type'] : 'fs';
		if ($type != 'db') { $type = 'fs'; }
		$item = isset($_POST['item']) ? trim($_POST['item']) : '';

		if (defined('ELXIS_MULTISITE') && (ELXIS_MULTISITE != 1) && ($type == 'fs')) {
			$response['message'] = 'You are not allowed to take filesystem backups from a sub-site!';
			$this->ajaxHeaders('text/plain');
			echo json_encode($response);
			exit;
		}

		if ($type == 'fs') {
			$result = $this->fsBackup($elxis, $item);
		} else {
			$result = $this->dbBackup($elxis, $item);
		}

		if ($result['success'] === true) {
			$response['success'] = 1;
		} else {
			$response['message'] = $result['message'];
		}
		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/************************************/
	/* GENERATE A NEW FILESYSTEM BACKUP */
	/************************************/
	private function fsBackup($elxis, $folder='') {
		$result = array('success' => false, 'message' => 'Backup failed');

		$pat = "#([\']|[\"]|[\$]|[\#]|[\<]|[\>]|[\*]|[\%]|[\~]|[\`]|[\^]|[\|]|[\}]|[\.])#u";
		$newfolder = eUTF::trim(preg_replace($pat, '', $folder));
		$newfolder = ltrim($newfolder, '/');
		if ($newfolder != $folder) {
			$result['message'] = 'Requested folder has invalid name!';
			return $result;
		}

		$folder_name = '';
		if ($folder != '') {
			if (!file_exists(ELXIS_PATH.'/'.$folder) || !is_dir(ELXIS_PATH.'/'.$folder)) {
				$result['message'] = 'Requested folder not found!';
				return $result;
			}
			$source = array(ELXIS_PATH.'/'.$folder);
			$parts = preg_split('@\/@', $folder, -1, PREG_SPLIT_NO_EMPTY);
			$n = count($parts) - 1;
			$folder_name = '_'.str_replace('_', '', $parts[$n]);
			unset($parts);
		} else {
			$source = array(
				ELXIS_PATH.'/components/',
				ELXIS_PATH.'/includes/',
				ELXIS_PATH.'/language/',
				ELXIS_PATH.'/media/',
				ELXIS_PATH.'/modules/',
				ELXIS_PATH.'/templates/',
				ELXIS_PATH.'/'.ELXIS_ADIR.'/',
				ELXIS_PATH.'/index.php',
				ELXIS_PATH.'/inner.php',
				ELXIS_PATH.'/configuration.php'
			);

			if (defined('ELXIS_MULTISITE')) {
				for ($i=1; $i<21; $i++) {
					if (file_exists(ELXIS_PATH.'/config'.$i.'.php')) { $source[] = ELXIS_PATH.'/config'.$i.'.php'; }
				}
			}

			if (file_exists(ELXIS_PATH.'/.htaccess')) { $source[] = ELXIS_PATH.'/.htaccess'; }
			if (file_exists(ELXIS_PATH.'/robots.txt')) { $source[] = ELXIS_PATH.'/robots.txt'; }
			if (file_exists(ELXIS_PATH.'/favicon.ico')) { $source[] = ELXIS_PATH.'/favicon.ico'; }
			if (file_exists(ELXIS_PATH.'/license.txt')) { $source[] = ELXIS_PATH.'/license.txt'; }
		}

		$repo_path = $elxis->getConfig('REPO_PATH');
		if ($repo_path == '') { $repo_path = ELXIS_PATH.'/repository'; }

		$parsed = parse_url($elxis->getConfig('URL'));
		$fname = str_replace('www.', '', $parsed['host']);
		if (isset($parsed['path']) && ($parsed['path'] != '') && ($parsed['path'] != '/')) {
			$fname .= $parsed['path'];
		}

		$fname = str_replace('/', '', $fname);
		$fname = str_replace('-', '', $fname);
		$fname = strtolower(str_replace('.', '', $fname));
		$fname = 'fs_'.$fname.$folder_name.'_'.date('YmdHis').'.zip';

		$zip = $elxis->obj('zip');
		$result['success'] = $zip->zip($repo_path.'/backup/'.$fname, $source);
		if ($result['success'] === true) {
			$size = filesize($repo_path.'/backup/'.$fname);
			$size = round($size / 1048576, 2).' MB';
			$result['message'] = 'Elxis filesystem backup success! File generated '.$fname.', Size: '.$size;
		} else {
			$result['message'] = $zip->getError();
		}
		return $result;
	}


	/**********************************/
	/* GENERATE A NEW DATABASE BACKUP */
	/**********************************/
	private function dbBackup($elxis, $table='') {
		$result = array('success' => false, 'message' => 'Backup failed');

		$pat = "#([\']|[\"]|[\$]|[\#]|[\<]|[\>]|[\*]|[\%]|[\~]|[\`]|[\^]|[\|]|[\}]|[\.]|[\/])#u";
		$newtable = eUTF::trim(preg_replace($pat, '', $table));
		if ($newtable != $table) {
			$result['message'] = 'Requested table has invalid name!';
			return $result;
		}

		$repo_path = $elxis->getConfig('REPO_PATH');
		if ($repo_path == '') { $repo_path = ELXIS_PATH.'/repository'; }

		$table_name = '';
		$params = array();
		if ($table != '') {
			$params['tables'] = array($table);
			$table_name = '_'.str_replace('_', '', $table);
		}

		$fname1 = ($elxis->getConfig('DB_NAME') != '') ? $elxis->getConfig('DB_NAME') : 'elxis';
		$fname1 = str_replace('/', '', $fname1);
		$fname1 = str_replace('-', '', $fname1);
		$fname1 = strtolower(str_replace('.', '', $fname1));
		$fname = 'db_'.$fname1.$table_name.'_'.date('YmdHis').'.zip';

		$archive = $repo_path.'/backup/'.$fname;
	 	$sql = eFactory::getDB()->backup($params);

	 	if ($sql === 0) {
	 		return $result;
		} else if ($sql === -1) {
		 	$result['message'] = 'Not supported database type!';
	 		return $result;
	 	} else if ($sql === -2) {
		 	$result['message'] = 'Invalid or insufficient backup parameters!';
	 		return $result;
 		} else if ($sql === -3) {
		 	$result['message'] = $elxis->getConfig('DB_TYPE').' database adapter faced an unrecoverable error!';
 			return $result;
 		} else {
			$result['success'] = true;
		}

		$sqlname = $fname1.'.sql';
		$data = array($sqlname => $sql);
		$zip = $elxis->obj('zip');
		$result['success'] = $zip->zip($archive, null, $data);
		if ($result['success'] === true) {
			$size = filesize($repo_path.'/backup/'.$fname);
			$size = round($size / 1048576, 2).' MB';
			$result['message'] = 'Elxis database backup success! File generated '.$fname.', Size: '.$size;
		} else {
			$result['message'] = $zip->getError();
		}
		return $result;
	}


	/**************************/
	/* DOWNLOAD A BACKUP FILE */
	/**************************/
	public function downbackup() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		if ($elxis->acl()->check('com_cpanel', 'backup', 'edit') < 1) {
			echo $eLang->get('NOTALLOWACCPAGE');
			exit;
		}

		$f = (isset($_GET['f'])) ? strip_tags(base64_decode($_GET['f'])) : '';
		$f = str_replace('/', '', $f);
		$f = str_replace('..', '', $f);
		if (($f == '') || !preg_match('/(\.zip)$/i', $f)) {
			echo 'Empty or invalid backup file!';
			exit;
		}

		$repo_path = rtrim($elxis->getConfig('REPO_PATH'), '/');
		if ($repo_path == '') { $repo_path = ELXIS_PATH.'/repository'; }
		$filepath = $repo_path.'/backup/'.$f;
		if (!file_exists($filepath)) {
			echo $eLang->get('FILE_NOT_FOUND');
			exit;
		}

		if (ob_get_length() > 0) { ob_end_clean(); }
		header('Content-Description: File Transfer');
		header('Content-Type: application/force-download');
		header('Content-Length: '.filesize($filepath));
		header('Content-Disposition: attachment; filename='.$f);
		$handle = @fopen($filepath, 'rb');
		if ($handle !== false) {
			while (!feof($handle)) {
				echo fread($handle, 1048576);
				ob_flush();
				flush();
			}
			fclose($handle);
		}
		exit;
	}


	/**********************************/
	/* PREPARE TO LIST SYSTEM ROUTING */
	/**********************************/
	public function listroutes() {
		$eLang = eFactory::getLang();
		$eDoc = eFactory::getDocument();
		$elxis = eFactory::getElxis();

		if ($elxis->acl()->check('com_cpanel', 'routes', 'manage') < 1) {
			$url = $elxis->makeAURL('cpanel:/');
			$elxis->redirect($url, $eLang->get('NOTALLOWACCPAGE'), true);
		}

		$rows = $this->model->fetchRoutes($elxis, $eLang);

		$components = $this->model->getComponents(false);

		eFactory::getPathway()->addNode($eLang->get('ROUTING'));
		$eDoc->setTitle($eLang->get('ELXIS_ROUTER'));
		$eDoc->addScriptLink($elxis->secureBase().'/components/com_cpanel/js/cpanel.js');
		if ($rows) {
			$eDoc->addNativeDocReady('elx5DataTable(\'routestbl\', false); elx5SortableTable(\'routestbl\');');
		}

		$this->view->listRoutes($rows, $components, $elxis, $eLang);
	}


	/*********************/
	/* SAVE ROUTE (AJAX) */
	/*********************/
	public function saveroute() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$response = array('success' => 0, 'message' => '');

		if ($elxis->acl()->check('com_cpanel', 'routes', 'manage') < 1) {
			$response['message'] = $eLang->get('NOTALLOWACCPAGE');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$isnew = (isset($_POST['isnew'])) ? (int)$_POST['isnew'] : 0;
		$rtype = filter_input(INPUT_POST, 'rtype', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		$rroute = trim(filter_input(INPUT_POST, 'rroute', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));

		if ($rtype == 'frontpage') {
			$response['message'] = $eLang->get('SET_FRONT_CONF');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		if ($isnew == 1) {
			$action = 'add';
			$rbase = trim(filter_input(INPUT_POST, 'rbase', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
			$rbase = trim($rbase, '/');
			if ($rbase == '') {
				$response['message'] = 'Source can not be empty!';
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit;
			}
			if (($rtype == '') || (($rtype != 'page') && ($rtype != 'dir'))) {
				$response['message'] = 'Type is invalid!';
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit;
			}
		} else {
			$action = 'edit';
			$rbase = trim(filter_input(INPUT_POST, 'rbase', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		}

		if (($rtype == 'dir') || ($rtype == 'page')) {
			$ok = $this->updateRoutesFile($rtype, $rbase, $rroute, $action);
			if ($ok) {
				$response['success'] = 1;
			} else {
				$response['message'] = 'Could not update other/routes.php file in Elxis Repository!';
			}
		} else if ($rtype == 'component') {
			$ok = $this->model->setComponentRoute($rbase, $rroute);
			if ($ok) {
				$response['success'] = 1;
			} else {
				$response['message'] = 'Could not update database! Make sure component exists and routes are unique.';
			}
		} else {
			$response['message'] = 'Invalid request';
		}

		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/***********************/
	/* DELETE ROUTE (AJAX) */
	/***********************/
	public function deleteroute() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$response = array('success' => 0, 'message' => '');

		if ($elxis->acl()->check('com_cpanel', 'routes', 'manage') < 1) {
			$response['message'] = $eLang->get('NOTALLOWACCPAGE');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$rtype = filter_input(INPUT_POST, 'rtype', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		$rbase = base64_decode(filter_input(INPUT_POST, 'rbase', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$rbase = trim($rbase, '/');

		if ($rbase == '') {
			$response['message'] = 'Source can not be empty!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		if (($rtype == '') || (($rtype != 'page') && ($rtype != 'dir'))) {
			$response['message'] = 'Type is invalid!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$ok = $this->updateRoutesFile($rtype, $rbase, '', 'delete');
		if ($ok) {
			$response['success'] = 1;
		} else {
			$response['message'] = 'Could not update other/routes.php file in Elxis Repository!';
		}

		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/**********************/
	/* UPDATE ROUTES FILE */
	/**********************/
	private function updateRoutesFile($type, $base, $route, $action) {
		$elxis = eFactory::getElxis();
		$repo_path = $elxis->getConfig('REPO_PATH');
		if ($repo_path == '') { $repo_path = ELXIS_PATH.'/repository'; }

		$buffer = '<?php '._LEND;
		$buffer .= '/*'._LEND;
		$buffer .= 'Elxis Routes - Copyright (c) 2006-'.date('Y').' elxis.org'._LEND;
		$buffer .= 'Last update on '.date('Y-m-d H:i:s')._LEND;
		$buffer .= '*/'._LEND;
		$buffer .= _LEND._LEND;
		$buffer .= 'defined(\'_ELXIS_\') or die (\'Direct access to this location is not allowed\');'._LEND._LEND;

		if (file_exists($repo_path.'/other/routes.php')) { include($repo_path.'/other/routes.php'); }

		if (!isset($routes) || !is_array($routes)) { $routes = array(); }
		if ($type == 'dir') {
			if ($action == 'delete') {
				if (isset($routes[$base])) { unset($routes[$base]); }
			} else {
				$routes[$base] = $route;
			}
		}

		$n = count($routes);
		$buffer .= '$routes = array('._LEND;
		if ($n > 0) {
			$i = 1;
			foreach ($routes as $k => $v) {
				$buffer .= ($i < $n) ? "\t'".$k."' => '".$v."',"._LEND : "\t'".$k."' => '".$v."'"._LEND;
				$i++;
			}
		}
		$buffer .= ');'._LEND._LEND;

		if (!isset($page_routes) || !is_array($page_routes)) { $page_routes = array(); }
		if ($type == 'page') {
			if ($action == 'delete') {
				if (isset($page_routes[$base])) { unset($page_routes[$base]); }
			} else {
				$page_routes[$base] = $route;
			}
		}

		$n = count($page_routes);
		$buffer .= '$page_routes = array('._LEND;
		if ($n > 0) {
			$i = 1;
			foreach ($page_routes as $k => $v) {
				$buffer .= ($i < $n) ? "\t'".$k."' => '".$v."',"._LEND : "\t'".$k."' => '".$v."'"._LEND;
				$i++;
			}
		}
		$buffer .= ');'._LEND._LEND;
		$buffer .= '?>';

		$ok = eFactory::getFiles()->createFile('other/routes.php', $buffer, true, true);
		return $ok;
	}


	/*******************************/
	/* PREPARE TO LIST SYSTEM LOGS */
	/*******************************/
	public function listlogs() {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$eDoc = eFactory::getDocument();

		if ($elxis->acl()->check('com_cpanel', 'logs', 'manage') < 1) {
			$url = $elxis->makeAURL('cpanel:/');
			$elxis->redirect($url, $eLang->get('NOTALLOWACCPAGE'), true);
		}

		$options = array('limit' => 20, 'page' => 1, 'maxpage' => 1, 'sn' => 'lastmodified', 'so' => 'desc', 'limitstart' => 0, 'total' => 0, 'type' => '');

		$options['limit'] = (isset($_GET['limit'])) ? (int)$_GET['limit'] : 20;
		if ($options['limit'] < 1) { $options['limit'] = 20; }
		$elxis->updateCookie('limit', $options['limit']);
		$options['page'] = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
		if ($options['page'] < 1) { $options['page'] = 1; }
		$options['sn'] = (isset($_GET['sn'])) ? trim($_GET['sn']) : 'lastmodified';
		if ($options['sn'] == '') { $options['sn'] = 'lastmodified'; }
		if (!in_array($options['sn'], array('filename', 'type', 'logperiod', 'lastmodified', 'size'))) { $options['sn'] = 'lastmodified'; }
		$options['so'] = (isset($_GET['so'])) ? trim($_GET['so']) : 'desc';
		if ($options['so'] != 'asc') { $options['so'] = 'desc'; }
		$options['type'] = trim(filter_input(INPUT_GET, 'type', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		if ($options['type'] != '') {
			if (!in_array($options['type'], array('notice', 'warning', 'error', 'security', 'notfound', 'other'))) { $options['type'] = ''; }
		}

		$rows = $this->model->fetchLogs($options, $eLang);
		$options['total'] = count($rows);

		if ($options['total'] > 1) {
			$options['maxpage'] = ceil($options['total']/$options['limit']);
			if ($options['maxpage'] < 1) { $options['maxpage'] = 1; }
			if ($options['page'] > $options['maxpage']) { $options['page'] = $options['maxpage']; }
			$options['limitstart'] = (($options['page'] - 1) * $options['limit']);
			if ($options['total'] > $options['limit']) {
				$limitrows = array();
				$end = $options['limitstart'] + $options['limit'];
				foreach ($rows as $k => $row) {
					if ($k < $options['limitstart']) { $k++; continue; }
					if ($k >= $end) { break; }
					$limitrows[] = $row;
				}
				$rows = $limitrows;
				unset($limitrows);
			}
		}

		eFactory::getPathway()->addNode($eLang->get('LOGS'));
		$eDoc->setTitle($eLang->get('LOGS'));

		$eDoc->addScriptLink($elxis->secureBase().'/components/com_cpanel/js/cpanel.js');
		if ($rows) {
			$eDoc->addNativeDocReady('elx5DataTable(\'logstbl\', true);');
		}

		$this->view->listLogs($rows, $options, $elxis, $eLang);
	}


	/****************************/
	/* PREPARE TO VIEW LOG FILE */
	/****************************/
	public function viewlog() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eFiles = eFactory::getFiles();

		if ($elxis->acl()->check('com_cpanel', 'logs', 'manage') < 1) {
			echo '<div class="elx5_pad"><div class="elx5_error">'.$eLang->get('NOTALLOWACCPAGE')."</div></div>\n";
			return;
		}

		$repo_path = $elxis->getConfig('REPO_PATH');
		if ($repo_path == '') { $repo_path = ELXIS_PATH.'/repository'; }
		$fname = trim(filter_input(INPUT_GET, 'fname', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$fname = base64_decode($fname);
		if (($fname == '') || !file_exists($repo_path.'/logs/'.$fname)) {
			echo '<div class="elx5_pad"><div class="elx5_error">'.$eLang->get('FILE_NOT_FOUND')."</div></div>\n";
			return;
		}

		$extension = $eFiles->getExtension($fname);
		$ts = filemtime($repo_path.'/logs/'.$fname);
		$moddate = eFactory::getDate()->formatTS($ts, $eLang->get('DATE_FORMAT_5'));

		echo '<div class="elx5_pad"><div class="elx5_info">'."\n";
		echo $eLang->get('FILENAME').': <strong>'.$fname."</strong><br />\n";
		echo $eLang->get('LAST_MODIFIED').': <strong>'.$moddate."</strong>\n";
		echo "</div></div>\n";

		if ($extension == 'log') {
			echo '<pre dir="ltr">'."\n";
			echo file_get_contents($repo_path.'/logs/'.$fname);
			echo "</pre>\n";
		} else if (($fname == 'defender_notify.txt') || ($fname == 'lastnotify.txt')) {
			echo '<p><em>The contents of this file is of no importance</em></p>'."\n";
		} else if ($fname == 'defender_ban.php') {
			include($repo_path.'/logs/'.$fname);
			if (isset($ban) && is_array($ban) && (count($ban) > 0)) {
				$this->view->listBanned($ban, $eLang);
			} else {
				$this->view->listBanned(array(), $eLang);
			}
		} else {
			echo '<div class="elx5_pad"><div class="elx5_error">Preview of this file is not supported or not allowed.'."</div></div>\n";
		}
	}


	/********************/
	/* CLEAR A LOG FILE */
	/********************/
	public function clearlog($is_delete=false) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eFiles = eFactory::getFiles();

		$response = array('success' => 0, 'message' => '');

		if ($elxis->acl()->check('com_cpanel', 'logs', 'manage') < 1) {
			$response['message'] = $eLang->get('NOTALLOWACCPAGE');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$repo_path = $elxis->getConfig('REPO_PATH');
		if ($repo_path == '') { $repo_path = ELXIS_PATH.'/repository'; }

		$files = array();
		$elids = isset($_POST['elids']) ? trim($_POST['elids']) : '';//multiple select
		$specialmsg = '';
		if ($elids != '') {
			$pat = "#([\']|[\"]|[\$]|[\#]|[\<]|[\>]|[\*]|[\%]|[\~]|[\`]|[\^]|[\|]|[\}]|[\/])#u";
			$parts = explode(',', $elids);
			foreach ($parts as $part) {
				$f = base64_decode($part);
				$f = preg_replace($pat, '', $f);
				$f = trim(str_replace('..', '', $f));
				if ($f == 'installer.log') {
					$specialmsg = 'Clear/Delete of installer.log is not allowed!';
					continue;
				}
				if (($f != '') && file_exists($repo_path.'/logs/'.$f)) { $files[] = $f; }
			}
		}

		if (!$files) {
			$response['message'] = ($specialmsg != '') ? $specialmsg : 'No file(s) requested or file(s) not found!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		if ($is_delete) {
			foreach ($files as $file) {
				$extension = $eFiles->getExtension($file);
				if ($extension != 'log') {
					$response['message'] = addslashes($eLang->get('FILE_CNOT_DELETE')).' ('.$file.')';
					$this->ajaxHeaders('application/json');
					echo json_encode($response);
					exit;
				}
				$eFiles->deleteFile('logs/'.$file, true);
			}

			$response['success'] = 1;
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		//clear/empty file(s)
		foreach ($files as $file) {
			$extension = $eFiles->getExtension($file);
			if ($file == 'defender_ban.php') {
				$data = '<?php '."\n";
				$data .= '//Elxis Defender - Banned IPs - Created on '.gmdate('Y-m-d H:i:s')." (UTC)\n\n";
				$data .= 'defined(\'_ELXIS_\') or die (\'Protected by Elxis Defender\');'."\n\n";
				$data .= '$ban = array();'."\n\n";
				$data .= '?>';
				$ok = $eFiles->createFile('logs/'.$file, $data, true, true);
			} else if (($file == 'defender_ips.php') || ($file == 'defender_ip_ranges.php')) {
				$data = '<?php '."\n";
				$data .= '//Elxis Defender - Blocked IPs - Created on '.gmdate('Y-m-d H:i:s')." (UTC)\n\n";
				$data .= '$ips = array();'."\n\n";
				$data .= '?>';
				$ok = $eFiles->createFile('logs/'.$file, $data, true, true);
			} else if ($extension == 'log') {
				$eFiles->createFile('logs/'.$file, null, true, true);
			}
		}

		$response['success'] = 1;
		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}

 
	/*********************/
	/* DELETE A LOG FILE */
	/*********************/
	public function deletelog() {
		$this->clearlog(true);
	}


	/*********************/
	/* DOWNLOAD LOG FILE */
	/*********************/
	public function downloadlog() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eFiles = eFactory::getFiles();

		if ($elxis->acl()->check('com_cpanel', 'logs', 'manage') < 1) {
			echo '<div class="elx5_pad"><div class="elx5_error">'.$eLang->get('NOTALLOWACCPAGE')."</div></div>\n";
			return;
		}

		$repo_path = $elxis->getConfig('REPO_PATH');
		if ($repo_path == '') { $repo_path = ELXIS_PATH.'/repository'; }
		$fname = trim(filter_input(INPUT_GET, 'fname', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$fname = base64_decode($fname);
		if (($fname == '') || !file_exists($repo_path.'/logs/'.$fname)) {
			echo '<div class="elx5_pad"><div class="elx5_error">'.$eLang->get('FILE_NOT_FOUND')."</div></div>\n";
			return;
		}

		$extension = $eFiles->getExtension($fname);
		if ($extension != 'log') {
			echo '<div class="elx5_pad"><div class="elx5_error">'.$eLang->get('ONLY_LOG_DOWNLOAD')."</div></div>\n";
			return;
		}

		$filepath = $repo_path.'/logs/'.$fname;
		if (ob_get_length() > 0) { ob_end_clean(); }
		header('Content-Description: File Transfer');
		header('Content-Type: application/force-download');
		header('Content-Length: '.filesize($filepath));
		header('Content-Disposition: attachment; filename='.$fname);
		$handle = @fopen($filepath, 'rb');
		if ($handle !== false) {
			while (!feof($handle)) {
				echo fread($handle, 1048576);
				ob_flush();
				flush();
			}
			fclose($handle);
		}
		exit;
	}


	/********************************/
	/* PREPARE TO LIST CACHED ITEMS */
	/********************************/
	public function listcache() {
		$eLang = eFactory::getLang();
		$eDoc = eFactory::getDocument();
		$elxis = eFactory::getElxis();

		if ($elxis->acl()->check('com_cpanel', 'cache', 'manage') < 1) {
			$url = $elxis->makeAURL('cpanel:/');
			$elxis->redirect($url, $eLang->get('NOTALLOWACCPAGE'), true);
		}

		$options = array('limit' => 20, 'page' => 1, 'maxpage' => 1, 'sn' => 'dt', 'so' => 'desc', 'limitstart' => 0, 'total' => 0);
		$options['limit'] = (isset($_GET['limit'])) ? (int)$_GET['limit'] : 20;
		if ($options['limit'] < 1) { $options['limit'] = 20; }
		$elxis->updateCookie('limit', $options['limit']);
		$options['page'] = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
		if ($options['page'] < 1) { $options['page'] = 1; }
		$options['sn'] = (isset($_GET['sn'])) ? trim($_GET['sn']) : 'dt';
		if ($options['sn'] == '') { $options['sn'] = 'dt'; }
		if (!in_array($options['sn'], array('item', 'dt', 'size'))) { $options['sn'] = 'dt'; }
		$options['so'] = (isset($_GET['so'])) ? trim($_GET['so']) : 'asc';
		if ($options['so'] != 'desc') { $options['so'] = 'asc'; }

		$rows = array();
		$files = eFactory::getFiles()->listFiles('cache/', '', true, true, true);
		if ($files) {
			$repo_path = rtrim($elxis->getConfig('REPO_PATH'), '/');
			if ($repo_path == '') { $repo_path = ELXIS_PATH.'/repository'; }
			$now = eFactory::getDate()->getTS();
			foreach ($files as $file) {
				$filename = basename($file);
				if ($filename == 'index.html') { continue; }
				if (strpos($filename, '.') === 0) { continue; }

				$rel = str_replace($repo_path.'/cache/', '', $file);
				$dt = $now - filemtime($file);
				$size = filesize($file);
				$rows[] = array('item' => $rel, 'dt' => $dt, 'size' => $size);
			}
		}
		unset($files);

		$options['total'] = count($rows);
		if ($options['total'] > 1) {
			$rows = $this->sortCacheFiles($rows, $options['sn'], $options['so']);
			$options['maxpage'] = ceil($options['total']/$options['limit']);
			if ($options['maxpage'] < 1) { $options['maxpage'] = 1; }
			if ($options['page'] > $options['maxpage']) { $options['page'] = $options['maxpage']; }
			$options['limitstart'] = (($options['page'] - 1) * $options['limit']);
			if ($options['total'] > $options['limit']) {
				$limitrows = array();
				$end = $options['limitstart'] + $options['limit'];
				foreach ($rows as $k => $row) {
					if ($k < $options['limitstart']) { $k++; continue; }
					if ($k >= $end) { break; }
					$limitrows[] = $row;
				}
				$rows = $limitrows;
				unset($limitrows);
			}
		}

		if ($rows) {
			foreach ($rows as $i => $row) {
				$rows[$i]['timediff'] = $this->humanTime($row['dt'], $eLang);
			}
		}

		eFactory::getPathway()->addNode($eLang->get('CACHE'));
		$eDoc->setTitle($eLang->get('CACHE').' - '.$eLang->get('ADMINISTRATION'));

		if ($rows) {
			$eDoc->addNativeDocReady('elx5DataTable(\'cachetbl\', true);');
		}
		$this->view->listCache($rows, $options, $elxis, $eLang);
	}


	/********************/
	/* SORT CACHE FILES */
	/********************/
	private function sortCacheFiles($rows, $sortname, $sortorder) {
		$sortmethod = '';
		if ($sortname == 'dt') {
			$sortmethod = ($sortorder == 'asc') ? 'sortCachedtAsc' : 'sortCachedtDesc';
		} else if ($sortname == 'size') {
			$sortmethod = ($sortorder == 'asc') ? 'sortCachesizeAsc' : 'sortCachesizeDesc';
		} else if ($sortname == 'item') {
			$sortmethod = ($sortorder == 'asc') ? 'sortCacheitemAsc' : 'sortCacheitemDesc';
		}

		if ($sortmethod == '') { return $rows; }
		usort($rows, array($this, $sortmethod));

		return $rows;
	}


	public function sortCachedtDesc($a, $b) {
		if ($a['dt'] == $b['dt']) { return 0; }
		return ($a['dt'] < $b['dt'] ? 1 : -1);
	}

	public function sortCachedtAsc($a, $b) {
		if ($a['dt'] == $b['dt']) { return 0; }
		return ($a['dt'] > $b['dt'] ? 1 : -1);
	}

	public function sortCachesizeDesc($a, $b) {
		if ($a['size'] == $b['size']) { return 0; }
		return ($a['size'] < $b['size'] ? 1 : -1);
	}

	public function sortCachesizeAsc($a, $b) {
		if ($a['size'] == $b['size']) { return 0; }
		return ($a['size'] > $b['size'] ? 1 : -1);
	}

	public function sortCacheitemDesc($a, $b) {
		if ($a['item'] == $b['item']) { return 0; }
		return strcasecmp($b['item'], $a['item']);
	}

	public function sortCacheitemAsc($a, $b) {
		if ($a['item'] == $b['item']) { return 0; }
		return strcasecmp($a['item'], $b['item']);
	}


	/*********************************/
	/* HUMAN FRINDLY TIME DIFFERENCE */
	/*********************************/
	private function humanTime($dt, $eLang) {
		if ($dt < 60) { return $dt.' '.$eLang->get('ABR_SECONDS'); }
		if ($dt < 3600) {
			$m = floor($dt / 60);
			$s = $dt - ($m * 60);
			return $m.' '.$eLang->get('ABR_MINUTES').', '.$s.' '.$eLang->get('ABR_SECONDS');
		}

		$d = floor($dt / 86400);
		$rem = $dt - ($d * 86400);
		$h = floor($rem / 3600);
		$rem = $rem - ($h * 3600);
		$m = floor($rem / 60);

		$parts = array();
		if ($d == 1) {
			$parts[] = '1 '.$eLang->get('DAY');
		} else if ($d > 1) {
			$parts[] = $d.' '.$eLang->get('DAYS');
		}

		if ($h == 1) {
			$parts[] = '1 '.$eLang->get('HOUR');
		} else if ($h > 1) {
			$parts[] = $h.' '.$eLang->get('HOURS');
		}

		if ($m > 0) { $parts[] = $m.' '.$eLang->get('ABR_MINUTES'); }
		return implode(', ', $parts);
	}


	/******************************/
	/* DELETE CACHED ITEMS (AJAX) */
	/******************************/
	public function deletecache() {
		$elxis = eFactory::getElxis();
		$eFiles = eFactory::getFiles();

		$response = array('success' => 0, 'message' => '');

		if ($elxis->acl()->check('com_cpanel', 'cache', 'manage') < 1) {
			$response['message'] = eFactory::getLang()->get('NOTALLOWACCPAGE');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$response['success'] = 1;
		$elids = isset($_POST['elids']) ? trim($_POST['elids']) : '';//multiple select
		if ($elids != '') {
			$parts = explode(',', $elids);
			foreach ($parts as $part) {
				$f = trim(strip_tags(base64_decode($part)));
				$f = str_replace('..', '', $f);
				if (($f != '') && ($f != '/')) {
					$ok = $eFiles->deleteFile('cache/'.$f, true);
					if (!$ok) {
						$response['success'] = 0;
						$response['message'] = 'Could not delete file '.$f;
						break;
					}
				}
			}
		}

		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/***********************/
	/* FORCE RUN CRON JOBS */
	/***********************/
	public function runcronjobs() {//Elxis 5.x: do not change errormsg to message as it is used in com_extmanager
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$response = array ('success' => 0, 'errormsg' => '', 'lastrun' => '');

		if ($elxis->getConfig('CRONJOBS') == 0) {
			$response['errormsg'] = $eLang->get('CRON_DISABLED');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$cron = $elxis->obj('cron');
		$ok = $cron->run(true);
		unset($cron);
		if (!$ok) {
			$response['errormsg'] = 'Could not run cron jobs! Maybe file lastcron.txt does not exist in repository logs file.';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$response['success'] = 1;
		$response['lastrun'] = 'Can not determine run time!';
		$path = eFactory::getFiles()->elxisPath('logs/lastcron.txt', true);
		if (file_exists($path)) {
			$lastcronts = filemtime($path);
			if ($lastcronts > 1406894400) { //2014-08-01 12:00:00
				$lastcron = time() - $lastcronts;
				if ($lastcron < 60) {
					$response['lastrun'] = sprintf($eLang->get('SEC_AGO'), $lastcron);
				} else if ($lastcron < 3600) {
					$min = floor($lastcron / 60);
					$sec = $lastcron % 60;
					$response['lastrun'] = sprintf($eLang->get('MIN_SEC_AGO'), $min, $sec);
				} else if ($lastcron < 7200) {
					$min = floor(($lastcron - 3600) / 60);
					$response['lastrun'] = sprintf($eLang->get('HOUR_MIN_AGO'), $min);
				} else if ($lastcron < 172800) {//2 days
					$hours = floor($lastcron / 3600);
					$sec = $lastcron - ($hours * 3600);
					$min = floor($sec / 60);
					$response['lastrun'] = sprintf($eLang->get('HOURS_MIN_AGO'), $hours, $min);
				} else {
					$response['lastrun'] = eFactory::getDate()->formatTS($lastcronts, $eLang->get('DATE_FORMAT_4'));
				}
			} else {
				$response['lastrun'] = $eLang->get('NEVER');
			}
		}
		unset($path);

		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/***********************************************/
	/* PREPARE TO LIST FILES AVAILABLE FOR EDITING */
	/***********************************************/
	public function codeEditorList() {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$eDoc = eFactory::getDocument();
		$eFiles = eFactory::getFiles();

		if ($elxis->acl()->check('com_cpanel', 'settings', 'edit') < 1) {
			$url = $elxis->makeAURL('cpanel:/');
			$elxis->redirect($url, $eLang->get('NOTALLOWACCPAGE'), true);
		}

		if (defined('ELXIS_MULTISITE') && (ELXIS_MULTISITE != 1)) {
			$url = $elxis->makeAURL('cpanel:/');
			$elxis->redirect($url, 'You cannot edit code from sub-sites!', true);
		}

		$curextension = '';
		$ext = trim(filter_input(INPUT_GET, 'ext', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		if ($ext != '') {
			$parts = preg_split('@\_@', $ext, 2, PREG_SPLIT_NO_EMPTY);
			if (count($parts) == 2) {
				switch ($parts[0]) {
					case 'tpl': $curextension = 'Template '.$parts[1]; break;
					case 'mod': $curextension = 'Module '.$parts[1]; break;
					case 'plg': $curextension = 'Plugin '.$parts[1]; break;
					case 'eng': $curextension = 'Search '.$parts[1]; break;
					case 'com': $curextension = 'Component '.$parts[1]; break;//not used
					default: break;
				}
			}
		}

		$rows = array();

		$data = array(
			'type' => 'css',
			'extension' => 'Custom CSS',
			'file' => 'user.config.css',
			'relpath' => 'templates/system/css/user.config.css',
			'id' => base64_encode('templates/system/css/user.config.css'),
			'lastmodified' => 0,
			'size' => 0
		);
		if (file_exists(ELXIS_PATH.'/templates/system/css/user.config.css')) {
			$data['lastmodified'] = filemtime(ELXIS_PATH.'/templates/system/css/user.config.css');
			$data['size'] = filesize(ELXIS_PATH.'/templates/system/css/user.config.css');
		}
		$rows[] = $data;

		$data = array(
			'type' => 'css',
			'extension' => 'Custom CSS (RTL)',
			'file' => 'user.config-rtl.css',
			'relpath' => 'templates/system/css/user.config-rtl.css',
			'id' => base64_encode('templates/system/css/user.config-rtl.css'),
			'lastmodified' => 0,
			'size' => 0
		);
		if (file_exists(ELXIS_PATH.'/templates/system/css/user.config-rtl.css')) {
			$data['lastmodified'] = filemtime(ELXIS_PATH.'/templates/system/css/user.config-rtl.css');
			$data['size'] = filesize(ELXIS_PATH.'/templates/system/css/user.config-rtl.css');
		}
		$rows[] = $data;

		$data = array(
			'type' => 'js',
			'extension' => 'Custom Javascript',
			'file' => 'user.config.js',
			'relpath' => 'templates/system/js/user.config.js',
			'id' => base64_encode('templates/system/js/user.config.js'),
			'lastmodified' => 0,
			'size' => 0
		);
		if (file_exists(ELXIS_PATH.'/templates/system/js/user.config.js')) {
			$data['lastmodified'] = filemtime(ELXIS_PATH.'/templates/system/js/user.config.js');
			$data['size'] = filesize(ELXIS_PATH.'/templates/system/js/user.config.js');
		}
		$rows[] = $data;

		$extensions = array();
		$folders = $eFiles->listFolders('templates/', false, false, false);
		if ($folders) {
			foreach ($folders as $folder) {
				if (($folder == 'system') || ($folder == 'admin')) { continue; }
				$extensions[] = 'Template '.$folder;
				$files = $eFiles->listFiles('templates/'.$folder.'/', '(.css)$', true, true, false);
				if ($files) {
					foreach ($files as $fullpath) {
						$relpath = str_replace(ELXIS_PATH.'/templates/'.$folder.'/', '', $fullpath);
						$parts = explode('/', $relpath);
						$last = count($parts) - 1;
						$rows[] = array(
							'type' => 'css',
							'extension' => 'Template '.$folder,
							'file' => $parts[$last],
							'relpath' => 'templates/'.$folder.'/'.$relpath,
							'id' => base64_encode('templates/'.$folder.'/'.$relpath),
							'lastmodified' => filemtime($fullpath),
							'size' => filesize($fullpath)
						);
					}
				}
				$tfiles = array('index.php', 'inner.php', '403.php', '404.php', 'error.php', 'fatal.php', 'offline.php', 'security.php');
				foreach ($tfiles as $tfile) {
					if (!file_exists(ELXIS_PATH.'/templates/'.$folder.'/'.$tfile)) { continue; }
					$rows[] = array(
						'type' => 'php',
						'extension' => 'Template '.$folder,
						'file' => $tfile,
						'relpath' => 'templates/'.$folder.'/'.$tfile,
						'id' => base64_encode('templates/'.$folder.'/'.$tfile),
						'lastmodified' => filemtime(ELXIS_PATH.'/templates/'.$folder.'/'.$tfile),
						'size' => filesize(ELXIS_PATH.'/templates/'.$folder.'/'.$tfile)
					);
				}
			}
		}
		unset($folders);

		$files = $eFiles->listFiles('modules/', '(.css)$', true, true, false);
		if ($files) {
			$mexts = array();
			foreach ($files as $fullpath) {
				$relpath = str_replace(ELXIS_PATH.'/modules/', '', $fullpath);
				$parts = explode('/', $relpath);
				$last = count($parts) - 1;
				$ext = preg_replace('@^(mod_)@', '', $parts[0]);
				$mexts[] = $ext;
				$rows[] = array(
					'type' => 'css',
					'extension' => 'Module '.$ext,
					'file' => $parts[$last],
					'relpath' => 'modules/'.$relpath,
					'id' => base64_encode('modules/'.$relpath),
					'lastmodified' => filemtime($fullpath),
					'size' => filesize($fullpath)
				);
			}

			$exts = array_unique($mexts);
			foreach ($exts as $ext) { $extensions[] = 'Module '.$ext; }
		}

		$files = $eFiles->listFiles('components/com_content/plugins/', '(.css)$', true, true, false);
		if ($files) {
			$mexts = array();
			foreach ($files as $fullpath) {
				$relpath = str_replace(ELXIS_PATH.'/components/com_content/plugins/', '', $fullpath);
				$parts = explode('/', $relpath);
				$last = count($parts) - 1;
				$mexts[] = $parts[0];
				$rows[] = array(
					'type' => 'css',
					'extension' => 'Plugin '.$parts[0],
					'file' => $parts[$last],
					'relpath' => 'components/com_content/plugins/'.$relpath,
					'id' => base64_encode('components/com_content/plugins/'.$relpath),
					'lastmodified' => filemtime($fullpath),
					'size' => filesize($fullpath)
				);
			}
			$exts = array_unique($mexts);
			foreach ($exts as $ext) { $extensions[] = 'Plugin '.$ext; }
		}

		$files = $eFiles->listFiles('components/com_search/engines/', '(.css)$', true, true, false);
		if ($files) {
			$mexts = array();
			foreach ($files as $fullpath) {
				$relpath = str_replace(ELXIS_PATH.'/components/com_search/engines/', '', $fullpath);
				$parts = explode('/', $relpath);
				$mexts[] = $parts[0];
				$last = count($parts) - 1;
				$rows[] = array(
					'type' => 'css',
					'extension' => 'Search '.$parts[0],
					'file' => $parts[$last],
					'relpath' => 'components/com_search/engines/'.$relpath,
					'id' => base64_encode('components/com_search/engines/'.$relpath),
					'lastmodified' => filemtime($fullpath),
					'size' => filesize($fullpath)
				);
			}
			$exts = array_unique($mexts);
			foreach ($exts as $ext) { $extensions[] = 'Search '.$ext; }
		}
		unset($files);

		eFactory::getPathway()->addNode('Code editor');
		$eDoc->setTitle('Code editor');
		$eDoc->addNativeDocReady('elx5DataTable(\'ceditortbl\', false); elx5SortableTable(\'ceditortbl\');');
		$eDoc->addFontAwesome(true);
		$eDoc->addScriptLink($elxis->secureBase().'/components/com_cpanel/js/cpanel.js');

		$this->view->codeEditorListHTML($rows, $extensions, $curextension, $elxis, $eLang);
	}


	/************************/
	/* PREPARE TO EDIT CODE */
	/************************/
	public function editCode() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eDoc = eFactory::getDocument();
		$pathway = eFactory::getPathway();

		if ($elxis->acl()->check('com_cpanel', 'settings', 'edit') < 1) {
			$url = $elxis->makeAURL('cpanel:/');
			$elxis->redirect($url, $eLang->get('NOTALLOWACCPAGE'), true);
		}

		if (defined('ELXIS_MULTISITE') && (ELXIS_MULTISITE != 1)) {
			$url = $elxis->makeAURL('cpanel:/');
			$elxis->redirect($url, 'You cannot edit code from sub-sites!', true);
		}

		$f = trim(filter_input(INPUT_GET, 'f', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		if ($f == '') {
			$url = $elxis->makeAURL('cpanel:codeeditor/');
			$elxis->redirect($url);
		}

		$relpath = strip_tags(base64_decode($f));
		$relpath = trim(str_replace('..', '', $relpath));
		if (strlen($relpath) < 12) {
			$url = $elxis->makeAURL('cpanel:codeeditor/');
			$elxis->redirect($url);
		}

		$filedata = $this->getFileData($relpath);
		if ($filedata['message'] != '') {
			$url = $elxis->makeAURL('cpanel:codeeditor/');
			$elxis->redirect($url, $filedata['message'], true);
		}

		$contents = '';
		if (file_exists(ELXIS_PATH.'/'.$filedata['relpath'])) { $contents = file_get_contents(ELXIS_PATH.'/'.$filedata['relpath']); }
		if ($contents == '') {
			if (($filedata['relpath'] == 'templates/system/css/user.config.css') || ($filedata['relpath'] == 'templates/system/css/user.config-rtl.css')) {
				$dt = eFactory::getDate()->formatDate('now', '%Y-%m-%d %H:%M:%S');
				$contents = '/* Custom CSS rules - Created by '.$elxis->user()->uname.' on '.$dt.' */';
				$contents .= "\n";
			}
			if ($filedata['relpath'] == 'templates/system/js/user.config.js') {
				$dt = eFactory::getDate()->formatDate('now', '%Y-%m-%d %H:%M:%S');
				$contents = '/* Custom JavaScript code - Created by '.$elxis->user()->uname.' on '.$dt.' */';
				$contents .= "\n";
			}
		}

		if ($filedata['type'] == 'js') {
			$editortype = 'javascript';
		} else if ($filedata['type'] == 'html') {
			$editortype = 'htmlmixed';
		} else {
			$editortype = $filedata['type'];
		} 

		$pathway->addNode('Code editor', 'cpanel:codeeditor/');
		$pathway->addNode($eLang->get('EDIT_CODE'));
		$eDoc->setTitle($eLang->get('EDIT_CODE'));

		$eDoc->addStyleLink($elxis->secureBase().'/includes/js/codemirror/codemirror.css');
		$eDoc->addStyleLink($elxis->secureBase().'/components/com_cpanel/css/cp'.$eLang->getinfo('RTLSFX').'.css');//after codemirror.css
		$eDoc->addScriptLink($elxis->secureBase().'/includes/js/codemirror/codemirror.js');

		$eDoc->addScriptLink($elxis->secureBase().'/includes/js/codemirror/htmlmixed.js');

		if ($filedata['type'] == 'php') {
			$eDoc->addScriptLink($elxis->secureBase().'/includes/js/codemirror/xml.js');
			$eDoc->addScriptLink($elxis->secureBase().'/includes/js/codemirror/javascript.js');
			$eDoc->addScriptLink($elxis->secureBase().'/includes/js/codemirror/css.js');
			$eDoc->addScriptLink($elxis->secureBase().'/includes/js/codemirror/htmlmixed.js');
			$eDoc->addScriptLink($elxis->secureBase().'/includes/js/codemirror/clike.js');
			$eDoc->addScriptLink($elxis->secureBase().'/includes/js/codemirror/php.js');
		} else if ($filedata['type'] == 'js') {
			$eDoc->addScriptLink($elxis->secureBase().'/includes/js/codemirror/javascript.js');
		} else if ($filedata['type'] == 'css') {
			$eDoc->addScriptLink($elxis->secureBase().'/includes/js/codemirror/css.js');
		} else if ($filedata['type'] == 'html') {
			$eDoc->addScriptLink($elxis->secureBase().'/includes/js/codemirror/xml.js');
			$eDoc->addScriptLink($elxis->secureBase().'/includes/js/codemirror/javascript.js');
			$eDoc->addScriptLink($elxis->secureBase().'/includes/js/codemirror/css.js');
			$eDoc->addScriptLink($elxis->secureBase().'/includes/js/codemirror/htmlmixed.js');
		} else {
			$eDoc->addScriptLink($elxis->secureBase().'/includes/js/codemirror/'.$editortype.'.js');
		}

		$eDoc->setMetaTag('Cache-Control', 'no-cache, no-store, must-revalidate', true);
		$eDoc->setMetaTag('Pragma', 'no-cache', true);
		$eDoc->setMetaTag('Expires', '-1', true);

		$toolbar = $elxis->obj('toolbar');
		$toolbar->add($eLang->get('SAVE'), 'save', false, '', 'elx5Submit(\'save\', \'fmedcode\', \'ecotask\');');
		$toolbar->add($eLang->get('APPLY'), 'saveedit', false, '', 'elx5Submit(\'apply\', \'fmedcode\', \'ecotask\');');
		$toolbar->add($eLang->get('CANCEL'), 'cancel', false, $elxis->makeAURL('cpanel:codeeditor/'));

		$this->view->editCodeHTML($filedata, $contents, $editortype, $elxis, $eLang);
	}


	/********************/
	/* SAVE EDITED CODE */
	/********************/
	public function saveCode() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eSession = eFactory::getSession();
		$eFiles = eFactory::getFiles();

		if ($elxis->acl()->check('com_cpanel', 'settings', 'edit') < 1) {
			$url = $elxis->makeAURL('cpanel:/');
			$elxis->redirect($url, $eLang->get('NOTALLOWACCPAGE'), true);
		}

		if (defined('ELXIS_MULTISITE') && (ELXIS_MULTISITE != 1)) {
			$url = $elxis->makeAURL('cpanel:/');
			$elxis->redirect($url, 'You cannot edit code from sub-sites!', true);
		}

		$sess_token = trim($eSession->get('token_codeeditor'));
		$token = trim(filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		if (($token == '') || ($sess_token == '') || ($sess_token != $token)) {
			exitPage::make('403', 'CCPA-0014', $eLang->get('REQDROPPEDSEC'));
		}

		$id = trim(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		if ($id == '') {
			$url = $elxis->makeAURL('cpanel:codeeditor/');
			$elxis->redirect($url);
		}

		$relpath = strip_tags(base64_decode($id));
		$relpath = trim(str_replace('..', '', $relpath));
		if (strlen($relpath) < 12) {
			$url = $elxis->makeAURL('cpanel:codeeditor/');
			$elxis->redirect($url);
		}

		$filedata = $this->getFileData($relpath);
		if ($filedata['message'] != '') {
			$url = $elxis->makeAURL('cpanel:codeeditor/');
			$elxis->redirect($url, $filedata['message'], true);
		}

		$contents = filter_input(INPUT_POST, 'contents', FILTER_UNSAFE_RAW);
		$eFiles->createFile($filedata['relpath'], $contents, false, true);

		$eSession->set('token_codeeditor');

		$task = isset($_POST['task']) ? trim($_POST['task']) : 'save';
		if ($task == 'apply') {
			$redirurl = $elxis->makeAURL('cpanel:codeeditor/edit.html?f='.$filedata['id']);
		} else {
			$redirurl = $elxis->makeAURL('cpanel:codeeditor/');
		}
		$elxis->redirect($redirurl);
	}


	/***********************************/
	/* GET FILE'S DATA FOR CODE EDITOR */
	/***********************************/
	private function getFileData($relpath) {
		if ($relpath == 'templates/system/css/user.config.css') {
			$filedata = array(
				'message' => '',
				'type' => 'css',
				'extension' => 'Custom CSS',
				'file' => 'user.config.css',
				'relpath' => 'templates/system/css/user.config.css',
				'id' => base64_encode('templates/system/css/user.config.css'),
				'lastmodified' => 0,
				'size' => 0
			);
			if (file_exists(ELXIS_PATH.'/templates/system/css/user.config.css')) {
				$filedata['lastmodified'] = filemtime(ELXIS_PATH.'/templates/system/css/user.config.css');
				$filedata['size'] = filesize(ELXIS_PATH.'/templates/system/css/user.config.css');
			}
			return $filedata;			
		}

		if ($relpath == 'templates/system/css/user.config-rtl.css') {
			$filedata = array(
				'message' => '',
				'type' => 'css',
				'extension' => 'Custom CSS (RTL)',
				'file' => 'user.config-rtl.css',
				'relpath' => 'templates/system/css/user.config-rtl.css',
				'id' => base64_encode('templates/system/css/user.config-rtl.css'),
				'lastmodified' => 0,
				'size' => 0
			);
			if (file_exists(ELXIS_PATH.'/templates/system/css/user.config-rtl.css')) {
				$filedata['lastmodified'] = filemtime(ELXIS_PATH.'/templates/system/css/user.config-rtl.css');
				$filedata['size'] = filesize(ELXIS_PATH.'/templates/system/css/user.config-rtl.css');
			}
			return $filedata;			
		}

		if ($relpath == 'templates/system/js/user.config.js') {
			$filedata = array(
				'message' => '',
				'type' => 'js',
				'extension' => 'Custom CSS',
				'file' => 'user.config.js',
				'relpath' => 'templates/system/js/user.config.js',
				'id' => base64_encode('templates/system/js/user.config.js'),
				'lastmodified' => 0,
				'size' => 0
			);
			if (file_exists(ELXIS_PATH.'/templates/system/js/user.config.js')) {
				$filedata['lastmodified'] = filemtime(ELXIS_PATH.'/templates/system/js/user.config.js');
				$filedata['size'] = filesize(ELXIS_PATH.'/templates/system/js/user.config.js');
			}
			return $filedata;			
		}

		$filedata = array(
			'message' => '', 'type' => '', 'extension' => '', 'file' => '',
			'relpath' => '', 'id' => '', 'lastmodified' => 0, 'size' => 0
		);

		if (!file_exists(ELXIS_PATH.'/'.$relpath) || is_dir(ELXIS_PATH.'/'.$relpath)) {
			$filedata['message'] = 'File not found!';
			return $filedata;
		}

		if (strpos($relpath, 'modules/') === 0) {
			$parts = explode('/', $relpath);
			$last = count($parts) - 1;
			$ext = '';
			foreach ($parts as $part) {
				if (strpos($part, 'mod_') === 0) {
					$ext = preg_replace('@^(mod_)@', '', $part);
					break;
				}
			}
			if (preg_match('@(.css)$@', $parts[$last])) {
				$filedata['type'] = 'css';
				$filedata['extension'] = 'Module '.$ext;
				$filedata['file'] = $parts[$last];
				$filedata['relpath'] = $relpath;
				$filedata['id'] = base64_encode($relpath);
				$filedata['lastmodified'] = filemtime(ELXIS_PATH.'/'.$relpath);
				$filedata['size'] = filesize(ELXIS_PATH.'/'.$relpath);
			} else {
				$filedata['message'] = 'You cannot edit this file!';
			}
			return $filedata;
		}

		if (strpos($relpath, 'components/com_content/plugins/') === 0) {
			$parts = explode('/', $relpath);
			$last = count($parts) - 1;
			$str = str_replace('components/com_content/plugins/', '', $relpath);
			$parts2 = explode('/', $str);
			$ext = $parts2[0];
			if (preg_match('@(.css)$@', $parts[$last])) {
				$filedata['type'] = 'css';
				$filedata['extension'] = 'Plugin '.$ext;
				$filedata['file'] = $parts[$last];
				$filedata['relpath'] = $relpath;
				$filedata['id'] = base64_encode($relpath);
				$filedata['lastmodified'] = filemtime(ELXIS_PATH.'/'.$relpath);
				$filedata['size'] = filesize(ELXIS_PATH.'/'.$relpath);
			} else {
				$filedata['message'] = 'You cannot edit this file!';
			}
			return $filedata;
		}

		if (strpos($relpath, 'components/com_search/engines/') === 0) {
			$parts = explode('/', $relpath);
			$last = count($parts) - 1;
			$str = str_replace('components/com_search/engines/', '', $relpath);
			$parts2 = explode('/', $str);
			$ext = $parts2[0];
			if (preg_match('@(.css)$@', $parts[$last])) {
				$filedata['type'] = 'css';
				$filedata['extension'] = 'Search '.$ext;
				$filedata['file'] = $parts[$last];
				$filedata['relpath'] = $relpath;
				$filedata['id'] = base64_encode($relpath);
				$filedata['lastmodified'] = filemtime(ELXIS_PATH.'/'.$relpath);
				$filedata['size'] = filesize(ELXIS_PATH.'/'.$relpath);
			} else {
				$filedata['message'] = 'You cannot edit this file!';
			}
			return $filedata;
		}

		if (strpos($relpath, 'templates/') !== 0) {
			$filedata['message'] = 'You cannot edit this file!';
			return $filedata;
		}
		if (strpos($relpath, 'templates/admin/') === 0) {
			$filedata['message'] = 'You cannot edit administration templates!';
			return $filedata;
		}
		if (strpos($relpath, 'templates/system/') === 0) {
			$filedata['message'] = 'You cannot edit the system template!';
			return $filedata;
		}

		$parts = explode('/', $relpath);
		$last = count($parts) - 1;
		$ext = $parts[1];
		if (preg_match('@(.css)$@', $parts[$last])) {
			$filedata['type'] = 'css';
			$filedata['extension'] = 'Template '.$ext;
			$filedata['file'] = $parts[$last];
			$filedata['relpath'] = $relpath;
			$filedata['id'] = base64_encode($relpath);
			$filedata['lastmodified'] = filemtime(ELXIS_PATH.'/'.$relpath);
			$filedata['size'] = filesize(ELXIS_PATH.'/'.$relpath);
		} else if (preg_match('@(.php)$@', $parts[$last])) {
			$filedata['type'] = 'php';
			$filedata['extension'] = 'Template '.$ext;
			$filedata['file'] = $parts[$last];
			$filedata['relpath'] = $relpath;
			$filedata['id'] = base64_encode($relpath);
			$filedata['lastmodified'] = filemtime(ELXIS_PATH.'/'.$relpath);
			$filedata['size'] = filesize(ELXIS_PATH.'/'.$relpath);
		} else {
			$filedata['message'] = 'You cannot edit this file!';
		}

		return $filedata;
	}

}

?>