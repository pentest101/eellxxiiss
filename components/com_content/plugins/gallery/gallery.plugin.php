<?php 
/**
* @version		$Id$
* @package		Elxis
* @subpackage	Component Content / Plugins
* @copyright	Copyright (c) 2006-2021 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class galleryPlugin implements contentPlugin {


	/********************/
	/* MAGIC CONTRUCTOR */
	/********************/
	public function __construct() {
	}


	/***********************************/
	/* EXECUTE PLUGIN ON THE GIVEN ROW */
	/***********************************/
	public function process(&$row, $published, $params) {
		$regex = "#{gallery\s*(.*?)}(.*?){/gallery}#s";
    	$regexno = "#{gallery\s*.*?}.*?{/gallery}#s";

    	if (!$published) {
    		$row->text = preg_replace($regexno, '', $row->text);
    		return true;
    	}

		preg_match_all($regex, $row->text, $matches, PREG_PATTERN_ORDER);
		if (!$matches) { return true; }

		$ePlugin = eFactory::getPlugin();

		$cfg = array();
		$cfg['ordering'] = (int)$params->get('ordering', 0);
		$cfg['autocaptions'] = (int)$params->get('autocaptions', 1);
		$cfg['columns'] = (int)$params->get('columns', 3);
		if ($cfg['columns'] != 4) { $cfg['columns'] = 3; }
		$cfg['thumbnails'] = (int)$params->get('thumbnails', 0);
		$cfg['thumbwidth'] = (int)$params->get('thumbwidth', 240);
		$cfg['thumbheight'] = (int)$params->get('thumbheight', 0);
		$cfg['thumbmanip'] = (int)$params->get('thumbmanip', 0);

		foreach ($matches[0] as $i => $match) {
			$fpath = trim($matches[2][$i]);
			if (($fpath == '') || !file_exists(ELXIS_PATH.'/'.$fpath) || !is_dir(ELXIS_PATH.'/'.$fpath)) {
				$row->text = preg_replace("#".$match."#", '', $row->text);
				continue;
			}
			if (!preg_match('#(\/)$#', $fpath)) { $fpath .= '/'; }
			$images = $this->getImages($fpath, $cfg['ordering']);
			if ($images === false) {
			    $row->text = preg_replace("#".$match."#", '', $row->text);
				continue;
			}

			$this->importCSSJS();

			$options = $cfg;
			$attributes = $ePlugin->parseAttributes($matches[1][$i]);
			if (isset($attributes['columns'])) {
				$options['columns'] = (int)$attributes['columns'];
				if ($options['columns'] != 4) { $options['columns'] = 3; }
			}
			if (isset($attributes['thumbnails'])) {
				$options['thumbnails'] = (int)$attributes['thumbnails'];
				if ($options['thumbnails'] != 1) { $options['thumbnails'] = 0; }
			}
			if (isset($attributes['thumbwidth'])) {
				$options['thumbwidth'] = (int)$attributes['thumbwidth'];
				if ($options['thumbwidth'] < 60) { $options['thumbwidth'] = 120; }
			}
			if (isset($attributes['thumbheight'])) {
				$options['thumbheight'] = (int)$attributes['thumbheight'];
				if ($options['thumbheight'] < 60) { $options['thumbheight'] = 0; }
			}
			if (isset($attributes['thumbmanip'])) {
				$options['thumbmanip'] = (int)$attributes['thumbmanip'];
				if ($options['thumbmanip'] != 1) { $options['thumbmanip'] = 0; }
			}

			if (strpos($fpath, 'plugin_gallery_thumbs/') !== false) { $options['thumbnails'] = 0; }//just in case

			$html = $this->makeHTML($fpath, $images, $row->id.$i, $options);
			$row->text = preg_replace("#".$match."#", $html, $row->text);
		}

		return true;
	}


	/************************/
	/* GENERIC SYNTAX STYLE */
	/************************/
	public function syntax() {
		return '{gallery optional-attributes}relative/path/to/images/folder/{/gallery}';
	}


	/***********************/
	/* LIST OF HELPER TABS */
	/***********************/
	public function tabs() {
		$eLang = eFactory::getLang();
		return array($eLang->get('SELECT_FOLDER') , $eLang->get('UPLOAD_IMAGES'), $eLang->get('HELP'));
	}


	/***************************************************/
	/* RETURN REQUIRED CSS AND JS FILES FOR THE HELPER */
	/***************************************************/
	public function head() {
		$sfx = eFactory::getLang()->getinfo('RTLSFX');
		$response = array(
			'css' => array(eFactory::getElxis()->secureBase().'/components/com_content/plugins/gallery/includes/gallery'.$sfx.'.css'),
			'js' => array(eFactory::getElxis()->secureBase().'/components/com_content/plugins/gallery/includes/gallery.js')
		);
		return $response;
	}


	/*****************/
	/* PLUGIN HELPER */
	/*****************/
	public function helper($pluginid, $tabidx, $fn) {
		switch ($tabidx) {
			case 1: $this->pickFolder($pluginid, $fn); break;
			case 2: $this->uploadForm($pluginid, $fn); break;
			case 3: $this->showHelp(); break;
			default: break;
		}
	}


	/*******************************/
	/* PLUGIN SPECIAL TASK HANDLER */
	/*******************************/
	public function handler($pluginid, $fn) {
		$act = (isset($_POST['act'])) ? $_POST['act'] : '';
		if ($act == 'list') {
			$this->listImages($pluginid, $fn);
			exit;
		}
		if ($act == 'upload') {
			$this->uploadImages($pluginid, $fn);
			exit;
		}
		if ($act == 'setcaption') {
			$this->setImageCaption($pluginid, $fn);
			exit;
		}
		if ($act == 'delimage') {
			$this->deleteImage($pluginid, $fn);
			exit;
		}

		die('Invalid request');
	}


	/*********************************/
	/* HELPER : SELECT IMAGES FOLDER */
	/*********************************/
	private function pickFolder($pluginid, $fn) {
		$eLang = eFactory::getLang();
		$eFiles = eFactory::getFiles();
		$elxis = eFactory::getElxis();

		$relpath = $this->imagesRoot();
		$options = array();
		$folders = $eFiles->listFolders($relpath, false);
		if ($folders) {
			foreach ($folders as $folder) {
				if ($folder == 'plugin_gallery_thumbs') { continue; }
				$options[] = array($folder.'/', $folder.'/');
				$subfolders = $eFiles->listFolders($relpath.$folder.'/', false);
				if ($subfolders) {
					foreach ($subfolders as $subfolder) {
						$options[] = array($folder.'/'.$subfolder.'/', $folder.'/'.$subfolder.'/');
					}
				}
			}
		}

		echo '<div class="elx5_sideinput_wrap">';
		echo '<div class="elx5_sideinput_value_end elx5_spad">';
		echo '<a href="javascript:void(null);" class="elx5_btn elx5_ibtn" title="'.$eLang->get('INSERT_LINK_FOL').'" onclick="egal5toFolder();"><i class="fas fa-location-arrow"></i></a>';
		echo '</div>';
		echo '<div class="elx5_sideinput_input_front elx5_spad">';
		echo '<label class="elx5_label" for="egalleryctg">'.$eLang->get('FOLDER').'</label>';
		echo '<div class="elx5_labelside">';
		echo '<select name="egalleryctg" id="egalleryctg" class="elx5_select" dir="ltr" onchange="egal5FolderImages('.$pluginid.', '.$fn.')" data-relpath="'.$relpath.'">'."\n";
		echo '<option value="" selected="selected">- '.$eLang->get('NONE')." -</option>\n";
		if ($options) {
			foreach ($options as $option) {
				echo '<option value="'.$option['0'].'">'.$option[1]."</option>\n";
			}
		}
		echo "</select>\n";
		echo "</div></div></div>\n";

		echo '<div id="plugal_lng_cap" class="elx5_invisible">'.$eLang->get('CAPTION')."</div>\n";
		echo '<div id="plugal_lng_sure" class="elx5_invisible">'.$eLang->get('AREYOUSURE')."</div>\n";
		echo '<div id="plugal_url" class="elx5_invisible" dir="ltr">'.$elxis->makeAURL('content:plugin/', 'inner.php')."?task=handler</div>\n";
		echo '<div id="egalimages"></div>'."\n";
	}


	/*******************************/
	/* HELPER : UPLOAD IMAGES FORM */
	/*******************************/
	private function uploadForm($pluginid, $fn) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eFiles = eFactory::getFiles();

		if ($elxis->acl()->check('com_emedia', 'files', 'upload') < 1) {
			echo '<div class="elx5_warning">'.$eLang->get('REQ_ACCESS_UPLOAD')."</div>\n";
			return;
		}

		if ($elxis->getConfig('SECURITY_LEVEL') > 1) {
			echo '<div class="elx5_warning">Upload media files under the current security policy is not allowed!</div>'."\n";
			return;
		}

		$relpath = $this->imagesRoot();
		$foptions = array();
		$folders = $eFiles->listFolders($relpath, false);
		if ($folders) {
			foreach ($folders as $folder) {
				if ($folder == 'plugin_gallery_thumbs') { continue; }
				$foptions[] = $folder.'/';
				$subfolders = $eFiles->listFolders($relpath.$folder.'/', false);
				if ($subfolders) {
					foreach ($subfolders as $subfolder) { $foptions[] = $folder.'/'.$subfolder.'/'; }
				}
			}
		}

		$action = $elxis->makeAURL('content:plugin/?task=handler', 'inner.php');
		elxisLoader::loadFile('includes/libraries/elxis/form5.class.php');
		$form = new elxis5Form(array('idprefix' => 'gal'));
		$form->openForm(array('name' => 'pluggalform', 'method' =>'post', 'action' => $action, 'id' => 'fmrtedit', 'enctype' => 'multipart/form-data', 'onsubmit' => 'plugGallerySubmit()'));
		$form->addNote($eLang->get('UPLOAD_CREATE_TP'), 'elx5_info');
		$options = array();
		$options[] = $form->makeOption('', '- '.$eLang->get('ROOT_FOLDER').' -');
		if ($foptions) {
			foreach ($foptions as $foption) { $options[] = $form->makeOption($foption, $foption); }
		}
		$form->addSelect('folder', $eLang->get('FOLDER'), '', $options, array('dir' => 'ltr'));
		$form->addText('newfolder', '', $eLang->get('NEW_SUBFOLDER'), array('forcedir' => 'ltr', 'size' => 15, 'maxlength' => 40));
		$form->addImage('ifile1', '', $eLang->get('IMAGE').' 1');
		$form->addText('icaption1', '', $eLang->get('CAPTION').' 1', array('forcedir' => 'ltr', 'size' => 40, 'maxlength' => 100));
		$form->addImage('ifile2', '', $eLang->get('IMAGE').' 2');
		$form->addText('icaption2', '', $eLang->get('CAPTION').' 2', array('forcedir' => 'ltr', 'size' => 40, 'maxlength' => 100));
		$form->addImage('ifile3', '', $eLang->get('IMAGE').' 3');
		$form->addText('icaption3', '', $eLang->get('CAPTION').' 3', array('forcedir' => 'ltr', 'size' => 40, 'maxlength' => 100));
		$form->addHTML('<div class="elx5_vpad">');
		$form->addButton('upload', $eLang->get('UPLOAD'), 'submit');
		$form->addHTML('</div>');
		$notice = $eLang->get('GALLERY_LIMIT_1')."<br />\n".$eLang->get('GALLERY_LIMIT_2')."<br />\n".$eLang->get('GALLERY_LIMIT_3');
		$form->addNote($notice, 'elx5_warning');
		$form->addHidden('task', 'handler');
		$form->addHidden('act', 'upload');
		$form->addHidden('id', $pluginid);
		$form->addHidden('fn', $fn);
		$form->addToken('pluggalform');
		$form->closeForm();
	}


	/**************************************/
	/* HANDLER: LIST FOLDER IMAGES (AJAX) */
	/**************************************/
	private function listImages($pluginid, $fn) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$fpath = '';
		if (isset($_POST['fpath'])) {
			$fpath = trim(preg_replace('#[^a-z0-9\-\_\(\)\/]#i', '', $_POST['fpath']));
			if (($fpath == '') || ($fpath != $_POST['fpath'])) {
				$this->ajaxHeaders('text/html');
				echo '<div class="elx5_warning">Requested path is invalid!'."</div>\n";
				exit;
			}
		}

		$relpath = $this->imagesRoot();
		if (($fpath == '') || !is_dir(ELXIS_PATH.'/'.$relpath.$fpath)) {
			$this->ajaxHeaders('text/html');
			echo '<div class="elx5_warning">Requested path not found!'."</div>\n";
			exit;
		}

		$images = eFactory::getFiles()->listFiles($relpath.$fpath, '(.gif)|(.jpeg)|(.jpg)|(.png)$');
		$this->ajaxHeaders('text/html');
		if (!$images) {
			echo '<div class="elx5_warning">'.$eLang->get('NO_IMAGES')."</div>\n";
			exit;
		}

		$captions = $this->getCaptions($relpath.$fpath);

		$total = count($images);
		$txt = sprintf($eLang->get('FOLDER_CONTAIN_IMAGES'), '<strong>'.$total.'</strong>');
		if ($total > 40) { $txt .= sprintf($eLang->get('ONLY_SHOWN'), '<strong>40</strong>'); }
		$baseURL = eFactory::getElxis()->secureBase().'/'.$relpath.$fpath;

		$can_delete = false;
		if ($elxis->acl()->check('com_emedia', 'files', 'edit') > 0) {
			if ($elxis->getConfig('SECURITY_LEVEL') < 2) { $can_delete = true; }
		}

		echo '<div class="elx5_sminfo">'.$txt."</div>\n";

		$rnd = rand(100, 999);
		$i = 1;

		echo '<table class="plug_gallery" id="plug_gallery'.$rnd.'">'."\n";
		foreach ($images as $k => $image) {
			$caption = '';
			if ($captions) {
				foreach ($captions as $cap) {
					if ($cap[0] == $image) {
						$caption = $cap[1];
						break;
					}
				}
			}

			$isize = getimagesize(ELXIS_PATH.'/'.$relpath.$fpath.$image);
			$fs = filesize(ELXIS_PATH.'/'.$relpath.$fpath.$image) / 1024;
			$fs_txt = number_format($fs, 2, $eLang->get('DECIMALS_SEP'), $eLang->get('THOUSANDS_SEP')).' KB';

			echo '<tr id="plugal'.$rnd.'_row'.$i.'"><td><img src="'.$baseURL.$image.'" alt="'.$image.'" title="'.$image.'" /></td>';
			echo '<td><span class="plug_gallery_note">'.$isize[0].' x '.$isize[1].'<br />'.$fs_txt.'</span></td>';
			echo '<td>';
			if ($caption == '') {
				echo '<a href="javascript:void(null);" id="plugal'.$rnd.'_img'.$i.'" onclick="plugGalleryAddCaption(\'plugal'.$rnd.'_img'.$i.'\', \'add\', '.$pluginid.', '.$fn.');" class="plug_gallery_addcap" data-img="'.$image.'" data-path="'.$fpath.'">'.$eLang->get('ADD_CAPTION').'</a>';
			} else {
				echo '<a href="javascript:void(null);" id="plugal'.$rnd.'_img'.$i.'" onclick="plugGalleryAddCaption(\'plugal'.$rnd.'_img'.$i.'\', \'edit\', '.$pluginid.', '.$fn.');" class="plug_gallery_editcap" data-img="'.$image.'" data-path="'.$fpath.'" title="'.$eLang->get('EDIT').'">'.$caption.'</a>';
			}
			if ($can_delete) {
				echo '<a href="javascript:void(null);" id="plugal'.$rnd.'_dimg'.$i.'" onclick="plugGalleryDeleteImage('.$rnd.', '.$i.', '.$pluginid.', '.$fn.');" class="plug_gallery_delete" data-img="'.$image.'" data-path="'.$fpath.'">'.$eLang->get('DELETE').'</a>';
			}
			echo "</td></tr>\n";
			if ($i > 39) { break; }
			$i++;
		}
		echo "</table>\n";
		exit;
	}


	/*********************************************/
	/* HANDLER: UPLOAD IMAGES AND CREATE FOLDERS */
	/*********************************************/
	private function uploadImages($pluginid, $fn) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eSession = eFactory::getSession();
		$eFiles = eFactory::getFiles();

		$redirurl = $elxis->makeAURL('content:plugin/', 'inner.php').'?id='.$pluginid.'&fn='.$fn;
		$sess_token = trim($eSession->get('token_pluggalform'));
		$token = trim(filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		if (($token == '') || ($sess_token == '') || ($sess_token != $token)) {
			echo '<div class="elx5_error">'.$eLang->get('REQDROPPEDSEC')."</div>\n";
			return;
		}

		if ($elxis->acl()->check('com_emedia', 'files', 'upload') < 1) { $elxis->redirect($redirurl); }
		if ($elxis->getConfig('SECURITY_LEVEL') > 1) { $elxis->redirect($redirurl); }

		$relpath = $this->imagesRoot();
		$folder = '';
		$newfolder = '';
		$uploadpath = '';
		if (isset($_POST['folder'])) {
			$folder = trim(preg_replace('#[^a-z0-9\_\-\/]#i', '', $_POST['folder']));
			if ($folder != $_POST['folder']) {  $elxis->redirect($redirurl); }
			if (!is_dir(ELXIS_PATH.'/'.$relpath.$folder)) { $elxis->redirect($redirurl); }
		}

		if (isset($_POST['newfolder'])) {
			$newfolder = trim(preg_replace('#[^a-z0-9\_\-]#i', '', $_POST['newfolder']));
			if ($newfolder != $_POST['newfolder']) { $elxis->redirect($redirurl); }
			$newfolder = strtolower($newfolder);
		}

		if ($folder == '') {
			if ($newfolder == '') { $elxis->redirect($redirurl); }
			if (!is_dir(ELXIS_PATH.'/'.$relpath.$newfolder.'/')) {
				$ok = $eFiles->createFolder($relpath.$newfolder.'/');
				if (!$ok) { $elxis->redirect($redirurl); }
			}
			$uploadpath = $relpath.$newfolder.'/';
		} else {
			if (!is_dir(ELXIS_PATH.'/'.$relpath.$folder)) { $elxis->redirect($redirurl); }
			$level = substr_count($folder, '/');
			if ($newfolder != '') {
				if ($level >= 2) { $elxis->redirect($redirurl); }
				if (!is_dir(ELXIS_PATH.'/'.$relpath.$folder.$newfolder.'/')) {
					$ok = $eFiles->createFolder($relpath.$folder.$newfolder.'/');
					if (!$ok) { $elxis->redirect($redirurl); }
				}
				$uploadpath = $relpath.$folder.$newfolder.'/';
			} else {
				$uploadpath = $relpath.$folder;
			}
		}

		if (!isset($_FILES) || (count($_FILES) == 0)) { $elxis->redirect($redirurl); }
		$valid_exts = array('jpg', 'jpeg', 'png', 'gif');

		$captions = array();
		for ($i=1; $i < 4; $i++) {
			if (!isset($_FILES['ifile'.$i])) { continue; }
			$upf = $_FILES['ifile'.$i];
		 	if (($upf['name'] != '') && ($upf['error'] == 0) && ($upf['size'] > 0)) {
		 		$filename = strtolower(preg_replace('#[^a-zA-Z0-9\_\-\.]#', '', $upf['name']));
		 		$info = $eFiles->getNameExtension($filename);
		 		$ext = strtolower($info['extension']);
		 		if (($ext == '') || !in_array($ext, $valid_exts)) { continue; }
		 		if ($info['name'] == '') { $filename = 'image_'.rand(1000,9999).'.'.$ext; }
		 		if (file_exists(ELXIS_PATH.'/'.$uploadpath.$filename)) { continue; }
		 		$ok = $eFiles->upload($upf['tmp_name'], $uploadpath.$filename);
		 		if ($ok) {
		 			$idx = 'icaption'.$i;
					$caption = filter_input(INPUT_POST, $idx, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
					$pat = "#([\']|[\"]|[\$]|[\<]|[\>]|[\%]|[\`]|[\^]|[\|]|[\\\])#u";
					$caption = eUTF::trim(preg_replace($pat, '', $caption));
					if ($caption != '') {
						$captions[] = array($filename, $caption);
					}
		 		}
	 		}
		}

		if ($captions) {
			$ok = $this->saveCaptions($uploadpath, $captions);
		}

		$elxis->redirect($redirurl);
	}


	/******************************/
	/* HANDLER: SET IMAGE CAPTION */
	/******************************/
	private function setImageCaption($pluginid, $fn) {
		$relpath = $this->imagesRoot();
		$gallerypath = '';
		$galleryimage = '';
		$imagecaption = '';

		if (isset($_POST['fpath'])) {
			$v = trim(preg_replace('#[^a-z0-9\_\-\/]#i', '', $_POST['fpath']));
			if (($v != '') && ($v == $_POST['fpath'])) {
				if (is_dir(ELXIS_PATH.'/'.$relpath.$v)) {
					$gallerypath = $relpath.$v;
				}
			}
		}

		if (isset($_POST['image'])) {
			$v = filter_input(INPUT_POST, 'image', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
			$v = trim(preg_replace('#[^a-zA-Z0-9\_\-\.]#', '', $v));
			if (($v != '') && ($v == $_POST['image']) && ($gallerypath != '')) {
				if (file_exists(ELXIS_PATH.'/'.$gallerypath.$v)) { $galleryimage = $v; }
			}
		}

		$v = filter_input(INPUT_POST, 'caption', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$pat = "#([\']|[\"]|[\$]|[\<]|[\>]|[\%]|[\`]|[\^]|[\|]|[\\\])#u";
		$imagecaption = eUTF::trim(preg_replace($pat, '', $v));

		$ok = false;
		if (($gallerypath != '') && ($galleryimage != '') && ($imagecaption != '')) {
			$captions = array();
			$captions[] = array($galleryimage, $imagecaption);
			$ok = $this->saveCaptions($gallerypath, $captions);
		}

		if ($ok == true) {
			$json = array('success' => 1, 'errormsg' => '');
		} else {
			$json = array('success' => 0, 'errormsg' => eFactory::getLang()->get('ACTION_FAILED'));
		}

		if (ob_get_length() > 0) { ob_end_clean(); }
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').'GMT');
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header('Content-type: application/json; charset=utf-8');
		echo json_encode($json);
		exit;
	}


	/*************************/
	/* HANDLER: DELETE IMAGE */
	/*************************/
	private function deleteImage($pluginid, $fn) {
		$elxis = eFactory::getElxis();

		$can_delete = false;
		if ($elxis->acl()->check('com_emedia', 'files', 'edit') > 0) {
			if ($elxis->getConfig('SECURITY_LEVEL') < 2) { $can_delete = true; }
		}

		$relpath = $this->imagesRoot();
		$gallerypath = '';
		$galleryimage = '';

		if (isset($_POST['fpath'])) {
			$v = trim(preg_replace('#[^a-z0-9\_\-\/]#i', '', $_POST['fpath']));
			if (($v != '') && ($v == $_POST['fpath'])) {
				if (is_dir(ELXIS_PATH.'/'.$relpath.$v)) {
					$gallerypath = $relpath.$v;
				}
			}
		}

		if (isset($_POST['image'])) {
			$v = filter_input(INPUT_POST, 'image', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
			$v = trim(preg_replace('#[^a-zA-Z0-9\_\-\.]#', '', $v));
			if (($v != '') && ($v == $_POST['image']) && ($gallerypath != '')) {
				if (file_exists(ELXIS_PATH.'/'.$gallerypath.$v)) { $galleryimage = $v; }
			}
		}

		$ok = false;
		if ($can_delete && ($gallerypath != '') && ($galleryimage != '')) {
			$ok = eFactory::getFiles()->deleteFile($gallerypath.$galleryimage);
		}

		if ($ok == true) {
			$json = array('success' => 1, 'errormsg' => '');
		} else {
			$json = array('success' => 0, 'errormsg' => 'Action failed!');
		}

		if (ob_get_length() > 0) { ob_end_clean(); }
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').'GMT');
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header('Content-type: application/json; charset=utf-8');
		echo json_encode($json);
		exit;
	}


	/******************************/
	/* IMPORT REQUIRED CSS AND JS */
	/******************************/
	private function importCSSJS() {
		if (defined('PLUGGAL_LOADED')) { return; }

		$eDoc = eFactory::getDocument();
		$baselink = eFactory::getElxis()->secureBase().'/components/com_content/plugins/gallery/includes/';
		$sfx = eFactory::getLang()->getinfo('RTLSFX');
		$eDoc->addStyleLink($baselink.'gallery'.$sfx.'.css');
		if (!defined('GLIGHTBOX_LOADED')) {
			$eDoc->addStyleLink($baselink.'glightbox.min.css');
			$eDoc->addScriptLink($baselink.'glightbox.min.js');
		}
		define('PLUGGAL_LOADED', 1);
		define('GLIGHTBOX_LOADED', 1);
	}


	/********************************/
	/* MAKE IMAGE GALLERY HTML CODE */
	/********************************/
	private function makeHTML($fpath, $images, $id, $options) {
		$elxis_url_base = eFactory::getElxis()->secureBase();
		$images_root_relpath = $this->imagesRoot();

		$captions = $this->getCaptions($fpath);

		if ($options['columns'] == 4) {
			$columns = array(array(), array(), array(), array());
			$column_class = 'plugal_column4';
		} else {
			$columns = array(array(), array(), array());
			$column_class = 'plugal_column';
		}

		foreach ($images as $k => $image) {
			if ($options['thumbnails'] == 1) {
				$thumb = $this->getThumbnail($images_root_relpath, $fpath, $image, $options['thumbwidth'], $options['thumbheight'], $options['thumbmanip']);
			} else {
				$thumb = '';
			}
			if ($k == 0) { $columns[0][] = array($image, $thumb); continue; }
			$n = $k % $options['columns'];
			$columns[$n][] = array($image, $thumb);
		}

		$html = '<section itemscope itemtype="http://schema.org/ImageGallery" class="plugal_gallery" id="plugal_gallery'.$id.'">'."\n";
		foreach ($columns as $i => $colimages) {
			if (!$colimages) { continue; }
			$html .= '<div class="'.$column_class.'">'."\n";
			foreach ($colimages as $imagedata) {
				$caption = '';
				if ($captions) {
					foreach ($captions as $cap) {
						if ($cap[0] == $imagedata[0]) {
							$caption = $cap[1];
							break;
						}
					}
				}
				if ($caption == '') {
					if ($options['autocaptions'] == 1) { $caption = $this->makeCaption($imagedata[0]); }
				}

				$html .= '<figure itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject" class="plugal_item">'."\n";
				if ($caption == '') {
					$html .= '<a href="'.$elxis_url_base.'/'.$fpath.$imagedata[0].'" itemprop="contentURL" class="glightbox'.$id.'">';
					if ($imagedata[1] != '') {//thumbnail
						$html .= '<img src="'.$elxis_url_base.'/'.$imagedata[1].'" itemprop="image" alt="'.$imagedata[0].'" />';
					} else {
						$html .= '<img src="'.$elxis_url_base.'/'.$fpath.$imagedata[0].'" itemprop="image" alt="'.$imagedata[0].'" />';
					}
					$html .= "</a>\n";
				} else {
					$html .= '<a href="'.$elxis_url_base.'/'.$fpath.$imagedata[0].'" itemprop="contentURL" title="'.$caption.'" data-title="'.$caption.'" class="glightbox'.$id.'">';
					if ($imagedata[1] != '') {//thumbnail
						$html .= '<img src="'.$elxis_url_base.'/'.$imagedata[1].'" itemprop="image" alt="'.$caption.'" />';
					} else {
						$html .= '<img src="'.$elxis_url_base.'/'.$fpath.$imagedata[0].'" itemprop="image" alt="'.$caption.'" />';
					}
					$html .= "</a>\n";
					$html .= '<figcaption itemprop="caption description">'.$caption."</figcaption>\n";
				}
				$html .= "</figure>\n";
			}
			$html .= '</div>'."\n";//$column_class
		}
		$html .= "</section>\n";
		$html .= '<script>var pluggalbox'.$id.' = GLightbox({ selector: \'glightbox'.$id.'\'});</script>'."\n";

		return $html;
	}


	/***********************************************/
	/* GET IMAGE THUMBNAIL, IF NOT EXIST CREATE IT */
	/***********************************************/
	private function getThumbnail($images_root_relpath, $fpath, $image, $thumbwidth, $thumbheight, $thumbmanip) {
		$id = md5($fpath.','.$image.','.$thumbwidth.','.$thumbheight.','.$thumbmanip);
		$ext = substr(strrchr($image, '.'), 1);

		if (file_exists(ELXIS_PATH.'/'.$images_root_relpath.'plugin_gallery_thumbs/'.$id.'.'.$ext)) {
			return $images_root_relpath.'plugin_gallery_thumbs/'.$id.'.'.$ext;
		}

		$eFiles = eFactory::getFiles();
		if (!file_exists(ELXIS_PATH.'/'.$images_root_relpath.'plugin_gallery_thumbs/')) {
			$eFiles->createFolder($images_root_relpath.'plugin_gallery_thumbs/');
			$eFiles->createFile($images_root_relpath.'plugin_gallery_thumbs/index.html', '');
		}

		@copy(ELXIS_PATH.'/'.$fpath.$image, ELXIS_PATH.'/'.$images_root_relpath.'plugin_gallery_thumbs/'.$id.'.'.$ext);

		if ($thumbheight == 0) {
			$imginfo = getimagesize(ELXIS_PATH.'/'.$fpath.$image);
			$thumbheight = intval(($thumbwidth * $imginfo[1]) / $imginfo[0]);
		}

		$crop = ($thumbmanip == 1) ? true : false;
		$eFiles->resizeImage($images_root_relpath.'plugin_gallery_thumbs/'.$id.'.'.$ext, $thumbwidth, $thumbheight, $crop);

		return $images_root_relpath.'plugin_gallery_thumbs/'.$id.'.'.$ext;
	}


	/*********************/
	/* GET FOLDER IMAGES */
	/*********************/
	private function getImages($fpath, $ordering) {
		if (!is_dir(ELXIS_PATH.'/'.$fpath)) { return false; }
		if (strpos($fpath, 'media/images/') !== 0) { return false; }
		$images = eFactory::getFiles()->listFiles($fpath, '(.gif)|(.jpeg)|(.jpg)|(.png)$');
		if (!$images) { return false; }

		if ($ordering == 1) {
			usort($images, array('galleryPlugin', 'orderByName'));
			return $images;
		} else if (($ordering == 2) || ($ordering == 3)) {
			$temp = array();
			foreach ($images as $image) {
				$ts = filemtime(ELXIS_PATH.'/'.$fpath.$image);
				$temp[] = array('image' => $image, 'ts' => $ts);
			}
			$method = ($ordering == 2) ? 'orderNewer' : 'orderOlder';
			usort($temp, array('galleryPlugin', $method));
			$final = array();
			foreach ($temp as $tmp) { $final[] = $tmp['image']; }
			return $final;
		} else {
			return $images;
		}
	}


	/**********************************/
	/* ORDER IMAGES BY THEIR FILENAME */
	/**********************************/
	public static function orderByName($a, $b) {
		return strcmp($a, $b);
	}


	/**********************/
	/* NEWER IMAGES FIRST */
	/**********************/
	public static function orderNewer($a, $b) {
		if ($a['ts'] == $b['ts']) { return 0; }
		return ($a['ts'] < $b['ts']) ? 1 : -1;
	}


	/**********************/
	/* OLDER IMAGES FIRST */
	/**********************/
	public static function orderOlder($a, $b) {
		if ($a['ts'] == $b['ts']) { return 0; }
		return ($a['ts'] < $b['ts']) ? -1 : 1;
	}


	/***************/
	/* PLUGIN HELP */
	/***************/
	private function showHelp() {
?>
		<p class="galhelp">The <strong>Gallery</strong> plugin allows you to easily display image galleries inside articles. There are several settings on the Gallery plugin&apos;s 
		edit page. It is also possible to display multiple image galleries on a single article. Images should be located in a sub-folder of the <em>media/images/</em> path. The generated HTML 
		is SEO friendly and uses microdata in order your imaged to rank high on search engines.</p>
		<h3>Upload images and captions</h3>
		<p class="galhelp">You can upload up to 3 images per time. You can alternatively upload your images from Elxis Media manager. On each image you can set a caption 
		(strongly recommended). If caption text is not provided the plugin has an option (enabled by default) to generate automatically caption texts from image filenames. 
		For SEO reasons it is important to provide good names to your images. Example of good file name for an image: <em>Arsenal-football-team.jpg</em> If you dont want 
		to provide caption when you upload your images you can do that later.</p>
		<h3>Display a gallery</h3>
		<p class="galhelp">Select the folder that contains the images you want to display in gallery. Click on the button below the select box to create the ready to use plugin code. 
		That&apos;s it! The plugin will insert the integration code into your article.</p>

		<h3>Optional attributes</h3>
		<p class="galhelp"><strong>columns</strong>: Number of columns. Values: 3 (default) or 4</p>
		<p class="galhelp"><strong>thumbnails</strong>: General thumbnails? Values: 0 (No - default), 1 (Yes)</p>
		<p class="galhelp"><strong>thumbwidth</strong>: Thumbnails width in pixels. Values: Any integer greater than 60</p>
		<p class="galhelp"><strong>thumbheight</strong>: Thumbnails height in pixels. Values: Any integer greater than 60 or 0 for auto.</p>
		<p class="galhelp"><strong>thumbmanip</strong>: Thumbnails manipulation. Values: 0 (distortion - default), 1 (crop)</p>
		<p class="galhelp">{gallery columns="4" thumbnails="1" thumbwidth="200" thumbheight="200" thumbmanip="1"}relative/path/{/gallery}</p>
<?php 
	}


	/**********************************/
	/* GET IMAGE UPLOAD RELATIVE PATH */
	/**********************************/
	private function imagesRoot() {
		$relpath = 'media/images/';
		if (defined('ELXIS_MULTISITE')) {
			if (ELXIS_MULTISITE > 1) { $relpath = 'media/images/site'.ELXIS_MULTISITE.'/'; }
		}
		return $relpath;
	}


	/***************************************/
	/* ECHO PAGE HEADERS FOR AJAX REQUESTS */
	/***************************************/
	private function ajaxHeaders($type='text/plain') {
		if(ob_get_length() > 0) { ob_end_clean(); }
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').'GMT');
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header('Content-type: '.$type.'; charset=utf-8');
	}


	/************************/
	/* SAVE IMAGES CAPTIONS */
	/************************/
	private function saveCaptions($relpath, $newcaptions) {
		if (!file_exists(ELXIS_PATH.'/'.$relpath) || !is_dir(ELXIS_PATH.'/'.$relpath)) { return false; }
		if (!$newcaptions) { return false; }

		$captions = $this->getCaptions($relpath);
		foreach ($newcaptions as $newcap) {
			$file = $newcap[0];
			$text = $newcap[1];

			$found = false;
			if ($captions) {
				foreach ($captions as $i => $cap) {
					if ($cap[0] == $file) {
						$captions[$i][1] = $text;//update caption text
						$found = true;
						break;
					}
				}
			}
			if (!$found) {
				$captions[] = array($file, $text);
			}
		}

		$ok = true;
		if ($captions) {
			$n = count($captions) - 1;
			$txt = '';
			foreach ($captions as $i => $cap) {
				if ($i < $n) {
					$txt .= $cap[0].';'.$cap[1]."\n";
				} else {
					$txt .= $cap[0].';'.$cap[1];
				}
			}
			$ok = eFactory::getFiles()->createFile($relpath.'gallery_captions.txt', $txt, false, true);
		}
		return $ok;
	}


	/**********************/
	/* GET IMAGE CAPTIONS */
	/**********************/
	private function getCaptions($relpath) {
		$captions = array();
		if (!file_exists(ELXIS_PATH.'/'.$relpath.'gallery_captions.txt')) { return $captions; }
		$handle = fopen(ELXIS_PATH.'/'.$relpath.'gallery_captions.txt', 'r');
		if (!$handle) { return $captions; }
		while (!feof($handle)) {
			$line = fgets($handle);
			$line = str_replace(array("\r", "\n"), '', $line);
			if (trim($line) == '') { continue; }
			$parts = preg_split('#\;#', $line, 2, PREG_SPLIT_NO_EMPTY);
			if (count($parts) == 2) {
				$captions[] = array($parts[0], $parts[1]);
			}
		}
		fclose($handle);

		return $captions;
	}


	/************************************/
	/* MAKE CAPTION FROM IMAGE FILENAME */
	/************************************/
	private function makeCaption($image) {
		$pos = strpos($image, '.');
		$text = substr($image, 0, $pos);
		$text = preg_replace('#\-#', ' ', $text);
		$text = preg_replace('#\_#', ' ', $text);
		$text = preg_replace('#\"#', '', $text);
		$text = preg_replace('#\'#', '', $text);
		$text = ucfirst($text);
		return $text;
	}

}

?>