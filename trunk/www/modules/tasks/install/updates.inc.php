<?php

$updates[201108190000][]="RENAME TABLE `go_links_12` TO `go_links_ta_tasks`;";
$updates[201108190000][]="ALTER TABLE `go_links_ta_tasks` CHANGE `link_id` `model_id` INT( 11 ) NOT NULL";
$updates[201108190000][]="ALTER TABLE `go_links_ta_tasks` CHANGE `link_type` `model_type_id` INT( 11 ) NOT NULL";


$updates[201108190000][]="RENAME TABLE `cf_12` TO `cf_ta_tasks` ";
$updates[201108190000][]="ALTER TABLE `cf_ta_tasks` CHANGE `link_id` `model_id` INT( 11 ) NOT NULL DEFAULT '0'";

$updates[201108301656][]="INSERT INTO `go_model_types` (
`id` ,
`model_name`
)
VALUES (
'12', 'GO_Tasks_Model_Task'
);";

$updates[201109070000][]="ALTER TABLE `ta_tasks` CHANGE `rrule` `rrule` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL";
$updates[201109140000][]="ALTER TABLE `ta_tasks` CHANGE `id` `id` INT( 11 ) NOT NULL AUTO_INCREMENT";