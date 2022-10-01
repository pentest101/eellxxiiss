<?php 
/**
* @version		$Id: step3.php 1798 2016-02-24 18:11:59Z sannosi $
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
<h2><?php echo $this->getLang('FINISH'); ?></h2>

<?php 
if (($this->dataValue('final', 'save', false) === false) || ($this->dataValue('final', 'renhtaccess', -1) === 0)) {
	echo '<div class="ielx_warn">'.$this->getLang('ELXIS_INST_WARN')."<br />\n";
	if ($this->dataValue('final', 'save', false) === false) {
		echo '- '.$this->getLang('CNOT_CREA_CONFIG')."<br />\n";
	}
	if ($this->dataValue('final', 'renhtaccess', -1) === 0) {
		echo '- '.$this->getLang('CNOT_REN_HTACC')."<br />\n";
	}
	echo "</div>\n";
} else {
	echo '<div class="ielx_info">'.$this->getLang('ELXIS_INST_SUCC')."</div>\n";
}

if ($this->dataValue('final', 'save', false) === false) {
	echo '<h3>'.$this->getLang('CONFIG_FILE')."</h3>\n";
	echo '<div class="ielx_textblock" dir="ltr">'.ELXIS_PATH."/configuration.php</div>\n";
	echo '<p>'.$this->getLang('CONFIG_FILE_MANUAL')."</p>\n";
	echo '<textarea rows="10" cols="80" class="ielx_license" dir="ltr">'."\n";
	echo htmlspecialchars($this->dataValue('final', 'config', ''))."</textarea>\n";
}

if ($this->dataValue('final', 'renhtaccess', -1) === 0) {
	echo '<h3>.htaccess / web.config</h3>'."\n";
	echo '<p>'.$this->getLang('REN_HTACCESS_MANUAL')."</p>\n";
}
?>

<h3><?php echo $this->getLang('WHAT_TODO'); ?></h3>

<ul class="ielx_finalacts">
<?php 
	if (file_exists(ELXIS_PATH.'/estia/')) {
		echo '<li><i class="fas fa-check-square"></i> '.$this->getLang('RENAME_ADMIN_FOLDER')."</li>\n";
		echo '<li><a href="'.$this->data['cfg']['cfg_url'].'/estia/"><i class="fas fa-external-link-square-alt"></i> '.$this->getLang('LOGIN_CONFIG')."</a></li>\n";
	} else {
		$admin_folder = isset($this->data['final']['adminfolder']) ? trim($this->data['final']['adminfolder']) : 'estia';
		if ($admin_folder == '') { $admin_folder = 'estia'; }
		if (file_exists(ELXIS_PATH.'/'.$admin_folder.'/')) {
			echo '<li><a href="'.$this->data['cfg']['cfg_url'].'/'.$admin_folder.'/"><i class="fas fa-external-link-square-alt"></i> '.$this->getLang('LOGIN_CONFIG')."</a></li>\n";
		} else {
			echo '<li><i class="fas fa-check-square"></i> '.$this->getLang('LOGIN_CONFIG')."</li>\n";
		}
	}
?>
	<li><a href="<?php echo $this->url; ?>/"><i class="fas fa-external-link-square-alt"></i> <?php echo $this->getLang('VISIT_NEW_SITE'); ?></a></li>
	<li><a href="http://forum.elxis.org/" target="_blank"><i class="fas fa-external-link-square-alt"></i> <?php echo $this->getLang('VISIT_ELXIS_SUP'); ?></a></li>
</ul><br />

<p><?php echo $this->getLang('THANKS_USING_ELXIS'); ?><br />Elxis Team</p>