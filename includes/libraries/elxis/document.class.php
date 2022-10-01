<?php 
/**
* @version		$Id: document.class.php 2449 2022-05-08 10:21:10Z IOS $
* @package		Elxis
* @subpackage	Document
* @copyright	Copyright (c) 2006-2022 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisDocument {

	private $title = ''; //Document title
	private $description = ''; //Document description
	private $base = ''; // Document base URL
	private $meta = array(); //meta tags array
	private $keywords = array();
	private $stylesheets = array(); //Array of linked style sheets
	private $rawstyle = array(); //Array of included style declarations
	private $libraries = array(); //Array of unique linked javascript libraries (jquery, mootools, etc...)
	private $scripts = array(); //Array of linked scripts
	private $rawscript = array(); //Array of scripts placed in the header
	private $docready = array();//Array of javascript executed on jquery document ready
	private $nativedocready = array();//Array of javascript executed on document ready (plain javascript)
	private $custom = array(); //Array of custom head data
	private $favicon = ''; //URL to favicon
	private $links = array(); //Array of link tags
	private $buffer = array(); //Array of buffered output	-> mixed (depends on the renderer)
	private $headers = array(); //PHP headers
	private $replacements = array(); //Array of replacements marks
	private $replace_mark = 0; //Last replacement mark for modules
	private $debug = 0;
	private $doctype = '<!DOCTYPE html>';
	private $contenttype = 'text/html';
	private $namespace = '';
	private $cdata = false;
	private $observe = false;
	private $observedItems = array();


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($options=array()) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$this->determineMobile();

		//As of Elxis 5.0 doctype is always html5
		$this->doctype = '<!DOCTYPE html>';
		$this->contenttype = 'text/html';
		$this->cdata = false;
		$this->namespace = '';
		$this->meta['charset'] = 'UTF-8';

		$this->base = (defined('ELXIS_ADMIN')) ? $elxis->secureURL($elxis->getConfig('URL').'/'.ELXIS_ADIR.'/') : $elxis->secureBase().'/'; //dont use it or get it with getRealUriString
		$this->title = (defined('ELXIS_ADMIN')) ? $eLang->get('ADMINISTRATION').' '.$elxis->getConfig('SITENAME') : $elxis->getConfig('SITENAME');
		$this->description = $elxis->getConfig('METADESC');
		$this->debug = (ELXIS_INNER == 1) ? 0 : (int)$elxis->getConfig('DEBUG');
		$this->meta['http-equiv'] = array();
		$this->meta['standard'] = array();
		$this->meta['property'] = array();
		if (!isset($this->meta['charset'])) { //meta "charset" and "http-equiv" cannot co-exist
			$this->meta['http-equiv']['content-type'] = $this->contenttype.'; charset=utf-8';
		}
		$this->meta['standard']['generator'] = 'Elxis - Open Source CMS';
		$this->meta['standard']['robots'] = defined('ELXIS_ADMIN') ? 'noindex, nofollow' : 'index, follow';

		if (!defined('ELXIS_ADMIN')) {
			if (ELXIS_INNER == 0) {
				$this->addOpenSearchLink();
				$this->addAlterlanglinks();
			}
		}
		$this->addStyleLink($elxis->secureBase().'/templates/system/css/standard'.$eLang->getinfo('RTLSFX').'.css', 'text/css', 'all');
		$this->addScriptLink($elxis->secureBase().'/includes/js/elxis.js');

		if (file_exists(ELXIS_PATH.'/favicon.ico')) {
			$this->favicon = $elxis->secureBase().'/favicon.ico';
		} elseif (file_exists(ELXIS_PATH.'/favicon.png')) {
			$this->favicon = $elxis->secureBase().'/favicon.png';
		} elseif (file_exists(ELXIS_PATH.'/media/images/favicon.ico')) {
			$this->favicon = $elxis->secureBase().'/media/images/favicon.ico';
		} elseif (file_exists(ELXIS_PATH.'/media/images/favicon.png')) {
			$this->favicon = $elxis->secureBase().'/media/images/favicon.png';
		}

		$this->buffer['modules'] = array();

		if (!defined('ELXIS_ADMIN')) {
			$this->langRedirection();

			if ($elxis->getConfig('MULTILINGUISM') == 1) {
				$lng = eFactory::getURI()->getUriLang();
				if ($lng != '') { $this->loadMLConfig($lng); }
			}
		}
	}


	/********************************************/
	/* SHOULD WE REDIRECT TO AN OTHER LANGUAGE? */
	/********************************************/
	private function langRedirection() {
		if (ELXIS_INNER == 1) { return; }

		$elxis = eFactory::getElxis();
		if ($elxis->getConfig('LANG_DETECT') == 0) { return; }

		$eSession = eFactory::getSession();
		$lngdetected = (int)$eSession->get('lngdetected', 0);
		if ($lngdetected == 1) { return; } //already redirected, or queried specific language or a specific page

		$uagent = (isset($_SERVER['HTTP_USER_AGENT'])) ? $_SERVER['HTTP_USER_AGENT'] : '';
		if ($uagent != '') {
			if (preg_match('/robot|yahoo|google|bot|yandex|baidu|facebook|elxis|spider|crawl|slurp|inktomi|lycos|msn|altavista|askjeeves|curl|^$/i', $uagent)) { return; }
		}

		$eSession->set('lngdetected', 1);

		$uri = eFactory::getURI()->getElxisUri(true, true);
		if (($uri == '') || ($uri == '/')) {
			$eLang = eFactory::getLang();
			$blang = $eLang->browserLang();
			if ($blang != $eLang->currentLang()) {
				$link = (defined('ELXIS_ADMIN')) ? $elxis->makeAURL($blang.':') : $elxis->makeURL($blang.':');
				$elxis->redirect($link);
			}
		}
	}


	/*************************************************/
	/* SHOULD WE GENERATE A MOBILE FRIENDLY VERSION? */
	/*************************************************/
	private function determineMobile() {
		if (defined('ELXIS_MOBILE')) { return; }
		define('ELXIS_MOBILE', 0);//Deprecated as of Elxis 5.0, disable
	}


	/*********************************************/
	/* GET BEST CONTENT TYPE FOR XHTML DOCUMENTS */
	/*********************************************/
	private function getXHTMLctype() {
		if (!isset($_SERVER['HTTP_ACCEPT'])) { return 'text/html'; }
		$accept = strtolower($_SERVER['HTTP_ACCEPT']);
		if (strpos($accept, 'application/xhtml+xml') !== false) {
			return 'application/xhtml+xml';
		} else {
			return 'text/html';
		}
	}


	/***********************************/
	/* LOAD MULTILINGUAL CONFIGURATION */
	/***********************************/
	private function loadMLConfig($lng) {
		$db = eFactory::getDB();

		$ctg = 'config';
		$sql = "SELECT ".$db->quoteId('element').", ".$db->quoteId('translation')." FROM ".$db->quoteId('#__translations')
		."\n WHERE ".$db->quoteId('category')." = :ctg AND ".$db->quoteId('language')." = :lng";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':ctg', $ctg, PDO::PARAM_STR);
		$stmt->bindParam(':lng', $lng, PDO::PARAM_STR);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if ($rows) {
			$elxis = eFactory::getElxis();
			foreach ($rows as $row) {
				$key = strtoupper($row['element']);
				$elxis->setConfig($key, $row['translation']);
			}
			$this->title = $elxis->getConfig('SITENAME');
			$this->description = $elxis->getConfig('METADESC');
		}
	}


	/**********************/
	/* ADD STYLEHEET LINK */
	/**********************/
	public function addStyleLink($url, $type='text/css', $media='', $attribs='') {
		$url = eFactory::getElxis()->secureURL($url);
		$this->stylesheets[$url]['type'] = $type;
		$this->stylesheets[$url]['media'] = $media;
		$this->stylesheets[$url]['attribs'] = $attribs;
		$this->observer('cssfile', $url);
	}


	/*****************/
	/* ADD RAW STYLE */
	/*****************/
	public function addStyle($content, $type='text/css') {
		$type = strtolower($type);
		if (!isset($this->rawstyle[$type])) { $this->rawstyle[$type] = array(); }
		$this->rawstyle[$type][] = $content;
		$this->observer('css', $content);
	}


	/********************/
	/* ADD FONT AWESOME */
	/********************/
	public function addFontAwesome($with_brands=false) {
		if (defined('FONTAWESOME_LOADED')) {
			if ($with_brands) {
				if (!defined('FONTAWESOME_BRANDS_LOADED')) {
					$faurl = eFactory::getElxis()->secureBase().'/includes/fontawesome/';
					$this->addStyleLink($faurl.'css/brands.min.css', 'text/css', 'all');
					define('FONTAWESOME_BRANDS_LOADED', 1);
				}
			}
			return;
		}
		$faurl = eFactory::getElxis()->secureBase().'/includes/fontawesome/';
		$this->addStyleLink($faurl.'css/solid.min.css', 'text/css', 'all');
		$this->addStyleLink($faurl.'css/regular.min.css', 'text/css', 'all');
		if ($with_brands) {
			$this->addStyleLink($faurl.'css/brands.min.css', 'text/css', 'all');
			define('FONTAWESOME_BRANDS_LOADED', 1);
		}
		$this->addStyleLink($faurl.'css/fontawesome.min.css', 'text/css', 'all');
		$this->addStyleLink($faurl.'css/v4-shims.min.css', 'text/css', 'all');
		define('FONTAWESOME_LOADED', 1);
	}


	/******************************************/
	/* ADD FONT AWESOME ANIMATED (DEPRECATED) */
	/******************************************/
	public function addFontAwesomeAnim() {
		$this->addFontAwesome();
	}


	/******************/
	/* ADD FONT ELXIS */
	/******************/
	public function addFontElxis() {
		if (defined('FONTELXIS_LOADED')) { return; }
		$feurl = eFactory::getElxis()->secureBase().'/includes/fontelxis/fontelxis.css';
		$this->addStyleLink($feurl, 'text/css', 'all');
		define('FONTELXIS_LOADED', 1);
	}


	/*****************/
	/* ADD SIMPLEBAR */
	/*****************/
	public function addSimplebar() {
		if (defined('SIMPLEBAR_LOADED')) { return; }
		$url = eFactory::getElxis()->secureBase().'/includes/js/simplebar/';
		$this->addScriptLink($url.'simplebar.js');
		$this->addStyleLink($url.'simplebar.css');
		define('SIMPLEBAR_LOADED', 1);
	}


	/*****************/
	/* ADD SCROLLBAR */
	/*****************/
	public function addScrollbar() {
		if (defined('SCROLLBAR_LOADED')) { return; }
		$url = eFactory::getElxis()->secureBase().'/includes/js/overlayscrollbars/';
		$this->addScriptLink($url.'OverlayScrollbars.min.js');
		$this->addStyleLink($url.'OverlayScrollbars.min.css');
		$js = 'var instances = OverlayScrollbars(document.querySelectorAll(\'[data-simplebar]\'), { overflowBehavior: { x: "hidden", y: "scroll" }, scrollbars: { visibility: "auto", autoHide: "scroll"}});';
		$this->addNativeDocReady($js);
		define('SCROLLBAR_LOADED', 1);
	}


	/********************************************************/
	/* ADD UNIQUE SCRIPT LIBRARY (JQUERY, MOOTOOLS, ETC...) */
	/********************************************************/
	public function addLibrary($name, $url, $version='1.0') {
		$name = strtolower($name);
		if (isset($this->libraries[$name])) {
			if ($version > $this->libraries[$name][1]) {
				$url = eFactory::getElxis()->secureURL($url);
				$this->libraries[$name] = array($url, $version);
			}
		} else {
			$url = eFactory::getElxis()->secureURL($url);
			$this->libraries[$name] = array($url, $version);
		}

		$this->observer('jslibrary', $url, $name, $version);
	}


	/**************/
	/* ADD JQUERY */
	/**************/
	public function addJQuery() {
		$elxis = eFactory::getElxis();

		$jqpath = $elxis->secureBase().'/includes/js/jquery/';
		switch ($elxis->getConfig('JQUERY')) {
			case '1':
				$this->addLibrary('jquery', $jqpath.'jquery-1.11.1.min.js', '1.11.1');
			break;
			case '1m':
				$this->addLibrary('jquery', $jqpath.'jquery-1.11.1.min.js', '1.11.1');
				$this->addLibrary('jquery-migrate', $jqpath.'jquery-migrate-1.2.1.min.js', '1.2.1');
			break;
			case '3':
				$this->addLibrary('jquery', $jqpath.'jquery-3.6.0.min.js', '3.6.0');
			break;
			case '3m': default:
				$this->addLibrary('jquery', $jqpath.'jquery-3.6.0.min.js', '3.6.0');
				$this->addLibrary('jquery-migrate', $jqpath.'jquery-migrate-3.3.2.min.js', '3.3.2');
			break;
		}
	}


	/*******************/
	/* ADD SCRIPT LINK */
	/*******************/
	public function addScriptLink($url, $type='') {//type:deprecated
		$url = eFactory::getElxis()->secureURL($url);
		$this->scripts[$url] = $type;
		$this->observer('jsfile', $url);
	}


	/******************/
	/* ADD RAW SCRIPT */
	/******************/
	public function addScript($content, $type='') {//type:deprecated
		$this->rawscript[] = $content;
		$this->observer('js', $content);
	}


	/************************************************/
	/* JAVASCRIPT EXECUTED ON JQUERY DOCUMENT READY */
	/************************************************/
	public function addDocReady($content) {
		if (!isset($this->libraries['jquery'])) { $this->addJQuery(); }
		$this->docready[] = $content;
		$this->observer('docready', $content);
	}


	/*****************************************************************************************/
	/* JAVASCRIPT EXECUTED ON CONTENT LOADED (LIKE "addDocReady" WITHOUT JQUERY - Elxis 5.0) */
	/*****************************************************************************************/
	public function addNativeDocReady($content) {
		$this->nativedocready[] = $content;
		$this->observer('nativedocready', $content);
	}


	/************************/
	/* ADD CUSTOM HEAD DATA */
	/************************/
	public function addCustom($content) {
		$this->custom[] = $content;
		$this->observer('custom', $content);
	}


	/******************************************/
	/* ADD LINK TAG (eg. alternate for feeds) */
	/******************************************/
	public function addLink($href, $type='application/rss+xml', $relation='alternate', $attribs='') {
		$extra = ($attribs != '') ? ' '.$attribs : '';
		$type_str = ($type != '') ? ' type="'.$type.'"' : '';
		$html = '<link rel="'.$relation.'"'.$type_str.' href="'.$href.'"'.$extra.' />';
		$this->links[] = $html;
		$this->observer('custom', $html);
	}


	/********************************/
	/* ADD ALTERNATE LANGUAGE LINKS */
	/********************************/
	private function addAlterlanglinks() {
		$elxis = eFactory::getElxis();

		if (($elxis->getConfig('MULTILINGUISM') == 0) || ($elxis->getConfig('SITELANGS') == '')) { return; }
		$lngs = explode(',', $elxis->getConfig('SITELANGS'));
		if (!$lngs || (count($lngs) < 2)) { return; }

		$eURI = eFactory::getURI();
		$segs = $eURI->getSegments();
		$elxis_uri = $eURI->getComponent();
		if ($elxis_uri == 'content') { $elxis_uri = ''; }
		if ($segs) {
			$elxis_uri .= ($elxis_uri == '') ? implode('/', $segs) : ':'.implode('/', $segs);
			$n = count($segs) - 1;
			if (!preg_match('#\.#', $segs[$n])) { $elxis_uri .= '/'; }
		} else {
			$elxis_uri .= ($elxis_uri != '') ? '/' : '';
		}
		$ssl = $eURI->detectSSL();
		$file = (ELXIS_INNER == 1) ? 'inner.php' : 'index.php';
		foreach ($lngs as $lng) {
			//if ($lng == $elxis->getConfig('LANG')) { continue; }
			if ($lng == 'zh') {
				$alt_lng = 'zh-Hans';
			} else if ($lng == 'zt') {
				$alt_lng = 'zh-Hant';
			} else if ($lng == 'el') {
				$alt_lng = 'el-GR';
			} else if ($lng == 'sr') {
				$alt_lng = 'sr-CS';
			} else if ($lng == 'rs') {
				$alt_lng = 'sr-RS';
			} else {
				$alt_lng = $lng;
			}
			$link = $elxis->makeURL($lng.':'.$elxis_uri, $file, $ssl);
			$this->links[] = '<link rel="alternate" hreflang="'.$alt_lng.'" href="'.$link.'" />';
		}
	}


	/***********************/
	/* ADD OPENSEARCH LINK */
	/***********************/
	private function addOpenSearchLink() {
		$eSearch = eFactory::getSearch();
		$engine = trim($eSearch->getDefaultEngine());
		if ($engine == '') { return; }
		$href = eFactory::getURI()->makeURL('search:osdescription.xml', 'inner.php', true);
		$this->addLink($href, 'application/opensearchdescription+xml', 'search', 'title="'.eFactory::getElxis()->getConfig('SITENAME').'"');
	}


	/***************/
	/* SET FAVICON */
	/***************/
	public function setFavicon($url) {
		if (trim($url) != '') {
			$this->favicon = eFactory::getElxis()->secureURL($url);
		}
	}


	/***************/
	/* GET FAVICON */
	/***************/
	public function getFavicon() {
		return $this->favicon;
	}


	/**********************/
	/* FETCH HEAD SECTION */
	/**********************/
	private function fetchHead() {
		$elxis = eFactory::getElxis();

		if (!defined('ELXIS_ADMIN')) {
			$user_js_relpath = 'templates/system/js/user.config.js';
			if (file_exists(ELXIS_PATH.'/'.$user_js_relpath)) {
				$fs = filesize(ELXIS_PATH.'/'.$user_js_relpath);
				if ($fs > 0) {
					$this->addScriptLink($elxis->secureBase().'/'.$user_js_relpath);
				}
			}
		}

		//$this->base = eFactory::getURI()->getRealUriString(); //get the correct base href value
		//$html = '<base href="'.$this->base.'" />'."\n"; //dont use a base tag!
		$html = '';
		foreach ($this->meta as $type => $tag) {
			if (is_array($tag)) {
				if (count($tag) == 0) { continue; }
				foreach ($tag as $name => $content) {
					if ($type == 'http-equiv') {
						$html .= "\t".'<meta http-equiv="'.$name.'" content="'.$content.'"'.' />'."\n";
					} elseif ($type == 'standard') {
						$html .= "\t".'<meta name="'.$name.'" content="'.eUTF::str_replace('"',"'",$content).'"'.' />'."\n";
					} else {
						$html .= "\t".'<meta '.$type.'="'.$name.'" content="'.eUTF::str_replace('"',"'",$content).'"'.' />'."\n";
					}
				}
			} else {
				$html .= "\t".'<meta '.$type.'="'.$tag.'" />'."\n";
			}
		}
	
		$html .= "\t".'<title>'.htmlspecialchars($this->title)."</title>\n";
		$html .= "\t".'<meta name="description" content="'.$this->description.'" />'."\n";
		if (count($this->keywords) > 0) {
			$str = eUTF::str_replace('"',"'", implode(', ', $this->keywords));
			$html .= "\t".'<meta name="keywords" content="'.$str.'" />'."\n";
		}

		if ($this->favicon != '') {
			if (preg_match('#(\.png)$#i', $this->favicon)) {
				$html .= "\t".'<link rel="icon" type="image/png" href="'.$this->favicon.'" />'."\n";
			} else {
				$html .= "\t".'<link rel="shortcut icon" href="'.$this->favicon.'" />'."\n";
			}
		}

		if (count($this->links) > 0) {
			foreach ($this->links as $link) {
				$html .= "\t".$link."\n";
			}
		}

		$baseurl = $elxis->secureBase();

		if ($this->stylesheets) {
			$minified = false;
			if (!defined('ELXIS_ADMIN') && ((int)$elxis->getConfig('MINICSS') > 0)) {
				$links = array();
				foreach ($this->stylesheets as $href => $arr) { $links[] = $href; }
				$minifier = $elxis->obj('minifier');
				$ok = $minifier->minify($links, 'css', false);
				if ($ok) {
					$minified = true;
					$lng = $elxis->getConfig('LANG');
					$csslink = $elxis->makeURL($lng.':content:minify/', 'inner.php').$minifier->getHash().'.css';
					$html .= "\t".'<link rel="stylesheet" href="'.$csslink.'" type="text/css" media="all" />'."\n";
					$excluded = $minifier->getExcluded();
					if ($excluded) {
						foreach ($this->stylesheets as $href => $arr) {
							if (!in_array($href, $excluded)) { continue; }
							$html .= "\t".'<link rel="stylesheet" href="'.$href.'" type="'.$arr['type'].'"';
							if ($arr['media'] != '') { $html .= ' media="'.$arr['media'].'" '; }
							if ($arr['attribs'] != '') { $html .= ' '.$arr['attribs']; }
							$html .= ' />'."\n";
						}
					}
				}
			}
			if (!$minified) {
				foreach ($this->stylesheets as $href => $arr) {
					$v = '';
					if (strpos($href, $baseurl) === 0) {
						if ((strpos($href, '/inner.php/') === false) && (strpos($href, '?') === false)) {
							$f = str_replace($baseurl, ELXIS_PATH, $href);
							if (file_exists($f)) { $v = '?v='.filemtime($f); }
						}
					}

					$html .= "\t".'<link rel="stylesheet" href="'.$href.$v.'" type="'.$arr['type'].'"';
					if ($arr['media'] != '') { $html .= ' media="'.$arr['media'].'" '; }
					if ($arr['attribs'] != '') { $html .= ' '.$arr['attribs']; }
					$html .= ' />'."\n";
				}
			}
		}

		if ($this->rawstyle) {
			foreach ($this->rawstyle as $type => $arr) {
				$html .= "\t".'<style type="'.$type.'">'."\n";
				foreach ($arr as $content) {
					$html .= "\t\t".'<!--'."\n\t\t".$content."\n\t\t".'-->'."\n";
				}
				$html .= "\t</style>\n";
			}
		}

		$minified = false;
		if (!defined('ELXIS_ADMIN') && ((int)$elxis->getConfig('MINIJS') > 0)) {
			$links = array();
			if ($this->libraries) {
				foreach ($this->libraries as $library => $arr) { $links[] = $arr[0]; }
			}
			if ($this->scripts) {
				foreach ($this->scripts as $src => $type) { $links[] = $src; }
			}
			if ($links) {
				if (!isset($minifier)) {
					$minifier = $elxis->obj('minifier');
				}
				$minifier->remove_emptylines = false;
				$ok = $minifier->minify($links, 'js', false);
				if ($ok) {
					$minified = true;
					$lng = $elxis->getConfig('LANG');
					$jslink = $elxis->makeURL($lng.':content:minify/', 'inner.php').$minifier->getHash().'.js';
					$html .= "\t".'<script src="'.$jslink.'"></script>'."\n";
					$excluded = $minifier->getExcluded();
					if ($excluded) {
						if ($this->libraries) {
							foreach ($this->libraries as $library => $arr) {
								if (!in_array($arr[0], $excluded)) { continue; }
								$html .= "\t".'<script src="'.$arr[0].'"></script>'."\n";
							}
						}
						if ($this->scripts) {
							foreach ($this->scripts as $src => $type) {
								if (!in_array($src, $excluded)) { continue; }
								$html .= "\t".'<script src="'.$src.'"></script>'."\n";
							}
						}
					}
				}
			}
		}

		if (!$minified) {
			if ($this->libraries) {
				foreach ($this->libraries as $library => $arr) {
					$html .= "\t".'<script src="'.$arr[0].'"></script>'."\n";
				}
			}

			if ($this->scripts) {
				foreach ($this->scripts as $src => $type) {
					$v = '';
					if (strpos($src, $baseurl) === 0) {
						if ((strpos($src, '/inner.php/') === false) && (strpos($src, '?') === false)) {
							$f = str_replace($baseurl, ELXIS_PATH, $src);
							if (file_exists($f)) { $v = '?v='.filemtime($f); }
						}
					}
					$html .= "\t".'<script src="'.$src.$v.'"></script>'."\n";
				}
			}
		}

		if ($this->rawscript) {
			$html .= "\t".'<script>'."\n";
			foreach ($this->rawscript as $content) { $html .= "\t\t".$content."\n"; }
			$html .= "\t</script>\n";
		}

		if ($this->docready || $this->nativedocready) {
			$html .= "\t".'<script>'."\n";
			if ($this->docready) {
				$html .= "\t\t".'$(document).ready(function() {'."\n";			
				foreach ($this->docready as $script) { $html .= "\t\t".$script."\n"; }
				$html .= "\t\t".'});'."\n";
			}
			if ($this->nativedocready) {
				$txt = '';
				foreach ($this->nativedocready as $script) { $txt .= $script.' '; }
				$html .= 'if (document.readyState != \'loading\') {'."\n";
				$html .= $txt."\n";
				$html .= '} else if (document.addEventListener) {'."\n";
				$html .= 'document.addEventListener(\'DOMContentLoaded\', function(){ '.$txt.' });'."\n";
				$html .= '} else {'."\n";
				$html .= 'document.attachEvent(\'onreadystatechange\', function() { if (document.readyState == \'complete\') { '.$txt.' } });'."\n";
				$html .=  "}\n";
			}
			$html .= "\t</script>\n";
		}
		if ($this->custom) {
			foreach ($this->custom as $custom) {
				$html .= "\t".$custom."\n";
			}
		}

		return $html;
	}


	/***************/
	/* GET DOCTYPE */
	/***************/
	public function getDocType() {
		return $this->doctype;
	}


	/***************/
	/* SET DOCTYPE */
	/***************/
	public function setDocType($type) {
		$this->doctype = $type;
	}


	/********************/
	/* GET CONTENT TYPE */
	/********************/
	public function getContentType() {
		return $this->contenttype;
	}


	/********************/
	/* SET CONTENT TYPE */
	/********************/
	public function setContentType($type) {
		$this->contenttype = $type;
	}


	/*********************/
	/* GET XML NAMESPACE */
	/*********************/
	public function getNamespace() {
		return $this->namespace;
	}


	/********************/
	/* SET XML NAMESPACE */
	/********************/
	public function setNamespace($ns) {
		$this->namespace = $ns;
	}


	/*******************************************************/
	/* GET ADDITIONAL ATTRIBUTES FOR THE HTML ROOT ELEMENT */
	/*******************************************************/
	public function htmlAttributes() {
		$eLang = eFactory::getLang();

		$iso_lang = $eLang->getinfo('LANGUAGE');
		if ($iso_lang == 'zt') { $iso_lang = 'zh-Hant'; }
		$out = '';
		if ($this->namespace != '') { $out .= ' xmlns="'.$this->namespace.'"'; }
		$out .= ' lang="'.$iso_lang.'" dir="'.$eLang->getinfo('DIR').'"';
		return $out;
	}


	/**********************/
	/* SET DOCUMENT TITLE */
	/**********************/
	public function setTitle($title) {
		$this->title = $title;
		$this->observer('title', $title);
	}


	/**********************/
	/* GET DOCUMENT TITLE */
	/**********************/
	public function getTitle() {
		return $this->title;
	}


	/****************************/
	/* SET DOCUMENT DESCRIPTION */
	/****************************/
	public function setDescription($description) {
		$this->description = $description;
		$this->observer('description', $description);
	}


	/****************************/
	/* GET DOCUMENT DESCRIPTION */
	/****************************/
	public function getDescription() {
		return $this->description;
	}


	/****************/
	/* SET META TAG */
	/****************/
	public function setMetaTag($name, $content, $equiv=false, $type='') {
		switch (strtolower($name)) {
			case 'generator': break;
			case 'description':
				$this->setDescription($content);
			break;
			default:
				if ($equiv == true) {
					$this->meta['http-equiv'][$name] = $content;
				} else if ($type != '') {
					$this->meta[$type][$name] = $content;
				} else {
					$this->meta['standard'][$name] = $content;
				}
			break;
		}
    }


	/************************************************************/
	/* SET META KEYWORDS (ACCEPTS ARRAY/COMMA SEPERATED STRING) */
	/************************************************************/
	public function setKeywords($keywords) {
		if (is_array($keywords)) {
			if (count($keywords) > 0) {
				foreach ($keywords as $word) {
					$this->addKeyword($word);
				}
				$this->observer('keywords', implode(',', $keywords));
			}
		} else {
			$parts = preg_split('/\,/', $keywords);
			if ($parts) {
				foreach ($parts as $word) {
					$this->addKeyword($word);
				}
				$this->observer('keywords', $keywords);
			}
		}
	}


	/***************************/
	/* LOAD SYNTAX HIGHLIGHTER */
	/***************************/
	public function loadHighlighter($brushes=array(), $theme='default') {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$urlbase = $elxis->getConfig('URL').'/includes/js/syntaxhighlighter';
		$this->addLibrary('syntaxhighlighter', $urlbase.'/scripts/shCore.js', '3.0.83');
		if (is_array($brushes) && (count($brushes) > 0)) {
			foreach ($brushes as $brush) {
				switch(strtolower($brush)) {
					case 'as3': case 'actionscript3': $this->addScriptLink($urlbase.'/scripts/shBrushAS3.js'); break;
					case 'bash': case 'shell': $this->addScriptLink($urlbase.'/scripts/shBrushBash.js'); break;
					case 'cf': case 'coldfusion': $this->addScriptLink($urlbase.'/scripts/shBrushColdFusion.js'); break;
					case 'c-sharp': case 'csharp': $this->addScriptLink($urlbase.'/scripts/shBrushCSharp.js'); break;
					case 'css': $this->addScriptLink($urlbase.'/scripts/shBrushCss.js'); break;
					case 'cpp': case 'c': $this->addScriptLink($urlbase.'/scripts/shBrushCpp.js'); break;
					case 'delphi': case 'pas': case 'pascal': $this->addScriptLink($urlbase.'/scripts/shBrushDelphi.js'); break;
					case 'diff': case 'patch': $this->addScriptLink($urlbase.'/scripts/shBrushDiff.js'); break;
					case 'erl': case 'erlang': $this->addScriptLink($urlbase.'/scripts/shBrushErlang.js'); break;
					case 'groovy': $this->addScriptLink($urlbase.'/scripts/shBrushGroovy.js'); break;
					case 'js': case 'jscript': case 'javascript': $this->addScriptLink($urlbase.'/scripts/shBrushJScript.js'); break;
					case 'java': $this->addScriptLink($urlbase.'/scripts/shBrushJava.js'); break;
					case 'jfx': case 'javafx': $this->addScriptLink($urlbase.'/scripts/shBrushJavaFX.js'); break;
					case 'perl': case 'pl': $this->addScriptLink($urlbase.'/scripts/shBrushPerl.js'); break;
					case 'php': $this->addScriptLink($urlbase.'/scripts/shBrushPhp.js'); break;
					case 'plain': case 'text': $this->addScriptLink($urlbase.'/scripts/shBrushPlain.js'); break;
					case 'ps': case 'powershell': $this->addScriptLink($urlbase.'/scripts/shBrushPowerShell.js'); break;
					case 'py': case 'python': $this->addScriptLink($urlbase.'/scripts/shBrushPython.js'); break;
					case 'rails': case 'ror': case 'ruby': $this->addScriptLink($urlbase.'/scripts/shBrushRuby.js'); break;
					case 'scala': $this->addScriptLink($urlbase.'/scripts/shBrushScala.js'); break;
					case 'sql': $this->addScriptLink($urlbase.'/scripts/shBrushSql.js'); break;
					case 'vb': case 'vbnet': $this->addScriptLink($urlbase.'/scripts/shBrushVb.js'); break;
					case 'xml': case 'xhtml': case 'xslt': case 'html': 
						$this->addScriptLink($urlbase.'/scripts/shBrushXml.js');
						if (!defined('SYNTAX_HIGHLIGHT_XML')) { define('SYNTAX_HIGHLIGHT_XML', 1); }
					break;
					default: break;
				}
			}
		} else {
			$this->addScriptLink($urlbase.'/scripts/shBrushCss.js');
			$this->addScriptLink($urlbase.'/scripts/shBrushJScript.js');
			$this->addScriptLink($urlbase.'/scripts/shBrushPhp.js');
			$this->addScriptLink($urlbase.'/scripts/shBrushSql.js');
			$this->addScriptLink($urlbase.'/scripts/shBrushXml.js');
			if (!defined('SYNTAX_HIGHLIGHT_XML')) { define('SYNTAX_HIGHLIGHT_XML', 1); }
		}

		$this->addStyleLink($urlbase.'/styles/shCore.css');
		switch (strtolower($theme)) {
			case 'django': $this->addStyleLink($urlbase.'/styles/shThemeDjango.css'); break;
			case 'eclipse': $this->addStyleLink($urlbase.'/styles/shThemeEclipse.css'); break;
			case 'emacs': $this->addStyleLink($urlbase.'/styles/shThemeEmacs.css'); break;
			case 'fade to grey': $this->addStyleLink($urlbase.'/styles/shThemeFadeToGrey.css'); break;
			case 'midnight': $this->addStyleLink($urlbase.'/styles/shThemeMidnight.css'); break;
			case 'rdark': $this->addStyleLink($urlbase.'/styles/shThemeRDark.css'); break;
			default: $this->addStyleLink($urlbase.'/styles/shThemeDefault.css'); break;
		}
	}


	/*************************/
	/* HIGHLIGHT SOURCE CODE */
	/*************************/
	public function highlight($code, $brush='xml', $title='', $gutter=true, $firstLine=1, $collapse=false, $autoLinks=true) {
		$firstLine = (int)$firstLine;
		$str = 'brush: '.$brush;
		if ($gutter == false) { $str .= '; gutter: false'; }
		if ($firstLine > 1) { $str .= '; first-line: '.$firstLine; }
		if ($collapse == true) { $str .= '; collapse: true'; }
		if ($autoLinks == false) { $str .= '; auto-links: false'; }
		if ($collapse == false) { $str .= '; toolbar: false'; }
		if (defined('SYNTAX_HIGHLIGHT_XML')) {
			$htmlBrushes = array('as3', 'actionscript3', 'c-sharp', 'csharp', 'groovy', 'css', 'java', 'jfx', 'javafx', 
			'js', 'jscript', 'javascript', 'perl', 'pl', 'php', 'py', 'python', 'rails', 'ror', 'ruby', 'vb', 'vbnet');
			if (in_array($brush, $htmlBrushes)) { $str .= '; html-script: true'; }
		}
		$code = str_replace('<', '&lt;', $code);
		$titlestr = ($title != '') ? ' title="'.$title.'"' : '';
		$out = '<pre class="'.$str.'"'.$titlestr.'>'.$code."</pre>\n";
		if (!defined('SYNTAX_HIGHLIGHT_INIT')) {
			$out .= '<script>SyntaxHighlighter.all();</script>'."\n";
			define('SYNTAX_HIGHLIGHT_INIT', 1);
		}
		return $out;
	}


	/**************************************/
	/* LOAD LIGHTBOX (Elxis 4.x colorbox) */
	/**************************************/
	public function loadLightbox() {
		$this->addJQuery(); //required for observer
		$urlbase = eFactory::getElxis()->secureBase().'/includes/js/jquery/colorbox';
		$cssfile = 'colorbox.css';
		if (!defined('ELXIS_ADMIN') && ((int)eFactory::getElxis()->getConfig('MINICSS') > 0)) { $cssfile = 'colorbox_abs.css'; }
		if (defined('ELX_LIGHTBOX_LOADED')) {
			$this->observer('jslibrary', $urlbase.'/colorbox.js', 'colorbox', '1.3.19');
			$this->observer('cssfile', $urlbase.'/'.$cssfile);
			return;
		}
		$this->addLibrary('colorbox', $urlbase.'/colorbox.js', '1.3.19');
		$this->addStyleLink($urlbase.'/'.$cssfile);
		define('ELX_LIGHTBOX_LOADED', 1);
	}


	/*****************************/
	/* LOAD MEDIABOX (Elxis 5.x) */
	/*****************************/
	public function loadMediabox() {
		$urlbase = eFactory::getElxis()->secureBase().'/includes/js/mediabox';
		if (defined('ELX_MEDIABOX_LOADED')) {
			$this->observer('jsfile', $urlbase.'/wa-mediabox.min.js');
			$this->observer('cssfile', $urlbase.'/wa-mediabox.min.css');
			return;
		}
		$this->addScriptLink($urlbase.'/wa-mediabox.min.js');
		$this->addStyleLink($urlbase.'/wa-mediabox.min.css');
		define('ELX_MEDIABOX_LOADED', 1);
	}


	/****************************************/
	/* ADD META KEYWORD (CHECK FOR DOUBLES) */
	/****************************************/
	private function addKeyword($keyword) {
		if (!isset($this->keywords[$keyword])) {
			$this->keywords[] = $keyword;
		}
	}


	/****************************/
	/* MAKE DOCUMENT (FRONTEND) */
	/****************************/
	public function make() {
		if (defined('ELXIS_ADMIN')) {
			$this->makeAdmin();
			return;
		}

		$elxis = eFactory::getElxis();

		if (ELXIS_INNER == 0) {
			eFactory::getModule()->load();
			if ($elxis->getConfig('CRONJOBS') == 1) {
				$prob = (int)$elxis->getConfig('CRONJOBS_PROB');
				if ($prob > 0) {
					if (rand(1, 100) <= $prob) {
						$cron = $elxis->obj('cron');
						$cron->run();
						unset($cron);
					}
				}
			} else if ($elxis->getConfig('CRONJOBS') == 2) {
				if (isset($_GET['cronkey'])) {
					$k = trim($_GET['cronkey']);
					$p = sha1($elxis->getConfig('URL').$elxis->getConfig('ENCRYPT_KEY').$elxis->getConfig('REPO_PATH'));
					if ($p == $k) {
						$cron = $elxis->obj('cron');
						$cron->run(true);
						unset($cron);
						if (ob_get_length() > 0) { ob_end_clean(); }
						header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
						header('Last-Modified: '.gmdate('D, d M Y H:i:s').'GMT');
						header('Cache-Control: no-cache, must-revalidate');
						header('Pragma: no-cache');
						header('Content-type: text/plain; charset=utf-8');
						echo 'OK';
						exit;
					} else {
						exitPage::make('403', 'EDOC-0009', 'Invalid request!');
					}
				}
			}
		}

		$this->renderComponent();
		$this->parseTemplate();
		$this->renderPathway();
		$this->renderToolbar();
		if (ELXIS_INNER == 0) { $this->renderModules(); }
		$this->renderHead();

		if ($elxis->getConfig('STATISTICS') == 1) {
			$stats = $elxis->obj('stats');
			$stats->track();
			unset($stats);
		}

		$this->dispatch($elxis);
	}


	/*************************/
	/* MAKE BACKEND DOCUMENT */
	/*************************/
	private function makeAdmin() {
		$elxis = eFactory::getElxis();

		if ($elxis->getConfig('SSL') > 0) {
			$eURI = eFactory::getURI();
			if ($eURI->detectSSL() === false) {
				$url = $elxis->secureURL($eURI->getRealUriString(), true);
				$elxis->redirect($url);
			}
		}

		$status = $this->backAuthStatus();
		switch ($status) {
			case -5: exitPage::make('alogin', 'EDOC-0007', '1'); break;
			case -4: exitPage::make('403', 'EDOC-0002', 'You do not have permissions to access this page.'); break;
			case -3: exitPage::make('403', 'EDOC-0003', 'You need a higher access level to access this page.'); break;
			case -2: exitPage::make('403', 'EDOC-0004', 'You need a higher access level to access this page.'); break;
			case -1: exitPage::make('alogin', 'EDOC-0006', '0'); break;
			case 1: break;
			case 0: default:
				exitPage::make('error', 'EDOC-0005', 'There is something wrong with your credentials. You can not access this page.');
			break;
		}

		if (ELXIS_INNER == 0) { eFactory::getModule()->load(); }

		$this->renderComponent();
		$this->parseTemplate();
		$this->renderPathway();
		$this->renderToolbar();
		if (ELXIS_INNER == 0) { $this->renderModules(); }
		$this->renderHead();
		$this->dispatch($elxis);
	}


	/************************************************/
	/* GET USER'S AUTHENTICATION STATUS FOR BACKEND */
	/************************************************/
	private function backAuthStatus() {
		$elxis = eFactory::getElxis();
		$user = $elxis->user();
		$session = $elxis->session();
		$acl = $elxis->acl();
		$minlevel = ($elxis->getConfig('SECURITY_LEVEL') > 1) ? 100 : 70;
		if ($user->gid <> $session->gid) { return 0; }
		if (($user->gid  == 0) || ($user->gid == 7)) { return -1; }
		if ($user->gid == 6) { return -2; }
		if ($user->uid <> $session->uid) { return 0; }
		if ($user->uid < 1) { return -1; }
		if ($acl->getLevel() < $minlevel) { return -3; }
		if ($acl->check('administration', 'interface', 'login', 0) < 1) { return -4; }
		if (eFactory::getSession()->get('backauth', 0) !== 1) { return -5; }
		return 1;
	}


	/******************/
	/* PARSE TEMPLATE */
	/******************/
	private function parseTemplate() {
		if (!defined('ELXIS_ADMIN')) {
			$tpl = eFactory::getElxis()->getConfig('TEMPLATE');
			$path = ELXIS_PATH.'/templates';
			$exttype = 'template';
		} else {
			$tpl = eFactory::getElxis()->getConfig('ATEMPLATE');
			$path = ELXIS_PATH.'/templates/admin';
			$exttype = 'atemplate';
		}

		if (ELXIS_INNER == 1) {
			if (!file_exists($path.'/'.$tpl.'/inner.php')) {
				$tpl_file = ELXIS_PATH.'/templates/system/inner.php';
			} else {
				$tpl_file = $path.'/'.$tpl.'/inner.php';
			}
		} else {
			$tpl_file = $path.'/'.$tpl.'/index.php';
		}

		if (!file_exists($tpl_file)) {
			$tf = (ELXIS_INNER == 1) ? 'inner.php' : 'index.php';
			exitPage::make('error', 'EDOC-0001', 'File '.$tf.' does not exist in template '.$tpl.'!');
		}

		eFactory::getLang()->load($tpl, $exttype);
		ob_start();
		include($tpl_file);
		$this->buffer['template'] = ob_get_contents();
		ob_end_clean();
	}


	/***********************************/
	/* COUNT THE MODULES IN A POSITION */
	/***********************************/
	public function countModules($position) {
		if (ELXIS_INNER == 1) { return 0; }
		$eModule = eFactory::getModule();
		$eModule->load();
		return $eModule->countModules($position);
	}


	/***********************************************************/
	/* MARK TEMPLATE'S OUTPUT WITH HTML HEAD FOR LATER PROCESS */
	/***********************************************************/
	public function showHead() {
		echo '[:head:]';
	}


	/***********************************************************/
	/* MARK TEMPLATE'S OUTPUT WITH COMPONENT FOR LATER PROCESS */
	/***********************************************************/
	public function component() {
		echo '[:component:]';
	}


	/*******************************************************************/
	/* MARK TEMPLATE'S OUTPUT WITH MODULE NAME OR ID FOR LATER PROCESS */
	/*******************************************************************/
	public function module($modname, $style='') {
		if (ELXIS_INNER == 1) { return; }
		if ($modname == '') { return; }
		$this->replace_mark++;
		$mark = 'replmark'.$this->replace_mark;
		$this->replacements[$mark] = array('', $modname, $style, false);
		echo '[:'.$mark.':]';
	}


	/**********************************************************/
	/* MARK TEMPLATE'S OUTPUT WITH POSITION FOR LATER PROCESS */
	/**********************************************************/
	public function modules($position, $style='') {
		if (ELXIS_INNER == 1) { return; }
		if ($position == '') { return; }
		$this->replace_mark++;
		$mark = 'replmark'.$this->replace_mark;
		$this->replacements[$mark] = array($position, '', $style, true);
		echo '[:'.$mark.':]';
	}


	/***********************************************************/
	/* MARK TEMPLATE'S OUTPUT WITH PATHWAY FOR LATER PROCESS */
	/***********************************************************/
	public function pathway($pathway_here=false, $separator='nothing') {
		$pw = eFactory::getPathway();
		$pw->setYouAreHere($pathway_here);
		if ($separator != 'nothing') {
			$pw->setSeparator($separator);
		}
		echo '[:pathway:]';
	}


	/*********************************************************/
	/* MARK TEMPLATE'S OUTPUT WITH TOOLBAR FOR LATER PROCESS */
	/*********************************************************/
	public function toolbar() {
		echo '[:toolbar:]';
	}


	/******************/
	/* RENDER MODULES */
	/******************/
	private function renderModules() {
		if ($this->replacements) {
			$eModule = eFactory::getModule();
			foreach ($this->replacements as $mark => $element) {
				if ($element[3] == true) {
					$this->buffer['modules'][$mark] = $eModule->renderPosition($element[0], $element[2], $this->debug);
				} else {
					$this->buffer['modules'][$mark] = $eModule->renderModule($element[1], $element[2], $this->debug);
				}
			}
		}
	}


	/*****************************************************/
	/* RENDER COMPONENT AND CATCH OUTPUT INTO THE BUFFER */
	/*****************************************************/
	private function renderComponent() {
		$eURI = eFactory::getURI();
		$comp = $eURI->getComponent();

		$compfile = 'components/com_'.$comp.'/'.$comp.'.php';
		if ($this->debug > 1) {
			$ePerformance = eRegistry::get('ePerformance');
			$ePerformance->startBlock($comp, $compfile);
		}
		$this->beforeRender('com_'.$comp);
		elxisLoader::loadFile('includes/libraries/elxis/router.class.php');
		elxisLoader::loadFile($compfile);
		$class = $comp.'Router';
		$router = new $class();

		$notes = '';
		$elxmsg = filter_input(INPUT_GET, 'elxmsg', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$elxerror = filter_input(INPUT_GET, 'elxerror', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		if (trim($elxmsg) != '') { $notes .= '<div class="elx5_info">'.$elxmsg."</div>\n"; }
		if (trim($elxerror) != '') { $notes .= '<div class="elx5_error">'.$elxerror."</div>\n"; }

		ob_start();
		$router->route();
		$this->buffer['component'] = $notes.ob_get_contents();
		ob_end_clean();

		if ($this->debug > 1) {
			$ePerformance->stopBlock();
		}
	}


	/*************************************************/
	/* RENDER PATHWAY AND SET OUTPUT INTO THE BUFFER */
	/*************************************************/
	private function renderPathway() {
		$this->buffer['pathway'] = eFactory::getPathway()->getHTMLNodes();
	}


	/*************************************************/
	/* RENDER TOOLBAR AND SET OUTPUT INTO THE BUFFER */
	/*************************************************/
	private function renderToolbar() {
		if (ELXIS_INNER == 1) { $this->buffer['toolbar'] = ''; return; }
		$this->buffer['toolbar'] = eFactory::getElxis()->obj('toolbar')->getHTML();
	}


	/******************************************/
	/* EXECUTE BEFORE RENDERING THE EXTENSION */
	/******************************************/
	private function beforeRender($extension) {
		$eLang = eFactory::getLang();

		$action = (defined('ELXIS_ADMIN')) ? 'manage' : 'view';
		$v = eFactory::getElxis()->acl()->check('component', $extension, $action);
		if ($v < 1) {
			exitPage::make('403', 'EDOC-0008', $eLang->get('NOTALLOWACCPAGE'));
		}

		$eLang->load($extension, 'component');
	}


	/***********************/
	/* REORDER STYLESHEETS */
	/***********************/
	private function reorderCSS() {
		if (!$this->stylesheets) { return; }
		$base = eFactory::getElxis()->secureBase();
		$sys = array();
		$tpl = array();
		$rest = array();
		foreach ($this->stylesheets as $href => $arr) {
			if (strpos($href, $base.'/templates/system/') === 0) {
				$sys[$href] = $arr;
			} elseif (strpos($href, $base.'/templates/') === 0) {
				$tpl[$href] = $arr;
			} else {
				$rest[$href] = $arr;
			}
		}

		$this->stylesheets = array_merge($sys, $tpl, $rest);
	}


	/********************************/
	/* RENDER DOCUMENT HEAD SECTION */
	/********************************/
	private function renderHead() {
		$this->reorderCSS();
		ob_start();
		echo $this->fetchHead();
		$this->buffer['head'] = ob_get_contents();
		ob_end_clean();
	}


	/*************************/
	/* DISPATCH THE DOCUMENT */
	/*************************/
	private function dispatch($elxis) {
		$compress = $elxis->getConfig('GZIP');

		$html = str_replace('[:head:]', $this->buffer['head'], $this->buffer['template']);
		unset($this->buffer['template']);

		if ($this->debug > 1) {
			$ePerformance = eRegistry::get('ePerformance');
			$this->buffer['component'] .= $ePerformance->makeReport($this->debug);
		}

		$html = str_replace('[:component:]', $this->buffer['component'], $html);
		unset($this->buffer['component']);

		if (isset($this->buffer['pathway'])) {
			$html = str_replace('[:pathway:]', $this->buffer['pathway'], $html);
			unset($this->buffer['pathway']);
		}

		if (isset($this->buffer['toolbar'])) {
			$html = str_replace('[:toolbar:]', $this->buffer['toolbar'], $html);
			unset($this->buffer['toolbar']);
		}

		if (count($this->buffer['modules']) > 0) {
			foreach($this->buffer['modules'] as $mark => $buffer) {
				$html = str_replace('[:'.$mark.':]', $buffer, $html);
			}
		}
		unset($this->buffer['modules']);

		if (!defined('ELXIS_ADMIN')) {
			$user_css_relpath = 'templates/system/css/user.config'.eFactory::getLang()->getinfo('RTLSFX').'.css';
			if (file_exists(ELXIS_PATH.'/'.$user_css_relpath)) {
				$fs = filesize(ELXIS_PATH.'/'.$user_css_relpath);
				if ($fs > 0) {
					$ft = filemtime(ELXIS_PATH.'/'.$user_css_relpath);
					$usercss = "\t".'<link rel="stylesheet" href="'.$elxis->secureBase().'/'.$user_css_relpath.'?v='.$ft.'" type="text/css" media="all" />'."\n";
					if (strpos($html, '</head>') !== false) {
						$html = str_replace('</head>', $usercss.'</head>', $html);
					}
				}
			}
		}

		$this->setHeader('Content-type', $this->contenttype.'; charset=utf-8');
		//$this->setHeader('Expires', gmdate('D, d M Y H:i:s', time()+360).'GMT');
		$this->setHeader('Expires', 'Mon, 1 Jan 2001 00:00:00 GMT', true); //Expires in the past
		$this->setHeader('Last-Modified', gmdate("D, d M Y H:i:s").' GMT', true); //Always modified
		$this->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0', false);
		$this->setHeader('Pragma', 'no-cache'); // HTTP 1.0

		if ($elxis->getConfig('XFOPTIONS') != '') {
			$this->setHeader('X-Frame-Options', $elxis->getConfig('XFOPTIONS'));
		}
		if ($elxis->getConfig('CSP') != '') {
			$this->setHeader('Content-Security-Policy', $elxis->getConfig('CSP'));
		}
		$this->setHeader('X-XSS-Protection', '1; mode=block');
		if ($elxis->getConfig('SECURITY_LEVEL') > 0) {
			$this->setHeader('X-Content-Type-Options', 'nosniff');
		}

		if ($compress && !ini_get('zlib.output_compression') && ini_get('output_handler')!='ob_gzhandler') {
			$html = $this->compress($html);
		}

		$this->sendHeaders();
		echo $html;
	}


	/***************************/
	/* COMPRESS DATA WITH GZIP */
	/***************************/
	private function compress($data) {
		$encoding = $this->clientEncoding();
		if ($encoding === false) { return $data; }
		//if (!extension_loaded('zlib') || ini_get('zlib.output_compression')) { return $data; }
		if (headers_sent()) { return $data; }
		if (connection_status() !== 0) { return $data; }
		$gzdata = gzencode($data, 4);
		$size = strlen($gzdata);
		$this->setHeader('Content-Encoding', $encoding, true);
		$this->setHeader('Content-Length', $size, true);
		$this->setHeader('X-Content-Encoded-By', 'Elxis', true);
		return $gzdata;
	}


	/**************/
	/* SET HEADER */
	/**************/
	private function setHeader($name, $value, $force=false) {
		if (isset($this->headers[$name])) {
			if ($force) {
				$this->headers[$name] = $value;
			}
		} else {
			$this->headers[$name] = $value;
		}
	}


	/***************************/
	/* SEND HEADERS TO BROWSER */
	/***************************/
	private function sendHeaders() {
		if (!headers_sent()) {
			if (count($this->headers) > 0) {
				foreach ($this->headers as $name => $value) {
					if ($name == 'status') {
						header('Status: '.$value, null, (int)$value);
					} else {
						header($name.': '.$value);
					}
				}
			}
		}
	}


	/*********************/
	/* ACTIVATE OBSERVER */
	/*********************/
	public function beginObserver() {
		$this->observe = true;
		$this->observedItems = array();
	}


	/*************************/
	/* DE-ACTIVATE OBESERVER */
	/*************************/
	public function endObserver() {
		$this->observe = false;
		$this->observedItems = array();
	}


	/***************************/
	/* APPEND DATA TO OBSERVER */
	/***************************/
	private function observer($type, $contents, $lib='', $v='') {
		if (!$this->observe) { return; }
		$this->observedItems[] = array('type' => $type, 'contents' => $contents, 'libn' => $lib, 'libv' => $v);
	}


	/**********************/
	/* GET OBSERVED ITEMS */
	/**********************/
	public function getObserved() {
		return $this->observedItems;
	}


	/********************************************/
	/* CHECK IF CLIENT SUPPORTS COMPRESSED DATA */
	/********************************************/
	private function clientEncoding() {
		if (!isset($_SERVER['HTTP_ACCEPT_ENCODING'])) { return false; }
		if (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== false) { return 'x-gzip'; }
		if (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false) { return 'gzip'; }
		return false;
	}

}

?>