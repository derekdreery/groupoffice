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

$updates[201110051633][]="ALTER TABLE `cal_events` CHANGE `uuid` `uuid` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''";

$updates[201110051633][]="ALTER TABLE `cal_events` CHANGE `resource_event_id` `resource_event_id` INT( 11 ) NOT NULL DEFAULT '0'";
$updates[201110051633][]="ALTER TABLE `cal_events` CHANGE `rrule` `rrule` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''";
$updates[201110051633][]="ALTER TABLE `cal_events` CHANGE `exception_for_event_id` `exception_for_event_id` INT( 11 ) NOT NULL DEFAULT '0'";

$updates[201110061133][]="ALTER TABLE `cal_participants` CHANGE `id` `id` INT( 11 ) NOT NULL AUTO_INCREMENT ";

$updates[201110071512][]="ALTER TABLE `cal_calendars` CHANGE `id` `id` INT( 11 ) NOT NULL AUTO_INCREMENT";
$updates[201110071512][]="ALTER TABLE `cal_calendars` ADD `files_folder_id` INT NOT NULL DEFAULT '0'";


$updates[201110071512][]="ALTER TABLE `cf_21` CHANGE `link_id` `model_id` INT( 11 ) NOT NULL DEFAULT '0'";
$updates[201110071512][]="RENAME TABLE `cf_21` TO `cf_cal_calendars` ;";

$updates[201110071512][]="ALTER TABLE `cal_calendars` CHANGE `public` `public` BOOLEAN NOT NULL DEFAULT '0'";
$updates[201110071512][]="ALTER TABLE `cal_calendars` CHANGE `shared_acl` `shared_acl` TINYINT( 1 ) NOT NULL DEFAULT '0'";
$updates[201110071512][]="ALTER TABLE `cal_calendars` CHANGE `show_bdays` `show_bdays` TINYINT( 1 ) NOT NULL DEFAULT '1'";
$updates[201110071512][]="ALTER TABLE `cal_calendars` CHANGE `project_id` `project_id` INT( 11 ) NOT NULL DEFAULT '0'";
$updates[201110071512][]="ALTER TABLE `cal_calendars` CHANGE `tasklist_id` `tasklist_id` INT( 11 ) NOT NULL DEFAULT '0'";