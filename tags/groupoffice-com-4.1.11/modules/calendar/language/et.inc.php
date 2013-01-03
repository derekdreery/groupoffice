<?php
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('calendar'));
$lang['calendar']['name'] = 'Kalender';
$lang['calendar']['description'] = 'Kalendri moodul; Kõik kasutajad saavad kas lisada, muuta või kustutada sündmusi. Ka teiste sisestatud sündmusi on võimalik vaadata ja vajadusel muuta.';

$lang['link_type'][1]='Kohtumine';

$lang['calendar']['groupView'] = 'Grupi vaade';
$lang['calendar']['event']='Sündmus';
$lang['calendar']['startsAt']='Algab';
$lang['calendar']['endsAt']='Lõppeb';

$lang['calendar']['exceptionNoCalendarID'] = 'VIGA: puudub kalendri ID!';
$lang['calendar']['appointment'] = 'Kohtumine: ';
$lang['calendar']['allTogether'] = 'Kõik koos';

$lang['calendar']['location']='Asukoht';

$lang['calendar']['invited']='Oled kutsutud järgmisele sündmusele';
$lang['calendar']['acccept_question']='kas võtad kutse vastu?';

$lang['calendar']['accept']='Võtan vastu';
$lang['calendar']['decline']='Lükkan tagasi';

$lang['calendar']['bad_event']='Sündmust ei eksisteeri enam';

$lang['calendar']['subject']='Teema';
$lang['calendar']['status']='Staatus';



$lang['calendar']['statuses']['NEEDS-ACTION'] = 'Vajab tegevust';
$lang['calendar']['statuses']['ACCEPTED'] = 'Vastu võetud';
$lang['calendar']['statuses']['DECLINED'] = 'Tagasi lükatud';
$lang['calendar']['statuses']['TENTATIVE'] = 'Esialgne';
$lang['calendar']['statuses']['DELEGATED'] = 'Deleeeritud';
$lang['calendar']['statuses']['COMPLETED'] = 'Lõpetatud';
$lang['calendar']['statuses']['IN-PROCESS'] = 'Pooleli';


$lang['calendar']['accept_mail_subject'] = 'Kutse \'%s\'-le vastu võetud';
$lang['calendar']['accept_mail_body'] = '%s võttis vastu kutse:';

$lang['calendar']['decline_mail_subject'] = 'Kutse \'%s\' jaoks tagasi lükatud';
$lang['calendar']['decline_mail_body'] = '%s lükkas tagasi kutse:';

$lang['calendar']['location']='Asukoht';
$lang['calendar']['and']='ja';

$lang['calendar']['repeats'] = 'Kordub iga %s';
$lang['calendar']['repeats_at'] = 'Kordub iga %s  igal %s';//eg. Repeats every month at the first monday
$lang['calendar']['repeats_at_not_every'] = 'Kordub iga %s %s igal %s';//eg. Repeats every 2 weeks at monday
$lang['calendar']['until']='kuni'; 

$lang['calendar']['not_invited']='Sind ei ole sellele sündmusele kutsutud. Ilmselt pead sisse logima teise kasutajana.';


$lang['calendar']['accept_title']='Vastu võetud';
$lang['calendar']['accept_confirm']='Omanikku teavitatakse sündmuse vastu võtmisest';

$lang['calendar']['decline_title']='tagasi lükatud';
$lang['calendar']['decline_confirm']='Omanikku teavitatakse sündmuse tagasi lükkamisest';

$lang['calendar']['cumulative']='Vigane korduv seos- ei tohi alata enne kui eelmine on lõppenud.';

$lang['calendar']['already_accepted']='Selle sündmuse oled juba vastu võtnud.';

$lang['calendar']['private']='Isiklik';

$lang['calendar']['import_success']='%s sündmust imporditi';

$lang['calendar']['printTimeFormat']='Alates %s kuni %s';
$lang['calendar']['printLocationFormat']=' asukohas "%s"';
$lang['calendar']['printPage']='Lehekülg %s  %s -st';
$lang['calendar']['printList']='Kohtumiste nimekiri';

$lang['calendar']['printAllDaySingle']='Terve päev';
$lang['calendar']['printAllDayMultiple']='terve päev alates %s kuni %s';

$lang['calendar']['calendars']='Kalendrid';

$lang['calendar']['open_resource']='Avatud broneerimine';

$lang['calendar']['resource_mail_subject']='Ressurss \'%s\' broneeritud \'%s\' jaoks \'%s\'';//%s is resource name, %s is event name, %s is start date
$lang['calendar']['resource_mail_body']='%s hbroneeris ressursi \'%s\' jaoks. Sina oled selle ressursi hooldaja. Kinnitamiseks või tagasi lükkamiseks ava palun broneering.'; //First %s is the name of the person who created the event. Second is the calendar name

$lang['calendar']['resource_modified_mail_subject']='Ressurss \'%s\' broneeritud \'%s\' jaoks \'%s\' muudetud';//%s is resource name, %s is event name, %s is start date
$lang['calendar']['resource_modified_mail_body']='%s muutis broneeringut \'%s\' jaoks. Sina oled selle ressursi hooldaja. Kinnitamiseks või tagasi lükkamiseks ava palun broneering.'; //First %s is the name of the person who created the event. Second is the calendar name

$lang['calendar']['your_resource_modified_mail_subject']='Sinu broneeringgut \'%s\' jaoks \'%s\' staatusega \'%s\' on muudetud';//is resource name, %s is event name, %s is start date
$lang['calendar']['your_resource_modified_mail_body']='%s muutis sinu \'%s\' jaoks tehtud broneeringut.';

$lang['calendar']['your_resource_accepted_mail_subject']='Sinu poolt \'%s\' jaoks tehtav broneering \'%s\' on kinnitatud';//%s is resource name, %s is start date
$lang['calendar']['your_resource_accepted_mail_body']='%s kinnitas \'%s\' jaoks tehtava broneeringu.'; //First %s is the name of the person who created the event. Second is the calendar name

$lang['calendar']['your_resource_declined_mail_subject']='Sinu poolt \'%s\' jaoks tehtav broneering \'%s\' on tagasi lükatud';//%s is resource name, %s is start date
$lang['calendar']['your_resource_declined_mail_body']='%s lükkas  \'%s\' jaoks tehtava broneeringu tagasi.'; //First %s is the name of the person who created the event. Second is the calendar name

$lang['calendar']['birthday_name']='Sünnipäev: {NAME}';
$lang['calendar']['birthday_desc']='{NAME} sai täna {AGE} aastaseks';

$lang['calendar']['unauthorized_participants_write']='Sul ei ole piisavalt õigusi alljärgnevate kasutajate jaoks broneeringute tegemiseks:<br /><br />{NAMES}<br /><br />';

$lang['calendar']['noCalSelected'] = 'Ülevaatamiseks ei ole valitud ühtegi kalendrit. Vali Seaded alt vähemalt üks kalender.';

$lang['calendar']['month_times'][1]='esimene';
$lang['calendar']['month_times'][2]='teine';
$lang['calendar']['month_times'][3]='kolmas';
$lang['calendar']['month_times'][4]='neljas';
$lang['calendar']['month_times'][5]='viies';

$lang['calendar']['repeats_not_every']= 'Kordub iga %s %s';
$lang['calendar']['rightClickToCopy']='Asukoha kopeerimiseks tee paremklikk';
$lang['calendar']['invitation']='Kutse';
$lang['calendar']['invitation_update']='Uuendatud kutse';
$lang['calendar']['cancellation']='Tühistamine';
$lang['calendar']['non_selected']= 'mitte valitud kalendris';