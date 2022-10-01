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


$locale = array('de_DE.utf8', 'de_DE.UTF-8', 'en_GB', 'en', 'english', 'england'); //utf-8 locales array

$_lang = array();
//date formats
$_lang['DATE_FORMAT_BOX'] = 'd-m-Y';
$_lang['DATE_FORMAT_BOX_LONG'] = 'd-m-Y H:i:s';
$_lang['DATE_FORMAT_1'] = "%d/%m/%Y"; //Beispiel: 25/12/2010
$_lang['DATE_FORMAT_2'] = "%b %d, %Y"; //Beispiel: Dez 25, 2010
$_lang['DATE_FORMAT_3'] = "%B %d, %Y"; //Beispiel: Dezember 25, 2010
$_lang['DATE_FORMAT_4'] = "%b %d, %Y %H:%M"; //Beispiel: Dez 25, 2010 12:34
$_lang['DATE_FORMAT_5'] = "%B %d, %Y %H:%M"; //Beispiel: Dezember 25, 2010 12:34
$_lang['DATE_FORMAT_6'] = "%B %d, %Y %H:%M:%S"; //Beispiel: Dezember 25, 2010 12:34:45
$_lang['DATE_FORMAT_7'] = "%a %b %d, %Y"; //Beispiel: Sam Dez 25, 2010
$_lang['DATE_FORMAT_8'] = "%A %b %d, %Y"; //Beispiel: Samstag Dez 25, 2010
$_lang['DATE_FORMAT_9'] = "%A %B %d, %Y"; //Beispiel: Samstag Dezember 25, 2010
$_lang['DATE_FORMAT_10'] = "%A %B %d, %Y %H:%M"; //Beispiel: Samstag Dezember 25, 2010 12:34
$_lang['DATE_FORMAT_11'] = "%A %B %d, %Y %H:%M:%S"; //Beispiel: Samstag Dezember 25, 2010 12:34:45
$_lang['DATE_FORMAT_12'] = "%a %B %d, %Y %H:%M"; //Beispiel: Sam Dezember 25, 2010 12:34
$_lang['DATE_FORMAT_13'] = "%a %B %d, %Y %H:%M:%S"; //Beispiel: Sam Dezember 25, 2010 12:34:45
$_lang['THOUSANDS_SEP'] = ',';
$_lang['DECIMALS_SEP'] = '.';
//Monats namen
$_lang['JANUARY'] = 'Jänner';
$_lang['FEBRUARY'] = 'Februar';
$_lang['MARCH'] = 'März';
$_lang['APRIL'] = 'April';
$_lang['MAY'] = 'Mai';
$_lang['JUNE'] = 'Juni';
$_lang['JULY'] = 'Juli';
$_lang['AUGUST'] = 'August';
$_lang['SEPTEMBER'] = 'September';
$_lang['OCTOBER'] = 'Oktober';
$_lang['NOVEMBER'] = 'November';
$_lang['DECEMBER'] = 'Dezember';
$_lang['JANUARY_SHORT'] = 'Jan';
$_lang['FEBRUARY_SHORT'] = 'Feb';
$_lang['MARCH_SHORT'] = 'Mar';
$_lang['APRIL_SHORT'] = 'Apr';
$_lang['MAY_SHORT'] = 'Mai';
$_lang['JUNE_SHORT'] = 'Jun';
$_lang['JULY_SHORT'] = 'Jul';
$_lang['AUGUST_SHORT'] = 'Aug';
$_lang['SEPTEMBER_SHORT'] = 'Sep';
$_lang['OCTOBER_SHORT'] = 'Okt';
$_lang['NOVEMBER_SHORT'] = 'Nov';
$_lang['DECEMBER_SHORT'] = 'Dez';
//Tages Namen
$_lang['MONDAY'] = 'Montag';
$_lang['THUESDAY'] = 'Dienstag';
$_lang['WEDNESDAY'] = 'Mittwoch';
$_lang['THURSDAY'] = 'Donnerstag';
$_lang['FRIDAY'] = 'Freitag';
$_lang['SATURDAY'] = 'Samstag';
$_lang['SUNDAY'] = 'Sonntag';
$_lang['MONDAY_SHORT'] = 'Mon';
$_lang['THUESDAY_SHORT'] = 'Die';
$_lang['WEDNESDAY_SHORT'] = 'Mit';
$_lang['THURSDAY_SHORT'] = 'Don';
$_lang['FRIDAY_SHORT'] = 'Fre';
$_lang['SATURDAY_SHORT'] = 'Sam';
$_lang['SUNDAY_SHORT'] = 'Son';
/* elxis performance monitor */
$_lang['ELX_PERF_MONITOR'] = 'Elxis Performance Monitor';
$_lang['ITEM'] = 'Artikel';
$_lang['INIT_FILE'] = 'Initialisierungs Datei';
$_lang['EXEC_TIME'] = 'Ausführungszeit';
$_lang['DB_QUERIES'] = 'DB Abfragen';
$_lang['ERRORS'] = 'Fehler';
$_lang['SIZE'] = 'Größe';
$_lang['ENTRIES'] = 'Einträge';

