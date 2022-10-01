<?php 
/**
* @version: 5.0
* @package: Elxis CMS
* @subpackage: Elxis Language
* @author: Elxis Team ( http://www.elxis.org )
* @copyright: (C) 2006-2019 Elxis.org. All rights reserved.
* @description: nl-NL (Nederlands - Nederland) language for Elxis CMS
* @license: Elxis public license http://www.elxis.org/elxis-public-license.html
* @translator: Frank Gijsels ( http://www.onsnet.be/elxis )
*
* ---- THIS FILE MUST BE ENCODED AS UTF-8! ----
*
*****************************************************************************/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


$locale = array('nl_NL.utf8', 'nl_NL.UTF-8', 'nl_NL', 'nl', 'nederlands', 'nederland'); //utf-8 locales array

$_lang = array();
//date formats
$_lang['DATE_FORMAT_BOX'] = 'd-m-Y'; //supported formats: d-m-Y, Y-m-d, d/m/Y, Y/m/d
$_lang['DATE_FORMAT_BOX_LONG'] = 'd-m-Y H:i:s'; //supported formats: d-m-Y H:i:s, Y-m-d H:i:s, d/m/Y H:i:s, Y/m/d H:i:s
$_lang['DATE_FORMAT_1'] = "%d/%m/%Y"; //example: 25/12/2010
$_lang['DATE_FORMAT_2'] = "%b %d, %Y"; //example: Dec 25, 2010
$_lang['DATE_FORMAT_3'] = "%B %d, %Y"; //example: December 25, 2010
$_lang['DATE_FORMAT_4'] = "%b %d, %Y %H:%M"; //example: Dec 25, 2010 12:34
$_lang['DATE_FORMAT_5'] = "%B %d, %Y %H:%M"; //example: December 25, 2010 12:34
$_lang['DATE_FORMAT_6'] = "%B %d, %Y %H:%M:%S"; //example: December 25, 2010 12:34:45
$_lang['DATE_FORMAT_7'] = "%a %b %d, %Y"; //example: Sat Dec 25, 2010
$_lang['DATE_FORMAT_8'] = "%A %b %d, %Y"; //example: Saturday Dec 25, 2010
$_lang['DATE_FORMAT_9'] = "%A %B %d, %Y"; //example: Saturday December 25, 2010
$_lang['DATE_FORMAT_10'] = "%A %B %d, %Y %H:%M"; //example: Saturday December 25, 2010 12:34
$_lang['DATE_FORMAT_11'] = "%A %B %d, %Y %H:%M:%S"; //example: Saturday December 25, 2010 12:34:45
$_lang['DATE_FORMAT_12'] = "%a %B %d, %Y %H:%M"; //example: Sat December 25, 2010 12:34
$_lang['DATE_FORMAT_13'] = "%a %B %d, %Y %H:%M:%S"; //example: Sat December 25, 2010 12:34:45
$_lang['THOUSANDS_SEP'] = ',';
$_lang['DECIMALS_SEP'] = '.';
//month names
$_lang['JANUARY'] = 'Januari';
$_lang['FEBRUARY'] = 'Februari';
$_lang['MARCH'] = 'Maart';
$_lang['APRIL'] = 'April';
$_lang['MAY'] = 'Mei';
$_lang['JUNE'] = 'Juni';
$_lang['JULY'] = 'Juli';
$_lang['AUGUST'] = 'Augustus';
$_lang['SEPTEMBER'] = 'September';
$_lang['OCTOBER'] = 'Oktober';
$_lang['NOVEMBER'] = 'November';
$_lang['DECEMBER'] = 'December';
$_lang['JANUARY_SHORT'] = 'Jan';
$_lang['FEBRUARY_SHORT'] = 'Feb';
$_lang['MARCH_SHORT'] = 'Mrt';
$_lang['APRIL_SHORT'] = 'Apr';
$_lang['MAY_SHORT'] = 'Mei';
$_lang['JUNE_SHORT'] = 'Jun';
$_lang['JULY_SHORT'] = 'Jul';
$_lang['AUGUST_SHORT'] = 'Aug';
$_lang['SEPTEMBER_SHORT'] = 'Sep';
$_lang['OCTOBER_SHORT'] = 'Okt';
$_lang['NOVEMBER_SHORT'] = 'Nov';
$_lang['DECEMBER_SHORT'] = 'Dec';
//day names
$_lang['MONDAY'] = 'Maandag';
$_lang['THUESDAY'] = 'Dinsdag';
$_lang['WEDNESDAY'] = 'Woensdag';
$_lang['THURSDAY'] = 'Donderdag';
$_lang['FRIDAY'] = 'Vrijdag';
$_lang['SATURDAY'] = 'Zaterdag';
$_lang['SUNDAY'] = 'Zondag';
$_lang['MONDAY_SHORT'] = 'Ma';
$_lang['THUESDAY_SHORT'] = 'Di';
$_lang['WEDNESDAY_SHORT'] = 'Wo';
$_lang['THURSDAY_SHORT'] = 'Do';
$_lang['FRIDAY_SHORT'] = 'Vr';
$_lang['SATURDAY_SHORT'] = 'Za';
$_lang['SUNDAY_SHORT'] = 'Zo';
/* elxis performance monitor */
$_lang['ELX_PERF_MONITOR'] = 'Elxis Prestatie Meter';
$_lang['ITEM'] = 'Item';
$_lang['INIT_FILE'] = 'Initialisatie bestand';
$_lang['EXEC_TIME'] = 'Uitvoeringstijd';
$_lang['DB_QUERIES'] = 'DB queries';
$_lang['ERRORS'] = 'Fouten';
$_lang['SIZE'] = 'Grootte';
$_lang['ENTRIES'] = 'Entries';

