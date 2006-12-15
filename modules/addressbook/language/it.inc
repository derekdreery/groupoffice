<?php
require_once($GO_LANGUAGE->get_fallback_language_file('addressbook'));

//contacts
$contacts_title = "Rubrica";
$contacts_no_contacts = "Nessun contatto trovato.";
$contacts_members = "Membri";
$contacts_contacts = "Contatti";
$contacts_add = "Nuovo Contatto";
$contacts_groups = "Gruppi";
$contacts_other = "Altri Contatti";
$contacts_move_to_group = "Sposta i contatti selezionati al gruppo...";
$groups_no_groups = "Non hai nessun gruppo";
$contacts_group = "Gruppi";
$contacts_empty_group = "Usa il combobox per spostare i contatti un questo gruppo.";
$contacts_this_group = 'Questo gruppo';

$add_contacts_text = "Inserisci l'indirizzo E-mail, il codice di autorizzazione e seleziona 'ok'";
$add_contacts_success = "Utente inserito con successo";
$add_contacts_wrong = "Indirizzo E-mail o codice di autorizzazione sbagliati.";
$add_personal_text = "Seleziona 'ok' per inserire il contatto. I campi contrassegnati con il simbolo * sono obbligatori.";
$contacts_select = "Scegli i contatti da aggiungere e seleziona 'ok'.";
$contacts_select_title = "Contatti";
$contact_profile = "Profilo";
$user_profile = "Profilo utente";
$contacts_authorise = "Autorizzazione";
$contacts_add_member = "Aggiungi questo membro nei contatti";
$strConfirmSynchronise = "Questo aggiornerà il contatto con i dati inseriti dall'utente stesso. I cambiamenti manuali verranno persi. Sei sicuro di volere continuare?";
$contacts_import = "Importa";
$contacts_export = "Esporta";
$contacts_export_text = "Seleziona il tipo di CSV che vorresti creare.";
$contacts_delete = "Elimina";

$contacts_import_text = "Scegli un file con i dati da importare e seleziona 'ok'";
$contacts_import_text_group = "Scegli un gruppo e il file CSV. Quindi seleziona su 'ok'.";
$fbNoFile = "Nessun file è stato ricevuto";
$fbPleaseWait = "Attendere importazione file in corso...";

$strNotIncluded = "Non importare";
$contacts_import_feedback = "Nel file CSV sono state trovate le seguenti colonne.<br />";
$contacts_import_feedback .= "Seleziona il gruppo che desideri importare oppure crea i gruppi automaticamente basati sulle colonne del file.<br />";
$contacts_import_feedback .= " Corrispondenze trovate tra le colonne Group-Office e le colonne del file, assicurati di non avere selezionato delle colonne due volte.";
$contacts_separator = "Valori separati da";
$contacts_no_separator = "Non hai inserito il carattere di separazione.";
$fbFilename = "Nome file";
$contacts_import_title = "Importa Rubrica";
$contacts_import_success = "File importato con successo";
$contacts_import_double = "Hai selezionato una o più colonne due volte. Correggi e prova ancora.";
$contacts_import_noname = 'Devi selezionare il campo \'Nome\' e \'Cognome\' ';
$contacts_import_incompatible = "Formato del file incompatibile.<br /><br />Un file CSV è un file con valori separati da (punto e)virgola.";
$strMember = "Membro";
$contact_exist_warning = "<b>Nota:</b> Questo utente è già nella lista dei contatti.";
$contacts_add_to_group = "Aggiungi utenti selezionati nel gruppo dei contatti...";
$contacts_add_to = "Aggiungi gli utenti selezionati nei contatti";
$contacts_add_user = "Aggiungi";
$contacts_edit = "Modifica";
$contacts_no_select = "Non hai selezionato niente";
$contacts_this_contact = "questo contatto";

$contacts_seperator_text = 'Valori separati da';
$contacts_quote = 'Valori inclusi tra';
$contacts_clrf = 'Linee terminate da';
$contacts_auto_group = 'Crea gruppi automaticamente dal file';
$contacts_import_to_group = 'Importa nei gruppi';

//translation required for 1.1
$contacts_search = 'Cerca';
$cmdSearch = 'Cerca';
$contacts_search_text = "Seleziona una lettera oppure inserisci una frase e premi 'cerca'. Puoi usare * come carattere generico.";
$contacts_result = 'Risultato';
$contacts_results = 'Risultati';

$contacts_color = 'Colore';

$contacts_colors['black'] = 'Nero';
$contacts_colors['red'] = 'Rosso';
$contacts_colors['green'] = 'Verde';
$contacts_colors['blue'] = 'Blu';
$contacts_colors['pink'] = 'Rosa';
$contacts_colors['orange'] = 'Arancione';
$contacts_colors['brown'] = 'Marrone';

//translation required
$ab_addressbooks = 'Rubriche';
$ab_owner = 'Proprietario';
$ab_subscribed = 'Sottoscrivi';
$ab_new_ab = 'Nuova rubrica';
$ab_addressbook_exists = 'Esiste già una rubrica con lo stesso nome';
$ab_addressbook = 'Rubrica';
$contacts_select_single = 'Click su un contatto per selezionare questa persona';

$ab_group_exists = 'Esiste già una rubrica con lo stesso nome';
$ab_group_on_file = 'Gruppi di contatti basati su file';
$ab_default = 'Predefinito';

$ab_edit_contact = 'Selezionare \'Ok\' per salvare le modifiche. I campi marcati con un * sono obbligatori';

$ab_browse = 'Sfoglia';
$ab_comment = 'Commento';

