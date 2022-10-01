<?php 
/**
* @version		$Id: navigation.helper.php 2185 2019-03-25 16:53:53Z IOS $
* @package		Elxis
* @subpackage	Helpers
* @copyright	Copyright (c) 2006-2019 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisNavigationHelper {


	/***************/
	/* CONSTRUCTOR */
	/***************/
	public function __construct() {
	}


	/*************************/
	/* MAKE NAVIGATION LINKS */
	/*************************/
	public function navLinks($linkbase, $page, $maxpage, $indicator=true) {//deprecated -> use html helper
		$html = eFactory::getElxis()->obj('html')->pagination($linkbase, $page, $maxpage);
		return $html;
	}

}

?>