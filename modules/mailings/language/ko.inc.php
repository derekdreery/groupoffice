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
 * @version $Id: en.inc.php 6224 2010-11-19 13:36:54Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 */

//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('mailings'));
$lang['mailings']['name'] = '메일 템플릿과 주소 리스트';
$lang['mailings']['description'] = 'Adds e-mail templates and addresslists for sending newsletters to the addressbook.';

$lang['mailings']['templateAlreadyExists'] = 'The template you are trying to create already exists';
$lang['mailings']['mailingAlreadyExists'] = 'The mailing you are trying to create already exists';

$lang['mailings']['greet']='Best regards';

$lang['mailings']['unsubscribe']='Unsubscribe';
$lang['mailings']['unsubscription']='Click her to unsubscribe from this mailing.';
$lang['mailings']['r_u_sure'] = 'Are you sure you want to unsubscribe from the mailing?';
$lang['mailings']['delete_success'] = 'You have been successfully unsubscribed from the mailing.';
$lang['mailings']['setCurrentTemplateAsDefault']='Set current template as default';