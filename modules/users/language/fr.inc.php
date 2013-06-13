<?php
/////////////////////////////////////////////////////////////////////////////////
//
// Copyright Intermesh
// 
// This file is part of {product_name}. You should have received a copy of the
// {product_name} license along with {product_name}. See the file /LICENSE.TXT
// 
// If you have questions write an e-mail to info@intermesh.nl
//
// @copyright Copyright Intermesh
// @version $Id$
// @author Merijn Schering <mschering@intermesh.nl>
//
// French Translation
// Version : 3.7.29 
// Author : Lionel JULLIEN
// Date : September, 27 2011
//
/////////////////////////////////////////////////////////////////////////////////

//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('users'));

$lang['users']['name'] = 'Utilisateurs';
$lang['users']['description'] = 'Module Admin. Gérer les utilisateurs de {product_name}.';
$lang['users']['deletePrimaryAdmin'] = 'Vous ne pouvez pas supprimer l\'administrateur principal';
$lang['users']['deleteYourself'] = 'Vous ne pouvez pas vous supprimer !';
$lang['link_type'][8]=$us_user = 'Utilisateur';
$lang['users']['error_username']='Il y a des caractères non supportés dans votre nom d\'utilisateur';
$lang['users']['error_username_exists']='Désolé, ce nom d\'utilisateur existe déjà';
$lang['users']['error_email_exists']='Désolé, cette adresse e-mail est déjà utilisée.';
$lang['users']['error_match_pass']='Les mots de passe ne correspondent pas !';
$lang['users']['error_email']='Vous avez saisi une adresse e-mail invalide';
$lang['users']['error_user']='L\'utilisateur ne peut pas être créé';
$lang['users']['imported']='%s utilisateurs importés';
$lang['users']['failed']='Echec';
$lang['users']['incorrectFormat']='Le fichier n\'est pas dans le bon format CSV';
$lang['users']['register_email_subject']='Details de votre compte {product_name}';
$lang['users']['register_email_body']='Un compte {product_name} vient d\'être créé pour vous à l\'adresse suivante {url}
Vos identifiants sont :

Nom d\'utilisateur : {username}
Mot de passe : {password}';
$lang['users']['max_users_reached']='Le nombre maximun d\'utilisateurs a été atteint sur votre système.';
