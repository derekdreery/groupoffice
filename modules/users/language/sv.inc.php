<?php
/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @version $Id: en.inc.php 1616 2008-12-17 16:16:28Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 */

//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('users'));
$lang['users']['name'] = 'Användare';
$lang['users']['description'] = 'Admin modul, Hanterar systemanvändarna.';

$lang['users']['deletePrimaryAdmin'] = 'Du kan inte bort den primära administratören';
$lang['users']['deleteYourself'] = 'Du kan inte ta bort dig själv';

$lang['link_type'][8]=$us_user = 'Användare';

$lang['users']['error_username']= 'Du har ogiltiga tecken i användarnamnet';
$lang['users']['error_username_exists']= 'Tyvärr, det användarnamnet finns redan';
$lang['users']['error_email_exists']= 'Tyvärr, den e-postadressen är redan registrerad här.';
$lang['users']['error_match_pass']= 'Lösenorden matchade inte';
$lang['users']['error_email']= 'Du har angett en ogiltig e-postadress';

$lang['users']['imported']= 'Importerade %s användare';
$lang['users']['failed']= 'Misslyckades';

$lang['users']['incorrectFormat']= 'Filen var inte i korrekt CSV-format';