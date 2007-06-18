<?php
//Uncomment this line in new translations!
require_once($GO_LANGUAGE->get_fallback_base_language_file('common'));

$charset = 'UTF-8';
//main language menu file register login and other non logged in pages
//common
$cmdLogin = "Login";
$cmdSave = "Gem";
$cmdBack = "Tilbage";
$cmdCancel = "Annuller";
$cmdClose = "Luk";
$cmdOk = "Ok";
$cmdDelete = "Slet";
$cmdNext = "Næste";
$cmdPrevious = "Forrige";
$cmdContinue = "Fortsæt";
$cmdUpdate = "Synkroniser";
$cmdApply = "Anvend";
$cmdYes = "Ja";
$cmdNo = "Nej";
$cmdAdd = 'Tilføj';
$cmdEdit = 'Rediger';
$cmdInvertSelection = 'Skift valg';

$strEdit = "Rediger";
$strThis = 'disse';
$strItems = 'poster';
$strDescription = 'Beskrivelse';

$strName = "Navn";
$strUser = "Bruger";
$strUsername = "Brugernavn";
$strPassword = "Adgangskode";
$strAuthcode = "Autorisationskode";
$strCountry = "Land";
$strState = "Stat";
$strCity = "By";
$strZip = "Postnummer";
$strAddress = "Vej";
$strEmail = "E-mail";
$strPhone = "Telefon";
$strWorkphone = "Tlf. arbejde";
$strCellular = "Mobil";
$strCompany = "Firma";
$strDepartment = "Afdeling";
$strFunction = "Funktion";
$strQuestion = "Hemmeligt spørgsmål";
$strAnswer = "Svar";
$strFax = "Fax";
$strWorkFax = "Fax arbejde";
$strHomepage = "Hjemmeside";
$strWorkAddress = "Adresse arbejde";
$strWorkZip = "Postnummer arbejde";
$strWorkCountry = "Land arbejde";
$strWorkState = "Stat arbejde";
$strWorkCity = "By arbejde";
$strPhoto = "Foto";
$strComments = "Kommentarer";
$strProperties = 'Egenskaber';
$strDeleteItem = "Slet";
$strDate = "Dato";
$strSaveSuccess = "Data blev gemt";
$strSaveError = "En fejl opstod under forsøg på at gemme data.";
$strDataError = "En fejl opstod under læsning af data.";
$strDeleteError = "Data kunne ikke slettes.";
$strAclError = "Fejl ved adgangskontrol";
$strDateError = "Du indtastede en ugyldig dato";
$strAccessDenied = "Adgang nægtet";
$AccessDenied_text = "Du har ikke tilladelse til at se denne del af systemet eller til at udføre denne handling.<br />En anden bruger har begrænset adgangen til den information du har søgt.";
$invalid_chars = "Følgende tegn er ikke tilladte";
$strGroups = "Grupper";
$strOwner = "Ejer";
$strDeletePrefix = "Er du sikker på du vil slette ";
$strDeleteSuffix = "?";
//menu
$menu_logout = "Log ud";
$menu_applications = "Programmer";
$menu_help = "Hjælp";
$menu_about = "Om";
$menu_documentation = "Dokumentation";
$menu_configuration = "Opsætning";
$menu_groups = "Brugergrupper";
$menu_accounts = "Kontoindstillinger";
$menu_preferences = "Personlige indstillinger";
$menu_search_discussions = "Diskussioner";
$menu_search_files = "Filer&nbsp;og&nbsp;mapper";
$menu_search_web = "Internettet";
$menu_filetypes = "Filtyper";
$menu_discussions = "Diskussions&nbsp;forum";

$menu_users = "Brugere";
$menu_new = "Ny";
$menu_message = "Besked";
$menu_contact = "Kontakt";
$menu_event = "Hændelse";
$menu_bookmark = "Bogmærke";
$menu_topics = "Emner";
$menu_support = "Support";
$menu_projects = "Projekter";
$menu_sysinfo = "Systeminformation";
$menu_admin = 'Administrator';
$menu_modules = 'Tilføj/fjern moduler';
$menu_manual = 'Manual';

//class validate
$error_required = "Du udfyldte ikke nedenstående felt.";
$error_match_auth = "Autorisationskoden er forkert.";
$error_match_pass = "Adgangskoderne passer ikke.";
$error_min_length = "Mindste antal tegn for nedenstående felt:";
$error_max_length = "Højeste antal tegn for nedenstående felt:";
$error_email = "E-mail adressen du angav er ugyldig.";
$error_email_exists = "E-mail adressen du indtastede eksisterer allerede.";
$error_username_exists = "Beklager, brugernavnet du indtastede eksisterer allerede. Vælg venligst et andet.";
$errors_in_form = "Der var fejl i formularen. Ret dem og prøv igen.";
$error_missing_field = "Du udfyldte ikke samtlige påkrævede felter.";

//acl control
$acl_control_auth_groups = "Autoriserede grupper";
$acl_control_auth_users = "Autoriserede brugere";
$acl_control_no_contacts = "Du kan ikke vælge nogen kontakter.<br /> Brug adressebogen til at tilføje kontakter.";

