<?php 
/**
* @version		$Id: mod_adminarticles.php 2450 2022-05-08 10:26:02Z IOS $
* @package		Elxis
* @subpackage	Module Administration Articles
* @copyright	Copyright (c) 2006-2022 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


if (!class_exists('modadminArticles', false)) {
	class modadminArticles {

		private $poparticles = 0;
		private $popmonths = 0;
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
			$eDate = eFactory::getDate();
			$eDoc = eFactory::getDocument();

			if (!defined('ELXIS_ADMIN')) {
				echo '<div class="elx5_warning">This module is available only in Elxis administration area!'."</div>\n";
				return;
			}

			if (ELXIS_INNER == 1) { return; }

			$htmlHelper = $elxis->obj('html');

			$rows = $this->getArticles();

			$eDoc->addScrollbar();
			$eDoc->addScriptLink($elxis->secureBase().'/modules/mod_adminarticles/js/adminarticles.js');

			if ($elxis->acl()->check('component', 'com_content', 'manage') > 0) {
				$can_edit_art = $elxis->acl()->check('com_content', 'article', 'edit');
				$can_edit_ctg = $elxis->acl()->check('com_content', 'category', 'edit');
			} else {
				$can_edit_art = 0;
				$can_edit_ctg = 0;
			}
			$edit_link_art = $elxis->makeAURL('content:articles/edit.html');
			$edit_link_ctg = $elxis->makeAURL('content:categories/edit.html');

			echo '<div class="elx5_box elx5_border_blue">'."\n";
			echo '<div class="elx5_box_body">'."\n";

			echo '<div class="elx5_dataactions elx5_spad">'."\n";
			echo '<a href="javascript:void(null);" onclick="modAArtLoad(0);" id="modaart_latest" class="elx5_dataaction elx5_datahighlight" title="'.$eLang->get('LATEST_ARTICLES').'"><i class="fas fa-clock"></i><span class="elx5_lmobhide"> '.$eLang->get('LATEST').'</span></a>'."\n";
			echo '<a href="javascript:void(null);" onclick="modAArtLoad(1);" id="modaart_popular" class="elx5_dataaction elx5_datanotcurrent" title="'.$eLang->get('POPULAR_ARTICLES').'"><i class="fas fa-fire"></i><span class="elx5_lmobhide"> '.$eLang->get('POPULAR').'</span></a>'."\n";
			echo '<a href="javascript:void(null);" onclick="modAArtToggle();" id="modaart_selmonths" class="elx5_dataaction elx5_datanotcurrent elx5_lmobhide" title="'.$eLang->get('SEARCH_OPTIONS').'"><i class="fas fa-search"></i></a>'."\n";
			echo '<h3 class="elx5_box_title" id="modaart_title">'.$eLang->get('LATEST_ARTICLES').'</h3>'."\n";
			echo '<div class="elx5_invisible" id="modaart_options">'."\n";
			echo '<div class="elx5_vsspace">'."\n";
			echo '<select name="months" id="modaart_months" class="elx5_select" title="'.$eLang->get('POP_MONTHS').'" onchange="modAArtLoad(1);">'."\n";
			echo '<option value="0" selected="selected">'.$eLang->get('ALL_TIME_POPULAR')."</option>\n";
			echo '<option value="1">'.$eLang->get('LAST_MONTH')."</option>\n";
			echo '<option value="2">'.$eLang->get('LAST_2MONTHS')."</option>\n";
			echo '<option value="3">'.$eLang->get('LAST_3MONTHS')."</option>\n";
			echo '<option value="6">'.$eLang->get('LAST_6MONTHS')."</option>\n";
			echo '<option value="12">'.$eLang->get('LAST_YEAR')."</option>\n";
			echo "</select>\n";
			echo "</div>\n";
			echo "</div>\n";//#modaart_options
			echo "</div>\n";//elx5_dataactions

			$inlink = $elxis->makeAURL('cpanel:ajax', 'inner.php');
			echo '<div class="elx5_height300" data-simplebar="1">'."\n";
			echo '<table id="modaart_tbl" class="elx5_datatable" data-inpage="'.$inlink.'" dir="'.$eLang->getinfo('DIR').'" data-lngin="'.$eLang->get('IN').'"  data-lngnores="'.$eLang->get('NO_RESULTS').'">'."\n";
			echo "<thead>\n";
			echo "<tr>\n";
			echo $htmlHelper->tableHead($eLang->get('HITS'), 'elx5_nosorting elx5_center');
			echo $htmlHelper->tableHead($eLang->get('TITLE'), 'elx5_nosorting');
			echo $htmlHelper->tableHead($eLang->get('AUTHOR'), 'elx5_nosorting elx5_tabhide');
			echo $htmlHelper->tableHead($eLang->get('DATE'), 'elx5_nosorting elx5_lmobhide');
			echo "</tr>\n";
			echo "</thead>\n";

			echo '<tbody id="modaart_tbody">'."\n";
			if ($rows) {
				foreach ($rows as $row) {
					$iconclass = ($row->published == 0) ? 'fas fa-ban elx5_red' : 'fas fa-check elx5_green';
					$title = $row->title;
					if (eUTF::strlen($row->title) > 30) { $title = eUTF::substr($row->title, 0, 27).'...'; }

					if ($can_edit_art > 0) {
						$txt = '<i class="'.$iconclass.'"></i> <a href="'.$edit_link_art.'?id='.$row->id.'" title="'.$eLang->get('EDIT').' '.$row->title.'">'.$title.'</a>';
					} else {
						$txt = '<span title="'.$row->title.'"><i class="'.$iconclass.'"></i> '.$title.'</span>';
					}
					if ($row->catid > 0) {
						if ($can_edit_ctg > 0) {
							$txt .= '<div class="elx5_tip elx5_lmobhide">'.$eLang->get('IN').' <a href="'.$edit_link_ctg.'?catid='.$row->catid.'" title="'.$eLang->get('EDIT_CATEGORY').'">'.$row->cattitle.'</a></div>';
						} else {
							$txt = '<div class="elx5_tip elx5_lmobhide">'.$eLang->get('IN').' '.$row->cattitle."</div>\n";
						}
					}

					$dt = $eDate->formatDate($row->created, $eLang->get('DATE_FORMAT_4'));

					echo '<tr id="datarow'.$row->id.'">'."\n";
					echo '<td class="elx5_center">'.$row->hits."</td>\n";
					echo '<td>'.$txt."</td>\n";
					echo '<td class="elx5_tabhide">'.$row->created_by_name."</td>\n";
					echo '<td class="elx5_lmobhide">'.$dt."</td>\n";
					echo "</tr>\n";
				}
			} else {
				echo '<tr id="datarow0" class="elx5_rowwarn"><td class="elx5_center" colspan="4">'.$eLang->get('NO_RESULTS')."</td></tr>\n";
			}
			echo "</tbody>\n";
			echo "</table>\n";
			echo '</div>';//elx5_height300

			if ($this->poparticles == 1) {
				if ($this->popmonths > 1) {
					$list_desc = sprintf($eLang->get('POPULAR_LAST_MONTHS'), $this->popmonths);
				} else if ($this->popmonths == 1) {
					$list_desc = $eLang->get('POPULAR_LAST_MONTH');
				} else {
					$list_desc = $eLang->get('POPULAR_ALL_TIME');
				}
			} else {
				$list_desc = $eLang->get('LATEST_ARTS_SITE');
			}
			echo '<div class="elx5_table_note elx5_spad" id="modaart_listdesc">'.$list_desc."</div>\n";
			echo "</div>\n";//elx5_box_body
			echo "</div>\n";//elx5_box

			echo '<div class="elx5_invisible" id="modaart_dataeditart" data-canedit="'.$can_edit_art.'">'.$edit_link_art."</div>\n";
			echo '<div class="elx5_invisible" id="modaart_dataeditctg" data-canedit="'.$can_edit_ctg.'">'.$edit_link_ctg."</div>\n";
		}


		/**********************************/
		/* GET ARTICLES FROM THE DATABASE */
		/**********************************/
		private function getArticles() {
			$db = eFactory::getDB();

			$binds = array();
			$sql = "SELECT a.id, a.catid, a.title, a.created, a.created_by, a.created_by_name, a.published, a.hits, c.title AS cattitle"
			."\n FROM ".$db->quoteId('#__content')." a"
			."\n LEFT JOIN ".$db->quoteId('#__categories')." c ON c.catid=a.catid";
			if ($this->poparticles == 1) {
				if ($this->popmonths > 0) {
					$ts = gmmktime(0, 0, 0, gmdate('m') - $this->popmonths, gmdate('d'), gmdate('Y'));
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

            return $rows;            
        }

	}
}


$admarticles = new modadminArticles($params, $elxmod);
$admarticles->run();
unset($admarticles);

?>