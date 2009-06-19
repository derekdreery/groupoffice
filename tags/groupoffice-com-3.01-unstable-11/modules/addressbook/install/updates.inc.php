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