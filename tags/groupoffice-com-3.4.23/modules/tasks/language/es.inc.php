<?php
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('tasks'));

$lang['tasks']['name']='Actividad';
$lang['tasks']['description']='Por favor, introduzca una descripción';

$lang['link_type'][12]=$lang['tasks']['task']='Actividad';
$lang['tasks']['status']='Estado';

$lang['tasks']['statuses']['NEEDS-ACTION']= 'Solicitud de Acción';
$lang['tasks']['statuses']['ACCEPTED']= 'Aceptada';
$lang['tasks']['statuses']['DECLINED']= 'Rechazada';
$lang['tasks']['statuses']['TENTATIVE']= 'Intento';
$lang['tasks']['statuses']['DELEGATED']= 'Delegado';
$lang['tasks']['statuses']['COMPLETED']= 'Completo';
$lang['tasks']['statuses']['IN-PROCESS']= 'En curso';

$lang['tasks']['scheduled_call']='Pianificato alle %s';
$lang['tasks']['call']='Chiamata';
$lang['tasks']['scheduled_call']='Prevista en la palabra %s';
$lang['tasks']['call']='Llamar';
?>