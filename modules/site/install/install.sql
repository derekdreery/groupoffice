-- --------------------------------------------------------

--
-- Table structure for table `site_sites`
--

CREATE TABLE IF NOT EXISTS `site_sites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `user_id` int(11) NOT NULL,
  `mtime` int(11) NOT NULL,
  `ctime` int(11) NOT NULL,
  `domain` varchar(100) NOT NULL,
  `module` varchar(100) NOT NULL,
  `ssl` tinyint(1) NOT NULL DEFAULT '0',
  `mod_rewrite` tinyint(1) NOT NULL DEFAULT '0',
  `mod_rewrite_base_path` varchar(50) NOT NULL DEFAULT '/',
  `base_path` varchar(100) NOT NULL DEFAULT '',
  `acl_id` int(11) NOT NULL DEFAULT '0',
  `language` varchar(10) NOT NULL,
  `files_folder_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `domain` (`domain`)
) ENGINE=InnoDB;


-- --------------------------------------------------------

--
-- Table structure for table `site_content`
--

CREATE TABLE IF NOT EXISTS `site_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `ctime` int(11) NOT NULL DEFAULT '0',
  `mtime` int(11) NOT NULL DEFAULT '0',
  `ptime` int(11) NOT NULL DEFAULT '0',
  `title` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `meta_title` varchar(100) DEFAULT NULL,
  `meta_description` varchar(255) DEFAULT NULL,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `content` text,
  `status` int(11) NOT NULL DEFAULT '1',
  `parent_id` int(11) DEFAULT NULL,
  `site_id` int(11) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT '0',
  `template` varchar(255) DEFAULT NULL,
  `default_child_template` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`,`site_id`)
) ENGINE=InnoDB;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `cf_site_sites`
--

DROP TABLE IF EXISTS `cf_site_sites`;
CREATE TABLE IF NOT EXISTS `cf_site_sites` (
  `model_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`model_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `cf_site_content`
--

DROP TABLE IF EXISTS `cf_site_content`;
CREATE TABLE IF NOT EXISTS `cf_site_content` (
  `model_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`model_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `site_multifile_files`
--

CREATE TABLE IF NOT EXISTS `site_multifile_files` (
  `model_id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL,
  `order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`model_id`,`field_id`,`file_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

