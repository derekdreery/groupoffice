-- phpMyAdmin SQL Dump
-- version 2.11.3deb1ubuntu1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generatie Tijd: 05 Mei 2008 om 14:21
-- Server versie: 5.0.51
-- PHP Versie: 5.2.4-2ubuntu5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `go3test3`
--

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `em_accounts`
--

DROP TABLE IF EXISTS `em_accounts`;
CREATE TABLE IF NOT EXISTS `em_accounts` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `type` varchar(4) NOT NULL default '',
  `host` varchar(100) NOT NULL default '',
  `port` int(11) NOT NULL default '0',
  `use_ssl` enum('0','1') NOT NULL default '0',
  `novalidate_cert` enum('0','1') NOT NULL default '0',
  `username` varchar(50) NOT NULL default '',
  `password` varchar(64) NOT NULL default '',
  `name` varchar(100) NOT NULL default '',
  `email` varchar(100) NOT NULL default '',
  `signature` text NOT NULL,
  `standard` tinyint(4) NOT NULL default '0',
  `mbroot` varchar(30) NOT NULL default '',
  `sent` varchar(100) NOT NULL default '',
  `drafts` varchar(100) NOT NULL default '',
  `trash` varchar(100) NOT NULL default '',
  `spam` varchar(100) NOT NULL default '',
  `spamtag` varchar(20) NOT NULL default '',
  `examine_headers` enum('0','1') NOT NULL default '0',
  `enable_vacation` enum('0','1') NOT NULL default '0',
  `vacation_subject` varchar(100) NOT NULL default '',
  `vacation_text` text NOT NULL,
  `auto_check` enum('0','1') NOT NULL default '0',
  `forward_enabled` enum('0','1') NOT NULL,
  `forward_to` varchar(255) NOT NULL,
  `forward_local_copy` enum('0','1') NOT NULL,
  `smtp_host` varchar(100) NOT NULL,
  `smtp_port` int(11) NOT NULL,
  `smtp_encryption` tinyint(4) NOT NULL,
  `smtp_username` varchar(50) NOT NULL,
  `smtp_password` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `em_filters`
--

DROP TABLE IF EXISTS `em_filters`;
CREATE TABLE IF NOT EXISTS `em_filters` (
  `id` int(11) NOT NULL default '0',
  `account_id` int(11) NOT NULL default '0',
  `field` varchar(20) NOT NULL default '0',
  `keyword` varchar(100) NOT NULL default '0',
  `folder` varchar(100) NOT NULL default '0',
  `priority` int(11) NOT NULL default '0',
  `mark_as_read` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `em_folders`
--

DROP TABLE IF EXISTS `em_folders`;
CREATE TABLE IF NOT EXISTS `em_folders` (
  `id` int(11) NOT NULL default '0',
  `account_id` int(11) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `subscribed` enum('0','1') NOT NULL default '0',
  `parent_id` int(11) NOT NULL default '0',
  `delimiter` char(1) NOT NULL default '',
  `attributes` int(11) NOT NULL default '0',
  `sort_order` tinyint(4) NOT NULL default '0',
  `msgcount` int(11) NOT NULL default '0',
  `unseen` int(11) NOT NULL default '0',
  `auto_check` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `account_id` (`account_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `em_links`
--

DROP TABLE IF EXISTS `em_links`;
CREATE TABLE IF NOT EXISTS `em_links` (
  `link_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `from` varchar(255) NOT NULL default '',
  `to` text NOT NULL,
  `subject` varchar(255) NOT NULL default '',
  `time` int(11) NOT NULL default '0',
  `path` varchar(255) NOT NULL default '',
  `ctime` int(11) NOT NULL,
  PRIMARY KEY  (`link_id`),
  KEY `account_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `go_links_9`
-- 

DROP TABLE IF EXISTS `go_links_9`;
CREATE TABLE IF NOT EXISTS `go_links_9` (
  `id` int(11) NOT NULL,
  `folder_id` int(11) NOT NULL,
  `link_id` int(11) NOT NULL,
  `link_type` int(11) NOT NULL,
  `description` varchar(100) NOT NULL,
  `ctime` int(11) NOT NULL,
  KEY `link_id` (`link_id`,`link_type`),
  KEY `id` (`id`,`folder_id`),
  KEY `ctime` (`ctime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
