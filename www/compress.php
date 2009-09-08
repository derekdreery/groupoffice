<?php
require('Group-Office.php');

$file = $GO_CONFIG->local_path.'cache/'.$_REQUEST['file'];
go_log(LOG_DEBUG, 'Compressed: '.$file);

$ext = File::get_extension($file);

$type = $ext =='js' ? 'text/javascript' : 'text/css';

ob_start();
ob_start('ob_gzhandler');
$offset = 30*24*60*60;
header ("Content-Type: $type; charset: UTF-8");
header("Expires: " . date("D, j M Y G:i:s ", time()+$offset) . 'GMT');
header('Cache-Control: cache');
header('Pragma: cache');

readfile($file);

ob_end_flush();  // The ob_gzhandler one

header("Content-Length: ".ob_get_length());

ob_end_flush();  // The main one
?>
