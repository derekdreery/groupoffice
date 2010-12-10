<?php

/*
 * export_installation.php
 *
 * Usage:
 * ((to use this file, type the next line in the console))
 * php export_installation.php --domain=[sourcedomain] --targethost=[remotehost]
 * 
 * optional:
 *	--domain_config=[pathtoconfigfile]
 *	--targetpath=[destinationpath]
 *  --targethostport=[portoftargethostssh]
 */

// De benodigde bestanden includen
chdir(dirname(__FILE__));
require('/usr/share/groupoffice/cli-functions.inc.php');

$args = parse_cli_args($argv);

// Checken of het domein is meegegeven als argument.
if(!isset($args['domain']))
	die("@#!&@! - No domain given in the arguments!!\n\n");

// Checken of het target_host is meegegeven als argument.
if(!isset($args['targethost']))
	die("@#!&@! - No target host given in the arguments!!\n\n");

// Checken of de domein_config is meegegeven als argument, als dat niet zo is dan neemt hij een standaard pad.
if(!isset($args['domain_config']))
	$args['domain_config'] = '/etc/groupoffice/'.$args['domain'].'/config.php';

// Checken of de config file bestaat
if(!file_exists($args['domain_config']))
	die("@#!&@! - ".$args['domain_config']." not found!\n\n");

// Checken of je leesrechten hebt op de config file.
if(!is_readable(($args['domain_config'])))
	die("@#!&@! - No rights to read ".$args['domain_config']."!\n\n");

// De domain config includen als hij bestaat
require($args['domain_config']);

// De file "full_dump.sql" maken en in de map "MYSQLDUMP" plaatsen.
$sqldump_destination_folder = 'MYSQLDUMP';
$sqldump_destination_file = 'full_dump.sql';

// De map aanmaken als hij nog niet bestaat
if(!is_dir($config['file_storage_path'].$sqldump_destination_folder))
{
	if(!mkdir($config['file_storage_path'].$sqldump_destination_folder))
		die("@#!&@! - No rights to create the folder: ".$config['file_storage_path'].$sqldump_destination_folder."!\n\n");
}

// De database dumpen naar het bestand
$sqldump_destination = $config['file_storage_path'].$sqldump_destination_folder.'/'.$sqldump_destination_file;
$mysql_dump_command = 'mysqldump --default-character-set=utf8 -u '.$config['db_user'].' --password="'.$config['db_pass'].'" '.$config['db_name'].' > '.$sqldump_destination;
system($mysql_dump_command);

if(file_exists($sqldump_destination))
{

	if(!isset($args['targetpath']))
		$target_path = "/home/govhosts/".$args['domain']."/data/"; // == $config['file_storage_path']

	$thport = '';
	if(isset($args['targethostport']))
		$thport = '-p '.$args['targethostport'];

	$create_target_cmd = 'ssh '.$thport.' root@'.$args['targethost'].' "mkdir -p '.$target_path.'"';
	system($create_target_cmd, $status);

	if($status!=0)
		die("@#!&@! - No target dir created!\n\n");

	$rsync_command = 'rsync -r -v -rltD -e "ssh '.$thport.' -i /root/.ssh/id_rsa" '.$config['file_storage_path'].' root@'.$args['targethost'].':'.$target_path;
	system($rsync_command, $status);

	if($status!=0)
		die("@#!&@! - Error while syncing to '".$target_path."'!\n\n");

	$config_file_path = dirname($args['domain_config']);

	$create_target_cmd2 = 'ssh '.$thport.' root@'.$args['targethost'].' "mkdir -p '.$config_file_path.'"';
	system($create_target_cmd2, $status);

	if($status!=0)
		die("@#!&@! - No target config dir created!\n\n");

	$rsync_command2 = 'rsync -r -v -rltD -e "ssh '.$thport.' -i /root/.ssh/id_rsa" '.$args['domain_config'].' root@'.$args['targethost'].':'.$args['domain_config'];
	system($rsync_command2, $status);

	if($status==0)
	{
		echo "\n\n\n\n";
		echo "  ********************************\n";
		echo "  **                            **\n";
		echo "  **    Export is successful    **\n";
		echo "  **                            **\n";
		echo "  ********************************\n";
		echo "\n\n\n\n";
	}
	else
	{
		echo "\n\n\n\n";
		echo "  ********************************\n";
		echo "  **                            **\n";
		echo "  **   @#!&@! - Export failed   **\n";
		echo "  **                            **\n";
		echo "  ********************************\n";
		echo "\n\n\n\n";
	}
}
else
	die("@#!&@! - De mysql dump is mislukt!\n\n");

?>