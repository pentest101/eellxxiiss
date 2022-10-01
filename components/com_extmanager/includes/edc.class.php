<?php 
/**
* @version		$Id: edc.class.php 2422 2021-09-23 19:40:45Z IOS $
* @package		Elxis
* @subpackage	Component Extensions Manager
* @copyright	Copyright (c) 2006-2020 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisDC {

	private $elxisid = '';
	private $edc_url = 'https://www.elxis.net/inner.php/edc/rc/';
	private $releases_url = 'https://www.elxis.org/elxis-releases.xml';
	private $hashes_url = 'https://www.elxis.org/update/hashes4/';// hashes5/ ? => no, leave it to 4
	private $edc_limit = 12;
	private $edc_ordering = 'c';
	private $edc_vcheck = true;
	private $cache_category = 43200; //12 hours
	private $cache_frontpage = 21600; //6 hours
	private $cache_extension = 7200; //2 hours
	private $cache_edc_connect_error = 14400; //4 hours
	private $subsite = false;
	private $errormsg = '';
	private $time = 0;
	private $lang = 'en';
	private $repo_path = '';
	private $cache_path = '';
	private $edcauth = '';


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($params=null) {
		$elxis = eFactory::getElxis();

		if ($params) {
			//$this->elxisid = trim($params->get('elxisid', ''));
			$edc_url = trim($params->get('edc_url', ''));
			if (strpos($edc_url, 'http') === 0) {$this->edc_url = $edc_url; }
			$this->edc_limit = (int)$params->get('edc_limit', 12);
			if ($this->edc_limit < 1) { $this->edc_limit = 12; }
			$this->edc_ordering = trim($params->get('edc_ordering', 'c'));
			if (!in_array($this->edc_ordering, array('c', 'm', 'd', 'a', 'r'))) { $this->edc_ordering = 'c'; }
			$this->edc_vcheck = (intval($params->get('edc_vcheck', 1)) == 1) ? true : false;
		}
		unset($params);

		if (defined('ELXIS_MULTISITE') && (ELXIS_MULTISITE > 1)) { $this->subsite = true; }
		$this->time = time();
		$this->lang = eFactory::getLang()->currentLang();
		$this->repo_path = rtrim($elxis->getConfig('REPO_PATH'), '/');
		if ($this->repo_path == '') { $this->repo_path = ELXIS_PATH.'/repository'; }
		$this->cache_path = $this->repo_path.'/cache';

		if (!file_exists($this->cache_path.'/edc/')) {
			eFactory::getFiles()->createFolder('cache/edc/', 0755, true);
		}
	}


	/****************/
	/* GET ELXIS ID */
	/****************/
	public function getElxisId() {
		return $this->elxisid;
	}


	/***************/
	/* GET EDC URL */
	/***************/
	public function getEdcUrl() {
		return $this->edc_url;
	}


	/****************/
	/* GET EDC AUTH */
	/****************/
	public function getEdcAuth() {
		return $this->edcauth;
	}


	/******************************/
	/* GET THE LAST ERROR MESSAGE */
	/******************************/
	public function getErrorMessage() {
		return $this->errormsg;
	}


	/******************/
	/* EDC CATEGORIES */
	/******************/
	public function getCategories($sort=true) {
		$eLang = eFactory::getLang();

		$categories = array(
			1 => $eLang->get('CORE'),
			2 => $eLang->get('E_COMMERCE'),
			3 => $eLang->get('LANGUAGE'),
			4 => $eLang->get('MULTIMEDIA'),
			5 => $eLang->get('CALENDARS_EVENTS'),
			6 => $eLang->get('SEARCH_INDEXES'),
			7 => $eLang->get('LOCATION_WEATHER'),
			8 => $eLang->get('ADMINISTRATION'),
			9 => $eLang->get('SOCIAL_NETWORKS'),
			10 => $eLang->get('MENUS_NAVIGATION'),
			11 => $eLang->get('MOBILE_PHONES'),
			12 => $eLang->get('COMMUNICATION'),
			13 => $eLang->get('CONTENT'),
			14 => $eLang->get('FILE_MANAGEMENT'),
			15 => $eLang->get('EFFECTS'),
			16 => $eLang->get('ADVERTISING'),
			17 => $eLang->get('STATISTICS'),
			18 => $eLang->get('AUTH_USERS'),
			19 => $eLang->get('TEMPLATES'),
			20 => $eLang->get('ADMIN_TEMPLATES'),
			21 => $eLang->get('PUBLIC_OPINION'),
			22 => $eLang->get('BUSINESS')
		);

		if ($sort) { asort($categories); }
		$categories[23] = $eLang->get('MISCELLANEOUS'); //display it last!

		return $categories;
	}


	/***********************/
	/* GET CATEGORY'S NAME */
	/***********************/
	public function getCategoryName($catid) {
		$categories = $this->getCategories(false);
		return (isset($categories[$catid])) ? $categories[$catid] : 'Unknown';
	}


	/*****************************/
	/* GET EXTENSION'S TYPE NAME */
	/*****************************/
	public function getTypeName($type) {
		$eLang = eFactory::getLang();

		switch ($type) {
			case 'core': return $eLang->get('CORE'); break;
			case 'component': return $eLang->get('COMPONENT'); break;
			case 'module': return $eLang->get('MODULE'); break;
			case 'template': return $eLang->get('TEMPLATE'); break;
			case 'atemplate': return $eLang->get('TEMPLATE').' '.$eLang->get('BACKEND'); break;
			case 'auth': return $eLang->get('AUTH_METHOD'); break;
			case 'plugin': return $eLang->get('PLUGIN'); break;
			case 'engine': return $eLang->get('SEARCH_ENGINE'); break;
			case 'language': return $eLang->get('LANGUAGE'); break;
			case 'other': default: return $eLang->get('OTHER'); break;
		}
	}


	/*************************************************/
	/* GET EXTENSION'S TYPE ICON CLASS (FONTAWESOME) */
	/*************************************************/
	public function getTypeIconClass($type) {
		switch ($type) {
			case 'core': $fonticon = 'felxis-logo'; break;
			case 'component': $fonticon = 'fas fa-cube'; break;
			case 'module': $fonticon = 'fas fa-puzzle-piece'; break;
			case 'template': $fonticon = 'fas fa-paint-brush'; break;
			case 'atemplate': $fonticon = 'fas fa-paint-brush'; break;
			case 'auth': $fonticon = 'fas fa-key'; break;
			case 'plugin': $fonticon = 'fas fa-plug'; break;
			case 'engine': $fonticon = 'fas fa-search'; break;
			case 'engine': $fonticon = 'fas fa-language'; break;
			case 'other': default: $fonticon = 'fas fa-cube'; break;
		}

		return $fonticon;
	}


	/*****************************/
	/* INITIAL CONNECTION TO EDC */
	/*****************************/
	public function connect() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$out = array('edcauth' => '', 'error' => '');
		if (($elxis->getConfig('SECURITY_LEVEL') > 1) && ($this->subsite == true)) {
			 $out['error'] = $eLang->exist('SECLEV_EDC_NOALLOW') ? $eLang->get('SECLEV_EDC_NOALLOW') : 'Under the current security level accessing EDC from sub-sites is not allowed!';
			 return $out;
	 	}

		$rnd = rand(1, 1000);
		$v = $elxis->getVersion();
		$p = $elxis->fromVersion('PRODUCT');
		$url = base64_encode($elxis->getConfig('URL'));
		$url = preg_replace('@(\=)+$@', '', $url);
		$options = array('task' => 'auth', 'elxisid' => $this->elxisid, 'url' => $url, 'version'=> $v, 'product' => $p, 'rnd' => $rnd);
		if (function_exists('curl_init')) {
			$xmldata = $this->curlget($this->edc_url, $options);
		} else {
			$xmldata = $this->httpget($this->edc_url, $options);
		}
		if (!$xmldata) {
			$out['error'] = $eLang->exist('DATA_EDC_FAILED') ? $eLang->get('DATA_EDC_FAILED') : 'Data receive from EDC server failed!';;
			if ($this->errormsg != '') { $out['error'] .= ' - '.$this->errormsg; }
			return $out;
		}

        libxml_use_internal_errors(true);
        $xmlDoc = simplexml_load_string($xmldata, 'SimpleXMLElement');
		if (!$xmlDoc) {
			$out['error'] = 'Invalid response from EDC server!';
			return $out;
		}
		if ($xmlDoc->getName() != 'edc') {
			$out['error'] = 'Could not connect to EDC server!';
			return $out;
		}

		if (isset($xmlDoc->error) && (trim($xmlDoc->error) != '')) {
			$out['error'] = (string)$xmlDoc->error;
			return $out;
		}

		if (!isset($xmlDoc->edcauth)) {
			$out['error'] = $eLang->get('AUTH_FAILED');
			return $out;
		}

		$edcauth = (string)$xmlDoc->edcauth;
		$out['edcauth'] = trim($edcauth);
		$this->edcauth = $out['edcauth'];
		return $out;
	}


	/**********************/
	/* LOAD EDC FRONTPAGE */
	/**********************/
	public function getFrontpage($lng, $edcauth) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$out = array('blocks' => array(), 'rows' => array(), 'error' => '');
		if ($edcauth == '') { $out['error'] = $eLang->get('AUTH_FAILED'); return $out; }
		if (($elxis->getConfig('SECURITY_LEVEL') > 1) && ($this->subsite == true)) {
			 $out['error'] = $eLang->get('SECLEV_EDC_NOALLOW');
			 return $out;
	 	}

		$from_cache = false;
		if (file_exists($this->cache_path.'/edc/frontpage_'.$lng.'.php')) {
			$ts = filemtime($this->cache_path.'/edc/frontpage_'.$lng.'.php');
			if ($this->time - $ts <= $this->cache_frontpage) { $from_cache = true; }
		}

		if (!$from_cache) {
			$rnd = rand(1, 1000);
			$v = $elxis->getVersion();
			$options = array('task' => 'frontpage', 'elxisid' => $this->elxisid, 'lang' => $lng, 'edcauth' => $edcauth, 'version' => $v, 'rnd' => $rnd);
			if (function_exists('curl_init')) {
				$xmldata = $this->curlget($this->edc_url, $options);
			} else {
				$xmldata = $this->httpget($this->edc_url, $options);
			}
			if (!$xmldata) {
				$out['error'] = $eLang->get('DATA_EDC_FAILED');
				if ($this->errormsg != '') { $out['error'] .= ' - '.$this->errormsg; }
				return $out;
			}

			libxml_use_internal_errors(true);
			$xmlDoc = simplexml_load_string($xmldata, 'SimpleXMLElement');
			if (!$xmlDoc) {
				$out['error'] = 'Invalid response from EDC server!';
				return $out;
			}
			if ($xmlDoc->getName() != 'edc') {
				$out['error'] = 'Could not load frontpage content from EDC server!';
				return $out;
			}

			if (isset($xmlDoc->error) && (trim($xmlDoc->error) != '')) {
				$out['error'] = (string)$xmlDoc->error;
				return $out;
			}

			if (!isset($xmlDoc->blocks)) {
				$out['error'] = 'EDC server returned no frontpage content!';
				return $out;
			}

			if (count($xmlDoc->blocks->children()) == 0) {
				$out['error'] = 'EDC server returned no frontpage content!';
				return $out;
			}

			$blocks = array();
			$extensions = array();
			foreach ($xmlDoc->blocks->children() as $block) {
				$attrs = $block->attributes();
				if ($attrs && isset($attrs['type'])) {
					$contents = (string)$block;
					if (trim($contents) == '') { continue; }
					$blocks[] = array('type' => trim($attrs['type']), 'contents' => $contents);
				}
			}
			
			if (isset($xmlDoc->extensions)) {
				if (count($xmlDoc->extensions->children()) > 0) {
					foreach ($xmlDoc->extensions->children() as $extension) {
						$ext = $this->fetchExtension($extension);
						if (!$ext) { continue; }
						$id = $ext['id'];
						$extensions[$id] = $ext;
					}
				}
			}

			$this->cacheFrontpage($lng, $blocks, $extensions);
		}

		if (!isset($blocks)) {
			include($this->cache_path.'/edc/frontpage_'.$lng.'.php');
		}

		if (!isset($blocks) || !is_array($blocks)) { return $out; }
		if (count($blocks) == 0) { return $out; }

		if (!isset($extensions)) { $extensions = array(); }

		$out['blocks'] = $blocks;
		$out['rows'] = $extensions;
		return $out;
	}


	/*****************************/
	/* GET A CATEGORY'S LISTINGS */
	/*****************************/
	public function getCategory($options) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$out = array('total' => 0, 'page' => 1, 'maxpage' => 1, 'rows' => array(), 'ordering' => $this->edc_ordering, 'error' => '');
		if ($options['edcauth'] == '') { $out['error'] = $eLang->get('AUTH_FAILED'); return $out; }
		$catid = (int)$options['catid'];
		if ($catid < 1) { $out['error'] = 'Invalid category'; return $out; }
		if (($elxis->getConfig('SECURITY_LEVEL') > 1) && ($this->subsite == true)) {
			 $out['error'] = $eLang->get('SECLEV_EDC_NOALLOW');
			 return $out;
	 	}

		$from_cache = false;
		if (file_exists($this->cache_path.'/edc/category_'.$catid.'.php')) {
			$ts = filemtime($this->cache_path.'/edc/category_'.$catid.'.php');
			if ($this->time - $ts <= $this->cache_category) { $from_cache = true; }
		}

		if (!$from_cache) {
			$rnd = rand(1, 1000);
			$v = $elxis->getVersion();
			$roptions = array('task' => 'category', 'elxisid' => $this->elxisid, 'catid' => $catid, 'edcauth' => $options['edcauth'], 'version' => $v, 'rnd' => $rnd);
			if (function_exists('curl_init')) {
				$xmldata = $this->curlget($this->edc_url, $roptions);
			} else {
				$xmldata = $this->httpget($this->edc_url, $roptions);
			}
			if (!$xmldata) {
				$out['error'] = $eLang->get('DATA_EDC_FAILED');
				if ($this->errormsg != '') { $out['error'] .= ' - '.$this->errormsg; }
				return $out;
			}

        	libxml_use_internal_errors(true);
        	$xmlDoc = simplexml_load_string($xmldata, 'SimpleXMLElement');
			if (!$xmlDoc) {
				$out['error'] = 'Invalid response from EDC server!';
				return $out;
			}
			if ($xmlDoc->getName() != 'edc') {
				$out['error'] = 'Could not load category extensions from EDC server!';
				return $out;
			}

			if (isset($xmlDoc->error) && (trim($xmlDoc->error) != '')) {
				$out['error'] = (string)$xmlDoc->error;
				return $out;
			}

			if (!isset($xmlDoc->extension)) {
				$this->cacheCategory($catid, array());
				return $out;
			}

			if (count($xmlDoc->extension->children()) == 0) {
				$this->cacheCategory($catid, array());
				return $out;
			}

			$extensions = array();
			foreach ($xmlDoc->extension as $extension) {
				$ext = $this->fetchExtension($extension);
				if (!$ext) { continue; }
				$id = $ext['id'];
				$extensions[$id] = $ext;
			}
			$this->cacheCategory($catid, $extensions);
		}

		if (!isset($extensions)) {
			include($this->cache_path.'/edc/category_'.$catid.'.php');
		}
		if (!isset($extensions) || !is_array($extensions)) { return $out; }
		if (count($extensions) == 0) { return $out; }

		$rows = array();		
		foreach ($extensions as $id => $extension) {
			if ($options['fid'] > 0) {
				if ($extension['fid'] <> $options['fid']) { continue; }
			}
			$rows[] = $extension;
		}

		if (!$rows) { return $out; }
		unset($extensions);

		$total = count($rows);
		$page = $options['page'];
		if ($page < 1) { $page = 1; }
		$maxpage = ($total == 0) ? 1 : ceil($total/$this->edc_limit);
		if ($maxpage < 1) { $maxpage = 1; }
		if ($page > $maxpage) { $page = $maxpage; }
		$limitstart = (($page - 1) * $this->edc_limit);

		usort($rows, array($this, 'sortExtensions'));

		if ($total <= $this->edc_limit) {
			$out = array('total' => $total, 'page' => $page, 'maxpage' => $maxpage, 'rows' => $rows, 'ordering' => $this->edc_ordering, 'error' => '');
			return $out;
		}

		$page_rows = array();
		$end = $limitstart + $this->edc_limit;
		foreach ($rows as $key => $row) {
			if ($key < $limitstart) { continue; }
			if ($key >= $end) { break; }
			$page_rows[] = $row;
		}
		unset($rows);

		$out = array('total' => $total, 'page' => $page, 'maxpage' => $maxpage, 'rows' => $page_rows, 'ordering' => $this->edc_ordering, 'error' => '');
		return $out;
	}


	/*****************************************/
	/* GET EXTENSION'S FULL DETAILS FROM EDC */
	/*****************************************/
	public function getExtension($options) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$out = array('row' => array(), 'error' => '');

		if ($options['edcauth'] == '') { $out['error'] = $eLang->get('AUTH_FAILED'); return $out; }
		$id = (int)$options['id'];
		if ($id < 1) { $out['error'] = 'Invalid extension'; return $out; }

		if (($elxis->getConfig('SECURITY_LEVEL') > 1) && ($this->subsite == true)) {
			 $out['error'] = $eLang->get('SECLEV_EDC_NOALLOW');
			 return $out;
	 	}

		$from_cache = false;
		if (file_exists($this->cache_path.'/edc/extension_'.$id.'.xml')) {
			$ts = filemtime($this->cache_path.'/edc/extension_'.$id.'.xml');
			if ($this->time - $ts <= $this->cache_extension) { $from_cache = true; }
		}

		if (!$from_cache) {
			$rnd = rand(1, 1000);
			$v = $elxis->getVersion();
			$roptions = array('task' => 'view', 'elxisid' => $this->elxisid, 'id' => $id, 'edcauth' => $options['edcauth'], 'version' => $v, 'rnd' => $rnd);
			if (function_exists('curl_init')) {
				$xmldata = $this->curlget($this->edc_url, $roptions);
			} else {
				$xmldata = $this->httpget($this->edc_url, $roptions);
			}
			if (!$xmldata) {
				$out['error'] = $eLang->exist('DATA_EDC_FAILED') ? $eLang->get('DATA_EDC_FAILED') : 'Data receive from EDC server failed!';;
				if ($this->errormsg != '') { $out['error'] .= ' - '.$this->errormsg; }
				return $out;
			}

			eFactory::getFiles()->createFile('cache/edc/extension_'.$id.'.xml', $xmldata, true);
		} else {
			if (rand(1, 40) == 33) { $this->deleteExpired(); } //2,5% probability
		}

		libxml_use_internal_errors(true);
		if (!isset($xmldata)) {
			$xmlfile = $this->cache_path.'/edc/extension_'.$id.'.xml';
        	$xmlDoc = simplexml_load_file($xmlfile, 'SimpleXMLElement');
		} else {
        	$xmlDoc = simplexml_load_string($xmldata, 'SimpleXMLElement');
		}
		if (!$xmlDoc) {
			$out['error'] = 'Invalid response from EDC server!';
			return $out;
		}
		if ($xmlDoc->getName() != 'edc') {
			$out['error'] = 'Error loading extension from EDC server!';
			return $out;
		}
		if (isset($xmlDoc->error) && (trim($xmlDoc->error) != '')) {
			$out['error'] = (string)$xmlDoc->error;
			return $out;
		}

		if (!isset($xmlDoc->extension)) {
			$out['error'] = 'EDC response is not an extension!';
			return $out;
		}

		if (count($xmlDoc->extension->children()) == 0) {
			$out['error'] = 'EDC response is not an extension!';
			return $out;
		}

		$integers = array('id', 'catid', 'altcatid', 'fid', 'altfid', 'uid', 'downloads', 'published', 'verified');
		$links = array('icon', 'authorlink', 'link', 'buylink', 'licenseurl', 'image1', 'image2', 'image3', 'image4', 'image5', 'image6', 'demolink', 'doclink');

		$row = array();
		foreach ($xmlDoc->extension->children() as $k => $v) {
			$k = (string)$k;
			if (in_array($k, $integers)) {
				$row[$k] = intval(trim($v));
			} else if (in_array($k, $links)) {
				$link = trim($v);
				if ($link != '') {
					if (!preg_match('@^(https?\:\/\/)@i', $link)) { $link = ''; }
				}
				$row[$k] = $link;
			} else {
				$row[$k] = (string)$v;
			}
		}

		if (!isset($row['id'])) { $out['error'] = 'Missing or not acceptable value for id!'; return $out; }
		if (!isset($row['catid'])) { $out['error'] = 'Missing or not acceptable value for catid!'; return $out; }
		if (!isset($row['title'])) { $out['error'] = 'Missing or not acceptable value for title!'; return $out; }
		if (trim($row['title']) == '') {$out['error'] = 'Missing or not acceptable value for title!'; return $out; }
		if (!isset($row['type'])) { $out['error'] = 'Missing or not acceptable value for type!'; return $out; }
		if (!in_array($row['type'], array('core', 'component', 'module', 'template', 'atemplate', 'auth', 'plugin', 'engine', 'language', 'other'))) {
			$row['type'] = 'other';
		}
		if (!isset($row['created']) || (trim($row['created']) == '')) { $out['error'] = 'Missing or not acceptable value for created!'; return $out; }
		if (!is_numeric($row['created'])) { $out['error'] = 'Missing or not acceptable value for created!'; return $out; }
		if (!isset($row['modified']) || (trim($row['modified']) == '')) { $out['error'] = 'Missing or not acceptable value for modified!'; return $out; }
		if (!is_numeric($row['modified'])) { $out['error'] = 'Missing or not acceptable value for modified!'; return $out; }
		if (!isset($row['version']) || (trim($row['version']) == '')) { $out['error'] = 'Missing or not acceptable value for version!'; return $out; }
		if (!is_numeric($row['version'])) { $out['error'] = 'Missing or not acceptable value for version!'; return $out; }
		if (!isset($row['name'])) { $row['name'] = ''; }
		if (($row['name'] == '') && in_array($row['name'], array('component', 'module', 'template', 'atemplate', 'auth', 'plugin', 'engine'))) {
			$out['error'] = 'Missing or not acceptable value for name!'; return $out;
		}
		if (!isset($row['short'])) { $row['short'] = ''; }
		if (trim($row['short'] != '')) { $row['short'] = htmlspecialchars(eUTF::trim($row['short'])); }
		if (!isset($row['author'])) { $row['author'] = ''; }
		if (trim($row['author'] != '')) { $row['author'] = htmlspecialchars(eUTF::trim($row['author'])); }
		if (!isset($row['downloads'])) { $row['downloads'] = 0; }
		if (!isset($row['price'])) { $row['price'] = ''; }
		if (!isset($row['pcode'])) { $row['pcode'] = ''; }
		if (!isset($row['size'])) { $row['size'] = '0.00'; }
		if (is_numeric($row['size'])) { $row['size'] = number_format($row['size'], 2, '.', ''); } else { $row['size'] = 0.00; }
		if (!isset($row['category'])) { $row['category'] = $this->getCategoryName($row['catid']); }

		$out['row'] = $row;
		return $out;
	}


	/************************************/
	/* GET AUTHOR'S EXTENSIONS FROM EDC */
	/************************************/
	public function getAuthorExtensions($options) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$out = array('author' => array(), 'rows' => array(), 'error' => '');

		if ($options['edcauth'] == '') { $out['error'] = $eLang->get('AUTH_FAILED'); return $out; }
		$uid = (int)$options['uid'];
		if ($uid < 1) { $out['error'] = 'Invalid author'; return $out; }
		if (($elxis->getConfig('SECURITY_LEVEL') > 1) && ($this->subsite == true)) {
			 $out['error'] = $eLang->get('SECLEV_EDC_NOALLOW');
			 return $out;
	 	}

		$from_cache = false;
		if (file_exists($this->cache_path.'/edc/author_'.$uid.'.php')) {
			$ts = filemtime($this->cache_path.'/edc/author_'.$uid.'.php');
			if ($this->time - $ts <= $this->cache_category) { $from_cache = true; }
		}

		if (!$from_cache) {
			$rnd = rand(1, 1000);
			$v = $elxis->getVersion();
			$roptions = array('task' => 'author', 'elxisid' => $this->elxisid, 'uid' => $uid, 'edcauth' => $options['edcauth'], 'version' => $v, 'rnd' => $rnd);
			if (function_exists('curl_init')) {
				$xmldata = $this->curlget($this->edc_url, $roptions);
			} else {
				$xmldata = $this->httpget($this->edc_url, $roptions);
			}
			if (!$xmldata) {
				$out['error'] = $eLang->exist('DATA_EDC_FAILED') ? $eLang->get('DATA_EDC_FAILED') : 'Data receive from EDC server failed!';;
				if ($this->errormsg != '') { $out['error'] .= ' - '.$this->errormsg; }
				return $out;
			}

        	libxml_use_internal_errors(true);
        	$xmlDoc = simplexml_load_string($xmldata, 'SimpleXMLElement');
			if (!$xmlDoc) {
				$out['error'] = 'Invalid response from EDC server!';
				return $out;
			}
			if ($xmlDoc->getName() != 'edc') {
				$out['error'] = 'Could not load author extensions from EDC server!';
				return $out;
			}

			if (isset($xmlDoc->error) && (trim($xmlDoc->error) != '')) {
				$out['error'] = (string)$xmlDoc->error;
				return $out;
			}

			if (!isset($xmlDoc->author)) {
				$out['error'] = 'Author not found in EDC!';
				return $out;
			}

			if (count($xmlDoc->author->children()) == 0) {
				$out['error'] = 'Author not found in EDC!';
				return $out;
			}

			if (!isset($xmlDoc->extensions)) {
				$out['error'] = 'This author doesnt seem to own extensions in EDC!';
				return $out;
			}

			if (count($xmlDoc->extensions->children()) == 0) {
				$out['error'] = 'This author doesnt seem to own extensions in EDC!';
				return $out;
			}

			$author = $this->fetchAuthor($xmlDoc->author);
			if (!$author) {
				$out['error'] = 'Could not fetch author details from EDC!';
				return $out;
			}

			$extensions = array();
			foreach ($xmlDoc->extensions->children() as $extension) {
				$ext = $this->fetchExtension($extension);
				if (!$ext) { continue; }
				$id = $ext['id'];
				$extensions[$id] = $ext;
			}

			$this->cacheAuthor($uid, $author, $extensions);
		}

		if (!isset($author)) {
			include($this->cache_path.'/edc/author_'.$uid.'.php');
		}
		if (!isset($author) || !isset($extensions) || !is_array($author) || !is_array($extensions)) { return $out; }
		if (count($author) == 0) { return $out; }
		if (count($extensions) == 0) { return $out; }

		$out = array('author' => $author, 'rows' => $extensions, 'error' => '');
		return $out;
	}


	/*****************************/
	/* DOWNLOAD PACKAGE FROM EDC */
	/*****************************/
	public function downloadPackage($options) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$out = array('error' => 1, 'errormsg' => 'Unknown error', 'pack' => '');
		if ($this->subsite == true) {
			 $out['error'] = $eLang->get('SECLEV_EDC_NOALLOW');
			 return $out;
	 	}
		if ($options['edcauth'] == '') { $out['error'] = $eLang->get('AUTH_FAILED'); return $out; }
		if ($options['pcode'] == '') { $out['error'] = 'No Elxis package set!'; return $out; }

		$rnd = rand(100, 999);
		$zippack = 'package_'.date('YmdHis').'_'.$rnd.'.zip';
		$remotefile = $this->edc_url.'?task=package&edcauth='.$options['edcauth'].'&pcode='.$options['pcode'].'&rnd='.$rnd;
		$zippath = $this->repo_path.'/tmp/'.$zippack;

		if (function_exists('curl_init')) {
			$fw = @fopen($zippath, 'wb');
			if (!$fw) { $out['errormsg'] = 'Repository tmp folder is not writeable!'; return $out; }
			$ch = curl_init();

			$uagent = (isset($_SERVER['HTTP_USER_AGENT'])) ? $_SERVER['HTTP_USER_AGENT'] : 'Mozilla/5.0 (Windows NT 6.0; WOW64; rv:48.0) Gecko/20100101 Firefox/48.0)';
			curl_setopt($ch, CURLOPT_URL, $remotefile);
			curl_setopt($ch, CURLOPT_USERAGENT, $uagent);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_NOBODY, 0);
			curl_setopt($ch, CURLOPT_FAILONERROR, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
			curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        	curl_setopt($ch, CURLOPT_TIMEOUT, 28);
        	curl_setopt($ch, CURLOPT_REFERER, $elxis->getConfig('URL'));
    		curl_setopt($ch, CURLOPT_FILE, $fw);
			$data = curl_exec($ch);
			if (0 == curl_errno($ch)) {
            	curl_close($ch);
				fclose($fw);
				$out['error'] = 0;
				$out['errormsg'] = '';
				$out['pack'] = $zippack;
			} else {
				curl_close($ch);
				$out['errormsg'] = $eLang->get('DLPACK_EDC_FAILED');
			}

			return $out;
		}

		$parsed = parse_url($remotefile);
		$port = ($parsed['scheme'] == 'https') ? 443 : 80;
		$getstr = (isset($parsed['path'])) ? $parsed['path'] : '/';
		$getstr .= (isset($parsed['query'])) ? '?'.$parsed['query'] : '';

		$fp = @fsockopen($parsed['host'], $port, $errno, $errstr, 28);
       	if (!$fp) { $out['errormsg'] = 'Could not access EDC!'; return $out; }
 	
		$req = "GET ".$getstr." HTTP/1.1\r\n";
		$req .= "Host: ".$parsed['host']."\r\n";
		$req .= "Referer: ".$elxis->getConfig('URL')."\r\n";
		$req .= 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:65.0) Gecko/20100101 Firefox/65.0'."\r\n";
    	$req .= 'Accept-Language: en-us,en;q=0.5'."\r\n";
    	$req .= 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7'."\r\n";
		$req .= "Connection: Close\r\n\r\n";
        $response = '';
        fwrite($fp, $req);
        while (!feof($fp)) { $response .= fread($fp, 1024); }
        fclose($fp);
  
		$http_response_header = array();
		if (stripos($response, "\r\n\r\n") !== false) {
			$hc = explode("\r\n\r\n", $response);
			$headers = explode("\r\n", $hc[0]);
            if (!is_array($headers)) { $headers = array(); }
			if ($headers) {
				foreach($headers as $key => $header) {
					$a = "";
                    $b = "";
                    if (stripos($header, ":") !== false) {
                       	list($a, $b) = explode(":", $header);
                       	$http_response_header[trim($a)] = trim($b);
                    }
                }
            }
			$output = end($hc);
        } elseif (stripos($response, "\r\n") !== false) {
			$headers = explode("\r\n",  $response);
			if (!is_array($headers)) { $headers = array(); }
            if ($headers) {
				foreach($headers as $key => $header){
                    if($key < (count($headers) - 1)) {
                        $a = "";
                        $b = "";
                        if (stripos($header, ":") !== false) {
                            list($a, $b) = explode(":", $header);
                            $http_response_header[trim($a)] = trim($b);
                        }
                    }
                }
            }
			$output = end($headers);
        } else {
			$output = $response;
		}

		$fw = @fopen($zippath, 'wb');
		if (!$fw) { $out['errormsg'] = $eLang->get('DLPACK_EDC_FAILED'); return $out; }
		fwrite($fw, $output);
		fclose($fw);

		$out['error'] = 0;
		$out['errormsg'] = '';
		$out['pack'] = $zippack;
		return $out;
	}


	/****************************/
	/* SEARCH EXTENSIONS IN EDC */
	/****************************/
	public function searchExtensions($options) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$out = array('keyword' => '', 'rows' => array(), 'error' => '');

		if ($options['edcauth'] == '') { $out['error'] = $eLang->get('AUTH_FAILED'); return $out; }
		if ($options['keyword'] == '') {
			 $out['error'] = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('KEYWORD'));
			 return $out;
		}
		if (eUTF::strlen($options['keyword']) < 4) {
			 $out['error'] = $eLang->get('KEYWORD_LENGTH');
			 return $out;
		}

		$out['keyword'] = $options['keyword'];

		$rnd = rand(1, 1000);
		$v = $elxis->getVersion();
		$roptions = array('task' => 'search', 'elxisid' => $this->elxisid, 'keyword' => $options['keyword'], 'edcauth' => $options['edcauth'], 'version' => $v, 'rnd' => $rnd);
		if (function_exists('curl_init')) {
			$xmldata = $this->curlget($this->edc_url, $roptions);
		} else {
			$xmldata = $this->httpget($this->edc_url, $roptions);
		}
		if (!$xmldata) {
			$out['error'] = $eLang->exist('DATA_EDC_FAILED') ? $eLang->get('DATA_EDC_FAILED') : 'Data receive from EDC server failed!';;
			if ($this->errormsg != '') { $out['error'] .= ' - '.$this->errormsg; }
			return $out;
		}

        libxml_use_internal_errors(true);
        $xmlDoc = simplexml_load_string($xmldata, 'SimpleXMLElement');
		if (!$xmlDoc) {
			$out['error'] = 'Invalid response from EDC server!';
			return $out;
		}
		if ($xmlDoc->getName() != 'edc') {
			$out['error'] = 'Could not load author extensions from EDC server!';
			return $out;
		}

		if (isset($xmlDoc->error) && (trim($xmlDoc->error) != '')) {
			$out['error'] = (string)$xmlDoc->error;
			return $out;
		}

		if (!isset($xmlDoc->extensions)) {
			$out['error'] = $eLang->exist('NO_EXTS_FOUND');
			return $out;
		}

		if (count($xmlDoc->extensions->children()) == 0) {
			$out['error'] = $eLang->exist('NO_EXTS_FOUND');
			return $out;
		}

		$extensions = array();
		foreach ($xmlDoc->extensions->children() as $extension) {
			$ext = $this->fetchExtension($extension);
			if (!$ext) { continue; }
			$id = $ext['id'];
			$extensions[$id] = $ext;
		}

		if (count($extensions) == 0) {
			$out['error'] = $eLang->exist('NO_EXTS_FOUND');
			return $out;
		}

		$out['rows'] = $extensions;
		return $out;
	}


	/***********************************************/
	/* RATE EXTENSION (DEPRECATED AS OF ELXIS 5.1) */
	/***********************************************/
	public function rateExtension($options) {
		$out = array('success' => false, 'error' => 'Rating extensions is deprecated as of Elxis 5.1');
		return $out;
	}


	/*************************************************/
	/* REPORT EXTENSION (DEPRECATED AS OF ELXIS 5.1) */
	/*************************************************/
	public function reportExtension($options) {
		$out = array('success' => false, 'error' => 'Reporting extensions is deprecated as of Elxis 5.1');
		return $out;
	}


	/***********************************************************/
	/* REGISTER SITE AT ELXIS.ORG (DEPRECATED AS OF ELXIS 5.1) */
	/***********************************************************/
	public function registerSite($options, $comparams) {
		$out = array('elxisid' => '', 'error' => 'Site registration is deprecated as of Elxis 5.1', 'newparams' => '');
		return $out;
	}


	/*********************/
	/* ORDER EXTENSIONS */
	/*********************/
	private function sortExtensions($a, $b) {
		if ($this->edc_ordering == 'c') {
			if ($a['created'] == $b['created']) { return 0; }
			return ($a['created'] < $b['created']) ? 1 : -1;
		}else if ($this->edc_ordering == 'm') {
			if ($a['modified'] == $b['modified']) { return 0; }
			return ($a['modified'] < $b['modified']) ? 1 : -1;
		} else if ($this->edc_ordering == 'd') {
			if ($a['downloads'] == $b['downloads']) { return 0; }
			return ($a['downloads'] < $b['downloads']) ? 1 : -1;
		} else if ($this->edc_ordering == 'a') {
			return strcasecmp($a['downloads'], $b['downloads']);
		//} else if ($this->edc_ordering == 'r') {
		//	if ($a['rating'] == $b['rating']) { return 0; }
		//	return ($a['rating'] < $b['rating']) ? 1 : -1;
		} else {
			return 0;
		}
	}


	/*********************************************/
	/* FETCH EXTENSION'S DETAILS FROM XML OBJECT */
	/*********************************************/
	private function fetchExtension($extension) {
		if (!isset($extension->id)) { return false; }
		$id = (int)$extension->id;
		if ($id < 1) { return false; }
		if (!isset($extension->catid)) { return false; }
		$catid = (int)$extension->catid;
		if ($catid < 1) { return false; }
		$fid = 0;
		if (isset($extension->fid)) { $fid = (int)$extension->fid; }

		$row = array();
		$row['id'] = $id;
		$row['catid'] = $catid;
		$row['fid'] = $fid;
		$row['type'] = 'other';
		$row['category'] = 'Unknown';
		$row['title'] = '';
		$row['name'] = '';
		$row['short'] = '';
		$row['icon'] = '';
		$row['uid'] = 0;
		$row['author'] = 'Unknown';
		$row['authorlink'] = '';
		$row['version'] = 0;
		$row['created'] = 0;
		$row['modified'] = 0;
		$row['link'] = '';
		$row['price'] = '';
		$row['buylink'] = '';
		$row['license'] = 'Unknown';
		$row['licenseurl'] = '';
		$row['downloads'] = 0;
		$row['pcode'] = '';

		if (isset($extension->type) && (trim($extension->type) != '')) {
			$row['type'] = (string)$extension->type;
			if (!in_array($row['type'], array('core', 'component', 'module', 'template', 'atemplate', 'auth', 'plugin', 'engine', 'language', 'other'))) {
				$row['type'] = 'other';
			}
		}

		if (!isset($extension->title)) { return false; }
		$row['title'] = (string)$extension->title;
		if (trim($row['title']) == '') { return false; }
		if (!isset($extension->created) || (trim($extension->created) == '')) { return false; }
		$row['created'] = (string)$extension->created;
		if (!is_numeric($row['created'])) { return false; }
		if (!isset($extension->modified) || (trim($extension->modified) == '')) { return false; }
		$row['modified'] = (string)$extension->modified;
		if (!is_numeric($row['modified'])) { return false; }
		if (!isset($extension->version)) { return false; }
		$row['version'] = (string)$extension->version;
		if (($row['version'] == '') || !is_numeric($row['version'])) { return false; }
		if (!isset($extension->uid)) { return false; }
		$row['uid'] = (int)$extension->uid;
		if ($row['uid'] < 1) { return false; }
		if (isset($extension->name)) { $row['name'] = trim($extension->name); }
		if (($row['name'] == '') && in_array($row['type'], array('component', 'module', 'template', 'atemplate', 'auth', 'plugin', 'engine'))) { return false; }
		if (isset($extension->short) && (trim($extension->short) != '')) { $row['short'] = htmlspecialchars(eUTF::trim($extension->short)); }
		if (isset($extension->icon) && (strpos(trim($extension->icon), 'http') === 0)) { $row['icon'] = (string)$extension->icon; }
		if (isset($extension->author) && (trim($extension->author) != '')) { $row['author'] = htmlspecialchars(eUTF::trim($extension->author)); }
		if (isset($extension->authorlink) && (strpos(trim($extension->authorlink), 'http') === 0)) { $row['authorlink'] = (string)$extension->authorlink; }
		if (isset($extension->link) && (strpos(trim($extension->link), 'http') === 0)) { $row['link'] = (string)$extension->link; }
		if (isset($extension->buylink) && (strpos(trim($extension->buylink), 'http') === 0)) { $row['buylink'] = (string)$extension->buylink; }
		if (isset($extension->license) && (trim($extension->license) != '')) { $row['license'] = (string)$extension->license; }
		if (isset($extension->licenseurl) && (strpos(trim($extension->licenseurl), 'http') === 0)) { $row['licenseurl'] = (string)$extension->licenseurl; }
		if (isset($extension->price) && (trim($extension->price) != '')) { $row['price'] = trim($extension->price); }
		if (isset($extension->downloads)) { $row['downloads'] = (int)$extension->downloads; }
		if (isset($extension->pcode) && (trim($extension->pcode) != '')) { $row['pcode'] = trim($extension->pcode); }
		$row['category'] = $this->getCategoryName($row['catid']);

		return $row;
	}


	/******************************************/
	/* FETCH AUTHOR'S DETAILS FROM XML OBJECT */
	/******************************************/
	private function fetchAuthor($author) {
		if (!isset($author->uid)) { return false; }
		$uid = (int)$author->uid;
		if ($uid < 1) { return false; }

		$row = array();
		$row['uid'] = $uid;
		$row['name'] = '';
		$row['avatar'] = '';
		$row['country'] = '';
		$row['city'] = '';
		$row['website'] = '';

		if (!isset($author->name)) { return false; }
		$row['name'] = (string)$author->name;
		if (trim($row['name']) == '') { return false; }
		if (isset($author->avatar) && (strpos(trim($author->avatar), 'http') === 0)) { $row['avatar'] = (string)$author->avatar; }
		if (isset($author->country) && (trim($author->country) != '')) { $row['country'] = trim($author->country); }
		if (isset($author->city) && (trim($author->city) != '')) { $row['city'] = trim($author->city); }
		if (isset($author->website) && (strpos(trim($author->website), 'http') === 0)) { $row['website'] = (string)$author->website; }

		return $row;
	}


	/******************************************/
	/* WRITE CATEGORY'S EXTENSIONS INTO CACHE */
	/******************************************/
	private function cacheCategory($catid, $extensions) {
		$total_ext = count($extensions);
		$contents = '<?php '."\n";
		$contents .= '//Elxis Cache file generated on '.gmdate('Y-m-d H:i:s').' GMT'."\n\n";
		$contents .= 'defined(\'_ELXIS_\') or die (\'Direct access to this location is not allowed\');'."\n\n";
		$contents .= '$extensions = array('."\n";
		if ($total_ext > 0) {
			$x = 1;
			$n = -1;
			foreach ($extensions as $extension) {
				$id = $extension['id'];
				if ($n == -1) { $n = count($extension); }
				$i = 1;
				$contents .= "\t".$id.' => array(';
				foreach($extension as $key => $val) {
					if ($val === '') {
						$contents .= '\''.$key.'\' => \'\'';
					} else if ($key == 'version') {
						$contents .= '\''.$key.'\' => \''.$val.'\'';
					} else if (is_numeric($val)) {
						$contents .= '\''.$key.'\' => '.$val;
					} else {
						$contents .= '\''.$key.'\' => \''.addslashes($val).'\'';
					}
					
					if ($i < $n) { $contents .= ', '; }
					$i++;
				}

				$contents .= ($x == $total_ext) ? ")\n" : "),\n";
				$x++;
			}
		}
		$contents .= ');'."\n\n";
		$contents .= '?>';

		$ok = eFactory::getFiles()->createFile('cache/edc/category_'.$catid.'.php', $contents, true);
		return $ok;
	}


	/****************************************/
	/* WRITE AUTHOR'S EXTENSIONS INTO CACHE */
	/****************************************/
	private function cacheAuthor($uid, $author, $extensions) {
		$total_ext = count($extensions);

		$contents = '<?php '."\n";
		$contents .= '//Elxis Cache file generated on '.gmdate('Y-m-d H:i:s').' GMT'."\n\n";
		$contents .= 'defined(\'_ELXIS_\') or die (\'Direct access to this location is not allowed\');'."\n\n";

		$contents .= '$author = array('."\n";
		$contents .= "\t".'\'uid\' => '.$uid.','."\n";
		$contents .= "\t".'\'name\' => \''.addslashes($author['name']).'\','."\n";
		$contents .= "\t".'\'avatar\' => \''.addslashes($author['avatar']).'\','."\n";
		$contents .= "\t".'\'country\' => \''.addslashes($author['country']).'\','."\n";
		$contents .= "\t".'\'city\' => \''.addslashes($author['city']).'\','."\n";
		$contents .= "\t".'\'website\' => \''.addslashes($author['website']).'\''."\n";
		$contents .= ');'."\n\n";

		$contents .= '$extensions = array('."\n";
		if ($total_ext > 0) {
			$x = 1;
			$n = 0;
			foreach ($extensions as $ext) {
    			if ($n == 0) { $n = count($ext); }
				$contents .= "\t".$ext['id'].' => array(';
				$q = 1;
				foreach ($ext as $key => $val) {
					if ($val === '') {
						$contents .= '\''.$key.'\' => \'\'';
					} else if ($key == 'version') {
						$contents .= '\''.$key.'\' => \''.$val.'\'';
					} else if (is_numeric($val)) {
						$contents .= '\''.$key.'\' => '.$val;
					} else {
						$contents .= '\''.$key.'\' => \''.addslashes($val).'\'';
					}
					if ($q < $n) { $contents .= ', '; }
					$q++;
				}
				$contents .= ($x == $total_ext) ? ")\n" : "),\n";
				$x++;
			}
		}
		$contents .= ');'."\n\n";
		$contents .= '?>';

		$ok = eFactory::getFiles()->createFile('cache/edc/author_'.$uid.'.php', $contents, true);
		return $ok;
	}


	/******************************/
	/* WRITE FRONTPAGE INTO CACHE */
	/******************************/
	private function cacheFrontpage($lng, $blocks, $extensions) {
		$total_blk = count($blocks);
		$total_ext = count($extensions);
		$contents = '<?php '."\n";
		$contents .= '//Elxis Cache file generated on '.gmdate('Y-m-d H:i:s').' GMT'."\n\n";
		$contents .= 'defined(\'_ELXIS_\') or die (\'Direct access to this location is not allowed\');'."\n\n";
		$contents .= '$blocks = array('."\n";
		if ($total_blk > 0) {
			$x = 1;
			foreach ($blocks as $block) {
				$contents .= "\t".'array(\'type\' => \''.$block['type'].'\', \'contents\' => \''.addslashes($block['contents']).'\')';
				$contents .= ($x == $total_blk) ? "\n" : ",\n";
				$x++;
			}
		}
		$contents .= ');'."\n\n";
		$contents .= '$extensions = array('."\n";
		if ($total_ext > 0) {
			$x = 1;
			$n = -1;
			foreach ($extensions as $extension) {
				$id = $extension['id'];
				if ($n == -1) { $n = count($extension); }
				$i = 1;
				$contents .= "\t".$id.' => array(';
				foreach($extension as $key => $val) {
					if ($val === '') {
						$contents .= '\''.$key.'\' => \'\'';
					} else if ($key == 'version') {
						$contents .= '\''.$key.'\' => \''.$val.'\'';
					} else if (is_numeric($val)) {
						$contents .= '\''.$key.'\' => '.$val;
					} else {
						$contents .= '\''.$key.'\' => \''.addslashes($val).'\'';
					}
					
					if ($i < $n) { $contents .= ', '; }
					$i++;
				}

				$contents .= ($x == $total_ext) ? ")\n" : "),\n";
				$x++;
			}
		}
		$contents .= ');'."\n\n";
		$contents .= '?>';

		$ok = eFactory::getFiles()->createFile('cache/edc/frontpage_'.$lng.'.php', $contents, true);
		return $ok;
	}


	/***********************************/
	/* WRITE ALL EXTENSIONS INTO CACHE */
	/***********************************/
	private function cacheAllExtensions($extensions) {
		$total_ext = count($extensions);
		$contents = '<?php '."\n";
		$contents .= '//Elxis Cache file generated on '.gmdate('Y-m-d H:i:s').' GMT'."\n\n";
		$contents .= 'defined(\'_ELXIS_\') or die (\'Direct access to this location is not allowed\');'."\n\n";
		$contents .= '$extensions = array('."\n";
		if ($total_ext > 0) {
			$x = 1;
			$n = -1;
			foreach ($extensions as $extension) {
				$id = $extension['id'];
				if ($n == -1) { $n = count($extension); }
				$i = 1;
				$contents .= "\t".$id.' => array(';
				foreach($extension as $key => $val) {
					if ($val === '') {
						$contents .= '\''.$key.'\' => \'\'';
					} else if ($key == 'version') {
						$contents .= '\''.$key.'\' => \''.$val.'\'';
					} else if (is_numeric($val)) {
						$contents .= '\''.$key.'\' => '.$val;
					} else {
						$contents .= '\''.$key.'\' => \''.addslashes($val).'\'';
					}
					
					if ($i < $n) { $contents .= ', '; }
					$i++;
				}

				$contents .= ($x == $total_ext) ? ")\n" : "),\n";
				$x++;
			}
		}
		$contents .= ');'."\n\n";
		$contents .= '?>';

		$ok = eFactory::getFiles()->createFile('cache/edc/extensions_all.php', $contents, true);
		return $ok;
	}


	/*******************************/
	/* HTTP GET REQUEST USING CURL */
	/*******************************/
	private function curlget($url, $params=null) {
		$ch = curl_init();
		if ($params) {
			curl_setopt($ch, CURLOPT_URL, $url.'?'.http_build_query($params, '', '&')); //url encodes the data, PHP 5.1.2+
		} else {
			curl_setopt($ch, CURLOPT_URL, $url);
		}

		$uagent = (isset($_SERVER['HTTP_USER_AGENT'])) ? $_SERVER['HTTP_USER_AGENT'] : 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:65.0) Gecko/20100101 Firefox/65.0';
		curl_setopt($ch, CURLOPT_USERAGENT, $uagent);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_NOBODY, false);
		curl_setopt($ch, CURLOPT_FAILONERROR, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
		curl_setopt($ch, CURLOPT_REFERER, eFactory::getElxis()->getConfig('URL'));
		$result = curl_exec($ch);
		if (0 == curl_errno($ch)) {
			curl_close($ch);
			return $result;
		} else {
			$this->errormsg = curl_error($ch);//TODO
			curl_close($ch);
			return false;
		}
	}


	/************************************/
	/* HTTP GET REQUEST USING FSOCKOPEN */
	/************************************/
	private function httpget($url, $params=null) {
		$parseurl = parse_url($url);
		if ($params) {
			$parr = array();
			foreach($params as $key => $val) { $parr[] = $key.'='.urlencode($val); }
			$getstr = implode('&', $parr);
			$req = 'GET '.$parseurl['path'].'?'.$getstr." HTTP/1.1\r\n";
			unset($parr, $getstr);
		} else {
			$req = 'GET '.$parseurl['path']." HTTP/1.1\r\n";
		}

		$req .= 'Host: '.$parseurl['host']."\r\n";
		$req .= "Referer: ".eFactory::getElxis()->getConfig('URL')."\r\n";
		$req .= 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:65.0) Gecko/20100101 Firefox/65.0'."\r\n";
    	$req .= 'Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,*/*;q=0.6'."\r\n";
    	$req .= 'Accept-Language: en-us,en;q=0.5'."\r\n";
    	$req .= 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7'."\r\n";
		$req .= "Connection: Close\r\n\r\n"; 

		if (!isset($parseurl['port'])) {
			$parseurl['port'] = ($parseurl['scheme'] == 'https') ? 443 : 80;
		}

		$fp = fsockopen($parseurl['host'], $parseurl['port'], $errno, $errstr, 20);
		if (!$fp) { return false; }
		stream_set_timeout($fp, 15);
		fputs($fp, $req);
		$raw = '';
		while(!feof($fp)) {
			$raw .= fgets($fp);
			$info = stream_get_meta_data($fp);
			if ($info['timed_out']) {
				fclose($fp);
				return false;
			}
		}
		fclose($fp);
		$result = '';
		$chunked = false;
		if ($raw != '') {
			$expl = preg_split("/(\r\n){2,2}/", $raw, 2);
			$result = $expl[1];
			if (preg_match('/Transfer\\-Encoding:\\s+chunked/i',$expl[0])) { $chunked = true; }
			unset($expl);
		}
		unset($raw);

		if ($chunked) {
			$result = $this->decodeChunked($result);
		}
		return $result;
	}


	/***************************/
	/* DECODE A CHUNKED STRING */
	/***************************/
	private function decodeChunked($chunk) {
		if (function_exists('http_chunked_decode')) {
			return http_chunked_decode($chunk);
		}

		$pos = 0;
		$len = strlen($chunk);
		$dechunk = null;
		while(($pos < $len) && ($chunkLenHex = substr($chunk,$pos, ($newlineAt = strpos($chunk,"\n",$pos+1))-$pos))) {
            if (!$this->is_hex($chunkLenHex)) { return $chunk; }
			$pos = $newlineAt + 1;
            $chunkLen = hexdec(rtrim($chunkLenHex,"\r\n"));
            $dechunk .= substr($chunk, $pos, $chunkLen);
            $pos = strpos($chunk, "\n", $pos + $chunkLen) + 1;
        }

		return $dechunk;
	}


	/****************************/
	/* IS STRING A HEX NUMBER ? */
	/****************************/
	private function is_hex($hex) {
		$hex = strtolower(trim(ltrim($hex,"0")));
		if (empty($hex)) { $hex = 0; };
		$dec = hexdec($hex);
		return ($hex == dechex($dec));
	}


	/**********************/
	/* GLOBAL PERMISSIONS */
	/**********************/
	public function permissions() {
		$elxis = eFactory::getElxis();

		$perms = array();
		if (($elxis->getConfig('SECURITY_LEVEL') > 0) && ($elxis->user()->gid <> 1)) {
			$perms['component_install'] = false;
			$perms['module_install'] = false;
			$perms['template_install'] = false;
			$perms['engine_install'] = false;
			$perms['auth_install'] = false;
			$perms['plugin_install'] = false;
		} else if (defined('ELXIS_MULTISITE') && (ELXIS_MULTISITE > 1)) {
			$perms['component_install'] = false;
			$perms['module_install'] = false;
			$perms['template_install'] = false;
			$perms['engine_install'] = false;
			$perms['auth_install'] = false;
			$perms['plugin_install'] = false;
		} else {
			$perms['component_install'] = ($elxis->acl()->check('com_extmanager', 'components', 'install') > 0) ? true : false;
			$perms['module_install'] = ($elxis->acl()->check('com_extmanager', 'modules', 'install') > 0) ? true : false;
			$perms['template_install'] = ($elxis->acl()->check('com_extmanager', 'templates', 'install') > 0) ? true : false;
			$perms['engine_install'] = ($elxis->acl()->check('com_extmanager', 'engines', 'install') > 0) ? true : false;
			$perms['auth_install'] = ($elxis->acl()->check('com_extmanager', 'auth', 'install') > 0) ? true : false;
			$perms['plugin_install'] = ($elxis->acl()->check('com_extmanager', 'plugins', 'install') > 0) ? true : false;
		}
		return $perms;
	}


	/***************************************/
	/* EXTENSIONS SPECIFIC ALLOWED ACTIONS */
	/***************************************/
	public function extActions($row, $perms) {
		$actions = array('install' => false, 'update' => false, 'download' => false, 'buy' => false, 'is_installed' => false, 'is_updated' => false);

		if ($row['pcode'] != '') {
			$actions['download'] = true;
		} else {
			if ($row['price'] != '') { $actions['buy'] = true; }
		}

		switch ($row['type']) {
			case 'component':
				$comp = preg_replace('/^(com\_)/', '', $row['name']);
				if (file_exists(ELXIS_PATH.'/components/'.$row['name'].'/'.$comp.'.php')) { $actions['is_installed'] = true; }
				$can_install = $perms['component_install'];
			break;
			case 'module':
				if (file_exists(ELXIS_PATH.'/modules/'.$row['name'].'/'.$row['name'].'.php')) { $actions['is_installed'] = true; }
				$can_install = $perms['module_install'];
			break;
			case 'atemplate':
				if (file_exists(ELXIS_PATH.'/templates/admin/'.$row['name'].'/index.php')) { $actions['is_installed'] = true; }
				$can_install = $perms['template_install'];
			break;
			case 'template':
				if (file_exists(ELXIS_PATH.'/templates/'.$row['name'].'/index.php')) { $actions['is_installed'] = true; }
				$can_install = $perms['template_install'];
			break;
			case 'engine':
				if (file_exists(ELXIS_PATH.'/components/com_search/engines/'.$row['name'].'/'.$row['name'].'.engine.php')) { $actions['is_installed'] = true; }
				$can_install = $perms['engine_install'];
			break;
			case 'auth':
				if (file_exists(ELXIS_PATH.'/components/com_user/auth/'.$row['name'].'/'.$row['name'].'.auth.php')) { $actions['is_installed'] = true; }
				$can_install = $perms['auth_install'];
			break;
			case 'plugin':
				if (file_exists(ELXIS_PATH.'/components/com_content/plugins/'.$row['name'].'/'.$row['name'].'.plugin.php')) { $actions['is_installed'] = true; }
				$can_install = $perms['plugin_install'];
			break;
			case 'language':
				$can_install = false;
			break;
			case 'other':
				$can_install = false;
				if ($row['name'] != '') {
					$name = strtolower($row['name']);
					if ($name == 'iosrentals') { $name = 'rentals'; }
					if (in_array($name, array('hotels', 'rentals', 'cars'))) {
						if (file_exists(ELXIS_PATH.'/components/com_reservations/ext/'.$name.'/'.$name.'.iosr.php')) { $actions['is_installed'] = true; }
					}
				}
			break;
			case 'core':
				$can_install = false;
				$row['is_installed'] = true;
			break;
			default:
				$can_install = false;
			break;
		}

		if (($row['type'] == 'core') || ($row['type'] == 'other') || ($row['type'] == 'language')) { 
			return $actions;
		}

		if ($can_install && ($row['pcode'] != '')) {
			if ($actions['is_installed'] == true) {
				$actions['update'] = true;
				if ($this->edc_vcheck) {
					$actions['update'] = false;
					elxisLoader::loadFile('components/com_extmanager/includes/extension.xml.php');
					$exml = new extensionXML();
					$info = $exml->quickXML($row['type'], $row['name']);
					$iversion = $info['version'];
					unset($exml, $info);
					if ($iversion > 0) {
						if ($iversion < $row['version']) {
							$actions['update'] = true;
						}
						if ($iversion == $row['version']) {
							$actions['is_updated'] = true;
						}
					}
				}
			} else {
				$actions['install'] = true;
			}
		} else if ($actions['is_installed'] == true) {
			if ($this->edc_vcheck) {
				elxisLoader::loadFile('components/com_extmanager/includes/extension.xml.php');
				$exml = new extensionXML();
				$info = $exml->quickXML($row['type'], $row['name']);
				$iversion = $info['version'];
				unset($exml, $info);
				if ($iversion > 0) {
					if ($iversion == $row['version']) { $actions['is_updated'] = true; }
				}
			}
		}

		return $actions;
	}


	/************************************/
	/* DELETE EXPIRED CACHED EXTENSIONS */
	/************************************/
	private function deleteExpired() {
		$eFiles = eFactory::getFiles();

		$filter = '^(extension\_)';
		$files = $eFiles->listFiles('cache/edc/', $filter, false, false, true);
		if (!$files) { return; }
		foreach ($files as $file) {
			$ts = filemtime($this->cache_path.'/edc/'.$file);
			if ($this->time - $ts > $this->cache_extension) {
				$eFiles->deleteFile('cache/edc/'.$file, true);
			}
		}
	}


	/**************************/
	/* GET ALL EDC EXTENSIONS */
	/**************************/
	public function getAllExtensions() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$out = array('rows' => array(), 'error' => '');

		if (($elxis->getConfig('SECURITY_LEVEL') > 1) && ($this->subsite == true)) {
			$out['error'] = $eLang->exist('SECLEV_EDC_NOALLOW') ? $eLang->get('SECLEV_EDC_NOALLOW') : 'Under the current security level accessing EDC from sub-sites is not allowed!';
			return $out;
	 	}

		$from_cache = false;
		if (file_exists($this->cache_path.'/edc/extensions_all.php')) {
			$ts = filemtime($this->cache_path.'/edc/extensions_all.php');
			if ($this->time - $ts <= $this->cache_frontpage) { $from_cache = true; }
		}

		if (!$from_cache) {
			if (trim($this->edcauth) == '') {
				$con_response = $this->connect();
				if ($con_response['error'] != '') {
					$out['error'] = $con_response['error'];
					return $out;
				}
				if ($con_response['edcauth'] == '') {
					$lng_authfailed = $eLang->exist('AUTH_FAILED') ? $eLang->get('AUTH_FAILED') : 'Authorization failed.';
					$out['error'] = 'EDC '.$eLang->get('ERROR').' - '.$lng_authfailed;
					return $out;
				}
			}

			$rnd = rand(1, 1000);
			$v = $elxis->getVersion();
			$roptions = array('task' => 'allexts', 'elxisid' => $this->elxisid, 'edcauth' => $this->edcauth, 'version' => $v, 'rnd' => $rnd);
			if (function_exists('curl_init')) {
				$xmldata = $this->curlget($this->edc_url, $roptions);
			} else {
				$xmldata = $this->httpget($this->edc_url, $roptions);
			}
			if (!$xmldata) {
				$out['error'] = $eLang->exist('DATA_EDC_FAILED') ? $eLang->get('DATA_EDC_FAILED') : 'Data receive from EDC server failed!';
				if ($this->errormsg != '') { $out['error'] .= ' - '.$this->errormsg; }
				return $out;
			}

        	libxml_use_internal_errors(true);
        	$xmlDoc = simplexml_load_string($xmldata, 'SimpleXMLElement');
			if (!$xmlDoc) {
				$out['error'] = 'Invalid response from EDC server!';
				return $out;
			}
			if ($xmlDoc->getName() != 'edc') {
				$out['error'] = 'Could not load extensions from EDC server!';
				return $out;
			}

			if (isset($xmlDoc->error) && (trim($xmlDoc->error) != '')) {
				$out['error'] = (string)$xmlDoc->error;
				return $out;
			}

			if (!isset($xmlDoc->extension)) { return $out; }

			if (count($xmlDoc->extension->children()) == 0) { return $out; }

			$extensions = array();
			foreach ($xmlDoc->extension as $extension) {
				$ext = $this->fetchUpExtension($extension);
				if (!$ext) { continue; }
				$id = $ext['id'];
				$extensions[$id] = $ext;
			}
			$this->cacheAllExtensions($extensions);
		}

		if (!isset($extensions)) {
			include($this->cache_path.'/edc/extensions_all.php');
		}
		if (!isset($extensions) || !is_array($extensions)) { return $out; }
		if (count($extensions) == 0) { return $out; }
		
		foreach ($extensions as $id => $extension) {
			$out['rows'][] = $extension;
		}

		return $out;
	}


	/***************************************************************/
	/* FETCH EXTENSION'S DETAILS FROM XML OBJECT FOR UPDATED CHECK */
	/***************************************************************/
	private function fetchUpExtension($extension) {
		if (!isset($extension->id)) { return false; }
		$id = (int)$extension->id;
		if ($id < 1) { return false; }
		if (!isset($extension->catid)) { return false; }
		$catid = (int)$extension->catid;
		if ($catid < 1) { return false; }
		if (!isset($extension->type) || (trim($extension->type) == '')) { return false; }
		$type = (string)$extension->type;
		if (!in_array($type, array('component', 'module', 'template', 'atemplate', 'auth', 'plugin', 'engine'))) { return false; }

		$row = array();
		$row['id'] = $id;
		$row['catid'] = $catid;
		$row['uid'] = 0;
		$row['type'] = $type;
		$row['name'] = '';
		$row['title'] = '';
		$row['author'] = 'Unknown';
		$row['version'] = 0;
		$row['created'] = 0;
		$row['modified'] = 0;
		$row['compatibility'] = '';
		$row['edclink'] = '';
		$row['pcode'] = '';

		if (!isset($extension->title)) { return false; }
		$row['title'] = (string)$extension->title;
		if (trim($row['title']) == '') { return false; }
		if (!isset($extension->created) || (trim($extension->created) == '')) { return false; }
		$row['created'] = (string)$extension->created;
		if (!is_numeric($row['created'])) { return false; }
		if (!isset($extension->modified) || (trim($extension->modified) == '')) { return false; }
		$row['modified'] = (string)$extension->modified;
		if (!is_numeric($row['modified'])) { return false; }
		if (!isset($extension->version)) { return false; }
		$row['version'] = (string)$extension->version;
		if (($row['version'] == '') || !is_numeric($row['version'])) { return false; }
		if (!isset($extension->uid)) { return false; }
		$row['uid'] = (int)$extension->uid;
		if ($row['uid'] < 1) { return false; }
		if (isset($extension->name)) { $row['name'] = trim($extension->name); }
		if (($row['name'] == '') && in_array($row['type'], array('component', 'module', 'template', 'atemplate', 'auth', 'plugin', 'engine'))) { return false; }
		if (isset($extension->author) && (trim($extension->author) != '')) { $row['author'] = htmlspecialchars(eUTF::trim($extension->author)); }
		if (isset($extension->pcode) && (trim($extension->pcode) != '')) { $row['pcode'] = trim($extension->pcode); }
		if (isset($extension->compatibility) && (trim($extension->compatibility) != '')) { $row['compatibility'] = trim($extension->compatibility); }
		if (isset($extension->edclink) && (trim($extension->edclink) != '')) { $row['edclink'] = trim($extension->edclink); }

		return $row;
	}


	/**************************/
	/* GET ALL ELXIS_RELEASES */
	/**************************/
	public function getElxisReleases() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$out = array('rows' => array(), 'current' => '', 'error' => '');

		if (($elxis->getConfig('SECURITY_LEVEL') > 1) && ($this->subsite == true)) {
			$out['error'] = $eLang->exist('SECLEV_EDC_NOALLOW') ? $eLang->get('SECLEV_EDC_NOALLOW') : 'Under the current security level accessing EDC from sub-sites is not allowed!';
			return $out;
		}

		$from_cache = false;
		if (file_exists($this->cache_path.'/edc/elxis_releases.php')) {
			$ts = filemtime($this->cache_path.'/edc/elxis_releases.php');
			if ($this->time - $ts <= $this->cache_category) { $from_cache = true; }
		}

		if (!$from_cache) {
			if (function_exists('curl_init')) {
				$xmldata = $this->curlget($this->releases_url);
			} else {
				$xmldata = $this->httpget($this->releases_url);
			}
			if (!$xmldata) {
				$out['error'] = $eLang->exist('DATA_EDC_FAILED') ? $eLang->get('DATA_EDC_FAILED') : 'Data receive from EDC server failed!';
				if ($this->errormsg != '') { $out['error'] .= ' - '.$this->errormsg; }
				return $out;
			}

			libxml_use_internal_errors(true);
			$xmlDoc = simplexml_load_string($xmldata, 'SimpleXMLElement');
			if (!$xmlDoc) {
				$out['error'] = 'Invalid response from Elxis server!';
				return $out;
			}

			$current = '';
			if ($xmlDoc->getName() != 'elxis') {
				$out['error'] = 'Could not load releases from Elxis server!';
				return $out;
			}
			$attrs = $xmlDoc->attributes();
			if ($attrs && isset($attrs['current'])) { $current = trim($attrs['current']); }
			unset($attrs);

			if (!isset($xmlDoc->releases)) { return $out; }
			if (count($xmlDoc->releases->children()) == 0) { return $out; }

			$releases = array();
			foreach ($xmlDoc->releases->children() as $release) {
				if (!isset($release->version)) { continue; }
				if (!isset($release->reldate)) { continue; }
				if (!isset($release->revision)) { continue; }
				$version = trim($release->version);
				if (($version == '') || !is_numeric($version)) { continue; }
				$is_current = 0;
				if ($current != '') {
					if ($current == $version) { $is_current = 1; }
				}

				$row = array();
				$row['version'] = $version;
				$row['current']= $is_current;
				$row['reldate'] = (string)$release->reldate;
				$row['revision'] = (int)$release->revision;
				$row['codename'] = (string)$release->codename;
				$row['status'] = (string)$release->status;
				$row['link'] = (string)$release->link;

				$releases[$version] = $row;
				unset($version, $row);
			}

			if (!$releases) { return $out; }

			uasort($releases, array($this, 'sortReleases'));

			$this->cacheReleases($releases);
		}

		if (!isset($releases)) {
			include($this->cache_path.'/edc/elxis_releases.php');
		}
		if (!isset($releases) || !is_array($releases)) { return $out; }
		if (count($releases) == 0) { return $out; }

		foreach ($releases as $release) {
			if ($release['current'] == 1) {
				$out['current'] = $release['version'];
				break;
			}
		}

		$out['rows'] = $releases;

		return $out;
	}


	/*****************************/
	/* GET ELXIS CURRENT RELEASE */
	/*****************************/
	public function getElxisCurrent() {
		$elxis_releases = $this->getElxisReleases();
		if (!$elxis_releases) { return false; }
		if ($elxis_releases['error'] != '') { return false; }
		if (!$elxis_releases['rows']) { return false; }

		$current = array();
		foreach ($elxis_releases['rows'] as $v => $release) {
			if (intval($release['current']) != 1) { continue; }
			$current = $release;
			break;
		}

		return $current ? $current : false;
	}


	/************************/
	/* ORDER ELXIS RELEASES */
	/************************/
	private function sortReleases($a, $b) {
		if ($a['version'] == $b['version']) { return 0; }
		return ($a['version'] < $b['version']) ? 1 : -1;
	}


	/***********************************/
	/* WRITE ELXIS RELEASES INTO CACHE */
	/***********************************/
	private function cacheReleases($releases) {
		$total_rel = count($releases);
		$contents = '<?php '."\n";
		$contents .= '//Elxis Cache file generated on '.gmdate('Y-m-d H:i:s').' GMT'."\n\n";
		$contents .= 'defined(\'_ELXIS_\') or die (\'Direct access to this location is not allowed\');'."\n\n";
		$contents .= '$releases = array('."\n";
		if ($total_rel > 0) {
			$x = 1;
			$n = -1;
			foreach ($releases as $version => $release) {
				if ($n == -1) { $n = count($release); }
				$i = 1;
				$contents .= "\t'".$version.'\' => array(';
				foreach($release as $key => $val) {
					if ($val === '') {
						$contents .= '\''.$key.'\' => \'\'';
					} else if (($key == 'current') || ($key == 'revision')) {
						$contents .= '\''.$key.'\' => '.intval($val);
					} else {
						$contents .= '\''.$key.'\' => \''.$val.'\'';
					}
					if ($i < $n) { $contents .= ', '; }
					$i++;
				}

				$contents .= ($x == $total_rel) ? ")\n" : "),\n";
				$x++;
			}
		}
		$contents .= ');'."\n\n";
		$contents .= '?>';

		$ok = eFactory::getFiles()->createFile('cache/edc/elxis_releases.php', $contents, true);
		return $ok;
	}


	/**********************************/
	/* GET ELXIS FILESYSTEM HASH FILE */
	/**********************************/
	public function getElxisHashes($version) {
		$url = $this->hashes_url.'elxis_hashes_'.$version.'.txt';
		if (function_exists('curl_init')) {
			$txt = $this->curlget($url);
		} else {
			$txt = $this->httpget($url);
		}

		if ($txt === false) { return false; }
		if (trim($txt) == '') { return false; }
		$lines = explode("\n",$txt);
		if (strpos($lines[0], 'VERSION:Elxis') !== 0) { return false; }
		$ok = eFactory::getFiles()->createFile('cache/edc/elxis_hashes_'.$version.'.txt', $txt, true);
		return $ok;
	}


	/************************************************************/
	/* CHECK EDC FOR UPDATES FOR ELXIS AND INSTALLED EXTENSIONS */
	/************************************************************/
	public function checkForUpdates() {//this is used from third party extensions (like com cpanel)
		$elxis = eFactory::getElxis();

		$response = array(
			'elxis' => array('updated' => true, 'version' => '', 'codename' => '', 'revision' => ''),//latest Elxis release
			'totalextensions' => 0,
			'extensions' => array()
		);

		$myelxisversion = $elxis->getVersion();
		$response['elxis']['version'] = $myelxisversion;
		$response['elxis']['codename'] = $elxis->fromVersion('CODENAME');
		$response['elxis']['revision'] = $elxis->fromVersion('REVISION');

		if ($this->hasEDCerror()) { return $response; }

		if (defined('ELXIS_MULTISITE') && (ELXIS_MULTISITE != 1)) { return $response; }

		$can_install = $elxis->acl()->check('com_extmanager', 'components', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'modules', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'templates', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'engines', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'auth', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'plugins', 'install');
		if ($can_install > 0) {
			if (($elxis->getConfig('SECURITY_LEVEL') > 0) && ($elxis->user()->gid <> 1)) { $can_install = 0; }
		}

		if ($can_install < 1) { return $response; }

		$elxis_releases = $this->getElxisReleases();
		if ($elxis_releases) {
			if ($elxis_releases['error'] != '') {
				$this->setEDCerror();
				return $response;
			}
			if ($elxis_releases['rows']) {
				foreach ($elxis_releases['rows'] as $v => $release) {
					if ($release['current'] != 1) { continue; }
					if ($myelxisversion < $release['version']) {
						$response['elxis']['updated'] = false;
						$response['elxis']['version'] = $release['version'];
						$response['elxis']['codename'] = $release['codename'];
						$response['elxis']['revision'] = $release['revision'];
						break;
					}
				}
			}
		}
		unset($elxis_releases);

		$extensions = $this->getThirdExtensions();
		if (!$extensions) { return $response; }

		$edc_result = $this->getAllExtensions();
		if (!$edc_result['rows']) {
			if ($edc_result['error'] != '') {
				$this->setEDCerror();
			} else {
				$this->clearEDCerror();
			}
			return $response;
		}

		$this->clearEDCerror();

		elxisLoader::loadFile('components/com_extmanager/includes/extension.xml.php');
		$exml = new extensionXML();
		foreach ($extensions as $extension) {
			$info = $exml->quickXML($extension['type'], $extension['name']);
			if ($info['installed'] == false) {
				if ($extension['type'] == 'template') {
					$extension['type'] = 'atemplate';
					$info = $exml->quickXML($extension['type'], $extension['name']);//try admin template
					if ($info['installed'] == false) {
						continue;
					}
				} else {
					continue;
				}
			}

			$inst_version = $info['version'];
			unset($info);

			foreach ($edc_result['rows'] as $k => $edcrow) {
				if (($edcrow['type'] == $extension['type']) && ($edcrow['name'] == $extension['name'])) {
					if ($inst_version < $edcrow['version']) {
						$response['totalextensions']++;
						$type = $edcrow['type'];
						if (!isset($response['extensions'][$type])) { $response['extensions'][$type] = array(); }
						$response['extensions'][$type][] = array('type' => $edcrow['type'], 'name' => $edcrow['name'], 'title' => $edcrow['title'], 'version' => $edcrow['version']);
					}
					break;
				}
			}
		}

		return $response;
	}


	/****************************************/
	/* GET INSTALLED THIRD PARTY EXTENSIONS */
	/****************************************/
	private function getThirdExtensions() {
		$db = eFactory::getDB();

		$sql = "SELECT ".$db->quoteId('auth')." FROM ".$db->quoteId('#__authentication')." WHERE ".$db->quoteId('iscore').' = 0';
		$stmt = $db->prepare($sql);
		$stmt->execute();
		$auths = $stmt->fetchCol();
		
		$sql = "SELECT ".$db->quoteId('component')." FROM ".$db->quoteId('#__components')." WHERE ".$db->quoteId('iscore').' = 0';
		$stmt = $db->prepare($sql);
		$stmt->execute();
		$components = $stmt->fetchCol();
		
		$sql = "SELECT ".$db->quoteId('engine')." FROM ".$db->quoteId('#__engines')." WHERE ".$db->quoteId('iscore').' = 0';
		$stmt = $db->prepare($sql);
		$stmt->execute();
		$engines = $stmt->fetchCol();

		$sql = "SELECT ".$db->quoteId('module')." FROM ".$db->quoteId('#__modules')." WHERE ".$db->quoteId('iscore').' = 0 GROUP BY '.$db->quoteId('module');
		$stmt = $db->prepare($sql);
		$stmt->execute();
		$modules = $stmt->fetchCol();

		$sql = "SELECT ".$db->quoteId('plugin')." FROM ".$db->quoteId('#__plugins')." WHERE ".$db->quoteId('iscore').' = 0';
		$stmt = $db->prepare($sql);
		$stmt->execute();
		$plugins = $stmt->fetchCol();

		$sql = "SELECT ".$db->quoteId('template')." FROM ".$db->quoteId('#__templates')." WHERE ".$db->quoteId('iscore').' = 0';
		$stmt = $db->prepare($sql);
		$stmt->execute();
		$templates = $stmt->fetchCol();

		$extensions = array();
		if ($auths) {
			foreach ($auths as $auth) {
				$extensions[] = array('type' => 'auth', 'name' => $auth);
			}
		}
		if ($components) {
			foreach ($components as $component) {
				$extensions[] = array('type' => 'component', 'name' => $component);
			}
		}
		if ($engines) {
			foreach ($engines as $engine) {
				$extensions[] = array('type' => 'engine', 'name' => $engine);
			}
		}
		if ($modules) {
			foreach ($modules as $module) {
				$extensions[] = array('type' => 'module', 'name' => $module);
			}
		}
		if ($plugins) {
			foreach ($plugins as $plugin) {
				$extensions[] = array('type' => 'plugin', 'name' => $plugin);
			}
		}
		if ($templates) {
			foreach ($templates as $template) {
				$extensions[] = array('type' => 'template', 'name' => $template);
			}
		}

		return $extensions;
	}


	/***************************************************/
	/* EDC CONNECTION ERROR IN NEAR PAST? (FOR CPANEL) */
	/***************************************************/
	private function hasEDCerror() {
		if (!file_exists($this->cache_path.'/edc/connect_error.txt')) { return false; }
		$ts = filemtime($this->cache_path.'/edc/connect_error.txt');
		if ($this->time - $ts <= $this->cache_edc_connect_error) { return true; }
		return false;
	}


	/****************************/
	/* SET EDC CONNECTION ERROR */
	/****************************/
	private function setEDCerror() {
		eFactory::getFiles()->createFile('cache/edc/connect_error.txt', $this->time, true);
	}


	/******************************/
	/* CLEAR EDC CONNECTION ERROR */
	/******************************/
	private function clearEDCerror() {
		if (!file_exists($this->cache_path.'/edc/connect_error.txt')) { return; }
		eFactory::getFiles()->deleteFile('cache/edc/connect_error.txt', true);
	}


	/************************************************/
	/* UPDATE ELXIS / DOWNLOAD ELXIS LATEST RELEASE */
	/************************************************/
	public function downloadElxis() {
		$response = array('success' => 0, 'message' => '');
		$zippath = $this->repo_path.'/tmp/elxis.zip';

		//check if file exists from previous attempts or on subsites
		if (file_exists($zippath)) {
			$dt = time() - filemtime($zippath);
			$ok = false;
			if (($dt < 14400) && (filesize($zippath) > 8000000)) { $ok = true; }//downloaded less than 4 hours ago and is large enough (complete download)
			if ($ok) {
				$response['success'] = 1;
				return $response;
			} else {
				@unlink($zippath);
			}
		}

		if (!function_exists('curl_init')) {
			$response['message'] = 'CURL is required in your PHP installation inorder to download Elxis!';
			return $response;
		}

		$remotefile = 'https://www.elxis.org/update/downloadelxis.php';

		$fw = @fopen($zippath, 'wb');
		if (!$fw) { $response['message'] = 'Repository tmp folder is not writeable!'; return $response; }
		$ch = curl_init();

		$uagent = (isset($_SERVER['HTTP_USER_AGENT'])) ? $_SERVER['HTTP_USER_AGENT'] : 'Mozilla/5.0 (Windows NT 6.0; WOW64; rv:48.0) Gecko/20100101 Firefox/48.0)';
		curl_setopt($ch, CURLOPT_URL, $remotefile);
		curl_setopt($ch, CURLOPT_USERAGENT, $uagent);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_NOBODY, 0);
		curl_setopt($ch, CURLOPT_FAILONERROR, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 12);
        curl_setopt($ch, CURLOPT_TIMEOUT, 28);
        curl_setopt($ch, CURLOPT_REFERER, eFactory::getElxis()->getConfig('URL'));
    	curl_setopt($ch, CURLOPT_FILE, $fw);
		$data = curl_exec($ch);
		if (0 == curl_errno($ch)) {
           	curl_close($ch);
			fclose($fw);
			if (filesize($zippath) < 8000000) {
				@unlink($zippath);
				$response['message'] = 'Received data is not Elxis zip package!';
			} else {
				$response['success'] = 1;
			}
		} else {
			$response['message'] = 'Downloading Elxis failed! '.curl_error($ch);
			curl_close($ch);
			fclose($fw);
		}

		return $response;
	}

}

?>