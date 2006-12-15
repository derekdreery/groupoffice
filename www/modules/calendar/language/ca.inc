<?php
require_once($GO_LANGUAGE->get_fallback_language_file('calendar'));

$sc_title = "Títol";
$sc_description = "Descripció";
$sc_participants = "Convidats";

$sc_location = "Lloc";

$month_times[0] = "Primer";
$month_times[1] = "Segón";
$month_times[2] = "Tercer";
$month_times[3] = "Quart";

$sc_new_app = "Nova cita";
$sc_calendars = "Calendaris";
$sc_invited = 'Heu estat convidat a unir-vos a la cita descrita a sota.';

$sc_types1[0] = 'No repeteixis';
$sc_types1[1] = 'dies';
$sc_types1[2] = 'setmanes';
$sc_types1[3] = 'mesos per data';
$sc_types1[4] = 'mesos per dia';
$sc_types1[5] = 'anys';


$sc_types[0] = 'Un cop';
$sc_types[1] = 'Diàriament';
$sc_types[2] = 'Setmanalment';
$sc_types[3] = 'Mensualment per data';
$sc_types[4] = 'Mensualment per dia';
$sc_types[5] = 'Anualment';


$sc_start_at = 'Comença el';
$sc_end_at = 'Acaba el';
$sc_start_time = 'Hora d\'inici';
$sc_end_time = 'Hora final';

$sc_at_days = 'Els dies';
$sc_of_month = 'del mes..';

$sc_cycle_start = 'El cicle comença el';
$sc_cycle_end = 'Repeteix fins';
$sc_accept_question = 'Voleu acceptar i programar aquesta cita?';

$sc_accept = 'accepta';
$sc_decline = 'rebutja';

$sc_notime = 'L\'hora no és aplicable';
$sc_noend = 'Sense data d\'acabament.';

$sc_never_happens = 'Entrada incorrecte! Aquesta cita mai succeeirà.';
$sc_start_later = 'Entrada incorrecte!! L\'hora d\'inici és superior a l\'hora d\'acabament.';

$sc_add_self = 'Entrada manual';

$sc_select_calendar = 'Seleccioneu el calendari on programar aquesta cita';
$sc_accept_confirm = 'La cita ha estat acceptada i programada.';
$sc_no_calendars = 'No s\'ha trobat cap calendari vàlid per programar aquesta cita.';
$sc_bad_event = 'No s\'ha trobat cap cita vàlida. Probablement està suprimida.';
$sc_accept_title = 'Acepta la cita';

$sc_decline_confirm = 'La cita està rebutjada.';
$sc_decline_title = 'Rebutja la cita';

$sc_status = 'Estat';
$sc_not_responded = 'No respós';
$sc_accepted = 'Acceptat';
$sc_declined = 'Rebutjat';
$sc_no_participants = 'Sense participants';

$sc_person = 'Persona';
$sc_owner = 'Propietari';
$sc_type = 'Tipus';
$sc_subscribed = 'Subscrit';

$sc_subscribe = 'Subscriu';
$sc_new_calendar = 'Nou calendari';
$sc_no_calendar = 'No s\'ha trobat cap calendari. Si us plau, creeu-ne un ara o deixeu que Group-Office us en crei un.';
$sc_day_view = 'Vista&nbsp;diària';
$sc_week_view = 'Vista&nbsp;setmanal';
$sc_list_view = 'Vista&nbsp;en&nbsp;llista';
$sc_put_in = 'Posa-hi';
$sc_inherit_permissions ='Hereta els permisos de';
$sc_no_inherit = 'No heretis';

$sc_select_calendar_please = 'Seleccioneu al menys un calendari si us plau.';
$sc_events = 'Cites';

$sc_from_calendar = 'del calendari';
$sc_enitrely = 'Completament';
$sc_delete_event = 'Suprimeix cita';
$sc_delete_pre = 'Voleu suprimir la cita';
$sc_delete_suf = 'completament o només d\'aquest calendari?';

$sc_refresh = 'Refresca';
$sc_reminder = 'Recordatori';
$sc_mins = 'minuts';
$sc_hour = 'hora';
$sc_hours = 'hores';
$sc_day = 'dia';
$sc_days = 'dies';
$sc_week = 'setmana';
$sc_weeks = 'setmanes';
$sc_reminder_set_to_early = "El recordatori no pot ser posat per aparèixer abans de l'anterior ocurrència. Si us plau, escurceu l'hora del recordatori o allargueu la periodicitat de la repetició.";
$sc_cycle_start_later = 'La data d\'inici del cicle és superior o igual a la data d\'acabament.';


$cmdGenerate = 'Genera';
$sc_preferences = 'Preferències';
$sc_holidays = 'Vacances';

$strNoHolidays = 'No hi ha vacances a la base de dades';
$strUnknown = 'Desconegut';
$strYear = 'Any';
$strDeleteHolidaysPrefix = 'Esteu segurs de suprimir tots els registres de vacances de l\'any ';
$strDeleteHolidaysSuffix = '?';
$strReplaceHolidays = 'Esteu segurs de sobreescriure els actuals registres de vacances?';
$sc_holidays_text1 = 'Si us plau, feu clic a \''.$cmdGenerate.'\' per calcular els registres de vacances de l\'any seleccionat.';
$sc_holidays_text2 = 'Si us plau, feu clic a \''.$GLOBALS['cmdApply'].'\' per desar tots els registres calculats a la base de dades.';

//translation required below this
$sc_client = 'Client';
$sc_no_contact = 'Cap';

$sc_background = 'Color de fons';

