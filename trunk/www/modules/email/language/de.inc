<?php
require_once($GO_LANGUAGE->get_fallback_language_file('email'));

$auml = utf8_encode('�');
$ouml = utf8_encode('�');
$uuml = utf8_encode('�');
$Auml = utf8_encode('�');
$Ouml = utf8_encode('�');
$Uuml = utf8_encode('�');
$szlig = utf8_encode('�');

//Email
$ml_from = "Von";
$ml_to = "An";
$ml_send = "Senden";
$ml_attach = "Anhängen";
$ml_attachments = "Anhänge";
$ml_attachment = "Anhang";
$ml_high = "Hoch";
$ml_normal = "Normal";
$ml_low = "Niedrig";
$ml_priority = "Priorität";
$ml_subject = "Betreff";
$ml_filename = "Dateiname";
$ml_total_size = "Gesamtgrösse";
$ml_size = "Grösse";
$ml_to_empty = "Sie haben keinen Empfänger angegeben.";
$ml_print = "Drucken";
$fbNoSelect = "Sie haben keine Nachricht ausgewählt";
$ml_inbox = 'Posteingang';
$ml_properties = 'Eigenschaften';
$ml_add_account = "Ein Postfach einrichten";
$em_new_folder = "Neuer Ordner";
$ml_your_accounts = "Ihre Email-Postfächer";
$ml_no_accounts = "Sie haben noch keine Email-Postfächer eingerichtet.";
$ml_original_follows = "----- Originalnachricht -----";
$ml_welcome = "Willkommen zu Group-Office Email";
$ml_text = "Klicken Sie auf Konten, um ein Email-Postfach einzurichten.";
$ml_accounts = "Konten";
$ml_compose = "Neue Nachricht";
$ml_delete = "Löschen";
$ml_refresh = "Aktualisieren";
$ml_reply = "Antworten";
$ml_forward = "Weiterleiten";
$ml_reply_all = "Allen Antworten";
$ml_delete_message = "Sind Sie sicher, dass Sie diese Nachricht endgültig löschen möchten?";
$ml_no_subject = "Kein Betreff";
$ml_no_reciepent = "Unbekannt";
$ml_online_files = "Online-Dateien";
$ml_send_error = "Der Email-Versand ist fehlgeschlagen.";
$ml_attach_forget = "Sie haben zwar eine Datei ausgewählt, aber noch nicht angehangen.";
$ml_messages = "Nachricht(en)";
$ml_message = 'diese Nachricht';
$ml_messages2 = 'Nachrichten';
$ml_new = "Neu";
$ml_delete_mailbox = "Sind Sie sicher, dass Sie die ausgewählten Nachrichten endgültig löschen möchten?";
$contact_profile = "Kontaktprofil";
$ml_add_contact = "Absender zur Kontaktliste hinzufügen";
$strHost = "Host";
$strDefault = "Standard";
$ml_edit_account = "Postfach bearbeiten";
$ml_new_account = "Neues Postfach";
$ml_no_messages = "Keine Nachrichten in diesem Ordner.";
$ml_file_too_big = "Die anzuhängende Datei ist zu gross. Die maximale Grösse für einen Dateianhang ist: ";
$fbNoRightToOverwrite = "Die Datei existiert bereits, und Sie haben nicht die Berechtigung diese zu überschreiben.";
$ml_save_attachment = "Dateianhang in Group-Office speichern.";
$ml_folders = "Ordner";
$ml_select_folder = "Bitte den Ordner fuer die gesendeteten Nachrichten angeben. Oder geben Sie unten einen Ordnernamen ein, um diesen neu anzulegen.";
$ml_connect_failed = "Verbindungsversuch zum Mailserver fehlgeschlagen:";
$ml_at_port = "Am Port";
$ml_solve_error = 'Klicken Sie auf \'Postfächer\', um Ihre Postfacheinstellungen zu korrigieren, oder klicken Sie auf \'Aktualisieren\', um Ihr Standard-Postfach zu öffnen.';
$ml_sent_items = "Gesendet";
$ml_spam = "Spam";
$ml_use = "In Verwendung";
$ml_synchronise = "Synchronisieren";
$ml_move_mail = "Verschiebe selektierte Nachrichten in den Ordner...";
$ml_filters = "Filter";
$ml_move_to = "dann verschiebe die Nachricht in den Ordner:";
$ml_email_is = "Wenn das Absender-Feld enthält:";
$ml_to_is = "Wenn das Empfänger-Feld enthält:";
$ml_cc_is = "Wenn das CC-Feld enthält:";
$ml_subject_is = "Wenn die Betreffzeile enthält:";
$ml_new_filter = "Neuer Filter";
$ml_field = "Feld";
$ml_contains = "Enthält";
$ml_folder = "Ordner";
$ml_no_folders = "Sie haben noch keine Ordner, daher ist es nicht möglich, Filter zu definieren.";
$ml_unwanted = "Bitte hier klicken, wenn Nachrichten von diesem Absender unerwünscht sind";
$fbFolderSize = "Grösse des Ordners";
$ml_move_up = "Priorität anheben";
$ml_delete_filter = "Sind Sie sicher, dass Sie diesen Filter entfernen möchten?";
$ml_high_priority = "Diese Nachricht hat eine hohe Priorität!";
$ml_low_priority = "Diese Nachricht hat eine niedrige Priorität.";
$ml_block = "Absender blocken";
$ml_sent_items_fail = "Diese Nachricht ist versendet worden. Allerdings hat es einen Fehler gegeben beim Speichern dieser Nachricht im Ordner für gesendete Nachrichten. Bitte überpruefen Sie Ihr IMAP-Postfach.";
$fbProperties = "Eigenschaften";
$ml_notification = "Vom Empfänger eine Lesebenachrichtigung anfordern";
$ml_ask_notify = "Der Absender dieser Nachricht hat eine Lesebenachrichtigung angefordert. Sind Sie damit einverstanden, dass der Absender diese bekommt?";
$ml_displayed = "Die nachfolgende Nachricht wurde gesichtet von ";
$ml_notify = "Lesebenachrichtigung";
$ml_delete_filter1 = "Löschfilter";
$ml_html_message_attached = "Die originalen HTML-Nachrichten sind angehängt";
$ml_no_folders = 'Sie haben keine ordner zum filtern';
$ml_no_filters = 'Sie haben noch keine Filter.';
$ml_host = 'Mailserver';
$ml_port = 'Port';
$ml_type = 'Typ';
$ml_servertype = 'Servertyp';
$ml_format = 'Format';
$ml_loose_changes = 'Alle bisher gemachten Texteingaben werden verloren gehen. Sind Sie sicher?';
$ml_markread = "gelesen";
$ml_markunread = "ungelesen";
$ml_root_mailbox = "Stammordner";
$ml_inside = "Im";
$ml_no_mailbox = "Keine Mailbox. Dieser Ordner kann keine Nachrichten enthalten.";
$ml_signature = 'Signatur';
$ml_root = 'Stammordner';
$ml_disable_accounts = 'Kontenverwaltung deaktivieren';
$ml_trash = 'Papierkorb';
$ml_disable = 'Deaktivieren';

