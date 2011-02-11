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
require('../../Group-Office.php');
$GO_SECURITY->json_authenticate('gnupg');
require_once ($GO_MODULES->modules['gnupg']['class_path'].'gnupg.class.inc.php');
$gnupg = new gnupg();
$task=isset($_REQUEST['task']) ? $_REQUEST['task'] : '';
try{
	switch($task)
	{
		case 'send_key': 			
			require_once ($GO_MODULES->modules['email']['class_path']."email.class.inc.php");
		
			$tmp_file = $GO_CONFIG->tmpdir.'public_key.asc';
			
			$data = $gnupg->export($_REQUEST['fingerprint']);
			file_put_contents($tmp_file, $data);
			
			if($GO_MODULES->has_module('mailings') && isset($_REQUEST['template_id']))
			{
				$response = load_template($_REQUEST['template_id'], '');
			}
			$response['success']=true;
				
			$response['data']['attachments'][] = array(
					'tmp_name'=>$tmp_file,
					'name'=>'public_key.asc',
					'size'=>filesize($tmp_file),
					'type'=>'text/plain'
			);
			
			if($_POST['content_type']=='plain')
			{
				$response['data']['textbody']=$response['data']['body'];
				unset($response['data']['body']);
			}
		break;
		
		case 'keys':
				
			if(isset($_POST['delete_keys']))
			{
				try{
					$response['deleteSuccess']=true;
					$delete_keys = json_decode($_POST['delete_keys']);
					foreach($delete_keys as $fingerprint)
					{
						$gnupg->delete_key($fingerprint);
					}
				}catch(Exception $e)
				{
					$response['deleteSuccess']=false;
					$response['deleteFeedback']=$e->getMessage();
				}
			}
				
			$keys = $gnupg->list_keys();
			$response['results']=array();
			while($key = array_shift($keys))
			{
				$key['uid']=htmlspecialchars($key['uid'], ENT_QUOTES,'UTF-8');
				$response['results'][]=$key;
			}
			$response['total'] = count($response['results']);
			break;
				
		case 'private_keys':
			$keys = $gnupg->list_private_keys();
			$response['results']=array();
			while($key = array_shift($keys))
			{
				$key['uid']=htmlspecialchars($key['uid'], ENT_QUOTES,'UTF-8');
				$response['results'][]=$key;
			}
			$response['total'] = count($response['results']);

			break;

			/* {TASKSWITCH} */
	}
}catch(Exception $e)
{
	$response['feedback']=$e->getMessage();
	$response['success']=false;
}
echo json_encode($response);
