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

$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';
$load_frames = isset($_REQUEST['load_frames']) ? $_REQUEST['load_frames'] : 'true';

require_once($GO_LANGUAGE->get_base_language_file('login'));

if ($task == "logout")
{
	$GO_SECURITY->logout();
	
	SetCookie("GO_UN","",time()-3600,"/","",0);
    SetCookie("GO_PW","",time()-3600,"/","",0);
    unset($_SESSION);
    unset($_COOKIE); 
	
	if(!empty($GO_CONFIG->logout_url))
	{
		header('Location: '.$GO_CONFIG->logout_url);
		exit();
	}
}

//when the user is logged in redirect him.
if ($GO_SECURITY->logged_in())
{
	
	
	
	$start_module = $GO_MODULES->get_module(
	$_SESSION['GO_SESSION']['start_module']);

	if (isset($_REQUEST['return_to']))
	{
		$start_url = $_REQUEST['return_to'];
		
		if($load_frames=='false')
		{
			header('Location: '.$start_url);
			exit();
		}
		
	}elseif ($start_module && ($GO_SECURITY->has_permission(
	$GO_SECURITY->user_id, $start_module['acl_read']) ||
	$GO_SECURITY->has_permission($GO_SECURITY->user_id,
	$start_module['acl_write'])))
	{
		$start_url = $start_module['url'];
	}else
	{
		$start_url = $GO_CONFIG->host.'configuration/';
	}
	require_once($GO_THEME->theme_path."frames.inc");
	exit();
}

//if form was posted user wants to login
//set cookies to remember login before headers are sent
if ( $_SERVER['REQUEST_METHOD'] == "POST" || (isset($_COOKIE['GO_UN'])
&& isset($_COOKIE['GO_PW'])) )
{
	if ($_SERVER['REQUEST_METHOD'] != "POST")
	{
		$remind = true;
		$password = smart_addslashes($_COOKIE['GO_PW']);
		$username = smart_addslashes($_COOKIE['GO_UN']);
	} else {
		$remind = isset($_POST['remind']) ? true : false;
		$username = smart_addslashes($_POST['username']);
		$password = smart_addslashes($_POST['password']);
	}

	//check if both fields were filled
	if (!$username)// || !$password)
	{
		$feedback = "<p class=\"Error\">".$login_missing_field."</p>";
	} else {

		//attempt login using security class inherited from index.php
		//$params = isset( $auth_sources[$auth_source]) ?  $auth_sources[$auth_source] : false;
		if ($GO_AUTH->login($username, $password, $_SESSION['auth_source']))
		{
			//login is correct final check if login registration was ok
			if ($GO_SECURITY->logged_in())
			{
				if ($remind)
				{
					SetCookie("GO_UN",$username,time()+3600*24*30,"/",'',0);
					SetCookie("GO_PW",$password,time()+3600*24*30,"/",'',0);
				}

				//update language
				if(isset($_POST['SET_LANGUAGE']) &&
				$_POST['SET_LANGUAGE'] != $_SESSION['GO_SESSION']['language']['id'])
				{
					$user['id'] = $GO_SECURITY->user_id;
					$user['language'] = smart_addslashes($_POST['SET_LANGUAGE']);
					$GO_USERS->update_profile($user);
				}

				if ($_SESSION['GO_SESSION']['first_name'] == '' ||
				$_SESSION['GO_SESSION']['last_name'] == '' ||
				$_SESSION['GO_SESSION']['email'] == '')
				{
					header("Location: ".$GO_CONFIG->host.
					"configuration/index.php");
					exit();
				}else
				{
					$start_module = $GO_MODULES->get_module($_SESSION['GO_SESSION']['start_module']);
					if(!$start_module ||
					(!$GO_SECURITY->has_permission($GO_SECURITY->user_id,$start_module['acl_read']) &&
					!$GO_SECURITY->has_permission($GO_SECURITY->user_id,$start_module['acl_write'])))
					{
						if($modules = $GO_MODULES->get_modules_with_locations())
						{
							while($module = array_shift($modules))
							{
								if($GO_SECURITY->has_permission($GO_SECURITY->user_id, $module['acl_read']) ||
								$GO_SECURITY->has_permission($GO_SECURITY->user_id, $module['acl_write']))
								{
									$start_module = $module;
									$GO_USERS->set_start_module($GO_SECURITY->user_id, $module['id']);
									break;
								}
							}
						}
					}

					if (isset($_REQUEST['return_to']))
					{
						$start_url = $_REQUEST['return_to'];
						if($load_frames=='false')
						{
							header('Location: '.$start_url);
							exit();
						}
						
					} elseif ($start_module)
					{
						$start_url = $start_module['url'];
					} else
					{
						$start_url = $GO_CONFIG->host.'configuration/index.php?account=look.inc';
					}
					//redefine theme
					$GO_THEME = new GO_THEME();
					require_once($GO_THEME->theme_path."frames.inc");
					exit();
				}
			}else
			{
				$feedback = "<p class=\"Error\">".$login_registration_fail."</p>";
			}
		}else
		{
			$feedback = "<p class=\"Error\">".$login_bad_login."</p>";
		}
	}
}
load_basic_controls();
if(file_exists($GO_CONFIG->root_path.'login_screens/'.$GO_CONFIG->login_screen))
{
	require_once($GO_CONFIG->root_path.'login_screens/'.$GO_CONFIG->login_screen.'/login.inc');
}else {
	require_once($GO_CONFIG->root_path.'login_screens/Default/login.inc');
}
