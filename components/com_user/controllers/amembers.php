<?php 
/**
* @version		$Id$
* @package		Elxis
* @subpackage	Component User
* @copyright	Copyright (c) 2006-2019 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class amembersUserController extends userController {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null, $task='', $model=null) {
		parent::__construct($view, $task, $model);
	}


	/*********************************/
	/* PREPARE TO DISPLAY USERS LIST */
	/*********************************/
	public function listusers() {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$pathway = eFactory::getPathway();
		$eDoc = eFactory::getDocument();

		$options = array('limit' => 20, 'page' => 1, 'maxpage' => 1, 'sn' => 'uname', 'so' => 'asc', 'limitstart' => 0, 'total' => 0);
		$options['uid'] = isset($_GET['uid']) ? (int)$_GET['uid'] : 0;
		$querycols = array('firstname', 'lastname', 'uname', 'email', 'city', 'address', 'phone', 'mobile', 'website');
		foreach ($querycols as $col) {
			$options[$col] = eUTF::trim(filter_input(INPUT_GET, $col, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		}

		$options['limit'] = (isset($_GET['limit'])) ? (int)$_GET['limit'] : 20;
		if ($options['limit'] < 1) { $options['limit'] = 20; }
		$elxis->updateCookie('limit', $options['limit']);

		$options['page'] = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
		if ($options['page'] < 1) { $options['page'] = 1; }
		$options['sn'] = (isset($_GET['sn'])) ? trim($_GET['sn']) : 'uname';
		if ($options['sn'] == '') { $options['sn'] = 'uname'; }
		if (!in_array($options['sn'], array('uid', 'firstname', 'uname', 'block', 'groupname', 'email', 'registerdate', 'lastvisitdate'))) { $options['sn'] = 'uname'; }
		$options['so'] = (isset($_GET['so'])) ? trim($_GET['so']) : 'asc';
		if ($options['so'] != 'desc') { $options['so'] = 'asc'; }

		$options['total'] = $this->model->countUsers($options);

		$rows = array();
		$options['maxpage'] = ceil($options['total']/$options['limit']);
		if ($options['maxpage'] < 1) { $options['maxpage'] = 1; }
		if ($options['page'] > $options['maxpage']) { $options['page'] = $options['maxpage']; }
		$options['limitstart'] = (($options['page'] - 1) * $options['limit']);
		if ($options['total'] > 0) {
			$rows = $this->model->getUsers($options);
		}

		$pathway->deleteAllNodes();
		$pathway->addNode($eLang->get('USERS_MANAGER'), 'user:users/');
		$pathway->addNode($eLang->get('MEMBERSLIST'));

		$eDoc->setTitle($eLang->get('USERS_MANAGER').' - '.$elxis->getConfig('SITENAME'));
		$eDoc->addScriptLink($elxis->secureBase().'/components/com_user/inc/user.js');
		if ($rows) {
			$eDoc->addNativeDocReady('elx5DataTable(\'userstbl\', false);');
		}

		$this->view->listUsers($rows, $options, $elxis, $eLang);
	}


	/************************/
	/* TOGGLE USER'S STATUS */
	/************************/
	public function toggleuser() {
		$eLang = eFactory::getLang();

		$response = array('success' => 0, 'published' => -1, 'icontitle' => '', 'reloadpage' => 0);

		$uid = isset($_POST['elid']) ? (int)$_POST['elid'] : 0;
		$results = $this->model->blockUser($uid, -1); //includes acl checks

		if ($results['success'] === false) {
			$response['icontitle'] = $results['message'];
		} else {
			$response['success'] = 1;
			$response['published'] = ($results['newblocked'] == 1) ? 0 : 1;
			if ($results['newblocked'] == 1) {
				$response['icontitle'] = $eLang->get('INACTIVE').' - '.$eLang->get('CLICK_TOGGLE_STATUS');
			} else {
				$response['icontitle'] = $eLang->get('ACTIVE').' - '.$eLang->get('CLICK_TOGGLE_STATUS');
			}
		}

		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/***************/
	/* DELETE USER */
	/***************/
	public function deleteuser() {
		$response = array('success' => 0, 'message' => '');

		$uid = (isset($_POST['elids'])) ? (int)$_POST['elids'] : 0;
		if ($uid < 1) {
			$response['message'] = 'Invalid user!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$result = $this->model->deleteUser($uid, 'delete'); //includes acl checks
		if ($result['success'] === false) {
			$response['message'] = addslashes($result['message']);
		} else {
			$response['success'] = 1;
		}

		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/****************************/
	/* PREPARE TO ADD/EDIT USER */
	/****************************/
	public function edituser() {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$pathway = eFactory::getPathway();

		$redirurl = $elxis->makeAURL('user:users/');

		$uid = isset($_GET['uid']) ? (int)$_GET['uid'] : 0;
		if ($uid < 0) { $uid = 0; }
		
		if ($uid > 0) {
			$proceed = false;
			$allowed = $elxis->acl()->check('com_user', 'profile', 'edit');
			if (($allowed == 2) || (($allowed == 1) && ($elxis->user()->uid == $uid))) { $proceed = true; }
			if ($proceed === false) {
				$elxis->redirect($redirurl, $eLang->get('NOTALLOWACCPAGE'), true);
			}

			$row = $this->model->getUser($uid);
			if (!$row) {
				$elxis->redirect($redirurl, $eLang->get('USERNFOUND'), true);
			}

			$userLevel = $this->model->getGroupLevel($row->gid);
			if ($elxis->acl()->getLevel() < $userLevel) {
				$elxis->redirect($redirurl, $eLang->get('NALLOW_HIGHER_ACCESS'), true);
			}
		} else {
			$allowed = $elxis->acl()->check('com_user', 'profile', 'edit');
			if ($allowed !== 2) {
				$elxis->redirect($redirurl, $eLang->get('NOTALLOWACCPAGE'), true);
			}
			$row = new usersDbTable();
			$row->uid = 0;
			$row->gid = 5;
			$row->gender = 'male';
			$row->block = 1;
		}

		$info = new stdClass;
		$info->articles = 0;
		$info->comments = 0;
		if ($row->uid > 0) {
			$info->articles = $this->model->counter($row->uid, 'content', false);
			$info->comments = $this->model->counter($row->uid, 'comments', false);
		}

		elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
		$userparams = new elxisParameters($row->params, '', 'custom');		

		$pathway->deleteAllNodes();
		$pathway->addNode($eLang->get('MEMBERSLIST'), '');
		if ($row->uid > 0) {
			$pathway->addNode($eLang->get('EDITPROFILE').' <strong>'.$row->uname.'</strong>');
		} else {
			$pathway->addNode($eLang->get('NEW_USER'));
		}

		$toolbar = $elxis->obj('toolbar');
		$toolbar->add($eLang->get('SAVE'), 'save', false, '', 'elx5Submit(\'save\', \'fmusedit\', \'eprtask\');');
		$toolbar->add($eLang->get('APPLY'), 'saveedit', false, '', 'elx5Submit(\'apply\', \'fmusedit\', \'eprtask\');');
		$toolbar->add($eLang->get('CANCEL'), 'cancel', false, $elxis->makeAURL('user:users/'));

		$this->view->editUser($row, $info, $userparams, $elxis, $eLang);
	}


	/*********************/
	/* SAVE USER PROFILE */
	/*********************/
	public function saveuser() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eSession = eFactory::getSession();
		$eDate = eFactory::getDate();

		$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : 0;
		if ($uid < 0) { $uid = 0; }

		$sess_token = trim($eSession->get('token_fmusedit'));
		$token = trim(filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		if (($token == '') || ($sess_token == '') || ($sess_token != $token)) {
			exitPage::make('403', 'CUSE-0011', $eLang->get('REQDROPPEDSEC'));
		}

		$proceed = false;
		$allowed = $elxis->acl()->check('com_user', 'profile', 'edit');
		if ($uid > 0) {
			if (($allowed == 2) || (($allowed == 1) && ($elxis->user()->uid == $uid))) { $proceed = true; }
		} else {
			if ($allowed == 2) { $proceed = true; }
		}

		$redirurl = $elxis->makeAURL('user:users/');
		if ($proceed === false) {
			$elxis->redirect($redirurl, $eLang->get('NOTALLOWACCPAGE'), true);
		}

		$oldpass = '';
		$oldlastclicks = '';
		$oldavatar = '';
		$row = new usersDbTable();
		if ($uid > 0) {
			if (!$row->load($uid)) {
				$elxis->redirect($redirurl, $eLang->get('USERNFOUND'), true);
			}
			$oldpass = $row->pword;
			$oldlastclicks = $row->lastclicks;
			$oldavatar = $row->avatar;
		}

		if (!$row->bind($_POST)) {
			$elxis->redirect($redirurl, $row->getErrorMsg(), true);
		}

		$row->lastclicks = $oldlastclicks;
		$row->block = isset($_POST['block']) ? (int)$_POST['block'] : 0;//because it is a checkbox

		$tabopen = isset($_POST['tabopen']) ? (int)$_POST['tabopen'] : 0;
		$redirurl = $elxis->makeAURL('user:users/edit.html').'?uid='.$uid.'&tabopen='.$tabopen;

		$pword = trim(filter_input(INPUT_POST, 'pword', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		$pword2 = filter_input(INPUT_POST, 'pword2', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		if ($uid > 0) {
			if ($pword != '') {
				$len = eUTF::strlen($pword);
				if (($pword != $pword2) || ($len < 6)) {
					$errormsg = ($pword != $pword2) ? $eLang->get('PASSNOMATCH') : $eLang->get('INVALIDPASS');
					$elxis->redirect($redirurl, $errormsg, true);
				}
				$pstr = preg_replace('/[^A-Z\-\_0-9\!\@]/i', '', $pword);
				if (($pstr != $pword) || (trim($_POST['pword']) !=  $pword)) {
					$errormsg = sprintf($eLang->get('FIELDNOACCCHAR'), $eLang->get('PASSWORD'));
					$elxis->redirect($redirurl, $errormsg, true);
				}
				$row->pword = $elxis->obj('crypt')->getEncryptedPassword($pword);
			} else {
				$row->pword = $oldpass;
			}
		} else {
			$len = eUTF::strlen($pword);
			if (($pword == '') || ($pword != $pword2) || ($len < 6)) {
				$errormsg = ($pword != $pword2) ? $eLang->get('PASSNOMATCH') : $eLang->get('INVALIDPASS');
				$elxis->redirect($redirurl, $errormsg, true);
			}
			$pstr = preg_replace('/[^A-Z\-\_0-9\!\@]/i', '', $pword);
			if (($pstr != $pword) || (trim($_POST['pword']) !=  $pword)) {
				$errormsg = sprintf($eLang->get('FIELDNOACCCHAR'), $eLang->get('PASSWORD'));
				$elxis->redirect($redirurl, $errormsg, true);
			}
			$row->pword = $elxis->obj('crypt')->getEncryptedPassword($pword);
		}

		$userLevel = $this->model->getGroupLevel($row->gid);
		if ($elxis->acl()->getLevel() < $userLevel) {
			$elxis->redirect($redirurl, $eLang->get('NALLOW_HIGHER_ACCESS'), true);
		}

		$row->birthdate = trim($row->birthdate);
		if ($row->birthdate != '') {
			$newdate = $eDate->convertFormat($row->birthdate, $eLang->get('DATE_FORMAT_BOX'), 'Y-m-d');
			if ($newdate !== false) { $row->birthdate = $newdate; } else { $row->birthdate = null; }
		}

		$row->expiredate = trim($row->expiredate);
		if ($row->expiredate != '') {
			$newdate = $eDate->convertFormat($row->expiredate, $eLang->get('DATE_FORMAT_BOX'), 'Y-m-d H:i:s');
			if ($newdate !== false) { $row->expiredate = $newdate; } else { $row->expiredate = '2060-01-01 00:00:00'; }
		} else {
			$row->expiredate = '2060-01-01 00:00:00';
		}

		//process params
		$row->params = '';
		$pat = "#([\"]|[\$]|[\#]|[\<]|[\>]|[\*]|[\%]|[\~]|[\`]|[\|]|[\{]|[\}]|[\\\])#u";
		foreach ($_POST as $key => $val) {
			if (strpos($key, 'params_') === 0) {
				$param_name = substr($key, 7);
				$param_val = filter_input(INPUT_POST, $key, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
				$param_val = eUTF::trim(preg_replace($pat, '', $param_val));
				$row->params .= $param_name.'='.$param_val."\n";
			}
		}
		if ($row->params == '') { $row->params = null; }

		$relpath = 'media/images/avatars/';
		if (defined('ELXIS_MULTISITE')) {
			if (ELXIS_MULTISITE > 1) { $relpath = 'media/images/site'.ELXIS_MULTISITE.'/avatars/'; }
		}
		if ($elxis->acl()->check('com_user', 'profile', 'uploadavatar') == 1) {
			$avatar_deleteold = isset($_POST['avatar_deleteold']) ? (int)$_POST['avatar_deleteold'] : 0;
			$newavatar = false;
			$eFiles = eFactory::getFiles();
			if (isset($_FILES['avatar']) && is_array($_FILES['avatar'])) {
				$tmpuid = 'temp'.rand(1000, 9999);
				$avname = eUTF::strtolower($_FILES['avatar']['name']);
				$avname = preg_replace("/[\s]/", "_", $avname);
				$avname = filter_var($avname, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
				$lowfilename = ($uid > 0) ? $row->uid.'_'.$avname : $tmpuid.'_'.$avname;
				$ext = $eFiles->getExtension($lowfilename);
				$valid_exts = array('jpg', 'jpeg', 'png', 'gif');
				if (in_array($ext, $valid_exts)) {
					if (file_exists(ELXIS_PATH.'/'.$relpath.$lowfilename)) {
						$lowfilename = ($uid > 0) ? $row->uid.'_'.time().'.'.$ext : $tmpuid.'_'.time().'.'.$ext;
					}
					if ($eFiles->upload($_FILES['avatar']['tmp_name'], $relpath.$lowfilename)) {
						$newavatar = true;
						$isize = getimagesize(ELXIS_PATH.'/'.$relpath.$lowfilename);
						if (($isize[0] != 200) || ($isize[1] != 200)) {
							if (!$eFiles->resizeImage($relpath.$lowfilename, 200, 200, true)) {
								$eFiles->deleteFile($relpath.$lowfilename);
								$newavatar = false;
							}
						}
					}
				}
				unset($avname, $ext, $valid_exts);
			}

			if ($newavatar) {
				if ((trim($row->avatar) != '') && ($row->avatar != 'noavatar.png') && file_exists(ELXIS_PATH.'/'.$relpath.$row->avatar)) {
					$eFiles->deleteFile($relpath.$row->avatar);
				}
				$row->avatar = $lowfilename;
			} else if ($avatar_deleteold == 1) {
				if ((trim($row->avatar) != '') && ($row->avatar != 'noavatar.png') && file_exists(ELXIS_PATH.'/'.$relpath.$row->avatar)) {
					$eFiles->deleteFile($relpath.$row->avatar);
				}
				$row->avatar = '';
			}
		}

		if (!$row->fullCheck()) {
			if ((intval($uid) == 0) && (trim($row->avatar) != '')) {
				$eFiles->deleteFile($relpath.$row->avatar);
			}
			$elxis->redirect($redirurl, $row->getErrorMsg(), true);
		}

		$ok = ($uid > 0) ? $row->update() : $row->insert();
		if (!$ok) {
			if ((intval($uid) == 0) && (trim($row->avatar) != '')) {
				$eFiles->deleteFile($relpath.$row->avatar);
			}
			$elxis->redirect($redirurl, $row->getErrorMsg(), true);
		}

		if ((intval($uid) == 0) && (trim($row->avatar) != '') && ($row->uid > 0) && isset($tmpuid)) {
			$newname = str_replace($tmpuid, $row->uid, $row->avatar);
			$ok = $eFiles->move($relpath.$row->avatar, $relpath.$newname);
			if ($ok) {
				$row->avatar = $newname;
				$row->update();
			}
		}

		$mailsent = 0;
		if ($row->gid != 1) {
			$mailpw = isset($_POST['mailpw']) ? (int)$_POST['mailpw'] : 0;
			if (($mailpw == 1) && ($pword != '') && ($row->block == 0)) { //mail user access details
    			$parsed = parse_url($elxis->getConfig('URL')); 
 				$host = preg_replace('#^(www\.)#i', '', $parsed['host']);

				$subject = sprintf($eLang->get('ACCESS_DETAILS_SITE'), $host);
				$profile_page = $elxis->makeURL('user:members/'.$row->uid.'.html');

				$body = $eLang->get('HI').' '.$row->firstname.' '.$row->lastname."\n";
				$body .= sprintf($eLang->get('YCAN_LOGIN_DETAILS'), $host)."\n\n";
				$body .= $eLang->get('USERNAME')."\t\t".' : '.$row->uname."\n";
				$body .= $eLang->get('PASSWORD')."\t\t".' : '.$pword."\n\n";
				$body .= $eLang->get('LOGIN')."\t\t".' : '.$elxis->makeURL('user:login/', '', true)."\n";
				$body .= $eLang->get('USERPROFILE')."\t\t".' : '.$profile_page."\n\n\n";
				$body .= $eLang->get('REGARDS')."\n";
				$body .= $elxis->getConfig('SITENAME')."\n";
				$body .= $elxis->getConfig('URL')."\n\n\n";
				$body .= "______________________________________________________________________________\n";
				$body .= $eLang->get('NOREPLYMSGINFO');

				$to = $row->email.','.$row->firstname.' '.$row->lastname;
				$mailsent = $elxis->sendmail($subject, $body, '', null, 'plain', $to);
			}
		}

		$eSession->set('token_fmusedit');

		$task = isset($_POST['task']) ? trim($_POST['task']) : 'save';
		$tabopen = (isset($_POST['tabopen'])) ? (int)$_POST['tabopen'] : 0;
		if ($task == 'apply') {
			$redirurl = $elxis->makeAURL('user:users/edit.html?uid='.$row->uid);
			if ($tabopen > 0) { $redirurl .= '&tabopen='.$tabopen; }
		} else {
			$redirurl = $elxis->makeAURL('user:users/');
		}
		$msg = ($uid > 0) ? $eLang->get('PROFUPSUC') : $eLang->get('ACCOUNT_CREATED_SUC');
		if ($mailsent == 1) { $msg .= ' '.$eLang->get('ACCDET_SENT_USER'); }

		$elxis->redirect($redirurl, $msg);
	}


	/***********************/
	/* SEND E-MAIL TO USER */
	/***********************/
	public function mailuser() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$response = array('success' => 0, 'message' => '');

		$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : 0;
		if ($uid < 1) {
			$response['message'] = 'No user selected!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$pat = "#([\']|[\"]|[\$]|[\#]|[\<]|[\>]|[\*]|[\%]|[\~]|[\`]|[\^]|[\|]|[\}]|[\\\])#u";
		$subject = filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$subject = eUTF::trim(preg_replace($pat, '', $subject));
		$message = eUTF::trim(filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING));
		if ($subject == '') {
			$response['message'] = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('SUBJECT'));
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}
		if ($message == '') {
			$response['message'] = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('MESSAGE'));
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}
		if ($subject != eUTF::trim($_POST['subject'])) {
			$response['message'] = sprintf($eLang->get('FIELDNOACCCHAR'), $eLang->get('SUBJECT'));
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$row = $this->model->getUser($uid);
		if (!$row) {
			$response['message'] = $eLang->get('USERNFOUND');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$txt = $message."\r\n\r\n";
		$txt .= "----------------------------------------------\r\n";
		$txt .= $elxis->getConfig('SITENAME')."\r\n";
		$txt .= $elxis->getConfig('URL');

		$from = $elxis->getConfig('MAIL_FROM_EMAIL').','.$elxis->getConfig('MAIL_FROM_NAME');
		$to = $row->email.','.$row->firstname.' '.$row->lastname;

		$ok = $elxis->sendmail($subject, $txt, '', null, 'plain', $to, null, null, $from);
		if (!$ok) {
			$response['message'] = $eLang->get('ACTION_FAILED');
		} else {
			$response['success'] = 1;
			$response['message'] = $eLang->get('ACTION_SUCCESS');
		}

		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}

}

?>