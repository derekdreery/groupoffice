-- phpMyAdmin SQL Dump
-- version 2.6.0-pl2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generatie Tijd: 02 Aug 2009 om 18:23
-- Server versie: 5.0.32
-- PHP Versie: 5.2.0-8+etch15
--
-- Database: `servermanager`
--

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `sm_installations`
--

DROP TABLE IF EXISTS `sm_installations`;
CREATE TABLE `sm_installations` (
  `id` int(11) NOT NULL,
  `name` varchar(100) default NULL,
  `ctime` int(11) NOT NULL,
  `mtime` int(11) NOT NULL,
  `max_users` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `sm_new_trials`
--

DROP TABLE IF EXISTS `sm_new_trials`;
CREATE TABLE `sm_new_trials` (
  `name` varchar(50) NOT NULL default '',
  `title` varchar(100) default NULL,
  `first_name` varchar(50) default NULL,
  `last_name` varchar(50) default NULL,
  `email` varchar(100) default NULL,
  `password` varchar(20) default NULL,
  `key` varchar(100) default NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY  (`name`),
  KEY `key` (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `sm_reports`
--

DROP TABLE IF EXISTS `sm_reports`;
CREATE TABLE `sm_reports` (
  `name` varchar(100) NOT NULL default '',
  `count_users` int(11) NOT NULL,
  `install_time` int(11) NOT NULL,
  `lastlogin` int(11) NOT NULL,
  `total_logins` int(11) NOT NULL,
  `database_usage` int(11) NOT NULL,
  `file_storage_usage` int(11) NOT NULL,
  `mailbox_usage` int(11) default NULL,
  `ctime` int(11) NOT NULL,
  `comment` text,
  `features` varchar(255) default NULL,
  `mail_domains` varchar(255) default NULL,
  `admin_email` varchar(100) default NULL,
  `admin_name` varchar(100) default NULL,
  `admin_salutation` varchar(100) default NULL,
  `admin_country` char(2) NOT NULL,
  `date_format` varchar(20) default NULL,
  `thousands_separator` char(1) NOT NULL,
  `decimal_separator` char(1) default NULL,
  `billing` tinyint(1) NOT NULL,
  `professional` tinyint(1) NOT NULL,
  `max_users` int(11) NOT NULL,
  PRIMARY KEY  (`name`),
  KEY `ctime` (`ctime`),
  KEY `professional` (`professional`),
  KEY `billing` (`billing`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
        