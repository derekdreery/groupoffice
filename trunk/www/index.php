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




require_once("Group-Office.php");

$config_file = $GO_CONFIG->get_config_file();
if(empty($GO_CONFIG->db_user))
{
	header('Location: install/install.php');
	exit();
}
/*Uncomment with release!
if(is_writable($config_file))
{
	echo '<font color="red"><b>\''.$config_file.'\' is writable please chmod 755
    '.$config_file.' and change the ownership to any other user then the
    webserver user.</b></font>';
    
	exit();
}*/

require_once($GO_THEME->theme_path."layout.inc");