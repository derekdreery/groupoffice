<?php
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('calendar'));
$lang['calendar']['name'] = 'Kalender';
$lang['calendar']['description'] = 'Kalender modul. Alle brugere kan tilføje, redigere og slette aftaler. Aftaler tilhørende andre brugere kan ses og om nødvendigt ændres.';

$lang['link_type'][1]='Aftale';

$lang['calendar']['groupView'] = 'Gruppe visning';
$lang['calendar']['event']='Hændelse';
$lang['calendar']['startsAt']='Starter';
$lang['calendar']['endsAt']='Slutter';

$lang['calendar']['exceptionNoCalendarID'] = 'FATALT: Ingen kalender ID!';
$lang['calendar']['appointment'] = 'Aftale: ';
$lang['calendar']['allTogether'] = 'Alle sammen';

$lang['calendar']['location']='Lokation';

$lang['calendar']['invited']='Du er inviteret til følgende hændelse';
$lang['calendar']['acccept_question']='Accepterer du denne hændelse?';

$lang['calendar']['accept']='Accepter';
$lang['calendar']['decline']='Afvis';

$lang['calendar']['bad_event']='Hændelsen eksisterer ikke mere';

$lang['calendar']['subject']='Emne';
$lang['calendar']['status']='Status';



$lang['calendar']['statuses']['NEEDS-ACTION'] = 'Afventer reaktion';
$lang['calendar']['statuses']['ACCEPTED'] = 'Accepteret';
$lang['calendar']['statuses']['DECLINED'] = 'Afvist';
$lang['calendar']['statuses']['TENTATIVE'] = 'Foreløbig';
$lang['calendar']['statuses']['DELEGATED'] = 'Delegeret';
$lang['calendar']['statuses']['COMPLETED'] = 'Fuldført';
$lang['calendar']['statuses']['IN-PROCESS'] = 'Igangværende';


$lang['calendar']['accept_mail_subject'] = 'Invitation til \'%s\' accepteret';
$lang['calendar']['accept_mail_body'] = '%s har accepteret din invitation til:';

$lang['calendar']['decline_mail_subject'] = 'Invitation til \'%s\' afvist';
$lang['calendar']['decline_mail_body'] = '%s har afvist din invitation til:';

$lang['calendar']['location']='Lokation';
$lang['calendar']['and']='og';

$lang['calendar']['repeats'] = 'Gentages hver %s';
$lang['calendar']['repeats_at'] = 'Gentages hver %s på %s';//eks. Gentages hver måned på den første mandag
$lang['calendar']['repeats_at_not_every'] = 'Gentages hver %s %s på %s';//eks. Gentages hver anden uge på mandage
$lang['calendar']['until']='indtil'; 

$lang['calendar']['not_invited']='Du er ikke inviteret til denne hændelse. Du skal måske logge ind som en anden bruger.';


$lang['calendar']['accept_title']='Accepteret';
$lang['calendar']['accept_confirm']='Ejeren underrettes om at du accepterede denne hændelse';

$lang['calendar']['decline_title']='Afvist';
$lang['calendar']['decline_confirm']='Ejeren underrettes om at du afviste denne hændelse';

$lang['calendar']['cumulative']='Ugyldig gentagelses regel. Næste hændelse kan ikke starte før den forrige er afsluttet.';

$lang['calendar']['already_accepted']='Du har allerede accepteret denne hændelse.';

$lang['calendar']['private']='Privat';

$lang['calendar']['import_success']='%s hændelser blev importeret';