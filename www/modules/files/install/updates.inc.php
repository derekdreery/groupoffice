<?php

$updates[201108190000][]="RENAME TABLE `go_links_6` TO `go_links_fs_files`;";
$updates[201108190000][]="ALTER TABLE `go_links_fs_files` CHANGE `link_id` `model_id` INT( 11 ) NOT NULL";
$updates[201108190000][]="ALTER TABLE `go_links_fs_files` CHANGE `link_type` `model_type_id` INT( 11 ) NOT NULL";


$updates[201108190000][]="RENAME TABLE `cf_6` TO `cf_fs_files` ";
$updates[201108190000][]="ALTER TABLE `cf_fs_files` CHANGE `link_id` `model_id` INT( 11 ) NOT NULL DEFAULT '0'";



$updates[201108190000][]="RENAME TABLE `go_links_17` TO `go_links_fs_folders`;";
$updates[201108190000][]="ALTER TABLE `go_links_fs_folders` CHANGE `link_id` `model_id` INT( 11 ) NOT NULL";
$updates[201108190000][]="ALTER TABLE `go_links_fs_folders` CHANGE `link_type` `model_type_id` INT( 11 ) NOT NULL";


$updates[201108190000][]="RENAME TABLE `cf_17` TO `cf_fs_folders` ";
$updates[201108190000][]="ALTER TABLE `cf_fs_folders` CHANGE `link_id` `model_id` INT( 11 ) NOT NULL DEFAULT '0'";

$updates[201109020000][]="ALTER TABLE `fs_files` DROP `path`";
$updates[201109020000][]="ALTER TABLE `fs_folders` DROP `path`";

$updates[201109020000][]="ALTER TABLE `fs_folders` CHANGE `id` `id` INT( 11 ) NOT NULL AUTO_INCREMENT ";
$updates[201109020000][]="ALTER TABLE `fs_files` CHANGE `id` `id` INT( 11 ) NOT NULL AUTO_INCREMENT ";


$updates[201109050000][]="ALTER TABLE `fs_folders` CHANGE `comments` `comment` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL";
$updates[201109050000][]="ALTER TABLE `fs_files` CHANGE `comments` `comment` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL";

$updates[201109060000][]="ALTER TABLE `fs_notifications` DROP `path`";