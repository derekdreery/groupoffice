<?php
require_once("../../Group-Office.php");

global $GO_SECURITY;
require_once($GO_CONFIG->class_path.'base/users.class.inc.php');
$GO_USERS = new GO_USERS();
$user = $GO_USERS->get_user($GO_SECURITY->user_id);

if (empty($_REQUEST['code']) || md5($user['username'].$user['password'].$_REQUEST['filename']) != $_REQUEST['code'])
	throw new AccessDeniedException();

$browser = detect_browser();

$path = $GO_CONFIG->tmpdir.'attachments/'.$_REQUEST['last_dir'].'/'.$_REQUEST['filename'];

$extension = File::get_extension($path);
$filename = utf8_basename($path);

//header('Content-Length: '.filesize($path));


//header("Expires: " . date("D, j M Y G:i:s ", time()+86400) . 'GMT');//expires in 1 day
//header('Cache-Control: cache');
//header('Pragma: cache');


header('Content-Type: '.File::get_mime($path));
header('Content-Disposition: attachment; filename="'.$filename.'"');

//header('Content-Transfer-Encoding: binary');

readfile($path);
