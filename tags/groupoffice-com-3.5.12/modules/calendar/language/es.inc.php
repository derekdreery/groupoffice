<?php
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('calendar'));
$lang['calendar']['name'] = 'Calendario';
$lang['calendar']['description'] = 'Módulo de calendario; Cada usuario puede agregar, editar o borrar citas. Incluso los usuarios puede ver y modificar (de ser necesario) las citas de otros usuarios';

$lang['link_type'][1]='Cita';

$lang['calendar']['groupView'] = 'Mostrar en grupos';
$lang['calendar']['event']='Evento';
$lang['calendar']['startsAt']='Comenzar el';
$lang['calendar']['endsAt']='Al final';

$lang['calendar']['exceptionNoCalendarID'] = 'ERROR: No calendario ID!';
$lang['calendar']['appointment'] = 'Cita: ';
$lang['calendar']['allTogether'] = 'Todo juntos';

$lang['calendar']['location']='Lugar';

$lang['calendar']['invited']='Usted está invitado al siguiente evento';
$lang['calendar']['acccept_question']='¿Aceptar este evento?';

$lang['calendar']['accept']='Aceptar';
$lang['calendar']['decline']='Rechazar';

$lang['calendar']['bad_event']='El evento ya no existe';

$lang['calendar']['subject']='Asunto';
$lang['calendar']['status']='Estado';



$lang['calendar']['statuses']['NEEDS-ACTION'] = 'Debe intervenir';
$lang['calendar']['statuses']['ACCEPTED'] = 'Aceptada';
$lang['calendar']['statuses']['DECLINED'] = 'Rechazada';
$lang['calendar']['statuses']['TENTATIVE'] = 'Tentativa';
$lang['calendar']['statuses']['DELEGATED'] = 'Delegado';
$lang['calendar']['statuses']['COMPLETED'] = 'Completo';
$lang['calendar']['statuses']['IN-PROCESS'] = 'Transformación';


$lang['calendar']['accept_mail_subject'] = 'Invitación para \'%s\' aceptada';
$lang['calendar']['accept_mail_body'] = '%s ha aceptado su invitación a:';

$lang['calendar']['decline_mail_subject'] = 'Invitación para \'%s\' rechazada';
$lang['calendar']['decline_mail_body'] = '%s ha rechazado su invitación a:';

$lang['calendar']['and']='y';

$lang['calendar']['repeats'] = 'Repetir cada %s';
$lang['calendar']['repeats_at'] = 'Repetir cada %s el %s';//eg. Repetir cada mes el primer lunes
$lang['calendar']['repeats_at_not_every'] = 'Repetir cada vez %s %s para %s';//eg. el lunes repetido cada 2 semanas
$lang['calendar']['until']='hasta'; 

$lang['calendar']['not_invited']='Usted no ha sido invitado a este evento. Puede que necesite acceder con un usuario diferente.';


$lang['calendar']['accept_title']='Aceptado';
$lang['calendar']['accept_confirm']='El propietario será notificado de que ha aceptado el evento';

$lang['calendar']['decline_title']='Rechazado';
$lang['calendar']['decline_confirm']='El propietario será notificado de que ha rechazado el evento';

$lang['calendar']['cumulative']='Regla de repetición no válida. La próxima recurrencia no puede empezar antes de que haya terminado la anterior.';
$lang['calendar']['already_accepted']='Usted ya aceptó este evento.';
$lang['calendar']['private']='Privado';

$lang['calendar']['import_success']='%s eventos fueron importados';

$lang['calendar']['printTimeFormat']='Desde %s hasta %s';
$lang['calendar']['printLocationFormat']=' en lugar "%s"';
$lang['calendar']['printPage']='Página %s de %s';
$lang['calendar']['printList']='Listado de citas';

$lang['calendar']['printAllDaySingle']='Todo el día';
$lang['calendar']['printAllDayMultiple']='Todo el día desde %s hasta %s';

$lang['calendar']['calendars']='Calendarios';

$lang['calendar']['resource_mail_subject']='Recurso \'%s\' reservado para \'%s\' el \'%s\'';//%s is resource name, %s is event name, %s is start date
$lang['calendar']['resource_mail_body']='%s ha reservado el recurso \'%s\'. Ud. es el administrador de este recurso. Por favor abra la reserva para aprobar o denegar el pedido.'; //First %s is the name of the person who created the event. Second is the calendar name

$lang['calendar']['resource_modified_mail_subject']='Recurso \'%s\' reservado para \'%s\' el \'%s\' fue modificado';//%s is resource name, %s is event name, %s is start date

