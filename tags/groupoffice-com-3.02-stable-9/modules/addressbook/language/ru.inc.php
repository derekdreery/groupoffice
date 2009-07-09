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
 * @version $Id: en.inc.php 1131 2008-10-13 18:12:25Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 */
/**
 * Russian translation
 * By Valery Yanchenko (utf-8 encoding)
 * vajanchenko@hotmail.com
 * 10 December 2008
*/

//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('addressbook'));
$lang['addressbook']['name'] = 'Контакты';
$lang['addressbook']['description'] = 'Модуль для управления всеми контактами.';



$lang['addressbook']['allAddressbooks'] = 'Все адресные книги';
$lang['common']['addressbookAlreadyExists'] = 'Адресная книга, которую Вы хотели создать уже существует';
$lang['addressbook']['notIncluded'] = 'Не загружается';

$lang['addressbook']['comment'] = 'Коментарий';
$lang['addressbook']['bankNo'] = 'Банковские реквизиты'; 
$lang['addressbook']['vatNo'] = 'Банковские реквизиты2';
$lang['addressbook']['contactsGroup'] = 'Группа';

$lang['link_type'][2]=$lang['addressbook']['contact'] = 'Контакт';
$lang['link_type'][3]=$lang['addressbook']['company'] = 'Компания';

$lang['addressbook']['customers'] = 'Клиенты';
$lang['addressbook']['suppliers'] = 'Поставщики';
$lang['addressbook']['prospects'] = 'Предварительно';


$lang['addressbook']['contacts'] = 'Контакты';
$lang['addressbook']['companies'] = 'Компании';

$lang['addressbook']['newContactAdded']='Добавлен новый контакт';
$lang['addressbook']['newContactFromSite']='Добавлен новый контакт через WEB-форму.';
$lang['addressbook']['clickHereToView']='Нажмите здесь для просмотра контакта';
?>