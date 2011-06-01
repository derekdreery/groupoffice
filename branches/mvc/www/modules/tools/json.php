<?php
require('../../Group-Office.php');

GO::security()->json_authenticate('tools');

require(GO::language()->get_language_file('tools'));
$task=isset($_REQUEST['task']) ? ($_REQUEST['task']) : '';

try{

	switch($task)
	{
		case 'scripts':
			
				$response['results']=array();				
				$response['results'][]=array('name'=>$lang['tools']['dbcheck'], 'script'=>GO::modules()->modules['tools']['url'].'dbcheck.php');
				//$response['results'][]=array('name'=>$lang['tools']['checkmodules'], 'script'=>GO::modules()->modules['tools']['url'].'checkmodules.php');
				$response['results'][]=array('name'=>$lang['tools']['buildsearchcache'], 'script'=>GO::modules()->modules['tools']['url'].'buildsearchcache.php');
				$response['results'][]=array('name'=>$lang['tools']['rm_duplicates'], 'script'=>GO::modules()->modules['tools']['url'].'rm_duplicates.php');
				$response['results'][]=array('name'=>$lang['tools']['resetState'], 'script'=>GO::modules()->modules['tools']['url'].'reset_state.php');
				
				if(isset(GO::modules()->modules['files']))
				{
					//$response['results'][]=array('name'=>'Remove duplicate folders and files', 'script'=>GO::modules()->modules['files']['url'].'scripts/removeduplicatefolders.php');
					//$response['results'][]=array('name'=>'Sync filesystem with files database', 'script'=>GO::modules()->modules['files']['url'].'scripts/sync_filesystem.php');
				}

				if(!empty(GO::config()->phpMyAdminUrl))
					$response['results'][]=array('name'=>'PhpMyAdmin', 'script'=>GO::modules()->modules['tools']['url'].'phpmyadmin.php');

			break;
	}
}catch(Exception $e)
{
	$response['feedback']=$e->getMessage();
	$response['success']=false;
}
echo json_encode($response);