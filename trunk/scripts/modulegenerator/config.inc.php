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
	'name'=>'pm_project_contacts', 
	'friendly_single'=>'project_contact', 
	'friendly_multiple'=>'project_contacts',
	'authenticate'=>false,
	'paging'=>true,
	'autoload'=>true,
	'files'=>false);

$tables[] = $westpanel;

$westpanel = array(
	'mainpanel_tag'=> 'GRID',
	'template'=>'GridPanel.tpl',
	'name'=>'pm_contact_statuses', 
	'friendly_single'=>'contact_status', 
	'friendly_multiple'=>'contact_statuses',
	'authenticate'=>false,
	'paging'=>true,
	'autoload'=>true,
	'files'=>false);

$tables[] = $westpanel;

