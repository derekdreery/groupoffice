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
?>