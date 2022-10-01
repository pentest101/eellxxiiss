<?php 
/**
* @version		$Id: generic.php 2415 2021-08-29 17:05:38Z IOS $
* @package		Elxis
* @subpackage	Component Content
* @copyright	Copyright (c) 2006-2021 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class genericContentController extends contentController {


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null, $model=null, $format='') {
		parent::__construct($view, $model, $format);
	}


	/**************************************/
	/* PREPARE TO DISPLAY TAGGED ARTICLES */
	/**************************************/
	public function tags() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eDoc = eFactory::getDocument();

		if (isset($_GET['tag'])) {
			$tag = filter_input(INPUT_GET, 'tag', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
			$pat = "#([\']|[\!]|[\;]|[\"]|[\$]|[\/]|[\#]|[\<]|[\>]|[\*]|[\%]|[\~]|[\`]|[\^]|[\|]|[\\\])#u";
			$tag = eUTF::trim(preg_replace($pat, '', $tag));
			if (eUTF::strlen($tag) < 3) { $tag = ''; }
		} else {
			$tag = '';
		}
		
		if ($tag == '') {
			$pathway = eFactory::getPathway();
			$pathway->addNode($eLang->get('TAGS'));
			$pathway->addNode($eLang->get('ERROR'));
			$eDoc->setTitle($eLang->get('TAGS').' - '.$elxis->getConfig('SITENAME'));
			$this->view->base_errorScreen($eLang->get('NO_TAG_SPECIFIED'), $eLang->get('ERROR'), false, true, true);
			return;
		}

		$rows = $this->loadTagArticles($tag);

		$global_str = (string)$this->model->componentParams();
		elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
		$params = new elxisParameters($global_str, '', 'component');
		if ((int)$params->def('img_thumb_width', 120) < 10) {
			$params->set('img_thumb_width', 120);
		}

		$eDoc->setTitle($eLang->get('TAG').' '.$tag.' - '.$elxis->getConfig('SITENAME'));
		$desc = sprintf($eLang->get('ARTICLES_TAGGED'), $tag);
		$eDoc->setDescription($desc.'. '.$elxis->getConfig('SITENAME'));
		$eDoc->setKeywords(array($tag, $eLang->get('TAGS')));

		$pathway = eFactory::getPathway();
		$pathway->addNode($eLang->get('TAGS'));
		$pathway->addNode($tag);

		$this->view->showTagArticles($rows, $tag, $params);
	}


	/**************************************************/
	/* PREPARE TO DISPLAY LIST OF AVAILABLE XML FEEDS */
	/**************************************************/
	public function feeds() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eDoc = eFactory::getDocument();

		$rows = $this->loadFeedCategories();

		$eDoc->setTitle($eLang->get('RSS_ATOM_FEEDS_CENTRAL').' - '.$elxis->getConfig('SITENAME'));
		$desc = sprintf($eLang->get('XML_FEEDS_FROM'), $elxis->getConfig('SITENAME'));
		$eDoc->setDescription($desc);
		$eDoc->setKeywords(array('RSS', 'ATOM', 'XML', 'news feeds', 'syndication', 'feeds', $eLang->get('RSS_ATOM_FEEDS_CENTRAL')));

		$pathway = eFactory::getPathway();
		$pathway->addNode($eLang->get('RSS_ATOM_FEEDS_CENTRAL'));

		$this->view->feedsCentral($rows);
	}


	/***********************************/
	/* DISPLAY SITE FEED IN RSS FORMAT */
	/***********************************/
	public function rssfeed() {
		$this->viewXMLsite('rss');
	}


	/************************************/
	/* DISPLAY SITE FEED IN ATOM FORMAT */
	/************************************/
	public function atomfeed() {
		$this->viewXMLsite('atom');
	}


	/****************************************/
	/* PREPARE TO DISPLAY XML FEED FOR SITE */
	/****************************************/
	private function viewXMLsite($type='rss') {
		$elxis = eFactory::getElxis();
		$eFiles = eFactory::getFiles();
		$eLang = eFactory::getLang();

		$feeditems = 10;
		$cachefile = $type.'-'.$eLang->currentLang().'.xml';
		$feed_cache = 14400; //4 hours
		$repo_path = rtrim($elxis->getConfig('REPO_PATH'), '/');
		if ($repo_path == '') { $repo_path = ELXIS_PATH.'/repository'; }

		if (file_exists($repo_path.'/cache/feeds/'.$cachefile)) {
			$ts = filemtime($repo_path.'/cache/feeds/'.$cachefile);
			if (($ts + $feed_cache) > time()) {
				if (@ob_get_length() > 0) { @ob_end_clean(); }
				@header("Content-type:text/xml; charset=utf-8");
				echo file_get_contents($repo_path.'/cache/feeds/'.$cachefile);
				exit;
			}
		}

		$articles = $this->loadFeedArticles(10);

		elxisLoader::loadFile('includes/libraries/elxis/feed.class.php');
		$feed = new elxisFeed($type);
		if (!file_exists($repo_path.'/cache/feeds/')) {
			$eFiles->createFolder('cache/feeds/', 0755, true);
		}

		$ttl = intval($feed_cache / 60);
		$feed->setTTL($ttl);

		$channel_title = $elxis->getConfig('SITENAME');
		$channel_link = $elxis->getConfig('URL');

		$feed->addChannel($channel_title, $channel_link, $elxis->getConfig('METADESC'));

		if ($articles) {
			$ePlugin = eFactory::getPlugin();
			foreach ($articles as $article) {
				$enclosure = null;
				$itemdesc = '';
				if (trim($article->subtitle) != '') {
					$itemdesc = '<strong>'.$article->subtitle.'</strong><br />'."\n";
				}
				if (trim($article->introtext) != '') {
					$desc = $ePlugin->removePlugins($article->introtext);
					$desc = strip_tags($desc);
					$itemdesc .= $desc;
				}

				if (trim($article->image != '')) {
					$enclosure = $article->image;
					$file_info = $eFiles->getNameExtension($article->image);
					if (file_exists(ELXIS_PATH.'/'.$file_info['name'].'_thumb.'.$file_info['extension'])) {
						$enclosure = $file_info['name'].'_thumb.'.$file_info['extension'];
						$itemdesc = '<img style="margin:5px; float:left;" src="'.$elxis->getConfig('URL').'/'.$enclosure.'" alt="'.$article->title.'" /> '.$itemdesc;
					} elseif (!file_exists(ELXIS_PATH.'/'.$article->image)) {
						$enclosure = null;
					}
				}

				if ($article->catid > 0) {
					$link = $elxis->makeURL($article->seolink.$article->seotitle.'.html');
				} else {
					$link = $elxis->makeURL($article->seotitle.'.html');
				}

				$feed->addItem(
					$article->title,
					$itemdesc,
					$link,
					strtotime($article->created),
					$article->created_by_name,
					$enclosure
				);
			}
		}

		$action = ($feed_cache > 0) ? 'saveshow' : 'show';
		$feed->makeFeed($action, 'cache/feeds/'.$cachefile);
	}


	/***************************************/
	/* SEND AN ARTICLE TO A FRIEND (POPUP) */
	/***************************************/
	public function sendtofriend() {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$eSession = eFactory::getSession();

		eFactory::getDocument()->setTitle($eLang->get('EMAIL_TO_FRIEND'));
		eFactory::getDocument()->setDescription($eLang->get('SENT_ARTICLE_FRIEND'));

		if (isset($_POST['article_id'])) {
			$id = (int)$_POST['article_id'];
		} else if (isset($_GET['id'])) {
			$id = (int)$_GET['id'];
		} else {
			$id = 0;
		}

		if ($id < 1) {
			$this->view->base_errorScreen($eLang->get('ARTICLE_NOT_FOUND'));
			return;
		}
		
		$row = $this->loadArticle('', $id);
		if (!$row) {
			$this->view->base_errorScreen($eLang->get('ARTICLE_NOT_FOUND'));
			return;
		}

		$category_link = '';
		if ($row->catid > 0) {
			$tree = $this->loadCategoryTree($row->catid);
			if (!$tree) {
				$this->view->base_errorScreen($eLang->get('ARTICLE_NOT_FOUND'));
				return;
			}
			$n = count($tree) - 1;
			$category_title = $tree[$n]->title;
			$category_link = $tree[$n]->link;
			$row->link = $category_link.$row->seotitle.'.html';
		} else {
			$row->link = $row->seotitle.'.html';
			$category_title = '';
		}

		$errormsg = '';
		$successmsg = '';
		$data = new stdClass;
		$data->sender_name = ($elxis->user()->firstname != '') ? $elxis->user()->firstname.' '.$elxis->user()->lastname : '';
		$data->sender_email = $elxis->user()->email;
		$data->friend_name = '';
		$data->friend_email = '';
		if (isset($_POST['sbmsf'])) {
			$sess_token = trim($eSession->get('token_fmsendfriend'));
			$token = trim(filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
			if (($token == '') || ($sess_token == '') || ($sess_token != $token)) {
				$errormsg = $eLang->get('REQDROPPEDSEC');
			}

			$captcha = $elxis->obj('captcha');
			$ok = $captcha->validate($elxis->getConfig('CAPTCHA'), 'captcha_seccode', 'seccode', 'norobot', '');
			if (!$ok) {
				$errormsg = $captcha->getError();
			}
			unset($captcha, $ok);

			$data->sender_name = filter_input(INPUT_POST, 'sender_name', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
			if ($data->sender_name == '') { $errormsg = $eLang->get('PROVIDE_YOUR_NAME'); }
			$data->sender_email = filter_input(INPUT_POST, 'sender_email', FILTER_SANITIZE_EMAIL);
			if (!filter_var($data->sender_email, FILTER_VALIDATE_EMAIL)) {
				$errormsg = $eLang->get('INVALIDEMAIL');
			}
			$data->friend_name = filter_input(INPUT_POST, 'friend_name', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
			if ($data->friend_name == '') { $errormsg = $eLang->get('PROVIDE_FRIEND_NAME'); }
			$data->friend_email = filter_input(INPUT_POST, 'friend_email', FILTER_SANITIZE_EMAIL);
			if (!filter_var($data->friend_email, FILTER_VALIDATE_EMAIL)) {
				$errormsg = $eLang->get('INVALIDEMAIL');
			}

			if ($errormsg == '') {
				$ok = $this->sendMailToFriend($row, $data, $category_link, $category_title);
				if (!$ok) {
					$errormsg = 'Could not send email!';
				} else {
					$successmsg = $eLang->get('MSG_SENT_SUCCESS');
					$data->sender_name = '';
					$data->sender_email = '';
					$data->friend_name = '';
					$data->friend_email = '';
				}
			}
		}

		$this->view->sendToFriendHTML($row, $data, $errormsg, $successmsg);
	}


	/************************/
	/* SEND EMAIL TO FRIEND */
	/************************/
	private function sendMailToFriend($row, $data, $category_link='', $category_title='') {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();

		$subject = $eLang->get('INTERESTING_ARTICLE');
		$body = $eLang->get('HI').' '.$data->friend_name.",\n";
		$body .= sprintf($eLang->get('LINK_ARTICLE_INTEREST'), $data->sender_name)."\n\n";
		$body .= $row->title."\n";
		$body .= $elxis->makeURL($row->link)."\n\n";
		if (($category_link != '') && ($category_title != '')) {
			$body .= $eLang->get('CATEGORY').": \t".$category_title."\n";
			$body .= $elxis->makeURL($category_link)."\n\n";
		}
		$body .= $eLang->get('FRIEND_NAME').": \t".$data->sender_name."\n";
		$body .= $eLang->get('FRIEND_EMAIL').": \t".$data->sender_email."\n\n\n";
		$body .= $eLang->get('REGARDS')."\n";
		$body .= $elxis->getConfig('SITENAME')."\n";
		$body .= $elxis->getConfig('URL')."\n\n\n\n";
		$body .= "_______________________________________________________________\n";
		$body .= $eLang->get('NOREPLYMSGINFO');

		$to = $data->friend_email.','.$data->friend_name;
		$ok = $elxis->sendmail($subject, $body, '', null, 'plain', $to);
		return $ok;
	}


	/*****************/
	/* CONTENT TOOLS */
	/*****************/
	public function contenttools() {
		$act = trim(filter_input(INPUT_POST, 'act', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		switch($act) {
			case 'pubcomment': $this->publishComment(); break;
			case 'delcomment': $this->deleteComment(); break;
			case 'postcomment': $this->postComment(); break;
			default:
				$response = array('success' => 0, 'message' => 'Invalid request');
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit;
			break;
		}
	}


	/*******************************/
	/* GENERIC AJAX REQUEST (AJAX) */
	/*******************************/
	public function genericajax() {
		$f = '';
		if (isset($_POST['f'])) {
			$pat = "#([\']|[\!]|[\(]|[\)]|[\;]|[\"]|[\$]|[\#]|[\<]|[\>]|[\*]|[\%]|[\~]|[\`]|[\^]|[\|]|[\{]|[\}]|[\\\])#u";
			$f = trim(filter_input(INPUT_POST, 'f', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
			$f = preg_replace('@^(\/)@', '', $f);

			$f2 = trim(strip_tags(preg_replace($pat, '', $f)));
			$f2 = str_replace('..', '', $f2);
			$f2 = str_replace('\/\/', '', $f2);
		
			if ($f2 != $f) {
				$this->ajaxHeaders('text/plain');
				die('BAD');
			}

			if (strpos($f, 'modules/') === 0) {
				$ok = true;
			} else if (strpos($f, 'components/com_content/plugins/') === 0) {
				$ok = true;
			} else if (strpos($f, 'components/com_user/auth/') === 0) {
				$ok = true;
			} else if (strpos($f, 'components/com_search/engines/') === 0) {
				$ok = true;
			} else if (strpos($f, 'templates/system/') === 0) {
				$ok = false;//not the system template!
			} else if (strpos($f, 'templates/admin/') === 0) {
				$ok = false;//not the administration templates!
			} else if (strpos($f, 'templates/') === 0) {
				$ok = true;
			} else {
				$ok = false;
			}

			if (!$ok) {
				$this->ajaxHeaders('text/plain');
				die('BAD');
			}
			if (!preg_match('@(\.php)$@', $f)) {
				$this->ajaxHeaders('text/plain');
				die('BAD');
			}
			if (!is_file(ELXIS_PATH.'/'.$f) || !file_exists(ELXIS_PATH.'/'.$f)) {
				$this->ajaxHeaders('text/plain');
				die('BAD');
			}
		}

		$this->ajaxHeaders('text/plain');
		if ($f == '') {
			echo 'BAD';
		} else {
			include(ELXIS_PATH.'/'.$f);
		}

		exit;
	}


	/*************************************/
	/* NO ROBOT CAPTCHA GENERATOR (AJAX) */
	/*************************************/
	public function captchagenerator() {
		if (isset($_GET['custom'])) {
			$custom = $_GET['custom'];
		} else if (isset($_POST['custom'])) {
			$custom = $_POST['custom'];
		} else {
			$custom = '';
		}

		$response = array('success' => 0, 'errormsg' => '', 'captchakey' => '');

		if ($custom != '') {
			$filtered = trim(preg_replace("/[^A-Za-z0-9 ]/", '', $custom));
			if ($filtered != $custom) {
				$response['errormsg'] = 'Not acceptable custom parameter!';
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit;
			}
		}

		$captcha = eFactory::getElxis()->obj('captcha');
		$captchakey = $captcha->generate($custom);

		$response['success'] = 1;
		$response['captchakey'] = $captchakey;

		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/**************************/
	/* PUBLISH COMMENT (AJAX) */
	/**************************/
	private function publishComment() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$pubaccess = (int)$elxis->acl()->check('com_content', 'comments', 'publish');
		$id = (isset($_POST['id'])) ? (int)$_POST['id'] : 0;

		$response = array('success' => 0, 'message' => '');

		if ($id < 1) {
			$response['message'] = 'Invalid request';
		} else if ($pubaccess < 1) {
			$response['message'] = $eLang->get('NOTALLOWACTION');
		}
		if ($response['message'] != '') {
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$comment = $this->model->fetchComment($id);
		if (!$comment) {
			$response['message'] = 'The requested comment was not found!';
		} else {
			if ($pubaccess == 1) {
				if (($comment->uid == 0) || ($comment->uid != $elxis->user()->uid)) {
					$response['message'] = $eLang->get('NOTALLOWACTION');
				}
			} elseif ($pubaccess <> 2) { //just in case
				$response['message'] = $eLang->get('NOTALLOWACTION');
			}
		}
		if ($response['message'] != '') {
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$artid = (int)$comment->elid;
		$row = $this->model->fetchArticle('', $artid);
		if (!$row) {
			$response['message'] = $eLang->get('ARTICLE_NOT_FOUND');
		} else {
			if ($row->catid > 0) {
				$tree = $this->model->categoryTree($row->catid);
				if (!$tree) { $response['message'] = $eLang->get('NOTALLOWACCPAGE'); }
			}
		}
		if ($response['message'] != '') {
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		if ($comment->published == 1) {//already published
			$response['success'] = 1;
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		if ($row->catid > 0) {
			$n = count($tree) - 1;
			$article_link = $elxis->makeURL($tree[$n]->link.$row->seotitle.'.html');
			unset($tree);
		} else {
			$article_link = $elxis->makeURL($row->seotitle.'.html');
		}

		$ok = $this->model->publishComment($id);
		if ($ok) {
			$this->notifyPublishComment($comment->author, $comment->email, $row->title, $article_link);
			$response['success'] = 1;
		} else {
			$response['message'] = 'Could not publish comment!';
		}

		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/*************************/
	/* DELETE COMMENT (AJAX) */
	/*************************/
	private function deleteComment() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$delaccess = (int)$elxis->acl()->check('com_content', 'comments', 'delete');
		$id = (isset($_POST['id'])) ? (int)$_POST['id'] : 0;

		$response = array('success' => 0, 'message' => '');
		if ($id < 1) {
			$response['message'] = 'Invalid request';
		} else if ($delaccess < 1) {
			$response['message'] = $eLang->get('NOTALLOWACTION');
		}
		if ($response['message'] != '') {
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$comment = $this->model->fetchComment($id);
		if (!$comment) {
			$response['message'] = 'The requested comment was not found!';
		} else {
			if ($delaccess == 1) {
				if (($comment->uid == 0) || ($comment->uid != $elxis->user()->uid)) {
					$response['message'] = $eLang->get('NOTALLOWACTION');
				}
			} elseif ($delaccess <> 2) { //just in case
				$response['message'] = $eLang->get('NOTALLOWACTION');
			}
		}
		if ($response['message'] != '') {
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$artid = (int)$comment->elid;
		$row = $this->model->fetchArticle('', $artid);
		if (!$row) {
			$response['message'] = $eLang->get('ARTICLE_NOT_FOUND');
		} else {
			if ($row->catid > 0) {
				$tree = $this->model->categoryTree($row->catid);
				if (!$tree) { $response['message'] = $eLang->get('NOTALLOWACCPAGE'); }
			}
		}
		if ($response['message'] != '') {
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit;
		}

		$ok = $this->model->deleteComment($id);
		if ($ok) {
			$response['success'] = 1;
		} else {
			$response['message'] = 'Could not delete comment!';
		}

		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}



	/***********************/
	/* POST COMMENT (AJAX) */
	/***********************/
	private function postComment() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eSession = eFactory::getSession();

		$id = (isset($_POST['id'])) ? (int)$_POST['id'] : 0;
		$isajax = (isset($_POST['rnd'])) ? true : false;
		$token = trim(filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$author = filter_input(INPUT_POST, 'author', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
		$sess_token = trim($eSession->get('token_fmpostcomment'));

		$captcha_errormsg = '';
		$captcha = $elxis->obj('captcha');
		$ok = $captcha->validate($elxis->getConfig('CAPTCHA'), 'captcha_comseccode', 'comseccode', 'comseccode', '');
		if (!$ok) { $captcha_errormsg = $captcha->getError(); }
		unset($captcha);

		$response = array('success' => 0, 'message' => '', 'waitapproval' => 0);

		if ((int)$elxis->acl()->check('com_content', 'comments', 'post') !== 1) {
			$response['message'] = $eLang->get('NALLOW_POST_COMMENTS');
		} else if (($token == '') || ($sess_token == '') || ($sess_token != $token)) {
			$response['message'] = $eLang->get('REQDROPPEDSEC');
		} else if ($captcha_errormsg != '') {
			$response['message'] = $captcha_errormsg;
		} else if ($id < 1) {
			$response['message'] = $eLang->get('ARTICLE_NOT_FOUND');
		}

		if ($response['message'] != '') {
			if ($isajax) {
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit;
			} else {
				exitPage::make('error', 'CCON-0009', $response['message']);
			}
		}

		$row = $this->model->fetchArticle('', $id);
		if (!$row) {
			$response['message'] = $eLang->get('ARTICLE_NOT_FOUND');
			if ($isajax) {
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit;
			} else {
				exitPage::make('404', 'CCON-0010', $response['message']);
			}
		}

		if ($row->catid > 0) {
			$tree = $this->model->categoryTree($row->catid);
			if (!$tree) {
				$response['message'] = $eLang->get('NOTALLOWACCPAGE');
				if ($isajax) {
					$this->ajaxHeaders('application/json');
					echo json_encode($response);
					exit;
				} else {
					exitPage::make('404', 'CCON-0011', $response['message']);
				}
			}
		}

		$params = $this->combinedArticleParams($row->params, $row->catid);
		$comallowed = (int)$params->get('comments', 0);
		if ($comallowed !== 1) {
			$response['message'] = $eLang->get('COMMENTS_NALLOW_ARTICLE');
			if ($isajax) {
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit;
			} else {
				exitPage::make('403', 'CCON-0012', $response['message']);
			}
		}

		if ($row->catid > 0) {
			$n = count($tree) - 1;
			$article_link = $elxis->makeURL($tree[$n]->link.$row->seotitle.'.html');
			unset($tree);
		} else {
			$article_link = $elxis->makeURL($row->seotitle.'.html');
		}

		$pat = "#([\!]|[\(]|[\)]|[\;]|[\"]|[\$]|[\#]|[\<]|[\>]|[\*]|[\%]|[\~]|[\`]|[\^]|[\|]|[\{]|[\}]|[\\\])#u";
		$uid = (int)$elxis->user()->uid;
		if ($uid  > 0) {
			$email = $elxis->user()->email;
			if ($elxis->getConfig('REALNAME') == 1) {
				$author = $elxis->user()->firstname.' '.$elxis->user()->lastname;
			} else {
				$author = $elxis->user()->uname;
			}
		} else {
			if ($elxis->user()->gid == 6) {
				$name = eUTF::trim($elxis->user()->firstname.' '.$elxis->user()->lastname);
				if ($name == '') { $name = eUTF::trim($elxis->user()->uname); }
				if ($name != '') {
					$author = $name;
				} else {
					$author = eUTF::trim(preg_replace($pat, '', $author));
					if ($author == '') {
						$response['message'] = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('NAME'));
					}
				}

				if (trim($elxis->user()->email) != '') {
					$email = $elxis->user()->email;
				} else {
					if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
						$response['message'] = $eLang->get('INVALIDEMAIL');
					}
				}
			} else {
				$author = eUTF::trim(preg_replace($pat, '', $author));
				if ($author == '') {
					$response['message'] = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('NAME'));
				}
				if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
					$response['message'] = $eLang->get('INVALIDEMAIL');
				}
			}
		}

		if ($response['message'] != '') {
			if ($isajax) {
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit;
			} else {
				$elxis->redirect($article_link, $response['message'], true);
			}
		}

		$message = '';
		if (isset($_POST['message'])) { //filter_input destroys line breaks
			$message = strip_tags($_POST['message']);
			$pat = "#([\"]|[\']|[\$]|[\%]|[\~]|[\`]|[\<]|[\>]|[\|]|[\\\])#u";
			$message = eUTF::trim(preg_replace($pat, '', $message));
			$message = htmlspecialchars($message);
		}

		if ($message == '') {
			$response['message'] = $eLang->get('MUST_WRITE_MSG');
			if ($isajax) {
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit;
			} else {
				$elxis->redirect($article_link, $response['message'], true);
			}
		}

		elxisLoader::loadFile('includes/libraries/elxis/database/tables/comments.db.php');
		$comment = new commentsDbTable();
		$comment->element = 'com_content';
		$comment->elid = $id;
		$comment->message = $message;
		$comment->uid = $uid;
		$comment->author = $author;
		$comment->email = $email;
		$comment->published = (intval($elxis->acl()->check('com_content', 'comments', 'publish') > 0)) ? 1 : 0;

		if (!$comment->store()) {
			$response['message'] = $comment->getErrorMsg();
			if ($isajax) {
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit;
			} else {
				$elxis->redirect($article_link, $response['message'], true);
			}
		}

		$response['success'] = 1;

		$this->commentNotifyAdmin($row, $comment, $article_link);

		if ($comment->published == 0) {
			$response['waitapproval'] = 1;
			$response['message'] = $eLang->get('COM_PUBLISH_APPROVAL');
			if ($isajax) {
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit;
			} else {
				$elxis->redirect($article_link, $response['message'], false);
			}
		}

		if (!$isajax) { $elxis->redirect($article_link); }

		$avatar = $elxis->obj('avatar')->getAvatar($elxis->user()->avatar, 50, 1, $comment->email);

		$response['artid'] = $comment->elid;
		$response['comid'] = $comment->id;
		$response['curtime'] = time();
		$response['author'] = $comment->author;
		$response['avatar'] = $avatar;
		$response['created'] = eFactory::getDate()->formatDate($comment->created, $eLang->get('DATE_FORMAT_5'));
		$response['published'] = $comment->published;
		$response['canmail'] = ($elxis->acl()->getLevel() >= 70) ? 1 : 0;
		$response['canpub'] = ($elxis->acl()->check('com_content', 'comments', 'publish') == 2) ? 1 : 0;
		$response['candel'] = ($elxis->acl()->check('com_content', 'comments', 'delete') == 2) ? 1 : 0;
		$response['commessage'] = nl2br($comment->message);
		$response['email'] = $comment->email;
		$response['lngpublish'] = $eLang->get('PUBLISH');
		$response['lngdelete'] = $eLang->get('DELETE');

		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit;
	}


	/*********************************/
	/* SHOW RESPONSE TO AJAX REQUEST */
	/*********************************/
	private function ajaxResponse($msg='0|Invalid request!') {//TODO: OLD STYLE, DEPRECATED
		$this->ajaxHeaders('text/plain');
		echo $msg;
		exit;	
	}


	/*********************************/
	/* SEND NEW COMMENT NOTIFICATION */
	/*********************************/
	private function commentNotifyAdmin($row, $comment, $article_link) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();

		$admins = $this->model->getAdmins();
		if (!$admins) { return; }

		$original_language = $eLang->currentLang();
		$curlang = $original_language;

		$clear_message = strip_tags($comment->message);
		$ip_address = eFactory::getSession()->getIP();
		foreach ($admins as $admin) {
			if ($admin->uid == $elxis->user()->uid) { continue; } //dont notify himself!
			$userlang = trim($admin->preflang);
			if (($userlang != '') && ($userlang != $curlang)) {
				$eLang->switchLanguage($userlang);
				$curlang = $userlang;
			}

			$subject = $eLang->get('NEW_COMMENT_NOTIF');
			$body = $eLang->get('HI').' '.$admin->firstname.' '.$admin->lastname.",\n";
			if ($comment->published == 1) {
				$body .= $eLang->get('NEW_COMMENT_PUBLISHED')."\n\n";
			} else {
				$body .= $eLang->get('NEW_COMMENT_WAIT_APPR')."\n\n";
			}
			$body .= $eLang->get('ARTICLE').": \t".$row->title."\n";
			$body .= $article_link."\n\n";
			$body .= $eLang->get('COMMENTED_BY').": \t".$comment->author.' ('.$comment->email.")\n";
			$body .= 'IP address: '.$ip_address."\n\n";
			$body .= $eLang->get('COMMENT').' #'.$comment->id.":\n";
			$body .= $clear_message."\n\n\n";
			$body .= $eLang->get('REGARDS')."\n";
			$body .= $elxis->getConfig('SITENAME')."\n";
			$body .= $elxis->getConfig('URL')."\n\n\n\n";
			$body .= "_______________________________________________________________\n";
			$body .= $eLang->get('NOREPLYMSGINFO');			

			$to = $admin->email.','.$admin->firstname.' '.$admin->lastname;
			$elxis->sendmail($subject, $body, '', null, 'plain', $to);			
		}

		if ($curlang != $original_language) {
			$eLang->switchLanguage($original_language);
		}
	}


	/************************/
	/* SHOW MINIFIED CSS/JS */
	/************************/
	public function minify() {
		$segs = eFactory::getURI()->getSegments();
		$last = count($segs) - 1;
		$error = false;
		$gzip = false;
		$path = '';
		$type = 'plain';
		if ($last < 0) {
			$error = true;
		} else if (preg_match('/(\.css)$/', $segs[$last])) {
			$type = 'css';
			$path = eFactory::getFiles()->elxisPath('cache/minify/'.$segs[$last], true);
			$gzip = (eFactory::getElxis()->getConfig('MINICSS') == 2) ? true : false;
		} else if (preg_match('/(\.js)$/', $segs[$last])) {
			$type = 'javascript';
			$path = eFactory::getFiles()->elxisPath('cache/minify/'.$segs[$last], true);
			$gzip = (eFactory::getElxis()->getConfig('MINIJS') == 2) ? true : false;
		} else {
			$error = true;
		}
		
		if (!$error) {
			if (!file_exists($path)) { $error = true; }
		}

		if (ob_get_length() > 0) { ob_end_clean(); }
		if ($gzip) {
			ob_start('ob_gzhandler');
		}
		header('content-type:text/'.$type.'; charset:UTF-8');
		if (!$error) {
			header("cache-control: must-revalidate");
			$expire = 'expires: '.gmdate("D, d M Y H:i:s", time() + 864000)." GMT";
			header($expire);
			include($path);
   		}
   		exit();
	}


	/***********************************************/
	/* PREVIEW MODULE (REQUEST FROM ADMIN SECTION) */
	/***********************************************/
	public function modulepreview() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$uid = (int)$elxis->user()->uid;
		$level = (int)$elxis->acl()->getLevel();
		if (($uid < 1) || ($level < 70) || ($elxis->acl()->check('com_extmanager', 'modules', 'edit') < 1)) {
			exitPage::make('403', 'CCON-0013', $eLang->get('NOTALLOWACCPAGE'));
		}
 		if (ELXIS_INNER == 0) {
 			echo '<div class="elx5_error">Invalid page access method!</div>'."\n";
 			return;
		}
		$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

		$ok = false;
		if ($id > 0) {
			$row = new modulesDbTable();
			if (!$row->load($id)) {
				$ok = false;
			} else {
				$ok = true;
			}
		}
		if (!$ok) {
 			echo '<div class="elx5_error">Module with id '.$id.' not found!</div>'."\n";
 			return;
		}

		if ($row->section == 'backend') {
 			echo '<div class="elx5_error">Module '.$row->title.' can be accessed only from the administration section!</div>'."\n";
 			return;
		}

		if (!file_exists(ELXIS_PATH.'/modules/'.$row->module.'/'.$row->module.'.php')) {
 			echo '<div class="elx5_error">Module '.$row->title.' files not found!</div>'."\n";
 			return;
		}

		$eLang->load($row->module, 'module');

		elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
		$params = new elxisParameters($row->params, '', 'module');

		$title = $row->title;
		if ($row->showtitle == 2) {
			$str = strtoupper($row->module).'_TITLE';
			if ($eLang->exist($str)) { $title = $eLang->get($str); }
		}

		$elxmod = new stdClass; //$elxmod object is required by some modules
		$elxmod->id = $row->id;
		$elxmod->title = $title;
		$elxmod->module = $row->module;
		$elxmod->showtitle	= $row->showtitle;
		$elxmod->position = $row->position;
		$elxmod->content = $row->content;
		$elxmod->params = $row->params;

		$css_sfx = $params->get('css_sfx');
		echo '<div style="background-color:#1F6AAB; color:#FFF; padding:4px; font-size:14px;">Preview of module <strong>'.$row->module.'</strong> (id: <strong>'.$row->id.'</strong>, position: <strong>'.$row->position.'</strong>)<br />';
		echo 'Note that the layout and style many differ on the final template position! Resize the window to match final position width.</div>'."\n";
		echo '<div style="margin:10px 0; padding:10px; background-color:#FFFFFF; border:1px solid #1F6AAB;">'."\n";
		echo '<div class="module'.$css_sfx.'">'."\n";
		if ($row->showtitle > 0) {
			echo "\t<h3>".$title."</h3>\n";
		}
		include(ELXIS_PATH.'/modules/'.$row->module.'/'.$row->module.'.php');
		echo "</div>\n";
		echo '<div style="clear:both;"></div>'."\n";
		echo "</div>\n";
	}


	/****************************************/
	/* PREPARE TO DISPLAY ARCHIVED ARTICLES */
	/****************************************/
	public function archive() {
		$elxis = eFactory::getElxis();
		$eURI = eFactory::getURI();
		$eDoc = eFactory::getDocument();
		$eLang = eFactory::getLang();
		$eDate = eFactory::getDate();

		/** 
		Important note for date: Search is been performed based on system date (GMT), not for the user's timezone! 
		So you might see "wrong" results especially for day archive pages.
		*/

		$segs = $eURI->getSegments();

		$year = 0;
		$month = 0;
		$day = 0;

		if (isset($segs[1])) { //year
			if (!is_numeric($segs[1])) {
				exitPage::make('404', 'CCON-0011');
			}
			$x = (int)$segs[1];
			if (($x < 2000) || ($x > gmdate('Y'))) {
				exitPage::make('404', 'CCON-0012', 'Invalid archive year!');
			}
			$year = $x;
			if (isset($segs[2])) { //month
				if (!is_numeric($segs[2])) {
					exitPage::make('404', 'CCON-0013');
				}
				$x = (int)$segs[2];
				if (($x < 1) || ($x >12)) {
					exitPage::make('404', 'CCON-0014', 'Invalid archive month!');
				}
				$month = $x;
				if (isset($segs[3])) { //day
					if (!is_numeric($segs[3])) {
						exitPage::make('404', 'CCON-0015');
					}
					$x = (int)$segs[3];
					
					if (($x < 1) || ($x >31)) {
						exitPage::make('404', 'CCON-0016', 'Invalid archive day!');
					}
					$day = $x;
				}
			}
			unset($x);
		}

		if ($day > 0) {
			if (!checkdate($month, $day, $year)) {
				exitPage::make('404', 'CCON-0017', 'Invalid archive date!');
			}
		}

		if (isset($segs[4])) {
			exitPage::make('404', 'CCON-0018');
		}

		$total = $this->model->countArchiveArticles($year, $month, $day);

		$global_str = (string)$this->model->componentParams();
		elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
		$params = new elxisParameters($global_str, '', 'component');

		$perpage = (int)$params->get('arc_perpage', 10);

		$page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
		if ($page < 1) { $page = 1; }
		$maxpage = ($total == 0) ? 1 : ceil($total/$perpage);
		if ($page > $maxpage) { $page = $maxpage; }
    	$limit = $perpage;
		$limitstart = (($page - 1) * $limit);

		$rows = null;

		$metaKeys = array();
		$metaKeys[] = $eLang->get('ARCHIVE');
		if ($month > 0) {
			$metaKeys[] = $eDate->monthName($month).' '.$year;
		}

		if ($total > 0) {
			$order = $params->get('arc_ordering', 'cd');
			$rows = $this->model->fetchArchiveArticles($year, $month, $day, $limitstart, $limit, $order, $this->translate, $this->lng);
			if ($rows) {
				$ePlugin = eFactory::getPlugin();

				$show_intro = (int)$params->get('arc_intro', 0);

				foreach ($rows as $k => $row) {
					if ($row->metakeys != '') {
						$parts = explode(',', $row->metakeys);
						$metaKeys[] = $parts[0];
					}
					if ($row->catid > 0) {
						$row->link = $elxis->makeURL('content:'.$row->seolink.$row->seotitle.'.html');
					} else {
						$row->link = $elxis->makeURL('content:'.$row->seotitle.'.html');
					}
					$rows[$k]->link = $row->link;

					if ($show_intro == 1) {
						$rows[$k]->introtext = $ePlugin->removePlugins($row->introtext);
					}
				}
			}
		}

		$metaKeys = array_unique($metaKeys);
		if (count($metaKeys) < 6) {
			$metaKeys[] = $eLang->get('ARTICLES');
		}

		$pathway = eFactory::getPathway();

		if ($day > 0) {
			$monthname = $eDate->monthName($month);
			$m = sprintf("%02d", $month);
			$d = sprintf("%02d", $day);
			$ts = mktime(12, 0, 0, $month, $day, $year);
			$dt = $eDate->formatTS($ts, $eLang->get('DATE_FORMAT_3'), false);
			$title = $eLang->get('ARCHIVE').' '.$dt;
			$desc = sprintf($eLang->get('ARCHIVED_ARTS_FOR'), $dt);

			$pathway->addNode($eLang->get('ARCHIVE'), 'content:archive/');
			$pathway->addNode($year, 'content:archive/'.$year.'/');
			$pathway->addNode($monthname, 'content:archive/'.$year.'/'.$m.'/');
			if ($page > 1) {
				$pathway->addNode($d, 'content:archive/'.$year.'/'.$m.'/'.$d.'/');
				$pathway->addNode($eLang->get('PAGE').' '.$page);
			} else {
				$pathway->addNode($d);
			}
			unset($m, $d, $monthname, $ts, $dt);
		} else if ($month > 0) {
			$monthname = $eDate->monthName($month);
			$m = sprintf("%02d", $month);
			$dt = $monthname.' '.$year;
			$title = $eLang->get('ARCHIVE').' '.$dt;
			$desc = sprintf($eLang->get('ARCHIVED_ARTS_FOR'), $dt);

			$pathway->addNode($eLang->get('ARCHIVE'), 'content:archive/');
			$pathway->addNode($year, 'content:archive/'.$year.'/');
			if ($page > 1) {
				$pathway->addNode($monthname, 'content:archive/'.$year.'/'.$m.'/');
				$pathway->addNode($eLang->get('PAGE').' '.$page);
			} else {
				$pathway->addNode($monthname);
			}
			unset($m, $monthname, $ts, $dt);
		} else if ($year > 0) {
			$title = $eLang->get('ARCHIVE').' '.$year;
			$desc = sprintf($eLang->get('ARCHIVED_ARTS_FOR'), $year);

			$pathway->addNode($eLang->get('ARCHIVE'), 'content:archive/');
			if ($page > 1) {
				$pathway->addNode($year, 'content:archive/'.$year.'/');
				$pathway->addNode($eLang->get('PAGE').' '.$page);
			} else {
				$pathway->addNode($year);
			}
		} else {
			$title = $eLang->get('ARCHIVE');
			$desc = $eLang->get('SITE_ARTS_CHRONO');
			if ($page > 1) {
				$pathway->addNode($eLang->get('ARCHIVE'), 'content:archive/');
				$pathway->addNode($eLang->get('PAGE').' '.$page);
			} else {
				$pathway->addNode($eLang->get('ARCHIVE'));
			}
		}

		if ($page > 1) {
			$title .= ' - '.$eLang->get('PAGE').' '.$page;
			$desc .= ' '.$eLang->get('PAGE').' '.$page;
		}

		$eDoc->setTitle($title);
		$eDoc->setDescription($desc);
		$eDoc->setKeywords($metaKeys);
		unset($metaKeys, $desc);

		$this->view->archiveHTML($rows, $year, $month, $day, $page, $maxpage, $total, $params, $title);
	}

}

?>