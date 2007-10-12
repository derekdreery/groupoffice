<?php
/**
 * @copyright Intermesh 2007
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.13 $ $Date: 2006/10/20 12:36:43 $3
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */


require_once("Group-Office.php");
$GO_SECURITY->authenticate();



$result =array();
switch($_REQUEST['task'])
{
	case 'login':

		require_once($GO_LANGUAGE->get_base_language_file('login'));

		$result['success']=false;

		$username = smart_addslashes($_POST['username']);
		$password = smart_addslashes($_POST['password']);

		if (!$username || !$password)
		{
			$result['success']=$login_missing_field;

		} else {
		
			//attempt login using security class inherited from index.php
			//$params = isset( $auth_sources[$auth_source]) ?  $auth_sources[$auth_source] : false;
			if (!$GO_AUTH->login($username, $password, $_SESSION['auth_source']))
			{
				$result['errors'] = $login_bad_login;
			}else
			{
				//login is correct final check if login registration was ok
				if (!$GO_SECURITY->logged_in())
				{
					$result['errors'] = $login_registration_fail;
				}else
				{
					if (isset($_POST['remind']))
					{
						SetCookie("GO_UN",$username,time()+3600*24*30,"/",'',0);
						SetCookie("GO_PW",$password,time()+3600*24*30,"/",'',0);
					}
					
		
					$result['success']=true;
				}
			}
		}


		break;

	case 'logout':
		$GO_SECURITY->logout();
			
		SetCookie("GO_UN","",time()-3600,"/","",0);
		SetCookie("GO_PW","",time()-3600,"/","",0);
		unset($_SESSION);
		unset($_COOKIE);

		break;

	case 'link':

		$fromLinks = json_decode(smart_stripslashes($_POST['fromLinks']));
		$toLinks = json_decode(smart_stripslashes($_POST['toLinks']));

		foreach($fromLinks as $fromLink)
		{
			foreach($toLinks as $toLink)
			{
				$GO_LINKS->add_link($fromLink->link_id, $fromLink->link_type, $toLink->link_id, $toLink->link_type);
			}
		}

		$result['success']=true;
		$result['errors']='Items linked successfully';

		break;

	case 'unlink':


		$unlinks = json_decode(smart_stripslashes($_POST['unlinks']));
		$link_id = smart_stripslashes($_POST['link_id']);



		foreach($unlinks as $unlink_id)
		{
			$GO_LINKS->delete_link($link_id, $unlink_id);
		}

		$result['success']=true;
		$result['errors']='Items linked successfully';

		break;
}

echo json_encode($result);