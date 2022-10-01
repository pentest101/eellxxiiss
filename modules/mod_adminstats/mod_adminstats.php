<?php 
/**
* @version		$Id: mod_adminstats.php 2160 2019-03-12 19:32:37Z IOS $
* @package		Elxis
* @subpackage	Module Administration Statistics
* @copyright	Copyright (c) 2006-2019 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


if (!class_exists('modadminStats', false)) {
	class modadminStats {

		private $moduleId = 0;


		/*********************/
		/* MAGIC CONSTRUCTOR */
		/*********************/
		public function __construct($params, $elxmod) {
			$this->moduleId = $elxmod->id;
		}


		/********************/
		/* RUN FOREST, RUN! */
		/********************/
		public function run() {
			$elxis = eFactory::getElxis();
			$eLang = eFactory::getLang();
			$eDoc = eFactory::getDocument();

			if (!defined('ELXIS_ADMIN')) {
				echo '<div class="elx5_warning">This module is available only in Elxis administration area!'."</div>\n";
				return;
			}

			if (ELXIS_INNER == 1) { return; }

			$year = date('Y');
			$month = date('n');
			$data = $this->collectMonthStats($year, $month);

			$eDoc->addFontAwesome();
			$eDoc->addScriptLink($elxis->secureBase().'/components/com_cpanel/js/chart.min.js');
			$eDoc->addScriptLink($elxis->secureBase().'/modules/mod_adminstats/js/adminstats.js');
			$eDoc->addStyleLink($elxis->secureBase().'/components/com_cpanel/css/cp.css');

			$clicks_desc = sprintf($eLang->get('CLICKS_PER_DAY'), '<strong>'.$data['monthname'].' '. $data['year'].'</strong>');
			$visits_desc = sprintf($eLang->get('VISITS_PER_DAY'), '<strong>'.$data['monthname'].' '. $data['year'].'</strong>');

			$clicksdata = array();
			$visitsdata = array();
			$labels = array();
			foreach($data['clicks'] as $i => $num) {
				$clicksdata[] = $num;
				$labels[] = $i;
			}
			foreach($data['visits'] as $i => $num) { $visitsdata[] = $num; }

			echo '<div class="elx5_box elx5_border_blue">'."\n";
			echo '<div class="elx5_box_body">'."\n";

			echo '<div class="elx5_dataactions elx5_spad">'."\n";
			echo '<a href="javascript:void(null);" onclick="modAStatsUpdate(-1);" class="elx5_dataaction elx5_datanotcurrent" title="'.$eLang->get('PREV_MONTH').'"><i class="fas fa-chevron-left"></i></a>'."\n";
			echo '<a href="javascript:void(null);" onclick="modAStatsUpdate(0);" id="modastats_btn" class="elx5_dataaction elx5_datahighlight" title="'.$eLang->get('CLICKS').'/'.$eLang->get('VISITS').'"><i class="fas fa-mouse-pointer"></i><span class="elx5_lmobhide"> '.$eLang->get('CLICKS').'</span></a>'."\n";
			echo '<a href="javascript:void(null);" onclick="modAStatsUpdate(1);" class="elx5_dataaction elx5_datanotcurrent" title="'.$eLang->get('NEXT_MONTH').'"><i class="fas fa-chevron-right"></i></a>'."\n";
			echo '<h3 class="elx5_box_title elx5_tabhide">'.$eLang->get('STATISTICS')."</h3>\n";
			echo '<div class="elx5_box_subtitle" id="modastat_desc" data-cdesc="'.$clicks_desc.'" data-vdesc="'.$visits_desc.'" data-clickslng="'.$eLang->get('CLICKS').'" data-visitslng="'.$eLang->get('VISITS').'">'.$clicks_desc."</div>\n";
			echo "</div>\n";//elx5_dataactions

			$inlink = $elxis->makeAURL('cpanel:ajax', 'inner.php');
			echo '<div class="elxcp_graphbox" id="modastats_box" data-year="'.$year.'" data-month="'.$month.'" data-type="clicks" data-days="'.count($clicksdata).'" data-inpage="'.$inlink.'">'."\n";
			echo '<canvas id="modastatsgraph" class="elxcp_graph"></canvas>'."\n";
			echo "</div>\n";
			echo "<script>\n";
			echo 'var astatsChart = new Chart(document.getElementById(\'modastatsgraph\'), {'."\n";
			echo 'type: \'bar\','."\n";
			echo 'data: {'."\n";
			echo 'labels:[\''.implode('\', \'', $labels).'\'],'."\n";
			echo 'datasets: [{'."\n";
			echo 'label: \''.$eLang->get('CLICKS').'\','."\n";
			echo 'data: ['.implode(', ', $clicksdata).'],'."\n";
			echo 'fill: false,'."\n";
			echo 'backgroundColor:[\'rgba(54, 162, 235, 0.4)\', \'rgba(54, 162, 235, 0.2)\', \'rgba(54, 162, 235, 0.4)\', \'rgba(54, 162, 235, 0.2)\', \'rgba(54, 162, 235, 0.4)\', 
			\'rgba(54, 162, 235, 0.2)\', \'rgba(54, 162, 235, 0.4)\', \'rgba(54, 162, 235, 0.2)\', \'rgba(54, 162, 235, 0.4)\', \'rgba(54, 162, 235, 0.2)\', \'rgba(54, 162, 235, 0.4)\', 
			\'rgba(54, 162, 235, 0.2)\', \'rgba(54, 162, 235, 0.4)\', \'rgba(54, 162, 235, 0.2)\', \'rgba(54, 162, 235, 0.4)\', \'rgba(54, 162, 235, 0.2)\', \'rgba(54, 162, 235, 0.4)\', 
			\'rgba(54, 162, 235, 0.2)\', \'rgba(54, 162, 235, 0.4)\', \'rgba(54, 162, 235, 0.2)\', \'rgba(54, 162, 235, 0.4)\', \'rgba(54, 162, 235, 0.2)\', \'rgba(54, 162, 235, 0.4)\', 
			\'rgba(54, 162, 235, 0.2)\', \'rgba(54, 162, 235, 0.4)\', \'rgba(54, 162, 235, 0.2)\', \'rgba(54, 162, 235, 0.4)\', \'rgba(54, 162, 235, 0.2)\', \'rgba(54, 162, 235, 0.4)\', 
			\'rgba(54, 162, 235, 0.2)\', \'rgba(54, 162, 235, 0.4)\' ],'."\n";
			echo 'borderWidth:1'."\n";
			echo '}]'."\n";
			echo '},'."\n";
			echo 'options:{responsive: true, maintainAspectRatio:false, legend: { display:false }, scales:{xAxes:[{ticks:{display:false}}], yAxes:[{ticks:{beginAtZero:true}}]}}'."\n";
			echo '});'."\n";
			echo "</script>\n";
			if ($elxis->acl()->check('com_cpanel', 'statistics', 'view') > 0) {
				$link = $elxis->makeAURL('cpanel:stats/');
				echo '<div class="elx5_table_note elx5_spad"><a href="'.$link.'" title="'.$eLang->get('ANALYTIC_STATS').'">'.$eLang->get('MORE')."</a></div>\n";
			}
			echo "</div>\n";//elx5_box_body
			echo "</div>\n";//elx5_box

			echo '<div class="elx5_invisible" id="modastats_data" data-clicks="'.implode(',', $clicksdata).'" data-visits="'.implode(',', $visitsdata).'">&#160;</div>'."\n";
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
				'visits' => array(),
				'clicks' => array()
			);

			for ($i=1; $i <= $daysnum; $i++) {
				$data['visits'][$i] = 0;
				$data['clicks'][$i] = 0;
			}

			$rows = $this->getDbStats($year, $month);
			if (!$rows) { return $data; }

			foreach ($rows as $row) {
				$day = (int)substr($row['statdate'], -2);
				$data['visits'][$day] = $row['visits'];
				$data['clicks'][$day] = $row['clicks'];
			}

			return $data;
		}


		/************************************/
		/* GET STATISTICS FROM THE DATABASE */
		/************************************/
		private function getDbStats($year, $month) {
			$db = eFactory::getDB();

			$dt = $year.'-'.sprintf("%02d", $month).'%';
			$sql = "SELECT ".$db->quoteId('statdate').", ".$db->quoteId('clicks').", ".$db->quoteId('visits')
			."\n FROM ".$db->quoteId('#__statistics')
			."\n WHERE ".$db->quoteId('statdate')." LIKE :sdt ORDER BY ".$db->quoteId('statdate')." ASC";
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':sdt', $dt, PDO::PARAM_STR);
			$stmt->execute();
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

			return $rows;		
		}

	}
}


$admstats = new modadminStats($params, $elxmod);
$admstats->run();
unset($admstats);

?>