<?php 
/**
* @version		$Id: collection.html.php 1984 2018-10-07 10:08:53Z IOS $
* @package		Elxis
* @subpackage	Component eMenu
* @copyright	Copyright (c) 2006-2019 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class collectionEmenuView extends emenuView {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/*************************/
	/* SHOW COLLECTIONS LIST */
	/*************************/
	public function listcollections($rows, $options, $eLang, $elxis) {
		$link = $elxis->makeAURL('emenu:/');
		$inlink = $elxis->makeAURL('emenu:/', 'inner.php');

		$htmlHelper = $elxis->obj('html');

		echo '<h2>'.$eLang->get('MENU_ITEM_COLLECTIONS')."</h2>\n";

		echo '<div class="elx5_box elx5_border_blue">'."\n";
		echo '<div class="elx5_box_body">'."\n";
		echo '<div class="elx5_dataactions">'."\n";
		if ($elxis->acl()->check('com_emenu', 'menu', 'add') > 0) {
			echo '<a href="javascript:void(null);" onclick="elx5ModalOpen(\'ecol\');" class="elx5_dataaction elx5_datahighlight" data-alwaysactive="1" title="'.$eLang->get('ADD').'">'.$eLang->get('ADD')."</a>\n";
		}
		if ($elxis->acl()->check('com_emenu', 'menu', 'delete') > 0) {
			echo '<a href="javascript:void(null);" class="elx5_dataaction" title="'.$eLang->get('DELETE').'" onclick="elx5DeleteTableRows(\'collectionstbl\', false);" data-selector="1" data-activeclass="elx5_datawarn">'.$eLang->get('DELETE')."</a>\n";
		}
		echo "</div>\n";

		echo '<table id="collectionstbl" class="elx5_datatable" data-deletelng="'.addslashes($eLang->get('WARN_DELETE_COLLECT')).'" data-deletepage="'.$inlink.'deletecol">'."\n";
		echo "<thead>\n";
		echo "<tr>\n";
		echo $htmlHelper->tableHead('&#160;', 'elx5_nosorting elx5_center');
		echo $htmlHelper->sortableTableHead($link.'?', $eLang->get('COLLECTION'), 'collection', $options['sn'], $options['so']);
		echo $htmlHelper->sortableTableHead($link.'?', $eLang->get('MENU_ITEMS'), 'items', $options['sn'], $options['so'], 'elx5_lmobhide');
		echo $htmlHelper->tableHead($eLang->get('MODULES'), 'elx5_nosorting elx5_lmobhide');
		echo "</tr>\n";
		echo "</thead>\n";

		echo "<tbody>\n";
		if ($rows) {
			foreach ($rows as $k => $row) {
				$modsarr = array();
				if ($row->modules) {
					foreach ($row->modules as $mod) {
						$modsarr[] = $mod['title'].' <span dir="ltr">('.$mod['position'].')</span>';
					}
				}
				echo '<tr id="datarow'.$row->collection.'">'."\n";
				echo '<td class="elx5_center">';
				echo '<input type="checkbox" name="dataprimary" id="dataprimary'.$row->collection.'" class="elx5_datacheck" value="'.$row->collection.'" />';
				echo '<label for="dataprimary'.$row->collection.'"></label></td>'."\n";
				echo '<td><a href="'.$link.'mitems/'.$row->collection.'.html" title="'.$eLang->get('MANAGE_MENU_ITEMS').'">'.$row->collection.'</a></td>'."\n";
				echo '<td class="elx5_lmobhide">'.$row->items.'</td>'."\n";
				echo '<td class="elx5_lmobhide">'.implode(', ', $modsarr).'</td>'."\n";
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

		$clang = $elxis->getConfig('LANG');
		$cinfo = $eLang->getallinfo($clang);

		echo $htmlHelper->startModalWindow($eLang->get('ADD_NEW_COLLECT'), 'ecol');
		echo '<form name="fmaddcollection" id="fmaddcollection" method="post" action="'.$inlink.'savecol" class="elx5_form">'."\n";
		echo '<div class="elx5_formrow">'.$eLang->get('COLLECT_NAME_INFO')."</div>\n";
		echo '<div class="elx5_formrow">'."\n";
		echo '<label class="elx5_label" for="ecolcollection">'.$eLang->get('COLLECTION')."</label>\n";
		echo '<div class="elx5_labelside">'."\n";
		echo '<input type="text" name="collection" id="ecolcollection" dir="ltr" class="elx5_text" value="" maxlength="30" placeholder="'.$eLang->get('COLLECTION').'" required="required" />'."\n";
		echo "</div>\n</div>\n";
		echo '<div class="elx5_formrow">'."\n";
		echo '<label class="elx5_label" for="ecolmodtitle">'.$eLang->get('MODULE_TITLE')."</label>\n";
		echo '<div class="elx5_labelside">'."\n";
		echo '<input type="text" name="modtitle" id="ecolmodtitle" dir="'.$cinfo['DIR'].'" class="elx5_text" value="" maxlength="120" placeholder="'.$eLang->get('MODULE_TITLE').'" required="required" />'."\n";
		echo "</div>\n</div>\n";
		echo '<div class="elx5_vpad">'."\n";
		echo '<button type="button" class="elx5_btn elx5_sucbtn" id="ecolsave" name="save" onclick="emenuSaveCollection();">'.$eLang->get('SAVE')."</button> \n";
		echo "</div>\n";
		echo "</form>\n";
		echo $htmlHelper->endModalWindow();
	}

}

?>