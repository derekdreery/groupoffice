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
?>