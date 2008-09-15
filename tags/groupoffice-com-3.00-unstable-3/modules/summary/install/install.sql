DROP TABLE IF EXISTS `su_notes`;
CREATE TABLE IF NOT EXISTS `su_notes` (
  `user_id` int(11) NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `su_rss_feeds` (
`user_id` INT NOT NULL ,
`url` VARCHAR( 255 ) NOT NULL ,
PRIMARY KEY ( `user_id` )
) ENGINE = MYISAM DEFAULT CHARSET=utf8 ;