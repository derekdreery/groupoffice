<?php
$updates[29] = array();

$updates[28][] = "ALTER TABLE `cms_sites` ADD `name` VARCHAR( 100 ) NOT NULL ;";

$updates[27][] = "ALTER TABLE `cms_sites` ADD `language` CHAR( 10 ) NOT NULL ;";
$updates[27][] = "UPDATE `cms_sites` SET language='nl';";

$updates[26][] = "ALTER TABLE `cms_templates` ADD `fckeditor_styles` TEXT NOT NULL ;";

$updates[25][] = "ALTER TABLE `cms_templates` ADD `login_template_item_id` INT NOT NULL ;";

$updates[24][] = "ALTER TABLE `cms_files` ADD FULLTEXT ( `name`)";
$updates[24][] = "ALTER TABLE `cms_files` ADD FULLTEXT ( `content`)";
$updates[24][] = "ALTER TABLE `cms_files` CHANGE `content` `content` LONGTEXT ";

$updates[23][] = "CREATE TABLE `cms_comments` (
  `id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `comments` text NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `file_id` (`file_id`)
) TYPE=MyISAM;";


$updates[22][] = "ALTER TABLE `cms_templates` ADD `doctype` TEXT NOT NULL ;";
$updates[22][] = "UPDATE `cms_templates` SET `doctype` = '<?xml version=\"1.0\" encoding=\"UTF-8\"?> <!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">'";


$updates[21][] = "ALTER TABLE `cms_files` ADD `hot_item_text` TEXT NOT NULL AFTER `hot_item` ;";

$updates[20][] = "ALTER TABLE `cms_files` ADD `ctime` INT NOT NULL AFTER `size` ;";
$updates[20][] = "UPDATE cms_files SET ctime=mtime;";

$updates[20][] = "ALTER TABLE `cms_folders` ADD `ctime` INT NOT NULL AFTER `parent_id` ;";
$updates[20][] = "UPDATE cms_folders SET ctime=mtime;";

$updates[19][] = "ALTER TABLE `cms_sites` ADD `start_file_id` INT NOT NULL ;";

$updates[18][] = "ALTER TABLE `cms_folders` ADD `acl` INT NOT NULL ;";
$updates[18][] = "ALTER TABLE `cms_files` ADD `acl` INT NOT NULL ;";
$updates[18][] = "ALTER TABLE `cms_sites` DROP `acl_read` ";

$updates[17][] = "ALTER TABLE `cms_folders` DROP INDEX `id` ";
$updates[17][] = "ALTER TABLE `cms_folders` ADD `template_item_id` INT NOT NULL ;";
$updates[17][] = "ALTER TABLE `cms_sites` ADD `root_folder_id` INT NOT NULL ;";
$updates[17][] = "ALTER TABLE `cms_sites` DROP `multilingual` ";
$updates[17][] = "ALTER TABLE `cms_templates` DROP `activate_frontpage` , DROP `main_template_item_id` , DROP `frontpage_template_item_id` ";
$updates[17][] = "ALTER TABLE `cms_template_items` CHANGE `main` `page` ENUM( '0', '1' ) NOT NULL DEFAULT '0'";
$updates[17][] = "UPDATE `cms_template_items` SET page = '1'";
$updates[17][] = "ALTER TABLE `cms_template_items` ADD INDEX ( `page` ) ";

$updates[16][] = "ALTER TABLE `cms_files` ADD `template_item_id` INT NOT NULL ;";
$updates[16][] = "ALTER TABLE `cms_sites` DROP INDEX `id` ";
$updates[16][] = "ALTER TABLE `cms_sites` ADD `template_id` INT NOT NULL ;";
$updates[16][] = "ALTER TABLE `cms_languages` CHANGE `template_id` `template_item_id` INT( 11 ) NOT NULL DEFAULT '0'";

$updates[15][] = "ALTER TABLE `cms_files` ADD `auto_meta` ENUM( '0', '1' ) DEFAULT '1' NOT NULL AFTER `content` ;";
$updates[15][] = "DROP TABLE IF EXISTS `qn_actions`;";
$updates[15][] = "DROP TABLE IF EXISTS `qn_answers`;";
$updates[15][] = "DROP TABLE IF EXISTS `qn_emails`;";
$updates[15][] = "DROP TABLE IF EXISTS `qn_questionnaires`;";
$updates[15][] = "DROP TABLE IF EXISTS `cms_subscribed`;";
$updates[15][] = "DROP TABLE IF EXISTS `cms_search_files`;";
$updates[15][] = "DROP TABLE IF EXISTS `cms_search_words`;";

$updates[14][] = "ALTER TABLE `cms_languages` ADD `language_code` CHAR( 2 ) NOT NULL";
$updates[14][] = "ALTER TABLE `cms_templates` ADD `print_style` TEXT NOT NULL AFTER `additional_style`;";

$updates[13][] = "ALTER TABLE `cms_sites` ADD `allow_properties` ENUM( '0', '1' ) DEFAULT '0' NOT NULL AFTER `acl_read` ";
$updates[13][] = "ALTER TABLE `cms_sites` ADD `multilingual` ENUM( '0', '1' ) NOT NULL AFTER `acl_read`";
$updates[13][] = "ALTER TABLE `cms_sites` ADD `webmaster` VARCHAR( 100 ) NOT NULL AFTER `domain`";
$updates[13][] = "ALTER TABLE `cms_files` CHANGE `name` `name` VARCHAR( 255 ) NOT NULL"; 
$updates[13][] = "ALTER TABLE `cms_folders` CHANGE `name` `name` CHAR( 255 ) NOT NULL";
$updates[13][] = "ALTER TABLE `cms_sites` DROP `display_type`";
$updates[13][] = "ALTER TABLE `cms_folders` CHANGE `disable_multipage` `multipage` ENUM( '0', '1' ) DEFAULT NULL";

$updates[12][] = "ALTER TABLE `cms_templates` DROP `root_folder_id`;";
$updates[12][] = "ALTER TABLE `cms_templates` ADD `activate_frontpage` ENUM( '0', '1' ) NOT NULL AFTER `restrict_editor` ;";
$updates[12][] = "ALTER TABLE `cms_templates` ADD `main_template_item_id` INT NOT NULL AFTER `activate_frontpage` ,
ADD `frontpage_template_item_id` INT NOT NULL AFTER `main_template_item_id` ;";

$updates[11][] = "ALTER TABLE `cms_files` ADD INDEX ( `folder_id` );";

$updates[10][] = "ALTER TABLE `cms_sites` ADD `domain` varchar(100) NOT NULL default '';";
$updates[10][] = "ALTER TABLE `cms_sites` ADD `display_type` ENUM( '0', '1');";
$updates[10][] = "ALTER TABLE `cms_files` ADD `hot_item` ENUM( '0', '1');";
$updates[10][] = "ALTER TABLE `cms_folders` ADD `disable_multipage` ENUM( '0', '1');";
