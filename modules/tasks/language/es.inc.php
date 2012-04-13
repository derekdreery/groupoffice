<?php
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('tasks'));

$lang['tasks']['name']='Tareas';
$lang['tasks']['description']='Por favor, introduzca una descripción';

$lang['link_type'][12]=$lang['tasks']['task']='Tare';
$lang['tasks']['status']='Estado';

$lang['tasks']['statuses']['NEEDS-ACTION']= 'Solicitud de Acción';
$lang['tasks']['statuses']['ACCEPTED']= 'Aceptada';
$lang['tasks']['statuses']['DECLINED']= 'Rechazada';
$lang['tasks']['statuses']['TENTATIVE']= 'Tentativa';
$lang['tasks']['statuses']['DELEGATED']= 'Delegado';
$lang['tasks']['statuses']['COMPLETED']= 'Completo';
$lang['tasks']['statuses']['IN-PROCESS']= 'En curso';

$lang['tasks']['scheduled_call']='Llamada agendada para %s';
$lang['tasks']['call']='Llamada';

$lang['tasks']['import_success']='%s tareas fueron importadas';

$lang['tasks']['dueAtdate']='Vence en %s';

$lang['tasks']['list']='Lista de tareas';
$lang['tasks']['tasklistChanged']="* Lista de tareas cambió de '%s' a '%s'";
$lang['tasks']['statusChanged']="* Estado cambió de '%s' a '%s'";

?>
