<?php
require('../../Group-Office.php');

//name of the module. No spaces or strange characters.
$module = 'shipping';

//Short name of the module. The prefix of the database tables.
$prefix = 'sh';

//Tables to create an interface for
$tables[] = array('name'=>'sh_destinations', 'friendly_single'=>'destination', 'friendly_multiple'=>'destinations', 'select_fields'=>array());

//Add some fields that have a pulldown menu.
$select_fields['destination_id']=array('class'=>$module, 'function'=>'get_destinations();','value'=>'id','text'=>'name');
$tables[] = array('name'=>'sh_jobs', 'friendly_single'=>'job', 'friendly_multiple'=>'jobs', 'select_fields'=>$select_fields);



require('generatecode.php');

$module_dir=$GO_CONFIG->root_path.'modules/'.$module.'/';
exec('rm -Rf '.$module_dir);

mkdir($module_dir);
mkdir($module_dir.'classes/');
mkdir($module_dir.'sql/');
mkdir($module_dir.'language/');

$module_info_file=
'<?php
$module[\''.$module.'\'][\'description\'] = \'\';
$module[\''.$module.'\'][\'version\'] = \'0.1\';
$module[\''.$module.'\'][\'release_date\'] = \'2006-11-29\';
$module[\''.$module.'\'][\'status\'] = \'Alpha\';
$module[\''.$module.'\'][\'authors\'][] = array(\'name\'=>\'Merijn Schering\', \'email\'=>\'mschering@intermesh.nl\');
$module[\''.$module.'\'][\'sort_order\'] = \'1000\';
$module[\''.$module.'\'][\'linkable_items\'][] = array();
?>';

file_put_contents($module_dir.'module.info', $module_info_file);

$lang_file=
'<?php
//Uncomment this line in new translations!
//require_once($GO_LANGUAGE->get_fallback_language_file(\''.$module.'\'));

$lang_modules[\''.$module.'\']=\''.$module.'\';
?>';

file_put_contents($module_dir.'language/en.inc', $lang_file);


$class_file=
'<?php
/**
 * @copyright Intermesh 2006
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.00 $ $Date: 2006/12/05 11:37:30 $
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */

class '.$module.' extends db {

	function '.$module.'() {
		$this->db();
	}
}
?>';

file_put_contents($module_dir.'classes/'.$module.'.class.inc', $class_file);


$index_file = 
'<?php
/**
 * @copyright Intermesh 2006
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.00 $ $Date: 2006/12/05 11:37:30 $
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */
 
//Initialize Group-Office framework
require_once(\'../../Group-Office.php\');

//Load commonly used controls
load_basic_controls();

//Authenticate the user for the framework
$GO_SECURITY->authenticate();

//Authenticate the user for the module
$GO_MODULES->authenticate(\''.$module.'\');

//Get the language variables
require_once($GO_LANGUAGE->get_language_file(\''.$module.'\'));

//Require the module class
require_once($GO_MODULES->class_path.\''.$module.'.class.inc\');
$'.$module.' = new '.$module.'();

//Declare variables
$task = isset($_REQUEST[\'task\']) ? $_REQUEST[\'task\'] : \'\';
$link_back=$_SERVER[\'PHP_SELF\'];

$form = new form(\''.$module.'_form\');
$form->add_html_element(new input(\'hidden\',\'task\',\'\',false));

//$form->add_html_element(new html_element(\'h1\', $lang_modules[\''.$module.'\']));

//Create tabstrip control 
$tabstrip = new tabstrip(\''.$module.'_tabstrip\', $lang_modules[\''.$module.'\']);
$tabstrip->set_attribute(\'style\',\'width:100%\');
';

foreach($tables as $table)
{
	$index_file .= '$tabstrip->add_tab(\''.$table['friendly_multiple'].'.inc\', $'.$prefix.'_'.$table['friendly_multiple'].');';
	$index_file .= "\n";
}

$index_file .='

require($tabstrip->get_active_tab_id());

$form->add_html_element($tabstrip);

require($GO_THEME->theme_path.\'header.inc\');
echo $form->get_html();
require($GO_THEME->theme_path.\'footer.inc\');
?>';


file_put_contents($module_dir.'index.php', $index_file);

touch($module_dir.'sql/'.$module.'.install.sql');
touch($module_dir.'sql/'.$module.'.uninstall.sql');
touch($module_dir.'sql/'.$module.'.updates.inc');


foreach($tables as $table)
{
	generate_code($prefix,$module,$module,$table['name'],$table['friendly_single'],$table['friendly_multiple'],$table['select_fields']);
}

echo "Module generated\n";
