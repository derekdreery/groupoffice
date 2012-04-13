<?php
$updates["201110140934"][]="ALTER TABLE `bm_bookmarks` CHANGE `id` `id` INT( 11 ) NOT NULL AUTO_INCREMENT";
$updates["201110140934"][]="ALTER TABLE `bm_categories` CHANGE `id` `id` INT( 11 ) NOT NULL AUTO_INCREMENT";

$updates["201202080800"][]="ALTER TABLE `bm_bookmarks` CHANGE `behave_as_module` `behave_as_module` BOOLEAN NOT NULL DEFAULT '0'";
$updates["201202080800"][]="ALTER TABLE `bm_bookmarks` CHANGE `open_extern` `open_extern` BOOLEAN NOT NULL DEFAULT '1'";
$updates["201202080800"][]="ALTER TABLE `bm_bookmarks` CHANGE `public_icon` `public_icon` BOOLEAN NOT NULL DEFAULT '0'";
$updates["201203011316"][]="script:1_fixPermissions.php";