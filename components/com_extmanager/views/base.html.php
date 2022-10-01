<?php 
/**
* @version		$Id: base.html.php 2427 2021-09-26 18:34:45Z IOS $
* @package		Elxis
* @subpackage	Component Extensions Manager
* @copyright	Copyright (c) 2006-2021 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class extmanagerView {


	protected function __construct() {
	}


	/************************/
	/* LIST EXTENSIONS HTML */
	/************************/
	public function listExtensionsHTML($extdata, $options, $rows, $modsacl, $warnmsg, $elxis, $eLang) {
		$eDate = eFactory::getDate();

		$htmlHelper = $elxis->obj('html');

		$link = $elxis->makeAURL('extmanager:/');
		$inlink = $elxis->makeAURL('extmanager:/', 'inner.php');
		$cronlink = $elxis->makeAURL('cpanel:utilities/runcron', 'inner.php');

		$parts = array();
		if (isset($options['section'])) {
			if ($options['section'] != '') { $parts[] = 'section='.$options['section']; }
		}
		if (isset($options['position'])) {
			if ($options['position'] != '') { $parts[] = 'position='.$options['position']; }
		}
		if ($options['key'] != '') { $parts[] = 'key='.$options['key']; }
		$ordlink = ($parts) ? $link.$extdata['type'].'/?'.implode('&amp;', $parts).'&amp;' : $link.$extdata['type'].'/?';
		unset($parts);

		$is_subsite = false;
		if (defined('ELXIS_MULTISITE') && (ELXIS_MULTISITE != 1)) { $is_subsite = true; }

		$p = array();
		if (isset($options['position'])) { $p[] = 'position='.$options['position']; }
		if (isset($options['sn'])) { $p[] = 'sn='.$options['sn']; }
		if (isset($options['so'])) { $p[] = 'so='.$options['so']; }
		$section_rest_options = $p ? implode('&', $p) : '';

		echo '<h1>'.$extdata['pgtitle']."</h1>\n";

		if ($warnmsg != '') {
			echo '<div class="elx5_warning">'.$warnmsg."</div>\n";
		}

		echo '<div class="elx5_box elx5_border_blue">'."\n";
		echo '<div class="elx5_box_body">'."\n";
		echo '<div class="elx5_sticky">'."\n";

		echo '<div class="elx5_dataactions">'."\n";
		if ($elxis->acl()->check('com_extmanager', $extdata['type'], 'install') > 0) {
			if ($extdata['type'] == 'modules') {
				echo '<a href="'.$link.$extdata['type'].'/add.html" class="elx5_dataaction elx5_datahighlight" data-alwaysactive="1" title="'.$eLang->get('NEW').'"><i class="fas fa-plus"></i><span class="elx5_lmobhide"> '.$eLang->get('NEW')."</span></a>\n";
			}
			if (!$is_subsite) {
				echo '<a href="javascript:void(null);" onclick="extMan5InstallExtension();" class="elx5_dataaction elx5_datahighlight" data-alwaysactive="1" title="'.$eLang->get('INSTALL').'/'.$eLang->get('UPDATE').'"><i class="fas fa-download"></i><span class="elx5_lmobhide"> '.$eLang->get('INSTALL')."</span></a>\n";
			}
		}
		if (isset($options['section'])) {
			if ($options['section'] == 'backend') {
				echo '<a href="javascript:void(null);" onclick="extMan5Filter(\'section\', \'frontend\', \''.$section_rest_options.'\');" class="elx5_dataaction elx5_datawarn" data-alwaysactive="1" title="'.$eLang->get('FRONTEND').'"><i class="fas fa-user-secret"></i></a>'."\n";
			} else {
				echo '<a href="javascript:void(null);" onclick="extMan5Filter(\'section\', \'backend\', \''.$section_rest_options.'\');" class="elx5_dataaction elx5_datahighlight" data-alwaysactive="1" title="'.$eLang->get('BACKEND').'"><i class="fas fa-users"></i></a>'."\n";
			}
		}
		if ($elxis->acl()->check('com_extmanager', $extdata['type'], 'edit') > 0) {
			echo '<a href="javascript:void(null);" onclick="elx5EditTableRow(\'extensionstbl\', \'id\');" class="elx5_dataaction" data-selector="1" title="'.$eLang->get('EDIT').'"><i class="fas fa-edit"></i><span class="elx5_tabhide"> '.$eLang->get('EDIT')."</span></a>\n";
			if ($extdata['type'] == 'plugins') {
				echo '<a href="javascript:void(null);" onclick="extMan5ManagePlugin(\'extensionstbl\');" class="elx5_dataaction elx5_dataactive elx5_lmobhide" data-alwaysactive="1" title="'.$eLang->get('MANAGE').'"><i class="fas fa-cog"></i><span class="elx5_lmobhide"> '.$eLang->get('MANAGE')."</span></a>\n";
			}
			if ($extdata['type'] == 'modules') {
				if (isset($options['section'])) {
					if ($options['section'] == 'frontend') {
						echo '<a href="javascript:void(null);" onclick="extMan5PreviewModule(\'extensionstbl\');" class="elx5_dataaction" data-selector="1" title="'.$eLang->get('PREVIEW').'"><i class="fas fa-eye"></i></a>'."\n";
					}
				}
				echo '<a href="javascript:void(null);" onclick="extMan5ExtensionTrans(\'extensionstbl\');" class="elx5_dataaction" data-selector="1" title="'.$eLang->get('TRANSLATIONS').'"><i class="fas fa-globe"></i></a>'."\n";
			}
		}
		if ($extdata['type'] == 'plugins') {
			echo '<a href="javascript:void(null);" onclick="extMan5UsagePlugin(\'extensionstbl\');" class="elx5_dataaction elx5_dataactive" data-alwaysactive="1" title="'.$eLang->get('USAGE').'"><i class="fas fa-people-carry"></i><span class="elx5_tabhide"> '.$eLang->get('USAGE')."</span></a>\n";
		}
		if ($extdata['type'] == 'templates') {
			if (isset($options['section'])) {
				if ($options['section'] == 'frontend') {
					if (!$is_subsite) {
						if ($elxis->acl()->check('com_extmanager', $extdata['type'], 'install') > 0) {
							echo '<a href="javascript:void(null);" onclick="extMan5CopyTpl(\'extensionstbl\');" class="elx5_dataaction" data-selector="1" title="'.$eLang->get('COPY').'"><i class="fas fa-copy"></i><span class="elx5_smallscreenhide"> '.$eLang->get('COPY')."</span></a>\n";
						}
					}
					echo '<a href="'.$link.$extdata['type'].'/positions.html" class="elx5_dataaction elx5_datahighlight" data-alwaysactive="1" title="'.$eLang->get('MODULE_POSITIONS').'"><i class="fas fa-map-pin"></i><span class="elx5_smallscreenhide"> '.$eLang->get('MODULE_POSITIONS')."</span></a>\n";
				}
			}
		}

		$cssclass = ($options['key'] != '') ? 'elx5_dataorange' : 'elx5_datahighlight';
		if (($extdata['type'] == 'modules') || ($extdata['type'] == 'plugins') || ($extdata['type'] == 'templates')) {
			echo '<a href="javascript:void(null);" onclick="elx5ModalOpen(\'esr\');" class="elx5_dataaction '.$cssclass.'" data-alwaysactive="1" title="'.$eLang->get('SEARCH').'"><i class="fas fa-search"></i></a>'."\n";
		} else {
			echo '<a href="javascript:void(null);" onclick="elx5ModalOpen(\'esr\');" class="elx5_dataaction '.$cssclass.'" data-alwaysactive="1" title="'.$eLang->get('SEARCH').'"><i class="fas fa-search"></i><span class="elx5_tabhide"> '.$eLang->get('SEARCH')."</span></a>\n";
		}

		if ($elxis->acl()->check('com_extmanager', $extdata['type'], 'install') > 0) {
			if ($extdata['type'] == 'modules') {
				echo '<a href="javascript:void(null);" onclick="extMan5CopyExtension(\'extensionstbl\');" class="elx5_dataaction" data-selector="1" title="'.$eLang->get('COPY').'"><i class="fas fa-copy"></i></a>'."\n";
				if ($is_subsite) {
					echo '<a href="javascript:void(null);" class="elx5_dataaction" title="'.$eLang->get('DELETE').'" onclick="elx5DeleteTableRows(\'extensionstbl\', false);" data-selector="1" data-activeclass="elx5_datawarn"><i class="fas fa-trash"></i><span class="elx5_lmobhide"> '.$eLang->get('DELETE')."</span></a>\n";
				} else {
					echo '<a href="javascript:void(null);" class="elx5_dataaction" title="'.$eLang->get('DELETE').'/'.$eLang->get('UNINSTALL').'" onclick="elx5DeleteTableRows(\'extensionstbl\', false);" data-selector="1" data-activeclass="elx5_datawarn"><i class="fas fa-trash"></i><span class="elx5_tabhide"> '.$eLang->get('DELETE')."</span></a>\n";
				}
				if ($elxis->getConfig('CRONJOBS') > 0) {
					echo '<a href="javascript:void(null);" onclick="extMan5CronJobs();" class="elx5_dataaction elx5_datahighlight" data-alwaysactive="1" title="Cron jobs"><i class="fas fa-clock"></i></a>'."\n";
				}
			} else {
				if (!$is_subsite) {
					echo '<a href="javascript:void(null);" class="elx5_dataaction" title="'.$eLang->get('UNINSTALL').'" onclick="elx5DeleteTableRows(\'extensionstbl\', false);" data-selector="1" data-activeclass="elx5_datawarn"><i class="fas fa-trash"></i><span class="elx5_lmobhide"> '.$eLang->get('UNINSTALL')."</span></a>\n";
				}				
			}
		}
		echo "</div>\n";
		echo "</div>\n";//elx5_sticky

		echo '<table id="extensionstbl" class="elx5_datatable" data-deletelng="'.addslashes($eLang->get('AREYOUSURE')).'" data-listpage="'.$link.$extdata['type'].'/" data-inpage="'.$inlink.$extdata['type'].'/" data-cronpage="'.$cronlink.'">'."\n";
		echo "<thead>\n";
		echo "<tr>\n";
		echo $htmlHelper->tableHead('&#160;', 'elx5_nosorting elx5_center');
		foreach ($extdata['columns'] as $col) {
			if ($col['sortable']) {
				echo $htmlHelper->sortableTableHead($ordlink, $col['title'], $col['name'], $options['sn'], $options['so'], $col['class']);
			} else {
				$class = ($col['class'] == '') ? 'elx5_nosorting' : 'elx5_nosorting '.$col['class'];
				echo $htmlHelper->tableHead($col['title'], $class);
			}
		}
		echo "</tr>\n";
		echo "</thead>\n";
		echo "<tbody>\n";
		if ($rows) {
			$idcol = $extdata['id'];
			$togglelink = $inlink.$extdata['type'].'/toggle';
			$deflink = $inlink.$extdata['type'].'/makedef';
			$orderinglink = $inlink.$extdata['type'].'/setordering';
			$editlink = $link.$extdata['type'].'/edit.html?page='.$options['page'];
			if (isset($options['sn'])) { if ($options['sn'] != '') { $editlink .= '&sn='.$options['sn']; } }
			if (isset($options['so'])) { if ($options['so'] != '') { $editlink .= '&so='.$options['so']; } }
			if (isset($options['section'])) { if ($options['section'] != '') { $editlink .= '&section='.$options['section']; } }

			$canedit = ($elxis->acl()->check('com_extmanager', $extdata['type'], 'edit') > 0) ? true : false;
			foreach ($rows as $row) {
				$idstr = '';
				if ($extdata['type'] == 'templates') {
					if (isset($options['section'])) {
						if ($options['section'] == 'frontend') { $idstr = ' data-title="'.$row->title .'" data-template="'.$row->template.'"'; }
					}
				}

				echo '<tr id="datarow'.$row->$idcol.'"'.$idstr.'>'."\n";
				echo '<td class="elx5_center">';
				echo '<input type="checkbox" name="dataprimary" id="dataprimary'.$row->$idcol.'" class="elx5_datacheck" value="'.$row->$idcol.'" />';
				echo '<label for="dataprimary'.$row->$idcol.'"></label></td>'."\n";

				foreach ($extdata['columns'] as $col) {
					if ($col['name'] == 'title') {
						$titletxt = ($row->title == '') ? '<span class="elx5_orange">'.$eLang->get('NOT_AVAILABLE').'</span>' : $row->title;
						if ($canedit) {
							$txt = '<a href="'.$editlink.'&'.$idcol.'='.$row->$idcol.'" title="'.$eLang->get('EDIT').'">'.$titletxt.'</a>';
						} else {
							$txt = $titletxt;
						}
					} else if (($extdata['type'] == 'components') && ($col['name'] == 'name')) {
						$titletxt = ($row->name == '') ? '<span class="elx5_orange">'.$eLang->get('NOT_AVAILABLE').'</span>' : $row->name;
						if ($canedit) {
							$txt = '<a href="'.$editlink.'&'.$idcol.'='.$row->$idcol.'" title="'.$eLang->get('EDIT').'">'.$titletxt.'</a>';
						} else {
							$txt = $titletxt;
						}
					} else if ($col['name'] == 'version') {
						$txt = $this->listExtVersion($row->version, $eLang);
					} else if ($col['name'] == 'created') {
						$txt = $this->listExtCreated($row->created, $eLang, $eDate);
					} else if ($col['name'] == 'published') {
						$txt = $this->listExtPublished($row->published, $eLang, $canedit, $row->$idcol, $togglelink);
					} else if ($col['name'] == 'alevel') {
						$txt = $elxis->alevelToGroup($row->alevel);
					} else if ($col['name'] == 'modaccess') {
						$txt = $this->listExtModAccess($row->$idcol, $modsacl, $extdata['allgroups'], $eLang, $elxis);
					} else if ($col['name'] == 'defengine') {
						$txt = $this->listExtDefault($row->defengine, $eLang, $canedit, $row->$idcol, $deflink);
					} else if ($col['name'] == 'deftpl') {
						$txt = $this->listExtDefault($row->deftpl, $eLang, 0, $row->$idcol, '');
					} else if ($col['name'] == 'ordering') {
						$txt = $this->listExtOrdering($row->ordering, $canedit, $row->$idcol, $orderinglink);
					} else if ($col['name'] == 'author') {
						$txt = $this->listExtAuthor($row->author, $row->authorurl, $eLang);
					} else if ($col['name'] == 'position') {
						$txt = $this->listExtPosition($row->position, $options, $eLang);
					} else if ($col['name'] == 'section') {
						$txt = $this->listExtSection($row->section, $options, $eLang);
					} else {
						$name = $col['name'];
						$txt = isset($row->$name) ? $row->$name : '';
					}

					$class_str = ($col['class'] != '') ? ' class="'.$col['class'].'"' : '';
					echo '<td'.$class_str.'>'.$txt.'</td>'."\n";
				}
				echo "</tr>\n";
			}
		} else {
			$n = count($extdata['columns']) + 1;
			echo '<tr id="datarow0" class="elx5_rowwarn"><td class="elx5_center" colspan="'.$n.'">'.$eLang->get('NO_RESULTS')."</td></tr>\n";
		}
		echo "</tbody>\n";
		echo "</table>\n";

		echo "</div>\n";//elx5_box_body

		if ($rows) {
			$linkbase = $ordlink.'sn='.$options['sn'].'&amp;so='.$options['so'];
			echo $htmlHelper->tableSummary($linkbase, $options['page'], $options['maxpage'], $options['total']);
		}

		echo "</div>\n";//elx5_box

		if ($extdata['type'] == 'modules') {
			if (isset($options['section'])) {
				if ($options['section'] == 'frontend') {
					$deflang = $elxis->getConfig('LANG');
					echo '<div id="extmanmodpreview" class="elx5_invisible">'.$elxis->makeURL($deflang.':content:modpreview', 'inner.php')."</div>\n";
				}
			}
			echo '<div id="extmanexttranslations" class="elx5_invisible">'.$elxis->makeAURL('etranslator:single/editall.html', 'inner.php').'?category=module&element=title&tbl=modules&col=title&idcol=id</div>'."\n";
		}
		if ($extdata['type'] == 'plugins') {
			echo '<div id="extmanmngplugins" class="elx5_invisible">'.$elxis->makeAURL('content:plugin/', 'inner.php')."</div>\n";
		}

		elxisLoader::loadFile('includes/libraries/elxis/form5.class.php');

		if (!$is_subsite) {
			if ($elxis->acl()->check('com_extmanager', $extdata['type'], 'install') > 0) {
				echo $htmlHelper->startModalWindow('<i class="fas fa-download"></i> '.$eLang->get('INSTALL').' / '.$eLang->get('UPDATE'), 'ie', '', false, '', '');
				$form = new elxis5Form(array('idprefix' => 'ief', 'labelclass' => 'elx5_label', 'sideclass' => 'elx5_labelside'));
				$form->openForm(array('name' => 'fmiextension', 'method' =>'post', 'action' => $inlink.'install/', 'id' => 'fmiextension', 'enctype' => 'multipart/form-data', 'onsubmit' => 'return false;'));
				$form->openFieldset();
				$note = $eLang->get('SEL_PACKAGE_INSTALL').' '.$eLang->get('UPDATE_UPLOAD_NEW').' '.$eLang->get('CONSIDER_DEV_NOTES_UPD');
				$form->addNote($note, 'elx5_tip elx5_dspace');
				$form->addAjaxFile('package', array('help' => '<div class="elx5_formtext">ZIP, max 20MB</div>'));
				$form->closeFieldset();
				$form->addToken('extmaninst');
				$form->closeForm();
				echo $htmlHelper->endModalWindow(false);

				if ($extdata['type'] == 'templates') {
					if (isset($options['section'])) {
						if ($options['section'] == 'frontend') {
							echo $htmlHelper->startModalWindow('<i class="fas fa-copy"></i> '.$eLang->get('COPY').' <span id="ctforigtitle" class="elx5_yellow"></span>', 'copytpl', '', false, '', '');
							$form = new elxis5Form(array('idprefix' => 'ctf', 'labelclass' => 'elx5_label', 'sideclass' => 'elx5_labelside'));
							//todo: action
							$form->openForm(array('name' => 'fmctemplate', 'method' =>'post', 'action' => $inlink.'templates/copy', 'id' => 'fmctemplate', 'onsubmit' => 'return false;'));
							$form->openFieldset();
							$form->addText('title', '', $eLang->get('TITLE'), array('required' => 'required', 'forcedir' => 'ltr', 'maxlength' => 60));
							$form->addText('template', '', $eLang->get('TEMPLATE'), array('required' => 'required', 'forcedir' => 'ltr', 'maxlength' => 20));
							$form->addHidden('originalid', '0');
							$form->addHidden('originaltemplate', '');
							$form->addNote($eLang->get('COPY_TEMPLATE_TIP'), 'elx5_tip elx5_dspace');
							$form->addHTML('<div class="elx5_vpad">');
							$form->addButton('copy', $eLang->get('COPY'), 'button', array('class' => 'elx5_btn elx5_sucbtn', 'onclick' => 'extMan5DoCopyTpl();', 'data-waitlng' => $eLang->get('PLEASE_WAIT'), 'data-copylng' => $eLang->get('COPY')));
							$form->addHTML('</div>');
							$form->closeFieldset();
							$form->closeForm();
							echo $htmlHelper->endModalWindow(false);
						}
					}
				}
			}
		}

		echo $htmlHelper->startModalWindow('<i class="fas fa-search"></i> '.$eLang->get('SEARCH'), 'esr');
		$form = new elxis5Form(array('idprefix' => 'esr', 'labelclass' => 'elx5_label', 'sideclass' => 'elx5_labelside'));
		$form->openForm(array('name' => 'fmsearchext', 'method' =>'get', 'action' => $link.$extdata['type'].'/', 'id' => 'fmsearchext'));
		$form->openFieldset();
		$form->addText('key', $options['key'], $eLang->get('KEYWORD'), array('dir' => 'ltr'));
		if (isset($options['section'])) {
			if ($options['section'] != '') {
				$form->addHidden('section', $options['section']);
			}
		}
		$form->addHidden('sn', $options['sn']);
		$form->addHidden('so', $options['so']);
		$form->addHTML('<div class="elx5_vpad">');
		$form->addButton('esrsearch', $eLang->get('SEARCH'), 'submit', array('class' => 'elx5_btn elx5_sucbtn', 'fontawesome' => 'fas fa-search'));
		$form->addHTML('</div>');
		$form->closeFieldset();
		$form->closeForm();
		echo $htmlHelper->endModalWindow(false);
	}


	private function listExtVersion($version, $eLang) {
		$txt = ($version == 0) ? '<span class="elx5_orange">'.$eLang->get('NOT_AVAILABLE').'</span>' : $version;
		return $txt;
	}

	private function listExtCreated($created, $eLang, $eDate) {
		$txt = '';
		if (trim($created) != '') {
			$txt = $eDate->formatDate($created, $eLang->get('DATE_FORMAT_2'));
		}
		if ($txt == '') { $txt = '<span class="elx5_orange">'.$eLang->get('NOT_AVAILABLE').'</span>'; }
		return $txt;
	}

	private function listExtPublished($published, $eLang, $canedit, $id, $togglelink) {
		if ($published == 1) {
			$status_class = 'elx5_statuspub';
			$status_title = $eLang->get('PUBLISHED');
		} else {
			$status_class = 'elx5_statusunpub';
			$status_title = $eLang->get('UNPUBLISHED');
		}

		if ($canedit) {
			$txt = '<a href="javascript:void(null);" onclick="elx5ToggleStatus('.$id.', this);" class="elx5_statusicon '.$status_class.'" title="'.$status_title.' - '.$eLang->get('CLICK_TOGGLE_STATUS').'" data-actlink="'.$togglelink.'"></a>';
		} else {
			$txt = '<a href="javascript:void(null);" class="elx5_statusicon '.$status_class.'" title="'.$status_title.'"></a>';
		}
		return $txt;
	}


	private function listExtDefault($default, $eLang, $canedit, $id, $togglelink) {
		if ($default == 1) {
			$status_class = 'elx5_statuspub';
			$status_title = $eLang->get('DEFAULT');
		} else {
			$status_class = 'elx5_statusinact';
			$status_title = $eLang->get('NO');
		}

		if ($canedit) {
			$txt = '<a href="javascript:void(null);" onclick="elx5ToggleStatus('.$id.', this);" class="elx5_statusicon '.$status_class.'" title="'.$status_title.' - '.$eLang->get('CLICK_TOGGLE_STATUS').'" data-actlink="'.$togglelink.'"></a>';
		} else {
			$txt = '<a href="javascript:void(null);" class="elx5_statusicon '.$status_class.'" title="'.$status_title.'"></a>';
		}
		return $txt;
	}


	private function listExtAuthor($author, $authorurl, $eLang) {
		if ($author != '') {
			if (trim($authorurl) != '') {
				$txt = '<a href="'.$authorurl.'" target="_blank">'.$author.'</a>';
			} else {
				$txt = $author;
			}
		} else {
			$txt = '<span class="elx5_orange">'.$eLang->get('NOT_AVAILABLE').'</span>';
		}
		return $txt;
	}


	private function listExtOrdering($ordering, $canedit, $id, $orderinglink) {
		if ($canedit) {
			$txt = '<input name="setordering'.$id.'" id="setordering'.$id.'" type="text" pattern="[0-9]{1,8}" value="'.$ordering.'" onchange="elx5SetOrdering(\'setordering'.$id.'\', \''.$id.'\', 1);" class="elx5_text elx5_superminitext" data-ordlink="'.$orderinglink.'" />';
		} else {
			$txt = $ordering;
		}
		return $txt;
	}

	private function listExtPosition($position, $options, $eLang) {
		if (!isset($options['position'])) { return $position; }

		$p = array();
		if (isset($options['section'])) { $p[] = 'section='.$options['section']; }
		if (isset($options['sn'])) { $p[] = 'sn='.$options['sn']; }
		if (isset($options['so'])) { $p[] = 'so='.$options['so']; }
		$rest_options = $p ? implode('&', $p) : '';

		if ($options['position'] != '') {
			$txt = '<a href="javascript:void(null);" onclick="extMan5UnFilter(\''.$rest_options.'\');" title="'.$eLang->get('REMOVE_FILTER').'"><i class="fas fa-times"></i> '.$position.'</a>';
		} else if ($position != '') {
			$txt = '<a href="javascript:void(null);" onclick="extMan5Filter(\'position\', \''.$position.'\', \''.$rest_options.'\');" title="'.$eLang->get('FILTER_BY_ITEM').'"><i class="fas fa-filter"></i> '.$position.'</a>';
		} else {
			$txt = '-';
		}
		return $txt;
	}

	private function listExtSection($section, $options, $eLang) {
		if (!isset($options['section'])) { return $section; }

		$p = array();
		if (isset($options['position'])) { $p[] = 'position='.$options['position']; }
		if (isset($options['sn'])) { $p[] = 'sn='.$options['sn']; }
		if (isset($options['so'])) { $p[] = 'so='.$options['so']; }
		$rest_options = $p ? implode('&', $p) : '';

		if ($options['section'] == 'backend') {
			$txt = '<a href="javascript:void(null);" onclick="extMan5Filter(\'section\', \'frontend\', \''.$rest_options.'\');" title="'.$eLang->get('FRONTEND').'"><i class="fas fa-sync"></i> '.$eLang->get('BACKEND').'</a>';
		} else {
			$txt = '<a href="javascript:void(null);" onclick="extMan5Filter(\'section\', \'backend\', \''.$rest_options.'\');" title="'.$eLang->get('BACKEND').'"><i class="fas fa-sync"></i> '.$eLang->get('FRONTEND').'</a>';
		}
		return $txt;
	}

	private function listExtModAccess($id, $modsacl, $allgroups, $eLang, $elxis) {
		$txt = $eLang->get('NOONE');
		if (isset($modsacl[$id])) {
			if ($modsacl[$id]['aclvalue'] == 1) {
				if ($modsacl[$id]['uid'] > 0) {
					$txt = '<span class="elx5_orange">'.$eLang->get('USER').' '.$modsacl[$id]['uid'].'</span>';
				} else if ($modsacl[$id]['gid'] > 0) {
					switch ($modsacl[$id]['gid']) {
						case 1: $grpname = $eLang->get('ADMINISTRATOR'); break;
						case 5: $grpname = $eLang->get('USER'); break;
						case 6: $grpname = $eLang->get('EXTERNALUSER'); break;
						case 7: $grpname = $eLang->get('GUEST'); break;
						default:
							$grpname = '';
							if ($allgroups) {
								foreach ($allgroups as $grp) {
									if ($grp['gid'] == $modsacl[$id]['gid']) { $grpname = $grp['groupname']; break; }
								}
							}
						break;
					}
					if ($grpname == '') { $grpname = $eLang->get('GROUP').' '.$modsacl[$id]['gid']; }
					$txt = '<span class="elx5_orange">'.$grpname.'</span>';
				} else {
					$lvl = $modsacl[$id]['minlevel'] * 1000;
					$txt = $elxis->alevelToGroup($lvl, $allgroups);
				}
				if ($modsacl[$id]['num'] > 1) { $txt .= ' +'; }
			}
		}

		return $txt;
	}


	/**************************************************/
	/* ADD/EDIT EXTENSION HTML (ADD ONLY FOR MODULES) */
	/**************************************************/
	public function editExtension($extdata, $exml, $elxis, $eLang) {
		$eDate = eFactory::getDate();

		$deflang = $elxis->getConfig('LANG');
		$row = $extdata['extension'];

		if ($extdata['type'] == 'modules') {
			if (!$row->id) {
				$pgtitle = $eLang->get('ADD_NEW_MODULE');
			} else if ($row->module == 'mod_content') {
				$pgtitle = $eLang->get('EDIT_TEXT_MODULE');
			} else {
				$pgtitle = sprintf($eLang->get('EDIT_MODULE_X'), '<span>'.$row->title.'</span>');
			}
		} else {
			$pgtitle = $eLang->get('EDIT').' <span>'.$extdata['exttitle'].'</span>';
		}

		echo '<h1>'.$pgtitle."</h1>\n";

		$action = $elxis->makeAURL('extmanager:'.$extdata['type'].'/save.html', 'inner.php');

		elxisLoader::loadFile('includes/libraries/elxis/form5.class.php');
		$form = new elxis5Form(array('idprefix' => 'eext'));
		$form->openForm(array('name' => 'fmextedit', 'method' =>'post', 'action' => $action, 'id' => 'fmextedit', 'enctype' => 'multipart/form-data'));
		$tabs = array();
		$tabs['basicsettings'] = $eLang->get('BASIC_SETTINGS');
		if ($extdata['type'] == 'components') { $tabs['compaccess'] = $eLang->get('ACCESS'); }
		if ($extdata['type'] == 'modules') {
			$tabs['modaccess'] = $eLang->get('ACCESS');
			if ($row->module == 'mod_content') { $tabs['modcontent'] = $eLang->get('MODULE_TEXT'); }
		}
		$tabs['parameters'] = $eLang->get('PARAMETERS');
		$custom_tabs = $exml->getTabs();
		if ($custom_tabs) {
			foreach ($custom_tabs as $k => $ctab) {
				$idx = 'custom'.$k;
				$tabs[$idx] = $ctab['label'];
			}
		}
		if ($extdata['type'] == 'modules') {
			if ($row->section == 'frontend') { $tabs['modassignment'] = $eLang->get('MODULE_ASSIGNMENT'); }
		}

		$form->startTabs($tabs);

		foreach ($tabs as $idx => $tab) {
			$form->openTab();

			if ($idx == 'basicsettings') {
				$this->extEditBasicSettings($extdata, $form, $exml, $elxis, $eLang, $eDate);
			} else if ($idx == 'modcontent') {
				$this->extEditModuleContent($row, $deflang, $form, $elxis, $eLang);
			} else if ($idx == 'modassignment') {
				$this->extEditModuleAssignment($extdata, $form, $elxis, $eLang);
			} else if ($idx == 'compaccess') {
				$this->extEditExtensionAccess($extdata, $form, $elxis, $eLang, 0);
			} else if ($idx == 'modaccess') {
				$this->extEditExtensionAccess($extdata, $form, $elxis, $eLang, 1);
			} else if ($idx == 'parameters') {
				elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
				$params = new elxisParameters($row->params, $extdata['xmlfile'], $extdata['xmltype']);
				$form->addHTML($params->render(array(), false));
				unset($params);
			} else if (strpos($idx, 'custom') === 0) {
				$k = intval(str_replace('custom', '', $idx));
				if (isset($custom_tabs[$k])) {
					if ($custom_tabs[$k]['include'] != '') {
						ob_start();
						include($custom_tabs[$k]['include']);
						$tab_contents = ob_get_clean();
					} else {
						$tab_contents = $custom_tabs[$k]['contents'];
					}
					$form->addHTML($tab_contents);
				} else {
					echo '<div class="elx5_error">Elxis could not load tab contents!</div>'."\n";
				}
			} else {
				echo '<div class="elx5_error">Elxis could not load tab contents!!!</div>'."\n";
			}

			$form->closeTab();
		}

		$form->endTabs();

		$onsave = $exml->getTabsOnSave();
		if ($onsave != '') {
			$form->addHidden('onsave', $onsave);
		}
		$form->addToken('fmextedit');
		$form->addHidden('id', $row->id);
		$form->addHidden('page', $extdata['listpage']['page']);
		$form->addHidden('sn', $extdata['listpage']['sn']);
		$form->addHidden('so', $extdata['listpage']['so']);
		$form->addHidden('lpsection', $extdata['listpage']['section']);//not just "section"
		$form->addHidden('task', '');

		$form->closeForm();

		echo '<div id="extmanagerbase" class="elx5_invisible" dir="ltr">'.$elxis->makeAURL('extmanager:/', 'inner.php')."</div>\n";
	}


	/***************************************/
	/* EDIT EXTENSION - BASIC SETTINGS TAB */
	/***************************************/
	private function extEditBasicSettings($extdata, $form, $exml, $elxis, $eLang, $eDate) {
		$row = $extdata['extension'];

		$this->extensionInformation($exml, $elxis, $eLang);

		$form->openFieldset($eLang->get('BASIC_SETTINGS'));

		if ($extdata['type'] == 'engines') {
			$form->addText('title', $row->title, $eLang->get('TITLE'), array('required' => 'required', 'maxlength' => 255));
			$form->addYesNo('published', $eLang->get('PUBLISHED'), $row->published);
			$form->addYesNo('defengine', $eLang->get('DEFAULT'), $row->defengine, array('tip' => $eLang->get('DEF_ENGINE_PUB')));
			$options = array();
			$options[] = $form->makeOption(0, '- '.$eLang->get('FIRST'));
			$q = 1;
			if ($extdata['extspecific']['allengines']) {
				foreach ($extdata['extspecific']['allengines'] as $item) {
					$options[] = $form->makeOption($q, $q.' - '.$item->title);
					$q++;
				}
			}
			$q = ($q > 1) ? $q : 999;
			$options[] = $form->makeOption($q, '- '.$eLang->get('LAST'));
			$form->addSelect('ordering', $eLang->get('ORDERING'), $row->ordering, $options);
			$form->addAccesslevel('alevel', $eLang->get('ACCESS_LEVEL'), $row->alevel, $elxis->acl()->getLevel(), array('dir' => 'ltr'));
			$form->addHidden('engine', $row->engine);
			$form->addHidden('iscore', $row->iscore);
		} else if ($extdata['type'] == 'auth') {
			$form->addText('title', $row->title, $eLang->get('TITLE'), array('required' => 'required', 'maxlength' => 255));
			$form->addYesNo('published', $eLang->get('PUBLISHED'), $row->published);
			$options = array();
			$options[] = $form->makeOption(0, '- '.$eLang->get('FIRST'));
			$q = 1;
			if ($extdata['extspecific']['allauths']) {
				foreach ($extdata['extspecific']['allauths'] as $item) {
					$options[] = $form->makeOption($q, $q.' - '.$item->title);
					$q++;
				}
			}
			$q = ($q > 1) ? $q : 999;
			$options[] = $form->makeOption($q, '- '.$eLang->get('LAST'));
			$form->addSelect('ordering', $eLang->get('ORDERING'), $row->ordering, $options);
			$form->addHidden('auth', $row->auth);
			$form->addHidden('iscore', $row->iscore);
		} else if ($extdata['type'] == 'plugins') {
			$form->addText('title', $row->title, $eLang->get('TITLE'), array('required' => 'required', 'maxlength' => 255));
			$form->addYesNo('published', $eLang->get('PUBLISHED'), $row->published);
			$options = array();
			$options[] = $form->makeOption(0, '- '.$eLang->get('FIRST'));
			$q = 1;
			if ($extdata['extspecific']['allplugins']) {
				foreach ($extdata['extspecific']['allplugins'] as $item) {
					$options[] = $form->makeOption($q, $q.' - '.$item->title);
					$q++;
				}
			}
			$q = ($q > 1) ? $q : 999;
			$options[] = $form->makeOption($q, '- '.$eLang->get('LAST'));
			$form->addSelect('ordering', $eLang->get('ORDERING'), $row->ordering, $options);
			$form->addAccesslevel('alevel', $eLang->get('ACCESS_LEVEL'), $row->alevel, $elxis->acl()->getLevel(), array('dir' => 'ltr'));
			$form->addHidden('plugin', $row->plugin);
			$form->addHidden('iscore', $row->iscore);
		} else if ($extdata['type'] == 'templates') {
			$cur_template = ($row->section == 'backend') ? $elxis->getConfig('ATEMPLATE') : $elxis->getConfig('TEMPLATE');
			$sectiontxt = ($row->section == 'backend') ? $eLang->get('BACKEND') : $eLang->get('FRONTEND');
			$form->addInfo($eLang->get('TITLE'), $row->title);
			$form->addInfo($eLang->get('SECTION'), $sectiontxt);
			if ($row->template == $cur_template) {
				$txt = '<span class="elx5_green elx5_bold">'.$eLang->get('YES').'</span>';
			} else {
				$txt = '<span class="elx5_red elx5_bold">'.$eLang->get('NO').'</span>';
				if ($elxis->acl()->check('com_cpanel', 'settings', 'edit') > 0) { $txt .= ' - '.$eLang->get('SET_DEFAULT_IN_CONFIG');}
			}
			$form->addInfo($eLang->get('DEFAULT'), $txt);
			$form->addHidden('title', $row->title);
			$form->addHidden('template', $row->template);
			$form->addHidden('iscore', $row->iscore);
			$form->addHidden('section', $row->section);
		} else if ($extdata['type'] == 'components') {
			//$form->addInfo($eLang->get('TITLE'), $row->name);
			//$form->addInfo($eLang->get('COMPONENT'), $row->component);
			$form->addText('route', $row->route, $eLang->get('ROUTE'), array('dir' => 'ltr', 'maxlength' => 60, 'tip' => $eLang->get('ROUTING_HELP')));
			$form->addHidden('name', $row->name);
			$form->addHidden('component', $row->component);
			$form->addHidden('iscore', $row->iscore);
		} else if ($extdata['type'] == 'modules') {
			$trdata = array('category' => 'module', 'element' => 'title', 'elid' => intval($row->id));
			$form->addMLText('title', $trdata, $row->title, $eLang->get('TITLE'), array('required' => 'required', 'maxlength' => 255));
			$options = array(
				array('name' => $eLang->get('NO'), 'value' => 0, 'color' => 'red'),
				array('name' => $eLang->get('YES'), 'value' => 1, 'color' => 'yellow'),
				array('name' => $eLang->get('AUTO_MULTILINGUAL_TITLE'), 'value' => 2, 'color' => 'green')
			);
			$form->addItemStatus('showtitle', $eLang->get('SHOW_TITLE'), $row->showtitle, $options, array('tip' => $eLang->get('SHOW_TITLE_DESC')));
			$form->addYesNo('published', $eLang->get('PUBLISHED'), $row->published);
			if (($row->pubdate == '') || ($row->pubdate == '2014-01-01 00:00:00')) {
				$pubdtval = '';
			} else {
				$val = $eDate->elxisToLocal($row->pubdate, true);
				$datetime = new DateTime($val);
				$pubdtval = $datetime->format($eLang->get('DATE_FORMAT_BOX_LONG'));
				unset($datetime, $val);
			}

			if (($row->unpubdate == '') || ($row->unpubdate == '2060-01-01 00:00:00')) {
				$unpubdtval = '';
			} else {
				$val = $eDate->elxisToLocal($row->unpubdate, true);
				$datetime = new DateTime($val);
				$unpubdtval = $datetime->format($eLang->get('DATE_FORMAT_BOX_LONG'));
				unset($datetime, $val);
			}

			$form->addDatetime('pubdate', $pubdtval, $eLang->get('PUBLISH_ON'));
			$form->addDatetime('unpubdate', $unpubdtval, $eLang->get('UNPUBLISH_ON'));

			if ($extdata['extspecific']['cron_msg'][1] != '') {
				$class = 'elx5_sm'.$extdata['extspecific']['cron_msg'][0];
				$form->addInfo('', $extdata['extspecific']['cron_msg'][1], array('class' => $class));
			}
			unset($pubdtval, $unpubdtval);

			if ($extdata['extspecific']['positions']) {
				$options = array();
				foreach ($extdata['extspecific']['positions'] as $position) {
					$options[] = $form->makeOption($position, $position);
				}
				$form->addSelect('position', $eLang->get('POSITION'), $row->position, $options, array('onchange' => 'elxman5LoadPositionOrder();', 'tip' => $eLang->get('POSITION_TPL_MOD')));
			} else {
				$form->addText('position', $row->position, $eLang->get('POSITION'), array('required' => 'required', 'maxlength' => 60, 'tip' => $eLang->get('POSITION_TPL_MOD')));
			}

			$options = array();
			$options[] = $form->makeOption(0, '- '.$eLang->get('FIRST'));
			$q = 1;
			if ($extdata['extspecific']['posmods']) {
				foreach ($extdata['extspecific']['posmods'] as $item) {
					$options[] = $form->makeOption($q, $q.' - '.$item->title);
					$q++;
				}
			}
			$q = ($q > 1) ? $q : 999;
			$options[] = $form->makeOption($q, '- '.$eLang->get('LAST'));
			$form->addSelect('ordering', $eLang->get('ORDERING'), $row->ordering, $options);

			$form->addHidden('section', $row->section);
			$form->addHidden('module', $row->module);
			$form->addHidden('iscore', $row->iscore);
			if ($row->module != 'mod_content') {
				$form->addHidden('content', '');
			}
		}
		$form->closeFieldset();

		$this->extensionDependencies($exml, $elxis, $eLang);
	}


	/************************************************/
	/* EDIT EXTENSION - COMPONENT/MODULE ACCESS TAB */
	/************************************************/
	private function extEditExtensionAccess($extdata, $form, $elxis, $eLang, $is_module=0) {
		$row = $extdata['extension'];

		if ($is_module == 1) {
			if (!$row->id) {
				$form->addNote($eLang->get('FIRST_SAVE_ITEM'), 'elx5_warning');
				return;
			}
		}

		$htmlHelper = $elxis->obj('html');
		$delete_link = $elxis->makeAURL('user:acl/deleteacl', 'inner.php');
		$save_link = $elxis->makeAURL('user:acl/savejson', 'inner.php');

		$sel_user = 0;

		echo '<div class="elx5_box elx5_border_blue">'."\n";
		echo '<div class="elx5_box_body">'."\n";
		echo '<div class="elx5_dataactions elx5_spad">'."\n";
		if ($elxis->acl()->check('com_user', 'acl', 'manage') > 0) {
			$sel_user = $extdata['extspecific']['users'] ? 1 : 0;
			echo '<a href="javascript:void(null);" onclick="extMan5EditACLRule(0, '.$sel_user.', '.$is_module.');" class="elx5_dataaction elx5_datahighlight" data-alwaysactive="1" title="'.$eLang->get('ADD').'"><i class="fas fa-plus"></i><span class="elx5_lmobhide"> '.$eLang->get('ADD').'</span></a>';
		}
		echo "</div>\n";

		echo '<table id="extaccesstbl" class="elx5_datatable" data-deletelng="'.addslashes($eLang->get('AREYOUSURE')).'" data-savepage="'.$save_link.'" data-deletepage="'.$delete_link.'">'."\n";
		echo "<tr>\n";
		if ($is_module == 0) {
			echo $htmlHelper->tableHead($eLang->get('CATEGORY'), 'elx5_tabhide');
			echo $htmlHelper->tableHead($eLang->get('ELEMENT'), 'elx5_tabhide');
		}
		echo $htmlHelper->tableHead($eLang->get('ACTION'));
		echo $htmlHelper->tableHead($eLang->get('ACCESS_LEVEL'), 'elx5_center elx5_lmobhide');
		echo $htmlHelper->tableHead($eLang->get('GROUP'), 'elx5_mobhide');
		echo $htmlHelper->tableHead($eLang->get('USER'), 'elx5_mobhide');
		echo $htmlHelper->tableHead($eLang->get('ACL_VALUE'), 'elx5_center elx5_lmobhide');
		echo $htmlHelper->tableHead($eLang->get('ACTIONS'), 'elx5_center');
		echo "</tr>\n";

		if ($extdata['extspecific']['aclrows']) {
			foreach ($extdata['extspecific']['aclrows'] as $aclrow) {
				$acttxt = $aclrow->action;
				$elemtxt = $aclrow->element;
				$upstring = strtoupper($aclrow->action);
				if ($eLang->exist($upstring)) { $acttxt = $eLang->get($upstring); }
				$upstring = strtoupper($aclrow->element);
				if ($eLang->exist($upstring)) { $elemtxt = $eLang->get($upstring); }
				unset($upstring);

				$grouptxt = '<span class="elx5_gray">'.$eLang->get('NONE').'</span>';
				if ($aclrow->gid > 0) {
					$grouptxt = $eLang->get('GROUP').' '.$aclrow->gid;
					if ($extdata['extspecific']['groups']) {
						foreach ($extdata['extspecific']['groups'] as $group) {
							if ($group['gid'] == $aclrow->gid) {
								$grouptxt = $group['groupname'].' <span dir="ltr">('.$aclrow->gid.')</span>';
								break;
							}
						}
					}
				}

				$usertxt = '<span class="elx5_gray">'.$eLang->get('NOONE').'</span>';
				if ($aclrow->uid > 0) {
					$usertxt = $eLang->get('USER').' '.$aclrow->uid;
					if ($extdata['extspecific']['users']) {
						foreach ($extdata['extspecific']['users'] as $user) {
							if ($user['uid'] == $aclrow->uid) {
								$usertxt = ($elxis->getConfig('REALNAME') == 1) ? $user['firstname'].' '.$user['lastname'] : $user['uname'];
								$usertxt .= ' <span dir="ltr">('.$aclrow->uid.')</span>';
								break;
							}
						}
					}
				}

				if ($aclrow->minlevel < 0) {
					$leveltxt = '<span class="elx5_gray">'.$aclrow->minlevel.'</span>';
				} else {
					$leveltxt = $aclrow->minlevel;
				}

				echo '<tr id="aclrow'.$aclrow->id.'">'."\n";
				if ($is_module == 0) {
					echo '<td id="aclcategory_'.$aclrow->id.'" data-value="'.$aclrow->category.'" class="elx5_tabhide">'.$aclrow->category."</td>\n";
					echo '<td id="aclelement_'.$aclrow->id.'" data-value="'.$aclrow->element.'" class="elx5_tabhide">'.$elemtxt."</td>\n";
				}
				echo '<td id="aclaction_'.$aclrow->id.'" data-value="'.$aclrow->action.'">'.$acttxt."</td>\n";
				echo '<td id="aclminlevel_'.$aclrow->id.'" data-value="'.$aclrow->minlevel.'" class="elx5_center elx5_lmobhide">'.$leveltxt."</td>\n";
				echo '<td id="aclgid_'.$aclrow->id.'" data-value="'.$aclrow->gid.'" class="elx5_mobhide">'.$grouptxt."</td>\n";
				echo '<td id="acluid_'.$aclrow->id.'" data-value="'.$aclrow->uid.'" class="elx5_mobhide">'.$usertxt."</td>\n";
				echo '<td id="aclaclvalue_'.$aclrow->id.'" data-value="'.$aclrow->aclvalue.'" class="elx5_center elx5_lmobhide">'.$aclrow->aclvalue."</td>\n";
				echo '<td class="elx5_center">'."\n";
				if ($elxis->acl()->check('com_user', 'acl', 'manage') > 0) {
					echo '<a href="javascript:void(null);" onclick="extMan5EditACLRule('.$aclrow->id.', '.$sel_user.', '.$is_module.')" title="'.$eLang->get('EDIT').'" class="elx5_smbtn"><i class="fas fa-pencil-alt"></i></a> &#160; '."\n";
					echo '<a href="javascript:void(null);" onclick="extMan5DeleteACLRule('.$aclrow->id.')" title="'.$eLang->get('DELETE').'" class="elx5_smbtn elx5_errorbtn"><i class="fas fa-times"></i></a>'."\n";
				}
				echo "</td>\n";
				echo "</tr>\n";
			}
		}

		echo "</table>\n";
		echo "</div>\n</div>\n";

		if ($elxis->acl()->check('com_user', 'acl', 'manage') > 0) {
			$title = $eLang->get('ACCESS').' : '.$eLang->get('ADD').'/'.$eLang->get('EDIT');
			echo $htmlHelper->startModalWindow($title, 'eacl');
			//NO FORM AS WE ARE ALREADY WITIN A FORM!
			if ($is_module == 0) {
				echo '<div class="elx5_formrow">'."\n";
				echo '<label class="elx5_label" for="aclcategory">'.$eLang->get('CATEGORY')."</label>\n";
				echo '<div class="elx5_labelside">'."\n";
				echo '<select name="aclcategory" id="aclcategory" class="elx5_select" dir="ltr" onchange="extman5SwitchACLCat(\'component\', \''.$row->component.'\');">
				<option value="'.$row->component.'" selected="selected">'.$row->component.'</option>
				<option value="component">component</option>
				</select>'."\n";
				echo "</div>\n</div>\n";

				echo '<div class="elx5_formrow">'."\n";
				echo '<label class="elx5_label" for="aclelement">'.$eLang->get('ELEMENT')."</label>\n";
				echo '<div class="elx5_labelside">'."\n";
				echo '<input type="text" name="aclelement" id="aclelement" dir="ltr" class="elx5_text" value="" placeholder="'.$eLang->get('ELEMENT').'" />'."\n";
				echo "</div>\n</div>\n";
			} else {
				echo '<input type="hidden" name="aclcategory" id="aclcategory" dir="ltr" value="module" />'."\n";
				echo '<input type="hidden" name="aclelement" id="aclelement" dir="ltr" value="'.$row->module.'" />'."\n";
			}

			echo '<div class="elx5_formrow">'."\n";
			echo '<label class="elx5_label" for="aclaction">'.$eLang->get('ACTION')."</label>\n";
			echo '<div class="elx5_labelside">'."\n";
			echo '<input type="text" name="aclaction" id="aclaction" dir="ltr" class="elx5_text" value="" placeholder="'.$eLang->get('ACTION').'" />'."\n";
			echo "</div>\n</div>\n";

			echo '<div class="elx5_formrow">'."\n";
			echo '<label class="elx5_label" for="acltype">'.$eLang->get('TYPE')."</label>\n";
			echo '<div class="elx5_labelside">'."\n";
			echo '<select name="acltype" id="acltype" class="elx5_select" dir="'.$eLang->getinfo('DIR').'" onchange="extman5SwitchACLType();">
			<option value="level" selected="selected">'.$eLang->get('ACCESS_LEVEL').'</option>
			<option value="group">'.$eLang->get('GROUP').'</option>
			<option value="user">'.$eLang->get('USER').'</option>
			</select>'."\n";
			echo "</div>\n</div>\n";

			echo '<div class="elx5_formrow" id="acllevelbox">'."\n";
			echo '<label class="elx5_label" for="acllevel">'.$eLang->get('ACCESS_LEVEL')."</label>\n";
			echo '<div class="elx5_labelside">'."\n";
			echo '<select name="acllevel" id="acllevel" class="elx5_select" dir="'.$eLang->getinfo('DIR').'">';
			if ($extdata['extspecific']['groups']) {
				$lastlevel = -1;
				$space = '';
				foreach ($extdata['extspecific']['groups'] as $group) {
					if ($group['level'] != $lastlevel) {
						$space .= ($lastlevel == -1) ? '' : '&#160; ';
						$lastlevel = $group['level'];						
					}
					$sel = ($group['gid'] == 0) ? ' selected="selected"' : '';
					echo '<option value="'.$group['level'].'"'.$sel.'>'.$space.$group['level'].' - '.$group['groupname']."</option>\n";
				}
			}
			echo "</select>\n";
			echo "</div>\n</div>\n";

			echo '<div class="elx5_invisible" id="aclgroupbox">'."\n";
			echo '<label class="elx5_label" for="aclgroup">'.$eLang->get('GROUP')."</label>\n";
			echo '<div class="elx5_labelside">'."\n";
			echo '<select name="aclgroup" id="aclgroup" class="elx5_select" dir="'.$eLang->getinfo('DIR').'">';
			if ($extdata['extspecific']['groups']) {
				foreach ($extdata['extspecific']['groups'] as $group) {
					$sel = ($group['gid'] == 7) ? ' selected="selected"' : '';
					echo '<option value="'.$group['gid'].'"'.$sel.'>'.$group['gid'].' - '.$group['groupname']."</option>\n";
				}
			}
			echo "</select>\n";
			echo "</div>\n</div>\n";

			echo '<div class="elx5_invisible" id="acluserbox">'."\n";
			echo '<label class="elx5_label" for="acluser">'.$eLang->get('USER')."</label>\n";
			echo '<div class="elx5_labelside">'."\n";
			if ($extdata['extspecific']['users']) {
				echo '<select name="acluser" id="acluser" class="elx5_select" title="'.$eLang->get('USER').'" dir="'.$eLang->getinfo('DIR').'">';
				foreach ($extdata['extspecific']['users'] as $user) {
					$sel = ($user['uid'] == 1) ? ' selected="selected"' : '';
					$utxt = ($elxis->getConfig('REALNAME') == 1) ? $user['firstname'] .' '.$user['lastname'] : $user['uname'];
					echo '<option value="'.$user['uid'].'"'.$sel.'>'.$user['uid'].' - '.$utxt."</option>\n";
				}
				echo "</select>\n";
			} else {
				echo '<input type="text" name="acluser" id="acluser" dir="ltr" value="0" maxlength="6" class="elx5_text elx5_minitext" title="'.$eLang->get('USER').'" />'."\n";
			}
			echo "</div>\n</div>\n";

			echo '<div class="elx5_formrow">'."\n";
			echo '<label class="elx5_label" for="aclvalue">'.$eLang->get('ACL_VALUE')."</label>\n";
			echo '<div class="elx5_labelside">'."\n";
			echo '<select name="aclvalue" id="aclvalue" class="elx5_select" dir="ltr">'."\n";
			echo '<option value="0">0</option>'."\n";
			echo '<option value="1" selected="selected">1</option>'."\n";
			echo '<option value="2">2</option>'."\n";
			echo '<option value="3">3</option>'."\n";
			echo '<option value="4">4</option>'."\n";
			echo '<option value="5">5</option>'."\n";
			echo "</select>\n";
			echo "</div>\n</div>\n";

			if ($is_module == 1) {
				echo '<input type="hidden" name="aclidentity" id="aclidentity" value="'.$row->id.'" />'."\n";
			} else {
				echo '<input type="hidden" name="aclidentity" id="aclidentity" value="0" />'."\n";
			}
			echo '<input type="hidden" name="aclid" id="acleditid" value="0" />'."\n";
			echo '<div class="elx5_vpad">'."\n";
			echo '<button type="button" class="elx5_btn elx5_sucbtn" id="acladd" name="save" onclick="extMan5SaveACLRule('.$sel_user.', '.$is_module.');">'.$eLang->get('SAVE')."</button>\n";
			echo "</div>\n";
			echo $htmlHelper->endModalWindow();
		}
	}


	/***************************************/
	/* EDIT EXTENSION - MODULE CONTENT TAB */
	/***************************************/
	private function extEditModuleContent($row, $deflang, $form, $elxis, $eLang) {
		$cinfo = $eLang->getallinfo($deflang);
		$trdata = array('category' => 'module', 'element' => 'content', 'elid' => (int)$row->id);
		$form->addMLTextarea(
			'content', $trdata, $row->content, $eLang->get('TEXT'), 
			array('cols' => 80, 'rows' => 8, 'forcedir' => $cinfo['DIR'], 'editor' => 'html', 'contentslang' => $deflang)
		);
	}


	/******************************************/
	/* EDIT EXTENSION - MODULE ASSIGNMENT TAB */
	/******************************************/
	private function extEditModuleAssignment($extdata, $form, $elxis, $eLang) {
		$form->addNote($eLang->get('MODULE_ASSIGNMENT_HELP'), 'elx5_sminfo');
		$options = array();
		$size = 1;
		$options[] = $form->makeOption(0, '- '.$eLang->get('ALL_ITEMS').' -');
		if ($extdata['extspecific']['allmenuitems']) {
			$collection = '';
			$disid = -1;
			foreach ($extdata['extspecific']['allmenuitems'] as $menuitem) {
				if (($collection == '') || ($collection != $menuitem['collection'])) {
					$options[] = $form->makeOption($disid, '- '.$menuitem['collection'].' -', array(), 1);
					$collection = $menuitem['collection'];
					$disid--;
					$size++;
				}
				$options[] = $form->makeOption($menuitem['menu_id'], $menuitem['title']);
				$size++;
			}
			unset($collection, $disid);
		}

		if ($size > 20) { $size = 20; }
		if (!is_array($extdata['extspecific']['modmenuitems'])) { $extdata['extspecific']['modmenuitems'] = array(); }

		$form->addSelect('pages', $eLang->get('MENU_ITEMS'), $extdata['extspecific']['modmenuitems'], $options, array('multiple' => 'multiple', 'size' => $size, 'class' => 'elx5_select elx5_selectmultipletall'));
		unset($options, $size);
	}


	/***********************************/
	/* DISPLAY EXTENSION'S INFORMATION */
	/***********************************/
	private function extensionInformation($exml, $elxis, $eLang) {
		if ($exml->getErrorMsg() != '') {
			echo '<div class="elx5_warning">'.$exml->getErrorMsg()."</div>\n";
		}

		$head = $exml->getHead();

		$codeeditor_name = '';
		switch ($head->type) {
			case 'component': $path = 'components/'.$head->name.'/logo.png'; $fonticon = 'fas fa-cube'; $humantype = $eLang->get('COMPONENT'); break;
			case 'module':
				$path = 'modules/'.$head->name.'/logo.png'; $fonticon = 'fas fa-puzzle-piece'; $humantype = $eLang->get('MODULE');
				$codeeditor_name = $head->name;
			break;
			case 'plugin':
				$path = 'components/com_content/plugins/'.$head->name.'/logo.png'; $fonticon = 'fas fa-plug'; $humantype = $eLang->get('PLUGIN');
				$codeeditor_name = 'plg_'.$head->name;
			break;
			case 'template':
				$path = 'templates/'.$head->name.'/logo.png'; $fonticon = 'fas fa-paint-brush'; $humantype = $eLang->get('TEMPLATE');
				$codeeditor_name = 'tpl_'.$head->name;
			break;
			case 'atemplate': $path = 'templates/admin/'.$head->name.'/logo.png'; $fonticon = 'fas fa-paint-brush'; $humantype = $eLang->get('TEMPLATE'); break;
			case 'engine':
				$path = 'components/com_search/engines/'.$head->name.'/logo.png'; $fonticon = 'fas fa-search'; $humantype = $eLang->get('SEARCH_ENGINE');
				$codeeditor_name = 'eng_'.$head->name;
			break;
			case 'auth': $path = 'components/com_user/auth/'.$head->name.'/logo.png'; $fonticon = 'fas fa-key'; $humantype = $eLang->get('AUTH_METHOD'); break;
			default: $path = ''; $fonticon = 'fas fa-cube'; $humantype = 'Unknown'; break;
		}

		$logohtml = '';
		if ($head->link != '') { $logohtml = '<a href="'.$head->link.'" title="'.$head->title.'" target="_blank">'; }
		if (($path != '') && file_exists(ELXIS_PATH.'/'.$path)) {
			$logohtml .= '<img src="'.$elxis->secureBase().'/'.$path.'" alt="logo" />';
		} else {
			$logohtml .= '<i class="'.$fonticon.'"></i>';
		}
		if ($head->link != '') { $logohtml .= '</a>'; }

		$exttitle = '';
		if ($head->type == 'module') {
			$trtxt = strtoupper($head->name).'_TITLE';
			if ($eLang->exist($trtxt)) { $exttitle = $eLang->get($trtxt); }
		}
		if ($exttitle == '') { $exttitle = $eLang->silentGet($head->title, true); }

		if ($head->link != '') {
			$ttl = sprintf($eLang->get('MORE_INFO_FOR'), $head->title);
			$contents = '<h4 class="extman5_iexttitle"><a href="'.$head->link.'" title="'.$ttl.'" target="_blank">'.$exttitle.' <span dir="ltr">('.$head->name.' / '.$humantype.')</span></a></h4>';
		} else {
			$contents = '<h4 class="extman5_iexttitle">'.$exttitle.' <span dir="ltr">('.$head->name.' / '.$humantype.')</span></h4>';
		}

		$contents .= '<div class="extman5_iextrow"><div class="extman5_iextrowk">'.$eLang->get('VERSION').'</div><div class="extman5_iextrowv"><div class="extman5_iextversion">'.$head->version.'</div> &#160; <span dir="ltr">('.eFactory::getDate()->formatDate($head->created, $eLang->get('DATE_FORMAT_12')).")</span></div></div>\n";

		if ($head->author != '') {
			$contents .= '<div class="extman5_iextrow"><div class="extman5_iextrowk">'.$eLang->get('AUTHOR').'</div><div class="extman5_iextrowv">';
			if ($head->authorurl != '') {
				$contents .= '<a href="'.$head->authorurl.'" title="'.$head->author.'" target="_blank">'.$head->author.'</a>';
			} else {
				$contents .= '<strong>'.$head->author.'</strong>';
			}
			if ($head->authoremail != '') { $contents .= ' &#160; <span dir="ltr">(<a href="mailto:'.$head->authoremail.'" title="e-mail">'.$head->authoremail.'</a>)</span>'; }
			$contents .= "</div>\n";
			$contents .= "</div>\n";
		}

		if ($head->copyright != '') {
			$contents .= '<div class="extman5_iextrow"><div class="extman5_iextrowk">'.$eLang->get('COPYRIGHT').'</div><div class="extman5_iextrowv">'.$head->copyright."</div></div>\n";
		}

		if ($head->license != '') {
			$contents .= '<div class="extman5_iextrow"><div class="extman5_iextrowk">'.$eLang->get('LICENSE').'</div><div class="extman5_iextrowv">';
			if ($head->licenseurl != '') {
				$contents .= '<a href="'.$head->licenseurl.'" title="'.$head->license.'" target="_blank">'.$head->license.'</a>';
			} else {
				$contents .= $head->license;
			}
			$contents .= "</div>\n";
			$contents .= "</div>\n";
		}

		if ($head->description != '') {
			$contents .= '<div class="extman5_iextrowd">'.$head->description."</div>\n";
		}

		if ($codeeditor_name != '') {
			if ($elxis->acl()->check('com_cpanel', 'settings', 'edit') > 0) {
				$link = $elxis->makeAURL('cpanel:codeeditor/').'?ext='.$codeeditor_name;
				$contents .= '<div class="extman5_iextrow"><a href="'.$link.'" title="'.$eLang->get('EDIT').'" class="extman5_editfiles">'.$eLang->get('EDIT_EXT_FILES').'</a></div>';
			}
		}

		echo '<div class="extman5_iextwrap">'."\n";
		echo '<div class="extman5_iextlogo">'.$logohtml."</div>\n";
		echo '<div class="extman5_iextcontents">'.$contents."</div>"."\n";
		echo "</div>\n";
	}


	/*****************************************/
	/* DISPLAY EXTENSION'S DEPENDENCIES INFO */
	/*****************************************/
	protected function extensionDependencies($exml, $elxis, $eLang) {
		if ($exml->getErrorMsg() != '') { return; }

		$dependencies = $exml->getDependencies();

		echo '<div class="elx5_box elx5_border_blue">'."\n";
		echo '<div class="elx5_box_body">'."\n";
		echo '<div class="elx5_dataactions elx5_spad">'."\n";
		echo '<h3 class="elx5_box_title">'.$eLang->get('COMPAT_DEPENDECIES')."</h3>\n";
		echo "</div>\n";

		echo '<table dir="'.$eLang->getinfo('DIR').'" class="elx5_datatable">'."\n";
		echo "<tr>\n";
		echo '<th class="elx5_tabhide">'.$eLang->get('TYPE')."</th>\n";
		echo '<th>'.$eLang->get('EXTENSION')."</th>\n";
		echo '<th class="elx5_center">'.$eLang->get('REQUIRED_VERSION')."</th>\n";
		echo '<th class="elx5_center elx5_mobhide">'.$eLang->get('INSTALLED_VERSION')."</th>\n";
		echo "</tr>\n";

		if ($dependencies) {
			foreach ($dependencies as $dpc) {
				$extclassstr = '';
				if ($dpc->icompatible === true) {
					$ivertxt = '<span class="extman5_curversion" title="'.$eLang->get('COMPATIBLE').'">'.$dpc->iversion.'</span>';
				} elseif ($dpc->iversion > 0) {
					$ivertxt = '<span class="extman5_oldversion" title="'.$eLang->get('NOT_COMPATIBLE').'">'.$dpc->iversion.'</span>';
					$extclassstr = ' class="elx5_red"';
				} else if ($dpc->installed === false) {
					$iversion = ($dpc->iversion == 0) ? '-' : $dpc->iversion;
					$ivertxt = '<span class="extman5_oldversion" title="'.$eLang->get('NOT_INSTALLED').'">'.$iversion.'</span>';
					$extclassstr = ' class="elx5_red"';
				} else {
					$ivertxt = '<span class="extman5_version">'.$dpc->iversion.'</span>';
				}
				echo '<tr>'."\n";
				echo '<td>'.$eLang->silentGet($dpc->type, true)."</td>\n";
				echo '<td><span'.$extclassstr.'>'.ucfirst($dpc->extension)."</span></td>\n";
				echo '<td class="elx5_center">';
				if ($dpc->versions) {
					$final = array();
					foreach ($dpc->versions as $v) {
						$plus = strpos($v, '+');
						if ($plus !== false) {
							$v = str_replace('+', '', $v);
							$final[] = $v.' '.$eLang->get('OR_GREATER');
						} else {
							$final[] = $v;
						}
					}
					echo implode(', ', $final);
				}
				echo "</td>\n";
				echo '<td class="elx5_center elx5_mobhide">'.$ivertxt."</td>\n";
				echo "</tr>\n";
			}

		} else {
			echo '<tr class="elx5_rowwarn"><td class="elx5_center" colspan="4">'.$eLang->get('NO_DEPENDENCIES')."</td></tr>\n";
		}
		echo "</table>\n";

		echo "</div>\n";//elx5_box_body
		echo "</div>\n";//elx5_box
	}


	/***************************************/
	/* ECHO PAGE HEADERS FOR AJAX REQUESTS */
	/***************************************/
	protected function ajaxHeaders($type='text/plain') {
		if(ob_get_length() > 0) { ob_end_clean(); }
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').'GMT');
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header('Content-type: '.$type.'; charset=utf-8');
	}

}

?>