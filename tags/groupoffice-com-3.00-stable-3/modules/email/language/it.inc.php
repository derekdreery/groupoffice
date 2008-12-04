<?php
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('email'));
$lang['email']['name'] = 'Email';
$lang['email']['description'] = 'Modulo e-mail; Small webbased e-mail client. Ogni utente sarà in grado di inviare, ricevere ed inoltrare e-mail';

$lang['link_type'][9]='E-mail';

$lang['email']['feedbackNoReciepent'] = 'Non hai inserito un destinatario';
$lang['email']['feedbackSMTPProblem'] = 'Si è verificato un problema di comunicazione con SMTP: ';
$lang['email']['feedbackUnexpectedError'] = 'C\'è stato un errore inaspettato nell\'e-mail: ';
$lang['email']['feedbackCreateFolderFailed'] = 'Impossibile creare la cartella';
$lang['email']['feedbackSubscribeFolderFailed'] = 'Impossibile registrare la cartella';
$lang['email']['feedbackUnsubscribeFolderFailed'] = 'Impossibile deregistrare la cartella';
$lang['email']['feedbackCannotConnect'] = 'Impossibile collegarsi al %1$s<br /><br />Il server di posta ha restituito: %2$s';
$lang['email']['inbox'] = 'Posta in arrivo';

$lang['email']['spam']='Spam';
$lang['email']['trash']='Cestino';
$lang['email']['sent']='Posta inviata';
$lang['email']['drafts']='Bozze';

$lang['email']['no_subject']='Nessun oggetto';
$lang['email']['to']='A';
$lang['email']['from']='Da';
$lang['email']['subject']='Oggetto';
$lang['email']['no_recipients']='Nessun destinatario';
$lang['email']['original_message']='--- Messaggio originale ---';
$lang['email']['attachments']='Allegati';

$lang['email']['notification_subject']='Letto: %s';
$lang['email']['notification_body']='Il tuo messaggio con oggetto "%s" è stato visualizzato alle %s';
$lang['email']['feedbackDeleteFolderFailed']= 'Impossibile cancellare la cartella';
$lang['email']['errorGettingMessage']='Impossibile recuperare I messaggi dal server';
$lang['email']['no_recipients_drafts']='Nessun destinatario';
$lang['email']['usage_limit']= '%s di %s usati';
$lang['email']['usage']= '%s usati';