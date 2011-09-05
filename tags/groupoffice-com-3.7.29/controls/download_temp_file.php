<?php

//Server and client send the session ID in the URL
if(isset($_REQUEST['sid'])) {
	session_id($_REQUEST['sid']);
}
require_once("../Group-Office.php");

$GO_SECURITY->json_authenticate();

//close writing to session so other concurrent requests won't be locked out.
session_write_close();

$name = (isset($_REQUEST['name']) && $_REQUEST['name']) ? $_REQUEST['name'] : '';

$path = $GO_CONFIG->tmpdir.'attachments/';;
$file = $path.$name;

if(File::path_leads_to_parent($file))
{
	die('File not found: '.$file);
}

if(!$path || !file_exists($file)) {
	die('File not found: '.$file);
}

$browser = detect_browser();
$extension = File::get_extension($file['name']);

header('Content-Length: '.filesize($file));
header('Content-Transfer-Encoding: binary');

header("Last-Modified: ".gmdate("D, d M Y H:i:s", filemtime($file))." GMT");
header("ETag: ".md5_file($file));

if($browser['name'] == 'MSIE')
{
	header('Content-Type: application/download');
	header('Content-Disposition: attachment; filename="'.$name.'"');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
}else
{
	header('Content-Type: '.File::get_mime($file));
	header('Content-Disposition: attachment; filename="'.$name.'"');
	header('Pragma: no-cache');
}

readfile($file);
