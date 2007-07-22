<?php
$updates[26] = array();

$updates[25][]="ALTER TABLE `ab_companies` CHANGE `bank_no` `bank_no` VARCHAR( 50 ) NOT NULL"; 

$updates[24][]="UPDATE ab_contacts SET birthday = '' WHERE birthday = '1999-11-30';";
$updates[24][]="UPDATE ab_companies SET mtime=NOW() where mtime=0;";
$updates[24][]="UPDATE ab_companies SET ctime=mtime where ctime=0;";

$updates[24][]="UPDATE ab_contacts SET mtime=NOW() where mtime=0;";
$updates[24][]="UPDATE ab_contacts SET ctime=mtime where ctime=0;";

$updates[21][] ="update ab_contacts set birthday='0000-00-00' WHERE birthday='1970-01-01';";

$updates[20][] ="ALTER TABLE `ab_companies` ADD `link_id` INT AFTER `id` ;";
$updates[20][] ="ALTER TABLE `ab_companies` ADD INDEX ( `link_id` ) ;";
$updates[20][] ="ALTER TABLE `ab_contacts` ADD `link_id` INT AFTER `id` ;";
$updates[20][] ="ALTER TABLE `ab_contacts` ADD INDEX ( `link_id` ) ;";

$updates[19][] ="ALTER TABLE `ab_contacts` DROP INDEX `id`;";
$updates[19][] ="ALTER TABLE `ab_contacts` ADD `email2` VARCHAR( 100 ) NOT NULL AFTER `email` ,
ADD `email3` VARCHAR( 100 ) NOT NULL AFTER `email2`";

$updates[18][] ="CREATE TABLE `tp_templates_content` (
  `id` int(11) NOT NULL default '0',
  `content` blob NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;";
$updates[18][] ="INSERT INTO `tp_templates_content`
SELECT id, content
FROM `tp_templates` ;";
$updates[18][] ="ALTER TABLE `tp_templates` DROP `content`";



$updates[17][] ="CREATE TABLE `tp_mailing_users` (
  `group_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  KEY `group_id` (`group_id`)
) TYPE=MyISAM;";
$updates[16][] ="ALTER TABLE `ab_settings` ADD `addressbook_id` INT NOT NULL"; 

$updates[16][] ="ALTER TABLE `ab_settings` DROP `sort_contacts_field` ,
DROP `sort_contacts_order` ,
DROP `sort_companies_field` ,
DROP `sort_companies_order` ,
DROP `sort_users_field` ,
DROP `sort_users_order` ";

$updates[15][] = "ALTER TABLE `ab_companies` ADD `ctime` INT NOT NULL ,
	ADD `mtime` INT NOT NULL"; 

$updates[14][] = "UPDATE ab_settings SET search_type='contact'";

$updates[13][] = "ALTER TABLE `ab_contacts` ADD `ctime` int( 11 ) NOT NULL AFTER `color`";
$updates[13][] = "ALTER TABLE `ab_contacts` ADD `mtime` int( 11 ) NOT NULL AFTER `color`";

$updates[12][] = "ALTER TABLE `ab_zipcodes` DROP `range_start`";
$updates[12][] = "ALTER TABLE `ab_zipcodes` DROP `range_end`";
$updates[12][] = "ALTER TABLE `ab_zipcodes` ADD `state` VARCHAR( 100 ) NOT NULL AFTER `city`";
$updates[12][] = "ALTER TABLE `ab_zipcodes` ADD `country` VARCHAR( 100 ) NOT NULL AFTER `city`";
$updates[12][] = "ALTER TABLE `ab_zipcodes` ADD INDEX ( `zip` )";

$updates[11][] = "ALTER TABLE `ab_companies` DROP `acl_read` ,DROP `acl_write`";
$updates[11][] = "ALTER TABLE `ab_contacts` DROP `acl_read` , DROP `acl_write`";

$updates[10][] = "ALTER TABLE `ab_companies` ADD `address_no` VARCHAR( 10 ) NOT NULL AFTER `address`;";
$updates[10][] = "ALTER TABLE `ab_contacts` ADD `address_no` VARCHAR( 10 ) NOT NULL AFTER `address`;";
$updates[10][] = "CREATE TABLE `ab_zipcodes` (
  `id` int(11) NOT NULL default '0',
  `zip` varchar(10) NOT NULL default '',
  `range_start` int(11) NOT NULL default '0',
  `range_end` int(11) NOT NULL default '0',
  `city` varchar(100) NOT NULL default '',
  `street` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;";
$updates[10][] = "ALTER TABLE `ab_companies` ADD `post_address` VARCHAR( 100 ) NOT NULL AFTER `country` ,".
		"ADD `post_address_no` VARCHAR( 10 ) NOT NULL AFTER `post_address` ,".
		"ADD `post_city` VARCHAR( 50 ) NOT NULL AFTER `post_address_no` ,".
		"ADD `post_state` VARCHAR( 50 ) NOT NULL AFTER `post_city` ,".
		"ADD `post_country` VARCHAR( 50 ) NOT NULL AFTER `post_state` ,".
		"ADD `post_zip` VARCHAR( 10 ) NOT NULL AFTER `post_country`;";
