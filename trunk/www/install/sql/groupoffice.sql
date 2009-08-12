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
  `name` varchar(100) default NULL,
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
  `name` varchar(50) NOT NULL default '',
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
  `password` varchar(64) default NULL,
  `auth_md5_pass` varchar(100) default NULL,
  `enabled` enum('0','1') NOT NULL default '1',
  `first_name` varchar(50) default NULL,
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
  `home_phone` varchar(20) default NULL,
  `work_phone` varchar(20) default NULL,
  `fax` varchar(20) default NULL,
  `cellular` varchar(20) default NULL,
  `country` char(2) NOT NULL,
  `state` varchar(50) default NULL,
  `city` varchar(50) default NULL,
  `zip` varchar(10) default NULL,
  `address` varchar(100) default NULL,
  `address_no` varchar(10) default NULL,
  `homepage` varchar(100) default NULL,
  `work_address` varchar(100) default NULL,
  `work_address_no` varchar(10) default NULL,
  `work_zip` varchar(10) default NULL,
  `work_country` char(2) NOT NULL,
  `work_state` varchar(50) default NULL,
  `work_city` varchar(50) default NULL,
  `work_fax` varchar(20) default NULL,
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
  `list_separator` char(3) NOT NULL default ';',
  `text_separator` char(3) NOT NULL default '"',
  `files_folder_id` INT NOT NULL,
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
  `format` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `go_address_format`
--

INSERT INTO `go_address_format` (`id`, `format`) VALUES
(1, '{address} {address_no}\r\n{zip} {city}\r\n{state}\r\n{country}'),
(2, '{address_no} {address}\r\n{city}, {state} {zip}\r\n{country}'),
(3, '{address}, {address_no}\r\n{zip} {city}\r\n{state} {country}'),
(4, '{address_no} {address}\r\n{city} {zip}\r\n{state} {country}');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `go_iso_address_format`
--

DROP TABLE IF EXISTS `go_iso_address_format`;
CREATE TABLE IF NOT EXISTS `go_iso_address_format` (
  `iso` varchar(2) NOT NULL,
  `address_format_id` int(11) NOT NULL,
  PRIMARY KEY  (`address_format_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `go_iso_address_format`
--

INSERT INTO `go_iso_address_format` (`iso`, `address_format_id`) VALUES
('NL', 1),
('US', 2),
('ES', 3),
('SG', 4);