<?php
/*
  * Run a cron job once(?) a day at midnight for example. Add this to /etc/cron.d/groupoffice :
  *
  * 0 0 * * * root php /path/to/go/modules/backupmanager/cron.php /path/to/config.php
*/

if(php_sapi_name()!='cli'){
	die('This script only runs on the command line');
}

if(isset($argv[1]))
	define('CONFIG_FILE', $argv[1]);

chdir(__FILE__);
require_once('../../Group-Office.php');

if(!isset($GO_MODULES->modules['backupmanager']))
{
    echo "Backupmanager is not installed\n";
    exit();
}

require_once($GO_MODULES->modules['backupmanager']['class_path'].'backupmanager.class.inc.php');
$backupmanager = new backupmanager();

$output = array();
$settings = $backupmanager->get_settings();
$settings['rkey'] = $GO_CONFIG->file_storage_path.'.ssh/id_rsa';
if(file_exists($settings['rkey']))
{    
    if(fsockopen($settings['rmachine'], $settings['rport']))
    {
        // key exists and server is ready, prepare backup
        unset($settings['id']);
        $parameters = '';
        $multivalues = array('emailaddress','emailsubject','sources');
        foreach($settings as $key=>$val)
        {
            if(!in_array($key, $multivalues))
            {
                $parameters .= ' '.$val;
            }else
            {
                $parameters .= ' "'.$val.'"';
            }
        }

        $mysql_config = $backupmanager->get_mysql_config();
        if(count($mysql_config))
        {
            $parameters .= ' '.$mysql_config['user'].' '.$mysql_config['pass'];            
        }
        //echo $parameters."\n\n";

        // start backup
        exec($GO_MODULES->modules['backupmanager']['path'].'rsync_backup.sh '.$parameters);
        
        exit();        
    }else
    {
        echo "Target host seems to be down\n";
    }
}else
{
    echo "Keyfile not found\n";
}

?>