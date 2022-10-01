<?php 
/**
* @version		$Id: 404.php 2150 2019-03-10 19:40:40Z IOS $
* @package		Elxis
* @subpackage	Templates / System
* @copyright	Copyright (c) 2006-2019 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');

$xdata = array();
$xdata['menu'] = array();
if (defined('ELXIS_ADMIN')) {
	$xdata['basehref'] = $elxis->secureBase().'/'.ELXIS_ADIR.'/';
	$link = $elxis->makeAURL();
	$title = $eLang->exist('CONTROL_PANEL') ? $eLang->get('CONTROL_PANEL') : $eLang->get('HOME');
	$xdata['menu'][] = array('link' => $link, 'title' => $title, 'target' => '', 'fonticon' => 'fas fa-home');
	if ($elxis->acl()->check('com_cpanel', 'settings', 'edit') > 0) {
		$link = $elxis->makeAURL('cpanel:config.html');
		$title = $eLang->exist('SETTINGS') ? $eLang->get('SETTINGS') : 'Settings';
		$xdata['menu'][] = array('link' => $link, 'title' => $title, 'target' => '', 'fonticon' => 'fas fa-tools');
	}
	if ($elxis->acl()->check('component', 'com_content', 'manage') > 0) {
		$link = $elxis->makeAURL('content:categories/');
		$title = $eLang->exist('CATEGORIES') ? $eLang->get('CATEGORIES') : 'Categories';
		$xdata['menu'][] = array('link' => $link, 'title' => $title, 'target' => '', 'fonticon' => 'fas fa-folder-open');
		$link = $elxis->makeAURL('content:articles/');
		$title = $eLang->exist('ALL_ARTICLES') ? $eLang->get('ALL_ARTICLES') : $eLang->get('CONTENT');
		$xdata['menu'][] = array('link' => $link, 'title' => $title, 'target' => '', 'fonticon' => 'fas fa-file');
	}
	if ($elxis->acl()->check('component', 'com_user', 'manage') > 0) {
		$link = $elxis->makeAURL('user:users/');
		$title = $eLang->exist('USERS') ? $eLang->get('USERS') : 'Users';
		$xdata['menu'][] = array('link' => $link, 'title' => $title, 'target' => '', 'fonticon' => 'fas fa-users');
	}
	if ($elxis->acl()->check('component', 'com_extmanager', 'manage') > 0) {
		if ($elxis->acl()->check('com_extmanager', 'components', 'edit') > 0) {
			$link = $elxis->makeAURL('extmanager:components/');
			$title = $eLang->exist('COMPONENTS') ? $eLang->get('COMPONENTS') : 'Components';
			$xdata['menu'][] = array('link' => $link, 'title' => $title, 'target' => '', 'fonticon' => 'fas fa-cube');
		}
		if ($elxis->acl()->check('com_extmanager', 'modules', 'edit') > 0) {
			$link = $elxis->makeAURL('extmanager:modules/');
			$title = $eLang->exist('MODULES') ? $eLang->get('MODULES') : 'Modules';
			$xdata['menu'][] = array('link' => $link, 'title' => $title, 'target' => '', 'fonticon' => 'fas fa-puzzle-piece');
		}
		if ($elxis->acl()->check('com_extmanager', 'plugins', 'edit') > 0) {
			$link = $elxis->makeAURL('extmanager:plugins/');
			$title = $eLang->exist('CONTENT_PLUGINS') ? $eLang->get('CONTENT_PLUGINS') : 'Plugins';
			$xdata['menu'][] = array('link' => $link, 'title' => $title, 'target' => '', 'fonticon' => 'fas fa-plug');
		}
	}
	$xdata['menu'][] = array('link' => 'javascript:window.history.go(-1);', 'title' => $eLang->get('BACK'), 'target' => '', 'fonticon' => 'fas fa-undo');
} else {
	$xdata['basehref'] = $elxis->getConfig('URL').'/';
	if ($page->menu && (count($page->menu) > 0)) {
		$i = 0;
		foreach ($page->menu as $item) {
			if ($item->menu_type != 'link') { continue; }
			if ($i > 7) { break; }
			$ssl = ($item->secure == 1) ? true : false;
			if ($item->popup == 1) { continue; }
			$onclick = '';
			$link = $elxis->makeURL($item->link, $item->file, $ssl);
			$xdata['menu'][] = array('link' => $link, 'title' => $item->title, 'target' => $item->target, 'fonticon' => '');
		}
	} else {
		$xdata['menu'][] = array('link' => $page->sitelink, 'title' => $eLang->get('HOME'), 'target' => '', 'fonticon' => '');
	}
	$xdata['menu'][] = array('link' => 'javascript:window.history.go(-1);', 'title' => $eLang->get('BACK'), 'target' => '', 'fonticon' => '');
}

$rtlsfx = ($eLang->getinfo('DIR') == 'rtl') ? '-rtl' : '';
if (preg_match('#(\.png)$#i', $page->favicon)) {
	$favrel = 'rel="icon" type="image/png"';
} else {
	$favrel = 'rel="shortcut icon"';
}

echo $page->doctype."\n";
?>
<html<?php echo $page->htmlattributes; ?>>
<head>
	<base href="<?php echo $xdata['basehref']; ?>" />
	<meta http-equiv="content-type" content="<?php echo $page->contenttype; ?>; charset=utf-8" />
	<meta name="generator" content="Elxis - Open Source CMS" />
	<meta name="distribution" content="global" />
	<meta name="robots" content="noindex, follow" />
	<title><?php echo $page->title.' - '.$elxis->getConfig('SITENAME'); ?></title>
	<meta name="description" content="<?php echo $page->msgtitle; ?>" />
	<link <?php echo $favrel; ?> href="<?php echo $page->favicon; ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta name="HandheldFriendly" content="true" />
	<link rel="stylesheet" href="<?php echo $elxis->secureBase().'/templates/system/css/standard'.$rtlsfx.'.css'; ?>" type="text/css" media="all" />
<?php 
if (defined('ELXIS_ADMIN')) {
	echo '<link rel="stylesheet" href="'.$elxis->secureBase().'/includes/fontawesome/css/solid.min.css" type="text/css" media="all" />'."\n";
	echo '<link rel="stylesheet" href="'.$elxis->secureBase().'/includes/fontawesome/css/fontawesome.min.css" type="text/css" media="all" />'."\n";
}
?>
	<link rel="stylesheet" href="<?php echo $elxis->secureBase().'/templates/system/css/exit'.$rtlsfx.'.css'; ?>" type="text/css" media="all" />
</head>
<body>
	<div class="exit5_pgwrapin">
		<div class="exit5_titlebox">
			<h1><?php echo $page->title; ?></h1>
			<div class="exit5_title_desc"><?php echo $page->msgtitle; ?></div>
		</div>
		<div class="exit5_message"><?php echo $page->message; ?></div>
		<div class="exit5_errurl"><?php echo $page->url; ?></div>
		<div class="exit5_refcode"><?php echo $eLang->get('REFERENCE_CODE').': <span>'.$page->refcode.'</span>'; ?></div>
		<ul class="exit5_menu">
<?php 
		foreach ($xdata['menu'] as $item) {
			$trg = (($item['target'] != '_self') && ($item['target'] != '')) ? ' target="'.$item['target'].'"' : '';
			$iconhtml = ($item['fonticon'] != '') ? '<i class="'.$item['fonticon'].'"></i> ' : '';
			echo '<li><a href="'.$item['link'].'" title="'.$item['title'].'"'.$trg.'>'.$iconhtml.$item['title']."</a></li>\n";
		}
?>
		</ul>
	</div><!-- exit5_pgwrapin -->
	<div class="exit5_pgfooter">
		<div class="exit5_pgfooter_copy"><a href="<?php echo $page->sitelink; ?>" title="<?php echo $elxis->getConfig('SITENAME'); ?>"><?php echo $elxis->getConfig('SITENAME'); ?></a> &copy; <?php echo date('Y'); ?></div>
		<a href="http://www.elxis.org" title="Elxis Open Source CMS" class="exit5_powerby">Powered by Elxis CMS &#169; 2006-<?php echo date('Y'); ?></a>
	</div>
</body>
</html>