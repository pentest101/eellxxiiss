<?php 
/**
* @version		$Id: single.php 1503 2014-05-04 19:10:01Z sannosi $
* @package		Elxis
* @subpackage	Component Translator
* @copyright	Copyright (c) 2006-2018 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class singleEtranslatorController extends etranslatorController {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null, $task='', $model=null) {
		parent::__construct($view, $task, $model);
	}


	/***********************************/
	/* PREPARE TO DISPLAY TRANSLATIONS */
	/***********************************/
	public function listtrans() {
		$eLang = eFactory::getLang();
		$pathway = eFactory::getPathway();
		$eDoc = eFactory::getDocument();
		$elxis = eFactory::getElxis();

		$options = array(
			'limit' => 50, 'page' => 1, 'maxpage' => 1, 'sn' => 'category', 'so' => 'asc', 'limitstart' => 0, 'total' => 0, 
			'category' => '', 'element' => '', 'language' => '', 'elid' => 0, 
		);

		$options['limit'] = (isset($_GET['limit'])) ? (int)$_GET['limit'] : 20;
		if ($options['limit'] < 1) { $options['limit'] = 20; }
		$elxis->updateCookie('limit', $options['limit']);

		$options['page'] = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
		if ($options['page'] < 1) { $options['page'] = 1; }
		$options['sn'] = (isset($_GET['sn'])) ? trim($_GET['sn']) : 'category';
		if ($options['sn'] == '') { $options['sn'] = 'category'; }
		if (!in_array($options['sn'], array('category', 'element', 'elid', 'language', 'translation'))) { $options['sn'] = 'category'; }
		$options['so'] = (isset($_GET['so'])) ? trim($_GET['so']) : 'asc';
		if ($options['so'] != 'desc') { $options['so'] = 'asc'; }

		$pat = '#[^a-zA-Z0-9\_\-]#';
		$options['category'] = filter_input(INPUT_GET, 'category', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		$options['category'] = preg_replace($pat, '', $options['category']);
		$options['element'] = filter_input(INPUT_GET, 'element', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		$options['element'] = preg_replace($pat, '', $options['element']);
		$options['language'] = filter_input(INPUT_GET, 'language', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		$options['language'] = preg_replace($pat, '', $options['language']);
		if ($options['language'] != '') {
			if (!file_exists(ELXIS_PATH.'/language/'.$options['language'].'/'.$options['language'].'.php')) { $options['language'] = ''; }
		}
		$options['elid'] = isset($_GET['elid']) ? (int)$_GET['elid'] : 0;

		$options['total'] = $this->model->countTranslations($options);

		$rows = array();
		$options['maxpage'] = ceil($options['total']/$options['limit']);
		if ($options['maxpage'] < 1) { $options['maxpage'] = 1; }
		if ($options['page'] > $options['maxpage']) { $options['page'] = $options['maxpage']; }
		$options['limitstart'] = (($options['page'] - 1) * $options['limit']);
		if ($options['total'] >0) {
			$rows = $this->model->getTranslations($options, true);
		}

		$trcats = $this->model->getTransCategories();
		$trelements = array (
			'title' => $eLang->get('TITLE'),
			'subtitle' => $eLang->get('SUBTITLE'),
			'introtext' => $eLang->get('INTROTEXT'),
			'maintext' => $eLang->get('MAINTEXT'),
			'caption' => $eLang->get('CAPTION'),
			'sitename' => $eLang->get('SITENAME'),
			'metadesc' => $eLang->get('METADESC'),
			'metakeys' => $eLang->get('METAKEYS'),
			'category_title' => $eLang->get('CATEGORY_TITLE'),
			'category_description' => $eLang->get('CATEGORY_DESCRIPTION'),			
			'content' => $eLang->get('CONTENT')
		);

        $eDoc->addScriptLink($elxis->secureBase().'/components/com_etranslator/includes/etranslator.js');

		$pathway->addNode($eLang->get('TRANSLATOR'), 'etranslator:/');
		$eDoc->setTitle($eLang->get('TRANSLATOR').' - '.$eLang->get('ADMINISTRATION'));
		if ($rows) {
			$eDoc->addDocReady('elx5DataTable(\'translationstbl\', false);');
		}

		$this->view->listTrans($rows, $options, $trcats, $trelements, $elxis, $eLang);
	}


	/***********************************************/
	/* GET TRANSLATION DATA (TRANSLATION ADD/EDIT) */
	/***********************************************/
	public function gettransdata() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$response = array(
			'success' => 0,
			'message' => '',
			'trid' => 0,
			'elid' => 0,
			'category' => '',
			'element' => '',
			'originaltext' => '',
			'longtext' => 0,
			'translation' => '',
			'translang' => '',
			'curtranslangs' => ''
		);

		$is_new = isset($_POST['new']) ? (int)$_POST['new'] : 1;
		$trid = isset($_POST['trid']) ? (int)$_POST['trid'] : 0;
		if ($trid < 1) {
			$response['message'] = $eLang->get('TRANS_NOT_FOUND');
		} else {
			$row = new translationsDbTable();
			if (!$row->load($trid)) {
				$response['message'] = $eLang->get('TRANS_NOT_FOUND');
			}
		}
		if ($response['message'] != '') {
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		if (preg_match('/^(com\_)/i', $row->category)) {
			$component = strtolower($row->category);
			if ($elxis->acl()->check('component', $component, 'manage') < 1) {
				$response['message'] = 'You are not allowed to manage component '.$component;
			}
		}
		if ($row->category == 'module') {
			if (preg_match('/^(mod\_)/i', $row->element)) {
				$module = strtolower($row->element);
				if ($elxis->acl()->check('module', $module, 'manage', $row->elid) < 1) {
					$response['message'] = 'You are not allowed to edit module '.$module.' with instance '.$elid.'!';
				}
			}
		}
		if ($row->category == 'config') {
			if ($elxis->acl()->check('com_cpanel', 'settings', 'edit') < 1) {
				$response['message'] = 'You are not allowed to edit Elxis configuration options!';
			}
		}
		if ($response['message'] != '') {
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$original = $this->loadOriginal($row->category, $row->element, $row->elid);

		if ($is_new == 1) {
			$response['trid'] = 0;
			$response['translation'] = $original->text;
		} else {
			$response['trid'] = $row->trid;
			$response['translation'] = $row->translation;
			$response['translang'] = $row->language;
		}
		$response['category'] = $row->category;
		$response['element'] = $row->element;
		$response['elid'] = $row->elid;

		if ($original->longtext) {
			$response['longtext'] = 1;
			$response['originaltext'] = '<span style="color:#888888; font-style:italic;">'.$eLang->get('LONG_TEXT').'</span>';
		} else {
			$response['longtext'] = 0;
			$response['originaltext'] = ($original->text == '') ? $eLang->get('NOT_AVAILABLE') : $original->text;
		}

		if ($is_new) {
			$ctlangs = $this->model->getCTLangs($row->category, $row->element, $row->elid);
			if (!$ctlangs) { $ctlangs = array(); }
			$ctlangs[] = $elxis->getConfig('LANG');
			$response['curtranslangs'] = $ctlangs ? implode(',', $ctlangs) : '';
			unset($ctlangs);
		}

		$response['success'] = 1;

		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/***********************************************/
	/* LOAD ORIGINAL TEXT FROM TRANSLATION ELEMENT */
	/***********************************************/
	private function loadOriginal($category, $element, $elid) {
		$original = new stdClass;
		$original->table = '';
		$original->id_column = '';
		$original->text_column = '';
		$original->text = '';
		$original->longtext = false;

		if ($category == 'config') {
			$key = strtoupper($element);
			if (in_array($key, array('SITENAME', 'METADESC', 'METAKEYS'))) {
				$original->text = eFactory::getElxis()->getConfig($key);
			}
			return $original;
		} else if ($category == 'com_content') {
			switch ($element) {
				case 'category_title': 
					$original->table = '#__categories'; $original->id_column = 'catid'; $original->text_column = 'title';
				break;
				case 'category_description':
					$original->table = '#__categories'; $original->id_column = 'catid'; $original->text_column = 'description'; $original->longtext = true;
				break;
				case 'title':
					$original->table = '#__content'; $original->id_column = 'id'; $original->text_column = 'title';
				break;
				case 'subtitle':
					$original->table = '#__content'; $original->id_column = 'id'; $original->text_column = 'subtitle';
				break;
				case 'introtext':
					$original->table = '#__content'; $original->id_column = 'id'; $original->text_column = 'introtext'; $original->longtext = true;
				break;
				case 'maintext':
					$original->table = '#__content'; $original->id_column = 'id'; $original->text_column = 'maintext'; $original->longtext = true;
				break;
				case 'caption':
					$original->table = '#__content'; $original->id_column = 'id'; $original->text_column = 'caption';
				break;
				case 'metakeys':
					$original->table = '#__content'; $original->id_column = 'id'; $original->text_column = 'metakeys';
				break;
				default: break;
			}
		} else if ($category == 'com_emenu') {
			if ($element == 'title') {
				$original->table = '#__menu';
				$original->id_column = 'menu_id';
				$original->text_column = 'title';
			}
		} else if ($category == 'module') {
			if ($element == 'title') {
				$original->table = '#__modules';
				$original->id_column = 'id';
				$original->text_column = 'title';
			} else 	if ($element == 'content') {
				$original->table = '#__modules';
				$original->id_column = 'id';
				$original->text_column = 'content';
				$original->longtext = true;
			}
		} else if ($category == 'com_reservations') {
			switch ($element) {
				case 'hotdesc':
					$original->table = '#__res_hotels'; $original->id_column = 'hid'; $original->text_column = 'description'; $original->longtext = true;
				break;
				case 'hotterms': 
					$original->table = '#__res_hotels'; $original->id_column = 'hid'; $original->text_column = 'terms'; $original->longtext = true;
				break;
				case 'hottitle': 
					$original->table = '#__res_hotels'; $original->id_column = 'hid'; $original->text_column = 'title';
				break;
				case 'roomtitle': 
					$original->table = '#__res_rooms'; $original->id_column = 'rid'; $original->text_column = 'title';
				break;
				case 'roomdesc': 
					$original->table = '#__res_rooms'; $original->id_column = 'rid'; $original->text_column = 'description'; $original->longtext = true;
				break;
				case 'loctitle':
					$original->table = '#__res_locations'; $original->id_column = 'lid'; $original->text_column = 'title';
				break;
				case 'locdescription': 
					$original->table = '#__res_locations'; $original->id_column = 'lid'; $original->text_column = 'description'; $original->longtext = true;
				break;
				case 'servhotel':
					$original->table = '#__res_addonservices'; $original->id_column = 'asid'; $original->text_column = 'title';
				break;
				case 'acctitle':
					$original->table = '#__res_accommodation'; $original->id_column = 'accid'; $original->text_column = 'title';
				break;
				case 'placetitle':
					$original->table = '#__res_places'; $original->id_column = 'plid'; $original->text_column = 'title';
				break;
				case 'placeaddress':
					$original->table = '#__res_places'; $original->id_column = 'plid'; $original->text_column = 'address';
				break;
				case 'placeaddress2':
					$original->table = '#__res_places'; $original->id_column = 'plid'; $original->text_column = 'address2';
				break;
				case 'cartitle':
					$original->table = '#__res_cars'; $original->id_column = 'carid'; $original->text_column = 'title';
				break;
				case 'cardetails':
					$original->table = '#__res_cars'; $original->id_column = 'carid'; $original->text_column = 'details';
				break;
				case 'carextratitle':
					$original->table = '#__res_carextras'; $original->id_column = 'exid'; $original->text_column = 'title';
				break;
				case 'grouptitle':
					$original->table = '#__res_rt_groups'; $original->id_column = 'gid'; $original->text_column = 'title';
				break;
				case 'modelribbon':
					$original->table = '#__res_rt_models'; $original->id_column = 'mid'; $original->text_column = 'ribbon';
				break;
				case 'grouptitle':
					$original->table = '#__res_rt_groups'; $original->id_column = 'gid'; $original->text_column = 'title';
				break;
				case 'rtloctitle':
					$original->table = '#__res_rt_locations'; $original->id_column = 'lid'; $original->text_column = 'title';
				break;
				case 'rtextratitle':
					$original->table = '#__res_rt_extras'; $original->id_column = 'xid'; $original->text_column = 'title';
				break;
				case 'rtextradesc':
					$original->table = '#__res_rt_extras'; $original->id_column = 'xid'; $original->text_column = 'description'; $original->longtext = true;
				break;
				case 'areatitle':
					$original->table = '#__res_rt_areas'; $original->id_column = 'aid'; $original->text_column = 'title';
				break;
				case 'rtpaytitle':
					$original->table = '#__res_rt_paymethods'; $original->id_column = 'pid'; $original->text_column = 'title';
				break;
				case 'rtpaydesc':
					$original->table = '#__res_rt_paymethods'; $original->id_column = 'pid'; $original->text_column = 'description'; $original->longtext = true;
				break;
				case 'rtprinctitle':
					$original->table = '#__res_rt_priceincludes'; $original->id_column = 'piid'; $original->text_column = 'title';
				break;
				case 'rtpaydesc':
					$original->table = '#__res_rt_priceincludes'; $original->id_column = 'piid'; $original->text_column = 'description';
				break;
				default: break;
			}
		} else if ($category == 'com_mikro') {
			switch ($element) {
				case 'cattitle':
					$original->table = '#__mikro_categories'; $original->id_column = 'catid'; $original->text_column = 'title';
				break;
				case 'catdesc':
					$original->table = '#__mikro_categories'; $original->id_column = 'catid'; $original->text_column = 'description';
				break;
				default: break;
			}

		} else if ($category == 'com_shop') {
			switch ($element) {
				case 'category_title':
					$original->table = '#__shop_categories'; $original->id_column = 'cid'; $original->text_column = 'title';
				break;
				case 'extratitle':
					$original->table = '#__shop_products_extra'; $original->id_column = 'extraid'; $original->text_column = 'title';
				break;
				case 'producttitle':
					$original->table = '#__shop_products'; $original->id_column = 'id'; $original->text_column = 'title';
				break;
				case 'prodbriefdescr':
					$original->table = '#__shop_products'; $original->id_column = 'id'; $original->text_column = 'briefdesc'; $original->longtext = true;
				break;
				case 'shiptitle':
					$original->table = '#__shop_shipping'; $original->id_column = 'shid'; $original->text_column = 'title';
				break;
				case 'shipdescription':
					$original->table = '#__shop_shipping'; $original->id_column = 'shid'; $original->text_column = 'description'; $original->longtext = true;
				break;
				case 'typetitle':
					$original->table = '#__shop_product_types'; $original->id_column = 'ptid'; $original->text_column = 'title';
				break;
				case 'paytitle':
					$original->table = '#__shop_payment'; $original->id_column = 'pid'; $original->text_column = 'title';
				break;
				case 'paydescription':
					$original->table = '#__shop_payment'; $original->id_column = 'pid'; $original->text_column = 'description'; $original->longtext = true;
				break;
				default: break;
			}
		} else {
			//do nothing
		}

		if ($original->text_column == '') { //try to guess if it is long text
			if (strpos($element, 'description') !== false) {
				$original->longtext = true;
			} elseif (strpos($element, 'text') !== false) {
				$original->longtext = true;
			}

			return $original;
		}

		$original->text = $this->model->getOriginal($original->table, $original->id_column, $original->text_column, $elid);

		return $original;
	}


	/********************************************************/
	/* DELETE TRANSLATION (THIS IS DIFFERENT FROM THE API!) */
	/********************************************************/
	public function deletetrans() {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();

		$response = array('success' => 0, 'message' => '');

		if (!isset($_POST['elids'])) {
			$response['message'] = 'No item set!';
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$trid = (isset($_POST['elids'])) ? (int)$_POST['elids'] : 0;
		if ($trid < 1) {
			$response['success'] = 1;
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$row = new translationsDbTable();
		if (!$row->load($trid)) {
			$response['message'] = $eLang->get('TRANS_NOT_FOUND');
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		if (preg_match('/^(com\_)/i', $row->category)) {
			$component = strtolower($row->category);
			if ($elxis->acl()->check('component', $component, 'manage') < 1) {
				$response['message'] = 'You are not allowed to manage component '.$component;
			}
		}

		if ($row->category == 'module') {
			if (preg_match('/^(mod\_)/i', $row->element)) {
				$module = strtolower($row->element);
				if ($elxis->acl()->check('module', $module, 'manage', $row->elid) < 1) {
					$response['message'] = 'You are not allowed to edit '.$module.' with instance '.$row->elid.'!';
					return;
				}
			}
		}

		if ($row->category == 'config') {
			if ($elxis->acl()->check('com_cpanel', 'settings', 'edit') < 1) {
				$response['message'] = 'You are not allowed to edit Elxis configuration options!';
				return;
			}
		}

		if ($response['message'] != '') {
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		if (!$row->delete()) {
			$response['message'] = addslashes($eLang->get('ACTION_FAILED'));
		} else {
			$response['success'] = 1;
		}

		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/*******************************************/
	/* ADD/EDIT ALL ITEM'S STRING TRANSLATIONS */
	/*******************************************/
	public function editalltrans() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eDoc = eFactory::getDocument();

		$pat = '#[^a-zA-Z0-9\_\-]#';
		$options = array();
		$options['category'] = filter_input(INPUT_GET, 'category', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH); //com_reservations
		$options['category'] = preg_replace($pat, '', $options['category']);
		$options['element'] = filter_input(INPUT_GET, 'element', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH); //servhotel
		$options['element'] = preg_replace($pat, '', $options['element']);
		$options['tbl'] = filter_input(INPUT_GET, 'tbl', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH); //res_addonservices
		$options['tbl'] = preg_replace($pat, '', $options['tbl']);
		$options['col'] = filter_input(INPUT_GET, 'col', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);//title
		$options['col'] = preg_replace($pat, '', $options['col']);
		$options['idcol'] = filter_input(INPUT_GET, 'idcol', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);//asid
		$options['idcol'] = preg_replace($pat, '', $options['idcol']);
		$options['id'] = isset($_GET['id']) ? (int)$_GET['id'] : 0;

		if (($options['category'] == '') || ($options['element'] == '') || ($options['tbl'] == '') || ($options['col'] == '') || ($options['idcol'] == '') || ($options['id'] < 1)) {
			echo '<div class="elx5_error">Invalid request!</div>';
			return;
		}
		$options['tbl'] = $elxis->getConfig('DB_PREFIX').$options['tbl'];

		$original = $this->model->getOriginal($options['tbl'], $options['idcol'], $options['col'], $options['id']);
		if (trim($original) == '') {
			echo '<div class="elx5_error">Original element not found!</div>';
			return;
		}

		$dboptions = array(
			'category' => $options['category'],
			'element' => $options['element'],
			'elid' => $options['id'],
			'sn' => 'trid',
			'so' => 'ASC',
			'limitstart' => 0,
			'limit' => 100
		);
		$translations = $this->model->getTranslations($dboptions, false);
		unset($dboptions);

		$eDoc->setTitle($eLang->get('TRANS_MANAGEMENT'));
        $eDoc->addScriptLink($elxis->secureBase().'/components/com_etranslator/includes/etranslator.js');

		$this->view->editAllTranslations($options, $original, $translations);
	}

}
	
?>