<?php 

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


use Jodit\Application;

class JoditRestApplication extends Application {

	function checkPermissions() {
		if (!defined('ELXIS_ADMIN')) { return false; }
		if (intval(eFactory::getElxis()->user()->uid) < 1) {
			trigger_error('You are not authorized!', E_USER_WARNING);
		}
		return true;
	}

	function checkAuthentication() {
		if (!defined('ELXIS_ADMIN')) { return false; }
		if (intval(eFactory::getElxis()->user()->uid) < 1) {
			trigger_error('You are not authorized!', E_USER_WARNING);
		}
		return true;
	}
}