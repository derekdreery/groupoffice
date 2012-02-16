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
 * @version $Id$
 * @author Merijn Schering <mschering@intermesh.nl>
 */

//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('mailings'));
$lang['mailings']['name'] = 'Mallar och sändlistor';
$lang['mailings']['description'] = 'Modul för att hantera sändlistor samt e-post- och dokumentmallar.';

$lang['mailings']['templateAlreadyExists'] = 'Mallen du försöker skapa finns redan';
$lang['mailings']['mailingAlreadyExists'] = 'Sändlistan du försöker skapa finns redan';

$lang['mailings']['greet']= 'Med vänlig hälsning';

$lang['mailings']['unsubscribe']='Avsluta prenumeration';
$lang['mailings']['unsubscription']='Klicka här för att sluta prenumerera på det här nyhetsbrevet.';
$lang['mailings']['r_u_sure'] = 'Är du säker på att du vill avsluta din prenumeration av nyhetsbrevet?';
$lang['mailings']['delete_success'] = 'Din prenumeration av nyhetsbrevet har avslutats.';
$lang['mailings']['setCurrentTemplateAsDefault']='Sätt nuvarande mall som förval';