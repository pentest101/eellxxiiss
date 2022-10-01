<?php 
/**
* @version		$Id: install.php 2356 2020-10-17 18:23:20Z IOS $
* @package		Elxis
* @subpackage	Component Extensions Manager
* @copyright	Copyright (c) 2006-2020 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class installExtmanagerController extends extmanagerController {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null, $task='', $model=null) {
		parent::__construct($view, $task, $model);
	}


	/************************************************/
	/* PREPARE TO DISPLAY COMPONENT'S CONTROL PANEL */
	/************************************************/
	public function ipanel() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eDoc = eFactory::getDocument();

		$sync = array('components' => array(), 'modules' => array(), 'plugins' => array(), 'templates' => array(), 'engines' => array(), 'auths' => array());
		$subdbupdated = true;
		if (defined('ELXIS_MULTISITE') && (ELXIS_MULTISITE != 1)) {
			$subdbupdated = $this->model->dbisUptodate();
			if (isset($_GET['upsubdb'])) {
				if (!$subdbupdated) {
					$this->model->updateDatabase();
					$subdbupdated = $this->model->dbisUptodate();
				}
			}
			$sync = $this->syncExtensions();
		}

		$eDoc->addFontAwesome();
		$eDoc->addStyleLink($elxis->secureBase().'/components/com_extmanager/css/extmanager'.$eLang->getinfo('RTLSFX').'.css');
		$eDoc->addScriptLink($elxis->secureBase().'/components/com_extmanager/js/extmanager.js');

		eFactory::getPathway()->addNode($eLang->get('EXTENSIONS'), 'extmanager:/');
		$eDoc->setTitle($eLang->get('EXTENSIONS'));

		$this->view->ipanel($sync, $subdbupdated, $elxis, $eLang);
	}


	/************************************************/
	/* GET EXTENSIONS AVAILABLE FOR SYNCHRONIZATION */
	/************************************************/
	private function syncExtensions() {
		$eFiles = eFactory::getFiles();

		$sync = array('components' => array(), 'modules' => array(), 'plugins' => array(), 'templates' => array(), 'engines' => array(), 'auths' => array());

		$fcomps = array();
		$icomps = array();
		$cdirs = $eFiles->listFolders('components/');
		if ($cdirs) {
			foreach ($cdirs as $cdir) {
				$cname = preg_replace('#^(com_)#', '', $cdir);
				if (file_exists(ELXIS_PATH.'/components/'.$cdir.'/'.$cname.'.php')) { $fcomps[] = $cdir; }
			}
		}

		$options = array('sn' => 'component', 'so' => 'ASC', 'limitstart' => 0, 'limit' => 500);
		$dbcomps = $this->model->getComponents($options);
		if ($dbcomps) {
			foreach ($dbcomps as $dbcomp) { $icomps[] = $dbcomp->component; }
		}

		if ($fcomps) {
			foreach ($fcomps as $fcomp) {
				if (!in_array($fcomp, $icomps)) { $sync['components'][] = $fcomp; }
			}
		}
		unset($cdirs, $fcomps, $icomps, $dbcomps);

		$fmods = array();
		$imods = array('mod_content');
		$mdirs = $eFiles->listFolders('modules/');
		if ($mdirs) {
			foreach ($mdirs as $mdir) {
				if (file_exists(ELXIS_PATH.'/modules/'.$mdir.'/'.$mdir.'.php')) { $fmods[] = $mdir; }
			}
		}

		$options = array('sn' => 'module', 'so' => 'ASC', 'limitstart' => 0, 'limit' => 900);
		$dbmods = $this->model->getModules($options);
		if ($dbmods) {
			foreach ($dbmods as $dbmod) { $imods[] = $dbmod->module; }
			$imods = array_unique($imods);
		}

		if ($fmods) {
			foreach ($fmods as $fmod) {
				if (!in_array($fmod, $imods)) { $sync['modules'][] = $fmod; }
			}
		}
		unset($mdirs, $fmods, $imods, $dbmods);

		$fplgs = array();
		$iplgs = array();
		$pdirs = $eFiles->listFolders('components/com_content/plugins/');
		if ($pdirs) {
			foreach ($pdirs as $pdir) {
				if (file_exists(ELXIS_PATH.'/components/com_content/plugins/'.$pdir.'/'.$pdir.'.plugin.php')) { $fplgs[] = $pdir; }
			}
		}

		$options = array('sn' => 'plugin', 'so' => 'ASC', 'limitstart' => 0, 'limit' => 500);
		$dbplgs = $this->model->getPlugins($options);
		if ($dbplgs) {
			foreach ($dbplgs as $dbplg) { $iplgs[] = $dbplg->plugin; }
		}

		if ($fplgs) {
			foreach ($fplgs as $fplg) {
				if (!in_array($fplg, $iplgs)) { $sync['plugins'][] = $fplg; }
			}
		}
		unset($pdirs, $fplgs, $iplgs, $dbplgs);

		$ftpls = array();
		$itpls = array();
		$tdirs = $eFiles->listFolders('templates/');
		if ($tdirs) {
			foreach ($tdirs as $tdir) {
				if ($tdir == 'admin') { continue; }
				if (file_exists(ELXIS_PATH.'/templates/'.$tdir.'/index.php')) { $ftpls[] = $tdir; }
			}
		}

		$options = array('section' => 'frontend', 'sn' => 'template', 'so' => 'ASC', 'limitstart' => 0, 'limit' => 500);
		$dbtpls = $this->model->getTemplates($options);
		if ($dbtpls) {
			foreach ($dbtpls as $dbtpl) { $itpls[] = $dbtpl->template; }
		}

		if ($ftpls) {
			foreach ($ftpls as $ftpl) {
				if (!in_array($ftpl, $itpls)) { $sync['templates'][] = $ftpl; }
			}
		}
		unset($tdirs, $ftpls, $itpls, $dbtpls);

		$fengs = array();
		$iengs = array();
		$edirs = $eFiles->listFolders('components/com_search/engines/');
		if ($edirs) {
			foreach ($edirs as $edir) {
				if (file_exists(ELXIS_PATH.'/components/com_search/engines/'.$edir.'/'.$edir.'.engine.php')) { $fengs[] = $edir; }
			}
		}

		$options = array('sn' => 'engine', 'so' => 'ASC', 'limitstart' => 0, 'limit' => 500);
		$dbengs = $this->model->getEngines($options);
		if ($dbengs) {
			foreach ($dbengs as $dbeng) { $iengs[] = $dbeng->engine; }
		}

		if ($fengs) {
			foreach ($fengs as $feng) {
				if (!in_array($feng, $iengs)) { $sync['engines'][] = $feng; }
			}
		}
		unset($edirs, $fengs, $iengs, $dbengs);

		$faths = array();
		$iaths = array();
		$adirs = $eFiles->listFolders('components/com_user/auth/');
		if ($adirs) {
			foreach ($adirs as $adir) {
				if (file_exists(ELXIS_PATH.'/components/com_user/auth/'.$adir.'/'.$adir.'.auth.php')) { $faths[] = $adir; }
			}
		}

		$options = array('sn' => 'auth', 'so' => 'ASC', 'limitstart' => 0, 'limit' => 500);
		$dbaths = $this->model->getAuthMethods($options);
		if ($dbaths) {
			foreach ($dbaths as $dbath) { $iaths[] = $dbath->auth; }
		}

		if ($faths) {
			foreach ($faths as $fath) {
				if (!in_array($fath, $iaths)) { $sync['auths'][] = $fath; }
			}
		}
		unset($adirs, $faths, $iaths, $dbaths);

		return $sync;
	}


	/***************************************/
	/* PERFORM SYSTEM DOWNLOAD AND INSTALL */
	/***************************************/
	public function installextensionsys() {
		$this->installextension(true);
	}


	/***********************************/
	/* PERFORM USER UPLOAD AND INSTALL */
	/***********************************/
	public function installextension($system=false) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eSession = eFactory::getSession();
		$eFiles = eFactory::getFiles();

		if (defined('ELXIS_MULTISITE') && (ELXIS_MULTISITE != 1)) {
			$this->view->installError($eLang->get('THIS_IS_SUBSITE').' '.$eLang->get('INST_EXT_MOTHERSITE'));
			exit;
		}

		$can_install = $elxis->acl()->check('com_extmanager', 'components', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'modules', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'templates', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'engines', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'auth', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'plugins', 'install');
		if ($can_install > 0) {
			if (($elxis->getConfig('SECURITY_LEVEL') > 0) && ($elxis->user()->gid <> 1)) { $can_install = 0; }
		}

		if ($can_install < 1) {
			$this->view->installError($eLang->get('NOTALLOWACTION'));
			exit;
		}

		$sess_token = trim($eSession->get('token_extmaninst'));
		$token = trim(filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		if (($token == '') || ($sess_token == '') || ($sess_token != $token)) {
			$this->view->installError($eLang->get('REQDROPPEDSEC'));
			exit;
		}

		$packok = false;
		if (isset($_FILES) && isset($_FILES['package']) && ($_FILES['package']['name'] != '') && ($_FILES['package']['error'] == 0) && ($_FILES['package']['size'] > 0)) {
			$packok = true;
		}

		if (!$packok) {
			$this->view->installError($eLang->get('NO_PACK_UPLOADED'));
			exit;
		}

		$filext = strtolower($eFiles->getExtension($_FILES['package']['name']));
		if ($filext != 'zip') {
			$this->view->installError($eLang->get('ELXIS_PACK_MUST_ZIP'));
			exit;
		}

		$upfile = 'package_'.date('YmdHis').'_'.rand(100, 999).'.zip';

		$ok = $eFiles->upload($_FILES['package']['tmp_name'], 'tmp/'.$upfile, true);
		if (!$ok) {
			$this->view->installError($eFiles->getError());
			exit;
		}

		elxisLoader::loadFile('components/com_extmanager/includes/installer.class.php');
		$installer = new elxisInstaller();
		$ok = $installer->prepare($upfile);
		if (!$ok) {
			$this->view->installError($installer->getError());
			exit;
		}

		if (($system == false) && ($installer->isUpdate() === true)) {
			$installer->deletePackage();
			$this->view->confirmUpdate($installer, $eLang);
			exit;
		}

		if (($system == false) && (count($installer->getWarnings()) > 0)) {
			$installer->deletePackage();
			$this->view->confirmInstall($installer, $eLang);
			exit;
		}

		$ok = $installer->install();
		if (!$ok) {
			$this->view->installError($installer->getError());
			exit;
		}

		$this->view->installSuccess($installer, $elxis, $eLang);
	}


	/**************************************/
	/* CONTINUE INSTALL (BY PASS WARNINGS) */
	/**************************************/
	public function extcinstall() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eFiles = eFactory::getFiles();

		if (defined('ELXIS_MULTISITE') && (ELXIS_MULTISITE != 1)) {
			$this->view->installError($eLang->get('THIS_IS_SUBSITE').' '.$eLang->get('INST_EXT_MOTHERSITE'));
			exit;
		}

		$can_install = $elxis->acl()->check('com_extmanager', 'components', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'modules', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'templates', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'engines', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'auth', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'plugins', 'install');
		if ($can_install > 0) {
			if (($elxis->getConfig('SECURITY_LEVEL') > 0) && ($elxis->user()->gid <> 1)) { $can_install = 0; }
		}

		if ($can_install < 1) {
			$this->view->installError($eLang->get('NOTALLOWACTION'));
			exit;
		}

		if (!isset($_POST['ufolder'])) {
			$this->view->installError('Invalid request! ufolder not set.');
			exit;
		}

		$pat = "#([\']|[\"]|[\$]|[\#]|[\<]|[\>]|[\*]|[\%]|[\~]|[\`]|[\^]|[\|]|[\{]|[\}]|[\\\])#u";
		$ufolder = filter_input(INPUT_POST, 'ufolder', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		$ufolder = preg_replace($pat, '', $ufolder);
		$ufolder = trim(str_replace('..', '', $ufolder));
		if (($ufolder == '') || ($ufolder != $_POST['ufolder'])) {
			$this->view->installError('Temporary folder (ufolder) has an invalid name!');
			exit;
		}

		$tmpdir = $eFiles->elxisPath('tmp/', true);
		if (!is_dir($tmpdir.$ufolder.'/')) {
			$this->view->installError('Temporary folder (ufolder) does not exist!');
			exit;
		}

		elxisLoader::loadFile('components/com_extmanager/includes/installer.class.php');
		$installer = new elxisInstaller();
		$ok = $installer->prepareFromFolder($ufolder);
		if (!$ok) {
			$this->view->installError($installer->getError());
			exit;
		}

		$ok = $installer->install();
		if (!$ok) {
			$this->view->installError($installer->getError());
			exit;
		}

		$this->view->installSuccess($installer, $elxis, $eLang);
	}


	/*************************************/
	/* CONTINUE UPDATE (BYPASS WARNINGS) */
	/*************************************/
	public function extcupdate() {
		$this->extcinstall();
	}


	/**************************************************/ 
	/* DOWNLOAD AND INSTALL/UPLOAD EXTENSION FROM EDC */
	/**************************************************/ 
	public function edcinstall() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eFiles = eFactory::getFiles();

		if (defined('ELXIS_MULTISITE') && (ELXIS_MULTISITE != 1)) {
			$this->view->installError($eLang->get('THIS_IS_SUBSITE').' '.$eLang->get('INST_EXT_MOTHERSITE'));
			exit;
		}

		$can_install = $elxis->acl()->check('com_extmanager', 'components', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'modules', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'templates', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'engines', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'auth', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'plugins', 'install');
		if ($can_install > 0) {
			if (($elxis->getConfig('SECURITY_LEVEL') > 0) && ($elxis->user()->gid <> 1)) { $can_install = 0; }
		}

		if ($can_install < 1) {
			$this->view->installError($eLang->get('NOTALLOWACTION'));
			exit;
		}

		$options = array();
		$options['task'] = filter_input(INPUT_POST, 'task', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		$options['edcauth'] = trim(filter_input(INPUT_POST, 'edcauth', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$options['pcode'] = trim(filter_input(INPUT_POST, 'pcode', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));

		if (($options['task'] != 'install') && ($options['task'] != 'update')) {
			$this->view->installError('Invalid request!');
			exit;
		}
		if ($options['edcauth'] == '') {
			$this->view->installError('You are not authorized to access EDC!');
			exit;
		}
		if ($options['pcode'] == '') {
			$this->view->installError('No Elxis package set!');
			exit;
		}

		$str = $this->model->componentParams();
		elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
		elxisLoader::loadFile('components/com_extmanager/includes/edc.class.php');

		$params = new elxisParameters($str, '', 'component');
		$edc = new elxisDC($params);
		$edc_result = $edc->downloadPackage($options);
		unset($edc, $params, $str);

		if (($edc_result['error'] == 1) || ($edc_result['pack'] == '')) {
			$errormsg = ($edc_result['errormsg'] != '') ? $edc_result['errormsg'] : 'Downloading extension from EDC failed!';
			$this->view->installError($errormsg);
			exit;
		}

		$upfile = $edc_result['pack'];
		elxisLoader::loadFile('components/com_extmanager/includes/installer.class.php');
		$installer = new elxisInstaller();
		$ok = $installer->prepare($upfile, true);
		if (!$ok) {
			$this->view->installError($installer->getError());
			exit;
		}

		$ok = $installer->install();
		if (!$ok) {
			$this->view->installError($installer->getError());
			exit;
		}

		$this->view->installSuccess($installer, $elxis, $eLang);
	}


	/*************************/
	/* SYNCHRONIZE EXTENSION */
	/*************************/
	public function syncextension() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eSession = eFactory::getSession();

		if (!defined('ELXIS_MULTISITE') || (ELXIS_MULTISITE == 1)) {
			$this->view->installError($eLang->get('SYNC_EXT_SUBSITES'));
			exit;
		}

		$can_install = $elxis->acl()->check('com_extmanager', 'components', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'modules', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'templates', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'engines', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'auth', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'plugins', 'install');
		if ($can_install > 0) {
			if (($elxis->getConfig('SECURITY_LEVEL') > 0) && ($elxis->user()->gid <> 1)) { $can_install = 0; }
		}

		if ($can_install < 1) {
			$this->view->installError($eLang->get('NOTALLOWACTION'));
			exit;
		}

		$sess_token = trim($eSession->get('token_extmansync'));
		$token = trim(filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		if (($token == '') || ($sess_token == '') || ($sess_token != $token)) {
			$this->view->installError($eLang->get('REQDROPPEDSEC'));
			exit;
		}

		$extension = trim(filter_input(INPUT_POST, 'extension', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		if ($extension == '') {
			$this->view->installError($eLang->get('NO_EXT_SELECTED'));
			exit;
		}

		if (preg_match('#^(com\_)#', $extension)) {
			$type = 'component';
			$acltype = 'components';
			$cname = preg_replace('#^(com_)#', '', $extension);
			if (!file_exists(ELXIS_PATH.'/components/'.$extension.'/'.$cname.'.php')) {
				$this->view->installError('Component '.$extension.' does not exist!');
				exit;
			}
		} else if (preg_match('#^(mod\_)#', $extension)) {
			$type = 'module';
			$acltype = 'modules';
			if (!file_exists(ELXIS_PATH.'/modules/'.$extension.'/'.$extension.'.php')) {
				$this->view->installError('Module '.$extension.' does not exist!');
				exit;
			}
		} else {
			if (file_exists(ELXIS_PATH.'/components/com_search/engines/'.$extension.'/'.$extension.'.engine.php')) {
				$type = 'engine';
				$acltype = 'engines';
			} else if (file_exists(ELXIS_PATH.'/components/com_user/auth/'.$extension.'/'.$extension.'.auth.php')) {
				$type = 'auth';
				$acltype = 'auth';
			} else if (file_exists(ELXIS_PATH.'/components/com_content/plugins/'.$extension.'/'.$extension.'.plugin.php')) {
				$type = 'plugin';
				$acltype = 'plugins';
			} else {
				$type = 'template';
				$acltype = 'templates';
				if (!file_exists(ELXIS_PATH.'/templates/'.$extension.'/index.php')) {
					$this->view->installError('Template '.$extension.' does not exist!');
					exit;
				}
				if (($extension == 'admin') || ($extension == 'system')) {
					$this->view->installError('Invalid template!');
					exit;
				}
			}
		}

		if ($elxis->acl()->check('com_extmanager', $acltype, 'install') < 1) {
			$this->view->installError($eLang->get('NOTALLOWACTION'));
			exit;
		}

		elxisLoader::loadFile('components/com_extmanager/includes/installer.class.php');
		$installer = new elxisInstaller();
		$ok = $installer->synchronize($type, $extension);
		if (!$ok) {
			$this->view->installError($installer->getError());
			exit;
		}

		$this->view->installSuccess($installer, $elxis, $eLang, true);
	}


	/**************************************************/
	/* CHECK EDC FOR UPDATES FOR INSTALLED EXTENSIONS */
	/**************************************************/
	public function checkupdates() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eDoc = eFactory::getDocument();
		$pathway = eFactory::getPathway();

		if (defined('ELXIS_MULTISITE') && (ELXIS_MULTISITE != 1)) {
			$msg = $eLang->get('THIS_IS_SUBSITE').' '.$eLang->get('INST_EXT_MOTHERSITE');
			$link = $elxis->makeAURL('cpanel:/');
			$elxis->redirect($link, $msg, true);
		}

		$can_install = $elxis->acl()->check('com_extmanager', 'components', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'modules', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'templates', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'engines', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'auth', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'plugins', 'install');
		if ($can_install > 0) {
			if (($elxis->getConfig('SECURITY_LEVEL') > 0) && ($elxis->user()->gid <> 1)) { $can_install = 0; }
		}

		if ($can_install < 1) {
			$link = $elxis->makeAURL('cpanel:/');
			$elxis->redirect($link, $eLang->get('NOTALLOWACTION'), true);
		}

		$extensions = $this->model->getThirdExtensions();
		$dbupdated = $this->model->dbisUptodate();

		$final = array();
		$errormsg = '';
		$elxisid = '';
		$edcauth = '';

		$str = $this->model->componentParams();
		elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
		elxisLoader::loadFile('components/com_extmanager/includes/edc.class.php');
		$params = new elxisParameters($str, '', 'component');
		$edc = new elxisDC($params);
		$elxis_releases = $edc->getElxisReleases();
		unset($str);

		if ($extensions) {
			$refresh_auth = false;
			$edc_result = $edc->getAllExtensions();
			$errormsg = $edc_result['error'];

			$elxisid = $edc->getElxisId();
			$edcauth = $edc->getEdcAuth();

			elxisLoader::loadFile('components/com_extmanager/includes/extension.xml.php');
			$exml = new extensionXML();
			foreach ($extensions as $extension) {
				$info = $exml->quickXML($extension['type'], $extension['name']);
				if ($info['installed'] == false) {
					if ($extension['type'] == 'template') {
						$extension['type'] = 'atemplate';
						$info = $exml->quickXML($extension['type'], $extension['name']);//try admin template
						if ($info['installed'] == false) {
							continue;
						}
					} else {
						continue;
					}
				}
				$inst_version = $info['version'];
				$inst_date = $info['created'];
				$inst_title = $info['title'];
				$inst_author = $info['author'];
				unset($info);

				if ($edc_result['rows']) {
					foreach ($edc_result['rows'] as $k => $edcrow) {
						if (($edcrow['type'] == $extension['type']) && ($edcrow['name'] == $extension['name'])) {
							$edcrow['inst_version'] = $inst_version;
							$edcrow['inst_date'] = $inst_date;
							$final[] = $edcrow;
							 //If we used a cached version and there is an update => Refresh edcauth!
							if (($edcauth == '') && ($edcrow['inst_version'] != $edcrow['version'])) { $refresh_auth = true; }
							unset($edc_result['rows'][$k]);
							break;
						}
					}
				} else {
					$final[] = array(
						'id' => 0,
						'catid' => 0,
						'uid' => 0,
						'type' => $extension['type'],
						'name' => $extension['name'],
						'title' => $inst_title,
						'author' => $inst_author,
						'version' => '',
						'created' => 0,
						'modified' => 0,
						'pcode' => '',
						'inst_version' => $inst_version,
						'inst_date' => $inst_date,
						'compatibility' => '',
						'edclink' => ''
					);
				}
			}
			unset($edcrows, $exml);
			
			if ($refresh_auth) {
				$edc = new elxisDC($params);
				$edc->connect();
				$edcauth = $edc->getEdcAuth();
				unset($edc);
			}
			unset($params, $refresh_auth);
		}
		unset($extensions, $edc);

		$eDoc->addFontAwesome();
		$eDoc->addFontElxis();
		$eDoc->addStyleLink($elxis->secureBase().'/components/com_extmanager/css/extmanager'.$eLang->getinfo('RTLSFX').'.css');
		$eDoc->addScriptLink($elxis->secureBase().'/components/com_extmanager/js/extmanager.js');

		$pathway->addNode($eLang->get('EXTENSIONS'), 'extmanager:/');
		$pathway->addNode($eLang->get('CHECK_UPDATES'));
		$eDoc->setTitle($eLang->get('CHECK_UPDATES'));

		$this->view->updates($final, $elxis_releases, $errormsg, $elxisid, $edcauth, $dbupdated, $elxis, $eLang);
	}


	/*************************/
	/* UPDATE ELXIS DATABASE */
	/*************************/
	public function updateelxisdb($return_result=false) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		if (defined('ELXIS_MULTISITE') && (ELXIS_MULTISITE != 1)) {
			$msg = $eLang->get('THIS_IS_SUBSITE').' '.$eLang->get('INST_EXT_MOTHERSITE');
			if ($return_result) {
				$response = array('success' => 0, 'message' => $msg);
				return $response;
			}
			$link = $elxis->makeAURL('cpanel:/');
			$elxis->redirect($link, $msg, true);
		}

		$can_install = $elxis->acl()->check('com_extmanager', 'components', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'modules', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'templates', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'engines', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'auth', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'plugins', 'install');
		if ($can_install > 0) {
			if (($elxis->getConfig('SECURITY_LEVEL') > 0) && ($elxis->user()->gid <> 1)) { $can_install = 0; }
		}

		if ($can_install < 1) {
			if ($return_result) {
				$response = array('success' => 0, 'message' => $eLang->get('NOTALLOWACTION'));
				return $response;
			}
			$link = $elxis->makeAURL('cpanel:/');
			$elxis->redirect($link, $eLang->get('NOTALLOWACTION'), true);
		}

		$link = $elxis->makeAURL('extmanager:install/updates.html');

		$dbupdated = $this->model->dbisUptodate();
		if ($dbupdated) {
			if ($return_result) {
				$response = array('success' => 1, 'message' => '');
				return $response;
			}
			$elxis->redirect($link);
		}

		$this->model->updateDatabase();

		if ($return_result) {
			$response = array('success' => 1, 'message' => '');
			return $response;
		}
		$elxis->redirect($link);
	}


	/******************************************************************/
	/* DO FILESYSTEM INTEGRITY CHECK USING HASHES FILE FROM ELXIS.ORG */
	/******************************************************************/
	public function checkfilesystem() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eDoc = eFactory::getDocument();
		$pathway = eFactory::getPathway();

		$prompt_upelxis = false;

		if (defined('ELXIS_MULTISITE') && (ELXIS_MULTISITE != 1)) {
			$link = $elxis->makeAURL('cpanel:/');
			$elxis->redirect($link);
		}

		$data = array(
			'iversion' => (string)$elxis->getVersion(),
			'irevision' => $elxis->fromVersion('REVISION'),
			'hashfile' => 'elxis_hashes_'.$elxis->getVersion().'.txt',
			'warnings' => array(),
			'infos' => array(),
			'do' => 0,
			'cando' => 1
		);

		$data['do'] = isset($_GET['do']) ? (int)$_GET['do'] : 0;

		elxisLoader::loadFile('components/com_extmanager/includes/edc.class.php');
		$edc = new elxisDC();
		
		if ($data['do'] == 0) {
			$elxis_releases = $edc->getElxisReleases();
			if ($elxis_releases['rows']) {
				if ($data['iversion'] < $elxis_releases['current']) {
					$data['warnings'][] = 'There is a newer Elxis version available (<strong>'.$elxis_releases['current'].'</strong>). Update Elxis or <a href="https://www.elxis.org" target="_blank">Visit elxis.org</a> for more information.';
					$prompt_upelxis = true;
				}
				$iversion = $data['iversion'];
				if (isset($elxis_releases['rows'][$iversion])) {
					$release = $elxis_releases['rows'][$iversion];
					if ($release['revision'] > $data['irevision']) {
						$data['warnings'][] = 'There is an updated <strong>Elxis '.$release['version'].'</strong> '.$release['codename'].' available. Update Elxis! Filesystem check is performed based on the latest Elxis '.$release['version'].' release (r'.$release['revision'].').';
						$prompt_upelxis = true;
					} else {
						$data['infos'][] = 'You have the latest <strong>Elxis '.$release['version'].'</strong> '.$release['codename'].' installed (r'.$release['revision'].').';
					}
				} else {
					$data['warnings'][] = 'No information found on elxis.org for the Elxis version you are using (<strong>Elxis '.$data['iversion'].'</strong>).';
				}
			}
			unset($elxis_releases);
		}

		$hashpath = eFactory::getFiles()->elxisPath('cache/edc/'.$data['hashfile'], true);

		if ($data['do'] == 0) {
			$hashupdate = true;
			if (file_exists($hashpath)) {
				$dt = time() - filemtime($hashpath);
				if ($dt < 7200) {//less than 2 hours old, no need to update it
					$hashupdate = false;
				}
			}

			if ($hashupdate) {
				$ok = $edc->getElxisHashes($data['iversion']);
				if (!$ok) {
					$data['warnings'][] = 'Could not receive <strong>Elxis '.$data['iversion'].'</strong> hashes file from elxis.org';
				}
			}
		}
		unset($edc);

		if (!file_exists($hashpath)) {
			$data['cando'] = 0;
		}

		$pathway->addNode($eLang->get('EXTENSIONS'), 'extmanager:/');
		$pathway->addNode($eLang->get('CHECK_FS'));
		$eDoc->setTitle($eLang->get('CHECK_FS'));
		$eDoc->addStyleLink($elxis->secureBase().'/components/com_extmanager/css/extmanager'.$eLang->getinfo('RTLSFX').'.css');

		if ($data['do'] == 0) {
			$this->view->checkFilesystemHTML($data, array(), $elxis, $eLang, $prompt_upelxis);
			return;
		}

		if (!file_exists($hashpath)) {
			$data['do'] = 0;
			$data['warnings'][] = 'Filesystem check cannot be executed. Cannot download <strong>Elxis '.$data['iversion'].'</strong> hashes file in folder {repository/}cache/edc/';
			$this->view->checkFilesystemHTML($data, array(), $elxis, $eLang, $prompt_upelxis);
			return;
		}

		@clearstatcache();

		$temp = array();
		$lines = file($hashpath);
		foreach ($lines as $i => $line) {
			if (trim($line == '')) { continue; }
			if ($i == 0) { continue; } //Version:Elxis X.X revXXXX DATE:XXXX-XX-XX XX:XX:XX
			$parts = explode(',', $line);
			$temp[] = array($parts[0], trim($parts[1]), trim($parts[2]));
		}
		unset($lines);

		$results = array();
		$repl = array('/[\r\n]/','/[\n]/');
		foreach ($temp as $file) {
			if (!file_exists(ELXIS_PATH.'/'.$file[0])) {
				$results[] = array($file[0], 'notfound');
			} else if (md5_file(ELXIS_PATH.'/'.$file[0]) != $file[1]) {
				$txt = file_get_contents(ELXIS_PATH.'/'.$file[0]);
				if (md5(preg_replace($repl, '', $txt)) != $file[2]) {
					$results[] = array($file[0], 'notuptodate');
				}
			}
		}
		unset($temp, $repl);

		$this->view->checkFilesystemHTML($data, $results, $elxis, $eLang, $prompt_upelxis);
	}


	/************************/
	/* PERFORM ELXIS UPDATE */
	/************************/
	public function updateelxis() {
		$elxis = eFactory::getElxis();
		$eDoc = eFactory::getDocument();
		$eLang = eFactory::getLang();

		if (defined('ELXIS_MULTISITE') && (ELXIS_MULTISITE != 1)) {
			echo '<div class="elx5_error">'.$eLang->get('THIS_IS_SUBSITE').' You can update Elxis only on the main site!</div>';
			return;
		}

		$can_install = $elxis->acl()->check('com_extmanager', 'components', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'modules', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'templates', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'engines', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'auth', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'plugins', 'install');
		if ($can_install > 0) {
			if (($elxis->getConfig('SECURITY_LEVEL') > 0) && ($elxis->user()->gid <> 1)) { $can_install = 0; }
		}

		if ($can_install < 1) {
			echo '<div class="elx5_error">'.$eLang->get('NOTALLOWACTION').'</div>';
			return;
		}

		$eDoc->addFontAwesome();
		$eDoc->addStyleLink($elxis->secureBase().'/components/com_extmanager/css/extmanager'.$eLang->getinfo('RTLSFX').'.css');

		if (isset($_POST['step'])) {
			$step = (int)$_POST['step'];
		} else if (isset($_GET['step'])) {
			$step = (int)$_GET['step'];
		} else {
			$step = 0;
		}
		if ($step < 0) { $step = 0; }

		if ($step == 7) {//Cleanup files
			$response = $this->upElxisCleanup($elxis);
			if ($response['page'] < $response['maxpage']) {
				$eDoc->addScript('setTimeout("document.getElementById(\'fmupelxis\').submit();", 3000);');
				$next = $response['page'] + 1;
				$this->view->updateElxisHTML(7, 'Clean up ('.$next.'/'.$response['maxpage'].')', '', array('page' => $next));
			} else {
				$link = $elxis->makeAURL('extmanager:install/updates.html');
				$elxis->redirect($link);
			}
		} else if ($step == 6) {//Update db, configuration
			$response = $this->updateelxisdb(true);
			if ($response['success'] == 1) {
				$eDoc->addScript('setTimeout("document.getElementById(\'fmupelxis\').submit();", 3000);');
				$this->view->updateElxisHTML(7, 'Clean up');
			} else {
				$eDoc->addScript('setTimeout("document.getElementById(\'fmupelxis\').submit();", 3000);');
				$this->view->updateElxisHTML(6, 'Update database', $response['message']);
			}
		} else if ($step == 5) {
			$response = $this->upElxisUpdateFiles($elxis);
			if ($response['errormsg'] != '') {
				$this->view->updateElxisHTML(5, 'Update files (1/'.$response['maxpage'].')', $response['errormsg']);
			} else {
				if ($response['page'] < $response['maxpage']) {
					$eDoc->addScript('setTimeout("document.getElementById(\'fmupelxis\').submit();", 3000);');
					$next = $response['page'] + 1;
					$this->view->updateElxisHTML(5, 'Update files ('.$next.'/'.$response['maxpage'].')', '', array('page' => $next));
				} else {
					$eDoc->addScript('setTimeout("document.getElementById(\'fmupelxis\').submit();", 3000);');
					$this->view->updateElxisHTML(6, 'Update database');
				}
			}
		} else if ($step == 4) {
			$uptov = isset($_POST['uptoversion']) ? trim($_POST['uptoversion']) : '';
			$response = $this->upElxisCheckFS($uptov, $elxis);
			if ($response['success'] == 1) {
				$eDoc->addScript('setTimeout("document.getElementById(\'fmupelxis\').submit();", 3000);');
				$max = ceil($response['numfilestoupdate'] / 100);
				$this->view->updateElxisHTML(5, 'Update '.$response['numfilestoupdate'].' files (1/'.$max.')');
			} else {
				$this->view->updateElxisHTML(4, 'Filesystem check', $response['message'], array('uptoversion' => $uptov));
			}
		} else if ($step == 3) {
			$uptov = isset($_POST['uptoversion']) ? trim($_POST['uptoversion']) : '';
			elxisLoader::loadFile('components/com_extmanager/includes/edc.class.php');
			$edc = new elxisDC();
			$ok = $edc->getElxisHashes($uptov);
			unset($edc);
			if ($ok) {
				$eDoc->addScript('setTimeout("document.getElementById(\'fmupelxis\').submit();", 3000);');
				$this->view->updateElxisHTML(4, 'Filesystem check', '', array('uptoversion' => $uptov));
			} else {
				$this->view->updateElxisHTML(3, 'Download checksums', 'Downloading Elxis hashes failed!', array('uptoversion' => $uptov));
			}
		} else if ($step == 2) {
			$repo_path = rtrim($elxis->getConfig('REPO_PATH'), '/');
			if ($repo_path == '') { $repo_path = ELXIS_PATH.'/repository'; }
			$sum = 0;
			if (file_exists($repo_path.'/tmp/elxis/')) {//check if it is already unzipped
				if (file_exists($repo_path.'/tmp/elxis/components/')) { $sum++; }
				if (file_exists($repo_path.'/tmp/elxis/includes/version.php')) { $sum++; }
				if (file_exists($repo_path.'/tmp/elxis/language/')) { $sum++; }
				if (file_exists($repo_path.'/tmp/elxis/templates/')) { $sum++; }
				if (file_exists($repo_path.'/tmp/elxis/index.php')) { $sum++; }
			}
			if ($sum == 5) {//files already there
				include($repo_path.'/tmp/elxis/includes/version.php');
				$uptoversion = $elxis_version['RELEASE'].'.'.$elxis_version['LEVEL'];//required for hashes download
				$eDoc->addScript('setTimeout("document.getElementById(\'fmupelxis\').submit();", 3000);');
				$this->view->updateElxisHTML(3, 'Download checksums', '', array('uptoversion' => $uptoversion));
			} else {
				$zip = $elxis->obj('zip');
				$ok = $zip->unzip($repo_path.'/tmp/elxis.zip', $repo_path.'/tmp/elxis/');
				if (!$ok) {
					$errormsg = $zip->getError();
					unset($zip);
					$this->view->updateElxisHTML(2, 'Unzip Elxis package', $errormsg);
				} else {
					include($repo_path.'/tmp/elxis/includes/version.php');
					$uptoversion = $elxis_version['RELEASE'].'.'.$elxis_version['LEVEL'];//required for hashes download
					$eDoc->addScript('setTimeout("document.getElementById(\'fmupelxis\').submit();", 3000);');
					$this->view->updateElxisHTML(3, 'Download checksums', '', array('uptoversion' => $uptoversion));
				}
			}
		} else if ($step == 1) {
			elxisLoader::loadFile('components/com_extmanager/includes/edc.class.php');
			$edc = new elxisDC();
			$response = $edc->downloadElxis();
			if ($response['success'] == 1) {
				$eDoc->addScript('setTimeout("document.getElementById(\'fmupelxis\').submit();", 3000);');
				$this->view->updateElxisHTML(2, 'Unzip Elxis package');
			} else {
				$msg = $response['message'].' - If the problem persists download elxis from elxis.org, name it as elxis.zip and place it inside folder repository/tmp/';
				$this->view->updateElxisHTML(1, 'Download Elxis', $response['message']);
			}
		} else {//step = 0
			$this->view->updateElxisHTML(1, 'Download Elxis');
		}
	}


	/***********************************/
	/* UPDATE ELXIS / CHECK FILESYSTEM */
	/***********************************/
	private function upElxisCheckFS($upversion, $elxis) {
		$repo_path = rtrim($elxis->getConfig('REPO_PATH'), '/');
		if ($repo_path == '') { $repo_path = ELXIS_PATH.'/repository'; }

		$response = array('success' => 0, 'numfilestoupdate' => 0, 'message' => '');

		$hashpath = $repo_path.'/cache/edc/elxis_hashes_'.$upversion.'.txt';
		if (!file_exists($hashpath)) {
			$response['message'] = 'Hashes file not found!';
			return $response;
		}

		@clearstatcache();

		$temp = array();
		$lines = file($hashpath);
		foreach ($lines as $i => $line) {
			if (trim($line == '')) { continue; }
			if ($i == 0) { continue; } //Version:Elxis X.X revXXXX DATE:XXXX-XX-XX XX:XX:XX
			$parts = explode(',', $line);
			$temp[] = array($parts[0], trim($parts[1]), trim($parts[2]));
		}
		unset($lines);

		$datatxt = '';
		$repl = array('/[\r\n]/','/[\n]/');
		foreach ($temp as $file) {
			if (!file_exists(ELXIS_PATH.'/'.$file[0])) {
				$datatxt .= $file[0]."\n";
				$response['numfilestoupdate']++;
			} else if (md5_file(ELXIS_PATH.'/'.$file[0]) != $file[1]) {
				$txt = file_get_contents(ELXIS_PATH.'/'.$file[0]);
				if (md5(preg_replace($repl, '', $txt)) != $file[2]) {
					$datatxt .= $file[0]."\n";
					$response['numfilestoupdate']++;
				}
			}
		}
		unset($temp, $repl);

		if ($datatxt == '') {
			$response['success'] = 1;
			return $response;
		}

		if ($handle = @fopen($repo_path.'/tmp/files_to_update.txt', 'w')) {
			@fwrite($handle, $datatxt);
			fclose($handle);
			$response['success'] = 1;
			return $response;
		}

		$response['message'] = 'Could not save list of files that need to be updated!';
		return $response;
	}


	/***************************/
	/* UPDATE ELXIS FILESYSTEM */
	/***************************/
	private function upElxisUpdateFiles($elxis) {
		$repo_path = rtrim($elxis->getConfig('REPO_PATH'), '/');
		if ($repo_path == '') { $repo_path = ELXIS_PATH.'/repository'; }

		$fileslimit = 100;//update 100 files per turn
		$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
		if ($page < 1) { $page = 1; }
		$lines = file($repo_path.'/tmp/files_to_update.txt');

		$response = array(
			'total' => count($lines),//-1 because the last line is empty but its ok, makes no problem
			'page' => $page,
			'maxpage' => 1,
			'limitstart' => 0,
			'errormsg' => ''
		);

		if ($response['total'] > 1) {
			$response['maxpage'] = ceil($response['total'] / $fileslimit);
			if ($response['maxpage'] < 1) { $response['maxpage'] = 1; }
			if ($response['page'] > $response['maxpage']) { $response['page'] = $response['maxpage']; }
			$response['limitstart'] = (($response['page'] - 1) * $fileslimit);
		}

		$q = 0;
		foreach ($lines as $i => $path) {
			if ($i < $response['limitstart']) { continue; }
			if ($q >= $fileslimit) { break; }
			$q++;
			$path = trim($path);//also removes line endings
			if ($path == '') { continue; }
			$dir = dirname(ELXIS_PATH.'/'.$path);
			if (!file_exists($dir)) {
				$ok = @mkdir($dir, 0755, true);
				if (!$ok) {
					$response['errormsg'] = 'Could not create required folder '.$dir.'. Is the parent folder writeable?';
					break;
				}
			}
			$ok = @copy($repo_path.'/tmp/elxis/'.$path, ELXIS_PATH.'/'.$path);
			if (!$ok) {
				$response['errormsg'] = 'Could not copy file '.$path.' to destination directory.';
				break;
			}
		}
		unset($lines);

		return $response;
	}


	/*****************************************/
	/* UPDATE ELXIS: CLEANUP TEMPORARY FILES */
	/*****************************************/
	private function upElxisCleanup($elxis) {
		$repo_path = rtrim($elxis->getConfig('REPO_PATH'), '/');
		if ($repo_path == '') { $repo_path = ELXIS_PATH.'/repository'; }

		$response = array('page' => 1, 'maxpage' => 4);//never return error, just continue
		$response['page'] = isset($_POST['page']) ? (int)$_POST['page'] : 1;
		if ($response['page'] < 1) { $response['page'] = 1; }
		if ($response['page'] > $response['maxpage']) { $response['page'] = $response['maxpage']; }

		if ($response['page'] == 1) {
			$abspath = $repo_path.'/tmp/elxis/includes/libraries/';
			if (file_exists($abspath)) {
				$this->upElxisDeleteFolder($abspath);
			}
			return $response;
		}

		if ($response['page'] == 2) {
			$abspath = $repo_path.'/tmp/elxis/includes/';
			if (file_exists($abspath)) {
				$this->upElxisDeleteFolder($abspath);
			}
			$abspath = $repo_path.'/tmp/elxis/media/';
			if (file_exists($abspath)) {
				$this->upElxisDeleteFolder($abspath);
			}
			return $response;
		}

		if ($response['page'] == 3) {
			$abspath = $repo_path.'/tmp/elxis/language/';
			if (file_exists($abspath)) {
				$this->upElxisDeleteFolder($abspath);
			}
			$abspath = $repo_path.'/tmp/elxis/modules/';
			if (file_exists($abspath)) {
				$this->upElxisDeleteFolder($abspath);
			}
			$abspath = $repo_path.'/tmp/elxis/templates/';
			if (file_exists($abspath)) {
				$this->upElxisDeleteFolder($abspath);
			}
			return $response;
		}

		$abspath = $repo_path.'/tmp/elxis/components/';
		if (file_exists($abspath)) {
			$this->upElxisDeleteFolder($abspath);
		}
		$abspath = $repo_path.'/tmp/elxis/';
		if (file_exists($abspath)) {
			$this->upElxisDeleteFolder($abspath);
		}

		$abspath = $repo_path.'/tmp/elxis.zip';
		if (file_exists($abspath)) { @unlink($abspath); }

		$abspath = $repo_path.'/tmp/files_to_update.txt';
		if (file_exists($abspath)) { @unlink($abspath); }

		return $response;
	}


    private function upElxisDeleteFolder($abspath) {
    	$current_dir = opendir($abspath);
    	while ($entry = readdir($current_dir)) {
    		if (($entry != '.') && ($entry != '..')) {
    			if (is_dir($abspath.$entry)) {
    				$this->upElxisDeleteFolder($abspath.$entry.'/');
    			} else {
    				@unlink($abspath.$entry);
    			}
    		}
    	}
    	closedir($current_dir);
    	$ok = @rmdir($abspath);
    	return $ok;
    }

}

?>