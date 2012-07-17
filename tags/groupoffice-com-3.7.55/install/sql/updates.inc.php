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

$updates[]="CREATE TABLE IF NOT EXISTS `go_iso_address_format` (
  `iso` varchar(2) NOT NULL,
  `address_format_id` int(11) NOT NULL,
  PRIMARY KEY  (`address_format_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$updates[]="INSERT INTO `go_iso_address_format` (`iso`, `address_format_id`) VALUES
('NL', 1),
('US', 2),
('ES', 3),
('SG', 4);";

$updates[]="CREATE TABLE IF NOT EXISTS `go_address_format` (
  `id` int(11) NOT NULL,
  `format` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
$updates[]="INSERT INTO `go_address_format` (`id`, `format`) VALUES
(1, '{address} {address_no}\r\n{zip} {city}\r\n{state}\r\n{country}'),
(2, '{address_no} {address}\r\n{city}, {state} {zip}\r\n{country}'),
(3, '{address}, {address_no}\r\n{zip} {city}\r\n{state} {country}'),
(4, '{address_no} {address}\r\n{city} {zip}\r\n{state} {country}');";


$updates[]="INSERT INTO `go_address_format` (`id`, `format`) VALUES
(5, '{address_no} {address}\r\n{zip} {city}\r\n{state} {country}'),
(6, '{address_no} {address}\r\n{city}\r\n{zip}\r\n{country}'),
(7, '{address_no} {address}\r\n{zip} {city} {state}\r\n{country}'),
(8, '{address_no} {address}, {city}\r\n{zip} {state}\r\n{country}');";


$updates[]="ALTER TABLE `go_iso_address_format` DROP PRIMARY KEY ,
ADD PRIMARY KEY ( `iso` , `address_format_id` ) ;";

$updates[]="INSERT INTO `go_iso_address_format` (`iso`, `address_format_id`) VALUES
('AD', 1),
('AF', 1),
('AG', 1),
('AI', 1),
('AL', 1),
('AM', 1),
('AN', 1),
('AO', 1),
('AQ', 1),
('AR', 1),
('AS', 1),
('AT', 1),
('AU', 1),
('AW', 1),
('AX', 1),
('AZ', 1),
('BA', 1),
('BB', 1),
('BD', 1),
('BE', 3),
('BF', 1),
('BG', 1),
('BH', 1),
('BI', 1),
('BJ', 1),
('BM', 1),
('BN', 1),
('BO', 1),
('BR', 1),
('BS', 1),
('BT', 1),
('BV', 1),
('BW', 1),
('BY', 1),
('BZ', 1),
('CA', 1),
('CC', 1),
('CD', 1),
('CF', 1),
('CG', 1),
('CH', 1),
('CI', 1),
('CK', 1),
('CL', 1),
('CM', 1),
('CN', 8),
('CO', 1),
('CR', 1),
('CU', 1),
('CV', 1),
('CX', 1),
('CY', 1),
('CZ', 1),
('DE', 1),
('DJ', 1),
('DK', 1),
('DM', 1),
('DO', 1),
('DZ', 1),
('EC', 1),
('EE', 1),
('EG', 1),
('EH', 1),
('ER', 1),
('ET', 1),
('FI', 1),
('FJ', 1),
('FK', 1),
('FM', 1),
('FO', 1),
('FR', 5),
('FX', 1),
('GA', 1),
('GB', 6),
('GD', 1),
('GE', 1),
('GF', 1),
('GG', 1),
('GH', 1),
('GI', 1),
('GL', 1),
('GM', 1),
('GN', 1),
('GP', 1),
('GQ', 1),
('GR', 1),
('GS', 1),
('GT', 1),
('GU', 1),
('GW', 1),
('GY', 1),
('HK', 1),
('HM', 1),
('HN', 1),
('HR', 1),
('HT', 1),
('HU', 1),
('ID', 1),
('IE', 1),
('IL', 1),
('IN', 1),
('IO', 1),
('IQ', 1),
('IR', 1),
('IS', 1),
('IT', 7),
('JM', 1),
('JO', 1),
('JP', 1),
('KE', 1),
('KG', 1),
('KH', 1),
('KI', 1),
('KM', 1),
('KN', 1),
('KW', 1),
('KY', 1),
('KZ', 1),
('LA', 1),
('LB', 1),
('LC', 1),
('LI', 1),
('LK', 1),
('LR', 1),
('LS', 1),
('LT', 1),
('LU', 1),
('LV', 1),
('LY', 1),
('MA', 1),
('MC', 1),
('MD', 1),
('MG', 1),
('MH', 1),
('MK', 1),
('ML', 1),
('MM', 1),
('MN', 1),
('MO', 1),
('MP', 1),
('MQ', 1),
('MR', 1),
('MS', 1),
('MT', 1),
('MU', 1),
('MV', 1),
('MW', 1),
('MX', 1),
('MY', 1),
('MZ', 1),
('NA', 1),
('NC', 1),
('NE', 1),
('NF', 1),
('NG', 1),
('NI', 1),
('NO', 1),
('NP', 1),
('NR', 1),
('NU', 1),
('NZ', 1),
('OM', 1),
('PA', 1),
('PE', 1),
('PF', 1),
('PG', 1),
('PH', 1),
('PL', 1),
('PM', 1),
('PN', 1),
('PR', 1),
('PT', 1),
('PW', 1),
('PY', 1),
('QA', 1),
('RE', 1),
('RO', 1),
('RU', 1),
('RW', 1),
('SA', 1),
('SB', 1),
('SC', 1),
('SD', 1),
('SE', 1),
('SH', 1),
('SI', 1),
('SJ', 1),
('SK', 1),
('SL', 1),
('SM', 1),
('SN', 1),
('SO', 1),
('SR', 1),
('ST', 1),
('SV', 1),
('SY', 1),
('SZ', 1),
('TC', 1),
('TD', 1),
('TF', 1),
('TG', 1),
('TH', 1),
('TJ', 1),
('TK', 1),
('TM', 1),
('TN', 1),
('TO', 1),
('TP', 1),
('TR', 1),
('TT', 1),
('TV', 1),
('TW', 1),
('TZ', 1),
('UA', 1),
('UG', 1),
('UM', 1),
('UY', 1),
('UZ', 1),
('VA', 1),
('VC', 1),
('VE', 1),
('VG', 1),
('VI', 1),
('VU', 1),
('WF', 1),
('WS', 1),
('YE', 1),
('YT', 1),
('YU', 1),
('ZA', 1),
('ZM', 1),
('ZW', 1);";


$updates[]="INSERT INTO `go_address_format` (
`id` ,
`format`
)
VALUES (
'9', '{address}
{address_no}
{zip} {city}
{state}
{country}'
);";


$updates[]="UPDATE `go_iso_address_format` SET `address_format_id` = '9' WHERE `go_iso_address_format`.`iso` = 'NO' AND `go_iso_address_format`.`address_format_id` =1 LIMIT 1 ;";
$updates[]="INSERT INTO `go_iso_address_format` ( `iso` , `address_format_id` )
VALUES (
'AE', '1'
);";

$updates[]="ALTER TABLE `go_state` CHANGE `name` `name` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL";

$updates[]="ALTER TABLE `go_reminders` ADD `vtime` INT NOT NULL DEFAULT 0;";
$updates[]="ALTER TABLE `go_reminders` ADD `mail_send` TINYINT NOT NULL DEFAULT 0;";

$updates[]="ALTER TABLE `go_acl` ADD `level` TINYINT NOT NULL DEFAULT '1'";
$updates[]="UPDATE go_acl SET level=-1";
$updates[]="ALTER TABLE `go_acl` ADD INDEX ( `acl_id` , `user_id` , `group_id` , `level` ) ;";
$updates[]="ALTER TABLE `go_search_cache` CHANGE `acl_read` `acl_id` INT( 11 ) NOT NULL ";
$updates[]="ALTER TABLE `go_search_cache` DROP `acl_write`";

$updates[]='script:6_convert_acl.inc.php';

$updates[]="ALTER TABLE `go_reminders` ADD `mail_send` TINYINT NOT NULL DEFAULT 0;";
$updates[]="ALTER TABLE `go_users` ADD `mail_reminders` BOOL NOT NULL ";

$updates[]="UPDATE `go_users` SET max_rows_list=20;";
$updates[]="ALTER TABLE `go_users` ADD `popup_reminders` BOOLEAN NOT NULL";
$updates[]="UPDATE `go_users` SET `popup_reminders`='1'";

$updates[]="ALTER TABLE `go_users` DROP `auth_md5_pass`";
$updates[]="ALTER TABLE `go_users` ADD `password_type` VARCHAR( 20 ) ";
$updates[]="UPDATE go_users SET password_type='md5'";

$updates[]='script:7_install_blacklist.inc.php';

$updates[]="ALTER TABLE  `go_users` CHANGE  `home_phone`  `home_phone` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL";
$updates[]="ALTER TABLE  `go_users` CHANGE  `work_phone`  `work_phone` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL";
$updates[]="ALTER TABLE  `go_users` CHANGE  `fax`  `fax` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL";
$updates[]="ALTER TABLE  `go_users` CHANGE  `cellular`  `cellular` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL";
$updates[]="ALTER TABLE  `go_users` CHANGE  `work_fax`  `work_fax` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL";

$updates[]= "CREATE TABLE `go_holidays` (
  `id` int(11) NOT NULL default '0',
  `date` int(10) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `region` varchar(4) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `region` (`region`)
) TYPE=MyISAM DEFAULT CHARSET=utf8;";


$updates[]= "ALTER TABLE  `go_reminders` ADD  `snooze_time` INT NOT NULL";
$updates[]= "UPDATE go_reminders SET snooze_time=7200";
$updates[]= "ALTER TABLE  `go_reminders` ADD  `manual` BOOLEAN NOT NULL";
$updates[]= "ALTER TABLE  `go_reminders` DROP INDEX  `link_id`";

$updates[]= "CREATE TABLE IF NOT EXISTS `go_reminders_users` (
  `reminder_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `time` int(11) NOT NULL,
	`mail_sent` BOOL NOT NULL,
  PRIMARY KEY (`reminder_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$updates[]= "INSERT INTO `go_reminders_users` SELECT id,user_id,time,mail_send FROM go_reminders;";


$updates[]= "ALTER TABLE  `go_reminders` DROP `user_id`, DROP  `mail_send` ;";

$updates[]= "ALTER TABLE  `go_reminders` ADD  `text` TEXT NOT NULL";

$updates[]= "ALTER TABLE `go_users` ADD `contact_id` INT NOT NULL";

$updates[]= "ALTER TABLE `go_search_cache` ADD `link_count` INT NOT NULL ";

$updates[]='script:8_install_search.inc.php';

$updates[]="ALTER TABLE `go_users` ADD `cache` TEXT NOT NULL";

$updates[]='script:9_install_mailings.inc.php';

$updates[]="ALTER TABLE  `go_users` CHANGE  `address_no`  `address_no` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL";

$updates[]='CREATE TABLE `go_saved_search_queries` (
`id` INT NOT NULL ,
`user_id` INT NOT NULL ,
`name` VARCHAR( 50 ) NOT NULL ,
`sql` TEXT NOT NULL ,
`type` VARCHAR( 50 ) NOT NULL ,
PRIMARY KEY ( `id` ) ,
INDEX ( `type` )
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;';

$updates[]='ALTER TABLE `go_groups` ADD `acl_id` INT NOT NULL ,
ADD `admin_only` BOOLEAN NOT NULL ';
$updates[]='script:10_add_acl_to_groups.inc.php';
$updates[]="ALTER TABLE `go_users` CHANGE `middle_name` `middle_name` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL";

$updates[]="ALTER TABLE `go_users` ADD `mute_reminder_sound` ENUM( '0', '1' ) NOT NULL AFTER `mute_sound` ,
ADD `mute_new_mail_sound` ENUM( '0', '1' ) NOT NULL AFTER `mute_reminder_sound` ";

$updates[]="ALTER TABLE `go_users` ADD `show_smilies` ENUM( '0', '1' ) NOT NULL DEFAULT '1' AFTER `mute_new_mail_sound`";
$updates[]="ALTER TABLE `go_users` CHANGE `password` `password` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL";

$updates[]="ALTER TABLE `go_users` CHANGE `password` `password` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ";