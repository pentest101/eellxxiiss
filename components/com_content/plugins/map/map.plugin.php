<?php 
/**
* @version		$Id: map.plugin.php 2315 2019-12-26 18:32:13Z IOS $
* @package		Elxis
* @subpackage	Component Content / Plugins
* @copyright	Copyright (c) 2006-2020 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class mapPlugin implements contentPlugin {


	private static $imap = 0;


	/********************/
	/* MAGIC CONTRUCTOR */
	/********************/
	public function __construct() {
	}


	/***********************************/
	/* EXECUTE PLUGIN ON THE GIVEN ROW */
	/***********************************/
	public function process(&$row, $published, $params) {
		$regex = "#{map\s*(.*?)}(.*?){/map}#s";
    	$regexno = "#{map\s*.*?}.*?{/map}#s";
    	if (!$published) {
    		$row->text = preg_replace($regexno, '', $row->text);
    		return true;
    	}

		preg_match_all($regex, $row->text, $matches, PREG_PATTERN_ORDER);
		if (!$matches) { return true; }

		$ePlugin = eFactory::getPlugin();

		$cfg = array();
		$cfg['mtype'] = trim($params->get('mtype', 'ROADMAP'));
		if (($cfg['mtype'] == '') || !in_array($cfg['mtype'], array('ROADMAP', 'SATELLITE', 'HYBRID', 'TERRAIN'))) { $cfg['mtype'] = 'ROADMAP'; }
		$cfg['mtypecontrol'] = (intval($params->get('mtypecontrol', 1)) == 1) ? 'true' : 'false';
		$cfg['mtypecontrolopts'] = trim($params->get('mtypecontrolopts', 'DEFAULT'));
		if (($cfg['mtypecontrolopts'] == '') || !in_array($cfg['mtypecontrolopts'], array('DEFAULT', 'HORIZONTAL_BAR', 'DROPDOWN_MENU'))) { $cfg['mtypecontrolopts'] = 'DEFAULT'; }
		$cfg['mzoom'] = (int)$params->get('mzoom', 13);
		if (($cfg['mzoom'] < 1) || ($cfg['mzoom'] > 20)) { $cfg['mzoom'] = 13; }
		$cfg['mzoomcontrol'] = (intval($params->get('mzoomcontrol', 1)) == 1) ? 'true' : 'false';
		$cfg['mzoomcontrolopts'] = trim($params->get('mzoomcontrolopts', 'DEFAULT'));
		if (($cfg['mzoomcontrolopts'] == '') || !in_array($cfg['mzoomcontrolopts'], array('DEFAULT', 'SMALL', 'LARGE'))) { $cfg['mzoomcontrolopts'] = 'DEFAULT'; }
		$cfg['mnavcontrol'] = (intval($params->get('mnavcontrol', 1)) == 1) ? 'true' : 'false';
		$cfg['mnavcontrolopts'] = trim($params->get('mnavcontrolopts', 'DEFAULT'));
		if (($cfg['mnavcontrolopts'] == '') || !in_array($cfg['mnavcontrolopts'], array('DEFAULT', 'SMALL', 'ANDROID', 'ZOOM_PAN'))) { $cfg['mnavcontrolopts'] = 'DEFAULT'; }
		$cfg['mscale'] = (intval($params->get('mscale', 1)) == 1) ? 'true' : 'false';
		$cfg['key'] = trim($params->get('key', ''));

		foreach ($matches[0] as $i => $match) {
			$address = trim($matches[2][$i]);
			if ($address == '') {
				$row->text = preg_replace("#".$match."#", '', $row->text);
				continue;
			}

			$is_multiple = false;
			if (strpos($address, '|') !== false) {
				$is_multiple = true;
				$addresses = explode('|', $address);
				$ok = true;
				foreach ($addresses as $addr) {
					if (preg_match('#([^0-9\-\,\.])#', $addr)) { $ok = false; break; }
				}
				if (!$ok) {
					$repl = '<div class="elx5_warning">Invalid map coordinates!</div>'."\n";
					$row->text = preg_replace("#".$match."#", $repl, $row->text);
					continue;
				}
				$address = $addresses;
			} else if (preg_match('#([^0-9\-\,\.])#', $address)) {
				$repl = '<div class="elx5_warning">Invalid map coordinates!</div>'."\n";
				$row->text = preg_replace("#".$match."#", $repl, $row->text);
				continue;
			}

			self::$imap++;
			$attributes = $ePlugin->parseAttributes($matches[1][$i]);
			if ($is_multiple) {
				$infos = isset($attributes['info']) ? explode('|', $attributes['info']) : array();
				$attributes['info'] = array();
				foreach ($address as $k => $addr) {
					$attributes['info'][] = (isset($infos[$k])) ? $infos[$k] : '';
				}
			}

			$maphtml = $this->makeMap($cfg, $attributes, $address);
			if ($is_multiple) {
				$row->text = str_replace($match, $maphtml, $row->text);
			} else {
				$row->text = preg_replace("#".$match."#", $maphtml, $row->text);
			}
		}
		return true;
	}


	/************************/
	/* GENERIC SYNTAX STYLE */
	/************************/
	public function syntax() {
		return '{map info="optional info"}latitude,longitude{/map}';
	}


	/***********************/
	/* LIST OF HELPER TABS */
	/***********************/
	public function tabs() {
		$eLang = eFactory::getLang();
		return array($eLang->get('COORDINATES'), $eLang->get('HELP'));
	}


	/*****************/
	/* PLUGIN HELPER */
	/*****************/
	public function helper($pluginid, $tabidx, $fn) {
		switch ($tabidx) {
			case 1: $this->setArea(); break;
			case 2: $this->showHelp(); break;
			default: break;
		}
	}


	/***************************************************/
	/* RETURN REQUIRED CSS AND JS FILES FOR THE HELPER */
	/***************************************************/
	public function head() {
		$elxis = eFactory::getElxis();

		$response = array(
			'js' => array($elxis->secureBase().'/components/com_content/plugins/map/includes/map.js'),
			'css' => array()
		);
		return $response;
	}
    

	/*******************************/
	/* PLUGIN SPECIAL TASK HANDLER */
	/*******************************/
	public function handler($pluginid, $fn) {
		$elxis = eFactory::getElxis();
		$url = $elxis->makeAURL('content:plugin/', 'inner.php').'?id='.$pluginid.'&fn='.$fn;
		$elxis->redirect($url);
	}


	/**********************************/
	/* GENERATE GOOGLE MAPS HTML CODE */
	/**********************************/
	private function makeMap($cfg, $attributes, $address) {
		if (is_array($address)) {
			$html = $this->makeMultipleMap($cfg, $attributes, $address);
			return $html;
		}

		$info = '';
		if (isset($attributes['info']) && ($attributes['info'] != '')) {
			$pat = '@([\']|[\"]|[\$]|[\#]|[\*]|[\%]|[\~]|[\`]|[\^]|[\|]|[\{]|[\}]|[\\\])@';
			$info = strip_tags($attributes['info']);
			$info = preg_replace($pat, '', $info);
		}

		$route = false;
		if (isset($attributes['destination'])) {
			$route = array();
			$route['origin'] = $address;
			$route['destination'] = '';
			$route['waypoints'] = '';
			$route['travelmode'] = 'DRIVING';
			$latlng = explode(',', $attributes['destination']);
			if (is_array($latlng) && (count($latlng) == 2) && (trim($latlng[0]) != '') && (trim($latlng[1]) != '')) {
				$route['destination'] = trim($latlng[0]).','.trim($latlng[1]);
				if (isset($attributes['waypoints']) && ($attributes['waypoints'] != '')) {
					$route['waypoints'] = str_replace('%7C', '|', $attributes['waypoints']);
				}
				if (isset($attributes['travelmode']) && ($attributes['travelmode'] != '')) { $route['travelmode'] = $attributes['travelmode']; }
			}
			if ($route['travelmode'] == '') { $route['travelmode'] = 'DRIVING'; }
			if ($route['destination'] == '') { $route = false; }
		}

		$this->importJS($cfg, $info, $address, $route, 0);

		//data-coord can be used for individual map styling (CSS3 [data-coord=X] selector) alternative to id selector if id is unknown
		$coord = preg_replace('@([\-]|[\,]|[\.])@', '', $address);
		$html = '<div id="googlemap'.self::$imap.'" class="elx_googlemap" data-coord="'.$coord.'"></div>';

		return $html;
	}


	/*******************************************/
	/* GENERATE GOOGLE MAP WITH MULTIPLE SPOTS */
	/*******************************************/
	private function makeMultipleMap($cfg, $attributes, $addresses) {
		$elxis = eFactory::getElxis();
		$eDoc = eFactory::getDocument();

		$pat = '@([\']|[\"]|[\$]|[\#]|[\*]|[\%]|[\~]|[\`]|[\^]|[\|]|[\{]|[\}]|[\\\])@';
		$lats = array();
		$longs = array();
		$spottexts = array();
		foreach ($addresses as $k => $addr) {
			$spottext = strip_tags($attributes['info'][$k]);
			$spottext = preg_replace($pat, '', $spottext);
			$parts = preg_split('/\,/', $addr, -1, PREG_SPLIT_NO_EMPTY);

			$lats[] = $parts[0];
			$longs[] = $parts[1];
			$spottexts[] = $spottext;
		}

		$infostr = implode('|', $spottexts);
		$latstr = implode('|', $lats);
		$longstr = implode('|', $longs);
		$address_str = $latstr.','.$longstr;

		$this->importJS($cfg, $infostr, $address_str, false, 1);

		$html = '<div id="googlemap'.self::$imap.'" class="elx_googlemap" style="height:500px;" data-coord="multiplespots" data-multiple="1"></div>';
		return $html;
	}


	/******************************/
	/* IMPORT REQUIRED JAVASCRIPT */
	/******************************/	
	private function importJS($cfg, $info, $address, $route, $is_multiple) {
		$eDoc = eFactory::getDocument();

		$latlng = explode(',',$address);
		if (!is_array($latlng) || (count($latlng) != 2)) { return; }

		if (!defined('PLUG_MAP_LOADED')) {
			$eDoc->setContentType('text/html'); //google maps do not work with application/xhtml+xml due to document.write
			if ($cfg['key'] != '') {
				$eDoc->addScriptLink('https://maps.googleapis.com/maps/api/js?key='.$cfg['key'].'&sensor=false');
			} else {
				$eDoc->addScriptLink('https://maps.googleapis.com/maps/api/js?sensor=false');
			}

			$js = 'var mapcfg = { mzoom:'.$cfg['mzoom'].', mtypecontrol:'.$cfg['mtypecontrol'].', mzoomcontrol:'.$cfg['mzoomcontrol'].', mnavcontrol:'.$cfg['mnavcontrol'];
			$js .= ', mscale:'.$cfg['mscale'].', mtype:\''.$cfg['mtype'].'\'';
			if ($cfg['mtypecontrol'] === 'true') { $js.= ', mtypecontrolopts: \''.$cfg['mtypecontrolopts'].'\''; }
			if ($cfg['mzoomcontrol'] === 'true') { $js.= ', mzoomcontrolopts: \''.$cfg['mzoomcontrolopts'].'\''; }
			if ($cfg['mnavcontrol'] === 'true') { $js.= ', mnavcontrolopts: \''.$cfg['mnavcontrolopts'].'\''; }
			$js .= ', multiple: [], address: [], lat: [], lng: [], info: [], destination: [], waypoints: [], travelmode: []'."};\n";
			$js .= 'elxLoadEvent(function() { initGoogleMaps(); });';
			$eDoc->addScript($js);

			$link = eFactory::getElxis()->secureBase().'/components/com_content/plugins/map/includes/map.js';
			$eDoc->addScriptLink($link);
			define('PLUG_MAP_LOADED', 1);
		}

		$js = 'mapcfg.multiple['.self::$imap.'] = '.$is_multiple.'; mapcfg.lat['.self::$imap.'] = \''.$latlng[0].'\'; mapcfg.lng['.self::$imap.'] = \''.$latlng[1].'\'; mapcfg.info['.self::$imap.'] = \''.$info.'\';';
		if (is_array($route)) {
			$js .= ' mapcfg.destination['.self::$imap.'] = \''.$route['destination'].'\'; mapcfg.waypoints['.self::$imap.'] = \''.$route['waypoints'].'\'; mapcfg.travelmode['.self::$imap.'] = \''.$route['travelmode'].'\'; ';
		} else {
			$js .= ' mapcfg.destination['.self::$imap.'] = \'\'; mapcfg.waypoints['.self::$imap.'] = \'\'; mapcfg.travelmode['.self::$imap.'] = \'\'; ';
		}

		$eDoc->addScript($js);
	}


	/***************/
	/* SET AN AREA */
	/***************/
	private function setArea() {
		$eLang = eFactory::getLang();

		echo '<h3>Single location</h3>'."\n";
		echo '<div class="elx5_sideinput_wrap">';
		echo '<div class="elx5_sideinput_value_end elx5_spad">';
		echo '<a href="javascript:void(null);" class="elx5_btn elx5_ibtn" title="'.$eLang->get('ADD').'" onclick="plugMapAddLocation();"><i class="fas fa-location-arrow"></i></a>';
		echo '</div>';
		echo '<div class="elx5_sideinput_input_front elx5_spad">';
		echo '<label class="elx5_label" for="plgmap_area">'.$eLang->get('COORDINATES').'</label>';
		echo '<div class="elx5_labelside">';
		echo '<input type="text" name="plgmap_area" value="" id="plgmap_area" class="elx5_text" dir="ltr" placeholder="'.$eLang->get('COORDINATES').'" />';
		echo "</div></div></div>\n";
		echo '<div class="elx5_formrow">'."\n";
		echo '<label class="elx5_label" for="plgmap_info">'.$eLang->get('INFORMATION')."</label>\n";
		echo '<div class="elx5_labelside">'."\n";
		echo '<input type="text" name="plgmap_info" value="" id="plgmap_info" class="elx5_text" dir="ltr" placeholder="'.$eLang->get('INFORMATION').' ('.$eLang->get('OPTIONAL').')" />';
		echo "</div>\n</div>\n";

		echo '<h3>Route</h3>'."\n";
		echo '<div class="elx5_sideinput_wrap">';
		echo '<div class="elx5_sideinput_value_end elx5_spad">';
		echo '<a href="javascript:void(null);" class="elx5_btn elx5_ibtn" title="'.$eLang->get('ADD').'" onclick="plugMapAddRoute();"><i class="fas fa-location-arrow"></i></a>';
		echo '</div>';
		echo '<div class="elx5_sideinput_input_front elx5_spad">';
		echo '<label class="elx5_label" for="plgmap_origin">'.$eLang->get('ORIGIN').'</label>';
		echo '<div class="elx5_labelside">';
		echo '<input type="text" name="plgmap_origin" value="" id="plgmap_origin" class="elx5_text" dir="ltr" placeholder="'.$eLang->get('ORIGIN').' ('.$eLang->get('COORDINATES').')" />';
		echo "</div></div></div>\n";

		echo '<div class="elx5_formrow">'."\n";
		echo '<label class="elx5_label" for="plgmap_dest">'.$eLang->get('DESTINATION')."</label>\n";
		echo '<div class="elx5_labelside">'."\n";
		echo '<input type="text" name="plgmap_dest" value="" id="plgmap_dest" class="elx5_text" dir="ltr" placeholder="'.$eLang->get('DESTINATION').' ('.$eLang->get('COORDINATES').')" />';
		echo "</div>\n</div>\n";

		echo '<div class="elx5_formrow">'."\n";
		echo '<label class="elx5_label" for="plgmap_wayp">'.$eLang->get('WAYPOINTS')."</label>\n";
		echo '<div class="elx5_labelside">'."\n";
		echo '<input type="text" name="plgmap_wayp" value="" id="plgmap_wayp" class="elx5_text" dir="ltr" placeholder="'.$eLang->get('WAYPOINTS').' ('.$eLang->get('OPTIONAL').')" />';
		echo '<div class="elx5_tip">'.$eLang->get('WAYPOINTS_TIP')."</div>\n";
		echo "</div>\n</div>\n";

		echo '<div class="elx5_formrow">'."\n";
		echo '<label class="elx5_label" for="plgmap_tmode">'.$eLang->get('TRAVEL_MODE')."</label>\n";
		echo '<div class="elx5_labelside">'."\n";
		echo '<select name="plgmap_tmode" id="plgmap_tmode" class="elx5_select">'."\n";
		echo '<option value="DRIVING" selected="selected">DRIVING</option>'."\n";
		echo '<option value="BICYCLING">BICYCLING</option>'."\n";
		echo '<option value="TRANSIT">TRANSIT</option>'."\n";
		echo '<option value="WALKING">WALKING</option>'."\n";
		echo "</select>\n";
		echo "</div>\n</div>\n";
	}
	
	
	/***************/
	/* PLUGIN HELP */
	/***************/
	private function showHelp() {
?>		
		<p><strong>Map</strong> plugin allows you to display Google maps inside Elxis articles. You can display any location in the world and info for each location. 
		The map appearance is fully customizable (marker style, map size, map type can be normal, satellite and hybrid, zoom, controls, map scale etc). 
		You can change these parameters on Map plugin&apos;s edit page. The map plugin also supports displaying a <strong>travel route</strong>. In this case you must also provide
		coordinates for the <strong>destination</strong> and the <strong>waypoints</strong>.</p>

		<h4>Usage <span>(Single location)</span></h4>
		<p>To display a spot on the map:</p>
		<div class="elx5_info">{map info=&quot;optional info&quot;}latitude,longitude{/map}</div>
		<p>To get a location coordinates, go to <a href="https://maps.google.com/" target="_blank">Google maps</a> find the spot you are interested in, 
		right click and select <strong>Whats here?</strong>. Copy the coordinates showm (example: <em>19.07598304,72.87765502</em> You can optional type 
		a description for the map spot.</p>

		<h4>Usage <span>(Multiple locations)</span></h4>
		<p>To display multiple spots on the map:</p>
		<div class="elx5_info">{map info=&quot;optional info1|optional info2&quot;}latitude1,longitude1|latitude2,longitude2{/map}</div>
		<p>To get a location coordinates, go to <a href="https://maps.google.com/" target="_blank">Google maps</a> find the spot you are interested in, 
		right click and select <strong>Whats here?</strong>. Copy the coordinates showm (example: <em>19.07598304,72.87765502</em> You can optional type 
		a description for the map spot. Separate multiple spots with |</p>

		<h4>Usage <span>(route)</span></h4>
		<p>To display a route on the map:</p>
		<div class="elx5_info">{map destination=&quot;lat1,long1&quot; waypoints=&quot;lat2,long2|lat3,long3|lat4,long4&quot; travelmode=&quot;DRIVING&quot;}latitude,longitude{/map}</div>

		<h4>Limitations</h4>
		<p>You can use the Google Maps API version 3 for free (<a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">get an API key</a> 
		for free). However if you make massive usage it is recommended to take a look at the <a href="https://developers.google.com/maps/documentation/javascript/usage" title="Google maps usage limits" target="_blank">usage limits</a> 
		of this API. You must load the maps API using an API key in order to purchase additional quota.</p>

<?php 
	}

}

?>