<?php 
/**
* @version		$Id: afpage.php 2127 2019-03-03 18:53:41Z IOS $
* @package		Elxis
* @subpackage	Component Content
* @copyright	Copyright (c) 2006-2019 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class afpageContentController extends contentController {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null, $model=null, $format='') {
		parent::__construct($view, $model, $format);
	}


	/*******************************/
	/* PREPARE TO DESIGN FRONTPAGE */
	/*******************************/
	public function design() {
		$elxis = eFactory::getElxis();
		$eDoc = eFactory::getDocument();
		$pathway = eFactory::getPathway();
		$eLang = eFactory::getLang();

		if ($elxis->acl()->check('com_content', 'frontpage', 'edit') < 1) {
			$link = $elxis->makeAURL('cpanel:/');
			$elxis->redirect($link, $eLang->get('NOTALLOWACTION'), true);
		}

		$eDoc->addStyleLink($elxis->secureBase().'/components/com_content/css/layout.css');
		$eDoc->addJQuery();
		$eDoc->addFontAwesome();
		$eDoc->addLibrary('iqueryui', $elxis->secureBase().'/components/com_content/js/jquery-ui.min.js', '1.9.1');
		$eDoc->addScriptLink($elxis->secureBase().'/components/com_content/js/layout.js');

		$type = 'positions';
		$rows = $this->model->getFrontpage();
		$rowsorder = '2,4x5,6x7,8x9,10,11x12x13,14,15x16,17';
		if ($rows) {
			foreach ($rows as $row) {
				if ($row['pname'] == 'type') { $type = $row['pval']; }
				if ($row['pname'] == 'rowsorder') { $rowsorder = $row['pval']; }
			}
		}
		if ($rowsorder == '') { $rowsorder = '2,4x5,6x7,8x9,10,11x12x13,14,15x16,17'; }

		$parts = explode(',', $rowsorder);
		$i = 1;
		$ordering = array();
		foreach ($parts as $part) {
			$ordering[$part] = $i;
			$i++;
		}

		$switch_type = false;
		$newtype = trim(filter_input(INPUT_GET, 'type', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		if (($newtype != '') && in_array($newtype, array('positions', 'modules'))) {
			if ($type != $newtype) { $switch_type = true; }
			$type = $newtype;
		}

		if ($type == 'positions') {
			$items = $this->model->getTplPositions();
			if ($items) {
				usort($items, array('afpageContentController', 'orderPositions'));
			}
		} else {
			$items = $this->model->getFrontModules();
		}

		$layout = $this->getLayout($items, $rows, $type, $switch_type);
		unset($rows, $switch_type);

		$pathway->deleteAllNodes();
		$pathway->addNode($eLang->get('FRONTPAGE_DESIGNER'));
		$eDoc->setTitle($eLang->get('FRONTPAGE_DESIGNER'));

		$this->view->design($layout, $items, $type, $ordering);
	}


	/***************************************************************/
	/* RE-ORDER POSITION BASED ON THEIR NUMBER OF ASSIGNED MODULES */
	/***************************************************************/
	public static function orderPositions($a, $b) {
		if ($a->modules == $b->modules) { return 0; }
		return ($a->modules < $b->modules) ? 1 : -1;
	}


	/**********************/
	/* GET CURRENT LAYOUT */
	/**********************/
	private function getLayout($items, $rows, $type, $switch_type) {
		$layout = new stdClass;
		$layout->wl = 20;
		$layout->wc = 60;
		$layout->wr = 20;
		$layout->type = $type;
		$layout->reswidth = 650;
		$layout->items = array();
		for ($i=1; $i<18; $i++) {
			$property = 'c'.$i;
			$property2 = 'resbox'.$i;
			$layout->$property = array();
			$layout->$property2 = 1;
		}

		if ($rows) {
			foreach ($rows as $row) {
				$pname = $row['pname'];
				switch ($pname) {
					case 'wl': case 'wc': case 'wr': case 'reswidth': case 'resbox1': case 'resbox2': case 'resbox3': case 'resbox4': case 'resbox5': case 'resbox6': case 'resbox7': case 'resbox8': 
					case 'resbox9': case 'resbox10': case 'resbox11': case 'resbox12': case 'resbox13': case 'resbox14': case 'resbox15': case 'resbox16': case 'resbox17': 
						$layout->$pname = (int)$row['pval'];
					break;
					case 'type': $layout->type = $type; break;
					default:
						$pval = trim($row['pval']);
						if (!$switch_type && ($pval != '')) {
							$cellitems = explode(',', $pval);
							$final = array();
							if ($cellitems && $items) {
								foreach ($items as $item) {
									if ($type == 'positions') {
										if (in_array($item->position, $cellitems)) {
											$final[] = $item->position;
											$layout->items[] = $item->position;
										}
									} else {
										$mod_withid = $item->module.':'.$item->id;
										if (in_array($mod_withid, $cellitems)) {
											$final[] = $mod_withid;
											$layout->items[] = $mod_withid;
										}
									}
								}
							}
							$layout->$pname = $final;
						}
					break;
				}
			}
		}

		return $layout;
	}


	/****************************************/
	/* GET LAYOUT DATA FROM USER SUBMISSION */
	/****************************************/
	private function getUserLayout() {
		$userLayout = array();
		$userLayout['wl'] = (isset($_POST['wl'])) ? (int)$_POST['wl'] : 0;
		$userLayout['wc'] = (isset($_POST['wc'])) ? (int)$_POST['wc'] : 0;
		$userLayout['wr'] = (isset($_POST['wr'])) ? (int)$_POST['wr'] : 0;
		$userLayout['type'] = (isset($_POST['type'])) ? trim($_POST['type']) : 'positions';
		$userLayout['reswidth'] = (isset($_POST['reswidth'])) ? (int)$_POST['reswidth'] : 650;
		$userLayout['rowsorder'] = (isset($_POST['rowsorder'])) ? trim($_POST['rowsorder']) : '';
		if ($userLayout['rowsorder'] == '') { $userLayout['rowsorder'] = '2,4x5,6x7,8x9,10,11x12x13,14,15x16,17'; }

		for ($i=1; $i<18; $i++) {
			$pname = 'c'.$i;
			$pname2 = 'resbox'.$i;
			$pval = trim(filter_input(INPUT_POST, $pname, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
			$userLayout[$pname] = ($pval != '') ? explode(',', $pval) : array();
			$userLayout[$pname2] = (isset($_POST[$pname2])) ? (int)$_POST[$pname2] : 1;
		}
		return $userLayout;
	}


	/***************/
	/* SAVE LAYOUT */
	/***************/
	public function savelayout() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$this->ajaxHeaders('text/plain');
		if ($elxis->acl()->check('com_content', 'frontpage', 'edit') < 1) {
			echo '0|'.addslashes($eLang->get('NOTALLOWACTION'));
			exit;
		}

		$userLayout = $this->getUserLayout();

		if ($userLayout['wl'] + $userLayout['wc'] + $userLayout['wr'] != 100) {
			echo '0|'.addslashes($eLang->get('WIDTHS_SUM_100'));
			exit;
		}

		if ($userLayout['wl'] == 0) { $userLayout['c1'] = array(); }
		if ($userLayout['wc'] == 0) {
			$userLayout['c2'] = array();
			$userLayout['c4'] = array();
			$userLayout['c5'] = array();
			$userLayout['c6'] = array();
			$userLayout['c7'] = array();
			$userLayout['c8'] = array();
			$userLayout['c9'] = array();
			$userLayout['c10'] = array();
			$userLayout['c11'] = array();
			$userLayout['c12'] = array();
			$userLayout['c13'] = array();
			$userLayout['c14'] = array();
			$userLayout['c15'] = array();
			$userLayout['c16'] = array();
			$userLayout['c17'] = array();
		}
		if ($userLayout['wr'] == 0) { $userLayout['c3'] = array(); }

		if ($userLayout['reswidth'] < 300) { $userLayout['reswidth'] = 650; }

		$allowed_items = array();
		if ($userLayout['type'] == 'positions') {
			$positions = $this->model->getTplPositions();
			if ($positions) {
				foreach ($positions as $pos) {
					if ($pos->position == 'hidden') { continue; }
					if ($pos->position == 'tools') { continue; }
					if ($pos->position == 'menu') { continue; }
					if (strpos($pos->position, 'category') === 0) { continue; }
					$allowed_items[] = $pos->position;
				}
			}
			unset($positions);
		} else {
			$modules = $this->model->getFrontModules();
			if ($modules) {
				foreach ($modules as $mod) {
					$mod_withid = $mod->module.':'.$mod->id;
					$allowed_items[] = $mod_withid;
				}
			}
			unset($modules);
		}

		for ($i=1; $i < 18; $i++) {
			$pname = 'c'.$i;
			if (count($userLayout[$pname]) > 0) {
				$final = array();
				foreach ($userLayout[$pname] as $item) {
					if (in_array($item, $allowed_items)) { $final[] = $item; }
				}
				$userLayout[$pname] = implode(',', $final);
			} else {
				$userLayout[$pname] = '';
			}
		}

		$dbrows = $this->model->getFrontpage();

		$intcols = array('wl', 'wc', 'wr', 'reswidth', 'resbox1', 'resbox2', 'resbox3', 'resbox4', 'resbox5', 'resbox6', 'resbox7', 
		'resbox8', 'resbox9', 'resbox10', 'resbox11', 'resbox12', 'resbox13', 'resbox14', 'resbox15', 'resbox16', 'resbox17');
		$rows = array();
		foreach ($userLayout as $pname => $pval) {
			$is_int = (in_array($pname, $intcols)) ? true : false;
			$row = new stdClass;
			$row->id = 0;
			$row->pname = $pname;
			$row->pval = $is_int ? (int)$pval : $pval;
			$row->is_int = $is_int;
			$rows[$pname] = $row;
			unset($row);
		}
		unset($userLayout);

		if ($dbrows) {
			foreach ($dbrows as $dbrow) {
				$pname = $dbrow['pname'];
				if (isset($rows[$pname])) {
					$rows[$pname]->id = (int)$dbrow['id'];
				}
			}
		}
		unset($dbrows);

		$this->model->saveFrontpage($rows);

		echo '1|'.$eLang->get('ITEM_SAVED');
		exit;	
	}

}

?>