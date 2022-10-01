<?php 
/**
* @version		$Id: cron.helper.php 1832 2016-05-29 19:55:50Z sannosi $
* @package		Elxis
* @subpackage	Helpers/(Pseudo) Cron jobs
* @copyright	Copyright (c) 2006-2015 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisCronHelper {

	private $forcerun_allowed = true;
	private $runnow = true;
	private $seclevel = 0;
	private $repo_path = '';


	/***************/
	/* CONSTRUCTOR */
	/***************/
	public function __construct() {
		$elxis = eFactory::getElxis();

		if ((int)$elxis->getConfig('CRONJOBS') == 0) {
			$this->runnow = false;
			$this->forcerun_allowed = false;
			return;
		}

		$this->seclevel = (int)$elxis->getConfig('SECURITY_LEVEL');

		$this->repo_path = rtrim($elxis->getConfig('REPO_PATH'), '/');
		if ($this->repo_path == '') { $this->repo_path = ELXIS_PATH.'/repository'; }
		if (!file_exists($this->repo_path.'/logs/') || !is_dir($this->repo_path.'/logs/')) {
			$this->runnow = false;
			$this->forcerun_allowed = false;
		}
	}


	/*****************/
	/* RUN CRON JOBS */
	/*****************/
	public function run($force=false) {
		if ($this->forcerun_allowed === false) { $force = false; }
		if ($force === true) {
			$this->runnow = true; 
		}
		if (!$this->runnow) { return false; }

		if (file_exists($this->repo_path.'/logs/lastcron.txt')) {
			//Don't run sooner than 10 minutes since last run except if force run was requested
			if (!$force) {
				$dts = time() - filemtime($this->repo_path.'/logs/lastcron.txt');
				if ($dts < 600) { return false; }
			}
		} else {
			$eFiles = eFactory::getFiles();
			$eFiles->createFile('logs/lastcron.txt', '', true);
		}

		$this->pubUnpubArticles();
		$this->pubUnpubModules();
		$this->blockExpiredUsers();
		$this->sendUserReminders();

		if ($this->seclevel == 0) { //Custom cron jobs only under "Normal" security level
			$this->customCronjobs();
		}

		@touch($this->repo_path.'/logs/lastcron.txt');

		return true;
	}


	/****************************************/
	/* SCHEDULED PUBLISH/UNPUBLISH ARTICLES */
	/****************************************/
	private function pubUnpubArticles() {
		$db = eFactory::getDB();

		$stdpubdate = '2014-01-01 00:00:00';
		$stdunpubdate = '2060-01-01 00:00:00';
		$today = gmdate('Y-m-d H:i:s');

		$sql = "UPDATE ".$db->quoteId('#__content')." SET ".$db->quoteId('published')." = 1, ".$db->quoteId('pubdate')." = :xspd"
		."\n WHERE ".$db->quoteId('published')." = 0 AND ".$db->quoteId('pubdate')." != :xspd AND ".$db->quoteId('pubdate')." <= :xnow";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':xspd', $stdpubdate, PDO::PARAM_STR);
		$stmt->bindParam(':xnow', $today, PDO::PARAM_STR);
		$stmt->execute();

		$sql = "UPDATE ".$db->quoteId('#__content')." SET ".$db->quoteId('published')." = 0, ".$db->quoteId('unpubdate')." = :xsupd"
		."\n WHERE ".$db->quoteId('published')." = 1 AND ".$db->quoteId('unpubdate')." != :xsupd AND ".$db->quoteId('unpubdate')." <= :xnow";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':xsupd', $stdunpubdate, PDO::PARAM_STR);
		$stmt->bindParam(':xnow', $today, PDO::PARAM_STR);
		$stmt->execute();
	}


	/***************************************/
	/* SCHEDULED PUBLISH/UNPUBLISH MODULES */
	/***************************************/
	private function pubUnpubModules() {
		$db = eFactory::getDB();

		$stdpubdate = '2014-01-01 00:00:00';
		$stdunpubdate = '2060-01-01 00:00:00';
		$today = gmdate('Y-m-d H:i:s');

		$sql = "UPDATE ".$db->quoteId('#__modules')." SET ".$db->quoteId('published')." = 1, ".$db->quoteId('pubdate')." = :xspd"
		."\n WHERE ".$db->quoteId('published')." = 0 AND ".$db->quoteId('pubdate')." != :xspd AND ".$db->quoteId('pubdate')." <= :xnow";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':xspd', $stdpubdate, PDO::PARAM_STR);
		$stmt->bindParam(':xnow', $today, PDO::PARAM_STR);
		$stmt->execute();

		$sql = "UPDATE ".$db->quoteId('#__modules')." SET ".$db->quoteId('published')." = 0, ".$db->quoteId('unpubdate')." = :xsupd"
		."\n WHERE ".$db->quoteId('published')." = 1 AND ".$db->quoteId('unpubdate')." != :xsupd AND ".$db->quoteId('unpubdate')." <= :xnow";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':xsupd', $stdunpubdate, PDO::PARAM_STR);
		$stmt->bindParam(':xnow', $today, PDO::PARAM_STR);
		$stmt->execute();
	}


	/*******************************/
	/* BLOCK EXPIRED USER ACCOUNTS */
	/*******************************/
	private function blockExpiredUsers() {
		$db = eFactory::getDB();

		$stdexpdate = '2060-01-01 00:00:00';
		$today = gmdate('Y-m-d H:i:s');

		$sql = "UPDATE ".$db->quoteId('#__users')." SET ".$db->quoteId('block')." = 1"
		."\n WHERE ".$db->quoteId('block')." = 0 AND ".$db->quoteId('expiredate')." != :xsexp AND ".$db->quoteId('expiredate')." <= :xnow";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':xsexp', $stdexpdate, PDO::PARAM_STR);
		$stmt->bindParam(':xnow', $today, PDO::PARAM_STR);
		$stmt->execute();
	}


	/***********************/
	/* SEND USER REMINDERS */
	/***********************/
	private function sendUserReminders() {
		$db = eFactory::getDB();

		$today = gmdate('Y-m-d H:i:s');
		$sql = "SELECT b.id, b.uid, b.reminderdate, b.title, b.link, b.note, u.firstname, u.lastname, u.email"
		."\n FROM ".$db->quoteId('#__bookmarks')." b"
		."\n INNER JOIN ".$db->quoteId('#__users')." u ON u.uid = b.uid"
		."\n WHERE b.cid = 5 AND b.remindersent = 0 AND b.reminderdate <= :xnow";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':xnow', $today, PDO::PARAM_STR);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_OBJ);
		if (!$rows) { return; }

		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eDate = eFactory::getDate();

		$eLang->load('com_user', 'component');

		$parsed = parse_url($elxis->getConfig('URL'));

		foreach ($rows as $row) {
			$subject = sprintf($eLang->get('REMINDER_FROM_SITE'), $parsed['host']);

			$txt = ($row->title != '') ? $row->title : $subject;
			$txt .= "\r\n\r\n";
			if ($row->note != '') {	$txt .= $row->note."\r\n"; }
			if ($row->link != '') {	$txt .= $row->link."\r\n"; }
			$txt .= $eLang->get('REMINDER_DATETIME').': ';
			$txt .= $eDate->formatDate($row->reminderdate, $eLang->get('DATE_FORMAT_12'));
			$txt .= "\r\n\r\n\r\n\r\n";
			$txt .= "------------------------------------\r\n";
			$txt .= $eLang->get('NOREPLYMSGINFO')."\r\n\r\n";
			$txt .= $elxis->getConfig('SITENAME')."\r\n";
			$txt .= $elxis->getConfig('URL');

			$to = $row->email.','.$row->firstname.' '.$row->lastname;
			$elxis->sendmail($subject, $txt, '', null, 'plain', $to);
		}

		$sql = "UPDATE ".$db->quoteId('#__bookmarks')." SET ".$db->quoteId('remindersent')." = 1 WHERE ".$db->quoteId('id')." = :xid";
		$stmt = $db->prepare($sql);
		foreach ($rows as $row) {
			$id = $row->id;
			$stmt->bindParam(':xid', $id, PDO::PARAM_INT);
			$stmt->execute();
		}
	}


	/************************/
	/* RUN CUSTOM CRON JOBS */
	/************************/
	private function customCronjobs() {
		if (!file_exists($this->repo_path.'/cronjobs/')) { return; }
		if (!is_dir($this->repo_path.'/cronjobs/')) { return; }

		$eFiles = eFactory::getFiles();
		$files = $eFiles->listFiles('cronjobs/', '(\.php)$', false, true, true);
		if (!$files) { return; }

		foreach ($files as $file) {
			include($file);
		}
	}

}

?>