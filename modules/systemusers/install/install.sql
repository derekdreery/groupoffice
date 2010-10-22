
DROP TABLE IF EXISTS `syu_vacation`;
CREATE TABLE IF NOT EXISTS `syu_vacation` (
  `account_id` int(11) NOT NULL,
  `vacation_active` enum('0','1') NOT NULL,
  `vacation_subject` varchar(255) DEFAULT NULL,
  `vacation_body` text,
  `forward_to` varchar(255) NOT NULL,
  PRIMARY KEY (`account_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
