<?php 
/**
* @version		$Id: mod_language.php 2113 2019-02-25 21:45:04Z IOS $
* @package		Elxis
* @subpackage	Module Language
* @copyright	Copyright (c) 2006-2019 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


if (!class_exists('modLanguage', false)) {
	class modLanguage {

		private $style = 0;
		private $langnames = 0;
		private $colour = 2;
		private $elxis_uri = '';
		private $ssl = false;
		private $lang = 'en';
		private $infolangs = array();


		/*********************/
		/* MAGIC CONSTRUCTOR */
		/*********************/
		public function __construct($params) {
			$elxis = eFactory::getElxis();
			$eLang = eFactory::getLang();
			$eURI = eFactory::getURI();

			$this->style = (int)$params->get('style', 0);
			$this->langnames = (int)$params->get('langnames', 0);
			$this->colour = (int)$params->get('colour', 2);

			if ($eLang->getinfo('DIR') == 'RTL') {
				if ($this->style == 5) {
					$this->style = 6;
				} else if ($this->style == 6) {
					$this->style = 5;
				}
			}

			$segs = $eURI->getSegments();
			$this->elxis_uri = $eURI->getComponent();
			//if (($this->elxis_uri == $elxis->getConfig('DEFAULT_ROUTE')) || ($this->elxis_uri.':/' == $elxis->getConfig('DEFAULT_ROUTE'))) {
			if ($this->elxis_uri == 'content') {
				$this->elxis_uri = '';
			}

			if ($segs) {
				$this->elxis_uri .= ($this->elxis_uri == '') ? implode('/', $segs) : ':'.implode('/', $segs);
				$n = count($segs) - 1;
				if (!preg_match('#\.#', $segs[$n])) { $this->elxis_uri .= '/'; }
			} else {
				$this->elxis_uri .= ($this->elxis_uri != '') ? '/' : '';
			}

			$this->lang = $eLang->currentLang();
			$this->ssl = $eURI->detectSSL();
			$this->infolangs = $eLang->getSiteLangs(true);
		}


		/**********************/
		/* EXECUTE THE MODULE */
		/**********************/
		public function run() {
			if (!$this->infolangs) { return; }
			$elxis = eFactory::getElxis();
			$eLang = eFactory::getLang();

			$flagsdir = $elxis->secureBase().'/includes/libraries/elxis/language/flags/';
			$sfx = $eLang->getinfo('RTLSFX');
			$cssfile = $elxis->secureBase().'/modules/mod_language/css/modlang'.$sfx.'.css';
			eFactory::getDocument()->addStyleLink($cssfile);

			if ($this->style == 1) {
				echo '<div class="modulang">'."\n";
				foreach ($this->infolangs as $lng => $info) {
					$css_class = ($lng == $this->lang) ? ' class="curlang"' : '';
					echo '<a href="'.$elxis->makeURL($lng.':'.$this->elxis_uri, '', $this->ssl).'" title="'.$info['NAME'].' - '.$info['NAME_ENG'].'"'.$css_class.'>';
					echo '<img src="'.$flagsdir.$lng.'.png" alt="'.$info['NAME_ENG'].'" /></a> '."\n";
				}
				echo "</div>\n";

			} else if ($this->style == 2) {
				echo '<div class="modulang">'."\n";
				foreach ($this->infolangs as $lng => $info) {
					$name = $this->langName($lng, $info);
					$css_class = ($lng == $this->lang) ? ' class="curlang"' : '';
					echo '<a href="'.$elxis->makeURL($lng.':'.$this->elxis_uri, '', $this->ssl).'" title="'.$info['NAME'].'"'.$css_class.'>';
					echo '<img src="'.$flagsdir.$lng.'.png" alt="'.$info['NAME_ENG'].'" /> '.$name.'</a> '."\n";
				}
				echo "</div>\n";
			} else if ($this->style == 3) {
				echo '<div class="modulang">'."\n";
				foreach ($this->infolangs as $lng => $info) {
					$name = $this->langName($lng, $info);
					$css_class = ($lng == $this->lang) ? ' class="curlang"' : '';
					echo '<a href="'.$elxis->makeURL($lng.':'.$this->elxis_uri, '', $this->ssl).'" title="'.$name.'"'.$css_class.'>'.$name.'</a> '."\n";
				}
				echo "</div>\n";
			} else if ($this->style == 4) {
				$lng = $this->lang;
				$info = $this->infolangs[$lng];
				$name = $this->langName($lng, $info);
				echo '<div class="modulang">'."\n";
				echo '<a href="'.$elxis->makeURL('user:/', '', $this->ssl).'" title="'.$name.' - '.$eLang->get('SELECT_LANG').'">'."\n";
				echo '<img src="'.$flagsdir.$lng.'.png" alt="'.$info['NAME_ENG'].'" /> '.$name."</a>\n";
				echo "</div>\n";
			} else if (($this->style == 5) || ($this->style == 6)) {
				$info = $this->infolangs[ $this->lang ];
				$name = $this->langName($this->lang, $info);
				if ($this->style == 5) {
					$slider_id = 'langslider'.count($this->infolangs);
					$css1 = 'langslider';
					$css2 = 'langslcur';
					$css3 = 'langslmore';
				} else {
					$slider_id = 'langrslider'.count($this->infolangs);
					$css1 = 'langrslider';
					$css2 = 'langrslcur';
					$css3 = 'langrslmore';
				}
				echo '<div class="modulang">'."\n";
				echo '<div class="'.$css1.'" id="'.$slider_id.'">'."\n";
				echo '<div class="'.$css2.'"><a href="javascript:void(null);" title="'.$name.'"><img src="'.$flagsdir.$this->lang.'.png" alt="'.$this->infolangs[$this->lang]['NAME_ENG'].'" />'."</a></div>\n";
				echo '<div class="'.$css3.'">'."\n";
				foreach ($this->infolangs as $lng => $info) {
					if ($lng == $this->lang) { continue; }
					$name = $this->langName($lng, $info);
					echo '<a hreflang="'.$lng.'" href="'.$elxis->makeURL($lng.':'.$this->elxis_uri, '', $this->ssl).'" title="'.$name.'"><img src="'.$flagsdir.$lng.'.png" alt="'.$info['NAME_ENG'].'" />'."</a>\n";
				}
				echo "</div>\n";
				echo "</div>\n";
				echo "</div>\n";
			} else {
				switch ($this->colour) {
					case 0: $addon = ' langgray'; break;
					case 1: $addon = ' langblack'; break;
					case 3: $addon = ' langpurple'; break;
					case 4: $addon = ' langdgray'; break;
					case 5: $addon = ' langrgray'; break;
					case 6: $addon = ' langtgray'; break;
					case 7: $addon = ' langgreen'; break;
					case 8: $addon = ' langorange'; break;
					case 9: $addon = ' langyellow'; break;
					case 2: default: $addon = ''; break;
				}
				$info = $this->infolangs[ $this->lang ];
				$name = $this->langName($this->lang, $info);
				echo '<div class="modulang">'."\n";
				echo '<ul class="langdrop'.$addon.'">'."\n";
				echo '<li><a href="javascript:void(null);" title="'.$name.'"><img src="'.$flagsdir.$this->lang.'.png" alt="'.$this->infolangs[$this->lang]['NAME_ENG'].'" /> '.$name."</a>\n";
				echo "<ul>\n";
				foreach ($this->infolangs as $lng => $info) {
					if ($lng == $this->lang) { continue; }
					$name = $this->langName($lng, $info);
					echo '<li><a hreflang="'.$lng.'" href="'.$elxis->makeURL($lng.':'.$this->elxis_uri, '', $this->ssl).'" title="'.$name.'">';
					echo '<img src="'.$flagsdir.$lng.'.png" alt="'.$info['NAME_ENG'].'" /> '.$name."</a></li>\n";
				}
				echo "</ul>\n";
				echo "</li>\n";
				echo "</ul>\n";
				echo "</div>\n";
			}
		}


		/**********************************/
		/* GET LANGUAGE NAME AS ON PARAMS */
		/**********************************/
		private function langName($lng, $info) {
			switch ($this->langnames) {
				case 1: return $info['NAME_ENG']; break;
				case 2: return $lng; break;
				case 3: return $info['LANGUAGE'].'-'.$info['REGION']; break;
				case 0: default: return $info['NAME']; break;
			}
		}

	}
}


$modlang = new modLanguage($params);
$modlang->run();
unset($modlang);

?>