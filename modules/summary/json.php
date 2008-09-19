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
		case 'note':
			$response['data']['text'] = $sum->get_note($GO_SECURITY->user_id);
			$response['success']=true;
			break;
		case 'feed':
			$response['data']['url'] = $sum->get_feed($GO_SECURITY->user_id);
			$response['success']=true;
			break;
	}
}catch(Exception $e)
{
	$response['feedback']=$e->getMessage();
	$response['success']=false;
}

echo json_encode($response);
