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
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('addressbook'));
$lang['addressbook']['name'] = 'Aadressiraamat';
$lang['addressbook']['description'] = 'Kõikide kontaktide haldamise moodul.';



$lang['addressbook']['allAddressbooks'] = 'Kõik aadressiraamatud';
$lang['common']['addressbookAlreadyExists'] = 'Aadressiraamat, mida proovid lisada, on juba olemas';
$lang['addressbook']['notIncluded'] = 'Ära impordi';

$lang['addressbook']['comment'] = 'Kommenteeri';
$lang['addressbook']['bankNo'] = 'Pangakonto nr'; 
$lang['addressbook']['vatNo'] = 'Reg. nr';
$lang['addressbook']['contactsGroup'] = 'Grupp';

$lang['link_type'][2]=$lang['addressbook']['contact'] = 'Kontakt';
$lang['link_type'][3]=$lang['addressbook']['company'] = 'Ettevõte';

$lang['addressbook']['customers'] = 'Kliendid';
$lang['addressbook']['suppliers'] = 'Varustajad';
$lang['addressbook']['prospects'] = 'Võiamlikud kliendid';


$lang['addressbook']['contacts'] = 'Kontaktid';
$lang['addressbook']['companies'] = 'Ettevõtted';

$lang['addressbook']['newContactAdded']='Uus kontakt lisatud';
$lang['addressbook']['newContactFromSite']='Kodulehe vormi kaudu lisati uus kontakt.';
$lang['addressbook']['clickHereToView']='Kontakti vaatamiseks vaata siia';

$lang['addressbook']['contactFromAddressbook']='Kontakt %s';
$lang['addressbook']['companyFromAddressbook']='Ettevõte %s';
$lang['addressbook']['defaultSalutation']='Lugupeetud [hr./pr.] {middle_name} {last_name}';

$lang['addressbook']['multipleSelected']='Mitu aadressiraamatut valitud';
$lang['addressbook']['incomplete_delete_contacts']='Valitud kontaktide kustutamiseks puuduvad sul õigused';
$lang['addressbook']['incomplete_delete_companies']='Valitud ettevõtete kostutamiseks puuduvad sul õigused';
$lang['addressbook']['emailAlreadyExists']='E-posti aadress on sellele kontaktile juba lisatud';
$lang['addressbook']['emailDoesntExists']='E-posti aadressi ei leitud';
$lang['addressbook']['imageNotSupported']='Üles laetud pildi formaat ei ole lubatud. Ainult gif, png ja jpg on lubatud';
?>