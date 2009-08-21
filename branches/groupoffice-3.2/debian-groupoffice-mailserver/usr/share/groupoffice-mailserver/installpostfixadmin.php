#!/usr/bin/php
<?php
require('/usr/share/groupoffice/Group-Office.php');

if(!isset($GO_MODULES->modules['postfixadmin']))
{
	$GO_MODULES->add_module('postfixadmin');
}

if(!isset($GO_MODULES->modules['serverclient']))
{
	$GO_MODULES->add_module('serverclient');
}

