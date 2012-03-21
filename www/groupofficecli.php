#!/usr/bin/php
<?php
echo "\nGroup-Office CLI - Copyright Intermesh BV.\n\n";

if(PHP_SAPI!='cli')
	exit("ERROR: This script must be run on the command line\n\n");

$root = dirname(__FILE__).'/';
chdir($root);
//on the command line you can pass -c=/path/to/config.php to set the config file.

require_once($root.'go/base/util/Cli.php');

$args = GO_Base_Util_Cli::parseArgs();

if(isset($args['c'])){
	define("GO_CONFIG_FILE", $args['c']);
}

//initialize autoloading of library
require_once($root.'go/GO.php');
GO::init();

if(empty($args['r'])){
	
	echo "ERROR: You must pass a controller route to use the command line script.\n".
		"eg.:\n\n".
		"sudo -u www-data php index.php -c=/path/to/config.php -r=maintenance/upgrade --param=value\n\n";
	exit();

}elseif(isset($args['u']) && isset($args['p']))
{
	$user = GO::session()->login($args['u'], $args['p']);
	if(!$user){
		die("Login failed for user ".$args['u']."\n");
	}
	unset($args['u'],$args['p']);
}

GO::router()->runController();
