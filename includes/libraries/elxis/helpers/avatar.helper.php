<?php 
/**
* @version		$Id: avatar.helper.php 1832 2016-05-29 19:55:50Z sannosi $
* @package		Elxis
* @subpackage	Helpers / User Avatar
* @copyright	Copyright (c) 2006-2016 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisAvatarHelper {
	
	/***************/
	/* CONSTRUCTOR */
	/***************/
	public function __construct() {
	}


	/******************************/
	/* GET USER'S AVATAR FULL URL */
	/******************************/
	public function getAvatar($avatar='', $size=200, $use_gravatar=0, $email='') {
		$relpath = 'media/images/avatars/';
		if (defined('ELXIS_MULTISITE')) {
			if (ELXIS_MULTISITE > 1) { $relpath = 'media/images/site'.ELXIS_MULTISITE.'/avatars/'; }
		}

		if ((trim($avatar) != '') && file_exists(ELXIS_PATH.'/'.$relpath.$avatar)) {
			$out = eFactory::getElxis()->secureBase().'/'.$relpath.$avatar;
		} elseif ((trim($avatar) != '') && preg_match('#^(http(s)?\:\/\/)#', $avatar)) {
			$out = $avatar;
		} elseif ($use_gravatar && (trim($email) != '')) {
			$size = (int)$size;
			if ($size < 10) { $size = 200; }
			$out = 'https://www.gravatar.com/avatar/'.md5(strtolower($email)).'?s='.$size;
		} else {
			$out = eFactory::getElxis()->secureBase().'/components/com_user/images/noavatar.png';
		}
		return $out;
	}

}

?>