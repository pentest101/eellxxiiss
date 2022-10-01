<?php 
/**
* @version		$Id: minifier.helper.php 1514 2014-05-13 18:20:15Z sannosi $
* @package		Elxis
* @subpackage	Helpers / CSS & JS minifier
* @copyright	Copyright (c) 2006-2014 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisMinifierHelper {

	private $repo_path = '';
	private $hash = '';
	private $url = '';
	private $securl = '';
	private $excluded_links = array();
	public $remove_comments = true;
	public $remove_emptylines = true;
	private $phpcompat = true; //CSS minifier requires at least PHP 5.3


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		$elxis = eFactory::getElxis();

		$this->url = $elxis->getConfig('URL').'/';
		$this->securl = str_replace('http:', 'https:', $this->url);
		$this->repo_path = rtrim($elxis->getConfig('REPO_PATH'), '/');
		if ($this->repo_path == '') { $this->repo_path = ELXIS_PATH.'/repository'; }
		$this->phpcompat = (version_compare(PHP_VERSION, '5.3.0') >= 0) ? true : false;
	}


	/**********************/
	/* MINIFY GIVEN LINKS */
	/**********************/
	public function minify($links, $type='css', $only_get_contents=false) {
		$this->hash = '';
		$this->excluded_links = array();
		if (!is_array($links) || (count($links) == 0)) { return false; }
		$names = '';
		$rellinks = array();

		foreach ($links as $link) {
			if (strpos($link, '.php') !== false) {
				$this->excluded_links[] = $link;
			} else if (strpos($link, $this->url) !== false) {
				$rel_link = str_replace($this->url, '', $link);
				$names .= $rel_link;
				$rellinks[] = $rel_link;
			} else if (strpos($link, $this->securl) !== false) {
				$rel_link = str_replace($this->securl, '', $link);
				$names .= $rel_link;
				$rellinks[] = $rel_link;
			} else {
				$this->excluded_links[] = $link;
			}
		}

		if (!$rellinks) { return false; }
		$this->hash = md5($names);
		if (!$only_get_contents) {
			if (file_exists($this->repo_path.'/cache/minify/'.$this->hash.'.'.$type)) { return true; }
		}
		$contents = '';
		foreach ($rellinks as $lnk) {
			if ($this->phpcompat && ($type == 'css')) {
				$dirurl = dirname($this->url.$lnk).'/';
				$temp = file_get_contents(ELXIS_PATH.'/'.$lnk);
				$contents .= $this->cssRelToAbs($dirurl, $temp);
			} else {
				$contents .= file_get_contents(ELXIS_PATH.'/'.$lnk);
			}
		}

		if ($this->remove_comments) {
			$contents = $this->removeComments($contents);
		}
		if ($this->remove_emptylines) {
			$contents = $this->removeEmptyLines($contents);
		}

		if ($only_get_contents) {
			return $contents;
		}

		$eFiles = eFactory::getFiles();
		if (!file_exists($this->repo_path.'/cache/minify/')) {
			$ok = $eFiles->createFolder('cache/minify/', 0777, true);
			if (!$ok) { return false; }
		}
		$filename = $this->hash.'.'.$type;
		$ok = $eFiles->createFile('cache/minify/'.$filename, $contents, true, true);

		return $ok;
	}


	/******************/
	/* GET HASH VALUE */
	/******************/
	public function getHash() {
		return $this->hash;
	}


	/**********************/
	/* GET EXCLUDED LINKS */
	/**********************/
	public function getExcluded() {
		return $this->excluded_links;
	}


	/***********************************/
	/* REMOVE COMMENTS FROM GIVEN TEXT */
	/***********************************/
	private function removeComments($txt) {
    	$txt = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $txt);
		return $txt;
	}


	/**************************************/
	/* REMOVE EMPTY LINES FROM GIVEN TEXT */
	/**************************************/
	private function removeEmptyLines($txt) {
		$txt = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $txt);
		return $txt;
	}


	/*************************************************************************/
	/* CONVERT RELATIVE IMAGE URL PATHS IN CSS INTO ABSOLUTE ONES (PHP 5.3+) */
	/*************************************************************************/
	private function cssRelToAbs($absurl, $css) {
		if (!preg_match('@url@i', $css)) { return $css; }
		$options = parse_url($absurl); 
		if (!$options) { return $css; }
		$options['absurl'] = $absurl;
		$options['urlBasePath'] = substr($options['path'], 0, strrpos($options['path'],"/"));
		$options['dirDepth'] = substr_count($options['path'], '/')-1;
		$options['urlBase'] = $options['scheme'].'://'.$options['host'].$options['urlBasePath'];

		$replcss = preg_replace_callback(
			'\'(url\\(\s*[\\\'"]?\\s*)(.*?\\))\'',
			function ($matches) {
				while (strpos($matches[0], "/./") !== false) { $matches[0]=str_replace("/./","/",$matches[0]); } 
				if (substr($matches[2], 0, 2) === "./") {
					$matches[2] = substr($matches[2], 2); 
					return $matches[1].$matches[2];
				} else {
					return $matches[0];
				}
			},
			$css
		);

		$replcss = preg_replace_callback(
			'\'(url\\(\\s*[\\\'"]?\\s*)((\\.\\./)+)(?!\.\\./)(.*?\\))\'',
			function ($matches) use ($options) {
				$dirDepthRel = substr_count($matches[2], '../');
				$urlBasePath = $options['urlBasePath'];
				for ($i=0; $i < $dirDepthRel; $i++) {
					$urlBasePath = substr($options['urlBasePath'], 0, strrpos($options['urlBasePath'],"/"));
				}
				$urlBase = $options['scheme'].'://'.$options['host'].$urlBasePath;
				$relativeURL = $urlBase.'/'.$matches[4];
				return $matches[1].$relativeURL;
			},
			$replcss
		); 

		do {
			$tempContent = $replcss; 
			$filtcss = preg_filter('\'(url\\(.*?)(([^/]*)/\\.\\./)(.*?\\))\'', '$1$4', $replcss);
			if ($filtcss != NULL) { $replcss = $filtcss; } 
		} while ($tempContent != $replcss);

		$finalcss = preg_replace('\'(url\\(\\s*[\\\'"]?\\s*)(//)(.*?\\))\'', '$1'.$options['scheme'].':$2$3', $replcss); 
		$finalcss = preg_replace('\'(url\\(\\s*[\\\'"]?\\s*)(/)(.*?\\))\'', '$1'.$options['scheme'].'://'.$options['host'].'$2$3', $finalcss); 
		$finalcss = preg_replace('\'(url\\(\\s*[\\\'"]?\\s*)(((?!https?://).)*?\\))\'', '$1'.$options['urlBase'].'/'.'$2', $finalcss); 
		return $finalcss;
	}

}

?>