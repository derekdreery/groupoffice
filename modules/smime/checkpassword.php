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

$GO_SECURITY->json_authenticate('smime');

require_once($GO_MODULES->modules['smime']['class_path'].'smime.class.inc.php');
$smime = new smime();

$cert = $smime->get_pkcs12_certificate($_POST['account_id']);

openssl_pkcs12_read ($cert['cert'], $certs, $_POST['password']);

$response['success'] = !empty($certs);
	
if($response['success'] )
{
	$_SESSION['GO_SESSION']['smime']['passwords'][$_POST['account_id']]=$_POST['password'];
}


echo json_encode($response);


