<?php 
/**
* @version		$Id: single.html.php 2326 2020-01-30 19:58:33Z IOS $
* @package		Elxis
* @subpackage	Component Translator
* @copyright	Copyright (c) 2006-2020 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class singleEtranslatorView extends etranslatorView {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/**************************/
	/* SHOW TRANSLATIONS LIST */
	/**************************/
	public function listTrans($rows, $options, $trcats, $trelements, $elxis, $eLang) {
		$link = $elxis->makeAURL('etranslator:/');
		$inlink = $elxis->makeAURL('etranslator:/', 'inner.php');

		$htmlHelper = $elxis->obj('html');

		elxisLoader::loadFile('includes/libraries/elxis/form5.class.php');

		$parts = array();
		if ($options['category'] != '') { $parts[] = 'category='.$options['category']; }
		if ($options['element'] != '') { $parts[] = 'element='.$options['element']; }
		if ($options['language'] != '') { $parts[] = 'language='.$options['language']; }
		if ($options['elid'] > 0) { $parts[] = 'elid='.$options['elid']; }
		$ordlink = ($parts) ? $link.'?'.implode('&amp;', $parts).'&amp;' : $link.'?';
		$is_filtered = $parts ? true : false;
		unset($parts);

		echo '<h2>'.$eLang->get('TRANS_MANAGEMENT')."</h2>\n";
		if ($elxis->getConfig('MULTILINGUISM') == 0) {
			echo '<div class="elx5_warning">'.$eLang->get('MLCONTENT_DISABLED')."</div>\n";
		}

		echo '<div class="elx5_box elx5_border_blue">'."\n";
		echo '<div class="elx5_box_body">'."\n";
		echo '<div class="elx5_sticky">'."\n";

		echo '<div class="elx5_dataactions">'."\n";
		echo '<a href="javascript:void(null);" onclick="etransEditTranslation(1);" class="elx5_dataaction" data-selector="1" data-activeclass="elx5_datahighlight" title="'.$eLang->get('ADD').'"><i class="fas fa-plus"></i><span class="elx5_lmobhide"> '.$eLang->get('ADD')."</span></a>\n";
		echo '<a href="javascript:void(null);" onclick="etransEditTranslation(0);" class="elx5_dataaction" data-selector="1" title="'.$eLang->get('EDIT').'"><i class="fas fa-edit"></i><span class="elx5_lmobhide"> '.$eLang->get('EDIT')."</span></a>\n";
		echo '<a href="javascript:void(null);" class="elx5_dataaction" title="'.$eLang->get('DELETE').'" onclick="elx5DeleteTableRows(\'translationstbl\', false);" data-selector="1" data-activeclass="elx5_datawarn"><i class="fas fa-trash"></i><span class="elx5_lmobhide"> '.$eLang->get('DELETE')."</span></a>\n";
		if ($is_filtered) {
			echo '<a href="javascript:void(null);" class="elx5_dataaction elx5_dataorange" data-elx5tooltip="'.$eLang->get('FILTERS_HAVE_APPLIED').'" onclick="elx5Toggle(\'etranssearchoptions\');"><i class="fas fa-filter"></i><span class="elx5_lmobhide"> '.$eLang->get('SEARCH_OPTIONS')."</span></a>\n";
		} else {
			echo '<a href="javascript:void(null);" class="elx5_dataaction elx5_dataactive" title="'.$eLang->get('SEARCH_OPTIONS').'" onclick="elx5Toggle(\'etranssearchoptions\');"><i class="fas fa-filter"></i><span class="elx5_lmobhide"> '.$eLang->get('SEARCH_OPTIONS')."</span></a>\n";
		}
		echo "</div>\n";

		echo '<div class="elx5_invisible" id="etranssearchoptions">'."\n";
		echo '<div class="elx5_actionsbox elx5_dspace">';

		$form = new elxis5Form(array('idprefix' => 'trs', 'labelclass' => 'elx5_label', 'sideclass' => 'elx5_labelside'));
		$form->openForm(array('name' => 'fmsrtrans', 'method' =>'get', 'action' => $link, 'id' => 'fmsrtrans'));

		$form->addHTML('<div class="elx5_2colwrap"><div class="elx5_2colbox elx5_spad">');
		$foptions = array();
		$foptions[] = $form->makeOption('', '- '.$eLang->get('ANY').' -');
		if ($trcats) {
			foreach ($trcats as $trcat) { $foptions[] = $form->makeOption($trcat, $trcat); }
		}
		$form->addSelect('category', $eLang->get('CATEGORY'), $options['category'], $foptions);

		$foptions = array();
		$foptions[] = $form->makeOption('', '- '.$eLang->get('ANY').' -');
		if ($trelements) {
			foreach ($trelements as $trelement => $txt) { $foptions[] = $form->makeOption($trelement, $txt); }
		}
		$form->addSelect('element', $eLang->get('ELEMENT'), $options['element'], $foptions);

		$form->addHTML('</div><div class="elx5_2colbox elx5_spad">');

		$form->addLanguage('language', $eLang->get('LANGUAGE'), $options['language'], array(), 1, 5, true, '- '.$eLang->get('SELECT').' -');
		$form->addText('elid', $options['elid'], $eLang->get('ID'), array('dir' => 'ltr'));

		$form->addHTML('</div></div>');

		$form->addHidden('sn', $options['sn']);
		$form->addHidden('so', $options['so']);

		$form->addHTML('<div class="elx5_vpad">');
		$form->addButton('srcbtn', $eLang->get('SEARCH'), 'submit');
		$form->addHTML('</div>');

		$form->closeForm();
		echo "</div>\n";//elx5_actionsbox
		echo "</div>\n";//#etranssearchoptions
		echo "</div>\n";//elx5_sticky

		echo '<table id="translationstbl" class="elx5_datatable" data-deletelng="'.addslashes($eLang->get('AREYOUSURE')).'" data-deletepage="'.$inlink.'single/delete">'."\n";
		echo "<thead>\n";
		echo "<tr>\n";
		echo $htmlHelper->tableHead('&#160;', 'elx5_nosorting elx5_center');
		echo $htmlHelper->sortableTableHead($ordlink, $eLang->get('CATEGORY'), 'category', $options['sn'], $options['so'], 'elx5_lmobhide');
		echo $htmlHelper->sortableTableHead($ordlink, $eLang->get('ELEMENT'), 'element', $options['sn'], $options['so'], 'elx5_lmobhide');
		echo $htmlHelper->sortableTableHead($ordlink, $eLang->get('ID'), 'elid', $options['sn'], $options['so'], 'elx5_center elx5_lmobhide');
		$txt = $eLang->get('ORIGINAL_TEXT').' <img src="'.$elxis->secureBase().'/includes/libraries/elxis/language/flags/'.$elxis->getConfig('LANG').'.png" ';
		$txt .= 'alt="'.$elxis->getConfig('LANG').'" title="'.$elxis->getConfig('LANG').'" />';
		echo $htmlHelper->tableHead($txt, 'elx5_nosorting elx5_lmobhide');
		echo $htmlHelper->sortableTableHead($ordlink, $eLang->get('LANGUAGE'), 'language', $options['sn'], $options['so'], 'elx5_center');
		echo $htmlHelper->sortableTableHead($ordlink, $eLang->get('TRANSLATION'), 'translation', $options['sn'], $options['so']);
		echo "</tr>\n";
		echo "</thead>\n";

		echo "<tbody>\n";
		if ($rows) {
			$flags_dir = $elxis->secureBase().'/includes/libraries/elxis/language/flags/';
			$otextdir = (in_array($elxis->getConfig('LANG'), array('fa', 'he', 'ar'))) ? 'rtl' : 'ltr';
			foreach ($rows as $row) {
				if (in_array($row['element'], array('introtext', 'maintext', 'category_description', 'content', 'locdescription', 'roomdesc', 'hotterms', 'hotdesc', 'rtextradesc', 'rtpaydesc', 'rentterms', 'rtprincdesc'))) {
					$trans_txt = '<span style="color:#888888; font-style:italic;">'.$eLang->get('LONG_TEXT').'</span>';
					$orig_txt = '<span style="color:#888888; font-style:italic;">'.$eLang->get('LONG_TEXT').'</span>';
				} else if ((strpos($row['element'], 'description') === true) || (strpos($row['element'], 'text') === true)) {
					$trans_txt = '<span style="color:#888888 font-style:italic;">'.$eLang->get('LONG_TEXT').'</span>';
					$orig_txt = '<span style="color:#888888; font-style:italic;">'.$eLang->get('LONG_TEXT').'</span>';
				} else {
					$textdir = (in_array($row['translation'], array('fa', 'he', 'ar'))) ? 'rtl' : 'ltr';
					$trans_txt = (eUTF::strlen($row['translation']) > 30) ? eUTF::substr(strip_tags($row['translation']), 0, 27).'...' : $row['translation'];
					$trans_txt = '<span dir="'.$textdir.'">'.$trans_txt.'</span>';
					if ($row['original_text'] == '') {
						$orig_txt = '<span style="color:#888888; font-style:italic;">'.$eLang->get('NOT_AVAILABLE').'</span>';
					} else {
						$orig_txt = (eUTF::strlen($row['original_text']) > 30) ? eUTF::substr(strip_tags($row['original_text']), 0, 27).'...' : $row['original_text'];
						$orig_txt = '<span dir="'.$otextdir.'">'.$orig_txt.'</span>';
					}
				}

				$element_txt = $eLang->silentGet($row['element'], true);

				echo '<tr id="datarow'.$row['trid'].'">'."\n";
				echo '<td class="elx5_center">';
				echo '<input type="checkbox" name="dataprimary" id="dataprimary'.$row['trid'].'" class="elx5_datacheck" value="'.$row['trid'].'" />';
				echo '<label for="dataprimary'.$row['trid'].'"></label></td>'."\n";
				echo '<td class="elx5_lmobhide">'.$row['category'].'</td>'."\n";
				echo '<td class="elx5_lmobhide"><a href="javascript:void(null);" title="'.$eLang->get('EDIT').'" onclick="etransEditTranslation(0, '.$row['trid'].');">'.$element_txt.'</a></td>'."\n";
				echo '<td class="elx5_center elx5_lmobhide">'.$row['elid'].'</td>'."\n";
				echo '<td class="elx5_lmobhide">'.$orig_txt.'</td>'."\n";
				echo '<td class="elx5_center"><img src="'.$flags_dir.$row['language'].'.png" alt="'.$row['language'].'" title="'.$row['language'].'"/></td>'."\n";
				echo '<td><a href="javascript:void(null);" title="'.$eLang->get('EDIT').'" onclick="etransEditTranslation(0, '.$row['trid'].');">'.$trans_txt.'</a></td>'."\n";
				echo "</tr>\n";
			}
		} else {
			echo '<tr id="datarow0" class="elx5_rowwarn"><td class="elx5_center" colspan="7">'.$eLang->get('NO_RESULTS')."</td></tr>\n";
		}
		echo "</tbody>\n";
		echo "</table>\n";

		echo "</div>\n";//elx5_box_body

		if ($rows) {
			$linkbase = $ordlink.'sn='.$options['sn'].'&amp;so='.$options['so'];
			echo $htmlHelper->tableSummary($linkbase, $options['page'], $options['maxpage'], $options['total']);
		}

		echo "</div>\n";//elx5_box

		$attrs = array('data-addlng' => $eLang->get('ADD_TRANSLATION'), 'data-editlng' => $eLang->get('EDIT_TRANSLATION'));
		echo $htmlHelper->startModalWindow($eLang->get('ADD_TRANSLATION'), '1', '', true, '', '', $attrs);

		$form = new elxis5Form(array('idprefix' => 'etrans_', 'labelclass' => 'elx5_labelblock', 'sideclass' => 'elx5_zero'));

		$form->openForm(array('name' => 'fmedtrans', 'method' =>'post', 'action' => $inlink.'single/gettransdata', 'id' => 'fmedtrans', 'onsubmit' => 'return false;'));
		$form->openFieldset();
		$label = '<img src="'.$elxis->secureBase().'/includes/libraries/elxis/language/flags/'.$elxis->getConfig('LANG').'.png" alt="'.$elxis->getConfig('LANG').'" title="'.$elxis->getConfig('LANG').'" />'."\n";
		$label .= ' '.$eLang->get('ORIGINAL_TEXT');
		$orig_txt = '<div class="elx5_zero" id="etrans_original" dir="'.$eLang->getinfo('DIR').'">-</div>';
		$form->addInfo($label, $orig_txt);

		$form->setOption('labelclass', 'elx5_label');
		$form->setOption('sideclass', 'elx5_labelside');
		$form->addLanguage('language', $eLang->get('LANGUAGE'), '', $attrs=array(), 2, 5, true);

		$form->setOption('labelclass', 'elx5_labelblock');
		$form->setOption('sideclass', 'elx5_zero');

		$form->addHTML('<div class="elx5_invisible" id="etrans_transwrap">');
		$form->addText('translation', '', $eLang->get('TRANSLATION'), array('maxlength' => 255, 'dir' => 'ltr'));
		$form->addHTML('</div>');

		$form->addHTML('<div class="elx5_invisible" id="etrans_texttranswrap">');
		$form->addTextarea('translationtext', '', $eLang->get('TRANSLATION'));
		$form->addHTML('</div>');

		$form->addHTML('<div class="elx5_spad elx5_center"><a href="javascript:void(null);" title="'.$eLang->get('SWITCH_WRITE_DIR').'" onclick="etransSwitchDir()">LTR/RTL</a></div>');

		$form->addHidden('trid', '0');
		$form->addHidden('category', '');
		$form->addHidden('element', '');
		$form->addHidden('elid', '0');
		$form->addHidden('longtext', '0');
		$form->addHidden('saveurl', $inlink.'api/savetrans');

		$form->addHTML('<div class="elx5_vpad">');
		$form->addButton('save', $eLang->get('SAVE'), 'button', array('class' => 'elx5_btn elx5_sucbtn', 'onclick' => 'etransSaveTranslation();', 'data-waitlng' => $eLang->get('PLEASE_WAIT'), 'data-savelng' => $eLang->get('SAVE')));
		$form->addHTML('</div>');

		$form->closeFieldset();
		$form->closeForm();
		echo $htmlHelper->endModalWindow(true);
	}


	/*****************************************/
	/* ADD/EDIT ALL ITEM'S TRANSLATIONS HTML */
	/*****************************************/
	public function editAllTranslations($options, $original, $translations) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();

		$inlink = $elxis->makeAURL('etranslator:/', 'inner.php');

		elxisLoader::loadFile('includes/libraries/elxis/form5.class.php');

		$form = new elxis5Form(array('idprefix' => 'tra_', 'labelclass' => 'elx5_label', 'sideclass' => 'elx5_labelside'));
		$form->addHTML('<div class="elx5_pad">');
		$form->openForm(array('name' => 'fmalltrans', 'method' =>'post', 'action' => $inlink.'api/saveall', 'id' => 'fmalltrans'));
		$form->openFieldset($eLang->get('TRANS_MANAGEMENT'));
		$trdata = array('category' => $options['category'], 'element' => $options['element'], 'elid' => $options['id']);
		$form->addMLText('translation', $trdata, $original, $eLang->get('TRANSLATION'), array('required' => 'required', 'maxlength' => 255));

		$form->addHidden('category', $options['category']);
		$form->addHidden('element', $options['element']);
		$form->addHidden('elid', $options['id']);

		$form->addHTML('<div class="elx5_vpad">');
		$form->addButton('save', $eLang->get('SAVE'), 'submit', array('class' => 'elx5_btn elx5_sucbtn'));
		$form->addHTML('</div>');
		$form->closeFieldset();
		$form->closeForm();
		$form->addHTML('</div>');

