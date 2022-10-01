<?php
/**
* @version		$Id: inner.php 2102 2019-02-24 10:52:16Z IOS $
* @package		Elxis
* @subpackage	Onyx administration template
* @copyright	Copyright (c) 2006-2019 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


$eLang = eFactory::getLang();
$eDoc = eFactory::getDocument();
$elxis = eFactory::getElxis();

$tplurl = $elxis->secureBase().'/templates/admin/onyx/';
$eDoc->setMetaTag('viewport', 'width=device-width, initial-scale=1.0');
$eDoc->addStyleLink($tplurl.'css/template'.$eLang->getinfo('RTLSFX').'.css');
$eDoc->addScriptLink($tplurl.'js/onyx.js');

echo $eDoc->getDocType()."\n";
?>
<html<?php echo $eDoc->htmlAttributes(); ?>>
<head>
	<?php $eDoc->showHead(); ?>
</head>
<body class="innerpage" id="innerpage">
	<?php $eDoc->component(); ?>
</body>
</html>