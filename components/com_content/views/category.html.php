<?php 
/**
* @version		$Id: category.html.php 2337 2020-02-20 19:07:53Z IOS $
* @package		Elxis
* @subpackage	Component Content
* @copyright	Copyright (c) 2006-2019 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class categoryContentView extends contentView {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/********************/
	/* DISPLAY CATEGORY */
	/********************/
	public function showCategory($row, $subcategories, $articles, $page, $maxpage, $total, $params, $print=0) {
		$num_articles = $articles ? count($articles) : 0;
		$ctg_featured_num = (int)$params->def('ctg_featured_num', 0);
		$ctg_short_num = (int)$params->def('ctg_short_num', 0);
		$ctg_links_num = (int)$params->def('ctg_links_num', 0);
		$ctg_layout = (int)$params->def('ctg_layout', 0);

		if ($page > 1) {
			$ctg_layout = 0;
			$params->set('ctg_short_cols', 0);
			$params->set('ctg_links_cols', 0);
			switch ((int)$params->get('ctg_nextpages_style', 0)) {
				case 0:
					$ctg_featured_num += $ctg_short_num;
					$ctg_featured_num += $ctg_links_num;
					$ctg_short_num = 0;
					$ctg_links_num = 0;
				break;
				case 2:
					$ctg_links_num += $ctg_featured_num;
					$ctg_links_num += $ctg_short_num;
					$ctg_featured_num = 0;
					$ctg_short_num = 0;
				break;
				case 1: default:
					$ctg_short_num += $ctg_featured_num;
					$ctg_short_num += $ctg_links_num;
					$ctg_featured_num = 0;
					$ctg_links_num = 0;
				break;
			}
		} else {
			$rest = $num_articles - $ctg_featured_num;
			if ($rest <= 0) {
				$rest = 0;
				$ctg_featured_num = $num_articles;
				$ctg_short_num = 0;
				$ctg_links_num = 0;
			}
			
			if ($rest > 0) {
				$rest = $rest - $ctg_short_num;
				if ($rest <= 0) {
					$rest = 0;
					$ctg_short_num = $num_articles - $ctg_featured_num;
					$ctg_links_num = 0;
				}
			}

			$ctg_links_num = $rest;

			if ($ctg_featured_num == 0) {
				if ($ctg_layout == 1) { $ctg_layout = 0; }
			}
			if ($ctg_short_num == 0) { $ctg_layout = 0; }
			if ($ctg_links_num == 0) {
				if ($ctg_layout == 2) { $ctg_layout = 0; }
			}
		}

		$this->wrapperStart('category', $row->catid);
		$this->renderCategorySummary($row, $params, $print);
		if ((int)$params->def('ctg_subcategories', 2) === 1) {
			$this->renderSubcategories($row, $subcategories, $params);
		}

		$ctg_mods_pos = $params->get('ctg_mods_pos', '');
		switch ($ctg_layout) {
			case 1:
				if (($ctg_featured_num > 0) && ($ctg_short_num > 0)) {
					echo '<div class="elx5_2colwrap">'."\n";
					echo '<div class="elx5_2colbox">'."\n";
					$this->renderFeaturedArticles($row, $articles, $ctg_featured_num, $params);
					echo "</div>\n";
					echo '<div class="elx5_2colbox">'."\n";
					$this->renderShortArticles($row, $articles, $ctg_featured_num, $ctg_short_num, $params);
					echo "</div>\n";
					echo "</div>\n";
				} else {
					if ($ctg_featured_num > 0) {
						$this->renderFeaturedArticles($row, $articles, $ctg_featured_num, $params);
					}
					if ($ctg_short_num > 0) {
						$this->renderShortArticles($row, $articles, $ctg_featured_num, $ctg_short_num, $params);
					}
				}
				if ($ctg_mods_pos != '') {
					eFactory::getDocument()->modules($ctg_mods_pos);
				}
				if ($ctg_links_num > 0) {
					$this->renderLinkArticles($row, $articles, $ctg_featured_num, $ctg_short_num, $ctg_links_num, $params);
				}
			break;
			case 2:
				if ($ctg_featured_num > 0) {
					$this->renderFeaturedArticles($row, $articles, $ctg_featured_num, $params);
				}
				if ($ctg_mods_pos != '') {
					eFactory::getDocument()->modules($ctg_mods_pos);
				}
				if (($ctg_short_num > 0) && ($ctg_links_num > 0)) {
					echo '<div class="elx5_2colwrap">'."\n";
					echo '<div class="elx5_2colbox">'."\n";
					$this->renderShortArticles($row, $articles, $ctg_featured_num, $ctg_short_num, $params);
					echo "</div>\n";
					echo '<div class="elx5_2colbox">'."\n";
					$this->renderLinkArticles($row, $articles, $ctg_featured_num, $ctg_short_num, $ctg_links_num, $params);
					echo "</div>\n";
					echo "</div>\n";
				} else {
					if ($ctg_short_num > 0) {
						$this->renderShortArticles($row, $articles, $ctg_featured_num, $ctg_short_num, $params);
					}
					if ($ctg_links_num > 0) {
						$this->renderLinkArticles($row, $articles, $ctg_featured_num, $ctg_short_num, $ctg_links_num, $params);
					}
				}
			break;
			case 0: default:
				if ($ctg_featured_num > 0) {
					$this->renderFeaturedArticles($row, $articles, $ctg_featured_num, $params);
				}
				if ($ctg_mods_pos != '') {
					eFactory::getDocument()->modules($ctg_mods_pos);
				}
				if ($ctg_short_num > 0) {
					$this->renderShortArticles($row, $articles, $ctg_featured_num, $ctg_short_num, $params);
				}
				if ($ctg_links_num > 0) {
					$this->renderLinkArticles($row, $articles, $ctg_featured_num, $ctg_short_num, $ctg_links_num, $params);
				}
			break;
		}

		if (($maxpage > 1) && ($print == 0)) {
			if ((int)$params->get('ctg_pagination') == 1) {
				$linkbase = eFactory::getElxis()->makeURL($row->link);
				echo '<div class="elx5_vspace">'."\n";
				echo eFactory::getElxis()->obj('html')->pagination($linkbase, $page, $maxpage);
				echo "</div>\n";
			}
		}

		if ((int)$params->get('ctg_subcategories', 2) === 2) {
			$this->renderSubcategories($row, $subcategories, $params);
		}

		$this->wrapperEnd('category');
	}


	/***************************/
	/* RENDER CATEGORY SUMMARY */
	/***************************/
	private function renderCategorySummary($row, $params, $print=0) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$ctg_show = (int)$params->get('ctg_show', 2);
		$ctg_print = (int)$params->get('ctg_print', 0);
		if ($ctg_show < 1) {
			if (($print == 1) || ($ctg_print == 1)) {
				echo '<div class="elx5_content_icons" data-icons="1">'."\n";
				echo '<div class="elx_content_icon">'."\n";
				if ($print == 1) {
					echo '<a href="javascript:void(null);" title="'.$eLang->get('PRINT').'" onclick="javascript:window.print();" class="elx5_lmobhide">';
				} else {
					$link = $elxis->makeURL($row->link.'?print=1', 'inner.php');
					echo '<a href="javascript:void(null);" title="'.$eLang->get('PRINTABLE_VERSION').'" onclick="elxPopup(\''.$link.'\', 800, 600);">';
				}
				echo '<i class="fas fa-print"></i></a>'."\n";
				echo "</div>\n";
				echo "</div>\n";
			}
			return;
		}

		echo '<div class="elx5_category_header">'."\n";
		if ($ctg_print == 1) {
			echo '<div class="elx5_zero">'."\n";
			echo '<div class="elx5_content_icons" data-icons="1">'."\n";
			if ($print == 1) {
				echo '<a href="javascript:void(null);" title="'.$eLang->get('PRINT').'" onclick="javascript:window.print();" class="elx5_lmobhide">';
			} else {
				$link = $elxis->makeURL($row->link.'?print=1', 'inner.php');
				echo '<a href="javascript:void(null);" title="'.$eLang->get('PRINTABLE_VERSION').'" onclick="elxPopup(\''.$link.'\', 800, 600);" class="elx5_lmobhide">';
			}
			echo '<i class="fas fa-print"></i></a>'."\n";
			echo "</div>\n";
			echo '<h1 class="elx5_category_h1i">'.$row->title."</h1>\n";
			echo "</div>\n";
		} else {
			echo '<h1>'.$row->title."</h1>\n";
		}
		echo "</div>\n";//elx5_category_header

		if ($ctg_show <> 2) { return; }
		$html = '';
		$clear = '';
		if ((trim($row->image) != '') && file_exists(ELXIS_PATH.'/'.$row->image)) {
			$img = $elxis->secureBase().'/'.$row->image;
			$html = '<img src="'.$img.'" alt="'.$row->title.'" class="elx5_category_image" />'."\n";
			$clear = '<div class="clear"></div>'."\n";
		}
		if (trim($row->description) != '') { $html.= $row->description."\n"; }
		if ($html != '') {
			echo '<div class="elx5_category_summary">'."\n".$html.$clear."</div>\n";
		}
	}


	/*************************/
	/* RENDER SUB-CATEGORIES */
	/*************************/
	private function renderSubcategories($row, $subcategories, $params) {
		if (!$subcategories) { return; }
		$elxis = eFactory::getElxis();
		$cols = (int)$params->get('ctg_subcategories_cols', 2);
		echo '<h3 class="elx_subcategories_title">'.eFactory::getLang()->get('SUBCATEGORIES')."</h3>\n";
		if ($cols  > 1) {
			$cols_idx = array();
			for ($i=0; $i<$cols; $i++) { $cols_idx[$i] = 0; }
			$curcol = 0;
			for ($k=0; $k < count($subcategories); $k++) {
				$cols_idx[$curcol]++;
				$curcol++;
				if ($curcol == $cols) { $curcol = 0; }
			}

			$start = 0;
			echo '<div class="elx5_'.$cols.'colwrap">'."\n";
			for($col=0; $col < $cols; $col++) {
				$end = $start + $cols_idx[$col];
				echo '<div class="elx5_'.$cols.'colbox">'."\n";
				echo "\t".'<ul class="elx_subcategories">'."\n";
				for ($i=$start; $i < $end; $i++) {
					if (isset($subcategories[$i])) {
						$subcategory = $subcategories[$i];
						echo "\t\t".'<li><a href="'.$elxis->makeURL($row->link.$subcategory->seotitle.'/').'" title="'.$subcategory->title.'">'.$subcategory->title."</a></li>\n";
					}
				}
				$start += $cols_idx[$col];
				echo "\t</ul>\n";
				echo "</div>\n";
			}
			echo "</div>\n";
		} else {
			echo '<ul class="elx_subcategories">'."\n";
			foreach ($subcategories as $subcategory) {
				echo "\t".'<li><a href="'.$elxis->makeURL($row->link.$subcategory->seotitle.'/').'" title="'.$subcategory->title.'">'.$subcategory->title."</a></li>\n";
			}
			echo "</ul>\n";
		}
	}


	/****************************/
	/* RENDER FEATURED ARTICLES */
	/****************************/
	private function renderFeaturedArticles($row, $articles, $ctg_featured_num, $params) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$eFiles = eFactory::getFiles();

		$ctg_featured_dateauthor = (int)$params->get('ctg_featured_dateauthor', 6);
		$ctg_featured_more = (int)$params->get('ctg_featured_more', 0);
		$ctg_img_empty = (int)$params->get('ctg_img_empty', 1);
		$ctg_featured_img = (int)$params->get('ctg_featured_img', 2);
		if ($ctg_featured_img == 1) { $ctg_featured_img = 4; }//MEDIUM_IMG_TOP ==> LARGE_IMG_TOP (Elxis 5.x)

		switch ($ctg_featured_img) {
			case 2: $figaddon = ' elx5_content_imageboxml'; $boxaddon = ' elx5_artboxml'; break;
			case 3: $figaddon = ' elx5_content_imageboxmr'; $boxaddon = ' elx5_artboxmr'; break;
			case 4: $figaddon = ' elx5_content_imageboxlt'; $boxaddon = ' elx5_artboxlt'; break;
			case 5: $figaddon = ' elx5_content_imageboxlt'; $boxaddon = ' elx5_artboxvt'; break;
			case 0: default: $figaddon = ''; $boxaddon = ''; break;
		}

		$allowed_any_profile = ((int)$elxis->acl()->check('com_user', 'profile', 'view') == 2) ? true : false;
		$i = 0;
		foreach ($articles as $id => $article) {
			if ($i >= $ctg_featured_num) { break; }
			$link = $elxis->makeURL($row->link.$article->seotitle.'.html');
			$imgbox = '';
			if ($ctg_featured_img > 0) {
				if ((trim($article->image) == '') || !file_exists(ELXIS_PATH.'/'.$article->image)) {
					if ($ctg_img_empty == 1) {
						$alt = (trim($article->caption) != '') ? $article->caption : $article->title;
						$imgbox = '<figure class="elx5_content_imagebox'.$figaddon.'">'."\n";
						$imgbox .= '<a href="'.$link.'" title="'.$article->title.'">';
						$imgbox .= '<img src="'.$elxis->secureBase().'/templates/system/images/nopicture_article.jpg" alt="'.$alt.'" />'; 
						$imgbox .= "</a>\n";
						if (trim($article->caption) != '') { $imgbox .= '<figcaption>'.$article->caption."</figcaption>\n"; }
						$imgbox .= "</figure>\n";
					}
				} else {
					$imgfile = $elxis->secureBase().'/'.$article->image;
					if ($ctg_featured_img < 4) {
						$file_info = $eFiles->getNameExtension($article->image);
						if (file_exists(ELXIS_PATH.'/'.$file_info['name'].'_medium.'.$file_info['extension'])) {
							$imgfile = $elxis->secureBase().'/'.$file_info['name'].'_medium.'.$file_info['extension'];
						}
						unset($file_info);
					}
					$alt = (trim($article->caption) != '') ? $article->caption : $article->title;
					$imgbox = '<figure class="elx5_content_imagebox'.$figaddon.'">'."\n";
					$imgbox .= '<a href="'.$link.'" title="'.$article->title.'"><img src="'.$imgfile.'" alt="'.$alt.'" /></a>'."\n";
					if (trim($article->caption) != '') { $imgbox .= '<figcaption>'.$article->caption."</figcaption>\n"; }
					$imgbox .= "</figure>\n";
				}
			}

			$dateauthor = $this->getDateAuthor($article, $ctg_featured_dateauthor, $allowed_any_profile, 'DATE_FORMAT_5', 'DATE_FORMAT_4');

			echo '<div class="elx5_artbox'.$boxaddon.'" data-featured="1">'."\n";
			if ($ctg_featured_img != 4) { echo $imgbox; }
			echo '<div class="elx5_artbox_inner">'."\n";
			echo '<h3><a href="'.$link.'" title="'.$article->title.'">'.$article->title.'</a></h3>'."\n";
			if ($dateauthor != '') {
				if ($ctg_featured_dateauthor == 7) {
					echo $dateauthor;
				} else {
					echo '<div class="elx5_dateauthor">'.$dateauthor.'</div>'."\n"; 
				}
			}
			if ($ctg_featured_img == 4) { echo $imgbox; }
			if (trim($article->subtitle) != '') { echo '<p class="elx5_content_subtitle">'.$article->subtitle."</p>\n"; }
			echo $article->introtext."\n";
			if ($ctg_featured_more == 1) {
				echo ' <a href="'.$link.'" title="'.$article->title.'" class="elx_more">'.$eLang->get('MORE').'</a>'."\n";
			}
			echo "</div>\n";//elx5_artbox_inner
			echo "</div>\n";//elx5_artbox
			$i++;
		}
	}


	/*************************/
	/* RENDER SHORT ARTICLES */
	/*************************/
	private function renderShortArticles($row, $articles, $ctg_featured_num, $ctg_short_num, $params) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$eFiles = eFactory::getFiles();

		$cols = (int)$params->get('ctg_short_cols', 1);
		$ctg_short_dateauthor = (int)$params->get('ctg_short_dateauthor', 6);
		$ctg_short_img = (int)$params->get('ctg_short_img', 2);
		$ctg_short_text = (int)$params->get('ctg_short_text', 180);
		$ctg_short_more = (int)$params->get('ctg_short_more', 0);
		$ctg_img_empty = (int)$params->get('ctg_img_empty', 1);

		switch ($ctg_short_img) {
			case 1: $figaddon = ' elx5_content_imageboxlt'; $boxaddon = ' elx5_artboxlt'; break;
			case 2: $figaddon = ' elx5_content_imageboxtl'; $boxaddon = ' elx5_artboxtl'; break;
			case 3: $figaddon = ' elx5_content_imageboxtr'; $boxaddon = ' elx5_artboxtr'; break;
			case 4: $figaddon = ' elx5_content_imageboxlt'; $boxaddon = ' elx5_artboxvt'; break;
			case 0: default: $figaddon = '';  $boxaddon = ''; break;
		}

		if ($cols > 1) {
			$date_format_long = 'DATE_FORMAT_3';
			$date_format_short = 'DATE_FORMAT_2';
		} else {
			$date_format_long = 'DATE_FORMAT_5';
			$date_format_short = 'DATE_FORMAT_4';
		}

		$allowed_any_profile = ((int)$elxis->acl()->check('com_user', 'profile', 'view') == 2) ? true : false;
		$i = 0;
		$b = 0;
		$imgsize = (($ctg_short_img == 2) || ($ctg_short_img == 3)) ? '_thumb' : '_medium';
		$buffer = array();
		foreach ($articles as $id => $article) {
			if ($i < $ctg_featured_num) { $i++; continue; }
			if ($i >= ($ctg_featured_num + $ctg_short_num)) { break; }
			$link = $elxis->makeURL($row->link.$article->seotitle.'.html');
			$imgbox = '';
			if ($ctg_short_img > 0) {
				if ((trim($article->image) == '') || !file_exists(ELXIS_PATH.'/'.$article->image)) {
					if ($ctg_img_empty == 1) {
						$imgbox = '<figure class="elx5_content_imagebox'.$figaddon.'">'."\n";
						$imgbox .= '<a href="'.$link.'" title="'.$article->title.'">';
						$imgbox .= '<img src="'.$elxis->secureBase().'/templates/system/images/nopicture_article.jpg" alt="'.$article->title.'" />'; 
						$imgbox .= "</a>\n";
						$imgbox .= "</figure>\n";
					}
				} else {
					$imgfile = $elxis->secureBase().'/'.$article->image;
					$file_info = $eFiles->getNameExtension($article->image);
					if ($ctg_short_img > 1) if (file_exists(ELXIS_PATH.'/'.$file_info['name'].$imgsize.'.'.$file_info['extension'])) {
						$imgfile = $elxis->secureBase().'/'.$file_info['name'].$imgsize.'.'.$file_info['extension'];
					}
					unset($file_info);
					$imgbox = '<figure class="elx5_content_imagebox'.$figaddon.'">'."\n";
					$imgbox .= '<a href="'.$link.'" title="'.$article->title.'"><img src="'.$imgfile.'" alt="'.$article->title.'" /></a>'."\n";
					$imgbox .= "</figure>\n";
				}
			}

			$dateauthor = $this->getDateAuthor($article, $ctg_short_dateauthor, $allowed_any_profile, $date_format_long, $date_format_short);

			$buffer[$b] = '';
			if ($ctg_short_img != 1) { $buffer[$b] .= $imgbox; }
			$buffer[$b] .= '<div class="elx5_artbox_inner">'."\n";
			$buffer[$b] .= '<h3><a href="'.$link.'" title="'.$article->title.'">'.$article->title.'</a></h3>'."\n";
			if ($dateauthor != '') {
				if ($ctg_short_dateauthor == 7) {
					$buffer[$b] .= $dateauthor;
				} else {
					$buffer[$b] .= '<div class="elx5_dateauthor">'.$dateauthor.'</div>'."\n";
				}
			}
			$buffer[$b] .= "</div>\n";
			if ($ctg_short_img == 1) { $buffer[$b] .= $imgbox; }

			$buffer[$b] .= '<div class="elx5_artbox_inner">'."\n";
			if ($ctg_short_text > 0) {
				$moretext = '';
				if ($ctg_short_more == 1) {
					$moretext = ' <a href="'.$link.'" title="'.$article->title.'" class="elx_more">'.$eLang->get('MORE').'</a>'."\n";
				}
				if ($ctg_short_text == 1) {
					if (trim($article->subtitle) != '') {
						$buffer[$b] .= '<p class="elx5_content_subtitle">'.$article->subtitle."</p>\n";
					}
				} else if ($ctg_short_text == 1000) {
					if (trim($article->subtitle) != '') { $buffer[$b] .= '<p class="elx5_content_subtitle">'.$article->subtitle."</p>\n"; }
					$buffer[$b] .= $article->introtext.$moretext."\n";
				} else {
					$txt = strip_tags($article->introtext);
					if (trim($article->subtitle) != '') { $txt = $article->subtitle.' '.$txt; }
					$len = eUTF::strlen($txt);
					if ($len > $ctg_short_text) {
						$limit = $ctg_short_text - 3;
						$txt = eUTF::substr($txt, 0, $limit).'...';
					}
					$buffer[$b] .= '<p>'.$txt.$moretext."</p>\n";
				}
			}
			if (($ctg_short_img != 1) && ($ctg_short_img != 4)) {
				$buffer[$b] .= '<div class="clear"></div>'."\n";
			}
			$buffer[$b] .= "</div>\n";//elx5_artbox_inner
			$i++;
			$b++;
		}

		if (!$buffer) { return; }

		if ($cols > 1) {
			echo '<div class="elx5_'.$cols.'colwrap">'."\n";
			foreach ($buffer as $txt) {
				echo '<div class="elx5_'.$cols.'colbox elx5_artbox'.$boxaddon.'" data-short="1">'."\n";
				echo $txt;
				echo "</div>\n";
			}
			echo "</div>\n";
		} else {
			foreach ($buffer as $txt) {
				echo '<div class="elx5_artbox'.$boxaddon.'" data-short="1">'."\n";
				echo $txt;
				echo "</div>\n";
			}
		}
	}


	/************************/
	/* RENDER LINK ARTICLES */
	/************************/
	private function renderLinkArticles($row, $articles, $ctg_featured_num, $ctg_short_num, $ctg_links_num, $params) {
		$elxis = eFactory::getElxis();

		$cols = (int)$params->get('ctg_links_cols', 1);

		$ctg_links_dateauthor = (int)$params->get('ctg_links_dateauthor', 0);
		if ($cols > 1) {
			$date_format_long = 'DATE_FORMAT_3';
			$date_format_short = 'DATE_FORMAT_2';
		} else {
			$date_format_long = 'DATE_FORMAT_5';
			$date_format_short = 'DATE_FORMAT_4';
		}
		$allowed_any_profile = ((int)$elxis->acl()->check('com_user', 'profile', 'view') == 2) ? true : false;
		$i = 0;
		$b = 0;
		$buffer = array();
		foreach ($articles as $id => $article) {
			if ($i < ($ctg_featured_num + $ctg_short_num)) { $i++; continue; }
			if ($i >= ($ctg_featured_num + $ctg_short_num + $ctg_links_num)) { break; }
			$link = $elxis->makeURL($row->link.$article->seotitle.'.html');
			$dateauthor = $this->getDateAuthor($article, $ctg_links_dateauthor, $allowed_any_profile, $date_format_long, $date_format_short);
			$buffer[$b] = '<li><a href="'.$link.'" title="'.$article->title.'">'.$article->title.'</a>';
			if ($dateauthor != '') {
				if ($ctg_links_dateauthor == 7) {
					$buffer[$b] .= $dateauthor; 
				} else {
					$buffer[$b] .= '<div class="elx5_dateauthor">'.$dateauthor.'</div>'; 
				}
			}
			$buffer[$b] .= "</li>\n";
			$i++;
			$b++;
		}

		switch (intval($params->get('ctg_links_header', 0))) {
			case 1: echo '<h3 class="elx_links_box_title">'.eFactory::getLang()->get('READ_ALSO')."</h3>\n"; break;
			case 2: echo '<h3 class="elx_links_box_title">'.eFactory::getLang()->get('OTHER_ARTICLES')."</h3>\n"; break;
			case 0: default: break;
		}

		$class = ($cols > 1) ? 'elx5_links_box elx5_links_box'.$cols.'cols' : 'elx5_links_box';
		echo '<ul class="'.$class.'">'."\n";
		foreach ($buffer as $txt) {
			echo $txt;
		}
		echo "</ul>\n";
	}


	/********************************************/
	/* GET/FORMAT DATE AN AUTHOR FOR AN ARTICLE */
	/********************************************/
	private function getDateAuthor($article, $type, $allowed=false, $date_format_long='DATE_FORMAT_5', $date_format_short='DATE_FORMAT_4') {
		$eLang = eFactory::getLang();
		$eDate = eFactory::getDate();

		$dateauthor = '';

		switch($type) {
			case 1:	$dateauthor = '<time datetime="'.$article->created.'">'.$eDate->formatDate($article->created, $eLang->get($date_format_long)).'</time>'; break;
			case 2:
				$dateauthor = '<time datetime="'.$article->created.'">'.$eDate->formatDate($article->created, $eLang->get($date_format_long)).'</time>';
				if ($allowed) {
					$proflink = eFactory::getElxis()->makeURL('user:members/'.$article->created_by.'.html');
					$dateauthor .= ' '.$eLang->get('BY').' <a href="'.$proflink.'" title="'.$article->created_by_name.'">'.$article->created_by_name.'</a>';
				} else {
					$dateauthor .= ' '.$eLang->get('BY').' '.$article->created_by_name;
				}
			break;
			case 3:
				if ($article->modified != '1970-01-01 00:00:00') {
					$dateauthor = $eLang->get('LAST_UPDATE').' <time datetime="'.$article->modified.'">'.$eDate->formatDate($article->modified, $eLang->get($date_format_short)).'</time>';
				}
			break;
			case 4:
				if ($article->modified != '1970-01-01 00:00:00') {
					$dateauthor = $eLang->get('LAST_UPDATE').' <time datetime="'.$article->modified.'">'.$eDate->formatDate($article->modified, $eLang->get($date_format_short)).'</time>';
					if (($article->modified_by > 0) && $allowed) {
						$proflink = eFactory::getElxis()->makeURL('user:members/'.$article->modified_by.'.html');
						$dateauthor .= ' '.$eLang->get('BY').' <a href="'.$proflink.'" title="'.$article->modified_by_name.'">'.$article->modified_by_name.'</a>';
					} else {
						$dateauthor .= ' '.$eLang->get('BY').' '.$article->modified_by_name;
					}
				}
			break;
			case 5:
				if ($article->modified != '1970-01-01 00:00:00') {
					$dateauthor = $eLang->get('LAST_UPDATE').' <time datetime="'.$article->modified.'">'.$eDate->formatDate($article->modified, $eLang->get($date_format_short)).'</time>';
				} else {
					$dateauthor = '<time datetime="'.$article->created.'">'.$eDate->formatDate($article->created, $eLang->get($date_format_long)).'</time>';
				}
			break;
			case 6:
				if ($article->modified != '1970-01-01 00:00:00') {
					$dateauthor = $eLang->get('LAST_UPDATE').' <time datetime="'.$article->modified.'">'.$eDate->formatDate($article->modified, $eLang->get($date_format_short)).'</time>';
					if (($article->modified_by > 0) && $allowed) {
						$proflink = eFactory::getElxis()->makeURL('user:members/'.$article->modified_by.'.html');
						$dateauthor .= ' '.$eLang->get('BY').' <a href="'.$proflink.'" title="'.$article->modified_by_name.'">'.$article->modified_by_name.'</a>';
					} else {
						$dateauthor .= ' '.$eLang->get('BY').' '.$article->modified_by_name;
					}
				} else {
					$dateauthor = '<time datetime="'.$article->created.'">'.$eDate->formatDate($article->created, $eLang->get($date_format_long)).'</time>';
					if ($allowed) {
						$proflink = eFactory::getElxis()->makeURL('user:members/'.$article->created_by.'.html');
						$dateauthor .= ' '.$eLang->get('BY').' <a href="'.$proflink.'" title="'.$article->created_by_name.'">'.$article->created_by_name.'</a>';
					} else {
						$dateauthor .= ' '.$eLang->get('BY').' '.$article->created_by_name;
					}
				}
			break;
			case 7:
				$ts = time() + $eDate->getOffset();
				$ts2 = $ts - 86400;
				$tsx = strtotime($article->created) + $eDate->getOffset();
				$dt = $ts - $tsx;

				if (date('Y', $ts) != date('Y', $tsx)) {
					$special_format = '<time datetime="'.$article->created.'" class="elx_datetime"><span class="month">%b</span><span class="day">%d</span><span class="year">%Y</span></time>';
				} else if (date('Ymd', $ts) == date('Ymd', $tsx)) {//today
					$special_format = '<time datetime="'.$article->created.'" class="elx_datetime"><span class="wday">'.$eLang->get('TODAY').'</span><span class="time">%H:%M</span></time>';
				} else if (date('Ymd', $ts2) == date('Ymd', $tsx)) {//yesterday
					$special_format = '<time datetime="'.$article->created.'" class="elx_datetime"><span class="wday">'.$eLang->get('YESTERDAY').'</span><span class="time">%H:%M</span></time>';
				} else if ($dt < 345600) { //4 days
					$special_format = '<time datetime="'.$article->created.'" class="elx_datetime"><span class="wday">%a</span><span class="time">%H:%M</span><span class="month">%b</span></time>';
				} else {
					$special_format = '<time datetime="'.$article->created.'" class="elx_datetime"><span class="wday">%a</span><span class="day">%d</span><span class="month">%b</span></time>';
				}
				$dateauthor = $eDate->formatDate($article->created, $special_format);
			break;
			default: break;
		}

		return $dateauthor;
	}
		
}

?>