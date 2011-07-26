<?php
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('email'));
$lang['email']['name']='E-Mail';
$lang['email']['description']='Modul zum Verwalten von E-Mail-Konten und -Nachrichten';
$lang['link_type'][9]='E-Mail';
$lang['email']['feedbackNoReciepent']='Sie haben keinen Empfänger eingegeben';
$lang['email']['feedbackSMTPProblem']='Es gab ein Problem bei der SMTP-Kommunikation: ';
$lang['email']['feedbackUnexpectedError']='Es gab ein unerwartetes Problem bei Erstellung der Nachricht: ';
$lang['email']['feedbackCreateFolderFailed']='Ordner erstellen fehlgeschlagen';
$lang['email']['feedbackDeleteFolderFailed']='Ordner löschen fehlgeschlagen';
$lang['email']['feedbackSubscribeFolderFailed']='Ordner abonnieren fehlgeschlagen';
$lang['email']['feedbackUnsubscribeFolderFailed']='Abonnement aufheben fehlgeschlagen';
$lang['email']['feedbackCannotConnect']='Es konnte keine Verbindung zu %1$s erstellt werden<br /><br />Der Mailserver hat folgende Antwort gesandt: %2$s';
$lang['email']['inbox']='Eingang';
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
$lang['email']['notification_body']='Ihre Nachricht mit dem Betreff "%s" wurde am %s geöffnet';
$lang['email']['errorGettingMessage']='Nachricht konnte vom Server nicht empfangen werden';
$lang['email']['no_recipients_drafts']='Keine Empfänger';
$lang['email']['usage_limit']='%s von %s genutzt';
$lang['email']['usage']='%s genutzt';
$lang['email']['event']='Termin';
$lang['email']['calendar']='Kalender';
$lang['email']['quotaError']='Ihre Mailbox ist voll. Leeren Sie zuerst ihren Papierkorb. Bleibt dies erfolglos, so deaktivieren Sie den Papierkorb, um Nachrichten aus anderen Ordnern zu löschen. Sie deaktivieren den Papierkorb über:\n\nEinstellungen -> E-Mail-Konten -> Doppelklick auf das gewünschte E-Mail-Konto -> Ordner.';
$lang['email']['draftsDisabled']='Die Nachricht konnte nicht gespeichert werden, da der Ordner \'Entwürfe\' (Drafts) nicht aktiviert wurde.<br /><br />Gehen Sie zu E-Mail -> Verwaltung -> Konten -> Doppelklick auf das gewünschte E-Mail-Konto -> Ordner, um den Ordner zu aktivieren.';
$lang['email']['noSaveWithPop3']='Die Nachricht konnte nicht gespeichert werden, da ein POP3-Konto diese Funktion nicht unterstützt.';
$lang['email']['goAlreadyStarted']='GroupOffice wurde bereits gestartet. Die E-Mail-Oberfläche wird nun in GroupOffice geladen. Schließen Sie dieses Fenster und schreiben Sie Ihre Nachricht in GroupOffice.';
$lang['email']['replyHeader']='Am %s, den %s um %s schrieb %s:';
$lang['email']['alias']='Alias';
$lang['email']['aliases']='Aliase';
$lang['email']['noUidNext']='Ihr Mailserver unterstützt kein UIDNEXT. Der Ordner \'Entwürfe\' (Drafts) wurde daher automatisch für dieses E-Mail-Konto deaktiviert.';
$lang['email']['disable_trash_folder']='Die Nachricht konnte nicht in den Papierkorb verschoben werden. Vielleicht ist der Ihnen zugewiesene Festplattenspeicher voll. Sie können versuchen Speicher freizumachen, indem Sie den gesamten Papierkorb unter Administration -> Accounts -> Doppelklick auf Ihren Account -> Ordner vorübergehend deaktivieren';
$lang['email']['error_move_folder']='Der Ordner konnte nicht verschoben werden';
$lang['email']['error_getaddrinfo']='Angegebene Hostadresse ist ungültig';
$lang['email']['error_authentication']='Benutzername oder Passwort sind falsch';
$lang['email']['error_connection_refused']='Die Verbindung wurde abgebrochen. Bitte überprüfen Sie den Host und die Portnummer.';
$lang['email']['iCalendar_event_invitation']='Diese Nachricht enthält eine Termineinladung.';
$lang['email']['iCalendar_event_not_found']='Diese Nachricht enthält eine Aktualisierung zu einem nicht mehr vorhandenem Termin.';
$lang['email']['iCalendar_update_available']='Diese Nachricht enthält eine Aktualisierung eines existierenden Termins.';
$lang['email']['iCalendar_update_old']='Diese Nachricht enthält einen Termin der in der Vergangenheit liegt.';
$lang['email']['iCalendar_event_cancelled']='Diese Nachricht enthält die Absage eines Termins.';
$lang['email']['iCalendar_event_invitation_declined']='Diese Nachricht enthält einen Termin den sie abgelehnt haben.';

