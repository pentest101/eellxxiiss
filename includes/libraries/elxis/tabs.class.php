<?php 
/**
* @version		$Id$
* @package		Elxis
* @subpackage	Tabs (Elxis 4.x style - For 5.x style either set the elxis5 = 1 option or use the elxis5Form library)
* @copyright	Copyright (c) 2006-2020 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http:s//www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisTabs {

	//Elxis 4.x tabs
	private $tabs = array();
	private $lastidx = 0;
	private $openidx = 0;
	//Elxis 5.1+ tabs
	private $elxis5 = 0;//off by default in order to work old extensions already using this library
	private $tabidx = 0;
	private $opentab = 0;
	private $options = array(
		'prefix' => 'elxtab',//Elxis 4.x
		//elxis 5.x (5.1+)
		'tab_prefix' => 'tab_elx5_',
		'tabs_id' => 'elx5_tabs',
		'tabs_class' => 'elx5_tabs',
		'tabs_content_class' => 'elx5_tab_content',
		'tabs_container_class' => 'elx5_tab_container',
		'tabs_open_class' => 'elx5_tab_open',
		'tabs_use_iconclass' => 0,
		'tabs_use_numeric' => 1,
		'tabs_add_tabopen' => true,//if false you must add the hidden input element elsewhere <input type="hidden" name="tabopen" id="tabopen...
		'returnhtml' => false//true: return html, false: echo html (only if elxis5 = 1)
	);


	/***************/
	/* CONSTRUCTOR */
	/***************/
	public function __construct($options=array()) {
		if ($options && (count($options) > 0)) {
			foreach ($options as $k => $v) {
				if ($k == 'elxis5') {
					$this->elxis5 = (int)$v;
					continue;
				}
				if (isset($this->options[$k])) { $this->options[$k] = $v; }
			}
		}
		if ($this->elxis5 == 0) {
			$this->importJS();
		}
	}


	/*******************/
	/* SET TABS OPTION */
	/*******************/
	public function setOption($option, $value) {
		if ($option == 'elxis5') {
			$this->elxis5 = (int)$value;
			return;
		}
		$this->options[$option] = $value;
	}


	/*******************/
	/* GET TABS OPTION */
	/*******************/
	public function getOption($option) {
		return isset($this->options[$option]) ? $this->options[$option] : '';
	}


	/****************************/
	/* GET INITIALLY OPENED TAB */
	/****************************/
	public function getOpenTab() {
		return $this->opentab;
	}


	/***************************************************/
	/* ADD REQUIRED JAVASCRIPT TO DOCUMENT (ELXIS 4.x) */
	/***************************************************/
	private function importJS() {
		if (defined('ELXIS_TABS_LOADED')) { return; }
	
		$eDoc = eFactory::getDocument();

		$jsFile = eFactory::getElxis()->secureBase().'/includes/js/jquery/tabs.js';
		$eDoc->addJQuery();
		$eDoc->addScriptLink($jsFile);
		define('ELXIS_TABS_LOADED', 1);
	}


	/******************************/
	/* START NEW TABS (ELXIS 5.x) */
	/******************************/
	public function startTabs($tabs, $opentab=-1, $attrs=array()) {
		$this->tabidx = 0;
		$this->opentab = (int)$opentab;
		if ($this->opentab == -1) {
			$this->opentab = (isset($_GET['tabopen'])) ? (int)$_GET['tabopen'] : 0;//auto get desired tab to open (Elxis 4.x style)
		}

		if (!$attrs) { $attrs = array(); }
		if (!isset($attrs['id'])) { $attrs['id'] = 'elx5_tabs'.rand(1000, 9999); }
		$this->options['tabs_id'] = $attrs['id'];

		eFactory::getDocument()->addNativeDocReady('elx5Tabs(\''.$attrs['id'].'\', \''.$this->options['tabs_open_class'].'\', \''.$this->options['tabs_content_class'].'\');');
		$html = '<ul class="'.$this->options['tabs_class'].'" id="'.$attrs['id'].'">'."\n";

		$k = 0;
		foreach ($tabs as $tab) {
			$icon = '';
			if (is_array($tab)) {
				$title = (isset($tab['title'])) ? $tab['title'] : 'Tab '.($k + 1);
				if ($this->options['tabs_use_iconclass'] == 1) {
					if (isset($tab['iconclass'])) {
						if ($tab['iconclass'] != '') { $icon = '<i class="'.$tab['iconclass'].'" aria-hidden="false"></i>'; }
					}
				} else if ($this->options['tabs_use_numeric'] == 1) {
					$icon = '<i class="elx5_tab_num" aria-hidden="false">'.($k + 1).'</i>';
				}
			} else {//string
				$title = (string)$tab;
				if ($this->options['tabs_use_numeric'] == 1) { $icon = '<i class="elx5_tab_num" aria-hidden="false">'.($k + 1).'</i>'; }
			}

			$class_str = ($k == $this->opentab) ? ' class="'.$this->options['tabs_open_class'].'"' : '';
			$html .= "\t".'<li><a href="javascript:void(null);" data-tab="'.$this->options['tab_prefix'].$k.'"'.$class_str.'>';
			$html .= ($icon != '') ? $icon.'<span class="elx5_lmobhide">'.$title.'</span>' : $title;
			$html .= "</a></li>\n";
			$k++;
		}

		$html .= "</ul>\n";
		$html .= '<div class="'.$this->options['tabs_container_class'].'">'."\n";

		if ($this->options['returnhtml']) {
			return $html;
		} else {
			echo $html;
		}
	}


	/************************/
	/* END TABS (ELXIS 5.x) */
	/************************/
	public function endTabs() {
		$html = '';
		if ($this->options['tabs_add_tabopen']) {
			$html .= '<input type="hidden" name="tabopen" id="tabopen'.$this->options['tabs_id'].'" value="'.$this->opentab.'" />'."\n";
		}
		$html .= "</div>\n";//tabs_container_class
		$this->tabidx = 0;
		$this->opentab = 0;

		if ($this->options['returnhtml']) {
			return $html;
		} else {
			echo $html;
		}
	}


	/************************/
	/* OPEN TAB (ELXIS 5.x) */
	/************************/
	public function openTab5() {
		$class = ($this->tabidx == $this->opentab) ? $this->options['tabs_content_class'] : 'elx5_invisible';
		$html = '<div id="'.$this->options['tab_prefix'].$this->tabidx.'" class="'.$class.'">'."\n";
		$this->tabidx++;
		if ($this->options['returnhtml']) {
			return $html;
		} else {
			echo $html;
		}
	}


	/*************************/
	/* CLOSE TAB (ELXIS 5.x) */
	/*************************/
	public function closeTab5() {
		if ($this->options['returnhtml']) {
			return "</div>\n";//tabs_content_class
		} else {
			echo "</div>\n";//tabs_content_class
		}
	}


	/*************************************************/
	/* START A NEW TAB (ELXIS 4.x / ELXIS 5.x ALIAS) */
	/*************************************************/
	public function openTab() {
		if ($this->elxis5 == 1) {
			if ($this->options['returnhtml']) {
				return $this->openTab5();
			} else {
				$this->openTab5();
				return;
			}
		}
		echo 'xxxxxx';
		exit;
		if (!$this->tabs) { return; }
		$this->openidx++;
		if ($this->openidx > $this->lastidx) { return; }
		if (!isset($this->tabs[ $this->openidx ])) { return; }
		if ($this->openidx == 1) { echo '<div class="tab_container">'."\n"; }
		$tab = $this->tabs[ $this->openidx ];
		echo '<div id="'.$this->options['prefix'].$this->openidx.'" class="tab_content">'."\n";
		if ($tab[1] != '') {
			$aid = 'ajax'.$this->options['prefix'].$this->openidx;
			echo '<script>'."\n";
			echo '$(document).ready(function() { $.ajaxSetup ({ cache: false });'."\n";
			echo 'var u'.$aid.' = \''.$tab[1].'\';'."\n";
			echo '$(\'#'.$aid.'\').click(function(){ $(\'#l'.$aid.'\').html(\'Loading...\').load(u'.$aid.');});'."\n";
			echo "});\n";
			echo "</script>\n";
			echo '<div id="l'.$aid.'"></div>'."\n";
		}
	}


	/************************************************/
	/* CLOSE OPEN TAB (ELXIS 4.x / ELXIS 5.x ALIAS) */
	/************************************************/
	public function closeTab() {
		if ($this->elxis5 == 1) {
			if ($this->options['returnhtml']) {
				return $this->closeTab5();
			} else {
				$this->closeTab5();
				return;
			}
		}

		if (!$this->tabs) { return; }
		if ($this->openidx > $this->lastidx) { return; } 
		echo "</div>\n";
		if ($this->openidx == $this->lastidx) { echo "</div>\n"; }
	}


	/***********************/
	/* ADD TAB (ELXIS 4.x) */
	/***********************/
	public function addTab($legend, $ajaxurl='') {
		$this->lastidx++;
		if (trim($legend) == '') { $legend = 'Tab '.$this->lastidx; }
		$this->tabs[ $this->lastidx ] = array($legend, $ajaxurl);
	}


	/*********************************/
	/* ADD ARRAY OF TABS (ELXIS 4.x) */
	/*********************************/
	public function addTabs($legends) {
		if (is_array($legends) && (count($legends) > 0)) {
			foreach ($legends as $legend) {
				$this->addTab($legend);
			}
		}
	}


	/*******************************/
	/* MAKE TABS INDEX (ELXIS 4.x) */
	/*******************************/
	public function makeIndex() {
		if (!defined('ELXIS_ADMIN')) {
			$this->tabs = array();
			$this->lastidx = 0;
			$this->openidx = 0;
			echo '<div class="elx5_error">Elxis Tabs work only in the administration area!</div>';
			return;
		}
		if (!$this->tabs) {
			echo '<div class="elx5_error">No tabs were set!</div>';
			return;
		}
		echo '<ul class="tabs">'."\n";
		foreach ($this->tabs as $idx => $tab) {
			$ext = ($tab[1] != '') ? ' id="ajax'.$this->options['prefix'].$idx.'"' : '';
			echo "\t".'<li><a href="#'.$this->options['prefix'].$idx.'"'.$ext.'>'.$tab[0]."</a></li>\n";
		}
		echo "</ul>\n";
	}

}

?>