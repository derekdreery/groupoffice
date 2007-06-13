<?php
$updates[19] = array();
$updates[18] = array();

$updates[17][] = "ALTER TABLE `fs_links` ADD `ctime` INT NOT NULL , ADD `mtime` INT NOT NULL ;";

$updates[16][] = "ALTER TABLE `fs_settings` ADD `open_properties` ENUM( '0', '1' ) NOT NULL ;";
$updates[16][] = "ALTER TABLE `fs_links` ADD `status_id` INT NOT NULL ;";

$updates[16][] = "CREATE TABLE `fs_statuses` (
`id` INT NOT NULL ,
`name` VARCHAR( 50 ) NOT NULL ,
PRIMARY KEY ( `id` )
) TYPE = MYISAM ;";


$updates[16][] = "CREATE TABLE `fs_status_history` (
`id` INT NOT NULL ,
`link_id` INT NOT NULL ,
`status_id` INT NOT NULL ,
`user_id` INT NOT NULL ,
`ctime` INT NOT NULL ,
`comments` TEXT NOT NULL ,
PRIMARY KEY ( `id` ) ,
INDEX ( `link_id` )
) TYPE = MYISAM ;";

$updates[15][] = "CREATE TABLE `fs_settings` (
	`user_id` INT NOT NULL ,
	`notify` ENUM( '0', '1' ) NOT NULL ,
	PRIMARY KEY ( `user_id` )
	) TYPE = MYISAM ;";

$updates[14][] = "CREATE TABLE `fs_links` (
  `link_id` int(11) NOT NULL default '0',
  `path` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`link_id`),
  KEY `path` (`path`)
) TYPE=MyISAM;";

$updates[14][] = "ALTER TABLE `fsShares` RENAME `fs_shares` ;"; 

$updates[13] = array();

$updates[12][] = "ALTER TABLE `fsShares` ADD `link_id` INT  AFTER `user_id` ;";
$updates[12][] = "ALTER TABLE `fsShares` ADD INDEX ( `link_id` ) ;";
$updates[12][] = "ALTER TABLE `fsShares` DROP INDEX `path`;";

$updates[11][] = "ALTER TABLE `fsShares` ADD `type` VARCHAR( 50 ) NOT NULL AFTER `path`";
$updates[11][] = "UPDATE `fsShares` SET TYPE = 'filesystem';";
$updates[11][] = "ALTER TABLE `fsShares` ADD INDEX ( `type` );";

$updates[10][] = "DROP TABLE `fs_settings`";
