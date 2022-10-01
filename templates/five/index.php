<?php 
/**
* @version		$Id$
* @package		Elxis CMS
* @subpackage	Templates / Five
* @author		Ioannis Sannos ( http://www.isopensource.com )
* @copyright	Copyleft (c) 2008-2019 Is Open Source (http://www.isopensource.com).
* @license		GNU/GPL ( http://www.gnu.org/copyleft/gpl.html )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
************************************************************************************************/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');

$eLang = eFactory::getLang();
$eDoc = eFactory::getDocument();
$elxis = eFactory::getElxis();

$eDoc->setMetaTag('viewport', 'width=device-width, initial-scale=1.0');
$relpath = $elxis->secureBase().'/templates/five/images/favicons/';
$eDoc->addLink($relpath.'fav192.png', '', 'apple-touch-icon', 'sizes="192x192"');
$eDoc->addLink($relpath.'fav152.png', '', 'apple-touch-icon', 'sizes="152x152"');
$eDoc->addLink($relpath.'fav96.png', '', 'apple-touch-icon', 'sizes="96x96"');
$eDoc->addLink($relpath.'fav32.png', 'image/png', 'icon', 'sizes="32x32"');
$eDoc->addLink($relpath.'fav16.png', 'image/png', 'icon', 'sizes="16x16"');
$eDoc->setFavicon($relpath.'fav16.png');
$eDoc->addScriptLink($elxis->secureBase().'/templates/five/includes/five.js');
$eDoc->addNativeDocReady('tpl5OnLoad();');
$eDoc->addFontAwesome(true);
unset($relpath);

elxisloader::loadFile('templates/five/includes/five.class.php');
$tpl5 = new templateFive();

$tplts = filemtime(ELXIS_PATH.'/templates/five/css/template'.$eLang->getinfo('RTLSFX').'.css');

echo $eDoc->getDocType()."\n";
?>
<html<?php echo $eDoc->htmlAttributes(); ?>>
<head>
	<?php $eDoc->showHead(); ?>
	<link rel="stylesheet" href="<?php echo $elxis->secureBase(); ?>/templates/five/css/template<?php echo $eLang->getinfo('RTLSFX'); ?>.css?v=<?php echo $tplts; ?>" type="text/css" />
</head>
<body>
	<div class="tpl5_wrap">
		<div class="<?php echo $tpl5->globalHeaderClass(); ?>" id="tpl5_header_all">
			<div class="<?php echo $tpl5->topHeaderClass(); ?>">
				<?php $tpl5->makeTopLine(); ?>
				<div class="tpl5_header_menu_line" id="tpl5_header_menu_line">
					<div class="tpl5_container<?php echo $tpl5->containerSuffix(); ?>">
						<div class="tpl5_logo">
							<a href="<?php echo $elxis->makeURL(); ?>" title="<?php echo $elxis->getConfig('SITENAME'); ?>">
								<div class="tpl5_logo_title"><?php echo $tpl5->getParam('title'); ?></div>
								<div class="tpl5_logo_slogan"><?php echo $tpl5->getParam('slogan'); ?></div>
							</a>
						</div>
						<div class="tpl5_menu_wrap">
							<a class="tpl5_mobmenu" href="javascript:void(null);" onclick="tpl5OpenMenu();" title="<?php echo $eLang->get('MENU'); ?>"><i class="fas fa-bars"></i></a>
							<nav class="tpl5_menu" id="tpl5_menu">
								<a class="tpl5_mobmenuclose" href="javascript:void(null);" onclick="tpl5CloseMenu();" title="<?php echo $eLang->get('CLOSE'); ?>"><i class="fas fa-times"></i><span> <?php echo $eLang->get('CLOSE'); ?></span></a>
								<?php $eDoc->modules('menu', 'none'); ?>
								<div class="clear"></div>
							</nav>
						</div>
					</div>
				</div>
				<div class="clear"></div>
			</div>
			<?php $tpl5->makeSliderHeaderModules(); ?>
		</div>

		<?php $tpl5->pathwayMarqueeSpace(); ?>

		<div class="tpl5_container<?php echo $tpl5->containerSuffix(); ?>" id="tpl5_maincontainer">
			<div class="tpl5_main">
<?php 
			if ($eDoc->countModules('top') > 0) {
				echo '<div class="tpl5_toppos">'."\n";
				$eDoc->modules('top');
				echo "</div>\n";
				echo '<div class="clear"></div>'."\n";
			}

			if ($tpl5->showColumn() == true) {
				echo '<div class="tpl5_maincol">'."\n";
				$eDoc->component();
				echo "</div>\n";
				echo '<div class="tpl5_sidecol">'."\n";
				$tpl5->sideBlocks($eDoc);
				echo "</div>\n";
				echo '<div class="clear"></div>'."\n";
			} else {
				$eDoc->component();
				echo '<div class="clear"></div>'."\n";
			}

			if ($eDoc->countModules('bottom') > 0) {
				echo '<div class="tpl5_pos_bottom">'."\n";
				$eDoc->modules('bottom');
				echo "</div>\n";
				echo '<div class="clear"></div>'."\n";
			}
?>
			</div>
		</div>
		<footer class="tpl5_footer" id="tpl5_footer">
			<div class="tpl5_container<?php echo $tpl5->containerSuffix(); ?>">
<?php 
			$tpl5->footerMods();

			if ($eDoc->countModules('footer') > 0) {
				echo '<div class="tpl5_footer_menu">'."\n";
				$eDoc->modules('footer', 'none');
				echo "</div>\n";
				echo '<div class="clear"></div>'."\n";
			}
?>
			<?php $tpl5->footerCopyIcons(); ?>
			</div>
		</footer>
	</div>
	<a href="javascript:void(null);" onclick="tpl5ScrollTop();" class="tpl5_to_top" title="Top" id="tpl5_to_top"><i class="fas fa-angle-up"></i></a>
	<!-- Template Five for Elxis CMS designed by Ioannis Sannos - https://www.isopensource.com -->
</body>
</html>