<?php 
/**
* @version: 5.2
* @package: Elxis CMS
* @subpackage: Elxis Language
* @author: Elxis Team ( https://www.elxis.org )
* @copyright: (C) 2006-2021 Elxis.org. All rights reserved.
* @description: nl-NL (Nederlands - Nederland) language for Elxis CMS
* @license: Elxis public license https://www.elxis.org/elxis-public-license.html
* @translator: Frank Gijsels ( http://www.onsnet.be/elxis )
*
* ---- THIS FILE MUST BE ENCODED AS UTF-8! ----
*
*****************************************************************************/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


$_lang = array();
$_lang['CONTROL_PANEL'] = 'Controle paneel';
$_lang['GENERAL_SITE_SETS'] = 'Algemene website instellingen';
$_lang['LANGS_MANAGER'] = 'Talen beheer';
$_lang['MANAGE_SITE_LANGS'] = 'Beheer talen website';
$_lang['USERS'] = 'Gebruikers';
$_lang['MANAGE_USERS'] = 'Maken, bewerken en verwijderen van gebruikersaccounts';
$_lang['USER_GROUPS'] = 'Gebruikers groepen';
$_lang['MANAGE_UGROUPS'] = 'Beheer gebruikers groepen';
$_lang['MEDIA_MANAGER'] = 'Media beheer';
$_lang['MEDIA_MANAGER_INFO'] = 'Beheer multi-media bestanden';
$_lang['ACCESS_MANAGER'] = 'Toegangs beheer';
$_lang['MANAGE_ACL'] = 'Beheer toegangs-controle lijsten';
$_lang['MENU_MANAGER'] = 'Menu beheer';
$_lang['MANAGE_MENUS_ITEMS'] = 'Beheer menu\'s en menu-items';
$_lang['FRONTPAGE'] = 'Voorpagina';
$_lang['DESIGN_FRONTPAGE'] = 'Ontwerp website voorpagina';
$_lang['CATEGORIES_MANAGER'] = 'Beheer categorieën';
$_lang['MANAGE_CONT_CATS'] = 'Beheer inhoud categorieën';
$_lang['CONTENT_MANAGER'] = 'Beheer inhoud';
$_lang['MANAGE_CONT_ITEMS'] = 'Beheer inhoud items';
$_lang['MODULES_MANAGE_INST'] = 'Beheer modules en installeer nieuwe.';
$_lang['PLUGINS_MANAGE_INST'] = 'Beheer plugins en installeer nieuwe.';
$_lang['COMPONENTS_MANAGE_INST'] = 'Beheer componenten en installeer nieuwe.';
$_lang['TEMPLATES_MANAGE_INST'] = 'Beheer templates en installeer nieuwe.';
$_lang['SENGINES_MANAGE_INST'] = 'Beheer zoekmachines en installeer nieuwe.';
$_lang['MANAGE_WAY_LOGIN'] = 'Beheer de manieren waarmee gebruikers mogen inloggen op de site.';
$_lang['TRANSLATOR'] = 'Vertaler';
$_lang['MANAGE_MLANG_CONTENT'] = 'Beheer meertalige inhoud';
$_lang['LOGS'] = 'Logs';
$_lang['VIEW_MANAGE_LOGS'] = 'Bekijk en beheer logbestanden';
$_lang['GENERAL'] = 'Algemeen';
$_lang['WEBSITE_STATUS'] = 'Website status';
$_lang['ONLINE'] = 'Online';
$_lang['OFFLINE'] = 'Offline';
$_lang['ONLINE_ADMINS'] = 'Enkel voor beheerders online';
$_lang['OFFLINE_MSG'] = 'Offline bericht';
$_lang['OFFLINE_MSG_INFO'] = 'Laat dit veld leeg om automatisch een meertalig bericht weer te geven';
$_lang['SITENAME'] = 'Site naam';
$_lang['URL_ADDRESS'] = 'URL adres';
$_lang['REPO_PATH'] = 'Repository path';
$_lang['REPO_PATH_INFO'] = 'Het volledige path naar de Elxis repository map. Laat leeg voor de standaard locatie (elxis_root/repository/). Wij raden aan om deze map boven de WWW te verplaatsen en te hernoemen!';
$_lang['FRIENDLY_URLS'] = 'SEO vriendelijke URL\'s';
$_lang['SEF_INFO'] = 'Indien ingesteld op JA (aanbevolen), hernoem het htaccess.txt bestand naar .htaccess';
$_lang['STATISTICS_INFO'] = 'Site statistieken inschakelen?';
$_lang['GZIP_COMPRESSION'] = 'Gzip compressie';
$_lang['GZIP_COMPRESSION_DESC'] = 'Elxis zal het document met GZIP comprimeren alvorens het naar de browser te sturen. Zo bespaar je 70% tot 80% bandbreedte.';
$_lang['DEFAULT_ROUTE'] = 'Standaard route';
$_lang['DEFAULT_ROUTE_INFO'] = 'Een Elxis opgemaakte URI die gebruikt zal worden als de site\'s voorpagina';
$_lang['META_DATA'] = 'META data';
$_lang['META_DATA_INFO'] = 'Een korte omschrijving voor de website';
$_lang['KEYWORDS'] = 'Sleutelwoorden';
$_lang['KEYWORDS_INFO'] = 'Een paar sleutelwoorden gescheiden met komma\'s';
$_lang['STYLE_LAYOUT'] = 'Stijl en layout';
$_lang['SITE_TEMPLATE'] = 'Site template';
$_lang['ADMIN_TEMPLATE'] = 'Administratie template';
$_lang['ICONS_PACK'] = 'Iconen pakket';
$_lang['LOCALE'] = 'Lokaal';
$_lang['TIMEZONE'] = 'Tijdzone';
$_lang['MULTILINGUISM'] = 'Meertaligheid';
$_lang['MULTILINGUISM_INFO'] = 'Stelt je in staat om tekst in te voeren in meer dan één taal (vertalingen). Schakel deze optie niet in als je niet van plan bent deze optie te gebruiken omdat het dan onnodig je site zal vertragen. De Elxis-interface zal steeds meertalig zijn, zelfs als deze optie niet is ingeschakeld.';
$_lang['CHANGE_LANG'] = 'Verander taal';
$_lang['LANG_CHANGE_WARN'] = 'Als je de standaard taal verandert kunnen er inconsistenties ontstaan tusssen de taal indicatoren en de vertalingen in de vertalingen tafel.';
$_lang['CACHE'] = 'Cache';
$_lang['CACHE_INFO'] = 'Elxis kan de gegenereerde HTML-code van individuele elementen bewaren in de cache zodat deze later sneller terug kan opgeboud worden. Dit is een algemene instelling, je moet ook de cache bij de elementen (bijv. Modules) waarvan je wil dat ze gecached worden inschakelen.';
$_lang['APC_INFO'] = 'De Alternatieve PHP Cache (APC) is een opcode cache voor PHP. Het moet worden ondersteund door uw webserver.
Het is niet aanbevolen op shared hosting omgevingen. Elxis zal het gebruiken bij speciale pagina\'s om de prestaties te verbeteren.';
$_lang['APC_ID_INFO'] = 'Indien meer dan 1 site op dezelfde server worden gehost, identificeer ze dan door een uniek getal voor deze website te geven.';
$_lang['USERS_AND_REGISTRATION'] = 'Gebruikers en Registratie';
$_lang['PRIVACY_PROTECTION'] = 'Privacy bescherming';
$_lang['PASSWORD_NOT_SHOWN'] = 'Het huidige wachtwoord wordt niet getoond om veiligheidsredenen. Vul dit veld enkel in als je het huidige wachtwoord wil wijzigen.';
$_lang['DB_TYPE'] = 'Database type';
$_lang['ALERT_CON_LOST'] = 'Als je de verbinding wijzigt, gaat de verbinding met de huidige database verloren!';
$_lang['HOST'] = 'Host';
$_lang['PORT'] = 'Poort';
$_lang['PERSISTENT_CON'] = 'Permanente verbinding';
$_lang['DB_NAME'] = 'DB Naam';
$_lang['TABLES_PREFIX'] = 'Tabel voorvoegsel';
$_lang['DSN_INFO'] = 'Een kant-en-klare gegevensbronnaam om te worden gebruikt voor verbinding te maken met de database.';
$_lang['SCHEME'] = 'Schema';
$_lang['SCHEME_INFO'] = 'Het absolute path naar een database-bestand als je een database zoals SQLite gebruikt.';
$_lang['SEND_METHOD'] = 'Send methode';
$_lang['SMTP_OPTIONS'] = 'SMTP opties';
$_lang['AUTH_REQ'] = 'Authenticatie vereist';
$_lang['SECURE_CON'] = 'Beveiligde verbinding';
$_lang['SENDER_NAME'] = 'Verzender naam';
$_lang['SENDER_EMAIL'] = 'Verzender e-mail';
$_lang['RCPT_NAME'] = 'Ontvanger naam';
$_lang['RCPT_EMAIL'] = 'Ontvanger e-mail';
$_lang['TECHNICAL_MANAGER'] = 'Technisch beheerder';
$_lang['TECHNICAL_MANAGER_INFO'] = 'De technisch beheerder ontvangt fout en veiligheid gerelateerde waarschuwingen.';
$_lang['USE_FTP'] = 'Gebruik FTP';
$_lang['PATH'] = 'Path';
$_lang['FTP_PATH_INFO'] = 'Het relatieve path van de FTP-hoofdmap naar de Elxis installatiemap (bijvoorbeeld: /public_html).';
$_lang['SESSION'] = 'Sessie';
$_lang['HANDLER'] = 'Handler';
$_lang['HANDLER_INFO'] = 'Elxis kan sessies opslaan als bestanden in Repository of in de database. Je kan ook Geen kiezen om PHP sessies te laten bewaren op de server\'s standaard locatie.';
$_lang['FILES'] = 'Bestanden';
$_lang['LIFETIME'] = 'Levensduur';
$_lang['SESS_LIFETIME_INFO'] = 'Tijd voordat de sessie verloopt wanneer je inactief bent.';
$_lang['CACHE_TIME_INFO'] = 'Na deze tijd worden gecachte items opnieuw gegenereerd.';
$_lang['MINUTES'] = 'Minuten';
$_lang['HOURS'] = 'Uren';
$_lang['MATCH_IP'] = 'Match IP';
$_lang['MATCH_BROWSER'] = 'Match browser';
$_lang['MATCH_REFERER'] = 'Match HTTP Referrer';
$_lang['MATCH_SESS_INFO'] = 'Activeert een geavanceerde sessie validatie routine.';
$_lang['ENCRYPTION'] = 'Encryptie';
$_lang['ENCRYPT_SESS_INFO'] = 'Versleutel sessie data?';
$_lang['ERRORS'] = 'Fouten';
$_lang['WARNINGS'] = 'Waarschuwingen';
$_lang['NOTICES'] = 'Mededelingen';
$_lang['NOTICE'] = 'Mededeling';
$_lang['REPORT'] = 'Rapport';
$_lang['REPORT_INFO'] = 'Meldings niveau fouten. Op productie sites raden we aan om deze uit te schakelen.';
$_lang['LOG'] = 'Log';
$_lang['LOG_INFO'] = 'Error log niveau. Selecteer welke fouten je wil dat Elxis in het systeem log schrijft (repository/logs/).';
$_lang['ALERT'] = 'Waarschuwing';
$_lang['ALERT_INFO'] = 'Mail fatale fouten naar de technisch beheerder van de website.';
$_lang['ROTATE'] = 'Roteren';
$_lang['ROTATE_INFO'] = 'Roteer fouten logs aan het einde van elke maand. Aanbevolen.';
$_lang['DEBUG'] = 'Debug';
$_lang['MODULE_POS'] = 'Module posities';
$_lang['MINIMAL'] = 'Minimaal';
$_lang['FULL'] = 'Volledig';
$_lang['DISPUSERS_AS'] = 'Geef gebruikers weer als';
$_lang['USERS_REGISTRATION'] = 'Gebruikers registratie';
$_lang['ALLOWED_DOMAIN'] = 'Toegelaten domein';
$_lang['ALLOWED_DOMAIN_INFO'] = 'Geef een domein naam (bijvoorbeeld elxis.org) waarvan het systeem de registratie e-mail adressen zal accepteren.';
$_lang['EXCLUDED_DOMAINS'] = 'Uitgesloten domeinen';
$_lang['EXCLUDED_DOMAINS_INFO'] = 'Komma\'s gescheiden lijst van domeinnamen (bijvoorbeeld badsite.com, hacksite.com) waarvan e-mailadressen niet worden geaccepteerd tijdens de registratie.';
$_lang['ACCOUNT_ACTIVATION'] = 'account activatie';
$_lang['DIRECT'] = 'Rechtstreeks';
$_lang['MANUAL_BY_ADMIN'] = 'Handmatig door de administrator';
$_lang['PASS_RECOVERY'] = 'Wachtwoord herstel';
$_lang['SECURITY'] = 'Beveiliging';
$_lang['SECURITY_LEVEL'] = 'Beveiligings niveau';
$_lang['SECURITY_LEVEL_INFO'] = 'Door het verhogen van het beveiligings niveau worden sommige opties geforceerd ingeschakeld terwijl andere functies worden uitgeschakeld. Raadpleeg de Elxis documentatie voor meer.';
$_lang['NORMAL'] = 'Normaal';
$_lang['HIGH'] = 'Hoog';
$_lang['INSANE'] = 'Zeer Hoog';
$_lang['ENCRYPT_METHOD'] = 'Encryptie methode';
$_lang['AUTOMATIC'] = 'Automatisch';
$_lang['ENCRYPTION_KEY'] = 'Encryptie sleutel';
$_lang['ELXIS_DEFENDER'] = 'Elxis defender';
$_lang['ELXIS_DEFENDER_INFO'] = 'Elxis defender beschermt uw website tegen XSS en SQL-injectie aanvallen. Deze krachtige tool filtert user requests en blokkeert aanvallen op uw site. Het zal je ook op de hoogte brengen van een aanval en deze in het log schrijven. U kunt kiezen welk type filters er moeten toegepast worden en zelfs je cruciale systeem-bestanden beschermen tegen ongeoorloofd wijzigingen. Wij raden aan om op zijn minste optie G in te schakelen. Raadpleeg de Elxis documentatie voor meer.';
$_lang['SSL_SWITCH'] = 'SSL schakelaar';
$_lang['SSL_SWITCH_INFO'] = 'Elxis schakelt automatisch over van HTTP naar HTTPS in pagina\'s waar privacy belangrijk is. Voor het administratie gebied is HTTPS permanent. Vereist een SSL-certificaat!';
$_lang['PUBLIC_AREA'] = 'Openbaar gebied';
$_lang['GENERAL_FILTERS'] = 'Algemene regels';
$_lang['CUSTOM_FILTERS'] = 'Aangepaste regels';
$_lang['FSYS_PROTECTION'] = 'Bescherming bestands systeem';
$_lang['CHECK_FTP_SETS'] = 'Controleer FTP instellingen';
$_lang['FTP_CON_SUCCESS'] = 'De verbinding met de FTP server is gelukt.';
$_lang['ELXIS_FOUND_FTP'] = 'Elxis installatie werd gevonden op FTP.';
$_lang['ELXIS_NOT_FOUND_FTP'] = 'Elxis installatie is niet gevonden op FTP! Controleer het FTP path.';
$_lang['CAN_NOT_CHANGE'] = 'Je kunt het niet veranderen.';
$_lang['SETS_SAVED_SUCC'] = 'Instellingen succesvol opgeslagen';
$_lang['ACTIONS'] = 'Acties';
$_lang['BAN_IP_REQ_DEF'] = 'Om een IP adres te blokkeren moet je ten minste één optie in Elxis defender inschakelen!';
$_lang['BAN_YOURSELF'] = 'Ben je aan het proberen om jezelf te blokkeren?';
$_lang['IP_AL_BANNED'] = 'Dit IP is reeds verboden!';
$_lang['IP_BANNED'] = 'IP adres %s verboden!';
$_lang['BAN_FAILED_NOWRITE'] = 'Verbod mislukt! File repository/logs/defender_ban.php is niet schrijfbaar.';
$_lang['ONLY_ADMINS_ACTION'] = 'Enkel beheerders kunnen deze actie uitvoeren!';
$_lang['CNOT_LOGOUT_ADMIN'] = 'U kunt een beheerder niet afmelden!';
$_lang['USER_LOGGED_OUT'] = 'De gebruiker is afgemeld!';
$_lang['SITE_STATISTICS'] = 'Site statistieken';
$_lang['SITE_STATISTICS_INFO'] = 'Zie de bezoekers statistieken';
$_lang['BACKUP'] = 'Backup';
$_lang['BACKUP_INFO'] = 'Neem een nieuwe volledige site backup en beheer bestaande backups.';
$_lang['BACKUP_FLIST'] = 'Lijst van bestaande backup bestanden';
$_lang['TYPE'] = 'Type';
$_lang['FILENAME'] = 'Bestandsnaam';
$_lang['SIZE'] = 'Grootte';
$_lang['NEW_DB_BACKUP'] = 'Nieuwe database backup';
$_lang['NEW_FS_BACKUP'] = 'Nieuwe bestands systeem backup';
$_lang['FILESYSTEM'] = 'Bestands systeem';
$_lang['DOWNLOAD'] = 'Download';
$_lang['TAKE_NEW_BACKUP'] = 'Een nieuwe backup maken?\nDit kan een tijdje duren, wees geduldig!';
$_lang['FOLDER_NOT_EXIST'] = "Map %s bestaat niet!";
$_lang['FOLDER_NOT_WRITE'] = "Map %s is niet schrijfbaar!";
$_lang['BACKUP_SAVED_INTO'] = "Backup bestanden worden bewaard in %s";
$_lang['CACHE_SAVED_INTO'] = "Cache bestanden worden bewaard in %s";
$_lang['CACHED_ITEMS'] = 'Gecachte items';
$_lang['ELXIS_ROUTER'] = 'Elxis router';
$_lang['ROUTING'] = 'Routing';
$_lang['ROUTING_INFO'] = 'Re-route user requests naar aangepaste URL adressen.';
$_lang['SOURCE'] = 'Bron';
$_lang['ROUTE_TO'] = 'Route naar';
$_lang['REROUTE'] = "Re-route %s";
$_lang['DIRECTORY'] = 'Directorie';
$_lang['SET_FRONT_CONF'] = 'Zet site voorpagina in Elxis configuratie!';
$_lang['ADD_NEW_ROUTE'] = 'Voeg een nieuwe route toe';
$_lang['OTHER'] = 'Andere';
$_lang['LAST_MODIFIED'] = 'Laatst bewerkt';
$_lang['PERIOD'] = 'Tijdvak'; //time period
$_lang['ERROR_LOG_DISABLED'] = 'Fouten logging is uitgeschakeld!';
$_lang['LOG_ENABLE_ERR'] = 'Log is alleen ingeschakeld voor fatale fouten.';
$_lang['LOG_ENABLE_ERRWARN'] = 'Log is ingeschakeld voor fouten en waarschuwingen.';
$_lang['LOG_ENABLE_ERRWARNNTC'] = 'Log is ingeschakeld voor fouten, waarschuwingen en mededelingen.';
$_lang['LOGROT_ENABLED'] = 'Log rotatie is ingeschakeld.';
$_lang['LOGROT_DISABLED'] = 'Log rotatie is uitgeschakeld!';
$_lang['SYSLOG_FILES'] = 'Systeem log bestanden';
$_lang['DEFENDER_BANS'] = 'Defender bans';
$_lang['LAST_DEFEND_NOTIF'] = 'Laatste Defender melding';
$_lang['LAST_ERROR_NOTIF'] = 'Laatste fout melding';
$_lang['TIMES_BLOCKED'] = 'Aantal keer geblokkeerd';
$_lang['REFER_CODE'] = 'Referentie code';
$_lang['CLEAR_FILE'] = 'Maak bestand leeg';
$_lang['CLEAR_FILE_WARN'] = 'De inhoud van het bestand wordt verwijderd. Doorgaan?';
$_lang['FILE_NOT_FOUND'] = 'Bestand niet gevonden!';
$_lang['FILE_CNOT_DELETE'] = 'Dit bestand kan niet worden verwijderd!';
$_lang['ONLY_LOG_DOWNLOAD'] = 'Enkel bestanden met de extensie .log kunnen worden gedownload!';
$_lang['SYSTEM'] = 'Systeem';
$_lang['PHP_INFO'] = 'PHP informatie';
$_lang['PHP_VERSION'] = 'PHP versie';
$_lang['ELXIS_INFO'] = 'Elxis informatie';
$_lang['VERSION'] = 'Versie';
$_lang['REVISION_NUMBER'] = 'Revisie nummer';
$_lang['STATUS'] = 'Status';
$_lang['CODENAME'] = 'Code naam';
$_lang['RELEASE_DATE'] = 'Release datum';
$_lang['COPYRIGHT'] = 'Copyright';
$_lang['POWERED_BY'] = 'Powered by';
$_lang['AUTHOR'] = 'Auteur';
$_lang['PLATFORM'] = 'Platform';
$_lang['HEADQUARTERS'] = 'Hoofdzetel';
$_lang['ELXIS_ENVIROMENT'] = 'Elxis omgeving';
$_lang['DEFENDER_LOGS'] = 'Defender logs';
$_lang['ADMIN_FOLDER'] = 'Administratie map';
$_lang['DEF_NAME_RENAME'] = 'Standaard naam, hernoem het!';
$_lang['INSTALL_PATH'] = 'Installatie path';
$_lang['IS_PUBLIC'] = 'Is publiek!';
$_lang['CREDITS'] = 'Dankbetuigingen';
$_lang['LOCATION'] = 'Locatie';
$_lang['CONTRIBUTION'] = 'Bijdragen';
$_lang['LICENSE'] = 'Licentie';
$_lang['MULTISITES'] = 'Multisites';
$_lang['MULTISITES_DESC'] = 'Beheer meerdere sites onder één Elxis installatie.';
$_lang['MULTISITES_WARN'] = 'Je kan meerdere sites onder één Elxis installatie hebben. Werken met multisites is een taak die geavanceerde kennis van het Elxis CMS vereist. Zorg ervoor dat de database bestaat voordat je gegevens importeert naar een nieuwe multisite . Na het aanmaken van een nieuwe multisite, bewerk het .htaccess bestand op basis van de gegeven istructies. Als je een multisite verwijdert, wordt de gekoppelde database niet verwijdert. Raadpleeg een ervaren technicus als je hulp nodig hebt.';
$_lang['MULTISITES_DISABLED'] = 'Multisites is uitgeschakeld!';
$_lang['ENABLE'] = 'Inschakelen';
$_lang['ACTIVE'] = 'Actief';
$_lang['URL_ID'] = 'URL identifier';
$_lang['MAN_MULTISITES_ONLY'] = "Je kan multisites enkel beheren vanaf site %s";
$_lang['LOWER_ALPHANUM'] = 'Kleine alfanumerieke tekens zonder spaties';
$_lang['IMPORT_DATA'] = 'Importeer gegevens';
$_lang['CNOT_CREATE_CFG_NEW'] = "Kon het configuratiebestand %s voor de nieuwe site niet maken!";
$_lang['DATA_IMPORT_FAILED'] = 'Importeren gegevens mislukt!';
$_lang['DATA_IMPORT_SUC'] = 'Gegevens geïmporteerd!';
$_lang['ADD_RULES_HTACCESS'] = 'Voeg de volgende regels toe in het .htaccess bestand';
$_lang['CREATE_REPOSITORY_NOTE'] = 'We raden aan een aparte repository aan te maken voor elke sub-site!';
$_lang['NOT_SUP_DBTYPE'] = 'Niet ondersteund database type!';
$_lang['DBTYPES_MUST_SAME'] = 'Database types van deze site en de nieuwe site moeten hetzelfde zijn!';
$_lang['DISABLE_MULTISITES'] = 'Multisites uitschakelen';
$_lang['DISABLE_MULTISITES_WARN'] = 'Alle sites behalve de site met id 1 zullen verwijderd worden!';
$_lang['VISITS_PER_DAY'] = "Bezoeken per dag voor %s"; //translators help: ... for {MONTH YEAR}
$_lang['CLICKS_PER_DAY'] = "Kliks per dag voor %s"; //translators help: ... for {MONTH YEAR}
$_lang['VISITS_PER_MONTH'] = "Bezoeken per maand voor %s"; //translators help: ... for {YEAR}
$_lang['CLICKS_PER_MONTH'] = "Kliks per maand voor %s"; //translators help: ... for {YEAR}
$_lang['LANGS_USAGE_FOR'] = "Percentage talen voor %s"; //translators help: ... for {MONTH YEAR}
$_lang['UNIQUE_VISITS'] = 'Unieke bezoeken';
$_lang['PAGE_VIEWS'] = 'Pagina weergaven';
$_lang['TOTAL_VISITS'] = 'Totaal aantal bezoeken';
$_lang['TOTAL_PAGE_VIEWS'] = 'Pagina weergaven';
$_lang['LANGS_USAGE'] = 'Talen gebruik';
$_lang['LEGEND'] = 'Legende';
$_lang['USAGE'] = 'Gebruik';
$_lang['VIEWS'] = 'Bekeken';
$_lang['OTHER'] = 'Andere';
$_lang['NO_DATA_AVAIL'] = 'Geen gegevens beschikbaar';
$_lang['PERIOD'] = 'Periode';
$_lang['YEAR_STATS'] = 'Jaar statistieken';
$_lang['MONTH_STATS'] = 'Maand statistieken';
$_lang['PREVIOUS_YEAR'] = 'Vorig jaar';
$_lang['NEXT_YEAR'] = 'Volgend jaar';
$_lang['STATS_COL_DISABLED'] = 'Het verzamelen van statistische gegevens is uitgeschakeld! Je kan dit inschakelen in Elxis configuratie.';
$_lang['DOCTYPE'] = 'Document type';
$_lang['DOCTYPE_INFO'] = 'De aanbevolen optie is HTML5. Elxis zal XHTML genereren, zelfs als je het DOCTYPE instelt op HTML5. 
	Bij XHTML doctypes genereert Elxis documenten met de application/xhtml + xml MIME-type bij moderne browsers en met tekst/html bij oudere.';
