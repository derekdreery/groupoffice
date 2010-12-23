<?php
if(isset($argv[1]))
define('CONFIG_FILE', $argv[1]);

$root_path = dirname(dirname(dirname(__FILE__)));

require($root_path.'/Group-Office.php');

require('/etc/groupoffice/servermanager.inc.php');

require($root_path.'/modules/servermanager/classes/servermanager.class.inc.php');
$sm = new servermanager();

$fs = new filesystem();

$folders = $fs->get_folders('/etc/groupoffice');

foreach($folders as $folder)
{
	/*if(is_dir('/var/www/groupoffice/'.$folder['name']))
	{
		$rootpath = '/var/www/groupoffice/'.$folder['name'];
	}elseif('/var/www/'.$folder['name'])
	{
		$rootpath = '/var/www/'.$folder['name'];
	}else
	{
		$rootpath = '';
	}
	if(!empty($rootpath)
	{*/
		if(file_exists($folder['path'].'/config.php'))
		{
			require($folder['path'].'/config.php');

			$cnf['max_users']='5';
			$cnf['quota']='512000';
			
			$sm->write_config($folder['path'].'/config.php', $cnf);

			echo "Written ".$folder['path']."/config.php\n";
		}
	//}
}
