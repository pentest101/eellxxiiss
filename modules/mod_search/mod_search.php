<?php 
/**
* @version		$Id$
* @package		Elxis
* @subpackage	Module search
* @copyright	Copyright (c) 2006-2019 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


if (!class_exists('moduleSearch', false)) {
	class moduleSearch {

		private $showbutton = 0;


		/*********************/
		/* MAGIC CONSTRUCTOR */
		/*********************/
		public function __construct($params) {
			$this->showbutton = (int)$params->get('showbutton', 1);
		}


		/**************/
		/* RUN MODULE */
		/**************/
		public function run() {
			$eLang = eFactory::getLang();
			$eURI = eFactory::getURI();
			$eDoc = eFactory::getDocument();
			$eSearch = eFactory::getSearch();

			$engines = $eSearch->getEngines();
			if (count($engines) == 0) { return; }
			$current = $eSearch->getCurrentEngine();
			$rnd = rand(1,999);
			if (count($engines) > 1) {
				$eDoc->addScriptLink($eURI->secureBase().'/modules/mod_search/search.js');
				$eDoc->addScript('elxLoadEvent(function() { msearchPick('.$rnd.') });');
			}
			$isssl = $eURI->detectSSL();
			$baseaction = $eURI->makeURL('search:/', '', $isssl);

			echo '<form name="fmmodsearch" id="fmmodsearch'.$rnd.'" class="elx_modsearchform" action="'.$baseaction.$current.'.html" method="get">'."\n";
			if (count($engines) > 1) {
				$imgdir = $eURI->secureBase().'/components/com_search/engines/';
				echo '<select name="engine" class="elx_modsearch_eng" id="elx_modsearch_eng'.$rnd.'" onchange="msearchPick('.$rnd.')" title="'.$eLang->get('SELECT').'">'."\n";
				foreach ($engines as $name => $engine) {
					$sel = ($name == $current) ? ' selected="selected"' : '';
					echo '<option value="'.$name.'"'.$sel.' data-image="'.$imgdir.$name.'/'.$name.'.png" data-act="'.$baseaction.$name.'.html">'.$engine['title']."</option>\n";
				}
				echo "</select>\n";
			}
			echo '<input type="text" name="q" id="msearchq" size="20" class="elx_modsearch_input" value="" placeholder="'.$eLang->get('SEARCH').'" dir="'.$eLang->getinfo('DIR').'" />'."\n";
			if ($this->showbutton == 1) {
				echo '<button type="submit" name="searchbtn" class="elx_modsearch_btn">'.$eLang->get('SEARCH').'</button>'."\n";
			}
			echo "</form>\n";
			echo '<div class="clear"></div>'."\n";
		}

	}
}

$modsearch = new moduleSearch($params);
$modsearch->run();
unset($modsearch);

?>