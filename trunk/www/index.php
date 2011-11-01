<?php

$root = dirname(__FILE__).'/';

if(PHP_SAPI=='cli'){	
	//on the command line you can pass -c=/path/to/config.php to set the config file.
	
	require_once($root.'go/base/util/Cli.php');
	
	$args = GO_Base_Util_Cli::parseArgs();

	if(isset($args['c'])){
		define("GO_CONFIG_FILE", $args['c']);
	}
}


require_once($root.'go/GO.php');
GO::init();

$router = new GO_Base_Router();
$router->runController();