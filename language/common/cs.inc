<?php
//Uncomment this line in new translations!
require_once($GO_LANGUAGE->get_fallback_base_language_file('common'));
$htmldirection='ltr';
$charset = 'UTF-8';
//main language menu file register login and other non logged in pages
//common
$cmdLogin = "Přihlášení";
$cmdSave = "Uložení";
$cmdBack = "Zpět";
$cmdCancel = "Zrušit";
$cmdClose = "Zavřít";
$cmdOk = "Ok";
$cmdDelete = "Smazat";
$cmdNext = "Další";
$cmdPrevious = "Předchozí";
$cmdContinue = "Pokračovat";
$cmdUpdate = "Synchronizovat";
$cmdApply = "Použít";
$cmdYes = "Ano";
$cmdNo = "Ne";
$cmdAdd = 'Přidat';
$cmdEdit = 'Upravit';
$cmdInvertSelection = 'Invertovat výběr';

$strEdit = "Upravit";
$strThis = 'toto';
$strItems = 'položky';
$strDescription = 'Popis';

$strName = "Jméno";
$strUser = "Uživatel";
$strUsername = "Uživatelské jméno";
$strPassword = "Heslo";
$strAuthcode = "Přístupový kód";
$strCountry = "Země";
$strState = "Stát";
$strCity = "Město";
$strZip = "PSČ";
$strAddress = "Ulice";
$strEmail = "E-mail";
$strPhone = "Telefon";
$strWorkphone = "Telefon do zaměstnání";
$strCellular = "Mobilní telefon";
$strCompany = "Firma";
$strDepartment = "Oddělení";
$strFunction = "Funkce";
$strQuestion = "Bezpečnostní dotaz";
$strAnswer = "Odpověď";
$strFax = "Fax";
$strWorkFax = "Fax do zaměstnání";
$strHomepage = "Homepage";
$strWorkAddress = "Adresa zaměstnání";
$strWorkZip = "PSČ místa zaměstnání";
$strWorkCountry = "Země místa zaměstnání";
$strWorkState = "Stát místa zaměstnání";
$strWorkCity = "Město místa zaměstnání";
$strPhoto = "Foto";
$strComments = "Komentáře";
$strProperties = 'Vlastnosti';
$strDeleteItem = "Smazat";
$strDate = "Datum";
$strSaveSuccess = "Data byla úspěšně uložena";
$strSaveError = "Nastala chyba při ukládání dat.";
$strDataError = "Nastala chyba při čtení dat.";
$strDeleteError = "Údaje nelze smazat.";
$strAclError = "Chyba při kontrole přístupu";
$strDateError = "Zadal jste neplatné údaje";
$strAccessDenied = "Přístup zamítnut";
$AccessDenied_text = "Nemáte oprávnění k zobrazení těchto informací nebo k provedení akce.<br />Jiný uživatel vám omezil tato oprávnění.";
$invalid_chars = "Následující znaky nejsou povoleny";
$strGroups = "Skupiny";
$strOwner = "Vlastník";
$strDeletePrefix = "Jste si jistý, ze chcete smazat ";
$strDeleteSuffix = "?";
//menu
$menu_logout = "Odhlášení";
$menu_applications = "Aplikace";
$menu_help = "Nápověda";
$menu_about = "O";
$menu_documentation = "Dokumentace";
$menu_configuration = "Konfigurace";
$menu_groups = "Uživatel&nbsp;skupiny";
$menu_accounts = "Váš&nbsp;účet";
$menu_preferences = "Předvolby";
$menu_search_discussions = "Diskuze";
$menu_search_files = "Soubory&nbsp;a&nbsp;složky";
$menu_search_web = "Web";
$menu_filetypes = "Typy souborů";
$menu_discussions = "Diskuze";

$menu_users = "Uživatelé";
$menu_new = "Nové";
$menu_message = "Zpráva";
$menu_contact = "Kontakt";
$menu_event = "Událost";
$menu_bookmark = "Oblíbené";
$menu_topics = "Témata";
$menu_support = "Podpora";
$menu_projects = "Projekty";
$menu_sysinfo = "Systémové informace";
$menu_admin = 'Administrátor';
$menu_modules = 'Moduly';
$menu_manual = 'Manuál';

//class validate
$error_required = "Nespecifikoval jste požadované pole, viz.níže.";
$error_match_auth = "Tento přístupový kód neodpovídal.";
$error_match_pass = "Tato hesla neodpovídá.";
$error_min_length = "Pole s minimem znaků, viz.níže:";
$error_max_length = "Pole s maximem znaků, viz.níže:";
$error_email = "Vámi udaná E-mailová adresa je neplatná.";
$error_email_exists = "Vámi vložená adresa již existuje.";
$error_username_exists = "Omlouváme se, uživatelské heslo, které jste vložil, již existuje. Prosím vyberte jiné.";
$errors_in_form = "Ve formuláři byly zjištěny chyby. Opravte je a zkuste to znova.";
$error_missing_field = "Nevyplnil jste všechna požadovaná pole.";