$_lang['ABR_SECONDS'] = 'Sec';
$_lang['ABR_MINUTES'] = 'Min';
$_lang['HOUR'] = 'Uur';
$_lang['HOURS'] = 'Uren';
$_lang['DAY'] = 'Dag';
$_lang['DAYS'] = 'Dagen';
$_lang['UPDATED_BEFORE'] = 'Updated vóór';
$_lang['CACHE_INFO'] = 'Bekijk en verwijder de items opgeslagen in de cache.';
$_lang['ELXISDC'] = 'Elxis Downloads Center';
$_lang['ELXISDC_INFO'] = 'Browse live EDC en bekijk de beschikbare extensies';
$_lang['SITE_LANGS'] = 'Site talen';
$_lang['SITE_LANGS_DESC'] = 'Standaard zijn alle geïnstalleerde talen beschikbaar in het frontend van de site. 
	Je kan dit wijzigen door hieronder de talen te selecteren die je wil beschikbaar stellen.';
//Elxis 4.1
$_lang['PERFORMANCE'] = 'Performance';
$_lang['MINIFIER_CSSJS'] = 'CSS/Javascript minifier';
$_lang['MINIFIER_INFO'] = 'Elxis kan individuele lokale CSS en JS bestanden samenvoegen en hen eventueel comprimeren. Het samengevoegde bestand wordt opgeslagen in de cache. Dus in plaats van meerdere CSS/JS-bestanden in je head sectie heb je dan nog slechts één (gecomprimeerd) bestand.';
$_lang['MOBILE_VERSION'] = 'Mobiele versie';
$_lang['MOBILE_VERSION_DESC'] = 'Mobiel vriendelijke versie voor handheld-apparaten inschakelen?';
//Elxis 4.2
$_lang['SEND_TEST_EMAIL'] = 'Test e-mail versturen';
$_lang['ONLINE_USERS'] = 'Online voor gebruikers';
$_lang['CRONJOBS'] = 'Cron jobs';
$_lang['CRONJOBS_INFO'] = 'Cron jobs inschakelen als je geautomatiseerde taken wil uitvoeren zoals publicatie van geplande artikelen.';
$_lang['LANG_DETECTION'] = 'Taal detectie';
$_lang['LANG_DETECTION_INFO'] = 'Moedertaal detectie en doorverwijzing naar de juiste taalversie van de site bij het eerste bezoek van de voorpagina.';
//Elxis 4.4
$_lang['DEFENDER_NOTIFS'] = 'Defender meldingen';
$_lang['XFRAMEOPT_HELP'] = 'HTTP-header die bepaalt of de browser pagina\'s van deze site binnen een frame accepteert of weigert. Helpt clickjacking-aanvallen te vermijden.';
$_lang['ACCEPT_XFRAME'] = 'Accepteren X-Frame';
$_lang['DENY'] = 'Ontkennen';
$_lang['SAMEORIGIN'] = 'Dezelfde oorsprong';
$_lang['ALLOW_FROM'] = 'Toestaan van';
$_lang['ALLOW_FROM_ORIGIN'] = 'Toestaan van oorsprong';
$_lang['CONTENT_SEC_POLICY'] = 'Inhoud beveiligingsbeleid';
$_lang['IP_RANGES'] = 'IP-bereiken';
$_lang['UPDATED_AUTO'] = 'Automatisch bijgewerkt';
$_lang['CHECK_IP_MOMENT'] = 'Controleer IP-ogenblik';
$_lang['BEFORE_LOAD_ELXIS'] = 'Voor het laden van de Elxis-kern';
$_lang['AFTER_LOAD_ELXIS'] = 'Na het laden van de Elxis-kern';
$_lang['CHECK_IP_MOMENT_HELP'] = 'VOOR: Defender controleert IP\'s bij elke klik. Slechte IP\'s bereiken de Elxis kern niet. 
	NA: Defender controleert IP\'s slechts eenmaal per sessie (prestatieverbetering). Slechte IP\'s bereiken de Elxis kern voordat ze worden geblokkeerd.';
