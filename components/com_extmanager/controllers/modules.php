<?php 
/**
* @version		$Id: modules.php 2434 2022-01-19 17:32:52Z IOS $
* @package		Elxis
* @subpackage	Component Extensions Manager
* @copyright	Copyright (c) 2006-2022 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class modulesExtmanagerController extends extmanagerController {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null, $task='', $model=null) {
		parent::__construct($view, $task, $model);
	}


	/******************************/
	/* PREPARE TO DISPLAY MODULES */
	/******************************/
	public function listmods() {
		$this->listExtensions('modules');//base
	}


	/**********************************/
	/* TOGGLE MODULE'S PUBLISH STATUS */
	/**********************************/
	public function togglemodule() {
		$this->toggleExtension('modules');//base
	}


	/*******************/
	/* DELETE A MODULE */
	/*******************/
	public function deletemodule() {
		$this->deleteExtension('modules');//base
	}


	/*****************/
	/* COPY A MODULE */
	/*****************/
	public function copymodule() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$response = array('success' => 0, 'message' => '');

		if ($elxis->acl()->check('com_extmanager', 'modules', 'install') < 1) {
			$response['message'] = $eLang->get('NOTALLOWACTION');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}
		$id = isset($_POST['elid']) ? (int)$_POST['elid'] : 0;
		if ($id < 1) {
			$response['message'] = 'No module requested!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$row = new modulesDbTable();
		if (!$row->load($id)) {
			$response['message'] = 'Module not found!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		if ($elxis->acl()->check('module', $row->module, 'manage', $id) < 1) {
			$response['message'] = $eLang->get('NOTALLOWMANITEM');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$row->title = $row->title.' ('.$eLang->get('COPY').')';
		$row->published = 0;
		$row->ordering += 1;

		$row->forceNew(true);

		if (!$row->insert()) {
			$response['message'] = $row->getErrorMsg();
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$wheres = array(
			array('position', '=', $row->position),
			array('section', '=', $row->section)
		);
		$row->reorder($wheres, true);

		$this->model->copyModuleACL($row->module, $id, $row->id);
		$this->model->copyModuleTranslations($id, $row->id);

		if ($row->section == 'frontend') {
			$db = eFactory::getDB();
			$modid = $row->id;
			$menuid = 0;
			$sql = "INSERT INTO ".$db->quoteId('#__modules_menu')." (".$db->quoteId('mmid').", ".$db->quoteId('moduleid').", ".$db->quoteId('menuid').")"
			."\n VALUES (NULL, :xmod, :xmen)";
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':xmod', $modid, PDO::PARAM_INT);
			$stmt->bindParam(':xmen', $menuid, PDO::PARAM_INT);
			$stmt->execute();
		}

		$response['success'] = 1;
		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/*************************/
	/* SET MODULE'S ORDERING */
	/*************************/
	public function setordering() {
		$this->setExtensionOrdering('modules');//base
	}


	/**************/
	/* ADD MODULE */
	/**************/
	public function addmodule() {
		$this->editExtension('modules', true);//base
	}


	/*******************/
	/* ADD/EDIT MODULE */
	/*******************/
	public function editmodule() {
		$this->editExtension('modules', false);//base
	}


	/*****************************************/
	/* GET A POSITION'S MODULES FOR ORDERING */
	/*****************************************/
	public function positionorder() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$json = array();
		$json['error'] = 0;
		$json['errormsg'] = '';
		$json['modules'] = array();

		if ($elxis->acl()->check('com_extmanager', 'modules', 'edit') < 1) {
			$json['error'] = 1;
			$json['errormsg'] = addslashes($eLang->get('NOTALLOWACTION'));
			$this->ajaxHeaders('application/json');
			echo json_encode($json);
			exit;
		}

		$position = filter_input(INPUT_POST, 'position', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		$position = trim(preg_replace('/[^A-Za-z\_\-0-9]/', '', $position));

		if ($position == '') {
			$json['error'] = 1;
			$json['errormsg'] = 'You must select a position!';
			$this->ajaxHeaders('application/json');
			echo json_encode($json);
			exit;
		}

		$posmods = $this->model->getModsByPosition($position);

		$json['modules'][] = array('0' => '- '.$eLang->get('FIRST'));
		$q = 1;
		if ($posmods) {
			foreach ($posmods as $posmod) {
				$ttl = addslashes($posmod->title);
				$json['modules'][] = array($q => $q.' - '.$ttl);
				$q++;
			}
		}

		$q = ($q > 1) ? $q : 999;
		$json['modules'][] = array($q => '- '.$eLang->get('LAST'));

		$this->ajaxHeaders('application/json');
		echo json_encode($json);
		exit;
	}


	/***************/
	/* SAVE MODULE */
	/***************/
	public function savemodule() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eSession = eFactory::getSession();
		$eDate = eFactory::getDate();

		$sess_token = trim($eSession->get('token_fmextedit'));
		$token = trim(filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		if (($token == '') || ($sess_token == '') || ($sess_token != $token)) {
			exitPage::make('403', 'CEXT-0006', $eLang->get('REQDROPPEDSEC'));
		}

		$task = isset($_POST['task']) ? trim($_POST['task']) : 'save';
		$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
		if ($id < 1) { $id = 0; }

		$redirurl = $elxis->makeAURL('extmanager:/');
		if ($id > 0) {
			if ($elxis->acl()->check('com_extmanager', 'modules', 'edit') < 1) {
				$elxis->redirect($redirurl, $eLang->get('NOTALLOWACTION'), true);
			}
		} else {
			if ($elxis->acl()->check('com_extmanager', 'modules', 'install') < 1) {
				$elxis->redirect($redirurl, $eLang->get('NOTALLOWACTION'), true);
			}
		}

		elxisLoader::loadFile('includes/libraries/elxis/database/tables/modules.db.php');
		$row = new modulesDbTable();
		$old_ordering = -1;
		$iscore = 1;
		$module = 'mod_content';
		$section = 'frontend';
		if ($id > 0) {
			if (!$row->load($id)) { $elxis->redirect($redirurl, 'Module not found!', true); }
			if ($elxis->acl()->check('module', $row->module, 'manage', $id) < 1) {
				$elxis->redirect($redirurl, $eLang->get('NOTALLOWMANITEM'), true);
			}
			$old_ordering = $row->ordering;
			$iscore = $row->iscore;
			$module = $row->module;
			$section = $row->section;
		}

		if (!$row->bind($_POST)) {
			$elxis->redirect($redirurl, $row->getErrorMsg(), true);
		}

		$row->published = (isset($_POST['published'])) ? (int)$_POST['published'] : 0;
		$row->iscore = (int)$iscore;
		$row->module = $module;
		$row->section = $section;
		$row->pubdate = trim($row->pubdate);
		$row->unpubdate = trim($row->unpubdate);

		if ($row->pubdate == '') { $row->pubdate = '2014-01-01 00:00:00'; }
		if ($row->unpubdate == '') { $row->unpubdate = '2060-01-01 00:00:00'; }

		if ($row->pubdate != '2014-01-01 00:00:00') {
			$newdate = $eDate->convertFormat($row->pubdate, $eLang->get('DATE_FORMAT_BOX_LONG'), 'Y-m-d H:i:s');
			if ($newdate !== false) {
				$row->pubdate = $eDate->localToElxis($newdate);
			} else {
				$row->pubdate = '2014-01-01 00:00:00';
			}
		}

		if ($row->unpubdate != '2060-01-01 00:00:00') {
			$newdate = $eDate->convertFormat($row->unpubdate, $eLang->get('DATE_FORMAT_BOX_LONG'), 'Y-m-d H:i:s');
			if ($newdate !== false) {
				$row->unpubdate = $eDate->localToElxis($newdate);
			} else {
				$row->unpubdate = '2060-01-01 00:00:00';
			}
		}

		$ts = time() - 86400; 
		$yesterday = gmdate('Y-m-d H:i:s', $ts);
		if ($row->pubdate != '2014-01-01 00:00:00') {
			if ($row->pubdate < $yesterday) { $row->pubdate = '2014-01-01 00:00:00'; }
		}
		if ($row->unpubdate != '2060-01-01 00:00:00') {
			if ($row->unpubdate < $yesterday) { $row->unpubdate = '2060-01-01 00:00:00'; }
		}

		$addacl = false;
		if ($id > 0) {
			$redirurledit = $elxis->makeAURL('extmanager:modules/edit.html?id='.$id);
		} else {
			$addacl = true;
			$redirurledit = $elxis->makeAURL('extmanager:modules/add.html');
		}

		$modxml = ELXIS_PATH.'/modules/'.$row->module.'/'.$row->module.'.xml';
		if (file_exists($modxml)) {
			elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
			$params = new elxisParameters('', $modxml, 'module');
			$row->params = isset($_POST['params']) ? $params->toString($_POST['params']) : null;
			unset($params);
		} else {
			$row->params = null;
		}

		$ok = ($id > 0) ? $row->update() : $row->insert();
		if (!$ok) {
			$elxis->redirect($redirurledit, $row->getErrorMsg(), true);
		}

		if (($id == 0) || ($old_ordering <> $row->ordering)) {
			$reorder = true;
		} else {
			$reorder = false;
		}
		if ($reorder) {
			$wheres = array(array('position', '=', $row->position));
			$row->reorder($wheres, true);
		}

		//save translations
		$sitelangs = $eLang->getSiteLangs(false);
		$translations = array('title' => array());
		if ($row->module == 'mod_content') { $translations['content'] = array(); }
		foreach ($sitelangs as $lng) {
			if ($lng == $elxis->getConfig('LANG')) { continue; }
			$idx = 'title_'.$lng;
			$translations['title'][$lng] = isset($_POST[$idx]) ? eUTF::trim(filter_input(INPUT_POST, $idx, FILTER_UNSAFE_RAW)) : '';
			if ($row->module == 'mod_content') {
				$idx = 'content_'.$lng;
				$translations['content'][$lng] = isset($_POST[$idx]) ? filter_input(INPUT_POST, $idx, FILTER_UNSAFE_RAW) : '';
			}
		}
		$elxis->obj('translations')->saveElementTranslations('module', 'title', $row->id, $translations['title']);
		if ($row->module == 'mod_content') {
			$elxis->obj('translations')->saveElementTranslations('module', 'content', $row->id, $translations['content']);
		}
		unset($sitelangs, $translations);

		if ($addacl == true) {
			$arow = new aclDbTable();
			$arow->category = 'module';
			$arow->element = $row->module;
			$arow->identity = (int)$row->id;
			$arow->action = 'view';
			$arow->minlevel = 0;
			$arow->gid = 0;
			$arow->uid = 0;
			$arow->aclvalue = 1;
			$arow->insert();
			unset($arow);
			$arow = new aclDbTable();
			$arow->category = 'module';
			$arow->element = $row->module;
			$arow->identity = (int)$row->id;
			$arow->action = 'manage';
			$arow->minlevel = 70;
			$arow->gid = 0;
			$arow->uid = 0;
			$arow->aclvalue = 1;
			$arow->insert();
			unset($arow);
		}

		$pages = isset($_POST['pages']) ? $_POST['pages'] : array();
		if (!is_array($pages)) { $pages = array(); }
		$to_add = array();
		$to_delete = array();
		if ($row->section == 'frontend') {
			if ($id > 0) {
				$modmenuitems = $this->model->getModMenuItems($id);
				if (!is_array($modmenuitems)) { $modmenuitems = array(); }
			} else {
				$modmenuitems = array();
			}
		} else {
			$modmenuitems = array();
		}

		if ($pages) {
			foreach ($pages as $page) {
				$page = (int)$page;
				if ($page < 0) { continue; }
				if (!in_array($page, $modmenuitems)) {
					if ($page == 0) {
						$to_add = array(0);
					} else {
						$to_add[] = $page;
					}
				}
				if ($page == 0) { $pages = array(0); break; }
			}
		}

		if ($modmenuitems) {
			foreach ($modmenuitems as $mmitem) {
				if (!in_array($mmitem, $pages)) { $to_delete[] = $mmitem; }
			}
		}

		if (count($to_delete) > 0) {
			$this->model->deleteModMenus($row->id, $to_delete);
		}
		if (count($to_add) > 0) {
			$this->model->insertModMenus($row->id, $to_add);
		}

		if ($id > 0) {
			eFactory::getCache()->clearItems('modules', '^'.$row->module.'_'.$id);
		}
		$eSession->set('token_fmextedit');

		if (isset($_POST['onsave'])) {
			$onsave = trim($_POST['onsave']);
			$onsave = ltrim($_POST['onsave'], '/');
			$xmldir = dirname($modxml).'/';
			if (($onsave != '') && file_exists($xmldir.$onsave) && is_file($xmldir.$onsave)) {
				include($xmldir.$onsave);
			}
		}

		$p = array();
		if ($task == 'apply') {
			$p[] = 'id='.$row->id;
			if (isset($_POST['tabopen'])) {
				$v = (int)$_POST['tabopen'];
				if ($v > 0) { $p[] = 'tabopen='.$v; }
			}
		}
		if (isset($_POST['page'])) {
			$v = (int)$_POST['page'];
			if ($v > 1) { $p[] = 'page='.$v; }
		}
		if (isset($_POST['sn'])) {
			$v = trim($_POST['sn']);
			if ($v != '') { $p[] = 'sn='.$v; }
		}
		if (isset($_POST['so'])) {
			$v = trim($_POST['so']);
			if ($v != '') { $p[] = 'so='.$v; }
		}
		if (isset($_POST['lpsection'])) {
			$v = trim($_POST['lpsection']);
			if ($v != '') { $p[] = 'section='.$v; }
		}

		$redirurl = $elxis->makeAURL('extmanager:modules/');
		if ($task == 'apply') { $redirurl .= 'edit.html'; }
		if ($p) { $redirurl .= '?'.implode('&', $p); }
		$elxis->redirect($redirurl, $eLang->get('ITEM_SAVED'));
	}

}
	
?>