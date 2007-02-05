<?php
require_once($GO_LANGUAGE->get_fallback_base_language_file('common'));

$charset = 'UTF-8';


//main language menu file register login and other non logged in pages
//common
$cmdLogin = "Anmelden";
$cmdSave = "Speichern";
$cmdBack = "Zurück";
$cmdCancel = "Abbrechen";
$cmdClose = "Schließen";
$cmdOk = "Ok";
$cmdDelete = "Löschen";
$cmdNext = "Vor";
$cmdPrevious = "Zurück";
$cmdContinue = "Weiter";
$cmdUpdate = "Synchronisieren";
$cmdApply = "Übernehmen";
$cmdYes = "Ja";
$cmdNo = "Nein";
$cmdAdd = 'Hinzufügen';
$cmdEdit = 'Bearbeiten';
$cmdInvertSelection = 'Auswahl umkehren';

$strEdit = "Bearbeiten";
$strThis = 'diese';
$strItems = 'Elemente';
$strDescription = 'Beschreibung';

$strName = "Name";
$strUser = "Benutzer";
$strUsername = "Benutzername";
$strPassword = "Passwort";
$strAuthcode = "Authorisierungscode";
$strCountry = "Land";
$strState = "Bundesland";
$strCity = "Stadt";
$strZip = "Postleitzahl";
$strAddress = "Straße";
$strEmail = "Email";
$strPhone = "Telefon";
$strWorkphone = "Telefon berufl.";
$strCellular = "Mobil";
$strCompany = "Firma";
$strDepartment = "Abteilung";
$strFunction = "Funktion";
$strQuestion = "Geheime Frage";
$strAnswer = "Antwort";
$strFax = "Fax";
$strWorkFax = "Fax berufl.";
$strHomepage = "Homepage";
$strWorkAddress = "Straße berufl.";
$strWorkZip = "PLZ berufl.";
$strWorkCountry = "Land berufl.";
$strWorkState = "Bundesland berufl.";
$strWorkCity = "Stadt berufl.";
$strPhoto = "Foto";
$strComments = "Kommentar";
$strProperties = 'Eigenschaften';
$strDeleteItem = "Löschen";
$strDate = "Datum";
$strSaveSuccess = "Daten erfolgreich gespeichert";
$strSaveError = "Beim Speichern der Daten ist ein Fehler aufgetreten. Bitte erneut versuchen.";
$strDataError = "Beim Lesen der Daten ist ein Fehler aufgetreten. Bitte versuchen Sie, die Seite neu zu laden.";
$strDeleteError = "Die Daten konnten nicht gelöscht werden.";
$strAclError = "Fehler in der Zugriffskontrolle";
$strDateError = "Sie haben ein ungültiges Datum eingegeben";
$strAccessDenied = "Zugriff verweigert";
$AccessDenied_text = "Keine Berechtigung.<br />Ihnen wurden lediglich eingeschränkte Zugriffsrechte erteilt.";
$invalid_chars = "Die nachfolgenden Zeichen sind nicht erlaubt";
$strGroups = "Gruppen";
$strOwner = "Eigentümer";
$strDeletePrefix = "Sind Sie sicher, dass Sie ";
$strDeleteSuffix = " löschen möchten?";
//menu
$menu_logout = "Abmelden";
$menu_applications = "Applikationen";
$menu_help = "Hilfe";
$menu_about = "Über";
$menu_documentation = "Dokumentation";
$menu_configuration = "Konfiguration";
$menu_groups = "Benutzer&nbsp;und&nbsp;Gruppen";
$menu_accounts = "Mein&nbsp;Account";
$menu_preferences = "Einstellungen";
$menu_search_discussions = "Diskussionen";
$menu_search_files = "Dateien&nbsp;und&nbsp;Ordner";
$menu_search_web = "Das&nbsp;Web";
$menu_filetypes = "Dateitypen";
$menu_discussions = "Diskussionen";

$menu_users = "Benutzer";
$menu_new = "Neu";
$menu_message = "Nachricht";
$menu_contact = "Kontakt";
$menu_event = "Vorgang";
$menu_bookmark = "Lesezeichen";
$menu_topics = "Themen";
$menu_support = "Support";
$menu_projects = "Projekte";
$menu_sysinfo = "Systeminformation";
$menu_admin = 'Administrator';
$menu_modules = 'Module';
$menu_manual = 'Manual';

