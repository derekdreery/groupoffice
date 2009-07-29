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