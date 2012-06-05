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