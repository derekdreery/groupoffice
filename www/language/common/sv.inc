<?php
//Uncomment this line in new translations!
require_once($GO_LANGUAGE->get_fallback_base_language_file('common'));
$htmldirection='ltr';
$charset = 'UTF-8';
//main language menu file register login and other non logged in pages
//common
$cmdLogin = "Login";
$cmdSave = "Spara";
$cmdBack = "Tillbaka";
$cmdCancel = "Avbryt";
$cmdClose = "Stäng";
$cmdOk = "Ok";
$cmdDelete = "Radera";
$cmdNext = "Nästa";
$cmdPrevious = "Föregående";
$cmdContinue = "Fortsätt";
$cmdUpdate = "Synkronisera";
$cmdApply = "Bekräfta";
$cmdYes = "Ja";
$cmdNo = "Nej";
$cmdAdd = 'Lägg till';
$cmdEdit = 'Editera';
$cmdInvertSelection = 'Inverta markering';

$strEdit = "Editera";
$strThis = 'denna';
$strItems = 'sak';
$strDescription = 'Beskrivning';

$strName = "Namn";
$strUser = "Användare";
$strUsername = "Användarnamn";
$strPassword = "Lösenord";
$strAuthcode = "Auktoriseringskod";
$strCountry = "Land";
$strState = "Stat";
$strCity = "Stad";
$strZip = "Postnummer";
$strAddress = "Gatuadress";
$strEmail = "E-post";
$strPhone = "Telefon";
$strWorkphone = "Telefon arbete";
$strCellular = "Mobil";
$strCompany = "Företag";
$strDepartment = "Avdelning";
$strFunction = "Funktion";
$strQuestion = "Hemlig fråga";
$strAnswer = "Svar";
$strFax = "Fax";
$strWorkFax = "Fax arbete";
$strHomepage = "Hemsida";
$strWorkAddress = "Adress arbete";
$strWorkZip = "Postnummer arbete";
$strWorkCountry = "Land arbete";
$strWorkState = "Stat arbete";
$strWorkCity = "Stad arbete";
$strPhoto = "Foto";
$strComments = "Kommentarer";
$strProperties = 'Egenskaper';
$strDeleteItem = "Ta bort";
$strDate = "Datum";
$strSaveSuccess = "Data sparades";
$strSaveError = "Ett fel uppstod vid sparning av data.";
$strDataError = "Ett fel uppstod vid läsning av data.";
$strDeleteError = "Kunde inte ta bort data.";
$strAclError = "Access Kontrols fel";
$strDateError = "Du skrev in ett felaktigt datum";
$strAccessDenied = "Access nekad";
$AccessDenied_text = "Du har inte tillstånd att se denna avdelning av siten eller att utföra denna åtgärd.<br />En annan användare har begränsat tillträdet till informationen du sökte.";
$invalid_chars = "Följande tecken är inte tillåtna";
$strGroups = "Grupper";
$strOwner = "Ägare";
$strDeletePrefix = "Är du säker på att du vill ta bort ";
$strDeleteSuffix = "?";
//menu
$menu_logout = "Logga ut";
$menu_applications = "Programvaror";
$menu_help = "Hjälp";
$menu_about = "Om";
$menu_documentation = "Dokumentation";
$menu_configuration = "Konfiguration";
$menu_groups = "Användargrupper";
$menu_accounts = "Ditt&nbsp;konto";
$menu_preferences = "Preferenser";
$menu_search_discussions = "Diskussioner";
$menu_search_files = "Filer&nbsp;och&nbsp;kataloger";
$menu_search_web = "Webben";
$menu_filetypes = "Filtyper";
$menu_discussions = "Diskussions&nbsp;forum";

$menu_users = "Hantera användare";
$menu_new = "Ny";
$menu_message = "Meddelande";
$menu_contact = "Kontakt";
$menu_event = "Händelse";
$menu_bookmark = "Bokmärke";
$menu_topics = "Rubriker";
$menu_support = "Support";
$menu_projects = "Projekt";
$menu_sysinfo = "Visa systeminformation";
$menu_admin = 'Administrators meny';
$menu_modules = 'Lägg till/Ta bort moduler';
$menu_manual = 'Manual';

//class validate
$error_required = "Du specifierade inte det fältet som krävs nedanför.";
$error_match_auth = "Auktoriseringskoderna matchar inte.";
$error_match_pass = "Lösenorden överensstämmer inte.";
$error_min_length = "Minst antal tecken av fältet nedan:";
$error_max_length = "Max antal tecken av fältet nedan:";
$error_email = "E-postadressen du angav är ogilitig.";
$error_email_exists = "E-postadressen du angav är redan registrerad.";
$error_username_exists = "Tyvär, användarnamnet du angav existerar redan. Var god välj ett annat.";
$errors_in_form = "Det finns fel i formuläret. Korrigera dessa och pröva igen.";
$error_missing_field = "Du fyllde inte i alla fält som krävs.";

//acl control
$acl_control_auth_groups = "Auktoriserade grupper";
$acl_control_auth_users = "Auktoriserade användare";
$acl_control_no_contacts = "Du har inga medlemskontakter att välja.<br />Använd adressboken för att lägga till medlemmar till dina kontakter.";

$strShowProfile = "Kolla profil";
$strShowGroup = "Kolla gruppanvändare";
$strProcessingTime = "Laddningen av denna sida tog ";
$strLoggedInAs = "Inloggad som ";
$strAt = " den ";
$strEmailTo = "Skicka E-postmeddelande till: ";
$strProtectedUser = "Skyddad användare";

$http_not_found = "Dokumentet hittades inte";
$http_not_found_text = "Det begärda dokumentet hittades inte. Du kanske skrev fel URL.";
$http_error = "Internt serverfel";
$http_bad_request = "Felaktig begäran";
$strAddContact = "Lägg till kontakter";

