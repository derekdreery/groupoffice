#!/usr/bin/php
<?php
require_once('/etc/groupoffice/config.php');
require($config['root_path'].'Group-Office.php');




if(!isset(GO::modules()->modules['servermanager']))
{
	GO::modules()->add_module('servermanager');
}

