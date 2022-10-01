<?php 
/**
* @version		$Id: mod_adminsearch.php 2066 2019-02-10 07:53:14Z IOS $
* @package		Elxis
* @subpackage	Module Administration search
* @copyright	Copyright (c) 2006-2019 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


if (!class_exists('modadminSearch', false)) {
	class modadminSearch {

		private $disabled = false;

		/*********************/
		/* MAGIC CONSTRUCTOR */
		/*********************/
		public function __construct() {
			$segs = eFactory::getURI()->getSegments();
			$n = count($segs);
			if ($n > 0) {
				$last_segment = $segs[$n - 1];
				if (in_array($last_segment, array('add.html', 'edit.html', 'new.html', 'config.html', 'configuration.html', 'settings.html'))) { $this->disabled = true; }
			}
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

			$action = $elxis->makeAURL('content:articles/');
			echo '<div class="elx5_cptoptool"><form class="modasearch_fm" action="'.$action.'" method="get" name="asearchfm">'."\n";
			if ($this->disabled === true) {
				echo '<input type="text" name="q" value="" placeholder="'.$eLang->get('SEARCH').'..." dir="'.$eLang->getinfo('DIR').'" class="modasearch_input" readonly="readonly" />'."\n";
			} else {
				echo '<input type="text" name="q" value="" placeholder="'.$eLang->get('SEARCH').'..." dir="'.$eLang->getinfo('DIR').'" class="modasearch_input" />'."\n";
			}
			echo '<input type="hidden" name="st" value="1" dir="ltr" />'."\n";
			echo '<button type="submit" name="s" class="elx5_invisible">search</button>'."\n";
			echo "</form></div>\n";
		}

	}
}


$modasearch = new modadminSearch();
$modasearch->run();
unset($modasearch);

?>