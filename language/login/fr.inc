<?php

// LANG: "fr", ENCODING: UTF-8
// Author: L. Jullien
// 15 Jan 2007

require_once($GO_LANGUAGE->get_fallback_base_language_file('login'));

$strWelcomeTo = "Bienvenue dans ";
$strUseMenu = "Utilisez le menu ou cliquez sur les liens rapides ci-dessous pour commencer à utiliser ";
$click_to_login = "Cliquez ici pour vous connecter.";
$login_text = "Entrer votre nom d'utilisateur et votre mot de passe pour vous connecter";
$login_username = "Utilisateur";
$login_password = "Mot de passe";
$login_missing_field = "Vous n'avez pas rempli votre nom d'utilisateur ou votre mot de passe.";
$login_bad_login = "Mauvais nom d'utilisateur ou mot de passe";
$login_remind = "Me laisser connecté jusqu'à ce que j'appuie sur le boutton de déconnexion.";
$login_language = 'Langue';

//2.12
$login_register = 'ou cliquez sur %s pour valider';
$login_here = "ici";
$login_lost_password = 'Vous avez perdu votre mot de passe ?';
$login_lost_password_title = 'Vous avez retrouvé votre mot de passe ?';
$login_lost_password_text= 'Veuillez entrez votre adresse email et un nouveau password vous sera envoyé à cette adresse.';
$login_lost_password_mail_body = 'Vous avez demandé un nouveau mot de passe pour %s. Vos nouveaux identifiants pour vous connecter à %s est:\n\nNom d\'utilisateur : %s\nMot de passe : %s';
$login_new_password = 'Nouveau mot de passe';
$login_lost_password_success = 'Un nouveau mot de passe a été envoyé à %s';
$login_lost_password_failed = 'Désolé, l\'adresse e-mail  \'%s\' n\'est pas enregistrée.';

//2.14
$login_fullscreen = $GO_CONFIG->title.' a été ouvert automatiquement plein-écran pour vous. Vous pouvez fermer cette fenêtre ou relancer Group-Office une nouvelle fois.';
$login_start = 'Relancer à nouveau';

//2.16
$login_to = 'Bienvenue sur %s';
$login_no_account = 'Vous n\'ètes pas encore enregistré ?';
$login_register = 'Cliquez ici pour vous enregistrer sur %s';
$login_no_account_text = 'Afin de vous connecter sur notre système, vous devez être enregistré. Vous devrez créer un identifiant et un mot de passe que vous utiliserez à chaque connexion.';
?>
