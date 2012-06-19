-- phpMyAdmin SQL Dump
-- version 3.4.5deb1
-- http://www.phpmyadmin.net
--
-- Machine: localhost
-- Genereertijd: 11 apr 2012 om 16:20
-- Serverversie: 5.1.61
-- PHP-Versie: 5.3.6-13ubuntu3.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `groupofficecomoud`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `sm_installations`
--

DROP TABLE IF EXISTS `sm_installations`;
CREATE TABLE IF NOT EXISTS `sm_installations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `ctime` int(11) NOT NULL,
  `mtime` int(11) NOT NULL,
  `max_users` int(11) NOT NULL,
  `count_users` int(11) NOT NULL DEFAULT '0',
  `install_time` int(11) NOT NULL DEFAULT '0',
  `lastlogin` int(11) NOT NULL DEFAULT '0',
  `total_logins` int(11) NOT NULL DEFAULT '0',
  `database_usage` int(11) NOT NULL DEFAULT '0',
  `file_storage_usage` int(11) NOT NULL DEFAULT '0',
  `mailbox_usage` int(11) DEFAULT NULL,
  `report_ctime` int(11) NOT NULL DEFAULT '0',
  `comment` text,
  `features` varchar(255) DEFAULT NULL,
  `mail_domains` varchar(255) DEFAULT NULL,
  `admin_email` varchar(100) DEFAULT NULL,
  `admin_name` varchar(100) DEFAULT NULL,
  `admin_salutation` varchar(100) DEFAULT NULL,
  `admin_country` char(2) NOT NULL DEFAULT '',
  `date_format` varchar(20) DEFAULT NULL,
  `thousands_separator` char(1) DEFAULT NULL,
  `decimal_separator` char(1) DEFAULT NULL,
  `billing` tinyint(1) NOT NULL DEFAULT '0',
  `professional` tinyint(1) NOT NULL DEFAULT '0',
  `status` varchar(50) NOT NULL,
  `status_change_time` int(11) NOT NULL DEFAULT '0',
  `config_file` varchar(255) NOT NULL DEFAULT '',
  `token` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=36 ;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `sm_installation_users`
--

DROP TABLE IF EXISTS `sm_installation_users`;
CREATE TABLE IF NOT EXISTS `sm_installation_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `installation_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL DEFAULT '',
  `ctime` int(11) NOT NULL,
  `lastlogin` int(11) NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `installation_id` (`installation_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=146 ;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `sm_installation_user_modules`
--

DROP TABLE IF EXISTS `sm_installation_user_modules`;
CREATE TABLE IF NOT EXISTS `sm_installation_user_modules` (
  `user_id` int(11) NOT NULL,
  `module_id` varchar(100) NOT NULL,
  PRIMARY KEY (`user_id`,`module_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `sm_new_trials`
--

DROP TABLE IF EXISTS `sm_new_trials`;
CREATE TABLE IF NOT EXISTS `sm_new_trials` (
  `name` varchar(50) NOT NULL DEFAULT '',
  `title` varchar(100) DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(20) DEFAULT NULL,
  `key` varchar(100) DEFAULT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`name`),
  KEY `key` (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `sm_auto_email`
--

DROP TABLE IF EXISTS `sm_auto_email`;
CREATE TABLE IF NOT EXISTS `sm_auto_email` (
	`id` INT( 11 ) NOT NULL AUTO_INCREMENT,
	`name` varchar(50) NOT NULL DEFAULT '',
	`days` int(5) NOT NULL DEFAULT '0',
	`mime` TEXT,
	`active` tinyint(1) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;