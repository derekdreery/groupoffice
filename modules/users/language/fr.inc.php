<?php
//French Translation v1.0
//Author : Lionel JULLIEN
//Date : September, 05 2008

//Uncomment this line in new translations!
require_once($GO_LANGUAGE->get_fallback_language_file('users'));

$lang['users']['name'] = 'Utilisateurs';
$lang['users']['description'] = 'Module Admin. Gérer les utilisateurs de Group-Office.';

$lang['users']['deletePrimaryAdmin'] = 'Vous ne pouvez pas supprimer l\'administrateur principal';
$lang['users']['deleteYourself'] = 'vous ne pouvez pas vous supprimer !';

$link_type[8]=$us_user = 'Utilisateur';

$lang['users']['error_username']='Il y a des caractères invalides dans votre nom d\'utilisateur';
$lang['users']['error_username_exists']='Désolé, ce nom d\'utilisateur existe déjà';
$lang['users']['error_email_exists']='Désolé, cette adresse e-mail est déjà utilisée. Vous pouvez utiliser l\'utilitaire de récupération de mot de passe afin de recevoir votre mot de passe.';
$lang['users']['error_match_pass']='Les mots de passe ne correspondent pas !';
$lang['users']['error_email']='vous avez saisi une adresse e-mail invalide';