//acl control
$acl_control_auth_groups = "Oprávněné skupiny";
$acl_control_auth_users = "Oprávnění uživatelé";
$acl_control_no_contacts = "Nemáte na výběr žádné kontakty členů.<br /> Využijte adresáře k přidání členů do vašich kontaktů.";

$strShowProfile = "Zobrazit profil";
$strShowGroup = "Zobrazit členy skupiny";
$strProcessingTime = "Zpracování této stránky trvalo ";
$strLoggedInAs = "Přihlásit se jako";
$strAt = " v ";
$strEmailTo = "Poslat E-mailovou zprávu: ";
$strProtectedUser = "Protected User";

$http_not_found = "Dokument nebyl nalezen";
$http_not_found_text = "Požadovaný dokument nebyl nalezen. Možná jste nesprávně zapsali URL.";
$http_error = "Vnitřní chyba serveru";
$http_bad_request = "Špatný požadavek";
$strAddContact = "Přidat do kontaktů";

$strReadRights = "Povolit čtení";
$strWriteRights = "Povolit psaní";
$strPermissions = 'Oprávnění';

$days[0]="So";
$days[1]="Po";
$days[2]="Út";
$days[3]="St";
$days[4]="Čt";
$days[5]="Pá";
$days[6]="Ne";

$months[0]="Leden";
$months[1]="Únor";
$months[2]="Březen";
$months[3]="Duben";
$months[4]="Květen";
$months[5]="Červen";
$months[6]="Červenec";
$months[7]="Srpen";
$months[8]="Září";
$months[9]="Říjen";
$months[10]="Listopad";
$months[11]="Prosinec";

$full_days[0] = "Neděle";
$full_days[1] = "Pondělí";
$full_days[2] = "Úterý";
$full_days[3] = "Středa";
$full_days[4] = "Čtvrtek";
$full_days[5]= "Pátek";
$full_days[6] = "Sobota";
$strLoadTime = 'Využijte adresáře k přidání členů do vašich kontakt';
$strDescription = 'Označení';



$lang_modules['addressbook'] = "Adresář";
$cmdShowAll = 'Zobrazit vše';
$cmdSearch = 'Vyhledat';

//translation required
$cmdYesToAll = 'Ano všem';
$cmdNoToAll = 'Ne všem';

$short_months[0]="Leden";
$short_months[1]="Únor";
$short_months[2]="Březen";
$short_months[3]="Duben";
$short_months[4]="Květen";
$short_months[5]="Červen";
$short_months[6]="Červenec";
$short_months[7]="Srpen";
$short_months[8]="Září";
$short_months[9]="Říjen";
$short_months[10]="Listopad";
$short_months[11]="Prosinec";

$strToday = 'dnes';

$cmdPrint = 'Tisk';
$strFirstName = 'Jméno';
$strLastName = 'Příjmení';
$strInitials = 'Iniciály';
$strSex = 'Pohlaví';
$strBirthday = 'Narození';
$strSexes['M'] = 'Muž';
$strSexes['F'] = 'Žena';

$strTitle = 'Titul';
$strBeginning = 'Začátek';
$strMiddleName = 'Druhé&nbsp;jméno';
$sir_madam['M'] = 'pán';
$sir_madam['F'] = 'paní';
$strModifiedAt = 'Měněno';
$strCreatedAt = 'Vytvořeno';

$htmlarea_language_name = 'cs';
$strLocalization = 'Lokalizace';


//look up the language in controls/jscalendar/lang
$jscalendar_language_name = 'calendar-cs.js';


//2.01 translations

//look up language in 'classes/phpmailer/language'
$php_mailer_lang = 'cs';
$strEmailFail = 'Chyba při odesílání e-mailu.';

//for people that want to use ktmlarea
$ktml_language = 'czech';

$ml_host_unreachable = ' je nedostupný. Automatická kontrola e-mailu bude zakázána.';
$strRegion = 'Oblast';

//2.05
$cmdReset = 'Reset';
$strMyName = 'Moje jméno';

//2.06
$strDeleteRow = 'Jste si jistí, že to chcete smazat teď?';
$strAddressNo = 'číslo domu';
$strWorkAddressNo = 'Pracovní číslo domu';

$strPostAddress = 'Poštovní adresa';
$strPostAddressNo = 'Poštovní číslo domu';
$strPostCity = 'Město pošty';
$strPostState = 'Stát pošty';
$strPostCountry = 'Země pošty';
$strPostZip = 'PSČ';
$strVisitAddress = 'Kontaktní adresa';




//modules
$lang_modules['scheduler'] = 'Plánovač';
$lang_modules['calendar'] = "Kalendář";
$lang_modules['filesystem'] = 'Soubory';
$lang_modules['email'] = 'E-mail';
$lang_modules['projects'] = 'Projekty';
$lang_modules['search'] = 'Hledej';
$lang_modules['contacts'] = "Adresář";
$lang_modules['cms'] = "Weby";
$lang_modules['notes'] = "Poznámky";
$lang_modules['bookmarks'] = "Oblíbené";
$lang_modules['summary'] = "Přehled";
$lang_modules['todos'] = "Úkoly";
$lang_modules['opentts'] = "Podpora";
$lang_modules['products'] = "Obchod";

