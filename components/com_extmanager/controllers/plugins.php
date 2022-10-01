<?php 
/**
* @version		$Id: plugins.php 2393 2021-04-07 19:54:28Z IOS $
* @package		Elxis
* @subpackage	Component Extensions Manager
* @copyright	Copyright (c) 2006-2021 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class pluginsExtmanagerController extends extmanagerController {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null, $task='', $model=null) {
		parent::__construct($view, $task, $model);
	}


	/**************************************/
	/* PREPARE TO DISPLAY CONTENT PLUGINS */
	/**************************************/
	public function listplugins() {
		$this->listExtensions('plugins');//base
	}


	/*************************/
	/* SET PLUGIN'S ORDERING */
	/*************************/
	public function setordering() {
		$this->setExtensionOrdering('plugins');//base
	}


	/*****************************************/
	/* TOGGLE PLUGIN'S PUBLISH STATUS (ICON) */
	/*****************************************/
	public function toggleplugin() {
		$this->toggleExtension('plugins');//base
	}


	/*****************/
	/* DELETE PLUGIN */
	/*****************/
	public function deleteplugin() {
		$this->deleteExtension('plugins');//base
	}


	/***************/
	/* EDIT PLUGIN */
	/***************/
	public function editplugin() {
		$this->editExtension('plugins');//base
	}


	/***************/
	/* SAVE PLUGIN */
	/***************/
	public function saveplugin() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eSession = eFactory::getSession();

		$sess_token = trim($eSession->get('token_fmextedit'));
		$token = trim(filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		if (($token == '') || ($sess_token == '') || ($sess_token != $token)) {
			exitPage::make('403', 'CEXT-0017', $eLang->get('REQDROPPEDSEC'));
		}

		$task = isset($_POST['task']) ? trim($_POST['task']) : 'save';
		$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
		if ($id < 1) {
			$link = $elxis->makeAURL('extmanager:plugins/');
			$elxis->redirect($link, 'Plugin not found!', true);
		}

		if ($elxis->acl()->check('com_extmanager', 'plugins', 'edit') < 1) {
			$msg = $eLang->get('NOTALLOWACTION');
			$link = $elxis->makeAURL('extmanager:/');
			$elxis->redirect($link, $msg, true);
		}

		$row = new pluginsDbTable();
		if (!$row->load($id)) {
			$link = $elxis->makeAURL('extmanager:plugins/');
			$elxis->redirect($link, 'Plugin not found!', true);
		}

		$old_ordering = $row->ordering;
		$row->ordering = isset($_POST['ordering']) ? (int)$_POST['ordering'] : 0;
		$row->published = isset($_POST['published']) ? (int)$_POST['published'] : 0;
		$row->alevel = isset($_POST['alevel']) ? (int)$_POST['alevel'] : 0;
		$row->title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);

		$xmlfile = ELXIS_PATH.'/components/com_content/plugins/'.$row->plugin.'/'.$row->plugin.'.plugin.xml';
		if (file_exists($xmlfile)) {
			elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
			$params = new elxisParameters('', $xmlfile, 'plugin');
			$parr = (isset($_POST['params'])) ? $_POST['params'] : array();
			$row->params = $params->toString($parr);
			unset($params);
		} else {
			$row->params = null;
		}

		if (!$row->update()) {
			$redirurl = $elxis->makeAURL('extmanager:plugins/edit.html?id='.$id);
			$elxis->redirect($redirurl, $row->getErrorMsg(), true);
		}

		if ($old_ordering <> $row->ordering) { $row->reorder(); }

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

		$redirurl = $elxis->makeAURL('extmanager:plugins/');
		if ($task == 'apply') { $redirurl .= 'edit.html'; }
		if ($p) { $redirurl .= '?'.implode('&', $p); }
		$elxis->redirect($redirurl, $eLang->get('ITEM_SAVED'));
	}


	/*******************************/
	/* PREPARE PLUGIN USAGE REPORT */
	/*******************************/
	public function pluginusage() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
		if ($id < 1) {
			echo '<div class="elx5_error">No plugin requested!</div>';
			return;
		}

		$row = new pluginsDbTable();
		if (!$row->load($id)) {
			echo '<div class="elx5_error">Plugin not found!</div>';
			return;
		}
		$plugin = $row->plugin;
		$plugin_title = $row->title;
		unset($row);

		if ($plugin == 'elink') {
			$qsyntax = 'href="#elink:';
		} else {
			if (!file_exists(ELXIS_PATH.'/components/com_content/plugins/'.$plugin.'/'.$plugin.'.plugin.php')) {
				echo '<div class="elx5_error">Plugin '.$plugin.' PHP file was not found!</div>';
				return;
			}
			elxisLoader::loadFile('components/com_content/plugins/plugin.interface.php');
			elxisLoader::loadFile('components/com_content/plugins/'.$plugin.'/'.$plugin.'.plugin.php');
			$class = $plugin.'Plugin';
			$langfile = ELXIS_PATH.'/components/com_content/plugins/'.$plugin.'/language/en.plugin_'.$plugin.'.php';
			if (file_exists($langfile)) { $eLang->loadFile($langfile); }

			$plugObj = new $class();
			$syntax = $plugObj->syntax();
			unset($plugObj);
			$nopen = strpos($syntax, '{');
			$nclose = strpos($syntax, '}');
			if (($nopen === false) || ($nopen !== 0) || ($nclose === false)) {
				echo '<div class="elx5_error">Could not determine plugin '.$plugin.' usage!</div>';
				return;
			}

			$rest = substr($syntax, 0, $nclose);
			$rest = trim(preg_replace('@^(\{)@', '', $rest));
			$parts = preg_split('/\s+/', $rest, -1, PREG_SPLIT_NO_EMPTY);
			$qsyntax = '{'.$parts[0].'}';
			unset($class, $langfile, $syntax, $nopen, $nclose, $rest, $parts);
		}

		$usage = $this->model->getPluginUsage($qsyntax);

		eFactory::getDocument()->setTitle($plugin.' :: '.$eLang->get('USAGE'));

		$this->view->pluginusageHTML($plugin, $plugin_title, $usage, $elxis, $eLang);
	}

}
	
?>