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

$GO_SECURITY->html_authenticate();

require_once($GO_LANGUAGE->get_language_file('email'));
require_once($GO_CONFIG->class_path . "mail/imap.class.inc");
require_once($GO_MODULES->modules['email']['class_path'] . "cached_imap.class.inc.php");
require_once($GO_MODULES->modules['email']['class_path'] . "email.class.inc.php");

$imap = new cached_imap();
$email = new email();

require_once($GO_MODULES->modules['smime']['class_path'].'smime.class.inc.php');
$smime = new smime();

//if(empty($_REQUEST['filepath']))
if(!empty($_REQUEST['account_id'])){
	$account_id = $_REQUEST['account_id'];
	$mailbox = $_REQUEST['mailbox'];
	$uid = $_REQUEST['uid'];

	$account = $imap->open_account($_REQUEST['account_id'], $_REQUEST['mailbox']);

	if (!$GO_SECURITY->has_permission($GO_SECURITY->user_id, $account['acl_id'])) {
		die($lang['common']['accessDenied']);
	}


	$tmpdir = $GO_CONFIG->tmpdir . 'smime/verify/';
	File::mkdir($tmpdir);

	$src_filename = !empty($_REQUEST['filepath']) ? $GO_CONFIG->file_storage_path.$_REQUEST['filepath'] : $tmpdir . $uid . '_' . File::strip_invalid_chars($mailbox) . '_' . $account_id . '.eml';
	$cert_filename = $tmpdir . $uid . '_' . File::strip_invalid_chars($mailbox) . '_' . $account_id . '.crt';
	
	if (empty($_REQUEST['filepath']) && !file_exists($src_filename) && $uid>0){
	//if($uid>0){
		$imap->save_to_file($uid, $src_filename);
		
		$data = file_get_contents($src_filename);
		if(strpos($data, "application/pkcs7-mime") || strpos($data, "application/x-pkcs7-mime")) {		
			
			$cert = $smime->get_pkcs12_certificate($account_id);
			$password = $_SESSION['GO_SESSION']['smime']['passwords'][$account_id];			
			openssl_pkcs12_read ($cert['cert'], $certs, $password);
			
			$reldir='smimetmp/'.$GO_SECURITY->user_id.'/';
			$dir = $GO_CONFIG->file_storage_path.$reldir;
			File::mkdir($dir);

			$outfilename=$dir.'unencrypted.txt';

			$ret = openssl_pkcs7_decrypt($src_filename, $outfilename, $certs['cert'], array($certs['pkey'], $password));
			
			rename($outfilename, $src_filename);			
		}
		
	}

	if(!file_exists($src_filename))
	{
		die('Could not get message from IMAP server to verify the signature');
	}
	//go_debug(file_get_contents($src_filename));
	
	$valid = openssl_pkcs7_verify($src_filename, null, $cert_filename, smime::get_root_certificates());
	unlink($src_filename);
//	if(!file_exists($cert_filename))
//	{
//		die('Could not get certificate from signature');
//	}
	if($valid){
		if(file_exists($cert_filename)){
			$cert = file_get_contents($cert_filename);
			unlink($cert_filename);
		}
		
		if(empty($cert))
			die('Could not get certificate from signature');
	}

}else
{
	$cert = $smime->get_public_certificate_by_id($_REQUEST['cert_id']);
	$cert = $cert['cert'];
}

if(isset($cert)){
	$arr = openssl_x509_parse($cert);

	$email = String::get_email_from_string($arr['extensions']['subjectAltName']);

	$existing_cert = $smime->get_public_certificate($GO_SECURITY->user_id, $email);
	if(!$existing_cert)
		$smime->add_public_certificate($GO_SECURITY->user_id, $email, $cert);
	else if($existing_cert['cert']!=$cert){
		$smime->update_public_certificate($existing_cert['id'], $cert);
	}
}

$GO_LANGUAGE->require_language_file('smime');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<meta content="text/html; charset=UTF-8" http-equiv="Content-Type" />
		<title>SMIME certificate</title>
		<style>body {font: 12px arial;}</style>
	</head>
	<body>

<?php
if(isset($_REQUEST['account_id'])){
	if (!$valid) {
		echo '<h1 class="smi-invalid">'.$lang['smime']['invalidCert'].'</h1>';
	} else {
		echo '<h1 class="smi-valid">'.$lang['smime']['validCert'].'</h1>';
	}
}

if(!isset($_REQUEST['account_id']) || $valid){
	echo '<table>';
	echo '<tr><td width="100">'.$lang['common']['name'].':</td><td>' . $arr['name'] . '</td></tr>';
	echo '<tr><td width="100">E-mail:</td><td>' . $email. '</td></tr>';
	echo '<tr><td>Hash:</td><td>'.$arr['hash'].'</td></tr>';
	echo '<tr><td>Serial number:</td><td>'.$arr['serialNumber'].'</td></tr>';
	echo '<tr><td>Version:</td><td>'.$arr['version'].'</td></tr>';
	echo '<tr><td>Issuer:</td><td>';
	
	foreach ($arr['issuer'] as $skey => $svalue) {
		echo $skey . ':' . $svalue . '; ';
	}
	
	echo '</td></tr>';
	echo '<tr><td>Valid from:</td><td>' . Date::get_timestamp($arr['validFrom_time_t']) . '</td></tr>';
	echo '<tr><td>Valid to:</td><td>' . Date::get_timestamp($arr['validTo_time_t']) . '</td></tr>';
	echo '</table>';
}
?>
	</body>
</html>


