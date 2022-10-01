<?php 
/**
* @version		$Id: statistics.html.php 2161 2019-03-12 21:37:01Z IOS $
* @package		Elxis
* @subpackage	CPanel component
* @copyright	Copyright (c) 2006-2018 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class statisticsCPView extends cpanelView {


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/*******************************/
	/* MAKE/SHOW STATISTICS GRAPHS */
	/*******************************/
	public function graphs($yeardata, $monthdata, $stats_start, $statsyear, $statsmonth, $eLang, $elxis) {
		$eDate = eFactory::getDate();

		echo '<h2>'.$eLang->get('STATISTICS').' <span>'.$eDate->monthName($statsmonth).' '.$statsyear."</span></h2>\n";
		if ($stats_start) {
			$curyear = date('Y');
			$curmonth = date('n');
			$selv = $statsyear.sprintf("%02d", $statsmonth);
			$action = $elxis->makeAURL('cpanel:stats/');
			echo '<form name="fmstatsperiod" id="fmstatsperiod" action="'.$action.'" class="elx5_form" method="get">'."\n";
			echo '<fieldset class="elx5_fieldset">'."\n";
			echo '<div class="elx5_zero">'."\n";
			echo '<label class="elx5_label" for="statsdt">'.$eLang->get('PERIOD')."</label>\n";
			echo '<div class="elx5_labelside">'."\n";
			echo '<select name="dt" id="statsdt" class="elx5_select" onchange="document.getElementById(\'fmstatsperiod\').submit();">'."\n";
			for ($y = $stats_start['year']; $y <= $curyear; $y++) {
				for ($m = 1; $m < 13; $m++) {
					if ($y == $stats_start['year']) {
						if ($m < $stats_start['month']) { continue; }
					}
					if ($y == $curyear) {
						if ($m > $curmonth) { break; }
					}
					$v = $y.sprintf("%02d", $m);

					$sel = ($v == $selv) ? ' selected="selected"' : '';
					echo '<option value="'.$v.'"'.$sel.'>'.$eDate->monthName($m).' '.$y."</option>\n";
				}
			}
			echo "</select>\n";
			echo "</div>\n";
			echo "</div>\n";
			echo "</fieldset>\n";
			echo "</form>\n";
		}

		if ($elxis->getConfig('STATISTICS') == 0) {
			echo '<div class="elx5_warning">'.$eLang->get('STATS_COL_DISABLED')."</div>\n";
		}

		$this->makeTimeGraph($yeardata, true, $eLang);
		$this->makeTimeGraph($yeardata, false, $eLang);
		$this->makeTimeGraph($monthdata, true, $eLang);
		$this->makeTimeGraph($monthdata, false, $eLang);

		echo '<div class="elx5_2colwrap">'."\n";
		echo '<div class="elx5_2colbox">'."\n";
		$this->langsGraph($yeardata, $eLang);
		echo "</div>\n";
		echo '<div class="elx5_2colbox">'."\n";
		$this->langsGraph($monthdata, $eLang);
		echo "</div>\n";
		echo "</div>\n";
	}


	/******************************/
	/* CREATE YEAR OR MONTH GRAPH */
	/******************************/
	private function makeTimeGraph($data, $is_visits, $eLang) {
		$eDate = eFactory::getDate();

		if ($is_visits) {
			$idx = 'visits';
			$graphid = 'statsvis';
			$is_year = (count($data[$idx]['stats']) == 12) ? true : false;
			$title = $eLang->get('UNIQUE_VISITS');
			if ($is_year) {
				$period = $data['year'];
				$description = sprintf($eLang->get('VISITS_PER_MONTH'), '<strong>'.$data['year'].'</strong>');
			} else {
				$period = $data['monthname'].' '.$data['year'];
				$description = sprintf($eLang->get('VISITS_PER_DAY'), '<strong>'.$data['monthname'].' '. $data['year'].'</strong>');
			}
		} else {
			$idx = 'clicks';
			$graphid = 'statscli';
			$is_year = (count($data[$idx]['stats']) == 12) ? true : false;
			$title = $eLang->get('PAGE_VIEWS');
			if ($is_year) {
				$period = $data['year'];
				$description = sprintf($eLang->get('CLICKS_PER_MONTH'), '<strong>'.$data['year'].'</strong>');
			} else {
				$period = $data['monthname'].' '.$data['year'];
				$description = sprintf($eLang->get('CLICKS_PER_DAY'), '<strong>'.$data['monthname'].' '. $data['year'].'</strong>');
			}
		}

		$graphid .= $is_year ? 'y' : 'm';
		$coldata = array();
		$labels = array();
		foreach($data[$idx]['stats'] as $i => $num) {
			$coldata[] = $num;
			$labels[] = $is_year ? $eDate->monthName($i, true) : $i;
		}

		echo '<div class="elx5_box elx5_border_green">'."\n";
		echo '<div class="elx5_box_body">'."\n";
		echo '<div class="elx5_dataactions elx5_spad">'."\n";
		echo '<h3 class="elx5_box_title">'.$title.' <span dir="ltr" class="elx5_orange">'.$period."</span></h3>\n";
		echo "</div>\n";
		echo '<div class="elxcp_graphbox">'."\n";
		echo '<canvas id="'.$graphid.'" class="elxcp_graph"></canvas>'."\n";
		echo "</div>\n";
		echo "<script>\n";
		echo 'var x3chart = new Chart(document.getElementById(\''.$graphid.'\'), {'."\n";
		echo 'type: \'bar\','."\n";
		echo 'data: {'."\n";
		echo 'labels:[\''.implode('\', \'', $labels).'\'],'."\n";
		echo 'datasets: [{'."\n";
		echo 'label: \''.$title.'\','."\n";
		echo 'data: ['.implode(', ', $coldata).'],'."\n";
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
		echo 'options:{responsive: true, maintainAspectRatio:false, legend: { display:false }, scales:{yAxes:[{ticks:{beginAtZero:true}}]}}'."\n";
		echo '});'."\n";
		echo "</script>\n";
		echo '<div class="elx5_tip elx5_vpad elx5_center">'.$description."</div>\n";
		echo "</div>\n";//elx5_box_body
		echo "</div>\n";//elx5_box
	}


	/**************************/
	/* CREATE LANGUAGES GRAPH */
	/**************************/
	private function langsGraph($data, $eLang) {
		$is_year = (count($data['visits']['stats']) == 12) ? true : false;

		$labels = array();
		$languages = array();
		$descriptions = array();
		$total = 0;
		foreach ($data['langs']['stats'] as $lng => $num) {
			$labels[] = strtoupper($lng);
			$descriptions[] = strtoupper($lng);
			$languages[] = $num;
			$total += $num;
		}

		if ($is_year) {
			$graphid = 'statslangsy';
			$period = $data['year'];
			$description = sprintf($eLang->get('LANGS_USAGE_FOR'), '<strong>'.$data['year'].'</strong>');
		} else {
			$graphid = 'statslangsm';
			$period = $data['monthname'].' '.$data['year'];
			$description = sprintf($eLang->get('LANGS_USAGE_FOR'), '<strong>'.$data['monthname'].' '.$data['year'].'</strong>');
		}

		echo '<div class="elx5_box elx5_border_green">'."\n";
		echo '<div class="elx5_box_body">'."\n";
		echo '<div class="elx5_dataactions elx5_spad">'."\n";
		echo '<h3 class="elx5_box_title">'.$eLang->get('LANGUAGE').' <span dir="ltr" class="elx5_orange">'.$period."</span></h3>\n";
		echo "</div>\n";
		echo '<div class="elxcp_graphbox">'."\n";
		echo '<canvas id="'.$graphid.'" class="elxcp_graph"></canvas>'."\n";
		echo "</div>\n";
		echo "<script>\n";
		echo 'var x3chart = new Chart(document.getElementById(\''.$graphid.'\'), {'."\n";
		echo 'type: \'pie\','."\n";
		echo 'data: {'."\n";
		echo 'labels:[\''.implode('\', \'', $labels).'\'],'."\n";
		echo 'descriptions:[\''.implode('\', \'', $descriptions).'\'],'."\n";
		echo 'datasets: [{'."\n";
		echo 'label: \''.$eLang->get('LANGUAGE').'\','."\n";
		echo 'data: ['.implode(', ', $languages).'],'."\n";
		echo 'fill: false,'."\n";
		echo 'backgroundColor:[\'rgba(54, 162, 235, 0.4)\', \'rgba(255, 99, 132, 0.4)\', \'rgba(153, 102, 255, 0.4)\', \'rgba(255, 159, 64, 0.4)\', \'rgba(20, 90, 50, 0.4)\', \'rgba(75, 192, 192, 0.4)\', \'rgba(255, 205, 86, 0.4)\', ';
		echo '\'rgba(215, 189, 226, 0.4)\', \'rgba(52, 73, 94, 0.4)\', \'rgba(135, 54, 0, 0.4)\', \'rgba(244, 208, 63, 0.4)\', \'rgba(230, 126, 34, 0.4)\', \'rgba(36, 113, 163 , 0.4)\', \'rgba(40, 180, 99, 0.4)\', \'rgba(187, 143, 206, 0.4)\', \'rgba(231, 76, 60, 0.4)\'],'."\n";
		echo 'borderWidth:1'."\n";
		echo '}]'."\n";
		echo '},'."\n";
		echo 'options:{responsive: true, maintainAspectRatio:false, legend: { display:true }, 
			tooltips: {
				custom: function(tooltip) {}, mode: \'single\',
				callbacks: {
					label: function(tooltipItems, data) {
						let sum = data.datasets[0].data.reduce(add, 0);
						function add(a, b) { return a + b; }
						return data.descriptions[tooltipItems.index]+\': \'+data.datasets[0].data[tooltipItems.index]+\' (\'+parseInt((data.datasets[0].data[tooltipItems.index] / sum * 100), 10) + \'%)\';
					}
				}
			}
		}'."\n";
		echo '});'."\n";
		echo "</script>\n";
		echo '<div class="elx5_tip elx5_vpad elx5_center">'.$description."</div>\n";
		echo "</div>\n";//elx5_box_body
		echo "</div>\n";//elx5_box
	}

}

?>