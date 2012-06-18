<?php
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('users'));
$lang['users']['name'] = 'Käyttäjät';
$lang['users']['description'] = 'Hallintamoduuli; Hallitaan järjestelmän käyttäjiä.';

$lang['users']['deletePrimaryAdmin'] = 'Et voi poistaa järjestelmän pääkäyttäjää';
$lang['users']['deleteYourself'] = 'Et voi poistaa itseäsi';

$lang['link_type'][8]=$us_user = 'Käyttäjä';

$lang['users']['error_username']='Sinulla on sopimattomia merkkejä käyttäjänimessä';
$lang['users']['error_username_exists']='Valitettavasti tämä käyttäjänimi on jo käytössä';
$lang['users']['error_email_exists']='Valitettavasti tämä sähköpostiosoite on jo käytössä.';
$lang['users']['error_match_pass']='Salasana ei täsmää';
$lang['users']['error_email']='Syötit epäkelvon sähköpostiosoitteen';

$lang['users']['imported']='Tuotu %s käyttäjää';
$lang['users']['failed']='Epäonnistui';

$lang['users']['incorrectFormat']='Tiedosto ei ollut oikeassa CSV-formaatissa';