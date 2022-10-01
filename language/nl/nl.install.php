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


$_lang = array();
$_lang['INSTALLATION'] = 'Installatie';
$_lang['STEP'] = 'Stap';
$_lang['VERSION'] = 'Versie';
$_lang['VERSION_CHECK'] = 'versie controle';
$_lang['STATUS'] = 'Status';
$_lang['REVISION_NUMBER'] = 'Revisienummer';
$_lang['RELEASE_DATE'] = 'Release datum';
$_lang['ELXIS_INSTALL'] = 'Elxis installatie';
$_lang['LICENSE'] = 'licentie';
$_lang['VERSION_PROLOGUE'] = 'Je gaat het Elxis CMS installeren. De exacte versie van de Elxis kopie die U gaat installeren staat hieronder weergegeven. Zorg ervoor dat dit de laatst vrijgegeven Elxis versie is op <a href="http://www.elxis.org" target="_blank"> elxis.org </a>.';
$_lang['BEFORE_BEGIN'] = 'Voordat u begint';
$_lang['BEFORE_DESC'] = 'Alvorens verder te gaan, lees aandachtig het volgende.';
$_lang['DATABASE'] = 'Database';
$_lang['DATABASE_DESC'] = 'Maak een lege database die gebruikt zal worden door Elxis om uw gegevens in op te slaan. We raden ten zeerste aan een <strong> MySQL </ strong> database te gebruiken. Hoewel Elxis backend ondersteuning heeft voor andere soorten databanken zoals PostgreSQL en SQLite 3, het is enkel goed getest met MySQL. Je kan een lege MySQL database maken vanuit uw hosting control panel (Cpanel, Plesk, ISP Config, etc) of vanuit phpMyAdmin of andere database-management tool. Kies een <strong> naam </ strong> voor uw database en maak het. Hierna maak je een database <strong> gebruiker </ strong> en wijs hem toe aan uw nieuw aangemaakte database. Maak een notitie van de database naam, de gebruikersnaam en het wachtwoord; je hebt ze later nodig tijdens de installatie.';
$_lang['REPOSITORY'] = 'Repository';
$_lang['REPOSITORY_DESC'] = 'Elxis maakt gebruik van een speciale map om de cache, logbestanden, sessies, back-ups en nog veel meer in op te slaan. Standaard krijgt deze map de naam <strong> repository </ strong> en wordt in de hoofdmap van Elxis geplaatst. Deze map <strong> moet schrijfbaar (writeable)</ strong> zijn! We raden sterk aan om deze map <strong> te hernoemen </ strong> en <strong> te verplaatsen </ strong> naar een plaats die niet bereikbaar is vanaf het web. Als de<strong> open basedir </ strong> bescherming in PHP is ingeschakeld, kan het zijn dat je ook het repository path moet toevoegen in de toegestane paden.';
$_lang['REPOSITORY_DEFAULT'] = 'Repository is op de standaardlocatie!';
$_lang['SAMPLE_ELXPATH'] = 'Voorbeeld Elxis path';
$_lang['DEF_REPOPATH'] = 'Standaard repository path';
$_lang['REQ_REPOPATH'] = 'Aanbevolen repository path';
$_lang['CONTINUE'] = 'Doorgaan';
$_lang['I_AGREE_TERMS'] = 'Ik heb de EPL voorwaarden gelezen en begrepen en ga akkoord met de EPL voorwaarden.';
$_lang['LICENSE_NOTES'] = 'Elxis CMS is gratis software vrijgegeven onder de <strong> Elxis Public License </ strong> (EPL). Om de installatie van Elxis voort te zetten en te gebruiken dient u akkoord te gaan met de voorwaarden en condities van EPL. Lees aandachtig de Elxis licentie en als u akkoord bent, zet dan een vinkje in het vakje aan de onderkant van de pagina en klik op Doorgaan. Als je niet akkoord bent, stop dan deze installatie en verwijder de Elxis bestanden.';
$_lang['SETTINGS'] = 'Instellingen';
$_lang['SITE_URL'] = 'Site URL';
$_lang['SITE_URL_DESC'] = 'Zonder slash (bijv. http://www.example.com)';
$_lang['REPOPATH_DESC'] = 'Het absolute pad naar de Elxis repository map. Laat leeg voor standaard-pad en naam.';
$_lang['SETTINGS_DESC'] = 'Stel de vereiste Elxis configuratie parameters in. Sommige parameters moeten voor de installatie van Elxis worden ingesteld. Na voltooiing van de installatie, log in en ga naar de beheer console waar je de overige parameters kan configureren. Dit moet je allereerste taak als beheerder zijn.';
$_lang['DEF_LANG'] = 'Standaard taal';
$_lang['DEFLANG_DESC'] = 'De inhoud is geschreven in de standaard taal. Inhoud in andere talen is de vertaling van de originele inhoud in de standaard taal.';
$_lang['ENCRYPT_METHOD'] = 'Encryptie methode';
$_lang['ENCRYPT_KEY'] = 'Encryptie sleutel';
$_lang['AUTOMATIC'] = 'Automatisch';
$_lang['GEN_OTHER'] = 'Genereer een andere';
$_lang['SITENAME'] = 'Naam van de site';
$_lang['TYPE'] = 'Type';
$_lang['DBTYPE_DESC'] = 'We raden MySQL aan. Enkel de door het systeem en de Elxis installatie ondersteunde stuurprogramma\'s zijn selecteerbaar.';
$_lang['HOST'] = 'Host';
$_lang['TABLES_PREFIX'] = 'Tabellen voorvoegsel';
$_lang['DSN_DESC'] = 'Je kan in de plaats een kant-en-klare "Data Source" naam geven om verbinding te maken met de database.';
$_lang['SCHEME'] = 'Schema';
$_lang['SCHEME_DESC'] = 'Het absolute pad naar een database bestand als u een database gebruikt zoals SQLite.';
$_lang['PORT'] = 'Poort';
$_lang['PORT_DESC'] = 'De standaard poort voor MySQL is 3306. Laat 0 staan voor automatische selectie.';
$_lang['FTPPORT_DESC'] = 'De standaard poort voor FTP is 21. Laat 0 staan voor automatische selectie.';
$_lang['USE_FTP'] = 'Gebruik FTP';
$_lang['PATH'] = 'Path';
$_lang['FTP_PATH_INFO'] = 'Het relatieve pad van de FTP-hoofdmap naar de Elxis installatiemap (bijvoorbeeld: / public_html).';
$_lang['CHECK_FTP_SETS'] = 'Controleer FTP-instellingen';
$_lang['CHECK_DB_SETS'] = 'Controleer database-instellingen';
$_lang['DATA_IMPORT'] = 'Gegevens importeren';
$_lang['SETTINGS_ERRORS'] = 'De instellingen die u gaf bevatten fouten!';
$_lang['NO_QUERIES_WARN'] = 'De initiële gegevens zijn in de database geïnporteerd, maar het ziet er naar uit dat er geen queries zijn uitgevoerd. Controleer of alle gegevens geïmporteerd zijn vooralleer verder te gaan.';
$_lang['RETRY_PREV_STEP'] = 'Probeer vorige stap opnieuw';
$_lang['INIT_DATA_IMPORTED'] = 'Initiële data geïmporteerd in de database.';
$_lang['QUERIES_EXEC'] = "%s SQL queries uitgevoerd."; //translators help: {NUMBER} SQL queries executed
$_lang['ADMIN_ACCOUNT'] = 'Beheerders account';
$_lang['CONFIRM_PASS'] = 'Bevestig wachtwoord';
$_lang['AVOID_COMUNAMES'] = 'Vermijd veel voorkomende gebruikersnamen zoals admin en beheerder.';
$_lang['YOUR_DETAILS'] = 'Uw gegevens';
$_lang['PASS_NOMATCH'] = 'Wachtwoorden komen niet overeen!';
$_lang['REPOPATH_NOEX'] = 'Repository path bestaat niet!';
$_lang['FINISH'] = 'Einde';
$_lang['FRIENDLY_URLS'] = 'SEO vriendelijke URL\'s';
$_lang['FRIENDLY_URLS_DESC'] = 'We raden aan om het in te schakelen. Om te kunnen werken, zal Elxis proberen om het bestand htaccess.txt te hernoemen naar
<strong> .htaccess </ strong>. Als er al een .htaccess bestand staat in dezelfde map zal het worden verwijderd.';
$_lang['GENERAL'] = 'Algemeen';
$_lang['ELXIS_INST_SUCC'] = 'Elxis installatie met succes afgerond.';
$_lang['ELXIS_INST_WARN'] = 'Elxis installatie voltooid met waarschuwingen.';
$_lang['CNOT_CREA_CONFIG'] = 'Kon het bestand <strong> configuration.php </ strong> niet maken in de Elxis hoofdmap.';
$_lang['CNOT_REN_HTACC'] = 'Kon het<strong> htaccess.txt </ strong> bestand niet hernoemen in<strong> .htaccess </ strong>';
$_lang['CONFIG_FILE'] = 'Configuratie bestand';
$_lang['CONFIG_FILE_MANUAL'] = 'Maak configuration.php handmatig, kopieer de volgende code en plak het erin.';
$_lang['REN_HTACCESS_MANUAL'] = 'Wijzig de naam van het bestand <strong> htaccess.txt </ strong> handmatig naar <strong> .htaccess </ strong>';
$_lang['WHAT_TODO'] = 'Wat nu te doen?';
$_lang['RENAME_ADMIN_FOLDER'] = 'Om de veiligheid te verbeteren kunt u de administratie map (<em> estia </ em>) een andere naam geven. Als u dat doet, moet u ook het .htaccess bestand met de nieuwe naam bijwerken.';
$_lang['LOGIN_CONFIG'] = 'Login het administratie gedeelte en stel de overige configuratie opties in.';
$_lang['VISIT_NEW_SITE'] = 'Bezoek uw nieuwe website';
$_lang['VISIT_ELXIS_SUP'] = 'Bezoek Elxis support site';
$_lang['THANKS_USING_ELXIS'] = 'Bedankt om Elxis CMS te gebruiken.';
//Elxis 5.0
$_lang['OTHER_LANGS'] = 'Andere talen';
$_lang['OTHER_LANGS_DESC'] = 'Welke andere talen, behalve de standaard, wilt u dat er beschikbaar zijn?';
$_lang['ALL_LANGS'] = 'Allemaal';
$_lang['NONE_LANGS'] = 'Geen';
$_lang['REMOVE'] = 'Verwijderen';
$_lang['CONFIG_EMAIL_DISPATCH'] = 'E-mailverzending configureren (optioneel)';
$_lang['SEND_METHOD'] = 'Verzend methode';
$_lang['RECOMMENDED'] = 'aanbevolen';
$_lang['SECURE_CONNECTION'] = 'Beveiligde verbinding';
$_lang['AUTH_REQUIRED'] = 'Authenticatie vereist';
$_lang['AUTH_METHOD'] = 'Authenticatie methode';
$_lang['DEFAULT_METHOD'] = 'Standaard';

?>