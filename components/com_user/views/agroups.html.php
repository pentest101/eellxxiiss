<?php 
/**
* @version		$Id: agroups.html.php 2244 2019-04-21 19:33:39Z IOS $
* @package		Elxis
* @subpackage	Component User
* @copyright	Copyright (c) 2006-2019 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class agroupsUserView extends userView {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/********************/
	/* SHOW GROUPS LIST */
	/********************/
	public function listgroups($rows, $elxis, $eLang) {
		$htmlHelper = $elxis->obj('html');

		elxisLoader::loadFile('includes/libraries/elxis/form5.class.php');

		$link = $elxis->makeAURL('user:/');
		$inlink = $elxis->makeAURL('user:/', 'inner.php');

		echo '<h2>'.$eLang->get('USER_GROUPS')."</h2>\n";

		echo '<div class="elx5_box elx5_border_blue">'."\n";
		echo '<div class="elx5_box_body">'."\n";

		echo '<div class="elx5_dataactions">'."\n";
		echo '<a href="javascript:void(null);" onclick="elx5UserEditGroup(1, 0)" class="elx5_dataaction elx5_datahighlight" data-alwaysactive="1" title="'.$eLang->get('ADD').'"><i class="fas fa-plus"></i><span class="elx5_lmobhide"> '.$eLang->get('ADD')."</span></a>\n";
		echo '<a href="javascript:void(null);" onclick="elx5UserEditGroup(0, 0);" class="elx5_dataaction" data-selector="1" title="'.$eLang->get('EDIT').'"><i class="fas fa-edit"></i><span class="elx5_lmobhide"> '.$eLang->get('EDIT')."</span></a>\n";
		echo '<a href="javascript:void(null);" class="elx5_dataaction" title="'.$eLang->get('DELETE').'" onclick="elx5DeleteTableRows(\'groupstbl\', false);" data-selector="1" data-activeclass="elx5_datawarn"><i class="fas fa-trash"></i><span class="elx5_lmobhide"> '.$eLang->get('DELETE')."</span></a>\n";
		echo "</div>\n";

		echo '<table id="groupstbl" class="elx5_datatable" data-deletelng="'.addslashes($eLang->get('DELETE_SEL_ITEMS')).'" data-listpage="'.$link.'" data-deletepage="'.$inlink.'groups/deletegroup">'."\n";
		echo "<thead>\n";
		echo "<tr>\n";
		echo $htmlHelper->tableHead('&#160;', 'elx5_nosorting elx5_center');
		echo $htmlHelper->autoSortTableHead($eLang->get('ID'), '', 'elx5_center elx5_lmobhide');
		echo $htmlHelper->autoSortTableHead($eLang->get('GROUP'));
		echo $htmlHelper->autoSortTableHead($eLang->get('ACCESS_LEVEL'), 'desc', 'elx5_center elx5_lmobhide');
		echo $htmlHelper->autoSortTableHead($eLang->get('MEMBERS'), '', 'elx5_center elx5_lmobhide');
		echo "</tr>\n";
		echo "</thead>\n";
		echo "<tbody>\n";

		if ($rows) {
			foreach ($rows as $row) {
				$groupname = $row['groupname'];
				switch ($row['gid']) {
					case 1: $groupname = $eLang->get('ADMINISTRATOR'); break;
					case 5: $groupname = $eLang->get('USER'); break;
					case 6: $groupname = $eLang->get('EXTERNALUSER'); break;
					case 7: $groupname = $eLang->get('GUEST'); break;
					default: break;
				}
				echo '<tr id="datarow'.$row['gid'].'">'."\n";
				echo '<td class="elx5_center">';
				echo '<input type="checkbox" name="dataprimary" id="dataprimary'.$row['gid'].'" class="elx5_datacheck" value="'.$row['gid'].'" />';
				echo '<label for="dataprimary'.$row['gid'].'"></label></td>'."\n";
				echo '<td data-value="'.$row['gid'].'" class="elx5_center elx5_lmobhide">'.$row['gid']."</td>\n";
				echo '<td data-value="'.$groupname.'"><a href="javascript:void(null);" onclick="elx5UserEditGroup(0, '.$row['gid'].')">'.$groupname."</a></td>\n";
				echo '<td data-value="'.$row['level'].'" class="elx5_center elx5_lmobhide">'.$row['level']."</td>\n";
				echo '<td data-value="'.$row['members'].'" class="elx5_center elx5_lmobhide">'.$row['members']."</td>\n";
				echo "</tr>\n";
			}

		} else {
			echo '<tr id="datarow0" class="elx5_rowwarn"><td class="elx5_center" colspan="5">'.$eLang->get('MULTISITES_DISABLED')."</td></tr>\n";
		}

		echo "</tbody>\n";
		echo "</table>\n";

		echo "</div>\n";//elx5_box_body
		echo "</div>\n";//elx5_box

		$attrs = array('data-addlng' => $eLang->get('ADD'), 'data-editlng' => $eLang->get('EDIT'));
		echo $htmlHelper->startModalWindow($eLang->get('ADD'), 'grm', '', true, '', '', $attrs);

		$form = new elxis5Form(array('idprefix' => 'egr_', 'labelclass' => 'elx5_label', 'sideclass' => 'elx5_labelside'));
		$form->openForm(array('name' => 'fmeditgroup', 'method' =>'post', 'action' => $inlink.'groups/', 'id' => 'fmeditgroup', 'onsubmit' => 'return false;'));

		$form->addNote($eLang->get('CNOT_MOD_GROUP'), 'elx5_invisible', array('id' => 'egr_nomodify'));

		$form->openFieldset($eLang->get('GROUP_DETAILS'));
		$form->addInfo($eLang->get('ID'), '<span id="egr_id_text">0</span>');
		$form->addText('groupname', '', $eLang->get('GROUP'), array('required' => 'required', 'dir' => 'ltr', 'maxlength' => 60));
		$max = $elxis->acl()->getLevel() - 1;
		if ($max > 99) { $max = 99; }
		$form->addSlider('level', 2, $eLang->get('ACCESS_LEVEL'), array('min' => 2, 'max' => $max, 'step' => 1, 'showvalue' => 2, 'required' => 'required'));
		$form->addInfo($eLang->get('MEMBERS'), '<span id="egr_members_text">0</span>');
		$form->closeFieldset();

		$form->openFieldset($eLang->get('GROUPS_HIERARCHY_TREE'));
		$form->addHTML('<div id="egr_groupstree_text" class="elx5_spad">-</div>');
		$form->closeFieldset();

		$form->addHidden('gid', '0');

		$form->addHTML('<div class="elx5_vpad">');
		$form->addButton('save', $eLang->get('SAVE'), 'button', array('class' => 'elx5_btn elx5_sucbtn', 'onclick' => 'elx5UserSaveGroup();', 'data-waitlng' => $eLang->get('PLEASE_WAIT'), 'data-savelng' => $eLang->get('SAVE')));
		$form->addHTML('</div>');

		$html = '<div class="elx5_tsspace"><a href="javascript:void(null);" onclick="elx5Toggle(\'egr_groupshelp\', \'elx5_info elx5_tsspace\');" class="elx5_btn">'.$eLang->get('HELP')."</a></div>\n";
		$html .= '<p id="egr_groupshelp" class="elx5_invisible">'.$eLang->get('GROUPS_GENERIC_INFO')."</p>\n";
		$form->addHTML($html);

		$form->closeForm();
		echo $htmlHelper->endModalWindow(true);
	}

}

?>