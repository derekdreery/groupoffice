-- phpMyAdmin SQL Dump
-- version 2.11.3deb1ubuntu1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generatie Tijd: 05 Mei 2008 om 14:21
-- Server versie: 5.0.51
-- PHP Versie: 5.2.4-2ubuntu5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Tabel structuur voor tabel `em_accounts`
--

DROP TABLE IF EXISTS `em_accounts`;
CREATE TABLE IF NOT EXISTS `em_accounts` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
	`acl_id` int(11) NOT NULL default '0',
  `type` varchar(4) default NULL,
  `host` varchar(100) default NULL,
  `port` int(11) NOT NULL default '0',
  `use_ssl` enum('0','1') NOT NULL default '0',
  `novalidate_cert` enum('0','1') NOT NULL default '0',
  `username` varchar(50) default NULL,
  `password` varchar(255) default NULL,
  `signature` text,
  `mbroot` varchar(30) default NULL,
  `sent` varchar(100) default NULL,
  `drafts` varchar(100) default NULL,
  `trash` varchar(100) default NULL,
  `spam` varchar(100) default NULL,
  `spamtag` varchar(20) default NULL,
  `examine_headers` enum('0','1') NOT NULL default '0',
  `auto_check` enum('0','1') NOT NULL default '0',
  `forward_enabled` enum('0','1') NOT NULL,
  `forward_to` varchar(255) default NULL,
  `forward_local_copy` enum('0','1') NOT NULL,
  `smtp_host` varchar(100) default NULL,
  `smtp_port` int(11) NOT NULL,
  `smtp_encryption` char(3) NOT NULL,
  `smtp_username` varchar(50) default NULL,
  `smtp_password` varchar(50) default NULL,
	`password_encrypted` TINYINT NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `em_aliases`;
CREATE TABLE IF NOT EXISTS `em_aliases` (
  `id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `signature` text NOT NULL,
  `default` enum('0','1') NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `account_id` (`account_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `em_filters`
--

DROP TABLE IF EXISTS `em_filters`;
CREATE TABLE IF NOT EXISTS `em_filters` (
  `id` int(11) NOT NULL default '0',
  `account_id` int(11) NOT NULL default '0',
  `field` varchar(20) default NULL,
  `keyword` varchar(100) default NULL,
  `folder` varchar(100) default NULL,
  `priority` int(11) NOT NULL default '0',
  `mark_as_read` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `em_folders`
--

DROP TABLE IF EXISTS `em_folders`;
CREATE TABLE IF NOT EXISTS `em_folders` (
  `id` int(11) NOT NULL default '0',
  `account_id` int(11) NOT NULL default '0',
  `name` varchar(255) default NULL,
  `subscribed` enum('0','1') NOT NULL default '0',
  `parent_id` int(11) NOT NULL default '0',
  `delimiter` char(1) NOT NULL default '',  
  `sort_order` tinyint(4) NOT NULL default '0',
  `msgcount` int(11) NOT NULL default '0',
  `unseen` int(11) NOT NULL default '0',
  `auto_check` enum('0','1') NOT NULL default '0',
  `can_have_children` BOOLEAN NOT NULL,
  `no_select` BOOLEAN,
  `sort` longtext,
  PRIMARY KEY  (`id`),
  KEY `account_id` (`account_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `em_links`
--

DROP TABLE IF EXISTS `em_links`;
CREATE TABLE IF NOT EXISTS `em_links` (
  `link_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `from` varchar(http://www.group-office.com/forum/index.php255) default NULL,
  `to` text,
  `subject` varchar(255) default NULL,
  `time` int(11) NOT NULL default '0',
  `path` varchar(255) default NULL,
  `ctime` int(11) NOT NULL,
  `acl_id` int(11) NOT NULL,
  PRIMARY KEY  (`link_id`),
  KEY `account_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `em_messages_cache`
--

DROP TABLE IF EXISTS `em_messages_cache`;
CREATE TABLE IF NOT EXISTS `em_messages_cache` (
  `folder_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `new` enum('0','1') NOT NULL,
  `subject` varchar(100) DEFAULT NULL,
  `from` varchar(100) DEFAULT NULL,
  `size` int(11) NOT NULL,
  `udate` int(11) NOT NULL,
  `attachments` enum('0','1') NOT NULL,
  `flagged` enum('0','1') NOT NULL,
  `answered` enum('0','1') NOT NULL,
	`forwarded` tinyint(1) NOT NULL,
  `priority` tinyint(4) NOT NULL,
  `to` varchar(255) DEFAULT NULL,
  `serialized_message_object` mediumtext NOT NULL,
  PRIMARY KEY (`folder_id`,`uid`),
  KEY `account_id` (`account_id`),
	KEY `udate` (`udate`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `go_links_9`
-- 

DROP TABLE IF EXISTS `go_links_9`;
CREATE TABLE IF NOT EXISTS `go_links_9` (
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
-- Tabelstructuur voor tabel `em_accounts_collapsed`
--

DROP TABLE IF EXISTS `em_accounts_collapsed`;
CREATE TABLE IF NOT EXISTS `em_accounts_collapsed` (
  `account_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`account_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `em_folders_expanded`
--
DROP TABLE IF EXISTS `em_folders_expanded`;
CREATE TABLE IF NOT EXISTS `em_folders_expanded` (
  `folder_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`folder_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `em_accounts_sort`
--
CREATE TABLE IF NOT EXISTS `em_accounts_sort` (
`account_id` INT( 11 ) NOT NULL ,
`user_id` INT( 11 ) NOT NULL ,
`order` INT( 11 ) NOT NULL ,
PRIMARY KEY ( `account_id` , `user_id` )
) ENGINE = MYISAM DEFAULT CHARSET=utf8 ;