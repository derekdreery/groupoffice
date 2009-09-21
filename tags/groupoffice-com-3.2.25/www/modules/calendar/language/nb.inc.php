<?php
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('calendar'));
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
$lang['calendar']['accept_mail_body'] = '%s her akseptert din invitasjon til:';

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
$lang['calendar']['accept_confirm']='Eieren vil få beskjed om at du har akseptert hendelsen';

$lang['calendar']['decline_title']='Avvist';
$lang['calendar']['decline_confirm']='Eieren vil få beskjed om at du har avvist hendelsen';

$lang['calendar']['cumulative']='Uguldig gjentagelsesregel. Neste forskomst kan ikke starte før den forrige er ferdig.';

$lang['calendar']['already_accepted']='Du har allerede akseptert denne hendelsen.';

$lang['calendar']['private']='Privat';

$lang['calendar']['import_success']='%s hendelser er importert';

$lang['calendar']['printTimeFormat']='Fra %s til %s';
$lang['calendar']['printLocationFormat']=' på lokasjonen "%s"';
$lang['calendar']['printPage']='Side %s av %s';
$lang['calendar']['printList']='Avtaleoversikt';

$lang['calendar']['printAllDaySingle']='Hele dagen';
$lang['calendar']['printAllDayMultiple']='Hele dagen fra %s til %s';
