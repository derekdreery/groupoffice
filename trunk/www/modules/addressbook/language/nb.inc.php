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
require($GO_LANGUAGE->get_fallback_language_file('addressbook'));
$lang['addressbook']['name'] = 'Adressebok';
$lang['addressbook']['description'] = 'Modul for å håndtere alle kontakter.';



$lang['addressbook']['allAddressbooks'] = 'Alle adressebøker';
$lang['common']['addressbookAlreadyExists'] = 'Adresseboken du prøver å opprette eksisterer fra før.';
$lang['addressbook']['notIncluded'] = 'Ikke importer';

$lang['addressbook']['comment'] = 'Kommentar';
$lang['addressbook']['bankNo'] = 'Bankkonto'; 
$lang['addressbook']['vatNo'] = 'Organisasjonsnummer';
$lang['addressbook']['contactsGroup'] = 'Gruppe';

$lang['link_type'][2]=$lang['addressbook']['contact'] = 'Kontaktperson';
$lang['link_type'][3]=$lang['addressbook']['company'] = 'Firma';

$lang['addressbook']['customers'] = 'Kunder';
$lang['addressbook']['suppliers'] = 'Leverandører';
$lang['addressbook']['prospects'] = 'Prospekter';


$lang['addressbook']['contacts'] = 'Kontaktpersoner';
$lang['addressbook']['companies'] = 'Firmaer';

$lang['addressbook']['newContactAdded']='Ny kontaktperson er lagt til';
$lang['addressbook']['newContactFromSite']='En ny kontaktperson er lagt til via et nettstedskjema.';
$lang['addressbook']['clickHereToView']='Trykk her for å vise kontakpersonen';
?>