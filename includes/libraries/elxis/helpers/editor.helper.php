<?php 
/**
* @version		$Id: editor.helper.php 2341 2020-03-05 17:32:58Z IOS $
* @package		Elxis
* @subpackage	Helpers / Editor
* @copyright	Copyright (c) 2006-2020 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisEditorHelper {

	private $editor_id = 'editor1';
	private $type = 'html';
	private $contentsLang = 'en';
	private $contentsDir = 'ltr';
	private $options = array();


	/***************/
	/* CONSTRUCTOR */
	/***************/
	public function __construct() {
	}
	

	/************************/
	/* SET AN EDITOR OPTION */
	/************************/
	public function setOption($option, $value) {
		$this->options[$option] = $value;
	}


	/***************************************/
	/* SET EDITOR MULTIPLE OPTIONS AT ONCE */
	/***************************************/
	public function setOptions($options) {
		if (is_array($options) && (count($options) > 0)) {
			foreach ($options as $option => $value) {
				$this->setOption($option, $value);
			}
		}
	}


	/*****************************/
	/* PREPARE EDITOR ENVIROMENT */
	/*****************************/
	public function prepare($editor_id, $type='html', $clang='', $custom_options=array()) {
		$elxis = eFactory::getElxis();
		$eDoc = eFactory::getDocument();

		if (!is_array($custom_options)) { $custom_options = array(); }
		$this->editor_id = $editor_id;
		$type = 'html';//"bbcode" and "text" are no longer supported (Elxis 5.0+)
		$this->type = $type;

		$removebuttons = array();
		$removebuttons[] = 'print';
		$removebuttons[] = 'about';
		$alevel = $elxis->acl()->getLevel();
		if ($alevel < 3) {
			$removebuttons[] = 'image';
			$removebuttons[] = 'video';
			$removebuttons[] = 'source';
		}
		if ($alevel < 70) {
			$removebuttons[] = 'file';
			$removebuttons[] = 'elxisplugin';
		}

		$this->options = array();

		$clang = trim($clang);
		if (($clang == '') || !file_exists(ELXIS_PATH.'/language/'.$clang.'/'.$clang.'.php')) { $clang = $elxis->getConfig('LANG'); }
		$this->contentsLang = $clang;
		$this->contentsDir = 'ltr';
		$ilangs = eFactory::getLang()->getAllLangs(true);
		if (isset($ilangs[$clang])) { $this->contentsDir = $ilangs[$clang]['DIR']; }

		$this->options['direction'] = $this->contentsDir;
		$this->options['language'] = 'auto';
		$this->options['zIndex'] = '1031';
		$this->options['height'] = '400';
		$this->options['toolbarAdaptive'] = 'false';
		$this->options['removeButtons'] = '[\''.implode('\', \'', $removebuttons).'\']';

		if ($custom_options) {
			if (!isset($custom_options['language'])) {
				$custom_options['language'] = eFactory::getLang()->currentLang();
			}
		} else {
			$custom_options['language'] = eFactory::getLang()->currentLang();
		}
		if ($custom_options['language'] == 'zh') { $custom_options['language'] = 'zh_cn'; }
		if ($custom_options['language'] == 'zt') { $custom_options['language'] = 'zh_cn'; }
		if ($custom_options['language'] == 'pt') { $custom_options['language'] = 'pt_br'; }

		$custom_lang_toload = '';
		if (!in_array($custom_options['language'], array('de', 'fr', 'ru', 'es', 'nl', 'hu', 'pt_br', 'en', 'tr', 'ar', 'zh_cn'))) {//built-in languages
			if (!file_exists(ELXIS_PATH.'/includes/js/jodit/lang/'.$custom_options['language'].'.js')) {
				$custom_options['language'] = 'auto';
			} else {
				$custom_lang_toload = $custom_options['language'];
			}
		}
		unset($ilangs);

		if (isset($custom_options['forcedir'])) {
			if ($custom_options['forcedir'] != '') { $this->options['direction'] = $custom_options['forcedir']; }
			unset($custom_options['forcedir']);
		}

		foreach ($custom_options as $k => $v) {//old 4.x/ckeditor config options
			if (in_array($k, array('contentsCss', 'skin', 'entities_greek', 'entities_latin', 'filebrowserBrowseUrl', 'filebrowserImageWindowWidth', 'filebrowserImageWindowHeight', 'editor', 'contentslang'))) {
				unset($custom_options[$k]);
			}
		}

		$this->setOptions($custom_options);

		$baselink = $elxis->secureBase().'/includes/js/jodit/';
		$eDoc->addStyleLink($baselink.'jodit.min.css');
		if ($custom_lang_toload != '') {
			$eDoc->addScriptLink($baselink.'lang/'.$custom_lang_toload.'.js');
		}
		$eDoc->addLibrary('jodit', $baselink.'jodit.min.js', '3.3.24');
	}


	/****************************/
	/* GET EDITOR INSTANCE HTML */
	/****************************/
	public function editor($name, $value='', $attributes=array()) {
		if (!is_array($attributes)) { $attributes = array(); }
		$attributes['dir'] = $this->contentsDir;
		if (!isset($attributes['class'])) { $attributes['class'] = 'elx5_textarea'; }
		if (!isset($attributes['cols'])) { $attributes['cols'] = 80; } else { $attributes['cols'] = (int)$attributes['cols']; }
		if (!isset($attributes['rows'])) { $attributes['rows'] = 8; } else { $attributes['rows'] = (int)$attributes['rows']; }

		$attr = '';
		foreach ($attributes as $key => $val) { $attr .= ' '.$key.' = "'.$val.'"'; }

		$out = '<textarea name="'.$name.'" id="'.$this->editor_id.'"'.$attr.'>'.htmlspecialchars($value)."</textarea>\n";
		$out .= $this->getJS();

		return $out;
	}


	/***************************************/
	/* MAKE AND RETURN REQUIRED JAVASCRIPT */
	/***************************************/
	public function getJS() {
		$elxis = eFactory::getElxis();

		$js = '<script>'."\n";
		$js .= 'var ed5'.$this->editor_id.' = new Jodit(\'#'.$this->editor_id.'\', {'."\n";
		if (count($this->options) > 0) {
			foreach ($this->options as $option => $value) {
				if (($value == 'true') || ($value == 'false')) {
					$v = $value;
				} else if (is_numeric($value)) {
					$v = $value;
				} else if (is_bool($value)) {
					$v = ($value === true) ? 'true' : 'false';
				} else if (strpos($value, '[') === 0) {
					$v = $value;
				} else {
					$v = '\''.$value.'\'';
				}
				$js .= $option.': '.$v.",\n";
			}
		}

		if (defined('ELXIS_ADMIN')) {
			$connectorlink = $elxis->makeAURL('emedia:editor/', 'inner.php');
			$js .= 'uploader: { url: \''.$connectorlink.'?action=fileUpload\' },'."\n";
			$js .= 'filebrowser: { ajax: { url: \''.$connectorlink.'\' } },'."\n";
			$js .= 'extraButtons: [{'."\n";
			$js .= 'name: \'elxisplugin\','."\n";
            $js .= 'iconURL: \''.$elxis->getConfig('URL').'/includes/js/jodit/elxis.png\','."\n";
			$js .= 'exec: function (editor) { '."\n";
			$js .= 'var elximportURL = \''.$elxis->makeAURL('content:plugin/', 'inner.php').'\'+\'?fn=\'+editor.element.id;'."\n";
			$js .= 'elxPopup(elximportURL, 950, 700, \'pluginhelper\', \'yes\');'."\n";
			$js .= '},'."\n";
			$js .= 'tooltip: \'Elxis plugin\','."\n";
			$js .= '}],'."\n";
		}
		$js .= '});'."\n";
		$js .= "</script>\n";
		return $js;

	}

}

?>