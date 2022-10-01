<?php 
/**
* @version		$Id: mod_gallery.php 2126 2019-03-03 13:01:08Z IOS $
* @package		Elxis
* @subpackage	Module Gallery
* @copyright	Copyright (c) 2006-2019 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


if (!class_exists('modGallery', false)) {
	class modGallery {

		private $cache = 0;
		private $dir = '';
		private $errormsg = '';
		private $limit = 12;
		private $ordering = 0;
		private $pretext = '';
		private $link = '';
		private $lightbox = 1;


		/*********************/
		/* MAGIC CONSTRUCTOR */
		/*********************/
		public function __construct($params) {
			$this->getParams($params);
		}


		/*************************/
		/* GET MODULE PARAMETERS */
		/*************************/
		private function getParams($params) {
			$this->cache = (int)$params->get('cache', 0);
			$this->limit = (int)$params->get('limit', 12);
			if ($this->limit < 1) { $this->limit = 12; }
			$this->ordering = (int)$params->get('ordering', 0);
			$this->pretext = trim(strip_tags($params->getML('pretext', '')));
			$this->link = trim($params->get('link', ''));

			if ($this->link != '') {
				if (!preg_match('#^(http(s?)\:\/\/)#i', $this->link)) {
					$this->link = eFactory::getElxis()->makeURL($this->link);
				}
			}
			$this->lightbox = (int)$params->get('lightbox', 1);
			$this->dir = $params->get('dir', '');
			$this->dir = trim(preg_replace('#[^a-z0-9\-\_\/]#i', '', $this->dir));
			$this->dir = preg_replace('#^(\/)#', '', $this->dir);
			if ($this->dir == '') {//try the sample folder
				if (is_dir(ELXIS_PATH.'/media/images/sample_gallery/')) { $this->dir = 'sample_gallery/'; }
			}
			if (($this->dir == '/') || ($this->dir == '')) { $this->errormsg = 'Images folder is invalid!'; return; }
			if (!preg_match('#(\/)$#', $this->dir)) { $this->dir .= '/'; }
			if (!is_dir(ELXIS_PATH.'/media/images/'.$this->dir)) { $this->errormsg = 'Images folder does not exist!'; return; }
		}


		/*********************/
		/* GET FOLDER IMAGES */
		/*********************/
		private function getImages() {
			$images = eFactory::getFiles()->listFiles('media/images/'.$this->dir, '(.gif)|(.jpeg)|(.jpg)|(.png)$');
			if (!$images) { return false; }
			if ($this->ordering == 1) {
				usort($images, array('modGallery', 'orderByName'));
				$final = $images;
			} else if (($this->ordering == 2) || ($this->ordering == 3)) {
				$temp = array();
				foreach ($images as $image) {
					$ts = filemtime(ELXIS_PATH.'/media/images/'.$this->dir.$image);
					$temp[] = array('image' => $image, 'ts' => $ts);
				}
				$method = ($this->ordering == 2) ? 'orderNewer' : 'orderOlder';
				usort($temp, array('modGallery', $method));
				$final = array();
				foreach ($temp as $tmp) { $final[] = $tmp['image']; }
			} else {
				shuffle($images);
				$final = $images;
			}

            return $final;
		}


		/**********************************/
		/* ORDER IMAGES BY THEIR FILENAME */
		/**********************************/
		public static function orderByName($a, $b) {
			return strcmp($a, $b);
		}


		/**********************/
		/* NEWER IMAGES FIRST */
		/**********************/
		public static function orderNewer($a, $b) {
			if ($a['ts'] == $b['ts']) { return 0; }
			return ($a['ts'] < $b['ts']) ? 1 : -1;
		}


		/**********************/
		/* OLDER IMAGES FIRST */
		/**********************/
		public static function orderOlder($a, $b) {
			if ($a['ts'] == $b['ts']) { return 0; }
			return ($a['ts'] < $b['ts']) ? -1 : 1;
		}


		/***************************/
		/* ADD REQUIRED JS AND CSS */
		/***************************/
		private function addJSCSS() {
			if (defined('GLIGHTBOX_LOADED')) { return; }
			$eDoc = eFactory::getDocument();
			$baselink = eFactory::getElxis()->secureBase().'/modules/mod_gallery/includes/';
			$eDoc->addStyleLink($baselink.'glightbox.min.css');
			$eDoc->addScriptLink($baselink.'glightbox.min.js');
			define('GLIGHTBOX_LOADED', 1);
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

			$images = $this->getImages();
			if (!$images) {
				$this->showError('No images found in the given folder!');
				return;
			}

			$rnd = rand(100, 999);
			if ($this->lightbox) { $this->addJSCSS(); }
			$i = 1;
			$baseURL = eFactory::getElxis()->secureBase().'/media/images/'.$this->dir;
			echo '<div class="mod_gallery_box">'."\n";
			if ($this->pretext != '') { echo '<p>'.$this->pretext."</p>\n"; }
			
			echo '<div class="mod_gallery_images">'."\n";
			foreach ($images as $image) {
				if (file_exists(ELXIS_PATH.'/media/images/'.$this->dir.'thumbs/'.$image)) {
					$thumb = $baseURL.'thumbs/'.$image;
				} else {
					$thumb = $baseURL.$image;
				}
				if ($this->lightbox) {
					echo '<a href="'.$baseURL.$image.'" class="mod_gallery'.$rnd.'"><img src="'.$thumb.'" alt="'.$image.'" /></a>'."\n";
				} else {
					echo '<a href="javascript:void(null);"><img src="'.$thumb.'" alt="'.$image.'" /></a>'."\n";
				}

				if ($i >= $this->limit) { break; }
				$i++;
			}
			echo "</div>\n";
			if ($this->link != '') {
				$eLang = eFactory::getLang();
				echo '<div class="mod_gallery_more"><a href="'.$this->link.'" title="'.$eLang->get('MORE_IMAGES').'">'.$eLang->get('MORE_IMAGES')."</a></div>\n";
			}
			echo "</div>\n";
			if ($this->lightbox) {
				echo '<script>var modgalbox'.$rnd.' = GLightbox({ selector: \'mod_gallery'.$rnd.'\'});</script>'."\n";
			}
		}

	}
}


$mgallery = new modGallery($params);
$mgallery->run();
unset($mgallery);

?>