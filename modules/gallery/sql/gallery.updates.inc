<?php
$updates[11]=array();
$updates[10][] ="ALTER TABLE `ig_galleries` ADD `maxrows` TINYINT NOT NULL AFTER `maxcolumns`";
$updates[10][] ="ALTER TABLE `ig_images` ADD `user_id` INT NOT NULL AFTER `gallery_id` ;";
