-- phpMyAdmin SQL Dump
-- version 2.10.3deb1ubuntu0.2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generatie Tijd: 22 Apr 2008 om 14:57
-- Server versie: 5.0.45
-- PHP Versie: 5.2.3-1ubuntu6.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

DROP TABLE IF EXISTS `go_saved_search_queries`;
CREATE TABLE `go_saved_search_queries` (
`id` INT NOT NULL ,
`user_id` INT NOT NULL ,
`name` VARCHAR( 50 ) NOT NULL ,
`sql` TEXT NOT NULL ,
`type` VARCHAR( 50 ) NOT NULL ,
PRIMARY KEY ( `id` ) ,
INDEX ( `type` )
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

-- 
-- Database: `imfoss_nl`
--
DROP TABLE IF EXISTS `go_holidays`;
CREATE TABLE `go_holidays` (
  `id` int(11) NOT NULL default '0',
  `date` int(10) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `region` varchar(4) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `region` (`region`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `go_link_descriptions`;
CREATE TABLE IF NOT EXISTS `go_link_descriptions` (
  `id` int(11) NOT NULL,
  `description` varchar(100) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Tabel structuur voor tabel `go_acl`
--

DROP TABLE IF EXISTS `go_acl`;
CREATE TABLE IF NOT EXISTS `go_acl` (
  `acl_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '0',
	`level` TINYINT NOT NULL DEFAULT '1',
  PRIMARY KEY  (`acl_id`,`user_id`,`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `go_acl_items`
--

DROP TABLE IF EXISTS `go_acl_items`;
CREATE TABLE IF NOT EXISTS `go_acl_items` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `description` varchar(50) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `go_cache`
--

DROP TABLE IF EXISTS `go_cache`;
CREATE TABLE IF NOT EXISTS `go_cache` (
  `user_id` int(11) NOT NULL,
  `key` varchar(255) NOT NULL default '',
  `content` longtext,
  `mtime` int(11) NOT NULL,
  PRIMARY KEY  (`user_id`,`key`),
  KEY `mtime` (`mtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `go_countries`
--

DROP TABLE IF EXISTS `go_countries`;
CREATE TABLE IF NOT EXISTS `go_countries` (
  `id` int(11) NOT NULL default '0',
  `name` varchar(64) default NULL,
  `iso_code_2` char(2) NOT NULL default '',
  `iso_code_3` char(3) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `go_db_sequence`
--

DROP TABLE IF EXISTS `go_db_sequence`;
CREATE TABLE IF NOT EXISTS `go_db_sequence` (
  `seq_name` varchar(50) NOT NULL default '',
  `nextid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`seq_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `go_groups`
--

DROP TABLE IF EXISTS `go_groups`;
CREATE TABLE IF NOT EXISTS `go_groups` (
  `id` int(11) NOT NULL default '0',
  `name` varchar(50) default NULL,
  `user_id` int(11) NOT NULL default '0',
  `acl_id` INT NOT NULL ,
	`admin_only` BOOLEAN NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `go_link_folders`
--

DROP TABLE IF EXISTS `go_link_folders`;
CREATE TABLE IF NOT EXISTS `go_link_folders` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `link_id` int(11) NOT NULL,
  `link_type` int(11) NOT NULL,
  `name` varchar(50) default NULL,
  PRIMARY KEY  (`id`),
  KEY `link_id` (`link_id`,`link_type`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `go_log`
--

DROP TABLE IF EXISTS `go_log`;
CREATE TABLE IF NOT EXISTS `go_log` (
  `id` int(11) NOT NULL,
  `link_id` int(11) NOT NULL,
  `link_type` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `text` varchar(100) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `link_type` (`link_type`,`time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `go_mail_counter`
--

DROP TABLE IF EXISTS `go_mail_counter`;
CREATE TABLE IF NOT EXISTS `go_mail_counter` (
  `host` varchar(100) NOT NULL default '',
  `date` date NOT NULL,
  `count` int(11) NOT NULL,
  PRIMARY KEY  (`host`),
  KEY `date` (`date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `go_modules`
--

DROP TABLE IF EXISTS `go_modules`;
CREATE TABLE IF NOT EXISTS `go_modules` (
  `id` varchar(20) NOT NULL default '',
  `version` int(11) NOT NULL,
  `sort_order` int(11) NOT NULL default '0',
  `admin_menu` enum('0','1') NOT NULL default '0',
  `acl_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `go_reminders`
--

DROP TABLE IF EXISTS `go_reminders`;

CREATE TABLE IF NOT EXISTS `go_reminders` (
  `id` int(11) NOT NULL,
  `link_id` int(11) NOT NULL,
  `link_type` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `time` int(11) NOT NULL,
  `vtime` int(11) NOT NULL DEFAULT '0',
  `snooze_time` int(11) NOT NULL,
  `manual` tinyint(1) NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `go_reminders_users`;
CREATE TABLE IF NOT EXISTS `go_reminders_users` (
  `reminder_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `mail_sent` tinyint(1) NOT NULL,
  PRIMARY KEY (`reminder_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `go_search_cache`
--

DROP TABLE IF EXISTS `go_search_cache`;
CREATE TABLE IF NOT EXISTS `go_search_cache` (
  `user_id` int(11) NOT NULL default '0',
  `table` varchar(50) default NULL,
  `id` int(11) NOT NULL default '0',
  `module` varchar(50) default NULL,
  `name` varchar(100) default NULL,
  `description` varchar(255) default NULL,
  `url` varchar(255) default NULL,
  `link_type` int(11) NOT NULL default '0',
  `type` varchar(20) default NULL,
  `keywords` text,
  `mtime` int(11) NOT NULL default '0',
  `acl_id` int(11) NOT NULL,
  `link_count` INT NOT NULL,
  PRIMARY KEY  (`id`,`link_type`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `go_search_sync`
--

DROP TABLE IF EXISTS `go_search_sync`;
CREATE TABLE IF NOT EXISTS `go_search_sync` (
  `user_id` int(11) NOT NULL default '0',
  `module` varchar(50) NOT NULL default '',
  `last_sync_time` int(11) NOT NULL default '0',
  PRIMARY KEY  (`user_id`,`module`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `go_settings`
--

DROP TABLE IF EXISTS `go_settings`;
CREATE TABLE IF NOT EXISTS `go_settings` (
  `user_id` int(11) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `value` text,
  PRIMARY KEY  (`user_id`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `go_state`
--

DROP TABLE IF EXISTS `go_state`;
CREATE TABLE IF NOT EXISTS `go_state` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL default '',
  `value` text,
  PRIMARY KEY  (`user_id`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `go_users`
--

DROP TABLE IF EXISTS `go_users`;
CREATE TABLE IF NOT EXISTS `go_users` (
  `id` int(11) NOT NULL default '0',
  `username` varchar(50) default NULL,
  `password` varchar(255) default NULL,
  `password_type` VARCHAR( 20 ) default NULL,
  `enabled` enum('0','1') NOT NULL default '1',
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) default NULL,
  `last_name` varchar(100) default NULL,
  `initials` varchar(10) default NULL,
  `title` varchar(10) default NULL,
  `sex` enum('M','F') NOT NULL default 'M',
  `birthday` date default NULL,
  `email` varchar(100) default NULL,
  `company` varchar(50) default NULL,
  `department` varchar(50) default NULL,
  `function` varchar(50) default NULL,
  `home_phone` varchar(30) default NULL,
  `work_phone` varchar(30) default NULL,
  `fax` varchar(30) default NULL,
  `cellular` varchar(30) default NULL,
  `country` char(2) NOT NULL,
  `state` varchar(50) default NULL,
  `city` varchar(50) default NULL,
  `zip` varchar(10) default NULL,
  `address` varchar(100) default NULL,
  `address_no` varchar(100) default NULL,
  `homepage` varchar(100) default NULL,
  `work_address` varchar(100) default NULL,
  `work_address_no` varchar(10) default NULL,
  `work_zip` varchar(10) default NULL,
  `work_country` char(2) NOT NULL,
  `work_state` varchar(50) default NULL,
  `work_city` varchar(50) default NULL,
  `work_fax` varchar(30) default NULL,
  `acl_id` int(11) NOT NULL default '0',
  `date_format` varchar(20) default NULL,
  `date_separator` char(1) NOT NULL default '-',
  `time_format` varchar(10) default NULL,
  `thousands_separator` char(1) NOT NULL default '.',
  `decimal_separator` char(1) NOT NULL default ',',
  `currency` char(3) NOT NULL default '',
  `logins` int(11) NOT NULL default '0',
  `lastlogin` int(11) NOT NULL default '0',
  `registration_time` int(11) NOT NULL default '0',
  `max_rows_list` tinyint(4) NOT NULL default '15',
  `timezone` varchar(50) default NULL,
  `start_module` varchar(50) default NULL,
  `language` varchar(20) default NULL,
  `theme` varchar(20) default NULL,
  `first_weekday` tinyint(4) NOT NULL default '0',
  `sort_name` varchar(20) default NULL,
  `bank` varchar(50) default NULL,
  `bank_no` varchar(50) default NULL,
  `mtime` int(11) NOT NULL default '0',
  `mute_sound` enum('0','1') NOT NULL,
  `mute_reminder_sound` enum('0','1') NOT NULL,
  `mute_new_mail_sound` enum('0','1') NOT NULL,
  `show_smilies` enum('0','1') NOT NULL default '1',
  `list_separator` char(3) NOT NULL default ';',
  `text_separator` char(3) NOT NULL default '"',
  `files_folder_id` INT NOT NULL,
  `mail_reminders` BOOL NOT NULL,
  `popup_reminders` BOOLEAN NOT NULL,
  `contact_id` INT NOT NULL,
  `cache` TEXT NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `go_users_groups`
--

DROP TABLE IF EXISTS `go_users_groups`;
CREATE TABLE IF NOT EXISTS `go_users_groups` (
  `group_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`group_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `go_address_format`
--

DROP TABLE IF EXISTS `go_address_format`;
CREATE TABLE IF NOT EXISTS `go_address_format` (
  `id` int(11) NOT NULL,
  `format` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `go_address_format`
--

INSERT INTO `go_address_format` (`id`, `format`) VALUES
(1, '{address} {address_no}\r\n{zip} {city}\r\n{state}\r\n{country}'),
(2, '{address_no} {address}\r\n{city}, {state} {zip}\r\n{country}'),
(3, '{address}, {address_no}\r\n{zip} {city}\r\n{state} {country}'),
(4, '{address_no} {address}\r\n{city} {zip}\r\n{state} {country}'),
(5, '{address_no} {address}\r\n{zip} {city}\r\n{state} {country}'),
(6, '{address_no} {address}\r\n{city}\r\n{zip}\r\n{country}'),
(7, '{address_no} {address}\r\n{zip} {city} {state}\r\n{country}'),
(8, '{address_no} {address}, {city}\r\n{zip} {state}\r\n{country}');

INSERT INTO `go_address_format` (
`id` ,
`format`
)
VALUES (
'9', '{address} {address_no} {zip} {city} {state} {country}'
);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `go_iso_address_format`
--

DROP TABLE IF EXISTS `go_iso_address_format`;
CREATE TABLE IF NOT EXISTS `go_iso_address_format` (
  `iso` varchar(2) NOT NULL,
  `address_format_id` int(11) NOT NULL,
  PRIMARY KEY  (`iso`,`address_format_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `go_iso_address_format`
--

INSERT INTO `go_iso_address_format` (`iso`, `address_format_id`) VALUES
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
('ES', 3),
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
('NL', 1),
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
('SG', 4),
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
('US', 2),
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
('AE', 1),
('ZW', 1);