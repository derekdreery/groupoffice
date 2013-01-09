DROP TABLE IF EXISTS `li_auto_imports`;
CREATE TABLE IF NOT EXISTS `li_auto_imports` (
	`addressbook_id` int(11) NOT NULL,
  `access` varchar(1024) DEFAULT NULL,
	`auto_import_enabled` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`addressbook_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `li_profiles`;
CREATE TABLE IF NOT EXISTS `li_profiles` (
  `contact_id` int(11) NOT NULL,
  `first_name` varchar(32) DEFAULT NULL,
	`last_name` varchar(64) DEFAULT NULL,
	`headline` varchar(128) DEFAULT NULL,
	`area` varchar(128) DEFAULT NULL,
	`country` varchar(8) DEFAULT NULL,
	`industry` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`contact_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;