/* general */
$_lang['HOME'] = 'Home';
$_lang['YOU_ARE_HERE'] = 'Sie sind hier';
$_lang['CATEGORY'] = 'Kategorie';
$_lang['DESCRIPTION'] = 'Beschreibung';
$_lang['FILE'] = 'Datei';
$_lang['IMAGE'] = 'Bild';
$_lang['IMAGES'] = 'Bilder';
$_lang['CONTENT'] = 'Inhalt';
$_lang['DATE'] = 'Datum';
$_lang['YES'] = 'Ja';
$_lang['NO'] = 'Nein';
$_lang['NONE'] = 'Nichts';
$_lang['SELECT'] = 'Auswählen';
$_lang['LOGIN'] = 'Login';
$_lang['LOGOUT'] = 'Logout';
$_lang['WEBSITE'] = 'Webseite';
$_lang['SECURITY_CODE'] = 'Sicherheitscode';
$_lang['RESET'] = 'Zurücksetzen';
$_lang['SUBMIT'] = 'Senden';
$_lang['REQFIELDEMPTY'] = 'Eines oder mehrere notwendige Felder sind leer!';
$_lang['FIELDNOEMPTY'] = "%s darf nicht leer sein!";
$_lang['FIELDNOACCCHAR'] = "%s enthält ungültige Zeichen!";
$_lang['INVALID_DATE'] = 'Ungültiges Datum!';
$_lang['INVALID_NUMBER'] = 'Ungültige Zahl!';
$_lang['INVALID_URL'] = 'Ungültige URL Adresse!';
$_lang['FIELDSASTERREQ'] = 'Felder die mit einem * gekennzeichnet sind, müssen ausgefüllt werden.';
$_lang['ERROR'] = 'Fehler';
$_lang['REGARDS'] = 'Viele Grüße';
$_lang['NOREPLYMSGINFO'] = 'Bitte antworten Sie nicht auf diese Nachricht da sie nur zu Informationszwecken gesendet wurde.';
$_lang['LANGUAGE'] = 'Sprache';
$_lang['PAGE'] = 'Seite';
$_lang['PAGEOF'] = "Seite %s von %s";
$_lang['OF'] = 'von';
$_lang['DISPLAY_FROM_TO_TOTAL'] = "Anzeigen von %s bis %s von %s .";
$_lang['HITS'] = 'Treffer';
$_lang['PRINT'] = 'Drucken';
$_lang['BACK'] = 'Zurück';
$_lang['PREVIOUS'] = 'Vorheriges';
$_lang['NEXT'] = 'Nächstes';
$_lang['CLOSE'] = 'Schließen';
$_lang['CLOSE_WINDOW'] = 'Fenster schließen';
$_lang['COMMENTS'] = 'Kommentare';
$_lang['COMMENT'] = 'Kommentieren';
$_lang['PUBLISH'] = 'Veröffentlichen';
$_lang['DELETE'] = 'Löschen';
$_lang['EDIT'] = 'Bearbeiten';
$_lang['COPY'] = 'Kopieren';
$_lang['SEARCH'] = 'Suchen';
$_lang['PLEASE_WAIT'] = 'Bitte warten ...';
$_lang['ANY'] = 'Irgendeines';
$_lang['NEW'] = 'Neu';
$_lang['ADD'] = 'Hinzufügen';
$_lang['VIEW'] = 'Ansehen';
$_lang['MENU'] = 'Menü';
$_lang['HELP'] = 'Hilfe';
$_lang['TOP'] = 'Anfang';
$_lang['BOTTOM'] = 'Ende';
$_lang['LEFT'] = 'Links';
$_lang['RIGHT'] = 'Rechts';
$_lang['CENTER'] = 'Mitte';

