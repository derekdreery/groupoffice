-- phpMyAdmin SQL Dump
-- version 2.10.3deb1ubuntu0.2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generatie Tijd: 22 Apr 2008 om 17:45
-- Server versie: 5.0.45
-- PHP Versie: 5.2.3-1ubuntu6.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- Database: `imfoss_nl`
-- 

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `cf_5`
-- 

DROP TABLE IF EXISTS `cf_5`;
CREATE TABLE IF NOT EXISTS `cf_5` (
  `link_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`link_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `go_links_5`
-- 

DROP TABLE IF EXISTS `go_links_5`;
CREATE TABLE IF NOT EXISTS `go_links_5` (
  `id` int(11) NOT NULL,
  `folder_id` int(11) NOT NULL,
  `link_id` int(11) NOT NULL,
  `link_type` int(11) NOT NULL,
  `description` varchar(100) NOT NULL,
  KEY `link_id` (`link_id`,`link_type`),
  KEY `id` (`id`,`folder_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `pm_fees`
-- 

DROP TABLE IF EXISTS `pm_fees`;
CREATE TABLE IF NOT EXISTS `pm_fees` (
  `id` int(11) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `external_value` double NOT NULL default '0',
  `internal_value` double NOT NULL default '0',
  `time` int(11) NOT NULL default '0',
  `acl_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `pm_hours`
-- 

DROP TABLE IF EXISTS `pm_hours`;
CREATE TABLE IF NOT EXISTS `pm_hours` (
  `id` int(11) NOT NULL default '0',
  `project_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `date` int(11) NOT NULL default '0',
  `units` double NOT NULL default '0',
  `comments` text NOT NULL,
  `fee_id` int(11) NOT NULL default '0',
  `ext_fee_value` double NOT NULL default '0',
  `fee_time` int(11) NOT NULL default '0',
  `int_fee_value` double NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `pm_milestones`
-- 

DROP TABLE IF EXISTS `pm_milestones`;
CREATE TABLE IF NOT EXISTS `pm_milestones` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `completion_time` int(11) NOT NULL,
  `due_time` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `project_id` (`project_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `pm_projects`
-- 

DROP TABLE IF EXISTS `pm_projects`;
CREATE TABLE IF NOT EXISTS `pm_projects` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `customer` varchar(50) NOT NULL default '',
  `description` varchar(50) NOT NULL default '',
  `company_id` int(11) NOT NULL default '0',
  `acl_read` int(11) NOT NULL default '0',
  `acl_write` int(11) NOT NULL default '0',
  `acl_book` int(11) NOT NULL default '0',
  `ctime` int(11) NOT NULL default '0',
  `mtime` int(11) NOT NULL default '0',
  `active` enum('0','1') NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
