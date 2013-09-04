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
// Version : 3.7.29 
// Author : Lionel JULLIEN
// Date : September, 27 2011
//
/////////////////////////////////////////////////////////////////////////////////

//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('calendar'));

$lang['calendar']['name'] = 'Calendrier';
$lang['calendar']['description'] = 'Module de gestion du calendrier. Chaque utilisateur peut ajouter, éditer ou supprimer des rendez-vous. Les rendez-vous des autres utilisateurs peuvent être consultés (selon les permissions accordées).';
$lang['link_type'][1]='Rendez-vous';
$lang['calendar']['groupView'] = 'Vue de groupe';
$lang['calendar']['event']='Evènement';
$lang['calendar']['startsAt']='Débute à';
$lang['calendar']['endsAt']='Termine à';
$lang['calendar']['exceptionNoCalendarID'] = 'ERREUR FATALE : calendrier sans ID !';
$lang['calendar']['appointment'] = 'Rendez-vous : ';
$lang['calendar']['allTogether'] = 'Tous ensemble';
$lang['calendar']['location']='Lieu';
$lang['calendar']['invited']='Vous êtes invité à l\'évènement suivant';
$lang['calendar']['acccept_question']='Acceptez vous cet évènement ?';
$lang['calendar']['accept']='Accepter';
$lang['calendar']['decline']='Décliner';
$lang['calendar']['bad_event']='Cet évènement n\'existe plus';
$lang['calendar']['subject']='Sujet';
$lang['calendar']['status']='Statut';
$lang['calendar']['statuses']['NEEDS-ACTION'] = 'Action nécessaire';
$lang['calendar']['statuses']['ACCEPTED'] = 'Accepté';
$lang['calendar']['statuses']['DECLINED'] = 'Decliné';
$lang['calendar']['statuses']['TENTATIVE'] = 'Accepté sous réserve';
$lang['calendar']['statuses']['DELEGATED'] = 'Délégué';
$lang['calendar']['statuses']['COMPLETED'] = 'Terminé';
$lang['calendar']['statuses']['IN-PROCESS'] = 'En cours';
$lang['calendar']['accept_mail_subject'] = 'Invitation pour \'%s\' accepté';
$lang['calendar']['accept_mail_body'] = '%s a accepté votre invitation pour :';
$lang['calendar']['decline_mail_subject'] = 'Invitation pour \'%s\' déclinée';
$lang['calendar']['decline_mail_body'] = '%s a décliné votre invitation pour :';
$lang['calendar']['location'] = 'Lieu';
$lang['calendar']['and'] = 'et';
$lang['calendar']['repeats'] = 'Répéter chaque %s';
$lang['calendar']['repeats_at'] = 'Répéter chaque %s le %s';//eg. Repeats every month at the first monday
$lang['calendar']['repeats_at_not_every'] = 'Répéter chaque %s %s le %s';//eg. Repeats every 2 weeks at monday
$lang['calendar']['until']='jusqu\'à'; 
$lang['calendar']['not_invited']='Vous n\'êtes pas invité à cet évènement. Vous devriez vous connecter sous un autre nom.';
$lang['calendar']['accept_title']='Accepté';
$lang['calendar']['accept_confirm']='Le propriétaire de cet évènement sera notifié que vous avez accepté l\'invitation';
$lang['calendar']['decline_title']='Décliné';
$lang['calendar']['decline_confirm']='Le propriétaire de cet évènement sera notifié que vous avez décliné l\'invitation';
$lang['calendar']['cumulative']='Règle de récurrence invalide ! La prochaine occurrence ne peut pas débuter avant que la précedente soit terminée.';
$lang['calendar']['already_accepted']='Vous avez déjà accepté ce rendez-vous.';
$lang['calendar']['private']='Privé';
$lang['calendar']['import_success']='%s rendez-vous ont été importé(s)';
$lang['calendar']['printTimeFormat']='De %s à %s';
$lang['calendar']['printLocationFormat']='. Lieu: %s';
$lang['calendar']['printPage']='Page %s sur %s';
$lang['calendar']['printList']='Liste des rendez-vous';
$lang['calendar']['printAllDaySingle']='Toute la journée';
$lang['calendar']['printAllDayMultiple']='Tous les jours du %s au %s';
$lang['calendar']['calendars']='Calendriers';
$lang['calendar']['open_resource']='Réservation ouverte';
$lang['calendar']['resource_mail_subject']='La ressource \'%s\' est réservée pour \'%s\' le \'%s\'';//%s is resource name, %s is event name, %s is start date
$lang['calendar']['resource_mail_body']='%s a fait une réservation pour la ressource \'%s\'. En tant qu\'administrateur de cette ressource, merci de valider la réservation.'; //First %s is the name of the person who created the event. Second is the calendar name
$lang['calendar']['resource_modified_mail_subject']='Réservation pour la ressource \'%s\' - \'%s\' le \'%s\' modifiée';//%s is resource name, %s is event name, %s is start date
$lang['calendar']['resource_modified_mail_body']='%s a modifié une réservation pour la ressource \'%s\'. En tant qu\'administrateur de cette ressource, merci de valider la réservation.'; //First %s is the name of the person who created the event. Second is the calendar name
$lang['calendar']['your_resource_modified_mail_subject']='Votre réservation pour \'%s\' le \'%s\' en statut \'%s\' est modifiée';//is resource name, %s is event name, %s is start date
$lang['calendar']['your_resource_modified_mail_body']='%s a modifié votre réservation de la ressource \'%s\'.';
$lang['calendar']['your_resource_accepted_mail_subject']='Votre réservation pour \'%s\' le \'%s\' est acceptée';//%s is resource name, %s is start date
$lang['calendar']['your_resource_accepted_mail_body']='%s a accepté votre réservation pour la ressource \'%s\'.'; //First %s is the name of the person who created the event. Second is the calendar name
$lang['calendar']['your_resource_declined_mail_subject']='Votre réservation pour la ressource \'%s\' le \'%s\' est refusée';//%s is resource name, %s is start date
$lang['calendar']['your_resource_declined_mail_body']='%s a refusé votre réservation pour la ressource \'%s\'.'; //First %s is the name of the person who created the event. Second is the calendar name
$lang['calendar']['birthday_name']='Anniversaire : {NAME}';
$lang['calendar']['birthday_desc']='{NAME} a {AGE} ans aujourd\'hui';
$lang['calendar']['unauthorized_participants_write']='Vous n\'avez pas les permissions suffisantes pour planifier un rendez-vous avec les utilisateurs suivants :<br /><br />{NAMES}<br /><br />Vous pouvez leurs envoyer une invitation pour qu\'ils puissent accepter et planifier votre rendez-vous.';
$lang['calendar']['noCalSelected'] = 'Aucun calendrier n\'a été selectionné pour cette vue. Sélectionnez au moins un calendrier.';
$lang['calendar']['month_times'][1]='le premier';
$lang['calendar']['month_times'][2]='le deuxième';
$lang['calendar']['month_times'][3]='le troisième';
$lang['calendar']['month_times'][4]='le quatrième';
$lang['calendar']['month_times'][5]='le cinquième';

$lang['calendar']['statuses']['CONFIRMED']= 'Confirmé';
$lang['calendar']['repeats_not_every']= 'Répéter tous les %s %s';
$lang['calendar']['rightClickToCopy']='Clic droit pour copier le lien';
$lang['calendar']['invitation']='Invitation';
$lang['calendar']['invitation_update']='Invitation mise à jour';
$lang['calendar']['cancellation']='Annulation';
$lang['calendar']['non_selected']= 'dans les calendriers non sélectionnés';
$lang['calendar']['linkIfCalendarNotSupported']='N\'utilisez les liens ci-dessous que si votre client email ne possède pas la fonctionnalité de calendrier.';
?>
