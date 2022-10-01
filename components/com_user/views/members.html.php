<?php 
/**
* @version		$Id: members.html.php 2431 2022-01-18 19:26:35Z IOS $
* @package		Elxis
* @subpackage	User component
* @copyright	Copyright (c) 2006-2022 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class membersUserView extends userView {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/*********************/
	/* SHOW MEMBERS LIST */
	/*********************/
	public function membersList($rows, $columns, $options, $nav_links, $members_ordering, $params) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$eDate = eFactory::getDate();
		$eDoc = eFactory::getDocument();

		$baseLink = $elxis->makeURL('user:members/');

		$avatarpath = 'media/images/avatars/';
		if (defined('ELXIS_MULTISITE')) {
			if (ELXIS_MULTISITE > 1) { $avatarpath = 'media/images/site'.ELXIS_MULTISITE.'/avatars/'; }
		}
		$defavatar = $elxis->secureBase().'/components/com_user/images/noavatar.png';
		$acl_profile = $elxis->acl()->check('com_user', 'profile', 'view');
		$members_link = $elxis->makeURL('user:members/');
		$time = $eDate->getTS() - $elxis->getConfig('SESSION_LIFETIME');

		if ($elxis->acl()->getLevel() <= 2) {//users send pms access check applies
			$usersendpms = (int)$params->get('usersendpms', 2);
		} else {
			$usersendpms = 2;//allowed to all
		}

		include(ELXIS_PATH.'/includes/libraries/elxis/language/langdb.php');

		$lng = $eLang->getinfo('LANGUAGE');
		if (file_exists(ELXIS_PATH.'/includes/libraries/elxis/form/countries.'.$lng.'.php')) {
			include(ELXIS_PATH.'/includes/libraries/elxis/form/countries.'.$lng.'.php');
		} else {
			include(ELXIS_PATH.'/includes/libraries/elxis/form/countries.en.php');
		}

		if ($options['page'] > 1) {
			echo '<h1>'.$eLang->get('MEMBERSLIST').' <span>'.$eLang->get('PAGE').' '.$options['page']."</h1>\n";
		} else {
			echo '<h1>'.$eLang->get('MEMBERSLIST')."</h1>\n";
		}

		echo '<p>'.sprintf($eLang->get('REGMEMBERSTOTAL'), '<strong>'.$options['total'].'</strong>');
		if ($options['maxpage'] > 1) {
			echo ' ';
			printf($eLang->get('PAGEOF'), '<strong>'.$options['page'].'</strong>', '<strong>'.$options['maxpage'].'</strong>');
		}
		echo "</p>\n";

		if ($eDoc->countModules('user_memberstop') > 0) {
			echo '<div class="user_memberstop_mods">'."\n";
			$eDoc->modules('user_memberstop');
			echo "</div>\n";
		}

		if ($options['maxpage'] > 1) {
			$linkbase = $elxis->makeURL('user:members/?order='.$options['order']);
			$navigation = $elxis->obj('html')->pagination($linkbase, $options['page'], $options['maxpage']);
			if (($nav_links == 0) || ($nav_links == 2)) { echo '<div class="elx5_dspace">'.$navigation."</div>\n"; }
		}

		if (($members_ordering == 1) && ($options['total'] > 2)) {
			$this->membersOrdering($options, $columns, $elxis, $eLang);
		}

		echo '<ul class="elx5_user_members">'."\n";
		foreach ($rows as $row) {
			$avatar = $defavatar;
			if ($row->avatar != '') {
				if (preg_match('#^(http(s)?\:\/\/)#i', $row->avatar)) {
					$avatar = $row->avatar;
				} else if (file_exists(ELXIS_PATH.'/'.$avatarpath.$row->avatar)) {
					$avatar = $elxis->secureBase().'/'.$avatarpath.$row->avatar;
				}
			}

			echo '<li>'."\n";
			echo '<div class="elx5_user_members_side">';
			if (($acl_profile == 2) || (($acl_profile == 1) && ($elxis->user()->uid == $row->uid))) {
				echo '<a href="'.$members_link.$row->uid.'.html" title="'.$row->firstname.' '.$row->lastname.' : '.$eLang->get('PROFILE').'"><img src="'.$avatar.'" alt="'.$row->firstname.' '.$row->lastname.'" /></a>';
			} else {
				echo '<img src="'.$avatar.'" alt="'.$row->firstname.' '.$row->lastname.'" />';
			}
			if ($row->uid == $elxis->user()->uid) {
				echo '<div class="elx5_user_members_online" title="'.$eLang->get('ONLINE').'">online</div>'."\n";
			} else if ($row->last_activity >= $time) {
				echo '<div class="elx5_user_members_online" title="'.$eLang->get('ONLINE').'">online</div>'."\n";
			} else {
				echo '<div class="elx5_user_members_offline" title="'.$eLang->get('OFFLINE').'">offline</div>'."\n";
			}
			echo "</div>\n";//.elx5_user_members_side end

			echo '<div class="elx5_user_members_main">';
			if (($acl_profile == 2) || (($acl_profile == 1) && ($elxis->user()->uid == $row->uid))) {
				echo '<h4><a href="'.$members_link.$row->uid.'.html" title="'.$row->firstname.' '.$row->lastname.' : '.$eLang->get('PROFILE').'">'.$row->firstname.' '.$row->lastname.'</a></h4>';
			} else {
				echo '<h4>'.$row->firstname.' '.$row->lastname.'</h4>';
			}

			$txt = '';
			if ($columns['address'] == 1) {
				if ($row->address != '') { $txt .= ', '.$row->address; }
			}
			if ($columns['pcode'] == 1) {
				if ($row->postalcode != '') { $txt .= ', '.$row->postalcode; }
			}
			if ($columns['city'] == 1) {
				if ($row->city != '') { $txt .= ', '.$row->city; }
			}
			if ($columns['country'] == 1) {
				$country = $row->country;
				if (isset($countries[$country])) {
					$txt .= ', '.$countries[$country];
				} else {
					$txt .= ', '.$row->country;
				}
				unset($webscountryite);
			}
			if ($columns['preflang'] == 1) {
				if (trim($row->preflang) != '') {
					if (isset($langdb[ $row->preflang ])) {
						if ($lng == $row->preflang) {
							$txt .= ', '. $langdb[ $row->preflang ]['NAME'];
						} else {
							$txt .= ', '. $langdb[ $row->preflang ]['NAME_ENG'];
						}
					} else {
						$txt .= ', '. $row->preflang;
					}
				}
			}
			if ($columns['phone'] == 1) {
				if ($row->phone != '') { $txt .= ', '.$row->phone; }
			}
			if ($columns['mobile'] == 1) {
				if ($row->mobile != '') { $txt .= ', '.$row->mobile; }
			}
			if ($columns['email'] == 1) {
				if ($row->email != '') { $txt .= ', '.$row->email; }
			}
			if ($columns['website'] == 1) {
				$website = trim($row->website);
				if (($website != '') && filter_var($website, FILTER_VALIDATE_URL)) {
					$parsed = parse_url($website);
					$domain = preg_replace('@^(www\.)@i', '', $parsed['host']);
					$txt .= ', <a href="'.$website.'" target="_blank" title="'.$domain.'">'.$domain.'</a>';
					unset($parsed);
				}
				unset($website);
			}

			$can_send_pms = false;
			if ($usersendpms == 2) {
				$can_send_pms = true;
			} else if ($usersendpms == 1) {
				if (($row->gid == 1) || ($row->gid == 2)) { $can_send_pms = true; }
			}

			echo '<div class="elx5_user_members_info" dir="ltr">';
			if (($row->uid == $elxis->user()->uid) || !$can_send_pms) {
				echo '@'.$row->uname;
			} else {
				echo '<a href="javascript:void(null);" onclick="elx5UserPMSOpen(0, '.$row->uid.', \''.addslashes($row->firstname.' '.$row->lastname).'\');" title="'.$eLang->get('SEND_MESSAGE').'">@'.$row->uname.'</a>';
			}
			echo $txt.'</div>';

			echo '<div class="elx5_user_members_boxes">';
			if ($columns['groupname'] == 1) {
				$txt = $this->base_translateGroup($row->groupname, $row->gid);
				echo '<div class="elx5_user_members_box"><span class="elx5_user_members_boxtitle">'.$eLang->get('GROUP').'</span><span class="elx5_user_members_boxvalue">'.$txt.'</span></div>';
			}
			if ($columns['gender'] == 1) {
				$txt = '-';
				if (trim($row->gender) != '') {
					$txt = ($row->gender == 'female') ? $eLang->get('FEMALE') : $eLang->get('MALE');
				}
				echo '<div class="elx5_user_members_box"><span class="elx5_user_members_boxtitle">'.$eLang->get('GENDER').'</span><span class="elx5_user_members_boxvalue">'.$txt.'</span></div>';
			}

			if ($columns['registerdate'] == 1) {
				echo '<div class="elx5_user_members_box"><span class="elx5_user_members_boxtitle">'.$eLang->get('REGDATE_SHORT').'</span><span class="elx5_user_members_boxvalue">'.$eDate->formatDate($row->registerdate, $eLang->get('DATE_FORMAT_2')).'</span></div>';
			}
			if ($columns['lastvisitdate'] == 1) {
				$txt = ((trim($row->lastvisitdate) != '') && ($row->lastvisitdate != '1970-01-01 00:00:00')) ? $eDate->formatDate($row->lastvisitdate, $eLang->get('DATE_FORMAT_4')) : $eLang->get('NEVER');
				echo '<div class="elx5_user_members_box"><span class="elx5_user_members_boxtitle">'.$eLang->get('LASTVISIT').'</span><span class="elx5_user_members_boxvalue">'.$txt.'</span></div>';
			}
			if ($columns['profile_views'] == 1) {
				echo '<div class="elx5_user_members_box"><span class="elx5_user_members_boxtitle">'.$eLang->get('PROFILE_VIEWS').'</span><span class="elx5_user_members_boxvalue">'.$row->profile_views.'</span></div>';
			}
			echo '</div>';//.elx5_user_members_boxes end

			echo '</div>';//.elx5_user_members_main end
			//echo '<div class="clear"></div>'."\n";
			echo '</li>'."\n";
		}
		echo "</ul>\n";

		if ($options['maxpage'] > 1) {
			if (($nav_links == 1) || ($nav_links == 2)) { echo '<div class="elx5_vspace">'.$navigation."</div>\n"; }
			unset($navigation);
		}

		if (($members_ordering == 2) && ($options['total'] > 2)) {
			$this->membersOrdering($options, $columns, $elxis, $eLang);
		}

		if ($eDoc->countModules('user_membersbottom') > 0) {
			echo '<div class="user_membersbottom_mods">'."\n";
			$eDoc->modules('user_membersbottom');
			echo "</div>\n";
		}

		$link = $elxis->makeURL('user:/');
		echo '<div class="elx5_dspace"><a href="'.$link.'" class="elx5_btn elx5_ibtn"><i class="fas fa-bars"></i> '.$eLang->get('USERSCENTRAL')."</a></div>\n";


		$this->base_pmsform($elxis, $eLang, $params);
	}


	/******************************/
	/* MEMBERS LIST ORDERING FORM */
	/******************************/
	private function membersOrdering($options, $columns, $elxis, $eLang) {
		$action = $elxis->makeURL('user:members/', '', true);

		echo '<div class="elx5_user_members_order">'."\n";
		echo '<form name="fmuserord" method="get" action="'.$action.'" class="elx5_form">'."\n";
		echo '<div class="elx5_zero">'."\n";
		echo '<label for="membersorder" class="elx5_label">'.$eLang->get('ORDERING')."</label>\n";
		echo '<div class="elx5_labelside">'."\n";
		echo '<select name="order" id="membersorder" onchange="this.form.submit()" class="elx5_select">'."\n";
		$sel = ($options['order'] == 'fa') ? ' selected="selected"' : '';
		echo '<option value="fa"'.$sel.'>'.$eLang->get('FIRSTNAME')." &#8593;</option>\n";
		$sel = ($options['order'] == 'fd') ? ' selected="selected"' : '';
		echo '<option value="fd"'.$sel.'>'.$eLang->get('FIRSTNAME')." &#8595;</option>\n";
		$sel = ($options['order'] == 'la') ? ' selected="selected"' : '';
		echo '<option value="la"'.$sel.'>'.$eLang->get('LASTNAME')." &#8593;</option>\n";
		$sel = ($options['order'] == 'ld') ? ' selected="selected"' : '';
		echo '<option value="ld"'.$sel.'>'.$eLang->get('LASTNAME')." &#8595;</option>\n";
		$sel = ($options['order'] == 'ua') ? ' selected="selected"' : '';
		echo '<option value="ua"'.$sel.'>'.$eLang->get('USERNAME')." &#8593;</option>\n";
		$sel = ($options['order'] == 'ud') ? ' selected="selected"' : '';
		echo '<option value="ud"'.$sel.'>'.$eLang->get('USERNAME')." &#8595;</option>\n";
		if ($columns['preflang'] == 1) {
			$sel = ($options['order'] == 'pa') ? ' selected="selected"' : '';
			echo '<option value="pa"'.$sel.'>'.$eLang->get('LANGUAGE')." &#8593;</option>\n";
			$sel = ($options['order'] == 'pd') ? ' selected="selected"' : '';
			echo '<option value="pd"'.$sel.'>'.$eLang->get('LANGUAGE')." &#8595;</option>\n";
		}
		if ($columns['country'] == 1) {
			$sel = ($options['order'] == 'ca') ? ' selected="selected"' : '';
			echo '<option value="ca"'.$sel.'>'.$eLang->get('COUNTRY')." &#8593;</option>\n";
			$sel = ($options['order'] == 'cd') ? ' selected="selected"' : '';
			echo '<option value="cd"'.$sel.'>'.$eLang->get('COUNTRY')." &#8595;</option>\n";
		}
		if ($columns['city'] == 1) {
			$sel = ($options['order'] == 'cia') ? ' selected="selected"' : '';
			echo '<option value="cia"'.$sel.'>'.$eLang->get('CITY')." &#8593;</option>\n";
			$sel = ($options['order'] == 'cid') ? ' selected="selected"' : '';
			echo '<option value="cid"'.$sel.'>'.$eLang->get('CITY')." &#8595;</option>\n";
		}
		if ($columns['pcode'] == 1) {
			$sel = ($options['order'] == 'pca') ? ' selected="selected"' : '';
			echo '<option value="pca"'.$sel.'>'.$eLang->get('POSTAL_CODE')." &#8593;</option>\n";
			$sel = ($options['order'] == 'pcd') ? ' selected="selected"' : '';
			echo '<option value="pcd"'.$sel.'>'.$eLang->get('POSTAL_CODE')." &#8595;</option>\n";
		}
		if ($columns['address'] == 1) {
			$sel = ($options['order'] == 'aa') ? ' selected="selected"' : '';
			echo '<option value="aa"'.$sel.'>'.$eLang->get('ADDRESS')." &#8593;</option>\n";
			$sel = ($options['order'] == 'ad') ? ' selected="selected"' : '';
			echo '<option value="ad"'.$sel.'>'.$eLang->get('ADDRESS')." &#8595;</option>\n";
		}
		if ($columns['phone'] == 1) {
			$sel = ($options['order'] == 'pha') ? ' selected="selected"' : '';
			echo '<option value="pha"'.$sel.'>'.$eLang->get('TELEPHONE')." &#8593;</option>\n";
			$sel = ($options['order'] == 'phd') ? ' selected="selected"' : '';
			echo '<option value="phd"'.$sel.'>'.$eLang->get('TELEPHONE')." &#8595;</option>\n";
		}
		if ($columns['mobile'] == 1) {
			$sel = ($options['order'] == 'moa') ? ' selected="selected"' : '';
			echo '<option value="moa"'.$sel.'>'.$eLang->get('MOBILE')." &#8593;</option>\n";
			$sel = ($options['order'] == 'mod') ? ' selected="selected"' : '';
			echo '<option value="mod"'.$sel.'>'.$eLang->get('MOBILE')." &#8595;</option>\n";
		}
		if ($columns['email'] == 1) {
			$sel = ($options['order'] == 'ema') ? ' selected="selected"' : '';
			echo '<option value="ema"'.$sel.'>'.$eLang->get('EMAIL')." &#8593;</option>\n";
			$sel = ($options['order'] == 'emd') ? ' selected="selected"' : '';
			echo '<option value="emd"'.$sel.'>'.$eLang->get('EMAIL')." &#8595;</option>\n";
		}
		if ($columns['website'] == 1) {
			$sel = ($options['order'] == 'wa') ? ' selected="selected"' : '';
			echo '<option value="wa"'.$sel.'>'.$eLang->get('WEBSITE')." &#8593;</option>\n";
			$sel = ($options['order'] == 'wd') ? ' selected="selected"' : '';
			echo '<option value="wd"'.$sel.'>'.$eLang->get('WEBSITE')." &#8595;</option>\n";
		}
		if ($columns['gender'] == 1) {
			$sel = ($options['order'] == 'gea') ? ' selected="selected"' : '';
			echo '<option value="gea"'.$sel.'>'.$eLang->get('GENDER')." &#8593;</option>\n";
			$sel = ($options['order'] == 'ged') ? ' selected="selected"' : '';
			echo '<option value="ged"'.$sel.'>'.$eLang->get('GENDER')." &#8595;</option>\n";
		}
		if ($columns['registerdate'] == 1) {
			$sel = ($options['order'] == 'ra') ? ' selected="selected"' : '';
			echo '<option value="ra"'.$sel.'>'.$eLang->get('REGDATE_SHORT')." &#8593;</option>\n";
			$sel = ($options['order'] == 'rd') ? ' selected="selected"' : '';
			echo '<option value="rd"'.$sel.'>'.$eLang->get('REGDATE_SHORT')." &#8595;</option>\n";
		}
		if ($columns['lastvisitdate'] == 1) {
			$sel = ($options['order'] == 'lva') ? ' selected="selected"' : '';
			echo '<option value="lva"'.$sel.'>'.$eLang->get('LASTVISIT')." &#8593;</option>\n";
			$sel = ($options['order'] == 'lvd') ? ' selected="selected"' : '';
			echo '<option value="lvd"'.$sel.'>'.$eLang->get('LASTVISIT')." &#8595;</option>\n";
		}
		if ($columns['profile_views'] == 1) {
			$sel = ($options['order'] == 'pva') ? ' selected="selected"' : '';
			echo '<option value="pva"'.$sel.'>'.$eLang->get('PROFILE_VIEWS')." &#8593;</option>\n";
			$sel = ($options['order'] == 'pvd') ? ' selected="selected"' : '';
			echo '<option value="pvd"'.$sel.'>'.$eLang->get('PROFILE_VIEWS')." &#8595;</option>\n";
		}
		if ($columns['groupname'] == 1) {
			$sel = ($options['order'] == 'ga') ? ' selected="selected"' : '';
			echo '<option value="ga"'.$sel.'>'.$eLang->get('GROUP')." &#8593;</option>\n";
			$sel = ($options['order'] == 'gd') ? ' selected="selected"' : '';
			echo '<option value="gd"'.$sel.'>'.$eLang->get('GROUP')." &#8595;</option>\n";
		}
		echo "</select>\n";
		echo "</div>\n";
		echo "</div>\n";
		echo "</form>\n";
		echo "</div>\n";
	}


	/*********************/
	/* SHOW USER PROFILE */
	/*********************/
	public function userProfile($row, $params, $userparams, $usname, $twitter, $comments, $messages_total, $messages_unread, $bookmarks) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$eDate = eFactory::getDate();
		$eDoc = eFactory::getDocument();
		$eFiles = eFactory::getFiles();

		echo '<h1>'.$usname."</h1>\n";
		
		echo '<p>'.sprintf($eLang->get('PROFILEUSERAT'), '<strong>'.$usname.'</strong>', $elxis->getConfig('SITENAME'))."</p>\n";

		echo '<div class="elx_user_wrapcol">'."\n";

		echo '<div class="elx_user_maincol">'."\n";
		echo '<div class="elx_user_maincolin">'."\n";

		echo '<div class="elx5_dlspace">'."\n";
		echo '<h3>'.$eLang->get('ACCOUNT_DETAILS')."</h3>\n";

		echo '<div class="elx5_box elx5_border_blue">'."\n";
		echo '<div class="elx5_box_body">'."\n";
		echo '<table id="userprofiletbl" class="elx5_datatable">'."\n";
		echo "<tbody>\n";
		$allowed = $elxis->acl()->check('com_user', 'profile', 'viewaddress');
		if (($allowed == 2) || (($allowed == 1) && ($row->uid = $elxis->user()->uid))) {
			$address = array();
			if (trim($row->address) != '') { $address[] = $row->address; }
			if (trim($row->postalcode) != '') { $address[] = $row->postalcode; }
			if (trim($row->city) != '') { $address[] = $row->city; }
			if (trim($row->country) != '') {
				$country = $row->country;
				$lng = $eLang->getinfo('LANGUAGE');
				if (file_exists(ELXIS_PATH.'/includes/libraries/elxis/form/countries.'.$lng.'.php')) {
					include(ELXIS_PATH.'/includes/libraries/elxis/form/countries.'.$lng.'.php');
				} else {
					include(ELXIS_PATH.'/includes/libraries/elxis/form/countries.en.php');
				}
				$address[] = isset($countries[$country]) ? $countries[$country] : $row->country;
				unset($countries);
			}
			if (count($address) > 0) {
				echo '<tr data-row="address"><th scope="row" class="elx5_mobhide">'.$eLang->get('ADDRESS').'</th><td>'.implode(', ', $address)."</td></tr>\n";
			}
			unset($address);
		}

		$allowed = $elxis->acl()->check('com_user', 'profile', 'viewemail');
		if (($allowed == 2) || (($allowed == 1) && ($row->uid = $elxis->user()->uid))) {
			$eparts = str_split($row->email);
			$encmail = '';
			for ($i = 0; $i < count($eparts); $i++) { $encmail .= '&#'.ord($eparts[$i]).';'; }
			echo '<tr data-row="address"><th scope="row" class="elx5_mobhide">'.$eLang->get('EMAIL').'</th><td>'.$encmail."</td></tr>\n";
			unset($eparts, $encmail);
		}

		if (trim($row->phone) != '') {
			$allowed = $elxis->acl()->check('com_user', 'profile', 'viewphone');
			if (($allowed == 2) || (($allowed == 1) && ($row->uid = $elxis->user()->uid))) {
				echo '<tr data-row="phone"><th scope="row" class="elx5_mobhide">'.$eLang->get('TELEPHONE').'</th><td>'.$row->phone."</td></tr>\n";
			}
		}

		if (trim($row->mobile) != '') {
			$allowed = $elxis->acl()->check('com_user', 'profile', 'viewmobile');
			if (($allowed == 2) || (($allowed == 1) && ($row->uid = $elxis->user()->uid))) {
				echo '<tr data-row="mobile"><th scope="row" class="elx5_mobhide">'.$eLang->get('MOBILE').'</th><td>'.$row->mobile."</td></tr>\n";
			}
		}
		if (trim($row->website) != '') {
			$allowed = $elxis->acl()->check('com_user', 'profile', 'viewwebsite');
			if (($allowed == 2) || (($allowed == 1) && ($row->uid = $elxis->user()->uid))) {
				$txt = (strlen($row->website) > 30) ? substr($row->website, 0, 27).'...' : $row->website;
				echo '<tr data-row="website"><th scope="row" class="elx5_mobhide">'.$eLang->get('WEBSITE').'</th><td><a href="'.$row->website.'" target="_blank">'.$txt."</a></td></tr>\n";
			}
		}

		if (trim($row->occupation) != '') {
			echo '<tr data-row="occupation"><th scope="row" class="elx5_mobhide">'.$eLang->get('OCCUPATION').'</th><td>'.$row->occupation."</td></tr>\n";
		}

		if (trim($row->preflang) != '') {
			$lnginfo = $eLang->getallinfo($row->preflang);
			if (isset($lnginfo['NAME'])) {
				$txt = $lnginfo['NAME'] .' - '.$lnginfo['NAME_ENG'].' <span dir="ltr">('.$lnginfo['LANGUAGE'].'_'.$lnginfo['REGION'].')</span>';
				echo '<tr data-row="language"><th scope="row" class="elx5_mobhide">'.$eLang->get('LANGUAGE').'</th><td>'.$txt."</td></tr>\n";
			}
			unset($lnginfo);
		}
		if (trim($row->timezone) != '') {
			$txt = $row->timezone.'<div class="elx5_tip">'.$eLang->get('LOCALTIME').' '.$eDate->worldDate('now', $row->timezone, $eLang->get('DATE_FORMAT_4')).'</div>';
			echo '<tr data-row="timezone"><th scope="row" class="elx5_mobhide">'.$eLang->get('TIMEZONE').'</th><td>'.$txt."</td></tr>\n";
		}

		if (trim($row->gender) != '') {
			$allowed = $elxis->acl()->check('com_user', 'profile', 'viewgender');
			if (($allowed == 2) || (($allowed == 1) && ($row->uid = $elxis->user()->uid))) {
				$txt = ($row->gender == 'female') ? $eLang->get('FEMALE') : $eLang->get('MALE');
				echo '<tr data-row="gender"><th scope="row" class="elx5_mobhide">'.$eLang->get('GENDER').'</th><td>'.$txt."</td></tr>\n";
			}
		}

		if (trim($row->birthdate) != '') {
			$allowed = $elxis->acl()->check('com_user', 'profile', 'viewage');
			if (($allowed == 2) || (($allowed == 1) && ($row->uid = $elxis->user()->uid))) {
				$row->birthdate .= (strlen($row->birthdate) == 10) ? ' 12:00:00' : '';
				$parts = preg_split("/[\s-]+/", $row->birthdate, -1, PREG_SPLIT_NO_EMPTY);
				if ($parts && (count($parts) == 4)) {
					if (checkdate($parts[1], $parts[2], $parts[0]) === true) {
						$age = date('Y') - $parts[0];
						if (($age > 1) && ($age < 150)) {
							$txt = $age.' <span class="elx5_tip" dir="ltr">('.$eDate->formatDate($row->birthdate, $eLang->get('DATE_FORMAT_2')).')</span>';
							echo '<tr data-row="age"><th scope="row" class="elx5_mobhide">'.$eLang->get('AGE').'</th><td>'.$txt."</td></tr>\n";
						}
					}
				}
			}
		}

		if ($elxis->acl()->getLevel() >= 70) {
			$txt = $this->base_translateGroup($row->groupname, $row->gid);
			echo '<tr data-row="group"><th scope="row" class="elx5_mobhide">'.$eLang->get('GROUP').'</th><td>'.$txt."</td></tr>\n";
		}

		echo '<tr data-row="profileviews"><th scope="row" class="elx5_mobhide">'.$eLang->get('PROFILE_VIEWS').'</th><td>'.$row->profile_views."</td></tr>\n";
		echo '<tr data-row="timesonline"><th scope="row" class="elx5_mobhide">'.$eLang->get('TIMES_ONLINE').'</th><td>'.$row->times_online."</td></tr>\n";

		$txt = $eDate->formatDate($row->registerdate, $eLang->get('DATE_FORMAT_5'));
		echo '<tr data-row="registerdate"><th scope="row" class="elx5_mobhide">'.$eLang->get('MEMBERSINCE').'</th><td>'.$txt."</td></tr>\n";

		if ((trim($row->lastvisitdate) != '') && ($row->lastvisitdate != '1970-01-01 00:00:00')) {
			$txt = $eDate->formatDate($row->lastvisitdate, $eLang->get('DATE_FORMAT_5'));
		} else {
			$txt = $eLang->get('NEVER');
		}
		echo '<tr data-row="lastvisit"><th scope="row" class="elx5_mobhide">'.$eLang->get('LASTVISIT').'</th><td>'.$txt."</td></tr>\n";

		if (($elxis->acl()->getLevel() >= 70) || ($row->uid == $elxis->user()->uid)) {
			if ((trim($row->expiredate) != '') && ($row->expiredate != '2060-01-01 00:00:00')) {
				$txt = $eDate->formatDate($row->expiredate, $eLang->get('DATE_FORMAT_4'));
			} else {
				$txt = $eLang->get('NEVER');
			}
			echo '<tr data-row="expiredate"><th scope="row" class="elx5_mobhide">'.$eLang->get('EXPIRATION_DATE').'</th><td>'.$txt."</td></tr>\n";
		}

		if ($elxis->acl()->getLevel() >= 70) {
			if ($row->is_online) {
				$mins = floor($row->time_online / 60);
				$secs = $row->time_online - ($mins * 60);
				echo '<tr data-row="ipaddress"><th scope="row" class="elx5_mobhide">'.$eLang->get('IP_ADDRESS').'</th><td>'.$row->ip_address."</td></tr>\n";

				if ($row->browser) {
					$lines = array();
					if ($row->browser['name'] != '') {
						$lines[] = ($row->browser['version'] != '') ? $row->browser['name'].' '.$row->browser['version'] : $row->browser['name'];
					}
					if ($row->browser['os_name'] != '') {
						$txt = $row->browser['os_name'];
						if ($row->browser['os_version'] != '') {
							$txt .= ' '.$row->browser['os_version'];
						}
						if ($row->browser['os_platform'] != '') { $txt .= ' ('.$row->browser['os_platform'].')'; }
						$lines[] = $txt;
					}
					if ($lines) {
						echo '<tr data-row="browser"><th scope="row" class="elx5_mobhide">'.$eLang->get('BROWSER').'</th><td>'.implode('<br />', $lines)."</td></tr>\n";
					}
					if ($row->browser['type'] != '') {
						echo '<tr data-row="device"><th scope="row" class="elx5_mobhide">Device</th><td>'.ucfirst($row->browser['type'])."</td></tr>\n";
					}
				}

				$txt = '';
				if ($mins > 0) { $txt.= $mins.' min, '; }
				$txt .= $secs.' sec';
				echo '<tr data-row="timeonline"><th scope="row" class="elx5_mobhide">'.$eLang->get('TIME_ONLINE').'</th><td>'.$txt."</td></tr>\n";
				echo '<tr data-row="clicks"><th scope="row" class="elx5_mobhide">'.$eLang->get('CLICKS').'</th><td>'.$row->clicks."</td></tr>\n";
				if (trim($row->lastclicks) == '') {
					$curlink = $elxis->getConfig('URL').'/'.$row->current_page;
					if ((stripos($row->current_page, 'http:') === false) && (stripos($row->current_page, 'https:') === false)) {
						if (stripos($row->current_page, ':') !== false) {
							$curlink = $elxis->makeURL($row->current_page);
						}
					}
					echo '<tr data-row="currentpage"><th scope="row" class="elx5_mobhide">'.$eLang->get('CURRENT_PAGE').'</th><td><a href="'.$curlink.'">'.$row->current_page."</a></td></tr>\n";
				}
			}
		}
		echo "</tbody>\n";
		echo "</table>\n";
		echo "</div>\n";
		echo "</div>\n";
		echo "</div>\n";

		if ($elxis->acl()->getLevel() >= 70) {
			if (trim($row->lastclicks) != '') {
				$clicks = json_decode($row->lastclicks, true);
				usort($clicks, array($this, 'base_sortUserClicks'));
				echo '<div class="elx5_dlspace">'."\n";
				echo '<h3>'.$eLang->get('LATEST_PAGES')."</h3>\n";
				echo '<div class="elx5_box elx5_border_blue">'."\n";
				echo '<div class="elx5_box_body">'."\n";
				echo '<table id="userclickstbl" class="elx5_datatable">'."\n";
				echo "<tbody>\n";
				foreach ($clicks as $click) {
					$date = $eDate->humanDate('', $click['ts']);
					$pg = (strlen($click['page']) > 33) ? '...'.substr($click['page'], -30) : $click['page'];
					echo '<tr><th scope="row" class="elx5_mobhide">'.$date.'</th><td><a href="'.$click['page'].'" target="_blank">'.$pg."</a></td></tr>\n";
				}
				echo "</tbody>\n";
				echo "</table>\n";
				echo "</div>\n";
				echo "</div>\n";
				echo "</div>\n";
			}
		}

		if ($comments) {
			$defimage = $elxis->secureBase().'/templates/system/images/nopicture_article.jpg';

			echo '<h3 id="elx_user_comments_title">'.$eLang->get('COMMENTS')."</h3>\n";
			echo '<ul class="elx5_comments_box" id="elx_user_comments">'."\n";
			foreach ($comments as $comment) {
				$link = $elxis->makeURL('content:'.$comment->link);
				$image = $defimage;
				if ((trim($comment->image) != '') && file_exists(ELXIS_PATH.'/'.$comment->image)) {
					$image = $elxis->secureBase().'/'.$comment->image;
					$file_info = $eFiles->getNameExtension($comment->image);
					if (file_exists(ELXIS_PATH.'/'.$file_info['name'].'_thumb.'.$file_info['extension'])) {
						$image = $elxis->secureBase().'/'.$file_info['name'].'_thumb.'.$file_info['extension'];
					} else if (!file_exists(ELXIS_PATH.'/'.$comment->image)) {
						$image = $elxis->secureBase().'/templates/system/images/nopicture_article.jpg'; 
					}
					unset($file_info);
				}

				echo '<li>'."\n";
				echo '<div class="elx5_comment_avatar"><a href="'.$link.'" title="'.$comment->title.'"><img src="'.$image.'" alt="'.$comment->title.'" /></a></div>'."\n";
				echo '<div class="elx5_comment_main">'."\n";
				echo '<div class="elx5_comment_top">'."\n";
				echo '<div class="elx5_comment_author"><a href="'.$link.'" title="'.$comment->title.'">'.$comment->title."</a></div>\n";
				echo '<time class="elx5_comment_date" datetime="'.$comment->created.'">'.$eDate->formatDate($comment->created, $eLang->get('DATE_FORMAT_5'))."</time>\n";
				echo "</div>\n";//elx5_comment_top
				echo '<div class="elx5_comment_message">'.nl2br(strip_tags($comment->message))."</div>\n";
				echo "</div>\n";//elx5_comment_main
				echo "</li>\n";
			}
			echo "</ul>\n";
		}

		if (is_object($twitter['user'])) {
			echo '<h3 id="elx_user_twitter_title">'.sprintf($eLang->get('ONTWITTER'), $row->firstname.' '.$row->lastname)."</h3>\n";
			echo '<p><a href="https://twitter.com/'.$twitter['user']->screen_name.'" title="'.$twitter['user']->name.' : Twitter" target="_blank">';
			echo '@'.$twitter['user']->screen_name.'</a>, '.$twitter['user']->description.'</p>';
			echo '<div class="elx5_tip">';
			echo $eLang->get('FOLLOWERS').' <strong>'.$twitter['user']->followers_count.'</strong>, ';
			echo $eLang->get('FRIENDS').' <strong>'.$twitter['user']->friends_count.'</strong>, ';
			echo $eLang->get('FAVORITES').' <strong>'.$twitter['user']->favourites_count.'</strong>, ';
			echo $eLang->get('STATUSES').' <strong>'.$twitter['user']->statuses_count.'</strong>';
			echo "</div>\n";

			if (is_array($twitter['tweets']) && (count($twitter['tweets']) > 0)) {
				echo '<ul class="elx5_comments_box" id="elx_user_twits">'."\n";
				foreach ($twitter['tweets'] as $tweet) {
					$link = 'https://twitter.com/'.$twitter['user']->screen_name;
					$message = $tweet->text;
					if ($tweet->link != '') { $message .= '<br /><a href="'.$tweet->link.'" title="'.$eLang->get('PERMALINK').'" target="_blank">'.$eLang->get('PERMALINK').'</a>'; }

					echo '<li>'."\n";
					echo '<div class="elx5_comment_avatar"><a href="'.$link.'" title="'.$twitter['user']->name.' : Twitter" target="_blank"><img src="'.$twitter['user']->profile_image_url.'" alt="'.$twitter['user']->name.'" /></a></div>'."\n";
					echo '<div class="elx5_comment_main">'."\n";
					echo '<div class="elx5_comment_top">'."\n";
					echo '<div class="elx5_comment_author"><a href="'.$link.'" title="'.$twitter['user']->name.' : Twitter" target="_blank">@'.$twitter['user']->name."</a></div>\n";
					echo '<time class="elx5_comment_date">'.$eDate->formatTS($tweet->created_ts, $eLang->get('DATE_FORMAT_5'))."</time>\n";
					echo "</div>\n";//elx5_comment_top
					echo '<div class="elx5_comment_message">'.$tweet->message."</div>\n";
					echo "</div>\n";//elx5_comment_main
					echo "</li>\n";
				}
				echo "</ul>\n";
			}
		}

		if ($eDoc->countModules('user_maincol') > 0) {
			echo '<div class="user_maincol_mods">'."\n";
			$eDoc->modules('user_maincol');
			echo "</div>\n";
		}

		echo "</div>\n";//.elx_user_maincolin end
		echo "</div>\n";//.elx_user_maincol end

		$userdata = new stdClass;
		$userdata->name = $row->firstname.' '.$row->lastname;
		$userdata->uname = $row->uname;
		$userdata->uid = $row->uid;
		$userdata->gid = $row->gid;
		$userdata->online = $row->is_online;
		$userdata->totalmessages = $messages_total;
		$userdata->newmessages = $messages_unread;
		$userdata->bookmarks = $bookmarks;
		$userdata->avatar = $row->avatar;
		$userdata->twitter_username = $row->twitter_username;

		echo '<div class="elx_user_sidecol">'."\n";
		$this->base_sideProfile($userdata, $elxis, $eLang, $eDoc, 'profile', $params);
		echo "</div>\n";//.elx_user_sidecol

		echo '<div class="clear"></div>'."\n";
		echo "</div>\n";

		$this->base_pmsform($elxis, $eLang, $params);
	}


	/**************************/
	/* HTML EDIT USER PROFILE */
	/**************************/
	public function editProfile($row, $avatar, $userparams, $errormsg, $messages_total, $messages_unread, $bookmarks, $is_online, $params) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$eDate = eFactory::getDate();
		$eDoc = eFactory::getDocument();

		$action = $elxis->makeURL('user:members/save.html', '', true, false);
		elxisLoader::loadFile('includes/libraries/elxis/form5.class.php');

		echo '<h1>'.$eLang->get('EDITPROFILE').' '.$row->uname."</h1>\n";
		if ($errormsg != '') {
			echo '<div class="elx5_error elx5_dspace">'.$errormsg."</div>\n";
		}

		echo '<div class="elx_user_wrapcol">'."\n";

		echo '<div class="elx_user_maincol">'."\n";
		echo '<div class="elx_user_maincolin">'."\n";

		$form = new elxis5Form(array('idprefix' => 'epr', 'labelclass' => 'elx5_label', 'sideclass' => 'elx5_labelside'));
		$form->openForm(array('name' => 'fmeditprof', 'method' => 'post', 'action' => $action, 'id' => 'fmeditprof', 'enctype' => 'multipart/form-data'));

		$form->startTabs(array('', '', ''));
		$form->openTab();
		$form->openFieldset($eLang->get('ACCOUNT_DETAILS'));
		$form->addText('firstname', $row->firstname, $eLang->get('FIRSTNAME'), array('required' => 'required', 'maxlength' => 60));
		$form->addText('lastname', $row->lastname, $eLang->get('LASTNAME'), array('required' => 'required', 'maxlength' => 60));

		if ($elxis->getConfig('SECURITY_LEVEL') < 2) {
			if ($elxis->getConfig('REGISTRATION_ACTIVATION') == 2) {
				$email_desc = ($elxis->user()->gid == 1) ? '' : $eLang->get('CHMAILADMACT');
			} else if ($elxis->getConfig('REGISTRATION_ACTIVATION') == 1) {
				$email_desc = ($elxis->user()->gid == 1) ? '' : $eLang->get('CHMAILREACT');
			} else {
				$email_desc = '';
			}
			$form->addEmail('email', $row->email, $eLang->get('EMAIL'), array('required' => 'required', 'tip' => $email_desc));
		}

		if ($elxis->acl()->check('com_user', 'profile', 'uploadavatar') == 1) {
			$txt = $eLang->get('AVATAR_D').' '.$eLang->get('PREFER_SQUARE_PIC');
			if ($avatar['localpath'] != '') {
				$form->addImage('avatar', $avatar['localpath'], $eLang->get('AVATAR'), array('tip' => $txt));
			} else {
				$form->addFile('avatar', $eLang->get('AVATAR'), array('tip' => $txt));
			}
		}

		$form->addPassword('pword', '', $eLang->get('PASSWORD'), 
			array(
				'maxlength' => 60, 'tip' => $eLang->get('ONLY_IF_CHANGE'), 'placeholder' => $eLang->get('PASSWORD'), 'autocomplete' => 'off', 
				'pattern' => '[A-Za-z0-9_!@\-]{6,}', 'title' => $eLang->get('MINLENGTH6').'. Acceptable characters are A-Z a-z 0-9 _ - ! @', 'password_meter' => 1
			)
		);
		$form->addPassword('pword2', '', $eLang->get('PASSWORD_AGAIN'), array('maxlength' => 60, 'match' => 'eprpword', 'autocomplete' => 'off'));
		$form->closeFieldset();
		$form->closeTab();

		$form->openTab();
		$form->openFieldset($eLang->get('CONTACT'));
		$form->addText('address', $row->address, $eLang->get('ADDRESS'), array('maxlength' => 120));
		$form->addText('postalcode', $row->postalcode, $eLang->get('POSTAL_CODE'), array('dir' => 'ltr'));
		$form->addText('city', $row->city, $eLang->get('CITY'));
		$val = (trim($row->country) == '') ? $eLang->getinfo('REGION') : $row->country;
		$form->addCountry('country', '', $val);
		$form->addTel('phone', $row->phone, $eLang->get('TELEPHONE'), array('maxlength' => 40, 'pattern' => '[0-9\+\-\s]{6,}'));
		$form->addTel('mobile', $row->mobile, $eLang->get('MOBILE'), array('maxlength' => 40, 'pattern' => '[0-9\+\-\s]{6,}'));
		$form->addURL('website', $row->website, $eLang->get('WEBSITE'), array('maxlength' => 120));

		$val = $userparams->get('twitter', '');
		$form->addText('params_twitter', $val, $eLang->get('TWITACCOUNT'), array('tip' => $eLang->get('TWITACCOUNT_D'), 'maxlength' => 60, 'dir' => 'ltr', 'pattern' => '[A-Za-z0-9_]{1,15}'));
		$form->closeFieldset();
		$form->closeTab();

		$form->openTab();
		$form->openFieldset($eLang->get('PREFERENCES'));
		$val = (trim($row->preflang) == '') ? $eLang->getinfo('LANGUAGE') : $row->preflang;
		$form->addLanguage('preflang', $eLang->get('LANGUAGE'), $val, array('tip' => $eLang->get('SETPREFLANG')));
		$tz = ($elxis->user()->uid == $row->uid) ? $eDate->getTimezone() : $row->timezone;
		if (trim($tz) == '') { $tz = $eDate->getTimezone(); }
		$user_daytime = $eDate->worldDate('now', $tz, $eLang->get('DATE_FORMAT_12'));
		$form->addTimezone('timezone', $eLang->get('TIMEZONE'), $tz, array('tip' => $user_daytime));
		$form->closeFieldset();

		$form->openFieldset($eLang->get('OTHER_DETAILS'));
		$options = array();
		$options[] = $form->makeOption('male', $eLang->get('MALE'));
		$options[] = $form->makeOption('female', $eLang->get('FEMALE'));
		$form->addRadio('gender', $eLang->get('GENDER'), $row->gender, $options, array('vertical_options' => 0));
		if (trim($row->birthdate) != '') {
			$x = substr($row->birthdate, 0, 10);
			$val = $eDate->convertFormat($x, 'Y-m-d', $eLang->get('DATE_FORMAT_BOX'));
		} else {
			$val = '';
		}
		$form->addDate('birthdate', $val, $eLang->get('BIRTHDATE'));

		$form->addText('occupation', $row->occupation, $eLang->get('OCCUPATION'), array('maxlength' => 120));
		$form->addInfo($eLang->get('USERNAME'), $row->uname);
		$txt = $this->base_translateGroup($row->groupname, $row->gid);
		$form->addInfo($eLang->get('GROUP'), $txt);
		$txt = $eDate->formatDate($row->registerdate, $eLang->get('DATE_FORMAT_5'));
		$form->addInfo($eLang->get('MEMBERSINCE'), $txt);
		if ((trim($row->lastvisitdate) != '') && ($row->lastvisitdate != '1970-01-01 00:00:00')) {
			$txt = $eDate->formatDate($row->lastvisitdate, $eLang->get('DATE_FORMAT_5'));
		} else {
			$txt = $eLang->get('NEVER');
		}
		$form->addInfo($eLang->get('LASTVISIT'), $txt);
		$form->closeFieldset();
		$form->closeTab();
		$form->endTabs();

		if ($elxis->getConfig('CAPTCHA') != 'NONE') {
			if ($elxis->getConfig('CAPTCHA') == 'MATH') {
				$form->addCaptcha('seccode');
			} else {
				$form->addNoRobot('', false);
			}
		}

		$form->addHidden('uid', $row->uid);
		$form->addHidden('uname', $row->uname);
		$form->addToken('fmeditprof');
		$form->addHTML('<div class="elx5_vspace">');
		$form->addButton('sbmepr', $eLang->get('SUBMIT'), 'submit');
		$form->addHTML('</div>');
		$form->closeForm();
		unset($form);
		echo "</div>\n";//.elx_user_maincolin end
		echo "</div>\n";//.elx_user_maincol end

		$avatarpath = 'media/images/avatars/';
		if (defined('ELXIS_MULTISITE')) {
			if (ELXIS_MULTISITE > 1) { $avatarpath = 'media/images/site'.ELXIS_MULTISITE.'/avatars/'; }
		}
		$img = trim($row->avatar);
		$avatar = $elxis->secureBase().'/components/com_user/images/noavatar.png';
		if ($img != '') {
			if (preg_match('#^(http(s)?\:\/\/)#i', $img)) {
				$avatar = $img;
			} else if (file_exists(ELXIS_PATH.'/'.$avatarpath.$img)) {
				$avatar = $elxis->secureBase().'/'.$avatarpath.$img;
			}
		}

		$userdata = new stdClass;
		$userdata->name = $row->firstname.' '.$row->lastname;
		$userdata->uname = $row->uname;
		$userdata->uid = $row->uid;
		$userdata->gid = $row->gid;
		$userdata->online = $is_online;
		$userdata->totalmessages = $messages_total;
		$userdata->newmessages = $messages_unread;
		$userdata->bookmarks =  $bookmarks;
		$userdata->avatar = $avatar;
		$userdata->twitter_username = '';
		unset($img, $avatar, $avatarpath);

		echo '<div class="elx_user_sidecol">'."\n";
		$this->base_sideProfile($userdata, $elxis, $eLang, $eDoc, 'editprofile', $params);
		echo "</div>\n";//.elx_user_sidecol

		echo '<div class="clear"></div>'."\n";
		echo "</div>\n";
		$this->base_pmsform($elxis, $eLang, $params);
	}


	/****************************************/
	/* DISPLAY PROFILE SAVE SUCCESS MESSAGE */
	/****************************************/
	public function profileSuccess($row, $msg, $messages_total, $messages_unread, $bookmarks, $is_online, $params) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$eDoc = eFactory::getDocument();

		echo '<h1>'.$eLang->get('EDITPROFILE').' '.$row->uname."</h1>\n";
		echo '<div class="elx_user_wrapcol">'."\n";

		echo '<div class="elx_user_maincol">'."\n";
		echo '<div class="elx_user_maincolin">'."\n";
		echo '<div class="elx5_success">'.$msg."</div>\n";
		echo '<div class="elx5_vlspace">';
		if ($row->block == 0) {
			$link = $elxis->makeURL('user:members/'.$row->uid.'.html');
			echo '<a href="'.$link.'" title="'.$eLang->get('PROFILE').'" class="elx5_btn elx5_ibtn">'.$eLang->get('PROFILE').' '.$row->firstname.' '.$row->lastname."</a> \n";
		}
		if ($elxis->acl()->check('com_user', 'memberslist', 'view') > 0) {
			$link = $elxis->makeURL('user:members/');
			echo '<a href="'.$link.'" title="'.$eLang->get('MEMBERSLIST').'" class="elx5_btn elx5_ibtn">'.$eLang->get('MEMBERSLIST')."</a> \n";
		}

		$link = $elxis->makeURL('user:/');
		echo '<a href="'.$link.'" title="'.$eLang->get('USERSCENTRAL').'" class="elx5_btn elx5_ibtn">'.$eLang->get('USERSCENTRAL')."</a>\n";
		echo "</div>\n";
		echo "</div>\n";//.elx_user_maincolin end
		echo "</div>\n";//.elx_user_maincol end

		$avatarpath = 'media/images/avatars/';
		if (defined('ELXIS_MULTISITE')) {
			if (ELXIS_MULTISITE > 1) { $avatarpath = 'media/images/site'.ELXIS_MULTISITE.'/avatars/'; }
		}
		$img = trim($row->avatar);
		$avatar = $elxis->secureBase().'/components/com_user/images/noavatar.png';
		if ($img != '') {
			if (preg_match('#^(http(s)?\:\/\/)#i', $img)) {
				$avatar = $img;
			} else if (file_exists(ELXIS_PATH.'/'.$avatarpath.$img)) {
				$avatar = $elxis->secureBase().'/'.$avatarpath.$img;
			}
		}

		$userdata = new stdClass;
		$userdata->name = $row->firstname.' '.$row->lastname;
		$userdata->uname = $row->uname;
		$userdata->uid = $row->uid;
		$userdata->gid = $row->gid;
		$userdata->online = $is_online;
		$userdata->totalmessages = $messages_total;
		$userdata->newmessages = $messages_unread;
		$userdata->bookmarks =  $bookmarks;
		$userdata->avatar = $avatar;
		$userdata->twitter_username = '';
		unset($img, $avatar, $avatarpath);

		echo '<div class="elx_user_sidecol">'."\n";
		$this->base_sideProfile($userdata, $elxis, $eLang, $eDoc, 'saveprofile', $params);
		echo "</div>\n";//.elx_user_sidecol

		echo '<div class="clear"></div>'."\n";
		echo "</div>\n";
		$this->base_pmsform($elxis, $eLang, $params);
	}


	/*****************************/
	/* VIEW BOOKMARKS/NOTES HTML */
	/*****************************/
	public function bookmarksHTML($rows, $categories, $options, $messages_unread, $messages_total, $avatar, $params) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$eDate = eFactory::getDate();
		$eDoc = eFactory::getDocument();

		$title = ($options['page'] > 1) ? $eLang->get('BOOKMARKS_NOTES').' <span>'.$eLang->get('PAGE').' '.$options['page'].'</span>' : $eLang->get('BOOKMARKS_NOTES');

		echo '<h1>'.$title."</h1>\n";
		if ($options['page'] == 1) {
			echo '<p>'.$eLang->get('BOOKMARKS_NOTES_DESC')."</p>\n";
		}

		echo '<div class="elx_user_wrapcol">'."\n";

		echo '<div class="elx_user_maincol">'."\n";
		echo '<div class="elx_user_maincolin">'."\n";

		echo '<div class="elx5_dataactions elx5_spad">';
		echo '<a href="javascript:void(null);" onclick="elx5UserEditBookmark(0, 1);" class="elx5_dataaction elx5_datahighlight"><i class="fas fa-plus"></i> '.$eLang->get('ADD').'</a>'."\n";
		echo "</div>\n";

		echo '<ul id="elx5_user_bookmarks" class="elx5_user_bookmarks">'."\n";
		if ($rows) {
			foreach ($rows as $row) {
				$cid = $row->cid;
				$category = $categories[$cid];
				$title = ($row->title == '') ? $category[1] : $row->title;
				echo '<li id="elx5_user_bookmark_'.$row->id.'">';
				echo '<div class="elx5_user_bmk_icon elx5_user_bmk_icon_'.$category[0].'"><div><i class="'.$category[2].'"></i></div></div>'."\n";
				echo '<div class="elx5_user_bmk_main">'."\n";
				echo '<div class="elx5_user_bmk_top">';
				echo '<div class="elx5_user_bmk_date">'.$eDate->formatDate($row->created, $eLang->get('DATE_FORMAT_4')).'</div>';
				if ($row->link != '') {
					echo '<h4 title="'.$category[1].'"><a href="'.$row->link.'" target="_blank">'.$title.'</a></h4>';
				} else {
					echo '<h4 title="'.$category[1].'">'.$title.'</h4>';
				}
				if ($cid == 5) { //reminder
					$date = $eDate->formatDate($row->reminderdate, $eLang->get('DATE_FORMAT_12'));
					if ($row->remindersent == 1) {
						$txt = '<i class="fas fa-check elx5_green"></i> ';
					} else {
						$txt = '<i class="fas fa-bell"></i> ';
					}
					$txt .= sprintf($eLang->get('REMINDER_FOR_DATE'), '<span>'.$date.'</span>');
					echo '<div class="elx5_user_bmk_reminderdate">'.$txt.'</div>';
				}
				echo "</div>\n";//elx5_user_bmk_top
				if ($row->note != '') {
					echo '<div class="elx5_user_bmk_text">'.$row->note.'</div>';
				}
				if ($cid == 5) { //reminder
					if ($elxis->getConfig('CRONJOBS') == 0) {
						echo '<div class="elx5_smwarning">'.$eLang->get('SEND_NOTIFS_DISABLED')."</div>\n";
					}
				}
				echo '<div class="elx5_dataactions elx5_zero">';
				echo '<a href="javascript:void(null);" onclick="elx5UserEditBookmark('.$row->id.');" class="elx5_dataaction elx5_dataactive"><i class="fas fa-pencil-alt"></i> '.$eLang->get('EDIT').'</a>'."\n";
				echo '<a href="javascript:void(null);" onclick="elx5UserDeleteBookmark('.$row->id.');" class="elx5_dataaction elx5_datawarn"><i class="fas fa-trash-alt"></i> '.$eLang->get('DELETE').'</a>';
				echo "</div>\n";
				echo "</div>\n";//elx5_user_bmk_main
				echo "</li>\n";
			}
		}
		echo "</ul>\n";

		if ($rows) {
			if ($options['maxpage'] > 1) {
				$linkbase = $elxis->makeURL('user:bookmarks/');
				$navigation = $elxis->obj('html')->pagination($linkbase, $options['page'], $options['maxpage']);
				echo '<div class="elx5_dspace">'.$navigation."</div>\n";
			}
		}

		if ($eDoc->countModules('user_maincol') > 0) {
			echo '<div class="user_maincol_mods">'."\n";
			$eDoc->modules('user_maincol');
			echo "</div>\n";
		}

		echo "</div>\n";//.elx_user_maincolin end
		echo "</div>\n";//.elx_user_maincol end

		$userdata = new stdClass;
		$userdata->name = $elxis->user()->firstname.' '.$elxis->user()->lastname;
		$userdata->uname = $elxis->user()->uname;
		$userdata->uid = $elxis->user()->uid;
		$userdata->gid = $elxis->user()->gid;
		$userdata->online = 1;
		$userdata->totalmessages = $messages_total;
		$userdata->newmessages = $messages_unread;
		$userdata->bookmarks = $options['total'];
		if ($avatar == '') {
			$userdata->avatar = $elxis->secureBase().'/components/com_user/images/noavatar.png';
		} else {
			$userdata->avatar = $avatar;
		}
		$userdata->twitter_username = '';

		echo '<div class="elx_user_sidecol">'."\n";
		$this->base_sideProfile($userdata, $elxis, $eLang, $eDoc, 'bookmarks', $params);
		echo "</div>\n";//.elx_user_sidecol

		echo '<div class="clear"></div>'."\n";
		echo "</div>\n";

		$this->base_pmsform($elxis, $eLang, $params);
		$this->base_bookmarkform($categories, $elxis, $eLang);
	}


	/***********************************/
	/* GET PRIVATE MESSAGE AVATAR/ICON */
	/***********************************/
	private function getMessageAvatar($img, $uid, $name, $msgtype, $avatarpath, $elxis) {
		if ($img != '') {
			if (preg_match('#^(http(s)?\:\/\/)#i', $img)) {
				return $img;
			}
			if (file_exists(ELXIS_PATH.'/'.$avatarpath.$img)) {
				$avatar = $elxis->secureBase().'/'.$avatarpath.$img;
				return $avatar;
			}
		}

		if ($uid > 0) {
			$avatar = $elxis->secureBase().'/components/com_user/images/noavatar.png';
			return $avatar;
		}

		$fname = eUTF::strtolower($name);
		if (($fname == 'elxis.org') || ($fname == 'elxis') || ($fname == 'system') || ($fname == 'elxis team')) {
			$avatar = $elxis->secureBase().'/components/com_user/images/elxis.png';
			return $avatar;
		}
		if (($fname == 'ios') || ($fname == 'is open source')) {
			$avatar = $elxis->secureBase().'/components/com_user/images/isopensource.png';
			return $avatar;
		}

		if ($msgtype == 'warning') {
			$avatar = $elxis->secureBase().'/components/com_user/images/warning.png';
		} else if ($msgtype == 'help') {
			$avatar = $elxis->secureBase().'/components/com_user/images/help.png';
		} else {
			$avatar = $elxis->secureBase().'/components/com_user/images/noavatar.png';
		}

		return $avatar;
	}


	/**********************************/
	/* VIEW PERSONAL MESSAGES THREADS */
	/**********************************/
	public function pmThreadsHTML($threads, $total_threads, $total_messages, $unread_messages, $bookmarks, $elxis, $eLang, $eDoc, $params) {
		$eDate = eFactory::getDate();

		echo '<div id="elx_pmessages_page">'."\n";
		echo '<h1>'.$eLang->get('PERSONAL_MESSAGES')."</h1>\n";

		echo '<div class="elx_user_wrapcol">'."\n";

		echo '<div class="elx_user_maincol">'."\n";
		echo '<div class="elx_user_maincolin">'."\n";

		$myid = $elxis->user()->uid;
		$defavatar = $elxis->secureBase().'/components/com_user/images/noavatar.png';
		$avatarpath = 'media/images/avatars/';
		if (defined('ELXIS_MULTISITE')) {
			if (ELXIS_MULTISITE > 1) { $avatarpath = 'media/images/site'.ELXIS_MULTISITE.'/avatars/'; }
		}
		$user_link = $elxis->makeURL('user:/');

		if ($elxis->acl()->getLevel() <= 2) {//users send pms access check applies
			$usersendpms = (int)$params->get('usersendpms', 2);
		} else {
			$usersendpms = 2;//allowed to all
		}

		echo '<ul class="elx5_user_threads" id="elx5_user_threads" data-lngnomsg="'.$eLang->get('NO_MESSAGES').'">'."\n";
		if ($threads) {
			$usernowdt = $eDate->elxisToLocal('now', true);//Y-m-d H:i:s
			$ts = time() - 86400;
			$useryestdt = $eDate->formatTS($ts);
			$acl_profile = $elxis->acl()->check('com_user', 'profile', 'view');
			$time = $eDate->getTS() - $elxis->getConfig('SESSION_LIFETIME');

			foreach ($threads as $id => $thread) {
				$avatar = $defavatar;
				$class_unread = '';
				$icon_readby = '';

				if ($thread->fromid == $myid) {
					$img = trim($thread->to_avatar);
					$user_uid = $thread->toid;
					$user_name = $thread->toname;
					$user_uname = $thread->to_uname;
					$fromtotxt = $eLang->get('TO');
					$iconclass = 'fas fa-arrow-circle-up';
					$lastactivity = $thread->to_last_activity;
					if ($thread->read == 1) {
						$icon_readby = ' <i class="fas fa-eye elx5_gray" style="font-size:13px;"></i>';
					} else {
						$icon_readby = ' <i class="fas fa-eye-slash elx5_gray" style="font-size:13px;"></i>';
					}
				} else {//sent to me
					$img = trim($thread->from_avatar);
					$user_uid = $thread->fromid;
					$user_name = $thread->fromname;
					$user_uname = $thread->from_uname;
					$fromtotxt = $eLang->get('FROM');
					$iconclass = 'fas fa-arrow-circle-down';
					$lastactivity = $thread->from_last_activity;
					$class_unread = ($thread->read == 1) ? '' : ' class="elx5_user_thread_unread"';
				}
				if ($user_uname == '') { $user_uname = $user_name; }

				$icon_online = '';
				$can_reply = false;
				if ($user_uid > 0) {
					$icon_online = ' <i class="fas fa-user-slash elx5_gray" style="font-size:13px;"></i>';
					if ($user_uid == $myid) {
						$icon_online = ' <i class="fas fa-user elx5_green" style="font-size:13px;"></i>';
					} else if ($lastactivity > 0) {
						if ($lastactivity >= $time) { $icon_online = ' <i class="fas fa-user elx5_green" style="font-size:13px;"></i>'; }
					}
					if ($usersendpms > 0) { $can_reply = true; }
				}

				$avatar = $this->getMessageAvatar($img, $user_uid, $user_name, $thread->msgtype, $avatarpath, $elxis);
				$human_date = $this->humanDate($usernowdt, $useryestdt, $thread->created, $eLang, $eDate);

				echo '<li id="elx5_user_thread'.$id.'">';
				echo '<div class="elx5_user_thread_avatar"><a href="'.$user_link.'pms/'.$id.'.html" title="'.$eLang->get('GO_TO_THREAD').'"><img src="'.$avatar.'" alt="'.$user_name.'" /></a></div>'."\n";
				echo '<div class="elx5_user_thread_main">'."\n";
				echo '<div class="elx5_user_thread_top">'."\n";
				echo '<div class="elx5_user_thread_date">'.$human_date."</div>\n";
				echo '<h4'.$class_unread.'><a href="'.$user_link.'pms/'.$id.'.html" title="'.$eLang->get('GO_TO_THREAD').'"><span>'.$fromtotxt.'</span> '.$user_name.$icon_online.$icon_readby."</a></h4>\n";
				echo "</div>\n";//elx5_user_thread_top
				echo '<div class="elx5_user_thread_msg">'."\n";
				echo '<a href="'.$user_link.'pms/'.$id.'.html" title="'.$eLang->get('GO_TO_THREAD').'"><i class="'.$iconclass.'"></i> '.$thread->message.'</a>';
				echo "</div>\n";//elx5_user_thread_msg

				echo '<div class="elx5_tspace">'."\n";
				if ($can_reply) {
					echo '<a href="javascript:void(null);" onclick="elx5UserPMSOpen('.$id.', '.$user_uid.', \''.addslashes($user_name).'\');" class="elx5_smbtn elx5_sucbtn" title="'.$eLang->get('REPLY').'"><i class="fas fa-reply"></i><span class="elx5_tabhide"> '.$eLang->get('REPLY').'</span></a> &#160; '."\n";
				} else {
					echo '<a href="javascript:void(null);" class="elx5_smbtn elx5_notallowedbtn"><i class="fas fa-reply"></i><span class="elx5_tabhide"> '.$eLang->get('REPLY').'</span></a> &#160; '."\n";
				}
				echo '<a href="javascript:void(null);" onclick="elx5UserPMSDelete('.$id.');" class="elx5_smbtn elx5_errorbtn" title="'.$eLang->get('DELETE').'"><i class="fas fa-trash-alt"></i><span class="elx5_tabhide"> '.$eLang->get('DELETE').'</span></a>';
				if ($user_uid > 0) {
					if (($acl_profile == 2) || (($acl_profile == 1) && ($elxis->user()->uid == $myid))) {
						echo ' &#160; <a href="'.$user_link.'members/'.$user_uid.'.html" class="elx5_smbtn" title="'.$user_name.' : '.$eLang->get('PROFILE').'"><i class="fas fa-at"></i><span class="elx5_tabhide"> '.$user_uname.'</span></a>';
					} else {
						echo ' &#160; <a href="javascript:void(null);" class="elx5_smbtn elx5_notallowedbtn"><i class="fas fa-at"></i><span class="elx5_tabhide"> '.$user_uname.'</span></a>';
					}
				}
				echo "</div>\n";//elx5_tsspace
				echo "</div>\n";//elx5_user_thread_main
				echo "</li>\n";
			}
		} else {
			echo '<li id="elx5_user_thread0" class="elx5_user_nothreads">'.$eLang->get('NO_MESSAGES')."</li>\n";
		}
		echo "</ul>\n";

		echo '<div class="elx5_dlspace">'."\n";
		echo '<a href="javascript:void(null);" onclick="elx5UserPMSOpen(0, 0, \'\');" class="elx5_btn elx5_sucbtn" title="'.$eLang->get('SEND_NEW_MESSAGE').'"><i class="fas fa-paper-plane"></i> '.$eLang->get('SEND_NEW_MESSAGE').'</a>'."\n";
		echo "</div>\n";

		if ($eDoc->countModules('user_maincol') > 0) {
			echo '<div class="user_maincol_mods">'."\n";
			$eDoc->modules('user_maincol');
			echo "</div>\n";
		}
		echo "</div>\n";//.elx_user_maincolin end
		echo "</div>\n";//.elx_user_maincol end

		$avatar = $elxis->obj('avatar')->getAvatar($elxis->user()->avatar, 200, 0, $elxis->user()->email);

		$userdata = new stdClass;
		$userdata->name = $elxis->user()->firstname.' '.$elxis->user()->lastname;
		$userdata->uname = $elxis->user()->uname;
		$userdata->uid = $elxis->user()->uid;
		$userdata->gid = $elxis->user()->gid;
		$userdata->online = 1;
		$userdata->totalmessages = $total_messages;
		$userdata->newmessages = $unread_messages;
		$userdata->bookmarks = $bookmarks;
		if ($avatar == '') {
			$userdata->avatar = $elxis->secureBase().'/components/com_user/images/noavatar.png';
		} else {
			$userdata->avatar = $avatar;
		}
		$userdata->twitter_username = '';

		echo '<div class="elx_user_sidecol">'."\n";
		$this->base_sideProfile($userdata, $elxis, $eLang, $eDoc, 'messages', $params);
		echo "</div>\n";//.elx_user_sidecol

		echo '<div class="clear"></div>'."\n";
		echo "</div>\n";

		echo "</div>\n";
		$this->base_pmsform($elxis, $eLang, $params);
	}


	/*********************************/
	/* VIEW PERSONAL MESSAGES THREAD */
	/*********************************/
	public function pmThreadHTML($rows, $total_threads, $total_messages, $unread_messages, $bookmarks, $elxis, $eLang, $eDoc, $params) {
		$eDate = eFactory::getDate();

		$myid = $elxis->user()->uid;
		$threadid = ($rows[0]->replyto > 0) ? $rows[0]->replyto : $rows[0]->id;
		if ($rows[0]->fromid == $elxis->user()->uid) {
			$other_person_id = $rows[0]->toid;
			$other_person_name = $rows[0]->toname;
		} else {
			$other_person_id = $rows[0]->fromid;
			$other_person_name = $rows[0]->fromname;
		}

		$defavatar = $elxis->secureBase().'/components/com_user/images/noavatar.png';
		$avatarpath = 'media/images/avatars/';
		if (defined('ELXIS_MULTISITE')) {
			if (ELXIS_MULTISITE > 1) { $avatarpath = 'media/images/site'.ELXIS_MULTISITE.'/avatars/'; }
		}
		$user_link = $elxis->makeURL('user:/');

		$usernowdt = $eDate->elxisToLocal('now', true);//Y-m-d H:i:s
		$ts = time() - 86400;
		$useryestdt = $eDate->formatTS($ts);
		$acl_profile = $elxis->acl()->check('com_user', 'profile', 'view');
		$time = $eDate->getTS() - $elxis->getConfig('SESSION_LIFETIME');

		if ($elxis->acl()->getLevel() <= 2) {//users send pms access check applies
			$usersendpms = (int)$params->get('usersendpms', 2);
		} else {
			$usersendpms = 2;//allowed to all
		}

		echo '<div id="elx_pmessagesthread_page">'."\n";
		echo '<h1>'.$eLang->get('DISCUSSION_THREAD').' <span>#'.$threadid."</span></h1>\n";

		echo '<div class="elx_user_wrapcol">'."\n";

		echo '<div class="elx_user_maincol">'."\n";
		echo '<div class="elx_user_maincolin">'."\n";

		echo '<ul class="elx5_user_threads" id="elx5_user_fullthread">'."\n";

		foreach ($rows as $id => $row) {
			$avatar = $defavatar;
			$class_unread = '';
			$icon_readby = '';
			$img = trim($row->from_avatar);
			$user_uidav = $row->fromid;
			$user_nameav = $row->fromname;

			if ($row->fromid == $myid) {
				$user_uid = $row->toid;
				$user_name = $row->toname;
				$user_uname = $row->to_uname;
				$fromtotxt = $eLang->get('TO');
				$iconclass = 'fas fa-arrow-circle-up';
				$lastactivity = $row->to_last_activity;
				if ($row->read == 1) {
					$icon_readby = ' <i class="fas fa-eye elx5_gray" style="font-size:13px;"></i>';
				} else {
					$icon_readby = ' <i class="fas fa-eye-slash elx5_gray" style="font-size:13px;"></i>';
				}
			} else {//sent to me
				$user_uid = $row->fromid;
				$user_name = $row->fromname;
				$user_uname = $row->from_uname;
				$fromtotxt = $eLang->get('FROM');
				$iconclass = 'fas fa-arrow-circle-down';
				$lastactivity = $row->from_last_activity;
			}
			if ($user_uname == '') { $user_uname = $user_name; }

			$icon_online = '';
			if ($user_uid > 0) {
				$icon_online = ' <i class="fas fa-user-slash elx5_gray" style="font-size:13px;"></i>';
				if ($user_uid == $myid) {
					$icon_online = ' <i class="fas fa-user elx5_green" style="font-size:13px;"></i>';
				} else if ($lastactivity > 0) {
					if ($lastactivity >= $time) { $icon_online = ' <i class="fas fa-user elx5_green" style="font-size:13px;"></i>'; }
				}
			}

			$avatar = $this->getMessageAvatar($img, $user_uidav, $user_nameav, $row->msgtype, $avatarpath, $elxis);
			$human_date = $this->humanDate($usernowdt, $useryestdt, $row->created, $eLang, $eDate);

			echo '<li id="elx5_user_thread'.$id.'">';
			echo '<div class="elx5_user_thread_avatar"><img src="'.$avatar.'" alt="'.$user_nameav.'" /></div>'."\n";
			echo '<div class="elx5_user_thread_main">'."\n";
			echo '<div class="elx5_user_thread_top">'."\n";
			echo '<div class="elx5_user_thread_date">'.$human_date."</div>\n";
			echo '<h4'.$class_unread.'><span>'.$fromtotxt.'</span> '.$user_name.$icon_online.$icon_readby."</h4>\n";
			echo "</div>\n";//elx5_user_thread_top
			echo '<div class="elx5_user_thread_fullmsg"><i class="'.$iconclass.'"></i> '.$row->message."</div>\n";
			echo "</div>\n";//elx5_user_thread_main
			echo "</li>\n";
		}
		echo "</ul>\n";

		echo '<div class="elx5_dlspace">'."\n";
		if ($other_person_id > 0) {
			if ($usersendpms > 0) {
				echo '<div class="elx5_2colwrap">'."\n";
				echo '<div class="elx5_2colbox">'."\n";
				echo '<a href="javascript:void(null);" onclick="elx5UserPMSOpen('.$threadid.', '.$other_person_id.', \''.addslashes($other_person_name).'\');" class="elx5_btn elx5_sucbtn" title="'.$eLang->get('REPLY').'"><i class="fas fa-reply"></i> '.$eLang->get('REPLY').'</a>'."\n";
				echo "</div>\n";
				echo '<div class="elx5_2colbox">'."\n";
				echo '<a href="javascript:void(null);" onclick="elx5UserPMSDelete('.$threadid.');" class="elx5_btn elx5_errorbtn" title="'.$eLang->get('DELETE').'"><i class="fas fa-trash-alt"></i><span class="elx5_tabhide"> '.$eLang->get('DELETE').'</span></a>';
				echo "</div>\n";
				echo "</div>\n";
			} else {
				echo '<a href="javascript:void(null);" onclick="elx5UserPMSDelete('.$threadid.');" class="elx5_btn elx5_errorbtn" title="'.$eLang->get('DELETE').'"><i class="fas fa-trash-alt"></i><span class="elx5_tabhide"> '.$eLang->get('DELETE').'</span></a>';
			}
		} else {
			echo '<a href="javascript:void(null);" onclick="elx5UserPMSDelete('.$threadid.');" class="elx5_btn elx5_errorbtn" title="'.$eLang->get('DELETE').'"><i class="fas fa-trash-alt"></i><span class="elx5_tabhide"> '.$eLang->get('DELETE').'</span></a>';
		}
		echo "</div>\n";

		if ($eDoc->countModules('user_maincol') > 0) {
			echo '<div class="user_maincol_mods">'."\n";
			$eDoc->modules('user_maincol');
			echo "</div>\n";
		}
		echo "</div>\n";//.elx_user_maincolin end
		echo "</div>\n";//.elx_user_maincol end

		$avatar = $elxis->obj('avatar')->getAvatar($elxis->user()->avatar, 200, 0, $elxis->user()->email);

		$userdata = new stdClass;
		$userdata->name = $elxis->user()->firstname.' '.$elxis->user()->lastname;
		$userdata->uname = $elxis->user()->uname;
		$userdata->uid = $elxis->user()->uid;
		$userdata->gid = $elxis->user()->gid;
		$userdata->online = 1;
		$userdata->totalmessages = $total_messages;
		$userdata->newmessages = $unread_messages;
		$userdata->bookmarks = $bookmarks;
		if ($avatar == '') {
			$userdata->avatar = $elxis->secureBase().'/components/com_user/images/noavatar.png';
		} else {
			$userdata->avatar = $avatar;
		}
		$userdata->twitter_username = '';

		echo '<div class="elx_user_sidecol">'."\n";
		$this->base_sideProfile($userdata, $elxis, $eLang, $eDoc, 'messages', $params);
		echo "</div>\n";//.elx_user_sidecol

		echo '<div class="clear"></div>'."\n";
		echo "</div>\n";

		echo "</div>\n";
		$this->base_pmsform($elxis, $eLang, $params);
	}


	/**************************************/
	/* HUMAN FRIENDLY DATE - FOR MESSAGES */
	/**************************************/
	private function humanDate($usernowdt, $useryestdt, $gmcreated, $eLang, $eDate) {
		$localdt = $eDate->elxisToLocal($gmcreated, true);//Y-m-d H:i:s

		$usernowdate = substr($usernowdt, 0, 10);
		$localdate = substr($localdt, 0, 10);

		if ($usernowdate == $localdate) {
			$humandate = $eLang->get('TODAY').' '.substr($localdt, 11, 5);
			return $humandate;
		}

		$useryestdate = substr($useryestdt, 0, 10);
		if ($useryestdate == $localdate) {
			$humandate = $eLang->get('YESTERDAY').' '.substr($localdt, 11, 5);
			return $humandate;
		}

		if (time() - strtotime($gmcreated) < 432000) {//last 5 days
			$humandate = $eDate->formatDate($gmcreated, '%a %H:%M');
			return $humandate;
		}

		$humandate = $eDate->formatDate($gmcreated, $eLang->get('DATE_FORMAT_4'));
		return $humandate;
	}

}

?>