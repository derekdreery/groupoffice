<?php
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('tasks'));

$lang['tasks']['name']='Aufgaben';
$lang['tasks']['description']='Beschreibung hier eingeben';

$lang['link_type'][12]=$lang['tasks']['task']='Aufgabe';
$lang['tasks']['status']='Status';

$lang['tasks']['statuses']['NEEDS-ACTION']= 'Benötigt Aktion';
$lang['tasks']['statuses']['ACCEPTED']= 'Akzeptiert';
$lang['tasks']['statuses']['DECLINED']= 'Abgelehnt';
$lang['tasks']['statuses']['TENTATIVE']= 'Vorläufig';
$lang['tasks']['statuses']['DELEGATED']= 'Delegiert';
$lang['tasks']['statuses']['COMPLETED']= 'Beendet';
$lang['tasks']['statuses']['IN-PROCESS']= 'In Bearbeitung';
?>