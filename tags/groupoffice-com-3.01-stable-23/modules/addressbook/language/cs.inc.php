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
$lang['addressbook']['name'] = 'Adresář';
$lang['addressbook']['description'] = 'Adresář slouží k uložení kontaktů a k jejich úpravě.';



$lang['addressbook']['allAddressbooks'] = 'Všechny Adresáře';
$lang['common']['addressbookAlreadyExists'] = 'Snažíte se vytvořit adresář, který již existuje';
$lang['addressbook']['notIncluded'] = 'Neimportovat';

$lang['addressbook']['comment'] = 'Komentář';
$lang['addressbook']['bankNo'] = 'Číslo účtu'; 
$lang['addressbook']['vatNo'] = 'Daň z přidané hodnoty';
$lang['addressbook']['contactsGroup'] = 'Skupina';

$lang['link_type'][2]=$lang['addressbook']['contact'] = 'Kontakt';
$lang['link_type'][3]=$lang['addressbook']['company'] = 'Společnost';

$lang['addressbook']['customers'] = 'Zákazníci';
$lang['addressbook']['suppliers'] = 'Dodavatelé';
$lang['addressbook']['prospects'] = 'Perspektivy';


$lang['addressbook']['contacts'] = 'Kontakty';
$lang['addressbook']['companies'] = 'Společnosti';

$lang['addressbook']['newContactAdded']='Nový kontakt přidán';
$lang['addressbook']['newContactFromSite']='Nový kontakt byl přidán přes webový formulář.';
$lang['addressbook']['clickHereToView']='Klikni zde pro zobrazení kontaktu';
?>
