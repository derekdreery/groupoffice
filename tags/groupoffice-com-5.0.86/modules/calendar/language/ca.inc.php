<?php
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('calendar'));
$lang['calendar']['name'] = 'Calendari';
$lang['calendar']['description'] = 'Mòdul de calendari; Cada usuari pot afegir, editar o esborrar cites. Inclús els usuaris poden veure i modificar (en cas de ser necessari) les cites d\'altres usuaris';

$lang['link_type'][1]='Cita';

$lang['calendar']['groupView'] = 'Mostrar en grups';
$lang['calendar']['event']='Esdeveniment';
$lang['calendar']['startsAt']='Començar el';
$lang['calendar']['endsAt']='Al final';

$lang['calendar']['exceptionNoCalendarID'] = 'ERROR: Sense ID de calendari!';
$lang['calendar']['appointment'] = 'Cita: ';
$lang['calendar']['allTogether'] = 'Tot junt';

$lang['calendar']['location']='Lloc';

$lang['calendar']['invited']='Esteu convidats al següent esdeveniment';
$lang['calendar']['acccept_question']='Acceptar aquest esdeveniment?';

$lang['calendar']['accept']='Acceptar';
$lang['calendar']['decline']='Rebutjar';

$lang['calendar']['bad_event']='L\'esdeveniment ja no existeix';

$lang['calendar']['subject']='Assumpte';
$lang['calendar']['status']='Estat';



$lang['calendar']['statuses']['NEEDS-ACTION'] = 'Cal intervindre';
$lang['calendar']['statuses']['ACCEPTED'] = 'Acceptada';
$lang['calendar']['statuses']['DECLINED'] = 'Rebutjada';
$lang['calendar']['statuses']['TENTATIVE'] = 'Temptativa';
$lang['calendar']['statuses']['DELEGATED'] = 'Delegat';
$lang['calendar']['statuses']['COMPLETED'] = 'Complert';
$lang['calendar']['statuses']['IN-PROCESS'] = 'Transformació';


$lang['calendar']['accept_mail_subject'] = 'Invitació per \'%s\' acceptada';
$lang['calendar']['accept_mail_body'] = '%s ha acceptatla vostra invitació a:';

$lang['calendar']['decline_mail_subject'] = 'Invitació per \'%s\' rebutjada';
$lang['calendar']['decline_mail_body'] = '%s ha rebutjat la vostra invitació a:';

$lang['calendar']['and']='i';

$lang['calendar']['repeats'] = 'Repetir cada %s';
$lang['calendar']['repeats_at'] = 'Repetir cada %s el %s';//eg. Repetir cada mes el primer lunes
$lang['calendar']['repeats_at_not_every'] = 'Repetir cada cop %s %s per %s';//eg. el lunes repetido cada 2 semanas
$lang['calendar']['until']='fins'; 

$lang['calendar']['not_invited']='No heu estat convidats a aquest esdeveniment. És possible que necessiteu accedir amb un usuari diferent.';


$lang['calendar']['accept_title']='Acceptat';
$lang['calendar']['accept_confirm']='El propietari serà notificat conforme heu acceptat l\'esdeveniment';

$lang['calendar']['decline_title']='Rebutjat';
$lang['calendar']['decline_confirm']='El propietari serà notificat conforme heu rebutjat l\'esdeveniment';

$lang['calendar']['cumulative']='Regla de repetició no vàlida. La propera recurrència no pot començar abans que hagi finalitzat l\'anterior.';
$lang['calendar']['already_accepted']='Ja heu acceptat aquest esdeveniment.';
$lang['calendar']['private']='Privat';

$lang['calendar']['import_success']='%s esdeveniments han estat importats';

$lang['calendar']['printTimeFormat']='Des de %s fins %s';
$lang['calendar']['printLocationFormat']=' en lloc "%s"';
$lang['calendar']['printPage']='Pàgina %s de %s';
$lang['calendar']['printList']='Llistat de cites';

$lang['calendar']['printAllDaySingle']='Tot el dia';
$lang['calendar']['printAllDayMultiple']='Tot el dia des de %s fins %s';

$lang['calendar']['calendars']='Calendaris';

