-- phpMyAdmin SQL Dump
-- version 2.11.3deb1ubuntu1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generatie Tijd: 19 Jun 2008 om 14:30
-- Server versie: 5.0.51
-- PHP Versie: 5.2.4-2ubuntu5.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `imfoss_nl`
--

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `ta_lists`
--

DROP TABLE IF EXISTS `ta_lists`;
CREATE TABLE IF NOT EXISTS `ta_lists` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `user_id` int(11) NOT NULL,
  `acl_read` int(11) NOT NULL,
  `acl_write` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `ta_tasks`
--

DROP TABLE IF EXISTS `ta_tasks`;
CREATE TABLE IF NOT EXISTS `ta_tasks` (
  `id` int(11) NOT NULL,
  `tasklist_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ctime` int(11) NOT NULL,
  `mtime` int(11) NOT NULL,
  `start_time` int(11) NOT NULL,
  `due_time` int(11) NOT NULL,
  `completion_time` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `status` varchar(20) NOT NULL,
  `repeat_end_time` int(11) NOT NULL,
  `reminder` int(11) NOT NULL,
  `rrule` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `list_id` (`tasklist_id`),
  KEY `rrule` (`rrule`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
