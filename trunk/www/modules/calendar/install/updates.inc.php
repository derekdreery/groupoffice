<?php
$updates[201109011450][]="RENAME TABLE `go_links_1` TO `go_links_cal_events`;";
$updates[201109011450][]="ALTER TABLE `go_links_cal_events` CHANGE `link_id` `model_id` INT( 11 ) NOT NULL";
$updates[201109011450][]="ALTER TABLE `go_links_cal_events` CHANGE `link_type` `model_type_id` INT( 11 ) NOT NULL";

$updates[201109011450][]="ALTER TABLE `cf_1` CHANGE `link_id` `model_id` INT( 11 ) NOT NULL DEFAULT '0'";
$updates[201109011450][]="RENAME TABLE `cf_1` TO `cf_cal_events` ;";

$updates[201108301656][]="INSERT INTO `go_model_types` (
`id` ,
`model_name`
)
VALUES (
'1', 'GO_Calendar_Model_Event'
);";


$updates[201108301656][]="delete from cal_events where calendar_id not in(select id from cal_calendars);";

$updates[201109140000][]="ALTER TABLE `cal_events` CHANGE `id` `id` INT( 11 ) NOT NULL AUTO_INCREMENT";
$updates[201109140000][]="ALTER TABLE `cal_events` CHANGE `all_day_event` `all_day_event` BOOLEAN NOT NULL DEFAULT '0'";
$updates[201109140000][]="ALTER TABLE `cal_events` CHANGE `busy` `busy` BOOLEAN NOT NULL DEFAULT '0'";
$updates[201109140000][]="ALTER TABLE `cal_events` CHANGE `private` `private` BOOLEAN NOT NULL DEFAULT '0'";
$updates[201109140000][]="ALTER TABLE `cal_events` CHANGE `read_only` `read_only` BOOLEAN NOT NULL DEFAULT '0'";
$updates[201109160000][]="ALTER TABLE `cal_exceptions` CHANGE `id` `id` INT( 11 ) NOT NULL AUTO_INCREMENT";

