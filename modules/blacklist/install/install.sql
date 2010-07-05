DROP TABLE IF EXISTS `bl_ips`;
CREATE TABLE IF NOT EXISTS `bl_ips` (
  `ip` varchar(15) NOT NULL,
  `mtime` int(11) NOT NULL,
  `count` int(11) NOT NULL,
  PRIMARY KEY (`ip`),
  KEY `count` (`count`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;