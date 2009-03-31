<?php
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('email'));
$lang['email']['name'] = 'E-Mail';
$lang['email']['description'] = 'Modul zum Verwalten von E-Mail-Nachrichten';

$lang['link_type'][9]='E-Mail';

$lang['email']['feedbackNoReciepent'] = 'Sie haben keinen Empfänger eingegeben';
$lang['email']['feedbackSMTPProblem'] = 'Es gab ein Problem bei der Kommunikation mit SMTP: ';
$lang['email']['feedbackUnexpectedError'] = 'Es gab ein unerwartetes Problem mit der E-Mail: ';
$lang['email']['feedbackCreateFolderFailed'] = 'Ordner konnte nicht erstellt werden';
$lang['email']['feedbackSubscribeFolderFailed'] = 'Fehler beim Ordner abonnieren';
$lang['email']['feedbackUnsubscribeFolderFailed'] = 'Fehler beim Ordner abmelden';
$lang['email']['feedbackCannotConnect'] = 'Es konnte keine Verbindung zu %1$s erstellt werden<br /><br />Der Mail-Server hat folgende Antwort gesandt: %2$s';
$lang['email']['inbox'] = 'Eingang';

$lang['email']['spam']='Spam';
$lang['email']['trash']='Papierkorb';
$lang['email']['sent']='Gesendet';
$lang['email']['drafts']='Entwürfe';

$lang['email']['no_subject']='Kein Betreff';
$lang['email']['to']='An';
$lang['email']['from']='Von';
$lang['email']['subject']='Betreff';
$lang['email']['no_recipients']='Verborgene Empfänger';
$lang['email']['original_message']='--- Original Nachricht ---';
$lang['email']['attachments']='Anhänge';

$lang['email']['notification_subject']='Gelesen: %s';
$lang['email']['notification_body']='Ihre Nachricht mit dem Betreff "%s" wurde gezeigt am %s';

$lang['email']['errorGettingMessage']='Konnte Nachricht vom Server nicht empfangen';
$lang['email']['no_recipients_drafts']='Keine Empfänger';
$lang['email']['usage_limit'] = '%s von %s genutzt';
$lang['email']['usage'] = '%s genutzt';

$lang['email']['event']='Termin';
$lang['email']['calendar']='Kalender';

$lang['email']['quotaError']="Ihre Mailbox ist voll. Leeren Sie zuerst ihren Papierkorb. If it is already empty and your mailbox is still full, you must disable the Trash folder to delete messages from other folders. You can disable it at:\n\nSettings -> Accounts -> Double click account -> Folders.";

$lang['email']['draftsDisabled']="Message could not be saved because the 'Drafts' folder is disabled.<br /><br />Go to Settings -> Accounts -> Double click account -> Folders to configure it.";
$lang['email']['noSaveWithPop3']='Message could not be saved because a POP3 account does not support this.';

$lang['email']['goAlreadyStarted']='GroupOffice wurde bereits gestartet. Die E-Mail-Oberfläche wird nun in GroupOffice geladen. Schliessen Sie dieses Fenster und schreiben Sie Ihre Nachricht in GroupOffice.';
