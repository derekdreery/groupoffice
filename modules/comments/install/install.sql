DROP TABLE IF EXISTS `co_comments`;
CREATE TABLE IF NOT EXISTS `co_comments` (
  `id` int(11) NOT NULL,
  `link_id` int(11) NOT NULL,
  `link_type` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ctime` int(11) NOT NULL,
  `mtime` int(11) NOT NULL,
  `comments` text,
  PRIMARY KEY  (`id`),
  KEY `link_id` (`link_id`,`link_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;