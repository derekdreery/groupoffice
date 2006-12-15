 <?php
$updates[11] = array();

$updates[10][] = "ALTER TABLE `td_todos` CHANGE `remind_time` `reminder` INT( 11 ) DEFAULT '0' NOT NULL"; 
$updates[10][] = "UPDATE td_todos SET reminder=0;";
$updates[10][] = "CREATE TABLE `td_reminders` (
  `user_id` int(11) NOT NULL default '0',
  `todo_id` int(11) NOT NULL default '0',
  `remind_time` int(11) NOT NULL default '0',
  KEY `user_id` (`user_id`),
  KEY `remind_time` (`remind_time`)
) TYPE=MyISAM;";
?>
