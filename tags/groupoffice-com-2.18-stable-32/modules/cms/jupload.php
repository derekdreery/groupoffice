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

//set umask to 000 and remember the old umaks to reset it below
//umask must be 000 to create 777 files and folders

session_id($_REQUEST['sid']);
//basic group-office authentication
require_once("../../Group-Office.php");
$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('cms');

require_once ($GO_LANGUAGE->get_language_file('filesystem'));


$tmp_dir = $GO_CONFIG->tmpdir.'/'.$GO_SECURITY->user_id.'/cms/batch_upload/';
if(!is_dir($tmp_dir))
{
	mkdir_recursive($tmp_dir);
}

while($file = array_shift($_FILES))
{
	if (is_uploaded_file($file['tmp_name']))
	{
		move_uploaded_file($file['tmp_name'], $tmp_dir.utf8_basename($file['name']));
	}
}
