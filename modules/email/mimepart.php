<?php
/**
 * @copyright Copyright Intermesh 2006
 * @version $Revision: 1615 $ $Date: 2008-04-25 16:18:36 +0200 (vr, 25 apr 2008) $
 * 
 * @author Merijn Schering <mschering@intermesh.nl>

   This program is protected by copyright law and the Group-Office Professional license.

   You should have received a copy of the Group-Office Proffessional license
   along with Group-Office; if not, write to:
   
   Intermesh
   Reitscheweg 37
   5232BX Den Bosch
   The Netherlands   
   
   info@intermesh.nl
   
 * @package Templates
 * @category Addressbook
 */
 
//load Group-Office
require_once("../../Group-Office.php");

require_once($GO_CONFIG->class_path."mail/mimeDecode.class.inc");

//authenticate the user
$GO_SECURITY->authenticate();


if(isset($_REQUEST['path']))
{
	$path = smart_stripslashes($_REQUEST['path']);
	$params['input'] = file_get_contents($path);
}else
{
	
	require_once($GO_CONFIG->class_path."mail/imap.class.inc");
	require_once($GO_MODULES->modules['email']['class_path']."email.class.inc");
	$mail = new imap();
	$email = new email();

	$account = $email->get_account($_REQUEST['account_id']);

	if ($mail->open($account['host'], $account['type'],$account['port'],$account['username'],$account['password'], $_REQUEST['mailbox'], 0, $account['use_ssl'], $account['novalidate_cert']))
	{
		$params['input'] = $mail->view_part($_REQUEST['uid'], $_REQUEST['part'], $_REQUEST['transfer']);
		$mail->close();
	}
}

$params['include_bodies'] = true;
$params['decode_bodies'] = true;
$params['decode_headers'] = true;


$part = Mail_mimeDecode::decode($params);
$parts_arr = explode('.',$_REQUEST['part_number']);
for($i=0;$i<count($parts_arr);$i++)
{
	$part = $part->parts[$parts_arr[$i]];
}

$filename = isset($part->d_parameters['filename']) ? $part->d_parameters['filename'] : 'attachment';

$content_transfer_encoding = isset($part->headers['content-transfer-encoding']) ? $part->headers['content-transfer-encoding'] : '';
$browser = detect_browser();

header('Content-Length: '.strlen($part->body));
header('Expires: '.gmdate('D, d M Y H:i:s') . ' GMT');
if ($browser['name'] == 'MSIE')
{
	header('Content-Type: application/download');
	header('Content-Disposition: attachment; filename="'.$filename.'"');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
}else
{
	header('Content-Type: '.$part->ctype_primary.'/'.$part->ctype_secondary);
	header('Pragma: no-cache');
	header('Content-Disposition: attachment; filename="'.$filename.'"');
}
header('Content-Transfer-Encoding: binary');
if ($content_transfer_encoding == 'base_64')
{
	echo base64_encode($part->body);
}else
{
	echo ($part->body);
}
?>
