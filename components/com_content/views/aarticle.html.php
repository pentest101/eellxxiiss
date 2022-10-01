<?php 
/**
* @version		$Id: aarticle.html.php 2367 2020-12-13 09:49:06Z IOS $
* @package		Elxis
* @subpackage	Component Content
* @copyright	Copyright (c) 2006-2021 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class aarticleContentView extends contentView {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/*************************/
	/* DISPLAY ARTICLES LIST */
	/*************************/
	public function listArticles($rows, $categories, $categories_tree, $allgroups, $warnmsg, $options, $eLang, $elxis) {
		$eDate = eFactory::getDate();

		$link = $elxis->makeAURL('content:articles/');
		$inlink = $elxis->makeAURL('content:articles/', 'inner.php');

		$canedit = ($elxis->acl()->check('com_content', 'article', 'edit') > 0) ? true : false;
		$canpublish = ($elxis->acl()->check('com_content', 'article', 'publish') > 0) ? true : false;

		$htmlHelper = $elxis->obj('html');
		elxisLoader::loadFile('includes/libraries/elxis/form5.class.php');

		$parts = array();
		if ($options['catid'] > -1) { $parts[] = 'catid='.$options['catid']; }
		if ($options['image'] > -1) { $parts[] = 'image='.$options['image']; }
		if ($options['published'] > -1) { $parts[] = 'published='.$options['published']; }
		if ($options['important'] > -1) { $parts[] = 'important='.$options['important']; }
		if ($options['q'] != '') { $parts[] = 'q='.urlencode($options['q']); }
		if ($options['author'] != '') { $parts[] = 'author='.urlencode($options['author']); }

		$ordlink = ($parts) ? $link.'?'.implode('&amp;', $parts).'&amp;' : $link.'?';
		$is_filtered = $parts ? true : false;
		unset($parts);

		if ($options['q'] != '') {
			echo '<h2>'.$eLang->get('ARTICLES').' : <span>'.$options['q']."</span></h2>\n";
		} else {
			echo '<h2>'.$eLang->get('ARTICLES')."</h2>\n";
		}

		if ($warnmsg != '') {
			echo '<div class="elx5_warning">'.$warnmsg."</div>\n";
		}

		echo '<div class="elx5_box elx5_border_blue">'."\n";
		echo '<div class="elx5_box_body">'."\n";
		echo '<div class="elx5_sticky">'."\n";

		echo '<div class="elx5_dataactions">'."\n";
		if ($elxis->acl()->check('com_content', 'article', 'add') > 0) {
			echo '<a href="'.$link.'add.html" class="elx5_dataaction elx5_datahighlight" data-alwaysactive="1" title="'.$eLang->get('ADD').'"><i class="fas fa-plus"></i><span class="elx5_lmobhide"> '.$eLang->get('ADD')."</span></a>\n";
			echo '<a href="javascript:void(null);" onclick="con5CopyMoveArticles(\'copy\');" class="elx5_dataaction" data-selector="1" title="'.$eLang->get('COPY').'"><i class="fas fa-copy"></i><span class="elx5_tabhide"> '.$eLang->get('COPY').'</span></a>'."\n";
		}
		if ($elxis->acl()->check('com_content', 'article', 'edit') > 1) {
			echo '<a href="javascript:void(null);" onclick="con5CopyMoveArticles(\'move\');" class="elx5_dataaction" data-selector="1" title="'.$eLang->get('MOVE').'"><i class="fas fa-location-arrow"></i><span class="elx5_tabhide"> '.$eLang->get('MOVE').'</span></a>'."\n";
		}
		if ($elxis->acl()->check('com_content', 'article', 'delete') > 0) {
			echo '<a href="javascript:void(null);" class="elx5_dataaction" title="'.$eLang->get('DELETE').'" onclick="elx5DeleteTableRows(\'articlestbl\', false);" data-selector="1" data-activeclass="elx5_datawarn"><i class="fas fa-trash"></i><span class="elx5_lmobhide"> '.$eLang->get('DELETE')."</span></a>\n";
		}
		if ($elxis->acl()->check('com_content', 'article', 'edit') > 0) {
			echo '<a href="javascript:void(null);" onclick="con5ArticleTrans(\'articlestbl\');" class="elx5_dataaction elx5_lmobhide" data-selector="1" title="'.$eLang->get('TRANSLATIONS').'"><i class="fas fa-globe"></i></a>'."\n";
		}
		echo '<a href="javascript:void(null);" onclick="con5ArticleShare(\'twitter\');" class="elx5_dataaction elx5_lmobhide" data-selector="1" title="Share on Twitter"><i class="fab fa-twitter"></i></a>'."\n";
		echo '<a href="javascript:void(null);" onclick="con5ArticleShare(\'facebook\');" class="elx5_dataaction elx5_lmobhide" data-selector="1" title="Share on Facebook"><i class="fab fa-facebook-f"></i></a>'."\n";
		if ($elxis->getConfig('CRONJOBS') > 0) {
			echo '<a href="javascript:void(null);" onclick="con5CronJobs();" class="elx5_dataaction elx5_dataactive elx5_lmobhide" data-alwaysactive="1" title="Execute Cron Jobs"><i class="fas fa-clock"></i></a>'."\n";
		}

		if ($is_filtered) {
			echo '<a href="javascript:void(null);" class="elx5_dataaction elx5_dataorange" data-alwaysactive="1" data-elx5tooltip="'.$eLang->get('FILTERS_HAVE_APPLIED').'" onclick="elx5Toggle(\'artsearchoptions\');"><i class="fas fa-search"></i><span class="elx5_smallscreenhide"> '.$eLang->get('SEARCH_OPTIONS')."</span></a>\n";
		} else {
			echo '<a href="javascript:void(null);" class="elx5_dataaction elx5_datahighlight" data-alwaysactive="1" title="'.$eLang->get('SEARCH_OPTIONS').'" onclick="elx5Toggle(\'artsearchoptions\');"><i class="fas fa-search"></i><span class="elx5_smallscreenhide"> '.$eLang->get('SEARCH_OPTIONS')."</span></a>\n";
		}
		echo "</div>\n";

		echo '<div class="elx5_invisible" id="artsearchoptions">'."\n";
		echo '<div class="elx5_actionsbox elx5_dspace">';
		$form = new elxis5Form(array('idprefix' => 'us', 'labelclass' => 'elx5_label', 'sideclass' => 'elx5_labelside'));
		$form->openForm(array('name' => 'fmsrarts', 'method' => 'get', 'action' => $link, 'id' => 'fmsrarts'));
		$form->addHTML('<div class="elx5_2colwrap"><div class="elx5_2colbox elx5_spad">');
		$soptions = array();
		$soptions[] = $form->makeOption(-1, '- '.$eLang->get('SELECT').' -');
		$soptions[] = $form->makeOption(0, '- '.$eLang->get('NONE').' -');
		if ($categories_tree) {
			foreach ($categories_tree as $citem) { $soptions[] = $form->makeOption($citem->catid, $citem->treename); }
		}
		$form->addSelect('catid', $eLang->get('CATEGORY'), $options['catid'], $soptions);
		$form->addText('q', $options['q'], $eLang->get('KEYWORD'));
		$form->addText('author', $options['author'], $eLang->get('AUTHOR'));
		$form->addHTML('</div><div class="elx5_2colbox elx5_spad">');
		$soptions = array(
			array('name' => $eLang->get('ALL'), 'value' => -1, 'color' => 'gray'),
			array('name' => $eLang->get('NO'), 'value' => 0, 'color' => 'red'),
			array('name' => $eLang->get('YES'), 'value' => 1, 'color' => 'green')
		);
		$form->addItemStatus('image', $eLang->get('IMAGE'), $options['image'], $soptions);
		$soptions = array(
			array('name' => $eLang->get('ALL'), 'value' => -1, 'color' => 'gray'),
			array('name' => $eLang->get('NO'), 'value' => 0, 'color' => 'red'),
			array('name' => $eLang->get('YES'), 'value' => 1, 'color' => 'green')
		);
		$form->addItemStatus('published', $eLang->get('PUBLISHED'), $options['published'], $soptions);
		$soptions = array(
			array('name' => $eLang->get('ALL'), 'value' => -1, 'color' => 'gray'),
			array('name' => $eLang->get('NO'), 'value' => 0, 'color' => 'red'),
			array('name' => $eLang->get('YES'), 'value' => 1, 'color' => 'green')
		);
		$form->addItemStatus('important', $eLang->get('IMPORTANT'), $options['important'], $soptions);
		$form->addHTML('</div></div>');
		$form->addHidden('sn', $options['sn']);
		$form->addHidden('so', $options['so']);
		$form->addHTML('<div class="elx5_vpad">');
		$form->addButton('srcbtn', $eLang->get('SEARCH'), 'submit');
		$form->addHTML('</div>');
		$form->closeForm();
		echo "</div>\n";//elx5_actionsbox
		echo "</div>\n";//#artsearchoptions
		echo "</div>\n";//elx5_sticky

		echo '<table id="articlestbl" class="elx5_datatable" data-deletelng="'.addslashes($eLang->get('AREYOUSURE')).'" data-deletepage="'.$inlink.'delete" data-inpage="'.$inlink.'" data-listpage="'.$link.'">'."\n";
		echo "<thead>\n";
		echo "<tr>\n";
		echo $htmlHelper->tableCheckAllHead('articlestbl', 'art');
		echo $htmlHelper->sortableTableHead($ordlink, $eLang->get('ID'), 'id', $options['sn'], $options['so'], 'elx5_center elx5_smallscreenhide');
		echo $htmlHelper->sortableTableHead($ordlink, $eLang->get('TITLE'), 'title', $options['sn'], $options['so']);
		echo $htmlHelper->sortableTableHead($ordlink, $eLang->get('PUBLISHED'), 'published', $options['sn'], $options['so'], 'elx5_center');
		echo $htmlHelper->sortableTableHead($ordlink, $eLang->get('CATEGORY'), 'catid', $options['sn'], $options['so'], 'elx5_lmobhide');
		echo $htmlHelper->sortableTableHead($ordlink, $eLang->get('IMPORTANT'), 'important', $options['sn'], $options['so'], 'elx5_center elx5_tabhide');
		echo $htmlHelper->sortableTableHead($ordlink, $eLang->get('ORDERING'), 'ordering', $options['sn'], $options['so'], 'elx5_center elx5_tabhide');
		echo $htmlHelper->tableHead($eLang->get('ACCESS'), 'elx5_nosorting elx5_midscreenhide');
		echo $htmlHelper->sortableTableHead($ordlink, $eLang->get('DATE'), 'created', $options['sn'], $options['so'], 'elx5_smallscreenhide');
		echo $htmlHelper->sortableTableHead($ordlink, $eLang->get('AUTHOR'), 'created_by_name', $options['sn'], $options['so'], 'elx5_midscreenhide');
		echo $htmlHelper->sortableTableHead($ordlink, $eLang->get('HITS'), 'hits', $options['sn'], $options['so'], 'elx5_center elx5_smallscreenhide');
		echo "</tr>\n";
		echo "</thead>\n";

		echo "<tbody>\n";
		if ($rows) {
			foreach ($rows as $row) {
				$pubdate = (strlen($row->pubdate) == 10) ? $row->pubdate.' 00:00:00' : $row->pubdate;//Elxis 4.2- compatibility
				if ($row->published == 1) {
					$status_class = 'elx5_statuspub';
					$status_title = $eLang->get('PUBLISHED');
					if (($row->unpubdate != '2060-01-01 00:00:00') && ($row->unpubdate >= '2014-01-01 00:00:00')) {
						$status_class = 'elx5_statuspubtime';
						$status_title = $eLang->get('PUBLISHED').'. '.$eLang->get('UNPUBLISH_ON').' '.$eDate->formatDate($row->unpubdate, $eLang->get('DATE_FORMAT_12'));
					}
				} else {
					$status_class = 'elx5_statusunpub';
					$status_title = $eLang->get('UNPUBLISHED');
					if (($pubdate != '2014-01-01 00:00:00') && ($pubdate >= '2014-01-01 00:00:00')) {
						$status_class = 'elx5_statusunpubtime';
						$status_title = $eLang->get('UNPUBLISHED').'. '.$eLang->get('PUBLISH_ON').' '.$eDate->formatDate($pubdate, $eLang->get('DATE_FORMAT_12'));
					}
				}

				$acctxt = $elxis->alevelToGroup($row->alevel, $allgroups);
				$cdate = $eDate->formatDate($row->created, $eLang->get('DATE_FORMAT_4'));
				$author = (eUTF::strlen($row->created_by_name) > 15) ? '<span title="'.$row->created_by_name.'">'.eUTF::substr($row->created_by_name, 0, 12).'...</span>' : $row->created_by_name;

				$title = $row->title;
				if ($options['q'] != '') {//module search, $row->translation may contain $row->title translation
					if ($row->translation != '') {
						if (!preg_match('/'.$options['q'].'/iu', $row->title)) {//query text doesnt exist in default title
							if (preg_match('/'.$options['q'].'/iu', $row->translation)) { $title = $row->translation; }
						}
					}
				}
				if (eUTF::strlen($title) > 30) { $title = eUTF::substr($title, 0, 27).'...'; }

				echo '<tr id="datarow'.$row->id.'">'."\n";
				echo '<td class="elx5_center">';
				echo '<input type="checkbox" name="dataprimary" id="dataprimary'.$row->id.'" class="elx5_datacheck" value="'.$row->id.'" />';
				echo '<label for="dataprimary'.$row->id.'"></label></td>'."\n";
				echo '<td class="elx5_center elx5_smallscreenhide">'.$row->id."</td>\n";
				if ($canedit) {
					echo '<td><a href="'.$link.'edit.html?id='.$row->id.'" title="'.$eLang->get('EDIT').': '.$row->title.'">'.$title."</a></td>\n";
				} else {
					echo '<td><span title="'.$row->title.'">'.$title."</td>\n";
				}
				if ($canpublish) {
					echo '<td class="elx5_center"><a href="javascript:void(null);" onclick="elx5ToggleStatus('.$row->id.', this);" class="elx5_statusicon '.$status_class.'" title="'.$status_title.' - '.$eLang->get('CLICK_TOGGLE_STATUS').'" data-actlink="'.$inlink.'togglestatus"></a></td>'."\n";
				} else {
					echo '<td class="elx5_center"><a href="javascript:void(null);" class="elx5_statusicon '.$status_class.'" title="'.$status_title.'"></a></td>'."\n";
				}
				echo '<td class="elx5_lmobhide">'.$this->listFilterCategory($row->catid, $categories, $options, $eLang)."</td>\n";
				if ($row->important == 1) {
					$status_class = 'elx5_statusstar';
					$status_title = $eLang->get('IMPORTANT').' : '.$eLang->get('YES');
				} else {
					$status_class = 'elx5_statusinact';
					$status_title =$eLang->get('IMPORTANT').' : '. $eLang->get('NO');
				}
				if ($canedit) {
					echo '<td class="elx5_center elx5_tabhide"><a href="javascript:void(null);" onclick="elx5ToggleStatus('.$row->id.', this);" class="elx5_statusicon '.$status_class.'" title="'.$status_title.' - '.$eLang->get('CLICK_TOGGLE_STATUS').'" data-actlink="'.$inlink.'toggleimpstatus"></a></td>'."\n";
				} else {
					echo '<td class="elx5_center elx5_tabhide"><a href="javascript:void(null);" class="elx5_statusicon '.$status_class.'" title="'.$status_title.'"></a></td>'."\n";
				}

				if ($canedit) {
					$txt = '<input name="setordering'.$row->id.'" id="setordering'.$row->id.'" type="text" pattern="[0-9]{1,8}" value="'.$row->ordering.'" onchange="elx5SetOrdering(\'setordering'.$row->id.'\', \''.$row->id.'\', 0);" class="elx5_text elx5_superminitext" data-ordlink="'.$inlink.'setordering'.'"></a>';
				} else {
					$txt = $row->ordering;
				}
				echo '<td class="elx5_center elx5_tabhide">'.$txt."</td>\n";
				echo '<td class="elx5_midscreenhide">'.$acctxt."</td>\n";
				echo '<td class="elx5_smallscreenhide">'.$cdate."</td>\n";
				echo '<td class="elx5_midscreenhide">'.$author."</td>\n";
				echo '<td class="elx5_center elx5_smallscreenhide">'.$row->hits."</td>\n";
				echo "</tr>\n";
			}
		} else {
			echo '<tr id="datarow0" class="elx5_rowwarn"><td class="elx5_center" colspan="11">'.$eLang->get('NO_RESULTS')."</td></tr>\n";
		}
		echo "</tbody>\n";
		echo "</table>\n";

		echo "</div>\n";//elx5_box_body

		if ($rows) {
			$linkbase = $ordlink.'sn='.$options['sn'].'&amp;so='.$options['so'];
			echo $htmlHelper->tableSummary($linkbase, $options['page'], $options['maxpage'], $options['total']);
		}

		echo "</div>\n";//elx5_box

		echo $htmlHelper->startModalWindow($eLang->get('COPY'), 'eartcp');
		echo '<form name="fmcpmvarticle" id="fmcpmvarticle" method="post" action="'.$inlink.'" class="elx5_form" data-lngcopy="'.$eLang->get('COPY').'" data-lngmove="'.$eLang->get('MOVE').'">'."\n";
		echo '<div class="elx5_formrow">'."\n";
		echo '<label class="elx5_label" for="cpmvartcategory">'.$eLang->get('CATEGORY')."</label>\n";
		echo '<div class="elx5_labelside">'."\n";
		echo '<select name="category" id="cpmvartcategory" class="elx5_select">'."\n";
		echo '<option value="-1" selected="selected">- '.$eLang->get('SELECT')." -</option>\n";
		echo '<option value="0">- '.$eLang->get('NONE')." -</option>\n";
		if ($categories_tree) {
			foreach ($categories_tree as $citem) {
				echo '<option value="'.$citem->catid.'">'.$citem->treename."</option>\n";
			}
		}
		echo "</select>\n";
		echo "</div>\n</div>\n";
		echo '<input type="hidden" name="task" id="cpmvtask" value="copy" />'."\n";
		echo '<input type="hidden" name="ids" id="cpmvartids" value="" />'."\n";
		echo '<div class="elx5_vpad">'."\n";
		echo '<button type="button" class="elx5_btn elx5_sucbtn" id="eartcpmvsave" name="save" onclick="con5CopyMoveArtSave();">'.$eLang->get('COPY')."</button> \n";
		echo "</div>\n";
		echo "</form>\n";
		echo $htmlHelper->endModalWindow();

		echo '<div id="con5articletranslations" class="elx5_invisible">'.$elxis->makeAURL('etranslator:single/editall.html', 'inner.php').'?category=com_content&element=title&tbl=content&col=title&idcol=id</div>'."\n";
		echo '<div id="con5articlecron" class="elx5_invisible">'.$elxis->makeAURL('cpanel:utilities/runcron', 'inner.php').'</div>'."\n";
	}


	private function listFilterCategory($catid, $categories, $options, $eLang) {
		if ($catid == 0) {
			$category = $eLang->get('NONE');
			$ctg_text = $eLang->get('NONE');
		} else {
			$category = (isset($categories[$catid])) ? $categories[$catid] : $eLang->get('CATEGORY').' '.$catid;
			$ctg_text = (eUTF::strlen($category) > 20) ? eUTF::substr($category, 0, 17).'...' : $category;
		}

		$parts = array();
		if ($options['image'] > -1) { $parts[] = 'image='.$options['image']; }
		if ($options['published'] > -1) { $parts[] = 'published='.$options['published']; }
		if ($options['important'] > -1) { $parts[] = 'important='.$options['important']; }
		if ($options['q'] != '') { $parts[] = 'q='.urlencode($options['q']); }
		if ($options['author'] != '') { $parts[] = 'author='.urlencode($options['author']); }
		$parts[] = 'sn='.$options['sn'];
		$parts[] = 'so='.$options['so'];
		$rest_options = implode('&', $parts);

		if (!isset($options['catid']) || ($options['catid'] < 0)) {
			$txt = '<a href="javascript:void(null);" onclick="con5Filter('.$catid.', \''.$rest_options.'\');" title="'.$eLang->get('FILTER_BY_ITEM').'"><i class="fas fa-filter"></i> '.$ctg_text.'</a>';
		} else {
			$txt = '<a href="javascript:void(null);" onclick="con5UnFilter(\''.$rest_options.'\');" title="'.$eLang->get('REMOVE_FILTER').' - '.$category.'"><i class="fas fa-times"></i> '.$ctg_text.'</a>';
		}

		return $txt;
	}


	/*************************/
	/* ADD/EDIT ARTICLE HTML */
	/*************************/
	public function editArticle($row, $treeitems, $leveltip, $ordering, $comments, $relkeywords, $cron_msg, $images, $menus) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$eDate = eFactory::getDate();

		$clang = $elxis->getConfig('LANG');
		$cinfo = $eLang->getallinfo($clang);

		$inarturl = $elxis->makeAURL('content:articles/', 'inner.php');

		if ($row->id) {
			$pgtitle = $row->title;
		} else {
			$pgtitle = $eLang->get('NEW_ARTICLE');
		}
		echo '<h2>'.$pgtitle."</h2>\n";

		elxisLoader::loadFile('includes/libraries/elxis/form5.class.php');
		$form = new elxis5Form(array('idprefix' => 'ear', 'tabs_use_numeric' => 1));

		$form->openForm(array('name' => 'fmartedit', 'method' =>'post', 'action' => $inarturl.'save.html', 'id' => 'fmartedit', 'enctype' => 'multipart/form-data'));
		$tabs = array($eLang->get('DETAILS'), $eLang->get('ARTICLE_BODY'), $eLang->get('PARAMETERS'));
		if ($menus) { $tabs[] = $eLang->get('MENU'); }
		if (is_array($comments) && (count($comments) > 0)) { $tabs[] = $eLang->get('COMMENTS'); }
		$form->startTabs($tabs);
	
		$form->openTab();//DETAILS

		$form->openFieldset('SEO &amp; META');
		if ($row->id) {
			$form->addInfo($eLang->get('ID'), $row->id);
		}

		$trdata = array('category' => 'com_content', 'element' => 'title', 'elid' => intval($row->id));
		$form->addMLText('title', $trdata, $row->title, $eLang->get('TITLE'), array('required' => 'required', 'maxlength' => 255));
		$form->addText('seotitle', $row->seotitle, $eLang->get('SEOTITLE'), array('required' => 'required', 'dir' => 'ltr', 'maxlength' => 160, 'tip' => $eLang->get('SEOTITLE_DESC')));
		$form->add5SEO('title', 'seotitle', 'id', $inarturl);

		$catseolink = '';
		$options = array();
		$options[] = $form->makeOption(0, '- '.$eLang->get('NONE').' -');
		if ($treeitems) {
			foreach ($treeitems as $treeitem) {
				if ($row->catid == $treeitem->catid) { $catseolink = $treeitem->seolink; }
				$disabled = (($treeitem->alevel <= $elxis->acl()->getLowLevel()) || ($treeitem->alevel == $elxis->acl()->getExactLevel())) ? 0 : 1;
				$options[] = $form->makeOption($treeitem->catid, $treeitem->treename, array(), $disabled);
			}
		}

		if (trim($row->id) > 0) {
			$txt = '<a href="'.$elxis->makeURL('content:'.$catseolink.$row->seotitle.'.html').'" target="_blank" class="elx5_smlink" title="'.$eLang->get('VIEW').'" id="earseolinktext">'.$catseolink.$row->seotitle.'.html</a>';
			$txt .= ' <a href="javascript:void(null);" onclick="elx5CopyToClipboard(\'earseolinktext\');" title="'.$eLang->get('COPY').'" class="elx5_smlink"><i class="fas fa-copy"></i></a>';
			$form->addInfo($eLang->get('SEO_LINK'), $txt);
		} else {
			$form->addInfo($eLang->get('SEO_LINK'), $eLang->get('SEO_LINK_DESC'));
		}
		unset($catseolink);

		$trdata = array('category' => 'com_content', 'element' => 'subtitle', 'elid' => intval($row->id));
		$form->addMLText('subtitle', $trdata, $row->subtitle, $eLang->get('SUBTITLE'), array('required' => 'required', 'maxlength' => 255, 'tip' => $eLang->get('SUBTITLE_DESC')));

		$trdata = array('category' => 'com_content', 'element' => 'metakeys', 'elid' => intval($row->id));
		$form->addMLText('metakeys', $trdata, $row->metakeys, $eLang->get('METAKEYS'), array('maxlength' => 255, 'tip' => $eLang->get('METAKEYS_DESC')));

		$options2 = array();
		if ($relkeywords) {
			foreach ($relkeywords as $relkeyword) {
				if ($relkeyword == '') { continue; }
				$options2[] = $form->makeOption($relkeyword, $relkeyword);
			}
		}
		$form->addSelectAddOther('relkey', $eLang->get('RELATION_KEY'), $row->relkey, $options2, array('dir' => 'ltr', 'tip' => $eLang->get('RELATION_KEY_DESC')));
		unset($options2);
		$form->closeFieldset();

		$form->openFieldset($eLang->get('CATEGORY').' &amp; '.$eLang->get('ORDERING'));
		$form->addSelect('catid', $eLang->get('CATEGORY'), $row->catid, $options);
		$options = array();
		$options[] = $form->makeOption(0, '- '.$eLang->get('FIRST'));
		if ($row->id) {
			if ($ordering['total'] > 0) {
				if (is_array($ordering['articles']) && (count($ordering['articles']) > 0)) {
					if ($ordering['start'] > 0) {
						$options[] = $form->makeOption(1, '1 - '.$eLang->get('FIRST_ARTICLE'));
						$options[] = $form->makeOption(-1, '...', array(), 1);
					}
					$found = false;
					foreach ($ordering['articles'] as $article) {
						if ($article['id'] == $row->id) { $found = true; }
						$options[] = $form->makeOption($article['ordering'], $article['ordering'].' - '.$article['title']); 
					}
					if (!$found) {
						$options[] = $form->makeOption($row->ordering, $row->ordering.' - '.$row->title);
					}
					if ($ordering['end'] < $ordering['total']) {
						$options[] = $form->makeOption(-2, '...', array(), 1);
						$options[] = $form->makeOption($ordering['total'], $ordering['total'].' - '.$eLang->get('LAST_ARTICLE'));
					}
				}
			}
		}
		$q = ($row->id) ? $ordering['total'] + 1 : 9999;
		$options[] = $form->makeOption($q, '- '.$eLang->get('LAST'));
		$form->addSelect('ordering', $eLang->get('ORDERING'), $row->ordering, $options);
		$form->addYesNo('important', $eLang->get('IMPORTANT'), $row->important, array('tip' => $eLang->get('IMPORTANT_DESC')));
		$form->closeFieldset();

		$form->openFieldset($eLang->get('PUBLISH'));
		$pubaccess = $elxis->acl()->check('com_content', 'article', 'publish');
		if (!$row->id) { $row->published = 0; }

		if (($row->pubdate == '') || ($row->pubdate == '2014-01-01 00:00:00')) {
			$pubdtval = '';
		} else {
			$val = $eDate->elxisToLocal($row->pubdate, true);
			$datetime = new DateTime($val);
			$pubdtval = $datetime->format($eLang->get('DATE_FORMAT_BOX_LONG'));
			unset($datetime);
		}

		if (($row->unpubdate == '') || ($row->unpubdate == '2060-01-01 00:00:00')) {
			$unpubdtval = '';
		} else {
			$val = $eDate->elxisToLocal($row->unpubdate, true);
			$datetime = new DateTime($val);
			$unpubdtval = $datetime->format($eLang->get('DATE_FORMAT_BOX_LONG'));
			unset($datetime);			
		}

		if ($pubaccess > 1) {
			$form->addYesNo('published', $eLang->get('PUBLISHED'), $row->published);
			$form->addDatetime('pubdate', $pubdtval, $eLang->get('PUBLISH_ON'));
			$form->addDatetime('unpubdate', $unpubdtval, $eLang->get('UNPUBLISH_ON'));
			$form->addInfo('', $cron_msg);
		} else if ($pubaccess == 1) {
			if ($row->created_by == $elxis->user()->uid) {
				$form->addYesNo('published', $eLang->get('PUBLISHED'), $row->published);
				$form->addDatetime('pubdate', $pubdtval, $eLang->get('PUBLISH_ON'));
				$form->addDatetime('unpubdate', $unpubdtval, $eLang->get('UNPUBLISH_ON'));
				$form->addInfo('', $cron_msg);
			} else {
				$txt = (intval($row->published) == 1) ? $eLang->get('YES') : $eLang->get('NO');
				$form->addInfo($eLang->get('PUBLISHED'), $txt);
				$form->addHidden('published', $row->published);
				$form->addHidden('pubdate', $pubdtval);
				$form->addHidden('unpubdate', $unpubdtval);
			}
		} else {
			$txt = (intval($row->published) == 1) ? $eLang->get('YES') : $eLang->get('NO');
			$form->addInfo($eLang->get('PUBLISHED'), $txt);
			$form->addHidden('published', $row->published);
			$form->addHidden('pubdate', $pubdtval);
			$form->addHidden('unpubdate', $unpubdtval);
		}
		unset($pubaccess, $pubdtval, $val);
		$form->closeFieldset();

		$form->openFieldset($eLang->get('IMAGE'));
		if ($images) {
			$options = array();
			$options[] = $form->makeOption('', '- '.$eLang->get('SELECT').' -');
			foreach ($images as $image) {
				$options[] = $form->makeOption($image, $image);
			}
			$form->addSelectImage('shared_image', $eLang->get('SELECT_IMAGE'), '', $options);
			unset($options);
		}
		$form->addImage('image', $row->image, $eLang->get('UPLOAD_IMAGE'));
		$trdata = array('category' => 'com_content', 'element' => 'caption', 'elid' => intval($row->id));
		$form->addMLText('caption', $trdata, $row->caption, $eLang->get('CAPTION'), array('maxlength' => 255, 'tip' => $eLang->get('CAPTION_DESC')));
		$form->closeFieldset();

		$form->openFieldset($eLang->get('DATE').' &amp; '.$eLang->get('AUTHOR'));
		$created_user_text = $row->created_by_name;
		if ($elxis->acl()->check('component', 'com_user', 'manage') > 0) {
			$access = $elxis->acl()->check('com_user', 'profile', 'edit');
			if (($access == 2) || (($access == 1) && ($elxis->user()->uid == $row->created_by))) {
				$link = $elxis->makeAURL('user:users/edit.html').'?uid='.$row->created_by;
				$created_user_text = '<a href="'.$link.'" title="'.$eLang->get('EDIT').' '.$row->created_by_name.'">'.$row->created_by_name.'</a>';
			}
			unset($access);
		}

		$val = $eDate->elxisToLocal($row->created, true);
		$datetime = new DateTime($val);
		$crdtval = $datetime->format($eLang->get('DATE_FORMAT_BOX_LONG'));
		$form->addDatetime('newcreated', $crdtval, $eLang->get('DATE'));
		unset($val, $crdtval, $datetime);

		$form->addInfo($eLang->get('AUTHOR'), $created_user_text);

		if ($row->id) {
			if ($row->modified != '1970-01-01 00:00:00') {
				$mod_date = $eDate->formatDate($row->modified, $eLang->get('DATE_FORMAT_11'));
			} else {
				$mod_date = $eLang->get('NEVER');
			}
			$form->addInfo($eLang->get('MODIFIED_DATE'), $mod_date);

			if (($row->modified_by > 0) && ($elxis->acl()->check('component', 'com_user', 'manage') > 0)) {
				$access = $elxis->acl()->check('com_user', 'profile', 'edit');
				if (($access == 2) || (($access == 1) && ($elxis->user()->uid == $row->modified_by))) {
					$link = $elxis->makeAURL('user:edit.html').'?uid='.$row->modified_by;
					$modified_user_text = '<a href="'.$link.'" title="'.$eLang->get('EDIT').' '.$row->modified_by_name.'">'.$row->modified_by_name.'</a>';
					$form->addInfo($eLang->get('AUTHOR'), $modified_user_text);
					unset($modified_user_text);
				}
				unset($access);
			}
		}
		$form->closeFieldset();

		$form->openFieldset();
		$form->addAccesslevel('alevel', $eLang->get('ACCESS_LEVEL'), $row->alevel, $elxis->acl()->getLevel(), array('dir' => 'ltr', 'tip' => $leveltip));

		$form->addInfo($eLang->get('HITS'), $row->hits);
		if ($row->id) {
			$options = array();
			$options[] = $form->makeOption(1, $eLang->get('RESET'));
			$form->addCheckbox('resethits', '', null, $options);
		}
		$form->closeFieldset();

		$form->closeTab();

		$form->openTab();//ARTICLE_BODY
		$trdata = array('category' => 'com_content', 'element' => 'introtext', 'elid' => (int)$row->id);
		$form->addMLTextarea('introtext', $trdata, $row->introtext, $eLang->get('INTRO_TEXT'), array('cols' => 80, 'rows' => 8, 'forcedir' => $cinfo['DIR'], 'editor' => 'html', 'contentslang' => $clang));
		$trdata = array('category' => 'com_content', 'element' => 'maintext', 'elid' => (int)$row->id);
		$form->addMLTextarea('maintext', $trdata, $row->maintext, $eLang->get('MAIN_TEXT'), array('cols' => 80, 'rows' => 8, 'forcedir' => $cinfo['DIR'], 'editor' => 'html', 'contentslang' => $clang));
		$form->closeTab();

		$form->openTab();//PARAMETERS
		elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
		$path = ELXIS_PATH.'/components/com_content/content.article.xml';
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

		if (is_array($comments) && (count($comments) > 0)) {
			$htmlHelper = $elxis->obj('html');

			$pubaccess = $elxis->acl()->check('com_content', 'comments', 'publish');
			$delaccess = $elxis->acl()->check('com_content', 'comments', 'delete');

			$form->openTab();//COMMENTS
			echo '<table id="commentstbl" class="elx5_datatable" data-deletelng="'.addslashes($eLang->get('AREYOUSURE')).'" data-inpage="'.$inarturl.'">'."\n";
			echo "<thead>\n";
			echo "<tr>\n";
			echo $htmlHelper->tableHead($eLang->get('DATE'), 'elx5_nosorting');
			echo $htmlHelper->tableHead($eLang->get('AUTHOR'), 'elx5_nosorting elx5_tabhide');
			echo $htmlHelper->tableHead('Message', 'elx5_nosorting');
			echo "</tr>\n";
			echo "</thead>\n";
			echo "<tbody>\n";
			foreach ($comments as $comment) {
				$cdate = $eDate->formatDate($comment->created, $eLang->get('DATE_FORMAT_4'));
				$txt = '<div class="elx5_dsspace">'.$cdate.'</div>';
				if ($comment->published == 0) {
					if (($pubaccess == 2) || (($pubaccess == 1) && ($comment->uid == $elxis->user()->uid))) {
						$txt .= '<div class="elx5_dsspace elx5_center" id="con5pubcombox'.$comment->id.'"><a href="javascript:void(null);" onclick="con5PublishComment('.$comment->id.')" title="'.$eLang->get('PUBLISH').'" class="elx5_smbtn">'.$eLang->get('UNPUBLISHED').'</a></div>';
					}
				}
				if (($delaccess == 2) || (($delaccess == 1) && ($comment->uid == $elxis->user()->uid))) {
					$txt .= '<div class="elx5_dsspace elx5_center"><a href="javascript:void(null);" onclick="con5DeleteComment('.$comment->id.')" title="'.$eLang->get('DELETE').'" class="elx5_smbtn elx5_errorbtn">'.$eLang->get('DELETE').'</a></div>';
				}

				echo '<tr id="datarow'.$comment->id.'">'."\n";
				echo '<td style="white-space: nowrap; width:150px;">'.$txt."</td>\n";
				echo '<td class="elx5_tabhide">'.$comment->author.'<div class="elx5_tip"><a href="mailto:'.$comment->email.'">'.$comment->email."</a></div></td>\n";
				echo '<td><div class="elx5_tip">'.nl2br($comment->message)."</div></td>\n";
				echo "</tr>\n";
			}
			echo "</tbody>\n";
			echo "</table>\n";
			echo "</div>\n";//elx5_box_body
			echo "</div>\n";//elx5_box

			$form->closeTab();
		}

		$form->endTabs();

		$form->addToken('article');
		$form->addHidden('id', $row->id);
		$form->addHidden('task', '');
		$form->closeForm();

		$pinglink = $elxis->makeAURL('cpanel:beat', 'inner.php');
		echo '<script>'."\n";
		echo 'setInterval(function(){ elx5NoExpirePing(\''.$pinglink.'\'); }, 540000);'."\n";//9 minutes
		echo "</script>\n";
	}

}

?>