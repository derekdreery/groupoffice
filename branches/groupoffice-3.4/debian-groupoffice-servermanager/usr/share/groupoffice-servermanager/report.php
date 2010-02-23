#!/usr/bin/php
<?php
if(isset($argv[1]))
	define('CONFIG_FILE', $argv[1]);

ini_set('display_errors', 'on');
error_reporting(E_ALL);

require('/etc/groupoffice/servermanager.inc.php');
//require_once('/etc/groupoffice/config.php');

require('/usr/share/groupoffice/Group-Office.php');


if(!isset($GO_MODULES->modules['serverclient'])) {
	die('Fatal error: serverclient module must be installed');
}

if(!isset($GO_MODULES->modules['servermanager'])) {
	die('Fatal error: servermanager module must be installed');
}

//require_once($GO_CONFIG->class_path.'filesystem.class.inc');
//$fs = new filesystem();

$roots=array($sm_config['install_path']);
//$roots=array('/var/www/groupoffice');


$lang['common']['default_salutation']['M']='Dear Mr';
$lang['common']['default_salutation']['F']='Dear Ms';


require_once($GO_MODULES->modules['servermanager']['class_path'].'servermanager.class.inc.php');
require_once($GO_MODULES->modules['serverclient']['class_path'].'serverclient.class.inc.php');
$sc = new serverclient();
$sm = new servermanager();
$configs=$sm->get_all_config_files($roots);

foreach($configs as $conf) {
	//if($conf['conf']!=$GO_CONFIG->get_config_file()){
		echo 'Processing '.$conf['name']."\n";
		$sm->create_report($conf['name'], $conf['conf'], $sc);
	//}
}
