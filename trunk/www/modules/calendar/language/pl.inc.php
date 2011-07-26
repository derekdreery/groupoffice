<?php
//Polish Translation v1.0
//Author : Robert GOLIAT info@robertgoliat.com  info@it-administrator.org
//Date : January, 20 2009
//Polish Translation v1.1
//Author : Paweł Dmitruk pawel.dmitruk@gmail.com
//Date : September, 04 2010
//Polish Translation v1.2
//Author : rajmund
//Date : January, 26 2011
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('calendar'));
$lang['calendar']['name'] = 'kalendarz';
$lang['calendar']['description'] = 'Moduł Kalendarz; Każdy użytkownik może dodawać, edytować lub usuwać terminy spotkań. Można także przeglądać terminy innych użytkowników i w razie potrzeby zmieniać je.';
$lang['link_type'][1]='Termin';
$lang['calendar']['groupView'] = 'Widok grupowy';
$lang['calendar']['event']='Zdarzenie';
$lang['calendar']['startsAt']='Zaczyna się';
$lang['calendar']['endsAt']='Kończy się';
$lang['calendar']['exceptionNoCalendarID'] = 'FATALNY BŁAD: Brak identyfikatora kalendarza!';
$lang['calendar']['appointment'] = 'Termin: ';
$lang['calendar']['allTogether'] = 'Wszystko razem';
$lang['calendar']['location']='Lokalizacja';
$lang['calendar']['invited']='Zaproszono Cię do następującego zdarzenia';
$lang['calendar']['acccept_question']='Akceptujesz to zdarzenie?';
$lang['calendar']['accept']='Akceptuj';
$lang['calendar']['decline']='Odrzuć';
$lang['calendar']['bad_event']='Zdarzenie nie bedzie miało nigdy miejsca.';
$lang['calendar']['subject']='Temat';
$lang['calendar']['status']='Status';
$lang['calendar']['statuses']['NEEDS-ACTION'] = 'Wymaga akcji';
$lang['calendar']['statuses']['ACCEPTED'] = 'Zaakceptowane';
$lang['calendar']['statuses']['DECLINED'] = 'Odrzucone';
$lang['calendar']['statuses']['TENTATIVE'] = 'Próbne';
$lang['calendar']['statuses']['DELEGATED'] = 'Oddelegowane';
$lang['calendar']['statuses']['COMPLETED'] = 'Wykonane';
$lang['calendar']['statuses']['IN-PROCESS'] = 'W trakcie';
$lang['calendar']['accept_mail_subject'] = 'Zaproszenie dla \'%s\' zostało zaakceptowane';
$lang['calendar']['accept_mail_body'] = 'Użytkownik %s zaakceptował Twoje zaproszenie do:';
$lang['calendar']['decline_mail_subject'] = 'Zaproszenie dlo \'%s\' zostało odrzucone';
$lang['calendar']['decline_mail_body'] = 'Użytkowik %s odrzucił Twoje zaproszenie do:';
$lang['calendar']['location']='Lokalizacja';
$lang['calendar']['and']='i';
$lang['calendar']['repeats'] = 'Powtarza się co %s';
$lang['calendar']['repeats_at'] = 'Powtarza się co %s w %s';//eg. Repeats every month at the first monday
$lang['calendar']['repeats_at_not_every'] = 'Powtarza się co %s %s w %s';//eg. Repeats every 2 weeks at monday
$lang['calendar']['until']='dopóki'; 
$lang['calendar']['not_invited']='Nie ma dla Ciebie zaproszenia do tego zdarzenia. Może zaloguj się jako inny użytkownik.';
$lang['calendar']['accept_title']='Zaakceptowany';
$lang['calendar']['accept_confirm']='Własciciel zostanie powiadomiony o Twojej akceptacji zdarzenia';
$lang['calendar']['decline_title']='Odrzucony';
$lang['calendar']['decline_confirm']='Własciciel zostanie powiadomiony o Twoim odrzuceniu zdarzenia';
$lang['calendar']['cumulative']='Niepoprawna zasada powtarzania. Kolejne wystąpienie nie może byc wczesniejsze zanim kolejne nie zostanie zakończone.';
$lang['calendar']['already_accepted']='Zaakceptowałeś/aś juz to zaproszenie.';
$lang['calendar']['private']='Prywatne';
$lang['calendar']['import_success']='%s zdarzeń zostało zaimportowanych';
$lang['calendar']['printTimeFormat']='Od %s do %s';
$lang['calendar']['printLocationFormat']=' w lokalizaci "%s"';
$lang['calendar']['printPage']='Strona %s z %s';
$lang['calendar']['printList']='Lista terminów';
$lang['calendar']['printAllDaySingle']='Wszystkie dni';
$lang['calendar']['printAllDayMultiple']='Wszystkie dni od %s do %s';
$lang['calendar']['calendars']='Kalendarze';
$lang['calendar']['open_resource']='Otwarta rezerwacja';
$lang['calendar']['resource_mail_subject']='Kalendarz \'%s\' zarezerwowany dla \'%s\' od \'%s\'';//%s is resource name, %s is event name, %s is start date
$lang['calendar']['resource_mail_body']='%s utworzył rezerwację w kalendarzu \'%s\'. Jesteś opiekunem tego kalendarza. Prosze otworzyć rezerwację w celu odrzucenia lub zaakceptowania tego.'; //First %s is the name of the person who created the event. Second is the calendar name
$lang['calendar']['resource_modified_mail_subject']='Kalendarz \'%s\' zarezerwowany dla \'%s\' od \'%s\' modified';//%s is resource name, %s is event name, %s is start date
$lang['calendar']['resource_modified_mail_body']='%s zmodyfikował rezerwację w kalendarzu \'%s\'. Jesteś opiekunem tego kalendarza. Prosze otworzyć rezerwację w celu odrzucenia lub zaakceptowania tego.'; //First %s is the name of the person who created the event. Second is the calendar name
$lang['calendar']['your_resource_modified_mail_subject']='Twoja rezerwacja dla \'%s\' w \'%s\' o statusie \'%s\' została zmodyfikowana';//is resource name, %s is event name, %s is start date
$lang['calendar']['your_resource_modified_mail_body']='%s zmodyfikował Twoją rezerwację w kalendarzu \'%s\'.';
$lang['calendar']['your_resource_accepted_mail_subject']='Rezerwacja dla \'%s\' w \'%s\' została zaakceptowana';//%s is resource name, %s is start date
$lang['calendar']['your_resource_accepted_mail_body']='%s zaakceptował Twoją rezerwację w kalendarzu \'%s\'.'; //First %s is the name of the person who created the event. Second is the calendar name
$lang['calendar']['your_resource_declined_mail_subject']='Twoja rezerwacja dla \'%s\' w \'%s\' została odrzucona';//%s is resource name, %s is start date
$lang['calendar']['your_resource_declined_mail_body']='%s odrzucił Twoją rezerwację w kalendarzu \'%s\'.'; //First %s is the name of the person who created the event. Second is the calendar name
$lang['calendar']['birthday_name']='Urodziny: {NAME}';
$lang['calendar']['birthday_desc']='{NAME} kończy dzisiaj {AGE} lat';
$lang['calendar']['unauthorized_participants_write']='Nie masz wystarczających uprawnień do planowania spotkań dla użytkowników:<br /><br />{NAMES}<br /><br />Możesz im wysłać zaproszenie, aby mogli je zaakceptować i dopisać do swojego kalendarza.';
$lang['calendar']['noCalSelected']= 'Nie wybrano żadnego kalendarza do tego zestawienia. Prosze wybrać przynajmniej jeden kalendarz';
$lang['calendar']['month_times'][1]='pierwszy';
$lang['calendar']['month_times'][2]='drugi';
$lang['calendar']['month_times'][3]='trzeci';
$lang['calendar']['month_times'][4]='czwarty';
$lang['calendar']['month_times'][5]='piąty';
$lang['calendar']['statuses']['CONFIRMED']= 'Potwierdzone';
$lang['calendar']['repeats_not_every']= 'Powtarzanie co %s %s';
$lang['calendar']['rightClickToCopy']='Kliknij prawym, aby skopiować adres odnośnika';
$lang['calendar']['invitation']='Zaproszenie';
$lang['calendar']['invitation_update']='Zaktualizowane zaproszenie';
$lang['calendar']['cancellation']='Anulowanie';
$lang['calendar']['non_selected']= 'w niezaznaczonym kalendarzu';
$lang['calendar']['linkIfCalendarNotSupported']='Only use the links below if your mail client does not support calendaring functions.';