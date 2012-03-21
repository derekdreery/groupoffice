<?php
$updates["201203201554"][]="ALTER TABLE `sm_installations` CHANGE `id` `id` INT( 11 ) NOT NULL AUTO_INCREMENT ";
$updates["201203201554"][]="ALTER TABLE `sm_installations` CHANGE `count_users` `count_users` INT( 11 ) NOT NULL DEFAULT '0'";
$updates["201203201554"][]="ALTER TABLE `sm_installations` CHANGE `install_time` `install_time` INT( 11 ) NOT NULL DEFAULT '0'";
$updates["201203201554"][]="ALTER TABLE `sm_installations` CHANGE `total_logins` `total_logins` INT( 11 ) NOT NULL DEFAULT '0'";
$updates["201203201554"][]="ALTER TABLE `sm_installations` CHANGE `report_ctime` `report_ctime` INT( 11 ) NOT NULL DEFAULT '0'";
$updates["201203201554"][]="ALTER TABLE `sm_installations` CHANGE `database_usage` `database_usage` INT( 11 ) NOT NULL DEFAULT '0'";
$updates["201203201554"][]="ALTER TABLE `sm_installations` CHANGE `file_storage_usage` `file_storage_usage` INT( 11 ) NOT NULL DEFAULT '0'";
$updates["201203201554"][]="ALTER TABLE `sm_installations` CHANGE `admin_country` `admin_country` CHAR( 2 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''";
$updates["201203201554"][]="ALTER TABLE `sm_installations` CHANGE `thousands_separator` `thousands_separator` CHAR( 1 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL ";
$updates["201203201554"][]="ALTER TABLE `sm_installations` CHANGE `billing` `billing` TINYINT( 1 ) NOT NULL DEFAULT '0'";
$updates["201203201554"][]="ALTER TABLE `sm_installations` CHANGE `professional` `professional` TINYINT( 1 ) NOT NULL DEFAULT '0'";
$updates["201203201554"][]="ALTER TABLE `sm_installations` CHANGE `status_change_time` `status_change_time` INT( 11 ) NOT NULL DEFAULT '0'";
$updates["201203201554"][]="ALTER TABLE `sm_installations` CHANGE `config_file` `config_file` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''";