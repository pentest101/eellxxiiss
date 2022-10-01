<?php 
/**
* @version		$Id: main.php 2432 2022-01-18 19:47:07Z IOS $
* @package		Elxis
* @subpackage	CPanel component
* @copyright	Copyright (c) 2006-2022 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class mainCPController extends cpanelController {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null, $model=null) {
		parent::__construct($view, $model);
	}


	/************************************/
	/* PREPARE TO MAKE CPANEL DASHBOARD */
	/************************************/
	public function dashboard() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eDoc = eFactory::getDocument();

		$str = $this->model->componentParams('com_extmanager');
		elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
		elxisLoader::loadFile('components/com_extmanager/includes/edc.class.php');
		$params = new elxisParameters($str, '', 'component');
		$edc = new elxisDC($params);
		$updates = $edc->checkForUpdates();
		unset($edc, $str, $params);

		$boarditems = array();
		if ($elxis->acl()->check('com_cpanel', 'settings', 'edit') > 0) {
			$item = new stdClass;
			$item->link = $elxis->makeAURL('cpanel:config.html');
			$item->title = $eLang->get('SETTINGS');
			$item->description = $eLang->get('GENERAL_SITE_SETS');
			$item->iconclass = 'fas fa-tools';
			$item->mark = 0;

			$parts = array();
			if ($elxis->getConfig('MAIL_METHOD') == 'mail') {
				$item->mark++;
				$parts[] = $eLang->get('CHANGE_MAIL_TO_SMTP');
			}
			//if ($elxis->getConfig('DEFENDER') == '') {
			//	$item->mark++;
			//	$parts[] = $eLang->get('DEFENDER_IS_DISABLED');
			//}
			if ($elxis->getConfig('REPO_PATH') == '') {
				$item->mark++;
				$parts[] = $eLang->get('REPO_DEF_PATH');
			}
			if ($elxis->getConfig('SITELANGS') != '') {
				if (strpos($elxis->getConfig('SITELANGS'), ',') === false) {//single language
					if ($elxis->getConfig('MULTILINGUISM') == 1) {
						$item->mark++;
						$parts[] = $eLang->get('DISABLE_MULTILINGUISM');
					}
				} else {
					if ($elxis->getConfig('MULTILINGUISM') == 0) {
						$item->mark++;
						$parts[] = $eLang->get('ENABLE_MULTILINGUISM');
					}
				}
			} else if ($elxis->getConfig('MULTILINGUISM') == 0) {
				$item->mark++;
				$parts[] = $eLang->get('ENABLE_MULTILINGUISM');
			}

			if ($parts) { $item->description = implode(', - ', $parts); }
			unset($parts);
			$boarditems[] = $item;
		}

		if ($elxis->acl()->check('component', 'com_emedia', 'manage') > 0) {
			$item = new stdClass;
			$item->link = $elxis->makeAURL('emedia:/');
			$item->title = $eLang->get('MEDIA_MANAGER');
			$item->description = $eLang->get('MEDIA_MANAGER_INFO');
			$item->iconclass = 'fas fa-images';
			$item->mark = 0;
			$boarditems[] = $item;
		}

		if ($elxis->acl()->check('component', 'com_user', 'manage') > 0) {
			$item = new stdClass;
			$item->link = $elxis->makeAURL('user:users/');
			$item->title = $eLang->get('USERS');
			$item->description = $eLang->get('MANAGE_USERS');
			$item->iconclass = 'fas fa-user-cog';
			$item->mark = 0;
			$boarditems[] = $item;

			if ($elxis->acl()->check('com_user', 'groups', 'manage') > 0) {
				$item = new stdClass;
				$item->link = $elxis->makeAURL('user:groups/');
				$item->title = $eLang->get('USER_GROUPS');
				$item->description = $eLang->get('MANAGE_UGROUPS');
				$item->iconclass = 'fas fa-users-cog';
				$item->mark = 0;
				$boarditems[] = $item;
			}

			if ($elxis->acl()->check('com_user', 'acl', 'manage') > 0) {
				$item = new stdClass;
				$item->link = $elxis->makeAURL('user:acl/');
				$item->title = $eLang->get('ACCESS_MANAGER');
				$item->description = $eLang->get('MANAGE_ACL');
				$item->iconclass = 'fas fa-user-lock';
				$item->mark = 0;
				$boarditems[] = $item;
			}
		}

		if ($elxis->acl()->check('component', 'com_emenu', 'manage') > 0) {
			$item = new stdClass;
			$item->link = $elxis->makeAURL('emenu:/');
			$item->title = $eLang->get('MENU_MANAGER');
			$item->description = $eLang->get('MANAGE_MENUS_ITEMS');
			$item->iconclass = 'fas fa-bars';
			$item->mark = 0;
			$boarditems[] = $item;
		}

		if ($elxis->acl()->check('component', 'com_content', 'manage') > 0) {
			if ($elxis->acl()->check('com_content', 'frontpage', 'edit') > 0) {
				$item = new stdClass;
				$item->link = $elxis->makeAURL('content:fpage/');
				$item->title = $eLang->get('FRONTPAGE');
				$item->description = $eLang->get('DESIGN_FRONTPAGE');
				$item->iconclass = 'fas fa-home';
				$item->mark = 0;
				$boarditems[] = $item;
			}

			$item = new stdClass;
			$item->link = $elxis->makeAURL('content:categories/');
			$item->title = $eLang->get('CATEGORIES_MANAGER');
			$item->description = $eLang->get('MANAGE_CONT_CATS');
			$item->iconclass = 'fas fa-folder';
			$item->mark = 0;
			$boarditems[] = $item;

			$item = new stdClass;
			$item->link = $elxis->makeAURL('content:articles/');
			$item->title = $eLang->get('CONTENT_MANAGER');
			$item->description = $eLang->get('MANAGE_CONT_ITEMS');
			$item->iconclass = 'fas fa-file';
			$item->mark = 0;
			$boarditems[] = $item;
		}

		if ($elxis->acl()->check('component', 'com_etranslator', 'manage') > 0) {
			$item = new stdClass;
			$item->link = $elxis->makeAURL('etranslator:/');
			$item->title = $eLang->get('TRANSLATOR');
			$item->description = $eLang->get('MANAGE_MLANG_CONTENT');
			$item->iconclass = 'fas fa-language';
			$item->mark = 0;
			$boarditems[] = $item;
		}

		if ($elxis->acl()->check('component', 'com_extmanager', 'manage') > 0) {
			if ($elxis->acl()->check('com_extmanager', 'components', 'edit') > 0) {
				$item = new stdClass;
				$item->link = $elxis->makeAURL('extmanager:components/');
				$item->title = $eLang->get('COMPONENTS');
				$item->description = $eLang->get('COMPONENTS_MANAGE_INST');
				$item->iconclass = 'fas fa-cube';
				$item->mark = 0;
				if (isset($updates['extensions']['component'])) {
					$item->mark = count($updates['extensions']['component']);
					if ($item->mark == 1) {
						$item->description = sprintf($eLang->get('NEWER_VERSION_FOR'), $updates['extensions']['component'][0]['version'], $updates['extensions']['component'][0]['title']);
						
					} else {
						$parts = array();
						foreach ($updates['extensions']['component'] as $k => $d) { $parts[] = $d['title']; }
						$str = implode(', ', $parts);
						$item->description = sprintf($eLang->get('NEWER_VERSIONS_FOR'), $str);
					}
				}
				$boarditems[] = $item;
			}
			if ($elxis->acl()->check('com_extmanager', 'modules', 'edit') > 0) {
				$item = new stdClass;
				$item->link = $elxis->makeAURL('extmanager:modules/');
				$item->title = $eLang->get('MODULES');
				$item->description = $eLang->get('MODULES_MANAGE_INST');
				$item->iconclass = 'fas fa-puzzle-piece';
				$item->mark = 0;
				if (isset($updates['extensions']['module'])) {
					$item->mark = count($updates['extensions']['module']);
					if ($item->mark == 1) {
						$item->description = sprintf($eLang->get('NEWER_VERSION_FOR'), $updates['extensions']['module'][0]['version'], $updates['extensions']['module'][0]['title']);
						
					} else {
						$parts = array();
						foreach ($updates['extensions']['module'] as $k => $d) { $parts[] = $d['title']; }
						$str = implode(', ', $parts);
						$item->description = sprintf($eLang->get('NEWER_VERSIONS_FOR'), $str);
					}
				}
				$boarditems[] = $item;
			}
			if ($elxis->acl()->check('com_extmanager', 'plugins', 'edit') > 0) {
				$item = new stdClass;
				$item->link = $elxis->makeAURL('extmanager:plugins/');
				$item->title = $eLang->get('CONTENT_PLUGINS');
				$item->description = $eLang->get('PLUGINS_MANAGE_INST');
				$item->iconclass = 'fas fa-plug';
				$item->mark = 0;
				if (isset($updates['extensions']['plugin'])) {
					$item->mark = count($updates['extensions']['plugin']);
					if ($item->mark == 1) {
						$item->description = sprintf($eLang->get('NEWER_VERSION_FOR'), $updates['extensions']['plugin'][0]['version'], $updates['extensions']['plugin'][0]['title']);
						
					} else {
						$parts = array();
						foreach ($updates['extensions']['plugin'] as $k => $d) { $parts[] = $d['title']; }
						$str = implode(', ', $parts);
						$item->description = sprintf($eLang->get('NEWER_VERSIONS_FOR'), $str);
					}
				}
				$boarditems[] = $item;
			}
			if ($elxis->acl()->check('com_extmanager', 'templates', 'edit') > 0) {
				$item = new stdClass;
				$item->link = $elxis->makeAURL('extmanager:templates/');
				$item->title = $eLang->get('TEMPLATES');
				$item->description = $eLang->get('TEMPLATES_MANAGE_INST');
				$item->iconclass = 'fas fa-paint-brush';
				$item->mark = 0;
				if (isset($updates['extensions']['template'])) {//atemplate ?
					$item->mark = count($updates['extensions']['template']);
					if ($item->mark == 1) {
						$item->description = sprintf($eLang->get('NEWER_VERSION_FOR'), $updates['extensions']['template'][0]['version'], $updates['extensions']['template'][0]['title']);
						
					} else {
						$parts = array();
						foreach ($updates['extensions']['template'] as $k => $d) { $parts[] = $d['title']; }
						$str = implode(', ', $parts);
						$item->description = sprintf($eLang->get('NEWER_VERSIONS_FOR'), $str);
					}
				}
				$boarditems[] = $item;
			}
			if ($elxis->acl()->check('com_extmanager', 'engines', 'edit') > 0) {
				$item = new stdClass;
				$item->link = $elxis->makeAURL('extmanager:engines/');
				$item->title = $eLang->get('SEARCH_ENGINES');
				$item->description = $eLang->get('SENGINES_MANAGE_INST');
				$item->iconclass = 'fas fa-search';
				$item->mark = 0;
				if (isset($updates['extensions']['engine'])) {
					$item->mark = count($updates['extensions']['engine']);
					if ($item->mark == 1) {
						$item->description = sprintf($eLang->get('NEWER_VERSION_FOR'), $updates['extensions']['engine'][0]['version'], $updates['extensions']['engine'][0]['title']);
						
					} else {
						$parts = array();
						foreach ($updates['extensions']['engine'] as $k => $d) { $parts[] = $d['title']; }
						$str = implode(', ', $parts);
						$item->description = sprintf($eLang->get('NEWER_VERSIONS_FOR'), $str);
					}
				}
				$boarditems[] = $item;
			}
			if ($elxis->acl()->check('com_extmanager', 'auth', 'edit') > 0) {
				$item = new stdClass;
				$item->link = $elxis->makeAURL('extmanager:auth/');
				$item->title = $eLang->get('AUTH_METHODS');
				$item->description = $eLang->get('MANAGE_WAY_LOGIN');
				$item->iconclass = 'fas fa-key';
				$item->mark = 0;
				if (isset($updates['extensions']['auth'])) {
					$item->mark = count($updates['extensions']['auth']);
					if ($item->mark == 1) {
						$item->description = sprintf($eLang->get('NEWER_VERSION_FOR'), $updates['extensions']['auth'][0]['version'], $updates['extensions']['auth'][0]['title']);
						
					} else {
						$parts = array();
						foreach ($updates['extensions']['auth'] as $k => $d) { $parts[] = $d['title']; }
						$str = implode(', ', $parts);
						$item->description = sprintf($eLang->get('NEWER_VERSIONS_FOR'), $str);
					}
				}
				$boarditems[] = $item;
			}

			$item = new stdClass;
			$item->link = $elxis->makeAURL('extmanager:browse/');
			$item->title = $eLang->get('ELXISDC');
			$item->description = $eLang->get('ELXISDC_INFO');
			$item->iconclass = 'felxis-logo';
			$item->mark = 0;
			if ($updates['totalextensions'] > 0) {
				$item->mark = $updates['totalextensions'];
				$item->description = sprintf($eLang->get('NEWER_VERSIONS_FOR_EXTS'),  $updates['totalextensions']);
			}
			$boarditems[] = $item;
		}

		if ($elxis->acl()->check('com_cpanel', 'statistics', 'view') > 0) {
			$item = new stdClass;
			$item->link = $elxis->makeAURL('cpanel:stats/');
			$item->title = $eLang->get('SITE_STATISTICS');
			$item->description = $eLang->get('SITE_STATISTICS_INFO');
			$item->iconclass = 'fas fa-chart-pie';
			$item->mark = 0;
			$boarditems[] = $item;
		}

		if ($elxis->acl()->check('com_cpanel', 'backup', 'edit') > 0) {
			$item = new stdClass;
			$item->link = $elxis->makeAURL('cpanel:backup/');
			$item->title = $eLang->get('BACKUP');
			$item->description = $eLang->get('BACKUP_INFO');
			$item->iconclass = 'fas fa-file-archive';
			$item->mark = 0;

			$files = eFactory::getFiles()->listFiles('backup/', '(\.zip)$', false, true, true);
			if ($files) {
				$now = time();
				$ok = false;
				foreach ($files as $file) {
					if ($now - filemtime($file) < 5184000) { $ok = true; break; }//2 months
				}
				if (!$ok) {
					$item->mark = 1;
					$item->description = $eLang->get('LONGTIME_TAKE_BACKUP');
				}
			} else {
				$item->mark = 1;
				$item->description = $eLang->get('NO_BACKUPS');
			}
			unset($files);
			$boarditems[] = $item;
		}

		if ($elxis->acl()->check('com_cpanel', 'cache', 'manage') > 0) {
			$item = new stdClass;
			$item->link = $elxis->makeAURL('cpanel:cache/');
			$item->title = $eLang->get('CACHE');
			$item->description = $eLang->get('CACHE_INFO');
			$item->iconclass = 'fas fa-memory';
			$item->mark = 0;
			$boarditems[] = $item;
		}

		if ($elxis->acl()->check('com_cpanel', 'multisites', 'edit') > 0) {
			$item = new stdClass;
			$item->link = $elxis->makeAURL('cpanel:multisites/');
			$item->title = $eLang->get('MULTISITES');
			$item->description = $eLang->get('MULTISITES_DESC');
			$item->iconclass = 'fas fa-project-diagram';
			$item->mark = 0;
			$boarditems[] = $item;
		}

		if ($elxis->acl()->check('com_cpanel', 'routes', 'manage') > 0) {
			$item = new stdClass;
			$item->link = $elxis->makeAURL('cpanel:routing/');
			$item->title = $eLang->get('ROUTING');
			$item->description = $eLang->get('ROUTING_INFO');
			$item->iconclass = 'fas fa-network-wired';
			$item->mark = 0;
			$boarditems[] = $item;
		}

		if ($elxis->acl()->check('com_cpanel', 'logs', 'manage') > 0) {
			$item = new stdClass;
			$item->link = $elxis->makeAURL('cpanel:logs/');
			$item->title = $eLang->get('LOGS');
			$item->description = $eLang->get('VIEW_MANAGE_LOGS');
			$item->iconclass = 'fas fa-file-signature';
			$item->mark = 0;
			$logfiles = eFactory::getFiles()->listFiles('logs/', '', false, true, true);
			if ($logfiles) {
				if (count($logfiles) > 50) {
					$item->mark = 1;
					$item->description = $eLang->get('DELETE_OLD_LOGS');
				}
			}
			unset($files);
			$boarditems[] = $item;
		}

		$comps = $this->model->getThirdComponents();
		if ($comps) {
			foreach ($comps as $comp) {
				if ($elxis->acl()->check('component', $comp['component'], 'manage') > 0) {
					if (strpos($comp['component'], 'shop') !== false) {
						$iconclass = 'fas fa-shopping-cart';
					} else if ($comp['component'] == 'com_sim') {
						$iconclass = 'fas fa-traffic-light';
					} else if ($comp['component'] == 'com_mikro') {
						$iconclass = 'fas fa-blog';
					} else if (strpos($comp['component'], 'sitemap') !== false) {
						$iconclass = 'fas fa-sitemap';
					} else if (strpos($comp['component'], 'reserv') !== false) {
						$iconclass = 'fas fa-concierge-bell';
					} else if (strpos($comp['component'], 'archive') !== false) {
						$iconclass = 'fas fa-landmark';
					} else if (strpos($comp['component'], 'form') !== false) {
						$iconclass = 'fas fa-keyboard';
					} else if (strpos($comp['component'], 'import') !== false) {
						$iconclass = 'fas fa-file-import'; 
					} else if (strpos($comp['component'], 'content') !== false) {
						$iconclass = 'fas fa-user-edit'; 
					} else if (strpos($comp['component'], 'map') !== false) {
						$iconclass = 'fas fa-map-pin'; 
					} else if (strpos($comp['component'], 'galler') !== false) {
						$iconclass = 'fas fa-images'; 
					} else {
						$iconclass = 'fas fa-cube';
					}

					$comproute = preg_replace('@^(com\_)@', '', $comp['component']);
					$item = new stdClass;
					$item->link = $elxis->makeAURL($comproute.':/');
					$item->title = $comp['name'];
					$item->description = $comp['name'];
					$item->iconclass = $iconclass;
					$item->mark = 0;
					$boarditems[] = $item;
				}
			}
		}
		unset($comps);

		$eDoc->addFontAwesome();
		$eDoc->addFontElxis();

		$eDoc->addStyleLink($elxis->secureBase().'/components/com_cpanel/css/cp'.$eLang->getinfo('RTLSFX').'.css');
		$eDoc->addScriptLink($elxis->secureBase().'/components/com_cpanel/js/cpanel.js');

		$this->view->dashboardHTML($boarditems, $updates, $elxis, $eLang, $eDoc);
	}


	/***********************************/
	/* PREPARE CONFIGURATION EDIT PAGE */
	/***********************************/
	public function configure() {
		$elxis = eFactory::getElxis();
		$eDoc = eFactory::getDocument();
		$eLang = eFactory::getLang();
		$eFiles = eFactory::getFiles();

		if ($elxis->acl()->check('com_cpanel', 'settings', 'edit') == 0) {
			$url = $elxis->makeAURL('cpanel:/');
			$elxis->redirect($url, $eLang->get('NOTALLOWACCPAGE'), true);
		}

		$data = array();
		$data['templates'] = array();
		$data['atemplates'] = array();
		$data['dbtypes'] = array(
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

		$items = $this->model->getTemplates();
		if ($items) {
			foreach ($items as $item) {
				$k = $item['template'];
				if ($item['section'] == 'backend') {
					$data['atemplates'][$k] = $item['title'];
				} else {
					$data['templates'][$k] = $item['title'];
				}
			}
		}

		$data['lastcron'] = -1;
		$data['lastcronts'] = 0;
		$path = $eFiles->elxisPath('logs/lastcron.txt', true);
		if (file_exists($path)) {
			$data['lastcronts'] = filemtime($path);
			if ($data['lastcronts'] > 1406894400) { //2014-08-01 12:00:00
				$data['lastcron'] = time() - $data['lastcronts'];
			}
		}
		unset($path);

		eFactory::getPathway()->addNode($eLang->get('SETTINGS'));
		$eDoc->setTitle($eLang->get('SETTINGS').' - '.$eLang->get('ADMINISTRATION'));

		$toolbar = $elxis->obj('toolbar');
		$toolbar->add($eLang->get('SAVE'), 'save', false, '', 'elx5Submit(\'save\', \'fmconfig\', \'cfgtask\');');
		$toolbar->add($eLang->get('APPLY'), 'saveedit', false, '', 'elx5Submit(\'apply\', \'fmconfig\', \'cfgtask\');');
		$toolbar->add($eLang->get('CANCEL'), 'cancel', false, $elxis->makeAURL('cpanel:/'));

		$eDoc->addStyleLink($elxis->secureBase().'/components/com_cpanel/css/cp'.$eLang->getinfo('RTLSFX').'.css');
		$eDoc->addScriptLink($elxis->secureBase().'/components/com_cpanel/js/cpanel.js');
		$eDoc->setMetaTag('Cache-Control', 'no-cache, no-store, must-revalidate', true);
		$eDoc->setMetaTag('Pragma', 'no-cache', true);
		$eDoc->setMetaTag('Expires', '-1', true);

		$this->view->configHTML($data, $elxis, $eLang);
	}


	/*******************************/
	/* SAVE ELXIS GENERAL SETTINGS */
	/*******************************/
	public function saveconfig() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		if ($elxis->acl()->check('com_cpanel', 'settings', 'edit') == 0) {
			$url = $elxis->makeAURL('cpanel:/');
			$elxis->redirect($url, $eLang->get('NOTALLOWACCPAGE'), true);
		}

		$eSession = eFactory::getSession();
		$sess_token = trim($eSession->get('token_fmconfig'));
		$token = trim(filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		if (($token == '') || ($sess_token == '') || ($sess_token != $token)) {
			exitPage::make('403', 'CCPA-0013', 'Detected session by-pass attempt!');
		}

		$cfg = $this->catchConfigPost();
		$cfg = $this->cfgValidatePrepare($cfg);

		if (isset($cfg['errormsg'])) {
			$url = $elxis->makeAURL('cpanel:config.html');
			$elxis->redirect($url, $cfg['errormsg'], true);
		}

		ksort($cfg);

		//save translations
		$sitelangs = $eLang->getSiteLangs(false);
		$translations = array('sitename' => array(), 'metadesc' => array(), 'metakeys' => array());
		foreach ($sitelangs as $lng) {
			if ($lng == $elxis->getConfig('LANG')) { continue; }
			$idx = 'sitename_'.$lng;
			$translations['sitename'][$lng] = isset($_POST[$idx]) ? eUTF::trim(filter_input(INPUT_POST, $idx, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES | FILTER_FLAG_STRIP_LOW)) : '';
			$idx = 'metadesc_'.$lng;
			$translations['metadesc'][$lng] = isset($_POST[$idx]) ? eUTF::trim(filter_input(INPUT_POST, $idx, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES | FILTER_FLAG_STRIP_LOW)) : '';
			$idx = 'metakeys_'.$lng;
			$translations['metakeys'][$lng] = isset($_POST[$idx]) ? eUTF::trim(filter_input(INPUT_POST, $idx, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES | FILTER_FLAG_STRIP_LOW)) : '';
		}

		$eTrans = $elxis->obj('translations');
		$eTrans->saveElementTranslations('config', 'sitename', 1, $translations['sitename']);
		$eTrans->saveElementTranslations('config', 'metadesc', 1, $translations['metadesc']);
		$eTrans->saveElementTranslations('config', 'metakeys', 1, $translations['metakeys']);
		unset($eTrans);

		$eSession->set('token_fmconfig');

		$out = '<?php '._LEND;
		$out .= '/**'._LEND;
		$out .= 'Elxis CMS - Copyright 2006-'.date('Y').' elxis.org. All rights reserved.'._LEND;
		$out .= 'Last saved on '.gmdate('Y-m-d H:i:s').' (UTC) by '.$elxis->user()->uname._LEND;
		$out .= '******************************************/'._LEND._LEND;
		$out .= 'defined(\'_ELXIS_\') or die (\'Direct access to this location is not allowed\');'._LEND._LEND._LEND;
		$out .= 'class elxisConfig {'._LEND._LEND;
		foreach ($cfg as $key => $val) {
			if (is_int($val)) {
				$out .= "\t".'private $'.strtoupper($key).' = '.$val.';'._LEND;
			} else {
				$out .= "\t".'private $'.strtoupper($key).' = \''.$val.'\';'._LEND;
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

		$eFiles = eFactory::getFiles();
		
		$configfile = 'configuration.php';
		if (defined('ELXIS_MULTISITE')) { $configfile = 'config'.ELXIS_MULTISITE.'.php'; }
		$ok = $eFiles->createFile($configfile, $out);

		$task = isset($_POST['task']) ? trim($_POST['task']) : 'save';
		$tabopen = (isset($_POST['tabopen'])) ? (int)$_POST['tabopen'] : 0;
		if ($task == 'apply') {
			$rnd = rand(1000, 999999);
			$url = $elxis->makeAURL('cpanel:config.html').'?rnd='.$rnd;
			if ($tabopen > 0) { $url .= '&tabopen='.$tabopen; }
		} else {
			$url = $elxis->makeAURL('cpanel:/');
		}

		if (!$ok) {
			$msg = $eFiles->getError();
			$elxis->redirect($url, $msg, true);
		} else {
			if (function_exists('opcache_get_status')) {//solve problem with config vars not immediately updated due to opcache
				$opc = opcache_get_status();
				if (is_array($opc)) {
					if ($opc['opcache_enabled'] == 1) { opcache_reset(); }
				}
			}
			$elxis->redirect($url, $eLang->get('SETS_SAVED_SUCC'));
		}
	}


	/*******************/
	/* CATCH HTTP POST */
	/*******************/
	private function catchConfigPost() {
		if (!isset($_POST['sitename'])) { return null; }

		$emails = array('mail_email', 'mail_from_email', 'mail_manager_email');
		$strings = array(
			'repo_path', 'default_route', 'template', 'atemplate', 'lang', 'timezone', 'registration_email_domain', 'registration_exclude_email_domains', 
			'mail_method', 'mail_smtp_host', 'mail_smtp_user', 'mail_smtp_pass', 'mail_smtp_secure', 'mail_auth_method', 'db_type', 'db_host', 'db_name', 'db_prefix', 
			'db_user', 'db_pass', 'db_dsn', 'db_scheme', 'ftp_host', 'ftp_user', 'ftp_pass', 'ftp_root', 
			'session_handler', 'encrypt_method', 'encrypt_key', 'xfoptions', 'seo_match', 'captcha', 'jquery', 'defender_whitelist'
		);

		$ustrings = array('sitename', 'offline_message', 'metadesc', 'metakeys', 'mail_name', 'mail_from_name', 'mail_manager_name');

		$integers = array(
			'online', 'sef', 'statistics', 'gzip', 'multilinguism', 'lang_detect', 'cache', 'cache_time', 'realname', 'registration', 'registration_activation', 
			'pass_recover', 'mail_smtp_port', 'mail_smtp_auth', 'db_port', 'db_persistent', 'ftp', 'ftp_port', 'session_lifetime', 'session_matchip', 
			'session_matchbrowser', 'session_matchreferer', 'session_encrypt', 'security_level', 'ssl', 'error_report', 'error_log', 'error_alert', 'log_rotate', 
			'debug', 'minicss', 'minijs', 'cronjobs', 'cronjobs_prob', 'defender_notify', 'defender_ipafter', 'defender_log'
		);

		$cfg = array();
		$cfg['url'] = trim(filter_input(INPUT_POST, 'url', FILTER_SANITIZE_URL));
		if (version_compare(phpversion(), '7.3.0', '>=')) {
			$cfg['csp'] = trim(filter_input(INPUT_POST, 'csp', FILTER_SANITIZE_ADD_SLASHES, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		} else {
			$cfg['csp'] = trim(filter_input(INPUT_POST, 'csp', FILTER_SANITIZE_MAGIC_QUOTES, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		}

		foreach ($emails as $str) {
			$cfg[$str] = trim(filter_input(INPUT_POST, $str, FILTER_SANITIZE_EMAIL));
		}

		foreach ($strings as $str) {
			$cfg[$str] = trim(filter_input(INPUT_POST, $str, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
			$cfg[$str] = addslashes($cfg[$str]);
		}
		foreach ($ustrings as $str) {
			$cfg[$str] = eUTF::trim(filter_input(INPUT_POST, $str, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES | FILTER_FLAG_STRIP_LOW));
			$cfg[$str] = addslashes($cfg[$str]);
		}
		foreach ($integers as $intg) {
			$cfg[$intg] = isset($_POST[$intg]) ? (int)$_POST[$intg] : 0;
		}

		$cfg['defender'] = array();
		if (isset($_POST['defender'])) {
			if (trim($_POST['defender']) != '') {
				$arr = explode(',', $_POST['defender']);
				foreach ($arr as $v) {
					$cfg['defender'][] = trim(filter_var($v, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
				}
			}
		}

		$cfg['sitelangs'] = '';
		if (isset($_POST['sitelangs'])) {
			if (trim($_POST['sitelangs']) != '') {
				$arr = explode(',', $_POST['sitelangs']);
				if (!in_array($cfg['lang'], $arr)) { $arr[] = $cfg['lang']; }
				$cfg['sitelangs'] = implode(',', $arr);
			}
		}

		if ($cfg['xfoptions'] == 'ALLOW-FROM') {
			if (isset($_POST['xfoptionsfrom'])) {
				$cfg['xfoptions'] .= ' '.trim(filter_input(INPUT_POST, 'xfoptionsfrom', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES | FILTER_FLAG_STRIP_LOW));
			}
		}

		return $cfg;
	}


	/****************************************************/
	/* VALIDATE ELXIS CONFIGURATION AND PREPARE TO SAVE */
	/****************************************************/
	private function cfgValidatePrepare($cfg) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();

		if ($cfg['online'] < 0) { 
			$cfg['errormsg'] = $eLang->get('WEBSITE_STATUS').' '.$eLang->get('INVALID_NUMBER'); return $cfg;
		}
		if ($cfg['sitename'] == '') {
			$cfg['errormsg'] = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('SITENAME')); return $cfg;
		}
		if ($cfg['metadesc'] == '') {
			$cfg['errormsg'] = sprintf($eLang->get('FIELDNOEMPTY'), 'META '.$eLang->get('DESCRIPTION')); return $cfg;
		}
		if ($cfg['metakeys'] == '') {
			$cfg['errormsg'] = sprintf($eLang->get('FIELDNOEMPTY'), 'META '.$eLang->get('KEYWORDS')); return $cfg;
		}

		if ($cfg['template'] == '') {
			$cfg['errormsg'] = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('SITE_TEMPLATE')); return $cfg;
		}
		if (!file_exists(ELXIS_PATH.'/templates/'.$cfg['template'].'/index.php')) {
			$cfg['errormsg'] = $cfg['template'].' is not a valid Elxis template!'; return $cfg;
		}

		if ($cfg['atemplate'] == '') {
			$cfg['errormsg'] = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('ADMIN_TEMPLATE')); return $cfg;
		}
		if (!file_exists(ELXIS_PATH.'/templates/admin/'.$cfg['atemplate'].'/index.php')) {
			$cfg['errormsg'] = $cfg['atemplate'].' is not a valid Elxis administration template!'; return $cfg;
		}

		if (($cfg['url'] == '') || !filter_var($cfg['url'], FILTER_VALIDATE_URL)) {
			$cfg['errormsg'] = $eLang->get('INVALID_URL'); return $cfg;
		}
		
		$cfg['repo_path'] = rtrim(str_replace('\\', '/', $cfg['repo_path']), '/');
		if ($cfg['repo_path'] != '') {
			if (!file_exists($cfg['repo_path'].'/')) { $cfg['errormsg'] = 'Repository path does not exist!'; return $cfg; }
		}

		$cfg['ftp_root'] = '/'.trim(str_replace('\\', '/', $cfg['ftp_root']), '/');
		if ($cfg['ftp_port'] < 1) { $cfg['ftp_port'] = 21; }
		if ($cfg['ftp_host'] == '') { $cfg['ftp_host'] = 'localhost'; }
		if ($cfg['ftp_pass'] == '') { $cfg['ftp_pass'] = $elxis->getConfig('FTP_PASS'); }
		if ($cfg['ftp'] == 1) {
			if ($cfg['ftp_user'] == '') {
				$cfg['errormsg'] = sprintf($eLang->get('FIELDNOEMPTY'), 'FTP '.$eLang->get('USERNAME')); return $cfg;
			}
			if ($cfg['ftp_pass'] == '') {
				$cfg['errormsg'] = sprintf($eLang->get('FIELDNOEMPTY'), 'FTP '.$eLang->get('PASSWORD')); return $cfg;
			}
		}

		if ($cfg['db_type'] == '') {
			$cfg['errormsg'] = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('DB_TYPE')); return $cfg;
		}
		if ($cfg['db_prefix'] == '') {
			$cfg['errormsg'] = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('TABLES_PREFIX')); return $cfg;
		}
		if ($cfg['db_host'] == '') { $cfg['db_host'] = 'localhost'; }
		if ($cfg['db_port'] < 0) { $cfg['db_port'] = 0; }
		if ($cfg['db_pass'] == '') { $cfg['db_pass'] = $elxis->getConfig('DB_PASS'); }
		$cfg['db_scheme'] = str_replace('\\', '/', $cfg['db_scheme']);

		if ($cfg['mail_method'] == 'gmail') { $cfg['mail_method'] = 'smtp'; }
		if (($cfg['mail_method'] == '') || !in_array($cfg['mail_method'], array('mail', 'smtp', 'sendmail'))) {
			$cfg['errormsg'] = 'Invalid mail dispatch method!'; return $cfg;
		}
		if ($cfg['mail_name'] == '') {
			$cfg['errormsg'] = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('RCPT_NAME')); return $cfg;
		}
		if (($cfg['mail_email'] == '') || !filter_var($cfg['mail_email'], FILTER_VALIDATE_EMAIL)) {
			$cfg['errormsg'] = sprintf($eLang->get('FIELDNOEMPTY'), 'Recipient Email'); return $cfg;
		}
		if ($cfg['mail_from_name'] == '') {
			$cfg['errormsg'] = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('SENDER_NAME')); return $cfg;
		}
		if (($cfg['mail_from_email'] == '') || !filter_var($cfg['mail_from_email'], FILTER_VALIDATE_EMAIL)) {
			$cfg['errormsg'] = sprintf($eLang->get('FIELDNOEMPTY'), 'Sender Email'); return $cfg;
		}
		if ($cfg['mail_manager_name'] == '') {
			$cfg['errormsg'] = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('TECHNICAL_MANAGER')); return $cfg;
		}
		if (($cfg['mail_manager_email'] == '') || !filter_var($cfg['mail_manager_email'], FILTER_VALIDATE_EMAIL)) {
			$cfg['errormsg'] = sprintf($eLang->get('FIELDNOEMPTY'), 'Technical Manager Email'); return $cfg;
		}
		if ($cfg['mail_smtp_port'] < 1) { $cfg['mail_smtp_port'] = 25; }
		if ($cfg['mail_smtp_host'] == '') { $cfg['mail_smtp_host'] = 'localhost'; }
		if (!in_array($cfg['mail_smtp_secure'], array('ssl', 'tls', 'starttls'))) { $cfg['mail_smtp_secure'] = ''; }
		if ($cfg['mail_smtp_pass'] == '') { $cfg['mail_smtp_pass'] = $elxis->getConfig('MAIL_SMTP_PASS'); }
		if ($cfg['mail_method'] == 'smtp') {
			if ($cfg['mail_smtp_user'] == '') {
				$cfg['errormsg'] = sprintf($eLang->get('FIELDNOEMPTY'), 'SMTP '.$eLang->get('USERNAME')); return $cfg;
			}
			if ($cfg['mail_smtp_pass'] == '') {
				$cfg['errormsg'] = sprintf($eLang->get('FIELDNOEMPTY'), 'SMTP '.$eLang->get('PASSWORD')); return $cfg;
			}
		}

		if ($cfg['session_lifetime'] < 1) { $cfg['session_lifetime'] = 900; }
		if (!in_array($cfg['session_handler'], array('none', 'files', 'database'))) { $cfg['session_handler'] = 'database'; }

		if ($cfg['timezone'] == '') { $cfg['timezone'] = 'UTC'; }
		if ($cfg['cache_time'] < 600) { $cfg['cache_time'] = 1800; }
		if ($cfg['registration_email_domain'] != '') {
			if (!preg_match("/^[a-z0-9][a-z0-9\-]+[a-z0-9](\.[a-z]{2,4})+$/i", $cfg['registration_email_domain'])) {
				$cfg['errormsg'] = sprintf($eLang->get('FIELDNOACCCHAR'), $eLang->get('ALLOWED_DOMAIN')); return $cfg;
			}
		}

		if ($cfg['registration_exclude_email_domains'] != '') {
			$domains = explode(',',$cfg['registration_exclude_email_domains']);
			$cfg['registration_exclude_email_domains'] = '';
			if ($domains) {
				$newdoms = array();
				foreach ($domains as $domain) {
					$domain = trim($domain);
					if ($domain == '') { continue; }
					if (!preg_match("/^[a-z0-9][a-z0-9\-]+[a-z0-9](\.[a-z]{2,4})+$/i", $domain)) {
						$cfg['errormsg'] = 'Invalid excluded domain name!'; return $cfg;
					}
					$newdoms[] = $domain;
				}
				$cfg['registration_exclude_email_domains'] = implode(',',$newdoms);
				unset($newdoms);
			}
			unset($domains);
		}

		$cfg['encrypt_method'] = $elxis->getConfig('ENCRYPT_METHOD');
		$cfg['encrypt_key'] = $elxis->getConfig('ENCRYPT_KEY');

		$signatures = $cfg['defender'];
		$cfg['defender'] = '';
		if (count($signatures) > 0) {
			$final = array();
			foreach ($signatures as $signature) {
				$signature = trim($signature);
				if ($signature == '') { continue; }
				if (!in_array($signature, array('G', 'I', 'R', 'C', 'F'))) {
					$cfg['errormsg'] = 'Invalid option '.$signature.' for Elxis Defender!'; return $cfg;
				}
				$cfg['defender'] .= $signature;
				if ($signature == 'F') {
					$rp = ($cfg['repo_path'] != '') ? $cfg['repo_path'] : ELXIS_PATH.'/repository';
					if (file_exists($rp.'/other/elxis_hashes_'.md5($elxis->getConfig('ENCRYPT_KEY')).'.php')) {
						@unlink($rp.'/other/elxis_hashes_'.md5($elxis->getConfig('ENCRYPT_KEY')).'.php');
					}
					unset($rp);
				}
			}
		}
		unset($signatures);

		if ($cfg['defender_whitelist'] != '') {
			$parts = explode(',', $cfg['defender_whitelist']);
			$ips = array();
			foreach ($parts as $part) {
				$ip = trim($part);
				if ($ip == '') { continue; }
				if (filter_var($ip, FILTER_VALIDATE_IP)) { $ips[] = $ip; }
			}
			$cfg['defender_whitelist'] = $ips ? implode(',', $ips) : '';
		}

		if ($cfg['lang'] == '') {
			$cfg['errormsg'] = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('LANGUAGE')); return $cfg;
		}
		if (!file_exists(ELXIS_PATH.'/language/'.$cfg['lang'].'/'.$cfg['lang'].'.php')) {
			$cfg['errormsg'] = 'Language '.$cfg['lang'].' does not exist!'; return $cfg;
		}

		if (trim($cfg['xfoptions']) == 'ALLOW-FROM') {
			$cfg['errormsg'] = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('ALLOW_FROM_ORIGIN'));
			return $cfg;
		}

		if (($cfg['default_route'] == '') || ($cfg['default_route'] == '/') || ($cfg['default_route'] == 'content')) { $cfg['default_route'] = 'content:/'; }
		if ($cfg['captcha'] == '') { $cfg['captcha'] = 'NOROBOT'; }
		if ($cfg['jquery'] == '') { $cfg['jquery'] = '3m'; }

		return $cfg;
	}

}

?>