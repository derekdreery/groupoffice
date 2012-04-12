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


//load Group-Office
require_once("../../Group-Office.php");

require_once($GLOBALS['GO_CONFIG']->class_path."mail/mimeDecode.class.inc");

//authenticate the user
$GLOBALS['GO_SECURITY']->authenticate();


$uid = (isset($_REQUEST['uid'])) ? $_REQUEST['uid'] : 0;
$imap_id = (isset($_REQUEST['imap_id'])) ? $_REQUEST['imap_id'] : 0;
$tmp_dir = $GLOBALS['GO_CONFIG']->tmpdir.'attachments/';

if(isset($_SESSION['GO_SESSION']['tmp_attachments']))
{
	$tmp_name = $tmp_dir.$uid.'_'.$imap_id.'.eml';
	if((!in_array($tmp_name, $_SESSION['GO_SESSION']['tmp_attachments'])) || (!file_exists($tmp_name)))
	{
		unset($tmp_name);
	}
}

if(isset($_REQUEST['path'])) {
	$path = $GLOBALS['GO_CONFIG']->file_storage_path.$_REQUEST['path'];

	if(File::path_leads_to_parent($path) || !file_exists($path)){
		die('Invalid request');
	}

	$params['input'] = file_get_contents($path);
}else
if(isset($tmp_name))
{	
	$params['input'] = file_get_contents($tmp_name);
}else{	

	require_once($GLOBALS['GO_MODULES']->modules['email']['class_path']."cached_imap.class.inc.php");
	require_once($GLOBALS['GO_MODULES']->modules['email']['class_path']."email.class.inc.php");
	$imap = new cached_imap();
	$email = new email();

	$account = $imap->open_account($_REQUEST['account_id'], $_REQUEST['mailbox']);		

	$charset = isset($_REQUEST['charset']) ? $_REQUEST['charset'] : false;
	if ($account) {
		$params['input'] = $imap->get_message_part_decoded($uid, $imap_id, $_REQUEST['encoding'], $charset);
		$imap->disconnect();

		if($params['input'])
		{
			if(!file_exists($tmp_dir))
			{
				require_once($GLOBALS['GO_CONFIG']->class_path.'filesystem.class.inc');
				filesystem::mkdir_recursive($tmp_dir);
			}

			$tmp_name = $tmp_dir.$uid.'_'.$imap_id.'.eml';
			file_put_contents($tmp_name, $params['input']);

			if(!isset($_SESSION['GO_SESSION']['tmp_attachments']))
			{
				$_SESSION['GO_SESSION']['tmp_attachments'] = array();
			}
			if(!in_array($tmp_name, $_SESSION['GO_SESSION']['tmp_attachments']))
			{
				$_SESSION['GO_SESSION']['tmp_attachments'][] = $tmp_name;
			}
		}
	}
}


//close writing to session so other concurrent requests won't be locked out.
session_write_close();

$params['include_bodies'] = true;
$params['decode_bodies'] = true;
$params['decode_headers'] = true;


$part = Mail_mimeDecode::decode($params);

$parts_arr = explode('.',$_REQUEST['part_imap_id']);
for($i=0;$i<count($parts_arr);$i++) {
	if(isset($part->parts[$parts_arr[$i]])){
		$part = $part->parts[$parts_arr[$i]];
	}else{
		go_debug('Mime part not found!');
		go_debug($_REQUEST);
		die('Part not found');
	}
}


$filename = 'attachment';
if(!empty($part->ctype_parameters['name'])) {
	$filename = $part->ctype_parameters['name'];
}elseif(!empty($part->d_parameters['filename']) ) {
	$filename = $part->d_parameters['filename'];
}elseif(!empty($part->d_parameters['filename*'])) {
	$filename=$part->d_parameters['filename*'];
}

$content_transfer_encoding = isset($part->headers['content-transfer-encoding']) ? $part->headers['content-transfer-encoding'] : '';
$browser = detect_browser();

header('Content-Length: '.strlen($part->body));
header("Expires: " . date("D, j M Y G:i:s ", time()+(86400*14)) . 'GMT');//expires in 2 weeks
header('Cache-Control: cache');
header('Pragma: cache');

if ($browser['name'] == 'MSIE') {
	header('Content-Type: application/download');
	header('Content-Disposition: attachment; filename="'.$filename.'"');
}else {
	header('Content-Type: '.File::get_mime($filename));
	header('Content-Disposition: attachment; filename="'.$filename.'"');
}
header('Content-Transfer-Encoding: binary');
if ($content_transfer_encoding == 'base_64') {
	echo base64_encode($part->body);
}else {
	echo ($part->body);
}