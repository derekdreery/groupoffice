<?php
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('calendar'));
$lang['calendar']['already_accepted']='U heeft deze afspraak al geaccepteerd';
$lang['calendar']['name'] = 'Agenda';
$lang['calendar']['description'] = 'Agenda module; Iedere gebruiker kan afspraken toevoegen, bewerken of verwijderen. Ook kunnen afspraken van andere gebruikers worden ingezien en als het nodig is aangepast worden.';

$lang['calendar']['groupView'] = 'Groepsoverzicht';
$lang['calendar']['event']='Afspraak';
$lang['calendar']['startsAt']='Begint op';
$lang['calendar']['endsAt']='Eindigt op';

$lang['calendar']['exceptionNoCalendarID'] = 'FATAAL: Geen agenda ID!';
$lang['calendar']['appointment'] = 'Afspraak: ';
$lang['calendar']['allTogether'] = 'Samen';

$lang['calendar']['location']='Locatie';

$lang['calendar']['invited']='U bent uitgenodigd voor de volgende afspraak';
$lang['calendar']['acccept_question']='Accepteert u de uitnodiging?';

$lang['calendar']['accept']='Accepteren';
$lang['calendar']['decline']='Afwijzen';

$lang['calendar']['bad_event']='De afspraak bestaat niet meer';

$lang['link_type'][1]='Afspraak';
$lang['calendar']['subject']='Onderwerp';
$lang['calendar']['status']='Status';
$lang['calendar']['statuses']['NEEDS-ACTION']= 'Heeft actie nodig';
$lang['calendar']['statuses']['ACCEPTED']= 'Geaccepteerd';
$lang['calendar']['statuses']['DECLINED']= 'Afgewezen';
$lang['calendar']['statuses']['TENTATIVE']= 'Voorlopig';
$lang['calendar']['statuses']['DELEGATED']= 'Gedelegeerd';
$lang['calendar']['statuses']['COMPLETED']= 'Afgerond';
$lang['calendar']['statuses']['IN-PROCESS']= 'Bezig';
$lang['calendar']['statuses']['CONFIRMED'] = 'Bevestigd';

$lang['calendar']['accept_mail_subject']= 'Uitnodiging voor \'%s\' geaccepteerd';
$lang['calendar']['accept_mail_body']= '%s heeft uw uitnodiging geaccepteerd voor:';
$lang['calendar']['decline_mail_subject']= 'Uitnodiging voor \'%s\' afgewezen';
$lang['calendar']['decline_mail_body']= '%s heeft uw uitnodiging afgewezen voor:';
$lang['calendar']['and']='en';
$lang['calendar']['repeats']= 'Herhaalt elke %s';
$lang['calendar']['repeats_at']= 'Herhaalt elke %s op %s';//eg. Repeats every month at the first monday
$lang['calendar']['repeats_at_not_every']= 'Herhaalt elke %s %s op %s';//eg. Repeats every 2 weeks at monday
$lang['calendar']['repeats_not_every'] = 'Herhaalt elke %s %s';
$lang['calendar']['until']='tot';
$lang['calendar']['not_invited']='U bent niet uitgenodigd voor deze gebeurtenis. U moet wellicht inloggen als een andere gebruiker.';
$lang['calendar']['accept_title']='Geaccepteerd';
$lang['calendar']['accept_confirm']='De eigenaar zal op de hoogte gebracht worden van uw acceptatie voor deze gebeurtenis';
$lang['calendar']['decline_title']='Afgewezen';
$lang['calendar']['decline_confirm']='De eigenaar zal op de hoogte gebracht worden van uw afwijzing voor deze gebeurtenis';
$lang['calendar']['cumulative']='Ongeldige herhaling. De volgende herhaling mag niet plaatsvinden voor de vorige is geeindigd.';
$lang['calendar']['private']='Privé';
$lang['calendar']['import_success']='%s afspraken werden geïmporteerd';

$lang['calendar']['printTimeFormat']='Van %s tot %s';
$lang['calendar']['printLocationFormat']=' op locatie "%s"';
$lang['calendar']['printPage']='Pagina %s van %s';
$lang['calendar']['printList']='Afsprakenlijst';

$lang['calendar']['printAllDaySingle']='Hele dag';
$lang['calendar']['printAllDayMultiple']='Hele dag van %s tot %s';

$lang['calendar']['calendars']='Agenda\'s';

$lang['calendar']['open_resource']='Reservering openen';

$lang['calendar']['resource_mail_subject']='Hulpmiddel \'%s\' gereserveerd voor \'%s\' op \'%s\'';//%s is resource name, %s is event name, %s is start date
$lang['calendar']['resource_mail_body']='%s heeft hulpmiddel \'%s\' gereserveerd. U beheert dit hulpmiddel. Open de reservering om deze te accepteren of weigeren.';

$lang['calendar']['resource_modified_mail_subject']='Hulpmiddel \'%s\' reservering voor \'%s\' op \'%s\' gewijzigd';//%s is resource name, %s is event name, %s is start date
$lang['calendar']['resource_modified_mail_body']='%s heeft de reservering voor hulpmiddel \'%s\' gewijzigd. U beheert dit hulpmiddel. Open de reservering om deze te accepteren of weigeren.';

$lang['calendar']['your_resource_modified_mail_subject']='Reservering voor \'%s\' op \'%s\' in status \'%s\' is gewijzigd';//is resource name, %s is start date, %s is status
$lang['calendar']['your_resource_modified_mail_body']='%s heeft uw reservering voor \'%s\' gewijzigd.';

$lang['calendar']['your_resource_accepted_mail_subject']='Reservering voor \'%s\' op \'%s\' is geaccepteerd';//%s is resource name, %s is start date
$lang['calendar']['your_resource_accepted_mail_body'] = '%s heeft uw boeking voor hulpmiddel \'%s\' geaccepteerd.';

$lang['calendar']['your_resource_declined_mail_subject']='Reservering voor \'%s\' op \'%s\' is geweigerd';//%s is resource name, %s is start date
$lang['calendar']['your_resource_declined_mail_body'] = '%s heeft uw boeking voor hulpmiddel \'%s\' geweigerd.';

$lang['calendar']['birthday_name']='Verjaardag: {NAME}';
$lang['calendar']['birthday_desc']='{NAME} is vandaag {AGE} geworden';

$lang['calendar']['unauthorized_participants_write']='U heeft onvoldoende toegangsrechten om afspraken te plannen voor de volgende gebruikers:<br /><br />{NAMES}<br /><br />Wellicht wilt u nog een uitnodiging versturen zodat zij de afspraak kunnen accepteren en inplannen.';

$lang['calendar']['noCalSelected'] = 'Er is geen agenda geselecteerd voor deze overzicht. Selecteer minstens een agenda bij Beheer.';

$lang['calendar']['month_times'][1]='de eerste';
$lang['calendar']['month_times'][2]='de tweede';
$lang['calendar']['month_times'][3]='de derde';
$lang['calendar']['month_times'][4]='de vierde';
$lang['calendar']['month_times'][5]='de vijfde';

$lang['calendar']['rightClickToCopy']='Gebruik de rechtermuisknop om de koppelingslocatie te kopiëren';

$lang['calendar']['invitation']='Uitnodiging';
$lang['calendar']['invitation_update']='Bijgewerkte uitnodiging';
$lang['calendar']['cancellation']='Annulering';

$lang['calendar']['non_selected'] = 'in niet-geselecteerde agenda';

$lang['calendar']['linkIfCalendarNotSupported']='Gebruik onderstaande links alleen wanneer uw mail programma geen agenda functies ondersteund.';