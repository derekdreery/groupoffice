-- phpMyAdmin SQL Dump
-- version 2.10.3deb1ubuntu0.2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generatie Tijd: 22 Apr 2008 om 14:57
-- Server versie: 5.0.45
-- PHP Versie: 5.2.3-1ubuntu6.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- Database: `imfoss_nl`
-- 

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `go_acl`
-- 

DROP TABLE IF EXISTS `go_acl`;
CREATE TABLE IF NOT EXISTS `go_acl` (
  `acl_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '0',
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
  `description` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `go_countries`
-- 

DROP TABLE IF EXISTS `go_countries`;
CREATE TABLE IF NOT EXISTS `go_countries` (
  `id` int(11) NOT NULL default '0',
  `name` varchar(64) NOT NULL default '',
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
  `name` varchar(50) NOT NULL default '',
  `user_id` int(11) NOT NULL default '0',
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
  `name` varchar(50) NOT NULL,
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
  `user_id` int(11) NOT NULL,
  `module` varchar(50) NOT NULL,
  `time` int(11) NOT NULL,
  `link_id` int(255) NOT NULL,
  `text` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
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
  `acl_read` int(11) NOT NULL default '0',
  `acl_write` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `go_reminders`
-- 

DROP TABLE IF EXISTS `go_reminders`;
CREATE TABLE IF NOT EXISTS `go_reminders` (
  `id` int(11) NOT NULL default '0',
  `link_id` int(11) NOT NULL default '0',
  `link_type` int(11) NOT NULL,
  `user_id` int(11) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `time` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `link_id` (`link_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `go_search_cache`
-- 

DROP TABLE IF EXISTS `go_search_cache`;
CREATE TABLE IF NOT EXISTS `go_search_cache` (
  `user_id` int(11) NOT NULL default '0',
  `table` varchar(50) NOT NULL default '',
  `id` int(11) NOT NULL default '0',
  `module` varchar(50) NOT NULL default '',
  `name` varchar(100) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  `url` varchar(255) NOT NULL default '',
  `link_type` int(11) NOT NULL default '0',
  `type` varchar(20) NOT NULL default '',
  `keywords` text NOT NULL,
  `mtime` int(11) NOT NULL default '0',
  `acl_read` int(11) NOT NULL,
  `acl_write` int(11) NOT NULL,
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
  `value` text NOT NULL,
  PRIMARY KEY  (`user_id`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `go_state`
-- 

DROP TABLE IF EXISTS `go_state`;
CREATE TABLE IF NOT EXISTS `go_state` (
  `user_id` int(11) NOT NULL,
  `index` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY  (`user_id`,`index`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `go_users`
-- 
DROP TABLE IF EXISTS `go_users`;
CREATE TABLE IF NOT EXISTS `go_users` (
  `id` int(11) NOT NULL default '0',
  `username` varchar(50) NOT NULL default '',
  `password` varchar(64) NOT NULL default '',
  `auth_md5_pass` varchar(100) NOT NULL,
  `enabled` enum('0','1') NOT NULL default '1',
  `first_name` varchar(50) NOT NULL default '',
  `middle_name` varchar(50) NOT NULL default '',
  `last_name` varchar(100) NOT NULL default '',
  `initials` varchar(10) NOT NULL default '',
  `title` varchar(10) NOT NULL default '',
  `sex` enum('M','F') NOT NULL default 'M',
  `birthday` date NOT NULL default '0000-00-00',
  `email` varchar(100) NOT NULL default '',
  `company` varchar(50) NOT NULL default '',
  `department` varchar(50) NOT NULL default '',
  `function` varchar(50) NOT NULL default '',
  `home_phone` varchar(20) NOT NULL default '',
  `work_phone` varchar(20) NOT NULL default '',
  `fax` varchar(20) NOT NULL default '',
  `cellular` varchar(20) NOT NULL default '',
  `country` char(2) NOT NULL,
  `state` varchar(50) NOT NULL default '',
  `city` varchar(50) NOT NULL default '',
  `zip` varchar(10) NOT NULL default '',
  `address` varchar(100) NOT NULL default '',
  `address_no` varchar(10) NOT NULL default '',
  `homepage` varchar(100) NOT NULL default '',
  `work_address` varchar(100) NOT NULL default '',
  `work_address_no` varchar(10) NOT NULL default '',
  `work_zip` varchar(10) NOT NULL default '',
  `work_country` char(2) NOT NULL,
  `work_state` varchar(50) NOT NULL default '',
  `work_city` varchar(50) NOT NULL default '',
  `work_fax` varchar(20) NOT NULL default '',
  `acl_id` int(11) NOT NULL default '0',
  `date_format` varchar(20) NOT NULL default 'd-m-Y H:i',
  `date_seperator` char(1) NOT NULL default '-',
  `time_format` varchar(10) NOT NULL default '',
  `thousands_seperator` char(1) NOT NULL default '.',
  `decimal_seperator` char(1) NOT NULL default ',',
  `currency` char(3) NOT NULL default '',
  `logins` int(11) NOT NULL default '0',
  `lastlogin` int(11) NOT NULL default '0',
  `registration_time` int(11) NOT NULL default '0',
  `max_rows_list` tinyint(4) NOT NULL default '15',
  `timezone` varchar(50) NOT NULL default '0',
  `start_module` varchar(50) NOT NULL default '',
  `language` varchar(20) NOT NULL default '',
  `theme` varchar(20) NOT NULL default '',
  `first_weekday` tinyint(4) NOT NULL default '0',
  `sort_name` varchar(20) NOT NULL default 'first_name',
  `bank` varchar(50) NOT NULL default '',
  `bank_no` varchar(50) NOT NULL default '',
  `mtime` int(11) NOT NULL default '0',
  `mute_sound` enum('0','1') NOT NULL,
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


DROP TABLE IF EXISTS `go_mail_counter`;
CREATE TABLE `go_mail_counter` (
`host` VARCHAR( 100 ) NOT NULL ,
`date` DATE NOT NULL ,
`count` INT NOT NULL ,
PRIMARY KEY ( `host` ) ,
INDEX ( `date` )
) ENGINE = MYISAM DEFAULT CHARSET=utf8; 
