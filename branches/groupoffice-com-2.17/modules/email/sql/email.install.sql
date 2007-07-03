-- phpMyAdmin SQL Dump
-- version 2.9.1.1-Debian-2ubuntu1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generatie Tijd: 03 Jul 2007 om 13:28
-- Server versie: 5.0.38
-- PHP Versie: 5.2.1
-- 
-- Database: `imfoss`
-- 

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `emAccounts`
-- 

CREATE TABLE `emAccounts` (
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
  `auto_check` enum('0','1') NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `emFilters`
-- 

CREATE TABLE `emFilters` (
  `id` int(11) NOT NULL default '0',
  `account_id` int(11) NOT NULL default '0',
  `field` varchar(20) NOT NULL default '0',
  `keyword` varchar(100) NOT NULL default '0',
  `folder` varchar(100) NOT NULL default '0',
  `priority` int(11) NOT NULL default '0',
  `mark_as_read` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `emFolders`
-- 

CREATE TABLE `emFolders` (
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
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `em_links`
-- 

CREATE TABLE `em_links` (
  `link_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `from` varchar(255) NOT NULL default '',
  `to` text NOT NULL,
  `subject` varchar(255) NOT NULL default '',
  `time` int(11) NOT NULL default '0',
  `path` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`link_id`),
  KEY `account_id` (`user_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `em_settings`
-- 

CREATE TABLE `em_settings` (
  `user_id` int(11) NOT NULL default '0',
  `send_format` varchar(10) NOT NULL default '',
  `add_recievers` int(11) NOT NULL default '0',
  `add_senders` int(11) NOT NULL default '0',
  `request_notification` enum('0','1') NOT NULL default '0',
  `charset` varchar(20) NOT NULL default '',
  `enable_vacation` enum('0','1') NOT NULL default '0',
  `vacation_subject` varchar(100) NOT NULL default '',
  `vacation_text` text NOT NULL,
  `show_preview` enum('0','1') NOT NULL default '1',
  `beep` enum('0','1') NOT NULL default '0',
  `open_popup` enum('0','1') NOT NULL,
  PRIMARY KEY  (`user_id`)
) TYPE=MyISAM;
