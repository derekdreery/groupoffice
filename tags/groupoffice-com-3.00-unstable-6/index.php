<?php
/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @version $Id: index.php 2952 2008-09-03 09:47:49Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 */




require_once("Group-Office.php");

//$config_file = $GO_CONFIG->get_config_file();
if(empty($GO_CONFIG->db_user))
{
	header('Location: install/');
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

if(isset($_REQUEST['task']) && $_REQUEST['task']=='logout')
{
	$GO_SECURITY->logout();	
}

require_once($GO_THEME->theme_path."layout.inc.php");
