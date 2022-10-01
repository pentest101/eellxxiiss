<?php 
/**
* @version		$Id: req.php 2046 2019-01-27 20:30:44Z IOS $
* @package		Elxis
* @subpackage	Module Administration Users
* @copyright	Copyright (c) 2006-2019 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


function modAUsersGetVisitorInfo($row, $visbrowser, $iphelper) {
	$info = new stdClass;
	$info->browser = '';
	$info->deviceicon = '';
	$info->visitorname = $row->uname;
	$info->ipaddress = '';
	$info->os = '';

	switch ($visbrowser['type']) {
		case 'robot': 
			$info->visitorname = ($visbrowser['version'] != '') ? $visbrowser['name'].' '.$visbrowser['version'] : $visbrowser['name'];
			$info->deviceicon = 'fas fa-rocket';
		break;
		case 'feedreader': 
			$info->visitorname = ($visbrowser['version'] != '') ? $visbrowser['name'].' '.$visbrowser['version'] : $visbrowser['name'];
			$info->deviceicon = 'fas fa-rss';
		break;
		case 'emailclient': 
			$info->visitorname = ($visbrowser['version'] != '') ? $visbrowser['name'].' '.$visbrowser['version'] : $visbrowser['name'];
			$info->deviceicon = 'fas fa-envelope';
		break;
		case 'library': 
			$info->visitorname = ($visbrowser['version'] != '') ? $visbrowser['name'].' '.$visbrowser['version'] : $visbrowser['name'];
			$info->deviceicon = 'fas fa-code';
		break;
		case 'validator': 
			$info->visitorname = ($visbrowser['version'] != '') ? $visbrowser['name'].' '.$visbrowser['version'] : $visbrowser['name'];
			$info->deviceicon = 'fas fa-wheelchair';
		break;
		case 'mediaplayer': 
			$info->visitorname = ($visbrowser['version'] != '') ? $visbrowser['name'].' '.$visbrowser['version'] : $visbrowser['name'];
			$info->deviceicon = 'fab fa-youtube';
		break;
		case '':
			$info->visitorname = $row->uname;
		break;
		case 'desktop':
			$info->visitorname = $row->uname;
			if ($visbrowser['name'] != '') {
				$info->browser = $visbrowser['name'];
				if ($visbrowser['version'] != '') { $info->browser .= ' '.$visbrowser['version']; }
			}
			$info->deviceicon = 'fas fa-desktop';
		break;
		case 'mobile':
			$info->visitorname = $row->uname;
			if ($visbrowser['name'] != '') {
				$info->browser = $visbrowser['name'];
				if ($visbrowser['version'] != '') { $info->browser .= ' '.$visbrowser['version']; }
			}
			$info->deviceicon = 'fas fa-mobile-alt';
		break;
		case 'tablet':
			$info->visitorname = $row->uname;
			if ($visbrowser['name'] != '') {
				$info->browser = $visbrowser['name'];
				if ($visbrowser['version'] != '') { $info->browser .= ' '.$visbrowser['version']; }
			}
			$info->deviceicon = 'fas fa-tablet-alt';
		break;
		case 'tv':
			$info->visitorname = $row->uname;
			if ($visbrowser['name'] != '') {
				$info->browser = $visbrowser['name'];
				if ($visbrowser['version'] != '') { $info->browser .= ' '.$visbrowser['version']; }
			}
			$info->deviceicon = 'fas fa-tv';
		break;
		default:
			$info->visitorname = $row->uname;
			if ($visbrowser['name'] != '') {
				$info->browser = $visbrowser['name'];
				if ($visbrowser['version'] != '') { $info->browser .= ' '.$visbrowser['version']; }
			}
		break;
	}

	if (trim($row->ip_address) != '') {
		$info->ipaddress = $iphelper->ipv6tov4($row->ip_address);//if it is a IPv4 converted to v6, convert it back to v4
	}

	if ($visbrowser['os_name'] != '') {
		$info->os = $visbrowser['os_name'];
		if ($visbrowser['os_version'] != '') {
			$info->os .= ' '.$visbrowser['os_version'];
			if ($visbrowser['os_platform'] != '') { $info->os .= ' ('.$visbrowser['os_platform'].')'; }
		}
	}

	return $info;
}


$elxis = eFactory::getElxis();
$db = eFactory::getDB();
$eDate = eFactory::getDate();
$eLang = eFactory::getLang();

$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
if ($page < 1) { $page = 1; }
$sort = isset($_POST['sort']) ? trim($_POST['sort']) : 'timeasc';
if (($sort == '') || !in_array($sort, array('userasc', 'userdesc', 'timeasc', 'timedesc', 'clicksasc', 'clicksdesc'))) { $sort = 'timeasc'; }

$eLang->load('mod_adminusers', 'module');

$response = array(
	'success' => 1,
	'message' => '',
	'total' => 0,
	'page' => 1,
	'maxpage' => 1,
	'sort' => 'timeasc',
	'lngflogout' => $eLang->get('FORCE_LOGOUT'),
	'lngbanip' => $eLang->get('BAN_IP'),
	'lngnores' => $eLang->get('NO_RESULTS'),
	'lngos' => $eLang->get('OS'),
	'lngauth' => $eLang->get('AUTHENTICATION'),
	'lngbrowser' => $eLang->get('BROWSER'),
	'lngonlinetime' => $eLang->get('ONLINE_TIME'),
	'lngidletime' => $eLang->get('IDLE_TIME'),
	'lngclicks' => $eLang->get('CLICKS'),
	'lngpage' => $eLang->get('PAGE'),
	'lngpageof' => sprintf($eLang->get('PAGEOF'), '1', '1'),
	'visitors' => array()
);

$nowts = $eDate->getTS();
$ts = $nowts - $elxis->getConfig('SESSION_LIFETIME');

$sql = "SELECT COUNT(".$db->quoteId('uid').") FROM ".$db->quoteId('#__session')." WHERE ".$db->quoteId('last_activity')." > :ts";
$stmt = $db->prepareLimit($sql, 0, 1);
$stmt->bindParam(':ts', $ts, PDO::PARAM_INT);
$stmt->execute();
$response['total'] = (int)$stmt->fetchResult();

if ($response['total'] < 1) {
	$this->ajaxHeaders('application/json');
	echo json_encode($response);
	exit;
}

$response['maxpage'] = ($response['total'] == 0) ? 1 : ceil($response['total']/10);
$response['page'] = $page;
if ($response['page'] < 1) { $response['page'] = 1; }
if ($response['page'] > $response['maxpage']) { $response['page'] = $response['maxpage']; }
$response['sort'] = $sort;

switch ($sort) {
	case 'userasc': $orderby = 'u.uname ASC'; break;
	case 'userdesc': $orderby = 'u.uname DESC'; break;
	case 'clicksasc': $orderby = 's.clicks ASC'; break;
	case 'clicksdesc': $orderby = 's.clicks DESC'; break;
	case 'timedesc': $orderby = 's.last_activity ASC'; break;
	case 'timeasc': default: $orderby = 's.last_activity DESC'; $response['sort'] = 'timeasc'; break;
}

$limitstart = (($response['page'] - 1) * 10);

$sql = "SELECT s.uid, s.gid, s.login_method, s.first_activity, s.last_activity, s.clicks, s.current_page,"
."\n s.ip_address, s.user_agent, u.uname, u.groupname FROM ".$db->quoteId('#__session')." s"
."\n LEFT JOIN ".$db->quoteId('#__users')." u ON u.uid = s.uid"
."\n WHERE s.last_activity > :ts ORDER BY ".$orderby;
$stmt = $db->prepareLimit($sql, $limitstart, 10);
$stmt->bindParam(':ts', $ts, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_OBJ);

if (!$rows) {
	$this->ajaxHeaders('application/json');
	echo json_encode($response);
	exit;
}

$response['lngpageof'] = sprintf($eLang->get('PAGEOF'), $response['page'], $response['maxpage']);

$myip = eFactory::getSession()->getIP();
$iphelper = $elxis->obj('ip');

$browser = $elxis->obj('browser');

foreach ($rows as $row) {
	$visitor = array();

	$dt = $nowts - $row->first_activity;
	$ldt = $nowts - $row->last_activity;

	$h = floor($dt/3600);
	$rem = $dt - ($h * 3600);
	$min = floor($rem/60);
	$sec = $rem - ($min * 60);
	$visitor['time_online'] = ($h > 0) ? $h.':'.sprintf("%02d", $min).':'.sprintf("%02d", $sec) : sprintf("%02d", $min).':'.sprintf("%02d", $sec);

	$min = floor($ldt/60);
	$sec = $ldt - ($min * 60);
	$visitor['time_idle'] = $min.':'.sprintf("%02d", $sec);
	
	$visbrowser = $browser->getBrowser($row->user_agent, false);

	$visitor['clicks'] = $row->clicks;
	$visitor['uname'] = $row->uname;
	$visitor['groupname'] = $row->groupname;

	switch ($row->gid) {
		case 1: $visitor['groupname'] = $eLang->get('ADMINISTRATOR'); break;
		case 5: $visitor['groupname'] = $eLang->get('USER'); break;
		case 6:
			$visitor['groupname'] = $eLang->get('EXTERNALUSER');
			$visitor['uname'] = sprintf($eLang->get('EXT_UNAME'), ucfirst($row->login_method));
		break;
		case 7: case 0:
			$visitor['groupname'] = $eLang->get('GUEST');
			if ($visbrowser['type'] == 'robot') {
				$visitor['uname'] = ($visbrowser['name'] != '') ? $visbrowser['name'] : 'Robot';
			} else if ($visbrowser['type'] == 'feedreader') {
				$visitor['uname'] = ($visbrowser['name'] != '') ? $visbrowser['name'] : 'Fead reader';
			} else if ($visbrowser['type'] == 'validator') {
				$visitor['uname'] = ($visbrowser['name'] != '') ? $visbrowser['name'] : 'Validator';
			} else if ($visbrowser['type'] == 'library') {
				$visitor['uname'] = ($visbrowser['name'] != '') ? $visbrowser['name'] : 'Library';
			} else if ($visbrowser['type'] == 'mediaplayer') {
				$visitor['uname'] = ($visbrowser['name'] != '') ? $visbrowser['name'] : 'Media player';
			} else if ($visbrowser['type'] == 'emailclient') {
				$visitor['uname'] = ($visbrowser['name'] != '') ? $visbrowser['name'] : 'E-mail client';
			} else {
				$visitor['uname'] = $eLang->get('GUEST');
			}
		break;
		default:break;
	}

	if ($visitor['uname'] == '') { $visitor['uname'] = $eLang->get('GUEST'); }
	if ($visitor['groupname'] == '') { $visitor['groupname'] = $eLang->get('GUEST'); }

	$visitor['uid'] = (int)$row->uid;
	$visitor['gid'] = (int)$row->gid;
	$visitor['login_method'] = $row->login_method;
	$visitor['first_activity'] = $row->first_activity;
	$visitor['current_page'] = $row->current_page;

	$info = modAUsersGetVisitorInfo($row, $visbrowser, $iphelper);

	$visitor['ip_address'] = $info->ipaddress;
	$visitor['deviceicon'] = $info->deviceicon;
	$visitor['visitorname'] = ($info->visitorname == '') ? $eLang->get('GUEST') : $info->visitorname;
	$visitor['os'] = $info->os;
	$visitor['browser'] = $info->browser;
	$visitor['device'] = $visbrowser['type'];

	$visitor['canlogout'] = 0;
	$visitor['canban'] = 0;
	if (($elxis->user()->gid == 1) && ($row->gid <> 7) && ($row->uid <> $elxis->user()->uid) && ($info->ipaddress != $myip)) { $visitor['canlogout'] = 1; }
	if (($elxis->user()->gid == 1) && ($row->uid <> $elxis->user()->uid) && ($info->ipaddress != $myip)) { $visitor['canban'] = 1; }

	$response['visitors'][] = $visitor;
}

$this->ajaxHeaders('application/json');
echo json_encode($response);
exit;

?>