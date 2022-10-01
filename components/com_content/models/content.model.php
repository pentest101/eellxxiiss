<?php 
/**
* @version		$Id: content.model.php 2420 2021-09-10 17:14:20Z IOS $
* @package		Elxis
* @subpackage	Component Content
* @copyright	Copyright (c) 2006-2021 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class contentModel {

	private $db;


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		$this->db = eFactory::getDB();
	}


	/**************************/
	/* FETCH CATEGORY FROM DB */
	/**************************/
	public function fetchCategory($seotitle, $onlypublic=false) {
		$elxis = eFactory::getElxis();
		if ($onlypublic === true) {
			$lowlev = 0;
			$exactlev = 0;
		} else {
			$lowlev = $elxis->acl()->getLowLevel();
			$exactlev = $elxis->acl()->getExactLevel();
		}

		$sql = "SELECT * FROM ".$this->db->quoteId('#__categories')
		."\n WHERE ".$this->db->quoteId('seotitle')." = :seotitle AND ".$this->db->quoteId('published')."=1"
		."\n AND ((".$this->db->quoteId('alevel')." <= :lowlevel) OR (".$this->db->quoteId('alevel')." = :exactlevel))";
		$stmt = $this->db->prepareLimit($sql, 0, 1);
		$stmt->bindParam(':seotitle', $seotitle, PDO::PARAM_STR);
		$stmt->bindParam(':lowlevel', $lowlev, PDO::PARAM_INT);
		$stmt->bindParam(':exactlevel', $exactlev, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_OBJ);
	}


	/***************************************/
	/* GET ALL TRANSLATIONS FOR A CATEGORY */
	/***************************************/
	public function categoryTranslate($catid, $lng) {
		$query = "SELECT ".$this->db->quoteId('element').", ".$this->db->quoteId('translation')." FROM ".$this->db->quoteId('#__translations')
		."\n WHERE ".$this->db->quoteId('category')."=".$this->db->quote('com_content')." AND ".$this->db->quoteId('language')." = :lng"
		."\n AND ".$this->db->quoteId('elid')." = :catid";
		$stmt = $this->db->prepare($query);
		$stmt->bindParam(':lng', $lng, PDO::PARAM_STR);
		$stmt->bindParam(':catid', $catid, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchPairs();
	}


	/******************************************************************/
	/* MAKE A PATHWAY TREE TO CATEGORY (RETURN FALSE ON ACCESS ERROR) */
	/******************************************************************/
	public function categoryTree($catid, $curctg=null, $onlypublic=false) {
		if ($onlypublic === true) {
			$lowlevel = 0;
			$exactlevel = 0;
		} else {
			$elxis = eFactory::getElxis();
			$lowlevel = $elxis->acl()->getLowLevel();
			$exactlevel = $elxis->acl()->getExactLevel();
		}

		$categories = array();
		if ($curctg) { //added to reduce queries by 1
			$ctg = new stdClass;
			$ctg->catid = $curctg->catid;
			$ctg->title = $curctg->title;
			$ctg->seotitle = $curctg->seotitle;
			$ctg->link = $curctg->seolink;
			$categories[] = $ctg;
			$catid = (int)$curctg->parent_id;
			if ($catid < 1) { return $categories; }
		}

		$sql = "SELECT ".$this->db->quoteId('catid').", ".$this->db->quoteId('parent_id').", ".$this->db->quoteId('title').", "
		."\n ".$this->db->quoteId('seotitle').", ".$this->db->quoteId('seolink').", ".$this->db->quoteId('published').", ".$this->db->quoteId('alevel')
		."\n FROM ".$this->db->quoteId('#__categories')." WHERE ".$this->db->quoteId('catid')." = :ctg";
		$stmt = $this->db->prepareLimit($sql, 0, 1);

		$error = false;
		$continue = true;
		$elids = array();
		while ($continue === true) {
			$elids[] = $catid;
			$stmt->bindParam(':ctg', $catid, PDO::PARAM_INT);
			$stmt->execute();
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			if (!$row) { $continue = false; break; }
			$allowed = (($row['alevel'] <= $lowlevel) || ($row['alevel'] == $exactlevel)) ? true : false;
			if ($row['published'] == 0) {
				$error = true;
				$continue = false;
				break;
			} elseif ($allowed == false) {
				$error = true;
				$continue = false;
				break;
			}

			$ctg = new stdClass;
			$ctg->catid = (int)$row['catid'];
			$ctg->title = $row['title'];
			$ctg->seotitle = $row['seotitle'];
			$ctg->link = $row['seolink'];
			$categories[] = $ctg;
			unset($ctg);

			if ($row['parent_id'] > 0) {
				$catid = (int)$row['parent_id'];
			} else {
				$continue = false;
			}
		}

		if ($error === true) { return false; }
		if (count($categories) == 0) { return array(); }
		$tree = array_reverse($categories);

		return $tree;
	}


	/**************************************************/
	/* GET TITLES TRANSLATION FOR MULTIPLE CATEGORIES */
	/**************************************************/
	public function categoriesTranslate($elids, $lng) {
		$sql = "SELECT ".$this->db->quoteId('elid').", ".$this->db->quoteId('translation')." FROM ".$this->db->quoteId('#__translations')
		."\n WHERE ".$this->db->quoteId('category')."=".$this->db->quote('com_content')." AND ".$this->db->quoteId('element')."=".$this->db->quote('category_title')
		."\n AND ".$this->db->quoteId('language')." = :lng AND ".$this->db->quoteId('elid')." IN (".implode(", ", $elids).")";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':lng', $lng, PDO::PARAM_STR);
		$stmt->execute();
		return $stmt->fetchPairs();
	}


	/************************************/
	/* GET COMPONENT CONTENT PARAMETERS */
	/************************************/
	public function componentParams() {
		$sql = "SELECT ".$this->db->quoteId('params')." FROM ".$this->db->quoteId('#__components')
		."\n WHERE ".$this->db->quoteId('component')." = ".$this->db->quote('com_content');
		$stmt = $this->db->prepareLimit($sql, 0, 1);
		$stmt->execute();
		return (string)$stmt->fetchResult();
	}


	/***********************************/
	/* GET CONTENT CATEGORY PARAMETERS */
	/***********************************/
	public function categoryParams($catid) {
		$sql = "SELECT ".$this->db->quoteId('params')." FROM ".$this->db->quoteId('#__categories')
		."\n WHERE ".$this->db->quoteId('catid')." = :ctg";
		$stmt = $this->db->prepareLimit($sql, 0, 1);
		$stmt->bindParam(':ctg', $catid, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetchResult();
		return $result ? (string)$result : '';
	}


	/***********************************/
	/* COUNT CATEGORY'S TOTAL ARTICLES */
	/***********************************/
	public function countArticles($catid) {
		$elxis = eFactory::getElxis();
		$lowlev = $elxis->acl()->getLowLevel();
		$exactlev = $elxis->acl()->getExactLevel();

		$catid = (int)$catid;
		$sql = "SELECT COUNT(".$this->db->quoteId('id').") FROM ".$this->db->quoteId('#__content')
		."\n WHERE ".$this->db->quoteId('catid')." = :ctg AND ".$this->db->quoteId('published')."=1"
		."\n AND ((".$this->db->quoteId('alevel')." <= :lowlevel) OR (".$this->db->quoteId('alevel')." = :exactlevel))";
		$stmt = $this->db->prepareLimit($sql, 0, 1);
		$stmt->bindParam(':ctg', $catid, PDO::PARAM_INT);
		$stmt->bindParam(':lowlevel', $lowlev, PDO::PARAM_INT);
		$stmt->bindParam(':exactlevel', $exactlev, PDO::PARAM_INT);
		$stmt->execute();
		return (int)$stmt->fetchResult();
	}


	/*********************************************/
	/* FETCH ALL SUB-CATEGORIES (ONE LEVEL ONLY) */
	/*********************************************/
	public function fetchSubCategories($catid) {
		$elxis = eFactory::getElxis();
		$lowlev = $elxis->acl()->getLowLevel();
		$exactlev = $elxis->acl()->getExactLevel();

		$sql = "SELECT ".$this->db->quoteId('catid').", ".$this->db->quoteId('title').", ".$this->db->quoteId('seotitle')
		."\n FROM ".$this->db->quoteId('#__categories')." WHERE ".$this->db->quoteId('published')."=1 AND ".$this->db->quoteId('parent_id')." = :ctg"
		."\n AND ((".$this->db->quoteId('alevel')." <= :lowlevel) OR (".$this->db->quoteId('alevel')." = :exactlevel))"
		."\n ORDER BY ".$this->db->quoteId('ordering')." ASC";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':ctg', $catid, PDO::PARAM_INT);
		$stmt->bindParam(':lowlevel', $lowlev, PDO::PARAM_INT);
		$stmt->bindParam(':exactlevel', $exactlev, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_OBJ);
	}


	/*******************************/
	/* FETCH A CATEGORY'S ARTICLES */
	/*******************************/
	public function fetchArticles($catid, $limitstart, $limit, $order, $onlypublic=false) {
		$elxis = eFactory::getElxis();
		if ($onlypublic === true) {
			$lowlev = 0;
			$exactlev = 0;
		} else {
			$lowlev = $elxis->acl()->getLowLevel();
			$exactlev = $elxis->acl()->getExactLevel();
		}

		switch ($order) {
			case 'ma': $orderby = 'modified'; $dir = 'ASC'; break;
			case 'md': $orderby = 'modified'; $dir = 'DESC'; break;
			case 'oa': $orderby = 'ordering'; $dir = 'ASC'; break;
			case 'od': $orderby = 'ordering'; $dir = 'DESC'; break;
			case 'ta': $orderby = 'title'; $dir = 'ASC'; break;
			case 'td': $orderby = 'title'; $dir = 'DESC'; break;
			case 'ca': $orderby = 'created'; $dir = 'ASC'; break;
			case 'cd': default: $orderby = 'created'; $dir = 'DESC';  break;
		}

		$sql  = "SELECT ".$this->db->quoteId('id').", ".$this->db->quoteId('title').", ".$this->db->quoteId('seotitle').","
		."\n ".$this->db->quoteId('subtitle').", ".$this->db->quoteId('introtext').", ".$this->db->quoteId('metakeys').","
		."\n ".$this->db->quoteId('image').", ".$this->db->quoteId('caption').", ".$this->db->quoteId('created').","
		."\n ".$this->db->quoteId('created_by').", ".$this->db->quoteId('created_by_name').", ".$this->db->quoteId('modified').","
		."\n ".$this->db->quoteId('modified_by').", ".$this->db->quoteId('modified_by_name').", ".$this->db->quoteId('hits')
		."\n FROM ".$this->db->quoteId('#__content')." WHERE ".$this->db->quoteId('catid')." = :ctg AND ".$this->db->quoteId('published')."=1"
		."\n AND ((".$this->db->quoteId('alevel')." <= :lowlevel) OR (".$this->db->quoteId('alevel')." = :exactlevel))"
		."\n ORDER BY ".$this->db->quoteId($orderby)." ".$dir;

		$stmt = $this->db->prepareLimit($sql, $limitstart, $limit);
		$stmt->bindParam(':ctg', $catid, PDO::PARAM_INT);
		$stmt->bindParam(':lowlevel', $lowlev, PDO::PARAM_INT);
		$stmt->bindParam(':exactlevel', $exactlev, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAllAssoc('id', PDO::FETCH_OBJ);
	}


	/********************************************/
	/* FETCH ARTICLES FOR RSS/ATOM GENERIC FEED */
	/********************************************/
	public function fetchFeedArticles($limit=10) {
		$elxis = eFactory::getElxis();
		
		$lowlev = 0;
		$exactlev = 0;
		$sql  = "SELECT a.id, a.title, a.seotitle, a.subtitle, a.introtext, a.image, a.created, a.created_by, a.created_by_name,"
		."\n a.modified, a.modified_by, a.modified_by_name, c.catid, c.seolink, c.title AS category"
		."\n FROM ".$this->db->quoteId('#__content')." a"
		."\n LEFT JOIN ".$this->db->quoteId('#__categories')." c ON c.catid=a.catid"
		."\n WHERE a.published =1 AND ((a.alevel <= :lowlevel) OR (a.alevel = :exactlevel))"
		."\n ORDER BY ".$this->db->quoteId('created')." DESC";
		$stmt = $this->db->prepareLimit($sql, 0, $limit);
		$stmt->bindParam(':lowlevel', $lowlev, PDO::PARAM_INT);
		$stmt->bindParam(':exactlevel', $exactlev, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAllAssoc('id', PDO::FETCH_OBJ);
	}


	/*****************************/
	/* GET ARTICLES TRANSLATIONS */
	/*****************************/
	public function articlesTranslate($elids, $lng) {
		$sql = "SELECT ".$this->db->quoteId('elid').", ".$this->db->quoteId('element').", ".$this->db->quoteId('translation')
		."\n FROM ".$this->db->quoteId('#__translations')
		."\n WHERE ".$this->db->quoteId('category')."=".$this->db->quote('com_content')." AND ".$this->db->quoteId('language')." = :lng"
		."\n AND ((".$this->db->quoteId('element')." = ".$this->db->quote('title').") OR (".$this->db->quoteId('element')." = ".$this->db->quote('subtitle').")"
		."\n OR (".$this->db->quoteId('element')." = ".$this->db->quote('introtext').") OR (".$this->db->quoteId('element')." = ".$this->db->quote('caption')."))"
		."\n AND ".$this->db->quoteId('elid')." IN (".implode(", ", $elids).")";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':lng', $lng, PDO::PARAM_STR);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}


	/*************************/
	/* FETCH ARTICLE FROM DB */
	/*************************/
	public function fetchArticle($seotitle='', $id=0) {
		$id = (int)$id;
		if (($seotitle == '') && ($id < 1)) { return null; }
		$elxis = eFactory::getElxis();
		$lowlev = $elxis->acl()->getLowLevel();
		$exactlev = $elxis->acl()->getExactLevel();

		$sql = "SELECT * FROM ".$this->db->quoteId('#__content')." WHERE ".$this->db->quoteId('published')."=1";
		if ($id > 0) {
			$sql .= " AND ".$this->db->quoteId('id')." = :artid";
		} else {
			$sql .= " AND ".$this->db->quoteId('seotitle')." = :seotitle";
		}
		$sql .= "\n AND ((".$this->db->quoteId('alevel')." <= :lowlevel) OR (".$this->db->quoteId('alevel')." = :exactlevel))";
		$stmt = $this->db->prepareLimit($sql, 0, 1);
		if ($id > 0) {
			$stmt->bindParam(':artid', $id, PDO::PARAM_INT);
		} else {
			$stmt->bindParam(':seotitle', $seotitle, PDO::PARAM_STR);
		}
		$stmt->bindParam(':lowlevel', $lowlev, PDO::PARAM_INT);
		$stmt->bindParam(':exactlevel', $exactlev, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_OBJ);
	}


	/***************************************/
	/* GET ALL TRANSLATIONS FOR AN ARTICLE */
	/***************************************/
	public function articleTranslate($id, $lng) {
		$sql = "SELECT ".$this->db->quoteId('element').", ".$this->db->quoteId('translation')." FROM ".$this->db->quoteId('#__translations')
		."\n WHERE ".$this->db->quoteId('category')."=".$this->db->quote('com_content')." AND ".$this->db->quoteId('language')." = :lng"
		."\n AND ".$this->db->quoteId('elid')." = :id";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':lng', $lng, PDO::PARAM_STR);
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchPairs();
	}


	/*************************/
	/* UPDATE ARTICLE'S HITS */
	/*************************/
	public function updateHits($id, $hits=-1) {
		if ($hits < 0) {
			$sql = "SELECT ".$this->db->quoteId('hits')." FROM ".$this->db->quoteId('#__content')." WHERE ".$this->db->quoteId('id')." = :artid";
			$stmt = $this->db->prepareLimit($sql, 0, 1);
			$stmt->bindParam(':artid', $id, PDO::PARAM_INT);
			$stmt->execute();
			$hits = (int)$stmt->fetchResult();
		}

		$hits++;
		$sql = "UPDATE ".$this->db->quoteId('#__content')." SET ".$this->db->quoteId('hits')." = :newhits WHERE ".$this->db->quoteId('id')." = :artid";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':newhits', $hits, PDO::PARAM_INT);
		$stmt->bindParam(':artid', $id, PDO::PARAM_INT);
		$stmt->execute();
	}


	/*******************************/
	/* FETCH NEXT/PREVIOUS ARTICLE */
	/*******************************/
	public function fetchChainedArticles($row, $order) {
		$elxis = eFactory::getElxis();
		$lowlev = $elxis->acl()->getLowLevel();
		$exactlev = $elxis->acl()->getExactLevel();
		switch ($order) {
			case 'ma': $orderby = 'modified'; $dir = 'ASC'; break;
			case 'md': $orderby = 'modified'; $dir = 'DESC'; break;
			case 'oa': $orderby = 'ordering'; $dir = 'ASC'; break;
			case 'od': $orderby = 'ordering'; $dir = 'DESC'; break;
			case 'ta': $orderby = 'title'; $dir = 'ASC'; break;
			case 'td': $orderby = 'title'; $dir = 'DESC'; break;
			case 'ca': $orderby = 'created'; $dir = 'ASC'; break;
			case 'cd': default: $orderby = 'created'; $dir = 'DESC'; break;
		}

		$sql  = "SELECT ".$this->db->quoteId('id').", ".$this->db->quoteId('title').", ".$this->db->quoteId('seotitle').",".$this->db->quoteId('image')
		."\n FROM ".$this->db->quoteId('#__content')." WHERE ".$this->db->quoteId('catid')." = :ctg AND ".$this->db->quoteId('published')."=1"
		."\n AND ((".$this->db->quoteId('alevel')." <= :lowlevel) OR (".$this->db->quoteId('alevel')." = :exactlevel))"
		."\n ORDER BY ".$this->db->quoteId($orderby)." ".$dir;
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':ctg', $row->catid, PDO::PARAM_INT);
		$stmt->bindParam(':lowlevel', $lowlev, PDO::PARAM_INT);
		$stmt->bindParam(':exactlevel', $exactlev, PDO::PARAM_INT);
		$stmt->execute();
		$arts = $stmt->fetchAll(PDO::FETCH_OBJ);
		if (!$arts) { return null; }

		$chained = array('previous' => null, 'next' => null);
		foreach ($arts as $key => $art) {
			if ($art->id == $row->id) {
				$prev = $key - 1;
				$next = $key + 1;
				if ($prev > -1) { $chained['previous'] = $arts[$prev]; }
				if (isset($arts[$next])) { $chained['next'] = $arts[$next]; }
				break;
			}
		}
		unset($arts, $stmt);
		return $chained;
	}


	/********************************/
	/* GET TITLES ONLY TRANSLATIONS */
	/********************************/
	public function articlesTitlesTranslate($elids, $lng) {
		$sql = "SELECT ".$this->db->quoteId('elid').", ".$this->db->quoteId('translation')." FROM ".$this->db->quoteId('#__translations')
		."\n WHERE ".$this->db->quoteId('category')."=".$this->db->quote('com_content')." AND ".$this->db->quoteId('language')." = :lng"
		."\n AND ".$this->db->quoteId('element')." = ".$this->db->quote('title')
		."\n AND ".$this->db->quoteId('elid')." IN (".implode(", ", $elids).")";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':lng', $lng, PDO::PARAM_STR);
		$stmt->execute();
		return $stmt->fetchPairs();
	}


	/*************************/
	/* FETCH TAGGED ARTICLES */
	/*************************/
	public function fetchTagArticles($tag, $translate=false, $lng='') {
		$elxis = eFactory::getElxis();

		$tag = "%".$tag."%";
		$elids = array();
		if ($translate && ($lng != '')) {
			$trcategory = 'com_content';
			$trelement = 'metakeys';
			$sql = "SELECT ".$this->db->quoteId('elid')." FROM ".$this->db->quoteId('#__translations')." WHERE ".$this->db->quoteId('category')." = :xcat"
			."\n AND ".$this->db->quoteId('element')." = :xelement AND ".$this->db->quoteId('language')." = :xlng  AND ".$this->db->quoteId('translation')." LIKE :tag"
			."\n ORDER BY ".$this->db->quoteId('trid')." DESC";
			$stmt = $this->db->prepareLimit($sql, 0, 80);
			$stmt->bindParam(':xcat', $trcategory, PDO::PARAM_STR);
			$stmt->bindParam(':xelement', $trelement, PDO::PARAM_STR);
			$stmt->bindParam(':xlng', $lng, PDO::PARAM_STR);
			$stmt->bindParam(':tag', $tag, PDO::PARAM_STR);
			$stmt->execute();
			$elids = $stmt->fetchCol();
		}

		$lowlev = $elxis->acl()->getLowLevel();
		$exactlev = $elxis->acl()->getExactLevel();

		$query_tag = true;
		$sql = "SELECT a.id, a.title, a.seotitle, a.subtitle, a.created, a.image, c.catid,"
		."\n c.seolink, c.title AS category, c.parent_id, c.published AS catpub, c.alevel AS catlevel"
		."\n FROM #__content a"
		."\n LEFT JOIN #__categories c ON c.catid=a.catid";
		if ($elids) {
			if (count($elids) > 10) {
				$query_tag = false;
				$sql .= "\n WHERE a.id IN (".implode(', ',$elids).")";
			} else {
				$sql .= "\n WHERE ((a.id IN (".implode(', ',$elids).")) OR (a.metakeys LIKE :tag))";
			}
		} else {
			$sql .= "\n WHERE a.metakeys LIKE :tag";
		}
		$sql .= " AND a.published=1 AND ((a.alevel <= :lowlevel) OR (a.alevel = :exactlevel))"
		."\n ORDER BY a.created DESC";
		$stmt = $this->db->prepareLimit($sql, 0, 80);
		if ($query_tag) {
			$stmt->bindParam(':tag', $tag, PDO::PARAM_STR);
		}
		$stmt->bindParam(':lowlevel', $lowlev, PDO::PARAM_INT);
		$stmt->bindParam(':exactlevel', $exactlev, PDO::PARAM_INT);
		$stmt->execute();
		$articles = $stmt->fetchAllAssoc('id', PDO::FETCH_OBJ);
		if (!$articles) { return null; }

		$ctg_check = array();
		foreach ($articles as $id => $article) {
			if ((int)$article->catid === 0) {
				$articles[$id]->link = $article->seotitle.'.html';
				continue;
			}
			if ((int)$article->catpub === 0) { unset($articles[$id]); continue; }
			if (((int)$article->catlevel > $lowlev) && ((int)$article->catlevel <> $exactlev)) {
				 unset($articles[$id]);
				 continue;
			}
			$articles[$id]->link = $article->seolink.$article->seotitle.'.html';
			if ((int)$article->parent_id > 0) { $ctg_check[] = (int)$article->parent_id; }
		}

		if (!$articles) { return null; }
		if (count($ctg_check) > 0) {
			$ctg_check = array_unique($ctg_check);
			$remove_cats = array();
			$sql = "SELECT ".$this->db->quoteId('catid').", ".$this->db->quoteId('published').", ".$this->db->quoteId('alevel')
			."\n FROM ".$this->db->quoteId('#__categories')." WHERE ".$this->db->quoteId('catid')." IN (".implode(',', $ctg_check).")";
			$stmt = $this->db->prepare($sql);
			$stmt->execute();
			$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if ($categories) {
				foreach ($categories as $ctg) {
					if ((int)$ctg['published'] === 0) { $remove_cats[] = $ctg['catid']; continue; }
					if (((int)$ctg['alevel'] > $lowlev) && ((int)$ctg['alevel'] <> $exactlev)) {
						$remove_cats[] = $ctg['catid']; continue;
					}
				}
			}
			unset($categories);
			if ($remove_cats) {
				foreach ($articles as $id => $article) {
					if ((int)$article->catid == 0) { continue; }
					if (in_array($article->catid, $remove_cats)) {
						unset($articles[$id]);
						continue;
					}
					if (in_array($article->parent_id, $remove_cats)) {
						unset($articles[$id]);
						continue;
					}
				}
			}
		}
		unset($ctg_check);
		if (!$articles) { return null; }

		$rows = array();
		foreach ($articles as $id => $article) {
			$row = new stdClass;
			$row->id = $article->id;
			$row->title = $article->title;
			$row->subtitle = $article->subtitle;
			$row->created = $article->created;
			$row->image = $article->image;
			$row->link = $article->link;
			$row->catid = (int)$article->catid;
			$row->category = $article->category;
			$row->catlink = $article->seolink;
			$rows[$id] = $row;
		}

		unset($articles);
		return $rows;
	}


	/**************************************************/
	/* GET ARTICLES TITLES AND SUBTITLES TRANSLATIONS */
	/**************************************************/
	public function articlesTitleSubTranslate($elids, $lng) {
		$sql = "SELECT ".$this->db->quoteId('elid').", ".$this->db->quoteId('element').", ".$this->db->quoteId('translation')
		."\n FROM ".$this->db->quoteId('#__translations')
		."\n WHERE ".$this->db->quoteId('category')."=".$this->db->quote('com_content')." AND ".$this->db->quoteId('language')." = :lng"
		."\n AND ((".$this->db->quoteId('element')." = ".$this->db->quote('title').") OR (".$this->db->quoteId('element')." = ".$this->db->quote('subtitle')."))"
		."\n AND ".$this->db->quoteId('elid')." IN (".implode(", ", $elids).")";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':lng', $lng, PDO::PARAM_STR);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}


	/*********************************************************/
	/* FETCH PUBLIC FEED CATEGORIES FROM DB (2 FIRST LEVELS) */
	/*********************************************************/
	public function fetchFeedCategories() {
		$dbtype = $this->db->getType();
		if (in_array($dbtype, array('mysql', 'pgsql', 'mssql', 'oci'))) {
			$sql = "SELECT c.catid, c.title, c.seotitle, (SELECT COUNT(a.id) FROM #__content a WHERE a.catid=c.catid AND a.published=1 AND a.alevel=0) AS articles"
			."\n FROM #__categories c"
			."\n WHERE c.parent_id=0 AND c.published=1 AND c.alevel=0"
			."\n GROUP BY c.catid"
			."\n ORDER BY c.ordering ASC";
		} else {
			$sql = "SELECT c.catid, c.title, c.seotitle, -1 AS articles"
			."\n FROM #__categories c"
			."\n WHERE c.parent_id=0 AND c.published=1 AND c.alevel=0"
			."\n ORDER BY c.ordering ASC";
		}
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$rows = $stmt->fetchAllAssoc('catid', PDO::FETCH_OBJ);
		if (!$rows) { return null; }

		$elids = array();
		foreach ($rows as $i => $row) {
			$rows[$i]->categories = array();
			$elids[] = $row->catid;
		}

		if (in_array($dbtype, array('mysql', 'pgsql', 'mssql', 'oci'))) {
			$sql = "SELECT c.catid, c.parent_id, c.title, c.seotitle, (SELECT COUNT(a.id) FROM #__content a WHERE a.catid=c.catid AND a.published=1 AND a.alevel=0) AS articles"
			."\n FROM #__categories c"
			."\n WHERE c.published=1 AND c.alevel=0 AND c.parent_id IN (".implode(',',$elids).")"
			."\n GROUP BY c.catid"
			."\n ORDER BY c.ordering ASC";
		} else {
			$sql = "SELECT c.catid, c.parent_id, c.title, c.seotitle, -1 AS articles"
			."\n FROM #__categories c"
			."\n WHERE c.published=1 AND c.alevel=0 AND c.parent_id IN (".implode(',',$elids).")"
			."\n ORDER BY c.ordering ASC";
		}
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$subcats = $stmt->fetchAll(PDO::FETCH_OBJ);
		if (!$subcats) { return array($rows, $elids); }

		foreach ($subcats as $subcat) {
			$p = $subcat->parent_id;
			if (!isset($rows[$p])) { continue; }
			$c = $subcat->catid;
			$rows[$p]->categories[$c] = $subcat;
			$elids[] = $c;
		}
		return array($rows, $elids);
	}


	/****************************/
	/* FETCH ARTICLE'S COMMENTS */
	/****************************/
	public function fetchComments($id, $onlypublished=true) {
		$sql = "SELECT c.*, u.avatar FROM ".$this->db->quoteId('#__comments')." c"
		."\n LEFT JOIN ".$this->db->quoteId('#__users')." u ON u.uid=c.uid"
		."\n WHERE c.element = ".$this->db->quote('com_content')." AND c.elid = :artid";
		if ($onlypublished) { $sql .= ' AND c.published=1'; }
		$sql .= "\n ORDER BY c.created ASC";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':artid', $id, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_OBJ);
	}


	/*******************************/
	/* FETCH SPECIFIC ONLY COMMENT */
	/*******************************/
	public function fetchComment($id) {
		$sql = "SELECT * FROM ".$this->db->quoteId('#__comments')
		."\n WHERE ".$this->db->quoteId('element')." = ".$this->db->quote('com_content')." AND ".$this->db->quoteId('id')." = :comid";
		$stmt = $this->db->prepareLimit($sql, 0, 1);
		$stmt->bindParam(':comid', $id, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_OBJ);
	}


	/*********************/
	/* PUBLISH A COMMENT */
	/*********************/
	public function publishComment($id) {
		$sql = "UPDATE ".$this->db->quoteId('#__comments')." SET ".$this->db->quoteId('published')." = 1 WHERE ".$this->db->quoteId('id')." = :comid";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':comid', $id, PDO::PARAM_INT);
		return $stmt->execute();
	}


	/********************/
	/* DELETE A COMMENT */
	/********************/
	public function deleteComment($id) {
		$sql = "DELETE FROM ".$this->db->quoteId('#__comments')." WHERE ".$this->db->quoteId('id')." = :comid";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':comid', $id, PDO::PARAM_INT);
		return $stmt->execute();
	}


	/***************************/
	/* GET SITE ADMINISTRATORS */
	/***************************/
	public function getAdmins() {
		$sql = "SELECT ".$this->db->quoteId('uid').", ".$this->db->quoteId('firstname').", ".$this->db->quoteId('lastname').", ".$this->db->quoteId('email').", ".$this->db->quoteId('preflang')
		."\n FROM  ".$this->db->quoteId('#__users')." WHERE  ".$this->db->quoteId('gid')." = 1 AND ".$this->db->quoteId('block')." = 0";
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_OBJ);
	}


	//---- backend -----------------


	/**********************/
	/* GET ALL CATEGORIES */
	/**********************/
	public function getAllCategories($options=false) {
		if (!is_array($options)) {
			$sql = "SELECT * FROM ".$this->db->quoteId('#__categories');
			$sql .= " ORDER BY ".$this->db->quoteId('parent_id')." ASC, ".$this->db->quoteId('ordering')." ASC";
			$stmt = $this->db->prepare($sql);
			$stmt->execute();
			$rows = $stmt->fetchAll(PDO::FETCH_OBJ);
			return $rows;
		}

		$bind = false;
		$sql = "SELECT * FROM ".$this->db->quoteId('#__categories');
		switch ($options['qtype']) {
			case 'catid':
				$value = (int)$options['query'];
				if ($value > 0) {
					$sql .= " WHERE ".$this->db->quoteId('catid')." = :xval";
					$data_type = PDO::PARAM_INT;
					$bind = true;
				}
			break;
			case 'title': case 'seotitle':
				if ($options['query'] != '') {
					$sql .= " WHERE ".$this->db->quoteId($options['qtype'])." LIKE :xval";
					$data_type = PDO::PARAM_STR;
					$value = '%'.$options['query'].'%';
					$bind = true;
				}
			break;
			default: break;
		}
	
		switch ($options['sortname']) {
			case 'catid':
				$sql .= ' ORDER BY '.$this->db->quoteId('catid').' '.strtoupper($options['sortorder']);
			break;
			case 'treename': default:
				$sql .= ' ORDER BY '.$this->db->quoteId('title').' '.strtoupper($options['sortorder']);
			break;
		}

		$stmt = $this->db->prepareLimit($sql, $options['limitstart'], $options['rp']);
		if ($bind) { $stmt->bindParam(':xval', $value, $data_type); }
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_OBJ);
		return $rows;
	}


	/****************************************************/
	/* COUNT CATEGORY'S OR ARRAY OF CATEGORIES ARTICLES */
	/****************************************************/
	public function countCtgArticles($catid) {
		$sql = "SELECT COUNT(id) FROM ".$this->db->quoteId('#__content')." WHERE ".$this->db->quoteId('catid')." = :xctg";
		$stmt = $this->db->prepare($sql);
		if (is_array($catid)) {
			$result = array();
			foreach ($catid as $ctg) {
				$stmt->bindParam(':xctg', $ctg, PDO::PARAM_INT);
				$stmt->execute();
				$result[$ctg] = (int)$stmt->fetchResult();
			}
			return $result;
		}

		$catid = (int)$catid;
		$stmt->bindParam(':xctg', $catid, PDO::PARAM_INT);
		$stmt->execute();
		return (int)$stmt->fetchResult();
	}


	/**********************************/
	/* COUNT CATEGORIES WITH CRITERIA */
	/**********************************/
	public function countAllCategories($column='', $value='') {
		$bind = false;
		$sql = "SELECT COUNT(catid) FROM ".$this->db->quoteId('#__categories');
		switch ($column) {
			case 'catid':
				$value = (int)$value;
				if ($value > 0) {
					$sql .= " WHERE ".$this->db->quoteId('catid')." = :xval";
					$value = (int)$value;
					$data_type = PDO::PARAM_INT;
					$bind = true;
				}
			break;
			case 'title': case 'seotitle':
				if ($value != '') {
					$sql .= " WHERE ".$this->db->quoteId($column)." LIKE :xval";
					$data_type = PDO::PARAM_STR;
					$value = '%'.$value.'%';
					$bind = true;
				}
			break;
			default: break;
		}
		$stmt = $this->db->prepare($sql);
		if ($bind) { $stmt->bindParam(':xval', $value, $data_type); }
		$stmt->execute();
		return (int)$stmt->fetchResult();
	}


	/*************************************/
	/* PUBLISH/UNPUBLISH/TOGGLE CATEGORY */
	/*************************************/
	public function publishCategory($catid, $publish=-1, $recursive=true) {
		$response = array('success' => false, 'message' => 'Unknown error');
		if ($catid < 1) { return $response; } //just in case
		if (eFactory::getElxis()->acl()->check('com_content', 'category', 'publish') < 1) {
			$response['message'] = eFactory::getLang()->get('NOTALLOWACTION');
			return $response;
		}

		if ($publish == -1) { //toggle status
			$sql = "SELECT ".$this->db->quoteId('published')." FROM ".$this->db->quoteId('#__categories')
			."\n WHERE ".$this->db->quoteId('catid')." = :xcatid";
			$stmt = $this->db->prepareLimit($sql, 0, 1);
			$stmt->bindParam(':xcatid', $catid, PDO::PARAM_INT);
			$stmt->execute();
			$publish = ((int)$stmt->fetchResult() == 1) ? 0 : 1;
		}

		$items_to_publish = array($catid);
		if (($publish == 0) && ($recursive === true)) { //apply recursively
			$sql = "SELECT ".$this->db->quoteId('catid')." FROM ".$this->db->quoteId('#__categories')." WHERE ".$this->db->quoteId('parent_id')." = :xparent";
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':xparent', $catid, PDO::PARAM_INT);
			$stmt->execute();
			$childs = $stmt->fetchCol(0);
			if ($childs) {
				foreach ($childs as $child) {
					$items_to_publish[] = $child;
					$stmt->bindParam(':xparent', $child, PDO::PARAM_INT);
					$stmt->execute();
					$childs2 = $stmt->fetchCol(0);
					if ($childs2) {
						foreach ($childs2 as $child2) {
							$items_to_publish[] = $child2;
							$stmt->bindParam(':xparent', $child2, PDO::PARAM_INT);
							$stmt->execute();
							$childs3 = $stmt->fetchCol(0);
							if ($childs3) {
								foreach ($childs3 as $child3) {
									$items_to_publish[] = $child3;
								}
							}
						}
					}
				}
			}
		}

		$sql = "UPDATE ".$this->db->quoteId('#__categories')." SET ".$this->db->quoteId('published')." = :xpub"
		."\n WHERE ".$this->db->quoteId('catid')." = :xcatid";
		$stmt = $this->db->prepare($sql);
		foreach ($items_to_publish as $item) {
			$stmt->bindParam(':xpub', $publish, PDO::PARAM_INT);
			$stmt->bindParam(':xcatid', $item, PDO::PARAM_INT);
			$stmt->execute();
		}

		$response['success'] = true;
		$response['message'] = 'Success';
		return $response;
	}


	/********************/
	/* DELETE CATEGORY */
	/********************/
	public function deleteCategory($catid) {
		$response = array('success' => false, 'message' => 'Unknown error');
		if ($catid < 1) { return $response; } //just in case
		if (eFactory::getElxis()->acl()->check('com_content', 'category', 'delete') < 1) {
			$response['message'] = eFactory::getLang()->get('NOTALLOWACTION');
			return $response;
		}

		$items_to_delete = array($catid);
		$sql = "SELECT ".$this->db->quoteId('catid')." FROM ".$this->db->quoteId('#__categories')
		."\n WHERE ".$this->db->quoteId('parent_id')." = :xparent";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xparent', $catid, PDO::PARAM_INT);
		$stmt->execute();
		$childs = $stmt->fetchCol(0);

		if ($childs) {
			foreach ($childs as $child) {
				$items_to_delete[] = $child;
				$stmt->bindParam(':xparent', $child, PDO::PARAM_INT);
				$stmt->execute();
				$childs2 = $stmt->fetchCol(0);
				if ($childs2) {
					foreach ($childs2 as $child2) {
						$items_to_delete[] = $child2;
						$stmt->bindParam(':xparent', $child2, PDO::PARAM_INT);
						$stmt->execute();
						$childs3 = $stmt->fetchCol(0);
						if ($childs3) {
							foreach ($childs3 as $child3) {
								$items_to_delete[] = $child3;
								$stmt->bindParam(':xparent', $child3, PDO::PARAM_INT);
								$stmt->execute();
								$childs4 = $stmt->fetchCol(0);
								if ($childs4) {
									foreach ($childs4 as $child4) {
										$items_to_delete[] = $child4;
									}
								}
							}
						}
					}
				}
			}
		}

		$sql = "SELECT COUNT(id) FROM ".$this->db->quoteId('#__content')
		."\n WHERE ".$this->db->quoteId('catid')." IN (".implode(',',$items_to_delete).")";
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$articles = (int)$stmt->fetchResult();
		if ($articles > 0) {
			$response['message'] = eFactory::getLang()->get('CNOT_DEL_CATS_ARTICLES');
			return $response;
		}

		$sql = "DELETE FROM ".$this->db->quoteId('#__categories')." WHERE ".$this->db->quoteId('catid')." = :xcatid";
		$stmt = $this->db->prepare($sql);
		foreach ($items_to_delete as $item) {
			$stmt->bindParam(':xcatid', $item, PDO::PARAM_INT);
			$stmt->execute();
		}

		$trcategory = 'com_content';
		$trelement = 'category_title';
		$sql = "DELETE FROM ".$this->db->quoteId('#__translations')." WHERE ".$this->db->quoteId('category')." = :xcat"
		."\n AND ".$this->db->quoteId('element')." = :xelement AND ".$this->db->quoteId('elid')." = :xelid";
		$stmt = $this->db->prepare($sql);
		foreach ($items_to_delete as $item) {
			$stmt->bindParam(':xcat', $trcategory, PDO::PARAM_STR);
			$stmt->bindParam(':xelement', $trelement, PDO::PARAM_STR);
			$stmt->bindParam(':xelid', $item, PDO::PARAM_INT);
			$stmt->execute();
		}

		$trelement = 'category_description';
		foreach ($items_to_delete as $item) {
			$stmt->bindParam(':xcat', $trcategory, PDO::PARAM_STR);
			$stmt->bindParam(':xelement', $trelement, PDO::PARAM_STR);
			$stmt->bindParam(':xelid', $item, PDO::PARAM_INT);
			$stmt->execute();
		}

		$celement = 'com_content';
		$sql = "DELETE FROM ".$this->db->quoteId('#__comments')." WHERE ".$this->db->quoteId('element')." = :xelement"
		."\n AND ".$this->db->quoteId('elid')." = :xelid";
		$stmt = $this->db->prepare($sql);
		foreach ($items_to_delete as $item) {
			$stmt->bindParam(':xelement', $celement, PDO::PARAM_STR);
			$stmt->bindParam(':xelid', $item, PDO::PARAM_INT);
			$stmt->execute();
		}

		$response['success'] = true;
		$response['message'] = 'Success';
		return $response;
	}


	/************************************/
	/* GET USER GROUPS AND THEIR LEVELS */
	/************************************/
	public function getGroups() {
		$sql = "SELECT * FROM ".$this->db->quoteId('#__groups')." ORDER BY ".$this->db->quoteId('level')." ASC";
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $rows;
	}


	/*****************************/
	/* COUNT COMPONENTS BY ROUTE */
	/*****************************/
	public function countComponentsByRoute($str) {
		$sql = "SELECT COUNT(id) FROM ".$this->db->quoteId('#__components')." WHERE ".$this->db->quoteId('route')." = :xseo";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xseo', $str, PDO::PARAM_STR);
		$stmt->execute();
		$c = (int)$stmt->fetchResult();
		return $c;
	}


	/*********************************/
	/* COUNT CATEGORIES BY SEO TITLE */
	/*********************************/
	public function countCategoriesBySEO($seotitle, $catid=0) {
		$sql = "SELECT COUNT(catid) FROM ".$this->db->quoteId('#__categories')." WHERE ".$this->db->quoteId('seotitle')." = :xseo";
		if ($catid > 0) { $sql .= " AND catid <> :xctg"; }
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xseo', $seotitle, PDO::PARAM_STR);
		if ($catid > 0) {
			$stmt->bindParam(':xctg', $catid, PDO::PARAM_INT);
		}
		$stmt->execute();
		$c = (int)$stmt->fetchResult();
		return $c;
	}


	/*******************************/
	/* COUNT ARTICLES BY SEO TITLE */
	/*******************************/
	public function countArticlesBySEO($seotitle, $id=0) {
		$sql = "SELECT COUNT(id) FROM ".$this->db->quoteId('#__content')." WHERE ".$this->db->quoteId('seotitle')." = :xseo";
		if ($id > 0) { $sql .= " AND id <> :xid"; }
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xseo', $seotitle, PDO::PARAM_STR);
		if ($id > 0) {
			$stmt->bindParam(':xid', $id, PDO::PARAM_INT);
		}
		$stmt->execute();
		$c = (int)$stmt->fetchResult();
		return $c;
	}


	/****************************/
	/* GET A CATEGORY'S SEOLINK */
	/****************************/
	public function categorySEOLink($catid) {
		$sql = "SELECT ".$this->db->quoteId('seolink')." FROM ".$this->db->quoteId('#__categories')
		."\n WHERE ".$this->db->quoteId('catid')." = :xcatid";
		$stmt = $this->db->prepareLimit($sql, 0, 1);
		$stmt->bindParam(':xcatid', $catid, PDO::PARAM_INT);
		$stmt->execute();
		$seolink = $stmt->fetchResult();
		return $seolink;
	}


	/******************************************/
	/* RE-BUILD CATEGORY'S CHILDREN SEO LINKS */
	/******************************************/
	public function rebuildSEOLinks($catid, $seolink) {
		$sql = "SELECT ".$this->db->quoteId('catid').", ".$this->db->quoteId('seotitle')." FROM ".$this->db->quoteId('#__categories')
		."\n WHERE ".$this->db->quoteId('parent_id')." = :xcatid";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xcatid', $catid, PDO::PARAM_INT);
		$stmt->execute();
		$children = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$categories = array();
		if ($children) {
			foreach ($children as $child) {
				$ctg = $child['catid'];
				$categories[$ctg] = $seolink.$child['seotitle'].'/';
				$stmt->bindParam(':xcatid', $ctg, PDO::PARAM_INT);
				$stmt->execute();
				$children2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
				if ($children2) {
					foreach ($children2 as $child2) {
						$ctg2 = $child2['catid'];
						$categories[$ctg2] = $seolink.$child['seotitle'].'/'.$child2['seotitle'].'/';
						$stmt->bindParam(':xcatid', $ctg2, PDO::PARAM_INT);
						$stmt->execute();
						$children3 = $stmt->fetchAll(PDO::FETCH_ASSOC);
						if ($children3) {
							foreach ($children3 as $child3) {
								$ctg3 = $child3['catid'];
								$categories[$ctg3] = $seolink.$child['seotitle'].'/'.$child2['seotitle'].'/'.$child3['seotitle'].'/';
								$stmt->bindParam(':xcatid', $ctg3, PDO::PARAM_INT);
								$stmt->execute();
								$children4 = $stmt->fetchAll(PDO::FETCH_ASSOC);
								if ($children4) {
									foreach ($children4 as $child4) {
										$ctg4 = $child4['catid'];
										$categories[$ctg4] = $seolink.$child['seotitle'].'/'.$child2['seotitle'].'/'
										.$child3['seotitle'].'/'.$child4['seotitle'].'/';
										$stmt->bindParam(':xcatid', $ctg4, PDO::PARAM_INT);
										$stmt->execute();
										$children5 = $stmt->fetchAll(PDO::FETCH_ASSOC);
										if ($children5) {
											$ctg5 = $child5['catid'];
											$categories[$ctg5] = $seolink.$child['seotitle'].'/'.$child2['seotitle'].'/'
											.$child3['seotitle'].'/'.$child4['seotitle'].'/'.$child5['seotitle'].'/';
										}
									}
								}
							}
						}
					}
				}
			}
		}

		if ($categories) {
			$sql = "UPDATE ".$this->db->quoteId('#__categories')." SET ".$this->db->quoteId('seolink')." = :xseo"
			."\n WHERE ".$this->db->quoteId('catid')." = :xcatid";
			$stmt = $this->db->prepare($sql);
			foreach ($categories as $ctg => $seo) {
				$stmt->bindParam(':xseo', $seo, PDO::PARAM_STR);
				$stmt->bindParam(':xcatid', $ctg, PDO::PARAM_INT);
				$stmt->execute();				
			}
		}
	}


	/*******************************/
	/* GET A CATEGORY ACCESS LEVEL */
	/*******************************/
	public function getCategoryLevel($catid) {
		$sql = "SELECT ".$this->db->quoteId('alevel')." FROM ".$this->db->quoteId('#__categories')
		."\n WHERE ".$this->db->quoteId('catid')." = :xcatid";
		$stmt = $this->db->prepareLimit($sql, 0, 1);
		$stmt->bindParam(':xcatid', $catid, PDO::PARAM_INT);
		$stmt->execute();
		return (int)$stmt->fetchResult();
	}


	/********************************/
	/* COUNT ARTICLES WITH CRITERIA */
	/********************************/
	public function countAllArticles($options) {
		if ($options['q'] != '') {
			return $this->countAllArticlesMLSearch($options);
		}

		$wheres = array();
		$pdo_binds = array();
		if (isset($options['catid']) && ($options['catid'] > -1)) {
			$wheres[] = $this->db->quoteId('catid').' = :xctg';
			$pdo_binds[':xctg'] = array($options['catid'], PDO::PARAM_INT);
		}
		if (isset($options['image']) && ($options['image'] > -1)) {
			if ($options['image'] == 0) {
				$value = '';
				$wheres[] = '(('.$this->db->quoteId('image').' = :ximg) OR ('.$this->db->quoteId('image').' IS NULL))';
				$pdo_binds[':ximg'] = array($value, PDO::PARAM_STR);
			} else {
				$value = '%images%';
				$wheres[] = $this->db->quoteId('image').' LIKE :ximg';
				$pdo_binds[':ximg'] = array($value, PDO::PARAM_STR);
			}
		}
		if (isset($options['published']) && ($options['published'] > -1)) {
			$wheres[] = $this->db->quoteId('published').' = :xpub';
			$pdo_binds[':xpub'] = array($options['published'], PDO::PARAM_INT);
		}
		if (isset($options['important']) && ($options['important'] > -1)) {
			$wheres[] = $this->db->quoteId('important').' = :ximp';
			$pdo_binds[':ximp'] = array($options['important'], PDO::PARAM_INT);
		}
		if (isset($options['author']) && ($options['author']  != '')) {
			$value = '%'.$options['author'].'%';
			$wheres[] = $this->db->quoteId('created_by_name').' LIKE :xauth';
			$pdo_binds[':xauth'] = array($value, PDO::PARAM_STR);
		}

		$sql = "SELECT COUNT(id) FROM ".$this->db->quoteId('#__content');
		if (count($wheres) > 0) { $sql .= ' WHERE '.implode(' AND ', $wheres); }
		$stmt = $this->db->prepareLimit($sql, 0, 1);
		if (count($pdo_binds) > 0) {
			foreach ($pdo_binds as $key => $parr) {
				$stmt->bindParam($key, $parr[0], $parr[1]);
			}
		}
		$stmt->execute();
		return (int)$stmt->fetchResult();
	}


	/****************************************/
	/* COUNT ARTICLES - MULTILINGUAL SEARCH */
	/****************************************/
	private function countAllArticlesMLSearch($options) {
		$wheres = array();
		$pdo_binds = array();
		if ($options['q'] != '') {//"if" statement not really needed
			$value = '%'.$options['q'].'%';
			if ($options['mlsearch'] > 0) {
				$wheres[] = '((c.title LIKE :xq) OR (c.subtitle LIKE :xq) OR (t.translation LIKE :xq))';
			} else {
				$wheres[] = '((c.title LIKE :xq) OR (c.subtitle LIKE :xq))';
			}
			$pdo_binds[':xq'] = array($value, PDO::PARAM_STR);
		}
		if (isset($options['catid']) && ($options['catid'] > -1)) {
			$wheres[] = 'c.catid = :xctg';
			$pdo_binds[':xctg'] = array($options['catid'], PDO::PARAM_INT);
		}
		if (isset($options['image']) && ($options['image'] > -1)) {
			if ($options['image'] == 0) {
				$value = '';
				$wheres[] = '((c.image = :ximg) OR (c.image IS NULL))';
				$pdo_binds[':ximg'] = array($value, PDO::PARAM_STR);
			} else {
				$value = '%images%';
				$wheres[] = 'c.image LIKE :ximg';
				$pdo_binds[':ximg'] = array($value, PDO::PARAM_STR);
			}
		}
		if (isset($options['published']) && ($options['published'] > -1)) {
			$wheres[] = 'c.published = :xpub';
			$pdo_binds[':xpub'] = array($options['published'], PDO::PARAM_INT);
		}
		if (isset($options['important']) && ($options['important'] > -1)) {
			$wheres[] = 'c.important = :ximp';
			$pdo_binds[':ximp'] = array($options['important'], PDO::PARAM_INT);
		}
		if (isset($options['author']) && ($options['author']  != '')) {
			$value = '%'.$options['author'].'%';
			$wheres[] = 'c.created_by_name LIKE :xauth';
			$pdo_binds[':xauth'] = array($value, PDO::PARAM_STR);
		}

		$sql = "SELECT c.id FROM ".$this->db->quoteId('#__content')." c";
		if ($options['mlsearch'] == 2) {
			$sql .= "\n LEFT JOIN ".$this->db->quoteId('#__translations')." t ON t.elid = c.id AND t.category = ".$this->db->quote('com_content')." AND ((t.element = ".$this->db->quote('title').") OR (t.element= ".$this->db->quote('subtitle')."))";
		} else if ($options['mlsearch'] == 1) {
			$sql .= "\n LEFT JOIN ".$this->db->quoteId('#__translations')." t ON t.elid = c.id AND t.category = ".$this->db->quote('com_content')." AND t.element = ".$this->db->quote('title');
		}
		if (count($wheres) > 0) { $sql .= ' WHERE '.implode(' AND ', $wheres); }
		$sql .= "\n GROUP BY c.id";
		$stmt = $this->db->prepare($sql);
		if (count($pdo_binds) > 0) {
			foreach ($pdo_binds as $key => $parr) {
				$stmt->bindParam($key, $parr[0], $parr[1]);
			}
		}
		$stmt->execute();
		$ids = $stmt->fetchCol();
		return ($ids) ? count($ids) : 0;
	}


	/********************/
	/* GET ALL ARTICLES */
	/********************/
	public function getAllArticles($options) {
		if ($options['q'] != '') {
			return $this->getAllArticlesMLSearch($options);
		}

		$wheres = array();
		$pdo_binds = array();
		if (isset($options['catid']) && ($options['catid'] > -1)) {
			$wheres[] = $this->db->quoteId('catid').' = :xctg';
			$pdo_binds[':xctg'] = array($options['catid'], PDO::PARAM_INT);
		}
		if (isset($options['image']) && ($options['image'] > -1)) {
			if ($options['image'] == 0) {
				$value = '';
				$wheres[] = '(('.$this->db->quoteId('image').' = :ximg) OR ('.$this->db->quoteId('image').' IS NULL))';
				$pdo_binds[':ximg'] = array($value, PDO::PARAM_STR);
			} else {
				$value = '%images%';
				$wheres[] = $this->db->quoteId('image').' LIKE :ximg';
				$pdo_binds[':ximg'] = array($value, PDO::PARAM_STR);
			}
		}
		if (isset($options['published']) && ($options['published'] > -1)) {
			$wheres[] = $this->db->quoteId('published').' = :xpub';
			$pdo_binds[':xpub'] = array($options['published'], PDO::PARAM_INT);
		}
		if (isset($options['important']) && ($options['important'] > -1)) {
			$wheres[] = $this->db->quoteId('important').' = :ximp';
			$pdo_binds[':ximp'] = array($options['important'], PDO::PARAM_INT);
		}
		if (isset($options['author']) && ($options['author']  != '')) {
			$value = '%'.$options['author'].'%';
			$wheres[] = $this->db->quoteId('created_by_name').' LIKE :xauth';
			$pdo_binds[':xauth'] = array($value, PDO::PARAM_STR);
		}

		$sql = "SELECT * FROM ".$this->db->quoteId('#__content');
		if (count($wheres) > 0) { $sql .= ' WHERE '.implode(' AND ', $wheres); }
		$sql .= ' ORDER BY '.$this->db->quoteId($options['sn']).' '.strtoupper($options['so']);
		$stmt = $this->db->prepareLimit($sql, $options['limitstart'], $options['limit']);
		if (count($pdo_binds) > 0) {
			foreach ($pdo_binds as $key => $parr) {
				$stmt->bindParam($key, $parr[0], $parr[1]);
			}
		}
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_OBJ);
		return $rows;
	}


	/******************************************************/
	/* GET ALL ARTICLES - MULTILINGUAL SEARCH FROM MODULE */
	/******************************************************/
	private function getAllArticlesMLSearch($options) {
		$wheres = array();
		$pdo_binds = array();
		if ($options['q'] != '') {//"if" statement not really needed
			$value = '%'.$options['q'].'%';
			if ($options['mlsearch'] > 0) {
				$wheres[] = '((c.title LIKE :xq) OR (c.subtitle LIKE :xq) OR (t.translation LIKE :xq))';
			} else {
				$wheres[] = '((c.title LIKE :xq) OR (c.subtitle LIKE :xq))';
			}
			$pdo_binds[':xq'] = array($value, PDO::PARAM_STR);
		}
		if (isset($options['catid']) && ($options['catid'] > -1)) {
			$wheres[] = 'c.catid = :xctg';
			$pdo_binds[':xctg'] = array($options['catid'], PDO::PARAM_INT);
		}
		if (isset($options['image']) && ($options['image'] > -1)) {
			if ($options['image'] == 0) {
				$value = '';
				$wheres[] = '((c.image = :ximg) OR (c.image IS NULL))';
				$pdo_binds[':ximg'] = array($value, PDO::PARAM_STR);
			} else {
				$value = '%images%';
				$wheres[] = 'c.image LIKE :ximg';
				$pdo_binds[':ximg'] = array($value, PDO::PARAM_STR);
			}
		}
		if (isset($options['published']) && ($options['published'] > -1)) {
			$wheres[] = 'c.published = :xpub';
			$pdo_binds[':xpub'] = array($options['published'], PDO::PARAM_INT);
		}
		if (isset($options['important']) && ($options['important'] > -1)) {
			$wheres[] = 'c.important = :ximp';
			$pdo_binds[':ximp'] = array($options['important'], PDO::PARAM_INT);
		}
		if (isset($options['author']) && ($options['author']  != '')) {
			$value = '%'.$options['author'].'%';
			$wheres[] = 'c.created_by_name LIKE :xauth';
			$pdo_binds[':xauth'] = array($value, PDO::PARAM_STR);
		}

		if ($options['mlsearch'] > 0) {
			$sql = "SELECT c.*, t.translation FROM ".$this->db->quoteId('#__content')." c";
			if ($options['mlsearch'] == 2) {
				$sql .= "\n LEFT JOIN ".$this->db->quoteId('#__translations')." t ON t.elid = c.id AND t.category = ".$this->db->quote('com_content')." AND ((t.element = ".$this->db->quote('title').") OR (t.element= ".$this->db->quote('subtitle')."))";
			} else {//mlsearch = 1
				$sql .= "\n LEFT JOIN ".$this->db->quoteId('#__translations')." t ON t.elid = c.id AND t.category = ".$this->db->quote('com_content')." AND t.element = ".$this->db->quote('title');
			}
		} else {
			$sql = "SELECT c.*, ".$this->db->quote('')." AS translation FROM ".$this->db->quoteId('#__content')." c";
		}

		if (count($wheres) > 0) { $sql .= ' WHERE '.implode(' AND ', $wheres); }
		$sql .= "\n GROUP BY c.id ORDER BY c.".$options['sn']." ".strtoupper($options['so']);
		$stmt = $this->db->prepareLimit($sql, $options['limitstart'], $options['limit']);
		if (count($pdo_binds) > 0) {
			foreach ($pdo_binds as $key => $parr) {
				$stmt->bindParam($key, $parr[0], $parr[1]);
			}
		}
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_OBJ);
		return $rows;
	}


	/*****************************/
	/* GET ARTICLES BY THEIR IDS */
	/*****************************/
	public function getArticlesById($ids) {
		if (is_array($ids)) {
			$sql = "SELECT ".$this->db->quoteId('id').", ".$this->db->quoteId('catid').", ".$this->db->quoteId('title').", ".$this->db->quoteId('seotitle').","
			."\n ".$this->db->quoteId('image').", ".$this->db->quoteId('published').", ".$this->db->quoteId('created_by').", ".$this->db->quoteId('alevel')
			."\n FROM ".$this->db->quoteId('#__content')." WHERE ".$this->db->quoteId('id')." IN (".implode(",", $ids).")";
			$stmt = $this->db->prepare($sql);
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		} else if (is_int($ids)) {
			$sql = "SELECT ".$this->db->quoteId('id').", ".$this->db->quoteId('catid').", ".$this->db->quoteId('title').", ".$this->db->quoteId('seotitle').","
			."\n ".$this->db->quoteId('image').", ".$this->db->quoteId('published').", ".$this->db->quoteId('created_by').", ".$this->db->quoteId('alevel')
			."\n FROM ".$this->db->quoteId('#__content')." WHERE ".$this->db->quoteId('id').' = :xval';
			$stmt = $this->db->prepareLimit($sql, 0, 1);
			$stmt->bindParam(':xval', $ids, PDO::PARAM_INT);
			$stmt->execute();
			return $stmt->fetch(PDO::FETCH_ASSOC);
		} else {
			return false;
		}
	}


	/***************************************************/
	/* SET THE PUBLISH STATUS FOR AN ARRAY OF ARTICLES */
	/***************************************************/
	public function setArticlesStatus($items) {
		$pubdate = '2014-01-01 00:00:00';
		$unpubdate = '2060-01-01 00:00:00';
		$sql = "UPDATE ".$this->db->quoteId('#__content')." SET ".$this->db->quoteId('published')." = :xpub, ".$this->db->quoteId('pubdate')." = :xpdt, ".$this->db->quoteId('unpubdate')." = :xupdt WHERE ".$this->db->quoteId('id')." = :xid";
		$stmt = $this->db->prepare($sql);
		foreach ($items as $id => $published) {
			$stmt->bindParam(':xpub', $published, PDO::PARAM_INT);
			$stmt->bindParam(':xpdt', $pubdate, PDO::PARAM_STR);
			$stmt->bindParam(':xupdt', $unpubdate, PDO::PARAM_STR);
			$stmt->bindParam(':xid', $id, PDO::PARAM_INT);
			$stmt->execute();
		}
	}


	/*******************************/
	/* DELETE AN ARRAY OF ARTICLES */
	/*******************************/
	public function deleteArticles($ids) {
		$sql = "DELETE FROM ".$this->db->quoteId('#__content')." WHERE ".$this->db->quoteId('id')." IN (".implode(",", $ids).")";
		$stmt = $this->db->prepare($sql);
		$stmt->execute();

		$trcategory = 'com_content';
		$trelements = array('title', 'subtitle', 'introtext', 'maintext', 'caption', 'metakeys');
		$sql = "DELETE FROM ".$this->db->quoteId('#__translations')." WHERE ".$this->db->quoteId('category')." = :xcat"
		."\n AND ".$this->db->quoteId('element')." = :xelement AND ".$this->db->quoteId('elid')." IN (".implode(",", $ids).")";
		$stmt = $this->db->prepare($sql);
		foreach ($trelements as $trelement) {
			$stmt->bindParam(':xcat', $trcategory, PDO::PARAM_STR);
			$stmt->bindParam(':xelement', $trelement, PDO::PARAM_STR);
			$stmt->execute();
		}

		$sql = "DELETE FROM ".$this->db->quoteId('#__comments')." WHERE ".$this->db->quoteId('element')." = :xcat"
		."\n AND ".$this->db->quoteId('elid')." IN (".implode(",", $ids).")";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xcat', $trcategory, PDO::PARAM_STR);
		$stmt->execute();
	}


	/**********************************/
	/* GET ARTICLES FOR ORDERING LIST */
	/**********************************/
	public function getOrderingArticles($catid, $ordering_start=-1, $limitstart=0, $limit=50) {
		$binds = array();
		if ($catid > -1) {
			$binds[] = array('catid', '=', $catid, PDO::PARAM_INT);
		}

		if ($ordering_start > -1) {
			$binds[] = array('ordering', '>', $ordering_start, PDO::PARAM_INT);
		}

		$sql = "SELECT ".$this->db->quoteId('id').", ".$this->db->quoteId('title').", ".$this->db->quoteId('ordering')." FROM ".$this->db->quoteId('#__content');
		if ($binds) {
			foreach ($binds as $k => $bind) {
				if ($k == 0) {
					$sql .= ' WHERE '.$this->db->quoteId($bind[0]).' '.$bind[1].' :xval'.$k."\n";
				} else {
					$sql .= ' AND '.$this->db->quoteId($bind[0]).' '.$bind[1].' :xval'.$k."\n";
				}
			}
		}
		$sql .= ' ORDER BY '.$this->db->quoteId('ordering').' ASC';
		$stmt = $this->db->prepareLimit($sql, $limitstart, $limit);
		if ($binds) {
			foreach ($binds as $k => $bind) {
				$stmt->bindParam(':xval'.$k, $bind[2], $bind[3]);
			}
		}
		
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $rows;
	}


	/****************************************/
	/* GET TEMPLATE POSITIONS FROM DATABASE */
	/****************************************/
	public function getTplPositions() {
		$sql = "SELECT ".$this->db->quoteId('id').", ".$this->db->quoteId('position')." FROM ".$this->db->quoteId('#__template_positions');
		$sql .= ' ORDER BY '.$this->db->quoteId('position')." ASC";
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_OBJ);
		
		if (!$rows) { return $rows; }

		$section = 'frontend';
		$sql = "SELECT COUNT(id) FROM ".$this->db->quoteId('#__modules')
		."\n WHERE ".$this->db->quoteId('section')." = :xsec AND ".$this->db->quoteId('position')." = :xpos";
		$stmt = $this->db->prepare($sql);
		for ($i=0; $i < count($rows); $i++) {
			$position = $rows[$i]->position;
			$stmt->bindParam(':xsec', $section, PDO::PARAM_STR);
			$stmt->bindParam(':xpos', $position, PDO::PARAM_STR);
			$stmt->execute();
			$rows[$i]->modules = (int)$stmt->fetchResult();
		}

		return $rows;
	}


	/**********************************************/
	/* GET FRONTEND SECTION MODULES FROM DATABASE */
	/**********************************************/
	public function getFrontModules() {
		$section = 'frontend';
		$sql = "SELECT ".$this->db->quoteId('id').", ".$this->db->quoteId('title').", ".$this->db->quoteId('module')
		."\n FROM ".$this->db->quoteId('#__modules')
		."\n WHERE ".$this->db->quoteId('published')." = 1 AND ".$this->db->quoteId('section')." = :xsec ORDER BY ".$this->db->quoteId('title')." ASC";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xsec', $section, PDO::PARAM_STR);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_OBJ);
		return $rows;
	}


	/**************************************/
	/* GET FRONTPAGE LAYOUT FROM DATABASE */
	/**************************************/
	public function getFrontpage() {
		$sql = "SELECT * FROM ".$this->db->quoteId('#__frontpage');
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $rows;
	}


	/*************************/
	/* SAVE FRONTPAGE LAYOUT */
	/*************************/
	public function saveFrontpage($rows) {
		$sqlIn = "INSERT INTO ".$this->db->quoteId('#__frontpage')." (".$this->db->quoteId('id').", ".$this->db->quoteId('pname').", ".$this->db->quoteId('pval').")"
		."\n VALUES (NULL, :xname, :xval)";
		$sqlUp = "UPDATE ".$this->db->quoteId('#__frontpage')." SET ".$this->db->quoteId('pval')." = :xval WHERE ".$this->db->quoteId('id')." = :xid";
		$stmtIn = $this->db->prepare($sqlIn);
		$stmtUp = $this->db->prepare($sqlUp);
		foreach ($rows as $row) {
			$datatype = ($row->is_int === true) ? PDO::PARAM_INT : PDO::PARAM_STR;
			if ($row->id > 0) {
				$stmtUp->bindParam(':xval', $row->pval, $datatype);
				$stmtUp->bindParam(':xid', $row->id, PDO::PARAM_INT);
				$stmtUp->execute();
			} else {
				$stmtIn->bindParam(':xname', $row->pname, PDO::PARAM_STR);
				$stmtIn->bindParam(':xval', $row->pval, $datatype);
				$stmtIn->execute();
			}
		}
	}


	/***************************************/
	/* GET ALL TRANSLATIONS FOR AN ARTICLE */
	/***************************************/
	public function allArticleTrans($id) {
		$sql = "SELECT ".$this->db->quoteId('element').", ".$this->db->quoteId('language').", ".$this->db->quoteId('translation')
		."\n FROM ".$this->db->quoteId('#__translations')
		."\n WHERE ".$this->db->quoteId('category')."=".$this->db->quote('com_content')
		."\n AND ((".$this->db->quoteId('element')." = ".$this->db->quote('title').") OR (".$this->db->quoteId('element')." = ".$this->db->quote('subtitle').")"
		."\n OR (".$this->db->quoteId('element')." = ".$this->db->quote('introtext').") OR (".$this->db->quoteId('element')." = ".$this->db->quote('maintext').")"
		."\n OR (".$this->db->quoteId('element')." = ".$this->db->quote('caption').") OR (".$this->db->quoteId('element')." = ".$this->db->quote('metakeys')."))"
		."\n AND ".$this->db->quoteId('elid')." = :xelid";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xelid', $id, PDO::PARAM_INT);
		$stmt->execute();
		$trans = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $trans;
	}


	/*************************/
	/* COUNT SCHEDULED ITEMS */
	/*************************/
	public function countScheduledItems() {
		$pubdate = '2014-01-01 00:00:00';
		$unpubdate = '2060-01-01 00:00:00';

		$sql = "SELECT COUNT(id) FROM ".$this->db->quoteId('#__content')
		."\n WHERE (".$this->db->quoteId('published')." = 0 AND ".$this->db->quoteId('pubdate')." != :xpub) OR"
		."\n (".$this->db->quoteId('published')." = 1 AND ".$this->db->quoteId('unpubdate')." != :xupub)";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xpub', $pubdate, PDO::PARAM_STR);
		$stmt->bindParam(':xupub', $unpubdate, PDO::PARAM_STR);
		$stmt->execute();
		return (int)$stmt->fetchResult();
	}


	/********************************/
	/* COUNT ARCHIVE TOTAL ARTICLES */
	/********************************/
	public function countArchiveArticles($year, $month, $day) {
		$elxis = eFactory::getElxis();

		$lowlev = $elxis->acl()->getLowLevel();
		$exactlev = $elxis->acl()->getExactLevel();

		$created = '';
		if ($year > 0) {
			$created = $year;
			if ($month > 0) {
				$created .= '-'.sprintf("%02d", $month);
				if ($day > 0) {
					$created .= '-'.sprintf("%02d", $day);
				}
			}
		}

		$cval = '';
		$sql = "SELECT COUNT(".$this->db->quoteId('id').") FROM ".$this->db->quoteId('#__content')
		."\n WHERE ".$this->db->quoteId('published')." = 1";
		if ($created != '') {
			$sql .= " AND ".$this->db->quoteId('created')." LIKE :xval";
			$cval = $created.'%';
		}
		$sql .= "\n AND ((".$this->db->quoteId('alevel')." <= :lowlevel) OR (".$this->db->quoteId('alevel')." = :exactlevel))";
		$stmt = $this->db->prepareLimit($sql, 0, 1);
		if ($cval != '') {
			$stmt->bindParam(':xval', $cval, PDO::PARAM_STR);
		}
		$stmt->bindParam(':lowlevel', $lowlev, PDO::PARAM_INT);
		$stmt->bindParam(':exactlevel', $exactlev, PDO::PARAM_INT);
		$stmt->execute();
		return (int)$stmt->fetchResult();
	}


	/**************************/
	/* FETCH ARCHIVE ARTICLES */
	/**************************/
	public function fetchArchiveArticles($year, $month, $day, $limitstart, $limit, $order, $translate=false, $lng='') {
		$elxis = eFactory::getElxis();

		$lowlev = $elxis->acl()->getLowLevel();
		$exactlev = $elxis->acl()->getExactLevel();

		$created = '';
		if ($year > 0) {
			$created = $year;
			if ($month > 0) {
				$created .= '-'.sprintf("%02d", $month);
				if ($day > 0) {
					$created .= '-'.sprintf("%02d", $day);
				}
			}
		}

		switch ($order) { //only ca and cd are usable, rest left for possible future changes
			case 'ma': $orderby = 'a.modified'; $dir = 'ASC'; break;
			case 'md': $orderby = 'a.modified'; $dir = 'DESC'; break;
			case 'oa': $orderby = 'a.ordering'; $dir = 'ASC'; break;
			case 'od': $orderby = 'a.ordering'; $dir = 'DESC'; break;
			case 'ta': $orderby = 'a.title'; $dir = 'ASC'; break;
			case 'td': $orderby = 'a.title'; $dir = 'DESC'; break;
			case 'ca': $orderby = 'a.created'; $dir = 'ASC'; break;
			case 'cd': default: $orderby = 'a.created'; $dir = 'DESC'; break;
		}

		$cval = '';
		$sql  = "SELECT a.id, a.title, a.seotitle, a.subtitle, a.introtext, a.metakeys, a.image, a.caption, a.created, a.created_by, a.created_by_name,"
		."\n a.modified, a.modified_by, a.modified_by_name, a.hits, c.catid, c.seolink, c.title AS category"
		."\n FROM ".$this->db->quoteId('#__content')." a"
		."\n LEFT JOIN ".$this->db->quoteId('#__categories')." c ON c.catid=a.catid"
		."\n WHERE a.published = 1";
		if ($created != '') {
			$sql .= " AND a.created LIKE :xval";
			$cval = $created.'%';
		}
		$sql .="\n AND ((a.alevel <= :lowlevel) OR (a.alevel = :exactlevel))"
		."\n ORDER BY ".$orderby." ".$dir;
		$stmt = $this->db->prepareLimit($sql, $limitstart, $limit);
		if ($cval != '') {
			$stmt->bindParam(':xval', $cval, PDO::PARAM_STR);
		}
		$stmt->bindParam(':lowlevel', $lowlev, PDO::PARAM_INT);
		$stmt->bindParam(':exactlevel', $exactlev, PDO::PARAM_INT);
		$stmt->execute();
		$rows = $stmt->fetchAllAssoc('id', PDO::FETCH_OBJ);
		if (!$rows) { return false; }

		if ($translate && ($lng != '')) {
			$elids = array();
			$cids = array();
			foreach ($rows as $row) {
				$elids[] = $row->id;
				if ($row->catid > 0) { $cids[] = $row->catid; }
			}

			if ($cids) {
				$cids = array_unique($cids);
				$translations = $this->categoriesTranslate($cids, $lng);
				if ($translations) {
					foreach ($rows as $idx => $row) {
						$cid = $row->catid;
						if (isset($translations[$cid])) {
							$rows[$idx]->category = $translations[$cid];
						}
					}
				}
			}

			$translations = $this->articlesTranslate($elids, $lng);
			if ($translations) {
				foreach ($translations as $trans) {
					$id = (int)$trans['elid'];
					$element = $trans['element'];
					switch ($element) {
						case 'title': $rows[$id]->title = $trans['translation']; break;
						case 'subtitle': $rows[$id]->subtitle = $trans['translation']; break;
						case 'introtext': $rows[$id]->introtext = $trans['translation']; break;
						case 'caption': $rows[$id]->caption = $trans['translation']; break;
						default: break;
					}
				}
			}

		}

		return $rows;
	}


	/********************************/
	/* GET UNIQUE RELATIVE KEYWORDS */
	/********************************/
	public function getRelKeys() {
		//$sql = "SELECT DISTINCT ".$this->db->quoteId('relkey')." FROM ".$this->db->quoteId('#__content')//MYSQL 5.7+ PROBLEM WITH DISTINCT AND ORDER BY
		//."\n WHERE ".$this->db->quoteId('relkey')." <> '' AND ".$this->db->quoteId('relkey')." IS NOT NULL"
		//."\n ORDER BY ".$this->db->quoteId('created')." DESC";
		//$stmt = $this->db->prepareLimit($sql, 0, 100);
		//$stmt->execute();
		//$relkeys = $stmt->fetchCol();
		//return $relkeys;

		$sql = "SELECT ".$this->db->quoteId('relkey')." FROM ".$this->db->quoteId('#__content')
		."\n WHERE ".$this->db->quoteId('relkey')." <> '' AND ".$this->db->quoteId('relkey')." IS NOT NULL"
		."\n ORDER BY ".$this->db->quoteId('created')." DESC";
		$stmt = $this->db->prepareLimit($sql, 0, 100);
		$stmt->execute();
		$results = $stmt->fetchCol();
		if (!$results) { return false; }

		$relkeys = array();
		foreach ($results as $result) {
			if (!in_array($result, $relkeys)) { $relkeys[] = $result; }
		}
		return $relkeys;
	}


	/**************************/
	/* FETCH RELATED ARTICLES */
	/**************************/
	public function fetchRelatedArticles($id, $relkey, $art_related, $translate=false, $lng='') {
		if (trim($relkey) == '') { return false; }

		$elxis = eFactory::getElxis();

		$lowlev = $elxis->acl()->getLowLevel();
		$exactlev = $elxis->acl()->getExactLevel();

		$sql  = "SELECT a.id, a.title, a.seotitle, c.catid, c.seolink"
		."\n FROM ".$this->db->quoteId('#__content')." a"
		."\n LEFT JOIN ".$this->db->quoteId('#__categories')." c ON c.catid=a.catid"
		."\n WHERE a.id <> :xid AND a.published = 1 AND a.relkey = :xkey"
		."\n AND ((a.alevel <= :lowlevel) OR (a.alevel = :exactlevel))"
		."\n ORDER BY a.created DESC";
		$stmt = $this->db->prepareLimit($sql, 0, $art_related);
		$stmt->bindParam(':xid', $id, PDO::PARAM_INT);
		$stmt->bindParam(':xkey', $relkey, PDO::PARAM_STR);
		$stmt->bindParam(':lowlevel', $lowlev, PDO::PARAM_INT);
		$stmt->bindParam(':exactlevel', $exactlev, PDO::PARAM_INT);
		$stmt->execute();
		$rows = $stmt->fetchAllAssoc('id', PDO::FETCH_OBJ);
		if (!$rows) { return false; }

		if ($translate && ($lng != '')) {
			$elids = array();
			foreach ($rows as $row) { $elids[] = $row->id; }
			$translations = $this->articlesTitlesTranslate($elids, $lng);
			if ($translations) {
				foreach ($translations as $artid => $translation) {
					$rows[$artid]->title = $translation;
				}
			}
		}

		return $rows;
	}


	/*****************************************************/
	/* FETCH ARTICLES IMAGES (FOR ARTICLE IMAGE SHARING) */
	/*****************************************************/
	public function fetchArticlesImages($limit=100) {
		$limit = (int)$limit;
		if ($limit < 1) { $limit = 100; }
		//$sql = "SELECT DISTINCT ".$this->db->quoteId('image')." FROM ".$this->db->quoteId('#__content')//MYSQL 5.7+ PROBLEM WITH DISTINCT AND ORDER BY
		//."\n WHERE ".$this->db->quoteId('image')." <> '' AND ".$this->db->quoteId('image')." IS NOT NULL"
		//."\n ORDER BY ".$this->db->quoteId('created')." DESC";
		//$stmt = $this->db->prepareLimit($sql, 0, $limit);
		//$stmt->execute();
		//return $stmt->fetchCol();

		$sql = "SELECT ".$this->db->quoteId('image')." FROM ".$this->db->quoteId('#__content')
		."\n WHERE ".$this->db->quoteId('image')." <> '' AND ".$this->db->quoteId('image')." IS NOT NULL"
		."\n ORDER BY ".$this->db->quoteId('created')." DESC";
		$stmt = $this->db->prepareLimit($sql, 0, $limit);
		$stmt->execute();
		$results = $stmt->fetchCol();
		if (!$results) { return false; }

		$images = array();
		foreach ($results as $result) {
			if (!in_array($result, $images)) { $images[] = $result; }
		}
		return $images;
	}


	/*********************************/
	/* COUNT ARTICLES USING AN IMAGE */
	/*********************************/
	public function countImageArticles($image, $except_id=0) {
		$except_id = (int)$except_id;
		$sql = "SELECT COUNT(".$this->db->quoteId('id').") FROM ".$this->db->quoteId('#__content')
		."\n WHERE ".$this->db->quoteId('image')." = :ximg";
		if ($except_id > 0) {
			$sql .= " AND ".$this->db->quoteId('id')." <> :xid";
		}
		$stmt = $this->db->prepareLimit($sql, 0, 1);
		$stmt->bindParam(':ximg', $image, PDO::PARAM_STR);
		if ($except_id > 0) {
			$stmt->bindParam(':xid', $except_id, PDO::PARAM_INT);
		}
		$stmt->execute();
		return (int)$stmt->fetchResult();
	}


	/************************************************************/
	/* FIND INCREMENTAL NUMBER FOR SEO TITLE (FOR ARTICLE COPY) */
	/************************************************************/
	public function findNextSeoTitle($seotitle) {
		$inc = 0;
		$sql = "SELECT COUNT(".$this->db->quoteId('id').") FROM ".$this->db->quoteId('#__content')
		."\n WHERE ".$this->db->quoteId('seotitle')." = :xseo";
		$stmt = $this->db->prepareLimit($sql, 0, 1);
		for ($i=2; $i < 100; $i ++) {
			$newseo = $seotitle.$i;
			$stmt->bindParam(':xseo', $newseo, PDO::PARAM_STR);
			$stmt->execute();
			$n = (int)$stmt->fetchResult();
			if ($n == 0) {
				$inc = $i;
				break;
			}
		}

		if ($inc == 0) { $inc = time(); }
		return $inc;
	}


	/**************************************************************/
	/* FETCH ALL MENU COLLECTIONS AND MENU ITEMS (AS ARRAYS TREE) */
	/**************************************************************/
	public function fetchAllMenus() {
		$section = 'frontend';
		$sql = "SELECT ".$this->db->quoteId('menu_id').", ".$this->db->quoteId('title').", ".$this->db->quoteId('collection').", ".$this->db->quoteId('parent_id')
		."\n FROM ".$this->db->quoteId('#__menu')
		."\n WHERE ".$this->db->quoteId('section')." = :xsection"
		."\n ORDER BY ".$this->db->quoteId('parent_id')." ASC, ".$this->db->quoteId('ordering')." ASC";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xsection', $section, PDO::PARAM_STR);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (!$rows) { return false; }

		$needs_tree = array();
		$menus = array();
		foreach ($rows as $row) {
			$id = $row['menu_id'];
			$col = $row['collection'];
			if (!isset($menus[$col])) {
				$menus[$col] = array();
			}

			$menus[$col][$id] = array('menu_id' => $row['menu_id'], 'parent_id' => $row['parent_id'], 'title' => $row['title'], 'treename' => $row['title'], 'children' => array());
			if ($row['parent_id'] > 0) { $needs_tree[$col] = 1; }
		}

		if ($needs_tree) {
			$tree = eFactory::getElxis()->obj('tree');
			foreach ($menus as $col => $menu) {
				if (!isset($needs_tree[$col])) { continue; }
				$tree->setOptions(array('itemid' => 'menu_id', 'parentid' => 'parent_id', 'itemname' => 'title', 'html' => false));
				$menus[$col] = $tree->makeTree($menu);
			}
			unset($tree);
		}

		return $menus;
	}


	/********************************/
	/* GET A MENU ITEM ACCESS LEVEL */
	/********************************/
	public function getMenuItemLevel($menu_id) {
		$sql = "SELECT ".$this->db->quoteId('alevel')." FROM ".$this->db->quoteId('#__menu')
		."\n WHERE ".$this->db->quoteId('menu_id')." = :xmenuid";
		$stmt = $this->db->prepareLimit($sql, 0, 1);
		$stmt->bindParam(':xmenuid', $menu_id, PDO::PARAM_INT);
		$stmt->execute();
		return (int)$stmt->fetchResult();
	}


	/***************************************************************************/
	/* ADD MENU ITEM TRANSLATIONS FROM ARTICLE'S/CATEGORY'S TITLE TRANSLATIONS */
	/***************************************************************************/
	public function addMenuTranslations($item_id, $menu_id, $is_category=false) {
		$element = ($is_category) ? 'category_title' : 'title';

		$sql = "SELECT ".$this->db->quoteId('language').", ".$this->db->quoteId('translation')
		."\n FROM ".$this->db->quoteId('#__translations')
		."\n WHERE ".$this->db->quoteId('category')."=".$this->db->quote('com_content')
		."\n AND ".$this->db->quoteId('element')." = :xelem AND ".$this->db->quoteId('elid')." = :xid";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xelem', $element, PDO::PARAM_STR);
		$stmt->bindParam(':xid', $item_id, PDO::PARAM_INT);
		$stmt->execute();
		$trans = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (!$trans) { return; }

		elxisLoader::loadFile('includes/libraries/elxis/database/tables/translations.db.php');
		foreach ($trans as $tran) {
			$item = new translationsDbTable();
			$item->category = 'com_emenu';
			$item->element = 'title';
			$item->language = $tran['language'];
			$item->elid = $menu_id;
			$item->translation = $tran['translation'];
			$item->insert();
		}
	}

}

?>