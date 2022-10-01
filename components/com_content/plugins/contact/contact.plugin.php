<?php 
/**
* @version		$Id: contact.plugin.php 2347 2020-05-25 18:07:01Z IOS $
* @package		Elxis
* @subpackage	Content Plugins / Contact
* @copyright	Copyright (c) 2006-2020 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class contactPlugin implements contentPlugin {


	/********************/
	/* MAGIC CONTRUCTOR */
	/********************/
	public function __construct() {
	}


	/***********************************/
	/* EXECUTE PLUGIN ON THE GIVEN ROW */
	/***********************************/
	public function process(&$row, $published, $params) {
    	$regex = "#{contact}(.*?){/contact}#s";
    	if (!$published) {
    		$row->text = preg_replace($regex, '', $row->text);
    		return true;
    	}

		preg_match_all($regex, $row->text, $matches, PREG_PATTERN_ORDER);
		if (!$matches) { return true; }

		$eURI = eFactory::getURI();
    	$proceed = false;
    	if ($eURI->getComponent() == 'content') {
    		if (!$eURI->isDir()) {
    			$proceed = true;
				$action = $eURI->getRealUriString();
				$parts = preg_split('#\?#', $action, 2);
				$action = $parts[0];
				unset($parts);
			}
   		}

		if (!$proceed) {
    		$row->text = preg_replace($regex, '', $row->text);
    		return true;
		}
		unset($proceed);

		$cfg = array();
		$stdfields = array('phone', 'mobile', 'address', 'city', 'postalcode', 'country');
		foreach ($stdfields as $stdfield) {
			$rfield = 'req_'.$stdfield;
			$cfg[$stdfield] = (int)$params->get($stdfield, 1);
			$cfg[$rfield] = (int)$params->get($rfield, 0);
		}
		$cfg['website'] = (int)$params->get('website', 0);
		$cfg['req_website'] = (int)$params->get('req_website', 0);
		unset($stdfields);

		$cfg['field1'] = eUTF::trim($params->getML('field1', ''));
		$cfg['req_field1'] = (int)$params->get('req_field1', 0);
		$cfg['field2'] = eUTF::trim($params->getML('field2', ''));
		$cfg['req_field2'] = (int)$params->get('req_field2', 0);
		$cfg['field3'] = eUTF::trim($params->getML('field3', ''));
		$cfg['req_field3'] = (int)$params->get('req_field3', 0);
		$cfg['field4'] = eUTF::trim($params->getML('field4', ''));
		$cfg['field4_options'] = array();
		$cfg['field5'] = eUTF::trim($params->getML('field5', ''));
		$cfg['field5_options'] = array();
		$cfg['field6'] = eUTF::trim($params->getML('field6', ''));
		$cfg['field6_options'] = array();
		for ($i=4; $i < 7; $i++) {
			$idx_field = 'field'.$i;
			if ($cfg[$idx_field] != '') {
				$n = 0;
				$idx_option = 'field'.$i.'_options';
				for ($k=1; $k < 6; $k++) {
					$idx = 'option'.$i.$k;
					$opt = eUTF::trim($params->getML($idx, ''));
					if ($opt != '') {
						$cfg[$idx_option][] = $opt;
						$n++;
					}
				}
				if ($n == 0) {
					$cfg[$idx_field] = '';
					$cfg[$idx_option] = array();
				}
			}
		}

		$proc = false;
		foreach ($matches[0] as $i => $match) {//only the first match will be processed!
			if ($proc == true) {
			    $row->text = str_replace($match, '', $row->text);
				continue;
			}
			$rcptmail = $matches[1][$i];
			if (!filter_var($rcptmail, FILTER_VALIDATE_EMAIL)) {
			    $row->text = str_replace($match, '', $row->text);
				continue;
			}

			$response = '';
			if (isset($_POST['firstname'])) {
				$response = $this->processRequest($cfg, $rcptmail, $row->title, $action);
			}

			$html = $this->makeForm($row->id, $cfg, $action);
			$html = $response.$html;
			$row->text = preg_replace("#".$match."#", $html, $row->text);
			$proc = true;
		}
		return true;
	}


	/************************/
	/* GENERIC SYNTAX STYLE */
	/************************/
	public function syntax() {
		return '{contact}recipient_email_address{/contact}';
	}


	/***********************/
	/* LIST OF HELPER TABS */
	/***********************/
	public function tabs() {
		return array();
	}


	/*****************/
	/* PLUGIN HELPER */
	/*****************/
	public function helper($pluginid, $tabidx, $fn) {
		if ($tabidx <> 1) { return; }

		$eLang = eFactory::getLang();
		$defmail = eFactory::getElxis()->getConfig('MAIL_EMAIL');

		echo '<div class="elx5_sideinput_wrap">';
		echo '<div class="elx5_sideinput_value_end elx5_spad">';
		echo '<a href="javascript:void(null);" class="elx5_btn elx5_ibtn" title="'.$eLang->get('ADD').'" onclick="addContactCode();"><i class="fas fa-location-arrow"></i></a>';
		echo '</div>';
		echo '<div class="elx5_sideinput_input_front elx5_spad">';
		echo '<label class="elx5_label" for="contact_email">'.$eLang->get('RCPT_EMAIL').'</label>';
		echo '<div class="elx5_labelside">';
		echo '<input type="text" name="contact_email" value="'.$defmail.'" id="contact_email" class="elx5_text" dir="ltr" placeholder="'.$eLang->get('RCPT_EMAIL').'">';
		echo "</div></div></div>\n";
	}


	/***************************************************/
	/* RETURN REQUIRED CSS AND JS FILES FOR THE HELPER */
	/***************************************************/
	public function head() {
		$elxis = eFactory::getElxis();

		$response = array(
			'js' => array($elxis->secureBase().'/components/com_content/plugins/contact/includes/contact.js'),
			'css' => array()
		);

		return $response;
	}


	/*******************************/
	/* PLUGIN SPECIAL TASK HANDLER */
	/*******************************/
	public function handler($pluginid, $fn) {
		$elxis = eFactory::getElxis();
		$url = $elxis->makeAURL('content:plugin/', 'inner.php').'?id='.$pluginid.'&fn='.$fn;
		$elxis->redirect($url);
	}


	/***********************/
	/* MAKE FORM HTML CODE */
	/***********************/
	private function makeForm($id, $cfg, $action) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$pfx = 'art'.$id;
		if ($eLang->getinfo('DIR') == 'rtl') {
			$dir = 'rtl';
		} else {
			$dir = 'ltr';
		}

		$jslink = $elxis->secureBase().'/components/com_content/plugins/contact/includes/contact.js';
		eFactory::getDocument()->addScriptLink($jslink);

		elxisLoader::loadFile('includes/libraries/elxis/form5.class.php');

		$form = new elxis5Form(array('idprefix' => $pfx, 'labelclass' => 'elx5_label', 'sideclass' => 'elx5_labelside', 'returnhtml' => true));
		$out = $form->openForm(array('name' => 'contactform', 'method' => 'post', 'action' => $action, 'id' => $pfx.'contactform'));

		$out .= $form->openFieldset($eLang->get('CONTACT'));
		$out .= $form->addText('firstname', $elxis->user()->firstname, $eLang->get('FIRSTNAME').'*', array('dir' => $dir, 'maxlength' => 60, 'required' => 'required'));
		$out .= $form->addText('lastname', $elxis->user()->lastname, $eLang->get('LASTNAME').'*', array('dir' => $dir, 'maxlength' => 60, 'required' => 'required'));
		if ($cfg['address'] == 1) {
			$reqmark = '';
			$attrs = array('maxlength' => 120, 'dir' => $dir);
			if ($cfg['req_address'] == 1) { $reqmark = '*'; $attrs['required'] = 'required'; }
			$out .= $form->addText('address', $elxis->user()->address, $eLang->get('ADDRESS').$reqmark, $attrs);
		}
		if ($cfg['city'] == 1) {
			$reqmark = '';
			$attrs = array('maxlength' => 120, 'dir' => $dir);
			if ($cfg['req_address'] == 1) { $reqmark = '*'; $attrs['required'] = 'required'; }
			$out .= $form->addText('city', $elxis->user()->city, $eLang->get('CITY').$reqmark, $attrs);
		}
		if ($cfg['postalcode'] == 1) {
			$reqmark = '';
			$attrs = array('maxlength' => 10, 'dir' => 'ltr', 'class' => 'elx5_text elx5_minitext');
			if ($cfg['req_address'] == 1) { $reqmark = '*'; $attrs['required'] = 'required'; }
			$out .= $form->addText('postalcode', $elxis->user()->postalcode, $eLang->get('POSTAL_CODE').$reqmark, $attrs);
		}
		if ($cfg['country'] == 1) {
			$reqmark = '';
			if ($cfg['req_country'] == 1) { $reqmark = '*'; }
			$vcountry = $elxis->user()->country;
			if ($vcountry == '') { $vcountry = $this->visitorCountry(); }
			if ($vcountry != '') {
				$sel = $vcountry;
			} else {
				$sel = 'US';
			}
			$out .= $form->addCountry('country', $eLang->get('COUNTRY').$reqmark, $sel, array('dir' => 'rtl'));
		}

		if ($cfg['phone'] == 1) {
			$reqmark = '';
			$attrs = array('maxlength' => 40, 'dir' => 'ltr', 'pattern' => '[0-9\+\-\s]{6,}');
			if ($cfg['req_phone'] == 1) { $reqmark = '*'; $attrs['required'] = 'required'; }
			$out .= $form->addTel('phone', $elxis->user()->phone, $eLang->get('TELEPHONE').$reqmark, $attrs);
		}

		if ($cfg['mobile'] == 1) {
			$reqmark = '';
			$attrs = array('maxlength' => 40, 'dir' => 'ltr', 'pattern' => '[0-9\+\-\s]{6,}');
			if ($cfg['req_mobile'] == 1) { $reqmark = '*'; $attrs['required'] = 'required'; }
			$out .= $form->addTel('mobile', $elxis->user()->mobile, $eLang->get('MOBILE').$reqmark, $attrs);
		}

		$out .= $form->addEmail('email', $elxis->user()->email, $eLang->get('EMAIL').'*', array('required' => 'required',  'dir' => 'ltr'));

		if ($cfg['website'] == 1) {
			$reqmark = '';
			$attrs = array('maxlength' => 120, 'dir' => 'ltr');
			if ($cfg['req_website'] == 1) { $reqmark = '*'; $attrs['required'] = 'required'; }
			$out .= $form->addURL('website', $elxis->user()->website, $eLang->get('WEBSITE').$reqmark, $attrs);
		}

		for ($i=1; $i < 4; $i++) {
			$idx = 'field'.$i;
			if ($cfg[$idx] == '') { continue; }
			$idx_req = 'req_field'.$i;
			$reqmark = '';
			$attrs = array('dir' => $dir);
			if ($cfg[$idx_req] == 1) { $reqmark = '*'; $attrs['required'] = 'required'; }
			$out .= $form->addText('field'.$i, '', $cfg[$idx].$reqmark, $attrs);
		}
		for ($i=4; $i < 7; $i++) {
			$idx = 'field'.$i;
			if ($cfg[$idx] == '') { continue; }
			$idx_options = 'field'.$i.'_options';
			$sel = '';
			$options = array();
			foreach ($cfg[$idx_options] as $k => $option) {
				if ($k == 0) { $sel = $option; }
				$options[] = $form->makeOption($option, $option);
			}
			$out .= $form->addSelect('field'.$i, $cfg[$idx], $sel, $options);
		}

		$out .= $form->addTextarea('message', '', $eLang->get('MESSAGE').'*', array('dir' => $dir, 'required' => 'required'));

		if ($elxis->getConfig('CAPTCHA') != 'NONE') {
			if ($elxis->getConfig('CAPTCHA') == 'MATH') {
				$out .= $form->addCaptcha('conseccode', $eLang->get('SECURITY_CODE').'*');
			} else {
				$out .= $form->addNoRobot('norobot');
			}
		}

		$attrs = array('onclick' => 'return plgContactValidate(\''.$pfx.'\');', 'tip' => $eLang->get('FIELDSASTERREQ'), 'class' => 'elx5_btn elx5_sucbtn', 'sidepad' => 1);
		$out .= $form->addHTML('<div class="elx5_vlspace">');
		$out .= $form->addButton('sbmcontact', $eLang->get('SUBMIT'), 'button', $attrs);
		$out .= $form->addHTML('</div>');

		$out .= $form->closeFieldset();
		$out .= $form->closeForm();

		return $out;
	}


	/**************************/
	/* DETECT VISITOR COUNTRY */
	/**************************/
	private function visitorCountry() {
		$eLang = eFactory::getLang();

		$region = $eLang->getinfo('REGION');

		if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) { return $region; }
		$acc_langs_str = trim($_SERVER['HTTP_ACCEPT_LANGUAGE']);
		if ($acc_langs_str == '') { return $region; }
		$langs = array();
		$acc_langs = explode(',', trim($acc_langs_str));
		foreach ($acc_langs as $acc_lang) {
			if (preg_match('/(\*|[a-zA-Z0-9]{1,8}(?:-[a-zA-Z0-9]{1,8})*)(?:\s*;\s*q\s*=\s*(0(?:\.\d{0,3})|1(?:\.0{0,3})))?/', trim($acc_lang), $match)) {
				$q = (!isset($match[2])) ? '1.0' : (string)floatval($match[2]);
				$m = trim(strtolower($match[1]));
				$n = strpos($m, '-');
				if ($n !== false) { $m = substr($m, 0, $n); }
				if (!isset($langs[$q])) { $langs[$q] = array(); }
				$langs[$q][] = $m;
			}
		}
		if (!$langs) { return $region; }
		krsort($langs);
		$final_lang = '';
		foreach ($langs as $k => $lngs) {
			$final_lang = $lngs[0];
			break;
		}
		if ($final_lang == '') { return $region; }
		if ($final_lang == 'en') { return $region; }
		if ($final_lang == 'fr') { return 'FR'; }
		if ($final_lang == 'es') { return 'ES'; }
		if ($final_lang == 'el') { return 'GR'; }
		if ($final_lang == 'de') { return 'DE'; }
		if ($final_lang == 'ru') { return 'RU'; }
		if ($final_lang == 'it') { return 'IT'; }
		if ($final_lang == 'pt') { return 'PT'; }
		if ($final_lang == 'pl') { return 'PL'; }
		if ($final_lang == 'tr') { return 'TR'; }
		if ($final_lang == 'ro') { return 'RO'; }

		include(ELXIS_PATH.'/includes/libraries/elxis/language/langdb.php');
		foreach ($langdb as $elxis_lang => $data) {
			if ($data['LANGUAGE'] == $final_lang) {
				$region = $data['REGION'];
				break;
			}
		}

		return $region;
	}


	/******************************/
	/* ALIGN 2 STRINGS IN COLUMNS */
	/******************************/
	private function alignPlainText($left_string, $right_string, $left_width=25) {
		$spaces = $left_width - eUTF::strlen($left_string);
		$text = $left_string.': ';
		if ($spaces > 0) { $text .= str_repeat(' ', $spaces); }
		$text .= $right_string;
		return $text;
	}


	/***************************/
	/* PROCESS FORM SUBMISSION */
	/***************************/
	private function processRequest($cfg, $rcptemail, $pagetitle, $pageurl) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();

		$captcha = $elxis->obj('captcha');
		$ok = $captcha->validate($elxis->getConfig('CAPTCHA'), 'captcha_conseccode', 'conseccode', 'norobot', '');
		if (!$ok) {
			return '<div class="elx5_warning"><strong>'.$eLang->get('ERROR').':</strong> '.$captcha->getError()."</div>\n";
		}
		unset($captcha);

		$text = '';
		$firstname = filter_input(INPUT_POST, 'firstname', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		if (trim($firstname) == '') {
			$ltext = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('FIRSTNAME'));
			return '<div class="elx5_warning"><strong>'.$eLang->get('ERROR').':</strong> '.$ltext."</div>\n";
		}
		$text .= $this->alignPlainText($eLang->get('FIRSTNAME'), $firstname)."\n";

		$lastname = filter_input(INPUT_POST, 'lastname', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		if (trim($lastname) == '') {
			$ltext = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('LASTNAME'));
			return '<div class="elx5_warning"><strong>'.$eLang->get('ERROR').':</strong> '.$ltext."</div>\n";
		}
		$text .= $this->alignPlainText($eLang->get('LASTNAME'), $lastname)."\n";

		if ($cfg['address'] == 1) {
			$val = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
			if ($cfg['req_address'] == 1) {
				if (trim($val) == '') {
					$ltext = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('ADDRESS'));
					return '<div class="elx5_warning"><strong>'.$eLang->get('ERROR').':</strong> '.$ltext."</div>\n";
				}
			}
			if ($val != '') {
				$text .= $this->alignPlainText($eLang->get('ADDRESS'), $val)."\n";
			}
		}

		if ($cfg['city'] == 1) {
			$val = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
			if ($cfg['req_city'] == 1) {
				if (trim($val) == '') {
					$ltext = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('CITY'));
					return '<div class="elx5_warning"><strong>'.$eLang->get('ERROR').':</strong> '.$ltext."</div>\n";
				}
			}
			if ($val != '') {
				$text .= $this->alignPlainText($eLang->get('CITY'), $val)."\n";
			}
		}

		if ($cfg['postalcode'] == 1) {
			$val = isset($_POST['postalcode']) ? (int)$_POST['postalcode'] : 0;
			if ($cfg['req_postalcode'] == 1) {
				if ($val < 1) {
					$ltext = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('POSTAL_CODE'));
					return '<div class="elx5_warning"><strong>'.$eLang->get('ERROR').':</strong> '.$ltext."</div>\n";
				}
			}
			if ($val == 0) { $val = ''; }
			if ($val != '') {
				$text .= $this->alignPlainText($eLang->get('POSTAL_CODE'), $val)."\n";
			}
		}

		if ($cfg['country'] == 1) {
			$val = filter_input(INPUT_POST, 'country', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
			if ($val != '') {
				$lng = $eLang->getinfo('LANGUAGE');
				if (file_exists(ELXIS_PATH.'/includes/libraries/elxis/form/countries.'.$lng.'.php')) {
					include(ELXIS_PATH.'/includes/libraries/elxis/form/countries.'.$lng.'.php');
				} else {
					include(ELXIS_PATH.'/includes/libraries/elxis/form/countries.en.php');
				}
				if (isset($countries[$val])) {
					$val = $countries[$val].' ('.$val.')';
				} else {
					$val = '';
				}
				unset($countries);
			}

			if ($cfg['req_country'] == 1) {
				if ($val == '') {
					$ltext = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('COUNTRY'));
					return '<div class="elx5_warning"><strong>'.$eLang->get('ERROR').':</strong> '.$ltext."</div>\n";
				}
			}

			$text .= $this->alignPlainText($eLang->get('COUNTRY'), $val)."\n";
		}

		if ($cfg['phone'] == 1) {
			$val = isset($_POST['phone']) ? (int)$_POST['phone'] : 0;
			if ($cfg['req_phone'] == 1) {
				if ($val < 1) {
					$ltext = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('TELEPHONE'));
					return '<div class="elx5_warning"><strong>'.$eLang->get('ERROR').':</strong> '.$ltext."</div>\n";
				}
			}
			if ($val == 0) { $val = ''; }
			if ($val != '') {
				$text .= $this->alignPlainText($eLang->get('TELEPHONE'), $val)."\n";
			}
		}

		if ($cfg['mobile'] == 1) {
			$val = isset($_POST['mobile']) ? (int)$_POST['mobile'] : 0;
			if ($cfg['req_mobile'] == 1) {
				if ($val < 1) {
					$ltext = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('MOBILE'));
					return '<div class="elx5_warning"><strong>'.$eLang->get('ERROR').':</strong> '.$ltext."</div>\n";
				}
			}
			if ($val == 0) { $val = ''; }
			if ($val != '') {
				$text .= $this->alignPlainText($eLang->get('MOBILE'), $val)."\n";
			}
		}

		$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
		if (($email == '') || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
			return '<div class="elx5_warning"><strong>'.$eLang->get('ERROR').':</strong> '.$eLang->get('INVALIDEMAIL')."</div>\n";
		}
		$text .= $this->alignPlainText($eLang->get('EMAIL'), $email)."\n";

		if ($cfg['website'] == 1) {
			$val = filter_input(INPUT_POST, 'website', FILTER_SANITIZE_URL);
			if (!filter_var($val, FILTER_VALIDATE_URL)) { $val = ''; }
			if ($cfg['req_website'] == 1) {
				if ($val == '') {
					return '<div class="elx5_warning"><strong>'.$eLang->get('ERROR').':</strong> '.$eLang->get('INVALID_URL_ADDR')."</div>\n";
				}
			}
			if ($val != '') {
				$text .= $this->alignPlainText($eLang->get('WEBSITE'), $val)."\n";
			}
		}

		for ($i=1; $i < 7; $i++) {
			$idx = 'field'.$i;
			if ($cfg[$idx] == '') { continue; }
			$val = filter_input(INPUT_POST, 'field'.$i, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
			if ($i < 4) {
				$idx_req = 'req_field'.$i;
				if ($cfg[$idx_req] == 1) {
					if (trim($val) == '') {
						$ltext = sprintf($eLang->get('FIELDNOEMPTY'), $cfg[$idx]);
						return '<div class="elx5_warning"><strong>'.$eLang->get('ERROR').':</strong> '.$ltext."</div>\n";
					}
				}
			}
			if (trim($val) != '') {
				$text .= $this->alignPlainText($cfg[$idx], $val)."\n";
			}
		}

		$ipaddr = $elxis->obj('ip')->clientIP(false, false);
		$text .= $this->alignPlainText($eLang->get('IP_ADDRESS'), $ipaddr)."\n\n";

		$val = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		if (trim($val) == '') {
			return '<div class="elx5_warning"><strong>'.$eLang->get('ERROR').':</strong> '.$eLang->get('MUST_WRITE_MESSAGE')."</div>\n";
		}

		$text .= $eLang->get('MESSAGE').":\n";
		$text .= $val."\n";

		$parsed = parse_url($elxis->getConfig('URL'));
		$subject = sprintf($eLang->get('CONTACT_INQ_FROM'), $parsed['host']);

		$body = $eLang->get('HI')."\n";
		$body .= $eLang->get('CFORM_SUBMIT')."\n";
		$body .= $eLang->get('INFO_FOLLOW')."\n\n";
		$body .= $eLang->get('PAGE').': '.$pagetitle."\n";
		$body .= $pageurl."\n\n\n";
		$body .= $text."\n\n\n";
		$body .= $eLang->get('REGARDS')."\n";
		$body .= $elxis->getConfig('SITENAME')."\n";
		$body .= $elxis->getConfig('URL')."\n\n\n";
		$body .= "_______________________________________________________________\n";
		$body .= 'Sent by Elxis running on '.$elxis->getConfig('URL');

		$replyto = $email.','.$firstname.' '.$lastname;
		$from = $elxis->getConfig('MAIL_FROM_EMAIL').','.$elxis->getConfig('MAIL_FROM_NAME');
		$ok = $elxis->sendmail($subject, $body, '', null, 'plain', $rcptemail, null, null, $from, 3, false, $replyto);
		if ($ok) {
			return '<div class="elx5_success">'.$eLang->get('MSG_SENT_REPLY_THANKS').'</div>';
		} else {
			return '<div class="elx5_error">'.$eLang->get('SORRY_SEND_FAILED').'</div>';
		}
	}

}

?>