$strReadRights = "Läsrättigheter";
$strWriteRights = "Skrivrättigheter";
$strPermissions = 'rättigheter';

$days[0]="Sö";
$days[1]="Må";
$days[2]="Ti";
$days[3]="On";
$days[4]="To";
$days[5]="Fr";
$days[6]="Lö";

$months[0]="Januari";
$months[1]="Februari";
$months[2]="Mars";
$months[3]="April";
$months[4]="Maj";
$months[5]="Juni";
$months[6]="Juli";
$months[7]="Augusti";
$months[8]="September";
$months[9]="Oktober";
$months[10]="November";
$months[11]="December";

$full_days[0] = "Söndag";
$full_days[1] = "Måndag";
$full_days[2] = "Tisdag";
$full_days[3] = "Onsdag";
$full_days[4] = "Torsdag";
$full_days[5] = "Fredag";
$full_days[6] = "Lördag";
$strLoadTime = 'Genererad i';
$strDescription = 'Beskrivning';

$lang_modules['addressbook'] = "Adressbok";
$cmdShowAll = 'Visa alla';
$cmdSearch = 'Sök';

//translation required
$cmdYesToAll = 'Ja till alla';
$cmdNoToAll = 'Nej til alla';

$short_months[0]="Jan";
$short_months[1]="Feb";
$short_months[2]="Mar";
$short_months[3]="Apr";
$short_months[4]="Maj";
$short_months[5]="Jun";
$short_months[6]="Jul";
$short_months[7]="Aug";
$short_months[8]="Sep";
$short_months[9]="Okt";
$short_months[10]="Nov";
$short_months[11]="Dec";

$strToday = 'Idag';

$cmdPrint = 'Skriv ut';
$strFirstName = 'Tilltalsnamn';
$strLastName = 'Efternamn';
$strInitials = 'Initialer';
$strSex = 'Kön';
$strBirthday = 'Födelsedag';
$strSexes['M'] = 'Man';
$strSexes['F'] = 'Kvinna';

$strTitle = 'Titel';
$strBeginning = 'Början';
$strMiddleName = 'Andra&nbsp;namn';
$sir_madam['M'] = 'sir';
$sir_madam['F'] = 'madam';
$strModifiedAt = 'Ändrad';
$strCreatedAt = 'Skapad';

$htmlarea_language_name = 'sv';
$strLocalization = 'Lokalisation';


//look up the language in controls/jscalendar/lang
$jscalendar_language_name = 'calendar-en.js';


//2.01 translations

//look up language in 'classes/phpmailer/language'
$php_mailer_lang = 'sv';
$strEmailFail = 'Misslyckades med att skicka e-posten.';

//for people that want to use ktmlarea
$ktml_language = 'svenska';

$ml_host_unreachable = ' går inte att nå. Automatisk hämtning av e-post är avaktiverat.';
$strRegion = 'Region';

//2.05
$cmdReset = 'Reset';
$strMyName = 'Mitt namn';

//2.06
$strDeleteRow = 'Är du säker på att du vill ta bort denna rad?';
$strAddressNo = 'hus nummer';
$strWorkAddressNo = 'Arbetets hus nummer';

$strPostAddress = 'Post adress';
$strPostAddressNo = 'Post nummer till huset';
$strPostCity = 'Post stad';
$strPostState = 'Post stat';
$strPostCountry = 'Post land';
$strPostZip = 'Post nummer';
$strVisitAddress = 'Besöks adress';
$strPostAddress = 'Post adress';


$login_register = 'Click here to sign up.';


//modules
$lang_modules['scheduler'] = 'Schemaläggare';
$lang_modules['calendar'] = "Kalender";
$lang_modules['filesystem'] = 'Filer';
$lang_modules['email'] = 'E-post';
$lang_modules['projects'] = 'Projekt';
$lang_modules['search'] = 'Sök';
$lang_modules['contacts'] = "Adressbok";
$lang_modules['cms'] = "Websiter";
$lang_modules['notes'] = "Anteckningar";
$lang_modules['bookmarks'] = "Bokmärken";
$lang_modules['summary'] = "Sammanfattning";
$lang_modules['todos'] = "Uppgifter";
$lang_modules['opentts'] = "Helpdesk";
$lang_modules['products'] = "Mjukvaruaffär";

$cmdVcard = 'vCard';

//2.07
$strNotEnoughLicenses = 'Du har inte tillräckligt med licenser för att använda denna del av'.
												' Group-Office. Kontakta sales@intermesh.nl om du vill '.
												'köpa ytterligare licenser.';
												
//2.08
$alphabet = 'A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z,Å,Ä,Ö';
									
$strContact = 'Kontakt';
$strRelatedTo = 'Relaterat till';
$strProject = 'Projekt';

$strNewProject = 'Nytt projekt';
$strNewEvent = 'Ny händelse';
$strNewTodo = 'Ny uppgift';
$strNewNote = 'Ny anteckning';

$strActivities = 'Aktiviteter';
$strFile = 'Fil';

//2.10
$cmdCopy = 'Kopiera';
$strProjects = 'Projekt';
$strFiles = 'Filer';
$cmdSaveNew = 'Spara & Ny';
$strAvailability = 'Tillgänglighet';
$strCheckAvailabilityFor = 'Undersök tillgängligheten för';
$cmdPleaseSelect = 'Var snäll och välj...';

//2.11
$strNewEmail = 'Ny e-post';
$strConsistencyCheck = "Konsistens test";
$strSearchAll = 'Alla fält';
$sync_download_clients = 'Ladda ner synchronisationsklienter';

//2.12
$strNoData = 'Inga data hittades';
?>
