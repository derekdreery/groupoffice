#!/usr/bin/php
<?php
if(isset($argv[1]))
	define('CONFIG_FILE', $argv[1]);

ini_set('display_errors', 'on');
error_reporting(E_ALL);

require('/etc/groupoffice/servermanager.inc.php');
//require_once('/etc/groupoffice/config.php');

require('/usr/share/groupoffice/Group-Office.php');


if(!isset(GO::modules()->modules['serverclient'])) {
	die('Fatal error: serverclient module must be installed');
}

if(!isset(GO::modules()->modules['servermanager'])) {
	die('Fatal error: servermanager module must be installed');
}

$roots=array($sm_config['install_path']);
//$roots=array('/var/www/groupoffice');

require_once(GO::modules()->modules['servermanager']['class_path'].'servermanager.class.inc.php');
$sm = new servermanager();
$configs=$sm->get_all_config_files($roots);


$db = new db();
$db->halt_on_error='report';

$emails=array();

/*if($db->table_exists('pa_mailboxes')){
	$db->query("SELECT username FROM pa_mailboxes");
	while($record=$db->next_record()){
		$emails[]=$record['username'];
	}
}*/

foreach($configs as $conf) {

	require($conf['conf']);

	if(empty($config['db_name'])) {
		echo 'Warning: empty db_name in '.$conf['conf']."\n";
	}else {
		$db->halt_on_error = "no";
		$use = $db->query("USE `".$config['db_name']."`");
		$db->halt_on_error='report';
		if($use){
			if($db->table_exists('go_users')){
				$prefix='go_';
			}else
			{
				$prefix='';
			}

			$sql = "SELECT DISTINCT email FROM ".$prefix."users LIMIT 0,2";
			$db->query($sql);
			while($record=$db->next_record()){
				$emails[]=$record['email'];
			}
		}
	}
}

$emails=array_unique($emails);
echo count($emails)."\n\n";
echo implode(', ', $emails)."\n";
