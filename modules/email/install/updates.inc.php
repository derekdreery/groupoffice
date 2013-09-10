<?php
$updates["201201031630"][]="ALTER TABLE `em_accounts` CHANGE `use_ssl` `use_ssl` BOOLEAN NOT NULL DEFAULT '0'";
$updates["201201031630"][]="UPDATE em_accounts SET use_ssl=0 where use_ssl=1";
$updates["201201031630"][]="UPDATE em_accounts SET use_ssl=1 where use_ssl=2";

$updates["201201031630"][]="ALTER TABLE `em_accounts` CHANGE `novalidate_cert` `novalidate_cert` BOOLEAN NOT NULL DEFAULT '0'";
$updates["201201031630"][]="UPDATE em_accounts SET novalidate_cert=0 where novalidate_cert=1";
$updates["201201031630"][]="UPDATE em_accounts SET novalidate_cert=1 where novalidate_cert=2";

$updates["201201031630"][]="ALTER TABLE `em_accounts` CHANGE `id` `id` INT( 11 ) NOT NULL AUTO_INCREMENT ";
$updates["201201031630"][]="ALTER TABLE `em_accounts` CHANGE `password_encrypted` `password_encrypted` TINYINT( 4 ) NOT NULL DEFAULT '0'";

$updates["201201031630"][]="ALTER TABLE `em_accounts` DROP `spamtag`;";
$updates["201201031630"][]="ALTER TABLE `em_accounts` DROP `examine_headers`;";
$updates["201201031630"][]="ALTER TABLE `em_accounts` DROP `auto_check`;";
$updates["201201031630"][]="ALTER TABLE `em_accounts` DROP `forward_enabled`;";
$updates["201201031630"][]="ALTER TABLE `em_accounts` DROP `forward_to`;";
$updates["201201031630"][]="ALTER TABLE `em_accounts` DROP `forward_local_copy`;";

$updates["201201031630"][]="ALTER TABLE `em_accounts` DROP `signature`;";


$updates["201201031630"][]="ALTER TABLE `em_aliases` CHANGE `default` `default` BOOLEAN NOT NULL DEFAULT '0'";
$updates["201201031630"][]="UPDATE em_aliases SET `default`=0 where `default`=1";
$updates["201201031630"][]="UPDATE em_aliases SET `default`=1 where `default`=2";

$updates["201201031630"][]="ALTER TABLE `em_aliases` CHANGE `id` `id` INT( 11 ) NOT NULL AUTO_INCREMENT ";

$updates["201201031630"][]="ALTER TABLE `em_aliases` CHANGE `signature` `signature` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL ";

$updates["201201031630"][]="ALTER TABLE `em_accounts` CHANGE `mbroot` `mbroot` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''";
$updates["201201031630"][]="ALTER TABLE `em_accounts` CHANGE `sent` `sent` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'Sent'";
$updates["201201031630"][]="ALTER TABLE `em_accounts` CHANGE `drafts` `drafts` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'Drafts'";
$updates["201201031630"][]="ALTER TABLE `em_accounts` CHANGE `trash` `trash` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'Trash'";
$updates["201201031630"][]="ALTER TABLE `em_accounts` CHANGE `spam` `spam` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'Spam'";
$updates["201205011100"][]="UPDATE `em_accounts` SET password=CONCAT('{GOCRYPT}',`password`);";
$updates["201205011230"][]="ALTER TABLE `em_accounts` CHANGE `smtp_password` `smtp_password` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';";
$updates["201205011400"][]="script:encrypt.inc.php";
$updates["201206051342"][]="ALTER TABLE `em_links` ADD `mtime` INT NOT NULL DEFAULT '0' AFTER `ctime` ";

$updates["201206121446"][]="ALTER TABLE `em_accounts` ADD `ignore_sent_folder` TINYINT( 1 ) NOT NULL DEFAULT '0'";


$updates["201206141446"][]="";
$updates["201206141446"][]="";
$updates["201206141446"][]="";

$updates["201207040933"][]="ALTER TABLE `em_links` ADD `uid` VARCHAR( 100 ) NOT NULL DEFAULT '', ADD INDEX ( `uid` ) ";

$updates["201207191730"][]="ALTER TABLE `em_accounts` ADD `sieve_port` int(11) NOT NULL;";
$updates["201207191730"][]="ALTER TABLE `em_accounts` ADD `sieve_usetls` tinyint(1) NOT NULL DEFAULT '1';";

$updates["201207191730"][]="UPDATE `em_accounts` SET `sieve_port`='2000'";

$updates["201209060935"][]="ALTER TABLE `em_filters` CHANGE `id` `id` INT( 11 ) NOT NULL AUTO_INCREMENT ";

$updates["201209061100"][]="CREATE TABLE IF NOT EXISTS `em_portlet_folders` (
  `account_id` int(11) NOT NULL,
	`folder_name` varchar(100) NOT NULL,
  `user_id` int(11) NOT NULL,
  `mtime` int(11) NOT NULL,
  PRIMARY KEY (`account_id`,`folder_name`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$updates["201209061400"][]="ALTER TABLE `em_accounts` ADD `check_mailboxes` TEXT;";

$updates["201209111400"][]="update `em_accounts` set check_mailboxes='INBOX';";

$updates["201209211112"][]="ALTER TABLE `em_links` CHANGE `link_id` `id` INT( 11 ) NOT NULL AUTO_INCREMENT";

$updates["201212171547"][]="ALTER TABLE  `em_links` CHANGE  `uid`  `uid` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  ''";

$updates["201303011412"][]="delete from go_state where name='em-pnl-west' or name='eml-pnl-north';";

$updates["201303011412"][]="delete from go_state where name='em-pnl-west' or name='eml-pnl-north';";

// All acls for email accounts with read permission will be updated to create permissions
$updates["201304081400"][]="UPDATE go_acl SET level=20 WHERE level = 10 AND acl_id IN (select acl_id from em_accounts) AND user_id>0;";

$updates['201304231330'][]="ALTER TABLE `em_links` ADD `muser_id` int(11) NOT NULL DEFAULT '0';";

$updates['201306251122'][]="ALTER TABLE  `em_accounts` ADD  `do_not_mark_as_read` BOOLEAN NOT NULL DEFAULT FALSE";

$updates['201306251600'][]="ALTER TABLE `em_links` CHANGE `time` `time` INT( 11 ) NOT NULL DEFAULT '0';";
