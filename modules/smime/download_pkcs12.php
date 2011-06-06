<?php
/**
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * @version $Id: source.php 7354 2011-05-03 06:46:51Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 * @since Group-Office 1.0
 */
require_once("../../Group-Office.php");

session_write_close();

$GO_SECURITY->json_authenticate('smime');

require_once($GO_MODULES->modules['smime']['class_path'].'smime.class.inc.php');
$smime = new smime();

require_once($GO_MODULES->modules['email']['class_path'].'email.class.inc.php');
$email = new email();

$account = $email->get_account($_REQUEST['account_id']);

if(!$GO_SECURITY->has_permission($GO_SECURITY->user_id,$account['acl_id'])) {
	die($lang['common']['accessDenied']);
}

$cert = $smime->get_pkcs12_certificate($_REQUEST['account_id']);


header('Content-Type: application/x-pkcs12');
header('Content-Disposition: attachment; filename="'.str_replace(array('@','.'),'-',$account['username']).'.p12";');

echo $cert['cert'];
