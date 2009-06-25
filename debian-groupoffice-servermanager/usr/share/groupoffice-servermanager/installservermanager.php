#!/usr/bin/php
<?php
require('/usr/share/groupoffice/Group-Office.php');

if(!isset($GO_MODULES->modules['servermanager']))
{
	$GO_MODULES->add_module('servermanager');
}

