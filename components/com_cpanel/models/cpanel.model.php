<?php 
/**
* @version		$Id: cpanel.model.php 2433 2022-01-19 17:24:43Z IOS $
* @package		Elxis
* @subpackage	CPanel component
* @copyright	Copyright (c) 2006-2022 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class cpanelModel {

	private $db;


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		$this->db = eFactory::getDB();
	}


	/*******************************************/
	/* DELETE A SESSION DB ENTRY BY IP ADDRESS */
	/*******************************************/
	public function removeSessionIP($ip) {
		$sql = "DELETE FROM #__session WHERE ip_address = :banip";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':banip', $ip, PDO::PARAM_STR);
		$stmt->execute();
	}


	/***********************************************/
	/* DELETE A SESSION DB ENTRY FOR AN ELXIS USER */
	/***********************************************/
	public function removeSessionUser($uid) {
		$lmethod = 'elxis';
		$sql = "DELETE FROM #__session WHERE uid = :userid AND login_method = :lmethod";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':userid', $uid, PDO::PARAM_INT);
		$stmt->bindParam(':lmethod', $lmethod, PDO::PARAM_STR);
		$stmt->execute();
	}


	/**************************************************/
	/* DELETE A SESSION DB ENTRY FOR AN EXTERNAL USER */
	/**************************************************/
	public function removeSessionXUser($lmethod, $ip, $fact) {
		$uid = 0;
		$gid = 6;		
		$sql = "SELECT COUNT(".$this->db->quoteId('uid').") FROM #__session"
		."\n WHERE uid = :userid AND gid = :groupid AND login_method = :lmethod AND ip_address = :ipaddr";
		$stmt = $this->db->prepareLimit($sql, 0, 1);
		$stmt->bindParam(':userid', $uid, PDO::PARAM_INT);
		$stmt->bindParam(':groupid', $gid, PDO::PARAM_INT);
		$stmt->bindParam(':lmethod', $lmethod, PDO::PARAM_STR);
		$stmt->bindParam(':ipaddr', $ip, PDO::PARAM_STR);
		$stmt->execute();
		$num = (int)$stmt->fetchResult();
		if ($num < 1) { return false; }
		if ($num == 1) {
			$sql = "DELETE FROM #__session WHERE uid = :userid AND gid = :groupid AND login_method = :lmethod AND ip_address = :ipaddr";
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':userid', $uid, PDO::PARAM_INT);
			$stmt->bindParam(':groupid', $gid, PDO::PARAM_INT);
			$stmt->bindParam(':lmethod', $lmethod, PDO::PARAM_STR);
			$stmt->bindParam(':ipaddr', $ip, PDO::PARAM_STR);
			$stmt->execute();
			return true;
		}

		$sql = "DELETE FROM #__session WHERE uid = :userid AND gid = :groupid AND login_method = :lmethod AND ip_address = :ipaddr AND first_activity = :fact";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':userid', $uid, PDO::PARAM_INT);
		$stmt->bindParam(':groupid', $gid, PDO::PARAM_INT);
		$stmt->bindParam(':lmethod', $lmethod, PDO::PARAM_STR);
		$stmt->bindParam(':ipaddr', $ip, PDO::PARAM_STR);
		$stmt->bindParam(':fact', $fact, PDO::PARAM_INT);
		$stmt->execute();
		return true;
	}


	/*********************************************/
	/* GET INSTALLED COMPONENTS AND THEIR ROUTES */
	/*********************************************/
	public function getComponents($with_routes=true) {
		$stmt = $this->db->prepare("SELECT component, route FROM ".$this->db->quoteId('#__components')." ORDER BY id ASC");
		$stmt->execute();
		if (!$with_routes) {
			$rows = $stmt->fetchCol(0);
		} else {
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		}
		return $rows;
	}


	/****************************************/
	/* GET INSTALLED THIRD PARTY COMPONENTS */
	/****************************************/
	public function getThirdComponents() {
		$sql = "SELECT ".$this->db->quoteId('name').", ".$this->db->quoteId('component')." FROM ".$this->db->quoteId('#__components')
		."\n WHERE ".$this->db->quoteId('iscore')." = 0 ORDER BY ".$this->db->quoteId('id')." ASC";
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $rows;
	}


	/************************/
	/* GET AN ELEMENT ROUTE */
	/************************/
	public function getRoute($type, $base) {
		$result = '';
		switch($type) {
			case 'component':
				$sql = "SELECT ".$this->db->quoteId('route')." FROM ".$this->db->quoteId('#__components')." WHERE ".$this->db->quoteId('component')." = :cmp";
				$stmt = $this->db->prepareLimit($sql, 0, 1);
				$stmt->bindParam(':cmp', $base, PDO::PARAM_STR);
				$stmt->execute();
				$result = trim($stmt->fetchResult());
			break;
			case 'dir':
			case 'page':
				$repo_path = eFactory::getElxis()->getConfig('REPO_PATH');
				if ($repo_path == '') { $repo_path = ELXIS_PATH.'/repository'; }
				if (file_exists($repo_path.'/other/routes.php')) {
					include($repo_path.'/other/routes.php');
					if ($type == 'dir') {
						if (isset($routes) && is_array($routes) && (count($routes) > 0)) {
							if (isset($routes[$base])) { $result = $routes[$base]; }
						}
					} else {
						if (isset($page_routes) && is_array($page_routes) && (count($page_routes) > 0)) {
							if (isset($page_routes[$base])) { $result = $page_routes[$base]; }
						}
					}
				}
			break;
			case 'frontpage':
				$result = eFactory::getElxis()->getConfig('DEFAULT_ROUTE');
			break;
			default: break;
		}

		return $result;
	}


	/*************************/
	/* SET COMPONENT'S ROUTE */
	/*************************/
	public function setComponentRoute($rbase, $rroute) {
		if ($rroute != '') { //2 components can not have the same route
			$stmt = $this->db->prepare("SELECT COUNT(component) FROM #__components WHERE route = :rt AND component != :cmp");
			$stmt->bindParam(':rt', $rroute, PDO::PARAM_STR);
			$stmt->bindParam(':cmp', $rbase, PDO::PARAM_STR);
			$stmt->execute();
			$n = (int)$stmt->fetchResult();
			if ($n > 0) { return false; }
		}

		$stmt = $this->db->prepare("SELECT component, route FROM #__components WHERE component = :cmp");
		$stmt->bindParam(':cmp', $rbase, PDO::PARAM_STR);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if (!$row) { return false; }

		if (trim($row['route']) == $rroute) { return true; }

		$stmt = $this->db->prepare("UPDATE #__components SET route = :rt WHERE component = :cmp");
		$stmt->bindParam(':rt', $rroute, PDO::PARAM_STR);
		$stmt->bindParam(':cmp', $rbase, PDO::PARAM_STR);
		$stmt->execute();
		return true;
	}


	/*********************************************/
	/* GET INSTALLED TEMPLATES AND THEIR SECTION */
	/*********************************************/
	public function getTemplates() {
		$sql = "SELECT ".$this->db->quoteId('title').", ".$this->db->quoteId('template').", ".$this->db->quoteId('section')." FROM ".$this->db->quoteId('#__templates');
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $rows;
	}


	/************************************/
	/* GET STATISTICS FROM THE DATABASE */
	/************************************/
	public function getStatistics($year, $month=0) {
		$dt = ($month > 0) ? $year.'-'.sprintf("%02d", $month).'%' : $year.'%';
		$sql = "SELECT ".$this->db->quoteId('statdate').", ".$this->db->quoteId('clicks').", ".$this->db->quoteId('visits').", ".$this->db->quoteId('langs')
		."\n FROM ".$this->db->quoteId('#__statistics')
		."\n WHERE ".$this->db->quoteId('statdate')." LIKE :sdt ORDER BY ".$this->db->quoteId('statdate')." ASC";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':sdt', $dt, PDO::PARAM_STR);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

		return $rows;		
	}


	/***********************************/
	/* GET STATISTICS START YEAR/MONTH */
	/***********************************/
	public function getStatisticsStart() {
		$sql = "SELECT ".$this->db->quoteId('statdate')." FROM ".$this->db->quoteId('#__statistics')." ORDER BY ".$this->db->quoteId('statdate')." ASC";
		$stmt = $this->db->prepareLimit($sql, 0, 1);
		$stmt->execute();
		$statdate = $stmt->fetchResult();
		if (!$statdate) { return false; }

		$parts = explode('-', $statdate);
		$y = (int)$parts[0];
		$m = (int)$parts[1];
		return array('year' => $y, 'month' => $m);		
	}


	/***********************************/
	/* GET COMPONENT CPANEL PARAMETERS */
	/***********************************/
	public function componentParams($comp='com_cpanel') {
		if (trim($comp) == '') { $comp = 'com_cpanel'; }//backwards compatibility
		$sql = "SELECT ".$this->db->quoteId('params')." FROM ".$this->db->quoteId('#__components')
		."\n WHERE ".$this->db->quoteId('component')." = :xcomp";
		$stmt = $this->db->prepareLimit($sql, 0, 1);
		$stmt->bindParam(':xcomp', $comp, PDO::PARAM_STR);
		$stmt->execute();
		return (string)$stmt->fetchResult();
	}


	/***************************/
	/* GET COMPONENT CPANEL ID */
	/***************************/
	public function componentID() {
		$sql = "SELECT ".$this->db->quoteId('id')." FROM ".$this->db->quoteId('#__components')
		."\n WHERE ".$this->db->quoteId('component')." = ".$this->db->quote('com_cpanel');
		$stmt = $this->db->prepareLimit($sql, 0, 1);
		$stmt->execute();
		return (int)$stmt->fetchResult();
	}


	/********************/
	/* GET BACKUP FILES */
	/********************/
	public function fetchBackups() {
		$is_subsite = false;
		if (defined('ELXIS_MULTISITE') && (ELXIS_MULTISITE != 1)) { $is_subsite = true; }

		$files = eFactory::getFiles()->listFiles('backup/', '(\.zip)$', false, true, true);
		$rows = array();
		if ($files) {
			foreach ($files as $file) {
				$filename = basename($file);
				$type = (preg_match('/^(db)/i', $filename)) ? 'db' : 'fs';
				if (($type == 'fs') && ($is_subsite == true)) { continue; }
				$row = array(
					'bktype' => $type,
					'bkdate' => filemtime($file),
					'bkname' => $filename,
					'bksize' => filesize($file)
				);
				$rows[] = $row;
			}
			if (count($rows) > 1) { usort($rows, array($this, 'sortBackups')); }
		}
		return $rows;
	}


	/*****************************/
	/* SORT BACKUPS BY DATE DESC */
	/*****************************/
	public function sortBackups($a, $b) {
		if ($a['bkdate'] == $b['bkdate']) { return 0; }
		return ($a['bkdate'] < $b['bkdate'] ? 1 : -1);
	}


	/***********************/
	/* FETCH ALL LOG FILES */
	/***********************/
	public function fetchLogs($options, $eLang) {
		$eFiles = eFactory::getFiles();
		$eDate = eFactory::getDate();

		$rows = array();
		$logfiles = $eFiles->listFiles('logs/', '', false, true, true);
		if ($logfiles) {
			foreach ($logfiles as $logfile) {
				$filename = basename($logfile);
				$finfo = $eFiles->getNameExtension($filename);
				if (($finfo['extension'] == '') || ($finfo['extension'] == 'html')) { continue; }

				$row = new stdClass;
				$row->filename = $filename;
				$row->type = 'unknown';
				$row->typetext = 'Unknown';
				$row->logdate = '';
				$row->logperiod = 0;
				$row->lastmodified = filemtime($logfile);
				$row->size = filesize($logfile);

				if ($finfo['extension'] == 'log') {
					$parts = preg_split('#\_#', $finfo['name']);
					if (in_array($parts[0], array('error', 'notice', 'warning', 'security', 'notfound'))) {
						$row->type = $parts[0];
						$uptype = strtoupper($parts[0]);
						$row->typetext = $eLang->get($uptype);
					} else { //custom log file
						$row->type = 'other';
						$row->typetext = ucfirst($parts[0]);
					}

					if (isset($parts[1])) {
						if (strlen($parts[1]) == 6) {
							$year = substr($parts[1], 0, 4);
							$month = substr($parts[1], 4, 2);
						} else {
							$year = date('Y');
							$month = date('m');
						}
					} else {
						$year = date('Y');
						$month = date('m');
					}
					$month = (int)$month;
					$row->logdate =  $eDate->monthName($month).' '.$year;
					$row->logperiod = intval($year.$month);
				} else if ($row->filename == 'defender_ban.php') {
					$row->typetext = $eLang->get('DEFENDER_BANS');
					$row->type = 'other';
					$year = date('Y');
					$month = date('m');
					$month = (int)$month;
					$row->logdate =  $eDate->monthName($month).' '.$year;
					$row->logperiod = intval($year.$month);
				} else if ($row->filename == 'defender_ip_ranges.php') {
					$row->typetext = 'Elxis Defender IP ranges';
					$row->type = 'other';
					$year = date('Y');
					$month = date('m');
					$month = (int)$month;
					$row->logdate =  $eDate->monthName($month).' '.$year;
					$row->logperiod = intval($year.$month);
				} else if ($row->filename == 'defender_ips.php') {
					$row->typetext = 'Elxis Defender IPs';
					$row->type = 'other';
					$year = date('Y');
					$month = date('m');
					$month = (int)$month;
					$row->logdate =  $eDate->monthName($month).' '.$year;
					$row->logperiod = intval($year.$month);
				} else if ($row->filename == 'lastnotify.txt') {
					$row->typetext = $eLang->get('LAST_ERROR_NOTIF');
					$row->type = 'other';
					$year = date('Y');
					$month = date('m');
					$month = (int)$month;
					$row->logdate =  $eDate->monthName($month).' '.$year;
					$row->logperiod = intval($year.$month);
				} else if ($row->filename == 'defender_notify.txt') {
					$row->typetext = $eLang->get('LAST_DEFEND_NOTIF');
					$row->type = 'other';
					$year = date('Y');
					$month = date('m');
					$month = (int)$month;
					$row->logdate =  $eDate->monthName($month).' '.$year;
					$row->logperiod = intval($year.$month);
				} else {
					continue;
				}

				if ($options['type'] != '') {
					if ($options['type'] != $row->type) { continue; }
				}

				$rows[] = $row;
			}
		}
		unset($logfiles);

		if (count($rows) > 1) {
			$rows = $this->sortLogFiles($rows, $options['sn'], $options['so']);
		}

		return $rows;
	}


	/******************/
	/* SORT LOG FILES */
	/******************/
	private function sortLogFiles($rows, $sortname, $sortorder) {
		if ($sortname == 'type') { $sortname = 'typetext'; }

		$sortmethod = '';
		if ($sortname == 'filename') {
			$sortmethod = ($sortorder == 'asc') ? 'sortLogfilenameAsc' : 'sortLogfilenameDesc';
		} else if ($sortname == 'typetext') {
			$sortmethod = ($sortorder == 'asc') ? 'sortLogtypetextAsc' : 'sortLogtypetextDesc';
		} else if ($sortname == 'logperiod') {
			$sortmethod = ($sortorder == 'asc') ? 'sortLoglogperiodAsc' : 'sortLoglogperiodDesc';
		} else if ($sortname == 'lastmodified') {
			$sortmethod = ($sortorder == 'asc') ? 'sortLoglastmodifiedAsc' : 'sortLoglastmodifiedDesc';
		} else if ($sortname == 'size') {
			$sortmethod = ($sortorder == 'asc') ? 'sortLogsizeAsc' : 'sortLogsizeDesc';
		}

		if ($sortmethod == '') { return $rows; }
		usort($rows, array($this, $sortmethod));

		return $rows;
	}

	public function sortLogsizeDesc($a, $b) {
		if ($a->size == $b->size) { return 0; }
		return ($a->size < $b->size ? 1 : -1);
	}

	public function sortLogsizeAsc($a, $b) {
		if ($a->size == $b->size) { return 0; }
		return ($a->size > $b->size ? 1 : -1);
	}


	public function sortLoglogperiodDesc($a, $b) {
		if ($a->logperiod == $b->logperiod) { return 0; }
		return ($a->logperiod < $b->logperiod ? 1 : -1);
	}

	public function sortLoglogperiodAsc($a, $b) {
		if ($a->logperiod == $b->logperiod) { return 0; }
		return ($a->logperiod > $b->logperiod ? 1 : -1);
	}

	public function sortLoglastmodifiedDesc($a, $b) {
		if ($a->lastmodified == $b->lastmodified) { return 0; }
		return ($a->lastmodified < $b->lastmodified ? 1 : -1);
	}

	public function sortLoglastmodifiedAsc($a, $b) {
		if ($a->lastmodified == $b->lastmodified) { return 0; }
		return ($a->lastmodified > $b->lastmodified ? 1 : -1);
	}

	public function sortLogtypetextDesc($a, $b) {
		if ($a->typetext == $b->typetext) { return 0; }
		return strcasecmp($b->typetext, $a->typetext);
	}

	public function sortLogtypetextAsc($a, $b) {
		if ($a->typetext == $b->typetext) { return 0; }
		return strcasecmp($a->typetext, $b->typetext);
	}

	public function sortLogfilenameDesc($a, $b) {
		if ($a->filename == $b->filename) { return 0; }
		return strcasecmp($b->filename, $a->filename);
	}

	public function sortLogfilenameAsc($a, $b) {
		if ($a->filename == $b->filename) { return 0; }
		return strcasecmp($a->filename, $b->filename);
	}


	/********************/
	/* FETCH ALL ROUTES */
	/********************/
	public function fetchRoutes($elxis, $eLang) {
		$rows = array();

		$row = new stdClass;
		$row->type = 'frontpage';
		$row->typetext = $eLang->get('HOME');
		$row->base = '/';
		$row->route = $elxis->getConfig('DEFAULT_ROUTE');
		$row->stdroute = 0;
		$rows[] = $row;

		$components = $this->getComponents();
		if ($components) {
			foreach ($components as $cmp) {
				$row = new stdClass;
				$row->type = 'component';
				$row->typetext = 'Component';
				$row->base = $cmp['component'];
				$cname = str_replace('com_', '', $cmp['component']);
				if (trim($cmp['route']) == '') {
					$row->route = $cname;
					$row->stdroute = 1;
				} else {
					$row->route = $cmp['route'];
					$row->stdroute = 0;
				}
				$rows[] = $row;
			}
		}

		$repo_path = $elxis->getConfig('REPO_PATH');
		if ($repo_path == '') { $repo_path = ELXIS_PATH.'/repository'; }

		if (file_exists($repo_path.'/other/routes.php')) {
			include($repo_path.'/other/routes.php');
			if (isset($routes) && is_array($routes) && (count($routes) > 0)) {
				foreach ($routes as $k => $v) {
					$row = new stdClass;
					$row->type = 'dir';
					$row->typetext = $eLang->get('DIRECTORY');
					$row->base = $k;
					$row->route = trim($v);
					$row->stdroute = 0;
					$rows[] = $row;
				}
			}

			if (isset($page_routes) && is_array($page_routes) && (count($page_routes) > 0)) {
				foreach ($page_routes as $k => $v) {
					$row = new stdClass;
					$row->type = 'page';
					$row->typetext = $eLang->get('PAGE');
					$row->base = $k;
					$row->route = trim($v);
					$row->stdroute = 0;
					$rows[] = $row;
				}
			}
		}

		if (count($rows) > 1) { usort($rows, array($this, 'sortRoutes')); }

		return $rows;
	}


	/***************/
	/* SORT ROUTES */
	/***************/
	public function sortRoutes($a, $b) {
		if ($a->base == $b->base) { return 0; }
		return strcasecmp($a->base, $b->base);
	}

}

?>