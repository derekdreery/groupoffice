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
	'name'=>'pm_custom_reports',
	'friendly_single'=>'custom_report',
	'friendly_multiple'=>'custom_reports',
	'link_type'=>'14',
	'authenticate'=>false,
	'paging'=>true,
	'autoload'=>true,
	'files'=>false);

$tables[] = $westpanel;

//$main_template='SimpleMainPanel.tpl'; //The template for MainPanel.js