<?php
//French Translation v1.0
//Author : Lionel JULLIEN
//Date : September, 05 2008

// Update for 3.02-stable-10
// Author : Cyril DUCHENOY
// Date : July, 21 2009

//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('tasks'));

$lang['tasks']['name']='Tâches';
$lang['tasks']['description']='Module de gestion des tâches';

$lang['link_type'][12]=$lang['tasks']['task']='Tâche';
$lang['tasks']['status']='Statut';

$lang['tasks']['scheduled_call']='Appel téléphonique programmé le  %s';

$lang['tasks']['statuses']['NEEDS-ACTION']= 'Action nécessaire';
$lang['tasks']['statuses']['ACCEPTED']= 'Accepté';
$lang['tasks']['statuses']['DECLINED']= 'Décliné';
$lang['tasks']['statuses']['TENTATIVE']= 'Tentative';
$lang['tasks']['statuses']['DELEGATED']= 'Délégué';
$lang['tasks']['statuses']['COMPLETED']= 'Terminé';
$lang['tasks']['statuses']['IN-PROCESS']= 'En cours';

$lang['tasks']['import_success']='%s tâches importées avec succès';

$lang['tasks']['call']='Appel Téléphonique';

$lang['tasks']['dueAtdate']='Termine le %s';
?>