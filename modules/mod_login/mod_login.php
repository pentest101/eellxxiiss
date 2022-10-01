<?php 
/**
* @version		$Id: mod_login.php 2197 2019-04-08 17:54:27Z IOS $
* @package		Elxis
* @subpackage	Module login
* @copyright	Copyright (c) 2006-2019 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


if (!class_exists('moduleLogin', false)) {
	class moduleLogin {

		private $ext_auths = 1;
		private $auth_help = 1;
		private $rememberme = 0;
		private $labels = 2;
		private $orientation = 0;
		private $regireco = 1;
		private $ext_help = 1;
		private $pretext = '';
		private $posttext = '';
		private $login_redir = 2;
		private $displayname = 0;
		private $avatar = 1;
		private $gravatar = 0;
		private $usergroup = 0;
		private $timeonline = 0;
		private $authmethod = 0;
		private $login_redir_uri = 'user:/';
		private $logout_redir_uri = '';
		private $rand = 1;


		/*********************/
		/* MAGIC CONSTRUCTOR */
		/*********************/
		public function __construct($params) {
			$this->ext_auths = (int)$params->get('ext_auths', 1);
			$this->auth_help = (int)$params->get('auth_help', 1);
			$this->rememberme = (int)$params->get('rememberme', 0);
			$this->labels = (int)$params->get('labels', 2);
			$this->orientation = (int)$params->get('orientation', 0);
			$this->regireco = (int)$params->get('regireco', 1);
			$this->pretext = eUTF::trim($params->get('pretext', ''));
			$this->posttext = eUTF::trim($params->get('posttext', ''));
			$this->login_redir = (int)$params->get('login_redir', 2);
			$this->displayname = (int)$params->get('displayname', 0);
			$this->avatar = (int)$params->get('avatar', 1);
			$this->gravatar = (int)$params->get('gravatar', 0);
			$this->usergroup = (int)$params->get('usergroup', 0);
			$this->timeonline = (int)$params->get('timeonline', 0);
			$this->authmethod = (int)$params->get('authmethod', 0);
			$this->login_redir_uri = trim($params->get('login_redir_uri', 'user:/'));
			if ($this->login_redir_uri == '') { $this->login_redir_uri = 'user:/'; }
			$this->logout_redir_uri = trim($params->get('logout_redir_uri', ''));
			$this->rand = rand(1, 999);

			if ($this->orientation == 1) {
				if ($this->labels == 1) { $this->labels == 2; }
				$this->rememberme = 0;
				$this->auth_help = 0;
				$this->usergroup = 0;
				$this->timeonline = 0;
				$this->authmethod = 0;
			}
		}


		/******************/
		/* MODULE EXECUTE */
		/******************/
		public function run() {
			if (eFactory::getElxis()->user()->gid <> 7) {
				$this->logoutForm();
			} else {
				$this->loginForm();
			}
		}


		/**********************/
		/* DISPLAY LOGIN FORM */
		/**********************/
		private function loginForm() {
			if (defined('ELX_MOD_LOGIN')) {
				$this->showError('Module login can be displayed only 1 time!');
				return;
			}

			elxisLoader::loadInit('libraries:elxis:auth.class', 'eAuth', 'elxisAuth');
			$eAuth = eRegistry::get('eAuth');
			if ($eAuth->getError() != '') {
				$this->showError($eAuth->getError());
				return;
			}

			$auths = $eAuth->getAuths();
			if (!$auths) {
				$this->showError('There are no public Authentication methods!');
				return;
			}

			echo '<div class="elx5_modlogin_wrapper" id="modlogin_wrapper'.$this->rand.'">'."\n";
			if ($this->pretext != '') {
				echo '<div class="elx5_modlogin_pretext">'.$this->pretext."</div>\n";
			}
			$this->elxisLogin($auths);
			if ($this->orientation == 0) {
				$this->showProviders($auths);
			}
			if ($this->posttext != '') {
				echo '<div class="elx5_modlogin_posttext">'.$this->posttext."</div>\n";
			}
			echo "</div>\n";
		}


		/********************/
		/* ELXIS LOGIN FORM */
		/********************/
		private function elxisLogin($auths) {
			$elxis = eFactory::getElxis();
			$eDoc = eFactory::getDocument();
			$eLang = eFactory::getLang();

			if (!isset($auths['elxis'])) { return; }

			$token = trim(eFactory::getSession()->get('token_loginform')); //check if another login module has already set the token
			if ($token == '') {
				$token = md5(uniqid(rand(), true));
				eFactory::getSession()->set('token_loginform', $token);
			}
			
			if ($this->login_redir == 0) {
				$return = base64_encode($elxis->makeURL('user:/'));
			} else if ($this->login_redir == 3) {
				if ((stripos($this->login_redir_uri, 'http://') === 0) || (stripos($this->login_redir_uri, 'https://') === 0)) {
					$return = base64_encode($this->login_redir_uri);
				} else {
					$return = base64_encode($elxis->makeURL($this->login_redir_uri));
				}
			} else {
				$return = base64_encode(eFactory::getURI()->getRealUriString());
			}

			if ($this->login_redir == 2) { //AJAX
				$eDoc->addScriptLink($elxis->secureBase().'/modules/mod_login/mlogin.js');
				$action = $elxis->makeURL('user:ilogin', 'inner.php', true, false);
				echo '<form name="loginform" id="loginform'.$this->rand.'" method="post" class="elx5_form elx5_modlogin_form" action="'.$action.'" onsubmit="modlogin('.$this->rand.', '.$this->avatar.', '.$this->displayname.', \''.$eLang->getinfo('DIR').'\'); return false;">'."\n";
			} else {
				$action = $elxis->makeURL('user:login/elxis.html', '', true, false);
				echo '<form name="loginform" id="loginform'.$this->rand.'" method="post" class="elx5_form elx5_modlogin_form" action="'.$action.'">'."\n";
			}
			echo '<input type="hidden" name="auth_method" value="elxis" />'."\n";
			echo '<input type="hidden" name="return" value="'.$return.'" />'."\n";
			echo '<input type="hidden" name="modtoken" id="modtoken'.$this->rand.'" value="'.$token.'" />'."\n";

			if ($this->orientation == 0) {//vertical
				switch ($this->labels) {
					case 1: $labelclass = 'elx5_labelsmall'; $sideclass = 'elx5_labelsmallside'; break;//side
					case 2: $labelclass = 'elx5_labelblock'; $sideclass = 'elx5_zero'; break;//top
					case 0: default: $labelclass = ''; $sideclass = ''; break;
				}
				echo '<div class="elx5_formrow">'."\n";
				if ($this->labels > 0) {
					echo '<label class="'.$labelclass.'" for="uname'.$this->rand.'">'.$eLang->get('USERNAME')."</label>\n";
					echo '<div class="'.$sideclass.'">'."\n";
				}
				echo '<input type="text" name="uname" id="uname'.$this->rand.'" dir="ltr" class="elx5_text elx5_modlogin_uname" placeholder="'.$eLang->get('USERNAME').'" title="'.$eLang->get('USERNAME').'" required="required" value="" autocomplete="off" />'."\n";
				if ($this->labels > 0) { echo "</div>\n"; }
				echo "</div>\n";

				echo '<div class="elx5_formrow">'."\n";
				if ($this->labels > 0) {
					echo '<label class="'.$labelclass.'" for="pword'.$this->rand.'">'.$eLang->get('PASSWORD_SHORT')."</label>\n";
					echo '<div class="'.$sideclass.'">'."\n";
				}
				echo '<input type="password" name="pword" id="pword'.$this->rand.'" dir="ltr" class="elx5_text elx5_modlogin_pword" placeholder="'.$eLang->get('PASSWORD').'" title="'.$eLang->get('PASSWORD').'" required="required" value="" autocomplete="off" />'."\n";
				if ($this->labels > 0) { echo "</div>\n"; }
				echo "</div>\n";

				if ($this->rememberme == 1) {
					echo '<div class="elx5_formrow">'."\n";
					if ($this->labels == 1) {
						echo '<label class="'.$labelclass.'">&#160;</label>'."\n";
						echo '<div class="'.$sideclass.'">'."\n";
					}
					echo '<label class="elx5_checkboxwrap">'.$eLang->get('REMEMBER_ME').'<input type="checkbox" name="remember" id="remember'.$this->rand.'" class="elx5_checkbox" value="1" />';
					echo '<span class="elx5_checkbox_checkmark"></span></label>'."\n";
					if ($this->labels == 1) { echo "</div>\n"; }
					echo "</div>\n";
				} else {
					echo '<input type="hidden" name="remember" id="remember'.$this->rand.'" value="1" />'."\n";
				}
				echo '<div class="elx5_formrow">'."\n";
				if ($this->labels == 1) {
					echo '<label class="'.$labelclass.'" for="sublogin'.$this->rand.'">&#160;</label>'."\n";
					echo '<div class="'.$sideclass.'">'."\n";
				}
				echo '<button type="submit" name="sublogin" id="sublogin'.$this->rand.'" class="elx5_btn">'.$eLang->get('LOGIN').'</button>'."\n";
				if ($this->labels == 1) { echo "</div>\n"; }
				echo "</div>\n";
			} else {//horizontal
				echo '<div class="elx5_3colwrap">'."\n";
				echo '<div class="elx5_3colbox">'."\n";
				if ($this->labels > 0) {
					echo '<div class="elx5_formrow">'."\n";
					echo '<label class="elx5_labelblock" for="uname'.$this->rand.'">'.$eLang->get('USERNAME')."</label>\n";
				}
				echo '<div class="elx5_zero"><input type="text" name="uname" id="uname'.$this->rand.'" dir="ltr" class="elx5_text elx5_modlogin_uname" placeholder="'.$eLang->get('USERNAME').'" title="'.$eLang->get('USERNAME').'" required="required" value="" autocomplete="off" /></div>'."\n";
				if ($this->labels > 0) { echo "</div>\n"; }
				echo "</div>\n";//elx5_3colbox
				echo '<div class="elx5_3colbox">'."\n";
				if ($this->labels > 0) {
					echo '<div class="elx5_formrow">'."\n";
					echo '<label class="elx5_labelblock" for="pword'.$this->rand.'">'.$eLang->get('PASSWORD_SHORT')."</label>\n";
				}
				echo '<div class="elx5_zero"><input type="password" name="pword" id="pword'.$this->rand.'" dir="ltr" class="elx5_text elx5_modlogin_pword" placeholder="'.$eLang->get('PASSWORD').'" title="'.$eLang->get('PASSWORD').'" required="required" value="" autocomplete="off" /></div>'."\n";
				if ($this->labels > 0) { echo "</div>\n"; }
				echo "</div>\n";//elx5_3colbox
				echo '<div class="elx5_3colbox">'."\n";
				if ($this->labels > 0) {
					echo '<div class="elx5_formrow">'."\n";
					echo '<label class="elx5_labelblock" for="sublogin'.$this->rand.'">&#160;</label>'."\n";
				}
				echo '<div class="elx5_zero"><button type="submit" name="sublogin" id="sublogin'.$this->rand.'" class="elx5_btn elx5_modlogin_btn">'.$eLang->get('LOGIN').'</button></div>'."\n";
				if ($this->labels > 0) { echo "</div>\n"; }
				echo "</div>\n";//elx5_3colbox
				echo "</div>\n";//elx5_3colwrap

				if ($this->rememberme == 1) {
					echo '<div class="elx5_dsspace">'."\n";
					echo '<label class="elx5_checkboxwrap">'.$eLang->get('REMEMBER_ME').'<input type="checkbox" name="remember" id="remember'.$this->rand.'" class="elx5_checkbox" value="1" />';
					echo '<span class="elx5_checkbox_checkmark"></span></label>'."\n";
					echo "</div>\n";
				} else {
					echo '<input type="hidden" name="remember" id="remember'.$this->rand.'" value="1" />'."\n";
				}
			}
			echo "</form>\n";

			if ($this->orientation == 1) {//horizontal
				$this->showProviders($auths);
			}

			if ($this->regireco == 1) {
				if (($elxis->getConfig('REGISTRATION') == 1) || ($elxis->getConfig('PASS_RECOVER') == 1)) {
					echo '<div class="elx5_modlogin_linksbox">'."\n";
					if ($elxis->getConfig('REGISTRATION') == 1) {
						$link = $elxis->makeURL('user:register.html', '', true, false);
						echo '<a href="'.$link.'" title="'.$eLang->get('CREATE_ACCOUNT').'" rel="nofollow">'.$eLang->get('CREATE_ACCOUNT')."</a> \n";
					}
					if ($elxis->getConfig('PASS_RECOVER') == 1) {
						$link = $elxis->makeURL('user:recover-pwd.html', '', true, false);
						echo '<a href="'.$link.'" title="'.$eLang->get('PASS_RECOVERY').'" rel="nofollow">'.$eLang->get('PASS_RECOVERY')."</a>\n";
					}
					echo "</div>\n";
				}
			}

			if ($this->login_redir == 2) {
				echo '<div id="mlogin_logout'.$this->rand.'" class="elx5_invisible">'.$eLang->get('LOGOUT')."</div>\n";
				echo '<div id="mlogin_profile'.$this->rand.'" class="elx5_invisible" dir="ltr">'.$elxis->makeURL('user:members/myprofile.html')."</div>\n";
			}

			$logout_redirect = '';
			if ($this->logout_redir_uri != '') {
				if ((stripos($this->logout_redir_uri, 'http://') === 0) || (stripos($this->logout_redir_uri, 'https://') === 0)) {
					$logout_redirect = $this->logout_redir_uri;
				} else {
					$logout_redirect = $elxis->makeURL($this->logout_redir_uri);
				}
			}
			echo '<div id="mlogout_redir'.$this->rand.'" class="elx5_invisible" dir="ltr">'.$logout_redirect."</div>\n";

			define('ELX_MOD_LOGIN', 1);
		}


		/*********************************************/
		/* DISPLAY EXTERNAL AUTHENTICATION PROVIDERS */
		/*********************************************/
		private function showProviders($auths) {
			$elxis = eFactory::getElxis();
			$eLang = eFactory::getLang();

			if ($this->ext_auths == 0) { return; }
			if (!$auths) { return; }
			$n = 0;
			foreach ($auths as $auth => $data) {
				if ($auth != 'elxis') { $n++; }
			}
			if ($n == 0) { return; }

			echo '<div class="elx5_modlogin_authbox">'."\n";
			if ($this->auth_help == 1) {
				echo '<p>'.$eLang->get('LOGIN_EXACC_PROVIDERS')."</p>\n";
			}
			echo '<ul class="elx5_modlogin_authlist">'."\n";
			foreach ($auths as $auth => $data) {
				if ($auth == 'elxis') { continue; }
				$link = $elxis->makeURL('user:login/'.$auth.'.html', 'inner.php', true);
				$title = sprintf($eLang->get('LOGIN_WITH'), $data['title']);
				echo '<li><a href="javascript:void(null);" title="'.$title.'" onclick="elxPopup(\''.$link.'\', 700, 550, \'Login with '.$auth.'\')" rel="nofollow">'.$data['title']."</a></li>\n";
			}
			echo "</ul>\n";
			echo "</div>\n";
		}


		/***********************/
		/* DISPLAY LOGOUT FORM */
		/***********************/
		private function logoutForm() {
			$elxis = eFactory::getElxis();
			$eDoc = eFactory::getDocument();
			$eLang = eFactory::getLang();

			if ($elxis->user()->gid != 6) {
				switch ($this->displayname) {
					case 1: $name = $elxis->user()->firstname.' '.$elxis->user()->lastname; break;
					case 2: $name = $elxis->user()->uname; break;
					case 0: default:
						$name = ($elxis->getConfig('REALNAME') == 1) ? $elxis->user()->firstname.' '.$elxis->user()->lastname : $elxis->user()->uname; break;
					break;
				}
			} else {
				switch ($this->displayname) {
					case 1: 
						if ($elxis->user()->firstname != '') {
							$name = $elxis->user()->firstname.' '.$elxis->user()->lastname;
						} else if ($elxis->user()->uname != '') {
							$name = $elxis->user()->uname;
						} else if ($elxis->user()->email != '') {
							$name = $elxis->user()->email;
						} else {
							$name = $eLang->get('UNKNOWN');
						}
					 break;
					case 2:
						if ($elxis->user()->uname != '') {
							$name = $elxis->user()->uname;
						} else if ($elxis->user()->firstname != '') {
							$name = $elxis->user()->firstname.' '.$elxis->user()->lastname;
						} else if ($elxis->user()->email != '') {
							$name = $elxis->user()->email;
						} else {
							$name = $eLang->get('UNKNOWN');
						}
					break;
					case 0: default:
						if ($elxis->user()->firstname != '') {
							$name = $elxis->user()->firstname.' '.$elxis->user()->lastname;
						} else if ($elxis->user()->uname != '') {
							$name = $elxis->user()->uname;
						} else if ($elxis->user()->email != '') {
							$name = $elxis->user()->email;
						} else {
							$name = $eLang->get('UNKNOWN');
						}
					break;
				}
			}

			if ($this->login_redir == 2) { //AJAX
				$eDoc->addScriptLink($elxis->secureBase().'/modules/mod_login/mlogin.js');
				$logout_link = $elxis->makeURL('user:ilogout', 'inner.php', true, false);
			} else if ($this->logout_redir_uri != '') { //custom logout re-direction, use AJAX logout
				$eDoc->addScriptLink($elxis->secureBase().'/modules/mod_login/mlogin.js');
				$logout_link = $elxis->makeURL('user:ilogout', 'inner.php', true, false);
			} else {
				$logout_link = $elxis->makeURL('user:logout.html', '', true, false);
			}

			if ($elxis->user()->gid != 6) {
				$utitle = $eLang->get('MY_PROFILE');
				$ulink = $elxis->makeURL('user:members/myprofile.html');
			} else {
				$utitle = $eLang->get('USERS_CENTRAL');
				$ulink = $elxis->makeURL('user:/');
			}

			echo '<div class="elx5_modlogin_wrapper" id="modlogin_wrapper'.$this->rand.'">'."\n";

			if ($this->avatar == 1) {
				$avatar = $elxis->obj('avatar')->getAvatar($elxis->user()->avatar, 48, $this->gravatar, $elxis->user()->email);
				echo '<div class="elx5_modlogin_avatarbox">'."\n";
				echo '<a href="'.$ulink.'" title="'.$utitle.'"><img src="'.$avatar.'" alt="'.$name.'" /></a>';
				echo "</div>\n";
				echo '<div class="elx5_modlogin_mainbox">'."\n";
			} else { 
				echo '<div class="elx5_zero">'."\n";
			}

			echo '<a href="'.$ulink.'" title="'.$utitle.'" class="elx5_modlogin_profile">'.$name.'</a>';
			if ($this->usergroup == 1) {
				switch ($elxis->user()->gid) {
					case 1: $groupname = $eLang->get('ADMINISTRATOR'); break;
					case 5: $groupname = $eLang->get('USER'); break;
					case 6: $groupname = $eLang->get('EXTERNALUSER'); break;
					default: $groupname = $elxis->user()->groupname; break;
				}
				echo '<div class="elx5_modlogin_group">'.$groupname."</div>\n";
			}
			if ($this->timeonline == 1) {
				$dt = eFactory::getDate()->getTS() - $elxis->session()->first_activity;
				$min = floor($dt/60);
				$sec = $dt - ($min * 60);
				$duration = $min.':'.sprintf("%02d", $sec);
				echo '<div class="elx5_modlogin_online">'.sprintf($eLang->get('ONLINE_FOR'), $duration)."</div>\n";
			}
			if ($this->authmethod == 1) {
				if ($elxis->session()->login_method != 'elxis') {
					$authm = ucfirst($elxis->session()->login_method);
					$login_method_desc = sprintf($eLang->get('LOGGED_VIA'), $authm);
					echo '<div class="elx5_modlogin_method">'.$login_method_desc."</div>\n";
				}
			}
			if ($this->logout_redir_uri != '') { //Custom logout re-direction, use AJAX logout
				if ((stripos($this->logout_redir_uri, 'http://') === 0) || (stripos($this->logout_redir_uri, 'https://') === 0)) {
					$logout_redirect = $this->logout_redir_uri;
				} else {
					$logout_redirect = $elxis->makeURL($this->logout_redir_uri);
				}
				echo '<a href="javascript:void(null);" title="'.$eLang->get('LOGOUT').'" class="elx5_modlogin_logout" onclick="modlogout(\''.$logout_link.'\', \''.$logout_redirect.'\');">'.$eLang->get('LOGOUT')."</a>\n";
			} else if ($this->login_redir == 2) { //AJAX
				echo '<a href="javascript:void(null);" title="'.$eLang->get('LOGOUT').'" class="elx5_modlogin_logout" onclick="modlogout(\''.$logout_link.'\', \'\');">'.$eLang->get('LOGOUT')."</a>\n";
			} else {
				echo '<a href="'.$logout_link.'" title="'.$eLang->get('LOGOUT').'" class="elx5_modlogin_logout">'.$eLang->get('LOGOUT')."</a>\n";
			}
			echo "</div>\n";
			echo "</div>\n";
		}


		/**********************/
		/* SHOW ERROR MESSAGE */
		/**********************/
		private function showError($msg) {
			echo '<div class="elx5_error">'.$msg."</div>\n";
		}

	}
}

$modlogin = new moduleLogin($params);
$modlogin->run();
unset($modlogin);


?>