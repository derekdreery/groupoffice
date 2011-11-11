
--
-- Tabelstructuur voor tabel `ab_addressbooks`
--

DROP TABLE IF EXISTS `ab_addressbooks`;
CREATE TABLE IF NOT EXISTS `ab_addressbooks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(50) DEFAULT NULL,
  `acl_id` int(11) NOT NULL DEFAULT '0',
  `shared_acl` tinyint(1) NOT NULL,
  `default_iso_address_format` varchar(2) NOT NULL,
  `default_salutation` varchar(255) NOT NULL,
  `files_folder_id` int(11) NOT NULL DEFAULT '0',
  `users` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `ab_companies`
--

DROP TABLE IF EXISTS `ab_companies`;
CREATE TABLE IF NOT EXISTS `ab_companies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `post_address` varchar(100) DEFAULT NULL,
  `post_address_no` varchar(100) DEFAULT NULL,
  `post_city` varchar(50) DEFAULT NULL,
  `post_state` varchar(50) DEFAULT NULL,
  `post_country` varchar(50) DEFAULT NULL,
  `post_zip` varchar(10) DEFAULT NULL,
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
  `files_folder_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `addressbook_id` (`addressbook_id`),
  KEY `link_id` (`link_id`),
  KEY `email` (`email`),
  KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `ab_contacts`
--

DROP TABLE IF EXISTS `ab_contacts`;
CREATE TABLE IF NOT EXISTS `ab_contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `addressbook_id` int(11) NOT NULL DEFAULT '0',
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) NOT NULL,
  `initials` varchar(10) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `sex` enum('M','F') NOT NULL DEFAULT 'M',
  `birthday` date DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `email2` varchar(100) NOT NULL DEFAULT '',
  `email3` varchar(100) NOT NULL DEFAULT '',
  `company_id` int(11) NOT NULL DEFAULT '0',
  `department` varchar(50) DEFAULT NULL,
  `function` varchar(50) DEFAULT NULL,
  `home_phone` varchar(30) DEFAULT NULL,
  `work_phone` varchar(30) DEFAULT NULL,
  `fax` varchar(30) DEFAULT NULL,
  `work_fax` varchar(30) DEFAULT NULL,
  `cellular` varchar(30) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `zip` varchar(10) DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL,
  `address_no` varchar(100) DEFAULT NULL,
  `comment` text,
  `ctime` int(11) NOT NULL DEFAULT '0',
  `mtime` int(11) NOT NULL DEFAULT '0',
  `salutation` varchar(50) DEFAULT NULL,
  `email_allowed` tinyint(1) NOT NULL DEFAULT '1',
  `files_folder_id` int(11) NOT NULL DEFAULT '0',
  `go_user_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `addressbook_id` (`addressbook_id`),
  KEY `email` (`email`),
  KEY `email2` (`email2`),
  KEY `email3` (`email3`),
  KEY `last_name` (`last_name`),
  KEY `go_user_id` (`go_user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `ab_sql`
--

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

--
-- Tabel structuur voor tabel `ab_sent_mailings`
--

DROP TABLE IF EXISTS `ab_sent_mailings`;
CREATE TABLE IF NOT EXISTS `ab_sent_mailings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `subject` varchar(100) default NULL,
  `message_path` varchar(255) default NULL,
  `ctime` int(11) NOT NULL,
  `mailing_group_id` int(11) NOT NULL,
  `alias_id` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `total` int(11) default NULL,
  `sent` int(11) default NULL,
  `errors` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------


-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `ab_addresslists`
--

DROP TABLE IF EXISTS `ab_addresslists`;
CREATE TABLE IF NOT EXISTS `ab_addresslists` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `acl_id` int(11) NOT NULL default '0',
  `name` varchar(255) default NULL,
  `default_salutation` varchar(50) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------



DROP TABLE IF EXISTS `ab_addresslist_contacts`;
CREATE TABLE IF NOT EXISTS `ab_addresslist_contacts` (
  `group_id` int(11) NOT NULL DEFAULT '0',
  `contact_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`group_id`,`contact_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ab_addresslist_companies`;
CREATE TABLE IF NOT EXISTS `ab_addresslist_companies` (
  `group_id` int(11) NOT NULL DEFAULT '0',
  `company_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`group_id`,`company_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `ab_email_templates`
--

DROP TABLE IF EXISTS `ab_email_templates`;
CREATE TABLE IF NOT EXISTS `ab_email_templates` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `type` tinyint(4) NOT NULL default '0',
  `name` varchar(100) default NULL,
  `acl_id` int(11) NOT NULL default '0',
  `content` longblob NOT NULL,
	`extension` VARCHAR( 4 ) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `ab_default_email_templates`
--

DROP TABLE IF EXISTS `ab_default_email_templates`;
CREATE TABLE IF NOT EXISTS `ab_default_email_templates` (
  `user_id` int(11) NOT NULL,
  `template_id` int(11) NOT NULL,
  PRIMARY KEY  (`user_id`),
  KEY `template_id` (`template_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;