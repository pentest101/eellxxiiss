<?php 
/**
* @version		5.0
* @package		Elxis
* @subpackage	Elxis Defender
* @copyright	Copyright (c) 2006-2020 Elxis CMS (https://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( https://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( https://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*
* Last update: 2019-04-02 17:27:00 GMT
*
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');

$rules = array(
	array('REFERER', 'misc\.yahoo\.com\.cn', 'Not allowed'),//07.03.2020
	//Not acceptable characters
	array('URI,QUERY,POST,AGENT,REFERER', '\%00|\x00', 'Null byte'),
	array('URI,QUERY,POST,AGENT', '[^a-z0-9]0x[0-9a-f][0-9a-f]', 'Unacceptable character'),
	array('URI,QUERY,POST,AGENT,REFERER', '\x01|\x02|\x03|\x04|\x05|\x06|\x07|\x08', 'Unacceptable ASCII character'),
	array('URI,QUERY,POST,AGENT,REFERER', '\x0e|\x0f|\x10|\x11|\x12|\x13|\x14|\x15|\x16|\x17|\x18|\x19|\x1a|\x1b|\x1c|\x1d|\x1e|\x1f', 'Unacceptable ASCII character'),
	//Hostnames - mostly from previous defender alerts
	//array('HOST', 'virtua\.com\.br|sl-reverse\.com|myhosting\.com|phpnet\.org|cappuccino\.dreamhost\.com|indianitoffice\.com|nowhiringu\.com|rzone\.de', 'Bad host'),
	//array('HOST', 'infoconcours\.com|linux\-power\.de|orion\.actwd\.net|perfora\.net|cl10\.omnis\.com|v21205\.1blu\.de|nc-p-web1\.netcore\.ch|tn2298\.telenec\.de', 'Bad host'),
	//array('HOST', 'prolinux2\.barrieweb\.net|experts\-ci\.com|\<\?|\<script|is74\.ru|servidorwebfacil\.com|veloxzone\.com\.br', 'Bad host'),
	//array('HOST', 'sovam\.net\.ua|ecatel\.net|kyivstar\.net|dnshigh\.com|njcontractor\.us|sweb\.ru|secureserver\.net|sarmo\.ru|ffbad\.org|dinaserver\.com', 'Bad host'),
	//array('REFERER', 'top1\-seo\-service\.com|nagdak\.ru|bezeqint\.net', 'Bad origin'),
	//array('REFERER', 'itsm\-kazan\.ru|purchasepillsnorx\.com|vadimkravtcov\.ru|video\-chat\.cn|essay\-zone\.com', 'Bad origin'),
	//User agents - mostly from previous defender alerts
	array('AGENT', 'bot\sfor\sjce|bot\/0\.1|thewebminer|indy\slibrary|ahrefsbot|libweb|libcurl|libwww|libidn|httplib|winhttp|libssh|wget|phpcrawl|emailsiphon|emailwolf|gbplugin|teleport|mj12bot|sistrix|fetchbot|007ac9|blexbot|weblogs|murzillo|arachni|absinthe', 'Bad user agent'),
	array('AGENT', 'abonti|jakarta|snoopy|missigua|aggregator|almaden|anarchie|aspseek|asterias|autoemailspider|bandit|bdcbot|backweb|blackwidow|vadixbot|concealed|takeout|cherrypicker|webroot|stealth|brutus|masscan|sqlmap|havij', 'Bad user agent'),
	array('AGENT', 'builtbottough|bullseye|bumblebee|ca\-crawler|cazoodlebot|ccbot|cegbfeieh|cheesebot|cherrypicker|chinaclaw|diibot|discobot|dittospyder|ecatch|emailharvest|wisebot|botversion|black\shole|w3af|hydra|java\/gigabot', 'Bad user agent'),
	array('AGENT', 'exabot|eirgrabber|emailcollec|flashget|foobot|getright|harvest|heritrix|httpdown|interget|k2spider|linkextractorpro|linkwalker|morfeus|netmechanic|netspider|ninja|npbot|voideye|netsparker|jaascois|dotbot|\[pl\]', 'Bad user agent'),
	array('AGENT', 'octopus|openfind|pagegrabber|propowerbot|prowebwalker|python|urllib|reget|repomonkey|rippers|sbider|scooter|seeker|seamonkey|semrushbot|seznambot|siphon|spacebison|spankbot|sucker|superhttp|suzuran|deepnet|chilkat', 'Bad user agent'),
	array('AGENT', 'szukacz|takeout|urldispatcher|true\_robot|ubicrawler|vacuum|webalta|webauto|webbandit|webcollage|webcopier|webemailextrac|webhook|webminer|webreaper|webzip|widow|wotbox|wwwoffle|x\-tractor|xaldon|zyborg|lmspider|memorybot|unknown', 'Bad user agent'),
	array('AGENT', 'webmole|wisenutbot|hanzoweb|gameboy|emailsiphon|adsarobot|nessus|floodgate|findlinks|email\sextractor|webaltbot|contactbot|butch|\<\?|\?\>|iframe|password|htaccess|htpasswd|\<script|fantombrowser|digout4uagent|panscient|telesoft|widows|converacrawler', 'Bad user agent'),
	array('AGENT', 'emailmagnet|datacha0s|production\sbot|sitesnagger|faxobot|grub\scrawler|attache|franklin|pcbrowser|psurf|user\-agent|pleasecrawl|kenjin|gecko\/2525|no\sbrowser|webster\spro|grub\-client|fastlwspider|contentsmartz|nikto', 'Bad user agent'),
	array('AGENT', 'morzilla|atomic\_email\_hunter|ecollector|backdoor|emailreaper|s\.t\.a\.l\.k\.e\.r\.|webvulnscan|nameofagent|copyrightcheck|surveybot|wordpress|zeus|windows\-update\-agent|mosiac|safexplorer|fiddler|digimarc|scanner|system\(|\/perl', 'Bad user agent'),
	array('AGENT', 'psycheclone|core\-project|atspider|copyguard|neuralbot|packrat|rsync|crescent|security\sscan|a\shref\=|bwh3\_user\_agent|microsoft\surl|internet\sexploiter|wells\ssearch|w3mir|pmafind|injection|internet\-exprorer|freshcafe|opensiteexplorer', 'Bad user agent'),
	//XSS
	array('URI,QUERY', '\.\./|\.\/\./|\.\/\/\.\/\/', 'Directory traversal attack'),
	array('URI', '(\<(.*)s(.*)c(.*)r(.*)i(.*)p(.*)t(.*)\>)', 'XSS attack'),
	array('QUERY', 'allow\_url\_include|code\=print\-|xxu\.php', 'Remote file inclusion'),
	//array('URI', '(?i)(<script[^>]*>[\s\S]*?<\/script[^>]*>|<script[^>]*>[\s\S]*?<\/script[[\s\S]]*[\s\S]|<script[^>]*>[\s\S]*?<\/script[\s]*[\s]|<script[^>]*>[\s\S]*?<\/script|<script[^>]*>[\s\S]*?)', 'XSS attack'),
	array('URI,QUERY', 'ftp\:|ftps\:|rtp\:|udp\:|php\:|data\:|file\:|glob\:|zlib\:|ogg\:|ssh2\:|expect\:', 'PHP wrapper attack'),
	array('URI,QUERY', 'alert\(|soapcaller\.bs|w00tw00t|\/explore\.php|\>|\<', 'XSS attack'),
	array('URI,QUERY', 'http\:|https\:', 'Remote file inclusion'),
	//array('URI', '\?\?|\?\&|\/\/', 'Bad URL'), //2 slashes "//" generates false alarm on base 64 encoded strings
	array('URI', '\?\?|\?\&', 'Bad URL'),
	//SQL injection
	array('URI,QUERY', '\*\/|\/\*|concat\s*\(|union.*select|select\sunion|group\_concat|\/\*\*/|information\_schema|\'1\'=\'1|concat\_ws|0x3a|select\/\*|count\(', 'SQL injection'),
	array('URI,QUERY', '\(user|password\)|create\stable|drop\stable|0x1e|0x3a|\'\sor(.*)1=1|table\_name|where\(|shootmmee\.php', 'SQL injection'),
	array('URI,QUERY,AGENT', 'drop\(|delete\(|select\(|char\(|benchmark\(|ascii\(|substring\(|jdatabasedrivermysqli|jsimplepiefactory|getconfig', 'SQL injection'),
	//Common cms scans and exploits
	array('URI,QUERY', 'wp\-login|wp\-content|wp\-admin|wp\-config|wp\-trackback|wp\-includes|wlwmanifest\.xml|redmystic|com\_jinc|com\_jce|com\_jnews|com\_community|civicrm|jnewsletter|joomleague|acymailing|oziogallery|maianmedia|maian15', 'Common CMS scan'),
	array('URI,QUERY', 'fckeditor\/|food\.php|myluph\.php|0day\.php|fruit\.php|allow\_url\_include|immanager|php\-ofc\-library|connector\.asp|uploadify.swf|magic\.php\.png|petx\.php|imgmanager|\/images\/stories', 'Common CMS scan'),
	array('URI,QUERY', 'hdflvplayer|\/administrator\/|config\.php\.bak|upload\.asp|elfinder\.php|elfinder\.html|zohoeditor|bigdump\.php|openflashchart|ofc\_upload\_image\.php|pwn\_base\_admin', 'Common CMS scan'),
	array('URI,QUERY', 'option\=http\:|newstype\.asp|option\=ftp\:|lpswomen|phpthumb\.php|revslider\_show\_image|ftp\.php|mosconfig\_|action\=dlattach|jfactory|data\-clickurl|plugin\_googlemap2\_proxy\.php', 'Common CMS scan'),
	array('URI', 'cfg\-contactform|1x\.php\.xxxjpg|\/pbot.php', 'Exploit scanner'),
	array('URI', 'com\_jdownloads|com\_bt\_portfolio|com\_sexycontactform|com\_creativecontactform|com\_adsmanager|com\_hwdvideoshare|com\_fabrik|com\_myblog', 'Common CMS scan'),
	array('URI', 'wwwroot\.php|concon\.asp|robots\.php|allowurl\.txt', 'Generic scan'),
	array('URI', 'wwwroot\.rar|wwwroot\.zip|www\.rar|www\.zip|web\.rar|web\.zip', 'Backup scanner'),
	//PHP 
	array('URI,QUERY', '\_get\[|\_post\[|\_cookie\[|\_server\[|\_files\[|\_request\[|globals\[', 'Gloval variable overwrite attempt'),
	array('URI,QUERY,AGENT', '\<\?|\<\%|base64\_decode\(|include\(|require\(|function\(|open\(|fwrite\(|eval\(|exec\(|passthru\(|system\(|if\(|gzinflate\(', 'PHP execution attempt'),
	array('URI,QUERY', '\-\-|\/\/\*|\/\*', 'PHP/SQL Comment'),
	//POST
	array('POST', '\<|\>|\"|\'', 'Unescaped character'),
	array('POST', 'include\(|require\(|function\(|exec\(|eval\(|system\(|passthru\(|\<script|open\(|write\(|\<\?php', 'PHP execution attempt'),
	array('POST', 'option\=com\_jce|plugin\=imgmanager', 'Bad bot'),
	//LFI
	array('URI,QUERY', '\/etc\/httpd\/|\/var\/www\/|\/apache\/logs\/|\/etc\/init\.d|\/etc\/passwd|\/proc\/self|\/usr\/local\/|\.htpasswd|\.htaccess|passwd\%00', 'Local file inclusion attack'),
	array('IP', '92.255.195.226', 'pfsense scanner')
);

?>