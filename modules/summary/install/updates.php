<?php
$updates["201206191645"][] = "ALTER TABLE `su_announcements` CHANGE `id` `id` INT( 11 ) NOT NULL AUTO_INCREMENT;";
$updates["201207051232"][] = "ALTER TABLE `su_rss_feeds` CHANGE `summary` `summary` TINYINT( 1 ) NOT NULL DEFAULT '0'";

$updates["201306040852"][] = "ALTER TABLE `su_announcements` ADD `acl_id` INT NOT NULL;";
$updates["201306040853"][]='script:share_existing_announcements.php';

$updates["201408061500"][]= "CREATE TABLE IF NOT EXISTS `su_latest_read_announcement_records` (
  `user_id` int(11) NOT NULL,
  `announcement_id` int(11) NOT NULL DEFAULT '0',
	`announcement_ctime` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";