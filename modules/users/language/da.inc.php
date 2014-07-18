<?php
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('users'));
$lang['users']['name'] = ' Brugere';
$lang['users']['description'] = 'Admin modul; Håndtering af systemets brugere.';

$lang['users']['deletePrimaryAdmin'] = 'Du kan ikke slette den primære administrator';
$lang['users']['deleteYourself'] = 'Du kan ikke slette dig selv';

$lang['link_type'][8]=$us_user = 'Bruger';

$lang['users']['error_username']='Der er ugyldige tegn i dit brugernavn';
$lang['users']['error_username_exists']='Beklager, det brugernavn eksisterer allerede';
$lang['users']['error_email_exists']='Beklager, den e-mail adresse er allerede i anvendelse.';
$lang['users']['error_match_pass']='Kodeordene var ikke identiske';
$lang['users']['error_email']='Du angav en ugyldig e-mail adresse';

$lang['users']['imported']='Importerede %s brugere';
$lang['users']['failed']='Fejl';

$lang['users']['incorrectFormat']='Fil var ikke i korrekt CSV format';