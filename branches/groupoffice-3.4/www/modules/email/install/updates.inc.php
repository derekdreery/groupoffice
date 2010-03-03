<?php
$updates[]="UPDATE em_links SET path=replace(path, '".$GO_CONFIG->file_storage_path."','');";

$updates[]="CREATE TABLE IF NOT EXISTS `em_messages_cache` (
  `folder_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `new` enum('0','1') NOT NULL,
  `subject` varchar(100) NOT NULL,
  `from` varchar(100) NOT NULL,
  `reply_to` varchar(100) NOT NULL,
  `size` int(11) NOT NULL,
  `udate` int(11) NOT NULL,
  `attachments` enum('0','1') NOT NULL,
  `flagged` enum('0','1') NOT NULL,
  `answered` enum('0','1') NOT NULL,
  `priority` tinyint(4) NOT NULL,
  `to` varchar(100) NOT NULL,
  PRIMARY KEY  (`folder_id`,`uid`),
  KEY `account_id` (`account_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$updates[]="ALTER TABLE `em_folders` ADD `sort` LONGTEXT NOT NULL ;";

$updates[]="update `em_filters` set field='from' where field='sender';";

$updates[]="ALTER TABLE `em_messages_cache` ADD `notification` VARCHAR( 100 ) NOT NULL ,
ADD `content_type` VARCHAR( 100 ) NOT NULL ,
ADD `content_transfer_encoding` VARCHAR( 50 ) NOT NULL ;";

$updates[]="ALTER TABLE `em_accounts`
  DROP `enable_vacation`,
  DROP `vacation_subject`,
  DROP `vacation_text`;";

$updates[]="ALTER TABLE `em_links` ADD `acl_read` INT NOT NULL ,
ADD `acl_write` INT NOT NULL ;";

$updates[]="ALTER TABLE `em_accounts` CHANGE `smtp_encryption` `smtp_encryption` CHAR( 3 ) NOT NULL";  
$updates[]="UPDATE em_accounts SET smtp_encryption='' WHERE smtp_encryption='8'";
$updates[]="UPDATE em_accounts SET smtp_encryption='tls' WHERE smtp_encryption='2'";
$updates[]="UPDATE em_accounts SET smtp_encryption='ssl' WHERE smtp_encryption='4'";
$updates[]="UPDATE em_accounts SET smtp_encryption='' WHERE smtp_encryption='0'";

$updates[]="CREATE TABLE IF NOT EXISTS `em_aliases` (
  `id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `signature` text NOT NULL,
  `default` enum('0','1') NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `account_id` (`account_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$updates[]="script:1_sender_aliases.inc.php";

$updates[]="ALTER TABLE `em_accounts` DROP `name` , DROP `email` ;";
$updates[]="ALTER TABLE `em_messages_cache` CHANGE `to` `to` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ";

$updates[]="ALTER TABLE `em_links` CHANGE `acl_read` `acl_id` INT( 11 ) NOT NULL DEFAULT '0'";
$updates[]="ALTER TABLE `em_messages_cache` ADD `serialized_message_object` MEDIUMTEXT NOT NULL ";

$updates[]="TRUNCATE TABLE `em_messages_cache`";