$strShowProfile = "Vis profil";
$strShowGroup = "Vis gruppemedlemmer";
$strProcessingTime = "Behandlingen af denne side tog ";
$strLoggedInAs = "Logget ind som ";
$strAt = " at ";
$strEmailTo = "Send en e-mail til: ";
$strProtectedUser = "Beskyttet bruger";

$http_not_found = "Dokumentet findes ikke";
$http_not_found_text = "Det søgte dokument blev ikke fundet. Undersøg at der ikke er stavefejl i adressen.";
$http_error = "Intern Serverfejl";
$http_bad_request = "Bad request";
$strAddContact = "Tilføj til kontakter";

$strReadRights = "Læserettigheder";
$strWriteRights = "Skriverettigheder";
$strPermissions = 'Rettigheder';

$days[0]="Sø¸";
$days[1]="Ma";
$days[2]="Ti";
$days[3]="On";
$days[4]="To";
$days[5]="Fr";
$days[6]="Lø¸";

$months[0]="Januar";
$months[1]="Februar";
$months[2]="Marts";
$months[3]="April";
$months[4]="Maj";
$months[5]="Juni";
$months[6]="Juli";
$months[7]="August";
$months[8]="September";
$months[9]="Oktober";
$months[10]="November";
$months[11]="December";

$full_days[0] = "Søndag";
$full_days[1] = "Mandag";
$full_days[2] = "Tirsdag";
$full_days[3] = "Onsdag";
$full_days[4] = "Torsdag";
$full_days[5]= "Fredag";
$full_days[6] = "Lørdag";
$strLoadTime = 'Genereret på';
$strDescription = 'Beskrivelse';



$lang_modules['addressbook'] = "Adressebog";
$cmdShowAll = 'Vis alle';
$cmdSearch = 'Søg';

//translation required
$cmdYesToAll = 'Ja til alle';
$cmdNoToAll = 'Nej til alle';

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

$cmdPrint = 'Udskriv';
$strFirstName = 'Fornavn';
$strLastName = 'Efternavn';
$strInitials = 'Initialer';
$strSex = 'Køn';
$strBirthday = 'Fødselsdag';
$strSexes['M'] = 'Mand';
$strSexes['F'] = 'Dame';

$strTitle = 'Titel';
$strBeginning = 'Indledende';
$strMiddleName = 'Mellemnavn';
$sir_madam['M'] = 'Herr ';
$sir_madam['F'] = 'Fru ';
$strModifiedAt = 'Ændret den';
$strCreatedAt = 'Oprettet den';

$htmlarea_language_name = 'da';
$strLocalization = 'lokalisering';


//look up the language in controls/jscalendar/lang
$jscalendar_language_name = 'calendar-da.js';


//2.01 translations

//look up language in 'classes/phpmailer/language'
$php_mailer_lang = 'da';
$strEmailFail = 'Kunne ikke sende e-mail.';

//for people that want to use ktmlarea
$ktml_language = 'dansk';

$ml_host_unreachable = ' kan ikke tilgås. Automatisk check af e-mail er slået fra.';
$strRegion = 'Region';

//2.05
$cmdReset = 'Nulstil';
$strMyName = 'Mit navn';

//2.06
$strDeleteRow = 'Ønsker du at slette denne række?';
$strAddressNo = 'vejnummer';
$strWorkAddressNo = 'vejnummer for arbejde';

$strPostAddress = 'Post adresse';
$strPostAddressNo = 'Post husnummer';
$strPostCity = 'Post by';
$strPostState = 'Post stat';
$strPostCountry = 'Post land';
$strPostZip = 'Post postnummer';
$strVisitAddress = 'Adresse';
$strPostAddress = 'Post adresse';


$login_register = 'Klik her for tilmelding.';


//modules
$lang_modules['scheduler'] = 'Almanak';
$lang_modules['calendar'] = "Kalender";
$lang_modules['filesystem'] = 'Filer';
$lang_modules['email'] = 'E-mail';
$lang_modules['projects'] = 'Projekter';
$lang_modules['search'] = 'Søg';
$lang_modules['contacts'] = "Adressebog";
$lang_modules['cms'] = "Hjemmesider";
$lang_modules['notes'] = "Noter";
$lang_modules['bookmarks'] = "Bogmærker";
$lang_modules['summary'] = "Oversigt";
$lang_modules['todos'] = "Opgaver";
$lang_modules['opentts'] = "Helpdesk";

$lang_modules['chat'] = "Chat";
$lang_modules['comics'] = "Tegneserie";
$lang_modules['daogen'] = "DAO";
$lang_modules['example'] = "Eksempel";
$lang_modules['news'] = "Nyheder";
$lang_modules['wiki'] = "Wiki";


$cmdVcard = 'vCard';

//2.07
$strNotEnoughLicenses = 'Du har ikke tilstrækkelig mange licenser til at bruge denne sektion af Group-Office.'.
												'Kontakt sales@intermesh.nl for at købe yderligere licenser.';
