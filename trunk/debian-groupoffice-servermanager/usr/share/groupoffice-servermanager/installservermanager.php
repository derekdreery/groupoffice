#!/usr/bin/php
<?php
require('/etc/groupoffice/servermanager.inc.php');

$go_src_path = isset($sm_config['go_src_path']) ? $sm_config['go_src_path'] : '/usr/share/groupoffice/';
require($go_src_path.'Group-Office.php');


if(!isset($GO_MODULES->modules['servermanager']))
{
	$GO_MODULES->add_module('servermanager');
}

