<?php
/**
 * @copyright Intermesh 2008
 * @author Merijn Schering <mschering@intermesh.nl>
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
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
	}
}catch(Exception $e)
{
	$response['feedback']=$e->getMessage();
	$response['success']=false;
}

echo json_encode($response);
