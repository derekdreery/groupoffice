#!/usr/bin/php
<?php

chdir(dirname(__FILE__));
require('../../Group-Office.php');


echo "\n\nIntermesh Group-Office update service client version ".$GO_CONFIG->version."\n\n";
echo "Written by Merijn Schering <mschering@intermesh.nl>\n";
echo "Copyright Intemresh 2003-2007\n\n";

function status($data)
{
	echo $data."\n";
}
/*
function read() {
    $fp=fopen("/dev/stdin", "r");
    $input=fgets($fp, 255);
    fclose($fp);
   	return str_replace("\n", "", $input);
}

print('Username:');
$username=read();

print('Password:');
$password=read();*/

$host = $GO_CONFIG->get_setting('updateclient_host');
$username = $GO_CONFIG->get_setting('updateclient_username');
$password = $GO_CONFIG->get_setting('updateclient_password');

require('../updateclient/classes/updateclient.class.inc');
$uc= new updateclient($host,$username,$password);




$tmpdir = $GO_CONFIG->file_storage_path.'updateclient/';
mkdir_recursive($tmpdir);
chdir($tmpdir);

status('Checking '.$uc->remote_host.' for available updates for '.$uc->host);
if(!$uc->check())
{
	exit('Failed to connect to update server!');
}

//var_dump($uc);

if($uc->status=='401')
{
	status('Fatal error: You are not authorized');

}elseif($uc->status=='500')
{
	status('Fatal error: Internal server error');

}else {

	if(count($uc->packages)==0)
	{
		status('Your Group-Office is already up to date');
	}else {
		status('Found '.count($uc->packages));
	}
	
	status('Backing up database '.$GO_CONFIG->db_name);
	system('mysqldump '.$GO_CONFIG->db_name.' -u '.$GO_CONFIG->db_user.' --password='.$GO_CONFIG->db_pass.' > '.$GO_CONFIG->root_path.'groupoffice-'.date('Ymd').'.sql');
	
	status('Backing up Group-Office: '.$GO_CONFIG->root_path);
	chdir($GO_CONFIG->root_path);
	system($GO_CONFIG->cmd_tar.' -czf '.$GO_CONFIG->tmpdir.'groupoffice-backup-'.date('Ymd').'.tar.gz *');
	rename($GO_CONFIG->tmpdir.'groupoffice-backup-'.date('Ymd').'.tar.gz',	$tmpdir.'groupoffice-backup-'.date('Ymd').'.tar.gz');
	
	foreach($uc->packages as $package)
	{
		chdir($tmpdir);
		
		if($package['date']>$package['local_date'])
		{
			status('Found an update for package '.$package['package_name']);
			
			$url = $uc->remote_url.'download.php?sid='.$uc->remote_sid.'&license_id='.$package['license_id'].'&package_id='.$package['id'];
		
			status('Downloading '.$package['package_name'].'.tar.gz');
			
			system(escapeshellcmd('wget --no-check-certificate '.$url));
			if(!file_exists(basename($url)))
			{
				status('Failed to download package!');
				status('exited');
				die();
			}
			
			rename(basename($url), $package['package_name'].'.tar.gz');
			status('Downloaded '.$package['package_name'].'.tar.gz');
			
			status('Entering '.$GO_CONFIG->root_path);
			chdir($GO_CONFIG->root_path);
			status('Unpacking new files');
			system($GO_CONFIG->cmd_tar.' --overwrite -zxf '.$tmpdir.$package['package_name'].'.tar.gz');
			
			$update_package['id']=$package['local_package_id'];
			$update_package['date']=$package['date'];
			$update_package['version']=$package['version'];
			$uc->update_package($update_package);
		}else {
			status('Package '.$package['package_name'].' is already up to date');
		}
	}
	
	status('All packages downloaded and installed');
	status('Updating database '.$GO_CONFIG->db_name);
	$line_break="\n";
	$quiet=true;
	define('GO_LOADED',true);
	require($GO_CONFIG->root_path.'install/upgrade.php');
	status('Update complete! Intermesh thanks you for using Group-Office.');
}
status('exited');
