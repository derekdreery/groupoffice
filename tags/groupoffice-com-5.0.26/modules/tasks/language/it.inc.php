<?php
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('tasks'));

$lang['tasks']['name']='Attività';
$lang['tasks']['description']='Inserisci qui una descrizione';

$lang['link_type'][12]=$lang['tasks']['task']='Attività';
$lang['tasks']['status']='Stato';

$lang['tasks']['statuses']['NEEDS-ACTION']= 'Richiesta Azione';
$lang['tasks']['statuses']['ACCEPTED']= 'Accettato';
$lang['tasks']['statuses']['DECLINED']= 'Rifiutato';
$lang['tasks']['statuses']['TENTATIVE']= 'Tentativo';
$lang['tasks']['statuses']['DELEGATED']= 'Delegato';
$lang['tasks']['statuses']['COMPLETED']= 'Completato';
$lang['tasks']['statuses']['IN-PROCESS']= 'In corso';
?>