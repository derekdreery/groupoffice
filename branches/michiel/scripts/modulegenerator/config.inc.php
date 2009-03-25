<?php
require('../../www/Group-Office.php');

//name of the module. No spaces or strange characters.
$module = 'userhtml';

//Short name of the module. The prefix of the database tables.
$prefix = 'uh';

$tables=array();
//Tables to create an interface for


$westpanel = array(
	'mainpanel_tag'=> 'GRID',
	'template'=>'GridPanel.tpl',
	'name'=>'uh_pages', 
	'friendly_single'=>'userpage', 
	'friendly_multiple'=>'userpages',
	'authenticate'=>false,
	'paging'=>true,
	'autoload'=>false,
	'files'=>false);

$tables[] = $westpanel;

$main_template='SimpleMainPanel.tpl';