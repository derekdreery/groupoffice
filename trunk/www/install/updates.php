<?php
$updates[201108120000][]="UPDATE go_modules SET version=0";

$updates[201108120000][]="ALTER TABLE `go_users` CHANGE `id` `id` INT( 11 ) NOT NULL AUTO_INCREMENT";

$updates[201108120000][]="ALTER TABLE `go_acl_items` CHANGE `id` `id` INT( 11 ) NOT NULL AUTO_INCREMENT";

$updates[201108120000][]="ALTER TABLE `go_search_cache` DROP `table` ,
DROP `url` ,
DROP `link_count` ,
DROP `acl_read` ;";

$updates[201108120000][]="ALTER TABLE `go_users` CHANGE `max_rows_list` `max_rows_list` TINYINT( 4 ) NOT NULL DEFAULT '20'";
$updates[201108120000][]="ALTER TABLE `go_users` CHANGE `registration_time` `ctime` INT( 11 ) NOT NULL DEFAULT '0'";
$updates[201108120000][]="ALTER TABLE `go_groups` CHANGE `id` `id` INT( 11 ) NOT NULL AUTO_INCREMENT";

$updates[201108181012][]="ALTER TABLE `go_search_cache` CHANGE `description` `description` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''";

$updates[201108181012][]="script:11_users_to_addressbook.inc.php";

$updates[201108181012][]="ALTER TABLE `go_users`
  DROP `initials`,
  DROP `title`,
  DROP `sex`,
  DROP `birthday`,
  DROP `company`,
  DROP `department`,
  DROP `function`,
  DROP `home_phone`,
  DROP `work_phone`,
  DROP `fax`,
  DROP `cellular`,
  DROP `country`,
  DROP `state`,
  DROP `city`,
  DROP `zip`,
  DROP `address`,
  DROP `address_no`,
  DROP `homepage`,
  DROP `work_address`,
  DROP `work_address_no`,
  DROP `work_zip`,
  DROP `work_country`,
  DROP `work_state`,
  DROP `work_city`,
  DROP `work_fax`,
  DROP `contact_id`;";




$updates[201108301656][]="CREATE TABLE IF NOT EXISTS `go_model_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model_name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";



$updates[201108301656][]="ALTER TABLE `go_search_cache` CHANGE `id` `model_id` INT( 11 ) NOT NULL DEFAULT '0'";

$updates[201108301656][]="ALTER TABLE `go_search_cache` CHANGE `link_type` `model_type_id` INT( 11 ) NOT NULL DEFAULT '0' ";
	
$updates[201108301656][]="ALTER TABLE `go_search_cache` CHANGE `type` `model_name` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL  ";
$updates[201108301656][]="ALTER TABLE `go_search_cache` DROP PRIMARY KEY  ";
$updates[201108301656][]="ALTER TABLE `go_search_cache` ADD PRIMARY KEY ( `model_id` , `model_type_id` ) ;";



$updates[201108301656][]="INSERT INTO `go_model_types` (
`id` ,
`model_name`
)
VALUES (
'8', 'GO_Base_Model_User'
);";

$updates[201108301656][]="ALTER TABLE `go_search_cache` ADD `type` VARCHAR( 20 ) NOT NULL ";



$updates[201108190000][]="RENAME TABLE `go_links_8` TO `go_links_go_users`;";
$updates[201108190000][]="ALTER TABLE `go_links_go_users` CHANGE `link_id` `model_id` INT( 11 ) NOT NULL";
$updates[201108190000][]="ALTER TABLE `go_links_go_users` CHANGE `link_type` `model_type_id` INT( 11 ) NOT NULL";