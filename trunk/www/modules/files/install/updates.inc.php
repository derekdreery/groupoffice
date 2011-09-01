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