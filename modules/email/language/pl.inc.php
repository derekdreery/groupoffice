<?php

//Polish Translation v1.0
//Author : Robert GOLIAT info@robertgoliat.com  info@it-administrator.org
//Date : January, 20 2009

//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('email'));

$lang['email']['name'] = 'Email';
$lang['email']['description'] = 'Moduł E-mail; Small webbased e-mail client. Every user will be able to sent, receive and forward emails';

$lang['link_type'][9]='E-mail';

$lang['email']['feedbackNoReciepent'] = 'Nie wprowadzono odbiorcy';
$lang['email']['feedbackSMTPProblem'] = 'Wystąpił problem podczas komunikacji SMTP: ';
$lang['email']['feedbackUnexpectedError'] = 'Wystąpił niespodziewany problem podczas tworzenia e-mail: ';
$lang['email']['feedbackCreateFolderFailed'] = 'Nie można utworzyć folderu';
$lang['email']['feedbackDeleteFolderFailed'] = 'Nie mozna usunąc folderu';
$lang['email']['feedbackSubscribeFolderFailed'] = 'Nie można zasubskrybować folderu';
$lang['email']['feedbackUnsubscribeFolderFailed'] = 'Nie można wyłączyć subskrypcji folderu';
$lang['email']['feedbackCannotConnect'] = 'Nie mogę połączyć się z %1$s po porcie %3$s<br /><br />Serwer pocztowy zwrócił odpoiwedź: %2$s';
$lang['email']['inbox'] = 'Skrzynka odbiorcza';

$lang['email']['spam']='Spam';
$lang['email']['trash']='Trash';
$lang['email']['sent']='Wysłane';
$lang['email']['drafts']='Szkice';

$lang['email']['no_subject']='Brak tematu';
$lang['email']['to']='Do';
$lang['email']['from']='Od';
$lang['email']['subject']='Temat';
$lang['email']['no_recipients']='Undisclosed recipients';
$lang['email']['original_message']='--- Original message follows ---';
$lang['email']['attachments']='Załączniki';

$lang['email']['notification_subject']='Przeczytano: %s';
$lang['email']['notification_body']='Twoja przesyłka o temacie "%s" została wyswietlona u %s';

$lang['email']['errorGettingMessage']='Nie można pobrać danych z serwera';
$lang['email']['no_recipients_drafts']='Brak odbiorców';
$lang['email']['usage_limit'] = 'Używane %s z %s';
$lang['email']['usage'] = 'Uzywane %s';

$lang['email']['event']='Termin';
$lang['email']['calendar']='kalendarz';
