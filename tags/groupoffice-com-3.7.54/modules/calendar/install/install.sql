-- phpMyAdmin SQL Dump
-- version 2.6.0-pl2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generatie Tijd: 26 Jun 2008 om 14:46
-- Server versie: 5.0.32
-- PHP Versie: 5.2.0-8+etch11
--
-- Database: `fossit`
--

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `cal_calendars`
--

DROP TABLE IF EXISTS `cf_1`;
CREATE TABLE IF NOT EXISTS `cf_1` (
  `link_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`link_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `cal_settings`;
CREATE TABLE `cal_settings` (
`user_id` INT NOT NULL ,
`reminder` INT NOT NULL ,
`background` CHAR( 6 ) NOT NULL ,
`calendar_id` int(11) NOT NULL,
PRIMARY KEY ( `user_id` ),
  KEY `calendar_id` (`calendar_id`)
) ENGINE = MYISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cal_calendars`;
CREATE TABLE IF NOT EXISTS `cal_calendars` (
  `id` int(11) NOT NULL DEFAULT '0',
  `group_id` int(11) NOT NULL DEFAULT '1',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `acl_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(100) DEFAULT NULL,
  `start_hour` tinyint(4) NOT NULL DEFAULT '0',
  `end_hour` tinyint(4) NOT NULL DEFAULT '0',
  `background` varchar(6) DEFAULT NULL,
  `time_interval` int(11) NOT NULL DEFAULT '1800',
  `public` enum('0','1') NOT NULL,
  `shared_acl` tinyint(1) NOT NULL,
  `show_bdays` tinyint(1) NOT NULL,
  `comment` varchar(255) NOT NULL,
  `project_id` int(11) NOT NULL,
  `tasklist_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  KEY `group_id` (`group_id`),
  KEY `project_id` (`project_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `cal_events`
--

DROP TABLE IF EXISTS `cal_events`;
CREATE TABLE IF NOT EXISTS `cal_events` (
  `id` int(11) NOT NULL DEFAULT '0',
  `uuid` varchar(200) NOT NULL,
  `calendar_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `start_time` int(11) NOT NULL DEFAULT '0',
  `end_time` int(11) NOT NULL DEFAULT '0',
  `all_day_event` enum('0','1') NOT NULL DEFAULT '0',
  `name` varchar(100) DEFAULT NULL,
  `description` text,
  `location` varchar(100) DEFAULT NULL,
  `repeat_end_time` int(11) NOT NULL DEFAULT '0',
  `reminder` int(11) NOT NULL DEFAULT '0',
  `ctime` int(11) NOT NULL DEFAULT '0',
  `mtime` int(11) NOT NULL DEFAULT '0',
  `busy` enum('0','1') NOT NULL DEFAULT '0',
  `status` varchar(20) DEFAULT NULL,
  `resource_event_id` int(11) NOT NULL,
  `private` enum('0','1') NOT NULL,
  `rrule` varchar(100) NOT NULL,
  `background` char(6) NOT NULL DEFAULT 'ebf1e2',
  `files_folder_id` int(11) NOT NULL,
  `read_only` tinyint(1) NOT NULL DEFAULT '0',
  `category_id` int(11) NOT NULL,
  `sequence` int(11) NOT NULL DEFAULT '0',
  `exception_for_event_id` int(11) NOT NULL,
  `recurrence_id` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `start_time` (`start_time`),
  KEY `end_time` (`end_time`),
  KEY `repeat_end_time` (`repeat_end_time`),
  KEY `rrule` (`rrule`),
  KEY `calendar_id` (`calendar_id`),
  KEY `busy` (`busy`),
  KEY `category_id` (`category_id`),
  KEY `uuid` (`uuid`),
  KEY `resource_event_id` (`resource_event_id`),
  KEY `recurrence_id` (`recurrence_id`),
  KEY `exception_for_event_id` (`exception_for_event_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `cal_exceptions`
--

DROP TABLE IF EXISTS `cal_exceptions`;
CREATE TABLE IF NOT EXISTS `cal_exceptions` (
  `id` int(11) NOT NULL default '0',
  `event_id` int(11) NOT NULL default '0',
  `time` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `event_id` (`event_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `cal_participants`
--

DROP TABLE IF EXISTS `cal_participants`;
CREATE TABLE IF NOT EXISTS `cal_participants` (
  `id` int(11) NOT NULL default '0',
  `event_id` int(11) NOT NULL default '0',
  `name` varchar(50) default NULL,
  `email` varchar(100) default NULL,
  `user_id` int(11) NOT NULL default '0',
  `status` enum('0','1','2','3') NOT NULL default '0',
  `last_modified` varchar(20) default NULL,
  `is_organizer` tinyint(1) NOT NULL default '0',
  `role` varchar(100) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `cal_views`
--

DROP TABLE IF EXISTS `cal_views`;
CREATE TABLE IF NOT EXISTS `cal_views` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `name` varchar(50) default NULL,
  `start_hour` tinyint(4) NOT NULL default '0',
  `end_hour` tinyint(4) NOT NULL default '0',
  `event_colors_override` enum('0','1') NOT NULL default '0',
  `time_interval` int(11) NOT NULL default '1800',
  `acl_id` int(11) NOT NULL default '0',
	`merge` tinyint(1) NOT NULL default '0',
	`owncolor` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `cal_views_calendars`
--

DROP TABLE IF EXISTS `cal_views_calendars`;
CREATE TABLE IF NOT EXISTS `cal_views_calendars` (
  `view_id` int(11) NOT NULL default '0',
  `calendar_id` int(11) NOT NULL default '0',
  `background` char(6) NOT NULL default 'CCFFCC',
  PRIMARY KEY  (`view_id`,`calendar_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


--
-- Tabel structuur voor tabel `go_links_1`
--

DROP TABLE IF EXISTS `go_links_1`;
CREATE TABLE IF NOT EXISTS `go_links_1` (
  `id` int(11) NOT NULL,
  `folder_id` int(11) NOT NULL,
  `link_id` int(11) NOT NULL,
  `link_type` int(11) NOT NULL,
  `description` varchar(100) NULL,
  `ctime` int(11) NOT NULL,
  KEY `link_id` (`link_id`,`link_type`),
  KEY `id` (`id`,`folder_id`),
  KEY `ctime` (`ctime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Tabelstructuur voor tabel `cal_groups`
--

DROP TABLE IF EXISTS `cal_groups`;
CREATE TABLE IF NOT EXISTS `cal_groups` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `name` varchar(50) default NULL,
  `fields` varchar(255) NOT NULL,
  `show_not_as_busy` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cal_group_admins`;
CREATE TABLE IF NOT EXISTS `cal_group_admins` (
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`group_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Tabelstructuur voor tabel `cal_visible_tasklists`
--

DROP TABLE IF EXISTS `cal_visible_tasklists`;
CREATE TABLE IF NOT EXISTS `cal_visible_tasklists` (
  `calendar_id` int(11) NOT NULL,
  `tasklist_id` int(11) NOT NULL,
  PRIMARY KEY (`calendar_id`,`tasklist_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Tabelstructuur voor tabel `cal_categories`
--

DROP TABLE IF EXISTS `cal_categories`;
CREATE TABLE IF NOT EXISTS `cal_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `color` char(6) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Tabelstructuur voor tabel `cal_events_declined`
--

CREATE TABLE IF NOT EXISTS `cal_events_declined` (
  `uid` varchar(200) NOT NULL,
  `email` varchar(100) NOT NULL,
  PRIMARY KEY (`uid`,`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Tabelstructuur voor tabel `cal_calendar_user_colors`
--
 
CREATE TABLE IF NOT EXISTS `cal_calendar_user_colors` (
  `user_id` int(11) NOT NULL,
  `calendar_id` int(11) NOT NULL,
  `color` varchar(6) NOT NULL,
  PRIMARY KEY (`user_id`,`calendar_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
