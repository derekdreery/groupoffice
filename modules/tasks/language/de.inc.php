<?php
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('tasks'));

$lang['tasks']['name']='Aufgaben';
$lang['tasks']['description']='Beschreibung hier eingeben';

$lang['link_type'][12]=$lang['tasks']['task']='Aufgabe';
$lang['tasks']['status']='Status';

$lang['tasks']['scheduled_call']='Scheduled call at %s';

$lang['tasks']['statuses']['NEEDS-ACTION']= 'Benötigt Aktion';
$lang['tasks']['statuses']['ACCEPTED']= 'Akzeptiert';
$lang['tasks']['statuses']['DECLINED']= 'Abgelehnt';
$lang['tasks']['statuses']['TENTATIVE']= 'Vorläufig';
$lang['tasks']['statuses']['DELEGATED']= 'Delegiert';
$lang['tasks']['statuses']['COMPLETED']= 'Erledigt';
$lang['tasks']['statuses']['IN-PROCESS']= 'In Bearbeitung';

$lang['tasks']['import_success']='%s Aufgaben wurden importiert';

$lang['tasks']['call']='Anruf';

$lang['tasks']['dueAtdate']='Fällig am %s';
?>