/* general */
$_lang['HOME'] = 'Home';
$_lang['YOU_ARE_HERE'] = 'U bevindt zich hier';
$_lang['CATEGORY'] = 'Categorie';
$_lang['DESCRIPTION'] = 'Beschrijving';
$_lang['FILE'] = 'Bestand';
$_lang['IMAGE'] = 'Afbeelding';
$_lang['IMAGES'] = 'Afbeeldingen';
$_lang['CONTENT'] = 'Artikelen';
$_lang['DATE'] = 'Datum';
$_lang['YES'] = 'Ja';
$_lang['NO'] = 'Nee';
$_lang['NONE'] = 'Geen';
$_lang['SELECT'] = 'Selecteer';
$_lang['LOGIN'] = 'Inloggen';
$_lang['LOGOUT'] = 'Uitloggen';
$_lang['WEBSITE'] = 'Website';
$_lang['SECURITY_CODE'] = 'Veiligheids code';
$_lang['RESET'] = 'Herstellen';
$_lang['SUBMIT'] = 'Verstuur'; 
$_lang['REQFIELDEMPTY'] = 'Eén of meerdere verplichte velden zijn leeg!';
$_lang['FIELDNOEMPTY'] = "%s kan niet leeg zijn!";
$_lang['FIELDNOACCCHAR'] = "%s bevat niet aanvaardbare tekens!";
$_lang['INVALID_DATE'] = 'Ongeldige datum!';
$_lang['INVALID_NUMBER'] = 'Ongeldig getal!';
$_lang['INVALID_URL'] = 'Ongeldig URL-adres!';
$_lang['FIELDSASTERREQ'] = 'Velden met een sterretje * zijn verplicht.';
$_lang['ERROR'] = 'Fout';
$_lang['REGARDS'] = 'Vriendelijke groeten';
$_lang['NOREPLYMSGINFO'] = 'Gelieve niet te reageren op dit bericht, het werd enkel verstuurd ter informatie.';
$_lang['LANGUAGE'] = 'Taal';
$_lang['PAGE'] = 'Pagina';
$_lang['PAGEOF'] = "Pagina %s van %s";
$_lang['OF'] = 'van';
$_lang['DISPLAY_FROM_TO_TOTAL'] = "Weergeven van %s tot %s van %s items";
$_lang['HITS'] = 'Hits';
$_lang['PRINT'] = 'Afdrukken';
$_lang['BACK'] = 'Terug';
$_lang['PREVIOUS'] = 'Vorige';
$_lang['NEXT'] = 'Volgende';
$_lang['CLOSE'] = 'Sluiten';
$_lang['CLOSE_WINDOW'] = 'Venster sluiten';
$_lang['COMMENTS'] = 'Reacties';
$_lang['COMMENT'] = 'Reactie';
$_lang['PUBLISH'] = 'Publiceren';
$_lang['DELETE'] = 'Verwijderen';
$_lang['EDIT'] = 'Bewerken';
$_lang['COPY'] = 'Kopiëren';
$_lang['SEARCH'] = 'Zoeken';
$_lang['PLEASE_WAIT'] = 'Wacht alstublieft ...';
$_lang['ANY'] = 'Elke';
$_lang['NEW'] = 'Nieuw';
$_lang['ADD'] = 'Toevoegen';
$_lang['VIEW'] = 'Weergave';
$_lang['MENU'] = 'Menu';
$_lang['HELP'] = 'Help';
$_lang['TOP'] = 'Top';
$_lang['BOTTOM'] = 'Bottom';
$_lang['LEFT'] = 'Links';
$_lang['RIGHT'] = 'Rechts';
$_lang['CENTER'] = 'Midden';

