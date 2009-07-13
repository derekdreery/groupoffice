<?php
require('../../www/Group-Office.php');

//name of the module. No spaces or strange characters.
$module = 'cabinet';

//Short name of the module. The prefix of the database tables.
$prefix = 'ca';

$tables=array();
//Tables to create an interface for


$westpanel = array(
	'mainpanel_tag'=> 'GRID',
	'template'=>'GridPanel.tpl',
	'name'=>'ca_cabinets',
	'friendly_single'=>'cabinet',
	'friendly_multiple'=>'cabinets',
	'authenticate'=>false,
	'paging'=>true,
	'autoload'=>false,
	'files'=>false);

$tables[] = $westpanel;

$main_template='SimpleMainPanel.tpl'; //The template for MainPanel.js