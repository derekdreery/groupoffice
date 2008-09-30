<?php
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
	'mainpanel_tag'=> 'WESTPANEL',
	'template'=>'GridPanel.tpl',
	'name'=>'no_categories', 
	'friendly_single'=>'category', 
	'friendly_multiple'=>'categories',
	'authenticate'=>true,
	'paging'=>false,
	'autoload'=>true,
	'files'=>false,
	'select_fields'=>array());

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
		),
	'template'=>'GridPanel.tpl',
	'name'=>'no_notes', 
	'friendly_single'=>'note', 
	'friendly_multiple'=>'notes',
	'paging'=>true,
	'autoload'=>false,
	'authenticate'=>false,
	'authenticate_relation'=>true,
	'files'=>true,
	'link_type'=>4,
	'relation'=>array('field'=>'category_id', 'remote_field'=>'id', 'remote_table'=>$westpanel));
		
$main_template='MainPanel.tpl';
?>