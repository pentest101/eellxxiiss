<?php 
/**
* @version		$Id$
* @package		Elxis
* @subpackage	Helpers / Browser detection
* @copyright	Copyright (c) 2006-2019 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*
* data sources 	
				http://www.webuseragents.com/feed-reader
				http://www.webuseragents.com/robot
				https://user-agents.me/cfnetwork-version-list
				https://en.wikipedia.org/wiki/Comparison_of_mobile_operating_systems
				https://www.whatismybrowser.com/developers/tools/user-agent-parser/browse
				https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/User-Agent/Firefox
				https://msdn.microsoft.com/en-us/library/ms537503(v=vs.85).aspx
				https://developer.chrome.com/multidevice/user-agent
				http://www.webapps-online.com/online-tools/user-agent-strings/dv/device5/tv
				https://udger.com/resources/ua-list
				http://stackoverflow.com/questions/26528668/detecting-the-stock-android-browser-with-php
* inpired by
				https://github.com/lennerd/vipx-bot-detect
				https://github.com/piwik/device-detector
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisBrowserHelper {

	private $useragent = '';
	private $device = array(
		'type' => '', //desktop, mobile, tablet, tv, car, robot, feedreader, emailclient, ...
		'name' => '',
		'version' => '',
		'os_platform' => '',
		'os_name' => '',
		'os_version' => '',
		'extra' => array()
	);


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
	}


	/********************/
	/* GET BROWSER INFO */
	/********************/
	public function getBrowser($useragent='', $compat=true) {
		if (trim($useragent) == '') {
			$useragent = isset($_SERVER['HTTP_USER_AGENT']) ? filter_var($_SERVER['HTTP_USER_AGENT'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH) : '';
		}
		if (($useragent != '') && ($useragent == $this->useragent)) { //previously fully analized
			return $this->getBrowserResponse($useragent, $compat);
		}

		$this->init($useragent);
		if ($useragent == '') {
			return $this->getBrowserResponse($useragent, $compat);
		}

		$this->detect();

		return $this->getBrowserResponse($useragent, $compat);
	}


	/****************************/
	/* MAKE getBrowser RESPONSE */
	/****************************/
	private function getBrowserResponse($useragent, $compat=true) {
		//No compatibility with previous Elxis versions (4.5 r1889+)
		if (!$compat) {
			$browser_info = $this->device;
			$browser_info['useragent'] = $useragent;
			return $browser_info;
		}

		//Elxis 4.0-4.5r1888 compatibility
		$browser_info = array(
			'agent' => $useragent,
			'browser' => $this->device['name'],
			'version' => $this->device['version'],
			'os' => '',//deprecated
			'os_name' => $this->device['os_name'],
			'os_version' => $this->device['os_version'],
			'platform' => $this->device['os_platform'],//warning: previously OS family, now architecture
			'mobile' => false,
			'robot' => false,
			'aol' => false,//deprecated
			'aol_version' => ''//deprecated
		);

		if ($useragent != '') {
			$browser_info['mobile'] = $this->isMobile($useragent);
		}
		
		$browser_info['robot'] = ($this->device['type'] == 'robot') ? true : false;

		//Elxis 4.5 r1888+ device
		$browser_info['useragent'] = $useragent;
		foreach ($this->device as $k => $v) {
			$browser_info[$k] = $v;
		}

		return $browser_info;
	}


	/******************************/
	/* DETECT BROWSER/OS/PLATFORM */
	/******************************/
	private function detect() {
		if ($this->checkRobot($this->useragent) === true) { return; } //found type "robot", dont proceed further
		if ($this->checkFeedReader($this->useragent) === true) { return; } //found type "feedreader"
		if ($this->checkValidator($this->useragent) === true) { return; } //found type "validator"
		if ($this->checkLibrary($this->useragent) === true) { return; } //found type "library"
		if ($this->checkMediaPlayer($this->useragent) === true) { return; } //found type "mediaplayer"
		if ($this->checkEmailClient($this->useragent) === true) { return; } //found type "emailclient"
		if ($this->checkBrowser($this->useragent) === true) { //found type "desktop/mobile/tv etc"
			//includes OS check and platform check
			return; 
		}
	}


	/*****************************/
	/* INITIALIZE ALL PROPERTIES */
	/*****************************/
	private function init($useragent='') {
		$this->useragent = $useragent;
		$this->device = array(
			'type' => '', //desktop, mobile, tablet, tv, car, robot, feedreader, ...
			'name' => '',
			'version' => '',
			'os_platform' => '',
			'os_name' => '',
			'os_version' => '',
			'extra' => array()
		);
	}


	/**************************************************/
	/* PERFORM REGULAR EXPRESION SEARCH ON USER AGENT */
	/**************************************************/
	private function matchUserAgent($useragent, $regex) {
		if (!@preg_match('#'.$regex.'#i', $useragent, $matches)) { return false; }
		return $matches;
    }


	/*********************************/
	/* CHECK IF IT IS A ROBOT/SPIDER */
	/*********************************/
	private function checkRobot($useragent) {
		$found = false;
		$patterns = $this->getRobotPatterns();
		foreach ($patterns as $pat) {
			$matches = $this->matchUserAgent($useragent, $pat[0]);
			if (!$matches) { continue; }
			$this->device['type'] = 'robot';
			$this->device['name'] = $pat[1];
			$found = true;
			break;
		}
		return $found;
	}


	/********************************/
	/* CHECK IF IT IS A FEED READER */
	/********************************/
	private function checkFeedReader($useragent) {
		$found = false;
		$patterns = $this->getFeedPatterns();
		foreach ($patterns as $pat) {
			$matches = $this->matchUserAgent($useragent, $pat[0]);
			if (!$matches) { continue; }
			$this->device['type'] = 'feedreader';
			$this->device['name'] = $pat[1];
			$found = true;
			break;
		}
		return $found;
	}


	/********************************************/
	/* CHECK IF IT IS A (W3C) VALIDATOR SERVICE */
	/********************************************/
	private function checkValidator($useragent) {
		$found = false;
		$patterns = array(
			array('W3C_*Validator', 'W3C Validator'),
			array('online link validator', 'Online link'),
			array('JCheckLinks', 'JCheckLinks'),
			array('CheckLinks\/', 'CheckLinks'),
			array('LongURL\sAPI', 'LongURL'),
			array('FeedValidator', 'W3C Feed Validator'),
			array('FeedValidator', 'W3C Feed Validator'),
			array('W3C-checklink\/', 'W3C Checklink'),
			array('HTMLParser\/', 'HTMLParser'),
			array('Xenu\sLink\sSleuth\/', 'Xenu'),
		);
		foreach ($patterns as $pat) {
			$matches = $this->matchUserAgent($useragent, $pat[0]);
			if (!$matches) { continue; }
			$this->device['type'] = 'validator';
			$this->device['name'] = $pat[1];
			$found = true;
			break;
		}
		return $found;
	}


	/**************************************/
	/* CHECK IF IT IS A LIBRARY LIKE WGET */
	/**************************************/
	private function checkLibrary($useragent) {
		$found = false;
		$patterns = array(
			array('libcurl', 'cURL'),
			array('Elxis\sCURL', 'Elxis cURL'),
			array('perlclient|libwww-perl', 'Perl'),
			array('winhttp', 'WinHTTP'),
			array('Java\/', 'Java'),
			array('Python-urllib|Python-webchecker|python-requests', 'Python'),
			array('Snoopy', 'Snoopy'),
			array('Wget\/', 'Wget'),
			array('Go\s1\.1\spackage\shttp|Go\shttp\spackage|Go\-http\-client', 'Go language'),
			array('Indy\sLibrary', 'Indy')
		);
		foreach ($patterns as $pat) {
			$matches = $this->matchUserAgent($useragent, $pat[0]);
			if (!$matches) { continue; }
			$this->device['type'] = 'library';
			$this->device['name'] = $pat[1];
			$found = true;
			break;
		}
		return $found;
	}


	/*********************************/
	/* CHECK IF IT IS A MEDIA PLAYER */
	/*********************************/
	private function checkMediaPlayer($useragent) {
		$found = false;
		$patterns = array(
			array('iTunes', 'iTunes'),
			array('QuickTime', 'QuickTime'),
			array('Songbird', 'Songbird'),
			array('FlyCast', 'FlyCast'),
			array('Windows-Media-Player', 'Windows Media Player'),
			array('VLC\smedia\splayer', 'VLC media player'),
			array('Winamp', 'Winamp'),
			array('XBMC', 'XBMC'),
			array('Banshee', 'Banshee'),
			array('Boxxe', 'Boxxe'),
			array('Dailymotion', 'Dailymotion'),
			array('foobar2000', 'foobar2000'),
			array('GOM\sPlayer', 'GOM Player'),
			array('MPlayer2', 'MPlayer2'),
			array('MPlayer', 'MPlayer'),
			array('Plex\sMedia\sCenter', 'Plex Media Center'),
			array('Instacast', 'Instacast'),
			array('SubStream', 'SubStream'),
			array('XMPlay', 'XMPlay')
		);
		foreach ($patterns as $pat) {
			$matches = $this->matchUserAgent($useragent, $pat[0]);
			if (!$matches) { continue; }
			$this->device['type'] = 'mediaplayer';
			$this->device['name'] = $pat[1];
			$found = true;
			break;
		}
		return $found;
	}


	/**********************************/
	/* CHECK IF IT IS AN EMAIL CLIENT */
	/**********************************/
	private function checkEmailClient($useragent) {
		$found = false;
		$patterns = array(
			array('Outlook-Express', 'Windows Live Mail'),
			array('Thunderbird', 'Thunderbird'),
			array('Airmail', 'Airmail'),
			array('Microsoft\sOutlook', 'Microsoft Outlook'),
			array('Lotus-Notes', 'Lotus Notes'),
			array('The\sBat!', 'The Bat!'),
			array('PocoMail', 'PocoMail')
		);
		foreach ($patterns as $pat) {
			$matches = $this->matchUserAgent($useragent, $pat[0]);
			if (!$matches) { continue; }
			$this->device['type'] = 'emailclient';
			$this->device['name'] = $pat[1];
			$found = true;
			break;
		}
		return $found;
	}


	/*************************************/
	/* CHECK IF IT IS A STANDARD BROWSER */
	/*************************************/
	private function checkBrowser($useragent) {
		$browsers = array(
			'Edge' => 'Windows',//edge needs to be placed first, else might be detected as Chrome.
			'Small' => 'Android',//check small (mobile mostly) browsers first as they contain strings like safari, opera, etc.
			'Other' => 'Linux',//same as for "Small" browsers
			'Chrome' => 'Windows',
			'Firefox' => 'Windows',
			'IE' => 'Windows',//internet explorer
			'Safari' => 'Mac',
			'Opera' => 'Mac',
			'Mozilla' => 'Linux'//check last due to open standard
		);

		$found = false;
		$os_found = false;
		foreach ($browsers as $browser => $possible_os) {
			$funcBR = 'checkBrowser'.$browser;
			$found = $this->$funcBR($useragent);
			if (!$found) { continue; }

			$os_found = false;
			if ($possible_os != '') {
				$funcOS = 'checkOs'.$possible_os;
				$os_found = $this->$funcOS($useragent);
			}
			if (!$os_found) {
				$os_found = $this->checkAllOperatingSystems($useragent, $possible_os);//check all operating systems except possible os
			}
			$this->checkPlatform($useragent);
			break;
		}

		if (!$found) {//browser not found, try at least to find operating system
			if (!$os_found) {
				$this->checkAllOperatingSystems($useragent, '');
				$this->checkPlatform($useragent);
			}
		}
	}


	/******************/
	/* CHECK PLATFORM */
	/******************/
	private function checkPlatform($useragent) {
		if ($this->matchUserAgent($useragent, 'WOW64|x64|win64|amd64|x86_64')) {
			$this->device['os_platform'] = 'x64';
		} else if ($this->matchUserAgent($useragent, 'i[0-9]86|i86pc')) {
			$this->device['os_platform'] = 'x86';
		} else if (stripos($useragent, 'arm')) {
			$this->device['os_platform'] = 'ARM';
		} else {
			$this->device['os_platform'] = '';
		}
    }


	private function checkBrowserEdge($useragent) {
		$regex_mobile = 'Windows Phone .*(Edge)/(\\d+)\\.(\\d+)';
		$regex = '(Edge)/(\\d+)\\.(\\d+)';
		$matches = $this->matchUserAgent($useragent, $regex_mobile);
		if ($matches) {
			$this->device['type'] = 'mobile';
			$this->device['name'] = 'Edge';
			$this->device['version'] = $matches[2].'.'.$matches[3];
			return true;
		}
		$matches = $this->matchUserAgent($useragent, $regex);
		if ($matches) {
			$this->device['type'] = 'desktop';
			$this->device['name'] = 'Edge';
			$this->device['version'] = $matches[2].'.'.$matches[3];
			return true;
		}
		return false;
	}


	private function checkBrowserFirefox($useragent) {
		$is_firefox = false;
		$version = '';
		if (preg_match("#Firefox[\/ \(]([^ ;\)]+)#i", $useragent, $matches)) {
			$is_firefox = true;
			$version = (isset($matches[1])) ? $matches[1] : '';
		} else if (preg_match("#FxiOS/(\d+[\.\d]+)#i", $useragent, $matches)) {
			$is_firefox = true;
			$version = (isset($matches[1])) ? 'FxiOS '.$matches[1] : 'FxiOS';
		} else if (preg_match("#Firefox#i", $useragent, $matches)) {
			$is_firefox = true;
			$version = '';
		}

		if (!$is_firefox) { return; }

		if (preg_match('/^[0-9\.]+$/', $version)) {
			$parts = explode('.', $version);
			$version = $parts[0];
			if (isset($parts[1])) { $version .= '.'.$parts[1]; }
		}

		$this->device['name'] = 'Firefox';
		$this->device['version'] = $version;

		$is_tv = $this->deviceIsTV($useragent);
		if ($is_tv) {
			$this->device['type'] = 'tv';
		} else if (stripos($useragent, 'Mobile') !== false) {
			$this->device['type'] = 'mobile';
		} else if (stripos($useragent, 'Tablet') !== false) {
			$this->device['type'] = 'tablet';
		} else {
			$this->device['type'] = 'desktop';
		}

		return true;
	}


	private function checkBrowserIE($useragent) {
		$patterns = array(
			//mobile
			array('IEMobile[ /](\d+[\.\d]+)', 'IE Mobile', 1),
			array('MSIE (\d+[\.\d]+).*XBLWP7', 'IE Mobile', 1),
			//desktop
			array('MSIE.*Trident/4.0', 'Internet Explorer', '8.0'),
			array('MSIE.*Trident/5.0', 'Internet Explorer', '9.0'),
			array('MSIE.*Trident/6.0', 'Internet Explorer', '10.0'),
			array('MSIE.*Trident/7.0', 'Internet Explorer', '11.0'),
			array('MSIE (\d+[\.\d]+)', 'Internet Explorer', 1),
			array('IE[ /](\d+[\.\d]++)', 'Internet Explorer', 1)
		);

		$found = false;
		foreach ($patterns as $pat) {
			$matches = $this->matchUserAgent($useragent, $pat[0]);
			if (!$matches) { continue; }
			if (stripos($useragent, 'Tablet PC') !== false) {
				$this->device['type'] = 'tablet';
			} else {
				$this->device['type'] = ($pat[1] == 'IE Mobile') ? 'mobile' : 'desktop';
			}
			$this->device['name'] = $pat[1];
			$this->device['version'] = $this->versionFromMatches($pat[2], $matches);
			$found = true;
			break;
		}
		return $found;
	}


	private function checkBrowserSafari($useragent) {
		$patterns = array(
			//mobile
			array('(?:(?:iPod|iPad|iPhone).+Version|MobileSafari)/(\d+[\.\d]+)', 'Mobile Safari', 1),
			array('Version/(\d+[\.\d]+).*Mobile.*Safari/', 'Mobile Safari', 1),
			array('(?:iPod|iPhone|iPad)', 'Mobile Safari', ''),
			//desktop
			array('Version/(\d+[\.\d]+).*Safari/|Safari/\d+', 'Safari', 1)
		);

		$found = false;
		foreach ($patterns as $pat) {
			$matches = $this->matchUserAgent($useragent, $pat[0]);
			if (!$matches) { continue; }
			if (stripos($useragent, 'iPad') !== false) {
				$this->device['type'] = 'tablet';
			} else {
				$this->device['type'] = ($pat[1] == 'Mobile Safari') ? 'mobile' : 'desktop';
			}
			$this->device['name'] = $pat[1];
			$this->device['version'] = $this->versionFromMatches($pat[2], $matches);
			$found = true;
			break;
		}
		return $found;
	}


	private function checkBrowserChrome($useragent) {
		$patterns = array(
			//mobile
			array('CrMo(?:/(\d+[\.\d]+))?', 'Chrome Mobile', 1),
			array('CriOS(?:/(\d+[\.\d]+))?', 'Chrome Mobile iOS', 1),
			array('Chrome(?:/(\d+[\.\d]+))? Mobile', 'Chrome Mobile', 1),
			//desktop/tablet/TV
			array('Chrome(?:/(\d+[\.\d]+))?', 'Chrome', 1)
		);

		$found = false;
		foreach ($patterns as $pat) {
			$matches = $this->matchUserAgent($useragent, $pat[0]);
			if (!$matches) { continue; }
			$is_tv = $this->deviceIsTV($useragent);
			if ($is_tv) {
				$this->device['type'] = 'tv';
			} else if (($pat[1] == 'Chrome Mobile') || ($pat[1] == 'Chrome Mobile iOS')) {
				$this->device['type'] = 'mobile';
			} else {
				$this->device['type'] = (stripos($useragent, 'Android') === false) ? 'desktop' : 'tablet';
			}
			$this->device['name'] = $pat[1];
			$this->device['version'] = $this->versionFromMatches($pat[2], $matches);
			$found = true;
			break;
		}
		return $found;
	}


	private function checkBrowserOpera($useragent) {
		$patterns = array(
			//mobile
			array('(?:Opera Tablet.*Version)/(\d+[\.\d]+)', 'Opera Mobile', 1),
			array('Opera Mini/(?:att/)?(\d+[\.\d]+)', 'Opera Mini', 1),
			array('(Opera/.+Opera Mobi.+Version)/(\d+[\.\d]+)', 'Opera Mobile', 1),
			array('(Mobile.+OPR)/(\d+[\.\d]+)', 'Opera Mobile', 1),
			//desktop/tablet/TV
			array('Opera.+Edition Next.+Version/(\d+[\.\d]+)', 'Opera Next', 1),
			array('(?:Opera|OPR)[/ ](?:9.80.*Version/)?(\d+[\.\d]+).+Edition Next', 'Opera Next', 1),
			array('(?:Opera|OPR)[/ ]?(?:9.80.*Version/)?(\d+[\.\d]+)', 'Opera', 1)
		);


//Opera/9.80 (J2ME/MIDP; Opera Mini/9.80 (S60; SymbOS; Opera Mobi/23.348; U; en) Presto/2.5.25 Version/10.54

		$found = false;
		foreach ($patterns as $pat) {
			$matches = $this->matchUserAgent($useragent, $pat[0]);
			if (!$matches) { continue; }
			$is_tv = $this->deviceIsTV($useragent);
			if ($is_tv) {
				$this->device['type'] = 'tv';
			} else if (($pat[1] == 'Opera Mobile') || ($pat[1] == 'Opera Mini')) {
				$this->device['type'] = 'mobile';
			} else {
				if ((stripos($useragent, 'Tablet') !== false) || (stripos($useragent, 'Android') !== false)) {
					$this->device['type'] = 'tablet';
				} else if (stripos($useragent, 'Coast') !== false) {//Opera Coast for iphone
					$this->device['type'] = 'mobile';
				} else {
					$this->device['type'] = 'desktop';
				}
			}
			$this->device['name'] = $pat[1];
			$this->device['version'] = $this->versionFromMatches($pat[2], $matches);
			$found = true;
			break;
		}
		return $found;
	}


	private function checkBrowserSmall($useragent) {
		$patterns = array(
			//mobile
			array('Dolfin(?:/(\d+[\.\d]+))?|dolphin', 'Dolphin', 1),
			array('flynx', 'Flynx', ''),
			array('GhosteryBrowser-Android(?:/v(\d+[\.\d]+))?|GhosteryBrowser', 'Ghostery', 1),
			array('Mercury(?:/(\d+[\.\d]+))?', 'Mercury', 1),
			array('Puffin(?:/(\d+[\.\d]+))?', 'Puffin', 1),
			array('UC[ ]?Browser(?:[ /]?(\d+[\.\d]+))?', 'UC Browser', 1),
			array('UCWEB(?:[ /]?(\d+[\.\d]+))?', 'UC Browser', 1),
			array('Skyfire(?:/(\d+[\.\d]+))?|Skyfire', 'Skyfire', 1),
			array('Bolt(?:/(\d+[\.\d]+))?', 'Bolt', 1),
			array('TeaShark(?:/(\d+[\.\d]+))?', 'TeaShark', 1),
			array('Blazer(?:/(\d+[\.\d]+))?|Blazer', 'Blazer', 1),
			array('(Tizen\/|Tizen\s|Tizen\sBrowser\/)(\d+[\.\d]+)?', 'Tizen', 2),
			array('OmniWeb(?:/[v]?(\d+[\.\d]+))?', 'OmniWeb', 1),
			array('SailfishBrowser(?:/(\d+[\.\d]+))?', 'Sailfish', 1),
			array('Fennec(?:/(\d+[\.\d]+))?', 'Fennec', 1),
			array('Silk(?:/(\d+[\.\d]+))?', 'Silk', 1),
			array('(BB10|BlackBerry|PlayBook).+Version/(\d+[\.\d]+)', 'BlackBerry', 2),
			array('BlackBerry', 'BlackBerry', ''),
			array('(?:Maxthon|MxBrowser)[ /](\d+[\.\d]+)', 'Maxthon', 1),//mobile/desktop
			array('ACHEETAHI', 'CM Browser', ''),
			array('Brave(?:/(\d+[\.\d]+))?', 'Brave', 1),
			array('Openwave(?:/(\d+[\.\d]+))?', 'Openwave', 1),
			array('UP.Browser(?:/(\d+[\.\d]+))?', 'Openwave', 1),
			array('Firefox.*Tablet browser (\d+[\.\d]+)', 'MicroB', 1),
			array('Maemo Browser(?: (\d+[\.\d]+))?', 'MicroB', 1),
			array('PLAYSTATION|NINTENDO\s3', 'Playstation', ''),
			array('NetFront', 'NetFront', ''),
			array('(?:Polaris|Embider)(?:[/ ](\d+[\.\d]+))?', 'Polaris', 1)
		);

		$found = false;
		foreach ($patterns as $pat) {
			$matches = $this->matchUserAgent($useragent, $pat[0]);
			if (!$matches) { continue; }
			$is_tv = $this->deviceIsTV($useragent);
			if ($is_tv) {
				$this->device['type'] = 'tv';
			} else if ($pat[1] == 'Maxthon') {
				$this->device['type'] = (stripos($useragent, 'Android') === false) ? 'desktop' : 'mobile';
			} else if ($pat[1] == 'MicroB') {
				$this->device['type'] = 'tablet';
			} else {
				$this->device['type'] = 'mobile';
			}
			$this->device['name'] = $pat[1];
			$this->device['version'] = $this->versionFromMatches($pat[2], $matches);
			$found = true;
			break;
		}

		if (!$found) {
			//Stock Android browser
			preg_match('#Android.*AppleWebKit\/([\d.]+)#i', $useragent, $matches);
			if ($matches) {
				if (isset($matches[0]) && isset($matches[1]) && (intval($matches[1]) < 537)) {
					$this->device['type'] = 'mobile';
					$this->device['name'] = 'Android browser';
					$this->device['version'] = '';
					$found = true;
				}
			}
		}

		return $found;
	}


	private function checkBrowserOther($useragent) {
		$pattern = '(?:Avant\sBrowser|Kapiko|Fireweb|Conkeror|Vivaldi|Midori|AmigaVoyager|Amiga-Aweb|CometBird|Flock|Swiftfox|BaiduBrowser|Kindle|QupZilla|Lynx)';
		preg_match("#".$pattern."#i", $useragent, $matches);
		if ($matches) {
			$this->device['type'] = ($matches[0] == 'Kindle') ? 'tablet' : 'desktop';
			$this->device['name'] = $matches[0];
			$this->device['version'] = '';
			return true;
		}

		$patterns = array(
			array('Konqueror(?:/(\d+[\.\d]+))?', 'Konqueror', 1),
			array('Epiphany(?:/(\d+[\.\d]+))?', 'Epiphany', 1),
			array('PaleMoon(?:/(\d+[\.\d]+))?', 'Pale Moon', 1),
			array('Camino(?:/(\d+[\.\d]+))?', 'Camino', 1),
			array('Chimera(?:/(\d+[\.\d]+))?', 'Camino', 1),
			array('Iceweasel(?:/(\d+[\.\d]+))?', 'Iceweasel', 1),
			array('IceDragon(?:/(\d+[\.\d]+))?', 'IceDragon', 1),
			array('YaBrowser(?:/(\d+[\.\d]+))?', 'Yandex', 1),
			array('SamsungBrowser(?:/(\d+[\.\d]+))?', 'Samsung Browser', 1),
			array('Galeon(?:/(\d+[\.\d]+))?', 'Galeon', 1),
			array('LG Browser(?:/(\d+[\.\d]+))', 'LG Browser', 1),
			array('Netscape(?:/(\d+[\.\d]+))?', 'Netscape', 1),
			array('Navigator(?:/(\d+[\.\d]+))?', 'Netscape', 1),
			array('Arora(?:/(\d+[\.\d]+))?', 'Arora', 1)
		);

		$found = false;
		foreach ($patterns as $pat) {
			$matches = $this->matchUserAgent($useragent, $pat[0]);
			if (!$matches) { continue; }
			$is_tv = $this->deviceIsTV($useragent);
			if ($is_tv) {
				$this->device['type'] = 'tv';
			} else {
				$this->device['type'] = 'desktop';
			}
			$this->device['name'] = $pat[1];
			$this->device['version'] = $this->versionFromMatches($pat[2], $matches);
			$found = true;
			break;
		}
		return $found;
	}


	private function checkBrowserMozilla($useragent) {
		if (stripos($useragent,'mozilla') === false) { return false; }
		$matches = $this->matchUserAgent($useragent, 'rv:(?:(\d+[\.\d]+))?', 1);
		$is_tv = $this->deviceIsTV($useragent);
		if ($is_tv) {
			$this->device['type'] = 'tv';
		} else if (preg_match('#android|mobile|tablet|iphone|ipad|smartphone#i', $useragent)) {
			$this->device['type'] = 'mobile';
		} else {
			$this->device['type'] = 'desktop';
		}
		$this->device['name'] = 'Mozilla';
		if ($matches) {
			if (isset($matches[1])) {
				$this->device['version'] = $matches[1];
			}
		}
		return true;
	}


	/********************/
	/* CHECK WINDOWS OS */
	/********************/
	private function checkOsWindows($useragent) {
		$patterns = array(
			//mobile/tablet
			array('Windows Phone (?:OS)?[ ]?(\d+[\.\d]+)', 'Windows Phone', 1),
			array('XBLWP7|Windows Phone', 'Windows Phone', ''),
			array('Windows CE(?: (\d+[\.\d]+))?', 'Windows CE', 1),
			array('(?:IEMobile|Windows Mobile)(?: (\d+[\.\d]+))?', 'Windows Mobile', 1), 
			array('Windows NT 6.2; ARM;', 'Windows RT', ''),
			array('Windows NT 6.3; ARM;', 'Windows RT', '8.1'),
			//desktop
			array('CYGWIN_NT-10.0|Windows NT 10.0|Windows 10', 'Windows', '10'),
			array('CYGWIN_NT-6.4|Windows NT 6.4|Windows 10', 'Windows', '10'),
			array('CYGWIN_NT-6.3|Windows NT 6.3|Windows 8.1', 'Windows', '8.1'),
			array('CYGWIN_NT-6.2|Windows NT 6.2|Windows 8', 'Windows', '8'),
			array('CYGWIN_NT-6.1|Windows NT 6.1|Windows 7', 'Windows', '7'),
			array('CYGWIN_NT-6.0|Windows NT 6.0|Windows Vista', 'Windows', 'Vista'),
			array('CYGWIN_NT-5.2|Windows NT 5.2|Windows Server 2003 / XP x64', 'Windows', 'Server 2003'),
			array('CYGWIN_NT-5.1|Windows NT 5.1|Windows XP', 'Windows', 'XP'),
			array('CYGWIN_NT-5.0|Windows NT 5.0|Windows 2000', 'Windows', '2000'),
			array('CYGWIN_NT-4.0|Windows NT 4.0|WinNT|Windows NT', 'Windows', 'NT'),
			array('CYGWIN_ME-4.90|Win 9x 4.90|Windows ME', 'Windows', 'ME'),
			array('CYGWIN_98-4.10|Win98|Windows 98', 'Windows', '98'),
			array('CYGWIN_95-4.0|Win32|Win95|Windows 95|Windows_95', 'Windows', '95'),
			array('Windows 3.1', 'Windows', '3.1'),
			array('Windows', 'Windows', '')
		);

		return $this->checkOs($useragent, $patterns);
	}


	/********************/
	/* CHECK ANDROID OS */
	/********************/
	private function checkOsAndroid($useragent) {
		$patterns = array(
			array('(?:(?:Orca-)?Android|Adr)[ /](?:[a-z]+ )?(\d+[\.\d]+)', 'Android', 1),
			array('Android|Silk-Accelerated=[a-z]{4,5}', 'Android', ''),
			array('BeyondPod|AntennaPod|Podkicker|DoggCatcher', 'Android', ''),
			array('(?:Ali)?YunOS[ /]?(\d+[\.\d]+)?', 'YunOS', 1),
			array('RazoDroiD(?: v(\d+[\.\d]*))?', 'RazoDroiD', 1),
			array('MildWild(?: CM-(\d+[\.\d]*))?', 'MildWild', 1),
			array('CyanogenMod(?:[\-/](?:CM)?(\d+[\.\d]*))?', 'CyanogenMod', 1),
			array('(?:.*_)?MocorDroid(?:(\d+[\.\d]*))?', 'MocorDroid', 1),
		);

		return $this->checkOs($useragent, $patterns);
	}


	/****************/
	/* CHECK MAC OS */
	/****************/
	private function checkOsMac($useragent) {
		$patterns = array(
			//https://user-agents.me/cfnetwork-version-list
			array('CFNetwork\/760', 'Mac', '10.11'),
			array('CFNetwork\/720', 'Mac', '10.10'),
			array('CFNetwork\/673', 'Mac', '10.9'),
			array('CFNetwork\/596', 'Mac', '10.8'),
			array('CFNetwork\/520', 'Mac', '10.7'),
			array('CFNetwork\/454', 'Mac', '10.6'),
			array('CFNetwork\/438|CFNetwork\/422|CFNetwork\/339|CFNetwork\/330|CFNetwork\/221|CFNetwork\/220|CFNetwork\/217', 'Mac', '10.5'),
			array('CFNetwork\/129|CFNetwork\/128', 'Mac', '10.4'),
			array('CFNetwork\/1\.2', 'Mac', '10.3'),
			array('CFNetwork\/1\.1', 'Mac', '10.2'),
			array('Mac OS X(?: (?:Version )?(\d+(?:[_\.]\d+)+))?', 'Mac', 1),
			array('Mac (\d+(?:[_\.]\d+)+)', 'Mac', 1),
			array('Darwin|Macintosh|Mac_PowerPC|PPC|Mac PowerPC|iMac|MacBook', 'Mac', '')
		);

		return $this->checkOs($useragent, $patterns);
	}


	/****************/
	/* CHECK IOS OS */
	/****************/
	private function checkOsIos($useragent) {
		$patterns = array(
			//https://user-agents.me/cfnetwork-version-list
			array('CFNetwork\/808|CFNetwork\/790', 'iOS', '10'),
			array('CFNetwork\/758\.4|CFNetwork\/758\.3', 'iOS', '9.3'),
			array('CFNetwork\/758\.2', 'iOS', '9.2'),
			array('CFNetwork\/758', 'iOS', '9'),
			array('CFNetwork\/711', 'iOS', '8'),
			array('CFNetwork\/672', 'iOS', '7'),
			array('CFNetwork\/609|CFNetwork\/602', 'iOS', '6'),
			array('CFNetwork\/548', 'iOS', '5'),
			array('CFNetwork\/485', 'iOS', '4'),
			array('CFNetwork\/467', 'iOS', '3.2'),
			array('CFNetwork\/459', 'iOS', '3.1'),
			array('(?:CPU OS|iPh(?:one)?[ _]OS|iOS)[ _/](\d+(?:[_\.]\d+)*)', 'iOS', 1),
			array('(?:Apple-)?(?:iPhone|iPad|iPod)(?:.*Mac OS X.*Version/(\d+\.\d+)|; Opera)?', 'iOS', 1),
			array('Podcasts/(?:[\d\.]+)|Instacast(?:HD)?/(?:\d\.[\d\.abc]+)|Pocket Casts, iOS|Overcast|Castro|Podcat|i[cC]atcher', 'iOS', ''),
			array('iTunes-(iPod|iPad|iPhone)/(?:[\d\.]+)', 'iOS', '')
		);

		return $this->checkOs($useragent, $patterns);
	}

	/***********************/
	/* CHECK LINUX/UNIX OS */
	/***********************/
	private function checkOsLinux($useragent) {
		$pattern = 'Debian|Knoppix|Mint|Ubuntu|Kubuntu|Xubuntu|Lubuntu|Fedora|Red\sHat|Mandriva|Gentoo|Sabayon|Slackware|SUSE|CentOS|BackTrack';
		$matches = $this->matchUserAgent($useragent, $pattern);
		if ($matches) {
			$this->device['os_name'] = $matches[0];
			$this->device['os_version'] = '';
			return true;
		}

		$patterns = array(
			array('SunOS|Solaris', 'Solaris', ''),
			array('FreeBSD(?:[/ ]?(\d+[\.\d]+))?', 'FreeBSD', 1),
			array('OpenBSD(?:[/ ]?(\d+[\.\d]+))?', 'OpenBSD', 1),
			array('NetBSD(?:[/ ]?(\d+[\.\d]+))?', 'NetBSD', 1),
			array('DragonFly(?:[/ ]?(\d+[\.\d]+))?', 'DragonFly', 1),
			array('Linux(?:OS)?[^a-z]', 'Linux', '')
		);

		return $this->checkOs($useragent, $patterns);
	}


	/****************/
	/* CHECK MAC OS */
	/****************/
	private function checkOsBlackBerry($useragent) {
		$patterns = array(
			array('(?:BB10;.+Version|Black[Bb]erry[0-9a-z]+|Black[Bb]erry.+Version)/(\d+[\.\d]+)', 'BlackBerry OS', 1),
			array('RIM Tablet OS (\d+[\.\d]+)', 'BlackBerry Tablet OS', 1),
			array('RIM Tablet OS|QNX|Play[Bb]ook', 'BlackBerry Tablet OS', ''),
			array('BlackBerry', 'BlackBerry OS', '')
		);
		return $this->checkOs($useragent, $patterns);
	}


	/*********************************/
	/* CHECK OTHER OPERATING SYSTEMS */
	/*********************************/
	private function checkOsOther($useragent) {
		$patterns = array(
			array('(?:Mobile|Tablet);.+Firefox/\d+\.\d+', 'Firefox OS', ''),
			array('CrOS [a-z0-9_]+ (\d+[\.\d]+)', 'Chrome OS', 1),
			array('(?:webOS|Palm webOS)(?:/(\d+[\.\d]+))?', 'webOS', 1),
			array('(?:PalmOS|Palm OS)(?:[/ ](\d+[\.\d]+))?|Palm', 'palmOS', 1),
			array('Xiino(?:.*v\. (\d+[\.\d]+))?', 'palmOS', 1),
			array('MorphOS(?:[ /](\d+[\.\d]+))?', 'MorphOS', 1),
			array('Symbian|SymbOS', 'Symbian OS', ''),
			array('GoogleTV(?:[ /](\d+[\.\d]+))?', 'Google TV', 1),
			array('AppleTV(?:/?(\d+[\.\d]+))?', 'Apple TV', 1),
			array('WebTV/(\d+[\.\d]+)', 'WebTV', 1),
			array('AmigaOS|AmigaVoyager|Amiga-AWeb', 'AmigaOS', ''),
			array('Nintendo Wii', 'Nintendo', 'Wii'),
			array('PlayStation', 'PlayStation', ''),
			array('Xbox|KIN\.(?:One|Two)', 'Xbox', '360'),
			array('OS\/2', 'OS/2', ''),
			array('Sailfish|Jolla', 'Sailfish OS', ''),
			array('Tizen', 'Tizen', '')
		);
		return $this->checkOs($useragent, $patterns);
	}


	/**************************/
	/* CHECK OPERATING SYSTEM */
	/**************************/
	private function checkOs($useragent, $patterns) {
		if (!$patterns) { return false; }
		$found = false;
		foreach ($patterns as $pat) {
			$matches = $this->matchUserAgent($useragent, $pat[0]);
			if (!$matches) { continue; }
			$this->device['os_name'] = $pat[1];
			$this->device['os_version'] = $this->versionFromMatches($pat[2], $matches);
			$found = true;
			break;
		}
		return $found;
	}


	/*******************************************************/
	/* CHECK ALL OPERATING SYSTEMS WITH OPTIONAL EXCEPTION */
	/*******************************************************/
	private function checkAllOperatingSystems($useragent, $os_exception='') {
		$oses = array('Windows', 'Linux', 'Mac', 'Android', 'Ios', 'BlackBerry', 'Other');
		$found = false;
		foreach ($oses as $os) {
			if ($os == $os_exception) { continue; }
			$method = 'checkOs'.$os;
			$found = $this->$method($useragent);
			if ($found) { break; }
		}
		return $found;
	}


	/****************************/
	/* GET VERSION FROM MATCHES */
	/****************************/
	private function versionFromMatches($pattern_idx, $matches) {
		$version = '';
		if ($pattern_idx != '') {
			if (is_int($pattern_idx)) {
				$version = isset($matches[$pattern_idx]) ? $matches[$pattern_idx] : '';
				if (preg_match('/^[0-9\.]+$/', $version)) {
					$parts = explode('.', $version);
					$version = $parts[0];
					if (isset($parts[1])) { $version .= '.'.$parts[1]; }
				}
			} else {
				$version = $pattern_idx;
			}
			if (strlen($version) > 10) { $version = ''; }//wrong version => reset it
		}
		return $version;
	}


	/***********************************/
	/* CHECK IF A DEVICE IS A SMART TV */
	/***********************************/
	private function deviceIsTV($useragent) {
		$tv = false;
		$patterns = array('GoogleTV', 'SmartTV', 'Smart TV', 'SMART-TV', 'Android TV', 'GTV100', 'HbbTV', 'POV_TV-HDMI', '(TV;', '+SCREEN', 'Changhong', 'NETTV', 'AND1E', 'TSBNetTV');
		foreach ($patterns as $pat) {
			if (stripos($useragent, $pat) !== false) { $tv = true; break; }
		}
		return $tv;
	}


	/***************************************************/
	/* INDEPENDENT MOBILE CHECK (FASTER, MORE UPDATED) */
	/* Harald Hope, 5.4.12, http://techpatterns.com    */
	/***************************************************/
	public function isMobile($useragent='', $forcescan=false) {
		if ($useragent == '') {
			if (!isset($_SERVER['HTTP_USER_AGENT'])) { return false; }
			$useragent = $_SERVER['HTTP_USER_AGENT'];
		}
		if ($useragent == '') { return false; }

		if (!$forcescan) {
			if ($this->useragent == $useragent) {//fully analized previously
				return ($this->device['type'] == 'mobile') ? true : false;
			}			
		}

		if ($this->deviceIsTV($useragent)) { return false; }

		$pat1 = 'android|blackberry|linux\sarmv|palmos|palmsource|windows\sce|windows\sphone|iemobile|ddipocket|ipad|ipod|iphone|playstation|nintendo|samsung|sharp|zune|vodafone|smartphone|mobile|tablet|opera\smobi|opera\smini|fennec';
		$pat2 = 'HTC|NexusHD2|StarShine|StarTrail|STARADDICT|StarText|StarNaute|StarXtrem|StarTab|Nokia|Lumia|Maemo\sRX|portalmmm|Symbian|BB10;|PlayBook|Xiino|ViewSonic|ViewPad|ViewPhone|NetCast|SAGEM|j-phone|skyfire';
		$pat3 = 'Kindle|Huawei|EasyPad|EasyPhone|Elephone|TouchPad|webOS|hp-tablet|MicroMax|Alcatel|Xiaomi|Wexler|Walton|Walpad|Vodafone|ZTE|AxonPhone|Xenta|Smartfren|Androtab|Andromax|QMobile|QTab|Q-Smart|MobilePhone';
		$pat4 = 'Ericsson|Xperia|PadFone|BENQ|Bmobile|Turbo-X|SmartPad|Lenovo|IdeaTab|IdeaPad|Thinkpad|Yoga\sTablet|SlidePad|Garminfone|Capitel|Coolpad|Hasee|Hisense|Koobee|Kyocera|blazer|avantgo|reqwirelessweb|netfront';

		if (preg_match('#'.$pat1.'#i', $useragent, $matches)) { return true; }
		if (preg_match('#'.$pat2.'#i', $useragent, $matches)) { return true; }
		if (preg_match('#'.$pat3.'#i', $useragent, $matches)) { return true; }
		if (preg_match('#'.$pat4.'#i', $useragent, $matches)) { return true; }

		return false;
	}


	/**********************************/
	/* PATTERNS USED TO DETECT ROBOTS */
	/**********************************/
	private function getRobotPatterns() {
		$patterns = array(
			array('360Spider(-Image|-Video)?', '360Spider'),
			array('Aboundex', 'Aboundexbot'),
			array('AcoonBot', 'Acoon'),
			array('AddThis\.com', 'AddThis.com'),
			array('AhrefsBot', 'aHrefs Bot'),
			array('ia_archiver|alexabot|verifybot', 'Alexa Crawler'),
			array('AmorankSpider', 'Amorank Spider'),
			array('Applebot', 'Applebot'),
			array('Curious George', 'Analytics SEO Crawler'),
			array('archive\.org_bot|special_archiver', 'archive.org bot'),
			array('Ask Jeeves\/Teoma', 'Ask Jeeves'),
			array('Backlink-Ceck\.de', 'Backlink-Ceck.de'),
			array('BacklinkCrawler', 'BacklinkCrawler'),
			array('baiduspider(-image)?|baidu Transcoder|baidu.*spider', 'Baidu Spider'),
			array('BazQux', 'BazQux Reader'),
			array('MSNBot|msrbot|bingbot|BingPreview|msnbot-(UDiscovery|NewsBlogs)|adidxbot', 'BingBot'),
			array('Blekkobot', 'Blekkobot'),
			array('BLEXBot(Test)?', 'BLEXBot Crawler'),
			array('Bloglovin', 'Bloglovin'),
			array('Blogtrottr', 'Blogtrottr'),
			array('BountiiBot', 'Bountii Bot'),
			array('Browsershots', 'Browsershots'),
			array('BUbiNG', 'BUbiNG'),
			array('(?<!HTC)[ _]Butterfly\/', 'Butterfly Robot'),
			array('CareerBot', 'CareerBot'),
			array('CCBot', 'ccBot crawler'),
			array('Cliqzbot', 'Cliqzbot'),
			array('CloudFlare-AlwaysOnline', 'CloudFlare Always Online'),
			array('coccoc\/', 'Cốc Cốc Bot'),
			array('CommaFeed', 'CommaFeed'),
			array('Dataprovider', 'Dataprovider'),
			array('Daum(oa)?[ /][0-9]', 'Daum'),
			array('Dazoobot', 'Dazoobot'),
			array('discobot(-news)?', 'Discobot'),
			array('Domain Re-Animator Bot|support@domainreanimator.com', 'Domain Re-Animator Bot'),
			array('DotBot', 'DotBot'),
			array('EasouSpider', 'Easou Spider'),
			array('EMail Exractor', 'EMail Exractor'),
			array('Exabot(-Thumbnails|-Images)?|ExaleadCloudview', 'ExaBot'),
			array('ExactSeek Crawler', 'ExactSeek Crawler'),
			array('Ezooms', 'Ezooms'),
			array('facebookexternalhit|facebookplatform', 'Facebook External Hit'),
			array('Feedbin', 'Feedbin'),
			array('FeedBurner', 'FeedBurner'),
			array('Feed Wrangler', 'Feed Wrangler'),
			array('(Meta)?Feedly(Bot|App)?', 'Feedly'),
			array('Feedspot', 'Feedspot'),
			array('Fever\/[0-9]', 'Fever'),
			array('Genieo', 'Genieo Web filter'),
			array('Gluten\sFree\sCrawler', 'Gluten Free Crawler'),
			array('ichiro\/mobile goo', 'Goo'),
			array('Google Page Speed Insights', 'Google PageSpeed Insights'),
			array('google_partner_monitoring', 'Google Partner Monitoring'),
			array('via ggpht\.com GoogleImageProxy', 'Gmail Image Proxy'),
			array('SentiBot', 'SentiBot'),//before Googlebot
			array('Googlebot(-Mobile|-Image|-Video|-News)?|Feedfetcher-Google|Google-Test|Google-Site-Verification|Google\sWeb\sPreview|AdsBot-Google(-Mobile)?|Mediapartners-Google|Google.*/\+/web/snippet|GoogleProducer|Google[ -]Publisher[ -]Plugin', 'Googlebot'),
			array('heritrix', 'Heritrix'),
			array('HTTPMon', 'HTTPMon'),
			array('ICC-Crawler', 'ICC-Crawler'),
			array('iisbot', 'IIS Site Analysis'),
			array('kouio', 'Kouio'),
			array('larbin', 'Larbin web crawler'),
			//array('linkdexbot(-mobile)?|linkdex\.com', 'Linkdex Bot'),
			array('linkdexbot', 'Linkdex Bot'),
			array('LinkedInBot', 'LinkedIn Bot'),
			array('ltx71', 'LTX71'),
			array('Mail\.RU(_Bot)?', 'Mail.Ru Bot'),
			array('magpie-crawler', 'Magpie-Crawler'),
			array('MagpieRSS', 'MagpieRSS'),
			array('meanpathbot', 'Meanpath Bot'),
			array('MetaJobBot', 'MetaJobBot'),
			array('MixrankBot', 'Mixrank Bot'),
			array('MJ12bot', 'MJ12 Bot'),
			array('MojeekBot', 'MojeekBot'),
			array('NalezenCzBot', 'NalezenCzBot'),
			array('Netcraft (Web Server Survey|SSL Server Survey)', 'Netcraft Survey Bot'),
			array('Netvibes', 'Netvibes'),
			array('NewsBlur .*(Fetcher|Finder)', 'NewsBlur'),
			array('NewsGatorOnline', 'NewsGator'),
			array('nlcrawler', 'NLCrawler'),
			array('omgilibot', 'Omgili bot'),
			array('OpenindexSpider', 'Openindex Spider'),
			array('spbot', 'OpenLinkProfiler'),
			array('OpenWebSpider', 'OpenWebSpider'),
			array('OrangeBot|VoilaBot', 'Orange Bot'),
			array('PaperLiBot', 'PaperLiBot'),
			array('phpservermon', 'PHP Server Monitor'),
			array('psbot(-page)?', 'Picsearch bot'),
			array('Pingdom\.com', 'Pingdom Bot'),
			array('QuerySeekerSpider', 'QuerySeekerSpider'),
			array('Qwantify', 'Qwantify'),
			array('Rainmeter', 'Rainmeter'),
			array('redditbot', 'Reddit Bot'),
			array('rogerbot', 'Rogerbot'),
			array('ROI Hunter', 'ROI Hunter'),
			array('SafeDNSBot', 'SafeDNSBot'),
			array('Scrapy', 'Scrapy'),
			array('Screaming Frog SEO Spider', 'Screaming Frog SEO Spider'),
			array('ScreenerBot', 'ScreenerBot'),
			array('SemrushBot', 'Semrush Bot'),
			array('SensikaBot', 'Sensika Bot'),
			array('SEOENG(World)?Bot', 'SEOENGBot'),
			array('SkypeUriPreview', 'Skype URI Preview'),
			array('SeznamBot|SklikBot|Seznam screenshot-generator', 'Seznam Bot'),
			array('ShopWiki', 'ShopWiki'),
			array('SilverReader', 'SilverReader'),
			array('SimplePie', 'SimplePie'),
			array('SISTRIX Crawler', 'SISTRIX Crawler'),
			array('sixy.ch', 'Sixy.ch'),
			array('Slackbot|Slack-ImgProxy', 'Slackbot'),
			array('(Sogou (web|inst|Pic) spider)|New-Sogou-Spider', 'Sogou Spider'),
			array('Sosospider|Sosoimagespider', 'Soso Spider'),
			array('Superfeedr bot', 'Superfeedr Bot'),
			array('Spinn3r', 'Spinn3r'),
			array('Sputnik(Image)?Bot', 'Sputnik Bot'),
			array('SurveyBot', 'Survey Bot'),
			array('TelegramBot', 'TelgramBot'),
			array('TinEye-bot', 'TinEye Crawler'),
			array('Tiny Tiny RSS', 'Tiny Tiny RSS'),
			array('TurnitinBot', 'TurnitinBot'),
			array('TweetedTimes Bot', 'TweetedTimes Bot'),
			array('TweetmemeBot', 'Tweetmeme Bot'),
			array('Twitterbot', 'Twitterbot'),
			array('UptimeRobot', 'Uptime Robot'),
			array('URLAppendBot', 'URLAppendBot'),
			array('VSMCrawler', 'Visual Site Mapper Crawler'),
			array('Jigsaw', 'W3C CSS Validator'),
			array('W3C_I18n-Checker', 'W3C I18N Checker'),
			array('W3C-checklink', 'W3C Link Checker'),
			array('W3C_Validator', 'W3C Markup Validation Service'),
			array('W3C-mobileOK', 'W3C MobileOK Checker'),
			array('W3C_Unicorn', 'W3C Unified Validator'),
			array('WeSEE(:Search)?', 'WeSEE:Search'),
			array('WebbCrawler', 'WebbCrawler'),
			array('websitepulse[+ ]checker', 'WebSitePulse'),
			array('Wotbox', 'Wotbox'),
			array('yacybot', 'YaCy'),
			array('Yahoo! Slurp|Yahoo!-AdCrawler', 'Yahoo! Slurp'),
			array('Yahoo Link Preview|Yahoo:LinkExpander:Slingstone', 'Yahoo! Link Preview'),
			array('YahooCacheSystem', 'Yahoo! Cache System'),
			array('Yandex(SpravBot|ScreenshotBot|MobileBot|AccessibilityBot|ForDomain|Vertis|Market|Catalog|Calendar|Sitelinks|AdNet|Pagechecker|Webmaster|Media|Video|Bot|Images|Antivirus|Direct|Blogs|Favicons|ImageResizer|News(links)?|Metrika|\.Gazeta Bot)|YaDirectFetcher', 'Yandex Bot'),
			array('Yeti', 'Yeti/Naverbot'),
			array('YoudaoBot', 'Youdao Bot'),
			array('YOURLS v[0-9]', 'Yourls'),
			array('YRSpider|YYSpider', 'Yunyun Bot'),
			array('Zookabot', 'Zookabot'),
			array('ZumBot', 'ZumBot'),
			array('YottaaMonitor', 'Yottaa Site Monitor'),
			array('Yahoo Ad monitoring.*yahoo-ad-monitoring-SLN24857.*', 'Yahoo Gemini'),
			array('.*Java.*outbrain', 'Outbrain'),
			array('HubPages.*crawlingpolicy', 'HubPages'),
			array('Pinterest\/\d\.\d.*www\.pinterest\.com.*', 'Pinterest'),
			array('Site24x7', 'Site24x7 Website Monitoring'),
			array('www\.monitor\.us', 'Monitor.Us'),
			array('Catchpoint( bot)?', 'Catchpoint'),
			array('Zao\/', 'Zao'),
			array('lycos', 'Lycos'),
			array('Slurp', 'Inktomi Slurp'),
			array('Speedy Spider', 'Speedy'),
			array('ScoutJet', 'ScoutJet'),
			array('nrsbot|netresearch', 'NetResearchServer'),
			array('scooter', 'Scooter'),
			array('gigabot', 'Gigabot'),
			array('CatchBot', 'CatchBot'),
			array('charlotte', 'Charlotte'),
			array('Pompos', 'Pompos'),
			array('ichiro', 'ichiro'),
			array('PagePeeker', 'PagePeeker'),
			array('WebThumbnail', 'WebThumbnail'),
			array('Willow Internet Crawler', 'Willow Internet Crawler'),
			array('EmailWolf', 'EmailWolf'),
			array('NetLyzer FastProbe', 'NetLyzer FastProbe'),
			array('AdMantX.*admantx\.com', 'ADMantX'),
			array('Server Density Service Monitoring.*', 'Server Density'),
			array('(A6-Indexer|nuhk|TsolCrawler|Yammybot|Openbot|Gulper Web Bot|grub-client|Download Demon|SearchExpress|Microsoft URL Control|borg|altavista|teoma|blitzbot|oegp|furlbot|http%20client|polybot|htdig|mogimogi|larbin|scrubby|searchsight|seekbot|semanticdiscovery|snappy|vortex(?! Build)|zeal|fast-webcrawler|converacrawler|dataparksearch|findlinks|BrowserMob|HttpMonitor|ThumbShotsBot|URL2PNG|ZooShot|GomezA|Google SketchUp|Read%20Later|Minimo|RackspaceBot)', 'Generic Bot'),
			array('Nutch', 'Nutch-based Bot'),
			array('YisouSpider', 'Yisou Spider'),
			array('Bumble\sBee|WikiApiary', 'Bumble Bee'),
			array('yoozBot|vebidoobot|DomainStatsBot|RSSingBot|CISPA\sVulnerability', 'Generic Bot'),
			array('[a-z0-9\-_]*((?<!cu)bot|crawler|archiver|transcoder|spider)([^a-z]|$)', 'Generic Bot')
		);

		return $patterns;
	}


	/************************************************/
	/* PATTERNS USED TO DETECT RSS/ATOM AGGREGATORS */
	/************************************************/
	private function getFeedPatterns() {
		$patterns = array(
			array('AideRSS', 'AideRSS'),
			array('Akregator', 'Akregator'),
			array('Apple-PubSub', 'Apple PubSub'),
			array('AppleSyndication', 'Safari RSS reader'),
			array('BlogBridge', 'BlogBridge'),
			array('Bloglines', 'Bloglines'),
			array('BashPodder', 'BashPodder'),
			array('GreatNews', 'GreatNews'),
			array('NetNewsWire', 'NetNewsWire'),
			array('NewsFire', 'NewsFire'),
			array('NewzCrawler', 'NewzCrawler'),
			array('ReadKit', 'ReadKit'),
			array('RSSOwl', 'RSSOwl'),
			array('RSSBandit', 'RSS Bandit'),
			array('RSS\sJunkie', 'RSS Junkie'),
			array('RSS\sMenu', 'RSS Menu'),
			array('NewsBlur', 'NewsBlur'),
			array('NewsFox', 'NewsFox'),
			array('Feeddler', 'Feeddler'),
			array('Downcast', 'Downcast'),
			array('FeedDemon', 'FeedDemon'),
			array('Feedfetcher', 'Feedfetcher'),
			array('Liferea', 'Liferea'),
			array('JetBrains\sOmea\sReader', 'JetBrains Omea Reader'),
			array('Netvibes', 'Netvibes feed reader'),
			array('Windows-RSS-Platform', 'IE RSS reader'),
			array('YahooFeedSeeker', 'Yahoo Feed Seeker')
		);

		return $patterns;
	}

}

?>