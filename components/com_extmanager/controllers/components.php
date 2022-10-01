<?php 
/**
* @version		$Id: components.php 1834 2016-06-01 18:25:11Z sannosi $
* @package		Elxis
* @subpackage	Component Extensions Manager
* @copyright	Copyright (c) 2006-2019 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class componentsExtmanagerController extends extmanagerController {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null, $task='', $model=null) {
		parent::__construct($view, $task, $model);
	}


	/*********************************/
	/* PREPARE TO DISPLAY COMPONENTS */
	/*********************************/
	public function listcomps() {
		$this->listExtensions('components');//base
	}


	/***********************/
	/* UNINSTALL COMPONENT */
	/***********************/
	public function deletecomponent() {
		$this->deleteExtension('components');//base
	}


	/******************/
	/* EDIT COMPONENT */
	/******************/
	public function editcomponent() {
		$this->editExtension('components');//base
	}


	/******************/
	/* SAVE COMPONENT */
	/******************/
	public function savecomponent() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eSession = eFactory::getSession();

		$sess_token = trim($eSession->get('token_fmextedit'));
		$token = trim(filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		if (($token == '') || ($sess_token == '') || ($sess_token != $token)) {
			exitPage::make('403', 'CEXT-0007', $eLang->get('REQDROPPEDSEC'));
		}

		$task = isset($_POST['task']) ? trim($_POST['task']) : 'save';
		$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
		if ($id < 1) {
			$link = $elxis->makeAURL('extmanager:components/');
			$elxis->redirect($link, 'Component not found!', true);
		}

		if ($elxis->acl()->check('com_extmanager', 'components', 'edit') < 1) {
			$msg = $eLang->get('NOTALLOWACTION');
			$link = $elxis->makeAURL('extmanager:/');
			$elxis->redirect($link, $msg, true);
		}

		$row = new componentsDbTable();
		if (!$row->load($id)) {
			$link = $elxis->makeAURL('extmanager:components/');
			$elxis->redirect($link, 'Component not found!', true);
		}

		if ($elxis->acl()->check('component', $row->component, 'manage') < 1) {
			$link = $elxis->makeAURL('extmanager:components/');
			$elxis->redirect($link, $eLang->get('NOTALLOWMANITEM'), true);
		}

		$route = strtolower(trim(filter_input(INPUT_POST, 'route', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)));
		$route_clean = preg_replace('/[^a-z0-9\_\-]/', '', $route);
		if ($route != $route_clean) {
			$link = $elxis->makeAURL('extmanager:components/edit.html?id='.$id);
			$elxis->redirect($link, 'Route is invalid!', true);
		}
		
		if ($route != '') {
			if (file_exists(ELXIS_PATH.'/'.$route.'/')) {
				$link = $elxis->makeAURL('extmanager:components/edit.html?id='.$id);
				$elxis->redirect($link, 'You can not route a component to an existing folder!', true);
			}
		}		

		$cname = preg_replace('/^(com\_)/', '', $row->component);
		$comxml = ELXIS_PATH.'/components/'.$row->component.'/'.$cname.'.xml';
		if (file_exists($comxml)) {
			elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
			$params = new elxisParameters('', $comxml, 'component');
			$row->params = isset($_POST['params']) ? $params->toString($_POST['params']) : null;
			unset($params);
		} else {
			$row->params = null;
		}

		if (!$row->update()) {
			$redirurl = $elxis->makeAURL('extmanager:components/edit.html?id='.$id);
			$elxis->redirect($redirurl, $row->getErrorMsg(), true);
		}

		if ($route != trim($row->route)) {
			elxisLoader::loadFile('components/com_cpanel/models/cpanel.model.php');
			$cpmodel = new cpanelModel();
			$cpmodel->setComponentRoute($row->component, $route);
			unset($cpmodel);
		}

		$eSession->set('token_fmextedit');

		if (isset($_POST['onsave'])) {
			$onsave = trim($_POST['onsave']);
			$onsave = ltrim($_POST['onsave'], '/');
			$xmldir = dirname($comxml).'/';
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

		$redirurl = $elxis->makeAURL('extmanager:components/');
		if ($task == 'apply') { $redirurl .= 'edit.html'; }
		if ($p) { $redirurl .= '?'.implode('&', $p); }
		$elxis->redirect($redirurl, $eLang->get('ITEM_SAVED'));
	}

}
	
?>