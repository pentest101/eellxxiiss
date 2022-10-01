<?php 
/**
* @version		$Id: api.php 834 2012-01-12 20:07:34Z datahell $
* @package		Elxis
* @subpackage	Component Translator
* @copyright	Copyright (c) 2006-2016 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class apiEtranslatorController extends etranslatorController {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null, $task='', $model=null) {
		parent::__construct($view, $task, $model);
	}


	/*****************************/
	/* LOAD TRANSLATION (STRING) */
	/*****************************/
	public function loadtranslation() {
		$pat = '#[^a-zA-Z0-9\_\-]#';
		$options = array();
		$options['category'] = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		$options['category'] = preg_replace($pat, '', $options['category']);
		$options['element'] = filter_input(INPUT_POST, 'element', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		$options['element'] = preg_replace($pat, '', $options['element']);
		$options['elid'] = (isset($_POST['elid'])) ? (int)$_POST['elid'] : 0;
		if ($options['elid'] < 0) { $options['elid'] = 0; }
		$options['language'] = filter_input(INPUT_POST, 'language', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		$options['language'] = preg_replace($pat, '', $options['language']);
		if ($options['language'] != '') {
			if (!file_exists(ELXIS_PATH.'/language/'.$options['language'].'/'.$options['language'].'.php')) { $options['language'] = ''; }
		}

		if (($options['category'] == '') || ($options['category'] != $_POST['category'])) {
			$this->view->apiError('Translation element Category is invalid!');
			return;
		}
		if (($options['element'] == '') || ($options['element'] != $_POST['element'])) {
			$this->view->apiError('Translation element Element is invalid!');
			return;
		}
		if ($options['elid'] == 0) {
			$this->view->apiError('To add a translation first save the original item!');
			return;
		}
		if (($options['language'] == '') || ($options['language'] != $_POST['language'])) {
			$this->view->apiError('Translation element Language is invalid!');
			return;
		}

		$row = $this->model->getTranslation($options['category'], $options['element'], $options['elid'], $options['language']);

		$this->view->loadResponse($options, $row);
	}


	/********************/
	/* SAVE TRANSLATION */
	/********************/
	public function savetrans() {//ELXIS 5.X
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$response = array('success' => 0, 'message' => '', 'trid' => 0);

		$pat = '#[^a-zA-Z0-9\_\-]#';
		$longtext = isset($_POST['longtext']) ? (int)$_POST['longtext'] : 0;
		$trid = isset($_POST['trid']) ? (int)$_POST['trid'] : 0;
		$elid = isset($_POST['elid']) ? (int)$_POST['elid'] : 0;
		$category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		$category = preg_replace($pat, '', $category);
		$element = filter_input(INPUT_POST, 'element', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		$element = preg_replace($pat, '', $element);
		$language = filter_input(INPUT_POST, 'language', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		$language = preg_replace($pat, '', $language);
		if ($language != '') {
			if (!file_exists(ELXIS_PATH.'/language/'.$language.'/'.$language.'.php')) { $language = ''; }
		}

		if ($longtext == 1) {
			$translation = filter_input(INPUT_POST, 'translation', FILTER_UNSAFE_RAW);
		} else {
			$translation = eUTF::trim(filter_input(INPUT_POST, 'translation', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		}

		$row = new translationsDbTable();
		if ($trid > 0) {
			if (!$row->load($trid)) {
				$response['message'] = $eLang->get('TRANS_NOT_FOUND');
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit;
			}
		}

		if ($category == '') {
			$response['message'] = 'Translation element Category is invalid!';
		} else if ($element == '') {
			$response['message'] = 'Translation element Element is invalid!';
		} else if ($elid < 1) {
			$response['message'] = $eLang->get('FIRST_SAVE_ORIG');
		} else if ($language == '') {
			$response['message'] = 'Translation element Language is invalid!';
		} else if ($translation == '') {
			$response['message'] = $eLang->get('PROVIDE_TRANS');
		}

		if ($response['message'] != '') {
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		if ($trid > 0) {
			if (($category != $row->category) || ($element != $row->element) || ($language != $row->language) || ($elid != $row->elid)) {
				$response['message'] = 'Saved translation data do not match the provided ones!';
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit;
			}
		} else {
			$row->category = $category;
			$row->element = $element;
			$row->language = $language;
			$row->elid = $elid;
		}

		$row->translation = $translation;

		if (preg_match('/^(com\_)/i', $category)) {
			$component = strtolower($category);
			if ($elxis->acl()->check('component', $component, 'manage') < 1) {
				$response['message'] = 'You are not allowed to manage component '.$component;
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit;
			}
		}

		if ($category == 'module') {
			if (preg_match('/^(mod\_)/i', $element)) {
				$module = strtolower($element);
				if ($elxis->acl()->check('module', $module, 'manage', $elid) < 1) {
					$response['message'] = 'You are not allowed to edit '.$module.' with instance '.$elid.'!';
					$this->ajaxHeaders('application/json');
					echo json_encode($response);
					exit;
				}
			}
		}

		if (!$row->store()) {
			$response['message'] = $eLang->get('TRANS_CNOT_SAVE');
			return;
		} else {
			$response['trid'] = $row->trid;
			$response['success'] = 1;
		}

		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/*************************************/
	/* SAVE ALL TRANSLATIONS (ELXIS 5.X) */
	/*************************************/
	public function savealltrans() {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();

		$pat = '#[^a-zA-Z0-9\_\-]#';
		$category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		$category = preg_replace($pat, '', $category);
		$element = filter_input(INPUT_POST, 'element', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		$element = preg_replace($pat, '', $element);
		$elid = (isset($_POST['elid'])) ? (int)$_POST['elid'] : 0;
		if ($elid < 0) { $elid = 0; }

		if (($category == '') || ($category != $_POST['category'])) {
			echo '<div class="elx5_error">Translation element Category is invalid!</div>';
			return;
		}
		if (($element == '') || ($element != $_POST['element'])) {
			echo '<div class="elx5_error">Translation element Element is invalid!</div>';
			return;
		}
		if ($elid == 0) {
			echo '<div class="elx5_error">'.$eLang->get('FIRST_SAVE_ORIG').'</div>';
			return;
		}

		if (preg_match('/^(com\_)/i', $category)) {
			$component = strtolower($category);
			if ($elxis->acl()->check('component', $component, 'manage') < 1) {
				echo '<div class="elx5_error">You are not allowed to manage component '.$component.'</div>';
				return;
			}
		}

		if ($category == 'module') {
			if (preg_match('/^(mod\_)/i', $element)) {
				$module = strtolower($element);
				if ($elxis->acl()->check('module', $module, 'manage', $elid) < 1) {
					echo '<div class="elx5_error">You are not allowed to edit '.$module.' with instance '.$elid.'!</div>';
					return;
				}
			}
		}

		//save translations
		$sitelangs = $eLang->getSiteLangs(false);
		$translations = array('translation' => array());
		foreach ($sitelangs as $lng) {
			if ($lng == $elxis->getConfig('LANG')) { continue; }
			$idx = 'translation_'.$lng;
			//if ($is_text) {
			//	$translations['translation'][$lng] = filter_input(INPUT_POST, $idx, FILTER_UNSAFE_RAW);
			//} else {
				$translations['translation'][$lng] = eUTF::trim(filter_input(INPUT_POST, $idx, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
			//}
		}

		$elxis->obj('translations')->saveElementTranslations($category, $element, $elid, $translations['translation']);

		echo '<div class="elx5_info">'.$eLang->get('TRANS_SAVED').'</div>';
	}


	/********************/
	/* SAVE TRANSLATION */
	/********************/
	public function savetranslation($is_text=false) {//Elxis 4.x compatibility
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();

		$trid = isset($_POST['trid']) ? (int)$_POST['trid'] : 0;
		if ($trid < 0) { $trid = 0; }
		
		$row = new translationsDbTable();
		if ($trid > 0) {
			if (!$row->load($trid)) {
				$this->view->apiError($eLang->get('TRANS_NOT_FOUND'));
				return;
			}
		}

		$pat = '#[^a-zA-Z0-9\_\-]#';
		$options = array();
		$category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		$category = preg_replace($pat, '', $category);
		$element = filter_input(INPUT_POST, 'element', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		$element = preg_replace($pat, '', $element);
		$elid = (isset($_POST['elid'])) ? (int)$_POST['elid'] : 0;
		if ($elid < 0) { $elid = 0; }
		$language = filter_input(INPUT_POST, 'language', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		$language = preg_replace($pat, '', $language);
		if ($language != '') {
			if (!file_exists(ELXIS_PATH.'/language/'.$language.'/'.$language.'.php')) { $language = ''; }
		}

		if (($category == '') || ($category != $_POST['category'])) {
			$this->view->apiError('Translation element Category is invalid!');
			return;
		}
		if (($element == '') || ($element != $_POST['element'])) {
			$this->view->apiError('Translation element Element is invalid!');
			return;
		}
		if ($elid == 0) {
			$this->view->apiError($eLang->get('FIRST_SAVE_ORIG'));
			return;
		}
		if (($language == '') || ($language != $_POST['language'])) {
			$this->view->apiError('Translation element Language is invalid!');
			return;
		}

		if ($is_text) {
			$translation = filter_input(INPUT_POST, 'translation', FILTER_UNSAFE_RAW);
		} else {
			$translation = eUTF::trim(filter_input(INPUT_POST, 'translation', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		}
		if ($translation == '') {
			$this->view->apiError($eLang->get('PROVIDE_TRANS'));
			return;
		}

		if ($trid > 0) {
			if (($category != $row->category) || ($element != $row->element) || ($language != $row->language) || ($elid != $row->elid)) {
				$this->view->apiError('Saved translation data do not match the provided ones!');
				return;
			}
		} else {
			$row->category = $category;
			$row->element = $element;
			$row->language = $language;
			$row->elid = $elid;
		}

		$row->translation = $translation;

		if (preg_match('/^(com\_)/i', $category)) {
			$component = strtolower($category);
			if ($elxis->acl()->check('component', $component, 'manage') < 1) {
				$this->view->apiError('You are not allowed to manage component '.$component);
				return;
			}
		}

		if ($category == 'module') {
			if (preg_match('/^(mod\_)/i', $element)) {
				$module = strtolower($element);
				if ($elxis->acl()->check('module', $module, 'manage', $elid) < 1) {
					$this->view->apiError('You are not allowed to edit '.$module.' with instance '.$elid.'!');
					return;
				}
			}
		}

		if (!$row->store()) {
			$this->view->apiError($eLang->get('TRANS_CNOT_SAVE'));
			return;
		}

		$this->view->saveResponse($row->trid, $eLang->get('TRANS_SAVED'));
	}


	/******************************/
	/* SAVE LONG TEXT TRANSLATION */
	/******************************/
	public function savettranslation() {//Elxis 4.x compatibility
		$this->savetranslation(true);
	}


	/**********************/
	/* DELETE TRANSLATION */
	/**********************/
	public function deletetranslation() {//Elxis 4.x compatibility
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();

		$trid = isset($_POST['trid']) ? (int)$_POST['trid'] : 0;
		if ($trid < 0) { $trid = 0; }
		if ($trid == 0) {
			$this->view->deleteResponse($eLang->get('TRANS_DELETED'));
			return;
		}

		$row = new translationsDbTable();
		if (!$row->load($trid)) {
			$this->view->apiError($eLang->get('TRANS_NOT_FOUND'));
			return;
		}

		$category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		$element = filter_input(INPUT_POST, 'element', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		$elid = (isset($_POST['elid'])) ? (int)$_POST['elid'] : 0;
		$language = filter_input(INPUT_POST, 'language', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		if ($category != $row->category) {
			$this->view->apiError('Option category does not match the saved one!');
			return;
		}
		if ($element != $row->element) {
			$this->view->apiError('Option element does not match the saved one!');
			return;
		}
		if ($elid != $row->elid) {
			$this->view->apiError('Option elid does not match the saved one!');
			return;
		}
		if ($language != $row->language) {
			$this->view->apiError('Option language does not match the saved one!');
			return;
		}

		if (preg_match('/^(com\_)/i', $row->category)) {
			$component = strtolower($row->category);
			if ($elxis->acl()->check('component', $component, 'manage') < 1) {
				$this->view->apiError('You are not allowed to manage component '.$component);
				return;
			}
		}

		if ($row->category == 'module') {
			if (preg_match('/^(mod\_)/i', $row->element)) {
				$module = strtolower($row->element);
				if ($elxis->acl()->check('module', $module, 'manage', $row->elid) < 1) {
					$this->view->apiError('You are not allowed to edit '.$module.' with instance '.$row->elid.'!');
					return;
				}
			}
		}

		if (!$row->delete()) {
			$this->view->apiError($eLang->get('ACTION_FAILED'));
			return;
		}

		$this->view->deleteResponse($eLang->get('TRANS_DELETED'));
	}


	/***************/
	/* BAD REQUEST */
	/***************/
	public function badrequest() {
		$this->view->apiError('Bad request!');
	}

}
	
?>