<?php
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('users'));
$lang['users']['name'] = 'Kasutajad';
$lang['users']['description'] = 'Admin moodul. Kasutajate haldamine.';

$lang['users']['deletePrimaryAdmin'] = 'Super-administraatorit ei saa kustutada';
$lang['users']['deleteYourself'] = 'Iseenda kontot ei saa kustutada';

$lang['link_type'][8]=$us_user = 'Kasutaja';

$lang['users']['error_username']='Kasutajanimes on vigased tähemärgid';
$lang['users']['error_username_exists']='See kasutajanimi on juba kasutusel.';
$lang['users']['error_email_exists']='sisestatud e-postiga kasutaja on juba olemas.';
$lang['users']['error_match_pass']='Paroolid ei kattunud';
$lang['users']['error_email']='Sisestatud e-posti aadres on vale';
$lang['users']['error_user']='kasutajat ei olnud võimalik luua';

$lang['users']['imported']='Imporditud %s kasutajat';
$lang['users']['failed']='Ebaõnnestus';

$lang['users']['incorrectFormat']='Fail ei olnud korrektses CSV formaadis';

$lang['users']['register_email_subject']='Sinu konto detailid';
$lang['users']['register_email_body']='Sinu konto loodi aadressil {url}
Sinu andmed on:

Kasutajanimi: {username}
Parool: {password}';


$lang['users']['max_users_reached']='Maksimum süsteemis lubatud kasutajate arv on täis.';