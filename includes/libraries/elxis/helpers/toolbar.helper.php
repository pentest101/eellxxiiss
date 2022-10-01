<?php 
/**
* @version		$Id$
* @package		Elxis
* @subpackage	Helpers / Toolbar
* @copyright	Copyright (c) 2006-2019 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisToolbarHelper {

	private $buttons = array();


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
	}


	/********************/
	/* RESET EVERYTHING */
	/********************/
	public function resetAll() {
		$this->buttons = array();
	}


	/**************/
	/* SET OPTION */
	/**************/
	public function setOption($option, $value) {//Deprecated. Left for backwards compatibility
	}


	/**********************************/
	/* GENERIC METHOD TO ADD A BUTTON */
	/**********************************/
	public function add($title, $image='', $icononly=false, $link='', $onclick='', $onmouseover='', $onmouseout='', $css='') {
		$addon_class = '';
		$icon = 'fas fa-dot-circle';
		if ($image != '') {
			if (strpos($image, 'http') === 0) { //custom icon => switch to fontawesome default
				$icon = 'fas fa-dot-circle';
			} else if (strpos($image, 'fa') === 0) {//fontawesome class
				$icon = $image;
			} else {//Elxis 4.x style image icon => switch to fontawesome + addon class
				switch ($image) {
					case 'save': $icon = 'fas fa-save'; $addon_class = ' elx5_sucbtn'; break;
					case 'saveedit': $icon = 'far fa-save'; $addon_class = ' elx5_sucbtn'; break;
					case 'cancel': $icon = 'fas fa-times'; break;
					case 'add': $icon = 'fas fa-plus'; break;
					case 'delete': $icon = 'fas fa-trash'; $addon_class = ' elx5_errorbtn'; break;
					case 'error': $icon = 'fas fa-exclamation-triangle'; $addon_class = ' elx5_errorbtn'; break;
					case 'edit': $icon = 'fas fa-pen'; break;
					case 'folder': $icon = 'fas fa-folder'; break;
					case 'help': $icon = 'fas fa-question'; break;
					case 'home': $icon = 'fas fa-home'; break;
					case 'info': $icon = 'fas fa-info'; break;
					case 'lock': $icon = 'fas fa-lock'; break;
					case 'media': $icon = 'fas fa-images'; break;
					case 'menu': $icon = 'fas fa-bars'; break;
					case 'settings': $icon = 'fas fa-cog'; break;
					case 'tick': $icon = 'fas fa-check'; break;
					case 'user': $icon = 'fas fa-user'; break;
					default: $icon = 'fas fa-dot-circle'; break;
				}
			}
		}

		if (($css == '') || ($css =='elx_toolbar') || ($css =='elx5_toptoolbar_item')) {//Elxis 4.x "elx_toolbar" => 5.x "elx5_toptoolbar_item"
			$css = 'elx5_toptoolbar_item'.$addon_class;
		}

		$btn = new stdClass;
		$btn->title = $title;
		$btn->icon = $icon;
		$btn->icononly = (bool)$icononly;
		$btn->link = $link;
		$btn->onclick = trim($onclick);
		$btn->onmouseover = trim($onmouseover);
		$btn->onmouseout = trim($onmouseout);
		$btn->css = $css;
		$this->buttons[] = $btn;
	}



	/*********************************/
	/* GENERATE TOOLBAR BUTTONS HTML */
	/*********************************/
	public function getHTML() {
		if (!$this->buttons) { return ''; }

		$html = '<div class="elx5_toptoolbar">'."\n";
		foreach ($this->buttons as $button) {
			if ($button->link == '') {
				$html .= '<a href="javascript:void(null);" class="'.$button->css.'" title="'.$button->title.'"';
			} else {
				$html .= '<a href="'.$button->link.'" class="'.$button->css.'" title="'.$button->title.'"';
			}
			if ($button->onclick != '') { $html .= ' onclick="'.$button->onclick.'"'; }
			if ($button->onmouseover != '') { $html .= ' onmouseover="'.$button->onmouseover.'"'; }
			if ($button->onmouseout != '') { $html .= ' onmouseout="'.$button->onmouseout.'"'; }
			$html .= '><i class="'.$button->icon.'" aria-hidden="false"></i>';
			if (!$button->icononly) { $html .= '<span class="elx5_lmobhide"> '.$button->title.'</span>'; }
			$html .= '</a>';
		}
		$html .= '</div>';

		$this->resetAll();
		return $html;
	}

}

?>