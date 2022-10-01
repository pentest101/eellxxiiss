<?php
/**
* @version		$Id: index.php 2449 2022-05-08 10:21:10Z IOS $
* @package		Elxis
* @subpackage	Onyx administration template
* @copyright	Copyright (c) 2006-2022 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


$eLang = eFactory::getLang();
$eDoc = eFactory::getDocument();
$elxis = eFactory::getElxis();

$tplurl = $elxis->secureBase().'/templates/admin/onyx';
$eDoc->setMetaTag('viewport', 'width=device-width, initial-scale=1.0');
$eDoc->addFontAwesome();
//$eDoc->addSimplebar();//TODO
$eDoc->addScrollbar();
$eDoc->addScriptLink($tplurl.'/js/onyx.js');
$eDoc->addStyleLink($tplurl.'/css/template'.$eLang->getinfo('RTLSFX').'.css');

echo $eDoc->getDocType()."\n";
?>
<html<?php echo $eDoc->htmlAttributes(); ?>>
<head>
	<?php $eDoc->showHead(); ?>
</head>
<body>
	<header class="onyx_cpheader">
		<div class="onyx_cplogo" id="onyx_cplogo">
			<a href="<?php echo $elxis->getConfig('URL'); ?>" title="<?php echo $elxis->getConfig('SITENAME'); ?>" target="_blank">
				<div id="onyx_minilogo"></div>
				<div id="onyx_sitename"><?php echo $elxis->getConfig('SITENAME'); ?></div>
				<div id="onyx_largelogo">Elxis <?php echo $elxis->getVersion().' <span>'.$elxis->fromVersion('CODENAME').'</span>'; ?></div>
			</a>
		</div>
		<a href="javascript:void(null);" class="onyx_menutoggle" onclick="onyxToggleSidebar();"><i class="fas fa-bars"></i></a>
		<?php $eDoc->toolbar(); ?>
		<div class="onyx_tools">
			<?php $eDoc->modules('tools', 'none'); ?>
		</div>
	</header>
	<aside class="onyx_sidenav" id="onyx_sidenav" data-status="open">
		<section class="onyx_sidescroll" id="onyx_sidescroll" data-simplebar="1">
			<?php $eDoc->modules('adminside', 'none'); ?>
			<?php $eDoc->module('mod_adminmenu', 'none'); ?>
		</section>
	</aside>
	<div class="onyx_contentwrap" id="onyx_contentwrap">
		<div class="onyx_container">
			<?php
			if ($eDoc->countModules('admintop') > 0) {
				echo '<div class="onyx_top_mods">'."\n";
				$eDoc->modules('admintop');
				echo "</div>\n";
			}
			$eDoc->component();
			if ($eDoc->countModules('adminbottom') > 0) {
				echo '<div class="onyx_bottom_mods">'."\n";
				$eDoc->modules('adminbottom');
				echo "</div>\n";
			}
			?>
		</div>
		<footer class="onyx_footer" id="onyx_footer">
			Elxis CMS v<?php echo $elxis->getVersion().' '.$elxis->fromVersion('CODENAME'); ?> -  Copyright &copy; 2006-<?php echo date('Y'); ?> 
			<a href="https://www.elxis.org" title="Elxis Open Source CMS" target="_blank">elxis.org</a>
		</footer>
	</div>
	<div class="elx5_pgloading" id="elx5_pgloading">
		<div class="elx5_pgloadingcon">
			<div class="elx5_pgloadingicon">&#160;</div>
			<div class="elx5_pgloadingtext"><?php echo $eLang->get('PLEASE_WAIT'); ?></div>
		</div>
	</div>
</body>
</html>