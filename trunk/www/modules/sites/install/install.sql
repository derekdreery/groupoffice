-- phpMyAdmin SQL Dump
-- version 3.4.5deb1
-- http://www.phpmyadmin.net
--
-- Machine: localhost
-- Genereertijd: 07 dec 2011 om 14:15
-- Serverversie: 5.1.58
-- PHP-Versie: 5.3.6-13ubuntu3.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `intermesh`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `si_pages`
--

CREATE TABLE IF NOT EXISTS `si_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `site_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ctime` int(11) NOT NULL,
  `mtime` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(255) NOT NULL,
  `keywords` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `template` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `hidden` tinyint(1) NOT NULL DEFAULT '0',
  `sort` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `path` (`path`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Gegevens worden uitgevoerd voor tabel `si_pages`
--

INSERT INTO `si_pages` (`id`, `parent_id`, `site_id`, `user_id`, `ctime`, `mtime`, `name`, `title`, `description`, `keywords`, `path`, `template`, `content`, `hidden`, `sort`) VALUES
(1, 0, 1, 1, 0, 0, 'Products', 'Products', '', '', 'products', '', '', 0, 0),
(2, 0, 1, 1, 0, 0, 'Checkout', '', '', '', 'checkout', 'checkout', '', 1, 0);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `si_sites`
--

CREATE TABLE IF NOT EXISTS `si_sites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `user_id` int(11) NOT NULL,
  `mtime` int(11) NOT NULL,
  `ctime` int(11) NOT NULL,
  `domain` varchar(100) NOT NULL,
  `template` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `domain` (`domain`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Gegevens worden uitgevoerd voor tabel `si_sites`
--

INSERT INTO `si_sites` (`id`, `name`, `user_id`, `mtime`, `ctime`, `domain`, `template`) VALUES
(1, 'Intermesh software shop', 1, 1320058048, 1320058048, 'shop.group-office.com', 'Example');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;