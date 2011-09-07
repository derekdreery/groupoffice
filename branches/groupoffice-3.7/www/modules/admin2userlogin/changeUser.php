<?php
	require('../../Group-Office.php'); // Add the groupoffice php file
	
	require($GO_CONFIG->class_path.'base/users.class.inc.php');
	$GO_USERS = new GO_USERS();

	if($GO_SECURITY->has_admin_permission($GO_SECURITY->user_id))
	{
		$_SESSION=array();
		$user = $GO_USERS->get_user($_GET['id']);
		$GO_SECURITY->logged_in($user);
		$GO_MODULES->load_modules();
		go_infolog("ADMIN logged-in as user: \"".$user['username']."\" from IP: ".$_SERVER['REMOTE_ADDR']);
		header( 'Location: '.$GO_CONFIG->host ) ;
	}
	else
	{
		echo "Unable to login as a normal user, you probably don't have admin permissions!";
	}
?>