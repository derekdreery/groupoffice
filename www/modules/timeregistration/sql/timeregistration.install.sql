-- phpMyAdmin SQL Dump
-- version 2.8.2-Debian-0.2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generatie Tijd: 28 Nov 2006 om 11:37
-- Server versie: 5.0.24
-- PHP Versie: 5.1.6
-- 
-- Database: `imfoss_nl`
-- 

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `tr_hours`
-- 

CREATE TABLE `tr_hours` (
  `id` int(11) NOT NULL default '0',
  `project_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `start_time` int(11) NOT NULL default '0',
  `end_time` int(11) NOT NULL default '0',
  `comments` text NOT NULL,
  `event_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `tr_projects`
-- 

CREATE TABLE `tr_projects` (
  `id` int(11) NOT NULL default '0',
  `link_id` int(11) default NULL,
  `user_id` int(11) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `res_user_id` int(11) NOT NULL default '0',
  `acl_read` int(11) NOT NULL default '0',
  `acl_write` int(11) NOT NULL default '0',
  `acl_book` int(11) NOT NULL,
  `comments` text NOT NULL,
  `ctime` int(11) NOT NULL default '0',
  `mtime` int(11) NOT NULL default '0',
  `calendar_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `tr_settings`
-- 

CREATE TABLE `tr_settings` (
  `user_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`user_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `tr_template_events`
-- 

CREATE TABLE `tr_template_events` (
  `id` int(11) NOT NULL default '0',
  `template_id` int(11) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `description` text NOT NULL,
  `time_offset` int(11) NOT NULL default '0',
  `duration` int(11) NOT NULL default '0',
  `todo` enum('0','1') NOT NULL default '0',
  `reminder` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `template_id` (`template_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `tr_templates`
-- 

CREATE TABLE `tr_templates` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `acl_read` int(11) NOT NULL default '0',
  `acl_write` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `tr_timers`
-- 

CREATE TABLE `tr_timers` (
  `user_id` int(11) NOT NULL default '0',
  `start_time` int(11) NOT NULL default '0',
  `project_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`user_id`)
) TYPE=MyISAM;
