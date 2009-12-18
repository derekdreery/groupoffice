<?php
/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 * @since Group-Office 1.0
 */

require_once("../../Group-Office.php");
$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('email');

require_once($GO_CONFIG->class_path."mail/imap.class.inc");
require_once($GO_MODULES->class_path."email.class.inc.php");
$mail = new imap();
$email = new email();

if(!is_executable($GO_CONFIG->cmd_zip))
{
	exit('Fatal error: ZIP compression not configured');
}


if(!is_executable($GO_CONFIG->cmd_tnef))
{
	exit('Fatal error: TNEF extraction not configured');
}

require_once ($GO_LANGUAGE->get_language_file('email'));


$account = $email->get_account($_REQUEST['account_id']);

if ($mail->open($account['host'], $account['type'],$account['port'],$account['username'],$account['password'], $_REQUEST['mailbox'], null, $account['use_ssl'], $account['novalidate_cert']))
{
	$file = $mail->view_part($_REQUEST['uid'], $_REQUEST['part'], $_REQUEST['transfer']);
	$mail->close();
}

$tmpdir = $GO_CONFIG->tmpdir.'groupoffice/'.$GO_SECURITY->user_id.'/mail/'.uniqid(time()).'/';
mkdir($tmpdir, $GO_CONFIG->folder_create_mode, true);

file_put_contents($tmpdir.'winmail.dat',$file);
chdir($tmpdir);
exec($GO_CONFIG->cmd_tnef.' winmail.dat');
unlink($tmpdir.'winmail.dat');

exec($GO_CONFIG->cmd_zip.' -r "tnef-attachments.zip" *.*');


$browser = detect_browser();
//header('Content-Length: '.strlen($file));
header('Expires: '.gmdate('D, d M Y H:i:s') . ' GMT');
if ($browser['name'] == 'MSIE')
{
	header('Content-Type: application/download');
	header('Content-Disposition: attachment; filename="'.$lang['email']['attachments'].'.zip"');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
}else
{
	header('Content-Type: '.File::get_mime($path));
	header('Pragma: no-cache');
	header('Content-Disposition: attachment; filename="'.$lang['email']['attachments'].'.zip"');
}
header('Content-Transfer-Encoding: binary');

readfile($tmpdir.'tnef-attachments.zip');
exec('rm -Rf '.$tmpdir);