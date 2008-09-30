<?php
//name of the module. No spaces or strange characters.
$module = 'notes2';

//Short name of the module. The prefix of the database tables.
$prefix = 'no';

$tables=array();
//Tables to create an interface for


/*
 * If you specify a link_type then linking will be enabled. Make sure your table also has a 
 * ctime and mtime column for this to work. Also either authenticate or authenticate related must be set.
 * 
 * If you specify authenticate. Then make sure your table has an acl_read and acl_write column
 */

$westpanel = array(
	'mainpanel_tag'=> 'GRID',
	'template'=>'GridPanel.tpl',
	'name'=>'su_announcements', 
	'friendly_single'=>'announcement', 
	'friendly_multiple'=>'announcements',
	'authenticate'=>false,
	'paging'=>true,
	'autoload'=>true,
	'files'=>false);

$tables[] = $westpanel;

$main_template='SimpleMainPanel.tpl';
?>