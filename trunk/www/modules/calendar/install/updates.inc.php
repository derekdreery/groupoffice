<?php
$updates[]="ALTER TABLE `cal_events` CHANGE `rrule` `rrule` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL";
$updates[]="ALTER TABLE `cal_events` ADD `background` CHAR( 6 ) NOT NULL DEFAULT 'ebf1e2';";
$updates[]="ALTER TABLE `cal_events` ADD INDEX ( `participants_event_id` )";

$updates[]="DROP TABLE IF EXISTS `cal_settings`;";
$updates[]="CREATE TABLE `cal_settings` (
`user_id` INT NOT NULL ,
`reminder` INT NOT NULL ,
`color` CHAR( 6 ) NOT NULL ,
PRIMARY KEY ( `user_id` )
) ENGINE = MYISAM DEFAULT CHARSET=utf8;"; 

$updates[]="ALTER TABLE `cal_settings` CHANGE `color` `background` CHAR( 6 ) CHARACTER SET utf8 COLLATE utf8_general_ci";
$updates[]="ALTER TABLE `cal_events` CHANGE `rrule` `rrule` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL";

$updates[]="ALTER TABLE `cal_events` ADD `files_folder_id` INT NOT NULL;";
//$updates[]="script:1_shift_days.inc.php";

$updates[]="ALTER TABLE `cal_settings` ADD `calendar_id` INT NOT NULL;";
$updates[]="ALTER TABLE `cal_settings` ADD INDEX ( `calendar_id` )";

$updates[]="ALTER TABLE `cal_calendars` ADD `shared_acl` BOOL NOT NULL ";


$updates[]="DROP TABLE IF EXISTS `cal_groups`;";
$updates[]="CREATE TABLE IF NOT EXISTS `cal_groups` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `name` varchar(50) default NULL,
  `acl_admin` int(11) NOT NULL,
  `fields` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$updates[]="CREATE TABLE IF NOT EXISTS `cf_1` (
  `link_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`link_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";