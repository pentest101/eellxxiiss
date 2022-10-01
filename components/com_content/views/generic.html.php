<?php 
/**
* @version		$Id$
* @package		Elxis
* @subpackage	Component Content
* @copyright	Copyright (c) 2006-2019 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class genericContentView extends contentView {


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/***************************************/
	/* DISPLAY LIST OF AVAILABLE XML FEEDS */
	/***************************************/
	public function feedsCentral($rows) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$this->wrapperStart('feeds', 0);
		echo '<h1>'.$eLang->get('RSS_ATOM_FEEDS_CENTRAL')."</h1>\n";
		echo '<p>'.sprintf($eLang->get('LIST_OF_FEEDS'), '<strong>'.$elxis->getConfig('SITENAME').'</strong>')."</p>\n";

		$rss_icon = $elxis->icon('rss', 24);
		$rss_no_icon = $elxis->icon('rss_no', 24);
		$atom_icon = $elxis->icon('atom', 24);
		$atom_no_icon = $elxis->icon('atom_no', 24);

		$link1 = $elxis->makeURL('content:rss.xml');
		$link2 = $elxis->makeURL('content:atom.xml');

		echo '<table dir="'.$eLang->getinfo('DIR').'" class="elx_feeds_tbl">'."\n";
		echo "<tr>\n";
		echo '<td colspan="2"><h3>'.$eLang->get('SITE_FEED').'</h3>'."</td>\n";
		echo '<td>&#160;'."</td>\n";
		echo '<td class="elx5_feeds_icontd"><a href="'.$link1.'" title="RSS" target="_blank"><img src="'.$rss_icon.'" alt="RSS" /></a></td>'."\n";
		echo '<td class="elx5_feeds_icontd"><a href="'.$link2.'" title="ATOM" target="_blank"><img src="'.$atom_icon.'" alt="ATOM" /></a></td>'."\n";
		echo "</tr>\n";
		echo "</table>\n";
		unset($link1, $link2);

		echo '<p>'.$eLang->get('FEEDS_CONTAIN_ARTS')."</p>\n";

		if (!$rows) {
			echo '<div class="elx5_warning">'.$eLang->get('NO_FEEDS_AV')."</div>\n";
			$this->wrapperEnd('feeds');
			return;
		}

		echo '<table dir="'.$eLang->getinfo('DIR').'" class="elx_feeds_tbl">'."\n";
		foreach ($rows as $row) {
			$link = $elxis->makeURL($row->seotitle.'/');
			echo "<tr>\n";
			echo '<td colspan="2"><h3>'.$row->title.'</h3>'."</td>\n";
			echo '<td class="elx5_feeds_artstd elx5_lmobhide">';
			if ($row->articles > -1) {
				echo '<span>'.$row->articles.' ';
				echo ($row->articles == 1) ? $eLang->get('ARTICLE') : $eLang->get('ARTICLES');
				echo '</span>';			
			} else {
				echo '&#160;';
			}
			echo "</td>\n";
			if ($row->articles == 0) {
				echo '<td class="elx5_feeds_icontd"><img src="'.$rss_no_icon.'" alt="RSS" title="RSS" /></td>'."\n";
				echo '<td class="elx5_feeds_icontd"><img src="'.$atom_no_icon.'" alt="ATOM" title="ATOM" /></td>'."\n";
			}  else {
				echo '<td class="elx5_feeds_icontd"><a href="'.$link.'rss.xml" title="RSS" target="_blank"><img src="'.$rss_icon.'" alt="RSS" /></a></td>'."\n";
				echo '<td class="elx5_feeds_icontd"><a href="'.$link.'atom.xml" title="ATOM" target="_blank"><img src="'.$atom_icon.'" alt="ATOM" /></a></td>'."\n";
			}
			echo "</tr>\n";
			if (count($row->categories) > 0) {
				foreach ($row->categories as $sub) {
					$link = $elxis->makeURL($row->seotitle.'/'.$sub->seotitle.'/');
					echo "<tr>\n";
					echo '<td class="elx5_feeds_icontd">&#160;</td>'."\n";
					echo '<td>'.$sub->title."</td>\n";
					echo '<td class="elx5_feeds_artstd elx5_lmobhide">';
					if ($sub->articles > -1) {
						echo '<span>'.$sub->articles.' ';
						echo ($sub->articles == 1) ? $eLang->get('ARTICLE') : $eLang->get('ARTICLES');
						echo '</span>';
					} else {
						echo '&#160;';
					}
					echo "</td>\n";
					if ($sub->articles == 0) {
						echo '<td class="elx5_feeds_icontd"><img src="'.$rss_no_icon.'" alt="RSS" title="RSS" /></td>'."\n";
						echo '<td class="elx5_feeds_icontd"><img src="'.$atom_no_icon.'" alt="ATOM" title="ATOM" /></td>'."\n";
					}  else {
						echo '<td class="elx5_feeds_icontd"><a href="'.$link.'rss.xml" title="RSS" target="_blank"><img src="'.$rss_icon.'" alt="RSS" /></a></td>'."\n";
						echo '<td class="elx5_feeds_icontd"><a href="'.$link.'atom.xml" title="ATOM" target="_blank"><img src="'.$atom_icon.'" alt="ATOM" /></a></td>'."\n";
					}
					echo "</tr>\n";
				}
			}
		}
		echo "</table>\n";
		$this->wrapperEnd('feeds');
	}


	/*****************************/
	/* SHOW TAGGED ARTICLES LIST */
	/*****************************/
	public function showTagArticles($rows, $tag, $params) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eFiles = eFactory::getFiles();
		$eDate = eFactory::getDate();

		$c = (!$rows) ? 0 : count($rows);
		$this->wrapperStart('tags', 0);
		echo '<h2>'.sprintf($eLang->get('ARTICLES_TAGGED'), $tag)."</h2>\n";
		echo '<p>'.sprintf($eLang->get('ARTS_FOUND_TAG'), '<strong>'.$c.'</strong>')."</p>\n";
		if (!$rows) {
			echo '<div class="elx_back">'."\n";
			echo '<a href="javascript:void(null);" onclick="javascript:window.history.go(-1);" title="'.$eLang->get('BACK').'">'.$eLang->get('BACK')."</a>\n";
			echo "</div>\n";
			$this->wrapperEnd('tags');
			return;
		}

		foreach ($rows as $row) {
			$link = $elxis->makeURL($row->link);
			if ((trim($row->image) == '') || !file_exists(ELXIS_PATH.'/'.$row->image)) {
				$imgbox = '<figure class="elx5_content_imagebox elx5_content_imageboxtl">'."\n";
				$imgbox .= '<a href="'.$link.'" title="'.$row->title.'">';
				$imgbox .= '<img src="'.$elxis->secureBase().'/templates/system/images/nopicture_article.jpg" alt="'.$row->title.'" />'; 
				$imgbox .= "</a>\n";
				$imgbox .= "</figure>\n";
			} else {
				$imgfile = $elxis->secureBase().'/'.$row->image;
				$file_info = $eFiles->getNameExtension($row->image);
				if (file_exists(ELXIS_PATH.'/'.$file_info['name'].'_thumb.'.$file_info['extension'])) {
					$imgfile = $elxis->secureBase().'/'.$file_info['name'].'_thumb.'.$file_info['extension'];
				}
				unset($file_info);
				$imgbox = '<figure class="elx5_content_imagebox elx5_content_imageboxtl">'."\n";
				$imgbox .= '<a href="'.$link.'" title="'.$row->title.'"><img src="'.$imgfile.'" alt="'.$row->title.'" /></a>'."\n";
				$imgbox .= "</figure>\n";
			}
			echo '<div class="elx5_artbox elx5_artboxtl" data-short="1">'."\n";
			echo $imgbox;
			echo '<div class="elx5_artbox_inner">'."\n";
			echo '<h3><a href="'.$link.'" title="'.$row->title.'">'.$row->title.'</a></h3>'."\n";
			echo '<div class="elx5_dateauthor">';
			echo '<time datetime="'.$row->created.'">'.$eDate->formatDate($row->created, $eLang->get('DATE_FORMAT_4')).'</time>';
			if ($row->catid > 0) {
				$link = $elxis->makeURL($row->catlink);
				echo ' '.$eLang->get('IN').' <a href="'.$link.'" title="'.$row->category.'">'.$row->category.'</a>';
			}
			echo "</div>\n";
			echo "</div>\n";
			echo '<div class="elx5_artbox_inner">'."\n";
			if (trim($row->subtitle) != '') {
				echo '<p>'.$row->subtitle."</p>\n";
			}
			echo '<div class="clear"></div>'."\n";
			echo "</div>\n";//elx5_artbox_inner
			echo "</div>\n";
		}

		echo '<div class="elx_back">'."\n";
		echo '<a href="javascript:void(null);" onclick="javascript:window.history.go(-1);" title="'.$eLang->get('BACK').'">'.$eLang->get('BACK')."</a>\n";
		echo "</div>\n";
		$this->wrapperEnd('tags');
	}


	/****************************/
	/* SEND TO FRIEND HTML FORM */
	/****************************/
	public function sendToFriendHTML($row, $data, $errormsg='', $successmsg='') {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$action = $elxis->makeURL('content:send-to-friend.html', 'inner.php');

		elxisLoader::loadFile('includes/libraries/elxis/form5.class.php');

		echo '<div class="elx5_mpad" id="sendtofriendwrap">'."\n";
		echo '<h3>'.$row->title."</h3>\n";
		echo '<p>'.$eLang->get('SENT_ARTICLE_FRIEND')."</p>\n";
		if ($errormsg != '') {
			echo '<div class="elx5_error">'.$errormsg."</div>\n";
		} elseif ($successmsg != '') {
			echo '<div class="elx5_success">'.$successmsg."</div>\n";
		}

		$form = new elxis5Form(array('idprefix' => 'sf', 'labelclass' => 'elx5_label', 'sideclass' => 'elx5_labelside'));
		$form->openForm(array('name' => 'fmsendfriend', 'method' => 'post', 'action' => $action, 'id' => 'fmsendfriend'));
		$form->openFieldset($eLang->get('EMAIL_TO_FRIEND'));
		$form->addText('sender_name', $data->sender_name, $eLang->get('YOUR_NAME'), array('required' => 'required'));
		$form->addEmail('sender_email', $data->sender_email, $eLang->get('YOUR_EMAIL'), array('required' => 'required', 'dir' => 'ltr'));
		$form->addText('friend_name', $data->friend_name, $eLang->get('FRIEND_NAME'), array('required' => 'required'));
		$form->addEmail('friend_email', $data->friend_email, $eLang->get('FRIEND_EMAIL'), array('required' => 'required', 'dir' => 'ltr'));
		if ($elxis->getConfig('CAPTCHA') != 'NONE') {
			if ($elxis->getConfig('CAPTCHA') == 'MATH') {
				$form->addCaptcha('seccode', '', array('autocomplete' => 'off'));
			} else {
				$form->addNoRobot();
			}
		}
		$form->addHidden('article_id', $row->id);
		$form->addToken('fmsendfriend');
		$form->addHTML('<div class="elx5_dspace">');
		$form->addButton('sbmsf', $eLang->get('SEND'), 'submit');
		$form->addHTML('</div>');
		$form->closeFieldset();
		$form->closeForm();

		echo "</div>\n";
	}


	/****************/
	/* ARCHIVE HTML */
	/****************/
	public function archiveHTML($rows, $year, $month, $day, $page, $maxpage, $total, $params, $title) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eFiles = eFactory::getFiles();
		$eDate = eFactory::getDate();

		$this->wrapperStart('archive', 0);
		echo '<h2>'.$title."</h2>\n";
		if (!$rows) {
			echo '<div class="elx5_warning">'.$eLang->get('NO_RESULTS')."</div>\n";
			$this->wrapperEnd('archive');
			return;
		}

		$show_image = (int)$params->get('arc_image', 1);
		$show_subtitle = (int)$params->get('arc_subtitle', 1);
		$show_intro = (int)$params->get('arc_intro', 0);

		$subclass = ($show_intro == 1) ? ' class="elx5_content_subtitle"' : '';
		foreach ($rows as $row) {
			$imgbox = '';
			if ($show_image == 1) {
				$alt = ($row->caption != '') ? $row->caption : $row->title;
				if ((trim($row->image) == '') || !file_exists(ELXIS_PATH.'/'.$row->image)) {
					$imgbox = '<figure class="elx5_content_imagebox elx5_content_imageboxtl">'."\n";
					$imgbox .= '<a href="'.$row->link.'" title="'.$row->title.'">';
					$imgbox .= '<img src="'.$elxis->secureBase().'/templates/system/images/nopicture_article.jpg" alt="'.$alt.'" />'; 
					$imgbox .= "</a>\n";
					$imgbox .= "</figure>\n";
				} else {
					$imgfile = $elxis->secureBase().'/'.$row->image;
					$file_info = $eFiles->getNameExtension($row->image);
					if (file_exists(ELXIS_PATH.'/'.$file_info['name'].'_thumb.'.$file_info['extension'])) {
						$imgfile = $elxis->secureBase().'/'.$file_info['name'].'_thumb.'.$file_info['extension'];
					}
					unset($file_info);
					$imgbox = '<figure class="elx5_content_imagebox elx5_content_imageboxtl">'."\n";
					$imgbox .= '<a href="'.$row->link.'" title="'.$row->title.'"><img src="'.$imgfile.'" alt="'.$alt.'" /></a>'."\n";
					$imgbox .= "</figure>\n";
				}
			}

			echo '<div class="elx5_artbox elx5_artboxtl" data-short="1">'."\n";
			echo $imgbox;
			echo '<div class="elx5_artbox_inner">'."\n";
			echo '<h3><a href="'.$row->link.'" title="'.$row->title.'">'.$row->title.'</a></h3>'."\n";
			echo '<div class="elx5_dateauthor">';
			echo '<time datetime="'.$row->created.'">'.$eDate->formatDate($row->created, $eLang->get('DATE_FORMAT_4')).'</time>';
			if ($row->catid > 0) {
				$pos = strrpos($row->link, '/') + 1;
				$catlink = substr($row->link, 0, $pos);
				echo ' '.$eLang->get('IN').' <a href="'.$catlink.'" title="'.$row->category.'">'.$row->category.'</a>';
			}
			echo "</div>\n";
			echo "</div>\n";
			echo '<div class="elx5_artbox_inner">'."\n";
			if (trim($row->subtitle) != '') { echo '<p'.$subclass.'>'.$row->subtitle."</p>\n"; }
			if ($show_intro == 1) {
				$txt = strip_tags($row->introtext);
				if ($txt != '') { echo '<p>'.$txt."</p>\n"; }
			}
			echo '<div class="clear"></div>'."\n";
			echo "</div>\n";//elx5_artbox_inner
			echo "</div>\n";
		}

		if ($maxpage > 1) {
			$link = 'content:archive/';
			if ($year > 0) { $link .= $year.'/'; }
			if ($month > 0) { $link .= sprintf("%02d", $month).'/'; }
			if ($day > 0) { $link .= sprintf("%02d", $day).'/'; }
			$linkbase = $elxis->makeURL($link);
			echo '<div class="elx5_vspace">'."\n";
			echo $elxis->obj('html')->pagination($linkbase, $page, $maxpage);
			echo "</div>\n";
		}

		$this->wrapperEnd('archive');
	}

}

?>