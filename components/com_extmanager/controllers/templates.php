<?php 
/**
* @version		$Id: templates.php 2405 2021-04-13 16:06:13Z IOS $
* @package		Elxis
* @subpackage	Component Extensions Manager
* @copyright	Copyright (c) 2006-2021 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class templatesExtmanagerController extends extmanagerController {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null, $task='', $model=null) {
		parent::__construct($view, $task, $model);
	}


	/*********************************/
	/* PREPARE TO DISPLAY TEMPLATES */
	/*********************************/
	public function listtempls() {
		$this->listExtensions('templates');//base
	}


	/*******************/
	/* DELETE TEMPLATE */
	/*******************/
	public function deletetemplate() {
		$this->deleteExtension('templates');//base
	}


	/*****************/
	/* EDIT TEMPLATE */
	/*****************/
	public function edittemplate() {
		$this->editExtension('templates');//base
	}


	/*****************/
	/* SAVE TEMPLATE */
	/*****************/
	public function savetemplate() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eSession = eFactory::getSession();

		$sess_token = trim($eSession->get('token_fmextedit'));
		$token = trim(filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		if (($token == '') || ($sess_token == '') || ($sess_token != $token)) {
			exitPage::make('403', 'CEXT-0010', $eLang->get('REQDROPPEDSEC'));
		}

		$task = isset($_POST['task']) ? trim($_POST['task']) : 'save';
		$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
		if ($id < 1) {
			$link = $elxis->makeAURL('extmanager:templates/');
			$elxis->redirect($link, 'Engine not found!', true);
		}

		if ($elxis->acl()->check('com_extmanager', 'templates', 'edit') < 1) {
			$msg = $eLang->get('NOTALLOWACTION');
			$link = $elxis->makeAURL('extmanager:/');
			$elxis->redirect($link, $msg, true);
		}

		$row = new templatesDbTable();
		if (!$row->load($id)) {
			$link = $elxis->makeAURL('extmanager:templates/');
			$elxis->redirect($link, 'Template not found!', true);
		}

		$reldir = ($row->section == 'backend') ? 'templates/admin/' : 'templates/';
		$xmlfile = ELXIS_PATH.'/'.$reldir.$row->template.'/'.$row->template.'.xml';
		$hasxml = true;
		if (!file_exists($xmlfile)) {
			$xmlfile = ELXIS_PATH.'/'.$reldir.$row->template.'/templateDetails.xml'; //elxis 2009.x compatibility
			if (!file_exists($xmlfile)) { $hasxml = false; }
		}

		if ($hasxml) {
			elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
			$params = new elxisParameters('', $xmlfile, 'template');
			$row->params = isset($_POST['params']) ? $params->toString($_POST['params']) : null;
			unset($params);
		} else {
			$row->params = null;
		}

		if (!$row->update()) {
			$redirurl = $elxis->makeAURL('extmanager:templates/edit.html?id='.$id);
			$elxis->redirect($redirurl, $row->getErrorMsg(), true);
		}

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

		$redirurl = $elxis->makeAURL('extmanager:templates/');
		if ($task == 'apply') { $redirurl .= 'edit.html'; }
		if ($p) { $redirurl .= '?'.implode('&', $p); }
		$elxis->redirect($redirurl, $eLang->get('ITEM_SAVED'));
	}


	/************************************/
	/* PREPARE TO LIST MODULE POSITIONS */
	/************************************/
	public function listpositions() {
		$eLang = eFactory::getLang();
		$eDoc = eFactory::getDocument();
		$elxis = eFactory::getElxis();
		$pathway = eFactory::getPathway();

		if ($elxis->acl()->check('com_extmanager', 'templates', 'edit') < 1) {
			$link = $elxis->makeAURL('extmanager:/');
			$elxis->redirect($link, $eLang->get('NOTALLOWACTION'), true);
		}


		$options = array('limit' => 20, 'page' => 1, 'maxpage' => 1, 'sn' => 'position', 'so' => 'asc', 'limitstart' => 0, 'total' => 0);
		$options['limit'] = (isset($_GET['limit'])) ? (int)$_GET['limit'] : 20;
		if ($options['limit'] < 1) { $options['limit'] = 20; }
		$elxis->updateCookie('limit', $options['limit']);
		$options['page'] = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
		if ($options['page'] < 1) { $options['page'] = 1; }
		if (isset($_GET['sn'])) {
			$sn = trim($_GET['sn']);
			if ($sn != '') { if (in_array($sn, array('position', 'modules', 'description'))) { $options['sn'] = $sn; } }
		}
		$options['so'] = (isset($_GET['so'])) ? trim($_GET['so']) : 'asc';
		if ($options['so'] != 'desc') { $options['so'] = 'asc'; }

		$options['total'] = $this->model->countPositions();

		$options['maxpage'] = ceil($options['total']/$options['limit']);
		if ($options['maxpage'] < 1) { $options['maxpage'] = 1; }
		if ($options['page'] > $options['maxpage']) { $options['page'] = $options['maxpage']; }
		$options['limitstart'] = (($options['page'] - 1) * $options['limit']);

		$rows = array();
		if ($options['total'] > 0) {
			$rows = $this->model->getFullPositions($options);
		}

		$pathway->addNode($eLang->get('EXTENSIONS'), 'extmanager:/');
		$pathway->addNode($eLang->get('TEMPLATES'), 'extmanager:templates/');
		$pathway->addNode($eLang->get('MODULE_POSITIONS'));
		$eDoc->setTitle($eLang->get('MODULE_POSITIONS'));
		$eDoc->addFontAwesome();
		$eDoc->addScriptLink($elxis->secureBase().'/components/com_extmanager/js/extmanager.js');
		if ($rows) {
			$eDoc->addNativeDocReady('elx5DataTable(\'positionstbl\', false);');
		}

		$this->view->listPositionsHTML($rows, $options, $elxis, $eLang);
	}


	/*******************************/
	/* SAVE MODULE POSITION (AJAX) */
	/*******************************/
	public function saveposition() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$response = array('success' => 0, 'message' => '');

		if ($elxis->acl()->check('com_extmanager', 'templates', 'edit') < 1) {
			$response['message'] = $eLang->get('NOTALLOWACTION');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$sess_token = trim(eFactory::getSession()->get('token_modpos'));
		$token = trim(filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		if (($token == '') || ($sess_token == '') || ($sess_token != $token)) {
			$response['message'] = $eLang->get('REQDROPPEDSEC');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$id = (isset($_POST['posid'])) ? (int)$_POST['posid'] : 0;
		if ($id < 0) { $id = 0; }
		$position = strtolower(trim(filter_input(INPUT_POST, 'position', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW)));
		$description = trim(strip_tags(filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)));
		$position2 = preg_replace("/[^a-z0-9\_]/", '', $position);

		if (($position == '') || ($position != $position2)) {
			$response['message'] = $eLang->get('POS_NAME_CHARS');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$oldname = '';
		elxisLoader::loadFile('includes/libraries/elxis/database/tables/tplpositions.db.php');
		$row = new tplpositionsDbTable();
		if ($id > 0) {
			if (!$row->load($id)) {
				$response['message'] = 'The requested position was not found!';
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit;
			}
			if ($row->position != $position) {
				$oldname = $row->position;
				$num = $this->model->countPositionName($position);
				if ($num > 0) {
					$response['message'] = $eLang->get('ALREADY_POS_NAME');
					$this->ajaxHeaders('application/json');
					echo json_encode($response);
					exit;
				}
			}
		} else {
			$num = $this->model->countPositionName($position);
			if ($num > 0) {
				$response['message'] = $eLang->get('ALREADY_POS_NAME');
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit;
			}
		}

		$row->position = $position;
		$row->description = $description;

		if (!$row->store()) {
			$response['message'] = 'Could not save position!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		if (($id > 0) && ($oldname != '')) {
			$this->model->updateModulesPositions($oldname, $row->position);
		}

		$response['success'] = 1;

		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/**************************/
	/* DELETE MODULE POSITION */
	/**************************/
	public function deleteposition() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$response = array('success' => 0, 'message' => '');

		if ($elxis->acl()->check('com_extmanager', 'templates', 'edit') < 1) {
			$response['message'] = $eLang->get('NOTALLOWACTION');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$id = isset($_POST['elids']) ? (int)$_POST['elids'] : 0;
		if ($id < 1) {
			$response['message'] = 'Invalid position!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		elxisLoader::loadFile('includes/libraries/elxis/database/tables/tplpositions.db.php');
		$row = new tplpositionsDbTable();
		if (!$row->load($id)) {
			$response['message'] = 'The requested position was not found!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$options = array('section' => 'frontend', 'position' => $row->position);
		$num = $this->model->countModules($options);
		if ($num > 0) {
			$response['message'] = $eLang->get('CNOT_DELETE_POSMODS');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$ok = $row->delete();
		if ($ok) {
			$response['success'] = 1;
		} else {
			$response['message'] = 'Could not delete module position!';
		}

		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/*****************/
	/* COPY TEMPLATE */
	/*****************/
	public function copytemplate() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eFiles = eFactory::getFiles();

		$response = array('success' => 0, 'message' => '');

		if ($elxis->acl()->check('com_extmanager', 'templates', 'install') < 1) {
			$response['message'] = $eLang->get('NOTALLOWACTION');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}
		if (defined('ELXIS_MULTISITE') && (ELXIS_MULTISITE != 1)) {
			$response['message'] = 'You can only copy templates on the mother site!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$originalid = isset($_POST['originalid']) ? (int)$_POST['originalid'] : 0;
		if ($originalid < 1) {
			$response['message'] = 'No original template specified!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$original = new templatesDbTable();
		if (!$original->load($originalid)) {
			$response['message'] = 'Original template not found!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}
		if ($original->section == 'backend') {
			$response['message'] = 'You can copy only frontend templates!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$pat = "#([\']|[\"]|[\$]|[\#]|[\<]|[\>]|[\*]|[\%]|[\~]|[\`]|[\^]|[\|]|[\}]|[\\\])#u";
		$title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW || FILTER_FLAG_STRIP_HIGH);
		$title = eUTF::trim(preg_replace($pat, '', $title));
		if ($title == '') {
			$response['message'] = addslashes(sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('TITLE')));
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$template = filter_input(INPUT_POST, 'template', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW || FILTER_FLAG_STRIP_HIGH);
		$template = trim(preg_replace($pat, '', $template));
		if ($template == '') {
			$response['message'] = addslashes(sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('TEMPLATE')));
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}
		$b = trim(preg_replace("/[^a-z0-9_]/", '', $template));
		if ($b != $template) {
			$response['message'] = addslashes(sprintf($eLang->get('FIELDNOACCCHAR'), $eLang->get('TEMPLATE')));
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}
		if (file_exists(ELXIS_PATH.'/templates/'.$template)) {
			$response['message'] = 'There is already a template named '.$template.'!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}
		if (strpos($template, $original->template) !== false) {
			$response['message'] = 'The new template name contains part of the original one!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}
		$originaltemplate = $original->template;

		unset($original);

		$ok = $eFiles->copyFolder('templates/'.$originaltemplate.'/', 'templates/'.$template.'/', false, false);
		if (!$ok) {
			$response['message'] = 'Copying template '.$originaltemplate.' to '.$template.' failed!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		if (file_exists(ELXIS_PATH.'/templates/'.$template.'/'.$originaltemplate.'.xml')) {
			$eFiles->move('templates/'.$template.'/'.$originaltemplate.'.xml', 'templates/'.$template.'/'.$template.'.xml');
			$contents = file_get_contents(ELXIS_PATH.'/templates/'.$template.'/'.$template.'.xml');
			$contents = preg_replace('@<name>(.*?)<\/name>@', '<name>'.$template.'</name>', $contents);
			$contents = preg_replace('@<title>(.*?)<\/title>@', '<title>'.$title.'</title>', $contents);
			$contents = preg_replace('@<created>(.*?)<\/created>@', '<created>'.gmdate('Y-m-d H:i:s').'</created>', $contents);
			$contents = preg_replace('@<version>(.*?)<\/version>@', '<version>1.0</version>', $contents);
			$contents = str_replace('/'.$originaltemplate.'/', '/'.$template.'/', $contents);
			$eFiles->createFile('templates/'.$template.'/'.$template.'.xml', $contents);
			unset($contents);
		}

		if (file_exists(ELXIS_PATH.'/templates/'.$template.'/'.$originaltemplate.'.png')) {
			$eFiles->move('templates/'.$template.'/'.$originaltemplate.'.png', 'templates/'.$template.'/'.$template.'.png');
		}

		$files = $eFiles->listFiles('templates/'.$template.'/', '(.php)$', true, true, false);
		if ($files) {
			foreach ($files as $file) {
				if (strpos($file, 'templates/language/') !== false) { continue; }
				$relpath = str_replace(ELXIS_PATH.'/', '', $file);
				$contents = file_get_contents($file);
				$contents = str_replace('/'.$originaltemplate.'/', '/'.$template.'/', $contents);
				$contents = str_replace($originaltemplate.'.xml', $template.'.xml', $contents);
				$contents = str_replace('\''.$originaltemplate.'\'', '\''.$template.'\'', $contents);
				$eFiles->createFile($relpath, $contents);
				unset($contents);
			}
		}
		unset($files);

		if (file_exists(ELXIS_PATH.'/templates/'.$template.'/language/')) {
			$files = $eFiles->listFiles('templates/'.$template.'/language/', '(.php)$', false, false, false);
			if ($files) {
				foreach ($files as $file) {
					if (strpos($file, '.tpl_'.$originaltemplate.'.') === false) { continue; }
					$newfile = str_replace('.tpl_'.$originaltemplate.'.', '.tpl_'.$template.'.', $file);
					$eFiles->move('templates/'.$template.'/language/'.$file, 'templates/'.$template.'/language/'.$newfile);
				}
			}
			unset($files);
		}

		$row = new templatesDbTable();
		$row->title = $title;
		$row->template = $template;
		$row->section = 'frontend';
		$row->iscore = 0;
		$row->params = null;
		$row->insert();

		$response['success'] = 1;

		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}

}
	
?>