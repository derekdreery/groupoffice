<?php
//French Translation v1.0
//Author : Lionel JULLIEN
//Date : September, 05 2008

//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('tasks'));

$lang['tasks']['name']='Tâches';
$lang['tasks']['description']='Module de gestion des tâches';

$lang['link_type'][12]=$lang['tasks']['task']='Tâche';
$lang['tasks']['status']='Statut';

// 3.0-14
$lang['tasks']['statuses']['NEEDS-ACTION']= 'Action nécessaire';
$lang['tasks']['statuses']['ACCEPTED']= 'Accepté';
$lang['tasks']['statuses']['DECLINED']= 'Décliné';
$lang['tasks']['statuses']['TENTATIVE']= 'Tentative';
$lang['tasks']['statuses']['DELEGATED']= 'Délégué';
$lang['tasks']['statuses']['COMPLETED']= 'Terminé';
$lang['tasks']['statuses']['IN-PROCESS']= 'En cours';

?>