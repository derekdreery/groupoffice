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



$updates[]="CREATE TABLE IF NOT EXISTS `cf_1` (
  `link_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`link_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$updates[]="script:2_install_groups.inc.php";
$updates[]="UPDATE cal_calendars SET group_id=1 WHERE group_id<1";

$updates[]="ALTER TABLE `cal_calendars` ADD `show_bdays` TINYINT( 1 ) NOT NULL ";
$updates[]="script:3_convert_acl.inc.php";

$updates[]="ALTER TABLE `cal_calendars` ADD `show_tasks` TINYINT( 1 ) NOT NULL ";

$updates[]="CREATE TABLE IF NOT EXISTS `cal_group_admins` (
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`group_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8";

$updates[]="ALTER TABLE `cal_groups` DROP `acl_admin` ";

$updates[]="ALTER TABLE `cal_calendars` DROP `show_tasks`";
$updates[]="CREATE TABLE IF NOT EXISTS `cal_visible_tasklists` (
  `calendar_id` int(11) NOT NULL,
  `tasklist_id` int(11) NOT NULL,
  PRIMARY KEY (`calendar_id`,`tasklist_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8";

$updates[]= "ALTER TABLE `cal_views` ADD `merge` tinyint(1) NOT NULL default '0'";
$updates[]= "ALTER TABLE `cal_views` ADD `owncolor` tinyint(1) NOT NULL default '1'";

$updates[]= "ALTER TABLE `cal_events` ADD INDEX ( `calendar_id` )";
$updates[]= "ALTER TABLE `cal_events` ADD INDEX ( `busy` )";
$updates[]= "ALTER TABLE `cal_events` ADD COLUMN `read_only` TINYINT(1) NOT NULL default '0'";