<?php 
/**
* @version: 5.0
* @package: Elxis CMS
* @subpackage: Elxis Language
* @author: Elxis Team ( http://www.elxis.org )
* @copyright: (C) 2006-2019 Elxis.org. All rights reserved.
* @description: de-DE (Deutsch - Germany) language for Elxis CMS
* @license: Elxis public license http://www.elxis.org/elxis-public-license.html
* @translator: Christian Hirsch ( https://www.hitwe.at )
*
* ---- THIS FILE MUST BE ENCODED AS UTF-8! ----
*
*****************************************************************************/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


$_lang = array();
$_lang['INSTALLATION'] = 'Installation';
$_lang['STEP'] = 'Schritt';
$_lang['VERSION'] = 'Version';
$_lang['VERSION_CHECK'] = 'Versions Check';
$_lang['STATUS'] = 'Status';
$_lang['REVISION_NUMBER'] = 'Revisions Nummer';
$_lang['RELEASE_DATE'] = 'Release Datum';
$_lang['ELXIS_INSTALL'] = 'Elxis Installation';
$_lang['LICENSE'] = 'Lizenz';
$_lang['VERSION_PROLOGUE'] = 'Sie sind dabei Elxis CMS zu installieren. Die Version dieser Elxis Installation 
	sehen Sie nachstehend. Bitte versichern Sie sich unter <a href="http://www.elxis.org" target="_blank">elxis.org</a> 
	das dies die aktuellste Version ist.';
$_lang['BEFORE_BEGIN'] = 'Bevor Sie beginnen';
$_lang['BEFORE_DESC'] = 'Bevor Sie fortfahren lesen sie bitte die Anleitung sorgfältig durch';
$_lang['DATABASE'] = 'Datenbank';
$_lang['DATABASE_DESC'] = 'Für die Installation von Elxis CMS benötigen Sie eine leere Datenbank. <br>Wir empfehlen ausdrücklich eine <strong>MySQL<strong> Datenbank zu verwenden. <br>
Obwohl Elxis auch andere Datenbanktypen wie PostgreSQL und SQLite 3 unterstützt, ist Elxis CMS nur mit MySQL ausführlich getestet worden.';
$_lang['REPOSITORY'] = 'Repository';
$_lang['REPOSITORY_DESC'] = 'Elxis benutzt ein spezielles Verzeichnis um gecachte Seiten, Log-Dateien, Sitzungen, Sicherungen 
	und mehr zu speichern. Standardmäßig heißt dieses Verzeichnis <strong>repository</strong> und ist unterhalb des Elxis Stammverzeichnisses angelegt. 
	Dieses Verzeichnis <strong>muss beschreibbar sein</strong>! Wir empfehlen dringend, dieses Verzeichnis <strong>umzubenennen</strong>, und an eine 
	Stelle die nicht vom Web aus zugänglich ist zu <strong>verschieben</strong>. Nachdem Sie das Verzeichnis verschoben haben, und 
	<strong>open basedir</strong>-Schutz in PHP aktiviert haben, müssen Sie während der Installation den absoluten Pfad zum Repository angeben.';
