<?php

/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: json.php 6110 2011-08-12 15:27:17Z wsmits $
 * @copyright Copyright Intermesh
 * @author Wesley Smits <wsmits@intermesh.nl>
 */
require('../../Group-Office.php');
$GO_SECURITY->json_authenticate('copytag');
require_once ($GO_MODULES->modules['copytag']['class_path'].'copytag.class.inc.php');
$copyTag = new copytag();

require_once($GO_CONFIG->class_path.'base/users.class.inc.php');
$GO_USERS = new GO_USERS();

$task=isset($_REQUEST['task']) ? $_REQUEST['task'] : '';
try{
	switch($task)
	{
		case 'grid':
//      if(isset($_POST['delete_keys'])) {
//				try {
//					$response['deleteSuccess']=true;
//					$tags = json_decode($_POST['delete_keys']);
//					foreach($tags as $tag)
//          {
//						//$copyTag->removeTag($user,$tag);
//          }
//				}
//        catch(Exception $e) {
//					$response['deleteSuccess']=false;
//					$response['deleteFeedback']=$e->getMessage();
//				}
//			}
      
			$sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'last_name';
			$dir = isset($_REQUEST['dir']) ? $_REQUEST['dir'] : 'ASC';
			$start = isset($_REQUEST['start']) ? $_REQUEST['start'] : '0';
			$limit = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : '0';
			$query = !empty($_REQUEST['query']) ? '%'.$_REQUEST['query'].'%' : '';
			$response['total'] = $copyTag->getGridData( $query, $sort, $dir, $start, $limit);
			$response['results']=array();
			while($tagData = $copyTag->next_record())
			{
        $tagData['userid'] = $tagData['id'];
        $tagData['user'] = $GO_USERS->get_user_realname($tagData['id']);
				$response['results'][] = $tagData;
			}
            
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
