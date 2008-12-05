<?php
$updates[] = "script:1.inc.php";
//do it twice because it went wrong the first time on some installations
$updates[] = "script:1.inc.php";
$updates[] = "script:2.inc.php";

$updates[] = "ALTER TABLE `fs_folders` CHANGE `path` `path` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL";  

$updates[]="UPDATE fs_files SET path=replace(path, '".$GO_CONFIG->file_storage_path."','');";
$updates[]="UPDATE fs_folders SET path=replace(path, '".$GO_CONFIG->file_storage_path."','');";
$updates[]="ALTER TABLE `fs_folders` ADD `thumbs` ENUM( '0', '1' ) NOT NULL DEFAULT '0';";

$updates[]="ALTER TABLE `fs_folders` CHANGE `comments` `comments` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL";
$updates[]="ALTER TABLE `fs_files` CHANGE `comments` `comments` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL";  
$updates[]="ALTER TABLE `fs_files` CHANGE `locked_user_id` `locked_user_id` INT( 11 ) NOT NULL DEFAULT '0'";
$updates[]="ALTER TABLE `fs_templates` CHANGE `content` `content` MEDIUMBLOB NOT NULL";