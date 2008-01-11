<?php
//Uncomment this line in new translations!
require_once($GO_LANGUAGE->get_fallback_language_file('calendar'));

$sc_title = "Titel";
$sc_description = "Beskrivning";
$sc_participants = "Inbjudna";

$sc_location = "Plats";

$month_times[0] = "Första";
$month_times[1] = "Andra";
$month_times[2] = "Tredje";
$month_times[3] = "Fjärde";

$sc_new_app = "Ny händelse";
$sc_calendars = "Kalendrar";
$sc_invited = 'Du är inbjuden att delta i händelsen nedan.';

$sc_types1[0] = 'Ingen upprepning';
$sc_types1[1] = 'dagar';
$sc_types1[2] = 'veckor';
$sc_types1[3] = 'månader på datum';
$sc_types1[4] = 'månader på dag';
$sc_types1[5] = 'år';


$sc_types[0] = 'En gång';
$sc_types[1] = 'Dagligen';
$sc_types[2] = 'Veckovis';
$sc_types[3] = 'Månadsvis på datum';
$sc_types[4] = 'Månadsvis på dag';
$sc_types[5] = 'Årsvis';


$sc_start_at = 'Startar';
$sc_end_at = 'Slutar';
$sc_start_time = 'Start tid';
$sc_end_time = 'Slut tid';

$sc_at_days = 'På dagar';
$sc_of_month = 'av månaden.';

$sc_cycle_start = 'Cykeln startar';
$sc_cycle_end = 'Upprepa tills';
$sc_accept_question = 'Vill du acceptera och schemalägga denna händelse?';

$sc_accept = 'acceptera';
$sc_decline = 'avböja';

$sc_notime = 'Tid är inte tillämpligt.';
$sc_noend = 'Inget slutdatum.';

$sc_never_happens = 'Felaktig inmatning! Denna händelse kommer aldrig inträffa.';
$sc_start_later = 'Felaktig inmatning! Start tiden är senare än slut tiden.';

$sc_add_self = 'Manuell inmatning';

$sc_select_calendar = 'Välj kalender att schemalägga denna händelse i';
$sc_accept_confirm = 'Händelsen är accepterad och schemalaggd.';
$sc_no_calendars = 'Hittade ingen giltig kalender att schemalägga denna händelse i.';
$sc_bad_event = 'Hittade ingen giltig händelse. Händelsen är antagligen borttagen.';
$sc_accept_title = 'Acceptera händelsen';

$sc_decline_confirm = 'Händelsen avböjdes.';
$sc_decline_title = 'Avböj händelsen';

$sc_status = 'Status';
$sc_not_responded = 'Ej besvarad';
$sc_accepted = 'Accepterad';
$sc_declined = 'Avböjd';
$sc_no_participants = 'Inga deltagare';

$sc_person = 'Person';
$sc_owner = 'Ägare';
$sc_type = 'Typ';
$sc_subscribed = 'Prenumererad på';

$sc_subscribe = 'Prenumerera';
$sc_new_calendar = 'Ny kalender';
$sc_no_calendar = 'Ingen kalender hittades. Skapa en ny eller låt Group-Office automatiskt skapa en åt dig.';
$sc_day_view = 'Dags&nbsp;vy';
$sc_week_view = 'Vecko&nbsp;vy';
$sc_list_view = 'List&nbsp;vy';
$sc_put_in = 'Lägg händelsen i';
$sc_inherit_permissions ='Ärv rättigheter från';
$sc_no_inherit = 'Ärv inte rättigheter';

$sc_select_calendar_please = 'Välj åtminstone en kalender.';
$sc_events = 'Händelser';

$sc_from_calendar = 'från kalender';
$sc_enitrely = 'Helt';
$sc_delete_event = 'Ta bort händelse';
$sc_delete_pre = 'Vill du ta bort händelsen';
$sc_delete_suf = 'helt och hållet eller bara från denna kalender?';

$sc_refresh = 'Uppdatera';
$sc_reminder = 'Påminnelse';
$sc_mins = 'minuter';
$sc_hour = 'timma';
$sc_hours = 'timmar';
$sc_day = 'dag';
$sc_days = 'dagar';
$sc_week = 'vecka';
$sc_weeks = 'veckor';
$sc_reminder_set_to_early = "Påminnelsen kan inte sättas innan föregående inträffade. Korta påminnelsetiden eller förläng upprepningsperioden.";
$sc_cycle_start_later = 'Cykelns start datum är senare eller samma som slut datumet.';


$cmdGenerate = 'Generera';
$sc_preferences = 'Preferenser';
$sc_holidays = 'Helgdagar';

$strNoHolidays = 'Inga helgdagar i databasen';
$strUnknown = 'Okänd';
$strYear = 'År';
$strDeleteHolidaysPrefix = 'Är du säker på att du vill ta bort alla helgdagar på året ';
$strDeleteHolidaysSuffix = '?';
$strReplaceHolidays = 'Är du säker på att du vill skriva över alla existerande helgdagar?';
$sc_holidays_text1 = 'Klicka på \''.$cmdGenerate.'\' för att beräkna alla helgdagar för det valda året.';
$sc_holidays_text2 = 'Klicka på \''.$cmdApply.'\' för att lagra alla beräknade helgdagar i databasen.';

//translation required below this
$sc_client = 'Klient';
$sc_no_contact = 'Ingen';

$sc_background = 'Bakgrund';

