<?php
//start session
require('../../Group-Office.php');

$tmp_file = GO::config()->tmpdir.'/'.md5(uniqid(time())).'txt';
file_put_contents($tmp_file, GO::security()->user_id);

if(empty(GO::config()->phpbb3_url))
{
	exit('Error: you must configure phpbb3_url in your config.php file');
}

$url = GO::config()->phpbb3_url.'?goauth='.base64_encode($tmp_file).'&sid='.md5(uniqid(time()));
header('Location: '.$url);
?>