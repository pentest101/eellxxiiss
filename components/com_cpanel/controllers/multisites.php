<?php 
/**
* @version		$Id: multisites.php 2432 2022-01-18 19:47:07Z IOS $
* @package		Elxis
* @subpackage	CPanel component
* @copyright	Copyright (c) 2006-2022 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class multisitesCPController extends cpanelController {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null, $model=null) {
		parent::__construct($view, $model);
	}


	/**************************/
	/* LIST / EDIT MULTISITES */
	/**************************/
	public function listsites() {
		$elxis = eFactory::getElxis();
		$eDoc = eFactory::getDocument();
		$eLang = eFactory::getLang();

		if ($elxis->acl()->check('com_cpanel', 'multisites', 'edit') < 1) {
			$url = $elxis->makeAURL('cpanel:/');
			$elxis->redirect($url, $eLang->get('NOTALLOWACCPAGE'), true);
		}

		$curl = $elxis->getConfig('URL');
		$cpath = '';
		$p1 = parse_url($curl);
		if (isset($p1['path'])) {
			$fullpath = trim($p1['path'], '/');
			$p2 = explode('/', $fullpath);
			$n = count($p2) - 1;
			$cpath = $p2[$n];
			unset($n, $p2);
		}
		unset($p1);

		$rows = array();
		if (defined('ELXIS_MULTISITE')) {
			include(ELXIS_PATH.'/configuration.php');
			foreach ($multisites as $id => $multisite) {
				$row = new stdClass;
				$row->id = $id;
				$row->name = $multisite['name'];
				$row->folder = $multisite['folder'];
				$row->active = (bool)$multisite['active'];
				if (ELXIS_MULTISITE == $id) {
					$row->current = true;
					$row->url = $curl;
				} else {
					$row->current = false;
					$row->url = '';
				}
				$rows[] = $row;
			}

			foreach ($rows as $i => $row) {
				if ($row->url == '') {
					$rows[$i]->url = $this->calculateURL($row->folder, $cpath, $curl);
				}
			}
		}

		$dbtypes = array(
			'4D' => '4D',
			'cubrid' => 'Cubrid',
			'dblib' => 'dbLib',
			'firebird' => 'Firebird',
			'freetds' => 'FreeTDS',
			'ibm' => 'IBM',
			'informix' => 'Informix',
			'mssql' => 'msSQL',
			'mysql' => 'MySQL',
			'oci' => 'OCI (Oracle)',
			'odbc' => 'ODBC',
			'odbc_db2' => 'ODBC db2',
			'odbc_access' => 'ODBC MS Access',
			'odbc_mssql' => 'ODBC msSQL',
			'pgsql' => 'PostgreSQL',
			'sqlite' => 'SQLite 3',
			'sqlite2' => 'SQLite 2',
			'sybase' => 'SyBase'
		);

		$newid = $this->makeSiteId();

		$importers = array();
		$files = eFactory::getFiles()->listFiles('includes/libraries/elxis/database/importers/', 'php$');
		if ($files) {
			foreach ($files as $file) {
				$n = strpos($file, '.importer.php');
				if ($n !== false) { $importers[] = substr($file, 0, $n); }
			}
		}

		eFactory::getPathway()->addNode($eLang->get('MULTISITES'));
		$eDoc->setTitle($eLang->get('MULTISITES').' - '.$eLang->get('ADMINISTRATION'));

		$eDoc->addStyleLink($elxis->secureBase().'/components/com_cpanel/css/cp'.$eLang->getinfo('RTLSFX').'.css');
		$eDoc->addScriptLink($elxis->secureBase().'/components/com_cpanel/js/cpanel.js');
		if ($rows) {
			$eDoc->addNativeDocReady('elx5DataTable(\'msitestbl\', false);');
		}

		$this->view->listSites($rows, $dbtypes, $importers, $newid, $elxis, $eLang);
	}


	/*****************************/
	/* CALCULATE MULTISITE'S URL */
	/*****************************/
	private function calculateURL($folder, $cpath, $curl) {
		if ($cpath == '') {
			return rtrim($curl.'/'.$folder, '/');
		}
		$url = preg_replace('#('.$cpath.')$#', $folder, $curl);
		return rtrim($url, '/');
	}


	/*********************/
	/* ENABLE MULTISITES */
	/*********************/
	public function enablemultisites() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eFiles = eFactory::getFiles();

		if ($elxis->acl()->check('com_cpanel', 'multisites', 'edit') < 1) {
			$url = $elxis->makeAURL('cpanel:/');
			$elxis->redirect($url, $eLang->get('NOTALLOWACCPAGE'), true);
		}

		$redirurl = $elxis->makeAURL('cpanel:multisites/');

		if (defined('ELXIS_MULTISITE')) { $elxis->redirect($redirurl); }

		$contents = @file_get_contents(ELXIS_PATH.'/configuration.php'); 
		if ($contents === false) {
			$elxis->redirect($redirurl, 'Could not read configuration file', true);
		}

		$ok = $eFiles->createFile('config1.php', $contents);
		if (!$ok) {
			$elxis->redirect($redirurl, 'Could not create config1.php file', true);
		}

		$name = $elxis->getConfig('SITENAME');
		$name = filter_var($name, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$pat = "#([\']|[\"]|[\$]|[\#]|[\<]|[\>]|[\*]|[\%]|[\~]|[\`]|[\^]|[\|]|[\\\])#u";
		$name = eUTF::trim(preg_replace($pat, '', $name));

		$sites = array(
			1 => array('folder' => '', 'name' => $name, 'active' => true)
		);

		$multiconfig = $this->makeMultiConfig($sites);
		$ok = $eFiles->createFile('configuration.php', $multiconfig);
		if (!$ok) {
			$elxis->redirect($redirurl, 'Could not update configuration.php file', true);
		}

		$elxis->redirect($redirurl);
	}


	/****************************/
	/* CREATE MULTISITE ENTRIES */
	/****************************/
	private function makeMultiConfig($sites) {
		$out = '<?php '._LEND;
		$out .= '/**'._LEND;
		$out .= 'Elxis CMS - Copyright 2006-'.date('Y').' elxis.org. All rights reserved.'._LEND;
		$out .= 'Last saved on '.gmdate('Y-m-d H:i:s').' (UTC)'._LEND;
		$out .= '******************************************/'._LEND._LEND;
		$out .= 'defined(\'_ELXIS_\') or die (\'Direct access to this location is not allowed\');'._LEND._LEND._LEND;
		$out .= '$multisites = array('._LEND;
		$total = count($sites);
		$i = 1;
		foreach ($sites as $id => $site) {
			$acttxt = ($site['active'] === true) ? 'true' : 'false';
			$comma = ($i == $total) ? '' : ',';
			$out .= "\t".''.$id.' => array(\'folder\' => \''.$site['folder'].'\', \'name\' => \''.$site['name'].'\', \'active\' => '.$acttxt.')'.$comma._LEND;
			$i++;
		}
		$out .= ');'._LEND._LEND;
		$out .= 'include(ELXIS_PATH.\'/includes/multiconfig.php\');'._LEND._LEND;
		$out .= '?>';
		return $out;
	}


	/**********************/
	/* DISABLE MULTISITES */
	/**********************/
	public function disablemultisites() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eFiles = eFactory::getFiles();

		if ($elxis->acl()->check('com_cpanel', 'multisites', 'edit') < 1) {
			$url = $elxis->makeAURL('cpanel:/');
			$elxis->redirect($url, $eLang->get('NOTALLOWACCPAGE'), true);
		}

		$redirurl = $elxis->makeAURL('cpanel:multisites/');

		if (!defined('ELXIS_MULTISITE')) { $elxis->redirect($redirurl); }

		if (ELXIS_MULTISITE != 1) {
			$msg = sprintf($eLang->get('MAN_MULTISITES_ONLY'), '1');
			$elxis->redirect($url, $msg, true);
		}

		if (!file_exists(ELXIS_PATH.'/config1.php')) {
			$elxis->redirect($redirurl, 'Configuration file 1 does not exist!', true);
		}

		$contents = @file_get_contents(ELXIS_PATH.'/config1.php'); 
		if ($contents === false) {
			$elxis->redirect($redirurl, 'Could not read configuration file 1', true);
		}

		$ok = $eFiles->createFile('configuration.php', $contents);
		if (!$ok) {
			$elxis->redirect($redirurl, 'Could not create configuration file', true);
		}

		$eFiles->deleteFile('config1.php');
		for ($i=2; $i<21; $i++) {
			if (file_exists(ELXIS_PATH.'/config'.$i.'.php')) {
				$eFiles->deleteFile('config'.$i.'.php');
			}
		}

		$elxis->redirect($redirurl);
	}


	/************************************/
	/* TOGGLE MULTISITE'S ACTIVE STATUS */
	/************************************/
	public function togglemultisite() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$response = array('success' => 0, 'published' => -1, 'icontitle' => '', 'reloadpage' => 0);

		if ($elxis->acl()->check('com_cpanel', 'multisites', 'edit') < 1) {
			$response['icontitle'] = $eLang->get('NOTALLOWACCPAGE');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		if (!defined('ELXIS_MULTISITE')) {
			$response['icontitle'] = $eLang->get('MULTISITES_DISABLED');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		if (ELXIS_MULTISITE != 1) {
			$response['icontitle'] = sprintf($eLang->get('MAN_MULTISITES_ONLY'), '1');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$id = isset($_POST['elid']) ? (int)$_POST['elid'] : 0;

		if ($id == 1) {
			$response['icontitle'] = 'You cannot deactivate the main site!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		if ($id < 2) {
			$response['icontitle'] = 'No sub-site requested!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$active = false;
		$found = false;
		$newmultisites = array();
		include(ELXIS_PATH.'/configuration.php');
		foreach ($multisites as $mid => $multisite) {
			if ($mid != $id) {
				$newmultisites[$mid] = $multisite;
				continue;
			}
			$found = true;
			$active = ($multisite['active']) ? false : true;
			$newmultisites[$mid] = array('folder' => $multisite['folder'], 'name' => $multisite['name'], 'active' => $active);
		}

		if (!$found) {
			$response['icontitle'] = 'Sub-site with id '.$id.' not found!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$multiconfig = $this->makeMultiConfig($newmultisites);
		$ok = eFactory::getFiles()->createFile('configuration.php', $multiconfig);
		if (!$ok) {
			$response['icontitle'] = 'Could not save sub-site active status. Is file configuration.php writeable?';
		} else if ($active) {
			$response['success'] = 1;
			$response['published'] = 1;
			$response['icontitle'] = $eLang->get('ACTIVE').' - '.$eLang->get('CLICK_TOGGLE_STATUS');
		} else {
			$response['success'] = 1;
			$response['published'] = 0;
			$response['icontitle'] = $eLang->get('INACTIVE').' - '.$eLang->get('CLICK_TOGGLE_STATUS');
		}

		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/***********************/
	/* DELETE A MULTI-SITE */
	/***********************/
	public function deletemultisite() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eFiles = eFactory::getFiles();

		$response = array('success' => 0, 'message' => '');

		if ($elxis->acl()->check('com_cpanel', 'multisites', 'edit') < 1) {
			$response['message'] = $eLang->get('NOTALLOWACTION');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		if (!defined('ELXIS_MULTISITE')) {
			$response['message'] = $eLang->get('MULTISITES_DISABLED');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		if (ELXIS_MULTISITE != 1) {
			$response['message'] = sprintf($eLang->get('MAN_MULTISITES_ONLY'), '1');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$id = (isset($_POST['elids'])) ? (int)$_POST['elids'] : 0;
		if ($id == 1) {
			$response['message'] = 'You cannot delete the main site!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		if ($id < 2) {
			$response['message'] = 'Invalid sub-site id!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$newmultisites = array();
		$found = false;
		include(ELXIS_PATH.'/configuration.php');
		foreach ($multisites as $mid => $multisite) {
			if ($mid == $id) { $found = true; continue; }
			$newmultisites[$mid] = $multisite;
		}

		if (!$found) {
			$response['message'] = 'Sub-site with id '.$id.' not found!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$multiconfig = $this->makeMultiConfig($newmultisites);
		$ok = $eFiles->createFile('configuration.php', $multiconfig);
		if (!$ok) {
			$response['message'] = 'Delete failed! Could not save settings. Is file configuration.php writeable?';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}
		$eFiles->deleteFile('config'.$id.'.php');

		$response['success'] = 1;
		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/******************/
	/* SAVE MULTISITE */
	/******************/
	public function savemultisite() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eFiles = eFactory::getFiles();

		$response = array('success' => 0, 'message' => '');

		if ($elxis->acl()->check('com_cpanel', 'multisites', 'edit') < 1) {
			$response['message'] = $eLang->get('NOTALLOWACCPAGE');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		if (!defined('ELXIS_MULTISITE')) {
			$response['message'] = $eLang->get('MULTISITES_DISABLED');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		if (ELXIS_MULTISITE != 1) {
			$response['message'] = sprintf($eLang->get('MAN_MULTISITES_ONLY'), '1');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$id = (isset($_POST['msid'])) ? (int)$_POST['msid'] : 0;
		$pat = "#([\']|[\"]|[\$]|[\#]|[\<]|[\>]|[\*]|[\%]|[\~]|[\`]|[\^]|[\|]|[\\\])#u";
		$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$name = eUTF::trim(preg_replace($pat, '', $name));
		if ($name == '') {
			$response['message'] = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('NAME'));
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		if ($id == 1) {
			$folder = '';
		} else {
			$folder = trim(preg_replace('/[^a-z0-9]/', '', $_POST['folder']));
			if ($folder != $_POST['folder']) {
				$response['message'] = sprintf($eLang->get('FIELDNOACCCHAR'), $eLang->get('URL_ID'));
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit;
			}
			if ($folder == '') {
				$response['message'] = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('URL_ID'));
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit;
			}
			if (is_dir(ELXIS_PATH.'/'.$folder.'/')) {
				$response['message'] = 'There is a folder with the same name as the URL identifier! Please choose an other URL identifier.';
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit;
			}
			if (is_dir(ELXIS_PATH.'/components/com_'.$folder.'/')) {
				$response['message'] = 'There is a component with the same name as the URL identifier! Please choose an other URL identifier.';
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit;
			}
			if (strlen($folder) < 3) {
				$response['message'] = 'URL identifier is too short! Please choose an other URL identifier.';
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit;
			}
		}

		$active = (intval($_POST['active']) == 1) ? true : false;
		if ($id == ELXIS_MULTISITE) { $active = true; }

		$newmultisites = array();
		$unique = true;
		include(ELXIS_PATH.'/configuration.php');
		foreach ($multisites as $mid => $multisite) {
			if ($mid == $id) {
				$newmultisites[$mid] = array('folder' => $folder, 'name' => $name, 'active' => $active);
			} else {
				if ($multisite['folder'] == $folder) { $unique = false; }
				$newmultisites[$mid] = $multisite;
			}
		}

		if (!$unique) {
			$response['message'] = 'URL identifier is not unique! Please choose an other URL identifier.';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		if ($id == 0) {
			$newid = $this->makeSiteId();
			$newmultisites[$newid] = array('folder' => $folder, 'name' => $name, 'active' => $active);

			$dbdata = array();
			$strings = array('db_type', 'db_host', 'db_name', 'db_prefix', 'db_user', 'db_pass', 'db_dsn', 'db_scheme');
			foreach ($strings as $str) {
				$up = strtoupper($str);
				$dbdata[$up] = trim(filter_input(INPUT_POST, $str, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
				$dbdata[$up] = addslashes($dbdata[$up]);
			}
			$dbdata['DB_PORT'] = (int)$_POST['db_port'];

			$db_import = (int)$_POST['db_import'];

			$siteconfig = $this->makeConfig($elxis, $folder, $name, $dbdata);
			$configfile = 'config'.$newid.'.php';
			$ok = $eFiles->createFile($configfile, $siteconfig);
			if (!$ok) {
				$response['message'] = sprintf($eLang->get('CNOT_CREATE_CFG_NEW'), $siteconfig);
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit;
			}

			if ($db_import > 0) {
				$result = $this->importData($dbdata, $db_import);
				if ($result['success'] == false) {
					$response['message'] = $eLang->get('DATA_IMPORT_FAILED').': '.$result['message'];
					$this->ajaxHeaders('application/json');
					echo json_encode($response);
					exit;
				}
			}
		}

		$multiconfig = $this->makeMultiConfig($newmultisites);
		$ok = $eFiles->createFile('configuration.php', $multiconfig);
		if ($ok) {
			$response['success'] = 1;
		} else {
			$response['message'] = 'Could not save settings! Is file configuration.php writeable?';
		}
		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/******************************************/
	/* BACKUP AND IMPORT DATA TO THE NEW SITE */
	/******************************************/
	private function importData($upperdbdata, $db_import=0) {
		$elxis = eFactory::getElxis();
		$eFiles = eFactory::getFiles();
		$eLang = eFactory::getLang();

		$dbdata = array();
		foreach ($upperdbdata as $key => $val) {
			$lowkey = strtolower($key);
			$dbdata[$lowkey] = $val;
		}

		$result = array('success' => false, 'message' => 'Import failed!');

		if (!file_exists(ELXIS_PATH.'/includes/libraries/elxis/database/importers/'.$dbdata['db_type'].'.importer.php')) {
			$result['message'] = $eLang->get('NOT_SUP_DBTYPE');
			return $result;
		}

		if ($dbdata['db_type'] != $elxis->getConfig('DB_TYPE')) {
			$result['message'] = $eLang->get('DBTYPES_MUST_SAME');
			return $result;
		}

		$repo_path = $elxis->getConfig('REPO_PATH');
		if ($repo_path == '') { $repo_path = ELXIS_PATH.'/repository'; }

		$prefix = $elxis->getConfig('DB_PREFIX');
		$noinsert = array();
		if ($db_import < 2) {
			$noinsert[] = $prefix.'categories';
			$noinsert[] = $prefix.'comments';
			$noinsert[] = $prefix.'content';
			$noinsert[] = $prefix.'menu';
			$noinsert[] = $prefix.'translations';
		}
		$noinsert[] = $prefix.'session';
		$userparams = array('no_insert_tables' => $noinsert);
		unset($prefix, $noinsert);

	 	$sql = eFactory::getDB()->backup($userparams);
	 	unset($userparams);

	 	if ($sql === 0) {
	 		return $result;
		} else if ($sql === -1) {
		 	$result['message'] = $eLang->get('NOT_SUP_DBTYPE');
	 		return $result;
	 	} else if ($sql === -2) {
		 	$result['message'] = 'Invalid or insufficient backup parameters!';
	 		return $result;
 		} else if ($sql === -3) {
		 	$result['message'] = $elxis->getConfig('DB_TYPE').' database adapter faced an unrecoverable error!';
 			return $result;
 		}

		$rnd = rand(1000, 9999);
		$sqlfile = $repo_path.'/backup/elxis'.$rnd.'.sql';

		$ok = $eFiles->createFile('backup/elxis'.$rnd.'.sql', $sql, true);
		if (!$ok) {
		 	$result['message'] = 'Could not create backup of this site!';
			return $result;
		}
		unset($sql, $ok);

		$classname = 'elxis'.ucfirst($dbdata['db_type']).'Importer';
		elxisLoader::loadFile('includes/libraries/elxis/database/importers/'.$dbdata['db_type'].'.importer.php');

		$dbdata['db_prefix_old'] = $elxis->getConfig('DB_PREFIX');
		$dbdata['file'] = $sqlfile;

		$importer = new $classname($dbdata);
		if (!$importer->import()) {
			$result['message'] = $importer->getError();
			$importer->disconnect();
			$eFiles->deleteFile('backup/elxis'.$rnd.'.sql', true);
			return $result;
		}

		$eFiles->deleteFile('backup/elxis'.$rnd.'.sql', true);

		$sql = 'DELETE FROM '.$dbdata['db_prefix'].'users WHERE gid <> 1';
		$importer->query($sql);

		$importer->disconnect();
		$result['success'] = true;
		$result['message'] = $eLang->get('DATA_IMPORT_SUC');

		return $result;
	}


	/*****************************/
	/* MAKE AN ID FOR A NEW SITE */
	/*****************************/
	private function makeSiteId() {
		for ($i=2; $i < 101; $i++) {
			if (!file_exists(ELXIS_PATH.'/config'.$i.'.php')) { return $i; }
		}
		return rand(101, 1000);
	}


	/********************************************/
	/* CREATE CONFIGURATION FILE FOR A NEW SITE */
	/********************************************/
	private function makeConfig($elxis, $folder, $name, $dbdata) {
		$cfgvars = array(
			'ATEMPLATE', 'CACHE', 'CACHE_TIME', 'CAPTCHA', 'CRONJOBS', 'CRONJOBS_PROB', 'CSP', 'DB_DSN', 'DB_HOST', 'DB_NAME',
			'DB_PASS', 'DB_PERSISTENT', 'DB_PORT', 'DB_PREFIX', 'DB_SCHEME', 'DB_TYPE', 'DB_USER', 'DEBUG', 'DEFAULT_ROUTE', 'DEFENDER', 'DEFENDER_IPAFTER',
			'DEFENDER_LOG', 'DEFENDER_NOTIFY', 'ENCRYPT_KEY', 'ENCRYPT_METHOD', 'ERROR_ALERT', 'ERROR_LOG', 'ERROR_REPORT', 'FTP', 'FTP_HOST',
			'FTP_PASS', 'FTP_PORT', 'FTP_ROOT', 'FTP_USER', 'GZIP', 'JQUERY', 'LANG', 'LANG_DETECT', 'LOG_ROTATE', 'MAIL_AUTH_METHOD', 'MAIL_EMAIL',
			'MAIL_FROM_EMAIL', 'MAIL_FROM_NAME', 'MAIL_MANAGER_EMAIL', 'MAIL_MANAGER_NAME', 'MAIL_METHOD', 'MAIL_NAME', 'MAIL_SMTP_AUTH', 'MAIL_SMTP_HOST',
			'MAIL_SMTP_PASS', 'MAIL_SMTP_PORT', 'MAIL_SMTP_SECURE', 'MAIL_SMTP_USER', 'METADESC', 'METAKEYS', 'MINICSS', 'MINIJS', 'MULTILINGUISM',
			'OFFLINE_MESSAGE', 'ONLINE', 'PASS_RECOVER', 'REALNAME', 'REGISTRATION', 'REGISTRATION_ACTIVATION', 'REGISTRATION_EMAIL_DOMAIN', 
			'REGISTRATION_EXCLUDE_EMAIL_DOMAINS', 'REPO_PATH', 'SECURITY_LEVEL', 'SEF', 'SEO_MATCH', 'SESSION_ENCRYPT', 'SESSION_HANDLER', 'SESSION_LIFETIME',
			'SESSION_MATCHBROWSER', 'SESSION_MATCHIP', 'SESSION_MATCHREFERER', 'SITELANGS', 'SITENAME', 'SSL', 'STATISTICS', 'TEMPLATE', 'TIMEZONE', 'URL', 'XFOPTIONS'
		);
		sort($cfgvars);

		$out = '<?php '._LEND;
		$out .= '/**'._LEND;
		$out .= 'Elxis CMS - Copyright 2006-'.date('Y').' elxis.org. All rights reserved.'._LEND;
		$out .= 'Last saved on '.gmdate('Y-m-d H:i:s').' (UTC) by '.$elxis->user()->uname._LEND;
		$out .= '******************************************/'._LEND._LEND;
		$out .= 'defined(\'_ELXIS_\') or die (\'Direct access to this location is not allowed\');'._LEND._LEND._LEND;
		$out .= 'class elxisConfig {'._LEND._LEND;

		foreach ($cfgvars as $cfg) {
			switch ($cfg) {
				case 'URL': $v = $elxis->getConfig('URL').'/'.$folder; break;
				case 'DB_TYPE': $v = $dbdata['DB_TYPE']; break;
				case 'DB_HOST': $v = $dbdata['DB_HOST']; break;
				case 'DB_NAME': $v = $dbdata['DB_NAME']; break;
				case 'DB_PREFIX': $v = $dbdata['DB_PREFIX']; break;
				case 'DB_USER': $v = $dbdata['DB_USER']; break;
				case 'DB_PASS': $v = $dbdata['DB_PASS']; break;
				case 'DB_DSN': $v = $dbdata['DB_DSN']; break;
				case 'DB_SCHEME': $v = $dbdata['DB_SCHEME']; break;
				case 'DB_PORT': $v = intval($dbdata['DB_PORT']); break;
				case 'SITENAME': $v = $name; break;
				case 'ONLINE': $v = 0; break;
				case 'MULTILINGUISM': $v = 0; break;
				default: $v = $elxis->getConfig($cfg); break;
			}
			if (is_numeric($v)) {
				$out .= "\t".'private $'.$cfg.' = '.$v.';'._LEND;
			} else {
				$out .= "\t".'private $'.$cfg.' = \''.addslashes($v).'\';'._LEND;
			}
		}

		$out .= _LEND;
		$out .= "\t".'public function __construct() {'._LEND;
		$out .= "\t".'}'._LEND._LEND;
		$out .= "\t".'public function get($var=\'\') {'._LEND;
		$out .= "\t\t".'if (($var != \'\') && isset($this->$var)) { return $this->$var; }'._LEND;
		$out .= "\t\t".'return \'\';'._LEND;
		$out .= "\t".'}'._LEND._LEND;
		$out .= "\t".'public function set($var, $value) {'._LEND;
		$out .= "\t\t".'if (($var == \'\') || (!is_string($var))) { return false; }'._LEND;
		$out .= "\t\t".'if (isset($this->$var)) {'._LEND;
		$out .= "\t\t\t".'if (!in_array($var, array(\'SITENAME\', \'METADESC\', \'METAKEYS\'))) { return false; }'._LEND;
		$out .= "\t\t".'}'._LEND;
		$out .= "\t\t".'$this->$var = $value;'._LEND;
		$out .= "\t\t".'return true;'._LEND;
		$out .= "\t".'}'._LEND._LEND;
		$out .= '}'._LEND._LEND;
		$out .= '?>';

		return $out;
	}

}

?>