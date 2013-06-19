<?php
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('users'));
$lang['users']['name']='Benutzer';
$lang['users']['description']='Modul zur Benutzerverwaltung';
$lang['users']['deletePrimaryAdmin']='Sie können den primären Administrator nicht löschen';
$lang['users']['deleteYourself']='Sie können sich nicht selbst löschen';
$lang['link_type'][8]=$us_user='Benutzer';
$lang['users']['error_username']='Ihr Benutzername enthält ungültige Zeichen';
$lang['users']['error_username_exists']='Der Benutzername ist bereits registriert';
$lang['users']['error_email_exists']='Die E-Mail-Adresse ist bereits registriert.';
$lang['users']['error_match_pass']='Die Passwörter sind nicht identisch';
$lang['users']['error_email']='Sie haben eine ungültige E-Mail-Adresse angegeben';
$lang['users']['error_user']='Benutzer konnte nicht angelegt werden';
$lang['users']['imported']='%s Benutzer importiert';
$lang['users']['failed']='Fehlgeschlagen';
$lang['users']['incorrectFormat']='Datei hatte nicht das erforderliche CSV-Format';
$lang['users']['register_email_subject']='Ihre GroupOffice-Kontodaten';
$lang['users']['register_email_body']='Ein GroupOffice-Konto wurde für Sie auf {url} erstellt.
Ihre Logindaten sind:

Benutzername: {username}
Passwort: {password}';


$lang['users']['max_users_reached']='Für dieses System wurde die maximale Benutzeranzahl erreicht.';