<?php

//Polish Translation v1.0
//Author : Robert GOLIAT info@robertgoliat.com  info@it-administrator.org
//Date : January, 20 2009

//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('calendar'));

$lang['calendar']['name'] = 'kalendarz';
$lang['calendar']['description'] = 'Moduł Kalendarz; Every user can add, edit or delete appointments Also appointments from other users can be viewed and if necessary it can be changed.';

$lang['link_type'][1]='Termin';

$lang['calendar']['groupView'] = 'Widok grupowy';
$lang['calendar']['event']='Zdarzenie';
$lang['calendar']['startsAt']='Zaczyna się';
$lang['calendar']['endsAt']='Kończy się';

$lang['calendar']['exceptionNoCalendarID'] = 'FATALNY BŁAD: Brak ID kalendarza!';
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


$lang['calendar']['accept_mail_subject'] = 'Zaproszenie dla zostało  \'%s\' zaakceptowane';
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