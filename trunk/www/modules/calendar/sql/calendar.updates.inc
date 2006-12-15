<?php
$updates[30]=array();

$updates[29][] = "TRUNCATE TABLE `cal_holidays` ";
$updates[29][] = "ALTER TABLE `cal_holidays` ADD INDEX ( `user_id` ) ";
$updates[29][] = "ALTER TABLE `cal_holidays` DROP calendar_id";

$updates[28][] = "UPDATE cal_calendars SET group_id=0 WHERE group_id=1;";

$updates[27][] = "UPDATE cal_events SET status_id =11 WHERE status_id <1 AND completion_time >0;";
$updates[27][] = "UPDATE cal_events SET status_id =6 WHERE status_id<1 AND completion_time=0 AND todo='1';";
$updates[27][] = "UPDATE cal_events SET status_id=1 WHERE status_id<1 AND todo='0';";

$updates[26][] = "CREATE TABLE `cal_calendar_backgrounds` (
`id` INT NOT NULL ,
`calendar_id` INT NOT NULL ,
`background_id` INT NOT NULL ,
`weekday` TINYINT NOT NULL ,
`start_time` INT NOT NULL ,
`end_time` INT NOT NULL ,
PRIMARY KEY ( `id` ) ,
INDEX ( `calendar_id` )
) TYPE = MYISAM ;";

$updates[26][] = "CREATE TABLE `cal_backgrounds` (
`id` INT NOT NULL ,
`color` CHAR (6) NOT NULL ,
`name` VARCHAR (100) NOT NULL ,
PRIMARY KEY ( `id` )
) TYPE = MYISAM ;";


$updates[25][] = "ALTER TABLE `cal_events` ADD `timezone` FLOAT NOT NULL ,
ADD `DST` ENUM( '0', '1' ) NOT NULL ;";

$updates[24][] = "ALTER TABLE `users` CHANGE `timezone` `timezone` FLOAT NOT NULL DEFAULT '0'";

$updates[23][] = "ALTER TABLE `cal_settings` ADD `email_reminders` ENUM( '0', '1' ) NOT NULL ;";

$updates[22][] = "ALTER TABLE `cal_settings` ADD `check_conflicts` ENUM( '0', '1' ) DEFAULT '1' NOT NULL ,
ADD `email_changes` ENUM( '0', '1' ) DEFAULT '1' NOT NULL ;";
$updates[22][] = "ALTER TABLE `cal_settings` DROP `sort_field` ,
DROP `sort_order` ;";

$updates[21][] = "ALTER TABLE `cal_settings` ADD `view_type` ENUM( 'grid', 'list' ) DEFAULT 'grid' NOT NULL ;";
$updates[21][] = "ALTER TABLE `cal_events` ADD `link_id` INT NOT NULL AFTER `all_day_event` ";
$updates[21][] = "ALTER TABLE `cal_events` ADD INDEX ( `link_id` ) ";

