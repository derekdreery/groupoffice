<?php
/////////////////////////////////////////////////////////////////////////////////
//
// Copyright Intermesh
// 
// This file is part of Group-Office. You should have received a copy of the
// Group-Office license along with Group-Office. See the file /LICENSE.TXT
// 
// If you have questions write an e-mail to info@intermesh.nl
//
// @copyright Copyright Intermesh
// @version $Id$
// @author Merijn Schering <mschering@intermesh.nl>
//
// French Translation
// Version : 4.0.99
// Author : Lionel JULLIEN / Boris HERBINIERE-SEVE
// Date : September, 20 2012
//
/////////////////////////////////////////////////////////////////////////////////

//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('email'));

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
$lang['email']['notification_subject']='Lire : %s';
$lang['email']['notification_body']='Votre message ayant pour sujet "%s" a été lu à %s';
$lang['email']['errorGettingMessage']='Impossible d\'obtenir le message sur le serveur';
$lang['email']['no_recipients_drafts']='Pas de destinataire';
$lang['email']['usage_limit'] = '%s de %s utilisé';
$lang['email']['usage'] = '%s utilisé';
$lang['email']['event']='Rendez-vous';
$lang['email']['calendar']='Calendrier';
$lang['email']['quotaError']='Votre boîte aux lettres est pleine. Vider la corbeille de votre dossier en premier. Si elle est déjà vide et que votre boîte aux lettres est toujours pleine, vous devez désactiver la corbeille pour supprimer les messages des autres dossiers. Pour désactiver la corbeille :\n\nAdministration -> Comptes de messagerie -> Double-cliquez sur votre compte -> Onglet Dossier';
$lang['email']['draftsDisabled']='Votre message n\'a pas pu être sauvegardé car votre dossier \'Brouillons\' est désactivé<br /> <br />Aller dans : Administration -> Comptes de messagerie -> Double-cliquez sur votre compte -> Onglet Dossier pour le configurer.';
$lang['email']['noSaveWithPop3']='Votre message n\'a pas pu être sauvegardé car votre compte de messagerie utilise le protocole POP3';
$lang['email']['replyHeader']='%s %s à %s %s a écrit:';
$lang['email']['alias']='Alias';
$lang['email']['aliases']='Alias';
$lang['email']['noUidNext']='Votre serveur de messagerie ne supporte pas UIDNEXT. Le dossier \'Brouillons\' est donc automatiquement désactivé.';
$lang['email']['disable_trash_folder']='Le déplacement du message dans le dossier corbeille a échoué ! Vous avez peut être dépassé votre quota. Vous pouvez libérer de l\'espace en désactivant le dossier corbeille. Allez dans Administration -> Comptes -> Double-cliquez sur votre compte -> Dossiers';
$lang['email']['error_move_folder']='Impossible de déplacer le dossier';
$lang['email']['error_getaddrinfo']='L\'adresse hôte spécifiée est invalide';
$lang['email']['error_authentication']='Nom d\'utilisateur ou mot de passe invalide';
$lang['email']['error_connection_refused']='La connection a été refusée ! Vérifiez l\'adresse du serveur et le port.';
$lang['email']['feedbackDeleteFolderFailed']= 'Echec lors de la suppression du dossier';
$lang['email']['goAlreadyStarted']='Group-Office est déjà démarré. Le module email est maintenant chargé dans Group-Office. Fermez cette fenêtre et composez votre message dans Group-Office.';
$lang['email']['iCalendar_event_invitation']='Ce message contient une invitation a un rendez-vous.';
$lang['email']['iCalendar_event_not_found']='Ce message contient une mise à jour pour un rendez-vous qui n\'existe plus.';
$lang['email']['iCalendar_update_available']='Ce message contient une mise à jour pour un rendez-vous existant.';
$lang['email']['iCalendar_update_old']='Ce message contient un rendez-vous qui a déjà été traité.';
$lang['email']['iCalendar_event_cancelled']='Ce message contient une annulation de rendez-vous.';
$lang['email']['iCalendar_event_invitation_declined']='Ce message contient une invitation à un rendez-vous que vous avez décliné.';
$lang['email']['untilDateError']='Une erreur est apparue ! Processus stoppé...';
$lang['email']['autolinked']='Ce message a été automatiquement lié à %s';
