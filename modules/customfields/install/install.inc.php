<?php
$db = new db();
for($link_type=1;$link_type<20;$link_type++)
{
	$sql = "CREATE TABLE IF NOT EXISTS `cf_$link_type` (
  `link_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`link_id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

	$db->query($sql);

	$sql = "CREATE TABLE IF NOT EXISTS `go_links_$link_type` (
  `id` int(11) NOT NULL,
  `folder_id` int(11) NOT NULL,
  `link_id` int(11) NOT NULL,
  `link_type` int(11) NOT NULL,
  `description` varchar(100) NOT NULL,
  KEY `link_id` (`link_id`,`link_type`),
  KEY `id` (`id`,`folder_id`)
)  DEFAULT CHARSET=utf8;";
	$db->query($sql);
}