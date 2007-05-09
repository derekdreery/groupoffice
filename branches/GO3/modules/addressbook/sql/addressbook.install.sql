-- phpMyAdmin SQL Dump
-- version 2.6.4-pl1-Debian-1ubuntu1.1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generatie Tijd: 07 Mei 2006 om 21:12
-- Server versie: 4.0.24
-- PHP Versie: 4.4.0-3ubuntu2
-- 
-- Database: `intermesh`
-- 

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `ab_addressbooks`
-- 

DROP TABLE IF EXISTS `ab_addressbooks`;
CREATE TABLE `ab_addressbooks` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `acl_read` int(11) NOT NULL default '0',
  `acl_write` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `ab_companies`
-- 

DROP TABLE IF EXISTS `ab_companies`;
CREATE TABLE `ab_companies` (
  `id` int(11) NOT NULL default '0',
  `link_id` int(11) default NULL,
  `user_id` int(11) NOT NULL default '0',
  `addressbook_id` int(11) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `address` varchar(100) NOT NULL default '',
  `address_no` varchar(10) NOT NULL default '',
  `zip` varchar(10) NOT NULL default '',
  `city` varchar(50) NOT NULL default '',
  `state` varchar(50) NOT NULL default '',
  `country` varchar(50) NOT NULL default '',
  `post_address` varchar(100) NOT NULL default '',
  `post_address_no` varchar(10) NOT NULL default '',
  `post_city` varchar(50) NOT NULL default '',
  `post_state` varchar(50) NOT NULL default '',
  `post_country` varchar(50) NOT NULL default '',
  `post_zip` varchar(10) NOT NULL default '',
  `phone` varchar(20) NOT NULL default '',
  `fax` varchar(20) NOT NULL default '',
  `email` varchar(75) NOT NULL default '',
  `homepage` varchar(100) NOT NULL default '',
  `bank_no` varchar(20) NOT NULL default '',
  `vat_no` varchar(30) NOT NULL default '',
  `ctime` int(11) NOT NULL default '0',
  `mtime` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `addressbook_id` (`addressbook_id`),
  KEY `link_id` (`link_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `ab_contacts`
-- 

DROP TABLE IF EXISTS `ab_contacts`;
CREATE TABLE `ab_contacts` (
  `id` int(11) NOT NULL default '0',
  `link_id` int(11) default NULL,
  `user_id` int(11) NOT NULL default '0',
  `addressbook_id` int(11) NOT NULL default '0',
  `source_id` int(11) NOT NULL default '0',
  `first_name` varchar(50) NOT NULL default '',
  `middle_name` varchar(50) NOT NULL default '',
  `last_name` varchar(50) NOT NULL default '',
  `initials` varchar(10) NOT NULL default '',
  `title` varchar(10) NOT NULL default '',
  `sex` enum('M','F') NOT NULL default 'M',
  `birthday` date NOT NULL default '0000-00-00',
  `email` varchar(100) NOT NULL default '',
  `email2` varchar(100) NOT NULL default '',
  `email3` varchar(100) NOT NULL default '',
  `company_id` int(11) NOT NULL default '0',
  `department` varchar(50) NOT NULL default '',
  `function` varchar(50) NOT NULL default '',
  `home_phone` varchar(20) NOT NULL default '',
  `work_phone` varchar(20) NOT NULL default '',
  `fax` varchar(20) NOT NULL default '',
  `work_fax` varchar(20) NOT NULL default '',
  `cellular` varchar(20) NOT NULL default '',
  `country` varchar(50) NOT NULL default '',
  `state` varchar(50) NOT NULL default '',
  `city` varchar(50) NOT NULL default '',
  `zip` varchar(10) NOT NULL default '',
  `address` varchar(100) NOT NULL default '',
  `address_no` varchar(10) NOT NULL default '',
  `comment` varchar(50) NOT NULL default '',
  `color` varchar(6) NOT NULL default '',
  `sid` varchar(32) NOT NULL default '',
  `ctime` int(11) NOT NULL default '0',
  `mtime` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `user_id` (`addressbook_id`),
  KEY `link_id` (`link_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `ab_settings`
-- 

DROP TABLE IF EXISTS `ab_settings`;
CREATE TABLE `ab_settings` (
  `user_id` int(11) NOT NULL default '0',
  `search_type` varchar(10) NOT NULL default '',
  `search_contacts_field` varchar(30) NOT NULL default '',
  `search_companies_field` varchar(30) NOT NULL default '',
  `search_users_field` varchar(30) NOT NULL default '',
  `search_addressbook_id` int(11) NOT NULL default '0',
  `addressbook_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`user_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `ab_zipcodes`
-- 

DROP TABLE IF EXISTS `ab_zipcodes`;
CREATE TABLE `ab_zipcodes` (
  `id` int(11) NOT NULL default '0',
  `zip` varchar(10) NOT NULL default '',
  `state` varchar(100) NOT NULL default '',
  `city` varchar(100) NOT NULL default '',
  `street` varchar(100) NOT NULL default '',
  `country` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `zip` (`zip`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `tp_mailing_companies`
-- 

DROP TABLE IF EXISTS `tp_mailing_companies`;
CREATE TABLE `tp_mailing_companies` (
  `group_id` int(11) NOT NULL default '0',
  `company_id` int(11) NOT NULL default '0',
  KEY `group_id` (`group_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `tp_mailing_contacts`
-- 

DROP TABLE IF EXISTS `tp_mailing_contacts`;
CREATE TABLE `tp_mailing_contacts` (
  `group_id` int(11) NOT NULL default '0',
  `contact_id` int(11) NOT NULL default '0',
  KEY `group_id` (`group_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `tp_mailing_groups`
-- 

DROP TABLE IF EXISTS `tp_mailing_groups`;
CREATE TABLE `tp_mailing_groups` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `acl_read` int(11) NOT NULL default '0',
  `acl_write` int(11) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `tp_mailing_users`
-- 

DROP TABLE IF EXISTS `tp_mailing_users`;
CREATE TABLE `tp_mailing_users` (
  `group_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  KEY `group_id` (`group_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `tp_templates`
-- 

DROP TABLE IF EXISTS `tp_templates`;
CREATE TABLE `tp_templates` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `type` tinyint(4) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `acl_read` int(11) NOT NULL default '0',
  `acl_write` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `tp_templates_content`
-- 

DROP TABLE IF EXISTS `tp_templates_content`;
CREATE TABLE `tp_templates_content` (
  `id` int(11) NOT NULL default '0',
  `content` longblob NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;
