<?php
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('calendar'));
$lang['calendar']['name'] = 'Kalender';
$lang['calendar']['description'] = 'Kalender modul; Varje användare kan lägga till, ändra eller radera möten Även möten från andra användare kan visas och vid behov ändras.';

$lang['link_type'][1]= 'Möte';

$lang['calendar']['groupView'] = 'Gruppvy';
$lang['calendar']['event']= 'Händelse';
$lang['calendar']['startsAt']= 'Börjar vid';
$lang['calendar']['endsAt']= 'Slutar vid';

$lang['calendar']['exceptionNoCalendarID'] = 'FATAL: Ingen kalender ID!';
$lang['calendar']['appointment'] = 'Möte:';
$lang['calendar']['allTogether'] = 'Alla tillsammans';

$lang['calendar']['location']= 'Plats';

$lang['calendar']['invited']= 'Du är inbjuden till följande händelse';
$lang['calendar']['acccept_question']= 'Accepterar du denna händelse?';

$lang['calendar']['accept']= 'Acceptera';
$lang['calendar']['decline']= 'Avböj';

$lang['calendar']['bad_event']= 'Händelsen existerar inte längre';

$lang['calendar']['subject']= 'Ämne';
$lang['calendar']['status']= 'Status';



$lang['calendar']['statuses']['NEEDS-ACTION'] = 'Åtgärd krävs';
$lang['calendar']['statuses']['ACCEPTED'] = 'Accepterad';
$lang['calendar']['statuses']['DECLINED'] = 'Avvisad';
$lang['calendar']['statuses']['TENTATIVE'] = 'Tveksam';
$lang['calendar']['statuses']['DELEGATED'] = 'Delegerad';
$lang['calendar']['statuses']['COMPLETED'] = 'Avslutad';
$lang['calendar']['statuses']['IN-PROCESS'] = 'I process';


$lang['calendar']['accept_mail_subject'] = 'Inbjudan till \'%s\' godtogs';
$lang['calendar']['accept_mail_body'] = '%s har accepterat din inbjudan till:';

$lang['calendar']['decline_mail_subject'] = 'Inbjudan till \'%s\' avvisad';
$lang['calendar']['decline_mail_body'] = '% s har avvisat din inbjudan till:';

$lang['calendar']['location']= 'Plats';
$lang['calendar']['and']= 'och';

$lang['calendar']['repeats'] = 'Upprepas varje% s';
$lang['calendar']['repeats_at'] = 'Upprepas varje %s på %s';//eg. Repeats every month at the first monday
$lang['calendar']['repeats_at_not_every'] = 'Upprepas varje %s %s på %s';//eg. Repeats every 2 weeks at monday
$lang['calendar']['until']= 'tills'; 

$lang['calendar']['not_invited']= 'Du var inte inbjuden till denna händelse. Du kanske behöver logga in som en annan användare.';


$lang['calendar']['accept_title']= 'Acceptera';
$lang['calendar']['accept_confirm']= 'Ägaren kommer att meddelas att du accepterat händelsen';

$lang['calendar']['decline_title']= 'Avvisa';
$lang['calendar']['decline_confirm']= 'Ägaren kommer att meddelas att du avböjt händelsen';

$lang['calendar']['cumulative']= 'Ogiltig återkomma regeln. Nästa händelse får inte börja innan den föregående har avslutats.';

$lang['calendar']['already_accepted']= 'Du har redan godkänt denna händelse.';

$lang['calendar']['private']= 'Privat';

$lang['calendar']['import_success']= '%s Händelser importerades';