$ml_empty_mailbox = 'Ordner&nbsp;leeren';
$ml_you_have = 'Sie haben';
$ml_new_mail_single = 'neue Email-Nachricht.';
$ml_new_mail_multiple = 'neue Email-Nachrichten.';
$ml_auto_check = 'Automatische Benachrichtigung bei neuen Emails.';
//1.98
$ml_no_contacts_in_mailing_group = 'Die selektierte Mailing-Gruppe enthält keine Kontakte. Bitte zuerst Kontakte hinzufügen.';
$ml_attention= 'Achtung';

//2.02
$ml_autocheck_disabled = ' konnte nicht erreicht werden. Die automatische Überprüfung nach neuen Mails ist auf diesem Konto ausgeschaltet.';
$ml_mark = 'Nachrichten markieren als...';
$ml_flag = 'gekennzeichnet';
$ml_clearflag = 'nicht gekennzeichnet';
$ml_expand_all = 'Alle Ordner ausklappen';
$ml_collapse_all = 'Alle Ordner zusammenklappen';

//2.05
$ml_search = 'Suchen';
$ml_answered = 'beantwortet';
$ml_doesnt_matter = 'nicht relevant';
$ml_body = 'Nachricht';
$ml_before = 'älter als';
$ml_since = 'neuer als';
$ml_select_one_criteria = 'Bitte wenigstens einen Suchbegriff eingeben.';

//2.06
$ml_appointment = 'Diese Nachricht enthält einen oder mehrere Termineinträge. Möchten Sie den Dateiinhalt im Kalender betrachten?';
$ml_mails_were_sent = 'Die Nachrichten wurden versandt';
$ml_novalidate_cert = 'Das Zertifikat nicht validieren';
$ml_appointment_vcf = 'Diese Nachricht enthält eine Kontaktadresse. Möchten Sie den Dateiinhalt im Adressbuch betrachten?';


