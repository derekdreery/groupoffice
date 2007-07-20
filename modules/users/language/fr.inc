<?php

// LANG: "fr", ENCODING: UTF-8
// Author: L. Jullien
// 15 Jan 2007

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
Entrez une question secrète à laquelle seul cet utilisateur pourra répondre. Nous poserons cette question dans le cas ou l utilisateur a oublié son mot de passe.
Lorsque la réponse correcte est donnée, un email sera envoyé à l adresse mail enregistrée avec le nom d utilisateur et un nouveau mot de passe pour se connecter.
registration_question_text;

$registration_text=<<<registration_text
Remplissez ce formulaire et cliquez sur 'Ok' pour vous enregistrer. Les champs marqués d une * sont nécessaires.<br />
Les informations fournies ne sont utilisées que pour Group-Office, nous ne les fournirons pas à d autre personnes.
registration_text;

$registartion_authcode_text = "Entrez un code d\'autorisation. Le code d\'autorisation est un code que les autres utilisateurs devront connaître pour ajouter cet utilisateur à leur liste de contacts ainsi que pour voir vos informations détaillées.";
$registartion_confirm_authcode = "Confirmez le code d'autorisation";
$registration_visibility = "Rendre cet utilisateur visible par tous les utilisateurs.";
$registration_success = "<h1>Félicitations !</h1>Votre enregistrement s'est déroulé avec succès et un message de bienvenue vous a été envoyé à :";
$registration_failure = "Une erreur est apparue pendant l\'enregistrement de vos informations. Merci de réessayer.";
$registration_mail_subject = "Bienvenue dans Group-Office.";
$registration_folder_error = "Impossible de créer le dossier personnel. Contactez-nous pour le restaurer !";
$registration_title = "Enregistré dans Group-Office";
$registration_email = "Créer une adresse email pour cet utilisateur au Group-Office.";
$registration_samba = "Fichier & Serveur d'impression.";
$registration_email_error = "Une erreur est apparue pendant la création de votre compte. Contactez nous.";
$strPasswordConfirm = "Confirmez votre mot de passe";
$strPassword = "Mot de passe";
$reg_company_info = "Informations sur la société";
$max_user_limit = "Le nombre maximum d\'utilisateurs a été atteint.";
$max_users_text = "Désolé vous ne pouvez pas créer plus d'utilisateur. Contactez nous si vous souhaitez augmenter votre nombre d'utilisateurs.";
$ml_sent_items = "Eléments envoyés";
$ml_spam = "Couriers indésirables";
$ac_samba_user = "L'utilisateur a accédé à ce fichier et au serveur d'impression.";
$ml_email_only = "Seulement nécessaire lorsque vous ne voulez pas créer une adresse email";
$register_new_user = 'Nouvelle inscription dans Group-Office';
$register_thanks = '<h2>Merci</h2>. Votre inscription sera traitée dès que possible. Vous recevrez plus d\'informations.';
$register_accept = 'Ajouter aux utilisateurs';
$register_addressbook = 'Ajouter au carnet d\'adresses';
$register_title = 'S\'enregister';
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
$admin_groups = 'Groupe utilisateurs';
$admin_groups_user = 'L\'utilistateur est membre des groupes :';

//2.13
$registration_mail_subject = "Bienvenue dans %groupoffice_title%"; //changed string
$registration_title = "Enregistrer dans %groupoffice_title%"; //changed string
$lang_modules['users'] = 'Utilisateurs';

//2.14
$users_enabled = 'Compte valide';
$admin_groups_visible = 'L\'utilisateur est visible dans les groupes suivants';
$admin_look_feel = 'Look & feel';

//2.16
$disabled_user_fields_text = 'Selectionner les champs pouvant être modifié par les utilisateurs dans leur profil.';
$admin_disabled_user_fields = 'Désactiver les champs utilisateur';
$admin_registration_unconfirmed = 'Notification d\'enregistrement non confirmé';
$admin_registration_confirmation = 'Notifiaction d\'enregistrement confirmé';
$admin_registration_confirmation_text = 'Ceci est le message que les utilisateurs reçoivent lorsqu\'ils ont un nouveau compte actif. Laisser vide pour ne pas envoyer de message.';
$admin_registration_unconfirmed_text = 'Ceci est le message que les utilisateurs reçoivent lorsqu\'ils ont un nouveau compte inactif. Laisser vide pour ne pas envoyer de message.';
$admin_subject = 'Sujet';

//string modified:
$registration_success = "<h1>Félicitations !</h1>Le nouvel utilisateur a été créé avec succès.";
$registration_success_activate = "<h1>Activation nécessaire !</h1>Le nouvel utilisateur a été créé avec succès. Néanmoins, l\'administrateur doit activer votre compte.";
$admin_new_user_subject = 'Nouvel utilisateur enregistré';
$admin_new_user_body = 'Un nouvel utilisateur s\'est enregistré. Cliquez sur le lien ci-dessous pour visualiser le compte.';
$admin_new_user_subject_activate = 'Nouvel utilisateur enregistré. Activation nécessaire !';
$admin_password_changed = 'Mot de passe changé avec succès !';
$registration_self_success = 'Bienvenue dans %s!';
$registration_self_success_text = 'Votre compte a été créé avec succès. Vous pouvez vous connecter en utilisant votre nom d\'utilisateur et votre mot de passe.';
?>
