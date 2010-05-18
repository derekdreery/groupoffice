#!/usr/bin/php
<?php
require('/etc/groupoffice/config.php');
require($config['root_path'].'Group-Office.php');


if(!isset($GO_MODULES->modules['postfixadmin']))
{
	$GO_MODULES->add_module('postfixadmin');
}

if(!isset($GO_MODULES->modules['serverclient']))
{
	$GO_MODULES->add_module('serverclient');
}

