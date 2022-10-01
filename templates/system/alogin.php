<?php 
/**
* @version		$Id: alogin.php 2150 2019-03-10 19:40:40Z IOS $
* @package		Elxis
* @subpackage	Templates / System
* @copyright	Copyright (c) 2006-2019 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');
defined('ELXIS_ADMIN') or die('You can not access this file from this location!');

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
	<base href="<?php echo $elxis->secureBase().'/'.ELXIS_ADIR; ?>/" />
	<meta http-equiv="content-type" content="<?php echo $page->contenttype; ?>; charset=utf-8" />
	<meta name="generator" content="Elxis - Open Source CMS" />
	<meta name="distribution" content="global" />
	<meta name="robots" content="noindex, nofollow" />
	<title><?php echo $page->title.' - '.$elxis->getConfig('SITENAME'); ?></title>
	<meta name="description" content="<?php echo $page->msgtitle; ?>" />
	<link <?php echo $favrel; ?> href="<?php echo $page->favicon; ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta name="HandheldFriendly" content="true" />
	<link rel="stylesheet" href="<?php echo $elxis->secureBase().'/templates/system/css/standard'.$rtlsfx.'.css'; ?>" type="text/css" media="all" />
	<link rel="stylesheet" href="<?php echo $elxis->secureBase().'/templates/system/css/exit'.$rtlsfx.'.css'; ?>" type="text/css" media="all" />
	<script>
	function exit5SwitchLang() {
		if (!document.getElementById('exitlngfm')) { return; }
		var fmObj = document.getElementById('exitlngfm');
		var selObj = document.getElementById('exit5_select_lang');
		var admURL = selObj.options[selObj.selectedIndex].getAttribute('data-act');
		window.location.href = admURL;
	}
	</script>
</head>
<body>
	<div class="exit5_pgwrapin">
		<div class="exit5_titlebox">
			<h1><?php echo $page->title; ?></h1>
			<div class="exit5_title_desc"><?php echo $page->msgtitle; ?></div>
		</div>
<?php 
		if ($page->loginerror != '') {
			echo '<div class="exit5_message_error">'.$page->loginerror."</div>\n";
		} else if (($page->message != '') && ($page->confirmform == 1)) {
			echo '<div class="exit5_message">'.$page->message."</div>\n";
		} else {
			echo '<div class="elx5_invisible"></div>'."\n";
		}
?>
		<form name="fmelxisalogin" id="fmelxisalogin" method="post" action="<?php echo $page->loginaction; ?>" class="elx5_form" autocomplete="off">
<?php 
		if ($page->confirmform == 0) {
			echo '<div class="elx5_dspace">'."\n";
			echo '<label for="uname" class="exit5_label">'.$eLang->get('USERNAME')."</label>\n";
			echo '<input type="text" name="uname" id="uname" value="" dir="ltr" class="exit5_smpgtext" placeholder="'.$eLang->get('USERNAME').'" required="required" autocomplete="off" />'."\n";
			echo "</div>\n";
			echo '<div class="elx5_dspace">'."\n";
			echo '<label for="pword" class="exit5_label">'.$eLang->get('PASSWORD')."</label>\n";
			echo '<input type="password" name="pword" id="pword" value="" dir="ltr" class="exit5_smpgtext" placeholder="'.$eLang->get('PASSWORD').'" required="required" autocomplete="off" />'."\n";
			echo "</div>\n";
		} else {
			echo '<div class="elx5_dspace">'."\n";
			echo '<label for="pword" class="exit5_label">'.$eLang->get('PASSWORD')."</label>\n";
			echo '<input type="password" name="pword2" id="pword" value="" dir="ltr" class="exit5_smpgtext" placeholder="'.$eLang->get('PASSWORD').'" required="required" autocomplete="off" />'."\n";
			echo "</div>\n";
		}
?>
			<input type="hidden" name="remember" id="exit5_remember" dir="ltr" value="0" />
			<input type="hidden" name="auth_method" id="exit5_auth_method" dir="ltr" value="elxis" />
			<input type="hidden" name="return" id="exit5_return" dir="ltr" value="<?php echo $page->return; ?>" />
			<div class="elx5_dspace">
				<button type="submit" name="loginbtn" id="loginbtn" class="exit5_btn"><?php echo $page->buttontext; ?></button>
			</div>
		</form>

<?php 
		$flagsdir = $elxis->secureBase().'/includes/libraries/elxis/language/flags32/';
		$curlng = $eLang->currentLang();
		$admurl = $elxis->makeAURL();
		echo '<form name="exitlngfm" id="exitlngfm" method="get" action="'.$admurl.'" class="elx5_form">'."\n";
		echo '<div class="exit5_lang_wrap">'."\n";
		echo '<div class="exit5_downpick">'.$eLang->get('CHOOSE_LANG').'</div>';
		if (isset($page->infolangs[$curlng])) {
			echo '<img src="'.$flagsdir.$curlng.'.png" alt="'.$page->infolangs[$curlng]['NAME'].' - '.$page->infolangs[$curlng]['NAME_ENG'].'" title="'.$page->infolangs[$curlng]['NAME'].' - '.$page->infolangs[$curlng]['NAME_ENG'].'" /> ';
			echo '<div class="exit5_lang_selwrap">'."\n";
		}
		echo '<select name="lang" class="exit5_select_lang" id="exit5_select_lang" onchange="exit5SwitchLang();">'."\n";
		foreach ($page->infolangs as $lng => $info) {
			$selected = ($curlng == $lng) ? ' selected="selected"' : '';
			$link = $elxis->makeAURL($lng);
			echo '<option value="'.$lng.'"'.$selected.' data-act="'.$link.'">'.$info['NAME'].' - '.$info['NAME_ENG']."</option>\n";
		}
		echo "</select>\n";
		if (isset($page->infolangs[$curlng])) { echo "</div>\n"; }
		echo "</div>\n";
		echo "</form>\n";
?>
	</div><!-- exit5_pgwrapin -->
	<div class="exit5_pgfooter">
		<div class="exit5_pgfooter_copy"><a href="<?php echo $page->sitelink; ?>" title="<?php echo $elxis->getConfig('SITENAME'); ?>"><?php echo $elxis->getConfig('SITENAME'); ?></a> &copy; <?php echo date('Y'); ?></div>
		<a href="http://www.elxis.org" title="Elxis Open Source CMS" class="exit5_powerby">Powered by Elxis CMS &#169; 2006-<?php echo date('Y'); ?></a>
	</div>
</body>
</html>