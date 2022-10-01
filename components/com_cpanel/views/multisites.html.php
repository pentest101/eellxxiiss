<?php 
/**
* @version		$Id: multisites.html.php 1982 2018-09-30 15:25:08Z IOS $
* @package		Elxis
* @subpackage	CPanel component
* @copyright	Copyright (c) 2006-2018 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class multisitesCPView extends cpanelView {


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/******************************/
	/* LIST AND MANAGE MULTISITES */
	/******************************/
	public function listSites($rows, $dbtypes, $importers, $newid, $elxis, $eLang) {
		elxisLoader::loadFile('includes/libraries/elxis/form5.class.php');
		$htmlHelper = $elxis->obj('html');

		$inlink = $elxis->makeAURL('cpanel:multisites/', 'inner.php');

		echo '<h2>'.$eLang->get('MULTISITES')."</h2>\n";

		echo '<div class="elx5_box elx5_border_blue">'."\n";
		echo '<div class="elx5_box_body">'."\n";
		echo '<div class="elx5_dataactions">'."\n";
		if ($rows) {
			$ctxt = addslashes($eLang->get('DISABLE_MULTISITES_WARN'));
			echo '<a href="javascript:void(null);" class="elx5_dataaction elx5_datawarn elx5_lmobhide" data-alwaysactive="1" title="'.$eLang->get('DISABLE_MULTISITES').'" onclick="elx5CPConfirmLink(\''.$ctxt.'\', \''.$inlink.'disable\')" data-activeclass="elx5_datawarn">'.$eLang->get('DISABLE')."</a>\n";
			echo '<a href="javascript:void(null);" onclick="elx5CPMultisite(1, \''.ELXIS_ADIR.'\');" class="elx5_dataaction elx5_datahighlight" data-alwaysactive="1" title="'.$eLang->get('ADD').'">'.$eLang->get('ADD')."</a>\n";
			echo '<a href="javascript:void(null);" onclick="elx5CPMultisite(0, \''.ELXIS_ADIR.'\');" class="elx5_dataaction" title="'.$eLang->get('EDIT').'" data-selector="1">'.$eLang->get('EDIT')."</a>\n";
			echo '<a href="javascript:void(null);" class="elx5_dataaction elx5_lmobhide" title="'.$eLang->get('DELETE').'" onclick="elx5DeleteTableRows(\'msitestbl\', false);" data-selector="1" data-activeclass="elx5_datawarn">'.$eLang->get('DELETE')."</a>\n";
		} else {
			$ctxt = addslashes($eLang->get('AREYOUSURE'));
			echo '<a href="javascript:void(null);" class="elx5_dataaction elx5_datahighlight" data-alwaysactive="1" title="'.$eLang->get('ENABLE').'" onclick="elx5CPConfirmLink(\''.$ctxt.'\', \''.$inlink.'enable\')" data-activeclass="elx5_datahighlight">'.$eLang->get('ENABLE')."</a>\n";
		}
		echo "</div>\n";

		echo '<table id="msitestbl" class="elx5_datatable" data-deletelng="'.addslashes($eLang->get('DELETE_SEL_ITEMS')).'" data-listpage="'.$inlink.'" data-deletepage="'.$inlink.'delete">'."\n";
		echo "<thead>\n";
		echo "<tr>\n";
		echo $htmlHelper->tableHead('&#160;', 'elx5_nosorting elx5_center');
		echo $htmlHelper->tableHead($eLang->get('ID'), 'elx5_nosorting elx5_center elx5_lmobhide');
		echo $htmlHelper->tableHead($eLang->get('NAME'), 'elx5_nosorting');
		echo $htmlHelper->tableHead($eLang->get('ACTIVE'), 'elx5_nosorting elx5_center');
		echo $htmlHelper->tableHead($eLang->get('URL_ID'), 'elx5_nosorting elx5_lmobhide');
		echo $htmlHelper->tableHead('URL', 'elx5_nosorting elx5_tabhide');
		echo "</tr>\n";
		echo "</thead>\n";
		echo "<tbody>\n";
		if ($rows) {
			foreach ($rows as $row) {
				if ($row->active) {
					$active = 1;
					$status_class = 'elx5_statuspub';
					$status_title = $eLang->get('ACTIVE');
				} else {
					$active = 0;
					$status_class = 'elx5_statusunpub';
					$status_title = $eLang->get('INACTIVE');
				}

				echo '<tr id="datarow'.$row->id.'">'."\n";
				echo '<td class="elx5_center">';
				echo '<input type="checkbox" name="dataprimary" id="dataprimary'.$row->id.'" class="elx5_datacheck" value="'.$row->id.'" />';
				echo '<label for="dataprimary'.$row->id.'"></label></td>'."\n";
				echo '<td class="elx5_center elx5_lmobhide">'.$row->id."</td>\n";
				if ($row->current) {
					echo '<td id="msdataname'.$row->id.'" data-value="'.addslashes($row->name).'"><span class="elx5_orange">'.$row->name."</span></td>\n";
				} else {
					echo '<td id="msdataname'.$row->id.'" data-value="'.addslashes($row->name).'">'.$row->name."</td>\n";
				}
				if (defined('ELXIS_MULTISITE') && (ELXIS_MULTISITE == 1)) {
					echo '<td id="msdataactive'.$row->id.'" data-value="'.$active.'" class="elx5_center"><a href="javascript:void(null);" onclick="elx5ToggleStatus('.$row->id.', this);" class="elx5_statusicon '.$status_class.'" title="'.$status_title.' - '.$eLang->get('CLICK_TOGGLE_STATUS').'" data-actlink="'.$inlink.'toggle"></a></td>'."\n";
				} else {
					echo '<td id="msdataactive'.$row->id.'" data-value="'.$active.'" class="elx5_center"><a href="javascript:void(null);" class="elx5_statusicon '.$status_class.'" title="'.$status_title.'"></a></td>'."\n";
				}
				echo '<td id="msdatafolder'.$row->id.'" data-value="'.$row->folder.'" class="elx5_lmobhide">'.$row->folder."</td>\n";
				if ($row->active) {
					echo '<td id="msdataurl'.$row->id.'" data-value="'.$row->url.'" class="elx5_tabhide"><a href="'.$row->url.'" target="_blank">'.$row->url."</a></td>\n";
				} else {
					echo '<td id="msdataurl'.$row->id.'" data-value="'.$row->url.'" class="elx5_tabhide">'.$row->url."</td>\n";
				}
				echo "</tr>\n";
			}
		} else {
			echo '<tr id="datarow0" class="elx5_rowwarn"><td class="elx5_center" colspan="6">'.$eLang->get('MULTISITES_DISABLED')."</td></tr>\n";
		}
		echo "</tbody>\n";
		echo "</table>\n";

		echo "</div>\n";//elx5_box_body
		echo "</div>\n";//elx5_box

		echo '<div class="elx5_help elx5_vlspace">&#x02022; '.$eLang->get('MULTISITES_WARN');
		if ($rows) {
			echo '<br />&#x02022; '.$eLang->get('CREATE_REPOSITORY_NOTE');
		}
		if (defined('ELXIS_MULTISITE') && (ELXIS_MULTISITE != 1)) {
			echo '<br />&#x02022; '.sprintf($eLang->get('MAN_MULTISITES_ONLY'), '<strong>1</strong>');
		}
		echo "</div>\n";

		$attrs = array('data-addlng' => $eLang->get('NEW'), 'data-editlng' => $eLang->get('EDIT'));
		echo $htmlHelper->startModalWindow($eLang->get('NEW'), 'msm', '', false, '', '', $attrs);

		$form = new elxis5Form(array('idprefix' => 'ms', 'labelclass' => 'elx5_label', 'sideclass' => 'elx5_labelside'));
		$form->openForm(array('name' => 'fmmsite', 'method' =>'post', 'action' => $inlink.'save', 'id' => 'fmmsite', 'onsubmit' => 'return false;'));

		$form->openFieldset($eLang->get('NEW'));
		$form->addText('name', '', $eLang->get('NAME'), array('required' => 1, 'dir' => 'ltr'));
		$form->addText('folder', '', $eLang->get('URL_ID'), array('required' => 1, 'dir' => 'ltr', 'tip'=> $eLang->get('LOWER_ALPHANUM')));

		$form->addHTML('<div class="elx5_invisible" id="msurlwrap">');
		$form->addInfo('URL', '<span id="msurl">-</span>');
		$form->addHTML('</div>');

		$form->addYesNo('active', $eLang->get('ACTIVE'), 0);
		$form->closeFieldset();

		$form->addHTML('<div class="elx5_invisible" id="mshtaccwrap">');
		$form->openFieldset('.htaccess');
		$txt = '<p>'.$eLang->get('ADD_RULES_HTACCESS').'</p>';
		$txt .= '<pre id="mshtaccrules"></pre>';
		$form->addHTML($txt);
		$form->closeFieldset();
		$form->addHTML('</div>');

		$form->addHTML('<div class="elx5_zero" id="msdatabasewrap">');
		$form->openFieldset($eLang->get('DATABASE'));

		$foptions = array();
		foreach ($dbtypes as $dbtype => $dbtypetxt) {
			$foptions[] = $form->makeOption($dbtype, $dbtypetxt);
		}
		$form->addSelect('db_type', $eLang->get('DB_TYPE'), $elxis->getConfig('DB_TYPE'), $foptions, array('dir' => 'ltr', 'data-defvalue' => $elxis->getConfig('DB_TYPE')));
		$form->addText('db_host', $elxis->getConfig('DB_HOST'), $eLang->get('HOST'), array('dir' => 'ltr', 'data-defvalue' => $elxis->getConfig('DB_HOST')));
		$form->addNumber('db_port', $elxis->getConfig('DB_PORT'), $eLang->get('PORT'), array('dir' => 'ltr', 'class' => 'elx5_text elx5_minitext', 'min' => 0, 'max' => 99999, 'step' => 1, 'data-defvalue' => $elxis->getConfig('DB_PORT')));
		$form->addText('db_name', $elxis->getConfig('DB_NAME'), $eLang->get('DB_NAME'), array('dir' => 'ltr', 'data-defvalue' => $elxis->getConfig('DB_NAME')));
		$form->addText('db_prefix', 'elx'.$newid.'_', $eLang->get('TABLES_PREFIX'), array('required' => 'required', 'class' => 'elx5_text elx5_minitext', 'dir' => 'ltr', 'maxlength' => 10, 'data-defvalue' => 'elx'.$newid.'_'));
		$form->addText('db_user', $elxis->getConfig('DB_USER'), $eLang->get('USERNAME'), array('dir' => 'ltr', 'autocomplete' => 'off', 'data-defvalue' => $elxis->getConfig('USER')));
		$form->addPassword('db_pass', '', $eLang->get('PASSWORD'), array('dir' => 'ltr', 'autocomplete' => 'off'));
		$form->addText('db_dsn', $elxis->getConfig('DSN'), 'DSN', array('dir' => 'ltr', 'data-defvalue' => $elxis->getConfig('DSN')));
		$form->addText('db_scheme', $elxis->getConfig('DB_SCHEME'), $eLang->get('SCHEME'), array('dir' => 'ltr', 'data-defvalue' => $elxis->getConfig('DB_SCHEME')));
		$attrs = array();
		if ($importers) { $attrs['tip'] = implode(', ', $importers); }
		$foptions = array();
		$foptions[] = $form->makeOption(0, $eLang->get('NO'));
		$foptions[] = $form->makeOption(1, $eLang->get('YES'));
		$foptions[] = $form->makeOption(2, $eLang->get('YES').' + '.$eLang->get('CONTENT'));
		$form->addSelect('db_import', $eLang->get('IMPORT_DATA'), 1, $foptions, $attrs);
		$form->closeFieldset();
		$form->addHTML('</div>');//#msdatabasewrap

		$form->addHidden('id', 0);
		$form->addHTML('<div class="elx5_vpad">');
		$form->addButton('save', $eLang->get('SAVE'), 'button', array('class' => 'elx5_btn elx5_sucbtn', 'onclick' => 'elx5CPSaveMultisite();', 'data-waitlng' => $eLang->get('PLEASE_WAIT'), 'data-savelng' => $eLang->get('SAVE')));
		$form->addHTML('</div>');

		$form->closeForm();
		echo $htmlHelper->endModalWindow(false);
	}

}

?>