-- phpMyAdmin SQL Dump
-- version 2.8.0.3-Debian-1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generatie Tijd: 05 Sept 2006 om 15:02
-- Server versie: 5.0.22
-- PHP Versie: 5.1.2
-- 
-- Database: `imfoss_nl`
-- 

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `ig_galleries`
-- 

DROP TABLE IF EXISTS `ig_galleries`;
CREATE TABLE `ig_galleries` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `thumbwidth` int(11) NOT NULL,
  `maxcolumns` tinyint(4) NOT NULL default '5',
  `maxrows` tinyint(4) NOT NULL default '3',
  `resizeto` int(11) NOT NULL default '640',
  `acl_read` int(11) NOT NULL,
  `acl_write` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `ig_images`
-- 

DROP TABLE IF EXISTS `ig_images`;
CREATE TABLE `ig_images` (
  `id` int(11) NOT NULL,
  `gallery_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `filename` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `site_id` (`gallery_id`)
) TYPE=MyISAM;
