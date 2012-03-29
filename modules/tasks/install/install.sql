-- phpMyAdmin SQL Dump
-- version 2.11.3deb1ubuntu1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generatie Tijd: 19 Jun 2008 om 14:30
-- Server versie: 5.0.51
-- PHP Versie: 5.2.4-2ubuntu5.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `imfoss_nl`
--

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `ta_lists`
--
DROP TABLE IF EXISTS `ta_settings`;
CREATE TABLE IF NOT EXISTS `ta_settings` (
  `user_id` int(11) NOT NULL,
  `reminder_days` int(11) NOT NULL,
  `reminder_time`  VARCHAR( 10 ) NOT NULL,
  `remind` enum('0','1') NOT NULL,
	`default_tasklist_id` INT NOT NULL,
  PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ta_lists`;
CREATE TABLE IF NOT EXISTS `ta_lists` (
  `id` int(11) NOT NULL,
  `name` varchar(100) default NULL,
  `user_id` int(11) NOT NULL,
  `acl_id` int(11) NOT NULL,
	`shared_acl` BOOL NOT NULL ,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `ta_tasks`
--

DROP TABLE IF EXISTS `ta_tasks`;
CREATE TABLE IF NOT EXISTS `ta_tasks` (
  `id` int(11) NOT NULL,
  `uuid` varchar(100) NOT NULL,
  `tasklist_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ctime` int(11) NOT NULL,
  `mtime` int(11) NOT NULL,
  `start_time` int(11) NOT NULL,
  `due_time` int(11) NOT NULL,
  `completion_time` int(11) NOT NULL DEFAULT '0',
  `name` varchar(100) DEFAULT NULL,
  `description` text,
  `status` varchar(20) DEFAULT NULL,
  `repeat_end_time` int(11) NOT NULL,
  `reminder` int(11) NOT NULL,
  `rrule` varchar(50) NOT NULL,
  `files_folder_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `priority` int(11) NOT NULL DEFAULT '1',
  `project_name` VARCHAR( 50 ) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `list_id` (`tasklist_id`),
  KEY `rrule` (`rrule`),
  KEY `uuid` (`uuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `go_links_12`;
CREATE TABLE IF NOT EXISTS `go_links_12` (
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

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `cf_12`
--

CREATE TABLE IF NOT EXISTS `cf_12` (
  `link_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`link_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


--
-- Tabelstructuur voor tabel `ta_categories`
--

CREATE TABLE IF NOT EXISTS `ta_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
