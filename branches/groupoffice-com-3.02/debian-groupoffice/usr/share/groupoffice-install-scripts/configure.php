#!/usr/bin/php
<?php
echo 'Configuring Group-Office'."\n";
if(!file_exists('/etc/groupoffice/config.php'))
{
	require('/etc/groupoffice/config-db.php');

	chdir(dirname(__FILE__));

	$replacements['db_name']=$dbname;
	$replacements['db_user']=$dbuser;
	$replacements['db_pass']=$dbpass;
	$replacements['domain']=$domain;
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

	$replacements['lang']=$arr[0];
	$replacements['country']=$arr[1];


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

	create_file('/etc/groupoffice/config.php', 'tpl/config.php', $replacements);
}

echo "Done!\n\n";
?>