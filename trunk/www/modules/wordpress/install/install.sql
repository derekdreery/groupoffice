
CREATE TABLE IF NOT EXISTS `gw_contacts_wp_users` (
  `contact_id` int(11) NOT NULL,
  `wp_user_id` int(11) NOT NULL,
  PRIMARY KEY (`contact_id`,`wp_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `wp_posts`;
CREATE TABLE IF NOT EXISTS `wp_posts` (
  `id` int(11) NOT NULL,
  `link_type` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `updated` tinyint(1) NOT NULL,
  `publish` tinyint(1) NOT NULL,
  `title` varchar(100) NOT NULL,
  `content` text NOT NULL,
  KEY `id` (`id`,`link_type`),
  KEY `updated` (`updated`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;