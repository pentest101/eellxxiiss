<?php 
/**
* @version		$Id: parameters.class.php 2398 2021-04-08 17:46:50Z IOS $
* @package		Elxis
* @subpackage	XML
* @copyright	Copyright (c) 2006-2021 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisParameters {

	private $raw = null; //raw parameters string or array
	private $path = '';
	private $type = 'component';
	private $params = null; //instance of stdClass of parsed raw parameters
	private $xml = null; //simple xml instance
	private $hasParams = false;
	private $errormsg = ''; //last error message
	private $lang = 'en'; //current language indentifier
	private $deflang = 'en'; //site default language
	private $dir = 'ltr'; //current language direction
	private $langFile = ''; //language file to include (if any) for multilingual labels/descriptions
	private $params_group = 1;
	private $uri_lang = '';
	private $translate = false;
	private $uploadFields = array();
	private $groupsVisibility = array();
	private $multilinguism = 0;
	private $form = null; //Elxis 5.x form instance


	/***************/
	/* CONSTRUCTOR */
	/***************/
	public function __construct($raw, $path='', $type='component') {
	    $eLang = eFactory::getLang();
	    $elxis = eFactory::getElxis();

		$this->raw = $raw;
	    $this->path = $path;
	    $this->type = $type;
	    $this->lang = $eLang->currentLang();
	    $this->deflang = $elxis->getConfig('LANG');
	    $this->dir = $eLang->getinfo('DIR');
		$this->uri_lang = eFactory::getURI()->getUriLang();
		$this->multilinguism = $elxis->getConfig('MULTILINGUISM');
		if ($this->multilinguism == 1) {
			if ($this->uri_lang != '') { $this->translate = true; }
		}
	    $this->params = $this->parse($raw);
	}


	/**************************/
	/* SET VALUE TO PARAMETER */
	/**************************/
	public function set($key, $value='') {
		$this->params->$key = $value;
		return $value;
	}


	/******************************************/
	/* SET DEFAULT VALUE IF NO VALUE ASSIGNED */
	/******************************************/
	public function def($key, $value='') {
	    return $this->set($key, $this->get($key, $value));
	}


	/*************************/
	/* GET PARAMETER'S VALUE */
	/*************************/
	public function get($key, $default='') {
		if ($key == '') { return $default; }
	    if (isset($this->params->$key)) {
	        return ($this->params->$key === '') ? $default : $this->params->$key;
		} else {
		    return $default;
		}
	}


	/**************************************/
	/* GET MULTILINGUAL PARAMETER'S VALUE */
	/**************************************/
	public function getML($key, $default='') {
		if ($this->multilinguism == 0) { return $this->get($key, $default); }
		$mlkey = $key.'_ml'.$this->lang;
		if (isset($this->params->$mlkey)) {
			if ($this->params->$mlkey != '') { return $this->params->$mlkey; }
		}
		return $this->get($key, $default);
	}


	/*********************************************************/
	/* GET ALL PARAMS FROM AN XML FILE WITH THEIR ATTRIBUTES */
	/*********************************************************/
	public function allParams($xmlpath='') {
		if ($xmlpath == '') { $xmlpath = $this->path; }
		if ((trim($xmlpath) == '') || !is_file($xmlpath)) { return array(); }
		libxml_use_internal_errors(true);
		$xmlDoc = simplexml_load_file($xmlpath, 'SimpleXMLElement');
		if (!$xmlDoc) { return array(); }
		if (($xmlDoc->getName() != 'package') && ($xmlDoc->getName() != 'elxisparameters')) { return array(); }
		if (!isset($xmlDoc->params)) { return array(); }
		if (count($xmlDoc->params->children()) == 0) { return array(); }

		$all_params = array();
		foreach ($xmlDoc->params as $params) {
			if (!isset($params->param)) { continue; }
			foreach ($params->children() as $param) {
				$attrs = $param->attributes();
				if ($attrs && isset($attrs['name'])) {
					$name = (string)$attrs['name'];
					$all_params[$name] = array();
					foreach ($attrs as $k => $v) {
						if ($k == 'name') { continue; }
						$v = (string)$v;
						$all_params[$name][$k] = trim($v);
					}
				}
			}
		}

		return $all_params;
	}


	/**************************************************/
	/* GET UPLOAD FIELDS FOUND IN RENDERED PARAMETERS */
	/**************************************************/
	public function getUpload() {
		return $this->uploadFields;
	}


	/******************************/
	/* PARSE RAW STRING OR ARRAY */
	/******************************/
	private function parse($raw) {
	    if (is_string($raw)) {
			$lines = explode("\n", $raw);
		} else if (is_array($raw)) {
		    $lines = $raw;
		} else {
		    $lines = array();
		}

		$obj = new stdClass();
	    if (!$lines) { return $obj; }
	    foreach ($lines as $line) {
	        $line = eUTF::trim($line);
	        if ($line == '') { continue; }
	        if ($pos = strpos($line, '=')) {
	        	$property = trim(substr($line, 0, $pos));
	        	$value = eUTF::trim(eUTF::substr($line, $pos + 1, 1000));
	            if ($value == 'false') { $value = false; }
	            if ($value == 'true') {	$value = true; }
	            if ((eUTF::substr($value, 0, 1) == '"') && (eUTF::substr($value, -1, 1) == '"')) {
	                $value = stripcslashes(eUTF::substr($value, 1, eUTF::strlen($value) - 2));
	            }
				$obj->$property = $value;
	        }
	    }

	    return $obj;
	}


	/***********************/
	/* PERFORM FILE UPLOAD */
	/***********************/
	private function uploadFile($name, $attrs) {
		if (!defined('ELXIS_ADMIN')) { return false; }
		if (!isset($_FILES[$name]) || !is_array($_FILES[$name])) { return false; }
		if (eFactory::getElxis()->getConfig('SECURITY_LEVEL') > 0) { return false; }

		$eFiles = eFactory::getFiles();
		$file = $_FILES[$name];
		if (!isset($file['tmp_name']) || ($file['tmp_name'] == '') || !is_uploaded_file($file['tmp_name'])) { return false; }
		$fname = eUTF::strtolower($file['name']);
		$ext = $eFiles->getExtension($fname);
		if ($ext == '') { return false; }

		$allowed_exts = array(
			'png', 'jpg', 'jpeg', 'gif', 'ico', 'svg', 'psd', 'bmp', 'tiff', 'tif', 
			'mp3', 'ogg', 'ogv', 'avi', 'mpg', 'mpeg', 'wma', 'wmv', 'mkv', 'aac', 'mp4', 'mp3', 'webm', 
			'mpa', '3gp', 'asf', 'asx', 'mov', 'rm', 'ra', 'm4a', 'mid', 'wav', 'flv', 'swf', 
			'doc', 'docx', 'pps', 'ppt', 'smil', 'xlsx', 'xls', 'csv', 'odt', 'odp', 'odf', 'ods', 'rtf', 'pdf', 'txt', 'srt', 'vtt', 
			'xsl', 'xslt', 'css', 'xml', 
			'zip', 'rar', 'tar', 'gz', 'bzip2', 'gzip'
		);
  		if (!in_array($ext, $allowed_exts)) { return false; }
		if (!isset($attrs['path']) || ($attrs['path'] == '') || ($attrs['path'] == '/')) { return false; }
		if (isset($attrs['filetype']) && ($attrs['filetype'] != '')) {
			$valid_exts = explode(',', $attrs['filetype']);
			if (!in_array($ext, $valid_exts)) { return false; }
		}

		if (isset($attrs['maxsize']) && (intval($attrs['maxsize']) > 0)) {
			if ($file['size'] > (int)$attrs['maxsize']) { return false; }
		}

		$lowfilename = $name.'.'.$ext;
		$resizewidth = (isset($attrs['resizewidth'])) ? (int)$attrs['resizewidth'] : 0;
		$resizeheight = (isset($attrs['resizeheight'])) ? (int)$attrs['resizeheight'] : 0;

		$attrs['path'] = $this->msReplacer($attrs['path']);

		if ($eFiles->upload($file['tmp_name'], $attrs['path'].$lowfilename)) {
			if (($resizewidth > 0) && ($resizeheight > 0) && in_array($ext, array('png', 'jpg', 'jpeg', 'gif'))) {
				$eFiles->resizeImage($attrs['path'].$lowfilename, $resizewidth, $resizeheight);
			}
			return $attrs['path'].$lowfilename;
		}

		return false;
	}


	/***************************/
	/* MULTISITE PATH REPLACER */
	/***************************/
	private function msReplacer($string) {
		if (strpos($string, 'multisite') === false) { return $string; }
		if (defined('ELXIS_MULTISITE')) {
			if (ELXIS_MULTISITE > 1) {
				$ms_replace = 'site'.ELXIS_MULTISITE; 
				$string = str_replace('{multisite}', $ms_replace, $string);
				$string = str_replace('{multisite/}', $ms_replace.'/', $string);
				$string = str_replace('{/multisite}', '/'.$ms_replace, $string);
				$string = str_replace('{/multisite/}', '/'.$ms_replace.'/', $string);	
				return $string;
			}
		}

		$string = str_replace('{multisite}', '', $string);
		$string = str_replace('{multisite/}', '', $string);
		$string = str_replace('{/multisite}', '', $string);
		$string = str_replace('{/multisite/}', '', $string);
		return $string;
	}


	/*******************************************************/
	/* CONVERT AN ARRAY OF PARAMS (POST REQUEST) TO STRING */
	/*******************************************************/
	public function toString($params, $integers=array(), $strings=array()) {
		$all_params = array();
		$mlparams = array();

		if ($this->path != '') {
			$all_params = $this->allParams();
			if (count($all_params) == 0) { return ''; }

			//fix elxis 5.x radio displayed as checkboxes (like yes/no)
			foreach ($all_params as $k => $data) {
				if ($data['type'] == 'radio') {
					if (!isset($params[$k])) {
						$params[$k] = is_numeric($data['default']) ? 0 : $data['default'];
					}
				}
			}

			if ($this->multilinguism == 1) {
				foreach ($all_params as $k => $v) {
					if (($v['type'] == 'text') || ($v['type'] == 'textarea')) {
						if (isset($v['multilingual']) && ($v['multilingual'] == 1)) { $mlparams[] = $k; }
					}
				}
			}

			if ($params) {
				foreach ($params as $k => $v) {
					$isml = false;
					if ($mlparams) {
						foreach ($mlparams as $mlparam) {
							if (strpos($k, $mlparam.'_ml') === 0) { $isml = true; break; }
						}
					}
					if (!$isml) {
						if (!isset($all_params[$k])) { unset($params[$k]); }
					} else {
						if (trim($v) == '') { continue; }//dont save empty translations
					}
				}
			}
			foreach ($all_params as $k => $v) {
				if (!isset($params[$k])) { $params[$k] = ''; }
				if ($v['type'] == 'file') {
					$newvalue = $this->uploadFile($k, $v);
					if ($newvalue !== false) {
						$params[$k] = $newvalue;
					}
				}
			}
		}

		if (!is_array($params) || (count($params) == 0)) { return ''; }
		$arr = array();
		foreach ($params as $k => $v) {
			if (!preg_match("/^([a-z0-9\-\_])+$/i", $k)) { continue; }
			if ($all_params) {
				if (!isset($all_params[$k])) { continue; }
			}
			if (in_array($k, $integers)) {
				$v = (int)$v;
			} else if (in_array($k, $strings)) {
				$v = filter_var($v, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
			}
			$arr[] = $k.'='.$v;
		}

		if (!is_array($params) || (count($params) == 0)) { return ''; }
		$arr = array();
		foreach ($params as $k => $v) {
			if (!preg_match("/^([a-z0-9\-\_])+$/i", $k)) { continue; }
			if ($all_params) {
				$isml = false;
				if ($mlparams) {
					foreach ($mlparams as $mlparam) {
						if (strpos($k, $mlparam.'_ml') === 0) { $isml = true; break; }
					}
				}
				if (!$isml) {
					if (!isset($all_params[$k])) { continue; }
				} else {
					if (trim($v) == '') { continue; }//dont save empty translations
				}
			}

			if (in_array($k, $integers)) {
				$v = (int)$v;
			} else if (in_array($k, $strings)) {
				$v = filter_var($v, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
			}
			$arr[] = $k.'='.$v;
		}

		for ($i=0; $i < count($arr); $i++) {
			if (strstr($arr[$i], "\n")) {
				$arr[$i] = eUTF::str_replace("\n", '<br />', $arr[$i]);
			}
		}

		$str = implode("\n", $arr);
		return $str;
	}

		
	/*************************************************/
	/* RETURN TEXTAREA WITH RAW TEXT ON RENDER ERROR */
	/*************************************************/
	private function displayRaw($raw) {
		return '<textarea name="params" cols="40" dir="ltr" rows="10" class="elx5_textarea">'.$raw."</textarea>\n";
	}


	/**************************/
	/* RENDER PARAMETERS HTML */
	/**************************/
	public function render($style=array(), $show_description=false, $formoptions=array()) {
		$this->errormsg = '';
		$this->langFile = '';
	    if ($this->path != '') {
	        if (!is_object($this->xml)) {
				libxml_use_internal_errors(true);
	        	$xmlDoc = simplexml_load_file($this->path, 'SimpleXMLElement');
	        	if (!$xmlDoc) {
					foreach (libxml_get_errors() as $error) {
						$this->errormsg = 'Could not parse XML file. Error: '.$error->message.'. Line: '.$error->line;
						break;
					}
					return $this->displayRaw($this->raw);
	        	}

				if (($xmlDoc->getName() != 'package') && ($xmlDoc->getName() != 'elxisparameters')) {
					$this->errormsg = 'The XML file is not a valid Elxis extension XML!';
					return $this->displayRaw($this->raw);
				}

				$ok = true;
				$attrs = $xmlDoc->attributes();
				if ($attrs) {
					if (!isset($attrs['type']) || ((string)$attrs['type'] != $this->type)) { $ok = false; }
				} else {
					$ok = false;
				}
				
				if (!$ok) {
					$this->errormsg = 'The XML file is not a valid Elxis extension XML for '.$this->type.'!';
					return $this->displayRaw($this->raw);
				}

				if (isset($xmlDoc->language)) {
					$lng = $this->lang;
					$found = false;
					if (isset($xmlDoc->language->$lng)) {
						$langfile = ELXIS_PATH.'/language/'.$lng.'/';
						$langfile .= (string)$xmlDoc->language->$lng;
						if (file_exists($langfile)) {
							$this->langFile = $langfile;
							$found = true;
						}
						unset($langfile);
					}
					if (!$found && isset($xmlDoc->language->en)) {
						$langfile = ELXIS_PATH.'/language/en/';
						$langfile .= (string)$xmlDoc->language->en;
						if (file_exists($langfile)) {
							$this->langFile = $langfile;
						}
						unset($langfile);
					}
					unset($lng, $found);
				}

				if (isset($xmlDoc->params)) {
					if (count($xmlDoc->params->children()) > 0) {
						$this->hasParams = true;
					} else {
						$this->hasParams = false;
					}
				} else {
					$this->hasParams = false;
				}

				$this->xml = $xmlDoc;
	        	unset($xmlDoc);
			}
		}

	    if (!is_object($this->xml)) {
	    	return $this->displayRaw($this->raw);
	    }

		$eLang = eFactory::getLang();
		if ($this->langFile != '') {
			$eLang->loadFile($this->langFile);
		}

		$css_sfx = '';
		if (is_array($style) && (count($style) > 0)) {
			if (isset($style['css_sfx']) && ($style['css_sfx'] != '')) { $css_sfx = $style['css_sfx']; }
		}

		$html = '';
		if (($show_description == true) && isset($this->xml->description)) {
			$description = (string)$this->xml->description;
			if ($description != '') {
				$html .= '<div class="elx5_info'.$css_sfx.'">'."\n";
			    $html .= $eLang->silentGet($description)."\n";
			    $html .= "</div>\n";
			}
		}
		
		if (!$this->hasParams) {
			$html .= '<div class="elx5_info'.$css_sfx.'">'.$eLang->get('NO_PARAMS')."</div>\n";
			return $html;
		}

		elxisLoader::loadFile('includes/libraries/elxis/form5.class.php');
		if (!$formoptions) { $formoptions = array(); }
		$formoptions['idprefix'] = 'par';
		$formoptions['returnhtml'] = true;
		$this->form = new elxis5Form($formoptions);

		$data = array();
		foreach ($this->xml->params as $params) { //first pass: render params
			if (!isset($params->param)) { continue; }
			$attrs = $params->attributes();
			$tbldata = array('collapsed' => 0, 'groupname' => '', 'elements' => array());
			$groupid = $this->params_group;
			if ($attrs) {
				$group = '';
				if (isset($attrs['group'])) { $group = (string)$attrs['group']; }
				if (trim($group) != '') { $tbldata['groupname'] = $eLang->silentGet($group); }
				if (isset($attrs['groupid']) && (intval($attrs['groupid']) > 999)) { $groupid = (int)$attrs['groupid']; }
				if (isset($attrs['collapsed'])) { $tbldata['collapsed'] = (int)$attrs['collapsed']; }
				unset($group);
			}

			foreach ($params->children() as $param) {
				$tbldata['elements'][] = $this->renderParam($param, $eLang);
			}
			$this->params_group++;
			$data[$groupid] = $tbldata;
			unset($tbldata, $attrs, $groupid);
		}

		if (!$data) {
			$html .= '<div class="elx5_warning'.$css_sfx.'">'.$eLang->get('NO_PARAMS')."</div>\n";
			return $html;
		}

		foreach ($data as $groupid => $groupdata) { //second pass: make HTML
			$groupclass = 'elx5_zero';
			$can_collapse = false;
			if ($groupid > 999) {
				if (isset($this->groupsVisibility[$groupid])) {
					if ($this->groupsVisibility[$groupid] == 0) { $groupclass =  'elx5_invisible'; }
				}
			}

			$html .= '<div id="params_group_'.$groupid.'" class="'.$groupclass.'">'."\n";
			if ($groupdata['groupname'] != '') {
				$groupinclass = ($groupdata['collapsed'] == 1) ? 'elx5_invisible' : 'elx5_zero';
				$html .= '<fieldset class="elx5_fieldset"><legend><a href="javascript:void(null);" onclick="elx5Toggle(\'params_groupin_'.$groupid.'\')"><span>&#8643;&#8638;</span> '.$groupdata['groupname'].'</a></legend>'."\n";
				$html .= '<div id="params_groupin_'.$groupid.'" class="'.$groupinclass.'">'."\n";
			}

			foreach ($groupdata['elements'] as $elementhtml) { $html .= $elementhtml; }
			if ($groupdata['groupname'] != '') {
				$html .= "</div>\n";
				$html .= "</fieldset>\n";
			}
			$html .= "</div>\n";//params_group_
		}

		return $html;
	}


	/********************/
	/* RENDER PARAMETER */
	/********************/
	public function renderParam($param, $eLang=false) {
		if (!$eLang) { $eLang = eFactory::getLang(); }//Elxis 4.x compatibility
		
		$result = array();
		$attrs = $param->attributes();
		$name = isset($attrs['name']) ? (string)$attrs['name'] : '';
		$label = isset($attrs['label']) ? (string)$attrs['label'] : '';
		$labelprf = isset($attrs['labelprf']) ? (string)$attrs['labelprf'] : '';
		$labelsfx = isset($attrs['labelsfx']) ? (string)$attrs['labelsfx'] : '';
		if (trim($label) != '') {
			$label = $eLang->silentGet($label);
			if ($labelprf != '') { $label = $labelprf.$label; }
			if ($labelsfx != '') { $label .= $labelsfx; }
		}

		$def_value = (string)$attrs['default'];
		$value = $this->get($name, $def_value);
		$type = (string)$attrs['type'];
		$item_tip = isset($attrs['description']) ? $eLang->silentGet((string)$attrs['description']) : '';
		$item_label = ($label != '') ? $label : $name;

		$method = 'form_'.$type;
		if (method_exists($this, $method)) {
			$result = $this->$method($name, $value, $param, $item_label, $item_tip);
		} else {
			$result = $this->form_text($name, $value, $param, $item_label, $item_tip);
		}

		return $result;
	}


	/**************************/
	/* GET LAST ERROR MESSAGE */
	/**************************/
	public function getErrorMsg() {
		return $this->errormsg;
	}


	/********************/
	/* MAKE INPUT FIELD */
	/********************/
	private function makeInput($name, $value, $node, $inputtype, $item_label, $item_tip) {
		if ($inputtype == '') { $inputtype = 'text'; }
		$attrs = $node->attributes();

		$multilingual = 0;
		if ($this->multilinguism == 1) {
			$multilingual = isset($attrs['multilingual']) ? (int)$attrs['multilingual'] : 0;
		}

		$dir = 'ltr';
		if (isset($attrs['dir']) && (strtolower((string)$attrs['dir']) == 'rtl')) {
			if ($this->dir == 'rtl') { $dir = 'rtl'; }
		}

		$item_attrs = array('id' => 'params'.$name, 'dir' => $dir);
		if ($item_tip != '') { $item_attrs['tip'] = $item_tip; }
		if (isset($attrs['maxlength']) && (intval($attrs['maxlength']) > 0)) { $item_attrs['maxlength'] = intval($attrs['maxlength']); }
		if (isset($attrs['required']) && (intval($attrs['required']) == 1)) { $item_attrs['required'] = 'required'; }
		if (isset($attrs['placeholder']) && ($attrs['placeholder'] != '')) {
			$item_attrs['placeholder'] = $attrs['placeholder'];
		} else {
			$label = isset($attrs['label']) ? (string)$attrs['label'] : '';
			if (trim($label) != '') { $item_attrs['placeholder'] = eFactory::getLang()->silentGet($label); }
			unset($label);
		}

		if ($inputtype == 'number') {
			if (isset($attrs['min'])) { $v = trim($attrs['min']); if (is_numeric($v)) { $item_attrs['min'] = $v; } }
			if (isset($attrs['max'])) { $v = trim($attrs['max']); if (is_numeric($v)) { $item_attrs['max'] = $v; } }
			if (isset($attrs['step'])) { $v = trim($attrs['step']); if (is_numeric($v)) { $item_attrs['step'] = $v; } }
		}

		if ($multilingual == 1) {
			$item_attrs['translations'] = array();
			if ($this->params) {
				$parr = get_object_vars($this->params);
				if ($parr) {
					foreach ($parr as $k => $v) {
						if (strpos($k, $name.'_ml') === 0) {
							$lng = str_replace($name.'_ml', '', $k);
							if (($lng == '') || ($lng == $this->deflang)) { continue; }
							$item_attrs['translations'][$lng] = $v;
						}
					}
				}
			}

			$trdata = array();//not used for XML parameters
			$html = $this->form->addMLText('params['.$name.']', $trdata, $value, $item_label, $item_attrs);
		} else {
			$html = $this->form->addInput($inputtype, 'params['.$name.']', $value, $item_label, $item_attrs);
		}
		return $html;
	}


	/*******************/
	/* MAKE TEXT FIELD */
	/*******************/
	private function form_text($name, $value, $node, $item_label, $item_tip) {
		return $this->makeInput($name, $value, $node, 'text', $item_label, $item_tip);
	}


	/*********************/
	/* MAKE NUMBER FIELD */
	/*********************/
	private function form_number($name, $value, $node, $item_label, $item_tip) {
		return $this->makeInput($name, $value, $node, 'number', $item_label, $item_tip);
	}


	/*********************/
	/* MAKE NUMBER FIELD */
	/*********************/
	private function form_email($name, $value, $node, $item_label, $item_tip) {
		return $this->makeInput($name, $value, $node, 'email', $item_label, $item_tip);
	}


	/*********************/
	/* MAKE NUMBER FIELD */
	/*********************/
	private function form_tel($name, $value, $node, $item_label, $item_tip) {
		return $this->makeInput($name, $value, $node, 'tel', $item_label, $item_tip);
	}


	/******************/
	/* MAKE URL FIELD */
	/******************/
	private function form_url($name, $value, $node, $item_label, $item_tip) {
		return $this->makeInput($name, $value, $node, 'url', $item_label, $item_tip);
	}


	/***********************/
	/* MAKE PASSWORD FIELD */
	/***********************/
	private function form_password($name, $value, $node, $item_label, $item_tip) {
		return $this->makeInput($name, $value, $node, 'password', $item_label, $item_tip);
	}


	/************************************************/
	/* MAKE DROP DOWN SELECT LIST (ALIAS OF "LIST") */
	/************************************************/
	private function form_select($name, $value, $node, $item_label, $item_tip) {
		return $this->form_list($name, $value, $node, $item_label, $item_tip);
	}


	/******************************/
	/* MAKE DROP DOWN SELECT LIST */
	/******************************/
	private function form_list($name, $value, $node, $item_label, $item_tip) {
		$eLang = eFactory::getLang();
		$attrs = $node->attributes();

		$options = array();
		$ashow = array();
		$ahide = array();
		$children = $node->children();
		if ($children) {
			$index = 0;
			foreach ($children as $child) {
				$attr2 = $child->attributes();
				$val = isset($attr2['value']) ? (string)$attr2['value'] : '';
				$show = isset($attr2['show']) ? (string)$attr2['show'] : '';
				$show = trim($show);
				$hide = isset($attr2['hide']) ? (string)$attr2['hide'] : '';
				$hide = trim($hide);

				if ($show != '') {
					$ashow[] = $index.':'.$show;
					if ($val == $value) {
						$grids = explode(',',$show);
						foreach ($grids as $grid) {
							$grid = (int)$grid;
							if ($grid > 999) { $this->groupsVisibility[$grid] = 1; }
						}
					}
				}

				if ($hide != '') {
					$ahide[] = $index.':'.$hide;
					if ($val == $value) {
						$grids = explode(',',$hide);
						foreach ($grids as $grid) {
							$grid = (int)$grid;
							if ($grid > 999) { $this->groupsVisibility[$grid] = 0; }
						}
					}
				}

				$text = (string)$child[0];
				if (($text != '') && !is_numeric($text)) { $text = $eLang->silentGet($text); }
				$disabled = 0;
				if (isset($attr2['disabled']) && (((string)$attr2['disabled'] == 'disabled') || ((int)$attr2['disabled'] == 1))) { $disabled = 1; }
				$options[] = $this->form->makeOption($val, $text, array(), $disabled);
				$index++;
			}
		}

		$dir = 'ltr';
		if ($this->dir == 'rtl') {
			if (isset($attrs['dir']) && (strtolower((string)$attrs['dir']) == 'rtl')) { $dir = 'rtl'; }
		}
		$item_attrs = array('id' => 'params'.$name, 'dir' => $dir );
		if ($item_tip != '') { $item_attrs['tip'] = $item_tip; }
		if (isset($attrs['required']) && (intval($attrs['required']) == 1)) { $item_attrs['required'] = 'required'; }

		$onchange_str = '';
		if (count($ashow) > 0) { $onchange_str .= 'elx5ShowParams(this, \''.implode(';', $ashow).'\', 1);'; }
		if (count($ahide) > 0) {
			if ($onchange_str != '') { $onchange_str .= ' '; }
			$onchange_str .= 'elx5HideParams(this, \''.implode(';', $ahide).'\', 1);';
		}
		if ($onchange_str != '') { $item_attrs['onchange'] = $onchange_str; }

		return $this->form->addSelect('params['.$name.']', $item_label, $value, $options, $item_attrs);
	}


	/***************************/
	/* MAKE FOLDER SELECT LIST */
	/***************************/
	private function form_folderlist($name, $value, $node, $item_label, $item_tip) {
		$attrs = $node->attributes();
		$noselect = (isset($attrs['noselect'])) ? trim($attrs['noselect']) : '';
		$options = array();
		$options[] = $this->form->makeOption($noselect, '- '.eFactory::getLang()->get('SELECT').' -');
		if (isset($attrs['directory'])) {
			$dir = str_replace(DIRECTORY_SEPARATOR, '/', (string)$attrs['directory']);
			$dir = $this->msReplacer($dir);
			$dir = preg_replace('/^(\/)/', '', $dir);
			$dir = preg_replace('/(\/)$/', '', $dir);
			if ($dir != '') {
				$path = ELXIS_PATH.'/'.$dir.'/';
				if (file_exists($path) && is_dir($path)) {
					$folders = eFactory::getFiles()->listFolders($dir);
					if ($folders && (count($folders) > 0)) {
						foreach ($folders as $folder) {
							$options[] = $this->form->makeOption($folder, $folder);
						}
					}
				}
			}
		}

		$item_attrs = array('id' => 'params'.$name, 'dir' => 'ltr' );
		if ($item_tip != '') { $item_attrs['tip'] = $item_tip; }
		if (isset($attrs['required']) && (intval($attrs['required']) == 1)) { $item_attrs['required'] = 'required'; }

		return $this->form->addSelect('params['.$name.']', $item_label, $value, $options, $item_attrs);
	}


	/**************************/
	/* MAKE FILES SELECT LIST */
	/**************************/
	private function form_filelist($name, $value, $node, $item_label, $item_tip) {
		$attrs = $node->attributes();
		$noselect = (isset($attrs['noselect'])) ? trim($attrs['noselect']) : '';
		$options = array();
		$options[] = $this->form->makeOption($noselect, '- '.eFactory::getLang()->get('NONE').' -');
		if (isset($attrs['directory'])) {
			$dir = str_replace(DIRECTORY_SEPARATOR, '/', (string)$attrs['directory']);
			$dir = $this->msReplacer($dir);
			$dir = preg_replace('/^(\/)/', '', $dir);
			$dir = preg_replace('/(\/)$/', '', $dir);
			if ($dir != '') {
				$path = ELXIS_PATH.'/'.$dir.'/';
				if (file_exists($path) && is_dir($path)) {
					$regex = '';
					if (isset($attrs['regex'])) {
						$regex = trim($attrs['regex']);
					} else if (isset($attrs['filetype'])) {
						$filetype = trim($attrs['filetype']);
						$filetype = preg_replace('/^(\.)/', '', $filetype);
						if ($filetype != '') { $regex = '(\.'.$filetype.')$'; }
					}
					$files = eFactory::getFiles()->listFiles($dir, $regex);
					if ($files && (count($files) > 0)) {
						$seclevel = eFactory::getElxis()->getConfig('SECURITY_LEVEL');
						foreach ($files as $file) {
							if (strpos($file, '.') === 0) { continue; }
							if ($seclevel > 0) {
								if (strrpos($file, '.php') === 0) { continue; }
							}
							$options[] = $this->form->makeOption($file, $file);
						}
					}
				}
			}
		}

		$item_attrs = array('id' => 'params'.$name, 'dir' => 'ltr');
		if ($item_tip != '') { $item_attrs['tip'] = $item_tip; }
		if (isset($attrs['required']) && (intval($attrs['required']) == 1)) { $item_attrs['required'] = 'required'; }

		return $this->form->addSelect('params['.$name.']', $item_label, $value, $options, $item_attrs);
	}


	/*****************************/
	/* MAKE CATEGORY SELECT LIST */
	/*****************************/
	private function form_category($name, $value, $node, $item_label, $item_tip) {
		$db = eFactory::getDB();

		$attrs = $node->attributes();
		$dir = 'ltr';
		if ($this->dir == 'rtl') {
			if (isset($attrs['dir']) && (strtolower((string)$attrs['dir']) == 'rtl')) { $dir = 'rtl'; }
		}
		$onlyroot = 0;
		if (isset($attrs['onlyroot'])) { $onlyroot = (int)$attrs['onlyroot']; }

        $query= "SELECT ".$db->quoteId('catid').", ".$db->quoteId('parent_id').", ".$db->quoteId('title')." FROM ".$db->quoteId('#__categories')
		."\n WHERE ".$db->quoteId('published')."=1 ORDER BY ".$db->quoteId('parent_id')." ASC, ".$db->quoteId('ordering')." ASC";
		$sth = $db->prepare($query);
		$sth->execute();
		$rows = $sth->fetchAll(PDO::FETCH_ASSOC);
		unset($sth);

		$elids = array();
		$categories = array();
		if ($rows) {
			foreach ($rows as $k => $row) {
				if ($row['parent_id'] == 0) {
					$catid = $row['catid'];
					$elids[] = $catid;
					$categories[$catid] = array(
						'catid' => $catid,
						'title' => $row['title'],
						'children' => array()
					);
					unset($rows[$k]);
				}
			}
			if ($rows && ($onlyroot == 0)) {
				foreach ($rows as $k => $row) {
					$p = $row['parent_id'];
					if (isset($categories[$p])) {
						$c = $row['catid'];
						$elids[] = $c;
						$categories[$p]['children'][$c] = $row['title'];
					}
				}
			}
		}
		unset($rows);

		if ($this->translate && $elids) {
			$query = "SELECT ".$db->quoteId('elid').", ".$db->quoteId('translation')." FROM ".$db->quoteId('#__translations')
			."\n WHERE ".$db->quoteId('category')." = ".$db->quote('com_content')." AND ".$db->quoteId('element')." = ".$db->quote('category_title')
			."\n AND ".$db->quoteId('language')." = :lng AND ".$db->quoteId('elid')." IN (".implode(', ', $elids).")";
			$sth = $db->prepare($query);
			$sth->execute(array(':lng' => $this->uri_lang));
			$trans = $sth->fetchPairs();
			if ($trans) {
				foreach ($categories as $c => $cat) {
					if (isset($trans[$c])) {
						$categories[$c]['title'] = $trans[$c];
					}
					if ($categories[$c]['children']) {
						foreach ($categories[$c]['children'] as $sc => $stitle) {
							if (isset($trans[$sc])) {
								$categories[$c]['children'][$sc] = $trans[$sc];
							}
						}
					}
				}
			}
			unset($trans);
		}
		unset($elids);

		$options = array();
		$options[] = $this->form->makeOption(0, '- '.eFactory::getLang()->get('SELECT').' -');
		if ($categories) {
			foreach ($categories as $category) {
				$options[] = $this->form->makeOption($category['catid'], $category['title']);
				if ($category['children']) {
					foreach ($category['children'] as $sc => $stitle) {
						$options[] = $this->form->makeOption($sc, '--- '.$stitle);
					}
				}
			}
		}

		$item_attrs = array('id' => 'params'.$name, 'dir' => $dir);
		if ($item_tip != '') { $item_attrs['tip'] = $item_tip; }

		return $this->form->addSelect('params['.$name.']', $item_label, $value, $options, $item_attrs);
	}


	/****************************/
	/* MAKE COUNTRY SELECT LIST */
	/****************************/
	private function form_country($name, $value, $node, $item_label, $item_tip) {
		$item_attrs = array('id' => 'params'.$name, 'dir' => $this->dir);
		if ($item_tip != '') { $item_attrs['tip'] = $item_tip; }

		return $this->form->addCountry('params['.$name.']', $item_label, $value, $item_attrs, '');
	}


	/*****************************/
	/* MAKE LANGUAGE SELECT LIST */
	/*****************************/
	private function form_language($name, $value, $node, $item_label, $item_tip) {
		$attrs = $node->attributes();
		$public = isset($attrs['public']) ? (int)$attrs['public'] : 1;
		$ltype = ($public == 1) ? 2 : 1;
		if ($value == '') {
			$value = isset($attrs['default']) ? trim($attrs['default']) : '';
		}
		if ($value == '') { $value = eFactory::getElxis()->getConfig('LANG'); }

		$item_attrs = array('id' => 'params'.$name, 'dir' => $this->dir);
		if ($item_tip != '') { $item_attrs['tip'] = $item_tip; }

		return $this->form->addLanguage('params['.$name.']', $item_label, $value, $item_attrs, $ltype, 4, true, '');
	}

	/**************************************/
	/* MAKE AN INTEGERS RANGE SELECT LIST */
	/**************************************/
	private function form_range($name, $value, $node, $item_label, $item_tip) {
		$value = (int)$value;
		$attrs = $node->attributes();
		$first = isset($attrs['first']) ? (int)$attrs['first'] : 1;
		$last = isset($attrs['last']) ? (int)$attrs['last'] : 1;
		$step = isset($attrs['step']) ? (int)$attrs['step'] : 1;
		if ($step < 1) { $step = 1; }
		if ($first == $last) { $last++; }

		$item_attrs = array('id' => 'params'.$name, 'dir' => 'ltr');
		if ($item_tip != '') { $item_attrs['tip'] = $item_tip; }

		return $this->form->addRange('params['.$name.']', $item_label, $first, $last, $value, $step, $item_attrs);
	}


	/****************************/
	/* MAKE A MONTH SELECT LIST */
	/****************************/
	private function form_month($name, $value, $node, $item_label, $item_tip) {
		$value = (int)$value;
		if ($value < 1) { $value = (int)date('m'); }
		$attrs = $node->attributes();
		$short = (isset($attrs['short']) && (intval($attrs['short']) == 1)) ? true : false;

		$item_attrs = array('id' => 'params'.$name, 'dir' => $this->dir);
		if ($item_tip != '') { $item_attrs['tip'] = $item_tip; }

		return $this->form->addMonth('params['.$name.']', $item_label, $value, $short, $item_attrs);
	}


	/*********************************/
	/* MAKE A USER GROUP SELECT LIST */
	/*********************************/
	private function form_usergroup($name, $value, $node, $item_label, $item_tip) {
		$value = (int)$value;
		if ($value < 0) { $value = 0; }

		$item_attrs = array('id' => 'params'.$name, 'dir' => $this->dir);
		if ($item_tip != '') { $item_attrs['tip'] = $item_tip; }
		$item_attrs['showgid'] = 0;
		$item_attrs['showlevel'] = 1;
		$item_attrs['showgroupname'] = 1;
		$item_attrs['showalloption'] = 0;

		return $this->form->addUsergroup('params['.$name.']', $item_label, $value, 0, 100, $item_attrs);
	}


	/*********************************/
	/* MAKE A USER NAME SELECT LIST */
	/*********************************/
	private function form_username($name, $value, $node, $item_label, $item_tip) {
		$value = (int)$value;
		if ($value < 0) { $value = 0; }

		$db = eFactory::getDB();
		$attrs = $node->attributes();
		$realname = isset($attrs['realname']) ? (int)$attrs['realname'] : 0;

		$sql = "SELECT ".$db->quoteId('uid').", ".$db->quoteId('firstname').", ".$db->quoteId('lastname').", ".$db->quoteId('uname')." FROM ".$db->quoteId('#__users')
		."\n WHERE ".$db->quoteId('block')."=0 AND ".$db->quoteId('expiredate')." > '".eFactory::getDate()->getDate()."'";
		if ($realname == 1) {
			$sql .= "\n ORDER BY ".$db->quoteId('firstname')." ASC";
		} else {
			$sql .= "\n ORDER BY ".$db->quoteId('uname')." ASC";
		}
		$stmt = $db->prepareLimit($sql, 0, 200);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$options = array();
		$options[] = $this->form->makeOption(0, '- '.eFactory::getLang()->get('SELECT').' -');
		if ($rows) {
			foreach ($rows as $row) {
				$txt = ($realname == 1) ? $row['firstname'].' '.$row['lastname'] : $row['uname'];
				$options[] = $this->form->makeOption($row['uid'], $txt);
			}
		}

		$dir = ($realname == 1) ? $this->dir : 'ltr';
		$item_attrs = array('id' => 'params'.$name, 'dir' => $dir);
		if ($item_tip != '') { $item_attrs['tip'] = $item_tip; }

		return $this->form->addSelect('params['.$name.']', $item_label, $value, $options, $item_attrs);
	}


	/****************************************/
	/* MAKE A TEMPLATE POSITION SELECT LIST */
	/****************************************/
	private function form_position($name, $value, $node, $item_label, $item_tip) {
		$eLang = eFactory::getLang();
		$db = eFactory::getDB();

		$value = trim($value);
		$sql = "SELECT ".$db->quoteId('position')." FROM ".$db->quoteId('#__template_positions')." ORDER BY ".$db->quoteId('id')." ASC";
		$stmt = $db->prepare($sql);
		$stmt->execute();
		$rows = $stmt->fetchCol();

		$options = array();
		$attrs = $node->attributes();
		if (isset($attrs['global']) && (intval($attrs['global']) == 1)) {
			$options[] = $this->form->makeOption('_global_', $eLang->silentGet('GLOBAL_SETTING'));
		}
		if (isset($attrs['none']) && (intval($attrs['none']) == 1)) {
			$options[] = $this->form->makeOption('', $eLang->silentGet('NONE'));
		}
		if ($rows) {
			foreach ($rows as $position) {
				$options[] = $this->form->makeOption($position, $position);
			}
		}

		$item_attrs = array('id' => 'params'.$name, 'dir' => 'ltr');
		if ($item_tip != '') { $item_attrs['tip'] = $item_tip; }
		return $this->form->addSelect('params['.$name.']', $item_label, $value, $options, $item_attrs);
	}


	/************************************/
	/* MAKE MENU COLLECTION SELECT LIST */
	/************************************/
	private function form_collection($name, $value, $node, $item_label, $item_tip) {
		$db = eFactory::getDB();

		$section = 'frontend';
		$modname = 'mod_menu';
		$all_collections = array();

		$sql = "SELECT ".$db->quoteId('params')." FROM ".$db->quoteId('#__modules')
		."\n WHERE ".$db->quoteId('module')." = :xmodname AND ".$db->quoteId('section')." = :xsection";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':xmodname', $modname, PDO::PARAM_STR);
		$stmt->bindParam(':xsection', $section, PDO::PARAM_STR);
		$stmt->execute();
		$modparams = $stmt->fetchCol();
		if ($modparams) {
			elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
			foreach ($modparams as $modparam) {
				$params = new elxisParameters($modparam, '', 'module');
				$collection = trim($params->get('collection', ''));
				if (($collection != '') && !in_array($collection, $all_collections)) {
					$all_collections[] = $collection;
				}
				unset($params);
			}
		}

		$sql = "SELECT ".$db->quoteId('collection')." FROM ".$db->quoteId('#__menu')
		."\n WHERE ".$db->quoteId('section')." = :xsection GROUP BY ".$db->quoteId('collection');
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':xsection', $section, PDO::PARAM_STR);
		$stmt->execute();
		$collections = $stmt->fetchCol();
		if ($collections) {
			foreach ($collections as $collection) {
				if (($collection != '') && !in_array($collection, $all_collections)) {
					$all_collections[] = $collection;
				}
			}
		}

		$options = array();
		if ($all_collections) {
			asort($all_collections);
			foreach ($all_collections as $col) {
				$options[] = $this->form->makeOption($col, $col);
			}
		}

		$item_attrs = array('id' => 'params'.$name, 'dir' => 'ltr');
		if ($item_tip != '') { $item_attrs['tip'] = $item_tip; }
		return $this->form->addSelect('params['.$name.']', $item_label, $value, $options, $item_attrs);
	}


	/*************************************/
	/* MAKE DATABASE DYNAMIC SELECT LIST */
	/*************************************/
	private function form_database($name, $value, $node, $item_label, $item_tip) {
		$db = eFactory::getDB();
		$eLang = eFactory::getLang();

		$attrs = $node->attributes();
		$dir = isset($attrs['dir']) ? trim($attrs['dir']) : 'ltr';
		if ($this->dir != 'rtl') { $dir = 'ltr'; }
		$default = isset($attrs['default']) ? trim($attrs['default']) : '';
		$showselect = isset($attrs['showselect']) ? (int)$attrs['showselect'] : 1;
		$table = isset($attrs['table']) ? trim($attrs['table']) : '';
		$table = str_replace('#__', '', $table);
		$colvalue = isset($attrs['colvalue']) ? trim($attrs['colvalue']) : '';
		$colname = isset($attrs['colname']) ? trim($attrs['colname']) : '';
		$where = isset($attrs['where']) ? trim($attrs['where']) : '';

		$replacements = isset($attrs['replacements']) ? (int)$attrs['replacements'] : 1;
		if (($replacements == 1) && ($where != '')) {
			//Quote Id {column_name} --> `column_name`
			$regex = "#\{.*?\}#s";
			preg_match_all($regex, $where, $matches);
			if ($matches[0]) {
				foreach ($matches[0] as $match) {
					$x = str_replace('{', '', $match);
					$x = str_replace('}', '', $x);
					$quoted = $db->quoteId($x);
					$where = str_replace($match, $quoted, $where);
				}
			}

			//Quote [string value] --> 'string value'
			$regex = "#\[.*?\]#s";
			preg_match_all($regex, $where, $matches);
			if ($matches[0]) {
				foreach ($matches[0] as $match) {
					$x = str_replace('[', '', $match);
					$x = str_replace(']', '', $x);
					$quoted = $db->quote($x);
					$where = str_replace($match, $quoted, $where);
				}
			}

			//Less than LESSTHAN    -->    <
			$where = str_replace('LESSTHAN', '<', $where);
			//Greater than GREATERTHAN  -->   >
			$where = str_replace('GREATERTHAN', '>', $where);
		}

		$groupbycol = isset($attrs['groupbycol']) ? trim($attrs['groupbycol']) : '';
		$orderby = isset($attrs['orderby']) ? trim($attrs['orderby']) : '';
		$limit = isset($attrs['limit']) ? (int)$attrs['limit'] : 0;

		$seloptions = array();
		if ($showselect == 1) {
			$seloptions[] = array($default, '- '.$eLang->get('NONE').'/'.$eLang->get('SELECT').' -');
		}

		if (($table != '') && ($colvalue != '') && ($colname != '')) {
			$sql = "SELECT ".$db->quoteId($colvalue);
			$namecolumns = array();
			$parts = explode(',',$colname);
			if ($parts && (count($parts) > 1)) {
				foreach ($parts as $part) { $namecolumns[] = trim($part); }
			} else {
				$namecolumns[] = $colname;
			}
			unset($parts);
			foreach ($namecolumns as $namecolumn) { $sql .= ', '.$db->quoteId($namecolumn); }
			$sql .= " FROM ".$db->quoteId('#__'.$table);
			if ($where != '') {$sql .= ' WHERE '.$where; }
			if ($groupbycol != '') {$sql .= ' GROUP BY '.$db->quoteId($groupbycol); }
			if ($orderby != '') {$sql .= ' ORDER BY '.$orderby; }
			if ($limit > 0) {
				$stmt = $db->prepareLimit($sql, 0, $limit);
			} else {
				$stmt = $db->prepare($sql);
			}
			$stmt->execute();
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

			if ($rows) {
				foreach ($rows as $row) {
					$optval = '';
					$opttexts = array();
					foreach ($row as $k => $v) {
						if ($k == $colvalue) { $optval = $v; }
						if (in_array($k, $namecolumns)) { $opttexts[] = $v; } //same column can be in keys and names
					}
					$opttext = implode(', ', $opttexts);
					$seloptions[] = array($optval, $opttext);
				}
			}
			unset($rows, $namecolumns);
		}

		$options = array();
		if ($seloptions) {
			foreach ($seloptions as $seloption) {
				$options[] = $this->form->makeOption($seloption[0], $seloption[1]);
			}
		}

		$item_attrs = array('id' => 'params'.$name, 'dir' => $dir);
		if ($item_tip != '') { $item_attrs['tip'] = $item_tip; }
		return $this->form->addSelect('params['.$name.']', $item_label, $value, $options, $item_attrs);
	}


	/*********************/
	/* MAKE HIDDEN FIELD */
	/*********************/
	private function form_hidden($name, $value, $node, $item_label, $item_tip) {
		$attrs = $node->attributes();
		$final = $value;
		if (isset($attrs['autovalue'])) {
			switch($attrs['autovalue']) {
				case '{UID}': $final = eFactory::getElxis()->user()->uid; break;
				case '{GID}': $final = eFactory::getElxis()->user()->gid; break;
				case '{DATETIME}': $final = eFactory::getDate()->getDate(); break;
				case '{TIMESTAMP}': $final = eFactory::getDate()->getTS(); break;
				case '{LANGUAGE}': $final = $this->lang; break;
				default: $final = $value; break;
			}
		}
		
		$dir = 'ltr';
		if ($this->dir == 'rtl') {
			if (isset($attrs['dir']) && (strtolower((string)$attrs['dir']) == 'rtl')) { $dir = 'rtl'; }
		}
		$item_attrs = array('id' => 'params'.$name, 'dir' => $dir);
		return $this->form->addHidden('params['.$name.']', $final, $item_attrs);
	}


	/****************/
	/* MAKE COMMENT */
	/****************/
	private function form_comment($name, $value, $node, $item_label, $item_tip) {
		$text = '';
		$attrs = $node->attributes();
		$val = isset($attrs['default']) ? (string)$attrs['default'] : '';
		if ($val != '') {
			$text = eFactory::getLang()->silentGet($val);
		} else {
			$text = (string)$node[0];
			if ($text != '') {
				$text = eFactory::getLang()->silentGet($text);
			}
		}

		return $this->form->addNote($text);
	}


	/*********************************/
	/* MAKE COLOUR SELECT TEXT FIELD */
	/*********************************/
	private function form_color($name, $value, $node, $item_label, $item_tip) {
		$attrs = $node->attributes();

		$item_attrs = array('id' => 'params'.$name, 'dir' => 'ltr');
		if ($item_tip != '') { $item_attrs['tip'] = $item_tip; }
		if (isset($attrs['required']) && (intval($attrs['required']) == 1)) { $item_attrs['required'] = 'required'; }

		if ($value != '') {
			if (strpos($value, '#') === false) { $value = '#'.$value; }
		}

		return $this->form->addColor('params['.$name.']', $value, $item_label, $item_attrs);
	}


	/*************************/
	/* MAKE RADIO BOX FIELDS */
	/*************************/
	private function form_radio($name, $value, $node, $item_label, $item_tip) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$options = array();
		$children = $node->children();

		$ashow = array();
		$ahide = array();
		$initvals = array();
		if ($children) {
			$index = 0;
			foreach ($children as $child) {
				$attr2 = $child->attributes();
				$val = isset($attr2['value']) ? (string)$attr2['value'] : '';
				$show = isset($attr2['show']) ? (string)$attr2['show'] : '';
				$show = trim($show);
				$hide = isset($attr2['hide']) ? (string)$attr2['hide'] : '';
				$hide = trim($hide);
				if ($show != '') {
					$ashow[] = $index.':'.$show;
					if ($val == $value) {
						$grids = explode(',',$show);
						foreach ($grids as $grid) {
							$grid = (int)$grid;
							if ($grid > 999) { $this->groupsVisibility[$grid] = 1; }
						}
					}
				}

				if ($hide != '') {
					$ahide[] = $index.':'.$hide;
					if ($val == $value) {
						$grids = explode(',',$hide);
						foreach ($grids as $grid) {
							$grid = (int)$grid;
							if ($grid > 999) { $this->groupsVisibility[$grid] = 0; }
						}
					}
				}

				$text = (string)$child[0];
				if (($text != '') && !is_numeric($text)) {
					$initvals[] = array($val, $text);
					$text = $eLang->silentGet($text);
				} else {
					$initvals[] = array($val, $text);
				}
				$options[] = $this->form->makeOption($val, $text);
				$index++;
			}
		}

		$is_yesno = false;
		$colors = false;
		if (count($initvals) == 2) {
			if (($initvals[0][0] == '0') && ($initvals[0][1] == 'NO') && ($initvals[1][0] == '1') && ($initvals[1][1] == 'YES')) {
				$is_yesno = true;
			} else if (($initvals[0][0] == '1') && ($initvals[0][1] == 'YES') && ($initvals[1][0] == '0') && ($initvals[1][1] == 'NO')) {
				$is_yesno = true;
			} else if (($initvals[0][1] == 'YES') && ($initvals[1][1] == 'NO')) {
				$colors = array('green', 'red', 'lightblue', 'blue', 'gray', 'yellow', 'orange');
			} else if (($initvals[0][1] == 'NO') && ($initvals[1][1] == 'YES')) {
				$colors = array('red', 'green', 'lightblue', 'blue', 'gray', 'yellow', 'orange');
			} else if (($initvals[0][1] == 'SHOW') && ($initvals[1][1] == 'HIDE')) {
				$colors = array('green', 'red', 'lightblue', 'blue', 'gray', 'yellow', 'orange');
			} else if (($initvals[0][1] == 'HIDE') && ($initvals[1][1] == 'SHOW')) {
				$colors = array('red', 'green', 'lightblue', 'blue', 'gray', 'yellow', 'orange');
			}
		}

		$attribs = '';
		if (count($ashow) > 0) {
			$is_yesno = false;
			$attribs .= 'elx5ShowParams(this, \''.implode(';', $ashow).'\', 2);';
		}
		if (count($ahide) > 0) {
			$is_yesno = false;
			if ($attribs != '') { $attribs .= ' '; }
			$attribs .= 'elx5HideParams(this, \''.implode(';', $ahide).'\', 2);';
		}

		$item_attrs = array('id' => 'params'.$name);
		if ($item_tip != '') { $item_attrs['tip'] = $item_tip; }
		if ($attribs != '') { $item_attrs['onclick'] = $attribs; }

		if ($is_yesno) {
			return $this->form->addYesNo('params['.$name.']', $item_label, $value, $item_attrs);
		}

		if (count($initvals) < 4) {
			if ($attribs == '') {
				$status_options = array();
				if ($colors === false) {
					$colors = array('lightblue', 'blue', 'gray', 'green', 'yellow', 'orange', 'red');
				}
				foreach ($options as $k => $option) {
					$status_options[] = array('name' => $option['label'], 'value' => $option['value'], 'color' => $colors[$k]);
				}
				return $this->form->addItemStatus('params['.$name.']', $item_label, $value, $status_options, $item_attrs);
			}
		}

		return $this->form->addRadio('params['.$name.']', $item_label, $value, $options, $item_attrs);
	}


	/***************************/
	/* MAKE IMAGES SELECT LIST */
	/***************************/
	private function form_imagelist($name, $value, $node, $item_label, $item_tip) {
		$elxis = eFactory::getElxis();
		$attrs = $node->attributes();
		$noselect = (isset($attrs['noselect'])) ? trim($attrs['noselect']) : '';

		$preview = false;
		if (isset($attrs['position'])) { $preview = true; } //"position", "width" and "height" are deprecated Elxis 4.x attributes
		if (isset($attrs['preview'])) {//Elxis 5.x attribute
			if (intval($attrs['preview']) == 1) { $preview = true; }
		}

		$images_dirurl = '/';
		$options = array();
		$options[] = $this->form->makeOption($noselect, '- '.eFactory::getLang()->get('NONE').' -');
		if (isset($attrs['directory'])) {
			$dir = str_replace(DIRECTORY_SEPARATOR, '/', (string)$attrs['directory']);
			$dir = $this->msReplacer($dir);
			$dir = preg_replace('/^(\/)/', '', $dir);
			$dir = preg_replace('/(\/)$/', '', $dir);
			if ($dir != '') {
				$path = ELXIS_PATH.'/'.$dir.'/';
				if (file_exists($path) && is_dir($path)) {
					$images_dirurl = $elxis->secureBase().'/'.$dir.'/';
					$files = eFactory::getFiles()->listFiles($dir, '(\.png)$|(\.gif)$|(\.jpg)$|(\.jpeg)$|(\.bmp)$|(\.ico)$');
					if ($files && (count($files) > 0)) {
						foreach ($files as $file) {
							$options[] = $this->form->makeOption($file, $file);
						}
					}
				}
			}
		}

		if (!$preview) {
			$item_attrs = array('id' => 'params'.$name, 'dir' => 'ltr');
			if ($item_tip != '') { $item_attrs['tip'] = $item_tip; }
			if (isset($attrs['required']) && (intval($attrs['required']) == 1)) { $item_attrs['required'] = 'required'; }

			return $this->form->addSelect('params['.$name.']', $item_label, $value, $options, $item_attrs);
		}

		$empty_image = $elxis->secureBase().'/templates/system/images/nopicture.png';
		$cur_imgurl = $empty_image;
		$cur_imgurl = ($value == '') ? $empty_image : $images_dirurl.$value;

		$html = '<div class="'.$this->form->getOption('rowclass').'">'."\n";
		if ($item_label != '') {
			$html .= '<label class="'.$this->form->getOption('labelclass').'" for="params'.$name.'">'.$item_label."</label>\n";
			$html .= '<div class="'.$this->form->getOption('sideclass').'">';			
		}
		$html .= '<div class="elx5_fileimg_wrap">'."\n";
		$html .= '<a href="'.$cur_imgurl.'" target="_blank" id="params'.$name.'_imagelink">';
		$html .= '<img src="'.$cur_imgurl.'" alt="image" id="params'.$name.'_image" data-empty="'.$empty_image.'" data-dirurl="'.$images_dirurl.'" /></a>'."\n";
		$html .= "</div>\n";//elx5_fileimg_wrap

		$html .= '<div class="elx5_fileimg_inwrap">'."\n";
		$html .= '<select name="params['.$name.']" id="params'.$name.'" class="elx5_select" dir="ltr" onchange="elx5SwitchPreviewImage(\'params'.$name.'\');">'."\n";
		if ($options) {
			foreach ($options as $option) {
				$sel = ($option['value'] == $value) ? ' selected="selected"' : '';
				$html .= '<option value="'.$option['value'].'"'.$sel.'>'.$option['label']."</option>\n";
			}
		}
		$html .= "</select>\n";
		$html .= "</div>\n";//elx5_fileimg_inwrap
		if ($item_tip != '') { $html .= '<div class="'.$this->form->getOption('tipclass').'">'.$item_tip."</div>\n"; }
		if ($item_label != '') {
			$html .= "</div>\n";//sideclass
		}
		$html .= "</div>\n";

		return $html;
	}


	/*****************************************/
	/* MAKE A SELECT LIST WITH IMAGE PREVIEW */
	/*****************************************/
	private function form_previewlist($name, $value, $node, $item_label, $item_tip) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$attrs = $node->attributes();
		$dir = 'ltr';
		if ($this->dir == 'rtl') {
			if (isset($attrs['dir']) && (strtolower((string)$attrs['dir']) == 'rtl')) { $dir = 'rtl'; }
		}

		//"position", "width" and "height" attributes are deprecated in Elxis 5.x
		$initial_image = '';
		$children = $node->children();
		if ($children) {
			foreach ($children as $child) {
				$attr2 = $child->attributes();
				$val = isset($attr2['value']) ? (string)$attr2['value'] : '';
				$image = isset($attr2['image']) ? trim((string)$attr2['image']) : '';
				$image = ltrim($image, '/');
				$dataimage = '';
				if (($image != '') && file_exists(ELXIS_PATH.'/'.$image)) {
					$dataimage = $image;
					//$images[$index] = $image;
					if ($val == $value) { $initial_image = $image; }
				}
				$text = (string)$child[0];
				if (($text != '') && !is_numeric($text)) { $text = $eLang->silentGet($text); }
				$disabled = 0;
				if (isset($attr2['disabled']) && (((string)$attr2['disabled'] == 'disabled') || ((int)$attr2['disabled'] == 1))) { $disabled = 1; }
				$elattrs = array('data-image' => $dataimage);
				$options[] = $this->form->makeOption($val, $text, $elattrs, $disabled);
			}
		}

		$images_dirurl = $elxis->secureBase().'/';
		$empty_image = $elxis->secureBase().'/templates/system/images/nopicture.png';
		$cur_imgurl = $empty_image;
		$cur_imgurl = ($initial_image == '') ? $empty_image : $images_dirurl.$initial_image;

		$html = '<div class="'.$this->form->getOption('rowclass').'">'."\n";
		if ($item_label != '') {
			$html .= '<label class="'.$this->form->getOption('labelclass').'" for="params'.$name.'">'.$item_label."</label>\n";
			$html .= '<div class="'.$this->form->getOption('sideclass').'">';			
		}
		$html .= '<div class="elx5_fileimg_wrap">'."\n";
		$html .= '<a href="'.$cur_imgurl.'" target="_blank" id="params'.$name.'_imagelink">';
		$html .= '<img src="'.$cur_imgurl.'" alt="image" id="params'.$name.'_image" data-empty="'.$empty_image.'" data-dirurl="'.$images_dirurl.'" /></a>'."\n";
		$html .= "</div>\n";//elx5_fileimg_wrap

		$html .= '<div class="elx5_fileimg_inwrap">'."\n";
		$html .= '<select name="params['.$name.']" id="params'.$name.'" class="elx5_select" dir="ltr" onchange="elx5SwitchPreviewImage(\'params'.$name.'\');">'."\n";
		if ($options) {
			foreach ($options as $option) {
				if ($option['value'] == $value) {
					$sel = ' selected="selected"';
				} else if ($option['value'] == $initial_image) {//backwards compatibility
					$sel = ' selected="selected"';
				} else {
					$sel = '';
				}
				$dis = ($option['disabled'] == 1) ? ' disabled="disabled"' : '';
				$html .= '<option value="'.$option['value'].'" data-image="'.$option['attributes']['data-image'].'"'.$sel.$dis.'>'.$option['label']."</option>\n";
			}
		}
		$html .= "</select>\n";
		$html .= "</div>\n";//elx5_fileimg_inwrap
		if ($item_tip != '') { $html .= '<div class="'.$this->form->getOption('tipclass').'">'.$item_tip."</div>\n"; }
		if ($item_label != '') {
			$html .= "</div>\n";//sideclass
		}
		$html .= "</div>\n";

		return $html;
	}


	/***********************/
	/* MAKE TEXTAREA FIELD */
	/***********************/
	private function form_textarea($name, $value, $node, $item_label, $item_tip) {
		$attrs = $node->attributes();

		$value = eUTF::str_replace('<br />', "\n", $value);
		$multilingual = 0;
		if ($this->multilinguism == 1) {
			$multilingual = isset($attrs['multilingual']) ? (int)$attrs['multilingual'] : 0;
		}

		$dir = 'ltr';
		if (isset($attrs['dir']) && (strtolower((string)$attrs['dir']) == 'rtl')) {
			if ($this->dir == 'rtl') { $dir = 'rtl'; }
		}

		$item_attrs = array('id' => 'params'.$name, 'dir' => $dir);
		if ($item_tip != '') { $item_attrs['tip'] = $item_tip; }
		if (isset($attrs['required']) && (intval($attrs['required']) == 1)) { $item_attrs['required'] = 'required'; }
		if (isset($attrs['rows']) && (intval($attrs['rows']) > 0)) { $item_attrs['rows'] = (int)$attrs['rows']; }
		if (isset($attrs['cols']) && (intval($attrs['cols']) > 0)) { $item_attrs['cols'] = (int)$attrs['cols']; }
		if (isset($attrs['placeholder']) && ($attrs['placeholder'] != '')) {
			$item_attrs['placeholder'] = $attrs['placeholder'];
		} else {
			$label = isset($attrs['label']) ? (string)$attrs['label'] : '';
			if (trim($label) != '') { $item_attrs['placeholder'] = eFactory::getLang()->silentGet($label); }
			unset($label);
		}

		if ($multilingual == 1) {
			$item_attrs['translations'] = array();
			if ($this->params) {
				$parr = get_object_vars($this->params);
				if ($parr) {
					foreach ($parr as $k => $v) {
						if (strpos($k, $name.'_ml') === 0) {
							$lng = str_replace($name.'_ml', '', $k);
							if (($lng == '') || ($lng == $this->deflang)) { continue; }
							$item_attrs['translations'][$lng] = $v;
						}
					}
				}
			}

			$trdata = array();//not used for XML parameters
			$html = $this->form->addMLTextarea('params['.$name.']', $trdata, $value, $item_label, $item_attrs);
		} else {
			$html = $this->form->addTextarea('params['.$name.']', $value, $item_label, $item_attrs);
		}
		return $html;
	}


	/*****************/
	/* MAKE DATETIME */
	/*****************/
	private function form_datetime($name, $value, $node, $item_label, $item_tip) {
		return $this->makedatetime($name, $value, $node, 'datetime', $item_label, $item_tip);
	}


	/*************/
	/* MAKE DATE */
	/*************/
	private function form_date($name, $value, $node, $item_label, $item_tip) {
		return $this->makedatetime($name, $value, $node, 'date', $item_label, $item_tip);
	}


	/*************/
	/* MAKE TIME */
	/*************/
	private function form_time($name, $value, $node, $item_label, $item_tip) {
		return $this->makedatetime($name, $value, $node, 'time', $item_label, $item_tip);
	}


	/******************************/
	/* MAKE DATETIME SELECT BOXES */
	/******************************/
	private function makedatetime($name, $value, $node, $type, $item_label, $item_tip) {
		$eDate = eFactory::getDate();

		$value = trim($value);
		$y = gmdate('Y');
		$m = gmdate('m');
		$d = gmdate('d');
		$h = gmdate('H');
		$min = 0;
		$hvalue = $value;
		$hformat = 'Y-m-d H:i:s';

		if ($type == 'time') {
			if (($value != '') && (strlen($value) == 5)) {
				$parts = explode(':', $value);
				if (count($parts) == 2) {
					$h2 = (int)$parts[0];
					$min2 = (int)$parts[1];
					if (($h2 >= 0) && ($h2 < 24) && ($min2 >= 0) && ($min2 < 60)) { $h = $h2; $min = $min2; }
				}
				unset($parts);
			}
			$hvalue = sprintf('%02d', $h).':'.sprintf('%02d', $min);
			$hformat = 'H:i';
		} else if ($type == 'date') {
			if (($value != '') && (strlen($value) == 10)) {
				$parts = explode('-', $value);
				if (count($parts) == 3) {
					$y2 = (int)$parts[0];
					$m2 = (int)$parts[1];
					$d2 = (int)$parts[2];
					if (($y2 > 1999) && ($m2 > 0) && ($m2 < 13) && ($d2 > 0) && ($d2 < 32) && (checkdate($m2, $d2, $y2) === true)) {
						$y = $y2; $m = $m2; $d = $d2;
					}
				}
				unset($parts);
			}
			$hvalue = $y.'-'.sprintf('%02d', $m).'-'.sprintf('%02d', $d);
			$hformat = 'Y-m-d';
		} else {//datetime
			if (($value != '') && (strlen($value) == 19)) {
				$parts = preg_split('/\s/', $value);
				if (count($parts) == 2) {
					$dparts = explode('-', $parts[0]);
					if (count($dparts) == 3) {
						$y2 = (int)$dparts[0];
						$m2 = (int)$dparts[1];
						$d2 = (int)$dparts[2];
						if (($y2 > 1999) && ($m2 > 0) && ($m2 < 13) && ($d2 > 0) && ($d2 < 32) && (checkdate($m2, $d2, $y2) === true)) {
							$y = $y2; $m = $m2; $d = $d2;
						}
					}
					unset($dparts);
					$tparts = explode(':', $parts[1]);
					if (count($tparts) >= 2) {
						$h2 = (int)$tparts[0];
						$min2 = (int)$tparts[1];
						if (($h2 >= 0) && ($h2 < 24) && ($min2 >= 0) && ($min2 < 60)) { $h = $h2; $min = $min2; }
					}
					unset($tparts);
				}
				unset($parts);
			}
			$hvalue = $y.'-'.sprintf('%02d', $m).'-'.sprintf('%02d', $d).' '.sprintf('%02d', $h).':'.sprintf('%02d', $min).':00';
			$hformat = 'Y-m-d H:i:s';
		}

		$item_attrs = array('id' => 'params'.$name, 'dir' => 'ltr', 'format' => $hformat);
		if ($item_tip != '') { $item_attrs['tip'] = $item_tip; }
		if (isset($attrs['required']) && (intval($attrs['required']) == 1)) { $item_attrs['required'] = 'required'; }
		if (isset($attrs['placeholder']) && ($attrs['placeholder'] != '')) { $item_attrs['placeholder'] = $attrs['placeholder']; }

		if ($type == 'date') {
			$html = $this->form->addDate('params['.$name.'].', $hvalue, $item_label, $item_attrs);
		} else if ($type == 'datetime') {
			$html = $this->form->addDatetime('params['.$name.'].', $hvalue, $item_label, $item_attrs);
		} else {//time
			$html = $this->form->addTime('params['.$name.'].', $hvalue, $item_label, $item_attrs);
		}

		return $html;
	}


	/***************************/
	/* MAKE AN UPLOAD FILE BOX */
	/***************************/
	private function form_file($name, $value, $node, $item_label, $item_tip) {
		$this->uploadFields[] = $name;

		$attrs = $node->attributes();

		$empty_image = eFactory::getElxis()->secureBase().'/templates/system/images/nopicture.png';

		if ((trim($value) != '') && is_file(ELXIS_PATH.'/'.$value)) {
			$filesize = round((filesize(ELXIS_PATH.'/'.$value) / 1024), 2);
			$link = eFactory::getElxis()->secureBase().'/'.$value;
			$parts = preg_split('#\/#', $value, -1, PREG_SPLIT_NO_EMPTY);
			$i = count($parts) -1;
			$infotext = $parts[$i];
			$extension = strtolower(substr(strrchr($parts[$i], '.'), 1));
			unset($parts, $i);
			$viewtxt = '<a href="'.$link.'" target="_blank" title="'.eFactory::getLang()->get('VIEW').'">';
			if (in_array($extension, array('png', 'jpg', 'jpeg', 'gif'))) {
				$info = getimagesize(ELXIS_PATH.'/'.$value);
				$infotext .= ' ('.$info[0].'x'.$info[1].', '.$filesize.' KB)';
				$viewtxt .= '<img src="'.$link.'" alt="preview" />';
			} else {
				$infotext .= ' ('.$filesize.' KB)';
				$viewtxt .= '<img src="'.$empty_image.'" alt="preview" />';
			}
			$viewtxt .= '</a>';
		} else {
			$infotext = eFactory::getLang()->get('NO_FILE_UPLOADED');
			$viewtxt = '<img src="'.$empty_image.'" alt="preview" />';
		}

		$html = '<div class="'.$this->form->getOption('rowclass').'">'."\n";
		if ($item_label != '') {
			$html .= '<label class="'.$this->form->getOption('labelclass').'" for="params'.$name.'">'.$item_label."</label>\n";
			$html .= '<div class="'.$this->form->getOption('sideclass').'">';			
		}
		$html .= '<div class="elx5_fileimg_wrap">'."\n";
		$html .= $viewtxt."\n";
		$html .= "</div>\n";//elx5_fileimg_wrap

		$html .= '<div class="elx5_fileimg_inwrap">'."\n";
		$html .= '<div class="elx5_tip elx5_vsspace">'.$infotext."</div>\n";

		if (defined('ELXIS_ADMIN') && (eFactory::getElxis()->getConfig('SECURITY_LEVEL') == 0)) {
			$pholder_str = ($item_label != '') ? ' placeholder="'.$item_label.'"' : '';
			$accept_str = '';
			if (isset($attrs['filetype']) && (trim($attrs['filetype']) != '')) {
				$parts = explode(',', $attrs['filetype']);
				$accept = array();
				foreach ($parts as $p) {
					if (strpos($p, '.') !== 0) {
						$accept[] = '.'.trim($p);
					} else {
						$accept[] = trim($p); 
					}
				}
				if ($accept) { $accept_str = ' accept="'.implode(',', $accept).'"'; }
			}
			$html .= '<input type="file" name="'.$name.'" id="params'.$name.'_file" value="" class="elx5_text" dir="ltr"'.$pholder_str.$accept_str." />\n";
		} else {
			$html .= '<div class="elx5_smwarning">'.eFactory::getLang()->get('NOTALLOWACTION')."</div>\n";
		}
		$html .= '<input type="hidden" name="params['.$name.']" id="params'.$name.'" value="'.$value.'" dir="ltr" />'."\n";
		$html .= "</div>\n";//elx5_fileimg_inwrap
		if ($item_tip != '') { $html .= '<div class="'.$this->form->getOption('tipclass').'">'.$item_tip."</div>\n"; }

		if ($item_label != '') { $html .= "</div>\n"; }
		$html .= "</div>\n";

		return $html;
		echo $html;
	}


	/*****************************************/
	/* MAKE ITEM STATUS SWITCHER (ELXIS 5.X) */
	/*****************************************/
	private function form_itemstatus($name, $value, $node, $item_label, $item_tip) {
		$eLang = eFactory::getLang();

		$value = (int)$value;
		$status_options = array();
		$children = $node->children();
		if ($children) {
			foreach ($children as $child) {
				$attr2 = $child->attributes();
				$optvalue = isset($attr2['value']) ? (int)$attr2['value'] : 0;
				$color = isset($attr2['color']) ? (string)$attr2['color'] : 'gray';
				if ($color == '') { $color = 'gray'; }
				$text = (string)$child[0];
				if (($text != '') && !is_numeric($text)) { $text = $eLang->silentGet($text); }
				$status_options[] = array('name' => $text, 'value' => $optvalue, 'color' => $color);
			}
		}

		$item_attrs = array('id' => 'params'.$name);
		if ($item_tip != '') { $item_attrs['tip'] = $item_tip; }
		return $this->form->addItemStatus('params['.$name.']', $item_label, $value, $status_options, $item_attrs);
	}

}

?>