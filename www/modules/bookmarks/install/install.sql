DROP TABLE IF EXISTS `bm_bookmarks`;
CREATE TABLE IF NOT EXISTS `bm_bookmarks` (
  `id` int(11) NOT NULL DEFAULT '0',
  `category_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(64) NOT NULL,
  `content` text NOT NULL,
  `description` varchar(64) DEFAULT NULL,
  `logo` varchar(64) DEFAULT NULL,
  `public_icon` tinyint(1) NOT NULL,
  `open_extern` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `category_id` (`category_id`),
  FULLTEXT KEY `content` (`content`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `bm_categories`;
CREATE TABLE IF NOT EXISTS `bm_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `acl_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
