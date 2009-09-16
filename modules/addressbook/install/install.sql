-- phpMyAdmin SQL Dump
-- version 2.11.3deb1ubuntu1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generatie Tijd: 05 Mei 2008 om 14:04
-- Server versie: 5.0.51
-- PHP Versie: 5.2.4-2ubuntu5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `imfoss_nl`
--

-- --------------------------------------------------------




----
-- Tabel structuur voor tabel `ab_addressbooks`
--

DROP TABLE IF EXISTS `ab_addressbooks`;
CREATE TABLE IF NOT EXISTS `ab_addressbooks` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `name` varchar(50) default NULL,
  `acl_read` int(11) NOT NULL default '0',
  `acl_write` int(11) NOT NULL default '0',
  `shared_acl` tinyint(1) NOT NULL,
  `default_iso_address_format` varchar(2) NOT NULL,
  `default_salutation` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `ab_companies`
--

DROP TABLE IF EXISTS `ab_companies`;
CREATE TABLE IF NOT EXISTS `ab_companies` (
  `id` int(11) NOT NULL default '0',
  `link_id` int(11) default NULL,
  `user_id` int(11) NOT NULL default '0',
  `addressbook_id` int(11) NOT NULL default '0',
  `name` varchar(100) default NULL,
  `address` varchar(100) default NULL,
  `address_no` varchar(10) default NULL,
  `zip` varchar(10) default NULL,
  `city` varchar(50) default NULL,
  `state` varchar(50) default NULL,
  `country` varchar(50) default NULL,
  `iso_address_format` varchar(2) NOT NULL,
  `post_address` varchar(100) default NULL,
  `post_address_no` varchar(10) default NULL,
  `post_city` varchar(50) default NULL,
  `post_state` varchar(50) default NULL,
  `post_country` varchar(50) default NULL,
  `post_zip` varchar(10) default NULL,
  `post_iso_address_format` varchar(2) NOT NULL,
  `phone` varchar(30) default NULL,
  `fax` varchar(30) default NULL,
  `email` varchar(75) default NULL,
  `homepage` varchar(100) default NULL,
  `comment` text,
  `bank_no` varchar(50) default NULL,
  `vat_no` varchar(30) default NULL,
  `ctime` int(11) NOT NULL default '0',
  `mtime` int(11) NOT NULL default '0',
  `email_allowed` enum('0','1') NOT NULL,
  `files_folder_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `addressbook_id` (`addressbook_id`),
  KEY `addressbook_id_2` (`addressbook_id`),
  KEY `link_id` (`link_id`),
  KEY `link_id_2` (`link_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `ab_contacts`
--

DROP TABLE IF EXISTS `ab_contacts`;
CREATE TABLE IF NOT EXISTS `ab_contacts` (
  `id` int(11) NOT NULL default '0',
  `link_id` int(11) default NULL,
  `user_id` int(11) NOT NULL default '0',
  `addressbook_id` int(11) NOT NULL default '0',
  `source_id` int(11) NOT NULL default '0',
  `first_name` varchar(50) default NULL,
  `middle_name` varchar(50) default NULL,
  `last_name` varchar(50) default NULL,
  `initials` varchar(10) default NULL,
  `title` varchar(10) default NULL,
  `sex` enum('M','F') NOT NULL default 'M',
  `birthday` date default NULL,
  `email` varchar(100) default NULL,
  `email2` varchar(100) default NULL,
  `email3` varchar(100) default NULL,
  `company_id` int(11) NOT NULL default '0',
  `department` varchar(50) default NULL,
  `function` varchar(50) default NULL,
  `home_phone` varchar(30) default NULL,
  `work_phone` varchar(30) default NULL,
  `fax` varchar(30) default NULL,
  `work_fax` varchar(30) default NULL,
  `cellular` varchar(30) default NULL,
  `country` varchar(50) default NULL,
  `state` varchar(50) default NULL,
  `city` varchar(50) default NULL,
  `zip` varchar(10) default NULL,
  `address` varchar(100) default NULL,
  `address_no` varchar(10) default NULL,
  `comment` text,
  `iso_address_format` varchar(2) NOT NULL,
  `color` varchar(6) default NULL,
  `sid` varchar(32) default NULL,
  `ctime` int(11) NOT NULL default '0',
  `mtime` int(11) NOT NULL default '0',
  `salutation` varchar(50) default NULL,
  `email_allowed` enum('0','1') NOT NULL,
  `files_folder_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`addressbook_id`),
  KEY `link_id` (`link_id`),
  KEY `link_id_2` (`link_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------




-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `cf_2`
-- 

DROP TABLE IF EXISTS `cf_2`;
CREATE TABLE IF NOT EXISTS `cf_2` (
  `link_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`link_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `cf_3`
-- 

DROP TABLE IF EXISTS `cf_3`;
CREATE TABLE IF NOT EXISTS `cf_3` (
  `link_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`link_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `go_links_2`
-- 

DROP TABLE IF EXISTS `go_links_2`;
CREATE TABLE IF NOT EXISTS `go_links_2` (
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
-- Tabel structuur voor tabel `go_links_3`
-- 

DROP TABLE IF EXISTS `go_links_3`;
CREATE TABLE IF NOT EXISTS `go_links_3` (
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

