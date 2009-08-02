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
