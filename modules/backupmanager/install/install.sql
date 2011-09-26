-- phpMyAdmin SQL Dump
-- version 3.2.3
-- http://www.phpmyadmin.net
--
-- Machine: localhost
-- Genereertijd: 11 Mar 2010 om 15:22
-- Serverversie: 5.1.37
-- PHP-Versie: 5.2.10-2ubuntu6.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `GO3`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `bm_settings`
--

CREATE TABLE IF NOT EXISTS `bm_settings` (
  `id` int(11) NOT NULL,
  `rmachine` varchar(255) NOT NULL,
  `rport` int(11) NOT NULL DEFAULT '22',
  `ruser` varchar(255) NOT NULL,
  `rtarget` varchar(255) NOT NULL,
  `rotations` int(11) NOT NULL DEFAULT '14',
  `emailaddress` varchar(255) NOT NULL,
  `emailsubject` varchar(255) NOT NULL,
  `sources` varchar(255) NOT NULL DEFAULT '/etc /home',
  `running` BOOLEAN NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Gegevens worden uitgevoerd voor tabel `bm_settings`
--

INSERT INTO `bm_settings` (`id`, `rmachine`, `rport`, `ruser`, `rtarget`, `rotations`, `emailaddress`, `emailsubject`, `sources`) VALUES
(1, '', 22, '', '', 14, '', '', '/etc /home');
