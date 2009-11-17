#!/usr/bin/php
<?php
require('/etc/groupoffice/config-mailserver.inc.php');

chdir(dirname(__FILE__));

$replacements['db_name']=$dbname;
$replacements['db_user']=$dbuser;
$replacements['db_pass']=$dbpass;
$replacements['domain']=$domain;

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

if(!file_exists('/etc/groupoffice/servermanager.inc.php'))
	create_file('/etc/groupoffice/servermanager.inc.php', 'tpl/etc/groupoffice/servermanager.inc.php', $replacements);

chgrp('/etc/groupoffice/servermanager.inc.php', 'www-data');
chmod('/etc/groupoffice/servermanager.inc.php', 640);

if(!file_exists('/etc/apache2/sites-enabled/000-groupoffice'))
	create_file('/etc/apache2/sites-enabled/000-groupoffice', 'tpl/etc/apache2/sites-enabled/000-groupoffice', $replacements);

if(file_exists('/etc/apache2/sites-enabled/000-default'))
	unlink('/etc/apache2/sites-enabled/000-default');

echo "Configuring sudo\n";
set_value('/etc/sudoers','www-data ALL=NOPASSWD:/usr/share/groupoffice/modules/servermanager/sudo.php');

echo "Done!\n\n";
?>