<?php
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('calendar'));
$lang['calendar']['name'] = 'Kalender';
$lang['calendar']['description'] = 'Kalendemodul: Alle brukere kan legge til, redigere og slette avtaler. Man kan også se andre brukeres avtaler, og de kan endres om nødvendig.';

$lang['link_type'][1]='Avtale';

$lang['calendar']['groupView'] = 'Gruppevisning';
$lang['calendar']['event']='Hendelse';
$lang['calendar']['startsAt']='Begynner';
$lang['calendar']['endsAt']='Slutter';

$lang['calendar']['exceptionNoCalendarID'] = 'FATAL: Ingen kalender-ID!';
$lang['calendar']['appointment'] = 'Avtale: ';
$lang['calendar']['allTogether'] = 'Alle sammen';

$lang['calendar']['location']='Lokasjon';

$lang['calendar']['invited']='Du er invitert til følgende hendelse';
$lang['calendar']['acccept_question']='Aksepterer du denne hendelsen?';

$lang['calendar']['accept']='Aksepter';
$lang['calendar']['decline']='Avvis';

$lang['calendar']['bad_event']='Denne hendelsen eksisterer ikke lenger';

$lang['calendar']['subject']='Emne';
$lang['calendar']['status']='Status';



$lang['calendar']['statuses']['NEEDS-ACTION'] = 'Trenger handling';
$lang['calendar']['statuses']['ACCEPTED'] = 'Akseptert';
$lang['calendar']['statuses']['DECLINED'] = 'Avvist';
$lang['calendar']['statuses']['TENTATIVE'] = 'Tentativ';
$lang['calendar']['statuses']['DELEGATED'] = 'Delegert';
$lang['calendar']['statuses']['COMPLETED'] = 'Fullført';
$lang['calendar']['statuses']['IN-PROCESS'] = 'Under behandling';


$lang['calendar']['accept_mail_subject'] = 'Invitasjon til \'%s\' er akseptert';
$lang['calendar']['accept_mail_body'] = '%s har akseptert din invitasjon til:';

$lang['calendar']['decline_mail_subject'] = 'Invitasjon til \'%s\' er avvist';
$lang['calendar']['decline_mail_body'] = '%s har avvist din invitasjon til:';

$lang['calendar']['location']='Lokasjon';
$lang['calendar']['and']='og';

$lang['calendar']['repeats'] = 'Gjentas hver %s';
$lang['calendar']['repeats_at'] = 'Gjentas hver %s på %s';//eg. Repeats every month at the first monday
$lang['calendar']['repeats_at_not_every'] = 'Gjentas hver %s %s på %s';//eg. Repeats every 2 weeks at monday
$lang['calendar']['until']='til og med'; 

$lang['calendar']['not_invited']='Du er ikke blitt invitert til denne hendelsen. Det kan være at du må logge inn som en annen bruker.';


$lang['calendar']['accept_title']='Akseptert';
$lang['calendar']['accept_confirm']='Avsender vil få beskjed om at du har akseptert hendelsen';

$lang['calendar']['decline_title']='Avvist';
$lang['calendar']['decline_confirm']='Avsender vil få beskjed om at du har avvist hendelsen';

$lang['calendar']['cumulative']='Ugyldig gjentagelsesregel. Neste forskomst kan ikke starte før den forrige er ferdig.';

$lang['calendar']['already_accepted']='Du har allerede akseptert denne hendelsen.';

$lang['calendar']['private']='Privat';

$lang['calendar']['import_success']='%s hendelser er importert';

$lang['calendar']['printTimeFormat']='Fra %s til %s';
$lang['calendar']['printLocationFormat']=' på lokasjonen "%s"';
$lang['calendar']['printPage']='Side %s av %s';
$lang['calendar']['printList']='Avtaleoversikt';

$lang['calendar']['printAllDaySingle']='Hele dagen';
$lang['calendar']['printAllDayMultiple']='Hele dagen fra %s til %s';

$lang['calendar']['calendars']='Kalendere';
$lang['calendar']['resource_mail_subject']='Ressursen \'%s\' er reservert'; //%s is resource name
$lang['calendar']['resource_mail_body']='%s ønsker å reservere ressursen \'%s\'. Du er ansvarlig for denne ressursen. Du må åpne bestillingen for å bekrefte eller avvise reservasjonen.'; //First %s is the name of the person who created the event. Second is the calendar name
$lang['calendar']['open_resource']='Åpne bestilling';
$lang['calendar']['resource_modified_mail_subject']='Endring i bestilling av ressursen \'%s\' ';//%s is resource name
$lang['calendar']['resource_modified_mail_body']='%s har endret en bestilling av ressursen \'%s\'. Du er ansvarlig for denne ressursen. Du må åpne bestillingen for å bekrefte eller avvise endringen.'; //First %s is the name of the person who created the event. Second is the calendar name
$lang['calendar']['your_resource_modified_mail_subject']='Din bestilling av \'%s\' med status %s er endret';
$lang['calendar']['your_resource_modified_mail_body']='%s her endret sin bestilling av ressursen \'%s\'.';
$lang['calendar']['your_resource_accepted_mail_subject']= 'Bestilling av \'%s\' er akseptert';//%s is resource name, status
$lang['calendar']['your_resource_accepted_mail_body']= '%s har akseptert din bestilling av ressursen \'%s\'.'; //First %s is the name of the person who created the event. Second is the calendar name
$lang['calendar']['your_resource_declined_mail_subject']= 'Bestilling av \'%s\' er avvist';//%s is resource name
$lang['calendar']['your_resource_declined_mail_body']= '%s har avvist din bestilling av ressursen \'%s\'.'; //First %s is the name of the person who created the event. Second is the calendar name
$lang['calendar']['birthday_name']='Fødselsdag: {NAME}';
$lang['calendar']['birthday_desc']='{NAME} fyller {AGE} i dag';
$lang['calendar']['unauthorized_participants_write']='Du har ikke tilstrekkelige rettigheter til å registrere avtaler for disse brukerne:<br /><br />{NAMES}<br /><br />I stedet kan du sende dem en invitasjon, slik at de selv kan akseptere og registrere avtalen.';

$lang['calendar']['noCalSelected']= 'Det er ikke valgt noen kalendere for denne oversikten. Velg minst en kalender i Administrasjon.';
$lang['calendar']['month_times'][1]='den første';
$lang['calendar']['month_times'][2]='den andre';
$lang['calendar']['month_times'][3]='den tredje';
$lang['calendar']['month_times'][4]='den fjerde';
$lang['calendar']['month_times'][5]='den femte';
$lang['calendar']['repeats_not_every']= 'Gjentas hver %s %s';
$lang['calendar']['rightClickToCopy']='Høyreklikk for å kopiere lenkelokasjon';
$lang['calendar']['invitation']='Invitasjon';
$lang['calendar']['invitation_update']='Oppdatert invitasjon';
$lang['calendar']['cancellation']='Kansellering';
$lang['calendar']['non_selected']= 'i ikke-valgt kalender';
$lang['calendar']['statuses']['CONFIRMED']= 'Bekreftet';
$lang['calendar']['linkIfCalendarNotSupported']='Bruk bare lenkene nedenfor hvis e-postprogrammet ditt ikke støtter kalenderfunksjoner.';