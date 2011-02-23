
-- --------------------------------------------------------




----
-- Tabel structuur voor tabel `ab_addressbooks`
--

DROP TABLE IF EXISTS `ab_addressbooks`;
CREATE TABLE IF NOT EXISTS `ab_addressbooks` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `name` varchar(50) default NULL,
  `acl_id` int(11) NOT NULL default '0',
  `shared_acl` tinyint(1) NOT NULL,
  `default_iso_address_format` varchar(2) NOT NULL,
  `default_salutation` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `ab_companies`
--

DROP TABLE IF EXISTS `ab_companies`;
CREATE TABLE IF NOT EXISTS `ab_companies` (
  `id` int(11) NOT NULL DEFAULT '0',
  `link_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `addressbook_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(100) DEFAULT NULL,
	`name2` varchar(100) DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL,
  `address_no` varchar(100) DEFAULT NULL,
  `zip` varchar(10) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `iso_address_format` varchar(2) NOT NULL,
  `post_address` varchar(100) DEFAULT NULL,
  `post_address_no` varchar(100) DEFAULT NULL,
  `post_city` varchar(50) DEFAULT NULL,
  `post_state` varchar(50) DEFAULT NULL,
  `post_country` varchar(50) DEFAULT NULL,
  `post_zip` varchar(10) DEFAULT NULL,
  `post_iso_address_format` varchar(2) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `fax` varchar(30) DEFAULT NULL,
  `email` varchar(75) DEFAULT NULL,
  `homepage` varchar(100) DEFAULT NULL,
  `comment` text,
  `bank_no` varchar(50) DEFAULT NULL,
  `vat_no` varchar(30) DEFAULT NULL,
  `iban` varchar(100) DEFAULT NULL,
  `crn` varchar(50) DEFAULT NULL,
  `ctime` int(11) NOT NULL DEFAULT '0',
  `mtime` int(11) NOT NULL DEFAULT '0',
  `email_allowed` enum('0','1') NOT NULL,
  `files_folder_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `addressbook_id` (`addressbook_id`),
  KEY `link_id` (`link_id`),
  KEY `email` (`email`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `ab_contacts`
--

DROP TABLE IF EXISTS `ab_contacts`;
CREATE TABLE IF NOT EXISTS `ab_contacts` (
  `id` int(11) NOT NULL default '0',
  `link_id` int(11) default NULL,
  `user_id` int(11) NOT NULL default '0',
  `addressbook_id` int(11) NOT NULL default '0',
  `source_id` int(11) NOT NULL default '0',
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) NULL,
  `last_name` varchar(50) NOT NULL,
  `initials` varchar(10) default NULL,
  `title` varchar(50) default NULL,
  `sex` enum('M','F') NOT NULL default 'M',
  `birthday` date default NULL,
  `email` varchar(100) NOT NULL,
  `email2` varchar(100) NOT NULL,
  `email3` varchar(100) NOT NULL,
  `company_id` int(11) NOT NULL default '0',
  `department` varchar(50) default NULL,
  `function` varchar(50) default NULL,
  `home_phone` varchar(30) default NULL,
  `work_phone` varchar(30) default NULL,
  `fax` varchar(30) default NULL,
  `work_fax` varchar(30) default NULL,
  `cellular` varchar(30) default NULL,
  `country` varchar(50) default NULL,
  `state` varchar(50) default NULL,
  `city` varchar(50) default NULL,
  `zip` varchar(10) default NULL,
  `address` varchar(100) default NULL,
  `address_no` varchar(100) default NULL,
  `comment` text,
  `iso_address_format` varchar(2) NOT NULL,
  `color` varchar(6) default NULL,
  `sid` varchar(32) default NULL,
  `ctime` int(11) NOT NULL default '0',
  `mtime` int(11) NOT NULL default '0',
  `salutation` varchar(50) default NULL,
  `email_allowed` enum('0','1') NOT NULL,
  `files_folder_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `link_id` (`link_id`),
  KEY `addressbook_id` (`addressbook_id`),
  KEY `email` (`email`),
  KEY `email2` (`email2`),
  KEY `email3` (`email3`),
  KEY `last_name` (`last_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ab_sql`;
CREATE TABLE IF NOT EXISTS `ab_sql` (
  `id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL,
  `companies` tinyint(1) NOT NULL,
  `name` varchar(32) DEFAULT NULL,
  `sql` text,
  PRIMARY KEY (`id`),
  KEY `companies` (`companies`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------




-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `cf_2`
-- 

DROP TABLE IF EXISTS `cf_2`;
CREATE TABLE IF NOT EXISTS `cf_2` (
  `link_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`link_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `cf_3`
-- 

DROP TABLE IF EXISTS `cf_3`;
CREATE TABLE IF NOT EXISTS `cf_3` (
  `link_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`link_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `go_links_2`
-- 

DROP TABLE IF EXISTS `go_links_2`;
CREATE TABLE IF NOT EXISTS `go_links_2` (
  `id` int(11) NOT NULL,
  `folder_id` int(11) NOT NULL,
  `link_id` int(11) NOT NULL,
  `link_type` int(11) NOT NULL,
  `description` varchar(100) NULL,
  `ctime` int(11) NOT NULL,
  KEY `link_id` (`link_id`,`link_type`),
  KEY `id` (`id`,`folder_id`),
  KEY `ctime` (`ctime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `go_links_3`
-- 

DROP TABLE IF EXISTS `go_links_3`;
CREATE TABLE IF NOT EXISTS `go_links_3` (
  `id` int(11) NOT NULL,
  `folder_id` int(11) NOT NULL,
  `link_id` int(11) NOT NULL,
  `link_type` int(11) NOT NULL,
  `description` varchar(100) NULL,
  `ctime` int(11) NOT NULL,
  KEY `link_id` (`link_id`,`link_type`),
  KEY `id` (`id`,`folder_id`),
  KEY `ctime` (`ctime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

