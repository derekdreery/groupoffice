-- phpMyAdmin SQL Dump
-- version 2.8.2-Debian-0.2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generatie Tijd: 16 Mar 2007 om 16:55
-- Server versie: 5.0.24
-- PHP Versie: 5.1.6
-- 
-- Database: `imfoss_nl`
-- 

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `acl`
-- 
DROP TABLE IF EXISTS `reminders`;
CREATE TABLE `reminders` (
  `id` int(11) NOT NULL,
  `link_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `time` int(11) NOT NULL,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `link_id` (`link_id`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `acl`;
CREATE TABLE `acl` (
  `acl_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`acl_id`,`user_id`,`group_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `acl_items`
-- 

DROP TABLE IF EXISTS `acl_items`;
CREATE TABLE `acl_items` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `description` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `countries`
-- 

DROP TABLE IF EXISTS `countries`;
CREATE TABLE `countries` (
  `id` int(11) NOT NULL default '0',
  `name` varchar(64) NOT NULL default '',
  `iso_code_2` char(2) NOT NULL default '',
  `iso_code_3` char(3) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `db_sequence`
-- 

DROP TABLE IF EXISTS `db_sequence`;
CREATE TABLE `db_sequence` (
  `seq_name` varchar(50) NOT NULL default '',
  `nextid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`seq_name`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `groups`
-- 

DROP TABLE IF EXISTS `groups`;
CREATE TABLE `groups` (
  `id` int(11) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `user_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `links`
-- 

DROP TABLE IF EXISTS `links`;
CREATE TABLE `links` (
  `type1` tinyint(4) NOT NULL default '0',
  `link_id1` int(11) NOT NULL default '0',
  `type2` tinyint(4) NOT NULL default '0',
  `link_id2` int(11) NOT NULL default '0',
  KEY `type1` (`type1`),
  KEY `type2` (`type2`),
  KEY `link_id1` (`link_id1`),
  KEY `link_id2` (`link_id2`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `modules`
-- 

DROP TABLE IF EXISTS `modules`;
CREATE TABLE `modules` (
  `id` varchar(20) NOT NULL default '',
  `version` varchar(5) NOT NULL default '',
  `path` varchar(50) NOT NULL default '',
  `sort_order` int(11) NOT NULL default '0',
  `admin_menu` enum('0','1') NOT NULL default '0',
  `acl_read` int(11) NOT NULL default '0',
  `acl_write` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `se_cache`
-- 

DROP TABLE IF EXISTS `se_cache`;
CREATE TABLE `se_cache` (
  `link_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `table` varchar(50) NOT NULL,
  `id` int(11) NOT NULL,
  `module` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `link_type` int(11) NOT NULL,
  `type` varchar(20) NOT NULL,
  `keywords` text NOT NULL,
  `mtime` int(11) NOT NULL,
  PRIMARY KEY  (`link_id`,`user_id`),
  KEY `name` (`name`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `se_last_sync_times`
-- 

DROP TABLE IF EXISTS `se_last_sync_times`;
CREATE TABLE `se_last_sync_times` (
  `user_id` int(11) NOT NULL,
  `module` varchar(50) NOT NULL,
  `last_sync_time` int(11) NOT NULL,
  PRIMARY KEY  (`user_id`,`module`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `settings`
-- 

DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `user_id` int(11) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `value` text NOT NULL,
  PRIMARY KEY  (`user_id`,`name`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `sync_contacts_maps`
-- 

DROP TABLE IF EXISTS `sync_contacts_maps`;
CREATE TABLE `sync_contacts_maps` (
  `device_id` int(11) NOT NULL default '0',
  `contact_id` int(11) NOT NULL default '0',
  `remote_id` varchar(255) NOT NULL,
  PRIMARY KEY  (`device_id`,`contact_id`,`remote_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `sync_contacts_syncs`
-- 

DROP TABLE IF EXISTS `sync_contacts_syncs`;
CREATE TABLE `sync_contacts_syncs` (
  `device_id` int(11) NOT NULL default '0',
  `local_last_anchor` int(11) NOT NULL default '0',
  `remote_last_anchor` char(32) NOT NULL default '',
  PRIMARY KEY  (`device_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `sync_devices`
-- 

DROP TABLE IF EXISTS `sync_devices`;
CREATE TABLE `sync_devices` (
  `id` int(11) NOT NULL default '0',
  `manufacturer` varchar(50) NOT NULL default '',
  `model` varchar(50) NOT NULL default '',
  `software_version` varchar(50) NOT NULL default '',
  `user_id` int(11) NOT NULL default '0',
  `uri` varchar(128) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `sync_events_maps`
-- 

DROP TABLE IF EXISTS `sync_events_maps`;
CREATE TABLE `sync_events_maps` (
  `device_id` int(11) NOT NULL default '0',
  `event_id` int(11) NOT NULL default '0',
  `remote_id` varchar(255) NOT NULL,
  `todo` enum('0','1') NOT NULL,
  PRIMARY KEY  (`device_id`,`event_id`,`remote_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `sync_events_syncs`
-- 

DROP TABLE IF EXISTS `sync_events_syncs`;
CREATE TABLE `sync_events_syncs` (
  `device_id` int(11) NOT NULL default '0',
  `local_last_anchor` int(11) NOT NULL default '0',
  `remote_last_anchor` char(32) NOT NULL default '',
  PRIMARY KEY  (`device_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `sync_settings`
-- 

DROP TABLE IF EXISTS `sync_settings`;
CREATE TABLE `sync_settings` (
  `user_id` int(11) NOT NULL default '0',
  `addressbook_id` int(11) NOT NULL default '0',
  `calendar_id` int(11) NOT NULL default '0',
  `sync_private` enum('0','1') NOT NULL default '0',
  `server_is_master` enum('0','1') NOT NULL default '0',
  `max_days_old` tinyint(4) NOT NULL,
  `delete_old_events` enum('0','1') NOT NULL,
  PRIMARY KEY  (`user_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `sync_todos_maps`
-- 

DROP TABLE IF EXISTS `sync_todos_maps`;
CREATE TABLE `sync_todos_maps` (
  `device_id` int(11) NOT NULL default '0',
  `event_id` int(11) NOT NULL default '0',
  `remote_id` char(64) NOT NULL default '',
  PRIMARY KEY  (`device_id`,`event_id`,`remote_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `sync_todos_syncs`
-- 

DROP TABLE IF EXISTS `sync_todos_syncs`;
CREATE TABLE `sync_todos_syncs` (
  `device_id` int(11) NOT NULL default '0',
  `local_last_anchor` int(11) NOT NULL default '0',
  `remote_last_anchor` char(32) NOT NULL default '',
  PRIMARY KEY  (`device_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `users`
-- 

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL default '0',
  `username` varchar(50) NOT NULL default '',
  `password` varchar(64) NOT NULL default '',
  `enabled` enum('0','1') NOT NULL default '1',
  `authcode` varchar(20) NOT NULL default '',
  `first_name` varchar(50) NOT NULL default '',
  `middle_name` varchar(50) NOT NULL default '',
  `last_name` varchar(50) NOT NULL default '',
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
  `country` varchar(50) NOT NULL default '',
  `state` varchar(50) NOT NULL default '',
  `city` varchar(50) NOT NULL default '',
  `zip` varchar(10) NOT NULL default '',
  `address` varchar(100) NOT NULL default '',
  `address_no` varchar(10) NOT NULL default '',
  `homepage` varchar(100) NOT NULL default '',
  `work_address` varchar(100) NOT NULL default '',
  `work_address_no` varchar(10) NOT NULL default '',
  `work_zip` varchar(10) NOT NULL default '',
  `work_country` varchar(50) NOT NULL default '',
  `work_state` varchar(50) NOT NULL default '',
  `work_city` varchar(50) NOT NULL default '',
  `work_fax` varchar(20) NOT NULL default '',
  `acl_id` int(11) NOT NULL default '0',
  `date_format` varchar(20) NOT NULL default 'd-m-Y H:i',
  `date_seperator` char(1) NOT NULL default '-',
  `time_format` varchar(10) NOT NULL default '',
  `thousands_seperator` char(1) NOT NULL default '.',
  `decimal_seperator` char(1) NOT NULL default ',',
  `currency` char(3) NOT NULL default '€',
  `mail_client` tinyint(4) NOT NULL default '1',
  `logins` int(11) NOT NULL default '0',
  `lastlogin` int(11) NOT NULL default '0',
  `registration_time` int(11) NOT NULL default '0',
  `max_rows_list` tinyint(4) NOT NULL default '15',
  `timezone` float NOT NULL default '0',
  `DST` enum('0','1','2') NOT NULL default '0',
  `start_module` varchar(50) NOT NULL default '',
  `language` varchar(20) NOT NULL default '',
  `theme` varchar(20) NOT NULL default '',
  `first_weekday` tinyint(4) NOT NULL default '0',
  `sort_name` varchar(20) NOT NULL default 'first_name',
  `use_checkbox_select` enum('0','1') NOT NULL default '0',
  `country_id` int(11) NOT NULL default '0',
  `work_country_id` int(11) NOT NULL default '0',
  `bank` varchar(50) NOT NULL default '',
  `bank_no` varchar(50) NOT NULL default '',
  `link_id` int(11) NOT NULL default '0',
  `mtime` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `link_id` (`link_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `users_groups`
-- 

DROP TABLE IF EXISTS `users_groups`;
CREATE TABLE `users_groups` (
  `group_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`group_id`,`user_id`)
) TYPE=MyISAM;
