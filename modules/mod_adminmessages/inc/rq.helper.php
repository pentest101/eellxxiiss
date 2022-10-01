<?php 
/**
* @version		$Id: rq.helper.php 2063 2019-02-05 21:20:42Z IOS $
* @package		Elxis
* @subpackage	Module Administration messages
* @copyright	Copyright (c) 2006-2019 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class aMsgsHelper {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
	}


	/***************************/
	/* DISPLAY A JSON RESPONSE */
	/***************************/
	public function json($response) {
		if (ob_get_length() > 0) { ob_end_clean(); }
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').'GMT');
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header('Content-type: application/json; charset=utf-8');

		echo json_encode($response);
		exit;
	}


	/**************************/
	/* GET RECIPIENTS (USERS) */
	/**************************/
	public function getRecipients($except_uid, $elxis, $eLang) {
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
		if (count($rows) < 101) {
			$recipients[] = array('uid' => -1, 'name' => $eLang->get('ALL_USERS'));
		}
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


	/**********************/
	/* GET RECIPIENT DATA */
	/**********************/
	public function getRecipient($uid) {
		$db = eFactory::getDB();

		$sql = "SELECT ".$db->quoteId('firstname').", ".$db->quoteId('lastname').", ".$db->quoteId('uname')." FROM ".$db->quoteId('#__users')
		."\n WHERE ".$db->quoteId('uid')." = :xid";
		$stmt = $db->prepareLimit($sql, 0, 1);
		$stmt->bindParam(':xid', $uid, PDO::PARAM_INT);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return $row;
	}

}

?>