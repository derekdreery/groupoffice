<?php
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('users'));
$lang['users']['name'] = 'Utenti';
$lang['users']['description'] = 'Modulo Admin; Gestione sistema utenti.';

$lang['users']['deletePrimaryAdmin'] = 'Non puoi cancellare l\'amministratore principale';
$lang['users']['deleteYourself'] = 'Non puoi cancellare te stesso';

$lang['link_type'][8]=$us_user = 'Utente';

$lang['users']['error_username']='Ci sono caratteri non validi nello username';
$lang['users']['error_username_exists']='Questo username esiste già';
$lang['users']['error_email_exists']='Questo indirizzo e-mail è già registrato. You can use the forgotten password feature to recover your password.';
$lang['users']['error_match_pass']='La password è errata';
$lang['users']['error_email']='L\'indirizzo e-mail non è valido';

$lang['users']['imported']='importati %s utenti';
$lang['users']['failed']='Fallito';
$lang['users']['incorrectFormat']='Il file non è in un formato CSV corretto';