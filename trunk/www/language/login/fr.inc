<?php

// LANG: "fr", ENCODING: UTF-8
// Author: A. Kermorvant : armek@akserver.dyndns.org
// 24 Oct 2004

// FOR TRANSLATORS:
//
//   1. PLEASE PUT YOUR CONTACT INFO IN THE ABOVE LINE
//      (at least a valid email address)
//
//   2. PLEASE TRY TO USE UTF-8 FOR ENCODING;
//      (if this is not possible, please include a comment
//       that states what encoding is necessary.)

require_once($GO_LANGUAGE->get_fallback_base_language_file('login'));

$strWelcomeTo = "Bienvenue à ";
$strUseMenu = "Utiliser le menu ou cliquer sur les liens rapides ci-dessous pour commencer à utiliser ";
$click_to_login = "Cliquer ici pour vous connecter.";
$login_text = "Entrer vôtre nom d'utilisateur et votre mot de passe pour vous connecter";
$login_username = "Utilisateur";
$login_password = "Mot de passe";
$login_missing_field = "Vous n'avez pas rempli vôtre nom d'utilisateur ou votre mot de passe.";
$login_bad_login = "Mauvais Nom d'utilisateur ou mot de passe";
$login_remind = "Me laisser connecté jusqu'à ce que j'appuie sur le boutton de déconnection.";
$login_language = 'Langue';

//2.12
$login_register = 'ou cliquet sur %s pour valider';
$login_here = "ici";
$login_lost_password = 'Vous avez perdu votre Password?';
$login_lost_password_title = 'Vous avez retrouvé votre Password?';
$login_lost_password_text= 'SVP entrez votre adresse e-mail et un nouveau password vous sera envoyé à cette adresse.';
$login_lost_password_mail_body = 'Vous avez demandé un nouveau password pour %s. Votre nouveau login pour vous connecter à  %s est:\n\nUsername: %s\nPassword: %s\n\nBonne chance!';
$login_new_password = 'Nouveau Password';
$login_lost_password_success = 'Un nouveau Password a été envoyé à %s';
$login_lost_password_failed = 'Désolé, l\'adresse e-mail  \'%s\' n\'est pas enregistrée.';

//2.14
$login_fullscreen = $GO_CONFIG->title.' a été ouvert automatiquement plein-écran pour vous. Vous pouvez fermer cette fenêtre ou relancer Group-Office une nouvelle fois.';
$login_start = 'Relancer à nouveau';

?>
