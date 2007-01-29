<?php
require('../../Group-Office.php');
/*
//name of the module. No spaces or strange characters.
$module = 'shipping';

//Short name of the module. The prefix of the database tables.
$prefix = 'sh';

//Tables to create an interface for
$tables[] = array('name'=>'sh_destinations', 'friendly_single'=>'destination', 'friendly_multiple'=>'destinations', 'select_fields'=>array());

//Add some fields that have a pulldown menu.
$select_fields['destination_id']=array('class'=>$module, 'function'=>'get_destinations();','value'=>'id','text'=>'name');
$tables[] = array('name'=>'sh_jobs', 'friendly_single'=>'job', 'friendly_multiple'=>'jobs', 'select_fields'=>$select_fields);
*/

fwrite(STDOUT, "\nWhat's the name of the module?\n");
$module = trim(fgets(STDIN)); 

fwrite(STDOUT, "\nWhat's the short name of the module (used for table prefixing)?\n");
$prefix = trim(fgets(STDIN)); 

$tables = array();
$table_name='test';
while($table_name!='')
{
	fwrite(STDOUT, "\n\nEnter table name (or leave empty to finish):\n");
	$table_name = trim(trim(fgets(STDIN))); 
	
	if(!empty($table_name))
	{
		fwrite(STDOUT, "\nEnter the code variable name referring to an item in the table (No spaces and no strange characters please eg. 'item'):\n");
		$friendly_single = trim(fgets(STDIN)); 
	
		fwrite(STDOUT, "\nEnter the code variable name referring to multiple items in the table  (No spaces and no strange characters please eg. 'items'):\n");
		$friendly_multiple = trim(fgets(STDIN)); 
	
		$select_fields=array();
		$select_field='y';
		while($select_field=='y')
		{
			fwrite(STDOUT, "\nIs there a dropdown field in the table that should get values from another table? (y/N):\n");
			$select_field = trim(fgets(STDIN));
			
			if($select_field=='y')
			{
				fwrite(STDOUT, "\nEnter field name:\n");
				$field_name = trim(fgets(STDIN));
				
				fwrite(STDOUT, "\nEnter the class name that has the function to get the dropbox values:\n");
				$class_name = trim(fgets(STDIN));
				
				fwrite(STDOUT, "\nEnter the function name with variables eg. get_items('some_value');:\n");
				$function_name = trim(fgets(STDIN));
				
				fwrite(STDOUT, "\nEnter the name of the record field that holds the dropbox value:\n");
				$value_field = trim(fgets(STDIN));
				
				fwrite(STDOUT, "\nEnter the name of the record field that holds the dropbox text:\n");
				$text_field = trim(fgets(STDIN));

				$select_fields[$field_name]=array('class'=>$class_name, 'function'=>$function_name,'value'=>$value_field,'text'=>$text_field);			
			}
		}
		
		$tables[] = array('name'=>$table_name, 'friendly_single'=>$friendly_single, 'friendly_multiple'=>$friendly_multiple, 'select_fields'=>$select_fields);
	}
}

require('generatecode.php');

$module_dir=$GO_CONFIG->root_path.'modules/'.$module.'/';
//exec('rm -Rf '.$module_dir);

if(file_exists($module_dir))
{
	fwrite(STDOUT, "\nModule directory already exists, skipping module creation\n");
}else {
	mkdir($module_dir);
	mkdir($module_dir.'classes/');
	mkdir($module_dir.'sql/');
	mkdir($module_dir.'language/');
	mkdir($module_dir.'themes/');
	mkdir($module_dir.'themes/Default');
	mkdir($module_dir.'themes/Default/images');
	

	
	copy($GO_CONFIG->root_path.'themes/Default/images/buttons/unknown.png', $module_dir.'themes/Default/images/'.$module.'.png');
	
	$image_file = 
	'<?php
$images[\''.$module.'\']=\''.$module.'.png\';
';
	
	file_put_contents($module_dir.'themes/Default/images.inc', $image_file);
	
	touch($module_dir.'themes/Default/style.css');
	
	$module_info_file=
	'<?php
	$module[\''.$module.'\'][\'description\'] = \'\';
	$module[\''.$module.'\'][\'version\'] = \'0.1\';
	$module[\''.$module.'\'][\'release_date\'] = \''.date('Y-m-d').'\';
	$module[\''.$module.'\'][\'status\'] = \'Alpha\';
	$module[\''.$module.'\'][\'authors\'][] = array(\'name\'=>\'Unknown\', \'email\'=>\'unkwown@unknown\');
	$module[\''.$module.'\'][\'sort_order\'] = \'1000\';
	$module[\''.$module.'\'][\'linkable_items\'][] = array();
	';
	
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
	';
	
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
	
	//create menu. The buttons are added in the include files.
	$menu = new button_menu();
	
	require($tabstrip->get_active_tab_id());
	
	$form->add_html_element($menu);
	$form->add_html_element($tabstrip);
	
	require($GO_THEME->theme_path.\'header.inc\');
	echo $form->get_html();
	require($GO_THEME->theme_path.\'footer.inc\');
	';
	
	
	file_put_contents($module_dir.'index.php', $index_file);
	
	touch($module_dir.'sql/'.$module.'.install.sql');
	touch($module_dir.'sql/'.$module.'.uninstall.sql');
	touch($module_dir.'sql/'.$module.'.updates.inc');

	echo "Module structure generated\n";
}

echo "Processing tables\n";

foreach($tables as $table)
{
	generate_code($prefix,$module,$module,$table['name'],$table['friendly_single'],$table['friendly_multiple'],$table['select_fields']);
}

echo "Finished\n";


