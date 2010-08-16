DROP TABLE IF EXISTS `dav_events`;
CREATE TABLE IF NOT EXISTS `dav_events` (
  `uuid` varchar(100) NOT NULL,
  `data` text NOT NULL,
  `mtime` int(11) NOT NULL,
  PRIMARY KEY (`uuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;