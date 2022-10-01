-- MySQL Administrator dump 1.4
--
-- Last Update: 15 December 2020 by datahell ( https://www.elxis.org )
-- ------------------------------------------------------
-- Server version	5.0.67-community-nt


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


DROP TABLE IF EXISTS `#__acl`;
CREATE TABLE  `#__acl` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `category` varchar(60) default NULL,
  `element` varchar(60) default NULL,
  `identity` int(10) unsigned NOT NULL default '0',
  `action` varchar(60) default NULL,
  `minlevel` int(11) NOT NULL default '-1',
  `gid` int(10) unsigned NOT NULL default '0',
  `uid` int(10) unsigned NOT NULL default '0',
  `aclvalue` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `idx_ctg_elem` (`category`,`element`)
) ENGINE=InnoDB AUTO_INCREMENT=125 DEFAULT CHARSET=utf8;

/*!40000 ALTER TABLE `#__acl` DISABLE KEYS */;
INSERT INTO `#__acl` (`id`,`category`,`element`,`identity`,`action`,`minlevel`,`gid`,`uid`,`aclvalue`) VALUES 
 (1, 'module', 'mod_language', 1, 'view', 0, 0, 0, 1),
 (2, 'module', 'mod_language', 1, 'manage', 70, 0, 0, 1),
 (3, 'module', 'mod_login', 2, 'view', 0, 0, 0, 1),
 (4, 'module', 'mod_login', 2,'manage', 70, 0, 0, 1),
 (5, 'module', 'mod_menu', 3, 'view', 0, 0, 0, 1),
 (6, 'module', 'mod_menu', 3, 'manage', 70, 0, 0, 1),
 (7, 'com_user', 'memberslist', 0, 'view', 2, 0, 0, 1),
 (8, 'com_user', 'profile', 0, 'view', 2, 0, 0, 2),
 (9, 'com_user', 'profile', 0, 'viewemail', 2, 0, 0, 2),
 (10, 'com_user', 'profile', 0, 'viewphone', 2, 0, 0, 2),
 (11, 'com_user', 'profile', 0, 'viewmobile', 2, 0, 0, 2),
 (12, 'com_user', 'profile', 0, 'viewwebsite', 2, 0, 0, 2),
 (13, 'com_user', 'profile', 0, 'viewaddress', 2, 0, 0, 2),
 (14, 'com_user', 'profile', 0, 'viewgender', 2, 0, 0, 2),
 (15, 'com_user', 'profile', 0, 'viewage', 2, 0, 0, 2),
 (16, 'com_user', 'profile', 0, 'edit', 2, 0, 0, 1),
 (17, 'com_user', 'profile', 0, 'edit', 70, 0, 0, 2),
 (18, 'com_user', 'profile', 0, 'block', 70, 0, 0, 1),
 (19, 'com_user', 'profile', 0, 'delete', 2, 0, 0, 1),
 (20, 'com_user', 'profile', 0, 'delete', -1, 1, 0, 2),
 (21, 'com_user', 'profile', 0, 'uploadavatar', 2, 0, 0, 1),
 (22, 'com_content', 'comments', 0, 'post', 2, 0, 0, 1),
 (23, 'com_content', 'comments', 0, 'publish', 70, 0, 0, 1),
 (24, 'com_content', 'comments', 0, 'publish', 70, 0, 0, 2),
 (25, 'com_content', 'comments', 0, 'delete', 70, 0, 0, 2),
 (26, 'module', 'mod_search', 4, 'view', 0, 0, 0, 1),
 (27, 'module', 'mod_search', 4, 'manage', 70, 0, 0, 1),
 (28, 'module', 'mod_articles', 5, 'view', 0, 0, 0, 1),
 (29, 'module', 'mod_articles', 5, 'manage', 70, 0, 0, 1),
 (30, 'module', 'mod_categories', 6, 'view', 0, 0, 0, 1),
 (31, 'module', 'mod_categories', 6, 'manage', 70, 0, 0, 1),
 (32, 'administration', 'interface', 0, 'login', 70, 0, 0, 1),
 (33, 'com_cpanel', 'settings', 0, 'edit', -1, 1, 0, 1),
 (34, 'com_cpanel', 'backup', 0, 'edit', -1, 1, 0, 1),
 (35, 'module', 'mod_comments', 7, 'view', 0, 0, 0, 1),
 (36, 'module', 'mod_comments', 7, 'manage', 70, 0, 0, 1),
 (37, 'module', 'mod_whosonline', 8, 'view', 0, 0, 0, 1),
 (38, 'module', 'mod_whosonline', 8, 'manage', 70, 0, 0, 1),
 (39, 'com_cpanel', 'routes', 0, 'manage', 70, 0, 0, 1),
 (40, 'component', 'com_content', 0, 'view', 0, 0, 0, 1),
 (41, 'component', 'com_content', 0, 'manage', 70, 0, 0, 1),
 (42, 'component', 'com_user', 0, 'view', 0, 0, 0, 1),
 (43, 'component', 'com_user', 0, 'manage', 70, 0, 0, 1),
 (44, 'component', 'com_search', 0, 'view', 0, 0, 0, 1),
 (45, 'component', 'com_search', 0, 'manage', 70, 0, 0, 1),
 (46, 'component', 'com_cpanel', 0, 'view', -1, 1, 0, 1),
 (47, 'component', 'com_cpanel', 0, 'manage', 70, 0, 0, 1),
 (48, 'module', 'mod_adminmessages', 26, 'view', 70, 0, 0, 1),
 (49, 'module', 'mod_adminmessages', 26, 'manage', 70, 0, 0, 1),
 (50, 'component', 'com_emedia', 0, 'view', -1, 1, 0, 1),
 (51, 'component', 'com_emedia', 0, 'manage', 70, 0, 0, 1),
 (52, 'component', 'com_emenu', 0, 'view', -1, 1, 0, 1),
 (53, 'component', 'com_emenu', 0, 'manage', 70, 0, 0, 1),
 (54, 'com_emenu', 'menu', 0, 'add', 70, 0, 0, 1),
 (55, 'com_emenu', 'menu', 0, 'edit', 70, 0, 0, 1),
 (56, 'com_emenu', 'menu', 0, 'delete', 70, 0, 0, 1),
 (57, 'component', 'com_wrapper', 0, 'view', 0, 0, 0, 1),
 (58, 'component', 'com_wrapper', 0, 'manage', 70, 0, 0, 1),
 (59, 'com_content', 'category', 0, 'add', 70, 0, 0, 1),
 (60, 'com_content', 'category', 0, 'edit', 70, 0, 0, 1),
 (61, 'com_content', 'category', 0, 'delete', 70, 0, 0, 1),
 (62, 'com_content', 'category', 0, 'publish', 70, 0, 0, 1),
 (63, 'com_content', 'article', 0, 'add', 70, 0, 0, 2),
 (64, 'com_content', 'article', 0, 'edit', 70, 0, 0, 2),
 (65, 'com_content', 'article', 0, 'delete', 70, 0, 0, 2),
 (66, 'com_content', 'article', 0, 'publish', 70, 0, 0, 2),
 (67, 'com_user', 'groups', 0, 'manage', -1, 1, 0, 1),
 (68, 'com_user', 'acl', 0, 'manage', -1, 1, 0, 1),
 (69, 'component', 'com_extmanager', 0, 'view', -1, 1, 0, 1),
 (70, 'component', 'com_extmanager', 0, 'manage', 70, 0, 0, 1),
 (71, 'com_extmanager', 'components', 0, 'edit', 70, 0, 0, 1),
 (72, 'com_extmanager', 'modules', 0, 'edit', 70, 0, 0, 1),
 (73, 'com_extmanager', 'templates', 0, 'edit', 70, 0, 0, 1),
 (74, 'com_extmanager', 'engines', 0, 'edit', 70, 0, 0, 1),
 (75, 'com_extmanager', 'components', 0, 'install', -1, 1, 0, 1),
 (76, 'com_extmanager', 'modules', 0, 'install', -1, 1, 0, 1),
 (77, 'com_extmanager', 'templates', 0, 'install', -1, 1, 0, 1),
 (78, 'com_extmanager', 'engines', 0, 'install', -1, 1, 0, 1),
 (79, 'com_cpanel', 'logs', 0, 'manage', -1, 1, 0, 1),
 (80, 'component', 'com_etranslator', 0, 'view', -1, 1, 0, 1),
 (81, 'component', 'com_etranslator', 0, 'manage', 70, 0, 0, 1),
 (82, 'module', 'mod_adminmenu', 9, 'view', 70, 0, 0, 1),
 (83, 'module', 'mod_adminmenu', 9, 'manage', 70, 0, 0, 1),
 (84, 'module', 'mod_adminprofile', 10, 'view', 70, 0, 0, 1),
 (85, 'module', 'mod_adminprofile', 10, 'manage', 70, 0, 0, 1),
 (86, 'module', 'mod_adminsearch', 11, 'view', 70, 0, 0, 1),
 (87, 'module', 'mod_adminsearch', 11, 'manage', 70, 0, 0, 1),
 (88, 'module', 'mod_adminlang', 12, 'view', 70, 0, 0, 1),
 (89, 'module', 'mod_adminlang', 12, 'manage', 70, 0, 0, 1),
 (90, 'com_content', 'frontpage', 0, 'edit', 70, 0, 0, 1),
 (91, 'com_cpanel', 'multisites', 0, 'edit', -1, 1, 0, 1),
 (92, 'module', 'mod_opensearch', 13, 'view', 0, 0, 0, 1),
 (93, 'module', 'mod_opensearch', 13, 'manage', 70, 0, 0, 1),
 (94, 'com_extmanager', 'auth', 0, 'edit', 70, 0, 0, 1),
 (95, 'com_extmanager', 'auth', 0, 'install', -1, 1, 0, 1),
 (96, 'module', 'mod_adminusers', 14, 'view', 70, 0, 0, 1),
 (97, 'module', 'mod_adminusers', 14, 'manage', 70, 0, 0, 1),
 (98, 'module', 'mod_adminarticles', 15, 'view', 70, 0, 0, 1),
 (99, 'module', 'mod_adminarticles', 15, 'manage', 70, 0, 0, 1),
 (100, 'module', 'mod_adminstats', 16, 'view', 70, 0, 0, 1),
 (101, 'module', 'mod_adminstats', 16, 'manage', 70, 0, 0, 1),
 (102, 'com_cpanel', 'statistics', 0, 'view', 70, 0, 0, 1),
 (103, 'com_emedia', 'files', 0, 'upload', 70, 0, 0, 1),
 (104, 'com_emedia', 'files', 0, 'edit', 70, 0, 0, 1),
 (105, 'com_extmanager', 'plugins', 0, 'edit', 70, 0, 0, 1),
 (106, 'com_extmanager', 'plugins', 0, 'install', -1, 1, 0, 1),
 (107, 'module', 'mod_iosslider', 17, 'view', 0, 0, 0, 1),
 (108, 'module', 'mod_iosslider', 17, 'manage', 70, 0, 0, 1),
 (109, 'module', 'mod_gallery', 18, 'view', 0, 0, 0, 1),
 (110, 'module', 'mod_gallery', 18, 'manage', 70, 0, 0, 1),
 (111, 'module', 'mod_ads', 19, 'view', 0, 0, 0, 1),
 (112, 'module', 'mod_ads', 19, 'manage', 70, 0, 0, 1),
 (113, 'module', 'mod_articles', 20, 'view', 0, 0, 0, 1),
 (114, 'module', 'mod_articles', 20, 'manage', 70, 0, 0, 1),
 (115, 'module', 'mod_content', 21, 'view', 0, 0, 0, 1),
 (116, 'module', 'mod_content', 21, 'manage', 70, 0, 0, 1),
 (117, 'module', 'mod_menu', 22, 'view', 0, 0, 0, 1),
 (118, 'module', 'mod_menu', 22, 'manage', 70, 0, 0, 1),
 (119, 'module', 'mod_menu', 23, 'view', 0, 0, 0, 1),
 (120, 'module', 'mod_menu', 23, 'manage', 70, 0, 0, 1),
 (121, 'com_cpanel', 'cache', 0, 'manage', 70, 0, 0, 1),
 (122, 'component', 'com_etranslator', 0, 'api', -1, 1, 0, 1),
 (123, 'module', 'mod_content', 25, 'view', 0, 0, 0, 1),
 (124, 'module', 'mod_content', 25, 'manage', 70, 0, 0, 1);
