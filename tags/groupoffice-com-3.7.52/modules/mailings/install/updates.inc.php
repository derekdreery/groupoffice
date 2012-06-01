<?php
$updates[]="ALTER TABLE `ml_mailings` ADD `default_salutation` VARCHAR( 50 ) NOT NULL ;";

$updates[]="ALTER TABLE `ml_mailing_contacts` DROP INDEX `group_id`";
$updates[]="ALTER TABLE `ml_mailing_companies` DROP INDEX `group_id`";  
$updates[]="ALTER TABLE `ml_mailing_users` DROP INDEX `group_id`";

$updates[]="ALTER TABLE `ml_mailing_contacts` ADD PRIMARY KEY(`group_id`, `contact_id`)";
$updates[]="ALTER TABLE `ml_mailing_companies` ADD PRIMARY KEY(`group_id`, `company_id`)";
$updates[]="ALTER TABLE `ml_mailing_users` ADD PRIMARY KEY(`group_id`, `user_id`)";

$updates[]="ALTER TABLE `ml_mailings` DROP `default_salutation`;";
$updates[]="ALTER TABLE `ml_mailing_groups` ADD `default_salutation` VARCHAR( 50 ) NOT NULL ;";  


$updates[]="ALTER TABLE `ml_mailings` CHANGE `total` `total` INT( 11 ) NULL";
$updates[]="ALTER TABLE `ml_mailings` CHANGE `errors` `errors` INT( 11 ) NULL";  
$updates[]="ALTER TABLE `ml_mailings` CHANGE `sent` `sent` INT( 11 ) NULL";

//sometimes it's not there
$updates[]="ALTER TABLE `ml_mailing_groups` ADD `default_salutation` VARCHAR( 50 ) NOT NULL ;";  

$updates[]="CREATE TABLE IF NOT EXISTS `ml_default_templates` (
  `user_id` int(11) NOT NULL,
  `template_id` int(11) NOT NULL,
  PRIMARY KEY  (`user_id`),
  KEY `template_id` (`template_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$updates[]="ALTER TABLE `ml_mailing_groups` CHANGE `name` `name` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL";

$updates[]="ALTER TABLE `ml_mailings` ADD `alias_id` INT NOT NULL AFTER `account_id`";
$updates[]="ALTER TABLE `ml_templates` CHANGE `name` `name` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL";


$updates[]="CREATE TABLE IF NOT EXISTS `ml_sendmailing_companies` (
  `mailing_id` int(11) NOT NULL DEFAULT '0',
  `company_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`mailing_id`,`company_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$updates[]="CREATE TABLE IF NOT EXISTS `ml_sendmailing_contacts` (
  `mailing_id` int(11) NOT NULL DEFAULT '0',
  `contact_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`mailing_id`,`contact_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$updates[]="CREATE TABLE IF NOT EXISTS `ml_sendmailing_users` (
  `mailing_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`mailing_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$updates[]="ALTER TABLE `ml_mailing_companies` DROP `mail_sent` ,
DROP `status` ;";

$updates[]="ALTER TABLE `ml_mailing_contacts` DROP `mail_sent` ,
DROP `status` ;";

$updates[]="ALTER TABLE `ml_mailing_users` DROP `mail_sent` ,
DROP `status` ;";

$updates[]="script:1_convert_acl.inc.php";

$updates[]="ALTER TABLE `ml_templates` ADD `extension` VARCHAR( 4 ) NOT NULL";
$updates[]="UPDATE ml_templates SET extension = 'odt' WHERE TYPE = '1'";