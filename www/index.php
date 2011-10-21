<?php
if(PHP_SAPI=='cli'){	
	//on the command line you can pass -c=/path/to/config.php to set the config file.
	if(preg_match('/-c=([^-]+)/', $argv[1], $matches)){
		define("GO_CONFIG_FILE", trim($matches[1]));
	}
}

$root = dirname(__FILE__).'/';
require_once($root.'go/GO.php');
GO::init();

$router = new GO_Base_Router();
$router->runController();