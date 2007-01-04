<?php
$updates[21] = array();

$updates[20][] = "ALTER TABLE `em_settings` DROP `sort_field` , DROP `sort_order` ;";
$updates[20][] = "ALTER TABLE `em_settings` ADD `show_preview` ENUM( '0', '1' ) DEFAULT '1' NOT NULL ;";

$updates[19][] = "ALTER TABLE `emAccounts` ADD `spam` VARCHAR( 100 ) NOT NULL AFTER `trash`";
$updates[19][] = "ALTER TABLE `emAccounts` ADD `spamtag` VARCHAR( 20 ) DEFAULT '***SPAM***' NOT NULL AFTER `spam` ;";

$updates[18][] = "ALTER TABLE `emFolders` DROP INDEX `id`";
$updates[18][] = "ALTER TABLE `emAccounts` DROP INDEX `id`";
$updates[18][] = "ALTER TABLE `emFolders` ADD `msgcount` INT NOT NULL ,
ADD `unseen` INT NOT NULL ;";

$updates[17][] = "ALTER TABLE `emFilters` DROP INDEX `id`";
$updates[17][] = "ALTER TABLE `emFilters` ADD `mark_as_read` ENUM( '0', '1' ) NOT NULL ;";

$updates[16][] = "UPDATE em_settings SET add_recievers='0' WHERE add_recievers='1'";

$updates[15][] = "ALTER TABLE `em_settings` ADD `enable_vacation` ENUM( '0', '1' ) NOT NULL ,
ADD `vacation_subject` VARCHAR( 100 ) NOT NULL ,
ADD `vacation_text` TEXT NOT NULL ;";

$updates[15][] = "ALTER TABLE `em_settings` CHANGE `add_recievers` `add_recievers` INT NOT NULL DEFAULT '0'";
$updates[15][] = "UPDATE `em_settings` SET add_recievers=0";
$updates[15][] = "ALTER TABLE `em_settings` ADD `charset` VARCHAR( 20 ) DEFAULT 'UTF-8' NOT NULL";

$updates[14][] = "ALTER TABLE `emFolders` ADD `sort_order` TINYINT NOT NULL";
$updates[14][] = "ALTER TABLE `emAccounts` DROP `spam`";

$updates[13][] = "ALTER TABLE `emAccounts` ADD `drafts` VARCHAR( 100 ) NOT NULL AFTER `sent` ";

$updates[12][] = "ALTER TABLE `em_settings` ADD `add_senders` ENUM( '0', '1' ) NOT NULL AFTER `add_recievers`";

$updates[11][] = "ALTER TABLE `em_settings` ADD `add_recievers` ENUM( '0', '1' ) NOT NULL;";
$updates[11][] = "ALTER TABLE `em_settings` ADD `request_notification` ENUM( '0', '1' ) NOT NULL;";

$updates[10][] = "ALTER TABLE `emAccounts` ADD `use_ssl` ENUM( '0', '1' ) NOT NULL AFTER `port`;";
$updates[10][] = "ALTER TABLE `emAccounts` ADD `novalidate_cert` ENUM( '0', '1' ) NOT NULL AFTER `use_ssl`;";
