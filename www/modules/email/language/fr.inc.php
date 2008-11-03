<?php
//French Translation v1.0
//Author : Lionel JULLIEN
//Date : September, 04 2008

//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('email'));

$lang['email']['name'] = 'E-mail';
$lang['email']['description'] = 'Module de gestion des E-mails. chaque utilisateur peut envoyer, recevoir et tranférer des messages.';

$lang['link_type'][9]='E-mail';

$lang['email']['feedbackNoReciepent'] = 'Vous n\'avez pas renseigné de destinataire';
$lang['email']['feedbackSMTPProblem'] = 'Il y a eut un problème de communication avec le serveur SMTP : ';
$lang['email']['feedbackUnexpectedError'] = 'Il y a eut un problème lors de la construction de l\'e-mail : ';
$lang['email']['feedbackCreateFolderFailed'] = 'Echec lors de la création du dossier';
$lang['email']['feedbackSubscribeFolderFailed'] = 'Echec lors de l\'abonnement au dossier';
$lang['email']['feedbackUnsubscribeFolderFailed'] = 'Echec lors du désabonnement au dossier';
$lang['email']['feedbackCannotConnect'] = 'Impossible de se connecter à %1$s<br /><br />Le serveur de mail a retourné l\'erreur suivante : %2$s';
$lang['email']['inbox'] = 'Boite de réception';

$lang['email']['spam']='Spam';
$lang['email']['trash']='Corbeille';
$lang['email']['sent']='Eléments envoyés';
$lang['email']['drafts']='Brouillons';

$lang['email']['no_subject']='Pas de sujet';
$lang['email']['to']='A';
$lang['email']['from']='De';
$lang['email']['subject']='Sujet';
$lang['email']['no_recipients']='Pas de destinataire';
$lang['email']['original_message']='----- MESSAGE ORIGINAL -----';
$lang['email']['attachments']='Pièces jointes';

// 3.0-14
$lang['email']['notification_subject']='Lire : %s';
$lang['email']['notification_body']='Votre message ayant pour sujet "%s" a été lu à %s';