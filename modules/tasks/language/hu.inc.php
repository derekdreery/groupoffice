<?php
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('tasks'));

$lang['tasks']['name']='Feladatok';
$lang['tasks']['description']='Put a description here';

$lang['link_type'][12]=$lang['tasks']['task']='Feladat';
$lang['tasks']['status']='Állapot';


$lang['tasks']['scheduled_call']='Scheduled call at %s';

$lang['tasks']['statuses']['NEEDS-ACTION'] = 'Needs action';
$lang['tasks']['statuses']['ACCEPTED'] = 'Elfogadva';
$lang['tasks']['statuses']['DECLINED'] = 'Elutasítva';
$lang['tasks']['statuses']['TENTATIVE'] = 'Kísérlet';
$lang['tasks']['statuses']['DELEGATED'] = 'Továbbadva';
$lang['tasks']['statuses']['COMPLETED'] = 'Befejezve';
$lang['tasks']['statuses']['IN-PROCESS'] = 'Folyamatban';

$lang['tasks']['import_success']='%s feladat importálva lett';

$lang['tasks']['call']='Hívás';

$lang['tasks']['dueAtdate']='Dátum: %s';
?>