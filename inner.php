<?php 
/**
* @version		$Id: inner.php 178 2011-03-12 14:06:14Z datahell $
* @package		Elxis
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

define('_ELXIS_', 1);


define('ELXIS_PATH', str_replace(DIRECTORY_SEPARATOR, '/', dirname(__FILE__)));
define('ELXIS_SELF', pathinfo(__FILE__, PATHINFO_BASENAME));
define('ELXIS_INNER', 1);

require(ELXIS_PATH.'/includes/loader.php'); //bootstrap

?>