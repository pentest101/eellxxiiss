<?php 
/**
* @version		$Id: utilities.html.php 2426 2021-09-26 18:24:43Z IOS $
* @package		Elxis
* @subpackage	CPanel component
* @copyright	Copyright (c) 2006-2021 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class utilitiesCPView extends cpanelView {


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/**************************/
	/* LIST BACKUP FILES HTML */
	/**************************/
	public function listBackups($rows, $folders, $tables, $elxis, $eLang) {
		$eDate = eFactory::getDate();

		$is_subsite = false;
		if (defined('ELXIS_MULTISITE') && (ELXIS_MULTISITE != 1)) { $is_subsite = true; }
		if ($elxis->getConfig('REPO_PATH') == '') {
			$backupdir = ELXIS_PATH.'/repository/backup/';
		} else {
			$backupdir = rtrim($elxis->getConfig('REPO_PATH'), '/').'/backup/';
		}

		elxisLoader::loadFile('includes/libraries/elxis/form5.class.php');

		$inlink = $elxis->makeAURL('cpanel:backup/', 'inner.php');

		$htmlHelper = $elxis->obj('html');

		echo '<h2>'.$eLang->get('BACKUP')."</h2>\n";

		if (!file_exists($backupdir)) {
			$txt = sprintf($eLang->get('FOLDER_NOT_EXIST'), '<strong>'.$backupdir.'</strong>');
			echo '<div class="elx5_warning">'.$txt."</div>\n";
		} else if (!is_writable($backupdir)) {
			$txt = sprintf($eLang->get('FOLDER_NOT_WRITE'), '<strong>'.$backupdir.'</strong>');
			echo '<div class="elx5_warning">'.$txt."</div>\n";
		}

		echo '<div class="elx5_box elx5_border_blue">'."\n";
		echo '<div class="elx5_box_body">'."\n";
		echo '<div class="elx5_sticky">'."\n";
		echo '<div class="elx5_dataactions">'."\n";
		if (!$is_subsite) {
			echo '<a href="javascript:void(null);" onclick="elx5CPNewBackup(\'fs\');" class="elx5_dataaction elx5_datahighlight" data-alwaysactive="1" title="'.$eLang->get('NEW_FS_BACKUP').'"><i class="fas fa-plus"></i> FS'."</a>\n";
		}
		echo '<a href="javascript:void(null);" onclick="elx5CPNewBackup(\'db\');" class="elx5_dataaction elx5_datahighlight" data-alwaysactive="1" title="'.$eLang->get('NEW_DB_BACKUP').'"><i class="fas fa-plus"></i> DB'."</a>\n";
		echo '<a href="javascript:void(null);" class="elx5_dataaction" title="'.$eLang->get('DELETE').'" onclick="elx5DeleteTableRows(\'backupstbl\', false);" data-selector="1" data-activeclass="elx5_datawarn"><i class="fas fa-trash"></i><span class="elx5_lmobhide"> '.$eLang->get('DELETE')."</span></a>\n";
		echo '<a href="javascript:void(null);" class="elx5_dataaction elx5_dataactive" data-alwaysactive="1" title="'.$eLang->get('OPTIONS').'" onclick="elx5Toggle(\'backupoptionsbox\');"><i class="fas fa-filter"></i><span class="elx5_lmobhide"> '.$eLang->get('OPTIONS')."</span></a>\n";
		echo "</div>\n";

		echo '<div class="elx5_invisible" id="backupoptionsbox">'."\n";
		echo '<div class="elx5_actionsbox elx5_dspace">';
		$form = new elxis5Form(array('idprefix' => 'bk', 'labelclass' => 'elx5_label', 'sideclass' => 'elx5_labelside'));
		$form->openForm(array('name' => 'fmbkoptions', 'method' =>'get', 'action' => '#', 'id' => 'fmbkoptions'));
		$form->addNote($eLang->get('BACKUP_FOLDER_TABLE_TIP'));
		$foptions = array();
		$foptions[] = $form->makeOption('', '- '.$eLang->get('ALL').' -');
		if ($folders) {
			foreach ($folders as $folder) { $foptions[] = $form->makeOption($folder, $folder); }
		}
		$form->addSelect('fsfolder', $eLang->get('FOLDER').' (FS)', '', $foptions, array('dir' => 'ltr'));
		$foptions = array();
		$foptions[] = $form->makeOption('', '- '.$eLang->get('ALL').' -');
		if ($tables) {
			foreach ($tables as $table) { $foptions[] = $form->makeOption($table, $table); }
		}
		$form->addSelect('dbtable', $eLang->get('TABLE').' (DB)', '', $foptions, array('dir' => 'ltr'));
		$form->closeForm();
		echo "</div>\n";//elx5_actionsbox
		echo "</div>\n";//#backupoptionsbox
		echo "</div>\n";//elx5_sticky
		unset($form);

		echo '<table id="backupstbl" class="elx5_datatable" data-deletelng="'.addslashes($eLang->get('DELETE_SEL_ITEMS')).'" data-deletepage="'.$inlink.'delbackup" data-backuppage="'.$inlink.'makebackup">'."\n";
		echo "<thead>\n";
		echo "<tr>\n";
		echo $htmlHelper->tableCheckAllHead('backupstbl', 'bk');
		echo $htmlHelper->autoSortTableHead($eLang->get('TYPE'), '', 'elx5_lmobhide');
		echo $htmlHelper->autoSortTableHead($eLang->get('DATE'), 'desc', 'elx5_lmobhide');
		echo $htmlHelper->autoSortTableHead($eLang->get('FILENAME'));
		echo $htmlHelper->autoSortTableHead($eLang->get('SIZE'), '', 'elx5_mobhide');
		echo "</tr>\n";
		echo "</thead>\n";

		echo "<tbody>\n";
		$total_fs = 0;
		if ($rows) {
			foreach ($rows as $row) {
				$total_fs += $row['bksize'];
				if ($row['bksize'] < 400000) {
					$size = number_format(($row['bksize'] / 1024), 2, $eLang->get('DECIMALS_SEP'), $eLang->get('THOUSANDS_SEP')).' KB';
				} else {
					$size = number_format(($row['bksize'] / (1024 * 1024)), 2, $eLang->get('DECIMALS_SEP'), $eLang->get('THOUSANDS_SEP')).' MB';
				}
				$bid = base64_encode($row['bkname']);
				$bkname = (strlen($row['bkname']) > 35) ? substr($row['bkname'], 0, 33).'...' : $row['bkname'];
				echo '<tr id="datarow'.$bid.'">'."\n";
				echo '<td class="elx5_center">';
				echo '<input type="checkbox" name="dataprimary" id="dataprimary'.$bid.'" class="elx5_datacheck" value="'.$bid.'" />';
				echo '<label for="dataprimary'.$bid.'"></label></td>'."\n";
				if ($row['bktype'] == 'db') {
					echo '<td data-value="'.$eLang->get('DATABASE').'" class="elx5_lmobhide"><i class="fas fa-database"></i> '.$eLang->get('DATABASE').'</td>'."\n";
				} else {
					echo '<td data-value="'.$eLang->get('FILESYSTEM').'" class="elx5_lmobhide"><i class="fas fa-file-archive"></i> '.$eLang->get('FILESYSTEM').'</td>'."\n";
				}
				echo '<td data-value="'.$row['bkdate'].'" class="elx5_lmobhide">'.$eDate->formatTS($row['bkdate'], $eLang->get('DATE_FORMAT_5'))."</td>\n";
				echo '<td data-value="'.$row['bkname'].'"><a href="'.$inlink.'download?f='.$bid.'" target="_blank" title="'.$eLang->get('DOWNLOAD').' '.$row['bkname'].'">'.$bkname.'</a></td>'."\n";
				echo '<td data-value="'.$row['bksize'].'" class="elx5_mobhide">'.$size.'</td>'."\n";
				echo "</tr>\n";
			}

		} else {
			echo '<tr id="datarow0" class="elx5_rowwarn"><td class="elx5_center" colspan="5">'.$eLang->get('NO_RESULTS')."</td></tr>\n";
		}
		echo "</tbody>\n";
		echo "</table>\n";

		if ($rows) {
			if ($total_fs < 400000) {
				$size = number_format(($total_fs / 1024), 2, $eLang->get('DECIMALS_SEP'), $eLang->get('THOUSANDS_SEP')).' KB';
			} else {
				$size = number_format(($total_fs / (1024 * 1024)), 2, $eLang->get('DECIMALS_SEP'), $eLang->get('THOUSANDS_SEP')).' MB';
			}
			$txt = $eLang->get('TOTAL').': <strong>'.$size.'</strong>';
			echo $htmlHelper->tableNote($txt);
		}

		echo "</div>\n";//elx5_box_body

		if ($rows) {
			$total = count($rows);
			echo $htmlHelper->tableSummary('#', 1, 1, $total);
		}

		echo "</div>\n";//elx5_box

		if (file_exists($backupdir) && is_writable($backupdir)) {
			$txt = sprintf($eLang->get('BACKUP_SAVED_INTO'), '<strong>'.$backupdir.'</strong>');
			echo '<div class="elx5_help elx5_tspace">'.$txt."</div>\n";
		}
	}


	/****************************/
	/* LIST SYSTEM ROUTING HTML */
	/****************************/
	public function listRoutes($rows, $components, $elxis, $eLang) {
		elxisLoader::loadFile('includes/libraries/elxis/form5.class.php');
		$htmlHelper = $elxis->obj('html');
		$inlink = $elxis->makeAURL('cpanel:routing/', 'inner.php');

		echo '<h2>'.$eLang->get('ELXIS_ROUTER')."</h2>\n";

		echo '<div class="elx5_box elx5_border_blue">'."\n";
		echo '<div class="elx5_box_body">'."\n";
		echo '<div class="elx5_sticky">'."\n";
		echo '<div class="elx5_dataactions">'."\n";
		echo '<a href="javascript:void(null);" onclick="elx5CPEditRoute(1);" class="elx5_dataaction elx5_datahighlight" data-alwaysactive="1" title="'.$eLang->get('NEW').'"><i class="fas fa-plus"></i><span class="elx5_lmobhide"> '.$eLang->get('NEW')."</span></a>\n";
		echo '<a href="javascript:void(null);" onclick="elx5CPEditRoute(0);" class="elx5_dataaction" data-selector="1" title="'.$eLang->get('EDIT').'"><i class="fas fa-edit"></i><span class="elx5_lmobhide"> '.$eLang->get('EDIT')."</span></a>\n";
		echo '<a href="javascript:void(null);" class="elx5_dataaction" title="'.$eLang->get('DELETE').'" onclick="elx5CPDeleteRoute();" data-selector="1" data-activeclass="elx5_datawarn"><i class="fas fa-trash"></i><span class="elx5_lmobhide"> '.$eLang->get('DELETE')."</span></a>\n";
		echo "</div>\n";
		echo "</div>\n";//elx5_sticky

		echo '<table id="routestbl" class="elx5_datatable" data-deletelng="'.addslashes($eLang->get('DELETE_SEL_ITEMS')).'" data-listpage="'.$inlink.'">'."\n";
		echo "<thead>\n";
		echo "<tr>\n";
		echo $htmlHelper->tableHead('&#160;');
		echo $htmlHelper->autoSortTableHead($eLang->get('TYPE'), '', 'elx5_lmobhide');
		echo $htmlHelper->autoSortTableHead($eLang->get('SOURCE'), 'asc');
		echo $htmlHelper->autoSortTableHead($eLang->get('ROUTE_TO'));
		echo "</tr>\n";
		echo "</thead>\n";

		echo "<tbody>\n";
		if ($rows) {
			foreach ($rows as $row) {
				$rowid = base64_encode($row->base);
				$can_delete = 0;
				if ($row->type == 'dir') {
					$can_delete = 1;
				} elseif ($row->type == 'page') {
					$can_delete = ($row->base == 'tags.html') ? 0 : 1;
				}

				echo '<tr id="datarow'.$rowid.'" data-candelete="'.$can_delete.'">'."\n";
				echo '<td class="elx5_center">';
				echo '<input type="checkbox" name="dataprimary" id="dataprimary'.$rowid.'" class="elx5_datacheck" value="'.$rowid.'" data-rtype="'.$row->type.'" />';
				echo '<label for="dataprimary'.$rowid.'"></label></td>'."\n";
				echo '<td data-value="'.$row->typetext.'" class="elx5_lmobhide">'.$row->typetext.'</td>'."\n";
				echo '<td data-value="'.$row->base.'">'.$row->base."</td>\n";
				$route_txt = ($row->stdroute == 1) ? '<span style="color:#888888;">'.$row->route.'</span>' : $row->route;
				$route_v = ($row->stdroute == 1) ? '' : $row->route;
				echo '<td id="dataroute'.$rowid.'" data-value="'.$route_v.'">'.$route_txt.'</td>'."\n";
				echo "</tr>\n";
			}
		} else {
			echo '<tr id="datarow0" class="elx5_rowwarn"><td class="elx5_center" colspan="4">'.$eLang->get('NO_RESULTS')."</td></tr>\n";
		}
		echo "</tbody>\n";
		echo "</table>\n";

		echo "</div>\n";//elx5_box_body

		if ($rows) {
			$total = count($rows);
			echo $htmlHelper->tableSummary('#', 1, 1, $total);
		}

		echo "</div>\n";//elx5_box

		$attrs = array('data-addlng' => $eLang->get('ADD_NEW_ROUTE'), 'data-editlng' => $eLang->get('REROUTE'));
		echo $htmlHelper->startModalWindow($eLang->get('ADD_NEW_ROUTE'), 'rtm', '', false, '', '', $attrs);

		$form = new elxis5Form(array('idprefix' => 'rt', 'labelclass' => 'elx5_label', 'sideclass' => 'elx5_labelside'));

		$form->openForm(array('name' => 'fmedroute', 'method' =>'post', 'action' => $inlink.'save', 'id' => 'fmedroute', 'onsubmit' => 'return false;'));
		$form->openFieldset($eLang->get('ELXIS_ROUTER'));

		$foptions = array();
		$foptions[] = $form->makeOption('page', $eLang->get('PAGE'));
		$foptions[] = $form->makeOption('dir', $eLang->get('DIRECTORY'));
		$foptions[] = $form->makeOption('component', 'Component');
		$foptions[] = $form->makeOption('frontpage', $eLang->get('HOME'));
		$form->addSelect('rtype', $eLang->get('TYPE'), 'page', $foptions);

		$form->addText('rbase', '', $eLang->get('SOURCE'), array('dir' => 'ltr'));

		$foptions = array();
		$selval = '';
		if ($components) {
			$selval = $components[0];
			foreach ($components as $comp) {
				$v = str_replace('com_', '', $comp);
				$foptions[] = $form->makeOption($v, $v);
			}
		}
		$form->addHTML('<div class="elx5_zero" id="rtroutewrap">');
		$form->addSelect('rroute', $eLang->get('ROUTE_TO'), $selval, $foptions, array('dir' => 'ltr'));
		$form->addHTML('</div>');

		$form->addHTML('<div class="elx5_invisible" id="rtroute2wrap">');
		$form->addText('rroute2', '', $eLang->get('ROUTE_TO'), array('dir' => 'ltr'));
		$form->addHTML('</div>');

		$form->addHidden('isnew', 1);

		$form->addHTML('<div class="elx5_vpad">');
		$form->addButton('save', $eLang->get('SAVE'), 'button', array('class' => 'elx5_btn elx5_sucbtn', 'onclick' => 'elx5CPSaveRoute();', 'data-waitlng' => $eLang->get('PLEASE_WAIT'), 'data-savelng' => $eLang->get('SAVE')));
		$form->addHTML('</div>');

		$form->closeFieldset();
		$form->closeForm();
		echo $htmlHelper->endModalWindow(false);
	}


	/*************************/
	/* LIST SYSTEM LOGS HTML */
	/*************************/
	public function listLogs($rows, $options, $elxis, $eLang) {
		$eDate = eFactory::getDate();

		$htmlHelper = $elxis->obj('html');
		$link = $elxis->makeAURL('cpanel:logs/');
		$inlink = $elxis->makeAURL('cpanel:logs/', 'inner.php');

		$sortlink = ($options['type'] != '') ? $link.'?type='.$options['type'].'&amp;' : $link.'?';

		$warnmessages = array();
		$infomessages = array();
		switch ($elxis->getConfig('ERROR_LOG')) {
			case 0: $warnmessages[] = $eLang->get('ERROR_LOG_DISABLED'); break;
			case 1: $infomessages[] = $eLang->get('LOG_ENABLE_ERR'); break;
			case 2: $infomessages[] = $eLang->get('LOG_ENABLE_ERRWARN'); break;
			case 3: $infomessages[] = $eLang->get('LOG_ENABLE_ERRWARNNTC'); break;
			default: break;
		}
		if ($elxis->getConfig('LOG_ROTATE') == 1) {
			$infomessages[] = $eLang->get('LOGROT_ENABLED');
		} else {
			$warnmessages[] = $eLang->get('LOGROT_DISABLED');
		}

		echo '<h2>'.$eLang->get('LOGS')."</h2>\n";

		if ($warnmessages) { echo '<div class="elx5_warning elx5_dspace">'.implode('<br />', $warnmessages)."</div>\n"; }

		echo '<div class="elx5_box elx5_border_blue">'."\n";
		echo '<div class="elx5_box_body">'."\n";
		echo '<div class="elx5_sticky">'."\n";
		echo '<div class="elx5_dataactions">'."\n";
		echo '<select name="type" id="lgtype" class="elx5_dataselect elx5_tabhide" onchange="elx5FilterPage(\'type\', this);" data-actlink="'.$link.'" data-sn="'.$options['sn'].'" data-so="'.$options['so'].'">'."\n";
		$sel = ($options['type'] == '') ? ' selected="selected"' : '';
		echo '<option value=""'.$sel.'>- '.$eLang->get('TYPE')." -</option>\n";
		$sel = ($options['type'] == 'notice') ? ' selected="selected"' : '';
		echo '<option value="notice"'.$sel.'>'.$eLang->get('NOTICE')."</option>\n";
		$sel = ($options['type'] == 'warning') ? ' selected="selected"' : '';
		echo '<option value="warning"'.$sel.'>'.$eLang->get('WARNING')."</option>\n";
		$sel = ($options['type'] == 'error') ? ' selected="selected"' : '';
		echo '<option value="error"'.$sel.'>'.$eLang->get('ERROR')."</option>\n";
		$sel = ($options['type'] == 'notfound') ? ' selected="selected"' : '';
		echo '<option value="notfound"'.$sel.'>'.$eLang->get('NOTFOUND')."</option>\n";
		$sel = ($options['type'] == 'security') ? ' selected="selected"' : '';
		echo '<option value="security"'.$sel.'>'.$eLang->get('SECURITY')."</option>\n";
		$sel = ($options['type'] == 'other') ? ' selected="selected"' : '';
		echo '<option value="other"'.$sel.'>'.$eLang->get('OTHER')."</option>\n";
		echo "</select>\n";
		echo '<a href="javascript:void(null);" class="elx5_dataaction" title="'.$eLang->get('VIEW').'" onclick="elx5CPViewLog(0);" data-selector="1"><i class="fas fa-eye"></i><span class="elx5_lmobhide"> '.$eLang->get('VIEW')."</span></a>\n";
		echo '<a href="javascript:void(null);" class="elx5_dataaction" title="'.$eLang->get('DOWNLOAD').'" onclick="elx5CPViewLog(1);" data-selector="1"><i class="fas fa-download"></i><span class="elx5_lmobhide"> '.$eLang->get('DOWNLOAD')."</span></a>\n";
		echo '<a href="javascript:void(null);" class="elx5_dataaction" title="'.$eLang->get('CLEAR_FILE').'" onclick="elx5ActionTableRows(\'logstbl\', \'clear\', true, \''.addslashes($eLang->get('CLEAR_FILE_WARN')).'\');" data-selector="1" data-activeclass="elx5_datawarn"><i class="fas fa-broom"></i><span class="elx5_tabhide"> '.$eLang->get('CLEAR_FILE')."</span></a>\n";
		echo '<a href="javascript:void(null);" class="elx5_dataaction" title="'.$eLang->get('DELETE').'" onclick="elx5DeleteTableRows(\'logstbl\', true);" data-selector="1" data-activeclass="elx5_datawarn"><i class="fas fa-trash"></i><span class="elx5_lmobhide"> '.$eLang->get('DELETE')."</span></a>\n";
		echo "</div>\n";
		echo "</div>\n";//elx5_sticky

		echo '<table id="logstbl" class="elx5_datatable" data-deletelng="'.addslashes($eLang->get('DELETE_SEL_ITEMS')).'" data-listpage="'.$inlink.'" data-deletepage="'.$inlink.'delete">'."\n";
		echo "<thead>\n";
		echo "<tr>\n";
		echo $htmlHelper->tableCheckAllHead('logstbl', 'lg');
		echo $htmlHelper->sortableTableHead($sortlink, $eLang->get('TYPE'), 'type', $options['sn'], $options['so'], 'elx5_tabhide');
		echo $htmlHelper->sortableTableHead($sortlink, $eLang->get('FILENAME'), 'filename', $options['sn'], $options['so']);
		echo $htmlHelper->sortableTableHead($sortlink, $eLang->get('PERIOD'), 'logperiod', $options['sn'], $options['so'], 'elx5_lmobhide');
		echo $htmlHelper->sortableTableHead($sortlink, $eLang->get('LAST_MODIFIED'), 'lastmodified', $options['sn'], $options['so']);
		echo $htmlHelper->sortableTableHead($sortlink, $eLang->get('SIZE'), 'size', $options['sn'], $options['so'], 'elx5_lmobhide');
		echo "</tr>\n";
		echo "</thead>\n";

		echo "<tbody>\n";
		if ($rows) {
			foreach ($rows as $row) {
				$rowid = base64_encode($row->filename);
				$moddate = $eDate->formatTS($row->lastmodified, $eLang->get('DATE_FORMAT_4'));
				if ($row->size > 700000) {
					$fsize = $row->size / 1048576;
					$fsize = number_format($fsize, 2, $eLang->get('DECIMALS_SEP'), $eLang->get('THOUSANDS_SEP')).' MB';
				} else {
					$fsize = $row->size / 1024;
					$fsize = number_format($fsize, 2, $eLang->get('DECIMALS_SEP'), $eLang->get('THOUSANDS_SEP')).' KB';
				}

				echo '<tr id="datarow'.$rowid.'">'."\n";
				echo '<td class="elx5_center">';
				echo '<input type="checkbox" name="dataprimary" id="dataprimary'.$rowid.'" class="elx5_datacheck" value="'.$rowid.'" />';
				echo '<label for="dataprimary'.$rowid.'"></label></td>'."\n";
				echo '<td data-value="'.$row->typetext.'" class="elx5_tabhide">'.$row->typetext.'</td>'."\n";
				echo '<td data-value="'.$row->filename.'"><a href="javascript:void(null);" title="'.$eLang->get('VIEW').' '.$row->filename.'" onclick="elx5CPViewLog(0, \''.$rowid.'\');">'.$row->filename.'</a></td>'."\n";
				echo '<td data-value="'.$row->logperiod.'" class="elx5_lmobhide">'.$row->logdate.'</td>'."\n";
				echo '<td data-value="'.$row->lastmodified.'">'.$moddate.'</td>'."\n";
				echo '<td data-value="'.$row->size.'" class="elx5_lmobhide">'.$fsize.'</td>'."\n";
				echo "</tr>\n";
			}
		} else {
			echo '<tr id="datarow0" class="elx5_rowwarn"><td class="elx5_center" colspan="6">'.$eLang->get('NO_RESULTS')."</td></tr>\n";
		}
		echo "</tbody>\n";
		echo "</table>\n";

		echo "</div>\n";//elx5_box_body
		if ($rows) {
			$linkbase = $link.'?sn='.$options['sn'].'&amp;so='.$options['so'];
			if ($options['type'] != '') { $linkbase .= '&amp;type='.$options['type']; }
			echo $htmlHelper->tableSummary($linkbase, $options['page'], $options['maxpage'], $options['total']);
		}
		echo "</div>\n";//elx5_box

		if ($infomessages) { echo '<div class="elx5_info elx5_tspace">'.implode('<br />', $infomessages)."</div>\n"; }
	}


	/***************************/
	/* HTML LIST DEFENDER BANS */
	/***************************/
	public function listBanned($ban, $eLang) {
		$eDate = eFactory::getDate();

		$num_txt = ($ban) ? ' <span class="elx5_orange">'.count($ban).'</span>' : '';

		echo '<div class="elx5_pad">'."\n";
		echo '<div class="elx5_box elx5_border_orange">'."\n";
		echo '<div class="elx5_box_body">'."\n";
		echo '<div class="elx5_dataactions"><h3 class="elx5_box_title">'.$eLang->get('DEFENDER_BANS').$num_txt."</h3></div>\n";
		echo '<table dir="'.$eLang->getinfo('DIR').'" class="elx5_datatable">'."\n";
		echo "<tr>\n";
		echo '<th class="elx5_center elx5_lmobhide">#'."</th>\n";
		echo '<th>IP'."</th>\n";
		echo '<th class="elx5_center elx5_tabhide">'.$eLang->get('TIMES_BLOCKED')."</th>\n";
		echo '<th class="elx5_lmobhide>'.$eLang->get('REFER_CODE')."</th>\n";
		echo '<th class="elx5_mobhide>'.$eLang->get('DATE')."</th>\n";
		echo "</tr>\n";
		$k = 0;
		if ($ban) {
			$i = 1;
			foreach ($ban as $ip => $row) {
				$ip = str_replace('x', '.', $ip);
				$ip = str_replace('y', ':', $ip);
				$times_txt = ($row['times'] >= 3) ? '<span style="font-weight:bold; color: #ff0000;">'.$row['times'].'</span>' : $row['times'];
				$date_txt = $eDate->formatDate($row['date'], $eLang->get('DATE_FORMAT_12'));
				echo '<tr>'."\n";
				echo '<td class="elx5_center elx5_lmobhide">'.$i."</td>\n";
				echo '<td>'.$ip."</td>\n";
				echo '<td class="elx5_center elx5_tabhide">'.$times_txt."</td>\n";
				echo '<td class="elx5_lmobhide>'.$row['refcode']."</td>\n";
				echo '<td class="elx5_mobhide>'.$date_txt."</td>\n";
				echo "</tr>\n";
				$k = 1 - $k;
				$i++;
			}
		} else {
			echo '<tr class="elx5_rowwarn"><td class="elx5_center" colspan="5">'.$eLang->get('NO_RESULTS')."</td></tr>\n";
		}
		echo "</table>\n";
		echo "</div>\n</div>\n";
		echo "</div>\n";
	}


	/**************************/
	/* LIST CACHED ITEMS HTML */
	/**************************/
	public function listCache($rows, $options, $elxis, $eLang) {
		if ($elxis->getConfig('REPO_PATH') == '') {
			$cachedir = ELXIS_PATH.'/repository/cache/';
		} else {
			$cachedir = rtrim($elxis->getConfig('REPO_PATH'), '/').'/cache/';
		}

		$htmlHelper = $elxis->obj('html');
		$link = $elxis->makeAURL('cpanel:cache/');
		$inlink = $elxis->makeAURL('cpanel:cache/', 'inner.php');

		echo '<h2>'.$eLang->get('CACHE')."</h2>\n";

		if (!file_exists($cachedir)) {
			$txt = sprintf($eLang->get('FOLDER_NOT_EXIST'), '<strong>'.$cachedir.'</strong>');
			echo '<div class="elx5_warning">'.$txt."</div>\n";
		} else if (!is_writable($cachedir)) {
			$txt = sprintf($eLang->get('FOLDER_NOT_WRITE'), '<strong>'.$cachedir.'</strong>');
			echo '<div class="elx5_warning">'.$txt."</div>\n";
		}

		echo '<div class="elx5_box elx5_border_blue">'."\n";
		echo '<div class="elx5_box_body">'."\n";
		echo '<div class="elx5_sticky">'."\n";
		echo '<div class="elx5_dataactions">'."\n";
		echo '<a href="javascript:void(null);" class="elx5_dataaction" title="'.$eLang->get('DELETE').'" onclick="elx5DeleteTableRows(\'cachetbl\', true);" data-selector="1" data-activeclass="elx5_datawarn"><i class="fas fa-trash"></i> '.$eLang->get('DELETE')."</a>\n";
		echo "</div>\n";
		echo "</div>\n";//elx5_sticky

		echo '<table id="cachetbl" class="elx5_datatable" data-deletelng="'.addslashes($eLang->get('DELETE_SEL_ITEMS')).'" data-deletepage="'.$inlink.'delcache">'."\n";
		echo "<thead>\n";
		echo "<tr>\n";
		echo $htmlHelper->tableCheckAllHead('cachetbl', 'cach');
		echo $htmlHelper->sortableTableHead($link.'?', $eLang->get('FILE'), 'item', $options['sn'], $options['so']);
		echo $htmlHelper->sortableTableHead($link.'?', $eLang->get('UPDATED_BEFORE'), 'dt', $options['sn'], $options['so'], 'elx5_lmobhide');
		echo $htmlHelper->sortableTableHead($link.'?', $eLang->get('SIZE'), 'size', $options['sn'], $options['so'], 'elx5_mobhide');
		echo "</tr>\n";
		echo "</thead>\n";

		echo "<tbody>\n";
		if ($rows) {
			foreach ($rows as $row) {
				$cid = base64_encode($row['item']);
				if ($row['size'] < 400000) {
					$size = number_format(($row['size'] / 1024), 2, $eLang->get('DECIMALS_SEP'), $eLang->get('THOUSANDS_SEP')).' KB';
				} else {
					$size = number_format(($row['size'] / (1024 * 1024)), 2, $eLang->get('DECIMALS_SEP'), $eLang->get('THOUSANDS_SEP')).' MB';
				}

				$itemname = (strlen($row['item']) > 40) ? substr($row['item'], 0, 37).'...' : $row['item'];
				echo '<tr id="datarow'.$cid.'">'."\n";
				echo '<td class="elx5_center">';
				echo '<input type="checkbox" name="dataprimary" id="dataprimary'.$cid.'" class="elx5_datacheck" value="'.$cid.'" />';
				echo '<label for="dataprimary'.$cid.'"></label></td>'."\n";
				echo '<td data-value="'.$row['item'].'" title="'.$row['item'].'">'.$itemname.'</td>'."\n";
				echo '<td data-value="'.$row['dt'].'" class="elx5_lmobhide">'.$row['timediff'].'</td>'."\n";
				echo '<td data-value="'.$row['size'].'" class="elx5_mobhide">'.$size.'</td>'."\n";
				echo "</tr>\n";
			}
		} else {
			echo '<tr id="datarow0" class="elx5_rowwarn"><td class="elx5_center" colspan="4">'.$eLang->get('NO_RESULTS')."</td></tr>\n";
		}
		echo "</tbody>\n";
		echo "</table>\n";

		echo "</div>\n";//elx5_box_body
		if ($rows) {
			$linkbase = $link.'?sn='.$options['sn'].'&amp;so='.$options['so'];
			echo $htmlHelper->tableSummary($linkbase, $options['page'], $options['maxpage'], $options['total']);
		}
		echo "</div>\n";//elx5_box

		$txt = sprintf($eLang->get('CACHE_SAVED_INTO'), '<strong>'.$cachedir.'</strong>');
		echo '<div class="elx5_info elx5_tspace">'.$txt."</div>\n";
	}


	/*******************************/
	/* LIST FILES FOR EDITING HTML */
	/*******************************/
	public function codeEditorListHTML($rows, $extensions, $curextension, $elxis, $eLang) {
		$eDate = eFactory::getDate();
		
		$htmlHelper = $elxis->obj('html');
		$link = $elxis->makeAURL('cpanel:codeeditor/');
		$inlink = $elxis->makeAURL('cpanel:codeeditor/', 'inner.php');

		echo '<h2>Code editor</h2>'."\n";
		echo '<div class="elx5_help elx5_dspace">'.$eLang->get('CODE_EDITOR_WARN')."</div>\n";

		echo '<div class="elx5_box elx5_border_blue">'."\n";
		echo '<div class="elx5_box_body">'."\n";
		echo '<div class="elx5_sticky">'."\n";
		echo '<div class="elx5_dataactions">'."\n";
		echo '<select name="extension" id="cedextension" class="elx5_dataselect elx5_lmobhide" onchange="elx5FilterTable(\'ceditortbl\', \'data-extension\', this);">'."\n";
		$sel = ($curextension == '') ? ' selected="selected"' : '';
		echo '<option value=""'.$sel.'>- '.$eLang->get('EXTENSION')." -</option>\n";
		if ($extensions) {
			foreach ($extensions as $extension) {
				$sel = ($curextension == $extension) ? ' selected="selected"' : '';
				echo '<option value="'.$extension.'"'.$sel.'>'.$extension."</option>\n";
			}
		}
		echo "</select>\n";
		echo '<a href="javascript:void(null);" class="elx5_dataaction" title="'.$eLang->get('VIEW').'" onclick="elx5CPViewCodeFile();" data-selector="1"><i class="fas fa-eye"></i> '.$eLang->get('VIEW')."</a>\n";
		echo '<a href="javascript:void(null);" class="elx5_dataaction" title="Validate" onclick="elx5CPValidateCodeFile();" data-selector="1" data-activeclass="elx5_datahighlight"><i class="fas fa-check"></i> Validate'."</a>\n";
		echo "</div>\n";
		echo "</div>\n";//elx5_sticky

		echo '<table id="ceditortbl" class="elx5_datatable" data-deletelng="'.addslashes($eLang->get('DELETE_SEL_ITEMS')).'" data-listpage="'.$inlink.'">'."\n";
		echo "<thead>\n";
		echo "<tr>\n";
		echo $htmlHelper->tableHead('&#160;');
		echo $htmlHelper->autoSortTableHead($eLang->get('TYPE'), '', 'elx5_mobhide');
		echo $htmlHelper->autoSortTableHead($eLang->get('FILE'));
		echo $htmlHelper->autoSortTableHead($eLang->get('EXTENSION'), '', 'elx5_lmobhide');
		echo $htmlHelper->autoSortTableHead($eLang->get('LAST_MODIFIED'), '', 'elx5_smallscreenhide');
		echo $htmlHelper->autoSortTableHead($eLang->get('SIZE'), '', 'elx5_tabhide');
		echo "</tr>\n";
		echo "</thead>\n";

		echo "<tbody>\n";
		if ($rows) {
			foreach ($rows as $row) {
				$css_class_str = '';
				if (strpos($row['file'], 'user.config') === 0) {
					$css_class_str = ' class="elx5_bold elx5_green"';
				}

				$trclass_str = '';
				if ($curextension != '') {
					if ($row['extension'] != $curextension) { $trclass_str = ' class="elx5_invisible'; }
				}
				echo '<tr id="datarow'.$row['id'].'"'.$trclass_str.' data-type="'.$row['type'].'" data-relpath="'.addslashes($row['relpath']).'" data-extension="'.$row['extension'].'">'."\n";
				echo '<td class="elx5_center">';
				echo '<input type="checkbox" name="dataprimary" id="dataprimary'.$row['id'].'" class="elx5_datacheck" value="'.$row['id'].'" />';
				echo '<label for="dataprimary'.$row['id'].'"></label></td>'."\n";
				switch ($row['type']) {
					case 'css': $txt = '<i class="fas fa-paint-brush"></i> CSS'; break;
					case 'js': $txt = '<i class="fas fa-code"></i> JavaScript'; break;
					case 'php': $txt = '<i class="fab fa-php"></i> PHP'; break;
					default: $txt = $row['type']; break;
				}
				echo '<td data-value="'.$row['type'].'" class="elx5_mobhide">'.$txt.'</td>'."\n";
				echo '<td data-value="'.$row['file'].'"><a href="'.$link.'edit.html?f='.$row['id'].'" title="'.$eLang->get('EDIT').'"'.$css_class_str.'>'.$row['file'].'</a></td>'."\n";
				echo '<td data-value="'.$row['extension'].'" class="elx5_lmobhide"><span'.$css_class_str.'>'.$row['extension'].'</span></td>'."\n";

				if ($row['lastmodified'] > 0) {
					$txt = $eDate->formatTS($row['lastmodified'], $eLang->get('DATE_FORMAT_4'));
				} else {
					$txt = $eLang->get('NEVER');
				}
				echo '<td data-value="'.$row['lastmodified'].'" class="elx5_smallscreenhide">'.$txt.'</td>'."\n";
				if ($row['size'] < 400000) {
					$txt = number_format(($row['size'] / 1024), 2, $eLang->get('DECIMALS_SEP'), $eLang->get('THOUSANDS_SEP')).' KB';
				} else {
					$txt = number_format(($row['size'] / (1024 * 1024)), 2, $eLang->get('DECIMALS_SEP'), $eLang->get('THOUSANDS_SEP')).' MB';
				}
				echo '<td data-value="'.$row['size'].'" class="elx5_tabhide">'.$txt.'</td>'."\n";
				echo "</tr>\n";
			}
		} else {
			echo '<tr id="datarow0" class="elx5_rowwarn"><td class="elx5_center" colspan="6">'.$eLang->get('NO_RESULTS')."</td></tr>\n";
		}
		echo "</tbody>\n";
		echo "</table>\n";

		echo "</div>\n";//elx5_box_body

		if ($rows) {
			$total = count($rows);
			echo $htmlHelper->tableSummary('#', 1, 1, $total);
		}

		echo "</div>\n";//elx5_box
		echo '<div class="elx5_invisible" id="elxcp_rootlink">'.$elxis->secureBase()."</div>\n";
	}


	/*******************************/
	/*EDIT CUSTOM CSS/JS FILE HTML */
	/*******************************/
	public function editCodeHTML($filedata, $contents, $editortype, $elxis, $eLang) {
		elxisLoader::loadFile('includes/libraries/elxis/form5.class.php');

		$inlink = $elxis->makeAURL('cpanel:codeeditor/', 'inner.php');
		$pinglink = $elxis->makeAURL('cpanel:beat', 'inner.php');

		echo '<h2><i class="fas fa-code elx5_blue"></i> '.$eLang->get('EDIT_CODE').' <span>'.$filedata['type'].'</span></h2>'."\n";
		echo '<div class="elx5_help elx5_dspace"><span class="elx5_green">'.$filedata['extension'].'</span> &gt; <span class="elx5_blue">'.$filedata['file']."</span></div>\n";

		$form = new elxis5Form(array('idprefix' => 'eco', 'labelclass' => 'elx5_labelblock', 'sideclass' => 'elx5_zero'));
		$form->openForm(array('name' => 'fmedcode', 'method' =>'post', 'action' => $inlink.'save', 'id' => 'fmedcode'));
		$form->addHTML('<div class="elx5_vspace">');
		$form->addTextarea('contents', $contents, 'Code', array('dir' => 'ltr', 'forcedir' => 'ltr', 'class' => 'elx5_textarea elxcp_editorarea', 'onlyelement' => 1));
		$form->addHTML('</div>');
		$form->addHidden('id', $filedata['id']);
		$form->addHidden('task', '');
		$form->addToken('codeeditor');
		$form->closeForm();

		echo '<script>'."\n";
		echo 'var elxCodeEditor = CodeMirror.fromTextArea(document.getElementById(\'ecocontents\'), {'."\n";
		echo 'value: document.getElementById(\'ecocontents\').value,'."\n";
		echo 'lineNumbers: true,'."\n";
		echo 'lineWrapping: true,'."\n";
		echo 'indentWithTabs: true,'."\n";
		echo 'indentUnit: 4,'."\n";
		echo 'mode: \''.$editortype.'\','."\n";
		echo 'spellcheck: true,'."\n";
		echo 'autocorrect: true,'."\n";
		echo "});\n";
		echo 'setInterval(function(){ elx5NoExpirePing(\''.$pinglink.'\'); }, 540000);'."\n";//9 minutes
		echo "</script>\n";
	}
}

?>