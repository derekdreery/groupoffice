<?php
//Uncomment this line in new translations! LI 21.01.2006
require_once($GO_LANGUAGE->get_fallback_base_language_file('common'));
$htmldirection='ltr';
$charset = 'UTF-8';
//main language menu file register login and other non logged in pages
//common
$cmdLogin = "Kirjaudu";
$cmdSave = "Talleta";
$cmdBack = "Takaisin";
$cmdCancel = "Keskeytä";
$cmdClose = "Sulje";
$cmdOk = "OK";
$cmdDelete = "Poista";
$cmdNext = "Seuraava";
$cmdPrevious = "Edellinen";
$cmdContinue = "Jatka";
$cmdUpdate = "Synkronoi";
$cmdApply = "Käytä & Uusi";
$cmdYes = "Kyllä";
$cmdNo = "Ei";
$cmdAdd = 'Lisää';
$cmdEdit = 'Muokkaa';
$cmdInvertSelection = 'Käänteinen valinta';

$strEdit = "Muokkaa";
$strThis = 'tämä';
$strItems = 'kohde';
$strDescription = 'Kuvaus';

$strName = "Nimi";
$strUser = "Käyttäjä";
$strUsername = "Käyttäjätunnus";
$strPassword = "Salasana";
$strAuthcode = "Auktorisointi koodi";
$strCountry = "Maa";
$strState = "Osavaltio";
$strCity = "Kaupunki";
$strZip = "Postinumero";
$strAddress = "Katuosoite";
$strEmail = "Sähköpostiposti";
$strPhone = "Puhelin";
$strWorkphone = "Puhelin työ";
$strCellular = "Kännykkä";
$strCompany = "Yritys";
$strDepartment = "Osasto";
$strFunction = "Tehtävä";
$strQuestion = "Salainen kysymys";
$strAnswer = "Vastaus";
$strFax = "Faksi";
$strWorkFax = "Faksi työ";
$strHomepage = "Kotisivu";
$strWorkAddress = "Työ Osoite";
$strWorkZip = "Työ postinumero";
$strWorkCountry = "Työ maa";
$strWorkState = "Työ osavaltio";
$strWorkCity = "Työ kaupunki";
$strPhoto = "Valokuva";
$strComments = "Kommentti";
$strProperties = 'Ominaisuudet';
$strDeleteItem = "Poista";
$strDate = "Päivämäärä";
$strSaveSuccess = "Tallennus onnistui";
$strSaveError = "Tallennus epäonnistui.";
$strDataError = "Tietojen luku ei onnistunut.";
$strDeleteError = "Tietojen poisto ei onnistunut.";
$strAclError = "Access Controls vika";
$strDateError = "Syötit viallisen päivämäärän";
$strAccessDenied = "Käyttölupa evätty";
$AccessDenied_text = "Sinulla ei ole lupaa nähdä tätä osastoa tai suorittaa tätä toimenpidettä.<br />Toinen käyttäjä on rajoittanut hakemasi tiedon käyttöoikeutta.";
$invalid_chars = "Seuraavat merkit eivät ole sallittujä";
$strGroups = "Ryhmät";
$strOwner = "Omistaja";
$strDeletePrefix = "Oletko varma että haluat poistaa ";
$strDeleteSuffix = "?";
//menu
$menu_logout = "Kirjoittaudu ulos";
$menu_applications = "Ohjelmistot";
$menu_help = "Apu";
$menu_about = "Ohje";
$menu_documentation = "Dokumentaatio";
$menu_configuration = "Asetukset";
$menu_groups = "Hallinnoi käyttäjäryhmiä";
$menu_accounts = "Muokka tili asetuksia";
$menu_preferences = "Oma oletusasetukset";
$menu_search_discussions = "Keskustelu";
$menu_search_files = "Tiedostot&nbsp;ja&nbsp;hakemistot";
$menu_search_web = "Internet";
$menu_filetypes = "Tiedostotyypit";
$menu_discussions = "Keskustelu&nbsp;ryhmä";

$menu_users = "Hallinnoi käyttäjiä";
$menu_new = "Uusi";
$menu_message = "Viesti";
$menu_contact = "Yhteystieto";
$menu_event = "Tapahtuma";
$menu_bookmark = "Suosikit";
$menu_topics = "Otsikot";
$menu_support = "Tuki";
$menu_projects = "Projektit";
$menu_sysinfo = "Järjestelmätiedot";
$menu_admin = 'Järjestelmä valikko';
$menu_modules = 'Lisää/poista moduuleja';
$menu_manual = 'Käyttöohje';

