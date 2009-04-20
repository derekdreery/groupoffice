<?php
require('../../www/Group-Office.php');

//name of the module. No spaces or strange characters.
$module = 'email';

//Short name of the module. The prefix of the database tables.
$prefix = 'em';

$tables=array();
//Tables to create an interface for


$westpanel = array(
	'mainpanel_tag'=> 'GRID',
	'template'=>'GridPanel.tpl',
	'name'=>'em_aliases', 
	'friendly_single'=>'alias', 
	'friendly_multiple'=>'aliases',
	'authenticate'=>false,
	'paging'=>true,
	'autoload'=>false,
	'files'=>false);

$tables[] = $westpanel;

//$main_template='SimpleMainPanel.tpl'; //The template for MainPanel.js