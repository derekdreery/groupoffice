<?php
require('../../www/Group-Office.php');

//name of the module. No spaces or strange characters.
$module = 'projects';

//Short name of the module. The prefix of the database tables.
$prefix = 'pm';

$tables=array();
//Tables to create an interface for


$westpanel = array(
	'mainpanel_tag'=> 'GRID',
	'template'=>'GridPanel.tpl',
	'name'=>'pm_expenses',
	'friendly_single'=>'expense',
	'friendly_multiple'=>'expenses',
	'authenticate'=>false,
	'paging'=>true,
	'autoload'=>false,
	'files'=>false);

$tables[] = $westpanel;

//$main_template='SimpleMainPanel.tpl'; //The template for MainPanel.js