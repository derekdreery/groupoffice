<?php
require('../../Group-Office.php');

$GLOBALS['GO_SECURITY']->json_authenticate('tools');

require($GLOBALS['GO_LANGUAGE']->get_language_file('tools'));
$task=isset($_REQUEST['task']) ? ($_REQUEST['task']) : '';

try{

	switch($task)
	{
		case 'scripts':
			
			require_once('../../GO.php');
			
				$response['results']=array();				
				$response['results'][]=array('name'=>$lang['tools']['dbcheck'], 'script'=>GO::url('maintenance/checkDatabase'));
				//$response['results'][]=array('name'=>$lang['tools']['checkmodules'], 'script'=>$GLOBALS['GO_MODULES']->modules['tools']['url'].'checkmodules.php');
				$response['results'][]=array('name'=>$lang['tools']['buildsearchcache'], 'script'=>GO::url('maintenance/buildSearchCache'));
				$response['results'][]=array('name'=>$lang['tools']['rm_duplicates'], 'script'=>GO::url('maintenance/removeDuplicates'));
				//$response['results'][]=array('name'=>$lang['tools']['rm_duplicates'], 'script'=>$GLOBALS['GO_MODULES']->modules['tools']['url'].'rm_duplicates.php');
					
				if(isset($GLOBALS['GO_MODULES']->modules['files']))
				{
					//$response['results'][]=array('name'=>'Remove duplicate folders and files', 'script'=>$GLOBALS['GO_MODULES']->modules['files']['url'].'scripts/removeduplicatefolders.php');
					$response['results'][]=array('name'=>'Sync filesystem with files database', 'script'=>GO::url('files/folder/syncFilesystem'));
				}
				
				if(isset($GLOBALS['GO_MODULES']->modules['filesearch']))
				{
					//$response['results'][]=array('name'=>'Remove duplicate folders and files', 'script'=>$GLOBALS['GO_MODULES']->modules['files']['url'].'scripts/removeduplicatefolders.php');
					$response['results'][]=array('name'=>'Update filesearch index', 'script'=>GO::url('filesearch/filesearch/sync'));
				}

//				if(!empty($GLOBALS['GO_CONFIG']->phpMyAdminUrl))
//					$response['results'][]=array('name'=>'PhpMyAdmin', 'script'=>$GLOBALS['GO_MODULES']->modules['tools']['url'].'phpmyadmin.php');

			break;
	}
}catch(Exception $e)
{
	$response['feedback']=$e->getMessage();
	$response['success']=false;
}
echo json_encode($response);