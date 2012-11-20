<?php

require_once('../../Group-Office.php');
$GLOBALS['GO_SECURITY']->json_authenticate('backupmanager');

require_once ($GLOBALS['GO_MODULES']->modules['backupmanager']['class_path'].'backupmanager.class.inc.php');

$backupmanager = new backupmanager();
$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';

try
{
    switch($task)
    {        
        case 'get_settings':

            $settings = $backupmanager->get_settings();

            if(!$settings)
            {
                $settings = array();
            }else            
            if($settings['rmachine'] && $settings['rport'] && $settings['ruser'])
            {
                $response['enable_publish'] = true;
            }

            $response['data'] = $settings;
            $response['success'] = true;
            break;
    }
}catch(Exception $e)
{
    $response['feedback']=$e->getMessage();
    $response['success']=false;
}

echo json_encode($response);
