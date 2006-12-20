<?php
require_once($GO_LANGUAGE->get_fallback_language_file('calendar'));

$auml = utf8_encode('�');
$ouml = utf8_encode('�');
$uuml = utf8_encode('�');
$Auml = utf8_encode('�');
$Ouml = utf8_encode('�');
$Uuml = utf8_encode('�');
$szlig = utf8_encode('�');

$sc_title = "Titel";
$sc_description = "Beschreibung";
$sc_participants = "Teilnehmer";

$sc_location = "Veranstaltungsort";

$month_times[0] = "Ersten";
$month_times[1] = "Zweiten";
$month_times[2] = "Dritten";
$month_times[3] = "Vierten";

$sc_new_app = "Neuer Termin";
$sc_calendars = "Kalender";
$sc_invited = 'Sie sind eingeladen, der unten beschriebenen Veranstaltung beizutreten.';

$sc_types1[0] = 'Keine Wiederholung';
$sc_types1[1] = 'Tage';
$sc_types1[2] = 'Wochen';
$sc_types1[3] = 'Monate nach Datum';
$sc_types1[4] = 'Monate nach Wochentag';
$sc_types1[5] = 'Jahre';


$sc_types[0] = 'Einmalig';
$sc_types[1] = 'Täglich';
$sc_types[2] = 'Wöchentlich';
$sc_types[3] = 'Monatlich nach Datum';
$sc_types[4] = 'Monatlich nach Wochentag';
$sc_types[5] = 'Jährlich';


$sc_start_at = 'Startet am';
$sc_end_at = 'Endet am';
$sc_start_time = 'Startzeit';
$sc_end_time = 'Endezeit';

$sc_at_days = 'Am';
$sc_of_month = 'des Monats.';

$sc_cycle_start = 'Zyklusstart am';
$sc_cycle_end = 'Wiederholung bis';
$sc_accept_question = 'Möchten Sie diesen Termin eintragen?';

$sc_accept = 'Akzeptieren';
$sc_decline = 'Ablehnen';

$sc_notime = 'die Veranstaltung ist ganztägig';
$sc_noend = 'kein Endedatum.';

$sc_never_happens = 'Eingabefehler! Dieser Termin wurde nicht gespeichert.';
$sc_start_later = 'Eingabefehler!! Die Startzeit liegt hinter der Endezeit.';

$sc_add_self = 'Manuelle Eingabe';

$sc_select_calendar = 'Einen Kalender auswählen, welchem der Termin zugeordnet werden soll.';
$sc_accept_confirm = 'Der Termin wurde erfolgreich eingetragen.';
$sc_no_calendars = 'Kein gültiger Kalender vorhanden.';
$sc_bad_event = 'Kein gültiger Termineintrag vorhanden. Ist möglicherweise bereits gelöscht.';
$sc_accept_title = 'Termin akzeptieren';

$sc_decline_confirm = 'Der Termin wurde abgelehnt.';
$sc_decline_title = 'Termin ablehnen';

$sc_status = 'Status';
$sc_not_responded = 'Keine Antwort';
$sc_accepted = 'Akzeptiert';
$sc_declined = 'Abgelehnt';
$sc_no_participants = 'Keine Teilnehmer';

$sc_person = 'Person';
$sc_owner = 'Eigentümer';
$sc_type = 'Typ';
$sc_subscribed = 'Abonniert';

$sc_subscribe = 'Abonnieren';
$sc_new_calendar = 'Neuer Kalender';
$sc_no_calendar = 'Kein Kalender vorhanden. Bitte legen Sie jetzt einen an, oder lassen Sie Group-Office einen fuer Sie anlegen.';
$sc_day_view = 'Tagesansicht';
$sc_week_view = 'Wochenansicht';
$sc_list_view = 'Listenansicht';
$sc_put_in = 'Eintragen in Kalender';
$sc_inherit_permissions = 'Übernehme Berechtigungen von';
$sc_no_inherit = 'Nicht übernehmen';

$sc_select_calendar_please = 'Bitte mindestens einen Kalender auswählen.';
$sc_events = 'Termine';

$sc_from_calendar = 'Vom Kalender';
$sc_enitrely = 'Komplett';
$sc_delete_event = 'Lösche Termin';
$sc_delete_pre = 'Möchten Sie den Termin';
$sc_delete_suf = 'komplett oder lediglich aus diesem Kalender entfernen?';

