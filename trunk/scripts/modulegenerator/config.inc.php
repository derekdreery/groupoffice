<?php
require('../../www/Group-Office.php');

//name of the module. No spaces or strange characters.
$module = 'currencies';

//Short name of the module. The prefix of the database tables.
$prefix = 'cu';

$tables=array();
//Tables to create an interface for

$westpanel = array(
	'mainpanel_tag'=> 'GRID',
	'template'=>'GridPanel.tpl',
	'name'=>'cu_currencies',
	'friendly_single'=>'currency',
	'friendly_multiple'=>'currencies',
	'authenticate'=>false,
	'paging'=>true,
	'autoload'=>false,
	'files'=>false);

$tables[] = $westpanel;

$main_template='SimpleMainPanel.tpl'; //The template for MainPanel.js