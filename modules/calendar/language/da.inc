<?php
//Uncomment this line in new translations!
require_once($GO_LANGUAGE->get_fallback_language_file('calendar'));

$sc_title = "Titel";
$sc_description = "Beskrivelse";
$sc_participants = "Inviterede";

$sc_location = "Sted";

$month_times[0] = "Første";
$month_times[1] = "Anden";
$month_times[2] = "Tredje";
$month_times[3] = "Fjerde";

$sc_new_app = "Ny hændelse";
$sc_calendars = "Kalendere";
$sc_invited = 'Du er inviteret til at deltage i nedenstående hændelse.';

$sc_types1[0] = 'Ingen gentagelse';
$sc_types1[1] = 'dage';
$sc_types1[2] = 'uger';
$sc_types1[3] = 'måneder efter dato';
$sc_types1[4] = 'måneder efter dag';
$sc_types1[5] = 'år';


$sc_types[0] = 'En gang';
$sc_types[1] = 'Dagligt';
$sc_types[2] = 'Ugenligt';
$sc_types[3] = 'Månedligt efter dato';
$sc_types[4] = 'Månedligt efter dag';
$sc_types[5] = 'Årligt';


$sc_start_at = 'Starter den';
$sc_end_at = 'Slutter den';
$sc_start_time = 'Start tidspunkt';
$sc_end_time = 'Slut tidspunkt';

$sc_at_days = 'På dage';
$sc_of_month = 'i måneden.';

$sc_cycle_start = 'Cyclus starter på';
$sc_cycle_end = 'Gentag indtil';
$sc_accept_question = 'Ønsker du at godkende og planlægge denne hændelse?';

$sc_accept = 'Godkend';
$sc_decline = 'Afslå';

$sc_notime = 'Ignorer tid';
$sc_noend = 'Ingen afslutningsdato.';

$sc_never_happens = 'Fejl i indtastede data! Denne hændelse kan aldrig forekomme.';
$sc_start_later = 'Fejl i indtastede data! Start tidspunktet er efter afslutningstidspunktet.';

$sc_add_self = 'Manuel indtastning';

$sc_select_calendar = 'Vælg kalenderen hændelsen skal forekomme i';
$sc_accept_confirm = 'Hændelsen er godkendt og planlagt.';
$sc_no_calendars = 'Fandt ingen gyldig kalender hændelsen kan planlægges i.';
$sc_bad_event = 'Ingen gyldig hændelse fundet. Hændelsen er formodentlig slettet.';
$sc_accept_title = 'Godkend hændelse';

$sc_decline_confirm = 'Hændelsen er afslået.';
$sc_decline_title = 'Afslå hændelse';

$sc_status = 'Status';
$sc_not_responded = 'Intet svar';
$sc_accepted = 'Accepteret';
$sc_declined = 'Afslået';
$sc_no_participants = 'Ingen deltagere';

$sc_person = 'Person';
$sc_owner = 'Ejer';
$sc_type = 'Type';
$sc_subscribed = 'Tilmeldt';

$sc_subscribe = 'TilmeldSubscribe';
$sc_new_calendar = 'Ny kalender';
$sc_no_calendar = 'Ny kalender fundet. Opret venligst en nu eller lad Group-Office oprette en for dig automatisk.';
$sc_day_view = 'Dag';
$sc_week_view = 'Uge';
$sc_list_view = 'Liste';
$sc_put_in = 'Put hændelse i';
$sc_inherit_permissions ='Arv rettigheder fra';
$sc_no_inherit = 'Arv ikke rettigheder';

$sc_select_calendar_please = 'Vælg venligst mindst én kalender.';
$sc_events = 'Hændelser';

$sc_from_calendar = 'fra kalender';
$sc_enitrely = 'Komplet';
$sc_delete_event = 'Slet hændelseevent';
$sc_delete_pre = 'Ønsker du at slette denne hændelse';
$sc_delete_suf = 'komplet eller kun fra denne kalender?';

$sc_refresh = 'Opdater';
$sc_reminder = 'Reminder';
$sc_mins = 'min.';
$sc_hour = 'time';
$sc_hours = 'timer';
$sc_day = 'dag';
$sc_days = 'dage';
$sc_week = 'uge';
$sc_weeks = 'uger';
$sc_reminder_set_to_early = "Reminderen kan ikke sættes til at blive udført før den forrige. Formindsk enten varigheden eller forøg gentagelsesperioden.";
$sc_cycle_start_later = 'Cyclus startdatoen er senere eller samtidig med slutdatoen.';


