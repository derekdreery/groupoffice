#!/usr/bin/php
<?php
require('/etc/groupoffice/config.php');
require($config['root_path'].'Group-Office.php');

try{
	if(!isset(GO::modules()->modules['postfixadmin']))
	{
		GO::modules()->add_module('postfixadmin');
	}

	if(!isset(GO::modules()->modules['serverclient']))
	{
		GO::modules()->add_module('serverclient');
	}
}
catch(Exception $e){
	echo 'ERROR: '.$e->getMessage();
}

