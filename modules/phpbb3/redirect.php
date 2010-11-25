<?php
//start session
require('../../Group-Office.php');

$tmp_file = $GO_CONFIG->tmpdir.'/'.md5(uniqid(time())).'txt';
file_put_contents($tmp_file, $GO_SECURITY->user_id);

if(empty($GO_CONFIG->phpbb3_url))
{
	exit('Error: you must configure phpbb3_url in your config.php file');
}

$url = $GO_CONFIG->phpbb3_url.'?goauth='.base64_encode($tmp_file).'&sid='.md5(uniqid(time()));
header('Location: '.$url);
?>