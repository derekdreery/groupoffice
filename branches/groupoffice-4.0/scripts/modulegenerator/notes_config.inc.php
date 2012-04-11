<?php
require('../../www/Group-Office.php');
//name of the module. No spaces or strange characters.
$module = 'notes2';

//Short name of the module. The prefix of the database tables.
$prefix = 'no';

$tables=array();
//Tables to create an interface for


/*
 * If you specify a link_type then linking will be enabled. Make sure your table also has a 
 * ctime and mtime column for this to work. Also either authenticate or authenticate related must be set.
 * 
 * If you specify authenticate. Then make sure your table has an acl_read and acl_write column
 */

$westpanel = array(
	'mainpanel_tag'=> 'WESTPANEL', //{WESTPANEL} will be replaced in the MainPanel template defined below.
	'template'=>'GridPanel.tpl', //The template to use for the grid. This is the only option at the moment
	'name'=>'no_categories',  //Name of the table
	'friendly_single'=>'category', //Name for a single item in this table. Must be lower case and alphanummeric
	'friendly_multiple'=>'categories',//Name for a multiple items in this table. Must be lower case and alphanummeric
	'authenticate'=>true,//Secure these items with authentication? If true then acl_read and acl_write columns must be defined in the table
	'paging'=>false, //Use pagination in the grid?
	'autoload'=>true, //Automatically load this table with data after rendering?
	'files'=>false //Can files be uploaded to these items?
	); 

$tables[] = $westpanel;

$tables[] = array(
	'mainpanel_tag'=> 'CENTERPANEL',
	'mainpanel_tags'=>array(
		'centerpanel_related_field'=>'category_id',
		'centerpanel_related_friendly_multiple_ucfirst'=>'Categories',
		'centerpanel_related_friendly_multiple'=>'categories',
		'centerpanel_friendly_single_ucfirst'=>'Note',
		'centerpanel_friendly_single'=>'note',
		'EASTPANEL'=>'GO.notes.NotePanel'
		), //Custom tags for the mainpanel template that will be replaced
	'template'=>'GridPanel.tpl',
	'name'=>'no_notes', 
	'friendly_single'=>'note', 
	'friendly_multiple'=>'notes',
	'paging'=>true,
	'autoload'=>false,
	'authenticate'=>false,
	'authenticate_relation'=>true, //Authenticate a related table. In this example the notes categories.
	'files'=>true,
	'link_type'=>4, //If a link type is specified then this item will be linkable to other items. Choose a free identifier above 100!
	'relation'=>array('field'=>'category_id', 'remote_field'=>'id', 'remote_table'=>$westpanel)); //Define a relation between the tables
		
$main_template='MainPanel.tpl'; //The template for MainPanel.js
?>