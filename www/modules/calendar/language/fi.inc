<?php
//Uncomment this line in new translations! LI 08.02.2006
require_once($GO_LANGUAGE->get_fallback_language_file('calendar'));

$sc_title = "Otsikko";
$sc_description = "Kuvaus";
$sc_participants = "Kutsutut";

$sc_location = "Paikka";

$month_times[0] = "Ensimmäinen";
$month_times[1] = "Toinen";
$month_times[2] = "Kolmas";
$month_times[3] = "Neljäs";

$sc_new_app = "Uusi tapahtuma";
$sc_calendars = "Kalenterit";
$sc_invited = 'Sinut on kutsuttu osallistumaan alla mainittuun tapahtumaan.';

$sc_types1[0] = 'Ei toistoa';
$sc_types1[1] = 'päiviä';
$sc_types1[2] = 'viikkoja';
$sc_types1[3] = 'kuukaudet päiväyksen mukaan';
$sc_types1[4] = 'kuukaudet päivän mukaan';
$sc_types1[5] = 'vuosi';


$sc_types[0] = 'Kerran';
$sc_types[1] = 'Päivittäin';
$sc_types[2] = 'Viikottain';
$sc_types[3] = 'Kuukausittain päiväyksen mukaan';
$sc_types[4] = 'Kuukausittain päivän mukaan';
$sc_types[5] = 'Vuosittain';


$sc_start_at = 'Alkaa';
$sc_end_at = 'Päättyy';
$sc_start_time = 'Alkaa klo';
$sc_end_time = 'Päättyy klo';

$sc_at_days = 'Päivinä';
$sc_of_month = 'kuukaudessa.';

$sc_cycle_start = 'Kierto alkaa';
$sc_cycle_end = 'Toista saakka';
$sc_accept_question = 'Haluatko hyväksyä ja aikatauluttaa tämän tapahtuman?';

$sc_accept = 'hyväky';
$sc_decline = 'kieltäydy';

$sc_notime = 'Ajankohta ei sovi';
$sc_noend = 'Ei loppu päivää.';

$sc_never_happens = 'Väärin syötetty! Tämä tapahtuma ei tule koskaa tapahtumaan.';
$sc_start_later = 'Väärin syötetty!! Aloitus aika on päättymisajan jälkeen.';

$sc_add_self = 'Käsin syöttö';

$sc_select_calendar = 'Vaitse kalenteri johon tämä tapahtuma liitetään';
$sc_accept_confirm = 'Tämä tapahtuma on hyväksytty ja aikataulutettu.';
$sc_no_calendars = 'Voimassa olevaa kalenteria tätä tapahtumaa varten ei löytynyt.';
$sc_bad_event = 'Voimassa olevaa tapahtumaa ei löytynyt. Tapahtuma on todennäköisesti poistettu.';
$sc_accept_title = 'Hyväksy tapahtuma';

$sc_decline_confirm = 'Tapahtumaan kieltäydyttiin.';
$sc_decline_title = 'Kieltäydy tapahtumasta';

$sc_status = 'Tila';
$sc_not_responded = 'Ei vastattu';
$sc_accepted = 'Hyväksytty';
$sc_declined = 'Kieltäydytty';
$sc_no_participants = 'Ei osanottajia';

$sc_person = 'Henkilö';
$sc_owner = 'Omistaja';
$sc_type = 'Tyyppi';
$sc_subscribed = 'Tilattu';

$sc_subscribe = 'Tilaa';
$sc_new_calendar = 'Uusi kalenteri';
$sc_no_calendar = 'Kaleteria ei löytynyt. Luo yksi nytt tai anna Group-Officen automaattisesti luoda yksi sinulle.';
$sc_day_view = 'Päivä&nbsp;näkymä';
$sc_week_view = 'Viiko&nbsp;näkymä';
$sc_list_view = 'Lista&nbsp;näkymä';
$sc_put_in = 'Sijoita tapahtuma';
$sc_inherit_permissions ='Oikeudet peritään';
$sc_no_inherit = 'Oikeuksia ei peritä';