//class validate
$error_required = "Et täyttänyt alla olevaa vaadittua kenttää.";
$error_match_auth = "Auktorisointikoodit eivät täsmää.";
$error_match_pass = "Salasanat ei täsmää.";
$error_min_length = "Kentän pienin merkkimäärä:";
$error_max_length = "Kentän suurin merkkimäärä:";
$error_email = "Antamasi sähköpostiosoite ei ollut kelvollinen.";
$error_email_exists = "Antamasi sähköpostiosoite on jo rekisteröity.";
$error_username_exists = "Antamasi käyttäjätunnus on jo valitettavasi käytössä. Ole hyvä ja valitse toinen.";
$errors_in_form = "Lomakkeella esintyy virheitä. Korjaa ne ja yritä uudelleen.";
$error_missing_field = "et täyttänyt kaikkia vaadittavia kenttiä.";

//acl control
$acl_control_auth_groups = "Auktorisoidut ryhmät";
$acl_control_auth_users = "Auktorisoidut käyttäjät";
$acl_control_no_contacts = "Sinulla ei ole jäsenyhteyksiä valittavan.<br />Käytä osoitekirjaa lisätäksesi jäseniä yhteystietoihisi.";

$strShowProfile = "Katso profiili";
$strShowGroup = "Katso ryhmä jäsenet";
$strProcessingTime = "Tämän sivulataamien kesti ";
$strLoggedInAs = "Kirjautunut ";
$strAt = " ";
$strEmailTo = "Lähetä sähköposti: ";
$strProtectedUser = "Suojattu käyttäjä";

$http_not_found = "Dokumenttia ei löytynyt";
$http_not_found_text = "Pyydettyä dokumenttia ei löytynyt. Annoit ehkä väärän URL.";
$http_error = "Internt palvelin vika";
$http_bad_request = "Huono pyyntö";
$strAddContact = "Lisää yhteystietoihin";

$strReadRights = "Lukuoikeus";
$strWriteRights = "Kirjoitusoikeus";
$strPermissions = 'Oikeudet';

$days[0]="Su";
$days[1]="Ma";
$days[2]="Ti";
$days[3]="Ke";
$days[4]="To";
$days[5]="Pe";
$days[6]="La";

$months[0]="Tammikuu";
$months[1]="Helmikuu";
$months[2]="Maalsikuu";
$months[3]="Huhtikuu";
$months[4]="Toukokuu";
$months[5]="Kesäkuu";
$months[6]="Heinäkuu";
$months[7]="Elokuu";
$months[8]="Syyskuu";
$months[9]="Lokakuu";
$months[10]="Marraskuu";
$months[11]="Joulukuu";

$full_days[0] = "Sunnuntai";
$full_days[1] = "Maanantai";
$full_days[2] = "Tiistai";
$full_days[3] = "Keskiviikko";
$full_days[4] = "Torstai";
$full_days[5] = "Perjantai";
$full_days[6] = "Launtai";
$strLoadTime = 'Luotu';
$strDescription = 'Kuvaus';



$lang_modules['addressbook'] = "Osoitekirja";
$cmdShowAll = 'Näytä kaikki';
$cmdSearch = 'Etsi';

//translation required
$cmdYesToAll = 'Kyllä kaikkiin';
$cmdNoToAll = 'Ei kaikkiin';

$short_months[0]="Tam";
$short_months[1]="Hel";
$short_months[2]="Maa";
$short_months[3]="Huh";
$short_months[4]="Tou";
$short_months[5]="Kes";
$short_months[6]="Hei";
$short_months[7]="Elo";
$short_months[8]="Syy";
$short_months[9]="Lok";
$short_months[10]="Mar";
$short_months[11]="Jou";

$strToday = 'Tänään';

$cmdPrint = 'Tulosta';
$strFirstName = 'Etunimi';
$strLastName = 'Sukunimi';
$strInitials = 'Puumerkki';
$strSex = 'Sukupuoli';
$strBirthday = 'Syntymäpäivä';
$strSexes['M'] = 'Mies';
$strSexes['F'] = 'Nainen';

$strTitle = 'Titteli';
$strBeginning = 'Aloitus';
$strMiddleName = 'Toinen&nbsp;nimi';
$sir_madam['M'] = 'Herra'; //'Herra';
$sir_madam['F'] = 'Rouva'; //'Rouva';
$strModifiedAt = 'Muutettu';
$strCreatedAt = 'Luotu';

$htmlarea_language_name = 'fi';
$strLocalization = 'Paikallistus';


