<?php 
/**
* @version		$Id: mod_adminmenu.php 2365 2020-12-12 19:19:18Z IOS $
* @package		Elxis
* @subpackage	Module Administration menu
* @copyright	Copyright (c) 2006-2021 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


if (!class_exists('modadminMenu', false)) {
	class modadminMenu {

		private $activemenu = '';
		private $idx = 1;
		private $items = array();
		private $lock = false;
		

		/*********************/
		/* MAGIC CONSTRUCTOR */
		/*********************/
		public function __construct() {
			$this->activemenu = $this->getActiveMenu();
		}


		/****************************/
		/* REGISTER A NEW MENU ITEM */
		/****************************/
		private function setItem($title, $link='', $icon='', $target='', $separator=false) {
			$item = new stdClass;
			$item->idx = $this->idx;
			$item->title = $title;
			$item->link = $link;
			$item->icon = $icon;
			$item->target = $target;
			$item->separator = (bool)$separator;
			$item->children = array();
			$this->idx++;

			return $item;
		}


		/**********************/
		/* COLLECT MENU ITEMS */
		/**********************/
		private function collect() {
			$elxis = eFactory::getElxis();
			$eLang = eFactory::getLang();
			$eURI = eFactory::getURI();

			$this->items['home'] = $this->setItem(eFactory::getLang()->get('HOME'), $eURI->makeAURL(), 'fas fa-home');

			$this->makeSite($elxis, $eLang);
			$this->makeContent($elxis, $eLang);
			$this->makeMenu($elxis, $eLang);
			$this->makeUsers($elxis, $eLang);
			$this->makeExtensions($elxis, $eLang);
			$this->makeThirdComponents($elxis, $eLang);
			$this->makeSystem($elxis, $eLang);
		}


		/***********************/
		/* MAKE "SITE" SECTION */
		/***********************/
		private function makeSite($elxis, $eLang) {
			$menu = $this->setItem($eLang->get('SITE'), '', 'fas fa-globe');
			if ($elxis->acl()->check('com_cpanel', 'settings', 'edit') > 0) {
				$link = $elxis->makeAURL('cpanel:config.html');
				$menu->children[] = $this->setItem($eLang->get('SETTINGS'), $link, 'fas fa-tools');
			}

			if ($elxis->acl()->check('com_cpanel', 'multisites', 'edit') > 0) {
				$link = $elxis->makeAURL('cpanel:multisites/');
				$menu->children[] = $this->setItem($eLang->get('MULTISITES'), $link, 'fas fa-project-diagram');
			}

			if ($elxis->acl()->check('component', 'com_emedia', 'manage') > 0) {
				$link = $elxis->makeAURL('emedia:/');
				$menu->children[] = $this->setItem($eLang->get('MEDIA'), $link, 'fas fa-images');
			}
			if ($elxis->acl()->check('com_cpanel', 'statistics', 'view') > 0) {
				$link = $elxis->makeAURL('cpanel:stats/');
				$menu->children[] = $this->setItem($eLang->get('STATISTICS'), $link, 'fas fa-chart-pie');
			}
			$menu->children[] = $this->setItem('', '', '', '', true);
			if ($elxis->acl()->check('com_cpanel', 'backup', 'edit') > 0) {
				$link = $elxis->makeAURL('cpanel:backup/');
				$menu->children[] = $this->setItem($eLang->get('BACKUP'), $link, 'fas fa-file-archive');
			}
			if ($elxis->acl()->check('com_cpanel', 'cache', 'manage') > 0) {
				$link = $elxis->makeAURL('cpanel:cache/');
				$menu->children[] = $this->setItem($eLang->get('CACHE'), $link, 'fas fa-memory');
			}
			if ($elxis->acl()->check('com_cpanel', 'logs', 'manage') > 0) {
				$link = $elxis->makeAURL('cpanel:logs/');
				$menu->children[] = $this->setItem($eLang->get('LOGS'), $link, 'fas fa-file-signature');
			}
			if ($elxis->acl()->check('com_cpanel', 'settings', 'edit') > 0) {
				$link = $elxis->makeAURL('cpanel:codeeditor/');
				$menu->children[] = $this->setItem('Code editor', $link, 'fas fa-code');
			}
			if ($elxis->acl()->check('component', 'com_etranslator', 'manage') > 0) {
				$link = $elxis->makeAURL('etranslator:/');
				$menu->children[] = $this->setItem($eLang->get('TRANSLATOR'), $link, 'fas fa-language');
			}
			if ($elxis->acl()->check('com_cpanel', 'routes', 'manage') > 0) {
				$link = $elxis->makeAURL('cpanel:routing/');
				$menu->children[] = $this->setItem($eLang->get('ROUTING'), $link, 'fas fa-network-wired');
			}

			$menu->children[] = $this->setItem('', '', '', '', true);
			$menu->children[] = $this->setItem($eLang->get('HELP'), 'https://www.elxis.net/docs/', 'fas fa-question-circle', '_blank');
			$this->items['site'] = $menu;
		}


		/**************************/
		/* MAKE "CONTENT" SECTION */
		/**************************/
		private function makeContent($elxis, $eLang) {
			if ($elxis->acl()->check('component', 'com_content', 'manage') < 1) { return; }

			$menu = $this->setItem($eLang->get('CONTENT'), '', 'fas fa-file-alt');
			$link = $elxis->makeAURL('content:categories/');
			$menu->children[] = $this->setItem($eLang->get('CATEGORIES'), $link, 'fas fa-folder-open');
			if ($elxis->acl()->check('com_content', 'category', 'add') > 0) {
				$link = $elxis->makeAURL('content:categories/add.html');
				$menu->children[] = $this->setItem($eLang->get('NEW_CATEGORY'), $link, 'fas fa-folder-plus');
			}
			$link = $elxis->makeAURL('content:articles/');
			$menu->children[] = $this->setItem($eLang->get('ALL_ARTICLES'), $link, 'fas fa-file');
			$link = $elxis->makeAURL('content:articles/?catid=0');
			$menu->children[] = $this->setItem($eLang->get('AUTONOMOUS_PAGES'), $link, 'fas fa-file');
			if ($elxis->acl()->check('com_content', 'article', 'add') > 0) {
				$link = $elxis->makeAURL('content:articles/add.html');
				$menu->children[] = $this->setItem($eLang->get('NEW_ARTICLE'), $link, 'fas fa-plus');
			}
			if ($elxis->acl()->check('com_content', 'frontpage', 'edit') > 0) {
				$link = $elxis->makeAURL('content:fpage/');
				$menu->children[] = $this->setItem($eLang->get('FRONTPAGE'), $link, 'fas fa-flag-checkered');
			}
			$this->items['content'] = $menu;
		}


		/***********************/
		/* MAKE "MENU" SECTION */
		/***********************/
		private function makeMenu($elxis, $eLang) {
			$db = eFactory::getDB();

			if ($elxis->acl()->check('component', 'com_emenu', 'manage') < 1) { return; }

			$menu = $this->setItem($eLang->get('MENU'), '', 'fas fa-bars');

			$menu->children[] = $this->setItem($eLang->get('MENUS_MANAGER'), $elxis->makeAURL('emenu:/'), 'fas fa-bars');

			$section = 'frontend';
			$sql = "SELECT ".$db->quoteId('collection')." FROM ".$db->quoteId('#__menu')
			."\n WHERE ".$db->quoteId('section')." = :xsection GROUP BY ".$db->quoteId('collection')
			."\n ORDER BY ".$db->quoteId('collection')." ASC";
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':xsection', $section, PDO::PARAM_STR);
			$stmt->execute();
			$collections = $stmt->fetchCol();
			if ($collections) {
				foreach ($collections as $collection) {
					$link = $elxis->makeAURL('emenu:mitems/'.$collection.'.html');
					$menu->children[] = $this->setItem($collection, $link, 'fas fa-ellipsis-v');
				}
			}

			$this->items['menu'] = $menu;
		}


		/************************/
		/* MAKE "USERS" SECTION */
		/************************/
		private function makeUsers($elxis, $eLang) {
			if ($elxis->acl()->check('component', 'com_user', 'manage') < 1) { return; }

			$menu = $this->setItem($eLang->get('USERS'), '', 'fas fa-users');

			$link = $elxis->makeAURL('user:users/');
			$menu->children[] = $this->setItem($eLang->get('MANAGE_USERS'), $link, 'fas fa-user-cog');

			if ($elxis->acl()->check('com_user', 'groups', 'manage') > 0) {
				$link = $elxis->makeAURL('user:groups/');
				$menu->children[] = $this->setItem($eLang->get('USER_GROUPS'), $link, 'fas fa-users-cog');
			}

			if ($elxis->acl()->check('com_user', 'acl', 'manage') > 0) {
				$link = $elxis->makeAURL('user:acl/');
				$menu->children[] = $this->setItem($eLang->get('ACCESS_MANAGER'), $link, 'fas fa-user-lock');
			}

			$this->items['user'] = $menu;
		}


		/*****************************/
		/* MAKE "EXTENSIONS" SECTION */
		/*****************************/
		private function makeExtensions($elxis, $eLang) {
			if ($elxis->acl()->check('component', 'com_extmanager', 'manage') < 1) { return; }

			$menu = $this->setItem($eLang->get('EXTENSIONS'), '', 'fas fa-cubes');

			$c = $elxis->acl()->check('com_extmanager', 'components', 'install');
			$c += $elxis->acl()->check('com_extmanager', 'modules', 'install');
			$c += $elxis->acl()->check('com_extmanager', 'plugins', 'install');
			$c += $elxis->acl()->check('com_extmanager', 'templates', 'install');
			$c += $elxis->acl()->check('com_extmanager', 'engines', 'install');
			$c += $elxis->acl()->check('com_extmanager', 'auth', 'install');
			if ($c > 0) {
				if (defined('ELXIS_MULTISITE') && (ELXIS_MULTISITE > 1)) {
					$link = $elxis->makeAURL('extmanager:/');
					$menu->children[] = $this->setItem($eLang->get('SYNCHRONIZE'), $link, 'fas fa-download');
				} else {
					$title = $eLang->get('INSTALL').' &amp; '.$eLang->get('UPDATE');
					$link = $elxis->makeAURL('extmanager:/');
					$menu->children[] = $this->setItem($title, $link, 'fas fa-download');
					$link = $elxis->makeAURL('extmanager:install/updates.html');
					$menu->children[] = $this->setItem($eLang->get('CHECK_UPDATES'), $link, 'fas fa-check');
					$link = $elxis->makeAURL('extmanager:install/checkfs.html');
					$menu->children[] = $this->setItem($eLang->get('CHECK_FS'), $link, 'fas fa-file-medical-alt');
				}
			}

			$link = $elxis->makeAURL('extmanager:browse/');
			$menu->children[] = $this->setItem($eLang->get('BROWSE_EDC'), $link, 'felxis-logo');
			$menu->children[] = $this->setItem('', '', '', '', true);

			if ($elxis->acl()->check('com_extmanager', 'components', 'edit') > 0) {
				$link = $elxis->makeAURL('extmanager:components/');
				$menu->children[] = $this->setItem($eLang->get('COMPONENTS'), $link, 'fas fa-cube');
			}
			if ($elxis->acl()->check('com_extmanager', 'modules', 'edit') > 0) {
				$link = $elxis->makeAURL('extmanager:modules/');
				$menu->children[] = $this->setItem($eLang->get('MODULES'), $link, 'fas fa-puzzle-piece');
			}
			if ($elxis->acl()->check('com_extmanager', 'plugins', 'edit') > 0) {
				$link = $elxis->makeAURL('extmanager:plugins/');
				$menu->children[] = $this->setItem($eLang->get('CONTENT_PLUGINS'), $link, 'fas fa-plug');
			}
			if ($elxis->acl()->check('com_extmanager', 'templates', 'edit') > 0) {
				$link = $elxis->makeAURL('extmanager:templates/');
				$menu->children[] = $this->setItem($eLang->get('TEMPLATES'), $link, 'fas fa-paint-brush');

				$link = $elxis->makeAURL('extmanager:templates/positions.html');
				$menu->children[] = $this->setItem($eLang->get('MOD_POSITIONS'), $link, 'fas fa-puzzle-piece');
			}
			if ($elxis->acl()->check('com_extmanager', 'engines', 'edit') > 0) {
				$link = $elxis->makeAURL('extmanager:engines/');
				$menu->children[] = $this->setItem($eLang->get('SEARCH_ENGINES'), $link, 'fas fa-search');
			}
			if ($elxis->acl()->check('com_extmanager', 'auth', 'edit') > 0) {
				$link = $elxis->makeAURL('extmanager:auth/');
				$menu->children[] = $this->setItem($eLang->get('AUTH_METHODS'), $link, 'fas fa-key');
			}

			$this->items['extensions'] = $menu;
		}


		/******************************************/
		/* MAKE THIRD PARTY "COMPONENTS" SECTIONS */
		/******************************************/
		private function makeThirdComponents($elxis, $eLang) {
			$db = eFactory::getDB();

			$iscore = 0;
			$sql = "SELECT ".$db->quoteId('name').", ".$db->quoteId('component')." FROM ".$db->quoteId('#__components')
			."\n WHERE ".$db->quoteId('iscore')." = :xcore ORDER BY ".$db->quoteId('name')." ASC";
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':xcore', $iscore, PDO::PARAM_INT);
			$stmt->execute();
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if (!$rows) { return; }

			$mygid = (int)$elxis->user()->gid;
			$mylevel = (int)$elxis->acl()->getLevel();
			foreach ($rows as $row) {
				if ($elxis->acl()->check('component', $row['component'], 'manage') < 1) { continue; }

				$component = preg_replace('/^(com_)/', '', $row['component']);
				if (strpos($component, 'shop') !== false) {
					$iconclass = 'fas fa-shopping-cart';
				} else if ($component == 'sim') {
					$iconclass = 'fas fa-traffic-light';
				} else if ($component == 'mikro') {
					$iconclass = 'fas fa-blog';
				} else if (strpos($component, 'sitemap') !== false) {
					$iconclass = 'fas fa-sitemap';
				} else if (strpos($component, 'reserv') !== false) {
					$iconclass = 'fas fa-concierge-bell';
				} else if (strpos($component, 'archive') !== false) {
					$iconclass = 'fas fa-landmark';
				} else if (strpos($component, 'form') !== false) {
					$iconclass = 'fas fa-keyboard';
				} else if (strpos($component, 'import') !== false) {
					$iconclass = 'fas fa-file-import'; 
				} else if (strpos($component, 'content') !== false) {
					$iconclass = 'fas fa-user-edit'; 
				} else if (strpos($component, 'map') !== false) {
					$iconclass = 'fas fa-map-pin'; 
				} else if (strpos($component, 'galler') !== false) {
					$iconclass = 'fas fa-images';
				} else {
					$iconclass = 'fas fa-cube';
				}

				$comlink = $elxis->makeAURL($component.':/');
				$xmlmenus = $this->getComponentMenu($component);
				if (!$xmlmenus) {
					$this->items[$component] = $this->setItem($row['name'], $comlink, $iconclass);
					continue;
				}

				$xmlmenu = $xmlmenus[0];
				$cpiconfont = '';
				if (trim($xmlmenu->iconfont) != '') { $iconclass = $xmlmenu->iconfont; }
				if (trim($xmlmenu->cpiconfont) != '') { $cpiconfont = $xmlmenu->cpiconfont; }

				if (!is_array($xmlmenu->items) || (count($xmlmenu->items) == 0)) {
					$this->items[$component] = $this->setItem($row['name'], $comlink, $iconclass);
					continue;
				}

				$subitems = array();
				$subitems[] = $this->setItem($eLang->get('CONTROL_PANEL'), $comlink, $cpiconfont);
				foreach ($xmlmenu->items as $item) {
					if (($item->gid > 0) && ($item->gid <> $mygid)) { continue; }
					if ($item->alevel > $mylevel) { continue; }

					$is_separator = false;
					if ($item->menu_type == 'link') {
						$link = $elxis->makeAURL($item->link, $item->file);
					} else if ($item->menu_type == 'url') {
						$link = $item->link;
					} else if ($item->menu_type == 'separator') {
						$link = '';
						$is_separator = true;
					} else {
						continue;
					}

					$iconfont = (isset($item->iconfont)) ? trim($item->iconfont) : '';
					$subitems[] = $this->setItem($item->title, $link, $iconfont, $item->target, $is_separator);
				}

				if (count($subitems) > 1) {
					$menu = $this->setItem($row['name'], '', $iconclass);
					$menu->children = $subitems;
				} else {
					$menu = $this->setItem($row['name'], $comlink, $iconclass);
				}

				$this->items[$component] = $menu;
			}
		}


		/************************/
		/* MAKE "SYSTEM" SECTION */
		/************************/
		private function makeSystem($elxis, $eLang) {
			$menu = $this->setItem($eLang->get('SYSTEM'), '', 'felxis-logo');
			$link = $elxis->makeAURL('cpanel:sys/elxis.html');
			$menu->children[] = $this->setItem($eLang->get('ELXIS_INFO'), $link, 'felxis-logo');
			$link = $elxis->makeAURL('cpanel:sys/php.html');
			$menu->children[] = $this->setItem($eLang->get('PHP_INFO'), $link, 'fab fa-php');

			$this->items['system'] = $menu;
		}


		/**********************************************/
		/* GET COMPONENT'S BACKEND MENU FROM XML FILE */
		/**********************************************/
		private function getComponentMenu($component) {
			$file = ELXIS_PATH.'/components/com_'.$component.'/'.$component.'.menu.xml';
			if (!file_exists($file)) { return false; }

			elxisLoader::loadFile('includes/libraries/elxis/menu.xml.php');
			$xmenu = new elxisXMLMenu(null);
			$xmlmenus = $xmenu->getAllMenus($component, 'backend');
			return $xmlmenus;
		}


		/*******************/
		/* GET ACTIVE MENU */
		/*******************/
		private function getActiveMenu() {
			$eURI = eFactory::getURI();

			$component = $eURI->getComponent();
			if ($component == 'content') { return 'content'; }
			if ($component == 'emenu') { return 'menu'; }
			if ($component == 'user') { return 'user'; }
			if ($component == 'extmanager') { return 'extensions'; }
			$uristr = $eURI->getElxisUri();
			if (($uristr == 'sys/elxis.html') || ($uristr == 'sys/php.html')) { return 'system'; }
			if ($uristr == '') { return 'home'; }
			if (in_array($component, array('cpanel', 'emedia', 'etranslator'))) { return 'site'; }
			return $component;
		}


		/*****************/
		/* POPULATE MENU */
		/*****************/
		private function populate($items) {
			$elxis = eFactory:: getElxis();
			$eDoc = eFactory::getDocument();

			if (!$items) { return; }

			$eDoc->addFontAwesome(true);
			$eDoc->addFontElxis();
			$eDoc->addScriptLink($elxis->secureBase().'/modules/mod_adminmenu/js/adminmenu.js');

			echo '<ul id="amenu_menu" class="amenu_menu">'."\n";
			foreach ($items as $menugroup => $item) {
				if ($item->separator === true) {
					echo '<li><hr class="amenu_separator" /></li>'."\n";
				} else if (count($item->children) > 0) {
					$addonclass = '';
					if ($menugroup == $this->activemenu) { $addonclass = ' amenu_submenuexp'; }

					echo '<li><a href="javascript:void(null);" onclick="modAMenuToggleSubmenu('.$item->idx.');">';
					if ($item->icon != '') { echo '<div class="amenu_icon"><i class="'.$item->icon.'"></i></div>'; }
					echo '<div class="amenu_title amenu_down" id="amenu_title_'.$item->idx.'">'.$item->title.'</div>';
					echo '</a></li>'."\n";

					echo '<ul class="amenu_submenu'.$addonclass.'" id="amenu_submenu'.$item->idx.'">'."\n";
					foreach ($item->children as $child) {
						if ($child->separator === true) {
							echo '<li><div class="amenu_subbox"><hr class="amenu_separator" /></div></li>'."\n";
						} else if (count($child->children) > 0) {
							echo '<li><a href="javascript:void(null);">';
							if ($item->icon != '') { echo '<div class="amenu_subicon"><i class="'.$item->icon.'"></i></div>'; }
							echo '<div class="amenu_subtitle">'.$child->title.' - MAX LEVEL!</div>';
							echo '</a></li>'."\n";
						} else {
							$target_str = ($child->target != '') ? ' target="'.$child->target.'"' : '';
							if ($child->icon != '') {
								echo '<li><a href="'.$child->link.'"'.$target_str.'>';
								echo '<div class="amenu_subicon"><i class="'.$child->icon.'"></i></div>';
							} else {
								echo '<li><a href="'.$child->link.'"'.$target_str.' class="amenu_subnoicon">';
							}
							echo '<div class="amenu_subtitle">'.$child->title.'</div>';
							echo '</a></li>'."\n";
						}
					}
					echo "</ul>\n";
				} else {
					$class_str = '';
					if ($menugroup == $this->activemenu) { $class_str = ' class="amenu_active"'; }

					$target_str = ($item->target != '') ? ' target="'.$item->target.'"' : '';
					echo '<li'.$class_str.'><a href="'.$item->link.'"'.$target_str.'>';
					if ($item->icon != '') { echo '<div class="amenu_icon"><i class="'.$item->icon.'"></i></div>'; }
					echo '<div class="amenu_title" id="amenu_title_'.$item->idx.'">'.$item->title.'</div>';
					echo '</a></li>'."\n";
				}
			}
			echo "</ul>\n";
		}


		/********************/
		/* RUN FOREST, RUN! */
		/********************/
		public function run() {
			if (!defined('ELXIS_ADMIN')) {
				echo '<div class="elx5_warning">'.eFactory::getLang()->get('MOD_AVAILABLE_ADMIN')."</div>\n";
				return;
			}

			if (ELXIS_INNER == 1) { return; }

			$this->collect();
			$this->populate($this->items);
		}

	}
}

$modamenu = new modadminMenu();
$modamenu->run();
unset($modamenu);

?>