$sc_select_calendar_please = 'Ole ystävällinen ja valitSelect at least one calendar please.';
$sc_events = 'Tapahtumat';

$sc_from_calendar = 'kalenterista';
$sc_enitrely = 'Kokonaan';
$sc_delete_event = 'Poista tapahtuma';
$sc_delete_pre = 'Haluatko poistaa tapahtuman';
$sc_delete_suf = 'kokonaan vai ainoastaan tästä kalenterista?';

$sc_refresh = 'Päivitä';
$sc_reminder = 'Muistutus';
$sc_mins = 'minutteja';
$sc_hour = 'tunti';
$sc_hours = 'tunteja';
$sc_day = 'päivä';
$sc_days = 'päivää';
$sc_week = 'viikko';
$sc_weeks = 'viikkoa';
$sc_reminder_set_to_early = "Muistutusta ei voi asettaa ennen edellistä. Lyhennä muistutus aikaa tai pidennä toisto valiä.";
$sc_cycle_start_later = 'Kierron aloitus päivä on ennen tai sama kuin loppu aika.';


$cmdGenerate = 'Luo';
$sc_preferences = 'Omat asetukset';
$sc_holidays = 'Lomapäivätä';

$strNoHolidays = 'Tietokannassa ei ole pyhäpäiviä';
$strUnknown = 'Tuntematon';
$strYear = 'Vuosi';
$strDeleteHolidaysPrefix = 'Oletko varma että haluat poistaa tämän vuoden kaikki pyhäpäivät ';
$strDeleteHolidaysSuffix = '?';
$strReplaceHolidays = 'Oletko vama että haluat kirjoittaa yli kaikki olemassa olevat pyhäpäivä tiedot?';
$sc_holidays_text1 = 'Paina \''.$cmdGenerate.'\' pyhäpäivä tietojen luomiseksi valitulle vuodelle.';
$sc_holidays_text2 = 'Paina \''.$GLOBALS['cmdApply'].'\' kaikki pyhäpäivä tietojen tallettamiseksi tietokantaan.';

//translation required below this
$sc_client = 'Asiakas';
$sc_no_contact = 'Ei ole';

$sc_background = 'Tausta';

$sc_colors['red'] = 'Punainen';
$sc_colors['green'] = 'Vihreä';
$sc_colors['blue'] = 'Sininen';
$sc_colors['pink'] = 'Pinkki';
$sc_colors['orange'] = 'Oranssi';
$sc_colors['brown'] = 'Ruskea';
$sc_colors['yellow'] = 'Keltainen';
$sc_colors['grey'] = 'Harmaa';
$sc_colors['purple'] = 'Purppura';
$sc_colors['white']= 'Valkoinen';
$sc_status = 'tila';

$sc_show_hours = 'Näytä tunnit alkaen';
$sc_to = '-';

$sc_today = 'Tänään';

$sc_recur_every = 'Toista joka';
$sc_months = 'kuukaudet';

$sc_private_event = 'Yksitynen tapahtuma';
$sc_calendar_exists = 'Tämän niminen kalenteri on jo olemassa';
//translate
$sc_month = 'Kuukausi';
$sc_view = 'Näkymä';
$sc_calendar = 'Kalenteri';
$sc_default = 'Oletus';
$sc_month_view = 'Kuukausi&nbsp;näkymä';

//text changed, needs new translation
$sc_holidays_no_file_prefix = 'Pyhäpäiviä ei voi luoda, tiedoto joka sisältää pyhäpäivä tiedot kielelle ';
$sc_holidays_no_file_suffix = ' on joko tyhjä tai sitä ei ole olemassa.';

$sc_no_events = 'Tapahtumia ei löytynyt';

//2.03
$cal_export = 'Vie';
$cal_import = 'Tuo';
$cal_please_wait = 'Ole hyvä ja odota...';
$cal_import_text = 'Valitse iCal (*.ics) tiedosta ja paina \'Ok\'';
$cal_filename = 'Tiedosto';
$cal_import_success = 'Tiedot tuotu';

//2.04
$cal_add_todo = 'Uusi tehtävä';
$cal_todos = 'Tehtävät';

