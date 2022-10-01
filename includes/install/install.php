<?php 
/**
* @version		$Id: install.php 2311 2019-12-07 07:56:11Z IOS $
* @package		Elxis
* @subpackage	Installer
* @copyright	Copyright (c) 2006-2019 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


define('ELXIS_INSTALLER', 1);

require(ELXIS_PATH.'/includes/install/install.class.php');
$ielxis = new elxisInstaller();
$ielxis->process();

header('content-type:text/html; charset=utf-8');
header('Expires:Mon, 1 Jan 2001 00:00:00 GMT', true);
header('Last-Modified:'.gmdate("D, d M Y H:i:s").' GMT', true);
header('Cache-Control:no-store, no-cache, must-revalidate, post-check=0, pre-check=0', false);
header('Pragma:no-cache');
?>
<!DOCTYPE html>
<html lang="<?php echo $ielxis->langInfo('LANGUAGE'); ?>" dir="<?php echo $ielxis->langInfo('DIR'); ?>">
<head>
	<meta charset="UTF-8" />
	<meta name="generator" content="Elxis - Open Source CMS" />
	<meta name="author" content="Elxis Team" />
	<meta name="copyright" content="Copyright (C) 2006-<?php echo date('Y'); ?> elxis.org" />
	<meta name="distribution" content="global" />
	<meta name="robots" content="noindex, follow" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Elxis <?php echo $ielxis->verInfo('RELEASE').'.'.$ielxis->verInfo('LEVEL').' '.$ielxis->verInfo('CODENAME').' - '.$ielxis->getLang('INSTALLATION'); ?></title>
	<meta name="description" content="Elxis CMS installer" />
	<link rel="shortcut icon" href="<?php echo $ielxis->url; ?>/media/images/favicon.ico" />
	<link rel="stylesheet" href="<?php echo $ielxis->url; ?>/templates/system/css/standard<?php echo $ielxis->langInfo('RTLSFX'); ?>.css" type="text/css" media="all"  />
	<link rel="stylesheet" href="<?php echo $ielxis->url; ?>/includes/fontawesome/css/solid.min.css" type="text/css" media="all"  />
	<link rel="stylesheet" href="<?php echo $ielxis->url; ?>/includes/fontawesome/css/regular.min.css" type="text/css" media="all"  />
	<link rel="stylesheet" href="<?php echo $ielxis->url; ?>/includes/fontawesome/css/fontawesome.min.css" type="text/css" media="all"  />
	<link rel="stylesheet" href="<?php echo $ielxis->url; ?>/includes/fontawesome/css/v4-shims.min.css" type="text/css" media="all"  />
	<link rel="stylesheet" href="<?php echo $ielxis->url; ?>/includes/install/css/install<?php echo $ielxis->langInfo('RTLSFX'); ?>.css" type="text/css" media="all"  />
	<script src="<?php echo $ielxis->url; ?>/includes/js/elxis.js"></script>
	<script src="<?php echo $ielxis->url; ?>/includes/install/inc/install.js"></script>
</head>
<body>
<header class="ielx_header">
	<div class="ielx_header1">
		<div class="ielx_inner">
			<div class="ielx_logo">
				<a href="<?php echo $ielxis->url.'?lang='.$ielxis->currentLang(); ?>" title="Restart installation">
					<img src="<?php echo $ielxis->url; ?>/includes/install/css/elxislogo.png" alt="elxis cms" />
				</a>
			</div>
			<div class="ielx_langbox">
				<form name="ielx_fmlng" id="ielx_fmlng" method="get" class="ielx_fmlng" action="<?php echo $ielxis->url; ?>">
					<select name="lang" id="ielx_lang" class="ielx_lang" onchange="ielxSwitchLang();">
<?php 
					$clang = $ielxis->currentLang();
					$cstep = $ielxis->getStep();
					if ($cstep <= 1) {
						$ilangs = $ielxis->getiLangs();
						if ($ilangs) {
							foreach ($ilangs as $lng) {
								$sel = ($lng == $clang) ? ' selected="selected"' : '';
								echo '<option value="'.$lng.'"'.$sel.'>'.strtoupper($lng)."</option>\n";
							}
						}
					} else {
						echo '<option value="'.$clang.'" selected="selected">'.strtoupper($clang)."</option>\n";
					}
?>
					</select>
					<input type="hidden" name="step" value="1" />
				</form>
			</div>
		</div>
	</div>
	<div class="ielx_header2">
		<div class="ielx_inner">
			<div class="ielx_stepbox">
				<div class="ielx_step"><?php echo $ielxis->getStep(); ?></div><div class="ielx_steptitle"><?php echo $ielxis->stepTitle(); ?></div>
			</div>
			<div class="ielx_verbox">
				<div class="ielx_version">Elxis <?php echo $ielxis->verInfo('RELEASE').'.'.$ielxis->verInfo('LEVEL'); ?> <span><?php echo $ielxis->verInfo('CODENAME').' rev'.$ielxis->verInfo('REVISION'); ?></span></div>
			</div>
		</div>
	</div>
</header>
<div class="ielx_contents">
	<div class="ielx_inner">
		<div class="ielx_contentbox">
			<?php $ielxis->makehtml(); ?>
		</div>
	</div>
	<div id="elxisbaseurlx" style="display:none; visibility:hidden;" dir="ltr"><?php echo $ielxis->url; ?></div>
</div>
<footer class="ielx_footer">
	Powered by <a href="https://www.elxis.org/" target="_blank" title="Elxis CMS">Elxis open source CMS</a>. Copyright (c) 2006-<?php echo date('Y'); ?> Elxis Team.
</footer>
</body>
</html>