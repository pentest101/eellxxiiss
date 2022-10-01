<?php 
/**
* @version		$Id: collection.php 2433 2022-01-19 17:24:43Z IOS $
* @package		Elxis
* @subpackage	Component eMenu
* @copyright	Copyright (c) 2006-2022 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class collectionEmenuController extends emenuController {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null, $task='', $model=null) {
		parent::__construct($view, $task, $model);
	}


	/***************************************/
	/* PREPARE TO DISPLAY COLLECTIONS LIST */
	/***************************************/
	public function listcollections() {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$eDoc = eFactory::getDocument();
		$pathway = eFactory::getPathway();

		$options = array('limit' => 50, 'page' => 1, 'maxpage' => 1, 'sn' => 'collection', 'so' => 'asc', 'limitstart' => 0, 'total' => 0);

		$options['limit'] = (isset($_GET['limit'])) ? (int)$_GET['limit'] : 50;
		if ($options['limit'] < 1) { $options['limit'] = 50; }
		$options['page'] = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
		if ($options['page'] < 1) { $options['page'] = 1; }
		$options['sn'] = (isset($_GET['sn'])) ? trim($_GET['sn']) : 'collection';
		if ($options['sn'] == '') { $options['sn'] = 'collection'; }
		if (!in_array($options['sn'], array('collection', 'items'))) { $options['sn'] = 'collection'; }
		$options['so'] = (isset($_GET['so'])) ? trim($_GET['so']) : 'asc';
		if ($options['so'] != 'desc') { $options['so'] = 'asc'; }

		$rows = $this->model->getCollections();
		$options['total'] = count($rows);

		if ($options['total'] > 1) {
			$rows = $this->sortCollections($rows, $options['sn'], $options['so']);
			$limitstart = 0;
			$options['maxpage'] = ceil($options['total']/$options['limit']);
			if ($options['maxpage'] < 1) { $options['maxpage'] = 1; }
			if ($options['page'] > $options['maxpage']) { $options['page'] = $options['maxpage']; }
			$options['limitstart'] = (($options['page'] - 1) * $options['limit']);

			if ($options['total'] > $options['limit']) {
				$limitrows = array();
				$end = $options['limitstart'] + $options['limit'];
				foreach ($rows as $key => $row) {
					if ($key < $options['limitstart']) { continue; }
					if ($key >= $end) { break; }
					$limitrows[] = $row;
				}
				$rows = $limitrows;
			}
		}

		$pathway->deleteAllNodes();
		$pathway->addNode($eLang->get('MENU_MANAGER'), 'emenu:/');
		$pathway->addNode($eLang->get('MENU_ITEM_COLLECTIONS'));

		$eDoc->setTitle($eLang->get('MENU_ITEM_COLLECTIONS').' - '.$elxis->getConfig('SITENAME'));
		if ($rows) {
			$eDoc->addDocReady('elx5DataTable(\'collectionstbl\', false);');
		}
		$eDoc->addScriptLink($elxis->secureBase().'/components/com_emenu/js/emenu.js');

		$this->view->listcollections($rows, $options, $eLang, $elxis);
	}


	/*********************/
	/* ORDER COLLECTIONS */
	/*********************/
	private function sortCollections($rows, $sortname, $sortorder) {
		if ($sortname == 'items') {
			$sortmethod = ($sortorder == 'asc') ? 'sortCollectionsitemsAsc' : 'sortCollectionsitemsDesc';
		} else {//collection
			$sortmethod = ($sortorder == 'asc') ? 'sortCollectionscolAsc' : 'sortCollectionscolDesc';
		}
		usort($rows, array($this, $sortmethod));

		return $rows;
    }


	public function sortCollectionsitemsDesc($a, $b) {
		if ($a->items == $b->items) { return 0; }
		return ($a->items < $b->items ? 1 : -1);
	}

	public function sortCollectionsitemsAsc($a, $b) {
		if ($a->items == $b->items) { return 0; }
		return ($a->items > $b->items ? 1 : -1);
	}

	public function sortCollectionscolDesc($a, $b) {
		if ($a->collection == $b->collection) { return 0; }
		return strcasecmp($b->collection, $a->collection);
	}

	public function sortCollectionscolAsc($a, $b) {
		if ($a->collection == $b->collection) { return 0; }
		return strcasecmp($a->collection, $b->collection);
	}


	/**************************/
	/* SAVE COLLECTION (AJAX) */
	/**************************/
	public function savecollection() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$response = array('success' => 0, 'message' => '');

		if ($elxis->acl()->check('com_emenu', 'menu', 'add') < 1) {
			$response['message'] = $eLang->get('NOTALLOWACCPAGE');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$pat = "#([\']|[\"]|[\$]|[\#]|[\<]|[\>]|[\*]|[\%]|[\~]|[\`]|[\^]|[\|]|[\\\])#u";
		$collection = trim(filter_input(INPUT_POST, 'collection', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$collection = trim(preg_replace('/[^A-Za-z0-9]/', '', $collection));
		$modtitle = trim(filter_input(INPUT_POST, 'collection', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		$modtitle = eUTF::trim(preg_replace($pat, '', $modtitle));

		if ($collection != $_POST['collection']) {
			$errormsg = sprintf($eLang->get('FIELDNOACCCHAR'), $eLang->get('COLLECTION'));
		} else if ($collection == '') {
			$response['message'] = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('COLLECTION'));
		} else if ($modtitle == '') {
			$response['message'] = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('MODULE_TITLE'));
		} else {
			$collection = strtolower($collection);
			$result = $this->model->saveCollection($collection, $modtitle);
			if ($result['success'] === false) {
				$response['message'] = $result['message'];
			} else {
				$response['success'] = 1;
			}
		}

		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/****************************/
	/* DELETE A MENU COLLECTION */
	/****************************/
	public function deletecollection() {
		$response = array('success' => 0, 'message' => '');

		if (!isset($_POST['elids'])) {
			$response['message'] = 'No collection set!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$collection = trim(preg_replace('/[^a-z0-9]/', '', $_POST['elids']));
		if (($collection == '') || ($collection != $_POST['elids'])) {
			$response['message'] = 'Invalid collection!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		if ($collection == 'mainmenu') {
			$response['message'] = eFactory::getLang()->get('CNOT_DELETE_MAINMENU');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$result = $this->model->deleteCollection($collection); //includes acl check
		if ($result['success'] === false) {
			$response['message'] = $result['message'];
		} else {
			$response['success'] = 1;
		}

		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}

}
	
?>