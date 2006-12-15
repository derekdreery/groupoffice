<?php
require_once($GO_LANGUAGE->get_fallback_language_file('calendar'));

$sc_title = "Título";
$sc_description = "Descripción";
$sc_participants = "Invitados";

$sc_location = "Lugar";

$month_times[0] = "Primero";
$month_times[1] = "Segundo";
$month_times[2] = "Tercero";
$month_times[3] = "Cuarto";

$sc_new_app = "Nueva cita";
$sc_calendars = "Calendarios";
$sc_invited = 'Está invitado a unirse a la cita descrita abajo.';

$sc_types1[0] = 'Sin repetición';
$sc_types1[1] = 'días';
$sc_types1[2] = 'semanas';
$sc_types1[3] = 'meses por fecha';
$sc_types1[4] = 'meses por día';
$sc_types1[5] = 'años';


$sc_types[0] = 'Una vez';
$sc_types[1] = 'Diariamente';
$sc_types[2] = 'Semanalmente';
$sc_types[3] = 'Mensualmente por fecha';
$sc_types[4] = 'Mensualmente por día';
$sc_types[5] = 'Anualmente';


$sc_start_at = 'Comienza el';
$sc_end_at = 'Termina el';
$sc_start_time = 'Hora de inicio';
$sc_end_time = 'Hora de finalización';

$sc_at_days = 'los días';
$sc_of_month = 'del mes.';

$sc_cycle_start = 'El ciclo empieza el';
$sc_cycle_end = 'Repetir hasta';
$sc_accept_question = '¿Desea aceptar y programar esta cita?';

$sc_accept = 'acepto';
$sc_decline = 'rechazo';

$sc_notime = 'La hora no es aplicable';
$sc_noend = 'Sin fecha de finalización.';

$sc_never_happens = '¡Entrada incorrecta! Esta cita nunca ocurrirá.';
$sc_start_later = '¡¡Entrada incorrecta!! La hora de inicio es superior a la hora de finalización.';

$sc_add_self = 'Entrada manual';

$sc_select_calendar = 'Seleccionar el calendario donde programar esta cita';
$sc_accept_confirm = 'La cita ha sido aceptada y programada.';
$sc_no_calendars = 'No se ha encontrado ningún calendario válido para programar esta cita.';
$sc_bad_event = 'No se ha encontrado ninguna cita válida. Probablemente está suprimida.';
$sc_accept_title = 'Aceptar la cita';

$sc_decline_confirm = 'La cita ha sido rechazada.';
$sc_decline_title = 'Rechazar la cita';

$sc_status = 'Estado';
$sc_not_responded = 'No respondido';
$sc_accepted = 'Aceptado';
$sc_declined = 'Rechazado';
$sc_no_participants = 'Sin participantes';

$sc_person = 'Persona';
$sc_owner = 'Propietario';
$sc_type = 'Tipo';
$sc_subscribed = 'Subscrito';

$sc_subscribe = 'Subscribir';
$sc_new_calendar = 'Nuevo calendario';
$sc_no_calendar = 'No se ha encontrado ningún calendario. Por favor, cree uno nuevo ahora o deje que Group-Office lo haga por usted.';
$sc_day_view = 'Vista&nbsp;diaria';
$sc_week_view = 'Vista&nbsp;semanal';
$sc_list_view = 'Vista&nbsp;en&nbsp;lista';
$sc_put_in = 'Poner dentro';
$sc_inherit_permissions ='Heredar los permisos de';
$sc_no_inherit = 'No heredar';

$sc_select_calendar_please = 'Seleccione al menos un calendario por favor.';
$sc_events = 'Citas';

$sc_from_calendar = 'del calendario';
$sc_enitrely = 'Completamente';
$sc_delete_event = 'Elimina la cita';
$sc_delete_pre = 'Desea eliminar la cita';
$sc_delete_suf = 'completamente o sólo de este calendario?';

$sc_refresh = 'Refrescar';
$sc_reminder = 'Recordatorio';
$sc_mins = 'minutos';
$sc_hour = 'hora';
$sc_hours = 'horas';
$sc_day = 'día';
$sc_days = 'días';
$sc_week = 'semana';
$sc_weeks = 'semanas';
$sc_reminder_set_to_early = "El recordatorio no puede ser puesto para aparecer antes de la ocurrencia anterior. Por favor, acorte la hora del recordatorio o alargue la periodicidad de la repetición.";
$sc_cycle_start_later = 'La fecha de inicio del ciclo es superior o igual a la fecha de finalización.';


$cmdGenerate = 'Generar';
$sc_preferences = 'Preferencias';
$sc_holidays = 'Vacaciones';

$strNoHolidays = 'No hay vacaciones en la base de datos';
$strUnknown = 'Desconocido';
$strYear = 'Año';
$strDeleteHolidaysPrefix = '¿Está seguro de eliminar todos los registros de vacaciones del año ';
$strDeleteHolidaysSuffix = '?';
$strReplaceHolidays = '¿Está seguro de sobreescribir los registros actuales de vacaciones?';
$sc_holidays_text1 = 'Por favor, haga clic en \''.$cmdGenerate.'\' para calcular los registros de vacaciones del año seleccionado.';
$sc_holidays_text2 = 'Por favor, haga clic en \''.$GLOBALS['cmdApply'].'\' para guardar todos los registros calculados en la base de datos.';

//translation required below this
$sc_client = 'Cliente';
$sc_no_contact = 'Ninguno';

$sc_background = 'Color de fondo';