$cal_hide_completed = 'Piilottaminen suoritettu';
$cal_show_all = 'Näytyä kaikki tehtävät';
$cal_show_own = 'Näytä omat tehtävät';
$cal_show_responsible = 'Näytä vastuulliset tehtävät';

$cal_priority = 'Prioriteetti';
$cal_priority_values[0] = 'Matala';
$cal_priority_values[1] = 'Normaali';
$cal_priority_values[2] = 'Korkea';

$cal_status = 'Tila';
$cal_canceled = 'Peruttu';
$cal_completed = 'Valmistunut';
$cal_percent_completed = '% valmis';

$cal_before_task_start = 'Ennen tehtävän aloitusta';
$cal_before_task_due = 'Ennenkuin tehtävän pitäisi olla valmis';
$cal_responsible = 'Vastuullinen';
$cal_due_at = 'Loppu päivä';
$cal_no_todos= 'Tehtäviä ei löytynyt';
$cal_resend_invitation = 'Lähetä kutsu uudelleen';

//2.05



$cal_view = 'Näkymä';
$cal_views = 'Näkymät';
$sc_view_exists = 'Tällä nimellä oleva näkymä on jo olemassa';
$cal_new_view = 'Uusi näkymä';
$cal_event_calendars = 'Kalenteri(t)';

//2.06
$cal_view_merged = 'merged';
$cal_view_emerged = 'emerged';

//2.07
$cal_display = 'Näyttö';
$sc_new_task = "Uusi tehtävä";
$sc_all_day_events = 'koko päivän tapahtumat';
$sc_all_day_event = 'koko päivään tapahtuma';
$sc_usual_events = 'tapahtumat';
$sc_usual_event = 'tapahtuma';

//2.08
$sc_recur_section = 'Toisto';
$sc_options_section = 'Optiot';
$sc_close = 'Sulje';

$cal_count_single = 'tapahtuma';
$cal_count_multiple = 'tapahtumat';

$cal_until = 'ennen';
$cal_forever = 'ikuisesti';

//2.09
$cal_merged_overview = 'Yhdistetty näkymä';
$cal_own_availability = 'Oma käytettävyys';

//2.11
$cal_no_file = 'Mitään tiedotoa ei vastaanotettu';
$cms_no_calendars_in_view = 'Tätä näkymää ei voi näyttää koska tässä näkymässä ei ole kalenteria. Paina \'Views\' näkymine hallinnoimiseksi.';
$cal_no_reminder = 'Ei muistutusta';

//2.13
$cal_refresh_rate = 'Päivitys tahti';
$sc_seconds = 'sekunttia (jätä tyhjäksi jos et halua päivitystä)';
$cal_default_reminder = 'Oletus muistutus';


//2.14
$cal_event = 'Tapahtuma';
$cal_todo = 'Tehtävä';
$cal_participants_write = 'Osallstujat voivat muokata tapahtumaa';

$cal_everybody_write = 'Kaikki voivat muokata tapahtumaa'; //modified string from 2.05 this became the
$cal_everybody_read = 'Vain minä voin muokata tapahtumaa';//modified string from 2.05 

$cal_default_permissions = 'Oletus oikeudet';
$cal_show_todos = 'Näytä tehtävät kalenterissa';

$cal_edit_series_or_single = 'Muokka vain tätä esintymää vai koko sarjaa?';
$cal_single = 'Esintymää';
$cal_series = 'Koko sarjaa';

$cal_legend = 'Otsikko';
$cal_multiple_calendars = 'Useampi kalenteri';

$cal_cumulative = 'Väärin syötetty! Tapahtuma kestää pidempään kuin toisto väli.';

$cal_weekview = 'Viikko näkymä näyttää';
$cal_mo_to_fr = 'Maanantaista perjantaihin';
$cal_mo_to_su = 'Maanantaista sunnutaihin';

$cal_batchcalendars = 'Luo kalenterit kaikille käyttäjille';
$cal_create_all_calendars = 'Luo kalenerit kaikille käyttäjille tai anna käyttäjäryhmille oikeudet';

