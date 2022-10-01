<?php 
/**
* @version		$Id: asearch.html.php 2204 2019-04-10 18:42:59Z IOS $
* @package		Elxis
* @subpackage	Search component
* @copyright	Copyright (c) 2006-2019 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class asearchSearchView {


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
	}


	/*****************************/
	/* SHOW ACCESS ERROR MESSAGE */
	/*****************************/
	public function accessError($title, $msg) {
		echo '<h2>'.$title."</h2>\n";
		echo '<div class="elx5_error">'.$msg."</div>\n";
	}

}

?>