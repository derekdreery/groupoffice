<?php
require_once($GO_LANGUAGE->get_fallback_language_file('users'));

$strLogins = "Connessioni";
$strRegistrationDate = "Data di registrazione";
$strUsers = "utente/i";
$user_profile = "Profilo Utente";
$ac_login_info = "Informazioni di connessione";
$ac_registration_time = "Ora di registrazione";
$ac_lastlogin = "Ultima connessione";
$ac_logins = "Totale connessioni";
$strMaxOf = "registrato da un massimo di";
$strUsername = "Nome utente";
$delete_fail = "Non puoi eliminare te stesso o l'amministratore.";
$admin_new_user = "Nuovo utente";

$admin_modules = 'Moduli';
$admin_module_access = 'Questo utente ha accesso ai seguenti moduli:';

$admin_password = 'Password';
$admin_confirm_password = 'Conferma password';
$admin_change_password = 'Cambia password';
$admin_pass_match = 'Le password non corrispondono. Provare nuovamente.';

//registration
$registration_personal_info = "Informazioni personali";
$registration_account_info = "Informazioni account";
$registartion_username_text = "Inserire nomeutente e password di questo utente per entrare nel sito.";

$registration_question_text=<<<registration_question_text
Inserisci una domanda segreta alla quale solo questo utente potrà rispondere. Vi faremo questa domanda solo nel caso l'utente dimentichi la password.
Quando verrà fornita la risposta corretta, un messaggio verrà inviato all'indirizzo e-mail specificato nella registrazione contenente il nome utente e la nuova password per connettersi.
registration_question_text;

$registration_text=<<<registration_text
Compila questa form e seleziona 'ok' per confermare. I campi contrassegnati con * sono obbligatori.<br />
Le informazioni che fornirai sono ad uso esclusivo di Group-Office, non verranno divulgate a terze parti.
registration_text;

$registartion_authcode_text = "Inserisci un codice di autorizzazione. Il codice di autorizzazione è necessario ad altri utenti che necessitano di aggiungere questo utente nei loro contatti e vederne i relativi dettagli.";
$registartion_confirm_authcode = "Conferma codice di autorizzazione";
$registration_visibility = "Rendi questo utente visibile a tutti.";
$registration_success = "<h1>Congratulazioni!</h1>Sei stato registrato correttamente ed un messaggio di benvenuto ti è stato inviato a:";
$registration_failure = "Si è verificato un errore nel tentativo di memorizzare le tue informazioni. Riprovare nuovamente.";
//$registration_mail_subject = "Benvenuto in Group-Office.";
$registration_folder_error = "Creazione della cartella personale fallita. Contattaci per ripristinarla!";
//$registration_title = "Registrazione in Group-Office";
$registration_mail_body=<<<registration_mail_body

Gentile %title% %last_name%,

lei è appena stato iscritto nel \"Group-Office\", 
speriamo che il servizio sia di suo gradimento.

Nome utente: %username%
Password: %password%


Selezioni il collegamento qui sotto per connettersi:

registration_mail_body;
$registration_email = "Crea un indirizzo E-mail per questo utente di Group-Office.";
$registration_samba = "File e Print server.";
$registration_email_error = "Si è verificato un errore nella creazione del tuo account E-mail. Contattaci per l'assistenza.";
$strPasswordConfirm = "Conferma password";
$strPassword = "Password";

$reg_company_info = "Infornazioni Società";

$max_user_limit = "Massimo numero di utenti registrabili.";
$max_users_text = "Spiacenti, non possiamo registrare altri utenti. Contattaci sei vuoi aumentare il tuo numero di utenti.";
$ml_sent_items = "Elementi spediti";
$ml_spam = "Spam";
$ac_samba_user = "L'utente ha accesso al file e print server.";
$ml_email_only = "Richiesto solamente quando non crei un indirizzo E-mail";

//translation required 1.93

$register_new_user = 'Nuova sottoscrizione Group-Office';
$register_thanks = '<h2>Grazie</h2>. La tua sottoscrizione verrà elaborata il più presto possibile. Successivamente ne riceverai la notifica.';
$register_accept = 'Aggiungi negli utenti';
$register_addressbook = 'Aggiungi nella rubrica';
$register_title = 'Sottoscrivi';

//1.98
$reg_language = 'Lingua';
$reg_theme = 'Tema';

//2.05
$admin_use = 'Utilizzo';
$admin_module = 'Modulo';
$admin_manage = 'Gestione';

$admin_groups = 'Gruppi di utenti';
$admin_groups_user = 'L\'utente è membro dei seguenti gruppi di utenti';


//2.13
$registration_mail_subject = "Benvenuto in %groupoffice_title%"; //changed string
$registration_title = "Registrati in %groupoffice_title%"; //changed string

$lang_modules['users'] = 'Utenti';

//2.14
$users_enabled = 'Account abilitato';

$admin_groups_visible = 'L\'utente e\' visibile per i seguenti gruppi';
$admin_look_feel = 'Aspetto';
