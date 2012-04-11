<?php

require_once('../../Group-Office.php');
$GLOBALS['GO_SECURITY']->json_authenticate('emailportlet');

require_once ($GLOBALS['GO_MODULES']->modules['emailportlet']['class_path'].'emailportlet.class.inc.php');
require_once ($GLOBALS['GO_MODULES']->modules['email']['class_path']."email.class.inc.php");

$email = new email();
$emailportlet = new emailportlet();
$task=isset($_REQUEST['task']) ? $_REQUEST['task'] : '';

try {
	switch($task) {

		case 'get_folders':

			$response['total'] = $emailportlet->get_folders_on_summary($GLOBALS['GO_SECURITY']->user_id);
			$response['data'] = array();
			while($emailportlet->next_record())
			{
				$folder = $email->get_folder_by_id($emailportlet->f('folder_id'));
				unset($folder['sort']);

				if(!$folder){
					$emailportlet->delete_on_summary($GLOBALS['GO_SECURITY']->user_id, $emailportlet->f('folder_id'));					
					$response['total']--;
					continue;
				}

				$pos = strrpos($folder['name'], '.');
				$pos = ($pos) ? ($pos)+1 : $pos;
				$folder['title'] = substr($folder['name'], $pos);
				$folder['fid'] = 'account_'.$folder['account_id'].':'.$folder['name'];

				$response['data'][] = $folder;
			}

			$response['success'] = true;
			break;
			
		case 'show_folder':

			$folder_id = (isset($_REQUEST['folder_id']) && $_REQUEST['folder_id']) ? $_REQUEST['folder_id'] : 0;

			if(!$emailportlet->exists_on_summary($GLOBALS['GO_SECURITY']->user_id, $folder_id))
			{
				$emailportlet->insert_on_summary($GLOBALS['GO_SECURITY']->user_id, $folder_id);

				$folder = $email->get_folder_by_id($folder_id);

				$pos = strrpos($folder['name'], '.');
				$pos = ($pos) ? ($pos)+1 : $pos;
				$folder['title'] = substr($folder['name'], $pos);

				$response['data'][] = $folder;
			}else
			{
				$response['data'] = array();
			}			

			$response['success'] = true;			
			break;		

		case 'hide_folder':

			$folder_id = (isset($_REQUEST['folder_id']) && $_REQUEST['folder_id']) ? $_REQUEST['folder_id'] : 0;
			if($emailportlet->exists_on_summary($GLOBALS['GO_SECURITY']->user_id, $folder_id))
			{
				$emailportlet->delete_on_summary($GLOBALS['GO_SECURITY']->user_id, $folder_id);				
			}

			$response['success'] = true;
			break;

	}
}catch(Exception $e) {
	$response['feedback']=$e->getMessage();
	$response['success']=false;
}

echo json_encode($response);
