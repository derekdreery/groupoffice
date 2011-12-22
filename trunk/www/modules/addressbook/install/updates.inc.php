<?php
$updates["201108131011"][]="ALTER TABLE `ab_companies` CHANGE `id` `id` INT( 11 ) NOT NULL AUTO_INCREMENT";
$updates["201108131011"][]="ALTER TABLE `ab_contacts` CHANGE `id` `id` INT( 11 ) NOT NULL AUTO_INCREMENT";
$updates["201108131011"][]="ALTER TABLE `ab_contacts` ADD `go_user_id` INT NOT NULL , ADD INDEX ( `go_user_id` )";
$updates["201108131011"][]="ALTER TABLE `ab_addressbooks` DROP `acl_write`";
$updates["201108131011"][]="ALTER TABLE `ab_addressbooks` CHANGE `id` `id` INT( 11 ) NOT NULL AUTO_INCREMENT";
$updates["201108131011"][]="ALTER TABLE `ab_addressbooks` ADD `files_folder_id` INT NOT NULL";
$updates["201108131011"][]="ALTER TABLE `ab_addressbooks` ADD `users` BOOLEAN NOT NULL ";
$updates["201108131011"][]="ALTER TABLE `ab_contacts` DROP `source_id`"; 
$updates["201108131011"][]="ALTER TABLE `ab_contacts` DROP `link_id` ";
$updates["201108131011"][]="ALTER TABLE `ab_contacts` CHANGE `email2` `email2` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''";
$updates["201108131011"][]="ALTER TABLE `ab_contacts` CHANGE `email3` `email3` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''";
$updates["201108131011"][]="ALTER TABLE `ab_contacts` CHANGE `email` `email` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''";
$updates["201108131011"][]="ALTER TABLE `ab_contacts` CHANGE `email_allowed` `email_allowed` ENUM( '0', '1' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '1'";
$updates["201108131011"][]="ALTER TABLE `ab_contacts` DROP `color`"; 
$updates["201108131011"][]="ALTER TABLE `ab_contacts` DROP `sid`"; 


$updates["201109011450"][]="RENAME TABLE `go_links_2` TO `go_links_ab_contacts`;";
$updates["201109011450"][]="ALTER TABLE `go_links_ab_contacts` CHANGE `link_id` `model_id` INT( 11 ) NOT NULL";
$updates["201109011450"][]="ALTER TABLE `go_links_ab_contacts` CHANGE `link_type` `model_type_id` INT( 11 ) NOT NULL";


$updates["201109011450"][]="RENAME TABLE `go_links_3` TO `go_links_ab_companies`;";
$updates["201109011450"][]="ALTER TABLE `go_links_ab_companies` CHANGE `link_id` `model_id` INT( 11 ) NOT NULL";
$updates["201109011450"][]="ALTER TABLE `go_links_ab_companies` CHANGE `link_type` `model_type_id` INT( 11 ) NOT NULL";


$updates["201109011450"][]="ALTER TABLE `cf_2` CHANGE `link_id` `model_id` INT( 11 ) NOT NULL DEFAULT '0'";
$updates["201109011450"][]="RENAME TABLE `cf_2` TO `cf_ab_contacts` ;";

$updates["201109011450"][]="ALTER TABLE `cf_3` CHANGE `link_id` `model_id` INT( 11 ) NOT NULL DEFAULT '0'";
$updates["201109011450"][]="RENAME TABLE `cf_3` TO `cf_ab_companies` ;";


$updates["201109021000"][]="ALTER TABLE `ab_contacts` DROP `iso_address_format` ";

$updates["201108301656"][]="INSERT INTO `go_model_types` (
`id` ,
`model_name`
)
VALUES (
'2', 'GO_Addressbook_Model_Contact'
);";
$updates["201108301656"][]="INSERT INTO `go_model_types` (
`id` ,
`model_name`
)
VALUES (
'3', 'GO_Addressbook_Model_Company'
);";


$updates["201110031344"][]="ALTER TABLE `ab_companies` DROP `iso_address_format`";
$updates["201110031344"][]="ALTER TABLE `ab_companies` DROP `post_iso_address_format` ";
$updates["201110031344"][]="ALTER TABLE `ab_companies` CHANGE `files_folder_id` `files_folder_id` INT( 11 ) NOT NULL DEFAULT '0'";
$updates["201110031344"][]="ALTER TABLE `ab_addressbooks` CHANGE `files_folder_id` `files_folder_id` INT( 11 ) NOT NULL DEFAULT '0'";
$updates["201110031344"][]="ALTER TABLE `ab_contacts` CHANGE `files_folder_id` `files_folder_id` INT( 11 ) NOT NULL DEFAULT '0'";
$updates["201110031344"][]="ALTER TABLE `ab_contacts` CHANGE `go_user_id` `go_user_id` INT( 11 ) NOT NULL DEFAULT '0'";
$updates["201110031344"][]="ALTER TABLE `ab_contacts` DROP `link_id` ";
$updates["201110031344"][]="ALTER TABLE `ab_contacts` CHANGE `email_allowed` `email_allowed` BOOLEAN NOT NULL DEFAULT '1'";
$updates["201110031344"][]="ALTER TABLE `ab_contacts` DROP `sid` ";
$updates["201110031344"][]="ALTER TABLE `ab_contacts` DROP `color` ";
$updates["201110031344"][]="ALTER TABLE `ab_contacts` DROP `source_id`";


