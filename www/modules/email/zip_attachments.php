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


//close writing to session so other concurrent requests won't be locked out.
session_write_close();

require_once($GO_CONFIG->class_path."mail/imap.class.inc");
require_once($GO_MODULES->class_path."email.class.inc.php");
$mail = new imap();
$email = new email();

require_once ($GO_LANGUAGE->get_language_file('email'));

$filename = File::strip_invalid_chars($_REQUEST['filename']);
if(empty($filename))
	$filename=$lang['email']['attachments'];


$browser = detect_browser();
//header('Content-Length: '.strlen($file));
header('Expires: '.gmdate('D, d M Y H:i:s') . ' GMT');
header('Content-Disposition: attachment; filename="'.$filename.'.zip"');
if ($browser['name'] == 'MSIE')
{
	header('Content-Type: application/download');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
}else
{
	header('Content-Type: application/x-zip');
	header('Pragma: no-cache');
}
header('Content-Transfer-Encoding: binary');
$zip_file=$email->get_zip_of_attachments($_REQUEST['account_id'],$_REQUEST['uid'], $_REQUEST['mailbox']);

readfile($zip_file);

unlink($zip_file);
