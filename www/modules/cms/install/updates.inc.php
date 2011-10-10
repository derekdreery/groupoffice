<?php
$updates[]="script:1_move_local_files.inc.php";
$updates[]="ALTER TABLE `cms_folders` ADD `default_template` VARCHAR( 100 ) NOT NULL ;";
$updates[]="ALTER TABLE `cms_folders` ADD `type` VARCHAR( 100 ) NOT NULL ;";
$updates[]="ALTER TABLE `cms_files` ADD `type` VARCHAR( 100 ) NOT NULL ;";
$updates[]="ALTER TABLE `cms_sites` ADD `files_folder_id` INT NOT NULL";
$updates[]="ALTER TABLE `cms_files` ADD `files_folder_id` INT NOT NULL";
$updates[]="script:2_new_paths.inc.php";
$updates[]="ALTER TABLE `cms_sites` CHANGE `template` `template` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ";
$updates[]="ALTER TABLE `cms_files` ADD `show_until` INT NOT NULL ,ADD INDEX ( show_until )";
//$updates[]="script:3_convert_acl.inc.php";
$updates[]="CREATE TABLE IF NOT EXISTS `cms_user_folder_access` (
  `user_id` int(11) NOT NULL default '0',
  `folder_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`user_id`,`folder_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
$updates[]="CREATE TABLE IF NOT EXISTS `cms_user_site_filter` (
  `user_id` int(11) NOT NULL default '0',
  `site_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`user_id`,`site_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
$updates[]="ALTER TABLE `cms_files` ADD `sort_time` int(11) NOT NULL default '0';";

$updates[]="ALTER TABLE `cms_folders` ADD `feed` BOOLEAN NOT NULL";
$updates[]="ALTER TABLE `cms_folders` ADD INDEX ( `feed` ) ";
$updates[]="ALTER TABLE `cms_sites` ADD `enable_rewrite` BOOLEAN NOT NULL";
$updates[]="ALTER TABLE `cms_sites` ADD `rewrite_base` VARCHAR( 50 ) NOT NULL";
$updates[]="UPDATE `cms_sites` SET `rewrite_base`='/', enable_rewrite='1'";

$updates[]="ALTER TABLE `cms_sites` ADD `enable_categories` BOOLEAN NOT NULL default '0';";
$updates[]="CREATE TABLE IF NOT EXISTS `cms_categories` (
	`id` int(11) NOT NULL default '0',
	`name` VARCHAR(50) NOT NULL default 'category_name',
	`site_id` int(11) NOT NULL default '0',
	PRIMARY KEY (`id`),
	KEY `site_id` (`site_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
$updates[]="CREATE TABLE IF NOT EXISTS `cms_files_categories` (
	`category_id` int(11) NOT NULL default '0',
	`file_id` int(11) NOT NULL default '0',
	PRIMARY KEY (`category_id`,`file_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$updates[]="ALTER TABLE `cms_categories` ADD `parent_id` int(11) NOT NULL default '0';";