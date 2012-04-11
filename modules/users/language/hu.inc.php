<?php
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('users'));
$lang['users']['name'] = 'Felhasználók';
$lang['users']['description'] = 'Admin modul. Felhasználók kezelése.';

$lang['users']['deletePrimaryAdmin'] = 'A főadminisztrátor nem törölhető.';
$lang['users']['deleteYourself'] = 'Nem törölheted saját magad.';

$lang['link_type'][8]=$us_user = 'Felhasználó';

$lang['users']['error_username']='Nem megfelelő karakter van a felhasználónévben';
$lang['users']['error_username_exists']='A felhasználónév már létezik';
$lang['users']['error_email_exists']='Ezzel az e-mail címmel már regisztrált valaki.';
$lang['users']['error_match_pass']='Hibás jelszó';
$lang['users']['error_email']='Hibás e-mail cím';

$lang['users']['imported']='Importálva %s felhasználó';
$lang['users']['failed']='Nem sikerült';

$lang['users']['incorrectFormat']='A fájl nem megfelelő CSV formátumú';