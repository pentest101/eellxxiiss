<?php 
/**
* @version		$Id: emenu.php 1695 2015-03-15 18:43:22Z sannosi $
* @package		Elxis
* @subpackage	Component eMenu
* @copyright	Copyright (c) 2006-2018 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


elxisLoader::loadFile('components/com_emenu/controllers/base.php');
elxisLoader::loadFile('components/com_emenu/views/base.html.php');


class emenuRouter extends elxisRouter {

	private $controller = 'collection';
	private $task = 'listcollections';


	/**********************************************/
	/* ROUTE THE REQUEST TO THE PROPER CONTROLLER */
	/**********************************************/
	public function route() {
		if (!defined('ELXIS_ADMIN')) { exitPage::make('404', 'CEME-0001'); }

		$this->makeAdminRoute();

		require(ELXIS_PATH.'/components/com_'.$this->component.'/controllers/'.$this->controller.'.php');
		require(ELXIS_PATH.'/components/com_'.$this->component.'/views/'.$this->controller.'.html.php');
		require(ELXIS_PATH.'/components/com_'.$this->component.'/models/'.$this->component.'.model.php');
		$class = $this->controller.ucfirst($this->component).'Controller';
		$viewclass = $this->controller.ucfirst($this->component).'View';
		$task = $this->task;
		if (!class_exists($class, false)) {
			exitPage::make('error', 'CEME-0002', 'Class '.$class.' was not found in file '.$this->controller.'.php');
		}
		if (!method_exists($class, $task)) {
			exitPage::make('error', 'CEME-0003', 'Task '.$task.' was not found in class '.$class.' in file '.$this->controller.'.php');
		}

		$view = new $viewclass();
		$model = new emenuModel();
		$controller = new $class($view, $task, $model);
		unset($view);
		$controller->$task();
	}


	/***********************/
	/* MAKE FRONTEND ROUTE */
	/***********************/
	private function makeAdminRoute() {
		$c = count($this->segments);
		if ($c == 0) {
			$this->task = 'listcollections';
			return;
		}

		if ($c == 1) {
			if ($this->segments[0] == 'deletecol') { //ajax
				$this->task = 'deletecollection';
				return;
			} else if ($this->segments[0] == 'savecol') { //ajax
				$this->task = 'savecollection';
				return;
			}
		}

		if (($c == 2) && ($this->segments[0] == 'mitems')) {
			$this->controller = 'menuitem';
			if ($this->segments[1] == 'togglestatus') { //ajax
				$this->task = 'toggleitem';
				return;
			} else if ($this->segments[1] == 'move') { //ajax
				$this->task = 'moveitemcol';
				return;
			} else if ($this->segments[1] == 'copy') { //ajax
				$this->task = 'copyitemcol';
				return;
			} else if ($this->segments[1] == 'add.html') {
				$this->task = 'additem';
				return;
			} else if ($this->segments[1] == 'moveitem') { //ajax
				$this->task = 'moveitem';
				return;
			} else if ($this->segments[1] == 'generator') { //ajax
				$this->task = 'linkgenerator';
				return;
			} else if ($this->segments[1] == 'delete') { //ajax
				$this->task = 'deleteitem';
				return;
			} else if ($this->segments[1] == 'edit.html') {
				$this->task = 'edititem';
				return;
			} else if ($this->segments[1] == 'save.html') {
				$this->task = 'saveitem';
				return;
			} else if ($this->segments[1] == 'browser.html') {
				$this->task = 'browser';
				return;
			} else {
				$this->task = 'listmenuitems';
				return;
			}
		}

		exitPage::make('404', 'CEME-0004');
	}

}

?>