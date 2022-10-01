<?php 
/**
* @version		$Id: mod_adminlang.php 2057 2019-02-03 19:12:39Z IOS $
* @package		Elxis
* @subpackage	Module Administration language
* @copyright	Copyright (c) 2006-2019 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


if (!class_exists('modadminLang', false)) {
	class modadminLang {

		private $lock = false;
		private $elxis_uri = '';
		private $ssl = false;
		

		/*********************/
		/* MAGIC CONSTRUCTOR */
		/*********************/
		public function __construct() {
			$eURI = eFactory::getURI();

			if (!defined('ELXIS_ADMIN')) { return; }

			$this->elxis_uri = $eURI->getComponent();
			if (($this->elxis_uri == 'cpanel') || ($this->elxis_uri.':/' == 'cpanel:/')) { $this->elxis_uri = ''; }

			$segs = $eURI->getSegments();
			$n = count($segs);

			if ($n > 0) {
				$last_segment = $segs[$n - 1];
				if (in_array($last_segment, array('add.html', 'edit.html', 'new.html', 'config.html', 'configuration.html', 'settings.html'))) { $this->lock = true; }
				$this->elxis_uri .= ($this->elxis_uri == '') ? implode('/', $segs) : ':'.implode('/', $segs);
				if (!preg_match('#\.#', $last_segment)) { $this->elxis_uri .= '/'; }
			} else {
				$this->elxis_uri .= ($this->elxis_uri != '') ? '/' : '';
			}

			$this->ssl = $eURI->detectSSL();
		}


		/********************/
		/* RUN FOREST, RUN! */
		/********************/
		public function run() {
			if (!defined('ELXIS_ADMIN')) {
				echo '<div class="elx5_warning">This module is available only in Elxis administratrion area!</div>'."\n";
				return;
			}

			if (ELXIS_INNER == 1) { return; }

			$elxis = eFactory::getElxis();
			$eLang = eFactory::getLang();

			$ilangs = $eLang->getAllLangs(false);
			if (!$ilangs) { return; }

			$jsfile = $elxis->secureBase().'/modules/mod_adminlang/js/adminlang.js';
			eFactory::getDocument()->addScriptLink($jsfile);

			$baselink = $elxis->makeAURL('xx:'.$this->elxis_uri, '', $this->ssl);

			$curlang = $eLang->currentLang();
			echo '<div class="elx5_cptoptool"><form name="modalang_fm" class="modalang_fm" action="">';
			echo '<select name="lang" class="modalang_select" id="modalang_select" onchange="modALangSwitch();" data-baselink="'.$baselink.'" data-deflang="'.$elxis->getConfig('LANG').'">'."\n";
			if ($this->lock === true) {
				echo '<option value="'.$curlang.'" selected="selected">'.strtoupper($curlang)."</option>\n";
			} else {
				foreach ($ilangs as $lng) {
					$selected = ($curlang == $lng) ? ' selected="selected"' : '';
					echo '<option value="'.$lng.'"'.$selected.'>'.strtoupper($lng)."</option>\n";
				}
			}
			echo '</select></form></div>'."\n";
		}

	}
}


$modadmlang = new modadminLang();
$modadmlang->run();
unset($modadmlang);

?>