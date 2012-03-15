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
$GLOBALS['GO_SECURITY']->json_authenticate('summary');

require_once ($GLOBALS['GO_MODULES']->modules['summary']['class_path']."summary.class.inc.php");
//require_once ($GLOBALS['GO_LANGUAGE']->get_language_file('calendar'));
$summary = new summary();

try{

	switch($_REQUEST['task'])
	{
		case 'save_note':
			$note['user_id']=$GLOBALS['GO_SECURITY']->user_id;
			$note['text']=$_POST['text'];
			$summary->update_note($note);
			
			$response['success']=true;
			
			break;
			
		case 'save_feeds':
			$feeds = json_decode($_POST['feeds'], true);
			$ids = array();
			$response['data'] = array();
			foreach($feeds as $feed)
			{
				$feed['user_id'] = $GLOBALS['GO_SECURITY']->user_id;
				// Hack for the table being updated correctly.
				if($feed['summary'] === true)
					$feed['summary'] = 1;
				
				$feed['id'] = $feed['feedId'];
				unset($feed['feedId']);
				if($feed['id']>0)
				{
					$summary->update_feed($feed);
				}
				else
				{
					$feed['id']=$summary->add_feed($feed);
				}
				$ids[] = $feed['id'];
				$response['data'][$feed['id']]=$feed;
			}
			$summary->delete_other_feeds($GLOBALS['GO_SECURITY']->user_id, $ids);
			$response['ids'] = $ids;
			$response['success']=true;
			
			break;
		case 'save_announcement':		
			$announcement_id=$announcement['id']=isset($_POST['announcement_id']) ? ($_POST['announcement_id']) : 0;
			
			$announcement['due_time']=Date::to_unixtime(trim($_POST['due_time']));
			$announcement['title']=$_POST['title'];
			$announcement['content']=String::convert_html($_POST['content']);
			if($announcement['id']>0)
			{
				$summary->update_announcement($announcement);
				$response['success']=true;
			}else
			{
				$announcement['user_id']=$GLOBALS['GO_SECURITY']->user_id;
				$announcement_id= $summary->add_announcement($announcement);
				$response['announcement_id']=$announcement_id;
				$response['success']=true;
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