/*
TODO: DELETE THESE: 
		$deflang = $elxis->getConfig('LANG');
		$site_langs = $eLang->getSiteLangs(true);
		$save_icon = $elxis->icon('save', 16);
		$delete_icon = $elxis->icon('delete', 16);

		echo '<form name="fmedalltrans" action="" onsubmit="return false;">'."\n";
		echo '<div style="margin:0; padding:10px;">'."\n";
		echo '<div class="elx_tbl_wrapper">'."\n";
		echo '<table cellspacing="0" cellpadding="2" border="1" width="100%" dir="'.$eLang->getinfo('DIR').'" class="elx_tbl_list">'."\n";
		echo '<tr><th colspan="2" class="elx_th_subcenter">'.$eLang->get('LANGUAGE').'</th><th class="elx_th_sub">'.$eLang->get('TRANSLATION').'</th><th class="elx_th_sub"></th></tr>'."\n";
		echo '<tr class="elx_trx"><td>'.$site_langs[$deflang]['NAME'].'</td><td>'.$site_langs[$deflang]['NAME_ENG'].'</td>';
		echo '<td><input type="text" class="inputbox mlflag'.$deflang.'" dir="'.$site_langs[$deflang]['DIR'].'" maxlength="255" size="50" disabled="disabled" value="'.$original.'" /></td>';
		echo '<td></td></tr>'."\n";
		$k = 0;
		foreach ($site_langs as $lng => $info) {
			if ($lng == $deflang) { continue; }
			$text = '';
			$trid = 0;
			if ($translations) {
				foreach ($translations as $tran) {
					if ($tran['language'] == $lng) {
						$trid = $tran['trid'];
						$text = $tran['translation'];
						break;
					}
				}
			}
			echo '<tr class="elx_tr'.$k.'"><td>'.$site_langs[$lng]['NAME'].'</td><td>'.$site_langs[$lng]['NAME_ENG'].'</td>';
			echo '<td><input type="text" name="'.$lng.'_translation" id="'.$lng.'_translation" class="inputbox mlflag'.$lng.'" dir="'.$site_langs[$lng]['DIR'].'" maxlength="255" size="50" value="'.$text.'" onchange="trans_marksiunsaved(this);" />';
			echo '<input type="hidden" name="'.$lng.'_trid" id="'.$lng.'_trid" dir="ltr" value="'.$trid.'" />';
			echo '</td>';
			echo '<td class="elx_td_center"><a href="javascript:void(null);" onclick="etrans_savetrans(\''.$lng.'\');" title="'.$eLang->get('SAVE').'"><img src="'.$save_icon.'" alt="save" /></a> &#160; ';
			echo '<a href="javascript:void(null);" onclick="etrans_deletetrans(\''.$lng.'\');" title="'.$eLang->get('DELETE').'"><img src="'.$delete_icon.'" alt="delete" /></a></td></tr>'."\n";
			$k = 1 - $k;
		}
		echo "</table>\n";
		echo "</div>\n";
		echo "</div>\n";
		echo '<input type="hidden" name="base" id="edall_base" dir="ltr" value="'.$elxis->makeAURL('etranslator:/', 'inner.php').'" />'."\n";
		echo '<input type="hidden" name="category" id="edall_category" dir="ltr" value="'.$options['category'].'" />'."\n";
		echo '<input type="hidden" name="element" id="edall_element" dir="ltr" value="'.$options['element'].'" />'."\n";
		echo '<input type="hidden" name="id" id="edall_id" dir="ltr" value="'.$options['id'].'" />'."\n";
		echo "</form>\n";
*/
	}

}

?>