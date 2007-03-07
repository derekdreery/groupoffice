-- phpMyAdmin SQL Dump
-- version 2.8.2-Debian-0.2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generatie Tijd: 07 Mar 2007 om 17:16
-- Server versie: 5.0.24
-- PHP Versie: 5.1.6
-- 
-- Database: `imfoss_nl`
-- 

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `cms_comments`
-- 

DROP TABLE IF EXISTS `cms_comments`;
CREATE TABLE `cms_comments` (
  `id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `comments` text NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `file_id` (`file_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `cms_files`
-- 

DROP TABLE IF EXISTS `cms_files`;
CREATE TABLE `cms_files` (
  `id` int(11) NOT NULL default '0',
  `folder_id` int(11) NOT NULL default '0',
  `extension` varchar(10) NOT NULL default '',
  `size` int(11) NOT NULL default '0',
  `ctime` int(11) NOT NULL default '0',
  `mtime` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `content` longtext,
  `auto_meta` enum('0','1') NOT NULL default '1',
  `title` varchar(100) NOT NULL default '',
  `description` text NOT NULL,
  `keywords` text NOT NULL,
  `priority` int(11) NOT NULL default '0',
  `hot_item` enum('0','1') default NULL,
  `hot_item_text` text NOT NULL,
  `template_item_id` int(11) NOT NULL default '0',
  `acl` int(11) NOT NULL default '0',
  `registered_comments` enum('0','1') NOT NULL default '0',
  `unregistered_comments` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `folder_id` (`folder_id`),
  KEY `name` (`name`),
  FULLTEXT KEY `content` (`content`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `cms_folders`
-- 

DROP TABLE IF EXISTS `cms_folders`;
CREATE TABLE `cms_folders` (
  `id` int(11) NOT NULL default '0',
  `parent_id` int(11) NOT NULL default '0',
  `ctime` int(11) NOT NULL default '0',
  `mtime` int(11) NOT NULL default '0',
  `name` char(255) NOT NULL default '',
  `disabled` enum('0','1') NOT NULL default '0',
  `priority` int(11) NOT NULL default '0',
  `multipage` enum('0','1') default NULL,
  `template_item_id` int(11) NOT NULL default '0',
  `acl` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `parent_id` (`parent_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `cms_languages`
-- 

DROP TABLE IF EXISTS `cms_languages`;
CREATE TABLE `cms_languages` (
  `id` int(11) NOT NULL default '0',
  `site_id` int(11) NOT NULL default '0',
  `template_item_id` int(11) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `title` varchar(50) NOT NULL default '',
  `description` text NOT NULL,
  `keywords` text NOT NULL,
  `image_url` varchar(255) NOT NULL default '',
  `sort_order` int(11) NOT NULL default '0',
  `root_folder_id` int(11) NOT NULL default '0',
  `language_code` char(2) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `site_id` (`site_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `cms_settings`
-- 

DROP TABLE IF EXISTS `cms_settings`;
CREATE TABLE `cms_settings` (
  `user_id` int(11) NOT NULL default '0',
  `sort_field` varchar(20) NOT NULL default '',
  `sort_order` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`user_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `cms_sites`
-- 

DROP TABLE IF EXISTS `cms_sites`;
CREATE TABLE `cms_sites` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `acl_write` int(11) NOT NULL default '0',
  `allow_properties` enum('0','1') NOT NULL default '0',
  `domain` varchar(100) NOT NULL default '',
  `webmaster` varchar(100) NOT NULL default '',
  `publish_style` enum('0','1','2') NOT NULL default '0',
  `publish_path` varchar(100) NOT NULL default '',
  `template_id` int(11) NOT NULL default '0',
  `root_folder_id` int(11) NOT NULL default '0',
  `start_file_id` int(11) NOT NULL default '0',
  `language` char(10) NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `cms_template_files`
-- 

DROP TABLE IF EXISTS `cms_template_files`;
CREATE TABLE `cms_template_files` (
  `id` int(11) NOT NULL default '0',
  `template_id` int(11) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `extension` varchar(10) NOT NULL default '',
  `size` int(11) NOT NULL default '0',
  `mtime` int(11) NOT NULL default '0',
  `content` mediumblob NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `cms_template_items`
-- 

DROP TABLE IF EXISTS `cms_template_items`;
CREATE TABLE `cms_template_items` (
  `id` int(11) NOT NULL default '0',
  `template_id` int(11) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `content` text NOT NULL,
  `page` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `template_id` (`template_id`),
  KEY `page` (`page`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `cms_templates`
-- 

DROP TABLE IF EXISTS `cms_templates`;
CREATE TABLE `cms_templates` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `style` text NOT NULL,
  `additional_style` text NOT NULL,
  `print_style` text NOT NULL,
  `restrict_editor` enum('0','1') NOT NULL default '0',
  `acl_read` int(11) NOT NULL default '0',
  `acl_write` int(11) NOT NULL default '0',
  `doctype` text NOT NULL,
  `login_template_item_id` int(11) NOT NULL default '0',
  `fckeditor_styles` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) TYPE=MyISAM;