$sc_refresh = 'Aktualisieren';
$sc_reminder = 'Erinnerung';
$sc_mins = 'Minuten';
$sc_hour = 'Stunde';
$sc_hours = 'Stunden';
$sc_day = 'Tag';
$sc_days = 'Tage';
$sc_week = 'Woche';
$sc_weeks = 'Wochen';
$sc_reminder_set_to_early = "Die Erinnerungsfunktion kann nicht aktiviert werden. Bitte entweder den Zeitraum für die Erinnerungsfunktion verringern oder die Periode des Wiederholungstermins vergrüssern.";
$sc_cycle_start_later = 'Das Startdatum des Zyklus liegt hinter oder ist gleich dem Endedatum.';


$cmdGenerate = 'Generieren';
$sc_preferences = 'Einstellungen';
$sc_holidays = 'Feiertage';

$strNoHolidays = 'Keine Feiertage in der Datenbank';
$strUnknown = 'Unbekannt';
$strYear = 'Jahr';
$strDeleteHolidaysPrefix = 'Sollen alle Feiertage für das Jahr ';
$strDeleteHolidaysSuffix = ' aus der Datenbank entfernt werden?';
$strReplaceHolidays = 'Alle gespeicherten Feiertage werden überschrieben. Sind Sie sicher?';
$sc_holidays_text1 = 'Bitte auf \''.$cmdGenerate.'\' klicken, um die Feiertage für das gewählte Jahr zu ermitteln.';
$sc_holidays_text2 = 'Bitte auf \''.$GLOBALS['cmdApply'].'\' klicken, um die ermittelten Feiertage in die Datenbank zu übernehmen.';

//translation required below this
$sc_client = 'Mandant';
$sc_no_contact = 'kein';

$sc_background = 'Hintergrund';

$sc_colors['red'] = 'Rot';
$sc_colors['green'] = 'Grün';
$sc_colors['blue'] = 'Blau';
$sc_colors['pink'] = 'Pink';
$sc_colors['orange'] = 'Orange';
$sc_colors['brown'] = 'Braun';
$sc_colors['yellow'] = 'Gelb';
$sc_colors['grey'] = 'Grau';
$sc_colors['purple'] = 'Lila';
$sc_colors['white']= 'Weiss';

$sc_show_hours = 'Stundenanzeige von';
$sc_to = 'bis';

$sc_today = 'Heute';

$sc_recur_every = 'Wiederholung alle';
$sc_months = 'Monate';

$sc_private_event = 'Privater Termin';
$sc_calendar_exists = 'Ein Kalender mit diesem Namen ist bereits vorhanden';
//translate
$sc_month = 'Monat';
$sc_view = 'Ansicht';
$sc_calendar = 'Kalender';
$sc_default = 'Standard';
$sc_month_view = 'Monatsansicht';

//text changed, needs new translation
$sc_holidays_no_file_prefix = 'Die Ferien konnten nicht generiert werden, da die Datei mit den Daten der Ferien für ';
$sc_holidays_no_file_suffix = ' entweder leer oder nciht vorhanden ist.';

$sc_no_events = 'Keine Termine';

//2.03
$cal_export = 'Export';
$cal_import = 'Import';
$cal_please_wait = 'Bitte warten...';
$cal_import_text = 'iCal (*.ics) Datei wählen und auf \'Ok\' klicken';
$cal_filename = 'Datei';
$cal_import_success = 'Daten importiert';

//2.04
$cal_add_todo = 'Neue Aufgabe';
$cal_todos = 'Aufgaben';

$cal_hide_completed = 'Erledigte verbergen';
$cal_show_all = 'Alle Aufgaben';
$cal_show_own = 'Eigene Aufgaben';
$cal_show_responsible = 'Eigene Zuständigkeit';

$cal_priority = 'Priorität';
$cal_priority_values[0] = 'Niedrig';
$cal_priority_values[1] = 'Normal';
$cal_priority_values[2] = 'Hoch';

$cal_status = 'Status';
$cal_canceled = 'Abgebrochen';
$cal_completed = 'Erledigt am';
$cal_percent_completed = '% erledigt';

$cal_before_task_start = 'vor Aufgabenbeginn';
$cal_before_task_due = 'vor Aufgabenende';
$cal_responsible = 'Verantwortlich';
$cal_due_at = 'Endet am';
$cal_no_todos= 'Keine Aufgaben gefunden';
$cal_resend_invitation = 'Einladung nochmal verschicken';

//2.05



$cal_view = 'Ansicht';
$cal_views = 'Ansichten';
$sc_view_exists = 'Eine Ansicht mit diesem Namen ist bereits vorhanden';
$cal_new_view = 'Neue Ansicht';
$cal_event_calendars = 'Kalender';

//2.06
$cal_view_merged = 'zusammen';
$cal_view_emerged = 'einzeln';

