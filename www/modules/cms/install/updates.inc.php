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
$updates[]="DROP TABLE IF EXISTS `cms_user_folder_forbid`;
CREATE TABLE IF NOT EXISTS `cms_user_folder_forbid` (
  `user_id` int(11) NOT NULL default '0',
  `folder_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`user_id`,`folder_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
?>