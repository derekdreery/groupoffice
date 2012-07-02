-- phpMyAdmin SQL Dump
-- version 2.10.3deb1ubuntu0.2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generatie Tijd: 22 Apr 2008 om 17:34
-- Server versie: 5.0.45
-- PHP Versie: 5.2.3-1ubuntu6.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- Database: `imfoss_nl`
-- 

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `cf_4`
-- 

DROP TABLE IF EXISTS `cf_4`;
CREATE TABLE IF NOT EXISTS `cf_4` (
  `link_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`link_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `go_links_4`
-- 

DROP TABLE IF EXISTS `go_links_4`;
CREATE TABLE IF NOT EXISTS `go_links_4` (
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
-- Tabel structuur voor tabel `no_categories`
--

DROP TABLE IF EXISTS `no_categories`;
CREATE TABLE IF NOT EXISTS `no_categories` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `acl_id` int(11) NOT NULL,
  `name` varchar(50) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `no_notes`
--

DROP TABLE IF EXISTS `no_notes`;
CREATE TABLE IF NOT EXISTS `no_notes` (
  `id` int(11) NOT NULL default '0',
  `category_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL default '0',
  `ctime` int(11) NOT NULL default '0',
  `mtime` int(11) NOT NULL default '0',
  `name` varchar(100) default NULL,
  `content` text,
  `files_folder_id` INT NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`),
  KEY `category_id` (`category_id`),
  FULLTEXT KEY `content` (`content`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
