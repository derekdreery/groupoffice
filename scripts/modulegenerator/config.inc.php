<?php
require('../../www/Group-Office.php');

//name of the module. No spaces or strange characters.
$module = 'log';

//Short name of the module. The prefix of the database tables.
$prefix = 'lo';

$tables=array();
//Tables to create an interface for


$westpanel = array(
	'mainpanel_tag'=> 'GRID',
	'template'=>'GridPanel.tpl',
	'name'=>'go_log', 
	'friendly_single'=>'entry', 
	'friendly_multiple'=>'entries',
	'authenticate'=>false,
	'paging'=>true,
	'autoload'=>true,
	'files'=>false);

$tables[] = $westpanel;

$main_template='SimpleMainPanel.tpl'; //The template for MainPanel.js

