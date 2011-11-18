<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @copyright Copyright Intermesh
 * @version $Id: json.php 8246 2011-10-05 13:55:38Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 */

$root = dirname(__FILE__).'/';

if(PHP_SAPI=='cli'){	
	//on the command line you can pass -c=/path/to/config.php to set the config file.
	
	require_once($root.'go/base/util/Cli.php');
	
	$args = GO_Base_Util_Cli::parseArgs();

	if(isset($args['c'])){
		define("GO_CONFIG_FILE", $args['c']);
	}
}

//initialize autoloading of library
require_once($root.'go/GO.php');
GO::init();


//check if GO is installed
//$installed=true;
//if(!GO::config()->get_config_file() || empty(GO::config()->db_user)){			
//	$installed=false;
//}else
//{
//	$stmt = GO::getDbConnection()->query("SHOW TABLES");
//	if(!$stmt->rowCount())
//		$installed=false;
//}
//if(!$installed){
//	header('Location: '.GO::config()->host.'install/');				
//	exit();
//}
//
////check for database upgrades
//$mtime = GO::config()->get_setting('upgrade_mtime');
//
//if($mtime!=GO::config()->mtime)
//{
//	GO::infolog("Running system update");
//	header('Location: '.GO::url('maintenance/upgrade'));
//	exit();
//}

//run controller.
$router = new GO_Base_Router();
$router->runController();