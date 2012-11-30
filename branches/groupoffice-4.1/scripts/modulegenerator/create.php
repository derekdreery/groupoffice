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

if(isset($argv[1]))
	define('CONFIG_FILE', $argv[1]);

require('config.inc.php');

$module_dir=$GLOBALS['GO_CONFIG']->root_path.'modules/'.$module.'/';

require('classes/modulegenerator.class.inc.php');
$mg = new modulegenerator();



//exec('rm -Rf '.$module_dir);
$mg->create_module($module, $prefix, $main_template, $tables);	

echo "Finished\n";



?>
