<?php 
/**
* @version		$Id$
* @package		Elxis
* @subpackage	Module Articles
* @copyright	Copyright (c) 2006-2021 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


if (!class_exists('modArticles', false)) {
	class modArticles {

		private $source = 0;
		private $catid = 0;
		private $subcats = 0;
		private $catids = array();
		private $artids = array();
		private $order = 0;
		private $days = 10;
		private $limit = 5;
		private $layout = 1;//1: 1column, 2: left-right
		private $short = 1;
		private $short_columns = 1;
		private $short_imp = 0;
		private $short_sub = 1;
		private $short_cat = 0;
		private $short_date = 0;
		private $short_text = 0;
		private $short_more = 0;
		private $short_img = 8;
		private $short_caption = 0;
		private $links_sub = 0;
		private $links_cat = 0;
		private $links_date = 0;
		private $links_img = 0;
		private $links_columns = 1;
		private $errormsg = '';
		private $lng = 'en';
		private $translate = false;
		private $relkey = '';


		/*********************/
		/* MAGIC CONSTRUCTOR */
		/*********************/
		public function __construct($params) {
			$this->lng = eFactory::getURI()->getUriLang();
			if (eFactory::getElxis()->getConfig('MULTILINGUISM') == 1) {
				if ($this->lng != '') { $this->translate = true; }
			}
			$this->getParams($params);
		}


		/*************************/
		/* GET MODULE PARAMETERS */
		/*************************/
		private function getParams($params) {
			$this->limit = (int)$params->get('limit', 5);
			if ($this->limit < 1) { $this->limit = 5; }

			$this->source = (int)$params->get('source', 0);
			if ($this->source == 1) {
				$this->catid = (int)$params->get('catid', 0);
				$this->subcats = (int)$params->get('subcats', 0);
				if ($this->catid < 1) { $this->errormsg = 'No category selected for the articles!'; }
			} else if ($this->source == 2) {
				$catstr = trim($params->get('catids', ''));
				$catids = explode(',', $catstr);
				if ($catids) {
					foreach ($catids as $catid) {
						$catid = (int)$catid;
						if ($catid > 0) { $this->catids[] = $catid; }
					}
				}
				if (count($this->catids) == 0) { $this->errormsg = 'No categories selected for the articles!'; }
			} else if ($this->source == 4) {
				$artstr = trim($params->get('artids', ''));
				$artids = explode(',', $artstr);
				if ($artids) {
					foreach ($artids as $artid) {
						$artid = (int)$artid;
						if ($artid > 0) { $this->artids[] = $artid; }
					}
				}
				$n = count($this->artids);
				if ($n == 0) {
					$this->errormsg = 'No categories selected for the articles!';
				} else {
					if ($n > $this->limit) { $this->limit = $n; }
				}
			}

			$this->order = (int)$params->get('order', 0);
			if ($this->order == 2) {
				$this->days = (int)$params->get('days', 10);
				if ($this->days < 1) { $this->days = 10; }
			}

			$this->layout = (int)$params->get('layout', 1);
			if ($this->layout != 2) { $this->layout = 1; }
			$this->relkey = trim($params->get('relkey', ''));
			$this->short = (int)$params->get('short', 1);
			if ($this->short > $this->limit) { $this->short = $this->limit; }
			$this->short_columns = (int)$params->get('short_columns', 1);
			$this->short_imp = (int)$params->get('short_imp', 0);
			$this->short_sub = (int)$params->get('short_sub', 1);
			$this->short_cat = (int)$params->get('short_cat', 0);
			$this->short_date = (int)$params->get('short_date', 0);
			$this->short_text = (int)$params->get('short_text', 0);
			$this->short_more = (int)$params->get('short_more', 0);
			$this->short_caption = (int)$params->get('short_caption', 0);
			$this->short_img = (int)$params->get('short_img', 8);
			//backwards compatibility
			if ($this->short_img == 6) { $this->short_img = 1; }
			if ($this->short_img == 7) { $this->short_img = 2; }
			$this->links_sub = (int)$params->get('links_sub', 0);
			$this->links_cat = (int)$params->get('links_cat', 0);
			$this->links_date = (int)$params->get('links_date', 0);
			$this->links_img = (int)$params->get('links_img', 0);
			//backwards compatibility
			if ($this->links_img == 3) { $this->links_img = 1; }
			if ($this->links_img == 4) { $this->links_img = 2; }
			$this->links_columns = (int)$params->get('links_columns', 1);
			if ($this->source == 3) {
				$this->short_cat = 0;
				$this->links_cat = 0;
			}
		}


		/*************************/
		/* DISPLAY ERROR MESSAGE */
		/*************************/
		private function showError($msg) {
			echo '<div class="elx5_error">'.$msg."</div>\n";
		}


		/********************/
		/* RUN FOREST, RUN! */
		/********************/         
		public function run() {
			if ($this->errormsg != '') {
				$this->showError($this->errormsg);
				return;
			}

			$rows = $this->getArticles();
			if (!$rows) { return; }

			$total = count($rows);
			$short = $this->short;
			if ($short > $total) { $short = $total; }
			$numlinks = $total - $short;

			if (($short == 0) || ($numlinks == 0)) { $this->layout = 1; }

			if ($this->layout == 2) {
				echo '<div class="elx5_2colwrap">'."\n";
				echo '<div class="elx5_2colbox">'."\n";
				$this->showShort($rows, $short);
				echo "</div>\n";
				echo '<div class="elx5_2colbox">'."\n";
				$this->showLinks($rows, $short, $numlinks);
				echo "</div>\n";
				echo "</div>\n";
			} else {
				$this->showShort($rows, $short);
				$this->showLinks($rows, $short, $numlinks);
			}
		}


		/**************************/
		/* RENDER LINKED ARTICLES */
		/**************************/
		private function showLinks($articles, $skip, $numlinks) {
			$eLang = eFactory::getLang();
			$elxis = eFactory::getElxis();
			$eFiles = eFactory::getFiles();

			switch ($this->links_img) {
				case 1: $figaddon = ' elx5_content_imageboxtl'; $boxaddon = ' elx5_artboxtl'; break;
				case 2: $figaddon = ' elx5_content_imageboxtr'; $boxaddon = ' elx5_artboxtr'; break;
				case 0: default: $figaddon = ''; $boxaddon = ''; break;
			}

			$i = 0;
			$b = 0;
			$c = 0;
			$buffer = array();
			foreach ($articles as $id => $article) {
				if ($i < $skip) { $i++; continue; }
				if ($c >= $numlinks) { break; }
				if ($this->source != 3) {
					$link = $elxis->makeURL($article->seolink.$article->seotitle.'.html');
				} else {
					$link = $elxis->makeURL($article->seotitle.'.html');
				}

				$imgbox = '';
				if ($this->links_img > 0) {
					if ((trim($article->image) == '') || !file_exists(ELXIS_PATH.'/'.$article->image)) {
						$alt = (trim($article->caption) != '') ? $article->caption : $article->title;
						$imgbox = '<figure class="elx5_content_imagebox'.$figaddon.'">'."\n";
						$imgbox .= '<a href="'.$link.'" title="'.$article->title.'">';
						$imgbox .= '<img src="'.$elxis->secureBase().'/templates/system/images/nopicture_article.jpg" alt="'.$alt.'" />'; 
						$imgbox .= "</a>\n";
						$imgbox .= "</figure>\n";
					} else {
						$imgfile = $elxis->secureBase().'/'.$article->image;
						$file_info = $eFiles->getNameExtension($article->image);
						if (file_exists(ELXIS_PATH.'/'.$file_info['name'].'_thumb.'.$file_info['extension'])) {
							$imgfile = $elxis->secureBase().'/'.$file_info['name'].'_thumb.'.$file_info['extension'];
						}
						unset($file_info);

						$alt = (trim($article->caption) != '') ? $article->caption : $article->title;
						$imgbox = '<figure class="elx5_content_imagebox'.$figaddon.'">'."\n";
						$imgbox .= '<a href="'.$link.'" title="'.$article->title.'"><img src="'.$imgfile.'" alt="'.$alt.'" /></a>'."\n";
						$imgbox .= "</figure>\n";
					}
				}

				$buffer[$b] = $imgbox;
				$buffer[$b] .= '<div class="elx5_artbox_inner">'."\n";
				$buffer[$b] .= '<h3><a href="'.$link.'" title="'.$article->title.'">'.$article->title.'</a></h3>'."\n";
				if ($this->links_date == 1) {
					$txt = '<time datetime="'.$article->created.'">'.$this->friendlyDate($article->created).'</time>';
					if (($this->links_cat == 1) && ($article->catid > 0)) {
						$link2 = $elxis->makeURL($article->seolink);
						$txt .= ' '.$eLang->get('IN').' <a href="'.$link2.'" title="'.$article->cattitle.'">'.$article->cattitle."</a>\n";
					}
					$buffer[$b] .= '<div class="elx5_dateauthor">'.$txt.'</div>'."\n";
				} elseif (($this->links_cat == 1) && ($article->catid > 0)) {
					$link2 = $elxis->makeURL($article->seolink);
					$txt = $eLang->get('IN').' <a href="'.$link2.'" title="'.$article->cattitle.'">'.$article->cattitle."</a>\n";
					$buffer[$b] .= '<div class="elx5_dateauthor">'.$txt.'</div>'."\n";
				}

				if ($this->links_sub == 1) {
					if (trim($article->subtitle) != '') { $buffer[$b] .= '<p class="elx5_content_subtitle">'.$article->subtitle."</p>\n"; }
				}
				if ($imgbox != '') { $buffer[$b] .= '<div class="clear"></div>'."\n"; }
				$buffer[$b] .= "</div>\n";//elx5_artbox_inner
				$i++;
				$b++;
				$c++;
			}

			if (!$buffer) { return; }

			if ($this->links_columns > 1) {
				echo '<div class="elx5_'.$this->links_columns.'colwrap">'."\n";
				foreach ($buffer as $txt) {
					echo '<div class="elx5_'.$this->links_columns.'colbox elx5_artbox'.$boxaddon.'">'."\n";
					echo $txt;
					echo "</div>\n";
				}
				echo "</div>\n";
			} else {
				foreach ($buffer as $txt) {
					echo '<div class="elx5_artbox'.$boxaddon.'">'."\n";
					echo $txt;
					echo "</div>\n";
				}
			}
		}


		/*************************/
		/* RENDER SHORT ARTICLES */
		/*************************/
		private function showShort($articles, $num) {
			$eLang = eFactory::getLang();
			$elxis = eFactory::getElxis();
			$eFiles = eFactory::getFiles();
			$eDate = eFactory::getDate();

			switch ($this->short_img) {
				case 1: $figaddon = ' elx5_content_imageboxtl'; $boxaddon = ' elx5_artboxtl'; break;
				case 2: $figaddon = ' elx5_content_imageboxtr'; $boxaddon = ' elx5_artboxtr'; break;
				case 3: $figaddon = ' elx5_content_imageboxlt'; $boxaddon = ' elx5_artboxlt'; break;
				case 4: $figaddon = ' elx5_content_imageboxml'; $boxaddon = ' elx5_artboxml'; break;
				case 5: $figaddon = ' elx5_content_imageboxmr'; $boxaddon = ' elx5_artboxmr'; break;
				case 8: $figaddon = ' elx5_content_imageboxlt'; $boxaddon = ' elx5_artboxvt'; break;
				case 0: default: $figaddon = ''; $boxaddon = ''; break;
			}

			$i = 0;
			$b = 0;
			$buffer = array();
			foreach ($articles as $id => $article) {
				if ($i >= $num) { break; }
				if ($this->source != 3) {
					$link = $elxis->makeURL($article->seolink.$article->seotitle.'.html');
				} else {
					$link = $elxis->makeURL($article->seotitle.'.html');
				}

				$imgbox = '';
				if ($this->short_img > 0) {
					if ((trim($article->image) == '') || !file_exists(ELXIS_PATH.'/'.$article->image)) {
						$alt = (trim($article->caption) != '') ? $article->caption : $article->title;
						$imgbox = '<figure class="elx5_content_imagebox'.$figaddon.'">'."\n";
						$imgbox .= '<a href="'.$link.'" title="'.$article->title.'">';
						$imgbox .= '<img src="'.$elxis->secureBase().'/templates/system/images/nopicture_article.jpg" alt="'.$alt.'" />'; 
						$imgbox .= "</a>\n";
						if (($this->short_caption == 1) && (trim($article->caption) != '')) { $imgbox .= '<figcaption>'.$article->caption."</figcaption>\n"; }
						$imgbox .= "</figure>\n";
					} else {
						$imgfile = $elxis->secureBase().'/'.$article->image;
						if ($this->short_img != 8) {
							$file_info = $eFiles->getNameExtension($article->image);
							if (file_exists(ELXIS_PATH.'/'.$file_info['name'].'_medium.'.$file_info['extension'])) {
								$imgfile = $elxis->secureBase().'/'.$file_info['name'].'_medium.'.$file_info['extension'];
							}
							unset($file_info);
						}

						$alt = (trim($article->caption) != '') ? $article->caption : $article->title;
						$imgbox = '<figure class="elx5_content_imagebox'.$figaddon.'">'."\n";
						$imgbox .= '<a href="'.$link.'" title="'.$article->title.'"><img src="'.$imgfile.'" alt="'.$alt.'" /></a>'."\n";
						if (($this->short_caption == 1) && (trim($article->caption) != '')) { $imgbox .= '<figcaption>'.$article->caption."</figcaption>\n"; }
						$imgbox .= "</figure>\n";
					}
				}

				$buffer[$b] = '';
				if ($this->short_img != 3) { $buffer[$b] .= $imgbox; }

				$buffer[$b] .= '<div class="elx5_artbox_inner">'."\n";
				$buffer[$b] .= '<h3><a href="'.$link.'" title="'.$article->title.'">'.$article->title.'</a></h3>'."\n";
				if ($this->short_date == 1) {
					$txt = '<time datetime="'.$article->created.'">'.$this->friendlyDate($article->created).'</time>';
					if (($this->short_cat == 1) && ($article->catid > 0)) {
						$link2 = $elxis->makeURL($article->seolink);
						$txt .= ' '.$eLang->get('IN').' <a href="'.$link2.'" title="'.$article->cattitle.'">'.$article->cattitle."</a>\n";
					}
					$buffer[$b] .= '<div class="elx5_dateauthor">'.$txt.'</div>'."\n";
				} elseif (($this->short_cat == 1) && ($article->catid > 0)) {
					$link2 = $elxis->makeURL($article->seolink);
					$txt = $eLang->get('IN').' <a href="'.$link2.'" title="'.$article->cattitle.'">'.$article->cattitle."</a>\n";
					$buffer[$b] .= '<div class="elx5_dateauthor">'.$txt.'</div>'."\n";
				}
				$buffer[$b] .= "</div>\n";
				if ($this->short_img == 3) { $buffer[$b] .= $imgbox; }

				$buffer[$b] .= '<div class="elx5_artbox_inner">'."\n";
				if ($this->short_sub == 1) {
					if (trim($article->subtitle) != '') { $buffer[$b] .= '<p class="elx5_content_subtitle">'.$article->subtitle."</p>\n"; }
				}

				if ($this->short_text > 0) {
					$article->introtext = $this->removePlugins($article->introtext);
					if ($this->short_text < 1000) {
						$txt = strip_tags($article->introtext);
						$len = eUTF::strlen($txt);
						if ($len > $this->short_text) {
							$limit = $this->short_text - 3;
							$txt = eUTF::substr($txt, 0, $limit).'...';
						}
					} else {
						$txt = $article->introtext;
					}
					if ($this->short_more) {
						$txt .= ' <a href="'.$link.'" title="'.$article->title.'">'.$eLang->get('MORE')."</a>\n";
					}
					$buffer[$b] .= '<p>'.$txt."</p>\n";
				} else if ($this->short_more) {
					$buffer[$b] .= '<a href="'.$link.'" title="'.$article->title.'">'.$eLang->get('MORE')."</a>\n";
				}

				if ($this->short_date == 2) {
					$txt = '<time datetime="'.$article->created.'">'.$this->friendlyDate($article->created).'</time>';
					if (($this->short_cat == 2) && ($article->catid > 0)) {
						$link2 = $elxis->makeURL($article->seolink);
						$txt .= ' '.$eLang->get('IN').' <a href="'.$link2.'" title="'.$article->cattitle.'">'.$article->cattitle."</a>\n";
					}
					$buffer[$b] .= '<div class="elx5_dateauthor">'.$txt.'</div>'."\n";
				} elseif (($this->short_cat == 2) && ($article->catid > 0)) {
					$link2 = $elxis->makeURL($article->seolink);
					$txt = $eLang->get('IN').' <a href="'.$link2.'" title="'.$article->cattitle.'">'.$article->cattitle."</a>\n";
					$buffer[$b] .= '<div class="elx5_dateauthor">'.$txt.'</div>'."\n";
				}

				if (($this->short_img != 3) && ($this->short_img != 8)) {
					$buffer[$b] .= '<div class="clear"></div>'."\n";
				}
				$buffer[$b] .= "</div>\n";//elx5_artbox_inner
				$i++;
				$b++;
			}

			if (!$buffer) { return; }
			if ($this->short_columns > 1) {
				echo '<div class="elx5_'.$this->short_columns.'colwrap">'."\n";
				foreach ($buffer as $txt) {
					echo '<div class="elx5_'.$this->short_columns.'colbox elx5_artbox'.$boxaddon.'">'."\n";
					echo $txt;
					echo "</div>\n";
				}
				echo "</div>\n";
			} else {
				foreach ($buffer as $txt) {
					echo '<div class="elx5_artbox'.$boxaddon.'">'."\n";
					echo $txt;
					echo "</div>\n";
				}
			}
		}


		/********************************/
		/* REMOVE ALL PLUGINS FROM TEXT */
		/********************************/
		/* Use this method instead of elxisPlugin::removePlugins() in order not to initiate class in pages were we don't need it */
		private function removePlugins($text) {
			$cregex = '#<code>(.*?)</code>#';
			$regex = '#{[^}]*}(?:.+?{\/[^}]*})?#';
			$eregex = '~href="#elink:(.*?)"~';
			$newtext = preg_replace($cregex, '', $text);
			$newtext = preg_replace($regex, '', $newtext);
			$newtext = preg_replace($eregex, 'href="javascript:void(null);"', $newtext);
			return $newtext;
		}


		/**********************/
		/* USER FRIENDLY DATE */
		/**********************/
		private function friendlyDate($date) {
			$eLang = eFactory::getLang();

			$today = gmdate('Y-m-d');
			if (strpos($date, $today) === 0) {
				$dt = eFactory::getDate()->formatDate($date, '%H:%M');
				return $eLang->get('TODAY').' '.$dt;
			} else {
				$ts = time() - 86400;
				$yesterday = gmdate('Y-m-d', $ts);
				if (strpos($date, $yesterday) === 0) {
					$dt = eFactory::getDate()->formatDate($date, '%H:%M');
					return $eLang->get('YESTERDAY').' '.$dt;
				} else {
					return eFactory::getDate()->formatDate($date, $eLang->get('DATE_FORMAT_4'));
				}
			}
		}


		/**********************************/
		/* GET ARTICLES FROM THE DATABASE */
		/**********************************/
		private function getArticles() {
			$db = eFactory::getDB();
			$elxis = eFactory::getElxis();

			$lowlev = $elxis->acl()->getLowLevel();
			$exactlev = $elxis->acl()->getExactLevel();
			$binds = array();

			$sql = "SELECT a.id, a.catid, a.title, a.seotitle, a.subtitle, a.introtext, a.image, a.caption, a.created";
			if ($this->source != 3) { $sql .= ", c.title AS cattitle, c.seolink"; }
			$sql .= "\n FROM ".$db->quoteId('#__content')." a";
			if ($this->source != 3) {
				$sql .= "\n LEFT JOIN ".$db->quoteId('#__categories')." c ON c.catid=a.catid";
			}
			$sql .= "\n WHERE a.published = 1";
			if (($this->short_imp == 1) && ($this->short > 0)) {
				$sql .= ' AND a.important = :imp'; //bind will be set after prepare
			}
			if ($this->relkey != '') {
				$sql .= ' AND a.relkey = :rlk';
				$binds[] = array(':rlk', $this->relkey, PDO::PARAM_STR);
			}
			if ($this->source != 3) { 
				if (($this->source == 0) || ($this->source == 4)) { 
					$sql .= " AND ((c.published = 1) OR (a.catid = 0))";
				} else {
					$sql .= " AND c.published = 1";
				}
			}
			if ($this->source == 1) {
				if ($this->subcats == 1) {
					$sql .= "\n AND ((c.catid = :ctg) OR (c.parent_id = :ctg))";
					$binds[] = array(':ctg', $this->catid, PDO::PARAM_INT);
				} else {
					$sql .= "\n AND c.catid = :ctg";
					$binds[] = array(':ctg', $this->catid, PDO::PARAM_INT);
				}
			} else if ($this->source == 2) {
				$sql .= "\n AND a.catid IN (".implode(",", $this->catids).")";
			} else if ($this->source == 3) {
				$sql .= "\n AND a.catid = :ctg";
				$binds[] = array(':ctg', 0, PDO::PARAM_INT);
			} else if ($this->source == 4) {
				if (count($this->artids) == 1) {
					$sql .= "\n AND a.id = :xart";
					$binds[] = array(':xart', $this->artids[0], PDO::PARAM_INT);
				} else {
					$v = implode(',', $this->artids);
					$sql .= "\n AND a.id IN (".$v.")";
				}
			}
			$sql .= " AND ((a.alevel <= :lowlevel) OR (a.alevel = :exactlevel))";
			$binds[] = array(':lowlevel', $lowlev, PDO::PARAM_INT);
			$binds[] = array(':exactlevel', $exactlev, PDO::PARAM_INT);

			if ($this->source == 4) {
				if (count($this->artids) > 1) {
					$sql .= "\n ORDER BY FIELD(a.id,".implode(',', $this->artids).")";//Mysql only
				}
			} else {
				if ($this->order == 1) {
					$sql .= "\n ORDER BY a.hits DESC";
				} else if ($this->order == 2) {
					$ts = gmmktime(0, 0, 0, gmdate('m'), gmdate('d') - $this->days, gmdate('Y'));
					$date = gmdate('Y-m-d H:i:s', $ts);
					$sql .= " AND a.created > :crdate";
					$binds[] = array(':crdate', $date, PDO::PARAM_STR);
					$sql .= "\n ORDER BY a.hits DESC";
				} else {
					$sql .= "\n ORDER BY a.created DESC";
				}
			}

			if (($this->short_imp == 1) && ($this->short > 0)) { //first get the important as short
				$stmt = $db->prepareLimit($sql, 0, $this->short);
				foreach ($binds as $bind) {
					$stmt->bindParam($bind[0], $bind[1], $bind[2]);
				}
				$imp = 1;
				$stmt->bindParam(':imp', $imp, PDO::PARAM_INT);
			} else {
				$stmt = $db->prepareLimit($sql, 0, $this->limit);
				foreach ($binds as $bind) {
					$stmt->bindParam($bind[0], $bind[1], $bind[2]);
				}				
			}
			$stmt->execute();
			$firstrows = $stmt->fetchAllAssoc('id', PDO::FETCH_OBJ);

			$secondrows = false;

			if (($this->short_imp == 1) && ($this->short > 0)) {
				$n = $firstrows ? count($firstrows) : 0;
				$this->short = $n;//set short number to those found as important. The rest (limit - short) will be displayed as links
				$rest = $this->limit - $n;
				if ($rest > 0) {
					$stmt = $db->prepareLimit($sql, 0, $rest);
					foreach ($binds as $bind) {
						$stmt->bindParam($bind[0], $bind[1], $bind[2]);
					}
					$imp = 0;//not important articles only
					$stmt->bindParam(':imp', $imp, PDO::PARAM_INT);
					$stmt->execute();
					$secondrows = $stmt->fetchAllAssoc('id', PDO::FETCH_OBJ);
				}
			}

			$rows = array();
			if ($firstrows) { $rows = $firstrows; }
			if ($secondrows) {
				foreach ($secondrows as $id => $row) { $rows[$id] = $row; }
			}

			if ($rows && ($this->translate === true)) {
				$rows = $this->translateArticles($rows);
			}

			return $rows;
		}


		/**********************/
		/* TRANSLATE ARTICLES */
		/**********************/
		private function translateArticles($rows) {
			$db = eFactory::getDB();

			$ids = array();
			$catids = array();
			foreach ($rows as $row) {
				$ids[] = $row->id;
				if ($row->catid > 0) { $catids[] = $row->catid; }
			}

			if (($this->source != 3) && ($catids)) { $catids = array_unique($catids); }

			$sql = "SELECT ".$db->quoteId('elid').", ".$db->quoteId('element').", ".$db->quoteId('translation')
			."\n FROM ".$db->quoteId('#__translations')
			."\n WHERE ".$db->quoteId('category')."=".$db->quote('com_content')." AND ".$db->quoteId('language')." = :lng"
			."\n AND ((".$db->quoteId('element')." = ".$db->quote('title').") OR (".$db->quoteId('element')." = ".$db->quote('subtitle').")"
			."\n OR (".$db->quoteId('element')." = ".$db->quote('introtext').") OR (".$db->quoteId('element')." = ".$db->quote('caption')."))"
			."\n AND ".$db->quoteId('elid')." IN (".implode(", ", $ids).")";
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':lng', $this->lng, PDO::PARAM_STR);
			$stmt->execute();
			$translations = $stmt->fetchAll(PDO::FETCH_ASSOC);

			if ($translations) {
				foreach ($translations as $trans) {
					$id = (int)$trans['elid'];
					$element = $trans['element'];
					if (!isset($rows[$id])) { continue; }
					switch($element) {
						case 'title': $rows[$id]->title = $trans['translation']; break;
						case 'subtitle': $rows[$id]->subtitle = $trans['translation']; break;
						case 'introtext': $rows[$id]->introtext = $trans['translation']; break;
						case 'caption': $rows[$id]->caption = $trans['translation']; break;
						default: break;
					}
				}
			}

			if (($this->source != 3) && ($catids)) {
				if ((($this->short > 0) && ($this->short_cat > 0)) || ($this->links_cat > 0)) {
					$sql = "SELECT ".$db->quoteId('elid').", ".$db->quoteId('translation')." FROM ".$db->quoteId('#__translations')
					."\n WHERE ".$db->quoteId('category')."=".$db->quote('com_content')." AND ".$db->quoteId('language')." = :lng"
					."\n AND ".$db->quoteId('element')." = ".$db->quote('category_title')." AND ".$db->quoteId('elid')." IN (".implode(", ", $catids).")";
					$stmt = $db->prepare($sql);
					$stmt->bindParam(':lng', $this->lng, PDO::PARAM_STR);
					$stmt->execute();
					$translations = $stmt->fetchAllAssoc('elid', PDO::FETCH_ASSOC);
					if ($translations) {
						foreach ($rows as $id => $row) {
							if (($row->catid > 0) && isset($translations[ $row->catid ])) {
								$rows[$id]->cattitle = $translations[ $row->catid ]['translation'];
							}
						}
					}
				}
			}

			return $rows;
		}

	}
}


$elxmodarts = new modArticles($params);
$elxmodarts->run();
unset($elxmodarts);

?>