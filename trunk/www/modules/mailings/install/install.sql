-- phpMyAdmin SQL Dump
-- version 2.11.3deb1ubuntu1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generatie Tijd: 04 Sept 2008 om 17:07
-- Server versie: 5.0.51
-- PHP Versie: 5.2.4-2ubuntu5.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `imfoss`
--

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `ml_templates`
--

DROP TABLE IF EXISTS `ml_templates`;
CREATE TABLE IF NOT EXISTS `ml_templates` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `type` tinyint(4) NOT NULL default '0',
  `name` varchar(100) default NULL,
  `acl_id` int(11) NOT NULL default '0',
  `content` longblob NOT NULL,
	`extension` VARCHAR( 4 ) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `ml_default_templates`
--

DROP TABLE IF EXISTS `ml_default_templates`;
CREATE TABLE IF NOT EXISTS `ml_default_templates` (
  `user_id` int(11) NOT NULL,
  `template_id` int(11) NOT NULL,
  PRIMARY KEY  (`user_id`),
  KEY `template_id` (`template_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `ml_mailing_users`
--

DROP TABLE IF EXISTS `ml_mailing_users`;
CREATE TABLE IF NOT EXISTS `ml_mailing_users` (
  `group_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`group_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `ml_sendmailing_companies`
--

DROP TABLE IF EXISTS `ml_sendmailing_companies`;
CREATE TABLE IF NOT EXISTS `ml_sendmailing_companies` (
  `mailing_id` int(11) NOT NULL DEFAULT '0',
  `company_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`mailing_id`,`company_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `ml_sendmailing_contacts`
--

DROP TABLE IF EXISTS `ml_sendmailing_contacts`;
CREATE TABLE IF NOT EXISTS `ml_sendmailing_contacts` (
  `mailing_id` int(11) NOT NULL DEFAULT '0',
  `contact_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`mailing_id`,`contact_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `ml_sendmailing_users`
--

DROP TABLE IF EXISTS `ml_sendmailing_users`;
CREATE TABLE IF NOT EXISTS `ml_sendmailing_users` (
  `mailing_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`mailing_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
