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
-- Table structure for table `si_pages`
--

CREATE TABLE IF NOT EXISTS `si_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `site_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `ctime` int(11) NOT NULL,
  `mtime` int(11) NOT NULL,
  `name` varchar(100) NOT NULL DEFAULT 'New Page',
  `title` varchar(100) NOT NULL DEFAULT 'New Page',
  `description` varchar(255) NOT NULL DEFAULT '',
  `keywords` varchar(255) NOT NULL DEFAULT '',
  `path` varchar(255) NOT NULL DEFAULT '',
  `template` varchar(100) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `hidden` tinyint(1) NOT NULL DEFAULT '0',
  `sort` int(11) NOT NULL DEFAULT '0',
  `login_required` tinyint(1) NOT NULL DEFAULT '0',
  `controller` varchar(80) NOT NULL DEFAULT 'GO_Sites_Controller_Site',
  `controller_action` varchar(80) NOT NULL DEFAULT 'index',
  PRIMARY KEY (`id`),
  KEY `path` (`path`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `si_sites`
--

CREATE TABLE IF NOT EXISTS `si_sites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `user_id` int(11) NOT NULL,
  `mtime` int(11) NOT NULL,
  `ctime` int(11) NOT NULL,
  `domain` varchar(100) NOT NULL,
  `template` varchar(100) NOT NULL,
  `login_path` varchar(255) NOT NULL DEFAULT 'login',
  `ssl` tinyint(1) NOT NULL DEFAULT '0',
  `mod_rewrite` tinyint(1) NOT NULL DEFAULT '0',
  `mod_rewrite_base_path` varchar(50) NOT NULL DEFAULT '/',
  `lost_password_path` varchar(255) NOT NULL DEFAULT 'lostpassword',
  `reset_password_path` varchar(255) NOT NULL DEFAULT 'resetpassword',
  `register_path` varchar(255) NOT NULL DEFAULT 'register',
  `logout_path` varchar(255) NOT NULL DEFAULT 'logout',
  `register_user_groups` varchar(50) DEFAULT '',
  `language` varchar(10) NOT NULL DEFAULT 'en',
  PRIMARY KEY (`id`),
  KEY `domain` (`domain`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;