/* xml */
$_lang['CACHE'] = 'Cache';
$_lang['ENABLE_CACHE_D'] = 'Cache inschakelen voor dit item?';
$_lang['YES_FOR_VISITORS'] = 'Ja, voor bezoekers';
$_lang['YES_FOR_ALL'] = 'Ja, voor alle';
$_lang['CACHE_LIFETIME'] = 'Cache tijdsduur';
$_lang['CACHE_LIFETIME_D'] = 'Tijd, in minuten, totdat de cache wordt vernieuwd voor dit item.';
$_lang['NO_PARAMS'] = 'Er zijn geen parameters!';
$_lang['STYLE'] = 'Stijl';
$_lang['ADVANCED_SETTINGS'] = 'Geavanceerde instellingen';
$_lang['CSS_SUFFIX'] = 'CSS Achtervoegsel (suffix)';
$_lang['CSS_SUFFIX_D'] = 'Een achtervoegsel (suffix) dat wordt toegevoegd aan de module CSS class.';
$_lang['MENU_TYPE'] = 'Menutype';
$_lang['ORIENTATION'] = 'Oriëntering';
$_lang['SHOW'] = 'Toon';
$_lang['HIDE'] = 'Verberg';
$_lang['GLOBAL_SETTING'] = 'Globale instelling';

/* users & authentication */
$_lang['USERNAME'] = 'Gebruikersnaam';
$_lang['PASSWORD'] = 'Wachtwoord';
$_lang['NOAUTHMETHODS'] = 'Er zijn geen authenticatiemethodes ingesteld';
$_lang['AUTHMETHNOTEN'] = 'Authenticatiemethode %s is niet ingeschakeld';
$_lang['PASSTOOSHORT'] = 'Uw wachtwoord is te kort om acceptabel te zijn';
$_lang['USERNOTFOUND'] = 'Gebruiker niet gevonden';
$_lang['INVALIDUNAME'] = 'Ongeldige gebruikersnaam';
$_lang['INVALIDPASS'] = 'Ongeldig wachtwoord';
$_lang['AUTHFAILED'] = 'Authenticatie mislukt';
$_lang['YACCBLOCKED'] = 'Uw account is geblokkeerd';
$_lang['YACCEXPIRED'] = 'Uw account is verlopen';
$_lang['INVUSERGROUP'] = 'Ongeldige gebruikersgroep';
$_lang['NAME'] = 'Naam';
$_lang['FIRSTNAME'] = 'Voornaam';
$_lang['LASTNAME'] = 'Achternaam';
$_lang['EMAIL'] = 'E-mail';
$_lang['INVALIDEMAIL'] = 'Ongeldig e-mail adres';
$_lang['ADMINISTRATOR'] = 'Administrator';
$_lang['GUEST'] = 'Gast';
$_lang['EXTERNALUSER'] = 'Externe gebruiker';
$_lang['USER'] = 'Gebruiker';
$_lang['GROUP'] = 'Groep';
$_lang['NOTALLOWACCPAGE'] = 'U hebt geen toelating om deze pagina te bekijken!';
$_lang['NOTALLOWACCITEM'] = 'U hebt geen toelating om dit item te bekijken!';
$_lang['NOTALLOWMANITEM'] = 'U hebt geen toelating om dit item te beheren!';
$_lang['NOTALLOWACTION'] = 'U hebt geen toelating om deze actie uit te voeren!';
$_lang['NEED_HIGHER_ACCESS'] = 'Je hebt een hoger toegangsniveau nodig voor deze actie!';
$_lang['AREYOUSURE'] = 'Ben je zeker?';

