<?php 
/**
* @version		$Id: error.php 2150 2019-03-10 19:40:40Z IOS $
* @package		Elxis
* @subpackage	Templates / System
* @copyright	Copyright (c) 2006-2019 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


$rtlsfx = ($eLang->getinfo('DIR') == 'rtl') ? '-rtl' : '';
if (preg_match('#(\.png)$#i', $page->favicon)) {
	$favrel = 'rel="icon" type="image/png"';
} else {
	$favrel = 'rel="shortcut icon"';
}

$errormsg = $eLang->get('UNREC_ERROR_REQUEST');
if (trim($page->message) != '') {
	$errormsg .= ' '.$eLang->get('ERROR_DETAILS').': '.$page->message;
}

echo $page->doctype."\n";
?>
<html<?php echo $page->htmlattributes; ?>>
<head>
	<base href="<?php echo $elxis->getConfig('URL'); ?>/" />
	<meta http-equiv="content-type" content="<?php echo $page->contenttype; ?>; charset=utf-8" />
	<meta name="generator" content="Elxis - Open Source CMS" />
	<meta name="distribution" content="global" />
	<meta name="robots" content="index, follow" />
	<title><?php echo $page->title.' - '.$elxis->getConfig('SITENAME'); ?></title>
	<meta name="description" content="<?php echo $eLang->get('UNREC_ERROR_REQUEST'); ?>" />
	<link <?php echo $favrel; ?> href="<?php echo $page->favicon; ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta name="HandheldFriendly" content="true" />
	<link rel="stylesheet" href="<?php echo $elxis->secureBase().'/templates/system/css/standard'.$rtlsfx.'.css'; ?>" type="text/css" media="all" />
	<link rel="stylesheet" href="<?php echo $elxis->secureBase().'/templates/system/css/exit'.$rtlsfx.'.css'; ?>" type="text/css" media="all" />
</head>
<body>
	<div class="exit5_pgwrapin exit5_pgwrapinred">
		<div class="exit5_titlebox">
			<h1><?php echo $page->title; ?></h1>
			<div class="exit5_title_desc"><?php echo $page->msgtitle; ?></div>
		</div>
		<div class="exit5_message_error"><?php echo $errormsg; ?></div>
		<div class="exit5_refcode"><?php echo $eLang->get('REFERENCE_CODE').': <span>'.$page->refcode.'</span>'; ?></div>
		<div class="exit5_horlinks">
			<a href="<?php echo $page->sitelink; ?>" title="<?php echo $eLang->get('HOME'); ?>"><?php echo $eLang->get('HOME'); ?></a> 
			<a href="javascript:window.history.go(-1);" title="<?php echo $eLang->get('BACK'); ?>"><?php echo $eLang->get('BACK'); ?></a>
		</div>
	</div><!-- exit5_pgwrapin -->
	<div class="exit5_pgfooter">
		<div class="exit5_pgfooter_copy"><a href="<?php echo $page->sitelink; ?>" title="<?php echo $elxis->getConfig('SITENAME'); ?>"><?php echo $elxis->getConfig('SITENAME'); ?></a> &copy; <?php echo date('Y'); ?></div>
		<a href="http://www.elxis.org" title="Elxis Open Source CMS" class="exit5_powerby">Powered by Elxis CMS &#169; 2006-<?php echo date('Y'); ?></a>
	</div>
</body>
</html>