/*!40000 ALTER TABLE `#__acl` ENABLE KEYS */;

--
-- Definition of table `#__auth`
--

DROP TABLE IF EXISTS `#__authentication`;
CREATE TABLE  `#__authentication` (
  `id` int(10) NOT NULL auto_increment,
  `title` varchar(255) default NULL,
  `auth` varchar(100) default NULL,
  `ordering` int(10) unsigned NOT NULL default '0',
  `published` tinyint(2) unsigned NOT NULL default '0',
  `iscore` tinyint(2) unsigned NOT NULL default '0',
  `params` text,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;


/*!40000 ALTER TABLE `#__authentication` DISABLE KEYS */;
INSERT INTO `#__authentication` (`id`,`title`,`auth`,`ordering`,`published`,`iscore`,`params`) VALUES 
 (1, 'Elxis', 'elxis', 1, 1, 1, NULL),
 (2, 'GMail', 'gmail', 2, 0, 1, NULL),
 (3, 'LDAP', 'ldap', 3, 0, 1, NULL),
 (4, 'Twitter', 'twitter', 4, 0, 1, NULL),
 (5, 'OpenID', 'openid', 5, 0, 1, NULL);
/*!40000 ALTER TABLE `#__authentication` ENABLE KEYS */;

--
-- Definition of table `#__bookmarks`
--

DROP TABLE IF EXISTS `#__bookmarks`;
CREATE TABLE  `#__bookmarks` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `uid` int(10) unsigned NOT NULL default '0',
  `cid` int(10) unsigned NOT NULL default '0',
  `created` datetime NOT NULL default '1970-01-01 00:00:00',
  `reminderdate` datetime NOT NULL default '1970-01-01 00:00:00',
  `remindersent` tinyint(2) unsigned NOT NULL default '0',
  `title` varchar(255) default NULL,
  `link` varchar(255) default NULL,
  `note` text,
  PRIMARY KEY  (`id`),
  KEY `idx_bkuid` USING BTREE (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


--
-- Definition of table `#__captcha`
--

DROP TABLE IF EXISTS `#__captcha`;
CREATE TABLE  `#__captcha` (
  `cid` int(10) unsigned NOT NULL auto_increment,
  `ckey` varchar(80) default NULL,
  `elements` varchar(255) default NULL,
  `keytime` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`cid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


--
-- Definition of table `#__categories`
--

DROP TABLE IF EXISTS `#__categories`;
CREATE TABLE `#__categories` (
  `catid` int(11) NOT NULL auto_increment,
  `parent_id` int(11) NOT NULL default '0',
  `title` varchar(255) default NULL,
  `seotitle` varchar(255) default NULL,
  `seolink` varchar(255) default NULL,
  `published` tinyint(4) NOT NULL default '0',
  `ordering` int(11) NOT NULL default '0',
  `image` varchar(255) default NULL,
  `description` text,
  `params` text,
  `alevel` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  USING BTREE (`catid`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `#__categories`
--

/*!40000 ALTER TABLE `#__categories` DISABLE KEYS */;
INSERT INTO `#__categories` (`catid`, `parent_id`, `title`, `seotitle`, `seolink`, `published`, `ordering`, `image`, `description`, `params`, `alevel`) VALUES
(1, 0, 'Amazing discoveries', 'amazing-discoveries', 'amazing-discoveries/', 1, 1, NULL, '', 'ctg_show=2\nctg_layout=-1\nctg_print=1\nctg_ordering=\nctg_img_empty=-1\nctg_mods_pos=category\nctg_pagination=1\nctg_nextpages_style=0\nctg_subcategories=-1\nctg_subcategories_cols=-1\nctg_featured_num=1\nctg_featured_img=2\nctg_featured_dateauthor=-1\nctg_featured_more=-1\nctg_short_num=4\nctg_short_cols=2\nctg_short_img=-1\nctg_short_dateauthor=-1\nctg_short_text=220\nctg_short_more=-1\nctg_links_num=10\nctg_links_cols=1\nctg_links_header=1\nctg_links_dateauthor=1\ncomments=1', 0),
(2, 0, 'Elxis', 'elxis', 'elxis/', 1, 2, NULL, '', 'ctg_show=-1\nctg_layout=-1\nctg_print=-1\nctg_ordering=\nctg_img_empty=-1\nctg_mods_pos=_global_\nctg_pagination=-1\nctg_nextpages_style=-1\nctg_subcategories=-1\nctg_subcategories_cols=-1\nctg_featured_num=0\nctg_featured_img=-1\nctg_featured_dateauthor=-1\nctg_featured_more=-1\nctg_short_num=10\nctg_short_cols=2\nctg_short_img=4\nctg_short_dateauthor=-1\nctg_short_text=1000\nctg_short_more=-1\nctg_links_num=0\nctg_links_cols=1\nctg_links_header=-1\nctg_links_dateauthor=-1', 0);
/*!40000 ALTER TABLE `#__categories` ENABLE KEYS */;


DROP TABLE IF EXISTS `#__comments`;
CREATE TABLE  `#__comments` (
  `id` int(11) NOT NULL auto_increment,
  `element` varchar(120) default NULL,
  `elid` int(11) NOT NULL default '0',
  `message` text,
  `created` datetime NOT NULL default '1970-01-01 00:00:00',
  `uid` int(10) unsigned NOT NULL default '0',
  `author` varchar(120) default NULL,
  `email` varchar(120) default NULL,
  `published` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `idx_elid` (`elid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
--
-- Definition of table `#__components`
--

DROP TABLE IF EXISTS `#__components`;
CREATE TABLE `#__components` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(120) default NULL,
  `component` varchar(60) default NULL,
  `route` varchar(60) default NULL,
  `iscore` tinyint(3) NOT NULL default '0',
  `params` text,
  PRIMARY KEY  (`id`),
  KEY `idx_component` (`component`),
  KEY `idx_route` USING BTREE (`route`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `#__components`
--

/*!40000 ALTER TABLE `#__components` DISABLE KEYS */;
INSERT INTO `#__components` (`id`,`name`,`component`,`route`,`iscore`,`params`) VALUES 
 (1,'User','com_user',NULL,1,'members_firstname=0\nmembers_lastname=0\nmembers_uname=1\nmembers_groupname=1\nmembers_preflang=1\nmembers_country=0\nmembers_website=0\nmembers_gender=0\nmembers_registerdate=1\nmembers_lastvisitdate=1\ngravatar=0\nprofile_avatar_width=80\nprofile_twitter=1'),
 (2,'Content','com_content',NULL,1,'popup_window=1\nlive_bookmarks=rss\nfeed_items=10\nfeed_cache=6\nimg_thumb_width=120\nimg_thumb_height=0\nimg_medium_width=320\nimg_medium_height=0\ncomments_src=0\nctg_img_empty=1\nctg_layout=0\nctg_show=2\nctg_subcategories=2\nctg_subcategories_cols=2\nctg_ordering=cd\nctg_print=0\nctg_featured_num=1\nctg_featured_img=2\nctg_featured_dateauthor=6\nctg_short_num=4\nctg_short_cols=1\nctg_short_img=2\nctg_short_dateauthor=6\nctg_short_text=220\nctg_links_num=5\nctg_links_cols=1\nctg_links_header=0\nctg_links_dateauthor=0\nctg_pagination=1\nctg_nextpages_style=1\nctg_mods_pos=category\nart_dateauthor=6\nart_dateauthor_pos=0\nart_img=2\nart_print=1\nart_email=1\nart_twitter=1\nart_facebook=1\nart_tags=1\nart_hits=1\nart_chain=2'),
 (3,'Search','com_search',NULL,1,NULL),
 (4,'CPanel','com_cpanel',NULL,1,NULL),
 (5,'Media manager','com_emedia',NULL,1,NULL),
 (6,'Menu manager','com_emenu',NULL,1,NULL),
 (7,'Wrapper','com_wrapper',NULL,1,NULL),
 (8,'Extensions manager','com_extmanager',NULL,1,NULL),
 (9,'Translator','com_etranslator',NULL,1,NULL);
/*!40000 ALTER TABLE `#__components` ENABLE KEYS */;


--
-- Definition of table `#__content`
--

DROP TABLE IF EXISTS `#__content`;
CREATE TABLE `#__content` (
  `id` int(11) NOT NULL auto_increment,
  `catid` int(10) unsigned NOT NULL default '0',
  `title` varchar(255) default NULL,
  `seotitle` varchar(255) default NULL,
  `subtitle` varchar(255) default NULL,
  `introtext` text,
  `maintext` longtext,
  `image` varchar(255) default NULL,
  `caption` varchar(255) default NULL,
  `published` tinyint(4) unsigned NOT NULL default '0',
  `metakeys` varchar(255) default NULL,
  `created` datetime NOT NULL default '1970-01-01 00:00:00',
  `created_by` int(10) unsigned NOT NULL default '0',
  `created_by_name` varchar(120) default NULL,
  `modified` datetime NOT NULL default '1970-01-01 00:00:00',
  `modified_by` int(10) unsigned NOT NULL default '0',
  `modified_by_name` varchar(120) default NULL,
  `ordering` int(10) unsigned NOT NULL default '0',
  `hits` int(10) unsigned NOT NULL default '0',
  `alevel` int(10) unsigned NOT NULL default '0',
  `params` text,
  `pubdate` datetime NOT NULL default '2014-01-01 00:00:00',
  `unpubdate` datetime NOT NULL default '2060-01-01 00:00:00',
  `important` tinyint(4) unsigned NOT NULL default '0',
  `relkey` varchar(120) default NULL,
  PRIMARY KEY  (`id`),
  KEY `idx_catid` USING BTREE (`catid`),
  KEY `idx_seotitle` (`seotitle`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `#__content`
--

/*!40000 ALTER TABLE `#__content` DISABLE KEYS */;

INSERT INTO `#__content` (`id`, `catid`, `title`, `seotitle`, `subtitle`, `introtext`, `maintext`, `image`, `caption`, `published`, `metakeys`, `created`, `created_by`, `created_by_name`, `modified`, `modified_by`, `modified_by_name`, `ordering`, `hits`, `alevel`, `params`, `pubdate`, `unpubdate`, `important`, `relkey`) VALUES
(1, 1, 'The Antikythera mechanism', 'antikythera-mechanism', 'An ancient Greek analogue computer used to predict astronomical positions and eclipses for calendar and astrological purposes decades in advance.', '<p>The artifact was retrieved from the sea in 1901, and identified on 17 May 1902 as containing a gear wheel by archaeologist Valerios Stais, among wreckage retrieved from a wreck off the coast of the Greek island Antikythera. The instrument is believed to have been designed and constructed by Greek scientists and has been variously dated to about 87 BC, or between 150 and 100 BC, or to 205 BC, or to within a generation before the shipwreck, which has been dated to approximately 70-60 BC.</p>', '<p>The device, housed in the remains of a 34 cm × 18 cm × 9 cm wooden box, was found as one lump, later separated into three main fragments which are now divided into 82 separate fragments after conservation works. Four of these fragments contain gears, while inscriptions are found on many others. The largest gear is approximately 14 centimetres in diameter and originally had 223 teeth.</p>\r\n\r\n<p>It is a complex clockwork mechanism composed of at least 30 meshing bronze gears. A team led by Mike Edmunds and Tony Freeth at Cardiff University used modern computer x-ray tomography and high resolution surface scanning to image inside fragments of the crust-encased mechanism and read the faintest inscriptions that once covered the outer casing of the machine.</p>\r\n\r\n<p>Detailed imaging of the mechanism suggests that it had 37 gear wheels enabling it to follow the movements of the Moon and the Sun through the zodiac, to predict eclipses and even to model the irregular orbit of the Moon, where the Moon\'s velocity is higher in its perigee than in its apogee. This motion was studied in the 2nd century BC by astronomer Hipparchus of Rhodes, and it is speculated that he may have been consulted in the machine\'s construction.</p>\r\n\r\n<p>The knowledge of this technology was lost at some point in antiquity, and technological works approaching its complexity and workmanship did not appear again until the development of mechanical astronomical clocks in Europe in the fourteenth century. All known fragments of the Antikythera mechanism are kept at the National Archaeological Museum in Athens, along with a number of artistic reconstructions/replicas of how the mechanism may have looked and worked.</p>', 'media/images/articles/antikythira.jpg', 'Officially the oldest computer ever', 1, 'antikythera,computer,astronomy', '2019-04-20 17:05:00', 1, 'John Doe', '1970-01-01 00:00:00', 0, NULL, 1, 0, 0, 'art_dateauthor=-1\nart_dateauthor_pos=-1\nart_img=-1\nart_print=-1\nart_email=-1\nart_twitter=-1\nart_facebook=-1\nart_hits=-1\nart_comments=-1\nart_tags=-1\nart_chain=-1\nart_related=-1', '2014-01-01 00:00:00', '2060-01-01 00:00:00', 0, 'discoveries'),
(2, 1, 'The Underwater City of Yonaguni', 'underwater-city-yonaguni', 'A submerged rock formation off the coast of Yonaguni, the southernmost of the Ryukyu Islands, in Japan', '<p>It was actually by accident that Kihachiro Aratake made the discovery of what scientists are calling, <em>the archeological find of the century</em>. He came across the mysterious find while diving off the coast of Yonaguni Jima, an island located 67 miles from Taiwan, in search of a new spot to view hammerhead sharks from.</p>', '<p>However, he inadvertently strayed outside of the designated safety zone and was taken by surprise when he saw what appeared to be a massive stone structure sitting on the bottom of the ocean floor. He swam closer to investigate his discovery and was in awe at how colossal it was. The structure was approximately 240 feet long and 90 feet wide, with an arranged pattern of monolithic blocks, black and gaunt. Due to the heavy encrustation of coral, it was difficult to determine exactly what it was. Curious, he encircled it, again and again, taking photographs before returning to shore.</p>\r\n\r\n<p>The next morning, he awoke to find his pictures plastered on the front page of every major newspaper in Japan. News of his discovery traveled quickly, and instantly attracted diving archeologists, news media people, and curious non professionals to the dive site. Controversy grew at the same quick pace as researchers and scholars debated over its origin. Were these structures man-made or simply formations caused by natural elements?</p>\r\n\r\n<p>Since then, Professor Masaaki Kimura has made more than 100 dives and has extensively mapped out the area. He believes he has stumbled across the remains of a 5,000 year old city beneath the ocean. A total of ten structures were discovered at Yonaguni, and another five off the main island of Okinawa. Combined, the ruins cover an area measuring 984 feet by 492 feet.</p>\r\n\r\n<p>Among the Yonaguni Jima ruins Professor Masaaki discovered – a stepped, stone structure measuring 82 feet high that dates back to 8,000 B.C. and closely resembles a stepped pyramid. It is also referred to as the world\'s oldest building. It\'s design has been compared to various pyramids found in the America’s and could prove to be one of the most important archeological discovery of the last fifty years.</p>', 'media/images/articles/underwater-city.jpg', 'The Underwater City of Yonaguni', 1, 'yonaguni,japan,underwater,ruins', '2019-04-20 17:05:00', 1, 'John Doe', '1970-01-01 00:00:00', 0, NULL, 2, 0, 0, 'art_dateauthor=-1\nart_dateauthor_pos=-1\nart_img=-1\nart_print=-1\nart_email=-1\nart_twitter=-1\nart_facebook=-1\nart_hits=-1\nart_comments=-1\nart_tags=-1\nart_chain=-1\nart_related=-1', '2014-01-01 00:00:00', '2060-01-01 00:00:00', 0, 'discoveries'),
(3, 1, 'The giant stone spheres of Costa Rica', 'costa-rica-stone-spheres', 'The stone spheres of Costa Rica are an assortment of over three hundred petrospheres in Costa Rica, located on the Diquís Delta and on Isla del Caño.', '<p>Found deep in the jungles of Costa Rica in the 1930\'s were 300 nearly perfectly round stone balls. They varied in size from a few inches in diameter, to seven feet across and weighing 16 tons. Scientists aren\'t sure who made them, how old they are or what purpose they might have had.</p>', '<p>In the early 1930\'s the United Fruit Company started searching for new space to plant their banana trees because their plantations on the eastern side of Costa Rica, in South America, were threatened by disease. On the western side, however, not too far from the Pacific Ocean they found a promising section of land in the Diquis Valley.</p>\r\n\r\n<p>When they started clearing the land, however, workers found something strange: stone balls. They ranged in size from a few inches to six or seven feet in diameter. The most striking thing about these rock spheres was that many of them appeared to be perfectly round and very, very smooth. Undoubtedly they were manmade.</p>\r\n\r\n<p>As far as the United Fruit Company was concerned the strange objects were just in the way of their plantation. Workers rolled them off to the sides of the fields by hand or pushed them using bulldozers. Many were eventually transported to homes or businesses to be used as lawn ornaments. Before authorities could intervene a rumor that some of the stones contained gold in their cores caused treasure hunters to drill holes in them, load them with dynamite and blow them pieces hoping to get rich. The only thing they found inside the stone spheres, however, was more stone. </p>', 'media/images/articles/costa-rica.jpg', 'Stone spheres of Costa Rica', 1, 'stone spheres,costa rica,united fruit company', '2019-04-20 17:05:00', 1, 'John Doe', '1970-01-01 00:00:00', 0, NULL, 3, 0, 0, 'art_dateauthor=-1\nart_dateauthor_pos=-1\nart_img=-1\nart_print=-1\nart_email=-1\nart_twitter=-1\nart_facebook=-1\nart_hits=-1\nart_comments=-1\nart_tags=-1\nart_chain=-1\nart_related=-1', '2014-01-01 00:00:00', '2060-01-01 00:00:00', 0, 'discoveries'),
(4, 1, 'The Voynich manuscript', 'voynich-manuscript', 'An illustrated codex hand-written in an unknown writing system.', '<p>Arguably the most mysterious manuscript that has ever been discovered, the Voynich Manuscript is an astonishing artifact whose origins and language are completely unknown. The manuscript is full of plant life, strange symbols, and diagrams, and it is written in a mysterious language that can’t be traced back to any known civilization.</p>', '<p>Wilfrid Voynich was an antiquarian book dealer who traveled the world searching for new additions to his collection. In 1912, he visited the Jesuit Villa Mondragone in Frascati, near Rome, where in a trunk he unearthed a strange medieval manuscript. The heavy tome was handwritten in an unknown script, and lavishly illustrated with colorful drawings of plants, astrological diagrams, and nude women. For the past 100 years, numerous people have attempted to decode the writing system in the Voynich Manuscript, which been called the world\'s most mysterious book, and the book that can’t be read.</p>\r\n\r\n<p>The Voynich Manuscript is the subject of much speculation and disagreement and there are numerous theories about its possible author, or authors. Voynich discovered a letter enclosed with the book that revealed it had once been in the possession of Rudolph II, the Holy Roman Emperor. The letter speculated that Franciscan Friar Roger Bacon (1214-1294) wrote the book. The manuscript now bears Voynich’s name, but he became so convinced by this theory that he referred to it as the Roger Bacon Manuscript. A juvenile Leonardo da Vinci has been posited as the artist because the illustrations are similar to his style, but have a child-like quality to them. Sixteenth century mystics John Dee and Edward Kelley, who transcribed the famous angelic Enochian script, have also been credited as authors. Disproving all of this conjecture, recent radiocarbon tests date the vellum to the fifteenth century, specifically between the years 1404-1438.</p>\r\n\r\n<p>We still don’t know the book’s purpose although the illustrations hold some clues. They suggest that the book is divided into sections about astrology, cosmology, biology, pharmacology, herbs, and recipes. These topics give the impression that the Voynich Manuscript is a book about botany, folk medicine, or an encyclopedia of medieval science, although some believe it holds secrets of magic and alchemy. The book contains 240 pages, including fold-out multi-part pages, with a drawing on nearly every one of them. The accompanying text was written from left to right by a quill pen using iron gall ink. The author(s) had very careful penmanship, as there are no errors at all, and nor is there any punctuation. While some 35,000 words, 170,000 characters, and 18-22 letters of Voynichese have been identified, we can’t read the book. Yet.</p>', 'media/images/articles/voynich-manuscript2.jpg', 'The Voynich manuscript', 1, 'voynich,manuscript,unknown,strange,illustrations,astrology,biology', '2019-04-20 17:05:00', 1, 'John Doe', '1970-01-01 00:00:00', 0, NULL, 4, 0, 0, 'art_dateauthor=-1\nart_dateauthor_pos=-1\nart_img=-1\nart_print=-1\nart_email=-1\nart_twitter=-1\nart_facebook=-1\nart_hits=-1\nart_comments=-1\nart_tags=-1\nart_chain=-1\nart_related=-1', '2014-01-01 00:00:00', '2060-01-01 00:00:00', 0, 'discoveries'),
(5, 1, 'The unfinished obelisk', 'unfinished-obelisk', 'Larger than any ancient Egyptian obelisk ever erected.', '<p>Its creation was ordered by Hatshepsut (1508–1458 BC), possibly to complement what would later be known as the Lateran Obelisk (which was originally at Karnak, and was later brought to the Lateran Palace in Rome). The unfinished obelisk is nearly one-third larger than any ancient Egyptian obelisk ever erected. If finished it would have measured around 42m and would have weighed nearly 1200 tons, a weight equal to about 200 African elephants.</p>', '<p>The obelisk\'s creators began to carve it directly out of bedrock, but cracks appeared in the granite and the project was abandoned. The bottom side of the obelisk is still attached to the bedrock.</p>\r\n\r\n<p>The unfinished obelisk offers unusual insights into ancient Egyptian stone-working techniques, with marks from workers\' tools still clearly visible as well as ochre-colored lines marking where they were working.</p>\r\n\r\n<p>Besides the unfinished obelisk, an unfinished, partly worked obelisk base was discovered in 2005 at the quarries of Aswan. Also discovered were some rock carvings and remains that may correspond to the site where most of the famous obelisks were worked. All these quarries in Aswan and the unfinished objects are an open-air museum and are officially protected by the Egyptian government as an archeological site. </p>', 'media/images/articles/unfinished-ovelisk.jpg', 'The unfinished ovelisk', 1, 'obelisk,unfinished,egypt,hatshepsut', '2019-04-20 17:05:00', 1, 'John Doe', '1970-01-01 00:00:00', 0, NULL, 5, 0, 0, 'art_dateauthor=-1\nart_dateauthor_pos=-1\nart_img=-1\nart_print=-1\nart_email=-1\nart_twitter=-1\nart_facebook=-1\nart_hits=-1\nart_comments=-1\nart_tags=-1\nart_chain=-1\nart_related=-1', '2014-01-01 00:00:00', '2060-01-01 00:00:00', 0, 'discoveries'),
(6, 2, 'Extensions', 'extensions', 'Elxis extensions extend the built-in functionality.', '<p>Elxis has some global extension types and some others that are component specific. The most important extensions are the <strong>Components</strong>. A component handles the user request and generates the corresponding output which is visible in the center area of the site. Only one component can run in each page. Depending on the user\'s request Elxis decides which component to load. <strong>Modules</strong>, on the other hand are small blocks shown around the component on the left, right, top and bottom areas of the site.</p>', '<p>The complete list of the built-in extension types follows.</p>\r\n<ul class=\"elx_stdul\"><li>Components</li><li>Modules</li><li>Content plugins</li><li>Authentication methods</li><li>Search engines</li><li>Templates</li></ul>\r\n<h3>Content plugins</h3>\r\n<p>Plugins are extensions bind to component <strong>Content </strong>and their scope is to make possible to easily import into standard content items things like image galleries, contact forms and videos. Plugins are based on a find and replace system which replaces small blocks of code into HTML code. To make things even easier Elxis provides you with a guided plugin code generation and import system.</p>\r\n\r\n<h3>Authentication methods</h3>\r\n<p>Elxis accepts user login from external providers such as OpenId, LDAP, GMail and Twitter. Elxis Authentication methods are component <strong>User </strong>specific extensions that provide this extra functionality. You can install new Authentication methods and manage them as any other extension type.</p>\r\n\r\n<h3>Search engines</h3>\r\n<p>People usually search for articles matching some given search criteria. But what about searching other things like video, images or people? Well, Search Engines are exactly for this. These component\'s <strong>Search</strong> extensions allows you to extend search on anything you can imagine.</p>\r\n\r\n<h3>Templates</h3>\r\n<p>Templates handles the structure and the style of the generated pages. The template controls things like the side columns of the site and the position of the modules. There are templates for the site\'s frontend area but also for the back-end area. Templates can also provide the structure and the layout for the <strong>Exit Pages</strong> (error 403, 404, etc).</p>', 'media/images/articles/extensions.jpg', '', 1, 'elxis,extensions,modules,components,plugins,authentication methods,search engines,templates', '2019-04-20 17:05:00', 1, 'John Doe', '1970-01-01 00:00:00', 0, NULL, 4, 0, 0, 'art_dateauthor=-1\nart_dateauthor_pos=-1\nart_img=-1\nart_print=-1\nart_email=-1\nart_twitter=-1\nart_facebook=-1\nart_hits=-1\nart_comments=-1\nart_tags=-1\nart_chain=-1\nart_related=-1', '2014-01-01 00:00:00', '2060-01-01 00:00:00', 0, 'elxis'),
(7, 2, 'FAQs', 'faqs', 'Answers to frequently asked questions regarding Elxis CMS usage.', '<p>Elxis 5.x is a modern powerful content management system having many cool features. Some of them are really unique. We advise you to take your time and explore Elxis. Every day you use Elxis you will discover new ways to do things faster and easier. It is impossible to list all the things you can do with Elxis and even more provide a detailed how-to guide for all of them. In this article we provide answers to most frequently questions people ask us. For anything else please visit the Elxis forums or the online documentation for support.</p>', '<h4 class=\"elx_question\">How do I set the site frontpage?</h4>\r\n<p class=\"elx_answer\">You set the site frontpage from Elxis configuration by providing the Elxis URI to the page you want to be shown in frontpage. By default this is the root of component content (<em>content:/</em>). Component content has a special feature that allows you to generate complex <strong>grid layouts</strong> for the frontpage. On each cell of this grid you can display any number of Elxis <strong>modules</strong>.</p>\r\n\r\n<h4 class=\"elx_question\">Does Elxis support sub-categories?</h4>\r\n<p class=\"elx_answer\">Yes, Elxis supports <strong>sub-categories of any level</strong>. For SEO reasons and easy of access we suggest you to create sub-categories up to the second or third level.</p>\r\n\r\n<h4 class=\"elx_question\">I need a second, or more, site on the same domain. Do I have to re-install Elxis?</h4>\r\n<p class=\"elx_answer\">No, you don\'t. With the Elxis <strong>multi-sites</strong> feature you can have an unlimited number of sub-sites under one mother site. These sites share the same filesystem but have different configuration, data, template, users, etc, making them independent. Although the sites are independent they still share the same filesystem. The Elxis Team recommendation is that the administrators of these sub-sites should be trustful to the admin of the mother site.</p>\r\n\r\n<h4 class=\"elx_question\">How do I configure a page?</h4>\r\n<p class=\"elx_answer\">Till Elxis 2009.x generation the layout and features of a page was controlled by the menu item that linked to that page. This system confused new users and many times lead to issues such as error 404. Since Elxis 4.x this has changed. The layout and features of a page is now controlled by the page it self. Each category or article has a set of <strong>parameters</strong> were you can set options such as the show of article author, hits, print links, if you allow comments, etc. You can also set global options, per category options and per article options. Specific element options overwrite the more generic ones. For instance you may allow comments for all articles in a category but disable commentary for an idividual article.</p>\r\n\r\n<h4 class=\"elx_question\">How do I create a blog?</h4>\r\n<p class=\"elx_answer\">Elxis content categories and articles are multi-functional. You don\'t need a special blog component to have a blog. A standard category can act as a blog by setting it\'s articles to be listed in a blog style. You can also make use of tags, comments, share and social buttons and anything else a blog should have.</p>\r\n\r\n<h4 class=\"elx_question\">I concern about security. How does Elxis goes with it?</h4>\r\n<p class=\"elx_answer\">In the Elxis world security is the first priority. Elxis is shipped with an attack detection and protection system called <strong>Elxis Defender</strong>. Among others, Elxis Defender can block bad requests to your site, detect file system changes and send alert notifications to the site\'s technical manager. There are many other security relared features like the <strong>Security Level</strong> configuration option, the automatic <strong>SSL/TLS switch</strong>, the usage of the PHP\'s native <strong>PDO</strong> library for handling the database which makes impossible SQL injection, the security images (captcha) and the XSS prevention system for the forms, the double authentication check for the administration area, the tight <strong>user access</strong> system, the in-accessible from the web <strong>Elxis repository</strong> (including session storage), the session <strong>encryption</strong> and much more.</p>\r\n\r\n<h4 class=\"elx_question\">Where the administrator folder went?</h4>\r\n<p class=\"elx_answer\">There is no administrator folder in Elxis any more! There is a folder named <em>estia</em> containg a file that initiates the administration user requests but nothing more. Note that you can rename that folder to anything you want.</p><h4 class=\"elx_question\">Can I have content in multiple languages?</h4>\r\n\r\n<p class=\"elx_answer\">Yes, Elxis has <strong>full multilingual support</strong>. Each element (article, category, etc) is initially entered in the site\'s main language. You can after add to the elements unlimited number of translations in different languages.</p>\r\n\r\n<h4 class=\"elx_question\">Can people login without registering?</h4>\r\n<p class=\"elx_answer\">Yes, Elxis supports <strong>external authentication</strong> methods such as Twitter, Gmail, OpenId, Yahoo and LDAP which make possible logging in with your account in any of the supported authentication providers.</p>', 'media/images/articles/faq.jpg', '', 1, 'faqs,frontpage,subcategories,elxis defender,multisites,security,multilingual,translations,openid', '2019-04-20 17:05:00', 1, 'John Doe', '1970-01-01 00:00:00', 0, NULL, 3, 0, 0, 'art_dateauthor=-1\nart_dateauthor_pos=-1\nart_img=-1\nart_print=-1\nart_email=-1\nart_twitter=-1\nart_facebook=-1\nart_hits=-1\nart_comments=-1\nart_tags=-1\nart_chain=-1\nart_related=-1', '2014-01-01 00:00:00', '2060-01-01 00:00:00', 0, 'elxis'),
(8, 0, 'Contact us', 'contact-us', 'We are located in the center of Athens. Feel free to contact us by submiting the contact form.', '<p class=\"elx5_info\">You will find us at Vasilissis Amalias 999, at the center of Athens. You can contact us by phone (+30-000-1234567) or fill-in and submit the contact form below.</p>', '<br /><code>{contact}noone@example.com{/contact}</code>', NULL, '', 1, 'contact,sample company,email,telephone', '2019-04-20 17:05:00', 1, 'John Doe', '1970-01-01 00:00:00', 0, NULL, 5, 0, 0, 'art_dateauthor=0\nart_dateauthor_pos=-1\nart_img=-1\nart_print=0\nart_email=0\nart_hits=0\nart_comments=0\nart_tags=0\nart_chain=0', '2014-01-01 00:00:00', '2060-01-01 00:00:00', 0, NULL),
(9, 0, 'Typography', 'typography', 'Template typography preview.', '<p>This article demonstrates how the current template styles basic HTML elements and Elxis specific CSS classes.</p>', '<h2>Generic typography styles</h2>\r\n<h1>This is an H1 header</h1>\r\n<h2>This is an H2 header</h2>\r\n<h3>This is an H3 header</h3>\r\n<h4>This is an H4 header</h4>\r\n<h5>This is an H5 header</h5>\r\n<p>This is a paragraph containing a <a href=\"http://www.elxis.org\" title=\"elxis cms\" target=\"_blank\">sample link to elxis.org</a> and some <strong>strong text</strong>. \r\nLorem ipsum dolor sit amet, consectetur adipiscing elit. Ut eget mi vitae nunc tincidunt cursus ac at ligula. Aliquam dignissim mi sit amet tortor sodales fermentum. \r\nAenean blandit suscipit erat auctor interdum. Pellentesque varius, lorem quis viverra imperdiet, nunc sem rhoncus ante, non varius justo metus in urna.</p>\r\n\r\n<pre>Preformated text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut eget mi vitae nunc tincidunt cursus ac at ligula. Aliquam dignissim mi sit amet tortor sodales \r\nfermentum.</pre>\r\n\r\n<blockquote>Blockquote text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut eget mi vitae nunc tincidunt cursus ac at ligula. Aliquam dignissim mi sit \r\namet tortor sodales fermentum. Aenean blandit suscipit erat auctor interdum. Pellentesque varius, lorem quis viverra imperdiet, nunc sem rhoncus ante, \r\nnon varius justo metus in urna.</blockquote>\r\n\r\n<h2>Elxis specific typography styles</h2>\r\n<p class=\"elx5_info\">This is an information message (&lt;p class=\"elx5_info\"&gt;text&lt;/p&gt;)</p>\r\n<p class=\"elx5_warning\">This is a warning message (&lt;p class=\"elx5_warning\"&gt;text&lt;/p&gt;)</p>\r\n<p class=\"elx5_error\">This is an error message (&lt;p class=\"elx5_error\"&gt;text&lt;/p&gt;)</p>\r\n<p class=\"elx5_success\">This is a success message (&lt;p class=\"elx5_success\"&gt;text&lt;/p&gt;)</p>\r\n\r\n<div class=\"elx5_sminfo\">This is a small information message (&lt;div class=\"elx5_sminfo\"&gt;text&lt;/div&gt;)</div>\r\n<div class=\"elx5_smwarning\">This is a small warning message (&lt;div class=\"elx5_smwarning\"&gt;text&lt;/div&gt;)</div>\r\n<div class=\"elx5_smerror\">This is a small error message (&lt;div class=\"elx5_smerror\"&gt;text&lt;/div&gt;)</div>\r\n<div class=\"elx5_smsuccess\">This is a small success message (&lt;div class=\"elx5_smsuccess\"&gt;text&lt;/div&gt;)</div>\r\n<br><br>\r\n<div class=\"module\">\r\n<h3>Module title</h3>\r\n<p>Generic module style with outer div wrapper (class module) and module title shown.</p>\r\n</div>\r\n\r\n<ul class=\"elx_stdul\"><li>Unordered items list (class: elx_stdul)</li><li>Unordered item</li><li>Unordered item</li></ul>\r\n<ol class=\"elx_stdol\"><li>Ordered items list (class: elx_stdol)</li><li>Ordered item</li><li>Ordered item</li></ol>\r\n\r\n<h3>Navigation through pages</h3>\r\n\r\n<div class=\"elx5_dspace\">\r\n<ul class=\"elx5_pagination\">\r\n	<li class=\"elx5_pagactive\"><a href=\"javascript:void(null);\" title=\"Page 1\">1</a></li>\r\n	<li><a href=\"#\" title=\"Page 2\">2</a></li>\r\n	<li><a href=\"#\" title=\"Page 3\">3</a></li>\r\n	<li><a href=\"#\" title=\"Page 4\">4</a></li>\r\n</ul>\r\n</div>\r\n\r\n\r\n<div class=\"elx5_artbox elx5_artboxml\" data-featured=\"1\">\r\n<figure class=\"elx5_content_imagebox elx5_content_imageboxml\">\r\n<a href=\"#\" title=\"Sample featured article\"><img src=\"/templates/system/images/nopicture_article.jpg\" alt=\"Officially the oldest computer ever\"></a>\r\n<figcaption>Sample image caption</figcaption>\r\n</figure>\r\n<div class=\"elx5_artbox_inner\">\r\n<h3><a href=\"#\" title=\"Sample featured article\">Sample featured article</a></h3>\r\n<div class=\"elx5_dateauthor\">Last update <time datetime=\"2000-01-10 12:00:32\">Jan 01, 2000 12:00</time> by <a href=\"#\" title=\"Author\">Author</a></div>\r\n<p class=\"elx5_content_subtitle\">Sample featured article sub-title.</p>\r\n<p>Sample featured article. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut eget mi vitae nunc tincidunt cursus ac at ligula. Aliquam dignissim mi sit amet tortor sodales fermentum. Aenean blandit suscipit erat auctor interdum. Pellentesque varius, lorem quis viverra imperdiet, nunc sem rhoncus ante, non varius justo metus in urna. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Sed in nunc mi. Cras quis lectus risus. Nulla non pharetra metus. Ut ut euismod mi. Etiam ut leo id tellus rhoncus convallis sit amet rhoncus ipsum. Sed non ligula nibh.</p>\r\n</div>\r\n</div>\r\n\r\n<div class=\"elx5_artbox elx5_artboxtl\" data-short=\"1\">\r\n<figure class=\"elx5_content_imagebox elx5_content_imageboxtl\">\r\n<a href=\"#\" title=\"Sample short article\"><img src=\"/templates/system/images/nopicture_article.jpg\" alt=\"Sample short article\"></a>\r\n</figure>\r\n<div class=\"elx5_artbox_inner\">\r\n<h3><a href=\"#\" title=\"Sample short article\">Sample short article</a></h3>\r\n<div class=\"elx5_dateauthor\">Last update <time datetime=\"2000-01-10 12:00:15\">Jan 01, 2000</time> by <a href=\"#\" title=\"Author\">Author</a></div>\r\n</div>\r\n<div class=\"elx5_artbox_inner\">\r\n<p>Sample short article. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut eget mi vitae nunc tincidunt cursus ac at ligula. Aliquam dignissim mi sit amet tortor sodales fermentum. Aenean blandit suscipit erat auctor interdum...</p>\r\n<div class=\"clear\"><br></div>\r\n</div>\r\n</div>\r\n\r\n<fieldset class=\"elx5_fieldset\">\r\n	<legend>Sample form</legend>\r\n	<div class=\"elx5_formrow\">\r\n		<label class=\"elx5_label\" for=\"sampletext\">Input text</label>\r\n		<div class=\"elx5_labelside\">\r\n			<input type=\"text\" name=\"sampletext\" id=\"sampletext\" value=\"\" class=\"elx5_text\" placeholder=\"Input text\" maxlength=\"60\" title=\"Input text\">\r\n			<div class=\"elx5_tip\">This is a sample tip message</div>\r\n		</div>\r\n	</div>\r\n	<div class=\"elx5_formrow\">\r\n		<label class=\"elx5_label\" for=\"sampleselect\">Select box</label>\r\n		<div class=\"elx5_labelside\">\r\n			<select name=\"sampleselect\" id=\"sampleselect\" title=\"Select box\" class=\"elx5_select\">\r\n				<option value=\"0\" selected=\"selected\">option 1</option>\r\n				<option value=\"1\">option 2</option>\r\n				<option value=\"2\">option 3</option>\r\n			</select>\r\n		</div>\r\n	</div>\r\n	<div class=\"elx5_formrow\">\r\n		<label class=\"elx5_label\" for=\"sampleitemstatus\">Item status</label>\r\n		<div class=\"elx5_labelside\"><a href=\"javascript:void(null);\" onclick=\"elx5SwitchStatus(\'sampleitemstatus\', this);\" class=\"elx5_itemstatus elx5_itemstatus_red\" data-values=\"0|1|2\" data-labels=\"Inactive|Active|Very active\" data-colors=\"red|lightgreen|green\">Inactive</a></div>\r\n	</div>\r\n	<input type=\"hidden\" name=\"sampleitemstatus\" id=\"sampleitemstatus\" value=\"0\">\r\n	<div class=\"elx5_dspace\"><button type=\"button\" name=\"samplebtn1\" class=\"elx5_btn\">Generic button</button></div>\r\n	<div class=\"elx5_dspace\"><button type=\"button\" name=\"samplebtn1\" class=\"elx5_btn elx5_sucbtn\">Success button</button></div>\r\n	<div class=\"elx5_dspace\"><button type=\"button\" name=\"samplebtn1\" class=\"elx5_btn elx5_warnbtn\">Warning button</button></div>\r\n	<div class=\"elx5_dspace\"><button type=\"button\" name=\"samplebtn1\" class=\"elx5_btn elx5_errorbtn\">Error button</button></div>\r\n	<div class=\"elx5_dspace\"><button type=\"button\" name=\"samplebtn1\" class=\"elx5_btn elx5_notallowedbtn\">Not allowed button</button></div>\r\n</fieldset>', NULL, '', 1, 'typography,stylesheet,style,html elements,elxinfo,elxwarning,headings', '2019-04-20 17:05:00', 1, 'John Doe', '1970-01-01 00:00:00', 0, NULL, 3, 0, 0, 'art_dateauthor=-1\nart_dateauthor_pos=-1\nart_img=-1\nart_print=-1\nart_email=-1\nart_twitter=-1\nart_facebook=-1\nart_hits=-1\nart_comments=-1\nart_tags=0\nart_chain=0\nart_related=-1', '2014-01-01 00:00:00', '2060-01-01 00:00:00', 0, ''),
(10, 0, 'Sample gallery', 'sample-gallery', 'Convert any article to an image gallery.', '<p>You can convert any typical article to an image <strong>gallery</strong> by using the gallery, or other similar, <strong>plugin</strong>. Just insert the plugin code inside the article text area in the exact spot you want the gallery to be displayed and your image gallery is ready!</p>', '<h3>Greek landscapes</h3>\r\n<p>Pictures from Athens, Chania, Chalkidiki, Meteora, Parga, Santorini, Skiathos and more.</p><br />\r\n\r\n{gallery}media/images/sample_gallery/{/gallery}\r\n\r\n<br />\r\n<p class=\"elx5_info\">The code used to generate the above gallery can be shown bellow.<br />\r\n<strong>&#123;gallery&#125;media/images/sample_gallery/&#123;/gallery&#125;</strong></p>', NULL, NULL, 1, 'gallery,plugin,article,sample gallery,greek,landscapes', '2019-04-20 17:05:00', 1, 'John Doe', '1970-01-01 00:00:00', 0, NULL, 4, 0, 0, 'art_dateauthor=-1\nart_dateauthor_pos=-1\nart_img=-1\nart_print=-1\nart_email=-1\nart_hits=-1\nart_comments=0\nart_tags=0\nart_chain=0', '2014-01-01 00:00:00', '2060-01-01 00:00:00', 0, NULL),
(11, 2, 'Features', 'features', 'The most important Elxis features.', '<p>Elxis is a powerful and rich CMS having many of the features you will need for your site built-in. You can extend more Elxis by installing additional <a href=\"#elink:content:elxis/extensions.html\" title=\"Elxis extensions types\">extensions</a>. This article lists the most important Elxis features you will find built-in.</p>', '<ul class=\"elx_stdul\">\r\n	<li>High quality object oriented programming in PHP5/PHP7.</li>\r\n	<li>PDO database layer</li>\r\n	<li>Multilanguage user interface and content.</li>\r\n	<li>Mobile and tablet friendly.</li>\r\n	<li>External authentication methods like Twitter, Gmail, OpenId, etc...</li>\r\n	<li>Extendable and configurable user groups and permissions.</li>\r\n	<li>Elxis Defender, Security Level, SSL/TLS, encryption and other security related features.</li>\r\n	<li>Problem notification (automatic email notifications to site\'s technical manager on security alerts, fatal errors, and more).</li>\r\n	<li>Cache</li>\r\n	<li>Small footprint</li>\r\n	<li>Multi-sites support</li>\r\n	<li>Right-To-Left languages support.</li>\r\n	<li>Sub-categories of any level.</li>\r\n	<li>Multi-functional articles and categories.</li>\r\n	<li>Built-in commentary system.</li>\r\n	<li>RSS/ATOM feeds per category.</li>\r\n	<li>APIs for all core features makes developer work extremely easy.</li>\r\n	<li>Open search - search through your browser\'s search box.</li>\r\n	<li>HTML 5 ready</li>\r\n	<li>Mobile ready</li>\r\n	<li>Custom exit pages (page not found, forbidden, error, etc)</li>\r\n	<li>Extendable search system (search for content, images, videos, and more).</li>\r\n	<li>Image galleries</li>\r\n	<li>Contact forms</li>\r\n	<li>Multi-level horizontal and vertical menus.</li>\r\n	<li>Highly configurable category and article pages.</li>\r\n	<li>jQuery ready (built-in extensions dont use jQuery).</li>\r\n	<li>Site traffic statistics.</li>\r\n	<li>Highly configurable frontpage.</li>\r\n	<li>Easy and powerful internal linking system and search engine friendly URLs (Elxis URIs).</li>\r\n	<li>One-click extension install/update/un-install.</li>\r\n	<li>Elxis repository</li>\r\n	<li>Powerful templating system for the site\'s frontend and backend sections.</li>\r\n	<li>Visitors can display dates based on their own location.</li>\r\n	<li>Automatic generation and expansion of menus for components.</li>\r\n	<li>Performance monitor</li>\r\n	<li>System debug with many report levels.</li>\r\n	<li>One-click complete file system and database backup.</li>\r\n	<li>System logs (logging errors, security alerts, install/update/un-install actions, and more).</li>\r\n	<li>WYSIWYG editor with many features like spell checker, image uploading, styling, and more</li>\r\n	<li>Media manager</li>\r\n	<li>Translations manager</li>\r\n	<li>FTP support</li>\r\n	<li>UTF-8 support</li>\r\n	<li>Users central, members list and rich user profile.</li>\r\n	<li>Integrated fontawesome icons</li>\r\n</ul>', 'media/images/articles/features.jpg', '', 1, 'elxis features,pdo,multilingual,security,defender,multisites,open search, statistics', '2019-04-20 17:05:00', 1, 'John Doe', '1970-01-01 00:00:00', 0, NULL, 2, 0, 0, 'art_dateauthor=-1\nart_dateauthor_pos=-1\nart_img=-1\nart_print=-1\nart_email=-1\nart_twitter=-1\nart_facebook=-1\nart_hits=-1\nart_comments=-1\nart_tags=-1\nart_chain=-1\nart_related=-1', '2014-01-01 00:00:00', '2060-01-01 00:00:00', 0, 'elxis'),
(12, 2, 'What is Elxis?', 'what-is-elxis', 'An introduction to the Elxis world.', '<p>Elxis is an open source content management system (CMS) written in PHP programming language. It was born on December 2005 and since then it is used by thousands of people in all over the world. Elxis is famous for its stability and security, for the well tested extensions, for the multi-lingual features and the unique ideas we have implemented in it through these years many of which copied by other CMSs later.</p>', '<h4 class=\"elx_question\">What can I do with Elxis?</h4>\r\n<p>With Elxis you can easily build small, medium or large scale web sites without the need of having programming skills (although basic knowledge of HTML/CSS is recommended for best results). You can create news portals, personal blogs, online magazines, business sites, online shops, community portals and more.</p>\r\n\r\n<h4 class=\"elx_question\">What do I need to use Elxis?</h4>\r\n<p>Except Elxis, you will need some place to host your site online (although you can install Elxis in your local computer too). Your hosting provider should provide you a web server such as Apache, lighttpd or nginx, able to run PHP scripts and a database such as MySQL or PostgreSQL. We recommend you to pick Linux as the web server operating system. Any Linux distribution is fine. For business web sites we strongly recommend new users to assign the build of their web site to a highly trained professional. You will save time and get the best result.</p>\r\n\r\n<h4 class=\"elx_question\">Who owns Elxis?</h4>\r\n<p>Elxis is been developed by the <strong>Elxis Team</strong>, a group of friends passionate with the open source software. There are no big companies behind Elxis, there are no sponsors, advertisers, or hidden financial interests. Elxis is independent, a pure open source project. For legal purposes Elxis is represented by <strong>Ioannis Sannos</strong>, the core developer of Elxis, located in Athens, Greece.</p>\r\n\r\n<h4 class=\"elx_question\">The Elxis license</h4>\r\n<p>Elxis is released for free under the <a href=\"http://www.elxis.org/elxis-public-license.html\" target=\"_blank\" title=\"EPL\">Elxis Public License</a> (EPL). In short EPL grands you or limits you the following permissions.</p>\r\n\r\n<ul class=\"elx_stdul\">\r\n<li>You can use Elxis for any web site you want, even commercial ones.</li>\r\n<li>You are allowed to provide paid services and <a href=\"#elink:content/elxis/extensions.html\" title=\"elxis extensions types\">extensions</a> for Elxis (custom development, web hosting, support, templates, training, etc).</li>\r\n<li>You are not allowed to sell Elxis.</li>\r\n<li>You are not allowed to re-brand or rename Elxis.</li>\r\n<li>You are not allowed to modify or remove the Elxis copyright notes.</li>\r\n<li>You can create extensions for Elxis of any license. These extensions should be installed after the initial Elxis installation. You can not include them into the official Elxis package even if they are free ones (you are not allowed to re-pack Elxis).</li>\r\n<li>Elxis Team is not responsible for any damages may occur to your web site such as data or money loss by the use of Elxis.</li>\r\n<li>You can share and re-distribute only original copies of Elxis as released by <a href=\"http://www.elxis.org\" title=\"Elxis CMS\" target=\"_blank\">elxis.org</a> website.</li><li>You can modify Elxis only for your own web site.</li>\r\n<li>You are not allowed to publish or distribute modified versions of Elxis (forks are not allowed).</li>\r\n<li>Improvements, fixes and new ideas should be send to the Elxis Team for implementation in the official release.</li>\r\n<li>Elxis copyright holder is Elxis Team having legal representative Ioannis Sannos.</li>\r\n</ul>', 'media/images/articles/question-answer.jpg', '', 1, 'elxis license,elxis team,open source,cms,epl', '2019-04-20 17:05:00', 1, 'John Doe', '1970-01-01 00:00:00', 0, NULL, 1, 0, 0, 'art_dateauthor=-1\nart_dateauthor_pos=-1\nart_img=-1\nart_print=-1\nart_email=-1\nart_twitter=-1\nart_facebook=-1\nart_hits=-1\nart_comments=-1\nart_tags=-1\nart_chain=-1\nart_related=-1', '2014-01-01 00:00:00', '2060-01-01 00:00:00', 0, 'elxis');

/*!40000 ALTER TABLE `#__content` ENABLE KEYS */;

--
-- Definition of table `#__engines`
--

DROP TABLE IF EXISTS `#__engines`;
CREATE TABLE  `#__engines` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) default NULL,
  `engine` varchar(100) default NULL,
  `alevel` int(10) unsigned NOT NULL default '0',
  `ordering` int(10) unsigned NOT NULL default '0',
  `published` tinyint(4) unsigned NOT NULL default '0',
  `defengine` tinyint(4) unsigned NOT NULL default '0',
  `iscore` tinyint(4) unsigned NOT NULL default '0',
  `params` text,
  PRIMARY KEY  (`id`),
  KEY `idx_publev` (`published`,`alevel`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*!40000 ALTER TABLE `#__engines` DISABLE KEYS */;
INSERT INTO `#__engines` (`id`,`title`,`engine`,`alevel`,`ordering`,`published`,`defengine`,`iscore`,`params`) VALUES 
 (1, 'Content', 'content', 0, 1, 1, 1, 1, NULL),
 (2, 'Images', 'images', 0, 2, 1, 0, 1, NULL),
 (3, 'YouTube', 'youtube', 0, 3, 1, 0, 1, NULL);
/*!40000 ALTER TABLE `#__engines` ENABLE KEYS */;


--
-- Definition of table `#__frontpage`
--

DROP TABLE IF EXISTS `#__frontpage`;
CREATE TABLE  `#__frontpage` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pname` varchar(20) default NULL,
  `pval` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8;

/*!40000 ALTER TABLE `#__frontpage` DISABLE KEYS */;
INSERT INTO `#__frontpage` (`id`,`pname`,`pval`) VALUES 
 (1, 'wl', '0'),
 (2, 'wc', '100'),
 (3, 'wr', '0'),
 (4, 'c1', ''),
 (5, 'c2', 'frontpage2'),
 (6, 'c3', ''),
 (7, 'c4', ''),
 (8, 'c5', ''),
 (9, 'c6', ''),
 (10, 'c7', ''),
 (11, 'c8', ''),
 (12, 'c9', ''),
 (13, 'c10', ''),
 (14, 'c11', ''),
 (15, 'c12', ''),
 (16, 'c13', ''),
 (17, 'c14', ''),
 (18, 'c15', ''),
 (19, 'c16', ''),
 (20, 'c17', ''),
 (21, 'type', 'positions'),
 (22, 'reswidth', '650'),
 (23, 'resbox1', '1'),
 (24, 'resbox2', '1'),
 (25, 'resbox3', '1'),
 (26, 'resbox4', '1'),
 (27, 'resbox5', '1'),
 (28, 'resbox6', '1'),
 (29, 'resbox7', '1'),
 (30, 'resbox8', '1'),
 (31, 'resbox9', '1'),
 (32, 'resbox10', '1'),
 (33, 'resbox11', '1'),
 (34, 'resbox12', '1'),
 (35, 'resbox13', '1'),
 (36, 'resbox14', '1'),
 (37, 'resbox15', '1'),
 (38, 'resbox16', '1'),
 (39, 'resbox17', '1'),
 (40, 'rowsorder', '2,4x5,6x7,8x9,10,11x12x13,14,15x16,17');

/*!40000 ALTER TABLE `#__engines` ENABLE KEYS */;

--
-- Definition of table `#__groups`
--

DROP TABLE IF EXISTS `#__groups`;
CREATE TABLE `#__groups` (
  `gid` int(10) unsigned NOT NULL auto_increment,
  `level` tinyint(3) unsigned NOT NULL default '0',
  `groupname` varchar(120) default NULL,
  PRIMARY KEY  (`gid`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

/*!40000 ALTER TABLE `#__groups` DISABLE KEYS */;
INSERT INTO `#__groups` (`gid`,`level`,`groupname`) VALUES 
 (1,100,'Administrator'),
 (2,70,'Manager'),
 (3,50,'Publisher'),
 (4,30,'Author'),
 (5,2,'User'),
 (6,1,'External user'),
 (7,0,'Guest');
/*!40000 ALTER TABLE `#__groups` ENABLE KEYS */;


--
-- Definition of table `#__menu`
--

DROP TABLE IF EXISTS `#__menu`;
CREATE TABLE `#__menu` (
  `menu_id` int(11) NOT NULL auto_increment,
  `title` varchar(255) default NULL,
  `section` varchar(25) default NULL,
  `collection` varchar(100) default NULL,
  `menu_type` varchar(50) default NULL,
  `link` varchar(255) default NULL,
  `file` varchar(25) default NULL,
  `popup` tinyint(2) NOT NULL default '0',
  `secure` tinyint(2) NOT NULL default '0',
  `published` tinyint(4) NOT NULL default '0',
  `parent_id` int(11) NOT NULL default '0',
  `ordering` int(11) default '0',
  `expand` tinyint(4) NOT NULL default '0',
  `target` varchar(20) default NULL,
  `alevel` int(10) unsigned NOT NULL default '0',
  `width` int(10) unsigned NOT NULL default '0',
  `height` int(10) unsigned NOT NULL default '0',
  `iconfont` varchar(40) default NULL,
  PRIMARY KEY  (`menu_id`),
  KEY `idx_menu` (`section`,`published`,`alevel`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `#__menu`
--

/*!40000 ALTER TABLE `#__menu` DISABLE KEYS */;
INSERT INTO `#__menu` (`menu_id`, `title`, `section`, `collection`, `menu_type`, `link`, `file`, `popup`, `secure`, `published`, `parent_id`, `ordering`, `expand`, `target`, `alevel`, `width`, `height`, `iconfont`) VALUES
(1, 'Home', 'frontend', 'mainmenu', 'link', 'content:/', 'index.php', 0, 0, 1, 0, 1, 0, '_self', 0, 0, 0, NULL),
(2, 'Amazing discoveries', 'frontend', 'mainmenu', 'link', 'content:amazing-discoveries/', 'index.php', 0, 0, 1, 0, 2, 2, '_self', 0, 0, 0, NULL),
(3, 'Gallery', 'frontend', 'mainmenu', 'link', 'content:sample-gallery.html', 'index.php', 0, 0, 1, 0, 3, 0, NULL, 0, 0, 0, NULL),
(4, 'Contact us', 'frontend', 'mainmenu', 'link', 'content:contact-us.html', 'index.php', 0, 0, 1, 0, 5, 0, NULL, 0, 0, 0, NULL),
(5, 'Elxis', 'frontend', 'mainmenu', 'link', 'content:elxis/', 'index.php', 0, 0, 1, 0, 6, 0, NULL, 0, 0, 0, NULL),
(6, 'Typography', 'frontend', 'mainmenu', 'link', 'content:typography.html', 'index.php', 0, 0, 1, 0, 7, 0, NULL, 0, 0, 0, NULL),
(7, 'Administration', 'frontend', 'mainmenu', 'url', 'http://localhost/estia/', NULL, 0, 0, 1, 0, 8, 0, NULL, 0, 0, 0, NULL),
(8, 'Home', 'frontend', 'topmenu', 'link', 'content:/', 'index.php', 0, 0, 1, 0, 2, 0, '_self', 0, 0, 0, NULL),
(9, 'Amazing discoveries', 'frontend', 'topmenu', 'link', 'content:amazing-discoveries/', 'index.php', 0, 0, 1, 0, 1, 2, '_self', 0, 0, 0, NULL),
(10, 'Gallery', 'frontend', 'topmenu', 'link', 'content:sample-gallery.html', 'index.php', 0, 0, 1, 0, 3, 0, NULL, 0, 0, 0, NULL),
(11, 'Contact us', 'frontend', 'topmenu', 'link', 'content:contact-us.html', 'index.php', 0, 0, 1, 0, 4, 0, NULL, 0, 0, 0, NULL),
(12, 'Elxis', 'frontend', 'topmenu', 'link', 'content:elxis/', 'index.php', 0, 0, 1, 0, 5, 0, NULL, 0, 0, 0, NULL),
(13, 'Typography', 'frontend', 'topmenu', 'link', 'content:typography.html', 'index.php', 0, 0, 1, 0, 6, 0, NULL, 0, 0, 0, NULL),
(14, 'Administration', 'frontend', 'topmenu', 'url', 'http://localhost/estia/', NULL, 0, 0, 1, 0, 7, 0, NULL, 0, 0, 0, NULL),
(15, 'Elxis CMS', 'frontend', 'footermenu', 'url', 'http://www.elxis.org', NULL, 0, 0, 1, 0, 15, 0, '_blank', 0, 0, 0, NULL),
(16, 'Elxis forum', 'frontend', 'footermenu', 'url', 'http://forum.elxis.org', NULL, 0, 0, 1, 0, 16, 0, '_blank', 0, 0, 0, NULL),
(17, 'Elxis docs', 'frontend', 'footermenu', 'url', 'http://www.elxis.net/docs/', NULL, 0, 0, 1, 0, 17, 0, '_blank', 0, 0, 0, NULL);
/*!40000 ALTER TABLE `#__menu` ENABLE KEYS */;


--
-- Definition of table `#__messages`
--

DROP TABLE IF EXISTS `#__messages`;
CREATE TABLE  `#__messages` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `fromid` int(10) unsigned NOT NULL default '0',
  `fromname` varchar(80) default NULL,
  `toid` int(10) unsigned NOT NULL default '0',
  `toname` varchar(80) default NULL,
  `msgtype` varchar(30) default NULL,
  `message` text,
  `created` datetime NOT NULL default '1970-01-01 00:00:00',
  `read` tinyint(2) unsigned NOT NULL default '0',
  `replyto` int(10) unsigned NOT NULL default '0',
  `delbyfrom` tinyint(2) unsigned NOT NULL default '0',
  `delbyto` tinyint(2) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Definition of table `#__modules`
--

DROP TABLE IF EXISTS `#__modules`;
CREATE TABLE  `#__modules` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) default NULL,
  `content` longtext,
  `ordering` int(11) NOT NULL default '0',
  `position` varchar(20) default NULL,
  `published` tinyint(3) NOT NULL default '0',
  `module` varchar(60) default NULL,
  `showtitle` tinyint(3) NOT NULL default '2',
  `params` text,
  `iscore` tinyint(3) NOT NULL default '0',
  `section` varchar(20) default NULL,
  `pubdate` datetime NOT NULL default '2014-01-01 00:00:00',
  `unpubdate` datetime NOT NULL default '2060-01-01 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `idx_pubsection` (`published`,`section`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;


/*!40000 ALTER TABLE `#__modules` DISABLE KEYS */;
INSERT INTO `#__modules` (`id`, `title`, `content`, `ordering`, `position`, `published`, `module`, `showtitle`, `params`, `iscore`, `section`, `pubdate`, `unpubdate`) VALUES
(1, 'Language', NULL, 1, 'language', 1, 'mod_language', 0, 'style=1', 1, 'frontend', '2014-01-01 00:00:00', '2060-01-01 00:00:00'),
(2, 'Login', '', 1, 'hidden', 1, 'mod_login', 2, 'css_sfx=\r\ncachetime=0\r\next_auths=0\r\nauth_help=1\r\nrememberme=0\r\nlabels=2\r\norientation=0\r\nregireco=1\r\npretext=\r\nposttext=\r\nlogin_redir=2\r\nlogin_redir_uri=user:/\r\ndisplayname=0\r\navatar=1\r\nlogout_redir_uri=\r\ncache=\r\ngravatar=\r\nusergroup=\r\ntimeonline=\r\nauthmethod=', 1, 'frontend', '2014-01-01 00:00:00', '2060-01-01 00:00:00'),
(3, 'Menu', '', 1, 'left', 1, 'mod_menu', 2, 'collection=mainmenu\norientation=0\niconfonts=0\ncache=1\ncachetime=0\ncss_sfx=', 1, 'frontend', '2014-01-01 00:00:00', '2060-01-01 00:00:00'),
(4, 'Search', NULL, 1, 'search', 1, 'mod_search', 0, NULL, 1, 'frontend', '2014-01-01 00:00:00', '2060-01-01 00:00:00'),
(5, 'Articles', '', 5, 'frontpage2', 1, 'mod_articles', 0, 'source=1\ncatid=1\ncatids=\norder=0\ndays=10\nlimit=4\nlayout=1\nrelkey=\nshort=4\nshort_columns=2\nshort_sub=1\nshort_cat=1\nshort_date=1\nshort_text=420\nshort_more=1\nshort_caption=1\nshort_img=8\nlinks_cat=1\nlinks_img=1\nlinks_columns=1\ncache=1\ncachetime=0\ncss_sfx=\nsubcats=\nshort_imp=\nlinks_sub=\nlinks_date=', 1, 'frontend', '2014-01-01 00:00:00', '2060-01-01 00:00:00'),
(6, 'Categories', NULL, 2, 'right', 1, 'mod_categories', 2, NULL, 1, 'frontend', '2014-01-01 00:00:00', '2060-01-01 00:00:00'),
(7, 'Comments', '', 3, 'right', 0, 'mod_comments', 2, 'cache=0\ncachetime=0\nlimit=5\nsource=0\ncatid=0\ncomment_limit=100\ndisplay_date=1\ndisplay_author=1\navatar=1\ncss_sfx=\nsubcats=\ngravatar=', 1, 'frontend', '2014-01-01 00:00:00', '2060-01-01 00:00:00'),
(8, 'Who is online', '', 5, 'right', 1, 'mod_whosonline', 2, 'mode=0\navatarw=40\nontime=0\ncache=0\ncachetime=300\ncss_sfx=', 1, 'frontend', '2014-01-01 00:00:00', '2060-01-01 00:00:00'),
(9, 'Admin menu', NULL, 1, 'menu', 1, 'mod_adminmenu', 0, NULL, 1, 'backend', '2014-01-01 00:00:00', '2060-01-01 00:00:00'),
(10, 'Admin profile', NULL, 1, 'adminside', 1, 'mod_adminprofile', 0, NULL, 1, 'backend', '2014-01-01 00:00:00', '2060-01-01 00:00:00'),
(11, 'Admin search', NULL, 2, 'tools', 1, 'mod_adminsearch', 0, NULL, 1, 'backend', '2014-01-01 00:00:00', '2060-01-01 00:00:00'),
(12, 'Admin language', NULL, 1, 'tools', 1, 'mod_adminlang', 0, NULL, 1, 'backend', '2014-01-01 00:00:00', '2060-01-01 00:00:00'),
(13, 'Open Search', '', 6, 'right', 0, 'mod_opensearch', 0, 'cache=2\ncachetime=172800\ncss_sfx=\nstyle=0\ncustom_image=', 1, 'frontend', '2014-01-01 00:00:00', '2060-01-01 00:00:00'),
(14, 'Online users', NULL, 2, 'cpanel', 1, 'mod_adminusers', 0, NULL, 1, 'backend', '2014-01-01 00:00:00', '2060-01-01 00:00:00'),
(15, 'Latest and Popular articles', NULL, 1, 'cpanelbottom', 1, 'mod_adminarticles', 0, NULL, 1, 'backend', '2014-01-01 00:00:00', '2060-01-01 00:00:00'),
(16, 'Site statistics', NULL, 1, 'cpanel', 1, 'mod_adminstats', 0, NULL, 1, 'backend', '2014-01-01 00:00:00', '2060-01-01 00:00:00'),
(17, 'IOS Slider', '', 1, 'frontpage2', 0, 'mod_iosslider', 0, 'source=5\ncatid=0\ncatids=\ntitle1=\nlink1=\nimage1=\ntitle2=\nlink2=\nimage2=\ntitle3=\nlink3=\nimage3=\ntitle4=\nlink4=\nimage4=\ntitle5=\nlink5=\nimage5=\ntitle6=\nlink6=\nimage6=\nlocid=0\nsublocs=1\nhids=\nfolder=sample_slider\nfolderlink=\nimg_height=0\nborder=0\neffect=kenburns\ntransdur=1500\ncapeffect=0\ndelay=6\nautoplay=1\nbullets=1\ncaption=1\ncontrols=1\nlimit=5\ncache=2\ncachetime=0\ncss_sfx=\nsubcats=\nhotels_com=\nfolder_com=\nmlfolders=\nplaybuttons=\nhoverstop=', 0, 'frontend', '2014-01-01 00:00:00', '2060-01-01 00:00:00'),
(18, 'Gallery', NULL, 1, 'right', 1, 'mod_gallery', 2, 'limit=12\nwidth=40\ndir=sample_gallery\nlightbox=1\nlink=content:sample-gallery.html', 1, 'frontend', '2014-01-01 00:00:00', '2060-01-01 00:00:00'),
(19, 'Advertisements', NULL, 4, 'frontpage2', 0, 'mod_ads', 0, 'source=0\nborder=1', 1, 'frontend', '2014-01-01 00:00:00', '2060-01-01 00:00:00'),
(20, 'The most popular article', '', 3, 'frontpage2', 1, 'mod_articles', 0, 'source=1\ncatid=1\ncatids=\norder=1\ndays=10\nlimit=1\nlayout=1\nrelkey=\nshort=1\nshort_columns=1\nshort_sub=1\nshort_cat=1\nshort_date=0\nshort_text=1000\nshort_more=1\nshort_caption=1\nshort_img=4\nlinks_img=0\nlinks_columns=1\ncache=1\ncachetime=0\ncss_sfx=\nsubcats=\nshort_imp=\nlinks_sub=\nlinks_cat=\nlinks_date=', 1, 'frontend', '2014-01-01 00:00:00', '2060-01-01 00:00:00'),
(21, 'Custom module', '<div class=\"elx5_info\">This is a custom module serving only demo purposes. In Elxis you can create custom user modules, having any content you wish, and display them in any template position or even automatically between the category\'s articles.</div>', 6, 'frontpage2', 1, 'mod_content', 0, 'css_sfx=\ncache=1\ncachetime=0\nplugins=', 1, 'frontend', '2014-01-01 00:00:00', '2060-01-01 00:00:00'),
(22, 'Top menu', '', 1, 'menu', 1, 'mod_menu', 0, 'collection=mainmenu\norientation=1\niconfonts=0\ncache=1\ncachetime=0\ncss_sfx=', 1, 'frontend', '2014-01-01 00:00:00', '2060-01-01 00:00:00'),
(23, 'Footer menu', NULL, 1, 'footer', 1, 'mod_menu', 0, 'collection=footermenu\norientation=1\ncache=1', 1, 'frontend', '2014-01-01 00:00:00', '2060-01-01 00:00:00'),
(25, 'Powered by Elxis', '<p style=\"margin:0 0 30px 0; text-align:justify;\">This website is powered by <strong>Elxis CMS</strong>! Elxis is a free, open source, content management system. Among others Elxis is famous for its multilingual features, the strong security, the easy to use interface, the adaptation of the modest web technologies and the quality of the source code and extensions. <a href=\"http://www.elxis.org\" title=\"Elxis open source cms\">Download Elxis CMS</a> from elxis.org and give it a try. If you want a modern and secure website then Elxis is the best choice.</p>', 2, 'frontpage2', 1, 'mod_content', 0, 'cache=2\nplugins=0', 1, 'frontend', '2014-01-01 00:00:00', '2060-01-01 00:00:00'),
(26, 'Admin messages', NULL, 3, 'tools', 1, 'mod_adminmessages', 0, NULL, 1, 'backend', '2014-01-01 00:00:00', '2060-01-01 00:00:00');
/*!40000 ALTER TABLE `#__modules` ENABLE KEYS */;


DROP TABLE IF EXISTS `#__modules_menu`;
CREATE TABLE  `#__modules_menu` (
  `mmid` int(10) unsigned NOT NULL auto_increment,
  `moduleid` int(11) NOT NULL default '0',
  `menuid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`mmid`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

/*!40000 ALTER TABLE `#__modules_menu` DISABLE KEYS */;
INSERT INTO `#__modules_menu` (`mmid`,`moduleid`,`menuid`) VALUES 
 (1, 1, 0),
 (2, 2, 0),
 (3, 3, 0),
 (4, 4, 0),
 (5, 5, 0),
 (6, 6, 0),
 (7, 7, 0),
 (8, 8, 0),
 (9, 17, 0),
 (10, 18, 0),
 (11, 19, 0),
 (12, 20, 0),
 (13, 21, 0),
 (14, 22, 0),
 (15, 23, 0),
 (16, 13, 0),
 (17, 25, 0);
/*!40000 ALTER TABLE `#__modules_menu` ENABLE KEYS */;


DROP TABLE IF EXISTS `#__plugins`;
CREATE TABLE  `#__plugins` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) default NULL,
  `plugin` varchar(100) default NULL,
  `alevel` int(10) unsigned NOT NULL default '0',
  `ordering` int(10) unsigned NOT NULL default '0',
  `published` tinyint(4) unsigned NOT NULL default '0',
  `iscore` tinyint(4) unsigned NOT NULL default '0',
  `params` text,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

/*!40000 ALTER TABLE `#__plugins` DISABLE KEYS */;
INSERT INTO `#__plugins` (`id`,`title`,`plugin`,`alevel`,`ordering`,`published`,`iscore`,`params`) VALUES 
 (1, 'Elxis link', 'elink', 0, 1, 1,  1, NULL),
 (2, 'HTML5 video', 'video', 0, 2, 1,  1, NULL),
 (3, 'Contact form', 'contact', 0, 3, 1,  1, NULL),
 (4, 'Gallery', 'gallery', 0, 4, 1,  1, 'ordering=0\nautocaptions=1'),
 (5, 'Automatic links', 'autolinks', 0, 5, 1,  1, NULL),
 (6, 'YouTube video', 'youtube', 0, 6, 1,  1, NULL),
 (7, 'Google maps', 'map', 0, 7, 1,  1, NULL),
 (8, 'Page Break', 'pagebreak', 0, 8, 1,  1, NULL);
/*!40000 ALTER TABLE `#__plugins` ENABLE KEYS */;


DROP TABLE IF EXISTS `#__session`;
CREATE TABLE  `#__session` (
  `session_id` varchar(60) NOT NULL,
  `uid` int(10) unsigned NOT NULL default '0',
  `gid` tinyint(3) unsigned NOT NULL default '0',
  `login_method` varchar(20) default NULL,
  `first_activity` int(11) NOT NULL default '0',
  `last_activity` int(11) unsigned default NULL,
  `clicks` int(11) NOT NULL default '0',
  `current_page` varchar(255) default NULL,
  `ip_address` varchar(40) NOT NULL default '0',
  `user_agent` varchar(500) default NULL,
  `session_data` text,
  PRIMARY KEY  (`session_id`),
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

/*!40000 ALTER TABLE `#__session` DISABLE KEYS */;
/*!40000 ALTER TABLE `#__session` ENABLE KEYS */;


DROP TABLE IF EXISTS `#__statistics`;
CREATE TABLE  `#__statistics` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `statdate` varchar(10) default NULL,
  `clicks` int(10) unsigned NOT NULL default '0',
  `visits` int(10) unsigned NOT NULL default '0',
  `langs` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `statdate` (`statdate`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;


DROP TABLE IF EXISTS `#__statistics_temp`;
CREATE TABLE  `#__statistics_temp` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `uniqueid` varchar(45) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uniqueid` (`uniqueid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;


DROP TABLE IF EXISTS `#__templates`;
CREATE TABLE  `#__templates` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(120) default NULL,
  `template` varchar(60) default NULL,
  `section` varchar(20) default NULL,
  `iscore` tinyint(3) unsigned NOT NULL default '0',
  `params` text,
  PRIMARY KEY  (`id`),
  KEY `idx_template` USING BTREE (`template`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*!40000 ALTER TABLE `#__templates` DISABLE KEYS */;
INSERT INTO `#__templates` (`id`,`title`,`template`,`section`,`iscore`,`params`) VALUES 
 (1,'Five','five','frontend',1,'tplwidth=0\nsearch=content\nlogin=1\nlangselect=1\nsliderimage=0\nbgtype=0\npathway=3\nmarquee=\nmarqdur=15\nsidecol=1\ntwitter=https://twitter.com/elxiscms\nrss=1\nsitemap=1\ncontact=content:contact-us.html\ncopyright=1\ncopyrighttxt=\n'),
 (2,'Onyx','onyx','backend',1,NULL);
/*!40000 ALTER TABLE `#__templates` ENABLE KEYS */;


DROP TABLE IF EXISTS `#__template_positions`;
CREATE TABLE  `#__template_positions` (
  `id` int(11) NOT NULL auto_increment,
  `position` varchar(20) default NULL,
  `description` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;

/*!40000 ALTER TABLE `#__template_positions` DISABLE KEYS */;
INSERT INTO `#__template_positions` (`id`,`position`,`description`) VALUES 
 (1, 'left', 'The left column of your template'),
 (2, 'right', 'The right column of your template'),
 (3, 'menu', 'Horizontal menu used in both frontend and backend'),
 (4, 'footer', 'Default position for the footer menu'),
 (5, 'language', 'Ideal position for module Language'),
 (6, 'search', 'Ideal position for module Search'),
 (7, 'category', 'Internal position in content category pages'),
 (8, 'top', 'Content top'),
 (9, 'bottom', 'Content bottom'),
 (10, 'user1', 'Custom position 1'),
 (11, 'user2', 'Custom position 2'),
 (12, 'user3', 'Custom position 3'),
 (13, 'user4', 'Custom position 4'),
 (14, 'frontpage1', 'Modules shown on frontpage cell 1'),
 (15, 'frontpage2', 'Modules shown on frontpage cell 2'),
 (16, 'frontpage3', 'Modules shown on frontpage cell 3'),
 (17, 'frontpage4', 'Modules shown on frontpage cell 4'),
 (18, 'frontpage5', 'Modules shown on frontpage cell 5'),
 (19, 'frontpage10', 'Modules shown on frontpage cell 10'),
 (20, 'hidden', 'A hidden position'),
 (21, 'tools', 'Admin - Administration tools'),
 (22, 'cpanel', 'Admin - Control panel dashboard right column'),
 (23, 'cpanelbottom', 'Admin - Control panel dashboard bottom area'),
 (24, 'admintop', 'Admin - Above component'),
 (25, 'adminbottom', 'Admin - Bellow component'),
 (26, 'adminside', 'Admin - Side column');



/*!40000 ALTER TABLE `#__template_positions` ENABLE KEYS */;


DROP TABLE IF EXISTS `#__translations`;
CREATE TABLE `#__translations` (
  `trid` int(10) unsigned NOT NULL auto_increment,
  `category` varchar(60) default NULL,
  `element` varchar(100) default NULL,
  `language` char(2) default NULL,
  `elid` int(11) unsigned NOT NULL default '0',
  `translation` longtext,
  PRIMARY KEY  (`trid`),
  KEY `idx_transelement` USING BTREE (`category`,`element`,`language`,`elid`)
) ENGINE=InnoDB AUTO_INCREMENT=97 DEFAULT CHARSET=utf8;

/*!40000 ALTER TABLE `#__translations` DISABLE KEYS */;
INSERT INTO `#__translations` (`trid`, `category`, `element`, `language`, `elid`, `translation`) VALUES
(1, 'com_emenu', 'title', 'el', 1, 'Αρχική'),
(2, 'com_emenu', 'title', 'de', 1, 'Startseite'),
(3, 'com_emenu', 'title', 'es', 1, 'Página principal'),
(4, 'com_emenu', 'title', 'fr', 1, 'Accueil'),
(5, 'com_emenu', 'title', 'ru', 1, 'Главная'),
(6, 'com_emenu', 'title', 'tr', 1, 'Ana Sayfa'),
(7, 'com_emenu', 'title', 'el', 8, 'Αρχική'),
(8, 'com_emenu', 'title', 'de', 8, 'Startseite'),
(9, 'com_emenu', 'title', 'es', 8, 'Página principal'),
(10, 'com_emenu', 'title', 'fr', 8, 'Accueil'),
(11, 'com_emenu', 'title', 'ru', 8, 'Главная'),
(12, 'com_emenu', 'title', 'tr', 8, 'Ana Sayfa'),
(27, 'com_emenu', 'title', 'el', 3, 'Εικονοθήκη'),
(28, 'com_emenu', 'title', 'de', 3, 'Galerie'),
(29, 'com_emenu', 'title', 'es', 3, 'Galería'),
(30, 'com_emenu', 'title', 'fr', 3, 'Galerie'),
(31, 'com_emenu', 'title', 'ru', 3, 'Галерея'),
(32, 'com_emenu', 'title', 'tr', 3, 'Galeri'),
(33, 'com_emenu', 'title', 'it', 3, 'Galleria'),
(34, 'com_emenu', 'title', 'el', 10, 'Εικονοθήκη'),
(35, 'com_emenu', 'title', 'de', 10, 'Galerie'),
(36, 'com_emenu', 'title', 'es', 10, 'Galería'),
(37, 'com_emenu', 'title', 'fr', 10, 'Galerie'),
(38, 'com_emenu', 'title', 'ru', 10, 'Галерея'),
(39, 'com_emenu', 'title', 'tr', 10, 'Galeri'),
(40, 'com_emenu', 'title', 'it', 10, 'Galleria'),
(41, 'com_emenu', 'title', 'el', 4, 'Επικοινωνία'),
(42, 'com_emenu', 'title', 'de', 4, 'Kontakt'),
(43, 'com_emenu', 'title', 'es', 4, 'Contacto'),
(44, 'com_emenu', 'title', 'fr', 4, 'Contactez-nous'),
(45, 'com_emenu', 'title', 'ru', 4, 'Связаться'),
(46, 'com_emenu', 'title', 'tr', 4, 'Bize Ulaşın'),
(47, 'com_emenu', 'title', 'it', 4, 'Contattaci'),
(48, 'com_emenu', 'title', 'el', 11, 'Επικοινωνία'),
(49, 'com_emenu', 'title', 'de', 11, 'Kontakt'),
(50, 'com_emenu', 'title', 'es', 11, 'Contacto'),
(51, 'com_emenu', 'title', 'fr', 11, 'Contactez-nous'),
(52, 'com_emenu', 'title', 'ru', 11, 'Связаться'),
(53, 'com_emenu', 'title', 'tr', 11, 'Bize Ulaşın'),
(54, 'com_emenu', 'title', 'it', 11, 'Contattaci'),
(55, 'com_emenu', 'title', 'el', 7, 'Διαχείριση'),
(56, 'com_emenu', 'title', 'es', 7, 'Administración'),
(57, 'com_emenu', 'title', 'ru', 7, 'Администрация'),
(58, 'com_emenu', 'title', 'tr', 7, 'Yönetim'),
(59, 'com_emenu', 'title', 'it', 7, 'Amministrazione'),
(60, 'com_emenu', 'title', 'el', 14, 'Διαχείριση'),
(61, 'com_emenu', 'title', 'es', 14, 'Administración'),
(62, 'com_emenu', 'title', 'ru', 14, 'Администрация'),
(63, 'com_emenu', 'title', 'tr', 14, 'Yönetim'),
(64, 'com_emenu', 'title', 'it', 14, 'Amministrazione'),
(74, 'com_content', 'title', 'el', 6, 'Επεκτάσεις'),
(75, 'com_content', 'title', 'es', 6, 'Extensiones'),
(76, 'com_content', 'title', 'it', 6, 'Estensioni'),
(77, 'com_content', 'title', 'el', 8, 'Επικοινωνία'),
(78, 'com_content', 'title', 'it', 8, 'Contattaci'),
(79, 'com_content', 'title', 'fr', 8, 'Contactez-nous'),
(80, 'com_content', 'title', 'es', 8, 'Contáctenos'),
(81, 'com_content', 'title', 'de', 8, 'Kontaktieren sie uns'),
(82, 'com_content', 'title', 'el', 10, 'Δείγμα εικονοθήκης'),
(83, 'com_content', 'title', 'de', 10, 'Beispielgalerie'),
(84, 'com_content', 'title', 'fr', 10, 'Galerie échantillon'),
(85, 'com_content', 'title', 'es', 10, 'Galería muestra'),
(86, 'com_content', 'title', 'it', 10, 'Galleria di campione'),
(87, 'com_content', 'title', 'el', 11, 'Χαρακτηριστικά'),
(88, 'com_content', 'title', 'it', 11, 'Lineamenti'),
(89, 'com_content', 'title', 'fr', 11, 'Traits'),
(90, 'com_content', 'title', 'es', 11, 'Características'),
(91, 'com_content', 'title', 'de', 11, 'Eigenschaften'),
(92, 'com_content', 'title', 'el', 12, 'Τι είναι το Elxis;'),
(93, 'com_content', 'title', 'de', 12, 'Was ist Elxis?'),
(94, 'com_content', 'title', 'fr', 12, 'Quest-ce que Elxis?'),
(95, 'com_content', 'title', 'it', 12, 'Che cosa è Elxis?'),
(96, 'com_content', 'title', 'es', 12, '¿Qué es Elxis?');
/*!40000 ALTER TABLE `#__translations` ENABLE KEYS */;


DROP TABLE IF EXISTS `#__users`;
CREATE TABLE  `#__users` (
  `uid` int(10) unsigned NOT NULL auto_increment,
  `firstname` varchar(150) NOT NULL,
  `lastname` varchar(150) NOT NULL,
  `uname` varchar(80) default NULL,
  `pword` varchar(64) default NULL,
  `block` tinyint(1) unsigned NOT NULL default '0',
  `activation` varchar(100) default NULL,
  `gid` int(10) unsigned NOT NULL default '7',
  `groupname` varchar(120) default NULL,
  `avatar` varchar(250) default NULL,
  `preflang` varchar(10) default NULL,
  `timezone` varchar(60) default NULL,
  `country` varchar(120) default NULL,
  `city` varchar(120) default NULL,
  `address` varchar(250) default NULL,
  `postalcode` varchar(20) default NULL,
  `website` varchar(120) default NULL,
  `email` varchar(120) default NULL,
  `phone` varchar(40) default NULL,
  `mobile` varchar(40) default NULL,
  `gender` varchar(10) default NULL,
  `birthdate` varchar(20) default NULL,
  `occupation` varchar(120) default NULL,
  `registerdate` datetime NOT NULL default '1970-01-01 00:00:00',
  `lastvisitdate` datetime NOT NULL default '1970-01-01 00:00:00',
  `expiredate` datetime NOT NULL default '2060-01-01 00:00:00',
  `profile_views` int(11) unsigned NOT NULL default '0',
  `times_online` int(11) unsigned NOT NULL default '0',
  `params` text,
  `lastclicks` text,
  PRIMARY KEY  (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*!40000 ALTER TABLE `#__users` DISABLE KEYS */;
INSERT INTO `#__users` VALUES (1,'John','Doe','admin','d5958c5164e1b56be496304dc91b94c2299226a5', 0, NULL, 1, 'Administrator', NULL, 
NULL, NULL, NULL, NULL, NULL,  NULL, 'http://www.elxis.org', 'info@example.com', NULL, NULL, 'male', NULL, NULL, '2019-04-20 17:52:00', 
'2019-04-20 17:52:00', '2060-01-01 00:00:00', 0, 0, 'twitter=elxiscms', NULL);
/*!40000 ALTER TABLE `#__users` ENABLE KEYS */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
