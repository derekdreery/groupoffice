<?php
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('calendar'));
$lang['calendar']['name'] = 'Kalendář';
$lang['calendar']['description'] = 'Každý uživatel může přidat, upravit nebo smazat činnosti. Má také možnost prohlížet činnosti od ostatní uživatelů a v případě nutnosti je změnit.';

$lang['link_type'][1]='Činnost';

$lang['calendar']['groupView'] = 'Skupiny';
$lang['calendar']['event']='Událost';
$lang['calendar']['startsAt']='Od';
$lang['calendar']['endsAt']='Do';

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
$lang['calendar']['statuses']['ACCEPTED'] = 'Přijatý';
$lang['calendar']['statuses']['DECLINED'] = 'Odmítnutý';
$lang['calendar']['statuses']['TENTATIVE'] = 'Nezávazný';
$lang['calendar']['statuses']['DELEGATED'] = 'Delegovaný';
$lang['calendar']['statuses']['COMPLETED'] = 'Dokončený';
$lang['calendar']['statuses']['IN-PROCESS'] = 'V procesu';
$lang['calendar']['statuses']['CONFIRMED'] = 'Potvrzený';


$lang['calendar']['accept_mail_subject'] = 'Pozvánka pro \'%s\' byla přijata';
$lang['calendar']['accept_mail_body'] = '%s přijal vaše pozvání na:';

$lang['calendar']['decline_mail_subject'] = 'Pozvánka na \'%s\' byla odtmítnuta';
$lang['calendar']['decline_mail_body'] = '%s nepřijal vaše pozvání na:';

$lang['calendar']['location']='Místo';
$lang['calendar']['and']='a';

$lang['calendar']['repeats'] = 'Opakovat vždy %s';
$lang['calendar']['repeats_at'] = 'Opakovat %s v %s';//eg. Repeats every month at the first monday
$lang['calendar']['repeats_at_not_every'] = 'Opakovat %s %s v %s';//eg. Repeats every 2 weeks at monday
$lang['calendar']['repeats_not_every'] = 'Opakovat %s %s';
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

$lang['calendar']['calendars']='Kalendáře';
$lang['calendar']['resource_mail_subject']='Prostředek \'%s\' byl zamluven'; //%s is resource name
$lang['calendar']['resource_mail_body']='%s vytvořil rezervaci pro prostředek \'%s\'. Vy jste správcem tohoto prostředku. Prosím, otevřete, zakažte nebo schvalte rezervace.'; //First %s is the name of the person who created the event. Second is the calendar name
$lang['calendar']['open_resource']='Otevřená rezervace';
$lang['calendar']['resource_modified_mail_subject']='Rezervace prostředku \'%s\' byla změněna';//%s is resource name
$lang['calendar']['resource_modified_mail_body']='%s změnil rezervace pro prostředek \'%s\'. Vy jste správcem tohoto prostředku. Prosím, otevřete, zakažte nebo schvalte rezervace.'; //First %s is the name of the person who created the event. Second is the calendar name

$lang['calendar']['your_resource_modified_mail_subject']='Vaše rezervace pro \'%s\' ve stavu %s byla změněna';
$lang['calendar']['your_resource_modified_mail_body']='%s byla změněna Vaše rezervace pro prostředek \'%s\'.';
$lang['calendar']['your_resource_accepted_mail_subject'] = 'Rezervace pro \'%s\' byla přijata';//%s is resource name, status
$lang['calendar']['your_resource_accepted_mail_body'] = '%s přijal Vaši rezervaci pro prostředek \'%s\'.'; //First %s is the name of the person who created the event. Second is the calendar name
$lang['calendar']['your_resource_declined_mail_subject'] = 'Rezervace pro \'%s\' byla odmítnuta';//%s is resource name
$lang['calendar']['your_resource_declined_mail_body'] = '%s odmítnul Vaši rezervaci pro prostředek \'%s\'.'; //First %s is the name of the person who created the event. Second is the calendar name

$lang['calendar']['birthday_name']='Narozeniny: {NAME}';
$lang['calendar']['birthday_desc']='{NAME} oslavil dnes {AGE}';

$lang['calendar']['unauthorized_participants_write']='Nemáte dostatečná oprávnění k naplánování událostí pro následující uživatele:<br /><br />{NAMES}<br /><br />Můžete poslat pozvánku, aby mohli událost přijmout a naplánovat.';

$lang['calendar']['noCalSelected'] = 'Nebyly vybráný kalendáře pro tento přehled. Vyberte alespoň jeden kalendář v Administraci.';

$lang['calendar']['month_times'][1]='první';
$lang['calendar']['month_times'][2]='druhý';
$lang['calendar']['month_times'][3]='třetí';
$lang['calendar']['month_times'][4]='čtvrtý';
$lang['calendar']['month_times'][5]='pátý';

$lang['calendar']['rightClickToCopy']='Klikněte pravým tlačítkem pro zkopírování odkazu';

$lang['calendar']['invitation']='Pozvánka';
$lang['calendar']['invitation_update']='Změna pozvánky';
$lang['calendar']['cancellation']='Zrušení';

$lang['calendar']['non_selected'] = 'v nevybraném kalendáři';

$lang['calendar']['linkIfCalendarNotSupported']='Používejte pouze odkazy níže, pokud Váš e-mailový klient nepodporuje funkce kalendáře.';