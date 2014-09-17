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

require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('addressbook'));

$lang['addressbook']['name'] = 'Adres Defteri';
$lang['addressbook']['description'] = 'Adres Defteri içersindeki Kişileri yöneten modül.';



$lang['addressbook']['allAddressbooks'] = 'Tüm Adres Defterleri';
$lang['common']['addressbookAlreadyExists'] = 'Oluşturmaya çalıştığınız Adres Defteri zaten mevcut';
$lang['addressbook']['notIncluded'] = 'İçeri aktarmayın';

$lang['addressbook']['comment'] = 'Görüş';
$lang['addressbook']['bankNo'] = 'Banka numarası'; 
$lang['addressbook']['vatNo'] = 'KDV numarası';
$lang['addressbook']['contactsGroup'] = 'Kişiler gurubu';

$lang['link_type'][2]=$lang['addressbook']['contact'] = 'Kişi';
$lang['link_type'][3]=$lang['addressbook']['company'] = 'Şirket';

$lang['addressbook']['customers'] = 'Müşteriler';
$lang['addressbook']['suppliers'] = 'Üreticiler';
$lang['addressbook']['prospects'] = 'Alıcılar';


$lang['addressbook']['contacts'] = 'Kişiler';
$lang['addressbook']['companies'] = 'Şirketler';

$lang['addressbook']['newContactAdded'] = 'Yeni Kişi eklendi';
$lang['addressbook']['newContactFromSite'] = 'Web sayfası formu üzerinden yeni bir Kişi eklenmiştir.';
$lang['addressbook']['clickHereToView'] = 'Kişiyi göstermek için burayı tıklayınız';
?>