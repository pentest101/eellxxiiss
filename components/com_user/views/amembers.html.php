<?php 
/**
* @version		$Id: amembers.html.php 2326 2020-01-30 19:58:33Z IOS $
* @package		Elxis
* @subpackage	Component User
* @copyright	Copyright (c) 2006-2020 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class amembersUserView extends userView {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/*******************/
	/* SHOW USERS LIST */
	/*******************/
	public function listUsers($rows, $options, $elxis, $eLang) {
		$eDate = eFactory::getDate();

		$link = $elxis->makeAURL('user:/');
		$inlink = $elxis->makeAURL('user:/', 'inner.php');

		$htmlHelper = $elxis->obj('html');

		elxisLoader::loadFile('includes/libraries/elxis/form5.class.php');

		$parts = array();
		if ($options['firstname'] != '') { $parts[] = 'firstname='.$options['firstname']; }
		if ($options['lastname'] != '') { $parts[] = 'lastname='.$options['lastname']; }
		if ($options['uname'] != '') { $parts[] = 'uname='.$options['uname']; }
		if ($options['email'] != '') { $parts[] = 'email='.$options['email']; }
		if ($options['city'] != '') { $parts[] = 'city='.$options['city']; }
		if ($options['address'] != '') { $parts[] = 'address='.$options['address']; }
		if ($options['phone'] != '') { $parts[] = 'phone='.$options['phone']; }
		if ($options['mobile'] != '') { $parts[] = 'mobile='.$options['mobile']; }
		if ($options['website'] != '') { $parts[] = 'website='.$options['website']; }
		if ($options['uid'] > 0) { $parts[] = 'uid='.$options['uid']; }

		$ordlink = ($parts) ? $link.'users/?'.implode('&amp;', $parts).'&amp;' : $link.'users/?';
		$is_filtered = $parts ? true : false;
		unset($parts);

		$canedit = ($elxis->acl()->check('com_user', 'profile', 'edit') > 1) ? true : false;

		echo '<h2>'.$eLang->get('MEMBERSLIST')."</h2>\n";

		echo '<div class="elx5_box elx5_border_blue">'."\n";
		echo '<div class="elx5_box_body">'."\n";
		echo '<div class="elx5_sticky">'."\n";

		echo '<div class="elx5_dataactions">'."\n";
		if ($canedit) {
			echo '<a href="'.$link.'users/edit.html?uid=0" class="elx5_dataaction elx5_datahighlight" data-alwaysactive="1" title="'.$eLang->get('ADD').'"><i class="fas fa-plus"></i><span class="elx5_lmobhide"> '.$eLang->get('ADD')."</span></a>\n";
			echo '<a href="javascript:void(null);" onclick="elx5UserEdit();" class="elx5_dataaction elx5_lmobhide" data-selector="1" title="'.$eLang->get('EDIT').'"><i class="fas fa-edit"></i><span class="elx5_smallscreenhide"> '.$eLang->get('EDIT')."</span></a>\n";
		}
		echo '<a href="javascript:void(null);" onclick="elx5UserContactForm();" class="elx5_dataaction" data-selector="1" title="'.$eLang->get('CONTACT').'"><i class="fas fa-envelope"></i><span class="elx5_smallscreenhide"> '.$eLang->get('CONTACT')."</span></a>\n";
		if ($elxis->acl()->check('com_user', 'profile', 'delete') > 1) {
			echo '<a href="javascript:void(null);" class="elx5_dataaction" title="'.$eLang->get('DELETE').'" onclick="elx5DeleteTableRows(\'userstbl\', false);" data-selector="1" data-activeclass="elx5_datawarn"><i class="fas fa-trash"></i><span class="elx5_lmobhide"> '.$eLang->get('DELETE')."</span></a>\n";
		}
		if ($is_filtered) {
			echo '<a href="javascript:void(null);" class="elx5_dataaction elx5_dataorange" data-alwaysactive="1" data-elx5tooltip="'.$eLang->get('FILTERS_HAVE_APPLIED').'" onclick="elx5Toggle(\'userssearchoptions\');"><i class="fas fa-filter"></i><span class="elx5_smallscreenhide"> '.$eLang->get('SEARCH_OPTIONS')."</span></a>\n";
		} else {
			echo '<a href="javascript:void(null);" class="elx5_dataaction elx5_datahighlight" data-alwaysactive="1" title="'.$eLang->get('SEARCH_OPTIONS').'" onclick="elx5Toggle(\'userssearchoptions\');"><i class="fas fa-filter"></i><span class="elx5_smallscreenhide"> '.$eLang->get('SEARCH_OPTIONS')."</span></a>\n";
		}
		echo "</div>\n";

		echo '<div class="elx5_invisible" id="userssearchoptions">'."\n";
		echo '<div class="elx5_actionsbox elx5_dspace">';
		$form = new elxis5Form(array('idprefix' => 'us', 'labelclass' => 'elx5_label', 'sideclass' => 'elx5_labelside'));
		$form->openForm(array('name' => 'fmsrusers', 'method' => 'get', 'action' => $link, 'id' => 'fmsrusers'));
		$form->addHTML('<div class="elx5_2colwrap"><div class="elx5_2colbox elx5_spad">');
		$form->addNumber('uid', $options['uid'], $eLang->get('ID'), array('min' => '0', 'max' => 9999999, 'step' => 1));
		$form->addText('firstname', $options['firstname'], $eLang->get('FIRSTNAME'));
		$form->addText('lastname', $options['lastname'], $eLang->get('LASTNAME'));
		$form->addText('uname', $options['uname'], $eLang->get('USERNAME'), array('dir' => 'ltr'));
		$form->addText('city', $options['city'], $eLang->get('CITY'));
		$form->addHTML('</div><div class="elx5_2colbox elx5_spad">');
		$form->addText('address', $options['address'], $eLang->get('ADDRESS'));
		$form->addText('email', $options['email'], $eLang->get('EMAIL'), array('dir' => 'ltr'));
		$form->addText('phone', $options['phone'], $eLang->get('TELEPHONE'), array('dir' => 'ltr'));
		$form->addText('mobile', $options['mobile'], $eLang->get('MOBILE'), array('dir' => 'ltr'));
		$form->addText('website', $options['website'], $eLang->get('WEBSITE'), array('dir' => 'ltr'));
		$form->addHTML('</div></div>');
		$form->addHidden('sn', $options['sn']);
		$form->addHidden('so', $options['so']);
		$form->addHTML('<div class="elx5_vpad">');
		$form->addButton('srcbtn', $eLang->get('SEARCH'), 'submit');
		$form->addHTML('</div>');
		$form->closeForm();
		echo "</div>\n";//elx5_actionsbox
		echo "</div>\n";//#userssearchoptions
		echo "</div>\n";//elx5_sticky

		echo '<table id="userstbl" class="elx5_datatable" data-deletelng="'.addslashes($eLang->get('AREYOUSURE')).'" data-deletepage="'.$inlink.'users/deleteuser">'."\n";
		echo "<thead>\n";
		echo "<tr>\n";
		echo $htmlHelper->tableHead('&#160;', 'elx5_nosorting elx5_center');
		echo $htmlHelper->sortableTableHead($ordlink, $eLang->get('ID'), 'uid', $options['sn'], $options['so'], 'elx5_center elx5_tabhide');
		echo $htmlHelper->sortableTableHead($ordlink, $eLang->get('NAME'), 'firstname', $options['sn'], $options['so'], 'elx5_lmobhide');
		echo $htmlHelper->sortableTableHead($ordlink, $eLang->get('USERNAME'), 'uname', $options['sn'], $options['so']);
		echo $htmlHelper->sortableTableHead($ordlink, $eLang->get('ACTIVE'), 'block', $options['sn'], $options['so'], 'elx5_center');
		echo $htmlHelper->sortableTableHead($ordlink, $eLang->get('GROUP'), 'groupname', $options['sn'], $options['so'], 'elx5_tabhide');
		echo $htmlHelper->sortableTableHead($ordlink, $eLang->get('EMAIL'), 'email', $options['sn'], $options['so'], 'elx5_smallscreenhide');
		echo $htmlHelper->sortableTableHead($ordlink, $eLang->get('REGDATE_SHORT'), 'registerdate', $options['sn'], $options['so'], 'elx5_midscreenhide');
		echo $htmlHelper->sortableTableHead($ordlink, $eLang->get('LASTVISIT'), 'lastvisitdate', $options['sn'], $options['so'], 'elx5_midscreenhide');
		echo $htmlHelper->tableHead($eLang->get('ARTICLES'), 'elx5_nosorting elx5_center elx5_smallscreenhide');
		echo "</tr>\n";
		echo "</thead>\n";

		echo "<tbody>\n";
		if ($rows) {
			$can_manage_groups = ($elxis->acl()->check('com_user', 'groups', 'manage') > 0) ? true : false;
			$canblock = ($elxis->acl()->check('com_user', 'profile', 'block') > 0) ? true : false;

			foreach ($rows as $row) {
				if ($row['block'] == 0) {
					$status_class = 'elx5_statuspub';
					$status_title = $eLang->get('ACTIVE');
				} else {
					$status_class = 'elx5_statusunpub';
					$status_title = $eLang->get('INACTIVE');
				}

				$emailtxt = (strlen($row['email']) > 25) ? substr($row['email'], 0, 22).'...' : $row['email'];

				echo '<tr id="datarow'.$row['uid'].'">'."\n";
				echo '<td class="elx5_center">';
				echo '<input type="checkbox" name="dataprimary" id="dataprimary'.$row['uid'].'" class="elx5_datacheck" value="'.$row['uid'].'" />';
				echo '<label for="dataprimary'.$row['uid'].'"></label></td>'."\n";
				echo '<td class="elx5_center elx5_tabhide">'.$row['uid'].'</td>'."\n";
				echo '<td id="udataname'.$row['uid'].'" data-value="'.addslashes($row['firstname'].' '.$row['lastname']).'" class="elx5_lmobhide">'.$row['firstname'].' '.$row['lastname'].'</td>'."\n";
				if ($canedit) {
					echo '<td><a href="'.$link.'users/edit.html?uid='.$row['uid'].'" title="'.$eLang->get('EDIT').'">'.$row['uname']."</a></td>\n";
				} else {
					echo '<td>'.$row['uname'].'</td>'."\n";
				}
				if ($canedit && $canblock) {
					echo '<td class="elx5_center"><a href="javascript:void(null);" onclick="elx5ToggleStatus('.$row['uid'].', this);" class="elx5_statusicon '.$status_class.'" title="'.$status_title.' - '.$eLang->get('CLICK_TOGGLE_STATUS').'" data-actlink="'.$link.'users/toggleuser"></a></td>'."\n";
				} else {
					echo '<td class="elx5_center"><a href="javascript:void(null);" class="elx5_statusicon '.$status_class.'" title="'.$status_title.'"></a></td>'."\n";
				}
				if ($can_manage_groups) {
					echo '<td class="elx5_tabhide"><a href="'.$link.'groups/" title="'.$eLang->get('EDIT_GROUP').'">'.$row['groupname']."</a></td>\n";
				} else {
					echo '<td class="elx5_tabhide">'.$row['groupname'].'</td>'."\n";
				}
				echo '<td id="udataemail'.$row['uid'].'" data-value="'.$row['email'].'" class="elx5_smallscreenhide"><a href="mailto:'.$row['email'].'" title="'.$row['email'].'">'.$emailtxt.'</a></td>'."\n";
				echo '<td class="elx5_midscreenhide">'.$eDate->humanDate($row['registerdate']).'</td>'."\n";
				echo '<td class="elx5_midscreenhide">'.$eDate->humanDate($row['lastvisitdate']).'</td>'."\n";
				echo '<td class="elx5_center elx5_smallscreenhide">'.$row['articles'].'</td>'."\n";
				echo "</tr>\n";
			}
		} else {
			echo '<tr id="datarow0" class="elx5_rowwarn"><td class="elx5_center" colspan="10">'.$eLang->get('NO_RESULTS')."</td></tr>\n";
		}
		echo "</tbody>\n";
		echo "</table>\n";

		echo "</div>\n";//elx5_box_body

		if ($rows) {
			$linkbase = $ordlink.'sn='.$options['sn'].'&amp;so='.$options['so'];
			echo $htmlHelper->tableSummary($linkbase, $options['page'], $options['maxpage'], $options['total']);
		}

		echo "</div>\n";//elx5_box

		echo $htmlHelper->startModalWindow($eLang->get('CONTACT'), 'uc', '', false, '', '');
		$form = new elxis5Form(array('idprefix' => 'cf', 'labelclass' => 'elx5_label', 'sideclass' => 'elx5_labelside'));
		$form->openForm(array('name' => 'fmcontactuser', 'method' =>'post', 'action' => $inlink.'users/mailuser', 'id' => 'fmcontactuser', 'onsubmit' => 'return false;'));
		$form->openFieldset($eLang->get('CONTACT'));
		$senderinfo = '<strong>'.$elxis->getConfig('MAIL_FROM_NAME').'</strong> &lt;'.$elxis->getConfig('MAIL_FROM_EMAIL').'&gt;';
		$form->addInfo($eLang->get('SENDER'), $senderinfo);
		$form->addInfo($eLang->get('RECIPIENT'), '<span id="murecipienttext"></span>');
		$form->addText('subject', '', $eLang->get('SUBJECT'), array('required' => 'required', 'maxlength' => 160));
		$form->addTextarea('message', '', $eLang->get('MESSAGE'), array('required' => 'required'));
		$form->closeFieldset();
		$form->addHidden('uid', 0);
		$form->addHTML('<div class="elx5_vpad">');
		$form->addButton('sendmsg', $eLang->get('SEND'), 'button', array('class' => 'elx5_btn elx5_sucbtn', 'onclick' => 'elx5UserContactSend();', 'data-waitlng' => $eLang->get('PLEASE_WAIT'), 'data-sendlng' => $eLang->get('SEND')));
		$form->addHTML('</div>');
		$form->closeForm();
		echo $htmlHelper->endModalWindow(false);
	}


	/******************/
	/* EDIT USER HTML */
	/******************/
	public function editUser($row, $info, $userparams, $elxis, $eLang) {
		$eDate = eFactory::getDate();

		$action = $elxis->makeAURL('user:users/save.html', 'inner.php');

		if ($row->uid > 0) {
			$pgtitle = $eLang->get('EDITPROFILE').' <span>'.$row->uname.'</span>';
		} else {
			$pgtitle = $eLang->get('NEW_USER');
		}

		elxisLoader::loadFile('includes/libraries/elxis/form5.class.php');
		$form = new elxis5Form(array('idprefix' => 'epr'));
		$form->openForm(array('name' => 'fmusedit', 'method' =>'post', 'action' => $action, 'id' => 'fmusedit', 'enctype' => 'multipart/form-data', 'autocomplete' => 'off'));
		$form->startTabs(array($eLang->get('ACCOUNT_DETAILS'), $eLang->get('PERSONAL_DETAILS')));

		$form->openTab();
		$form->openFieldset();
		$form->addText('firstname', $row->firstname, $eLang->get('FIRSTNAME'), array('required' => 'required', 'maxlength' => 60));
		$form->addText('lastname', $row->lastname, $eLang->get('LASTNAME'), array('required' => 'required', 'maxlength' => 60));
		$form->addUsergroup('gid', $eLang->get('GROUP'), $row->gid, 2, $elxis->acl()->getLevel());

		$attrs = array('required' => 'required', 'dir' => 'ltr', 'maxlength' => 60);
		if (intval($row->uid) > 0) {
			$attrs['readonly'] = 'readonly';
		} else {
			$attrs['pattern'] = '[A-Za-z0-9_\-]{4,32}';
			$attrs['tip'] = $eLang->get('MINCHARDIGSYM');
		}
		$form->addText('uname', $row->uname, $eLang->get('USERNAME'), $attrs);

		$form->setOption('tipclass', 'elx5_warntip');
		$attrs = array('maxlength' => 60, 'password_meter' => 1, 'pattern' => '[A-Za-z0-9_!@\-]{6,}', 'autocomplete' => 'new-password');
		if (intval($row->uid) > 0) {
			$attrs['tip'] = $eLang->get('ONLY_IF_CHANGE');
		} else {
			$attrs['required'] = 'required';
			$attrs['tip'] = $eLang->get('MINLENGTH6').'. Acceptable characters are A-Z a-z 0-9 _ - ! @';
		}
		$form->addPassword('pword', '', $eLang->get('PASSWORD'), $attrs);
		$form->setOption('tipclass', 'elx5_tip');

		$attrs = array('maxlength' => 60, 'match' => 'eprpword', 'autocomplete' => 'new-password');
		if (intval($row->uid) == 0) { $attrs['required'] = 'required'; }
		$form->addPassword('pword2', '', $eLang->get('PASSWORD_AGAIN'), $attrs);
		$form->addEmail('email', $row->email, $eLang->get('EMAIL'), array('required' => 'required',  'dir' => 'ltr', 'size' => 30));
		$form->addYesNo('mailpw', $eLang->get('SEND_ACCDET'), 0, array('tip' => $eLang->get('SEND_ACCDET_DESC')));
		$form->addYesNo('block', $eLang->get('BLOCKUSER'), $row->block, array('enablecolor' => 'red'));

		if ($elxis->acl()->check('com_user', 'profile', 'uploadavatar') == 1) {
			$avatar = (trim($row->avatar) != '') ? 'media/images/avatars/'.$row->avatar : '';
			$form->addImage('avatar', $avatar, $eLang->get('AVATAR'), array('tip' => $eLang->get('AVATAR_D')));
		}

		$val = $eDate->elxisToLocal($row->expiredate, true);
		$datetime = new DateTime($val);
		$val = $datetime->format($eLang->get('DATE_FORMAT_BOX'));
		$form->addDate('expiredate', $val, $eLang->get('EXPIRATION_DATE'), array('class' => 'elx5_text elx5_mediumtext'));
		$form->closeFieldset();

		$form->openFieldset($eLang->get('ACTIVITY'));
		if ($row->uid > 0) {
			$form->addInfo($eLang->get('MEMBERSINCE'), $eDate->formatDate($row->registerdate, $eLang->get('DATE_FORMAT_10')));
			if ((trim($row->lastvisitdate) != '') && ($row->lastvisitdate != '1970-01-01 00:00:00')) {
				$form->addInfo($eLang->get('LASTVISIT'), $eDate->formatDate($row->lastvisitdate, $eLang->get('DATE_FORMAT_10')));
			} else {
				$form->addInfo($eLang->get('LASTVISIT'), $eLang->get('NEVER'));
			}

			if (trim($row->lastclicks) != '') {
				$clicks = json_decode($row->lastclicks, true);
				usort($clicks, array($this, 'base_sortUserClicks'));
				$txt = '<ul class="elx5_timelist">'."\n";
				foreach ($clicks as $click) {
					$date = $eDate->humanDate('', $click['ts']);
					$pg = (strlen($click['page']) > 60) ? substr($click['page'], 0, 57).'...' : $click['page'];
					$txt .= '<li><div>'.$date.'</div><a href="'.$click['page'].'" target="_blank">'.$pg."</a></li>\n";
				}
				$txt .= "</ul>\n";
				$form->addInfo($eLang->get('LATEST_PAGES'), $txt);
			}
		}

		$form->addInfo($eLang->get('ARTICLES'), $info->articles);
		$form->addInfo($eLang->get('COMMENTS'), $info->comments);
		$form->addInfo($eLang->get('PROFILE_VIEWS'), $row->profile_views);
		$form->addInfo($eLang->get('TIMES_ONLINE'), $row->times_online);
		$form->closeFieldset();
		$form->closeTab();

		$form->openTab();

		$form->openFieldset($eLang->get('PREFERENCES'));
		$val = (trim($row->preflang) == '') ? $eLang->getinfo('LANGUAGE') : $row->preflang;
		$form->addLanguage('preflang', $eLang->get('LANGUAGE'), $val, array('tip' => $eLang->get('SETPREFLANG')), 2, 5, true);
		$tz = ($elxis->user()->uid == $row->uid) ? $eDate->getTimezone() : $row->timezone;
		if (trim($tz) == '') { $tz = $eDate->getTimezone(); }
		$user_daytime = $eDate->worldDate('now', $tz, $eLang->get('DATE_FORMAT_12'));
		$form->addTimezone('timezone', $eLang->get('TIMEZONE'), $tz, array('tip' => $user_daytime));
		$form->closeFieldset();

		$form->openFieldset();

		if ($row->gender != 'female') { $row->gender = 'male'; }
		$foptions = array();
		$foptions[] = $form->makeOption('male', $eLang->get('MALE'));
		$foptions[] = $form->makeOption('female', $eLang->get('FEMALE'));
		$form->addRadio('gender', $eLang->get('GENDER'), $row->gender, $foptions);

		$val = '';
		if (trim($row->birthdate) != '') {
			$val = $eDate->elxisToLocal($row->birthdate, true);
			$datetime = new DateTime($val);
			$val = $datetime->format($eLang->get('DATE_FORMAT_BOX'));
		}
		$form->addDate('birthdate', $val, $eLang->get('BIRTHDATE'), array('class' => 'elx5_text elx5_mediumtext'));
		$form->addText('occupation', $row->occupation, $eLang->get('OCCUPATION'), array('maxlength' => 120));
		$val = (trim($row->country) == '') ? $eLang->getinfo('REGION') : $row->country;
		$form->addCountry('country', $eLang->get('COUNTRY'), $val, array('dir' => 'rtl'));
		$form->addText('city', $row->city, $eLang->get('CITY'));
		$form->addText('postalcode', $row->postalcode, $eLang->get('POSTAL_CODE'));
		$form->addText('address', $row->address, $eLang->get('ADDRESS'), array('maxlength' => 120));
		$form->addTel('phone', $row->phone, $eLang->get('TELEPHONE'), array('maxlength' => 40, 'pattern' => '[0-9\+\-\s]{6,}'));
		$form->addTel('mobile', $row->mobile, $eLang->get('MOBILE'), array('maxlength' => 40, 'pattern' => '[0-9\+\-\s]{6,}'));
		$form->addURL('website', $row->website, $eLang->get('WEBSITE'), array('maxlength' => 120));
		$val = $userparams->get('twitter', '');
		$form->addText('params_twitter', $val, $eLang->get('TWITACCOUNT'), array('tip' => $eLang->get('TWITACCOUNT_D'), 'maxlength' => 60, 'pattern' => '[A-Za-z0-9_]{1,15}'));
		$form->closeFieldset();
		$form->closeTab();

		$form->endTabs();

		$form->addHidden('uid', $row->uid);
		$form->addToken('fmusedit');
		$form->addHidden('task', '');

		$form->closeForm();
	}

}

?>