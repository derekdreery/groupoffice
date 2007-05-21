-- phpMyAdmin SQL Dump
-- version 2.6.4-pl1-Debian-1ubuntu1.1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generatie Tijd: 21 Feb 2006 om 16:25
-- Server versie: 4.0.24
-- PHP Versie: 5.0.5-2ubuntu1.1
-- 
-- Database: `intermesh`
-- 

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `no_notes`
-- 

DROP TABLE IF EXISTS `no_notes`;
CREATE TABLE `no_notes` (
  `id` int(11) NOT NULL default '0',
  `link_id` int(11) default NULL,
  `user_id` int(11) NOT NULL default '0',
  `file_path` varchar(255) NOT NULL default '0',
  `due_date` int(11) NOT NULL default '0',
  `ctime` int(11) NOT NULL default '0',
  `mtime` int(11) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `content` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`),
  KEY `file_path` (`file_path`),
  KEY `link_id` (`link_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `no_settings`
-- 

DROP TABLE IF EXISTS `no_settings`;
CREATE TABLE `no_settings` (
  `user_id` int(11) NOT NULL default '0',
  `sort_field` varchar(20) NOT NULL default '',
  `sort_order` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`user_id`)
) TYPE=MyISAM;
