<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: action.tpl 1858 2008-04-29 14:09:19Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */
require_once("../../Group-Office.php");
$GO_SECURITY->json_authenticate('summary');

require_once ($GO_MODULES->modules['summary']['class_path']."summary.class.inc.php");
//require_once ($GO_LANGUAGE->get_language_file('calendar'));
$sum = new summary();

try{

	switch($_REQUEST['task'])
	{
		case 'save_note':
			
			$note['user_id']=$GO_SECURITY->user_id;
			$note['text']=smart_addslashes($_POST['text']);
			$sum->update_note($note);
			
			$response['success']=true;
			
			break;
			
		case 'save_rss_url':			
			$feed['user_id']=$GO_SECURITY->user_id;
			$feed['url']=smart_addslashes($_POST['url']);
			$sum->update_feed($feed);			
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
