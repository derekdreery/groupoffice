<?php
$updates[201108190000][]='ALTER TABLE `no_categories` CHANGE `id` `id` INT( 11 ) NOT NULL AUTO_INCREMENT ';
$updates[201108190000][]='ALTER TABLE `no_notes` CHANGE `id` `id` INT( 11 ) NOT NULL AUTO_INCREMENT ';
$updates[201108190000][]="RENAME TABLE `go_links_4` TO `go_links_no_notes`;";
$updates[201108190000][]="ALTER TABLE `go_links_no_notes` CHANGE `link_id` `model_id` INT( 11 ) NOT NULL";
$updates[201108190000][]="ALTER TABLE `go_links_no_notes` CHANGE `link_type` `model_type_id` INT( 11 ) NOT NULL";


$updates[201109011450][]="RENAME TABLE `cf_4` TO `cf_no_notes` ";
$updates[201109011450][]="ALTER TABLE `cf_no_notes` CHANGE `link_id` `id` INT( 11 ) NOT NULL DEFAULT '0'";
$updates[201109011450][]="ALTER TABLE `cf_categories` CHANGE `type` `type` VARCHAR( 50 ) NOT NULL DEFAULT '0'";
$updates[201109011450][]="update cf_categories set type='GO_Notes_Model_Note' where type=4;";