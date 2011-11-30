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
$updates[]= "DELETE FROM go_state WHERE name =  'calendar-state';";

$updates[]= "ALTER TABLE `cal_calendars` ADD `comment` VARCHAR( 255 ) NOT NULL";


$updates[]="CREATE TABLE IF NOT EXISTS `cal_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `color` char(6) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$updates[]="ALTER TABLE `cal_events` ADD `category_id` INT NOT NULL;";
$updates[]="ALTER TABLE `cal_events` ADD INDEX ( `category_id` )";


$updates[]="ALTER TABLE  `cal_events` ADD  `uuid` VARCHAR( 100 ) NOT NULL AFTER  `id` , ADD INDEX (  `uuid` )";

$updates[]="ALTER TABLE `cal_participants` ADD `last_modified` VARCHAR(20) NOT NULL";
$updates[]="ALTER TABLE `cal_participants` ADD `is_organizer` TINYINT(1) NOT NULL default '0'";

$updates[]="ALTER TABLE `cal_events` ADD `sequence` INT NOT NULL default '0'";

$updates[]="ALTER TABLE `cal_participants` CHANGE `status` `status` enum('0','1','2','3') NOT NULL default '0'";

$updates[]="ALTER TABLE `cal_events` ADD `uid` VARCHAR(255) NOT NULL";

$updates[]="ALTER TABLE `cal_participants` ADD `role` VARCHAR(100) NOT NULL";

$updates[]="CREATE TABLE IF NOT EXISTS `cal_events_declined` (
  `uid` varchar(200) NOT NULL,
  `email` varchar(100) NOT NULL,
  PRIMARY KEY (`uid`,`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$updates[]="ALTER TABLE `cal_events_declined` CHANGE `uid` `uid` VARCHAR(200) NOT NULL";

$updates[]="ALTER TABLE `cal_calendars` ADD `project_id` INT NOT NULL ,
ADD INDEX ( `project_id` ) ";

$updates[]="ALTER TABLE `cal_events` CHANGE `uuid` `uuid` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL";

$updates[]="ALTER TABLE `cal_calendars` ADD `tasklist_id` INT NOT NULL";
$updates[]="update cal_calendars set tasklist_id=(SELECT default_tasklist_id  FROM ta_settings WHERE user_id=cal_calendars.user_id);";

$updates[]="ALTER TABLE `cal_events` CHANGE `uid` `invitation_uuid` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL";
$updates[]="ALTER TABLE `cal_events` ADD INDEX ( `invitation_uuid` )";

$updates[]="ALTER TABLE `cal_events` DROP `event_id`";
$updates[]="ALTER TABLE `cal_events` CHANGE `participants_event_id` `resource_event_id` INT( 11 ) NOT NULL ";
$updates[]="ALTER TABLE `cal_events` DROP `invitation_uuid`";

$updates[]="ALTER TABLE `cal_events` CHANGE `uuid` `uuid` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL";
$updates[]="script:4_add_calendar_name_template.inc.php";

$updates[]="ALTER TABLE  `cal_calendars` ADD  `public` BOOL NOT NULL ;";

$updates[]="ALTER TABLE  `cal_groups` ADD `show_not_as_busy` BOOL NOT NULL ;";

$updates[]="ALTER TABLE `cal_events` ADD `exception_for_event_id` INT NOT NULL";
$updates[]="ALTER TABLE `cal_events` ADD INDEX ( `exception_for_event_id` ) ";

$updates[]="CREATE TABLE IF NOT EXISTS `cal_calendar_user_colors` (
  `user_id` int(11) NOT NULL,
  `calendar_id` int(11) NOT NULL,
  `color` varchar(6) NOT NULL,
  PRIMARY KEY (`user_id`,`calendar_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$updates[]="CREATE TABLE IF NOT EXISTS `cal_calendar_user_colors` (
  `user_id` int(11) NOT NULL,
  `calendar_id` int(11) NOT NULL,
  `color` varchar(6) NOT NULL,
  PRIMARY KEY (`user_id`,`calendar_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$updates[]="CREATE TABLE IF NOT EXISTS `cal_calendar_user_colors` (
  `user_id` int(11) NOT NULL,
  `calendar_id` int(11) NOT NULL,
  `color` varchar(6) NOT NULL,
  PRIMARY KEY (`user_id`,`calendar_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$updates[]="CREATE TABLE IF NOT EXISTS `cal_calendar_user_colors` (
  `user_id` int(11) NOT NULL,
  `calendar_id` int(11) NOT NULL,
  `color` varchar(6) NOT NULL,
  PRIMARY KEY (`user_id`,`calendar_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";