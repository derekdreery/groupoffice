-- phpMyAdmin SQL Dump
-- version 2.8.2-Debian-0.2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generatie Tijd: 29 Nov 2006 om 08:48
-- Server versie: 5.0.24
-- PHP Versie: 5.1.6
-- 
-- Database: `go216`
-- 

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `pmFees`
-- 

CREATE TABLE `pmFees` (
  `id` int(11) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `external_value` double NOT NULL default '0',
  `internal_value` double NOT NULL,
  `time` int(11) NOT NULL default '0',
  `acl_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
);

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `pmHours`
-- 

CREATE TABLE `pmHours` (
  `id` int(11) NOT NULL default '0',
  `project_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `start_time` int(11) NOT NULL default '0',
  `end_time` int(11) NOT NULL default '0',
  `break_time` int(11) NOT NULL default '0',
  `units` double NOT NULL,
  `comments` text NOT NULL,
  `fee_id` int(11) NOT NULL default '0',
  `ext_fee_value` double NOT NULL default '0',
  `fee_time` int(11) NOT NULL default '0',
  `int_fee_value` double NOT NULL default '0',
  `event_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
);

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `pmProjects`
-- 

CREATE TABLE `pmProjects` (
  `id` int(11) NOT NULL default '0',
  `link_id` int(11) default NULL,
  `user_id` int(11) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `customer` varchar(50) NOT NULL,
  `description` varchar(50) NOT NULL default '',
  `contact_id` int(11) NOT NULL default '0',
  `company_id` int(11) NOT NULL default '0',
  `project_id` int(11) NOT NULL default '0',
  `res_user_id` int(11) NOT NULL default '0',
  `acl_read` int(11) NOT NULL default '0',
  `acl_write` int(11) NOT NULL default '0',
  `acl_book` int(11) NOT NULL,
  `comments` text NOT NULL,
  `ctime` int(11) NOT NULL default '0',
  `mtime` int(11) NOT NULL default '0',
  `start_date` int(11) NOT NULL default '0',
  `end_date` int(11) NOT NULL default '0',
  `status` tinyint(10) NOT NULL default '0',
  `probability` tinyint(4) NOT NULL default '0',
  `budget` double NOT NULL default '0',
  `billed` double NOT NULL,
  `unit_value` int(11) NOT NULL,
  `calendar_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `contact_id` (`contact_id`),
  KEY `user_id` (`user_id`),
  KEY `link_id` (`link_id`)
);

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `pmStatuses`
-- 

CREATE TABLE `pmStatuses` (
  `id` int(11) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`id`)
);

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `pmTimers`
-- 

CREATE TABLE `pmTimers` (
  `user_id` int(11) NOT NULL default '0',
  `start_time` int(11) NOT NULL default '0',
  `project_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`user_id`)
);

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `pm_settings`
-- 

CREATE TABLE `pm_settings` (
  `user_id` int(11) NOT NULL default '0',
  `show_projects` tinyint(4) NOT NULL default '0',
  `fee_id` int(11) NOT NULL,
  PRIMARY KEY  (`user_id`)
);

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `pm_template_events`
-- 

CREATE TABLE `pm_template_events` (
  `id` int(11) NOT NULL default '0',
  `template_id` int(11) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `description` text NOT NULL,
  `time_offset` int(11) NOT NULL default '0',
  `duration` int(11) NOT NULL default '0',
  `todo` enum('0','1') NOT NULL default '0',
  `reminder` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `template_id` (`template_id`)
);

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `pm_templates`
-- 

CREATE TABLE `pm_templates` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `acl_read` int(11) NOT NULL default '0',
  `acl_write` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
);




INSERT INTO `pmStatuses` VALUES (1, 'Offer');
INSERT INTO `pmStatuses` VALUES (2, 'Ongoing');
INSERT INTO `pmStatuses` VALUES (3, 'Waiting');
INSERT INTO `pmStatuses` VALUES (4, 'Done');
INSERT INTO `pmStatuses` VALUES (5, 'Billed');
