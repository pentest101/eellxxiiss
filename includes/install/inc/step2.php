<?php 
/**
* @version		$Id: step2.php 2252 2019-04-23 18:19:52Z IOS $
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
<h2><?php echo $this->getLang('ADMIN_ACCOUNT'); ?></h2>

<?php 
if ($this->dataValue('cfg', 'errormsg', '') != '') {
	echo '<div class="ielx_error"><strong>'.$this->getLang('SETTINGS_ERRORS')."</strong><br />\n".$this->dataValue('cfg', 'errormsg', '')."</div>\n";
	echo '<div class="ielx_vspace">'."\n";
	echo '<a href="'.$this->url.'/?step=1&amp;lang='.$this->currentLang().'" class="ielx_back" title="'.$this->getLang('STEP').' 1"><span>'.$this->getLang('BACK')." </span></a>\n";
	echo '</div>'."\n";
	return;
}
if ($this->dataValue('import_error', '', '') != '') {
	echo '<div class="ielx_error"><strong>'.$this->getLang('ERROR')."</strong><br />\n".$this->dataValue('import_error', '', '')."</div>\n";
	echo '<div class="ielx_vspace">'."\n";
	echo '<a href="'.$this->url.'/?step=1&amp;lang='.$this->currentLang().'" class="ielx_back" title="'.$this->getLang('STEP').' 1"><span>'.$this->getLang('BACK')." </span></a>\n";
	echo '</div>'."\n";
	return;
} else if ($this->dataValue('usr', 'errormsg', '') != '') {
	//hide rest messages in case we come back to this page
} else if ($this->dataValue('queries', '', 0) == 0) {
	echo '<div class="ielx_warn"><strong>'.$this->getLang('WARNING')."</strong><br />\n".$this->getLang('NO_QUERIES_WARN')."</div>\n";
	echo '<div class="ielx_vspace">'."\n";
	echo '<a href="'.$this->url.'/?step=1&amp;lang='.$this->currentLang().'" class="ielx_back" title="'.$this->getLang('STEP').' 1"><span>'.$this->getLang('RETRY_PREV_STEP')." </span></a>\n";
	echo '</div>'."\n";
} else {
	echo '<div class="ielx_info">'.$this->getLang('INIT_DATA_IMPORTED').' ';
	printf($this->getLang('QUERIES_EXEC'), '<strong>'.$this->dataValue('queries', '', 0).'</strong>');
	echo "</div>\n";
}

$errormsg = $this->dataValue('usr', 'errormsg', '');
if ($errormsg != '') {
	echo '<div class="ielx_error">'.$errormsg."</div><br />\n";
}
?>

<form name="fmconfig" class="ielx_form" action="<?php echo $this->url; ?>/index.php" method="post" onsubmit="return ielxValidateUser();" autocomplete="off">
	<fieldset class="ielx_fieldset">
		<legend><?php echo $this->getLang('YOUR_DETAILS'); ?></legend>
		<div class="ielx_formrow">
			<label class="ielx_label" for="u_firstname"><?php echo $this->getLang('FIRSTNAME'); ?></label>
			<div class="ielx_labelside">
				<input type="text" name="u_firstname" id="u_firstname" placeholder="<?php echo $this->getLang('FIRSTNAME'); ?>" value="<?php echo $this->dataValue('usr', 'u_firstname', ''); ?>" class="ielx_text" required="required" />
			</div>
		</div>
		<div class="ielx_formrow">
			<label class="ielx_label" for="u_lastname"><?php echo $this->getLang('LASTNAME'); ?></label>
			<div class="ielx_labelside">
				<input type="text" name="u_lastname" id="u_lastname" placeholder="<?php echo $this->getLang('LASTNAME'); ?>" value="<?php echo $this->dataValue('usr', 'u_lastname', ''); ?>" class="ielx_text" required="required" />
			</div>
		</div>
		<div class="ielx_formrow">
			<label class="ielx_label" for="u_email"><?php echo $this->getLang('EMAIL'); ?></label>
			<div class="ielx_labelside">
				<input type="email" name="u_email" id="u_email" dir="ltr" placeholder="<?php echo $this->getLang('EMAIL'); ?>" value="<?php echo $this->dataValue('usr', 'u_email', ''); ?>" class="ielx_text" required="required" autocomplete="off" />
			</div>
		</div>
		<div class="ielx_formrow">
			<label class="ielx_label" for="u_uname"><?php echo $this->getLang('USERNAME'); ?></label>
			<div class="ielx_labelside">
				<input type="text" name="u_uname" id="u_uname" dir="ltr" placeholder="<?php echo $this->getLang('USERNAME'); ?>" value="<?php echo $this->dataValue('usr', 'u_uname', $this->makeUname()); ?>" class="ielx_text" required="required" autocomplete="off" />
				<div class="ielx_vsspace"><a href="javascript:void(null);" onclick="ielxMakeUname();" class="ielx_toggle"><i class="fas fa-sync-alt" aria-hidden="false"></i> <?php echo $this->getLang('GEN_OTHER'); ?></a></div>
				<div class="ielx_tip"><?php echo $this->getLang('AVOID_COMUNAMES'); ?></div>
			</div>
		</div>
		<div class="ielx_formrow">
			<label class="ielx_label" for="u_pword"><?php echo $this->getLang('PASSWORD'); ?></label>
			<div class="ielx_labelside">
				<input type="password" name="u_pword" id="u_pword" dir="ltr" placeholder="<?php echo $this->getLang('PASSWORD'); ?>" value="<?php echo $this->dataValue('usr', 'u_pword', ''); ?>" class="ielx_text" required="required" autocomplete="new-password" />
			</div>
		</div>
		<div class="ielx_formrow">
			<label class="ielx_label" for="u_pword2"><?php echo $this->getLang('CONFIRM_PASS'); ?></label>
			<div class="ielx_labelside">
				<input type="password" name="u_pword2" id="u_pword2" dir="ltr" placeholder="<?php echo $this->getLang('PASSWORD'); ?>" value="" class="ielx_text" required="required" autocomplete="new-password" />
			</div>
		</div>
	</fieldset>

	<input type="hidden" name="step" value="3" />
	<input type="hidden" name="lang" value="<?php echo $this->currentLang(); ?>" />
	<input type="hidden" id="langfamily" name="langfamily" dir="ltr"  value="<?php echo $this->langInfo('LANGUAGE'); ?>" />
	<input type="hidden" id="elxisbasefmconfig" name="elxisbasefmconfig" dir="ltr" value="<?php echo $this->url; ?>" />

<?php 
	foreach ($this->dataValue('cfg', '', array()) as $k => $v) {
		if (strpos($k, 'cfg_') !== 0) { continue; }
		echo '<input type="hidden" name="'.$k.'" value="'.$v.'" />'."\n";
	}
?>
	<div class="ielx_formrow">
		<label class="ielx_label" for="usubmit"></label>
		<div class="ielx_labelside">
			<button type="submit" name="usubmit" id="usubmit" value="1" class="ielx_btn"><span><?php echo $this->getLang('SUBMIT'); ?> </span></button>
		</div>
	</div>
</form>
<div id="ei_baseurl" class="ielx_invisible" dir="ltr"><?php echo $this->url; ?></div>