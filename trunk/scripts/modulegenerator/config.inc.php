<?php
require('../../www/Group-Office.php');

//name of the module. No spaces or strange characters.
$module = 'links';

//Short name of the module. The prefix of the database tables.
$prefix = 'li';

$tables=array();
//Tables to create an interface for


$westpanel = array(
	'mainpanel_tag'=> 'GRID',
	'template'=>'GridPanel.tpl',
	'name'=>'go_link_descriptions', 
	'friendly_single'=>'link_description', 
	'friendly_multiple'=>'link_descriptions',
	'authenticate'=>false,
	'paging'=>true,
	'autoload'=>true,
	'files'=>false);

$tables[] = $westpanel;

$main_template='SimpleMainPanel.tpl';