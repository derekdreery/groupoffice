<?php
require('../../www/Group-Office.php');

//name of the module. No spaces or strange characters.
$module = 'summary';

//Short name of the module. The prefix of the database tables.
$prefix = 'su';

$tables=array();
//Tables to create an interface for


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