//class validate
$error_required = "Sie haben die Pflichtfelder nicht vollständig ausgefüllt.";
$error_match_auth = "Die Authorisierungscodes stimmen nicht überein.";
$error_match_pass = "Die Passwörter stimmen nicht überein.";
$error_min_length = "Die Mindestanzahl von Zeichen für das untere Feld ist:";
$error_max_length = "Die Höchstanzahl von Zeichen für das untere Feld ist:";
$error_email = "Die von Ihnen eingegebene  Email-Adresse ist ungültig.";
$error_email_exists = "Die von Ihnen eingegebene  Email-Adresse ist bereits vorhanden.";
$error_username_exists = "Leider ist der von Ihnen eingegebene Benutzername bereits vorhanden. Bitte wählen Sie einen anderen Benutzernamen.";
$errors_in_form = "Das Formular wurde nicht fehlerfrei ausgefüllt. Bitte korrigieren und erneut versuchen.";
$error_missing_field = "Sie haben nicht alle Pflichtfelder ausgefüllt.";

//acl control
$acl_control_auth_groups = "Authorisierte Gruppen";
$acl_control_auth_users = "Authorisierte Benutzer";
$acl_control_no_contacts = "Ihnen stehen keine Kontakte zur Auswahl.<br /> Bitte benutzen Sie das Adressbuch, um Mitglieder in Ihre Kontaktliste aufzunehmen.";

$strShowProfile = "Profil anzeigen";
$strShowGroup = "Gruppenmitglieder anzeigen";
$strProcessingTime = "Die Verarbeitung dieser Seite benötigte ";
$strLoggedInAs = "Angemeldet als ";
$strAt = " um ";
$strEmailTo = "Sende eine Email-Nachricht an: ";
$strProtectedUser = "Geschützter Benutzer";

$http_not_found = "Dokument nicht gefunden";
$http_not_found_text = "Das angeforderte Dokument wurde nicht gefunden. Möglicherweise haben Sie die URL nicht korrekt eingegeben.";
$http_error = "Interner Serverfehler";
$http_bad_request = "Fehlerhafte Anforderung";
$strAddContact = "Zu den Kontakten hinzufügen";

$strReadRights = "Leseberechtigungen";
$strWriteRights = "Schreibberechtigungen";
$strPermissions = 'Berechtigungen';

$days[0]="So";
$days[1]="Mo";
$days[2]="Di";
$days[3]="Mi";
$days[4]="Do";
$days[5]="Fr";
$days[6]="Sa";

$months[0]="Januar";
$months[1]="Februar";
$months[2]="März";
$months[3]="April";
$months[4]="Mai";
$months[5]="Juni";
$months[6]="Juli";
$months[7]="August";
$months[8]="September";
$months[9]="Oktober";
$months[10]="November";
$months[11]="Dezember";

$full_days[0] = "Sonntag";
$full_days[1] = "Montag";
$full_days[2] = "Dienstag";
$full_days[3] = "Mittwoch";
$full_days[4] = "Donnerstag";
$full_days[5]= "Freitag";
$full_days[6] = "Samstag";
$strLoadTime = 'geladen in';
$strDescription = 'Beschreibung';



$lang_modules['addressbook'] = "Addressbuch";
$cmdShowAll = 'Alle anzeigen';
$cmdSearch = "Suchen";

//translation required
$cmdYesToAll = 'Ja zu allem';
$cmdNoToAll = 'Nein zu allem';

$short_months[0]="Jan";
$short_months[1]="Feb";
$short_months[2]="Mär";
$short_months[3]="Apr";
$short_months[4]="Mai";
$short_months[5]="Jun";
$short_months[6]="Jul";
$short_months[7]="Aug";
$short_months[8]="Sep";
$short_months[9]="Okt";
$short_months[10]="Nov";
$short_months[11]="Dez";

$strToday = 'Heute';

$cmdPrint = 'Drucken';
$strFirstName = 'Vorname';
$strLastName = 'Nachname';
$strInitials = 'Initialen';
$strSex = 'Geschlecht';
$strBirthday = 'Geburtstag';
$strSexes['M'] = 'männlich';
$strSexes['F'] = 'weiblich';

$strTitle = 'Titel';
$strBeginning = 'Ansprache';
$strMiddleName = '2. Vorname';
$sir_madam['M'] = 'Herr';
$sir_madam['F'] = 'Frau';
$strModifiedAt = 'Geändert am';
$strCreatedAt = 'Erzeugt am';

$htmlarea_language_name = 'de';
$strLocalization = 'Lokalität';


//look up the language in controls/jscalendar/lang
$jscalendar_language_name = 'calendar-de.js';


//2.01 translations

//look up language in 'classes/phpmailer/language'
$php_mailer_lang = 'de';
$strEmailFail = 'Die E-Mail konnte nicht versandt werden.';

//for people that want to use ktmlarea
$ktml_language = 'deutsch';

$ml_host_unreachable = ' ist nicht erreichbar. Die automatische E-Mail-Abfrage wird deaktiviert.';
$strRegion = 'Region';

//2.05
$cmdReset = 'Zurücksetzen';
$strMyName = 'Mein Name';