$updates["201110141221"][]="UPDATE ab_contacts SET email_allowed=0 where email_allowed=1";
$updates["201110141221"][]="UPDATE ab_contacts SET email_allowed=1 where email_allowed=2";

$updates["201110170846"][]="ALTER TABLE `ab_addressbooks` DROP `default_iso_address_format`";

$updates["201110281132"][]="ALTER TABLE `ab_addressbooks` CHANGE `users` `users` TINYINT( 1 ) NOT NULL DEFAULT '0'";
$updates["201110281132"][]="update `ab_contacts` set birthday=null where birthday='0000-00-00'";
$updates["201110281132"][]="ALTER TABLE `ab_companies` CHANGE `phone` `phone` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL";
$updates["201110281132"][]="ALTER TABLE `ab_companies` CHANGE `fax` `fax` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL ";

$updates["201110281132"][]="ALTER TABLE `ab_companies` CHANGE `email_allowed` `email_allowed` BOOLEAN NOT NULL DEFAULT '1'";
$updates["201110281132"][]="UPDATE ab_companies SET email_allowed=0 where email_allowed=1";
$updates["201110281132"][]="UPDATE ab_companies SET email_allowed=1 where email_allowed=2";

$updates["201110281132"][]="ALTER TABLE `ab_companies` CHANGE `crn` `crn` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL ";
$updates["201110281132"][]="ALTER TABLE `ab_companies` CHANGE `iban` `iban` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL ";
$updates["201110281132"][]="ALTER TABLE `ab_contacts` DROP `default_salutation`";

$updates["201111141132"][]="RENAME TABLE `ml_default_templates` TO `ab_default_email_templates` ;";

