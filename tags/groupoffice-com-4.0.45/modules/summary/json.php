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
		case 'note':
			$response['data']['text'] = $summary->get_note($GLOBALS['GO_SECURITY']->user_id);
			$response['success']=true;
			break;
		case 'rss_tabs':
			$response['data'] = $summary->get_feeds($GLOBALS['GO_SECURITY']->user_id);
			$response['success']=true;
			break;
		case 'feed':
			$response['data']['url'] = $summary->get_feed($GLOBALS['GO_SECURITY']->user_id);
			$response['success']=true;
			break;
		case 'announcement':
			$announcement = $summary->get_announcement(($_REQUEST['announcement_id']));
			require_once($GLOBALS['GO_CONFIG']->class_path.'base/users.class.inc.php');
			$GO_USERS = new GO_USERS();

			$announcement['user_name']=$GO_USERS->get_user_realname($announcement['user_id']);
			$announcement['due_time']=Date::get_timestamp($announcement['due_time'], false);
			$announcement['mtime']=Date::get_timestamp($announcement['mtime']);
			$announcement['ctime']=Date::get_timestamp($announcement['ctime']);
			$response['data']=$announcement;
			$response['success']=true;
			break;
		case 'announcements':
			if(isset($_POST['delete_keys']))
			{
				try{
					$response['deleteSuccess']=true;
					$delete_announcements = json_decode(($_POST['delete_keys']));
					foreach($delete_announcements as $announcement_id)
					{
						$summary->delete_announcement($announcement_id);
					}
				}catch(Exception $e)
				{
					$response['deleteSuccess']=false;
					$response['deleteFeedback']=$e->getMessage();
				}
			}
			$sort = isset($_REQUEST['sort']) ? ($_REQUEST['sort']) : 'id';
			$dir = isset($_REQUEST['dir']) ? ($_REQUEST['dir']) : 'DESC';
			$start = isset($_REQUEST['start']) ? ($_REQUEST['start']) : '0';
			$limit = isset($_REQUEST['limit']) ? ($_REQUEST['limit']) : '0';
			$query = !empty($_REQUEST['query']) ? '%'.($_REQUEST['query']).'%' : '';
			if(isset($_POST['active']) && $_POST['active']=='true')
			{
				$response['total'] = $summary->get_active_announcements($sort, $dir, $start, $limit);
			}else
			{
				$response['total'] = $summary->get_announcements( $query, $sort, $dir, $start, $limit);
			}
			require_once($GLOBALS['GO_CONFIG']->class_path.'base/users.class.inc.php');
			$GO_USERS = new GO_USERS();
			$response['results']=array();
			while($summary->next_record())
			{
				$announcement = $summary->record;
				$announcement['user_name']=$GO_USERS->get_user_realname($announcement['user_id']);
				$announcement['due_time']=!empty($announcement['due_time']) ? Date::get_timestamp($announcement['due_time'],false) : '-';
				$announcement['mtime']=Date::get_timestamp($announcement['mtime']);
				$announcement['ctime']=Date::get_timestamp($announcement['ctime']);
				$response['results'][] = $announcement;
			}
			break;
		case 'webfeeds':
			if(isset($_POST['delete_keys']))
			{
				try{
					$response['deleteSuccess']=true;
					$delete_webfeeds = json_decode(($_POST['delete_keys']));
					foreach($delete_webfeeds as $webfeed_id)
					{
						$summary->delete_webfeed($webfeed_id);
					}
				}catch(Exception $e)
				{
					$response['deleteSuccess']=false;
					$response['deleteFeedback']=$e->getMessage();
				}
			}
			$sort = isset($_REQUEST['sort']) ? ($_REQUEST['sort']) : 'id';
			$dir = isset($_REQUEST['dir']) ? ($_REQUEST['dir']) : 'ASC';
			$start = isset($_REQUEST['start']) ? ($_REQUEST['start']) : '0';
			$limit = isset($_REQUEST['limit']) ? ($_REQUEST['limit']) : '0';
			$query = !empty($_REQUEST['query']) ? '%'.($_REQUEST['query']).'%' : '';

			if(isset($_POST['active']) && $_POST['active']=='true')
			{
				$response['total'] = $summary->get_active_webfeeds($sort, $dir, $start, $limit, $GLOBALS['GO_SECURITY']->user_id);
			}else
			{
				$response['total'] = $summary->get_webfeeds($query, $sort, $dir, $start, $limit, $GLOBALS['GO_SECURITY']->user_id);
			}
			$response['results']=array();
			while($summary->next_record())
			{
				$webfeed = $summary->record;
				
				if(empty($webfeed['summary']))
					unset($webfeed['summary']);
				
				$webfeed['feedId'] = $webfeed['id'];
				$response['results'][] = $webfeed;
			}
			break;
		case 'active_announcements':
			if(isset($_POST['delete_keys']))
			{
				try{
					$response['deleteSuccess']=true;
					$delete_announcements = json_decode(($_POST['delete_keys']));
					foreach($delete_announcements as $announcement_id)
					{
						$summary->delete_announcement($announcement_id);
					}
				}catch(Exception $e)
				{
					$response['deleteSuccess']=false;
					$response['deleteFeedback']=$e->getMessage();
				}
			}
			$sort = isset($_REQUEST['sort']) ? ($_REQUEST['sort']) : 'id';
			$dir = isset($_REQUEST['dir']) ? ($_REQUEST['dir']) : 'DESC';
			$start = isset($_REQUEST['start']) ? ($_REQUEST['start']) : '0';
			$limit = isset($_REQUEST['limit']) ? ($_REQUEST['limit']) : '0';
			$query = !empty($_REQUEST['query']) ? '%'.($_REQUEST['query']).'%' : '';
			$response['total'] = $summary->get_announcements( $query, $sort, $dir, $start, $limit);
			$response['results']=array();

			require_once($GLOBALS['GO_CONFIG']->class_path.'base/users.class.inc.php');
			$GO_USERS = new GO_USERS();



			while($summary->next_record())
			{
				$announcement = $summary->record;
				$announcement['user_name']=$GO_USERS->get_user_realname($announcement['user_id']);
				$announcement['due_time']=!empty($announcement['due_time']) ? Date::get_timestamp($announcement['due_time'],false) : '-';
				$announcement['mtime']=Date::get_timestamp($announcement['mtime']);
				$announcement['ctime']=Date::get_timestamp($announcement['ctime']);
				$response['results'][] = $announcement;
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
