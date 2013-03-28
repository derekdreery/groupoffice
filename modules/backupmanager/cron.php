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

chdir(dirname(__FILE__));
require_once('../../Group-Office.php');

//echo $GLOBALS['GO_CONFIG']->get_config_file();

if(!isset($GLOBALS['GO_MODULES']->modules['backupmanager']))
{
    echo "Backupmanager is not installed\n";
    exit();
}

require_once($GLOBALS['GO_MODULES']->modules['backupmanager']['class_path'].'backupmanager.class.inc.php');
$backupmanager = new backupmanager();

$output = array();
$settings = $backupmanager->get_settings();

// Check for settings to be available
if($settings && $settings['running'] == 1)
{
	$settings['rkey'] = $GLOBALS['GO_CONFIG']->file_storage_path.'.ssh/id_rsa';
	if(file_exists($settings['rkey']))
	{
			if(fsockopen($settings['rmachine'], $settings['rport']))
			{
					// key exists and server is ready, prepare backup
					unset($settings['id'], $settings['running']);
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

					// Check for first run
					$firstRun = 0;

					if($GLOBALS['GO_CONFIG']->get_setting('backupmanager_first_run'))
					{
						$firstRun = 1;
					}

					$parameters .= ' '.$firstRun;

					//echo $parameters."\n\n";

					// start backup
					system($GLOBALS['GO_MODULES']->modules['backupmanager']['path'].'rsync_backup.sh '.$parameters, $ret);

					if(empty($ret) && $firstRun)
						$GLOBALS['GO_CONFIG']->delete_setting('backupmanager_first_run');

					exit();
			}else
			{
					echo "Target host seems to be down\n";
			}

	}else
	{
			echo "Keyfile not found\n";
	}
}
else
{
	echo "No settings for backup found or not running!\n";
}

?>