<?php
$updates["201112081020"][]="ALTER TABLE `si_sites` ADD `login_path` VARCHAR( 255 ) NOT NULL DEFAULT 'login'";
$updates["201112081020"][]="ALTER TABLE `si_sites` ADD `logout_path` VARCHAR( 255 ) NOT NULL DEFAULT 'logout'";
$updates["201112081020"][]="ALTER TABLE `si_sites` ADD `register_path` VARCHAR( 255 ) NOT NULL DEFAULT 'register'";
$updates["201112081020"][]="ALTER TABLE `si_sites` ADD `reset_password_path` VARCHAR( 255 ) NOT NULL DEFAULT 'resetpassword'";
$updates["201112081020"][]="ALTER TABLE `si_sites` ADD `lost_password_path` VARCHAR( 255 ) NOT NULL DEFAULT 'lostpassword'";
$updates["201112081020"][]="ALTER TABLE `si_pages` ADD `login_required` BOOLEAN NOT NULL DEFAULT '0'";


$updates["201112081020"][]="ALTER TABLE `si_sites` ADD `ssl` BOOLEAN NOT NULL DEFAULT '0',
ADD `mod_rewrite` BOOLEAN NOT NULL DEFAULT '0',
ADD `mod_rewrite_base_path` VARCHAR( 50 ) NOT NULL DEFAULT '/'";

$updates["201201111000"][]="ALTER TABLE `si_sites` ADD `register_user_groups` VARCHAR( 50 ) NULL DEFAULT ''";

$updates["201202061200"][]="ALTER TABLE `si_pages` CHANGE `site_id` `site_id` INT( 11 ) NOT NULL DEFAULT '0',
CHANGE `user_id` `user_id` INT( 11 ) NOT NULL DEFAULT '0',
CHANGE `ctime` `ctime` INT( 11 ) NOT NULL ,
CHANGE `mtime` `mtime` INT( 11 ) NOT NULL ,
CHANGE `name` `name` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'New Page',
CHANGE `title` `title` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'New Page',
CHANGE `description` `description` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
CHANGE `keywords` `keywords` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
CHANGE `path` `path` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
CHANGE `template` `template` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
CHANGE `content` `content` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''";

$updates["201202141200"][]="ALTER TABLE `si_sites` ADD `language` VARCHAR( 10 ) NOT NULL DEFAULT 'en'";

$updates["201204180918"][]="update `si_sites` set template='Plain' where template ='thehousecrowd';";

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

$updates["201211271539"][]="ALTER TABLE `si_sites` ADD `base_path` VARCHAR( 100 ) NOT NULL DEFAULT ''";

$updates["201301291408"][]="ALTER TABLE  `si_content` ADD  `sort_order` INT NOT NULL DEFAULT  '0'";

// Add Customfields to the site and content model
$updates["201303251100"][]="CREATE TABLE IF NOT EXISTS `cf_si_sites` (
  `model_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`model_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$updates["201303251100"][]="CREATE TABLE IF NOT EXISTS `cf_si_content` (
  `model_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`model_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