$_lang['SECURITY'] = 'Beveiliging';
$_lang['EVERYTHING'] = 'Alles';
$_lang['ONLY_ATTACKS'] = 'Alleen aanvallen';
$_lang['CRONJOBS_PROB'] = 'Cron jobs waarschijnlijkheid';
$_lang['CRONJOBS_PROB_INFO'] = 'De procentuele waarschijnlijkheid om Cron-taken uit te voeren bij elke gebruikersklik. Betreft alleen cron-taken die intern door Elxis worden uitgevoerd. Voor de beste prestaties geldt dat hoe meer bezoekers uw site heeft, hoe lager deze waarde zou moeten zijn. De standaardwaarde is 10%.';
$_lang['EXTERNAL'] = 'Externe';
$_lang['SEO_TITLES_MATCH'] = 'SEO-titels komen overeen';
$_lang['SEO_TITLES_MATCH_HELP'] = 'Regelt het genereren van SEO-titels op basis van normale titels. Exact maakt SEO Titels die exact overeenkomen met de originele titels die zijn getranslitereerd.';
$_lang['EXACT'] = 'Exact';
//Elxis 4.6
$_lang['CONFIG_FOR_GMAIL'] = 'Configuratie voor Gmail';
$_lang['AUTH_METHOD'] = 'Authenticatie methode';
$_lang['DEFAULT'] = 'Standaard';
$_lang['BACKUP_EXCLUDE_PATHS_HELP'] = 'U kunt sommige mappen uitsluiten van de back-up procedure van het bestandssysteem. Dit is zeer nuttig als u een groot bestandssysteem hebt en de back-up niet kan worden voltooid vanwege geheugenproblemen. Geef hieronder de mappen op die u wilt uitsluiten door hun relatieve paden op te geven. Voorbeeld: media/videos/';
$_lang['PATHS_EXCLUDED_FSBK'] = 'Paden uitgesloten van bestandssysteem backup';
$_lang['EXCLUSIONS'] = 'Uitsluitingen';
//Elxis 5.0
$_lang['BACKUP_FOLDER_TABLE_TIP'] = 'Voor back-up van het bestandssysteem kunt u ervoor kiezen om een back-up te maken van de hele Elxis-installatie of van een specifieke map. Voor de database kunt u een back-up maken van de hele database of van een specifieke tabel. Krijg je tijdens de back-up time-out of geheugenfouten (vooral op grote sites), kies er dan voor om een back-up te maken van afzonderlijke mappen of tabellen.';
$_lang['FOLDER'] = 'Map';
$_lang['TABLE'] = 'Tabel';
$_lang['INACTIVE'] = 'Inactief';
$_lang['DEPRECATED'] = 'Verouderd';
$_lang['ALL_AVAILABLE'] = 'Alle beschikbare';//translators help: "All available languages"
$_lang['NO_PROTECTION'] = 'Geen bescherming';//translators help: No Elxis Defender filters enabled
$_lang['NEWER_VERSION_FOR'] = 'Er is een nieuwere versie (%s) voor %s';
$_lang['NEWER_VERSIONS_FOR'] = 'Er zijn nieuwere versies voor  %s';
$_lang['NEWER_VERSIONS_FOR_EXTS'] = 'Er zijn nieuwere versies voor %s extensies';
$_lang['OUTDATED_ELXIS_UPDATE_TO'] = 'U gebruikt een verouderde Elxis-versie! Werk zo snel mogelijk bij naar %s';
$_lang['NO_BACKUPS'] = 'U hebt geen back-ups!';
$_lang['LONGTIME_TAKE_BACKUP'] = 'U hebt lang de tijd om een back-up van de site te maken';
$_lang['DELETE_OLD_LOGS'] = 'Verwijder oude logbestanden';
$_lang['DEFENDER_IS_DISABLED'] = 'Elxis Defender is uitgeschakeld';
$_lang['REPO_DEF_PATH'] = 'Repository bevindt zich op het standaardpad';
$_lang['CHANGE_MAIL_TO_SMTP'] = 'Verander PHP Mail naar SMTP of anders';
$_lang['DISABLE_MULTILINGUISM'] = 'Meertaligheid uitschakelen';
$_lang['ENABLE_MULTILINGUISM'] = 'Meertaligheid inschakelen';
//Elxis 5.2
$_lang['NOTFOUND'] = 'Not found';
$_lang['EXTENSION'] = 'Extension';
$_lang['CODE_EDITOR_WARN'] = 'We strongly recommend not to modify extensions\'s files because you will lose your changes after an update. 
	Add your custom or overwrite CSS rules on <strong>user.config</strong> files instead.';
$_lang['EDIT_CODE'] = 'Edit code';
$_lang['EXCLUDED_IPS'] = 'Excluded IPs';

?>