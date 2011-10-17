<?php
$updates[201108131011][]="ALTER TABLE `ab_companies` CHANGE `id` `id` INT( 11 ) NOT NULL AUTO_INCREMENT";
$updates[201108131011][]="ALTER TABLE `ab_contacts` CHANGE `id` `id` INT( 11 ) NOT NULL AUTO_INCREMENT";
$updates[201108131011][]="ALTER TABLE `ab_contacts` ADD `go_user_id` INT NOT NULL , ADD INDEX ( `go_user_id` )";
$updates[201108131011][]="ALTER TABLE `ab_addressbooks` DROP `acl_write`";
$updates[201108131011][]="ALTER TABLE `ab_addressbooks` CHANGE `id` `id` INT( 11 ) NOT NULL AUTO_INCREMENT";
$updates[201108131011][]="ALTER TABLE `ab_addressbooks` ADD `files_folder_id` INT NOT NULL";
$updates[201108131011][]="ALTER TABLE `ab_addressbooks` ADD `users` BOOLEAN NOT NULL ";
$updates[201108131011][]="ALTER TABLE `ab_contacts` DROP `source_id`"; 
$updates[201108131011][]="ALTER TABLE `ab_contacts` DROP `link_id` ";
$updates[201108131011][]="ALTER TABLE `ab_contacts` CHANGE `email2` `email2` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''";
$updates[201108131011][]="ALTER TABLE `ab_contacts` CHANGE `email3` `email3` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''";
$updates[201108131011][]="ALTER TALE `ab_contacts` CHANGE `email` `email` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''";
$updates[201108131011][]="ALTER TABLE `ab_contacts` CHANGE `email_allowed` `email_allowed` ENUM( '0', '1' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '1'";
$updates[201108131011][]="ALTER TABLE `ab_contacts` DROP `color`"; 
$updates[201108131011][]="ALTER TABLE `ab_contacts` DROP `sid`"; 


$updates[201109011450][]="RENAME TABLE `go_links_2` TO `go_links_ab_contacts`;";
$updates[201109011450][]="ALTER TABLE `go_links_ab_contacts` CHANGE `link_id` `model_id` INT( 11 ) NOT NULL";
$updates[201109011450][]="ALTER TABLE `go_links_ab_contacts` CHANGE `link_type` `model_type_id` INT( 11 ) NOT NULL";


$updates[201109011450][]="RENAME TABLE `go_links_3` TO `go_links_ab_companies`;";
$updates[201109011450][]="ALTER TABLE `go_links_ab_companies` CHANGE `link_id` `model_id` INT( 11 ) NOT NULL";
$updates[201109011450][]="ALTER TABLE `go_links_ab_companies` CHANGE `link_type` `model_type_id` INT( 11 ) NOT NULL";


$updates[201109011450][]="ALTER TABLE `cf_2` CHANGE `link_id` `model_id` INT( 11 ) NOT NULL DEFAULT '0'";
$updates[201109011450][]="RENAME TABLE `cf_2` TO `cf_ab_contacts` ;";

$updates[201109011450][]="ALTER TABLE `cf_3` CHANGE `link_id` `model_id` INT( 11 ) NOT NULL DEFAULT '0'";
$updates[201109011450][]="RENAME TABLE `cf_3` TO `cf_ab_companies` ;";


$updates[201109021000][]="ALTER TABLE `ab_contacts` DROP `iso_address_format` ";

$updates[201108301656][]="INSERT INTO `go_model_types` (
`id` ,
`model_name`
)
VALUES (
'2', 'GO_Addressbook_Model_Contact'
);";
$updates[201108301656][]="INSERT INTO `go_model_types` (
`id` ,
`model_name`
)
VALUES (
'3', 'GO_Addressbook_Model_Company'
);";


$updates[201110031344][]="ALTER TABLE `ab_companies` DROP `iso_address_format`";
$updates[201110031344][]="ALTER TABLE `ab_companies` DROP `post_iso_address_format` ";
$updates[201110031344][]="ALTER TABLE `ab_companies` CHANGE `files_folder_id` `files_folder_id` INT( 11 ) NOT NULL DEFAULT '0'";
$updates[201110031344][]="ALTER TABLE `ab_addressbooks` CHANGE `files_folder_id` `files_folder_id` INT( 11 ) NOT NULL DEFAULT '0'";
$updates[201110031344][]="ALTER TABLE `ab_contacts` CHANGE `files_folder_id` `files_folder_id` INT( 11 ) NOT NULL DEFAULT '0'";
$updates[201110031344][]="ALTER TABLE `ab_contacts` CHANGE `go_user_id` `go_user_id` INT( 11 ) NOT NULL DEFAULT '0'";
$updates[201110031344][]="ALTER TABLE `ab_contacts` DROP `link_id` ";
$updates[201110031344][]="ALTER TABLE `ab_contacts` CHANGE `email_allowed` `email_allowed` BOOLEAN NOT NULL DEFAULT '1'";
$updates[201110031344][]="ALTER TABLE `ab_contacts` DROP `sid` ";
$updates[201110031344][]="ALTER TABLE `ab_contacts` DROP `color` ";
$updates[201110031344][]="ALTER TABLE `ab_contacts` DROP `source_id`";


$updates[201110141221][]="UPDATE ab_contacts SET email_allowed=0 where email_allowed=1";
$updates[201110141221][]="UPDATE ab_contacts SET email_allowed=1 where email_allowed=2";

$updates[201110170846][]="ALTER TABLE `ab_addressbooks` DROP `default_iso_address_format`";