//2.06
$strDeleteRow = 'Soll diese Zeile gelöscht werden?';
$strAddressNo = 'Hausnummer';
$strWorkAddressNo = 'Hausnummer';

$strPostAddress = 'Postanschrift';
$strPostAddressNo = 'Hausnummer';
$strPostCity = 'Stadt';
$strPostState = 'Bundesland';
$strPostCountry = 'Land';
$strPostZip = 'Postleitzahl';
$strVisitAddress = 'Firmensitz';
$strPostAddress = 'Post Addresse';



//modules
$lang_modules['scheduler'] = 'Termine';
$lang_modules['calendar'] = "Kalender";
$lang_modules['filesystem'] = 'Dateien';
$lang_modules['email'] = 'Email';
$lang_modules['projects'] = 'Projekte';
$lang_modules['search'] = 'Suchen';
$lang_modules['contacts'] = "Adressbuch";
$lang_modules['cms'] = "Webseiten";
$lang_modules['notes'] = "Notizen";
$lang_modules['bookmarks'] = "Lesezeichen";
$lang_modules['summary'] = "Übersicht";
$lang_modules['todos'] = "Aufgaben";
$lang_modules['opentts'] = "Helpdesk";
$lang_modules['products'] = "Softwareshop";

$cmdVcard = 'vCard';

//2.07
$strNotEnoughLicenses = 'Die Anzahl der Lizenzen ist erschöpft. Bitte wenden'.
			' Sie sich an sales@intermesh.nl, wenn Sie beabsichtigen'.
			' weitere Lizenzen zu erwerben.';

//2.08
$alphabet = 'A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z';
									
$strContact = 'Kontakt';
$strRelatedTo = 'Bezug auf';
$strProject = 'Projekt';

$strNewProject = 'Neues Projekt';
$strNewEvent = 'Neuer Termin';
$strNewTodo = 'Neue Aufgabe';
$strNewNote = 'Neue Notiz';

$strActivities = 'Aktivitäten';
$strFile = 'Datei';

//2.10
$cmdCopy = 'Kopieren';
$strProjects = 'Projekte';
$strFiles = 'Dateien';
$cmdSaveNew = 'Speichern & Weiter';
$strAvailability = 'Verfügbarkeit';
$strCheckAvailabilityFor = 'Verfügbarkeit prüfen von';
$cmdPleaseSelect = 'Bitte wählen...';

//2.11
$strNewEmail = 'Neue Email';
$strConsistencyCheck = "Konsistenz prüfen";
$strSearchAll = 'Alle Felder';
$sync_download_clients = 'Client-Software für die Synchronisierung herunterladen';

//2.12
$strNoData = 'Keine Daten gefunden';
//2.13
$strSynchronization = 'Synchronisation';
$strSnooze = 'Dösen';
$strDismiss = 'Ausschalten';

//2.14
$strUpload = 'Datei hochladen';
$cmdInsert = 'Einfügen';
$strText = 'Text';
$strType = 'Typ';

$strNoItemSelected = 'Bitte ein Element wählen';
$strSelectedItem = 'das ausgewählte Element';

$strKeyword = 'Schlüsselwort';

$strNoActivities = 'Keine Aktivitäten für dieses Element';

$strCopy = 'Kopieren'; //not a verb

//2.15
$strBank = 'Bank';
$strBankNo = 'Bankleitzahl';
$strSelectedItems = 'ausgewählte Gegenstände';

$strMonth = 'Monat';
$strYear = 'Jahr';
$strDay = 'Tag';
$strHour = 'Stunde';
$strMinute = 'Minute';

$strTwelveHourFormat = '12 Stunden Format';
$strTwentyfourHourFormat = '24 Stunden Format';

//2.16
$strCreateLink = 'Link erstellen';
$strCancelLink = 'Verlinkung aufheben';
$strLinkText = 'Verlinkung ist aktiv. Gehe zu einem verlinkbaren Gegenstand und klicke auf den \'Link erstellen\' Button um ihn mit \'%s\' zu verlinken. Wenn du einen neuen verlinkbaren Gegenstand erstellst wird er automatisch verlinkt.';
$strLinks = 'Links';
$strUnlinkPrefix = 'Bist du dir sicher dass du die verlinkung aufheben willst: ';
$strUnlinkSuffix = '?';
$cmdUnlink = 'Verlinkung aufheben';



$strAdministrate = 'Administrieren';
$strProOnly = 'Dieses Feature ist nur in Group Office Professional verfügbar. <a href="http://www.group-office.com">Hier klicken um mehr über Group-Office Professional zu erfahren.</a>';

$strMoreColors='Mehr Farben';

$strNoItems = 'Keine Gegenstände gefunden';
$cmdExport = 'Exportieren';

$error_username = 'Ungültiger Benutzername. Erlaubte Schriftzeichen: a-z, 0-9,- en _';

$cmdSettings = 'Einstellungen';
