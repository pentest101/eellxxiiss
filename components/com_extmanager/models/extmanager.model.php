<?php 
/**
* @version		$Id: extmanager.model.php 2423 2021-09-25 19:10:27Z IOS $
* @package		Elxis
* @subpackage	Component Extensions Manager
* @copyright	Copyright (c) 2006-2021 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class extmanagerModel {

	private $db;


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		$this->db = eFactory::getDB();
	}


	/**************************/
	/* GET TEMPLATE POSITIONS */
	/**************************/
	public function getPositions() {
		$sql = "SELECT ".$this->db->quoteId('position')." FROM ".$this->db->quoteId('#__template_positions')." ORDER BY ".$this->db->quoteId('position')." ASC";
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		return $stmt->fetchCol(0);
	}


	/*****************/
	/* COUNT MODULES */
	/*****************/
	public function countModules($options) {
		$wheres = array();
		$pdo_binds = array();

		if (isset($options['section']) && ($options['section'] != '')) {
			$wheres[] = $this->db->quoteId('section').' = :xsect';
			$pdo_binds[':xsect'] = array($options['section'], PDO::PARAM_STR);
		}

		if (isset($options['position']) && ($options['position'] != '')) {
			$wheres[] = $this->db->quoteId('position').' = :xpos';
			$pdo_binds[':xpos'] = array($options['position'], PDO::PARAM_STR);
		}

		if (isset($options['key']) && ($options['key'] != '')) {
			$v = '%'.$options['key'].'%';
			$wheres[] = '('.$this->db->quoteId('title').' LIKE :xkey OR '.$this->db->quoteId('module').' LIKE :xkey OR '.$this->db->quoteId('position').' LIKE :xkey)';
			$pdo_binds[':xkey'] = array($v, PDO::PARAM_STR);
		}

		$sql = "SELECT COUNT(id) FROM ".$this->db->quoteId('#__modules');
		if (count($wheres) > 0) {
			$sql .= ' WHERE '.implode(' AND ', $wheres);
			$stmt = $this->db->prepare($sql);
			if (count($pdo_binds) > 0) {
				foreach ($pdo_binds as $key => $parr) {
					$stmt->bindParam($key, $parr[0], $parr[1]);
				}
			}
		} else {
			$stmt = $this->db->prepare($sql);
		}
		$stmt->execute();
		return (int)$stmt->fetchResult();
	}


	/*****************************/
	/* GET MODULES FROM DATABASE */
	/*****************************/
	public function getModules($options) {
		$wheres = array();
		$pdo_binds = array();

		if (isset($options['section']) && ($options['section'] != '')) {
			$wheres[] = $this->db->quoteId('section').' = :xsect';
			$pdo_binds[':xsect'] = array($options['section'], PDO::PARAM_STR);
		}

		if (isset($options['position']) && ($options['position'] != '')) {
			$wheres[] = $this->db->quoteId('position').' = :xpos';
			$pdo_binds[':xpos'] = array($options['position'], PDO::PARAM_STR);
		}

		if (isset($options['key']) && ($options['key'] != '')) {
			$v = '%'.$options['key'].'%';
			$wheres[] = '('.$this->db->quoteId('title').' LIKE :xkey OR '.$this->db->quoteId('module').' LIKE :xkey OR '.$this->db->quoteId('position').' LIKE :xkey)';
			$pdo_binds[':xkey'] = array($v, PDO::PARAM_STR);
		}

		if ($options['sn'] == 'position') {
			$orderby = $this->db->quoteId('position').' '.strtoupper($options['so']).', '.$this->db->quoteId('ordering').' '.strtoupper($options['so']);
		} else {
			$orderby = $this->db->quoteId($options['sn']).' '.strtoupper($options['so']);
		}

		$sql = "SELECT * FROM ".$this->db->quoteId('#__modules');
		if (count($wheres) > 0) {
			$sql .= ' WHERE '.implode(' AND ', $wheres);
			$sql .= ' ORDER BY '.$orderby;
			$stmt = $this->db->prepareLimit($sql, $options['limitstart'], $options['limit']);
			if (count($pdo_binds) > 0) {
				foreach ($pdo_binds as $key => $parr) {
					$stmt->bindParam($key, $parr[0], $parr[1]);
				}
			}
		} else {
			$sql .= ' ORDER BY '.$orderby;
			$stmt = $this->db->prepareLimit($sql, $options['limitstart'], $options['limit']);
		}

		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_OBJ);
		return $rows;
	}


	/***********************************/
	/* PUBLISH/UNPUBLISH/TOGGLE MODULE */
	/***********************************/
	public function publishModule($id, $publish=-1) {
		$response = array('success' => false, 'message' => 'Unknown error', 'newpublished' => -1);
		if ($id < 1) { $response['message'] = 'Module not found!'; return $response; } //just in case

		if ($publish == -1) { //toggle status
			$sql = "SELECT ".$this->db->quoteId('published')." FROM ".$this->db->quoteId('#__modules')
			."\n WHERE ".$this->db->quoteId('id')." = :xid";
			$stmt = $this->db->prepareLimit($sql, 0, 1);
			$stmt->bindParam(':xid', $id, PDO::PARAM_INT);
			$stmt->execute();
			$publish = ((int)$stmt->fetchResult() == 1) ? 0 : 1;
		}

		$pubdate = '2014-01-01 00:00:00';
		$unpubdate = '2060-01-01 00:00:00';

		$sql = "UPDATE ".$this->db->quoteId('#__modules')." SET ".$this->db->quoteId('published')." = :xpub, ".$this->db->quoteId('pubdate')." = :xpdt, ".$this->db->quoteId('unpubdate')." = :xupdt"
		."\n WHERE ".$this->db->quoteId('id')." = :xid";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xpub', $publish, PDO::PARAM_INT);
		$stmt->bindParam(':xpdt', $pubdate, PDO::PARAM_STR);
		$stmt->bindParam(':xupdt', $unpubdate, PDO::PARAM_STR);
		$stmt->bindParam(':xid', $id, PDO::PARAM_INT);
		$ok = $stmt->execute();
		if ($ok) {
			$response['newpublished'] = $publish;
			$response['success'] = true;
			$response['message'] = 'Success';
		} else {
			$response['message'] = $stmt->getErrorMsg();
		}
		return $response;
	}


	/*********************/
	/* COPY MODULE'S ACL */
	/*********************/
	public function copyModuleACL($module, $source_id, $newid) {
		$newid = (int)$newid;
		if ($newid < 1) { return false; } //just in case

		$ctg = 'module';
		$sql = "SELECT * FROM ".$this->db->quoteId('#__acl')." WHERE ".$this->db->quoteId('category')." = :xcat"
		."\n AND ".$this->db->quoteId('element')." = :xelem AND ".$this->db->quoteId('identity')." = :xident";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xcat', $ctg, PDO::PARAM_STR);
		$stmt->bindParam(':xelem', $module, PDO::PARAM_STR);
		$stmt->bindParam(':xident', $source_id, PDO::PARAM_INT);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_OBJ);
		if (!$rows) { return true; } //nothing to copy

		$sql = "INSERT INTO ".$this->db->quoteId('#__acl')
		."\n (".$this->db->quoteId('id').", ".$this->db->quoteId('category').", ".$this->db->quoteId('element').", ".$this->db->quoteId('identity').", "
		.$this->db->quoteId('action').", ".$this->db->quoteId('minlevel').", ".$this->db->quoteId('gid').", ".$this->db->quoteId('uid').", ".$this->db->quoteId('aclvalue').")"
		."\n VALUES (NULL, :xcol2, :xcol3, :xcol4, :xcol5, :xcol6, :xcol7, :xcol8, :xcol9)";
		$stmt = $this->db->prepare($sql);
		foreach ($rows as $row) {
			$stmt->bindParam(':xcol2', $row->category, PDO::PARAM_STR);
			$stmt->bindParam(':xcol3', $row->element, PDO::PARAM_STR);
			$stmt->bindParam(':xcol4', $newid, PDO::PARAM_INT);
			$stmt->bindParam(':xcol5', $row->action, PDO::PARAM_STR);
			$stmt->bindParam(':xcol6', $row->minlevel, PDO::PARAM_INT);
			$stmt->bindParam(':xcol7', $row->gid, PDO::PARAM_INT);
			$stmt->bindParam(':xcol8', $row->uid, PDO::PARAM_INT);
			$stmt->bindParam(':xcol9', $row->aclvalue, PDO::PARAM_INT);
			$stmt->execute();
		}
		return true;
	}


	/******************************/
	/* COPY MODULE'S TRANSLATIONS */
	/******************************/
	public function copyModuleTranslations($source_id, $newid) {
		$newid = (int)$newid;
		if ($newid < 1) { return false; } //just in case

		$ctg = 'module';
		$sql = "SELECT * FROM ".$this->db->quoteId('#__translations')." WHERE ".$this->db->quoteId('category')." = :xcat"
		."\n AND ".$this->db->quoteId('elid')." = :xid";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xcat', $ctg, PDO::PARAM_STR);
		$stmt->bindParam(':xid', $source_id, PDO::PARAM_INT);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_OBJ);
		if (!$rows) { return true; } //nothing to copy

		$sql = "INSERT INTO ".$this->db->quoteId('#__translations')
		."\n (".$this->db->quoteId('trid').", ".$this->db->quoteId('category').", ".$this->db->quoteId('element').", ".$this->db->quoteId('language').", "
		.$this->db->quoteId('elid').", ".$this->db->quoteId('translation').")"
		."\n VALUES (NULL, :xcol2, :xcol3, :xcol4, :xcol5, :xcol6)";
		$stmt = $this->db->prepare($sql);
		foreach ($rows as $row) {
			$stmt->bindParam(':xcol2', $row->category, PDO::PARAM_STR);
			$stmt->bindParam(':xcol3', $row->element, PDO::PARAM_STR);
			$stmt->bindParam(':xcol4', $row->language, PDO::PARAM_STR);
			$stmt->bindParam(':xcol5', $newid, PDO::PARAM_INT);
			$stmt->bindParam(':xcol6', $row->translation, PDO::PARAM_LOB);
			$stmt->execute();
		}
		return true;
	}


	/**************************/
	/* COUNT MODULE INSTANCES */
	/**************************/
	public function countModuleInstances($module) {
		$sql = "SELECT COUNT(id) FROM ".$this->db->quoteId('#__modules')." WHERE ".$this->db->quoteId('module')." = :xmod";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xmod', $module, PDO::PARAM_STR);
		$stmt->execute();
		return (int)$stmt->fetchResult();
	}


	/*********************************************/
	/* DELETE MODULE (USED FOR MODULE INSTANCES) */
	/*********************************************/
	public function deleteModule($id, $module) {
		$id = (int)$id;
		if ($id < 1) { return false; }

		$stmt = $this->db->prepare("DELETE FROM ".$this->db->quoteId('#__modules')." WHERE ".$this->db->quoteId('id')." = :xid");
		$stmt->bindParam(':xid', $id, PDO::PARAM_INT);
		$stmt->execute();

		$stmt = $this->db->prepare("DELETE FROM ".$this->db->quoteId('#__modules_menu')." WHERE ".$this->db->quoteId('moduleid')." = :xid");
		$stmt->bindParam(':xid', $id, PDO::PARAM_INT);
		$stmt->execute();

		$ctg = 'module';
		$sql = "DELETE FROM ".$this->db->quoteId('#__acl')
		."\n WHERE ".$this->db->quoteId('category')." = :xcat AND ".$this->db->quoteId('element')." = :xelem AND ".$this->db->quoteId('identity')." = :xident";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xcat', $ctg, PDO::PARAM_STR);
		$stmt->bindParam(':xelem', $module, PDO::PARAM_STR);
		$stmt->bindParam(':xident', $id, PDO::PARAM_INT);
		$stmt->execute();

		$sql = "DELETE FROM ".$this->db->quoteId('#__translations')
		."\n WHERE ".$this->db->quoteId('category')." = :xcat AND ".$this->db->quoteId('elid')." = :xelid";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xcat', $ctg, PDO::PARAM_STR);
		$stmt->bindParam(':xelid', $id, PDO::PARAM_INT);
		$stmt->execute();

		return true;
	}


	/****************************************/
	/* GET MODULES ACL PERMISSIONS FOR VIEW */
	/****************************************/
	public function getModulesViewACL($ids) {
		$ctg = 'module';
		$act = 'view';
		$sql = "SELECT * FROM ".$this->db->quoteId('#__acl')." WHERE ".$this->db->quoteId('category')." = :xcat AND ".$this->db->quoteId('action')." = :xact"
		."\n AND ".$this->db->quoteId('identity')." IN (".implode(', ', $ids).")";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xcat', $ctg, PDO::PARAM_STR);
		$stmt->bindParam(':xact', $act, PDO::PARAM_STR);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		$final = array();
		if ($rows) {
			foreach ($rows as $row) {
				$id = $row['identity'];
				$num = (isset($final[$id])) ? $final[$id]['num'] : 0;
				$num++;
				$final[$id] = array(
					'minlevel' => $row['minlevel'],
					'gid' => $row['gid'],
					'uid' => $row['uid'],
					'aclvalue' => $row['aclvalue'],
					'num' => $num
				);
			}
		}

		unset($rows);
		return $final;
	}


	/************************************/
	/* GET USER GROUPS AND THEIR LEVELS */
	/************************************/
	public function getGroups($orderby='level', $orderdir='ASC') {
		if ($orderby == '') { $orderby == 'level'; }
		if ($orderdir == '') { $orderdir = 'ASC'; }
		$sql = "SELECT * FROM ".$this->db->quoteId('#__groups')." ORDER BY ".$this->db->quoteId($orderby)." ".$orderdir;
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $rows;
	}


	/*****************************************/
	/* GET ACL ROWS THAT MATCH WHERE CLAUSES */
	/*****************************************/
	public function queryACL($wheres) {
		$ands = array();
		$binds = array();
		if ($wheres) {
			$i = 0;
			foreach ($wheres as $col => $val) {
				switch ($col) {
					case 'id': case 'identity': case 'minlevel': case 'gid': case 'uid': case 'aclvalue':
						$i++;
						$ands[] = $this->db->quoteId($col).' = :xcol'.$i;
						$binds[] = array(':xcol'.$i, intval($val), PDO::PARAM_INT);
					break;
					case 'category': case 'element': case 'action': 
						$i++;
						$ands[] = $this->db->quoteId($col).' = :xcol'.$i;
						$binds[] = array(':xcol'.$i, $val, PDO::PARAM_STR);
					break;
					default: break;
				}
			}
		}

		$sql = "SELECT * FROM ".$this->db->quoteId('#__acl');
		if ($ands) { $sql .= "\n WHERE ".implode(' AND ', $ands); }
		$sql .= "\n ORDER BY ".$this->db->quoteId('category')." ASC, ".$this->db->quoteId('element')." ASC, ".$this->db->quoteId('action')." ASC";
		$stmt = $this->db->prepare($sql);
		if ($binds) {
			foreach ($binds as $bind) {
				$stmt->bindParam($bind[0], $bind[1], $bind[2]);
			}
		}
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_OBJ);
	}


	/***************************/
	/* GET MODULES BY POSITION */
	/***************************/
	public function getModsByPosition($position) {
		$sql = "SELECT ".$this->db->quoteId('id').", ".$this->db->quoteId('title').", ".$this->db->quoteId('ordering')
		."\n FROM ".$this->db->quoteId('#__modules')." WHERE ".$this->db->quoteId('position')." = :xpos"
		."\n ORDER BY ".$this->db->quoteId('ordering')." ASC";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xpos', $position, PDO::PARAM_STR);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_OBJ);
	}


	/***************/
	/* COUNT USERS */
	/***************/
	public function countUsers() {
		$sql = "SELECT COUNT(uid) FROM ".$this->db->quoteId('#__users');
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		return (int)$stmt->fetchResult();
	}


	/***************************/
	/* GET USERS FROM DATABASE */
	/***************************/
	public function getUsers() {
		$orderby = (eFactory::getElxis()->getConfig('REALNAME') == 1) ? 'firstname' : 'uname';
		$sql = "SELECT ".$this->db->quoteId('uid').", ".$this->db->quoteId('firstname').", ".$this->db->quoteId('lastname').", ".$this->db->quoteId('uname')
		."\n FROM ".$this->db->quoteId('#__users')." ORDER BY ".$this->db->quoteId($orderby)." ASC";
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $rows;
	}


	/******************/
	/* GET MENU ITEMS */
	/******************/
	public function getMenuItems($section) {
		$xlin = 'link';
		$xwra = 'wrapper';
		$sql = "SELECT ".$this->db->quoteId('menu_id').", ".$this->db->quoteId('title').", ".$this->db->quoteId('collection')
		."\n FROM ".$this->db->quoteId('#__menu')
		."\n WHERE ".$this->db->quoteId('section')." = :xsec AND (".$this->db->quoteId('menu_type')." = :xlin OR ".$this->db->quoteId('menu_type')." = :xwra)"
		."\n ORDER BY ".$this->db->quoteId('collection')." ASC, ".$this->db->quoteId('parent_id')." ASC, ".$this->db->quoteId('ordering')." ASC";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xsec', $section, PDO::PARAM_STR);
		$stmt->bindParam(':xlin', $xlin, PDO::PARAM_STR);
		$stmt->bindParam(':xwra', $xwra, PDO::PARAM_STR);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $rows;
	}


	/*************************/
	/* GET MODULE MENU ITEMS */
	/*************************/
	public function getModMenuItems($moduleid) {
		$xlin = 'link';
		$xwra = 'wrapper';
		$sql = "SELECT ".$this->db->quoteId('menuid')." FROM ".$this->db->quoteId('#__modules_menu')
		."\n WHERE ".$this->db->quoteId('moduleid')." = :xmod";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xmod', $moduleid, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchCol();
	}


	/****************************/
	/* DELETE MODULE MENU ITEMS */
	/****************************/
	public function deleteModMenus($moduleid, $mitems) {
		if (!$mitems) { return; }
		$sql = "DELETE FROM ".$this->db->quoteId('#__modules_menu')
		."\n WHERE ".$this->db->quoteId('moduleid')." = :xmod AND ".$this->db->quoteId('menuid')." = :xmen";
		$stmt = $this->db->prepare($sql);
		foreach ($mitems as $menuid) {
			$stmt->bindParam(':xmod', $moduleid, PDO::PARAM_INT);
			$stmt->bindParam(':xmen', $menuid, PDO::PARAM_INT);
			$stmt->execute();
		}
	}


	/****************************/
	/* INSERT MODULE MENU ITEMS */
	/****************************/
	public function insertModMenus($moduleid, $mitems) {
		if (!$mitems) { return; }
		$sql = "INSERT INTO ".$this->db->quoteId('#__modules_menu')
		."\n (".$this->db->quoteId('mmid').", ".$this->db->quoteId('moduleid').", ".$this->db->quoteId('menuid').")"
		."\n VALUES (NULL, :xmod, :xmen)";
		$stmt = $this->db->prepare($sql);
		foreach ($mitems as $menuid) {
			$stmt->bindParam(':xmod', $moduleid, PDO::PARAM_INT);
			$stmt->bindParam(':xmen', $menuid, PDO::PARAM_INT);
			$stmt->execute();
		}
	}


	/********************/
	/* COUNT COMPONENTS */
	/********************/
	public function countComponents($options) {
		$wheres = array();
		$pdo_binds = array();

		if (isset($options['key']) && ($options['key'] != '')) {
			$v = '%'.$options['key'].'%';
			$wheres[] = '('.$this->db->quoteId('name').' LIKE :xkey OR '.$this->db->quoteId('component').' LIKE :xkey)';
			$pdo_binds[':xkey'] = array($v, PDO::PARAM_STR);
		}

		$sql = "SELECT COUNT(id) FROM ".$this->db->quoteId('#__components');
		if (count($wheres) > 0) {
			$sql .= ' WHERE '.implode(' AND ', $wheres);
			$stmt = $this->db->prepare($sql);
			if (count($pdo_binds) > 0) {
				foreach ($pdo_binds as $key => $parr) {
					$stmt->bindParam($key, $parr[0], $parr[1]);
				}
			}
		} else {
			$stmt = $this->db->prepare($sql);
		}
		$stmt->execute();
		return (int)$stmt->fetchResult();
	}


	/********************************/
	/* GET COMPONENTS FROM DATABASE */
	/********************************/
	public function getComponents($options) {
		$wheres = array();
		$pdo_binds = array();

		if (isset($options['key']) && ($options['key'] != '')) {
			$v = '%'.$options['key'].'%';
			$wheres[] = '('.$this->db->quoteId('name').' LIKE :xkey OR '.$this->db->quoteId('component').' LIKE :xkey)';
			$pdo_binds[':xkey'] = array($v, PDO::PARAM_STR);
		}

		$sql = "SELECT * FROM ".$this->db->quoteId('#__components');
		if (count($wheres) > 0) {
			$sql .= ' WHERE '.implode(' AND ', $wheres);
		}
		$sql .= "\n ORDER BY ".$this->db->quoteId($options['sn']).' '.strtoupper($options['so']);
		$stmt = $this->db->prepareLimit($sql, $options['limitstart'], $options['limit']);
		if (count($pdo_binds) > 0) {
			foreach ($pdo_binds as $key => $parr) {
				$stmt->bindParam($key, $parr[0], $parr[1]);
			}
		}
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_OBJ);
		return $rows;
	}


	/*******************/
	/* COUNT TEMPLATES */
	/*******************/
	public function countTemplates($options) {
		$wheres = array();
		$pdo_binds = array();

		if (isset($options['section'])) {
			$section = trim($options['section']);
			if ($section != '') {
				$wheres[] = $this->db->quoteId('section').' = :sect';
				$pdo_binds[':sect'] = array($section, PDO::PARAM_STR);
			}
		}

		if (isset($options['key']) && ($options['key'] != '')) {
			$v = '%'.$options['key'].'%';
			$wheres[] = '('.$this->db->quoteId('title').' LIKE :xkey OR '.$this->db->quoteId('template').' LIKE :xkey)';
			$pdo_binds[':xkey'] = array($v, PDO::PARAM_STR);
		}

		$sql = "SELECT COUNT(id) FROM ".$this->db->quoteId('#__templates');
		if (count($wheres) > 0) {
			$sql .= ' WHERE '.implode(' AND ', $wheres);
		}
		$stmt = $this->db->prepare($sql);
		if (count($pdo_binds) > 0) {
			foreach ($pdo_binds as $key => $parr) {
				$stmt->bindParam($key, $parr[0], $parr[1]);
			}
		}
		$stmt->execute();
		return (int)$stmt->fetchResult();
	}


	/*******************************/
	/* GET TEMPLATES FROM DATABASE */
	/*******************************/
	public function getTemplates($options) {
		$wheres = array();
		$pdo_binds = array();

		if (isset($options['section'])) {
			$section = trim($options['section']);
			if ($section != '') {
				$wheres[] = $this->db->quoteId('section').' = :sect';
				$pdo_binds[':sect'] = array($section, PDO::PARAM_STR);
			}
		}

		if (isset($options['key']) && ($options['key'] != '')) {
			$v = '%'.$options['key'].'%';
			$wheres[] = '('.$this->db->quoteId('title').' LIKE :xkey OR '.$this->db->quoteId('template').' LIKE :xkey)';
			$pdo_binds[':xkey'] = array($v, PDO::PARAM_STR);
		}

		$sql = "SELECT * FROM ".$this->db->quoteId('#__templates');
		if (count($wheres) > 0) {
			$sql .= ' WHERE '.implode(' AND ', $wheres);
		}
		$sql .= ' ORDER BY '.$this->db->quoteId($options['sn']).' '.strtoupper($options['so']);
		$stmt = $this->db->prepareLimit($sql, $options['limitstart'], $options['limit']);
		if (count($pdo_binds) > 0) {
			foreach ($pdo_binds as $key => $parr) {
				$stmt->bindParam($key, $parr[0], $parr[1]);
			}
		}
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_OBJ);
		return $rows;
	}


	/*******************/
	/* COUNT POSITIONS */
	/*******************/
	public function countPositions() {
		$sql = "SELECT COUNT(id) FROM ".$this->db->quoteId('#__template_positions');
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		return (int)$stmt->fetchResult();
	}


	/****************************************/
	/* GET TEMPLATE POSITIONS FROM DATABASE */
	/****************************************/
	public function getFullPositions($options) {
		$section = 'frontend';

		$sql = "SELECT p.*, (SELECT COUNT(m.id) FROM ".$this->db->quoteId('#__modules')." m WHERE m.section = :xsec AND m.position= p.position) AS modules "
		."\n FROM ".$this->db->quoteId('#__template_positions')." p"
		."\n ORDER BY ".$this->db->quoteId($options['sn']).' '.strtoupper($options['so']);
		$stmt = $this->db->prepareLimit($sql, $options['limitstart'], $options['limit']);
		$stmt->bindParam(':xsec', $section, PDO::PARAM_STR);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_OBJ);

		return $rows;
	}


	/**********************************************/
	/* COUNT ROWS HAVING A SPECIFIC POSITION NAME */
	/**********************************************/
	public function countPositionName($position) {
		$sql = "SELECT COUNT(id) FROM ".$this->db->quoteId('#__template_positions');
		$sql .= "\n WHERE ".$this->db->quoteId('position')." = :xpos";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xpos', $position, PDO::PARAM_STR);
		$stmt->execute();
		return (int)$stmt->fetchResult();
	}


	/*****************************************************/
	/* UPDATE MODULES POSITIONS (DUE TO POSITION RENAME) */
	/*****************************************************/
	public function updateModulesPositions($oldname, $newposition) {
		$section = 'frontend';
		$sql = "UPDATE ".$this->db->quoteId('#__modules')." SET ".$this->db->quoteId('position')." = :newpos"
		."\n WHERE ".$this->db->quoteId('position')." = :oldpos AND ".$this->db->quoteId('section')." = :xsec";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':newpos', $newposition, PDO::PARAM_STR);
		$stmt->bindParam(':oldpos', $oldname, PDO::PARAM_STR);
		$stmt->bindParam(':xsec', $section, PDO::PARAM_STR);
		$stmt->execute();
	}


	/*****************/
	/* COUNT ENGINES */
	/*****************/
	public function countEngines($options) {
		$wheres = array();
		$pdo_binds = array();

		if (isset($options['key']) && ($options['key'] != '')) {
			$v = '%'.$options['key'].'%';
			$wheres[] = '('.$this->db->quoteId('title').' LIKE :xkey OR '.$this->db->quoteId('engine').' LIKE :xkey)';
			$pdo_binds[':xkey'] = array($v, PDO::PARAM_STR);
		}

		$sql = "SELECT COUNT(id) FROM ".$this->db->quoteId('#__engines');
		if (count($wheres) > 0) {
			$sql .= ' WHERE '.implode(' AND ', $wheres);
		}
		$stmt = $this->db->prepare($sql);
		if (count($pdo_binds) > 0) {
			foreach ($pdo_binds as $key => $parr) {
				$stmt->bindParam($key, $parr[0], $parr[1]);
			}
		}
		$stmt->execute();
		return (int)$stmt->fetchResult();
	}


	/*****************************/
	/* GET ENGINES FROM DATABASE */
	/*****************************/
	public function getEngines($options) {
		$wheres = array();
		$pdo_binds = array();

		if (isset($options['key']) && ($options['key'] != '')) {
			$v = '%'.$options['key'].'%';
			$wheres[] = '('.$this->db->quoteId('title').' LIKE :xkey OR '.$this->db->quoteId('engine').' LIKE :xkey)';
			$pdo_binds[':xkey'] = array($v, PDO::PARAM_STR);
		}

		$sql = "SELECT * FROM ".$this->db->quoteId('#__engines');
		if (count($wheres) > 0) {
			$sql .= ' WHERE '.implode(' AND ', $wheres);
		}
		$sql .= ' ORDER BY '.$this->db->quoteId($options['sn']).' '.strtoupper($options['so']);
		$stmt = $this->db->prepareLimit($sql, $options['limitstart'], $options['limit']);
		if (count($pdo_binds) > 0) {
			foreach ($pdo_binds as $key => $parr) {
				$stmt->bindParam($key, $parr[0], $parr[1]);
			}
		}
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_OBJ);
		return $rows;
	}


	/***********************************/
	/* PUBLISH/UNPUBLISH/TOGGLE ENGINE */
	/***********************************/
	public function publishEngine($id, $publish=-1) {
		$response = array('success' => false, 'message' => 'Unknown error', 'newpublished' => -1);
		if ($id < 1) { $response['message'] = 'Engine not found!'; return $response; }
		if ($publish == -1) { //toggle status
			$sql = "SELECT ".$this->db->quoteId('published')." FROM ".$this->db->quoteId('#__engines')
			."\n WHERE ".$this->db->quoteId('id')." = :xid";
			$stmt = $this->db->prepareLimit($sql, 0, 1);
			$stmt->bindParam(':xid', $id, PDO::PARAM_INT);
			$stmt->execute();
			$publish = ((int)$stmt->fetchResult() == 1) ? 0 : 1;
		}

		$sql = "UPDATE ".$this->db->quoteId('#__engines')." SET ".$this->db->quoteId('published')." = :xpub"
		."\n WHERE ".$this->db->quoteId('id')." = :xid";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xpub', $publish, PDO::PARAM_INT);
		$stmt->bindParam(':xid', $id, PDO::PARAM_INT);
		$ok = $stmt->execute();
		if ($ok) {
			$response['success'] = true;
			$response['message'] = 'Success';
			$response['newpublished'] = $publish;
		} else {
			$response['message'] = $stmt->getErrorMsg();
		}
		return $response;
	}


	/*******************************/
	/* GET ALL ENGINES BY ORDERING */
	/*******************************/
	public function getAllEngines() {
		$sql = "SELECT ".$this->db->quoteId('id').", ".$this->db->quoteId('title').", ".$this->db->quoteId('ordering')
		."\n FROM ".$this->db->quoteId('#__engines')." ORDER BY ".$this->db->quoteId('ordering')." ASC";
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_OBJ);
	}


	/***********************/
	/* MAKE ENGINE DEFAULT */
	/***********************/
	public function setDefaultEngine($id) {
		$response = array('success' => false, 'message' => 'Unknown error');
		if ($id < 1) { $response['message'] = 'Engine not found!'; return $response; }

		$sql = "SELECT ".$this->db->quoteId('id').", ".$this->db->quoteId('published')." FROM ".$this->db->quoteId('#__engines')
		."\n WHERE ".$this->db->quoteId('id')." = :xid";
		$stmt = $this->db->prepareLimit($sql, 0, 1);
		$stmt->bindParam(':xid', $id, PDO::PARAM_INT);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if (!$row) { $response['message'] = 'Engine not found!'; return $response; }

		if ($row['published'] == 0) { $response['message'] = eFactory::getLang()->get('DEF_ENGINE_PUB'); return $response; }

		$sql = "UPDATE ".$this->db->quoteId('#__engines')." SET ".$this->db->quoteId('defengine')." = 0 WHERE ".$this->db->quoteId('id')." <> :xid";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xid', $id, PDO::PARAM_INT);
		$stmt->execute();

		$sql = "UPDATE ".$this->db->quoteId('#__engines')." SET ".$this->db->quoteId('defengine')." = 1 WHERE ".$this->db->quoteId('id')." = :xid";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xid', $id, PDO::PARAM_INT);
		$ok = $stmt->execute();
		if ($ok) {
			$response['success'] = true;
			$response['message'] = 'Success';
		} else {
			$response['message'] = $stmt->getErrorMsg();
		}
		return $response;
	}


	/**********************/
	/* COUNT AUTH METHODS */
	/**********************/
	public function countAuthMethods($options) {
		$wheres = array();
		$pdo_binds = array();
		if (isset($options['key']) && ($options['key'] != '')) {
			$v = '%'.$options['key'].'%';
			$wheres[] = '('.$this->db->quoteId('title').' LIKE :xkey OR '.$this->db->quoteId('auth').' LIKE :xkey)';
			$pdo_binds[':xkey'] = array($v, PDO::PARAM_STR);
		}

		$sql = "SELECT COUNT(id) FROM ".$this->db->quoteId('#__authentication');
		if (count($wheres) > 0) {
			$sql .= ' WHERE '.implode(' AND ', $wheres);
		}
		$stmt = $this->db->prepare($sql);
		if (count($pdo_binds) > 0) {
			foreach ($pdo_binds as $key => $parr) {
				$stmt->bindParam($key, $parr[0], $parr[1]);
			}
		}
		$stmt->execute();
		return (int)$stmt->fetchResult();
	}


	/**********************************/
	/* GET AUTH METHODS FROM DATABASE */
	/**********************************/
	public function getAuthMethods($options) {
		$wheres = array();
		$pdo_binds = array();
		if (isset($options['key']) && ($options['key'] != '')) {
			$v = '%'.$options['key'].'%';
			$wheres[] = '('.$this->db->quoteId('title').' LIKE :xkey OR '.$this->db->quoteId('auth').' LIKE :xkey)';
			$pdo_binds[':xkey'] = array($v, PDO::PARAM_STR);
		}

		$sql = "SELECT * FROM ".$this->db->quoteId('#__authentication');
		if (count($wheres) > 0) {
			$sql .= ' WHERE '.implode(' AND ', $wheres);
		}
		$sql .= ' ORDER BY '.$this->db->quoteId($options['sn']).' '.strtoupper($options['so']);
		$stmt = $this->db->prepareLimit($sql, $options['limitstart'], $options['limit']);
		if (count($pdo_binds) > 0) {
			foreach ($pdo_binds as $key => $parr) {
				$stmt->bindParam($key, $parr[0], $parr[1]);
			}
		}
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_OBJ);
		return $rows;
	}


	/****************************************/
	/* PUBLISH/UNPUBLISH/TOGGLE AUTH METHOD */
	/****************************************/
	public function publishAuth($id, $publish=-1) {
		$response = array('success' => false, 'message' => 'Unknown error', 'newpublished' => -1);
		if ($id < 1) { $response['message'] = 'Authentication method not found!'; return $response; }
		if ($publish == -1) { //toggle status
			$sql = "SELECT ".$this->db->quoteId('published')." FROM ".$this->db->quoteId('#__authentication')
			."\n WHERE ".$this->db->quoteId('id')." = :xid";
			$stmt = $this->db->prepareLimit($sql, 0, 1);
			$stmt->bindParam(':xid', $id, PDO::PARAM_INT);
			$stmt->execute();
			$publish = ((int)$stmt->fetchResult() == 1) ? 0 : 1;
		}

		$sql = "UPDATE ".$this->db->quoteId('#__authentication')." SET ".$this->db->quoteId('published')." = :xpub"
		."\n WHERE ".$this->db->quoteId('id')." = :xid";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xpub', $publish, PDO::PARAM_INT);
		$stmt->bindParam(':xid', $id, PDO::PARAM_INT);
		$ok = $stmt->execute();
		if ($ok) {
			$response['newpublished'] = $publish;
			$response['success'] = true;
			$response['message'] = 'Success';
		} else {
			$response['message'] = $stmt->getErrorMsg();
		}
		return $response;
	}


	/**********************************************/
	/* GET ALL AUTHENTICATION METHODS BY ORDERING */
	/**********************************************/
	public function getAllAuths() {
		$sql = "SELECT ".$this->db->quoteId('id').", ".$this->db->quoteId('title').", ".$this->db->quoteId('ordering')
		."\n FROM ".$this->db->quoteId('#__authentication')." ORDER BY ".$this->db->quoteId('ordering')." ASC";
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_OBJ);
	}


	/*****************/
	/* COUNT PLUGINS */
	/*****************/
	public function countPlugins($options) {
		$wheres = array();
		$pdo_binds = array();
		if (isset($options['key']) && ($options['key'] != '')) {
			$v = '%'.$options['key'].'%';
			$wheres[] = '('.$this->db->quoteId('title').' LIKE :xkey OR '.$this->db->quoteId('plugin').' LIKE :xkey)';
			$pdo_binds[':xkey'] = array($v, PDO::PARAM_STR);
		}

		$sql = "SELECT COUNT(id) FROM ".$this->db->quoteId('#__plugins');
		if (count($wheres) > 0) {
			$sql .= ' WHERE '.implode(' AND ', $wheres);
		}
		$stmt = $this->db->prepare($sql);
		if (count($pdo_binds) > 0) {
			foreach ($pdo_binds as $key => $parr) {
				$stmt->bindParam($key, $parr[0], $parr[1]);
			}
		}
		$stmt->execute();
		return (int)$stmt->fetchResult();
	}


	/*****************************/
	/* GET PLUGINS FROM DATABASE */
	/*****************************/
	public function getPlugins($options) {
		$wheres = array();
		$pdo_binds = array();
		if (isset($options['key']) && ($options['key'] != '')) {
			$v = '%'.$options['key'].'%';
			$wheres[] = '('.$this->db->quoteId('title').' LIKE :xkey OR '.$this->db->quoteId('plugin').' LIKE :xkey)';
			$pdo_binds[':xkey'] = array($v, PDO::PARAM_STR);
		}

		$sql = "SELECT * FROM ".$this->db->quoteId('#__plugins');
		if (count($wheres) > 0) {
			$sql .= ' WHERE '.implode(' AND ', $wheres);
		}
		$sql .= ' ORDER BY '.$this->db->quoteId($options['sn']).' '.strtoupper($options['so']);
		$stmt = $this->db->prepareLimit($sql, $options['limitstart'], $options['limit']);
		if (count($pdo_binds) > 0) {
			foreach ($pdo_binds as $key => $parr) {
				$stmt->bindParam($key, $parr[0], $parr[1]);
			}
		}
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_OBJ);
		return $rows;
	}


	/***********************************/
	/* PUBLISH/UNPUBLISH/TOGGLE PLUGIN */
	/***********************************/
	public function publishPlugin($id, $publish=-1) {
		$response = array('success' => false, 'message' => 'Unknown error', 'newpublished' => -1);
		if ($id < 1) { $response['message'] = 'Plugin not found!'; return $response; }
		if ($publish == -1) { //toggle status
			$sql = "SELECT ".$this->db->quoteId('published')." FROM ".$this->db->quoteId('#__plugins')
			."\n WHERE ".$this->db->quoteId('id')." = :xid";
			$stmt = $this->db->prepareLimit($sql, 0, 1);
			$stmt->bindParam(':xid', $id, PDO::PARAM_INT);
			$stmt->execute();
			$publish = ((int)$stmt->fetchResult() == 1) ? 0 : 1;
		}

		$sql = "UPDATE ".$this->db->quoteId('#__plugins')." SET ".$this->db->quoteId('published')." = :xpub"
		."\n WHERE ".$this->db->quoteId('id')." = :xid";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xpub', $publish, PDO::PARAM_INT);
		$stmt->bindParam(':xid', $id, PDO::PARAM_INT);
		$ok = $stmt->execute();
		if ($ok) {
			$response['newpublished'] = $publish;
			$response['success'] = true;
			$response['message'] = 'Success';
		} else {
			$response['message'] = $stmt->getErrorMsg();
		}
		return $response;
	}


	/*******************************/
	/* GET ALL PLUGINS BY ORDERING */
	/*******************************/
	public function getAllPlugins() {
		$sql = "SELECT ".$this->db->quoteId('id').", ".$this->db->quoteId('title').", ".$this->db->quoteId('ordering')
		."\n FROM ".$this->db->quoteId('#__plugins')." ORDER BY ".$this->db->quoteId('ordering')." ASC";
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_OBJ);
	}


	/****************************/
	/* GET COMPONENT PARAMETERS */
	/****************************/
	public function componentParams() {
		$sql = "SELECT ".$this->db->quoteId('params')." FROM ".$this->db->quoteId('#__components')
		."\n WHERE ".$this->db->quoteId('component')." = ".$this->db->quote('com_extmanager');
		$stmt = $this->db->prepareLimit($sql, 0, 1);
		$stmt->execute();
		return (string)$stmt->fetchResult();
	}


	/************************************/
	/* SAVE COMPONENT PARAMETERS STRING */
	/************************************/
	public function saveComponentParams($str) {
		$comp = 'com_extmanager';
		$sql = "UPDATE ".$this->db->quoteId('#__components')." SET  ".$this->db->quoteId('params')." = :xpar"
		."\n WHERE ".$this->db->quoteId('component')." = :xcomp";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xpar', $str, PDO::PARAM_STR);
		$stmt->bindParam(':xcomp', $comp, PDO::PARAM_STR);
		$ok = $stmt->execute();
		return $ok;
	}


	/****************************************/
	/* GET INSTALLED THIRD PARTY EXTENSIONS */
	/****************************************/
	public function getThirdExtensions() {
		$sql = "SELECT ".$this->db->quoteId('auth')." FROM ".$this->db->quoteId('#__authentication')." WHERE ".$this->db->quoteId('iscore').' = 0';
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$auths = $stmt->fetchCol();
		
		$sql = "SELECT ".$this->db->quoteId('component')." FROM ".$this->db->quoteId('#__components')." WHERE ".$this->db->quoteId('iscore').' = 0';
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$components = $stmt->fetchCol();
		
		$sql = "SELECT ".$this->db->quoteId('engine')." FROM ".$this->db->quoteId('#__engines')." WHERE ".$this->db->quoteId('iscore').' = 0';
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$engines = $stmt->fetchCol();

		$sql = "SELECT ".$this->db->quoteId('module')." FROM ".$this->db->quoteId('#__modules')." WHERE ".$this->db->quoteId('iscore').' = 0 GROUP BY '.$this->db->quoteId('module');
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$modules = $stmt->fetchCol();

		$sql = "SELECT ".$this->db->quoteId('plugin')." FROM ".$this->db->quoteId('#__plugins')." WHERE ".$this->db->quoteId('iscore').' = 0';
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$plugins = $stmt->fetchCol();

		$sql = "SELECT ".$this->db->quoteId('template')." FROM ".$this->db->quoteId('#__templates')." WHERE ".$this->db->quoteId('iscore').' = 0';
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$templates = $stmt->fetchCol();

		$extensions = array();
		if ($auths) {
			foreach ($auths as $auth) {
				$extensions[] = array('type' => 'auth', 'name' => $auth);
			}
		}
		if ($components) {
			foreach ($components as $component) {
				$extensions[] = array('type' => 'component', 'name' => $component);
			}
		}
		if ($engines) {
			foreach ($engines as $engine) {
				$extensions[] = array('type' => 'engine', 'name' => $engine);
			}
		}
		if ($modules) {
			foreach ($modules as $module) {
				$extensions[] = array('type' => 'module', 'name' => $module);
			}
		}
		if ($plugins) {
			foreach ($plugins as $plugin) {
				$extensions[] = array('type' => 'plugin', 'name' => $plugin);
			}
		}
		if ($templates) {
			foreach ($templates as $template) {
				$extensions[] = array('type' => 'template', 'name' => $template);
			}
		}

		return $extensions;
	}


	/***********************************/
	/* CHECK IF DATABASE IS UP-TO-DATE */
	/***********************************/
	public function dbisUptodate() {
		$elxis = eFactory::getElxis();
		$db = eFactory::getDB();

		//Elxis 5.0
		$sql = "SHOW COLUMNS FROM ".$db->quoteId('#__messages');
		$stmt = $db->prepare($sql);
		$stmt->execute();
		$columns = $stmt->fetchCol();
		if (!in_array('delbyto', $columns)) { return false; }//normally this should never happen

		return true;
	}


	/*************************/
	/* UPDATE ELXIS DATABASE */
	/*************************/
	public function updateDatabase() {
		$elxis = eFactory::getElxis();
		$db = eFactory::getDB();

		$elxis50_ok = false;
		$sql = "SHOW COLUMNS FROM ".$db->quoteId('#__messages');
		$stmt = $db->prepare($sql);
		$stmt->execute();
		$columns = $stmt->fetchCol();
		if (in_array('delbyto', $columns)) { $elxis50_ok = true; }

		if (!$elxis50_ok) {//normally this should never happen
			if ($elxis->getConfig('DB_TYPE') == 'pgsql') {
				$sql = "ALTER TABLE ".$db->quoteId('#__users')." ADD COLUMN ".$db->quoteId('lastclicks')." TEXT NULL";
			} else {
				$sql = "ALTER TABLE ".$db->quoteId('#__users')." ADD COLUMN ".$db->quoteId('lastclicks')." TEXT";
			}
			$stmt = $db->prepare($sql);
			$stmt->execute();

			$eFiles = eFactory::getFiles();

			//Create admin template position "adminside"
			$pos = 'adminside';
			$desc = 'Administration - Side column';
			$sql = "INSERT INTO ".$db->quoteId('#__template_positions')." VALUES (NULL, :xpos, :xdesc)";
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':xpos', $pos, PDO::PARAM_STR);
			$stmt->bindParam(':xdesc', $desc, PDO::PARAM_STR);
			$stmt->execute();

			//Update module adminprofile
			if (file_exists(ELXIS_PATH.'/modules/mod_adminprofile/css/')) {
				$eFiles->deleteFolder('modules/mod_adminprofile/css/');
				$eFiles->deleteFile('modules/mod_adminprofile/logo.png');
			}

			//Set module "adminprofile" to position "adminside"
			$pos = 'adminside';
			$mod = 'mod_adminprofile';
			$sql = "UPDATE ".$db->quoteId('#__modules')." SET ".$db->quoteId('ordering')." = 1, ".$db->quoteId('position')." = :xpos, ".$db->quoteId('published')." = 1"
			."\n WHERE ".$db->quoteId('module')." = :xmod";
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':xpos', $pos, PDO::PARAM_STR);
			$stmt->bindParam(':xmod', $mod, PDO::PARAM_STR);
			$stmt->execute();

			//Uninstall component "Languages"
			$eFiles->deleteFolder('components/com_languages/');

			$comp = 'com_languages';
			$sql = "DELETE FROM ".$db->quoteId('#__components')." WHERE ".$db->quoteId('component')." = :xcomp";
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':xcomp', $comp, PDO::PARAM_STR);
			$stmt->execute();

			$sql = "DELETE FROM ".$db->quoteId('#__acl')." WHERE ".$db->quoteId('element')." = :xcomp";
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':xcomp', $comp, PDO::PARAM_STR);
			$stmt->execute();

			//Uninstall module "mod_mobilefront", resmobile from frontpage and mobile template positions
			$eFiles->deleteFolder('modules/mod_mobilefront/');

			$mod = 'mod_mobilefront';
			$sql = "DELETE FROM ".$db->quoteId('#__modules')." WHERE ".$db->quoteId('module')." = :xmod";
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':xmod', $mod, PDO::PARAM_STR);
			$stmt->execute();
			$sql = "DELETE FROM ".$db->quoteId('#__acl')." WHERE ".$db->quoteId('element')." = :xmod";
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':xmod', $mod, PDO::PARAM_STR);
			$stmt->execute();
			$v = 'resmobile';
			$sql = "DELETE FROM ".$db->quoteId('#__frontpage')." WHERE ".$db->quoteId('pname')." = :xv";
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':xv', $v, PDO::PARAM_STR);
			$stmt->execute();
			$v = 'mobilefront';
			$sql = "DELETE FROM ".$db->quoteId('#__template_positions')." WHERE ".$db->quoteId('position')." = :xv";
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':xv', $v, PDO::PARAM_STR);
			$stmt->execute();
			$v = 'mobiletop';
			$stmt->bindParam(':xv', $v, PDO::PARAM_STR);
			$stmt->execute();
			$v = 'mobilebottom';
			$stmt->bindParam(':xv', $v, PDO::PARAM_STR);
			$stmt->execute();

			if ($elxis->getConfig('DB_TYPE') == 'pgsql') {
				$sql = "ALTER TABLE ".$db->quoteId('#__menu')." ADD COLUMN ".$db->quoteId('iconfont')." VARCHAR(40) NULL";
			} else {
				$sql = "ALTER TABLE ".$db->quoteId('#__menu')." ADD COLUMN ".$db->quoteId('iconfont')." VARCHAR(40) DEFAULT NULL";
			}
			$stmt = $db->prepare($sql);
			$stmt->execute();

			//Update personal messages
			if ($elxis->getConfig('DB_TYPE') == 'pgsql') {
				$sql = "ALTER TABLE ".$db->quoteId('#__messages')." ADD COLUMN ".$db->quoteId('replyto')." SMALLINT NOT NULL DEFAULT 0";
				$sql2 = "ALTER TABLE ".$db->quoteId('#__messages')." ADD COLUMN ".$db->quoteId('delbyfrom')." INTEGER NOT NULL DEFAULT 0";
				$sql3 = "ALTER TABLE ".$db->quoteId('#__messages')." ADD COLUMN ".$db->quoteId('delbyto')." INTEGER NOT NULL DEFAULT 0";
			} else {
				$sql = "ALTER TABLE ".$db->quoteId('#__messages')." ADD COLUMN ".$db->quoteId('replyto')." INT(10) UNSIGNED NOT NULL DEFAULT '0'";
				$sql2 = "ALTER TABLE ".$db->quoteId('#__messages')." ADD COLUMN ".$db->quoteId('delbyfrom')." TINYINT(2) UNSIGNED NOT NULL DEFAULT '0'";
				$sql3 = "ALTER TABLE ".$db->quoteId('#__messages')." ADD COLUMN ".$db->quoteId('delbyto')." TINYINT(2) UNSIGNED NOT NULL DEFAULT '0'";
			}
			$stmt = $db->prepare($sql);
			$stmt->execute();
			$stmt = $db->prepare($sql2);
			$stmt->execute();
			$stmt = $db->prepare($sql3);
			$stmt->execute();

			//Make past default templates not core in order to be able to be uninstalled
			$templates = array('flex', 'delta', 'aiolos');
			$sql = "UPDATE ".$db->quoteId('#__templates')." SET ".$db->quoteId('iscore')." = 0 WHERE ".$db->quoteId('template')." = :xtpl";
			$stmt = $db->prepare($sql);
			foreach ($templates as $tpl) {
				$stmt->bindParam(':xtpl', $tpl, PDO::PARAM_STR);
				$stmt->execute();				
			}

			if (file_exists(ELXIS_PATH.'/templates/five/index.php')) { //Install template Five
				$tpl = 'five';
				$sql = "SELECT COUNT(id) FROM ".$db->quoteId('#__templates')." WHERE ".$db->quoteId('template')." = :xtpl";
				$stmt = $db->prepare($sql);
				$stmt->bindParam(':xtpl', $tpl, PDO::PARAM_STR);
				$stmt->execute();
				$n = (int)$stmt->fetchResult();
				if ($n < 1) {
					$title = 'Five';
					$section = 'frontend';
					$core = 1;
					$sql = "INSERT INTO ".$db->quoteId('#__templates')." VALUES (NULL, :xtitle, :xtpl, :xsec, :xcore, NULL)";
					$stmt = $db->prepare($sql);
					$stmt->bindParam(':xtitle', $title, PDO::PARAM_STR);
					$stmt->bindParam(':xtpl', $tpl, PDO::PARAM_STR);
					$stmt->bindParam(':xsec', $section, PDO::PARAM_STR);
					$stmt->bindParam(':xcore', $core, PDO::PARAM_INT);
					$stmt->execute();
				}
			}

			//Switch admin template to Onyx
			if (file_exists(ELXIS_PATH.'/templates/admin/onyx/index.php')) {
				$tpl = 'onyx';
				$sql = "SELECT COUNT(id) FROM ".$db->quoteId('#__templates')." WHERE ".$db->quoteId('template')." = :xtpl";
				$stmt = $db->prepare($sql);
				$stmt->bindParam(':xtpl', $tpl, PDO::PARAM_STR);
				$stmt->execute();
				$n = (int)$stmt->fetchResult();
				if ($n < 1) {
					$title = 'Onyx';
					$section = 'backend';
					$core = 1;
					$sql = "INSERT INTO ".$db->quoteId('#__templates')." VALUES (NULL, :xtitle, :xtpl, :xsec, :xcore, NULL)";
					$stmt = $db->prepare($sql);
					$stmt->bindParam(':xtitle', $title, PDO::PARAM_STR);
					$stmt->bindParam(':xtpl', $tpl, PDO::PARAM_STR);
					$stmt->bindParam(':xsec', $section, PDO::PARAM_STR);
					$stmt->bindParam(':xcore', $core, PDO::PARAM_INT);
					$stmt->execute();
				}
				//remove all previous templates
				$sql = "DELETE FROM ".$db->quoteId('#__templates')
				."\n WHERE ".$db->quoteId('section')." = ".$db->quote('backend')." AND ".$db->quoteId('template')." <> ".$db->quote('onyx');
				$stmt = $db->prepare($sql);
				$stmt->execute();

				$configfile = 'configuration.php';
				if (defined('ELXIS_MULTISITE')) { $configfile = 'config'.ELXIS_MULTISITE.'.php'; }
				$handle = @fopen(ELXIS_PATH.'/'.$configfile, 'r');
				if ($handle) {
					$newdata = '';
					while (($line = fgets($handle)) !== false) {
						if (strpos($line, 'private $ATEMPLATE') !== false) {
							$newdata .= "\t".'private $ATEMPLATE = \'onyx\';'._LEND;
						} else {
							$newdata .= $line;
						}
					}
					fclose($handle);
					$configfile = 'configuration.php';
					if (defined('ELXIS_MULTISITE')) { $configfile = 'config'.ELXIS_MULTISITE.'.php'; }
					$eFiles->createFile($configfile, $newdata);
					unset($newdata);
					$eFiles->deleteFolder('templates/admin/iris/');
				}
				unset($handle);
			}
		}//Elxis 5.0 end
	}


	/*************************/
	/* COUNT SCHEDULED ITEMS */
	/*************************/
	public function countScheduledItems() {
		$pubdate = '2014-01-01 00:00:00';
		$unpubdate = '2060-01-01 00:00:00';

		$sql = "SELECT COUNT(id) FROM ".$this->db->quoteId('#__modules')
		."\n WHERE (".$this->db->quoteId('published')." = 0 AND ".$this->db->quoteId('pubdate')." != :xpub) OR"
		."\n (".$this->db->quoteId('published')." = 1 AND ".$this->db->quoteId('unpubdate')." != :xupub)";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xpub', $pubdate, PDO::PARAM_STR);
		$stmt->bindParam(':xupub', $unpubdate, PDO::PARAM_STR);
		$stmt->execute();
		return (int)$stmt->fetchResult();
	}


	/********************/
	/* GET PLUGIN USAGE */
	/********************/
	public function getPluginUsage($qsyntax) {
		$q = '%'.$qsyntax.'%';

		//com_content
		$sql = "SELECT ".$this->db->quoteId('id').", ".$this->db->quoteId('title')." FROM ".$this->db->quoteId('#__content')
		."\n WHERE ".$this->db->quoteId('introtext')." LIKE :qtxt OR ".$this->db->quoteId('maintext')." LIKE :qtxt ORDER BY ".$this->db->quoteId('created')." DESC";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':qtxt', $q, PDO::PARAM_STR);
		$stmt->execute();
		$rows = $stmt->fetchPairs();

		$category = 'com_content';
		$element1 = 'introtext';
		$element2 = 'maintext';
		$sql = "SELECT ".$this->db->quoteId('elid')." FROM ".$this->db->quoteId('#__translations')
		."\n WHERE ".$this->db->quoteId('category')." = :xctg AND (".$this->db->quoteId('element')." = :xelem1 OR ".$this->db->quoteId('element')." = :xelem2)"
		."\n AND ".$this->db->quoteId('translation')." LIKE :qtxt";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xctg', $category, PDO::PARAM_STR);
		$stmt->bindParam(':xelem1', $element1, PDO::PARAM_STR);
		$stmt->bindParam(':xelem2', $element2, PDO::PARAM_STR);
		$stmt->bindParam(':qtxt', $q, PDO::PARAM_STR);
		$stmt->execute();
		$trans = $stmt->fetchCol();

		$articles = array();
		if ($rows) { $articles = $rows; }
		unset($rows);
		if ($trans) {
			foreach ($trans as $elid) {
				if (!isset($articles[$elid])) { $articles[$elid] = 'Article '.$elid.' translation'; }
			}
		}
		unset($trans);

		//modules
		$module = 'mod_content';
		$sql = "SELECT ".$this->db->quoteId('id').", ".$this->db->quoteId('title')." FROM ".$this->db->quoteId('#__modules')
		."\n WHERE ".$this->db->quoteId('module')." = :xmod AND ".$this->db->quoteId('content')." LIKE :qtxt";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xmod', $module, PDO::PARAM_STR);
		$stmt->bindParam(':qtxt', $q, PDO::PARAM_STR);
		$stmt->execute();
		$modules = $stmt->fetchPairs();

		$products = array();
		//com_shop
		if (file_exists(ELXIS_PATH.'/components/com_shop/shop.php')) {
			$sql = "SELECT ".$this->db->quoteId('id').", ".$this->db->quoteId('title')." FROM ".$this->db->quoteId('#__shop_products')
			."\n WHERE ".$this->db->quoteId('description')." LIKE :qtxt";
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':qtxt', $q, PDO::PARAM_STR);
			$stmt->execute();
			$rows = $stmt->fetchPairs();

			$category = 'com_shop';
			$element = 'proddescr';
			$sql = "SELECT ".$this->db->quoteId('elid')." FROM ".$this->db->quoteId('#__translations')
			."\n WHERE ".$this->db->quoteId('category')." = :xctg AND ".$this->db->quoteId('element')." = :xelem AND ".$this->db->quoteId('translation')." LIKE :qtxt";
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':xctg', $category, PDO::PARAM_STR);
			$stmt->bindParam(':xelem', $element, PDO::PARAM_STR);
			$stmt->bindParam(':qtxt', $q, PDO::PARAM_STR);
			$stmt->execute();
			$trans = $stmt->fetchCol();

			if ($rows) { $products = $rows; }
			unset($rows);
			if ($trans) {
				foreach ($trans as $elid) {
					if (!isset($products[$elid])) { $products[$elid] = 'Product '.$elid.' translation'; }
				}
			}
			unset($trans);
		}

		return array(
			'articles' => $articles,
			'modules' => $modules,
			'products' => $products
		);
	}

}

?>