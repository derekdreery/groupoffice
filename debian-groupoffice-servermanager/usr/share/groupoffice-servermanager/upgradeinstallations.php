#!/usr/bin/php
<?php
if(isset($argv[1]))
define('CONFIG_FILE', $argv[1]);

require('/etc/groupoffice/servermanager.inc.php');

require($sm_config['source'].'Group-Office.php');



if(!isset($GO_MODULES->modules['servermanager']))
{
	die('Fatal error: servermanager module must be installed');
}

require_once($GO_CONFIG->class_path.'filesystem.class.inc');
$fs = new filesystem();

if(file_exists('/etc/groupoffice/config.php')){
	exec('php '.$sm_config['source'].'install/upgrade.php /etc/groupoffice/config.php');
}

//$roots=array('/var/www/groupoffice', '/var/www');
$roots=array($sm_config['install_path']);

foreach($roots as $root)
{
	$folders = $fs->get_folders($root);

	foreach($folders as $folder)
	{
		$conf = '/etc/groupoffice/'.$folder['name'].'/config.php';
		if(file_exists($conf))
		{
			echo 'Changing dir to: '.$folder['path'].'/groupoffice/install/'."\n";
			chdir($folder['path'].'/groupoffice/install/');
			
			system('php upgrade.php '.$conf);
			if(isset($argv[2]) && $argv[2]=='check')
			{
				chdir($folder['path'].'/groupoffice/modules/tools/');
				system('php dbcheck.php '.$conf);
			}
		}
	}
}

?>