<?php 
/**
* @version		$Id: menuitem.html.php 2326 2020-01-30 19:58:33Z IOS $
* @package		Elxis
* @subpackage	Component eMenu
* @copyright	Copyright (c) 2006-2020 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class menuitemEmenuView extends emenuView {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/************************/
	/* SHOW MENU ITEMS LIST */
	/************************/
	public function listmenuitems($rows, $options, $collections, $allgroups, $eLang, $elxis) {
		$link = $elxis->makeAURL('emenu:/');
		$inlink = $elxis->makeAURL('emenu:/', 'inner.php');

		$htmlHelper = $elxis->obj('html');

		echo '<h2>'.$eLang->get('COLLECTION').' <span>'.$options['collection']."</span></h2>\n";

		echo '<div class="elx5_box elx5_border_blue">'."\n";
		echo '<div class="elx5_box_body">'."\n";
		echo '<div class="elx5_sticky">'."\n";
		echo '<div class="elx5_dataactions">'."\n";
		if ($elxis->acl()->check('com_emenu', 'menu', 'add') > 0) {
			echo '<a href="javascript:void(null);" onclick="elx5ModalOpen(\'emenadd\');" class="elx5_dataaction elx5_datahighlight" data-alwaysactive="1" title="'.$eLang->get('ADD').'"><i class="fas fa-plus"></i><span class="elx5_lmobhide"> '.$eLang->get('ADD')."</span></a>\n";
		}
		if ($elxis->acl()->check('com_emenu', 'menu', 'edit') > 0) {
			if (count($collections) > 1) {
				echo '<a href="javascript:void(null);" onclick="elx5ModalOpen(\'emencp\');" class="elx5_dataaction" title="'.$eLang->get('COPY').'" data-selector="1"><i class="fas fa-copy"></i><span class="elx5_tabhide"> '.$eLang->get('COPY')."</span></a>\n";
				echo '<a href="javascript:void(null);" onclick="elx5ModalOpen(\'emenmo\');" class="elx5_dataaction" title="'.$eLang->get('MOVE').'" data-selector="1"><i class="fas fa-share"></i><span class="elx5_tabhide"> '.$eLang->get('MOVE')."</span></a>\n";
			}
			echo '<a href="javascript:void(null);" onclick="emenuMenuItemTrans(\'menuitemstbl\');" class="elx5_dataaction" data-selector="1" title="'.$eLang->get('TRANSLATIONS').'"><i class="fas fa-globe"></i></a>'."\n";
			echo '<a href="javascript:void(null);" onclick="emenuMoveItem(0);" class="elx5_dataaction" title="'.$eLang->get('MOVE_DOWN').'" data-selector="1"><i class="fas fa-arrow-down"></i></a>'."\n";
			echo '<a href="javascript:void(null);" onclick="emenuMoveItem(1);" class="elx5_dataaction" title="'.$eLang->get('MOVE_UP').'" data-selector="1"><i class="fas fa-arrow-up"></i></a>'."\n";
		}
		if ($elxis->acl()->check('com_emenu', 'menu', 'delete') > 0) {
			echo '<a href="javascript:void(null);" class="elx5_dataaction" title="'.$eLang->get('DELETE').'" onclick="elx5DeleteTableRows(\'menuitemstbl\', false);" data-selector="1" data-activeclass="elx5_datawarn"><i class="fas fa-trash"></i><span class="elx5_tabhide"> '.$eLang->get('DELETE')."</span></a>\n";
		}
		echo "</div>\n";
		echo "</div>\n";//elx5_sticky

		echo '<table id="menuitemstbl" class="elx5_datatable" data-deletelng="'.addslashes($eLang->get('WARN_DELETE_MENUITEM')).'" data-deletepage="'.$inlink.'mitems/delete" data-inpage="'.$inlink.'">'."\n";
		echo "<thead>\n";
		echo "<tr>\n";
		echo $htmlHelper->tableHead('&#160;', 'elx5_nosorting elx5_center');
		echo $htmlHelper->tableHead($eLang->get('TITLE'), 'elx5_nosorting');
		echo $htmlHelper->tableHead($eLang->get('PUBLISHED'), 'elx5_nosorting elx5_center elx5_mobhide');
		echo $htmlHelper->tableHead($eLang->get('TYPE'), 'elx5_nosorting elx5_lmobhide');
		echo $htmlHelper->tableHead($eLang->get('EXPAND'), 'elx5_nosorting elx5_center elx5_tabhide');
		echo $htmlHelper->tableHead($eLang->get('ACCESS'), 'elx5_nosorting elx5_tabhide');
		echo $htmlHelper->tableHead($eLang->get('ID'), 'elx5_nosorting elx5_center elx5_lmobhide');
		echo $htmlHelper->tableHead('SSL', 'elx5_nosorting elx5_center elx5_lmobhide');
		echo "</tr>\n";
		echo "</thead>\n";

		echo "<tbody>\n";
		if ($rows) {
			$canedit = ($elxis->acl()->check('com_emenu', 'menu', 'edit') > 0) ? true : false;

			foreach ($rows as $k => $row) {
				if ($row->published == 1) {
					$status_class = 'elx5_statuspub';
					$status_title = $eLang->get('PUBLISHED');
				} else {
					$status_class = 'elx5_statusunpub';
					$status_title = $eLang->get('UNPUBLISHED');
				}

				switch ($row->expand) {
					case 2: $lvl = $eLang->get('FULL'); break;
					case 1: $lvl = $eLang->get('LIMITED'); break;
					case 0: default: $lvl = $eLang->get('NO'); break;
				}

				$acctxt = $elxis->alevelToGroup($row->alevel, $allgroups);

				switch ($row->menu_type) {
					case 'link': $typetxt = '<span title="'.$row->link.'">'.$eLang->get('LINK').'</span>'; break;
					case 'separator': $typetxt = '<span title="'.$row->link.'">'.$eLang->get('SEPARATOR').'</span>'; break;
					case 'wrapper': $typetxt = '<span title="'.$row->link.'">'.$eLang->get('WRAPPER').'</span>'; break;
					case 'onclick': $typetxt = '<span title="'.addslashes($row->link).'">OnClick</span>'; break;
					default: $typetxt = '<span title="'.$row->link.'">'.$row->menu_type.'</span>'; break;
				}
				$secureclass = ($row->secure == 1) ? 'fas fa-lock' : 'fas fa-lock-open';

				echo '<tr id="datarow'.$row->menu_id.'">'."\n";
				echo '<td class="elx5_center">';
				echo '<input type="checkbox" name="dataprimary" id="dataprimary'.$row->menu_id.'" class="elx5_datacheck" value="'.$row->menu_id.'" />';
				echo '<label for="dataprimary'.$row->menu_id.'"></label></td>'."\n";
				if ($canedit) {
					echo '<td><a href="'.$link.'mitems/edit.html?menu_id='.$row->menu_id.'" title="'.$eLang->get('EDIT').'">'.$row->treename."</a></td>\n";
					echo '<td class="elx5_center elx5_mobhide"><a href="javascript:void(null);" onclick="elx5ToggleStatus('.$row->menu_id.', this);" class="elx5_statusicon '.$status_class.'" title="'.$status_title.' - '.$eLang->get('CLICK_TOGGLE_STATUS').'" data-actlink="'.$link.'mitems/togglestatus"></a></td>'."\n";
				} else {
					echo '<td>'.$row->treename."</td>\n";
					echo '<td class="elx5_center elx5_mobhide"><a href="javascript:void(null);" class="elx5_statusicon '.$status_class.'" title="'.$status_title.'"></a></td>'."\n";
				}

				echo '<td class="elx5_lmobhide">'.$typetxt.'</td>'."\n";
				echo '<td class="elx5_center elx5_tabhide">'.$lvl.'</td>'."\n";
				echo '<td class="elx5_tabhide">'.$acctxt.'</td>'."\n";
				echo '<td class="elx5_center elx5_lmobhide">'.$row->menu_id.'</td>'."\n";
				echo '<td class="elx5_center elx5_lmobhide"><i class="'.$secureclass.'"></i></td>'."\n";
				echo "</tr>\n";
			}
		} else {
			echo '<tr id="datarow0" class="elx5_rowwarn"><td class="elx5_center" colspan="8">'.$eLang->get('NO_RESULTS')."</td></tr>\n";
		}
		echo "</tbody>\n";
		echo "</table>\n";

		echo "</div>\n";//elx5_box_body

		if ($rows) {
			echo $htmlHelper->tableSummary($link, $options['page'], $options['maxpage'], $options['total']);
		}

		echo "</div>\n";//elx5_box
		echo '<div id="emenutranslations" class="elx5_invisible">'.$elxis->makeAURL('etranslator:single/editall.html', 'inner.php').'?category=com_emenu&element=title&tbl=menu&col=title&idcol=menu_id</div>'."\n";

		echo $htmlHelper->startModalWindow($eLang->get('SEL_MENUITEM_TYPE'), 'emenadd');
		echo '<div class="emenu5_typebox">'."\n";
		echo '<h4><a href="'. $link.'mitems/add.html?collection='.$options['collection'].'&amp;type=link">'.$eLang->get('LINK')."</a></h4>\n";
		echo '<div class="elx5_tip">'.$eLang->get('LINK_LINK_DESC')."</div>\n";
		echo "</div>\n";
		echo '<div class="emenu5_typebox">'."\n";
		echo '<h4><a href="'. $link.'mitems/add.html?collection='.$options['collection'].'&amp;type=url">URL'."</a></h4>\n";
		echo '<div class="elx5_tip">'.$eLang->get('LINK_URL_DESC')."</div>\n";
		echo "</div>\n";
		echo '<div class="emenu5_typebox">'."\n";
		echo '<h4><a href="'. $link.'mitems/add.html?collection='.$options['collection'].'&amp;type=onclick">OnClick</a></h4>'."\n";
		echo '<div class="elx5_tip">'.$eLang->get('ONCLICK_DESC')."</div>\n";
		echo "</div>\n";
		echo '<div class="emenu5_typebox">'."\n";
		echo '<h4><a href="'. $link.'mitems/add.html?collection='.$options['collection'].'&amp;type=separator">'.$eLang->get('SEPARATOR')."</a></h4>\n";
		echo '<div class="elx5_tip">'.$eLang->get('LINK_SEPARATOR_DESC')."</div>\n";
		echo "</div>\n";
		echo '<div class="emenu5_typebox">'."\n";
		echo '<h4><a href="'. $link.'mitems/add.html?collection='.$options['collection'].'&amp;type=wrapper">'.$eLang->get('WRAPPER')."</a></h4>\n";
		echo '<div class="elx5_tip">'.$eLang->get('LINK_WRAPPER_DESC')."</div>\n";
		echo "</div>\n";
		echo $htmlHelper->endModalWindow();

		echo $htmlHelper->startModalWindow($eLang->get('COPY'), 'emencp');
		echo '<form name="fmcopymenu" id="fmcopymenu" method="post" action="'.$inlink.'mitems/copy" class="elx5_form">'."\n";
		echo '<div class="elx5_formrow">'."\n";
		echo '<label class="elx5_label" for="emencpcollection">'.$eLang->get('COLLECTION')."</label>\n";
		echo '<div class="elx5_labelside">'."\n";
		echo '<select name="collection" id="emencpcollection" class="elx5_select">'."\n";
		echo '<option value="" selected="selected">- '.$eLang->get('SELECT')." -</option>\n";
		foreach ($collections as $mcol) {
			if ($mcol == $options['collection']) { continue; }
			echo '<option value="'.$mcol.'">'.$mcol."</option>\n";
		}
		echo "</select>\n";
		echo "</div>\n</div>\n";
		echo '<input type="hidden" name="menu_id" id="emencpmenu_id" value="0" />'."\n";
		echo '<div class="elx5_vpad">'."\n";
		echo '<button type="button" class="elx5_btn elx5_sucbtn" id="emencpsave" name="save" onclick="emenuCopyMoveItem(\'copy\');">'.$eLang->get('COPY')."</button> \n";
		echo "</div>\n";
		echo "</form>\n";
		echo $htmlHelper->endModalWindow();

		echo $htmlHelper->startModalWindow($eLang->get('MOVE'), 'emenmo');
		echo '<form name="fmmovemenu" id="fmmovemenu" method="post" action="'.$inlink.'mitems/move" class="elx5_form">'."\n";
		echo '<div class="elx5_formrow">'."\n";
		echo '<label class="elx5_label" for="emenmocollection">'.$eLang->get('COLLECTION')."</label>\n";
		echo '<div class="elx5_labelside">'."\n";
		echo '<select name="collection" id="emenmocollection" class="elx5_select">'."\n";
		echo '<option value="" selected="selected">- '.$eLang->get('SELECT')." -</option>\n";
		foreach ($collections as $mcol) {
			if ($mcol == $options['collection']) { continue; }
			echo '<option value="'.$mcol.'">'.$mcol."</option>\n";
		}
		echo "</select>\n";
		echo "</div>\n</div>\n";
		echo '<input type="hidden" name="menu_id" id="emenmomenu_id" value="0" />'."\n";
		echo '<div class="elx5_vpad">'."\n";
		echo '<button type="button" class="elx5_btn elx5_sucbtn" id="emenmosave" name="save" onclick="emenuCopyMoveItem(\'move\');">'.$eLang->get('MOVE')."</button> \n";
		echo "</div>\n";
		echo "</form>\n";
		echo $htmlHelper->endModalWindow();
	}


	/**********************/
	/* ADD/EDIT MENU ITEM */
	/**********************/
	public function editMenuItem($row, $treeitems, $components=null, $leveltip, $component='', $is_new=false) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();

		$clang = $elxis->getConfig('LANG');
		$cinfo = $eLang->getallinfo($clang);

		$action = $elxis->makeAURL('emenu:mitems/save.html', 'inner.php');

		switch ($row->menu_type) {
			case 'link':
				if ($row->menu_id) {
					$typetxt = $this->linkTitle($component, $row->link);
				} else {
					$typetxt = $eLang->get('LINK');
				}
				$typetxt2 = $eLang->get('LINK');
			break;
			case 'url': $typetxt = 'URL'; $typetxt2 = 'URL'; break;
			case 'onclick': $typetxt = 'OnClick'; $typetxt2 = 'OnClick'; break;
			case 'wrapper': $typetxt = $eLang->get('WRAPPER'); $typetxt2 = $eLang->get('WRAPPER'); break;
			case 'separator': $typetxt = $eLang->get('SEPARATOR'); $typetxt2 = $eLang->get('SEPARATOR'); break;
			default: $typetxt = $row->menu_type; $typetxt2 = $row->menu_type; break;
		}

		if ($is_new) {
			echo '<h2>'.$typetxt2.' <span>'.$eLang->get('NEW')."</span></h2>\n";
		} else {
			echo '<h2>'.$typetxt2.' <span>'.$row->title."</span></h2>\n";
		}

		echo '<div class="elx5_2colwrap">'."\n";
		echo '<div class="elx5_2colbox">'."\n";

		elxisLoader::loadFile('includes/libraries/elxis/form5.class.php');
		$form = new elxis5Form(array('idprefix' => 'epr'));
		$form->openForm(array('name' => 'fmrtedit', 'method' =>'post', 'action' => $action, 'id' => 'fmrtedit'));
		$form->openFieldset($eLang->get('BASIC_SETTINGS'));

		$form->addInfo($eLang->get('TYPE'), $typetxt);

		$trdata = array('category' => 'com_emenu', 'element' => 'title', 'elid' => intval($row->menu_id));
		$form->addMLText('title', $trdata, $row->title, $eLang->get('TITLE'), array('required' => 'required', 'maxlength' => 255));

		if (($row->menu_type == 'url') || ($row->menu_type == 'wrapper')) {
			$form->addUrl('link', $row->link, $eLang->get('LINK'), array('required' => 'required', 'maxlength' => 255));
		} else if ($row->menu_type == 'onclick') {
			$form->addText('link', $row->link, $eLang->get('JSCODE'), array('required' => 'required', 'maxlength' => 255, 'tip' => $eLang->get('JSCODE_DESC')));
		} else {
			$attrs = array('dir' => 'ltr', 'maxlength' => 160);
			if ($row->menu_type == 'separator') {
				$label = $eLang->get('LINK');
			} else {
				$attrs['required'] = 'required';
				$label = $eLang->get('ELXIS_LINK');
			}
			$form->addText('link', $row->link, $label, $attrs);
		}

		$options = array();
		$options[] = $form->makeOption(0, $eLang->get('NO'));
		if ($treeitems) {
			foreach ($treeitems as $treeitem) {
				$disabled = 0;
				if ($row->menu_id) {
					if ($row->menu_id == $treeitem->menu_id) { $disabled = 1; }
				}
				$options[] = $form->makeOption($treeitem->menu_id, $treeitem->treename, array(), $disabled);
			}
		}
		$form->addSelect('parent_id', $eLang->get('PARENT_ITEM'), $row->parent_id, $options, array('tip' => $eLang->get('PARENT_ITEM_DESC')));

		$options = array();
		$options[] = $form->makeOption(0, '- '.$eLang->get('FIRST'));
		$q = 1;
		if ($row->menu_id) {
			if ($treeitems) {
				foreach ($treeitems as $item) {
					if ($item->parent_id == $row->parent_id) {
						$options[] = $form->makeOption($q, $q.' - '.$item->title);
						$q++;
					}
				}
			}
		}
		$q = ($q > 1) ? $q : 999;
		$options[] = $form->makeOption($q, '- '.$eLang->get('LAST'));
		$form->addSelect('ordering', $eLang->get('ORDERING'), $row->ordering, $options);

		$form->addAccesslevel('alevel', $eLang->get('ACCESS_LEVEL'), $row->alevel, $elxis->acl()->getLevel(), array('dir' => 'ltr', 'tip' => $leveltip));

		$form->addYesNo('published', $eLang->get('PUBLISHED'), $row->published);
		$form->closeFieldset();

		if ($row->menu_type != 'separator') {
			$form->openFieldset($eLang->get('OTHER_OPTIONS'));

			$options = array();
			$options[] = $form->makeOption('felxis-logo', 'felxis-logo');
			$options[] = $form->makeOption('fas fa-address-card', 'fas fa-address-card');
			$options[] = $form->makeOption('fas fa-at', 'fas fa-at');
			$options[] = $form->makeOption('fas fa-calendar-alt', 'fas fa-calendar-alt');
			$options[] = $form->makeOption('fas fa-car', 'fas fa-car');
			$options[] = $form->makeOption('fas fa-caret-left', 'fas fa-caret-left');
			$options[] = $form->makeOption('fas fa-caret-right', 'fas fa-caret-right');
			$options[] = $form->makeOption('fas fa-chart-line', 'fas fa-chart-line');
			$options[] = $form->makeOption('fas fa-chart-pie', 'fas fa-chart-pie');
			$options[] = $form->makeOption('fas fa-comment', 'fas fa-comment');
			$options[] = $form->makeOption('fas fa-database', 'fas fa-database');
			$options[] = $form->makeOption('fas fa-desktop', 'fas fa-desktop');
			$options[] = $form->makeOption('fas fa-download', 'fas fa-download');
			$options[] = $form->makeOption('fas fa-envelope', 'fas fa-envelope');
			$options[] = $form->makeOption('fas fa-film', 'fas fa-film');
			$options[] = $form->makeOption('fas fa-folder-open', 'fas fa-folder-open');
			$options[] = $form->makeOption('fas fa-globe', 'fas fa-globe');
			$options[] = $form->makeOption('fas fa-home', 'fas fa-home');
			$options[] = $form->makeOption('fas fa-image', 'fas fa-image');
			$options[] = $form->makeOption('fas fa-info', 'fas fa-info');
			$options[] = $form->makeOption('fas fa-map-marker-alt', 'fas fa-map-marker-alt');
			$options[] = $form->makeOption('fas fa-microphone-alt', 'fas fa-microphone-alt');
			$options[] = $form->makeOption('fas fa-motorcycle', 'fas fa-motorcycle');
			$options[] = $form->makeOption('fas fa-music', 'fas fa-music');
			$options[] = $form->makeOption('fas fa-pen', 'fas fa-pen');
			$options[] = $form->makeOption('fas fa-phone', 'fas fa-phone');
			$options[] = $form->makeOption('fas fa-sign-in-alt', 'fas fa-sign-in-alt');
			$options[] = $form->makeOption('fas fa-shopping-cart', 'fas fa-shopping-cart');
			$options[] = $form->makeOption('fas fa-star', 'fas fa-star');
			$options[] = $form->makeOption('fas fa-tag', 'fas fa-tag');
			$options[] = $form->makeOption('fas fa-user', 'fas fa-user');
			$options[] = $form->makeOption('fas fa-volume-up', 'fas fa-volume-up');
			$form->addSelectAddOther('iconfont', $eLang->get('ICON_FONT'), $row->iconfont, $options, array('dir' => 'ltr', 'tip' => $eLang->get('ICON_FONT_DESC')));
			unset($options);

			if ($row->menu_type == 'link') {
				$options = array(
					array('name' => $eLang->get('NO'), 'value' => 0, 'color' => 'red'),
					array('name' => $eLang->get('LIMITED'), 'value' => 1, 'color' => 'yellow'),
					array('name' => $eLang->get('FULL'), 'value' => 2, 'color' => 'green')
				);
				$form->addItemStatus('expand', $eLang->get('EXPAND'), $row->expand, $options, array('tip' => $eLang->get('EXPAND_DESC')));
			}

			if (($row->menu_type == 'link') || ($row->menu_type == 'wrapper')) {
				$options = array();
				$options[] = $form->makeOption('index.php', $eLang->get('FULL_PAGE'));
				$options[] = $form->makeOption('inner.php', $eLang->get('ONLY_COMPONENT'));
				$form->addSelect('file', $eLang->get('ELXIS_INTERFACE'), $row->file, $options, array('tip' => $eLang->get('ELXIS_INTERFACE_DESC')));
			}
			if ($row->menu_type != 'onclick') {
				$options = array(
					array('name' => $eLang->get('NO'), 'value' => 0, 'color' => 'red'),
					array('name' => $eLang->get('TYPICAL_POPUP'), 'value' => 1, 'color' => 'yellow'),
					array('name' => $eLang->get('LIGHTBOX_WINDOW'), 'value' => 2, 'color' => 'green')
				);
				$form->addItemStatus('popup', $eLang->get('POPUP_WINDOW'), $row->popup, $options);

				$form->addNumber('width', $row->width, $eLang->get('WIDTH'), array('min' => '0', 'max' => 9999, 'step' => 1, 'tip' => $eLang->get('POPUP_WIDTH_DESC'), 'class' => 'elx5_text elx5_mediumtext'));

				$form->addNumber('height', $row->height, $eLang->get('HEIGHT'), array('min' => '0', 'max' => 9999, 'step' => 1, 'tip' => $eLang->get('POPUP_HEIGHT_DESC'), 'class' => 'elx5_text elx5_mediumtext'));
				$options = array();
				$options[] = $form->makeOption('', $eLang->get('NONE'));
				$options[] = $form->makeOption('_self', $eLang->get('SELF_WINDOW'));
				$options[] = $form->makeOption('_blank', $eLang->get('NEW_WINDOW'));
				$options[] = $form->makeOption('_parent', $eLang->get('PARENT_WINDOW'));
				$options[] = $form->makeOption('_top', $eLang->get('TOP_WINDOW'));
				$form->addSelect('target', $eLang->get('LINK_TARGET'), $row->target, $options);
			}

			if (($row->menu_type == 'link') || ($row->menu_type == 'wrapper')) {
				$form->addYesNo('secure', $eLang->get('SECURE_CONNECT'), $row->secure, array('tip' => $eLang->get('SECURE_CONNECT_DESC')));
			}
			$form->closeFieldset();
		} //not separator

		$form->addHidden('section', $row->section);
		$form->addHidden('collection', $row->collection);
		$form->addHidden('menu_type', $row->menu_type);
		$form->addHidden('menu_id', $row->menu_id);
		if (($row->menu_type != 'link') && ($row->menu_type != 'wrapper')) {
			$form->addHidden('file', '');
		}
		if ($row->menu_type != 'link') { $form->addHidden('expand', 0); }
		if ($row->menu_type == 'separator') {
			$form->addHidden('popup', 0);
			$form->addHidden('width', 0);
			$form->addHidden('height', 0);
			$form->addHidden('target', '');
			$form->addHidden('iconfont', '');
		}
		if ($row->menu_type == 'onclick') {
			$form->addHidden('popup', 0);
			$form->addHidden('width', 0);
			$form->addHidden('height', 0);
			$form->addHidden('target', '');
		}
		if (($row->menu_type != 'link') && ($row->menu_type != 'wrapper')) { $form->addHidden('secure', 0); }

		$form->addToken('menuitem');
		$form->addHidden('task', '');

		$form->closeForm();

		echo '</div>'."\n";//elx5_2colbox
		echo '<div class="elx5_2colbox">'."\n";

		$this->initMenuHelper($row->menu_type, $components, $component, $eLang, $elxis);

		echo '</div>'."\n";//elx5_2colbox
		echo '</div>'."\n";//elx5_2colwrap
	}


	/************************************/
	/* INITIALIZE MENU GENERATOR/HELPER */
	/************************************/
	private function initMenuHelper($menu_type, $components, $component, $eLang, $elxis) {
		if ($menu_type == 'link') {
			$title = $eLang->get('LINK_GENERATOR');
		} else if ($menu_type == 'url') {
			$title = 'URL';
		} else if ($menu_type == 'wrapper') {
			$title = $eLang->get('WRAPPER');
		} else if ($menu_type == 'separator') {
			$title = $eLang->get('SEPARATOR');
		} else if ($menu_type == 'onclick') {
			$title = 'OnClick';
		} else {
			$title = '';
		}

		echo '<div class="elx5_box elx5_border_green">'."\n";
		echo '<div class="elx5_box_body">'."\n";
		if ($title != '') {
			echo '<div class="elx5_dataactions elx5_spad">'."\n";
			echo '<h3 class="iosrt_box_title">'.$title."</h3>\n";
			echo "</div>\n";
		}

		if ($menu_type == 'link') {
			$datalink = $elxis->makeAURl('emenu:mitems/generator', 'inner.php');

			echo '<div class="elx5_formrow">'."\n";
			echo '<label class="elx5_label" for="emenpickcomponent">'.$eLang->get('SEL_COMPONENT')."</label>\n";
			echo '<div class="elx5_labelside">';
			echo '<select name="pickcomponent" id="emenpickcomponent" onchange="emenuPickComponent();" class="elx5_select" data-loadlng="'.$eLang->get('LOADING').'" data-datalink="'.$datalink.'">'."\n";
			if ($component == '') {
				echo '<option value="" selected="selected">- '.$eLang->get('SELECT')." -</option>\n";
			}
			if ($components) {
				foreach ($components as $key => $val) {
					$sel = ($key == 'com_'.$component) ? ' selected="selected"' : '';
					echo '<option value="'.$key.'"'.$sel.'>'.$val.'</option>'."\n";
				}
			}
			echo "</select>\n";
			echo "</div>\n</div>\n";
			echo '<div id="emenu_generator" class="emenu5_generatorwrap"></div>'."\n";
		} else if ($menu_type == 'url') {
			echo '<div class="elx5_info">'.$eLang->get('URL_HELPER')."</div>\n";
		} else if ($menu_type == 'wrapper') {
			echo '<div class="elx5_info">'.$eLang->get('WRAPPER_HELPER')."</div>\n";
			echo '<div class="elx5_help">'.$eLang->get('TIP_INTERFACE')."</div>\n";
		} else if ($menu_type == 'separator') {
			echo '<div class="elx5_info">'.$eLang->get('SEPARATOR_HELPER')."</div>\n";
		} else if ($menu_type == 'onclick') {
			echo '<div class="elx5_sminfo elx5_dspace">'.$eLang->get('ONCLICK_DESC')."</div>\n";
			echo '<h4>Example 1</h4>';
			echo '<div class="elx5_info">somefunction();</div>'."\n";
			echo '<h4>Example 2</h4>';
			echo '<div class="elx5_info">this.style.color = \'red\';</div>'."\n";
		} else {
		}

		echo "</div>\n</div>\n";
	}


	/*************************************************************************/
	/* GET A TITLE FOR THE CURRENT LINK (ONLY FOR EDIT AND MENU TYPE = LINK) */
	/*************************************************************************/
	private function linkTitle($component, $link) {
		$eLang = eFactory::getLang();
		if ($component == '') { return $eLang->get('LINK'); }
		if ($component == 'content') {
			if (($link == '') || ($link == '/') || ($link == 'content:/')) {
				return $eLang->get('FRONTPAGE');
			} else if (preg_match('#(\/)$#', $link)) {
				return $eLang->get('LINK_TO_CAT');
			} else if ($link == 'feeds.html') {
				return $eLang->get('SPECIAL_LINK').' <span dir="ltr">('.$eLang->get('CONTENT').')</span>';
			} else if (strpos('tags.html', $link) === 0) {
				return $eLang->get('SPECIAL_LINK').' <span dir="ltr">('.$eLang->get('CONTENT').')</span>';
			} else if (preg_match('#(\.html)$#', $link)) {
				if (strpos($link, '/') !== false) {
					return $eLang->get('LINK_TO_CAT_ARTICLE');
				} else {
					return $eLang->get('LINK_TO_AUT_PAGE');
				}
			} else if (preg_match('#(rss\.xml)$#', $link)) {
				return $eLang->get('LINK_TO_CAT_RSS');
			} else if (preg_match('#(atom\.xml)$#', $link)) {
				return $eLang->get('LINK_TO_CAT_ATOM');
			} else {
				return $eLang->get('SPECIAL_LINK').' <span dir="ltr">('.$eLang->get('CONTENT').')</span>';
			}
		} else {
			if ($link == $component.':/') {
				return sprintf($eLang->get('COMP_FRONTPAGE'), ucfirst($component));
			} else {
				return $eLang->get('SPECIAL_LINK').' <span dir="ltr">('.ucfirst($component).')</span>';
			}
		}
	}


	/********************/
	/* GENERATOR OUTPUT */
	/********************/
	public function linkGeneratorOutput($items, $xmlmenus, $cname, $elxis, $eLang) {
		$this->ajaxHeaders('text/plain');

		if (is_array($items) && (count($items) > 0)) {
			echo '<h3 class="emenu5_gtitle">'.$eLang->get('STANDARD_LINKS')."</h3>\n";
			echo '<ul class="emenu5_gblock">'."\n";
			foreach ($items as $item) {
				$this->showAddLink($item, 0);
			}
			if ($cname == 'content') {
				$pop = $elxis->makeAURL('emenu:mitems/browser.html', 'inner.php');
				echo '<li class="emenu5_glevel0">';
				echo '<a href="javascript:void(null);" onclick="elxPopup(\''.$pop.'\', 800, 440, \'browser\')">'.$eLang->get('LINK_TO_CAT_OR_ARTICLE').'</a>';
				echo "</li>\n";
			}
			echo "</ul>\n";
		}
		if (is_array($xmlmenus) && (count($xmlmenus) > 0)) {
			foreach ($xmlmenus as $xmlmenu) {
				if (!is_array($xmlmenu->items) || (count($xmlmenu->items) == 0)) { continue; }
				echo '<h3 class="emenu5_gtitle">'.$eLang->get('MENU').': '.$xmlmenu->title."</h3>\n";
				echo '<ul class="emenu5_gblock">'."\n";
				foreach ($xmlmenu->items as $item) {
					$this->showAddLink($item, 0, true);
					if (count($item->children) > 0) {
						foreach ($item->children as $level1) {
							$this->showAddLink($level1, 1, true);
							if (count($level1->children) > 0) {
								foreach ($level1->children as $level2) {
									$this->showAddLink($level2, 2, true);
									if (count($level2->children) > 0) {
										foreach ($level2->children as $level3) {
											$this->showAddLink($level3, 3, true);
											if (count($level3->children) > 0) {
												foreach ($level3->children as $level4) {
													$this->showAddLink($level4, 4, true);
												}
											}
										}
									}
								}
							}
						}
					}
				}
				echo "</ul>\n";
			}
		}
		exit;
	}


	/********************************/
	/* SHOW GENERATOR ADDITION LINK */
	/********************************/
	private function showAddLink($item, $level=0, $multiplier=false) {
		$title = addslashes($item->title);
		$alevel = ($multiplier === true) ? $item->alevel * 1000 : $item->alevel;
		echo '<li class="emenu5_glevel'.$level.'">';
		echo '<a href="javascript:void(null);" onclick="emenuSetlink(\''.$title.'\', \''.$item->link.'\', '.$item->secure.', '.$alevel.')">'.$item->name.'</a>';
		echo "</li>\n";
	}


	/***************************/
	/* CATEGORIES BROWSER HTML */
	/***************************/
	public function categoriesBrowser($rows, $paths, $options, $allgroups) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();

		$browser_link = $elxis->makeAURL('emenu:mitems/browser.html', 'inner.php');
		$n = count($paths) - 1;

		echo '<div class="elx5_pad">'."\n";
		echo '<div class="elx5_formtext">'."\n";
		foreach ($paths as $i => $path) {
			$cattitle = $path->title;
			$len = eUTF::strlen($path->title);
			$title = ($len > 20) ? eUTF::substr($path->title, 0, 17).'...' : $path->title;
			echo '<a href="'.$browser_link.'?catid='.$path->catid.'&amp;t=c&amp;o='.$options['order'].'" title="'.$path->title.'">'.$title.'</a>';
			if ($i < $n) { echo " &#187; \n"; }
		}
		echo "</div>\n";

		echo '<table dir="'.$eLang->getinfo('DIR').'" class="elx5_datatable">'."\n";
		echo "<tr>\n";
		echo '<th class="elx5_center">&#160;'."</th>\n";
		echo '<th>'.$eLang->get('CATEGORY')."</th>\n";
		echo '<th class="elx5_center">'.$eLang->get('PUBLISHED')."</th>\n";
		echo '<th class="elx5_center">'.$eLang->get('ARTICLES')."</th>\n";
		echo '<th class="elx5_center">'.$eLang->get('ACCESS')."</th>\n";
		echo '<th class="elx5_center">'.$eLang->get('ACTIONS')."</th>\n";
		echo "</tr>\n";

		$folder_icon = $elxis->icon('folder', 16);
		if ($rows) {
			$link_icon = $elxis->icon('link', 16);
			$rss_icon = $elxis->icon('rss', 16);
			$atom_icon = $elxis->icon('atom', 16);
			$pub_icon = $elxis->icon('tick', 16);
			$unpub_icon = $elxis->icon('error', 16);

			foreach ($rows as $row) {
				$picon = ($row->published == 1) ? $pub_icon : $unpub_icon;
				$title = addslashes($row->title);
				$acctxt = $elxis->alevelToGroup($row->alevel, $allgroups);

				echo '<tr>'."\n";
				echo '<td class="elx5_center"><a href="javascript:void(null);" title="'.$eLang->get('LINK_TO_ITEM').' ('.$row->catid.')" onclick="emenuSetLinkPop(\''.$title.'\', \'content:'.$row->seolink.'\', 0, '.$row->alevel.')"><img src="'.$link_icon.'" alt="add" /></a></td>'."\n";
				echo '<td><a href="'.$browser_link.'?catid='.$row->catid.'&amp;t=c&amp;o='.$options['order'].'">'.$row->title."</a></td>\n";
				echo '<td class="elx5_center"><img src="'.$picon.'" alt="publish status" />'."</td>\n";
				echo '<td class="elx5_center">'.$row->articles."</td>\n";
				echo '<td class="elx5_center">'.$acctxt."</td>\n";
				echo '<td class="elx5_center">'."\n";
				echo '<a href="'.$browser_link.'?catid='.$row->catid.'&amp;t=a&amp;o='.$options['order'].'" title="'.$eLang->get('BROWSE_ARTICLES').'"><img src="'.$folder_icon.'" alt="browse" /></a> &#160; '."\n";
				echo '<a href="javascript:void(null);" title="'.$eLang->get('LINK_TO_CAT_RSS').'" onclick="emenuSetLinkPop(\''.$title.' RSS\', \'content:'.$row->seolink.'rss.xml\', 0, 0)"><img src="'.$rss_icon.'" alt="RSS" /></a> &#160; '."\n";
				echo '<a href="javascript:void(null);" title="'.$eLang->get('LINK_TO_CAT_ATOM').'" onclick="emenuSetLinkPop(\''.$title.' ATOM\', \'content:'.$row->seolink.'atom.xml\', 0, 0)"><img src="'.$atom_icon.'" alt="ATOM" /></a>'."\n";
				echo "</td>\n";
				echo "</tr>\n";
			}
		} else {
			echo '<tr class="elx5_rowwarn"><td class="elx5_center" colspan="6">'.$eLang->get('NO_ITEMS_DISPLAY')."</td></tr>\n";
		}
		echo "</table>\n";

		echo '<div class="elx5_tspace">'."\n";
		if ($options['catid'] == 0) {
			$txt = sprintf($eLang->get('ART_WITHOUT_CAT'), '<strong>'.$options['articles'].'</strong>');
		} else {
			$txt = sprintf($eLang->get('CAT_CONT_ART'), '<strong>'.$cattitle.'</strong>', '<strong>'.$options['articles'].'</strong>');
		}

		echo '<a href="'.$browser_link.'?catid='.$options['catid'].'&amp;t=a&amp;o='.$options['order'].'" title="'.$eLang->get('BROWSE_ARTICLES').'" class="emenu5_catarts">';
		echo '<img src="'.$folder_icon.'" alt="browse" /> '.$txt.'</a>'."\n";

		if ($options['maxpage'] > 1) {
			$linkbase = $browser_link.'?catid='.$options['catid'].'&amp;t='.$options['type'].'&amp;o='.$options['order'];
			$navigation = $elxis->obj('navigation')->navLinks($linkbase, $options['page'], $options['maxpage']);
			echo $navigation;
		}
		$this->sortingOptions($options);
		echo "</div>\n";
		echo "</div>\n";//elx5_pad
	}


	/*************************/
	/* ARTICLES BROWSER HTML */
	/*************************/
	public function articlesBrowser($rows, $paths, $options, $allgroups) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();

		$browser_link = $elxis->makeAURL('emenu:mitems/browser.html', 'inner.php');
		$ctg_seolink = '';

		echo '<div class="elx5_pad">'."\n";
		echo '<div class="elx5_formtext">'."\n";
		foreach ($paths as $i => $path) {
			$len = eUTF::strlen($path->title);
			$title = ($len > 20) ? eUTF::substr($path->title, 0, 17).'...' : $path->title;
			$ctg_seolink = $path->seolink;
			echo '<a href="'.$browser_link.'?catid='.$path->catid.'&amp;t=c&amp;o='.$options['order'].'" title="'.$path->title.'">'.$title.'</a>';
			echo " &#187; \n";
		}
		echo $eLang->get('ARTICLES')."\n";
		echo "</div>\n";

		echo '<table dir="'.$eLang->getinfo('DIR').'" class="elx5_datatable">'."\n";
		echo "<tr>\n";
		echo '<th class="elx5_center">&#160;'."</th>\n";
		echo '<th>'.$eLang->get('ARTICLE')."</th>\n";
		echo '<th class="elx5_center">'.$eLang->get('PUBLISHED')."</th>\n";
		echo '<th class="elx5_center">'.$eLang->get('ACCESS')."</th>\n";
		echo "</tr>\n";
		if ($rows) {
			$link_icon = $elxis->icon('link', 16);
			$folder_icon = $elxis->icon('folder', 16);
			$pub_icon = $elxis->icon('tick', 16);
			$unpub_icon = $elxis->icon('error', 16);
			$browser_link = $elxis->makeAURL('emenu:mitems/browser.html', 'inner.php');

			foreach ($rows as $row) {
				$picon = ($row->published == 1) ? $pub_icon : $unpub_icon;
				$title = addslashes($row->title);
				$acctxt = $elxis->alevelToGroup($row->alevel, $allgroups);

				echo '<tr>'."\n";
				echo '<td class="elx5_center"><a href="javascript:void(null);" title="'.$eLang->get('LINK_TO_ITEM').' ('.$row->id.')" onclick="emenuSetLinkPop(\''.$title.'\', \'content:'.$ctg_seolink.$row->seotitle.'.html\', 0, '.$row->alevel.')"><img src="'.$link_icon.'" alt="add" /></a>'."</td>\n";
				echo '<td>'.$row->title."</td>\n";
				echo '<td class="elx5_center"><img src="'.$picon.'" alt="publish status" />'."</td>\n";
				echo '<td class="elx5_center">'.$acctxt."</td>\n";
				echo '<td class="elx5_center">'."\n";
				echo "</td>\n";
				echo "</tr>\n";
			}
		} else {
			echo '<tr class="elx5_rowwarn"><td class="elx5_center" colspan="4">'.$eLang->get('NO_ITEMS_DISPLAY')."</td></tr>\n";
		}
		echo "</table>\n";

		echo '<div style="margin:5px 10px;">'."\n";
		if ($options['maxpage'] > 1) {
			$linkbase = $browser_link.'?catid='.$options['catid'].'&amp;t='.$options['type'].'&amp;o='.$options['order'];
			$navigation = $elxis->obj('navigation')->navLinks($linkbase, $options['page'], $options['maxpage']);
			echo $navigation;
		}
		$this->sortingOptions($options);
		echo "</div>\n";
		echo "</div>\n";//elx5_pad
	}


	/***************************************/
	/* DISPLAY SORTING OPTIONS FOR BROWSER */
	/***************************************/
	private function sortingOptions($options) {
		$eLang = eFactory::getLang();

		$browser_link = eFactory::getElxis()->makeAURL('emenu:mitems/browser.html', 'inner.php');
		$sorts = array(
			'oa' => $eLang->get('ORDERING').' '.$eLang->get('ASCENDING'),
			'od' => $eLang->get('ORDERING').' '.$eLang->get('DESCENDING'),
			'ta' => $eLang->get('TITLE').' '.$eLang->get('ASCENDING'),
			'td' => $eLang->get('TITLE').' '.$eLang->get('DESCENDING'),
			'ia' => $eLang->get('ID').' '.$eLang->get('ASCENDING'),
			'id' => $eLang->get('ID').' '.$eLang->get('DESCENDING')
		);
		if ($options['type'] == 'a') {
			$sorts['da'] = $eLang->get('DATE').' '.$eLang->get('ASCENDING');
			$sorts['dd'] = $eLang->get('DATE').' '.$eLang->get('DESCENDING');
			$sorts['ma'] = $eLang->get('LAST_MODIFIED').' '.$eLang->get('ASCENDING');
			$sorts['md'] = $eLang->get('LAST_MODIFIED').' '.$eLang->get('DESCENDING');
		}

		echo '<div class="elx5_tspace">'."\n";
		echo '<form name="emchangesort" id="emchangesort" method="get" action="'.$browser_link.'" class="elx5_form">'."\n";
		echo '<div class="elx5_formrow">'."\n";
		echo '<label class="elx5_label" for="emensoo">'.$eLang->get('ORDERING')."</label>\n";
		echo '<div class="elx5_labelside">'."\n";
		echo '<select name="o" id="emensoo" class="elx5_select" onchange="this.form.submit();">'."\n";
		foreach ($sorts as $key => $name) {
			$sel = ($key == $options['order']) ? ' selected="selected"' : '';
			echo '<option value="'.$key.'"'.$sel.'>'.$name."</option>\n";
		}
		echo "</select>\n";
		echo "</div>\n</div>\n";
		echo '<input type="hidden" name="catid" value="'.$options['catid'].'" />'."\n";
		echo '<input type="hidden" name="t" value="'.$options['type'].'" />'."\n";
		echo '<input type="hidden" name="page" value="'.$options['page'].'" />'."\n";
		echo "</form>\n";
		echo "</div>\n";
	}

}

?>