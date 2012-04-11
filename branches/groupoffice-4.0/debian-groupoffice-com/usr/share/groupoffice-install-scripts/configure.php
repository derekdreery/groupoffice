#!/usr/bin/php
<?php
echo 'Configuring Group-Office'."\n";

$config_file = '/etc/groupoffice/config.php';
if(file_exists($config_file))
{
	//don't overwrite an existing configuration. Create a file with date suffix.
	$config_file = '/etc/groupoffice/config.php.'.date('Ymd');
}
require('/etc/groupoffice/config-db.php');

chdir(dirname(__FILE__));

$replacements['db_name']=$dbname;
$replacements['db_user']=$dbuser;
$replacements['db_pass']=$dbpass;
//$replacements['domain']=$domain;
$replacements['timezone']=trim(file_get_contents('/etc/timezone'));


exec('locale',$output);

$eq_pos = strpos($output[0], '=');

if($eq_pos)
{
	$locale = substr($output[0],$eq_pos+1);
	$dot_pos = strpos($locale,'.');
	if($dot_pos)
	{
		$locale = substr($locale,0, $dot_pos);
	}
}else
{
	$locale = 'en_US';
}

$arr = explode('_', $locale);

if(isset($arr[1]))
{
	$replacements['lang']=$arr[0];
	$replacements['country']=$arr[1];
}else
{
	$replacements['lang']='en';
	$replacements['country']='NL';
}


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

create_file($config_file, 'tpl/config.php', $replacements);


chgrp('/etc/groupoffice/config.php', 'www-data');
chmod('/etc/groupoffice/config.php', 0640);

require_once('/etc/groupoffice/config.php');

system('/usr/bin/php '.$config['root_path'].'install/autoinstall.php -c=/etc/groupoffice/config.php --adminpassword=admin --adminusername=admin');
system('/usr/bin/php '.$config['root_path'].'groupofficecli.php -r=maintenance/upgrade -c=/etc/groupoffice/config.php');

echo "Done!\n\n";
?>
