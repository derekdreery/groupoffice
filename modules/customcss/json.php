<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */
require_once("../../Group-Office.php");
$GLOBALS['GO_SECURITY']->json_authenticate('customcss');

try{

	if(!is_dir($GLOBALS['GO_CONFIG']->file_storage_path.'customcss')){
		mkdir($GLOBALS['GO_CONFIG']->file_storage_path.'customcss', 0755, true);
	}
	if(file_exists($GLOBALS['GO_CONFIG']->file_storage_path.'customcss/style.css')){
		$response['data']['css']=file_get_contents($GLOBALS['GO_CONFIG']->file_storage_path.'customcss/style.css');
	}else
	{
		$response['data']['css']='/*
* Put custom styles here that will be applied to Group-Office. You can use the select file button to upload your logo and insert the URL in to this stylesheet.
*/

/* this will override the logo at the top right */
#headerLeft{
background-image:url(/insert/url/here) !important;
}

/* this will override the logo at the login screen */
.go-app-logo {
background-image:url(/insert/url/here) !important;
}';
	}

	if(file_exists($GLOBALS['GO_CONFIG']->file_storage_path.'customcss/javascript.js')){
		$response['data']['javascript']=file_get_contents($GLOBALS['GO_CONFIG']->file_storage_path.'customcss/javascript.js');
	}

	
	$response['success']=true;
}catch(Exception $e)
{
	$response['feedback']=$e->getMessage();
	$response['success']=false;
}
echo json_encode($response);