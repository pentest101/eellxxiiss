<?php 
/**
* @version		$Id: base.html.php 2406 2021-04-16 17:47:04Z IOS $
* @package		Elxis
* @subpackage	Component User
* @copyright	Copyright (c) 2006-2021 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class userView {


	protected function __construct() {
	}


	/***************************/
	/* DISPLAY AN ERROR SCREEN */
	/***************************/
	public function base_errorScreen($message, $title='') {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$eDoc = eFactory::getDocument();

		if ($title == '') { $title = $eLang->get('ERROR'); }
		$link = $elxis->makeURL('user:/');
		$gid = $elxis->user()->gid;

		if ($gid != 7) { //not guest
			echo '<h1>'.$title."</h1>\n";
			echo '<div class="elx5_error">'.$message."</div>\n";
			echo '<div class="elx5_vlspace elx5_center">'."\n";
			echo '<a href="'.$link.'" title="'.$eLang->get('USERSCENTRAL').'" class="elx5_btn elx5_ibtn">'.$eLang->get('USERSCENTRAL')."</a>\n";
			echo "</div>\n";
			return;
		}

		//guest
		$userdata = new stdClass;
		$userdata->uname ='';
		$userdata->uid = 0;
		$userdata->gid = 7;
		$userdata->name = $eLang->get('GUEST');
		$userdata->avatar = $elxis->secureBase().'/components/com_user/images/noavatar.png';
		$userdata->online = -1;
		$userdata->totalmessages = 0;
		$userdata->newmessages = 0;
		$userdata->bookmarks = 0;
		$userdata->twitter_username = '';

		$eDoc->setMetaTag('robots', 'noindex, nofollow');
		$eDoc->addFontAwesome();
		$eDoc->addStyleLink($elxis->secureBase().'/components/com_user/inc/user'.$eLang->getinfo('RTLSFX').'.css');	

		echo '<h1>'.$title."</h1>\n";

		echo '<div class="elx_user_wrapcol">'."\n";

		echo '<div class="elx_user_maincol">'."\n";
		echo '<div class="elx_user_maincolin">'."\n";
		echo '<p class="elx5_error">'.$message."</p>\n";
		echo '<div class="elx5_dspace">'."\n";
		echo '<a href="'.$link.'" title="'.$eLang->get('USERSCENTRAL').'" class="elx5_btn elx5_ibtn">'.$eLang->get('USERSCENTRAL')."</a>\n";
		echo "</div>\n";
		if ($eDoc->countModules('usercp_maincol') > 0) {
			$eDoc->modules('usercp_maincol');
		}
		echo "</div>\n";//.elx_user_maincolin end
		echo "</div>\n";//.elx_user_maincol end
		echo '<div class="elx_user_sidecol">'."\n";
		$this->base_sideProfile($userdata, $elxis, $eLang, $eDoc, 'userscentral', false);
		echo "</div>\n";//.elx_user_sidecol
		echo '<div class="clear"></div>'."\n";
		echo "</div>\n";
	}


	/*********************************/
	/* GET THE TRANSLATED GROUP NAME */
	/*********************************/
	protected function base_translateGroup($groupname, $gid) {
		switch((int)$gid) {
			case 1: $out = eFactory::getLang()->get('ADMINISTRATOR'); break;
			case 5: $out = eFactory::getLang()->get('USER'); break;
			case 6: $out = eFactory::getLang()->get('EXTERNALUSER'); break;
			case 7: $out = eFactory::getLang()->get('GUEST'); break;
			default: $out = $groupname; break;
		}
		return $out;
	}


	/********************/
	/* CREATE SORT LINK */
	/********************/
	protected function base_sortLink($col, $corder='ua', $page=0) {
		switch ($col) {
			case 'firstname': $norder = ($corder == 'fa') ? 'fd': 'fa'; break;
			case 'lastname': $norder = ($corder == 'la') ? 'ld': 'la'; break;
			case 'groupname': $norder = ($corder == 'ga') ? 'gd': 'ga'; break;
			case 'preflang': $norder = ($corder == 'pa') ? 'pd': 'pa'; break;
			case 'country': $norder = ($corder == 'ca') ? 'cd': 'ca'; break;
			case 'website': $norder = ($corder == 'wa') ? 'wd': 'wa'; break;
			case 'gender': $norder = ($corder == 'gea') ? 'ged': 'gea'; break;
			case 'registerdate': $norder = ($corder == 'ra') ? 'rd': 'ra'; break;
			case 'lastvisitdate': $norder = ($corder == 'lva') ? 'lvd': 'lva'; break;
			case 'profile_views': $norder = ($corder == 'pva') ? 'pvd': 'pva'; break;
			case 'uname': default: $norder = ($corder == 'ua') ? 'ud': 'ua'; break;
		}
		return ($page > 0) ? 'page='.$page.'&amp;order='.$norder : 'order='.$norder;
	}


	/******************/
	/* USER SIDE MENU */
	/******************/
	protected function base_sideProfile($userdata, $elxis, $eLang, $eDoc, $page, $params=false) {
		if (!$params) {
			$db = eFactory::getDB();

			$sql = "SELECT ".$db->quoteId('params')." FROM ".$db->quoteId('#__components')." WHERE ".$db->quoteId('component')." = ".$db->quote('com_user');
			$stmt = $db->prepareLimit($sql, 0, 1);
			$stmt->execute();
			$params_str = (string)$stmt->fetchResult();
			elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
			$params = new elxisParameters($params_str, '', 'component');
		}

		if ($elxis->acl()->getLevel() <= 2) {//users send pms access check applies
			$usersendpms = (int)$params->get('usersendpms', 2);
		} else {
			$usersendpms = 2;//allowed to all
		}

		$acl_profile = $elxis->acl()->check('com_user', 'profile', 'view');
		$uc_link = $elxis->makeURL('user:/');

		$profile_link = '';
		if ($userdata->uid > 0) {
			if (($acl_profile == 2) || (($acl_profile == 1) && ($elxis->user()->uid == $userdata->uid))) {
				$profile_link = $uc_link.'members/'.$userdata->uid.'.html';
			}
		}

		if ($profile_link != '') {
			echo '<div class="elx_user_avatar"><a href="'.$profile_link.'" title="'.$userdata->name.' : '.$eLang->get('PROFILE').'"><img src="'.$userdata->avatar.'" alt="'.$userdata->name.'" /></a></div>'."\n";
			echo '<h3><a href="'.$profile_link.'" title="'.$userdata->name.' : '.$eLang->get('PROFILE').'">'.$userdata->name.'</a> <span class="elx_user_uname">('.$userdata->uname.")</span></h3>\n";
		} else {
			echo '<div class="elx_user_avatar"><img src="'.$userdata->avatar.'" alt="'.$userdata->name.'" /></div>'."\n";
			if ($userdata->uid > 0) {
				echo '<h3>'.$userdata->name.' <span class="elx_user_uname">('.$userdata->uname.")</span></h3>\n";
			} else {
				echo '<h3>'.$userdata->name."</h3>\n";
			}
		}

		if ($userdata->online == 1) {
			echo '<div class="elx5_user_online">'.$eLang->get('ONLINE')."</div>\n";
		} else if ($userdata->online == 0) {
			echo '<div class="elx5_user_offline">'.$eLang->get('OFFLINE')."</div>\n";
		}

		echo '<ul class="elx_user_sidemenu">'."\n";
		echo '<li data-item="cpanel"><a href="'.$uc_link.'" title="'.$eLang->get('USERSCENTRAL').'"><i class="fas fa-bars"></i> '.$eLang->get('USERSCENTRAL')."</a></li>\n";

		if ($elxis->user()->gid == 7) {
			echo '<li data-item="login"><a href="'.$uc_link.'login/" title="'.$eLang->get('LOGIN').'" rel="nofollow"><i class="fas fa-sign-in-alt"></i> '.$eLang->get('LOGIN')."</a></li>\n";
			if ($elxis->getConfig('REGISTRATION') == 1) {
				echo '<li data-item="register"><a href="'.$uc_link.'register.html" title="'.$eLang->get('REGISTER').'" rel="nofollow"><i class="fas fa-signature"></i> '.$eLang->get('REGISTER')."</a></li>\n";
			}
			if ($elxis->getConfig('PASS_RECOVER') == 1) {
				echo '<li data-item="recover"><a href="'.$uc_link.'recover-pwd.html" title="'.$eLang->get('RECOVERPASS').'" rel="nofollow"><i class="fas fa-undo"></i> '.$eLang->get('RECOVERPASS')."</a></li>\n";
			}
		} else {
			if ($elxis->acl()->getLevel() > 1) {
				if ($userdata->newmessages == 1) {
					$title_str = $eLang->get('HAVENEW_MESSAGE');
					$msgs_str = ' <span dir="ltr">(<strong>1</strong>)</span>';
				} else if ($userdata->newmessages > 1) {
					$title_str = sprintf($eLang->get('HAVENEW_MESSAGES'), $userdata->newmessages);
					$msgs_str = ' <span dir="ltr">(<strong>'.$userdata->newmessages.'</strong>)</span>';
				} else if ($userdata->totalmessages > 0) {
					$title_str = $eLang->get('PERSONAL_MESSAGES');
					$msgs_str = ' <span dir="ltr">('.$userdata->totalmessages.')</span>';
				} else {
					$title_str = $eLang->get('PERSONAL_MESSAGES');
					$msgs_str = '';
				}

				$can_sendpms_to_user = false;
				if ($usersendpms == 2) {
					$can_sendpms_to_user = true;
				} else if ($usersendpms == 1) {
					if (($userdata->gid == 1) || ($userdata->gid == 2)) {
						$can_sendpms_to_user = true;
					}
				}

				echo '<li data-item="messages"><a href="'.$uc_link.'pms/" title="'.$title_str.'"><i class="fas fa-envelope"></i> '.$eLang->get('MESSAGES').$msgs_str."</a></li>\n";
				if (($page == 'profile') || ($page == 'editprofile')) {
					if ($elxis->user()->uid != $userdata->uid) {
						if ($can_sendpms_to_user) {
							echo '<li data-item="sendmessage"><a href="javascript:void(null);" onclick="elx5UserPMSOpen(0, '.$userdata->uid.', \''.addslashes($userdata->name).'\');" title="'.$eLang->get('SEND_MESSAGE').'"><i class="fas fa-paper-plane"></i> '.$eLang->get('SEND_MESSAGE')."</a></li>\n";
						}
					}
				} else if ($page == 'messages') {
					if ($usersendpms > 0) {
						echo '<li data-item="sendmessage"><a href="javascript:void(null);" onclick="elx5UserPMSOpen(0, 0, \'\');" title="'.$eLang->get('SEND_MESSAGE').'"><i class="fas fa-paper-plane"></i> '.$eLang->get('SEND_MESSAGE')."</a></li>\n";
					}
				}

				$bookmarks_str = ($userdata->bookmarks > 0) ? ' <span dir="ltr">('.$userdata->bookmarks.')</span>' : '';
				echo '<li data-item="bookmarks"><a href="'.$uc_link.'bookmarks/" title="'.$eLang->get('BOOKMARKS_NOTES').'"><i class="fas fa-bookmark"></i> '.$eLang->get('BOOKMARKS').$bookmarks_str."</a></li>\n";
				if ($userdata->twitter_username != '') {
					$link = 'https://twitter.com/'.$userdata->twitter_username;
					echo '<li data-item="twitter"><a href="'.$link.'" title="'.$userdata->twitter_username.' : Twitter" target="_blank"><i class="fab fa-twitter"></i> Twitter</a></li>'."\n";
				}
				if ($page == 'bookmarks') {
					$editlink = $elxis->makeURL('user:editbookmark', 'inner.php', true);
					echo '<li data-item="addbookmark"><a href="javascript:void(null);" onclick="elx5UserEditBookmark(0, 1);" title="'.$eLang->get('NEW_BOOKMARK').'"><i class="fas fa-plus"></i> '.$eLang->get('NEW_BOOKMARK')."</a></li>\n";
					echo '<li data-item="addreminder"><a href="javascript:void(null);" onclick="elx5UserEditBookmark(0, 5);" title="'.$eLang->get('NEW_REMINDER').'"><i class="fas fa-bell"></i> '.$eLang->get('NEW_REMINDER')."</a></li>\n";
				}
			}

			if ($elxis->acl()->check('com_user', 'memberslist', 'view') > 0) {
				echo '<li data-item="members"><a href="'.$uc_link.'members/" title="'.$eLang->get('MEMBERSLIST').'"><i class="fas fa-users"></i> '.$eLang->get('MEMBERSLIST')."</a></li>\n";
			}

			if (($elxis->acl()->getLevel() > 1) && ($userdata->uid > 0)) {
				if ($elxis->acl()->check('com_user', 'profile', 'edit') > 0) {
					echo '<li data-item="editprofile"><a href="'.$uc_link.'members/edit.html?id='.$userdata->uid.'" title="'.$eLang->get('EDITPROFILE').' '.$userdata->name.'"><i class="fas fa-user-edit"></i> '.$eLang->get('EDITPROFILE')."</a></li>\n";
				}
			}

			if (($page == 'profile') || ($page == 'editprofile')) {
				$gid = (isset($userdata->gid)) ? (int)$userdata->gid : -1;
				if ($gid != 1) {//not for administrators
					$allowed = $elxis->acl()->check('com_user', 'profile', 'block');
					if (($allowed == 1) && ($elxis->user()->uid != $userdata->uid)) {
						echo '<li data-item="blockuser"><a href="'.$uc_link.'members/block.html?id='.$userdata->uid.'" title="'.$eLang->get('BLOCKUSER').' '.$userdata->name.'" onclick="return confirm(\''.addslashes($eLang->get('AREYOUSURE')).'\');"><i class="fas fa-ban"></i> '.$eLang->get('BLOCKUSER')."</a></li>\n";
					}
					$allowed = $elxis->acl()->check('com_user', 'profile', 'delete');
					if (($allowed == 2) || (($allowed == 1) && ($elxis->user()->uid == $userdata->uid))) {
						echo '<li data-item="deleteuser"><a href="'.$uc_link.'members/delete.html?id='.$userdata->uid.'" title="'.$eLang->get('DELETEACCOUNT').' '.$userdata->name.'" onclick="return confirm(\''.addslashes($eLang->get('AREYOUSURE')).'\');"><i class="fas fa-trash-alt"></i> '.$eLang->get('DELETEACCOUNT')."</a></li>\n";
					}
				}
			}

			echo '<li data-item="logout"><a href="'.$uc_link.'logout.html" title="'.$eLang->get('LOGOUT').'"><i class="fas fa-sign-out-alt"></i> '.$eLang->get('LOGOUT')."</a></li>\n";
			echo "</ul>\n";
		}

		if ($eDoc->countModules('user_sidecol') > 0) {
			echo '<div class="user_sidecol_mods">'."\n";
			$eDoc->modules('user_sidecol');
			echo "</div>\n";
		}

	}


	/*********************************/
	/* SORT USER CLICKS BY DATE DESC */
	/*********************************/
	public function base_sortUserClicks($a, $b) {
		if ($a['ts'] == $b['ts']) { return 0; }
		return ($a['ts'] < $b['ts'] ? 1 : -1);
	}


	/**************************************/
	/* SEND PERSONAL MESSAGE MODAL WINDOW */
	/**************************************/
	protected function base_pmsform($elxis, $eLang, $params=false) {
		$myuid = (int)$elxis->user()->uid;
		if ($myuid < 1) { return; }

		$db = eFactory::getDB();

		if (!$params) {
			$sql = "SELECT ".$db->quoteId('params')." FROM ".$db->quoteId('#__components')." WHERE ".$db->quoteId('component')." = ".$db->quote('com_user');
			$stmt = $db->prepareLimit($sql, 0, 1);
			$stmt->execute();
			$params_str = (string)$stmt->fetchResult();
			elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
			$params = new elxisParameters($params_str, '', 'component');
		}

		if ($elxis->acl()->getLevel() <= 2) {//users send pms access check applies
			$usersendpms = (int)$params->get('usersendpms', 2);
		} else {
			$usersendpms = 2;//allowed to all
		}

		$people = false;
		if ($usersendpms > 0) {
			$sql = "SELECT ".$db->quoteId('uid').", ".$db->quoteId('firstname').", ".$db->quoteId('lastname').", ".$db->quoteId('uname')
			."\n FROM ".$db->quoteId('#__users')
			."\n WHERE ".$db->quoteId('block')." = 0 AND ".$db->quoteId('uid')." <> ".$myuid;
			if ($usersendpms == 1) {
				$sql .= "\n AND (".$db->quoteId('gid')." = 1 OR ".$db->quoteId('gid')." = 2)";
			}
			$sql .= "\n ORDER BY ".$db->quoteId('firstname')." ASC";
			$stmt = $db->prepareLimit($sql, 0, 300);
			$stmt->execute();
			$people = $stmt->fetchAllAssoc('uid', PDO::FETCH_OBJ);
		}

		elxisLoader::loadFile('includes/libraries/elxis/form5.class.php');
		$action = $elxis->makeURL('user:pms/', 'inner.php', true);

		$htmlHelper = $elxis->obj('html');

		echo $htmlHelper->startModalWindow('<i class="fas fa-paper-plane"></i> '.$eLang->get('SEND_NEW_MESSAGE'), 'pms', '', false, '', '');

		$form = new elxis5Form(array('idprefix' => 'spm', 'labelclass' => 'elx5_label', 'sideclass' => 'elx5_labelside'));
		$form->openForm(array('name' => 'fmsendpm', 'method' =>'post', 'action' => $action, 'id' => 'fmsendpm', 'onsubmit' => 'return false;'));
		$form->openFieldset($eLang->get('SEND_NEW_MESSAGE'));
		
		$options = array();
		if ($people) {
			foreach ($people as $uid => $person) {
				$options[] = $form->makeOption($uid, $person->firstname.' '.$person->lastname);
			}
		}
		$form->addHTML('<div class="elx5_zero" id="elx5_user_pmsrcptbox">');
		$form->addMultiSelect('recipients', $eLang->get('RECIPIENT'), array(), $options);
		$form->addHTML('</div>');
		$form->addHTML('<div class="elx5_invisible" id="elx5_user_pmsreplybox">');
		$form->addInfo($eLang->get('RECIPIENT'), '', array('id' => 'elx5_user_pmsreplytext'));
		$form->addHTML('</div>');
		$form->setOption('labelclass', 'elx5_labelblock');
		$form->setOption('sideclass', 'elx5_zero');
		$form->addTextarea('message', '', $eLang->get('MESSAGE'), array('required' => 'required'));
		$form->addHidden('replyto', 0);
		$form->addHidden('touid', 0);
		$form->addToken('fmsendpm');
		$form->closeFieldset();

		$form->addHTML('<div class="elx5_vpad">');
		$form->addButton('sendmsg', $eLang->get('SEND'), 'button', array('class' => 'elx5_btn elx5_sucbtn', 'onclick' => 'elx5UserPMSSend();', 'data-waitlng' => $eLang->get('PLEASE_WAIT'), 'data-sendlng' => $eLang->get('SEND')));
		$form->addHTML('</div>');
		$form->closeForm();
		echo $htmlHelper->endModalWindow(false);

		echo $htmlHelper->pageLoader('elx5_user_pmsloading');
	}


	/**********************************/
	/* ADD/EDIT BOOKMARK MODAL WINDOW */
	/**********************************/
	protected function base_bookmarkform($categories, $elxis, $eLang) {
		$myuid = (int)$elxis->user()->uid;
		if ($myuid < 1) { return; }

		$eDate = eFactory::getDate();

		elxisLoader::loadFile('includes/libraries/elxis/form5.class.php');
		$action = $elxis->makeURL('user:bookmarks/', 'inner.php', true);

		$htmlHelper = $elxis->obj('html');

		echo $htmlHelper->startModalWindow('<i class="fas fa-bookmark"></i> '.$eLang->get('BOOKMARKS_NOTES'), 'bmk', '', false, '', '');
		$form = new elxis5Form(array('idprefix' => 'ebmk', 'labelclass' => 'elx5_label', 'sideclass' => 'elx5_labelside'));
		$form->openForm(array('name' => 'fmeditbkm', 'method' =>'post', 'action' => $action, 'id' => 'fmeditbkm', 'onsubmit' => 'return false;'));
		$options = array();
		if ($categories) {
			foreach ($categories as $cid => $category) {
				$options[] = $form->makeOption($cid, $category[1]);
			}
		}
		$form->addSelect('cid', $eLang->get('CATEGORY'), 1, $options, array('onchange' => 'elx5UserSwitchBookmarkCategory();'));
		$form->addText('title', '', $eLang->get('TITLE'), array('maxlength' => 255));
		$form->addUrl('link', '', $eLang->get('LINK'), array('maxlength' => 255));
		$form->addTextarea('note', '', $eLang->get('NOTE'));
		$ts = time() + 86400;
		$reminderdate = gmdate('Y-m-d H:i:s', $ts);
		$localdate = $eDate->elxisToLocal($reminderdate, true);
		$datetime = new DateTime($localdate);
		$remdatetime = $datetime->format($eLang->get('DATE_FORMAT_BOX_LONG'));
		$form->addHTML('<div class="elx5_zero" id="elx5_user_remdatebox">');
		$form->addDatetime('reminderdate', $remdatetime, $eLang->get('REMINDER_DATETIME'), array('tip' => $eDate->getTimezone()));
		$form->addHTML('</div>');
		unset($localdate, $datetime, $remdatetime);
		$form->addHidden('id', 0);
		$form->addToken('fmeditbkm');
		$form->addHTML('<div class="elx5_vspace">');
		$form->addButton('savebmk', $eLang->get('SAVE'), 'button', array('class' => 'elx5_btn elx5_sucbtn', 'onclick' => 'elx5UserSaveBookmark();', 'data-waitlng' => $eLang->get('PLEASE_WAIT'), 'data-savelng' => $eLang->get('SAVE')));
		$form->addHTML('</div>');
		$form->closeForm();
		echo $htmlHelper->endModalWindow(false);
	}

}

?>