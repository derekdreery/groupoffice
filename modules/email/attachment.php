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

require_once($GO_MODULES->modules['email']['class_path']."cached_imap.class.inc.php");
require_once($GO_MODULES->modules['email']['class_path']."email.class.inc.php");
require_once($GO_CONFIG->class_path."mail/mimeDecode.class.inc");
$imap = new cached_imap();
$email = new email();


if(!empty($_REQUEST['filepath'])){
	//message is cached on disk
	$path = $GO_CONFIG->file_storage_path.$_REQUEST['filepath'];

	if(File::path_leads_to_parent($path) || !file_exists($path)){
		die('Invalid request');
	}
	$params['input'] = file_get_contents($path);
	$params['include_bodies'] = true;
	$params['decode_bodies'] = true;
	$params['decode_headers'] = false;

	$part = Mail_mimeDecode::decode($params);

	$parts_arr = explode('.',$_REQUEST['imap_id']);
	for($i=0;$i<count($parts_arr);$i++) {
		if(isset($part->parts[$parts_arr[$i]])){
			$part = $part->parts[$parts_arr[$i]];
		}else{
			go_debug('Mime part not found!');
			go_debug($_REQUEST);
			die('Part not found');
		}
	}	
	$data = $part->body;
	unset($part);

}else
{
	$account = $imap->open_account($_REQUEST['account_id'], $_REQUEST['mailbox']);

	if(!$GO_SECURITY->has_permission($GO_SECURITY->user_id,$account['acl_id'])) {
		die($lang['common']['accessDenied']);
	}


	if(!empty($_REQUEST['uuencoded_partnumber']) && $_REQUEST['uuencoded_partnumber']!='undefined') {
		$data = $imap->get_message_part_decoded($_REQUEST['uid'], $_REQUEST['imap_id'], $_REQUEST['encoding'], false);

		$attachments = $imap->extract_uuencoded_attachments($data);

		$data = convert_uudecode($attachments[$_REQUEST['uuencoded_partnumber']-1]['data']);
		$size = strlen($data);
	}

	$extension = File::get_extension($_REQUEST['filename']);

	if($GO_MODULES->has_module('gnupg') && ($extension=='pgp' || $extension=='gpg')) {
		require_once ($GO_MODULES->modules['gnupg']['class_path'].'gnupg.class.inc.php');
		$gnupg = new gnupg();

		$tmpfile = $GO_CONFIG->tmpdir.$_REQUEST['filename'];

		$_REQUEST['filename']=File::strip_extension($_REQUEST['filename']);
		$file=$GO_CONFIG->tmpdir.$_REQUEST['filename'];
		$fp = fopen($tmpfile, 'w+');

		if(!$fp)
			die('Could not write to temp file');

		$imap->get_message_part_start($_REQUEST['uid'], $_REQUEST['imap_id']);

		while($line = $imap->get_message_part_line()){

			switch(strtolower($_REQUEST['encoding'])) {
				case 'base64':
					$line=base64_decode($line);
					break;
				case 'quoted-printable':
					$line= quoted_printable_decode($line);
					break;
			}

			if(!fputs($fp, $line))
				die('Could not write to temp file');
		}

		fclose($fp);

		$passphrase=isset($_SESSION['GO_SESSION']['gnupg']['passwords'][$_REQUEST['sender']]) ? $_SESSION['GO_SESSION']['gnupg']['passwords'][$_REQUEST['sender']] : '';

		$gnupg->decode_file($tmpfile, $file, $passphrase);
		unlink($tmpfile);


		//$file = file_get_contents($outfile);
	}else
	{
		$size = $imap->get_message_part_start($_REQUEST['uid'], $_REQUEST['imap_id']);
		//exit($size);
	}
}

$browser = detect_browser();

header("Expires: " . date("D, j M Y G:i:s ", time()+(86400*14)) . 'GMT');//expires in 2 weeks
header('Cache-Control: cache');
header('Pragma: cache');	
if ($browser['name'] == 'MSIE') {
	header('Content-Type: application/download');
	header('Content-Disposition: attachment; filename="'.rawurlencode($_REQUEST['filename']).'";');
}else {
	$mime = File::get_mime($_REQUEST['filename']);

	header('Content-Type: '.$mime);
	header('Content-Disposition: attachment; filename="'.$_REQUEST['filename'].'"');
}
header('Content-Transfer-Encoding: binary');

//unfortunately we don't know the size because file is not decoded yet
//header('Content-Length: '.$size);


if(isset($file)){
	//tmp file from gnupg
	readfile($file);
	unlink($file);
}elseif(isset($data)){
	echo $data;
}else
{
	//read from IMAP server
	while($line = $imap->get_message_part_line()){
		switch(strtolower($_REQUEST['encoding'])) {
			case 'base64':
				echo base64_decode($line);
				break;
			case 'quoted-printable':
				echo quoted_printable_decode($line);
				break;
			default:
				echo $line;
				break;
		}
	}
}
$imap->disconnect();