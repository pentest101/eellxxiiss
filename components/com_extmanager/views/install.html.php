<?php 
/**
* @version		$Id: install.html.php 2355 2020-10-17 18:04:28Z IOS $
* @package		Elxis
* @subpackage	Component Extensions Manager
* @copyright	Copyright (c) 2006-2020 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class installExtmanagerView extends extmanagerView {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/********************************/
	/* SHOW COMPONENT CONTROL PANEL */
	/********************************/
	public function ipanel($sync, $subdbupdated, $elxis, $eLang) {
		$extmanlink = $elxis->makeAURL('extmanager:/');

		$types = array(
			'components' => array('fonticon' => 'fas fa-cube', 'title' => $eLang->get('COMPONENTS'), 'descr' => $eLang->get('MANAGE_COMPONENTS')),
			'modules' => array('fonticon' => 'fas fa-puzzle-piece', 'title' => $eLang->get('MODULES'), 'descr' => $eLang->get('MANAGE_MODULES')),
			'plugins' => array('fonticon' => 'fas fa-plug', 'title' => $eLang->get('CONTENT_PLUGINS'), 'descr' => $eLang->get('MANAGE_CONTENT_PLUGINS')),
			'templates' => array('fonticon' => 'fas fa-paint-brush', 'title' => $eLang->get('TEMPLATES'), 'descr' => $eLang->get('MANAGE_TEMPLATES')),
			'engines' => array('fonticon' => 'fas fa-search', 'title' => $eLang->get('SEARCH_ENGINES'), 'descr' => $eLang->get('MANAGE_SEARCH_ENGINES')),
			'auth' => array('fonticon' => 'fas fa-key', 'title' => $eLang->get('AUTH_METHODS'), 'descr' => $eLang->get('MANAGE_AUTH_METHODS'))
		);

		$is_subsite = false;
		if (defined('ELXIS_MULTISITE')) {
			$is_subsite = (ELXIS_MULTISITE == 1) ? false : true;
		}

		$can_install = $elxis->acl()->check('com_extmanager', 'components', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'modules', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'templates', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'engines', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'auth', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'plugins', 'install');
		if ($can_install > 0) {
			if (($elxis->getConfig('SECURITY_LEVEL') > 0) && ($elxis->user()->gid <> 1)) { $can_install = 0; }
		}

		echo '<h2>'.$eLang->get('EXTENSIONS').' <span>Elxis '.$elxis->getVersion()."</span></h2>\n";

		echo '<div class="elx5_2colwrap">'."\n";
		echo '<div class="elx5_2colbox">'."\n";
		echo '<ul class="extman5_list">'."\n";

		$syncro_result = false;
		$modal_install = false;
		if ($can_install > 0) {
			if ($elxis->getConfig('SECURITY_LEVEL') > 0) {
				if ($elxis->user()->gid == 1) {
					if ($is_subsite) {
						if (!$subdbupdated) {
							$this->updateSubDBForm($extmanlink, $elxis, $eLang);
						}
						$syncro_result = $this->synchroForm($sync, $elxis, $eLang);
						if ($syncro_result['text'] != '') { echo $syncro_result['text']; }
					} else {
						$this->installForm($eLang);
						$modal_install = true;
					}
				}
			} else {
				if ($is_subsite) {
					if (!$subdbupdated) {
						$this->updateSubDBForm($extmanlink, $elxis, $eLang);
					}
					$syncro_result = $this->synchroForm($sync, $elxis, $eLang);
					if ($syncro_result['text'] != '') { echo $syncro_result['text']; }
				} else {
					$this->installForm($eLang);
					$modal_install = true;
				}
			}
		}

		echo '<li><a href="'.$extmanlink.'browse/" class="extman5_listlink" title="EDC live">'."\n";
		echo '<div class="extman5_listicon"><i class="fas fa-cubes"></i></div>'."\n";
		echo '<div class="extman5_listside">'."\n";
		echo '<h4 class="extman5_listh4">'.$eLang->get('ELXISDC')."</h4>\n";
		echo '<div class="extman5_listdesc">'.$eLang->get('BROWSE_EXTS_LIVE')."</div>\n";
		echo '</div>'."\n";
		echo "</a></li>\n";
		if (!$is_subsite) {
			if ($can_install > 0) {
				echo '<li><a href="'.$extmanlink.'install/updates.html" class="extman5_listlink">'."\n";
				echo '<div class="extman5_listicon"><i class="fas fa-check"></i></div>'."\n";
				echo '<div class="extman5_listside">'."\n";
				echo '<h4 class="extman5_listh4">'.$eLang->get('CHECK_UPDATES')."</h4>\n";
				echo '<div class="extman5_listdesc">Elxis &amp; '.$eLang->get('EXTENSIONS')."</div>\n";
				echo '</div>'."\n";
				echo "</a></li>\n";
			}
			echo '<li><a href="'.$extmanlink.'install/checkfs.html" class="extman5_listlink">'."\n";
			echo '<div class="extman5_listicon"><i class="fas fa-file-medical-alt"></i></div>'."\n";
			echo '<div class="extman5_listside">'."\n";
			echo '<h4 class="extman5_listh4">'.$eLang->get('CHECK_FS')."</h4>\n";
			echo '<div class="extman5_listdesc">Elxis'."</div>\n";
			echo '</div>'."\n";
			echo "</a></li>\n";
		}
		echo "</ul>\n";
		echo "</div>\n";
		echo '<div class="elx5_2colbox">'."\n";
		echo '<ul class="extman5_list">'."\n";
		foreach ($types as $k => $type) {
			if ($elxis->acl()->check('com_extmanager', $k, 'edit') > 0) {
				$link = $elxis->makeAURL('extmanager:'.$k.'/');
				$desc = $type['descr'];
			} else {
				$link = 'javascript:voic(null);';
				$desc = $type['descr'].' - '.$eLang->get('ACCESS_DENIED');	
			}
			echo '<li><a href="'.$link.'" class="extman5_listlink">'."\n";
			echo '<div class="extman5_listicon"><i class="'.$type['fonticon'].'"></i></div>'."\n";
			echo '<div class="extman5_listside">'."\n";
			echo '<h4 class="extman5_listh4">'.$type['title']."</h4>\n";
			echo '<div class="extman5_listdesc">'.$desc."</div>\n";
			echo '</div>'."\n";
			echo "</a></li>\n";
		}
		echo "</ul>\n";
		echo "</div>\n";//elx5_2colbox
		echo "</div>\n";//elx5_2colwrap

		if ($modal_install) {
			$htmlHelper = $elxis->obj('html');
			$inlink = $elxis->makeAURL('extmanager:/', 'inner.php');
			elxisLoader::loadFile('includes/libraries/elxis/form5.class.php');
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
		}

		if ($syncro_result) {
			if ($syncro_result['options']) {
				$htmlHelper = $elxis->obj('html');
				$inlink = $elxis->makeAURL('extmanager:/', 'inner.php');
				elxisLoader::loadFile('includes/libraries/elxis/form5.class.php');
				echo $htmlHelper->startModalWindow('<i class="fas fa-sync"></i> '.$eLang->get('SYNCHRONIZATION'), 'se', '', false, '', '');
				$form = new elxis5Form(array('idprefix' => 'sef', 'labelclass' => 'elx5_label', 'sideclass' => 'elx5_labelside'));
				$form->openForm(array('name' => 'fmsextension', 'method' =>'post', 'action' => $inlink.'install/synchro', 'id' => 'fmsextension', 'onsubmit' => 'return false;'));
				$form->openFieldset();
				$form->addNote($eLang->get('SYNCHRONIZATION_INFO'), 'elx5_tip elx5_dspace');

				$options = array();
				$options[] = $form->makeOption('', '- '.$eLang->get('SELECT').' -');
				foreach ($syncro_result['options'] as $exttype => $data) {
					foreach ($data['items'] as $item) {
						$options[] = $form->makeOption($item, $item, array(), 0, $data['label']);
					}
				}
				$form->addSelect('extension', $eLang->get('EXTENSION'), '', $options);
				$form->addHTML('<div class="elx5_vpad">');
				$form->addButton('syncsub', $eLang->get('SYNCHRONIZE'), 'button', array('class' => 'elx5_btn elx5_sucbtn', 'onclick' => 'extMan5SyncExtension();', 'data-waitlng' => $eLang->get('SYNCHRO_IN_PROGRESS'), 'data-synclng' => $eLang->get('SYNCHRONIZE')));
				$form->addHTML('</div>');
				$form->closeFieldset();
				$form->addToken('extmansync');
				$form->closeForm();
				echo $htmlHelper->endModalWindow(false);
			}
		}
	}


	/****************************/
	/* UPDATE SUB-SITE DATABASE */
	/****************************/
	private function updateSubDBForm($extmanlink, $elxis, $eLang) {
		$desc = sprintf($eLang->get('DB_NEEDSUP'), '<strong>'.$elxis->getVersion().'</strong>');
		echo '<li class="extman5_liwarn"><a href="'.$extmanlink.'?upsubdb=1" class="extman5_listlink">'."\n";
		echo '<div class="extman5_listicon"><i class="fas fa-database"></i></div>'."\n";
		echo '<div class="extman5_listside">'."\n";
		echo '<h4 class="extman5_listh4">'.$eLang->get('UPDATE').' - '.$eLang->get('DATABASE')."</h4>\n";
		echo '<div class="extman5_listdesc">'.$desc."</div>\n";
		echo '</div>'."\n";
		echo "</a></li>\n";
	}


	/*********************/
	/* SHOW INSTALL FORM */
	/*********************/
	private function installForm($eLang) {
		echo '<li class="extman5_lispec"><a href="javascript:void(null);" onclick="extMan5InstallExtension();" class="extman5_listlink">'."\n";
		echo '<div class="extman5_listicon"><i class="fas fa-download"></i></div>'."\n";
		echo '<div class="extman5_listside">'."\n";
		echo '<h4 class="extman5_listh4">'.$eLang->get('INSTALL').'/'.$eLang->get('UPDATE')."</h4>\n";
		echo '<div class="extman5_listdesc">'.$eLang->get('INSTNEW_EXT_UPEXIST')."</div>\n";
		echo '</div>'."\n";
		echo "</a></li>\n";
	}


	/*****************************/
	/* SHOW SYNCRHONIZATION FORM */
	/*****************************/
	private function synchroForm($sync, $elxis, $eLang) {
		$can_sync = 0;
		$options = array();
		if ($elxis->acl()->check('com_extmanager', 'components', 'install') > 0) {
			$can_sync++;
			if (count($sync['components']) > 0) {
				$options['components'] = array('label' => $eLang->get('COMPONENTS'), 'items' => array());
				foreach ($sync['components'] as $comp) { $options['components']['items'][] = $comp; }
			}
		}

		if ($elxis->acl()->check('com_extmanager', 'modules', 'install') > 0) {
			$can_sync++;
			if (count($sync['modules']) > 0) {
				$options['modules'] = array('label' => $eLang->get('MODULES'), 'items' => array());
				foreach ($sync['modules'] as $mod) { $options['modules']['items'][] = $mod; }
			}
		}

		if ($elxis->acl()->check('com_extmanager', 'plugins', 'install') > 0) {
			$can_sync++;
			if (count($sync['plugins']) > 0) {
				$options['plugins'] = array('label' => $eLang->get('CONTENT_PLUGINS'), 'items' => array());
				foreach ($sync['plugins'] as $plg) { $options['plugins']['items'][] = $plg; }
			}
		}

		if ($elxis->acl()->check('com_extmanager', 'templates', 'install') > 0) {
			$can_sync++;
			if (count($sync['templates']) > 0) {
				$options['templates'] = array('label' => $eLang->get('TEMPLATES'), 'items' => array());
				foreach ($sync['templates'] as $tpl) { $options['templates']['items'][] = $tpl; }
			}
		}

		if ($elxis->acl()->check('com_extmanager', 'engines', 'install') > 0) {
			$can_sync++;
			if (count($sync['engines']) > 0) {
				$options['engines'] = array('label' => $eLang->get('SEARCH_ENGINES'), 'items' => array());
				foreach ($sync['engines'] as $eng) { $options['engines']['items'][] = $eng; }
			}
		}

		if ($elxis->acl()->check('com_extmanager', 'auth', 'install') > 0) {
			$can_sync++;
			if (count($sync['auths']) > 0) {
				$options['auths'] = array('label' => $eLang->get('AUTH_METHODS'), 'items' => array());
				foreach ($sync['auths'] as $auth) { $options['auths']['items'][] = $auth; }
			}
		}

		$result = array('text' => '', 'options' => array());
		if ($can_sync == 0) { return $result; }

		if (!$options) {
			$result['text'] = '<li><a href="javascript:void(null);" class="extman5_listlink">'."\n";
			$desc = $eLang->get('ALL_EXT_SYNCHRO');
		} else {
			$result['text'] = '<li class="extman5_lispec"><a href="javascript:void(null);" onclick="elx5ModalOpen(\'se\');" class="extman5_listlink">'."\n";
			$desc = 'Synchronize installed extensions';
		}

		$result['text'] .= '<div class="extman5_listicon"><i class="fas fa-sync"></i></div>'."\n";
		$result['text'] .= '<div class="extman5_listside">'."\n";
		$result['text'] .= '<h4 class="extman5_listh4">'.$eLang->get('SYNCHRONIZATION')."</h4>\n";
		$result['text'] .= '<div class="extman5_listdesc">'.$desc."</div>\n";
		$result['text'] .= '</div>'."\n";
		$result['text'] .= "</a></li>\n";

		$result['options'] = $options;

		return $result;
	}


	/***************************/
	/* SHOW INSTALLATION ERROR */
	/***************************/
	public function installError($errormsg) {
		if ($errormsg == '') { $errormsg = 'Installation failed! Unknown error.'; }
		$response = array('success' => 0, 'message' => addslashes($errormsg));

		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/******************/
	/* CONFIRM UPDATE */
	/******************/
	public function confirmUpdate($installer, $eLang) {
		$current = $installer->getCurrent();
		$head = $installer->getHead();

		$response = array(
			'success' => 0, 'confirmup' => 1, 'confirmin' => 0, 'ufolder' => $installer->getUfolder(), 'lngcinstall' => $eLang->get('CONTINUE_INSTALL'), 
			'editlink' => '', 'warnings' => array(), 'message' => ''
		);

		$x1 = $head->type.' <strong>'.$head->name.'</strong>';
		$x2 = '<strong>'.$current['version'].'</strong>';
		$x3 = '<strong>'.$head->version.'</strong>';
		$response['message'] = sprintf($eLang->get('ABOUT_TO_UPDATE'), $x1, $x2, $x3);
		$response['message'] .= '<br />';

		$warnings = $installer->getWarnings();
		if ($warnings) {
			$response['message'] .= '<strong>'.$eLang->get('SYSTEM_WARNINGS').':</strong><br />';
			foreach ($warnings as $warn) {
				$response['message'] .= '<div class="elx5_smwarning">'.$warn.'</div>';
				$response['warnings'][] = $warn;
			}
		}

		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/*******************/
	/* CONFIRM INSTALL */
	/*******************/
	public function confirmInstall($installer, $eLang) {
		$head = $installer->getHead();

		$response = array(
			'success' => 0, 'confirmup' => 0, 'confirmin' => 1, 'ufolder' => $installer->getUfolder(), 'lngcinstall' => $eLang->get('CONTINUE_INSTALL'), 
			'editlink' => '', 'warnings' => array(), 'message' => ''
		);

		$x1 = $head->type.' <strong>'.$head->name.'</strong>';
		$x2 = '<strong>'.$head->version.'</strong>';
		$response['message'] = sprintf($eLang->get('ABOUT_TO_INSTALL'), $x1, $x2);
		$response['message'] .= '<br />';

		$warnings = $installer->getWarnings();
		if ($warnings) {
			$response['message'] .= '<strong>'.$eLang->get('SYSTEM_WARNINGS').':</strong><br />';
			foreach ($warnings as $warn) {
				$response['message'] .= '<div class="elx5_smwarning">'.$warn.'</div>';
				$response['warnings'][] = $warn;
			}
		}

		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/*********************************************/
	/* SHOW INSTALLATION/SYNCHRONIZATION SUCCESS */
	/*********************************************/
	public function installSuccess($installer, $elxis, $eLang, $is_synchro=false) {
		$head = $installer->getHead();

		$lastid = $installer->getLastID();
		$editlink = '';
		if ($lastid > 0) {
			switch ($head->type) {
				case 'component': $editlink = $elxis->makeAURL('extmanager:components/edit.html').'?id='.$lastid; break;
				case 'module': $editlink = $elxis->makeAURL('extmanager:modules/edit.html').'?id='.$lastid; break;
				case 'plugin': $editlink = $elxis->makeAURL('extmanager:plugins/edit.html').'?id='.$lastid; break;
				case 'engine': $editlink = $elxis->makeAURL('extmanager:engines/edit.html').'?id='.$lastid; break;
				case 'auth': $editlink = $elxis->makeAURL('extmanager:auth/edit.html').'?id='.$lastid; break;
				case 'template': case 'atemplate': $editlink = $elxis->makeAURL('extmanager:templates/edit.html').'?id='.$lastid; break;
				default: break;
			}
		}

		$response = array(
			'success' => 1, 'confirmup' => 0, 'confirmin' => 0, 'ufolder' => '', 'lngcinstall' => $eLang->get('CONTINUE_INSTALL'), 
			'editlink' => '', 'warnings' => array(), 'message' => '', 'exttype' => '', 'extension' => '', 'version' => ''
		);

		$response['exttype'] = $head->type;
		$response['extension'] = $head->name;
		$response['version'] = $head->version;

		$x1 = $head->type.' <strong>'.$head->name.'</strong>';
		$x2 = '<strong>'.$head->version.'</strong>';
		if ($is_synchro) {
			$response['message'] = sprintf($eLang->get('EXT_SYNC_SUCCESS'), $x1, $x2);
		} else {
			$response['message'] = sprintf($eLang->get('EXT_INST_SUCCESS'), $x1, $x2);
		}
		$response['editlink'] = $editlink;
		if ($editlink != '') { $response['message'] .= ' - <a href="'.$editlink.'">'.$eLang->get('EDIT').'</a>'; }
		$response['message'] .= '<br />';

		$warnings = $installer->getWarnings();
		if ($warnings) {
			$response['message'] .= '<strong>'.$eLang->get('SYSTEM_WARNINGS').':</strong><br />';
			foreach ($warnings as $warn) {
				$response['message'] .= '<div class="elx5_smwarning">'.$warn.'</div>';
				$response['warnings'][] = $warn;
			}
		}

		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/**************************/
	/* VIEW AVAILABLE UPDATES */
	/**************************/
	public function updates($extensions, $elxis_releases, $errormsg, $elxisid, $edcauth, $dbupdated, $elxis, $eLang) {
		$eDate = eFactory::getDate();

		$htmlHelper = $elxis->obj('html');

		echo '<h1>'.$eLang->get('CHECK_UPDATES')."</h1>\n";

		if ($errormsg != '') {
			echo '<div class="elx5_error">'.$errormsg."</div>\n";
		}

		$elxis_idate = $elxis->fromVersion('RELDATE');
		$elxis_ilongversion = 'Elxis '.$elxis->getVersion().' '.$elxis->fromVersion('STATUS').' ['.$elxis->fromVersion('CODENAME').'] rev'.$elxis->fromVersion('REVISION');

		$current = trim($elxis_releases['current']);

		echo '<h2><i class="felxis-logo"></i> Elxis</h2>'."\n";

		if ($elxis_releases['error'] != '') {
			echo '<div class="elx5_error">'.$elxis_releases['error']."</div>\n";
		}

		echo '<div class="elx5_box elx5_border_blue elx5_dlspace">'."\n";
		echo '<div class="elx5_box_body">'."\n";
		echo '<div class="elx5_dataactions elx5_spad">'."\n";
		echo '<h3 class="elx5_box_title">'.$eLang->get('INSTALLED_VERSION')."</h3>\n";
		echo "</div>\n";
		echo '<table id="ielxistbl" class="elx5_datatable">'."\n";
		echo "<thead>\n";
		echo "<tr>\n";
		echo $htmlHelper->tableHead($eLang->get('VERSION'), 'elx5_nosorting elx5_center extman5_tdversion');
		echo $htmlHelper->tableHead($eLang->get('DATE'), 'elx5_nosorting');
		echo $htmlHelper->tableHead($eLang->get('NAME'), 'elx5_nosorting elx5_lmobhide');
		echo "</tr>\n";
		echo "</thead>\n";
		echo "<tbody>\n";
		$ielxis_note = '';
		$updelxis_prompt = '';
		if ($current != '') {
			$updelxislink = $elxis->makeAURL('extmanager:install/upelxis', 'inner.php');
			if ($current > $elxis->getVersion()) {
				$vertxt = '<span class="extman5_oldversion" title="There is a newer Elxis version ('.$elxis_releases['current'].') available!">'.$elxis->getVersion().'</span>';
				$updelxis_prompt = '<a href="'.$updelxislink.'" class="elx5_smbtn elx5_sucbtn">Update to Elxis '.$elxis_releases['current'].' '.$elxis_releases['rows'][$current]['codename'].' rev'.$elxis_releases['rows'][$current]['revision'].'</a>';
			} else if ($current < $elxis->getVersion()) {
				$vertxt = '<span class="extman5_devversion" title="You have a newer - possible under development- Elxis version installed.">'.$elxis->getVersion().'</span>';
			} else {
				$vertxt = '<span class="extman5_curversion" title="You have the latest Elxis version installed.">'.$elxis->getVersion().'</span>';
				if (isset($elxis_releases['rows'][$current])) {
					$irev = $elxis->fromVersion('REVISION');
					if ($elxis_releases['rows'][$current]['revision'] > $irev) {
						$ielxis_note = 'There is an updated release (rev'.$elxis_releases['rows'][$current]['revision'].') of the Elxis version you have installed (rev'.$irev.'). You might consider update.';
						$updelxis_prompt = '<a href="'.$updelxislink.'" class="elx5_smbtn elx5_sucbtn">Update to Elxis '.$elxis_releases['current'].' '.$elxis_releases['rows'][$current]['codename'].' rev'.$elxis_releases['rows'][$current]['revision'].'</a>';
					}
				}
			}
		} else {
			$vertxt = '<span class="extman5_version" title="Elxis could not determine if you have the latest Elxis version installed.">'.$elxis->getVersion().'</span>';
		}
		echo "<tr>\n";
		echo '<td class="elx5_center extman5_tdversion">'.$vertxt."</td>\n";
		echo '<td>'.$eDate->formatDate($elxis_idate, $eLang->get('DATE_FORMAT_5')).'</td>'."\n";
		echo '<td class="elx5_lmobhide">'.$elxis_ilongversion."</td>\n";
		echo "</tr>\n";
		if (!$dbupdated) {
			$txt = sprintf($eLang->get('DB_NEEDSUP'), '<strong>'.$elxis->getVersion().'</strong>');
			$updlink = $elxis->makeAURL('extmanager:install/updatedb.html', 'inner.php');
			echo '<tr class="elx5_rowerror"><td class="elx5_center" colspan="3">'.$txt.'<br /><a href="'.$updlink.'" class="elx5_bold">'.$eLang->get('UPDATE').'</a></td></tr>'."\n";
		}
		if ($ielxis_note != '') { echo '<tr class="elx5_rowerror"><td class="elx5_center" colspan="3">'.$ielxis_note.'</td></tr>'."\n"; }
		if ($updelxis_prompt != '') { echo '<tr><td class="elx5_center" colspan="3">'.$updelxis_prompt.'</td></tr>'."\n"; }
		echo "</tbody>\n";
		echo "</table>\n";

		echo '<div class="elx5_dataactions elx5_spad">'."\n";
		echo '<h3 class="elx5_box_title">elxis.org'."</h3>\n";
		echo "</div>\n";

		echo '<table id="elxisorgtbl" class="elx5_datatable">'."\n";
		echo "<thead>\n";
		echo "<tr>\n";
		echo $htmlHelper->tableHead($eLang->get('VERSION'), 'elx5_nosorting elx5_center extman5_tdversion');
		echo $htmlHelper->tableHead($eLang->get('DATE'), 'elx5_nosorting');
		echo $htmlHelper->tableHead($eLang->get('NAME'), 'elx5_nosorting elx5_lmobhide');
		echo "</tr>\n";
		echo "</thead>\n";
		echo "<tbody>\n";
		if ($elxis_releases['rows']) {
			foreach ($elxis_releases['rows'] as $version => $release) {
				$vclass = 'extman5_version';
				if (($current != '') && ($current == $version)) { $vclass = 'extman5_curversion'; }
				if ($release['status'] != 'Stable') {
					$longversion = 'Elxis '.$release['version'].' <span class="elx5_red">'.$release['status'].'</span> ['.$release['codename'].'] rev'.$release['revision'];
				} else {
					$longversion = 'Elxis '.$release['version'].' '.$release['status'].' ['.$release['codename'].'] rev'.$release['revision'];
				}
				if ($release['link'] != '') {
					$longversion = '<a href="'.$release['link'].'" target="_blank" title="Elxis '.$release['version'].' release details">'.$longversion.'</a>';
				}
				echo "<tr>\n";
				echo '<td class="elx5_center extman5_tdversion"><span class="'.$vclass.'">'.$release['version']."</span></td>\n";
				echo '<td>'.$eDate->formatDate($release['reldate'], $eLang->get('DATE_FORMAT_5')).'</td>'."\n";
				echo '<td class="elx5_lmobhide">'.$longversion."</td>\n";
				echo "</tr>\n";
			}
		} else {
			echo '<tr class="elx5_rowwarn"><td class="elx5_center" colspan="3">Could not load data for Elxis releases</td></tr>'."\n";
		}
		echo "</tbody>\n";
		echo "</table>\n";

		echo "</div>\n";//elx5_box_body
		echo "</div>\n";//elx5_box

		echo '<h2><i class="fas fa-cubes"></i> '.$eLang->get('EXTENSIONS')."</h2>\n";

		if ($errormsg != '') {
			echo '<div class="elx5_error">'.$errormsg."</div>\n";
		}

		echo '<div class="elx5_box elx5_border_red elx5_dlspace">'."\n";
		echo '<div class="elx5_box_body">'."\n";
		echo '<div class="elx5_dataactions elx5_spad">'."\n";
		echo '<h3 class="elx5_box_title">'.$eLang->get('NEEDS_UPDATE')."</h3>\n";
		echo "</div>\n";

		echo '<table id="extnotupdatedtbl" class="elx5_datatable">'."\n";
		echo "<thead>\n";
		echo "<tr>\n";
		echo $htmlHelper->tableHead('&#160;', 'elx5_nosorting elx5_lmobhide elx5_center');
		echo $htmlHelper->tableHead($eLang->get('EXTENSION'), 'elx5_nosorting');
		echo $htmlHelper->tableHead($eLang->get('INSTALLED_VERSION'), 'elx5_nosorting', 'colspan="2"');
		echo $htmlHelper->tableHead($eLang->get('VERSION').' EDC', 'elx5_nosorting elx5_lmobhide', 'colspan="2"');
		echo $htmlHelper->tableHead($eLang->get('AUTHOR'), 'elx5_nosorting elx5_tabhide');
		echo $htmlHelper->tableHead($eLang->get('COMPATIBILITY'), 'elx5_nosorting elx5_tabhide');
		echo "</tr>\n";
		echo "</thead>\n";

		echo "<tbody>\n";
		$extfound = false;
		if ($extensions) {
			foreach ($extensions as $ext) {
				$is_old = false;
				if (($ext['inst_version'] != '') && ($ext['version'] != '')) {
					if ($ext['inst_version'] < $ext['version']) { $is_old = true; }
				}
				if (!$is_old) { continue; }

				$extfound = true;
				$has_update = false;
				if (intval($ext['modified']) > 0) {
					$dt = $eDate->formatTS($ext['modified'], $eLang->get('DATE_FORMAT_3'));
				} else if (intval($ext['created']) > 0) {
					$dt = $eDate->formatTS($ext['created'], $eLang->get('DATE_FORMAT_3'));
				} else {
					$dt = '';
				}
				$dt_inst = '';
				if (trim($ext['inst_date']) != '') {
					$dt_inst = $eDate->formatDate($ext['inst_date'], $eLang->get('DATE_FORMAT_3'));
				}
				$inst_version = '<span class="extman5_version">'.$ext['inst_version'].'</span>';
				if (($ext['inst_version'] != '') && ($ext['version'] != '')) {
					if ($ext['inst_version'] > $ext['version']) {
						$inst_version = '<span class="extman5_devversion" title="'.$eLang->get('UPDATED').' - Development or private version">'.$ext['inst_version'].'</span>';
					} else if ($ext['inst_version'] == $ext['version']) {
						$inst_version = '<span class="extman5_curversion" title="'.$eLang->get('UPDATED').'">'.$ext['inst_version'].'</span>';
					} else {
						if ($ext['pcode'] != '') { $has_update = true; }
						$inst_version = '<span class="extman5_oldversion" title="'.$eLang->get('NEEDS_UPDATE').'">'.$ext['inst_version'].'</span>';
					}
				}
				$compatibility = '';
				if ($ext['compatibility'] != '') {
					if (strpos($ext['compatibility'], '4') === 0) {
						$compatibility = 'Elxis '.$ext['compatibility'];
					} else if (strpos($ext['compatibility'], '5') === 0) {
						$compatibility = 'Elxis '.$ext['compatibility'];
					} else {
						$compatibility = $ext['compatibility'];
					}
				}

				switch ($ext['type']) {
					case 'component': $exttype = '<i class="fas fa-cube" title="'.$ext['type'].'"></i>'; break;
					case 'module': $exttype = '<i class="fas fa-puzzle-piece" title="'.$ext['type'].'"></i>'; break;
					case 'plugin': $exttype = '<i class="fas fa-plug" title="'.$ext['type'].'"></i>'; break;
					case 'template': $exttype = '<i class="fas fa-paint-brush" title="'.$ext['type'].'"></i>'; break;
					case 'engine': $exttype = '<i class="fas fa-search" title="'.$ext['type'].'"></i>'; break;
					case 'auth': $exttype = '<i class="fas fa-key" title="'.$ext['type'].'"></i>'; break;
					case 'core': $exttype = '<span title="'.$ext['type'].'">e</span>'; break;
					default: $exttype = '<span title="'.$ext['type'].'">?</span>'; break;
				}

				echo '<tr>'."\n";
				echo '<td class="elx5_center elx5_lmobhide">'.$exttype.'</td>'."\n";
				echo '<td>';
				if ($ext['edclink'] != '') {
					echo '<a href="'.$ext['edclink'].'" title="'.$ext['title'].' on EDC" target="_blank">'.$ext['title'].'</a>';
				} else {
					echo $ext['title'];
				}
				if ($has_update) {
					echo '<a href="javascript:void(null);" class="extman5_updatelink" onclick="extMan5UpdateExtension(\''.$ext['pcode'].'\');" title="'.$eLang->get('UPDATE').' '.$ext['title'].'">'.$eLang->get('UPDATE').'</a>'."\n";
				}
				echo '</td>'."\n";
				echo '<td class="elx5_center">'.$inst_version."</td>\n";
				echo '<td>'.$dt_inst."</td>\n";
				echo '<td class="elx5_center elx5_lmobhide"><span class="extman5_version">'.$ext['version'].'</span></td>'."\n";
				echo '<td class="elx5_lmobhide">'.$dt."</td>\n";
				echo '<td class="elx5_tabhide">'.$ext['author'].'</td>'."\n";
				echo '<td class="elx5_tabhide">'.$compatibility."</td>\n";
				echo "</tr>\n";
			}
		}
		if (!$extfound) {
			echo '<tr class="elx5_rowwarn"><td class="elx5_center" colspan="8">'.$eLang->get('NO_EXTS_FOUND')."</td></tr>\n";
		}
		echo "</tbody>\n";
		echo "</table>\n";

		echo "</div>\n";//elx5_box_body
		echo "</div>\n";//elx5_box

		echo '<div class="elx5_box elx5_border_green elx5_dlspace">'."\n";
		echo '<div class="elx5_box_body">'."\n";
		echo '<div class="elx5_dataactions elx5_spad">'."\n";
		echo '<h3 class="elx5_box_title">'.$eLang->get('UPDATED')."</h3>\n";
		echo "</div>\n";

		echo '<table id="extupdatedtbl" class="elx5_datatable">'."\n";
		echo "<thead>\n";
		echo "<tr>\n";
		echo $htmlHelper->tableHead('&#160;', 'elx5_nosorting elx5_lmobhide elx5_center');
		echo $htmlHelper->tableHead($eLang->get('EXTENSION'), 'elx5_nosorting');
		echo $htmlHelper->tableHead($eLang->get('INSTALLED_VERSION'), 'elx5_nosorting', 'colspan="2"');
		echo $htmlHelper->tableHead($eLang->get('VERSION').' EDC', 'elx5_nosorting elx5_lmobhide', 'colspan="2"');
		echo $htmlHelper->tableHead($eLang->get('AUTHOR'), 'elx5_nosorting elx5_tabhide');
		echo $htmlHelper->tableHead($eLang->get('COMPATIBILITY'), 'elx5_nosorting elx5_tabhide');
		echo "</tr>\n";
		echo "</thead>\n";

		echo "<tbody>\n";

		$extfound = false;
		if ($extensions) {
			foreach ($extensions as $ext) {
				$is_old = false;
				if (($ext['inst_version'] != '') && ($ext['version'] != '')) {
					if ($ext['inst_version'] < $ext['version']) { $is_old = true; }
				}
				if ($is_old) { continue; }

				$extfound = true;
				if (intval($ext['modified']) > 0) {
					$dt = $eDate->formatTS($ext['modified'], $eLang->get('DATE_FORMAT_3'));
				} else if (intval($ext['created']) > 0) {
					$dt = $eDate->formatTS($ext['created'], $eLang->get('DATE_FORMAT_3'));
				} else {
					$dt = '';
				}
				$dt_inst = '';
				if (trim($ext['inst_date']) != '') {
					$dt_inst = $eDate->formatDate($ext['inst_date'], $eLang->get('DATE_FORMAT_3'));
				}
				$inst_version = '<span class="extman5_version">'.$ext['inst_version'].'</span>';
				if (($ext['inst_version'] != '') && ($ext['version'] != '')) {
					if ($ext['inst_version'] > $ext['version']) {
						$inst_version = '<span class="extman5_devversion" title="'.$eLang->get('UPDATED').' - Development or private version">'.$ext['inst_version'].'</span>';
					} else if ($ext['inst_version'] == $ext['version']) {
						$inst_version = '<span class="extman5_curversion" title="'.$eLang->get('UPDATED').'">'.$ext['inst_version'].'</span>';
					} else {
						$inst_version = '<span class="extman5_oldversion" title="'.$eLang->get('NEEDS_UPDATE').'">'.$ext['inst_version'].'</span>';
					}
				}

				$compatibility = '';
				if ($ext['compatibility'] != '') {
					if (strpos($ext['compatibility'], '4') === 0) {
						$compatibility = 'Elxis '.$ext['compatibility'];
					} else if (strpos($ext['compatibility'], '5') === 0) {
						$compatibility = 'Elxis '.$ext['compatibility'];
					} else {
						$compatibility = $ext['compatibility'];
					}
				}

				switch ($ext['type']) {
					case 'component': $exttype = '<i class="fas fa-cube" title="'.$ext['type'].'"></i>'; break;
					case 'module': $exttype = '<i class="fas fa-puzzle-piece" title="'.$ext['type'].'"></i>'; break;
					case 'plugin': $exttype = '<i class="fas fa-plug" title="'.$ext['type'].'"></i>'; break;
					case 'template': $exttype = '<i class="fas fa-paint-brush" title="'.$ext['type'].'"></i>'; break;
					case 'engine': $exttype = '<i class="fas fa-search" title="'.$ext['type'].'"></i>'; break;
					case 'auth': $exttype = '<i class="fas fa-key" title="'.$ext['type'].'"></i>'; break;
					case 'core': $exttype = '<span title="'.$ext['type'].'">e</span>'; break;
					default: $exttype = '<span title="'.$ext['type'].'">?</span>'; break;
				}

				echo '<tr>'."\n";
				echo '<td class="elx5_center elx5_lmobhide">'.$exttype.'</td>'."\n";
				echo '<td>';
				if ($ext['edclink'] != '') {
					echo '<a href="'.$ext['edclink'].'" title="'.$ext['title'].' on EDC" target="_blank">'.$ext['title'].'</a>';
				} else {
					echo $ext['title'];
				}
				echo '</td>'."\n";
				echo '<td class="elx5_center">'.$inst_version."</td>\n";
				echo '<td>'.$dt_inst."</td>\n";
				echo '<td class="elx5_center elx5_lmobhide"><span class="extman5_version">'.$ext['version'].'</span></td>'."\n";
				echo '<td class="elx5_lmobhide">'.$dt."</td>\n";
				echo '<td class="elx5_tabhide">'.$ext['author'].'</td>'."\n";
				echo '<td class="elx5_tabhide">'.$compatibility."</td>\n";
				echo "</tr>\n";
			}
		}
		if (!$extfound) {
			echo '<tr class="elx5_rowwarn"><td class="elx5_center" colspan="8">'.$eLang->get('NO_EXTS_FOUND')."</td></tr>\n";
		}
		echo "</tbody>\n";
		echo "</table>\n";

		echo "</div>\n";//elx5_box_body
		echo "</div>\n";//elx5_box

		echo '<div id="extmanbase" class="elx5_invisible" dir="ltr">'.$elxis->makeAURL('extmanager:/', 'inner.php')."</div>\n";
		echo '<div id="elxisid" class="elx5_invisible" dir="ltr">'.$elxisid."</div>\n";
		echo '<div id="edcauth" class="elx5_invisible" dir="ltr">'.$edcauth."</div>\n";
	}


	/*************************/
	/* FILESYSTEM CHECK HTML */
	/*************************/
	public function checkFilesystemHTML($data, $results, $elxis, $eLang, $prompt_upelxis=false) {
		$dolink = $elxis->makeAURL('extmanager:install/checkfs.html').'?do=1';
		$updelxislink = $elxis->makeAURL('extmanager:install/upelxis', 'inner.php');

		$htmlHelper = $elxis->obj('html');

		echo '<h2>'.$eLang->get('CHECK_FS').' <span>Elxis '.$elxis->getVersion()."</span></h2>\n";

		if ($data['do'] == 0) {
			echo '<p class="elx5_help">File-system check makes sure your Elxis installation is updated and all files authentic. 
			If there are missing or modified files either perform an <a href="'.$updelxislink.'">Elxis update</a> or <a href="https://www.elxis.org/download.html">download Elxis '.$data['iversion'].'</a> 
			from elxis.org take the original files from Elxis zip package and update your site. File-system check is performed 
			on Elxis core files, not on third party installed extensions.</p>'."\n";
		}

		if ($data['infos']) {
			echo '<p class="elx5_info">';
			foreach ($data['infos'] as $txt) { echo $txt."<br />\n"; }
			echo "</p>\n";
		}
		if ($data['warnings']) {
			echo '<p class="elx5_warning">';
			foreach ($data['warnings'] as $txt) { echo $txt."<br />\n"; }
			echo "</p>\n";
		}

		if ($prompt_upelxis) {
			echo '<div class="elx5_dlspace"><a href="'.$updelxislink.'" class="elx5_btn elx5_ibtn elx5_errorbtn">Click to update Elxis!</a></div>';
		}

		$boxclass = $results ? 'elx5_border_red' : 'elx5_border_blue';

		echo '<div class="elx5_box '.$boxclass.'">'."\n";
		echo '<div class="elx5_box_body">'."\n";
		echo '<div class="elx5_dataactions">'."\n";
		if ($data['cando'] == 1) {
			$title = ($data['do'] == 1) ? $eLang->get('CHECK_AGAIN') : $eLang->get('CHECK_FS');
			echo '<a href="'.$dolink.'" class="elx5_dataaction elx5_datahighlight" data-alwaysactive="1" title="'.$eLang->get('CLICK_BEGIN_FSCHECK').'">'.$title."</a>\n";
			if ($results) {
				echo '<a href="'.$updelxislink.'" class="elx5_dataaction elx5_datawarn" data-alwaysactive="1" title="Update Elxis to latest version">Update Elxis</a>'."\n";
			}
		}
		echo "</div>\n";

		echo '<table id="fschecktbl" class="elx5_datatable">'."\n";
		echo "<thead>\n";
		echo "<tr>\n";
		echo $htmlHelper->tableHead('#', 'elx5_nosorting elx5_center');
		echo $htmlHelper->tableHead($eLang->get('FILE'), 'elx5_nosorting');
		echo $htmlHelper->tableHead('&#160;', 'elx5_nosorting elx5_center elx5_lmobhide');
		echo $htmlHelper->tableHead($eLang->get('STATUS'), 'elx5_nosorting elx5_tabhide');
		echo "</tr>\n";
		echo "</thead>\n";

		echo "<tbody>\n";
		if ($results) {
			$i = 1;
			foreach ($results as $res) {
				echo '<tr>'."\n";
				echo '<td class="elx5_center">'.$i."</td>\n";
				echo '<td>'.$res[0]."</td>\n";
				if ($res[1] == 'notfound') {
					echo '<td class="elx5_center elx5_lmobhide"><a href="javascript:void(null);" class="elx5_statusicon elx5_statusunpub" title="'.$eLang->get('NOT_FOUND').'"></a></td>'."\n";
					echo '<td class="elx5_tabhide">'.$eLang->get('NOT_FOUND').'</td>'."\n";
				} else {
					echo '<td class="elx5_center elx5_lmobhide"><a href="javascript:void(null);" class="elx5_statusicon elx5_statuswarn" title="'.$eLang->get('NEEDS_UPDATE').'"></a></td>'."\n";
					echo '<td class="elx5_tabhide">'.$eLang->get('NEEDS_UPDATE').'</td>'."\n";
				}
				echo "</tr>\n";
				$i++;
			}
		} else if ($data['do'] == 1) {
			echo '<tr class="elx5_rowspecial"><td class="elx5_center" colspan="4">'.$eLang->get('FSCHECK_OK')."</td></tr>\n";
		} else {
			echo '<tr><td class="elx5_center" colspan="4">-'."</td></tr>\n";
		}
		echo "</tbody>\n";
		echo "</table>\n";

		echo "</div>\n";//elx5_box_body
		if ($results) {
			$t = count($results);
			echo $htmlHelper->tableSummary('', 1, 1, $t);
		}
		echo "</div>\n";//elx5_box

		if ($results) {
			if ($data['cando'] == 1) {
				echo '<div class="elx5_vpad"><a href="'.$dolink.'" class="elx5_btn">'.$eLang->get('CHECK_AGAIN')."</a></div>\n";
			}
		}

	}


	/*********************/
	/* UPDATE ELXIS HTML */
	/*********************/
	public function updateElxisHTML($nextstep, $nexttitle, $errormsg='', $inputs=array()) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$updelxislink = $elxis->makeAURL('extmanager:install/upelxis', 'inner.php');
		$cancellink = $elxis->makeAURL('extmanager:/');

		echo '<div class="extman5_upelx_wrap">'."\n";
		if ($errormsg != '') {
			echo '<div class="elx5_dlspace elx5_error elx5_center">'.$errormsg."</div>\n";
		} else if ($nextstep == 1) {
			echo '<div class="extman5_upelx_note">To begin Elxis update click the button below and wait.<br />During update do not refresh the page!</div>'."\n";
		} else if ($nextstep > 1) {
			echo '<div class="extman5_upelx_spin"><i class="fas fa-sync fa-spin"></i></div>'."\n";
			echo '<div class="extman5_upelx_note">'.$eLang->get('PLEASE_WAIT').'</div>'."\n";
		}
?>
		<form name="fmupelxis" id="fmupelxis" class="elx5_form" action="<?php echo $updelxislink; ?>" method="post">
			<input type="hidden" name="step" value="<?php echo $nextstep; ?>" />
<?php 
			if ($inputs) {
				foreach ($inputs as $k => $v) {
					echo '<input type="hidden" name="'.$k.'" value="'.$v.'" />'."\r\n";
				}
			}
?>
			<div class="elx5_vlspace elx5_center">
				<button type="submit" name="updatebtn" value="1" class="elx5_btn elx5_ibtn elx5_sucbtn"><?php echo $nextstep.'/7 : '.$nexttitle; ?></button>
			</div>
		</form>
		
<?php 
		if ($nextstep < 5) {
			echo '<div class="elx5_vlspace elx5_center"><a href="'.$cancellink.'">'.$eLang->get('CANCEL')."</div>\n";
		}
		echo "</div>\n";//extman5_upelx_wrap
	}

}

?>