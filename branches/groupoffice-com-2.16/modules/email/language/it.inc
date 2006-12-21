<?php
require_once($GO_LANGUAGE->get_fallback_language_file('email'));

//Email
$ml_from = "Da";
$ml_to = "A";
$ml_send = "Invia";
$ml_attach = "Allega";
$ml_attachments = "Allegati";
$ml_attachment = "Allegato";
$ml_high = "Alta";
$ml_normal = "Normale";
$ml_low = "Bassa";
$ml_priority = "Priorità";
$ml_subject = "Oggetto";
$ml_filename = "Nome file";
$ml_total_size = "Dimensione totale";
$ml_size = "Dimensione";
$ml_to_empty = "Non hai specificato il destinatario.";
$ml_print = "Stampa";
$fbNoSelect = "Non hai selezionato un messaggio";

$ml_inbox = 'Posta arrivata';
$ml_properties = 'Proprietà';
$ml_add_account = "Aggiungi un account";
$em_new_folder = "Nuova cartella";
$ml_your_accounts = "I tuoi account di posta elettronica";
$ml_no_accounts = "Non hai nessun account di posta elettronica configurato.";
$ml_original_follows = "----- Messaggio Originale -----";
$ml_welcome = "Benvenuto in Group-Office Posta Elettronica";
$ml_text = "Seleziona account per aggiungere un profilo posta elettronica.";
$ml_accounts = "Account";
$ml_compose = "Componi";
$ml_delete = "Elimina";
$ml_refresh = "Aggiorna";
$ml_reply = "Rispondi";
$ml_forward = "Inoltra";
$ml_reply_all = "Rispondi a tutti";
$ml_delete_message = "Sei sicuro di eliminare questo messaggio in modo permanente?";
$ml_no_subject = "Nessun oggetto";
$ml_no_reciepent = "Destinatari non visibili";
$ml_online_files = "File Online";
$ml_send_error = "Invio del messaggio fallito.";

$ml_attach_forget = "Hai selezionato un file ma non l'hai allegato.";
$ml_messages = "messaggio/i";
$ml_message = 'questo messaggio';
$ml_messages2 = 'messaggi';
$ml_new = "nuovi";
$ml_delete_mailbox = "Sei sicuro di eliminare i messaggi selezionati in modo permanente?";

$contact_profile = "Profilo contatto";
$ml_add_contact = "Aggiungi il mittente nella rubrica";
$strHost = "Host";
$strDefault = "Predefinito";
$ml_edit_account = "Modifica account";
$ml_new_account = "Nuovo account";
$ml_no_messages = "Non ci sono messaggi in questa cartella.";
$ml_file_too_big = "Il file che hai tentato di allegare è troppo grande, la dimensione massima per un allegato è impostata a: ";
$fbNoRightToOverwrite = "Il file esiste già e non è possibile sovrascriverlo.";
$ml_save_attachment = "Salva l'allegato in Group-Office.";
$ml_folders = "Cartelle";

$ml_select_folder = "Seleziona la cartella dove Group-Office può memorizzare la posta inviata o inserisci un nome sotto per crearne una nuova";
$ml_connect_failed = "Connessione al server di posta fallita:";
$ml_at_port = "alla porta";
$ml_solve_error = 'Seleziona \'Account\' per correggere le impostazioni del tuo account o seleziona \'Aggiorna\' per aprire il account predefinito.';
$ml_sent_items = "Messaggi inviati";
$ml_spam = "Spam";
$ml_use = "Usa";
$ml_synchronise = "Sincronizza";
$ml_move_mail = "Muovi i messaggi selezionati nella cartella...";
$ml_filters = "Filtri";
$ml_move_to = "quindi muovi il messaggio nella cartella:";
$ml_email_is = "Se l'indirizzo email del mittente contiene:";
$ml_to_is = "Se i destinatari contengono:";
$ml_cc_is = "Se il campo cc contiene:";

$ml_subject_is = "Se l'oggetto contiene:";
$ml_new_filter = "Nuovo filtro:";
$ml_field = "Campo";
$ml_contains = "Contiene";
$ml_folder = "Cartella";
$ml_no_folders = "Non hai nessuna cartella perciò non puoi creare dei filtri.";
$ml_unwanted = "Seleziona qui se il mittente di questo messaggio è indesiderato";
$fbFolderSize = "Dimensione della cartella";
$ml_move_up = "Aumenta la priorità";
$ml_delete_filter = "Sei sicuro di eliminare questo filtro?";
$ml_high_priority = "Questo messaggio è di alta priorità!";
$ml_low_priority = "Questo messaggio è di bassa priorità.";
$ml_block = "mittente bloccato";
$ml_sent_items_fail = "Il messaggio è stato spedito ma si è verificato un errore salvandolo nella cartella dei messaggi inviati. Controllare l'account IMAP.";
$fbProperties = "Proprietà";
$ml_notification = "Richiedi la ricevuta di ritorno";
$ml_ask_notify = "Il mittente di questo messaggio ha richiesto la ricevuta di ritorno. Desideri inviare il messaggio di avvenuta ricezione?";
$ml_displayed = "Il seguente messaggio è stato visualizzato da ";
$ml_notify = "Notifica di lettura";
$ml_delete_filter1 = "Elimina cartella";
$ml_html_message_attached = "Il messaggio originale in HTML è allegato";
//$ml_add_recievers = "Aggiungi i destinatari sconosciuti nella rubrica";
$ml_no_folders = 'Non hai nessuna cartella da elaborare';
$ml_no_filters = 'Non hai nessun filtro';

