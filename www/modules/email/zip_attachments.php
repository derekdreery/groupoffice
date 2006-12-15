<?php
/*
Copyright Intermesh 2003
Author: Merijn Schering <mschering@intermesh.nl>
Version: 1.0 Release date: 08 July 2003

This program is free software; you can redistribute it and/or modify it
under the terms of the GNU General Public License as published by the
Free Software Foundation; either version 2 of the License, or (at your
option) any later version.
*/

require_once("../../Group-Office.php");
$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('email');

require_once($GO_CONFIG->class_path."mail/imap.class.inc");
require_once($GO_MODULES->class_path."email.class.inc");
$mail = new imap();
$email = new email();

require_once ($GO_LANGUAGE->get_language_file('email'));

$browser = detect_browser();
//header('Content-Length: '.strlen($file));
header('Expires: '.gmdate('D, d M Y H:i:s') . ' GMT');
if ($browser['name'] == 'MSIE')
{
	header('Content-Type: application/download');
	header('Content-Disposition: attachment; filename="'.$ml_attachments.'.zip"');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
}else
{
	header('Content-Type: application/download');
	header('Pragma: no-cache');
	header('Content-Disposition: attachment; filename="'.$ml_attachments.'.zip"');
}
header('Content-Transfer-Encoding: binary');
echo $email->get_zip_of_attachments($_REQUEST['account_id'],$_REQUEST['uid'], $_REQUEST['mailbox']);
?>
