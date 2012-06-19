<?php
$updates["201108010000"][]="UPDATE go_modules SET version=0";

$updates["201108120000"][]="ALTER TABLE `go_users` CHANGE `id` `id` INT( 11 ) NOT NULL AUTO_INCREMENT";

$updates["201108120000"][]="ALTER TABLE `go_acl_items` CHANGE `id` `id` INT( 11 ) NOT NULL AUTO_INCREMENT";

$updates["201108120000"][]="ALTER TABLE `go_search_cache` DROP `table`";
$updates["201108120000"][]="ALTER TABLE `go_search_cache` DROP `url`";
$updates["201108120000"][]="ALTER TABLE `go_search_cache` DROP `link_count`";
$updates["201108120000"][]="ALTER TABLE `go_search_cache` DROP `acl_read`";
	

$updates["201108120000"][]="ALTER TABLE `go_users` CHANGE `max_rows_list` `max_rows_list` TINYINT( 4 ) NOT NULL DEFAULT '20'";
$updates["201108120000"][]="ALTER TABLE `go_users` CHANGE `registration_time` `ctime` INT( 11 ) NOT NULL DEFAULT '0'";
$updates["201108120000"][]="ALTER TABLE `go_groups` CHANGE `id` `id` INT( 11 ) NOT NULL AUTO_INCREMENT";

$updates["201108181012"][]="ALTER TABLE `go_search_cache` CHANGE `description` `description` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''";

$updates["201108181012"][]="script:11_users_to_addressbook.inc.php";

$updates["201108181012"][]="ALTER TABLE `go_users`
  DROP `initials`,
  DROP `title`,
  DROP `sex`,
  DROP `birthday`,
  DROP `department`,
  DROP `function`,
  DROP `home_phone`,
  DROP `work_phone`,
  DROP `fax`,
  DROP `cellular`,
  DROP `homepage`,
  DROP `contact_id`;";




$updates["201108301656"][]="CREATE TABLE IF NOT EXISTS `go_model_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model_name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";



$updates["201108301656"][]="ALTER TABLE `go_search_cache` CHANGE `id` `model_id` INT( 11 ) NOT NULL DEFAULT '0'";

$updates["201108301656"][]="ALTER TABLE `go_search_cache` CHANGE `link_type` `model_type_id` INT( 11 ) NOT NULL DEFAULT '0' ";
	
$updates["201108301656"][]="ALTER TABLE `go_search_cache` CHANGE `type` `model_name` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL  ";
$updates["201108301656"][]="ALTER TABLE `go_search_cache` DROP PRIMARY KEY  ";
$updates["201108301656"][]="ALTER TABLE `go_search_cache` ADD PRIMARY KEY ( `model_id` , `model_type_id` ) ;";



$updates["201108301656"][]="INSERT INTO `go_model_types` (
`id` ,
`model_name`
)
VALUES (
'8', 'GO_Base_Model_User'
);";

$updates["201108301656"][]="ALTER TABLE `go_search_cache` ADD `type` VARCHAR( 20 ) NOT NULL ";



$updates["201108190000"][]="RENAME TABLE `go_links_8` TO `go_links_go_users`;";
$updates["201108190000"][]="ALTER TABLE `go_links_go_users` CHANGE `link_id` `model_id` INT( 11 ) NOT NULL";
$updates["201108190000"][]="ALTER TABLE `go_links_go_users` CHANGE `link_type` `model_type_id` INT( 11 ) NOT NULL";

$updates["201109280000"][]="ALTER TABLE `go_search_cache` DROP `table`";
$updates["201109280000"][]="ALTER TABLE `go_search_cache` DROP `link_count`";
$updates["201109301050"][]="ALTER TABLE `go_users` CHANGE `show_smilies` `show_smilies` BOOL NOT NULL DEFAULT '1'";