$sc_colors['red'] = 'Rojo';
$sc_colors['green'] = 'Verde';
$sc_colors['blue'] = 'Azul';
$sc_colors['pink'] = 'Rosa';
$sc_colors['orange'] = 'Naranja';
$sc_colors['brown'] = 'Marrón';
$sc_colors['yellow'] = 'Amarillo';
$sc_colors['grey'] = 'Gris';
$sc_colors['purple'] = 'Púrpura';
$sc_colors['white']= 'Blanco';
$sc_status = 'estado';

$sc_show_hours = 'Ver horas desde';
$sc_to = 'hasta';

$sc_today = 'Hoy';

$sc_recur_every = 'Repetir cada';
$sc_months = 'meses';

$sc_private_event = 'Cita privada';
$sc_calendar_exists = 'Ya existe un calendario con este nombre';
//translate
$sc_month = 'Mes';
$sc_view = 'Vista';
$sc_calendar = 'Calendario';
$sc_default = 'Por defecto';
$sc_month_view = 'Vista&nbsp;mensual';

//text changed, needs new translation
$sc_holidays_no_file_prefix = 'Las vacaciones no pueden ser generadas, porque el fichero que contiene la información de vacaciones para el idioma ';
$sc_holidays_no_file_suffix = ' está vacío o no existe.';

$sc_no_events = 'No se ha encontrado ninguna cita';

//2.03
$cal_export = 'Exportar';
$cal_import = 'Importar';
$cal_please_wait = 'Espere por favor...';
$cal_import_text = 'Seleccione un fichero iCal (*.ics) y haga click en \'Aceptar\'';
$cal_filename = 'Fichero';
$cal_import_success = 'Datos importados';

//2.04
$cal_add_todo = 'Nueva tarea';
$cal_todos = 'Tareas';

$cal_hide_completed = 'Ocultar las completadas';
$cal_show_all = 'Ver todas las tareas';
$cal_show_own = 'Ver las tareas propias';
$cal_show_responsible = 'Ver tareas de responsabilidad';

$cal_priority = 'Prioridad';
$cal_priority_values[0] = 'Baja';
$cal_priority_values[1] = 'Normal';
$cal_priority_values[2] = 'Alta';

$cal_status = 'Estado';
$cal_canceled = 'Cancelado';
$cal_completed = 'Completado el';
$cal_percent_completed = '% completado';

$cal_before_task_start = 'Antes que la tarea empiece';
$cal_before_task_due = 'Antes que la tarea haya terminado';
$cal_responsible = 'Responsable';
$cal_due_at = 'Para el';
$cal_no_todos= 'No se ha encontrado ninguna tarea';
$cal_resend_invitation = 'Reenviar la invitación';

//2.05



$cal_view = 'Vista';
$cal_views = 'Vistas';
$sc_view_exists = 'Ya existe una vista con este nombre';
$cal_new_view = 'Nueva vista';
$cal_event_calendars = 'Calendario(s)';

//2.06
$cal_view_merged = 'combinado';
$cal_view_emerged = 'surgido';

//2.07
$cal_display = 'Ver';
$sc_new_task = "Nueva tarea";
$sc_all_day_events = 'todos los días citados';
$sc_all_day_event = 'todo el día citado';
$sc_usual_events = 'citas';
$sc_usual_event = 'cita';

//2.08
$sc_recur_section = 'Periodicidad';
$sc_options_section = 'Opciones';
$sc_close = 'Cerrar';

$cal_count_single = 'cita';
$cal_count_multiple = 'citas';

$cal_until = 'hasta';
$cal_forever = 'para siempre';

//2.09
$cal_merged_overview = 'Resúmen del combinado';
$cal_own_availability = 'Disponibilidad propia';

//2.11
$cal_no_file = 'No se ha recibido ningún fichero';
$cms_no_calendars_in_view = 'Esta vista no puede ser mostrada porque no hay calendarios en esta vista. Haga click en \'Views\' para gestionar las vistas.';
$cal_no_reminder = 'Sin recordatorio';

//2.13
$cal_refresh_rate = 'Velocidad de refresco';
$sc_seconds = 'segundos (Dejar en blanco si no quiere refresco)';
$cal_default_reminder = 'Recordatorio por defecto';


//2.14
$cal_event = 'Cita';
$cal_todo = 'Por-hacer';
$cal_participants_write = 'Los participantes pueden modificar la cita';

$cal_everybody_write = 'Todo el mundo puede modificar la cita'; //modified string from 2.05 this became the
$cal_everybody_read = 'Sólo yo puedo modificar la cita';//modified string from 2.05 

$cal_default_permissions = 'Permisos por defecto';
$cal_show_todos = 'Ver tareas en el calendario';

$cal_edit_series_or_single = '¿Editar sólo este caso o la serie entera?';
$cal_single = 'Caso';
$cal_series = 'Serie entera';

$cal_legend = 'Leyenda';
$cal_multiple_calendars = 'Múltiples calendarios';

$cal_cumulative = '¡Entrada incorrecta! La cita duraría más tiempo que el intervalo de repetición.';

$cal_weekview = 'La vista semanal muestra';
$cal_mo_to_fr = 'de Lunes a Viernes';
$cal_mo_to_su = 'de Lunes a Domingo';

$cal_batchcalendars = 'Crear calendarios para todos los usuarios';
$cal_create_all_calendars = 'Crear calendarios para todos los usuarios o conceder permisos a grupos de usuarios';

$cal_no_views = 'No se ha encontrado ninguna vista';

$cal_my_cals = 'Mis calendarios';

$cal_event_colors_override = 'Usar los colores manualmente definidos para las citas';
?>