//look up the language in controls/jscalendar/lang
$jscalendar_language_name = 'calendar-en.js';
//$jscalendar_language_name = 'calendar-fi.js';


//2.01 translations

//look up language in 'classes/phpmailer/language'
$php_mailer_lang = 'fi';
$strEmailFail = 'Sähköpostin lähetys epäonnistui.';

//for people that want to use ktmlarea
$ktml_language = 'suomi';

$ml_host_unreachable = ' ei ole käytettävissä. Auromaattinen sähköpostien haku on pois käytöstä.';
$strRegion = 'Alue';

//2.05
$cmdReset = 'Resetoi';
$strMyName = 'Nimeni';

//2.06
$strDeleteRow = 'Oletko varma että haluat poistaa tämän rivin?';
$strAddressNo = 'Katunumero';
$strWorkAddressNo = 'Työ katunumero';

$strPostAddress = 'Postiosoite';
$strPostAddressNo = 'Posti katunumero';
$strPostCity = 'Posti kapunki';
$strPostState = 'Posti osavaltio';
$strPostCountry = 'Posti maa';
$strPostZip = 'Posti numero';
$strVisitAddress = 'Käyntiosoite';
$strPostAddress = 'Postiosoite';

$login_register = 'Paina tästä rekisteröityäksesi.';

//modules
$lang_modules['scheduler'] = 'Aikataulu';
$lang_modules['calendar'] = "Kalenteri";
$lang_modules['filesystem'] = 'Tiedostot';
$lang_modules['email'] = 'Sähköposti';
$lang_modules['projects'] = 'Projekti';
$lang_modules['search'] = 'Etsi';
$lang_modules['contacts'] = "Osoitekirja";
$lang_modules['cms'] = "Verkkosivut";
$lang_modules['notes'] = "Muistiinpanot";
$lang_modules['bookmarks'] = "Suosikit";
$lang_modules['summary'] = "Yhteenveto";
$lang_modules['todos'] = "Tehtävät";
$lang_modules['opentts'] = "Helpdesk";
$lang_modules['products'] = "Ohjelmisto kauppa";

$cmdVcard = 'vCard';

//2.07
$strNotEnoughLicenses = 'Sinulla ei ole tarpeeksi lisensessejä käyttääksesi tätä osa'.
												' Group-Officesta. Ota yhteys sales@intermesh.nl jos sinä haluat '.
												'osataa lisää lisensejä.';
												
//2.08
$alphabet = 'A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z,Å,Ä,Ö';
									
$strContact = 'Yhteys';
$strRelatedTo = 'Liittyy';
$strProject = 'Projekti';

$strNewProject = 'Uusi projekti';
$strNewEvent = 'Uusi tapahtuma';
$strNewTodo = 'Uusi tehtävä';
$strNewNote = 'Uusi muistiinpano';

$strActivities = 'Aktiviteetit';
$strFile = 'Tiedosto';

//2.10
$cmdCopy = 'Kopioi';
$strProjects = 'Projekti';
$strFiles = 'Tiedostot';
$cmdSaveNew = 'Talleta & Uusi';
$strAvailability = 'Käytettävyys';
$strCheckAvailabilityFor = 'Tarkista käytettävyys';
$cmdPleaseSelect = 'Ole ystävällinen ja valitse...';

//2.11
$strNewEmail = 'Uusi sähköposti';
$strConsistencyCheck = "Eheystarkistus";
$strSearchAll = 'Kaikki kentät';
$sync_download_clients = 'Lataa synkronointi klientit';

//2.12
$strNoData = 'Tietoja ei löytynyt';
//2.13
$strSynchronization = 'Synkronointi';
$strSnooze = 'Torkku';
$strDismiss = 'Hylkää';

//2.14
$strUpload = 'Lataa tiedoto';
$cmdInsert = 'Liitä';
$strText = 'Teksti';
$strType = 'Tyyppi';

$strNoItemSelected = 'Et valinnut ';
$strSelectedItem = 'valitun kohteen';

$strKeyword = 'Avainsana';

$strNoActivities = 'Ei aktiviteettejä tälle kohteelle ';

$strCopy = 'Kopio'; //not a verb

//2.15
$strBank = 'Pankki';
$strBankNo = 'Pankki numero';

$lang_modules['comics'] = "Päivän piirretyt";

$strMonth = 'Kuukausi';
$strYear = 'Vuosi';
$strDay = 'Päivä';
$strHour = 'Tunti';
$strMinute = 'Minuutti';

$strTwelveHourFormat = '12 tuntia muoto';
$strTwentyfourHourFormat = '24 tuntia muoto';

?>
