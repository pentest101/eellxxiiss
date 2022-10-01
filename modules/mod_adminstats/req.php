<?php 
/**
* @version		$Id: req.php 2073 2019-02-16 08:59:21Z IOS $
* @package		Elxis
* @subpackage	Module Administration Statistics
* @copyright	Copyright (c) 2006-2019 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


$db = eFactory::getDB();
$eDate = eFactory::getDate();
$eLang = eFactory::getLang();

$month = isset($_POST['month']) ? (int)$_POST['month'] : date('m');
$year = isset($_POST['year']) ? (int)$_POST['year'] : date('Y');
if (($month < 0) || ($month > 12)) { $month = date('m'); }
if (($year < 2000) || ($year > 2040)) { $year = date('Y'); }

$ts = mktime(12, 0, 0, $month, 15, $year);
$daysnum = date('t', $ts);
$mname = $eDate->monthName($month);

$eLang->load('mod_adminstats', 'module');

$data = array(
	'year' => $year,
	'month' => $month,
	'daysnum' => $daysnum,
	'visits' => array(),
	'clicks' => array(),
	'clicksdesc' => '',
	'visitsdesc' => ''
);

for ($i=1; $i <= $daysnum; $i++) {
	$data['visits'][$i] = 0;
	$data['clicks'][$i] = 0;
}

$data['clicksdesc'] = sprintf($eLang->get('CLICKS_PER_DAY'), '<strong>'.$mname.' '. $year.'</strong>');
$data['visitsdesc'] = sprintf($eLang->get('VISITS_PER_DAY'), '<strong>'.$mname.' '. $year.'</strong>');

$dt = $year.'-'.sprintf("%02d", $month).'%';
$sql = "SELECT ".$db->quoteId('statdate').", ".$db->quoteId('clicks').", ".$db->quoteId('visits')
."\n FROM ".$db->quoteId('#__statistics')
."\n WHERE ".$db->quoteId('statdate')." LIKE :sdt ORDER BY ".$db->quoteId('statdate')." ASC";
$stmt = $db->prepare($sql);
$stmt->bindParam(':sdt', $dt, PDO::PARAM_STR);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($rows) {
	foreach ($rows as $k => $row) {
		$day = (int)substr($row['statdate'], -2);
		$data['visits'][$day] = $row['visits'];
		$data['clicks'][$day] = $row['clicks'];
	}
}

$response = array(
	'success' => 1, 'message' => '', 'year' => $data['year'], 'month' => $data['month'], 'daysnum' => $data['daysnum'], 
	'visits' => implode(',', $data['visits']), 'clicks' => implode(',', $data['clicks']), 'clicksdesc' => $data['clicksdesc'], 'visitsdesc' => $data['visitsdesc']
);

$this->ajaxHeaders('application/json');
echo json_encode($response);
exit;

?>