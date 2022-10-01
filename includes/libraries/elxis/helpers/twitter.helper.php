<?php 
/**
* @version		$Id: twitter.helper.php 1839 2016-06-12 18:36:39Z sannosi $
* @package		Elxis
* @subpackage	Helpers / Twitter
* @copyright	Copyright (c) 2006-2016 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisTwitterHelper {

	private $options = array('key' => '', 'secret' => '', 'cachetime' => 0);
	private $token = null;
	private $uagent = '';
	private $errormsg = '';


	/***************/
    /* CONSTRUCTOR */
    /***************/
	public function __construct($key='', $secret='') {
		$this->options['key'] = trim($key);
		$this->options['secret'] = trim($secret);
 		$this->uagent = (isset($_SERVER['HTTP_USER_AGENT'])) ? $_SERVER['HTTP_USER_AGENT'] : 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:29.0) Gecko/20120101 Firefox/29.0';
		if ($this->uagent == '') { $this->uagent = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:29.0) Gecko/20120101 Firefox/29.0'; }
	}


	/***********************/
	/* SET API 1.1 OPTIONS */
	/***********************/
	public function setOption($name, $value) {
		$this->options[$name] = $value;
	}


	/**************************/
	/* GENERIC TWITTER SEARCH */
	/**************************/
	public function search($query, $params=array()) {
		$eCache = eFactory::getCache();

		if (trim($query) == '') {
			$this->errormsg = 'Query parameter is required to search Twitter!';
			return false;
		}

		$this->errormsg = '';
		$response = '';
		$cachetime = (int)$this->options['cachetime'];
		$cache_state = 0;
		$path = 'search/tweets.json?q='.urlencode(trim($query));
		if ($params) {
			foreach ($params as $key => $val) { $path .= '&'.$key.'='.$val; }
		}
		if ($cachetime > 0) {
			$cacheid = md5($path);
			$cache_state = $eCache->begin('search', $cacheid, 'twitter', $cachetime, false, true, 'txt');
			if ($cache_state == 1) {
				$response = $eCache->fetchContents();
			}
		}

		if ($response == '') {
			if (!$this->token) {
				$token = $this->getTwitterToken();
				if (!$token) { return false; }
				$this->token = $token;
			}

			$response = $this->request($path);
		}

		if ($cache_state == 2) {
			$eCache->store($response);
		}

		return $response;
	}


	/**************************/
	/* GENERIC TITTER REQUEST */
	/**************************/
	public function get($path, $params=array()) {
		$eCache = eFactory::getCache();

		$this->errormsg = '';
		$response = '';
		$cachetime = (int)$this->options['cachetime'];
		$cache_state = 0;
		$path = trim($path);
		$path = ltrim($path, '/');
		if ($params) {
			$path .= '?'.http_build_query($params);
		}
		if ($cachetime > 0) {
			$cacheid = md5($path);
			$cache_state = $eCache->begin('search', $cacheid, 'twitter', $cachetime, false, true, 'txt');
			if ($cache_state == 1) {
				$response = $eCache->fetchContents();
			}
		}

		if ($response == '') {
			if (!$this->token) {
				$token = $this->getTwitterToken();
				if (!$token) { return false; }
				$this->token = $token;
			}

			$response = $this->request($path);
		}

		if ($cache_state == 2) {
			$eCache->store($response);
		}

		return $response;
	}


	/********************************/
	/* GET USER TWEETS FROM TWITTER */
	/********************************/
	public function getTweets($username, $limit=10, $include_retweets=false, $convert_links=true, $convert_hashtags=true) {
		$eCache = eFactory::getCache();

		$username  = trim($username);
		$limit = (int)$limit;
		if ($limit < 1) { $limit = 15; }
		if ($username == '') {
			$this->errormsg = 'Twitter username can not be empty!';
			return false;
		}

		$this->errormsg = '';
		$json_str = '';
		$cachetime = (int)$this->options['cachetime'];
		$cache_state = 0;
		if ($cachetime > 0) {
			$cacheid = md5($username);
			$cache_state = $eCache->begin('tweets', $cacheid, 'twitter', $cachetime, false, true, 'txt');
			if ($cache_state == 1) {
				$json_str = $eCache->fetchContents();
			}
		}

		if ($json_str == '') {
			if (!$this->token) {
				$token = $this->getTwitterToken();
				if (!$token) { return false; }
				$this->token = $token;
			}

			$rts = ($include_retweets) ? 'true' : 'false';
			$path = 'statuses/user_timeline.json?screen_name='.$username.'&count='.$limit.'&include_rts='.$rts.'&exclude_replies=true';
			$json_str = $this->request($path);
		}

		if (!$json_str) { return false; }

		$timeline = json_decode($json_str);
		if (!$timeline) {
			$this->errormsg = 'Twitter response was not a JSON string!';
			return false;
		}
		if (!is_array($timeline) || (count($timeline) == 0)) {
			$this->errormsg = 'No tweets found!';
			return false;
		}
		if ($cache_state == 2) {
			$eCache->store($json_str);
		}
		unset($json_str);

		$rows = array();
		foreach ($timeline as $tweet) {
			$ts = strtotime($tweet->created_at);
			$created = gmdate('Y-m-d H:i:s', $ts);
			$row = new stdClass();
			$row->id = $tweet->id_str;
			$row->created = $created;
			$row->created_ts = $ts;
			if ($convert_links) {
				$row->text = preg_replace("~[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]~", "<a href=\"\\0\" target=\"_blank\">\\0</a>", $tweet->text);
			} else {
				$row->text = $tweet->text;
			}

			if ($convert_hashtags) {
				$row->text = preg_replace("/#([A-Za-z0-9\/\.]*)/", "<a target=\"_blank\" href=\"https://twitter.com/search?q=$1\">#$1</a>", $row->text);
			}

			$row->source = $tweet->source;
			$row->retweet_count = (isset($tweet->retweet_count)) ? (int)$tweet->retweet_count : 0;
			$row->favourites_count = (isset($tweet->favourites_count)) ? (int)$tweet->favourites_count : 0;
			$row->in_reply_to_status_id_str = $tweet->in_reply_to_status_id_str;
			$row->in_reply_to_user_id_str = $tweet->in_reply_to_user_id_str;
			$row->in_reply_to_screen_name = $tweet->in_reply_to_screen_name;
			$row->link = 'https://twitter.com/'.$tweet->user->screen_name.'/status/'.$tweet->id_str;
			$rows[] = $row;
		}

		return $rows;
	}


	/******************************/
	/* GET USER INFO FROM TWITTER */
	/******************************/
	public function getInfo($username) { //old method
		return $this->getUser($username);
	}


	/*********************************/
	/* GET USER PROFILE FROM TWITTER */
	/*********************************/
	public function getProfile($username) {
		$eCache = eFactory::getCache();

		$username  = trim($username);
		if ($username == '') {
			$this->errormsg = 'Twitter username can not be empty!';
			return false;
		}

		$this->errormsg = '';
		$json_str = '';
		$cachetime = (int)$this->options['cachetime'];
		$cache_state = 0;
		if ($cachetime > 0) {
			$cacheid = md5($username);
			$cache_state = $eCache->begin('profile', $cacheid, 'twitter', $cachetime, false, true, 'txt');
			if ($cache_state == 1) {
				$json_str = $eCache->fetchContents();
			}
		}

		if ($json_str == '') {
			if (!$this->token) {
				$token = $this->getTwitterToken();
				if (!$token) { return false; }
				$this->token = $token;
			}

			$path = 'users/lookup.json?screen_name='.$username.'&include_entities=false';
			$json_str = $this->request($path);
		}

		if (!$json_str) { return false; }

		$infos = json_decode($json_str);
		if (!$infos) {
			$this->errormsg = 'Twitter response was not a JSON string!';
			return false;
		}
		if (!is_array($infos) || (count($infos) == 0)) {
			$this->errormsg = 'User not found!';
			return false;
		}

		if ($cache_state == 2) {
			$eCache->store($json_str);
		}
		unset($json_str);
		$info = $infos[0];
		unset($infos);

		$row = new stdClass();
		$row->id = $info->id_str;
		$row->name = $info->name;
		$row->screen_name = $info->screen_name;
		$row->location = $info->location;
		$row->description = $info->description;
		$row->url = $info->url;
		$row->url_title = '';
		if (isset($info->entities->url->urls)) {
			if ($info->entities->url->urls) {
				foreach ($info->entities->url->urls as $url) {
					if (trim($url->expanded_url) != '') { $row->url = $url->expanded_url; }
					$row->url_title = $url->display_url;
					break;
				}
			}
		}
		$row->followers_count = (int)$info->followers_count;
		$row->friends_count = (int)$info->friends_count;
		$row->listed_count = (int)$info->listed_count;
		$row->statuses_count = (int)$info->statuses_count;
		$row->created_at = $info->created_at;
		$row->favourites_count = (isset($info->favourites_count)) ? (int)$info->favourites_count : 0;
		$row->utc_offset = (int)$info->utc_offset;
		$row->time_zone = $info->time_zone;
		$row->profile_image_url = $info->profile_image_url_https;
		$row->profile_banner_url = $info->profile_banner_url;

		return $row;
	}


	/********************************/
	/* GET THE LAST GENERATED ERROR */
	/********************************/
	public function getError() {
		return $this->errormsg;
	}


	/*********************************/
	/* GET BEARER TOKEN FROM TWITTER */
	/*********************************/
	private function getTwitterToken() {
		if (!function_exists('curl_init')) {
			$this->errormsg = 'There is no support for CURL in your PHP installation!';
			return false;
		}
		if (trim($this->options['key']) == '') {
			$this->errormsg = 'Twitter Consumer Key can not be empty!';
			return false;
		}
		if (trim($this->options['secret']) == '') {
			$this->errormsg = 'Twitter Consumer Secret can not be empty!';
			return false;
		}

		$enc_key = urlencode($this->options['key']);
		$enc_secret = urlencode($this->options['secret']);
		$base64_enc = base64_encode($enc_key.':'.$enc_secret);

		$headers = array( 
			'POST /oauth2/token HTTP/1.1', 
			'Host: api.twitter.com', 
			'User-Agent: '.$this->uagent,
			'Authorization: Basic '.$base64_enc,
			'Content-Type: application/x-www-form-urlencoded;charset=UTF-8', 
			'Content-Length: 29'
		); 

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://api.twitter.com/oauth2/token');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_REFERER, eFactory::getElxis()->getConfig('URL'));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->uagent);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
		curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_HEADER, false);
		//$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE); //set CURLOPT_HEADER to true if you want to use this
        $result = curl_exec($ch);
        if (curl_errno($ch) == 0) {
            curl_close($ch);
        } else {
        	$this->errormsg = curl_error($ch);
            curl_close($ch);
            return false;
        }

		if (trim($result) == '') { $this->errormsg = 'Bearer token failed! Empty response from Twitter.'; return false; }

		$output = explode("\n", $result);
		$bearer_token = '';
		foreach ($output as $line) {
			if (trim($line) != '') { $bearer_token = $line; break; }
		}
		if ($bearer_token == '') { $this->errormsg = 'Getting Bearer token for Twitter failed!'; return false; }

		$jObj = json_decode($bearer_token);

		return $jObj->{'access_token'};
	}


	/***************************/
	/* INVALIDATE BEARER TOKEN */
	/***************************/
	public function destroyToken() {
		if (!$this->token) { return true; }
		$enc_key = urlencode($this->options['key']);
		$enc_secret = urlencode($this->options['secret']);
		$base64_enc = base64_encode($enc_key.':'.$enc_secret);

		$headers = array( 
			'POST /oauth2/invalidate_token HTTP/1.1', 
			'Host: api.twitter.com', 
			'User-Agent: '.$this->uagent,
			'Authorization: Basic '.$base64_enc,
			'Accept: */*', 
			'Content-Type: application/x-www-form-urlencoded', 
			'Content-Length: '.(strlen($this->token)+13)
		); 

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://api.twitter.com/oauth2/invalidate_token');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_REFERER, eFactory::getElxis()->getConfig('URL'));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->uagent);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
		curl_setopt($ch, CURLOPT_POSTFIELDS, 'access_token='.$this->token);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_HEADER, false);
        $result = curl_exec($ch);
        if (curl_errno($ch) == 0) {
            curl_close($ch);
            $this->token = null;
            return true;
        } else {
        	$this->errormsg = curl_error($ch);
            curl_close($ch);
            return false;
        }
	}


	/*************************/
	/* EXECUTE A GET REQUEST */
	/*************************/
	private function request($path) {
		$headers = array(
			'GET /1.1/'.$path.' HTTP/1.1', 
			'Host: api.twitter.com', 
			'User-Agent: jonhurlock Twitter Application-only OAuth App v.1',
			'Authorization: Bearer '.$this->token
		);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://api.twitter.com/1.1/'.$path);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FAILONERROR, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->uagent);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
		curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_REFERER, eFactory::getElxis()->getConfig('URL'));
        $result = curl_exec($ch);
        if (curl_errno($ch) == 0) {
            curl_close($ch);
            return $result;
        } else {
        	$this->errormsg = curl_error($ch);
            curl_close($ch);
            return false;
        }
	}

}

?>