<?php 
/**
* @version		$Id: templates.html.php 2327 2020-01-30 20:01:30Z IOS $
* @package		Elxis
* @subpackage	Component Extensions Manager
* @copyright	Copyright (c) 2006-2020 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class templatesExtmanagerView extends extmanagerView {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/******************************/
	/* LIST MODULE POSITIONS HTML */
	/******************************/
	public function listPositionsHTML($rows, $options, $elxis, $eLang) {
		$htmlHelper = $elxis->obj('html');

		$extmanlink = $elxis->makeAURL('extmanager:/');
		$link = $extmanlink.'templates/positions.html';
		$inlink = $elxis->makeAURL('extmanager:templates/', 'inner.php');
		$canedit = ($elxis->acl()->check('com_extmanager', 'templates', 'edit') > 0) ? true : false;
		echo '<h2>'.$eLang->get('MODULE_POSITIONS')."</h2>\n";

		echo '<div class="elx5_box elx5_border_blue">'."\n";
		echo '<div class="elx5_box_body">'."\n";
		if ($canedit) {
			echo '<div class="elx5_sticky">'."\n";
			echo '<div class="elx5_dataactions">'."\n";
			echo '<a href="javascript:void(null);" onclick="extMan5EditPosition(0);" class="elx5_dataaction elx5_datahighlight" data-alwaysactive="1" title="'.$eLang->get('NEW').'"><i class="fas fa-plus"></i> '.$eLang->get('NEW')."</a>\n";
			echo '<a href="javascript:void(null);" onclick="extMan5EditPosition(-1);" class="elx5_dataaction elx5_lmobhide" data-selector="1" title="'.$eLang->get('EDIT').'"><i class="fas fa-edit"></i> '.$eLang->get('EDIT')."</a>\n";
			echo '<a href="javascript:void(null);" class="elx5_dataaction" title="'.$eLang->get('DELETE').'" onclick="elx5DeleteTableRows(\'positionstbl\', false);" data-selector="1" data-activeclass="elx5_datawarn"><i class="fas fa-trash"></i> '.$eLang->get('DELETE')."</a>\n";
			echo "</div>\n";
			echo "</div>\n";//elx5_sticky
		}

		echo '<table id="positionstbl" class="elx5_datatable" data-deletelng="'.addslashes($eLang->get('AREYOUSURE')).'" data-deletepage="'.$inlink.'deleteposition">'."\n";
		echo "<thead>\n";
		echo "<tr>\n";
		echo $htmlHelper->tableHead('&#160;', 'elx5_nosorting elx5_center');
		echo $htmlHelper->sortableTableHead($link.'?', $eLang->get('POSITION'), 'position', $options['sn'], $options['so'], '');
		echo $htmlHelper->sortableTableHead($link.'?', $eLang->get('MODULES'), 'modules', $options['sn'], $options['so'], 'elx5_center elx5_mobhide');
		echo $htmlHelper->sortableTableHead($link.'?', $eLang->get('DESCRIPTION'), 'description', $options['sn'], $options['so'], 'elx5_lmobhide');
		echo "</tr>\n";
		echo "</thead>\n";
		echo "<tbody>\n";
		if ($rows) {
			foreach ($rows as $row) {
				echo '<tr id="datarow'.$row->id.'">'."\n";
				echo '<td class="elx5_center">';
				echo '<input type="checkbox" name="dataprimary" id="dataprimary'.$row->id.'" class="elx5_datacheck" value="'.$row->id.'" />';
				echo '<label for="dataprimary'.$row->id.'"></label></td>'."\n";
				if ($canedit) {
					echo '<td id="positionname'.$row->id.'" data-value="'.$row->position.'"><a href="javascript:void(null);" onclick="extMan5EditPosition('.$row->id.');">'.$row->position.'</a></td>'."\n";
				} else {
					echo '<td id="positionname'.$row->id.'" data-value="'.$row->position.'">'.$row->position.'</td>'."\n";
				}
				if ($row->modules > 0) {
					echo '<td class="elx5_center elx5_mobhide"><a href="'.$extmanlink.'modules/?position='.$row->position.'&section=frontend" title="'.$eLang->get('MODULES').'">'.$row->modules.'</a></td>'."\n";
				} else {
					echo '<td class="elx5_center elx5_mobhide">'.$row->modules.'</td>'."\n";
				}
				echo '<td id="positiondesc'.$row->id.'" class="elx5_lmobhide" data-value="'.addslashes($row->description).'">'.$row->description.'</td>'."\n";
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

		if ($canedit) {
			elxisLoader::loadFile('includes/libraries/elxis/form5.class.php');
			echo $htmlHelper->startModalWindow('<i class="fas fa-edit"></i> '.$eLang->get('NEW_MOD_POSITION'), 'emp');
			$form = new elxis5Form(array('idprefix' => 'emp', 'labelclass' => 'elx5_label', 'sideclass' => 'elx5_labelside'));
			$form->openForm(array('name' => 'fmemodpos', 'method' =>'post', 'action' => $inlink.'saveposition', 'id' => 'fmemodpos', 'onsubmit' => 'return false;'));
			
			$form->openFieldset();
			$form->addText('position', '', $eLang->get('POSITION'), array('dir' => 'ltr', 'required' => 1));
			$form->addText('description', '', $eLang->get('DESCRIPTION'), array('dir' => 'ltr'));
			$form->addHidden('id', '0', array('dir' => 'ltr'));
			$form->addHidden('lngnew', $eLang->get('NEW_MOD_POSITION'));
			$txt = sprintf($eLang->get('EDIT_MOD_POSITION'), 'ZZZ');
			$form->addHidden('lngedit', $txt);

			$form->addHTML('<div class="elx5_vpad">');
			$form->addButton('possave', $eLang->get('SAVE'), 'button', array('onclick' => 'extMan5SavePosition();', 'class' => 'elx5_btn elx5_sucbtn', 'fontawesome' => 'fas fa-save'));
			$form->addHTML('</div>');
			$form->addToken('modpos');
			$form->closeFieldset();
			$form->closeForm();
			echo $htmlHelper->endModalWindow(false);
		}
	}

}

?>