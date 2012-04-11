<?php
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('users'));
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
$lang['users']['error_user']='Uživatel nemůže být vytvořen';

$lang['users']['imported']='Importováno %s uživatelů';
$lang['users']['failed']='Selhání';

$lang['users']['incorrectFormat']='Soubor nemá správný CSV formát';

$lang['users']['register_email_subject']='Podrobnosti o Vašem {product_name} účtu';
$lang['users']['register_email_body']='{product_name} účet byl pro Vás vytvořen z {url}
Vaše přihlašovací údaje:

Uživatelské jméno: {username}
Heslo: {password}';


$lang['users']['max_users_reached']='Byl překročen maximální počet uživatelů v tomto systému.';
