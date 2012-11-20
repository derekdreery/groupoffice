<?php
$updates["201211201622"][]="ALTER TABLE `si_sites` ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci";
$updates["201211201622"][]="CREATE TABLE IF NOT EXISTS `si_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `ctime` int(11) NOT NULL DEFAULT '0',
  `mtime` int(11) NOT NULL DEFAULT '0',
  `title` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `meta_title` varchar(100) DEFAULT NULL,
  `meta_description` varchar(255) DEFAULT NULL,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `content` text,
  `status` int(11) NOT NULL DEFAULT '1',
  `parent_id` int(11) DEFAULT NULL,
  `site_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug_UNIQUE` (`slug`,`site_id`),
  KEY `fk_si_content_si_content1` (`parent_id`),
  KEY `fk_si_content_si_sites1` (`site_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";