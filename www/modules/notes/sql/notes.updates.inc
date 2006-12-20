<?php
$updates[12] = array();

$updates[11][] = "ALTER TABLE `no_notes` ADD `link_id` INT AFTER `id` ;";
$updates[11][] = "ALTER TABLE `no_notes` ADD INDEX ( `link_id` ) ;";

$updates[10][] = "DROP TABLE `no_catagories`;";
$updates[10][] = "ALTER TABLE `no_notes` DROP `catagory_id`,DROP `res_user_id`;";
$updates[10][] = "ALTER TABLE `no_notes` ADD `company_id` INT NOT NULL AFTER `contact_id`;";
$updates[10][] = "ALTER TABLE `no_notes` DROP `acl_read`,DROP `acl_write`;";
$updates[10][] = "ALTER TABLE `no_settings` DROP `show_notes`";
