<?php 
/**
* @version		$Id: user.php 2361 2020-11-29 19:47:22Z IOS $
* @package		Elxis
* @subpackage	Component User
* @copyright	Copyright (c) 2006-2021 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


elxisLoader::loadFile('components/com_user/controllers/base.php');
elxisLoader::loadFile('components/com_user/views/base.html.php');


class userRouter extends elxisRouter {

	private $controller = '';
	private $task = '';
	private $json = false;
	private $auth = '';


	/**********************************************/
	/* ROUTE THE REQUEST TO THE PROPER CONTROLLER */
	/**********************************************/
	public function route() {
		if (defined('ELXIS_ADMIN')) {
			$this->makeAdminRoute();
		} else {
			$this->makeRoute();
		}
		require(ELXIS_PATH.'/components/com_'.$this->component.'/controllers/'.$this->controller.'.php');
		require(ELXIS_PATH.'/components/com_'.$this->component.'/views/'.$this->controller.'.html.php');
		require(ELXIS_PATH.'/components/com_'.$this->component.'/models/'.$this->component.'.model.php');
		$class = $this->controller.ucfirst($this->component).'Controller';
		$viewclass = $this->controller.ucfirst($this->component).'View';
		$task = $this->task;
		if (!class_exists($class, false)) {
			exitPage::make('error', 'CUSE-0001', 'Class '.$class.' was not found in file '.$this->controller.'.php');
		}
		if (!method_exists($class, $task)) {
			exitPage::make('error', 'CUSE-0002', 'Task '.$task.' was not found in class '.$class.' in file '.$this->controller.'.php');
		}

		$view = new $viewclass();
		$model = new userModel();
		if ($this->controller == 'aaccess') {
			$controller = new $class($view, $task, $model, $this->json);
		} else {
			$controller = new $class($view, $task, $model);
		}
		unset($view);

		if (($this->controller == 'account') && ($this->task == 'login')) {
			$controller->$task($this->auth);
		} else {
			$controller->$task();
		}
	}


	/***********************/
	/* MAKE FRONTEND ROUTE */
	/***********************/
	private function makeRoute() {
		$c = count($this->segments);
		if ($c == 0) {
			$this->controller = 'account';
			$this->task = 'userscentral';
			return;
		}

		if ($c > 2) {
			exitPage::make('404', 'CUSE-0003');
		}

		if ($this->segments[0] == 'pms') {
			$this->controller = 'members';
			if (!isset($this->segments[1])) {
				$this->task = 'pmthreads';
				return;
			}
			switch ($this->segments[1]) {
				case 'delete': $this->task = 'deletethread'; break;
				case 'send': $this->task = 'sendmessage'; break;
				default:
					if (preg_match('@(\.html)$@', $this->segments[1])) {
						$n = str_ireplace('.html', '', $this->segments[1]);
						if (is_numeric($n) && (intval($n) > 0)) { $this->task = 'readthread'; return; }
					}
				break;
			}
			if ($this->task == '') { exitPage::make('404', 'CUSE-0017'); }
			return;	
		}

		if ($this->segments[0] == 'bookmarks') {
			$this->controller = 'members';
			if (!isset($this->segments[1])) {
				$this->task = 'bookmarks';
				return;
			}
			switch ($this->segments[1]) {
				case 'load': $this->task = 'loadbookmark'; break;
				case 'delete': $this->task = 'deletebookmark'; break;
				case 'save': $this->task = 'savebookmark'; break;
				default: break;
			}
			if ($this->task == '') { exitPage::make('404', 'CUSE-0019'); }
		}

		if ($c == 1) {
			$this->controller = 'account';
			switch($this->segments[0]) {
				case 'login': $this->task = 'login'; break;
				case 'ilogin': $this->task = 'ilogin'; break;
				case 'logout.html': $this->task = 'logout'; break;
				case 'ilogout': $this->task = 'ilogout'; break;
				case 'register.html': $this->task = 'register'; break;
				case 'activate.html': $this->task = 'activate'; break;
				case 'recover-pwd.html': $this->task = 'recoverpass'; break;
				case 'resetpw.html': $this->task = 'changepass'; break;
				case 'changetz.html': $this->task = 'changetimezone'; break;
				case 'changelang.html': $this->task = 'changelanguage'; break;
				case 'userscentral': break;//don't allowed access to userscentral this way
				case 'members':
					$this->controller = 'members';
					$this->task = 'memberslist';
				break;
				case 'bookmarks.html'://old, deprecated, keep for backwards compatibility
					$this->controller = 'members';
					$this->task = 'bookmarks';
				break;
				case 'messages.html': case 'sentmessages.html'://old, deprecated, keep for backwards compatibility
					$this->controller = 'members';
					$this->task = 'pmthreads';
				break;
				default: break;
			}
			if ($this->task == '') {
				exitPage::make('404', 'CUSE-0004');
			}
			return;	
		}

		if ($this->segments[0] == 'members') {
			$this->controller = 'members';
			switch ($this->segments[1]) {
				case 'edit.html': $this->task = 'editprofile'; break;
				case 'save.html': $this->task = 'saveprofile'; break;
				case 'block.html': $this->task = 'blockaccount'; break;
				case 'delete.html': $this->task = 'deleteaccount'; break;
				case 'myprofile.html': $this->task = 'profile'; break; //shortcut for my own profile
				default:
					$n = str_ireplace('.html', '', $this->segments[1]);
					if (!is_numeric($n) || (intval($n) < 1)) {
						exitPage::make('404', 'CUSE-0005');
					}
					$this->task = 'profile';
				break;
			}
		}

		if ($this->segments[0] == 'login') {
			$this->controller = 'account';
			if (!preg_match('/(\.html)$/', $this->segments[1])) {
				exitPage::make('404', 'CUSE-0016');
			}
			$auth = str_replace('.html', '', $this->segments[1]);
			$auth = trim(preg_replace('/[^a-z\_\-0-9]/', '', $auth));
			if (($auth == '') || !file_exists(ELXIS_PATH.'/components/com_user/auth/'.$auth.'/'.$auth.'.auth.php')) {
				exitPage::make('404', 'CUSE-0015');
			}
			$this->auth = $auth;
			$this->task = 'login';
		}

		if ($this->task == '') {
			exitPage::make('404', 'CUSE-0006');
		}
	}


	/********************/
	/* MAKE ADMIN ROUTE */
	/********************/
	private function makeAdminRoute() {
		$this->controller = 'amembers';

		$c = count($this->segments);
		if ($c == 0) { //alias of user/amembers/
			$this->task = 'listusers';
			return;
		}

		if ($this->segments[0] == 'users') {
			$this->controller = 'amembers';
			if (!isset($this->segments[1])) { 
				$this->task = 'listusers';
				return;
			}
			switch ($this->segments[1]) {
				case 'toggleuser': $this->task = 'toggleuser'; break;
				case 'deleteuser': $this->task = 'deleteuser'; break;
				case 'edit.html': $this->task = 'edituser'; break;
				case 'save.html': $this->task = 'saveuser'; break;
				case 'mailuser': $this->task = 'mailuser'; break;
				default: $this->task = ''; break;
			}
			if ($this->task != '') { return; }
		}

		if ($this->segments[0] == 'groups') {
			$this->controller = 'agroups';
			if (!isset($this->segments[1])) { 
				$this->task = 'listgroups';
				return;
			}
			switch ($this->segments[1]) {
				case 'deletegroup': $this->task = 'deletegroup'; break;
				case 'getgroupdata': $this->task = 'getgroupdata'; break;
				case 'save': $this->task = 'savegroup'; break;
				default: $this->task = ''; break;
			}
			if ($this->task != '') { return; }
		}

		if ($this->segments[0] == 'acl') {
			$this->controller = 'aaccess';
			if (!isset($this->segments[1])) { $this->task = 'listacl'; return; }
			switch ($this->segments[1]) {
				case 'deleteacl': $this->task = 'deleteacl'; $this->json = true; break;
				case 'getacldata': $this->task = 'getacldata'; $this->json = true; break;
				case 'save': $this->task = 'save'; $this->json = true; break;
				case 'savejson': $this->task = 'savejson'; $this->json = true; break;//used by com_extmanager
				default: $this->task = ''; break;
			}
			if ($this->task != '') { return; }
		}

		exitPage::make('404', 'CUSE-0007');
	}

}

?>