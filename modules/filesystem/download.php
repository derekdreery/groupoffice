<?php
/*
 Copyright Intermesh 2003
 Author: Merijn Schering <mschering@intermesh.nl>
 Version: 1.0 Release date: 08 July 2003

 This program is free software; you can redistribute it and/or modify it
 under the terms of the GNU General Public License as published by the
 Free Software Foundation; either version 2 of the License, or (at your
 option) any later version.
 */
 
//Server and client send the session ID in the URL
if(isset($_REQUEST['sid']))
{
	session_id($_REQUEST['sid']);
}

require_once("../../Group-Office.php");
//load file management class
$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('filesystem');

require_once($GO_CONFIG->class_path.'filesystem.class.inc');
$fs = new filesystem();

$path = smart_stripslashes($_REQUEST['path']);

$mode = isset($_REQUEST['mode'])  ? $_REQUEST['mode'] : 'download';

if(!$fs->is_sub_dir(dirname($path),$GO_CONFIG->file_storage_path) && !$fs->is_sub_dir(dirname($path),$GO_CONFIG->local_path))
{
	exit('Forbidden');
}


if ($fs->has_read_permission($GO_SECURITY->user_id, $path) || $fs->has_write_permission($GO_SECURITY->user_id, $path))
{

	$browser = detect_browser();


	$filename = basename($path);
	$extension = get_extension($filename);



	header('Content-Length: '.filesize($path));
	header('Expires: '.gmdate('D, d M Y H:i:s') . ' GMT');
	header('Content-Transfer-Encoding: binary');

	if ($browser['name'] == 'MSIE')
	{
		header('Content-Type: application/download');
		if($mode == 'download')
		{
			header('Content-Disposition: attachment; filename="'.$filename.'"');
		}else
		{
			header('Content-Disposition: inline; filename="'.$filename.'"');
		}
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
	}else
	{
		if($mode == 'download')
		{
			header('Content-Type: application/download');
			header('Content-Disposition: attachment; filename="'.$filename.'"');
		}else
		{
			header('Content-Type: '.mime_content_type($path));
			header('Content-Disposition: inline; filename="'.$filename.'"');
		}
		header('Pragma: no-cache');
	}


	$fd = fopen($path,'rb');
	while (!feof($fd)) {
		print fread($fd, 32768);
	}
	fclose($fd);

}else
{
	header('Location: '.$GO_CONFIG->host.'error_docs/401.php');
	exit();
}
