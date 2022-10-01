<?php 
/**
* @version		$Id$
* @package		Elxis CMS
* @subpackage	Templates / Five
* @author		Ioannis Sannos ( https://www.isopensource.com )
* @copyright	Copyleft (c) 2008-2019 Is Open Source (https://www.isopensource.com).
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
************************************************************************************************/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


class templateFive {

	private $tplparams = array();
	private $is_frontpage = false;
	private $sidecolumn = true;


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		$elxis = eFactory::getElxis();

		$elxuri = eFactory::getURI()->getElxisUri();
		if (preg_match('@(\:)$@', $elxuri)) { $elxuri .= '/'; }
		if (($elxuri == '') || ($elxuri == 'content:/') || ($elxuri == '/') || ($elxuri == 'content') || ($elxuri == $elxis->getConfig('DEFAULT_ROUTE'))) {
			$this->is_frontpage = true;
		}

		$this->prepare($elxis);
	}


	/***************************/
	/* GET TEMPLATE PARAMETERS */
	/***************************/
	private function prepare($elxis) {
		elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
		$xmlpath = ELXIS_PATH.'/templates/five/five.xml';
		$tplparams = $this->getDBParams();
		$params = new elxisParameters($tplparams, $xmlpath, 'template');

		$this->tplparams['tplwidth'] = (int)$params->get('tplwidth', 0);
		$this->tplparams['title'] = eUTF::trim($params->getML('title', ''));
		if ($this->tplparams['title'] == '') {
			$parts = preg_split('@\s@', $elxis->getConfig('SITENAME'));
			if (count($parts) > 1) {
				$title = '<span>'.$parts[0].'</span>';
				foreach ($parts as $k => $part) { if ($k == 0) { continue; } $title .= ' '.$part; }
			} else {
				$title = $elxis->getConfig('SITENAME');
			}
			$this->tplparams['title'] = $title;
		}
		$this->tplparams['slogan'] = eUTF::trim($params->getML('slogan', ''));
		if ($this->tplparams['slogan'] == '') { $this->tplparams['slogan'] = 'Template Five for Elxis CMS'; }

		$socials = array('facebook', 'twitter', 'youtube', 'flickr', 'instagram', 'linkedin', 'pinterest', 'tumblr', 'tripadvisor');
		foreach ($socials as $social) {
			$this->tplparams[$social] = trim($params->get($social, ''));
			if ($this->tplparams[$social] != '') {
				if (!preg_match('@^(https?\:\/\/)@i', $this->tplparams[$social])) { $this->tplparams[$social] = ''; }	
			}
		}

		$this->tplparams['rss'] = (int)$params->get('rss', 1);
		$this->tplparams['sitemap'] = (int)$params->get('sitemap', 1);
		if (!file_exists(ELXIS_PATH.'/components/com_sitemap/sitemap.php')) { $this->tplparams['sitemap'] = 0; }
		$this->tplparams['sliderimage'] = (int)$params->get('sliderimage', 0);

		$this->tplparams['slider'] = array();
		$this->tplparams['sltitle'] = eUTF::trim($params->getML('sltitle', ''));
		if ($this->is_frontpage) { //frontpage slider
			for ($i=1; $i < 7; $i++) {
				$idx1 = 'sl'.$i;
				$idx2 = 'slcaption'.$i;
				$idx3 = 'sllink'.$i;
				$img = trim($params->get($idx1, ''));
				if ($img == '') { continue; }
				$caption = eUTF::trim($params->getML($idx2, ''));
				$link = trim($params->get($idx3, ''));
				if (!file_exists(ELXIS_PATH.'/'.$img)) { continue; }
				if ($link != '') {
					if (!preg_match('@^(http(s?)\:\/\/)@i', $link)) {
						$link = $elxis->makeURL($link, 'index.php', true, false);
					}
				}
				$this->tplparams['slider'][] = array(
					'image' => $elxis->secureBase().'/'.$img,
					'caption' => $caption,
					'link' => $link
				);
			}

			if (count($this->tplparams['slider']) == 0) {
				if ($this->tplparams['sltitle'] == '') { $this->tplparams['sltitle'] = 'Amazing discoveries'; }
				$this->tplparams['slider'][] = array(
					'image' => $elxis->secureBase().'/templates/five/images/slider/def1.jpg',
					'caption' => 'Giant stone spheres@The mysterious stone spheres of Costa Rica',
					'link' => $elxis->makeURL('content:amazing-discoveries/costa-rica-stone-spheres.html')
				);
				$this->tplparams['slider'][] = array(
					'image' => $elxis->secureBase().'/templates/five/images/slider/def2.jpg',
					'caption' => 'Yonaguni Monument@The Underwater City of Yonaguni',
					'link' => $elxis->makeURL('content:amazing-discoveries/underwater-city-yonaguni.html')
				);
				$this->tplparams['slider'][] = array(
					'image' => $elxis->secureBase().'/templates/five/images/slider/def3.jpg',
					'caption' => 'Antikythera Mechanism@A 2200-year-old astronomical calculator',
					'link' => $elxis->makeURL('content:amazing-discoveries/antikythera-mechanism.html')
				);
				$this->tplparams['slider'][] = array(
					'image' => $elxis->secureBase().'/templates/five/images/slider/def4.jpg',
					'caption' => 'Voynich Manuscript@The most mysterious manuscript',
					'link' => $elxis->makeURL('content:amazing-discoveries/voynich-manuscript.html')
				);
				$this->tplparams['slider'][] = array(
					'image' => $elxis->secureBase().'/templates/five/images/slider/def5.jpg',
					'caption' => 'The unfinished obelisk@Egypt, 1500 BC',
					'link' => $elxis->makeURL('content:amazing-discoveries/unfinished-obelisk.html')
				);
				//shuffle($this->tplparams['slider']);
			}
		}//else background image "fpimg"

		$this->tplparams['fpimgpos'] = trim($params->getML('fpimgpos', ''));//if sliderimage = 1
		$this->tplparams['bgtype'] = (int)$params->get('bgtype', 0);
		$this->tplparams['pathway'] = (int)$params->get('pathway', 3);
		$this->tplparams['marquee'] = eUTF::trim($params->getML('marquee', ''));
		if (($this->tplparams['pathway'] == 3) || ($this->tplparams['pathway'] == 4) || ($this->tplparams['pathway'] == 5)) {
			if ($this->tplparams['marquee'] == '') {
				$this->tplparams['marquee'] = '<strong>Five</strong> is a template for Elxis 5.x designed by <a href="https://www.isopensource.com">Is Open Source</a>. The template is friendly to mobile phones and tablets and has many parameters to customize it.';
			}
		}

		$this->tplparams['marqspeed'] = (int)$params->get('marqspeed', 7);
		$this->tplparams['sidecol'] = (int)$params->get('sidecol', 1);

		$this->tplparams['hidecol_paths'] = array();
		for ($i = 1; $i < 6; $i++) {
			$v = trim($params->get('hidecol_path'.$i, ''));
			if ($v != '') { $this->tplparams['hidecol_paths'][] = $v; }
		}
		$this->sidecolumn = $this->determineColumn();

		$this->tplparams['search'] = trim($params->get('search', 'content'));
		if ($this->tplparams['search'] == 'none') { $this->tplparams['search'] = ''; }
		$this->tplparams['searchuri'] = trim($params->get('searchuri', ''));
		if ($this->tplparams['searchuri'] != '') {
			if ((stripos($this->tplparams['searchuri'], 'http://') === 0) || (stripos($this->tplparams['searchuri'], 'https://') === 0)) {
				$this->tplparams['searchuri'] = '';
			} else if (strpos($this->tplparams['searchuri'], ':') === false) {
				$this->tplparams['searchuri'] = '';
			}
		}
		$this->tplparams['searchpar'] = trim($params->get('searchpar', 'q'));
		if ($this->tplparams['searchpar'] == '') { $this->tplparams['searchpar'] = 'q'; }

		$this->tplparams['contact'] = trim($params->get('contact', ''));
		$this->tplparams['phone'] = trim($params->get('phone', ''));
		$this->tplparams['langselect'] = (int)$params->get('langselect', 1);
		$this->tplparams['login'] = (int)$params->get('login', 1);
		$this->tplparams['cart'] = (int)$params->get('cart', 0);
		if (!file_exists(ELXIS_PATH.'/components/com_shop/shop.php')) { $this->tplparams['cart'] = 0; }
		$this->tplparams['copyright'] = (int)$params->get('copyright', 1);
		$this->tplparams['copyrighttxt'] = $params->get('copyrighttxt', '');
	}


	/***********************************/
	/* GET TEMPLATE PARAMETERS FROM DB */
	/***********************************/    
	private function getDBParams() {
		$db = eFactory::getDB();

		$sql = "SELECT ".$db->quoteId('params')." FROM ".$db->quoteId('#__templates')
		."\n WHERE ".$db->quoteId('template').' = '.$db->quote('five').' AND '.$db->quoteId('section').' = '.$db->quote('frontend');
		$stmt = $db->prepareLimit($sql, 0, 1);
		$stmt->execute();
		return (string)$stmt->fetchResult();        
	}


	/**************************/
	/* GET TEMPLATE PARAMETER */
	/**************************/
	public function getParam($name) {
		return (isset($this->tplparams[$name])) ? $this->tplparams[$name] : '';       
	}


	/***********************************************/
	/* DETERMINE IF WE SHOULD SHOW THE SIDE COLUMN */
	/***********************************************/
	private function determineColumn() {
		if ($this->tplparams['sidecol'] == 0) { return false; }

		$eURI = eFactory::getURI();
		$elxuri = $eURI->getElxisUri();
		$uri_str = $eURI->getUriString();

		if ($this->tplparams['sidecol'] === 2) {
			if ($this->is_frontpage === true) { return false; }
		}

		if (($elxuri == '') || ($uri_str == '')) { return true; }

		$parts = explode('/', $uri_str);
		if (!$parts) { return true; }
		if (strlen($parts[0]) < 3) {
			array_shift($parts);
			if (!$parts) { return true; }
			$uri_str = implode('/', $parts);
		}

		if ($uri_str == '') { return true; }
		if ($this->tplparams['hidecol_paths']) {
			foreach ($this->tplparams['hidecol_paths'] as $path) {
				if (strpos($uri_str, $path) === 0) { return false; }
			}
		}

		return true;
	}


	/******************************/
	/* DETERMINE HEADER CSS CLASS */
	/******************************/
	public function globalHeaderClass() {
		if ($this->is_frontpage) {
			if ($this->tplparams['sliderimage'] == 1) {//display static image
				if (defined('ELXIS_MULTISITE')) {
					if (ELXIS_MULTISITE > 1) {
						return 'tpl5_header_all_wrap tpl5_header_ms'.ELXIS_MULTISITE.'_fpbg';
					}
				}
				return 'tpl5_header_all_wrap tpl5_header_fpbg';
			} else if ($this->tplparams['sliderimage'] == 2) {//display nothing
				return 'tpl5_header_all_wrapno';
			} else {//0: slider
				return 'tpl5_header_all_wrapfp';
			}
		}

		if ($this->tplparams['bgtype'] == 0) {//display nothing
			return 'tpl5_header_all_wrapno';
		}
		//display background image
		if (defined('ELXIS_MULTISITE')) {
			if (ELXIS_MULTISITE > 1) {
				return 'tpl5_header_all_wrap tpl5_header_ms'.ELXIS_MULTISITE.'_inbg';
			}
		}
		return 'tpl5_header_all_wrap tpl5_header_inbg';
	}


	/**********************************/
	/* DETERMINE TOP HEADER CSS CLASS */
	/**********************************/
	public function topHeaderClass() {
		if ($this->is_frontpage) {
			if ($this->tplparams['sliderimage'] == 2) {//display nothing
				return 'tpl5_header_top_wrapno';
			}
			return 'tpl5_header_top_wrap';
		}
		if ($this->tplparams['bgtype'] == 0) {//display nothing
			return 'tpl5_header_top_wrapno';
		}
		return 'tpl5_header_top_wrap';
	}


	/*****************/
	/* MAKE TOP LINE */
	/*****************/
	public function makeTopLine() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eURI = eFactory::getURI();

		$block_search = '';
		$block_login = '';
		$block_lang = '';
		$block_cart = '';
		$has_blocks = false;

		//search
		if (($this->tplparams['search'] != '') || ($this->tplparams['searchuri'] != '')) {
			$has_blocks = true;
			if ($this->tplparams['searchuri'] != '') {//custom search
				$action = $elxis->makeURL($this->tplparams['searchuri'], 'index.php', true, false);
				$par = $this->tplparams['searchpar'];
			} else {//search engine
				$action = $elxis->makeURL('search:/', 'index.php', true, false).$this->tplparams['search'].'.html';
				$par = 'q';
			}

			$q = isset($_GET[$par]) ? $_GET[$par] : '';
			$block_search .= '<form id="fmtpl5search455" class="tpl5_searchform" name="fmtpl5search" action="'.$action.'" method="get">';
			$block_search .= '<div><div class="tpl5_search_magn"><i class="fas fa-search"></i></div>';
			$block_search .= '<div class="tpl5_search_in"><input type="text" name="'.$par.'" value="'.$q.'" id="tpl5search_q455" class="tpl5_search_input" placeholder="'.$eLang->get('SEARCH').'" dir="'.$eLang->getinfo('DIR').'" required="required" /></div>';
			$block_search .= '</div>';
			$block_search .= '<button type="submit" name="b" class="tpl5_search_btn">'.$eLang->get('SEARCH').'</button>';
			$block_search .= '</form>';
		}

		if ($this->tplparams['login'] == 1) {
			$has_blocks = true;
			if ($elxis->user()->gid <> 7) {
				$block_login = $this->userProfile($elxis, $eLang);
			} else {
				$block_login = $this->userLogin($elxis, $eLang);
			}
		}

		//language
		if ($this->tplparams['langselect'] == 1) {
			if (($elxis->getConfig('SITELANGS') == '') || (strpos($elxis->getConfig('SITELANGS'), ',') !== false)) {
				$has_blocks = true;
				$lang = $eLang->currentLang();
				$infolangs = $eLang->getSiteLangs(true);
				$ssl = $eURI->detectSSL();

				$segs = $eURI->getSegments();
				$elxis_uri = $eURI->getComponent();
				if ($elxis_uri == 'content') { $elxis_uri = ''; }
				if ($segs) {
					$elxis_uri .= ($elxis_uri == '') ? implode('/', $segs) : ':'.implode('/', $segs);
					$n = count($segs) - 1;
					if (!preg_match('#\.#', $segs[$n])) { $elxis_uri .= '/'; }
				} else {
					$elxis_uri .= ($elxis_uri != '') ? '/' : '';
				}
				$qstr = '';
				if (isset($_SERVER['QUERY_STRING'])) {
					if ($_SERVER['QUERY_STRING'] != '') { $qstr = '?'.$_SERVER['QUERY_STRING']; }
				}
				$block_lang .= '<form name="tpl5langfm" id="tpl5langfm" method="get" action="#" class="tpl5_lang_form">'."\n";
				if (isset($infolangs[$lang])) {
					$block_lang .= '<img src="'.$elxis->secureBase().'/templates/five/images/flags/'.$lang.'.png" alt="'.$infolangs[$lang]['NAME'].' - '.$infolangs[$lang]['NAME_ENG'].'" title="'.$infolangs[$lang]['NAME'].' - '.$infolangs[$lang]['NAME_ENG'].'" /> ';
				}
				$block_lang .= '<select name="lang" class="tpl5_select_lang" id="tpl5_select_lang" onchange="tpl5SwitchLang();">'."\n";
				foreach ($infolangs as $lng => $info) {
					$selected = ($lang == $lng) ? ' selected="selected"' : '';
					$link = $elxis->makeURL($lng.':'.$elxis_uri, '', $ssl).$qstr;
					$block_lang .= '<option value="'.$lng.'"'.$selected.' data-act="'.$link.'">'.strtoupper($lng)."</option>\n";
				}
				$block_lang .= "</select>\n";
				$block_lang .= "</form>\n";
			}
		}

		if ($this->tplparams['cart'] == 1) {
			if (isset($_COOKIE['shopcart'])) {
				$helper = $this->loadgetShop();
				if (!$helper) {
					$cart_items = 0;
				} else {
					$sid = $helper->getSession();
					$cart_items = $this->countCartItems($sid);						
				}
			} else {
				$cart_items = 0;
			}
			$has_blocks = true;
			$shop_link = $elxis->makeURL('shop:/', 'index.php', true);
			$title = ($cart_items == 1) ? $eLang->get('TPL5_ITEM_INCART') : sprintf($eLang->get('TPL5_ITEMS_INCART'), $cart_items);
			$block_cart .= '<a href="'.$shop_link.'cart.html" title="'.$title.'"><i class="fas fa-shopping-cart"></i> '.$cart_items.'</a>';
		}

		if (!$has_blocks) {
			echo '<div class="tpl5_header_top_lineno"></div>'."\n";
			return;
		}

		//Display top blocks
		echo '<div class="tpl5_header_top_line">'."\n";
		echo '<div class="tpl5_container'.$this->containerSuffix().'">'."\n";
		echo '<div class="tpl5_header_top_linein">'."\n";
		if ($block_search != '') {
			echo '<div class="tpl5_search_box">';
			echo $block_search;
			echo "</div>\n";
		}
		if ($block_lang != '') {
			echo '<div class="tpl5_lang_box">';
			echo $block_lang;
			echo "</div>\n";
		}
		if ($block_cart != '') {
			echo '<div class="tpl5_cart_box">';
			echo $block_cart;
			echo "</div>\n";
		}
		if ($block_login != '') {
			echo '<div class="tpl5_login_box">';
			echo $block_login;
			echo "</div>\n";
		}
		echo '<div class="clear"></div>'."\n";
		echo "</div>\n";
		echo "</div>\n";
		echo "</div>\n";
	}


	/*************************/
	/* USER PROFILE / LOGOUT */
	/*************************/
	private function userProfile($elxis, $eLang) {
		if ($elxis->user()->gid != 6) {
			$fullname = eUTF::substr($elxis->user()->firstname, 0, 1).'. '.$elxis->user()->lastname;
			$utitle = $eLang->get('TPL5_MY_PROFILE');
			$ulink = $elxis->makeURL('user:members/myprofile.html');
		} else {
			if ($elxis->user()->firstname != '') {
				$fullname = $elxis->user()->firstname.' '.$elxis->user()->lastname;
			} else if ($elxis->user()->uname != '') {
				$fullname = $elxis->user()->uname;
			} else if ($elxis->user()->email != '') {
				$parts = preg_split('#\@#', $elxis->user()->email, -1, PREG_SPLIT_NO_EMPTY);
				$fullname = $parts[0];
			} else {
				$fullname = $eLang->get('USER');
			}
			$utitle = $eLang->get('TPL5_USERS_CENTRAL');
			$ulink = $elxis->makeURL('user:/');
		}

		$logout_link = $elxis->makeURL('user:ilogout', 'inner.php', true, false);
		$redirect_link = $elxis->makeURL();
		$avatar = $elxis->obj('avatar')->getAvatar($elxis->user()->avatar, 48, 0, $elxis->user()->email);

		$html = '<div class="tpl5_logout_wrap">'."\n";
		$html .= '<a href="javascript:void(null);" title="'.$eLang->get('LOGOUT').'" class="tpl5_logout" onclick="tpl5Logout(\''.$logout_link.'\', \''.$redirect_link.'\');"><i class="fas fa-sign-out-alt"></i></a>'."\n";
		$html .= '<a href="'.$ulink.'" class="tpl5_logout_prof" title="'.$utitle.'"><img src="'.$avatar.'" alt="user" /> '.$fullname.'</a>';
		$html .= "</div>\n";
		return $html;
	}


	/**************/
	/* USER LOGIN */
	/**************/
	private function userLogin($elxis, $eLang) {
		$token = trim(eFactory::getSession()->get('token_loginform')); //check if another login module has already set the token
		if ($token == '') {
			$token = md5(uniqid(rand(), true));
			eFactory::getSession()->set('token_loginform', $token);
		}

		elxisLoader::loadFile('includes/libraries/elxis/form5.class.php');
		$htmlHelper = $elxis->obj('html');
		$action = $elxis->makeURL('user:ilogin', 'inner.php', true, false);

		$html = '<div class="tpl5_login_wrap">'."\n";
		$html .= '<a href="javascript:void(null);" title="'.$eLang->get('LOGIN').'" class="tpl5_login" onclick="elx5ModalOpen(\'tplog\');"><i class="fas fa-user"></i> '.$eLang->get('LOGIN').'</a>'."\n";
		$html .= "</div>\n";

		$html .= $htmlHelper->startModalWindow('<i class="fas fa-user"></i> '.$eLang->get('LOGIN'), 'tplog', '', false, '', '');
		$form = new elxis5Form(array('idprefix' => 'tplog', 'labelclass' => 'elx5_label', 'sideclass' => 'elx5_labelside', 'returnhtml' => true));
		$html .= $form->openForm(array('name' => 'tpl5loginform', 'method' =>'post', 'action' => $action, 'id' => 'tpl5loginform', 'onsubmit' => 'tpl5Login(); return false;'));
		$html .= $form->addText('uname', '', $eLang->get('USERNAME'), array('required' => 'required', 'dir' => 'ltr', 'autocomplete' => 'off', 'class' => 'elx5_text elx5_modlogin_uname'));
		$html .= $form->addPassword('pword', '', $eLang->get('PASSWORD'), array('required' => 'required', 'maxlength' => 60, 'autocomplete' => 'off', 'class' => 'elx5_text elx5_modlogin_pword'));
		$html .= $form->addHidden('modtoken', $token);
		$html .= $form->addButton('sublogin', $eLang->get('LOGIN'), 'submit', array('class' => 'elx5_btn', 'data-waitlng' => $eLang->get('PLEASE_WAIT'), 'data-loginlng' => $eLang->get('LOGIN'), 'sidepad' => 1));
		if ($elxis->getConfig('REGISTRATION') == 1) {
			$link = $elxis->makeURL('user:register.html', '', true, false);
			$html .= '<div class="tpl5_regprompt">'.$eLang->get('TPL5_NOACCOUNT').' <a href="'.$link.'" title="'.$eLang->get('TPL5_REGISTER').'">'.$eLang->get('TPL5_REGISTER').'</a></div>'."\n";
		}
		$html .= $form->closeForm();
		$html .= $htmlHelper->endModalWindow(false);

		return $html;
	}


	/*********************************************************************/
	/* IF FRONTPAGE, MAKE SLIDER OR DISPLAY MODULES POSITION ON BG IMAGE */
	/*********************************************************************/
	public function makeSliderHeaderModules() {
		if (!$this->is_frontpage) { return; }
		if ($this->tplparams['sliderimage'] == 2) { return; } //display nothing
		if ($this->tplparams['sliderimage'] == 1) { //display static image
			if ($this->tplparams['fpimgpos'] != '') {
				$eDoc = eFactory::getDocument();
				if ($eDoc->countModules($this->tplparams['fpimgpos']) > 0) {
					echo '<div class="tpl5_container'.$this->containerSuffix().'"><div class="tpl5_slidermodule">'."\n";
					$eDoc->modules($this->tplparams['fpimgpos'], 'none');
					echo "</div></div>\n";
				}
			}
			return;
		}
		if (!$this->tplparams['slider']) { return; }

		$eDoc = eFactory::getDocument();
		$tplbase = eFactory::getElxis()->secureBase().'/templates/five/';

		$eDoc->addStyleLink($tplbase.'css/glide.core.min.css');
		$eDoc->addStyleLink($tplbase.'css/glide.theme.min.css');
		$eDoc->addScriptLink($tplbase.'includes/glide.min.js');

		echo '<div class="tpl5_slider">';
		echo '<div class="glide__track" data-glide-el="track">';
		echo '<ul class="glide__slides" id="tpl5sl">';
		foreach ($this->tplparams['slider'] as $k => $item) {
			if ($item['link'] != '') {
				echo '<li class="glide__slide"><a href="'.$item['link'].'"><img src="'.$item['image'].'" alt="image"></a></li>';
			} else {
				echo '<li class="glide__slide"><img src="'.$item['image'].'" alt="image"></li>';
			}
		}
		echo "</ul>";
		echo "</div>\n";
		echo '<div class="glide__arrows" data-glide-el="controls">'."\n";
		echo '<button class="glide__arrow glide__arrow--left" data-glide-dir="<">&lt;</button>'."\n";
		echo '<button class="glide__arrow glide__arrow--right" data-glide-dir=">">&gt;</button>'."\n";
		echo "</div>\n";

		$caption = '';
		$capclass = 'elx5_invisible';
		if ($this->tplparams['slider'][0]['caption'] != '') {
			if (strpos($this->tplparams['slider'][0]['caption'], '@') !== false) {
				$caption = str_replace('@', '<span>', $this->tplparams['slider'][0]['caption']).'</span>';
			} else {
				$caption = $this->tplparams['slider'][0]['caption'];
			}
			$capclass = 'tpl5_captionswrap';
		}
		if ($this->tplparams['sltitle'] != '') {
			echo '<div class="tpl5_slidertitle">'.$this->tplparams['sltitle'].'</div>'."\n";
		}
		echo '<div class="'.$capclass.'" id="tpl5_captionswrap"><div class="tpl5_caption" id="tpl5_caption">'.$caption.'</div></div>'."\n";
		echo "</div>\n";

		echo '<div class="elx5_invisible">'."\n";
		foreach ($this->tplparams['slider'] as $k => $item) {
			if ($item['caption'] == '') { continue; }
			if (strpos($item['caption'], '@') !== false) {
				$caption = str_replace('@', '<span>', $item['caption']).'</span>';
			} else {
				$caption = $item['caption'];
			}
			echo '<div class="elx5_invisible" id="tpl5_slider_caption'.$k.'">'.$caption."</div>\n";
		}
		echo "</div>\n";

		$dir = (eFactory::getLang()->getinfo('DIR') == 'rtl') ? 'rtl' : 'ltr';
?>
		<script>
			var tpl5glide = new Glide('.tpl5_slider', { type: 'carousel', paddings: 0, startAt: 0, gap: 0, autoplay: 5000, animationDuration: 1000, hoverpause: false, bound: true, direction: '<?php echo $dir; ?>' });
			tpl5glide.mount();
			tpl5glide.on('run.after', function() {
				if (document.getElementById('tpl5_slider_caption'+tpl5glide.index)) {
					document.getElementById('tpl5_caption').innerHTML = document.getElementById('tpl5_slider_caption'+tpl5glide.index).innerHTML;
					document.getElementById('tpl5_captionswrap').className = 'tpl5_captionswrap';
				} else {
					document.getElementById('tpl5_captionswrap').className = 'elx5_invisible';
					document.getElementById('tpl5_caption').innerHTML = '';
				}
			});
		</script>

<?php 
	}


	/*****************************/
	/* LOAD/GET OPEN SHOP HELPER */
	/*****************************/
	private function loadgetShop() {
		if (eRegistry::isLoaded('shophelper')) { return eRegistry::get('shophelper'); }
		if (!file_exists(ELXIS_PATH.'/components/com_shop/includes/helper.class.php')) { return false; }
		elxisLoader::loadFile('components/com_shop/includes/helper.class.php');
		eRegistry::set(new shopHelper(true), 'shophelper');
		return eRegistry::get('shophelper');
	}


	/******************************/
	/* COUNT OPEN SHOP CART ITEMS */
	/******************************/
	private function countCartItems($sid) {
		$db = eFactory::getDB();

		$sql = "SELECT COUNT(".$db->quoteId('cartid').") FROM ".$db->quoteId('#__shop_cart')." WHERE ".$db->quoteId('session_id')." = :xsid";
		$stmt = $db->prepareLimit($sql, 0, 1);
		$stmt->bindParam(':xsid', $sid, PDO::PARAM_STR);
		$stmt->execute();
		$num = (int)$stmt->fetchResult();
		return $num;
	}


	/***********************/
	/* PATHWAY CUSTOM HTML */
	/***********************/
	private function pathwayHTML() {
		$nodes = eFactory::getPathway()->getNodes();
		if (!$nodes) { return ''; }

		$elxis = eFactory::getElxis();

		$html = '<nav class="tpl5_pathway">'."\n";
		$html .= '<ul>'."\n";
		foreach ($nodes as $idx => $node) {
			if ($node->link == '') {
				$html .= '<li>'.$node->title."</li>\n";
			} else {
				$elxuri = ($node->link == '/') ? '' : $node->link;
				$link = $elxis->makeURL($elxuri, 'index.php', $node->ssl);
				$html .= '<li><a href="'.$link.'" title="'.$node->title.'">'.$node->title."</a></li>\n";
			}
		}
		$html .= "</ul>\n";
		$html .= "</nav>\n";
		return $html;
	}


	/***************/
	/* MARQUEE HTML */
	/***************/
	private function marqueeHTML() {
		if ($this->tplparams['marquee'] == '') { return; }
		$tplbase = eFactory::getElxis()->secureBase().'/templates/five/';
		$reverse = (eFactory::getLang()->getinfo('DIR') == 'rtl') ? '1' : '0';
		if ($this->tplparams['marqspeed'] < 1) {
			$speed = '0.70';
		} else {
			$speed = ($this->tplparams['marqspeed'] / 10);
			$speed = number_format($speed, 2, '.', '');
		}
?>
		<div class="tpl5_marquee_wrap">
			<div class="tpl5_container<?php echo $this->containerSuffix(); ?>">
				<div class="tpl5_marquee_container">
					<div class="tpl5_marquee" data-speed="<?php echo $speed; ?>" data-reverse="<?php echo $reverse; ?>" data-pausable="1"><div><?php echo $this->tplparams['marquee']; ?> </div></div>
				</div>
			</div>
		</div>
		<script src="<?php echo $tplbase; ?>includes/marquee3k.js"></script>
		<script>Marquee3k.init({ 'selector': 'tpl5_marquee' });</script>

<?php 
	}


	/***********************************************/
	/* DISPLAY PATHWAY OR MARQUEE OR VERTICAL SPACE */
	/***********************************************/
	public function pathwayMarqueeSpace() {
		if ($this->tplparams['pathway'] == 0) {
			echo '<div class="tpl5_shadow_space"></div>'."\n";
			return;
		}

		if ($this->tplparams['pathway'] == 1) {
			$html = $this->pathwayHTML();
			if ($html == '') {
				echo '<div class="tpl5_shadow_space"></div>'."\n";
				return;
			}
			echo '<div class="tpl5_pathwrap"><div class="tpl5_container'.$this->containerSuffix().'">'."\n";
			echo $html;
			echo "\n</div></div>\n";
			return;
		}

		if ($this->tplparams['pathway'] == 2) {
			if (!$this->is_frontpage) {
				$html = $this->pathwayHTML();
				if ($html == '') {
					echo '<div class="tpl5_shadow_space"></div>'."\n";
					return;
				}
				echo '<div class="tpl5_pathwrap"><div class="tpl5_container'.$this->containerSuffix().'">'."\n";
				echo $html;
				echo "\n</div></div>\n";
			} else {
				echo '<div class="tpl5_shadow_space"></div>'."\n";
			}
			return;
		}

		if ($this->tplparams['pathway'] == 3) {
			if ($this->is_frontpage) {
				$this->marqueeHTML();
			} else {
				$html = $this->pathwayHTML();
				if ($html == '') {
					echo '<div class="tpl5_shadow_space"></div>'."\n";
					return;
				}
				echo '<div class="tpl5_pathwrap"><div class="tpl5_container'.$this->containerSuffix().'">'."\n";
				echo $html;
				echo "\n</div></div>\n";
			}
			return;
		}

		if ($this->tplparams['pathway'] == 4) {
			if ($this->is_frontpage) {
				$this->marqueeHTML();
			} else {
				echo '<div class="tpl5_shadow_space"></div>'."\n";
			}
			return;
		}

		$this->marqueeHTML();
	}


	/*************************/
	/* SHOW THE SIDE COLUMN? */
	/*************************/
	public function showColumn() {
		return $this->sidecolumn;
	}


	/******************************************************************/
	/* TEMPLATE CONTAINER SUFFIX (1200/1300/1400/1500/1600px or 100%) */
	/******************************************************************/
	public function containerSuffix() {
		switch ($this->tplparams['tplwidth']) {
			case 1: $sfx = '1'; break;
			case 2: $sfx = '2'; break;
			case 3: $sfx = '3'; break;
			case 4: $sfx = '4'; break;
			case 5: $sfx = '5'; break;
			case 0: default: $sfx = ''; break;
		}
		return $sfx;
	}


	/***************************/
	/* SHOW SIDE COLUMN BLOCKS */
	/***************************/
	public function sideBlocks($eDoc) {
		$eDoc->modules('left');
		$eDoc->modules('right');
	}


	private function getArticleCategory($id) {
		$db = eFactory::getDB();

		$sql = "SELECT ".$db->quoteId('catid')." FROM ".$db->quoteId('#__content')." WHERE ".$db->quoteId('id')." = :xid";
		$stmt = $db->prepareLimit($sql, 0, 1);
		$stmt->bindParam(':xid', $id, PDO::PARAM_INT);
		$stmt->execute();
		$catid = (int)$stmt->fetchResult();
		return $catid;
	}


	/********************************************/
	/* DISPLAY COPYRIGHT, SOCIAL ICONS AND MORE */
	/********************************************/
	public function footerCopyIcons() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$socials = array(
			'facebook' => 'fab fa-facebook-f',
			'twitter' => 'fab fa-twitter', 
			'youtube' => 'fab fa-youtube', 
			'flickr' => 'fab fa-flickr', 
			'instagram' => 'fab fa-instagram', 
			'linkedin' => 'fab fa-linkedin-in', 
			'pinterest' => 'fab fa-pinterest', 
			'tumblr' => 'fab fa-tumblr', 
			'tripadvisor' => 'fab fa-tripadvisor'
		);

		$icons_html = '';
		if ($this->tplparams['contact'] != '') {
			$link = $elxis->makeURL($this->tplparams['contact']);
			$icons_html .= '<a href="'.$link.'" title="'.$eLang->get('CONTACT').'"><i class="fas fa-envelope"></i></a>';
		}
		if ($this->tplparams['phone'] != '') {
			$phone = preg_replace('@[\s]@', '', $this->tplparams['phone']);
			$icons_html .= '<a href="tel:'.$phone.'" title="'.$eLang->get('TELEPHONE').': '.$this->tplparams['phone'].'"><i class="fas fa-phone"></i></a>';
		}
		foreach ($socials as $social => $iconclass) {
			if ($this->tplparams[$social] != '') {
				$title = ucfirst($social);
				$icons_html .= '<a href="'.$this->tplparams[$social].'" title="'.$title.'" target="_blank"><i class="'.$iconclass.'"></i></a>';
			}
		}
		if ($this->tplparams['rss'] == 1) {
			$link = $elxis->makeURL('content:feeds.html');
			$icons_html .= '<a href="'.$link.'" title="RSS"><i class="fas fa-rss"></i></a>';
		}
		if ($this->tplparams['sitemap'] == 1) {
			$title = $eLang->get('SITEMAP');
			$link = $elxis->makeURL('sitemap:/');
			$icons_html .= '<a href="'.$link.'" title="'.$title.'"><i class="fas fa-sitemap"></i></a>';
		}

		$copyright_html = '';
		if ($this->tplparams['copyright'] == 1) {
			if (trim($this->tplparams['copyrighttxt']) == '') {
				$copyright_html = '&copy; '.gmdate('Y').' elxis.org - powered by <a href="https://www.elxis.org" title="Elxis CMS">Elxis CMS</a>';
			} else {
				$copyright_html = $this->tplparams['copyrighttxt'];
			}
		}

		if (($copyright_html == '') && ($icons_html == '')) { return; }

		echo '<div class="tpl5_footer_copyicons">';
		if (($copyright_html != '') && ($icons_html != '')) {
			echo '<div class="tpl5_footer_copy">'.$copyright_html.'</div>'."\n";
			echo '<div class="tpl5_footer_icons">'.$icons_html.'</div>'."\n";
		} else if ($copyright_html != '') {
			echo '<div class="tpl5_footer_copy tpl5_footer_cisingle">'.$copyright_html.'</div>'."\n";
		} else {
			echo '<div class="tpl5_footer_icons tpl5_footer_cisingle">'.$icons_html.'</div>'."\n";
		}
		echo "</div>\n";
	}


	/**************************/
	/* DISPLAY FOOTER MODULES */
	/**************************/
	public function footerMods() {
		$eDoc = eFactory::getDocument();

		$mods1 = $eDoc->countModules('user1');
		$mods2 = $eDoc->countModules('user2');
		$mods3 = $eDoc->countModules('user3');

		$cols = 0;
		if ($mods1 > 0) { $cols++; }
		if ($mods2 > 0) { $cols++; }
		if ($mods3 > 0) { $cols++; }
		if ($cols == 0) { return; }

		if ($cols == 3) {
			$class = 'tpl5_fmods_33';
		} else if ($cols == 2) {
			$class = 'tpl5_fmods_50';
		} else {
			$class = 'tpl5_fmods_100';
		}

		$q = 1;
		echo '<div class="tpl5_fmods" id="tpl5_fmods'.$q.'">'."\n";
		if ($mods1 > 0) {
			echo '<div class="'.$class.'">'."\n";
			$eDoc->modules('user1');
			echo "</div>\n";
			$q++;
		}
		if ($mods2 > 0) {
			echo '<div class="'.$class.'" id="tpl5_fmods'.$q.'">'."\n";
			$eDoc->modules('user2');
			echo "</div>\n";
			$q++;
		}
		if ($mods3 > 0) {
			echo '<div class="'.$class.'" id="tpl5_fmods'.$q.'">'."\n";
			$eDoc->modules('user3');
			echo "</div>\n";
		}
		if ($cols > 1) {
			echo '<div class="clear"></div>'."\n";
		}
		echo "</div>\n";
	}

}

?>