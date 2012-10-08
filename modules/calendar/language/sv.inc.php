<?php
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('calendar'));
$lang['calendar']['name'] = 'Kalender';
$lang['calendar']['description'] = 'Modul som ger användare tillgång till en eller flera kalendrar. Kalendrar kan även delas mellan användare eller grupper.';

$lang['link_type'][1]= 'Möte';

$lang['calendar']['groupView'] = 'Gruppvy';
$lang['calendar']['event']= 'Händelse';
$lang['calendar']['startsAt']= 'Börjar vid';
$lang['calendar']['endsAt']= 'Slutar vid';

$lang['calendar']['exceptionNoCalendarID'] = 'STOP: Inget kalender-ID!';
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
$lang['calendar']['statuses']['TENTATIVE'] = 'Preliminär';
$lang['calendar']['statuses']['DELEGATED'] = 'Delegerad';
$lang['calendar']['statuses']['COMPLETED'] = 'Avslutad';
$lang['calendar']['statuses']['IN-PROCESS'] = 'Pågående';
$lang['calendar']['statuses']['CONFIRMED'] = 'Bekräftad';


$lang['calendar']['accept_mail_subject'] = 'Inbjudan till \'%s\' accepterad';
$lang['calendar']['accept_mail_body'] = '%s har accepterat din inbjudan till:';

$lang['calendar']['decline_mail_subject'] = 'Inbjudan till \'%s\' avböjd';
$lang['calendar']['decline_mail_body'] = '% s har avböjt din inbjudan till:';

$lang['calendar']['location']= 'Plats';
$lang['calendar']['and']= 'och';

$lang['calendar']['repeats'] = 'Upprepas varje %s';
$lang['calendar']['repeats_at'] = 'Upprepas varje %s på %sen';//t.ex. Upprepas varje månad på den första Måndagen
$lang['calendar']['repeats_at_not_every'] = 'Upprepas med %s %ss mellanrum på %sar';//t.ex. Upprepas med 2 veckors mellanrum på Måndagar
$lang['calendar']['repeats_not_every'] = 'Upprepas varje %s %s';
$lang['calendar']['until']= 'tills'; 

$lang['calendar']['not_invited']= 'Du är inte inbjuden till den här händelsen. Du kanske behöver logga in som en annan användare.';


$lang['calendar']['accept_title']= 'Acceptera';
$lang['calendar']['accept_confirm']= 'Ägaren kommer meddelas att du accepterat händelsen';

$lang['calendar']['decline_title']= 'Avböj';
$lang['calendar']['decline_confirm']= 'Ägaren kommer meddelas att du avböjt händelsen';

$lang['calendar']['cumulative']= 'Ogiltig regel för upprepning. Nästa händelse kan inte börja innan den föregående har avslutats.';

$lang['calendar']['already_accepted']= 'Du har redan accepterat den här händelsen.';

$lang['calendar']['private']= 'Privat';

$lang['calendar']['import_success']= '%s händelser importerades';

$lang['calendar']['printTimeFormat']='Från %s till %s';
$lang['calendar']['printLocationFormat']=' på platsen "%s"';
$lang['calendar']['printPage']='Sida %s av %s';
$lang['calendar']['printList']='Lista med möten';

$lang['calendar']['printAllDaySingle']='Hela dagen';
$lang['calendar']['printAllDayMultiple']='Hela dagen från %s till %s';

$lang['calendar']['calendars']='Kalendrar';

$lang['calendar']['open_resource']='Öppna bokning';

$lang['calendar']['resource_mail_subject']='Resursen \'%s\' bokades för \'%s\' på \'%s\'';//%s is resource name, %s is event name, %s is start date
$lang['calendar']['resource_mail_body']='%s har bokat resursen \'%s\'. Du är ansvarig för den här resursen. Vänligen öppna bokningen för att godkänna eller neka den.'; //First %s is the name of the person who created the event. Second is the calendar name

$lang['calendar']['resource_modified_mail_subject']='Resursen \'%s\' som bokats för \'%s\' på \'%s\' har ändrats';//%s is resource name, %s is event name, %s is start date
$lang['calendar']['resource_modified_mail_body']='%s har ändrat en bokning av resursen \'%s\'. Du är ansvarig för den här resursen. Vänligen öppna bokningen för att godkänna eller neka den.'; //First %s is the name of the person who created the event. Second is the calendar name

$lang['calendar']['your_resource_modified_mail_subject']='Din bokning av \'%s\' för \'%s\' på \'%s\' har ändrats';//is resource name, %s is event name, %s is start date
$lang['calendar']['your_resource_modified_mail_body']='%s har ändrat din bokning av resursen \'%s\'.';

$lang['calendar']['your_resource_accepted_mail_subject']='Din bokning av \'%s\' på \'%s\' har accepterats';//%s is resource name, %s is start date
$lang['calendar']['your_resource_accepted_mail_body']='%s har accepterat din bokning av resursen \'%s\'.'; //First %s is the name of the person who created the event. Second is the calendar name

$lang['calendar']['your_resource_declined_mail_subject']='Din bokning av \'%s\' på \'%s\' har nekats';//%s is resource name, %s is start date
$lang['calendar']['your_resource_declined_mail_body']='%s har nekat din bokning av resursen \'%s\'.'; //First %s is the name of the person who created the event. Second is the calendar name

$lang['calendar']['birthday_name']='Födelsedag: {NAME}';
$lang['calendar']['birthday_desc']='{NAME} har fyllt {AGE} idag';

$lang['calendar']['unauthorized_participants_write']='Du har otillräcklig behörighet för att schemalägga möten för följande användare:<br /><br />{NAMES}<br /><br />Du kan istället skicka en inbjudan så användaren själv kan acceptera och schemalägga mötet.';

$lang['calendar']['noCalSelected'] = 'Ingen kalender har valts för den här översikten. Välj minst en kalender under Administration.';

$lang['calendar']['month_times'][1]='den första';
$lang['calendar']['month_times'][2]='den andra';
$lang['calendar']['month_times'][3]='den tredje';
$lang['calendar']['month_times'][4]='den fjärde';
$lang['calendar']['month_times'][5]='den femte';

$lang['calendar']['rightClickToCopy']='Högerklicka för att kopiera länkadressen';

$lang['calendar']['invitation']='Inbjudan';
$lang['calendar']['invitation_update']='Uppdaterad inbjudan';
$lang['calendar']['cancellation']='Kancellering';

$lang['calendar']['non_selected'] = 'i icke-vald kalender';

$lang['calendar']['linkIfCalendarNotSupported']='Använd länkarna nedan endast om din mejlklient saknar stöd för kalenderfunktioner.';
