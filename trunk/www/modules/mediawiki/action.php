<?php
/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: action.php 5868 2010-10-16 14:41:18Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */


require_once("../../Group-Office.php");
$GLOBALS['GO_SECURITY']->json_authenticate('mediawiki');

require_once ($GLOBALS['GO_MODULES']->modules['mediawiki']['class_path']."mediawiki.class.inc.php");
//require_once ($GLOBALS['GO_LANGUAGE']->get_language_file('notes'));

$mw = new mediawiki();

try{
	switch($_REQUEST['task'])
	{
		case 'save_settings':
			$GLOBALS['GO_CONFIG']->save_setting('mediawiki_external_url', $_POST['external_url']);
			$response['data']['external_url'] = $_POST['external_url'];
			$GLOBALS['GO_CONFIG']->save_setting('mediawiki_title', $_POST['title']);
			$response['data']['title'] = $_POST['title'];

			$response['success']=true;
			break;
/* {TASKSWITCH} */
	}
}catch(Exception $e)
{
	$response['feedback']=$e->getMessage();
	$response['success']=false;
}

echo json_encode($response);