$updates["201110050822"][]="ALTER TABLE `go_reminders` CHANGE `id` `id` INT( 11 ) NOT NULL AUTO_INCREMENT ";
$updates["201110050822"][]="ALTER TABLE `go_reminders` CHANGE `link_id` `model_id` INT( 11 ) NOT NULL";
$updates["201110050822"][]="ALTER TABLE `go_reminders` CHANGE `link_type` `model_type_id` INT( 11 ) NOT NULL ";

$updates["201110050822"][]="ALTER TABLE `go_reminders` CHANGE `manual` `manual` TINYINT( 1 ) NOT NULL DEFAULT '0'";
$updates["201110050822"][]="ALTER TABLE `go_reminders` CHANGE `text` `text` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''";
$updates["201110050822"][]="ALTER TABLE `go_reminders_users` CHANGE `mail_sent` `mail_sent` TINYINT( 1 ) NOT NULL DEFAULT '0'";

$updates["201110070822"][]="ALTER TABLE `go_users` CHANGE `mail_reminders` `mail_reminders` TINYINT( 1 ) NOT NULL DEFAULT '0'";
$updates["201110070822"][]="ALTER TABLE `go_users` CHANGE `popup_reminders` `popup_reminders` TINYINT( 1 ) NOT NULL DEFAULT '0'";
$updates["201110070822"][]="ALTER TABLE `go_users` CHANGE `cache` `cache` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''";
$updates["201110140822"][]="ALTER TABLE `go_users` DROP `cache`";

$updates["201110140822"][]="ALTER TABLE `go_users` CHANGE `mute_sound` `mute_sound` BOOLEAN NOT NULL DEFAULT '0'";
$updates["201110140822"][]="ALTER TABLE `go_users` CHANGE `enabled` `enabled` BOOLEAN NOT NULL DEFAULT '1'";
$updates["201110140822"][]="ALTER TABLE `go_users` CHANGE `mute_reminder_sound` `mute_reminder_sound` BOOLEAN NOT NULL DEFAULT '0'";
$updates["201110140822"][]="ALTER TABLE `go_users` CHANGE `mute_new_mail_sound` `mute_new_mail_sound` BOOLEAN NOT NULL DEFAULT '0'";

$updates["201110141221"][]="UPDATE go_users SET mute_sound=0 where mute_sound=1";
$updates["201110141221"][]="UPDATE go_users SET mute_sound=1 where mute_sound=2";

$updates["201110141221"][]="UPDATE go_users SET enabled=0 where enabled=1";
$updates["201110141221"][]="UPDATE go_users SET enabled=1 where enabled=2";

$updates["201110141221"][]="UPDATE go_users SET mute_reminder_sound=0 where mute_reminder_sound=1";
$updates["201110141221"][]="UPDATE go_users SET mute_reminder_sound=1 where mute_reminder_sound=2";

$updates["201110141221"][]="UPDATE go_users SET mute_new_mail_sound=0 where mute_new_mail_sound=1";
$updates["201110141221"][]="UPDATE go_users SET mute_new_mail_sound=1 where mute_new_mail_sound=2";

$updates["201110311221"][]="ALTER TABLE `go_modules` CHANGE `admin_menu` `admin_menu` BOOLEAN NOT NULL DEFAULT '0'";
$updates["201110311221"][]="UPDATE go_modules SET admin_menu=0 where admin_menu=1";
$updates["201110311221"][]="UPDATE go_modules SET admin_menu=1 where admin_menu=2";
	
$updates["201111011221"][]="ALTER TABLE `go_reminders` CHANGE `text` `text` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL ";

$updates["201111112300"][]="ALTER TABLE `go_users` CHANGE `files_folder_id` `files_folder_id` INT( 11 ) NOT NULL DEFAULT '0'";
$updates["201111112300"][]="ALTER TABLE `go_users` CHANGE `password` `password` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ";

$updates["201111112300"][]="DROP TABLE IF EXISTS `go_iso_address_format`;";

$updates["201111112300"][]="ALTER TABLE `go_groups` CHANGE `name` `name` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL";
$updates["201111112300"][]="ALTER TABLE `go_groups` CHANGE `admin_only` `admin_only` TINYINT( 1 ) NOT NULL DEFAULT '0'";
$updates["201112021417"][]="UPDATE `go_users` SET `date_separator` = '/' WHERE `date_format` = 'mdY';";

