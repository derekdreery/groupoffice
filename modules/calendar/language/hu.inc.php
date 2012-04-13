<?php

//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('calendar'));

$lang['calendar']['name'] = 'Naptár';
$lang['calendar']['description'] = 'naptár modul; Every user can add, edit or delete appointments Also appointments from other users can be viewed and if necessary it can be changed.';

$lang['link_type'][1]='Esemény';

$lang['calendar']['groupView'] = 'Csoport nézet';
$lang['calendar']['event']='Esemény';
$lang['calendar']['startsAt']='Kezdődik';
$lang['calendar']['endsAt']='Befejeződik';

$lang['calendar']['exceptionNoCalendarID'] = 'HIBA: Nincs naptár ID!';
$lang['calendar']['appointment'] = 'Esemény: ';
$lang['calendar']['allTogether'] = 'Mindenki együtt';

$lang['calendar']['location']='Helyszín';

$lang['calendar']['invited']='Meghívtak a következő eseményre';
$lang['calendar']['acccept_question']='Elfogadod a meghívást?';

$lang['calendar']['accept']='Elfogadom';
$lang['calendar']['decline']='Elutasítom';

$lang['calendar']['bad_event']='Az eseményt eltávolítottam!';

$lang['calendar']['subject']='Tárgy';
$lang['calendar']['status']='Állapot';

$lang['calendar']['statuses']['NEEDS-ACTION'] = 'Tennivaló van';
$lang['calendar']['statuses']['ACCEPTED'] = 'Elfogadva';
$lang['calendar']['statuses']['DECLINED'] = 'Elutasítva';
$lang['calendar']['statuses']['TENTATIVE'] = 'Próba';
$lang['calendar']['statuses']['DELEGATED'] = 'Továbbadva';
$lang['calendar']['statuses']['COMPLETED'] = 'Befejezve';
$lang['calendar']['statuses']['IN-PROCESS'] = 'Folyamatban';

$lang['calendar']['accept_mail_subject'] = '\'%s\' meghívása elfogadva';
$lang['calendar']['accept_mail_body'] = '%s elfogadta a meghívásod a következő eseményre:';

$lang['calendar']['decline_mail_subject'] = '\'%s\' meghívása elutasítva';
$lang['calendar']['decline_mail_body'] = '%s nem fogadta el a meghívásod a következő eseményre:';

$lang['calendar']['location']='Helyszín';
$lang['calendar']['and']='és';

$lang['calendar']['repeats'] = 'Ismétlődés minden %s';
$lang['calendar']['repeats_at'] = 'ismétlődés minden %s %s';//eg. Repeats every month at the first monday
$lang['calendar']['repeats_at_not_every'] = 'Ismétlődés minden %s %s at %s';//eg. Repeats every 2 weeks at monday
$lang['calendar']['until']='amíg'; 

$lang['calendar']['not_invited']='Te nem kaptál meghívást erre az eseményre.';


$lang['calendar']['accept_title']='Elfogadva';
$lang['calendar']['accept_confirm']='A tulajdonos értesítve lesz, hogy elfogadtad a meghívást';

$lang['calendar']['decline_title']='Elutasítva';
$lang['calendar']['decline_confirm']='A tulajdonos értesítve lesz, hogy nem fogadtad el a meghívást';

$lang['calendar']['cumulative']='Rossz ismétlődés-szabály. Egy következő esemény nem kezdődhet az előző befejezése előtt.';

$lang['calendar']['already_accepted']='Már elfogadtad ezt az eseményt';

$lang['calendar']['private']='Személyes';

$lang['calendar']['import_success']='%s eseményei importálva lettek';

$lang['calendar']['printTimeFormat']='%s -tól %s -ig';
$lang['calendar']['printLocationFormat']=' helyszín: "%s"';
$lang['calendar']['printPage']='Oldal %s a %s-ból';
$lang['calendar']['printList']='Teendők listája';

$lang['calendar']['printAllDaySingle']='Egész napos';
$lang['calendar']['printAllDayMultiple']='Egész napos %s -tól %s -ig';
?>