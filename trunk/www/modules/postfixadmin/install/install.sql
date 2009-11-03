-- phpMyAdmin SQL Dump
-- version 2.6.0-pl2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generatie Tijd: 20 Aug 2008 om 15:59
-- Server versie: 5.0.32
-- PHP Versie: 5.2.0-8+etch11
-- 
-- Database: `servermanager`
-- 

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `pa_aliases`
--

DROP TABLE IF EXISTS `pa_aliases`;
CREATE TABLE IF NOT EXISTS `pa_aliases` (
  `id` int(11) NOT NULL,
  `domain_id` int(11) NOT NULL,
  `address` varchar(255) default NULL,
  `goto` text,
  `ctime` int(11) NOT NULL default '0',
  `mtime` int(11) NOT NULL default '0',
  `active` enum('0','1') NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `address` (`address`),
  KEY `domain_id` (`domain_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Postfix Admin - Virtual Aliases';

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `pa_domains`
--

DROP TABLE IF EXISTS `pa_domains`;
CREATE TABLE IF NOT EXISTS `pa_domains` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL,
  `domain` varchar(255) default NULL,
  `description` varchar(255) default NULL,
  `aliases` int(10) NOT NULL default '0',
  `mailboxes` int(10) NOT NULL default '0',
  `maxquota` bigint(20) NOT NULL default '0',
  `quota` bigint(20) NOT NULL default '0',
  `transport` varchar(255) default NULL,
  `backupmx` tinyint(1) NOT NULL default '0',
  `ctime` int(11) NOT NULL default '0',
  `mtime` int(11) NOT NULL default '0',
  `active` enum('0','1') NOT NULL default '1',
  `acl_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `domain` (`domain`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Postfix Admin - Virtual Domains';

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `pa_fetchmail`
--

DROP TABLE IF EXISTS `pa_fetchmail`;
CREATE TABLE IF NOT EXISTS `pa_fetchmail` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `mailbox` varchar(255) default NULL,
  `src_server` varchar(255) default NULL,
  `src_auth` enum('password','kerberos_v5','kerberos','kerberos_v4','gssapi','cram-md5','otp','ntlm','msn','ssh','any') default NULL,
  `src_user` varchar(255) default NULL,
  `src_password` varchar(255) default NULL,
  `src_folder` varchar(255) default NULL,
  `poll_time` int(11) unsigned NOT NULL default '10',
  `fetchall` tinyint(1) unsigned NOT NULL default '0',
  `keep` tinyint(1) unsigned NOT NULL default '0',
  `protocol` enum('POP3','IMAP','POP2','ETRN','AUTO') default NULL,
  `extra_options` text,
  `returned_text` text,
  `mda` varchar(255) default NULL,
  `date` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `pa_mailboxes`
--

DROP TABLE IF EXISTS `pa_mailboxes`;
CREATE TABLE IF NOT EXISTS `pa_mailboxes` (
  `id` int(11) NOT NULL,
  `domain_id` int(11) NOT NULL,
  `go_installation_id` varchar(50) default NULL,
  `username` varchar(255) default NULL,
  `password` varchar(255) default NULL,
  `name` varchar(255) default NULL,
  `maildir` varchar(255) default NULL,
  `quota` bigint(20) NOT NULL default '0',
  `domain` varchar(255) default NULL,
  `ctime` int(11) NOT NULL,
  `mtime` int(11) NOT NULL,
  `active` enum('0','1') NOT NULL default '1',
  `vacation_active` enum('0','1') NOT NULL,
  `vacation_subject` varchar(255) default NULL,
  `vacation_body` text,
  `usage` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `username` (`username`),
  KEY `username_2` (`username`,`vacation_active`),
  KEY `go_installation_id` (`go_installation_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Postfix Admin - Virtual Mailboxes';

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `pa_vacation_notification`
--

DROP TABLE IF EXISTS `pa_vacation_notification`;
CREATE TABLE IF NOT EXISTS `pa_vacation_notification` (
  `on_vacation` varchar(100) NOT NULL default '',
  `notified` varchar(100) NOT NULL default '',
  `notified_at` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`on_vacation`,`notified`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;     