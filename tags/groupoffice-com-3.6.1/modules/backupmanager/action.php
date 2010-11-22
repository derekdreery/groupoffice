<?php

require_once("../../Group-Office.php");
$GO_SECURITY->json_authenticate('backupmanager');

require_once ($GO_MODULES->modules['backupmanager']['class_path'].'backupmanager.class.inc.php');

$backupmanager = new backupmanager();
$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';

require($GO_LANGUAGE->get_language_file('backupmanager'));

try
{
    switch($task)
    {
        case 'save_settings':

            // TO-DO: check input before updating. Strip trailing slashes from folders.
            $settings['id'] = 1;
            $settings['rmachine'] = (isset($_REQUEST['rmachine']) && $_REQUEST['rmachine']) ? $_REQUEST['rmachine'] : '';
            $settings['rport'] = (isset($_REQUEST['rport']) && intval($_REQUEST['rport'])) ? $_REQUEST['rport'] : '';
            $settings['ruser'] = (isset($_REQUEST['ruser']) && $_REQUEST['ruser']) ? $_REQUEST['ruser'] : '';
            $settings['rtarget'] = (isset($_REQUEST['rtarget']) && $_REQUEST['rtarget']) ? $_REQUEST['rtarget'] : '';
            $settings['sources'] = (isset($_REQUEST['sources']) && $_REQUEST['sources']) ? $_REQUEST['sources'] : '';
            $settings['rotations'] = (isset($_REQUEST['rotations']) && intval($_REQUEST['rotations'])) ? $_REQUEST['rotations'] : '';
            $settings['emailaddress'] = (isset($_REQUEST['emailaddress']) && $_REQUEST['emailaddress']) ? $_REQUEST['emailaddress'] : '';
            $settings['emailsubject'] = (isset($_REQUEST['emailsubject']) && $_REQUEST['emailsubject']) ? $_REQUEST['emailsubject'] : '';

            if(!$settings['rmachine'] || !$settings['rport'] || !$settings['ruser'] || !$settings['rtarget'] || !$settings['sources'] || !$settings['rotations'] || !$settings['emailaddress'] || !$settings['emailsubject'])
            {
                throw new Exception($lang['common']['missingField']);
            }

            if(!$backupmanager->save_settings($settings))
            {
                $response['feedback'] = $lang['backupmanager']['save_error'];
            }else
            if(!$backupmanager->get_mysql_config(true))
            {
                $response['feedback'] = $lang['backupmanager']['no_mysql_config'];
            }

            $response['success'] = isset($response['feedback']) ? false : true;

            break;


        case 'scp_key':

            $rpassword = (isset($_REQUEST['rpassword']) && $_REQUEST['rpassword']) ? $_REQUEST['rpassword'] : '';
            $rmachine = (isset($_REQUEST['rmachine']) && $_REQUEST['rmachine']) ? $_REQUEST['rmachine'] : '';
            $rport = (isset($_REQUEST['rport']) && $_REQUEST['rport']) ? $_REQUEST['rport'] : '';
            $ruser = (isset($_REQUEST['ruser']) && $_REQUEST['ruser']) ? $_REQUEST['ruser'] : '';

            if(!$rpassword || !$rmachine || !$rport || !$ruser)
            {
                throw new Exception($lang['common']['missingField']);
            }          
            
            require_once($GO_MODULES->modules['backupmanager']['class_path'].'phpseclib/Net/SSH2.php');            
            $ssh = new Net_SSH2($rmachine, $rport);
            
            if($ssh->login($ruser, $rpassword))
            {
                if(!file_exists($GO_CONFIG->file_storage_path.'.ssh/id_rsa.pub'))
                {
                    if(!file_exists($GO_CONFIG->file_storage_path.'.ssh'))
                    {
                        mkdir($GO_CONFIG->file_storage_path.'.ssh', 0700);
                    }
                    
                    exec('ssh-keygen -q -f '.$GO_CONFIG->file_storage_path.'.ssh/id_rsa -N "" -P ""', $o, $r);                 
                }
                
                $key_content = file_get_contents($GO_CONFIG->file_storage_path.'.ssh/id_rsa.pub');
                if($key_content)
                {
                    echo $ssh->exec("umask 077; test -d .ssh || mkdir .ssh && touch .ssh/authorized_keys && chmod 600 .ssh/authorized_keys;");
                    echo $ssh->exec('echo "'.$key_content.'" >> .ssh/authorized_keys');
                }else
                {
                    $response['feedback'] = $lang['backupmanager']['empty_key'];
                }               
            }else
            {
                $response['feedback'] = $lang['backupmanager']['connection_error'];
            }

            $response['success'] = isset($response['feedback']) ? false : true;

            break;

    }
}catch(Exception $e)
{
    $response['feedback']=$e->getMessage();
    $response['success']=false;
}

echo json_encode($response);