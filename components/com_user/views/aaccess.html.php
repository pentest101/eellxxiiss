<?php 
/**
* @version		$Id: aaccess.html.php 2326 2020-01-30 19:58:33Z IOS $
* @package		Elxis
* @subpackage	Component User
* @copyright	Copyright (c) 2006-2020 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class aaccessUserView extends userView {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/*****************/
	/* SHOW ACL LIST */
	/*****************/
	public function listacl($rows, $options, $acldata, $groups, $elxis, $eLang) {
		$link = $elxis->makeAURL('user:/');
		$inlink = $elxis->makeAURL('user:/', 'inner.php');

		$htmlHelper = $elxis->obj('html');

		elxisLoader::loadFile('includes/libraries/elxis/form5.class.php');

		$parts = array();
		if ($options['category'] != '') { $parts[] = 'category='.$options['category']; }
		if ($options['element'] != '') { $parts[] = 'element='.$options['element']; }
		if ($options['action'] != '') { $parts[] = 'action='.$options['action']; }
		if ($options['minlevel'] > -1) { $parts[] = 'minlevel='.$options['minlevel']; }
		if ($options['gid'] > -1) { $parts[] = 'gid='.$options['gid']; }
		if ($options['uid'] > -1) { $parts[] = 'uid='.$options['uid']; }

		$ordlink = ($parts) ? $link.'acl/?'.implode('&amp;', $parts).'&amp;' : $link.'acl/?';
		$is_filtered = $parts ? true : false;
		unset($parts);

		echo '<h2>'.$eLang->get('ACL')."</h2>\n";

		echo '<div class="elx5_box elx5_border_blue">'."\n";
		echo '<div class="elx5_box_body">'."\n";
		echo '<div class="elx5_sticky">'."\n";

		echo '<div class="elx5_dataactions">'."\n";
		echo '<a href="javascript:void(null);" onclick="elx5UserEditACL(1)" class="elx5_dataaction elx5_datahighlight" data-alwaysactive="1" title="'.$eLang->get('ADD').'"><i class="fas fa-plus"></i><span class="elx5_lmobhide"> '.$eLang->get('ADD')."</span></a>\n";
		echo '<a href="javascript:void(null);" onclick="elx5UserEditACL(0);" class="elx5_dataaction" data-selector="1" title="'.$eLang->get('EDIT').'"><i class="fas fa-edit"></i><span class="elx5_lmobhide"> '.$eLang->get('EDIT')."</span></a>\n";
		echo '<a href="javascript:void(null);" class="elx5_dataaction" title="'.$eLang->get('DELETE').'" onclick="elx5DeleteTableRows(\'acltbl\', true);" data-selector="1" data-activeclass="elx5_datawarn"><i class="fas fa-trash"></i><span class="elx5_lmobhide"> '.$eLang->get('DELETE')."</span></a>\n";
		if ($is_filtered) {
			echo '<a href="javascript:void(null);" class="elx5_dataaction elx5_dataorange" data-alwaysactive="1" data-elx5tooltip="'.$eLang->get('FILTERS_HAVE_APPLIED').'" onclick="elx5Toggle(\'aclsearchoptions\');"><i class="fas fa-filter"></i><span class="elx5_smallscreenhide"> '.$eLang->get('SEARCH_OPTIONS')."</span></a>\n";
		} else {
			echo '<a href="javascript:void(null);" class="elx5_dataaction elx5_datahighlight" data-alwaysactive="1" title="'.$eLang->get('SEARCH_OPTIONS').'" onclick="elx5Toggle(\'aclsearchoptions\');"><i class="fas fa-filter"></i><span class="elx5_smallscreenhide"> '.$eLang->get('SEARCH_OPTIONS')."</span></a>\n";
		}
		echo "</div>\n";

		echo '<div class="elx5_invisible" id="aclsearchoptions">'."\n";
		echo '<div class="elx5_actionsbox elx5_dspace">';
		$form = new elxis5Form(array('idprefix' => 'us', 'labelclass' => 'elx5_label', 'sideclass' => 'elx5_labelside'));
		$form->openForm(array('name' => 'fmsracl', 'method' => 'get', 'action' => $link.'acl/', 'id' => 'fmsracl'));
		$form->addHTML('<div class="elx5_2colwrap"><div class="elx5_2colbox elx5_spad">');

		$foptions = array();
		$foptions[] = $form->makeOption('', '- '.$eLang->get('ALL').' -');
		if ($acldata['categories']) {
			foreach ($acldata['categories'] as $cat) {
				$foptions[] = $form->makeOption($cat, $cat);
			}
		}
		$form->addSelect('category', $eLang->get('CATEGORY'), $options['category'], $foptions, array('dir' => 'ltr'));
		$foptions = array();
		$foptions[] = $form->makeOption('', '- '.$eLang->get('ALL').' -');
		if ($acldata['elements']) {
			foreach ($acldata['elements'] as $item) {
				$txt = $eLang->silentGet($item, true);
				$foptions[] = $form->makeOption($item, $txt);
			}
		}
		$form->addSelect('element', $eLang->get('ELEMENT'), $options['element'], $foptions);
		$foptions = array();
		$foptions[] = $form->makeOption('', '- '.$eLang->get('ALL').' -');
		if ($acldata['actions']) {
			foreach ($acldata['actions'] as $item) {
				$txt = $eLang->silentGet($item, true);
				$foptions[] = $form->makeOption($item, $txt);
			}
		}
		$form->addSelect('action', $eLang->get('ACTION'), $options['action'], $foptions);
		$form->addHTML('</div><div class="elx5_2colbox elx5_spad">');
		$foptions = array();
		$foptions[] = $form->makeOption('-1', '- '.$eLang->get('ALL').' -');
		for ($i=0; $i < 101; $i++) { $foptions[] = $form->makeOption($i, $i); }
		$form->addSelect('minlevel', $eLang->get('MINLEVEL'), $options['minlevel'], $foptions, array('dir' => 'ltr'));
		$form->addUsergroup('gid', $eLang->get('GROUP'), $options['gid'], 0, 100, array('showgid' => 0, 'showalloption' => 1, 'alloptionvalue' => -1));
		$v = ($options['uid'] == -1) ? '' : $options['uid'];
		$form->addText('uid', $v, $eLang->get('USER_ID'), array('dir' => 'ltr', 'maxlength' => 7));
		$form->addHTML('</div></div>');
		$form->addHidden('sn', $options['sn']);
		$form->addHidden('so', $options['so']);
		$form->addHTML('<div class="elx5_vpad">');
		$form->addButton('srcbtn', $eLang->get('SEARCH'), 'submit');
		$form->addHTML('</div>');
		$form->closeForm();
		echo "</div>\n";//elx5_actionsbox
		echo "</div>\n";//#aclsearchoptions
		echo "</div>\n";//elx5_sticky

		echo '<table id="acltbl" class="elx5_datatable" data-deletelng="'.addslashes($eLang->get('AREYOUSURE')).'" data-listpage="'.$link.'" data-deletepage="'.$inlink.'acl/deleteacl">'."\n";
		echo "<thead>\n";
		echo "<tr>\n";
		echo $htmlHelper->tableCheckAllHead('acltbl', 'ac');
		echo $htmlHelper->sortableTableHead($ordlink, $eLang->get('CATEGORY'), 'category', $options['sn'], $options['so'], 'elx5_smallscreenhide');
		echo $htmlHelper->sortableTableHead($ordlink, $eLang->get('ELEMENT'), 'element', $options['sn'], $options['so']);
		echo $htmlHelper->tableHead($eLang->get('IDENTITY'), 'elx5_nosorting elx5_center elx5_smallscreenhide');
		echo $htmlHelper->sortableTableHead($ordlink, $eLang->get('ACTION'), 'action', $options['sn'], $options['so']);
		echo $htmlHelper->sortableTableHead($ordlink, $eLang->get('MINLEVEL'), 'minlevel', $options['sn'], $options['so'], 'elx5_center elx5_lmobhide');
		echo $htmlHelper->sortableTableHead($ordlink, $eLang->get('GROUP'), 'gid', $options['sn'], $options['so'], 'elx5_tabhide');
		echo $htmlHelper->sortableTableHead($ordlink, $eLang->get('USER_ID'), 'uid', $options['sn'], $options['so'], 'elx5_center elx5_tabhide');
		echo $htmlHelper->tableHead($eLang->get('ACCESS_VALUE'), 'elx5_nosorting elx5_midscreenhide');
		echo "</tr>\n";
		echo "</thead>\n";

		echo "<tbody>\n";
		if ($rows) {
			foreach ($rows as $row) {
				$elem_txt = $eLang->silentGet($row['element'], true);
				$action_txt = $eLang->silentGet($row['action'], true);
				if ($row['aclvalue'] == 0) {
					$allowed_txt = '<span class="elx5_red">'.$eLang->get('DENIED').'</span>';
				} elseif ($row['aclvalue'] == 1) {
					$allowed_txt = '<span class="elx5_green">'.$eLang->get('ALLOWED').'</span>';
				} else if ($row['aclvalue'] == 2) {
					$allowed_txt = '<span class="elx5_green">'.$eLang->get('ALLOWED_TO_ALL').'</span>';
				} else {
					$allowed_txt = $row['aclvalue'];
				}

				switch ($row['gid']) {
					case 7: $group_txt = $eLang->get('GUEST'); break;
					case 1: $group_txt = $eLang->get('ADMINISTRATOR'); break;
					case 2: $group_txt = $eLang->get('USER'); break;
					case 6: $group_txt = $eLang->get('EXTERNALUSER'); break;
					case 0: $group_txt = '-'; break;
					default:
						$gid = (int)$row['gid'];
						$group_txt = isset($groups[$gid]) ? $groups[$gid] : $eLang->get('GROUP').' '.$gid;
					break;
				}

				$level_txt = ($row['minlevel'] == -1) ? '-' : $row['minlevel'];

				echo '<tr id="datarow'.$row['id'].'">'."\n";
				echo '<td class="elx5_center">';
				echo '<input type="checkbox" name="dataprimary" id="dataprimary'.$row['id'].'" class="elx5_datacheck" value="'.$row['id'].'" />';
				echo '<label for="dataprimary'.$row['id'].'"></label></td>'."\n";
				echo '<td class="elx5_smallscreenhide">'.$row['category'].'</td>'."\n";
				echo '<td>'.$elem_txt.'</td>'."\n";
				echo '<td class="elx5_center elx5_smallscreenhide">'.$row['identity'].'</td>'."\n";
				echo '<td>'.$action_txt.'</td>'."\n";
				echo '<td class="elx5_center elx5_lmobhide">'.$level_txt.'</td>'."\n";
				echo '<td class="elx5_tabhide">'.$group_txt.'</td>'."\n";
				echo '<td class="elx5_center elx5_tabhide">'.$row['uid'].'</td>'."\n";
				echo '<td class="elx5_midscreenhide">'.$allowed_txt.'</td>'."\n";
				echo "</tr>\n";
			}
		} else {
			echo '<tr id="datarow0" class="elx5_rowwarn"><td class="elx5_center" colspan="9">'.$eLang->get('NO_RESULTS')."</td></tr>\n";
		}
		echo "</tbody>\n";
		echo "</table>\n";

		echo "</div>\n";//elx5_box_body

		if ($rows) {
			$linkbase = $ordlink.'sn='.$options['sn'].'&amp;so='.$options['so'];
			echo $htmlHelper->tableSummary($linkbase, $options['page'], $options['maxpage'], $options['total']);
		}

		echo "</div>\n";//elx5_box

		$attrs = array('data-addlng' => $eLang->get('ADD'), 'data-editlng' => $eLang->get('EDIT'));
		echo $htmlHelper->startModalWindow($eLang->get('ADD'), 'acm', '', true, '', '', $attrs);

		$form = new elxis5Form(array('idprefix' => 'eacl_', 'labelclass' => 'elx5_label', 'sideclass' => 'elx5_labelside'));
		$form->openForm(array('name' => 'fmeditaclrule', 'method' =>'post', 'action' => $inlink.'acl/', 'id' => 'fmeditaclrule', 'onsubmit' => 'return false;'));

		$form->openFieldset($eLang->get('ELEMENT_IDENTIF'));
		$foptions = array();
		if ($acldata['categories']) {
			foreach ($acldata['categories'] as $item) {
				$foptions[] = $form->makeOption($item, $item);
			}
		}
		$form->addSelectAddOther('category', $eLang->get('CATEGORY'), '', $foptions, array('dir' => 'ltr', 'othertext' => '--- '.$eLang->get('OTHER_CATEGORY').' ---'));
		$foptions = array();
		if ($acldata['elements']) {
			foreach ($acldata['elements'] as $item) {
				$foptions[] = $form->makeOption($item, $item);
			}
		}
		$form->addSelectAddOther('element', $eLang->get('ELEMENT'), '', $foptions, array('dir' => 'ltr', 'othertext' => '--- '.$eLang->get('OTHER_ELEMENT').' ---'));
		$foptions = array();
		if ($acldata['actions']) {
			foreach ($acldata['actions'] as $item) {
				$foptions[] = $form->makeOption($item, $item);
			}
		}
		$form->addSelectAddOther('aclaction', $eLang->get('ACTION'), '', $foptions, array('dir' => 'ltr', 'othertext' => '--- '.$eLang->get('OTHER_ACTION').' ---'));
		$form->addNumber('identity', 0, $eLang->get('IDENTITY'), array('required' => 'required', 'min' => 0, 'max' => 999999, 'step' => 1, 'tip' => $eLang->get('IDENTITY_HELP')));
		$form->closeFieldset();

		$form->openFieldset($eLang->get('USERS_IDENTIF'));
		$form->addSlider('minlevel', -1, $eLang->get('MINLEVEL'), array('min' => -1, 'max' => 100, 'step' => 1, 'showvalue' => 1, 'required' => 'required'));
		$form->addUsergroup('gid', $eLang->get('GROUP'), 0, 0, 100, array('showalloption' => 1, 'alloptionvalue' => 0, 'alloptiontext' => '- '.$eLang->get('NONE').' -'));
		$form->addNumber('uid', 0, $eLang->get('USER_ID'), array('required' => 'required', 'min' => 0, 'max' => 9999999, 'step' => 1));
		$form->closeFieldset();

		$form->openFieldset($eLang->get('GRANT_ACCESS'));
		$foptions = array();
		$foptions[] = $form->makeOption(0, 0);
		$foptions[] = $form->makeOption(1, 1);
		$foptions[] = $form->makeOption(2, 2);
		$foptions[] = $form->makeOption(3, 3);
		$foptions[] = $form->makeOption(4, 4);
		$foptions[] = $form->makeOption(5, 5);
		$form->addSelect('aclvalue', $eLang->get('ACCESS_VALUE'), 1, $foptions, array('tip' => $eLang->get('ACLVALUE_HELP')));
		$form->closeFieldset();

		$form->addHidden('id', '0');

		$form->addHTML('<div class="elx5_vpad">');
		$form->addButton('save', $eLang->get('SAVE'), 'button', array('class' => 'elx5_btn elx5_sucbtn', 'onclick' => 'elx5UserSaveACL();', 'data-waitlng' => $eLang->get('PLEASE_WAIT'), 'data-savelng' => $eLang->get('SAVE')));
		$form->addHTML('</div>');

		$form->closeForm();
		echo $htmlHelper->endModalWindow(true);
	}

}

?>