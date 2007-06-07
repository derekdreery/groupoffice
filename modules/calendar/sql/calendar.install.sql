-- phpMyAdmin SQL Dump
-- version 2.8.0.3-Debian-1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generatie Tijd: 29 Aug 2006 om 12:22
-- Server versie: 5.0.22
-- PHP Versie: 5.1.2
-- 
-- Database: `imfoss_nl`
-- 

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `cal_backgrounds`
-- 

DROP TABLE IF EXISTS `cal_backgrounds`;
CREATE TABLE `cal_backgrounds` (
  `id` int(11) NOT NULL,
  `color` char(6) NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `cal_calendar_backgrounds`
-- 

DROP TABLE IF EXISTS `cal_calendar_backgrounds`;
CREATE TABLE `cal_calendar_backgrounds` (
  `id` int(11) NOT NULL,
  `calendar_id` int(11) NOT NULL,
  `background_id` int(11) NOT NULL,
  `weekday` tinyint(4) NOT NULL,
  `start_time` int(11) NOT NULL,
  `end_time` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `calendar_id` (`calendar_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `cal_calendars`
-- 

DROP TABLE IF EXISTS `cal_calendars`;
CREATE TABLE `cal_calendars` (
  `id` int(11) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '1',
  `user_id` int(11) NOT NULL default '0',
  `acl_read` int(11) NOT NULL default '0',
  `acl_write` int(11) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `start_hour` tinyint(4) NOT NULL default '0',
  `end_hour` tinyint(4) NOT NULL default '0',
  `background` varchar(6) NOT NULL default 'FFFFCC',
  `time_interval` int(11) NOT NULL default '1800',
  PRIMARY KEY  (`id`),
  KEY `group_id` (`group_id`),
  KEY `group_id_2` (`group_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `cal_events`
-- 

DROP TABLE IF EXISTS `cal_events`;
CREATE TABLE `cal_events` (
  `id` int(11) NOT NULL default '0',
  `event_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `permissions` tinyint(4) NOT NULL default '0',
  `start_time` int(11) NOT NULL default '0',
  `end_time` int(11) NOT NULL default '0',
  `all_day_event` enum('0','1') NOT NULL default '0',
  `link_id` int(11) NOT NULL default '0',
  `contact_id` int(11) NOT NULL default '0',
  `company_id` int(11) NOT NULL default '0',
  `project_id` int(11) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `description` text NOT NULL,
  `location` varchar(100) NOT NULL default '',
  `background` varchar(7) NOT NULL default '',
  `repeat_type` enum('0','1','2','3','4','5') NOT NULL default '0',
  `repeat_forever` enum('0','1') NOT NULL default '0',
  `repeat_every` tinyint(4) NOT NULL default '0',
  `repeat_end_time` int(11) NOT NULL default '0',
  `mon` enum('0','1') NOT NULL default '0',
  `tue` enum('0','1') NOT NULL default '0',
  `wed` enum('0','1') NOT NULL default '0',
  `thu` enum('0','1') NOT NULL default '0',
  `fri` enum('0','1') NOT NULL default '0',
  `sat` enum('0','1') NOT NULL default '0',
  `sun` enum('0','1') NOT NULL default '0',
  `month_time` enum('0','1','2','3','4','5') NOT NULL default '0',
  `reminder` int(11) NOT NULL default '0',
  `ctime` int(11) NOT NULL default '0',
  `mtime` int(11) NOT NULL default '0',
  `todo` enum('0','1') NOT NULL default '0',
  `completion_time` int(11) NOT NULL default '0',
  `status_id` tinyint(4) NOT NULL default '0',
  `custom_fields` text NOT NULL,
  `timezone` float NOT NULL default '0',
  `DST` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `start_time` (`start_time`),
  KEY `end_time` (`end_time`),
  KEY `repeat_end_time` (`repeat_end_time`),
  KEY `todo` (`todo`),
  KEY `event_id` (`event_id`),
  KEY `event_id_2` (`event_id`),
  KEY `link_id` (`link_id`),
  KEY `link_id_2` (`link_id`),
  KEY `status_id` (`status_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `cal_events_calendars`
-- 

DROP TABLE IF EXISTS `cal_events_calendars`;
CREATE TABLE `cal_events_calendars` (
  `calendar_id` int(11) NOT NULL default '0',
  `event_id` int(11) NOT NULL default '0',
  `sid` char(32) NOT NULL default '',
  KEY `calendar_id` (`calendar_id`,`event_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `cal_exceptions`
-- 

DROP TABLE IF EXISTS `cal_exceptions`;
CREATE TABLE `cal_exceptions` (
  `id` int(11) NOT NULL default '0',
  `event_id` int(11) NOT NULL default '0',
  `time` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `event_id` (`event_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `cal_group_admins`
-- 

DROP TABLE IF EXISTS `cal_group_admins`;
CREATE TABLE `cal_group_admins` (
  `group_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`group_id`,`user_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `cal_groups`
-- 

DROP TABLE IF EXISTS `cal_groups`;
CREATE TABLE `cal_groups` (
  `id` int(11) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `custom_fields` text NOT NULL,
  `acl_write` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `cal_holidays`
-- 

DROP TABLE IF EXISTS `cal_holidays`;
CREATE TABLE `cal_holidays` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `date` int(10) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `region` varchar(4) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `cal_participants`
-- 

DROP TABLE IF EXISTS `cal_participants`;
CREATE TABLE `cal_participants` (
  `id` int(11) NOT NULL default '0',
  `event_id` int(11) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `email` varchar(100) NOT NULL default '',
  `user_id` int(11) NOT NULL default '0',
  `status` enum('0','1','2') NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `id` (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `cal_reminders`
-- 

DROP TABLE IF EXISTS `cal_reminders`;
CREATE TABLE `cal_reminders` (
  `user_id` int(11) NOT NULL default '0',
  `event_id` int(11) NOT NULL default '0',
  `remind_time` int(11) NOT NULL default '0',
  `occurence_time` int(11) NOT NULL default '0',
  `email_sent` enum('0','1') NOT NULL default '0',
  KEY `user_id` (`user_id`),
  KEY `remind_time` (`remind_time`),
  KEY `remind_time_2` (`remind_time`),
  KEY `email_sent` (`email_sent`),
  KEY `email_sent_2` (`email_sent`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `cal_settings`
-- 

DROP TABLE IF EXISTS `cal_settings`;
CREATE TABLE `cal_settings` (
  `user_id` int(11) NOT NULL default '0',
  `default_cal_id` int(11) NOT NULL default '0',
  `default_view_id` int(11) NOT NULL default '0',
  `show_days` tinyint(4) NOT NULL default '0',
  `merged_view` enum('0','1') NOT NULL default '0',
  `reminder` int(11) NOT NULL default '0',
  `refresh_rate` varchar(5) NOT NULL default '',
  `permissions` tinyint(4) NOT NULL default '0',
  `show_todos` enum('0','1') NOT NULL default '0',
  `weekview` enum('5','7') NOT NULL default '7',
  `show_completed` enum('0','1') NOT NULL default '0',
  `view_type` enum('grid','list') NOT NULL default 'grid',
  `check_conflicts` enum('0','1') NOT NULL default '1',
  `email_changes` enum('0','1') NOT NULL default '1',
  `email_reminders` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`user_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `cal_statuses`
-- 

DROP TABLE IF EXISTS `cal_statuses`;
CREATE TABLE `cal_statuses` (
  `id` int(11) NOT NULL default '0',
  `type` varchar(20) NOT NULL default '',
  `name` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `type` (`type`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `cal_views`
-- 

DROP TABLE IF EXISTS `cal_views`;
CREATE TABLE `cal_views` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `start_hour` tinyint(4) NOT NULL default '0',
  `end_hour` tinyint(4) NOT NULL default '0',
  `event_colors_override` enum('0','1') NOT NULL default '0',
  `time_interval` int(11) NOT NULL default '1800',
  `acl_read` int(11) NOT NULL default '0',
  `acl_write` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `cal_views_calendars`
-- 

DROP TABLE IF EXISTS `cal_views_calendars`;
CREATE TABLE `cal_views_calendars` (
  `view_id` int(11) NOT NULL default '0',
  `calendar_id` int(11) NOT NULL default '0',
  `background` char(6) NOT NULL default 'CCFFCC',
  PRIMARY KEY  (`view_id`,`calendar_id`)
) TYPE=MyISAM;

INSERT INTO `cal_statuses` (`id`, `type`, `name`) VALUES (1, 'VEVENT', 'NEEDS-ACTION'),
(2, 'VEVENT', 'ACCEPTED'),
(3, 'VEVENT', 'DECLINED'),
(4, 'VEVENT', 'TENTATIVE'),
(5, 'VEVENT', 'DELEGATED'),
(6, 'VTODO', 'NEEDS-ACTION'),
(7, 'VTODO', 'ACCEPTED'),
(8, 'VTODO', 'DECLINED'),
(9, 'VTODO', 'TENTATIVE'),
(10, 'VTODO', 'DELEGATED'),
(11, 'VTODO', 'COMPLETED'),
(12, 'VTODO', 'IN-PROCESS');
