<?PHP
/*
Copyright Intermesh 2003
Author: Merijn Schering <mschering@intermesh.nl>
Version: 1.0 Release date: 08 July 2003

This program is free software; you can redistribute it and/or modify it
under the terms of the GNU General Public License as published by the
Free Software Foundation; either version 2 of the License, or (at your
option) any later version.
*/

require_once("../../Group-Office.php");

$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('mailings');
$browser = detect_browser();

$filename = intval($_REQUEST['mailing_id']).'.log';
$path = $GO_CONFIG->file_storage_path.'log/mailings/'.$filename;
header("Content-type: text/plain;charset=UTF-8");

if ($browser['name'] == 'MSIE')
{
	header('Content-Disposition: attachment; filename="'.$filename.'"');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
} else {
	header('Pragma: no-cache');
	header('Content-Disposition: attachment; filename="'.$filename.'"');
}

if(file_exists($path)){
	header('Content-Length: '.filesize($path));
	readfile($path);
}else
{
	echo 'Log file was not found on the server';
}
?>