$ab_contact = 'Contatto';
$ab_send_message = 'Invia E-Mail';
$ab_new_event = 'Nuovo appuntamento';

$ab_templates = 'Templates';
$ab_your_templates = 'Tuo';
$ab_all_templates = 'Tutti';

$template_type[0] = 'E-mail';
$template_type[1] = 'OpenOffice.org';
$ab_type = 'Tipo';
$ab_template_exists = 'A template with this name already exists.';

$sir_madam['M'] = 'signore';
$sir_madam['F'] = 'signora';

$ab_upload_oo = 'Upload OpenOffice.org template';
$ab_upload_oo_text = 'When you finnished with editting your OpenOffice.org template you should transfer it back to Group-Office. Select the file and click at \'Upload\' to transfer your template.';
$ab_upload_nofile = 'Error while sending file.';

$ab_oo_doc = 'Textdocument';
$ab_select_template = 'Select the template for this document:';
$ab_add_field_data = 'You can use the following values in your document:';
$ab_datafield = 'Datafield';
$ab_no_template = 'No template';

$ab_new_note = 'Nota';
$ab_events = 'Appuntamenti';

$ab_new_contact = 'Nuovo&nbsp;contatto';
$ab_new_company = 'Nuova&nbsp;società';
$ab_companies = 'Società';
$ab_company_properties = 'Profilo&nbsp;società';
$ab_bank_no = 'Conto&nbsp;bancario';
$ab_companies_count_single = 'società';
$ab_companies_count = 'società';
$ab_no_companies = 'Nessuna società trovata';
$ab_no_company = 'Nessuna società trovata';

$ab_import_type = 'Importa';
$ab_no_company_found = 'Nessuna società trovata';
$ab_search_company = 'Cerca società';
$ab_edit_company = 'Modifica profilo società';
$ab_new_group = 'Nuovo gruppo';
$ab_export_type = 'Esporta';

$ab_custom_fields = 'Dati&nbsp;extra';
$ab_field_size = 'Dimensione';

$ab_new_field = 'Nuovo campo dati';
$ab_invalid_field_size = 'Dimensione non valida. Seleziona un valore tra 10 e 255.';
$ab_field_exists = 'Un campo con questo nome esiste già.';

$ab_employees = 'Impiegati';
$ab_no_employees = 'Nessun impiegato trovato';

$ab_employee_count_single = 'impiegato';
$ab_employee_count_multiple = 'impiegati';
$ab_vat_no = 'Partita IVA';

$ab_add_new = 'Aggiungi nuovo';
$ab_add_existing = 'Aggiungi esistente';

$ab_access = 'Accesso';

//1.98
// non GPL 
$ab_mailings = 'Mailings';
$ab_mailing_groups = 'Mailing groups';
$ab_no_mailing_groups = 'NO mailing groups found';
$ab_no_templates = 'NO templates found';
$ab_mailing_group_exists = 'A mailing group with this name already exists.';
$ab_contacts_count_single = 'contact';
$ab_contacts_count_multiple = 'contacts';
$ab_new_mailing = 'New mailing';


//1.98
$ab_all_your_addressbooks = 'tutte le rubriche';
$ab_search_for = 'Ricerca per ';
// $ab_search_in = ' in ';
$ab_search_on = ' nel campo ';
$ab_search_keyword = 'Chiave di ricerca';
$ab_wildcard = '(Usa \'%\' come carattere generico)';

//2.03
$ab_copy_read_acl = 'Copia i permessi di lettura ai contatti e alle compagnie';
$ab_copy_write_acl = 'Copia i permessi di scrittura ai contatti e alle compagnie';

$ab_catagory_exists = 'Una categoria con questo nome esiste già.';
$ab_add_catagory = 'Aggiungi categoria';
$ab_add_field = 'Aggiungi campo dati';

$ab_catagory = 'Categoria';
$ab_category = 'Categoria'; 

//2.04
$ab_todos = 'Attività';

//2.05
$ab_download = 'Download';
$ab_download_template = 'Seleziona il pulsante \'Download\' per scaricare il template nel PC e modificarlo.';
$ab_upload = 'Importa';
$ab_file_type = 'Tipo di file';
$ab_import_vcf_file_ok = 'L\'importazione del file è conclusa. Seleziona \'Ok\' per aggiornare la Rubrica.';
$ab_import_failed = 'Importazione non riuscita.';

//2.06
$ab_select_mailing_groups = 'Selezionare i gruppi di posta a cui appartiene questo contatto';

//2.08
$ab_activities = 'Attività';
$ab_new_project = 'Nuovo progetto';
$ab_new_todo = 'Nuova attività';

//2.11
$ab_move_to_ab = 'Sposta i contatti selezionati nella rubrica...';
$ab_move_com_to_ab = 'Sposta le compagnie selezionate nella rubrica...';

//2.12
$ab_search_sender = 'Cerca e-mail';

$ab_contact_exists = 'Questa contatto sembra esistere già. Seleziona %s per andare nei contatti esistenti oppure seleziona \'Ok\' o \'Applica\' nuovamente per salvare comunque il contatto.';
$ab_company_exists = 'Questa compagnia sembra esistere già. Seleziona %s per andare nelle compagnie esistenti oppure seleziona \'Ok\' o \'Applica\' nuovamente per salvare comunque la compagnia.';
$ab_here = 'qui';

//2.13
$ab_search_for_keyword = ' con ';

$ab_search_in = 'Cerca in'; //changed
$ab_search_on_users = 'Cerca';

$ab_users_count_single='utente';
$ab_users_count_multiple='utenti';
$ab_no_users='Utenti non trovati';
?>
