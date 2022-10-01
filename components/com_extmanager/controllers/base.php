<?php 
/**
* @version		$Id: base.php 2435 2022-01-19 17:47:51Z IOS $
* @package		Elxis
* @subpackage	Component Extensions Manager
* @copyright	Copyright (c) 2006-2022 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class extmanagerController {

	protected $view = null;
	protected $model = null;


	protected function __construct($view=null, $task='', $model=null) {
		$this->view = $view;
		$this->model = $model;
	}


	/******************************/
	/* PREPARE TO LIST EXTENSIONS */
	/******************************/
	protected function listExtensions($type) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$pathway = eFactory::getPathway();
		$eDoc = eFactory::getDocument();

		$options = array('limit' => 20, 'page' => 1, 'maxpage' => 1, 'sn' => 'title', 'so' => 'asc', 'limitstart' => 0, 'total' => 0, 'key' => '');

		if ($type == 'engines') {
			$extdata = array(
				'id' => 'id',
				'type' => 'engines',
				'pgtitle' => $eLang->get('SEARCH_ENGINES'),
				'countfunc' => 'countEngines',
				'getfunc' => 'getEngines',
				'columns' => array(
					array('title' => $eLang->get('TITLE'), 'name' => 'title', 'sortable' => true, 'class' => ''),
					array('title' => $eLang->get('SEARCH_ENGINE'), 'name' => 'engine', 'sortable' => true, 'class' => 'elx5_lmobhide'),
					array('title' => $eLang->get('PUBLISHED'), 'name' => 'published', 'sortable' => true, 'class' => 'elx5_center'),
					array('title' => $eLang->get('ORDERING'), 'name' => 'ordering', 'sortable' => true, 'class' => 'elx5_center elx5_smallscreenhide'),
					array('title' => $eLang->get('DEFAULT'), 'name' => 'defengine', 'sortable' => false, 'class' => 'elx5_center elx5_tabhide'),
					array('title' => $eLang->get('ACCESS'), 'name' => 'alevel', 'sortable' => false, 'class' => 'elx5_midscreenhide'),
					array('title' => $eLang->get('VERSION'), 'name' => 'version', 'sortable' => false, 'class' => 'elx5_center elx5_smallscreenhide'),
					array('title' => $eLang->get('DATE'), 'name' => 'created', 'sortable' => false, 'class' => 'elx5_midscreenhide')
				)
			);
			$options['sn'] = 'ordering';
		} else if ($type == 'auth') {
			$extdata = array(
				'id' => 'id',
				'type' => 'auth',
				'pgtitle' => $eLang->get('AUTH_METHODS'),
				'countfunc' => 'countAuthMethods',
				'getfunc' => 'getAuthMethods',
				'columns' => array(
					array('title' => $eLang->get('TITLE'), 'name' => 'title', 'sortable' => true, 'class' => ''),
					array('title' => $eLang->get('AUTH_METHOD'), 'name' => 'auth', 'sortable' => true, 'class' => 'elx5_tabhide'),
					array('title' => $eLang->get('PUBLISHED'), 'name' => 'published', 'sortable' => true, 'class' => 'elx5_center'),
					array('title' => $eLang->get('ORDERING'), 'name' => 'ordering', 'sortable' => true, 'class' => 'elx5_center elx5_smallscreenhide'),
					array('title' => $eLang->get('VERSION'), 'name' => 'version', 'sortable' => false, 'class' => 'elx5_center elx5_smallscreenhide'),
					array('title' => $eLang->get('DATE'), 'name' => 'created', 'sortable' => false, 'class' => 'elx5_midscreenhide')
				)
			);
			$options['sn'] = 'ordering';
		} else if ($type == 'plugins') {
			$extdata = array(
				'id' => 'id',
				'type' => 'plugins',
				'pgtitle' => $eLang->get('CONTENT_PLUGINS'),
				'countfunc' => 'countPlugins',
				'getfunc' => 'getPlugins',
				'columns' => array(
					array('title' => $eLang->get('TITLE'), 'name' => 'title', 'sortable' => true, 'class' => ''),
					array('title' => $eLang->get('PLUGIN'), 'name' => 'plugin', 'sortable' => true, 'class' => 'elx5_tabhide'),
					array('title' => $eLang->get('PUBLISHED'), 'name' => 'published', 'sortable' => true, 'class' => 'elx5_center'),
					array('title' => $eLang->get('ORDERING'), 'name' => 'ordering', 'sortable' => true, 'class' => 'elx5_center elx5_smallscreenhide'),
					array('title' => $eLang->get('ACCESS'), 'name' => 'alevel', 'sortable' => false, 'class' => 'elx5_midscreenhide'),
					array('title' => $eLang->get('VERSION'), 'name' => 'version', 'sortable' => false, 'class' => 'elx5_center elx5_smallscreenhide'),
					array('title' => $eLang->get('DATE'), 'name' => 'created', 'sortable' => false, 'class' => 'elx5_midscreenhide')
				)
			);
			$options['sn'] = 'ordering';
		} else if ($type == 'components') {
			$extdata = array(
				'id' => 'id',
				'type' => 'components',
				'pgtitle' => $eLang->get('COMPONENTS'),
				'countfunc' => 'countComponents',
				'getfunc' => 'getComponents',
				'columns' => array(
					array('title' => $eLang->get('TITLE'), 'name' => 'name', 'sortable' => true, 'class' => ''),
					array('title' => $eLang->get('COMPONENT'), 'name' => 'component', 'sortable' => true, 'class' => 'elx5_mobhide'),
					array('title' => $eLang->get('VERSION'), 'name' => 'version', 'sortable' => false, 'class' => 'elx5_center elx5_tabhide'),
					array('title' => $eLang->get('DATE'), 'name' => 'created', 'sortable' => false, 'class' => 'elx5_midscreenhide'),
					array('title' => $eLang->get('AUTHOR'), 'name' => 'author', 'sortable' => false, 'class' => 'elx5_smallscreenhide'),
					array('title' => $eLang->get('ROUTE'), 'name' => 'route', 'sortable' => false, 'class' => 'elx5_midscreenhide')
				)
			);
			$options['sn'] = 'name';
		} else if ($type == 'templates') {
			$extdata = array(
				'id' => 'id',
				'type' => 'templates',
				'pgtitle' => $eLang->get('TEMPLATES'),
				'countfunc' => 'countTemplates',
				'getfunc' => 'getTemplates',
				'columns' => array(
					array('title' => $eLang->get('TITLE'), 'name' => 'title', 'sortable' => true, 'class' => ''),
					array('title' => $eLang->get('TEMPLATE'), 'name' => 'template', 'sortable' => true, 'class' => 'elx5_tabhide'),
					array('title' => $eLang->get('VERSION'), 'name' => 'version', 'sortable' => false, 'class' => 'elx5_center elx5_lmobhide'),
					array('title' => $eLang->get('DATE'), 'name' => 'created', 'sortable' => false, 'class' => 'elx5_midscreenhide'),
					array('title' => $eLang->get('DEFAULT'), 'name' => 'deftpl', 'sortable' => false, 'class' => 'elx5_center'),
					array('title' => $eLang->get('AUTHOR'), 'name' => 'author', 'sortable' => false, 'class' => 'elx5_midscreenhide'),
					array('title' => $eLang->get('SECTION'), 'name' => 'section', 'sortable' => true, 'class' => 'elx5_smallscreenhide')
				)
			);
			$options['sn'] = 'title';
			if (isset($_POST['section'])) {
				$options['section'] = trim($_POST['section']);
			} else if (isset($_GET['section'])) {
				$options['section'] = trim($_GET['section']);
			} else {
				$options['section'] = 'frontend';
			}
			if ($options['section'] != 'backend') { $options['section'] = 'frontend'; }
		} else if ($type == 'modules') {
			$extdata = array(
				'id' => 'id',
				'type' => 'modules',
				'pgtitle' => $eLang->get('MODULES'),
				'countfunc' => 'countModules',
				'getfunc' => 'getModules',
				'columns' => array(
					array('title' => $eLang->get('TITLE'), 'name' => 'title', 'sortable' => true, 'class' => ''),
					array('title' => $eLang->get('MODULE'), 'name' => 'module', 'sortable' => true, 'class' => 'elx5_smallscreenhide'),
					array('title' => $eLang->get('VERSION'), 'name' => 'version', 'sortable' => false, 'class' => 'elx5_center elx5_smallscreenhide'),
					array('title' => $eLang->get('PUBLISHED'), 'name' => 'published', 'sortable' => true, 'class' => 'elx5_center'),
					array('title' => $eLang->get('POSITION'), 'name' => 'position', 'sortable' => true, 'class' => 'elx5_lmobhide'),
					array('title' => $eLang->get('ORDERING'), 'name' => 'ordering', 'sortable' => true, 'class' => 'elx5_center elx5_smallscreenhide'),
					array('title' => $eLang->get('ACCESS'), 'name' => 'modaccess', 'sortable' => false, 'class' => 'elx5_midscreenhide'),
					array('title' => $eLang->get('SECTION'), 'name' => 'section', 'sortable' => false, 'class' => 'elx5_midscreenhide')
				)
			);
			$extdata['allgroups'] = $this->model->getGroups();

			$options['sn'] = 'position';
			if (isset($_POST['section'])) {
				$options['section'] = trim($_POST['section']);
			} else if (isset($_GET['section'])) {
				$options['section'] = trim($_GET['section']);
			} else {
				$options['section'] = 'frontend';
			}
			if ($options['section'] != 'backend') { $options['section'] = 'frontend'; }
			$pat = "#([\']|[\"]|[\$]|[\#]|[\<]|[\>]|[\*]|[\%]|[\~]|[\`]|[\^]|[\|]|[\}]|[\\\])#u";
			$options['position'] = filter_input(INPUT_GET, 'position', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW || FILTER_FLAG_STRIP_HIGH);
			$options['position'] = eUTF::trim(preg_replace($pat, '', $options['position']));
		} else {
			$link = $elxis->makeAURL('extmanager:/');
			$elxis->redirect($link, 'Invalid extension type!', true);
		}

		$sortcols = array();
		foreach ($extdata['columns'] as $col) {
			if ($col['sortable']) { $sortcols[] = $col['name']; }
		}

		if (isset($_GET['key'])) {
			$options['key'] = trim(filter_input(INPUT_GET, 'key', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
			if (eUTF::strlen($options['key']) < 3) { $options['key'] = ''; }
		}

		$options['limit'] = (isset($_GET['limit'])) ? (int)$_GET['limit'] : 20;
		if ($options['limit'] < 1) { $options['limit'] = 20; }
		$elxis->updateCookie('limit', $options['limit']);
		$options['page'] = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
		if ($options['page'] < 1) { $options['page'] = 1; }
		if (isset($_GET['sn'])) {
			$sn = trim($_GET['sn']);
			if ($sn != '') { if (in_array($sn, $sortcols)) { $options['sn'] = $sn; } }
		}
		$options['so'] = (isset($_GET['so'])) ? trim($_GET['so']) : 'asc';
		if ($options['so'] != 'desc') { $options['so'] = 'asc'; }

		$func = $extdata['countfunc'];
		$options['total'] = $this->model->$func($options);

		$rows = array();
		$modsacl = array();
		$options['maxpage'] = ceil($options['total']/$options['limit']);
		if ($options['maxpage'] < 1) { $options['maxpage'] = 1; }
		if ($options['page'] > $options['maxpage']) { $options['page'] = $options['maxpage']; }
		$options['limitstart'] = (($options['page'] - 1) * $options['limit']);
		if ($options['total'] > 0) {
			$func = $extdata['getfunc'];
			$rows = $this->model->$func($options);
			if ($rows) {
				if ($extdata['type'] == 'modules') {
					$modids = array();
					foreach ($rows as $row) { $modids[] = $row->id; }
					$modsacl = $this->model->getModulesViewACL($modids);
				}
				elxisLoader::loadFile('components/com_extmanager/includes/extension.xml.php');
				$exml = new extensionXML();
				foreach ($rows as $k => $row) {
					$rows[$k]->version = 0;
					$rows[$k]->created = '';
					$rows[$k]->author = '';
					$rows[$k]->authorurl = '';

					$info = false;
					if ($extdata['type'] == 'engines') {
						$info = $exml->quickXML('engine', $row->engine);
					} else if ($extdata['type'] == 'auth') {
						$info = $exml->quickXML('auth', $row->auth);
					} else if ($extdata['type'] == 'plugins') {
						$info = $exml->quickXML('plugin', $row->plugin);
					} else if ($extdata['type'] == 'modules') {
						$info = $exml->quickXML('module', $row->module);
					} else if ($extdata['type'] == 'components') {
						$cname = preg_replace('#^(com\_)#', '', $row->component);
						$info = $exml->quickXML('component', $cname);
					} else if ($extdata['type'] == 'templates') {
						$exttype = ($options['section'] == 'backend') ? 'atemplate' : 'template';
						$info = $exml->quickXML($exttype, $row->template);
						$cur_template = ($options['section'] == 'backend') ? $elxis->getConfig('ATEMPLATE') : $elxis->getConfig('TEMPLATE');
						$rows[$k]->deftpl = ($row->template == $cur_template) ? 1 : 0;
					}
					if ($info) {
						$rows[$k]->version = $info['version'];
						$rows[$k]->created = $info['created'];
						$rows[$k]->author = $info['author'];
						$rows[$k]->authorurl = $info['authorurl'];
					}
					unset($info);
				}
				unset($exml);
			}
		}

		$warnmsg = '';
		if (($extdata['type'] == 'modules') && ($options['page'] == 1)) {
			if ($elxis->getConfig('CRONJOBS') == 0) {
				$scheduled = $this->model->countScheduledItems();
				if ($scheduled > 0) {
					$warnmsg = sprintf($eLang->get('SCHEDULED_CRON_DIS'), $scheduled);
					if ($elxis->acl()->check('com_cpanel', 'settings', 'edit') > 0) {
						$link = $elxis->makeAURL('cpanel:config.html');
						$warnmsg .= ' <a href="'.$link.'">'.$eLang->get('SETTINGS').'</a>';
					}
				}
			}
		}

		$pathway->addNode($eLang->get('EXTENSIONS'), 'extmanager:/');
		$pathway->addNode($extdata['pgtitle'], 'extmanager:'.$extdata['type'].'/');
		$eDoc->setTitle($extdata['pgtitle']);
		$eDoc->addFontAwesome();
		$eDoc->addScriptLink($elxis->secureBase().'/components/com_extmanager/js/extmanager.js');
		if ($rows) {
			$eDoc->addNativeDocReady('elx5DataTable(\'extensionstbl\', false);');
		}

		$this->view->listExtensionsHTML($extdata, $options, $rows, $modsacl, $warnmsg, $elxis, $eLang);
	}


	/*************************************/
	/* TOGGLE EXTENSION'S PUBLISH STATUS */
	/*************************************/
	protected function toggleExtension($type) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$response = array('success' => 0, 'published' => -1, 'icontitle' => '', 'reloadpage' => 0);

		if (($type == '') || ($elxis->acl()->check('com_extmanager', $type, 'edit') < 1)) {
			$response['icontitle'] = $eLang->get('NOTALLOWACTION');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$id = isset($_POST['elid']) ? (int)$_POST['elid'] : 0;
		if ($id < 1) {
			$response['icontitle'] = 'No item requested!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		if ($type == 'engines') {
			$results = $this->model->publishEngine($id, -1);
		} else if ($type == 'modules') {
			$results = $this->model->publishModule($id, -1);
		} else if ($type == 'auth') {
			$results = $this->model->publishAuth($id, -1);
		} else if ($type == 'plugins') {
			$results = $this->model->publishPlugin($id, -1);
		} else {
			$results = array('success' => false, 'message' => 'Not supported extension type!', 'newpublished' => -1);
		}

		if ($results['success'] === false) {
			$response['icontitle'] = $results['message'];
		} else {
			$response['success'] = 1;
			$response['published'] = $results['newpublished'];
			if ($results['newpublished'] == 1) {
				$response['icontitle'] = $eLang->get('PUBLISHED').' - '.$eLang->get('CLICK_TOGGLE_STATUS');
			} else {
				$response['icontitle'] = $eLang->get('UNPUBLISHED').' - '.$eLang->get('CLICK_TOGGLE_STATUS');
			}
		}

		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/*************************************/
	/* TOGGLE EXTENSION'S DEFAULT STATUS */
	/*************************************/
	protected function toggleExtensionDefault($type) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$response = array('success' => 0, 'published' => -1, 'icontitle' => '', 'reloadpage' => 0);

		if (($type == '') || ($elxis->acl()->check('com_extmanager', $type, 'edit') < 1)) {
			$response['icontitle'] = $eLang->get('NOTALLOWACTION');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$id = isset($_POST['elid']) ? (int)$_POST['elid'] : 0;
		if ($id < 1) {
			$response['icontitle'] = 'No item requested!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		if ($type == 'engines') {
			$results = $this->model->setDefaultEngine($id);
		} else {
			$results = array('success' => false, 'message' => 'Not supported extension type!');
		}

		if ($results['success'] === false) {
			$response['icontitle'] = $results['message'];
		} else {
			$response['success'] = 1;
			$response['published'] = 1;
			$response['icontitle'] = $eLang->get('DEFAULT').' - '.$eLang->get('CLICK_TOGGLE_STATUS');
			$response['reloadpage'] = 1;
		}

		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/****************************/
	/* SET EXTENSION'S ORDERING */
	/****************************/
	protected function setExtensionOrdering($type) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$response = array('success' => 0, 'message' => '');

		if (($type == '') || ($elxis->acl()->check('com_extmanager', $type, 'edit') < 1)) {
			$response['message'] = $eLang->get('NOTALLOWACTION');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$ordering = isset($_POST['ordering']) ? (int)$_POST['ordering'] : 0;
		$id = isset($_POST['elid']) ? (int)$_POST['elid'] : 0;
		if ($id < 1) {
			$response['message'] = 'No item requested!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}
		if (($ordering < 1) || ($ordering > 99999999)) {
			$response['message'] = 'Invalid value for ordering!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		if ($type == 'engines') {
			$row = new enginesDbTable();
		} else if ($type == 'auth') {
			$row = new authenticationDbTable();
		} else if ($type == 'plugins') {
			$row = new pluginsDbTable();
		} else if ($type == 'modules') {
			$row = new modulesDbTable();
		} else {
			$response['message'] = 'Invalid/not supported extension type '.$type.'!';
		}

		if ($response['message'] == '') {
			if (!$row->load($id)) {
				$response['message'] = 'Item not found!';
			}
		}
		if ($response['message'] == '') {
			if ($type == 'modules') {
				if ($elxis->acl()->check('module', $row->module, 'manage', $id) < 1) {
					$response['message'] = $eLang->get('NOTALLOWMANITEM');
					$this->ajaxHeaders('application/json');
					echo json_encode($response);
					exit;
				}
			}
			$row->ordering = $ordering;
			$ok = $row->store();
			if (!$ok) {
				$response['message'] = $row->getErrorMsg();
			} else {
				$response['success'] = 1;
			}
		}

		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/******************************/
	/* DELETE/UNINSTALL EXTENSION */
	/******************************/
	protected function deleteExtension($type) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$response = array('success' => 0, 'message' => '');

		if (($type == '') || ($elxis->acl()->check('com_extmanager', $type, 'install') < 1)) {
			$response['message'] = $eLang->get('NOTALLOWACTION');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}
		if (($type == 'engines') || ($type == 'auth') || ($type == 'plugins') || ($type == 'components') || ($type == 'templates')) {
			if (defined('ELXIS_MULTISITE') && (ELXIS_MULTISITE != 1)) {
				$response['message'] = $eLang->get('UNINST_EXT_MOTHERSITE');
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit;
			}
		}

		$id = (isset($_POST['elids'])) ? (int)$_POST['elids'] : 0;
		if ($id < 1) {
			$response['message'] = 'Invalid extension!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		if ($type == 'engines') {
			$row = new enginesDbTable();
			$xmltype = 'engine';
			$dbcol = 'engine';
		} else if ($type == 'auth') {
			$row = new authenticationDbTable();
			$xmltype = 'auth';
			$dbcol = 'auth';
		} else if ($type == 'plugins') {
			$row = new pluginsDbTable();
			$xmltype = 'plugin';
			$dbcol = 'plugin';
		} else if ($type == 'modules') {
			$row = new modulesDbTable();
			$xmltype = 'module';
			$dbcol = 'module';
		} else if ($type == 'components') {
			$row = new componentsDbTable();
			$xmltype = 'component';
			$dbcol = 'component';
		} else if ($type == 'templates') {
			$row = new templatesDbTable();
			$xmltype = 'template';
			$dbcol = 'template';
		} else {
			$response['message'] = 'Invalid/not supported extension type '.$type.'!';
		}

		if ($response['message'] == '') {
			if (!$row->load($id)) {
				$response['message'] = 'Extension not found!';
			}
		}

		if ($response['message'] != '') {
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$instances = 1;
		$uninstall = true;
		if ($type == 'modules') {
			if ($elxis->acl()->check('module', $row->module, 'manage', $id) < 1) {
				$response['message'] = $eLang->get('NOTALLOWMANITEM');
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit;
			}
			$instances = $this->model->countModuleInstances($row->module);
			if ($instances < 1) {
				$response['message'] = 'Module not found!';
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit;
			}
			$uninstall = false;
			if ($instances == 1) { $uninstall = true; }
			if ($row->module == 'mod_content') { $uninstall = false; }
			if (defined('ELXIS_MULTISITE') && (ELXIS_MULTISITE != 1)) { $uninstall = false; }
		}

		if ($type == 'components') {
			if ($elxis->acl()->check('component', $row->component, 'manage') < 1) {
				$response['message'] = $eLang->get('NOTALLOWMANITEM');
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit;
			}
		}


		if (($uninstall == true) && ($elxis->getConfig('SECURITY_LEVEL') > 0)) {
			$response['message'] = $eLang->get('UNINST_NALLOW_SECLEVEL');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		if (($row->iscore == 1) && ($uninstall == true)) {
			$response['message'] = $eLang->get('CNOT_UNINST_CORE_EXTS');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		if ($type == 'modules') {
			if (!$uninstall) {
				$ok = $this->model->deleteModule($row->id, $row->module);
				$this->ajaxHeaders('text/plain');
				if (!$ok) {
					$response['message'] = $eLang->get('ACTION_FAILED');
				} else {
					$response['success'] = 1;
				}
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit;
			}
		}

		if ($type == 'engines') {
			if ($row->defengine == 1) {
				$response['message'] = 'You can not uninstall the default search engine!';
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit;
			}
		} else if ($type == 'auth') {
			if ($row->auth == 'elxis') {
				$response['message'] = 'You can not uninstall the Elxis authentication method!';
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit;
			}
		} else if ($type == 'templates') {
			$cur_template = ($row->section == 'backend') ? $elxis->getConfig('ATEMPLATE') : $elxis->getConfig('TEMPLATE');
			if ($cur_template == $row->template) {
				$response['message'] = 'You can not uninstall the current template!';
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit;
			}
			if ($row->section == 'backend') { $xmltype = 'atemplate'; }
		}

		elxisLoader::loadFile('components/com_extmanager/includes/extension.xml.php');
		$exml = new extensionXML();
		$info = $exml->quickXML($xmltype, $row->$dbcol);
		$version = $info['version'];
		unset($exml);

		elxisLoader::loadFile('components/com_extmanager/includes/installer.class.php');
		$installer = new elxisInstaller();
		$ok = $installer->uninstall($xmltype, $row->$dbcol, $row->id, $version);
		if (!$ok) {
			$msg = $installer->getError();
			if ($msg == '') { $msg = $eLang->get('ACTION_FAILED'); }
			$response['message'] = $msg;
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$response['success'] = 1;
		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/*************************************************************/
	/* PREPARE TO EDIT EXTENSION (ALSO "ADD" ACTION FOR MODULES) */
	/*************************************************************/
	protected function editExtension($type, $is_add=false) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$pathway = eFactory::getPathway();
		$eDoc = eFactory::getDocument();

		if (($type == '') || ($elxis->acl()->check('com_extmanager', $type, 'edit') < 1)) {
			$msg = $eLang->get('NOTALLOWACTION');
			$link = $elxis->makeAURL('extmanager:/');
			$elxis->redirect($link, $msg, true);
		}

		$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
		$extdata = $this->loadExtension($type, $id, $elxis, $eLang, $is_add);
		if ($extdata['errormsg'] != '') {
			$link = $elxis->makeAURL('extmanager:/').$type.'/';
			$elxis->redirect($link, $extdata['errormsg'], true);
		}

		elxisLoader::loadFile('components/com_extmanager/includes/extension.xml.php');
		$exml = new extensionXML();
		$exml->parse($extdata['xmlfile'], true);
		$exml->checkDependencies();

		$pathway->addNode($eLang->get('EXTENSIONS'), 'extmanager:/');
		$pathway->addNode($extdata['typetitle'], 'extmanager:'.$type.'/');
		if ($type == 'templates') {
			$pgtitle = sprintf($eLang->get('EDIT_TEMPLATE_X'), $extdata['exttitle']);
			$eDoc->setTitle($pgtitle);
			$pathway->addNode($extdata['exttitle']);
		} else if ($type == 'components') {
			$pgtitle = sprintf($eLang->get('EDIT_COMPONENT_X'), $extdata['exttitle']);
			$eDoc->setTitle($pgtitle);
			$pathway->addNode($extdata['exttitle']);
		} else if ($type == 'modules') {
			if ($is_add) {
				$eDoc->setTitle($eLang->get('ADD_NEW_MODULE'));
				$pathway->addNode($eLang->get('NEW'));
			} else {
				if ($extdata['extension']->module == 'mod_content') {
					$eDoc->setTitle($eLang->get('EDIT_TEXT_MODULE'));
				} else {
					$pgtitle = sprintf($eLang->get('EDIT_MODULE_X'), $extdata['extension']->module);
					$eDoc->setTitle($pgtitle);
				}
				$pathway->addNode($extdata['extension']->title);
			}
		} else {
			$eDoc->setTitle($eLang->get('EDIT').' '.$extdata['exttitle']);
			$pathway->addNode($extdata['exttitle']);
		}

		$cancel_link = $elxis->makeAURL('extmanager:'.$extdata['type'].'/');
		$p = array();
		if ($extdata['listpage']['page'] > 0) { $p[] = 'page='.$extdata['listpage']['page']; }
		if ($extdata['listpage']['sn'] != '') { $p[] = 'sn='.$extdata['listpage']['sn']; }
		if ($extdata['listpage']['so'] != '') { $p[] = 'so='.$extdata['listpage']['so']; }
		if ($extdata['listpage']['section'] != '') { $p[] = 'section='.$extdata['listpage']['section']; }
		if ($p) { $cancel_link .= '?'.implode('&', $p); }
		unset($p);

		$toolbar = $elxis->obj('toolbar');
		$toolbar->add($eLang->get('SAVE'), 'save', false, '', 'elx5Submit(\'save\', \'fmextedit\', \'eexttask\');');
		$toolbar->add($eLang->get('APPLY'), 'saveedit', false, '', 'elx5Submit(\'apply\', \'fmextedit\', \'eexttask\');');
		$toolbar->add($eLang->get('CANCEL'), 'cancel', false, $cancel_link);
		if ($extdata['type'] == 'modules') {
			if ((intval($extdata['extension']->id) > 0) && ($extdata['extension']->section == 'frontend')) {
				$deflang = $elxis->getConfig('LANG');
				$prevurl = $elxis->makeURL($deflang.':content:modpreview', 'inner.php').'?id='.$extdata['extension']->id;
				$toolbar->add($eLang->get('PREVIEW'), 'fas fa-eye', false, '', 'elxPopup(\''.$prevurl.'\', 700, 500, \'modulepreview\', \'yes\');', '', '', '');
			}
		}
		unset($cancel_link);

		$eDoc->addFontAwesome();
		$eDoc->addStyleLink($elxis->secureBase().'/components/com_extmanager/css/extmanager'.$eLang->getinfo('RTLSFX').'.css');
		$eDoc->addScriptLink($elxis->secureBase().'/components/com_extmanager/js/extmanager.js');

		$this->view->editExtension($extdata, $exml, $elxis, $eLang);
	}


	/***********************************************************/
	/* LOAD EXTENSION FOR EDIT (ALSO "ADD" ACTION FOR MODULES) */
	/***********************************************************/
	private function loadExtension($type, $id, $elxis, $eLang, $is_add=false) {
		$extdata = array(
			'errormsg' => '',
			'type' => $type,
			'extension' => false,
			'xmltype' => '',
			'xmlfile' => '',
			'typetitle' => '',
			'exttitle' => '',
			'listpage' => array('page' => 0, 'sn' => '', 'so' => '', 'section' => ''),
			'extspecific' => array()
		);

		if (isset($_GET['page'])) {$extdata['listpage']['page'] = (int)$_GET['page']; }
		if (isset($_GET['sn'])) { $extdata['listpage']['sn'] = trim($_GET['sn']); }
		if (isset($_GET['so'])) { $extdata['listpage']['so'] = trim($_GET['so']); }
		if (isset($_GET['section'])) { $extdata['listpage']['section'] = trim($_GET['section']); }

		if ($type == 'engines') {
			$row = new enginesDbTable();
			if (!$row->load($id)) {
				$extdata['errormsg'] = 'Search engine not found!';
				return $extdata;
			}
			$extdata['xmltype'] = 'engine';
			$extdata['xmlfile'] = ELXIS_PATH.'/components/com_search/engines/'.$row->engine.'/'.$row->engine.'.engine.xml';
			if (!file_exists($extdata['xmlfile'])) {
				$extdata['errormsg'] = 'Extension XML file was not found!';
				return $extdata;
			}
			$eLang->load('com_search', 'component');
			$eLang->load($row->engine, 'engine');
			$extdata['extspecific']['allengines'] = $this->model->getAllEngines();
			$extdata['typetitle'] = $eLang->get('SEARCH_ENGINES');
			$extdata['exttitle'] = $row->title;
			$extdata['extension'] = $row;
			return $extdata;
		}

		if ($type == 'auth') {
			$row = new authenticationDbTable();
			if (!$row->load($id)) {
				$extdata['errormsg'] = 'Authentication method not found!';
				return $extdata;
			}
			$extdata['xmltype'] = 'auth';
			$extdata['xmlfile'] = ELXIS_PATH.'/components/com_user/auth/'.$row->auth.'/'.$row->auth.'.auth.xml';
			if (!file_exists($extdata['xmlfile'])) {
				$extdata['errormsg'] = 'Extension XML file was not found!';
				return $extdata;
			}
			$eLang->load($row->auth, 'auth');
			$extdata['extspecific']['allauths'] = $this->model->getAllAuths();
			$extdata['typetitle'] = $eLang->get('AUTH_METHODS');
			$extdata['exttitle'] = $row->title;
			$extdata['extension'] = $row;
			return $extdata;
		}

		if ($type == 'templates') {
			$row = new templatesDbTable();
			if (!$row->load($id)) {
				$extdata['errormsg'] = 'Authentication method not found!';
				return $extdata;
			}

			$reldir = ($row->section == 'backend') ? 'templates/admin/' : 'templates/';
			$extdata['xmltype'] = 'template';
			$extdata['xmlfile'] = ELXIS_PATH.'/'.$reldir.$row->template.'/'.$row->template.'.xml';
			if (!file_exists($extdata['xmlfile'])) {
				$extdata['xmlfile'] = ELXIS_PATH.'/'.$reldir.$row->template.'/templateDetails.xml'; //elxis 2009.x compatibility
				if (!file_exists($xmlfile)) {
					$extdata['errormsg'] = 'Extension XML file was not found!';
					return $extdata;
				}
			}
			$exttype = ($row->section == 'backend') ? 'atemplate' : 'template';
			$eLang->load($row->template, $exttype);

			$extdata['typetitle'] = $eLang->get('TEMPLATES');
			$extdata['exttitle'] = $row->title;
			$extdata['extension'] = $row;
			return $extdata;
		}

		if ($type == 'plugins') {
			$row = new pluginsDbTable();
			if (!$row->load($id)) {
				$extdata['errormsg'] = 'Plugin not found!';
				return $extdata;
			}
			$extdata['xmltype'] = 'plugin';
			$extdata['xmlfile'] = ELXIS_PATH.'/components/com_content/plugins/'.$row->plugin.'/'.$row->plugin.'.plugin.xml';
			if (!file_exists($extdata['xmlfile'])) {
				$extdata['errormsg'] = 'Extension XML file was not found!';
				return $extdata;
			}
			$eLang->load($row->plugin, 'plugin');
			$extdata['extspecific']['allplugins'] = $this->model->getAllPlugins();
			$extdata['typetitle'] = $eLang->get('CONTENT_PLUGINS');
			$extdata['exttitle'] = $row->title;
			$extdata['extension'] = $row;
			return $extdata;
		}

		if ($type == 'components') {
			$row = new componentsDbTable();
			if (!$row->load($id)) {
				$extdata['errormsg'] = 'Component not found!';
				return $extdata;
			}
			if ($elxis->acl()->check('component', $row->component, 'manage') < 1) {
				$extdata['errormsg'] = $eLang->get('NOTALLOWMANITEM');
				return $extdata;
			}

			$aclrows = array();
			$wheres = array('category' => 'component', 'element' => $row->component);
			$aclrows1 = $this->model->queryACL($wheres);
			$wheres = array('category' => $row->component);
			$aclrows2 = $this->model->queryACL($wheres);
			if ($aclrows1) {
				$aclrows = $aclrows1;
				if ($aclrows2) {
					foreach($aclrows2 as $aclrow2) { $aclrows[] = $aclrow2; }
				}
			} else if ($aclrows2) {
				$aclrows = $aclrows2;
			}
			$extdata['extspecific']['aclrows'] = $aclrows;

			unset($aclrows1, $aclrows2, $wheres, $aclrows);

			$cname = preg_replace('/^(com\_)/', '', $row->component);
			$extdata['xmltype'] = 'component';
			$extdata['xmlfile'] = ELXIS_PATH.'/components/'.$row->component.'/'.$cname.'.xml';
			if (!file_exists($extdata['xmlfile'])) {
				$extdata['errormsg'] = 'Extension XML file was not found!';
				return $extdata;
			}

			$eLang->load($row->component, 'component');

			$groups = $this->model->getGroups('level', 'DESC');
			$extdata['extspecific']['groups'] = $this->translateGroupNames($groups, $eLang);
			$extdata['extspecific']['users'] = array();
			$totalusers = $this->model->countUsers();
			if ($totalusers < 50) { $extdata['extspecific']['users'] = $this->model->getUsers(); }
			$extdata['typetitle'] = $eLang->get('COMPONENTS');
			$extdata['exttitle'] = $row->name;
			$extdata['extension'] = $row;
			return $extdata;
		}

		if ($type != 'modules') {
			$extdata['errormsg'] = 'Not supported extension type '.$type.'!';
			return $extdata;
		}

		//modules
		$extdata['extspecific']['aclrows'] = array();
		$extdata['extspecific']['groups'] = array();
		$extdata['extspecific']['users'] = array();
		$extdata['extspecific']['allmenuitems'] = array();
		$extdata['extspecific']['modmenuitems'] = array(0);

		if ($is_add) {
			if ($elxis->acl()->check('com_extmanager', 'modules', 'install') < 1) {
				$extdata['errormsg'] = $eLang->get('NOTALLOWACTION');
				return $extdata;
			}

			$row = new modulesDbTable();
			$row->position = 'left';
			$row->published = 1;
			$row->ordering = 0;
			$row->module = 'mod_content';
			$row->iscore = 1;
			$row->section = 'frontend';
		} else {
			$row = new modulesDbTable();
			if (!$row->load($id)) {
				$extdata['errormsg'] = 'Module not found!';
				return $extdata;
			}
			if ($elxis->acl()->check('module', $row->module, 'manage', $id) < 1) {
				$extdata['errormsg'] = $eLang->get('NOTALLOWMANITEM');
				return $extdata;
			}

			$wheres = array('category' => 'module', 'element' => $row->module, 'identity' => $row->id);
			$extdata['extspecific']['aclrows'] = $this->model->queryACL($wheres);
			unset($wheres);

			$groups = $this->model->getGroups('level', 'DESC');
			$extdata['extspecific']['groups'] = $this->translateGroupNames($groups, $eLang);
			$totalusers = $this->model->countUsers();
			if ($totalusers < 50) { $extdata['extspecific']['users'] = $this->model->getUsers(); }
			unset($groups, $totalusers);
		}

		$extdata['extspecific']['positions'] = $this->model->getPositions();
		$extdata['extspecific']['posmods'] = $this->model->getModsByPosition($row->position);
		
		if ($row->section == 'frontend') { $extdata['extspecific']['allmenuitems'] = $this->model->getMenuItems($row->section); }
		if (!$is_add) {
			if ($row->section == 'frontend') {
				$extdata['extspecific']['modmenuitems'] = $this->model->getModMenuItems($row->id);
			}
		}
		$extdata['xmltype'] = 'module';
		$extdata['xmlfile'] = ELXIS_PATH.'/modules/'.$row->module.'/'.$row->module.'.xml';
		if (!file_exists($extdata['xmlfile'])) {
			$extdata['errormsg'] = 'Extension XML file was not found!';
			return $extdata;
		}

		$eLang->load($row->module, 'module');

		$extdata['extspecific']['cron_msg'] = array('info', '');

		if ($elxis->getConfig('CRONJOBS') == 0) {
			$extdata['extspecific']['cron_msg'] = array('warning', $eLang->get('CRON_DISABLED'));
		} else {
			$path = eFactory::getFiles()->elxisPath('logs/lastcron.txt', true);
			if (file_exists($path)) {
				$lastcronts = filemtime($path);
				$extdata['extspecific']['cron_msg'] = array('info', 'Cron jobs - '.$eLang->get('LAST_RUN').': ');
				if ($lastcronts > 1406894400) { //2014-08-01 12:00:00
					$dt = time() - $lastcronts;
					if ($dt < 60) {
						$extdata['extspecific']['cron_msg'][1] .= sprintf($eLang->get('SEC_AGO'), $dt);
					} else if ($dt < 3600) {
						$min = floor($dt / 60);
						$sec = $dt % 60;
						$extdata['extspecific']['cron_msg'][1] .= sprintf($eLang->get('MIN_SEC_AGO'), $min, $sec);
					} else if ($dt < 7200) {
						$min = floor(($dt - 3600) / 60);
						$extdata['extspecific']['cron_msg'][1] .= sprintf($eLang->get('HOUR_MIN_AGO'), $min);
					} else if ($dt < 172800) {//2 days
						$hours = floor($dt / 3600);
						$sec = $dt - ($hours * 3600);
						$min = floor($sec / 60);
						$extdata['extspecific']['cron_msg'][1] .= sprintf($eLang->get('HOURS_MIN_AGO'), $hours, $min);
					} else {
						$extdata['extspecific']['cron_msg'][1] .= eFactory::getDate()->formatTS($lastcronts, $eLang->get('DATE_FORMAT_4'));
					}
				} else {
					$extdata['extspecific']['cron_msg'][1] .= $eLang->get('NEVER');
				}
			} else {
				$extdata['extspecific']['cron_msg'][0] = 'warning';
				$extdata['extspecific']['cron_msg'][1] = 'Cron jobs file '.$path.' does not exist!';
			}
			unset($path);
		}

		$extdata['typetitle'] = $eLang->get('MODULES');
		$extdata['exttitle'] = $row->module;
		$extdata['extension'] = $row;

		return $extdata;
	}


	/***************************************/
	/* ECHO PAGE HEADERS FOR AJAX REQUESTS */
	/***************************************/
	protected function ajaxHeaders($type='text/plain') {
		if(ob_get_length() > 0) { ob_end_clean(); }
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').'GMT');
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header('Content-type: '.$type.'; charset=utf-8');
	}


	/*************************/
	/* TRANSLATE GROUP NAMES */
	/*************************/
	protected function translateGroupNames($rows, $eLang) {
		if (!$rows) { return $rows; }
		foreach ($rows as $i => $row) {
			switch ($row['gid']) {
				case 1: $rows[$i]['groupname'] = $eLang->get('ADMINISTRATOR'); break;
				case 5: $rows[$i]['groupname'] = $eLang->get('USER'); break;
				case 6: $rows[$i]['groupname'] = $eLang->get('EXTERNALUSER'); break;
				case 7: $rows[$i]['groupname'] = $eLang->get('GUEST'); break;
				default: break;
			}
		}
		return $rows;
	}

}

?>