//2.08 
$ml_add_senders = 'Nachfragen, ob ein unbekannter Absender in das Standard-Adressbuch übernommen werden soll.';
$ml_text_format = 'Einfacher text';
$ml_html_format_is = 'HTML (Wenn der Browser das Bearbeiten von HTML unterstützt.)';
$ml_html_format = 'HTML-formatiert';
$ml_always_ask_notification = 'Den Empfänger immer um eine Lesebestätigung bitten.';
$ml_ask_add_sender = 'Die Absender-Adresse "%s" existiert nicht in Ihrem Adressbuch. Soll dieser Kontakt in Ihr Adressbuch aufgenommen werden?';

$ml_edit_filter = 'Filter bearbeiten';
$ml_choose_action = 'Bitte wählen Sie eine Option:';
$ml_search_criteria = 'Suchbegriff';
$ml_sender = 'Absender';
$ml_reciever = 'Empfänger';
$ml_cc_reciever = 'Kopie-Empfänger';

//2.10
$ml_empty_trash = 'Papierkorb&nbsp;leeren';
//2.11
$ml_drafts = 'Vorlagen';
$ml_save_draft_error = 'Das Speichern der Vorlage ist fehlgeschlagen. Bitte überprüfen Sie ihre Kontoeinstellungen.';
$ml_search_sender = 'Nach weiteren Emails von diesem Absender suchen';

$ml_no_attachments = 'Keine Dateien angehängt';
$ml_search_active = 'Die Suchfunktion ist aktiv. Derzeit werden nicht alle Nachrichten dieses Ordners angezeigt. Bitte hier klicken, um die Suchfunktion anzuhalten.';
$ml_local_files = 'Lokale Dateien';
$ml_please_close_attachments = 'Bitte zuerst den Dateianhang-Dialog schließen. Aber vorher bitte vergewissern, dass die Dateiübertragung erfolgreich abgeschlossen wurde.';

$ml_please_wait = 'Bitte warten...';
$ml_max_attachment_size = 'Maximale Grösse für einen Dateianhang';
$ml_select = 'Datei wählen';
$ml_multiple_files = 'Mehrere Dateien';

//2.12
$ml_reenter_password = 'Aus Sicherheitsgründen werden Sie aufgefordert, das Passwort für dieses Email-Konto erneut einzugeben.';

$ml_attachments_added = 'Dateien angehängt';
$ml_attachment_added = 'Datei angehängt';

//2.13
$ml_download_zipped_attachments = 'Zip-Archiv von allen angehängten Dateien';
$ml_fullscreen = 'Vollbild';
$ml_nofullscreen = 'Vollbild&nbsp;schließen';
$ml_confirm_empty_trash = 'Papierkorb leeren. Sind Sie sicher?';
$ml_seen = 'gelesen';
$ml_charset = 'Zeichensatzkodierung';

//2.14
$ml_no_reciever_add = "Nicht hinzufügen";
$ml_add_recievers = "Unbekannte Empfänger hinzufügen";//changed string from 2.06

$ml_automatic_reply = 'Automatische Antworten aktivieren';

$ml_set_default = 'Als Standard';

$ml_used_of = 'benutzt von';

$ml_folder_not_available = 'Sie können bei POP3 Accounts keine Extraordner erstellen';

//2.15
$ml_mark_as_read='Markiere gefilterte Nachrichten als gelesen';

$ml_get_message_error_title = 'Fehler beim Abrufen der Nachricht';
$ml_get_message_error_text = 'Konnte die Nachricht nicht vom Server laden. Vielleicht wurde sie von einem anderen Benutzer gelöscht. Bitte klicken Sie auf \'Aktualisieren\'.';
$ml_spamtag = 'Spam Markierung';
$ml_confirm_no_subject = 'Sie haben keinen Betreff für diese Nachricht eingegeben. Sind sie sicher dass sie die Nachricht ohne Betreff versenden wollen?';
$ml_show_preview = 'Zeige eine Vorschau bei der Auswahl einer Nachricht';

$ml_show_headers = 'Zeige Nachrichtenkopf';
$ml_confirm_empty_mailbox = 'Wollen sie wirklich den Ordner \'%s\' leeren?';

//2.16
$em_source = 'Quelltext der Nachricht';
