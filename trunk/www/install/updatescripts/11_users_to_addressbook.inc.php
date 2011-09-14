<?php

if(!isset($GLOBALS['GO_CONFIG'])){
	chdir(dirname(__FILE__));
	require('../../Group-Office.php');
}

if(!class_exists('GO', false)){
	require($GLOBALS['GO_CONFIG']->root_path.'GO.php');
}


GO::$ignoreAclPermissions=true;

$ab = GO_Addressbook_Model_Addressbook::model()->findSingleByAttribute('users', '1');//GO::t('users','base'));
if(!$ab){
	
//	$pdo->query("ALTER TABLE `ab_addressbooks` CHANGE `acl_read` `acl_id` INT( 11 ) NOT NULL DEFAULT '0'");
//	$pdo->query("ALTER TABLE `ab_addressbooks` CHANGE `id` `id` INT( 11 ) NOT NULL AUTO_INCREMENT");
//	$pdo->query("ALTER TABLE `ab_companies` CHANGE `id` `id` INT( 11 ) NOT NULL AUTO_INCREMENT");
//	$pdo->query("ALTER TABLE `ab_contacts` CHANGE `id` `id` INT( 11 ) NOT NULL AUTO_INCREMENT");
//	
//	$pdo->query("ALTER TABLE `ab_contacts` ADD `go_user_id` INT NOT NULL , ADD INDEX ( `go_user_id` )");
//
//	$pdo->query("ALTER TABLE `ab_addressbooks` DROP `acl_write`");
//
//	$pdo->query("ALTER TABLE `ab_addressbooks` CHANGE `id` `id` INT( 11 ) NOT NULL AUTO_INCREMENT");
//	$pdo->query("ALTER TABLE `ab_addressbooks` ADD `files_folder_id` INT NOT NULL");
//
//	$pdo->query("ALTER TABLE `ab_addressbooks` ADD `users` BOOLEAN NOT NULL ");
//
//	$pdo->query("ALTER TABLE `ab_contacts` DROP `source_id`"); 
//	$pdo->query("ALTER TABLE `ab_contacts` DROP `link_id` ");
	
	$ab = new GO_Addressbook_Model_Addressbook();
	$ab->name=GO::t('users');
	$ab->users=true;
	$ab->save();


	$pdo = GO::getDbConnection();

	$pdo->query("INSERT INTO ab_contacts (`addressbook_id`,`first_name`, `middle_name`, `last_name`, `initials`, `title`, `sex`, `birthday`, `email`, `department`, `function`, `home_phone`, `work_phone`, `fax`, `cellular`, `country`, `state`, `city`, `zip`, `address`, `address_no`,`go_user_id`) SELECT {$ab->id},`first_name`, `middle_name`, `last_name`, `initials`, `title`, `sex`, `birthday`, `email`, `department`, `function`, `home_phone`, `work_phone`, `fax`, `cellular`, `country`, `state`, `city`, `zip`, `address`, `address_no`,`id`  FROM `go_users` ");

}