/* xml */
$_lang['CACHE'] = 'Cache';
$_lang['ENABLE_CACHE_D'] = 'Den Cache hierfür aktivieren?';
$_lang['YES_FOR_VISITORS'] = 'Ja, für Besucher';
$_lang['YES_FOR_ALL'] = 'Ja, für Alle';
$_lang['CACHE_LIFETIME'] = 'Cache Lebenszeit';
$_lang['CACHE_LIFETIME_D'] = 'Zeit, in Minuten, bis der Cache erneuert wird.';
$_lang['NO_PARAMS'] = 'Es fehlen Parameter!';
$_lang['STYLE'] = 'Stil';
$_lang['ADVANCED_SETTINGS'] = 'Fortgeschrittene Einstellungen';
$_lang['CSS_SUFFIX'] = 'CSS Suffix';
$_lang['CSS_SUFFIX_D'] = 'Ein Suffix das zum Modul CSS class hinzugefügt wird.';
$_lang['MENU_TYPE'] = 'Menütyp';
$_lang['ORIENTATION'] = 'Ausrichtung';
$_lang['SHOW'] = 'Zeigen';
$_lang['HIDE'] = 'Verstecken';
$_lang['GLOBAL_SETTING'] = 'Allgemeine Einstellungen';

/* users & authentication */
$_lang['USERNAME'] = 'Benutzername';
$_lang['PASSWORD'] = 'Passwort';
$_lang['NOAUTHMETHODS'] = 'Keine Authentifizierungsmethode eingestellt';
$_lang['AUTHMETHNOTEN'] = 'Authentifizierungsmethode %s is nicht aktiviert';
$_lang['PASSTOOSHORT'] = 'Ihr Passwort ist zu kurz';
$_lang['USERNOTFOUND'] = 'Benutzer nicht gefunden';
$_lang['INVALIDUNAME'] = 'Ungültiger Benutzer';
$_lang['INVALIDPASS'] = 'Ungültiges Passwort';
$_lang['AUTHFAILED'] = 'Authentifizierung fehlgeschlagen';
$_lang['YACCBLOCKED'] = 'Ihr Konto ist gesperrt';
$_lang['YACCEXPIRED'] = 'Ihr Konto ist abgelaufen';
$_lang['INVUSERGROUP'] = 'Ungültige Benutzergruppe';
$_lang['NAME'] = 'Name';
$_lang['FIRSTNAME'] = 'Vorname';
$_lang['LASTNAME'] = 'Nachname';
$_lang['EMAIL'] = 'E-Mail';
$_lang['INVALIDEMAIL'] = 'Ungültige E-Mailaddresse';
$_lang['ADMINISTRATOR'] = 'Administrator';
$_lang['GUEST'] = 'Gast';
$_lang['EXTERNALUSER'] = 'Externer Benutzer';
$_lang['USER'] = 'Benutzer';
$_lang['GROUP'] = 'Gruppe';
$_lang['NOTALLOWACCPAGE'] = 'Sie haben keine Berechtigung diese Seite zu besuchen!';
$_lang['NOTALLOWACCITEM'] = 'Sie haben keine Berechtigung diesen Menupunkt zu besuchen!';
$_lang['NOTALLOWMANITEM'] = 'Sie haben keine Berechtigung diesen Menupunkt zu bearbeiten!';
$_lang['NOTALLOWACTION'] = 'Sie haben keine Berechtigung diese Aktion durchzuführen!';
$_lang['NEED_HIGHER_ACCESS'] = 'Sie benötigen einen höheren Zugangslevel um diese Aktion durchzuführen!';
$_lang['AREYOUSURE'] = 'Sind Sie sicher?';

