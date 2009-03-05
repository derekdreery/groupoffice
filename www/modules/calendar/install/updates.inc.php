<?php
$updates[]="ALTER TABLE `cal_events` CHANGE `rrule` `rrule` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL";
$updates[]="ALTER TABLE `cal_events` ADD `background` CHAR( 6 ) NOT NULL DEFAULT 'ebf1e2';";
$updates[]="ALTER TABLE `cal_events` ADD INDEX ( `participants_event_id` )";

$updates[]="DROP TABLE IF EXISTS `cal_settings`;";
$updates[]="CREATE TABLE `cal_settings` (
`user_id` INT NOT NULL ,
`reminder` INT NOT NULL ,
`color` CHAR( 6 ) NOT NULL ,
PRIMARY KEY ( `user_id` )
) ENGINE = MYISAM DEFAULT CHARSET=utf8;"; 
?>