$lang['calendar']['resource_mail_subject']='Recurs \'%s\' reservat per \'%s\' el \'%s\'';//%s is resource name, %s is event name, %s is start date
$lang['calendar']['resource_mail_body']='%s ha reservat el recurs \'%s\'. Sou l\'administrador d\'aquest recurs. Si us plau, obriu la reserva per aprovar o denegar la sol·licitud.'; //First %s is the name of the person who created the event. Second is the calendar name

$lang['calendar']['resource_modified_mail_subject']='Recurs \'%s\' reservat per \'%s\' el \'%s\' fou modificat';//%s is resource name, %s is event name, %s is start date

$lang['calendar']['unauthorized_participants_write']='No teniu permisos suficients com per afegir esdeveniments en els calendaris de: <br /><br />{NAMES}<br /><br />Envieu una invitació per que ells l\'acceptin.';

$lang['calendar']['resource_modified_mail_body']='\'%s\' ha modificat el recurs \'%s\'. Sou l\'administrador d\'aquest recurs. Si us plau, obriu la reserva per aprovar o rebutjar la sol·licitud.'; //First %s is the name of the person who created the event. Second is the calendar name
$lang['calendar']['your_resource_modified_mail_subject']='La vostra reserva del recurs \'%s\' pel \'%s\' amb l\'estat \'%s\' està modificada'; //is resource name, %s is event name, %s is start date
$lang['calendar']['your_resource_modified_mail_body']='%s ha modificat la vostra reserva del recurs \'%s\'.';
$lang['calendar']['your_resource_accepted_mail_subject']='La vostra reserva del recurs \'%s\' pel \'%s\' fou acceptada.';//%s is resource name, %s is start date
$lang['calendar']['your_resource_accepted_mail_body']='%s ha acceptat la vostra reserva pel recurs \'%s\'.'; //First %s is the name of the person who created the event. Second is the calendar name
$lang['calendar']['your_resource_declined_mail_subject']='La vostra reserva del recurs \'%s\' pel \'%s\' ha estat rebutjada';//%s is resource name, %s is start date
$lang['calendar']['your_resource_declined_mail_body']='%s ha rebutjat la vostra reserva del recurs \'%s\'.'; //First %s is the name of the person who created the event. Second is the calendar name
$lang['calendar']['birthday_name']='Aniversari: {NAME}';
$lang['calendar']['birthday_desc']='{NAME} compleix {AGE} hoy';
$lang['calendar']['month_times'][1]='el primer';
$lang['calendar']['month_times'][2]='el segon';
$lang['calendar']['month_times'][3]='el tercer';
$lang['calendar']['month_times'][4]='el quart';
$lang['calendar']['month_times'][5]='el cinquè';
$lang['calendar']['open_resource']='Recurs obert';
$lang['calendar']['noCalSelected']='No s\'ha sel·leccionat cap calendari per aquesta vista. Sel·leccioneu almenys un calendari en el menú d\'Administració';

$lang['calendar']['statuses']['CONFIRMED']= 'Confirmat';
$lang['calendar']['repeats_not_every']= 'Repetir cada %s %s';
$lang['calendar']['rightClickToCopy']='Clic amb el botó dret per copiar l\'adreça de l\'enllaç';
$lang['calendar']['invitation']='Invitació';
$lang['calendar']['invitation_update']='Invitació actualitzada';
$lang['calendar']['cancellation']='Cancel·lació';
$lang['calendar']['non_selected']= 'en un calendari no seleccionat';
$lang['calendar']['linkIfCalendarNotSupported']='Utilitzar els enllaços de sota només si el vostre client de correu no suporta funcions de calendari.';

$lang['calendar']['statuses']['CONFIRMED']= 'Confirmat';
$lang['calendar']['repeats_not_every']= 'Repetir cada %s %s';
$lang['calendar']['rightClickToCopy']='Clic amb el botó dret per copiar la ubicació de l\'enllaç';
$lang['calendar']['invitation']='Invitació';
$lang['calendar']['invitation_update']='Invitació actualitzada';
$lang['calendar']['cancellation']='Cancel·lació';
$lang['calendar']['non_selected']= 'en calendari no seleccionat';
$lang['calendar']['linkIfCalendarNotSupported']='Utilitzar els enllaços de sota només si el vostre client de mail no suporta funcions de calendari.';
?>