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
//require($GO_LANGUAGE->get_fallback_language_file('addressbook'));
$lang['addressbook']['name'] = 'Addressbook';
$lang['addressbook']['description'] = 'Module to manage all contacts.';



$lang['addressbook']['allAddressbooks'] = 'All Addressbooks';
$lang['common']['addressbookAlreadyExists'] = 'The addressbook you are trying to create already exists';
$lang['addressbook']['notIncluded'] = 'Do not import';

$lang['addressbook']['comment'] = 'Comment';
$lang['addressbook']['bankNo'] = 'Bank number'; 
$lang['addressbook']['vatNo'] = 'VAT number';
$lang['addressbook']['contactsGroup'] = 'Group';

$lang['link_type'][2]=$lang['addressbook']['contact'] = 'Contact';
$lang['link_type'][3]=$lang['addressbook']['company'] = 'Company';

$lang['addressbook']['customers'] = 'Customers';
$lang['addressbook']['suppliers'] = 'Suppliers';
$lang['addressbook']['prospects'] = 'Prospects';


$lang['addressbook']['contacts'] = 'Contacts';
$lang['addressbook']['companies'] = 'Companies';

$lang['addressbook']['newContactAdded']='New contact added';
$lang['addressbook']['newContactFromSite']='A new contact was added through a website form.';
$lang['addressbook']['clickHereToView']='Click here to view the contact';
?>