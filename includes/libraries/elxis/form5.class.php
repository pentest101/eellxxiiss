<?php 
/**
* @version		$Id: form5.class.php 2428 2021-11-14 17:52:34Z IOS $
* @package		Elxis
* @subpackage	Form builder
* @copyright	Copyright (c) 2006-2021 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxis5Form {

	private $curlang = 'en';
	private $dir = 'ltr';//text direction based on current language
	private $sitelangs = array();//published languages array
	private $tabidx = 0;
	private $opentab = 0;
	private $php64bit = -1;
	private $options = array(
		'idprefix' => '',//a prefix to add in all automatically generated id attributes (recommended)
		'fieldsetclass' => 'elx5_fieldset',
		'rowclass' => 'elx5_formrow',
		'labelclass' => 'elx5_label', //elx5_label or elx5_labelblock or anything
		'sideclass' => 'elx5_labelside', //contents next to label or elx5_zero or anything
		'tipclass' => 'elx5_tip',
		'tabs_id' => 'elx5_tabs',
		'tabs_class' => 'elx5_tabs',
		'tabs_content_class' => 'elx5_tab_content',
		'tabs_container_class' => 'elx5_tab_container',
		'tabs_open_class' => 'elx5_tab_open',
		'tabs_use_iconclass' => 0,
		'tabs_use_numeric' => 1,
		'date_format' => 'Y-m-d',
		'datetime_format' => 'Y-m-d H:i',
		'time_format' => 'H:i',
		'returnhtml' => false//true: return html, false: echo html
	);


	/***************/
	/* CONSTRUCTOR */
	/***************/
	public function __construct($options=array()) {
		$eLang = eFactory::getLang();

		$this->curlang = $eLang->currentLang();
		$this->dir = $eLang->getinfo('DIR');

		$this->options['date_format'] = $eLang->get('DATE_FORMAT_BOX');
		$this->options['datetime_format'] = preg_replace('@(\:s)$@', '', $eLang->get('DATE_FORMAT_BOX_LONG'));//remove seconds (Y-m-d H:i)

		$this->setOptions($options);
	}


	/***************************/
	/* GET PUBLISHED LANGUAGES */
	/***************************/
	private function getSiteLangs() {
		if (!$this->sitelangs) {
			$this->sitelangs = eFactory::getLang()->getSiteLangs(true);
		}
		return $this->sitelangs;
	}


	/********************/
	/* SET FORM OPTIONS */
	/********************/
	public function setOptions($options=array()) {
		if (!$options) { return; }
		if (!is_array($options)) { return; }
		foreach ($options as $k => $v) { $this->options[$k] = $v; }
	}


	/*******************/
	/* SET FORM OPTION */
	/*******************/
	public function setOption($option, $value) {
		$this->options[$option] = $value;
	}


	/*******************/
	/* GET FORM OPTION */
	/*******************/
	public function getOption($option, $default='') {
		if (($option != '') && isset($this->options[$option])) { return $this->options[$option]; }
		return $default;
	}


	/*************/
	/* OPEN FORM */
	/*************/
	public function openForm($attrs=array()) {
		$attributes = array(
			'name' => 'elxisform',
			'enctype' => 'application/x-www-form-urlencoded',//or multipart/form-data
			'class' => 'elx5_form',
			'action' => 'index.php',
			'method' => 'post'
		);

		$this->combinedStarted = false;
		$this->combined = array();

		if ($attrs) {
			foreach ($attrs as $k => $v) { $attributes[$k] = $v; }
		}

		$html = '<form';
		foreach ($attributes as $k => $v) {
			if ($v == '') { continue; }
			$html .= ' '.$k.'="'.$v.'"';
		}
		$html .= '>'."\n";

		if ($this->options['returnhtml']) { return $html; }
		echo $html;
	}


	/**************/
	/* CLOSE FORM */
	/**************/
	public function closeForm() {
		$html = "</form>\n";
		if ($this->options['returnhtml']) { return $html; }
		echo $html;
	}


	/************************/
	/* START A NEW FIELDSET */
	/************************/
	public function openFieldset($legend='', $attrs=array()) {
		$attributes = array('class' => $this->options['fieldsetclass']);
		if ($attrs) {
			foreach ($attrs as $k => $v) { $attributes[$k] = $v; }
		}

		$html = '<fieldset';
		foreach ($attributes as $k => $v) {
			if ($v == '') { continue; }
			$html .= ' '.$k.'="'.$v.'"';
		}
		$html .= '>';
		if ($legend != '') { $html .= '<legend>'.$legend.'</legend>'; }
		$html .= "\n";

		if ($this->options['returnhtml']) { return $html; }
		echo $html;
	}


	/***********************/
	/* CLOSE OPEN FIELDSET */
	/***********************/
	public function closeFieldset() {
		$html = "</fieldset>\n";
		if ($this->options['returnhtml']) { return $html; }
		echo $html;
	}


	/****************/
	/* OPEN TOOLBAR */
	/****************/
	public function openToolbar($attrs=array(), $title='') {
		$attributes = array('class' => 'elx5_toolbar');
		if ($title != '') { $attributes['class'] = 'elx5_toolbar_2cols'; }

		if ($attrs) {
			foreach ($attrs as $k => $v) { $attributes[$k] = $v; }
		}

		$html = '<div';
		foreach ($attributes as $k => $v) {
			if ($v == '') { continue; }
			$html .= ' '.$k.'="'.$v.'"';
		}
		$html .= ">\n";
		if ($title != '') {
			$html .= '<div class="elx5_toolbar_tcol"><h1>'.$title."</h1></div>\n";
			$html .= '<div class="elx5_toolbar_bcol">'."\n";
		}

		if ($this->options['returnhtml']) { return $html; }
		echo $html;
	}


	/*****************/
	/* CLOSE TOOLBAR */
	/*****************/
	public function closeToolbar($has_title=false) {
		$html = '';
		if ($has_title) { $html .= "</div>\n"; }//elx5_toolbar_bcol
		$html .= "</div>\n";
		if ($this->options['returnhtml']) { return $html; }
		echo $html;
	}


	/************/
	/* ADD NOTE */
	/************/
	public function addNote($text, $class='elx5_info', $attrs=array()) {
		if ($class == '') { $class = 'elx5_info'; }
		$attributes = array('class' => $class);
		if ($attrs) {
			foreach ($attrs as $k => $v) { $attributes[$k] = $v; }
		}

		$html = '<div';
		foreach ($attributes as $k => $v) {
			if ($v == '') { continue; }
			$html .= ' '.$k.'="'.$v.'"';
		}
		$html .= '>'.$text."</div>\n";

		if ($this->options['returnhtml']) { return $html; }
		echo $html;
	}


	/********************************************/
	/* ADD INFORMATIONAL LINE (NOTE WITH LABEL) */
	/********************************************/
	public function addInfo($label, $text, $attrs=array()) {
		$attributes = array('class' => 'elx5_formtext');
		if ($attrs) {
			foreach ($attrs as $k => $v) { $attributes[$k] = $v; }
		}

		$html = '<div class="'.$this->options['rowclass'].'">'."\n";
		$html .= '<label class="'.$this->options['labelclass'].'">'.$label."</label>\n";
		$html .= '<div class="'.$this->options['sideclass'].'">';
		if ($attributes) {
			$html .= '<div';
			foreach ($attributes as $k => $v) {
				if ($v == '') { continue; }
				$html .= ' '.$k.'="'.$v.'"';
			}
			$html .= '>'.$text.'</div>';
		} else {
			$html .= $text;
		}
		$html .= "</div>\n";
		$html .= "</div>\n";

		if ($this->options['returnhtml']) { return $html; }
		echo $html;
	}


	/******************/
	/* ADD INPUT ITEM */
	/******************/
	public function addInput($type, $name, $value, $label, $attrs=array()) {
		$attributes = array(
			'id' => $this->options['idprefix'].$name,
			'class' => 'elx5_text',
			'dir' => $this->dir,
			'placeholder' => $label
		);
		$tip = '';
		$password_meter = 0;
		$passmatch = '';
		$sidetext = '';
		$sidetextposition = 2;
		$datalistoptions = array();
		$onlyelement = 0;//Elxis 5.1
		if ($attrs) {
			foreach ($attrs as $k => $v) {
				if ($k == 'tip') { $tip = $v; continue; }
				if ($k == 'password_meter') {
					if ($type == 'password') { $password_meter = (int)$v; }//1: "front" or 2: "back"
					continue;
				}
				if ($k == 'match') {
					if ($type == 'password') { $passmatch = $v; }//$v : first password element id
					continue;
				}
				if ($k == 'sidetext') { $sidetext = $v; continue; }
				if ($k == 'sidetextposition') { $sidetextposition = (int)$v; continue; }
				if ($k == 'datalistoptions') { $datalistoptions = $v; continue; }
				if ($k == 'onlyelement') { $onlyelement = (int)$v; continue; }
				$attributes[$k] = $v;
			}
		}

		if ($password_meter > 0) { $attributes['onkeyup'] = 'elx5PasswordMeter(\''.$attributes['id'].'\');'; }
		if ($passmatch != '') { $attributes['onkeyup'] = 'elx5PasswordMatch(\''.$attributes['id'].'\', \''.$passmatch.'\');'; }

		if (isset($attributes['readonly'])) {
			if (strpos($attributes['class'], 'readonly') === false) { $attributes['class'] .= ' elx5_readonly'; }
		}

		if ($value != '') {
			if (!isset($attributes['title'])) { $attributes['title'] = $label; }
		}
		if ($onlyelement == 1) {
			if (!isset($attributes['title'])) { $attributes['title'] = $label; }
		}

		$html = '';
		if ($onlyelement == 0) {
			$html .= '<div class="'.$this->options['rowclass'].'">'."\n";
			$html .= '<label class="'.$this->options['labelclass'].'" for="'.$attributes['id'].'">'.$label."</label>\n";
			$html .= '<div class="'.$this->options['sideclass'].'">';
		}

		if ($password_meter > 0) {
			$html .= '<div class="elx5_sideinput_wrap">'."\n";
			if ($password_meter == 1) {
				$html .= '<div class="elx5_sideinput_value_front"><meter id="'.$attributes['id'].'_passmeter" class="elx5_meter" value="0" min="0" max="10"></meter></div>'."\n";
				$html .= '<div class="elx5_sideinput_input_end">'."\n";
			} else {
				$html .= '<div class="elx5_sideinput_value_end"><meter id="'.$attributes['id'].'_passmeter" class="elx5_meter" value="0" min="0" max="10"></meter></div>'."\n";
				$html .= '<div class="elx5_sideinput_input_front">'."\n";
			}
		} else if ($sidetext != '') {//for currency and any other side text
			$html .= '<div class="elx5_sideinput_wrap">'."\n";
			if ($sidetextposition == 2) {
				$html .= '<div class="elx5_sideinput_value_end" id="'.$attributes['id'].'_sidetext">'.$sidetext.'</div>'."\n";
				$html .= '<div class="elx5_sideinput_input_front">'."\n";
			} else {
				$html .= '<div class="elx5_sideinput_value_front" id="'.$attributes['id'].'_sidetext">'.$sidetext.'</div>'."\n";
				$html .= '<div class="elx5_sideinput_input_end">'."\n";
			}
		}

		$html .= '<input type="'.$type.'" name="'.$name.'" value="'.$value.'"';
		foreach ($attributes as $k => $v) {
			if ($v == '') { continue; }
			if ($k == 'type') { continue; }
			if ($k == 'name') { continue; }
			if ($k == 'value') { continue; }
			$html .= ' '.$k.'="'.$v.'"';
		}
		$html .= " />\n";

		if (isset($attributes['list'])) {
			if (count($datalistoptions) > 0) {
				$html .= '<datalist id="'.$attributes['list'].'">'."\n";
				foreach ($datalistoptions as $opt) {
					$html .= '<option value="'.$opt['value'].'">'.$opt['label']."</option>\n";
				}
				$html .= "</datalist>\n";
			}
		}

		if (($password_meter > 0) || ($sidetext != '')) { $html .= "</div></div>\n"; }
		if ($tip != '') { $html .= '<div class="'.$this->options['tipclass'].'">'.$tip."</div>\n"; }
		if ($onlyelement == 0) {
			$html .= "</div>\n";
			$html .= "</div>\n";
		}

		if ($this->options['returnhtml']) { return $html; }
		echo $html;
	}


	/**********************/
	/* ADD TEXTAREA FIELD */
	/**********************/
	public function addTextarea($name, $value, $label, $attrs=array()) {
		$attributes = array(
			'id' => $this->options['idprefix'].$name,
			'class' => 'elx5_textarea',
			'dir' => $this->dir,
			'placeholder' => $label
		);

		$editor = '';
		$editoroptions = array();
		$contentslang = '';
		$tip = '';
		$onlyelement = 0;//Elxis 5.1
		if ($attrs) {
			foreach ($attrs as $k => $v) {
				if ($k == 'tip') {
					$tip = $v;
					continue;
				}
				if ($k == 'editor') {
					$editor = trim($v);
					continue;
				}
				if ($k == 'editoroptions') {
					$editoroptions = is_array($v) ? $v : array();
					continue;
				}
				if ($k == 'contentslang') {//TODO: USE IN JODIT?
					$contentslang = (string)$v;
					continue;
				}
				if ($k == 'onlyelement') {
					$onlyelement = (int)$v;
					continue;
				}
				$attributes[$k] = $v;
			}
		}

		$editor_js = '';
		if ($editor == 'html') {
			$tip = ''; //disable tips for rich text editor
			$elxis = eFactory::getElxis();
			$elxeditor = $elxis->obj('editor');
			$elxeditor->prepare($attributes['id'], $editor, $contentslang, $editoroptions);
			$editor_js = $elxeditor->getJS();
			unset($elxeditor);
			$value = htmlspecialchars($value);
		}

		if ($onlyelement == 1) {
			if (!isset($attributes['title'])) { $attributes['title'] = $label; }
		}

		$html = '';
		if ($onlyelement == 0) {
			$html .= '<div class="'.$this->options['rowclass'].'">'."\n";
			$html .= '<label class="'.$this->options['labelclass'].'" for="'.$attributes['id'].'">'.$label."</label>\n";
			$html .= '<div class="'.$this->options['sideclass'].'">';
		}

		$html .= '<textarea name="'.$name.'"';
		foreach ($attributes as $k => $v) {
			if ($v == '') { continue; }
			if ($k == 'name') { continue; }
			if ($k == 'forcedir') { continue; }
			$html .= ' '.$k.'="'.$v.'"';
		}
		$html .= ">".$value."</textarea>\n";
		if ($tip != '') { $html .= '<div class="'.$this->options['tipclass'].'">'.$tip."</div>\n"; }
		if ($onlyelement == 0) {
			$html .= "</div>\n";
			$html .= "</div>\n";
		}
		$html .= $editor_js;

		if ($this->options['returnhtml']) { return $html; }
		echo $html;
	}


	/* SHORTCUTS & ELXIS 4.X COMPATIBILITY */  
	public function addUrl($name, $value='', $label='', $attrs=array()) {
		if (!$attrs) { $attrs = array(); }
		$attrs['dir'] = 'ltr';

		if ($this->options['returnhtml']) {
			return $this->addInput('url', $name, $value, $label, $attrs);
		} else {
			$this->addInput('url', $name, $value, $label, $attrs);
		}
	}


	public function addText($name, $value='', $label='', $attrs=array()) {
		if ($this->options['returnhtml']) {
			return $this->addInput('text', $name, $value, $label, $attrs);
		} else {
			$this->addInput('text', $name, $value, $label, $attrs);
		}
	}


	public function addPassword($name, $value='', $label='', $attrs=array()) {
		if (!$attrs) { $attrs = array(); }
		$attrs['dir'] = 'ltr';

		if ($this->options['returnhtml']) {
			return $this->addInput('password', $name, $value, $label, $attrs);
		} else {
			$this->addInput('password', $name, $value, $label, $attrs);
		}
	}


	public function addEmail($name, $value='', $label='', $attrs=array()) {
		if (!$attrs) { $attrs = array(); }
		$attrs['dir'] = 'ltr';

		if ($this->options['returnhtml']) {
			return $this->addInput('email', $name, $value, $label, $attrs);
		} else {
			$this->addInput('email', $name, $value, $label, $attrs);
		}
	}


	public function addNumber($name, $value='', $label='', $attrs=array()) {
		if (!$attrs) { $attrs = array(); }
		$attrs['dir'] = 'ltr';

		if ($this->options['returnhtml']) {
			return $this->addInput('number', $name, $value, $label, $attrs);
		} else {
			$this->addInput('number', $name, $value, $label, $attrs);
		}
	}


	/***********************/
	/* ADD INPUT TEL FIELD */
	/***********************/
	public function addTel($name, $value='', $label='', $attrs=array()) {
		if (!$attrs) { $attrs = array(); }
		$attrs['dir'] = 'ltr';

		if ($this->options['returnhtml']) {
			return $this->addInput('tel', $name, $value, $label, $attrs);
		} else {
			$this->addInput('tel', $name, $value, $label, $attrs);
		}
	}


	/*************************/
	/* ADD INPUT COLOR FIELD */
	/*************************/
	public function addColor($name, $value='', $label='', $attrs=array()) {
		if (!$attrs) { $attrs = array(); }
		$attrs['dir'] = 'ltr';
		if (!isset($attrs['class'])) { $attrs['class'] = 'elx5_text elx5_minitext'; }

		if ($this->options['returnhtml']) {
			return $this->addInput('color', $name, $value, $label, $attrs);
		} else {
			$this->addInput('color', $name, $value, $label, $attrs);
		}
	}


	/**************************/
	/* ADD INPUT SEARCH FIELD */
	/**************************/
	public function addSearch($name, $value='', $label='', $attrs=array()) {
		if ($this->options['returnhtml']) {
			return $this->addInput('search', $name, $value, $label, $attrs);
		} else {
			$this->addInput('search', $name, $value, $label, $attrs);
		}
	}


	/************************/
	/* ADD INPUT DATE FIELD */
	/************************/
	public function addDateNative($name, $value='', $label='', $attrs=array()) { //HTML5 date
		if ($this->options['returnhtml']) {
			return $this->addInput('date', $name, $value, $label, $attrs);
		} else {
			$this->addInput('date', $name, $value, $label, $attrs);
		}
	}


	/****************************/
	/* ADD INPUT DATETIME FIELD */
	/****************************/
	public function addDatetimeNative($name, $value='', $label='', $attrs=array()) { //HTML5 datetime
		if ($this->options['returnhtml']) {
			return $this->addInput('datetime', $name, $value, $label, $attrs);
		} else {
			$this->addInput('datetime', $name, $value, $label, $attrs);
		}
	}


	/**********************************/
	/* ADD INPUT DATETIME-LOCAL FIELD */
	/**********************************/
	public function addDatetimelocal($name, $value='', $label='', $attrs=array()) {
		if ($this->options['returnhtml']) {
			return $this->addInput('datetime-local', $name, $value, $label, $attrs);
		} else {
			$this->addInput('datetime-local', $name, $value, $label, $attrs);
		}
	}


	/************************/
	/* ADD INPUT TIME FIELD */
	/************************/
	public function addTimeNative($name, $value='', $label='', $attrs=array()) { //HTML5 time
		if ($this->options['returnhtml']) {
			return $this->addInput('time', $name, $value, $label, $attrs);
		} else {
			$this->addInput('time', $name, $value, $label, $attrs);
		}
	}


	/*************************/
	/* ADD INPUT MONTH FIELD */
	/*************************/
	public function addMonthNative($name, $value='', $label='', $attrs=array()) { //HTML5 month
		if ($this->options['returnhtml']) {
			return $this->addInput('month', $name, $value, $label, $attrs);
		} else {
			$this->addInput('month', $name, $value, $label, $attrs);
		}
	}


	/************************/
	/* ADD INPUT LIST FIELD */
	/************************/
	public function addList($name, $value='', $label='', $options=array(), $attrs=array()) {
		if (!$attrs) { $attrs = array(); }
		if ($options) {
			if (!isset($attrs['id'])) { $attrs['id'] = $this->options['idprefix'].$name; }
			if (!isset($attrs['list'])) { $attrs['list'] = $attrs['id'].'data'; }
			$attrs['datalistoptions'] = $options;
		}

		if ($this->options['returnhtml']) {
			return $this->addInput('text', $name, $value, $label, $attrs);
		} else {
			$this->addInput('text', $name, $value, $label, $attrs);
		}
	}


	/******************/
	/* ADD FILE FIELD */
	/******************/
	public function addFile($name, $label='', $attrs=array()) {
		if (!$attrs) { $attrs = array(); }
		$attrs['dir'] = 'ltr';

		if ($this->options['returnhtml']) {
			return $this->addInput('file', $name, '', $label, $attrs);
		} else {
			$this->addInput('file', $name, '', $label, $attrs);
		}
	}


	/***********************************************/
	/* ADD AJAX FILE UPLOAD (Simple-Ajax-Uploader) */
	/***********************************************/
	public function addAjaxFile($name, $attrs=array()) {
		$eLang = eFactory::getLang();

		$attributes = array(
			'id' => $this->options['idprefix'].$name,
			'class' => 'elx5_btn'
		);

		$tip = '';
		$help = '';
		if ($attrs) {
			foreach ($attrs as $k => $v) {
				if ($k == 'tip') { $tip = $v; continue; }
				if ($k == 'help') { $help = $v; continue; }
				$attributes[$k] = $v;
			}
		}

		$html = '<div class="'.$this->options['rowclass'].'">'."\n";
		if ($help != '') {
			$html .= '<div class="elx5_2colwrap">'."\n";
			$html .= '<div class="elx5_2colbox">'."\n";
		}
		$html .= '<button type="button" name="'.$name.'" id="'.$attributes['id'].'" class="'.$attributes['class'].'"';
		foreach ($attributes as $k => $v) {
			if ($v == '') { continue; }
			if ($k == 'type') { continue; }
			if ($k == 'name') { continue; }
			if ($k == 'id') { continue; }
			if ($k == 'class') { continue; }
			$html .= ' '.$k.'="'.$v.'"';
		}
		$html .= ' data-wait="'.$eLang->get('PLEASE_WAIT').'" data-selfile="'.$eLang->get('SELECT_FILE').'">'.$eLang->get('SELECT_FILE')."</button>\n";
		if ($help != '') {
			$html .= "</div>\n";//elx5_2colbox
			$html .= '<div class="elx5_2colbox">'.$help."</div>\n";
			$html .= "</div>\n";//elx5_2colwrap
		}
		if ($tip != '') { $html .= '<div class="'.$this->options['tipclass'].'">'.$tip."</div>\n"; }

		$html .= '<div id="'.$attributes['id'].'outer" class="progress progress-striped active" style="display:none;">'."\n";
		$html .= '<div id="'.$attributes['id'].'bar" class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>'."\n";
		$html .= "</div>\n";//outer
		
		$html .= '<div id="'.$attributes['id'].'msgbox" class="elx5_invisible"></div>'."\n";
		$html .= "</div>\n";//rowclass

		if (!defined('SIMPLE_AJAX_UPLOADER')) {
			$jsfile = eFactory::getElxis()->secureBase(true).'/includes/js/SimpleAjaxUploader.min.js';
			eFactory::getDocument()->addScriptLink($jsfile);
			define('SIMPLE_AJAX_UPLOADER', 1);
		}

		if ($this->options['returnhtml']) { return $html; }
		echo $html;
	}


	/************************/
	/* ADD PRICE TEXT FIELD */
	/************************/
	public function addPrice($name, $value='0.00', $label='', $decimals=2, $currency='EUR', $attrs=array()) {
		$decimals = (int)$decimals;
		if ($decimals < 1) { $decimals = 2; }
		if ($currency == '') { $currency = 'EUR'; }

		$value = filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
		if (!is_numeric($value)) { $value = '0.00'; }
		$value = number_format($value, $decimals, '.', '');

		if (!$attrs) { $attrs = array(); }
		$attrs['dir'] = 'ltr';
		if (!isset($attrs['lang'])) { $attrs['lang'] = 'en'; }
		if (!isset($attrs['step'])) {
			switch ($decimals) {
				case 0: $attrs['step'] = '1'; break;
				case 1: $attrs['step'] = '0.1'; break;
				case 2: $attrs['step'] = '0.01'; break;
				default: $attrs['step'] = 'any'; break;
			} 
		}
		if (!isset($attrs['sidetext'])) { $attrs['sidetext'] = $currency; }
		if (!isset($attrs['sidetextposition'])) { $attrs['sidetextposition'] = 2; }//1:front, 2: end

		if ($this->options['returnhtml']) {
			return $this->addInput('number', $name, $value, $label, $attrs);
		} else {
			$this->addInput('number', $name, $value, $label, $attrs);
		}
	}


	/************************/
	/* ADD HIDDEN INPUT BOX */
	/************************/
	public function addHidden($name, $value='', $attrs=array()) {
		$attributes = array('id' => $this->options['idprefix'].$name, 'dir' => 'ltr');
		if ($attrs) {
			foreach ($attrs as $k => $v) { $attributes[$k] = $v; }
		}

		$html = '<input type="hidden" name="'.$name.'" value="'.$value.'"';
		foreach ($attributes as $k => $v) {
			if ($v == '') { continue; }
			if ($k == 'type') { continue; }
			if ($k == 'name') { continue; }
			if ($k == 'value') { continue; }
			$html .= ' '.$k.'="'.$v.'"';
		}
		$html .= " />\n";

		if ($this->options['returnhtml']) { return $html; }
		echo $html;
	}


	/******************************/
	/* ADD TOKEN HIDDEN INPUT BOX */
	/******************************/
	public function addToken($name='') {
		if (trim($name) == '') { $name = 'token'; }
		$token = md5(uniqid(rand(), true));
		eFactory::getSession()->set('token_'.$name, $token);

		$html = '<input type="hidden" name="token" value="'.$token.'" id="'. $this->options['idprefix'].'token" dir="ltr" />'."\n";

		if ($this->options['returnhtml']) { return $html; }
		echo $html;
	}


	/***************/
	/* MAKE OPTION */
	/***************/
	public function makeOption($value, $label, $attributes=array(), $disabled=0, $optgroup='') {
		$disabled = (int)$disabled;
		$attributes = (is_array($attributes)) ? $attributes : array();
		return array(
			'value' => $value,
			'label' => $label, 
			'attributes' => $attributes,
			'disabled' => $disabled,
			'optgroup' => $optgroup
		);
	}


	/******************************/
	/* ADD DROP DOWN SELECT FIELD */
	/******************************/
	public function addSelect($name, $label='', $selected=null, $options=array(), $attrs=array()) {
		$attributes = array('id' => $this->options['idprefix'].$name, 'class' => 'elx5_select', 'dir' => $this->dir);
		$multiple = 0;
		$tip = '';
		$onlyelement = 0;//Elxis 5.1
		if ($attrs) {
			foreach ($attrs as $k => $v) {
				if ($k == 'multiple') {
					if (($v == 1) || ($v == 'multiple') || ($v === true)) { $multiple = 1; }
					continue;
				}
				if ($k == 'tip') {
					$tip = $v;
					continue;
				}
				if ($k == 'onlyelement') {
					$onlyelement = (int)$v;
					continue;
				}
				$attributes[$k] = $v;
			}
		}

		if ($onlyelement == 1) {
			if (!isset($attributes['title'])) { $attributes['title'] = $label; }
		}

		if (($multiple == 1) && ($attributes['class'] == 'elx5_select')) { $attributes['class'] = 'elx5_select elx5_selectmultiple'; }

		$html = '';
		if ($onlyelement == 0) {
			$html .= '<div class="'.$this->options['rowclass'].'">'."\n";
			$html .= '<label class="'.$this->options['labelclass'].'" for="'.$attributes['id'].'">'.$label."</label>\n";
			$html .= '<div class="'.$this->options['sideclass'].'">';
		}
		if ($multiple == 1) {
			$html .= '<select name="'.$name.'[]" multiple="multiple"';
			if (($selected === null) || ($selected === false)) { $selected = array(); }
		} else {
			$html .= '<select name="'.$name.'"';
		}
		foreach ($attributes as $k => $v) {
			if ($v == '') { continue; }
			if ($k == 'name') { continue; }
			if ($k == 'multiple') { continue; }
			$html .= ' '.$k.'="'.$v.'"';
		}
		$html .= ">\n";

		if (is_array($options)) {
			if (count($options) > 0) {
				$optgroup = '';
				foreach ($options as $option) {
					if ($option['optgroup'] != '') {
						if ($optgroup == '') {
							$html .= '<optgroup label="'.$option['optgroup'].'">'."\n";
						} else {
							if ($option['optgroup'] != $optgroup) {
								$html .= "</optgroup>\n";
								$html .= '<optgroup label="'.$option['optgroup'].'">'."\n";
							}
						}
						$optgroup = $option['optgroup'];
					} else {
						if ($optgroup != '') { $html .= "</optgroup>\n"; }
						$optgroup = '';
					}

					$dis = ($option['disabled'] == 1) ? ' disabled="disabled"' : '';

					if ($multiple == 1) {
						$sel = in_array($option['value'], $selected) ? ' selected="selected"' : '';
					} else {
						$sel = ($option['value'] == $selected) ? ' selected="selected"' : '';
					}

					$attr = '';
					if (is_array($option['attributes']) && (count($option['attributes']) > 0)) {
						foreach ($option['attributes'] as $key => $val) { $attr .= ' '.$key.'="'.$val.'"'; }
					}
					$html .= '<option value="'.$option['value'].'"'.$dis.''.$sel.''.$attr.'>'.$option['label']."</option>\n";
				}

				if ($optgroup != '') { $html .= "</optgroup>\n"; }
			}
		}

		$html .= "</select>\n";
		if ($tip != '') { $html .= '<div class="'.$this->options['tipclass'].'">'.$tip."</div>\n"; }
		if ($onlyelement == 0) {
			$html .= "</div>\n";
			$html .= "</div>\n";
		}

		if ($this->options['returnhtml']) { return $html; }
		echo $html;
	}


	/************************************************************************/
	/* ADD DROP DOWN SELECT FIELD WITH CUSTOM MULTIPLE SELECT FUNCTIONALITY */
	/************************************************************************/
	public function addMultiSelect($name, $label='', $selected=array(), $options=array(), $attrs=array()) {
		$eLang = eFactory::getLang();

		if (!is_array($options)) { $options = array(); }
		if (!is_array($selected)) {
			$v = (string)$selected;
			$selected = array();
			if ($v != '') { $selected = explode(',', $v); }
		}

		$attributes = array(
			'id' => $this->options['idprefix'].$name, 
			'class' => 'elx5_select', 
			'dir' => $this->dir, 
			'onchange' => '', 
			'data-lngremove' => $eLang->get('REMOVE'),
			'flagvalues' => 0,
			'noselected_text' => ''
		);

		$tip = '';
		$onlyelement = 0;//Elxis 5.1
		if ($attrs) {
			foreach ($attrs as $k => $v) {
				if ($k == 'multiple') { continue; }
				if ($k == 'tip') { $tip = $v; continue; }
				if ($k == 'onlyelement') { $onlyelement = (int)$v; continue; }
				$attributes[$k] = $v;
			}
		}
		$attributes['flagvalues'] = (int)$attributes['flagvalues'];

		if ($attributes['onchange'] == '') { $attributes['onchange'] = 'elx5MultiSelectAdd(\''.$attributes['id'].'\', \'addall\', '.$attributes['flagvalues'].');'; }
		if ($attributes['flagvalues'] == 1) { $this->prepareMultiLinguism(); }
		if ($onlyelement == 1) {
			if (!isset($attributes['title'])) { $attributes['title'] = $label; }
		}

		$html = '';
		if ($onlyelement == 0) {
			$html .= '<div class="'.$this->options['rowclass'].'">'."\n";
			$html .= '<label class="'.$this->options['labelclass'].'" for="'.$attributes['id'].'">'.$label."</label>\n";
			$html .= '<div class="'.$this->options['sideclass'].'">';
		}
		$selected_vals = array();
		$html .= '<div class="elx5_msel_items" id="'.$attributes['id'].'_items">'."\n";
		if ($selected) {
			if (count($options) > 0) {
				foreach ($selected as $value) {
					foreach ($options as $option) {
						if ($value == $option['value']) {
							$selected_vals[] = $value;
							if ($attributes['flagvalues'] == 1) {
								$class = 'elx5_msel_item elx5_mlflag'.$option['value'];
							} else {
								$class = 'elx5_msel_item';
							}
							$html .= '<a href="javascript:void(null);" class="'.$class.'" onclick="elx5MultiSelectRemove(\''.$attributes['id'].'\', \''.$option['value'].'\', '.$attributes['flagvalues'].');" title="'.$eLang->get('REMOVE').'">'.$option['label'].' <span>x</span></a>'."\n";
							break;
						}
					}
				}
			}
		}

		if ($attributes['noselected_text'] != '') {
			$class_name = 'elx5_invisible';
			if (!$selected) { $class_name = 'elx5_msel_noselitem'; }
			$html .= '<a href="javascript:void(null);" class="'.$class_name.'" id="'.$attributes['id'].'_noselitem">'.$attributes['noselected_text'].'</a>'."\n";
		}
		$html .= "</div>\n";

		$html .= '<select name="'.$name.'_selector" id="'.$attributes['id'].'_selector"';
		foreach ($attributes as $k => $v) {
			if ($k == 'id') { continue; }
			if ($k == 'name') { continue; }
			if ($k == 'flagvalues') { continue; }
			if ($k == 'noselected_text') { continue; }
			if ($v == '') { continue; }
			$html .= ' '.$k.'="'.$v.'"';
		}
		$html .= ">\n";

		$html .= '<option value="" selected="selected">- '.$eLang->get('ADD')." -</option>\n";
		$html .= '<option value="addall">- '.$eLang->get('ADD_ALL')." -</option>\n";
		if (count($options) > 0) {
			foreach ($options as $option) {
				if (($option['value'] == '') || ($option['value'] == 'addall')) { continue; }//invalid value
				$dis = ($option['disabled'] == 1) ? ' disabled="disabled"' : '';
				$attr = '';
				if (is_array($option['attributes']) && (count($option['attributes']) > 0)) {
					foreach ($option['attributes'] as $key => $val) { $attr .= ' '.$key.'="'.$val.'"'; }
				}
				$html .= '<option value="'.$option['value'].'"'.$dis.''.$attr.'>'.$option['label']."</option>\n";
			}
		}
		$html .= "</select>\n";

		$selected_vals_str = $selected_vals ? implode(',', $selected_vals) : '';
		$html .= '<input type="hidden" name="'.$name.'" id="'.$attributes['id'].'" dir="ltr" value="'.$selected_vals_str.'" />'."\n";
		if ($tip != '') { $html .= '<div class="'.$this->options['tipclass'].'">'.$tip."</div>\n"; }
		if ($onlyelement == 0) {
			$html .= "</div>\n";
			$html .= "</div>\n";
		}

		if ($this->options['returnhtml']) { return $html; }
		echo $html;
	}


	/*************************************************************/
	/* ADD DROP DOWN SELECT FIELD WITH "OTHER" TEXT INPUT OPTION */
	/*************************************************************/
	public function addSelectAddOther($name, $label='', $selected='', $options=array(), $attrs=array()) {
		$eLang = eFactory::getLang();

		$attributes = array('id' => $this->options['idprefix'].$name, 'class' => 'elx5_select', 'dir' => $this->dir);
		$tip = '';
		$noselvalue = '';
		$noseltext = ($label != '') ? ' -'.$label.' -' : '- '.$eLang->get('SELECT').' -';
		$othertext = '--- '.$eLang->get('OTHER').' ---';
		$other_customtitle = '';
		$onlyelement = 0;//Elxis 5.1

		if ($attrs) {
			foreach ($attrs as $k => $v) {
				if ($k == 'name') { continue; }
				if ($k == 'multiple') { continue; }//no multiple support
				if ($k == 'onchange') { continue; }
				if ($k == 'noselvalue') { $noselvalue = $v; continue; }
				if ($k == 'noseltext') { $noseltext = $v; continue; }
				if ($k == 'othertext') {
					$othertext = $v;
					$other_customtitle = eUTF::trim(str_replace('-', '', $v));
					continue;
				}
				if ($k == 'tip') { $tip = $v; continue; }
				if ($k == 'onlyelement') { $onlyelement = (int)$v; continue; }
				$attributes[$k] = $v;
			}
		}
		if ($onlyelement == 1) {
			if (!isset($attributes['title'])) { $attributes['title'] = $label; }
		}

		$html = '';
		if ($onlyelement == 0) {
			$html .= '<div class="'.$this->options['rowclass'].'">'."\n";
			$html .= '<label class="'.$this->options['labelclass'].'" for="'.$attributes['id'].'">'.$label."</label>\n";
			$html .= '<div class="'.$this->options['sideclass'].'">';
		}
		$html .= '<select name="'.$name.'" onchange="elx5SwitchSelectOther(\''.$attributes['id'].'\');"';
		foreach ($attributes as $k => $v) {
			if ($v == '') { continue; }
			$html .= ' '.$k.'="'.$v.'"';
		}
		$html .= ">\n";

		$found = false;
		if ($noselvalue == $selected) {
			$sel = ' selected="selected"';
			$found = true;
		} else {
			$sel = '';
		}

		$html .= '<option value="'.$noselvalue.'"'.$sel.'>'.$noseltext."</option>\n";

		if (is_array($options)) {
			if (count($options) > 0) {
				$optgroup = '';
				foreach ($options as $option) {
					if ($option['optgroup'] != '') {
						if ($optgroup == '') {
							$html .= '<optgroup label="'.$option['optgroup'].'">'."\n";
						} else {
							if ($option['optgroup'] != $optgroup) {
								$html .= "</optgroup>\n";
								$html .= '<optgroup label="'.$option['optgroup'].'">'."\n";
							}
						}
						$optgroup = $option['optgroup'];
					} else {
						if ($optgroup != '') { $html .= "</optgroup>\n"; }
						$optgroup = '';
					}

					$dis = ($option['disabled'] == 1) ? ' disabled="disabled"' : '';
					
					if ($option['value'] == $selected) {
						$sel = ' selected="selected"';
						$found = true;
					} else {
						$sel = '';
					}
					$attr = '';
					if (is_array($option['attributes']) && (count($option['attributes']) > 0)) {
						foreach ($option['attributes'] as $key => $val) { $attr .= ' '.$key.'="'.$val.'"'; }
					}
					$html .= '<option value="'.$option['value'].'"'.$dis.''.$sel.''.$attr.'>'.$option['label']."</option>\n";
				}

				if ($optgroup != '') { $html .= "</optgroup>\n"; }
			}
		}

		if (!$found && ($selected != '')) {
			$html .= '<option value="OTHER" selected="selected">'.$othertext."</option>\n";
			$other_class = 'elx5_tsspace';
			$other_value = $selected;
		} else {
			$html .= '<option value="OTHER">'.$othertext."</option>\n";
			$other_class = 'elx5_invisible';
			$other_value = '';
		}
		$html .= "</select>\n";

		if ($other_customtitle != '') {
			$title = $other_customtitle;
		} else {
			$title = ($label != '') ? $label : $eLang->get('OTHER');
		}
		$html .= '<div class="'.$other_class.'" id="'.$attributes['id'].'_other_box">';
		$html .= '<input type="text" name="'.$name.'_other" id="'.$attributes['id'].'_other" value="'.$other_value.'" class="elx5_text" title="'.$title.'" placeholder="'.$title.'" />'."\n";
		$html .= "</div>\n";
		if ($tip != '') { $html .= '<div class="'.$this->options['tipclass'].'">'.$tip."</div>\n"; }
		if ($onlyelement == 0) {
			$html .= "</div>\n";
			$html .= "</div>\n";
		}

		if ($this->options['returnhtml']) { return $html; }
		echo $html;
	}


	/*****************************************************/
	/* MAKE IMAGES SELECT LIST WITH PREVIEW (Elxis 5.0+) */
	/*****************************************************/
	public function addSelectImage($name, $label='', $selected='', $options=array(), $attrs=array()) {
		$attributes = array('id' => $this->options['idprefix'].$name, 'class' => 'elx5_select', 'dir' => 'ltr');
		$tip = '';
		$onlyelement = 0;//Elxis 5.1
		if ($attrs) {
			foreach ($attrs as $k => $v) {
				if ($k == 'name') { continue; }
				if ($k == 'multiple') { continue; }
				if ($k == 'tip') { $tip = $v; continue; }
				if ($k == 'onlyelement') { $onlyelement = (int)$v; continue; }
				$attributes[$k] = $v;
			}
		}
		if(!isset($attributes['onchange'])) {
			$attributes['onchange'] = 'elx5SwitchPreviewImage(\''.$attributes['id'].'\');';
		}
		if ($onlyelement == 1) {
			if (!isset($attributes['title'])) { $attributes['title'] = $label; }
		}

		$images_dirurl = eFactory::getElxis()->secureBase().'/';
		$empty_image = $images_dirurl.'templates/system/images/nopicture.png';
		$cur_imgurl = ($selected == '') ? $empty_image : $images_dirurl.$selected;

		$html = '';
		if ($onlyelement == 0) {
			$html .= '<div class="'.$this->options['rowclass'].'">'."\n";
			$html .= '<label class="'.$this->options['labelclass'].'" for="'.$attributes['id'].'">'.$label."</label>\n";
			$html .= '<div class="'.$this->options['sideclass'].'">';
		}

		$html .= '<div class="elx5_fileimg_wrap">'."\n";
		$html .= '<a href="'.$cur_imgurl.'" target="_blank" id="'.$attributes['id'].'_imagelink">';
		$html .= '<img src="'.$cur_imgurl.'" alt="image" id="'.$attributes['id'].'_image" data-empty="'.$empty_image.'" data-dirurl="'.$images_dirurl.'" /></a>'."\n";
		$html .= "</div>\n";//elx5_fileimg_wrap
		$html .= '<div class="elx5_fileimg_inwrap">'."\n";
		$html .= '<select name="'.$name.'"';
		foreach ($attributes as $k => $v) {
			if ($v == '') { continue; }
			if ($k == 'name') { continue; }
			if ($k == 'multiple') { continue; }
			$html .= ' '.$k.'="'.$v.'"';
		}
		$html .= ">\n";
		if (is_array($options)) {
			if (count($options) > 0) {
				foreach ($options as $option) {
					$dis = ($option['disabled'] == 1) ? ' disabled="disabled"' : '';
					$sel = ($option['value'] == $selected) ? ' selected="selected"' : '';
					$attr = '';
					if (is_array($option['attributes']) && (count($option['attributes']) > 0)) {
						foreach ($option['attributes'] as $key => $val) { $attr .= ' '.$key.'="'.$val.'"'; }
					}
					$html .= '<option value="'.$option['value'].'"'.$dis.''.$sel.''.$attr.'>'.$option['label']."</option>\n";
				}
			}
		}
		$html .= "</select>\n";
		$html .= "</div>\n";//elx5_fileimg_inwrap
		if ($tip != '') { $html .= '<div class="'.$this->options['tipclass'].'">'.$tip."</div>\n"; }
		if ($onlyelement == 0) {
			$html .= "</div>\n";
			$html .= "</div>\n";
		}

		if ($this->options['returnhtml']) { return $html; }
		echo $html;
	}


	/*********************************/
	/* ADD ACCESS LEVEL SELECT FIELD */
	/*********************************/
	public function addAccesslevel($name, $label='', $selected=0, $userlevel=0, $attrs=array()) {
		$eLang = eFactory::getLang();
		$db = eFactory::getDB();

		if (!is_array($attrs)) { $attrs = array(); }
		$attrs['dir'] = 'ltr';

		$stmt = $db->prepare("SELECT * FROM ".$db->quoteId('#__groups').' ORDER BY '.$db->quoteId('level').' DESC');
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$options = array();
		if ($rows) {
			$levels = array();
			foreach ($rows as $row) {
				$level = $row['level'];
				if (!isset($levels[$level])) { $levels[$level] = 0; }
				$levels[$level]++;
			}

			$lastlevel = -1;
			$space = '';
			foreach ($rows as $row) {
				$level = $row['level'];
				$lowlevel = $row['level'] * 1000;
				$exactlevel = ($row['level'] * 1000) + $row['gid'];
				if ($row['gid'] == 1) {
					$groupname = $eLang->get('ADMINISTRATOR');
					$exactlevel = 100000;
				} else if ($row['gid'] == 5) {
					$groupname = $eLang->get('USER');
				} else if ($row['gid'] == 6) {
					$groupname = $eLang->get('EXTERNALUSER');
				} elseif ($row['gid'] == 7) {
					$groupname = $eLang->get('GUEST');
					$exactlevel = 0;
				} else {
					$groupname = $row['groupname'];
				}

				$disabled = ($userlevel < $level) ? 1 : 0;
				if ($level != $lastlevel) {
					$space .= ($lastlevel == -1) ? '' : '. &#160;';
					$lastlevel = $level;
					if ($levels[$level] > 1) {
						$leveltext = sprintf($eLang->get('ALL_GROUPS_LEVEL'), $level);
						$options[] = $this->makeOption($lowlevel, $space.$leveltext, array(), $disabled);
					}
				}

				switch ($level) {
					case 0: case 1: case 2: case 100: $optionvalue = $lowlevel; break;
					default:
						$optionvalue = ($levels[$level] > 1) ? $exactlevel : $lowlevel;
					break;
				}

				$options[] = $this->makeOption($optionvalue, $space.$level.' - '.$groupname, array(), $disabled);
			}
		}

		if ($this->options['returnhtml']) {
			return $this->addSelect($name, $label, $selected, $options, $attrs);
		} else {
			$this->addSelect($name, $label, $selected, $options, $attrs);
		}
	}


	/*****************************/
	/* ADD LANGUAGE SELECT FIELD */
	/*****************************/
	public function addLanguage($name, $label, $selected='', $attrs=array(), $ltype=2, $nativeNames=1, $flags=false, $select_text='') {
		$eLang = eFactory::getLang();

		$ltype = (int)$ltype;
		$nativeNames = (int)$nativeNames;
		$xlangs = array();
		switch ($ltype) {
			case 0://all languages even not installed
				if (file_exists(ELXIS_PATH.'/includes/libraries/elxis/language/langdb.php')) {
					include(ELXIS_PATH.'/includes/libraries/elxis/language/langdb.php');
					if (isset($langdb)) {
						$xlangs = $langdb;
						unset($langdb);
					}
				}
			break;
			case 2:	$xlangs = eFactory::getLang()->getSiteLangs(true); break;//site enabled
			case 1: default: $xlangs = eFactory::getLang()->getAllLangs(true); break;//all installed
		}

		$attributes = array('id' => $this->options['idprefix'].$name, 'class' => 'elx5_select', 'dir' => 'ltr');
		if ($attrs) {
			foreach ($attrs as $k => $v) {
				if ($k == 'dir') { continue; }
				$attributes[$k] = $v;
			}
		}

		$options = array();
		if ($select_text != '') { $options[] = $this->makeOption('', $select_text); }

		if ($flags) {
			$attributes['onchange'] = 'elx5SwitchAddLanguage(\''.$attributes['id'].'\')';
			$this->prepareMultiLinguism();
		}

		if (!$xlangs) {
			$selected = '';
			if ($flags) { $attributes['class'] .= ' elx5_mlflagun'; }
			if ($select_text == '') { $options[] = $this->makeOption('', $eLang->get('SELECT')); }
		} else {
			if ($flags) {
				$attributes['class'] .= ($selected == '') ? ' elx5_mlflagun' : ' elx5_mlflag'.$selected;
			}
			if ($select_text == '') {
				if ($selected == '') { $options[] = $this->makeOption('', $eLang->get('SELECT')); }
			}
		}

		if ($xlangs) {
			foreach ($xlangs as $lng => $info) {
				if ($nativeNames == 5) {
					$optlabel = strtoupper($lng).' - '.$info['NAME'];
				} else if ($nativeNames == 4) {
					$optlabel = strtoupper($lng).' - '.$info['NAME_ENG'];
				} else if ($nativeNames == 3) {
					$optlabel = strtoupper($lng);
				} else if ($nativeNames == 2) {
					$optlabel = $info['NAME'].' - '.$info['NAME_ENG'];
				} else if ($nativeNames == 0) {
					$optlabel = $info['NAME_ENG'];
				} else {//1 : default
					$optlabel = $info['NAME'];
				}
				$options[] = $this->makeOption($lng, $optlabel);
			}
		}

		if ($this->options['returnhtml']) {
			return $this->addSelect($name, $label, $selected, $options, $attributes);
		} else {
			$this->addSelect($name, $label, $selected, $options, $attributes);
		}
	}


	/***********************************************/
	/* ADD YES/NO BOX FIELD (ATTENTION: CHECKBOX!) */
	/***********************************************/
	public function addYesNo($name, $label, $checked=0, $attrs=array()) {
		$eLang = eFactory::getLang();

		$checked = (int)$checked;
		$attributes = array('id' => $this->options['idprefix'].$name);
		$tip = '';//not recommended for this type of element
		$enablecolor = 'green';
		$onlyelement = 0;//Elxis 5.1
		if ($attrs) {
			if (isset($attrs['tip'])) { $tip = $attrs['tip']; }
			if (isset($attrs['id'])) { $attributes['id'] = $attrs['id']; }
			if (isset($attrs['enablecolor'])) { if ($attrs['enablecolor'] != '') { $enablecolor = $attrs['enablecolor']; } }
			if (isset($attrs['onlyelement'])) { $onlyelement = (int)$attrs['onlyelement']; }
		}

		$str_checked = ($checked == 1) ? ' checked="checked"' : '';
		$switchlabelclass = ($enablecolor == 'red') ? 'elx5_switchlabelred' : 'elx5_switchlabel';

		$html = '';
		if ($onlyelement == 0) {
			$html .= '<div class="'.$this->options['rowclass'].'">'."\n";
			$html .= '<label class="'.$this->options['labelclass'].'" for="'.$attributes['id'].'">'.$label."</label>\n";
			$html .= '<div class="'.$this->options['sideclass'].'">';
		}

		$html .= '<label class="elx5_switch"><input type="checkbox" name="'.$name.'" id="'.$attributes['id'].'" class="elx5_switchinput" value="1"'.$str_checked.' />';
		$html .= '<span class="'.$switchlabelclass.'" data-on="'.$eLang->get('YES').'" data-off="'.$eLang->get('NO').'"></span>';
		$html .= '<span class="elx5_switchhandle"></span>';
		$html .= '</label>';
		if ($tip != '') { $html .= '<div class="'.$this->options['tipclass'].'">'.$tip."</div>\n"; }
		if ($onlyelement == 0) {
			$html .= "</div>\n";
			$html .= "</div>\n";
		}

		if ($this->options['returnhtml']) { return $html; }
		echo $html;
	}


	/****************************************************************/
	/* ADD ITEM STATUS (ATTENTION: WORKS ONLY WITH INTEGER VALUES!) */
	/****************************************************************/
	public function addItemStatus($name, $label, $value, $status_options, $attrs=array()) {
		if (!$status_options) { return; }
		$value = (int)$value;

		$attributes = array('id' => $this->options['idprefix'].$name);
		$tip = '';//not recommended for this type of element
		$onlyelement = 0;//Elxis 5.1
		if ($attrs) {
			if (isset($attrs['tip'])) { $tip = $attrs['tip']; }
			if (isset($attrs['id'])) { $attributes['id'] = $attrs['id']; }
			if (isset($attrs['onlyelement'])) { $onlyelement = (int)$attrs['onlyelement']; }
		}

		//$status_options = array(
		//array('name' => , 'value' => , 'color' => ), ....
		//);

		$cur_color = 'gray';
		$cur_label = '-';
		$values = array();
		$labels = array();
		foreach ($status_options as $option) {
			$v = (int)$option['value'];
			if ($v == $value) {
				$cur_label = $option['name'];
				$cur_color = $option['color'];
			}
			$labels[] = addslashes($option['name']);
			$values[] = (int)$option['value'];
			$colors[] = $option['color'];
		}

		$html = '';
		if ($onlyelement == 0) {
			$html .= '<div class="'.$this->options['rowclass'].'">'."\n";
			$html .= '<label class="'.$this->options['labelclass'].'" for="'.$attributes['id'].'">'.$label."</label>\n";
			$html .= '<div class="'.$this->options['sideclass'].'">';
		}
		$html .= '<a href="javascript:void(null);" onclick="elx5SwitchStatus(\''.$attributes['id'].'\', this);" class="elx5_itemstatus elx5_itemstatus_'.$cur_color.'" data-values="'.implode('|', $values).'" data-labels="'.implode('|', $labels).'" data-colors="'.implode('|', $colors).'"><span></span>'.$cur_label.'</a>';
		if ($tip != '') { $html .= '<div class="'.$this->options['tipclass'].'">'.$tip."</div>\n"; }
		if ($onlyelement == 0) {
			$html .= "</div>\n</div>\n";
		}
		$html .= '<input type="hidden" name="'.$name.'" id="'.$attributes['id'].'" value="'.$value.'" />'."\n";

		if ($this->options['returnhtml']) { return $html; }
		echo $html;
	}


	/***********************/
	/* ADD RADIO BOX FIELD */
	/***********************/
	public function addRadio($name, $label='', $checked='', $options=array(), $attrs=array()) {
		$attributes = array('id' => $this->options['idprefix'].$name);
		$tip = '';
		$onlyelement = 0;//Elxis 5.1
		if ($attrs) {
			foreach ($attrs as $k => $v) {
				if ($k == 'tip') { $tip = $v; continue; }
				if ($k == 'onlyelement') { $onlyelement = (int)$v; continue; }
				$attributes[$k] = $v;
			}
		}

		$html = '';
		if ($onlyelement == 0) {
			$html .= '<div class="'.$this->options['rowclass'].'">'."\n";
			$html .= '<label class="'.$this->options['labelclass'].'" for="'.$attributes['id'].'0">'.$label."</label>\n";
			$html .= '<div class="'.$this->options['sideclass'].'">';			
		}
		if (is_array($options)) {
			if (count($options) > 0) {
				foreach ($options as $q => $option) {
					$chk = ($option['value'] == $checked) ? ' checked="checked"' : '';
					$attr = '';
					$optionclass = 'elx5_radio';
					if (is_array($option['attributes']) && (count($option['attributes']) > 0)) {
						foreach ($option['attributes'] as $key => $val) {
							if (in_array($key, array('id', 'name', 'type'))) { continue; }
							if ($key == 'class') { $optionclass = $val; continue; }
							$attr .= ' '.$key.'="'.$val.'"';
						}
					}
					$html .= '<label class="elx5_radiowrap">'.$option['label'].'<input type="radio" name="'.$name.'" id="'.$attributes['id'].$q.'" class="'.$optionclass.'" value="'.$option['value'].'"'.$chk.$attr.' />';
					$html .= '<span class="elx5_radio_checkmark"></span></label>'."\n";
				}
			}
		}

		if ($tip != '') { $html .= '<div class="'.$this->options['tipclass'].'">'.$tip."</div>\n"; }
		if ($onlyelement == 0) {
			$html .= "</div>\n";
			$html .= "</div>\n";
		}

		if ($this->options['returnhtml']) { return $html; }
		echo $html;
	}


	/*********************************/
	/* ADD SHOW/HIDE RADIO BOX FIELD */
	/*********************************/
	public function addShowHide($name, $label='', $checked=0, $attrs=array()) {
		$eLang = eFactory::getLang();

		$checked = (int)$checked;

		$options = array();
		$options[] = $this->makeOption(1, $eLang->get('SHOW'));
		$options[] = $this->makeOption(0, $eLang->get('HIDE'));

		if ($this->options['returnhtml']) {
			return $this->addRadio($name, $label, $checked, $options, $attrs);
		} else {
			$this->addRadio($name, $label, $checked, $options, $attrs);
		}
	}


	/********************/
	/* ADD BUTTON FIELD */
	/********************/
	public function addButton($name, $title, $button_type='submit', $attrs=array()) {
		$button_type = strtolower($button_type);
		if (($button_type == '') || !in_array($button_type, array('submit', 'reset', 'button'))) { $button_type = 'submit'; }

		$attributes = array('id' => $this->options['idprefix'].$name, 'class' => 'elx5_btn', 'value' => '1', 'title' => $title);

		$tip = '';
		$fontawesome = '';//fontawesome css class
		$sidepad = 0;
		$labelclass = 'elx5_label';//sidepad = 1, Elxis 5.3
		$sideclass = 'elx5_labelside';//sidepad = 1, Elxis 5.3
		$onlyelement = 0;//Elxis 5.1
		if ($attrs) {
			foreach ($attrs as $k => $v) {
				if ($k == 'fontawesome') { $fontawesome = $v; continue; }
				if ($k == 'tip') { $tip = $v; continue; }
				if ($k == 'sidepad') { $sidepad = (int)$v; continue; }
				if ($k == 'onlyelement') { $onlyelement = (int)$v; continue; }
				if ($k == 'labelclass') { $labelclass = $v; continue; }
				if ($k == 'sideclass') { $sideclass = $v; continue; }
				$attributes[$k] = $v;
			}
		}

		$html = '';
		if (($sidepad == 1) && ($onlyelement == 0)) {
			$html .= '<div class="'.$this->options['rowclass'].'">'."\n";
			$html .= '<label class="'.$labelclass.'" for="'.$attributes['id'].'"></label>'."\n";
			$html .= '<div class="'.$sideclass.'">';
		}
		$html .= '<button type="'.$button_type.'" name="'.$name.'"';
		foreach ($attributes as $k => $v) {
			if ($k == 'type') { continue; }
			if ($k == 'name') { continue; }
			if ($v == '') { continue; }
			$html .= ' '.$k.'="'.$v.'"';
		}
		if ($fontawesome != '') {
			eFactory::getDocument()->addFontAwesome();
			$html .= '><i class="'.$fontawesome.'" aria-hidden="true"></i><span>'.$title."</span></button>\n";
		} else {
			$html .= '>'.$title."</button>\n";
		}
		if ($tip != '') { $html .= '<div class="'.$this->options['tipclass'].'">'.$tip."</div>\n"; }
		if (($sidepad == 1) && ($onlyelement == 0)) { $html .= "</div></div>\n"; }

		if ($this->options['returnhtml']) { return $html; }
		echo $html;
	}


	/********************************/
	/* ADD LINK LOOKING LIKE BUTTON */
	/********************************/
	public function addLinkButton($title, $link='', $attrs=array()) {
		$attributes = array('class' => 'elx5_btn', 'title' => $title);

		$fontawesome = '';//fontawesome css class
		if ($attrs) {
			foreach ($attrs as $k => $v) {
				if ($k == 'fontawesome') { $fontawesome = $v; continue; }
				if ($k == 'href') {
					if ($link == '') { $link = $v; }
					continue;
				}
				$attributes[$k] = $v;
			}
		}

		if ($link == '') { $link = 'javascript:void(null);'; }

		$html = '<a href="'.$link.'"';
		foreach ($attributes as $k => $v) {
			if ($v == '') { continue; }
			$html .= ' '.$k.'="'.$v.'"';
		}
		if ($fontawesome != '') {
			eFactory::getDocument()->addFontAwesome();
			$html .= '><i class="'.$fontawesome.'" aria-hidden="true"></i><span>'.$title."</span></a>\n";
		} else {
			$html .= '>'.$title."</a>\n";
		}

		if ($this->options['returnhtml']) { return $html; }
		echo $html;
	}


	/*******************************/
	/* ADD MULTILINGUAL TEXT FIELD */
	/*******************************/
	public function addMLText($name, $trdata, $value='', $label='', $attrs=array()) {
		$elxis = eFactory::getElxis();

		$allowed = ($elxis->acl()->check('component', 'com_etranslator', 'manage') < 1) ? false : true;
		if (!$allowed && !defined('ELXIS_ADMIN')) {
			$allowed = ($elxis->acl()->check('component', 'com_etranslator', 'api') < 1) ? false : true;
		}

		$translations = array();
		$has_translations = false;
		$attributes = array('id' => $this->options['idprefix'].$name, 'placeholder' => $label);
		$tip = '';
		$onlyelement = 0;//Elxis 5.1
		if ($attrs) {
			foreach ($attrs as $k => $v) {
				if ($k == 'tip') {
					$tip = $v;
					continue;
				}
				if ($k == 'id') { continue; }
				if ($k == 'class') { continue; }
				if ($k == 'dir') { continue; }
				if ($k == 'translations') {//multilingual XML parameter, no trdata in this case but provided translations in $attrs
					$has_translations = true;
					$translations = $v;
					continue;
				}
				if ($k == 'onlyelement') { $onlyelement = (int)$v; continue; }
				$attributes[$k] = $v;
			}
		}
		if ($onlyelement == 1) {
			if (!isset($attributes['title'])) { $attributes['title'] = $label; }
		}

		$sitelangs = $this->getSiteLangs();
		$langnames = $this->getSiteLangs() ? array_keys($sitelangs) : array();
		$clang = $elxis->getConfig('LANG');
		if (!$has_translations) {
			$translations = $this->getTranslations($trdata['category'], $trdata['element'], $trdata['elid']);
		}

		$this->prepareMultiLinguism();

		$distxt = '';
		if (!$allowed) { $distxt = ' disabled="disabled"'; }

		$html = '';
		if ($onlyelement == 0) {
			$html .= '<div class="'.$this->options['rowclass'].'">'."\n";
			$html .= '<label class="'.$this->options['labelclass'].'" for="'.$attributes['id'].'">'.$label."</label>\n";
			$html .= '<div class="'.$this->options['sideclass'].'">';
		}
		$html .= '<div class="elx5_mlboxwrap">'."\n";
		$html .= '<div class="elx5_mlboxlang">'."\n";
		$html .= '<select name="'.$name.'_lang" id="'.$attributes['id'].'_lang" class="elx5_select elx5_mlflag'.$clang.'" dir="ltr" data-deflang="'.$clang.'" data-trelement="'.$name.'" data-sitelangs="'.implode(',', $langnames).'" onchange="elx5MLSwitch(\''.$this->options['idprefix'].'\', \''.$name.'\');"'.$distxt.'>'."\n";
		if ($sitelangs) {
			foreach ($sitelangs as $lng => $sitelang) {
				$sel = '';
				$oclass = '';
				if ($lng == $clang) {
					$sel = ' selected="selected"';
					$oclass = ' class="elx5_defoption"';
				} else {
					if (isset($translations[$lng])) { $oclass = ' class="elx5_hloption"'; }
				}
				$html .= '<option value="'.$lng.'"'.$oclass.$sel.'>'.strtoupper($lng)."</option>\n";
			}
		}
		$html .= "</select>\n";
		$html .= "</div>\n";//elx5_mlboxlang
		$html .= '<div class="elx5_mlboxtext">'."\n";
		if ($sitelangs) {
			$trname = $name.'_';
			$trname_end = '';
			if (preg_match('@(])$@', $name)) {//XML param or other array style naming like "something[else]" => "something[else_mlLNG]"
				$trname = preg_replace('@(])$@', '', $name).'_ml';
				$trname_end = ']';
			}
			foreach ($sitelangs as $lng => $sitelang) {
				if ($lng == $clang) { continue; }
				$dir = ($sitelang['RTLSFX'] == '-rtl') ? 'rtl' : 'ltr';
				$v = isset($translations[$lng]) ? $translations[$lng] : '';
				$html .= '<input type="text" name="'.$trname.$lng.$trname_end.'" id="'.$attributes['id'].'_'.$lng.'" dir="'.$dir.'" class="elx5_invisible" value="'.$v.'" placeholder="'.$label.' - '.$sitelang['NAME_ENG'].'" />'."\n";
			}
		}
		$dir = ($sitelangs[$clang]['RTLSFX'] == '-rtl') ? 'rtl' : 'ltr';
		$html .= '<input type="text" name="'.$name.'" value="'.$value.'" id="'.$attributes['id'].'" dir="'.$dir.'" class="elx5_text elx5_mlflag'.$clang.'"';
		foreach ($attributes as $k => $v) {
			if ($v == '') { continue; }
			if ($k == 'id') { continue; }
			$html .= ' '.$k.'="'.$v.'"';
		}
		$html .= " />\n";
		$html .= "</div>\n";//elx5_mlboxtext
		$html .= "</div>\n";//elx5_mlboxwrap
		if ($tip != '') { $html .= '<div class="'.$this->options['tipclass'].'">'.$tip."</div>\n"; }
		if ($onlyelement == 0) { $html .= "</div>\n</div>\n"; }

		if ($this->options['returnhtml']) { return $html; }
		echo $html;
	}


	/****************/
	/* ADD RAW HTML */
	/****************/
	public function addHTML($html) {
		if ($this->options['returnhtml']) { return $html; }
		echo $html;
	}


	public function addCustom($html) {//Elxis 4.x compatibility
		$this->addHTML($html);
	}


	/*****************************/
	/* ADD TIMEZONE SELECT FIELD */
	/*****************************/
	public function addTimezone($name, $label='', $selected='', $attrs=array()) {
		$attributes = array('id' => $this->options['idprefix'].$name, 'class' => 'elx5_select', 'dir' => 'ltr');
		if ($attrs) {
			foreach ($attrs as $k => $v) {
				if ($k == 'dir') { continue; }
				$attributes[$k] = $v;
			}
		}

		$zones = timezone_identifiers_list();
		$options = array();
		foreach ($zones as $zone) { $options[] = $this->makeOption($zone, $zone); }

		if ($this->options['returnhtml']) {
			return $this->addSelect($name, $label, $selected, $options, $attributes);
		} else {
			$this->addSelect($name, $label, $selected, $options, $attributes);
		}
	}


	/****************************/
	/* ADD SLIDER NUMERIC FIELD */
	/****************************/
	public function addSlider($name, $value='', $label='', $attrs=array()) {//Elxis 4.x compatibility
		if (!$attrs) { $attrs = array(); }
		$attrs['dir'] = 'ltr';
		if (!isset($attrs['min'])) { $attrs['min'] = 0; }
		if (!isset($attrs['max'])) { $attrs['max'] = 100; }
		if (!isset($attrs['step'])) { $attrs['step'] = 1; }
		if (!isset($attrs['showvalue'])) { $attrs['showvalue'] = 0; }

		if ($attrs['showvalue'] == 0) {
			if ($this->options['returnhtml']) {
				return $this->addInput('range', $name, $value, $label, $attrs);
			} else {
				$this->addInput('range', $name, $value, $label, $attrs);
			}
		}

		$attributes = array(
			'id' => $this->options['idprefix'].$name,
			'class' => 'elx5_text',
			'dir' => $this->dir,
			'placeholder' => $label
		);
		$tip = '';
		$onlyelement = 0;//Elxis 5.1
		if ($attrs) {
			foreach ($attrs as $k => $v) {
				if ($k == 'showvalue') { continue; }
				if ($k == 'tip') {
					$tip = $v;
					continue;
				}
				if ($k == 'onlyelement') { $onlyelement = (int)$v; continue; }
				$attributes[$k] = $v;
			}
		}

		if ($value != '') {
			if (!isset($attributes['title'])) { $attributes['title'] = $label; }
		}
		if ($onlyelement == 1) {
			if (!isset($attributes['title'])) { $attributes['title'] = $label; }
		}

		if ($attrs['showvalue'] == 1) {
			$class1 = 'elx5_sideinput_value_front';
			$class2 = 'elx5_sideinput_input_end';
		} else {
			$class1 = 'elx5_sideinput_value_end';
			$class2 = 'elx5_sideinput_input_front';
		}

		$html = '';
		if ($onlyelement == 0) {
			$html .= '<div class="'.$this->options['rowclass'].'">'."\n";
			$html .= '<label class="'.$this->options['labelclass'].'" for="'.$attributes['id'].'">'.$label."</label>\n";
			$html .= '<div class="'.$this->options['sideclass'].'">';			
		}
		$html .= '<div class="elx5_sideinput_wrap">'."\n";
		$html .= '<div id="'.$attributes['id'].'_value" class="'.$class1.'">'.$value.'</div>'."\n";
		$html .= '<div class="'.$class2.'">';
		$html .= '<input type="range" name="'.$name.'" value="'.$value.'"';
		foreach ($attributes as $k => $v) {
			if ($v == '') { continue; }
			if ($k == 'type') { continue; }
			if ($k == 'name') { continue; }
			if ($k == 'value') { continue; }
			$html .= ' '.$k.'="'.$v.'"';
		}
		$html .= " /></div>\n";
		$html .= "</div>\n";

		if ($tip != '') { $html .= '<div class="'.$this->options['tipclass'].'">'.$tip."</div>\n"; }
		if ($onlyelement == 0) {
			$html .= "</div>\n";
			$html .= "</div>\n";
		}

		$html .= "<script>\n";
		$html .= 'var '.$this->options['idprefix'].'fmslider = document.getElementById(\''.$attributes['id'].'\');'."\n";
		$html .= $this->options['idprefix'].'fmslider.oninput = function() {'."\n";
		$html .= 'document.getElementById(\''.$attributes['id'].'_value\').innerHTML = this.value;'."\n";
		$html .= "}\n";
		$html .= "</script>\n";

		if ($this->options['returnhtml']) { return $html; }
		echo $html;
	}


	/**************************/
	/* ADD INPUT RANGE FIELD */
	/**************************/
	public function addRangeNative($name, $value='', $label='', $min=1, $max=10, $step=1, $attrs=array()) { //HTML5 range, Elxis 4.x compatibility
		if (!$attrs) { $attrs = array(); }
		$attrs['dir'] = 'ltr';
		$attrs['min'] = $min;
		$attrs['max'] = $max;
		$attrs['step'] = $step;

		if ($this->options['returnhtml']) {
			return $this->addInput('range', $name, $value, $label, $attrs);
		} else {
			$this->addInput('range', $name, $value, $label, $attrs);
		}
	}


	/************************************************/
	/* ADD RANGE OF INTEGERS DROP DOWN SELECT FIELD */
	/************************************************/
	public function addRange($name, $label='', $first=0, $last=1, $selected=-1, $step=1, $attrs=array()) {//Elxis 4.x compatibility
		$first = (int)$first;
		$last = (int)$last;
		$step = (int)$step;
		$selected = (int)$selected;
		if ($step < 1) { $step = 1; }

		if ($first == $last) {
			$values = array($first);
		} else if ($first < $last) {
			$values = range($first, $last, $step);
		} else {
			$values = range($last, $first, $step);
			$values = array_reverse($values);
		}
		$options = array();
		foreach ($values as $value) {
			$options[] = $this->makeOption($value, $value);
		}
		if ($this->options['returnhtml']) {
			return $this->addSelect($name, $label, $selected, $options, $attrs);
		} else {
			$this->addSelect($name, $label, $selected, $options, $attrs);
		}
	}


	/******************************/
	/* ADD USERGROUP SELECT FIELD */
	/******************************/
	public function addUsergroup($name, $label='', $selected='0', $lowerlevel=0, $upperlevel=100, $attrs=array()) {
		$eLang = eFactory::getLang();
		$db = eFactory::getDB();

		$selected = (int)$selected;
		if (!$attrs) { $attrs = array(); }
		$showgid = 1;
		$showlevel = 1;
		$showgroupname = 1;
		$showalloption = 0;
		$alloptionvalue = 0;
		$alloptiontext = '- '.$eLang->get('ALL').' - ';
		if (isset($attrs['showgid'])) {
			$showgid = (int)$attrs['showgid'];
			unset($attrs['showgid']);
		}
		if (isset($attrs['showlevel'])) {
			$showlevel = (int)$attrs['showlevel'];
			unset($attrs['showlevel']);
		}
		if (isset($attrs['showgroupname'])) {
			$showgroupname = (int)$attrs['showgroupname'];
			unset($attrs['showgroupname']);
		}
		if (isset($attrs['showalloption'])) {
			$showalloption = (int)$attrs['showalloption'];
			unset($attrs['showalloption']);
		}
		if (isset($attrs['alloptionvalue'])) {
			$alloptionvalue = (int)$attrs['alloptionvalue'];
			unset($attrs['alloptionvalue']);
		}
		if (isset($attrs['alloptiontext'])) {
			$alloptiontext = $attrs['alloptiontext'];
			unset($attrs['alloptiontext']);
		}

		$sql = "SELECT * FROM ".$db->quoteId('#__groups');
		$sql .= ' WHERE '.$db->quoteId('level').' >= :llev AND '.$db->quoteId('level').' <= :ulev';
		$sql .= ' ORDER BY '.$db->quoteId('level').' DESC';
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':llev', $lowerlevel, PDO::PARAM_INT);
		$stmt->bindParam(':ulev', $upperlevel, PDO::PARAM_INT);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$options = array();
		if ($showalloption == 1) {
			$options[] = $this->makeOption($alloptionvalue, $alloptiontext);
		}

		if ($rows) {
			foreach ($rows as $row) {
				if ($row['gid'] == 1) {
					$groupname = $eLang->get('ADMINISTRATOR');
				} else if ($row['gid'] == 5) {
					$groupname = $eLang->get('USER');
				} else if ($row['gid'] == 6) {
					$groupname = $eLang->get('EXTERNALUSER');
				} elseif ($row['gid'] == 7) {
					$groupname = $eLang->get('GUEST');
				} else {
					$groupname = $row['groupname'];
				}

				$parts = array();
				if ($showgid == 1) { $parts[] = (string)$row['gid']; }
				if ($showlevel == 1) { $parts[] = sprintf("%03d", $row['level']); }
				if ($showgroupname == 1) { $parts[] = $groupname; }

				$txt = $parts ? implode(' - ', $parts) : $groupname;
				$options[] = $this->makeOption($row['gid'], $txt);
			}
		}

		if ($this->options['returnhtml']) {
			return $this->addSelect($name, $label, $selected, $options, $attrs);
		} else {
			$this->addSelect($name, $label, $selected, $options, $attrs);
		}
	}


	/********************************************/
	/* ADD IMAGE FIELD (FILE WITH IMAGE PREVIEW)*/
	/********************************************/
	public function addImage($name, $value='', $label='', $attrs=array()) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$attributes = array(
			'id' => $this->options['idprefix'].$name,
			'class' => 'elx5_text',
			'dir' => 'ltr',
			'placeholder' => $label
		);

		$relpath = '';
		$tip = '';
		$onlyelement = 0;//Elxis 5.1
		if ($attrs) {
			foreach ($attrs as $k => $v) {
				if ($k == 'tip') {
					$tip = $v;
					continue;
				}
				if ($k == 'relpath') {
					$relpath = $v;
					continue;
				}
				if ($k == 'onchange') { continue; }
				if ($k == 'onlyelement') { $onlyelement = (int)$v; continue; }
				$attributes[$k] = $v;
			}
		}
		if (!isset($attributes['accept'])) { $attributes['accept'] = '.jpg,.jpeg,.png,.gif'; }
		$attributes['onchange'] = 'elx5FileImagePreview(\''.$attributes['id'].'\');';
		if ($onlyelement == 1) {
			if (!isset($attributes['title'])) { $attributes['title'] = $label; }
		}

		$empty_image = $elxis->secureBase().'/templates/system/images/nopicture.png';
		$cur_imgurl = $empty_image;
		$cur_imgname = $eLang->get('NO_IMAGE_UPLOADED');

		if ($value != '') {
			$parts = preg_split('#\/#', $value, -1, PREG_SPLIT_NO_EMPTY);
			$i = count($parts) - 1;
			$cur_imgname = $parts[$i];
			if (strpos($value, 'http') === 0) {
				$cur_imgurl = $value;
			} else {
				if (file_exists(ELXIS_PATH.'/'.$relpath.$value)) {
					$info = getimagesize(ELXIS_PATH.'/'.$relpath.$value);
					$cur_imgname .= ' ('.$info[0].'x'.$info[1].', '.round((filesize(ELXIS_PATH.'/'.$relpath.$value) / 1024), 2).' KB)';
				}
				$cur_imgurl = $elxis->secureBase().'/'.$relpath.$value;
			}
		}

		if (isset($attributes['readonly'])) {
			if (strpos($attributes['class'], 'readonly') === false) { $attributes['class'] .= ' elx5_readonly'; }
		}

		$html = '';
		if ($onlyelement == 0) {
			$html .= '<div class="'.$this->options['rowclass'].'">'."\n";
			$html .= '<label class="'.$this->options['labelclass'].'" for="'.$attributes['id'].'">'.$label."</label>\n";
			$html .= '<div class="'.$this->options['sideclass'].'">';			
		}

		$html .= '<div class="elx5_fileimg_wrap">'."\n";
		$html .= '<a href="'.$cur_imgurl.'" target="_blank" id="'.$attributes['id'].'_imagelink">';
		$html .= '<img src="'.$cur_imgurl.'" alt="image" id="'.$attributes['id'].'_image" data-empty="'.$empty_image.'" data-noimglng="'.addslashes($eLang->get('NO_IMAGE_FILE')).'" data-noimguplng="'.addslashes($eLang->get('NO_IMAGE_UPLOADED')).'" /></a>'."\n";
		$html .= "</div>\n";//elx5_fileimg_wrap
		$html .= '<div class="elx5_fileimg_inwrap">'."\n";

		$html .= '<div class="elx5_fileimg_cur_wrap">'."\n";
		$html .= '<div class="elx5_fileimg_cur_file" title="'.$cur_imgname.'" id="'.$attributes['id'].'_imagename">'.$cur_imgname.'</div>'."\n";
		$html .= '<a href="javascript:void(null);" class="elx5_fileimg_del" onclick="elx5FileimgDeleteImage(\''.$attributes['id'].'\');" title="'.$eLang->get('DELETE_CURRENT_IMAGE').'">X</a>'."\n";
		$html .= "</div>\n";//elx5_fileimg_cur_wrap

		$html .= '<input type="file" name="'.$name.'" value=""';
		foreach ($attributes as $k => $v) {
			if ($v == '') { continue; }
			if ($k == 'type') { continue; }
			if ($k == 'name') { continue; }
			if ($k == 'value') { continue; }
			$html .= ' '.$k.'="'.$v.'"';
		}
		$html .= " />\n";
		$html .= '<input type="hidden" name="'.$name.'_deleteold" id="'.$attributes['id'].'_deleteold" value="0" dir="ltr" />'."\n";
		$html .= "</div>\n";//elx5_fileimg_inwrap
		if ($tip != '') { $html .= '<div class="'.$this->options['tipclass'].'">'.$tip."</div>\n"; }
		if ($onlyelement == 0) {
			$html .= "</div>\n";
			$html .= "</div>\n";
		}

		if ($this->options['returnhtml']) { return $html; }
		echo $html;
	}


	/****************************/
	/* ADD COUNTRY SELECT FIELD */
	/****************************/
	public function addCountry($name, $label='', $selected='', $attrs=array(), $select_text='') {
		$eLang = eFactory::getLang();

		$attributes = array('id' => $this->options['idprefix'].$name, 'class' => 'elx5_select', 'dir' => 'ltr');
		if ($attrs) {
			foreach ($attrs as $k => $v) {
				if ($k == 'dir') { continue; }
				$attributes[$k] = $v;
			}
		}

		$lng = $eLang->getinfo('LANGUAGE');
		if (file_exists(ELXIS_PATH.'/includes/libraries/elxis/form/countries.'.$lng.'.php')) {
			include(ELXIS_PATH.'/includes/libraries/elxis/form/countries.'.$lng.'.php');
		} else {
			include(ELXIS_PATH.'/includes/libraries/elxis/form/countries.en.php');
		}

		$options = array();
		if ($select_text != '') {
			$options[] = $this->makeOption('', $select_text);
		} else {
			if ($selected == '') { $options[] = $this->makeOption('', $eLang->get('SELECT')); }
		}
		if (isset($countries)) {
			foreach ($countries as $key => $cname) { $options[] = $this->makeOption($key, $cname); }
		}

		if ($this->options['returnhtml']) {
			return $this->addSelect($name, $label, $selected, $options, $attributes);
		} else {
			$this->addSelect($name, $label, $selected, $options, $attributes);
		}
	}


	public function addDate($name, $value='', $label='', $attrs=array()) {
		if ($this->options['returnhtml']) {
			return $this->addHmeromhnia($name, $value, $label, $attrs, 'date');
		} else {
			$this->addHmeromhnia($name, $value, $label, $attrs, 'date');
		}
	}


	public function addDatetime($name, $value='', $label='', $attrs=array()) {
		if ($this->options['returnhtml']) {
			return $this->addHmeromhnia($name, $value, $label, $attrs, 'datetime');
		} else {
			$this->addHmeromhnia($name, $value, $label, $attrs, 'datetime');
		}
	}


	public function addTime($name, $value='', $label='', $attrs=array()) {
		if ($this->options['returnhtml']) {
			return $this->addHmeromhnia($name, $value, $label, $attrs, 'time');
		} else {
			$this->addHmeromhnia($name, $value, $label, $attrs, 'time');
		}
	}


	/************************************/
	/* ADD DATE OR DATETIME TEXT FIELDS */
	/************************************/
	private function addHmeromhnia($name, $value='', $label='', $attrs=array(), $datetype='date') {
		$attributes = array(
			'id' => $this->options['idprefix'].$name,
			'class' => 'elx5_text',
			'dir' => 'ltr',
			'readonly' => 'readonly'
		);
		if ($datetype == 'datetime') {
			$formatitem = 'dateTimeFormat';
			$attributes['data-field'] = 'datetime';
			$attributes['data-format'] = $this->options['datetime_format'];
		} else if ($datetype == 'time') {
			$formatitem = 'timeFormat';
			$attributes['data-field'] = 'time';
			$attributes['data-format'] = $this->options['time_format'];
		} else {
			$formatitem = 'dateFormat';
			$attributes['data-field'] = 'date';
			$attributes['data-format'] = $this->options['date_format'];
		}

		$tip = '';
		$onlyelement = 0;//Elxis 5.1
		if ($attrs) {
			foreach ($attrs as $k => $v) {
				if ($k == 'tip') {
					$tip = $v;
					continue;
				}
				if (($k == 'format') || ($k == 'data-format')) {
					$attributes['data-format'] = $v;
					continue;
				}
				if ($k == 'onlyelement') { $onlyelement = (int)$v; continue; }
				$attributes[$k] = $v;
			}
		}

		$elxis_date_format = $attributes['data-format'];
		$attributes['data-format'] = $this->elxisTodatePickerFormat($elxis_date_format);
		if (!isset($attributes['title'])) { $attributes['title'] = $label.' ('.$attributes['data-format'].')'; }
		if (!isset($attributes['placeholder'])) { $attributes['placeholder'] = $attributes['data-format']; }
		if ($onlyelement == 1) {
			if (!isset($attributes['title'])) { $attributes['title'] = $label; }
		}

		$dtv = str_replace('/', '-', $value);
		if ($value == '') {
			$datevalue = '';
		} else if ((strpos($dtv, '1970-01-01') !== false) || (strpos($dtv, '01-01-1970') !== false) || (strpos($dtv, '2060-01-01') !== false) || (strpos($dtv, '01-01-2060') !== false)) {
			$datevalue = '';
		} else {
			$dtformat = str_replace('/', '-', $elxis_date_format);
			$datevalue = $this->getDateTimeValue($datetype, $value, $dtformat);
		}
		$lang = (file_exists(ELXIS_PATH.'/includes/js/datetimepicker/i18n/DateTimePicker-i18n-'.$this->curlang.'.js')) ? $this->curlang : 'en';

		$html = '';
		if ($onlyelement == 0) {
			$html .= '<div class="'.$this->options['rowclass'].'">'."\n";
			$html .= '<label class="'.$this->options['labelclass'].'" for="'.$attributes['id'].'">'.$label."</label>\n";
			$html .= '<div class="'.$this->options['sideclass'].'" id="'.$attributes['id'].'_wrap">';			
		}
		$html .= '<input type="text" name="'.$name.'" value="'.$datevalue.'"';
		foreach ($attributes as $k => $v) {
			if ($v == '') { continue; }
			if ($k == 'type') { continue; }
			if ($k == 'name') { continue; }
			if ($k == 'value') { continue; }
			$html .= ' '.$k.'="'.$v.'"';
		}
		$html .= " />\n";
		$html .= '<div id="dtBox'.$attributes['id'].'"></div>';
		$html .= '<script>';
		$html .= '$(document).ready(function() { $(\'#dtBox'.$attributes['id'].'\').DateTimePicker( { parentElement: \'#'.$attributes['id'].'_wrap\', language: \''.$lang.'\', '.$formatitem.': \''.$attributes['data-format'].'\'} ); });';
		$html .= "</script>\n";
		if ($tip != '') { $html .= '<div class="'.$this->options['tipclass'].'">'.$tip."</div>\n"; }
		if ($onlyelement == 0) {
			$html .= "</div>\n";
			$html .= "</div>\n";
		}

		$this->prepareDatePicker();

		if ($this->options['returnhtml']) { return $html; }
		echo $html;
	}


	private function elxisTodatePickerFormat($elxisformat) {
		$elxisformat = str_replace('/', '-', $elxisformat);
		switch ($elxisformat) {
			case 'H:i': $format = 'HH:mm'; break;
			case 'H:i:s': $format = 'HH:mm:ss'; break;
			case 'd-m-Y': $format = 'dd-MM-yyyy'; break;
			case 'm-d-Y': $format = 'MM-dd-yyyy'; break;
			case 'Y-m-d': $format = 'yyyy-MM-dd'; break;
			case 'd-m-Y H:i:s': $format = 'dd-MM-yyyy HH:mm:ss'; break;
			case 'Y-m-d H:i:s': $format = 'yyyy-MM-dd HH:mm:ss'; break;
			case 'm-d-Y H:i:s': $format = 'MM-dd-yyyy HH:mm:ss'; break;
			case 'd-m-Y H:i': $format = 'dd-MM-yyyy HH:mm'; break;
			case 'Y-m-d H:i': $format = 'yyyy-MM-dd HH:mm'; break;
			case 'm-d-Y H:i': $format = 'MM-dd-yyyy HH:mm'; break;
			default: $format = $elxisformat; break;
		}
		return $format;
	}


	private function getDateTimeValue($datetype, $value, $format) {
		if ($value == '') { return ''; }
		if ($datetype == 'time') {
			$parts = preg_split('#\:#', $value, -1, PREG_SPLIT_NO_EMPTY);
			if (count($parts) == 3) {
				$h = (int)$parts[0];
				$i = (int)$parts[1];
				$s = (int)$parts[2];
			} else if (count($parts) == 2) {
				$h = (int)$parts[0];
				$i = (int)$parts[1];
				$s = 0;
			} else {
				$h = -1; $i = -1; $s = -1;
			}
			if (($h >= 0) && ($h < 24) && ($i >= 0) && ($i < 60) && ($s >= 0) && ($s < 60)) {
				if ($format == 'H:i') {
					$newvalue = sprintf("%02d", $h).':'.sprintf("%02d", $i);
				} else {//H:i:s
					$newvalue = sprintf("%02d", $h).':'.sprintf("%02d", $i).':'.sprintf("%02d", $s);
				}
			} else {
				$newvalue = '';
			}
			return $newvalue;
		}

		//date - datetime
		if (preg_match('#\/#', $value)) {
			$parts = preg_split('#\/#', $value, -1, PREG_SPLIT_NO_EMPTY);
		} else {
			$parts = preg_split('#\-#', $value, -1, PREG_SPLIT_NO_EMPTY);
		}
		if (!$parts || (count($parts) != 3)) { return ''; }
		$part1 = (int)$parts[0];
		$part2 = (int)$parts[1];
		$lastparts = preg_split('#[\s]#', $parts[2], -1, PREG_SPLIT_NO_EMPTY);
		if ($lastparts && (count($lastparts) == 2)) {
			$part3 = (int)$lastparts[0];
			$part_time = trim($lastparts[1]);
			if (strlen($part_time) == 5) { $part_time .= ':00'; }
		} else {
			$part3 = (int)$parts[2];
			$part_time = '12:00:00';
		}

		$ok = true;
		$h = 12; $i = 0; $s = 0;
		switch ($format) {
			case 'Y-m-d': case 'Y/m/d': $d = $part3; $m = $part2; $y = $part1; break;
			case 'd-m-Y': case 'd/m/Y': $d = $part1; $m = $part2; $y = $part3; break;
			case 'm-d-Y': case 'm/d/Y': $d = $part2; $m = $part1; $y = $part3; break;
			case 'Y-m-d H:i:s': case 'Y/m/d H:i:s': case 'Y-m-d H:i': case 'Y/m/d H:i':
				$y = $part1; $m = $part2; $d = $part3;
				$timeparts = preg_split('#\:#', $part_time, -1, PREG_SPLIT_NO_EMPTY);
				$h = (int)$timeparts[0]; $i = (int)$timeparts[1]; $s = (int)$timeparts[2];
			break;
			case 'd-m-Y H:i:s': case 'd/m/Y H:i:s': case 'd-m-Y H:i': case 'd/m/Y H:i':
				$y = $part3; $m = $part2; $d = $part1;
				$timeparts = preg_split('#\:#', $part_time, -1, PREG_SPLIT_NO_EMPTY);
				$h = (int)$timeparts[0]; $i = (int)$timeparts[1]; $s = (int)$timeparts[2];
			break;
			case 'm-d-Y H:i:s': case 'm/d/Y H:i:s': case 'm-d-Y H:i': case 'm/d/Y H:i':
				$y = $part3; $m = $part1; $d = $part2;
				$timeparts = preg_split('#\:#', $part_time, -1, PREG_SPLIT_NO_EMPTY);
				$h = (int)$timeparts[0]; $i = (int)$timeparts[1]; $s = (int)$timeparts[2];
			break;
			default: $ok = false; break;
		}
		if (!$ok) { return ''; }

		$newvalue = '';
		if (checkdate($m, $d, $y)) {
			$newvalue = $this->safe64bit_gmdate($y, $m, $d, $h, $i, $s, $datetype, $format);
		}

		return $newvalue;
	}


	/************************************/
	/* ADD MONTH DROP DOWN SELECT FIELD */
	/************************************/
	public function addMonth($name, $label='', $selected=1, $short=false, $attrs=array()) {
		$eLang = eFactory::getLang();
		$eDate = eFactory::getDate();

		$selected = (int)$selected;
		if (($selected < 1) || ($selected > 12)) { $selected = 1; }

		$short = (bool)$short;
		$options = array();
		for ($i=1; $i<13; $i++) {
			$mname = $eDate->monthName($i, $short);
			$options[] = $this->makeOption($i, $mname);
		}

		if ($this->options['returnhtml']) {
			return $this->addSelect($name, $label, $selected, $options, $attrs);
		} else {
			$this->addSelect($name, $label, $selected, $options, $attrs);
		}
	}


	/******************/
	/* START NEW TABS */
	/******************/
	public function startTabs($tabs, $opentab=-1, $attrs=array()) {
		$this->tabidx = 0;
		$this->opentab = (int)$opentab;
		if ($this->opentab == -1) {
			$this->opentab = (isset($_GET['tabopen'])) ? (int)$_GET['tabopen'] : 0;//auto get desired tab to open (Elxis 4.x style)
		}

		if (!$attrs) { $attrs = array(); }
		if (!isset($attrs['id'])) { $attrs['id'] = 'elx5_tabs'.rand(1000, 9999); }
		$this->options['tabs_id'] = $attrs['id'];

		eFactory::getDocument()->addNativeDocReady('elx5Tabs(\''.$attrs['id'].'\', \''.$this->options['tabs_open_class'].'\', \''.$this->options['tabs_content_class'].'\');');
		$html = '<ul class="'.$this->options['tabs_class'].'" id="'.$attrs['id'].'">'."\n";

		$k = 0;
		foreach ($tabs as $tab) {
			$icon = '';
			if (is_array($tab)) {
				$title = (isset($tab['title'])) ? $tab['title'] : 'Tab '.($k + 1);
				if ($this->options['tabs_use_iconclass'] == 1) {
					if (isset($tab['iconclass'])) {
						if ($tab['iconclass'] != '') { $icon = '<i class="'.$tab['iconclass'].'" aria-hidden="false"></i>'; }
					}
				} else if ($this->options['tabs_use_numeric'] == 1) {
					$icon = '<i class="elx5_tab_num" aria-hidden="false">'.($k + 1).'</i>';
				}
			} else {//string
				$title = (string)$tab;
				if ($this->options['tabs_use_numeric'] == 1) { $icon = '<i class="elx5_tab_num" aria-hidden="false">'.($k + 1).'</i>'; }
			}

			$class_str = ($k == $this->opentab) ? ' class="'.$this->options['tabs_open_class'].'"' : '';
			$html .= "\t".'<li><a href="javascript:void(null);" data-tab="tab_elx5_'.$k.'"'.$class_str.'>';
			$html .= ($icon != '') ? $icon.'<span class="elx5_lmobhide">'.$title.'</span>' : $title;
			$html .= "</a></li>\n";
			$k++;
		}

		$html .= "</ul>\n";
		$html .= '<div class="'.$this->options['tabs_container_class'].'">'."\n";

		if ($this->options['returnhtml']) {
			return $html;
		} else {
			echo $html;
		}
	}


	/************/
	/* END TABS */
	/************/
	public function endTabs() {
		$html = '<input type="hidden" name="tabopen" id="tabopen'.$this->options['tabs_id'].'" value="'.$this->opentab.'" />'."\n";
		$html .= "</div>\n";//tabs_container_class
		$this->tabidx = 0;
		$this->opentab = 0;

		if ($this->options['returnhtml']) {
			return $html;
		} else {
			echo $html;
		}
	}


	/*************/
	/* OPEN TAB */
	/*************/
	public function openTab() {
		$class = ($this->tabidx == $this->opentab) ? $this->options['tabs_content_class'] : 'elx5_invisible';
		$html = '<div id="tab_elx5_'.$this->tabidx.'" class="'.$class.'">'."\n";
		$this->tabidx++;
		if ($this->options['returnhtml']) {
			return $html;
		} else {
			echo $html;
		}
	}


	/*************/
	/* CLOSE TAB */
	/*************/
	public function closeTab() {
		if ($this->options['returnhtml']) {
			return "</div>\n";//tabs_content_class
		} else {
			echo "</div>\n";//tabs_content_class
		}
	}


	/**************************************/
	/* OPEN ROW (ELXIS 4.x compatibility) */
	/**************************************/
	public function openRow() {
		if ($this->options['returnhtml']) {
			return '';
		} else {
			echo '';
		}
	}


	/********************************************/
	/* CLOSE OPEN ROW (ELXIS 4.x compatibility) */
	/********************************************/
	public function closeRow() {
		if ($this->options['returnhtml']) {
			return '';
		} else {
			echo '';
		}
	}


	/****************************/
	/* GET ELEMENT TRANSLATIONS */
	/****************************/
	private function getTranslations($ctg, $elem, $elid) {
		$db = eFactory::getDB();

		$sql = "SELECT ".$db->quoteId('language').", ".$db->quoteId('translation')." FROM ".$db->quoteId('#__translations')
		."\n WHERE ".$db->quoteId('category')." = :xcat AND ".$db->quoteId('element')." = :xelem AND ".$db->quoteId('elid')." = :xid";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':xcat', $ctg, PDO::PARAM_STR);
		$stmt->bindParam(':xelem', $elem, PDO::PARAM_STR);
		$stmt->bindParam(':xid', $elid, PDO::PARAM_INT);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$translations = array();
		if ($rows) {
			foreach ($rows as $row) {
				$lng = $row['language'];
				$translations[$lng] = $row['translation'];
			}
		}

		return $translations;
	}


	/****************************************/
	/* PREPARE MULTILINGUAL CONTENT SUPPORT */
	/****************************************/
	private function prepareMultiLinguism() {
		if (defined('ELXIS_MULTILINGUISM_OK')) { return; }

		$elxis = eFactory::getElxis();
		$eDoc = eFactory::getDocument();
		$eLang = eFactory::getLang();

		$eDoc->addStyleLink($elxis->secureBase().'/includes/libraries/elxis/language/mlflags'.$eLang->getinfo('RTLSFX').'.css');
		define('ELXIS_MULTILINGUISM_OK', 1);
	}


	/******************************/
	/* PREPARE DATEPICKER SUPPORT */
	/******************************/
	private function prepareDatePicker() {
		if (defined('ELXIS_DTPICKER')) { return; }

		$eDoc = eFactory::getDocument();
		$caldir = eFactory::getElxis()->secureBase(true).'/includes/js/datetimepicker';
		$eDoc->addJQuery();
		$eDoc->addLibrary('datetimepicker', $caldir.'/DateTimePicker.min.js', '0.1.29');
		$eDoc->addScriptLink($caldir.'/i18n/DateTimePicker-i18n.js');
		$eDoc->addStyleLink($caldir.'/DateTimePicker.min.css', 'text/css', 'all');
		define('ELXIS_DTPICKER', 1);
	}


	/*******************************************/
	/* 64BIT SAFE FORMAT GMDATE FOR DATE FIELD */
	/*******************************************/
	private function safe64bit_gmdate($y, $m, $d, $h=12, $i=0, $s=0, $datetype='date', $format='') {
		if ($format == '') {
			if ($datetype == 'datetime') {
				$format = $this->datetime_format;
			} elseif ($datetype == 'time') {
				$format = $this->time_format;
			} else {//date
				$format = $this->date_format;
			}
		}

		if ($y < 2038) {
			return gmdate($format, gmmktime($h, $i, $s, $m, $d, $y));
		}
		if ($this->php64bit === -1) {
			$int = "9223372036854775807";
			$int = intval($int);
			$this->php64bit = ($int == 9223372036854775807) ? 1 : 0;
		}

		if ($this->php64bit === 1) {
			return gmdate($format, gmmktime($h, $i, $s, $m, $d, $y));
		}

		$d = sprintf("%02d", $d);
		$m = sprintf("%02d", $m);
		$h = sprintf("%02d", $h);
		$i = sprintf("%02d", $i);
		$s = sprintf("%02d", $s);
		switch ($format) {
			case 'H:i': return $h.':'.$i; break;
			case 'H:i:s': return $h.':'.$i.':'.$s; break;
			case 'm/d/Y': return $m.'/'.$d.'/'.$y; break;
			case 'm-d-Y': return $m.'-'.$d.'-'.$y; break;
			case 'Y/m/d': return $y.'/'.$m.'/'.$d; break;
			case 'Y-m-d': return $y.'-'.$m.'-'.$d; break;
			case 'd/m/Y': return $d.'/'.$m.'/'.$y; break;
			case 'd-m-Y': return $d.'-'.$m.'-'.$y; break;
			case 'm/d/Y H:i:s': return $m.'/'.$d.'/'.$y.' '.$h.':'.$i.':'.$s; break;
			case 'm-d-Y H:i:s': return $m.'-'.$d.'-'.$y.' '.$h.':'.$i.':'.$s; break;
			case 'Y/m/d H:i:s': return $y.'/'.$m.'/'.$d.' '.$h.':'.$i.':'.$s; break;
			case 'Y-m-d H:i:s': return $y.'-'.$m.'-'.$d.' '.$h.':'.$i.':'.$s; break;
			case 'd/m/Y H:i:s': return $d.'/'.$m.'/'.$y.' '.$h.':'.$i.':'.$s; break;
			case 'd-m-Y H:i:s': return $d.'-'.$m.'-'.$y.' '.$h.':'.$i.':'.$s; break;
			case 'm/d/Y H:i': return $m.'/'.$d.'/'.$y.' '.$h.':'.$i; break;
			case 'm-d-Y H:i': return $m.'-'.$d.'-'.$y.' '.$h.':'.$i; break;
			case 'Y/m/d H:i': return $y.'/'.$m.'/'.$d.' '.$h.':'.$i; break;
			case 'Y-m-d H:i': return $y.'-'.$m.'-'.$d.' '.$h.':'.$i; break;
			case 'd/m/Y H:i': return $d.'/'.$m.'/'.$y.' '.$h.':'.$i; break;
			case 'd-m-Y H:i': return $d.'-'.$m.'-'.$y.' '.$h.':'.$i; break;
			default: return $y.'-'.$m.'-'.$d.' '.$h.':'.$i.':'.$s; break;
		}
	}


	/*********************/
	/* ADD CAPTCHA FIELD */
	/*********************/
	public function addCaptcha($name, $label='', $attrs=array()) {
		$v1 = rand(4, 30);
		$v2 = rand(3, 29);
		if ($v1 % 2) {
			$operator = '+';
			$number1 = $v1;
			$number2 = $v2;
			$sum = $number1 + $number2;
		} else {
			$operator = '-';
			if ($v1 == $v2) {
				$number1 = $v1 + rand(6, 21);
				$number2 = $v2;
			} else if ($v1 > $v2) {
				if (($v1 - $v2) < 6) { $v1 = $v1 + rand(5, 20); }
				$number1 = $v1;
				$number2 = $v2;
			} else {
				$number1 = $v1 + rand(5, 20);
				$number2 = $v1;
			}
			$sum = $number1 - $number2;
		}

		eFactory::getSession()->set('captcha_'.$name, $sum);

		$sidetext = $number1.' '.$operator.' '.$number2.' =';

		if (trim($label == '')) { $label = eFactory::getLang()->get('SECURITY_CODE'); }

		$attributes = array(
			'id' => $this->options['idprefix'].$name,
			'class' => 'elx5_text',
			'dir' => 'ltr',
			'placeholder' => $label,
			'maxlength' => 5,
			'required' => 'required'
		);

		$tip = '';
		$onlyelement = 0;//Elxis 5.1
		if ($attrs) {
			foreach ($attrs as $k => $v) {
				if ($k == 'tip') { $tip = $v; continue; }
				if ($k == 'onlyelement') { $onlyelement = (int)$v; continue; }
				$attributes[$k] = $v;
			}
		}
		if ($onlyelement == 1) {
			if (!isset($attributes['title'])) { $attributes['title'] = $label; }
		}

		$html = '';
		if ($onlyelement == 0) {
			$html .= '<div class="'.$this->options['rowclass'].'">'."\n";
			$html .= '<label class="'.$this->options['labelclass'].'" for="'.$attributes['id'].'">'.$label."</label>\n";
			$html .= '<div class="'.$this->options['sideclass'].'">';			
		}

		$html .= '<div class="elx5_sideinput_wrap">'."\n";
		$html .= '<div class="elx5_sideinput_text">'.$sidetext.'</div>'."\n";
		$html .= '<div class="elx5_sideinput_input">'."\n";

		$html .= '<input type="text" name="'.$name.'" value=""';
		foreach ($attributes as $k => $v) {
			if ($v == '') { continue; }
			if ($k == 'type') { continue; }
			if ($k == 'name') { continue; }
			if ($k == 'value') { continue; }
			$html .= ' '.$k.'="'.$v.'"';
		}
		$html .= " />\n";
		$html .= "</div></div>\n";
		if ($tip != '') { $html .= '<div class="'.$this->options['tipclass'].'">'.$tip."</div>\n"; }
		if ($onlyelement == 0) {
			$html .= "</div>\n";
			$html .= "</div>\n";
		}

		if ($this->options['returnhtml']) { return $html; }
		echo $html;
	}


	/****************************************/
	/* ADD "I AM NOT A ROBOT" CAPTCHA FIELD */
	/****************************************/
	public function addNoRobot($name='', $emptylabel=true) {
		if ($name == '') { $name = 'norobot'; }
		$attributes = array('id' => $this->options['idprefix'].$name);
		if (defined('ELXIS_ADMIN')) {
			$linkbase = eFactory::getElxis()->makeAURL('cpanel:/', 'inner.php', true);
		} else {
			$linkbase = eFactory::getElxis()->makeURL('content:/', 'inner.php', true);
		}
		eFactory::getDocument()->addFontAwesome();

		$html = '';
		if ($emptylabel) {
			$html .= '<div class="'.$this->options['rowclass'].'">'."\n";
			$html .= '<label class="'.$this->options['labelclass'].'">&#160;'."</label>\n";
			$html .= '<div class="'.$this->options['sideclass'].'">';
		}
		$html .= '<div class="elxnorobot"><a href="javascript:void(null);" onclick="elxIamNotRobot(\''.$attributes['id'].'\');" class="elxanorobot"><span id="'.$attributes['id'].'box" class="elxnorobotbox">&#160;</span> '.eFactory::getLang()->get('IAMNOTA_ROBOT').'</a></div>'."\n";
		$html .= '<input type="hidden" name="'.$name.'" id="'.$attributes['id'].'" value="" data-genbase="'.$linkbase.'" dir="ltr" />'."\n";
		if ($emptylabel) {
			$html .= "</div></div>\n";
		}

		if ($this->options['returnhtml']) { return $html; }
		echo $html;
	}


	/***********************/
	/* ADD CHECK BOX FIELD */
	/***********************/
	public function addCheckbox($name, $label='', $checked=null, $options=array(), $attrs=array()) {
		$checked = (is_array($checked)) ? $checked : array((string)$checked);
		$vertical_options = 0;
		$attributes = array('id' => $this->options['idprefix'].$name);
		$tip = '';
		$onlyelement = 0;//Elxis 5.1

		if ($attrs) {
			foreach ($attrs as $k => $v) {
				if ($k == 'vertical_options') { $vertical_options = (int)$v; continue; }
				if ($k == 'tip') { $tip = $v; continue; }
				if ($k == 'onlyelement') { $onlyelement = (int)$v; continue; }
				$attributes[$k] = $v;
			}
		}

		$html = '';
		if ($onlyelement == 0) {
			$html .= '<div class="'.$this->options['rowclass'].'">'."\n";
			$html .= '<label class="'.$this->options['labelclass'].'" for="'.$attributes['id'].'1">'.$label."</label>\n";
			$html .= '<div class="'.$this->options['sideclass'].'">';
		}

		if (is_array($options)) {
			if (count($options) > 0) {
				foreach ($options as $q => $option) {
					$i = $q + 1;
					$chk = (in_array($option['value'], $checked)) ? ' checked="checked"' : '';
					$attr = '';
					$optionclass = 'elx5_checkbox';
					if (is_array($option['attributes']) && (count($option['attributes']) > 0)) {
						foreach ($option['attributes'] as $key => $val) {
							if (in_array($key, array('id', 'name', 'type'))) { continue; }
							if ($key == 'class') { $optionclass = $val; continue; }
							$attr .= ' '.$key.'="'.$val.'"';
						}
					}
					$html .= '<label class="elx5_checkboxwrap">'.$option['label'].'<input type="checkbox" name="'.$name.'[]" id="'.$attributes['id'].$i.'" class="'.$optionclass.'" value="'.$option['value'].'"'.$chk.$attr.' />';
					$html .= '<span class="elx5_checkbox_checkmark"></span></label>'."\n";
				}
			}
		}
		if ($tip != '') { $html .= '<div class="'.$this->options['tipclass'].'">'.$tip."</div>\n"; }
		if ($onlyelement == 0) {
			$html .= "</div>\n";
			$html .= "</div>\n";
		}

		if ($this->options['returnhtml']) { return $html; }
		echo $html;
	}


	/****************************************************/
	/* ADD SEO SUGGEST/VALIDATE LINKS (ELXIS 4.X STYLE) */
	/****************************************************/
	public function addSEO($name, $seoname, $sugfunc='', $valfunc='', $sargs=array(), $vargs=array()) {
		$eLang = eFactory::getLang();

		if ($sugfunc == '') { $sugfunc = 'suggestSEO'; }
		if ($valfunc == '') { $valfunc = 'validateSEO'; }
		$updateid = $this->options['idprefix'].'valseo'.rand(100, 999);
		if ($name == '') { $name = 'title'; }
		if ($seoname == '') { $seoname = 'seotitle'; }
		$titleid =  $this->options['idprefix'].$name;
		$seotitleid = $this->options['idprefix'].$seoname;

		$onsuggest = $sugfunc.'(\''.$titleid.'\', \''.$seotitleid.'\', \''.$updateid.'\'';
		if (is_array($sargs) && (count($sargs) > 0)) { $onsuggest .= ', \''.implode('\', \'', $sargs).'\''; }
		$onsuggest .= ')';
		$onvalidate = $valfunc.'(\''.$seotitleid.'\', \''.$updateid.'\'';
		if (is_array($vargs) && (count($vargs) > 0)) { $onvalidate .= ', \''.implode('\', \'', $vargs).'\''; }
		$onvalidate .= ')';

		eFactory::getDocument()->addFontAwesome();

		$html = '<div class="'.$this->options['rowclass'].'">'."\n";
		$html .= '<label class="'.$this->options['labelclass'].'">&#160;'."</label>\n";
		$html .= '<div class="'.$this->options['sideclass'].'">';
		$html .= '<a href="javascript:void(null);" onclick="'.$onsuggest.'" class="elx5_suggest"><i class="fas fa-cog" id="'.$updateid.'sug"></i> '.$eLang->get('SUGGESTED')."</a>\n";
		$html .= '<a href="javascript:void(null);" onclick="'.$onvalidate.'" class="elx5_validate"><i class="fas fa-check" id="'.$updateid.'val"></i> '.$eLang->get('VALIDATE')."</a>\n";
		$html .= '<div id="'.$updateid.'" class="elx5_invisible"></div>'."\n";
		$html .= "</div></div>\n";

		if ($this->options['returnhtml']) { return $html; }
		echo $html;
	}


	/****************************************************/
	/* ADD SEO SUGGEST/VALIDATE LINKS (ELXIS 5.X STYLE) */
	/****************************************************/
	public function add5SEO($name, $seoname, $idname, $svbaseurl) {
		$eLang = eFactory::getLang();

		$id =  $this->options['idprefix'].$idname;
		$titleid =  $this->options['idprefix'].$name;
		$seotitleid = $this->options['idprefix'].$seoname;

		$updateid = $this->options['idprefix'].'valseo'.rand(100, 999);

		eFactory::getDocument()->addFontAwesome();

		$html = '<div class="'.$this->options['rowclass'].'">'."\n";
		$html .= '<label class="'.$this->options['labelclass'].'">&#160;'."</label>\n";
		$html .= '<div class="'.$this->options['sideclass'].'">';
		$html .= '<a href="javascript:void(null);" onclick="elx5SuggestSEO(\''.$titleid.'\', \''.$seotitleid.'\', \''.$id.'\', \''.$updateid.'\', \''.$svbaseurl.'\');" class="elx5_suggest"><i class="fas fa-cog" id="'.$updateid.'sug"></i> '.$eLang->get('SUGGESTED')."</a>\n";
		$html .= '<a href="javascript:void(null);" onclick="elx5ValidateSEO(\''.$seotitleid.'\', \''.$id.'\', \''.$updateid.'\', \''.$svbaseurl.'\');" class="elx5_validate" id="'.$updateid.'vallink"><i class="fas fa-check" id="'.$updateid.'val"></i> '.$eLang->get('VALIDATE')."</a>\n";
		$html .= '<div id="'.$updateid.'" class="elx5_invisible"></div>'."\n";
		$html .= "</div></div>\n";

		if ($this->options['returnhtml']) { return $html; }
		echo $html;
	}


	/***********************************/
	/* ADD MULTILINGUAL TEXTAREA FIELD */
	/***********************************/
	public function addMLTextarea($name, $trdata, $value='', $label='', $attrs=array()) {
		$elxis = eFactory::getElxis();

		$allowed = ($elxis->acl()->check('component', 'com_etranslator', 'manage') < 1) ? false : true;
		if (!$allowed && !defined('ELXIS_ADMIN')) {
			$allowed = ($elxis->acl()->check('component', 'com_etranslator', 'api') < 1) ? false : true;
		}

		$editor = '';
		$contentslang = '';
		$editoroptions = array();
		$has_translations = false;
		$translations = array();
		$attributes = array('id' => $this->options['idprefix'].$name, 'placeholder' => $label);
		$tip = '';
		$onlyelement = 0;//Elxis 5.1
		if ($attrs) {
			foreach ($attrs as $k => $v) {
				if ($k == 'tip') {
					$tip = $v;
					continue;
				}
				if ($k == 'id') { continue; }
				if ($k == 'class') { continue; }
				if ($k == 'dir') { continue; }
				if ($k == 'translations') {//multilingual XML parameter, no trdata in this case but provided translations in $attrs
					$has_translations = true;
					$translations = $v;
					continue;
				}
				if ($k == 'editor') {
					$editor = trim($v);//html
					continue;
				}
				if ($k == 'contentslang') {//TODO: USE IN JODIT?
					$contentslang = (string)$v;
					continue;
				}
				if ($k == 'editoroptions') {
					$editoroptions = is_array($v) ? $v : array();
					continue;
				}
				if ($k == 'onlyelement') { $onlyelement = (int)$v; continue; }
				$attributes[$k] = $v;
			}
		}
		if ($onlyelement == 1) {
			if (!isset($attributes['title'])) { $attributes['title'] = $label; }
		}

		$sitelangs = $this->getSiteLangs();
		$langnames = $this->getSiteLangs() ? array_keys($sitelangs) : array();
		$clang = $elxis->getConfig('LANG');
		if (!$has_translations) {
			$translations = $this->getTranslations($trdata['category'], $trdata['element'], $trdata['elid']);
		}

		$editor_js = '';
		if ($editor == 'html') {
			$tip = ''; //disable tips for rich text editor
			$elxeditor = $elxis->obj('editor');
			$elxeditor->prepare($attributes['id'], $editor, $contentslang, $editoroptions);
			$editor_js = $elxeditor->getJS();
			$value = htmlspecialchars($value);
			unset($elxeditor);
		}

		$this->prepareMultiLinguism();

		$distxt = '';
		if (!$allowed) { $distxt = ' disabled="disabled"'; }

		$html = '';
		if ($onlyelement == 0) {
			$html .= '<div class="'.$this->options['rowclass'].'">'."\n";
			$html .= '<label class="'.$this->options['labelclass'].'" for="'.$attributes['id'].'">'.$label."</label>\n";
			$html .= '<div class="'.$this->options['sideclass'].'">';
		}

		if ($editor == 'html') {
			$html .= '<div class="elx5_mlboxlangeditor">'."\n";
		} else {
			$html .= '<div class="elx5_mlboxwrap">'."\n";
			$html .= '<div class="elx5_mlboxlang">'."\n";
		}

		$is_editor = ($editor == 'html') ? 1 : 0;
		$html .= '<select name="'.$name.'_lang" id="'.$attributes['id'].'_lang" class="elx5_select elx5_mlflag'.$clang.'" dir="ltr" data-deflang="'.$clang.'" data-trelement="'.$name.'" data-sitelangs="'.implode(',', $langnames).'" onchange="elx5MLSwitch(\''.$this->options['idprefix'].'\', \''.$name.'\', '.$is_editor.');"'.$distxt.'>'."\n";
		if ($sitelangs) {
			foreach ($sitelangs as $lng => $sitelang) {
				$sel = '';
				$oclass = '';
				if ($lng == $clang) {
					$sel = ' selected="selected"';
					$oclass = ' class="elx5_defoption"';
				} else {
					if (isset($translations[$lng])) { $oclass = ' class="elx5_hloption"'; }
				}
				$html .= '<option value="'.$lng.'"'.$oclass.$sel.'>'.strtoupper($lng)."</option>\n";
			}
		}
		$html .= "</select>\n";

		$html .= "</div>\n";//elx5_mlboxlang / elx5_mlboxlangeditor
		if ($editor != 'html') {
			$html .= '<div class="elx5_mlboxtext">'."\n";
		}
		if ($sitelangs) {
			$trname = $name.'_';
			$trname_end = '';
			if (preg_match('@(])$@', $name)) {//XML param or other array style naming like "something[else]" => "something[else_mlLNG]"
				$trname = preg_replace('@(])$@', '', $name).'_ml';
				$trname_end = ']';
			}
			foreach ($sitelangs as $lng => $sitelang) {
				if ($lng == $clang) { continue; }
				$dir = ($sitelang['RTLSFX'] == '-rtl') ? 'rtl' : 'ltr';
				$v = isset($translations[$lng]) ? $translations[$lng] : '';
				$html .= '<textarea name="'.$trname.$lng.$trname_end.'" id="'.$attributes['id'].'_'.$lng.'" dir="'.$dir.'" class="elx5_invisible" placeholder="'.$label.' - '.$sitelang['NAME_ENG'].'">'.$v.'</textarea>'."\n";
			}
		}
		$dir = ($sitelangs[$clang]['RTLSFX'] == '-rtl') ? 'rtl' : 'ltr';

		$html .= '<textarea name="'.$name.'" id="'.$attributes['id'].'" dir="'.$dir.'" class="elx5_textarea elx5_mlflag'.$clang.'"';
		foreach ($attributes as $k => $v) {
			if ($v == '') { continue; }
			if ($k == 'id') { continue; }
			if ($k == 'name') { continue; }
			if ($k == 'dir') { continue; }
			if ($k == 'class') { continue; }
			if ($k == 'forcedir') { continue; }
			$html .= ' '.$k.'="'.$v.'"';
		}
		$html .= ">".$value."</textarea>\n";
		if ($editor != 'html') {
			$html .= "</div>\n";//elx5_mlboxtext
			$html .= "</div>\n";//elx5_mlboxwrap
		}
		if ($tip != '') { $html .= '<div class="'.$this->options['tipclass'].'">'.$tip."</div>\n"; }
		if ($onlyelement == 0) { $html .= "</div>\n</div>\n"; }
		$html .= $editor_js;

		if ($this->options['returnhtml']) { return $html; }
		echo $html;
	}


	/********************************************************************************/
	/* ADD COMBINED FORM ELEMENTS (GENERATED WITH "onlyelement" option) - Elxis 5.1 */
	/********************************************************************************/
	public function addCombined($name, $label, $combined_html, $attrs=array()) {
		$attributes = array(
			'id' => $this->options['idprefix'].$name,
		);
		$tip = '';
		if ($attrs) {
			foreach ($attrs as $k => $v) {
				if ($k == 'tip') { $tip = $v; continue; }
				if ($k == 'id') { $attributes['id'] = $v; continue; }
			}
		}

		$html = '<div class="'.$this->options['rowclass'].'">'."\n";
		$html .= '<label class="'.$this->options['labelclass'].'" for="'.$attributes['id'].'">'.$label."</label>\n";
		$html .= '<div class="'.$this->options['sideclass'].'">';
		$html .= $combined_html;
		if ($tip != '') { $html .= '<div class="'.$this->options['tipclass'].'">'.$tip."</div>\n"; }
		$html .= "</div>\n";
		$html .= "</div>\n";

		if ($this->options['returnhtml']) { return $html; }
		echo $html;
	}

}

?>