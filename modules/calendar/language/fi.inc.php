<?php
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('calendar'));
$lang['calendar']['name'] = 'Kalenteri';
$lang['calendar']['description'] = 'Kalenteri moduuli; Jokainen käyttäjä voi lisätä, muokata tai poistaa merkintöjä. Myös muiden kalenterin käyttäjien merkintöjä voidaan selata tai tarvittaessa muuttaa.';

$lang['link_type'][1]='Merkinnät';

$lang['calendar']['groupView'] = 'Ryhmä näkymä';
$lang['calendar']['event']='Tapahtuma';
$lang['calendar']['startsAt']='Alkaa';
$lang['calendar']['endsAt']='Päättyy';

$lang['calendar']['exceptionNoCalendarID'] = 'FATAALI: Ei kalenteri ID:tä!';
$lang['calendar']['appointment'] = 'Merkintä: ';
$lang['calendar']['allTogether'] = 'Kaikki yhdessä';

$lang['calendar']['location']='Sijainti';

$lang['calendar']['invited']='Teidät on kutsuttu seuraavaan tapahtumaan';
$lang['calendar']['acccept_question']='Hyväksytkö tämän tapahtuman?';

$lang['calendar']['accept']='Hyväksy';
$lang['calendar']['decline']='Hylkää';

$lang['calendar']['bad_event']='Tapahtumaa ei enää ole';

$lang['calendar']['subject']='Aihe';
$lang['calendar']['status']='Tila';



$lang['calendar']['statuses']['NEEDS-ACTION'] = 'Odotetaan toimenpiteitä';
$lang['calendar']['statuses']['ACCEPTED'] = 'Hyväksytty';
$lang['calendar']['statuses']['DECLINED'] = 'Hylätty';
$lang['calendar']['statuses']['TENTATIVE'] = 'Alustava';
$lang['calendar']['statuses']['DELEGATED'] = 'Delegoitu';
$lang['calendar']['statuses']['COMPLETED'] = 'Valmis';
$lang['calendar']['statuses']['IN-PROCESS'] = 'Keskeneräinen';


$lang['calendar']['accept_mail_subject'] = 'Kutsu \'%s\' hyväksytty';
$lang['calendar']['accept_mail_body'] = '%s on hyväksynyt kutsunne:';

$lang['calendar']['decline_mail_subject'] = 'Kutsu \'%s\' hylätty';
$lang['calendar']['decline_mail_body'] = '%s on hylännyt kutsunne:';

$lang['calendar']['location']='Sijainti';
$lang['calendar']['and']='ja';

$lang['calendar']['repeats'] = 'Toistuu joka %s';
$lang['calendar']['repeats_at'] = 'Toistuu joka %s aina %s';//eg. Repeats every month at the first monday
$lang['calendar']['repeats_at_not_every'] = 'Toistuu joka %s %s aina %s';//eg. Repeats every 2 weeks at monday
$lang['calendar']['until']='kunnes'; 

$lang['calendar']['not_invited']='Sinua ei kutsuttu tähän tapahtumaan. Ehkä sinun pitää kirjauta toisena käyttäjänä.';


$lang['calendar']['accept_title']='Hyväksytty';
$lang['calendar']['accept_confirm']='Kutsujalle ilmoitetaan, että olet hyväksynyt kutsun.';

$lang['calendar']['decline_title']='Hylätty';
$lang['calendar']['decline_confirm']='Kutsujalle ilmoitetaan, että olet hylännyt kutsun.';

$lang['calendar']['cumulative']='Pätemätön toistumissääntö. Seuraava tapahtuma ei voi alkaa, ennen kuin edellinen on päättynyt.';

$lang['calendar']['already_accepted']='Olet jo hyväksynyt tämän tapahtuman.';

$lang['calendar']['private']='Yksityinen';

$lang['calendar']['import_success']='%s tapahtumia tuotiin.';