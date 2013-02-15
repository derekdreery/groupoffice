<?php
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('email'));
$lang['email']['name'] = 'Email';
$lang['email']['description'] = 'Modul e-mail; Client e-mail bazat pe web. Fiecare utilizator poate să trimită, primească e-mail';

$lang['link_type'][9]='E-mail';

$lang['email']['feedbackNoReciepent'] = 'Nun ai introdus un destinatar';
$lang['email']['feedbackSMTPProblem'] = 'A fost găsită o problemă de comunicaţie cu SMTP: ';
$lang['email']['feedbackUnexpectedError'] = 'A fost o eroare neaşteptată în e-mail: ';
$lang['email']['feedbackCreateFolderFailed'] = 'Nu este posibilă crearea fişierului';
$lang['email']['feedbackSubscribeFolderFailed'] = 'Nu este posibilă înregistrarea fişierului';
$lang['email']['feedbackUnsubscribeFolderFailed'] = 'Nu este posibilă deregistrarea fişierului';
$lang['email']['feedbackCannotConnect'] = 'Nu este posibilă legătura la %1$s<br /><br />Serverul de postă a trimis: %2$s';
$lang['email']['inbox'] = 'Posta ajunsă';

$lang['email']['spam']='Spam';
$lang['email']['trash']='Gunoi';
$lang['email']['sent']='Posta trimisă';
$lang['email']['drafts']='Drafts';

$lang['email']['no_subject']='Nici un subiect';
$lang['email']['to']='La';
$lang['email']['from']='De la';
$lang['email']['subject']='Subiect';
$lang['email']['no_recipients']='Nici un destinatar';
$lang['email']['original_message']='--- Mesajul original ---';
$lang['email']['attachments']='Ataşări';

$lang['email']['notification_subject']='Citit: %s';
$lang['email']['notification_body']='Mesajul cu subiectul "%s" a fost citit la ora %s';
$lang['email']['feedbackDeleteFolderFailed']= 'Nu este posibilă ştergerea fişierului';
$lang['email']['errorGettingMessage']='Nu este posibilă recuperarea mesajelor din server';
$lang['email']['no_recipients_drafts']='Nici un destinatar';
$lang['email']['usage_limit']= '%s din %s folosit';
$lang['email']['usage']= '%s folosit';