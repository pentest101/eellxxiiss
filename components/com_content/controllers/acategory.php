<?php 
/**
* @version		$Id: acategory.php 2434 2022-01-19 17:32:52Z IOS $
* @package		Elxis
* @subpackage	Component Content
* @copyright	Copyright (c) 2006-2022 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class acategoryContentController extends contentController {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null, $model=null, $format='') {
		parent::__construct($view, $model, $format);
	}


	/**************************************/
	/* PREPARE TO DISPLAY CATEGORIES TREE */
	/**************************************/
	public function listcategories() {
		$elxis = eFactory::getElxis();
		$eDoc = eFactory::getDocument();
		$eLang = eFactory::getLang();
		$pathway = eFactory::getPathway();

		$options = array('limit' => 20, 'limitstart' => 0, 'page' => 1, 'maxpage' => 1, 'sn' => 'nothing', 'so' => 'asc', 'maxlevel' => 10, 'total' => 0);

		$options['limit'] = (isset($_GET['limit'])) ? (int)$_GET['limit'] : 20;
		if ($options['limit'] < 1) { $options['limit'] = 20; }
		$elxis->updateCookie('limit', $options['limit']);
		$options['page'] = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
		if ($options['page'] < 1) { $options['page'] = 1; }

		$cats = $this->model->getAllCategories();
		$tree = $elxis->obj('tree');
		$tree->setOptions(array('itemid' => 'catid', 'parentid' => 'parent_id', 'itemname' => 'title', 'html' => true));
		$rows = $tree->makeTree($cats, $options['maxlevel']);
		unset($cats, $tree);

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

		if ($rows) {
			$category_ids = array();
			foreach ($rows as $row) { $category_ids[] = $row->catid; }
			$result = $this->model->countCtgArticles($category_ids);
			foreach ($rows as $key => $row) {
				$cid = $row->catid;
				$rows[$key]->acticles = (isset($result[$cid])) ? $result[$cid] : 0;
			}
			unset($result, $category_ids);
		}

		$allgroups = $this->model->getGroups();

		$pathway->deleteAllNodes();
		$pathway->addNode($eLang->get('CONTENT_CATEGORIES'));
		$eDoc->setTitle($eLang->get('CONTENT_CATEGORIES'));
		$eDoc->addFontAwesome();
		$eDoc->addScriptLink($elxis->secureBase().'/components/com_content/js/acontent.js');
		if ($rows) {
			$eDoc->addNativeDocReady('elx5DataTable(\'categoriestbl\', false);');
		}

		$this->view->listcategories($rows, $options, $allgroups, $eLang, $elxis);
	}


	/*************************************************/
	/* TOGGLE CATEGORY'S PUBLISH STATUS (ICON CLICK) */
	/*************************************************/
	public function togglecategory() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$response = array('success' => 0, 'published' => -1, 'icontitle' => '', 'reloadpage' => 0);
		$catid = isset($_POST['elid']) ? (int)$_POST['elid'] : 0;

		if ($catid < 1) {
			$response['icontitle'] = 'No category requested!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$result = $this->model->publishCategory($catid, -1); //includes acl checks

		if ($result['success'] === false) {
			$response['icontitle'] = $result['message'];
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		elxisLoader::loadFile('includes/libraries/elxis/database/tables/categories.db.php');
		$row = new categoriesDbTable();
		$row->load($catid);

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


	/*********************/
	/* DELETE A CATEGORY */
	/*********************/
	public function deletecategory() {
		$response = array('success' => 0, 'message' => '');

		if (!isset($_POST['elids'])) {
			$response['message'] = 'No category set!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$catid = (isset($_POST['elids'])) ? (int)$_POST['elids'] : 0;
		if ($catid < 1) {
			$response['message'] = 'Invalid category!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$result = $this->model->deleteCategory($catid, true); //includes acl check
		if ($result['success'] === false) {
			$response['message'] = addslashes($result['message']);
		} else {
			$response['success'] = 1;
		}

		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/******************************/
	/* MOVE A CATEGORY UP OR DOWN */
	/******************************/
	public function movecategory() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$response = array('success' => 0, 'message' => '');

		if ($elxis->acl()->check('com_content', 'category', 'edit') < 1) {
			$response['message'] = $eLang->get('NOTALLOWACTION');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$catid = (isset($_POST['catid'])) ? (int)$_POST['catid'] : 0;
		if ($catid < 1) {
			$response['message'] = 'Invalid request!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$moveup = (isset($_POST['moveup'])) ? (int)$_POST['moveup'] : 0;
		$inc = ($moveup == 1) ? -1 : 1;

		elxisLoader::loadFile('includes/libraries/elxis/database/tables/categories.db.php');
		$row = new categoriesDbTable();
		$row->load($catid);
		if (!$row->catid) {
			$response['message'] = 'Category not found!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$wheres = array(
			array('parent_id', '=', $row->parent_id)
		);
		$ok = $row->move($inc, $wheres);
		if (!$ok) {
			$response['message'] = addslashes($row->getErrorMsg());
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$response['success'] = 1;
		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/****************/
	/* ADD CATEGORY */
	/****************/
	public function addcategory() {
		$elxis = eFactory::getElxis();
		if ($elxis->acl()->check('com_content', 'category', 'add') < 1) {
			$msg = eFactory::getLang()->get('NOTALLOWACTION');
			$link = $elxis->makeAURL('content:/');
			$elxis->redirect($link, $msg, true);
		}

		elxisLoader::loadFile('includes/libraries/elxis/database/tables/categories.db.php');
		$row = new categoriesDbTable();
		$row->published = 1;
		$this->editcategory($row);
	}


	/*********************/
	/* ADD/EDIT CATEGORY */
	/*********************/
	public function editcategory($row=null) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$pathway = eFactory::getPathway();
		$eDoc = eFactory::getDocument();

		$is_new = true;
		if (!$row) {
			if ($elxis->acl()->check('com_content', 'category', 'edit') < 1) {
				$msg = $eLang->get('NOTALLOWACTION');
				$link = $elxis->makeAURL('content:categories/');
				$elxis->redirect($link, $msg, true);
			}
			$catid = isset($_GET['catid']) ? (int)$_GET['catid'] : 0;
			elxisLoader::loadFile('includes/libraries/elxis/database/tables/categories.db.php');
			$row = new categoriesDbTable();
			if (!$row->load($catid)) {
				$link = $elxis->makeAURL('content:categories/');
				$elxis->redirect($link, 'Category not found', true);
			}

            $allowed = (($row->alevel <= $elxis->acl()->getLowLevel()) || ($row->alevel == $elxis->acl()->getExactLevel())) ? true : false;
			if (!$allowed) {
				$link = $elxis->makeAURL('content:categories/');
				$elxis->redirect($link, $eLang->get('NOTALLOWACCITEM'), true);
			}
			$is_new = false;
		}

		$allctgs = $this->model->getAllCategories();
		$tree = $elxis->obj('tree');
		$tree->setOptions(array('itemid' => 'catid', 'parentid' => 'parent_id', 'itemname' => 'title', 'html' => false));
		$treeitems = $tree->makeTree($allctgs, 10);
		unset($allctgs, $tree);

		$leveltip = $this->makeLevelsTip();

		if ($elxis->acl()->check('component', 'com_emenu', 'manage') > 0) {
			$menus = $this->model->fetchAllMenus();
		} else {
			$menus = array();
		}

		$pathway->addNode($eLang->get('CONTENT_CATEGORIES'), 'content:categories/');
		if ($is_new) {
			$eDoc->setTitle($eLang->get('NEW_CATEGORY'));
			$pathway->addNode($eLang->get('NEW_CATEGORY'));
		} else {
			$eDoc->setTitle($eLang->get('EDIT_CATEGORY'));
			$pathway->addNode($eLang->get('EDIT_CATEGORY').' '.$row->catid);
		}

		$toolbar = $elxis->obj('toolbar');
		$toolbar->add($eLang->get('SAVE'), 'save', false, '', 'elx5Submit(\'save\', \'fmctgedit\', \'ecttask\');');
		$toolbar->add($eLang->get('APPLY'), 'saveedit', false, '', 'elx5Submit(\'apply\', \'fmctgedit\', \'ecttask\');');
		$toolbar->add($eLang->get('CANCEL'), 'cancel', false, $elxis->makeAURL('content:categories/'));

		$eDoc->addScriptLink($elxis->secureBase().'/components/com_content/js/acontent.js');

		$this->view->editCategory($row, $treeitems, $leveltip, $menus, $elxis, $eLang);
	}


	/********************************/
	/* SUGGEST CATEGORY'S SEO TITLE */
	/********************************/
	public function suggestcategory() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$response = array('success' => 0, 'message' => '', 'seotitle' => '');

		$catid = (isset($_POST['elid'])) ? (int)$_POST['elid'] : 0;
		$title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$title = eUTF::trim($title);
		if ($title == '') {
			$response['message'] = addslashes(sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('TITLE')));
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$title = preg_replace('/[!@#;\'\"\.$%^&*(){}\[\]]/u', '', $title);
		$ascii = strtolower(eUTF::utf8_to_ascii($title, ''));
		$ascii = preg_replace("/[^a-z0-9-_\s]/", '', $ascii);
		if (strlen($ascii) < 3) {
			$response['message'] = addslashes($eLang->get('TITLE_FEW_ALPHANUM'));
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		if ($elxis->getConfig('SEO_MATCH') == 'exact') {
			$parts = preg_split('/[\s]/', $ascii, -1, PREG_SPLIT_NO_EMPTY);
			$seotitle = implode('-', $parts);
			unset($parts);
		} else {//normal
			$parts = preg_split('/[\s]/', $ascii, -1, PREG_SPLIT_NO_EMPTY);
			$nparts = array();
			$length = 0;
			foreach ($parts as $part) {
				if ($length > 30) { break; }
				$plength = strlen($part);
				if ($plength > 2) {
					$nparts[] = $part;
					$length += $plength;
				}
			}
			$seotitle = $nparts ? implode('-', $nparts) : preg_replace('/\s+/', '', $ascii);
			unset($parts, $nparts, $length);
		}
		unset($ascii);

		if (strlen($seotitle) < 3) {
			$response['message'] = addslashes($eLang->get('TITLE_FEW_ALPHANUM'));
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$result = $this->validateCatSEO($seotitle, $catid);
		if ($result['success'] === false) {
			for($i=2; $i<6; $i++) {
				if ($i < 5) {
					$newseo = $seotitle.$i;
				} else {
					$newseo = ($catid > 0) ? $seotitle.$catid : $seotitle.$i;
				}
				$res = $this->validateCatSEO($newseo, $catid);
				if ($res['success'] === true) {
					$seotitle = $newseo;
					break;
				}
			}
			if ($res['success'] === false) {
				$seotitle = $seotitle.'-'.rand(1000, 9999);
			}
		}

		$response['success'] = 1;
		$response['seotitle'] = $seotitle;
		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/**********************/
	/* VALIDATE SEO TITLE */
	/**********************/
	public function validatecategory() {
		$response = array('success' => 0, 'message' => '');

		$catid = (isset($_POST['elid'])) ? (int)$_POST['elid'] : 0;
		$seotitle = (isset($_POST['seotitle'])) ? $_POST['seotitle'] : '';
		
		$res = $this->validateCatSEO($seotitle, $catid);
		if ($res['success'] === true) { $response['success'] = 1; }

		$response['message'] = addslashes($res['message']);
		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/*********************************/
	/* VALIDATE CATEGORY'S SEO TITLE */
	/*********************************/
	private function validateCatSEO($seotitle, $catid) {
		$eLang = eFactory::getLang();

		$result = array('success' => false, 'message' => 'The SEO Title is invalid!');
        if (trim($seotitle) == '') {
			$result['message'] = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('SEOTITLE'));
			return $result;
       	}
        $ascii = preg_replace("/[^a-z0-9\-\_]/", '', $seotitle);
        if ($ascii != $seotitle) {
        	$result['message'] = sprintf($eLang->get('FIELDNOACCCHAR'), $eLang->get('SEOTITLE'));
			return $result;
        }
		if (strlen($seotitle) < 3) {
			$result['message'] = $eLang->get('SEOTITLE_FEW_ALPHANUM');
			return $result;
		}
		if (is_dir(ELXIS_PATH.'/'.$seotitle.'/')) {
			$result['message'] = sprintf($eLang->get('FOLDER_NAMED'), $seotitle);
			return $result;
		}
		if (is_dir(ELXIS_PATH.'/components/com_'.$seotitle)) {
			$result['message'] = sprintf($eLang->get('COMPONENT_NAMED'), $seotitle);
			return $result;
		}
		$c =  $this->model->countComponentsByRoute($seotitle);
		if ($c > 0) {
			$result['message'] = sprintf($eLang->get('COMPONENT_ROUTED'), $seotitle);
			return $result;
		}
		$c = $this->model->countCategoriesBySEO($seotitle, $catid);
		if ($c > 0) {
			$result['message'] = sprintf($eLang->get('OTHER_CATEGORY_SEO'), $seotitle);
			return $result;
		}

		$msg = $eLang->get('VALID');
		$result = array('success' => true, 'message' => $msg);
		return $result;
	}


	/*****************/
	/* SAVE CATEGORY */
	/*****************/
	public function savecategory() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eSession = eFactory::getSession();
		$eFiles = eFactory::getFiles();

		$task = isset($_POST['task']) ? trim($_POST['task']) : 'save';

		$catid = isset($_POST['catid']) ? (int)$_POST['catid'] : 0;
		if ($catid < 0) { $catid = 0; }

		$redirurl = $elxis->makeAURL('content:categories/');
		if ($catid > 0) {
			if ($elxis->acl()->check('com_content', 'category', 'edit') < 1) {
				$elxis->redirect($redirurl, $eLang->get('NOTALLOWACTION'), true);
			}
		} else {
			if ($elxis->acl()->check('com_content', 'category', 'add') < 1) {
				$elxis->redirect($redirurl, $eLang->get('NOTALLOWACTION'), true);
			}
		}

		$sess_token = trim($eSession->get('token_category'));
		$token = trim(filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		if (($token == '') || ($sess_token == '') || ($sess_token != $token)) {
			exitPage::make('403', 'CCON-0006', $eLang->get('REQDROPPEDSEC'));
		}

		elxisLoader::loadFile('includes/libraries/elxis/database/tables/categories.db.php');
		$row = new categoriesDbTable();
		$old_ordering = -1;
		$old_published = 0;
		$old_seolink = '';
		$old_image = '';
		if ($catid > 0) {
			if (!$row->load($catid)) { $elxis->redirect($redirurl, 'Category was not found!', true); }
			$old_ordering = $row->ordering;
			$old_published = $row->published;
			$old_seolink = $row->seolink;
			$old_image = trim($row->image);
		}

		if (!$row->bind($_POST)) {
			$elxis->redirect($redirurl, $row->getErrorMsg(), true);
		}

		$row->published = (isset($_POST['published'])) ? (int)$_POST['published'] : 0;//because it is checkbox!

		if ($catid > 0) {
			$redirurledit = $elxis->makeAURL('content:categories/edit.html?catid='.$catid);
		} else {
			$redirurledit = $elxis->makeAURL('content:categories/add.html');
		}

		if ($elxis->acl()->check('com_content', 'category', 'publish') < 1) {
			$row->published = $old_published;
		}

		$seoresult = $this->validateCatSEO($row->seotitle, $catid);
		if ($seoresult['success'] === false) {
			$elxis->redirect($redirurledit, $seoresult['message'], true);
		}
		
		if ($row->parent_id > 0) {
			$parent_seolink = $this->model->categorySEOLink($row->parent_id);
			if (!$parent_seolink) {
				$elxis->redirect($redirurl, 'Could not determine the SEO Link of the parent category!', true);
			}
			$row->seolink = $parent_seolink.$row->seotitle.'/';
			unset($parent_seolink);
		} else {
			$row->seolink = $row->seotitle.'/';
		}

		$row->alevel = (int)$row->alevel;
		if ($row->parent_id > 0) {
			$parent_alevel = $this->model->getCategoryLevel($row->parent_id);
			if ($parent_alevel > $row->alevel) { $row->alevel = $parent_alevel; }
		}

        $allowed = (($row->alevel <= $elxis->acl()->getLowLevel()) || ($row->alevel == $elxis->acl()->getExactLevel())) ? true : false;
		if (!$allowed) {
			$redirurl = $elxis->makeAURL('content:categories/');
			$elxis->redirect($redirurl, 'You can not manage a category with higher access level than yours!', true);
		}

		$pint = array('ctg_img_empty', 'ctg_layout', 'ctg_show', 'ctg_subcategories', 'ctg_subcategories_cols', 
		'ctg_print', 'ctg_featured_num', 'ctg_featured_img', 'ctg_featured_dateauthor', 'ctg_short_num', 'ctg_short_cols', 
		'ctg_short_img', 'ctg_short_dateauthor', 'ctg_short_text', 'ctg_links_num', 'ctg_links_cols', 'ctg_links_header', 
		'ctg_links_dateauthor', 'ctg_pagination', 'ctg_pagination', 'ctg_nextpages_style', 'comments');
		$pstr = array('ctg_ordering', 'ctg_mods_pos');
		elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
		$params = new  elxisParameters('', '', 'component');
		$row->params = $params->toString($_POST['params'], $pint, $pstr);
		unset($params, $pint, $pstr);

		$img_rel_path = 'media/images/';
		if (defined('ELXIS_MULTISITE')) {
			if (ELXIS_MULTISITE > 1) { $img_rel_path = 'media/images/site'.ELXIS_MULTISITE.'/'; }
		}

		if (isset($_POST['image_deleteold'])) {
			$delimage = (is_array($_POST['image_deleteold'])) ? (int)$_POST['image_deleteold'] : (int)$_POST['image_deleteold'];
			if ($delimage === 1) {
				if ($old_image != '') {
					$ok = $eFiles->deleteFile($old_image);
					if ($ok) {
						$old_image = '';
						$row->image = null;
					}
				}
			}
		}

		if (isset($_FILES) && isset($_FILES['image']) && ($_FILES['image']['name'] != '') && ($_FILES['image']['error'] == 0) && ($_FILES['image']['size'] > 0)) {
			$type = $_FILES['image']['type'];
			if (in_array($type, array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/png', 'image/jp2'))) {
				$extension = $eFiles->getExtension($_FILES['image']['name']);
				$filename = $eFiles->getFilename($_FILES['image']['name']);
				$filename = preg_replace("/[^a-zA-Z0-9\-\_]/", '', $filename);
				if ($filename == '') { $filename = ($row->catid > 0) ? 'category'.$row->catid : 'category'.rand(1000, 2000); }
				if (file_exists(ELXIS_PATH.'/'.$img_rel_path.'categories/'.$filename.'.'.$extension)) {
					$filename = ($row->catid > 0) ? 'category'.$row->catid : 'category'.rand(1000, 2000);
				}
				$relpath = $img_rel_path.'categories/'.$filename.'.'.$extension;
				$ok = $eFiles->upload($_FILES['image']['tmp_name'], $relpath);
				if ($ok) {
					$row->image = $relpath;
					if ($old_image != '') { $eFiles->deleteFile($old_image); }
				}
			}
		}

		$ok = ($catid > 0) ? $row->update() : $row->insert();
		if (!$ok) {
			$elxis->redirect($redirurledit, $row->getErrorMsg(), true);
		}
		
		if ($catid > 0) {
			if ($old_seolink != $row->seolink) {
				$this->model->rebuildSEOLinks($catid, $row->seolink);
			}
			if (($old_image != '') && ($old_image != $row->image)) {
				eFactory::getFiles()->deleteFile($old_image);
			}
		}

		if (($catid == 0) || ($old_ordering <> $row->ordering)) {
			$reorder = true;
		} else {
			$reorder = false;
		}
		if ($reorder) {
			$wheres = array(array('parent_id', '=', $row->parent_id));
			$row->reorder($wheres, true);
		}

		//save translations
		$sitelangs = $eLang->getSiteLangs(false);
		$translations = array('title' => array(), 'description' => array());
		foreach ($sitelangs as $lng) {
			if ($lng == $elxis->getConfig('LANG')) { continue; }
			$idx = 'title_'.$lng;
			$translations['title'][$lng] = isset($_POST[$idx]) ? eUTF::trim(filter_input(INPUT_POST, $idx, FILTER_UNSAFE_RAW)) : '';
			$idx = 'description_'.$lng;
			$translations['description'][$lng] = isset($_POST[$idx]) ? filter_input(INPUT_POST, $idx, FILTER_UNSAFE_RAW) : '';
		}
		$elxis->obj('translations')->saveElementTranslations('com_content', 'category_title', $row->catid, $translations['title']);
		$elxis->obj('translations')->saveElementTranslations('com_content', 'category_description', $row->catid, $translations['description']);
		unset($sitelangs, $translations);

		$new_menu_items = array();
		foreach($_POST as $k => $v) {
			if (strpos($k, 'collect_') !== 0) { continue; }
			$collection = str_replace('collect_', '', $k);
			if ($v == 'ROOT') {
				$parent = 0;
			} else {
				$parent = (int)$v;
				if ($parent < 1) { continue; }
			}
			$new_menu_items[$collection] = $parent;
		}

		$extra_saved_text = '';
		if ($new_menu_items) {
			$link = 'content:'.$row->seolink;

			elxisLoader::loadFile('includes/libraries/elxis/database/tables/menu.db.php');
			foreach ($new_menu_items as $collection => $parent_id) {
				$menu = new menuDbTable();
				$menu->title = $row->title;
				$menu->section = 'frontend';
				$menu->collection = $collection;
				$menu->menu_type = 'link';
				$menu->link = $link;
				$menu->file = 'index.php';
				$menu->published = 1;
				$menu->ordering = 9999;
				$menu->alevel = 0;
				$menu->parent_id = $parent_id;

				if ($menu->parent_id > 0) {
					$parent_alevel = $this->model->getMenuItemLevel($menu->parent_id);
					if ($parent_alevel > $menu->alevel) { $menu->alevel = $parent_alevel; }
				}

				$allowed = (($menu->alevel <= $elxis->acl()->getLowLevel()) || ($menu->alevel == $elxis->acl()->getExactLevel())) ? true : false;
				if (!$allowed) { continue; }
				$ok = $menu->insert();
				if (!$ok) { continue; }
				$menu_id = (int)$menu->menu_id;

				$wheres = array(array('section', '=', $menu->section), array('collection', '=', $menu->collection), array('parent_id', '=', $menu->parent_id));
				$menu->reorder($wheres, true);
				unset($menu);

				if ($elxis->getConfig('MULTILINGUISM') == 1) {
					$this->model->addMenuTranslations($row->catid, $menu_id, true);
				}
				$extra_saved_text = '. '.$eLang->get('NEW_MENU_CREATED');
			}
		}

		$eSession->set('token_category');

		if ($task == 'apply') {
			$redirurl = $elxis->makeAURL('content:categories/edit.html?catid='.$row->catid);
			$tabopen = (isset($_POST['tabopen'])) ? (int)$_POST['tabopen'] : 0;
			if ($tabopen > 0) { $redirurl .= '&tabopen='.$tabopen; }
		} else {
			$redirurl = $elxis->makeAURL('content:categories/');
		}
		$elxis->redirect($redirurl, $eLang->get('ITEM_SAVED').$extra_saved_text);
	}

}

?>