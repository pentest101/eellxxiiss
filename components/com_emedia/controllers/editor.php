<?php 
/**
* @version		$Id: editor.php 1784 2016-02-15 19:21:11Z sannosi $
* @package		Elxis
* @subpackage	Component eMedia
* @copyright	Copyright (c) 2006-2019 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class editorMediaControl extends emediaController {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null) {
		parent::__construct($view, true);
	}


	/**************************/
	/* JODIT EDITOR CONNECTOR */
	/**************************/
	public function editorconnector() {
		$elxis = eFactory::getElxis();

		$jodit_path = ELXIS_PATH.'/includes/js/jodit/connector/';
		
		define('JODIT_DEBUG', false);
		require_once $jodit_path.'vendor/autoload.php';
		require_once $jodit_path.'Application.php';

		$images_rel_path = 'media/images/';
		if (defined('ELXIS_MULTISITE')) {
			if (ELXIS_MULTISITE > 1) { $this->relpath = 'media/images/site'.ELXIS_MULTISITE.'/'; }
		}

		$config = [
			'datetimeFormat' => 'd/m/Y g:i A',
			'quality' => 90,
			'defaultPermission' => 0775,
			'sources' => [
				'Elxis images' => [
					'root' => ELXIS_PATH.'/'.$images_rel_path,
					'baseurl' => $elxis->secureBase(true).'/'.$images_rel_path,
					'maxFileSize' => '4000kb',
					'createThumb' => false,
					'extensions' => ['jpg', 'png', 'gif', 'jpeg', 'bmp', 'svg', 'ico'],
				]
			],
			'createThumb' => false,
			'thumbFolderName' => '_thumbs',
			'excludeDirectoryNames' => ['.tmb', '.quarantine'],
			'maxFileSize' => '8mb',
			'allowCrossOrigin' => false,
			'allowReplaceSourceFile' => true,
			//'baseurl' => ((isset($_SERVER['HTTPS']) and $_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/',
			'baseurl' => $elxis->secureBase(true).'/'.$images_rel_path,
			//'root' => '/',
			'root' => ELXIS_PATH.'/'.$images_rel_path,
			'extensions' => ['jpg', 'png', 'gif', 'jpeg'],
			'imageExtensions' => ['jpg', 'png', 'gif', 'jpeg'],
			'debug' => JODIT_DEBUG,
			'accessControl' => []
		];
		$config['roleSessionVar'] = 'JoditUserRole';
		$config['accessControl'][] = array(
			'role'                => '*',
			'extensions'          => '*',
			'path'                => '/',
			'FILES'               => true,
			'FILE_MOVE'           => true,
			'FILE_UPLOAD'         => true,
			'FILE_UPLOAD_REMOTE'  => false,
			'FILE_REMOVE'         => true,
			'FILE_RENAME'         => true,
			'FOLDERS'             => true,
			'FOLDER_MOVE'         => true,
			'FOLDER_REMOVE'       => true,
			'FOLDER_RENAME'       => true,
			'IMAGE_RESIZE'        => true,
			'IMAGE_CROP'          => true,
		);
		$config['accessControl'][] = array(
			'role'                => '*',
			'extensions'          => 'exe,bat,com,sh,swf,php,js',
			'FILE_MOVE'           => false,
			'FILE_UPLOAD'         => false,
			'FILE_UPLOAD_REMOTE'  => false,
			'FILE_RENAME'         => false,
		);

		$action = '';
		if (isset($_POST['action'])) { $action = trim($_POST['action']); }
		if ($action == '') {
			if (isset($_GET['action'])) { $action = trim($_GET['action']); }
		}

		$fileBrowser = new \JoditRestApplication($config);

		try {
			if ($action != '') { $fileBrowser->action = $action; }
			$fileBrowser->checkAuthentication();
			$fileBrowser->execute();
		} catch(\ErrorException $e) {
			$fileBrowser->exceptionHandler($e);
		}

		exit;
	}

}

?>