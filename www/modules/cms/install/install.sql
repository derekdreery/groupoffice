-- phpMyAdmin SQL Dump
-- version 2.11.3deb1ubuntu1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generatie Tijd: 08 Jul 2008 om 15:35
-- Server versie: 5.0.51
-- PHP Versie: 5.2.4-2ubuntu5.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `imfoss`
--

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `cms_files`
--

DROP TABLE IF EXISTS `cms_files`;
CREATE TABLE IF NOT EXISTS `cms_files` (
  `id` int(11) NOT NULL default '0',
  `folder_id` int(11) NOT NULL default '0',
  `size` int(11) NOT NULL default '0',
  `ctime` int(11) NOT NULL default '0',
  `mtime` int(11) NOT NULL default '0',
  `name` varchar(255) default NULL,
  `content` longtext,
  `auto_meta` enum('0','1') NOT NULL default '1',
  `title` varchar(100) default NULL,
  `description` text,
  `keywords` text,
  `priority` int(11) NOT NULL default '0',
  `option_values` text,
  `plugin` varchar(20) default NULL,
  `type` varchar(100) NOT NULL,
  `files_folder_id` int(11) NOT NULL,
  `show_until` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `folder_id` (`folder_id`),
  KEY `name` (`name`),
  KEY `show_until` (`show_until`),
  FULLTEXT KEY `content` (`content`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `cms_folders`
--

DROP TABLE IF EXISTS `cms_folders`;
CREATE TABLE IF NOT EXISTS `cms_folders` (
  `id` int(11) NOT NULL default '0',
  `parent_id` int(11) NOT NULL default '0',
  `ctime` int(11) NOT NULL default '0',
  `mtime` int(11) NOT NULL default '0',
  `name` char(255) NOT NULL default '',
  `disabled` enum('0','1') NOT NULL default '0',
  `priority` int(11) NOT NULL default '0',
  `acl` int(11) NOT NULL default '0',
  `site_id` int(11) NOT NULL,
  `option_values` text,
  `default_template` varchar(100) NOT NULL default '',
  `type` varchar(100) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `site_id` (`site_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `cms_sites`
--

DROP TABLE IF EXISTS `cms_sites`;
CREATE TABLE IF NOT EXISTS `cms_sites` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `acl_write` int(11) NOT NULL default '0',
  `domain` varchar(100) default NULL,
  `webmaster` varchar(100) default NULL,
  `root_folder_id` int(11) NOT NULL default '0',
  `start_file_id` int(11) NOT NULL default '0',
  `language` varchar(10) default NULL,
  `name` varchar(100) default NULL,
  `template` varchar(50) default NULL,
	`files_folder_id` INT NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
