<?php 
/**
* @version		$Id: user.model.php 2377 2020-12-16 19:01:24Z IOS $
* @package		Elxis
* @subpackage	Component User
* @copyright	Copyright (c) 2006-2021 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class userModel {

	private $db;


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		$this->db = eFactory::getDB();
	}


	/*******************************/
	/* GET ALL AVAILABLE LANGUAGES */
	/*******************************/
	public function getLanguages() {
		$ilangs = eFactory::getFiles()->listFolders('language');
		$langs = eFactory::getLang()->getallinfo($ilangs);
		return $langs;
	}


	/***************/
	/* COUNT USERS */
	/***************/
	public function countUsers($options) {
		$wheres = array();
		$pdo_binds = array();
		if (isset($options['uid']) && (intval($options['uid']) > 0)) {
			$wheres[] = $this->db->quoteId('uid').' = :xuid';
			$pdo_binds[':xuid'] = array($options['uid'], PDO::PARAM_INT);
		}
		$querycols = array('firstname', 'lastname', 'uname', 'email', 'city', 'address', 'phone', 'mobile', 'website');
		foreach ($querycols as $k => $col) {
			if (isset($options[$col])) {
				if ($options[$col] != '') {
					$v = '%'.$options[$col].'%';
					$idx = ':xq'.$k;
					$wheres[] = $this->db->quoteId($col).' LIKE '.$idx;
					$pdo_binds[$idx] = array($v, PDO::PARAM_STR);
				}
			}
		}

		if (isset($options['block']) && (intval($options['block']) > -1)) {//frontend
			$wheres[] = $this->db->quoteId('block').' = :xbl';
			$pdo_binds[':xbl'] = array($options['block'], PDO::PARAM_INT);
		}
		if (isset($options['expiredate']) && ($options['expiredate'] != '')) {//frontend
			$wheres[] = $this->db->quoteId('expiredate').' > :xpdate';
			$pdo_binds[':xpdate'] = array($options['expiredate'], PDO::PARAM_STR);
		}

		$sql = "SELECT COUNT(uid) FROM ".$this->db->quoteId('#__users');
		if (count($wheres) > 0) { $sql .= ' WHERE '.implode(' AND ', $wheres); }
		$stmt = $this->db->prepareLimit($sql, 0, 1);
		if (count($pdo_binds) > 0) {
			foreach ($pdo_binds as $key => $parr) {
				$stmt->bindParam($key, $parr[0], $parr[1]);
			}
		}
		$stmt->execute();

		return (int)$stmt->fetchResult();
	}


	/***************************/
	/* GET USERS FROM DATABASE */
	/***************************/
	public function getUsers($options, $countarticles=true) {
		$wheres = array();
		$pdo_binds = array();

		if (isset($options['uid']) && (intval($options['uid']) > 0)) {
			$wheres[] = $this->db->quoteId('uid').' = :xuid';
			$pdo_binds[':xuid'] = array($options['uid'], PDO::PARAM_INT);
		}
		$querycols = array('firstname', 'lastname', 'uname', 'email', 'city', 'address', 'phone', 'mobile', 'website');
		foreach ($querycols as $k => $col) {
			if (isset($options[$col])) {
				if ($options[$col] != '') {
					$v = '%'.$options[$col].'%';
					$idx = ':xq'.$k;
					$wheres[] = $this->db->quoteId($col).' LIKE '.$idx;
					$pdo_binds[$idx] = array($v, PDO::PARAM_STR);
				}
			}
		}

		$sql = "SELECT uid, firstname, lastname, uname, block, gid, groupname, email, registerdate, lastvisitdate FROM ".$this->db->quoteId('#__users');
		if (count($wheres) > 0) { $sql .= ' WHERE '.implode(' AND ', $wheres); }
		$sql .= ' ORDER BY '.$options['sn'].' '.strtoupper($options['so']);

		$stmt = $this->db->prepareLimit($sql, $options['limitstart'], $options['limit']);
		if (count($pdo_binds) > 0) {
			foreach ($pdo_binds as $key => $parr) {
				$stmt->bindParam($key, $parr[0], $parr[1]);
			}
		}
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (!$rows) { return false; }

		if ($countarticles) {
			$sql = "SELECT COUNT(id) FROM ".$this->db->quoteId('#__content')." WHERE ".$this->db->quoteId('created_by')." = :cuid";
			$stmt = $this->db->prepareLimit($sql, 0, 1);
			foreach ($rows as $k => $row) {
				$stmt->bindParam(':cuid', $row['uid'], PDO::PARAM_INT);
				$stmt->execute();
				$rows[$k]['articles'] = (int)$stmt->fetchResult();
			}
		}
		return $rows;
	}


	/**************************************/
	/* GET ALL USER DETAILS FROM DATABASE */
	/**************************************/
	public function getUser($uid=0, $block=-1) {
		$sql = "SELECT * FROM ".$this->db->quoteId('#__users').' WHERE uid = :xuid';
		if ($block > -1) { $sql .= ' AND block = :xblock'; }
		$stmt = $this->db->prepareLimit($sql, 0, 1);
		$stmt->bindParam(':xuid', $uid, PDO::PARAM_INT);
		if ($block > -1) { $stmt->bindParam(':xblock', $block, PDO::PARAM_INT); }
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_OBJ);
	}


	/*********************/
	/* GET USER ACTIVITY */
	/*********************/
	public function getUserActivity($uid) {
		$sql = "SELECT ".$this->db->quoteId('first_activity').", ".$this->db->quoteId('last_activity').", ".$this->db->quoteId('clicks').","
		."\n ".$this->db->quoteId('current_page').", ".$this->db->quoteId('ip_address').", ".$this->db->quoteId('user_agent')
		."\n FROM ".$this->db->quoteId('#__session')." WHERE ".$this->db->quoteId('uid')." = :userid ORDER BY ".$this->db->quoteId('last_activity')." DESC";
		$stmt = $this->db->prepareLimit($sql, 0, 1);
		$stmt->bindParam(':userid', $uid, PDO::PARAM_INT);
		$stmt->execute();
		$activity = $stmt->fetch(PDO::FETCH_OBJ);

		return $activity;
	}


	/*******************************/
	/* GET USER GROUP ACCESS LEVEL */
	/*******************************/
	public function getGroupLevel($gid) {
		$sql = "SELECT ".$this->db->quoteId('level')." FROM ".$this->db->quoteId('#__groups').' WHERE gid = :xgid';
		$stmt = $this->db->prepareLimit($sql, 0, 1);
		$stmt->bindParam(':xgid', $gid, PDO::PARAM_INT);
		$stmt->execute();
		return (int)$stmt->fetchResult();
	}


	/**********************/
	/* BLOCK/UNBLOCK USER */
	/**********************/
	public function blockUser($uid, $block=1) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$uid = (int)$uid;
		if ($uid < 0) { $uid = 0; }
		$block = (int)$block;
		$response = array('success' => false, 'message' => 'Unknown error', 'newblocked' => -1);
		if ($elxis->getConfig('SECURITY_LEVEL') > 1) {
			$response['message'] = 'The block of user accounts is not allowed under the current security level!';
			return $response;
		}
		$allowed = $elxis->acl()->check('com_user', 'profile', 'block');
		if (($uid == 0) || ($allowed != 1)) {
			$response['message'] = $eLang->get('NOTALLOWACCPAGE');
			return $response;
		}
		if ($elxis->user()->uid == $uid) {
			$response['message'] = $eLang->get('CNOT_ACTION_SELF');
			return $response;
		}

		$sql = "SELECT u.uid, u.gid, u.uname, u.block, g.level FROM ".$this->db->quoteId('#__users')." u"
		."\n INNER JOIN ".$this->db->quoteId('#__groups')." g ON g.gid=u.gid WHERE u.uid = :uid";
		$stmt = $this->db->prepareLimit($sql, 0, 1);
		$stmt->bindParam(':uid', $uid, PDO::PARAM_INT);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if (!$row) {
			$response['message'] = $eLang->get('USERNFOUND');
			return $response;
		}

		if ((intval($row['gid']) == 1) || ($row['level'] >= $elxis->acl()->getLevel())) {
			$response['message'] = $eLang->get('CNOT_ACTION_USER');
			return $response;
		}
		
		if ($block == -1) { $block = (intval($row['block']) == 0) ? 1 : 0; }

		$stmt = $this->db->prepare("UPDATE ".$this->db->quoteId('#__users')." SET ".$this->db->quoteId('block')." = :xblock WHERE ".$this->db->quoteId('uid')." = :xuid");
		$stmt->bindParam(':xblock', $block, PDO::PARAM_INT);
		$stmt->bindParam(':xuid', $uid, PDO::PARAM_INT);
		$ok = $stmt->execute();
		if ($ok) {
			$response['success'] = true;
			$response['newblocked'] = $block;
			if ($block == 1) {
				$response['message'] = sprintf($eLang->get('USERACCBLOCKED'), $row['uname']);
			} else {
				$response['message'] = sprintf($eLang->get('USERACCUNBLOCKED'), $row['uname']);
			}
			$stmt = $this->db->prepare("DELETE FROM ".$this->db->quoteId('#__session')." WHERE ".$this->db->quoteId('uid')." = :uid");
			$stmt->bindParam(':uid', $uid, PDO::PARAM_INT);
			$stmt->execute();
		} else {
			$response['message'] = $eLang->get('ACTION_FAILED');
		}

		return $response;
	}


	/***************/
	/* DELETE USER */
	/***************/
	public function deleteUser($uid, $usercontent='unpublish') {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$uid = (int)$uid;
		if ($uid < 0) { $uid = 0; }

		$response = array('success' => false, 'message' => 'Unknown error');
		if ($elxis->getConfig('SECURITY_LEVEL') > 0) {
			$response['message'] = 'The deletion of user accounts is not allowed under the current security level!';
			return $response;
		}
		$proceed = false;
		$allowed = $elxis->acl()->check('com_user', 'profile', 'delete');
		if (($allowed == 2) || (($allowed == 1) && ($elxis->user()->uid == $uid))) { $proceed = true; }
		if (($uid == 0) || ($proceed === false)) {
			$response['message'] = $eLang->get('NOTALLOWACCPAGE');
			return $response;
		}

		$sql = "SELECT u.uid, u.gid, u.uname, g.level FROM ".$this->db->quoteId('#__users')." u"
		."\n INNER JOIN ".$this->db->quoteId('#__groups')." g ON g.gid=u.gid WHERE u.uid = :uid";
		$stmt = $this->db->prepareLimit($sql, 0, 1);
		$stmt->bindParam(':uid', $uid, PDO::PARAM_INT);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if (!$row) {
			$response['message'] = $eLang->get('USERNFOUND');
			return $response;
		}

		if ((intval($row['gid']) == 1) || ($row['level'] >= $elxis->acl()->getLevel())) {
			$response['message'] = $eLang->get('CNOT_ACTION_USER');
			return $response;
		}

		$stmt = $this->db->prepare("DELETE FROM ".$this->db->quoteId('#__users')." WHERE ".$this->db->quoteId('uid')." = :xuid");
		$stmt->bindParam(':xuid', $uid, PDO::PARAM_INT);
		$ok = $stmt->execute();
		if (!$ok) {
			$response['message'] = $eLang->get('ACTION_FAILED');
			return $response;
		}
		
		$response['success'] = true;
		$stmt = $this->db->prepare("DELETE FROM ".$this->db->quoteId('#__session')." WHERE ".$this->db->quoteId('uid')." = :xuid");
		$stmt->bindParam(':xuid', $uid, PDO::PARAM_INT);
		$stmt->execute();

		$stmt = $this->db->prepare("DELETE FROM ".$this->db->quoteId('#__acl')." WHERE ".$this->db->quoteId('uid')." = :xuid");
		$stmt->bindParam(':xuid', $uid, PDO::PARAM_INT);
		$stmt->execute();

		$stmt = $this->db->prepare("DELETE FROM ".$this->db->quoteId('#__comments')." WHERE ".$this->db->quoteId('uid')." = :xuid");
		$stmt->bindParam(':xuid', $uid, PDO::PARAM_INT);
		$stmt->execute();

		if ($usercontent == 'delete') {
			$stmt = $this->db->prepare("DELETE FROM ".$this->db->quoteId('#__content')." WHERE ".$this->db->quoteId('created_by')." = :xuid");
			$stmt->bindParam(':xuid', $uid, PDO::PARAM_INT);
			$stmt->execute();
		} else if ($usercontent == 'unpublish') {
			$pub = 0;
			$stmt = $this->db->prepare("UPDATE ".$this->db->quoteId('#__content')." SET ".$this->db->quoteId('published')." = :xpub WHERE ".$this->db->quoteId('created_by')." = :xuid");
			$stmt->bindParam(':xpub', $pub, PDO::PARAM_INT);
			$stmt->bindParam(':xuid', $uid, PDO::PARAM_INT);
			$stmt->execute();
		}

		$response['message'] = sprintf($eLang->get('USERACCDELETED'), $row['uname']);
		return $response;
	}


	/*******************************************/
	/* COUNT USER'S TOTAL ARTICLES OR COMMENTS */
	/*******************************************/
	public function counter($uid, $cmp='content', $only_published=false) {
		if ($cmp == 'comments') {
			$sql = 'SELECT COUNT(id) FROM #__comments WHERE '.$this->db->quoteId('uid').' = :xuid';
		} else {
			$sql = 'SELECT COUNT(id) FROM #__content WHERE '.$this->db->quoteId('created_by').' = :xuid';
		}
		if ($only_published) { $sql .= ' AND published = 1'; }
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xuid', $uid, PDO::PARAM_INT);
		$stmt->execute();
		return (int)$stmt->fetchResult();
	}


	/********************************/
	/* INCREMENT USER PROFILE VIEWS */
	/********************************/
	public function incrementProfileViews($uid, $views) {
		$sql = 'UPDATE #__users SET '.$this->db->quoteId('profile_views').' = :xviews WHERE uid = :xuid';
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xviews', $views, PDO::PARAM_INT);
		$stmt->bindParam(':xuid', $uid, PDO::PARAM_INT);
		$stmt->execute();
	}


	/****************/
	/* COUNT GROUPS */
	/****************/
	public function countGroups() {
		$sql = "SELECT COUNT(gid) FROM ".$this->db->quoteId('#__groups');
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		return (int)$stmt->fetchResult();
	}


	/****************************/
	/* GET GROUPS FROM DATABASE */
	/****************************/
	public function getGroups($options, $with_members=true) {
		$sql = "SELECT * FROM ".$this->db->quoteId('#__groups')." ORDER BY ".$this->db->quoteId($options['sn'])." ".strtoupper($options['so']);
		$stmt = $this->db->prepareLimit($sql, $options['limitstart'], $options['limit']);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if ($rows && $with_members) {
			$sql = "SELECT COUNT(uid) FROM ".$this->db->quoteId('#__users')." WHERE ".$this->db->quoteId('gid')." = :xgid";
			$stmt = $this->db->prepare($sql);
			foreach ($rows as $k => $row) {
				if ($row['gid'] == 7) {
					$rows[$k]['members'] = 0;
				} else if ($row['gid'] == 6) {
					$rows[$k]['members'] = 0;
				} else {
					$stmt->bindParam(':xgid', $row['gid'], PDO::PARAM_INT);
					$stmt->execute();
					$rows[$k]['members'] = (int)$stmt->fetchResult();
				}
			}
		}

		return $rows;
	}


	/**********************************************/
	/* GET GROUPS FROM DATABASE (KEY/VALUE PAIRS) */
	/**********************************************/
	public function getGroupsList() {
		$sql = "SELECT ".$this->db->quoteId('gid').", ".$this->db->quoteId('groupname')." FROM ".$this->db->quoteId('#__groups')
		."\n ORDER BY ".$this->db->quoteId('level')." ASC";
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$rows = $stmt->fetchPairs();
		return $rows;
	}


	/***************************/
	/* GET GROUP FROM DATABASE */
	/***************************/
	public function getGroup($gid) {
		$sql = "SELECT * FROM ".$this->db->quoteId('#__groups')." WHERE ".$this->db->quoteId('gid')." = :xgid";
		$stmt = $this->db->prepareLimit($sql, 0, 1);
		$stmt->bindParam(':xgid', $gid, PDO::PARAM_INT);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if (!$row) { return false; }

		$sql = "SELECT COUNT(uid) FROM ".$this->db->quoteId('#__users')." WHERE ".$this->db->quoteId('gid')." = :xgid";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xgid', $row['gid'], PDO::PARAM_INT);
		$stmt->execute();
		$row['members'] = (int)$stmt->fetchResult();

		return $row;
	}


	/*********************/
	/* DELETE USER GROUP */
	/*********************/
	public function deleteGroup($gid) {
		$stmt = $this->db->prepare("DELETE FROM ".$this->db->quoteId('#__groups')." WHERE ".$this->db->quoteId('gid')." = :xgid");
		$stmt->bindParam(':xgid', $gid, PDO::PARAM_INT);
		return $stmt->execute();
	}


	/*************/
	/* COUNT ACL */
	/*************/
	public function countACL($options) {
		$wheres = array();
		$pdo_binds = array();
		$querycols = array('category', 'element', 'action', 'minlevel', 'gid', 'uid');
		foreach ($querycols as $k => $col) {
			if (!isset($options[$col])) { continue; }
			switch ($col) {
				case 'minlevel': case 'gid': case 'uid':
					$v = (int)$options[$col];
					if ($v > -1) {
						$idx = ':xq'.$k;
						$wheres[] = $this->db->quoteId($col).' = '.$idx;
						$pdo_binds[$idx] = array($v, PDO::PARAM_INT);
					}
				break;
				default:
					if ($options[$col] != '') {
						$v = '%'.$options[$col].'%';
						$idx = ':xq'.$k;
						$wheres[] = $this->db->quoteId($col).' LIKE '.$idx;
						$pdo_binds[$idx] = array($v, PDO::PARAM_STR);
					}
				break;
			}
		}

		$sql = "SELECT COUNT(id) FROM ".$this->db->quoteId('#__acl');
		if (count($wheres) > 0) { $sql .= ' WHERE '.implode(' AND ', $wheres); }
		$stmt = $this->db->prepareLimit($sql, 0, 1);
		if (count($pdo_binds) > 0) {
			foreach ($pdo_binds as $key => $parr) {
				$stmt->bindParam($key, $parr[0], $parr[1]);
			}
		}
		$stmt->execute();
		return (int)$stmt->fetchResult();
	}


	/*************************/
	/* GET ACL FROM DATABASE */
	/*************************/
	public function getACL($options) {
		$wheres = array();
		$pdo_binds = array();
		$querycols = array('category', 'element', 'action', 'minlevel', 'gid', 'uid');
		foreach ($querycols as $k => $col) {
			if (!isset($options[$col])) { continue; }
			switch ($col) {
				case 'minlevel': case 'gid': case 'uid':
					$v = (int)$options[$col];
					if ($v > -1) {
						$idx = ':xq'.$k;
						$wheres[] = $this->db->quoteId($col).' = '.$idx;
						$pdo_binds[$idx] = array($v, PDO::PARAM_INT);
					}
				break;
				default:
					if ($options[$col] != '') {
						$v = '%'.$options[$col].'%';
						$idx = ':xq'.$k;
						$wheres[] = $this->db->quoteId($col).' LIKE '.$idx;
						$pdo_binds[$idx] = array($v, PDO::PARAM_STR);
					}
				break;
			}
		}

		$sql = "SELECT * FROM ".$this->db->quoteId('#__acl');
		if (count($wheres) > 0) { $sql .= ' WHERE '.implode(' AND ', $wheres); }
		if ($options['sn'] == 'category') {
			$sql .= ' ORDER BY '.$this->db->quoteId('category').' '.strtoupper($options['so']).', '.$this->db->quoteId('element').' '.strtoupper($options['so']);
		} else {
			$sql .= ' ORDER BY '.$this->db->quoteId($options['sn']).' '.strtoupper($options['so']);
		}
		$stmt = $this->db->prepareLimit($sql, $options['limitstart'], $options['limit']);
		if (count($pdo_binds) > 0) {
			foreach ($pdo_binds as $key => $parr) {
				$stmt->bindParam($key, $parr[0], $parr[1]);
			}
		}
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $rows;
	}


	/*************************/
	/* DELETE ACL ELEMENT(S) */
	/*************************/
	public function deleteACL($ids) {
		if (is_array($ids)) {
			$stmt = $this->db->prepare("DELETE FROM ".$this->db->quoteId('#__acl')." WHERE ".$this->db->quoteId('id')." IN (".implode(', ', $ids).")");
			return $stmt->execute();
		} else if (is_int($ids)) {
			$stmt = $this->db->prepare("DELETE FROM ".$this->db->quoteId('#__acl')." WHERE ".$this->db->quoteId('id')." = :xid");
			$stmt->bindParam(':xid', $ids, PDO::PARAM_INT);
			return $stmt->execute();
		} else {
			return false;
		}
	}


	/*******************************************/
	/* GET ALL ACL CATEGORIES/ELEMENTS/ACTIONS */
	/*******************************************/
	public function getACLcea() {
		$eFiles = eFactory::getFiles();

		$data = array();
		$data['categories'] = array('administration', 'component', 'module');
		$data['elements'] = array('acl', 'article', 'backup', 'category', 'comments', 'groups', 'interface', 'memberslist', 'menu', 'profile', 'routes', 'settings');
		$data['actions'] = array('view', 'manage', 'add', 'edit', 'delete', 'publish', 'block', 'login', 'post', 'uploadavatar', 'viewaddress','viewage', 'viewemail', 'viewgender', 'viewmobile', 'viewphone', 'viewwebsite');

		$comps = $eFiles->listFolders('components/');
		if ($comps) {
			$data['categories'] = array_merge($data['categories'], $comps);
			$data['elements'] = array_merge($data['elements'], $comps);
		}
		$mods = $eFiles->listFolders('modules/');
		if ($mods) {
			$data['categories'] = array_merge($data['categories'], $mods);
			$data['elements'] = array_merge($data['elements'], $mods);
		}
		unset($comps, $mods);

		$sql = "SELECT DISTINCT ".$this->db->quoteId('category')." FROM ".$this->db->quoteId('#__acl');
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$items = $stmt->fetchCol();
		if ($items) {
			foreach ($items as $item) { $data['categories'][] = $item; }
		}

		$sql = "SELECT DISTINCT ".$this->db->quoteId('element')." FROM ".$this->db->quoteId('#__acl');
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$items = $stmt->fetchCol();
		if ($items) {
			foreach ($items as $item) { $data['elements'][] = $item; }
		}

		$sql = "SELECT DISTINCT ".$this->db->quoteId('action')." FROM ".$this->db->quoteId('#__acl');
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$items = $stmt->fetchCol();
		if ($items) {
			foreach ($items as $item) { $data['actions'][] = $item; }
		}

		$data['categories'] = array_unique($data['categories']);
		$data['elements'] = array_unique($data['elements']);
		$data['actions'] = array_unique($data['actions']);

		sort($data['categories']);
		sort($data['elements']);
		sort($data['actions']);
		return $data;
	}


	/*****************************************/
	/* CHECK IF THERE IS ALREADY AN ACL RULE */
	/*****************************************/
	public function countMatchRules($id, $category, $element, $identity, $action, $minlevel, $gid, $uid) {
		$id = (int)$id;
		$sql = "SELECT COUNT(id) FROM ".$this->db->quoteId('#__acl')
		."\n WHERE ".$this->db->quoteId('category')." = :xcat AND ".$this->db->quoteId('element')." = :xel"
		."\n AND ".$this->db->quoteId('identity')." = :xident AND ".$this->db->quoteId('action')." = :xact"
		."\n AND ".$this->db->quoteId('minlevel')." = :xlevel AND ".$this->db->quoteId('gid')." = :xgid AND ".$this->db->quoteId('uid')." = :xuid";
		if ($id > 0) {
			$sql .= " AND ".$this->db->quoteId('id')." <> :xid";
		}
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xcat', $category, PDO::PARAM_STR);
		$stmt->bindParam(':xel', $element, PDO::PARAM_STR);
		$stmt->bindParam(':xident', $identity, PDO::PARAM_INT);
		$stmt->bindParam(':xact', $action, PDO::PARAM_STR);
		$stmt->bindParam(':xlevel', $minlevel, PDO::PARAM_INT);
		$stmt->bindParam(':xgid', $gid, PDO::PARAM_INT);
		$stmt->bindParam(':xuid', $uid, PDO::PARAM_INT);
		if ($id > 0) {
			$stmt->bindParam(':xid', $id, PDO::PARAM_INT);
		}
		$stmt->execute();
		$n = (int)$stmt->fetchResult();
		return $n;
	}


	/******************************************************/
	/* CHECK IF THERE IS ALREADY A MINIMUM LEVEL ACL RULE */
	/******************************************************/
	public function countLevelRules($id, $category, $element, $identity, $action) {
		$id = (int)$id;
		$minlevel = -1;
		$sql = "SELECT COUNT(id) FROM ".$this->db->quoteId('#__acl')
		."\n WHERE ".$this->db->quoteId('category')." = :xcat AND ".$this->db->quoteId('element')." = :xel"
		."\n AND ".$this->db->quoteId('identity')." = :xident AND ".$this->db->quoteId('action')." = :xact"
		."\n AND ".$this->db->quoteId('minlevel')." > :xlevel";
		if ($id > 0) {
			$sql .= " AND ".$this->db->quoteId('id')." <> :xid";
		}
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xcat', $category, PDO::PARAM_STR);
		$stmt->bindParam(':xel', $element, PDO::PARAM_STR);
		$stmt->bindParam(':xident', $identity, PDO::PARAM_INT);
		$stmt->bindParam(':xact', $action, PDO::PARAM_STR);
		$stmt->bindParam(':xlevel', $minlevel, PDO::PARAM_INT);
		if ($id > 0) {
			$stmt->bindParam(':xid', $id, PDO::PARAM_INT);
		}
		$stmt->execute();
		$n = (int)$stmt->fetchResult();
		return $n;
	}


	/*************************/
	/* FETCH USER'S COMMENTS */
	/*************************/
	public function fetchUserComments($uid, $num) {
		$elxis = eFactory::getElxis();

		$lowlev = $elxis->acl()->getLowLevel();
		$exactlev = $elxis->acl()->getExactLevel();
		$element = 'com_content';
		$sql = "SELECT a.message, a.created, c.id, c.catid, c.title, c.seotitle, c.image, g.seolink, g.published FROM ".$this->db->quoteId('#__comments')." a"
		."\n LEFT JOIN ".$this->db->quoteId('#__content')." c ON c.id=a.elid"
		."\n LEFT JOIN ".$this->db->quoteId('#__categories')." g ON g.catid=c.catid"
		."\n WHERE a.uid = :xuid AND a.element = :xelem AND a.published=1 AND c.published=1"
		."\n AND ((c.alevel <= :lowlevel) OR (c.alevel = :exactlevel))"
		."\n ORDER BY a.created DESC";
		$stmt = $this->db->prepareLimit($sql, 0, $num);
		$stmt->bindParam(':xuid', $uid, PDO::PARAM_INT);
		$stmt->bindParam(':xelem', $element, PDO::PARAM_STR);
		$stmt->bindParam(':lowlevel', $lowlev, PDO::PARAM_INT);
		$stmt->bindParam(':exactlevel', $exactlev, PDO::PARAM_INT);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (!$rows) { return array(); }

		$comments = array();
		$elids = array();
		foreach ($rows as $row) {
			$catid = (int)$row['catid'];
			if (($catid > 0) && (intval($row['published']) == 0)) { continue; }
			$elids[] = $row['id'];

			$comment = new stdClass;
			$comment->id = $row['id'];
			$comment->title = $row['title'];
			$comment->image = $row['image'];
			$comment->catid = $catid;
			$comment->link = (($catid > 0) && (trim($row['seolink']) != '')) ? $row['seolink'].$row['seotitle'].'.html' : $row['seotitle'].'.html';
			$comment->created = $row['created'];
			$comment->message = $row['message'];
			$comments[] = $comment;
		}

		if (!$comments) { return array(); }
		if ($elxis->getConfig('MULTILINGUISM') == 0) { return $comments; }
		if (!$elids) { return $comments; }
		$lng = eFactory::getURI()->getUriLang();
		if ($lng == '') { return $comments; }

		$elids = array_unique($elids);
		$sql = "SELECT ".$this->db->quoteId('elid').", ".$this->db->quoteId('translation')." FROM ".$this->db->quoteId('#__translations')
		."\n WHERE ".$this->db->quoteId('category')."=".$this->db->quote('com_content')." AND ".$this->db->quoteId('element')."=".$this->db->quote('title')
		."\n AND ".$this->db->quoteId('language')." = :lng AND ".$this->db->quoteId('elid')." IN (".implode(", ", $elids).")";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':lng', $lng, PDO::PARAM_STR);
		$stmt->execute();
		$trans = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (!$trans) { return $comments; }

		foreach ($trans as $tran) {
			$elid = $tran['elid'];
			$title = $tran['translation'];
			foreach ($comments as $i => $comment) {
				if ($comment->id == $elid) { $comments[$i]->title = $title; }
			}
		}

		return $comments;
	}


	/*******************/
	/* COUNT BOOKMARKS */
	/*******************/
	public function countBookmarks($uid) {
		$sql = "SELECT COUNT(".$this->db->quoteId('id').") FROM ".$this->db->quoteId('#__bookmarks')." WHERE ".$this->db->quoteId('uid')." = :xuid";
		$stmt = $this->db->prepareLimit($sql, 0, 1);
		$stmt->bindParam(':xuid', $uid, PDO::PARAM_INT);
		$stmt->execute();
		return (int)$stmt->fetchResult();
	}


	/*******************/
	/* FETCH BOOKMARKS */
	/*******************/
	public function fetchBookmarks($uid, $limitstart, $limit) {
		$elxis = eFactory::getElxis();

		$sql  = "SELECT * FROM ".$this->db->quoteId('#__bookmarks')." WHERE ".$this->db->quoteId('uid')." = :xuid ORDER BY ".$this->db->quoteId('created').' DESC';
		$stmt = $this->db->prepareLimit($sql, $limitstart, $limit);
		$stmt->bindParam(':xuid', $uid, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_OBJ);
	}


	/****************************/
	/* FETCH USERS FOR FRONTEND */
	/****************************/
	public function fetchPublicUsers($order, $limitstart, $limit) {
		switch($order) {
			case 'fa': $orderby = 'u.firstname ASC'; break;
			case 'fd': $orderby = 'u.firstname DESC'; break;
			case 'la': $orderby = 'u.lastname ASC'; break;
			case 'ld': $orderby = 'u.lastname DESC'; break;
			case 'ga': $orderby = 'u.groupname ASC'; break;
			case 'gd': $orderby = 'u.groupname DESC'; break;
			case 'pa': $orderby = 'u.preflang ASC'; break;
			case 'pd': $orderby = 'u.preflang DESC'; break;
			case 'ca': $orderby = 'u.country ASC'; break;
			case 'cd': $orderby = 'u.country DESC'; break;
			case 'cia': $orderby = 'u.city ASC'; break;
			case 'cid': $orderby = 'u.city DESC'; break;
			case 'pca': $orderby = 'u.postalcode ASC'; break;
			case 'pcd': $orderby = 'u.postalcode DESC'; break;
			case 'aa': $orderby = 'u.address ASC'; break;
			case 'ad': $orderby = 'u.address DESC'; break;
			case 'wa': $orderby = 'u.website ASC'; break;
			case 'wd': $orderby = 'u.website DESC'; break;
			case 'gea': $orderby = 'u.gender ASC'; break;
			case 'ged': $orderby = 'u.gender DESC'; break;
			case 'ra': $orderby = 'u.registerdate ASC'; break;
			case 'rd': $orderby = 'u.registerdate DESC'; break;
			case 'lva': $orderby = 'u.lastvisitdate ASC'; break;
			case 'lvd': $orderby = 'u.lastvisitdate DESC'; break;
			case 'pva': $orderby = 'u.profile_views ASC'; break;
			case 'pvd': $orderby = 'u.profile_views DESC'; break;
			case 'ua': $orderby = 'u.uname ASC'; break;
			case 'ud': $orderby = 'u.uname DESC'; break;
			case 'pha': $orderby = 'u.phone ASC'; break;
			case 'phd': $orderby = 'u.phone DESC'; break;
			case 'moa': $orderby = 'u.mobile ASC'; break;
			case 'mod': $orderby = 'u.mobile DESC'; break;
			case 'ema': $orderby = 'u.email ASC'; break;
			case 'emd': $orderby = 'u.email DESC'; break;
			default: $orderby = 'u.firstname ASC'; break;
		}

		$sql = "SELECT u.*, s.last_activity FROM ".$this->db->quoteId('#__users')." u"
		."\n LEFT JOIN ".$this->db->quoteId('#__session')." s ON s.uid = u.uid"
		."\n WHERE u.block = 0 AND u.expiredate > '".eFactory::getDate()->getDate()."'"
		."\n GROUP BY u.uid ORDER BY ".$orderby;
		$stmt = $this->db->prepareLimit($sql, $limitstart, $limit);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_OBJ);

		return $rows;
	}


	/***************************/
	/* GET USERS LAST ACTIVITY */
	/***************************/
	public function getLastActivity($uids) {
		if (!$uids) { return array(); }
		$sql = "SELECT u.uid, s.last_activity FROM ".$this->db->quoteId('#__users')." u"
		."\n LEFT JOIN ".$this->db->quoteId('#__session')." s ON s.uid = u.uid";
		if (count($uids) == 1) {
			$sql .= " WHERE u.uid = ".$uids[0];
		} else {
			$sql .= " WHERE u.uid IN (".implode(', ', $uids).")";
		}
		$sql .= "\n GROUP BY u.uid";
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$rows = $stmt->fetchPairs();
		if (!$rows) { return array(); }
		return $rows;
	}


	/*****************/
	/* GET ALL USERS */
	/*****************/
	public function getAllUsers($except_uid=0) {
		$except_uid = (int)$except_uid;
		$sql = "SELECT ".$this->db->quoteId('uid').", ".$this->db->quoteId('firstname').", ".$this->db->quoteId('lastname').", ".$this->db->quoteId('uname').", ".$this->db->quoteId('gid').", ".$this->db->quoteId('email')
		."\n FROM ".$this->db->quoteId('#__users')
		."\n WHERE ".$this->db->quoteId('block')." = 0";
		if ($except_uid > 0) {
			$sql .= " AND ".$this->db->quoteId('uid')." <> ".$except_uid;
		}
		$sql .= "\n ORDER BY ".$this->db->quoteId('firstname')." ASC";
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$rows = $stmt->fetchAllAssoc('uid', PDO::FETCH_OBJ);
		return $rows;
	}

}

?>