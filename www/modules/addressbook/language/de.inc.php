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
 * @version $Id: en.inc.php 2763 2008-08-20 12:50:57Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 */

//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('addressbook'));
$lang['addressbook']['name'] = 'Adressbuch';
$lang['addressbook']['description'] = 'Modul zum Verwalten von Kontakten';



$lang['addressbook']['allAddressbooks'] = 'Alle Adressbücher';
$lang['common']['addressbookAlreadyExists'] = 'Das Adressbuch existiert bereits';
$lang['addressbook']['notIncluded'] = 'Nicht importieren';

$lang['addressbook']['comment'] = 'Kommentar';
$lang['addressbook']['bankNo'] = 'Kontonummer'; 
$lang['addressbook']['vatNo'] = 'Steuernummer';
$lang['addressbook']['contactsGroup'] = 'Gruppe';

$lang['link_type'][2]=$lang['addressbook']['contact'] = 'Kontakt';
$lang['link_type'][3]=$lang['addressbook']['company'] = 'Firma';

$lang['addressbook']['customers'] = 'Kunden';
$lang['addressbook']['suppliers'] = 'Lieferanten';
$lang['addressbook']['prospects'] = 'Aussichten';


$lang['addressbook']['contacts'] = 'Kontakte';
$lang['addressbook']['companies'] = 'Firmen';

$lang['addressbook']['newContactAdded']='Neuen Kontakt erstellt';
$lang['addressbook']['newContactFromSite']='Sie haben einen neuen Kontakt über das Webseitenformular erhalten';
$lang['addressbook']['clickHereToView']='Klicken Sie hier, um den Kontakt anzusehen';
?>
