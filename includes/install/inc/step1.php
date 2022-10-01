<?php 
/**
* @version		$Id$
* @package		Elxis
* @subpackage	Installer
* @copyright	Copyright (c) 2006-2019 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');
defined('ELXIS_INSTALLER') or die ('Direct access to this location is not allowed');
?>
<div class="ielx_block" id="ielblock1">
	<h2><?php echo $this->getLang('BEFORE_BEGIN'); ?></h2>
	<p><?php echo $this->getLang('DATABASE_DESC'); ?></p>
	<h3><?php echo $this->getLang('LICENSE'); ?></h3>
	<p><?php echo $this->getLang('LICENSE_NOTES'); ?></p>

<?php 
if (file_exists(ELXIS_PATH.'/includes/install/inc/license.txt')) {
	echo '<textarea rows="10" cols="80" class="ielx_license" dir="ltr">'."\n";
	include(ELXIS_PATH.'/includes/install/inc/license.txt');
	echo "</textarea>\n";
} else if (file_exists(ELXIS_PATH.'/license.txt')) {
	echo '<textarea rows="10" cols="80" class="ielx_license" dir="ltr">'."\n";
	include(ELXIS_PATH.'/license.txt');
	echo "</textarea>\n";
} else {
	echo '<textarea rows="10" cols="80" class="ielx_license" dir="ltr">Elxis Public License (EPL)</textarea>'."\n";
}
?>
	<div class="ielx_chkwrap">
		<div>
			<label class="ielx_chklabel"><?php echo $this->getLang('I_AGREE_TERMS'); ?>
				<input type="checkbox" name="licagree" id="ielxlicagree" value="1" onclick="ielxAgreeTerms();" />
				<span class="ielx_chkmark"></span>
			</label>
		</div>
	</div>
	<div class="ielx_vspace">
		<a href="javascript:void(null);" class="ielx_nocontinue" onclick="ielxSwitchBlock(2, 1, 0, 0, 1);" id="ielxcontbtn1"><span><?php echo $this->getLang('CONTINUE'); ?> </span></a>
	</div>
</div>

<div class="ielx_blockinv" id="ielblock2">
	<h2><?php echo $this->getLang('SETTINGS'); ?></h2>
	<p><?php echo $this->getLang('SETTINGS_DESC'); ?></p>

<?php 
	$errormsg = $this->dataValue('cfg', 'errormsg', '');
	if ($errormsg != '') {
		echo '<div class="ielx_error">'.$errormsg."</div><br />\n";
	}
?>

	<form name="fmconfig" class="ielx_form" action="<?php echo $this->url; ?>/index.php" method="post" onsubmit="return ielxValidateConfig();" autocomplete="off">
		<fieldset class="ielx_fieldset">
			<legend><?php echo $this->getLang('GENERAL'); ?></legend>
			<div class="ielx_formrow">
				<label class="ielx_label" for="cfg_sitename"><?php echo $this->getLang('SITENAME'); ?></label>
				<div class="ielx_labelside">
					<input type="text" name="cfg_sitename" id="cfg_sitename" placeholder="<?php echo $this->getLang('SITENAME'); ?>" value="<?php echo $this->dataValue('cfg', 'cfg_sitename', 'Elxis '.$this->verInfo('CODENAME')); ?>" class="ielx_text" required="required" />
				</div>
			</div>
			<div class="ielx_formrow">
				<label class="ielx_label" for="cfg_lang"><?php echo $this->getLang('DEF_LANG'); ?></label>
				<div class="ielx_labelside">
					<select name="cfg_lang" id="cfg_lang" dir="ltr" class="ielx_select">
<?php 
					$elangs = $this->elxisLanguages();
					$sellang = $this->currentLang();
					if (!isset($elangs[$sellang])) { $sellang = 'en'; }
					if ($elangs) {
						foreach ($elangs as $elng => $elnginfo) {
							$sel = ($elng == $sellang) ? ' selected="selected"' : '';
							echo '<option value="'.$elng.'"'.$sel.'>'.$elnginfo['NAME'].' / '.$elnginfo['NAME_ENG']."</option>\n";
						}
					}
					unset($sellang);
?>
					</select>
					<div class="ielx_tip"><?php echo $this->getLang('DEFLANG_DESC'); ?></div>
				</div>
			</div>
			<div class="ielx_formrow">
				<label class="ielx_label" for="cfg_sitelangs"><?php echo $this->getLang('OTHER_LANGS'); ?></label>
				<div class="ielx_labelside">
					<div class="ielx_msel_items" id="cfg_sitelangs_items">
						<div class="ielx_msel_itemall" id="cfg_sitelangs_all"><?php echo $this->getLang('ALL_LANGS'); ?></div>
					</div>
					<select name="sitelangs_selector" id="cfg_sitelangs_selector" class="ielx_select" onchange="ielxAddLang();" data-lngremove="<?php echo $this->getLang('REMOVE'); ?>" data-lngalllangs="<?php echo $this->getLang('ALL_LANGS'); ?>" data-lngnonelangs="<?php echo $this->getLang('NONE_LANGS'); ?>">
						<option value="" selected="selected">- <?php echo $this->getLang('ALL_LANGS'); ?> -</option>
						<option value="none">- <?php echo $this->getLang('NONE_LANGS'); ?> -</option>
<?php 
					if ($elangs) {
						foreach ($elangs as $elng => $elnginfo) {
							echo '<option value="'.$elng.'">'.strtoupper($elng).' - '.$elnginfo['NAME'].' / '.$elnginfo['NAME_ENG']."</option>\n";
						}
					}
?>
					</select>
					<input type="hidden" name="cfg_sitelangs" id="cfg_sitelangs" dir="ltr" value="" />
					<div class="ielx_tip"><?php echo $this->getLang('OTHER_LANGS_DESC'); ?></div>
				</div>
			</div>
			<div class="ielx_formrow">
				<label class="ielx_label" for="cfg_sef"><?php echo $this->getLang('FRIENDLY_URLS'); ?></label>
				<div class="ielx_labelside">
					<select name="cfg_sef" id="cfg_sef" dir="ltr" class="ielx_select">
						<option value="0" selected="selected"><?php echo $this->getLang('NO'); ?></option>
						<option value="1"><?php echo $this->getLang('YES'); ?> - Apache htaccess</option>
						<option value="2"><?php echo $this->getLang('YES'); ?> - IIS web.config</option>
					</select>
					<div class="ielx_tip"><?php echo $this->getLang('FRIENDLY_URLS_DESC'); ?></div>
				</div>
			</div>
<?php 
		if (function_exists('mcrypt_encrypt')) {//php 5.x, php 7.0
			$encmethod = 'mcrypt';
		} else if (function_exists('openssl_encrypt')) {//php 5.3.3+, php 7.1+
			$encmethod = 'openssl';
		} else {
			$encmethod = 'xor';
		}
		$chars = array_merge(range('a', 'z'), range('A', 'Z'), range(0, 9));
		shuffle($chars);
		$enckey = '';
		for ($i=0; $i<16; $i++) { $enckey .= $chars[$i]; }
?>
			<input type="hidden" name="cfg_encrypt_method" value="<?php echo $encmethod; ?>" />
			<input type="hidden" name="cfg_encrypt_key" value="<?php echo $this->dataValue('cfg', 'cfg_encrypt_key', $enckey); ?>" />
			<input type="hidden" name="cfg_url" value="<?php echo $this->dataValue('cfg', 'cfg_url', $this->url); ?>" />
			<input type="hidden" name="cfg_repo_path" value="<?php echo $this->dataValue('cfg', 'cfg_repo_path', ''); ?>" />
		</fieldset>

		<fieldset class="ielx_fieldset">
			<legend><?php echo $this->getLang('DATABASE'); ?></legend>
			<div class="ielx_formrow">
				<label class="ielx_label" for="cfg_db_type"><?php echo $this->getLang('TYPE'); ?></label>
				<div class="ielx_labelside">
					<select name="cfg_db_type" id="cfg_db_type" dir="ltr" class="ielx_select">
<?php 
				$pdodrivers = PDO::getAvailableDrivers();
				if (!$pdodrivers) { $pdodrivers = array(); }

				$dbtypes = array(
					'4D' => '4D',
					'cubrid' => 'Cubrid',
					'dblib' => 'dbLib',
					'firebird' => 'Firebird',
					'freetds' => 'FreeTDS',
					'ibm' => 'IBM',
					'informix' => 'Informix',
					'mssql' => 'msSQL',
					'mysql' => 'MySQL',
					'oci' => 'OCI (Oracle)',
					'odbc' => 'ODBC',
					'odbc_db2' => 'ODBC db2',
					'odbc_access' => 'ODBC MS Access',
					'odbc_mssql' => 'ODBC msSQL',
					'pgsql' => 'PostgreSQL',
					'sqlite' => 'SQLite 3',
					'sqlite2' => 'SQLite 2',
					'sybase' => 'SyBase'
				);
				$found = false;
				foreach ($dbtypes as $dbtype => $dbtext) {
					if (file_exists(ELXIS_PATH.'/includes/install/data/'.$dbtype.'.sql')) {
						$supported = (in_array($dbtype, $pdodrivers)) ? true : false;
					} else {
						$supported = false;
					}

					if ($dbtype == 'mysql') {
						if ($supported) {
							$found = true;
							$extra = ' selected="selected"';
						} else {
							$extra = ' disabled="disabled"';
						}
					} else {
						if ($supported) {
							$found = true;
							$extra = '';
						} else {
							$extra = ' disabled="disabled"';
						}
					}
					echo '<option value="'.$dbtype.'"'.$extra.'>'.$dbtext."</option>\n";
				}
				if (!$found) {
					echo '<option value="" selected="selected">No PDO driver is available!'."</option>\n";
				}
				unset($dbtypes);
?>
					</select>
					<div class="ielx_tip"><?php echo $this->getLang('DBTYPE_DESC'); ?></div>
				</div>
			</div>
			<div class="ielx_formrow">
				<label class="ielx_label" for="cfg_db_host"><?php echo $this->getLang('HOST'); ?></label>
				<div class="ielx_labelside">
					<input type="text" name="cfg_db_host" id="cfg_db_host" dir="ltr" placeholder="<?php echo $this->getLang('HOST'); ?>" value="<?php echo $this->dataValue('cfg', 'cfg_db_host', 'localhost'); ?>" class="ielx_text" required="required" />
				</div>
			</div>
			<div class="ielx_formrow">
				<label class="ielx_label" for="cfg_db_port"><?php echo $this->getLang('PORT'); ?></label>
				<div class="ielx_labelside">
					<input type="text" name="cfg_db_port" id="cfg_db_port" dir="ltr" placeholder="<?php echo $this->getLang('PORT'); ?>" value="<?php echo $this->dataValue('cfg', 'cfg_db_port', '0'); ?>" class="ielx_text" required="required" />
					<div class="ielx_tip"><?php echo $this->getLang('PORT_DESC'); ?></div>
				</div>
			</div>
			<div class="ielx_formrow">
				<label class="ielx_label" for="cfg_db_name"><?php echo $this->getLang('NAME'); ?></label>
				<div class="ielx_labelside">
					<input type="text" name="cfg_db_name" id="cfg_db_name" dir="ltr" placeholder="<?php echo $this->getLang('NAME'); ?>" value="<?php echo $this->dataValue('cfg', 'cfg_db_name', ''); ?>" class="ielx_text" required="required" autocomplete="off" />
				</div>
			</div>
			<div class="ielx_formrow">
				<label class="ielx_label" for="cfg_db_prefix"><?php echo $this->getLang('TABLES_PREFIX'); ?></label>
				<div class="ielx_labelside">
					<input type="text" name="cfg_db_prefix" id="cfg_db_prefix" dir="ltr" placeholder="<?php echo $this->getLang('TABLES_PREFIX'); ?>" value="<?php echo $this->dataValue('cfg', 'cfg_db_prefix', 'elx_'); ?>" class="ielx_text" required="required" maxlength="10" />
				</div>
			</div>
			<div class="ielx_formrow">
				<label class="ielx_label" for="cfg_db_user"><?php echo $this->getLang('USERNAME'); ?></label>
				<div class="ielx_labelside">
					<input type="text" name="cfg_db_user" id="cfg_db_user" dir="ltr" placeholder="<?php echo $this->getLang('USERNAME'); ?>" value="<?php echo $this->dataValue('cfg', 'cfg_db_user', ''); ?>" class="ielx_text" required="required" autocomplete="off" />
				</div>
			</div>
			<div class="ielx_formrow">
				<label class="ielx_label" for="cfg_db_pass"><?php echo $this->getLang('PASSWORD'); ?></label>
				<div class="ielx_labelside">
					<input type="password" name="cfg_db_pass" id="cfg_db_pass" dir="ltr" placeholder="<?php echo $this->getLang('PASSWORD'); ?>" value="<?php echo $this->dataValue('cfg', 'cfg_db_pass', ''); ?>" class="ielx_text" required="required" autocomplete="new-password" />
				</div>
			</div>
			<div class="ielx_formrow">
				<label class="ielx_label"></label>
				<div class="ielx_labelside"><a href="javascript:void(null);" onclick="ielxToggle('ielxadvdb');" class="ielx_toggle"><i class="fa fa-angle-down" aria-hidden="false"></i> <?php echo $this->getLang('ADVANCED_SETTINGS'); ?></a></div>
			</div>

			<div class="ielx_invisible" id="ielxadvdb">
				<div class="ielx_formrow">
					<label class="ielx_label" for="cfg_db_dsn">DSN</label>
					<div class="ielx_labelside">
						<input type="text" name="cfg_db_dsn" id="cfg_db_dsn" dir="ltr" placeholder="DSN" value="<?php echo $this->dataValue('cfg', 'cfg_db_dsn', ''); ?>" class="ielx_text" autocomplete="off" />
						<div class="ielx_tip"><?php echo $this->getLang('DSN_DESC'); ?></div>
					</div>
				</div>
				<div class="ielx_formrow">
					<label class="ielx_label" for="cfg_db_scheme"><?php echo $this->getLang('SCHEME'); ?></label>
					<div class="ielx_labelside">
						<input type="text" name="cfg_db_scheme" id="cfg_db_scheme" dir="ltr" placeholder="<?php echo $this->getLang('SCHEME'); ?>" value="<?php echo $this->dataValue('cfg', 'cfg_db_scheme', ''); ?>" class="ielx_text" autocomplete="off" />
						<div class="ielx_tip"><?php echo $this->getLang('SCHEME_DESC'); ?></div>
					</div>
				</div>
			</div>

			<div class="ielx_formrow">
				<label class="ielx_label"></label>
				<div class="ielx_labelside">
					<a href="javascript:void(null);" onclick="ielxCheckDB();" class="ielx_toggle"><i class="fa fa-database" aria-hidden="false"></i> <?php echo $this->getLang('CHECK_DB_SETS'); ?></a>
					<div id="dbresponse" class="ielx_invisible"></div>
				</div>
			</div>
		</fieldset>

		<fieldset class="ielx_fieldset">
			<legend>FTP</legend>
<?php 
			$parts = parse_url($this->url);
			$host = $parts['host'];
			$ftppath = isset($parts['path']) ? rtrim($parts['path'], '/') : '';
			if ($ftppath == '') { $ftppath = '/'; }

			if ($host == 'localhost') {
				$hoststr = $host;
				$mailhoststr = $host;
			} else if (preg_match('@(\.loc)$@', $host)) {
				$hoststr = 'localhost';
				$mailhoststr = 'localhost';
			} else if (preg_match('/^[0-9\.]+$/', $host)) {
				$hoststr = $host;
				$mailhoststr = $host;
			} else if (substr_count($host, '.') > 1) {
				$hoststr = $host;
				$mailhoststr = $host;
			} else {
				$hoststr = 'ftp.'.$host;
				$mailhoststr = 'mail.'.$host;
			}
			unset($parts, $host);
?>

			<div class="ielx_formrow">
				<label class="ielx_label" for="cfg_ftp"><?php echo $this->getLang('USE_FTP'); ?></label>
				<div class="ielx_labelside">
					<label class="ielx_switch"><input type="checkbox" name="cfg_ftp" id="cfg_ftp" class="ielx_switchinput" value="1" onclick="ielxToggleFTP();" />
					<span class="ielx_switchlabel" data-on="<?php echo $this->getLang('YES'); ?>" data-off="<?php echo $this->getLang('NO'); ?>"></span>
					<span class="ielx_switchhandle"></span>
					</label>
				</div>
			</div>
			<div id="ftp_details" class="ielx_invisible">
				<div class="ielx_formrow">
					<label class="ielx_label" for="cfg_ftp_host"><?php echo $this->getLang('HOST'); ?></label>
					<div class="ielx_labelside">
						<input type="text" name="cfg_ftp_host" id="cfg_ftp_host" dir="ltr" placeholder="<?php echo $this->getLang('HOST'); ?>" value="<?php echo $this->dataValue('cfg', 'cfg_ftp_host', $hoststr); ?>" class="ielx_text" />
					</div>
				</div>
				<div class="ielx_formrow">
					<label class="ielx_label" for="cfg_ftp_port"><?php echo $this->getLang('PORT'); ?></label>
					<div class="ielx_labelside">
						<input type="number" name="cfg_ftp_port" id="cfg_ftp_port" dir="ltr" placeholder="<?php echo $this->getLang('PORT'); ?>" value="<?php echo $this->dataValue('cfg', 'cfg_ftp_port', '0'); ?>" class="ielx_text" maxlength="6" />
						<div class="ielx_tip"><?php echo $this->getLang('FTPPORT_DESC'); ?></div>
					</div>
				</div>
				<div class="ielx_formrow">
					<label class="ielx_label" for="cfg_ftp_root"><?php echo $this->getLang('PATH'); ?></label>
					<div class="ielx_labelside">
						<input type="text" name="cfg_ftp_root" id="cfg_ftp_root" dir="ltr" placeholder="<?php echo $this->getLang('PATH'); ?>" value="<?php echo $this->dataValue('cfg', 'cfg_ftp_root', $ftppath); ?>" class="ielx_text" />
						<div class="ielx_tip"><?php echo $this->getLang('FTP_PATH_INFO'); ?></div>
					</div>
				</div>
				<div class="ielx_formrow">
					<label class="ielx_label" for="cfg_ftp_user"><?php echo $this->getLang('USERNAME'); ?></label>
					<div class="ielx_labelside">
						<input type="text" name="cfg_ftp_user" id="cfg_ftp_user" dir="ltr" placeholder="<?php echo $this->getLang('USERNAME'); ?>" value="<?php echo $this->dataValue('cfg', 'cfg_ftp_user', ''); ?>" class="ielx_text" autocomplete="off" />
					</div>
				</div>
				<div class="ielx_formrow">
					<label class="ielx_label" for="cfg_ftp_pass"><?php echo $this->getLang('PASSWORD'); ?></label>
					<div class="ielx_labelside">
						<input type="password" name="cfg_ftp_pass" id="cfg_ftp_pass" dir="ltr" placeholder="<?php echo $this->getLang('PASSWORD'); ?>" value="<?php echo $this->dataValue('cfg', 'cfg_ftp_pass', ''); ?>" class="ielx_text" autocomplete="new-password" />
					</div>
				</div>
				<div class="ielx_formrow">
					<label class="ielx_label"></label>
					<div class="ielx_labelside">
						<a href="javascript:void(null);" onclick="ielxCheckFTP();" class="ielx_toggle"><i class="fa fa-files-o" aria-hidden="true"></i> <?php echo $this->getLang('CHECK_FTP_SETS'); ?></a>
						<div id="ftpresponse" class="ielx_invisible"></div>
					</div>
				</div>
			</div>
		</fieldset>

		<fieldset class="ielx_fieldset">
			<legend>E-mail</legend>
			<div class="ielx_formrow">
				<label class="ielx_label"></label>
				<div class="ielx_labelside"><a href="javascript:void(null);" onclick="ielxToggle('ielxcfgemail');" class="ielx_toggle"><i class="fa fa-angle-down" aria-hidden="false"></i> <?php echo $this->getLang('CONFIG_EMAIL_DISPATCH'); ?></a></div>
			</div>
			<div class="ielx_invisible" id="ielxcfgemail">
				<div class="ielx_formrow">
					<label class="ielx_label" for="cfg_mail_method"><?php echo $this->getLang('SEND_METHOD'); ?></label>
					<div class="ielx_labelside">
						<select name="cfg_mail_method" id="cfg_mail_method" dir="ltr" class="ielx_select">
							<option value="mail" selected="selected">PHP mail</option>
							<option value="smtp">SMTP (<?php echo $this->getLang('RECOMMENDED'); ?>)</option>
							<option value="sendmail">Sendmail</option>
						</select>
					</div>
				</div>
				<div class="ielx_formrow">
					<label class="ielx_label" for="cfg_smtp_host"><?php echo $this->getLang('HOST'); ?> (SMTP)</label>
					<div class="ielx_labelside">
						<input type="text" name="cfg_smtp_host" id="cfg_smtp_host" dir="ltr" placeholder="<?php echo $this->getLang('HOST'); ?> (SMTP)" value="<?php echo $this->dataValue('cfg', 'cfg_smtp_host', $mailhoststr); ?>" class="ielx_text" />
					</div>
				</div>
				<div class="ielx_formrow">
					<label class="ielx_label" for="cfg_smtp_port"><?php echo $this->getLang('PORT'); ?> (SMTP)</label>
					<div class="ielx_labelside">
						<input type="number" name="cfg_smtp_port" id="cfg_smtp_port" dir="ltr" placeholder="<?php echo $this->getLang('PORT'); ?>" value="<?php echo $this->dataValue('cfg', 'cfg_smtp_port', '25'); ?>" class="ielx_text" maxlength="6" />
					</div>
				</div>
				<div class="ielx_formrow">
					<label class="ielx_label" for="cfg_smtp_secure"><?php echo $this->getLang('SECURE_CONNECTION'); ?></label>
					<div class="ielx_labelside">
						<select name="cfg_smtp_secure" id="cfg_smtp_secure" dir="ltr" class="ielx_select">
							<option value="" selected="selected"><?php echo $this->getLang('NO'); ?></option>
							<option value="ssl">SSL</option>
							<option value="tls">TLS</option>
							<option value="starttls">STARTTLS</option>
						</select>
					</div>
				</div>
				<div class="ielx_formrow">
					<label class="ielx_label" for="cfg_smtp_auth"><?php echo $this->getLang('AUTH_REQUIRED'); ?></label>
					<div class="ielx_labelside">
						<label class="ielx_switch"><input type="checkbox" name="cfg_smtp_auth" id="cfg_smtp_auth" class="ielx_switchinput" value="1" />
						<span class="ielx_switchlabel" data-on="<?php echo $this->getLang('YES'); ?>" data-off="<?php echo $this->getLang('NO'); ?>"></span>
						<span class="ielx_switchhandle"></span>
						</label>
					</div>
				</div>
				<div class="ielx_formrow">
					<label class="ielx_label" for="cfg_mail_auth_method"><?php echo $this->getLang('AUTH_METHOD'); ?></label>
					<div class="ielx_labelside">
						<select name="cfg_mail_auth_method" id="cfg_mail_auth_method" dir="ltr" class="ielx_select">
							<option value="" selected="selected"><?php echo $this->getLang('DEFAULT_METHOD'); ?></option>
							<option value="CRAM-MD5">CRAM-MD5</option>
							<option value="LOGIN">LOGIN</option>
							<option value="NTLM">NTLM</option>
							<option value="PLAIN">PLAIN</option>
							<option value="XOAUTH2">XOAUTH2</option>
						</select>
					</div>
				</div>
				<div class="ielx_formrow">
					<label class="ielx_label" for="cfg_smtp_user"><?php echo $this->getLang('USERNAME'); ?></label>
					<div class="ielx_labelside">
						<input type="text" name="cfg_smtp_user" id="cfg_smtp_user" dir="ltr" placeholder="<?php echo $this->getLang('USERNAME'); ?> (SMTP)" value="<?php echo $this->dataValue('cfg', 'cfg_smtp_user', ''); ?>" class="ielx_text" autocomplete="off" />
					</div>
				</div>
				<div class="ielx_formrow">
					<label class="ielx_label" for="cfg_smtp_pass"><?php echo $this->getLang('PASSWORD'); ?></label>
					<div class="ielx_labelside">
						<input type="password" name="cfg_smtp_pass" id="cfg_smtp_pass" dir="ltr" placeholder="<?php echo $this->getLang('PASSWORD'); ?> (SMTP)" value="<?php echo $this->dataValue('cfg', 'cfg_smtp_pass', ''); ?>" class="ielx_text" autocomplete="new-password" />
					</div>
				</div>
			</div>
		</fieldset>

		<input type="hidden" name="step" value="2" />
		<input type="hidden" name="lang" value="<?php echo $this->currentLang(); ?>" />

		<div class="ielx_formrow">
			<label class="ielx_label" for="cfgsubmit"></label>
			<div class="ielx_labelside">
				<button type="submit" name="cfgsubmit" id="cfgsubmit" value="1" class="ielx_btn"><span><?php echo $this->getLang('SUBMIT'); ?> </span></button>
			</div>
		</div>
	</form>
	<div id="ei_baseurl" class="ielx_invisible" dir="ltr"><?php echo $this->url; ?></div>
</div>