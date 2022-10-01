<?php 
/**
* @version		$Id: common.php 2363 2020-12-06 16:42:39Z IOS $
* @package		Elxis
* @subpackage	Component eMedia
* @copyright	Copyright (c) 2006-2021 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class emediaController {

	protected $view = null;
	protected $model = null;
	protected $relpath = 'media/';
	protected $max_upload_size = 3145728; //3mb
	protected $theme = 'default';
	protected $defviewmode = 'grid';
	protected $tree_show_files = 0;
	protected $connector;
	protected $is_editor = false;


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	protected function __construct($view=null, $is_editor=false) {
		$this->view = $view;
		$this->is_editor = (bool)$is_editor;
		if (defined('ELXIS_MULTISITE')) {
			if (ELXIS_MULTISITE > 1) {
				$this->relpath = 'media/images/site'.ELXIS_MULTISITE.'/';
			}
		}

		$this->loadParameters();
	}


	/****************************/
	/* GET COMPONENT PARAMETERS */
	/****************************/
	private function loadParameters() {
		$db = eFactory::getDB();

		$sql = "SELECT ".$db->quoteId('params')." FROM ".$db->quoteId('#__components')." WHERE ".$db->quoteId('component')." = ".$db->quote('com_emedia');
		$stmt = $db->prepareLimit($sql, 0, 1);
		$stmt->execute();
		$params_str = (string)$stmt->fetchResult();
		elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
		$params = new elxisParameters($params_str, '', 'component');

		$this->tree_show_files = (int)$params->get('tree_show_files', 0);
		$val = trim($params->get('max_upload_size', '5.0'));
		if (($val == '') || !is_numeric($val)) { $val = 5.0; }
		if ($val <= 0) { $val = 5.0; }
		$max_upload_size = round($val, 1);
		$this->max_upload_size = $max_upload_size * 1048576;

		$val = trim($params->get('theme', 'default'));
		if (($val != '') && file_exists(ELXIS_PATH.'/components/com_emedia/themes/'.$val.'/')) { $this->theme = $val; }
		$val = trim($params->get('defviewmode', 'grid'));
		if ($val != '') { $this->defviewmode = $val; }
	}


	/***********************************/
	/* IMPORT STYLE SHEETS TO DOCUMENT */
	/***********************************/
	protected function importCSS() {
		$eDoc = eFactory::getDocument();

		$baseurl = eFactory::getElxis()->secureBase().'/components/com_emedia';
		if ($this->is_editor) {
			$eDoc->addStyleLink($baseurl.'/styles/reset.css');
		}
		$eDoc->addStyleLink($baseurl.'/scripts/jquery.filetree/jqueryFileTree.css');
		$eDoc->addStyleLink($baseurl.'/scripts/jquery.contextmenu/jquery.contextMenu-1.01.css');
		$eDoc->addStyleLink($baseurl.'/scripts/custom-scrollbar-plugin/jquery.mCustomScrollbar.min.css');
	}


	/***************************************/
	/* IMPORT JAVASCRIPT FILES TO DOCUMENT */
	/***************************************/
	protected function importJS() {
		$elxis = eFactory::getElxis();
		$eDoc = eFactory::getDocument();

		$baseurl = $elxis->secureBase().'/components/com_emedia';
		$configurl = $elxis->makeAURL('emedia:config', 'inner.php');
		$configurl .= ($this->is_editor) ? '?editor=1' : '?editor=0';

		$eDoc->addJQuery();
		$eDoc->addScriptLink($baseurl.'/scripts/jquery-browser.js');
		$eDoc->addScriptLink($baseurl.'/scripts/jquery.form-3.24.js');
		$eDoc->addScriptLink($baseurl.'/scripts/jquery.splitter/jquery.splitter-1.5.1.js');
		$eDoc->addScriptLink($baseurl.'/scripts/jquery.filetree/jqueryFileTree.js');
		$eDoc->addScriptLink($baseurl.'/scripts/jquery.contextmenu/jquery.contextMenu-1.01.js');
		$eDoc->addScriptLink($baseurl.'/scripts/jquery.impromptu-3.2.min.js');
		$eDoc->addScriptLink($baseurl.'/scripts/jquery.tablesorter-2.7.2.min.js');
		$eDoc->addScriptLink($configurl);
		$eDoc->addScriptLink($baseurl.'/scripts/filemanager.js');
	}


	/******************************************/
	/* ECHO PAGE HEADERS FOR SPECIAL REQUESTS */
	/******************************************/
	protected function pageHeaders($type='text/html') {
		if(ob_get_length() > 0) { ob_end_clean(); }
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').'GMT');
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header('Content-type: '.$type.'; charset=utf-8');
	}


	/***************************/
	/* ALLOWED FILE EXTENSIONS */
	/***************************/
	protected function allowedExtensions() {
		$exts = array(
			'png', 'jpg', 'jpeg', 'gif', 'ico', 'svg', 'psd', 'bmp', 'tiff', 'tif', 
			'mp3', 'ogg', 'ogv', 'avi', 'mpg', 'mpeg', 'wma', 'wmv', 'mkv', 'aac', 'mp4', 'webm', 'm4v', 
			'mpa', '3gp', 'asf', 'asx', 'mov', 'rm', 'ra', 'm4a', 'mid', 'wav', 'flv', 'swf', 
			'doc', 'docx', 'pps', 'ppt', 'pptx', 'smil', 'xlsx', 'xls', 'csv', 'odt', 'odp', 'odf', 'ods', 'rtf', 'pdf', 'txt', 'srt', 'vtt', 
			'xsl', 'xslt', 'css', 'xml', 
			'zip', 'rar', 'tar', 'gz', 'bzip2', 'gzip'
		);

		return $exts;
	}

}

?>