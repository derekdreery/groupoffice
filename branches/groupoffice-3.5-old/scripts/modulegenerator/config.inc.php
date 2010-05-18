<?php
require('../../www/Group-Office.php');
//name of the module. No spaces or strange characters.
$module = 'filesearch';

//Short name of the module. The prefix of the database tables.
$prefix = 'fs';

$tables=array();
//Tables to create an interface for


/*
 * If you specify a link_type then linking will be enabled. Make sure your table also has a
 * ctime and mtime column for this to work. Also either authenticate or authenticate related must be set.
 *
 * If you specify authenticate. Then make sure your table has an acl_read and acl_write column
 */

$westpanel = array(
	'template'=>'GridPanel.tpl', //The template to use for the grid. This is the only option at the moment
	'name'=>'fs_docbundles',  //Name of the table
	'friendly_single'=>'docbundle', //Name for a single item in this table. Must be lower case and alphanummeric
	'friendly_multiple'=>'docbundles',//Name for a multiple items in this table. Must be lower case and alphanummeric
	'authenticate'=>true,//Secure these items with authentication? If true then acl_read and acl_write columns must be defined in the table
	'paging'=>true, //Use pagination in the grid?
	'autoload'=>false, //Automatically load this table with data after rendering?
	'files'=>false, //Can files be uploaded to these items?
	'link_type'=>16
	);

$tables[] = $westpanel;

//$main_template='SimpleMainPanel.tpl'; //The template for MainPanel.js
?>