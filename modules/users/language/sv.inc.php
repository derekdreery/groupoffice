<?php
/** 
 * Copyright Intermesh
 * 
 * This file is part of {product_name}. You should have received a copy of the
 * {product_name} license along with {product_name}. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @version $Id$
 * @author Merijn Schering <mschering@intermesh.nl>
 */

//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('users'));
$lang['users']['name'] = 'Användare';
$lang['users']['description'] = 'Adminmodul för att hantera användarkonton i {product_name}.';

$lang['users']['deletePrimaryAdmin'] = 'Du kan inte ta bort huvudadministratören';
$lang['users']['deleteYourself'] = 'Du kan inte ta bort dig själv';

$lang['link_type'][8]=$us_user = 'Användare';

$lang['users']['error_username']= 'Du har ogiltiga tecken i användarnamnet';
$lang['users']['error_username_exists']= 'Tyvärr, det användarnamnet finns redan';
$lang['users']['error_email_exists']= 'Tyvärr, den e-postadressen är redan registrerad.';
$lang['users']['error_match_pass']= 'Lösenorden matchade inte';
$lang['users']['error_email']= 'Du har angett en ogiltig e-postadress';
$lang['users']['error_user']='Användare kunde inte skapas';

$lang['users']['imported']= 'Importerade %s användare';
$lang['users']['failed']= 'Misslyckades';

$lang['users']['incorrectFormat']= 'Filen var inte i korrekt CSV-format';

$lang['users']['register_email_subject']='Detaljer för ditt konto i {product_name}';
$lang['users']['register_email_body']='Ett konto har skapats åt dig i {product_name} på {url}
Dina inloggningsuppgifter är:

Användarnamn: {username}
Lösenord: {password}';


$lang['users']['max_users_reached']='Max antal användare för det här systemet har uppnåtts.';