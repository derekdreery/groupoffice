<?php
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('users'));
$lang['users']['name'] = 'Utilizatori';
$lang['users']['description'] = 'Modul Admin; Gestione sistemul utilizatori.';

$lang['users']['deletePrimaryAdmin'] = 'Nu poţi şterge administratorul principal';
$lang['users']['deleteYourself'] = 'Nu te poţi şterge pe tine insăţi';

$link_type[8]=$us_user = 'Utilizator';

$lang['users']['error_username']='Sunt caractere invalide in numele utilizatorului';
$lang['users']['error_username_exists']='Nume utilizator există deja';
$lang['users']['error_email_exists']='Adresa email deja folosită. Poţi folosi sistemul de amintire a parolei pentru a recupera parola.';
$lang['users']['error_match_pass']='Parola este eronată';
$lang['users']['error_email']='Adresa e-mail nu este corectă';

$lang['users']['imported']='importaţi %s utilizatori';
$lang['users']['failed']='Eroare';
$lang['users']['incorrectFormat']='Fişierul nu este în formatul CSV corect';