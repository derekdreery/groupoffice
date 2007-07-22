<?php
//Uncomment this line in new translations!
require_once($GO_LANGUAGE->get_fallback_language_file('users'));

$strLogins = "Logins ialt";
$strRegistrationDate = "Oprettet";
$strUsers = "bruger(e)";
$user_profile = "Brugerprofil";
$ac_login_info = "Login information";
$ac_registration_time = "Oprettet";
$ac_lastlogin = "Sidste login";
$ac_logins = "Totalt antal logins";
$strMaxOf = "oprettet fra et maksimalt antal af ";
$strUsername = "Brugernavn";
$delete_fail = "Du kan ikke slette din egen konto eller superbrugeren.";
$admin_new_user = "Ny bruger";

$admin_modules = 'Moduler';
$admin_module_access = 'Denne bruger har adgang til følgende moduler';

$admin_password = 'Adgangskode';
$admin_confirm_password = 'Bekræft adgangskode';
$admin_change_password = 'Skift adgangskode';
$admin_pass_match = 'Adgangskoderne er ikke ens. Prøv venligst igen.';

//registration
$registration_personal_info = "Personlig information";
$registration_account_info = "Konto information";
$registartion_username_text = "Indtast brugernavn og adgangskode for denne bruger.";

$registration_question_text=<<<registration_question_text
Indtast et hemmeligt spørgsmål, som kun brugeren kender svaret på. Spørgsmålet stilles i tilfælde af at brugeren glemmer sit password.
Hvis det rigtige svar gives, sendes en besked til den registrerede e-mailadresse, med brugernavn og en ny adgangskode der kan bruges til login.
registration_question_text;

$registration_text=<<<registration_text
Indtast brugerinformation og klik 'Ok' for at oprette en ny bruger. Felter markeret med * skal udfyldes.<br />
Information der indtastes bruges kun i Group-Office og vil ikke blive videregivet til andre.
registration_text;

$registartion_authcode_text = "Indtast en autorisationskode. Koden kan bruges af andre brugere, hvis de ønsker at tilføje denne bruger til deres kontaktliste og se dine persondata.";
$registartion_confirm_authcode = "Bekræft autorisationskode";
$registration_visibility = "Gør brugeren synlig for alle brugere.";
$registration_success = "<h1>Tillykke!</h1>Du er nu registreret og en velkomstbesked er sendt til:";
$registration_failure = "Der opstod en fejl ved registrering af dine persondata. Prøv venligst igen.";
$registration_mail_subject = "Velkommen til Group-Office.";
$registration_folder_error = "Fejl under oprettelse af din personlige mappe. Kontakt systemadministratoren!";
$registration_title = "Registrering i Group-Office";

$registration_email = "Opret en e-mail adresse for denne Group-Office bruger.";
$registration_samba = "Fil & print server.";
$registration_email_error = "Der opstod en fejl under oprettelsen af din e-mail konto. Kontakt systemadministratoren.";
$strPasswordConfirm = "Bekræft adgangskode";
$strPassword = "Adgangskode";

$reg_company_info = "Firmainformation";

$max_user_limit = "Det maksimale antal brugere er nået.";
$max_users_text = "Beklager, der kan ikke registreres flere brugere. Kontakt systemadministratoren hvis du ønsker at øge antallet af brugere.";
$ml_sent_items = "Sendte beskeder";
$ml_spam = "Spam";
$ac_samba_user = "Brugeren har adgang til fil og print serveren.";
$ml_email_only = "Kun påkræves hvis du ikke opretter en e-mail adresse";
$register_new_user = 'Ny Group-Office tilmelding';
$register_thanks = '<h2>Tak</h2>Din tilmelding vil blive behandlet så snart som muligt. Du vil blive kontaktet.';
$register_accept = 'Tilføj til brugere';
$register_addressbook = 'Tilføj til adressebog';
$register_title = 'Tilmeld';
$registration_mail_body=<<<registration_mail_body
Kære %title% %last_name%,

Du er blevet registreret som bruger af Group-Office.
Vi håber du vil få glæde af denne service.

Du kan logge ind med følgende:

Brugernavn: %username%
Adgangskode: %password%

Klik på følgende link for at logge ind:

registration_mail_body;

//1.98
$reg_language = 'Sprog';
$reg_theme = 'Tema';

//2.05
$admin_use = 'Brug';
$admin_module = 'Modul';
$admin_manage = 'Administrer';

$admin_groups = 'Brugergrupper';
$admin_groups_user = 'Bruger er medlem af følgende brugergrupper';
