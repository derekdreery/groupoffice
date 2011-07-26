-- phpMyAdmin SQL Dump
-- version 3.3.2deb1
-- http://www.phpmyadmin.net
--
-- Machine: localhost
-- Genereertijd: 14 Oct 2010 om 13:42
-- Serverversie: 5.1.41
-- PHP-Versie: 5.3.2-1ubuntu4.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `GO3.6`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `emp_folders`
--

CREATE TABLE IF NOT EXISTS `emp_folders` (
  `folder_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `mtime` int(11) NOT NULL,
  PRIMARY KEY (`folder_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Gegevens worden uitgevoerd voor tabel `emp_folders`
--
