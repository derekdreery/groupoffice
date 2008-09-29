<?php
/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: create.php 2845 2008-08-27 10:33:31Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

require('../../www/Group-Office.php');

//name of the module. No spaces or strange characters.
$module = 'summary';

//Short name of the module. The prefix of the database tables.
$prefix = 'su';

$tables=array();
//Tables to create an interface for


/*
 * If you specify a link_type then linking will be enabled. Make sure your table also has a 
 * ctime and mtime column for this to work. Also either authenticate or authenticate related must be set.
 * 
 * If you specify authenticate. Then make sure your table has an acl_read and acl_write column
 */


$folders = array(
	'template'=>'GridPanel.tpl',
	'name'=>'us_licenses', 
	'friendly_single'=>'announcement', 
	'friendly_multiple'=>'announcements',
	'paging'=>true,
	'autoload'=>true,
	'authenticate'=>false);

$tables[] = $folders;


/* end config */
$main_template='MainPanel.tpl';


$module_dir=$GO_CONFIG->root_path.'modules/'.$module.'/';

require('classes/modulegenerator.class.inc.php');
$mg = new modulegenerator();

//exec('rm -Rf '.$module_dir);
$mg->create_module($module, $prefix, $main_template, $tables);	

echo "Finished\n";



?>