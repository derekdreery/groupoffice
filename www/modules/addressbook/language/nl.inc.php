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
 * @version $Id: auth.class.inc.php 2157 2008-06-23 17:10:11Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 */

require_once($GO_LANGUAGE->get_fallback_language_file('addressbook'));
$lang['addressbook']['name'] = 'Adresboek';
$lang['addressbook']['description'] = 'Module om alle contacten te beheren.';

$lang['addressbook']['allAddressbooks'] = 'Alle Adresboeken';
$lang['common']['addressbookAlreadyExists'] = 'Het adresboek wat je probeert te maken bestaat al';
$lang['addressbook']['notIncluded'] = 'Niet importeren';

$lang['addressbook']['comment'] = 'Opmerking';
$lang['addressbook']['bankNo'] = 'Bankrekeningnummer'; 
$lang['addressbook']['vatNo'] = 'BTW-nummer';
$lang['addressbook']['contactsGroup'] = 'Groep';

$lang['link_type'][2]=$lang['addressbook']['contact'] = 'Contact';
$lang['link_type'][3]=$lang['addressbook']['company'] = 'Bedrijf';

$lang['addressbook']['customers'] = 'Klanten';
$lang['addressbook']['suppliers'] = 'Leveranciers';
$lang['addressbook']['prospects'] = 'Potentiële klanten';
?>