//2.07
$cal_display = 'Anzeige';
$sc_new_task = "Neue Aufgabe";
$sc_all_day_events = 'ganztägig';
$sc_all_day_event = 'ganztägig';
$sc_usual_events = 'Termine';
$sc_usual_event = 'Termin';

//2.08
$sc_recur_section = 'Wiederholung';
$sc_options_section = 'Optionen';
$sc_close = 'Schließen';

$cal_count_single = 'Termin';
$cal_count_multiple = 'Termine';

$cal_until = 'bis';
$cal_forever = 'unendlich';

//2.09
$cal_merged_overview = 'Zusammenfassung';
$cal_own_availability = 'Eigene Verfügbarkeit';

//2.11
$cal_no_file = 'Die Datei wurde nicht übertragen';
$cms_no_calendars_in_view = 'Diese Ansicht beinhaltet keine Kalender. Bitte auf \'Ansichten\' klicken, um diese Ansicht zu bearbeiten.';
$cal_no_reminder = 'keine';

//2.13
$cal_refresh_rate = 'Aktualisierungintervall';
$sc_seconds = 'Sekunden (Leer lassen, wenn nicht gewünscht)';
$cal_default_reminder = 'Voreinstellung für die Erinnerungsfunktion';


//2.14
$cal_event = 'Termin';
$cal_todo = 'Aufgabe';
$cal_participants_write = 'Teilnehmer dürfen den Termin bearbeiten';

$cal_everybody_write = 'Alle dürfen den Termin bearbeiten'; //modified string from 2.05 this became the
$cal_everybody_read = 'Nur ich darf den Termin bearbeiten';//modified string from 2.05 

$cal_default_permissions = 'Voreinstellung für die Berechtigungen';
$cal_show_todos = 'Aufgaben im Kalender anzeigen';

$cal_edit_series_or_single = 'Möchten Sie lediglich diesen Einzeltermin bearbeiten, oder beabsichtigen Sie Ihre Änderungen auf alle Wiederholungstermine anzuwenden?';
$cal_single = 'Einzeltermin';
$cal_series = 'Alle Termine';

$cal_legend = 'Legende';
$cal_multiple_calendars = 'Mehrere Kalender';

$cal_cumulative = 'Eingabefehler! Der Termin überschreitet den festgelegten Wiederholungsintervall.';

$cal_weekview = 'Wochenanzeige';
$cal_mo_to_fr = 'Montag bis Freitag';
$cal_mo_to_su = 'Montag bis Sonntag';

$cal_batchcalendars = 'Kalender für alle Benutzer anlegen';
$cal_create_all_calendars = 'Kalender für alle Benutzer anlegen bzw. Berechtigungen vergeben';

$cal_no_views = 'Keine Ansichten gefunden';

$cal_my_cals = 'Meine Kalender';

$cal_event_colors_override = 'Benutze die manuelle Farbauswahl für Ereignisse';

//2.15
$cal_groups = 'Gruppen';
$cal_new_group = 'Neue Gruppe';
$cal_group_exists = 'Dieser Gruppenname existiert bereits. Bitte wählen Sie einen anderen Namen.';
$cal_no_calendars = 'In dieser Gruppe wurden keine Kalender gefunden';
$cal_group = 'Gruppe';
$cal_resources = 'Quellen';

$cal_event_mail_subject = 'Das Ereignis \'%s\' wurde in Ihrem Kalender eingetragen';//%s is event name
$cal_event_mail_body = '%s hat eine Anforderung \'%s\' in Ihrem Kalender \'%s\' eingetragen.'; //irst %s is the name of the person who created the event. Second is the event name, third is the calendar name
$cal_event_mail_open = 'Offene Anforderungen';
$cal_add_custom_field = 'Benutzerdefiniertes Feld hinzufügen';

$cal_event_modified_mail_subject = 'Das Ereignis \'%s\' wurde in Ihrem Kalender bearbeitet';//%s is event name
$cal_event_modified_mail_body = '%s hat die Anforderung \'%s\' in Ihrem Kalender \'%s\' geändert.'; //First %s is the name of the person who created the event. Second is the event name, third is the calendar name

$cal_your_event_modified_mail_subject = 'Das von Ihnen erstellte Ereignis \'%s\' wurde geändert';//%s is event name
$cal_your_event_modified_mail_body = '%s hat die von Ihnen erstellte Anforderung \'%s\' geändert.'; //First %s is the name of the person who created the event. Second is the event name.
$cal_no_custom_fields = 'Keine benutzerdefinierten Felder vorhanden';
$cal_add_field = 'Feld hinzufügen';


$cal_resource_mail_subject = 'Resource \'%s\' gebucht'; //%s is resource name
$cal_resource_mail_body = '%s has made a booking for the resource \'%s\'. You are the maintainer of this resource. Please open the booking to decline or approve it.'; //First %s is the name of the person who created the event. Second is the calendar name
$cal_open_resource = 'Offene Buchungen';

