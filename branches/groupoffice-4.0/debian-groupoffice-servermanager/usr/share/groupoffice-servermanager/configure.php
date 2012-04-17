#!/usr/bin/php
<?php
require('/etc/groupoffice/config-servermanager.inc.php');

chdir(dirname(__FILE__));

$replacements['db_name']=$dbname;
$replacements['db_user']=$dbuser;
$replacements['db_pass']=$dbpass;
$replacements['domain']=$wildcarddomain;

function create_file($file, $tpl, $replacements) {
	$data = file_get_contents($tpl);

	foreach($replacements as $key=>$value) {
		$data = str_replace('{'.$key.'}', $value, $data);
	}

	file_put_contents($file, $data);
}

function set_value($file, $str) {
	$data = file_get_contents($file);

	if(!strpos($data, $str)) {
		$data .= $str."\n";
	}
	file_put_contents($file, $data);
}


echo 'Configuring apache'."\n";

if(!file_exists('/etc/apache2/sites-enabled/000-groupoffice'))
	create_file('/etc/apache2/sites-enabled/000-groupoffice', 'tpl/etc/apache2/sites-enabled/000-groupoffice', $replacements);

//if(file_exists('/etc/apache2/sites-enabled/000-default'))
//	unlink('/etc/apache2/sites-enabled/000-default');

echo "Configuring sudo\n";
set_value('/etc/sudoers','www-data ALL=NOPASSWD:/usr/share/groupoffice/groupofficecli.php');

set_value('/etc/groupoffice/config.php','$config["servermanager_wildcard_domain"]="'.$wildcarddomain.'";');

set_value('/etc/groupoffice/config.php','$config["servermaner_trials_enabled"]=false;');

echo "Done!\n\n";
?>