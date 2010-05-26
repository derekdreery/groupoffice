<?php
$updates[]="RENAME TABLE `sum_announcements`  TO `su_announcements`;";
$updates[]="ALTER TABLE `su_announcements` DROP `acl_id`";
$updates[]="CREATE TABLE IF NOT EXISTS `su_announcements` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `due_time` int(11) NOT NULL default '0',
  `ctime` int(11) NOT NULL default '0',
  `mtime` int(11) NOT NULL default '0',
  `title` varchar(50) NOT NULL default '',
  `content` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `due_time` (`due_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$updates[]="DELETE FROM go_state WHERE name='summary-active-portlets';";
$updates[]="script:1_add_announcement.inc.php";

$updates[]="ALTER TABLE `su_rss_feeds` DROP PRIMARY KEY ";
$updates[]="ALTER TABLE `su_rss_feeds` ADD `id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ";
$updates[]="ALTER TABLE `su_rss_feeds` ADD `title` VARCHAR( 255 ) NOT NULL AFTER `user_id` ";
$updates[]="ALTER TABLE `su_rss_feeds` ADD `summary` BOOL NOT NULL ";
$updates[]="UPDATE `su_rss_feeds` SET `title` = 'News'";
$updates[]="UPDATE `su_rss_feeds` SET `summary` = 1";
$updates[]="script:2_add_rssfeed_title.inc.php";

$updates[]="CREATE TABLE IF NOT EXISTS `su_visible_lists` (
  `user_id` int(11) NOT NULL,
  `tasklist_id` int(11) NOT NULL,
  PRIMARY KEY  (`user_id`,`tasklist_id`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8;";
$updates[]="script:3_set_visible_list.inc.php";

$updates[]="CREATE TABLE IF NOT EXISTS `su_visible_calendars` (
  `user_id` int(11) NOT NULL,
  `calendar_id` int(11) NOT NULL,
  PRIMARY KEY  (`user_id`,`calendar_id`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8;";
$updates[]="script:4_set_visible_calendar.inc.php";