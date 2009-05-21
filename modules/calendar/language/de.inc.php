<?php
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('calendar'));
$lang['calendar']['name'] = 'Kalender';
$lang['calendar']['description'] = 'Modul zum Verwalten von Terminen';

$lang['link_type'][1]='Termin';

$lang['calendar']['groupView'] = 'Gruppenansicht';
$lang['calendar']['event']='Ereignis';
$lang['calendar']['startsAt']='Startet am';
$lang['calendar']['endsAt']='Endet am';

$lang['calendar']['exceptionNoCalendarID'] = 'ACHTUNG: Keine Kalender-ID!';
$lang['calendar']['appointment'] = 'Termin: ';
$lang['calendar']['allTogether'] = 'Alle zusammen';

$lang['calendar']['location']='Ort';

$lang['calendar']['invited']='Sie sind zu folgendem Termin eingeladen';
$lang['calendar']['acccept_question']='Möchten Sie diesen Termin akzeptieren?';

$lang['calendar']['accept']='Akzeptieren';
$lang['calendar']['decline']='Ablehnen';

$lang['calendar']['bad_event']='Der Termin ist nicht mehr vorhanden';

$lang['calendar']['subject']='Betreff';
$lang['calendar']['status']='Status';



$lang['calendar']['statuses']['NEEDS-ACTION'] = 'Benötigt Aktion';
$lang['calendar']['statuses']['ACCEPTED'] = 'Akzeptiert';
$lang['calendar']['statuses']['DECLINED'] = 'Abgelehnt';
$lang['calendar']['statuses']['TENTATIVE'] = 'Provisorisch';
$lang['calendar']['statuses']['DELEGATED'] = 'Delegiert';
$lang['calendar']['statuses']['COMPLETED'] = 'Beendet';
$lang['calendar']['statuses']['IN-PROCESS'] = 'In Bearbeitung';


$lang['calendar']['accept_mail_subject'] = 'Einladung für \'%s\' akzeptiert';
$lang['calendar']['accept_mail_body'] = '%s hat ihre Einladung akzeptiert:';

$lang['calendar']['decline_mail_subject'] = 'Einladung für \'%s\' abgelehnt';
$lang['calendar']['decline_mail_body'] = '%s hat Ihre Einladung abgelehnt:';

$lang['calendar']['location']='Ort';
$lang['calendar']['and']='und';

$lang['calendar']['repeats'] = 'Wiederhole alle %s';
$lang['calendar']['repeats_at'] = 'Wiederhole alle %s am %s';//eg. Wiederhole jeden Monat am ersten Montag
$lang['calendar']['repeats_at_not_every'] = 'Wiederhole alle %s %s am %s';//eg. Wiederhole alle 2 Wochen montags
$lang['calendar']['until']='bis'; 

$lang['calendar']['not_invited']='Sie sind zu diesem Termin nicht eingeladen. Melden Sie sich evtl. als ein anderer Benutzer an.';


$lang['calendar']['accept_title']='Akzeptiert';
$lang['calendar']['accept_confirm']='Der Eigentümer wird informiert, dass Sie den Termin akzeptiert haben';

$lang['calendar']['decline_title']='Abgelehnt';
$lang['calendar']['decline_confirm']='Der Eigentümer wird informiert, dass Sie den Termin abgelehnt haben';

$lang['calendar']['cumulative']='Falsche Wiederholung. Die Wiederholung kann nicht starten, bevor die Vorherige beendet ist.';

$lang['calendar']['already_accepted']='Sie haben diesen Termin schon akzeptiert.';

$lang['calendar']['private']='Privat';

$lang['calendar']['import_success']='%s Termine wurden importiert';

$lang['calendar']['printTimeFormat']='Von %s bis %s';
$lang['calendar']['printLocationFormat']=' at location "%s"';
$lang['calendar']['printPage']='Seite %s von %s';
$lang['calendar']['printList']='List of appointments';

$lang['calendar']['printAllDaySingle']='All day';
$lang['calendar']['printAllDayMultiple']='All day from %s till %s';
