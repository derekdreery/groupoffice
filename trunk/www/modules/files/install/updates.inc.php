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

$updates[201108301656][]="INSERT INTO `go_model_types` (
`id` ,
`model_name`
)
VALUES (
'6', 'GO_Files_Model_File'
);";

$updates[201108301656][]="INSERT INTO `go_model_types` (
`id` ,
`model_name`
)
VALUES (
'17', 'GO_Files_Model_Folder'
);";


$updates[201109271656][]="ALTER TABLE `fs_folders` CHANGE `cm_state` `cm_state` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL;";
$updates[201109271656][]="ALTER TABLE `fs_templates` DROP `acl_write`";
$updates[201109271656][]="ALTER TABLE `fs_templates` CHANGE `content` `content` MEDIUMBLOB NOT NULL DEFAULT ''";
$updates[201109271656][]="ALTER TABLE `fs_templates` CHANGE `extension` `extension` CHAR( 4 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''";
$updates[201109271656][]="ALTER TABLE `fs_templates` CHANGE `id` `id` INT( 11 ) NOT NULL AUTO_INCREMENT";