/* highslide */
$_lang['LOADING'] = 'Loading...';
$_lang['CLICK_CANCEL'] = 'Klik om te annuleren';
$_lang['MOVE'] = 'Verplaatsen';
$_lang['PLAY'] = 'Afspelen';
$_lang['PAUSE'] = 'Pauze';
$_lang['RESIZE'] = 'Formaat wijzigen';

/* admin */
$_lang['ADMINISTRATION'] = 'Administratie';
$_lang['SETTINGS'] = 'Instellingen';
$_lang['DATABASE'] = 'Database';
$_lang['ON'] = 'Aan';
$_lang['OFF'] = 'Uit';
$_lang['WARNING'] = 'Waarschuwing';
$_lang['SAVE'] = 'Opslaan';
$_lang['APPLY'] = 'Toepassen';
$_lang['CANCEL'] = 'Annuleren';
$_lang['LIMIT'] = 'Limiet';
$_lang['ORDERING'] = 'Volgorde';
$_lang['NO_RESULTS'] = 'Geen resultaten gevonden!';
$_lang['CONNECT_ERROR'] = 'Verbindingsfout';
$_lang['DELETE_SEL_ITEMS'] = 'Geselecteerde items verwijderen?';
$_lang['TOGGLE_SELECTED'] = 'Selectie Aan/Uit';
$_lang['NO_ITEMS_SELECTED'] = 'Geen items geselecteerd!';
$_lang['ID'] = 'Id';
$_lang['ACTION_FAILED'] = 'Actie mislukt!';
$_lang['ACTION_SUCCESS'] = 'Actie succesvol afgerond!';
$_lang['NO_IMAGE_UPLOADED'] = 'Geen afbeelding geüpload';
$_lang['NO_FILE_UPLOADED'] = 'Geen bestand geüpload';
$_lang['MODULES'] = 'Modules';
$_lang['COMPONENTS'] = 'Componenten';
$_lang['TEMPLATES'] = 'Templates';
$_lang['SEARCH_ENGINES'] = 'Zoekmachines';
$_lang['AUTH_METHODS'] = 'Authenticatie methodes';
$_lang['CONTENT_PLUGINS'] = 'Plugins';
$_lang['PLUGINS'] = 'Plugins';
$_lang['PUBLISHED'] = 'Gepubliceerd';
$_lang['ACCESS'] = 'Toegang';
$_lang['ACCESS_LEVEL'] = 'Toegangsniveau';
$_lang['TITLE'] = 'Titel';
$_lang['MOVE_UP'] = 'Naar boven';
$_lang['MOVE_DOWN'] = 'Naar beneden';
$_lang['WIDTH'] = 'Breedte';
$_lang['HEIGHT'] = 'Hoogte';
$_lang['ITEM_SAVED'] = 'Item opgeslagen';
$_lang['FIRST'] = 'Eerste';
$_lang['LAST'] = 'Laatste';
$_lang['SUGGESTED'] = 'Voorgesteld';
$_lang['VALIDATE'] = 'Valideer';
$_lang['NEVER'] = 'Nooit';
$_lang['ALL'] = 'Alle';
$_lang['ALL_GROUPS_LEVEL'] = "Alle groepen van niveau %s";
$_lang['REQDROPPEDSEC'] = 'Uw aanvraag is afgewezen om veiligheidsredenen. Gelieve opnieuw te proberen.';
$_lang['PROVIDE_TRANS'] = 'Geef een vertaling!';
$_lang['AUTO_TRANS'] = 'Automatische vertaling';
$_lang['STATISTICS'] = 'Statistieken';
$_lang['UPLOAD'] = 'Upload';
$_lang['MORE'] = 'Meer';
//Elxis 4.2
$_lang['TRANSLATIONS'] = 'Vertalingen';
$_lang['CHECK_UPDATES'] = 'Controleren op updates';
$_lang['TODAY'] = 'Vandaag';
$_lang['YESTERDAY'] = 'Gisteren';
//Elxis 4.3
$_lang['PUBLISH_ON'] = 'Publiceren op';
$_lang['UNPUBLISHED'] = 'Onuitgegeven';
$_lang['UNPUBLISH_ON'] = 'Depubliceren op';
$_lang['SCHEDULED_CRON_DIS'] = 'Er zijn %s geplande items, maar Cron Jobs zijn uitgeschakeld!';
$_lang['CRON_DISABLED'] = 'Cron Jobs zijn uitgeschakeld!';
$_lang['ARCHIVE'] = 'Archief';
$_lang['RUN_NOW'] = 'Νu uitvoeren';
$_lang['LAST_RUN'] = 'Laatste uitvoering';
$_lang['SEC_AGO'] = '%s sec geleden';
$_lang['MIN_SEC_AGO'] = '%s min en %s sec geleden';
$_lang['HOUR_MIN_AGO'] = '1 uur en %s min geleden';
$_lang['HOURS_MIN_AGO'] = '%s uur en %s min geleden';
$_lang['CLICK_TOGGLE_STATUS'] = 'Klik om de status te schakelen';
//Elxis 4.5
$_lang['IAMNOTA_ROBOT'] = 'Ik ben geen robot';
$_lang['VERIFY_NOROBOT'] = 'Bevestig dat u geen robot bent!';
$_lang['CHECK_FS'] = 'Bestanden te controleren';
//Elxis 5.0
$_lang['TOTAL_ITEMS'] = '%s items';
$_lang['SEARCH_OPTIONS'] = 'Zoek opties';
$_lang['FILTERS_HAVE_APPLIED'] = 'Filters zijn toegepast';
$_lang['FILTER_BY_ITEM'] = 'Filter op dit item';
$_lang['REMOVE_FILTER'] = 'Verwijder filter';
$_lang['TOTAL'] = 'Totaal';
$_lang['OPTIONS'] = 'Opties';
$_lang['DISABLE'] = 'Uitschakelen';
$_lang['REMOVE'] = 'Verwijderen';
$_lang['ADD_ALL'] = 'Voeg alles toe';
$_lang['TOMORROW'] = 'Morgen';
$_lang['NOW'] = 'Nu';
$_lang['MIN_AGO'] = '1 minuut geleden';
$_lang['MINS_AGO'] = '%s minuten geleden';
$_lang['HOUR_AGO'] = '1 uur geleden';
$_lang['HOURS_AGO'] = '%s uur geleden';
$_lang['IN_SEC'] = 'In %s sec';
$_lang['IN_MINUTE'] = 'Over 1 minuut';
$_lang['IN_MINUTES'] = 'In %s minuten';
$_lang['IN_HOUR'] = 'In 1 uur';
$_lang['IN_HOURS'] = 'Over %s uur';
$_lang['OTHER'] = 'Andere';
$_lang['DELETE_CURRENT_IMAGE'] = 'Huidige afbeelding verwijderen';
$_lang['NO_IMAGE_FILE'] = 'Geen afbeeldingsbestand!';
$_lang['SELECT_FILE'] = 'Selecteer bestand';

?>