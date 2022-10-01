<?php 
/**
* @version		$Id: menu.db.php 2431 2022-01-18 19:26:35Z IOS $
* @package		Elxis
* @subpackage	Database
* @copyright	Copyright (c) 2006-2022 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


class menuDbTable extends elxisDbTable {


	/*************************************************/
	/* CONSTRUCT PARENT CLASS AND SET INITIAL VALUES */
	/*************************************************/
	public function __construct() {
		parent::__construct('#__menu', 'menu_id');
		$this->columns = array(
			'menu_id' => array('type' => 'integer', 'value' => null),
			'title' => array('type' => 'string', 'value' => null),
			'section' => array('type' => 'string', 'value' => 'frontend'),
			'collection' => array('type' => 'string', 'value' => null),
			'menu_type' => array('type' => 'string', 'value' => 'link'),
			'link' => array('type' => 'string', 'value' => null),
			'file' => array('type' => 'string', 'value' => null),
			'popup' => array('type' => 'integer', 'value' => 0),
			'secure' => array('type' => 'bit', 'value' => 0),
			'published' => array('type' => 'bit', 'value' => 0),
			'parent_id' => array('type' => 'integer', 'value' => 0),
			'ordering' => array('type' => 'integer', 'value' => 0),
			'expand' => array('type' => 'integer', 'value' => 0),
			'target' => array('type' => 'string', 'value' => null),
			'alevel' => array('type' => 'integer', 'value' => 0),
			'width' => array('type' => 'integer', 'value' => 0),
			'height' => array('type' => 'integer', 'value' => 0),
			'iconfont' => array('type' => 'string', 'value' => null)
		);
	}


	/**********************/
	/* CHECK ROW VALIDITY */
	/**********************/
	public function check() {
		$this->title = eUTF::trim($this->title);
		if ($this->title == '') {
			$eLang = eFactory::getLang();
			$this->errorMsg = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('TITLE')); 
			return false;
		}
		if (trim($this->collection) == '') {
			$this->errorMsg = 'Collection can not be empty!'; 
			return false;
		}
		if ($this->section != 'backend') { $this->section = 'frontend'; }
		if (($this->menu_type == '') || !in_array($this->menu_type, array('link', 'wrapper', 'separator', 'url', 'onclick'))) {
			$this->errorMsg = 'Invalid menu type!';
			return false;
		}
		if (trim($this->file) == '') {
			$this->file == null;
		} else if ($this->file != 'inner.php') {
			$this->file = 'index.php';
		}
		if (($this->menu_type == 'url') || ($this->menu_type == 'separator') || ($this->menu_type == 'onclick')) { $this->file = null; }
		$this->expand = (int)$this->expand;
		if ($this->menu_type != 'link') { $this->expand = 0; }
		$this->secure = (int)$this->secure;
		if (($this->menu_type != 'link') && ($this->menu_type != 'wrapper')) { $this->secure = 0; }
		$this->popup = (int)$this->popup;
		if ($this->popup < 0) { $this->popup = 0; }
		$this->published = (int)$this->published;
		if ($this->published !== 1) { $this->published = 0; }
		$this->width = (int)$this->width;
		if ($this->width < 0) { $this->width = 0; }
		$this->height = (int)$this->height;
		if ($this->height < 0) { $this->height = 0; }
		$this->alevel = (int)$this->alevel;
		if ($this->alevel < 0) { $this->alevel = 0; }
		if ($this->alevel > 100000) { $this->alevel = 100000; }
		$this->parent_id = (int)$this->parent_id;
		if ($this->parent_id < 0) { $this->parent_id = 0; }
		$this->ordering = (int)$this->ordering;
		if ($this->ordering < 1) { $this->ordering = 1; }
		if (!in_array($this->target, array('_self', '_blank', '_parent', '_top'))) { $this->target = null; }
		if ($this->menu_type == 'separator') {
			$this->popup = 0;
			$this->width = 0;
			$this->height = 0;
			$this->target = null;
			$this->iconfont = null;
		}
		if ($this->menu_type == 'onclick') {
			$this->popup = 0;
			$this->width = 0;
			$this->height = 0;
			$this->target = null;
		}
		$this->link = eUTF::trim($this->link);
		if (($this->menu_type == 'url') || ($this->menu_type == 'wrapper')) {
			if (!filter_var($this->link, FILTER_VALIDATE_URL)) {
				$this->errorMsg = 'Provided link URL is invalid!'; 
				return false;
			}
		}
		
		if ($this->menu_type == 'link') {
			if (($this->link == '/') || ($this->link == '')) {
				$this->link = null;
			} else {
				if (filter_var($this->link, FILTER_VALIDATE_URL)) {
					$this->errorMsg = 'Provided link is a URL and not an Elxis URI!'; 
					return false;
				}

				$parts = preg_split('#\:#', $this->link, -1, PREG_SPLIT_NO_EMPTY);
				if (strlen($parts[0]) < 3) {
					if (!file_exists(ELXIS_PATH.'/language/'.$parts[0].'/'.$parts[0].'.php')) {
						$this->errorMsg = 'Provided link is for an non-existing language!'; 
						return false;
					}
					array_shift($parts);
				}
				if ($parts) {
					$c = count($parts);
					if ($c > 2) {
						$this->errorMsg = 'Provided link is not a valid Elxis URI!'; 
						return false;
					}
					if ($c == 2) {
						if ($parts[0] != 'content') {
							if (!file_exists(ELXIS_PATH.'/components/com_'.$parts[0].'/'.$parts[0].'.php')) {
								$this->errorMsg = 'Provided link is for an non-existing component!'; 
								return false;
							}
						}
					}

					$xlink = implode('/', $parts);
					$xlink = ltrim(str_replace('//', '/', $xlink), '/');
					$xlink = 'http://www.elxis.org/'.$xlink;
					if (!filter_var($xlink, FILTER_VALIDATE_URL)) {
						$this->errorMsg = 'Provided link is not a valid Elxis URI!';
						return false;
					}
				}
			}
		}

		if ($this->menu_type == 'onclick') {
			if (trim($this->link) == '') {
				$this->errorMsg = 'Please rovide code to be executed on click!'; 
				return false;
			}
		}

		return true;
	}

}

?>