<?php 
/**
* @version		$Id: html.helper.php 2379 2020-12-18 18:09:57Z IOS $
* @package		Elxis
* @subpackage	Helpers
* @copyright	Copyright (c) 2006-2019 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisHTMLHelper {

	private $elxis;


	/***************/
	/* CONSTRUCTOR */
	/***************/
	public function __construct() {
		$this->elxis = eFactory::getElxis();
	}


	/**************/
	/* ADD OPTION */
	/**************/
	public function makeOption($value, $text='', $disabled=false) {
		$obj = new stdClass;
		$obj->value = $value;
		$obj->text = (trim($text) != '') ? $text : $value;
		$obj->disabled = (bool)$disabled;
		return $obj;
	}


	/*************************************************/
	/* MAKE A SINGLE SELECTION DROP DOWN SELECT LIST */
	/*************************************************/
	public function selectList($arr, $name, $attribs='', $key='value', $text='text', $selected=NULL, $id=false) {
        if ($id === false) {
        	$idx = str_replace('[', '', $name);
        	$idx = str_replace(']', '', $idx);
            $html = "\n".'<select name="'.$name.'" id="'.$idx.'" '.$attribs.'>';
        } else {
            $html = "\n".'<select name="'.$name.'" id="'.$id.'" '.$attribs.'>';
        }
		if (is_array($arr)) {
			reset($arr);
		} else {
			$html .= "\n</select>";
			return $html;
		}

		foreach ($arr as $i => $option) {
			$k = $arr[$i]->$key;
			$extra = '';
			if (is_array($selected)) {
				foreach ($selected as $obj) {
					if ($k == $obj->$key) {
						$extra .= ' selected="selected"';
						break;
					}
				}
			} else {
				$extra .= ($k == $selected) ? ' selected="selected"' : '';
			}

			if ($arr[$i]->disabled == true) {
				$extra .= ' disabled="disabled"';
			}

			$k = $this->elxis->obj('filter')->ampReplace($k);
			$t = $this->elxis->obj('filter')->ampReplace($arr[$i]->$text);
			$html .= "\n\t".'<option value="'.$k.'"'.$extra.'>'.$t.'</option>';
		}

		$html .= "\n</select>\n";
		return $html;
	}


	/***************************/
	/* MAKE AN HTML RADIO LIST */
	/***************************/
	public function radioList($arr, $name, $attribs='', $key='value', $text='text', $selected=NULL, $id=false) {
		if (is_array($arr)) {
			reset($arr);
		} else {
			return '';
		}

		$attribs = trim($attribs);
		if ($attribs != '') { $attribs = ' '.$attribs; }

        if ($id === false) {
        	$idx = str_replace('[', '', $name);
        	$idx = str_replace(']', '', $idx);
        } else {
            $idx = $id;
        }

		$html = '';
		foreach ($arr as $i => $option) {
			$k = $arr[$i]->$key;
			$extra = '';
			if (is_array($selected)) {
				foreach ($selected as $obj) {
					$k2 = is_object($obj) ? $obj->$key : $obj;
					if ($k == $k2) {
						$extra .= ' checked="checked"';
						break;
					}
				}
			} else {
				$extra .= ($k == $selected) ? ' checked="checked"' : '';
			}

			$html .= "\n\t".'<input type="radio" name="'.$name.'" id="'.$idx.$i.'" value="'.$k.'"'.$extra.$attribs.' />';
			$html .= "\n\t".'<label for="'.$idx.$i.'">'.$arr[$i]->$text.'</label>';
		}
		$html .= "\n";
		return $html;
	}


	//------------------ Elxis 5.x --------------------


	/*******************/
	/* TABLE HEAD CELL */
	/*******************/
	public function tableHead($title, $class='elx5_nosorting', $attributes='') {
		$str_class = ($class != '') ? ' class="'.$class.'"' : '';
		if (trim($attributes) != '') {
			$x = trim($attributes);
			$attributes = ' '.$x;
		}
		return '<th'.$str_class.$attributes.'>'.$title."</th>\n";
	}


	/****************************/
	/* SORTABLE TABLE HEAD CELL */
	/****************************/
	public function sortableTableHead($orderlink, $title, $colname, $sortname, $sortorder, $addonclass='', $attributes='') {
		if (trim($attributes) != '') { $x = trim($attributes); $attributes = ' '.$x; }
		if (trim($addonclass) != '') { $x = trim($addonclass); $addonclass = ' '.$x; }

		if ($colname !== $sortname) {
			return '<th class="elx5_sorting'.$addonclass.'"'.$attributes.'><a href="'.$orderlink.'sn='.$colname.'&amp;so=asc">'.$title."</a></th>\n";
		}
		if ($sortorder == 'asc') {
			return '<th class="elx5_sorting_asc'.$addonclass.'"'.$attributes.'><a href="'.$orderlink.'sn='.$colname.'&amp;so=desc">'.$title."</a></th>\n";
		} else {
			return '<th class="elx5_sorting_desc'.$addonclass.'"'.$attributes.'><a href="'.$orderlink.'sn='.$colname.'&amp;so=asc">'.$title."</a></th>\n";
		}
	}


	/*********************************/
	/* AUTO-SORTABLE TABLE HEAD CELL */
	/*********************************/
	public function autoSortTableHead($title, $sortorder='', $addonclass='', $attributes='') {
		if (trim($attributes) != '') { $x = trim($attributes); $attributes = ' '.$x; }
		if (trim($addonclass) != '') { $x = trim($addonclass); $addonclass = ' '.$x; }

		if ($sortorder == 'asc') {
			$class = 'elx5_sorting_asc';
		} else if ($sortorder == 'desc') {
			$class = 'elx5_sorting_desc';
		} else {
			$class = 'elx5_sorting';
		}

		return '<th class="'.$class.$addonclass.'"'.$attributes.'><a href="javascript:void(null);">'.$title."</a></th>\n";
	}


	/******************************************/
	/* TABLE HEAD CELL CHECK/UNCHECK ALL ROWS */
	/******************************************/
	public function tableCheckAllHead($table, $sfx='') {
		$html = '<th class="elx5_nosorting elx5_center">';
		$html .= '<input type="checkbox" name="datacheckall" id="elx5_datacheckall'.$sfx.'" class="elx5_datacheck" value="1" onclick="elx5CheckTableRows(\''.$table.'\', \''.$sfx.'\');" />';
		$html .= '<label for="elx5_datacheckall'.$sfx.'" title="Check/Un-check all"></label></th>'."\n";
		return $html;
	}


	/**************/
	/* TABLE NOTE */
	/**************/
	public function tableNote($txt) {
		$html = '<div class="elx5_table_note">'.$txt."</div>\n";
		return $html;
	}


	/********************/
	/* PAGINATION LINKS */
	/********************/
	public function pagination($linkbase, $page, $maxpage) {
		$page = (int)$page;
		if ($page < 1) { $page = 1; }
		$maxpage = (int)$maxpage;
		if ($maxpage < 1) { $maxpage = 1; }
		if ($maxpage < 2) { return ''; }
		if ($page > $maxpage) { $page = $maxpage; }

		$eLang = eFactory::getLang();

		if ($maxpage < 11) {
			$first = 1;
			$last = $maxpage;
		} else {
			$first = $page - 5;
			$last = $page + 5;
			if ($first < 1) { $first = 1; $last = 11; }
			if ($last > $maxpage) { $last = $maxpage; $first = $maxpage - 10; }
		}

		$symb = (strpos($linkbase, '?') === false) ? '?' : '&amp;';

		$html = '<ul class="elx5_pagination">'."\n";
		if ($page > 1) {
			$html .= '<li><a href="'.$linkbase.$symb.'page=1" title="'.$eLang->get('PAGE').' 1">&laquo;</a></li>'."\n";
		} else {
			$html .= '<li class="elx5_pagdisabled"><a href="javascript:void(null);">&laquo;</a></li>'."\n";
		}
		for ($p = $first; $p <= $last; $p++) {
			if ($p == $page) {
				$html .= '<li class="elx5_pagactive"><a href="javascript:void(null);">'.$p.'</a></li>'."\n";
			} else {
				$html .= '<li><a href="'.$linkbase.$symb.'page='.$p.'" title="'.$eLang->get('PAGE').' '.$p.'">'.$p.'</a></li>'."\n";
			}
		}
		if ($page < $maxpage) {
			$html .= '<li><a href="'.$linkbase.$symb.'page='.$maxpage.'" title="'.$eLang->get('PAGE').' '.$maxpage.'">&raquo;</a></li>'."\n";
		} else {
			$html .= '<li class="elx5_pagdisabled"><a href="javascript:void(null);">&raquo;</a></li>'."\n";
		}
		$html .= "</ul>\n";

		return $html;
	}


	/*****************************************/
	/* LISTING TABLE SUMMARY WITH PAGINATION */
	/*****************************************/
	public function tableSummary($linkbase, $page, $maxpage, $totalitems) {
		$eLang = eFactory::getLang();

		$txt = sprintf($eLang->get('PAGEOF'), $page, $maxpage);
		$txt .= '<span class="elx5_lmobhide"> ('.sprintf($eLang->get('TOTAL_ITEMS'), $totalitems).')</span>';

		$html = '<div class="elx5_row elx5_vpad">'."\n";
		$html .= '<div class="elx5_datainfo">'.$txt."</div>\n";
		if ($maxpage > 1) {
			$html .= '<div class="elx5_datapagination">'."\n";
			$html .= $this->pagination($linkbase, $page, $maxpage);
			$html .= "</div>\n";
		}
		$html .= "</div>\n";

		return $html;
	}


	/**********************/
	/* START MODAL WINDOW */
	/**********************/
	public function startModalWindow($title, $sfx='', $close_function='', $modal_contents=false, $head_class='', $body_class='', $attrs=array()) {
		$eLang = eFactory::getLang();

		if (trim($close_function) == '') { $close_function = 'elx5ModalClose'; }
		if (trim($head_class) == '') { $head_class = 'elx5_modalhead'; }
		if (trim($body_class) == '') { $body_class = 'elx5_modalbody'; }

		$attrs_str = '';
		if ($attrs) {
			foreach ($attrs as $k => $v) { $attrs_str .= ' '.$k.'="'.$v.'"'; }
		}

		$html = '<div class="elx5_modal" id="elx5_modal'.$sfx.'"'.$attrs_str.'>'."\n";
		$html .= '<div class="elx5_modalcon" id="elx5_modalcon'.$sfx.'">'."\n";
		$html .= '<div class="'.$head_class.'"><a href="javascript:void(null);" onclick="'.$close_function.'(\''.$sfx.'\');" title="'.$eLang->get('CLOSE').'">x</a><h4 id="elx5_modaltitle'.$sfx.'">'.$title.'</h4></div>'."\n";
		$html .= '<div class="'.$body_class.'" id="elx5_modalbody'.$sfx.'" data-waitlng="'.$eLang->get('PLEASE_WAIT').'">'."\n";
		$html .= '<div class="elx5_invisible" id="elx5_modalmessage'.$sfx.'"></div>'."\n";
		if ($modal_contents) {
			$html .= '<div class="elx5_invisible" id="elx5_modalcontents'.$sfx.'">'."\n";
		}

		return $html;
	}


	/********************/
	/* END MODAL WINDOW */
	/********************/
	public function endModalWindow($modal_contents=false) {
		$html = '';
		if ($modal_contents) { $html .= "</div>\n"; }
		$html .= "</div>\n";//elx5_modalbody
		$html .= '</div></div>'."\n";

		return $html;
	}


	/************************/
	/* ADD FULL PAGE LOADER */
	/************************/
	public function pageLoader($id='elx5_pgloading', $waitmsg='') {
		if (trim($id) == '') { $id = 'elx5_pgloading'; }
		if (trim($waitmsg) == '') {
			$eLang = eFactory::getLang();
			$waitmsg = $eLang->exist('PLEASE_WAIT') ? $eLang->get('PLEASE_WAIT') : 'Please wait...';
		}

		$html = '<div class="elx5_pgloading" id="'.$id.'">'."\n";
		$html .= '<div class="elx5_pgloadingcon">'."\n";
		$html .= '<div class="elx5_pgloadingicon">&#160;</div>'."\n";
		$html .= '<div class="elx5_pgloadingtext">'.$waitmsg."</div>\n";
		$html .= "</div>\n</div>\n";

		return $html;
	}

}

?>