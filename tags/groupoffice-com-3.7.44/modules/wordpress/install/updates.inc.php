<?php
$updates[]="ALTER TABLE `wp_posts` ADD `categories` VARCHAR( 255 ) NOT NULL ";
$updates[]="ALTER TABLE `wp_posts` CHANGE `title` `title` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ";