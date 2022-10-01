<?php 
/**
* @version		$Id: account.php 2377 2020-12-16 19:01:24Z IOS $
* @package		Elxis
* @subpackage	User component
* @copyright	Copyright (c) 2006-2021 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class accountUserController extends userController {


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null, $task='', $model=null) {
		parent::__construct($view, $task, $model);
	}


	/****************************************/
	/* PREPRE TO DISPLAY USERS CENTRAL PAGE */
	/****************************************/
	public function userscentral() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eDoc = eFactory::getDocument();

		$params = $this->base_getParams();

		$avatar = '';
		if ($elxis->acl()->getLevel() > 0) {
			$gravatar = (int)$params->get('gravatar', 0);
			$avatar = $elxis->obj('avatar')->getAvatar($elxis->user()->avatar, 200, $gravatar, $elxis->user()->email);
			unset($gravatar);
		}

		$bookmarks = 0;
		$messages_total = 0;
		$messages_unread = 0;
		$uid = $elxis->user()->uid;
		if ($uid > 0) {
			$bookmarks = $this->model->countBookmarks($uid);
			$msgObj = $elxis->obj('messages');
			$mparams = array('toid' => $uid, 'read' => 0, 'delbyto' => 0);
			$messages_unread = $msgObj->countMessages($mparams);
			$messages_total = $msgObj->getTotalMessages($elxis->user()->uid);
			unset($mparams, $msgObj);
		}

		$eDoc->setTitle($eLang->get('USERSCENTRAL').' - '.$elxis->getConfig('SITENAME'));
		$eDoc->setDescription($eLang->get('USERSCENTRAL'));
		$eDoc->setKeywords(array($eLang->get('USER'), $eLang->get('RECOVERPASS'), $eLang->get('REGISTRATION'), $eLang->get('LOGIN'), $eLang->get('LOGOUT')));
		$eDoc->setMetaTag('robots', 'noindex, nofollow');
		$eDoc->addFontAwesome();
		$eDoc->addStyleLink($elxis->secureBase().'/components/com_user/inc/user'.$eLang->getinfo('RTLSFX').'.css');	

		$this->view->usersCentral($avatar, $bookmarks, $messages_unread, $messages_total, $params);
	}


	/*******************************************************/
	/* PREPARE TO REGISTER OR TO DISPLAY REGISTRATION FORM */
	/*******************************************************/
	public function register() {
		$this->base_forceSSL('user:register.html');

		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eDoc = eFactory::getDocument();

		$eDoc->setTitle($eLang->get('REGISTRATION').' - '.$elxis->getConfig('SITENAME'));
		$eDoc->setDescription($eLang->get('CRNEWUSERACC'));
		$eDoc->setKeywords(array($eLang->get('REGISTRATION'), $eLang->get('USER'), $eLang->get('USERNAME')));
		$eDoc->setMetaTag('robots', 'noindex, nofollow');
		$eDoc->addFontAwesome();
		$eDoc->addStyleLink($elxis->secureBase().'/components/com_user/inc/user'.$eLang->getinfo('RTLSFX').'.css');	

		if ($elxis->getConfig('REGISTRATION') !== 1) {
			$eDoc->setTitle($eLang->get('REGISTRATION').' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen($eLang->get('USERREGDISABLED'), $eLang->get('ERROR'));
			return;
		}

		if ($elxis->user()->gid != 7) {
			$eDoc->setTitle($eLang->get('REGISTRATION').' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen($eLang->get('ALREADYLOGIN'));
			return;
		}

		$extra_fields = array();
		$terms_txt = '';

		$params = $this->base_getParams();
		$cols = array('phone', 'mobile', 'address', 'postalcode', 'city', 'country');
		foreach ($cols as $col) {
			$pname = 'reg_'.$col;
			$v = (int)$params->get($pname, 0);
			if ($v == 1) { $extra_fields[] = $col; }
		}
		unset($cols);

		$v = (int)$params->get('reg_terms', 0);
		if ($v == 1) {
			$arr = array();
			for ($i=1; $i < 6; $i++) {
				$lngidx = 'reg_termslang'.$i;
				$txtidx = 'reg_termstext'.$i;
				$l = trim($params->get($lngidx, ''));
				$t = trim($params->get($txtidx, ''));
				if (($l != '') && ($t != '')) {
					if (file_exists(ELXIS_PATH.'/'.$t)) {
						$txt = strip_tags(file_get_contents(ELXIS_PATH.'/'.$t));
						if ($txt != '') { $arr[$l] = $txt; }
					}
				}
			}
			if ($arr) {
				$lng = $eLang->currentLang();
				$deflang = $elxis->getConfig('LANG');
				if (isset($arr[$lng])) {
					$terms_txt = $arr[$lng];
				} else if (isset($arr[$deflang])) {
					$terms_txt = $arr[$deflang];
				}
			}
			unset($arr);
		}

		$errormsg = '';
		$ok = false;
		$row = new usersDbTable();
		if (isset($_POST['firstname'])) {
			$row->gid = 5;
			$row->firstname = filter_input(INPUT_POST, 'firstname', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
			$row->lastname = filter_input(INPUT_POST, 'lastname', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
			$row->email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
			$uname = trim($_POST['uname']);
			$row->uname = filter_input(INPUT_POST, 'uname', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
			$row->uname = preg_replace('/[^A-Z\-\_0-9]/i', '', $row->uname);
			$pword = trim($_POST['pword']);
			$row->pword = trim(filter_input(INPUT_POST, 'pword', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
			$pword2 = trim(filter_input(INPUT_POST, 'pword2', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
			$pstr = preg_replace('/[^A-Z\-\_0-9\!\@]/i', '', $row->pword);

			$eSession = eFactory::getSession();
			$sess_token = trim($eSession->get('token_fmregister'));
			$token = trim(filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));

			$captcha_errormsg = '';
			$captcha = $elxis->obj('captcha');
			$captcha_ok = $captcha->validate($elxis->getConfig('CAPTCHA'), 'captcha_seccode', 'seccode', 'norobot', '');
			if (!$captcha_ok) {
				$captcha_errormsg = $captcha->getError(); 
				if ($captcha_errormsg == '') { $captcha_errormsg = 'Captcha validation failed!'; }
			}
			unset($captcha);

			if (($token == '') || ($sess_token == '') || ($sess_token != $token)) {
				$errormsg = $eLang->get('REQDROPPEDSEC');
			} else if ($captcha_errormsg != '') {
				$errormsg = $captcha_errormsg;
			} else if (($row->uname == '') || ($row->uname != $uname)) {
				$errormsg = $eLang->get('INVALIDUNAME');
			} else if (($row->pword == '') || ($row->pword != $pword)) {
				$errormsg = $eLang->get('INVALIDPASS');
			} else if ($row->pword != $pword2) {
				$errormsg = $eLang->get('PASSNOMATCH');
			} else if ($pstr != $row->pword) {
				$errormsg = sprintf($eLang->get('FIELDNOACCCHAR'), $eLang->get('PASSWORD'));
			} else {
				if ($extra_fields) {
					if (in_array('address', $extra_fields)) {
						$row->address = strip_tags(filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
						if (trim($row->address) == '') {
							$errormsg = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('ADDRESS'));
						}
					}
					if (in_array('postalcode', $extra_fields)) {
						$row->postalcode = strip_tags(filter_input(INPUT_POST, 'postalcode', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
						if (trim($row->postalcode) == '') {
							$errormsg = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('POSTAL_CODE'));
						}
					}
					if (in_array('city', $extra_fields)) {
						$row->city = strip_tags(filter_input(INPUT_POST, 'city', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
						if (trim($row->city) == '') {
							$errormsg = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('CITY'));
						}
					}
					if (in_array('phone', $extra_fields)) {
						$row->phone = strip_tags(filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
						if (trim($row->phone) == '') {
							$errormsg = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('TELEPHONE'));
						}
					}
					if (in_array('mobile', $extra_fields)) {
						$row->mobile = strip_tags(filter_input(INPUT_POST, 'mobile', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
						if (trim($row->mobile) == '') {
							$errormsg = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('MOBILE'));
						}
					}
					if (in_array('country', $extra_fields)) {
						$row->country = strip_tags(filter_input(INPUT_POST, 'country', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
						if (strlen($row->country) > 3) { $row->country = ''; }
						if (trim($row->country) == '') {
							$errormsg = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('COUNRTY'));
							$extra_ok = false;
						}
					}
				}

				$row->preflang = $eLang->currentLang();

				if ($errormsg == '') {
					if (!$row->fullCheck()) {
						$errormsg = $row->getErrorMsg();
					} else {
						$pwd = $row->pword;
						$row->pword = $elxis->obj('crypt')->getEncryptedPassword($pwd);
						if (!$row->store()) {
							$errormsg = $row->getErrorMsg();
						} else {
							$ok = true;
						}
					}
				}
			}

			$eSession->set('token_fmregister');
		}

		if ($ok === true) {
			$this->mailNewAccount($row);
			if ($elxis->getConfig('REGISTRATION_ACTIVATION') === 0) {
				$link = $elxis->makeURL('user:login/', '', true);
				$msg = sprintf($eLang->get('YOUMAYLOGIN'), '<strong>'.$row->uname.'</strong>')."<br />\n";
				$msg .= '<a href="'.$link.'" title="'.$eLang->get('LOGIN').'">'.$eLang->get('CLICKTOLOGIN')."</a>\n";
			} else if ($elxis->getConfig('REGISTRATION_ACTIVATION') === 2) {
				$msg = $eLang->get('REGINSPBEFLOG');
			} else {
				$msg = $eLang->get('MAILACTLINK');
			}

			$eDoc->setTitle($eLang->get('SUCCESSREG').' - '.$elxis->getConfig('SITENAME'));
			$this->view->registrationSuccess($row, $msg);
		} else {
			$this->view->registrationForm($row, $errormsg, $extra_fields, $terms_txt);
		}
	}


	/*****************************/
	/* ACTIVATE NEW USER ACCOUNT */
	/*****************************/
	public function activate() {
		$c = trim(filter_input(INPUT_GET, 'c', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$this->base_forceSSL('user:activate.html?c='.$c);

		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eDoc = eFactory::getDocument();

		$eDoc->setTitle($eLang->get('ACCOUNTACT').' - '.$elxis->getConfig('SITENAME'));
		$eDoc->setDescription($eLang->get('CRNEWUSERACC').'. '.$eLang->get('ACCOUNTACT'));
		$eDoc->setKeywords(array($eLang->get('ACCOUNTACT'), $eLang->get('REGISTRATION'), $eLang->get('USER'), $eLang->get('USERNAME')));
		$eDoc->setMetaTag('robots', 'noindex, nofollow');
		$eDoc->addFontAwesome();
		$eDoc->addStyleLink($elxis->secureBase().'/components/com_user/inc/user'.$eLang->getinfo('RTLSFX').'.css');	

		if ($elxis->getConfig('REGISTRATION') !== 1) {
			$eDoc->setTitle($eLang->get('ACCOUNTACT').' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen($eLang->get('USERREGDISABLED'), $eLang->get('ACCOUNTACT'));
			return;
		}

		if ($elxis->getConfig('REGISTRATION_ACTIVATION') !== 1) {
			$eDoc->setTitle($eLang->get('ACCOUNTACT').' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen('User account activation is disabled!', $eLang->get('ACCOUNTACT'));
			return;
		}

		if ($elxis->user()->gid != 7) {
			$eDoc->setTitle($eLang->get('ACCOUNTACT').' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen($eLang->get('ALREADYLOGIN'));
			return;
		}
		
		if ($c == '') {
			$eDoc->setTitle($eLang->get('ACCOUNTACT').' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen($eLang->get('INVACTCODE'));
			return;
		}

		$db = eFactory::getDB();
		$stmt = $db->prepareLimit("SELECT ".$db->quoteId('uid').", ".$db->quoteId('uname')." FROM ".$db->quoteId('#__users')." WHERE ".$db->quoteId('block')." = 1 AND ".$db->quoteId('activation')." = :actcode", 0, 1);
		$stmt->bindParam(':actcode', $c, PDO::PARAM_STR);
		$stmt->execute();
		$urow = $stmt->fetch(PDO::FETCH_ASSOC);
		if (!$urow || !is_array($urow) || (count($urow) == 0)) {
			$eDoc->setTitle($eLang->get('ACCOUNTACT').' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen($eLang->get('INVACTCODE'));
			return;
		}

		$nblock = 0;
		$stmt = $db->prepare("UPDATE ".$db->quoteId('#__users')." SET ".$db->quoteId('block')." = :nbl, ".$db->quoteId('activation')." = NULL WHERE ".$db->quoteId('uid')." = :userid");
		$stmt->bindParam(':nbl', $nblock, PDO::PARAM_INT);
		$stmt->bindParam(':userid', $urow['uid'], PDO::PARAM_INT);
		$ok = $stmt->execute();
		if (!$ok) {
			$eDoc->setTitle($eLang->get('ACCOUNTACT').' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen('The system failed to activate your account. Please contact the site administrator.');
			return;
		}

		$stmt = $db->prepareLimit("SELECT ".$db->quoteId('block')." FROM ".$db->quoteId('#__users')." WHERE ".$db->quoteId('uid')." = :userid", 0, 1);
		$stmt->bindParam(':userid', $urow['uid'], PDO::PARAM_INT);
		$stmt->execute();
		$is_blocked = (int)$stmt->fetchResult();
		if ($is_blocked == 1) {
			$eDoc->setTitle($eLang->get('ACCOUNTACT').' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen('The system failed to activate your account. Please contact the site administrator.');
			return;
		}

		$this->view->activationSuccess($urow['uname']);
	}


	/********************************************************/
	/* PREPARE TO DISPLAY/PROCESS PASSWORD RECOVERY REQUEST */
	/********************************************************/
	public function recoverpass() {
		$this->base_forceSSL('user:recover-pwd.html');

		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eDoc = eFactory::getDocument();

		$eDoc->setTitle($eLang->get('RECOVERPASS').' - '.$elxis->getConfig('SITENAME'));
		$eDoc->setDescription($eLang->get('CRPASSACCFORG'));
		$eDoc->setKeywords(array($eLang->get('PASSWORD'), $eLang->get('USER'), $eLang->get('USERNAME')));
		$eDoc->setMetaTag('robots', 'noindex, nofollow');
		$eDoc->addFontAwesome();
		$eDoc->addStyleLink($elxis->secureBase().'/components/com_user/inc/user'.$eLang->getinfo('RTLSFX').'.css');	

		if ($elxis->user()->gid != 7) {
			$eDoc->setTitle($eLang->get('RECOVERPASS').' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen($eLang->get('ALREADYLOGIN'));
			return;
		}

		if ($elxis->getConfig('PASS_RECOVER') !== 1) {
			$eDoc->setTitle($eLang->get('RECOVERPASS').' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen($eLang->get('PASSRECOVNALL'), $eLang->get('RECOVERPASS'));
			return;
		}

		if ($elxis->getConfig('SECURITY_LEVEL') > 1) {
			$eDoc->setTitle($eLang->get('RECOVERPASS').' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen($eLang->get('SECLEVNALLRP'), $eLang->get('RECOVERPASS'));
			return;
		}

		$errormsg = '';
		$row = new stdClass();
		$row->uname = null;
		$row->email = null;
		if (isset($_POST['sbmrec'])) {
			$uname = trim($_POST['uname']);
			$row->uname = filter_input(INPUT_POST, 'uname', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
			$row->uname = preg_replace('/[^A-Z\-\_0-9]/i', '', $row->uname);
			$row->email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

			$eSession = eFactory::getSession();
			$sess_token = trim($eSession->get('token_fmrecover'));
			$token = trim(filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));

			$captcha_errormsg = '';
			$captcha = $elxis->obj('captcha');
			$ok = $captcha->validate($elxis->getConfig('CAPTCHA'), 'captcha_seccode', 'seccode', 'norobot', '');
			if (!$ok) { $captcha_errormsg = $captcha->getError(); }
			unset($captcha);

			if (($token == '') || ($sess_token == '') || ($sess_token != $token)) {
				$errormsg = $eLang->get('REQDROPPEDSEC');
			} else if (($captcha_errormsg != '')) {
				$errormsg = $captcha_errormsg;
			} else if (($row->uname == '') || ($row->uname != $uname)) {
				$errormsg = $eLang->get('INVALIDUNAME');
			} else if (($row->email == '') || !filter_var($row->email, FILTER_VALIDATE_EMAIL)) {
				$errormsg = $eLang->get('INVALIDEMAIL');
			} else {
				$proceed = true;
				if (($elxis->getConfig('REGISTRATION_EMAIL_DOMAIN') != '') || ($elxis->getConfig('REGISTRATION_EXCLUDE_EMAIL_DOMAINS') != '')) {
					$parts = explode('@', $row->email);
					if (!$parts || !is_array($parts) || (count($parts) != 2)) {
						$errormsg = $eLang->get('INVALIDEMAIL');
						$proceed = false;
					}

					if ($proceed) {
						$emaildomain = strtolower($parts[1]);
						if ($elxis->getConfig('REGISTRATION_EMAIL_DOMAIN') != '') {
							if ($emaildomain != $elxis->getConfig('REGISTRATION_EMAIL_DOMAIN')) {
								$errormsg = sprintf($eLang->get('ONLYMAILFROMALLOW'), $elxis->getConfig('REGISTRATION_EMAIL_DOMAIN'));
								$proceed = false;
							}
						}						
					}

					if ($proceed) {
						if ($elxis->getConfig('REGISTRATION_EXCLUDE_EMAIL_DOMAINS') != '') {
							$exdomains = explode(',', $elxis->getConfig('REGISTRATION_EXCLUDE_EMAIL_DOMAINS'));
							if ($exdomains && is_array($exdomains) && (count($exdomains) > 0)) {
								foreach ($exdomains as $exdomain) {
									if ($emaildomain == $exdomain) {
										$errormsg = sprintf($eLang->get('EMAILADDRNOTACC'), $emaildomain);
										$proceed = false;
										break;
									}
								}
							}
						}
					}
					unset($parts);
				}

				if ($proceed) {
					$db = eFactory::getDB();
					$sql = "SELECT ".$db->quoteId('uid').", ".$db->quoteId('firstname').", ".$db->quoteId('lastname').", ";
					$sql .= $db->quoteId('gid').", ".$db->quoteId('block').", ".$db->quoteId('expiredate');
					$sql .= "\n FROM ".$db->quoteId('#__users')." WHERE ".$db->quoteId('uname')." = :username AND ".$db->quoteId('email')." = :mail";
					$stmt = $db->prepareLimit($sql, 0, 1);
					$stmt->bindParam(':username', $row->uname, PDO::PARAM_STR);
					$stmt->bindParam(':mail', $row->email, PDO::PARAM_STR);
					$stmt->execute();
					$result = $stmt->fetch(PDO::FETCH_OBJ);
					if (!$result) {
						$errormsg = $eLang->get('USERNFOUND');
					} else if (intval($result->block) == 1) {
						$errormsg = $eLang->get('YACCBLOCKED');
					} else if ($result->expiredate < gmdate('Y-m-d H:i:s')) {
						$errormsg = $eLang->get('YACCEXPIRED');
					} else if ((intval($result->gid) == 1) && ($elxis->getConfig('SECURITY_LEVEL') > 0)) {
						$errormsg = $eLang->get('SECLEVNALLRP');
					} else {
						$act = '';
						while (strlen($act) < 40) { $act .= mt_rand(0, mt_getrandmax()); }
						$enc_act = sha1($act);
						$reset_activation = 'RESET:'.$enc_act;
						$stmt = $db->prepare("UPDATE ".$db->quoteId('#__users')." SET ".$db->quoteId('activation')." = :resact WHERE ".$db->quoteId('uid')." = :userid");
						$stmt->bindParam(':resact', $reset_activation, PDO::PARAM_STR);
						$stmt->bindParam(':userid', $result->uid, PDO::PARAM_INT);
						if (!$stmt->execute()) {
							$errormsg = 'Could not reset your password!';
						} else {
							$this->mailPassRecover($result->firstname, $result->lastname, $row->email, $enc_act);
							$this->view->recoverSuccess();
							return;
						}
					}
				}
			}
			
			$eSession->set('token_fmrecover');
		}

		$this->view->recoverForm($row, $errormsg);
	}


	/******************************************************/
	/* CHANGE PASSWORD AFTER CLICKING PASSWORD RESET LINK */
	/******************************************************/
	public function changepass() {
		$this->base_forceSSL('user:resetpw.html');

		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eDoc = eFactory::getDocument();

		$eDoc->setTitle($eLang->get('RECOVERPASS').' - '.$elxis->getConfig('SITENAME'));
		$eDoc->setDescription($eLang->get('CRPASSACCFORG'));
		$eDoc->setKeywords(array($eLang->get('PASSWORD'), $eLang->get('USER'), $eLang->get('USERNAME')));
		$eDoc->setMetaTag('robots', 'noindex, nofollow');
		$eDoc->addFontAwesome();
		$eDoc->addStyleLink($elxis->secureBase().'/components/com_user/inc/user'.$eLang->getinfo('RTLSFX').'.css');	

		if ($elxis->user()->gid != 7) {
			$eDoc->setTitle($eLang->get('RECOVERPASS').' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen($eLang->get('ALREADYLOGIN'));
			return;
		}

		if ($elxis->getConfig('PASS_RECOVER') !== 1) {
			$eDoc->setTitle($eLang->get('RECOVERPASS').' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen($eLang->get('PASSRECOVNALL'), $eLang->get('RECOVERPASS'));
			return;
		}

		if ($elxis->getConfig('SECURITY_LEVEL') > 1) {
			$eDoc->setTitle($eLang->get('RECOVERPASS').' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen($eLang->get('SECLEVNALLRP'), $eLang->get('RECOVERPASS'));
			return;
		}

		$r = '';
		if (isset($_GET['r'])) {
			$r = filter_input(INPUT_GET, 'r', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
			$r = trim(preg_replace('/[^A-Z0-9]/i', '', $r));
		}

		if ($r == '') {
			$eDoc->setTitle($eLang->get('RECOVERPASS').' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen('Invalid request', $eLang->get('RECOVERPASS'));
			return;
		}

		$res_act = 'RESET:'.$r;

		$db = eFactory::getDB();
		$sql = "SELECT ".$db->quoteId('uid')." FROM ".$db->quoteId('#__users')
		."\n WHERE ".$db->quoteId('activation')." = :resact AND ".$db->quoteId('block')." = 0";
		$stmt = $db->prepareLimit($sql, 0, 1);
		$stmt->bindParam(':resact', $res_act, PDO::PARAM_STR);
		$stmt->execute();
		$uid = (int)$stmt->fetchResult();
		if (!$uid) {
			$eDoc->setTitle($eLang->get('RECOVERPASS').' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen('Invalid request', $eLang->get('RECOVERPASS'));
			return;
		}

		$newpass = $elxis->makePassword();
		$encpass = $elxis->obj('crypt')->getEncryptedPassword($newpass);

		$sql = "UPDATE ".$db->quoteId('#__users')." SET ".$db->quoteId('pword')." = :pwd, ".$db->quoteId('activation')." = NULL WHERE ".$db->quoteId('uid')." = :userid";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':pwd', $encpass, PDO::PARAM_STR);
		$stmt->bindParam(':userid', $uid, PDO::PARAM_INT);
		if (!$stmt->execute()) {
			$eDoc->setTitle($eLang->get('RECOVERPASS').' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen('Could not change your password', $eLang->get('RECOVERPASS'));
			return;
		}

		$this->view->recoverSuccess($newpass);
	}


	/***************************/
	/* CHANGE CURRENT TIMEZONE */
	/***************************/
	public function changetimezone() {
		$elxis = eFactory::getElxis();
		$eSession = eFactory::getSession();

		$sess_token = trim($eSession->get('token_fmucp'));//Elxis 4.5+
		if ($sess_token == '') { //Elxis 4.4- backwards compatibility for other possible forms other than users central pointing to that page
			$sess_token = trim($eSession->get('token_fmchangetz'));
		}

		$url = $elxis->makeURL('user:/');

		$token = trim(filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		if (($token != '') && ($sess_token != '') && ($sess_token == $token)) {
			$tz = trim(filter_input(INPUT_POST, 'timezone', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
			if ($tz != '') {
				if (eFactory::getDate()->setTimezone($tz) === true) {
					$eSession->set('timezone', $tz);
					$uid = (int)$elxis->user()->uid;
					if ($uid > 0) {
						$db = eFactory::getDB();
						$sql = "UPDATE ".$db->quoteId('#__users')." SET ".$db->quoteId('timezone')." = :tzone WHERE ".$db->quoteId('uid')." = :userid";
						$stmt = $db->prepare($sql);
						$stmt->bindParam(':tzone', $tz, PDO::PARAM_STR);
						$stmt->bindParam(':userid', $uid, PDO::PARAM_INT);
						$stmt->execute();
					}
				}
			}

			if (isset($_POST['redirectto'])) {
				$redirectto = trim(filter_input(INPUT_POST, 'redirectto', FILTER_SANITIZE_URL));
				if (($redirectto != '') && filter_var($redirectto, FILTER_VALIDATE_URL)) { $url = $redirectto; }
			}
		}

		$eSession->set('token_fmucp');
		$elxis->redirect($url);
	}


	/*******************/
	/* CHANGE LANGUAGE */
	/*******************/
	public function changelanguage() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		if (isset($_POST['lang'])) {
			$lang = trim(filter_input(INPUT_POST, 'lang', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		} else if (isset($_GET['lang'])) {
			$lang = trim(filter_input(INPUT_GET, 'lang', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		} else {
			$lang = '';
		}

		$redir_uri = 'user:/';
		if ($lang != '') {
			$publangs = $eLang->getSiteLangs(false);
			if (in_array($lang, $publangs)) { $redir_uri = $lang.':user:/'; }
		}

		$url = $elxis->makeURL($redir_uri);

		if (isset($_POST['redirectto'])) {//only with POST, requires token!
			$eSession = eFactory::getSession();

			$redirectto = trim(filter_input(INPUT_POST, 'redirectto', FILTER_SANITIZE_URL));
			if (($redirectto != '') && filter_var($redirectto, FILTER_VALIDATE_URL)) {
				$sess_token = trim($eSession->get('token_fmucp'));
				$token = trim(filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
				if (($token != '') && ($sess_token != '') && ($sess_token == $token)) {
					$url = $redirectto;
				}
			}
		}

		$elxis->redirect($url);
	}


	/****************************/
	/* USER LOGIN OR LOGIN FORM */
	/****************************/
	public function login($auth_method='') {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eDoc = eFactory::getDocument();

		$elxis_uri = ($auth_method != '') ? 'user:login/'.$auth_method.'.html' : 'user:login/';
		$this->base_forceSSL($elxis_uri);

		$eDoc->setTitle($eLang->get('LOGIN').' - '.$elxis->getConfig('SITENAME'));
		$eDoc->setDescription($eLang->get('LOGINOWNACC'));
		$eDoc->setKeywords(array($eLang->get('LOGIN'), $eLang->get('USER'), $eLang->get('USERNAME')));
		$eDoc->setMetaTag('robots', 'noindex, nofollow');
		$eDoc->addFontAwesome();
		$eDoc->addStyleLink($elxis->secureBase().'/components/com_user/inc/user'.$eLang->getinfo('RTLSFX').'.css');	

		if ($elxis->user()->gid <> 7) {
			if (ELXIS_INNER == 1) {
				$eDoc->setTitle($eLang->get('LOGIN').' - '.$eLang->get('ERROR'));
				$this->view->base_errorScreen($eLang->get('ALREADYLOGIN'));
				return;
			} else {
				$redir_url = $elxis->makeURL('user:/');
				$elxis->redirect($redir_url);
			}
		}

		elxisLoader::loadInit('libraries:elxis:auth.class', 'eAuth', 'elxisAuth');
		$eAuth = eRegistry::get('eAuth');
		if ($eAuth->getError() != '') {
			$eDoc->setTitle($eLang->get('LOGIN').' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen($eAuth->getError());
			return;
		}

		$auths = $eAuth->getAuths();
		if (!$auths) {
			$eDoc->setTitle($eLang->get('LOGIN').' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen('No authentication methods exist! You cannot login.');
			return;
		}

		if ($auth_method == '') {
			if (isset($auths['elxis'])) {
				$auth_method = 'elxis';
			} else {
				foreach ($auths as $auth => $data) {
					$auth_method = $auth;
					break;
				}
			}
		}

		if (!isset($auths[$auth_method])) {
			$msg = sprintf($eLang->get('AUTHMETHNOTEN'), $auth_method);
			$eDoc->setTitle($eLang->get('LOGIN').' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen($msg);
			return;
		}
		
		$eAuth->setAuth($auth_method);

		if (isset($_GET['etask'])) {
			$etask = trim(filter_input(INPUT_GET, 'etask', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		} else if (isset($_POST['etask'])) {
			$etask = trim(filter_input(INPUT_POST, 'etask', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		} else {
			$etask = '';
		}

		if (!isset($_POST['auth_method']) && ($etask == '')) {
			$this->view->loginForm($auth_method, $auths, $eAuth);
			return;
		}

		$required_post = array('elxis', 'gmail', 'ldap');
		if (in_array($auth_method, $required_post)) {
			if (!isset($_POST['auth_method'])) {
				$this->view->loginForm($auth_method, $auths, $eAuth);
				return;
			} else if ($_POST['auth_method'] != $auth_method) {
				$eDoc->setTitle($eLang->get('LOGIN').' - '.$eLang->get('ERROR'));
				$this->view->base_errorScreen('Submitted Authentication method is wrong!');
				return;
			}
		}

		if (($etask != '') && ($etask != 'auth')) {
			$eAuth->runTask($etask);
			return;
		}

		if ($auth_method == 'elxis') {
			if (isset($_POST['modtoken'])) {
				$sess_token = trim(eFactory::getSession()->get('token_loginform'));
				$token = trim(filter_input(INPUT_POST, 'modtoken', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
			} else {
				$sess_token = trim(eFactory::getSession()->get('token_fmuserlogin'));
				$token = trim(filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));				
			}
			if (($token == '') || ($sess_token == '') || ($sess_token != $token)) {
				$eDoc->setTitle($eLang->get('LOGIN').' - '.$eLang->get('ERROR'));
				$this->view->base_errorScreen($eLang->get('REQDROPPEDSEC'));
				return;
			}
		}

		if (isset($_SERVER['HTTP_REFERER'])) {
			$ref = $_SERVER['HTTP_REFERER'];
			if ($ref != '') {
				$siteurl = $elxis->getConfig('URL');
				if (strpos($ref, $siteurl) !== 0) {
					$eDoc->setTitle($eLang->get('LOGIN').' - '.$eLang->get('ERROR'));
					$this->view->base_errorScreen($eLang->get('REQDROPPEDSEC'));
					return;
				}
			}
		}

		$uname = filter_input(INPUT_POST, 'uname', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$pword = filter_input(INPUT_POST, 'pword', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$remember = isset($_POST['remember']) ? (int)$_POST['remember'] : 0;
		if ($remember !== 1) { $remember = 0; }
		if ($auth_method != 'elxis') { $remember = 0; }

		$return = '';
		if (isset($_POST['return']) && (trim($_POST['return']) != '')) {
			$return1 = base64_decode($_POST['return']);
			$return = filter_var($return1, FILTER_SANITIZE_URL);
			if ($return != $return1) {
				$return = '';
			} else {
				if (!filter_var($return, FILTER_VALIDATE_URL)) {
					$return = '';
				} else { //no external redirection!
					$siteurl = $elxis->getConfig('URL');
					if (strpos($return, $siteurl) === false) {
						$siteurlssl = eFactory::getURI()->secureBase(true);
						if (strpos($return, $siteurlssl) === false) { $return = ''; }
					}
				}
			}
		}

		$options = array();
		$options['auth_method'] = $auth_method;
		$options['uname'] = $uname;
		$options['pword'] = $pword;
		$options['remember'] = $remember;
		$options['return'] = $return;

		$elxis->login($options);
		if (ELXIS_INNER == 1) {
			$this->view->closeAfterLogin($return);
		} else {
			$elxis->redirect($return);
		}
	}


	/**********************************/
	/* LOGOUT USER IF HE IS LOGGED-IN */
	/**********************************/
	public function logout() {
		$this->base_forceSSL('user:logout.html');
		$elxis = eFactory::getElxis();
		if ($elxis->user()->gid <> 7) {
			$elxis->logout();
		}

		$return = '';
		if (isset($_POST['return']) && (trim($_POST['return']) != '')) {
			$return1 = base64_decode($_POST['return']);
			$return = filter_var($return1, FILTER_SANITIZE_URL);
			if ($return != $return1) {
				$return = '';
			} else {
				if (!filter_var($return, FILTER_VALIDATE_URL)) {
					$return = '';
				} else { //no external redirection!
					$siteurl = $elxis->getConfig('URL');
					if (strpos($return, $siteurl) === false) {
						$siteurlssl = eFactory::getURI()->secureBase(true);
						if (strpos($return, $siteurlssl) === false) {
							$return = '';
						}
					}
				}
			}
		}

		if ($return == '') { $return = $elxis->makeURL('user:/'); }
		$elxis->redirect($return);
	}


	/*****************************/
	/* INTERNAL ELXIS USER LOGIN */
	/*****************************/
	public function ilogin() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$response = array('success' => 0, 'errormsg' => '', 'uid' => 0, 'gid' => 0, 'uname' => '', 'firstname' => '', 'lastname' => '', 'email' => '', 'avatar' => '');

		if ($elxis->getConfig('SSL') == 2) {
			if (eFactory::getURI()->detectSSL() === false) {
				$response['errormsg'] = 'Login page must be accessed via the secure HTTPS protocol!';
				$this->view->internalResponse($response);
				return;
			}
		}

		if ($elxis->user()->gid <> 7) {
			$response['errormsg'] = $eLang->get('ALREADYLOGIN');
			$this->view->internalResponse($response);
			return;
		}

		elxisLoader::loadInit('libraries:elxis:auth.class', 'eAuth', 'elxisAuth');
		$eAuth = eRegistry::get('eAuth');
		if ($eAuth->getError() != '') {
			$response['errormsg'] = $eAuth->getError();
			$this->view->internalResponse($response);
			return;
		}

		$auths = $eAuth->getAuths();
		if (!isset($auths['elxis'])) {
			$response['errormsg'] = sprintf($eLang->get('AUTHMETHNOTEN'), 'Elxis');
			$this->view->internalResponse($response);
			return;
		}
		$eAuth->setAuth('elxis');

		if (isset($_POST['modtoken'])) {
			$sess_token = trim(eFactory::getSession()->get('token_loginform'));
			$token = trim(filter_input(INPUT_POST, 'modtoken', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		} else {
			$sess_token = trim(eFactory::getSession()->get('token_fmuserlogin'));
			$token = trim(filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));				
		}
		if (($token == '') || ($sess_token == '') || ($sess_token != $token)) {
			$response['errormsg'] = $eLang->get('REQDROPPEDSEC');
			$this->view->internalResponse($response);
			return;
		}

		if (isset($_SERVER['HTTP_REFERER'])) {
			$ref = $_SERVER['HTTP_REFERER'];
			if ($ref != '') {
				$siteurl = $elxis->getConfig('URL');
				if (strpos($ref, $siteurl) !== 0) {
					$response['errormsg'] = $eLang->get('REQDROPPEDSEC');
					$this->view->internalResponse($response);
					return;
				}
			}
		}

		$uname = filter_input(INPUT_POST, 'uname', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$pword = filter_input(INPUT_POST, 'pword', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);

		$options = array();
		$options['auth_method'] = 'elxis';
		$options['uname'] = $uname;
		$options['pword'] = $pword;
		$options['internal'] = true;
		$options['remember'] = 1;
		$options['return'] = '';

		$elxis_response = $elxis->login($options);
		$response['success'] = ($elxis_response['success'] === true) ? 1 : 0;
		$response['errormsg'] = $elxis_response['errormsg'];
		unset($elxis_response, $options);

		if ($response['success'] == 1) {
			$user = $elxis->user();
			$avatarHelper = $elxis->obj('avatar');

			$response['uid'] = (int)$user->uid;
			$response['gid'] = (int)$user->gid;
			$response['uname'] = $user->uname;
			$response['firstname'] = $user->firstname;
			$response['lastname'] = $user->lastname;
			$response['email'] = $user->email;
			$response['avatar'] = $avatarHelper->getAvatar($user->avatar, 40, 1, $user->email);
		}

		$this->view->internalResponse($response);
	}


	/************************/
	/* INTERNAL LOGOUT USER */
	/************************/
	public function ilogout() {
		$elxis = eFactory::getElxis();

		$response = array('success' => 0, 'errormsg' => '');

		if ($elxis->getConfig('SSL') == 2) {
			if (eFactory::getURI()->detectSSL() === false) {
				$response['errormsg'] = 'Logout page must be accessed via the secure HTTPS protocol!';
				$this->view->internalResponse($response);
				return;
			}
		}

		if ($elxis->user()->gid <> 7) {
			$elxis->logout();
		}

		$response['success'] = 1;
		$this->view->internalResponse($response);
	}

}

?>