#!/usr/bin/php
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
 * @version $Id$
 * @author Merijn Schering <mschering@intermesh.nl>
 */
$root = dirname(dirname(__FILE__)).'/';

if(PHP_SAPI=='cli'){	
	//on the command line you can pass -c=/path/to/config.php to set the config file.
	
	require_once($root.'go/base/util/Cli.php');
	
	$args = GO_Base_Util_Cli::parseArgs();
	
	if(isset($args['c'])){
		define("GO_CONFIG_FILE", $args['c']);
	}
}

try{
$exampleUsage = 'sudo -u www-data php /var/www/trunk/www/install/autoinstall.php --adminusername=admin --adminpassword=admin --adminemail=admin@intermesh.dev --modules="email,addressbook,files"';
$requiredArgs = array('adminusername','adminpassword','adminemail');

foreach($requiredArgs as $ra){
	if(empty($args[$ra])){
		throw new Exception($ra." must be supplied.\n\nExample usage:\n\n".$exampleUsage."\n\n");
	}
}

chdir(dirname(__FILE__));
require('../GO.php');

GO::setIgnoreAclPermissions();

$stmt = GO::getDbConnection()->query("SHOW TABLES");
if ($stmt->rowCount())
	throw new Exception("Automatic installation of Group-Office aborted because database is not empty");
else
	echo "Database connection established. Database is empty\n";

GO_Base_Util_SQL::executeSqlFile('install.sql');

$dbVersion = GO_Base_Util_Common::countUpgradeQueries("updates.php");

GO::config()->save_setting('version', $dbVersion);
GO::config()->save_setting('upgrade_mtime', GO::config()->mtime);

$adminGroup = new GO_Base_Model_Group();
$adminGroup->id = 1;
$adminGroup->name = GO::t('group_admins');
$adminGroup->save();

$everyoneGroup = new GO_Base_Model_Group();
$everyoneGroup->id = 2;
$everyoneGroup->name = GO::t('group_everyone');
$everyoneGroup->save();

$internalGroup = new GO_Base_Model_Group();
$internalGroup->id = 3;
$internalGroup->name = GO::t('group_internal');
$internalGroup->save();

//GO::config()->register_user_groups = GO::t('group_internal');
//GO::config()->register_visible_user_groups = GO::t('group_internal');

$modules = GO::modules()->getAvailableModules();

if(isset($args['modules'])){
	$installModules = explode(',', $args['modules']);
	
}elseif(!empty(GO::config()->allowed_modules)){
	$installModules=explode(',',GO::config()->allowed_modules);
}

if(isset($installModules)){
	$installModules[]="modules";
	$installModules[]="users";
	$installModules[]="groups";
}

foreach ($modules as $moduleClass) {
	$moduleController = new $moduleClass;
	if ((!isset($installModules) && $moduleController->autoInstall()) || (isset($installModules) && in_array($moduleController->id(), $installModules))) {
		
		echo "Installing module ".$moduleController->id()."\n";
		
		$module = new GO_Base_Model_Module();
		$module->id = $moduleController->id();
		$module->save();
	}
}

$admin = new GO_Base_Model_User();
$admin->first_name = GO::t('system');
$admin->last_name = GO::t('admin');
$admin->username = $args['adminusername'];
$admin->password = $args['adminpassword'];
$admin->email = GO::config()->webmaster_email = $args['adminemail'];

GO::config()->save();

$admin->save();

$admin->checkDefaultModels();

$adminGroup->addUser($admin->id);


echo "Database created successfully\n";
}catch(Exception $e){
	
	echo $e->getMessage()."\n";
	
	exit(1);
}