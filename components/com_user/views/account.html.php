<?php 
/**
* @version		$Id: account.html.php 2377 2020-12-16 19:01:24Z IOS $
* @package		Elxis
* @subpackage	User component
* @copyright	Copyright (c) 2006-2021 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class accountUserView extends userView {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/*************************/
	/* DISPLAY USERS CENTRAL */
	/*************************/
	public function usersCentral($avatar, $bookmarks, $messages_unread, $messages_total, $params) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eDate = eFactory::getDate();
		$eSession = eFactory::getSession();
		$eDoc = eFactory::getDocument();

		$userdata = new stdClass;
		$userdata->uname = $elxis->user()->uname;
		$userdata->uid = $elxis->user()->uid;
		$userdata->gid = $elxis->user()->gid;
		if ($elxis->user()->gid == 7) {
			$userdata->name = $eLang->get('GUEST');
		} else if ($elxis->user()->gid == 6) {
			$userdata->name = $elxis->user()->uname;
		} else {
			$userdata->name = $elxis->user()->firstname.' '.$elxis->user()->lastname;
		}
		if ($avatar == '') {
			$userdata->avatar = $elxis->secureBase().'/components/com_user/images/noavatar.png';
		} else {
			$userdata->avatar = $avatar;
		}
		$userdata->online = ($elxis->user()->uid > 0) ? 1 : -1; //use -1 for non elxis users and guests
		$userdata->totalmessages = $messages_total;
		$userdata->newmessages = $messages_unread;
		$userdata->bookmarks = $bookmarks;
		$userdata->twitter_username = '';

		$token = md5(uniqid(rand(), true));
		$eSession->set('token_fmucp', $token);

		echo '<h1>'.$eLang->get('USERSCENTRAL')."</h1>\n";
		echo '<p>'.$eLang->get('USERSCENTRALDESC')."</p>\n";

		echo '<div class="elx_user_wrapcol">'."\n";

		echo '<div class="elx_user_maincol">'."\n";
		echo '<div class="elx_user_maincolin">'."\n";

		$langs_info = $eLang->getSiteLangs(true);
		if ($langs_info) {
			$lang_current = $eLang->currentLang();
			$action = $elxis->makeURL('user:changelang.html', 'inner.php', true, false);

			echo '<div class="elx5_dlspace" id="elx5_user_languagebox">'."\n";
			echo '<h3>'.$eLang->get('LANGUAGE')."</h3>\n";
			echo '<p>'.$eLang->get('SETPREFLANG')."</p>\n";
			echo '<form name="fmchangetz" action="'.$action.'" method="post" class="elx5_form">'."\n";
			echo '<select name="lang" id="ucplang" class="elx5_select" onchange="this.form.submit()">';
			foreach ($langs_info as $lng => $info) {
				$sel = ($lng == $lang_current) ? ' selected="selected"' : '';
				echo '<option value="'.$info['LANGUAGE'].'"'.$sel.'>'.$info['NAME'].' - '.$info['NAME_ENG'].' ('.$info['LANGUAGE'].'_'.$info['REGION'].')</option>';
			}
			echo "</select>\n";
			echo '<input type="hidden" name="token" value="'.$token.'" /></form>'."\n";
			echo "</div>\n";
		}

		$tz = $eDate->getTimezone();
		$current_daytime = $eDate->formatDate('now', $eLang->get('DATE_FORMAT_12'));
		$zones = timezone_identifiers_list();
		$action = $elxis->makeURL('user:changetz.html', 'inner.php', true, false);

		echo '<div class="elx5_dlspace" id="elx5_user_timezonebox">'."\n";
		echo '<h3>'.$eLang->get('TIMEZONE')."</h3>\n";
		echo '<p>'.$eLang->get('CHATIMELOCAL')."</p>\n";
		echo '<form name="fmchangetz" action="'.$action.'" method="post" class="elx5_form">'."\n";
		echo '<select name="timezone" id="ucptimezone" class="elx5_select" onchange="this.form.submit()">'."\n";
		foreach ($zones as $zone) {
			$sel = ($tz == $zone) ? ' selected="selected"' : '';
			echo '<option value="'.$zone.'"'.$sel.'>'.$zone."</option>\n";
		}
		echo "</select>\n";
		echo '<div class="elx5_tip">'.$current_daytime."</div>\n";
		echo '<input type="hidden" name="token" value="'.$token.'" />'."\n";
		echo '</form>'."\n";
		echo "</div>\n";

		if ($eDoc->countModules('user_maincol') > 0) {
			echo '<div class="user_maincol_mods">'."\n";
			$eDoc->modules('user_maincol');
			echo "</div>\n";
		}

		echo "</div>\n";//.elx_user_maincolin end
		echo "</div>\n";//.elx_user_maincol end

		echo '<div class="elx_user_sidecol">'."\n";
		$this->base_sideProfile($userdata, $elxis, $eLang, $eDoc, 'userscentral', $params);
		echo "</div>\n";//.elx_user_sidecol

		echo '<div class="clear"></div>'."\n";
		echo "</div>\n";
	}


	/**********************************/
	/* DISPLAY USER REGISTRATION FORM */
	/**********************************/
	public function registrationForm($row, $errormsg, $extra_fields, $terms_txt) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$eDoc = eFactory::getDocument();

		$userdata = new stdClass;
		$userdata->uname = '';
		$userdata->uid = 0;
		$userdata->gid = 7;
		$userdata->name = $eLang->get('GUEST');
		$userdata->avatar = $elxis->secureBase().'/components/com_user/images/noavatar.png';
		$userdata->online = -1;
		$userdata->inbox = 0;
		$userdata->outbox = 0;
		$userdata->newmessages = 0;
		$userdata->bookmarks = 0;
		$userdata->twitter_username = '';

		$action = $elxis->makeURL('user:register.html', '', true, false);
		elxisLoader::loadFile('includes/libraries/elxis/form5.class.php');
	
		echo '<h1>'.$eLang->get('REGISTRATION')."</h1>\n";
		echo '<p class="elx5_dspace">'.$eLang->get('REGISTERDESC')."</p>\n";
		if ($errormsg != '') {
			echo '<div class="elx5_error">'.$errormsg."</div>\n";
		}

		echo '<div class="elx_user_wrapcol">'."\n";

		echo '<div class="elx_user_maincol">'."\n";
		echo '<div class="elx_user_maincolin">'."\n";

		$form = new elxis5Form(array('idprefix' => 'reg', 'labelclass' => 'elx5_label', 'sideclass' => 'elx5_labelside'));
		$form->openForm(array('name' => 'fmregister', 'method' => 'post', 'action' => $action, 'id' => 'fmregister'));

		$form->addText('firstname', $row->firstname, $eLang->get('FIRSTNAME'), array('required' => 'required', 'maxlength' => 60));
		$form->addText('lastname', $row->lastname, $eLang->get('LASTNAME'), array('required' => 'required', 'maxlength' => 60));
		$form->addEmail('email', $row->email, $eLang->get('EMAIL'), array('required' => 'required'));

		$form->addText('uname', $row->uname, $eLang->get('USERNAME'), array(
			'required' => 'required', 'tip' => $eLang->get('MINCHARDIGSYM'), 'placeholder' => $eLang->get('USERNAME'), 'dir' => 'ltr', 'autocomplete' => 'off', 
			'pattern' => '[A-Za-z0-9_\-]{4,32}', 'title' => $eLang->get('MINCHARDIGSYM')
			)
		);
		$form->addPassword('pword', '', $eLang->get('PASSWORD'), 
			array(
				'required' => 'required', 'maxlength' => 60, 'tip' => $eLang->get('MINLENGTH6'), 'placeholder' => $eLang->get('PASSWORD'), 'autocomplete' => 'off', 
				'pattern' => '[A-Za-z0-9_!@\-]{6,}', 'title' => $eLang->get('MINLENGTH6').'. Acceptable characters are A-Z a-z 0-9 _ - ! @', 'password_meter' => 1
			)
		);
		$form->addPassword('pword2', '', $eLang->get('PASSWORD_AGAIN'), array('required' => 'required', 'autocomplete' => 'off', 'maxlength' => 60, 'match' => 'regpword'));

		if ($extra_fields) {
			if (in_array('address', $extra_fields)) {
				$form->addText('address', $row->address, $eLang->get('ADDRESS'), array('required' => 'required', 'maxlength' => 120));
			}
			if (in_array('postalcode', $extra_fields)) {
				$form->addText('postalcode', $row->postalcode, $eLang->get('POSTAL_CODE'), array('required' => 'required'));
			}
			if (in_array('city', $extra_fields)) {
				$form->addText('city', $row->city, $eLang->get('CITY'), array('required' => 'required', 'maxlength' => 120));
			}
			if (in_array('country', $extra_fields)) {
				$val = (trim($row->country) == '') ? $eLang->getinfo('REGION') : $row->country;
				$form->addCountry('country', $eLang->get('COUNTRY'), $val);
			}
			if (in_array('phone', $extra_fields)) {
				$form->addTel('phone', $row->phone, $eLang->get('TELEPHONE'), array('required' => 'required', 'maxlength' => 40, 'pattern' => '^[0-9\+\-\s]{6,}$'));
			}
			if (in_array('mobile', $extra_fields)) {
				$form->addTel('mobile', $row->mobile, $eLang->get('MOBILE'), array('required' => 'required', 'maxlength' => 40, 'pattern' => '^[0-9\+\-\s]{6,}$'));
			}
		}

		if ($elxis->getConfig('CAPTCHA') != 'NONE') {
			if ($elxis->getConfig('CAPTCHA') == 'MATH') {
				$form->addCaptcha('seccode', '', array('autocomplete' => 'off'));
			} else {
				$form->addNoRobot();
			}
		}

		if ($terms_txt != '') {
			$html = '<p>'.$eLang->get('REGAGREE_TERMS_CONDITIONS')."</p>\n";
			$html .= '<pre class="elx5_user_terms">'.$terms_txt."</pre>\n";
			$html .= '<div class="elx5_formrow elx5_dlspace">';
			$html .= '<label class="elx5_checkboxwrap">'.$eLang->get('IAGREE_TERMS_CONDS_PRIVACY').'<input type="checkbox" name="agreeterms[]" id="regagreeterms1" class="elx5_checkbox" value="1" required="required" /><span class="elx5_checkbox_checkmark"></span></label>';
			$html .= "</div>\n";
			$form->addHTML($html);
		}
		$form->addToken('fmregister');
		$form->addHTML('<div class="elx5_dspace">');
		$form->addButton('sbmreg', $eLang->get('REGISTER'), 'submit');
		$form->addHTML('</div>');
		$form->closeForm();
		unset($form);

		if ($eDoc->countModules('user_maincol') > 0) {
			echo '<div class="user_maincol_mods">'."\n";
			$eDoc->modules('user_maincol');
			echo "</div>\n";
		}

		echo "</div>\n";//.elx_user_maincolin end
		echo "</div>\n";//.elx_user_maincol end

		echo '<div class="elx_user_sidecol">'."\n";
		$this->base_sideProfile($userdata, $elxis, $eLang, $eDoc, 'register');
		echo "</div>\n";//.elx_user_sidecol

		echo '<div class="clear"></div>'."\n";
		echo "</div>\n";
	}


	/****************************************/
	/* DISPLAY REGISTRATION SUCCESS MESSAGE */
	/****************************************/
	public function registrationSuccess($row, $msg) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$eDoc = eFactory::getDocument();

		$userdata = new stdClass;
		$userdata->uname = '';
		$userdata->uid = 0;
		$userdata->gid = 7;
		$userdata->name = $eLang->get('GUEST');
		$userdata->avatar = $elxis->secureBase().'/components/com_user/images/noavatar.png';
		$userdata->online = -1;
		$userdata->inbox = 0;
		$userdata->outbox = 0;
		$userdata->newmessages = 0;
		$userdata->bookmarks = 0;
		$userdata->twitter_username = '';

		$fplink = $elxis->makeURL('');

		echo '<h1>'.$eLang->get('SUCCESSREG')."</h1>\n";
		echo '<p class="elx5_success">'.$eLang->get('REGCOMPLSUCC')."</p>\n";

		echo '<div class="elx_user_wrapcol">'."\n";

		echo '<div class="elx_user_maincol">'."\n";
		echo '<div class="elx_user_maincolin">'."\n";
		echo '<p>'.$msg."</p>\n";
		echo '<div class="elx5_dspace">'."\n";
		echo $eLang->get('THANKYOU')."<br />\n";
		echo '<a href="'.$fplink.'" title="'.$elxis->getConfig('SITENAME').'">'.$elxis->getConfig('SITENAME')."</a>\n";
		echo "</div>\n";

		if ($eDoc->countModules('user_maincol') > 0) {
			echo '<div class="user_maincol_mods">'."\n";
			$eDoc->modules('user_maincol');
			echo "</div>\n";
		}

		echo "</div>\n";//.elx_user_maincolin end
		echo "</div>\n";//.elx_user_maincol end

		echo '<div class="elx_user_sidecol">'."\n";
		$this->base_sideProfile($userdata, $elxis, $eLang, $eDoc, 'registersuccess');
		echo "</div>\n";//.elx_user_sidecol

		echo '<div class="clear"></div>'."\n";
		echo "</div>\n";
	}


	/**************************************/
	/* DISPLAY ACTIVATION SUCCESS MESSAGE */
	/**************************************/
	public function activationSuccess($uname) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$eDoc = eFactory::getDocument();

		$userdata = new stdClass;
		$userdata->uname = '';
		$userdata->uid = 0;
		$userdata->gid = 7;
		$userdata->name = $eLang->get('GUEST');
		$userdata->avatar = $elxis->secureBase().'/components/com_user/images/noavatar.png';
		$userdata->online = -1;
		$userdata->inbox = 0;
		$userdata->outbox = 0;
		$userdata->newmessages = 0;
		$userdata->bookmarks = 0;
		$userdata->twitter_username = '';

		$fplink = $elxis->makeURL();
		$login_link = $elxis->makeURL('user:login/', '', true);

		echo '<h1>'.$eLang->get('ACCOUNTACT')."</h1>\n";
		echo '<p class="elx5_success">'.$eLang->get('YACCACTSUCC')."</p>\n";

		echo '<div class="elx_user_wrapcol">'."\n";

		echo '<div class="elx_user_maincol">'."\n";
		echo '<div class="elx_user_maincolin">'."\n";

		echo '<p>'.sprintf($eLang->get('YOUMAYLOGIN'), '<strong>'.$uname.'</strong>')."<br />\n";
		echo '<a href="'.$login_link.'" title="'.$eLang->get('LOGIN').'" rel="nofollow">'.$eLang->get('CLICKTOLOGIN')."</a>\n";
		echo "</p>\n";

		echo '<div class="elx5_dspace">'."\n";
		echo $eLang->get('THANKYOU')."<br />\n";
		echo '<a href="'.$fplink.'" title="'.$elxis->getConfig('SITENAME').'">'.$elxis->getConfig('SITENAME')."</a>\n";
		echo "</div>\n";
		if ($eDoc->countModules('user_maincol') > 0) {
			echo '<div class="user_maincol_mods">'."\n";
			$eDoc->modules('user_maincol');
			echo "</div>\n";
		}
		echo "</div>\n";//.elx_user_maincolin end
		echo "</div>\n";//.elx_user_maincol end

		echo '<div class="elx_user_sidecol">'."\n";
		$this->base_sideProfile($userdata, $elxis, $eLang, $eDoc, 'activationsuccess');
		echo "</div>\n";//.elx_user_sidecol

		echo '<div class="clear"></div>'."\n";
		echo "</div>\n";
	}


	/**********************************/
	/* DISPLAY PASSWORD RECOVERY FORM */
	/**********************************/
	public function recoverForm($row, $errormsg='') {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$eDoc = eFactory::getDocument();

		elxisLoader::loadFile('includes/libraries/elxis/form5.class.php');

		$userdata = new stdClass;
		$userdata->uname = '';
		$userdata->uid = 0;
		$userdata->gid = 7;
		$userdata->name = $eLang->get('GUEST');
		$userdata->avatar = $elxis->secureBase().'/components/com_user/images/noavatar.png';
		$userdata->online = -1;
		$userdata->inbox = 0;
		$userdata->outbox = 0;
		$userdata->newmessages = 0;
		$userdata->bookmarks = 0;
		$userdata->twitter_username = '';

		$action = $elxis->makeURL('user:recover-pwd.html', '', true, false);

		echo '<h1>'.$eLang->get('RECOVERPASS')."</h1>\n";
		echo '<p>'.$eLang->get('PASSRECOVDESC')."</p>\n";
		if ($errormsg != '') {
			echo '<div class="elx5_error">'.$errormsg."</div>\n";
		}

		echo '<div class="elx_user_wrapcol">'."\n";

		echo '<div class="elx_user_maincol">'."\n";
		echo '<div class="elx_user_maincolin">'."\n";

		$form = new elxis5Form(array('idprefix' => 'rec', 'labelclass' => 'elx5_labelblock', 'sideclass' => 'elx5_zero'));
		$form->openForm(array('name' => 'fmrecover', 'method' => 'post', 'action' => $action, 'id' => 'fmrecover'));
		$form->addText('uname', $row->uname, $eLang->get('USERNAME'), array('required' => 'required', 'dir' => 'ltr', 'autocomplete' => 'off'));
		$form->addEmail('email', $row->email, $eLang->get('EMAIL'), array('required' => 'required', 'autocomplete' => 'off'));
		if ($elxis->getConfig('CAPTCHA') != 'NONE') {
			if ($elxis->getConfig('CAPTCHA') == 'MATH') {
				$form->addCaptcha('seccode', '', array('autocomplete' => 'off'));
			} else {
				$form->addNoRobot();
			}
		}
		$form->addToken('fmrecover');
		$form->addHTML('<div class="elx5_dspace">');
		$form->addButton('sbmrec', $eLang->get('SUBMIT'), 'submit');
		$form->addHTML('</div>');
		$form->closeForm();
		unset($form);

		if ($eDoc->countModules('user_maincol') > 0) {
			echo '<div class="user_maincol_mods">'."\n";
			$eDoc->modules('user_maincol');
			echo "</div>\n";
		}

		echo "</div>\n";//.elx_user_maincolin end
		echo "</div>\n";//.elx_user_maincol end

		echo '<div class="elx_user_sidecol">'."\n";
		$this->base_sideProfile($userdata, $elxis, $eLang, $eDoc, 'recover');
		echo "</div>\n";//.elx_user_sidecol

		echo '<div class="clear"></div>'."\n";
		echo "</div>\n";
	}


	/*********************************************************/
	/* DISPLAY ACTIVATION SUCCESS OR PASSWORD CHANGE MESSAGE */
	/*********************************************************/
	public function recoverSuccess($newpass='') {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$eDoc = eFactory::getDocument();

		$userdata = new stdClass;
		$userdata->uname = '';
		$userdata->uid = 0;
		$userdata->gid = 7;
		$userdata->name = $eLang->get('GUEST');
		$userdata->avatar = $elxis->secureBase().'/components/com_user/images/noavatar.png';
		$userdata->online = -1;
		$userdata->inbox = 0;
		$userdata->outbox = 0;
		$userdata->newmessages = 0;
		$userdata->bookmarks = 0;
		$userdata->twitter_username = '';

		$fplink = $elxis->makeURL();

		echo '<h1>'.$eLang->get('RECOVERPASS')."</h1>\n";

		echo '<div class="elx_user_wrapcol">'."\n";

		echo '<div class="elx_user_maincol">'."\n";
		echo '<div class="elx_user_maincolin">'."\n";

		if ($newpass != '') {
			$msg = sprintf($eLang->get('PASS_CHANGEDTO'), '<strong>'.$newpass.'</strong>');
			echo '<p class="elx5_success">'.$msg."</p>\n";
		} else {
			echo '<p class="elx5_success">'.$eLang->get('LINKRESPASS_SENT')."</p>\n";
		}
		echo '<div class="elx5_dspace">'."\n";
		echo $eLang->get('THANKYOU')."<br />\n";
		echo '<a href="'.$fplink.'" title="'.$elxis->getConfig('SITENAME').'">'.$elxis->getConfig('SITENAME')."</a>\n";
		echo "</div>\n";
		if ($eDoc->countModules('user_maincol') > 0) {
			echo '<div class="user_maincol_mods">'."\n";
			$eDoc->modules('user_maincol');
			echo "</div>\n";
		}
		echo "</div>\n";//.elx_user_maincolin end
		echo "</div>\n";//.elx_user_maincol end

		echo '<div class="elx_user_sidecol">'."\n";
		$this->base_sideProfile($userdata, $elxis, $eLang, $eDoc, 'recoversuccess');
		echo "</div>\n";//.elx_user_sidecol

		echo '<div class="clear"></div>'."\n";
		echo "</div>\n";
	}


	/**********************/
	/* DISPLAY LOGIN FORM */
	/**********************/
	public function loginForm($auth, $auths, $eAuth, $errormsg='') {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$eDoc = eFactory::getDocument();

		if (ELXIS_INNER == 1) {
			echo '<div class="elx5_mpad">'."\n";
			$eAuth->loginForm();
			$this->showProviders($auths, $auth, $elxis, $eLang);
			echo "</div>\n";
			return;
		}

		$title = ($auth == 'elxis') ? $eLang->get('LOGIN') : sprintf($eLang->get('LOGIN_WITH'), $auths[$auth]['title']);
		echo '<h1>'.$title."</h1>\n";

		$userdata = new stdClass;
		$userdata->uname = '';
		$userdata->uid = 0;
		$userdata->gid = 7;
		$userdata->name = $eLang->get('GUEST');
		$userdata->avatar = $elxis->secureBase().'/components/com_user/images/noavatar.png';
		$userdata->online = -1;
		$userdata->inbox = 0;
		$userdata->outbox = 0;
		$userdata->newmessages = 0;
		$userdata->bookmarks = 0;
		$userdata->twitter_username = '';

		echo '<div class="elx_user_wrapcol">'."\n";
		echo '<div class="elx_user_maincol">'."\n";
		echo '<div class="elx_user_maincolin">'."\n";
		$eAuth->loginForm();
		$this->showProviders($auths, $auth, $elxis, $eLang);
		if ($eDoc->countModules('user_maincol') > 0) {
			echo '<div class="user_maincol_mods">'."\n";
			$eDoc->modules('user_maincol');
			echo "</div>\n";
		}
		echo "</div>\n";//.elx_user_maincolin end
		echo "</div>\n";//.elx_user_maincol end

		echo '<div class="elx_user_sidecol">'."\n";
		$this->base_sideProfile($userdata, $elxis, $eLang, $eDoc, 'login');
		echo "</div>\n";//.elx_user_sidecol

		echo '<div class="clear"></div>'."\n";
		echo "</div>\n";
	}


	/*********************************************/
	/* DISPLAY EXTERNAL AUTHENTICATION PROVIDERS */
	/*********************************************/
	private function showProviders($auths, $curauth, $elxis, $eLang) {
		if (count($auths) < 2) { return; }

		$file = (ELXIS_INNER == 1) ? 'inner.php' : 'index.php';
		echo '<div id="elx_other_auth_methods" class="elx5_vlspace">'."\n";
		echo '<h3>'.$eLang->get('OTHER_LOGIN_METHODS')."</h3>\n";
		echo '<p>'.$eLang->get('LOGIN_EXACC_PROVIDERS')."</p>\n";
		echo '<ul class="elx5_user_authlist">'."\n";
		foreach ($auths as $auth => $data) {
			if ($auth == $curauth) { continue; }
			$link = $elxis->makeURL('user:login/'.$auth.'.html', $file, true);
			$title = sprintf($eLang->get('LOGIN_WITH'), $data['title']);
			echo '<li><a href="'.$link.'" title="'.$title.'" rel="nofollow">'. $data['title'].'</a></li>';
		}
		echo "</ul>\n";
		echo "</div>\n";
	}


	/***********************************/
	/* CLOSE OPENED WINDOW AFTER LOGIN */
	/***********************************/
	public function closeAfterLogin($return) {
		$eLang = eFactory::getLang();
		$eDoc = eFactory::getDocument();

		if ($return == '') {
			$jscode = 'window.opener.location.reload(); window.close();'; 
		} else {
			$jscode = 'window.opener.location.href=\''.$return.'\'; window.close();'; 
		}

		$js = 'if (window.addEventListener) {
			window.addEventListener(\'load\', function() { '.$jscode.' }, false);
		} else if (window.attachEvent) {
			window.attachEvent(\'onload\', function() { '.$jscode.' });
		}
		function reloadAndClose() { '.$jscode.' }';
		$eDoc->addScript($js);

		$eDoc->setTitle($eLang->get('LOGIN').' - Success');

		echo '<div class="elx5_success">'.$eLang->get('SUCC_LOGGED')."</div>\n";
		echo '<div class="elx5_vspace elx5_center">'."\n";
		echo '<a href="javascript:void(null);" onclick="reloadAndClose();">'.$eLang->get('CLOSEWIN_IFNOTAUTO')."</a>";
		echo "</div>\n";
	}


	/***********************************************/
	/* DISPLAY INTERNAL LOGIN/LOGOUT JSON RESPONSE */
	/***********************************************/
	public function internalResponse($response) {
		$encoded = json_encode($response);

		if (ob_get_length() > 0) { ob_end_clean(); }
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').'GMT');
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header('Content-type: application/json; charset=utf-8');
		echo $encoded;
		exit;
	}

}

?>