$_lang['REPOSITORY_DEFAULT'] = 'Repository befindet sich an seiner Standardposition!';
$_lang['SAMPLE_ELXPATH'] = 'Muster Pfad von Elxis';
$_lang['DEF_REPOPATH'] = 'Standard Pfad für das Repository Verzeichnis';
$_lang['REQ_REPOPATH'] = 'Empfohlener Pfad für das Repository Verzeichnis';
$_lang['CONTINUE'] = 'Fortfahren';
$_lang['I_AGREE_TERMS'] = 'Ich habe die Elxis Public License (EPL) sowie die Bedingungen gelesen, verstanden und erkläre mich damit einverstanden.';
$_lang['LICENSE_NOTES'] = 'Elxis CMS ist eine <strong>kostenlose, Open Source</strong> Software die unter der <strong>Elxis Public License</strong>  (EPL) veröffentlicht wurde. <br>
Bitte lesen Sie die <strong>Elxis Public License</strong> (EPL) und die Bedingungen sorgfältig durch.';
$_lang['SETTINGS'] = 'Setup Elxis CMS';
$_lang['SITE_URL'] = 'URL der Seite';
$_lang['SITE_URL_DESC'] = 'Ohne Slash am Ende (zB. http://www.example.com)';
$_lang['REPOPATH_DESC'] = 'Der absolute Pfad zum Elxis repository Verzeichnis. Leer lassen für das Standardverzeichnis und -Name.';
$_lang['SETTINGS_DESC'] = 'Willkommen zum Setup Ihres Elxis CMS. <br>
Wir begleiten Sie nun durch die Schritte der Installation. <br>
Es sind einige Angaben notwendig, damit Ihr Elxis CMS arbeiten kann.';
$_lang['DEF_LANG'] = 'Standard Sprache';
$_lang['DEFLANG_DESC'] = 'Standardsprache Ihrer Seite.';
$_lang['ENCRYPT_METHOD'] = 'Verschlüsselungsmethode';
$_lang['ENCRYPT_KEY'] = 'Verschlüsselungsschlüssel';
$_lang['AUTOMATIC'] = 'Automatisch';
$_lang['GEN_OTHER'] = 'Einen anderen generieren';
$_lang['SITENAME'] = 'Name der Seite';
$_lang['TYPE'] = 'Typ';
$_lang['DBTYPE_DESC'] = 'Wir empfehlen ausdrücklich MySQL. Auswählbar sind lediglich die unterstützten Treiber Ihres Systems und der Elxis Installation.';
$_lang['HOST'] = 'Host';
$_lang['TABLES_PREFIX'] = 'Präfix der Tabellen';
$_lang['DSN_DESC'] = 'Sie können einen konfigurierten Datenquellen Namen angeben, um sich mit der Datenbank zu verbinden.';
$_lang['SCHEME'] = 'Schema';
$_lang['SCHEME_DESC'] = 'Der absolute Pfad zu der Datebnbankdatei wenn Sie eine Datenbank wie SQLite nutzen.';
$_lang['PORT'] = 'Port';
$_lang['PORT_DESC'] = 'Der Standard Port für MySQL ist 3306. Auf 0 belassen für automatische Auswahl.';
$_lang['FTPPORT_DESC'] = 'Der Standard Port für FTP ist 21. Auf 0 belassen für automatische Auswahl.';
$_lang['USE_FTP'] = 'FTP benutzen';
$_lang['PATH'] = 'Pfad';
$_lang['FTP_PATH_INFO'] = 'Der relative Pfad vom FTP Wurzelverzeichnis zum Elxis Installationsverzeichnis (z.B.: /public_html).';
$_lang['CHECK_FTP_SETS'] = 'FTP Einstellungen überprüfen';
$_lang['CHECK_DB_SETS'] = 'Datenbank Einstellungen überprüfen';
$_lang['DATA_IMPORT'] = 'Datenimport';
$_lang['SETTINGS_ERRORS'] = 'Die angegebenen Einstellungen sind fehlerhaft!';
$_lang['NO_QUERIES_WARN'] = 'Es hat den Anschein dass die ursprünglich in die Datenbank importierten Daten keine Verknüpfungen enthalten. 
	Vergewissern Sie sich dass die Daten wirklich importiert wurden bevor Sie fortfahren.';
