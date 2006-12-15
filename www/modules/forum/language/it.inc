<?php
require($GO_LANGUAGE->get_fallback_language_file('forum'));

$frm_new_forum = 'Nuovo forum';
$frm_properties = 'Proprietà';

$frm_read_perm = 'Permesso di lettura';
$frm_write_perm = 'Permesso di scrittura';

$frm_name = 'Nome';
$frm_desc = 'Descrizione';

$frm_already_exists = 'Questo forum esiste già';

$frm_added = 'Il forum è stato inserito con successo';
$frm_updated = 'Il forum è stato aggiornato con successo';
$frm_not_saved = 'Il forum NON può essere salvato';
$frm_not_updated = 'Il forum NON può essere aggiornato';

$frm_acl_read = 'Utenti abilitati alla visualizzazione del forum';
$frm_acl_write = 'Utenti abilitati all\'inserimento di messaggi';

$frm_no_forum = 'Nessun forum';
$frm_confirm_delete = 'Sei sicuro di voler eliminare il forum';

$frm_post_answer = 'Rispondi a questo messaggio';
$frm_post_new_message = 'Nuovo argomento';
$frm_responses = 'Altre risposte';

$frm_fields_missing = 'Riempire tutti i campi';
$frm_msg_no = 'Messaggio N°';

$frm_msg_posted = 'Messaggio inviato';
$frm_no_message = 'Nessun messaggio';
$frm_no_response = 'Nessuna risposta';
$frm_attached_files = 'File allegati';

$frm_title = 'Titolo';
$frm_body = 'Corpo';
$frm_posted_by = 'inviato da';

$frm_in_answer_to = 'in risposta a';
$frm_forum = 'Forum';
$frm_created_by = 'Creato da';
$frm_ctime = 'Creato il';
$frm_mtime = 'Ultimo messaggio';
$frm_reply = 'Rispondi';
$frm_reply_to = 'Rispondi a';

$frm_messages_total = 'Messaggi';
$frm_subjects_total = 'Argomenti';
$frm_unread_messages_total = 'Messaggi non letti';
$frm_unread_subjects_total = 'Nuovi argomenti';

$frm_mailing_list = 'Mailing list';

$frm_mail_subject='Nuovi argomenti nel forum "%forum_name%"';
$frm_mail_subject2='Nuovi messaggi nel forum "%forum_name%"';



$frm_mail_body=<<<frm_mail_body
%style%
<div class="automail">
<p class="date">%user% ha inserito un nuovo argomenti il %date% alle %time%</p>
<div class="box"><p class="title">%msg_title%</p>
<p class="body">
%msg_body%
</p></div>
<a href="%url%">Vai al forum</a>
</div>
frm_mail_body;




$frm_mail_body2=<<<frm_mail_body2
%style%
<div class="automail">
<p class="date">%user% ha inserito un nuovo messaggio il %date% alle %time%</p>
<div class="box"><p class="title">%msg_title%</p>
<p class="body">
%msg_body%
</p></div>
<a href="%url%">Vai al forum</a>
</div>
frm_mail_body2;


$frm_ad_type = 'Notifica con Email';
$frm_ad_1 = 'Solo nei nuovi argomenti';
$frm_ad_2 = 'Per ogni nuovo messaggio';
$frm_ad_3 = 'Mai';

$frm_display_type = 'Visualizzazione';
$frm_display_1 = 'albero';
$frm_display_2 = 'lista';

$frm_display_sort = 'Ordina per';
$frm_sort_1 = 'Titolo';
$frm_sort_2 = 'Data';
$frm_sort_3 = 'Nome autore';
$frm_sort_4 = 'Cognome autore';
$frm_ASC = 'Crescente';
$frm_DESC = 'Descrescente';
$frm_order ='Ordine';

$frm_expand_all = 'Espandi tutto';
$frm_collapse_all = 'Compatta tutto';

$mail_style=<<<mail_style
<style>
	.automail{ font-family:Verdana;font-weight:bolder;font-size:12px; }
	.box { border: 1px solid #ddd;background:#f8f8f8;width:400px;padding:5px; }
	.title { font-size:14px;text-align:center;text-decoration:underline; }
	.body { text-align:justify; font-size:10px; }
	
</style>
mail_style;

$frm_edit = 'Modifica';
$frm_delete = 'Elimina';
$frm_confirm_msg_delete = 'Sei sicuro di voler eliminare questo messaggio ?';

$frm_close_topic = 'Chiudi argomento';
$frm_confirm_close_topic = 'Sei sicuro di voler chiudere questo argomento ?';
$frm_closed_topic = 'Chiuso';
$frm_reopen_topic = 'Ri-apri argomento';
$frm_confirm_reopen_topic = 'Sei sicuro di voler ri-aprire questo argomento ?';
$frm_custom = 'Campo custom';

$frm_import = 'Importa';
$frm_import_text = 'Seleziona il file CSV che vuoi importare';
$frm_import_file = 'File CSV';
$frm_confirm_empty_forum = 'Sei sicuro che vuoi svuotare questo forum ?';
$frm_empty_forum = 'Svuota forum';
$frm_choose_sep = 'Inserisci il separatore del file CSV';
$frm_import_string = 'Organizza i campi del tuo file CSV come questi :<br /> Data, Ora, Argomento, Corpo, Field1, Field2, Field3';
$frm_entries_added = 'elementi sono stati aggiunti';
$frm_entry_added = 'elemento è stato aggiunto';

?>
