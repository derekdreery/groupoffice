#!/usr/bin/php
<?php
$root = dirname(__FILE__) . '/';
//chdir($root);
//on the command line you can pass -c=/path/to/config.php to set the config file.

require_once($root . 'go/base/util/Cli.php');

$args = GO_Base_Util_Cli::parseArgs();

if (isset($args['c'])) {
	define("GO_CONFIG_FILE", $args['c']);
}

//initialize autoloading of library
require_once($root . 'go/GO.php');
GO::init();

echo "\nGroup-Office CLI - Copyright Intermesh BV.\n\n";

if (PHP_SAPI != 'cli')
	exit("ERROR: This script must be run on the command line\n\n");

if (empty($args['r'])) {

	echo "ERROR: You must pass a controller route to use the command line script.\n" .
	"eg.:\n\n" .
	"sudo -u www-data php index.php -c=/path/to/config.php -r=maintenance/upgrade --param=value\n\n";
	exit();
} elseif (isset($args['u'])) {// && isset($args['p']))
	$prompt = "Enter password:";
	$command = "/usr/bin/env bash -c 'echo OK'";
	if (rtrim(shell_exec($command)) !== 'OK') {
		trigger_error("Can't invoke bash to get password");
	}
	$command = "/usr/bin/env bash -c 'read -s -p \""
					. $prompt
					. "\" mypassword && echo \$mypassword'";
	
	$password = rtrim(shell_exec($command));
	
	echo "\n";

	$user = GO::session()->login($args['u'], $password);
	if (!$user) {
		echo "Login failed for user " . $args['u'] . "\n";
		exit(1);
	}
	unset($args['u']);
}

GO::router()->runController();
