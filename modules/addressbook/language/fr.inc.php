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
 *
 * French Translation v1.0
 * Author : Lionel JULLIEN
 * Date : September, 04 2008
 */

//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('addressbook'));

$lang['addressbook']['name'] = 'Carnet d\'adresses';
$lang['addressbook']['description'] = 'Module de gestion des contacts.';

$lang['addressbook']['allAddressbooks'] = 'Tous les carnets d\'adresses';
$lang['common']['addressbookAlreadyExists'] = 'Le carnet d\'adresses que vous essayez de créer existe déjà';
$lang['addressbook']['notIncluded'] = 'Ne pas importer';

$lang['addressbook']['comment'] = 'Commentaire';
//$lang['addressbook']['bankNo'] = 'Numéro de banque'; 
$lang['addressbook']['bankNo'] = 'Numéro SIRET'; 
$lang['addressbook']['vatNo'] = 'Numéro de TVA';
$lang['addressbook']['contactsGroup'] = 'Groupe';

$lang['link_type'][2]=$lang['addressbook']['contact'] = 'Contact';
$lang['link_type'][3]=$lang['addressbook']['company'] = 'Société';

$lang['addressbook']['customers'] = 'Clients';
$lang['addressbook']['suppliers'] = 'Fournisseurs';
$lang['addressbook']['prospects'] = 'Prospects';

$lang['addressbook']['contacts'] = 'Contacts';
$lang['addressbook']['companies'] = 'Sociétés';

// 3.02-stable-10
$lang['addressbook']['newContactAdded']='Nouveau contact ajouté';
$lang['addressbook']['newContactFromSite']='Un nouveau contact a été ajouté via le formulaire du site web.';
$lang['addressbook']['clickHereToView']='Cliquez ici pour voir le contact';
?>
