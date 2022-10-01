<?php 
/**
* @version		$Id: mod_iosslider.php 2206 2019-04-10 19:12:14Z IOS $
* @package		Elxis
* @subpackage	Module IOS Slider
* @copyright	Copyright (c) 2008-2019 Is Open Source (http://www.isopensource.com). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Ioannis Sannos ( http://www.isopensource.com )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


if (!class_exists('modIOSslider', false)) {
	class modIOSslider {

		private $source = 0;
		private $img_height = 0;
		private $border = '';
		private $effect = 'fade';
		private $capeffect = 'move';
		private $transdur = 1500;
		private $delay = 6;
		private $autoplay = 1;
		private $playbuttons = 0;
		private $hoverstop = 0;
		private $bullets = 1;
		private $caption = 1;
		private $controls = 1;
		private $locid = 0;
		private $sublocs = 1;
		private $hids = '';
		private $catid = 0;
		private $subcats = 0;
		private $catids = array();
		private $custom = array();
		private $limit = 5;
		private $lng = 'en';
		private $translate = false;
		private static $idx = 0;
		private $moduleId = 0;
		private $errormsg = '';


		/*********************/
		/* MAGIC CONSTRUCTOR */
		/*********************/
		public function __construct($params, $elxmod) {
			$this->moduleId = $elxmod->id;
			$this->lng = eFactory::getURI()->getUriLang();
			if (eFactory::getElxis()->getConfig('MULTILINGUISM') == 1) {
				if ($this->lng != '') { $this->translate = true; }
			}
			$this->getParams($params);
		}


		/*************************/
		/* GET MODULE PARAMETERS */
		/*************************/
		private function getParams($params) {
			$elxis = eFactory::getElxis();

			$this->source = (int)$params->get('source', 0);
			$this->img_height = (int)$params->get('img_height', 0);
			$border = (int)$params->get('border', 0);
			switch ($border) {
				case 1: $this->border = 'white'; break;
				case 2: $this->border = 'gray'; break;
				case 3: $this->border = 'darkgray'; break;
				case 4: $this->border = 'black'; break;
				case 5: $this->border = 'blue'; break;
				case 0: default: $this->border = ''; break;
			}
			$this->effect = trim($params->get('effect', 'fade'));
			if ($this->effect == '') { $this->effect = 'fade'; }
			$capeffect = (int)$params->get('capeffect', 0);
			switch ($capeffect) {
				case 1: $this->capeffect = 'slide'; break;
				case 2: $this->capeffect = 'fade'; break;
				case 0: default: $this->capeffect = 'move'; break;
			}
			$this->transdur = (int)$params->get('transdur', 1500);
			if ($this->transdur < 500) { $this->transdur = 1500; }
			$this->delay = (int)$params->get('delay', 6);
			if ($this->delay < 1) { $this->delay = 6; }
			$this->autoplay = (int)$params->get('autoplay', 1);
			$this->playbuttons = (int)$params->get('playbuttons', 0);
			$this->hoverstop = (int)$params->get('hoverstop', 0);
			$this->bullets = (int)$params->get('bullets', 1);
			$this->caption = (int)$params->get('caption', 1);
			$this->controls = (int)$params->get('controls', 1);
			if ($this->source == 1) {
				$this->catid = (int)$params->get('catid', 0);
				$this->subcats = (int)$params->get('subcats', 0);
				if ($this->catid < 1) { $this->errormsg = 'No category selected for the slider!'; }
			} else if ($this->source == 2) {
				$catstr = trim($params->get('catids', ''));
				$catids = explode(',', $catstr);
				if ($catids) {
					foreach ($catids as $catid) {
						$catid = (int)$catid;
						if ($catid > 0) { $this->catids[] = $catid; }
					}
				}
				if (count($this->catids) == 0) { $this->errormsg = 'No categories selected for the slider!'; }
			} else if ($this->source == 3) {
				for ($i=1; $i<7; $i++) {
					$image = trim($params->get('image'.$i, ''));
					if (($image == '') || !file_exists(ELXIS_PATH.'/'.$image)) { continue; }
					$link = trim($params->get('link'.$i, ''));
					if (($link != '') && (!preg_match('#^(http(s)?\:\/\/)#i', $link))) { $link = $elxis->makeURL($link); }
					$title = eUTF::trim(strip_tags($params->getML('title'.$i, '')));
					$image = $elxis->secureBase().'/'.$image;
					$this->custom[] = array('title' => $title, 'subtitle' => '', 'link' => $link, 'image' => $image);
				}
				if (count($this->custom) == 0) { $this->errormsg = 'There are no custom images for the slider!'; }
			} else if ($this->source == 4) {
				if (!file_exists(ELXIS_PATH.'/components/com_reservations/ext/hotels/hotels.iosr.php')) {
					$this->errormsg = 'IOSR Hotels is not installed!';
				}
				$this->locid = (int)$params->get('locid', 0);
				$this->sublocs = (int)$params->get('sublocs', 1);
				$this->hids = trim($params->get('hids', ''));
			} else if ($this->source == 5) {
				$mlfolders = (int)$params->get('mlfolders', 0);
				$folderlink = trim($params->get('folderlink', ''));
				if (($folderlink != '') && (!preg_match('#^(http(s)?\:\/\/)#i', $folderlink))) { $folderlink = $elxis->makeURL($folderlink); }
				$folder = $params->get('folder', '');
				$folder = trim(preg_replace('#[^a-z0-9\-\_\/]#i', '', $folder));
				$folder = preg_replace('#^(\/)#', '', $folder);
				if ($folder == '') {//try the sample folder
					if (is_dir(ELXIS_PATH.'/media/images/sample_silder/')) { $folder = 'sample_gallery/'; }
				}
				if (($folder == '/') || ($folder == '')) {
					$this->errormsg = 'Images folder is invalid!';
				} else {
					if ($mlfolders == 1) {
						$lng = eFactory::getLang()->currentLang();
						if (!preg_match('#(\/)$#', $folder)) {
							$mlfolder = $folder.'_'.$lng.'/';
						} else {
							$mlfolder = rtrim($folder, '/').'_'.$lng.'/';
						}
						if (is_dir(ELXIS_PATH.'/media/images/'.$mlfolder)) { $folder = $mlfolder; }
					}
					if (!preg_match('#(\/)$#', $folder)) { $folder .= '/'; }

					if (is_dir(ELXIS_PATH.'/media/images/'.$folder)) {
						$images = eFactory::getFiles()->listFiles('media/images/'.$folder, '(.gif)|(.jpeg)|(.jpg)|(.png)$');
						if ($images) {
							foreach ($images as $image) {
								$title = $this->getTitleFromFile($image);
								$image = $elxis->secureBase().'/media/images/'.$folder.$image;
								$this->custom[] = array('title' => $title, 'subtitle' => '', 'link' => $folderlink, 'image' => $image);
							}
						}
					}
				}
				if (count($this->custom) == 0) { $this->errormsg = 'There are no folder images for the slider!'; }
			}

            $this->limit = (int)$params->get('limit', 5);
            if ($this->limit < 1) { $this->limit = 5; }
        }


		/***************************/
		/* ADD REQUIRED JS AND CSS */
		/***************************/
		private function addJSCSS() {
			$eDoc = eFactory::getDocument();

			if (!defined('IOS_SLIDER_LOADED')) {
				$sfx = eFactory::getLang()->getinfo('RTLSFX');
				$baseurl = eFactory::getElxis()->secureBase().'/modules/mod_iosslider';
				$eDoc->addStyleLink($baseurl.'/css/iosslider'.$sfx.'.css');
				$eDoc->addJQuery();
				$eDoc->addScriptLink($baseurl.'/js/iosslider.js');
				define('IOS_SLIDER_LOADED', 1);
			}

			$delayms = $this->delay * 1000;
			$height = ($this->img_height > 0) ? $this->img_height : 320;
			$js = 'jQuery(\'#iosslider_wrap'.self::$idx.'\').iosSlider({ '
			.'effect:\''.$this->effect.'\', prev:\'\', next:\'\', duration: '.$this->transdur.', delay:'.$delayms.', width:960, height:'.$height.', ';
			$js .= ($this->autoplay == 1) ? 'autoPlay:true, ' : 'autoPlay:false, ';
			$js .= ($this->playbuttons == 1) ? 'playPause:true, ' : 'playPause:false, ';
			$js .= ($this->hoverstop == 1) ? 'stopOnHover:true, ' : 'stopOnHover:false, ';
			$js .= ($this->bullets == 1) ? 'bullets:true, ' : 'bullets:false, ';
			$js .= ($this->caption == 1) ? 'caption:true, ' : 'caption:false, ';
			$js .= ($this->controls == 1) ? 'controls:true, ' : 'controls:false, ';
			$js .= 'loop:false, captionEffect:\''.$this->capeffect.'\', onBeforeStep:0, images:0, preventCopy:0 });';
			$eDoc->addDocReady($js);
		}


		/*************************/
		/* DISPLAY ERROR MESSAGE */
		/*************************/
		private function showError($msg) {
			echo '<div class="elx5_warning">'.$msg."</div>\n";
		}


		/**********************/
		/* EXECUTE THE MODULE */
		/**********************/
		public function run() {
        	if ($this->errormsg != '') {
        		$this->showError($this->errormsg);
        		return;
       		}

			self::$idx++;
			$data = $this->getData();
			if (!$data) {
        		if ($this->errormsg != '') { $this->showError($this->errormsg); }
				return;
			}

			$this->addJSCSS();
			$img_class = '';
			if ($this->img_height > 0) { $img_class = ' class="iosslider_h'.$this->img_height.'"'; }

			echo '<div class="iosslider_outer'.$this->border.'">';
			echo '<div class="iosslider_wrap" id="iosslider_wrap'.self::$idx.'">'."\n";
			echo '<div class="iosslider_images"><ul>'."\n";
			$i = 0;
			foreach ($data as $k => $item) {
				if ($item['link'] != '') {
					echo '<li><a href="'.$item['link'].'"><img src="'.$item['image'].'" alt="'.$item['title'].'" title="'.$item['title'].'" id="iossl'.self::$idx.'_'.$i.'"'.$img_class.' /></a>'.$item['subtitle'].'</li>'."\n";
				} else {
					echo '<li><img src="'.$item['image'].'" alt="'.$item['title'].'" title="'.$item['title'].'" id="iossl'.self::$idx.'_'.$i.'"'.$img_class.' />'.$item['subtitle'].'</li>'."\n";
				}
				$i++;
			}
			echo "</ul></div>\n";
			if ($this->bullets == 1) {
				echo '<div class="iosslider_bullets"><div>'."\n";
				$i = 1;
				foreach ($data as $k => $item) {
					echo '<a href="#" title="'.$item['title'].'"><img src="'.$item['image'].'" alt="'.$item['title'].'"/>'.$i.'</a>'."\n";
					$i++;
				}
				echo "</div></div>\n";
			}
			echo '<div class="iosslider_shadow"></div>'."\n";
			echo "</div>\n";
			echo "</div>\n";
		}


		/**********************************/
		/* LOAD/GET IOS RESERVATIONS CORE */
		/**********************************/
		private function loadgetIOSR() {
			if (eRegistry::isLoaded('icore')) { return eRegistry::get('icore'); }
			if (!file_exists(ELXIS_PATH.'/components/com_reservations/includes/ios.core.php')) { return false; }
			elxisLoader::loadFile('components/com_reservations/includes/ios.core.php');
			eRegistry::set(new iosCore(), 'icore');
			return eRegistry::get('icore');
		}


		/*******************/
		/* GET SLIDES DATA */
		/*******************/
		private function getData() {
			if (($this->source == 3) || ($this->source == 5)) { return $this->custom; }
			if ($this->source == 4) {
				return $this->getHotelsData();
			}

			$db = eFactory::getDB();
            $elxis = eFactory::getElxis();
            $eFiles = eFactory::getFiles();
			$lowlev = 0;
			$binds = array();
			$sql = "SELECT a.id, a.catid, a.title, a.seotitle, a.subtitle, a.image, c.seolink"
			."\n FROM ".$db->quoteId('#__content')." a"
			."\n LEFT JOIN ".$db->quoteId('#__categories')." c ON c.catid=a.catid";
			if ($this->source == 1) {
				$sql .= "\n WHERE a.published = 1 AND c.published = 1";
				if ($this->subcats == 1) {
					$sql .= "\n AND ((c.catid = :ctg) OR (c.parent_id = :ctg))";
					$binds[] = array(':ctg', $this->catid, PDO::PARAM_INT);
				} else {
					$sql .= "\n AND c.catid = :ctg";
					$binds[] = array(':ctg', $this->catid, PDO::PARAM_INT);
				}
			} else if ($this->source == 2) {
				$sql .= "\n WHERE a.published = 1 AND c.published = 1";
				$sql .= "\n AND a.catid IN (".implode(",", $this->catids).")";
			} else if ($this->source == 6) {
				$sql .= "\n WHERE (((a.published) = 1 AND (c.published = 1)) OR ((a.published = 1) AND (a.catid = 0)))";
				$sql .= "\n AND a.important = :ximp";
				$binds[] = array(':ximp', 1, PDO::PARAM_INT);
			} else { //source 0
				$sql .= "\n WHERE (((a.published) = 1 AND (c.published = 1)) OR ((a.published = 1) AND (a.catid = 0)))";
			}
			$sql .= "\n AND a.alevel = :lowlevel ORDER BY a.created DESC";
			$binds[] = array(':lowlevel', $lowlev, PDO::PARAM_INT);
			$stmt = $db->prepareLimit($sql, 0, $this->limit);
			foreach ($binds as $bind) {
				$stmt->bindParam($bind[0], $bind[1], $bind[2]);
			}
			$stmt->execute();
			$rows = $stmt->fetchAllAssoc('id', PDO::FETCH_OBJ);
			if (!$rows) { return array(); }

			if ($this->translate === true) { $rows = $this->translateArticles($rows); }

			$data = array();
			foreach ($rows as $row) {
				if ((trim($row->image) == '') || !file_exists(ELXIS_PATH.'/'.$row->image)) { continue; }
				$image = $elxis->secureBase().'/'.$row->image;
				if ($row->catid > 0) {
					$link = $elxis->makeURL('content:'.$row->seolink.$row->seotitle.'.html');
				} else {
					$link = $elxis->makeURL('content:'.$row->seotitle.'.html');
				}
      			$data[] = array('title' => $row->title, 'subtitle' => $row->subtitle, 'link' => $link, 'image' => $image);
			}

            return $data;
		}


		/**********************/
		/* TRANSLATE ARTICLES */
		/**********************/
		private function translateArticles($rows) {
			$db = eFactory::getDB();

			$ids = array();
			foreach ($rows as $row) { $ids[] = $row->id; }
			$sql = "SELECT ".$db->quoteId('element').", ".$db->quoteId('elid').", ".$db->quoteId('translation')
			."\n FROM ".$db->quoteId('#__translations')
			."\n WHERE ".$db->quoteId('category')."=".$db->quote('com_content')." AND ".$db->quoteId('language')." = :lng"
			."\n AND ((".$db->quoteId('element')." = ".$db->quote('title').") OR (".$db->quoteId('element')." = ".$db->quote('subtitle')."))"
			."\n AND ".$db->quoteId('elid')." IN (".implode(", ", $ids).")";
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':lng', $this->lng, PDO::PARAM_STR);
			$stmt->execute();
			$translations = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if ($translations) {
				foreach ($translations as $trans) {
					$id = (int)$trans['elid'];
					$element = $trans['element'];
					if (!isset($rows[$id])) { continue; }
					switch($element) {
						case 'title': $rows[$id]->title = $trans['translation']; break;
						case 'subtitle': $rows[$id]->subtitle = $trans['translation']; break;
						default: break;
					}
				}
			}
			return $rows;
		}

		/*******************************/
		/* GET IMAGES FROM IOSR HOTELS */
		/*******************************/
		private function getHotelsData() {
			$iosr = $this->loadgetIOSR();
			if ($iosr === false) {
				$this->errormsg = 'Component IOS Reservations is not installed!';
				return array();
			}
			if (!$iosr->extAvailable('hotels')) {
				$this->errormsg = 'Extension IOSR Hotels is not available in IOS Reservations!';
				return array();
			}

			$db = eFactory::getDB();
			$elxis = eFactory::getElxis();
			$helper = $iosr->loadOnce('hothelper', 'hotels');

			$lids = array();
			$hids = array();
			if ($this->locid > 0) {
				$lids[] = $this->locid;
				if ($this->sublocs == 1) {
					$sql = "SELECT ".$db->quoteId('lid')." FROM ".$db->quoteId('#__res_locations')." WHERE ".$db->quoteId('parent')." = :xpar";
					$stmt = $db->prepare($sql);
					$stmt->bindParam(':xpar', $this->locid, PDO::PARAM_INT);
					$stmt->execute();
					$locs = $stmt->fetchCol();
					if ($locs) {
						foreach ($locs as $loc) { $lids[] = (int)$loc; }
					}
					unset($locs);
				}
			} else if ($this->hids != '') {
				$ids = explode(',', $this->hids);
				foreach ($ids as $id) {
					$hid = (int)$id;
					if ($hid > 0) { $hids[] = $hid; }
				}
				if (!$hids) { return array(); }
			}

			$empty = '';
			$sql = "SELECT h.hid, h.title, h.lid, h.accid, h.defimage, l.country, l.title AS location, l.seotitle, l.seolink"
			."\n FROM ".$db->quoteId('#__res_hotels')." h"
			."\n INNER JOIN ".$db->quoteId('#__res_locations')." l ON l.lid=h.lid";
			if (count($lids) > 1) {
				$sql .= "\n WHERE h.lid IN (".implode(',', $lids).") AND ";
			} else if (count($hids) > 1) {
				$sql .= "\n WHERE h.hid IN (".implode(',', $hids).") AND ";
			} else {
				$sql .= "\n WHERE ";
			}
			$sql .= 'h.published = 1 AND h.defimage <> :xemp ORDER BY RAND()';
			$stmt = $db->prepareLimit($sql, 0, $this->limit);
			$stmt->bindParam('xemp', $empty, PDO::PARAM_STR);
			$stmt->execute();
			$rows = $stmt->fetchAllAssoc('hid', PDO::FETCH_OBJ);
			if (!$rows) { return array(); }

			if ($this->translate === true) { $rows = $this->translateHotels($rows); }

			$ms = 1;
			if (defined('ELXIS_MULTISITE')) { $ms = ELXIS_MULTISITE; }
			$gal_url_base = $elxis->secureBase().'/components/com_reservations/gallery/s'.$ms.'/hotels/large/';
			$gal_dir_base = ELXIS_PATH.'/components/com_reservations/gallery/s'.$ms.'/hotels/large/';
			$countries = $iosr->getCountryPairs(false);
			$data = array();
			foreach ($rows as $row) {
				if ((trim($row->defimage) == '') || !file_exists($gal_dir_base.$row->defimage)) { continue; }
				$image = $gal_url_base.$row->defimage;
				$link = $helper->locURL($row->country, $row->seolink, $row->seotitle, $row->hid);
				$c = $row->country;
				$subtitle = (isset($countries[$c])) ? $row->location.', '.$countries[$c] : $row->location.', '.$row->country;

      			$data[] = array('title' => $row->title, 'subtitle' => $subtitle, 'link' => $link, 'image' => $image);
			}

            return $data;
		}


		/***************************************/
		/* TRANSLATION IOSR HOTELS & LOCATIONS */
		/***************************************/
		private function translateHotels($rows) {
			$db = eFactory::getDB();

			$ids = array();
			foreach ($rows as $row) { $ids[] = $row->hid; $ids[] = $row->lid; }
			$ids = array_unique($ids);

			$sql = "SELECT ".$db->quoteId('element').", ".$db->quoteId('elid').", ".$db->quoteId('translation')
			."\n FROM ".$db->quoteId('#__translations')
			."\n WHERE ".$db->quoteId('category')."=".$db->quote('com_reservations')." AND ".$db->quoteId('language')." = :lng"
			."\n AND ((".$db->quoteId('element')." = ".$db->quote('hottitle').") OR (".$db->quoteId('element')." = ".$db->quote('loctitle')."))"
			."\n AND ".$db->quoteId('elid')." IN (".implode(", ", $ids).")";
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':lng', $this->lng, PDO::PARAM_STR);
			$stmt->execute();
			$translations = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if ($translations) {
				foreach ($translations as $trans) {
					$id = (int)$trans['elid'];
					$element = $trans['element'];
					if ($element == 'hottitle') {
						if (isset($rows[$id])) {
							$rows[$id]->title = $trans['translation'];
						}
					} else {
						foreach ($rows as $hid => $row) {
							if ($row->lid == $id) {
								$rows[$hid]->location = $trans['translation'];
							}
						}
					}
				}
			}
			return $rows;
		}


		/*****************************/
		/* MAKE TITLE FROM FILE NAME */
		/*****************************/
		private function getTitleFromFile($file) {
			$fn = basename($file);
			$n = strrpos($fn, '.');
			if ($n !== false) { $fn = substr($fn, 0, $n); }
			$title = str_replace('_', ' ', $fn);
			$title = str_replace('-', ' ', $title);
			$title = str_replace('.', ' ', $title);
			$title = preg_replace('/[^\s\da-z]/i', '', $title);
			if ($title == '') { $title = 'Image'; }
			return $title;
		}

	}
}


$iosslider = new modIOSslider($params, $elxmod);
$iosslider->run();
unset($iosslider);

?>