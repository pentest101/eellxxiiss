<?php 
/**
* @version		$Id: engines.php 1697 2015-03-21 20:59:08Z sannosi $
* @package		Elxis
* @subpackage	Component Extensions Manager
* @copyright	Copyright (c) 2006-2019 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class enginesExtmanagerController extends extmanagerController {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null, $task='', $model=null) {
		parent::__construct($view, $task, $model);
	}


	/*************************************/
	/* PREPARE TO DISPLAY SEARCH ENGINES */
	/*************************************/
	public function listengs() {
		$this->listExtensions('engines');//base
	}


	/******************************/
	/* SET SEARCH ENGINE ORDERING */
	/******************************/
	public function setordering() {
		$this->setExtensionOrdering('engines');//base
	}


	/*****************************************/
	/* TOGGLE ENGINE'S PUBLISH STATUS (ICON) */
	/*****************************************/
	public function toggleengine() {
		$this->toggleExtension('engines');//base
	}


	/******************************/
	/* MAKE SEARCH ENGINE DEFAULT */
	/******************************/
	public function makedefault() {
		$this->toggleExtensionDefault('engines');//base
	}


	/*****************/
	/* DELETE ENGINE */
	/*****************/
	public function deleteengine() {
		$this->deleteExtension('engines');//base
	}


	/***************/
	/* EDIT ENGINE */
	/***************/
	public function editengine() {
		$this->editExtension('engines');//base
	}


	/***************/
	/* SAVE ENGINE */
	/***************/
	public function saveengine() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eSession = eFactory::getSession();

		$sess_token = trim($eSession->get('token_fmextedit'));
		$token = trim(filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		if (($token == '') || ($sess_token == '') || ($sess_token != $token)) {
			exitPage::make('403', 'CEXT-0013', $eLang->get('REQDROPPEDSEC'));
		}

		$task = isset($_POST['task']) ? trim($_POST['task']) : 'save';
		$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
		if ($id < 1) {
			$link = $elxis->makeAURL('extmanager:engines/');
			$elxis->redirect($link, 'Engine not found!', true);
		}

		if ($elxis->acl()->check('com_extmanager', 'engines', 'edit') < 1) {
			$msg = $eLang->get('NOTALLOWACTION');
			$link = $elxis->makeAURL('extmanager:/');
			$elxis->redirect($link, $msg, true);
		}

		$row = new enginesDbTable();
		if (!$row->load($id)) {
			$link = $elxis->makeAURL('extmanager:engines/');
			$elxis->redirect($link, 'Engine not found!', true);
		}

		$old_ordering = $row->ordering;
		$old_defengine = $row->defengine;
		$row->ordering = isset($_POST['ordering']) ? (int)$_POST['ordering'] : 0;
		$row->published = isset($_POST['published']) ? (int)$_POST['published'] : 0;
		$row->defengine = isset($_POST['defengine']) ? (int)$_POST['defengine'] : 0;
		if (($row->defengine == 1) && ($row->published == 0)) {
			$redirurl = $elxis->makeAURL('extmanager:engines/edit.html?id='.$id);
			$elxis->redirect($redirurl, $eLang->get('DEF_ENGINE_PUB'), true);
		}
		$row->alevel = isset($_POST['alevel']) ? (int)$_POST['alevel'] : 0;
		$row->title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);

		$xmlfile = ELXIS_PATH.'/components/com_search/engines/'.$row->engine.'/'.$row->engine.'.engine.xml';
		if (file_exists($xmlfile)) {
			elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
			$params = new elxisParameters('', $xmlfile, 'engine');
			$parr = (isset($_POST['params'])) ? $_POST['params'] : array();
			$row->params = $params->toString($parr);
			unset($params);
		} else {
			$row->params = null;
		}

		if (!$row->update()) {
			$redirurl = $elxis->makeAURL('extmanager:engines/edit.html?id='.$id);
			$elxis->redirect($redirurl, $row->getErrorMsg(), true);
		}

		if ($old_ordering <> $row->ordering) { $row->reorder(); }
		if (($old_defengine <> $row->defengine) && ($row->defengine == 1)) { $this->model->setDefaultEngine($id); }

		$eSession->set('token_fmextedit');

		if (isset($_POST['onsave'])) {
			$onsave = trim($_POST['onsave']);
			$onsave = ltrim($_POST['onsave'], '/');
			$xmldir = dirname($xmlfile).'/';
			if (($onsave != '') && file_exists($xmldir.$onsave) && is_file($xmldir.$onsave)) {
				include($xmldir.$onsave);
			}
		}

		$p = array();
		if ($task == 'apply') {
			$p[] = 'id='.$id;
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

		$redirurl = $elxis->makeAURL('extmanager:engines/');
		if ($task == 'apply') { $redirurl .= 'edit.html'; }
		if ($p) { $redirurl .= '?'.implode('&', $p); }
		$elxis->redirect($redirurl, $eLang->get('ITEM_SAVED'));
	}

}
	
?>