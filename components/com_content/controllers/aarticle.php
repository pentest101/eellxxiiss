<?php 
/**
* @version		$Id: aarticle.php 2420 2021-09-10 17:14:20Z IOS $
* @package		Elxis
* @subpackage	Component Content
* @copyright	Copyright (c) 2006-2021 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class aarticleContentController extends contentController {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null, $model=null, $format='') {
		parent::__construct($view, $model, $format);
	}


	/************************************/
	/* PREPARE TO DISPLAY ARTICLES LIST */
	/************************************/
	public function listarticles() {
		$eLang = eFactory::getLang();
		$pathway = eFactory::getPathway();
		$eDoc = eFactory::getDocument();
		$elxis = eFactory::getElxis();

		$options = array(
			'limit' => 20, 'limitstart' => 0, 'page' => 1, 'maxpage' => 1, 'total' => 0, 'sn' => 'created', 'so' => 'desc', 
			'catid' => -1, 'image' => -1, 'published' => -1, 'important' => -1, 'q' => '', 'author' => '', 'mlsearch' => 0
		);

		if ($elxis->getConfig('MULTILINGUISM') == 1) {
			$global_str = $this->model->componentParams();
			elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
			$params = new elxisParameters($global_str, '', 'component');
			$params->def('mlsearch', 0);//make sure sthis param is defined
			$options['mlsearch'] = (int)$params->get('mlsearch', 0);
			unset($params, $global_str);
		}

		$options['limit'] = (isset($_GET['limit'])) ? (int)$_GET['limit'] : 20;
		if ($options['limit'] < 1) { $options['limit'] = 20; }
		$elxis->updateCookie('limit', $options['limit']);
		$options['page'] = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
		if ($options['page'] < 1) { $options['page'] = 1; }
		$options['sn'] = (isset($_GET['sn'])) ? trim($_GET['sn']) : 'created';
		if (!in_array($options['sn'], array('id', 'catid', 'title', 'published', 'important', 'ordering', 'created', 'hits', 'created_by_name'))) { $options['sn'] = 'created'; }
		$options['so'] = (isset($_GET['so'])) ? trim($_GET['so']) : 'desc';
		if ($options['so'] != 'asc') { $options['so'] = 'desc'; }
		$options['catid'] = (isset($_GET['catid'])) ? (int)$_GET['catid'] : -1;
		if ($options['catid'] < -1) { $options['catid'] = -1; }
		$options['image'] = (isset($_GET['image'])) ? (int)$_GET['image'] : -1;
		if (($options['image'] < -1) || ($options['image'] > 1)) { $options['image'] = -1; }
		$options['published'] = (isset($_GET['published'])) ? (int)$_GET['published'] : -1;
		if (($options['published'] < -1) || ($options['published'] > 1)) { $options['published'] = -1; }
		$options['important'] = (isset($_GET['important'])) ? (int)$_GET['important'] : -1;
		if (($options['important'] < -1) || ($options['important'] > 1)) { $options['important'] = -1; }
		$pat = "#([\']|[\"]|[\$]|[\#]|[\<]|[\>]|[\*]|[\%]|[\~]|[\`]|[\^]|[\|]|[\/]|[\}]|[\\\])#u";
		$options['q'] = filter_input(INPUT_GET, 'q', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$options['q'] = eUTF::trim(preg_replace($pat, '', $options['q']));
		if (eUTF::strlen($options['q']) < 3) { $options['q'] = ''; }
		$options['author'] = filter_input(INPUT_GET, 'author', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$options['author'] = eUTF::trim(preg_replace($pat, '', $options['author']));
		if (eUTF::strlen($options['author']) < 3) { $options['author'] = ''; }

		$options['total'] = $this->model->countAllArticles($options);
		$options['maxpage'] = ceil($options['total']/$options['limit']);
		if ($options['maxpage'] < 1) { $options['maxpage'] = 1; }
		if ($options['page'] > $options['maxpage']) { $options['page'] = $options['maxpage']; }
		$options['limitstart'] = (($options['page'] - 1) * $options['limit']);
		if ($options['total'] > 0) {
			$rows = $this->model->getAllArticles($options);
		} else {
			$rows = array();
		}

		$allgroups = $this->model->getGroups();
		$categories = $this->model->getAllCategories();

		$tree = $elxis->obj('tree');
		$tree->setOptions(array('itemid' => 'catid', 'parentid' => 'parent_id', 'itemname' => 'title', 'html' => false));
		$categories_tree = $tree->makeTree($categories, 10);
		unset($tree);

		if ($categories) {
			$arr = array();
			foreach ($categories as $cat) {
				$idx = (int)$cat->catid;
				$arr[$idx] = $cat->title;
			}
			$categories = $arr;
			unset($arr);
		} else {
			$categories = array();
		}

		$warnmsg = '';
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

		$pathway->deleteAllNodes();
		$pathway->addNode($eLang->get('ARTICLES'));
		$eDoc->setTitle($eLang->get('ARTICLES'));
		$eDoc->addFontAwesome(true);
		$eDoc->addScriptLink($elxis->secureBase().'/components/com_content/js/acontent.js');
		if ($rows) {
			$eDoc->addNativeDocReady('elx5DataTable(\'articlestbl\', true);');
		}

		$this->view->listArticles($rows, $categories, $categories_tree, $allgroups, $warnmsg, $options, $eLang, $elxis);
	}


	/**************************/
	/* SET ARTICLE'S ORDERING */
	/**************************/
	public function setordering() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$response = array('success' => 0, 'message' => '');

		$myaccess = $elxis->acl()->check('com_content', 'article', 'edit');
		if ($myaccess < 1) {
			$response['message'] = $eLang->get('NOTALLOWACTION');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$id = (isset($_POST['elid'])) ? (int)$_POST['elid'] : 0;
		$ordering = (isset($_POST['ordering'])) ? (int)$_POST['ordering'] : 0;
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

		elxisLoader::loadFile('includes/libraries/elxis/database/tables/content.db.php');
		$row = new contentDbTable();
		$row->load($id);
		if (!$row->id) {
			$response['message'] = $eLang->get('ARTICLE_NOT_FOUND');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		if (($myaccess === 1) && ($row->created_by != $elxis->user()->uid)) {
			$response['message'] = $eLang->get('ACTION_ONLY_OWN_ARTS');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

        $allowed = (($row->alevel <= $elxis->acl()->getLowLevel()) || ($row->alevel == $elxis->acl()->getExactLevel())) ? true : false;
		if (!$allowed) {
			$response['message'] = $eLang->get('NOTALLOWACCITEM');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		if ($row->ordering == $ordering) {
			$response['success'] = 1;
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$row->ordering = $ordering;
		if (!$row->update()) {
			$response['message'] = $row->getErrorMsg();
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$wheres = array(array('catid', '=', $row->catid));
		$row->reorder($wheres, false);

		$response['success'] = 1;
		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/*************************************************/
	/* TOGGLE ARTICLE'S PUBLISH & IMPORTANT STATUSES */
	/*************************************************/
	public function togglearticle($toggle_important=false) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$response = array('success' => 0, 'published' => -1, 'icontitle' => '', 'iconclass' => '', 'reloadpage' => 0);

		if ($toggle_important) {
			$myaccess = $elxis->acl()->check('com_content', 'article', 'edit');
		} else {
			$myaccess = $elxis->acl()->check('com_content', 'article', 'publish');
		}

		if ($myaccess < 1) {
			$response['icontitle'] = $eLang->get('NOTALLOWACTION');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$id = (isset($_POST['elid'])) ? (int)$_POST['elid'] : 0;
		if ($id < 1) {
			$response['icontitle'] = 'No article requested!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		elxisLoader::loadFile('includes/libraries/elxis/database/tables/content.db.php');
		$row = new contentDbTable();
		$row->load($id);
		if (!$row->id) {
			$response['icontitle'] = $eLang->get('ARTICLE_NOT_FOUND');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$iconclass = '';
		$newv = -1;
		if ($toggle_important) {
			if ($row->important == 1) {
				$row->important = 0;
				$response['icontitle'] = $eLang->get('IMPORTANT').' : '.$eLang->get('NO').' - '.$eLang->get('CLICK_TOGGLE_STATUS');
				$iconclass = 'elx5_statusicon elx5_statusinact';
			} else {
				$row->important = 1;
				$response['icontitle'] = $eLang->get('IMPORTANT').' : '.$eLang->get('YES').' - '.$eLang->get('CLICK_TOGGLE_STATUS');
				$iconclass = 'elx5_statusicon elx5_statusstar';
			}
			$newv = $row->important;
		} else {
			if ($row->published == 1) {
				$row->published = 0;
				$response['icontitle'] = $eLang->get('UNPUBLISHED').' - '.$eLang->get('CLICK_TOGGLE_STATUS');
			} else {
				$row->published = 1;
				$response['icontitle'] = $eLang->get('PUBLISHED').' - '.$eLang->get('CLICK_TOGGLE_STATUS');
			}
			$row->pubdate = '2014-01-01 00:00:00';
			$row->unpubdate = '2060-01-01 00:00:00';
			$newv = $row->published;
		}

		$ok = $row->store();
		if (!$ok) {
			$response['icontitle'] = $row->getErrorMsg();
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$response['success'] = 1;
		$response['published'] = $newv;
		$response['iconclass'] = $iconclass;

		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/*************************************/
	/* TOGGLE ARTICLE'S IMPORTANT STATUS */
	/*************************************/
	public function toggleimparticle() {
		$this->togglearticle(true);
	}


	/*****************************/
	/* DELETE MULTIPLE ARTICLES */
	/****************************/
	public function deletearticles() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$response = array('success' => 0, 'message' => '');

		$myaccess = $elxis->acl()->check('com_content', 'article', 'delete');
		if ($myaccess < 1) {
			$response['message'] = $eLang->get('NOTALLOWACTION');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$ids = array();
		$elids = isset($_POST['elids']) ? trim($_POST['elids']) : '';//multiple select
		if ($elids != '') {
			$parts = explode(',', $elids);
			foreach ($parts as $part) {
				$id = (int)$part;
				if ($id > 0) { $ids[] = $id; }
			}
		}

		if (!$ids) {
			$response['message'] = 'No articles selected!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$rows = $this->model->getArticlesById($ids);
		if (!$rows) {
			$response['message'] = 'Requested article(s) not found!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$delete_items = array();
		$delete_images = array();
		foreach ($rows as $row) {
			if (($myaccess === 1) && ($row['created_by'] != $elxis->user()->uid)) { continue; }
			$allowed = (($row['alevel'] <= $elxis->acl()->getLowLevel()) || ($row['alevel'] == $elxis->acl()->getExactLevel())) ? true : false;
			if (!$allowed) { continue; }
			$id = (int)$row['id'];
			$delete_items[] = $id;
			if (trim($row['image']) != '') { $delete_images[] = $row['image']; }
		}

		if (count($delete_items) == 0) {
			$response['message'] = $eLang->get('NOTALLOWACCITEM');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$this->model->deleteArticles($delete_items);
		if ($delete_images) {
			foreach ($delete_images as $delete_image) {
				$n = $this->model->countImageArticles($delete_image, 0);
				if ($n == 0) {
					$this->deleteArticleImage($delete_image);
				}
			}
		}

		$response['success'] = 1;
		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/************************/
	/* COPY ARTICLES (AJAX) */
	/************************/
	public function copyarticles() {
		$this->copymovearticles(false);
	}


	/************************/
	/* MOVE ARTICLES (AJAX) */
	/************************/
	public function movearticles() {
		$this->copymovearticles(true);
	}


	/********************************/
	/* COPY OR MOVE ARTICLES (AJAX) */
	/********************************/
	public function copymovearticles($is_move=false) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eFiles = eFactory::getFiles();

		$response = array('success' => 0, 'message' => '');

		$allowed = true;
		if ($is_move) {
			if ($elxis->acl()->check('com_content', 'article', 'edit') < 2) { $allowed = false; }
		} else {
			if ($elxis->acl()->check('com_content', 'article', 'add') < 2) { $allowed = false; }
		}

		if (!$allowed) {
			$response['message'] = $eLang->get('NOTALLOWACTION');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$catid = isset($_POST['catid']) ? (int)$_POST['catid'] : -1;
		if ($catid < 0) {
			$response['message'] = 'No category selected!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$ids = array();
		$ids_str = isset($_POST['ids']) ? trim($_POST['ids']) : '';
		if ($ids_str != '') {
			$parts = explode(',', $ids_str);
			foreach ($parts as $part) {
				$id = (int)$part;
				if ($id > 0) { $ids[] = $id; }
			}
		}

		if (count($ids) == 0) {
			$response['message'] = $eLang->get('NO_ITEMS_SELECTED');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		if ($catid > 0) {//check category exists
			elxisLoader::loadFile('includes/libraries/elxis/database/tables/categories.db.php');
			$cat = new categoriesDbTable();
			if (!$cat->load($catid)) {
				$response['message'] = 'Category not found!';
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit;
			}
			unset($cat);
		}

		$success_actions = 0;
		$now = eFactory::getDate()->getDate();
		$uid = $elxis->user()->uid;
		$author = ($elxis->getConfig('REALNAME') == 1) ? $elxis->user()->firstname.' '.$elxis->user()->lastname : $elxis->user()->uname;

		elxisLoader::loadFile('includes/libraries/elxis/database/tables/content.db.php');
		elxisLoader::loadFile('includes/libraries/elxis/database/tables/translations.db.php');

		foreach ($ids as $id) {
			$row = new contentDbTable();
			if (!$row->load($id)) { continue; }
			if ($is_move) {
				$row->catid = $catid;
				$row->modified = $now;
				$row->modified_by = $uid;
				$row->modified_by_name = $author;
				if ($row->update()) { $success_actions++; }
				unset($row);
			} else {
				$row->forceNew(true);

				preg_match('/\d+$/', $row->seotitle, $matches);
				if ($matches) {
					$x = (int)$matches[0];
					if (($x > 0) && ($x < 100)) {
						$baseseotitle = preg_replace('/\d+$/', '', $row->seotitle);
						$basetitle = eUTF::trim(preg_replace('/\d+$/', '', $row->title));
						if ($baseseotitle == '') { $baseseotitle = $row->seotitle; }
						if ($basetitle == '') { $basetitle = $row->title; }
						$inc = $this->model->findNextSeoTitle($baseseotitle);
						$newseotitle = $baseseotitle.$inc;
						$newtitle = $basetitle.' '.$inc;
					} else {
						$inc = 2;
						$newseotitle = $row->seotitle.'2';
						$newtitle = $row->title.' 2';
					}
				} else {
					$inc = $this->model->findNextSeoTitle($row->seotitle);
					$newseotitle = $row->seotitle.$inc;
					$newtitle = $row->title.' '.$inc;
				}
				unset($matches);

				if ($catid == $row->catid) { $row->title = $newtitle; }
				$row->catid = $catid;
				$row->seotitle = $newseotitle;
				$row->created = $now;
				$row->created_by = $uid;
				$row->created_by_name = $author;
				$row->modified = '1970-01-01 00:00:00';
				$row->modified_by = 0;
				$row->modified_by_name = '';
				$row->hits = 0;

				if ($row->insert()) {
					$success_actions++;
					//copy translations
					$trans = $this->model->allArticleTrans($id);
					if ($trans) {
						foreach ($trans as $tran) {
							$trow = new translationsDbTable();
							$trow->category = 'com_content';
							$trow->element = $tran['element'];
							$trow->language = $tran['language'];
							$trow->elid = $row->id;
							$trow->translation = $tran['translation'];
							$trow->insert();
							unset($trow);
						}
					}
					unset($trans);
				}
				unset($row);
			}
		}

		if ($success_actions > 0) {
			$response['success'] = 1;
		} else {
			$response['message'] = $eLang->get('ACTION_FAILED');
		}
		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/***************/
	/* ADD ARTICLE */
	/***************/
	public function addarticle() {
		$elxis = eFactory::getElxis();
		if ($elxis->acl()->check('com_content', 'article', 'add') < 1) {
			$msg = eFactory::getLang()->get('NOTALLOWACTION');
			$link = $elxis->makeAURL('content:articles/');
			$elxis->redirect($link, $msg, true);
		}

		elxisLoader::loadFile('includes/libraries/elxis/database/tables/content.db.php');
		$row = new contentDbTable();
		$row->published = 1;
		$this->editarticle($row);
	}


	/********************/
	/* ADD/EDIT ARTICLE */
	/********************/
	public function editarticle($row=null) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$eFiles = eFactory::getFiles();
		$pathway = eFactory::getPathway();
		$eDoc = eFactory::getDocument();

		$is_new = true;
		$ordering = array();
		$ordering['total'] = 0;
		$ordering['start'] = -1;
		$ordering['end'] = 9999;
		$ordering['articles'] = array();
		$comments = array();
		if (!$row) {
			$myaccess = $elxis->acl()->check('com_content', 'article', 'edit');
			if ($myaccess < 1) {
				$msg = $eLang->get('NOTALLOWACTION');
				$link = $elxis->makeAURL('content:articles/');
				$elxis->redirect($link, $msg, true);
			}
			$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
			elxisLoader::loadFile('includes/libraries/elxis/database/tables/content.db.php');
			$row = new contentDbTable();
			if (!$row->load($id)) {
				$link = $elxis->makeAURL('content:articles/');
				$elxis->redirect($link, 'Article not found', true);
			}

			if (strlen($row->pubdate) == 10) { $row->pubdate .= ' 00:00:00'; } // Elxis 4.2- compatibility

			if (($myaccess === 1) && ($row->created_by != $elxis->user()->uid)) {
				$link = $elxis->makeAURL('content:articles/');
				$elxis->redirect($link, $eLang->get('ACTION_ONLY_OWN_ARTS'), true);
			}

			$allowed = (($row->alevel <= $elxis->acl()->getLowLevel()) || ($row->alevel == $elxis->acl()->getExactLevel())) ? true : false;
			if (!$allowed) {
				$link = $elxis->makeAURL('content:articles/');
				$elxis->redirect($link, $eLang->get('NOTALLOWACCITEM'), true);
			}

			$ordering = array();
			$ordering['total'] = $this->model->countCtgArticles($row->catid);
			if ($ordering['total'] > 0) {
				if ($ordering['total'] > 50) {
					$ordering['start'] = $row->ordering - 25;
					if ($ordering['start'] < 2) { $ordering['start'] = 0; }
					if (($ordering['start'] + 50) > $ordering['total']) {
						$ordering['start'] = $ordering['total'] - 50;
					}
					$ordering['end'] = $ordering['start'] + 51;
				} else {
					$ordering['start'] = 0;
					$ordering['end'] = $ordering['total'] + 1;
				}
				$ordering['articles'] = $this->model->getOrderingArticles($row->catid, $ordering['start'], 0, 50);	
			}

			$comments = $this->model->fetchComments($row->id, false);
			$is_new = false;
		}

		if ($elxis->getConfig('CRONJOBS') == 0) {
			$cron_msg = '<span class="elx5_smwarning">'.$eLang->get('CRON_DISABLED').'</span>';
		} else {
			$path = $eFiles->elxisPath('logs/lastcron.txt', true);
			if (file_exists($path)) {
				$lastcronts = filemtime($path);
				$cron_msg = 'Cron jobs - '.$eLang->get('LAST_RUN').': ';
				if ($lastcronts > 1406894400) { //2014-08-01 12:00:00
					$dt = time() - $lastcronts;
					if ($dt < 60) {
						$cron_msg .= sprintf($eLang->get('SEC_AGO'), $dt);
					} else if ($dt < 3600) {
						$min = floor($dt / 60);
						$sec = $dt % 60;
						$cron_msg .= sprintf($eLang->get('MIN_SEC_AGO'), $min, $sec);
					} else if ($dt < 7200) {
						$min = floor(($dt - 3600) / 60);
						$cron_msg .= sprintf($eLang->get('HOUR_MIN_AGO'), $min);
					} else if ($dt < 172800) {//2 days
						$hours = floor($dt / 3600);
						$sec = $dt - ($hours * 3600);
						$min = floor($sec / 60);
						$cron_msg .= sprintf($eLang->get('HOURS_MIN_AGO'), $hours, $min);
					} else {
						$cron_msg .= eFactory::getDate()->formatTS($lastcronts, $eLang->get('DATE_FORMAT_4'));
					}
				} else {
					$cron_msg .= $eLang->get('NEVER');
				}
			} else {
				$cron_msg = '<span class="elx5_smerror">Cron jobs file '.$path.' does not exist!</span>';
			}
			unset($path);
		}

		$allctgs = $this->model->getAllCategories();
		$tree = $elxis->obj('tree');
		$tree->setOptions(array('itemid' => 'catid', 'parentid' => 'parent_id', 'itemname' => 'title', 'html' => false));
		$treeitems = $tree->makeTree($allctgs, 10);
		unset($allctgs, $tree);

		$images = $this->model->fetchArticlesImages(100);

		$relkeywords = $this->model->getRelKeys();

		$leveltip = $this->makeLevelsTip();

		if ($elxis->acl()->check('component', 'com_emenu', 'manage') > 0) {
			$menus = $this->model->fetchAllMenus();
		} else {
			$menus = array();
		}

		$pathway->addNode($eLang->get('ARTICLES'), 'content:articles/');
		if ($is_new) {
			$eDoc->setTitle($eLang->get('NEW_ARTICLE'));
			$pathway->addNode($eLang->get('NEW_ARTICLE'));
		} else {
			$eDoc->setTitle($eLang->get('EDIT_ARTICLE'));
			$pathway->addNode($eLang->get('EDIT_ARTICLE').' '.$row->id);
		}

		$toolbar = $elxis->obj('toolbar');
		$toolbar->add($eLang->get('SAVE'), 'save', false, '', 'elx5Submit(\'save\', \'fmartedit\', \'eartask\');');
		$toolbar->add($eLang->get('APPLY'), 'saveedit', false, '', 'elx5Submit(\'apply\', \'fmartedit\', \'eartask\');');
		$toolbar->add($eLang->get('CANCEL'), 'cancel', false, $elxis->makeAURL('content:articles/'));

		$eDoc->addScriptLink($elxis->secureBase().'/components/com_content/js/acontent.js');

		$this->view->editArticle($row, $treeitems, $leveltip, $ordering, $comments, $relkeywords, $cron_msg, $images, $menus);
	}


	/****************************/
	/* PUBLISH A COMMENT (AJAX) */
	/****************************/
	public function publishcomment() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$response = array('success' => 0, 'message' => '');

		$myaccess = $elxis->acl()->check('com_content', 'comments', 'publish');
		if ($myaccess < 1) {
			$response['message'] = $eLang->get('NOTALLOWACTION');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$id = (isset($_POST['id'])) ? (int)$_POST['id'] : 0;
		if ($id < 1) {
			$response['message'] = 'No comment requested!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$comment = $this->model->fetchComment($id);
		if (!$comment) {
			$response['message'] = 'Comment not found!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		if ($comment->published == 1) {
			$response['success'] = 1;
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$allowed = false;
		if (($myaccess == 2) || (($myaccess == 1) && ($comment->uid == $elxis->user()->uid))) { $allowed = true; }
		if (!$allowed) {
			$response['message'] = $eLang->get('NOTALLOWACTION');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$artid = (int)$comment->elid;
		$row = $this->model->getArticlesById($artid);
		if (!$row) {
			$response['message'] = $eLang->get('ARTICLE_NOT_FOUND');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$allowed = (($row['alevel'] <= $elxis->acl()->getLowLevel()) || ($row['alevel'] == $elxis->acl()->getExactLevel())) ? true : false;
		if (!$allowed) {
			$response['message'] = $eLang->get('NOTALLOWACCITEM');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$ok = $this->model->publishComment($id);
		if ($ok) {
			$seolink = '';
			if ($row['catid'] > 0) { $seolink = (string)$this->model->categorySEOLink($row['catid']); }
			$link = $elxis->makeURL('content:'.$seolink.$row['seotitle'].'.html');
			$this->notifyPublishComment($comment->author, $comment->email, $row['title'], $link);
			$response['success'] = 1;
		} else {
			$response['message'] = 'Could not publish comment!';
		}

		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/***************************/
	/* DELETE A COMMENT (AJAX) */
	/***************************/
	public function deletecomment() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$response = array('success' => 0, 'message' => '');

		$myaccess = $elxis->acl()->check('com_content', 'comments', 'delete');
		if ($myaccess < 1) {
			$response['message'] = $eLang->get('NOTALLOWACTION');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$id = (isset($_POST['id'])) ? (int)$_POST['id'] : 0;
		if ($id < 1) {
			$response['message'] = 'No comment requested!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$comment = $this->model->fetchComment($id);
		if (!$comment) {
			$response['message'] = 'Comment not found!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$allowed = false;
		if (($myaccess == 2) || (($myaccess == 1) && ($comment->uid == $elxis->user()->uid))) { $allowed = true; }
		if (!$allowed) {
			$response['message'] = $eLang->get('NOTALLOWACTION');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$artid = (int)$comment->elid;
		$row = $this->model->getArticlesById($artid);
		if (!$row) {
			$response['message'] = $eLang->get('ARTICLE_NOT_FOUND');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$allowed = (($row['alevel'] <= $elxis->acl()->getLowLevel()) || ($row['alevel'] == $elxis->acl()->getExactLevel())) ? true : false;
		if (!$allowed) {
			$response['message'] = $eLang->get('NOTALLOWACCITEM');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$ok = $this->model->deleteComment($id);
		if ($ok) {
			$response['success'] = 1;
		} else {
			$response['message'] = 'Could not delete comment!';
		}

		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/*******************************/
	/* SUGGEST ARTICLE'S SEO TITLE */
	/*******************************/
	public function suggestarticle() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$response = array('success' => 0, 'message' => '', 'seotitle' => '');

		$id = (isset($_POST['elid'])) ? (int)$_POST['elid'] : 0;
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

		$result = $this->validateArtSEO($seotitle, $id);
		if ($result['success'] === false) {
			for ($i=2; $i<6; $i++) {
				if ($i < 5) {
					$newseo = $seotitle.$i;
				} else {
					$newseo = ($id > 0) ? $seotitle.$id : $seotitle.$i;
				}
				$res = $this->validateArtSEO($newseo, $id);
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
	public function validatearticle() {
		$response = array('success' => 0, 'message' => '');

		$id = (isset($_POST['elid'])) ? (int)$_POST['elid'] : 0;
		$seotitle = (isset($_POST['seotitle'])) ? $_POST['seotitle'] : '';
		
		$res = $this->validateArtSEO($seotitle, $id);
		if ($res['success'] === true) { $response['success'] = 1; }

		$response['message'] = addslashes($res['message']);
		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/*********************************/
	/* VALIDATE ARTICLE'S SEO TITLE */
	/*********************************/
	private function validateArtSEO($seotitle, $id) {
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

		if (is_file(ELXIS_PATH.'/'.$seotitle.'.html')) {
			$result['message'] = sprintf($eLang->get('FILE_NAMED'), $seotitle.'.html');
			return $result;
		}

		$reserved_names = array('index', 'feeds', 'contenttools',  'tags', 'send-to-friend');
		if (in_array($seotitle, $reserved_names)) {
			$result['message'] = sprintf($eLang->get('SEOTITLE_RESERVED'), $seotitle);
			return $result;
		}

		$c = $this->model->countArticlesBySEO($seotitle, $id);
		if ($c > 0) {
			$result['message'] = sprintf($eLang->get('OTHER_ARTICLE_SEO'), $seotitle);
			return $result;
		}

		$msg = $eLang->get('VALID');
		$result = array('success' => true, 'message' => $msg);
		return $result;
	}


	/****************/
	/* SAVE ARTICLE */
	/****************/
	public function savearticle() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eSession = eFactory::getSession();
		$eFiles = eFactory::getFiles();
		$eDate = eFactory::getDate();

		$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
		if ($id < 0) { $id = 0; }

		$task = isset($_POST['task']) ? trim($_POST['task']) : 'save';

		$redirurl = $elxis->makeAURL('content:articles/');
		if ($id > 0) {
			if ($elxis->acl()->check('com_content', 'article', 'edit') < 1) {
				$elxis->redirect($redirurl, $eLang->get('NOTALLOWACTION'), true);
			}
		} else {
			if ($elxis->acl()->check('com_content', 'article', 'add') < 1) {
				$elxis->redirect($redirurl, $eLang->get('NOTALLOWACTION'), true);
			}
		}

		$sess_token = trim($eSession->get('token_article'));
		$token = trim(filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		if (($token == '') || ($sess_token == '') || ($sess_token != $token)) {
			exitPage::make('403', 'CCON-0013', $eLang->get('REQDROPPEDSEC'));
		}

		$old_image = '';
		$old_hits = 0;
		$old_created = '';
		$old_created_by = '';
		$old_created_by_name = '';
		$old_published = 0;
		$old_catid = 0;
		$old_ordering = 0;

		elxisLoader::loadFile('includes/libraries/elxis/database/tables/content.db.php');
		$row = new contentDbTable();
		if ($id > 0) {
			if (!$row->load($id)) { $elxis->redirect($redirurl, $eLang->get('ARTICLE_NOT_FOUND'), true); }
			$old_image = trim($row->image);
			$old_hits = (int)$row->hits;
			$old_created = $row->created;
			$old_created_by = (int)$row->created_by;
			$old_created_by_name = $row->created_by_name;
			$old_published = (int)$row->published;
			$old_catid = (int)$row->catid;
			$old_ordering = (int)$row->ordering;
			if ($elxis->acl()->check('com_content', 'article', 'edit') < 2) {
				if ($row->created_by != $elxis->user()->uid) {
					$elxis->redirect($redirurl, $eLang->get('NOTALLOWACTION'), true);
				}
			}
		}

		if (!$row->bind($_POST)) {
			$elxis->redirect($redirurl, $row->getErrorMsg(), true);
		}

		$row->published = (isset($_POST['published'])) ? (int)$_POST['published'] : 0;//because it is checkbox!
		$row->important = (isset($_POST['important'])) ? (int)$_POST['important'] : 0;//because it is checkbox!

		if ($id > 0) {
			$redirurledit = $elxis->makeAURL('content:articles/edit.html?id='.$id);
			$row->hits = $old_hits;
			$row->created = $old_created;
			$row->created_by = $old_created_by;
			$row->created_by_name = $old_created_by_name;
		} else {
			$redirurledit = $elxis->makeAURL('content:articles/add.html');
			$row->hits = 0;
			$row->created = $eDate->getDate();
			$row->created_by = $elxis->user()->uid;
			$row->created_by_name = ($elxis->getConfig('REALNAME') == 1) ? $elxis->user()->firstname.' '.$elxis->user()->lastname : $elxis->user()->uname;
		}

		$newcreated = trim(filter_input(INPUT_POST, 'newcreated', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		if (strlen($newcreated) == 16) { $newcreated .= ':00'; }
		if (strlen($newcreated) == 19) {
			$newdate_user = $eDate->convertFormat($newcreated, $eLang->get('DATE_FORMAT_BOX_LONG'), 'Y-m-d H:i:s');
			if ($newdate_user !== false) {
				$newdate_system = $eDate->localToElxis($newdate_user);
				if (strtotime($newdate_system) <= time()) {
					$row->created = $newdate_system;
				}
			}
		}

		if ($elxis->acl()->check('com_content', 'article', 'publish') < 1) {
			if ($id > 0) {
				$row->published = $old_published;
			} else {
				$row->published = 0;
			}
		}

		$seoresult = $this->validateArtSEO($row->seotitle, $id);
		if ($seoresult['success'] === false) {
			$elxis->redirect($redirurledit, $seoresult['message'], true);
		}

		$row->catid = (int)$row->catid;
		$row->alevel = (int)$row->alevel;
		if ($row->catid > 0) {
			$category_alevel = $this->model->getCategoryLevel($row->catid);
			if ($category_alevel > $row->alevel) { $row->alevel = $category_alevel; }
		}

        $allowed = (($row->alevel <= $elxis->acl()->getLowLevel()) || ($row->alevel == $elxis->acl()->getExactLevel())) ? true : false;
		if (!$allowed) {
			$redirurl = $elxis->makeAURL('content:articles/');
			$elxis->redirect($redirurl, 'You can not manage an article with higher access level than yours or place it in a category with higher access level than yours!', true);
		}

		if ($row->relkey == 'OTHER') {
			$row->relkey = '';
			if (isset($_POST['relkey_other'])) {
				$row->relkey = eUTF::trim(filter_input(INPUT_POST, 'relkey_other', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
			}
		}

		if (trim($row->metakeys) == '') {
			$metakeys = $elxis->obj('keywords');
			$keywords = $metakeys->getKeywords($row->title.' '.$row->introtext.' '.$row->maintext, 15, 4, $elxis->getConfig('LANG'));
			if ($keywords) {
				$row->metakeys = implode(',', $keywords);
			}
			unset($metakeys, $keywords);
		} else {
			$keywords = filter_var($row->metakeys, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
        	$keywords = str_replace(array('–', '(', ')', '+', ':', '.', '?', '!', '_', '*', '-', '"', '\'', '@', '#', '$', '%', '&', '[',']', '{', '}', '<', '>', ';'), '', $keywords); 
			$keywords = eUTF::strtolower(eUTF::trim($keywords));
			$arr = explode(',', $keywords);
			$final = array();
			if ($arr) {
				foreach ($arr as $str) {
					if (eUTF::strlen($str) > 2) { $final[] = $str; }
				}
			}
			$row->metakeys = ($final) ? implode(',', $final) : null;
			unset($final, $arr, $keywords);	
		}

		if (isset($_POST['resethits'])) {
			$resethits = (is_array($_POST['resethits'])) ? (int)$_POST['resethits'][0] : (int)$_POST['resethits'];
			if ($resethits == 1) {
				$row->hits = 0;
			}
		}

		$row->pubdate = trim($row->pubdate);
		$row->unpubdate = trim($row->unpubdate);

		if ($row->pubdate == '') { $row->pubdate = '2014-01-01 00:00:00'; }
		if ($row->unpubdate == '') { $row->unpubdate = '2060-01-01 00:00:00'; }

		if ($row->pubdate != '2014-01-01 00:00:00') {
			$newdate = $eDate->convertFormat($row->pubdate, $eLang->get('DATE_FORMAT_BOX_LONG'), 'Y-m-d H:i:s');
			if ($newdate !== false) {
				$row->pubdate = $eDate->localToElxis($newdate);
			} else {
				$row->pubdate = '2014-01-01 00:00:00';
			}
		}

		if ($row->unpubdate != '2060-01-01 00:00:00') {
			$newdate = $eDate->convertFormat($row->unpubdate, $eLang->get('DATE_FORMAT_BOX_LONG'), 'Y-m-d H:i:s');
			if ($newdate !== false) {
				$row->unpubdate = $eDate->localToElxis($newdate);
			} else {
				$row->unpubdate = '2060-01-01 00:00:00';
			}
		}

		$ts = time() - 86400; 
		$yesterday = gmdate('Y-m-d H:i:s', $ts);
		if ($row->pubdate != '2014-01-01 00:00:00') {
			if ($row->pubdate < $yesterday) { $row->pubdate = '2014-01-01 00:00:00'; }
		}
		if ($row->unpubdate != '2060-01-01 00:00:00') {
			if ($row->unpubdate < $yesterday) { $row->unpubdate = '2060-01-01 00:00:00'; }
		}

		$pint = array('art_dateauthor', 'art_dateauthor_pos', 'art_img', 'art_print', 'art_email', 'art_twitter', 'art_facebook', 'art_hits', 'art_comments', 'art_tags', 'art_chain', 'art_related');
		elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
		$params = new  elxisParameters('', '', 'component');
		$row->params = $params->toString($_POST['params'], $pint, array());
		unset($params, $pint);

		if (isset($_POST['image_deleteold'])) {
			$delimage = (is_array($_POST['image_deleteold'])) ? (int)$_POST['image_deleteold'] : (int)$_POST['image_deleteold'];
			if ($delimage === 1) {
				if ($old_image != '') {
					$n = $this->model->countImageArticles($old_image, $row->id);
					if ($n == 0) {
						$ok = $this->deleteArticleImage($old_image);
						if ($ok) {
							$old_image = '';
							$row->image = null;
						}						
					} else {//other articles use the same image, dont delete it
						$old_image = '';
						$row->image = null;
					}
				}
			}
		}

		if (isset($_FILES) && isset($_FILES['image']) && ($_FILES['image']['name'] != '') && ($_FILES['image']['error'] == 0) && ($_FILES['image']['size'] > 0)) {
			$type = $_FILES['image']['type'];
			if (in_array($type, array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/png', 'image/jp2'))) {
				$base = 'media/images/';
				if (defined('ELXIS_MULTISITE')) {
					if (ELXIS_MULTISITE > 1) {
						$base = 'media/images/site'.ELXIS_MULTISITE.'/';
					}
				}
				$extension = $eFiles->getExtension($_FILES['image']['name']);
				$filename = $eFiles->getFilename($_FILES['image']['name']);
				$filename = preg_replace("/[^a-zA-Z0-9\-\_]/", '', $filename);
				if ($filename == '') { $filename = $row->seotitle; }
				$updir = $this->determineUploadFolder();
				if (file_exists(ELXIS_PATH.'/'.$base.$updir.'/'.$filename.'.'.$extension)) {
					$filename = ($row->id > 0) ? 'article'.$row->id : 'article'.rand(1000, 2000);
					if (file_exists(ELXIS_PATH.'/'.$base.$updir.'/'.$filename.'.'.$extension)) {
						$filename = 'articleimage'.rand(1, 100000);
					}
				}

				$relpath = $base.$updir.'/'.$filename.'.'.$extension;
				$ok = $eFiles->upload($_FILES['image']['tmp_name'], $relpath);
				if ($ok) {
					$this->makeMediumThumb($base.$updir.'/', $filename, $extension);
					$row->image = $base.$updir.'/'.$filename.'.'.$extension;
					if ($old_image != '') {
						$n = $this->model->countImageArticles($old_image, $row->id);
						if ($n == 0) {
							$this->deleteArticleImage($old_image);
						}
					}
				}
			}
		}

		if (isset($_POST['shared_image'])) {
			$shared_image = trim(filter_input(INPUT_POST, 'shared_image', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
			if ($shared_image != '') {
				$extension = strtolower($eFiles->getExtension(ELXIS_PATH.'/'.$shared_image));
				if (file_exists(ELXIS_PATH.'/'.$shared_image) && in_array($extension, array('jpg', 'jpeg', 'png', 'gif'))) {
					$row->image = $shared_image;
				}
			}
		}

		$ok = ($id > 0) ? $row->update() : $row->insert();
		if (!$ok) {
			$elxis->redirect($redirurledit, $row->getErrorMsg(), true);
		}
		
		if ($id > 0) {
			if (($old_image != '') && ($old_image != $row->image)) {
				$n = $this->model->countImageArticles($old_image, 0);
				if ($n == 0) {
					$this->deleteArticleImage($old_image);//delete orphan image
				}
			}
		}

		$reorder = false;
		if ($id == 0) {
			$reorder = true;
		} else {
			if (($old_catid <> $row->catid) || ($old_ordering <> $row->ordering)) {
				$reorder = true;
			}
		}
		if ($reorder) {
			$wheres = array(array('catid', '=', $row->catid));
			$row->reorder($wheres, true);
		}

		//save translations
		$sitelangs = $eLang->getSiteLangs(false);
		$translations = array('title' => array(), 'subtitle' => array(), 'metakeys' => array(), 'caption' => array(), 'introtext' => array(), 'maintext' => array());
		foreach ($sitelangs as $lng) {
			if ($lng == $elxis->getConfig('LANG')) { continue; }
			$idx = 'title_'.$lng;
			$translations['title'][$lng] = isset($_POST[$idx]) ? eUTF::trim(filter_input(INPUT_POST, $idx, FILTER_UNSAFE_RAW)) : '';
			$idx = 'subtitle_'.$lng;
			$translations['subtitle'][$lng] = isset($_POST[$idx]) ? eUTF::trim(filter_input(INPUT_POST, $idx, FILTER_UNSAFE_RAW)) : '';
			$idx = 'metakeys_'.$lng;
			$translations['metakeys'][$lng] = isset($_POST[$idx]) ? eUTF::trim(filter_input(INPUT_POST, $idx, FILTER_UNSAFE_RAW)) : '';
			$idx = 'caption_'.$lng;
			$translations['caption'][$lng] = isset($_POST[$idx]) ? eUTF::trim(filter_input(INPUT_POST, $idx, FILTER_UNSAFE_RAW)) : '';
			$idx = 'introtext_'.$lng;
			$translations['introtext'][$lng] = isset($_POST[$idx]) ? eUTF::trim(filter_input(INPUT_POST, $idx, FILTER_UNSAFE_RAW)) : '';
			$idx = 'maintext_'.$lng;
			$translations['maintext'][$lng] = isset($_POST[$idx]) ? eUTF::trim(filter_input(INPUT_POST, $idx, FILTER_UNSAFE_RAW)) : '';
		}
		$elxis->obj('translations')->saveElementTranslations('com_content', 'title', $row->id, $translations['title']);
		$elxis->obj('translations')->saveElementTranslations('com_content', 'subtitle', $row->id, $translations['subtitle']);
		$elxis->obj('translations')->saveElementTranslations('com_content', 'metakeys', $row->id, $translations['metakeys']);
		$elxis->obj('translations')->saveElementTranslations('com_content', 'caption', $row->id, $translations['caption']);
		$elxis->obj('translations')->saveElementTranslations('com_content', 'introtext', $row->id, $translations['introtext']);
		$elxis->obj('translations')->saveElementTranslations('com_content', 'maintext', $row->id, $translations['maintext']);
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
			if ($row->catid > 0) {
				$seolink = $this->model->categorySEOLink($row->catid);
				$link = 'content:'.$seolink.$row->seotitle.'.html';
			} else {
				$link = 'content:'.$row->seotitle.'.html';
			}

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
					$this->model->addMenuTranslations($row->id, $menu_id, false);
				}
				$extra_saved_text = '. '.$eLang->get('NEW_MENU_CREATED');
			}
		}

		$eSession->set('token_article');

		if ($task == 'apply') {
			$redirurl = $elxis->makeAURL('content:articles/edit.html?id='.$row->id);
			$tabopen = (isset($_POST['tabopen'])) ? (int)$_POST['tabopen'] : 0;
			if ($tabopen > 0) { $redirurl .= '&tabopen='.$tabopen; }
		} else {
			$redirurl = $elxis->makeAURL('content:articles/?catid='.$row->catid);
		}
		$elxis->redirect($redirurl, $eLang->get('ITEM_SAVED').$extra_saved_text);
	}


	/*********************************/
	/* SHARE ARTICLE ON SOCIAL MEDIA */
	/*********************************/
	public function sharearticle() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
		$type = isset($_GET['type']) ? trim($_GET['type']) : '';

		if ($id < 1) {
			echo '<div class="elx5_error">No article selected!</div>';
			return;
		}
		if (($type == '') || !in_array($type, array('twitter', 'facebook'))) {
			echo '<div class="elx5_error">Invalid social media type!</div>';
			return;
		}

		elxisLoader::loadFile('includes/libraries/elxis/database/tables/content.db.php');
		$row = new contentDbTable();
		if (!$row->load($id)) {
			echo '<div class="elx5_error">Article with ID '.$id.' not found!</div>';
			return;
		}

		if ($row->published == 0) {
			echo '<div class="elx5_error">Article with ID '.$row->id.' is not published!</div>';
			return;
		}

		if ($row->alevel > 0) {
			echo '<div class="elx5_error">You must not share articles available only to registred users!</div>';
			return;
		}

		$deflang = $elxis->getConfig('LANG');

		if ($row->catid > 0) {
			elxisLoader::loadFile('includes/libraries/elxis/database/tables/categories.db.php');
			$cat = new categoriesDbTable();
			if (!$cat->load($row->catid)) {
				echo '<div class="elx5_error">Article '.$row->id.' is assigned to a non-existing category!</div>';
				return;
			}
			if ($cat->published == 0) {
				echo '<div class="elx5_error">Article '.$row->id.' is assigned to a non-published category!</div>';
				return;
			}

			$link = $elxis->makeURL($deflang.':content:'.$cat->seolink.$row->seotitle.'.html');
			unset($cat);
		} else {
			$link = $elxis->makeURL($deflang.':content:'.$row->seotitle.'.html');
		}

		if ($type == 'twitter') {
			$redirLink = 'https://twitter.com/intent/tweet?text='.urlencode($row->title).'&url='.urlencode($link);
		} else {
			$redirLink = 'https://www.facebook.com/sharer/sharer.php?u='.urlencode($link).'&t='.urlencode($row->title);
		}

		if (ob_get_length() > 0) { ob_end_clean(); }
		header('content-type:text/html; charset=utf-8');
		header('Location: '.$redirLink);
		exit;
	}

}

?>