$sc_colors['red'] = 'Vermell';
$sc_colors['green'] = 'Verd';
$sc_colors['blue'] = 'Blau';
$sc_colors['pink'] = 'Rosa';
$sc_colors['orange'] = 'Taronja';
$sc_colors['brown'] = 'Marró';
$sc_colors['yellow'] = 'Groc';
$sc_colors['grey'] = 'Gris';
$sc_colors['purple'] = 'Púrpura';
$sc_colors['white']= 'Blanc';
$sc_status = 'estat';

$sc_show_hours = 'mostra hores des de';
$sc_to = 'fins';

$sc_today = 'Avui';

$sc_recur_every = 'Repeteix cada';
$sc_months = 'mesos';

$sc_private_event = 'Cita privada';
$sc_calendar_exists = 'Ja existeix un calendari amb aquest nom';
//translate
$sc_month = 'Mes';
$sc_view = 'Vista';
$sc_calendar = 'Calendari';
$sc_default = 'Per defecte';
$sc_month_view = 'Vista&nbsp;mensual';

//text changed, needs new translation
$sc_holidays_no_file_prefix = 'Les vacances no poden ser generades, perque el fitxer que conté la informació de vacances l\'idioma ';
$sc_holidays_no_file_suffix = ' està buit o no existeix.';

$sc_no_events = 'No s\'ha trobat cap cita';

//2.03
$cal_export = 'Exporta';
$cal_import = 'Importa';
$cal_please_wait = 'Espereu si us plau...';
$cal_import_text = 'Seleccioneu un fitxer iCal (*.ics) i feu clic a \'D\'acord\'';
$cal_filename = 'Fitxer';
$cal_import_success = 'Dades importades';

//2.04
$cal_add_todo = 'Nova feina';
$cal_todos = 'Feines';

$cal_hide_completed = 'Amaga les completades';
$cal_show_all = 'Mostra totes les feines';
$cal_show_own = 'Mostra les feines pròpies';
$cal_show_responsible = 'Mostra feines de responsabilitat';

$cal_priority = 'Prioritat';
$cal_priority_values[0] = 'Baixa';
$cal_priority_values[1] = 'Normal';
$cal_priority_values[2] = 'Alta';

$cal_status = 'Estat';
$cal_canceled = 'Cancel·lat';
$cal_completed = 'Completat el';
$cal_percent_completed = '% completat';

$cal_before_task_start = 'Abans que la feina comenci';
$cal_before_task_due = 'Abans que la feina s\'acabi';
$cal_responsible = 'Responsable';
$cal_due_at = 'Pel';
$cal_no_todos= 'No s\'ha trobat cap feina';
$cal_resend_invitation = 'Reenvia la invitació';

//2.05



$cal_view = 'Vista';
$cal_views = 'Vistes';
$sc_view_exists = 'Ja existeix una vista amb aquest nom';
$cal_new_view = 'Nova vista';
$cal_event_calendars = 'Calendari(s)';

//2.06
$cal_view_merged = 'combinat';
$cal_view_emerged = 'sorgit';

//2.07
$cal_display = 'Visualitza';
$sc_new_task = "Nova feina";
$sc_all_day_events = 'tots els dies citats';
$sc_all_day_event = 'tot el dia citat';
$sc_usual_events = 'cites';
$sc_usual_event = 'cita';

//2.08
$sc_recur_section = 'Periodicitat';
$sc_options_section = 'Opcions';
$sc_close = 'Tanca';

$cal_count_single = 'cita';
$cal_count_multiple = 'cites';

$cal_until = 'fins';
$cal_forever = 'per sempre';

//2.09
$cal_merged_overview = 'Resum del combinat';
$cal_own_availability = 'Disponibilitat pròpia';

//2.11
$cal_no_file = 'No s\'ha rebut cap fitxer';
$cms_no_calendars_in_view = 'Aquesta vista no pot ser mostrada perque no hi ha calendaris en aquesta vista. Feu clic a \'Vistes\' per gestionar les vistes.';
$cal_no_reminder = 'Sense recordatori';

//2.13
$cal_refresh_rate = 'Velocitat de refresc';
$sc_seconds = 'segons (Deixeu-ho buit si no hi voleu cap refresc)';
$cal_default_reminder = 'Recordatori per defecte';


//2.14
$cal_event = 'Cita';
$cal_todo = 'Per-fer';
$cal_participants_write = 'Els participants poden modificar la cita';

$cal_everybody_write = 'Tothom pot modificar la cita'; //modified string from 2.05 this became the
$cal_everybody_read = 'Només jo puc modificar la cita';//modified string from 2.05 

$cal_default_permissions = 'Permisos per defecte';
$cal_show_todos = 'Mostra feines al calendari';

$cal_edit_series_or_single = 'Edita només aquest cas o la sèrie sencera?';
$cal_single = 'Cas';
$cal_series = 'Sèrie sencera';

$cal_legend = 'Llegenda';
$cal_multiple_calendars = 'Múltiples calendaris';

$cal_cumulative = 'Entrada incorrecte! La cita duraria més temps que l\'interval de repetició.';

$cal_weekview = 'La vista setmanal mostra';
$cal_mo_to_fr = 'de dilluns a divendres';
$cal_mo_to_su = 'de dilluns a diumenge';

$cal_batchcalendars = 'Crea calendaris per tots els usuaris';
$cal_create_all_calendars = 'Crea calendaris per tots els usuaris o concedeix permisos a grups d\'usuaris';

$cal_no_views = 'No s\'ha trobat cap vista';

$cal_my_cals = 'Els meus calendaris';

$cal_event_colors_override = 'Fés servir els colors manualment definits per les cites';
?>
