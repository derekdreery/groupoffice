#!/usr/bin/php
<?php
require('/etc/groupoffice/servermanager.inc.php');

require($sm_config['source'].'Group-Office.php');


if(!isset($GO_MODULES->modules['servermanager']))
{
	$GO_MODULES->add_module('servermanager');
}