$updates["201112021417"][]="CREATE TABLE IF NOT EXISTS `cf_go_users` (
  `model_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`model_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$updates["201201091109"][]="ALTER DATABASE CHARACTER SET utf8 COLLATE utf8_general_ci;";


$updates["201201091109"][]="script:12_users_to_companies.php";

$updates["201201091109"][]="ALTER TABLE `go_users`
	DROP `company`,
	DROP `country`,
  DROP `state`,
  DROP `city`,
  DROP `zip`,
  DROP `address`,
  DROP `address_no`,
  
  DROP `work_address`,
  DROP `work_address_no`,
  DROP `work_zip`,
  DROP `work_country`,
  DROP `work_state`,
  DROP `work_city`,
  DROP `work_fax`;";

$updates["201202131145"][]= "CREATE TABLE IF NOT EXISTS `go_advanced_searches` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) NOT NULL DEFAULT '',
	`user_id` int(11) NOT NULL DEFAULT '0',
	`acl_id` int(11) NOT NULL DEFAULT '0',
	`data` TEXT NULL DEFAULT '',
	`model_name` VARCHAR(100) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$updates["201202131153"][]= "ALTER TABLE `go_users` CHANGE `middle_name` `middle_name` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''";

$updates["201203070921"][]= "ALTER TABLE `go_users` CHANGE `sort_name` `sort_name` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'first_name'";

$updates["201203070921"][]= "update go_users set sort_name='first_name' where sort_name!='first_name' AND sort_name!='last_name'";

$updates["201203261017"][]= "ALTER TABLE `go_modules` DROP `acl_write`";

$updates["201203261017"][]= "ALTER TABLE `go_users` CHANGE `username` `username` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ";
$updates["201203261017"][]= "ALTER TABLE `go_users` CHANGE `password` `password` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ";
$updates["201203261017"][]= "ALTER TABLE `go_users` CHANGE `first_name` `first_name` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL";
$updates["201203261017"][]= "ALTER TABLE `go_users` CHANGE `last_name` `last_name` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ";
$updates["201203261017"][]= "ALTER TABLE `go_users` CHANGE `email` `email` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ";
$updates["201203261017"][]= "ALTER TABLE `go_users` CHANGE `date_format` `date_format` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'dmY'";
$updates["201203261017"][]= "ALTER TABLE `go_users` CHANGE `time_format` `time_format` VARCHAR( 10 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'G:i'";
$updates["201203261017"][]= "ALTER TABLE `go_users` CHANGE `timezone` `timezone` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'Europe/Amsterdam'";
$updates["201203261017"][]= "ALTER TABLE `go_users` CHANGE `start_module` `start_module` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'summary'";
$updates["201203261017"][]= "ALTER TABLE `go_users` CHANGE `language` `language` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'en'";
$updates["201203261017"][]= "ALTER TABLE `go_users` CHANGE `theme` `theme` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'Default'";
$updates["201203261017"][]= "ALTER TABLE `go_users` CHANGE `bank` `bank` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''";
$updates["201203261017"][]= "ALTER TABLE `go_users` CHANGE `bank_no` `bank_no` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''";
$updates["201203261017"][]= "ALTER TABLE `go_users` CHANGE `password_type` `password_type` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'crypt'";


$updates["201203261017"][]= "ALTER TABLE `go_users`
  DROP `bank`,
  DROP `bank_no`;";


$updates["201204051001"][]= "ALTER TABLE `go_modules` ADD `enabled` BOOLEAN NOT NULL DEFAULT '1'";

$updates["201204251613"][]= "DROP TABLE IF EXISTS `go_log`;";
$updates["201204251613"][]= "CREATE TABLE IF NOT EXISTS `go_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL DEFAULT '',
  `model` varchar(255) NOT NULL DEFAULT '',
  `model_id` varchar(255) NOT NULL DEFAULT '',
  `ctime` int(11) NOT NULL,
  `user_agent` varchar(100) NOT NULL DEFAULT '',
  `ip` varchar(15) NOT NULL DEFAULT '',
  `controller_route` varchar(255) NOT NULL DEFAULT '',
  `action` varchar(20) NOT NULL DEFAULT '',
  `message` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";