$cal_resource_modified_mail_subject = 'Resource \'%s\' booking modified';//%s is resource name
$cal_resource_modified_mail_body = '%s has modified a booking for the resource \'%s\'. You are the maintainer of this resource. Please open the booking to decline or approve it.'; //First %s is the name of the person who created the event. Second is the calendar name

$cal_your_resource_modified_mail_subject = 'Your booking for \'%s\' in status %s is modified';//%s is resource name, status
$cal_your_resource_modified_mail_body = '%s has modified your booking for the resource \'%s\'.'; //First %s is the name of the person who created the event. Second is the calendar name


$cal_accept_mail_subject = 'Einladung für \'%s\' angenommen';
$cal_accept_mail_body = '%s hat Ihre Einladung für \'%s\' angenommen';

$cal_decline_mail_subject = 'Einladung für \'%s\' abgelehnt';
$cal_decline_mail_body = '%s hat Ihre Einladung für \'%s\' abgelehnt';

$cal_statuses['NEEDS-ACTION'] = 'Benötigt Aktion';
$cal_statuses['ACCEPTED'] = 'Angenommen';
$cal_statuses['DECLINED'] = 'Abgelehnt';
$cal_statuses['TENTATIVE'] = 'Vorrübergehend';
$cal_statuses['DELEGATED'] = 'Beauftragt';
$cal_statuses['COMPLETED'] = 'Fertiggestellt';
$cal_statuses['IN-PROCESS'] = 'In Bearbeitung';

$cal_booking = 'Buchung für \'%s\'';//%s is resource name

$cal_group_admins = 'Admins';
$cal_no_admins = 'Keine Admins angegeben';
$cal_add_admin = 'Admin hinzufügen';

$cal_scale='Skalieren';

$cal_show_completed = 'Zeige fertige Aufgaben';

$cal_resource = 'Resource';
$cal_grid_view = 'Tabellenansicht';

$cal_field_types['checkbox'] = 'Checkbox';
$cal_field_types['text'] = 'Text';
$cal_field_types['textarea'] = 'Textfeld';
$cal_field_types['date'] = 'Datumsfeld';

$cal_conflict = 'Dieses Ereignis kollidiert mit einem anderen Ereignis oder einer Buchung. Klicken Sie auf \'Ok\' oder \'Annehmen\' um diese Warnung zu ignorieren, oder 
\'Verfügbarkeit überprüfen\' um einen passenden Zeitraum zu finden.';

$cal_select_calendar = 'Wähle Kalender';
$cal_check_conflicts = 'Warne mich beim Erstellen eines Termins über mögliche Terminkonflikten';
$cal_email_changes = 'Sende mir eine E-Mail wenn jemand Ändeurngen in meinem Kalender vornimmt';
$cal_shared_calendars = 'Freigegebene Kalender';

$cal_admin = 'Administration';
$cal_resource_groups = 'Resource Gruppen';
$cal_open_calendar = 'Öffne einen anderen Kalender';

$cal_email_reminders='Sende mir eine Erinnerung per E-Mail';

$cal_favourites = 'Favoriten';

$cal_your_resource_accepted_mail_subject = 'Buchung für \'%s\' wurde angenommen';//%s is resource name, status
$cal_your_resource_accepted_mail_body = '%s hat Ihre Buchung für \'%s\' angenommen.'; //First %s is the name of the person who created the event. Second is the calendar name

$cal_your_resource_declined_mail_subject = 'Buchung für \'%s\' wurde abgesagt';//%s is resource name
$cal_your_resource_declined_mail_body = '%s hat Ihre Buchung für  \'%s\' abgelehnt.'; //First %s is the name of the person who created the event. Second is the calendar name

$cal_resource_deleted_mail_subject = 'Buchung für \'%s\' wurde gelöscht';//%s is resource name, status

$cal_repeats_at = 'Wiederhole alle %s am %s';//eg. Repeats every month at the first monday
$cal_repeats_at_not_every = 'Wiederhole alle %s %s am %s';//eg. Repeats every 2 weeks at monday 

$cal_repeats = 'Wiederhole alle %s';

$cal_and='und';
$sc_months='Monate';
$sc_month='Monat';
$sc_year='Jahr';
$sc_years='Jahre';

$cal_another_resource = 'Die Recource wurde zu %s geändert';

$cal_background_colors = 'Hintergrundfarben';

$cal_weekday = 'Wochentag';
$cal_background_color='Hintergrundfarbe';
$cal_background = 'Hintergrund';
