#!/usr/bin/php
<?php
require_once('/etc/groupoffice/config.php');
require($config['root_path'].'Group-Office.php');




if(!isset($GLOBALS['GO_MODULES']->modules['servermanager']))
{
	$GLOBALS['GO_MODULES']->add_module('servermanager');
}

