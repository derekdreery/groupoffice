<?php
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('calendar'));
$lang['calendar']['name'] = 'Kalendář';
$lang['calendar']['description'] = 'Každý uživatel může přidat, upravit nebo smazat činnosti. Má také možnost prohlížet činnosti od ostatní uživatelů a v případě nutnosti je změnit.';

$lang['link_type'][1]='Činnost';

$lang['calendar']['groupView'] = 'Skupiny';
$lang['calendar']['event']='Událost';
$lang['calendar']['startsAt']='Začátek v';
$lang['calendar']['endsAt']='Konec v';

$lang['calendar']['exceptionNoCalendarID'] = 'CHYBA: Kalendář nemá ID!';
$lang['calendar']['appointment'] = 'Činnost: ';
$lang['calendar']['allTogether'] = 'Všechny dohromady';

$lang['calendar']['location']='Místo';

$lang['calendar']['invited']='Jste pozváni na následující akce';
$lang['calendar']['acccept_question']='Chcete přijmout tuto akci?';

$lang['calendar']['accept']='Přijmout';
$lang['calendar']['decline']='Odmítnout';

$lang['calendar']['bad_event']='Akce již neexistuje';

$lang['calendar']['subject']='Předmět';
$lang['calendar']['status']='Stav';



$lang['calendar']['statuses']['NEEDS-ACTION'] = 'Vyžaduje akci';
$lang['calendar']['statuses']['ACCEPTED'] = 'Přijat';
$lang['calendar']['statuses']['DECLINED'] = 'Odmítnutý';
$lang['calendar']['statuses']['TENTATIVE'] = 'Nezávazný';
$lang['calendar']['statuses']['DELEGATED'] = 'Delegovaný';
$lang['calendar']['statuses']['COMPLETED'] = 'Dokončený';
$lang['calendar']['statuses']['IN-PROCESS'] = 'V procesu';


$lang['calendar']['accept_mail_subject'] = 'Pozvánka pro \'%s\' byla přijata';
$lang['calendar']['accept_mail_body'] = '%s přijal vaše pozvání na:';

$lang['calendar']['decline_mail_subject'] = 'Pozvánka na \'%s\' byla odtmítnuta';
$lang['calendar']['decline_mail_body'] = '%s nepřijal vaše pozvání na:';

$lang['calendar']['location']='Místo';
$lang['calendar']['and']='a';

$lang['calendar']['repeats'] = 'Opakovat vždy %s';
$lang['calendar']['repeats_at'] = 'Opakovat %s v %s';//eg. Repeats every month at the first monday
$lang['calendar']['repeats_at_not_every'] = 'Opakovat %s %s v %s';//eg. Repeats every 2 weeks at monday
$lang['calendar']['until']='do'; 

$lang['calendar']['not_invited']='Nebyli jste pozváni na tuto akci. Budete se muset přihlásit jako jiný uživatel.';


$lang['calendar']['accept_title']='Přijat';
$lang['calendar']['accept_confirm']='Autor bude obeznámen o Vašem přijmutí akce';

$lang['calendar']['decline_title']='Odmítnutý';
$lang['calendar']['decline_confirm']='Autor bude obeznámen o Vašem odmítnutí akce';

$lang['calendar']['cumulative']='Neplatné opakování. Další činnost nesmí být zahájena dříve, než předchozí skončí.';

$lang['calendar']['already_accepted']='Již byla potvrzena tato údalost.';

$lang['calendar']['private']='Osobní';

$lang['calendar']['import_success']='%s událostí bylo importováno';

$lang['calendar']['printTimeFormat']='Od %s dp %s';
$lang['calendar']['printLocationFormat']=' v umístění "%s"';
$lang['calendar']['printPage']='Strana %s z %s';
$lang['calendar']['printList']='Seznam událostí';

$lang['calendar']['printAllDaySingle']='Celý den';
$lang['calendar']['printAllDayMultiple']='Celý den od %s do %s';
