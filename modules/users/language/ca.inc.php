<?php
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('users'));
$lang['users']['name'] = 'Usuaris';
$lang['users']['description'] = 'Mòdul d\'Administració; sistema de gestió dels usuaris.';

$lang['users']['deletePrimaryAdmin'] = 'No podeu esborrar l\'Administrador Principal';
$lang['users']['deleteYourself'] = 'No podeu esborrar el vostre';

$lang['link_type'][8]=$us_user = 'Usuari';

$lang['users']['error_username']='Hi ha caracters no vàlids dins el nom d\'usuari';
$lang['users']['error_username_exists']='Aquest nom d\'usuari ja existeix';
$lang['users']['error_email_exists']='Aquesta adreça de correu electrònic ja està registrada. Podeu utilitzar la funció de contrasenya oblidada per recuperar la contrasenya.';
$lang['users']['error_match_pass']='La contrasenya és incorrecta';
$lang['users']['error_email']='L\'adreça de correu electrònic no és vàlida';

$lang['users']['imported']='%s importats d\'usuari';
$lang['users']['failed']='Error';
$lang['users']['incorrectFormat']='L\'arxiu no conté un format CSV correcte';
$lang['users']['register_email_subject']='Detalls del vostre nou compte';
$lang['users']['register_email_body']='S\'ha creat un nou compte {product_name} a {url}
Les vostres dades de logueig són:

Usuari: {username}
Contrasenya: {password}';

$lang['users']['error_user']='No s\'ha pogut crear l\'usuari';
$lang['users']['max_users_reached']='S\'ha assolit el màxim nombre d\'usuaris per aquest sistema';

