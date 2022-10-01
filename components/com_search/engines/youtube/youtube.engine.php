<?php 
/**
* @version		$Id: youtube.engine.php 2203 2019-04-10 18:34:15Z IOS $
* @package		Elxis
* @subpackage	Component Search
* @copyright	Copyright (c) 2006-2019 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class youtubeEngine implements searchEngine {

	private $options = array('q' => '', 'time' => 0, 'ordering' => 'r');		
	private $dosearch = false;
	private $total = 0;
	private $limit = 10;
	private $limitstart = 0;
	private $page = 1;
	private $maxpage = 1;
	private $results = array();
	private $columns = 1;
	private $safe = 'moderate';
	private $channelid = '';
	private $apikey = '';
	private $apierror = '';
	private $year = 0; //for internal use only
	private $month = 0; //for internal use only


	/********************/
	/* MAGIC CONTRUCTOR */
	/********************/
	public function __construct($params) {
		$this->limit = (int)$params->get('limit', 10);
		if ($this->limit < 1) { $this->limit = 10; }
		$this->columns = (int)$params->get('columns', 1);
		if ($this->columns < 1) { $this->columns = 1; }
		if ($this->columns > 2) { $this->columns = 2; }

		$this->safe = trim($params->get('safe', 'moderate'));
		if (($this->safe == '') || !in_array($this->safe, array('none', 'moderate', 'strict'))) {
			$this->safe = 'moderate';
		}
		$this->channelid = trim($params->get('channelid', ''));
		$this->options['ordering'] = $params->get('ordering', 'r');
		if (($this->options['ordering'] == '') || !in_array($this->options['ordering'], array('r', 'dd', 'hd', 'vd', 'ta'))) {
			$this->options['ordering'] = 'r';
		}

		$this->apikey = trim($params->get('key', ''));

		$this->setOptions();
	}


	/***********************************/
	/* SET SEARCH OPTIONS FROM THE URL */
	/***********************************/
	private function setOptions() {
		$pat = "#([\']|[\;]|[\.]|[\"]|[\$]|[\/]|[\|]|[\=]|[\#]|[\<]|[\>]|[\*]|[\~]|[\`]|[\^]|[\|]|[\\\])#u";
		if (isset($_GET['q'])) {
			$q = urldecode(filter_input(INPUT_GET, 'q', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
			$q = eUTF::trim(preg_replace($pat, '', $q));
			if (eUTF::strlen($q) > 3) { $this->options['q'] = $q; $this->dosearch = true; }			
		}

		if (isset($_GET['time'])) {
			$time = (int)$_GET['time'];
			if ($time > 0) {
				if ($time < 365) {
					$this->options['time'] = $time;
					$this->dosearch = true;
				} else {
					$t = (string)$time;
					if (strlen($t) == 4) {
						if (($time > 1970) && ($time <= date('Y'))) { //valid year
							$this->options['time'] = $time;
							$this->year = $time;
							$this->month = 0;
							$this->dosearch = true;
						}
					} else if (strlen($t) == 6) {
						$y = intval(substr($t, 0, 4));
						$m = intval(substr($t, -2));
						$m2 = sprintf("%02d", $m);
						if (($y > 1970) && ($m > 0) && ($m < 13) && ($y.$m2 <= date('Ym'))) { //valid year & month
							$this->year = $y;
							$this->month = $m2;
							$this->options['time'] = $y.$this->month;
							$this->dosearch = true;
						}
						unset($y, $m, $m2);
					}
				}
			}
		}

		if (isset($_GET['ordering'])) {
			$ordering = trim(filter_input(INPUT_GET, 'ordering', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
			if (in_array($ordering, array('r', 'dd', 'hd', 'vd', 'ta'))) {
				$this->options['ordering'] = $ordering;
			}
		}
	}


	/**************************/
	/* GET ENGINE'S META INFO */
	/**************************/
	public function engineInfo() {
		$eLang = eFactory::getLang();
		$info = array(
			'title' => 'YouTube',
			'description' => $eLang->get('SEARCH_YOUTUBE'),
			'metakeys' => array(
				$eLang->get('SEARCH'), 
				'videos', 
				'youtube', 
				'youtube videos',
				$eLang->get('KEYWORD'),
				'OpenSearch',
				'elxis youtube search'
			)
		);
		if ($this->channelid != '') { $info['metakeys'][] = 'Videos by '.$this->channelid; }
		return $info;
	}


	/********************/
	/* MAKE SEARCH FORM */
	/********************/
	public function searchForm() {
		$eURI = eFactory::getURI();
		$eLang = eFactory::getLang();

		$isssl = $eURI->detectSSL();
		$action = $eURI->makeURL('search:youtube.html', '', $isssl);

		elxisLoader::loadFile('includes/libraries/elxis/form5.class.php');

		$form = new elxis5Form(array('idprefix' => 'stub', 'labelclass' => 'elx5_label', 'sideclass' => 'elx5_labelside'));
		$form->openForm(array('name' => 'fmsearchytube', 'method' => 'get', 'action' => $action, 'id' => 'fmsearchytube'));

		$form->openFieldset($eLang->get('SEARCH_OPTIONS'));
		$form->addNote($eLang->get('LEAST_ONE_CRITERIA'), 'elx5_formtext');
		$form->addText('q', $this->options['q'], $eLang->get('KEYWORD'), array('required' => 'required', 'maxlength' => 80, 'placeholder' => $eLang->get('KEYWORD')));
		if ($this->columns == 2) {
			$form->addHTML('<div class="elx5_2colwrap">');
			$form->addHTML('<div class="elx5_2colbox">');
		}

		$options = array();
		$options[] = $form->makeOption(0, $eLang->get('ANY_TIME'));
		$options[] = $form->makeOption(1, $eLang->get('LAST_24_HOURS'));
		$options[] = $form->makeOption(2, $eLang->get('LAST_2_DAYS'));
		$options[] = $form->makeOption(10, $eLang->get('LAST_10_DAYS'));
		$options[] = $form->makeOption(30, $eLang->get('LAST_30_DAYS'));
		$options[] = $form->makeOption(90, $eLang->get('LAST_3_MONTHS'));
		$options[] = $form->makeOption(date('Ym'), $eLang->get('THIS_MONTH'));
		$years = array();
		$end = (($this->year > 0) && ($this->year < 2010)) ? $this->year : 2010;
		for ($i = date('Y'); $i >= $end; $i--) { $years[] = $i; }
		foreach ($years as $year) {
			$txt = ($year == date('Y')) ? $eLang->get('THIS_YEAR') : $year;
			$options[] = $form->makeOption($year, $txt);
			if (($this->year == $year) && ($this->month > 0)) {
				$monthname = eFactory::getDate()->monthName($this->month);
				$options[] = $form->makeOption($year.$this->month, $monthname.' '.$year);
			}
		}
		$form->addSelect('time', $eLang->get('DATE'), $this->options['time'], $options);
		unset($years, $end, $options);
		if ($this->columns == 2) {
			$form->addHTML('</div><div class="elx5_2colbox">');
		}
		$options = array();
		$options[] = $form->makeOption('r', $eLang->get('RELEVANCY'));
		$options[] = $form->makeOption('dd', $eLang->get('NEWER_FIRST'));
		$options[] = $form->makeOption('hd', $eLang->get('MOST_POPULAR_FIRST'));
		$options[] = $form->makeOption('vd', $eLang->get('RATING'));
		$options[] = $form->makeOption('ta', $eLang->get('TITLE'));
		$form->addSelect('ordering', $eLang->get('ORDERING'), $this->options['ordering'], $options);
		unset($options);
		if ($this->columns == 2) {
			$form->addHTML('</div></div>');
		}

		$sidepad = ($this->columns == 2) ? 0 : 1;
		$form->addHTML('<div class="elx5_vspace">');
		$form->addButton('sbm', $eLang->get('SEARCH'), 'submit', array('class' => 'elx5_btn elx5_sucbtn', 'sidepad' => $sidepad));
		$form->addHTML('</div>');

		$form->closeFieldset();
		unset($form);
	}


	/**************************/
	/* PROCESS SEARCH REQUEST */
	/**************************/
	public function search($page=1) {
		$page = (int)$page;
		if ($page < 1) { $page = 1; }
		$this->total = 0;
		$this->limitstart = 0;
		$this->page = $page;
		$this->maxpage = 1;
		$this->results = array();
		if ($this->dosearch == false) { return false; }
		$results = $this->request($page);
		if ($results) {
			$this->results = $results;
			return $this->total;
		}
		return 0;
	}


	/**************************************/
	/* QUERY YOUTUBE AND GET BACK RESULTS */
	/**************************************/
	private function request($page) {
		$page = (int)$page;
		if ($page < 1) { $page = 1; }
		$start = (($page - 1) * $this->limit) + 1;

		$params = array();
		$params['part'] = 'snippet';
		$params['maxResults'] = 50;//always get 50 (maximum for 1 request) and then split them into pages ($this->limit);
		$params['safeSearch'] = $this->safe;
		$params['type'] = 'video';
		switch ($this->options['ordering']) {
			case 'dd': $params['order'] = 'date'; break;
			case 'hd': $params['order'] = 'viewCount'; break;
			case 'vd': $params['order'] = 'rating'; break;
			case 'ta': $params['order'] = 'title'; break;
			case 'r': default: $params['order'] = 'relevance'; break;
		}

		if ($this->channelid != '') { $params['channelId'] = $this->channelid; }

		if ($this->options['time'] > 0) {
			if ($this->options['time'] < 365) {
				$ts = time() - ($this->options['time'] * 24 * 3600);
				$datetime = gmdate('c', $ts); //RFC 3339
				$params['publishedAfter'] = $datetime;
			} else if ($this->year > 0) {
				if ($this->month > 0) {
					$ts = mktime(0, 0, 0, $this->month, 1, $this->year);
					$datetime = gmdate('c', $ts); //RFC 3339
					$params['publishedAfter'] = $datetime;
					$y = $this->year;
					$m = $this->month + 1;
					if ($m == 13) { $m = 1; $y = $y + 1; }
					$ts = mktime(0, 0, 0, $m, 1, $y);
					$datetime = gmdate('c', $ts); //RFC 3339
					$params['publishedBefore'] = $datetime;
				} else {
					$ts = mktime(0, 0, 0, 1, 1, $this->year);
					$datetime = gmdate('c', $ts); //RFC 3339
					$params['publishedAfter'] = $datetime;
					if ($this->year == gmdate('Y')) {
						$ts = time();
					} else {
						$y = $this->year + 1;
						$ts = mktime(0, 0, 0, 1, 1, $y);
					}
					$datetime = gmdate('c', $ts); //RFC 3339
					$params['publishedBefore'] = $datetime;
				}
			}
		}
		if ($this->options['q'] != '') { $params['q'] = $this->options['q']; }
		if ($this->apikey != '') { $params['key'] = $this->apikey; }

		$url = 'https://www.googleapis.com/youtube/v3/search';
		if (function_exists('curl_init')) {
			$result = $this->curlget($url, $params);
		} else {
			$result = $this->httpget($url, $params);
		}

		if (!$result) { return false; }
		return $this->parsejsondata($result);
	}


	/*******************************/
	/* HTTP GET REQUEST USING CURL */
	/*******************************/
	private function curlget($url, $params=null) {
		$ch = curl_init();
		if ($params) {
			curl_setopt($ch, CURLOPT_URL, $url.'?'.http_build_query($params)); //url encodes the data
		} else {
			curl_setopt($ch, CURLOPT_URL, $url);
		}

		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_FAILONERROR, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
		$result = curl_exec($ch);
		if (0 == curl_errno($ch)) {
			curl_close($ch);
			return $result;
		} else {
			$this->apierror = curl_error($ch);
			curl_close($ch);
			return false;
		}
	}


	/************************************/
	/* HTTP GET REQUEST USING FSOCKOPEN */
	/************************************/
	private function httpget($url, $params=null) {
		$parseurl = parse_url($url);
		$getstr = '';
		if ($params) {
			$parr = array();
			foreach($params as $key => $val) { $parr[] = $key.'='.urlencode($val); }
			$getstr = implode('&', $parr);
			unset($parr);
		}

		$req = 'GET '.$parseurl['path'].'?'.$getstr." HTTP/1.1\r\n";
		$req .= 'Host: '.$parseurl['host']."\r\n";
		$req .= "Referer: ".eFactory::getElxis()->getConfig('URL')."\r\n";
		$req .= 'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:40.0) Gecko/20100101 Firefox/40.1'."\r\n";
		$req .= 'Accept: application/json,text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,*/*;q=0.6'."\r\n";
		$req .= 'Accept-Language: en-us,en;q=0.5'."\r\n";
		$req .= 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7'."\r\n";
		$req .= "Connection: Close\r\n\r\n"; 

		if (!isset($parseurl['port'])) {
			$parseurl['port'] = ($parseurl['scheme'] == 'https') ? 443 : 80;
		}

		$fp = fsockopen($parseurl['host'], $parseurl['port'], $errno, $errstr, 20);
		if (!$fp) { return false; }
		stream_set_timeout($fp, 15);
		fputs($fp, $req);
		$raw = '';
		while(!feof($fp)) {
			$raw .= fgets($fp);
			$info = stream_get_meta_data($fp);
			if ($info['timed_out']) {
				fclose($fp);
				return false;
			}
		}
		fclose($fp);
		$result = '';
		$chunked = false;
		if ($raw != '') {
			$expl = preg_split("/(\r\n){2,2}/", $raw, 2);
			$result = $expl[1];
			if (preg_match('/Transfer\\-Encoding:\\s+chunked/i',$expl[0])) { $chunked = true; }
			unset($expl);
		}
		unset($raw);

		if ($chunked) {
			$result = $this->decodeChunked($result);
		}
		return $result;
	}


	/***********************/
	/* PARSE JSON RESPONSE */
	/***********************/
	private function parsejsondata($result) {
		$response = json_decode($result);

		if ((!is_object($response)) || (!isset($response->items))) { return false; }
		if (!is_array($response->items)) { return false; }

		$results = array();
		$this->total = count($response->items);
		$maxpage = ($this->total == 0) ? 1 : ceil($this->total/$this->limit);
		if ($maxpage < 1) { $maxpage = 1; }
		if ($this->page > $maxpage) { $this->page = $maxpage; }
		$this->maxpage = $maxpage;
		$this->limitstart = (($this->page - 1) * $this->limit);

		$i = 0;
		foreach ($response->items as $item) {
			if ($i < $this->limitstart) { $i++; continue; }
			if (count($results) == $this->limit) { break; }
			$i++;
			$video = new stdClass;
			$video->videoid = $item->id->videoId;
			$video->url = 'https://www.youtube.com/watch?v='.$item->id->videoId;
			$video->thumbnail = $item->snippet->thumbnails->medium->url;
			$video->title = $item->snippet->title;
			$video->description = $item->snippet->description;
			$video->time = strtotime($item->snippet->publishedAt);
			$results[] = $video;
		}

		return $results;
	}


	/***********************/
	/* SHOW SEARCH RESULTS */
	/***********************/
	public function showResults() {
		$elxis = eFactory::getElxis();
		$eDate = eFactory::getDate();
		$eLang = eFactory::getLang();
		$eDoc = eFactory::getDocument();

		if ($this->dosearch == false) { return; }

		eFactory::getDocument()->addStyleLink($elxis->secureBase().'/components/com_search/engines/youtube/css/youtube.engine.css');

		if ($this->total == 0) {
			if ($this->apierror != '') {
				echo '<div class="elx5_error" dir="ltr"><strong>'.$eLang->get('ERROR').'</strong> '.$this->apierror."<br />YouTube API v3</div>\n";
			} else if ($this->apikey != '') {
				echo '<div class="elx5_warning">An API key is required to perform search on YouTube!'."</div>\n";
			} else {
				echo '<div class="elx5_warning">'.$eLang->get('SEARCH_NO_RESULTS')."</div>\n";
			}
			return;
		}

		$eDoc->loadMediabox();

		$playimg = $elxis->secureBase().'/components/com_search/engines/youtube/css/play.png';

		$boxclass = 'elx_yeng_box'.$eLang->getinfo('RTLSFX');

		echo '<div class="elx_yeng_container">'."\n";
		foreach ($this->results as $row) {
			$title = (eUTF::strlen($row->title) > 38) ? eUTF::substr($row->title, 0, 35).'...' : $row->title;
			$link = 'https://www.youtube.com/v/'.$row->videoid.'&amp;fs=1&amp;rel=0&amp;wmode=transparent'; 
			echo '<div class="'.$boxclass.'">'."\n";
			echo '<div class="elx_yeng_imgbox">'."\n";
			echo '<img class="elx_yeng_play" src="'.$playimg.'" width="32" height="32" />'."\n";
			echo '<a href="'.$link.'" title="'.$row->title.'" target="_blank" data-mediabox="resultyoutube">';
			echo '<img src="'.$row->thumbnail.'" alt="'.$row->title.'" width="160" height="100" /></a>'."\n";
			echo "</div>\n";
			echo '<div class="elx_yeng_notes'.$eLang->getinfo('RTLSFX').'">'."\n";
			echo '<a href="'.$link.'" title="'.$row->title.'" class="elx_yeng_title resultyoutube" target="_blank">'.$title."</a><br />\n";
			echo $eLang->get('DATE').' <span>'.$eDate->formatTS($row->time, $eLang->get('DATE_FORMAT_2'))."</span>\n";
			echo "</div>\n";
			echo "</div>\n";
		}
		echo '<div class="clear"></div>'."\n";
		echo "</div>\n";
	}


	/*******************************/
	/* GET NUMBER OF TOTAL RESULTS */
	/*******************************/
	public function getTotal() {
		return $this->total;
	}


	/********************/
	/* GET SEARCH LIMIT */
	/********************/
	public function getLimit() {
		return $this->limit;
	}


	/**************************/
	/* GET SEARCH LIMIT START */
	/**************************/
	public function getLimitStart() {
		return $this->limitstart;
	}


	/***************************/
	/* GET CURRENT PAGE NUMBER */
	/***************************/
	public function getPage() {
		return $this->page;
	}


	/***************************/
	/* GET MAXIMUM PAGE NUMBER */
	/***************************/
	public function getMaxPage() {
		return $this->maxpage;
	}


	/****************************/
	/* GET SEARCH OPTIONS ARRAY */
	/****************************/
	public function getOptions() {
		return $this->options;
	}


	/******************************************/
	/* GET SEARCH SEARCH FOR THE CURRENT PAGE */
	/******************************************/
	public function getResults() {
		return $this->results;
	}


	/****************************/
	/* DECODE AN CHUNKED STRING */
	/****************************/
	private function decodeChunked($chunk) {
		if (function_exists('http_chunked_decode')) {
			return http_chunked_decode($chunk);
		}

		$pos = 0;
		$len = strlen($chunk);
		$dechunk = null;
		while(($pos < $len) && ($chunkLenHex = substr($chunk,$pos, ($newlineAt = strpos($chunk,"\n",$pos+1))-$pos))) {
			if (!$this->is_hex($chunkLenHex)) { return $chunk; }
			$pos = $newlineAt + 1;
			$chunkLen = hexdec(rtrim($chunkLenHex,"\r\n"));
			$dechunk .= substr($chunk, $pos, $chunkLen);
			$pos = strpos($chunk, "\n", $pos + $chunkLen) + 1;
		}

		return $dechunk;
	}


	/****************************/
	/* IS STRING A HEX NUMBER ? */
	/****************************/
	private function is_hex($hex) {
		$hex = strtolower(trim(ltrim($hex,"0")));
		if (empty($hex)) { $hex = 0; };
		$dec = hexdec($hex);
		return ($hex == dechex($dec));
	}

}

?>