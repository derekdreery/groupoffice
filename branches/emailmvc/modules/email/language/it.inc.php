<?php
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('email'));
$lang['email']['name'] = 'Email';
$lang['email']['description'] = 'Modulo Client e-mail. Ogni utente sara\' in grado di inviare, ricevere ed inoltrare e-mail';

$lang['link_type'][9]='E-mail';

$lang['email']['feedbackNoReciepent'] = 'Non hai inserito un destinatario';
$lang['email']['feedbackSMTPProblem'] = 'Si e\' verificato un problema di comunicazione con SMTP: ';
$lang['email']['feedbackUnexpectedError'] = 'C\'e\' stato un errore inaspettato nell\'e-mail: ';
$lang['email']['feedbackCreateFolderFailed'] = 'Impossibile creare la cartella';
$lang['email']['feedbackDeleteFolderFailed'] = 'Impossibile cancellare la cartella';
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
$lang['email']['notification_body']='Il tuo messaggio con oggetto "%s" e\' stato visualizzato alle %s';

$lang['email']['errorGettingMessage']='Impossibile recuperare i messaggi dal server';
$lang['email']['no_recipients_drafts']='Nessun destinatario';
$lang['email']['usage_limit']= '%s di %s usati';
$lang['email']['usage']= '%s usati';
$lang['email']['event']='Appuntamento';
$lang['email']['calendar']='Calendario';

$lang['email']['quotaError']="La tua casella di posta e\' piena. Se e\' gia\' vuota e la vostra casella di posta e\' ancora piena, e\' necessario disattivare la cartella Cestino per eliminare i messaggi da altre cartelle. Puoi disabilitarla da:\n\nAdministration -> Accounts -> Doppio click su account -> Cartelle.";

$lang['email']['draftsDisabled']="Il Messaggio non puo essere salvato perche\' la cartella \'Bozze\' e\' disabilitata.<br /><br />Per configurarla vai a Administration -> Accounts -> Doppio click su account -> Cartelle.";
$lang['email']['noSaveWithPop3']='Il Messaggio non puo essere salvato perche\' un account POP3 non lo supporta.';

$lang['email']['goAlreadyStarted']='Group-Office e\' gia\' stato avviato. Il compositore e-mail e\' ora caricato in Group-Office. Chiudere questa finestra e comporre il messaggio in Group-Office.';

//At Tuesday, 07-04-2009 on 8:58 Group-Office Administrator <test@intermeshdev.nl> wrote:
$lang['email']['replyHeader']='Di %s, %s alle %s %s ha scritto:';
$lang['email']['alias']='Alias';
$lang['email']['aliases']='Aliases';
$lang['email']['alias']='Alias';
$lang['email']['aliases']='Aliases';

$lang['email']['noUidNext']='Il server di posta non supporta UIDNEXT. La cartella \'Bozze\' e\' automaticamente disabilitata per questo account.';

$lang['email']['disable_trash_folder']='Spostamento della e-mail nella cartella Cestino fallito. Questo potrebbe essere perche\' si e\' raggiunto lo spazio massimo consentito su disco. Puoi solo liberare spazio disabilitando la cartella Cestino da Administration -> Accounts -> Doppio click su account -> Cartelle';

$lang['email']['error_move_folder']='Non puoi spostare la catella';

$lang['email']['error_getaddrinfo']='Indirizzo host specificato non valido';
$lang['email']['error_authentication']='Nome utente o password non valida';
$lang['email']['error_connection_refused']='La connessione e\' stata rifiutata. Si prega di controllare l\'host ed il numero di porta.';

$lang['email']['iCalendar_event_invitation']='Questo messaggio contiene un invito ad un evento.';
$lang['email']['iCalendar_event_not_found']='Questo messaggio contiene un aggiornamento di un evento che non esiste piu\'.';
$lang['email']['iCalendar_update_available']='Questo messaggio contiene un aggiornamento di un evento esistente.';
$lang['email']['iCalendar_update_old']='Questo messaggio contiene un evento che e\' gia\' stato elaborato.';
$lang['email']['iCalendar_event_cancelled']='Questo messaggio contiene un evento cancellato.';
$lang['email']['iCalendar_event_invitation_declined']='Questo messaggio contiene un invito ad un evento che hai rifiutato.';
