-- phpMyAdmin SQL Dump
-- version 2.10.3deb1ubuntu0.2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generatie Tijd: 22 Apr 2008 om 17:43
-- Server versie: 5.0.45
-- PHP Versie: 5.2.3-1ubuntu6.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- Database: `imfoss_nl`
-- 

-- --------------------------------------------------------

DROP TABLE IF EXISTS `cf_6`;
CREATE TABLE IF NOT EXISTS `cf_6` (
  `link_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`link_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `fs_shared_cache`;
CREATE TABLE IF NOT EXISTS `fs_shared_cache` (
  `user_id` int(11) NOT NULL,
  `folder_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `path` text NOT NULL,
  PRIMARY KEY (`user_id`,`folder_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Tabel structuur voor tabel `fs_files`
--
DROP TABLE IF EXISTS `fs_files`;
CREATE TABLE IF NOT EXISTS `fs_files` (
  `id` int(11) NOT NULL,
  `folder_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL default '',
  `locked_user_id` int(11) NOT NULL default '0',
  `status_id` int(11) NOT NULL default '0',
  `ctime` int(11) NOT NULL default '0',
  `mtime` int(11) NOT NULL default '0',
  `size` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comments` text,
  `extension` varchar(5) NOT NULL,
  `expire_time` int(11) NOT NULL default '0',
	`random_code` char(11) DEFAULT NULL,
  PRIMARY KEY  (`id`),
  KEY `folder_id` (`folder_id`),
  KEY `name` (`name`),
  KEY `extension` (`extension`),
  KEY `path` (`path`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
        -------------

--
-- Tabel structuur voor tabel `fs_folders`
--


DROP TABLE IF EXISTS `fs_folders`;
CREATE TABLE `fs_folders` (
  `user_id` int(11) NOT NULL default '0',
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `visible` enum('0','1') NOT NULL,
  `acl_id` int(11) NOT NULL default '0',
  `comments` text,
  `thumbs` enum('0','1') NOT NULL default '0',
  `ctime` int(11) NOT NULL,
  `mtime` int(11) NOT NULL,
  `readonly` enum('0','1') NOT NULL,
	`cm_state` TEXT NOT NULL DEFAULT '',
	`apply_state` TINYINT(1) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`id`),
  KEY `name` (`name`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `fs_notifications`
--

DROP TABLE IF EXISTS `fs_notifications`;
CREATE TABLE IF NOT EXISTS `fs_notifications` (
  `folder_id` int(11) NOT NULL,
  `path` varchar(255) NOT NULL default '',
  `user_id` int(11) NOT NULL,
  PRIMARY KEY  (`folder_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `fs_statuses`
--

DROP TABLE IF EXISTS `fs_statuses`;
CREATE TABLE IF NOT EXISTS `fs_statuses` (
  `id` int(11) NOT NULL default '0',
  `name` varchar(50) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `fs_status_history`
--

DROP TABLE IF EXISTS `fs_status_history`;
CREATE TABLE IF NOT EXISTS `fs_status_history` (
  `id` int(11) NOT NULL default '0',
  `link_id` int(11) NOT NULL default '0',
  `status_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `ctime` int(11) NOT NULL default '0',
  `comments` text,
  PRIMARY KEY  (`id`),
  KEY `link_id` (`link_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `fs_templates`
--

DROP TABLE IF EXISTS `fs_templates`;
CREATE TABLE IF NOT EXISTS `fs_templates` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(50) default NULL,
  `acl_id` int(11) NOT NULL,
  
  `content` MEDIUMBLOB NOT NULL,
  `extension` char(4) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `fs_new_files`
--

CREATE TABLE IF NOT EXISTS `fs_new_files` (
  `file_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  KEY `file_id` (`file_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `go_links_6`
-- 

DROP TABLE IF EXISTS `go_links_6`;
CREATE TABLE IF NOT EXISTS `go_links_6` (
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
-- Tabel structuur voor tabel `go_links_17`
--

DROP TABLE IF EXISTS `go_links_17`;
CREATE TABLE IF NOT EXISTS `go_links_17` (
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


DROP TABLE IF EXISTS `cf_17`;
CREATE TABLE IF NOT EXISTS `cf_17` (
  `link_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`link_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;