$updates[19][] = "ALTER TABLE `cal_reminders` ADD `email_sent` ENUM( '0', '1' ) NOT NULL ;";
$updates[19][] = "ALTER TABLE `cal_reminders` ADD INDEX ( `email_sent` ) ";
$updates[19][] = "ALTER TABLE `cal_settings` ADD `show_completed` ENUM( '0', '1' ) NOT NULL ;";
$updates[19][] = "ALTER TABLE `cal_views` ADD `time_interval` INT DEFAULT '1800' NOT NULL AFTER `event_colors_override` ;";
$updates[19][] = "ALTER TABLE `cal_calendars` ADD `time_interval` INT DEFAULT '1800' NOT NULL;";
$updates[19][] = "CREATE TABLE `cal_group_admins` (
  `group_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`group_id`,`user_id`)
) TYPE=MyISAM;";


$updates[19][] = "ALTER TABLE `cal_events` ADD `status_id` TINYINT NOT NULL AFTER `completion_time` ;";
$updates[19][] = "ALTER TABLE `cal_events` ADD `custom_fields` TEXT NOT NULL ;";
$updates[19][] = "ALTER TABLE `cal_events` ADD `event_id` INT NOT NULL AFTER `id` ;";
$updates[19][] = "ALTER TABLE `cal_events` ADD INDEX ( `event_id` ) ;";
$updates[19][] = "CREATE TABLE `cal_groups` (
  `id` int(11) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `custom_fields` text NOT NULL,
  `acl_write` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;";

$updates[19][] = "CREATE TABLE `cal_statuses` (
  `id` int(11) NOT NULL default '0',
  `type` varchar(20) NOT NULL default '',
  `name` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `type` (`type`)
) TYPE=MyISAM;";
$updates[19][] = "INSERT INTO `cal_statuses` VALUES (1, 'VEVENT', 'NEEDS-ACTION');";
$updates[19][] = "INSERT INTO `cal_statuses` VALUES (2, 'VEVENT', 'ACCEPTED');";
$updates[19][] = "INSERT INTO `cal_statuses` VALUES (3, 'VEVENT', 'DECLINED');";
$updates[19][] = "INSERT INTO `cal_statuses` VALUES (4, 'VEVENT', 'TENTATIVE');";
$updates[19][] = "INSERT INTO `cal_statuses` VALUES (5, 'VEVENT', 'DELEGATED');";
$updates[19][] = "INSERT INTO `cal_statuses` VALUES (6, 'VTODO', 'NEEDS-ACTION');";
$updates[19][] = "INSERT INTO `cal_statuses` VALUES (7, 'VTODO', 'ACCEPTED');";
$updates[19][] = "INSERT INTO `cal_statuses` VALUES (8, 'VTODO', 'DECLINED');";
$updates[19][] = "INSERT INTO `cal_statuses` VALUES (9, 'VTODO', 'TENTATIVE');";
$updates[19][] = "INSERT INTO `cal_statuses` VALUES (10, 'VTODO', 'DELEGATED');";
$updates[19][] = "INSERT INTO `cal_statuses` VALUES (11, 'VTODO', 'COMPLETED');";
$updates[19][] = "INSERT INTO `cal_statuses` VALUES (12, 'VTODO', 'IN-PROCESS');";
$updates[19][] = "ALTER TABLE `cal_calendars` ADD `group_id` INT DEFAULT '1' NOT NULL AFTER `id`;";
$updates[19][] = "ALTER TABLE `cal_calendars` ADD INDEX ( `group_id` )";





$updates[18][] = "ALTER TABLE `cal_views` ADD `event_colors_override` ENUM( '0', '1' ) NOT NULL AFTER `end_hour`";

$updates[17][] = "ALTER TABLE `cal_views` DROP `standard`";
$updates[17][] = "ALTER TABLE `cal_views` DROP `type`";
$updates[17][] = "ALTER TABLE `cal_views` ADD `acl_read` INT NOT NULL ,
ADD `acl_write` INT NOT NULL ;";
$updates[17][] = "ALTER TABLE `cal_settings` ADD `weekview` ENUM( '5', '7' ) DEFAULT '7' NOT NULL ;";

$updates[16][] = "ALTER TABLE `cal_views_calendars` ADD PRIMARY KEY ( `view_id` , `calendar_id` ) ";
$updates[16][] = "ALTER TABLE `cal_views_calendars` ADD `background` CHAR( 6 ) DEFAULT 'CCFFCC' NOT NULL" ;
$updates[16][] = "ALTER TABLE `cal_settings`DROP `hide_completed_todos` ,
DROP `show_todos` ;";
$updates[16][] = "ALTER TABLE `cal_reminders` ADD `occurence_time` INT NOT NULL";
$updates[16][] = "ALTER TABLE `cal_events` ADD `permissions` TINYINT NOT NULL AFTER `user_id`" ;
$updates[16][] = "ALTER TABLE `cal_settings` ADD `permissions` TINYINT NOT NULL ";
$updates[16][] = "ALTER TABLE `cal_settings` ADD `show_todos` ENUM( '0', '1' ) NOT NULL ;";
$updates[16][] = "CREATE TABLE `cal_exceptions` (
  `id` int(11) NOT NULL default '0',
  `event_id` int(11) NOT NULL default '0',
  `time` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `event_id` (`event_id`)
) TYPE=MyISAM;";

$updates[15][] = "ALTER TABLE `cal_settings` DROP `sort_field` ,
DROP `sort_order`";




$updates[14][] = "ALTER TABLE `cal_events` ADD `todo` ENUM( '0', '1' ) NOT NULL , ADD `completion_time` INT NOT NULL ;";
$updates[14][] = "ALTER TABLE `cal_events` ADD INDEX ( `todo` ) ;";

$updates[13][] = "ALTER TABLE `cal_settings` ADD `reminder` INT( 11 ) NOT NULL ,
ADD `refresh_rate` VARCHAR( 5 ) NOT NULL;";
$updates[12][] = "ALTER TABLE `cal_calendars` ADD `background` VARCHAR( 6 ) DEFAULT 'FFFFCC' NOT NULL";
$updates[11][] = "ALTER TABLE `cal_settings` ADD `sort_field` VARCHAR( 20 ) NOT NULL ,
ADD `sort_order` CHAR( 4 ) NOT NULL;";

$updates[11][] = "ALTER TABLE `cal_events` ADD `company_id` INT NOT NULL AFTER `contact_id` ,
ADD `project_id` INT NOT NULL AFTER `company_id`;";

$updates[10][] = "ALTER TABLE `cal_settings` ADD `merged_view` ENUM( '0', '1' ) NOT NULL ;";
?>
