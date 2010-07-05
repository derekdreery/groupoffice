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

require_once($GO_MODULES->modules['email']['class_path']."cached_imap.class.inc.php");
require_once($GO_MODULES->modules['email']['class_path']."email.class.inc.php");
$imap = new cached_imap();
$email = new email();

$account = $imap->open_account($_REQUEST['account_id'], $_REQUEST['mailbox']);

if($account['user_id']!=$GO_SECURITY->user_id) {
	die($lang['common']['accessDenied']);
}



$file = $imap->get_message_part_decoded($_REQUEST['uid'], $_REQUEST['imap_id'], $_REQUEST['encoding'], false);
$imap->disconnect();

if(!empty($_REQUEST['uuencoded_partnumber']) && $_REQUEST['uuencoded_partnumber']!='undefined') {
	$attachments = $imap->extract_uuencoded_attachments($file);

	$file = convert_uudecode($attachments[$_REQUEST['uuencoded_partnumber']-1]['data']);
}


if($GO_MODULES->has_module('gnupg')) {
	$extension = File::get_extension($_REQUEST['filename']);
	if($extension=='pgp' || $extension=='gpg') {
		require_once ($GO_MODULES->modules['gnupg']['class_path'].'gnupg.class.inc.php');
		$gnupg = new gnupg();

		$tmpfile = $GO_CONFIG->tmpdir.$_REQUEST['filename'];
		$_REQUEST['filename']=File::strip_extension($_REQUEST['filename']);
		$outfile = $GO_CONFIG->tmpdir.$_REQUEST['filename'];

		file_put_contents($tmpfile, $file);

		$passphrase=isset($_SESSION['GO_SESSION']['gnupg']['passwords'][$_REQUEST['sender']]) ? $_SESSION['GO_SESSION']['gnupg']['passwords'][$_REQUEST['sender']] : '';
		
		$gnupg->decode_file($tmpfile, $outfile, $passphrase);

		$file = file_get_contents($outfile);
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
header('Content-Length: '.strlen($file));

echo $file;

