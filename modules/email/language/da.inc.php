<?php
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('email'));
$lang['email']['name'] = 'E-mail';
$lang['email']['description'] = 'E-mail modul; Lille webbaseret e-mail klient. Samtlige brugere vil være i stand til at sende, modtage og videresende e-mails';

$lang['link_type'][9]='E-mail';

$lang['email']['feedbackNoReciepent'] = 'Du har ikke angivet en modtager';
$lang['email']['feedbackSMTPProblem'] = 'Der opstod et problem i SMTP-kommunikationen: ';
$lang['email']['feedbackUnexpectedError'] = 'Der opstod et uventet problemunder dannelsen af e-mailen: ';
$lang['email']['feedbackCreateFolderFailed'] = 'Kunne ikke oprette folder';
$lang['email']['feedbackDeleteFolderFailed'] = 'Kunne ikke slette folder';
$lang['email']['feedbackSubscribeFolderFailed'] = 'Kunne ikke abonnere på folderen';
$lang['email']['feedbackUnsubscribeFolderFailed'] = 'Kunne ikke afmelde abonnement på folderen';
$lang['email']['feedbackCannotConnect'] = 'Kunne ikke forbinde til %1$s på port %3$s<br /><br />Mail serveren returnerede: %2$s';
$lang['email']['inbox'] = 'Indbakke';

$lang['email']['spam']='Spam';
$lang['email']['trash']='Affald';
$lang['email']['sent']='Sendt post';
$lang['email']['drafts']='Kladder';

$lang['email']['no_subject']='Intet emne';
$lang['email']['to']='Til';
$lang['email']['from']='Fra';
$lang['email']['subject']='Emne';
$lang['email']['no_recipients']='Skjulte modtagere';
$lang['email']['original_message']='--- Original meddelelse nedenfor ---';
$lang['email']['attachments']='Vedhæftede filer';

$lang['email']['notification_subject']='Læs: %s';
$lang['email']['notification_body']='Din meddelelse med emnet "%s" blev vist  %s';

$lang['email']['errorGettingMessage']='Kunne ikke hente meddelelse fra server';
$lang['email']['no_recipients_drafts']='Ingen modtagere';
$lang['email']['usage_limit'] = '%s af %s brugt';
$lang['email']['usage'] = '%s brugt';

$lang['email']['event']='Aftale';
$lang['email']['calendar']='kalender';
