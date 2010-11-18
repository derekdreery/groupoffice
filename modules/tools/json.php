<?php
require('../../Group-Office.php');

$GO_SECURITY->json_authenticate('tools');

require($GO_LANGUAGE->get_language_file('tools'));
$task=isset($_REQUEST['task']) ? ($_REQUEST['task']) : '';

try{

	switch($task)
	{
		case 'scripts':
			
				$response['results']=array();				
				$response['results'][]=array('name'=>$lang['tools']['dbcheck'], 'script'=>$GO_MODULES->modules['tools']['url'].'dbcheck.php');
				//$response['results'][]=array('name'=>$lang['tools']['checkmodules'], 'script'=>$GO_MODULES->modules['tools']['url'].'checkmodules.php');
				$response['results'][]=array('name'=>$lang['tools']['buildsearchcache'], 'script'=>$GO_MODULES->modules['tools']['url'].'buildsearchcache.php');
				$response['results'][]=array('name'=>$lang['tools']['rm_duplicates'], 'script'=>$GO_MODULES->modules['tools']['url'].'rm_duplicates.php');
				$response['results'][]=array('name'=>$lang['tools']['resetState'], 'script'=>$GO_MODULES->modules['tools']['url'].'reset_state.php');
				
				if(isset($GO_MODULES->modules['files']))
				{
					//$response['results'][]=array('name'=>'Remove duplicate folders and files', 'script'=>$GO_MODULES->modules['files']['url'].'scripts/removeduplicatefolders.php');
					//$response['results'][]=array('name'=>'Sync filesystem with files database', 'script'=>$GO_MODULES->modules['files']['url'].'scripts/sync_filesystem.php');
				}

				if(!empty($GO_CONFIG->phpMyAdminUrl))
					$response['results'][]=array('name'=>'PhpMyAdmin', 'script'=>$GO_MODULES->modules['tools']['url'].'phpmyadmin.php');

			break;
	}
}catch(Exception $e)
{
	$response['feedback']=$e->getMessage();
	$response['success']=false;
}
echo json_encode($response);