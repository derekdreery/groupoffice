<?php
$updates[201108120000][]="ALTER TABLE `go_users` CHANGE `id` `id` INT( 11 ) NOT NULL AUTO_INCREMENT";

$updates[201108120000][]="ALTER TABLE `go_acl_items` CHANGE `id` `id` INT( 11 ) NOT NULL AUTO_INCREMENT";

$updates[201108120000][]="ALTER TABLE `go_search_cache` DROP `table` ,
DROP `url` ,
DROP `link_count` ,
DROP `acl_read` ;";

$updates[201108120000][]="ALTER TABLE `go_users` CHANGE `max_rows_list` `max_rows_list` TINYINT( 4 ) NOT NULL DEFAULT '20'";
$updates[201108120000][]="ALTER TABLE `go_users` CHANGE `registration_time` `ctime` INT( 11 ) NOT NULL DEFAULT '0'";
$updates[201108120000][]="ALTER TABLE `go_groups` CHANGE `id` `id` INT( 11 ) NOT NULL AUTO_INCREMENT";
$updates[201108131012][]="script:11_users_to_addressbook.inc.php";
$updates[201108181012][]="ALTER TABLE `go_search_cache` CHANGE `description` `description` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''";