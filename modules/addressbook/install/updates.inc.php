<?php
$updates[] = "ALTER TABLE `ab_mailing_contacts` ADD `mail_sent` ENUM( '0', '1' ) NOT NULL ;";
$updates[] = "ALTER TABLE `ab_mailing_contacts` ADD `status` VARCHAR( 100 ) NOT NULL ";

$updates[] = "ALTER TABLE `ab_mailing_companies` ADD `mail_sent` ENUM( '0', '1' ) NOT NULL ";
$updates[] = "ALTER TABLE `ab_mailing_companies` ADD `status` VARCHAR( 100 ) NOT NULL ";

$updates[] = "ALTER TABLE `ab_mailing_users` ADD `mail_sent` ENUM( '0', '1' ) NOT NULL ";
$updates[] = "ALTER TABLE `ab_mailing_users` ADD `status` VARCHAR( 100 ) NOT NULL ";


$updates[] = "CREATE TABLE IF NOT EXISTS `ml_mailings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `message_path` varchar(255) NOT NULL,
  `ctime` int(11) NOT NULL,
  `mailing_group_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `total` int(11) NOT NULL,
  `sent` int(11) NOT NULL,
  `errors` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";


$updates[] = " RENAME TABLE `ab_mailing_groups`  TO `ml_mailing_groups` ;";
$updates[] = " RENAME TABLE `ab_mailing_companies`  TO `ml_mailing_companies` ;";
$updates[] = " RENAME TABLE `ab_mailing_contacts`  TO `ml_mailing_contacts` ;";
$updates[] = " RENAME TABLE `ab_mailing_users`  TO `ml_mailing_users` ;";
$updates[] = " RENAME TABLE `ab_templates`  TO `ml_templates` ;";

$updates[] = "script:1.inc.php"; 


$updates[] = "ALTER TABLE `ab_companies` CHANGE `phone` `phone` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL";  
$updates[] = "ALTER TABLE `ab_companies` CHANGE `fax` `fax` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL";  

$updates[] = "ALTER TABLE `ab_contacts` CHANGE `home_phone` `home_phone` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL"; 
$updates[] = "ALTER TABLE `ab_contacts` CHANGE `work_phone` `work_phone` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL";
$updates[] = "ALTER TABLE `ab_contacts` CHANGE `fax` `fax` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL";
$updates[] = "ALTER TABLE `ab_contacts` CHANGE `work_fax` `work_fax` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL";
$updates[] = "ALTER TABLE `ab_contacts` CHANGE `cellular` `cellular` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL";


$updates[]="ALTER TABLE `ab_contacts` ADD `files_folder_id` INT NOT NULL;";
$updates[]="ALTER TABLE `ab_companies` ADD `files_folder_id` INT NOT NULL;";

$updates[]="ALTER TABLE `ab_addressbooks` ADD `shared_acl` BOOL NOT NULL ";

$updates[] = "ALTER TABLE `ab_addressbooks` ADD `default_iso_address_format` VARCHAR( 2 ) NOT NULL ";
$updates[] = "ALTER TABLE `ab_addressbooks` ADD `default_salutation` VARCHAR( 255 ) NOT NULL ";
$updates[] = "ALTER TABLE `ab_contacts` ADD `iso_address_format` VARCHAR( 2 ) NOT NULL ";
$updates[] = "ALTER TABLE `ab_contacts` ADD `default_salutation` VARCHAR( 255 ) NOT NULL ";
$updates[] = "ALTER TABLE `ab_companies` ADD `iso_address_format` VARCHAR( 2 ) NOT NULL ";
$updates[] = "ALTER TABLE `ab_companies` ADD `post_iso_address_format` VARCHAR( 2 ) NOT NULL ";
$updates[] = "script:2_set_default_salutation.inc.php";

$updates[] = "ALTER TABLE `ab_companies` CHANGE `name` `name` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL";

$updates[] = "ALTER TABLE `ab_contacts` CHANGE `first_name` `first_name` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT ''";
$updates[] = "ALTER TABLE `ab_contacts` CHANGE `middle_name` `middle_name` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT ''";
$updates[] = "ALTER TABLE `ab_contacts` CHANGE `last_name` `last_name` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT ''";
$updates[] = "ALTER TABLE `ab_contacts` CHANGE `email` `email` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT ''";
$updates[] = "ALTER TABLE `ab_contacts` CHANGE `email2` `email2` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT ''";
$updates[] = "ALTER TABLE `ab_contacts` CHANGE `email3` `email3` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT ''";

$updates[] = "ALTER TABLE `ab_contacts` DROP INDEX `link_id_2` ";


$updates[] = "ALTER TABLE `ab_contacts` CHANGE `first_name` `first_name` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''";
$updates[] = "ALTER TABLE `ab_contacts` CHANGE `middle_name` `middle_name` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''";
$updates[] = "ALTER TABLE `ab_contacts` CHANGE `last_name` `last_name` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''";
$updates[] = "ALTER TABLE `ab_contacts` CHANGE `email` `email` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''";
$updates[] = "ALTER TABLE `ab_contacts` CHANGE `email2` `email2` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''";
$updates[] = "ALTER TABLE `ab_contacts` CHANGE `email3` `email3` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''";
$updates[] = "script:3_convert_acl.inc.php";

$updates[] = "ALTER TABLE `ab_contacts` CHANGE `address_no` `address_no` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL";
$updates[] = "ALTER TABLE `ab_companies` CHANGE `address_no` `address_no` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ";
$updates[] = "ALTER TABLE `ab_companies` CHANGE `post_address_no` `post_address_no` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ";

$updates[] = "ALTER TABLE `ab_companies` ADD INDEX ( `email` )";
$updates[] = "ALTER TABLE `ab_contacts` ADD INDEX ( `email` )";
$updates[] = "ALTER TABLE `ab_contacts` ADD INDEX ( `email2` )";
$updates[] = "ALTER TABLE `ab_contacts` ADD INDEX ( `email3` )";
$updates[] = "CREATE TABLE IF NOT EXISTS `ab_sql` (
  `id` int(11) NOT NULL default '0',
	`name` varchar(32) default NULL,
  `sql` varchar(255) default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$updates[] = "ALTER TABLE `ab_sql` CHANGE `sql` `sql` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci ";
$updates[] = "ALTER TABLE `ab_sql` ADD `companies` BOOL NOT NULL AFTER `id`";
$updates[] = "ALTER TABLE `ab_sql` ADD INDEX ( `companies` ) ";
$updates[] = "ALTER TABLE `ab_sql` ADD `user_id` INT NOT NULL AFTER `id` , ADD INDEX ( `user_id` )";

$updates[]="ALTER TABLE `ab_contacts` CHANGE `title` `title` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ";

$updates[] = "ALTER TABLE  `ab_companies` ADD INDEX (  `name` )";
$updates[] = "ALTER TABLE  `ab_contacts` ADD INDEX (  `last_name` )";

$updates[] = "ALTER TABLE `ab_contacts` CHANGE `comment` `comment` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ";

$updates[]= "ALTER TABLE `ab_companies` ADD `crn` VARCHAR( 50 ) NOT NULL ,ADD `iban` VARCHAR( 100 ) NOT NULL ";
$updates[]= "ALTER TABLE `ab_contacts` ADD INDEX ( `company_id` ) ";

$updates[] = "ALTER TABLE `ab_contacts` CHANGE `email` `email` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''";
$updates[] = "ALTER TABLE `ab_contacts` CHANGE `email2` `email2` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''";
$updates[] = "ALTER TABLE `ab_contacts` CHANGE `email3` `email3` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''";
$updates[] = "ALTER TABLE `ab_companies` ADD `name2` VARCHAR( 100 ) NULL AFTER `name` ";
//was missing from instal file
$updates[]= "ALTER TABLE `ab_companies` ADD `crn` VARCHAR( 50 ) NOT NULL ,ADD `iban` VARCHAR( 100 ) NOT NULL ";

$updates[]="DELETE FROM go_links_2 WHERE link_id=0 OR id=0;";
$updates[]="DELETE FROM go_links_3 WHERE link_id=0 OR id=0;";