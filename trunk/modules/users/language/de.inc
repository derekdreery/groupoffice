<?php
require_once($GO_LANGUAGE->get_fallback_language_file('users'));

$strLogins = "Anmeldungen";
$strRegistrationDate = "Registrierungs-Datum";
$strUsers = "Benutzer";
$user_profile = "Benutzerprofil";
$ac_login_info = "Anmeldeinformation";
$ac_registration_time = "Registrierungs-Zeit";
$ac_lastlogin = "Letzte Anmeldung";
$ac_logins = "Anzahl Anmeldungen";
$strMaxOf = "registriert von möglichen";
$strUsername = "Benutzername";
$delete_fail = "Sich selbst bzw. den Administrator dürfen Sie nicht löschen.";
$admin_new_user = "Neuer Benutzer";

$admin_modules = 'Module';
$admin_module_access = 'Dieser Benutzer soll eine Zugriffsberechtigung auf nachfolgende Module erhalten:';

$admin_password = 'Passwort';
$admin_confirm_password = 'Passwort wiederholen';
$admin_change_password = 'Passwort ändern';
$admin_pass_match = 'Keine Übereinstimmung der Passworte. Bitte erneut versuchen.';

//registration
$registration_personal_info = "Persönliche Informationen";
$registration_account_info = "Zugangsinformationen";
$registartion_username_text = "Bitte Benutzername und Passwort des Benutzers für die Anmeldung festlegen.";

$registration_question_text=<<<registration_question_text
Bitte hier eine geheime Frage hinterlegen, welche nur der Benutzer beantworten kann. Sollte der Benutzer sein Passwort vergessen,
wird diese Frage an den Benutzer gerichtet. Bei korrekter Beantwortung der Frage erhält der Benutzer eine Email mit neuen Anmeldeinformationen.
registration_question_text;

$registration_text=<<<registration_text
Bitte dieses Formular ausfüllen und auf die Schaltfläche 'Ok' klicken, um die Registrierung abzuschlie&szlig;en. Mit einem * markierte Felder sind Pflichtfelder.<br />
Die eingegebenen Informationen werden lediglich innerhalb von Group-Office verwendet und werden nicht an dritte weitergereicht.
registration_text;

$registartion_authcode_text = "Bitte Authorisierungscode eingeben. Der Authorisierungscode ermöglicht anderen Benutzern die Einsicht in diese Benutzerinformationen und Eintragung des Benutzers in die eigene Kontaktliste.";
$registartion_confirm_authcode = "Authorisierungscode wiederholen";
$registration_visibility = "Dieser Benutzer soll für alle Benutzer sichtbar sein.";
$registration_success = "<h1>Herzlichen Glückwunsch!</h1>Die Registrierung war erfolgreich. Eine Willkommens-Email wurde versandt an:";
$registration_failure = "Während der Registrierung ist ein Fehler aufgetreten. Versuchen Sie es bitte erneut.";
$registration_mail_subject = "Willkommen in Group-Office.";
$registration_folder_error = "Beim Anlegen des persönlichen Ordners ist ein Fehler aufgetreten. Bitte an uns wenden, um eine Wiederherstellung vornehmen zu lassen!";
$registration_title = "Registrierung in Group-Office";

$registration_email = "Eine Email-Adresse für diesen Benutzer in Group-Office einrichten.";
$registration_samba = "datei- und druckdienste.";
$registration_email_error = "Beim Anlegen Ihres Zugangs ist ein Fehler aufgetreten. Bitte setzen Sie sich mit uns in Verbindung, um Support zu erhalten.";
$strPasswordConfirm = "Passwort wiederholen";
$strPassword = "Passwort";

$reg_company_info = "Firmen-Information";

$max_user_limit = "Die höchstmögliche Benutzeranzahl ist erreicht.";
$max_users_text = "Verzeihung, aber es können keine neuen Benutzer registriert werden. Kontaktieren Sie uns, wenn Sie die Benutzeranzahl erhöht haben möchten.";
$ml_sent_items = "Gesendet";
$ml_spam = "Spam";
$ac_samba_user = "Benutzer ist berechtigt auf Datei- und Druck-Server zuzugreifen.";
$ml_email_only = "Nur notwendig, wenn Email-Adresse bereits vorhanden";
$register_new_user = 'Neuer Group-Office Abonnent';
$register_thanks = '<h2>Vielen dank</h2>. Ihr Abonnement wird so schnell wie möglich bearbeitet. Sie werden über weitere Schritte informiert werden.';
$register_accept = 'Zu den Benutzern hinzufügen';
$register_addressbook = 'Zum Adressbuch hinzufügen';
$register_title = 'Abonnieren';


//1.98
$reg_language = 'Sprache';
$reg_theme = 'Thema';

//2.05
$admin_use = 'Benutze';
$admin_module = 'Modul';
$admin_manage = 'Verwalten';

$admin_groups = 'Benutzergruppen';
$admin_groups_user = 'Der Benutzer ist Mitglied der nachfolgenden Benutzergruppen';

//2.11

//Modified string (Added %groupoffice_title%)
$registration_mail_body=<<<registration_mail_body
Sehr geehrte(r) %sex% %title% %last_name%,

Für Sie ist ein Benutzerkonto in %groupoffice_title% eingerichtet worden.

Nachfolgend finden Sie Ihre Zugangsdaten:

Benutzername: %username%
Passwort: %password%

Bitte den unteren Link anklicken, um sich anzumelden:

registration_mail_body;

//2.13
$registration_mail_subject = "Willkommen in %groupoffice_title%"; //changed string
$registration_title = "Registrieren unter %groupoffice_title%"; //changed string

$lang_modules['users'] = 'Benutzer';

//2.14
$users_enabled = 'Benutzerkonto aktiviert';

$admin_groups_visible = 'Benutzer ist sichtbar für nachfolgende Benutzergruppen';
$admin_look_feel = 'Erscheinungsbild';
?>