/* highslide */
$_lang['LOADING'] = 'Loading...';
$_lang['CLICK_CANCEL'] = 'Klicken zum abbrechen';
$_lang['MOVE'] = 'Verschieben';
$_lang['PLAY'] = 'Abspielen';
$_lang['PAUSE'] = 'Pause';
$_lang['RESIZE'] = 'Größe ändern';

/* admin */
$_lang['ADMINISTRATION'] = 'Administration';
$_lang['SETTINGS'] = 'Einstellungen';
$_lang['DATABASE'] = 'Datenbank';
$_lang['ON'] = 'An';
$_lang['OFF'] = 'Aus';
$_lang['WARNING'] = 'Warnung';
$_lang['SAVE'] = 'Sichern';
$_lang['APPLY'] = 'Anwenden';
$_lang['CANCEL'] = 'Abbrechen';
$_lang['LIMIT'] = 'Begrenzung';
$_lang['ORDERING'] = 'Sortierung';
$_lang['NO_RESULTS'] = 'Keine Ergebnisse gefunden!';
$_lang['CONNECT_ERROR'] = 'Verbindungsfehler';
$_lang['DELETE_SEL_ITEMS'] = 'Ausgewählte Objekte löschen?';
$_lang['TOGGLE_SELECTED'] = 'Gewähltes umschalten';
$_lang['NO_ITEMS_SELECTED'] = 'Keine Objekte ausgewählt!';
$_lang['ID'] = 'Id';
$_lang['ACTION_FAILED'] = 'Vorgang fehlgeschlagen!';
$_lang['ACTION_SUCCESS'] = 'Vorgang erfolgreich abgeschlossen!';
$_lang['NO_IMAGE_UPLOADED'] = 'Kein Bild hochgeladen';
$_lang['NO_FILE_UPLOADED'] = 'Keine Datei hochgeladen';
$_lang['MODULES'] = 'Module';
$_lang['COMPONENTS'] = 'Komponenten';
$_lang['TEMPLATES'] = 'Templates';
$_lang['SEARCH_ENGINES'] = 'Suchmaschinen';
$_lang['AUTH_METHODS'] = 'Authentifizierungs methoden';//2 words because it is too long for the side menu
$_lang['CONTENT_PLUGINS'] = 'Inhaltsplugins';
$_lang['PLUGINS'] = 'Plugins';
$_lang['PUBLISHED'] = 'Veröffentlicht';
$_lang['ACCESS'] = 'Access';
$_lang['ACCESS_LEVEL'] = 'Access level';
$_lang['TITLE'] = 'Titel';
$_lang['MOVE_UP'] = 'Nach oben verschieben';
$_lang['MOVE_DOWN'] = 'Nach unten verschieben';
$_lang['WIDTH'] = 'Breite';
$_lang['HEIGHT'] = 'Höhe';
$_lang['ITEM_SAVED'] = 'Punkt gespeichert';
$_lang['FIRST'] = 'Erstes';
$_lang['LAST'] = 'Letztes';
$_lang['SUGGESTED'] = 'Vorgeschlagen';
$_lang['VALIDATE'] = 'Gültigkeit';
$_lang['NEVER'] = 'Niemals';
$_lang['ALL'] = 'Alles';
$_lang['ALL_GROUPS_LEVEL'] = "Alle Gruppenlevel von %s";
$_lang['REQDROPPEDSEC'] = 'Ihre Anfrage wurde aus Sicherheitsgründen gelöscht. Bitte erneut versuchen.';
$_lang['PROVIDE_TRANS'] = 'Bitte stellen Sie eine Übersetzung zur Verfügung!';
$_lang['AUTO_TRANS'] = 'Automatische Übersetzung';
$_lang['STATISTICS'] = 'Statistiken';
$_lang['UPLOAD'] = 'Hochladen';
$_lang['MORE'] = 'Weiter';
//Elxis 4.2
$_lang['TRANSLATIONS'] = 'Übersetzungen';
$_lang['CHECK_UPDATES'] = 'Updates überprüfen';
$_lang['TODAY'] = 'Heute';
$_lang['YESTERDAY'] = 'Gestern';
//Elxis 4.3
$_lang['PUBLISH_ON'] = 'Veröffentlichen am';
$_lang['UNPUBLISHED'] = 'Unveröffentlicht';
$_lang['UNPUBLISH_ON'] = 'Deaktivieren am';
$_lang['SCHEDULED_CRON_DIS'] = 'Es gibt % s geplante Aktionen, aber Cron-Jobs sind deaktiviert!';
$_lang['CRON_DISABLED'] = 'Cron-Jobs sind deaktiviert!';
$_lang['ARCHIVE'] = 'Archiv';
$_lang['RUN_NOW'] = 'Jetzt ausführen';
$_lang['LAST_RUN'] = 'Letzte Ausführung';
$_lang['SEC_AGO'] = 'vor %s Sek';
$_lang['MIN_SEC_AGO'] = 'vor %s Min und %s Sek';
$_lang['HOUR_MIN_AGO'] = 'vor 1 Stunde und %s Min';
$_lang['HOURS_MIN_AGO'] = 'vor %s Stunden und %s Min';
$_lang['CLICK_TOGGLE_STATUS'] = 'Klicken Sie, um den Status zu wechseln';
//Elxis 4.5
$_lang['IAMNOTA_ROBOT'] = 'Ich bin kein Roboter';
$_lang['VERIFY_NOROBOT'] = 'Bitte bestätigen Sie, dass Sie kein Roboter sind!';
$_lang['CHECK_FS'] = 'Dateien überprüfen';
//Elxis 5.0
$_lang['TOTAL_ITEMS'] = '%s Elemente';
$_lang['SEARCH_OPTIONS'] = 'Suchoptionen';
$_lang['FILTERS_HAVE_APPLIED'] = 'Filter wurden angewendet';
$_lang['FILTER_BY_ITEM'] = 'Nach diesem Element filtern';
$_lang['REMOVE_FILTER'] = 'Filter entfernen';
$_lang['TOTAL'] = 'Total';
$_lang['OPTIONS'] = 'Optionen';
$_lang['DISABLE'] = 'Deaktivieren';
$_lang['REMOVE'] = 'Löschen';
$_lang['ADD_ALL'] = 'Alles hinzufügen';
$_lang['TOMORROW'] = 'Morgen';
$_lang['NOW'] = 'Jetzt';
$_lang['MIN_AGO'] = 'vor 1 minute';
$_lang['MINS_AGO'] = 'vor %s Minuten';
$_lang['HOUR_AGO'] = 'Vor 1 Stunde';
$_lang['HOURS_AGO'] = 'Vor %s Stunden';
$_lang['IN_SEC'] = 'In %s sec';
$_lang['IN_MINUTE'] = 'In 1 Minute';
$_lang['IN_MINUTES'] = 'In %s Minuten';
$_lang['IN_HOUR'] = 'In 1 Stunde';
$_lang['IN_HOURS'] = 'In %s Stunden';
$_lang['OTHER'] = 'Andere';
$_lang['DELETE_CURRENT_IMAGE'] = 'Aktuelles Bild löschen';
$_lang['NO_IMAGE_FILE'] = 'Keine Bilddatei!';
$_lang['SELECT_FILE'] = 'Datei auswählen';

?>