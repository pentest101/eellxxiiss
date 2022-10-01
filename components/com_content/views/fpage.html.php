<?php 
/**
* @version		$Id: fpage.html.php 2391 2021-03-28 07:34:58Z IOS $
* @package		Elxis
* @subpackage	Component Content
* @copyright	Copyright (c) 2006-2021 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class fpageContentView extends contentView {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/**********************************/
	/* GENERATE FRONTPAGE LAYOUT HTML */
	/**********************************/
	public function showFrontpage($layout) {
		$this->openWrapper();

		if ($layout->wl > 0) {
			$this->openColumn('l', $layout->resbox1);
			$this->renderBox($layout->c1, $layout->type);
			$this->closeColumn();
		}

		if ($layout->wc > 0) {
			$this->openColumn('c', 1);
			$this->renderCenter($layout);
			$this->closeColumn();
		}

		if ($layout->wr > 0) {
			$this->openColumn('r', $layout->resbox3);
			$this->renderBox($layout->c3, $layout->type);
			$this->closeColumn();
		}

		$this->closeWrapper();
	}


	/***********************/
	/* OPEN GLOBAL WRAPPER */
	/***********************/
	private function openWrapper() {
		echo '<div class="gridzero">'."\n"; 
	}


	/************************/
	/* CLOSE GLOBAL WRAPPER */
	/************************/
	private function closeWrapper() {
		echo '<div class="clear"></div>'."\n";
		echo "</div>\n";
	}


	/***************/
	/* OPEN COLUMN */
	/***************/
	private function openColumn($col, $respshow=1) {
		if ($respshow == 1) {
			echo '<div class="grid'.$col.'col">'."\n";
		} else {
			echo '<div class="grid'.$col.'colh">'."\n";
		}
	}


	/****************/
	/* CLOSE COLUMN */
	/****************/
	private function closeColumn() {
		echo "</div>\n";
	}


	/**********************/
	/* RENDER COLUMN CELL */
	/**********************/
	private function renderBox($items, $type) {
		if ($items) {
			$eDoc = eFactory::getDocument();
			foreach ($items as $item) {
				if ($type == 'positions') {
					$eDoc->modules($item);
				} else {
					$parts = explode(':', $item);
					if (count($parts) == 2) {
						$modid = (int)$parts[1];
						$eDoc->module($modid);//load single module by its ID (Elxis 4.2+)
					}
				}
			}
		} else {
			echo '&#160;';
		}
	}


	/******************************/
	/* RENDER CENTER COLUMN CELLS */
	/******************************/
	private function renderCenter($layout) {
		$something = false;

		foreach ($layout->rowsorder as $fprow) {
			$nums = explode('x', $fprow);
			$cells = array();
			foreach ($nums as $num) {
				$k = (int)$num;
				$cells[$k] = 'c'.$num;
			}

			$process = false;
			foreach ($cells as $num => $cell) {
				if ($layout->$cell) { $process = true; break; }
			}
			if (!$process) { continue; }
			$something = true;
			$total = count($cells);

			if ($total > 1) {
				echo '<div class="griddspace">'."\n";
			}
			foreach ($cells as $num => $cell) {
				$box = 'resbox'.$num;
				$css = ($layout->$box == 1) ? 'gridcell'.$num : 'gridcell'.$num.'h';
				echo '<div class="'.$css.'">'."\n";
				$this->renderBox($layout->$cell, $layout->type);
				echo "</div>\n";
			}
			if ($total > 1) {
				echo '<div class="clear">'."</div>\n";
				echo "</div>\n";
			}
		}

		if (!$something) { echo '&#160;'; }
	}

}

?>