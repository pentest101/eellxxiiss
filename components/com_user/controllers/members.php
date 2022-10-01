<?php 
/**
* @version		$Id: members.php 2441 2022-03-05 18:01:08Z IOS $
* @package		Elxis
* @subpackage	Component User
* @copyright	Copyright (c) 2006-2022 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class membersUserController extends userController {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null, $task='', $model=null) {
		parent::__construct($view, $task, $model);
	}


	/***********************************/
	/* PREPARE TO DISPLAY MEMBERS LIST */
	/***********************************/
	public function memberslist() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eDoc = eFactory::getDocument();

		$eDoc->setTitle($eLang->get('MEMBERSLIST').' - '.$elxis->getConfig('SITENAME'));
		$eDoc->setDescription($eLang->get('MEMBERSLIST'));
		$eDoc->setKeywords(array($eLang->get('MEMBERSLIST'), $eLang->get('USER'), $eLang->get('PROFILE'), $eLang->get('USERNAME')));

		$allowed = $elxis->acl()->check('com_user', 'memberslist', 'view');
		if ($allowed < 1) {
			if ($elxis->user()->gid == 7) {//guest, redirect to login page
				$redir_url = $elxis->makeURL('user:login/', '', true);
				$elxis->redirect($redir_url);
			}
			$eDoc->setTitle($eLang->get('MEMBERSLIST').' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen($eLang->get('NOTALLOWACCPAGE'));
			return;
		}

		$options = array(
			'total' => 0,
			'page' => 1,
			'maxpage' => 1,
			'limit' => 20,
			'limitstart' => 0,
			'order' => 'fa'
		);

		$dopts = array('block' => 0, 'expiredate' => eFactory::getDate()->getDate());
		$options['total'] = $this->model->countUsers($dopts);
		unset($dopts);

		$options['page'] = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
		if ($options['page'] < 1) { $options['page'] = 1; }
		$options['maxpage'] = ($options['total'] == 0) ? 1 : ceil($options['total']/$options['limit']);
		if ($options['page'] > $options['maxpage']) { $options['page'] = $options['maxpage']; }
		$options['limitstart'] = (($options['page'] -1) * $options['limit']);
		if (isset($_GET['order'])) {
			$options['order'] = filter_input(INPUT_GET, 'order', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
			if ($options['order'] == '') {
				$options['order'] = 'fa'; 
			} else if (!preg_match("/^[a-z]+$/", $options['order'])) {
				$options['order'] = 'fa';
			}
		}
		
		$rows = $this->model->fetchPublicUsers($options['order'], $options['limitstart'], $options['limit']);
		if ($rows) {
			$extra_keys = array();
			foreach ($rows as $row) {
				$extra_keys[] = $row->uname;
			}
			$eDoc->setKeywords($extra_keys);
		}

		$txt = ($options['page'] > 1) ? $eLang->get('MEMBERSLIST').' - '.$eLang->get('PAGE').' '.$options['page'] : $eLang->get('MEMBERSLIST');
		$eDoc->setTitle($txt);
		$txt = $eLang->get('MEMBERSLIST').', '.$eLang->get('PAGE').' '.$options['page'].'. '.sprintf($eLang->get('REGMEMBERSTOTAL'), $options['total']);
		$eDoc->setDescription($txt);
		$eDoc->addFontAwesome(true);
		$eDoc->addStyleLink($elxis->secureBase().'/components/com_user/inc/user'.$eLang->getinfo('RTLSFX').'.css');
		$eDoc->addScriptLink($elxis->secureBase().'/components/com_user/inc/user.js');

		$params = $this->base_getParams();

		$columns = array(
			'groupname' => (int)$params->get('members_groupname', 0),
			'preflang' => (int)$params->get('members_preflang', 1),
			'address' => (int)$params->get('members_address', 0),
			'pcode' => (int)$params->get('members_pcode', 0),
			'city' => (int)$params->get('members_city', 0),
			'country' => (int)$params->get('members_country', 1),
			'phone' => (int)$params->get('members_phone', 0),
			'mobile' => (int)$params->get('members_mobile', 0),
			'email' => (int)$params->get('members_email', 0),
			'website' => (int)$params->get('members_website', 1),
			'gender' => (int)$params->get('members_gender', 0),
			'registerdate' => (int)$params->get('members_registerdate', 1),
			'lastvisitdate' => (int)$params->get('members_lastvisitdate', 1),
			'profile_views' => (int)$params->get('members_profile_views', 0)
		);

		$nav_links = (int)$params->get('nav_links', 1);
		$members_ordering = (int)$params->get('members_ordering', 1);

		$this->view->membersList($rows, $columns, $options, $nav_links, $members_ordering, $params);
	}


	/***********************************/
	/* PREPARE TO DISPLAY USER PROFILE */
	/***********************************/
	public function profile() {
		$elxis = eFactory::getElxis();
		$eDoc = eFactory::getDocument();
		$eLang = eFactory::getLang();

		$eDoc->setTitle($eLang->get('USERPROFILE').' - '.$elxis->getConfig('SITENAME'));
		$eDoc->setDescription($eLang->get('USERPROFILE'));
		$eDoc->setKeywords(array($eLang->get('PROFILE'), $eLang->get('USER')));
		$eDoc->setMetaTag('robots', 'noindex, nofollow');
		$eDoc->addFontAwesome(true);
		$eDoc->addStyleLink($elxis->secureBase().'/components/com_user/inc/user'.$eLang->getinfo('RTLSFX').'.css');	
		$eDoc->addScriptLink($elxis->secureBase().'/components/com_user/inc/user.js');	

		$segments = eFactory::getURI()->getSegments();
		if (count($segments) != 2) {
			exitPage::make('404', 'CUSE-0008'); //just in case
		}

		if ($segments[1] === 'myprofile.html') {
			$uid = $elxis->user()->uid;
		} else {
			$uid = str_ireplace('.html', '', $segments[1]);
			if (!is_numeric($uid)) {
				exitPage::make('404', 'CUSE-0009'); //just in case
			}			
		}

		$uid = (int)$uid;
		if ($uid < 1) {
			exitPage::make('404', 'CUSE-0010');
		}

		$allowed = (int)$elxis->acl()->check('com_user', 'profile', 'view');
		if ($allowed == 2) {
			$proceed = true;
		} else if (($allowed == 1) && ($uid == $elxis->user()->uid)) {
			$proceed = true;
		} else {
			$proceed = false;
		}

		if ($proceed === false) {
			if ($elxis->user()->gid == 7) {//guest, redirect to login page
				$redir_url = $elxis->makeURL('user:login/', '', true);
				$elxis->redirect($redir_url);
			}
			$eDoc->setTitle($eLang->get('USERPROFILE').' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen($eLang->get('NOTALLOWACCPAGE'));
			return;
		}

		$row = $this->model->getUser($uid);
		if (!$row) {
			$eDoc->setTitle($eLang->get('USERPROFILE').' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen($eLang->get('USERNFOUND'));
			return;
		}

		$usname = ($elxis->getConfig('REALNAME') == 1) ? $row->firstname.' '.$row->lastname : $row->uname;
		$eDoc->setTitle($usname.' : '.$eLang->get('PROFILE'));
		$desc = sprintf($eLang->get('PROFILEUSERAT'), $usname, $elxis->getConfig('SITENAME'));
		$eDoc->setDescription($desc);
		$eDoc->setKeywords(array($row->firstname, $row->lastname, $row->uname));
		unset($desc);

		if ($row->block == 1) {
			$eDoc->setTitle($usname.' : '.$eLang->get('PROFILE').' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen($eLang->get('ACCOUNTBLOCKED'));
			return;
		}

		if ($row->expiredate < gmdate('Y-m-d H:i:s')) {
			$eDoc->setTitle($eLang->get('USERPROFILE').' '.$usname.' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen($eLang->get('ACCOUNTEXPIRED'));
			return;
		}

		$row->is_online = 0;
		$row->ip_address = null;
		$row->time_online = 0;
		$row->clicks = 0;
		$row->current_page = null;
		$row->browser = null;

		if ($elxis->user()->uid <> $row->uid) {
			$row->profile_views++;
			$this->model->incrementProfileViews($row->uid, $row->profile_views);
		}

		$sess = $this->model->getUserActivity($uid);
		if ($sess) {
			if ($sess->last_activity + $elxis->getConfig('SESSION_LIFETIME') >= time()) {
				$row->is_online = 1;
				$row->ip_address = trim($sess->ip_address);
				if ($row->ip_address != '') {
					$row->ip_address = $elxis->obj('ip')->ipv6tov4($row->ip_address);
				}

				$row->time_online = intval($sess->last_activity - $sess->first_activity);
				if ($row->time_online < 1) { $row->time_online = 1; }
				$row->clicks = (int)$sess->clicks;
				$row->current_page = trim($sess->current_page);
				$row->user_agent = trim($sess->user_agent);
				$row->browser = $elxis->obj('browser')->getBrowser($row->user_agent, false);
			}
		}
		unset($sess);

		elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
		$userparams = new elxisParameters($row->params, '', 'custom');		

		$params = $this->base_getParams();
		$gravatar = (int)$params->get('gravatar', 0);
		$profile_comments = (int)$params->get('profile_comments', 5);
		$row->avatar = $elxis->obj('avatar')->getAvatar($row->avatar, 200, $gravatar, $row->email);
		unset($gravatar);

		$comments = array();
		if (($uid > 0) && ($profile_comments > 0)) {
			$comments = $this->model->fetchUserComments($uid, $profile_comments);
		}
		unset($profile_comments);

		$messages_total = 0;
		$messages_unread = 0;
		$bookmarks = 0;
		if ($elxis->user()->uid > 0) {
			$bookmarks = $this->model->countBookmarks($elxis->user()->uid);
			$msgObj = $elxis->obj('messages');
			$mparams = array('toid' => $elxis->user()->uid, 'read' => 0, 'delbyto' => 0);
			$messages_unread = $msgObj->countMessages($mparams);
			$messages_total = $msgObj->getTotalMessages($elxis->user()->uid);
			unset($mparams, $msgObj);
		}

		$twitter = array('user' => false, 'tweets' => false);
		$twitterkey = trim($params->get('twitterkey', ''));
		$twittersecret = trim($params->get('twittersecret', ''));
		$row->twitter_username = trim($userparams->get('twitter', ''));

		if ((intval($params->get('profile_twitter', 0)) == 1) && ($twitterkey != '') && ($twittersecret != '') && ($row->twitter_username != '')) {
			$tw = $elxis->obj('twitter');
			$tw->setOption('key', $twitterkey);
			$tw->setOption('secret', $twittersecret);
			$tw->setOption('cachetime', 14400);
			$twitter['user'] = $tw->getProfile($row->twitter_username);
			if ($twitter['user']) {
				$tw->setOption('cachetime', 1800);
				$twitter['tweets'] = $tw->getTweets($row->twitter_username, 5);
			}
			unset($tw);
		}

		$this->view->userProfile($row, $params, $userparams, $usname, $twitter, $comments, $messages_total, $messages_unread, $bookmarks);
	}


	/************************/
	/* BLOCK A USER ACCOUNT */
	/************************/
	public function blockaccount() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
		if ($id < 0) { $id = 0; }
		$this->base_forceSSL('user:members/block.html?id='.$id);

		$response = $this->model->blockUser($id, 1);
		if ($response['success'] === false) {
			eFactory::getDocument()->setTitle($eLang->get('BLOCKUSER').' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen($response['message']);
			return;
		}

		eFactory::getDocument()->setTitle($eLang->get('BLOCKUSER').' - '.$elxis->getConfig('SITENAME'));
		$url = $elxis->makeURL('user:/');
		$elxis->redirect($url, $response['message']);
	}


	/*************************/
	/* DELETE A USER ACCOUNT */
	/*************************/
	public function deleteaccount() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
		if ($id < 0) { $id = 0; }
		$this->base_forceSSL('user:members/delete.html?id='.$id);

		$response = $this->model->deleteUser($id, 'unpublish');
		if ($response['success'] === false) {
			eFactory::getDocument()->setTitle($eLang->get('DELETEACCOUNT').' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen($response['message']);
			return;
		}

		eFactory::getDocument()->setTitle($eLang->get('DELETEACCOUNT').' - '.$elxis->getConfig('SITENAME'));
		$url = $elxis->makeURL('user:/');
		$elxis->redirect($url, $response['message']);
	}


	/**********************************/
	/* PREPARE TO EDIT USER'S PROFILE */
	/**********************************/
	public function editprofile($row=null, $errormsg='') {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eDate = eFactory::getDate();
		$eDoc = eFactory::getDocument();

		if ($elxis->user()->gid == 7) {//guest, redirect to login page
			$redir_url = $elxis->makeURL('user:login/', '', true);
			$elxis->redirect($redir_url);
		}

		if (($row == null) || !is_object($row)) {
			$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
			if ($id < 0) { $id = 0; }
			$this->base_forceSSL('user:members/edit.html?id='.$id);

			$eDoc->setTitle($eLang->get('EDITPROFILE').' - '.$elxis->getConfig('SITENAME'));

			$proceed = false;
			$allowed = $elxis->acl()->check('com_user', 'profile', 'edit');
			if (($allowed == 2) || (($allowed == 1) && ($elxis->user()->uid == $id))) { $proceed = true; }
			if (($id == 0) || ($proceed === false)) {
				$eDoc->setTitle($eLang->get('EDITPROFILE').' - '.$eLang->get('ERROR'));
				$this->view->base_errorScreen($eLang->get('NOTALLOWACCPAGE'));
				return;
			}

			$row = $this->model->getUser($id, 0);
			if (!$row) {
				$eDoc->setTitle($eLang->get('EDITPROFILE').' - '.$eLang->get('ERROR'));
				$this->view->base_errorScreen($eLang->get('USERNFOUND'));
				return;
			}
			$userLevel = $this->model->getGroupLevel($row->gid);
			if ($elxis->acl()->getLevel() < $userLevel) {
				$eDoc->setTitle($eLang->get('EDITPROFILE').' - '.$eLang->get('ERROR'));
				$this->view->base_errorScreen($eLang->get('NALLOW_HIGHER_ACCESS'));
				return;
			}
		}

		$is_online = 1;
		if ($elxis->user()->uid != $row->uid) {
			$is_online = 0;
			$xuid = $row->uid;
			$uids = array();
			$uids[] = $xuid;
			$lastactivity = $this->model->getLastActivity($uids);
			if (isset($lastactivity[$xuid])) {
				$time = $eDate->getTS() - $elxis->getConfig('SESSION_LIFETIME');
				if ($lastactivity[$xuid] >= $time) { $is_online = 1; }
			}
			unset($uids, $xuid, $lastactivity);
		}
		$bookmarks = $this->model->countBookmarks($elxis->user()->uid);
		$msgObj = $elxis->obj('messages');
		$mparams = array('toid' => $elxis->user()->uid, 'read' => 0, 'delbyto' => 0);
		$messages_unread = $msgObj->countMessages($mparams);
		$messages_total = $msgObj->getTotalMessages($elxis->user()->uid);
		unset($mparams, $msgObj);

		$params = $this->base_getParams();

		$eDoc->setTitle($eLang->get('EDITPROFILE').' '.$row->uname.' - '.$elxis->getConfig('SITENAME'));
		$eDoc->setDescription($eLang->get('EDITPROFILE').' '.$row->firstname.' '.$row->lastname);
		$eDoc->setKeywords(array($eLang->get('EDITPROFILE'), $eLang->get('USER'), $row->uname, $row->firstname, $row->lastname));
		//$eDoc->addJQuery();
		$eDoc->addFontAwesome(true);
		$eDoc->addStyleLink($elxis->secureBase().'/components/com_user/inc/user'.$eLang->getinfo('RTLSFX').'.css');	
		$eDoc->addScriptLink($elxis->secureBase().'/components/com_user/inc/user.js');

		$avatar = array(
			'file' => $row->avatar,
			'url' => $elxis->obj('avatar')->getAvatar($row->avatar, 200, 0, $row->email),
			'localpath' => ''
		);

		if (trim($row->avatar) != '') {
			$relpath = 'media/images/avatars/';
			if (defined('ELXIS_MULTISITE')) {
				if (ELXIS_MULTISITE > 1) { $relpath = 'media/images/site'.ELXIS_MULTISITE.'/avatars/'; }
			}
			if (!preg_match('#^(http(s)?\:\/\/)#', $row->avatar) && file_exists(ELXIS_PATH.'/'.$relpath.$row->avatar)) {
				$avatar['localpath'] = $relpath.$row->avatar;
			}
		}

		elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
		$userparams = new elxisParameters($row->params, '', 'custom');		

		$this->view->editProfile($row, $avatar, $userparams, $errormsg, $messages_total, $messages_unread, $bookmarks, $is_online, $params);
	}


	/*********************/
	/* SAVE USER PROFILE */
	/*********************/
	public function saveprofile() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eDoc = eFactory::getDocument();
		$eDate = eFactory::getDate();

		if ($elxis->user()->gid == 7) {//guest, redirect to login page
			$redir_url = $elxis->makeURL('user:login/', '', true);
			$elxis->redirect($redir_url);
		}

		$id = isset($_POST['uid']) ? (int)$_POST['uid'] : 0;
		if (($id < 1) || ($elxis->user()->gid == 7) || ($elxis->user()->gid == 6)) {
			$url = $elxis->makeURL('user:/');
			$elxis->redirect($url);
		}

		$eDoc->setTitle($eLang->get('EDITPROFILE').' - '.$elxis->getConfig('SITENAME'));
		$eDoc->addFontAwesome(true);
		$eDoc->addStyleLink($elxis->secureBase().'/components/com_user/inc/user'.$eLang->getinfo('RTLSFX').'.css');

		$eSession = eFactory::getSession();
		$sess_token = trim($eSession->get('token_fmeditprof'));
		$token = trim(filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		if (($token == '') || ($sess_token == '') || ($sess_token != $token)) {
			$eDoc->setTitle($eLang->get('EDITPROFILE').' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen($eLang->get('REQDROPPEDSEC'));
			return;
		}

		$proceed = false;
		$allowed = $elxis->acl()->check('com_user', 'profile', 'edit');
		if (($allowed == 2) || (($allowed == 1) && ($elxis->user()->uid == $id))) { $proceed = true; }
		if ($proceed === false) {
			$eDoc->setTitle($eLang->get('EDITPROFILE').' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen($eLang->get('NOTALLOWACCPAGE'));
			return;
		}

		$row = new usersDbTable();
		if (!$row->load($id)) {
			$eDoc->setTitle($eLang->get('EDITPROFILE').' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen($eLang->get('USERNFOUND'));
			return;
		}

		if (intval($row->block) == 1) {
			$eDoc->setTitle($eLang->get('EDITPROFILE').' '.$row->uname.' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen($eLang->get('ACCOUNTBLOCKED'));
			return;
		}

		if ($row->expiredate < gmdate('Y-m-d H:i:s')) {
			$eDoc->setTitle($eLang->get('EDITPROFILE').' '.$row->uname.' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen($eLang->get('ACCOUNTEXPIRED'));
			return;
		}

		$row->firstname = filter_input(INPUT_POST, 'firstname', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$row->lastname = filter_input(INPUT_POST, 'lastname', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$row->gender = filter_input(INPUT_POST, 'gender', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$row->occupation = filter_input(INPUT_POST, 'occupation', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$row->country = filter_input(INPUT_POST, 'country', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		if (strlen($row->country) > 3) { $row->country = null; }
		$row->city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$row->postalcode = filter_input(INPUT_POST, 'postalcode', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$row->address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$row->phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$row->mobile = filter_input(INPUT_POST, 'mobile', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$row->website = filter_input(INPUT_POST, 'website', FILTER_SANITIZE_URL);
		$row->preflang = filter_input(INPUT_POST, 'preflang', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		$row->timezone = filter_input(INPUT_POST, 'timezone', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);

		$new_activation_by_user = false;
		$new_activation_by_admin = false;

		if ($elxis->getConfig('SECURITY_LEVEL') < 2) {
			$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
			if ($email != $row->email) {
				$row->email = $email;
				if ($elxis->getConfig('REGISTRATION_ACTIVATION') == 2) {
					if ($elxis->user()->gid <> 1) {
						$row->block = 1;
						$new_activation_by_admin = true;
					}
				} else if ($elxis->getConfig('REGISTRATION_ACTIVATION') == 1) {
					if ($elxis->user()->gid <> 1) {
						$row->block = 1;
						$act = '';
						while (strlen($act) < 40) { $act .= mt_rand(0, mt_getrandmax()); }
						$row->activation = sha1($act);
						$new_activation_by_user = true;
					}
				}
			}
		}

		$pword = trim(filter_input(INPUT_POST, 'pword', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		$pword2 = trim(filter_input(INPUT_POST, 'pword2', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		if ($pword != '') {
			$len = eUTF::strlen($pword);
			if (($pword != $pword2) || ($len < 6)) {
				$errormsg = ($pword != $pword2) ? $eLang->get('PASSNOMATCH') : $eLang->get('INVALIDPASS');
				$row->pword = '';
				$this->editprofile($row, $errormsg);
				return;
			}
			$pstr = preg_replace('/[^A-Z\-\_0-9\!\@]/i', '', $pword);
			if (($pstr != $pword) || (trim($_POST['pword']) !=  $pword)) {
				$errormsg = sprintf($eLang->get('FIELDNOACCCHAR'), $eLang->get('PASSWORD'));
				$row->pword = '';
				$this->editprofile($row, $errormsg);
				return;
			}
			$row->pword = $elxis->obj('crypt')->getEncryptedPassword($pword);
		}

		$captcha = $elxis->obj('captcha');
		$ok = $captcha->validate($elxis->getConfig('CAPTCHA'), 'captcha_seccode', 'seccode', 'norobot', '');
		if (!$ok) {
			$row->pword = '';
			$this->editprofile($row, $captcha->getError());
			return; 
		}
		unset($captcha);

		$birthdate = filter_input(INPUT_POST, 'birthdate', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		$row->birthdate = null;
		if (trim($birthdate) != '') {
			$newdate = eFactory::getDate()->convertFormat($birthdate, $eLang->get('DATE_FORMAT_BOX'), 'Y-m-d');
			if ($newdate === false) {
			 	$errormsg = 'Invalid date '.$eLang->get('BIRTHDATE');
			 	$row->pword = '';
			 	$this->editprofile($row, $errormsg);
			 	return;
			}
			$row->birthdate = $newdate;
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

		if ($elxis->acl()->check('com_user', 'profile', 'uploadavatar') == 1) {
			$avatar_deleteold = isset($_POST['avatar_deleteold']) ? (int)$_POST['avatar_deleteold'] : 0;
			$newavatar = false;
			$relpath = 'media/images/avatars/';
			if (defined('ELXIS_MULTISITE')) {
				if (ELXIS_MULTISITE > 1) { $relpath = 'media/images/site'.ELXIS_MULTISITE.'/avatars/'; }
			}
			$eFiles = eFactory::getFiles();
			if (isset($_FILES['avatar']) && is_array($_FILES['avatar'])) {
				$avname = eUTF::strtolower($_FILES['avatar']['name']);
				$avname = preg_replace("/[\s]/", "_", $avname);
				$avname = filter_var($avname, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
				$lowfilename = $row->uid.'_'.$avname;
				$ext = $eFiles->getExtension($lowfilename);
				$valid_exts = array('jpg', 'jpeg', 'png', 'gif');
				if (in_array($ext, $valid_exts)) {
					if (file_exists(ELXIS_PATH.'/'.$relpath.$lowfilename)) {
						$lowfilename = $row->uid.'_'.time().'.'.$ext;
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
			$errormsg = $row->getErrorMsg();
			$row->pword = '';
			$this->editprofile($row, $errormsg);
			return;
		}

		if (!$row->update()) {
			$eDoc->setTitle($eLang->get('EDITPROFILE').' '.$row->uname.' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen($row->getErrorMsg());
			return;
		}

		$eSession->set('token_fmeditprof');

		$redirect_to_profile = false;
		if ($new_activation_by_admin === true) {
			$id = $row->uid;
			$db = eFactory::getDB();
			$sql = "DELETE FROM ".$db->quoteId('#__session')." WHERE ".$db->quoteId('uid')." = :uid";
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':uid', $id, PDO::PARAM_INT);
			$stmt->execute();

			$this->mailReactivateAccount($row, 'admin');

			$msg = $eLang->get('PROFUPSUC')."<br />\n";
			$msg .= sprintf($eLang->get('USERACCBLOCKED'), $row->uname)."<br />\n";
			$msg .=	$eLang->get('ADMINMREACT');
		} else if ($new_activation_by_user === true) {
			$id = $row->uid;
			$db = eFactory::getDB();
			$sql = "DELETE FROM ".$db->quoteId('#__session')." WHERE ".$db->quoteId('uid')." = :uid";
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':uid', $id, PDO::PARAM_INT);
			$stmt->execute();

			$this->mailReactivateAccount($row, 'user');
			
			$msg = $eLang->get('PROFUPSUC')."<br />\n";
			if ($row->uid == $elxis->user()->uid) {
				$msg .= sprintf($eLang->get('EMAILATCHANGED'), $elxis->getConfig('SITENAME'))."<br />\n";
				$msg .= $eLang->get('MAILACTLINK');
			} else {
				$msg .= $eLang->get('USERMREACT');
			}
		} else {
			$msg = $eLang->get('PROFUPSUC');
			$redirect_to_profile = ($row->block == 0) ? true : false;
		}

		if ($redirect_to_profile) {
			$link = $elxis->makeURL('user:members/'.$row->uid.'.html');
			$elxis->redirect($link);
		}

		$is_online = 1;
		if ($elxis->user()->uid != $row->uid) {
			$is_online = 0;
			$xuid = $row->uid;
			$uids = array();
			$uids[] = $xuid;
			$lastactivity = $this->model->getLastActivity($uids);
			if (isset($lastactivity[$xuid])) {
				$time = $eDate->getTS() - $elxis->getConfig('SESSION_LIFETIME');
				if ($lastactivity[$xuid] >= $time) { $is_online = 1; }
			}
			unset($uids, $xuid, $lastactivity);
		}

		$bookmarks = $this->model->countBookmarks($elxis->user()->uid);
		$msgObj = $elxis->obj('messages');
		$mparams = array('toid' => $elxis->user()->uid, 'read' => 0, 'delbyto' => 0);
		$messages_unread = $msgObj->countMessages($mparams);
		$messages_total = $msgObj->getTotalMessages($elxis->user()->uid);
		unset($mparams, $msgObj);

		$params = $this->base_getParams();

		$eDoc->setTitle($row->uname.' : '.$eLang->get('EDITPROFILE'));
		$this->view->profileSuccess($row, $msg, $messages_total, $messages_unread, $bookmarks, $is_online, $params);
	}


	/*******************************************/
	/* PREPARE TO DISPLAY USER BOOKMARKS/NOTES */
	/*******************************************/
	public function bookmarks() {
		$elxis = eFactory::getElxis();
		$eDoc = eFactory::getDocument();
		$eLang = eFactory::getLang();
		$pathway = eFactory::getPathway();

		$options = array(
			'uid' => 0,
			'total' => 0,
			'page' => 1,
			'maxpage' => 1,
			'limit' => 10,
			'limitstart' => 0
		);

		$options['uid'] = (int)$elxis->user()->uid;

		if ($options['uid'] < 1) {
			if ($elxis->user()->gid == 7) {//guest, redirect to login page
				$redir_url = $elxis->makeURL('user:login/', '', true);
				$elxis->redirect($redir_url);
			}
			$eDoc->setTitle($eLang->get('BOOKMARKS_NOTES').' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen($eLang->get('NOTALLOWACCPAGE'));
			return;
		}

		$options['total'] = $this->model->countBookmarks($options['uid']);

		$options['page'] = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
		if ($options['page'] < 1) { $options['page'] = 1; }
		$options['maxpage'] = ($options['total'] == 0) ? 1 : ceil($options['total'] / $options['limit']);
		if ($options['page'] > $options['maxpage']) { $options['page'] = $options['maxpage']; }
		$options['limitstart'] = (($options['page'] - 1) * $options['limit']);

		$rows = null;
		if ($options['total'] > 0) {
			$rows = $this->model->fetchBookmarks($options['uid'], $options['limitstart'], $options['limit']);
		}

		$categories = $this->bookmarkCategories();//base

		$msgObj = $elxis->obj('messages');
		$params = array('toid' => $options['uid'], 'read' => 0, 'delbyto' => 0);
		$messages_unread = $msgObj->countMessages($params);
		$messages_total = $msgObj->getTotalMessages($options['uid']);
		unset($params, $msgObj);

		$avatar = '';
		$params = $this->base_getParams();
		$gravatar = (int)$params->get('gravatar', 0);
		$avatar = $elxis->obj('avatar')->getAvatar($elxis->user()->avatar, 200, $gravatar, $elxis->user()->email);
		unset($gravatar);

		$title = ($options['page'] > 1) ? $eLang->get('BOOKMARKS_NOTES').' - '.$eLang->get('PAGE').' '.$options['page'] : $eLang->get('BOOKMARKS_NOTES');
		$eDoc->setTitle($title);
		$desc = $eLang->get('BOOKMARKS_NOTES').', '.$elxis->getConfig('SITENAME');
		$eDoc->setDescription($desc);
		$eDoc->addFontAwesome(true);
		$eDoc->addStyleLink($elxis->secureBase().'/components/com_user/inc/user'.$eLang->getinfo('RTLSFX').'.css');	
		$eDoc->addScriptLink($elxis->secureBase().'/components/com_user/inc/user.js');
		unset($title, $desc);

		$pathway->deleteAllNodes();
		$pathway->addNode($eLang->get('USERSCENTRAL'), 'user:/');
		if ($options['page'] > 1) {
			$pathway->addNode($eLang->get('BOOKMARKS_NOTES'), 'user:bookmarks/');
			$pathway->addNode($eLang->get('PAGE').' '.$options['page']);
		} else {
			$pathway->addNode($eLang->get('BOOKMARKS_NOTES'));
		}

		$this->view->bookmarksHTML($rows, $categories, $options, $messages_unread, $messages_total, $avatar, $params);
	}


	/*****************/
	/* LOAD BOOKMARK */
	/*****************/
	public function loadbookmark() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$response = array('success' => 0, 'message' => '');

		$uid = (int)$elxis->user()->uid;
		if ($uid < 1) {
			$response['message'] = $eLang->get('NOTALLOWACTION');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
		if ($id < 1) {
			$response['message'] = 'No bookmark requested!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$row = new bookmarksDbTable();
		if (!$row->load($id)) {
			$response['message'] = 'Bookmark '.$id.' not found in database!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		if ($row->uid != $uid) {
			$response['message'] = $eLang->get('NOTALLOWACCITEM');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		if ($row->cid == 5) {//reminder
			$localdate = eFactory::getDate()->elxisToLocal($row->reminderdate, true);
			$datetime = new DateTime($localdate);
			$response['reminderdate'] = $datetime->format($eLang->get('DATE_FORMAT_BOX_LONG'));
			unset($localdate, $datetime);
		} else {
			$response['reminderdate'] = '';
		}

		$response['success'] = 1;
		$response['id'] = $row->id;
		$response['cid'] = $row->cid;
		$response['title'] = $row->title;
		$response['link'] = $row->link;
		$response['note'] = $row->note;

		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/*******************/
	/* DELETE BOOKMARK */
	/*******************/
	public function deletebookmark() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$response = array('success' => 0, 'message' => '');

		$uid = (int)$elxis->user()->uid;
		if ($uid < 1) {
			$response['message'] = $eLang->get('NOTALLOWACTION');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
		if ($id < 1) {
			$response['message'] = 'No bookmark set!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$row = new bookmarksDbTable();
		if (!$row->load($id)) {
			$response['message'] = 'Bookmark '.$id.' not found in database!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		if ($row->uid != $uid) {
			$response['message'] = $eLang->get('NOTALLOWACCITEM');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		if (!$row->delete()) {
			$response['message'] = $row->getErrorMsg();
		} else {
			$response['success'] = 1;
		}

		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/*****************/
	/* SAVE BOOKMARK */
	/*****************/
	public function savebookmark() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eDate = eFactory::getDate();
		$eSession = eFactory::getSession();

		$response = array('success' => 0, 'message' => '');

		$uid = (int)$elxis->user()->uid;
		if ($uid < 1) {
			$response['message'] = $eLang->get('NOTALLOWACTION');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$sess_token = trim($eSession->get('token_fmeditbkm'));
		$sess_modtoken = trim($eSession->get('token_fmeditbkmmod'));
		$token = trim(filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));

		$ok = false;
		if (($token != '') && ($sess_token != '') && ($sess_token == $token)) { $ok = true; }//Component user, module user alerts < v1.2
		if (($token != '') && ($sess_modtoken != '') && ($sess_modtoken == $token)) { $ok = true; }//Module user alerts >= v1.2
		if (!$ok) {
			$response['message'] = $eLang->get('REQDROPPEDSEC');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

		$row = new bookmarksDbTable();
		if ($id > 0) {
			if (!$row->load($id)) {
				$response['message'] = 'Bookmark '.$id.' not found in database!';
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit;
			}
			if ($row->uid != $uid) {
				$response['message'] = $eLang->get('NOTALLOWACCITEM');
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit;
			}
		}

		$pat = "#([\"]|[\$]|[\*]|[\%]|[\~]|[\`]|[\^]|[\|]|[\\\])#u";

		$row->cid = isset($_POST['cid']) ? (int)$_POST['cid'] : 0;
		$row->title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$row->title = eUTF::trim(preg_replace($pat, '', $row->title));
		$row->link = eUTF::trim(filter_input(INPUT_POST, 'link', FILTER_SANITIZE_URL));
		$row->note = strip_tags(filter_input(INPUT_POST, 'note', FILTER_UNSAFE_RAW));
		$row->note = eUTF::trim(preg_replace($pat, '', $row->note));
		if (!$row->id) {
			$row->created = $eDate->getDate();
		}
		$row->uid = (int)$elxis->user()->uid;

		if ($row->cid == 5) {//reminder
			$row->reminderdate = trim(filter_input(INPUT_POST, 'reminderdate', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
			if ($row->reminderdate == '') { $row->reminderdate = '1970-01-01 00:00:00'; }
			if ($row->reminderdate != '1970-01-01 00:00:00') {
				$n = strlen($row->reminderdate);
				if ($n == 19) {
					$userdate = $row->reminderdate;
				} else if ($n == 16) {
					$userdate = $row->reminderdate.':00';
				} else if ($n == 10) {//just in case
					$userdate = $row->reminderdate.' 12:00:00';
				} else {
					$userdate = $row->reminderdate;
					$response['message'] = $eLang->get('INVALID_DATE').' '.$row->reminderdate;
				}
				$newdate = $eDate->convertFormat($userdate, $eLang->get('DATE_FORMAT_BOX_LONG'), 'Y-m-d H:i:s');
				if ($newdate !== false) {
					$row->reminderdate = $eDate->localToElxis($newdate);
				} else {
					$response['message'] = $eLang->get('INVALID_DATE').' '.$row->reminderdate;
					$row->reminderdate = '1970-01-01 00:00:00';
				}
			}
			if ($row->reminderdate <= gmdate('Y-m-d H:i:s')) {
				$row->remindersent = 1;
			} else {
				$row->remindersent = 0;
			}
		} else {
			$row->reminderdate = '1970-01-01 00:00:00';
		}

		if ($row->cid < 1) { $response['message'] = 'Invalid category!'; }
		if ($row->link != '') {
			if (strpos(strtolower($row->link), 'http') === false) {
				$response['message'] = $eLang->get('INVALID_URL'); 
			}
		}

		if ($response['message'] != '') {
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$ok = $row->store();
		if (!$ok) {
			$response['message'] = $row->getErrorMsg();
		} else {
			$response['success'] = 1;
		}
		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/************************************/
	/* PREPARE TO LIST MESSAGES THREADS */
	/************************************/
	public function pmthreads() {
		$elxis = eFactory::getElxis();
		$eDoc = eFactory::getDocument();
		$eLang = eFactory::getLang();
		$pathway = eFactory::getPathway();

		$uid = (int)$elxis->user()->uid;
		if ($uid < 1) {
			if ($elxis->user()->gid == 7) {//guest, redirect to login page
				$redir_url = $elxis->makeURL('user:login/', '', true);
				$elxis->redirect($redir_url);
			}
			$eDoc->setTitle($eLang->get('PERSONAL_MESSAGES').' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen($eLang->get('NOTALLOWACCPAGE'));
			return;
		}

		$msgObj = $elxis->obj('messages');
		$threads = $msgObj->getThreads($uid, 0, 50, false);
		$total_threads = $msgObj->getTotalThreads();
		$total_messages = $msgObj->getTotalMessages($uid);
		$options = array('toid' => $uid, 'read' => 0, 'delbyto' => 0);
		$unread_messages = $msgObj->countMessages($options);
		unset($msgObj, $options);

		$params = $this->base_getParams();

		$bookmarks = $this->model->countBookmarks($uid);

		$eDoc->setTitle($eLang->get('PERSONAL_MESSAGES'));
		$eDoc->setDescription($eLang->get('PERSONAL_MESSAGES').', '.$elxis->getConfig('SITENAME'));
		$eDoc->addFontAwesome(true);
		$eDoc->addStyleLink($elxis->secureBase().'/components/com_user/inc/user'.$eLang->getinfo('RTLSFX').'.css');
		$eDoc->addScriptLink($elxis->secureBase().'/components/com_user/inc/user.js');

		$pathway->deleteAllNodes();
		$pathway->addNode($eLang->get('USERSCENTRAL'), 'user:/');
		$pathway->addNode($eLang->get('PERSONAL_MESSAGES'), 'user:pms/');

		$this->view->pmThreadsHTML($threads, $total_threads, $total_messages, $unread_messages, $bookmarks, $elxis, $eLang, $eDoc, $params);
	}


	/**************************************/
	/* PREPARE TO DISPLAY MESSAGES THREAD */
	/**************************************/
	public function readthread() {
		$elxis = eFactory::getElxis();
		$eDoc = eFactory::getDocument();
		$eLang = eFactory::getLang();
		$pathway = eFactory::getPathway();

		$uid = (int)$elxis->user()->uid;
		if ($uid < 1) {
			if ($elxis->user()->gid == 7) {//guest, redirect to login page
				$redir_url = $elxis->makeURL('user:login/', '', true);
				$elxis->redirect($redir_url);
			}
			$eDoc->setTitle($eLang->get('PERSONAL_MESSAGES').' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen($eLang->get('NOTALLOWACCPAGE'));
			return;
		}

		$segments = eFactory::getURI()->getSegments();
		if (count($segments) != 2) {//just in case
			exitPage::make('404', 'CUSE-0018');
		}

		$threadid = str_ireplace('.html', '', $segments[1]);
		if (!is_numeric($threadid) || (intval($threadid) < 1)) {//just in case
			exitPage::make('404', 'CUSE-0019');
		}
		$threadid = (int)$threadid;			

		$msgObj = $elxis->obj('messages');
		$rows = $msgObj->getThread($threadid);
		if (!$rows) {
			$redir_url = $elxis->makeURL('user:pms/');
			$elxis->redirect($redir_url);
		}

		if (($rows[0]->fromid != $uid) && ($rows[0]->toid != $uid)) {
			$redir_url = $elxis->makeURL('user:pms/');
			$elxis->redirect($redir_url);
		}

		$msgObj->markThreadRead($threadid, $uid, 1);

		$total_threads = $msgObj->countTotalThreads($uid);
		$total_messages = $msgObj->getTotalMessages($uid);
		$options = array('toid' => $uid, 'read' => 0, 'delbyto' => 0);
		$unread_messages = $msgObj->countMessages($options);
		unset($msgObj, $options);

		$bookmarks = $this->model->countBookmarks($uid);

		$params = $this->base_getParams();

		$eDoc->setTitle($eLang->get('PERSONAL_MESSAGES'));
		$eDoc->setDescription($eLang->get('PERSONAL_MESSAGES').', '.$elxis->getConfig('SITENAME'));
		$eDoc->addFontAwesome(true);
		$eDoc->addStyleLink($elxis->secureBase().'/components/com_user/inc/user'.$eLang->getinfo('RTLSFX').'.css');
		$eDoc->addScriptLink($elxis->secureBase().'/components/com_user/inc/user.js');

		$pathway->deleteAllNodes();
		$pathway->addNode($eLang->get('USERSCENTRAL'), 'user:/');
		$pathway->addNode($eLang->get('PERSONAL_MESSAGES'), 'user:pms/');
		$pathway->addNode($eLang->get('DISCUSSION_THREAD'));

		$this->view->pmThreadHTML($rows, $total_threads, $total_messages, $unread_messages, $bookmarks, $elxis, $eLang, $eDoc, $params);
	}


	/*****************************/
	/* SEND NEW PERSONAL MESSAGE */
	/*****************************/
	public function sendmessage() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eSession = eFactory::getSession();

		$response = array('success' => 0, 'message' => '');

		$myuid = (int)$elxis->user()->uid;
		if ($myuid < 1) {
			$response['message'] = $eLang->get('NOTALLOWACTION');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$params = $this->base_getParams();
		if ($elxis->acl()->getLevel() <= 2) {//users send pms access check applies
			$usersendpms = (int)$params->get('usersendpms', 2);
		} else {
			$usersendpms = 2;//allowed to all
		}
		unset($params);

		if ($usersendpms < 1) {
			$response['message'] = $eLang->get('NOTALLOWACTION');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$sess_token = trim($eSession->get('token_fmsendpm'));
		$token = trim(filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		if (($token == '') || ($sess_token == '') || ($sess_token != $token)) {
			$response['message'] = $eLang->get('REQDROPPEDSEC');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$data = array();
		$data['fromid'] = $myuid;
		$data['fromname'] = $elxis->user()->firstname.' '.$elxis->user()->lastname;
		$data['toid'] = array();
		$data['replyto'] = (isset($_POST['replyto'])) ? (int)$_POST['replyto'] : 0;

		$pat = "#([\"]|[\$]|[\*]|[\%]|[\~]|[\`]|[\^]|[\|]|[\\\])#u";
		$data['message'] = strip_tags(filter_input(INPUT_POST, 'message', FILTER_UNSAFE_RAW));
		$data['message'] = eUTF::trim(preg_replace($pat, '', $data['message']));
		if ($data['message'] == '') {
			$response['message'] = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('MESSAGE'));
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$toids = array();
		$recipients_str = isset($_POST['toid']) ? trim($_POST['toid']) : '';
		if ($recipients_str != '') {
			$parts = explode(',', $recipients_str);
			foreach ($parts as $part) {
				$id = (int)$part;
				if ($id == $myuid) { continue; }//you cannot send message to yourself!
				if ($id > 0) { $toids[] = $id; }
			}
		}

		if (!$toids) {
			$response['message'] = 'No recipients selected!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$people = $this->model->getAllUsers($myuid);
		if (!$people) {
			$response['message'] = 'There are no users to send messages to!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		foreach ($toids as $id) {
			if (!isset($people[$id])) {
				$response['message'] = 'User '.$id.' not found!';
				break;
			}
			if ($usersendpms == 1) {
				if (($people[$id]->gid <> 1) && ($people[$id]->gid <> 2)) {
					$response['message'] = 'You are not allowed to send personal message to '.$people[$id]->firstname.' '.$people[$id]->lastname;
					break;
				}
			}
			$data['toid'][] = $people[$id];
		}
		unset($people);

		if ($response['message'] != '') {
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		if ($data['replyto'] > 0) {
			if (count($data['toid']) > 1) {//you cannot reply to multiple users!
				$response['message'] = 'You cannot reply to multiple users!';
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit;
			}
			$row = new messagesDbTable();
			if (!$row->load($data['replyto'])) {
				$response['message'] = 'Original thread '.$data['replyto'].' not found!';
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit;
			}
			$ok = false;
			if (($row->fromid == $myuid) && ($row->toid == $data['toid'][0]->uid)) {
				$ok = true;
			} else if (($row->fromid == $data['toid'][0]->uid) && ($row->toid == $myuid)) {
				$ok = true;
			}
			if (!$ok) {
				$response['message'] = $eLang->get('ERROR').'! In this thread different people talk.';
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit;
			}

			//If the thread has been deleted by either the original sender or the recipient create a new thread
			if (($row->delbyfrom == 1) || ($row->delbyto == 1)) { $data['replyto'] = 0; }
			unset($row);
		}

		$numrecip = 0;

		$row = new messagesDbTable();
		$row->fromid = $data['fromid'];
		$row->fromname = $data['fromname'];
		$row->msgtype = 'info';
		$row->message = $data['message'];
		$row->read = 0;
		$row->replyto = $data['replyto'];
		$row->delbyfrom = 0;
		$row->delbyto = 0;

		foreach ($data['toid'] as $recipient) {
			$row->toid = $recipient->uid;
			$row->toname = $recipient->firstname.' '.$recipient->lastname;
			$ok = $row->insert();
			$row->forceNew(true);
			if ($ok) { $numrecip++; }
		}
		unset($row);

		if ($numrecip == 0) {
			$response['message'] = $eLang->get('ERROR').'! Could not save message.';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		if ($numrecip == count($data['toid'])) {
			$parsed = parse_url($elxis->getConfig('URL'));
			$domain = preg_replace('@^(www\.)@i', '', $parsed['host']);
			$subject = $eLang->get('HAVENEW_MESSAGE');
			if ($numrecip == 1) {
				foreach ($data['toid'] as $recipient) {
					$txt = $eLang->get('HI').' '.$recipient->firstname.' '.$recipient->lastname."\r\n";
					break;
				}
			} else {
				$txt = $eLang->get('HI')."\r\n";
			}
			$txt .= sprintf($eLang->get('USER_SENDYOU_AT'), $elxis->user()->firstname.' '.$elxis->user()->lastname, $domain)."\r\n\r\n";
			$txt .= $data['message']."\r\n\r\n";
			$txt .= $eLang->get('VISIT_PAGE_REPLY')."\r\n";
			$txt .= $elxis->makeURL('user:pms/', '', true)."\r\n\r\n\r\n";
			$txt .= $eLang->get('REGARDS')."\r\n";
			$txt .= $elxis->getConfig('SITENAME')."\r\n";
			$txt .= $elxis->getConfig('URL')."\n\n\n\n";
			$txt .= "_______________________________________________________________\r\n";
			$txt .= $eLang->get('NOREPLYMSGINFO');

			$to = array();
			foreach ($data['toid'] as $recipient) {
				$to[] = $recipient->email.','.$recipient->firstname.' '.$recipient->lastname;
			}
			$elxis->sendmail($subject, $txt, '', null, 'plain', $to);
		}

		$response['success'] = 1;
		if ($numrecip == 1) {
			$response['message'] = $eLang->get('MSG_SENT_RECIPIENT');
		} else {
			$response['message'] = sprintf($eLang->get('MSG_SENT_RECIPIENTS'), $numrecip);
		}

		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/**************************/
	/* DELETE MESSAGE THREAD */
	/**************************/
	public function deletethread() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$response = array('success' => 0, 'message' => '');

		$uid = (int)$elxis->user()->uid;
		if ($uid < 1) {
			$response['message'] = $eLang->get('NOTALLOWACTION');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
		if ($id < 1) {
			$response['message'] = 'No private message selected!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$row = new messagesDbTable();
		if (!$row->load($id)) {
			$response['message'] = 'The message with ID '.$id.' was not found!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		if ($row->fromid == $uid) {
			$is_sender = 1;
		} else if ($row->toid == $uid) {
			$is_sender = 0;
		} else {
			$is_sender = -1;
		}

		if ($is_sender == -1) {
			$response['message'] = $eLang->get('NOTALLOWACCITEM');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		if ($row->replyto > 0) {
			$response['message'] = 'This is not a thread! You can delete message threads, not single messages.';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}
		unset($row);

		$msgObj = $elxis->obj('messages');
		$msgObj->deleteThread($id, $is_sender);
		unset($msgObj);

		$response['success'] = 1;

		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}

}

?>