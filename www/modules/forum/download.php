<?php

require("../../Group-Office.php");
//load file management class
$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('forum');

require_once($GO_CONFIG->class_path.'filesystem.class.inc');
$fs = new filesystem();

$path = smart_stripslashes($_REQUEST['path']);

$filename = basename($path);
$extension = get_extension($filename);

$browser = detect_browser();
header('Content-Length: '.filesize($path));
header('Expires: '.gmdate('D, d M Y H:i:s') . ' GMT');
if ($browser['name'] == 'MSIE')
{
	header('Content-Type: application/download');
	header('Content-Disposition: attachment; filename="'.$filename.'"');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
}else
{
	header('Content-Type: '.mime_content_type($path));
	header('Pragma: no-cache');
	header('Content-Disposition: attachment; filename="'.$filename.'"');
}
header('Content-Transfer-Encoding: binary');
$fd = fopen($path,'rb');
while (!feof($fd)) {
	print fread($fd, 32768);
}
fclose($fd);

?>
