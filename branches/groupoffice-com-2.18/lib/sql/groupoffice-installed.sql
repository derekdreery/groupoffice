-- phpMyAdmin SQL Dump
-- version 2.10.3deb1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generatie Tijd: 26 Oct 2007 om 11:31
-- Server versie: 5.0.45
-- PHP Versie: 5.2.3-1ubuntu6

-- 
-- Database: `go218stable1`
-- 

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `ab_addressbooks`
-- 

CREATE TABLE IF NOT EXISTS `ab_addressbooks` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `acl_read` int(11) NOT NULL default '0',
  `acl_write` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `ab_addressbooks`
-- 

INSERT INTO `ab_addressbooks` (`id`, `user_id`, `name`, `acl_read`, `acl_write`) VALUES 
(1, 1, 'Admin, Group-Office', 36, 37),
(4, 1, 'test', 52, 53);

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `ab_companies`
-- 

CREATE TABLE IF NOT EXISTS `ab_companies` (
  `id` int(11) NOT NULL default '0',
  `link_id` int(11) default NULL,
  `user_id` int(11) NOT NULL default '0',
  `addressbook_id` int(11) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `address` varchar(100) NOT NULL default '',
  `address_no` varchar(10) NOT NULL default '',
  `zip` varchar(10) NOT NULL default '',
  `city` varchar(50) NOT NULL default '',
  `state` varchar(50) NOT NULL default '',
  `country` varchar(50) NOT NULL default '',
  `post_address` varchar(100) NOT NULL default '',
  `post_address_no` varchar(10) NOT NULL default '',
  `post_city` varchar(50) NOT NULL default '',
  `post_state` varchar(50) NOT NULL default '',
  `post_country` varchar(50) NOT NULL default '',
  `post_zip` varchar(10) NOT NULL default '',
  `phone` varchar(20) NOT NULL default '',
  `fax` varchar(20) NOT NULL default '',
  `email` varchar(75) NOT NULL default '',
  `homepage` varchar(100) NOT NULL default '',
  `bank_no` varchar(50) NOT NULL,
  `vat_no` varchar(30) NOT NULL default '',
  `ctime` int(11) NOT NULL default '0',
  `mtime` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `addressbook_id` (`addressbook_id`),
  KEY `link_id` (`link_id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `ab_companies`
-- 

INSERT INTO `ab_companies` (`id`, `link_id`, `user_id`, `addressbook_id`, `name`, `address`, `address_no`, `zip`, `city`, `state`, `country`, `post_address`, `post_address_no`, `post_city`, `post_state`, `post_country`, `post_zip`, `phone`, `fax`, `email`, `homepage`, `bank_no`, `vat_no`, `ctime`, `mtime`) VALUES 
(6, 6, 1, 1, 'Intermesh', 'Reitchscheweg', '37', '5231MN', 'Den Bosch', 'Noord-Brabant', 'Nederland', 'Wethouder Schuurmanslaan', '361', 'Den Bosch', 'Noord-Brabant', 'Nederland', '5231MN', '+31619864268', '+31619864268', 'info@intermesh.nl', 'http://', '12345.12344', 'NL212313215', 1163337736, 1163337805),
(7, 7, 1, 1, 'Intermesh Directie', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1163342395, 1163342395);

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `ab_contacts`
-- 

CREATE TABLE IF NOT EXISTS `ab_contacts` (
  `id` int(11) NOT NULL default '0',
  `link_id` int(11) default NULL,
  `user_id` int(11) NOT NULL default '0',
  `addressbook_id` int(11) NOT NULL default '0',
  `source_id` int(11) NOT NULL default '0',
  `first_name` varchar(50) NOT NULL default '',
  `middle_name` varchar(50) NOT NULL default '',
  `last_name` varchar(50) NOT NULL default '',
  `initials` varchar(10) NOT NULL default '',
  `title` varchar(10) NOT NULL default '',
  `sex` enum('M','F') NOT NULL default 'M',
  `birthday` date NOT NULL default '0000-00-00',
  `email` varchar(100) NOT NULL default '',
  `email2` varchar(100) NOT NULL default '',
  `email3` varchar(100) NOT NULL default '',
  `company_id` int(11) NOT NULL default '0',
  `department` varchar(50) NOT NULL default '',
  `function` varchar(50) NOT NULL default '',
  `home_phone` varchar(20) NOT NULL default '',
  `work_phone` varchar(20) NOT NULL default '',
  `fax` varchar(20) NOT NULL default '',
  `work_fax` varchar(20) NOT NULL default '',
  `cellular` varchar(20) NOT NULL default '',
  `country` varchar(50) NOT NULL default '',
  `state` varchar(50) NOT NULL default '',
  `city` varchar(50) NOT NULL default '',
  `zip` varchar(10) NOT NULL default '',
  `address` varchar(100) NOT NULL default '',
  `address_no` varchar(10) NOT NULL default '',
  `comment` varchar(50) NOT NULL default '',
  `color` varchar(6) NOT NULL default '',
  `sid` varchar(32) NOT NULL default '',
  `ctime` int(11) NOT NULL default '0',
  `mtime` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `user_id` (`addressbook_id`),
  KEY `link_id` (`link_id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `ab_contacts`
-- 


-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `ab_settings`
-- 

CREATE TABLE IF NOT EXISTS `ab_settings` (
  `user_id` int(11) NOT NULL default '0',
  `search_type` varchar(10) NOT NULL default '',
  `search_contacts_field` varchar(30) NOT NULL default '',
  `search_companies_field` varchar(30) NOT NULL default '',
  `search_users_field` varchar(30) NOT NULL default '',
  `search_addressbook_id` int(11) NOT NULL default '0',
  `addressbook_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`user_id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `ab_settings`
-- 

INSERT INTO `ab_settings` (`user_id`, `search_type`, `search_contacts_field`, `search_companies_field`, `search_users_field`, `search_addressbook_id`, `addressbook_id`) VALUES 
(1, 'contact', '', '', '', 1, 1);

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `ab_zipcodes`
-- 

CREATE TABLE IF NOT EXISTS `ab_zipcodes` (
  `id` int(11) NOT NULL default '0',
  `zip` varchar(10) NOT NULL default '',
  `state` varchar(100) NOT NULL default '',
  `city` varchar(100) NOT NULL default '',
  `street` varchar(100) NOT NULL default '',
  `country` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `zip` (`zip`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `ab_zipcodes`
-- 


-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `acl`
-- 

CREATE TABLE IF NOT EXISTS `acl` (
  `acl_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`acl_id`,`user_id`,`group_id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `acl`
-- 

INSERT INTO `acl` (`acl_id`, `user_id`, `group_id`) VALUES 
(1, 0, 1),
(2, 0, 1),
(3, 0, 1),
(3, 0, 2),
(4, 0, 1),
(4, 1, 0),
(5, 0, 1),
(5, 1, 0),
(6, 0, 1),
(6, 1, 0),
(7, 0, 1),
(7, 1, 0),
(8, 0, 1),
(8, 1, 0),
(9, 0, 1),
(9, 1, 0),
(10, 0, 1),
(10, 1, 0),
(11, 0, 1),
(11, 1, 0),
(12, 0, 1),
(12, 1, 0),
(13, 0, 1),
(13, 1, 0),
(14, 0, 1),
(14, 1, 0),
(15, 0, 1),
(15, 1, 0),
(16, 0, 1),
(16, 1, 0),
(17, 0, 1),
(17, 1, 0),
(18, 0, 1),
(18, 1, 0),
(19, 0, 1),
(19, 1, 0),
(20, 0, 1),
(20, 1, 0),
(21, 0, 1),
(21, 1, 0),
(22, 0, 1),
(22, 1, 0),
(23, 0, 1),
(23, 1, 0),
(24, 0, 1),
(24, 1, 0),
(25, 0, 1),
(25, 1, 0),
(26, 0, 1),
(26, 1, 0),
(27, 0, 1),
(27, 1, 0),
(28, 0, 1),
(28, 1, 0),
(29, 0, 1),
(29, 1, 0),
(30, 0, 1),
(31, 0, 1),
(31, 1, 0),
(32, 0, 1),
(33, 0, 1),
(33, 1, 0),
(34, 0, 1),
(34, 1, 0),
(35, 0, 1),
(36, 0, 1),
(37, 0, 1),
(37, 1, 0),
(50, 0, 1),
(51, 0, 1),
(52, 0, 1),
(53, 0, 1),
(53, 1, 0),
(54, 0, 1),
(54, 1, 0),
(55, 0, 1),
(55, 1, 0),
(56, 0, 1),
(57, 0, 1),
(57, 1, 0),
(58, 0, 1),
(59, 0, 1),
(59, 1, 0),
(60, 0, 1);

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `acl_items`
-- 

CREATE TABLE IF NOT EXISTS `acl_items` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `description` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `acl_items`
-- 

INSERT INTO `acl_items` (`id`, `user_id`, `description`) VALUES 
(1, 0, 'Module read: modules'),
(2, 0, 'Module write: modules'),
(3, 1, 'webmaster@example.com'),
(4, 0, 'Module read: addressbook'),
(5, 0, 'Module write: addressbook'),
(6, 0, 'Module read: calendar'),
(7, 0, 'Module write: calendar'),
(8, 0, 'Module read: email'),
(9, 0, 'Module write: email'),
(10, 0, 'Module read: filesystem'),
(11, 0, 'Module write: filesystem'),
(12, 0, 'Module read: groups'),
(13, 0, 'Module write: groups'),
(14, 0, 'Module read: cms'),
(15, 0, 'Module write: cms'),
(16, 0, 'Module read: phpsysinfo'),
(17, 0, 'Module write: phpsysinfo'),
(18, 0, 'Module read: projects'),
(19, 0, 'Module write: projects'),
(20, 0, 'Module read: summary'),
(21, 0, 'Module write: summary'),
(22, 0, 'Module read: todos'),
(23, 0, 'Module write: todos'),
(24, 0, 'Module read: gallery'),
(25, 0, 'Module write: gallery'),
(26, 0, 'Module read: users'),
(27, 0, 'Module write: users'),
(28, 0, 'Module read: notes'),
(29, 0, 'Module write: notes'),
(30, 1, 'calendar read: Admin, Group-Office'),
(31, 1, 'calendar write: Admin, Group-Office'),
(32, 1, ''),
(33, 1, ''),
(34, 1, 'cms write: www.example.com'),
(35, 1, ''),
(36, 1, 'acl_read addressbook_id: 1'),
(37, 1, 'acl_write addressbook_id: 1'),
(50, 1, 'Project book: test'),
(51, 1, ''),
(52, 1, 'acl_read addressbook_id: 4'),
(53, 1, 'acl_write addressbook_id: 4'),
(54, 0, 'Module read: translate'),
(55, 0, 'Module write: translate'),
(56, 1, 'calendar read: Test'),
(57, 1, 'calendar write: Test'),
(58, 1, 'Project read: Test'),
(59, 1, 'Project write: Test'),
(60, 1, 'Project book: Test');

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `cal_backgrounds`
-- 

CREATE TABLE IF NOT EXISTS `cal_backgrounds` (
  `id` int(11) NOT NULL,
  `color` char(6) NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `cal_backgrounds`
-- 


-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `cal_calendars`
-- 

CREATE TABLE IF NOT EXISTS `cal_calendars` (
  `id` int(11) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '1',
  `user_id` int(11) NOT NULL default '0',
  `acl_read` int(11) NOT NULL default '0',
  `acl_write` int(11) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `start_hour` tinyint(4) NOT NULL default '0',
  `end_hour` tinyint(4) NOT NULL default '0',
  `background` varchar(6) NOT NULL default 'FFFFCC',
  `time_interval` int(11) NOT NULL default '1800',
  PRIMARY KEY  (`id`),
  KEY `group_id` (`group_id`),
  KEY `group_id_2` (`group_id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `cal_calendars`
-- 

INSERT INTO `cal_calendars` (`id`, `group_id`, `user_id`, `acl_read`, `acl_write`, `name`, `start_hour`, `end_hour`, `background`, `time_interval`) VALUES 
(1, 0, 1, 30, 31, 'Admin, Group-Office', 7, 20, 'FFFFCC', 1800),
(4, 0, 1, 56, 57, 'Test', 8, 20, 'FFFFCC', 1800);

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `cal_calendar_backgrounds`
-- 

CREATE TABLE IF NOT EXISTS `cal_calendar_backgrounds` (
  `id` int(11) NOT NULL,
  `calendar_id` int(11) NOT NULL,
  `background_id` int(11) NOT NULL,
  `weekday` tinyint(4) NOT NULL,
  `start_time` int(11) NOT NULL,
  `end_time` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `calendar_id` (`calendar_id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `cal_calendar_backgrounds`
-- 


-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `cal_events`
-- 

CREATE TABLE IF NOT EXISTS `cal_events` (
  `id` int(11) NOT NULL default '0',
  `event_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `permissions` tinyint(4) NOT NULL default '0',
  `start_time` int(11) NOT NULL default '0',
  `end_time` int(11) NOT NULL default '0',
  `all_day_event` enum('0','1') NOT NULL default '0',
  `link_id` int(11) NOT NULL default '0',
  `contact_id` int(11) NOT NULL default '0',
  `company_id` int(11) NOT NULL default '0',
  `project_id` int(11) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `description` text NOT NULL,
  `location` varchar(100) NOT NULL default '',
  `background` varchar(7) NOT NULL default '',
  `repeat_type` enum('0','1','2','3','4','5') NOT NULL default '0',
  `repeat_forever` enum('0','1') NOT NULL default '0',
  `repeat_every` tinyint(4) NOT NULL default '0',
  `repeat_end_time` int(11) NOT NULL default '0',
  `mon` enum('0','1') NOT NULL default '0',
  `tue` enum('0','1') NOT NULL default '0',
  `wed` enum('0','1') NOT NULL default '0',
  `thu` enum('0','1') NOT NULL default '0',
  `fri` enum('0','1') NOT NULL default '0',
  `sat` enum('0','1') NOT NULL default '0',
  `sun` enum('0','1') NOT NULL default '0',
  `month_time` enum('0','1','2','3','4','5') NOT NULL default '0',
  `reminder` int(11) NOT NULL default '0',
  `ctime` int(11) NOT NULL default '0',
  `mtime` int(11) NOT NULL default '0',
  `todo` enum('0','1') NOT NULL default '0',
  `completion_time` int(11) NOT NULL default '0',
  `status_id` tinyint(4) NOT NULL default '0',
  `custom_fields` text NOT NULL,
  `timezone` float NOT NULL default '0',
  `DST` enum('0','1') NOT NULL default '0',
  `busy` enum('0','1') NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `start_time` (`start_time`),
  KEY `end_time` (`end_time`),
  KEY `repeat_end_time` (`repeat_end_time`),
  KEY `todo` (`todo`),
  KEY `event_id` (`event_id`),
  KEY `event_id_2` (`event_id`),
  KEY `link_id` (`link_id`),
  KEY `link_id_2` (`link_id`),
  KEY `status_id` (`status_id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `cal_events`
-- 


-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `cal_events_calendars`
-- 

CREATE TABLE IF NOT EXISTS `cal_events_calendars` (
  `calendar_id` int(11) NOT NULL default '0',
  `event_id` int(11) NOT NULL default '0',
  `sid` char(32) NOT NULL default '',
  KEY `calendar_id` (`calendar_id`,`event_id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `cal_events_calendars`
-- 


-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `cal_exceptions`
-- 

CREATE TABLE IF NOT EXISTS `cal_exceptions` (
  `id` int(11) NOT NULL default '0',
  `event_id` int(11) NOT NULL default '0',
  `time` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `event_id` (`event_id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `cal_exceptions`
-- 


-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `cal_groups`
-- 

CREATE TABLE IF NOT EXISTS `cal_groups` (
  `id` int(11) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `custom_fields` text NOT NULL,
  `acl_write` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `cal_groups`
-- 


-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `cal_group_admins`
-- 

CREATE TABLE IF NOT EXISTS `cal_group_admins` (
  `group_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`group_id`,`user_id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `cal_group_admins`
-- 


-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `cal_holidays`
-- 

CREATE TABLE IF NOT EXISTS `cal_holidays` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `date` int(10) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `region` varchar(4) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`),
  KEY `user_id_2` (`user_id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `cal_holidays`
-- 

INSERT INTO `cal_holidays` (`id`, `user_id`, `date`, `name`, `region`) VALUES 
(31, 1, 1136070000, 'Nieuwjaar', 'nl'),
(32, 1, 1139871600, 'Valentijnsdag', 'nl'),
(33, 1, 1144965600, 'Goede vrijdag', 'nl'),
(34, 1, 1145138400, '1e Paasdag', 'nl'),
(35, 1, 1145224800, '2e Paasdag', 'nl'),
(36, 1, 1146348000, 'Koninginnedag', 'nl'),
(37, 1, 1146780000, 'Bevrijdingsdag', 'nl'),
(38, 1, 1148508000, 'Hemelvaartsdag', 'nl'),
(39, 1, 1149372000, '1e pinksterdag', 'nl'),
(40, 1, 1149458400, '2e pinksterdag', 'nl'),
(41, 1, 1159912800, 'Wereld dierendag', 'nl'),
(42, 1, 1163199600, 'Sint Maarten', 'nl'),
(43, 1, 1167001200, '1e kerstdag', 'nl'),
(44, 1, 1167087600, '2e kerstdag', 'nl'),
(45, 1, 1167519600, 'Oudjaarsavond', 'nl');

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `cal_participants`
-- 

CREATE TABLE IF NOT EXISTS `cal_participants` (
  `id` int(11) NOT NULL default '0',
  `event_id` int(11) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `email` varchar(100) NOT NULL default '',
  `user_id` int(11) NOT NULL default '0',
  `status` enum('0','1','2') NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `id` (`id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `cal_participants`
-- 


-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `cal_reminders`
-- 

CREATE TABLE IF NOT EXISTS `cal_reminders` (
  `user_id` int(11) NOT NULL default '0',
  `event_id` int(11) NOT NULL default '0',
  `remind_time` int(11) NOT NULL default '0',
  `occurence_time` int(11) NOT NULL default '0',
  `email_sent` enum('0','1') NOT NULL default '0',
  KEY `user_id` (`user_id`),
  KEY `remind_time` (`remind_time`),
  KEY `remind_time_2` (`remind_time`),
  KEY `email_sent` (`email_sent`),
  KEY `email_sent_2` (`email_sent`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `cal_reminders`
-- 


-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `cal_settings`
-- 

CREATE TABLE IF NOT EXISTS `cal_settings` (
  `user_id` int(11) NOT NULL default '0',
  `default_cal_id` int(11) NOT NULL default '0',
  `default_view_id` int(11) NOT NULL default '0',
  `show_days` tinyint(4) NOT NULL default '0',
  `merged_view` enum('0','1') NOT NULL default '0',
  `reminder` int(11) NOT NULL default '0',
  `refresh_rate` varchar(5) NOT NULL default '',
  `permissions` tinyint(4) NOT NULL default '0',
  `show_todos` enum('0','1') NOT NULL default '0',
  `weekview` enum('5','7') NOT NULL default '7',
  `show_completed` enum('0','1') NOT NULL default '0',
  `view_type` enum('grid','list') NOT NULL default 'grid',
  `check_conflicts` enum('0','1') NOT NULL default '1',
  `email_changes` enum('0','1') NOT NULL default '1',
  `email_reminders` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`user_id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `cal_settings`
-- 

INSERT INTO `cal_settings` (`user_id`, `default_cal_id`, `default_view_id`, `show_days`, `merged_view`, `reminder`, `refresh_rate`, `permissions`, `show_todos`, `weekview`, `show_completed`, `view_type`, `check_conflicts`, `email_changes`, `email_reminders`) VALUES 
(1, 1, 0, 0, '0', 0, '', 0, '0', '7', '0', 'grid', '1', '1', '0');

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `cal_statuses`
-- 

CREATE TABLE IF NOT EXISTS `cal_statuses` (
  `id` int(11) NOT NULL default '0',
  `type` varchar(20) NOT NULL default '',
  `name` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `type` (`type`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `cal_statuses`
-- 

INSERT INTO `cal_statuses` (`id`, `type`, `name`) VALUES 
(1, 'VEVENT', 'NEEDS-ACTION'),
(2, 'VEVENT', 'ACCEPTED'),
(3, 'VEVENT', 'DECLINED'),
(4, 'VEVENT', 'TENTATIVE'),
(5, 'VEVENT', 'DELEGATED'),
(6, 'VTODO', 'NEEDS-ACTION'),
(7, 'VTODO', 'ACCEPTED'),
(8, 'VTODO', 'DECLINED'),
(9, 'VTODO', 'TENTATIVE'),
(10, 'VTODO', 'DELEGATED'),
(11, 'VTODO', 'COMPLETED'),
(12, 'VTODO', 'IN-PROCESS');

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `cal_views`
-- 

CREATE TABLE IF NOT EXISTS `cal_views` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `start_hour` tinyint(4) NOT NULL default '0',
  `end_hour` tinyint(4) NOT NULL default '0',
  `event_colors_override` enum('0','1') NOT NULL default '0',
  `time_interval` int(11) NOT NULL default '1800',
  `acl_read` int(11) NOT NULL default '0',
  `acl_write` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `cal_views`
-- 


-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `cal_views_calendars`
-- 

CREATE TABLE IF NOT EXISTS `cal_views_calendars` (
  `view_id` int(11) NOT NULL default '0',
  `calendar_id` int(11) NOT NULL default '0',
  `background` char(6) NOT NULL default 'CCFFCC',
  PRIMARY KEY  (`view_id`,`calendar_id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `cal_views_calendars`
-- 


-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `cms_comments`
-- 

CREATE TABLE IF NOT EXISTS `cms_comments` (
  `id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `comments` text NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `file_id` (`file_id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `cms_comments`
-- 

INSERT INTO `cms_comments` (`id`, `file_id`, `user_id`, `name`, `comments`, `ctime`) VALUES 
(2, 4, 1, 'Group-Office Admin', 'This is an example comment on this page.\r\nCustomers can easily repond to your articles with this plugin.', 1159524166);

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `cms_files`
-- 

CREATE TABLE IF NOT EXISTS `cms_files` (
  `id` int(11) NOT NULL default '0',
  `folder_id` int(11) NOT NULL default '0',
  `extension` varchar(10) NOT NULL default '',
  `size` int(11) NOT NULL default '0',
  `ctime` int(11) NOT NULL,
  `mtime` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `content` longtext NOT NULL,
  `auto_meta` enum('0','1') NOT NULL default '1',
  `title` varchar(100) NOT NULL default '',
  `description` text NOT NULL,
  `keywords` text NOT NULL,
  `priority` int(11) NOT NULL default '0',
  `hot_item` enum('0','1') default NULL,
  `hot_item_text` text NOT NULL,
  `template_item_id` int(11) NOT NULL default '0',
  `acl` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `folder_id` (`folder_id`),
  FULLTEXT KEY `name` (`name`),
  FULLTEXT KEY `content` (`content`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `cms_files`
-- 

INSERT INTO `cms_files` (`id`, `folder_id`, `extension`, `size`, `ctime`, `mtime`, `name`, `content`, `auto_meta`, `title`, `description`, `keywords`, `priority`, `hot_item`, `hot_item_text`, `template_item_id`, `acl`) VALUES 
(1, 1, 'html', 230, 1159522480, 1159522489, 'Welcome.html', '<h1>Welcome to the Group-Office CMS</h1>\r\nWelcome to the Group-Office content management system. This is just an example page to show how this module works.<br />\r\nThe system is extremely flexible and easy to use for the end user!', '1', 'Welcome to the Group-Office CMS', 'welcome group-office cms', 'welcome, group-office, cms', 1, '0', '', 1, 0),
(2, 1, 'html', 1279, 1159522623, 1159522715, 'CMS sites.html', '<h1>Sites that run on GO CMS</h1>\r\nTo show you what the system can do the following sites use the Group-Office CMS:<br />\r\n<br />\r\n<a href="http://www.pedulianak.com/">http://www.pedulianak.com</a><br />\r\n<a target="_blank" href="http://www.ish-web.org/">http://www.ish-web.org</a><br />\r\n<span style="font-weight: bold;"></span><a href="http://www.firmpeople.nl/" target="_blank">http://www.firmpeople.n</a><br />\r\n<a target="_blank" href="http://www.nvvh.com/">http://www.nvvh.com</a><span style="font-weight: bold;"><strong></strong></span><a href="http://www.firmpeople.nl/" target="_blank">l</a><br />\r\n<a href="http://www.group-office.com/" target="_blank">http://www.group-office.com</a><br />\r\n<a href="http://www.intermesh.nl/" target="_blank">http://www.intermesh.nl</a><br />\r\n<a href="http://www.online-ingredients.com/" target="_blank">http://www.online-ingredients.com</a><br />\r\n<a href="http://www.technicaltrader.nl/">http://www.technicaltrader.nl</a><br />\r\n<a href="http://www.foss-it.nl/" target="_blank">http://www.foss-it.nl</a><br />\r\n<br />\r\nDo you want your own CMS but don''t know how to create a template? Contact Intermesh to build one for you!<br />\r\n<a href="http://www.intermesh.nl/" target="_blank"></a>', '1', 'Sites that run on GO CMS', 'go cms', 'go, cms', 2, '0', '', 1, 0),
(3, 1, 'html', 169, 1159522780, 1161259796, 'Contact.html', '<h1>Contact</h1>\r\nUse the form below to contact. It''s a plugin for the GO CMS!<br />\r\n<br />\r\n<cms:plugin plugin_id="contact" height="150">Contact form</cms:plugin>', '1', 'Contact', 'contact', 'contact', 3, '0', '', 1, 0),
(4, 2, 'html', 613, 1159523334, 1161259885, 'GO CMS is improved.html', '<h1>GO CMS is improved!</h1>\r\nThe new CMS module has a lot of new features!<br />\r\nLike the new plugin system to add things like:<br />\r\n<ol>\r\n    <li>Photo galleries</li>\r\n    <li>Contact forms</li>\r\n    <li>A comments form for visitors to respond to the page (See this page)</li>\r\n</ol>\r\nAlso authentication on every page or folder is possible.<br />\r\n<strong><br />\r\n</strong>\r\n<h2><strong>Respond to this page</strong></h2>\r\nYou can repond to this page by filling in the form at the bottom of this page.<br />\r\n<br />\r\n<cms:plugin allow_unregistered="false" plugin_id="comments">Comments form</cms:plugin>', '1', 'GO CMS is improved! - respond', 'go cms improved respond', 'go, cms, improved, respond', 1, '1', '', 1, 0),
(5, 1, 'html', 138, 1159524352, 1161259814, 'Search.html', '<h1>Search</h1>\r\nThis page uses the search plugin to search this site.<br />\r\n<br />\r\n<cms:plugin plugin_id="search">Search</cms:plugin>', '1', 'Search', 'search', 'search', 5, '0', '', 1, 0),
(6, 1, 'html', 190, 1159524404, 1161259857, 'Member directory.html', '<h1>Member directory</h1>\r\nThis page uses the &quot;User search&quot; plugin to search the Group-Office user database.<br />\r\n<br />\r\n<cms:plugin plugin_id="users">User search</cms:plugin>', '1', 'Member directory', 'member directory', 'member, directory', 6, '0', '', 1, 0);

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `cms_folders`
-- 

CREATE TABLE IF NOT EXISTS `cms_folders` (
  `id` int(11) NOT NULL default '0',
  `parent_id` int(11) NOT NULL default '0',
  `ctime` int(11) NOT NULL,
  `mtime` int(11) NOT NULL default '0',
  `name` char(255) NOT NULL default '',
  `disabled` enum('0','1') NOT NULL default '0',
  `priority` int(11) NOT NULL default '0',
  `multipage` enum('0','1') default NULL,
  `template_item_id` int(11) NOT NULL default '0',
  `acl` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `parent_id` (`parent_id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `cms_folders`
-- 

INSERT INTO `cms_folders` (`id`, `parent_id`, `ctime`, `mtime`, `name`, `disabled`, `priority`, `multipage`, `template_item_id`, `acl`) VALUES 
(1, 0, 1159522197, 1159522197, 'www.example.com', '0', 1, NULL, 1, 0),
(2, 1, 1159523293, 1159523910, 'News', '0', 4, '0', 1, 0);

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `cms_galleries`
-- 

CREATE TABLE IF NOT EXISTS `cms_galleries` (
  `id` int(11) NOT NULL,
  `site_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `cms_galleries`
-- 


-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `cms_languages`
-- 

CREATE TABLE IF NOT EXISTS `cms_languages` (
  `id` int(11) NOT NULL default '0',
  `site_id` int(11) NOT NULL default '0',
  `template_item_id` int(11) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `title` varchar(50) NOT NULL default '',
  `description` text NOT NULL,
  `keywords` text NOT NULL,
  `image_url` varchar(255) NOT NULL default '',
  `sort_order` int(11) NOT NULL default '0',
  `root_folder_id` int(11) NOT NULL default '0',
  `language_code` char(2) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `site_id` (`site_id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `cms_languages`
-- 


-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `cms_settings`
-- 

CREATE TABLE IF NOT EXISTS `cms_settings` (
  `user_id` int(11) NOT NULL default '0',
  `sort_field` varchar(20) NOT NULL default '',
  `sort_order` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`user_id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `cms_settings`
-- 

INSERT INTO `cms_settings` (`user_id`, `sort_field`, `sort_order`) VALUES 
(1, 'cms_files.priority', 'ASC');

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `cms_sites`
-- 

CREATE TABLE IF NOT EXISTS `cms_sites` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `acl_write` int(11) NOT NULL default '0',
  `allow_properties` enum('0','1') NOT NULL default '0',
  `domain` varchar(100) NOT NULL default '',
  `webmaster` varchar(100) NOT NULL default '',
  `publish_style` enum('0','1','2') NOT NULL default '0',
  `publish_path` varchar(100) NOT NULL default '',
  `template_id` int(11) NOT NULL default '0',
  `root_folder_id` int(11) NOT NULL default '0',
  `start_file_id` int(11) NOT NULL,
  `language` char(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `cms_sites`
-- 

INSERT INTO `cms_sites` (`id`, `user_id`, `acl_write`, `allow_properties`, `domain`, `webmaster`, `publish_style`, `publish_path`, `template_id`, `root_folder_id`, `start_file_id`, `language`, `name`) VALUES 
(1, 1, 34, '0', 'www.example.com', 'webmaster@example.com', '0', '', 1, 1, 0, 'nl', '');

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `cms_templates`
-- 

CREATE TABLE IF NOT EXISTS `cms_templates` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `style` text NOT NULL,
  `additional_style` text NOT NULL,
  `print_style` text NOT NULL,
  `restrict_editor` enum('0','1') NOT NULL default '0',
  `acl_read` int(11) NOT NULL default '0',
  `acl_write` int(11) NOT NULL default '0',
  `doctype` text NOT NULL,
  `login_template_item_id` int(11) NOT NULL default '0',
  `fckeditor_styles` text NOT NULL,
  `head` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `cms_templates`
-- 

INSERT INTO `cms_templates` (`id`, `user_id`, `name`, `style`, `additional_style`, `print_style`, `restrict_editor`, `acl_read`, `acl_write`, `doctype`, `login_template_item_id`, `fckeditor_styles`, `head`) VALUES 
(1, 1, 'Example theme', '.error{\r\ncolor:red;\r\n}\r\n.login{\r\nfont-style:italic;\r\n}\r\n\r\n.logout{\r\nfont-weight:bold;\r\n}\r\n\r\n#start image gallery styles\r\na.ig_pagination:link,a.ig_pagination:visited,a.ig_pagination:active{\r\nfont-size:14px;\r\ncolor:black;\r\nmargin:14px;\r\n}\r\n\r\na.ig_pagination:hover{\r\nfont-size:14px;\r\ncolor:red;\r\n}\r\n\r\n\r\na.ig_paginationActive:link,a.ig_paginationActive:visited,a.ig_paginationActive:active{\r\nfont-size:14px;\r\ncolor:black;\r\nmargin:14px;\r\nfont-weight:bold;\r\n}\r\n\r\na.ig_paginationActive:hover{\r\nfont-size:14px;\r\ncolor:red;\r\n}\r\n\r\n\r\n.ig_paginationDisabled{\r\nfont-size:14px;\r\ncolor:#cccccc;\r\nmargin:14px;\r\n}\r\n\r\n.ig_thumb_description{\r\n\r\nfont-weight:bold;\r\nfont-size:10px;\r\ndisplay:block;\r\n}\r\n#end image gallery styles\r\n\r\n#start comments plugin styles\r\n.add_comment_error{\r\nfont-weight:bold;\r\ncolor:red;\r\n}\r\n.add_comment_title{\r\nmargin-top:20px;\r\nmargin-bottom:3px;\r\n}\r\ntable.comments{\r\nwidth:100%;\r\n}\r\n\r\ntable.comments td{\r\nvertical-align:top;\r\n}\r\n\r\n.comment{\r\ndisplay:block;\r\nmargin-top:20px;\r\n}\r\n\r\nspan.comments_input{\r\nwidth:100%;\r\ntext-align:right;\r\ndisplay:block;\r\n}\r\ninput.comments_input{\r\nwidth:100px;\r\n}\r\n\r\nh1.comments{\r\nmargin-top:20px;\r\nmargin-bottom:3px;\r\nborder-top:1px solid black;\r\npadding-top:20px;\r\nwidth:100%;\r\n}\r\n.comment_name{\r\nfont-weight:bold;\r\ndisplay:block;\r\nwidth:100%;\r\n}\r\n\r\n.comment_date{\r\nfont-style:italic;\r\ntext-align:right;\r\ndisplay:block;\r\nwidth:100%;\r\nfont-size:10px;\r\n}\r\n\r\n#end comments plugin styles\r\n\r\nbody{\r\nmargin:0px;\r\npadding:0px;\r\nfont-family: Sans-serif, Arial;\r\nfont-size:11px;\r\nheight:100%;\r\ncolor:#666666;\r\n}\r\n\r\ntd{\r\nfont-family: Sans-serif, Arial;\r\nfont-size:12px;\r\nheight:100%;\r\ncolor:#666666;\r\n}\r\n\r\nhtml{\r\nheight:100%;\r\n}\r\n\r\nh1{\r\nfont-family: Sans-serif, Arial;\r\nfont-size:14px;\r\nfont-weight:bold;\r\ncolor: black;\r\nmargin-top:8px;\r\nmargin-bottom:3px;\r\n}\r\nh2{\r\nfont-family: Sans-serif, Arial;\r\nfont-size:12px;\r\nfont-weight:bold;\r\ncolor: black;\r\nmargin-top:8px;\r\nmargin-bottom:3px;\r\n}\r\nh3{\r\nfont-family: Sans-serif, Arial;\r\nfont-size:11px;\r\nfont-weight:bold;\r\nmargin-top:8px;\r\nmargin-bottom:0px;\r\ncolor: black;\r\n}\r\n\r\nhr{\r\nborder-top-width: 0px;\r\nborder-left-width: 0px;\r\nborder-bottom: #16336e 2px solid;\r\nborder-right-width: 0px;\r\nmargin: 0px;\r\nwidth: 100%;\r\n}\r\n\r\na:link, a:visited, a:active{\r\ncolor: #16336e;\r\nfont-size:12px;\r\nfont-family: Arial, Sans-serif;\r\ntext-decoration: none;\r\n}\r\na:hover {\r\ncolor: #3a3a3a;\r\ntext-decoration: underline;\r\n}\r\n\r\n#banners{\r\ntext-align: center;\r\nposition:absolute;\r\nbottom: 0px;\r\nwidth: 800px;\r\nheight:80px;\r\nvertical-align:middle;\r\n}\r\n\r\n.newstd{\r\nvertical-align:top;\r\nwidth:200px;\r\nborder-left:1px #16336e dashed;\r\npadding-left:10px;\r\n\r\n}\r\n.contenttd{\r\nvertical-align:top;\r\nwidth:450px;\r\nheight:100%;\r\nfont-size:12px;\r\npadding-left:10px;\r\npadding-right:10px;\r\noverflow:scroll;\r\n}\r\n\r\n.menutd{\r\nvertical-align:top;\r\nwidth:150px;\r\nborder-right:1px #16336e dashed;\r\nheight:100%;\r\n}\r\n\r\n.maintable{\r\nborder:0px;\r\nheight:100%;\r\nwidth:800px;\r\nmargin:auto;\r\n}\r\n\r\n\r\n.treeview, .treeview div{\r\nmargin-left:8px;\r\n}\r\n\r\n.treeview a:link,.treeview  a:visited,.treeview a:active{\r\nfont-weight: bold;\r\n	font-size:12px;\r\ndisplay: block;\r\ncolor: #3a3a3a;\r\npadding:3px;\r\npadding-right:0;\r\n}\r\n\r\n.treeview a:hover{\r\ncolor:#d67527;\r\ntext-decoration:none;\r\n}\r\n\r\n.treeview div a:link,.treeview div  a:visited,.treeview div a:active{\r\nfont-weight: normal;\r\n	font-size:11px;\r\ndisplay: block;\r\ncolor: #3a3a3a;\r\npadding:2px;\r\npadding-right:0;\r\nborder:0px;\r\n}\r\n\r\n.treeview div a:hover{\r\ncolor: #16336e;\r\ntext-decoration:none;\r\n}\r\n\r\n\r\n.treeview div a.menu_active:link,\r\n.treeview div a.menu_active:visited,\r\n.treeview div a.menu_active:active{\r\nfont-weight: normal;\r\nfont-style:italic;\r\nfont-size:11px;\r\ndisplay: block;\r\ncolor: #16336e;\r\npadding:2px;\r\npadding-right:0;\r\n}\r\n\r\n\r\n.treeview div div a.menu_active:link,\r\n.treeview div div a.menu_active:visited,\r\n.treeview div div a.menu_active:active{\r\nfont-style: italic;\r\nfont-size:11px;\r\ndisplay: block;\r\ncolor: #16336e;\r\npadding:2px;\r\npadding-right:0;\r\n}\r\n\r\n\r\n.treeview div a.menu_active:hover{\r\ncolor: #d67527;\r\ntext-decoration:none;\r\n}\r\n\r\n\r\na.menu:link,a.menu:visited,a.menu:active{\r\nfont-weight: bold;\r\n	font-size:12px;\r\ndisplay: block;\r\ncolor: #3a3a3a;\r\npadding:3px;\r\npadding-left:11px;\r\npadding-right:0;\r\n}\r\n\r\na.menu:hover{\r\ncolor:#d67527;\r\ntext-decoration:none;\r\n}', 'body{\r\nfont-family: Sans-serif, Arial;\r\nfont-size:11px;\r\nheight:100%;\r\ncolor:#666666;\r\n}\r\n\r\ntd{\r\nfont-family: Sans-serif, Arial;\r\nfont-size:10px;\r\nheight:100%;\r\ncolor:#666666;\r\n}\r\n\r\nhtml{\r\nheight:100%;\r\n}\r\n\r\nh1{\r\nfont-family: Sans-serif, Arial;\r\nfont-size:14px;\r\nfont-weight:bold;\r\ncolor: black;\r\nmargin-top:8px;\r\nmargin-bottom:3px;\r\n}\r\nh2{\r\nfont-family: Sans-serif, Arial;\r\nfont-size:12px;\r\nfont-weight:bold;\r\ncolor: black;\r\nmargin-top:8px;\r\nmargin-bottom:3px;\r\n}\r\nh3{\r\nfont-family: Sans-serif, Arial;\r\nfont-size:11px;\r\nfont-weight:bold;\r\nmargin-top:8px;\r\nmargin-bottom:0px;\r\ncolor: black;\r\n}\r\n\r\nhr{\r\nborder-top-width: 0px;\r\nborder-left-width: 0px;\r\nborder-bottom: #199781 2px solid;\r\nborder-right-width: 0px;\r\nmargin: 0px;\r\nwidth: 100%;\r\n}\r\n\r\na:link, a:visited, a:active{\r\ncolor: #199781;\r\nfont-size:11px;\r\nfont-family: Arial, Sans-serif;\r\ntext-decoration: none;\r\n}\r\na:hover {\r\ncolor: #3a3a3a;\r\ntext-decoration: underline;\r\n}\r\n\r\n', '', '0', 32, 33, '<?xml version="1.0" encoding="UTF-8"?>\r\n<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"  http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">', 1, '', '');

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `cms_template_items`
-- 

CREATE TABLE IF NOT EXISTS `cms_template_items` (
  `id` int(11) NOT NULL default '0',
  `template_id` int(11) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `content` text NOT NULL,
  `page` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `template_id` (`template_id`),
  KEY `page` (`page`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `cms_template_items`
-- 

INSERT INTO `cms_template_items` (`id`, `template_id`, `name`, `content`, `page`) VALUES 
(1, 1, 'Main', '<table class="maintable" align="center" cellpadding="0" cellspacing="0">\r\n<tr>\r\n	<td colspan="3" style="width:100%px;height:60px;vertical-align:top;">\r\n	<img src="/groupoffice/themes/Default/images/GOCOM.gif" style="border:0px;margin-top:10px;" />\r\n	</td>\r\n</tr>\r\n<tr>\r\n<td style="height:22px;background-color:#f1f1f1;border-top:2px solid #16336e;border-bottom:1px #16336e dashed;padding-right:5px;" align="right" colspan="3">\r\n<php>\r\nif($GLOBALS[''GO_SECURITY'']->logged_in())\r\n{\r\necho ''Logged in as ''.$_SESSION[''GO_SESSION''][''name''].'' - <logout class="logout" text="logout" goto_url="index.php" />'';\r\n}else\r\n{\r\necho ''<login class="login" goto_url="groupoffice/" text="login &gt; &gt" />'';\r\n}\r\n</php>\r\n	</td>\r\n</tr>\r\n\r\n<tr>\r\n	<td class="menutd">\r\n<treeview class="treeview" item_active_class="menu_active" />\r\n<php>\r\nif($GLOBALS[''GO_SECURITY'']->logged_in())\r\n{\r\nif($GLOBALS[''GO_SECURITY'']->has_permission($GLOBALS[''GO_SECURITY'']->user_id, $GLOBALS[''cms_site'']->site[''acl_write'']))\r\n{\r\necho ''<admin class="menu" text="Admin" />'';\r\n}\r\n}\r\n</php>\r\n	</td>\r\n	<td class="contenttd">\r\n	<content read_more_text="read&nbsp;more&nbsp;>>" max_length="100"  />\r\n	</td>\r\n	<td class="newstd">\r\n	<h1 style="margin-bottom:0px;">News</h1>\r\n	<hr />\r\n<hot_items read_more_text="read&nbsp;more&nbsp;>>" read_more_class="readmore" print_title="true" max_length="100" title_class="news_title" class="news" />\r\n\r\n	</td>\r\n</tr>\r\n</table>', '1');

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `countries`
-- 

CREATE TABLE IF NOT EXISTS `countries` (
  `id` int(11) NOT NULL default '0',
  `name` varchar(64) NOT NULL default '',
  `iso_code_2` char(2) NOT NULL default '',
  `iso_code_3` char(3) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `countries`
-- 

INSERT INTO `countries` (`id`, `name`, `iso_code_2`, `iso_code_3`) VALUES 
(1, 'Afghanistan', 'AF', 'AFG'),
(2, 'Albania', 'AL', 'ALB'),
(3, 'Algeria', 'DZ', 'DZA'),
(4, 'American Samoa', 'AS', 'ASM'),
(5, 'Andorra', 'AD', 'AND'),
(6, 'Angola', 'AO', 'AGO'),
(7, 'Anguilla', 'AI', 'AIA'),
(8, 'Antarctica', 'AQ', 'ATA'),
(9, 'Antigua and Barbuda', 'AG', 'ATG'),
(10, 'Argentina', 'AR', 'ARG'),
(11, 'Armenia', 'AM', 'ARM'),
(12, 'Aruba', 'AW', 'ABW'),
(13, 'Australia', 'AU', 'AUS'),
(14, 'Austria', 'AT', 'AUT'),
(15, 'Azerbaijan', 'AZ', 'AZE'),
(16, 'Bahamas', 'BS', 'BHS'),
(17, 'Bahrain', 'BH', 'BHR'),
(18, 'Bangladesh', 'BD', 'BGD'),
(19, 'Barbados', 'BB', 'BRB'),
(20, 'Belarus', 'BY', 'BLR'),
(21, 'Belgium', 'BE', 'BEL'),
(22, 'Belize', 'BZ', 'BLZ'),
(23, 'Benin', 'BJ', 'BEN'),
(24, 'Bermuda', 'BM', 'BMU'),
(25, 'Bhutan', 'BT', 'BTN'),
(26, 'Bolivia', 'BO', 'BOL'),
(27, 'Bosnia and Herzegowina', 'BA', 'BIH'),
(28, 'Botswana', 'BW', 'BWA'),
(29, 'Bouvet Island', 'BV', 'BVT'),
(30, 'Brazil', 'BR', 'BRA'),
(31, 'British Indian Ocean Territory', 'IO', 'IOT'),
(32, 'Brunei Darussalam', 'BN', 'BRN'),
(33, 'Bulgaria', 'BG', 'BGR'),
(34, 'Burkina Faso', 'BF', 'BFA'),
(35, 'Burundi', 'BI', 'BDI'),
(36, 'Cambodia', 'KH', 'KHM'),
(37, 'Cameroon', 'CM', 'CMR'),
(38, 'Canada', 'CA', 'CAN'),
(39, 'Cape Verde', 'CV', 'CPV'),
(40, 'Cayman Islands', 'KY', 'CYM'),
(41, 'Central African Republic', 'CF', 'CAF'),
(42, 'Chad', 'TD', 'TCD'),
(43, 'Chile', 'CL', 'CHL'),
(44, 'China', 'CN', 'CHN'),
(45, 'Christmas Island', 'CX', 'CXR'),
(46, 'Cocos (Keeling) Islands', 'CC', 'CCK'),
(47, 'Colombia', 'CO', 'COL'),
(48, 'Comoros', 'KM', 'COM'),
(49, 'Congo', 'CG', 'COG'),
(50, 'Cook Islands', 'CK', 'COK'),
(51, 'Costa Rica', 'CR', 'CRI'),
(52, 'Cote D''Ivoire', 'CI', 'CIV'),
(53, 'Croatia', 'HR', 'HRV'),
(54, 'Cuba', 'CU', 'CUB'),
(55, 'Cyprus', 'CY', 'CYP'),
(56, 'Czech Republic', 'CZ', 'CZE'),
(57, 'Denmark', 'DK', 'DNK'),
(58, 'Djibouti', 'DJ', 'DJI'),
(59, 'Dominica', 'DM', 'DMA'),
(60, 'Dominican Republic', 'DO', 'DOM'),
(61, 'East Timor', 'TP', 'TMP'),
(62, 'Ecuador', 'EC', 'ECU'),
(63, 'Egypt', 'EG', 'EGY'),
(64, 'El Salvador', 'SV', 'SLV'),
(65, 'Equatorial Guinea', 'GQ', 'GNQ'),
(66, 'Eritrea', 'ER', 'ERI'),
(67, 'Estonia', 'EE', 'EST'),
(68, 'Ethiopia', 'ET', 'ETH'),
(69, 'Falkland Islands (Malvinas)', 'FK', 'FLK'),
(70, 'Faroe Islands', 'FO', 'FRO'),
(71, 'Fiji', 'FJ', 'FJI'),
(72, 'Finland', 'FI', 'FIN'),
(73, 'France', 'FR', 'FRA'),
(74, 'France, Metropolitan', 'FX', 'FXX'),
(75, 'French Guiana', 'GF', 'GUF'),
(76, 'French Polynesia', 'PF', 'PYF'),
(77, 'French Southern Territories', 'TF', 'ATF'),
(78, 'Gabon', 'GA', 'GAB'),
(79, 'Gambia', 'GM', 'GMB'),
(80, 'Georgia', 'GE', 'GEO'),
(81, 'Germany', 'DE', 'DEU'),
(82, 'Ghana', 'GH', 'GHA'),
(83, 'Gibraltar', 'GI', 'GIB'),
(84, 'Greece', 'GR', 'GRC'),
(85, 'Greenland', 'GL', 'GRL'),
(86, 'Grenada', 'GD', 'GRD'),
(87, 'Guadeloupe', 'GP', 'GLP'),
(88, 'Guam', 'GU', 'GUM'),
(89, 'Guatemala', 'GT', 'GTM'),
(90, 'Guinea', 'GN', 'GIN'),
(91, 'Guinea-bissau', 'GW', 'GNB'),
(92, 'Guyana', 'GY', 'GUY'),
(93, 'Haiti', 'HT', 'HTI'),
(94, 'Heard and Mc Donald Islands', 'HM', 'HMD'),
(95, 'Honduras', 'HN', 'HND'),
(96, 'Hong Kong', 'HK', 'HKG'),
(97, 'Hungary', 'HU', 'HUN'),
(98, 'Iceland', 'IS', 'ISL'),
(99, 'India', 'IN', 'IND'),
(100, 'Indonesia', 'ID', 'IDN'),
(101, 'Iran (Islamic Republic of)', 'IR', 'IRN'),
(102, 'Iraq', 'IQ', 'IRQ'),
(103, 'Ireland', 'IE', 'IRL'),
(104, 'Israel', 'IL', 'ISR'),
(105, 'Italy', 'IT', 'ITA'),
(106, 'Jamaica', 'JM', 'JAM'),
(107, 'Japan', 'JP', 'JPN'),
(108, 'Jordan', 'JO', 'JOR'),
(109, 'Kazakhstan', 'KZ', 'KAZ'),
(110, 'Kenya', 'KE', 'KEN'),
(111, 'Kiribati', 'KI', 'KIR'),
(112, 'Korea, Democratic People''s Republic of', 'KP', 'PRK'),
(113, 'Korea, Republic of', 'KR', 'KOR'),
(114, 'Kuwait', 'KW', 'KWT'),
(115, 'Kyrgyzstan', 'KG', 'KGZ'),
(116, 'Lao People''s Democratic Republic', 'LA', 'LAO'),
(117, 'Latvia', 'LV', 'LVA'),
(118, 'Lebanon', 'LB', 'LBN'),
(119, 'Lesotho', 'LS', 'LSO'),
(120, 'Liberia', 'LR', 'LBR'),
(121, 'Libyan Arab Jamahiriya', 'LY', 'LBY'),
(122, 'Liechtenstein', 'LI', 'LIE'),
(123, 'Lithuania', 'LT', 'LTU'),
(124, 'Luxembourg', 'LU', 'LUX'),
(125, 'Macau', 'MO', 'MAC'),
(126, 'Macedonia', 'MK', 'MKD'),
(127, 'Madagascar', 'MG', 'MDG'),
(128, 'Malawi', 'MW', 'MWI'),
(129, 'Malaysia', 'MY', 'MYS'),
(130, 'Maldives', 'MV', 'MDV'),
(131, 'Mali', 'ML', 'MLI'),
(132, 'Malta', 'MT', 'MLT'),
(133, 'Marshall Islands', 'MH', 'MHL'),
(134, 'Martinique', 'MQ', 'MTQ'),
(135, 'Mauritania', 'MR', 'MRT'),
(136, 'Mauritius', 'MU', 'MUS'),
(137, 'Mayotte', 'YT', 'MYT'),
(138, 'Mexico', 'MX', 'MEX'),
(139, 'Micronesia, Federated States of', 'FM', 'FSM'),
(140, 'Moldova, Republic of', 'MD', 'MDA'),
(141, 'Monaco', 'MC', 'MCO'),
(142, 'Mongolia', 'MN', 'MNG'),
(143, 'Montserrat', 'MS', 'MSR'),
(144, 'Morocco', 'MA', 'MAR'),
(145, 'Mozambique', 'MZ', 'MOZ'),
(146, 'Myanmar', 'MM', 'MMR'),
(147, 'Namibia', 'NA', 'NAM'),
(148, 'Nauru', 'NR', 'NRU'),
(149, 'Nepal', 'NP', 'NPL'),
(150, 'Netherlands', 'NL', 'NLD'),
(151, 'Netherlands Antilles', 'AN', 'ANT'),
(152, 'New Caledonia', 'NC', 'NCL'),
(153, 'New Zealand', 'NZ', 'NZL'),
(154, 'Nicaragua', 'NI', 'NIC'),
(155, 'Niger', 'NE', 'NER'),
(156, 'Nigeria', 'NG', 'NGA'),
(157, 'Niue', 'NU', 'NIU'),
(158, 'Norfolk Island', 'NF', 'NFK'),
(159, 'Northern Mariana Islands', 'MP', 'MNP'),
(160, 'Norway', 'NO', 'NOR'),
(161, 'Oman', 'OM', 'OMN'),
(162, 'Pakistan', 'PK', 'PAK'),
(163, 'Palau', 'PW', 'PLW'),
(164, 'Panama', 'PA', 'PAN'),
(165, 'Papua New Guinea', 'PG', 'PNG'),
(166, 'Paraguay', 'PY', 'PRY'),
(167, 'Peru', 'PE', 'PER'),
(168, 'Philippines', 'PH', 'PHL'),
(169, 'Pitcairn', 'PN', 'PCN'),
(170, 'Poland', 'PL', 'POL'),
(171, 'Portugal', 'PT', 'PRT'),
(172, 'Puerto Rico', 'PR', 'PRI'),
(173, 'Qatar', 'QA', 'QAT'),
(174, 'Reunion', 'RE', 'REU'),
(175, 'Romania', 'RO', 'ROM'),
(176, 'Russian Federation', 'RU', 'RUS'),
(177, 'Rwanda', 'RW', 'RWA'),
(178, 'Saint Kitts and Nevis', 'KN', 'KNA'),
(179, 'Saint Lucia', 'LC', 'LCA'),
(180, 'Saint Vincent and the Grenadines', 'VC', 'VCT'),
(181, 'Samoa', 'WS', 'WSM'),
(182, 'San Marino', 'SM', 'SMR'),
(183, 'Sao Tome and Principe', 'ST', 'STP'),
(184, 'Saudi Arabia', 'SA', 'SAU'),
(185, 'Senegal', 'SN', 'SEN'),
(186, 'Seychelles', 'SC', 'SYC'),
(187, 'Sierra Leone', 'SL', 'SLE'),
(188, 'Singapore', 'SG', 'SGP'),
(189, 'Slovakia (Slovak Republic)', 'SK', 'SVK'),
(190, 'Slovenia', 'SI', 'SVN'),
(191, 'Solomon Islands', 'SB', 'SLB'),
(192, 'Somalia', 'SO', 'SOM'),
(193, 'South Africa', 'ZA', 'ZAF'),
(194, 'South Georgia', 'GS', 'SGS'),
(195, 'Spain', 'ES', 'ESP'),
(196, 'Sri Lanka', 'LK', 'LKA'),
(197, 'St. Helena', 'SH', 'SHN'),
(198, 'St. Pierre and Miquelon', 'PM', 'SPM'),
(199, 'Sudan', 'SD', 'SDN'),
(200, 'Suriname', 'SR', 'SUR'),
(201, 'Svalbard and Jan Mayen Islands', 'SJ', 'SJM'),
(202, 'Swaziland', 'SZ', 'SWZ'),
(203, 'Sweden', 'SE', 'SWE'),
(204, 'Switzerland', 'CH', 'CHE'),
(205, 'Syrian Arab Republic', 'SY', 'SYR'),
(206, 'Taiwan', 'TW', 'TWN'),
(207, 'Tajikistan', 'TJ', 'TJK'),
(208, 'Tanzania, United Republic of', 'TZ', 'TZA'),
(209, 'Thailand', 'TH', 'THA'),
(210, 'Togo', 'TG', 'TGO'),
(211, 'Tokelau', 'TK', 'TKL'),
(212, 'Tonga', 'TO', 'TON'),
(213, 'Trinidad and Tobago', 'TT', 'TTO'),
(214, 'Tunisia', 'TN', 'TUN'),
(215, 'Turkey', 'TR', 'TUR'),
(216, 'Turkmenistan', 'TM', 'TKM'),
(217, 'Turks and Caicos Islands', 'TC', 'TCA'),
(218, 'Tuvalu', 'TV', 'TUV'),
(219, 'Uganda', 'UG', 'UGA'),
(220, 'Ukraine', 'UA', 'UKR'),
(221, 'United Arab Emirates', 'AE', 'ARE'),
(222, 'United Kingdom', 'GB', 'GBR'),
(223, 'United States', 'US', 'USA'),
(224, 'United States Minor Outlying Islands', 'UM', 'UMI'),
(225, 'Uruguay', 'UY', 'URY'),
(226, 'Uzbekistan', 'UZ', 'UZB'),
(227, 'Vanuatu', 'VU', 'VUT'),
(228, 'Vatican City State (Holy See)', 'VA', 'VAT'),
(229, 'Venezuela', 'VE', 'VEN'),
(230, 'Viet Nam', 'VN', 'VNM'),
(231, 'Virgin Islands (British)', 'VG', 'VGB'),
(232, 'Virgin Islands (U.S.)', 'VI', 'VIR'),
(233, 'Wallis and Futuna Islands', 'WF', 'WLF'),
(234, 'Western Sahara', 'EH', 'ESH'),
(235, 'Yemen', 'YE', 'YEM'),
(236, 'Yugoslavia', 'YU', 'YUG'),
(237, 'Zaire', 'ZR', 'ZAR'),
(238, 'Zambia', 'ZM', 'ZMB'),
(239, 'Zimbabwe', 'ZW', 'ZWE');

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `db_sequence`
-- 

CREATE TABLE IF NOT EXISTS `db_sequence` (
  `seq_name` varchar(50) NOT NULL default '',
  `nextid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`seq_name`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `db_sequence`
-- 

INSERT INTO `db_sequence` (`seq_name`, `nextid`) VALUES 
('acl_items', 60),
('users', 3),
('groups', 2),
('cal_calendars', 4),
('cal_holidays', 45),
('cms_templates', 1),
('cms_template_items', 1),
('cms_folders', 2),
('cms_sites', 1),
('cms_files', 6),
('cms_comments', 2),
('ab_addressbooks', 4),
('sync_devices', 3),
('ab_companies', 7),
('ab_contacts', 4),
('links', 8),
('pmProjects', 2),
('pmFees', 1),
('pmHours', 2),
('fs_statuses', 3),
('fs_status_history', 1);

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `emAccounts`
-- 

CREATE TABLE IF NOT EXISTS `emAccounts` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `type` varchar(4) NOT NULL default '',
  `host` varchar(100) NOT NULL default '',
  `port` int(11) NOT NULL default '0',
  `use_ssl` enum('0','1') NOT NULL default '0',
  `novalidate_cert` enum('0','1') NOT NULL default '0',
  `username` varchar(50) NOT NULL default '',
  `password` varchar(64) NOT NULL default '',
  `name` varchar(100) NOT NULL default '',
  `email` varchar(100) NOT NULL default '',
  `signature` text NOT NULL,
  `standard` tinyint(4) NOT NULL default '0',
  `mbroot` varchar(30) NOT NULL default '',
  `sent` varchar(100) NOT NULL default '',
  `drafts` varchar(100) NOT NULL default '',
  `trash` varchar(100) NOT NULL default '',
  `spam` varchar(100) NOT NULL default '',
  `spamtag` varchar(20) NOT NULL default '',
  `examine_headers` enum('0','1') NOT NULL,
  `auto_check` enum('0','1') NOT NULL,
  `enable_vacation` enum('0','1') NOT NULL,
  `vacation_subject` varchar(100) NOT NULL,
  `vacation_text` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `emAccounts`
-- 


-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `emFilters`
-- 

CREATE TABLE IF NOT EXISTS `emFilters` (
  `id` int(11) NOT NULL default '0',
  `account_id` int(11) NOT NULL default '0',
  `field` varchar(20) NOT NULL default '0',
  `keyword` varchar(100) NOT NULL default '0',
  `folder` varchar(100) NOT NULL default '0',
  `priority` int(11) NOT NULL default '0',
  `mark_as_read` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `emFilters`
-- 


-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `emFolders`
-- 

CREATE TABLE IF NOT EXISTS `emFolders` (
  `id` int(11) NOT NULL default '0',
  `account_id` int(11) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `subscribed` enum('0','1') NOT NULL default '0',
  `parent_id` int(11) NOT NULL default '0',
  `delimiter` char(1) NOT NULL default '',
  `attributes` int(11) NOT NULL default '0',
  `sort_order` tinyint(4) NOT NULL default '0',
  `msgcount` int(11) NOT NULL default '0',
  `unseen` int(11) NOT NULL default '0',
  `auto_check` enum('0','1') NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `account_id` (`account_id`),
  KEY `parent_id` (`parent_id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `emFolders`
-- 


-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `em_links`
-- 

CREATE TABLE IF NOT EXISTS `em_links` (
  `link_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `from` varchar(255) NOT NULL,
  `to` text NOT NULL,
  `subject` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `path` varchar(255) NOT NULL,
  PRIMARY KEY  (`link_id`),
  KEY `account_id` (`user_id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `em_links`
-- 


-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `em_settings`
-- 

CREATE TABLE IF NOT EXISTS `em_settings` (
  `user_id` int(11) NOT NULL default '0',
  `send_format` varchar(10) NOT NULL default '',
  `add_recievers` int(11) NOT NULL default '0',
  `add_senders` int(11) NOT NULL default '0',
  `request_notification` enum('0','1') NOT NULL default '0',
  `charset` varchar(20) NOT NULL default '',
  `show_preview` enum('0','1') NOT NULL default '1',
  `beep` enum('0','1') NOT NULL,
  `open_popup` enum('0','1') NOT NULL,
  PRIMARY KEY  (`user_id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `em_settings`
-- 

INSERT INTO `em_settings` (`user_id`, `send_format`, `add_recievers`, `add_senders`, `request_notification`, `charset`, `show_preview`, `beep`, `open_popup`) VALUES 
(1, 'text/HTML', 0, 0, '0', 'UTF-8', '1', '1', '0');

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `fs_links`
-- 

CREATE TABLE IF NOT EXISTS `fs_links` (
  `link_id` int(11) NOT NULL default '0',
  `path` varchar(255) NOT NULL default '',
  `status_id` int(11) NOT NULL,
  `ctime` int(11) NOT NULL,
  `mtime` int(11) NOT NULL,
  PRIMARY KEY  (`link_id`),
  KEY `path` (`path`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `fs_links`
-- 


-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `fs_settings`
-- 

CREATE TABLE IF NOT EXISTS `fs_settings` (
  `user_id` int(11) NOT NULL,
  `notify` enum('0','1') NOT NULL,
  `open_properties` enum('0','1') NOT NULL,
  `show_files_on_summary` enum('0','1') NOT NULL,
  `use_gota` enum('0','1') NOT NULL,
  PRIMARY KEY  (`user_id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `fs_settings`
-- 

INSERT INTO `fs_settings` (`user_id`, `notify`, `open_properties`, `show_files_on_summary`, `use_gota`) VALUES 
(1, '0', '0', '0', '1');

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `fs_shares`
-- 

CREATE TABLE IF NOT EXISTS `fs_shares` (
  `user_id` int(11) NOT NULL default '0',
  `link_id` int(11) default NULL,
  `path` varchar(200) NOT NULL default '',
  `type` varchar(50) NOT NULL default '',
  `acl_read` int(11) NOT NULL default '0',
  `acl_write` int(11) NOT NULL default '0',
  PRIMARY KEY  (`path`),
  KEY `user_id` (`user_id`),
  KEY `type` (`type`),
  KEY `link_id` (`link_id`),
  KEY `link_id_2` (`link_id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `fs_shares`
-- 

INSERT INTO `fs_shares` (`user_id`, `link_id`, `path`, `type`, `acl_read`, `acl_write`) VALUES 
(1, NULL, '/var/www/groupoffice/local/cms/sites/1', 'site', 35, 34);

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `fs_statuses`
-- 

CREATE TABLE IF NOT EXISTS `fs_statuses` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `fs_statuses`
-- 

INSERT INTO `fs_statuses` (`id`, `name`) VALUES 
(2, 'Waiting for approval'),
(3, 'Approved');

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `fs_status_history`
-- 

CREATE TABLE IF NOT EXISTS `fs_status_history` (
  `id` int(11) NOT NULL,
  `link_id` int(11) NOT NULL,
  `status_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ctime` int(11) NOT NULL,
  `comments` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `link_id` (`link_id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `fs_status_history`
-- 

INSERT INTO `fs_status_history` (`id`, `link_id`, `status_id`, `user_id`, `ctime`, `comments`) VALUES 
(1, 5, 2, 1, 1166535060, '');

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `groups`
-- 

CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(11) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `user_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `groups`
-- 

INSERT INTO `groups` (`id`, `name`, `user_id`) VALUES 
(1, 'Admins', 1),
(2, 'Everyone', 1);

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `ig_galleries`
-- 

CREATE TABLE IF NOT EXISTS `ig_galleries` (
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

-- 
-- Gegevens worden uitgevoerd voor tabel `ig_galleries`
-- 


-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `ig_images`
-- 

CREATE TABLE IF NOT EXISTS `ig_images` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `gallery_id` int(11) NOT NULL,
  `filename` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `site_id` (`gallery_id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `ig_images`
-- 


-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `links`
-- 

CREATE TABLE IF NOT EXISTS `links` (
  `type1` tinyint(4) NOT NULL default '0',
  `link_id1` int(11) NOT NULL default '0',
  `type2` tinyint(4) NOT NULL default '0',
  `link_id2` int(11) NOT NULL default '0',
  KEY `type1` (`type1`),
  KEY `type2` (`type2`),
  KEY `link_id1` (`link_id1`),
  KEY `link_id2` (`link_id2`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `links`
-- 


-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `log`
-- 

CREATE TABLE IF NOT EXISTS `log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `module` varchar(50) NOT NULL,
  `time` int(11) NOT NULL,
  `link_id` int(255) NOT NULL,
  `text` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `log`
-- 


-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `modules`
-- 

CREATE TABLE IF NOT EXISTS `modules` (
  `id` varchar(20) NOT NULL default '',
  `version` varchar(5) NOT NULL default '',
  `path` varchar(50) NOT NULL default '',
  `sort_order` int(11) NOT NULL default '0',
  `admin_menu` enum('0','1') NOT NULL default '0',
  `acl_read` int(11) NOT NULL default '0',
  `acl_write` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `modules`
-- 

INSERT INTO `modules` (`id`, `version`, `path`, `sort_order`, `admin_menu`, `acl_read`, `acl_write`) VALUES 
('modules', '1.0', '', 130, '1', 1, 2),
('addressbook', '2.6', '', 20, '0', 4, 5),
('calendar', '3.1', '', 40, '0', 6, 7),
('email', '2.5', '', 30, '0', 8, 9),
('filesystem', '2.0', '', 70, '0', 10, 11),
('groups', '1.0', '', 120, '1', 12, 13),
('cms', '3.0', '', 90, '0', 14, 15),
('phpsysinfo', '1.0', '', 140, '1', 16, 17),
('projects', '2.2', '', 80, '0', 18, 19),
('summary', '1.0', '', 10, '0', 20, 21),
('todos', '1.1', '', 20, '0', 22, 23),
('gallery', '1.1', '', 100, '0', 24, 25),
('users', '1.2', '', 110, '1', 26, 27),
('notes', '1.2', '', 60, '0', 28, 29),
('translate', '1.0', '', 120, '0', 54, 55);

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `no_notes`
-- 

CREATE TABLE IF NOT EXISTS `no_notes` (
  `id` int(11) NOT NULL default '0',
  `link_id` int(11) default NULL,
  `user_id` int(11) NOT NULL default '0',
  `file_path` varchar(255) NOT NULL default '0',
  `due_date` int(11) NOT NULL default '0',
  `ctime` int(11) NOT NULL default '0',
  `mtime` int(11) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `content` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`),
  KEY `file_path` (`file_path`),
  KEY `link_id` (`link_id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `no_notes`
-- 


-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `no_settings`
-- 

CREATE TABLE IF NOT EXISTS `no_settings` (
  `user_id` int(11) NOT NULL default '0',
  `sort_field` varchar(20) NOT NULL default '',
  `sort_order` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`user_id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `no_settings`
-- 


-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `pmFees`
-- 

CREATE TABLE IF NOT EXISTS `pmFees` (
  `id` int(11) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `external_value` double NOT NULL default '0',
  `internal_value` double NOT NULL,
  `time` int(11) NOT NULL default '0',
  `acl_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `pmFees`
-- 

INSERT INTO `pmFees` (`id`, `name`, `external_value`, `internal_value`, `time`, `acl_id`) VALUES 
(1, 'Test', 70, 0, 60, 51);

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `pmHours`
-- 

CREATE TABLE IF NOT EXISTS `pmHours` (
  `id` int(11) NOT NULL default '0',
  `project_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `start_time` int(11) NOT NULL default '0',
  `end_time` int(11) NOT NULL default '0',
  `break_time` int(11) NOT NULL default '0',
  `units` double NOT NULL,
  `comments` text NOT NULL,
  `fee_id` int(11) NOT NULL default '0',
  `ext_fee_value` double NOT NULL default '0',
  `fee_time` int(11) NOT NULL default '0',
  `int_fee_value` double NOT NULL default '0',
  `event_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `pmHours`
-- 


-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `pmProjects`
-- 

CREATE TABLE IF NOT EXISTS `pmProjects` (
  `id` int(11) NOT NULL default '0',
  `link_id` int(11) default NULL,
  `user_id` int(11) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `customer` varchar(50) NOT NULL,
  `description` varchar(50) NOT NULL default '',
  `contact_id` int(11) NOT NULL default '0',
  `company_id` int(11) NOT NULL default '0',
  `project_id` int(11) NOT NULL default '0',
  `res_user_id` int(11) NOT NULL default '0',
  `acl_read` int(11) NOT NULL default '0',
  `acl_write` int(11) NOT NULL default '0',
  `acl_book` int(11) NOT NULL,
  `comments` text NOT NULL,
  `ctime` int(11) NOT NULL default '0',
  `mtime` int(11) NOT NULL default '0',
  `start_date` int(11) NOT NULL default '0',
  `end_date` int(11) NOT NULL default '0',
  `status` tinyint(10) NOT NULL default '0',
  `probability` tinyint(4) NOT NULL default '0',
  `budget` double NOT NULL default '0',
  `billed` double NOT NULL,
  `unit_value` int(11) NOT NULL,
  `calendar_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `contact_id` (`contact_id`),
  KEY `user_id` (`user_id`),
  KEY `link_id` (`link_id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `pmProjects`
-- 


-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `pmStatuses`
-- 

CREATE TABLE IF NOT EXISTS `pmStatuses` (
  `id` int(11) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `pmStatuses`
-- 

INSERT INTO `pmStatuses` (`id`, `name`) VALUES 
(1, 'Offer'),
(2, 'Ongoing'),
(3, 'Waiting'),
(4, 'Done'),
(5, 'Billed');

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `pmTimers`
-- 

CREATE TABLE IF NOT EXISTS `pmTimers` (
  `user_id` int(11) NOT NULL default '0',
  `start_time` int(11) NOT NULL default '0',
  `project_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`user_id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `pmTimers`
-- 


-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `pm_settings`
-- 

CREATE TABLE IF NOT EXISTS `pm_settings` (
  `user_id` int(11) NOT NULL default '0',
  `show_projects` tinyint(4) NOT NULL default '0',
  `fee_id` int(11) NOT NULL,
  PRIMARY KEY  (`user_id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `pm_settings`
-- 

INSERT INTO `pm_settings` (`user_id`, `show_projects`, `fee_id`) VALUES 
(1, 1, 0);

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `pm_templates`
-- 

CREATE TABLE IF NOT EXISTS `pm_templates` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `acl_read` int(11) NOT NULL default '0',
  `acl_write` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `pm_templates`
-- 


-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `pm_template_events`
-- 

CREATE TABLE IF NOT EXISTS `pm_template_events` (
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

-- 
-- Gegevens worden uitgevoerd voor tabel `pm_template_events`
-- 


-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `reminders`
-- 

CREATE TABLE IF NOT EXISTS `reminders` (
  `id` int(11) NOT NULL,
  `link_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `time` int(11) NOT NULL,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `link_id` (`link_id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `reminders`
-- 


-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `settings`
-- 

CREATE TABLE IF NOT EXISTS `settings` (
  `user_id` int(11) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `value` text NOT NULL,
  PRIMARY KEY  (`user_id`,`name`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `settings`
-- 

INSERT INTO `settings` (`user_id`, `name`, `value`) VALUES 
(0, 'registration_unconfirmed_subject', 'Your account still needs activation'),
(0, 'registration_confirmation', '<html>\r\n    <head>\r\n    </head>\r\n    <body>\r\n        <font size="3" face="Verdana">Dear&nbsp;%beginning%&nbsp;%middle_name%%last_name%,<br />\r\n        <br />\r\n        Welcome to Group-Office! You can login at:<br />\r\n        <br />\r\n        %full_url%<br />\r\n        <br />\r\n        With:<br />\r\n        <br />\r\n        Username: %username%<br />\r\n        Password: %password%<br />\r\n        <br />\r\n        With kind regards,<br />\r\n        <br />\r\n        The Group-Office administrator</font>\r\n    </body>\r\n</html>'),
(0, 'registration_unconfirmed', '<html>\r\n    <head>\r\n    </head>\r\n    <body>\r\n        <font size="3" face="Verdana">Dear&nbsp;%beginning% %middle_name%%last_name%,<br />\r\n        <br />\r\n        Thank you for your registration at Group-Office. You can login when an administrator activates your account. You will recieve an e-mail with login instructions at that time.<br />\r\n        <br />\r\n        With kind regards,<br />\r\n        <br />\r\n        The Group-Office administrator</font>\r\n    </body>\r\n</html>'),
(0, 'registration_confirmation_subject', 'Welcome to Group-Office!'),
(0, 'version', '225'),
(0, 'enabled_columns_users', '0,1,2,3,4,5'),
(1, 'sort_index_users', 'lastlogin'),
(1, 'sort_asc_users', '0'),
(1, 'sort_index_addressbook_table', 'name'),
(1, 'book_units', '1'),
(3, 'sort_index_addressbook_table', 'name'),
(1, 'sort_index_projects_list', 'name'),
(1, 'sort_asc_projects_list', '1'),
(1, 'sort_asc_addressbook_table', '1');

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `se_cache`
-- 

CREATE TABLE IF NOT EXISTS `se_cache` (
  `link_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `table` varchar(50) NOT NULL,
  `id` int(11) NOT NULL,
  `module` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `link_type` int(11) NOT NULL,
  `type` varchar(20) NOT NULL,
  `keywords` text NOT NULL,
  `mtime` int(11) NOT NULL,
  PRIMARY KEY  (`link_id`,`user_id`),
  KEY `name` (`name`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `se_cache`
-- 


-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `se_last_sync_times`
-- 

CREATE TABLE IF NOT EXISTS `se_last_sync_times` (
  `user_id` int(11) NOT NULL,
  `module` varchar(50) NOT NULL,
  `last_sync_time` int(11) NOT NULL,
  PRIMARY KEY  (`user_id`,`module`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `se_last_sync_times`
-- 


-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `sum_announcements`
-- 

CREATE TABLE IF NOT EXISTS `sum_announcements` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `acl_id` int(11) NOT NULL default '0',
  `due_time` int(11) NOT NULL default '0',
  `ctime` int(11) NOT NULL default '0',
  `mtime` int(11) NOT NULL default '0',
  `title` varchar(50) NOT NULL default '',
  `content` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `due_time` (`due_time`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `sum_announcements`
-- 


-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `sync_contacts_maps`
-- 

CREATE TABLE IF NOT EXISTS `sync_contacts_maps` (
  `device_id` int(11) NOT NULL default '0',
  `contact_id` int(11) NOT NULL default '0',
  `remote_id` varchar(255) NOT NULL,
  PRIMARY KEY  (`device_id`,`contact_id`,`remote_id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `sync_contacts_maps`
-- 


-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `sync_contacts_syncs`
-- 

CREATE TABLE IF NOT EXISTS `sync_contacts_syncs` (
  `device_id` int(11) NOT NULL default '0',
  `local_last_anchor` int(11) NOT NULL default '0',
  `remote_last_anchor` char(32) NOT NULL default '',
  PRIMARY KEY  (`device_id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `sync_contacts_syncs`
-- 


-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `sync_datastores`
-- 

CREATE TABLE IF NOT EXISTS `sync_datastores` (
  `id` int(11) NOT NULL default '0',
  `device_id` int(11) NOT NULL default '0',
  `uri` varchar(100) NOT NULL default '',
  `ctype` varchar(50) NOT NULL default '',
  `ctype_version` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `sync_datastores`
-- 


-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `sync_devices`
-- 

CREATE TABLE IF NOT EXISTS `sync_devices` (
  `id` int(11) NOT NULL default '0',
  `manufacturer` varchar(50) NOT NULL default '',
  `model` varchar(50) NOT NULL default '',
  `software_version` varchar(50) NOT NULL default '',
  `user_id` int(11) NOT NULL default '0',
  `uri` varchar(128) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `sync_devices`
-- 


-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `sync_events_maps`
-- 

CREATE TABLE IF NOT EXISTS `sync_events_maps` (
  `device_id` int(11) NOT NULL default '0',
  `event_id` int(11) NOT NULL default '0',
  `remote_id` varchar(255) NOT NULL,
  `todo` enum('0','1') NOT NULL,
  PRIMARY KEY  (`device_id`,`event_id`,`remote_id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `sync_events_maps`
-- 


-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `sync_events_syncs`
-- 

CREATE TABLE IF NOT EXISTS `sync_events_syncs` (
  `device_id` int(11) NOT NULL default '0',
  `local_last_anchor` int(11) NOT NULL default '0',
  `remote_last_anchor` char(32) NOT NULL default '',
  PRIMARY KEY  (`device_id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `sync_events_syncs`
-- 


-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `sync_settings`
-- 

CREATE TABLE IF NOT EXISTS `sync_settings` (
  `user_id` int(11) NOT NULL default '0',
  `addressbook_id` int(11) NOT NULL default '0',
  `calendar_id` int(11) NOT NULL default '0',
  `sync_private` enum('0','1') NOT NULL default '0',
  `server_is_master` enum('0','1') NOT NULL default '0',
  `max_days_old` tinyint(4) NOT NULL,
  `delete_old_events` enum('0','1') NOT NULL,
  PRIMARY KEY  (`user_id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `sync_settings`
-- 

INSERT INTO `sync_settings` (`user_id`, `addressbook_id`, `calendar_id`, `sync_private`, `server_is_master`, `max_days_old`, `delete_old_events`) VALUES 
(1, 1, 1, '0', '0', 30, '0');

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `sync_todos_syncs`
-- 

CREATE TABLE IF NOT EXISTS `sync_todos_syncs` (
  `device_id` int(11) NOT NULL default '0',
  `local_last_anchor` int(11) NOT NULL default '0',
  `remote_last_anchor` char(32) NOT NULL default '',
  PRIMARY KEY  (`device_id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `sync_todos_syncs`
-- 


-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `td_reminders`
-- 

CREATE TABLE IF NOT EXISTS `td_reminders` (
  `user_id` int(11) NOT NULL default '0',
  `todo_id` int(11) NOT NULL default '0',
  `remind_time` int(11) NOT NULL default '0',
  KEY `user_id` (`user_id`),
  KEY `remind_time` (`remind_time`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `td_reminders`
-- 


-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `td_settings`
-- 

CREATE TABLE IF NOT EXISTS `td_settings` (
  `user_id` int(11) NOT NULL default '0',
  `sort_field` varchar(20) NOT NULL default '',
  `sort_order` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`user_id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `td_settings`
-- 


-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `td_todos`
-- 

CREATE TABLE IF NOT EXISTS `td_todos` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `contact_id` int(11) NOT NULL default '0',
  `company_id` int(11) NOT NULL default '0',
  `file_path` varchar(255) NOT NULL default '',
  `project_id` int(11) NOT NULL default '0',
  `ctime` int(11) NOT NULL default '0',
  `mtime` int(11) NOT NULL default '0',
  `start_time` int(11) NOT NULL default '0',
  `due_time` int(11) NOT NULL default '0',
  `completion_time` int(11) NOT NULL default '0',
  `reminder` int(11) NOT NULL default '0',
  `remind_style` enum('0','1') NOT NULL default '0',
  `status` tinyint(4) NOT NULL default '0',
  `priority` enum('0','1','2') NOT NULL default '0',
  `res_user_id` int(11) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `description` text NOT NULL,
  `location` varchar(50) NOT NULL default '',
  `background` varchar(6) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`,`res_user_id`),
  KEY `remind_time` (`reminder`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `td_todos`
-- 


-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `tp_mailing_companies`
-- 

CREATE TABLE IF NOT EXISTS `tp_mailing_companies` (
  `group_id` int(11) NOT NULL default '0',
  `company_id` int(11) NOT NULL default '0',
  KEY `group_id` (`group_id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `tp_mailing_companies`
-- 


-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `tp_mailing_contacts`
-- 

CREATE TABLE IF NOT EXISTS `tp_mailing_contacts` (
  `group_id` int(11) NOT NULL default '0',
  `contact_id` int(11) NOT NULL default '0',
  KEY `group_id` (`group_id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `tp_mailing_contacts`
-- 


-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `tp_mailing_groups`
-- 

CREATE TABLE IF NOT EXISTS `tp_mailing_groups` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `acl_read` int(11) NOT NULL default '0',
  `acl_write` int(11) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `tp_mailing_groups`
-- 


-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `tp_mailing_users`
-- 

CREATE TABLE IF NOT EXISTS `tp_mailing_users` (
  `group_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  KEY `group_id` (`group_id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `tp_mailing_users`
-- 


-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `tp_templates`
-- 

CREATE TABLE IF NOT EXISTS `tp_templates` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `type` tinyint(4) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `acl_read` int(11) NOT NULL default '0',
  `acl_write` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `tp_templates`
-- 


-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `tp_templates_content`
-- 

CREATE TABLE IF NOT EXISTS `tp_templates_content` (
  `id` int(11) NOT NULL default '0',
  `content` longblob NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `tp_templates_content`
-- 


-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `users`
-- 

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL default '0',
  `username` varchar(50) NOT NULL default '',
  `password` varchar(64) NOT NULL default '',
  `enabled` enum('0','1') NOT NULL default '1',
  `authcode` varchar(20) NOT NULL default '',
  `first_name` varchar(50) NOT NULL default '',
  `middle_name` varchar(50) NOT NULL default '',
  `last_name` varchar(100) NOT NULL,
  `initials` varchar(10) NOT NULL default '',
  `title` varchar(10) NOT NULL default '',
  `sex` enum('M','F') NOT NULL default 'M',
  `birthday` date NOT NULL default '0000-00-00',
  `email` varchar(100) NOT NULL default '',
  `company` varchar(50) NOT NULL default '',
  `department` varchar(50) NOT NULL default '',
  `function` varchar(50) NOT NULL default '',
  `home_phone` varchar(20) NOT NULL default '',
  `work_phone` varchar(20) NOT NULL default '',
  `fax` varchar(20) NOT NULL default '',
  `cellular` varchar(20) NOT NULL default '',
  `country` varchar(50) NOT NULL default '',
  `state` varchar(50) NOT NULL default '',
  `city` varchar(50) NOT NULL default '',
  `zip` varchar(10) NOT NULL default '',
  `address` varchar(100) NOT NULL default '',
  `address_no` varchar(10) NOT NULL,
  `homepage` varchar(100) NOT NULL default '',
  `work_address` varchar(100) NOT NULL default '',
  `work_address_no` varchar(10) NOT NULL,
  `work_zip` varchar(10) NOT NULL default '',
  `work_country` varchar(50) NOT NULL default '',
  `work_state` varchar(50) NOT NULL default '',
  `work_city` varchar(50) NOT NULL default '',
  `work_fax` varchar(20) NOT NULL default '',
  `acl_id` int(11) NOT NULL default '0',
  `date_format` varchar(20) NOT NULL default 'd-m-Y H:i',
  `date_seperator` char(1) NOT NULL default '-',
  `time_format` varchar(10) NOT NULL default '',
  `thousands_seperator` char(1) NOT NULL default '.',
  `decimal_seperator` char(1) NOT NULL default ',',
  `currency` char(3) NOT NULL default 'EUR',
  `mail_client` tinyint(4) NOT NULL default '1',
  `logins` int(11) NOT NULL default '0',
  `lastlogin` int(11) NOT NULL default '0',
  `registration_time` int(11) NOT NULL default '0',
  `max_rows_list` tinyint(4) NOT NULL default '15',
  `timezone` float NOT NULL default '0',
  `DST` enum('0','1','2') NOT NULL default '0',
  `start_module` varchar(50) NOT NULL default '',
  `language` varchar(20) NOT NULL default '',
  `theme` varchar(20) NOT NULL default '',
  `first_weekday` tinyint(4) NOT NULL default '0',
  `sort_name` varchar(20) NOT NULL default 'first_name',
  `use_checkbox_select` enum('0','1') NOT NULL default '0',
  `country_id` int(11) NOT NULL default '0',
  `work_country_id` int(11) NOT NULL default '0',
  `bank` varchar(50) NOT NULL default '',
  `bank_no` varchar(50) NOT NULL default '',
  `link_id` int(11) NOT NULL,
  `mtime` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `link_id` (`link_id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `users`
-- 

INSERT INTO `users` (`id`, `username`, `password`, `enabled`, `authcode`, `first_name`, `middle_name`, `last_name`, `initials`, `title`, `sex`, `birthday`, `email`, `company`, `department`, `function`, `home_phone`, `work_phone`, `fax`, `cellular`, `country`, `state`, `city`, `zip`, `address`, `address_no`, `homepage`, `work_address`, `work_address_no`, `work_zip`, `work_country`, `work_state`, `work_city`, `work_fax`, `acl_id`, `date_format`, `date_seperator`, `time_format`, `thousands_seperator`, `decimal_seperator`, `currency`, `mail_client`, `logins`, `lastlogin`, `registration_time`, `max_rows_list`, `timezone`, `DST`, `start_module`, `language`, `theme`, `first_weekday`, `sort_name`, `use_checkbox_select`, `country_id`, `work_country_id`, `bank`, `bank_no`, `link_id`, `mtime`) VALUES 
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', '1', 'jmvnqxuf', 'Group-Office', '', 'Admin', '', '', 'M', '0000-00-00', 'webmaster@example.com', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 3, 'dmY', '-', 'G:i', ',', '.', 'EUR', 1, 24, 1191914020, 1159517603, 15, 1, '1', 'summary', 'en', 'Default', 1, 'first_name', '0', 0, 0, '', '', 8, 1159517603);

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `users_groups`
-- 

CREATE TABLE IF NOT EXISTS `users_groups` (
  `group_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`group_id`,`user_id`)
) TYPE=MyISAM;

-- 
-- Gegevens worden uitgevoerd voor tabel `users_groups`
-- 

INSERT INTO `users_groups` (`group_id`, `user_id`) VALUES 
(1, 1),
(2, 1);
