<?php

/****************************************************************************/
/*                                                                          */
/* YOU MAY WISH TO MODIFY OR REMOVE THE FOLLOWING LINES WHICH SET DEFAULTS  */
/*                                                                          */
/****************************************************************************/

$preferences = Swift_Preferences::getInstance();

// Sets the default charset so that setCharset() is not needed elsewhere
$preferences->setCharset('utf-8');

$tmpDir = '';
if (defined('_ELXIS_') && defined('ELXIS_PATH')) {
	if (class_exists('elxisFramework', false)) {
		$elxis = eFactory::getElxis();
		$tmpDir = rtrim($elxis->getConfig('REPO_PATH'), '/');
		if ($tmpDir == '') { $tmpDir = ELXIS_PATH.'/repository'; }
		$tmpDir .= '/tmp/';
	}
}

if ($tmpDir == '') { $tmpDir = getenv('TMPDIR'); }

if ($tmpDir && @is_writable($tmpDir)) {
    $preferences->setTempDir($tmpDir)->setCacheType('disk');
} elseif (function_exists('sys_get_temp_dir') && @is_writable(sys_get_temp_dir())) {
    $preferences->setTempDir(sys_get_temp_dir())->setCacheType('disk');
}

if (version_compare(phpversion(), '5.4.7', '<')) {
    $preferences->setQPDotEscape(false);
}