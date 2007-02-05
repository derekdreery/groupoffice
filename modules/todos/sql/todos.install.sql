-- phpMyAdmin SQL Dump
-- version 2.6.0-pl2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generatie Tijd: 10 Apr 2005 om 22:56
-- Server versie: 3.23.58
-- PHP Versie: 4.3.10
-- 
-- Database: `imfoss_nl`
-- 

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `td_reminders`
-- 

DROP TABLE IF EXISTS `td_reminders`;
CREATE TABLE `td_reminders` (
  `user_id` int(11) NOT NULL default '0',
  `todo_id` int(11) NOT NULL default '0',
  `remind_time` int(11) NOT NULL default '0',
  KEY `user_id` (`user_id`),
  KEY `remind_time` (`remind_time`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `td_settings`
-- 

DROP TABLE IF EXISTS `td_settings`;
CREATE TABLE `td_settings` (
  `user_id` int(11) NOT NULL default '0',
  `sort_field` varchar(20) NOT NULL default '',
  `sort_order` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`user_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `td_todos`
-- 

DROP TABLE IF EXISTS `td_todos`;
CREATE TABLE `td_todos` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `contact_id` int(11) NOT NULL default '0',
  `company_id` int(11) NOT NULL default '0',
  `file_path` varchar(255) NOT NULL default '',
  `project_id` int(11) NOT NULL default '0',
  `ctime` int(11) NOT NULL default '0',
  `mtime` int(11) NOT NULL default '0',
  `start_time` int(11) NOT NULL default '0',
  `due_time` int(11) NOT NULL default '0',
  `completion_time` int(11) NOT NULL default '0',
  `reminder` int(11) NOT NULL default '0',
  `remind_style` enum('0','1') NOT NULL default '0',
  `status` tinyint(4) NOT NULL default '0',
  `priority` enum('0','1','2') NOT NULL default '0',
  `res_user_id` int(11) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `description` text NOT NULL,
  `location` varchar(50) NOT NULL default '',
  `background` varchar(6) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`,`res_user_id`),
  KEY `remind_time` (`reminder`)
) TYPE=MyISAM;