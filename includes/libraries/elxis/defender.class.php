<?php 
/**
* @version		$Id: defender.class.php 2403 2021-04-10 17:15:42Z IOS $
* @package		Elxis
* @copyright	Copyright (c) 2006-2021 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


$defstart = microtime(true);

if (isset($_SERVER['QUERY_STRING'])) {
	if (strpos(urldecode($_SERVER['QUERY_STRING']), chr(0)) !== false) {
		exitPage::make('security', 'DEF-0001');
	}
    if (preg_match("#((\%0d)|(\%0a)|(\\\r)|(\\\n))#", $_SERVER['QUERY_STRING'])) {
    	exitPage::make('security', 'DEF-0002', 'Possible CRLF injection/HTTP response split.');
    }
}

if (!empty($_COOKIE)) {
    if (preg_match("#((\%0d)|(\%0a)|(\\\r)|(\\\n))#", serialize($_COOKIE))) {
    	exitPage::make('security', 'DEF-0003', 'Possible CRLF injection/HTTP response split.');
    }
}


class elxisDefender {

	private $cfg = NULL;
	private $types = array();
	private $repo_path = '';
	private $query = '';
	private $requesturi = '';
	private $address = '';
	private $host = '';
	private $referer = '';
	private $useragent = '';
	private $rawpost = '';
	private $bantimes = 3;
	private $banmessage = '';
	private $ipcheckafter = false;
	private $triggered_rule = '';
	private $banduration = 864000;//in seconds, by default 10 days


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		elxisLoader::loadFile('configuration.php');
		$this->cfg = new elxisConfig();

		$this->repo_path = rtrim($this->cfg->get('REPO_PATH'), '/');
		if ($this->repo_path == '') { $this->repo_path = ELXIS_PATH.'/repository'; }
		if (!file_exists($this->repo_path.'/logs/') || !is_dir($this->repo_path.'/logs/')) {
			die('Folder logs in Elxis repository does not exist!');
		}

		if ($this->cfg->get('SECURITY_LEVEL', 0) > 0) {
			$this->ipcheckafter = false;
		} else {
			$this->ipcheckafter = ($this->cfg->get('DEFENDER_IPAFTER', 1) == 1) ? true : false;
		}

		$this->setTypes();
		if (count($this->types) == 0) { return; }

		if (isset($_SERVER['QUERY_STRING'])) {
			$this->query = strtolower(urldecode($_SERVER['QUERY_STRING']));
			$this->query = htmlspecialchars_decode($this->query, ENT_QUOTES);
		}

		$this->requesturi = strtolower(urldecode($this->getURI()));
		$this->requesturi = htmlspecialchars_decode($this->requesturi, ENT_QUOTES);

		$this->address = $this->getIP();
		if ($this->address == '') {
			$this->securityLogger(true, 'DEFB-0002', 'Empty IP address', 'IP');
			exitPage::make('security', 'DEFB-0002', 'Your IP address could not be detected!');
		}

		if (strpos($this->address, ':') !== false) { //IPv6
			if (filter_var($this->address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === false) {
				$this->securityLogger(true, 'DEFB-0003', 'Invalid IP v6 address', 'IP');
				exitPage::make('security', 'DEFB-0003', 'Your IP address is not valid IPv6!');
			}
		} else { //IPv4
			if (filter_var($this->address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === false) {
				$this->securityLogger(true, 'DEFB-0004', 'Invalid IP v4 address', 'IP');
				exitPage::make('security', 'DEFB-0004', 'Your IP address is not valid IPv4!');
			}
		}

		if (function_exists('gethostbyaddr') && is_callable('gethostbyaddr')) {
			$this->host = strtolower(gethostbyaddr($this->address)); 
		}

		if (isset($_SERVER['HTTP_REFERER'])) {
			$this->referer = strtolower($_SERVER['HTTP_REFERER']);
		}

		if (isset($_SERVER['HTTP_USER_AGENT'])) {
			$this->useragent = strtolower(urldecode($_SERVER['HTTP_USER_AGENT']));
			$this->useragent = htmlspecialchars_decode($this->useragent, ENT_QUOTES);
		}

		if (isset($_SERVER['REQUEST_METHOD'])) {
			if (($_SERVER['REQUEST_METHOD'] == 'PUT') || ($_SERVER['REQUEST_METHOD'] == 'DELETE') || ($_SERVER['REQUEST_METHOD'] == 'TRACE')) {
				$msg = 'Request method '.$_SERVER['REQUEST_METHOD'].' is not allowed';
				$this->securityLogger(true, 'DEFB-0005', $msg, 'OTHER');
				exitPage::make('security', 'DEFB-0005', $msg);
			}
		}

		if (!empty($_POST)) {
			$this->rawpost = file_get_contents('php://input');
			$this->rawpost = strtolower(substr($this->rawpost, 0, 4194304));
		}
	}


	/***************************/
	/* PERFORM SECURITY CHECKS */
	/***************************/
	public function check() {
		if (count($this->types) == 0) { return; }

		$this->checkBanned();

		if ($this->cfg->get('DEFENDER_WHITELIST', '') != '') {
			$parts = explode(',', $this->cfg->get('DEFENDER_WHITELIST', ''));
			if (in_array($this->address, $parts)) { return; }
		}

		if ($this->cfg->get('SECURITY_LEVEL', 0) > 0) {
			if (isset($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == 'POST')) {
				if ($this->referer == '') {//If POST, then there must be a referer! 
					//Be careful: causes problems on third party services like Paypal IPN
					$this->securityLogger(true, 'DEFB-0006', 'Empty HTTP REFERER on POST request', 'POST');
					exitPage::make('security', 'DEFB-0006', 'Empty HTTP REFERER on POST request!');
				}
			}

			if ($this->useragent == '') {
				$this->securityLogger(true, 'DEFB-0007', 'Empty user agent!', 'AGENT');
				exitPage::make('security', 'DEFB-0007', 'Empty user agent!');
			}
		}

		if (!$this->ipcheckafter) {
			if (in_array('R', $this->types)) {//IP RANGES
				$block = $this->checkAutoIPRanges();
				if ($block) {
					$this->banIP('DEFR-AUTO');
					$this->securityLogger(true, 'DEFR-AUTO', 'Blacklisted IP banned. Checked using IP ranges.', 'IP');
					if ($this->cfg->get('DEFENDER_NOTIFY') == 2) {
						$mailmsg = "IP address ".$this->address." belongs to a blacklisted IPs range!\r\n";
						$mailmsg .= "The list of blacklisted IP ranges is updated automatically every 24 hours.\r\n";
						$this->sendAlert('SEC-DEFR-AUTO', $mailmsg, 1);
					}
					exitPage::make('security', 'DEFR-AUTO', 'Your IP address is blacklisted!');
				}
				unset($block);
			}

			if (in_array('I', $this->types)) {//SPECIFIC IPs
				$block = $this->checkAutoIP();
				if ($block) {
					$this->banIP('DEFI-AUTO');
					$this->securityLogger(true, 'DEFI-AUTO', 'Blacklisted IP banned. Checked using IPs list.', 'IP');
					if ($this->cfg->get('DEFENDER_NOTIFY') == 2) {
						$mailmsg = "IP address ".$this->address." is blacklisted!\r\n";
						$mailmsg .= "The list of blacklisted IPs is updated automatically every 12 hours.\r\n";
						$this->sendAlert('SEC-DEFI-AUTO', $mailmsg, 1);
					}
					exitPage::make('security', 'DEFI-AUTO', 'Your IP address is blacklisted!');
				}
				unset($block);
			}
		}

		//Check for ascii chars 0-31 and 127
		$pat = '@[\x00-\x1F\x7F]@u';
		//For POST and USER AGENT exclude Horizontal Tab, Line Feed and Carriage Return 
		$patpost = '@[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]@u';
		$block = false;
		$blockmsg = '';
		$gvar = '';
		if (!empty($_GET)) {
			foreach ($_GET as $k => $v) {
				if (is_array($v)) {
					foreach ($v as $ka => $va) {
						if (is_numeric($va)) { continue; }
						if (is_array($va)) {
							$testva = '';
							array_walk_recursive($va, function($xva, $xk) use (&$testva) { $testva .= $xva.','; });
						} else {
							$testva = $va;
						}
						$v2 = preg_replace($pat, '', $testva);
						if (strcmp($v2, $testva) <> 0) {
							$block = true;
							$gvar = 'GET';
							$blockmsg = 'Unacceptable ASCII character in '.$gvar;
							break 2;
						}
					}
				} else {
					if (is_numeric($v)) { continue; }
					$v2 = preg_replace($pat, '', $v);
					if (strcmp($v2, $v) <> 0) {
						$block = true;
						$gvar = 'GET';
						$blockmsg = 'Unacceptable ASCII character in '.$gvar;
						break;
					}
				}
			}
		}

		if (!$block && !empty($_POST)) {
			foreach ($_POST as $k => $v) {
				if (is_array($v)) {
					foreach ($v as $ka => $va) {
						if (is_numeric($va)) { continue; }
						if (is_array($va)) {
							$testva = '';
							array_walk_recursive($va, function($xva, $xk) use (&$testva) { $testva .= $xva.','; });
						} else {
							$testva = $va;
						}
						$v2 = preg_replace($patpost, '', $testva);
						if (strcmp($v2, $testva) <> 0) {
							$block = true;
							$gvar = 'POST';
							$blockmsg = 'Unacceptable ASCII character in '.$gvar;
							break 2;
						}
					}
				} else {
					if (is_numeric($v)) { continue; }
					$v2 = preg_replace($patpost, '', $v);
					if (strcmp($v2, $v) <> 0) {
						$block = true;
						$gvar = 'POST';
						$blockmsg = 'Unacceptable ASCII character in '.$gvar;
						break;
					}					
				}
			}
		}

		if (!$block && ($this->useragent != '')) {
			$v2 = preg_replace($patpost, '', $this->useragent);
			if (strcmp($v2, $this->useragent) <> 0) {
				$block = true;
				$gvar = 'USER AGENT';
				$blockmsg = 'Unacceptable ASCII character in '.$gvar;
			}
		}

		if ($block) {
			$this->banIP('DEFB-0008');
			$this->securityLogger(true, 'DEFB-0008', $blockmsg, $gvar);
			if ($this->cfg->get('DEFENDER_NOTIFY') > 0) {
				$mailmsg = "Elxis Defender blocked an attack to your site!\r\n";
				$mailmsg .= "Elxis Defender report\r\n";
				$mailmsg .= $blockmsg;
				$this->sendAlert('SEC-DEFB-0008', $mailmsg, 1);
			}
			exitPage::make('security', 'DEFB-0008', $blockmsg);
		}
		unset($pat, $patpost, $block, $gvar, $blockmsg);

		foreach ($this->types as $type) {
			switch($type) {
				case 'G': $this->checkRules('general'); break;
				case 'C': $this->checkRules('custom'); break;
				case 'F': $this->checkFS(); break;
				case 'I': break; //checked previously with checkIP
				case 'R': break; //checked previously with checkIPRanges
				default: break;
			}
		}
	}


	/***********************************************************/
	/* RE-CHECK ONLY FOR AUTO IPS AFTER SESSION HAS INITIALIZE */
	/***********************************************************/
	public function reCheck() {
		if (!class_exists('elxisSession', false)) { return; }
		if (!$this->ipcheckafter) { return; }

		if ($this->cfg->get('DEFENDER_WHITELIST', '') != '') {
			$parts = explode(',', $this->cfg->get('DEFENDER_WHITELIST', ''));
			if (in_array($this->address, $parts)) { return; }
		}

		$eSession = eFactory::getSession();
		$checked = (int)$eSession->get('elxisdefips', '0');
		if ($checked == 1) { return; }

		$eSession->set('elxisdefips', '1');

		if (in_array('R', $this->types)) {//IP RANGES
			$block = $this->checkAutoIPRanges();
			if ($block) {
				$this->banIP('DEFR-AUTO');
				$this->securityLogger(true, 'DEFR-AUTO', 'Blacklisted IP banned. Checked using IP ranges.', 'IP');
				if ($this->cfg->get('DEFENDER_NOTIFY') == 2) {
					$mailmsg = "IP address ".$this->address." belongs to a blacklisted IPs range!\r\n";
					$mailmsg .= "The list of blacklisted IP ranges is updated automatically every 24 hours.\r\n";
					$this->sendAlert('SEC-DEFR-AUTO', $mailmsg, 1);
				}
				exitPage::make('security', 'DEFR-AUTO', 'Your IP address is blacklisted!');
			}
			unset($block);
		}

		if (in_array('I', $this->types)) {//SPECIFIC IPs
			$block = $this->checkAutoIP();
			if ($block) {
				$this->banIP('DEFI-AUTO');
				$this->securityLogger(true, 'DEFI-AUTO', 'Blacklisted IP banned. Checked using IPs list.', 'IP');
				if ($this->cfg->get('DEFENDER_NOTIFY') == 2) {
					$mailmsg = "IP address ".$this->address." is blacklisted!\r\n";
					$mailmsg .= "The list of blacklisted IPs is updated automatically every 12 hours.\r\n";
					$this->sendAlert('SEC-DEFI-AUTO', $mailmsg, 1);
				}
				exitPage::make('security', 'DEFI-AUTO', 'Your IP address is blacklisted!');
			}
			unset($block);
		}
	}


	/*******************/
	/* SET CHECK TYPES */
	/*******************/
	private function setTypes() {
		if (trim($this->cfg->get('DEFENDER')) != '') {
			$this->types = str_split($this->cfg->get('DEFENDER'));
			if (!in_array('C', $this->types)) { $this->types[] = 'C'; }
		}

		switch ($this->cfg->get('SECURITY_LEVEL')) {
			case 1:
				if (!in_array('G', $this->types)) { $this->types[] = 'G'; }
				if (!in_array('C', $this->types)) { $this->types[] = 'C'; }
				if (!in_array('F', $this->types)) { $this->types[] = 'F'; }
			break;
			case 2:
				if (!in_array('G', $this->types)) { $this->types[] = 'G'; }
				if (!in_array('C', $this->types)) { $this->types[] = 'C'; }
				if (!in_array('I', $this->types)) { $this->types[] = 'I'; }
				if (!in_array('R', $this->types)) { $this->types[] = 'R'; }
				if (!in_array('F', $this->types)) { $this->types[] = 'F'; }
			break;
			case 0: default: break;
		}

		if (!$this->types) { return; }

		if (in_array('R', $this->types)) {
			$update_ips_db = true;
			if (file_exists($this->repo_path.'/logs/defender_ip_ranges.php')) {
				$ts = filemtime($this->repo_path.'/logs/defender_ip_ranges.php');
				if ((time() - $ts) < 86400) { $update_ips_db = false; } //once per day
			} else {
				$fp = @fopen($this->repo_path.'/logs/defender_ip_ranges.php', 'w');
				if ($fp) {
					$ok = false;
					if (flock($fp, LOCK_EX)) {
						$ok = fwrite($fp, $txt);
						flock($fp, LOCK_UN);
					}
					fclose($fp);
					if ($ok) {
						$this->securityLogger(false, '', 'Bad IP ranges database updated successfully', 'UPDATE');
					} else {
						$this->securityLogger(false, '', 'Bad IP ranges database update FAILED', 'UPDATE');
					}
				} else {
					die('Could not create file logs/defender_ip_ranges.php in Elxis repository!');
				}
			}
			if ($update_ips_db) { $this->updateIPDB(true); }
		}

		if (in_array('I', $this->types)) {
			$update_ips_db = true;
			if (file_exists($this->repo_path.'/logs/defender_ips.php')) {
				$ts = filemtime($this->repo_path.'/logs/defender_ips.php');
				if ((time() - $ts) < 43200) { $update_ips_db = false; } //twice per day
			} else {
				$fp = @fopen($this->repo_path.'/logs/defender_ips.php', 'w');
				if ($fp) {
					$ok = false;
					if (flock($fp, LOCK_EX)) {
						$ok = fwrite($fp, $txt);
						flock($fp, LOCK_UN);
					}
					fclose($fp);
					if ($ok) {
						$this->securityLogger(false, '', 'Bad IPs list updated successfully', 'UPDATE');
					} else {
						$this->securityLogger(false, '', 'Bad IPs list update FAILED', 'UPDATE');
					}
				} else {
					die('Could not create file logs/defender_ips.php in Elxis repository!');
				}
			}
			if ($update_ips_db) { $this->updateIPDB(false); }
		}
	}


	/******************************/
	/* FILESYSTEM INTEGRITY CHECK */
	/******************************/
	private function checkFS() {
		$files = $this->getLockFiles();
		$hashfile = $this->repo_path.'/other/elxis_hashes_'.md5($this->cfg->get('ENCRYPT_KEY')).'.php';
		if (!file_exists($hashfile)) {
			$buffer = '<?php '._LEND._LEND;
			$buffer .= '//Elxis Defender - Filesystem hash fingerprint generated on '.gmdate('Y-m-d H:i:s').' (UTC)'._LEND._LEND;
			$buffer .= 'defined(\'_ELXIS_\') or die (\'Protected by Elxis Defender\');'._LEND._LEND;
			$buffer .= '$hashes = array('._LEND;
			foreach ($files as $file) {
				$m = md5_file(ELXIS_PATH.'/'.$file);
				$buffer.= "\t".'array(\''.$file.'\', \''.$m.'\'),'._LEND;
			}
			$buffer .= ');'._LEND._LEND;
			$buffer .= '?>';

			$ok = false;
			if ($handler = @fopen($hashfile, 'w')) {
				if (flock($handler, LOCK_EX)) {
					$ok = @fwrite($handler, $buffer);
					flock($handler, LOCK_UN);
				}
            	fclose($handler);
        	}
			if (!$ok) {
				$msg = 'Elxis Defender could not save fingerprint (hashes) file! Please make sure Elxis repository is writable.';
				exitPage::make('fatal', 'DEFF-0001', $msg);
			}
        	return true;
		}

		include($hashfile);
		if (!isset($hashes) || !is_array($hashes) || (count($hashes) == 0)) {
			$msg = 'Elxis Defender detected an empty or invalid fingerprint (hashes) file! If you dont 
			know what to do consider Elxis documentation or visit Elxis forums for support.';
			exitPage::make('fatal', 'DEFF-0002', $msg);
		}

		$i = 1;
		foreach ($hashes as $hash) {
			$f = $hash[0];
			if (!in_array($f, $files)) {
				$n = sprintf("%04d", $i);
				$this->securityLogger(true, 'DEFF-'.$n, 'A protected file has been deleted! '.$f, 'FSDEL');
				if ($this->cfg->get('DEFENDER_NOTIFY') > 0) {
					$mailmsg = 'A protected file has been deleted!'."\r\n";
					$mailmsg .= 'The deleted file is: '.$f."\r\n\r\n";
					$mailmsg .= 'Actions to perform (pick one):'."\r\n";
					$mailmsg .= '1. If the deletion made by an unauthorized person, or accidently by you, restore the original protected file.'."\r\n";
					$mailmsg .= '2. If you accept this deletion then you must delete the Elxis hashes file in order for the Elxis Defender to regenarate it without the deleted file.'."\r\n\r\n";
					$mailmsg .= "Elxis Defender hashes file: ".$hashfile."\r\n";
					$mailmsg .= "The site wont come back online until you perform one of the above actions.\r\n"; 
					$this->sendAlert('SEC-DEFF-'.$n, $mailmsg, 0);
				}
				$msg = 'A protected file has been deleted! If you dont know what 
				to do consider Elxis documentation or visit Elxis forums for support.';
				exitPage::make('security', 'DEFF-'.$n, $msg);
			}
			$m = md5_file(ELXIS_PATH.'/'.$f);
			if ($m != $hash[1]) {
				$n = sprintf("%04d", $i);
				$this->securityLogger(true, 'DEFF-'.$n, 'A protected file has been modified! '.$f, 'FSLOCK');
				if ($this->cfg->get('DEFENDER_NOTIFY') > 0) {
					$mailmsg = 'A protected file has been modified!'."\r\n";
					$mailmsg .= 'The modified file is: '.$f."\r\n\r\n";
					$mailmsg .= 'Actions to perform (pick one):'."\r\n";
					$mailmsg .= '1. If the modification made by an unauthorized person, or accidently by you, restore the original protected file.'."\r\n";
					$mailmsg .= '2. If you accept this modification then you must delete the Elxis hashes file in order for the Elxis Defender to regenarate it without the modified file.'."\r\n\r\n";
					$mailmsg .= "Elxis Defender hashes file: ".$hashfile."\r\n";
					$mailmsg .= "The site wont come back online until you perform one of the above actions.\r\n"; 
					$this->sendAlert('SEC-DEFF-'.$n, $mailmsg, 0);
				}
				$msg = 'A protected file has been modified! If you dont know what 
				to do consider Elxis documentation or visit Elxis forums for support.';
				exitPage::make('security', 'DEFF-'.$n, $msg);
			}
			$i++;
		}
	}


	/*******************************************/
	/* CHECK IF IP IS BANNED BY ELXIS DEFENDER */
	/*******************************************/
	private function checkBanned() {
		if ($this->address == '') { return; }
		$file = $this->repo_path.'/logs/defender_ban.php';
		if (!file_exists($file)) { return; }
		include($file);
		if (!isset($ban) || !is_array($ban) || (count($ban) == 0)) { return; }
		$ip = str_replace('.', 'x', $this->address);
		$ip = str_replace(':', 'y', $ip);
		if (isset($ban[$ip])) {
			if ($ban[$ip]['times'] >= $this->bantimes) {
				$msg = 'You have been banned! If you think this is wrong contact the site administrator.';
				exitPage::make('security', 'DEFB-0001', $msg);
			}
		}
	}


	/************************/
	/* CHECK BLOCKING RULES */
	/************************/
	private function checkRules($type) {
		if (!file_exists(ELXIS_PATH.'/includes/libraries/elxis/defender/'.$type.'.rules.php')) { return; }
		include(ELXIS_PATH.'/includes/libraries/elxis/defender/'.$type.'.rules.php');
		if (!isset($rules) || !is_array($rules) || (count($rules) == 0)) { return; }

		$this->triggered_rule = '';

		foreach ($rules as $k => $rule) {
			$methods = explode(',', $rule[0]);
			foreach ($methods as $method) {
				$func = 'check'.$method;
				if ($this->$func($rule[1])) {
					$char = strtoupper(substr($type, 0, 1));
					$n = sprintf("%04d", $k);
					$this->banIP('DEF'.$char.'-'.$n);
					if ($this->triggered_rule != '') {
						$logtxt = 'Request blocked, Method: '.$method.', Rule: '.$this->triggered_rule.', Reason: '.$rule[2];
					} else {
						$logtxt = 'Request blocked, Method: '.$method.', Reason: '.$rule[2];
					}
					$this->securityLogger(true, 'DEF'.$char.'-'.$n, $logtxt, $method);

					$notify = false;
					if ($this->cfg->get('DEFENDER_NOTIFY') == 2) {
						$notify = true;
					} elseif ($this->cfg->get('DEFENDER_NOTIFY') == 1) {
						if (!in_array($method, array('AGENT', 'HOST', 'IP'))) {
							$notify = true;
						}
					}

					if ($notify) {
						$mailmsg = "Rules: \t".$type."\r\n";
						$mailmsg .= "Match where: \t".$method."\r\n";
						$mailmsg .= "Regex match number: \t".($k+1)."\r\n";
						if ($this->triggered_rule != '') {
							$mailmsg .= "Match rule: \t".$this->triggered_rule."\r\n";
						}
						$mailmsg .= "Reason: \t".$rule[2]."\r\n";
						if ($this->banmessage != '') { $mailmsg .= $this->banmessage."\r\n"; }
						$this->sendAlert('SEC-DEF'.$char.'-'.$n, $mailmsg, 1);
					}
					exitPage::make('security', 'DEF'.$char.'-'.$n, $rule[2]);
				}
			}
		}
	}


	/* CHECK POST DATA */
	private function checkPOST($rule) {
		if ($this->rawpost == '') { return false; }
		if (preg_match('~'. $rule .'~', $this->rawpost)) {
			preg_match('~'. $rule .'~', $this->rawpost, $matches);
			if (isset($matches[0]) && ($matches[0] != '')) { $this->triggered_rule = $matches[0]; }
			return true;
		}
		return false;
	}


	private function checkSESSION($rule) {
		return false;//cannot check session before Elxis init
	}


	private function checkCOOKIE($rule) {
		return false;//cannot check cookie
	}


	private function checkURI($rule) {
		if ($this->requesturi == '') { return false; }
		if (preg_match('~'. $rule .'~', $this->requesturi)) {
			preg_match('~'. $rule .'~', $this->requesturi, $matches);
			if (isset($matches[0]) && ($matches[0] != '')) { $this->triggered_rule = $matches[0]; }
			return true;
		}
		return false;
	}


	private function checkREFERER($rule) {
		if ($this->referer == '') { return false; }
		if (preg_match('~'. $rule .'~', $this->referer)) {
			preg_match('~'. $rule .'~', $this->referer, $matches);
			if (isset($matches[0]) && ($matches[0] != '')) { $this->triggered_rule = $matches[0]; }
			return true;
		}
		return false;
	}


	private function checkAGENT($rule) {
		if ($this->useragent == '') { return false; }
		if (preg_match('~'. $rule .'~', $this->useragent)) {
			preg_match('~'. $rule .'~', $this->useragent, $matches);
			if (isset($matches[0]) && ($matches[0] != '')) { $this->triggered_rule = $matches[0]; }
			return true;
		}
		return false;
	}


	private function checkQUERY($rule) {
		if ($this->query == '') { return false; }
		if (preg_match('~'. $rule .'~', $this->query)) {
			preg_match('~'. $rule .'~', $this->query, $matches);
			if (isset($matches[0]) && ($matches[0] != '')) { $this->triggered_rule = $matches[0]; }
			return true;
		}
		return false;
	}


	/***********************/
	/* ALIAS OF checkQUERY */
	/***********************/
	private function checkGET($rule) {
		return $this->checkQUERY($rule);
	}


	private function checkHOST($rule) {
		if ($this->host == '') { return false; }
		if (preg_match('~'. $rule .'~', $this->host)) {
			preg_match('~'. $rule .'~', $this->host, $matches);
			if (isset($matches[0]) && ($matches[0] != '')) { $this->triggered_rule = $matches[0]; }
			return true;
		}
		return false;
	}


	private function checkIP($rule) {
		if ($this->address == '') { return false; }
		if (preg_match('~'. $rule .'~', $this->address)) {
			preg_match('~'. $rule .'~', $this->address, $matches);
			if (isset($matches[0]) && ($matches[0] != '')) { $this->triggered_rule = $matches[0]; }
			return true;
		}
		return false;
	}


	/****************************************/
	/* CHECK IP AGAINST DANGEROUS IP RANGES */
	/****************************************/
	private function checkAutoIPRanges() {
		if (strpos($this->address, ':') !== false) { return; } //not supported IP v6
		@include($this->repo_path.'/logs/defender_ip_ranges.php');
		if (!isset($ips) || !is_array($ips) || (count($ips) == 0)) { return; }

		$userip = sprintf('%u', ip2long($this->address));
		$block = false;
		foreach ($ips as $ip) {
			if (($userip >= $ip[2]) && ($userip <= $ip[3])) {
				$block = true;
				break;
			}
		}
		return $block;
    }


	/**********************************/
	/* CHECK IP AGAINST DANGEROUS IPS */
	/**********************************/
	private function checkAutoIP() {
		@include($this->repo_path.'/logs/defender_ips.php');
		if (!isset($ips) || !is_array($ips) || (count($ips) == 0)) { return; }

		$block = false;
		foreach ($ips as $ip) {
			if ($ip == $this->address) {
				$block = true;
				break;
			}
		}
		return $block;
    }


	/*******************/
	/* SEND MAIL ALERT */
	/*******************/
	private function sendAlert($code, $msg='', $attack=1) {
		$file = $this->repo_path.'/logs/defender_notify.txt';
		@clearstatcache();
		if (!file_exists($file)) {
			@touch($file);
			$proceed = true;
		} else {
			$last = filemtime($file);
			if ((time() - filemtime($file)) > 300) {
				@touch($file);
				$proceed = true;
			} else {
				$proceed = false;
			}
		}

		if (!$proceed) { return; }

		$uri = addslashes($this->requesturi);
    	$parsed = parse_url($this->cfg->get('URL')); 
 		$host = preg_replace('#^(www\.)#i', '', $parsed['host']);
		$subject = 'Message from Elxis Defender on '.$host;
		if ($attack == 1) {
			$text = "Elxis Defender blocked an attack to your site!\r\n";
		} else {
			$text = "Elxis Defender detected a change in site filesystem!\r\n";
		}
		$text .= 'Reference code: '.$code."\r\n";
		if ($msg != '') { $text .= "\r\nElxis Defender report\r\n".$msg."\r\n"; }
		$text .= "\r\n";
		$text .= "Requested URI: \t".$uri."\r\n";
		if ($this->address != '') { $text .= "IP address: \t".$this->address."\r\n"; }
		if ($this->host != '') { $text .= "Hostname: \t".$this->host."\r\n"; }
		if ($this->referer != '') { $text .= "HTTP Referrer: \t".$this->referer."\r\n"; }
		if (isset($_SERVER['HTTP_USER_AGENT'])) { $text .= "User agent: \t".$_SERVER['HTTP_USER_AGENT']."\r\n"; }

		if (!empty($_POST)) {
			$text .= "\r\n";
			$text .= '=== POST vars submitted ==='."\r\n";
			foreach ($_POST as $k => $v) {
				if (is_array($v)) {
					$text .= addslashes($k).' = ARRAY'."\r\n";
				} else if (strlen($v) > 200) {
					$text .= addslashes($k).' = LONG STRING'."\r\n";
				} else {
					$text .= addslashes($k).' = '.addslashes($v)."\r\n";
				}
			}
			$text .= "\r\n";
		}

		$text .= "Date (UTC): \t".gmdate('Y-m-d H:i:s')."\r\n";
		$text .= "Site URL: \t".$this->cfg->get('URL')."\r\n\r\n\r\n\r\n";
		$text .= "----------------------------------------------------------\r\n";
		$text .= "Elxis Defender by Elxis Team\r\n";
		$text .= 'Please do not reply to this message as it was generated automatically by the Elxis Defender ';
		$text .= 'and it was sent for informational purposes. Elxis Defender will not send you an other notification for ';
		$text .= 'the next 5 minutes even if more attacks occur.'."\r\n";
		$text .= "----------------------------------------------------------\r\n";

		require_once(ELXIS_PATH.'/includes/libraries/swift/swift_required.php');

		$message = Swift_Message::newInstance();
		$message->setCharset('UTF-8');
		$message->setPriority(2);
		$message->setSubject($subject);
		$message->setBody($text, 'text/plain');
		$message->addTo($this->cfg->get('MAIL_MANAGER_EMAIL'), $this->cfg->get('MAIL_MANAGER_NAME'));

		if (($this->cfg->get('MAIL_METHOD') == 'smtp') && (strpos($this->cfg->get('MAIL_SMTP_HOST'), '.gmail.') !== false)) {
			$message->setFrom(array($this->cfg->get('MAIL_FROM_EMAIL') => $this->cfg->get('MAIL_FROM_NAME')));
		} else {
			$message->setFrom(array('defender@'.$host => 'Elxis Defender'));
		}

		$headers = $message->getHeaders();
		$headers->addTextHeader('X-Mailer', 'Elxis');

		switch ($this->cfg->get('MAIL_METHOD')) {
			case 'smtp':
				$transport = Swift_SmtpTransport::newInstance(
					$this->cfg->get('MAIL_SMTP_HOST'), 
					$this->cfg->get('MAIL_SMTP_PORT'),
					$this->cfg->get('MAIL_SMTP_SECURE')
				);
				if ($this->cfg->get('MAIL_SMTP_AUTH') == 1) {
					if ($this->cfg->get('MAIL_AUTH_METHOD') != '') {
						$transport->setAuthMode($this->cfg->get('MAIL_AUTH_METHOD'));
					}
					$transport->setUsername($this->cfg->get('MAIL_SMTP_USER'));
					$transport->setPassword($this->cfg->get('MAIL_SMTP_PASS'));
				}
			break;
			case 'sendmail':
				$transport = Swift_SendmailTransport::newInstance();
			break;
			case 'mail': default:
				$transport = Swift_MailTransport::newInstance();
			break;
		}

		$mailer = Swift_Mailer::newInstance($transport);
		try {
			$mailer->send($message);
		} catch (\Swift_TransportException $Ste) {
		}
	}


	/*********************/
	/* BAN AN IP ADDRESS */
	/*********************/
	private function banIP($refcode) {
		if ($this->address == '') { return; }
		$file = $this->repo_path.'/logs/defender_ban.php';
		$ip = str_replace('.', 'x', $this->address);
		$ip = str_replace(':', 'y', $ip);
		$unban_ts = time() - $this->banduration;
		$unban_date = gmdate('Y-m-d H:i:s', $unban_ts);

		$nowtimes = 1;
		$buffer = '<?php '._LEND._LEND;
		$buffer .= '//Elxis Defender - Banned IPs - Last updated on '.gmdate('Y-m-d H:i:s').' (UTC)'._LEND._LEND;
		$buffer .= 'defined(\'_ELXIS_\') or die (\'Protected by Elxis Defender\');'._LEND._LEND;
		$buffer .= '$ban = array('._LEND;
		if (!file_exists($file)) {
			$buffer .= '\''.$ip.'\' => array(\'times\' => '.$nowtimes.', \'refcode\' => \'SEC-'.$refcode.'\', \'date\' => \''.gmdate('Y-m-d H:i:s').'\'),'._LEND;
		} else {
			include($file);
			$found = false;
			if (isset($ban) && is_array($ban) && (count($ban) > 0)) {
				foreach ($ban as $key => $row) {
					if ($key == $ip) {
						$found = true;
						$nowtimes = $row['times'] + 1;
						$buffer .= '\''.$ip.'\' => array(\'times\' => '.$nowtimes.', \'refcode\' => \'SEC-'.$refcode.'\', \'date\' => \''.gmdate('Y-m-d H:i:s').'\'),'._LEND;
					} else {
						if ($row['date'] > $unban_date) {//continue ban only IPs within the last 10 days (or whateven banduration value is) 
							$buffer .= '\''.$key.'\' => array(\'times\' => '.$row['times'].', \'refcode\' => \''.$row['refcode'].'\', \'date\' => \''.$row['date'].'\'),'._LEND;
						}
					}
				}
			}
			unset($ban);

			if (!$found) {
				$buffer .= '\''.$ip.'\' => array(\'times\' => '.$nowtimes.', \'refcode\' => \'SEC-'.$refcode.'\', \'date\' => \''.gmdate('Y-m-d H:i:s').'\'),'._LEND;
			}
		}

		$buffer .= ');'._LEND._LEND;
		$buffer .= '?>';

		if ($nowtimes >= $this->bantimes) {
			$this->banmessage .= 'The guest has been BANNED as he was blocked by Elxis Defender '.$nowtimes.' times!'."\r\n";
		}

		if ($handler = @fopen($file, 'w')) {
			$ok = false;
			if (flock($handler, LOCK_EX)) {
				$ok = @fwrite($handler, $buffer);
				flock($handler, LOCK_UN);
			}
			fclose($handler);

			if ($ok) {
				@clearstatcache();
				$fsize = intval(filesize($file) / 1024);
				if ($fsize > 200) {
					$this->banmessage .= 'Bans log file is '.$fsize.'KB! Please remove old bans to make it load faster.'."\r\n";
					$this->banmessage .= 'Bans log file: '.$file;
				}
			}
		}
	}


	/*******************/
	/* SECURITY LOGGER */
	/*******************/
	private function securityLogger($is_attack=true, $refcode='', $message='', $method='') {
		$dlog = (int)$this->cfg->get('DEFENDER_LOG');
		if ($dlog < 1) { return; }
		if ($dlog < 2) {
			if (in_array($method, array('AGENT', 'USER AGENT', 'HOST', 'IP'))) {
				return;
			}
		}

		$file = $this->repo_path.'/logs/security.log';

		if ($this->cfg->get('LOG_ROTATE') == 1) {
			if (file_exists($file)) {
				$modym = date('Ym', filemtime($file));
				$creym = date('Ym');
				if ($creym > $modym) {
					$new_file = $this->repo_path.'/logs/security_'.$modym.'.log';
					@copy($file, $new_file);
					@unlink($file);
				}
			}
		}

		if ($is_attack) {
			$txt = gmdate('Y-m-d H:i:s').' GMT ['.$this->address;
			if ($this->host != '') { $txt .= ' - '.$this->host; }
			$txt .= ']'._LEND;
			if (isset($_SERVER['HTTP_USER_AGENT'])) { $txt .= addslashes($_SERVER['HTTP_USER_AGENT'])._LEND; }
			if (isset($_SERVER['HTTP_REFERER'])) { $txt .= 'REFERER: '.addslashes($_SERVER['HTTP_REFERER'])._LEND; }
			if (isset($_SERVER['REQUEST_METHOD'])) {
				$txt .= $_SERVER['REQUEST_METHOD'].' ';
			} else if (!empty($_POST)) {
				$txt .= 'POST ';
			} else {
				$txt .= 'GET ';
			}
			$txt .= addslashes($this->getURI())._LEND;
			$txt .= 'REFCODE: '.$refcode;
			if ($message != '') { $txt .= ' '.$message; }
			$txt .= _LEND._LEND;
		} else {
			$txt = gmdate('Y-m-d H:i:s').' GMT   '.$message._LEND._LEND;
		}

		$ok = false;
		if (!$fp = @fopen($file, 'a')) { return false; }
		if (flock($fp, LOCK_EX)) {
			$ok = fwrite($fp, $txt);
			flock($fp, LOCK_UN);			
		}
		fclose($fp);
		return $ok;
	}


	/*********************/
	/* GET REQUESTED URI */
	/*********************/
	private function getURI() {
		if (isset($_SERVER['REQUEST_URI'])) { return $_SERVER['REQUEST_URI']; }
		if (isset($_SERVER['QUERY_STRING'])) {
			$query_str = $_SERVER['QUERY_STRING'];
		} else if (@getenv('QUERY_STRING')) {
			$query_str = getenv('QUERY_STRING');
		} else {
			$query_str = '';
		}
		if ($query_str != '') { $query_str = '?'.$query_str; }

		if (isset($_SERVER['PATH_INFO'])) {
			return $_SERVER['PATH_INFO'].$query_str;
		} elseif (@getenv('PATH_INFO')) {
			return getenv('PATH_INFO').$query_str;
		}

		if (isset($_SERVER['PHP_SELF'])) {
			return $_SERVER['PHP_SELF'].$query_str;
		} elseif (@getenv('PHP_SELF')) {
			return getenv('PHP_SELF').$query_str;
		} else {
			return $query_str;
		}
	}


	/*******************/
	/* GET CLIENT'S IP */
	/*******************/
	private function getIP() {
		if (isset($_SERVER['HTTP_CLIENT_IP']) && ($_SERVER['HTTP_CLIENT_IP'] != '')) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && ($_SERVER['HTTP_X_FORWARDED_FOR'] != '')) {
			$n = strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',');//Required in case we have multiple IPs
			if ($n === false) {
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
				$ip = substr($_SERVER['HTTP_X_FORWARDED_FOR'], 0, $n);//get the first IP in the list
			}
		} elseif (isset($_SERVER['REMOTE_ADDR']) && ($_SERVER['REMOTE_ADDR'] != '')) {
			$ip = $_SERVER['REMOTE_ADDR'];
		} else {
			$ip = '';
		}
		return $ip;
	}


	/***********************************/
	/* GET ELXIS FILESYSTEM LOCK FILES */
	/***********************************/
	private function getLockFiles() {
		$files = array('index.php', 'inner.php', 'includes/loader.php');
		$libs = $this->listFiles('includes/libraries/elxis/');
		if ($libs) {
			foreach ($libs as $lib) { $files[] = $lib; }
		}
		unset($libs);
		$tpls = $this->listFolders('templates/');
		if ($tpls) {
			foreach ($tpls as $tpl) {
				if (file_exists(ELXIS_PATH.'/templates/'.$tpl.'/index.php')) {
					$files[] = 'templates/'.$tpl.'/index.php';
				}
				if (file_exists(ELXIS_PATH.'/templates/'.$tpl.'/inner.php')) {
					$files[] = 'templates/'.$tpl.'/inner.php';
				}
			}
		}
		unset($tpls);
		$comps = $this->listFolders('components/');
		if ($comps) {
			foreach ($comps as $comp) {
				$f = str_replace('com_', '', $comp);
				if (file_exists(ELXIS_PATH.'/components/'.$comp.'/'.$f.'.php')) {
					$files[] = 'components/'.$comp.'/'.$f.'.php';
				}
			}
		}
		unset($tpls);
		return $files;
	}


	/*****************************/
	/* LIST FILES IN A DIRECTORY */
	/*****************************/
	private function listFiles($dir, $onlyphp=true) {
		$path = ELXIS_PATH.'/'.$dir;
		if (!is_dir($path)) { return array(); }
		$arr = array();
		$handle = opendir($path);
		while ($entry = readdir($handle)) {
			if (($entry != '.') && ($entry != '..')) {
				if ($onlyphp) {
					if (preg_match('#(\.php)$#i', $entry)) { $arr[] = $dir.$entry; }
				} else {
					$arr[] = $dir.$entry;
				}
			}
		}
		closedir($handle);
		asort($arr);
		return $arr;
	}


	/*******************************/
	/* LIST FOLDERS IN A DIRECTORY */
	/*******************************/
	private function listFolders($dir) {
		$path = ELXIS_PATH.'/'.$dir;
		if (!is_dir($path)) { return array(); }
		$arr = array();
		$handle = opendir($path);
		while ($entry = readdir($handle)) {
			if (($entry != '.') && ($entry != '..') && is_dir($path.$entry)) {
				$arr[] = $entry;
			}
		}
		closedir($handle);
		asort($arr);
		return $arr;
	}


	/************************************/
	/* UPDATE IPs OR IP RANGES DATABASE */
	/************************************/
	private function updateIPDB($ranges=false) {
		/* 
		Many thanks to Stop Forum Spam and blocklist.de teams for their work! 
		http://www.stopforumspam.com - http://www.blocklist.de
		*/
		if ($ranges) {
			$url = 'https://www.stopforumspam.com/downloads/toxic_ip_range.txt';
		} else {
			$url = 'https://api.blocklist.de/getlast.php?time=28800';
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPGET, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_FAILONERROR, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:40.0) Gecko/20100101 Firefox/40.1');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);
		curl_setopt($ch, CURLOPT_REFERER, $this->cfg->get('URL'));
		$data = curl_exec($ch);
		if (curl_errno($ch) == 0) {
			curl_close($ch);
		} else {
			curl_close($ch);
			return false;
		}
		if (trim($data) == '') { return false; }

		$lines = preg_split("@((\r?\n)|(\r\n?))@", $data);
		if (!$lines) { return false; }

		$txt = '<?php '._LEND;
		if ($ranges) {
			$txt .= '//Elxis Defender - Blocked IP ranges - Last update '.gmdate('Y-m-d H:i:s').' GMT'._LEND._LEND;
		} else {
			if (strpos($lines[0], '<') !== false) { return false; } //nothing found
			$txt .= '//Elxis Defender - Blocked IPs - Last update '.gmdate('Y-m-d H:i:s').' GMT'._LEND._LEND;
		}

		$i = 0;
		$txt .= '$ips = array('._LEND;
		foreach($lines as $line) {
			$line = trim($line);
			if ($line == '') { continue; }
			if ($ranges) {
				$parts = explode('-', $line);
				if (!isset($parts[1])) { continue; }
				$first = ip2long($parts[0]);
				$last = ip2long($parts[1]);
				if (($first == -1) || ($first === false) || ($last == -1) || ($last === false)) { continue; }
				//I. Sannos note: we use "%u" to get the unsigned ip address because on 32bit systems ip2long might return negative number
				$txt .= "\t".'array(\''.$parts[0].'\', \''.$parts[1].'\', \''.sprintf('%u', $first).'\', \''.sprintf('%u', $last).'\'),'._LEND;
			} else {
				if ($i > 4000) { break; }
				$txt .= '\''.$line.'\','._LEND;
				$i++;
			}
		}
		$txt .= ');'._LEND._LEND;
		$txt .= '?>';
		unset($lines);

		$file = ($ranges) ? $this->repo_path.'/logs/defender_ip_ranges.php' : $this->repo_path.'/logs/defender_ips.php';
		$fp = @fopen($file, 'w');
		if ($fp) {
			if (flock($fp, LOCK_EX)) {
				fwrite($fp, $txt);
				flock($fp, LOCK_UN);
			}
			fclose($fp);
		}
		return true;
	}

}

?>