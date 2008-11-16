<?php
$updates[]="UPDATE em_links SET path=replace(path, '".$GO_CONFIG->file_storage_path."','');";

$updates[]="CREATE TABLE IF NOT EXISTS `em_messages_cache` (
  `folder_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `new` enum('0','1') NOT NULL,
  `subject` varchar(255) NOT NULL,
  `from` varchar(255) NOT NULL,
  `reply_to` varchar(100) NOT NULL,
  `size` int(11) NOT NULL,
  `udate` int(11) NOT NULL,
  `attachments` enum('0','1') NOT NULL,
  `flagged` enum('0','1') NOT NULL,
  `answered` enum('0','1') NOT NULL,
  `priority` tinyint(4) NOT NULL,
  `to` text NOT NULL,
  PRIMARY KEY  (`folder_id`,`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

?>