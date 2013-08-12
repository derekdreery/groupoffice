<?php
$updates["201108291013"][]="ALTER TABLE `co_comments` CHANGE `id` `id` INT( 11 ) NOT NULL AUTO_INCREMENT ";
$updates["201109070000"][]="ALTER TABLE `co_comments` CHANGE `link_id` `model_id` INT( 11 ) NOT NULL";
$updates["201109070000"][]="ALTER TABLE `co_comments` CHANGE `link_type` `model_type_id` INT( 11 ) NOT NULL";

$updates["201209181100"][]="CREATE TABLE IF NOT EXISTS `co_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(127) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
$updates["201209181100"][]="ALTER TABLE `co_comments` ADD `category_id` int(11) NOT NULL DEFAULT '0';";