$cmdVcard = 'vCard';

//2.07
$strNotEnoughLicenses = 'You don\'t have enough licenses to use this section of'.
												' Group-Office. Contact sales@intermesh.nl if you want '.
												'to purchase additional licenses.';
												
//2.08
$alphabet = 'A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z';

$strContact = 'Kontakt';
$strRelatedTo = 'Vztahuje se k';
$strProject = 'Projekt';

$strNewProject = 'Nový projekt';
$strNewEvent = 'Nová událost';
$strNewTodo = 'Nový úkol';
$strNewNote = 'Nová poznámka';

$strActivities = 'Aktivity';
$strFile = 'Soubor';

//2.10
$cmdCopy = 'Kopie';
$strProjects = 'Projekty';
$strFiles = 'Soubory';
$cmdSaveNew = 'Ulož a nový';
$strAvailability = 'Dostupnost';
$strCheckAvailabilityFor = 'Zkontroluj dostupnost pro';
$cmdPleaseSelect = 'Prosím zvolte...';

//2.11
$strNewEmail = 'Nový e-mail';
$strConsistencyCheck = "Kontrola správnosti";
$strSearchAll = 'Všechny položky';
$sync_download_clients = 'Stažení klienta pro synchronizaci';

//2.12
$strNoData = 'Nabyla nalezena žádná data';
//2.13
$strSynchronization = 'Synchronizace';
$strSnooze = 'Dřímat';
$strDismiss = 'Zruš';

//2.14
$strUpload = 'nahraj soubor';
$cmdInsert = 'Vlož';
$strText = 'Text';
$strType = 'Typ';

$strNoItemSelected = 'Není vybraná žádná položka';
//$strNoItemSelected = 'Vyberte ze seznamu';
$strSelectedItem = 'vybraná položka';

$strKeyword = 'Klíčové slovo';

$strNoActivities = 'Žádné aktivity pro tuto položku';

$strCopy = 'Kopie'; //not a verb

//2.15
$strBank = 'Banka';
$strBankNo = 'Bankovní číslo';
$strSelectedItems = 'vybrané položky';

$strWeek='Týden';
$strWeeks='Týdny';
$strMonth = 'Měsíc';
$strYear = 'Rok';
$strDay = 'Den';
$strDays='Dny';
$strHour = 'Hodina';
$strHours='Hodiny';
$strMinute = 'Minuta';
$strMin='Minuta';
$strMins='Minuty';

$strTwelveHourFormat = '12 hodinový formát';
$strTwentyfourHourFormat = '24 hodinový formát';

//2.16
$strCreateLink = 'Vytvořit spojení';
$strCancelLink = 'Zrušit spojení';
$strLinkText = 'Spojení je aktivní. Vyberte něco, co chcete spojit a klikněte na \'Create link\' tlačítko, abyste spojili s uživatelem \'%s\'. Když vytvoříte něco, co se dá spojit, bude to provázáno automaticky.';
$strUnlinkPrefix = 'Jste si jisti,že chcete zrušit spojení';
$strLinks = 'Spojení';
$strUnlinkSuffix = '?';
$cmdUnlink = 'Rozpojit';

$strAdministrate = 'Administrace';
$strProOnly = 'Tato vlastnost je k dispozici pouze v Group-Office Professional. <a href="http://www.group-office.com">Klikněte zde pro informace o Group-Office Professional</a>';

$strMoreColors='Více barev';

$strNoItems = 'žádné položky';
$cmdExport = 'Export';

$error_username = 'Špatné uživatelské jméno. Pouze tyto znaky jsou povoleny: a-z, 0-9,- en _';

$cmdSettings = 'Nastavení';


$strTableConfig = 'Konfigurace tabulky';

$strMonthText = 'Měsíc text';

$cmdUpload='Nahrát';
$cmdAttachFile='Připoj soubor';


$search_text = '<b>Tip:</b> Začleňte do hledací fráze (kontakt, firmu, projekty atd.),když vyhledávání vyhodnocuje stále mnoho výsledků';
$search_global='Hledat v Group-Office';
$search_results='výsledky';
$search_result='výsledek';
$strTime='Čas';
$table_config_text='Vložte číslo na začátek sloupců, které chcete zobrazit. Sloupce budou řazeny podle tohoto čísla.';
$strMoveUp='Move up one row';

$strAddressAndNo='Adresa / Ne.';
$strWorkAddressAndNo='Adresa do práce / Ne.';

$jupload_text='Vyberte soubory z Vašeho počítače a klikněte \'Upload\'.';

$jupload_max_size='Maximální velikost';
$jupload_width='váha';
$jupload_height='výška';

$strReminder = 'Upomínka';

$strStartTime = 'Začátek';
$strAddReminder = 'Přidej upomínku';


$config_help_url='http://docs.group-office.com/index.php?folder_id=49&file_id=202';
