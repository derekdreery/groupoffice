-- phpMyAdmin SQL Dump
-- version 2.8.2-Debian-0.2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generatie Tijd: 01 Dec 2006 om 14:54
-- Server versie: 5.0.24
-- PHP Versie: 5.1.6
-- 
-- Database: `imfoss_nl`
-- 

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `fs_links`
-- 

DROP TABLE IF EXISTS `fs_links`;
CREATE TABLE `fs_links` (
  `link_id` int(11) NOT NULL default '0',
  `path` varchar(255) NOT NULL default '',
  `status_id` int(11) NOT NULL,
  `ctime` int(11) NOT NULL,
  `mtime` int(11) NOT NULL,
  PRIMARY KEY  (`link_id`),
  KEY `path` (`path`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `fs_settings`
-- 

DROP TABLE IF EXISTS `fs_settings`;
CREATE TABLE `fs_settings` (
  `user_id` int(11) NOT NULL default '0',
  `notify` enum('0','1') NOT NULL default '0',
  `open_properties` enum('0','1') NOT NULL,
  PRIMARY KEY  (`user_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `fs_shares`
-- 

DROP TABLE IF EXISTS `fs_shares`;
CREATE TABLE `fs_shares` (
  `user_id` int(11) NOT NULL default '0',
  `link_id` int(11) default NULL,
  `path` varchar(200) NOT NULL default '',
  `type` varchar(50) NOT NULL default '',
  `acl_read` int(11) NOT NULL default '0',
  `acl_write` int(11) NOT NULL default '0',
  PRIMARY KEY  (`path`),
  KEY `user_id` (`user_id`),
  KEY `type` (`type`),
  KEY `link_id` (`link_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `fs_status_history`
-- 

DROP TABLE IF EXISTS `fs_status_history`;
CREATE TABLE `fs_status_history` (
  `id` int(11) NOT NULL,
  `link_id` int(11) NOT NULL,
  `status_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ctime` int(11) NOT NULL,
  `comments` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `link_id` (`link_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `fs_statuses`
-- 

DROP TABLE IF EXISTS `fs_statuses`;
CREATE TABLE `fs_statuses` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;
