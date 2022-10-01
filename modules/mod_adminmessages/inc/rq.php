<?php 
/**
* @version		$Id: rq.php 2183 2019-03-25 08:12:14Z IOS $
* @package		Elxis
* @subpackage	Module Administration messages
* @copyright	Copyright (c) 2006-2019 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


require(ELXIS_PATH.'/modules/mod_adminmessages/inc/rq.helper.php');
$helper = new aMsgsHelper();

if (isset($_POST['task'])) {
	$task = trim(filter_input(INPUT_POST, 'task', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW || FILTER_FLAG_STRIP_HIGH));
} else {
	$task = '';
}

$eLang = eFactory::getLang();
$eLang->load('mod_adminmessages', 'module');

if ($task == 'delete') {
	$elxis = eFactory::getElxis();

	$response = array('success' => 0, 'message' => '');
	$threadid = isset($_POST['id']) ? (int)$_POST['id'] : 0;
	if ($threadid < 1) {
		$response['message'] = 'No message ID specified!';
		$helper->json($response);
	}

	$row = new messagesDbTable();
	if (!$row->load($threadid)) {//message not found, consider it a successfull deletion in order javascript to remove it from the list
		$response['success'] = 1;
		$helper->json($response);
	}

	$uid = $elxis->user()->uid;
	if ($row->fromid == $uid) {
		$is_sender = 1;
	} else if ($row->toid == $uid) {
		$is_sender = 0;
	} else {
		$is_sender = -1;
	}

	if ($is_sender == -1) {
		$response['message'] = 'You can delete only own messages!';
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
	$msgObj->deleteThread($threadid, $is_sender);
	unset($msgObj);

	$response['success'] = 1;
	$helper->json($response);
}


if ($task == 'send') {
	$elxis = eFactory::getElxis();
	$response = array('success' => 0, 'message' => '');

	$toid = isset($_POST['toid']) ? (int)$_POST['toid'] : 0;
	$replyto = isset($_POST['replyto']) ? (int)$_POST['replyto'] : 0;
	$message = strip_tags(filter_input(INPUT_POST, 'msg', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
	$pat = "#([\<]|[\>]|[\*]|[\~]|[\`]|[\^]|[\|]|[\\\])#u";
	$message = eUTF::trim(preg_replace($pat, '', $message));

	if (($toid < -1) || ($toid == 0)) {
		$response['message'] = $eLang->get('SELECT_RECIPIENT');
		$helper->json($response);
	}

	if ($message == '') {
		$response['message'] = $eLang->get('MUST_TYPEMSG');
		$helper->json($response);
	}

	$uid = $elxis->user()->uid;
	if ($toid == $uid) {
		$response['message'] = 'You cannot send a message to yourself!';
		$helper->json($response);
	}

	if ($toid == -1) {
		$rcpt = $helper->getRecipients(0, $elxis, $eLang);
	} else {
		$rcpt = $helper->getRecipient($toid);
		if (!$rcpt) {
			$response['message'] = 'Recipient not found!';
			$helper->json($response);
		}		
	}

	if ($elxis->getConfig('REALNAME') == 0) {
		$fromname = $elxis->user()->uname;
		$toname = ($toid == -1) ? '' : $rcpt['uname'];
	} else {
		$fromname = $elxis->user()->firstname.' '.$elxis->user()->lastname;
		$toname = ($toid == -1) ? '' : $rcpt['firstname'].' '.$rcpt['lastname'];
	}

	if ($replyto > 0) {
		if ($toid == -1) {//you cannot reply to multiple users! even if he has selected 1, ok with that
			$response['message'] = 'You cannot reply to multiple users! Please select single user.';
			$helper->json($response);
		}
		$row = new messagesDbTable();
		if (!$row->load($replyto)) {
			$response['message'] = 'Original thread '.$replyto.' not found!';
			$helper->json($response);
		}
		$ok = false;
		if (($row->fromid == $uid) && ($row->toid == $toid)) {
			$ok = true;
		} else if (($row->fromid == $toid) && ($row->toid == $uid)) {
			$ok = true;
		}
		if (!$ok) {
			$response['message'] = $eLang->get('ERROR').'! In this thread different people talk.';
			$helper->json($response);
		}
		//If the thread has been deleted by either the original sender or the recipient create a new thread
		if (($row->delbyfrom == 1) || ($row->delbyto == 1)) { $replyto = 0; }
		unset($row);
	}

	$row = new messagesDbTable();
	$row->fromid = $uid;
	$row->fromname = $fromname;
	$row->msgtype = 'info';
	$row->message = $message;
	$row->replyto = $replyto;

	$num = 0;
	if ($toid == -1) {
		foreach ($rcpt as $user) {
			if ($user['uid'] < 1) { continue; }
			$row->toid = $user['uid'];
			$row->toname = $user['name'];
			$ok = $row->insert();
			if ($ok) { $num++; }
			$row->forceNew(true);
		}
	} else {
		$row->toid = $toid;
		$row->toname = $toname;
		$ok = $row->insert();
		if ($ok) { $num++; }
	}

	if ($num == 0) {
		$response['message'] = 'Saving message failed!';
		$helper->json($response);
	}

	$response['success'] = 1;
	$helper->json($response);
}

$response = array('success' => 0, 'message' => 'Invalid request');
$helper->json($response);

?>