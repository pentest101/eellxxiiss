<?php 
/**
* @version		$Id: statistics.php 2161 2019-03-12 21:37:01Z IOS $
* @package		Elxis
* @subpackage	CPanel component
* @copyright	Copyright (c) 2006-2019 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class statisticsCPController extends cpanelController {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null, $model=null) {
		parent::__construct($view, $model);
	}


	/***************************/
	/* PREPARE SITE STATISTICS */
	/***************************/
	public function showstats() {
		$elxis = eFactory::getElxis();
		$eDoc = eFactory::getDocument();
		$eLang = eFactory::getLang();
		$pathway = eFactory::getPathway();

		if ($elxis->acl()->check('com_cpanel', 'statistics', 'view') < 1) {
			$url = $elxis->makeAURL('cpanel:/');
			$elxis->redirect($url, $eLang->get('NOTALLOWACCPAGE'), true);
		}

		$year = date('Y');
		$month = date('n');
		$stats_start = $this->model->getStatisticsStart();

		if ($stats_start) {
			if (isset($_GET['dt'])) {
				$dt = trim($_GET['dt']);
				if (is_numeric($dt)) {
					if (strlen($dt) == 6) {
						$y = intval(substr($dt, 0, 4));
						$m = intval(substr($dt, -2));
						if (($m > 0) && ($m < 13) && ($y >= $stats_start['year']) && ($y <= date('Y'))) {
							$year = $y;
							if ($y == date('Y')) {
								$month = ($m <= date('n')) ? $m : date('n');
							} else {
								$month = $m;
							}
						}
					}
				}
			}
		}

		$yeardata = $this->collectYearStats($year);
		$monthdata = $this->collectMonthStats($year, $month);

		$eDoc->addStyleLink($elxis->secureBase().'/components/com_cpanel/css/cp'.$eLang->getinfo('RTLSFX').'.css');
		$eDoc->addScriptLink($elxis->secureBase().'/components/com_cpanel/js/chart.min.js');

		$pathway->deleteAllNodes();
		$pathway->addNode($eLang->get('STATISTICS'));
		$eDoc->setTitle($eLang->get('STATISTICS').' '.$year);

		$this->view->graphs($yeardata, $monthdata, $stats_start, $year, $month, $eLang, $elxis);
	}


	/******************************************/
	/* COLLECT STATISTICS FOR THE GIVEN MONTH */
	/******************************************/
	private function collectMonthStats($year, $month) {
		$ts = mktime(12, 0, 0, $month, 15, $year);
		$daysnum = date('t', $ts);
		$mname = eFactory::getDate()->monthName($month);

		$data = array(
			'year' => $year,
			'month' => $month,
			'monthname' => $mname,
			'daysnum' => $daysnum,
			'visits' => array(
				'total' => 0,
				'stats' => array()
			),
			'clicks' => array(
				'total' => 0,
				'stats' => array()
			),
			'langs' => array(
				'total' => 0,
				'stats' => array()
			)
		);

		for ($i=1; $i <= $daysnum; $i++) {
			$data['visits']['stats'][$i] = 0;
			$data['clicks']['stats'][$i] = 0;
		}

		$rows = $this->model->getStatistics($year, $month);
		if (!$rows) { return $data; }

		foreach ($rows as $row) {
			$day = (int)substr($row['statdate'], -2);

			$data['visits']['stats'][$day] = $row['visits'];
			$data['visits']['total'] += $row['visits'];
			$data['clicks']['stats'][$day] = $row['clicks'];
			$data['clicks']['total'] += $row['clicks'];
			
			$alngs = unserialize($row['langs']);
			if (is_array($alngs) && (count($alngs) > 0)) {
				foreach ($alngs as $lng => $clicks) {
					$data['langs']['total'] += $clicks;
					if (!isset($data['langs']['stats'][$lng])) {
						$data['langs']['stats'][$lng] = $clicks;
					} else {
						$data['langs']['stats'][$lng] += (int)$clicks;
					}
				}
			}
		}

		return $data;
	}


	/*****************************************/
	/* COLLECT STATISTICS FOR THE GIVEN YEAR */
	/*****************************************/
	private function collectYearStats($year) {
		$data = array(
			'year' => $year,
			'visits' => array(
				'total' => 0,
				'stats' => array()
			),
			'clicks' => array(
				'total' => 0,
				'stats' => array()
			),
			'langs' => array(
				'total' => 0,
				'stats' => array()
			)
		);

		for ($i=1; $i <= 12; $i++) {
			$data['visits']['stats'][$i] = 0;
			$data['clicks']['stats'][$i] = 0;
		}

		$rows = $this->model->getStatistics($year, 0);
		if (!$rows) { return $data; }

		foreach ($rows as $row) {
			$month = (int)substr($row['statdate'], 5, 2);
			$data['visits']['stats'][$month] += $row['visits'];
			$data['visits']['total'] += $row['visits'];
			$data['clicks']['stats'][$month] += $row['clicks'];
			$data['clicks']['total'] += $row['clicks'];

			$alngs = unserialize($row['langs']);
			if (is_array($alngs) && (count($alngs) > 0)) {
				foreach ($alngs as $lng => $clicks) {
					$data['langs']['total'] += $clicks;
					if (!isset($data['langs']['stats'][$lng])) {
						$data['langs']['stats'][$lng] = $clicks;
					} else {
						$data['langs']['stats'][$lng] += $clicks;
					}
				}
			}
		}

		return $data;
	}

}

?>