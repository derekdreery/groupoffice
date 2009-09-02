#!/usr/bin/php
<?php
if(isset($argv[1]))
define('CONFIG_FILE', $argv[1]);

require('/etc/groupoffice/servermanager.inc.php');

$go_src_path = isset($sm_config['go_src_path']) ? $sm_config['go_src_path'] : '/usr/share/groupoffice/';
require($go_src_path.'Group-Office.php');



if(!isset($GO_MODULES->modules['servermanager']))
{
	die('Fatal error: servermanager module must be installed');
}

require_once($GO_CONFIG->class_path.'filesystem.class.inc');
$fs = new filesystem();

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