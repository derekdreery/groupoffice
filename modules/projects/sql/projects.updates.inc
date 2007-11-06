<?php
$updates[22] = array();

$updates[21][] = "ALTER TABLE `pmProjects` ADD `calendar_id` INT NOT NULL ;";
$updates[21][] = "ALTER TABLE `pmHours` ADD `event_id` INT NOT NULL ;";

$updates[20][] = "ALTER TABLE `pmHours` ADD `units` DOUBLE NOT NULL AFTER `break_time` ;";
$updates[20][] = "ALTER TABLE `pmProjects` DROP `book_units` ";
$updates[19][] = "ALTER TABLE `pmProjects` ADD `acl_book` INT NOT NULL AFTER `acl_write` ;";


$updates[18][] = "ALTER TABLE `pmProjects` ADD `customer` VARCHAR( 50 ) NOT NULL AFTER `name` ;";

$updates[17][] ="ALTER TABLE `pmHours`
  DROP `fee_value`,
  DROP `fee_time`;";
$updates[17][] ="ALTER TABLE `pmProjects` ADD `billed` DOUBLE NOT NULL ;";
$updates[17][] ="ALTER TABLE `pmProjects` CHANGE `budget` `budget` DOUBLE NOT NULL DEFAULT '0'";
$updates[17][] ="ALTER TABLE `pmFees` CHANGE `value` `external_value` DOUBLE NOT NULL DEFAULT '0'";
$updates[17][] ="ALTER TABLE `pmFees` ADD `internal_value` DOUBLE NOT NULL AFTER `external_value` ;";
$updates[17][] ="ALTER TABLE `pmProjects` ADD `book_units` enum('0', '1') NOT NULL ;";
$updates[17][] ="ALTER TABLE `pmProjects` ADD `unit_value` INT NOT NULL ;";

$updates[17][] ="ALTER TABLE `pm_settings` DROP `registration_method` ,
DROP `unit_value` ,
DROP `int_fee_value` ,
DROP `int_fee_time` ,
DROP `ext_fee_value` ,
DROP `ext_fee_time` ;";

$updates[17][] ="ALTER TABLE `pm_settings` ADD `fee_id` INT NOT NULL ;";
$updates[17][] ="ALTER TABLE `pmHours` CHANGE `ext_fee_time` `fee_time` INT( 11 ) NOT NULL DEFAULT '0';";
$updates[17][] ="ALTER TABLE `pmHours` DROP `int_fee_time`;";

$updates[17][] ="ALTER TABLE `pmHours` DROP `unit_value` ";

$updates[16][] ="ALTER TABLE `pmFees` DROP INDEX `id`";
$updates[16][] ="ALTER TABLE `pmHours` DROP INDEX `id` ";
$updates[16][] ="ALTER TABLE `pmHours` ADD `fee_value` DOUBLE NOT NULL ,
ADD `fee_time` INT NOT NULL ;";
$updates[16][] ="ALTER TABLE `pmFees` CHANGE `value` `value` DOUBLE NOT NULL DEFAULT '0'";
$updates[16][] ="ALTER TABLE `pmFees` ADD `acl_id` INT NOT NULL ;";
$updates[16][] ="ALTER TABLE `pm_settings` DROP `fee_id` ";
$updates[16][] ="ALTER TABLE `pm_settings` ADD `int_fee_value` DOUBLE NOT NULL ,
ADD `int_fee_time` INT NOT NULL ,
ADD `ext_fee_value` DOUBLE NOT NULL ,
ADD `ext_fee_time` INT NOT NULL ;";
$updates[16][] ="ALTER TABLE `pmHours` CHANGE `fee_value` `ext_fee_value` DOUBLE NOT NULL DEFAULT '0';";
$updates[16][] ="ALTER TABLE `pmHours` ADD `int_fee_value` DOUBLE NOT NULL , ADD `int_fee_time` INT NOT NULL , ADD `ext_fee_time` INT NOT NULL ;";

$updates[15][] ="ALTER TABLE `pmProjects` ADD `link_id` INT AFTER `id` ;";
$updates[15][] ="ALTER TABLE `pmProjects` ADD INDEX ( `link_id` ) ;";

$updates[13][] ="ALTER TABLE `pmProjects` DROP INDEX `id`";
$updates[13][] ="CREATE TABLE `pm_template_events` (
  `id` int(11) NOT NULL default '0',
  `template_id` int(11) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `description` text NOT NULL,
  `time_offset` int(11) NOT NULL default '0',
  `duration` int(11) NOT NULL default '0',
  `todo` enum('0','1') NOT NULL default '0',
  `reminder` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `template_id` (`template_id`)
) TYPE=MyISAM;";

$updates[13][] ="CREATE TABLE `pm_templates` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `acl_read` int(11) NOT NULL default '0',
  `acl_write` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;";



$updates[12][] ="ALTER TABLE `pm_settings` DROP `sort_field` ,
DROP `sort_order` ";

$updates[12][] ="CREATE TABLE `pmStatuses` (
`id` INT NOT NULL ,
`name` VARCHAR( 50 ) NOT NULL ,
PRIMARY KEY ( `id` )
) TYPE = MYISAM ;";

$updates[12][] ="INSERT INTO `pmStatuses` VALUES (1, 'Offer');";
$updates[12][] ="INSERT INTO `pmStatuses` VALUES (2, 'Ongoing');";
$updates[12][] ="INSERT INTO `pmStatuses` VALUES (3, 'Waiting');";
$updates[12][] ="INSERT INTO `pmStatuses` VALUES (4, 'Done');";
$updates[12][] ="INSERT INTO `pmStatuses` VALUES (5, 'Billed');";

$updates[11][] ="ALTER TABLE `pm_settings` ADD `fee_id` INT NOT NULL;";
$updates[11][] ="ALTER TABLE `pmHours` ADD `fee_id` INT NOT NULL;";
$updates[11][] ="ALTER TABLE `pm_settings` ADD `registration_method` ENUM( 'endtime', 'units' ) NOT NULL ,
ADD `unit_value` INT NOT NULL;";
$updates[10][] = "ALTER TABLE `pmProjects` ADD `company_id` INT NOT NULL AFTER `contact_id` ,
ADD `project_id` INT NOT NULL AFTER `company_id`;";
