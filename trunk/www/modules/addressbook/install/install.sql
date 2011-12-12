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
  `default_salutation` varchar(255) NOT NULL,
  `files_folder_id` int(11) NOT NULL DEFAULT '0',
  `users` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `ab_addresslists`
--

DROP TABLE IF EXISTS `ab_addresslists`;
CREATE TABLE IF NOT EXISTS `ab_addresslists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `acl_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) DEFAULT NULL,
  `default_salutation` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `ab_addresslist_companies`
--

DROP TABLE IF EXISTS `ab_addresslist_companies`;
CREATE TABLE IF NOT EXISTS `ab_addresslist_companies` (
  `addresslist_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`addresslist_id`,`company_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `ab_addresslist_contacts`
--

DROP TABLE IF EXISTS `ab_addresslist_contacts`;
CREATE TABLE IF NOT EXISTS `ab_addresslist_contacts` (
  `addresslist_id` int(11) NOT NULL,
  `contact_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`addresslist_id`,`contact_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
  `comment` text DEFAULT NULL,
  `bank_no` varchar(50) DEFAULT NULL,
  `vat_no` varchar(30) DEFAULT NULL,
  `iban` varchar(100) DEFAULT NULL,
  `crn` varchar(50) DEFAULT NULL,
  `ctime` int(11) NOT NULL DEFAULT '0',
  `mtime` int(11) NOT NULL DEFAULT '0',
  `email_allowed` tinyint(1) NOT NULL DEFAULT '1',
  `files_folder_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `addressbook_id` (`addressbook_id`),
  KEY `link_id` (`link_id`),
  KEY `email` (`email`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
	`suffix` varchar(50) NOT NULL DEFAULT '',
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
  `comment` text DEFAULT NULL,
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `ab_default_email_templates`
--

DROP TABLE IF EXISTS `ab_default_email_templates`;
CREATE TABLE IF NOT EXISTS `ab_default_email_templates` (
  `user_id` int(11) NOT NULL,
  `template_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`),
  KEY `template_id` (`template_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `ab_email_templates`
--

DROP TABLE IF EXISTS `ab_email_templates`;
CREATE TABLE IF NOT EXISTS `ab_email_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `type` tinyint(4) NOT NULL DEFAULT '0',
  `name` varchar(100) DEFAULT NULL,
  `acl_id` int(11) NOT NULL DEFAULT '0',
  `content` longblob NOT NULL,
  `extension` varchar(4) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `ab_sent_mailings`
--

DROP TABLE IF EXISTS `ab_sent_mailings`;
CREATE TABLE IF NOT EXISTS `ab_sent_mailings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `subject` varchar(100) DEFAULT NULL,
  `message_path` varchar(255) DEFAULT NULL,
  `ctime` int(11) NOT NULL,
  `addresslist_id` int(11) NOT NULL,
  `alias_id` int(11) NOT NULL,
  `status` tinyint(4) DEFAULT '0',
  `total` int(11) DEFAULT '0',
  `sent` int(11) DEFAULT '0',
  `errors` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `ab_search_queries`
--

DROP TABLE IF EXISTS `ab_search_queries`;
CREATE TABLE IF NOT EXISTS `ab_sql` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
-- Tabelstructuur voor tabel `cf_ab_companies`
--

DROP TABLE IF EXISTS `cf_ab_companies`;
CREATE TABLE IF NOT EXISTS `cf_ab_companies` (
  `model_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`model_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `cf_ab_contacts`
--

DROP TABLE IF EXISTS `cf_ab_contacts`;
CREATE TABLE IF NOT EXISTS `cf_ab_contacts` (
  `model_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`model_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------



--
-- Tabelstructuur voor tabel `go_links_ab_companies`
--

DROP TABLE IF EXISTS `go_links_ab_companies`;
CREATE TABLE IF NOT EXISTS `go_links_ab_companies` (
  `id` int(11) NOT NULL,
  `folder_id` int(11) NOT NULL,
  `model_id` int(11) NOT NULL,
  `model_type_id` int(11) NOT NULL,
  `description` varchar(100) DEFAULT NULL,
  `ctime` int(11) NOT NULL,
  KEY `link_id` (`model_id`,`model_type_id`),
  KEY `id` (`id`,`folder_id`),
  KEY `ctime` (`ctime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `go_links_ab_contacts`
--

DROP TABLE IF EXISTS `go_links_ab_contacts`;
CREATE TABLE IF NOT EXISTS `go_links_ab_contacts` (
  `id` int(11) NOT NULL,
  `folder_id` int(11) NOT NULL,
  `model_id` int(11) NOT NULL,
  `model_type_id` int(11) NOT NULL,
  `description` varchar(100) DEFAULT NULL,
  `ctime` int(11) NOT NULL,
  KEY `link_id` (`model_id`,`model_type_id`),
  KEY `id` (`id`,`folder_id`),
  KEY `ctime` (`ctime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------




--
-- Tabelstructuur voor tabel `ml_default_templates`
--

DROP TABLE IF EXISTS `ab_default_email_templates`;
CREATE TABLE IF NOT EXISTS `ab_default_email_templates` (
  `user_id` int(11) NOT NULL,
  `template_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`),
  KEY `template_id` (`template_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `ab_sendmailing_companies`
--

DROP TABLE IF EXISTS `ab_sendmailing_companies`;
CREATE TABLE IF NOT EXISTS `ml_sendmailing_companies` (
  `addresslist_id` int(11) NOT NULL DEFAULT '0',
  `company_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`addresslist_id`,`company_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `ab_sendmailing_contacts`
--

DROP TABLE IF EXISTS `ab_sendmailing_contacts`;
CREATE TABLE IF NOT EXISTS `ab_sendmailing_contacts` (
  `addresslist_id` int(11) NOT NULL DEFAULT '0',
  `contact_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`addresslist_id`,`contact_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
