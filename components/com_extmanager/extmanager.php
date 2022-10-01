<?php 
/**
* @version		$Id: extmanager.php 2393 2021-04-07 19:54:28Z IOS $
* @package		Elxis
* @subpackage	Component Extensions Manager
* @copyright	Copyright (c) 2006-2021 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


if (defined('ELXIS_ADMIN')) {
	elxisLoader::loadFile('components/com_extmanager/controllers/base.php');
	elxisLoader::loadFile('components/com_extmanager/views/base.html.php');
}


class extmanagerRouter extends elxisRouter {

	private $controller = 'none';
	private $task = '';


	/**********************************************/
	/* ROUTE THE REQUEST TO THE PROPER CONTROLLER */
	/**********************************************/
	public function route() {
		if (!defined('ELXIS_ADMIN')) { exitPage::make('404', 'CEXT-0001'); }

		$this->makeAdminRoute();

		require(ELXIS_PATH.'/components/com_'.$this->component.'/controllers/'.$this->controller.'.php');
		require(ELXIS_PATH.'/components/com_'.$this->component.'/views/'.$this->controller.'.html.php');
		require(ELXIS_PATH.'/components/com_'.$this->component.'/models/'.$this->component.'.model.php');
		$class = $this->controller.ucfirst($this->component).'Controller';
		$viewclass = $this->controller.ucfirst($this->component).'View';
		$task = $this->task;
		if (!class_exists($class, false)) {
			exitPage::make('error', 'CEXT-0002', 'Class '.$class.' was not found in file '.$this->controller.'.php');
		}
		if (!method_exists($class, $task)) {
			exitPage::make('error', 'CEXT-0003', 'Task '.$task.' was not found in class '.$class.' in file '.$this->controller.'.php');
		}

		$view = new $viewclass();
		$model = new extmanagerModel();
		$controller = new $class($view, $task, $model);
		unset($view);
		$controller->$task();
	}


	/**************/
	/* MAKE ROUTE */
	/**************/
	private function makeAdminRoute() {
		if (count($this->segments) == 0) {
			$this->controller = 'install';
			$this->task = 'ipanel';
			return;
		}

		if ($this->segments[0] == 'install') {
			$this->controller = 'install';
			if (!isset($this->segments[1])) { $this->task = 'ipanel'; return; }
			switch ($this->segments[1]) {
				case 'install': $this->task = 'installextension'; break;
				case 'cupdate': $this->task = 'extcupdate'; break;
				case 'cinstall': $this->task = 'extcinstall'; break;
				case 'synchro': $this->task = 'syncextension'; break;
				case 'checkfs.html': $this->task = 'checkfilesystem'; break;
				case 'updates.html': $this->task = 'checkupdates'; break;
				case 'updatedb.html': $this->task = 'updateelxisdb'; break;
				case 'sysinstall': $this->task = 'installextensionsys'; break;
				case 'edc': $this->task = 'edcinstall'; break;
				case 'upelxis': $this->task = 'updateelxis'; break;
				default: break;
			}
			if ($this->task != '') { return; }
			exitPage::make('404', 'CEXT-0011');
		}

		if ($this->segments[0] == 'components') {
			$this->controller = 'components';
			if (!isset($this->segments[1])) { $this->task = 'listcomps'; return; }
			switch ($this->segments[1]) {
				case 'edit.html': $this->task = 'editcomponent'; break;
				case 'save.html': $this->task = 'savecomponent'; break;
				case 'delete': $this->task = 'deletecomponent'; break;
				default: break;
			}
			if ($this->task != '') { return; }
			exitPage::make('404', 'CEXT-0008');
		}

		if ($this->segments[0] == 'engines') {
			$this->controller = 'engines';
			if (!isset($this->segments[1])) { $this->task = 'listengs'; return; }
			switch ($this->segments[1]) {
				case 'edit.html': $this->task = 'editengine'; break;
				case 'save.html': $this->task = 'saveengine'; break;
				case 'delete': $this->task = 'deleteengine'; break;
				case 'toggle': $this->task = 'toggleengine'; break;
				case 'makedef': $this->task = 'makedefault'; break;
				case 'setordering': $this->task = 'setordering'; break;
				default: break;
			}
			if ($this->task != '') { return; }
			exitPage::make('404', 'CEXT-0012');
		}

		if ($this->segments[0] == 'auth') {
			$this->controller = 'auth';
			if (!isset($this->segments[1])) { $this->task = 'listauth'; return; }
			switch ($this->segments[1]) {
				case 'edit.html': $this->task = 'editauth'; break;
				case 'save.html': $this->task = 'saveauth'; break;
				case 'delete': $this->task = 'deleteauth'; break;
				case 'toggle': $this->task = 'toggleauth'; break;
				case 'setordering': $this->task = 'setordering'; break;
				default: break;
			}
			if ($this->task != '') { return; }
			exitPage::make('404', 'CEXT-0014');
		}

		if ($this->segments[0] == 'modules') {
			$this->controller = 'modules';
			if (!isset($this->segments[1])) { $this->task = 'listmods'; return; }
			switch ($this->segments[1]) {
				case 'add.html': $this->task = 'addmodule'; break;
				case 'edit.html': $this->task = 'editmodule'; break;
				case 'save.html': $this->task = 'savemodule'; break;
				case 'toggle': $this->task = 'togglemodule'; break;
				case 'copy': $this->task = 'copymodule'; break;
				case 'delete': $this->task = 'deletemodule'; break;
				case 'setordering': $this->task = 'setordering'; break;
				case 'positionorder': $this->task = 'positionorder'; break;
				default: break;
			}
			if ($this->task != '') { return; }
			exitPage::make('404', 'CEXT-0005');
		}

		if ($this->segments[0] == 'plugins') {
			$this->controller = 'plugins';
			if (!isset($this->segments[1])) { $this->task = 'listplugins'; return; }
			switch ($this->segments[1]) {
				case 'edit.html': $this->task = 'editplugin'; break;
				case 'save.html': $this->task = 'saveplugin'; break;
				case 'delete': $this->task = 'deleteplugin'; break;
				case 'toggle': $this->task = 'toggleplugin'; break;
				case 'setordering': $this->task = 'setordering'; break;
				case 'usage': $this->task = 'pluginusage'; break;
				default: break;
			}
			if ($this->task != '') { return; }
			exitPage::make('404', 'CEXT-0016');
		}

		if ($this->segments[0] == 'templates') {
			$this->controller = 'templates';
			if (!isset($this->segments[1])) { $this->task = 'listtempls'; return; }
			switch ($this->segments[1]) {
				case 'edit.html': $this->task = 'edittemplate'; break;
				case 'save.html': $this->task = 'savetemplate'; break;
				case 'delete': $this->task = 'deletetemplate'; break;
				case 'copy': $this->task = 'copytemplate'; break;
				case 'positions.html': $this->task = 'listpositions'; break;
				case 'saveposition': $this->task = 'saveposition'; break;
				case 'deleteposition': $this->task = 'deleteposition'; break;
				default: break;
			}
			if ($this->task != '') { return; }
			exitPage::make('404', 'CEXT-0009');
		}

		if ($this->segments[0] == 'browse') {
			$this->controller = 'browse';
			if (!isset($this->segments[1])) { $this->task = 'central'; return; }
			switch ($this->segments[1]) {
				case 'req': $this->task = 'requestedc'; break;
				default: break;
			}
			if ($this->task != '') { return; }
			exitPage::make('404', 'CEXT-0013');
		}

		exitPage::make('404', 'CEXT-0004');
	}

}

?>