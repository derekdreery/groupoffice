<?php
$updates[201109011450][]="RENAME TABLE `go_links_1` TO `go_links_cal_events`;";
$updates[201109011450][]="ALTER TABLE `go_links_cal_events` CHANGE `link_id` `model_id` INT( 11 ) NOT NULL";
$updates[201109011450][]="ALTER TABLE `go_links_cal_events` CHANGE `link_type` `model_type_id` INT( 11 ) NOT NULL";

$updates[201109011450][]="ALTER TABLE `cf_1` CHANGE `link_id` `model_id` INT( 11 ) NOT NULL DEFAULT '0'";
$updates[201109011450][]="RENAME TABLE `cf_1` TO `cf_cal_events` ;";