$cal_no_views = 'Näkymiä ei löytynyt';

$cal_my_cals = 'Omat kalenterit';

$cal_event_colors_override = 'Asettele tapahtumien värit käsin';

//2.15
$cal_groups = 'Ryhmät';
$cal_new_group = 'Uusi ryhmä';
$cal_group_exists = 'Tämän niminen ryhmä on jo olemassa. Valitse toinen nimi.';
$cal_no_calendars = 'Tälle ryhmälle ei löytynyt kalenteria';
$cal_group = 'Ryhmä';
$cal_resources = 'Resurssit';

$cal_event_mail_subject = 'Tapahtuma \'%s\' luotu kalenteriin';//%s is event name
$cal_event_mail_body = '%s on tehnyt tapaamisen \'%s\' kalenteriisi nimeltä \'%s\'.'; //irst %s is the name of the person who created the event. Second is the event name, third is the calendar name
$cal_event_mail_open = 'Tapaaminen avoin';
$cal_add_custom_field = 'Lisää oma kenttä';

$cal_event_modified_mail_subject = 'Kalenterissa olevaa tapahtumaa \'%s\' on muokattu';//%s is event name
$cal_event_modified_mail_body = '%s on muokannut tapaamista \'%s\' kalenterissa \'%s\'.'; //First %s is the name of the person who created the event. Second is the event name, third is the calendar name

$cal_your_event_modified_mail_subject = 'Luomasi tapahtumaa \'%s\' on muokattu';//%s is event name
$cal_your_event_modified_mail_body = '%s on muikannut tapaamista \'%s\' joka on sinun luoma.'; //First %s is the name of the person who created the event. Second is the event name.
$cal_no_custom_fields = 'Omia kenttiä ei ole määritelty';
$cal_add_field = 'Lisää kenttä';


$cal_resource_mail_subject = 'Resussi \'%s\' varattu'; //%s is resource name
$cal_resource_mail_body = '%s on varannut resurssit \'%s\'. Olet tämän resurssin ylläpitäjä. Avaa varaus ja hyväksy tai hylkää se.'; //First %s is the name of the person who created the event. Second is the calendar name
$cal_open_resource = 'Avoin varaus';

$cal_resource_modified_mail_subject = 'Resurssin \'%s\' varausta on muokattu';//%s is resource name
$cal_resource_modified_mail_body = '%s on muokannut resurssin \'%s\'varausta. Olet tämän resurssin ylläpitäjä. Avaa varaus ja hyväksy tai hylkää se.'; //First %s is the name of the person who created the event. Second is the calendar name

$cal_your_resource_modified_mail_subject = 'Varaamasi resurssin \'%s\' varausta on muutettu ';//%s is resource name
$cal_your_resource_modified_mail_body = '%s on muokannut resurssin \'%s\' varausta.'; //First %s is the name of the person who created the event. Second is the calendar name


$cal_accept_mail_subject = 'Kutsu \'%s\' hyväksytty';
$cal_accept_mail_body = '%s on hyväksynyt kutsusi \'%s\'';

$cal_decline_mail_subject = 'Kutsu \'%s\' hylätty';
$cal_decline_mail_body = '%s on hylännyt kutsusi \'%s\'';

$cal_statuses['NEEDS-ACTION'] = 'Odotta toimenpidettä';
$cal_statuses['ACCEPTED'] = 'Hyväksytty';
$cal_statuses['DECLINED'] = 'Hylätty';
$cal_statuses['TENTATIVE'] = 'Kesken';
$cal_statuses['DELEGATED'] = 'Delegoitu';
$cal_statuses['COMPLETED'] = 'Valmis';
$cal_statuses['IN-PROCESS'] = 'Työn alla';

$cal_booking = 'Resurssin \'%s\' varaus'; // %s is resource name

$cal_group_admins = 'Järjestelmänhoitaja';
$cal_no_admins = 'Järjestelmänhoitajaa ei ole määritelty';
$cal_add_admin = 'Lisää Järjestelmänhoitaja';

$cal_scale='Skaala';

$cal_show_completed = 'Näytä valmiit tehtävät';
?>
