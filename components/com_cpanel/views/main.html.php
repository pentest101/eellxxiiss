<?php 
/**
* @version		$Id: main.html.php 2394 2021-04-08 17:16:01Z IOS $
* @package		Elxis
* @subpackage	CPanel component
* @copyright	Copyright (c) 2006-2021 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class mainCPView extends cpanelView {


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/********************************/
	/* CONTROL PANEL DASHBOARD HTML */
	/********************************/
	public function dashboardHTML($boarditems, $updates, $elxis, $eLang, $eDoc) {
		$configlink = $elxis->makeAURL('cpanel:config.html');	
?>
		<h2><?php echo $eLang->get('CONTROL_PANEL'); ?></h2>
<?php 
		if ($updates['elxis']['updated'] === false) {
			$v = '<strong>Elxis '.$updates['elxis']['version'].' '.$updates['elxis']['codename'].' r'.$updates['elxis']['revision'].'</strong>';
			$txt = sprintf($eLang->get('OUTDATED_ELXIS_UPDATE_TO'), $v);
			echo '<div class="elx5_error elx5_center">'.$txt."</div>\n";

			$ms = defined('ELXIS_MULTISITE') ? ELXIS_MULTISITE : 1;
			if ($ms < 2) {
				$can_update = $elxis->acl()->check('com_extmanager', 'components', 'install');
				$can_update += $elxis->acl()->check('com_extmanager', 'modules', 'install');
				$can_update += $elxis->acl()->check('com_extmanager', 'templates', 'install');
				$can_update += $elxis->acl()->check('com_extmanager', 'engines', 'install');
				$can_update += $elxis->acl()->check('com_extmanager', 'auth', 'install');
				$can_update += $elxis->acl()->check('com_extmanager', 'plugins', 'install');
				if ($can_update > 0) {
					if (($elxis->getConfig('SECURITY_LEVEL') > 0) && ($elxis->user()->gid <> 1)) { $can_update = 0; }
				}
				if ($can_update > 0) {
					$updelxislink = $elxis->makeAURL('extmanager:install/upelxis', 'inner.php');
					echo '<div class="elx5_dlspace elx5_center"><a href="'.$updelxislink.'" class="elx5_btn elx5_ibtn elx5_sucbtn">Update Elxis</a></div>'."\n";
				}
			}
		}
?>

		<div class="elxcp_panel_wrapper">
			<div class="elxcp_mainpanel">
				<div class="elxcp_dashboard elx5_lmobhide">
<?php 
				if ($boarditems) {
					foreach ($boarditems as $item) {
						if (!isset($item->iconclass)) { continue; }
						echo '<a href="'.$item->link.'" class="elxcp_dashb_item" data-elx5tooltip="'.$item->description.'">';
						if ($item->mark > 0) {
							echo '<div class="elxcp_dashb_icon"><i class="'.$item->iconclass.'"></i><div class="elxcp_dashb_icon_mark">'.$item->mark.'</div></div>'."\n";
						} else {
							echo '<div class="elxcp_dashb_icon"><i class="'.$item->iconclass.'"></i></div>'."\n";
						}
						echo '<div class="elxcp_dashb_text elx5_tabhide">'.$item->title."</div>\n";
						echo "</a>\n";
					}
				}
?>
				</div>
				<?php $eDoc->modules('cpanelbottom', 'none'); ?>
			</div>
			<div class="elxcp_sidepanel">
				<?php $eDoc->modules('cpanel', 'none'); ?>
			</div>
		</div>

<?php 
	}


	/*******************************/
	/* ELXIS GENERAL SETTINGS HTML */
	/*******************************/
	public function configHTML($data, $elxis, $eLang) {
		elxisLoader::loadFile('includes/libraries/elxis/form5.class.php');

		$inlink = $elxis->makeAURL('cpanel:/', 'inner.php');

		$form = new elxis5Form(array('idprefix' => 'cfg'));
		$form->openForm(array('name' => 'fmconfig', 'method' =>'post', 'action' => $inlink.'saveconfig', 'id' => 'fmconfig', 'data-inlink' => $inlink, 'data-waitlng' => $eLang->get('PLEASE_WAIT'), 'autocomplete' => 'off'));
		$form->startTabs(array(
			$eLang->get('GENERAL'), $eLang->get('PERFORMANCE'), $eLang->get('USERS_AND_REGISTRATION'), $eLang->get('EMAIL'), 
			$eLang->get('DATABASE'), 'FTP', $eLang->get('SESSION'), $eLang->get('SECURITY'), $eLang->get('ERRORS')
		));

		$form->openTab();
		$trdata = array('category' => 'config', 'element' => 'sitename', 'elid' => 1);
		$form->addMLText('sitename', $trdata, $elxis->getConfig('SITENAME'), $eLang->get('SITENAME'), array('required' => 'required', 'maxlength' => 255));

		$options = array(
			array('name' => $eLang->get('OFFLINE'), 'value' => 0, 'color' => 'red'),
			array('name' => $eLang->get('ONLINE'), 'value' => 1, 'color' => 'green'),
			array('name' => $eLang->get('ONLINE_USERS'), 'value' => 3, 'color' => 'yellow'),
			array('name' => $eLang->get('ONLINE_ADMINS'), 'value' => 2, 'color' => 'orange')
		);
		$form->addItemStatus('online', $eLang->get('WEBSITE_STATUS'), $elxis->getConfig('ONLINE'), $options);

		$form->addText('offline_message', stripslashes($elxis->getConfig('OFFLINE_MESSAGE')), $eLang->get('OFFLINE_MSG'), array('tip' => $eLang->get('OFFLINE_MSG_INFO')));
		$form->addUrl('url', $elxis->getConfig('URL'), $eLang->get('URL_ADDRESS'), array('required' => 'required', 'dir' => 'ltr'));
		$form->addText('repo_path', $elxis->getConfig('REPO_PATH'), $eLang->get('REPO_PATH'), array('dir' => 'ltr', 'tip' => $eLang->get('REPO_PATH_INFO')));
		$form->addYesNo('sef', $eLang->get('FRIENDLY_URLS'), $elxis->getConfig('SEF'), array('tip' => $eLang->get('SEF_INFO')));

		$seomatch = $elxis->getConfig('SEO_MATCH');
		if ($seomatch === '') { $seomatch = 'normal'; }//Elxis 4.3 compatibility
		$options = array();
		$options[] = $form->makeOption('exact', $eLang->get('EXACT'));
		$options[] = $form->makeOption('normal', $eLang->get('NORMAL'));
		$form->addSelect('seo_match', $eLang->get('SEO_TITLES_MATCH'), $seomatch, $options, array('tip' => $eLang->get('SEO_TITLES_MATCH_HELP')));
		$form->addYesNo('statistics', $eLang->get('STATISTICS'), $elxis->getConfig('STATISTICS'), array('tip' => $eLang->get('STATISTICS_INFO')));
		$form->addText('default_route', $elxis->getConfig('DEFAULT_ROUTE'), $eLang->get('DEFAULT_ROUTE'), array('required' => 'required', 'dir' => 'ltr', 'tip' => $eLang->get('DEFAULT_ROUTE_INFO')));

		$jquery = $elxis->getConfig('JQUERY');
		if ($jquery === '') { $jquery = '3m'; }//Elxis 4.5 rev1874- compatibility
		$options = array();
		$options[] = $form->makeOption('1', 'jQuery 1.x');
		$options[] = $form->makeOption('1m', 'jQuery 1.x + Migration plugin');
		$options[] = $form->makeOption('3', 'jQuery 3.x');
		$options[] = $form->makeOption('3m', 'jQuery 3.x + Migration plugin');
		$form->addSelect('jquery', 'jQuery', $jquery, $options, array('dir' => 'ltr', 'tip' => 'jQuery|jQuery version to use. Default option jQuery 3.x + Migration plugin. Use 1.x + Migration if version 3.x causes you problems. We recommend the use of migration plugin for compatibility with old scripts.'));

		$form->openFieldset($eLang->get('META_DATA'));
		$trdata = array('category' => 'config', 'element' => 'metadesc', 'elid' => 1);
		$form->addMLText('metadesc', $trdata, stripslashes($elxis->getConfig('METADESC')), $eLang->get('DESCRIPTION'), array('required' => 'required', 'tip' => $eLang->get('META_DATA_INFO')));
		$trdata = array('category' => 'config', 'element' => 'metakeys', 'elid' => 1);
		$form->addMLText('metakeys', $trdata, stripslashes($elxis->getConfig('METAKEYS')), $eLang->get('KEYWORDS'), array('required' => 'required', 'tip' => $eLang->get('KEYWORDS_INFO')));
		$form->closeFieldset();

		$form->openFieldset($eLang->get('LOCALE'));
		$form->addLanguage('lang', $eLang->get('LANGUAGE'), $elxis->getConfig('LANG'), array('tip' => $eLang->get('LANG_CHANGE_WARN')), 1, 5, true);
		$ilangs = $eLang->getAllLangs(true);
		$slangs = array();
		if ($elxis->getConfig('SITELANGS') != '') { $slangs = explode(',',$elxis->getConfig('SITELANGS')); }
		$options = array();
		foreach ($ilangs as $lng => $info) {
			$options[] = $form->makeOption($lng, $info['NAME']);
		}
		$form->addMultiSelect('sitelangs', $eLang->get('SITE_LANGS'), $slangs, $options, array('flagvalues' => 1, 'noselected_text' => '- '.$eLang->get('ALL_AVAILABLE').' -', 'tip' => $eLang->get('SITE_LANGS_DESC')));
		$form->addYesNo('multilinguism', $eLang->get('MULTILINGUISM'), $elxis->getConfig('MULTILINGUISM'), array('tip' => $eLang->get('MULTILINGUISM_INFO')));
		$form->addYesNo('lang_detect', $eLang->get('LANG_DETECTION'), $elxis->getConfig('LANG_DETECT'), array('tip' => $eLang->get('LANG_DETECTION_INFO')));
		$current_daytime = eFactory::getDate()->worldDate('now', $elxis->getConfig('TIMEZONE'), $eLang->get('DATE_FORMAT_12'));
		$form->addTimezone('timezone', $eLang->get('TIMEZONE'), $elxis->getConfig('TIMEZONE'), array('tip' => $current_daytime));
		$form->closeFieldset();

		$form->openFieldset($eLang->get('STYLE_LAYOUT'));
		$options = array();
		if ($data['templates']) {
			foreach ($data['templates'] as $tpl => $ttl) {
				$options[] = $form->makeOption($tpl, $ttl);
			}
		}
		$form->addSelect('template', $eLang->get('SITE_TEMPLATE'), $elxis->getConfig('TEMPLATE'), $options, array('dir' => 'ltr'));
		$options = array();
		if ($data['atemplates']) {
			foreach ($data['atemplates'] as $tpl => $ttl) {
				if (in_array($tpl, array('iris', 'butterfly'))) {
					$options[] = $form->makeOption($tpl, $ttl.' - '.$eLang->get('DEPRECATED'));
				} else {
					$options[] = $form->makeOption($tpl, $ttl);
				}
			}
		}
		$form->addSelect('atemplate', $eLang->get('ADMIN_TEMPLATE'), $elxis->getConfig('ATEMPLATE'), $options, array('dir' => 'ltr'));
		$form->closeFieldset();

		$form->openFieldset($eLang->get('CRONJOBS'));
		$options = array(
			array('name' => $eLang->get('NO'), 'value' => 0, 'color' => 'red'),
			array('name' => $eLang->get('YES').' - Elxis', 'value' => 1, 'color' => 'yellow'),
			array('name' => $eLang->get('YES').' - '.$eLang->get('EXTERNAL'), 'value' => 2, 'color' => 'green')
		);
		$form->addItemStatus('cronjobs', $eLang->get('CRONJOBS'), $elxis->getConfig('CRONJOBS'), $options, array('tip' => $eLang->get('CRONJOBS_INFO')));

		if ($elxis->getConfig('CRONJOBS') == 2) {
			$txt = $eLang->get('URL_ADDRESS').' <span dir="ltr" class="elx5_orange">'.$elxis->getConfig('URL').'/?cronkey='.sha1($elxis->getConfig('URL').$elxis->getConfig('ENCRYPT_KEY').$elxis->getConfig('REPO_PATH')).'</span>';
			$form->addInfo('', $txt);
		}

		$txt = $eLang->get('LAST_RUN').': <span id="cronlastrun">';
		if ($data['lastcron'] == -1) {
			$txt .= $eLang->get('NEVER');
		} else {
			if ($data['lastcron'] < 3600) {
				$min = floor($data['lastcron'] / 60);
				$sec = $data['lastcron'] % 60;
				$txt .= sprintf($eLang->get('MIN_SEC_AGO'), $min, $sec);
			} else if ($data['lastcron'] < 7200) {
				$min = floor(($data['lastcron'] - 3600) / 60);
				$txt .= sprintf($eLang->get('HOUR_MIN_AGO'), $min);
			} else if ($data['lastcron'] < 172800) {//2 days
				$hours = floor($data['lastcron'] / 3600);
				$sec = $data['lastcron'] - ($hours * 3600);
				$min = floor($sec / 60);
				$txt .= sprintf($eLang->get('HOURS_MIN_AGO'), $hours, $min);
			} else {
				$txt .= eFactory::getDate()->formatTS($data['lastcronts'], $eLang->get('DATE_FORMAT_4'));
			}
		}
		$txt .= '</span>';
		if ($elxis->getConfig('CRONJOBS') > 0) {//todo
			$txt .= ' <a href="javascript:void(null);" onclick="elx5CPRunCron(\'cronlastrun\', \'runcroncp\');" id="runcroncp">'.$eLang->get('RUN_NOW').'</a>';
		}
		$form->addInfo('', $txt);
		$cronprob = $elxis->getConfig('CRONJOBS_PROB');
		if ($cronprob === '') { $cronprob = 10; }//Elxis 4.3 compatibility
		$form->addSlider('cronjobs_prob', $cronprob, $eLang->get('CRONJOBS_PROB'), array('showvalue' => 1, 'min' => 0, 'max' => 100, 'tip' => $eLang->get('CRONJOBS_PROB_INFO')));
		unset($cronprob);
		$form->closeFieldset();

		$form->closeTab();

		$form->openTab();
		$form->addYesNo('gzip', $eLang->get('GZIP_COMPRESSION'), $elxis->getConfig('GZIP'), array('tip' => $eLang->get('GZIP_COMPRESSION_DESC')));
		$form->openFieldset($eLang->get('CACHE'));
		$form->addYesNo('cache', $eLang->get('CACHE'), $elxis->getConfig('CACHE'), array('tip' => $eLang->get('CACHE_INFO')));
		$options = array();
		$options[] = $form->makeOption(600, '10 '.$eLang->get('MINUTES'));
		$options[] = $form->makeOption(900, '15 '.$eLang->get('MINUTES'));
		$options[] = $form->makeOption(1200, '20 '.$eLang->get('MINUTES'));
		$options[] = $form->makeOption(1800, '30 '.$eLang->get('MINUTES'));
		$options[] = $form->makeOption(2700, '45 '.$eLang->get('MINUTES'));
		$options[] = $form->makeOption(3600, '60 '.$eLang->get('MINUTES'));
		$options[] = $form->makeOption(7200, '2 '.$eLang->get('HOURS'));
		$options[] = $form->makeOption(10800, '3 '.$eLang->get('HOURS'));
		$options[] = $form->makeOption(21600, '6 '.$eLang->get('HOURS'));
		$options[] = $form->makeOption(43200, '12 '.$eLang->get('HOURS'));
		$options[] = $form->makeOption(86400, '24 '.$eLang->get('HOURS'));
		$options[] = $form->makeOption(172800, '48 '.$eLang->get('HOURS'));
		$form->addSelect('cache_time', $eLang->get('LIFETIME'), $elxis->getConfig('CACHE_TIME'), $options, array('tip' => $eLang->get('CACHE_TIME_INFO')));
		$form->closeFieldset();
		$form->openFieldset($eLang->get('MINIFIER_CSSJS'));
		$form->addNote($eLang->get('MINIFIER_INFO'), 'elx5_sminfo');
		$options = array(
			array('name' => $eLang->get('NO'), 'value' => 0, 'color' => 'red'),
			array('name' => $eLang->get('YES'), 'value' => 1, 'color' => 'green'),
			array('name' => $eLang->get('YES').' + '.$eLang->get('GZIP_COMPRESSION'), 'value' => 2, 'color' => 'lightgreen')
		);
		$form->addItemStatus('minicss', 'CSS', $elxis->getConfig('MINICSS'), $options);
		$options = array(
			array('name' => $eLang->get('NO'), 'value' => 0, 'color' => 'red'),
			array('name' => $eLang->get('YES'), 'value' => 1, 'color' => 'green'),
			array('name' => $eLang->get('YES').' + '.$eLang->get('GZIP_COMPRESSION'), 'value' => 2, 'color' => 'lightgreen')
		);
		$form->addItemStatus('minijs', 'Javascript', $elxis->getConfig('MINIJS'), $options);
		$form->closeFieldset();
		$form->closeTab();

		$form->openTab();
		$options = array(
			array('name' => $eLang->get('USERNAME'), 'value' => 0, 'color' => 'lightblue'),
			array('name' => $eLang->get('FIRSTNAME').'/'.$eLang->get('LASTNAME'), 'value' => 1, 'color' => 'blue')
		);
		$form->addItemStatus('realname', $eLang->get('DISPUSERS_AS'), $elxis->getConfig('REALNAME'), $options);
		$form->openFieldset($eLang->get('USERS_REGISTRATION'));
		$form->addYesNo('registration', $eLang->get('USERS_REGISTRATION'), $elxis->getConfig('REGISTRATION'));
		$form->addText('registration_email_domain', $elxis->getConfig('REGISTRATION_EMAIL_DOMAIN'), $eLang->get('ALLOWED_DOMAIN'), array('dir' => 'ltr', 'tip' => $eLang->get('ALLOWED_DOMAIN_INFO')));
		$form->addText('registration_exclude_email_domains', $elxis->getConfig('REGISTRATION_EXCLUDE_EMAIL_DOMAINS'), $eLang->get('EXCLUDED_DOMAINS'), array('dir' => 'ltr', 'tip' => $eLang->get('EXCLUDED_DOMAINS_INFO')));
		$options = array();
		$options[] = $form->makeOption(0, $eLang->get('DIRECT'));
		$options[] = $form->makeOption(1, $eLang->get('EMAIL'));
		$options[] = $form->makeOption(2, $eLang->get('MANUAL_BY_ADMIN'));
		$form->addSelect('registration_activation', $eLang->get('ACCOUNT_ACTIVATION'), $elxis->getConfig('REGISTRATION_ACTIVATION'), $options);
		$form->addYesNo('pass_recover', $eLang->get('PASS_RECOVERY'), $elxis->getConfig('PASS_RECOVER'));
		$form->closeFieldset();
		$form->closeTab();

		$form->openTab();
		$options = array();
		$options[] = $form->makeOption('mail', 'PHP mail');
		$options[] = $form->makeOption('smtp', 'SMTP');
		$options[] = $form->makeOption('gmail', 'SMTP - Gmail');
		$options[] = $form->makeOption('sendmail', 'Sendmail');
		$mail_method = $elxis->getConfig('MAIL_METHOD');
		if ($mail_method == 'smtp') {
			if (preg_match('#(\.gmail\.com)$#i', $elxis->getConfig('MAIL_SMTP_HOST'))) {
				$mail_method = 'gmail';
			} else if (preg_match('#(\.gmail\.)$#i', $elxis->getConfig('MAIL_SMTP_HOST'))) {
				$mail_method = 'gmail';
			} else if (preg_match('#(\.google\.)$#i', $elxis->getConfig('MAIL_SMTP_HOST'))) {
				$mail_method = 'gmail';
			}
		}
		$form->addSelect('mail_method', $eLang->get('SEND_METHOD'), $mail_method, $options, array('dir' => 'ltr'));
		$form->addText('mail_name', $elxis->getConfig('MAIL_NAME'), $eLang->get('RCPT_NAME'), array('required' => 'required'));
		$form->addEmail('mail_email', $elxis->getConfig('MAIL_EMAIL'), $eLang->get('RCPT_EMAIL'), array('required' => 'required'));
		$form->addText('mail_from_name', $elxis->getConfig('MAIL_FROM_NAME'), $eLang->get('SENDER_NAME'), array('required' => 'required'));
		$form->addEmail('mail_from_email', $elxis->getConfig('MAIL_FROM_EMAIL'), $eLang->get('SENDER_EMAIL'), array('required' => 'required'));

		$form->openFieldset($eLang->get('TECHNICAL_MANAGER'));
		$form->addNote($eLang->get('TECHNICAL_MANAGER_INFO'), 'elx5_sminfo');
		$form->addText('mail_manager_name', $elxis->getConfig('MAIL_MANAGER_NAME'), $eLang->get('RCPT_NAME'), array('required' => 'required'));
		$form->addEmail('mail_manager_email', $elxis->getConfig('MAIL_MANAGER_EMAIL'), $eLang->get('RCPT_EMAIL'), array('required' => 'required'));
		$form->closeFieldset();

		$form->openFieldset($eLang->get('SMTP_OPTIONS'));
		$form->addInfo('', '<a href="javascript:void(null);" onclick="elx5CPConfigGmail();" class="elxcp_check">'.$eLang->get('CONFIG_FOR_GMAIL').'</a>');
		$form->addText('mail_smtp_host', $elxis->getConfig('MAIL_SMTP_HOST'), $eLang->get('HOST'), array('dir' => 'ltr'));
		$form->addNumber('mail_smtp_port', $elxis->getConfig('MAIL_SMTP_PORT'), $eLang->get('PORT'), array('min' => 0, 'max' => 99999, 'step' => 1, 'maxlength' => 5, 'class' => 'elx5_text elx5_minitext'));
		$options = array();
		$options[] = $form->makeOption('', $eLang->get('NO'));
		$options[] = $form->makeOption('ssl', 'SSL');
		$options[] = $form->makeOption('tls', 'TLS');
		$options[] = $form->makeOption('starttls', 'STARTTLS');
		$form->addSelect('mail_smtp_secure', $eLang->get('SECURE_CON'), $elxis->getConfig('MAIL_SMTP_SECURE'), $options);
		$form->addYesNo('mail_smtp_auth', $eLang->get('AUTH_REQ'), $elxis->getConfig('MAIL_SMTP_AUTH'));
		$options = array();
		$options[] = $form->makeOption('', $eLang->get('DEFAULT'));
		$options[] = $form->makeOption('CRAM-MD5', 'CRAM-MD5');
		$options[] = $form->makeOption('LOGIN', 'LOGIN');
		$options[] = $form->makeOption('NTLM', 'NTLM');
		$options[] = $form->makeOption('PLAIN', 'PLAIN');
		$options[] = $form->makeOption('XOAUTH2', 'XOAUTH2');
		$form->addSelect('mail_auth_method', $eLang->get('AUTH_METHOD'), $elxis->getConfig('MAIL_AUTH_METHOD'), $options);
		$form->addText('mail_smtp_user', $elxis->getConfig('MAIL_SMTP_USER'), $eLang->get('USERNAME'), array('dir' => 'ltr'));
		$form->setOption('tipclass', 'elx5_warntip');
		$tip = ($elxis->getConfig('MAIL_SMTP_PASS') == '') ? '' : $eLang->get('PRIVACY_PROTECTION').': '.$eLang->get('PASSWORD_NOT_SHOWN');
		$form->addPassword('mail_smtp_pass', '', $eLang->get('PASSWORD'), array('dir' => 'ltr', 'tip' => $tip, 'autocomplete' => 'new-password'));
		$form->setOption('tipclass', 'elx5_tip');
		$txt = '<a href="javascript:void(null);" onclick="elx5CPSendTestMail();" class="elxcp_check">'.$eLang->get('SEND_TEST_EMAIL').'</a>';
		$txt .= '<div id="elxcp_mailresponse" class="elx5_invisible"></div>';
		$form->addInfo('', $txt);
		$form->closeFieldset();

		$form->closeTab();

		$form->openTab();
		$options = array();
		if ($data['dbtypes']) {
			foreach ($data['dbtypes'] as $dbtype => $dbtypetxt) {
				$options[] = $form->makeOption($dbtype, $dbtypetxt);
			}
		}
		$form->setOption('tipclass', 'elx5_warntip');
		$form->addSelect('db_type', $eLang->get('DB_TYPE'), $elxis->getConfig('DB_TYPE'), $options, array('dir' => 'ltr', 'tip' => $eLang->get('WARNING').' '.$eLang->get('ALERT_CON_LOST')));
		$form->addText('db_host', $elxis->getConfig('DB_HOST'), $eLang->get('HOST'), array('dir' => 'ltr', 'tip' => $eLang->get('WARNING').' '.$eLang->get('ALERT_CON_LOST')));
		$form->setOption('tipclass', 'elx5_tip');

		$form->addNumber('db_port', $elxis->getConfig('DB_PORT'), $eLang->get('PORT'), array('min' => 0, 'max' => 99999, 'step' => 1, 'maxlength' => 5, 'class' => 'elx5_text elx5_minitext'));
		$form->addYesNo('db_persistent', $eLang->get('PERSISTENT_CON'), $elxis->getConfig('DB_PERSISTENT'));
		$form->addText('db_name', $elxis->getConfig('DB_NAME'), $eLang->get('DB_NAME'), array('dir' => 'ltr'));
		$form->addText('db_prefix', $elxis->getConfig('DB_PREFIX'), $eLang->get('TABLES_PREFIX'), array('required' => 1, 'dir' => 'ltr', 'maxlength' => 10, 'class' => 'elx5_text elx5_minitext'));
		$form->addText('db_user', $elxis->getConfig('DB_USER'), $eLang->get('USERNAME'), array('dir' => 'ltr'));
		$form->setOption('tipclass', 'elx5_warntip');
		$tip = ($elxis->getConfig('DB_PASS') == '') ? '' : $eLang->get('PRIVACY_PROTECTION').': '.$eLang->get('PASSWORD_NOT_SHOWN');
		$form->addPassword('db_pass', '', $eLang->get('PASSWORD'), array('dir' => 'ltr', 'tip' => $tip, 'autocomplete' => 'new-password'));
		$form->setOption('tipclass', 'elx5_tip');
		$form->addText('db_dsn', $elxis->getConfig('DB_DSN'), 'DSN', array('dir' => 'ltr', 'tip' => ''.$eLang->get('DSN_INFO')));
		$form->addText('db_scheme', $elxis->getConfig('DB_SCHEME'), $eLang->get('SCHEME'), array('dir' => 'ltr', 'tip' => $eLang->get('SCHEME_INFO')));
		$form->closeTab();

		$form->openTab();
		$form->addYesNo('ftp', $eLang->get('USE_FTP'), $elxis->getConfig('FTP'));
		$form->addText('ftp_host', $elxis->getConfig('FTP_HOST'), $eLang->get('HOST'), array('dir' => 'ltr'));
		$form->addNumber('ftp_port', $elxis->getConfig('FTP_PORT'), $eLang->get('PORT'), array('min' => 0, 'max' => 99999, 'step' => 1, 'maxlength' => 5, 'class' => 'elx5_text elx5_minitext'));
		$form->addText('ftp_user', $elxis->getConfig('FTP_USER'), $eLang->get('USERNAME'), array('dir' => 'ltr'));
		$form->setOption('tipclass', 'elx5_warntip');
		$tip = ($elxis->getConfig('FTP_PASS') == '') ? '' : $eLang->get('PRIVACY_PROTECTION').': '.$eLang->get('PASSWORD_NOT_SHOWN');
		$form->addPassword('ftp_pass', '', $eLang->get('PASSWORD'), array('dir' => 'ltr', 'tip' => $tip, 'autocomplete' => 'new-password'));
		$form->setOption('tipclass', 'elx5_tip');
		$form->addText('ftp_root', $elxis->getConfig('FTP_ROOT'), $eLang->get('PATH'), array('dir' => 'ltr', 'tip' => $eLang->get('FTP_PATH_INFO')));
		$txt = '<a href="javascript:void(null);" onclick="elx5CPCheckFTP();" class="elxcp_check">'.$eLang->get('CHECK_FTP_SETS').'</a>';
		$txt .= '<div id="elxcp_ftpresponse" class="elx5_invisible"></div>';
		$form->addInfo('', $txt);
		$form->closeTab();

		$form->openTab();
		$options = array();
		$options[] = $form->makeOption('none', $eLang->get('NONE'));
		$options[] = $form->makeOption('files', $eLang->get('FILES'));
		$options[] = $form->makeOption('database', $eLang->get('DATABASE'));
		$form->addSelect('session_handler', $eLang->get('HANDLER'), $elxis->getConfig('SESSION_HANDLER'), $options, array('tip' => $eLang->get('HANDLER_INFO')));
		$options = array();
		$options[] = $form->makeOption(600, '10 '.$eLang->get('MINUTES'));
		$options[] = $form->makeOption(900, '15 '.$eLang->get('MINUTES'));
		$options[] = $form->makeOption(1200, '20 '.$eLang->get('MINUTES'));
		$options[] = $form->makeOption(1800, '30 '.$eLang->get('MINUTES'));
		$options[] = $form->makeOption(2700, '45 '.$eLang->get('MINUTES'));
		$options[] = $form->makeOption(3600, '60 '.$eLang->get('MINUTES'));
		$form->addSelect('session_lifetime', $eLang->get('LIFETIME'), $elxis->getConfig('SESSION_LIFETIME'), $options, array('tip' => $eLang->get('SESS_LIFETIME_INFO')));
		$form->addYesNo('session_matchip', $eLang->get('MATCH_IP'), $elxis->getConfig('SESSION_MATCHIP'), array('tip' => 'IP|'.$eLang->get('MATCH_SESS_INFO')));
		$form->addYesNo('session_matchbrowser', $eLang->get('MATCH_BROWSER'), $elxis->getConfig('SESSION_MATCHBROWSER'), array('tip' => $eLang->get('MATCH_SESS_INFO')));
		$form->addYesNo('session_matchreferer', $eLang->get('MATCH_REFERER'), $elxis->getConfig('SESSION_MATCHREFERER'), array('tip' => $eLang->get('MATCH_SESS_INFO')));
		$form->addYesNo('session_encrypt', $eLang->get('ENCRYPTION'), $elxis->getConfig('SESSION_ENCRYPT'), array('tip' => $eLang->get('ENCRYPT_SESS_INFO')));
		$form->closeTab();

		$form->openTab();//security
		$form->setOption('tipclass', 'elx5_warntip');
		$options = array(
			array('name' => $eLang->get('NORMAL'), 'value' => 0, 'color' => 'green'),
			array('name' => $eLang->get('HIGH'), 'value' => 1, 'color' => 'orange'),
			array('name' => $eLang->get('INSANE'), 'value' => 2, 'color' => 'red')
		);
		$form->addItemStatus('security_level', $eLang->get('SECURITY_LEVEL'), $elxis->getConfig('SECURITY_LEVEL'), $options, array('tip' => $eLang->get('SECURITY_LEVEL_INFO')));

		if (stripos($elxis->getConfig('URL'), 'https:') !== false) {
			$colors = array('green', 'gray', 'gray');
		} else {
			$colors = array('gray', 'yellow', 'green');
		}
		$options = array(
			array('name' => $eLang->get('OFF'), 'value' => 0, 'color' => $colors[0]),
			array('name' => $eLang->get('ADMINISTRATION'), 'value' => 1, 'color' => $colors[1]),
			array('name' => '+ '.$eLang->get('PUBLIC_AREA'), 'value' => 2, 'color' => $colors[2])
		);
		$form->addItemStatus('ssl', $eLang->get('SSL_SWITCH'), $elxis->getConfig('SSL'), $options, array('tip' => $eLang->get('SSL_SWITCH_INFO')));

		$form->setOption('tipclass', 'elx5_tip');
		$options = array();
		$options[] = $form->makeOption('auto', $eLang->get('AUTOMATIC'));
		$options[] = $form->makeOption('openssl', 'OpenSSL');
		$options[] = $form->makeOption('mcrypt', 'Mcrypt');
		$options[] = $form->makeOption('xor', 'XOR');
		$form->addSelect('encrypt_method', $eLang->get('ENCRYPT_METHOD'), $elxis->getConfig('ENCRYPT_METHOD'), $options, array('disabled' => 'disabled', 'tip' => $eLang->get('CAN_NOT_CHANGE')));

		$val = ($elxis->getConfig('CAPTCHA') == '') ? 'NOROBOT' : $elxis->getConfig('CAPTCHA');//Elxis 4.4 compatibility
		$options = array();
		$options[] = $form->makeOption('NONE', $eLang->get('NO'));
		$options[] = $form->makeOption('MATH',  $eLang->get('SECURITY_CODE').' (X+Y=?)');
		$options[] = $form->makeOption('NOROBOT', $eLang->get('IAMNOTA_ROBOT'));
		$form->addSelect('captcha', 'Captcha', $val, $options);

		$form->openFieldset($eLang->get('ELXIS_DEFENDER'));
		$form->addNote($eLang->get('ELXIS_DEFENDER_INFO'), 'elx5_sminfo');
		$options = array();
		$options[] = $form->makeOption('G', 'G: '.$eLang->get('GENERAL_FILTERS'));
		$options[] = $form->makeOption('R', 'R: '.$eLang->get('IP_RANGES'));
		$options[] = $form->makeOption('I', 'I: IP');
		$options[] = $form->makeOption('C', 'C: '.$eLang->get('CUSTOM_FILTERS'));
		$options[] = $form->makeOption('F', 'F: '.$eLang->get('FSYS_PROTECTION'));
		$vals = ($elxis->getConfig('DEFENDER') == '') ? array() : str_split($elxis->getConfig('DEFENDER'));
		$form->addMultiSelect('defender', $eLang->get('ELXIS_DEFENDER'), $vals, $options, array('noselected_text' => '- '.$eLang->get('NO_PROTECTION').' -'));

		$afterinit = $elxis->getConfig('DEFENDER_IPAFTER');
		if ($afterinit === '') { $afterinit = 1; }//Elxis 4.3 compatibility
		$options = array();
		$options[] = $form->makeOption('0', $eLang->get('BEFORE_LOAD_ELXIS'));
		$options[] = $form->makeOption('1', $eLang->get('AFTER_LOAD_ELXIS'));
		$form->addSelect('defender_ipafter', $eLang->get('CHECK_IP_MOMENT'), $afterinit, $options, array('tip' => $eLang->get('CHECK_IP_MOMENT_HELP')));
		$defnotif = $elxis->getConfig('DEFENDER_NOTIFY');
		if ($defnotif === '') { $defnotif = 1; }//Elxis 4.3 r1713- compatibility	
		$options = array();
		$options[] = $form->makeOption('0', $eLang->get('NO'));
		$options[] = $form->makeOption('1', $eLang->get('YES') .', '.$eLang->get('ONLY_ATTACKS'));
		$options[] = $form->makeOption('2', $eLang->get('YES') .', '.$eLang->get('EVERYTHING'));
		$form->addSelect('defender_notify', $eLang->get('DEFENDER_NOTIFS'), $defnotif, $options, array('tip' => 'Enable email noifications from Defender? Notifications will be sent to the site technical manager.'));
		$deflog = $elxis->getConfig('DEFENDER_LOG');
		if ($deflog === '') { $deflog = 1; }	
		$form->addSelect('defender_log', $eLang->get('LOG'), $deflog, $options, array('tip' => 'Log defender alerts in security.log file? Everything includes: Attacks, Bad bots, bad hosts and blacklisted IPs'));
		unset($afterinit, $defnotif, $deflog);

		$form->addText('defender_whitelist', $elxis->getConfig('DEFENDER_WHITELIST'), $eLang->get('EXCLUDED_IPS'), array('dir' => 'ltr', 'tip' => 'Comma separated IPs to exclude from Defender check. Example: 1.1.1.1,2.2.2.2,::1'));
		$form->closeFieldset();

		$form->openFieldset('X-Frame-Options');
		$form->addNote($eLang->get('XFRAMEOPT_HELP'), 'elx5_sminfo');
		$val = $elxis->getConfig('XFOPTIONS');
		$xfoptorig = '';
		if (strpos($elxis->getConfig('XFOPTIONS'), 'ALLOW-FROM') !== false) {
			$val = 'ALLOW-FROM';
			$xfoptorig = trim(str_replace('ALLOW-FROM', '', $elxis->getConfig('XFOPTIONS')));
		}
		$options = array();
		$options[] = $form->makeOption('', $eLang->get('YES'));
		$options[] = $form->makeOption('DENY', $eLang->get('DENY'));
		$options[] = $form->makeOption('SAMEORIGIN', $eLang->get('SAMEORIGIN'));
		$options[] = $form->makeOption('ALLOW-FROM', $eLang->get('ALLOW_FROM'));
		$form->addSelect('xfoptions', $eLang->get('ACCEPT_XFRAME'), $val, $options, array('dir' => 'ltr'));
		$form->addText('xfoptionsfrom', $xfoptorig, $eLang->get('ALLOW_FROM_ORIGIN'), array('dir' => 'ltr', 'tip' => 'Origin (domain) allowed to include pages from this site within an iframe if you select X-Frame-Options = ALLOW-FROM'));
		$form->closeFieldset();

		$form->setOption('tipclass', 'elx5_warntip');
		$form->addText('csp', $elxis->getConfig('CSP'), $eLang->get('CONTENT_SEC_POLICY').' CSP', array('dir' => 'ltr', 'tip' => 'Be careful with this option! For help visit http://content-security-policy.com'));
		$form->setOption('tipclass', 'elx5_tip');
		$form->closeTab();

		$form->openTab();//errors
		$options = array(
			array('name' => $eLang->get('OFF'), 'value' => 0, 'color' => 'green'),
			array('name' => $eLang->get('MODULE_POS'), 'value' => 1, 'color' => 'red'),
			array('name' => $eLang->get('MINIMAL'), 'value' => 2, 'color' => 'red'),
			array('name' => $eLang->get('MINIMAL').' + '.$eLang->get('MODULE_POS'), 'value' => 3, 'color' => 'red'),
			array('name' => $eLang->get('FULL'), 'value' => 4, 'color' => 'red'),
			array('name' => $eLang->get('FULL').' + '.$eLang->get('MODULE_POS'), 'value' => 5, 'color' => 'red')
		);
		$form->addItemStatus('debug', $eLang->get('DEBUG'), $elxis->getConfig('DEBUG'), $options);
		$options = array(
			array('name' => $eLang->get('OFF'), 'value' => 0, 'color' => 'green'),
			array('name' => $eLang->get('ERRORS'), 'value' => 1, 'color' => 'orange'),
			array('name' => '+ '.$eLang->get('WARNINGS'), 'value' => 2, 'color' => 'red'),
			array('name' => '++ '.$eLang->get('NOTICES'), 'value' => 3, 'color' => 'red')
		);
		$form->addItemStatus('error_report', $eLang->get('REPORT'), $elxis->getConfig('ERROR_REPORT'), $options, array('tip' => $eLang->get('REPORT_INFO')));
		$options = array(
			array('name' => $eLang->get('OFF'), 'value' => 0, 'color' => 'red'),
			array('name' => $eLang->get('ERRORS'), 'value' => 1, 'color' => 'green'),
			array('name' => '+ '.$eLang->get('WARNINGS'), 'value' => 2, 'color' => 'green'),
			array('name' => '++ '.$eLang->get('NOTICES'), 'value' => 3, 'color' => 'green')
		);
		$form->addItemStatus('error_log', $eLang->get('LOG'), $elxis->getConfig('ERROR_LOG'), $options, array('tip' => $eLang->get('LOG_INFO')));
		$form->addYesNo('error_alert', $eLang->get('ALERT'), $elxis->getConfig('ERROR_ALERT'), array('tip' => $eLang->get('ALERT_INFO')));
		$form->addYesNo('log_rotate', $eLang->get('ROTATE'), $elxis->getConfig('LOG_ROTATE'), array('tip' => $eLang->get('ROTATE_INFO')));
		$form->closeTab();

		$form->endTabs();
		$form->addToken('fmconfig');
		$form->addHidden('task', '');

		$form->closeForm();
	}

}

?>