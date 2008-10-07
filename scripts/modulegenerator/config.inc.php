<?php
require('../../www/Group-Office.php');

//name of the module. No spaces or strange characters.
$module = 'tasks';

//Short name of the module. The prefix of the database tables.
$prefix = 'ta';

$tables=array();
//Tables to create an interface for


$westpanel = array(
	'mainpanel_tag'=> 'GRID',
	'template'=>'GridPanel.tpl',
	'name'=>'pm_tasks', 
	'friendly_single'=>'task', 
	'friendly_multiple'=>'types',
	'authenticate'=>true,
	'paging'=>true,
	'autoload'=>true,
	'files'=>false);

$tables[] = $westpanel;

$main_template='SimpleMainPanel.tpl';
?>
