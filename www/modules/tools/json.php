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
				$response['results'][]=array('name'=>$lang['tools']['rm_duplicates'], 'script'=>$GO_MODULES->modules['tools']['url'].'rm_duplicates.php');
				//$response['results'][]=array('name'=>$lang['tools']['backupdb'], 'script'=>$GO_MODULES->modules['tools']['url'].'backupdb.php');
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