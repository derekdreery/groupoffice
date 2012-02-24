<?php
for($link_type=1;$link_type<13;$link_type++)
{
	$sql = "CREATE TABLE IF NOT EXISTS `cf_$link_type` (
  `link_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`link_id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
	
	$db->query($sql);
}
?>