$cmdGenerate = 'Generer';
$sc_preferences = 'Indstillinger';
$sc_holidays = 'Helligdage';

$strNoHolidays = 'Ingen helligdage i database';
$strUnknown = 'Ukendt';
$strYear = 'År';
$strDeleteHolidaysPrefix = 'Er du sikker på du ønsker at slette alle helligdage for år  ';
$strDeleteHolidaysSuffix = '?';
$strReplaceHolidays = 'Er du sikker på du ønsker at overskrive alle eksisterende helligdage?';
$sc_holidays_text1 = 'Klik venligst på \''.$cmdGenerate.'\' for at beregne helligdage for det valgte år.';
$sc_holidays_text2 = 'Klik på \''.$cmdApply.'\' for at gemme alle beregnede helligdage i databasen.';

//translation required below this
$sc_client = 'Klient';
$sc_no_contact = 'Ingen';

$sc_background = 'Baggrund';

$sc_colors['red'] = 'Rød';
$sc_colors['green'] = 'Grøn';
$sc_colors['blue'] = 'Blå';
$sc_colors['pink'] = 'Pink';
$sc_colors['orange'] = 'Orange';
$sc_colors['brown'] = 'Brun';
$sc_colors['yellow'] = 'Gul';
$sc_colors['grey'] = 'Grå';
$sc_colors['purple'] = 'Rosa';
$sc_colors['white']= 'Hvid';
$sc_status = 'status';

$sc_show_hours = 'Vis timer fra';
$sc_to = 'til';

$sc_today = 'Idag';

$sc_recur_every = 'Gentag hver';
$sc_months = 'måneder';

$sc_private_event = 'Privat hændelse';
$sc_calendar_exists = 'En kalender ved det navn eksisterer allerede';
//translate
$sc_month = 'Måned';
$sc_view = 'Visning';
$sc_calendar = 'Kalender';
$sc_default = 'Standard';
$sc_month_view = 'Måned';

//text changed, needs new translation
$sc_holidays_no_file_prefix = 'Helligdage kunne ikke genereres, fordi filen der indeholder information om helligdage for sproget ';
$sc_holidays_no_file_suffix = ' enten er tom eller ikke eksisterer.';

$sc_no_events = 'Ingen hændelser fundet';

//2.03
$cal_export = 'Eksporter';
$cal_import = 'Importer';
$cal_please_wait = 'Vent venligst...';
$cal_import_text = 'Vælg en iCal (*.ics) fil og klik på \'Ok\'';
$cal_filename = 'Fil';
$cal_import_success = 'Data importeret';

//2.04
$cal_add_todo = 'Ny opgave';
$cal_todos = 'Opgaver';

$cal_hide_completed = 'Skjul afsluttede';
$cal_show_all = 'Vis alle opgaver';
$cal_show_own = 'Vis egne opgaver';
$cal_show_responsible = 'Vis ansvarlige opgaver';

$cal_priority = 'Prioritet';
$cal_priority_values[0] = 'Lav';
$cal_priority_values[1] = 'Normal';
$cal_priority_values[2] = 'Høj';

$cal_status = 'Status';
$cal_canceled = 'Annulleret';
$cal_completed = 'Færdig den';
$cal_percent_completed = '% færdig';

$cal_before_task_start = 'Før opgaven starter';
$cal_before_task_due = 'Før opgaven bør være afsluttet';
$cal_responsible = 'Ansvarlig';
$cal_due_at = 'Påbegyndes den';
$cal_no_todos= 'Ingen opgaver fundet';
$cal_resend_invitation = 'Gensend invitation';

//2.05
$cal_everybody_write = 'Alle kan ændre denne hændelse';
$cal_everybody_read = 'Kun jeg kan ændre denne hændelse';

$cal_view = 'Vis';
$cal_views = 'Visninger';
$sc_view_exists = 'En visning ved dette navn eksisterer allerede';
$cal_new_view = 'Ny visning';
$cal_event_calendars = 'Kalender(e)';

//2.06
$cal_view_merged = 'slået sammen';
$cal_view_emerged = 'opstået';

//2.07
$cal_display = 'Vis';
$sc_new_task = "Ny opgave";
$sc_all_day_events = 'alle dag hændelser';
$sc_all_day_event = 'alle dag hændelse';
$sc_usual_events = 'hændelser';
$sc_usual_event = 'hændelse';
