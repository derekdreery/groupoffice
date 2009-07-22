<?php
//French Translation v1.0
//Author : Lionel JULLIEN
//Date : September, 04 2008

//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('calendar'));

$lang['calendar']['name'] = 'Calendrier';
$lang['calendar']['description'] = 'Module de gestion du calendrier. Chaque utilisateur peut ajouter, éditer ou supprimer des rendez-vous. Les rendez-vous des autres utilisateurs peuvent être consultés (selon les permissions accordées).';

$lang['link_type'][1]='Rendez-vous';

$lang['calendar']['groupView'] = 'Vue de groupe';
$lang['calendar']['event']='Evènement';
$lang['calendar']['startsAt']='Débute à';
$lang['calendar']['endsAt']='Termine à';

$lang['calendar']['exceptionNoCalendarID'] = 'ERREUR FATALe : calendrier sans ID !';
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
$lang['calendar']['statuses']['TENTATIVE'] = 'Tentative';
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

// 3.02-stable-10
$lang['calendar']['already_accepted']='Vous avez déjà accepté ce rendez-vous.';

$lang['calendar']['private']='Privé';

$lang['calendar']['import_success']='%s rendez-vous on été importé(s)';

$lang['calendar']['printTimeFormat']='De %s à %s';
$lang['calendar']['printLocationFormat']='. Lieu: %s';
$lang['calendar']['printPage']='Page %s sur %s';
$lang['calendar']['printList']='Liste des rendez-vous';

$lang['calendar']['printAllDaySingle']='Toute la journée';
$lang['calendar']['printAllDayMultiple']='Tous les jours du %s au %s';
