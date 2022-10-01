<?php 
/**
* @version		$Id: mod_adminprofile.php 2049 2019-02-01 18:25:17Z IOS $
* @package		Elxis
* @subpackage	Module Administration profile
* @copyright	Copyright (c) 2006-2019 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


if (!class_exists('modadminProf', false)) {
	class modadminProf {
		

		/*********************/
		/* MAGIC CONSTRUCTOR */
		/*********************/
		public function __construct() {
		}


		/********************/
		/* RUN FOREST, RUN! */
		/********************/
		public function run() {
			if (!defined('ELXIS_ADMIN')) {
				echo '<div class="elx5_warning">This module is available only in Elxis administration area!'."</div>\n";
				return;
			}

			if (ELXIS_INNER == 1) { return; }

			$elxis = eFactory::getElxis();
			$eLang = eFactory::getLang();
			$eDate = eFactory::getDate();

			$avatar = $elxis->obj('avatar')->getAvatar($elxis->user()->avatar, 40, 0, '');
			$dt = $eDate->getTS() - $elxis->session()->first_activity;
			$min = floor($dt/60);
			$sec = $dt - ($min * 60);

			$groupname = ($elxis->user()->gid == 1) ? $eLang->get('ADMINISTRATOR') : $elxis->user()->groupname;

			echo '<div class="modaprof_user">'."\n";
			echo '<a href="'.$elxis->makeAURL('cpanel:logout.html', 'inner.php').'" title="'.$eLang->get('LOGOUT').'">'."\n";
			echo '<div class="modaprof_usericon"><i class="fas fa-sign-out-alt"></i></div>'."\n";
			echo '<div class="modaprof_usermain">'."\n";
			echo '<h3 class="modaprof_username">'.$elxis->user()->firstname.' '.$elxis->user()->lastname."</h3>\n";
			echo '<div class="modaprof_userinfo"><img src="'.$avatar.'" alt="avatar" />'.$groupname.' <span dir="ltr">('.$min.':'.sprintf("%02d", $sec).')</span></div>'."\n";
			echo "</div>\n";
			echo "</a>\n";
			echo "</div>\n";
		}

	}
}


$aprofile = new modadminProf();
$aprofile->run();
unset($aprofile);

?>