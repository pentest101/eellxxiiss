<?php 
/**
* @version		$Id: menuitem.php 2434 2022-01-19 17:32:52Z IOS $
* @package		Elxis
* @subpackage	Component eMenu
* @copyright	Copyright (c) 2006-2022 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class menuitemEmenuController extends emenuController {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null, $task='', $model=null) {
		parent::__construct($view, $task, $model);
	}


	/**************************************/
	/* PREPARE TO DISPLAY MENU ITEMS LIST */
	/**************************************/
	public function listmenuitems() {
		$eLang = eFactory::getLang();
		$pathway = eFactory::getPathway();
		$elxis = eFactory::getElxis();
		$eDoc = eFactory::getDocument();

		$segments = eFactory::getURI()->getSegments();
		$collection = str_ireplace('.html', '', $segments[1]);
		$collection = trim(preg_replace('/[^A-Za-z0-9]/', '', $collection));
		if ($this->model->validateCollection($collection) === false) {
			$link = $elxis->makeAURL('emenu:/');
			$elxis->redirect($link, 'Requested collection does not exist!', true);
		}

		$collections = $this->model->getCollections(true);

		$options = array('limit' => 1000, 'limitstart' => 0, 'page' => 1, 'maxpage' => 1, 'sn' => 'nothing', 'so' => 'asc', 'collection' => $collection, 'maxlevel' => 10, 'total' => 0);

		$items = $this->model->getMenuItems($options['collection']);
		$tree = $elxis->obj('tree');
		$tree->setOptions(array('itemid' => 'menu_id', 'parentid' => 'parent_id', 'itemname' => 'title', 'html' => true));
		$rows = $tree->makeTree($items, $options['maxlevel']);
		unset($items, $tree);

		$options['total'] = count($rows);
		if ($options['total'] > 1) {
			$options['maxpage'] = ceil($options['total']/$options['limit']);
			if ($options['maxpage'] < 1) { $options['maxpage'] = 1; }
			if ($options['page'] > $options['maxpage']) { $options['page'] = $options['maxpage']; }
			$options['limitstart'] = (($options['page'] - 1) * $options['limit']);

			if ($options['total'] > $options['limit']) {
				$limitrows = array();
				$end = $options['limitstart'] + $options['limit'];
				$k = 0;
				foreach ($rows as $key => $row) {
					if ($k < $options['limitstart']) { $k++; continue; }
					if ($k >= $end) { break; }
					$limitrows[] = $row;
					$k++;
				}
				$rows = $limitrows;
			}
		}

		$allgroups = $this->model->getGroups();

		$pathway->deleteAllNodes();
		$pathway->addNode($eLang->get('MENU_MANAGER'), 'emenu:/');
		$pathway->addNode($collection);

		$eDoc->setTitle($eLang->get('MANAGE_MENU_ITEMS').' - '.$collection);
		$eDoc->addFontAwesome();
		$eDoc->addStyleLink($elxis->secureBase().'/components/com_emenu/css/emenu'.$eLang->getinfo('RTLSFX').'.css');
		if ($rows) {
			$eDoc->addNativeDocReady('elx5DataTable(\'menuitemstbl\', false);');
		}
		$eDoc->addScriptLink($elxis->secureBase().'/components/com_emenu/js/emenu.js');

		$this->view->listmenuitems($rows, $options, $collections, $allgroups, $eLang, $elxis);
	}


	/*******************************/
	/* MOVE A MENU ITEM UP OR DOWN */
	/*******************************/
	public function moveitem() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$response = array('success' => 0, 'message' => '');

		if ($elxis->acl()->check('com_emenu', 'menu', 'edit') < 1) {
			$response['message'] = $eLang->get('NOTALLOWACTION');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$menu_id = isset($_POST['menu_id']) ? (int)$_POST['menu_id'] : 0;
		if ($menu_id < 1) {
			$response['message'] = 'No menu item requested!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$moveup = (isset($_POST['moveup'])) ? (int)$_POST['moveup'] : 0;
		$inc = ($moveup == 1) ? -1 : 1;

		elxisLoader::loadFile('includes/libraries/elxis/database/tables/menu.db.php');
		$row = new menuDbTable();
		$row->load($menu_id);
		if (!$row->menu_id) {
			$response['message'] = 'Menu item not found!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$wheres = array(
			array('collection', '=', $row->collection),
			array('parent_id', '=', $row->parent_id),
			array('section', '=', $row->section)
		);

		$ok = $row->move($inc, $wheres);
		if (!$ok) {
			$response['message'] = addslashes($row->getErrorMsg());
		} else {
			$response['success'] = 1;
		}

		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/**********************/
	/* DELETE A MENU ITEM */
	/**********************/
	public function deleteitem() {
		$response = array('success' => 0, 'message' => '');

		if (!isset($_POST['elids'])) {
			$response['message'] = 'No menu item set!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$menu_id = (isset($_POST['elids'])) ? (int)$_POST['elids'] : 0;
		if ($menu_id < 1) {
			$response['message'] = 'Invalid menu item!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$result = $this->model->deleteMenuItem($menu_id); //includes acl check
		if ($result['success'] === false) {
			$response['message'] = addslashes($result['message']);
		} else {
			$response['success'] = 1;
		}

		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/********************************/
	/* TOGGLE ITEM'S PUBLISH STATUS */
	/********************************/
	public function toggleitem() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$response = array('success' => 0, 'published' => -1, 'icontitle' => '', 'reloadpage' => 0);

		$menuid = isset($_POST['elid']) ? (int)$_POST['elid'] : 0;

		if ($menuid < 1) {
			$response['icontitle'] = 'No menu item requested!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$result = $this->model->publishItem($menuid, -1); //includes acl checks

		if ($result['success'] === false) {
			$response['icontitle'] = $result['message'];
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		elxisLoader::loadFile('includes/libraries/elxis/database/tables/menu.db.php');
		$row = new menuDbTable();
		$row->load($menuid);

		$response['success'] = 1;
		$response['published'] = $row->published;
		if ($row->published == 1) {
			$response['icontitle'] = $eLang->get('PUBLISHED').' - '.$eLang->get('CLICK_TOGGLE_STATUS');
		} else {
			$response['icontitle'] = $eLang->get('UNPUBLISHED').' - '.$eLang->get('CLICK_TOGGLE_STATUS');
			$response['reloadpage'] = 1;
		}

		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/**********************/
	/* ADD/EDIT MENU ITEM */
	/**********************/
	public function edititem($row=null) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$pathway = eFactory::getPathway();
		$eDoc = eFactory::getDocument();

		$is_new = true;
		if (!$row) {
			if ($elxis->acl()->check('com_emenu', 'menu', 'edit') < 1) {
				$msg = $eLang->get('NOTALLOWACTION');
				$link = $elxis->makeAURL('emenu:/');
				$elxis->redirect($link, $msg, true);
			}
			$menu_id = isset($_GET['menu_id']) ? (int)$_GET['menu_id'] : 0;
			elxisLoader::loadFile('includes/libraries/elxis/database/tables/menu.db.php');
			$row = new menuDbTable();
			if (!$row->load($menu_id)) {
				$link = $elxis->makeAURL('emenu:/');
				$elxis->redirect($link, 'Menu item not found', true);
			}

            $allowed = (($row->alevel <= $elxis->acl()->getLowLevel()) || ($row->alevel == $elxis->acl()->getExactLevel())) ? true : false;
			if (!$allowed) {
				$link = $elxis->makeAURL('emenu:mitems/'.$row->collection.'.html');
				$elxis->redirect($link, $eLang->get('NOTALLOWACCITEM'), true);
			}
			$is_new = false;
			if (trim($row->link) == '') { $row->link = '/'; } //needed to save problem on link validation for frontpage
		}

		$leveltip = $this->makeLevelsTip();
		$treeitems = $this->collectionTree($row->collection);
		$components = $this->componentsList();

		$pathway->addNode($eLang->get('MENU_MANAGER'), 'emenu:/');
		$pathway->addNode($row->collection, 'emenu:mitems/'.$row->collection.'.html');
		if ($is_new) {
			$eDoc->setTitle($eLang->get('MANAGE_MENU_ITEMS').' - '.$eLang->get('NEW'));
			$pathway->addNode($eLang->get('NEW'));
		} else {
			$eDoc->setTitle($eLang->get('MANAGE_MENU_ITEMS').' - '.$eLang->get('EDIT'));
			$pathway->addNode($eLang->get('EDIT').' '.$row->menu_id);
		}

		$toolbar = $elxis->obj('toolbar');
		$toolbar->add($eLang->get('SAVE'), 'save', false, '', 'elx5Submit(\'save\', \'fmrtedit\', \'eprtask\');');
		$toolbar->add($eLang->get('APPLY'), 'saveedit', false, '', 'elx5Submit(\'apply\', \'fmrtedit\', \'eprtask\');');
		$toolbar->add($eLang->get('CANCEL'), 'cancel', false, $elxis->makeAURL('emenu:mitems/'.$row->collection.'.html'));

		$component = '';
		if (!$is_new) {
			if ($row->menu_type == 'link') {
				$component = $this->findComponent($row->link);
			}
		}
        $eDoc->addScriptLink($elxis->secureBase().'/components/com_emenu/js/emenu.js');
        $eDoc->addStyleLink($elxis->secureBase().'/components/com_emenu/css/emenu'.$eLang->getinfo('RTLSFX').'.css');
        if ($component != '') {
			$eDoc->addNativeDocReady('emenuPickComponent(\'com_'.$component.'\');');
		}

		$this->view->editMenuItem($row, $treeitems, $components, $leveltip, $component, $is_new);
	}


	/*****************/
	/* ADD MENU ITEM */
	/*****************/
	public function additem() {
		$elxis = eFactory::getElxis();
		if ($elxis->acl()->check('com_emenu', 'menu', 'add') < 1) {
			$msg = eFactory::getLang()->get('NOTALLOWACTION');
			$link = $elxis->makeAURL('emenu:/');
			$elxis->redirect($link, $msg, true);
		}
		$collection = trim(filter_input(INPUT_GET, 'collection', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		if ($collection == '') { $collection = 'maimenu'; }
		$type = trim(filter_input(INPUT_GET, 'type', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		if (($type == '') || !in_array($type, array('link', 'url', 'separator', 'wrapper', 'onclick'))) { $type = 'link'; }

		elxisLoader::loadFile('includes/libraries/elxis/database/tables/menu.db.php');
		$row = new menuDbTable();
		$row->collection = $collection;
		$row->section = 'frontend';
		$row->target = '';
		$row->file = 'index.php';
		$row->published = 1;
		$row->menu_type = $type;
		if (($type == 'separator') || ($type == 'url') || ($type == 'onclick')) { $row->file = null; }

		$this->edititem($row);
	}


	/************************************/
	/* FIND COMPONENT FROM AN ELXIS URI */
	/************************************/
	private function findComponent($elxis_uri) {
		$parts = preg_split('#\:#', $elxis_uri, -1, PREG_SPLIT_NO_EMPTY);
		if (!$parts) { return 'content'; } //frontpage
		if (strlen($parts[0]) < 3) { array_shift($parts); } //language
		$c = count($parts);
		if ($c > 2) {
			return '';
		} else if ($c == 2) {
			$component = $parts[0];
		} else {
			$component = 'content';
		}

		if (!file_exists(ELXIS_PATH.'/components/com_'.$component.'/'.$component.'.php')) { return ''; }
		return $component;
	}


	/*******************/
	/* MAKE LEVELS TIP */
	/*******************/
	private function makeLevelsTip() {
		$eLang = eFactory::getLang();
		$groups = $this->model->getGroups();
		$elements = array();
		if ($groups) {
			foreach ($groups as $group) {
				switch ($group['gid']) {
					case 7: $name = $eLang->get('GUEST'); break;
					case 6: $name = $eLang->get('EXTERNALUSER'); break;
					case 5: $name = $eLang->get('USER'); break;
					case 1: $name = $eLang->get('ADMINISTRATOR'); break;
					default: $name = $group['groupname']; break;
				}
				$elements[] = (int)$group['level'].': '.$name;
			}
			return implode(', ', $elements);
		}
		return '';
	}


	/*************************************/
	/* MAKE COLLECTION'S MENU ITEMS TREE */
	/*************************************/
	private function collectionTree($collection) {
		$rows = $this->model->getMenuItems($collection);
		$tree = eFactory::getElxis()->obj('tree');
		$tree->setOptions(array('itemid' => 'menu_id', 'parentid' => 'parent_id', 'itemname' => 'title', 'html' => false));
		$items = $tree->makeTree($rows, 10);
		return $items;
	}


	/*************************************/
	/* MAKE COLLECTION'S MENU ITEMS TREE */
	/*************************************/
	private function componentsList() {
		$eLang = eFactory::getLang();
		$rows = array();
		$rows['com_content'] = $eLang->get('CONTENT');
		$comps = $this->model->getComponents();
		if ($comps) {
			foreach ($comps as $comp) {
				if ($comp['component'] == 'com_content') { continue; }
				$cmp = $comp['component'];
				$str = strtoupper(str_replace('com_', '', $comp['component']));
				$name = ($eLang->exist($str)) ? $eLang->get($str) : $comp['name'];
				$rows[$cmp] = $name;
			}
		}
		return $rows;
	}


	/**************************/
	/* PREPARE LINK GENERATOR */
	/**************************/
	public function linkgenerator() {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();

		if ($elxis->acl()->check('com_emenu', 'menu', 'edit') < 1) {
			$this->ajaxHeaders('text/plain');
			echo '<div class="elx5_error">'.$eLang->get('NOTALLOWACTION')."</div>\n";
			exit;
		}

		$component = trim(filter_input(INPUT_POST, 'component', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$no_public = array('com_cpanel', 'com_languages', 'com_emenu', 'com_emedia', 'com_etranslator', 'com_extmanager');

		if (strpos($component, 'com_') === false) {
			$this->ajaxHeaders('text/plain');
			echo '<div class="elx5_error">Invalid component!'."</div>\n";
			exit;
		}
		if (in_array($component, $no_public)) {
			$this->ajaxHeaders('text/plain');
			echo '<div class="elx5_warning">'.$eLang->get('COMP_NO_PUBLIC_IFACE')."</div>\n";
			exit;
		}

		$cname = str_replace('com_', '', $component);
		if (!file_exists(ELXIS_PATH.'/components/'.$component.'/'.$cname.'.php')) {
			$this->ajaxHeaders('text/plain');
			echo '<div class="elx5_error">Invalid component!'."</div>\n";
			exit;
		}

		$xmlmenus = null;
		if (file_exists(ELXIS_PATH.'/components/'.$component.'/'.$cname.'.menu.xml')) {
			elxisLoader::loadFile('includes/libraries/elxis/menu.xml.php');
			$xmenu = new elxisXMLMenu(null);
			$xmlmenus = $xmenu->getAllMenus($cname, 'frontend', $elxis->getConfig('LANG'));
			unset($xmenu);
		}

		if ($cname == 'search') {
			$items = $this->searchGenerator();	
		} else {
			$items = array();
			$items[] = $this->componentfpLink($cname);
		}

		$this->view->linkGeneratorOutput($items, $xmlmenus, $cname, $elxis, $eLang);
	}


	/*********************************/
	/* LINK TO COMPONENT'S FRONTPAGE */
	/*********************************/
	private function componentfpLink($cname) {
		$eLang = eFactory::getLang();

		$item = new stdClass;
		$item->name = sprintf($eLang->get('COMP_FRONTPAGE'), ucfirst($cname));
		$item->title = ucfirst($cname);
		$item->link = $cname.':/';
		$item->secure = 0;
		$item->alevel = 0;
		return $item;
	}


	/**********************************************/
	/* CREATE STANDARD LINKS FOR COMPONENT SEARCH */
	/**********************************************/
	private function searchGenerator() {
		$eFiles = eFactory::getFiles();
		$eLang = eFactory::getLang();

		$component_title = 'Search';
		if ($eLang->currentLang() == eFactory::getElxis()->getConfig('LANG')) {
			$component_title = $eLang->get('SEARCH');
		}
		$items = array();
		$item = new stdClass;
		$item->name = sprintf($eLang->get('COMP_FRONTPAGE'), $eLang->get('SEARCH'));
		$item->title = $component_title;
		$item->link = 'search:/';
		$item->secure = 0;
		$item->alevel = 0;
		$items[] = $item;

		$engs = $eFiles->listFolders('components/com_search/engines/');
		if ($engs) {
			foreach ($engs as $eng) {
				if (!file_exists(ELXIS_PATH.'/components/com_search/engines/'.$eng.'/'.$eng.'.engine.php')) { continue; }
				$item = new stdClass;
				$item->name = $eLang->get('SEARCH').' '.ucfirst($eng);
				$item->title = $component_title.' '.ucfirst($eng);
				$item->link = 'search:'.$eng.'.html';
				$item->secure = 0;
				$item->alevel = 0;
				$items[] = $item;
			}
		}
		return $items;
	}


	/*******************/
	/* CONTENT BROWSER */
	/*******************/
	public function browser() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		if ($elxis->acl()->check('com_emenu', 'menu', 'edit') < 1) {
			echo '<div class="elx5_error">'.$eLang->get('NOTALLOWACTION')."</div>\n";
			return;
		}

		$options = array(
			'catid' => 0,
			'page' => 1,
			'perpage' => 10,
			'total' => 0,
			'maxpage' => 1,
			'limitstart' => 0,
			'type' => 'c',
			'order' => 'oa',
			'articles' => 0
		);

		$options['catid'] = (isset($_GET['catid'])) ? (int)$_GET['catid'] : 0;
		if ($options['catid'] < 0) { $options['catid'] = 0; }
		$options['page'] = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
		if ($options['page'] < 1) { $options['page'] = 1; }
		$options['type'] = isset($_GET['t']) ? $_GET['t'] : 'c';
		if ($options['type'] != 'a') { $options['type'] = 'c'; }
		$options['order'] = isset($_GET['o']) ? $_GET['o'] : 'oa';
		if ($options['type'] == 'a') {
			if (!in_array($options['order'], array('oa', 'od', 'ta', 'td', 'ia', 'id', 'da', 'dd', 'ma', 'md'))) {
				$options['order'] = 'oa';
			}
		} else {
			if (!in_array($options['order'], array('oa', 'od', 'ta', 'td', 'ia', 'id'))) {
				$options['order'] = 'oa';
			}
		}

		if ($options['type'] == 'a') {
			$options['total'] = $this->model->countArticles($options['catid']);
		} else {
			$options['total'] = $this->model->countCategories($options['catid']);
			$options['articles'] = $this->model->countArticles($options['catid']);
		}

		if ($options['total'] > $options['perpage']) {
			$options['maxpage'] = ceil($options['total']/$options['perpage']);
			if ($options['maxpage'] < 1) { $options['maxpage'] = 1; }
			if ($options['page'] > $options['maxpage']) { $options['page'] = $options['maxpage']; }
			$options['limitstart'] = (($options['page'] - 1) * $options['perpage']);
		} else {
			$options['page'] = 1;
		}

		if ($options['type'] == 'a') {
			$rows = $this->model->getArticles($options['catid'], $options['limitstart'], $options['order']);
		} else {
			$rows = $this->model->getCategories($options['catid'], $options['limitstart'], $options['order']);
		}

		$allgroups = $this->model->getGroups();

		$paths = $this->makePath($options['catid']);

        eFactory::getDocument()->addScriptLink($elxis->secureBase().'/components/com_emenu/js/emenu.js');
        eFactory::getDocument()->addStyleLink($elxis->secureBase().'/components/com_emenu/css/emenu'.$eLang->getinfo('RTLSFX').'.css');

		if ($options['type'] == 'a') {
			$this->view->articlesBrowser($rows, $paths, $options, $allgroups);
		} else {
			$this->view->categoriesBrowser($rows, $paths, $options, $allgroups);
		}
	}


	/***************************/
	/* MAKE CATEGORIES PATHWAY */
	/***************************/
	private function makePath($catid=0) {
		$items = array();
		$p = $catid;
		while($p > 0) {
			$row = $this->model->getCategory($p);
			if (!$row) { $p = 0; break; }
			$item = new stdClass;
			$item->catid = $p;
			$item->title = $row['title'];
			$item->seolink = $row['seolink'];
			$items[] = $item;
			$p = $row['parent_id'];
		}

		$item = new stdClass;
		$item->catid = 0;
		$item->title = eFactory::getLang()->get('ROOT');
		$item->seolink = '';
		$items[] = $item;

		return array_reverse($items);
	}


	/******************/
	/* SAVE MENU ITEM */
	/******************/
	public function saveitem() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eSession = eFactory::getSession();

		$task = isset($_POST['task']) ? trim($_POST['task']) : 'save';

		$menu_id = isset($_POST['menu_id']) ? (int)$_POST['menu_id'] : 0;
		if ($menu_id < 0) { $menu_id = 0; }

		$redirurl = $elxis->makeAURL('emenu:/');
		if ($menu_id > 0) {
			if ($elxis->acl()->check('com_emenu', 'menu', 'edit') < 1) {
				$elxis->redirect($redirurl, $eLang->get('NOTALLOWACTION'), true);
			}
		} else {
			if ($elxis->acl()->check('com_emenu', 'menu', 'add') < 1) {
				$elxis->redirect($redirurl, $eLang->get('NOTALLOWACTION'), true);
			}
		}

		$sess_token = trim($eSession->get('token_menuitem'));
		$token = trim(filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		if (($token == '') || ($sess_token == '') || ($sess_token != $token)) {
			exitPage::make('403', 'CEME-0006', $eLang->get('REQDROPPEDSEC'));
		}

		elxisLoader::loadFile('includes/libraries/elxis/database/tables/menu.db.php');
		$row = new menuDbTable();
		$old_ordering = -1;
		if ($menu_id > 0) {
			if (!$row->load($menu_id)) { $elxis->redirect($redirurl, 'Menu item was not found!', true); }
			$old_ordering = $row->ordering;
		}

		if (!$row->bind($_POST)) {
			$elxis->redirect($redirurl, $row->getErrorMsg(), true);
		}

		$row->published = isset($_POST['published']) ? (int)$_POST['published'] : 0;//because it is checkbox
		$row->secure = isset($_POST['secure']) ? (int)$_POST['secure'] : 0;//because it is checkbox
		if ($row->iconfont == 'OTHER') {
			$row->iconfont = '';
			if (isset($_POST['iconfont_other'])) {
				$row->iconfont = eUTF::trim(filter_input(INPUT_POST, 'iconfont_other', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
			}
		}

		if ($menu_id > 0) {
			$redirurledit = $elxis->makeAURL('emenu:mitems/edit.html?menu_id='.$menu_id);
		} else {
			$redirurledit = $elxis->makeAURL('emenu:mitems/add.html?collection='.$row->collection.'&type='.$row->menu_type);
		}

		$row->alevel = (int)$row->alevel;
		if ($row->parent_id > 0) {
			$parent_alevel = $this->model->getItemLevel($row->parent_id);
			if ($parent_alevel > $row->alevel) { $row->alevel = $parent_alevel; }
		}

        $allowed = (($row->alevel <= $elxis->acl()->getLowLevel()) || ($row->alevel == $elxis->acl()->getExactLevel())) ? true : false;
		if (!$allowed) {
			$redirurl = $elxis->makeAURL('emenu:mitems/'.$row->collection.'.html');
			$elxis->redirect($redirurl, $eLang->get('NOTALLOWACCITEM'), true);
		}

		$ok = ($menu_id > 0) ? $row->update() : $row->insert();
		if (!$ok) {
			$elxis->redirect($redirurledit, $row->getErrorMsg(), true);
		}

		if (($menu_id == 0) || ($old_ordering <> $row->ordering)) {
			$reorder = true;
		} else {
			$reorder = false;
		}
		if ($reorder) {
			$wheres = array(array('section', '=', $row->section), array('collection', '=', $row->collection), array('parent_id', '=', $row->parent_id));
			$row->reorder($wheres, true);
		}

		//save translations
		$sitelangs = $eLang->getSiteLangs(false);
		$translations = array('title' => array());
		foreach ($sitelangs as $lng) {
			if ($lng == $elxis->getConfig('LANG')) { continue; }
			$idx = 'title_'.$lng;
			$translations['title'][$lng] = isset($_POST[$idx]) ? eUTF::trim(filter_input(INPUT_POST, $idx, FILTER_UNSAFE_RAW)) : '';
		}

		$elxis->obj('translations')->saveElementTranslations('com_emenu', 'title', $row->menu_id, $translations['title']);

		$eSession->set('token_menuitem');
		if ($task == 'apply') {
			$redirurl = $elxis->makeAURL('emenu:mitems/edit.html?menu_id='.$row->menu_id);
		} else {
			$redirurl = $elxis->makeAURL('emenu:mitems/'.$row->collection.'.html');
		}
		$elxis->redirect($redirurl, $eLang->get('ITEM_SAVED'));
	}


	/*****************************************/
	/* COPY MENU ITEM TO AN OTHER COLLECTION */
	/*****************************************/
	public function copyitemcol() {
		$this->copymoveitem();
	}


	/*****************************************/
	/* MOVE MENU ITEM TO AN OTHER COLLECTION */
	/*****************************************/
	public function moveitemcol() {
		$this->copymoveitem(true);
	}


	/*************************************************/
	/* COPY/MOVE MENU ITEM(S) TO AN OTHER COLLECTION */
	/*************************************************/
	private function copymoveitem($is_move=false) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$response = array('success' => 0, 'message' => '');

		$menu_id = (isset($_POST['menu_id'])) ? (int)$_POST['menu_id'] : 0;
		$collection = (isset($_POST['collection'])) ? trim($_POST['collection']) : '';

		if ($elxis->acl()->check('com_emenu', 'menu', 'edit') < 1) {
			$response['message'] = $eLang->get('NOTALLOWACTION');
		} else if ($menu_id < 1) {
			$response['message'] = 'Invalid menu item!';
		} else if ($collection == '') {
			$response['message'] = 'Invalid collection!';
		}

		if ($response['message'] != '') {
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		elxisLoader::loadFile('includes/libraries/elxis/database/tables/menu.db.php');
		$row = new menuDbTable();
		if (!$row->load($menu_id)) {
			$response['message'] = 'Menu item not found!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}
		if (($row->section != 'frontend') || ($row->collection == $collection)) {
			$response['message'] = 'Invalid action!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$children = $this->model->getMenuChildren($menu_id);
		$last_ordering = $this->model->getRootLastOrdering($collection);
		$last_ordering++;

		if ($is_move) {
			$row->parent_id = 0;
			$row->collection = $collection;
			$row->ordering = $last_ordering;
			$row->store();
			unset($row);
			if ($children) {
				$c1 = 1;
				foreach ($children as $child_id => $childs2) {
					$row = new menuDbTable();
					if ($row->load($child_id)) {
						$row->collection = $collection;
						$row->ordering = $c1;
						$row->store();
						$c1++;
					}
					unset($row);
					if ($childs2) {
						$c2 = 1;
						foreach ($childs2 as $child_id2 => $childs3) {
							$row = new menuDbTable();
							if ($row->load($child_id2)) {
								$row->collection = $collection;
								$row->ordering = $c2;
								$row->store();
								$c2++;
							}
							unset($row);
							if ($childs3) {
								$c3 = 1;
								foreach ($childs3 as $child_id3 => $childs4) {
									$row = new menuDbTable();
									if ($row->load($child_id3)) {
										$row->collection = $collection;
										$row->ordering = $c3;
										$row->store();
										$c3++;
									}
									unset($row);
									if ($childs4) {
										$c4 = 1;
										foreach ($childs4 as $child_id4 => $childs5) {
											$row = new menuDbTable();
											if ($row->load($child_id4)) {
												$row->collection = $collection;
												$row->ordering = $c4;
												$row->store();
												$c4++;
											}
											unset($row);
										}
									}
								}
							}
						}
					}
				}
			}

			$response['success'] = 1;
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		//copy
		$copied_ids = array();

		$row->forceNew(true);
		$row->parent_id = 0;
		$row->collection = $collection;
		$row->ordering = $last_ordering;
		$row->insert();
		$copied_ids[$menu_id] = $row->menu_id;
		unset($row);
		if ($children) {
			$c1 = 1;
			foreach ($children as $child_id => $childs2) {
				$row = new menuDbTable();
				if ($row->load($child_id)) {
					$row->forceNew(true);
					$row->collection = $collection;
					$row->parent_id = $copied_ids[$menu_id];
					$row->ordering = $c1;
					$row->insert();
					$copied_ids[$child_id] = $row->menu_id;
					$c1++;
				}
				unset($row);
				if ($childs2) {
					$c2 = 1;
					foreach ($childs2 as $child_id2 => $childs3) {
						$row = new menuDbTable();
						if ($row->load($child_id2)) {
							$row->forceNew(true);
							$row->collection = $collection;
							$row->parent_id = $copied_ids[$child_id];
							$row->ordering = $c2;
							$row->insert();
							$copied_ids[$child_id2] = $row->menu_id;
							$c2++;
						}
						unset($row);
						if ($childs3) {
							$c3 = 1;
							foreach ($childs3 as $child_id3 => $childs4) {
								$row = new menuDbTable();
								if ($row->load($child_id3)) {
									$row->forceNew(true);
									$row->collection = $collection;
									$row->parent_id = $copied_ids[$child_id2];
									$row->ordering = $c3;
									$row->insert();
									$copied_ids[$child_id3] = $row->menu_id;
									$c3++;
								}
								unset($row);
								if ($childs4) {
									$c4 = 1;
									foreach ($childs4 as $child_id4 => $childs5) {
										$row = new menuDbTable();
										if ($row->load($child_id4)) {
											$row->forceNew(true);
											$row->collection = $collection;
											$row->parent_id = $copied_ids[$child_id3];
											$row->ordering = $c4;
											$row->insert();
											$copied_ids[$child_id4] = $row->menu_id;
											$c4++;
										}
										unset($row);
									}
								}
							}
						}
					}
				}
			}
		}

		$this->model->copyItemsTranslations($copied_ids);

		$response['success'] = 1;
		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}

}
	
?>