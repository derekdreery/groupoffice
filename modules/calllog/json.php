<?php

require_once('../../Group-Office.php');
$GO_SECURITY->json_authenticate('calllog');
require_once ($GO_MODULES->modules['calllog']['class_path'].'calllog.class.inc.php');
$calllog = new calllog();
$task=isset($_REQUEST['task']) ? $_REQUEST['task'] : '';
try{
	switch($task)
	{
		case 'calls':
			
			if(isset($_POST['delete_keys']))
			{
				try
				{
					if($GO_MODULES->modules['calllog']['permission_level'] < GO_SECURITY::WRITE_PERMISSION)
					{
						throw new AccessDeniedException();
					}
					
					$response['deleteSuccess']=true;
					$delete_calls = json_decode($_POST['delete_keys']);
					foreach($delete_calls as $call)
					{
						$calllog->delete_call($call);
					}
				}catch(Exception $e)
				{
					$response['deleteSuccess']=false;
					$response['deleteFeedback']=$e->getMessage();
				}
			}
			
			$sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'mtime';
			$dir = isset($_REQUEST['dir']) ? $_REQUEST['dir'] : 'DESC';
			$start = isset($_REQUEST['start']) ? $_REQUEST['start'] : '0';
			$limit = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : '0';
			$query = !empty($_REQUEST['query']) ? '%'.$_REQUEST['query'].'%' : '';

			if($sort == 'grid_time')
			{
				$sort = 'time';
			}

			$response['results'] = array();
			$response['total'] = $calllog->get_calls($query, $sort, $dir, $start, $limit);
			while($call = $calllog->next_record())
			{
				$time = $call['time'];
				$call['date']=date($_SESSION['GO_SESSION']['date_format'], $time);
				$call['time']=date($_SESSION['GO_SESSION']['time_format'], $time);
				$call['grid_time']=$call['date'].' '.$call['time'];
				
				$response['results'][] = $call;
			}
			
			break;

		case 'save_call':

			$call = array();
			$call['id'] = (isset($_REQUEST['id']) && $_REQUEST['id']) ? $_REQUEST['id'] : 0;
			$call['name'] = (isset($_REQUEST['name']) && $_REQUEST['name']) ? $_REQUEST['name'] : '';
			$call['company'] = (isset($_REQUEST['company']) && $_REQUEST['company']) ? $_REQUEST['company'] : '';
			$call['phone'] = (isset($_REQUEST['phone']) && $_REQUEST['phone']) ? $_REQUEST['phone'] : '';
			$call['email'] = (isset($_REQUEST['email']) && $_REQUEST['email']) ? $_REQUEST['email'] : '';
			$call['description'] = (isset($_REQUEST['description']) && $_REQUEST['description']) ? $_REQUEST['description'] : '';

			$gmt_tz = new DateTimeZone('GMT');			
			$start_date = new DateTime(Date::to_input_format($_POST['date'].' '.$_POST['time']));
			$start_date->setTimezone($gmt_tz);			
			$call['time'] = $start_date->format('U');

			if($call['id'] > 0)
			{
				$calllog->update_call($call);
				$insert=false;
			}else
			{				
				$response['id'] = $call['id'] = $calllog->create_call($call);
				$insert=true;
			}

			if(isset($GO_MODULES->modules['customfields']) && $GO_MODULES->modules['customfields']['read_permission'])
			{
				require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
				$cf = new customfields();
				$cf->update_fields($GO_SECURITY->user_id, $call['id'], 18, $_POST, $insert);
			}
			
			$response['success'] = true;
			break;
	}
}catch(Exception $e)
{
	$response['feedback']=$e->getMessage();
	$response['success']=false;
}
echo json_encode($response);
