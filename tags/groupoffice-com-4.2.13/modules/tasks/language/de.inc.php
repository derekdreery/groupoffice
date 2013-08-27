<?php
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('tasks'));


$lang['tasks']['name']='Aufgaben';
$lang['tasks']['description']='Modul zum Verwalten von Aufgaben';

$lang['link_type'][12]=$lang['tasks']['task']='Aufgabe';
$lang['tasks']['status']='Status';


$lang['tasks']['scheduled_call']='Geplanter Anruf um %s';

$lang['tasks']['statuses']['NEEDS-ACTION']='Aktion erforderlich';
$lang['tasks']['statuses']['ACCEPTED']='Wurde angenommen';
$lang['tasks']['statuses']['DECLINED']='Wurde abgelehnt';
$lang['tasks']['statuses']['TENTATIVE']='Ist vorläufig';
$lang['tasks']['statuses']['DELEGATED']='Wurde aufgeteilt';
$lang['tasks']['statuses']['COMPLETED']='Ist erledigt';
$lang['tasks']['statuses']['IN-PROCESS']='Ist in Bearbeitung';

$lang['tasks']['import_success']='%s Aufgaben wurden importiert';

$lang['tasks']['call']='Anruf';

$lang['tasks']['dueAtdate']='Fällig am %s';

$lang['tasks']['list']='Aufgabenliste';
$lang['tasks']['tasklistChanged']="* Aufgabenliste geändert von '%s' zu '%s'";
$lang['tasks']['statusChanged']="* Status geändert von '%s' zu '%s'";
$lang['tasks']['multipleSelected']='Mehrere Aufgabenlisten gewählt';
$lang['tasks']['incomplete_delete']='Keine Berechtigung um alle gewählten Aufgaben zu löschen.';
?>
