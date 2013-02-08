<?php


$l['name'] = 'Calendari';
$l['description'] = 'Mòdul de calendari; Cada usuari pot afegir, editar o esborrar cites. Inclús els usuaris poden veure i modificar (en cas de ser necessari) les cites d\'altres usuaris';
$l['groupView'] = 'Mostrar en grups';
$l['event']='Esdeveniment';
$l['exceptionNoCalendarID'] = 'ERROR: Sense ID de calendari!';
$l['allTogether'] = 'Tot junt';
$l['invited']='Esteu convidats al següent esdeveniment';
$l['acccept_question']='Acceptar aquest esdeveniment?';
$l['accept']='Acceptar';
$l['decline']='Rebutjar';
$l['bad_event']='L\'esdeveniment ja no existeix';
$l['subject']='Assumpte';
$l['statuses']['NEEDS-ACTION'] = 'Cal intervindre';
$l['statuses']['ACCEPTED'] = 'Acceptada';
$l['statuses']['DECLINED'] = 'Rebutjada';
$l['statuses']['TENTATIVE'] = 'Temptativa';
$l['statuses']['DELEGATED'] = 'Delegat';
$l['statuses']['COMPLETED'] = 'Complert';
$l['statuses']['IN-PROCESS'] = 'Transformació';
$l['accept_mail_subject'] = 'Invitació per \'%s\' acceptada';
$l['accept_mail_body'] = '%s ha acceptatla vostra invitació a:';
$l['decline_mail_subject'] = 'Invitació per \'%s\' rebutjada';
$l['decline_mail_body'] = '%s ha rebutjat la vostra invitació a:';
$l['and']='i';
$l['repeats'] = 'Repetir cada %s';
$l['repeats_at'] = 'Repetir cada %s el %s';//eg. Repetir cada mes el primer lunes;
$l['repeats_at_not_every'] = 'Repetir cada cop %s %s per %s';//eg. el lunes repetido cada 2 semanas;
$l['until']='fins'; ;
$l['not_invited']='No heu estat convidats a aquest esdeveniment. És possible que necessiteu accedir amb un usuari diferent.';
$l['accept_title']='Acceptat';
$l['accept_confirm']='El propietari serà notificat conforme heu acceptat l\'esdeveniment';
$l['decline_title']='Rebutjat';
$l['decline_confirm']='El propietari serà notificat conforme heu rebutjat l\'esdeveniment';
$l['cumulative']='Regla de repetició no vàlida. La propera recurrència no pot començar abans que hagi finalitzat l\'anterior.';
$l['already_accepted']='Ja heu acceptat aquest esdeveniment.';
$l['private']='Privat';
$l['import_success']='%s esdeveniments han estat importats';
$l['printTimeFormat']='Des de %s fins %s';
$l['printLocationFormat']=' en lloc "%s"';
$l['printPage']='Pàgina %s de %s';
$l['printList']='Llistat de cites';
$l['printAllDaySingle']='Tot el dia';
$l['printAllDayMultiple']='Tot el dia des de %s fins %s';
$l['resource_mail_subject']='Recurs \'%s\' reservat per \'%s\' el \'%s\'';//%s is resource name, %s is event name, %s is start date;
$l['resource_mail_body']='%s ha reservat el recurs \'%s\'. Sou l\'administrador d\'aquest recurs. Si us plau, obriu la reserva per aprovar o denegar la sol·licitud.'; //First %s is the name of the person who created the event. Second is the calendar name;
$l['resource_modified_mail_subject']='Recurs \'%s\' reservat per \'%s\' el \'%s\' fou modificat';//%s is resource name, %s is event name, %s is start date;
$l['unauthorized_participants_write']='No teniu permisos suficients com per afegir esdeveniments en els calendaris de: <br /><br />{NAMES}<br /><br />Envieu una invitació per que ells l\'acceptin.';
$l['resource_modified_mail_body']='\'%s\' ha modificat el recurs \'%s\'. Sou l\'administrador d\'aquest recurs. Si us plau, obriu la reserva per aprovar o rebutjar la sol·licitud.'; //First %s is the name of the person who created the event. Second is the calendar name;
$l['your_resource_modified_mail_subject']='La vostra reserva del recurs \'%s\' pel \'%s\' amb l\'estat \'%s\' està modificada'; //is resource name, %s is event name, %s is start date;
$l['your_resource_modified_mail_body']='%s ha modificat la vostra reserva del recurs \'%s\'.';
$l['your_resource_accepted_mail_subject']='La vostra reserva del recurs \'%s\' pel \'%s\' fou acceptada.';//%s is resource name, %s is start date;
$l['your_resource_accepted_mail_body']='%s ha acceptat la vostra reserva pel recurs \'%s\'.'; //First %s is the name of the person who created the event. Second is the calendar name;
$l['your_resource_declined_mail_subject']='La vostra reserva del recurs \'%s\' pel \'%s\' ha estat rebutjada';//%s is resource name, %s is start date;
$l['your_resource_declined_mail_body']='%s ha rebutjat la vostra reserva del recurs \'%s\'.'; //First %s is the name of the person who created the event. Second is the calendar name;
$l['birthday_name']='Aniversari: {NAME}';
$l['birthday_desc']='{NAME} compleix {AGE} hoy';
$l['month_times'][1]='el primer';
$l['month_times'][2]='el segon';
$l['month_times'][3]='el tercer';
$l['month_times'][4]='el quart';
$l['month_times'][5]='el cinquè';
$l['open_resource']='Recurs obert';
$l['noCalSelected']='No s\'ha sel·leccionat cap calendari per aquesta vista. Sel·leccioneu almenys un calendari en el menú d\'Administració';
$l['statuses']['CONFIRMED']= 'Confirmat';
$l['statuses']['CONFIRMED']= 'Confirmat';
$l['repeats_not_every']= 'Repetir cada %s %s';
$l['rightClickToCopy']='Clic amb el botó dret per copiar la ubicació de l\'enllaç';
$l['invitation']='Invitació';
$l['invitation_update']='Invitació actualitzada';
$l['cancellation']='Cancel·lació';
$l['non_selected']= 'en calendari no seleccionat';
$l['linkIfCalendarNotSupported']='Utilitzar els enllaços de sota només si el vostre client de mail no suporta funcions de calendari.';
$l["addressbook"]='Contactes';
$l["appointment"]= 'Cita';
$l["appointments"]= 'Cites';
$l["recurrence"]= 'Repetició';
$l["options"]= 'Opcions';
$l["repeatForever"]= 'Repetir per sempre';
$l["repeatEvery"]= 'Repetir cada';
$l["repeatUntil"]= 'Repetir fins';
$l["busy"]= 'Mostrar com compromés';
$l["allDay"]= 'Tot el dia';
$l["navigation"]= 'Navegació';
$l["oneDay"]= '1 Dia';
$l["fiveDays"]= '5 Dies';
$l["sevenDays"]= '7 Dies';
$l["month"]= 'Mes';
$l["recurringEvent"]= 'Esdeveniment repetitiu';
$l["deleteRecurringEvent"]= 'Voleu esborrar només aquest o tota la sèrie de l\'esdeveniment?';
$l["singleOccurence"]= 'Només aquest';
$l["entireSeries"]= 'Tota la sèrie';
$l["calendar"]= 'Calendari';
$l["calendars"]= 'Calendaris';
$l["views"]= 'Vistes';
$l["administration"]= 'Administració';
$l["needsAction"]= 'Han d\'intervindre';
$l["accepted"]= 'Acceptat';
$l["declined"]= 'Rebutjat';
$l["tentative"]= 'Probable';
$l["delegated"]= 'Delegat';
$l["noRecurrence"]= 'No es repeteix';
$l["notRespondedYet"]= 'Encara no ha respost';
$l["days"]= 'Dies';
$l["weeks"]= 'Setmanes';
$l["monthsByDate"]= 'Mesos a partir de la data';
$l["monthsByDay"]= 'Mesos a partir del dia';
$l["years"]= 'Anys';
$l["atDays"]= 'Diària';
$l["noReminder"]= 'No recordatori';
$l["reminder"]='Recordatori';
$l["participants"]= 'Participants';
$l["checkAvailability"]= 'Comprovar disponibilitat';
$l["sendInvitation"]= 'Enviar invitació';
$l["emailSendingNotConfigured"]= 'L\'enviament d\'e-mail no està configurat';
$l["privateEvent"]= 'Privat';
$l["noInformationAvailable"]= 'No es disposa d\'informació';
$l["noParticipantsToDisplay"]= 'No hi ha participants per mostrar';
$l["previousDay"]= 'Dia anterior';
$l["nextDay"]= 'Dia següent';
$l["noAppointmentsToDisplay"]= 'No hi ha cites per veure';
$l["selectCalendar"]= 'Seleccioneu un calendari';
$l["selectCalendarForAppointment"]= 'Seleccioneu el calendari on incloure aquest esdeveniment';
$l["closeWindow"]= 'La cita ha estat acceptada i agendada. Podeu tancar aquesta finestra.';
$l["list"]='Llistat';
$l["editRecurringEvent"]='Voleu editar aquest esdeveniment o tota la sèrie?';
$l["selectIcalendarFile"]='Seleccioneu un arxiu d\'icalendar (*.ics)';
$l["visibleCalendars"]='Calendaris visibles';
$l["visible"]='Visible';
$l["importToCalendar"]='Afegir cita al calendari';
$l["default_calendar"]='Calendari per defecte';
$l["status"]='Estat';
$l["group"]='Grup';
$l["no_status"]='Nou';
$l["no_custom_fields"]='No hi ha opcions extra.';
$l["show_bdays"]='Mostrar aniversaris de llistat de contactes';
$l["show_tasks"]='Mostrar activitats de llistat d\'activitats';
$l["myCalendar"]='El meu calendari';
$l["merge"]='Unir';
$l["ownColor"]='Cada calendari amb el seu color';
$l["location"]='lloc';
$l["startsAt"]='Comença el';
$l["endsAt"]='Finalitza el';
$l["eventDefaults"]='Configuració per defecte per les cites';
$l["resource_groups"]='Grups de recursos';
$l["resource_group"]='Grup de recursos';
$l["resources"]='Recursos';
$l["resource"]='Recurs';
$l["calendar_group"]='Calendari grupal';
$l["admins"]='Administradors';
$l["no_group_selected"]='Teniu errors en el formulari. Necessiteu seleccionar un grup per aquest recurs.';
$l["ignoreConflictsTitle"]= 'Ignorar conflicte?';
$l["ignoreConflictsMsg"]= 'Aquest esdeveniment està en conflicte amb un altre esdeveniment en el vostre calendari. Voleu desar-lo de totes maneres?';
$l["resourceConflictTitle"]= 'Conflicte amb el recurs';
$l["resourceConflictMsg"]= 'Un o més recursos d\'aquest esdeveniment ja estan sent utilitzats a la mateixa hora: </br>';
$l["view"]= 'Veure';
$l["calendarsPermissions"]='Permisos de calendaris';
$l["resourcesPermissions"]='Permisos de recursos';
$l["daynames"][0]='Diumenge';
$l["daynames"][1]='Dilluns';
$l["daynames"][2]='Dimarts';
$l["daynames"][3]='Dimecres';
$l["daynames"][4]='Dijous';
$l["daynames"][5]='Divendres';
$l["daynames"][6]='Dissabte';
$l["categories"]='Categories';
$l["category"]='Categoria';
$l["globalCategory"]='Categoria global';
$l["selectCategory"]='Seleccionar categoria';
$l["duration"]='Duració';
$l["move"]='Moure';
$l["showInfo"]='Detalls';
$l["copyEvent"]='Copiar esdeveniment';
$l["moveEvent"]='Moure esdeveniment';
$l["eventInfo"]='Detalls de l\'esdeveniment';
$l["isOrganizer"]='Organitzador';
$l["sendInvitationInitial"]='Voleu enviar invitacions a l\'esdeveniment als participants?';
$l["sendInvitationUpdate"]='Voleu enviar la informació actualitzada de l\'esdeveniment als participants?';
$l["sendCancellation"]='Voleu enviar un avís de cancel·lació a tots els participants?';
$l["forthcomingAppointments"]='Cites pròximes';
$l["quarterShort"]= 'Q';
