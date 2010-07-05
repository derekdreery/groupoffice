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
require($GO_LANGUAGE->get_fallback_language_file('addressbook'));
$lang['addressbook']['name'] = 'Adressbok';
$lang['addressbook']['description'] = 'Modul för att hantera alla kontakter.';



$lang['addressbook']['allAddressbooks'] = 'Alla Addressböcker';
$lang['common']['addressbookAlreadyExists'] = 'Den Adressbok du försöker skapa finns redan';
$lang['addressbook']['notIncluded'] = 'Importera inte';

$lang['addressbook']['comment'] = 'Kommentar';
$lang['addressbook']['bankNo'] = 'Banknummer'; 
$lang['addressbook']['vatNo'] = 'Momsregistreringsnummer';
$lang['addressbook']['contactsGroup'] = 'Grupp';

$lang['link_type'][2]=$lang['addressbook']['contact'] = 'Kontakt';
$lang['link_type'][3]=$lang['addressbook']['company'] = 'Företag';

$lang['addressbook']['customers'] = 'Kunder';
$lang['addressbook']['suppliers'] = 'Leverantörer';
$lang['addressbook']['prospects'] = 'Framtidsutsikter';


$lang['addressbook']['contacts'] = 'Kontakter';
$lang['addressbook']['companies'] = 'Företag';

$lang['addressbook']['newContactAdded']= 'Ny kontakt tillagd';
$lang['addressbook']['newContactFromSite']= 'En ny kontakt har lagts till via et webbplats formulär.';
$lang['addressbook']['clickHereToView']= 'Klicka här för att visa kontakten';
?>