<?php
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('forum'));

$frm_new_forum = 'Neues Forum';
$frm_properties = 'Einstellungen';

$frm_read_perm = 'Leseberechtigung';
$frm_write_perm = 'Schreibberechtigung';

$frm_name = 'Name';
$frm_desc = 'Beschreibung';

$frm_already_exists = 'Dieses Forum existiert bereits';

$frm_added = 'Das Forum wurde erfolgreich angelegt';
$frm_updated = 'Das Forum wurde erfolgreich aktualisiert';
$frm_not_saved = 'Das Forum konnte nicht gespeichert werden';
$frm_not_updated = 'Das Forum konnte nicht aktualisiert werden';

$frm_acl_read = 'Berechtigung das Forum zu lesen';
$frm_acl_write = 'Berechtigung Beiträge zu verfassen';

$frm_no_forum = 'Kein Forum';
$frm_confirm_delete = 'Das Forum wird gelöscht. Sind Sie sicher?';

$frm_post_answer = 'Auf diesen Beitrag antworten';
$frm_post_new_message = 'Neues Thema';
$frm_responses = 'Andere Antworten';

$frm_fields_missing = 'Bitte alle Felder ausfüllen';
$frm_msg_no = 'Beitrag N°';

$frm_msg_posted = 'Beitrag veröffentlicht';
$frm_no_message = 'Keine Beiträge';
$frm_no_response = 'Keine Antworten';
$frm_attached_files = 'Angehängte Dateien';

$frm_title = 'Titel';
$frm_body = 'Beitragstext';
$frm_posted_by = 'veröffentlicht von';

$frm_in_answer_to = 'als Antwort auf';
$frm_forum = 'Forum';
$frm_created_by = 'Erstellt by';
$frm_ctime = 'Erstellt am';
$frm_mtime = 'Letzter Beitrag';
$frm_reply = 'Antwort';
$frm_reply_to = 'Antworten auf';

$frm_messages_total = 'Beiträge';
$frm_subjects_total = 'Themen';
$frm_unread_messages_total = 'Ungelesene Beiträge';
$frm_unread_subjects_total = 'Neue Themen';

$frm_mailing_list = 'Mailing-Liste';

$frm_mail_subject='Neues Thema im Forum "%forum_name%"';
$frm_mail_subject2='Neuer Beitrag im Forum "%forum_name%"';



$frm_mail_body=<<<frm_mail_body
%style%
<div class="automail">
<p class="date">%user% hat ein neues Thema am %date% um %time% veröffentlicht</p>
<div class="box"><p class="title">%msg_title%</p>
<p class="body">
%msg_body%
</p></div>
<a href="%url%">Das Forum aufrufen</a>
</div>
frm_mail_body;




$frm_mail_body2=<<<frm_mail_body2
%style%
<div class="automail">
<p class="date">%user% hat einen neuen Beitrag am %date% um %time% veröffentlicht</p>
<div class="box"><p class="title">%msg_title%</p>
<p class="body">
%msg_body%
</p></div>
<a href="%url%">Das Forum aufrufen</a>
</div>
frm_mail_body2;


$frm_ad_type = 'Email-Benachrichtigung';
$frm_ad_1 = 'Nur bei neuen Themen';
$frm_ad_2 = 'Bei jedem neuen Beitrag';
$frm_ad_3 = 'Nie';

$frm_display_type = 'Darstellung';
$frm_display_1 = 'Baumansicht';
$frm_display_2 = 'Listenansicht';

$frm_display_sort = 'Sortiert nach';
$frm_sort_1 = 'Titel';
$frm_sort_2 = 'Datum';
$frm_sort_3 = 'Vorname des Erstellers';
$frm_sort_4 = 'Nachname des Erstellers';
$frm_ASC = 'Aufsteigend';
$frm_DESC = 'Absteigend';
$frm_order ='Reihenfolge';

$frm_expand_all = 'Alle aufklappen';
$frm_collapse_all = 'Alle zuklappen';

$mail_style=<<<mail_style
<style>
	.automail{ font-family:Verdana;font-weight:bolder;font-size:12px; }
	.box { border: 1px solid #ddd;background:#f8f8f8;width:400px;padding:5px; }
	.title { font-size:14px;text-align:center;text-decoration:underline; }
	.body { text-align:justify; font-size:10px; }
	
</style>
mail_style;

$frm_edit = 'Bearbeiten';
$frm_delete = 'Löschen';
$frm_confirm_msg_delete = 'Dieser Beitrag wird gelöscht. Sind Sie sicher?';

$frm_close_topic = 'Thema schliessen';
$frm_confirm_close_topic = 'Dieses Thema wird geschlossen. Sind Sie sicher?';
$frm_closed_topic = 'Geschlossen';
$frm_reopen_topic = 'Theam wieder eröffnen';
$frm_confirm_reopen_topic = 'Das Thema wird erneut eröffnet. Sind Sie sichr?';
$frm_custom = 'Benutzerdefiniertes Feld';

$frm_import = 'Import';
$frm_import_text = 'Bitte die zu importierende CSV-Datei wählen';
$frm_import_file = 'CSV-Datei';
$frm_confirm_empty_forum = 'Das Forum wird geleert. Sind Sie sicher?';
$frm_empty_forum = 'Forum leeren';
$frm_choose_sep = 'Das verwendete Trennzeichen in der CSV-Datei eingeben';
$frm_import_string = 'Bitte organisieren Sie Ihre Felder in der CSV-Datei wie folgt:<br /> Datum, Zeit, Thema, Beitragstext, Feld1, Feld2, Feld3';
$frm_entries_added = 'Die Einträge wurden hinzugefügt';
$frm_entry_added = 'Der Eintrag wurde hinzugefügt';
