<?php
/** 
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: download.php 2549 2009-05-21 12:10:19Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

require_once("../../Group-Office.php");
$GO_SECURITY->json_authenticate('addressbook');



$path = $GO_CONFIG->file_storage_path.'contacts/contact_photos/'.$_REQUEST['contact_id'].'.jpg';


$public=false;

$browser = detect_browser();

$extension = File::get_extension($path);

header('Content-Length: '.filesize($path));
header('Content-Transfer-Encoding: binary');

header("Last-Modified: ".gmdate("D, d M Y H:i:s", filemtime($path))." GMT");
header("ETag: ".md5_file($path));


if($public) {
	header("Expires: " . date("D, j M Y G:i:s ", time()+86400) . 'GMT');//expires in 1 day
	header('Cache-Control: cache');
	header('Pragma: cache');
}

if ($browser['name'] == 'MSIE') {
	header('Content-Type: application/download');
	header('Content-Disposition: inline; filename="'.basename($path).'"');

	if(!$public) {
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
	}
}else {
	header('Content-Type: '.File::get_mime($path));

	header('Content-Disposition: inline; filename="'.basename($path).'"');

	if(!$public) {
		header('Pragma: no-cache');
	}
}

readfile($path);

