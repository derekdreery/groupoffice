<?php
$updates[] = "script:1.inc.php";
//do it twice because it went wrong the first time on some installations
$updates[] = "script:1.inc.php";
$updates[] = "script:2.inc.php";

$updates[] = "ALTER TABLE `fs_folders` CHANGE `path` `path` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL";  

$updates[]="UPDATE fs_files SET path=replace(path, '".$GO_CONFIG->file_storage_path."','');";
$updates[]="UPDATE fs_folders SET path=replace(path, '".$GO_CONFIG->file_storage_path."','');";