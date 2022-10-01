<?php 
/**
* @version		$Id: browse.html.php 2422 2021-09-23 19:40:45Z IOS $
* @package		Elxis
* @subpackage	Component Extensions Manager
* @copyright	Copyright (c) 2006-2020 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class browseExtmanagerView extends extmanagerView {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/***********************************/
	/* EXTENSIONS BROWSER CENTRAL PAGE */
	/***********************************/
	public function extCentral($edc) {
		$this->ui($edc);
	}


	/**************************/
	/* DISPLAY USER INTERFACE */
	/**************************/
	private function ui($edc, $catid=0) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
?>

		<?php $this->edcBrowserTop($edc, $catid, $elxis, $eLang); ?>

		<div id="extman5_edcauthmsg" class="elx5_invisible"></div>
		<div id="extman5_edcmain"></div>

		<div class="extman5_edc_copyright">
			<!-- created by Ioannis Sannos (datahell) -->
			Extensions provided by <a href="https://www.elxis.org/" target="_blank" title="Elxis CMS">elxis.org</a>. 
			Visit <a href="https://forum.elxis.org/" target="_blank" title="Elxis official forum">Elxis forums</a> for support. 
			Copyright &#0169; 2006 - <?php echo date('Y'); ?> elxis.org. All rights reserved.
		</div>

		<div class="elx5_invisible" id="extman5_edcdata" data-auth="" data-elxisid="<?php echo $edc->getElxisId(); ?>"></div>
		<div class="elx5_invisible" id="extman5_edcbase" dir="ltr"><?php echo $elxis->makeAURL('extmanager:/', 'inner.php'); ?></div>
		<div class="elx5_invisible" id="extman5_edcurl" dir="ltr"><?php echo $edc->getEdcUrl(); ?></div>
