<?php 
/**
* @version		$Id: mod_adminusers.php 2450 2022-05-08 10:26:02Z IOS $
* @package		Elxis
* @subpackage	Module Administration Online users
* @copyright	Copyright (c) 2006-2022 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


if (!class_exists('modadminUsers', false)) {
	class modadminUsers {

		private $moduleId = 0;


		/*********************/
		/* MAGIC CONSTRUCTOR */
		/*********************/
		public function __construct($elxmod) {
			$this->moduleId = $elxmod->id;
		}


		/********************/
		/* RUN FOREST, RUN! */
		/********************/
		public function run() {
			$elxis = eFactory::getElxis();
			$eLang = eFactory::getLang();
			$eDate = eFactory::getDate();
			$db = eFactory::getDB();
			$eDoc = eFactory::getDocument();

			if (!defined('ELXIS_ADMIN')) {
				echo '<div class="elx5_warning">This module is available only in Elxis administration area!'."</div>\n";
				return;
			}

			if (ELXIS_INNER == 1) { return; }

			$eDoc->addScrollbar();
			$eDoc->addFontAwesome();
			$eDoc->addScriptLink($elxis->secureBase().'/modules/mod_adminusers/js/adminusers.js');

			$total = $this->getTotalVisitors($db, $elxis, $eDate);
			$rows = array();
			if ($total > 0) {
				$rows = $this->getVisitors($db, $elxis, $eDate, $eLang);
			}

			$htmlHelper = $elxis->obj('html');
			$cplink = $elxis->makeAURL('cpanel:/', 'inner.php');

			echo '<div class="elx5_box elx5_border_blue">'."\n";
			echo '<div class="elx5_box_body">'."\n";

			echo '<div class="elx5_dataactions elx5_spad">'."\n";
			echo '<a href="javascript:void(null);" onclick="modAUsersUpdate(-1, \'\');" class="elx5_dataaction elx5_datahighlight" title="'.$eLang->get('PREV_PAGE').'"><i class="fas fa-chevron-left"></i></a>'."\n";
			echo '<a href="javascript:void(null);" onclick="modAUsersUpdate(1, \'\');" class="elx5_dataaction elx5_datahighlight" title="'.$eLang->get('NEXT_PAGE').'"><i class="fas fa-chevron-right"></i></a>'."\n";
			echo '<h3 class="elx5_box_title elx5_tabhide">'.$eLang->get('ONLINE_USERS').' <span dir="ltr" class="elx5_orange" id="modAUsersTotal">('.$total.')</span></h3>'."\n";
			$maxpage = ($total == 0) ? 1 : ceil($total/10);
			$txt = sprintf($eLang->get('PAGEOF'), '1', $maxpage);
			echo '<div class="elx5_box_subtitle" id="modAUsersPageSum">'.$txt."</div>\n";
			echo "</div>\n";//elx5_dataactions

			$maxpage = ($total == 0) ? 1 : ceil($total/10);

			echo '<div class="elx5_height300" data-simplebar="1">'."\n";
			echo '<table id="modadmusers" class="elx5_datatable" data-deletelng="'.addslashes($eLang->get('AREYOUSURE')).'" data-inpage="'.$cplink.'" data-page="1" data-maxpage="'.$maxpage.'" data-sort="timeasc">'."\n";
			echo "<thead>\n";
			echo "<tr>\n";
			echo $htmlHelper->tableHead('<a href="javascript:void(null);" onclick="modAUsersUpdate(0, \'user\');">'.$eLang->get('USER').'</a>', 'elx5_nosorting', 'id="modAUsersTHuser"');
			echo $htmlHelper->tableHead('<a href="javascript:void(null);" onclick="modAUsersUpdate(0, \'time\');">'.$eLang->get('IDLE_TIME').'</a>', 'elx5_sorting_asc elx5_center elx5_lmobhide', 'id="modAUsersTHutime"');
			echo $htmlHelper->tableHead('<a href="javascript:void(null);" onclick="modAUsersUpdate(0, \'clicks\');">'.$eLang->get('CLICKS').'</a>', 'elx5_nosorting elx5_center elx5_tabhide', 'id="modAUsersTHclicks"');
			echo $htmlHelper->tableHead($eLang->get('ACTIONS'), 'elx5_nosorting elx5_center');
			echo "</tr>\n";
			echo "</thead>\n";
			echo '<tbody id="modausers_tbody">'."\n";

			if ($rows) {
				$myip = eFactory::getSession()->getIP();
				$iphelper = $elxis->obj('ip');
				foreach ($rows as $row) {
					$info = $this->getVisitorInfo($row, $iphelper);
					$tipdata = array();
					if ($info->ipaddress != '') { $tipdata[] = 'IP: '.$info->ipaddress; }
					if ($info->os != '') { $tipdata[] = $eLang->get('OS').': '.$info->os; }
					if ($row->gid <> 7) { $tipdata[] = $eLang->get('AUTHENTICATION').': '.$row->login_method; }
					if ($row->browser['type'] != '') { $tipdata[] = 'Device: '.$row->browser['type']; }
					if ($info->browser != '') { $tipdata[] = $eLang->get('BROWSER').': '.$info->browser; }
					if ($row->current_page != '') { $tipdata[] = $eLang->get('PAGE').': '.$row->current_page; }
					$tipdata[] = $eLang->get('ONLINE_TIME').': '.$row->time_online;
					$tipdata[] = $eLang->get('IDLE_TIME').': '.$row->time_idle;
					$tipdata[] = $eLang->get('CLICKS').': '.$row->clicks;

					echo '<tr>'."\n";
					$txt = '<div title="'.implode("\n", $tipdata).'">';
					if ($info->deviceicon != '') { $txt .= '<i class="'.$info->deviceicon.'"></i> '; }
					$txt .= $info->visitorname;
					if ($row->gid <> 7) {
						if ($info->browser != '') {
							$txt .= '<div class="elx5_smallnote">'.$info->browser.'<span class="elx5_lmobhide"> | '.$row->groupname.'</span></div>';
						} else {
							$txt .= '<div class="elx5_smallnote">'.$row->groupname.'</div>';
						}
					} else {
						if (($info->browser != '') && ($info->ipaddress != '')) {
							$txt .= '<div class="elx5_smallnote">'.$info->browser.'<span class="elx5_lmobhide"> | '.$info->ipaddress.'</span></div>';
						} else if ($info->browser != '') {
							$txt .= '<div class="elx5_smallnote">'.$info->browser.'</div>';
						} else if ($info->ipaddress != '') {
							$txt .= '<div class="elx5_smallnote">'.$info->ipaddress.'</div>';
						}
					}
					$txt .= '</div>';
					echo '<td>'.$txt."</td>\n";
					echo '<td class="elx5_center elx5_lmobhide">'.$row->time_idle."</td>\n";
					echo '<td class="elx5_center elx5_tabhide">'.$row->clicks."</td>\n";
					echo '<td class="elx5_center">'."\n";

					if (($elxis->user()->gid == 1) && ($row->gid <> 7) && ($row->uid <> $elxis->user()->uid) && ($info->ipaddress != $myip)) {
						echo '<a href="javascript:void(null);" class="elx5_dataaction elx5_datawarn" onclick="modAUsersLogout('.$row->uid.', '.$row->gid.', \''.$row->login_method.'\', \''.$row->ip_address.'\', \''.$row->first_activity.'\');" title="'.$eLang->get('FORCE_LOGOUT').'"><i class="fas fa-power-off"></i></a>';
					} else {
						echo '<a href="javascript:void(null);" class="elx5_dataaction elx5_datanotallowed"><i class="fas fa-power-off"></i></a>';
					}

					if (($elxis->user()->gid == 1) && ($row->uid <> $elxis->user()->uid) && ($info->ipaddress != $myip)) {
						echo ' <a href="javascript:void(null);" class="elx5_dataaction elx5_datawarn" onclick="modAUsersBanIP(\''.$row->ip_address.'\');" title="'.$eLang->get('BAN_IP').'"><i class="fas fa-user-slash"></i></a>'."\n";
					} else {
						echo '<a href="javascript:void(null);" class="elx5_dataaction elx5_datanotallowed"><i class="fas fa-user-slash"></i></a>';
					}
					echo '</td>'."\n";
					echo "</tr>\n";
				}
			} else {
				echo '<tr id="datarow0" class="elx5_rowwarn"><td class="elx5_center" colspan="4">'.$eLang->get('NO_RESULTS')."</td></tr>\n";
			}
			echo "</tbody>\n";
			echo "</table>\n";

			echo '</div>';//elx5_height300
			echo '<div class="elx5_table_note elx5_spad">&#160;</div>'."\n";//just for space
			echo "</div>\n";//elx5_box_body
			echo "</div>\n";//elx5_box
		}


		/************************/
		/* COUNT TOTAL VISITORS */
		/************************/
		private function getTotalVisitors($db, $elxis, $eDate) {
			$ts = $eDate->getTS() - $elxis->getConfig('SESSION_LIFETIME');

			$sql = "SELECT COUNT(".$db->quoteId('uid').") FROM ".$db->quoteId('#__session')." WHERE ".$db->quoteId('last_activity')." > :ts";
			$stmt = $db->prepareLimit($sql, 0, 1);
			$stmt->bindParam(':ts', $ts, PDO::PARAM_INT);
			$stmt->execute();
			$total = (int)$stmt->fetchResult();

			return $total;		
		}


		/***********************/
		/* GET ONLINE VISITORS */
		/***********************/
		private function getVisitors($db, $elxis, $eDate, $eLang) {
			$nowts = $eDate->getTS();
			$ts = $nowts - $elxis->getConfig('SESSION_LIFETIME');

			$sql = "SELECT s.uid, s.gid, s.login_method, s.first_activity, s.last_activity, s.clicks, s.current_page,"
			."\n s.ip_address, s.user_agent, u.uname, u.groupname FROM ".$db->quoteId('#__session')." s"
			."\n LEFT JOIN ".$db->quoteId('#__users')." u ON u.uid = s.uid"
			."\n WHERE s.last_activity > :ts ORDER BY s.last_activity DESC";
			$stmt = $db->prepareLimit($sql, 0, 10);
			$stmt->bindParam(':ts', $ts, PDO::PARAM_INT);
			$stmt->execute();
			$rows = $stmt->fetchAll(PDO::FETCH_OBJ);

			if (!$rows) { return array(); }
			
			$browser = $elxis->obj('browser');

			$visitors = array();
			foreach ($rows as $row) {
				$visitor = $row;

				$dt = $nowts - $visitor->first_activity;
				$ldt = $nowts - $visitor->last_activity;

				$h = floor($dt/3600);
				$rem = $dt - ($h * 3600);
				$min = floor($rem/60);
				$sec = $rem - ($min * 60);
				$visitor->time_online = ($h > 0) ? $h.':'.sprintf("%02d", $min).':'.sprintf("%02d", $sec) : sprintf("%02d", $min).':'.sprintf("%02d", $sec);

				$min = floor($ldt/60);
				$sec = $ldt - ($min * 60);
				$visitor->time_idle = $min.':'.sprintf("%02d", $sec);
				$visitor->browser = $browser->getBrowser($visitor->user_agent, false);

				switch ($visitor->gid) {
					case 1: $visitor->groupname = $eLang->get('ADMINISTRATOR'); break;
					case 5: $visitor->groupname = $eLang->get('USER'); break;
					case 6:
						$visitor->groupname = $eLang->get('EXTERNALUSER');
						$visitor->uname = sprintf($eLang->get('EXT_UNAME'), ucfirst($visitor->login_method));
					break;
					case 7: case 0:
						$visitor->groupname = $eLang->get('GUEST');
						if ($visitor->browser['type'] == 'robot') {
							$visitor->uname = ($visitor->browser['name'] != '') ? $visitor->browser['name'] : 'Robot';
						} else if ($visitor->browser['type'] == 'feedreader') {
							$visitor->uname = ($visitor->browser['name'] != '') ? $visitor->browser['name'] : 'Fead reader';
						} else if ($visitor->browser['type'] == 'validator') {
							$visitor->uname = ($visitor->browser['name'] != '') ? $visitor->browser['name'] : 'Validator';
						} else if ($visitor->browser['type'] == 'library') {
							$visitor->uname = ($visitor->browser['name'] != '') ? $visitor->browser['name'] : 'Library';
						} else if ($visitor->browser['type'] == 'mediaplayer') {
							$visitor->uname = ($visitor->browser['name'] != '') ? $visitor->browser['name'] : 'Media player';
						} else if ($visitor->browser['type'] == 'emailclient') {
							$visitor->uname = ($visitor->browser['name'] != '') ? $visitor->browser['name'] : 'E-mail client';
						} else {
							$visitor->uname = $eLang->get('GUEST');
						}
					break;
					default:break;
				}

				if ($visitor->uname == '') { $visitor->uname = $eLang->get('GUEST'); }
				if ($visitor->groupname == '') { $visitor->groupname = $eLang->get('GUEST'); }
				$visitors[] = $visitor;
			}

			return $visitors;
		}


		/********************/
		/* GET VISITOR INFO */
		/********************/
		private function getVisitorInfo($row, $iphelper) {
			$info = new stdClass;
			$info->browser = '';
			$info->deviceicon = '';
			$info->visitorname = $row->uname;
			$info->ipaddress = '';
			$info->os = '';

			switch ($row->browser['type']) {
				case 'robot': 
					$info->visitorname = ($row->browser['version'] != '') ? $row->browser['name'].' '.$row->browser['version'] : $row->browser['name'];
					$info->deviceicon = 'fas fa-rocket';
				break;
				case 'feedreader': 
					$info->visitorname = ($row->browser['version'] != '') ? $row->browser['name'].' '.$row->browser['version'] : $row->browser['name'];
					$info->deviceicon = 'fas fa-rss';
				break;
				case 'emailclient': 
					$info->visitorname = ($row->browser['version'] != '') ? $row->browser['name'].' '.$row->browser['version'] : $row->browser['name'];
					$info->deviceicon = 'fas fa-envelope';
				break;
				case 'library': 
					$info->visitorname = ($row->browser['version'] != '') ? $row->browser['name'].' '.$row->browser['version'] : $row->browser['name'];
					$info->deviceicon = 'fas fa-code';
				break;
				case 'validator': 
					$info->visitorname = ($row->browser['version'] != '') ? $row->browser['name'].' '.$row->browser['version'] : $row->browser['name'];
					$info->deviceicon = 'fas fa-wheelchair';
				break;
				case 'mediaplayer': 
					$info->visitorname = ($row->browser['version'] != '') ? $row->browser['name'].' '.$row->browser['version'] : $row->browser['name'];
					$info->deviceicon = 'fab fa-youtube';
				break;
				case '':
					$info->visitorname = $row->uname;
				break;
				case 'desktop':
					$info->visitorname = $row->uname;
					if ($row->browser['name'] != '') {
						$info->browser = $row->browser['name'];
						if ($row->browser['version'] != '') { $info->browser .= ' '.$row->browser['version']; }
					}
					$info->deviceicon = 'fas fa-desktop';
				break;
				case 'mobile':
					$info->visitorname = $row->uname;
					if ($row->browser['name'] != '') {
						$info->browser = $row->browser['name'];
						if ($row->browser['version'] != '') { $info->browser .= ' '.$row->browser['version']; }
					}
					$info->deviceicon = 'fas fa-mobile-alt';
				break;
				case 'tablet':
					$info->visitorname = $row->uname;
					if ($row->browser['name'] != '') {
						$info->browser = $row->browser['name'];
						if ($row->browser['version'] != '') { $info->browser .= ' '.$row->browser['version']; }
					}
					$info->deviceicon = 'fas fa-tablet-alt';
				break;
				case 'tv':
					$info->visitorname = $row->uname;
					if ($row->browser['name'] != '') {
						$info->browser = $row->browser['name'];
						if ($row->browser['version'] != '') { $info->browser .= ' '.$row->browser['version']; }
					}
					$info->deviceicon = 'fas fa-tv';
				break;
				default:
					$info->visitorname = $row->uname;
					if ($row->browser['name'] != '') {
						$info->browser = $row->browser['name'];
						if ($row->browser['version'] != '') { $info->browser .= ' '.$row->browser['version']; }
					}
				break;
			}

			if (trim($row->ip_address) != '') {
				$info->ipaddress = $iphelper->ipv6tov4($row->ip_address);//if it is a IPv4 converted to v6, convert it back to v4
			}

			if ($row->browser['os_name'] != '') {
				$info->os = $row->browser['os_name'];
				if ($row->browser['os_version'] != '') {
					$info->os .= ' '.$row->browser['os_version'];
					if ($row->browser['os_platform'] != '') { $info->os .= ' ('.$row->browser['os_platform'].')'; }
				}
			}

			return $info;
		}
	}
}


$admusers = new modadminUsers($elxmod);
$admusers->run();
unset($admusers);

?>