<?php 
/**
* @version		$Id: system.html.php 2311 2019-12-07 07:56:11Z IOS $
* @package		Elxis
* @subpackage	CPanel component
* @copyright	Copyright (c) 2006-2019 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class systemCPView extends cpanelView {


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/*****************************/
	/* DISPLAY ELXIS INFORMATION */
	/*****************************/
	public function elxisInformation() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eDate = eFactory::getDate();

		$repo_path = rtrim($elxis->getConfig('REPO_PATH'), '/');
		if ($repo_path == '') { $repo_path = ELXIS_PATH.'/repository'; }

		echo '<h1><i class="felxis-logo"></i> Elxis '.$elxis->getVersion().' <span>'.$elxis->fromVersion('CODENAME')."</span></h1>\n";

		echo '<div class="elx5_box elx5_border_blue">'."\n";
		echo '<div class="elx5_box_body">'."\n";
		echo '<div class="elx5_dataactions elx5_spad"><h3 class="elx5_box_title">'.$eLang->get('ELXIS_INFO')."</h3></div>\n";
		echo '<table id="elxisinfotbl" class="elx5_datatable">'."\n";
		echo "<tbody>\n";
		echo '<tr><th>'.$eLang->get('PLATFORM').'</th><td>Elxis</td></tr>'."\n";
		echo '<tr><th>'.$eLang->get('VERSION').'</th><td>'.$elxis->fromVersion('RELEASE').'.'.$elxis->fromVersion('LEVEL').'</td></tr>'."\n";
		echo '<tr><th>'.$eLang->get('REVISION_NUMBER').'</th><td>'.$elxis->fromVersion('REVISION').'</td></tr>'."\n";
		echo '<tr><th>'.$eLang->get('CODENAME').'</th><td>'.$elxis->fromVersion('CODENAME').'</td></tr>'."\n";
		echo '<tr><th>'.$eLang->get('STATUS').'</th><td>'.$elxis->fromVersion('STATUS').'</td></tr>'."\n";
		echo '<tr><th>'.$eLang->get('RELEASE_DATE').'</th><td>'.$eDate->formatDate($elxis->fromVersion('RELDATE'), $eLang->get('DATE_FORMAT_10')).'</td></tr>'."\n";
		echo '<tr><th>'.$eLang->get('AUTHOR').'</th><td>Elxis Team (Chief developer Ioannis Sannos)</td></tr>'."\n";
		echo '<tr><th>'.$eLang->get('COPYRIGHT').'</th><td>'.$elxis->fromVersion('COPYRIGHTURL').'</td></tr>'."\n";
		echo '<tr><th>'.$eLang->get('POWERED_BY').'</th><td>'.$elxis->fromVersion('POWEREDBY').'</td></tr>'."\n";
		echo '<tr><th>'.$eLang->get('HEADQUARTERS').'</th><td>Athens, Hellas</td></tr>'."\n";
		echo '<tr><th>'.$eLang->get('LICENSE').'</th><td><a href="https://www.elxis.org/elxis-public-license.html" target="_blank">Elxis Public License</a></td></tr>'."\n";
		echo "</tbody>\n";
		echo "</table>\n";
		echo "</div>\n</div>\n";

		$linfo = $eLang->getallinfo($elxis->getConfig('LANG'));
		$current_daytime = $eDate->worldDate('now', $elxis->getConfig('TIMEZONE'), $eLang->get('DATE_FORMAT_10'));

		echo '<div class="elx5_box elx5_border_blue">'."\n";
		echo '<div class="elx5_box_body">'."\n";
		echo '<div class="elx5_dataactions elx5_spad"><h3 class="elx5_box_title">'.$eLang->get('ELXIS_ENVIROMENT')."</h3></div>\n";
		echo '<table id="elxisenvtbl" class="elx5_datatable">'."\n";
		echo "<tbody>\n";
		echo '<tr><td class="elx5_bold">'.$eLang->get('INSTALL_PATH').'</td><td><em>'.ELXIS_PATH.'/</em></td></tr>'."\n";

		if (!file_exists($repo_path.'/') || !is_dir($repo_path.'/')) {
			echo '<tr class="elx5_rowwarn"><td class="elx5_bold">'.$eLang->get('REPO_PATH').'</td><td><em>'.$repo_path.'/</em> <span class="elx5_red">Does not exist!</span></td></tr>'."\n";
		} elseif (!is_writeable($repo_path.'/')) {
			echo '<tr class="elx5_rowwarn"><td class="elx5_bold">'.$eLang->get('REPO_PATH').'</td><td><em>'.$repo_path.'/</em> <span class="elx5_red">Not writeable!</span></td></tr>'."\n";
		} elseif (preg_match('@(\/repository)$@', $repo_path)) {
			echo '<tr class="elx5_rowwarn"><td class="elx5_bold">'.$eLang->get('REPO_PATH').'</td><td><em>'.$repo_path.'/</em> <span class="elx5_red">'.$eLang->get('REPO_DEF_PATH').'</span></td></tr>'."\n";
		//} elseif (strpos($repo_path, ELXIS_PATH) !== false) {
		//	echo '<tr class="elx5_rowwarn"><td class="elx5_bold">'.$eLang->get('REPO_PATH').'</td><td><em>'.$repo_path.'/</em> <span class="elx5_red">'.$eLang->get('IS_PUBLIC').'</span></td></tr>'."\n";
		} else {
			echo '<tr><td class="elx5_bold">'.$eLang->get('REPO_PATH').'</td><td><em>'.$repo_path.'/</em>'.'</td></tr>'."\n";
		}
		echo '<tr><th class="elx5_themphasis" colspan="2">'.$eLang->get('LOCALE').'</th></tr>'."\n";
		echo '<tr><td class="elx5_bold">'.$eLang->get('LANGUAGE').'</td><td><strong>'.$elxis->getConfig('LANG').'</strong> '.$linfo['LANGUAGE'].'-'.$linfo['REGION'].' <em>'.$linfo['NAME'].'</em> '.$linfo['NAME_ENG'].'</td></tr>'."\n";
		echo '<tr><td class="elx5_bold">'.$eLang->get('TIMEZONE').'</td><td>'.$elxis->getConfig('TIMEZONE').' <span dir="ltr">('.$current_daytime.')</span></td></tr>'."\n";
		echo '<tr><th class="elx5_themphasis" colspan="2">'.$eLang->get('DATABASE').'</th></tr>'."\n";
		echo '<tr><td class="elx5_bold">'.$eLang->get('DB_TYPE').'</td><td>'.$elxis->getConfig('DB_TYPE').'</td></tr>'."\n";
		echo '<tr><td class="elx5_bold">'.$eLang->get('HOST').'</td><td>'.$elxis->getConfig('DB_HOST').'</td></tr>'."\n";
		if ($elxis->acl()->check('com_cpanel', 'settings', 'edit') > 0) {
			echo '<tr><td class="elx5_bold">DSN</td><td>'.$elxis->getConfig('DB_DSN').'</td></tr>'."\n";
		}
		switch ($elxis->getConfig('SESSION_HANDLER')) {
			case 'files': $text = $eLang->get('FILES'); break;
			case 'database': $text = $eLang->get('DATABASE'); break;
			case 'none': default: $text = $eLang->get('NONE'); break;
		}

		echo '<tr><th class="elx5_themphasis" colspan="2">'.$eLang->get('SESSION').'</th></tr>'."\n";
		echo '<tr><td class="elx5_bold">'.$eLang->get('HANDLER').'</td><td>'.$text.'</td></tr>'."\n";
		$text = intval($elxis->getConfig('SESSION_LIFETIME') / 60).' min';
		echo '<tr><td class="elx5_bold">'.$eLang->get('LIFETIME').'</td><td>'.$text.'</td></tr>'."\n";
		if ($elxis->getConfig('SESSION_HANDLER') == 'files') {
			if (!file_exists($repo_path.'/sessions/') || !is_dir($repo_path.'/sessions/')) {
				$text = sprintf($eLang->get('FOLDER_NOT_EXIST'), 'sessions/');
				echo '<tr class="elx5_rowwarn"><td class="elx5_bold">'.$eLang->get('PATH').'</td><td><em>'.$repo_path.'/sessions/</em> <span class="elx5_red">'.$text.'</span></td></tr>'."\n";
			} elseif (!is_writeable($repo_path.'/sessions/')) {
				echo '<tr class="elx5_rowwarn"><td class="elx5_bold">'.$eLang->get('PATH').'</td><td><em>'.$repo_path.'/sessions/</em> <span class="elx5_red">Not writeable!</span></td></tr>'."\n";
			} elseif (preg_match('@(\/repository)$@', $repo_path)) {
			//} elseif (strpos($repo_path, ELXIS_PATH) !== false) {
				echo '<tr class="elx5_rowwarn"><td class="elx5_bold">'.$eLang->get('PATH').'</td><td><em>'.$repo_path.'/sessions/</em> <span class="elx5_red">'.$eLang->get('IS_PUBLIC').'</span></td></tr>'."\n";
			} else {
				echo '<tr><td class="elx5_bold">'.$eLang->get('PATH').'</td><td><em>'.$repo_path.'/sessions/</em>'.'</td></tr>'."\n";
			}
		}

		echo '<tr><th class="elx5_themphasis" colspan="2">'.$eLang->get('SECURITY').'</th></tr>'."\n";
		switch ($elxis->getConfig('SECURITY_LEVEL')) {
			case 2: $text = $eLang->get('INSANE'); break;
			case 1: $text = $eLang->get('HIGH'); break;
			case 0: default: $text = $eLang->get('NORMAL'); break;
		}
		echo '<tr><td class="elx5_bold">'.$eLang->get('SECURITY_LEVEL').'</td><td>'.$text.'</td></tr>'."\n";
		if ($elxis->getConfig('DEFENDER') == '') {
			echo '<tr class="elx5_rowwarn"><td class="elx5_bold">'.$eLang->get('ELXIS_DEFENDER').'</td><td>'.$eLang->get('OFF').'</td></tr>'."\n";
		} else {
			echo '<tr><td class="elx5_bold">'.$eLang->get('ELXIS_DEFENDER').'</td><td>'.$elxis->getConfig('DEFENDER').'</td></tr>'."\n";
		}

		if (!file_exists($repo_path.'/logs/') || !is_dir($repo_path.'/logs/')) {
			$text = sprintf($eLang->get('FOLDER_NOT_EXIST'), 'logs/');
			echo '<tr class="elx5_rowwarn"><td class="elx5_bold">'.$eLang->get('DEFENDER_LOGS').'</td><td><em>'.$repo_path.'/logs/defender_ban.php</em> <span class="elx5_red">'.$text.'</span></td></tr>'."\n";
		} elseif (!is_writeable($repo_path.'/logs/')) {
			echo '<tr class="elx5_rowwarn"><td class="elx5_bold">'.$eLang->get('DEFENDER_LOGS').'</td><td><em>'.$repo_path.'/logs/defender_ban.php</em> <span class="elx5_red">Not writeable!</span></td></tr>'."\n";
		} elseif (preg_match('@(\/repository)$@', $repo_path)) {
		//} elseif (strpos($repo_path, ELXIS_PATH) !== false) {
			echo '<tr class="elx5_rowwarn"><td class="elx5_bold">'.$eLang->get('DEFENDER_LOGS').'</td><td><em>'.$repo_path.'/logs/defender_ban.php</em> <span class="elx5_red">'.$eLang->get('IS_PUBLIC').'</span></td></tr>'."\n";
		} else {
			echo '<tr><td class="elx5_bold">'.$eLang->get('DEFENDER_LOGS').'</td><td><em>'.$repo_path.'/logs/defender_ban.php</em>'.'</td></tr>'."\n";
		}

		switch ($elxis->getConfig('SSL')) {
			case 1: $text = $eLang->get('ADMINISTRATION'); break;
			case 2: $text = $eLang->get('PUBLIC_AREA').' + '.$eLang->get('ADMINISTRATION'); break;
			case 0: default: $text = $eLang->get('OFF'); break;
		}
		echo '<tr><td class="elx5_bold">'.$eLang->get('SSL_SWITCH').'</td><td>'.$text.'</td></tr>'."\n";
		if (ELXIS_ADIR == 'estia') {
			echo '<tr class="elx5_rowwarn"><td class="elx5_bold">'.$eLang->get('ADMIN_FOLDER').'</td><td><em>'.ELXIS_ADIR.'</em> <span class="elx5_red">'.$eLang->get('DEF_NAME_RENAME').'</span></td></tr>'."\n";
		} else {
			echo '<tr><td class="elx5_bold">'.$eLang->get('ADMIN_FOLDER').'</td><td><em>'.ELXIS_ADIR.'</em></td></tr>'."\n";
		}
		echo '<tr><th class="elx5_themphasis" colspan="2">'.$eLang->get('ERRORS').'</th></tr>'."\n";
		if ($elxis->getConfig('ERROR_REPORT') > 0) {
			switch ($elxis->getConfig('SECURITY_LEVEL')) {
				case 1: $text = $eLang->get('ERRORS'); break;
				case 2: $text = $eLang->get('ERRORS').' + '.$eLang->get('WARNINGS'); break;
				case 3: default: $text = $eLang->get('ERRORS').' + '.$eLang->get('WARNINGS').' + '.$eLang->get('NOTICES'); break;
			}
			echo '<tr class="elx5_rowwarn"><td class="elx5_bold">'.$eLang->get('REPORT').'</td><td><span class="elx5_red">'.$text.'</span></td></tr>'."\n";
		} else {
			echo '<tr><td class="elx5_bold">'.$eLang->get('REPORT').'</td><td>'.$eLang->get('OFF').'</td></tr>'."\n";
		}
		switch ($elxis->getConfig('ERROR_LOG')) {
			case 1: $text = $eLang->get('ERRORS'); break;
			case 2: $text = $eLang->get('ERRORS').' + '.$eLang->get('WARNINGS'); break;
			case 3: $text = $eLang->get('ERRORS').' + '.$eLang->get('WARNINGS').' + '.$eLang->get('NOTICES'); break;
			case 0: default: $text = $eLang->get('OFF'); break;
		}
		echo '<tr><td class="elx5_bold">'.$eLang->get('LOG').'</td><td>'.$text.'</td></tr>'."\n";
		if ($elxis->getConfig('LOG_ROTATE') == 0) {
			echo '<tr class="elx5_rowwarn"><td class="elx5_bold">'.$eLang->get('ROTATE').'</td><td><span class="elx5_red">'.$eLang->get('NO').'</span></td></tr>'."\n";
		} else {
			echo '<tr><td class="elx5_bold">'.$eLang->get('ROTATE').'</td><td>'.$eLang->get('YES').'</td></tr>'."\n";
		}
		echo "</tbody>\n";
		echo "</table>\n";
		echo "</div>\n</div>\n";
	}


	/***************************/
	/* DISPLAY PHP INFORMATION */
	/***************************/
	public function phpInformation($phpinfo) {
		$eLang = eFactory::getLang();

		if (!$phpinfo) {
			echo '<h1><i class="fab fa-php"></i> '.$eLang->get('PHP_INFO')."</h1>\n";
			echo '<div class="elx5_error">Could not get PHP information! Most probably <strong>phpinfo</strong> function is disabled.</div>'."\n";
			return;
		}

		echo '<h1><i class="fab fa-php"></i> '.$eLang->get('PHP_VERSION').' <span>'.phpversion()."</span></h1>\n";

		foreach ($phpinfo as $ctg => $items) {
			$columns = $items['tblcolumns'];
			if (count($items) > 1) {
				echo '<div class="elx5_box elx5_border_blue">'."\n";
				echo '<div class="elx5_box_body">'."\n";
				echo '<div class="elx5_dataactions elx5_spad"><h3 class="elx5_box_title">'.$ctg."</h3></div>\n";
				echo '<table class="elx5_datatable">'."\n";
				echo "<tbody>\n";
				if ($columns == 3) {
					echo '<tr><th class="elx5_themphasis"></th><th class="elx5_themphasis">Local value</th><th class="elx5_themphasis">Master value</th></tr>'."\n";
				}
				foreach ($items as $key => $item) {
					if ($key == 'tblcolumns') { continue; }
					if (is_array($item)) {
						echo '<tr><td class="elx5_bold">'.$key.'</td><td>'.$item['local'].'</td><td>'.$item['master'].'</td></tr>'."\n";
					} else {
						$text = $this->breaklong($key, $item);
						echo '<tr><td class="elx5_bold">'.$key.'</td><td colspan="2">'.$text.'</td></tr>'."\n";
					}
				}
				echo "</tbody>\n";
				echo "</table>\n";
				echo "</div>\n</div>\n";
			}
		}
	}


	/**********************/
	/* BREAK LONG STRINGS */
	/**********************/
	private function breaklong($key, $text, $max=100) {
		$key = strtoupper(trim($key));
		if ($key == 'HTTP_COOKIE') {
			$chunks = chunk_split($text, $max, " \n");
		} else if ($key == 'PATH') {
			$chunks = chunk_split($text, $max, " \n");
		} else if ($key == 'COOKIE') {
			$chunks = chunk_split($text, $max, " \n");
		} else {
			return $text;
		}
		return $chunks;
	}

}

?>