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
$GO_MODULES->authenticate('email');
require_once($GO_MODULES->class_path."email.class.inc");
$email = new email();

while($file = array_shift($_FILES))
{
	if (is_uploaded_file($file['tmp_name']))
	{
		$tmp_file = $GO_CONFIG->tmpdir.md5(uniqid(time()));
		move_uploaded_file($file['tmp_name'], $tmp_file);
						
		$email->register_attachment($tmp_file, 
			utf8_basename($file['name']), 
			$file['size'], 
			$file['type']);
    }	
}
