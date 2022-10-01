<?php 
/**
* @version		$Id$
* @package		Elxis
* @subpackage	Module Administration Articles
* @copyright	Copyright (c) 2006-2019 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


$popular = isset($_POST['popular']) ? (int)$_POST['popular'] : 0;
if ($popular != 1) { $popular = 0; }
$months = isset($_POST['months']) ? (int)$_POST['months'] : 0;
if (($months < 0) || ($months > 36)) { $months = 0; }//actually only up to "12" is used

$db = eFactory::getDB();
$elxis = eFactory::getElxis();
$eDate = eFactory::getDate();
$eLang = eFactory::getLang();

$eLang->load('mod_adminarticles', 'module');

$binds = array();
$sql = "SELECT a.id, a.catid, a.title, a.created, a.created_by, a.created_by_name, a.published, a.hits, c.title AS cattitle"
."\n FROM ".$db->quoteId('#__content')." a"
."\n LEFT JOIN ".$db->quoteId('#__categories')." c ON c.catid=a.catid";
if ($popular == 1) {
	if ($months > 0) {
		$ts = gmmktime(0, 0, 0, gmdate('m') - $months, gmdate('d'), gmdate('Y'));
		$date = gmdate('Y-m-d H:i:s', $ts);
		$binds[] = array(':crdate', $date, PDO::PARAM_STR);
		$sql .= " WHERE a.created > :crdate ORDER BY a.hits DESC";
	} else {
		$sql .= "\n ORDER BY a.hits DESC";
	}
} else {
	$sql .= "\n ORDER BY a.created DESC";
}
$stmt = $db->prepareLimit($sql, 0, 10);
if (count($binds) > 0) {
	foreach ($binds as $bind) {
		$stmt->bindParam($bind[0], $bind[1], $bind[2]);
	}
}
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_OBJ);

$response = array('success' => 1, 'message' => '', 'articles' => array(), 'listtitle' => '', 'listdesc' => '');


if ($popular == 1) {
	$response['listtitle'] = $eLang->get('POPULAR_ARTICLES');
	if ($months > 1) {
		$response['listdesc'] = sprintf($eLang->get('POPULAR_LAST_MONTHS'), $months);
	} else if ($months == 1) {
		$response['listdesc'] = $eLang->get('POPULAR_LAST_MONTH');
	} else {
		$response['listdesc'] = $eLang->get('POPULAR_ALL_TIME');
	}
} else {
	$response['listtitle'] = $eLang->get('LATEST_ARTICLES');
	$response['listdesc'] = $eLang->get('LATEST_ARTS_SITE');
}

if ($rows) {
	foreach ($rows as $row) {
		$iconclass = ($row->published == 0) ? 'fas fa-ban elx5_red' : 'fas fa-check elx5_green';
		$title = $row->title;
		if (eUTF::strlen($row->title) > 30) { $title = eUTF::substr($row->title, 0, 27).'...'; }

		$article = array(
			'artid' => $row->id,
			'iconclass' => $iconclass,
			'fulltitle' => $row->title,
			'title' => $title,
			'catid' => $row->catid,
			'cattitle' => $row->cattitle,
			'fdate' => $eDate->formatDate($row->created, $eLang->get('DATE_FORMAT_4')),
			'hits' => $row->hits,
			'created_by_name' => $row->created_by_name
		);

		$response['articles'][] = $article;
	}
}

$this->ajaxHeaders('application/json');
echo json_encode($response);
exit;

?>