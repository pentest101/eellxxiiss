<?php 
/**
* @version		$Id: connector.php 1788 2016-02-16 17:50:05Z sannosi $
* @package		Elxis
* @subpackage	Component eMedia
* @copyright	Copyright (c) 2006-2019 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class connectorMediaControl extends emediaController {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null) {
		parent::__construct($view);
	}


	/*****************************/
	/* INITIATE JS-PHP CONNECTOR */
	/*****************************/
	public function connect() {
		if (isset($_GET['mode'])) {
			$mode = trim(filter_input(INPUT_GET, 'mode', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		} else if (isset($_POST['mode'])) {
			$mode = trim(filter_input(INPUT_POST, 'mode', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		} else {
			$mode = '';
		}

		if ($mode == '') {
			$msg = eFactory::getLang()->get('INVALID_REQUEST');
			$this->view->errorResponse($msg);
		}

		switch ($mode) {
			case 'getinfo': $this->getinfo(); break;
			case 'getfolder': $this->getfolder(); break;
			case 'rename': $this->rename(); break;
			case 'move': $this->move(); break;
			case 'delete': $this->delete(); break;
			case 'addfolder': $this->addfolder(); break;
			case 'download': $this->download(); break;
			case 'preview': $this->preview(); break;
			case 'add': $this->uploadFile(); break;
			case 'replace': $this->replaceFile(); break;
			//case 'compress': $this->compressFolder(); break;//replaced by mode "download" => dir
			case 'resize': $this->resizeImage(); break; //developed by i.sannos
			case 'editfile': //not used
			case 'savefile': //not used
			default:
				$msg = eFactory::getLang()->get('INVALID_REQUEST');
				$this->view->errorResponse($msg);
			break;
		}
	}


	/*************************************/
	/* GET INFORMATION FOR A FILE/FOLDER */
	/*************************************/
	private function getinfo() {
		$path = $this->getPath();

		if ($path === false) {
			$msg = eFactory::getLang()->get('PATH_NOT_EXIST');
			$this->view->errorResponse($msg);
		}

		if (!file_exists(ELXIS_PATH.'/'.$this->relpath.$path)) {
			$msg = eFactory::getLang()->get('FILE_NOT_FOUND');
			$this->view->errorResponse($msg);
		}

		$item = $this->get_file_info($path, false);
		$response = array(
			'Path'=> $path,
			'Filename' => $item['Filename'],
			'File_Type'=> $item['File_Type'],
			'Protected'=> $item['Protected'],
			'Preview' => $item['Preview'],
			'Properties' => $item['Properties'],
			'Error' => '',
			'Code' => 0
		);

		$this->view->jsonResponse($response);
	}


	/**********************************/
	/* GET RELATIVE PATH FROM REQUEST */
	/**********************************/
	private function getPath() {
		$path = rawurldecode(filter_input(INPUT_GET, 'path', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$path = preg_replace('~/+~', '/', $path); //remove multiple slashes
		$path = ltrim($path, '/');
		$path = str_replace('..', '', $path);
		if (!file_exists(ELXIS_PATH.'/'.$this->relpath.$path)) { return false; }
		return $path;
	}


	/******************************/
	/* GET INFORMATION FOR A FILE */
	/******************************/
	private function get_file_info($path, $folder_view=false) {
		$elxis = eFactory::getElxis();
		$eDate = eFactory::getDate();
		$eLang = eFactory::getLang();

		$item = array();
		$item['Properties'] = array(
			'Date_Created' => null,
			'Date_Modified' => null,
			'filemtime' => 0,
			'filectime' => 0,
			'Height' => 0,
			'Width' => 0,
			'Size' => 0
		);
		$item['Capabilities'] = array('select', 'delete', 'rename');
		$item['Protected'] = 0;

		$tmp = explode('/', $path);
		$item['Filename'] = $tmp[(count($tmp)-1)];

    	$tmp = explode('.', $item['Filename']);
		$item['File_Type'] = $tmp[(count($tmp)-1)];
    	$item['Properties']['filemtime'] = filemtime(ELXIS_PATH.'/'.$this->relpath.$path);
    	$item['Properties']['filectime'] = filectime(ELXIS_PATH.'/'.$this->relpath.$path);

		if (is_dir(ELXIS_PATH.'/'.$this->relpath.$path)) {
			$item['Preview'] = $elxis->secureBase().'/components/com_emedia/images/fileicons/_Open.png';
			$item['Capabilities'][] = 'download';
		} else if (in_array(strtolower($item['File_Type']), array('jpeg', 'jpg', 'jpe', 'gif', 'png'))) {
			$item['Capabilities'][] = 'resize';
			$item['Capabilities'][] = 'download';
			$item['Capabilities'][] = 'move';
			$item['Properties']['Size'] = filesize(ELXIS_PATH.'/'.$this->relpath.$path);

			if (($folder_view == true) && ($item['Properties']['Size'] > 30000)) {
				$item['Preview'] = $elxis->makeAURL('emedia:connect/', 'inner.php').'?mode=preview&path='.rawurlencode($path).'&thumbnail=true';
			} else {
				$item['Preview'] = $elxis->secureBase().'/'.$this->relpath.$path;
			}
      		$info = getimagesize(ELXIS_PATH.'/'.$this->relpath.$path);
      		$item['Properties']['Width'] = (int)$info[0];
      		$item['Properties']['Height'] = (int)$info[1];
		} else if(file_exists(ELXIS_PATH.'/components/com_emedia/images/fileicons/'.strtolower($item['File_Type']).'.png')) {
			$item['Capabilities'][] = 'download';
			$item['Capabilities'][] = 'move';
			$item['Preview'] = $elxis->secureBase().'/components/com_emedia/images/fileicons/'.strtolower($item['File_Type']).'.png';
			$item['Properties']['Size'] = filesize(ELXIS_PATH.'/'.$this->relpath.$path);
		} else {
			$item['Preview'] = $elxis->secureBase().'/components/com_emedia/images/fileicons/default.png';
			$item['Capabilities'][] = 'download';
			$item['Capabilities'][] = 'move';
			$item['Properties']['Size'] = filesize(ELXIS_PATH.'/'.$this->relpath.$path);
		}

		$item['Properties']['Date_Created'] = $eDate->formatTS($item['Properties']['filectime'], $eLang->get('DATE_FORMAT_4'));
		$item['Properties']['Date_Modified'] = $eDate->formatTS($item['Properties']['filemtime'], $eLang->get('DATE_FORMAT_4'));

		return $item;
	}


	/************************/
	/* LOAD FOLDER CONTENTS */
	/************************/
	public function getfolder() {
		$eFiles = eFactory::getFiles();
		$elxis = eFactory::getElxis();
		$eDate = eFactory::getDate();
		$eLang = eFactory::getLang();

		$response = array();
		$path = $this->getPath();

		if ($path === false) {
			$msg = eFactory::getLang()->get('PATH_NOT_EXIST');
			$this->view->errorResponse($msg);
		}

		$current_path = ELXIS_PATH.'/'.$this->relpath.$path;

		$loadfiles = true;
		if (isset($_GET['tree']) && ($_GET['tree'] == 1)) {
			if ($this->tree_show_files == 0) { $loadfiles = false; }
		}
		$filesDir = ($loadfiles) ? $eFiles->listFiles($this->relpath.$path) : array();
		$foldersDir = $eFiles->listFolders($this->relpath.$path);
		$previewPath = $elxis->secureBase().'/components/com_emedia/images/fileicons/_Open.png';

		if ($foldersDir) {
			sort($foldersDir);
			foreach($foldersDir as $folder) {
				$relpath = $path.$folder.'/';
				$cts = filectime(ELXIS_PATH.'/'.$this->relpath.$relpath);
				$mts = filemtime(ELXIS_PATH.'/'.$this->relpath.$relpath);

            	$response[$relpath] = array(
					'Path' => $relpath,
					'Filename' => $folder,
					'File_Type' => 'dir',
					'Protected' => 0,
					'Preview' => $previewPath,
					'Properties' => array(
						'Date_Created' => $eDate->formatTS($cts, $eLang->get('DATE_FORMAT_4')),
						'Date_Modified' => $eDate->formatTS($mts, $eLang->get('DATE_FORMAT_4')),
						'filemtime' => $mts,
						'Height' => 0,
						'Width' => 0,
						'Size' => 0
					),
					'Capabilities' => array('select', 'delete', 'rename', 'compress'),
					'Error' => '',
					'Code' => 0
				);
			}
		}

		if ($filesDir) {
			sort($filesDir);
			foreach($filesDir as $file) {
				$relpath = $path.$file;
				$item = $this->get_file_info($relpath, true);
				$response[$relpath] = array(
					'Path' => $relpath,
					'Filename' => $item['Filename'],
					'File_Type' => $item['File_Type'],
					'Protected' => $item['Protected'],
					'Preview' => $item['Preview'],
					'Properties' => $item['Properties'],
					'Capabilities' => $item['Capabilities'],
					'Error' => '',
					'Code' => 0
				);
			}
		}

		$this->view->jsonResponse($response);
	}


	/************************/
	/* RENAME FILE / FOLDER */
	/************************/
	private function rename() {
		$elxis = eFactory::getElxis();
		$eFiles = eFactory::getFiles();

		if ($elxis->acl()->check('com_emedia', 'files', 'edit') < 1) {
			$msg = eFactory::getLang()->get('NOTALLOWACTION');
			$this->view->errorResponse($msg);
		}

		$old = rawurldecode(filter_input(INPUT_GET, 'old', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$new = rawurldecode(filter_input(INPUT_GET, 'new', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$old = str_replace('..', '', $old);
		$old = str_replace('//', '/', $old);
		$new = str_replace('..', '', $new);
		$new = preg_replace('/[^a-zA-Z0-9\_\-\.]/', '', $new);

		if (($old == '') || ($old == '/')) {
			$msg = eFactory::getLang()->get('NO_FILE_SELECTED');
			$this->view->errorResponse($msg);
		}

		if (!file_exists(ELXIS_PATH.'/'.$this->relpath.$old)) {
			$msg = eFactory::getLang()->get('FILE_NOT_FOUND');
			$this->view->errorResponse($msg);
		}

		$suffix = '';
		$is_folder = false;
		if (substr($old, -1, 1) == '/') { //folder
			$old = substr($old, 0, (strlen($old)-1));
			$suffix = '/';
			$is_folder = true;
		} else {
			$exts = $this->allowedExtensions();
			$ext = strtolower($eFiles->getExtension($old));
			if (($ext == '') || !in_array($ext, $exts)) {
				$msg = eFactory::getLang()->get('FORBIDDEN_FILE_TYPE');
				$this->view->errorResponse($msg);
			}
			unset($exts);
		}

    	$tmp = explode('/', $old);
		$filename = $tmp[(count($tmp)-1)];
		$path = str_replace('/'.$filename, '', $old);

		if ($is_folder) {
			if (($new == '') || ($new != $_GET['new'])) {
				$msg = eFactory::getLang()->get('INVALID_FOLDER_NAME');
				$this->view->errorResponse($msg);
			}

			if (is_dir(ELXIS_PATH.'/'.$this->relpath.$path.'/'.$new.'/')) {
				$msg = eFactory::getLang()->get('FOLDER_NAME_EXISTS');
				$this->view->errorResponse($msg);
			}			
		} else {
			if (($new == '') || ($new != $_GET['new'])) {
				$msg = eFactory::getLang()->get('INVALID_FILE_NAME');
				$this->view->errorResponse($msg);
			}

			if (file_exists(ELXIS_PATH.'/'.$this->relpath.$path.'/'.$new)) {
				$msg = eFactory::getLang()->get('FILE_NAME_EXISTS');
				$this->view->errorResponse($msg);
			}

			$exts = $this->allowedExtensions();
			$ext = strtolower($eFiles->getExtension($new));
			if (($ext == '') || !in_array($ext, $exts)) {
				$msg = eFactory::getLang()->get('INVALID_FILE_NAME');
				$this->view->errorResponse($msg);
			}
			unset($exts);
		}

		if ($is_folder) {
			$ok = $eFiles->moveFolder($this->relpath.$old.'/', $this->relpath.$path.'/'.$new.'/');
		} else {
			$ok = $eFiles->move($this->relpath.$old, $this->relpath.$path.'/'.$new);
		}

		if (!$ok) {
			$msg = eFactory::getLang()->get('RENAME_FAILED');
			$this->view->errorResponse($msg);
		}

		$response = array('Error' => '', 'Code' => 0, 'Old_Path' => $old, 'Old_Name' => $filename, 'New_Path' => urlencode($path.'/'.$new.$suffix), 'New_Name' => $new);
		$this->view->jsonResponse($response);
	}


	/********************/
	/* MOVE FILE/FOLDER */
	/********************/
	private function move() {
		$elxis = eFactory::getElxis();
		$eFiles = eFactory::getFiles();

		if ($elxis->acl()->check('com_emedia', 'files', 'edit') < 1) {
			$msg = eFactory::getLang()->get('NOTALLOWACTION');
			$this->view->errorResponse($msg);
		}

		$old = rawurldecode(filter_input(INPUT_GET, 'old', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$new = rawurldecode(filter_input(INPUT_GET, 'new', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$old = str_replace('..', '', $old);
		$old = str_replace('//', '/', $old);
		$old = str_replace('\\', '/', $old);
		$new = str_replace('..', '', $new);
		$new = str_replace('\\', '/', $new);
		$new = preg_replace('/[^a-zA-Z0-9\_\-\.\/]/', '', $new);
		$new = trim($new, '/');
		$new .= '/';

		if (($old == '') || ($old == '/')) {
			$msg = eFactory::getLang()->get('NO_FILE_SELECTED');
			$this->view->errorResponse($msg);
		}
		if (($old == 'images/') || ($old == 'videos/') || ($old == 'audio/')) {
			$msg = eFactory::getLang()->get('NOTALLOWACTION');
			$this->view->errorResponse($msg);
		}

		if (!file_exists(ELXIS_PATH.'/'.$this->relpath.$old)) {
			$msg = eFactory::getLang()->get('FILE_NOT_FOUND');
			$this->view->errorResponse($msg);
		}

		if ((strpos($old, '.') === false) || is_dir(ELXIS_PATH.'/'.$this->relpath.$old)) {
			$this->view->errorResponse('You can only move regular files, not folders!');
			$this->view->errorResponse($msg);
		}

		if (($new == '') || ($new != $_GET['new'])) {
			$msg = eFactory::getLang()->get('INVALID_FOLDER_NAME');
			$this->view->errorResponse($msg);
		}

		if (!file_exists(ELXIS_PATH.'/'.$this->relpath.$new) || !is_dir(ELXIS_PATH.'/'.$this->relpath.$new)) {
			$msg = eFactory::getLang()->get('PATH_NOT_EXIST').' '.$this->relpath.$new;
			$this->view->errorResponse($msg);
		}

		$fname = basename($old);
		if (file_exists(ELXIS_PATH.'/'.$this->relpath.$new.$fname)) {
			$msg = 'There is already a file named '.$fname.' in folder '.$new;
			$this->view->errorResponse($msg);
		}
		
		$ok = $eFiles->move($this->relpath.$old, $this->relpath.$new.$fname);
		if (!$ok) {
			$msg = eFactory::getLang()->get('ACTION_FAILED');
			$this->view->errorResponse($msg);
		}

		$response = array('Error' => '', 'Code' => 0, 'Old_Path' => $old, 'Old_Name' => $fname, 'New_Path' => $new, 'New_Name' => $fname);
		$this->view->jsonResponse($response);
	}



	/************************/
	/* DELETE FILE / FOLDER */
	/************************/
	private function delete() {
		$elxis = eFactory::getElxis();
		$eFiles = eFactory::getFiles();

		if ($elxis->acl()->check('com_emedia', 'files', 'edit') < 1) {
			$msg = eFactory::getLang()->get('NOTALLOWACTION');
			$this->view->errorResponse($msg);
		}

		$path = $this->getPath();

		if (($path === false) || ($path == '') || ($path == '/')) {
			$msg = eFactory::getLang()->get('FILE_NOT_FOUND');
			$this->view->errorResponse($msg);
		}

		if ((strpos($path, '..') !== false) || ($path == 'audio/') || ($path == 'images/') || ($path == 'videos/')) {
			$msg = eFactory::getLang()->get('NOTALLOWACTION');
			$this->view->errorResponse($msg);
		}

		if (is_dir(ELXIS_PATH.'/'.$this->relpath.$path)) {
			$ok = $eFiles->deleteFolder($this->relpath.$path);
		} else {
			$exts = $this->allowedExtensions();
			$ext = strtolower($eFiles->getExtension($path));
			if (($ext == '') || !in_array($ext, $exts)) {
				$msg = eFactory::getLang()->get('FORBIDDEN_FILE_TYPE');
				$this->view->errorResponse($msg);
			}
			$ok = $eFiles->deleteFile($this->relpath.$path);
		}

		if (!$ok) {
			$msg = eFactory::getLang()->get('DELETE_FAILED');
			$this->view->errorResponse($msg);
		}

		$response = array('Error' => '', 'Code' => 0, 'Path' => $path);
		$this->view->jsonResponse($response);
	}


	/***********************/
	/* CREATE A NEW FOLDER */
	/***********************/
	private function addfolder() {
		$elxis = eFactory::getElxis();
		$eFiles = eFactory::getFiles();

		if ($elxis->acl()->check('com_emedia', 'files', 'edit') < 1) {
			$msg = eFactory::getLang()->get('NOTALLOWACTION');
			$this->view->errorResponse($msg);
		}

		$path = $this->getPath();
		if ($path === false) {
			$msg = eFactory::getLang()->get('PATH_NOT_EXIST');
			$this->view->errorResponse($msg);
		}

		if ($path == '') {
			//do nothing
		} else if ($path == '/') {
			$path = '';
		} else if (!preg_match('#\/$#', $path)) {
			$path .= '/';
		}

		$name = rawurldecode(filter_input(INPUT_GET, 'name', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$name = str_replace('..', '', $name);
		$name = preg_replace('/[_]+/', '_', $name); //remove double underscore
		$name = trim($name, '/');
		$name = preg_replace('/[^a-zA-Z0-9\_\-]/', '', $name);
		if (($name == '') || ($name != $_GET['name']) || (strlen($name) < 3)) {
			$msg = eFactory::getLang()->get('INVALID_FOLDER_NAME');
			$this->view->errorResponse($msg);
		}

		if (is_dir(ELXIS_PATH.'/'.$this->relpath.$path.$name.'/')) {
			$msg = eFactory::getLang()->get('FOLDER_NAME_EXISTS');
			$this->view->errorResponse($msg);
		}

		$ok = $eFiles->createFolder($this->relpath.$path.$name.'/');
		if (!$ok) {
			$msg = sprintf(eFactory::getLang()->get('CNOT_CREATE_FOLDER'), $name);
			$this->view->errorResponse($msg);
		}

		$response = array('Parent' => $path, 'Name' => $name, 'Error' => '', 'Code' => 0);
		$this->view->jsonResponse($response);
	}


	/*****************/
	/* DOWNLOAD FILE */
	/*****************/
	private function download() {
		$eFiles = eFactory::getFiles();

		$path = $this->getPath();
		if ($path === false) {
			$msg = eFactory::getLang()->get('FILE_NOT_FOUND');
			$this->view->errorResponse($msg);
		}

		if ((strpos($path, '..') !== false) || ($path == '') || ($path == '/')) {
			$msg = eFactory::getLang()->get('NOTALLOWACTION');
			$this->view->errorResponse($msg);
		}

		if (!file_exists(ELXIS_PATH.'/'.$this->relpath.$path)) {
			$msg = eFactory::getLang()->get('FILE_NOT_FOUND');
			$this->view->errorResponse($msg);
		}

		if (is_dir(ELXIS_PATH.'/'.$this->relpath.$path)) {
			$tmp_path = $eFiles->elxisPath('tmp/', true);
			$name = basename($this->relpath.$path);
			if (file_exists($tmp_path.$name.'.zip')) { $name = $name.'_'.time(); }

			$zip = eFactory::getElxis()->obj('zip');
			$ok = $zip->zip($tmp_path.$name.'.zip', ELXIS_PATH.'/'.$this->relpath.$path);
			if (!$ok) {
				$this->view->errorResponse($zip->getError());
			}

			$fname = $name.'.zip';
			$abs_path = $tmp_path.$fname;
			$del_tmp_after = 'tmp/'.$fname;
		} else {
			$fname = basename($path);
			$abs_path = ELXIS_PATH.'/'.$this->relpath.$path;
			$del_tmp_after = '';
		}

		$this->pageHeaders('application/force-download');
		header('Content-Disposition: inline; filename="'.$fname.'"');
		header("Content-Transfer-Encoding: Binary");
		header("Content-length: ".filesize($abs_path));
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="'.$fname.'"');
		readfile($abs_path);

		if ($del_tmp_after != '') {
			$eFiles->deleteFile($del_tmp_after, true);
		}
		exit;
	}


	/*****************/
	/* PREVIEW IMAGE */
	/*****************/
	private function preview() {
		$path = $this->getPath();
		$final = $this->relpath.$path;
		if (($path == '') || ($path === false)) {
			$final = 'components/com_emedia/images/image_not_found.png';
		} else {
			if (!file_exists(ELXIS_PATH.'/'.$this->relpath.$path) || !is_file(ELXIS_PATH.'/'.$this->relpath.$path)) {
				$final = 'components/com_emedia/images/image_not_found.png';
			} else {
				$ext = strtolower(pathinfo(ELXIS_PATH.'/'.$this->relpath.$path, PATHINFO_EXTENSION));
				if (($ext == '') || !in_array($ext, array('png', 'gif', 'jpeg', 'jpg'))) {
					$final = 'components/com_emedia/images/preview_not_available.png';
				}
			}
		}

		$thumb = eFactory::getElxis()->obj('thumb');
		$thumb->show($final, 100, 100, true);
	}


	/*********************/
	/* UPLOAD A NEW FILE */
	/*********************/
	private function uploadFile() {
		$elxis = eFactory::getElxis();
		$eFiles = eFactory::getFiles();

		if ($elxis->acl()->check('com_emedia', 'files', 'upload') < 1) {
			$msg = eFactory::getLang()->get('NOTALLOWACTION');
			$this->view->errorResponse($msg, true);
		}

		if (!isset($_FILES['newfile']) || !is_uploaded_file($_FILES['newfile']['tmp_name'])) {
			$msg = eFactory::getLang()->get('NO_FILE_UPLOADED');
			$this->view->errorResponse($msg, true);	
		}

		if ($_FILES['newfile']['size'] > $this->max_upload_size) {
			$max = round(($this->max_upload_size / 1048576), 1);
			$msg = sprintf(eFactory::getLang()->get('MAX_ALLOWED_FSIZE'), $max.' mb');
			$this->view->errorResponse($msg, true);
		}

		$exts = $this->allowedExtensions();
		$ext = strtolower($eFiles->getExtension($_FILES['newfile']['name']));
		if (($ext == '') || !in_array($ext, $exts)) {
			$msg = eFactory::getLang()->get('FORBIDDEN_FILE_TYPE');
			$this->view->errorResponse($msg, true);
		}
		unset($exts);

		$currentpath = filter_input(INPUT_POST, 'currentpath', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		$currentpath = ltrim($currentpath, '/');
		$currentpath = str_replace('..', '', $currentpath);
		if ($currentpath == '') {
			//do nothing
		} else if ($currentpath == '/') {
			$currentpath = '';
		} else if (!preg_match('#\/$#', $currentpath)) {
			$currentpath .= '/';
		}

		if (!is_dir(ELXIS_PATH.'/'.$this->relpath.$currentpath)) {
			$msg = eFactory::getLang()->get('PATH_NOT_EXIST');
			$this->view->errorResponse($msg, true);
		}

		$filename = preg_replace('/[^a-zA-Z0-9\_\-\.\(\)]/', '', $_FILES['newfile']['name']);

		$info = $eFiles->getNameExtension($filename);
		if ($info['extension'] == '') {
			$msg = eFactory::getLang()->get('INVALID_FILE_NAME');
			$this->view->errorResponse($msg, true);
		}

		if ($info['name'] == '') { $filename = 'file'.rand(1000, 9999).'.'.$info['extension']; }

		if (file_exists(ELXIS_PATH.'/'.$this->relpath.$currentpath.$filename)) {
			$msg = eFactory::getLang()->get('FILE_NAME_EXISTS').' ('.$filename.')';
			$this->view->errorResponse($msg, true);
		}

		$ok = $eFiles->upload($_FILES['newfile']['tmp_name'], $this->relpath.$currentpath.$filename);
		if (!$ok) {
			$msg = eFactory::getLang()->get('FILE_UPLOAD_FAILED');
			$this->view->errorResponse($msg, true);
		}

		$response = array('Path' => $currentpath, 'Name' => $filename, 'Error' => '', 'Code' => 0);
		$this->view->jsonResponse($response, true);
	}


	/******************/
	/* REPLACE A FILE */
	/******************/
	private function replaceFile() {
		$elxis = eFactory::getElxis();
		$eFiles = eFactory::getFiles();

		if ($elxis->acl()->check('com_emedia', 'files', 'upload') < 1) {
			$msg = eFactory::getLang()->get('NOTALLOWACTION');
			$this->view->errorResponse($msg, true);
		}

		if (!isset($_FILES['fileR']) || !is_uploaded_file($_FILES['fileR']['tmp_name'])) {
			$msg = eFactory::getLang()->get('NO_FILE_UPLOADED');
			$this->view->errorResponse($msg, true);	
		}

		if ($_FILES['fileR']['size'] > $this->max_upload_size) {
			$max = round(($this->max_upload_size / 1048576), 1);
			$msg = sprintf(eFactory::getLang()->get('MAX_ALLOWED_FSIZE'), $max.' mb');
			$this->view->errorResponse($msg, true);
		}

		$exts = $this->allowedExtensions();
		$ext = strtolower($eFiles->getExtension($_FILES['fileR']['name']));
		if (($ext == '') || !in_array($ext, $exts)) {
			$msg = eFactory::getLang()->get('FORBIDDEN_FILE_TYPE');
			$this->view->errorResponse($msg, true);
		}
		unset($exts);

		$newfilepath = filter_input(INPUT_POST, 'newfilepath', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		$newfilepath = trim($newfilepath, '/');
		$newfilepath = str_replace('..', '', $newfilepath);
		if (($newfilepath == '') || ($newfilepath != $_POST['newfilepath'])) {
			$this->view->errorResponse('Error replacing file', true);
		}
		$ext2 = strtolower($eFiles->getExtension($newfilepath));
		if ($ext != $ext2) {
			$this->view->errorResponse('Replacing file extension must be the same as the original file ('.$ext2.')!', true);
		}

		$ok = $eFiles->upload($_FILES['fileR']['tmp_name'], $this->relpath.$newfilepath);
		if (!$ok) {
			$msg = eFactory::getLang()->get('FILE_UPLOAD_FAILED');
			$this->view->errorResponse($msg, true);
		}
		if (!$ok) {
			$msg = eFactory::getLang()->get('FILE_UPLOAD_FAILED');
			$this->view->errorResponse($msg, true);
		}

		$response = array('Path' => dirname($newfilepath), 'Name' => basename($newfilepath), 'Error' => '', 'Code' => 0);
		$this->view->jsonResponse($response, true);
	}


	/*******************/
	/* RESIZE AN IMAGE */
	/*******************/
	private function resizeImage() {//deprecated - not used any more (i.sannos 15.02.2016)
		$elxis = eFactory::getElxis();
		$eFiles = eFactory::getFiles();

		if ($elxis->acl()->check('com_emedia', 'files', 'edit') < 1) {
			$msg = eFactory::getLang()->get('NOTALLOWACTION');
			$this->view->errorResponse($msg);
		}

		$path = $this->getPath();
		if (($path == false) || !file_exists(ELXIS_PATH.'/'.$this->relpath.$path)) {
			$msg = eFactory::getLang()->get('FILE_NOT_FOUND');
			$this->view->errorResponse($msg);
		}


		$ext = strtolower($eFiles->getExtension($path));
		if (($ext == '') || !in_array($ext, array('png', 'jpeg', 'jpg', 'gif'))) {
			$msg = eFactory::getLang()->get('ONLY_RESIZE_IMAGES');
			$this->view->errorResponse($msg);
		}

		$rwidth = isset($_GET['rwidth']) ? (int)$_GET['rwidth'] : 0;
		$rheight = isset($_GET['rheight']) ? (int)$_GET['rheight'] : 0;
		if ($rwidth < 0) { $rwidth = 0; }
		if ($rheight < 0) { $rheight = 0; }

		if (($rwidth == 0) && ($rheight == 0)) {
			$msg = eFactory::getLang()->get('WIDTH_INVALID');
			$this->view->errorResponse($msg);
		}

		if (($rwidth < 10) && ($rheight < 10)) {
			$msg = eFactory::getLang()->get('WIDTH_TOO_SMALL');
			$this->view->errorResponse($msg);
		}

		$imginfo = getimagesize(ELXIS_PATH.'/'.$this->relpath.$path);
    	if (!$imginfo) {
			$this->view->errorResponse('Could not determine original image size!');
    	}
		if (!in_array($imginfo[2], array(1, 2, 3))) {
			$msg = eFactory::getLang()->get('ONLY_RESIZE_IMAGES');
			$this->view->errorResponse($msg);
		}

		if (($rwidth == $imginfo[0]) && ($rheight == $imginfo[1])) {
			$msg = eFactory::getLang()->get('IMAGE_ALREADY_DIMS');
			$this->view->errorResponse($msg);
		}

		if ($rwidth == 0) {
			$new_height = $rheight;
			$new_width = intval(($imginfo[0] * $rheight) / $imginfo[1]);
		} else if ($rheight == 0) {
			$new_width = $rwidth;
			$new_height = intval(($imginfo[1] * $rwidth) / $imginfo[0]);
		} else {
			$new_width = $rwidth;
			$new_height = $rheight;
		}

		$ok = $eFiles->resizeImage($this->relpath.$path, $new_width, $new_height, false, false);

		if (!$ok) {
			$msg = eFactory::getLang()->get('RESIZE_FAILED');
			$this->view->errorResponse($msg);
		}

		if (strpos($path, '/') !== false) { $dirname = dirname($path); } else { $dirname = ''; }
		$response = array('Error' => '', 'Code' => 0, 'New_Width' => $new_width, 'New_Height' => $new_height, 'Dirname' => $dirname);
		$this->view->jsonResponse($response);
	}


	/*************************/
	/* ZIP COMPRESS A FOLDER */
	/*************************/
	private function compressFolder() {//i. sannos 15.02.2016: replaced by mode "download" => dir
		$elxis = eFactory::getElxis();

		if ($elxis->acl()->check('com_emedia', 'files', 'edit') < 1) {
			$msg = eFactory::getLang()->get('NOTALLOWACTION');
			$this->view->errorResponse($msg);
		}

		$path = $this->getPath();
		if (($path === false) || ($path == '') || ($path == '/')) {
			$msg = eFactory::getLang()->get('SELECT_FOLDER_COMPRESS');
			$this->view->errorResponse($msg);
		}

		if (!is_dir(ELXIS_PATH.'/'.$this->relpath.$path)) {
			$this->view->errorResponse('Yopu can compress only folders!');
		}

		$parts = preg_split('#\/#', $path, -1, PREG_SPLIT_NO_EMPTY);
		if (!$parts || (count($parts) == 0)) {
			$msg = eFactory::getLang()->get('SELECT_FOLDER_COMPRESS');
			$this->view->errorResponse($msg);
		}

		$n = count($parts);
		$lastseg = $n - 1;
		$last = $parts[$lastseg];

		$upload_dir = '';
		if ($n > 1) {
			for ($i=0; $i < $n; $i++) {
				if ($i == $lastseg) { break; }
				$upload_dir .= $parts[$i].'/';
			}
		}

		$archive_name = $last.'.zip';
		if (file_exists(ELXIS_PATH.'/'.$this->relpath.$upload_dir.$archive_name)) {
			$archive_name = $last.'_'.rand(1000, 9999).'.zip';
		}

		$archive = ELXIS_PATH.'/'.$this->relpath.$upload_dir.$archive_name;
		$source = ELXIS_PATH.'/'.$this->relpath.$path;

		$zip = $elxis->obj('zip');
		$ok = $zip->zip($archive, $source);
		if (!$ok) {
			$msg = $zip->getError();
			if ($msg == '') { $msg = 'Compressing folder '.$last.' failed!'; }
		}
		unset($zip);

		$response = array('error' => '', 'code' => 0, 'path' => $upload_dir, 'archive' => $archive_name);
		$this->view->jsonResponse($response);
	}

}

?>