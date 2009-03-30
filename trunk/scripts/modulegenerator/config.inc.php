<?php
require('../../www/Group-Office.php');

//name of the module. No spaces or strange characters.
$module = 'workflow';

//Short name of the module. The prefix of the database tables.
$prefix = 'wf';

$tables=array();
//Tables to create an interface for


$westpanel = array(
	'mainpanel_tag'=> 'GRID',
	'template'=>'GridPanel.tpl',
	'name'=>'wf_processes', 
	'friendly_single'=>'process', 
	'friendly_multiple'=>'processes',
	'authenticate'=>true,
	'paging'=>true,
	'autoload'=>false,
	'files'=>false);

$tables[] = $westpanel;

$westpanel = array(
	'mainpanel_tag'=> 'GRID',
	'template'=>'GridPanel.tpl',
	'name'=>'wf_steps', 
	'friendly_single'=>'step', 
	'friendly_multiple'=>'steps',
	'authenticate'=>false,
	'paging'=>true,
	'autoload'=>false,
	'files'=>false);

$tables[] = $westpanel;

$westpanel = array(
	'mainpanel_tag'=> 'GRID',
	'template'=>'GridPanel.tpl',
	'name'=>'wf_step_history', 
	'friendly_single'=>'step_history_item', 
	'friendly_multiple'=>'step_history',
	'authenticate'=>false,
	'paging'=>true,
	'autoload'=>false,
	'files'=>false);

$tables[] = $westpanel;

$main_template='SimpleMainPanel.tpl'; //The template for MainPanel.js

