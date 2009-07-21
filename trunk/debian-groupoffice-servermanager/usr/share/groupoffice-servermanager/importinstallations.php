#!/usr/bin/php
<?php
if(isset($argv[1]))
define('CONFIG_FILE', $argv[1]);

$root_path = dirname(dirname(dirname(__FILE__)));
require($root_path.'/Group-Office.php');

require('/etc/groupoffice/servermanager.inc.php');

require_once ($GO_MODULES->modules['servermanager']['class_path']."servermanager.class.inc.php");
$servermanager = new servermanager();


if(!isset($GO_MODULES->modules['servermanager']))
{
	die('Fatal error: servermanager module must be installed');
}

require_once($GO_CONFIG->class_path.'filesystem.class.inc');
$fs = new filesystem();

//$roots=array('/var/www/groupoffice', '/var/www');
$roots=array('/etc/groupoffice/');

foreach($roots as $root)
{
	$folders = $fs->get_folders($root);

	foreach($folders as $folder)
	{
		$installation['name']=$folder['name'];
		if(!$servermanager->get_installation_by_name($installation['name']))
		{
			echo 'Adding '.$installation['name']."\n";
			$servermanager->add_installation($installation);

		}
	}
}

?>