$sc_colors['red'] = 'Röd';
$sc_colors['green'] = 'Grön';
$sc_colors['blue'] = 'Blå';
$sc_colors['pink'] = 'Rosa';
$sc_colors['orange'] = 'Orange';
$sc_colors['brown'] = 'Brun';
$sc_colors['yellow'] = 'Gul';
$sc_colors['grey'] = 'Grå';
$sc_colors['purple'] = 'Lila';
$sc_colors['white']= 'Vit';
$sc_status = 'status';

$sc_show_hours = 'Visa timmar från';
$sc_to = 'till';

$sc_today = 'Idag';

$sc_recur_every = 'Upprepa varje';
$sc_months = 'månad';

$sc_private_event = 'Privat händelse';
$sc_calendar_exists = 'En kalender med detta namn existerar redan';
//translate
$sc_month = 'Månad';
$sc_view = 'Vy';
$sc_calendar = 'Kalender';
$sc_default = 'Default';
$sc_month_view = 'Månads&nbsp;vy';

//text changed, needs new translation
$sc_holidays_no_file_prefix = 'Helgdagarna kunde inte genereras, för filen som innehåller helgdags information för ';
$sc_holidays_no_file_suffix = ' språket är antingen tom eller existerar inte.';

$sc_no_events = 'Inga händelser hittade';

//2.03
$cal_export = 'Export';
$cal_import = 'Import';
$cal_please_wait = 'Var god vänta...';
$cal_import_text = 'Välj en iCal (*.ics) fil och klicka på \'Ok\'';
$cal_filename = 'Fil';
$cal_import_success = 'Data importerad';

//2.04
$cal_add_todo = 'Ny uppgift';
$cal_todos = 'Uppgifter';

$cal_hide_completed = 'Göm avklarade';
$cal_show_all = 'Visa alla uppgifter';
$cal_show_own = 'Visa egna uppgifter';
$cal_show_responsible = 'Visa ansvariga uppgifter';

$cal_priority = 'Prioritet';
$cal_priority_values[0] = 'Låg';
$cal_priority_values[1] = 'Normal';
$cal_priority_values[2] = 'Hög';

$cal_status = 'Status';
$cal_canceled = 'Avbruten';
$cal_completed = 'Avslutad';
$cal_percent_completed = '% avslutad';

$cal_before_task_start = 'Innan uppgiften startar';
$cal_before_task_due = 'Innan uppgiften ska vara avslutad';
$cal_responsible = 'Ansvarig';
$cal_due_at = 'Slut datum';
$cal_no_todos= 'Inga uppgifter hittade';
$cal_resend_invitation = 'Skicka om inbjudan';

//2.05
$cal_everybody_write = 'Alla kan ändra i denna händelse';
$cal_everybody_read = 'Bara jag kan ändra i denna händelse';

$cal_view = 'Vy';
$cal_views = 'Vyer';
$sc_view_exists = 'En vy med detta namn existerar redan';
$cal_new_view = 'Ny vy';
$cal_event_calendars = 'Kalender(s)';

//2.06
$cal_view_merged = 'merged';
$cal_view_emerged = 'emerged';

//2.07
$cal_display = 'Visa';
$sc_new_task = "Ny uppgift";
$sc_all_day_events = 'heldags händelser';
$sc_all_day_event = 'heldags händelse';
$sc_usual_events = 'händelser';
$sc_usual_event = 'händelse';

//2.08
$sc_recur_section = 'Återkommande';
$sc_options_section = 'Alternativ';
$sc_close = 'Stäng';

$cal_count_single = 'händelse';
$cal_count_multiple = 'händelser';

$cal_until = 'innan';
$cal_forever = 'evigt';

//2.09
$cal_merged_overview = 'Sammanslagen överblick';
$cal_own_availability = 'Egen tillgänglighet';

//2.11
$cal_no_file = 'Ingen fil mottogs';
$cms_no_calendars_in_view = 'Denna vy kan inte visas eftersom det inte finns några kalendrar i denna vy. Klicka på \'Vyer\' för att hantera vyerna.';

$cal_no_reminder = 'Ingen påminnelse';

//2.13
$cal_refresh_rate = 'Uppdateringshastighet';
$sc_seconds = 'sekunder (Lämna blankt om ingen uppdatering önskas)';
$cal_default_reminder = 'Förvald påminnelse';


//2.14
$cal_event = 'Händelse';
$cal_todo = 'Att göra';
$cal_participants_write = 'Deltagare kan ändra i händelsen';

$cal_everybody_write = 'Alla kan ändra i händelsen'; //modified string from 2.05 this became the
$cal_everybody_read = 'Bara jag kan ändra i händelsen';//modified string from 2.05 

$cal_default_permissions = 'Förvalda rättigheter';
$cal_show_todos = 'Visa uppgifter i kalendern';

$cal_edit_series_or_single = 'Detta är en återkommande händelse. Vill du ändra denna enbart eller alla?';
$cal_single = 'Händelse';
$cal_series = 'Alla Händelser';

$cal_legend = 'Översikt';
$cal_multiple_calendars = 'Multipla kalendrar';

$cal_cumulative = 'Felaktig inmatning! Händelsen håller på längre än det återkommande intervallet.';

$cal_weekview = 'Vecko-vyn visar';
$cal_mo_to_fr = 'Måndag till fredag';
$cal_mo_to_su = 'Måndag till söndag';

$cal_batchcalendars = 'Skapa kalendrar för alla användare';
$cal_create_all_calendars = 'Skapa kalendrar för alla användare eller ändra rättigheter';

$cal_no_views = 'Inga vyer hittade';

$cal_my_cals = 'Mina kalendrar';

$cal_event_colors_override = 'Använd manuella färger för händelser';
