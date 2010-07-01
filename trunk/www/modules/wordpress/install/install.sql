
CREATE TABLE IF NOT EXISTS `gw_contacts_wp_users` (
  `contact_id` int(11) NOT NULL,
  `wp_user_id` int(11) NOT NULL,
  PRIMARY KEY (`contact_id`,`wp_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;