<?php
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('users'));
$lang['users']['name'] = 'Uživatelé';
$lang['users']['description'] = 'Administrátorské prostředí pro úpravu uživatelů.';

$lang['users']['deletePrimaryAdmin'] = 'Nemůžete smazat hlavního administrátora';
$lang['users']['deleteYourself'] = 'Nemůžete smazat svůj účet';

$lang['link_type'][8]=$us_user = 'Uživatel';

$lang['users']['error_username']='Byly použity nevhodné znaky v uživatelském jménu';
$lang['users']['error_username_exists']='Omlouváme se, ale toto uživatelské jméno již existuje';
$lang['users']['error_email_exists']='Omlouváme se, ale tato emailová adresa již v systému existuje. Pokud jste zapomněli své heslo, můžete si ho nechat znovu poslat.';
$lang['users']['error_match_pass']='Hesla se neshodují';
$lang['users']['error_email']='Byla vložena špatná emailová adresa';

$lang['users']['imported']='Importováno %s uživatelů';
$lang['users']['failed']='Selhání';

$lang['users']['incorrectFormat']='Soubor nemá správný CSV formát';
