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

//Polish Translation v1.0
//Author : Robert GOLIAT info@robertgoliat.com  info@it-administrator.org
//Date : January, 20 2009

//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('addressbook'));

$lang['addressbook']['name'] = 'Książka adresowa';
$lang['addressbook']['description'] = 'Moduł do zarządzania wszystkimi kontaktami.';



$lang['addressbook']['allAddressbooks'] = 'Wszystkie książki adresowe';
$lang['common']['addressbookAlreadyExists'] = 'Książka adresowa, którą próbujesz utworzyć juz istnieje';
$lang['addressbook']['notIncluded'] = 'Nie importuj';

$lang['addressbook']['comment'] = 'Uwagi';
$lang['addressbook']['bankNo'] = 'Nr konta bankowego'; 
$lang['addressbook']['vatNo'] = 'NIP';
$lang['addressbook']['contactsGroup'] = 'Grupa';

$lang['link_type'][2]=$lang['addressbook']['contact'] = 'Kontakt';
$lang['link_type'][3]=$lang['addressbook']['company'] = 'Firma';

$lang['addressbook']['customers'] = 'Klienci';
$lang['addressbook']['suppliers'] = 'Dostawcy';
$lang['addressbook']['prospects'] = 'Przyszłościowi';


$lang['addressbook']['contacts'] = 'Kontakty';
$lang['addressbook']['companies'] = 'Firmy';

$lang['addressbook']['newContactAdded']='Nowy kontakt został dodany';
$lang['addressbook']['newContactFromSite']='Nowy kontakt został dodany za pomocą formularza www.';
$lang['addressbook']['clickHereToView']='Kliknij tutaj by obejrzeć kontakt';
?>