<?php
$updates=array();

$updates[]="CREATE TABLE `go_mail_counter` (
`host` VARCHAR( 100 ) NOT NULL ,
`date` DATE NOT NULL ,
`count` INT NOT NULL ,
PRIMARY KEY ( `host` ) ,
INDEX ( `date` )
) ENGINE = MYISAM"; 

$updates[]="ALTER TABLE `go_search_cache` ADD PRIMARY KEY(`id`,`link_type`);";

$updates[]='script:1.inc.php';
$updates[]='script:2_ctime_in_links.inc.php';

$updates[]='script:3_install_comments_module.inc.php';
$updates[]="ALTER TABLE `go_users` ADD `mute_sound` ENUM( '0', '1' ) NOT NULL ;";
$updates[]="UPDATE go_users SET max_rows_list=50 WHERE max_rows_list>50;";


$updates[]="CREATE TABLE IF NOT EXISTS `go_cache` (
  `user_id` int(11) NOT NULL,
  `key` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `mtime` int(11) NOT NULL,
  PRIMARY KEY  (`user_id`,`key`),
  KEY `mtime` (`mtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$updates[]='script:4_set_null_allowed.inc.php';

$updates[]="ALTER TABLE `go_users` CHANGE `thousands_seperator` `thousands_separator` CHAR( 1 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '.'";
$updates[]="ALTER TABLE `go_users` CHANGE `decimal_seperator` `decimal_separator` CHAR( 1 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ','";
$updates[]="ALTER TABLE `go_users` CHANGE `date_seperator` `date_separator` CHAR( 1 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '-'";

$updates[]='script:5_warn_separator.inc.php';


$updates[]='DROP TABLE `go_log`';
$updates[]="CREATE TABLE IF NOT EXISTS `go_log` (
  `id` int(11) NOT NULL,
  `link_id` int(11) NOT NULL,
  `link_type` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `text` varchar(100) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `link_type` (`link_type`,`time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";  

$updates[]="CREATE TABLE IF NOT EXISTS `go_link_descriptions` (
  `id` int(11) NOT NULL,
  `description` varchar(100) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$updates[]="ALTER TABLE `go_users` ADD `list_separator` CHAR( 3 ) NOT NULL DEFAULT ';',
ADD `text_separator` CHAR( 3 ) NOT NULL DEFAULT '\"'";

$updates[]="ALTER TABLE `go_users` ADD `files_folder_id` INT NOT NULL;";


$updates[]="delete FROM `go_state` WHERE `index`!='go';";
$updates[]="ALTER TABLE `go_state` DROP `index`";
?>