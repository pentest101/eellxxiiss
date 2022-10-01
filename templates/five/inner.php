<?php 
/**
* @version		$Id$
* @package		Elxis CMS
* @subpackage	Templates / Five
* @author		Ioannis Sannos ( https://www.isopensource.com )
* @copyright	Copyleft (c) 2008-2021 Is Open Source (https://www.isopensource.com).
* @license		GNU/GPL ( http://www.gnu.org/copyleft/gpl.html )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
************************************************************************************************/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


$eLang = eFactory::getLang();
$eDoc = eFactory::getDocument();
$elxis = eFactory::getElxis();

$touch_icon = $elxis->secureBase().'/templates/five/images/favicons/fav192.png';
$eDoc->setMetaTag('viewport', 'width=device-width, initial-scale=1.0');
$eDoc->addLink($touch_icon, '', 'apple-touch-icon');
unset($touch_icon);

$tplts = filemtime(ELXIS_PATH.'/templates/five/css/template'.$eLang->getinfo('RTLSFX').'.css');

echo $eDoc->getDocType()."\n";
?>
<html<?php echo $eDoc->htmlAttributes(); ?>>
<head>
	<?php $eDoc->showHead(); ?>
	<link rel="stylesheet" href="<?php echo $elxis->secureBase(); ?>/templates/five/css/template<?php echo $eLang->getinfo('RTLSFX'); ?>.css?v=<?php echo $tplts; ?>" type="text/css" />
</head>
<body class="innerpage" id="innerpage">
	<?php $eDoc->component(); ?>
</body>
</html>