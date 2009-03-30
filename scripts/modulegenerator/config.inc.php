<?php
require('../../../unstable/Group-Office.php');

//name of the module. No spaces or strange characters.
$module = 'workflow';

//Short name of the module. The prefix of the database tables.
$prefix = 'wf';

$tables=array();
//Tables to create an interface for


$westpanel = array(
	'mainpanel_tag'=> 'GRID',
	'template'=>'GridPanel.tpl',
	'name'=>'pm_templates', 
	'friendly_single'=>'template', 
	'friendly_multiple'=>'templates',
	'authenticate'=>true,
	'paging'=>true,
	'autoload'=>false,
	'files'=>true);

$tables[] = $westpanel;

