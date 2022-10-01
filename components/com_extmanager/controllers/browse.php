<?php 
/**
* @version		$Id: browse.php 1492 2014-04-28 17:02:29Z sannosi $
* @package		Elxis
* @subpackage	Component Extensions Manager
* @copyright	Copyright (c) 2006-2020 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class browseExtmanagerController extends extmanagerController {

	private $edc = null;


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null, $task='', $model=null) {
		parent::__construct($view, $task, $model);
		$this->initEDC();
	}


	/*************************************/
	/* INITIALIZE ELXIS DOWNLOADS CENTER */
	/*************************************/
	private function initEDC() {
		$elxisid = '';
		$edcurl = '';
		$str = $this->model->componentParams();
		elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
		elxisLoader::loadFile('components/com_extmanager/includes/edc.class.php');
		$params = new elxisParameters($str, '', 'component');
		$this->edc = new elxisDC($params);
	}


	/******************************************************/
	/* PREPARE TO DISPLAY EXTENSIONS BROWSER CENTRAL PAGE */
	/******************************************************/
	public function central() {
		$eLang = eFactory::getLang();
		$pathway = eFactory::getPathway();
		$elxis = eFactory::getElxis();
		$eDoc = eFactory::getDocument();

		$eDoc->addFontAwesome();
		$eDoc->addFontElxis();
		$eDoc->addStyleLink($elxis->secureBase().'/components/com_extmanager/css/extmanager'.$eLang->getinfo('RTLSFX').'.css');//TODO: RTL
		$js = $this->getJSONLang();
		$eDoc->addScript($js);
		$eDoc->addScriptLink($elxis->secureBase().'/components/com_extmanager/js/extmanager.js');
		$eDoc->addNativeDocReady('extMan5EDCConnect();');

		$pathway->addNode($eLang->get('EXTENSIONS'), 'extmanager:/');
		$pathway->addNode($eLang->get('BROWSE'));
		$eDoc->setTitle($eLang->get('EXTENSIONS').' - '.$eLang->get('BROWSE'));

		$this->view->extCentral($this->edc);
	}


	/*************************************/
	/* GET REQUIRED JSON LANGUAGE STRING */
	/*************************************/
	private function getJSONLang() {
		$eLang = eFactory::getLang();
	
		$strings = array('PLEASE_WAIT', 'CONNECTING_EDC', 'LOADING_EDC', 'INSTALL', 'CANCEL', 'UPDATE', 'ELXISDC', 'SYSTEM_WARNINGS', 
		'AREYOUSURE', 'ACTION_WAIT', 'NAMEMAIL_ELXIS', 'INFO_STAY_PRIVE', 'CONTINUE', 'EDIT');

		$js = 'var edcLang = {'."\n";
		foreach ($strings as $string) {
			$js .= "\t".'\''.$string.'\':\''.addslashes($eLang->get($string)).'\','."\n";
		}
		$special1 = sprintf($eLang->get('ABOUT_TO_INSTALL'), 'X1', 'X2');
		$special2 = sprintf($eLang->get('ABOUT_TO_UPDATE_TO'), 'X1', 'X2');
		$special3 = sprintf($eLang->get('EXT_INST_SUCCESS'), 'X1', 'X2');
		$js .= "\t".'\'ABOUT_TO_INSTALL\':\''.addslashes($special1).'\','."\n";
		$js .= "\t".'\'ABOUT_TO_UPDATE_TO\':\''.addslashes($special2).'\','."\n";
		$js .= "\t".'\'EXT_INST_SUCCESS\':\''.addslashes($special3).'\''."\n";
		$js .= '};';

		return $js;
	}


	/**********************/
	/* HANDLE EDC REQUEST */
	/**********************/
	public function requestedc() {
		$options = array();
		$options['task'] = trim(filter_input(INPUT_POST, 'task', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		if ($options['task'] == '') { $options['task'] = 'nothing'; }
		$options['catid'] = isset($_POST['catid']) ? (int)$_POST['catid'] : 0;
		if ($options['catid'] < 0) { $options['catid'] = 0; }
		$options['fid'] = 0;//not used anymore
		$options['id'] = isset($_POST['id']) ? (int)$_POST['id'] : 0;
		if ($options['id'] < 0) { $options['id'] = 0; }
		$options['page'] = isset($_POST['page']) ? (int)$_POST['page'] : 1;
		if ($options['page'] < 1) { $options['page'] = 1; }
		$options['edcauth'] = trim(filter_input(INPUT_POST, 'edcauth', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));

		if ($options['task'] == 'auth') {
			$response = $this->edc->connect();
			$this->view->connectionResult($response);
			return;
		}

		if ($options['task'] == 'frontpage') {
			$lng = eFactory::getLang()->currentLang();
			$response = $this->edc->getFrontpage($lng, $options['edcauth']);
			$this->view->showFrontpage($response, $this->edc);
			return;
		}

		if ($options['task'] == 'category') {
			$response = $this->edc->getCategory($options);
			$this->view->showCategory($response, $this->edc);
			return;
		}

		if ($options['task'] == 'view') {
			$response = $this->edc->getExtension($options);
			$this->view->showExtension($response, $this->edc);
			return;
		}

		if ($options['task'] == 'author') {
			$options['uid'] = isset($_POST['uid']) ? (int)$_POST['uid'] : 0;
			$response = $this->edc->getAuthorExtensions($options);
			$this->view->showAuthorExtensions($response, $this->edc);
			return;
		}

		if ($options['task'] == 'search') {
			$options['keyword'] = strip_tags(filter_input(INPUT_POST, 'keyword', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
			if ($options['keyword'] != '') {
				$pat = "#([\']|[\"]|[\$]|[\#]|[\<]|[\>]|[\*]|[\%]|[\~]|[\`]|[\^]|[\|]|[\}]|[\\\])#u";
				$options['keyword'] = eUTF::trim(preg_replace($pat, '', $options['keyword']));
			}
			$response = $this->edc->searchExtensions($options);
			$this->view->showSearchExtensions($response, $this->edc);
			return;
		}

		$this->ajaxHeaders();
		exit;
	}

}
	
?>