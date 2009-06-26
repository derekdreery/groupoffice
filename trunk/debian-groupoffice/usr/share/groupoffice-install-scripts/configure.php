#!/usr/bin/php
<?php
require('/etc/groupoffice/config-db.php');

chdir(dirname(__FILE__));

$replacements['db_name']=$dbname;
$replacements['db_user']=$dbuser;
$replacements['db_pass']=$dbpass;
$replacements['domain']=$domain;
$replacements['timezone']=trim(file_get_contents('/etc/timezone'));

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
		$data .= "\n".$str;
	}
	file_put_contents($file, $data);
}


echo 'Configuring Group-Office'."\n";

create_file('/etc/groupoffice/config.php', 'tpl/config.php', $replacements);

echo "Done!\n\n";
?>