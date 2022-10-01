<?php 
/**
* @version		$Id: mod_adminmessages.php 2183 2019-03-25 08:12:14Z IOS $
* @package		Elxis
* @subpackage	Module Administration messages
* @copyright	Copyright (c) 2006-2019 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


if (!class_exists('modadminMsgs', false)) {
	class modadminMsgs {

		private $hide = false;

		/*********************/
		/* MAGIC CONSTRUCTOR */
		/*********************/
		public function __construct() {
			if (ELXIS_INNER == 1) {
				$this->hide = true;
			} else {
				$segs = eFactory::getURI()->getSegments();
				$n = count($segs);
				if ($n > 0) {
					$last_segment = $segs[$n - 1];
					if (in_array($last_segment, array('add.html', 'edit.html', 'new.html', 'config.html', 'configuration.html', 'settings.html', 'config.html'))) { $this->hide = true; }
				}				
			}
		}


		/********************/
		/* RUN FOREST, RUN! */
		/********************/
		public function run() {
			if (!defined('ELXIS_ADMIN')) { return; }
			if ($this->hide) { return; }

			$elxis = eFactory::getElxis();
			$eLang = eFactory::getLang();
			$eDoc = eFactory::getDocument();
			$eDate = eFactory::getDate();
			elxisLoader::loadFile('includes/libraries/elxis/form5.class.php');

			$eDoc->addFontAwesome();
			$js = $elxis->secureBase().'/modules/mod_adminmessages/inc/adminmessages.js';
			$eDoc->addScriptLink($js);

			$options = array('toid' => $elxis->user()->uid, 'read' => 0, 'delbyto' => 0);
			$msgObj = $elxis->obj('messages');
			$num_unread = $msgObj->countMessages($options);

			if ($num_unread > 0) {
				$options = array('toid' => $elxis->user()->uid, 'read' => 0, 'delbyto' => 0, 'userdata' => 1, 'convertlinks' => 1);
				$rows = $msgObj->getMessages($options);
			} else {
				$rows = false;
			}

			$except_uid = $elxis->user()->uid;
			$recipients = $this->getRecipients($except_uid, $elxis, $eLang);

			if ($num_unread > 0) {
				$title = ($num_unread == 1) ? $eLang->get('HAVE_NEW_MESSAGE') : sprintf($eLang->get('HAVE_NEW_MESSAGES'), $num_unread);
				$aclass = 'amsgs_icon amsgs_iconur';
				$class = 'amsgs_mark';
			} else {
				$title = $eLang->get('HAVE_NO_MESSAGES');
				$aclass = 'amsgs_icon';
				$class = 'amsgs_nomark';
			}
			echo '<div class="elx5_cptoptool">'."\n";
			echo '<a href="javascript:void(null);" onclick="elx5ModalOpen(\'amsgs\');" title="'.$title.'" class="'.$aclass.'" id="amsgsMarkLink"><i class="fas fa-envelope"></i><span class="'.$class.'" id="amsgsMarkNumber">'.$num_unread.'</span></a>';
			echo "</div>\n";

			$htmlHelper = $elxis->obj('html');

			echo $htmlHelper->startModalWindow('<i class="fas fa-envelope-open"></i> '.$eLang->get('MESSAGES'), 'amsgs');

			echo '<ul class="amsgs_messages" id="amsgs_messages">'."\n";
			if ($rows) {
				$now = time();
				$relpath = 'media/images/avatars/';
				if (defined('ELXIS_MULTISITE')) {
					if (ELXIS_MULTISITE > 1) { $relpath = 'media/images/site'.ELXIS_MULTISITE.'/avatars/'; }
				}
				foreach ($rows as $row) {
					$fdate = $this->friendlyDate($row->created, $now, $eDate, $eLang);
					$avatar = $this->getAvatar($row->from_avatar, $row->fromid, $row->fromname, $row->msgtype, $relpath, $elxis);
					$fname = trim(strtolower($row->fromname));
					if (($fname == 'elxis') || ($fname == 'system') || ($fname == '')) {
						$from_txt = $eLang->get('SYSTEM');
					} else {
						$from_txt = $row->fromname;
					}
					$threadid = ($row->replyto > 0) ? $row->replyto : $row->id;

					echo '<li id="amsgs_message'.$row->id.'" data-thread="'.$threadid.'">';
					echo '<div class="amsgs_side">';
					echo '<div class="amsgs_avatar"><img src="'.$avatar.'" alt="icon" /></div>'."\n";
					echo '<div class="amsgs_information"><h5>'.$from_txt.'</h5><div>'.$fdate.'</div></div>'."\n";
					echo "</div>\n";
					echo '<div class="amsgs_main"><div class="amsgs_message">'.$row->message."</div>\n";
					echo '<div class="amsgs_actions">';
					if (intval($row->fromid) > 0) {
						echo '<a href="javascript:void(null);" onclick="aMsgsReply('.$row->fromid.', '.$threadid.');"><i class="fas fa-reply"></i> '.$eLang->get('REPLY').'</a> | ';
					}
					echo '<a href="javascript:void(null);" onclick="aMsgsDeleteThread('.$threadid.');"><i class="fas fa-trash-alt"></i> '.$eLang->get('DELETE').'</a></div>'."\n";
					echo "</div>\n";
					echo "</li>\n";
				}
				echo '<li id="amsgs_message0" class="elx5_invisible" data-thread="0">'.$eLang->get('HAVE_NO_MESSAGES')."</li>\n";
			} else {//no messages
				echo '<li id="amsgs_message0" class="amsgs_nomessages" data-thread="0">'.$eLang->get('HAVE_NO_MESSAGES')."</li>\n";
			}
			echo "</ul>\n";

			$lng = $elxis->getConfig('LANG');
			echo '<a href="'.$elxis->makeURL($lng.':user:pms/').'" target="_blank" class="amsgs_viewthreads">'.$eLang->get('MESSAGE_THREADS').' <i class="fas fa-chevron-right"></i></a>'."\n";

			$inlink = $elxis->makeAURL('cpanel:ajax', 'inner.php', true);
			$form = new elxis5Form(array('idprefix' => 'amsgsf', 'labelclass' => 'elx5_label', 'sideclass' => 'elx5_labelside'));
			$form->openForm(array('name' => 'amsgsform', 'method' =>'post', 'action' => $inlink, 'id' => 'amsgsform', 'onsubmit' => 'return false;'));
			$form->openFieldset($eLang->get('NEW_MESSAGE'));
			$foptions = array();
			$foptions[] = $form->makeOption('0', '--- '.$eLang->get('SELECT').' ---');
			if ($recipients) {
				if (count($recipients) < 101) {
					$foptions[] = $form->makeOption('-1', '- '.$eLang->get('ALL_USERS').' -');
				}
				foreach ($recipients as $rcpt) { $foptions[] = $form->makeOption($rcpt['uid'], $rcpt['name']); }
			}
			$form->addSelect('toid', $eLang->get('RECIPIENT'), '0', $foptions, array('onchange' => 'document.getElementById(\'amsgsfthread\').value = \'0\';'));
			$form->addTextarea('message', '', $eLang->get('MESSAGE'), array('required' => 'required'));
			$form->addHidden('thread', '0');
			$form->addHTML('<div class="elx5_vpad">');
			$form->addButton('sendmsg', $eLang->get('SEND_MESSAGE'), 'button', array('class' => 'elx5_btn elx5_sucbtn', 'onclick' => 'aMsgsSendMessage();', 'data-waitlng' => $eLang->get('PLEASE_WAIT'), 'data-sendlng' => $eLang->get('SEND_MESSAGE')));
			$form->addHTML('</div>');

			$form->closeFieldset();
			$form->closeForm();

			echo $htmlHelper->endModalWindow();
		}


		/******************************/
		/* HUMAN FRIENDLY FORMAT DATE */
		/******************************/
		private function friendlyDate($created, $now, $eDate, $eLang) {
			$ts = strtotime($created);
			if ($now - $ts < 520000) {
				$fdate = $eDate->formatTS($ts, '%A %H:%M');
			} else if (strpos($created, gmdate('Y')) === 0) {
				$fdate = $eDate->formatTS($ts, '%d %B %H:%M');
			} else {
				$fdate = $eDate->formatTS($ts, $eLang->get('DATE_FORMAT_4'));
			}
			return $fdate;
		}


		/******************************/
		/* GET USER'S AVATAR FULL URL */
		/******************************/
		private function getAvatar($avataricon, $fromid, $fromname, $msgtype, $relpath, $elxis) {
			if ((trim($avataricon) != '') && file_exists(ELXIS_PATH.'/'.$relpath.$avataricon)) {
				return $elxis->secureBase(true).'/'.$relpath.$avataricon;
			}
			if ((trim($avataricon) != '') && preg_match('#^(http(s)?\:\/\/)#', $avataricon)) {
				return $avataricon;
			}
			if ($fromid > 0) {
				return $elxis->secureBase(true).'/modules/mod_adminmessages/inc/user32.png';
			}

			$fname = strtolower($fromname);
			if (($fname == 'elxis.org') || ($fname == 'elxis') || ($fname == 'system') || ($fname == 'elxis team')) {
				return $elxis->secureBase(true).'/modules/mod_adminmessages/inc/elxis32.png';
			}
			if (($fname == 'ios') || ($fname == 'is open source')) {
				return $elxis->secureBase(true).'/modules/mod_adminmessages/inc/ios32.png';
			}
			if ($msgtype == '') {
				return $elxis->secureBase(true).'/modules/mod_adminmessages/inc/elxis32.png';
			}

			$msgtype = strtolower($msgtype);
			switch ($msgtype) {
				case 'config': 
					$out = $elxis->secureBase(true).'/modules/mod_adminmessages/inc/config32.png';
				break;
				case 'info': 
					$out = $elxis->secureBase(true).'/modules/mod_adminmessages/inc/info32.png';
				break;
				case 'warning':
					$out = $elxis->secureBase(true).'/modules/mod_adminmessages/inc/warning32.png';
				break;
				case 'help':
					$out = $elxis->secureBase(true).'/modules/mod_adminmessages/inc/help32.png';
				break;
				default:
					if (file_exists(ELXIS_PATH.'/includes/icons/nautilus/64x64/'.$msgtype.'.png')) {
						$out = $elxis->secureBase(true).'/includes/icons/nautilus/64x64/'.$msgtype.'.png';
					} else {
						$out = $elxis->secureBase(true).'/modules/mod_adminmessages/inc/info32.png';
					}
				break;
			}
			return $out;
		}


		/**************************/
		/* GET RECIPIENTS (USERS) */
		/**************************/
		private function getRecipients($except_uid, $elxis, $eLang) {
			$db = eFactory::getDB();

			$except_uid = (int)$except_uid;
			$realname = $elxis->getConfig('REALNAME');
			$orderby_col = ($realname == 1) ? 'firstname' : 'uname';

			$sql = "SELECT ".$db->quoteId('uid').", ".$db->quoteId('firstname').", ".$db->quoteId('lastname').", ".$db->quoteId('uname')." FROM ".$db->quoteId('#__users')
			."\n WHERE ".$db->quoteId('block')." = 0 ORDER BY ".$db->quoteId($orderby_col)." ASC";
			$stmt = $db->prepareLimit($sql, 0, 500);
			$stmt->execute();
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if (!$rows) { return array(); }

			$recipients = array();
			foreach ($rows as $row) {
				if ($row['uid'] == $except_uid) { continue; }
				if ($realname == 1) {
					$recipients[] = array('uid' => $row['uid'], 'name' => $row['firstname'].' '.$row['lastname']);
				} else {
					$recipients[] = array('uid' => $row['uid'], 'name' => $row['uname']);
				}
			}
			return $recipients;
		}

	}
}


$modamsgs = new modadminMsgs();
$modamsgs->run();
unset($modamsgs);

?>