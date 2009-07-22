<?php
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('users'));
$lang['users']['name'] = 'Brukere';
$lang['users']['description'] = 'Administratormodul: Behandle systembrukere.';

$lang['users']['deletePrimaryAdmin'] = 'Du kan ikke slette hovedadministrator';
$lang['users']['deleteYourself'] = 'Du kan ikke slette deg selv.';

$lang['link_type'][8]=$us_user = 'Bruker';

$lang['users']['error_username']='Du har ugyldige tegn i brukenavnet';
$lang['users']['error_username_exists']='Brukernavnet er opptatt!';
$lang['users']['error_email_exists']='E-postadressen er allerede registrert her!';
$lang['users']['error_match_pass']='Passordene er ikke like';
$lang['users']['error_email']='Du har angitt en ugyldig e-postadresse!';

$lang['users']['imported']='Importerte %s brukere';
$lang['users']['failed']='Feilet!';

$lang['users']['incorrectFormat']='Filen er ikke i riktig CSV format';
