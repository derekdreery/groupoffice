ALTER DATABASE CHARACTER SET utf8 COLLATE utf8_general_ci;


DROP TABLE IF EXISTS `cf_go_users`;
CREATE TABLE IF NOT EXISTS `cf_go_users` (
  `model_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`model_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------



--
-- Tabelstructuur voor tabel `go_acl`
--

DROP TABLE IF EXISTS `go_acl`;
CREATE TABLE IF NOT EXISTS `go_acl` (
  `acl_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `group_id` int(11) NOT NULL DEFAULT '0',
  `level` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`acl_id`,`user_id`,`group_id`),
  KEY `acl_id` (`acl_id`,`user_id`),
  KEY `acl_id_2` (`acl_id`,`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `go_acl_items`
--

DROP TABLE IF EXISTS `go_acl_items`;
CREATE TABLE IF NOT EXISTS `go_acl_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `description` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `go_address_format`
--

DROP TABLE IF EXISTS `go_address_format`;
CREATE TABLE IF NOT EXISTS `go_address_format` (
  `id` int(11) NOT NULL,
  `format` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `go_cache`
--

DROP TABLE IF EXISTS `go_cache`;
CREATE TABLE IF NOT EXISTS `go_cache` (
  `user_id` int(11) NOT NULL,
  `key` varchar(255) NOT NULL DEFAULT '',
  `content` longtext,
  `mtime` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`key`),
  KEY `mtime` (`mtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `go_countries`
--

DROP TABLE IF EXISTS `go_countries`;
CREATE TABLE IF NOT EXISTS `go_countries` (
  `id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(64) DEFAULT NULL,
  `iso_code_2` char(2) NOT NULL DEFAULT '',
  `iso_code_3` char(3) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `go_db_sequence`
--

DROP TABLE IF EXISTS `go_db_sequence`;
CREATE TABLE IF NOT EXISTS `go_db_sequence` (
  `seq_name` varchar(50) NOT NULL DEFAULT '',
  `nextid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`seq_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `go_groups`
--

DROP TABLE IF EXISTS `go_groups`;
CREATE TABLE IF NOT EXISTS `go_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `acl_id` int(11) NOT NULL,
  `admin_only` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `go_holidays`
--

DROP TABLE IF EXISTS `go_holidays`;
CREATE TABLE IF NOT EXISTS `go_holidays` (
  `id` int(11) NOT NULL DEFAULT '0',
  `date` int(10) NOT NULL DEFAULT '0',
  `name` varchar(100) NOT NULL DEFAULT '',
  `region` varchar(4) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `region` (`region`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `go_links_go_users`
--

DROP TABLE IF EXISTS `go_links_go_users`;
CREATE TABLE IF NOT EXISTS `go_links_go_users` (
  `id` int(11) NOT NULL,
  `folder_id` int(11) NOT NULL,
  `model_id` int(11) NOT NULL,
  `model_type_id` int(11) NOT NULL,
  `description` varchar(100) NOT NULL,
  `ctime` int(11) NOT NULL,
  KEY `link_id` (`model_id`,`model_type_id`),
  KEY `id` (`id`,`folder_id`),
  KEY `ctime` (`ctime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------


--
-- Tabelstructuur voor tabel `go_link_descriptions`
--

DROP TABLE IF EXISTS `go_link_descriptions`;
CREATE TABLE IF NOT EXISTS `go_link_descriptions` (
  `id` int(11) NOT NULL,
  `description` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `go_link_folders`
--

DROP TABLE IF EXISTS `go_link_folders`;
CREATE TABLE IF NOT EXISTS `go_link_folders` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `link_id` int(11) NOT NULL,
  `link_type` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `link_id` (`link_id`,`link_type`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `go_log`
--

DROP TABLE IF EXISTS `go_log`;
CREATE TABLE IF NOT EXISTS `go_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL DEFAULT '',
  `model` varchar(255) NOT NULL DEFAULT '',
  `model_id` varchar(255) NOT NULL DEFAULT '',
  `ctime` int(11) NOT NULL,
  `user_agent` varchar(255) NOT NULL DEFAULT '',
  `ip` varchar(15) NOT NULL DEFAULT '',
  `controller_route` varchar(255) NOT NULL DEFAULT '',
  `action` varchar(20) NOT NULL DEFAULT '',
  `message` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `go_mail_counter`
--

DROP TABLE IF EXISTS `go_mail_counter`;
CREATE TABLE IF NOT EXISTS `go_mail_counter` (
  `host` varchar(100) NOT NULL DEFAULT '',
  `date` date NOT NULL,
  `count` int(11) NOT NULL,
  PRIMARY KEY (`host`),
  KEY `date` (`date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `go_model_types`
--

DROP TABLE IF EXISTS `go_model_types`;
CREATE TABLE IF NOT EXISTS `go_model_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model_name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `go_modules`
--

DROP TABLE IF EXISTS `go_modules`;
CREATE TABLE IF NOT EXISTS `go_modules` (
  `id` varchar(20) NOT NULL DEFAULT '',
  `version` int(11) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT '0',
  `admin_menu` tinyint(1) NOT NULL DEFAULT '0',
  `acl_id` int(11) NOT NULL DEFAULT '0',
	`enabled` BOOLEAN NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `go_reminders`
--

DROP TABLE IF EXISTS `go_reminders`;
CREATE TABLE IF NOT EXISTS `go_reminders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model_id` int(11) NOT NULL,
  `model_type_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `time` int(11) NOT NULL,
  `vtime` int(11) NOT NULL DEFAULT '0',
  `snooze_time` int(11) NOT NULL,
  `manual` tinyint(1) NOT NULL DEFAULT '0',
  `text` text,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `go_reminders_users`
--

DROP TABLE IF EXISTS `go_reminders_users`;
CREATE TABLE IF NOT EXISTS `go_reminders_users` (
  `reminder_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `mail_sent` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`reminder_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `go_saved_search_queries`
--

DROP TABLE IF EXISTS `go_saved_search_queries`;
CREATE TABLE IF NOT EXISTS `go_saved_search_queries` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `sql` text NOT NULL,
  `type` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `go_search_cache`
--

DROP TABLE IF EXISTS `go_search_cache`;
CREATE TABLE IF NOT EXISTS `go_search_cache` (
  `user_id` int(11) NOT NULL DEFAULT '0',
  `model_id` int(11) NOT NULL DEFAULT '0',
  `module` varchar(50) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `description` varchar(255) NOT NULL DEFAULT '',
  `model_type_id` int(11) NOT NULL DEFAULT '0',
  `model_name` varchar(100) DEFAULT NULL,
  `keywords` varchar(255) NOT NULL DEFAULT '',
  `mtime` int(11) NOT NULL DEFAULT '0',
  `acl_id` int(11) NOT NULL,
  `type` varchar(20) NOT NULL,
  PRIMARY KEY (`model_id`,`model_type_id`),
  KEY `name` (`name`),
  KEY `keywords` (`keywords`),
  KEY `acl_id` (`acl_id`),
  KEY `mtime` (`mtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `go_search_sync`
--

DROP TABLE IF EXISTS `go_search_sync`;
CREATE TABLE IF NOT EXISTS `go_search_sync` (
  `user_id` int(11) NOT NULL DEFAULT '0',
  `module` varchar(50) NOT NULL DEFAULT '',
  `last_sync_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`,`module`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `go_settings`
--

DROP TABLE IF EXISTS `go_settings`;
CREATE TABLE IF NOT EXISTS `go_settings` (
  `user_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  `value` text,
  PRIMARY KEY (`user_id`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `go_state`
--

DROP TABLE IF EXISTS `go_state`;
CREATE TABLE IF NOT EXISTS `go_state` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL DEFAULT '',
  `value` text,
  PRIMARY KEY (`user_id`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `go_users`
--

DROP TABLE IF EXISTS `go_users`;
CREATE TABLE IF NOT EXISTS `go_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) NOT NULL DEFAULT '',
  `last_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `acl_id` int(11) NOT NULL DEFAULT '0',
  `date_format` varchar(20) NOT NULL DEFAULT 'dmY',
  `date_separator` char(1) NOT NULL DEFAULT '-',
  `time_format` varchar(10) NOT NULL DEFAULT 'G:i',
  `thousands_separator` char(1) NOT NULL DEFAULT '.',
  `decimal_separator` char(1) NOT NULL DEFAULT ',',
  `currency` char(3) NOT NULL DEFAULT '',
  `logins` int(11) NOT NULL DEFAULT '0',
  `lastlogin` int(11) NOT NULL DEFAULT '0',
  `ctime` int(11) NOT NULL DEFAULT '0',
  `max_rows_list` tinyint(4) NOT NULL DEFAULT '20',
  `timezone` varchar(50) NOT NULL DEFAULT 'Europe/Amsterdam',
  `start_module` varchar(50) NOT NULL DEFAULT 'summary',
  `language` varchar(20) NOT NULL DEFAULT 'en',
  `theme` varchar(20) NOT NULL DEFAULT 'Default',
  `first_weekday` tinyint(4) NOT NULL DEFAULT '0',
  `sort_name` varchar(20) NOT NULL DEFAULT 'first_name',
  `mtime` int(11) NOT NULL DEFAULT '0',
  `mute_sound` tinyint(1) NOT NULL DEFAULT '0',
  `mute_reminder_sound` tinyint(1) NOT NULL DEFAULT '0',
  `mute_new_mail_sound` tinyint(1) NOT NULL DEFAULT '0',
  `show_smilies` tinyint(1) NOT NULL DEFAULT '1',
  `list_separator` char(3) NOT NULL DEFAULT ';',
  `text_separator` char(3) NOT NULL DEFAULT '"',
  `files_folder_id` int(11) NOT NULL DEFAULT '0',
  `mail_reminders` tinyint(1) NOT NULL DEFAULT '0',
  `popup_reminders` tinyint(1) NOT NULL DEFAULT '0',
  `password_type` varchar(20) NOT NULL DEFAULT 'crypt',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `go_users_groups`
--

DROP TABLE IF EXISTS `go_users_groups`;
CREATE TABLE IF NOT EXISTS `go_users_groups` (
  `group_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`group_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `go_advanced_searches`;
CREATE TABLE IF NOT EXISTS `go_advanced_searches` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) NOT NULL DEFAULT '',
	`user_id` int(11) NOT NULL DEFAULT '0',
	`acl_id` int(11) NOT NULL DEFAULT '0',
	`data` TEXT NULL,
	`model_name` VARCHAR(100) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;