$_lang['RETRY_PREV_STEP'] = 'Letzten Schritt wiederholen';
$_lang['INIT_DATA_IMPORTED'] = 'Ursprüngliche in die Datenbank importierte Daten.';
$_lang['QUERIES_EXEC'] = "%s SQL Verknüpfungen ausgeführt."; //translators help: {NUMBER} SQL queries executed
$_lang['ADMIN_ACCOUNT'] = 'Administrator Konto';
$_lang['CONFIRM_PASS'] = 'Passwort bestätigen';
$_lang['AVOID_COMUNAMES'] = 'Vermeidem Sie übliche Benutzernamen wie admin und Administrator.';
$_lang['YOUR_DETAILS'] = 'Ihre Daten';
$_lang['PASS_NOMATCH'] = 'Passwörter stimmen nicht überein!';
$_lang['REPOPATH_NOEX'] = 'Repository Verzeichnis existiert nicht!';
$_lang['FINISH'] = 'Beenden';
$_lang['FRIENDLY_URLS'] = 'Suchmaschinenfreundliche URLs';
$_lang['FRIENDLY_URLS_DESC'] = 'Wir empfehlen dringend das zu aktivieren. Damit das funktioniert wird Elxis versuchen die Datei htaccess.txt in 
	<strong>.htaccess</strong> umzubenennen. Sollte bereits eine ander .htaccess Datei im selben Verzeichnis existieren, wird sie überschrieben.';
$_lang['GENERAL'] = 'Allgemein';
$_lang['ELXIS_INST_SUCC'] = 'Ihre Elxis CMS Installation war erfolgreich.';
$_lang['ELXIS_INST_WARN'] = 'Elxis CMS Installation Warnungen.';
$_lang['CNOT_CREA_CONFIG'] = 'Konnte die Datei <strong>configuration.php</strong> im Elxis Installationsverzeichnis nicht erstellen.';
$_lang['CNOT_REN_HTACC'] = 'Konnte die Datei <strong>htaccess.txt</strong> nicht zu <strong>.htaccess</strong> umbenennen';
$_lang['CONFIG_FILE'] = 'Konfigurationsdatei';
$_lang['CONFIG_FILE_MANUAL'] = 'Datei configuration.php manuell erstellen, Kopieren Sie den folgenden Code und fügen ihn in die Datei ein.';
$_lang['REN_HTACCESS_MANUAL'] = 'Bitte benennen Sie die Datei <strong>htaccess.txt</strong> manuell in <strong>.htaccess</strong> um';
$_lang['WHAT_TODO'] = 'Ihre nächsten Schritte';
$_lang['RENAME_ADMIN_FOLDER'] = 'Um die Sicherheit zu erhöhen können Sie das Adminsitrationsverzeichnis <em>estia</em> umbenennen. 
	Sollten Sie das tun, müssen Sie den neuen Namen in der .htaccess datei eintragen.';
$_lang['LOGIN_CONFIG'] = 'Melden Sie sich im Administrationsbereich an und vervollständigen Sie die Konfiguration.';
$_lang['VISIT_NEW_SITE'] = 'Besuchen Sie Ihre neue Webseite';
$_lang['VISIT_ELXIS_SUP'] = 'Besuchen Sie die Elxis Support Seite';
$_lang['THANKS_USING_ELXIS'] = 'Danke für die Nutzung von Elxis CMS.';
//5.0
$_lang['OTHER_LANGS'] = 'Andere Sprachen';
$_lang['OTHER_LANGS_DESC'] = 'Welche anderen Sprachen, außer der Standardsprache sollen verfügbar sein?';
$_lang['ALL_LANGS'] = 'Alle';
$_lang['NONE_LANGS'] = 'Keine';
$_lang['REMOVE'] = 'Löschen';
$_lang['CONFIG_EMAIL_DISPATCH'] = 'E-Maileinstellungen konfigurieren (Optional)';
$_lang['SEND_METHOD'] = 'Versandmethode auswählen';
$_lang['RECOMMENDED'] = 'empfohlen';
$_lang['SECURE_CONNECTION'] = 'Sichere Verbindung';
$_lang['AUTH_REQUIRED'] = 'Authentifizierung erforderlich';
$_lang['AUTH_METHOD'] = 'Authentifizierungsmethode';
$_lang['DEFAULT_METHOD'] = 'Standard';

?>