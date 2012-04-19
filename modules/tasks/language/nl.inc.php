<?php
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('tasks'));

$lang['tasks']['name']='Taken';
$lang['tasks']['description']='Taken module';

$lang['link_type'][12]=$lang['tasks']['task']='Taak';
$lang['tasks']['status']='Status';

$lang['tasks']['scheduled_call']='Telefoongesprek gepland op %s';

$lang['tasks']['statuses']['NEEDS-ACTION']='Actie nodig';
$lang['tasks']['statuses']['ACCEPTED']='Geaccepteerd';
$lang['tasks']['statuses']['DECLINED']='Geweigerd';
$lang['tasks']['statuses']['TENTATIVE']='Voorlopig';
$lang['tasks']['statuses']['DELEGATED']='Gedelegeerd';
$lang['tasks']['statuses']['COMPLETED']='Voltooid';
$lang['tasks']['statuses']['IN-PROCESS']='Wordt aan gewerkt';

$lang['tasks']['call']='Bellen';

$lang['tasks']['import_success']='%s taken werden geïmporteerd';
$lang['tasks']['dueAtdate']='Verloopt op %s';

$lang['tasks']['list']='Takenlijst';
$lang['tasks']['tasklistChanged']="* Takenlijst is veranderd van '%s' naar '%s'";
$lang['tasks']['statusChanged']="* Status is veranderd van'%s' naar '%s'";
$lang['tasks']['multipleSelected']='Meerdere takenlijsten geselecteerd';
$lang['tasks']['incomplete_delete']='U heeft niet voldoende rechten om alle geselecteerde taken te verwijderen';