<?php 
		$htmlHelper = $elxis->obj('html');
		echo $htmlHelper->startModalWindow('<i class="fas fa-download"></i> '.$eLang->get('INSTALL').'/'.$eLang->get('UPDATE'), 'edcinst');
		echo '<div id="extman5_edc_imessage" class="elx5_center"></div>'."\n";
		echo '<div id="extman5_edc_iresponse" class="elx5_invisible"></div>'."\n";
		echo $htmlHelper->endModalWindow();
	}


	/*********************************************/
	/* MENU OF EXTENSION CATEGORIES AND ELXIS ID */
	/*********************************************/
	private function edcBrowserTop($edc, $curcatid, $elxis, $eLang) {
		$categories = $edc->getCategories();

		echo '<div class="extman5_edc_top">'."\n";
		echo '<a href="javascript:void(null);" class="extman5_edc_elxid" title="'.$eLang->get('SEARCH').'" onclick="elx5ModalMessageHide(\'edcsm\'); elx5ModalOpen(\'edcsm\');">'."\n";
		echo '<i class="fas fa-search"></i>';
		echo "</a>\n";
		echo '<div class="extman5_edc_topmain">'."\n";
		echo '<a href="javascript:void(null);" onclick="extMan5EDCFrontpage();" title="'.$eLang->get('HOME').'" class="extman5_edc_topbtn"><i class="fas fa-home"></i></a>'."\n";
		echo '<div class="extman5_edc_ctgbox">'."\n";
		echo '<select class="extman5_edc_ctgsel" id="extman5_edc_ctgsel" onchange="extMan5EDCSwitchCtg();">'."\n";
		$sel = ($curcatid == 0) ? ' selected="selected"' : '';
		echo '<option value="0"'.$sel.'>'.$eLang->get('HOME')."</option>\n";
		if ($categories) {
			foreach ($categories as $catid => $category) {
				$sel = ($curcatid == $catid) ? ' selected="selected"' : '';
				echo '<option value="'.$catid.'"'.$sel.'>'.$category."</option>\n";
			}			
		}
		echo "</select>\n";
		echo '<div class="extman5_edc_selarrow"></div>'."\n";
		echo "</div>\n";
		echo "</div>\n";//extman5_edc_topmain
		echo "</div>\n";//extman5_edc_top

		elxisLoader::loadFile('includes/libraries/elxis/form5.class.php');
		$inlink = $elxis->makeAURL('extmanager:/', 'inner.php');
		$htmlHelper = $elxis->obj('html');
		echo $htmlHelper->startModalWindow('<i class="fas fa-search"></i> '.$eLang->get('SEARCH_EXTENSIONS'), 'edcsm');
		$form = new elxis5Form(array('idprefix' => 'edcsf'));
		$form->openForm(array('name' => 'fmedcsearch', 'method' =>'post', 'action' => $inlink.'search', 'id' => 'fmedcsearch', 'autocomplete' => 'off', 'onsubmit' => 'return extman5EDCSearchSubmit();'));
		$form->addText('keyword', '', $eLang->get('KEYWORD'), array('required' => 'required', 'dir' => 'ltr'));
		$form->addHTML('<div class="elx5_vpad">');
		$form->addButton('search', $eLang->get('SEARCH'), 'button', array('class' => 'elx5_btn elx5_sucbtn', 'onclick' => 'extman5EDCSearch();'));
		$form->addHTML('</div>');
		$form->closeForm();
		echo $htmlHelper->endModalWindow();
	}


	/******************************/
	/* DISPLAY CONNECTION RESULTS */
	/******************************/
	public function connectionResult($response) {
		if ($response['error'] != '') {
			$json = array('error' => 1, 'errormsg' => addslashes($response['error']), 'edcauth' => $response['edcauth']);
		} else {
			$json = array('error' => 0, 'errormsg' => '', 'edcauth' => $response['edcauth']);
		}

		$this->ajaxHeaders('application/json');
		echo json_encode($json);
		exit;
	}



	/*********************************/
	/* DISPLAY CATEGORY'S EXTENSIONS */
	/*********************************/
	public function showCategory($response, $edc) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		if ($response['error'] != '') {
			$this->ajaxHeaders();
			echo '<div class="elx5_warning">'.$response['error']."</div>\n";
			exit;
		}
		
		if (($response['total'] == 0) || (count($response['rows']) == 0)) {
			$this->ajaxHeaders();
			echo '<p class="elx5_info">'.$eLang->get('NO_EXTS_FOUND')."</p>\n";
			exit;
		}

		$is_ssl = eFactory::getURI()->detectSSL();
		$perms = $edc->permissions();

		$this->ajaxHeaders('text/plain');
		if ($response['total'] == 1) {
			echo '<p class="elx5_sminfo elx5_dspace">'.$eLang->get('EXTENSION_FOUND')."</p>\n";
		} else {
			$txt = sprintf($eLang->get('EXTENSIONS_FOUND'), '<strong>'.$response['total'].'</strong>');
			if ($response['maxpage'] > 1) {
				$txt .= ' '.sprintf($eLang->get('PAGEOF'), '<strong>'.$response['page'].'</strong>', '<strong>'.$response['maxpage'].'</strong>');
			}
			echo '<p class="elx5_sminfo elx5_dspace">'.$txt."</p>\n";
		}

		echo '<div class="extman5_extensions">'."\n";
		foreach ($response['rows'] as $row) {
			$actions = $edc->extActions($row, $perms);
			$this->extensionBox($row, $is_ssl, $response['ordering'], $actions, false, $edc, $elxis, $eLang);
		}
		echo "</div>\n";

		if ($response['maxpage'] > 1) {
			$row = $response['rows'][0];
			$txt = sprintf($eLang->get('PAGEOF'), $response['page'], $response['maxpage']);
			echo '<div class="elx5_row elx5_vpad">'."\n";
			echo '<div class="elx5_datainfo">'.$txt."</div>\n";
			echo '<div class="elx5_datapagination">'."\n";
			echo '<ul class="elx5_pagination">'."\n";
			for ($i=1; $i <= $response['maxpage']; $i++) {
				if ($i == $response['page']) {
					echo '<li class="elx5_pagactive"><a href="javascript:void(null);" title="'.$eLang->get('PAGE').' '.$i.'">'.$i.'</a></li>'."\n";
				} else {
					echo '<li><a href="javascript:void(null);" onclick="extMan5EDCLoadCategory('.$row['catid'].', '.$i.');" title="'.$eLang->get('PAGE').' '.$i.'">'.$i.'</a></li>'."\n";
				}
			}
			echo "</ul>\n";
			echo "</div>\n";
			echo "</div>\n";
			unset($row);
		}

		exit;
	}


	/*******************************/
	/* DISPLAY EXTENSION'S DETAILS */
	/*******************************/
	public function showExtension($response, $edc) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		if ($response['error'] != '') {
			$this->ajaxHeaders();
			echo '<div class="elx5_warning">'.$response['error']."</div>\n";
			exit;
		}

		$row = $response['row'];
		$is_ssl = eFactory::getURI()->detectSSL();
		$perms = $edc->permissions();
		$actions = $edc->extActions($row, $perms);

		$this->ajaxHeaders('text/plain');

		$this->extensionBox($row, $is_ssl, 'c', $actions, true, $edc, $elxis, $eLang);
		$this->extensionMore($row, $is_ssl, $edc, $elxis, $eLang);
		exit;
	}


	/***************************/
	/* DISPLAY EDC'S FRONTPAGE */
	/***************************/
	public function showFrontpage($response, $edc) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		if ($response['error'] != '') {
			$this->ajaxHeaders();
			echo '<div class="elx5_warning">'.$response['error']."</div>\n";
			exit;
		}
		
		if (count($response['blocks']) == 0) {
			$this->ajaxHeaders();
			echo '<p class="elx5_info">Nothing to display!'."</p>\n";
			exit;
		}

		$is_ssl = eFactory::getURI()->detectSSL();
		$perms = $edc->permissions();

		$this->ajaxHeaders();
		foreach ($response['blocks'] as $block) {
			if (in_array($block['type'], array('latest', 'popular', 'featured', 'lastupdated'))) {
				switch ($block['type']) {
					case 'latest': $block_title = $eLang->get('LATEST_LISTINGS'); break;
					case 'lastupdated': $block_title = $eLang->get('NEW_UPDATED_EXTS'); break;
					case 'featured': $block_title = $eLang->get('SUGGESTED'); break;
					case 'popular': default: $block_title = $eLang->get('POPULAR_LISTINGS'); break;
				}

				$ids = explode(',',$block['contents']);
				if (!$ids) { continue; }
				echo '<h3>'.$block_title."</h3>\n";
				echo '<div class="extman5_extensions">'."\n";
				foreach ($ids as $id) {
					$x = (int)$id;
					if (isset($response['rows'][$x])) {
						$actions = $edc->extActions($response['rows'][$x], $perms);
						$this->extensionBox($response['rows'][$x], $is_ssl, 'c', $actions, false, $edc, $elxis, $eLang);
					}
				}
				echo "</div>\n";
				continue;
			}

			if ($block['type'] == 'alert') {
				echo '<div class="elx5_warning">'.stripslashes($block['contents'])."</div>\n";
			}
			if ($block['type'] == 'advertisement') {
				echo '<div class="extman5_edcadv"><span>'.$eLang->get('ADVERTISEMENT').':</span> '.stripslashes($block['contents'])."</div>\n";
			}
			if ($block['type'] == 'notice') {
				echo '<div class="extman5_edcnotice">'.stripslashes($block['contents'])."</div>\n";
			}
			if ($block['type'] == 'announcement') {
				echo '<div class="extman5_edcannouncement">'.stripslashes($block['contents'])."</div>\n";
			}
			if ($block['type'] == 'message') {
				echo '<div class="extman5_edcmsgcontents">'.stripslashes($block['contents'])."</div>\n";
			}
		}
		exit;
	}


	/****************************/
	/* SHOW AUTHOR'S EXTENSIONS */
	/****************************/
	public function showAuthorExtensions($response, $edc) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$eDate = eFactory::getDate();

		if ($response['error'] != '') {
			$this->ajaxHeaders();
			echo '<div class="elx5_warning">'.$response['error']."</div>\n";
			exit;
		}
		
		if (count($response['author']) == 0) {
			$this->ajaxHeaders();
			echo '<div class="elx5_warning">Invalid author!</div>'."\n";
			exit;
		}

		$total = count($response['rows']);
		$is_ssl = eFactory::getURI()->detectSSL();
		$total_downloads = 0;

		$img = $elxis->secureBase().'/components/com_user/images/noavatar.png';
		if ($total > 0) {
			if (isset($response['rows'][0])) {
				if ($response['rows'][0]['author']['avatar'] != '') {
					if (($is_ssl == true) && !preg_match('#^(https\:\/\/)#i', $response['rows'][0]['author']['avatar'])) {//prevent breaking SSL with no SSL images
						$img = $elxis->secureBase().'/components/com_user/images/noavatar.png';
					} else {
						$img = $response['rows'][0]['author']['avatar'];
					}
				}
			}
		}

		if ($response['author']['city'] != '') {
			$location = $response['author']['city'];
			if ($response['author']['country'] != '') {
				$location .= ', '.$response['author']['country'];
			}
		} else if ($response['author']['country'] != '') {
			$location = $response['author']['country'];
		} else {
			$location = '';
		}

		if ($total > 0) {
			foreach ($response['rows'] as $row) { $total_downloads += $row['downloads']; }
		}

		$this->ajaxHeaders('text/plain');

		echo '<div class="extman5_edc_authbox">'."\n";
		echo '<div class="extman5_edc_authimg">'."\n";
		if ($response['author']['website'] != '') {
			echo '<a href="'.$response['author']['website'].'" title="'.$eLang->get('VISIT_AUTHOR_SITE').'" target="_blank"><img src="'.$img.'" alt="icon" /></a>'."\n";
		} else {
			echo '<img src="'.$img.'" alt="icon" />'."\n";
		}
		echo "</div>\n";//extman5_edc_authimg
		echo '<div class="extman5_edc_authmain">'."\n";
		echo '<div class="extman5_edc_authrow"><strong>'.$response['author']['name']."</strong></div>\n";
		echo '<div class="extman5_edc_authrow" title="'.$eLang->get('LOCATION').'">'.$location."</div>\n";
		echo '<div class="extman5_edc_authrow">'.$eLang->get('EXTENSIONS').' <strong>'.$total.'</strong> | '.$eLang->get('DOWNLOADS').' <strong>'.$total_downloads."</strong></div>\n";
		if ($response['author']['website'] != '') {
			echo '<div class="extman5_edc_authrow">';
			echo '<a href="'.$response['author']['website'].'" title="'.$eLang->get('VISIT_AUTHOR_SITE').'" target="_blank">'.$response['author']['website'].'</a>';
			echo "</div>\n";
		}

		echo "</div>\n";//extman5_edc_authmain
		echo "</div>\n";//extman5_edc_authbox

		if ($total > 0) {
			echo '<h2>'.$eLang->get('EXTENSIONS').' <span dir="ltr">('.$total.")</span></h2>\n";
			echo '<p class="elx5_sminfo elx5_dspace">'.$eLang->get('ALL_EXTENSIONS_BY').' '.$response['author']['name']."</p>\n";

			$is_ssl = eFactory::getURI()->detectSSL();
			$perms = $edc->permissions();

			echo '<div class="extman5_extensions">'."\n";
			foreach ($response['rows'] as $row) {
				$actions = $edc->extActions($row, $perms);
				$this->extensionBox($row, $is_ssl, 'c', $actions, false, $edc, $elxis, $eLang);
			}
			echo "</div>\n";
		}

		exit;
	}


	/**********************************/
	/* SHOW SEARCH EXTENSIONS RESULTS */
	/**********************************/
	public function showSearchExtensions($response, $edc) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();

		if ($response['error'] != '') {
			$this->ajaxHeaders();
			if ($response['keyword'] != '') {
				echo '<div class="elx5_vlspace"><h2>'.sprintf($eLang->get('SEARCH_RESULTS_FOR'), '<span>'.$response['keyword'].'</span>').'</h2></div>';
			}
			echo '<div class="elx5_warning">'.$response['error']."</div>\n";
			exit;
		}

		$total = count($response['rows']);
		$is_ssl = eFactory::getURI()->detectSSL();

		$this->ajaxHeaders('text/plain');

		echo '<div class="elx5_vlspace"><h2>'.sprintf($eLang->get('SEARCH_RESULTS_FOR'), '<span>'.$response['keyword'].'</span>').'</h2></div>';
		if ($total > 0) {
			$perms = $edc->permissions();
			echo '<div class="extman5_extensions">'."\n";
			foreach ($response['rows'] as $row) {
				$actions = $edc->extActions($row, $perms);
				$this->extensionBox($row, $is_ssl, 'c', $actions, false, $edc, $elxis, $eLang);
			}
			echo "</div>\n";
		} else {
			echo '<div class="elx5_warning">'.$eLang->exist('NO_EXTS_FOUND')."</div>\n";
		}
		exit;
	}


	/*******************************/
	/* GENERATE AN EXTENSION'S BOX */
	/*******************************/
	private function extensionBox($row, $is_ssl, $ordering, $actions, $showdetails, $edc, $elxis, $eLang) {
		$eDate = eFactory::getDate();

		if ($row['icon'] != '') {
			if (($is_ssl == true) && !preg_match('#^(https\:\/\/)#i', $row['icon'])) {//prevent breaking SSL with no SSL images
				$iconclass = $edc->getTypeIconClass($row['type']);
				$exticon = '<i class="'.$iconclass.'" aria-hidden="false"></i>';
			} else {
				$exticon = '<img src="'.$row['icon'].'" alt="icon" />';
			}
		} else {
			$iconclass = $edc->getTypeIconClass($row['type']);
			$exticon = '<i class="'.$iconclass.'" aria-hidden="false"></i>';
		}

		$addon = $showdetails ? ' extman5_xboxlarge' : '';
		echo '<div class="extman5_xbox'.$addon.'">'."\n";
		echo '<div class="extman5_xbox_thumb">'."\n";
		if ($showdetails) {
			echo '<a href="javascript:void(null);" title="'.$row['title'].'">'.$exticon."</a>\n";
		} else {
			echo '<a href="javascript:void(null);" onclick="extman5EDCLoadExtension('.$row['id'].', '.$row['catid'].');" title="'.$row['title'].'">'.$exticon."</a>\n";
		}
		echo "</div>\n";

		echo '<div class="extman5_xbox_main">'."\n";
		if ($showdetails) {
			if ($row['verified'] == 1) {
				echo '<div class="extman5_xbox_verified" title="'.$eLang->get('VERIFIED_EXTENSION').'"><i class="fas fa-star"></i></div>'."\n";
			}
		}
		echo '<h3 class="extman5_xbox_title">'."\n";
		if ($showdetails) {
			echo '<a href="javascript:void(null);" title="'.$row['title'].'">'.$row['title'].' <span>'.$row['version'].'</span></a>'."\n";
		} else {
			$exttype = $edc->getTypeName($row['type']);
			echo '<a href="javascript:void(null);" onclick="extman5EDCLoadExtension('.$row['id'].', '.$row['catid'].');" title="'.$row['title'].' - '.$exttype.'">'.$row['title'].' <span>'.$row['version'].'</span></a>'."\n";
		}
		echo "</h3>\n";

		if ($showdetails) {
			$iconclass = $edc->getTypeIconClass($row['type']);
			echo '<div class="extman5_xbox_xtype">'.$eLang->get('TYPE').' <span><i class="'.$iconclass.'"></i> '.$edc->getTypeName($row['type'])."</span></div>\n";
			$altcatid = $row['altcatid'];
			$altcat = '';
			if ($altcatid > 0) {
				$categories = $edc->getCategories();
				if (isset($categories[$altcatid])) { $altcat = $categories[$altcatid]; }
			}
			if ($altcat != '') {
				echo '<div class="extman5_xbox_iline2">';
				echo '<a href="javascript:void(null);" onclick="extMan5EDCLoadCategory('.$row['catid'].', 1);" title="'.$row['category'].'" class="extman5_xbox_link"><i class="fas fa-folder"></i> '.$row['category'].'</a>';
				echo '<span class="elx5_lmobhide"> | <a href="javascript:void(null);" onclick="extMan5EDCLoadCategory('.$altcatid.', 1);" title="'.$altcat.'"class="extman5_xbox_link">'.$altcat.'</a></span>';
				echo "</div>\n";
			} else {
				echo '<a href="javascript:void(null);" onclick="extMan5EDCLoadCategory('.$row['catid'].', 1)" title="'.$row['category'].'" class="extman5_xbox_ctg"><i class="fas fa-folder"></i> '.$row['category']."</a>\n";
			}
		} else {
			echo '<a href="javascript:void(null);" onclick="extMan5EDCLoadCategory('.$row['catid'].', 1)" title="'.$row['category'].'" class="extman5_xbox_ctg"><i class="fas fa-folder"></i> '.$row['category']."</a>\n";
		}

		if ($showdetails) {
			if ($row['authorlink'] != '') {
				$parsed = parse_url($row['authorlink']);
				$domain = preg_replace('@^(www\.)@', '', $parsed['host']);
				echo '<div class="extman5_xbox_iline2">';
				echo '<a href="javascript:void(null);" onclick="extMan5EDCAuthor('.$row['uid'].');" title="'.$eLang->get('ALL_EXTENSIONS_BY').' '.$row['author'].'" class="extman5_xbox_link"><i class="fas fa-user"></i> '.$row['author'].'</a>';
				echo '<span class="elx5_lmobhide"> | <a href="'.$row['authorlink'].'" title="'.$eLang->get('VISIT_AUTHOR_SITE').'" target="_blank" class="extman5_xbox_link">'.$domain.' <i class="fas fa-external-link-alt"></i></a></span>';
				echo "</div>\n";
			} else {
				echo '<a href="javascript:void(null);" onclick="extMan5EDCAuthor('.$row['uid'].')" title="'.$eLang->get('ALL_EXTENSIONS_BY').' '.$row['author'].'" class="extman5_xbox_author"><i class="fas fa-user"></i> '.$row['author']."</a>\n";
			}
		} else {
			echo '<a href="javascript:void(null);" onclick="extMan5EDCAuthor('.$row['uid'].')" title="'.$eLang->get('ALL_EXTENSIONS_BY').' '.$row['author'].'" class="extman5_xbox_author"><i class="fas fa-user"></i> '.$row['author']."</a>\n";
		}

		if ($row['short'] != '') {
			echo '<div class="extman5_xbox_info">'.$row['short']."</div>\n";
		} else {
			echo '<div class="extman5_xbox_info">'.$eLang->get('NO_AVAIL_DESC')."</div>\n";
		}

		if ($showdetails) {
			if (($row['modified'] != '') && ($row['modified'] != $row['created'])) {
				echo '<div class="extman5_xbox_date">'.$eLang->get('LAST_MODIFIED').' <span>'.$eDate->formatTS($row['modified'], $eLang->get('DATE_FORMAT_5'))."</span></div>\n";
			} else {
				echo '<div class="extman5_xbox_date">'.$eLang->get('DATE').' <span>'.$eDate->formatTS($row['created'], $eLang->get('DATE_FORMAT_5'))."</span></div>\n";
			}			
		} else {
			if ($ordering == 'm') {
				echo '<div class="extman5_xbox_date" title="'.$eLang->get('LAST_MODIFIED').'">'.$eDate->formatTS($row['modified'], $eLang->get('DATE_FORMAT_5'))."</div>\n";
			} else if (($row['modified'] != '') && ($row['modified'] != $row['created'])) {
				echo '<div class="extman5_xbox_date">'.$eDate->formatTS($row['modified'], $eLang->get('DATE_FORMAT_5'))."</div>\n";
			} else {
				echo '<div class="extman5_xbox_date">'.$eDate->formatTS($row['created'], $eLang->get('DATE_FORMAT_5'))."</div>\n";
			}			
		}
		echo "\n";

		if (!$actions['buy']) {
			echo '<div class="extman5_xbox_ratedl">'.$row['downloads'].' '.$eLang->get('DOWNLOADS')."</div>\n";
		}

		if ($showdetails) {
			if ($row['compatibility'] != '') {
				echo '<div class="extman5_xbox_iline">'.$eLang->get('COMPATIBILITY').' <strong>Elxis '.$row['compatibility']."</strong></div>\n";
			}
			if ($row['license'] != '') {
				if ($row['licenseurl'] != '') {
					echo '<div class="extman5_xbox_iline">'.$eLang->get('LICENSE').' <a href="'.$row['licenseurl'].'" target="_blank" title="'.$row['license'].'" class="extman5_xbox_link">'.$row['license'].' <i class="fas fa-external-link-alt"></i></a></div>'."\n";
				} else {
					echo '<div class="extman5_xbox_iline">'.$eLang->get('LICENSE').' '.$row['license']."</div>\n";
				}
			}
			if ($row['size'] > 0) {
				$fsize = number_format($row['size'], 0, '', '').' kb';
				if ($row['size'] > 800) {
					$s = $row['size'] / 1000;
					$fsize = number_format($s, 2, $eLang->get('DECIMALS_SEP'), $eLang->get('THOUSANDS_SEP')).' mb';
				}
				echo '<div class="extman5_xbox_iline">'.$eLang->get('SIZE').' '.$fsize."</div>\n";
			}
		}
		echo "</div>\n";

		echo '<div class="extman5_xbox_buttons">'."\n";
		if ($actions['download']) {
			echo '<a class="elx5_smbtn elx5_sucbtn extman5_smbtn" href="javascript:void(null);" onclick="extman5EDCDownload(\''.$row['pcode'].'\');"><span class="elx5_lmobhide"><i class="fas fa-arrow-alt-circle-down"></i> </span>'.$eLang->get('DOWNLOAD').'</a>'." \n";
		}
		if ($actions['is_installed']) {
			if ($actions['update']) {
				$exttitle = addslashes($row['title']);
				echo '<a class="elx5_smbtn elx5_warnbtn extman5_smbtn" href="javascript:void(null);" onclick="extman5EDCPrompt(\'update\', \''.$row['pcode'].'\', \''.$exttitle.'\', \''.$row['version'].'\');"><span class="elx5_lmobhide"><i class="fas fa-sync"></i> </span>'.$eLang->get('UPDATE')."</a>\n";
			} else if ($actions['is_updated']) {
				echo '<a class="elx5_smbtn elx5_notallowedbtn extman5_smbtn" href="javascript:void(null);"><span class="elx5_lmobhide"><i class="fas fa-check"></i> </span>'.$eLang->get('UPDATED')."</a>\n";
			} else {
				echo '<a class="elx5_smbtn elx5_notallowedbtn extman5_smbtn" href="javascript:void(null);"><span class="elx5_lmobhide"><i class="fas fa-check"></i> </span>'.$eLang->get('INSTALLED')."</a>\n";
			}
		} else {
			if ($actions['install']) {
				$exttitle = addslashes($row['title']);
				echo '<a class="elx5_smbtn elx5_sucbtn extman5_smbtn" href="javascript:void(null);" onclick="extman5EDCPrompt(\'install\', \''.$row['pcode'].'\', \''.$exttitle.'\', \''.$row['version'].'\');"><i class="fas fa-download"></i> </span>'.$eLang->get('INSTALL')."</a>\n";
			}			
		}
		if ($actions['buy']) {
			echo '<a class="elx5_smbtn extman5_smbtn" href="'.$row['buylink'].'" title="'.$eLang->get('PRICE').' '.$row['price'].' - '.$eLang->get('BUY').'" target="_blank"><i class="fas fa-shopping-cart"></i> '.$row['price']."</a>\n";
		}

		if ($showdetails === true) {
			if ($row['demolink'] != '') {
				echo '<a href="'.$row['demolink'].'" target="_blank" title="'.$eLang->get('DEMO').'" class="elx5_smbtn extman5_smbtn">'.$eLang->get('DEMO').' <i class="fas fa-external-link-alt"></i></a>'."\n";
			}
			if ($row['doclink'] != '') {
				echo '<a href="'.$row['doclink'].'" target="_blank" title="'.$eLang->get('DOCUMENTATION').'" class="elx5_smbtn extman5_smbtn">'.$eLang->get('DOCUMENTATION').' <i class="fas fa-external-link-alt"></i></a>'."\n";
			}
		}
		echo "</div>\n";
		echo "</div>\n";
	}


	/***************************************/
	/* EXTENSION'S DETAILS IN THE SIDE BOX */
	/***************************************/
	private function extensionMore($row, $is_ssl, $edc, $elxis, $eLang) {
		echo '<h3><i class="fas fa-pen-fancy elx5_lmobhide"></i> '.$eLang->get('DESCRIPTION')."</h3>\n";

		$desc = ($row['description'] != '') ? $row['description'] : $eLang->get('NO_AVAIL_DESC');
		echo '<div class="extman5_edc_extdesc">'.$desc;
		if ($row['link'] != '') { echo '<br /><a href="'.$row['link'].'" target="_blank">'.$eLang->get('EXTDET_PUBSITE')."</a>\n"; }
		echo "</div>\n";

		$screens = array();
		$use_thumbs = true;
		for ($i=1; $i<7; $i++) {
			$idx = 'image'.$i;
			if ($row[$idx] == '') { continue; }
			if (($is_ssl == true) && !preg_match('#^(https\:\/\/)#i', $row[$idx])) { $use_thumbs = false; }
			$screens[] = $row[$idx];
		}
		
		if ($screens) {
			$deficon = $elxis->icon('media', 64);
			echo '<h3><i class="fas fa-camera elx5_lmobhide"></i> '.$eLang->get('SCREENSHOTS')."</h3>\n";
			echo '<div class="elx5_dlspace">'."\n";
			foreach ($screens as $i => $screen) {
				$icon = $use_thumbs ? $screen : $deficon;
				echo '<a href="#edcimage'.$i.'"><img src="'.$icon.'" alt="thumbnail" class="extman5_edc_lbthumb"></a>'."\n"; 
				echo '<a href="#" class="extman5_edc_lightbox" id="edcimage'.$i.'"><div><img src="'.$screen.'" alt="image" /></div></a>'."\n"; 
			}
			echo "</div>\n";
		}
	}

}

?>