#!/usr/bin/php
<?php
require('/etc/groupoffice/config.php');
require($config['root_path'].'Group-Office.php');

try{
	if(!isset($GLOBALS['GO_MODULES']->modules['postfixadmin']))
	{
		$GLOBALS['GO_MODULES']->add_module('postfixadmin');
	}

	if(!isset($GLOBALS['GO_MODULES']->modules['serverclient']))
	{
		$GLOBALS['GO_MODULES']->add_module('serverclient');
	}
}
catch(Exception $e){
	echo 'ERROR: '.$e->getMessage();
}

