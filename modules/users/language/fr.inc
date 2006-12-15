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

require_once($GO_LANGUAGE->get_fallback_language_file('users'));

$strLogins = "Connexions";
$strRegistrationDate = "Date d'enregistrement";
$strUsers = "utilisateur(s)";
$user_profile = "Profil utilisateur";
$ac_login_info = "Information de connexion";
$ac_registration_time = "Heure d'enregistrement";
$ac_lastlogin = "Dernière connexion";
$ac_logins = "Nombre de connexions";
$strMaxOf = "enregistré depuis le maximum de";
$strUsername = "Nom d'utilisateur";
$delete_fail = "Vous ne pouvez pas vous supprimer vous même ainsi que le super utilisateur.";
$admin_new_user = "Nouvel utlisateur";

$admin_modules = 'Modules';
$admin_module_access = 'Cet utilisateur a accès aux modules suivants :';

$admin_password = 'Mot de passe';
$admin_confirm_password = 'Confirmez le mot de passe';
$admin_change_password = 'Changer le mot de passe';
$admin_pass_match = 'Les mots de passe ne correspondent pas. Réessayez.';

//registration
$registration_personal_info = "Information personnelle";
$registration_account_info = "Information du compte";
$registartion_username_text = "Entrez un nom d'utilisateur et un mot de passe pour que cet utilisateur puisse se connecter à ce site.";

$registration_question_text=<<<registration_question_text
Entrez une question secrète pour laquelle seul cet utilisateur pourra répondre. Nous poserons cette question dans le cas ou l utilisateur a oublié son mot de passe.
Lorsque la réponse correcte est donnée, un email sera envoyé à l adresse mail enregistrée avec le nom d utilisateur et un nouveau mot de passe pour se connecter.
registration_question_text;

$registration_text=<<<registration_text
Remplissez ce formulaire et cliquez sur 'Ok' pour vous enregistrer. Les champs marqués d une * sont nécessaires.<br />
Les informations fournies ne sont utilisée que pour Group-Office, nous ne les fournirons pas à d autre personnes.
registration_text;

$registartion_authcode_text = "Entrez un code d\'autorisation. Le code d\'autorisation est un code que les autres utilisateurs devront connaître pour ajouter cet utilisateur à leur liste de contacts ainsi que pour voir vos informations détaillées.";
$registartion_confirm_authcode = "Confirmez le code d'autorisation";
$registration_visibility = "Rendre cet utilisateur visible à tous les utilisateurs.";
$registration_success = "<h1>Félicitations !</h1>Votre enregistrement s'est déroulé avec succès et un message de bienvenue vous a été envoyé à :";
$registration_failure = "Une erreur est apparue pendant l\'enregistrement de vos informations. Merci de réessayer.";
$registration_mail_subject = "Bienvenue à Group-Office.";
$registration_folder_error = "Impossible de créer le dossier personnel. Contactez-nous pour le restaurer !";
$registration_title = "Enregistré à Group-Office";

$registration_email = "Créer une adresse mail pour cet utilisateur au Group-Office.";
$registration_samba = "Fichier & Serveur d'impression.";
$registration_email_error = "Une erreur est apparue pendant la création de votre compte. Contactez nous.";
$strPasswordConfirm = "Confirmez votre mot de passe";
$strPassword = "Mot de passe";

$reg_company_info = "Informations sur la société";

$max_user_limit = "Le nombre maximum d\'utilisateurs a été atteint.";
$max_users_text = "Désolé vous ne pouvez pas créer plus d'utilisateur. Contactez nous si vous souhaitez augmenter votre nombre d'utilisateurs.";
$ml_sent_items = "Eléments envoyés";
$ml_spam = "Spam";
$ac_samba_user = "L'utlisateur a accédé à ce fichier et au serveur d'impression.";
$ml_email_only = "Seulement nécessaire lorsque vous ne voulez pas créer une adresse de mail";
$register_new_user = 'Nouvelle inscription à Group-Office';
$register_thanks = '<h2>Merci</h2>. Votre inscription sera traitée dès que possible. Vous recevrez plus d\'informations.';
$register_accept = 'Ajouter aux utilisateurs';
$register_addressbook = 'Ajouter au carnet d\'adresses';
$register_title = 'Souscrire';
$registration_mail_body=<<<registration_mail_body
Cher(e) %last_name%,

Vous êtes enregistré sur Group-Office.
Nous espérons que vous apprecierez nos services.

Vous pouvez vous connecter avec les données suivantes :

Utilisateur  : %username%
Mot de passe : %password%

Cliquez sur le lien ci-dessous pour vous connecter :

registration_mail_body;

$reg_language = 'Langage';
$reg_theme = 'Thème';

$admin_use = 'Utiliser';
$admin_module = 'Module';
$admin_manage = 'Gestion';

$admin_groups = 'Groupe Utilisateurs';
$admin_groups_user = 'L\'utilistateur est membre des groupes :';

//2.13
$registration_mail_subject = "Bienvenu à %groupoffice_title%"; //changed string
$registration_title = "Enregistrer à %groupoffice_title%"; //changed string

$lang_modules['users'] = 'Utilisateurs';

//2.14
$users_enabled = 'Compte valide';

$admin_groups_visible = 'L\'utilisateur est visible dans les groupes suivants';
$admin_look_feel = 'Look & feel';

?>
