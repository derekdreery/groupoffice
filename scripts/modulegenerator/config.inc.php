<?php
require('../../www/Group-Office.php');

//name of the module. No spaces or strange characters.
$module = 'tenders';

//Short name of the module. The prefix of the database tables.
$prefix = 'te';

$tables=array();
//Tables to create an interface for


$westpanel = array(
	'mainpanel_tag'=> 'GRID',
	'template'=>'GridPanel.tpl',
	'name'=>'te_tenders', 
	'friendly_single'=>'tender', 
	'friendly_multiple'=>'tenders',
	'authenticate'=>false,
	'paging'=>true,
	'autoload'=>false,
	'files'=>false);

$tables[] = $westpanel;

$main_template='SimpleMainPanel.tpl'; //The template for MainPanel.js