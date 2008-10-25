<?php
require('../../www/Group-Office.php');

//name of the module. No spaces or strange characters.
$module = 'comments';

//Short name of the module. The prefix of the database tables.
$prefix = 'co';

$tables=array();
//Tables to create an interface for


$westpanel = array(
	'mainpanel_tag'=> 'GRID',
	'template'=>'GridPanel.tpl',
	'name'=>'co_comments', 
	'friendly_single'=>'comment', 
	'friendly_multiple'=>'comments',
	'authenticate'=>false,
	'paging'=>true,
	'autoload'=>false,
	'files'=>false);

$tables[] = $westpanel;

$main_template='SimpleMainPanel.tpl';
?>
