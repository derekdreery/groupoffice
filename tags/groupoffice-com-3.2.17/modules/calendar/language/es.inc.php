<?php
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('calendar'));
$lang['calendar']['name'] = 'Calendario';
$lang['calendar']['description'] = 'Calendario de módulo, cada usuario puede agregar, editar o eliminar citas. Incluso los nombramientos de otros usuarios se pueden ver y modificar si es necesario.';

$lang['link_type'][1]='Cita';

$lang['calendar']['groupView'] = 'Mostrar en grupos';
$lang['calendar']['event']='Evento';
$lang['calendar']['startsAt']='Comenzar con';
$lang['calendar']['endsAt']='Al final';

$lang['calendar']['exceptionNoCalendarID'] = 'ERROR: No calendario ID!';
$lang['calendar']['appointment'] = 'Cita: ';
$lang['calendar']['allTogether'] = 'Juntos';

$lang['calendar']['location']='Place';

$lang['calendar']['invited']='Usted está invitado a la siguiente evento';
$lang['calendar']['acccept_question']='Aceptar este evento?';

$lang['calendar']['accept']='Aceptar';
$lang['calendar']['decline']='Rechazar';

$lang['calendar']['bad_event']='El caso de que no hay más';

$lang['calendar']['subject']='Objeto';
$lang['calendar']['status']='Estado';



$lang['calendar']['statuses']['NEEDS-ACTION'] = 'Debe intervenir';
$lang['calendar']['statuses']['ACCEPTED'] = 'Aceptada';
$lang['calendar']['statuses']['DECLINED'] = 'Rechazada';
$lang['calendar']['statuses']['TENTATIVE'] = 'Provisional';
$lang['calendar']['statuses']['DELEGATED'] = 'Delegado';
$lang['calendar']['statuses']['COMPLETED'] = 'Completo';
$lang['calendar']['statuses']['IN-PROCESS'] = 'Transformación';


$lang['calendar']['accept_mail_subject'] = 'Convocatoria para \'%s\' aceptada';
$lang['calendar']['accept_mail_body'] = '%s ha aceptado su invitación a:';

$lang['calendar']['decline_mail_subject'] = 'Convocatoria para \'%s\' negado';
$lang['calendar']['decline_mail_body'] = '%s ha rechazado su invitación a:';

$lang['calendar']['location']='Place';
$lang['calendar']['and']='e';

$lang['calendar']['repeats'] = 'Repetir cada %s';
$lang['calendar']['repeats_at'] = 'Repetir cada vez %s para %s';//eg. Repetir cada primer lunes del mes
$lang['calendar']['repeats_at_not_every'] = 'Repetir cada vez %s %s para %s';//eg. el lunes repetido cada 2 semanas
$lang['calendar']['until']='hasta'; 

$lang['calendar']['not_invited']='Usted no se ha invitado a este evento. Puede que necesite el acceso con un usuario diferente.';


$lang['calendar']['accept_title']='Aceptado';
$lang['calendar']['accept_confirm']='El propietario será notificado de que ha aceptado Evento';

$lang['calendar']['decline_title']='Rechazada';
$lang['calendar']['decline_confirm']='El propietario será notificado de que se han negado Evento';

$lang['calendar']['cumulative']='Imperio de la repetición no válida. El próximo evento no puede comenzar antes de que el anterior se ha completado.';
$lang['calendar']['already_accepted']='Usted ya aceptó este evento.';
$lang['calendar']['private']='Privado';
