<?php
require_once($GO_LANGUAGE->get_fallback_language_file('users'));

$strLogins = "Totalt antall innlogginger";
$strRegistrationDate = "Opprettet";
$strUsers = "bruker(e)";
$user_profile = "Bruker profil";
$ac_login_info = "Login informatsjon";
$ac_registration_time = "Opprettet";
$ac_lastlogin = "Siste login";
$ac_logins = "Totalt antall innlogginger";
$strMaxOf = "registered from the maximum of";
$strUsername = "Brukernavn";
$delete_fail = "Du kan ikke slette ditt eget brukernavn eller superbruker.";
$admin_new_user = "Ny bruker";

$admin_modules = 'Moduler';
$admin_module_access = 'Denne bruker har adgang til følgende moduler';

$admin_password = 'Adgangskode';
$admin_confirm_password = 'Bekreft adgangskode';
$admin_change_password = 'Endre adgangskode';
$admin_pass_match = 'Adgangskodene er ikke like.';

//registration
$registration_personal_info = "Personlig informasjon";
$registration_account_info = "Konto informasjon";
$registartion_username_text = "Sett inn brukernavn og adgangskode for denne bruker.";

$registration_question_text=<<<registration_question_text
Sett inn ethemmelig spørsmål, som kun brukeren kjenner svaret på. Spørsmålet brukes i tilfelle brukeren har glemt sitt passord.
Hvis det rigtige svar blir gitt, sendes det en besked til den registrerte e-postadresse, med brukernavn og en ny adgangskode som kan brukes til innlogging.
registration_question_text;

$registration_text=<<<registration_text
Sett inn informasjon og klikk på 'OK' for å registrere en ny bruker. Feltene markeret med * må utfylles.<br />
Informasjon som settes inn brukes kun i Group-Office og vil ikke bli gitt til andre.
registration_text;

$registartion_authcode_text = "Sett inn en autorisasjonskode. Koden skal brukes av andre brukere, hvis de ønsker at legge til denne brukeren til kontaktlisten og se persondata.";
$registartion_confirm_authcode = "Bekreft autorisasjonskode";
$registration_visibility = "Gjør brukeren synlig for alle andre brukere.";
$registration_success = "<h1>Gratulerer!</h1>Du er nå registrert og det er sendt en velkomstbesked til:";
$registration_failure = "Det oppstod en feil ved registreringen av dine personinformasjon. Vennligst prøv igjen.";
$registration_mail_subject = "Velkommen til Group-Office.";
$registration_folder_error = "Feil under opprettelsen av din personlige mappe. Kontakt systemadministratoren!";
$registration_title = "Registrering i Group-Office";
$registration_mail_body=<<<registration_mail_body
Kjære Hr/Fru %name%,

Du er nå blitt registrert i Group-Office.
Vi håper du vil få glede av denne tjenesten.

Du kan logge inn med følgende:

Brukernavn: %username%
Passord: %password%

Klikk på snarveien nedenfor for å deg logge inn:

registration_mail_body;
$registration_email = "Oppret en mailadresse for denne Group-Office bruker.";
$registration_samba = "Fil & Print server.";
$registration_email_error = "Det oppstod en feil under opprettelsen av din brukerkonto. Kontakt systemadministratoren.";
$strPasswordConfirm = "Bekreft adgangskode";
$strPassword = "Adgangskode";

$reg_company_info = "Firmainformasjon";

$max_user_limit = "Det maksimale antall brukere er nådd.";
$max_users_text = "Beklager, det kan ikke registreres flere brukere. Kontakt systemadministratoren hvis du ønsker å øke antallet brukere.";
$ml_sent_items = "Sendte beskjeder";
$ml_spam = "Spam";
$ac_samba_user = "Brukeren har adgang til fil og print serveren.";
$ml_email_only = "Dette kreves kun hvis du ikke oppretter en e-postadresse";

$register_new_user = 'Ny Group-Office abbonent';
$register_thanks = '<h2>Tusen takk</h2>. Ditt abbonement vil bli behandlet snarest mulig. Du vil få videre beskjed når dette er klart.';
$register_accept = 'Legg til brukere';
$register_addressbook = 'Legg til adressebok';
$register_title = 'Abboner';


?>