$ml_host = 'Nome server';
$ml_port = 'Porta';
$ml_type = 'Tipo';
$ml_servertype = 'Tipo di server';

//modified language under here for translators
$ml_markread = "Marca come letto";
$ml_markunread = "Marca come non letto";
$ml_format = 'Formato';
$ml_loose_changes = 'Le modifiche verranno perse. Sei sicuro?';

//modified language under here for translators for 1.1
$ml_root_mailbox = "Cartella principale";
$ml_inside = "in";

//modified language under here for translators for 1.12
$ml_no_mailbox = "Questa non è una cassetta di posta. Questa cartella non può contenere messaggi.";
$ml_signature = 'Firma';

$ml_root = 'Cartella principale';

// modified for 1.93
$ml_disable_accounts = 'Disattivazione account';
$ml_trash = 'Cestino';

$ml_disable = 'Disabilita';

// 1.96
$ml_confirm_empty_mailbox = 'Confermi di svuotare questa cartella?';
$ml_empty_mailbox = 'Svuota&nbsp;cartella';

// 1.97
$ml_you_have = 'Hai';
$ml_new_mail_single = 'nuovo messaggio di posta.';
$ml_new_mail_multiple = 'nuovi messaggi di posta.';

$ml_auto_check = 'Controlla automaticamente l\'arrivo di nuovi messaggi in questo account.';

//1.98
$ml_no_contacts_in_mailing_group = 'There are no contacts in the mailing group you selected. Please add contacts to the group first.';
$ml_attention= 'Attenzione';

//2.02
$ml_autocheck_disabled = ' non può essere raggiunto. Il controllo automatico di nuova posta per questo account è disattivato.';
$ml_mark = 'Marca messaggi...';
$ml_flag = 'marcato';
$ml_clearflag = 'non marcato';
$ml_expand_all = 'Espandi tutte le cartelle';
$ml_collapse_all = 'Raggruppa tutte le cartelle';

//2.05
$ml_search = 'Cerca';
$ml_answered = 'Risposte';
$ml_doesnt_matter = 'Non importa';
$ml_body = 'Corpo';
$ml_before = 'Più vecchio di';
$ml_since = 'Più nuovo di';
$ml_select_one_criteria = 'Seleziona o inserisci almeno un criterio di ricerca.';

//2.06
$ml_appointment = 'Questo messaggio contiene un appuntamento. Vuoi inserirlo nel calendario?';
$ml_mails_were_sent = 'messaggi email spediti';
$ml_novalidate_cert = 'Non convalidare certificato';
$ml_appointment_vcf = 'Questo messaggio contiene un contatto. Vuoi inserirlo nella rubrica?';
$ml_add_recievers = "Aggiungi destinatari sconosciuti nella tua rubrica predefinita";

//2.08 
$ml_add_senders = 'Chiedi sei vuoi aggiungere mittenti sconosciuti nella tua rubrica predefinita';
$ml_text_format = 'Testo semplice';
$ml_html_format_is = 'HTML (Se il browser supporta un editor HTML)';
$ml_html_format = 'Formato HTML';
$ml_always_ask_notification = 'Chiedi sempre la notifica di lettura del messaggio';
$ml_ask_add_sender = 'L\'indirizzo del mittente %s associato a %s non è nella tua rubrica. Vuoi inserire ora questa persona nella rubrica?';

$ml_edit_filter = 'Modifica filtro';
$ml_choose_action = 'Selezionare un\'azione:';
$ml_search_criteria = 'Criterio di ricerca';
$ml_sender = 'Mittente';
$ml_reciever = 'Destinatario';
$ml_cc_reciever = 'Destinatario-CC';

//2.10
$ml_empty_trash = 'Svuota&nbsp;cestino';

//2.11
$ml_drafts = 'Bozze';
$ml_save_draft_error = 'Salvataggio della bozza fallito. Controllare le impostazioni dell\'account.';

$ml_search_sender = 'Cerca le altre e-mail di questo mittente';

$ml_no_attachments = 'Nessun file allegato';
$ml_search_active = 'La funzione di ricerca è attiva. Non tutti i messaggi di questa cartella sono visualizzati. Seleziona qui per terminare la ricerca';
$ml_local_files = 'File locali';
$ml_please_close_attachments = 'Chiudere la finestra degli allegati e assicurarsi che il trasferimenti sia completato';

$ml_please_wait = 'Prego attendere...';
$ml_max_attachment_size = 'Dimensione massima dell\'allegato';
$ml_select = 'Seleziona file';
$ml_multiple_files = 'File multipli';

//2.12
$ml_reenter_password = 'Per motivi di sicurezza devi inserire nuovamente la password di questo account e-mail.';

$ml_attachments_added = 'file allegati';
$ml_attachment_added = 'file allegato';

//2.13
$ml_download_zipped_attachments = 'Archivio Zip di tutti gli allegati';
$ml_fullscreen = 'Visualizza&nbsp;schermo-intero';
$ml_nofullscreen = 'Chiudi&nbsp;schermo-intero';

$ml_confirm_empty_trash = 'Sei sicuro che vuoi svuotare il cestino?';

$ml_seen = 'Leggi';


$ml_charset = 'Codifica dei caratteri';

//2.14
$ml_no_reciever_add = "Non aggiungere";
$ml_add_recievers = "Aggiungi destinatari sconosciuti in ";//changed string from 2.06

$ml_automatic_reply = 'Attiva risposta automatica';
