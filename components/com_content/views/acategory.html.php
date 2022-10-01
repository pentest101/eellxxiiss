<?php 
/**
* @version		$Id: acategory.html.php 2367 2020-12-13 09:49:06Z IOS $
* @package		Elxis
* @subpackage	Component Content
* @copyright	Copyright (c) 2006-2021 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class acategoryContentView extends contentView {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/***************************/
	/* DISPLAY CATEGORIES LIST */
	/***************************/
	public function listcategories($rows, $options, $allgroups, $eLang, $elxis) {
		$link = $elxis->makeAURL('content:categories/');
		$inlink = $elxis->makeAURL('content:categories/', 'inner.php');

		$htmlHelper = $elxis->obj('html');

		echo '<h2>'.$eLang->get('CONTENT_CATEGORIES')."</h2>\n";

		echo '<div class="elx5_box elx5_border_blue">'."\n";
		echo '<div class="elx5_box_body">'."\n";
		echo '<div class="elx5_sticky">'."\n";

		echo '<div class="elx5_dataactions">'."\n";
		if ($elxis->acl()->check('com_content', 'category', 'add') > 0) {
			echo '<a href="'.$link.'add.html" class="elx5_dataaction elx5_datahighlight" data-alwaysactive="1" title="'.$eLang->get('ADD').'"><i class="fas fa-plus"></i><span class="elx5_lmobhide"> '.$eLang->get('ADD')."</span></a>\n";
		}
		if ($elxis->acl()->check('com_content', 'category', 'edit') > 0) {
			echo '<a href="javascript:void(null);" onclick="con5CategoryTrans(\'categoriestbl\');" class="elx5_dataaction" data-selector="1" title="'.$eLang->get('TRANSLATIONS').'"><i class="fas fa-globe"></i><span class="elx5_lmobhide"> '.$eLang->get('TRANSLATIONS').'</span></a>'."\n";
			echo '<a href="javascript:void(null);" onclick="con5MoveCategory(0);" class="elx5_dataaction elx5_lmobhide" title="'.$eLang->get('MOVE_DOWN').'" data-selector="1">&#x2193;'."</a>\n";
			echo '<a href="javascript:void(null);" onclick="con5MoveCategory(1);" class="elx5_dataaction elx5_lmobhide" title="'.$eLang->get('MOVE_UP').'" data-selector="1">&#x2191;'."</a>\n";
		}
		if ($elxis->acl()->check('com_content', 'category', 'delete') > 0) {
			echo '<a href="javascript:void(null);" class="elx5_dataaction" title="'.$eLang->get('DELETE').'" onclick="elx5DeleteTableRows(\'categoriestbl\', false);" data-selector="1" data-activeclass="elx5_datawarn"><i class="fas fa-trash"></i><span class="elx5_lmobhide"> '.$eLang->get('DELETE')."</span></a>\n";
		}
		echo "</div>\n";
		echo "</div>\n";//elx5_sticky

		echo '<table id="categoriestbl" class="elx5_datatable" data-deletelng="'.addslashes($eLang->get('AREYOUSURE')).'" data-deletepage="'.$inlink.'delete" data-inpage="'.$inlink.'">'."\n";
		echo "<thead>\n";
		echo "<tr>\n";
		echo $htmlHelper->tableHead('&#160;', 'elx5_nosorting elx5_center');
		echo $htmlHelper->tableHead($eLang->get('TITLE'), 'elx5_nosorting');
		echo $htmlHelper->tableHead($eLang->get('PUBLISHED'), 'elx5_nosorting elx5_center elx5_mobhide');
		echo $htmlHelper->tableHead($eLang->get('ACCESS'), 'elx5_nosorting elx5_tabhide');
		echo $htmlHelper->tableHead($eLang->get('ARTICLES'), 'elx5_nosorting elx5_center elx5_tabhide');
		echo $htmlHelper->tableHead($eLang->get('ID'), 'elx5_nosorting elx5_center elx5_lmobhide');
		echo "</tr>\n";
		echo "</thead>\n";

		echo "<tbody>\n";
		if ($rows) {
			$canedit = $elxis->acl()->check('com_content', 'category', 'edit');
			$canpublish = $elxis->acl()->check('com_content', 'category', 'publish');
			$articles_link = $elxis->makeAURL('content:articles/');

			foreach ($rows as $row) {
				if ($row->published == 1) {
					$status_class = 'elx5_statuspub';
					$status_title = $eLang->get('PUBLISHED');
				} else {
					$status_class = 'elx5_statusunpub';
					$status_title = $eLang->get('UNPUBLISHED');
				}
				$acctxt = $elxis->alevelToGroup($row->alevel, $allgroups);

				echo '<tr id="datarow'.$row->catid.'">'."\n";
				echo '<td class="elx5_center">';
				echo '<input type="checkbox" name="dataprimary" id="dataprimary'.$row->catid.'" class="elx5_datacheck" value="'.$row->catid.'" />';
				echo '<label for="dataprimary'.$row->catid.'"></label></td>'."\n";
				if ($canedit) {
					echo '<td><a href="'.$link.'edit.html?catid='.$row->catid.'" title="'.$eLang->get('EDIT').'">'.$row->treename."</a></td>\n";
				} else {
					echo '<td>'.$row->treename."</td>\n";
				}
				if ($canpublish) {
					echo '<td class="elx5_center elx5_mobhide"><a href="javascript:void(null);" onclick="elx5ToggleStatus('.$row->catid.', this);" class="elx5_statusicon '.$status_class.'" title="'.$status_title.' - '.$eLang->get('CLICK_TOGGLE_STATUS').'" data-actlink="'.$inlink.'togglestatus"></a></td>'."\n";
				} else {
					echo '<td class="elx5_center elx5_mobhide"><a href="javascript:void(null);" class="elx5_statusicon '.$status_class.'" title="'.$status_title.'"></a></td>'."\n";
				}
				echo '<td class="elx5_tabhide">'.$acctxt."</td>\n";
				echo '<td class="elx5_center elx5_tabhide"><a href="'.$articles_link.'?catid='.$row->catid.'" title="'.$eLang->get('VIEW_ARTICLES').'">'.$row->acticles."</a></td>\n";
				echo '<td class="elx5_center elx5_lmobhide">'.$row->catid.'</td>'."\n";
				echo "</tr>\n";
			}
		} else {
			echo '<tr id="datarow0" class="elx5_rowwarn"><td class="elx5_center" colspan="6">'.$eLang->get('NO_RESULTS')."</td></tr>\n";
		}
		echo "</tbody>\n";
		echo "</table>\n";

		echo "</div>\n";//elx5_box_body

		if ($rows) {
			echo $htmlHelper->tableSummary($link, $options['page'], $options['maxpage'], $options['total']);
		}

		echo "</div>\n";//elx5_box

		echo '<div id="con5categorytranslations" class="elx5_invisible">'.$elxis->makeAURL('etranslator:single/editall.html', 'inner.php').'?category=com_content&element=category_title&tbl=categories&col=title&idcol=catid</div>'."\n";
	}


	/**************************/
	/* ADD/EDIT CATEGORY HTML */
	/**************************/
	public function editCategory($row, $treeitems, $leveltip, $menus, $elxis, $eLang) {
		$clang = $elxis->getConfig('LANG');
		$cinfo = $eLang->getallinfo($clang);

		$inctgurl = $elxis->makeAURL('content:categories/', 'inner.php');

		if ($row->catid) {
			$pgtitle = $row->title;
		} else {
			$pgtitle = $eLang->get('NEW_CATEGORY');
		}
		echo '<h2>'.$pgtitle."</h2>\n";

		elxisLoader::loadFile('includes/libraries/elxis/form5.class.php');
		$form = new elxis5Form(array('idprefix' => 'ect'));

		$form->openForm(array('name' => 'fmctgedit', 'method' =>'post', 'action' => $inctgurl.'save.html', 'id' => 'fmctgedit', 'enctype' => 'multipart/form-data'));
		$tabs = array($eLang->get('DETAILS'), $eLang->get('DESCRIPTION'), $eLang->get('PARAMETERS'));
		if ($menus) { $tabs[] = $eLang->get('MENU'); }
		$form->startTabs($tabs);
	
		$form->openTab();//DETAILS
		if ($row->catid) {
			$form->addInfo($eLang->get('ID'), $row->catid);
		}

		$trdata = array('category' => 'com_content', 'element' => 'category_title', 'elid' => intval($row->catid));
		$form->addMLText('title', $trdata, $row->title, $eLang->get('TITLE'), array('required' => 'required', 'maxlength' => 255));

		$form->addText('seotitle', $row->seotitle, $eLang->get('SEOTITLE'), array('required' => 'required', 'dir' => 'ltr', 'maxlength' => 160, 'tip' => $eLang->get('SEOTITLE_DESC')));
		$form->add5SEO('title', 'seotitle', 'catid', $inctgurl);
		$form->addText('seolink', $row->seolink, $eLang->get('SEO_LINK'), array('dir' => 'ltr', 'readonly' => 'readonly', 'class' => 'elx5_text', 'tip' => $eLang->get('SEO_LINK_DESC')));

		$options = array();
		$options[] = $form->makeOption(0, $eLang->get('NO'));
		if ($treeitems) {
			$sameroot = array();
			foreach ($treeitems as $treeitem) {
				$disabled = 0;
				if ($row->catid) {
					if ($row->catid == $treeitem->catid) {
						$disabled = 1;
						$sameroot[] = $treeitem->catid;
					} elseif ($treeitem->parent_id == $row->catid) {
						$disabled = 1;
						$sameroot[] = $treeitem->catid;
					} else if (in_array($treeitem->parent_id, $sameroot)) {
						$disabled = 1;
						$sameroot[] = $treeitem->catid;
					}
				}
				$options[] = $form->makeOption($treeitem->catid, $treeitem->treename, array(), $disabled);
			}
			unset($sameroot);
		}
		$form->addSelect('parent_id', $eLang->get('PARENT_CTG'), $row->parent_id, $options, array('tip' => $eLang->get('PARENT_CTG_DESC')));

		$options = array();
		$options[] = $form->makeOption(0, '- '.$eLang->get('FIRST'));
		$q = 1;
		if ($row->catid) {
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
		$form->addAccesslevel('alevel', $eLang->get('ACCESS_LEVEL'), $row->alevel, $elxis->acl()->getLevel(), array('tip' => $leveltip));

		if ($elxis->acl()->check('com_content', 'category', 'publish') > 0) {
			$form->addYesNo('published', $eLang->get('PUBLISHED'), $row->published);
		} else {
			if (!$row->catid) { $row->published = 0; }
			$txt = (intval($row->published) == 1) ? $eLang->get('YES') : $eLang->get('NO');
			$form->addInfo($eLang->get('PUBLISHED'), $txt);
			$form->addHidden('published', $row->published);
		}

		$form->addImage('image', $row->image, $eLang->get('IMAGE'));
		$form->closeTab();

		$form->openTab();//DESCRIPTION
		$trdata = array('category' => 'com_content', 'element' => 'category_description', 'elid' => (int)$row->catid);
		$form->addMLTextarea(
			'description', $trdata, $row->description, $eLang->get('DESCRIPTION'), 
			array('cols' => 80, 'rows' => 8, 'forcedir' => $cinfo['DIR'], 'editor' => 'html', 'contentslang' => $clang)
		);
		$form->closeTab();

		$form->openTab();//PARAMETERS
		elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
		$path = ELXIS_PATH.'/components/com_content/content.category.xml';
		$params = new elxisParameters($row->params, $path, 'component');
		$form->addHTML($params->render());
		unset($params);
		$form->closeTab();

		if ($menus) {
			$form->openTab();//MENU
			$form->addNote($eLang->get('CREATE_MENUITEM_PAGE'));
			foreach ($menus as $collection => $items) {
				$options = array();
				$options[] = $form->makeOption(0, '--- '.$eLang->get('DONTCR_MENUITEM').' ---');
				$options[] = $form->makeOption('ROOT', ':: '.$eLang->get('TOP_LEVEL').' ::');
				foreach ($items as $item) {
					$options[] = $form->makeOption($item['menu_id'], $item['treename']);
				}
				$form->addSelect('collect_'.$collection, $collection, '0', $options);//important: "0" with quotes!
			}
			$form->closeTab();
		}

		$form->endTabs();

		$form->addToken('category');
		$form->addHidden('catid', $row->catid);
		$form->addHidden('task', '');
		$form->closeForm();

		echo '<div id="lng_titleempty" class="elx5_invisible">'.addslashes(sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('TITLE')))."</div>\n";

		$pinglink = $elxis->makeAURL('cpanel:beat', 'inner.php');
		echo '<script>'."\n";
		echo 'setInterval(function(){ elx5NoExpirePing(\''.$pinglink.'\'); }, 540000);'."\n";//9 minutes
		echo "</script>\n";
	}

}

?>