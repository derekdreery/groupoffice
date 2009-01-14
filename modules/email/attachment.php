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
require_once($GO_MODULES->class_path."email.class.inc");
$mail = new imap();
$email = new email();

$account = $email->get_account($_REQUEST['account_id']);

if($account['user_id']!=$GO_SECURITY->user_id)
{
	die($lang['common']['accessDenied']);
}



if ($mail->open($account['host'], $account['type'],$account['port'],$account['username'],$account['password'], $_REQUEST['mailbox'], null, $account['use_ssl'], $account['novalidate_cert']))
{
	$file = $mail->view_part($_REQUEST['uid'], $_REQUEST['part'], $_REQUEST['transfer']);
	$mail->close();
	
	if($_REQUEST['uuencoded_partnumber']>0)
	{
		$attachments = $mail->extract_uuencoded_attachments($file);
		
		$file = convert_uudecode($attachments[$_REQUEST['uuencoded_partnumber']-1]['data']);
	}

	$browser = detect_browser();
	
	//header('Content-Length: '.strlen($file));
	header('Expires: '.gmdate('D, d M Y H:i:s') . ' GMT');
	if ($browser['name'] == 'MSIE')
	{
		header('Content-Type: application/download');
		header('Content-Disposition: attachment; filename="'.rawurlencode($_REQUEST['filename']).'";');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
	}else
	{		
		$mime = File::get_mime($_REQUEST['filename']);
		
		header('Content-Type: '.$mime);
		header('Pragma: no-cache');
		header('Content-Disposition: attachment; filename="'.$_REQUEST['filename'].'"');
	}
	header('Content-Transfer-Encoding: binary');
	echo $file;
}else
{
	echo $lang['comon']['selectError'];
}
