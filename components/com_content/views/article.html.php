<?php 
/**
* @version		$Id: article.html.php 2329 2020-01-31 19:32:29Z IOS $
* @package		Elxis
* @subpackage	Component Content
* @copyright	Copyright (c) 2006-2020 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class articleContentView extends contentView {


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		$this->inc = rand(100,500);
		parent::__construct();
	}


	/*******************/
	/* DISPLAY ARTICLE */
	/*******************/
	public function showArticle($row, $params, $chained=null, $comments=null, $print=0, $related=null) {
		if ((int)$params->get('popup_window') == 1) {
			eFactory::getDocument()->loadMediabox();
		}

		$this->wrapperStart('article', $row->id);

		$this->renderArticleTop($row, $params, $print);

		echo $this->makeImageBox($row, $params);

		if ($params->get('art_hidesubtitle', 0) == 0) {
			if (trim($row->subtitle) != '') { echo '<p class="elx5_content_subtitle">'.$row->subtitle."</p>\n"; }
		}

		echo $row->text."\n";
		echo '<div class="clear"></div>'."\n";

		$this->renderArticleBottom($row, $params, $related);
		if ($print == 0) {
			$this->processComments($comments, $row, $params);
			$this->renderChainedArticles($chained, $params);
		}
		$this->wrapperEnd('article');
	}


	/*******************************/
	/* SHOW COMMENTS LIST AND FORM */
	/*******************************/
	private function processComments($comments, $row, $params) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eDate = eFactory::getDate();

		if ((int)$params->get('comments') <> 1) { return; }
		$comments_src = (int)$params->get('comments_src');
		if ($comments_src == 1) { $comments_src = 0; }//BBcode deprecated as of 5.0
		if ($comments_src == 2) {
			echo '<div id="disqus_thread"></div>'."\n";
			return;
		}

		$htmlHelper = $elxis->obj('html');

		eFactory::getDocument()->addScriptLink($elxis->secureBase().'/components/com_content/js/content.js');

		echo '<h3>'.$eLang->get('COMMENTS')."</h3>\n";

		echo '<ul class="elx5_comments_box" id="elx_comments_list" data-tools="'.$elxis->makeURL('content:contenttools', 'inner.php').'" data-lngsure="'.$eLang->get('AREYOUSURE').'" data-lngnocomments="'.$eLang->get('NO_COMMENTS_ARTICLE').'">'."\n";
		if ($comments) {
			$canmail = ($elxis->acl()->getLevel() >= 70) ? true : false;
			$canpub = ($elxis->acl()->check('com_content', 'comments', 'publish') == 2) ? true : false;
			$candel = ($elxis->acl()->check('com_content', 'comments', 'delete') == 2) ? true : false;

			foreach ($comments as $comment) {
				$avatar = $elxis->obj('avatar')->getAvatar($comment->avatar, 50, 1, $comment->email);
				$msgcss = ($comment->published == 1) ? 'elx5_comment_message' : 'elx5_comment_message_unpub';

				echo '<li id="elx_comment_'.$comment->id.'">'."\n";
				echo '<div class="elx5_comment_avatar"><img src="'.$avatar.'" alt="'.$comment->author.'" title="'.$comment->author.'" /></div>'."\n";
				echo '<div class="elx5_comment_main">'."\n";
				echo '<div class="elx5_comment_top">'."\n";
				echo '<div class="elx5_comment_author">'.$comment->author."</div>\n";
				echo '<time class="elx5_comment_date" datetime="'.$comment->created.'">'.$eDate->formatDate($comment->created, $eLang->get('DATE_FORMAT_5'))."</time>\n";
				echo "</div>\n";//elx5_comment_top
				echo '<div class="'.$msgcss.'" id="elx_comment_message_'.$comment->id.'">'."\n";
				echo nl2br(strip_tags($comment->message));
				echo "</div>\n";
				if ($canmail || $candel || ($canpub && ($comment->published == 0))) {
					echo '<div class="elx5_comment_actions">'."\n";
					if ($canmail) {
						echo '<a href="mailto:'.$comment->email.'" title="'.$comment->email.'"><i class="fas fa-envelope"></i><span class="elx5_mobhide"> e-mail</span></a>'."\n";
					}
					if ($canpub && ($comment->published == 0)) {
						echo '<a href="javascript:void(null);" id="elx_comment_publish_'.$comment->id.'" onclick="elx5PublishComment('.$comment->id.');" title="'.$eLang->get('PUBLISH').'">';
						echo '<i class="fas fa-check-square"></i><span class="elx5_mobhide"> '.$eLang->get('PUBLISH').'</span></a>'."\n";
					}
					if ($candel) {
						echo '<a href="javascript:void(null);" onclick="elx5DeleteComment('.$comment->id.');" title="'.$eLang->get('DELETE').'">';
						echo '<i class="fas fa-trash"></i><span class="elx5_mobhide"> '.$eLang->get('DELETE').'</span></a>'."\n";
					}
					echo "</div>\n";
				}
				echo "</div>\n";//elx5_comment_main
				echo "</li>\n";
			}
		} else {
			echo '<li id="elx_comment_0" class="elx5_nocomments">'.$eLang->get('NO_COMMENTS_ARTICLE')."</li>\n";
		}
		echo "</ul>\n";

		echo $htmlHelper->pageLoader('pgloadcomments');

		$allow_comment = (int)$elxis->acl()->check('com_content', 'comments', 'post');
		if (!$allow_comment) {
			if ($elxis->user()->gid == 7) {
				$link = $elxis->makeURL('user:login/');
				echo '<p>'.$eLang->get('NALLOW_POST_COMMENTS');
				echo ' <a href="'.$link.'" title="'.$eLang->get('LOGIN').'" rel="nofollow">'.$eLang->get('PLEASE_LOGIN').'</a>';
				echo "</p>\n";
			} else {
				echo '<p>'.$eLang->get('NALLOW_POST_COMMENTS')."</p>\n";
			}
			return;
		}

		elxisLoader::loadFile('includes/libraries/elxis/form5.class.php');
		$action = $elxis->makeURL('content:contenttools', 'inner.php');
		$form = new elxis5Form(array('idprefix' => 'pcom', 'labelclass' => 'elx5_label', 'sideclass' => 'elx5_labelside'));
		//echo '<h3>'.$eLang->get('POST_COMMENT')."</h3>\n";
		$form->openForm(array('name' => 'fmpostcomment', 'method' => 'post', 'action' => $action, 'id' => 'fmpostcomment', 'onsubmit' => 'return false;'));
		$form->openFieldset($eLang->get('POST_COMMENT'));
		if ((int)$elxis->user()->uid < 1) {
			if ($elxis->user()->gid == 6) {
				$name = eUTF::trim($elxis->user()->firstname.' '.$elxis->user()->lastname);
				if ($name == '') { $name = eUTF::trim($elxis->user()->uname); }
				if ($name == '') {
					$form->addText('author', $name, $eLang->get('NAME'), array('required' => 'required', 'maxlength' => 60));
				}
				if (trim($elxis->user()->email) == '') {
					$form->addEmail('email', '', $eLang->get('EMAIL'), array('required' => 'required', 'tip' => $eLang->get('EMAIL_NOT_PUBLISH')));
				}
			} else {
				$form->addText('author', '', $eLang->get('NAME'), array('required' => 'required', 'maxlength' => 60));
				$form->addEmail('email', '', $eLang->get('EMAIL'), array('required' => 'required', 'tip' => $eLang->get('EMAIL_NOT_PUBLISH')));
			}
		}

		$form->addTextarea('message', '', $eLang->get('YOUR_MESSAGE'), array('required' => 'required'));

		if ($elxis->getConfig('CAPTCHA') != 'NONE') {
			if ($elxis->getConfig('CAPTCHA') == 'MATH') {
				$form->addCaptcha('comseccode');
			} else {
				$form->addNoRobot('comseccode');
			}
		}

		$form->addHidden('id', $row->id);
		$form->addToken('fmpostcomment');
		$form->addHTML('<div class="elx5_invisible" id="postcommentreply"></div>');
		$form->addButton('sbmpc', $eLang->get('SUBMIT'), 'button', array('onclick' => 'elx5PostComment();', 'class' => 'elx5_btn elx5_sucbtn', 'sidepad' => 1));
		$form->closeFieldset();
		$form->closeForm();
	}


	/*************************/
	/* RENDER ARTICLE'S TAGS */
	/*************************/
	private function renderTags($row) {
		if (count($row->keywords['tags']) == 0) { return; }
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		
		echo '<div class="elx_tags_box">'."\n";
		echo '<span>'.$eLang->get('TAGS').":</span> \n";
		foreach ($row->keywords['tags'] as $tag) {
			$link = $elxis->makeURL('tags.html?tag='.urlencode($tag));
			$title = sprintf($eLang->get('ARTICLES_TAGGED'), $tag);
			echo '<a href="'.$link.'" title="'.$title.'">'.$tag."</a> \n";
		}
		echo "</div>\n";
	}


	/************************/
	/* RENDER ARTICLE'S TOP */
	/************************/
	private function renderArticleTop($row, $params, $print=0) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eDoc = eFactory::getDocument();

		$art_hidetitle = (int)$params->get('art_hidetitle', 0);
		if ($art_hidetitle == 1) { return; }

		$art_dateauthor = (int)$params->get('art_dateauthor', 6);
		$art_print = (int)$params->get('art_print', 1);
		$art_email = (int)$params->get('art_email', 1);
		$art_twitter = (int)$params->get('art_twitter', 1);
		$art_facebook = (int)$params->get('art_facebook', 1);
		if ($print == 1) { $art_print = 1; $art_email = 0; $art_twitter = 0; $art_facebook = 0; }
		$numicons = $art_print + $art_email + $art_twitter + $art_facebook;

		echo '<div class="elx5_article_header">'."\n";

		if ($numicons > 0) {
			echo '<div class="elx5_zero">'."\n";
			echo '<div class="elx5_content_icons" data-icons="'.$numicons.'">'."\n";
			if ($art_print == 1) {
				if ($print == 1) {
					echo '<a href="javascript:void(null);" title="'.$eLang->get('PRINT').'" onclick="javascript:window.print();" class="elx5_lmobhide">';
				} else {
					$link = $elxis->makeURL($row->link.'?print=1', 'inner.php');
					echo '<a href="javascript:void(null);" title="'.$eLang->get('PRINTABLE_VERSION').'" onclick="elxPopup(\''.$link.'\', 800, 600);" class="elx5_lmobhide">';
				}
				echo '<i class="fas fa-print"></i></a>'."\n";
			}
			if ($art_email == 1) {
				$link = $elxis->makeURL('send-to-friend.html?id='.$row->id, 'inner.php');
				if ((int)$params->get('popup_window') == 1) {
					$eDoc->loadMediabox();
					echo '<a href="'.$link.'" data-mediabox="send-to-friend" data-iframe="true" data-width="620" data-height="500" title="'.$eLang->get('EMAIL_TO_FRIEND').'" id="artmailfriend'.$row->id.'" rel="nofollow">';
				} else {
					echo '<a href="javascript:void(null);" title="'.$eLang->get('EMAIL_TO_FRIEND').'" onclick="elxPopup(\''.$link.'\', 600, 450);" rel="nofollow">';
				}
				echo '<i class="fas fa-envelope"></i></a>'."\n";
			}
			if ($art_twitter == 1) {
				$artlink = $elxis->makeURL('content:'.$row->link);
				$link = 'https://twitter.com/intent/tweet?text='.urlencode($row->title).'&url='.urlencode($artlink);
				echo '<a href="javascript:void(null);" title="Share on Twitter" onclick="elxPopup(\''.$link.'\', 700, 450);">';
				echo '<i class="fab fa-twitter"></i></a>'."\n";
			}
			if ($art_facebook == 1) {
				$artlink = $elxis->makeURL('content:'.$row->link);
				$link = 'https://www.facebook.com/sharer/sharer.php?u='.urlencode($artlink).'&t='.urlencode($row->title);
				echo '<a href="javascript:void(null);" title="Share on Facebook" onclick="elxPopup(\''.$link.'\', 700, 450);">';
				echo '<i class="fab fa-facebook-f"></i></a>'."\n";
			}
			echo "</div>\n";
			echo '<h1 class="elx5_article_h1i">'.$row->title."</h1>\n";
			echo "</div>\n";
		} else {
			echo '<h1>'.$row->title."</h1>\n";
		}

		if ($art_dateauthor > 0) {
			if ((int)$params->get('art_dateauthor_pos', 0) == 0) {
				$allowed_any_profile = ((int)eFactory::getElxis()->acl()->check('com_user', 'profile', 'view') == 2) ? true : false;
				$dateauthor = $this->getDateAuthor($row, $art_dateauthor, $allowed_any_profile);
				if ($dateauthor != '') {
					if ($art_dateauthor == 7) {
						echo $dateauthor;
					} else {
						echo '<div class="elx_dateauthor">'.$dateauthor.'</div>'."\n";
					}
				}
			}
		}

		echo "</div>\n";//elx5_article_header
	}


	/***************************/
	/* RENDER ARTICLE'S BOTTOM */
	/***************************/
	private function renderArticleBottom($row, $params, $related=null) {
		$art_dateauthor = (int)$params->get('art_dateauthor', 6);
		if ($art_dateauthor > 0) {
			if ((int)$params->get('art_dateauthor_pos', 0) == 1) {
				$allowed_any_profile = ((int)eFactory::getElxis()->acl()->check('com_user', 'profile', 'view') == 2) ? true : false;
				$dateauthor = $this->getDateAuthor($row, $art_dateauthor, $allowed_any_profile);
				if ($dateauthor != '') {
					if ($art_dateauthor == 7) {
						echo $dateauthor;
					} else {
						echo '<div class="elx_dateauthor">'.$dateauthor.'</div>'."\n";
					}
				}
			}
		}

		if ((int)$params->get('art_hits', 1) == 1) {
			$txt = sprintf(eFactory::getLang()->get('READ_TIMES'), '<span>'.$row->hits.'</span>');
			echo '<div class="elx_hits_box">'.$txt."</div>\n";
		}

		$this->renderRelated($related);

		if ((int)$params->get('art_tags') == 1) {
			$this->renderTags($row);
		}
	}


	/*********************************/
	/* RENDER PREVIOUS/NEXT ARTICLES */
	/*********************************/
	private function renderChainedArticles($chained, $params) {
		if (!$chained) { return; }
		if (($chained['previous'] === null) && ($chained['next'] === null)) { return; }

		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$eFiles = eFactory::getFiles();

		$ctg_img_empty = (int)$params->get('ctg_img_empty', 1);

		echo '<div class="elx5_tlspace">'."\n";
		echo '<div class="elx5_2colwrap">'."\n";
		echo '<div class="elx5_2colbox">'."\n";
		echo '<div class="elx_chain_previous">'."\n";
		if ($chained['previous'] !== null) {
			$link = $elxis->makeURL($chained['previous']->link);
			$imgfile = '';
			if ((int)$params->get('art_chain') == 2) {
				if ((trim($chained['previous']->image) == '') || !file_exists(ELXIS_PATH.'/'.$chained['previous']->image)) {
					if ($ctg_img_empty == 1) { $imgfile = $elxis->secureBase().'/templates/system/images/nopicture_article.jpg'; }
				} else {
					$imgfile = $elxis->secureBase().'/'.$chained['previous']->image;
					$file_info = $eFiles->getNameExtension($chained['previous']->image);
					if (file_exists(ELXIS_PATH.'/'.$file_info['name'].'_thumb.'.$file_info['extension'])) {
						$imgfile = $elxis->secureBase().'/'.$file_info['name'].'_thumb.'.$file_info['extension'];
					}
					unset($file_info);
				}
			}

			if ($imgfile != '') {
				echo '<a href="'.$link.'" title="'.$chained['previous']->title.'"><img src="'.$imgfile.'" alt="'.$chained['previous']->title.'" />'."</a>\n"; 
			}
			echo '<div class="elx_chain_title">'.$eLang->get('PREVIOUS_ARTICLE')."</div>\n";
			echo '<a href="'.$link.'" title="'.$chained['previous']->title.'">'.$chained['previous']->title."</a>\n";
		}
		echo "</div>\n";
		echo "</div>\n";
		echo '<div class="elx5_2colbox">'."\n";
		echo '<div class="elx_chain_next">'."\n";
		if ($chained['next'] !== null) {
			$link = $elxis->makeURL($chained['next']->link);
			$imgfile = '';
			if ((int)$params->get('art_chain') == 2) {
				if ((trim($chained['next']->image) == '') || !file_exists(ELXIS_PATH.'/'.$chained['next']->image)) {
					if ($ctg_img_empty == 1) { $imgfile = $elxis->secureBase().'/templates/system/images/nopicture_article.jpg'; }
				} else {
					$imgfile = $elxis->secureBase().'/'.$chained['next']->image;
					$file_info = $eFiles->getNameExtension($chained['next']->image);
					if (file_exists(ELXIS_PATH.'/'.$file_info['name'].'_thumb.'.$file_info['extension'])) {
						$imgfile = $elxis->secureBase().'/'.$file_info['name'].'_thumb.'.$file_info['extension'];
					}
					unset($file_info);
				}
			}

			if ($imgfile != '') {
				echo '<a href="'.$link.'" title="'.$chained['next']->title.'"><img src="'.$imgfile.'" alt="'.$chained['next']->title.'" />'."</a>\n";
			}
			echo '<div class="elx_chain_title">'.$eLang->get('NEXT_ARTICLE')."</div>\n";
			echo '<a href="'.$link.'" title="'.$chained['next']->title.'">'.$chained['next']->title."</a>\n";
		}
		echo "</div>\n";
		echo "</div>\n";
		//echo '<div class="clear">'."</div>\n";
		echo "</div>\n";
		echo "</div>\n";
	}


	/***************************/
	/* RENDER RELATED ARTICLES */
	/***************************/
	private function renderRelated($related) {
		if (!$related) { return; }

		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		echo '<h3 class="elx_links_box_title">'.$eLang->get('READ_ALSO').'</h3>';
		echo '<ul class="elx5_links_box">'."\n";
		foreach ($related as $rel) {
			if ($rel->catid > 0) {
				$link = $elxis->makeURL('content:'.$rel->seolink.$rel->seotitle.'.html');
			} else {
				$link = $elxis->makeURL('content:'.$rel->seotitle.'.html');
			}
			echo '<li><a href="'.$link.'">'.$rel->title."</a></li>\n";
		}
		echo "</ul>\n";
	}


	/********************************/
	/* GENERATE ARTICLE'S IMAGE BOX */
	/********************************/
	private function makeImageBox($row, $params) {
		if ((trim($row->image) == '') || !file_exists(ELXIS_PATH.'/'.$row->image)) { return ''; }
		$art_img = (int)$params->get('art_img', 2);
		if ($art_img == 0) { return ''; }
		if ($art_img < 1) { $art_img = 2; }
		if ($art_img == 1) { $art_img = 2; } //MEDIUM_IMG_TOP ==> LARGE_IMG_TOP (Elxis 5.x)

		$elxis = eFactory::getElxis();

		$imgfile = $elxis->secureBase().'/'.$row->image;
		$lightbox = false;
		if ($art_img < 4) {
			$lightbox = true;
			$file_info = eFactory::getFiles()->getNameExtension($row->image);
			if (file_exists(ELXIS_PATH.'/'.$file_info['name'].'_medium.'.$file_info['extension'])) {
				$imgfile = $elxis->secureBase().'/'.$file_info['name'].'_medium.'.$file_info['extension'];
			}
		}

		switch ($art_img) {
			case 2: $addon = ' elx5_content_imageboxml'; break;
			case 3: $addon = ' elx5_content_imageboxmr'; break;
			case 4: $addon = ' elx5_content_imageboxlt'; break;
			case 5: $addon = ' elx5_content_imageboxll'; break;
			case 6: $addon = ' elx5_content_imageboxlr'; break;
			default: $addon = ''; break;
		}

		$imgbox = '<figure class="elx5_content_imagebox'.$addon.'">'."\n";
		$alt = (trim($row->caption) != '') ? $row->caption : $row->title;
		if ($lightbox) {
			if ((int)$params->get('popup_window') == 0) { eFactory::getDocument()->loadMediabox(); }
			$imgbox .= '<a href="'.$elxis->secureBase().'/'.$row->image.'" title="'.$alt.'" data-mediabox="article-image" data-title="'.$alt.'">';
			$imgbox .= '<img src="'.$imgfile.'" alt="'.$alt.'" />';
			$imgbox .= "</a>\n";
		} else {
			$imgbox .= '<img src="'.$imgfile.'" alt="'.$alt.'" />'."\n"; 
		}
		if (trim($row->caption) != '') { $imgbox .= '<figcaption>'.$row->caption."</figcaption>\n"; }
		$imgbox .= "</figure>\n";

		return $imgbox;
	}


	/********************************************/
	/* GET/FORMAT DATE AN AUTHOR FOR AN ARTICLE */
	/********************************************/
	private function getDateAuthor($article, $type, $allowed=false) {
		$eLang = eFactory::getLang();
		$eDate = eFactory::getDate();

		$dateauthor = '';
		switch($type) {
			case 1:	$dateauthor = '<time datetime="'.$article->created.'">'.$eDate->formatDate($article->created, $eLang->get('DATE_FORMAT_12')).'</time>'; break;
			case 2:
				$dateauthor = '<time datetime="'.$article->created.'">'.$eDate->formatDate($article->created, $eLang->get('DATE_FORMAT_12')).'</time>';
				if ($allowed) {
					$proflink = eFactory::getElxis()->makeURL('user:members/'.$article->created_by.'.html');
					$dateauthor .= ' '.$eLang->get('BY').' <a href="'.$proflink.'" title="'.$article->created_by_name.'">'.$article->created_by_name.'</a>';
				} else {
					$dateauthor .= ' '.$eLang->get('BY').' '.$article->created_by_name;
				}
			break;
			case 3:
				if ($article->modified != '1970-01-01 00:00:00') {
					$dateauthor = $eLang->get('LAST_UPDATE').' <time datetime="'.$article->modified.'">'.$eDate->formatDate($article->modified, $eLang->get('DATE_FORMAT_4')).'</time>';
				}
			break;
			case 4:
				if ($article->modified != '1970-01-01 00:00:00') {
					$dateauthor = $eLang->get('LAST_UPDATE').' <time datetime="'.$article->modified.'">'.$eDate->formatDate($article->modified, $eLang->get('DATE_FORMAT_4')).'</time>';
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
					$dateauthor = $eLang->get('LAST_UPDATE').' <time datetime="'.$article->modified.'">'.$eDate->formatDate($article->modified, $eLang->get('DATE_FORMAT_4')).'</time>';
				} else {
					$dateauthor = '<time datetime="'.$article->created.'">'.$eDate->formatDate($article->created, $eLang->get('DATE_FORMAT_12')).'</time>';
				}
			break;
			case 6:
				if ($article->modified != '1970-01-01 00:00:00') {
					$dateauthor = $eLang->get('LAST_UPDATE').' <time datetime="'.$article->modified.'">'.$eDate->formatDate($article->modified, $eLang->get('DATE_FORMAT_4')).'</time>';
					if (($article->modified_by > 0) && $allowed) {
						$proflink = eFactory::getElxis()->makeURL('user:members/'.$article->modified_by.'.html');
						$dateauthor .= ' '.$eLang->get('BY').' <a href="'.$proflink.'" title="'.$article->modified_by_name.'">'.$article->modified_by_name.'</a>';
					} else {
						$dateauthor .= ' '.$eLang->get('BY').' '.$article->modified_by_name;
					}
				} else {
					$dateauthor = '<time datetime="'.$article->created.'">'.$eDate->formatDate($article->created, $eLang->get('DATE_FORMAT_12')).'</time>';
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