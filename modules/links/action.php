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
$GO_SECURITY->json_authenticate('links');

try{
	switch($_REQUEST['task'])
	{
		case 'save_link_description':		
			$link_description_id=$link_description['id']=isset($_POST['link_description_id']) ? $_POST['link_description_id'] : 0;
						$link_description['description']=$_POST['description'];
			if($link_description['id']>0)
			{
				$GO_LINKS->update_link_description($link_description);
				$response['success']=true;
				$insert=false;
			}else
			{
				$link_description_id= $GO_LINKS->add_link_description($link_description);
				$response['link_description_id']=$link_description_id;
				$response['success']=true;
				$insert=true;
			}
			break;
/* {TASKSWITCH} */
	}
}catch(Exception $e)
{
	$response['feedback']=$e->getMessage();
	$response['success']=false;
}
echo json_encode($response);