$updates["201204251613"][]= "ALTER TABLE `go_log` CHANGE `user_agent` `user_agent` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''";

// Change permission levels to new values
$updates["201205020900"][]="UPDATE `go_acl` SET `level`=10 WHERE `level`=1;";
$updates["201205020900"][]="UPDATE `go_acl` SET `level`=30 WHERE `level`=2;";
$updates["201205020900"][]="UPDATE `go_acl` SET `level`=40 WHERE `level`=3;";
$updates["201205020900"][]="UPDATE `go_acl` SET `level`=50 WHERE `level`=4;";

$updates["201204251613"][]= "ALTER TABLE `go_advanced_searches` ADD `model_name` VARCHAR( 100 ) NOT NULL DEFAULT ''";

$updates["201204251613"][]="ALTER TABLE `go_advanced_searches` CHANGE `model_name` `model_name` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''";

$updates["201204251613"][]="ALTER TABLE `go_acl` ADD INDEX ( `acl_id` , `user_id` ) ;";
$updates["201204251613"][]="ALTER TABLE `go_acl` ADD INDEX ( `acl_id` , `group_id` ) ;";


$updates["201204251613"][]="ALTER TABLE `go_search_cache` CHANGE `keywords` `keywords` VARCHAR( 254 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''";

$updates["201204251613"][]="ALTER TABLE `go_search_cache` ADD INDEX ( `acl_id` ) ";
$updates["201204251613"][]="ALTER TABLE `go_search_cache` ADD INDEX ( `keywords` ) ";

$updates["201204251613"][]="ALTER TABLE `go_search_cache` CHANGE `keywords` `keywords` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''";

$updates["201204251613"][]="update go_users set language='en' where language='';";

$updates["201206051617"][]="ALTER TABLE `go_search_cache` ADD FULLTEXT ft_keywords(
`name` ,
`keywords`
);";

$updates["201206051617"][]="ALTER TABLE go_search_cache DROP INDEX name";
$updates["201206051617"][]="ALTER TABLE go_search_cache DROP INDEX keywords";
$updates["201206051617"][]="ALTER TABLE go_search_cache DROP INDEX name_2";

$updates["201206110852"][]="ALTER TABLE `go_search_cache` ADD INDEX name( `name` ) ";

$updates["201206110852"][]="ALTER TABLE `go_link_folders` CHANGE `id` `id` INT( 11 ) NOT NULL AUTO_INCREMENT ";
$updates["201206110852"][]="ALTER TABLE `go_link_folders` CHANGE `link_type` `model_type_id` INT( 11 ) NOT NULL ";
$updates["201206110852"][]="ALTER TABLE `go_link_folders` CHANGE `link_id` `model_id` INT( 11 ) NOT NULL ";

$updates["201206110852"][]="ALTER TABLE `go_link_folders` CHANGE `model_id` `model_id` INT( 11 ) NOT NULL DEFAULT '0'";
$updates["201206110852"][]="ALTER TABLE `go_link_folders` CHANGE `model_type_id` `model_type_id` INT( 11 ) NOT NULL DEFAULT '0'";
$updates["201206110852"][]="ALTER TABLE `go_link_folders` CHANGE `parent_id` `parent_id` INT( 11 ) NOT NULL DEFAULT '0'";


// Change permission levels to new values
$updates["201206191425"][]="UPDATE `go_acl` SET `level`=10 WHERE `level`=1;";
$updates["201206191425"][]="UPDATE `go_acl` SET `level`=30 WHERE `level`=2;";
$updates["201206191425"][]="UPDATE `go_acl` SET `level`=40 WHERE `level`=3;";
$updates["201206191425"][]="UPDATE `go_acl` SET `level`=50 WHERE `level`=4;";