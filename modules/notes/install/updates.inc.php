<?php
$updates[]="ALTER TABLE no_notes ADD FULLTEXT (content);";
$updates[]="ALTER TABLE `no_notes` CHANGE `name` `name` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL";

$updates[]="ALTER TABLE `no_notes` ADD `files_folder_id` INT NOT NULL;";
$updates[]="script:1_convert_acl.inc.php";
?>