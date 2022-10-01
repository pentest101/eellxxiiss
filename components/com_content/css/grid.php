<?php 
/**
* @version		$Id: grid.php 2410 2021-04-28 15:52:15Z IOS $
* @package		Elxis
* @subpackage	Component Content
* @copyright	Copyright (c) 2006-2021 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/


$l = 0;
$c = 100;
$r = 0;
$rtl = 0;
$w = 650;

if (isset($_GET['k'])) {
	$parts = explode('-', $_GET['k']);
	if (count($parts) == 5) {
		$l = (int)$parts[0];
		$c = (int)$parts[1];
		$r = (int)$parts[2];
		$rtl = (int)$parts[3];
		$w = (int)$parts[4];
	}
}
if ($w < 300) { $w = 650; }

$etag = 'fpgrid'.$l.''.$c.''.$r.''.$rtl.''.$w;
if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && ($_SERVER['HTTP_IF_NONE_MATCH'] == $etag)) {
	header('HTTP/1.1 304 Not Modified');
	exit;
}

$cols = ($l > 0) ? 1 : 0;
$cols += ($c > 0) ? 1 : 0;
$cols += ($r > 0) ? 1 : 0;

if ($cols == 0) {
	$c = 100;
	$cols = 1;
}

if ($rtl == 1) {
	$float = 'right';
	$margin1 = '0 0 0 1%';
	$margin2 = '0 0 0 2%';
	$margin1x = '0 0 0 1.1%';
} else {
	$float = 'left';
	$margin1 = '0 1% 0 0';
	$margin2 = '0 2% 0 0';
	$margin1x = '0 1.1% 0 0';
}

$gridcss = '.gridzero { margin:0; padding:0; }'."\n";
$gridcss = '.griddspace { margin:0 0 10px 0; padding:0; }'."\n";
if ($cols > 1) {
	$gridcss .= '.gridlcol, .gridlcolh { margin:0; padding:0; float:'.$float.'; width:'.$l.'%; }'."\n";
	$gridcss .= '.gridccol, .gridccolh { margin:0; padding:0; float:'.$float.'; width:'.$c.'%; }'."\n";
	$gridcss .= '.gridrcol, .gridrcolh { margin:0; padding:0; float:'.$float.'; width:'.$r.'%; }'."\n";
} else {
	$gridcss .= '.gridlcol, .gridlcolh { margin:0; padding:0; }'."\n";
	$gridcss .= '.gridccol, .gridccolh { margin:0; padding:0; }'."\n";
	$gridcss .= '.gridrcol, .gridrcolh { margin:0; padding:0; }'."\n";
}
$gridcss .= '.gridcell2, .gridcell2h { margin:0 0 10px 0; padding:0; }'."\n";
$gridcss .= '.gridcell4, .gridcell4h { margin:'.$margin2.'; padding:0; float:'.$float.'; width:49%; }'."\n";
$gridcss .= '.gridcell5, .gridcell5h { margin:0; padding:0; float:'.$float.'; width:49%; }'."\n";
$gridcss .= '.gridcell6, .gridcell6h { margin:'.$margin1.'; padding:0; float:'.$float.'; width:66%; }'."\n";
$gridcss .= '.gridcell7, .gridcell7h { margin:0; padding:0; float:'.$float.'; width:33%; }'."\n";
$gridcss .= '.gridcell8, .gridcell8h { margin:'.$margin1.'; padding:0; float:'.$float.'; width:33%; }'."\n";
$gridcss .= '.gridcell9, .gridcell9h { margin:0; padding:0; float:'.$float.'; width:66%; }'."\n";
$gridcss .= '.gridcell10, .gridcell10h { margin:0 0 10px 0; padding:0; }'."\n";
$gridcss .= '.gridcell11, .gridcell11h { margin:'.$margin1x.'; padding:0; float:'.$float.'; width:32.6%; }'."\n";
$gridcss .= '.gridcell12, .gridcell12h { margin:'.$margin1x.'; padding:0; float:'.$float.'; width:32.6%; }'."\n";
$gridcss .= '.gridcell13, .gridcell13h { margin:0; padding:0; float:'.$float.'; width:32.6%; }'."\n";
$gridcss .= '.gridcell14, .gridcell14h { margin:0 0 10px 0; padding:0; }'."\n";
$gridcss .= '.gridcell15, .gridcell15h { margin:'.$margin2.'; padding:0; float:'.$float.'; width:49%; }'."\n";
$gridcss .= '.gridcell16, .gridcell16h { margin:0; padding:0; float:'.$float.'; width:49%; }'."\n";
$gridcss .= '.gridcell17, .gridcell17h { margin:0 0 10px 0; padding:0; }'."\n";
//media rules
$gridcss .= '@media only screen and (max-width: '.$w.'px) {'."\n";
$gridcss .= '.gridlcol { margin:0 0 10px 0; display:block; float:none; width:100%; }'."\n";
$gridcss .= '.gridlcolh { display:none; visibility:hidden; float:none; width:100%; }'."\n";
$gridcss .= '.gridccol { display:block; float:none; width:100%; }'."\n";
$gridcss .= '.gridcell2h { margin:0 0 0 0; display:none; visibility:hidden; }'."\n";
$gridcss .= '.gridcell4 { margin:0 0 10px 0; display:block; float:none; width:100%; }'."\n";
$gridcss .= '.gridcell4h { margin:0 0 0 0; display:none; visibility:hidden; float:none; width:100%; }'."\n";
$gridcss .= '.gridcell5 { display:block; float:none; width:100%; }'."\n";
$gridcss .= '.gridcell5h { display:none; visibility:hidden; float:none; width:100%; }'."\n";
$gridcss .= '.gridcell6 { margin:0 0 10px 0; display:block; float:none; width:100%; }'."\n";
$gridcss .= '.gridcell6h { margin:0 0 0 0; display:none; visibility:hidden; float:none; width:100%; }'."\n";
$gridcss .= '.gridcell7 { display:block; float:none; width:100%; }'."\n";
$gridcss .= '.gridcell7h { display:none; visibility:hidden; float:none; width:100%; }'."\n";
$gridcss .= '.gridcell8 { margin:0 0 10px 0; display:block; float:none; width:100%; }'."\n";
$gridcss .= '.gridcell8h { margin:0 0 0 0; display:none; visibility:hidden; float:none; width:100%; }'."\n";
$gridcss .= '.gridcell9 { display:block; float:none; width:100%; }'."\n";
$gridcss .= '.gridcell9h { display:none; visibility:hidden; float:none; width:100%; }'."\n";
$gridcss .= '.gridcell10h { margin:0 0 0 0; display:none; visibility:hidden; }'."\n";
$gridcss .= '.gridcell11 { margin:0 0 10px 0; display:block; float:none; width:100%; }'."\n";
$gridcss .= '.gridcell11h { margin:0 0 0 0; display:none; visibility:hidden; float:none; width:100%; }'."\n";
$gridcss .= '.gridcell12 { margin:0 0 10px 0; display:block; float:none; width:100%; }'."\n";
$gridcss .= '.gridcell12h { margin:0 0 0 0; display:none; visibility:hidden; float:none; width:100%; }'."\n";
$gridcss .= '.gridcell13 { margin:0 0 0 0; display:block; float:none; width:100%; }'."\n";
$gridcss .= '.gridcell13h { display:none; visibility:hidden; float:none; width:100%; }'."\n";
$gridcss .= '.gridcell14h { margin:0 0 0 0; display:none; visibility:hidden; }'."\n";
$gridcss .= '.gridcell15 { margin:0 0 10px 0; display:block; float:none; width:100%; }'."\n";
$gridcss .= '.gridcell15h { margin:0 0 0 0; display:none; visibility:hidden; float:none; width:100%; }'."\n";
$gridcss .= '.gridcell16 { display:block; float:none; width:100%; }'."\n";
$gridcss .= '.gridcell16h { display:none; visibility:hidden; float:none; width:100%; }'."\n";
$gridcss .= '.gridcell17h { margin:0 0 0 0; display:none; visibility:hidden; }'."\n";
$gridcss .= '.gridrcol { margin:0 0 10px 0; display:block; float:none; width:100%; }'."\n";
$gridcss .= '.gridrcolh { display:none; visibility:hidden; float:none; width:100%; }'."\n";
$gridcss .= '}';

if (@ob_get_length() > 0) { ob_end_clean(); }
header('content-type:text/css');
header('Last-Modified: '.gmdate('D, d M Y H:i:s', time()).' GMT', true, 200);
header('ETag: '.$etag);
echo $gridcss;
exit;

?>