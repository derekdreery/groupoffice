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