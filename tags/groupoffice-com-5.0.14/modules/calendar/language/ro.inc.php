<?php
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('calendar'));
$lang['calendar']['name'] = 'Calendar';
$lang['calendar']['description'] = 'Modul calendar; Fiecate user poate adiţiona, modifica sau şterge evenimentele. Şi evenimentele altora pot fi văzute sau modificate dacă este necesar.';

$lang['calendar']['already_accepted']='Ai deja acceptat acest eveniment.';
$lang['calendar']['private']='Privat';

$lang['link_type'][1]='Eveniment';

$lang['calendar']['groupView'] = 'Atată în grupuri';
$lang['calendar']['event']='Eveniment';
$lang['calendar']['startsAt']='Începe la';
$lang['calendar']['endsAt']='termină la';

$lang['calendar']['exceptionNoCalendarID'] = 'EROARE: Nici un ID de calendar!';
$lang['calendar']['appointment'] = 'Întîlnire: ';
$lang['calendar']['allTogether'] = 'Tuţi împreună';

$lang['calendar']['location']='Locul';

$lang['calendar']['invited']='Eşti invitat la acest eveniment';
$lang['calendar']['acccept_question']='Accepţi acest eveniment?';

$lang['calendar']['accept']='Acceptă';
$lang['calendar']['decline']='Refuză';

$lang['calendar']['bad_event']='Evenimentul nu mai există';

$lang['calendar']['subject']='Subiectul';
$lang['calendar']['status']='Statut';



$lang['calendar']['statuses']['NEEDS-ACTION'] = 'Trebuie intervenit';
$lang['calendar']['statuses']['ACCEPTED'] = 'Acceptat';
$lang['calendar']['statuses']['DECLINED'] = 'Refuzat';
$lang['calendar']['statuses']['TENTATIVE'] = 'Tentative';
$lang['calendar']['statuses']['DELEGATED'] = 'Delegat';
$lang['calendar']['statuses']['COMPLETED'] = 'Complectat';
$lang['calendar']['statuses']['IN-PROCESS'] = 'În elaborare';


$lang['calendar']['accept_mail_subject'] = 'Invitaţie pentru \'%s\' acceptată';
$lang['calendar']['accept_mail_body'] = '%s a acceptat invitaţia ta pentru:';

$lang['calendar']['decline_mail_subject'] = 'Invitaţia pentru \'%s\' refuzată';
$lang['calendar']['decline_mail_body'] = '%s a refuzat invitaţia pentru:';

$lang['calendar']['location']='Loc';
$lang['calendar']['and']='şi';

$lang['calendar']['repeats'] = 'Repetă în fiecare %s';
$lang['calendar']['repeats_at'] = 'Repetă în fiecare %s la %s';//eg. Repetă în fiecare Luni din lună
$lang['calendar']['repeats_at_not_every'] = 'Repetă în fiecare %s %s la %s';//eg. Repetă Lunedì în fiecare 2 săptămîni
$lang['calendar']['until']='până când'; 

$lang['calendar']['not_invited']='Nu eşti invitat la acest eveniment. Poate fi necesar accesul cu un alt nume.';


$lang['calendar']['accept_title']='Acceptat';
$lang['calendar']['accept_confirm']='Proprietarul va fi avertizat căci ai acceptat evenimentul';

$lang['calendar']['decline_title']='Refuzat';
$lang['calendar']['decline_confirm']='Proprietarul va fi avertizat căci ai refuzat evenimentul';

$lang['calendar']['cumulative']='Regulă de repetare eronată. Următorul eveniment nu poate începe înainte de a fi terminat cel precedent.';
