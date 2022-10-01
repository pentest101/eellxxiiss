<?php 
/**
* @version		$Id: connector.html.php 1784 2016-02-15 19:21:11Z sannosi $
* @package		Elxis
* @subpackage	Component eMedia
* @copyright	Copyright (c) 2006-2016 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class connectorMediaView extends emediaView {


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/***********************/
	/* ERROR JSON RESPONSE */
	/***********************/
	public function errorResponse($errormsg, $textarea=false) {
    	$properties = array('Date_Created' => null, 'Date_Modified' => null, 'Height' => null, 'Width' => null, 'Size' => null);
    	$response = array('Error' => $errormsg, 'Code' => '-1', 'Properties' => $properties);

		if ($textarea) {
			$this->pageHeaders('text/plain');
			echo '<textarea>'.json_encode($response).'</textarea>';
		} else {
			$this->pageHeaders('application/json');
			echo json_encode($response);
		}
		exit;
	}


	/*************************/
	/* SUCCESS JSON RESPONSE */
	/*************************/
	public function jsonResponse($response, $textarea=false) {
		if ($textarea) {
			$this->pageHeaders('text/plain');
			echo '<textarea>'.json_encode($response).'</textarea>';
		} else {
			$this->pageHeaders('application/json');
			echo json_encode($response);
		}
		exit;
	}

}

?>