<?php

/*
 * import_installation.php
 *
 * Usage:
 * ((to use this file, type the next line in the console))
 * php import_installation.php --domain=[theimporteddomain]
 *
 * optional:
 * --go_config=[theconfigfilepath]
 * --importroot[thefolderwiththerootfiles]
 */

// De benodigde bestanden includen
chdir(dirname(__FILE__));
require('/usr/share/groupoffice/cli-functions.inc.php');
require('/usr/share/groupoffice/Group-Office.php');

$args = parse_cli_args($argv);

if(empty($args['domain'])){
	
	echo 'Usage:
  ((to use this file, type the next line in the console))
  php import_installation.php --domain=[sourcedomain] --targethost=[remotehost]
 
  optional:
 	--go_config=[pathtoconfigfile]
 	--targetpath=[destinationpath]
  --targethostport=[portoftargethostssh]
	--rename_domain=[olddomainname]
';
	
	exit();
}

// Checken of het te importeren domein opgegeven is.
if(!isset($args['domain']))
	die("The domain argument is required\n\n");

if(isset($args['rename_domain']))
{
	$old_dir = '/home/govhosts/'.$args['rename_domain'];

	if(!is_dir($old_dir)){
		die('Could not rename '.$old_dir.' because it does not exist!');
	}
	$cmd = 'mv "'.$old_dir.'" "/home/govhosts/'.$args['domain'].'"';
	system($cmd);


	$old_dir = '/etc/groupoffice/'.$args['rename_domain'];

	if(!is_dir($old_dir)){
		die('Could not rename '.$old_dir.' because it does not exist!');
	}
	$cmd = 'mv "'.$old_dir.'" "/etc/groupoffice/'.$args['domain'].'"';
	system($cmd);
}

// Checken of het te importeren domein opgegeven is.
if(!isset($args['importroot']))
		$args['importroot'] = "/home/govhosts/".$args['domain']."/data/";

// Checken of het te importeren domein opgegeven is.
if(!isset($args['go_config']))
	$args['go_config'] = '/etc/groupoffice/'.$args['domain'].'/config.php';

// Checken of de config file bestaat
if(!file_exists($args['go_config']))
	die($args['go_config']." not found!\n\n");

// Een symlink aanmaken in de map $args['importroot'] - data
if(!is_dir('/home/govhosts/'.$args['domain'].'/groupoffice'))
{
	$symlink = 'ln -s /usr/share/groupoffice /home/govhosts/'.$args['domain'].'/groupoffice';
	system($symlink, $status);

	if($status!=0)
		die("@#!&@! - symlink is not created!\n\n");
}

// include de config zodat deze uitgelezen kan worden
include_once($args['go_config']);

// Maak een nieuwe instantie aan van config voor het geval er nog oude waardes nodig zijn.
$newconfig = $config;

// Set de nieuwe waardes in de nieuwe config array
$newconfig['file_storage_path'] = $args['importroot'];
$newconfig['host'] = '/';
$newconfig['root_path'] = '/home/govhosts/'.$args['domain'].'/groupoffice/';

// Schrijf de nieuwe waardes in de config file.
$handle = @fopen($args['go_config'], 'w+');
fwrite($handle,"<?php\n");
foreach($newconfig as $key => $value)
{
	// handle voor de booleans
	if($value === true)
		$value = 'true';
	else if($value === false)
		$value = 'false';
	else
		$value = '"'.$value.'"';

	// Text opbouwen
	$text = '$config[\''.$key.'\']='.$value.';';

	// Text naar bestand schrijven
	fwrite($handle,$text."\n");
}
fclose($handle);

// De owner van de root map aanpassen naar de apache user
system('chown www-data:www-data -R '.$args['importroot']);

// De rechten van de config file aanpassen TODO: misschien beter met php
system('chmod 644 '.$args['go_config']);

// Zorgen dat de config overeen komt met de nieuwe file.
$config = $newconfig;


// Maak de nieuwe database aan
require_once('/etc/groupoffice/servermanager.inc.php');

$db = new db();
$db->user=$sm_config['mysql_user'];
$db->password=$sm_config['mysql_pass'];
$db->host=$sm_config['mysql_host'];

$db_name = $config['db_name'];

$db->query("SET NAMES UTF8");

$exists=false;
$db->query('SHOW DATABASES');
while($db->next_record(MYSQL_NUM) && !$exists){
	if($db->f(0)==$db_name){
		$exists=true;
	}

}

if($exists)
{
	echo "Database '".$db_name."' exists. Are you sure you want to overwrite it (N/y) \n"; // Output - prompt user
	$answer = strtolower(trim(fgets(STDIN)));
	if($answer!='y'){
		exit("Aborted\n");
	}

	$db->query("DROP DATABASE `$db_name`");
}


$db->query("CREATE DATABASE `$db_name`");

$sql = "GRANT ALL PRIVILEGES ON `".$config['db_name']."`.*	TO ".
				"'".$config['db_user']."'@'".$sm_config['mysql_host']."' ".
				"IDENTIFIED BY '".$config['db_pass']."' WITH GRANT OPTION";
$db->query($sql);

$db->query('FLUSH PRIVILEGES');

// TODO: De SQL importeren in de database
$sql_import_file = $config['file_storage_path'].'MYSQLDUMP/full_dump.sql';

if(!file_exists($sql_import_file))
	die("@#!&@! - ".$sql_import_file." not found!\n\n");

$importdbcall = 'mysql '.$db_name.' -u '.$config['db_user'].' --password="'.$config['db_pass'].'" < '.$sql_import_file;
system($importdbcall,$status);
if($status!=0)
		die("@#!&@! - The import of the sql file failed!\n\n");

//Het groupoffice installatie check script uitvoeren
system('/usr/share/groupoffice/groupofficecli.php -r=servermanager/installation/import', $status);
if($status==0)
{
	echo "\n\n\n\n";
	echo "  *********************************\n";
	echo "  **                             **\n";
	echo "  **     Import is successful    **\n";
	echo "  **                             **\n";
	echo "  *********************************\n";
	echo "\n\n";
	echo "The installation is now available at: ".$args['domain'].".\n";
	echo "Look in the server manager to see the created domain\n";
	echo "\n\n";
}
else
{
	echo "\n\n\n\n";
	echo "  *********************************\n";
	echo "  **                             **\n";
	echo "  **    @#!&@! - Import failed   **\n";
	echo "  **                             **\n";
	echo "  *********************************\n";
	echo "\n\n\n\n";
}

?>
