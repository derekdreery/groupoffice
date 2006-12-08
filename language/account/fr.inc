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



require_once($GO_LANGUAGE->get_fallback_base_language_file('account'));

//privacy
$privacy_text = "Choisissez les utilisateurs et les groupes pour lesquels vous souhaitez être visible.";

//security
$security_text = "Entrez votre mot de passe et les champs que vous voulez changer et cliquez sur 'Enregistrer'.";
$security_current_password = "Mot de passe courant";
$security_new_password = "Nouveau mot de passe";
$security_confirm_password = "Confirmer le mot de passe";
$security_new_authcode = "Nouveau code d'autorisation";
$security_confirm_authcode = "Confirmer le code d'autorisation";
$security_question = "Question secrète";
$security_answer = "Réponse";
$security_wrong_password = "Mauvais mot de passe.";
$security_tasks = "Tâche terminé.";
$security_password_update = "Mot de passe modifié.";
$security_authcode_update = "Code d'autorisation modifié";
$security_question_update = "Question et réponse modifié.";
$security_question_fail = "Vous n'avez pas rempli et la question et la réponse.";

$acProfile = "Profil";
$acSecurity = "Sécurité";
$acPrivacy = "Intimité";
$acManager = "Vôtre compte";
$acStatistics = "Statistiques";
$acProfileText = "Cliquez sur 'Enregistrer' pour changer voter profil. Les champs marqués d'une * sont obligatoires.";
$acTitle = "Votre compte";
$ac_login_info = "Information de connexion";
$ac_registration_time = "Heure d'enregistrement";
$ac_lastlogin = "Dernière connexion";
$ac_logins = "Nombre total de connexions";
$ac_disk_usage = "Utilisation du disque";
$ac_max_diskspace = "Taille maximum de l'espace disque";
$ac_available_diskspace = "Taille disponible sur l'espace disque";
$ac_used_diskspace = "Espace disque utilisé";
$ac_used = "utilisé";

$ac_add_user = 'Demande d\'autorisation';
$ac_auth_error = 'Une erreur est apparue pendant la demande d\'autorisation. Veuillez recommencer.';
$ac_auth_success = 'Votre compte est maintenant visible pour cet utilisateur et un message lui a été envoyé lui indiquant que vous avez accepter sa requête.';
$ac_auth_decline = 'Un message a été envoyé à cet utilisateur pour lequel vous avez décliné sa requête.';
$ac_auth_accept_mail_title = 'Demande d\'autorisation de Group-Office accepté';
$ac_auth_decline_mail_title = 'Demande d\'autorisation de Group-Office refusé';

$ac_auth_accept_mail_body = 'a accepté votre requête de vous rendre visible pour ce compte.';
$ac_auth_decline_mail_body = 'a refusé votre requête de vous rendre visible pour ce compte.';
$ac_request_auth = 'demande votre permission pour rendre votre compte visible. Voulez vous rendre votre compte visible pour cet utilisateur ?';
$ac_request_auth_mail_title ='Demande d\'autorisation de Group-Office';
$ac_invalid_email = 'L\'adresse mail que vous avez entré n\'est pas enregistré dans le système. Merci de recommencer.';
$ac_request_mail_body_prefix = 'Un message a été envoyé à ';
$ac_request_mail_body_suffix = 'demander sa permission de rendre son compte visible pour vous.';
$ac_auth_text = 'Entrez l\'adresse mail de l\'utilisateur dont vous souhaitez voir le compte.';

$ac_accept = 'Accepter';
$ac_decline = 'Refuser';
$ac_salutation = 'Cher(e)';
?>