$updates["201111141132"][]="CREATE TABLE IF NOT EXISTS `ab_default_email_templates` (
  `user_id` int(11) NOT NULL,
  `template_id` int(11) NOT NULL,
  PRIMARY KEY  (`user_id`),
  KEY `template_id` (`template_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;"; // Added because with an existing installation with the addressbook this table was not created (Wesley).

$updates["201111141037"][]="ALTER TABLE `ab_contacts` CHANGE `comment` `comment` TEXT NOT NULL DEFAULT '';";
$updates["201111141037"][]="ALTER TABLE `ab_companies` CHANGE `comment` `comment` TEXT NOT NULL DEFAULT '';";

$updates["201111180945"][]="DROP TABLE IF EXISTS `cf_addressbook_limits`;";

$updates["201111180945"][]="DROP TABLE IF EXISTS `cf_companies_cf_categories`;";
$updates["201111180945"][]="DROP TABLE IF EXISTS `cf_contacts_cf_categories`;";

$updates["201111180945"][]="ALTER TABLE `ab_contacts` CHANGE `comment` `comment` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL ";
$updates["201111180945"][]="ALTER TABLE `ab_companies` CHANGE `comment` `comment` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL ";

$updates["201111211405"][]="RENAME TABLE `ml_sendmailing_contacts` TO `ab_sendmailing_contacts`;";
$updates["201111211405"][]="RENAME TABLE `ml_sendmailing_companies` TO `ab_sendmailing_companies`;";
$updates["201111211405"][]="ALTER TABLE `ab_sendmailing_contacts` CHANGE `mailing_id` `addresslist_id` INT(11) NOT NULL DEFAULT '0' ";

$updates["201111211405"][]="ALTER TABLE `ab_sendmailing_companies` CHANGE `mailing_id` `addresslist_id` INT(11) NOT NULL DEFAULT '0' ";

$updates["201111211405"][]="ALTER TABLE `ab_sendmailing_companies` CHANGE `mailing_id` `addresslist_id` INT(11) NOT NULL DEFAULT '0' ";

$updates["201111211405"][]="CREATE TABLE IF NOT EXISTS `ab_email_templates` (
  `id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `type` tinyint(4) NOT NULL DEFAULT '0',
  `name` varchar(100) DEFAULT NULL,
  `acl_id` int(11) NOT NULL DEFAULT '0',
  `content` longblob NOT NULL,
  `extension` varchar(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";


$updates["201111211405"][]="CREATE TABLE IF NOT EXISTS `ab_sent_mailings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `subject` varchar(100) DEFAULT NULL,
  `message_path` varchar(255) DEFAULT NULL,
  `ctime` int(11) NOT NULL,
  `addresslist_id` int(11) NOT NULL,
  `alias_id` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `total` int(11) DEFAULT NULL,
  `sent` int(11) DEFAULT NULL,
  `errors` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";


$updates["201111211405"][]="CREATE TABLE IF NOT EXISTS `ab_default_email_templates` (
  `user_id` int(11) NOT NULL,
  `template_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`),
  KEY `addresslist_id` (`template_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$updates["201111211405"][]="CREATE TABLE IF NOT EXISTS `ml_sendmailing_companies` (
  `addresslist_id` int(11) NOT NULL DEFAULT '0',
  `company_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`addresslist_id`,`company_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$updates["201111211405"][]="CREATE TABLE IF NOT EXISTS `ab_sendmailing_contacts` (
  `addresslist_id` int(11) NOT NULL DEFAULT '0',
  `contact_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`addresslist_id`,`contact_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$updates["201111211715"][]="ALTER TABLE `ab_sent_mailings` CHANGE `status` `status` tinyint(4) DEFAULT '0' ";
$updates["201111211715"][]="ALTER TABLE `ab_sent_mailings` CHANGE `total` `total` tinyint(4) DEFAULT '0' ";
$updates["201111211715"][]="ALTER TABLE `ab_sent_mailings` CHANGE `sent` `sent` tinyint(4) DEFAULT '0' ";
$updates["201111211715"][]="ALTER TABLE `ab_sent_mailings` CHANGE `errors` `errors` tinyint(4) DEFAULT '0' ";

$updates["201111221545"][]="RENAME TABLE `ab_sql` to `ab_search_queries` ";
$updates["201111221610"][]="ALTER TABLE `ab_search_queries` CHANGE `id` `id` INT( 11 ) NOT NULL AUTO_INCREMENT ";

$updates["201112011557"][]="ALTER TABLE `ab_email_templates` CHANGE `extension` `extension` varchar(4) NOT NULL DEFAULT '';";
$updates["201112011632"][]="ALTER TABLE `ab_email_templates` CHANGE `id` `id` int(11) NOT NULL AUTO_INCREMENT;";
$updates["201112011632"][]="ALTER TABLE `ab_sent_mailings` CHANGE `id` `id` int(11) NOT NULL AUTO_INCREMENT;";
$updates["201112011632"][]="ALTER TABLE `ab_search_queries` CHANGE `id` `id` int(11) NOT NULL AUTO_INCREMENT;";
$updates["201112021640"][]="ALTER TABLE `ab_contacts` CHANGE `email` `email` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''";

$updates["201112051545"][]="ALTER TABLE `ab_contacts` ADD `aftername_title` varchar(50) NOT NULL DEFAULT '';";

$updates["201112091545"][]="ALTER TABLE `ab_contacts` CHANGE `home_phone` `home_phone` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''";
$updates["201112091545"][]="ALTER TABLE `ab_contacts` CHANGE `work_phone` `work_phone` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''";
$updates["201112091545"][]="ALTER TABLE `ab_contacts` CHANGE `fax` `fax` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''";
$updates["201112091545"][]="ALTER TABLE `ab_contacts` CHANGE `work_fax` `work_fax` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''";
$updates["201112091545"][]="ALTER TABLE `ab_contacts` CHANGE `cellular` `cellular` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''";

$updates["201112121545"][]="ALTER TABLE `ab_contacts` CHANGE `aftername_title` `suffix` varchar(50) NOT NULL DEFAULT '';";

$updates["201112141253"][]="ALTER TABLE `ab_sendmailing_companies` CHANGE `addresslist_id` `sent_mailing_id` INT( 11 ) NOT NULL DEFAULT '0'";
$updates["201112141253"][]="RENAME TABLE `ab_sendmailing_companies` TO `ab_sent_mailing_companies` ;";

$updates["201112141253"][]="ALTER TABLE `ab_sendmailing_contacts` CHANGE `addresslist_id` `sent_mailing_id` INT( 11 ) NOT NULL DEFAULT '0'";
$updates["201112141253"][]="RENAME TABLE `ab_sendmailing_contacts` TO `ab_sent_mailing_contacts` ;";

$updates["201112221547"][]="CREATE TABLE IF NOT EXISTS `ab_addresslist_companies` (
  `addresslist_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`addresslist_id`,`company_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";


$updates["201112221547"][]="CREATE TABLE IF NOT EXISTS `ab_addresslist_contacts` (
  `addresslist_id` int(11) NOT NULL,
  `contact_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`addresslist_id`,`contact_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";


$updates["201112221547"][]="CREATE TABLE IF NOT EXISTS `cf_ab_contacts` (
  `model_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`model_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$updates["201112221547"][]="CREATE TABLE IF NOT EXISTS `cf_ab_companies` (
  `model_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`model_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
