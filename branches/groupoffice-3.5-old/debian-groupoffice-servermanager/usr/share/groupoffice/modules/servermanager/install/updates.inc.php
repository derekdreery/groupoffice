<?php
$updates[]="ALTER TABLE `sm_reports` ADD `date_format` VARCHAR( 20 ) NOT NULL ;";
$updates[]="ALTER TABLE `sm_reports` ADD `admin_country` CHAR( 2 ) NOT NULL AFTER `admin_salutation`;";
$updates[]="ALTER TABLE `sm_reports` ADD `thousands_separator` CHAR( 1 ) NOT NULL, ADD `decimal_separator` CHAR( 1 ) NOT NULL ;";
$updates[]="ALTER TABLE `sm_installations` ADD `max_users` INT NOT NULL ;";
$updates[]="ALTER TABLE `sm_installations` CHANGE `name` `name` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL";
$updates[]="ALTER TABLE `sm_reports` CHANGE `thousands_seperator` `thousands_separator` CHAR( 1 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL";
$updates[]="ALTER TABLE `sm_reports` CHANGE `decimal_seperator` `decimal_separator` CHAR( 1 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL";    
$updates[]="ALTER TABLE `sm_new_trials` CHANGE `password` `password` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL";
$updates[]="ALTER TABLE `sm_reports` ADD `billing` BOOL NOT NULL ";
$updates[]="ALTER TABLE `sm_reports` ADD `professional` BOOL NOT NULL ";
$updates[]="ALTER TABLE `sm_reports` ADD INDEX ( `ctime` )";
$updates[]="ALTER TABLE `sm_reports` ADD INDEX ( `professional` )";
$updates[]="ALTER TABLE `sm_reports` ADD INDEX ( `billing` ) ";
$updates[]="ALTER TABLE `sm_reports` ADD `max_users` INT NOT NULL ";

$updates[]="ALTER TABLE sm_installations ADD  `count_users` int( 11 ) NOT NULL ,
ADD `install_time` int( 11 ) NOT NULL ,
ADD `lastlogin` int( 11 ) NOT NULL ,
ADD `total_logins` int( 11 ) NOT NULL ,
ADD `database_usage` int( 11 ) NOT NULL ,
ADD `file_storage_usage` int( 11 ) NOT NULL ,
ADD `mailbox_usage` int( 11 ) DEFAULT NULL ,
ADD `report_ctime` int( 11 ) NOT NULL ,
ADD `comment` text,
ADD `features` varchar( 255 ) DEFAULT NULL ,
ADD `mail_domains` varchar( 255 ) DEFAULT NULL ,
ADD `admin_email` varchar( 100 ) DEFAULT NULL ,
ADD `admin_name` varchar( 100 ) DEFAULT NULL ,
ADD `admin_salutation` varchar( 100 ) DEFAULT NULL ,
ADD `admin_country` char( 2 ) NOT NULL ,
ADD `date_format` varchar( 20 ) DEFAULT NULL ,
ADD `thousands_separator` char( 1 ) NOT NULL ,
ADD `decimal_separator` char( 1 ) DEFAULT NULL ,
ADD `billing` tinyint( 1 ) NOT NULL ,
ADD `professional` tinyint( 1 ) NOT NULL ,
ADD `status` varchar( 50 ) NOT NULL ,
ADD `status_change_time` int( 11 ) NOT NULL ";

$updates[]="UPDATE sm_installations SET status='ignore'";

$updates[]="ALTER TABLE `sm_installations` ADD `config_file` VARCHAR( 255 ) NOT NULL";

