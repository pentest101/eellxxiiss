<?php 
/**
* @version		$Id: fatal.php 2150 2019-03-10 19:40:40Z IOS $
* @package		Elxis
* @subpackage	Templates / System
* @copyright	Copyright (c) 2006-2019 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');

if (preg_match('#(\.png)$#i', $page->favicon)) {
	$favrel = 'rel="icon" type="image/png"';
} else {
	$favrel = 'rel="shortcut icon"';
}

echo $page->doctype."\n";
?>
<html<?php echo $page->htmlattributes; ?>>
<head>
	<base href="<?php echo $cfg->get('URL'); ?>/" />
	<meta http-equiv="content-type" content="<?php echo $page->contenttype; ?>; charset=utf-8" />
	<meta name="generator" content="Elxis - Open Source CMS" />
	<meta name="distribution" content="global" />
	<meta name="robots" content="noindex, follow" />
	<title>Fatal error - <?php echo $cfg->get('SITENAME'); ?></title>
	<meta name="description" content="<?php echo $page->message; ?>" />
	<link <?php echo $favrel; ?> href="<?php echo $page->favicon; ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta name="HandheldFriendly" content="true" />
	<link rel="stylesheet" href="<?php echo $page->secure_sitelink; ?>/templates/system/css/standard.css" type="text/css" media="all" />
	<link rel="stylesheet" href="<?php echo $page->secure_sitelink; ?>/templates/system/css/exit.css" type="text/css" media="all" />
</head>
<body>
	<div class="exit5_pgwrapin exit5_pgwrapinred">
		<div class="exit5_titlebox">
			<h1><?php echo $page->title; ?></h1>
			<div class="exit5_title_desc"><?php echo $page->msgtitle; ?></div>
		</div>
		<div class="exit5_message_error"><?php echo $page->message; ?></div>
		<div class="exit5_refcode">Reference code: <span><?php echo $page->refcode; ?></span></div>
		<div class="exit5_horlinks">
			<a href="<?php echo $page->sitelink; ?>" title="Home">Home</a>
			<a href="javascript:window.history.go(-1);" title="Back">Back</a>
		</div>
	</div><!-- exit5_pgwrapin -->
	<div class="exit5_pgfooter">
		<div class="exit5_pgfooter_copy"><a href="<?php echo $page->sitelink; ?>" title="<?php echo $cfg->get('SITENAME'); ?>"><?php echo $cfg->get('SITENAME'); ?></a> &copy; <?php echo date('Y'); ?></div>
		<a href="http://www.elxis.org" title="Elxis Open Source CMS" class="exit5_powerby">Powered by Elxis CMS &#169; 2006-<?php echo date('Y'); ?></a>
	</div>
</body>
</html>