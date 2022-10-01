<?php 
/**
* @version		$Id: full.php 1817 2016-03-25 21:25:42Z sannosi $
* @package		Elxis
* @subpackage	Component eMedia
* @copyright	Copyright (c) 2006-2016 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class fullMediaControl extends emediaController {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null) {
		parent::__construct($view, false);
	}


	/***************************************/
	/* PREPARE FULL SCALE MEDIA MANAGER UI */
	/***************************************/
	public function fullui() {
		$eLang = eFactory::getLang();

		if (!is_dir(ELXIS_PATH.'/'.$this->relpath)) {
			if (!eFactory::getFiles()->createFolder($this->relpath)) {
				$this->view->fatalError('Could not create required folder '.$this->relpath);
				return;
			}
		}

		$this->importCSS();
		$this->importJS();
		eFactory::getPathway()->addNode($eLang->get('MEDIA_MANAGER'));
		eFactory::getDocument()->setTitle($eLang->get('MEDIA_MANAGER').' - '.$eLang->get('ADMINISTRATION'));
		$this->view->fullUI();
	}


	/*****************************************/
	/* CONFIGURE JS MEDIA MANAGER ON-THE-FLY */
	/*****************************************/
	public function configure() {
		$elxis = eFactory::getElxis();

		$curlang = eFactory::getLang()->currentLang();
		if (file_exists(ELXIS_PATH.'/components/com_emedia/scripts/languages/'.$curlang.'.js')) {
			$lng = $curlang;
		} else {
			$lng = 'en';
		}

		$editor = 0;
		if (isset($_GET['editor'])) { $editor = (int)$_GET['editor']; }

		$browse_only = 'true';
		$can_resize = false;
		$caps = array();
		$caps[] = ($editor == 1) ? 'select' : 'download';
		if ($elxis->acl()->check('com_emedia', 'files', 'edit') > 0) {
			$can_resize = true;
			$browse_only = 'false';
			if ($editor == 1) {
				$caps[] = 'rename';
				$caps[] = 'delete';
			} else {
				$caps[] = 'rename';
				$caps[] = 'resize';
				$caps[] = 'delete';
				$caps[] = 'move';
				$caps[] = 'replace';
			}
		}
		if ($elxis->acl()->check('com_emedia', 'files', 'upload') > 0) {
			$browse_only = 'false';
		}

		$connectorurl = $elxis->makeAURL('emedia:connect', 'inner.php', true);

		$js = 'var elxisfmcfg = {'."\n";
		$js .= "\t".'"options": {'."\n";
		$js .= "\t\t".'"fileConnector": "'.$connectorurl.'",'."\n";
		$js .= "\t\t".'"elxisurl": "'.$elxis->secureBase().'",'."\n";
		$js .= "\t\t".'"elxisrelpath": "'.$this->relpath.'",'."\n";
		$js .= "\t\t".'"culture": "'.$lng.'",'."\n";
		$js .= "\t\t".'"lang": "php",'."\n";
		$js .= "\t\t".'"theme": "'.$this->theme.'",'."\n";
		$v = ($editor == 1) ? 'list' : $this->defviewmode;
		$js .= "\t\t".'"defaultViewMode": "'.$v.'",'."\n";
		$js .= "\t\t".'"autoload": true,'."\n";
		$js .= "\t\t".'"showFullPath": false,'."\n";
		$js .= "\t\t".'"showTitleAttr": false,'."\n";
		$js .= "\t\t".'"browseOnly": '.$browse_only.','."\n";
		$js .= "\t\t".'"showConfirmation": false,'."\n";
		$js .= "\t\t".'"showThumbs": true,'."\n";
		$js .= "\t\t".'"generateThumbnails": true,'."\n";
		$js .= "\t\t".'"cacheThumbnails": false,'."\n";
		$js .= "\t\t".'"searchBox": false,'."\n";
		if ($editor == 1) {
			$v = 'false';
		} else {
			$v = ($this->tree_show_files == 1) ? 'true' : 'false';
		}
		$js .= "\t\t".'"listFiles": '.$v.','."\n";
		$js .= "\t\t".'"fileSorting": "default",'."\n"; // "default", "NAME_ASC", "NAME_DESC", "TYPE_ASC", "TYPE_DESC", "MODIFIED_ASC", "MODIFIED_DESC"
		$js .= "\t\t".'"chars_only_latin": true,'."\n";
		$js .= "\t\t".'"splitterWidth": 200,'."\n";
		$js .= "\t\t".'"splitterMinWidth": 200,'."\n";
		$js .= "\t\t".'"dateFormat": "d M Y H:i",'."\n";
		$js .= "\t\t".'"serverRoot": false,'."\n";
		$js .= "\t\t".'"fileRoot": "/",'."\n";
		$js .= "\t\t".'"baseUrl": "'.$elxis->secureBase().'/'.$this->relpath.'",'."\n";
		$js .= "\t\t".'"logger": false,'."\n";
		if ($caps) { 
			$js .= "\t\t".'"capabilities": ["'.implode('", "', $caps).'"],'."\n";
		} else {
			$js .= "\t\t".'"capabilities": [],'."\n";
		}
		$js .= "\t\t".'"plugins": []'."\n";
		$js .= "\t"."},\n";

		$js .= "\t".'"security": {'."\n";
		$v = ($editor == 1) ? 'false' : 'true';
		$js .= "\t\t".'"allowFolderDownload": '.$v.','."\n";
		$js .= "\t\t".'"allowChangeExtensions": false,'."\n";
		$js .= "\t\t".'"allowNoExtension": false,'."\n";
		$js .= "\t\t".'"uploadPolicy": "DISALLOW_ALL",'."\n";
		$js .= "\t\t".'"uploadRestrictions": ["jpg", "jpeg", "gif", "png", "svg", "ico", "psd", "bmp", "tiff", "tif", "txt", "pdf", "odp", "ods", "odt", "rtf", "doc", "docx", "xls", "xlsx", "ppt", "pptx", "csv", "xml", "mkv", "wmv", "mpeg", "mpg", "avi", "ogv", "swf", "mp4", "webm", "m4v", "ogg", "mp3", "wav", "mid", "zip", "rar", "tar", "gz"]'."\n";
		$js .= "\t"."},\n";

		$js .= "\t".'"upload": {'."\n";
		$js .= "\t\t".'"multiple": true,'."\n";
		$js .= "\t\t".'"number": 5,'."\n";
		$js .= "\t\t".'"overwrite": false,'."\n";
		$v = ($elxis->getConfig('SECURITY_LEVEL') > 0) ? 'true' : 'false';
		$js .= "\t\t".'"imagesOnly": '.$v.','."\n";
		$v = floor($this->max_upload_size / 1048576);
		if ($v < 1) { $v = 1; }
		$js .= "\t\t".'"fileSizeLimit": '.$v."\n";
		$js .= "\t"."},\n";

		$js .= "\t".'"exclude": {'."\n";
		$js .= "\t\t".'"unallowed_files": [".htaccess", "web.config"],'."\n";
		$js .= "\t\t".'"unallowed_dirs": ["_thumbs", ".CDN_ACCESS_LOGS", "cloudservers"],'."\n";
		$js .= "\t\t".'"unallowed_files_REGEXP": "/^\\./",'."\n";
		$js .= "\t\t".'"unallowed_dirs_REGEXP": "/^\\./"'."\n";
		$js .= "\t"."},\n";

		$js .= "\t".'"images": {'."\n";
		$js .= "\t\t".'"imagesExt": ["jpg", "jpeg", "gif", "png", "svg"],'."\n";
		$v = ($can_resize === true) ? 'true' : 'false';
		$js .= "\t\t".'"resize": { "enabled" : '.$can_resize.', "maxWidth": 1280, "maxHeight": 1024 }'."\n";
		$js .= "\t"."},\n";

		$v = ($editor == 1) ? 'false' : 'true';
		$js .= "\t".'"videos": {'."\n";
		$js .= "\t\t".'"showVideoPlayer": '.$v.', "videosExt": [ "ogv", "mp4", "webm", "m4v" ], "videosPlayerWidth": 400, "videosPlayerHeight": 222'."\n";
		$js .= "\t"."},\n";
		$js .= "\t".'"audios": {'."\n";
		$js .= "\t\t".'"showAudioPlayer": '.$v.', "audiosExt": [ "ogg", "mp3", "wav" ]'."\n";
		$js .= "\t"."},\n";
		$js .= "\t".'"pdfs": {'."\n";
		$js .= "\t\t".'"showPdfReader": false, "pdfsExt": [ "pdf", "odp" ], "pdfsReaderWidth": "640", "pdfsReaderHeight": "480"'."\n";
		$js .= "\t"."},\n";
		$js .= "\t".'"edit": {'."\n";
		$js .= "\t\t".'"enabled": false, "lineNumbers": true, "lineWrapping": true, "codeHighlight": false, "theme": "elegant", "editExt": [ "txt", "csv"]'."\n";
		$js .= "\t"."},\n";

		//todo: do we want customScrollbar? see how it looks without it
		$js .= "\t".'"customScrollbar": { "enabled": true, "theme": "inset-2-dark", "button": true },'."\n";
		$js .= "\t".'"extras": { "extra_js": [], "extra_js_async": true },'."\n";
		//todo: is full url required for path?
		$js .= "\t".'"icons": { "path": "images/fileicons/", "directory": "_Open.png", "default": "default.png" }'."\n";
		$js .= '};';

		$this->pageHeaders('application/javascript');//not "application/json" because it wont work with "X-Content-Type-Options: nosniff"
		echo $js;
		exit;
	}

}

?>