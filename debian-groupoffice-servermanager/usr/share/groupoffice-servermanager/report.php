#!/usr/bin/php
<?php
if(isset($argv[1]))
	define('CONFIG_FILE', $argv[1]);

//require('/etc/groupoffice/servermanager.inc.php');
//require_once('/etc/groupoffice/config.php');

require('/usr/share/groupoffice/Group-Office.php');


if(!isset($GO_MODULES->modules['serverclient'])) {
	die('Fatal error: serverclient module must be installed');
}

if(!isset($GO_MODULES->modules['servermanager'])) {
	die('Fatal error: servermanager module must be installed');
}

require_once($GO_CONFIG->class_path.'filesystem.class.inc');
$fs = new filesystem();

$roots=array($sm_config['install_path']);
//$roots=array('/var/www/groupoffice');

$configs=array();
if(file_exists('/etc/groupoffice/config.php')){
	$configs[]=array('name'=>'servermanager','conf'=>'/etc/groupoffice/config.php');
}
foreach($roots as $root) {
	$folders = $fs->get_folders($root);

	foreach($folders as $folder) {
		$conf = '/etc/groupoffice/'.$folder['name'].'/config.php';
		if(file_exists($conf)) {
			$configs[]=array('name'=>$folder['name'], 'conf'=>$conf);
		}else {
			$conf = $folder['path'].'/html/groupoffice/config.php';
			if(file_exists($conf)) {
				$configs[]=array('name'=>$folder['name'], 'conf'=>$conf);
			}
		}
	}
}

$lang['common']['default_salutation']['M']='Dear Mr';
$lang['common']['default_salutation']['F']='Dear Ms';


require_once($GO_MODULES->modules['servermanager']['class_path'].'servermanager.class.inc.php');
require_once($GO_MODULES->modules['serverclient']['class_path'].'serverclient.class.inc.php');
$sc = new serverclient();
$sm = new servermanager();


$sm->query("DELETE FROM sm_reports");

$logged_in=false;

$db2 = new db();

$db2->halt_on_error='report';

foreach($configs as $conf) {
	$sm->create_report($conf['name'], $conf['conf'], $sc);
}
