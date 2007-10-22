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

require_once ($GO_LANGUAGE->get_language_file('filesystem'));

require_once ($GO_CONFIG->class_path.'filesystem.class.inc');
$fs = new filesystem();
if($share= $fs->find_share($_SESSION['GO_FILESYSTEM_PATH']))
{
	$users = $GO_SECURITY->get_authorized_users_in_acl($share['acl_read']);
	
	$write_users = $GO_SECURITY->get_authorized_users_in_acl($share['acl_write']);
	while($user_id = array_shift($write_users))
	{
		$fs_settings = $fs->get_settings($user_id);
		if($fs_settings['notify']=='1' && !in_array($user_id, $users))
		{
			$users[]=$user_id;			
		}
	}
}

while($file = array_shift($_FILES))
{
	if (is_uploaded_file($file['tmp_name']))
	{
		if ( $GO_CONFIG->user_quota != 0 && $usedspace = $this->get_usedspace($_SESSION['GO_FILESYSTEM_PATH'])) {
				if ( ( $usedspace + filesize( $file['tmp_name'] ) ) >= $GO_CONFIG->user_quota*1024 ) {		
					break;
				}
		}
		
		$destination_path=$_SESSION['GO_FILESYSTEM_PATH'].'/'.basename($file['name']);
		
		if($GO_LOGGER->enabled)
		{
			if(file_exists($destination_path))
			{
				$link_id=$fs->get_link_id($path);
				$GO_LOGGER->log('filesystem', 'OVERWRITE '.$path, $link_id);
			}else
			{
				$link_id=$fs->get_link_id($path);
				$GO_LOGGER->log('filesystem', 'NEW FILE '.$path, $link_id);
			}
			
		}
		move_uploaded_file($file['tmp_name'], $destination_path);
				
	
		if($share)
		{
			foreach($users as $user_id)
			{
				$user = $GO_USERS->get_user($user_id);
				
				$subject = sprintf($fs_new_file_uploaded, basename($file['name']));
				
				$link = new hyperlink($GO_CONFIG->full_url.'index.php?return_to='.
					urlencode($GO_MODULES->url.'index.php?path='.
					urlencode($_SESSION['GO_FILESYSTEM_PATH'])),$fs_open_containing_folder);
					
				$link->set_attribute('target','_blank');
				$link->set_attribute('class','blue');
			
				$body = sprintf($fs_file_put_in, basename($file['name']), $_SESSION['GO_FILESYSTEM_PATH']).'<br>'.$link->get_html();
				
				sendmail($user['email'], $_SESSION['GO_SESSION']['email'], $_SESSION['GO_SESSION']['name'], $subject, $body, '3', 'text/HTML');
			}
		}
	}
}

echo "SUCCESS\n";
