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

session_write_close();


if(isset($_REQUEST['template_id'])) {
	//load the questionnaires module class library
	require_once($GO_MODULES->modules['mailings']['class_path'].'templates.class.inc.php');
	$tp = new templates();

	//is there a template to load?
	$template_id = isset($_REQUEST['template_id']) ? $_REQUEST['template_id'] : 0;

	$template = $tp->get_template($template_id);
	$params['input'] = $template['content'];

}else if(isset($_REQUEST['path'])) {
	$path = $GO_CONFIG->file_storage_path.$_REQUEST['path'];

	if(File::path_leads_to_parent($path) || !file_exists($path)){
		die('Invalid request');
	}

	$params['input'] = file_get_contents($path);
}else {

	require_once($GO_MODULES->modules['email']['class_path']."cached_imap.class.inc.php");
	require_once($GO_MODULES->modules['email']['class_path']."email.class.inc.php");
	$imap = new cached_imap();
	$email = new email();
	
	$account = $imap->open_account($_REQUEST['account_id'], $_REQUEST['mailbox']);

	if ($account) {
		$params['input'] = $imap->get_message_part_decoded($_REQUEST['uid'], $_REQUEST['imap_id'], $_REQUEST['encoding'], $_REQUEST['charset']);
		$imap->disconnect();
	}
}

$params['include_bodies'] = true;
$params['decode_bodies'] = true;
$params['decode_headers'] = true;


$part = Mail_mimeDecode::decode($params);
$parts_arr = explode('.',$_REQUEST['part_number']);
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
?>
