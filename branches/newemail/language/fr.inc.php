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
$lang['email']['feedbackSMTPProblem'] = 'Il y a eu un problème de communication avec le serveur SMTP : ';
$lang['email']['feedbackUnexpectedError'] = 'Il y a eu un problème lors de la construction de l\'e-mail : ';
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

// 3.02-stable-10
$lang['email']['errorGettingMessage']='Impossible d\'obtenir le message sur le serveur';
$lang['email']['no_recipients_drafts']='Pas de destinataire';
$lang['email']['usage_limit'] = '%s de %s utilisé';
$lang['email']['usage'] = '%s utilisé';

$lang['email']['event']='Rendez-vous';
$lang['email']['calendar']='Calendrier';

$lang['email']['quotaError']="Votre boîte aux lettres est pleine. Vider la corbeille de votre dossier en premier. Si elle est déjà vide et que votre boîte aux lettres est toujours pleine, vous devez désactiver la corbeille pour supprimer les messages des autres dossiers. Pour désactiver la corbeille:\n\nParamètres -> Comptes de messagerie -> Double-cliquez sur votre compte -> Onglet Dossier";

$lang['email']['draftsDisabled']="Votre message n\'a pas pu être sauvegardé car votre dossier 'Brouillons' est désactivé<br /> <br />Aller dans : Paramètres -> Comptes de messagerie -> Double-cliquez sur votre compte -> Onglet Dossier pour le configurer.";
$lang['email']['noSaveWithPop3']='Votre message n\'a pas pu être sauvegardé car votre compte de messagerie utilise POP3';

//$lang['email']['goAlreadyStarted']='Group-Office was al gestart. Het e-mailscherm wordt nu geladen in Group-Office. Sluit dit venster en stel uw bericht op in Group-Office.';

//At Tuesday, 07-04-2009 on 8:58 Group-Office Administrator <test@intermeshdev.nl> wrote:
$lang['email']['replyHeader']='Le %s, %s à %s %s a écrit:';
$lang['email']['alias']='Alias';
$lang['email']['aliases']='Alias';

$lang['email']['noUidNext']='Votre serveur de messagerie ne supoorte pas UIDNEXT. Le dossier \'Brouillons\' est donc automatiquement désactivé.';
