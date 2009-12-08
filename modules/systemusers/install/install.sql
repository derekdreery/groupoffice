-- phpMyAdmin SQL Dump
-- version 3.1.2deb1ubuntu0.1
-- http://www.phpmyadmin.net
--
-- Machine: localhost
-- Genereertijd: 06 Oct 2009 om 09:43
-- Serverversie: 5.0.75
-- PHP-Versie: 5.2.6-3ubuntu4.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `GOffice`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `syu_vacation`
--

CREATE TABLE IF NOT EXISTS `syu_vacation` (
  `account_id` int(11) NOT NULL,
  `vacation_active` enum('0','1') character set utf8 NOT NULL,
  `vacation_subject` varchar(255) character set utf8 default NULL,
  `vacation_body` text character set utf8,
  PRIMARY KEY  (`account_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
