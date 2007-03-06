<?php
require_once('../../Group-Office.php');

$controls=array();

function var_to_lang($varname)
{
	$lang = str_replace('_', ' ', $varname);
	return strtoupper(substr($lang,0,1)).substr($lang,1);
}

function parse_field_type($type)
{
	$pos = strpos($type,'(');

	if($pos)
	{
		$arr['type'] = substr($type,0,$pos);
		$arr['value'] = substr($type,$pos+1,-1);
	}else {
		$arr['type']=$field['Type'];
		$arr['value']='';
	}
	return $arr;
}

function dbfield_to_control($prefix, $friendly_single, $field, $select_fields=array())
{
	$pos = strpos($field['Type'],'(');

	if($pos)
	{
		$type = substr($field['Type'],0,$pos);
		$value = substr($field['Type'],$pos+1,-1);
	}else {
		$type=$field['Type'];
		$value='';
	}

	//echo $type.' '.$value;

	if(isset($select_fields[$field['Field']]))
	{
		$return =   '$row = new table_row();'."\n".
		'$row->add_cell(new table_cell($'.$prefix.'_'.$field['Field'].'.\':\'));'."\n".
		'$select = new select(\''.$field['Field'].'\', $'.$friendly_single.'[\''.$field['Field'].'\']);'."\n".
		'$'.$select_fields[$field['Field']]['class']."->".$select_fields[$field['Field']]['function'].";\n".
		'while($'.$select_fields[$field['Field']]['class'].'->next_record()){'."\n".
		"\t".'$select->add_value($'.$select_fields[$field['Field']]['class'].'->f(\''.$select_fields[$field['Field']]['value'].'\'), $'.$select_fields[$field['Field']]['class'].'->f(\''.$select_fields[$field['Field']]['text'].'\'));'."\n".
		"}\n".
		'$row->add_cell(new table_cell($select->get_html()));'."\n".
		'$table->add_row($row);'."\n\n";

		return $return;


	}elseif($field['Field']=='user_id')
	{
		return 'load_control(\'user_autocomplete\');'."\n".
		'$user_autocomplete=new user_autocomplete(\'user_id\',$'.$friendly_single.'[\'user_id\'],\'0\',$link_back);'."\n".
		'$row = new table_row();'."\n".
		'$row->add_cell(new table_cell($strOwner.\':\'));'."\n".
		'$row->add_cell(new table_cell($user_autocomplete->get_html()));'."\n".
		'$table->add_row($row);'."\n\n";
	}elseif($field['Field']=='ctime' || $field['Field']=='mtime')
	{
		return '';
	}else
	{
		switch($type)
		{


			case 'enum':

				if($value=="'0','1'")
				{
					return '$row = new table_row();'."\n".
					'$checkbox = new checkbox(\''.$field['Field'].'\',\''.$field['Field'].'\',$'.$friendly_single.'[\''.$field['Field'].'\'],$'.$prefix.'_'.$field['Field'].',($'.$friendly_single.'[\''.$field['Field'].'\']==\'1\'));'."\n".
					'$cell = new table_cell($checkbox->get_html());'."\n".
					'$cell->set_attribute(\'colspan\',\'2\');'."\n".
					'$row->add_cell($cell);'."\n".
					'$table->add_row($row);'."\n\n";
				}else {
					//todo create select
					$return =   '$row = new table_row();'."\n".
					'$row->add_cell(new table_cell($'.$prefix.'_'.$field['Field'].'.\':\'));'."\n".
					'$input = new input(\'text\',\''.$field['Field'].'\', $'.$friendly_single.'[\''.$field['Field'].'\']);'."\n";
					if($value>0)
					{
						$return .=	'$input->set_attribute(\'maxlength\',\''.$value.'\');'."\n";
					}

					$return .= '$row->add_cell(new table_cell($input->get_html()));'."\n".
					'$table->add_row($row);'."\n\n";

					return $return;

				}

				break;
			case 'text':
				$return =   '$row = new table_row();'."\n".
				'$cell = new table_cell($'.$prefix.'_'.$field['Field'].'.\':\');'."\n".
				'$cell->set_attribute(\'style\', \'vertical-align:top;\');'."\n".
				'$row->add_cell($cell);'."\n".
				'$textarea = new textarea(\''.$field['Field'].'\', $'.$friendly_single.'[\''.$field['Field'].'\']);'."\n".
				'$textarea->set_attribute(\'style\', \'width:300px;height:60px;\');'."\n".
				'$row->add_cell(new table_cell($textarea->get_html()));'."\n".
				'$table->add_row($row);'."\n\n";

				return $return;
				break;

			default:
				$return =   '$row = new table_row();'."\n".
				'$row->add_cell(new table_cell($'.$prefix.'_'.$field['Field'].'.\':\'));'."\n".
				'$input = new input(\'text\',\''.$field['Field'].'\', $'.$friendly_single.'[\''.$field['Field'].'\']);'."\n";
				if($value>0)
				{
					$return .=	'$input->set_attribute(\'maxlength\',\''.$value.'\');'."\n";
				}
				$return .= '$input->set_attribute(\'style\', \'width:300px;\');'."\n".
				'$row->add_cell(new table_cell($input->get_html()));'."\n".
				'$table->add_row($row);'."\n\n";

				return $return;
				break;
		}
	}

}

function dbfield_to_handler($friendly_single, $field)
{
	//echo $field['Type'];

	if($field['Field']=='ctime' || $field['Field']=='mtime')
	{
		return '';
	}




	if($field['Type']=='enum(\'0\',\'1\')')
	{
		return '$'.$friendly_single.'[\''.$field['Field'].'\']=isset($_POST[\''.$field['Field'].'\']) ? \'1\' : \'0\';'."\n";
	}else
	{
		return '$'.$friendly_single.'[\''.$field['Field'].'\']=smart_addslashes(trim($_POST[\''.$field['Field'].'\']));'."\n";
	}
}

function append_data($file, $data)
{
	$existing_data = file_get_contents($file);
	$new_data = str_replace('?>', $data."\n?>", $existing_data);
	file_put_contents($file,$new_data);
}

function insert_class_functions($file, $data)
{
	$existing_data = file_get_contents($file);
	$acc_pos= my_strrpos($existing_data,'}');

	$new_data = substr($existing_data,0,$acc_pos).$data.substr($existing_data,$acc_pos);
	file_put_contents($file,$new_data);
}

function my_strrpos($haystack, $needle)
{
	$haystack_length=strlen($haystack);

	$index = strpos(strrev($haystack), strrev($needle));
	if($index === false) {
		return false;
	}
	$index = $haystack_length - strlen($needle) - $index;
	return $index;
}

function get_var_name($prefix, $field)
{
	switch($field)
	{
		case'user_id':
		return '$strOwner';
		break;

		case 'ctime':
			return '$strCreatedAt';
			break;

		case 'mtime':
			return '$strModifiedAt';
			break;

		default:
			return '$'.$prefix.'_'.$field;
			break;

	}
}

function generate_code($prefix, $module_id, $class_name, $table, $friendly_single, $friendly_multiple, $select_fields)
{
	global $GO_CONFIG;

	$module_dir = $GO_CONFIG->root_path.'modules/'.$module_id.'/';

	$fields=array();

	$ctime_field=false;
	$mtime_field=false;

	$db = new db();
	$db->query('SHOW FIELDS FROM '.$table);
	while($db->next_record())
	{
		$fields[] = $db->Record;

		if($db->f('Field')=='ctime')
		{
			$ctime_field=true;
		}
		if($db->f('Field')=='mtime')
		{
			$mtime_field=true;
		}
	}


	$lang_vars= '$'.$prefix.'_'.$friendly_single.'=\''.var_to_lang($friendly_single).'\';'.
	"\n".
	'$'.$prefix.'_'.$friendly_multiple.'=\''.var_to_lang($friendly_multiple).'\';'.
	"\n";

	foreach($fields as $field)
	{
		if($field['Field']!='id' && $field['Field']!='user_id' && $field['Field']!='ctime' && $field['Field']!='mtime')
		{
			$lang_vars .= get_var_name($prefix,$field['Field'])."='".var_to_lang($field['Field'])."';\n";
		}
	}

	append_data($module_dir.'language/en.inc',$lang_vars);



	$class_functions= '

	/**
	* Add a '.$friendly_single.'
	*
	* @param Array $'.$friendly_single.' Associative array of record fields
	*
	* @access public
	* @return int New record ID created
	*/
	   
	function add_'.$friendly_single.'($'.$friendly_single.')
	{
		$'.$friendly_single.'[\'id\']=$this->nextid(\''.$table.'\');
		';
	if($ctime_field && $mtime_field)
	{
		$class_functions.= '
		$'.$friendly_single.'[\'ctime\']=$'.$friendly_single.'[\'mtime\']=get_gmt_time();
		';
	}else {
		if($ctime_field)
		{
			$class_functions.= '
		$'.$friendly_single.'[\'ctime\']=get_gmt_time();
		';
		}

		if($mtime_field)
		{
			$class_functions.= '
		$'.$friendly_single.'[\'mtime\']=get_gmt_time();
		';
		}
	}

	$class_functions.= '
	
		if($this->insert_row(\''.$table.'\', $'.$friendly_single.'))
		{
			return $'.$friendly_single.'[\'id\'];
		}
		return false;
	}
	
	/**
	* Update a '.$friendly_single.'
	*
	* @param Array $'.$friendly_single.' Associative array of record fields
	*
	* @access public
	* @return bool True on success
	*/
	
	function update_'.$friendly_single.'($'.$friendly_single.')
	{
		';
	if($mtime_field)
	{
		$class_functions.= '
		$'.$friendly_single.'[\'mtime\']=get_gmt_time();
			';
	}

	$class_functions.= '
		return $this->update_row(\''.$table.'\', \'id\', $'.$friendly_single.');
	}
	
	
	/**
	* Delete a '.$friendly_single.'
	*
	* @param Int $'.$friendly_single.'_id ID of the '.$friendly_single.'
	*
	* @access public
	* @return bool True on success
	*/
	
	function delete_'.$friendly_single.'($'.$friendly_single.'_id)
	{
		return $this->query("DELETE FROM '.$table.' WHERE id=$'.$friendly_single.'_id");
	}
	
	
	/**
	* Gets a '.$friendly_single.' record
	*
	* @param Int $'.$friendly_single.'_id ID of the '.$friendly_single.'
	*
	* @access public
	* @return Array Record properties
	*/
	
	function get_'.$friendly_single.'($'.$friendly_single.'_id)
	{
		$this->query("SELECT * FROM '.$table.' WHERE id=$'.$friendly_single.'_id");
		if($this->next_record())
		{
			return $this->Record;
		}
		return false;
	}
	
	/**
	* Gets a '.$friendly_single.' record by the name field
	*
	* @param String $name Name of the '.$friendly_single.'
	*
	* @access public
	* @return Array Record properties
	*/
	
	function get_'.$friendly_single.'_by_name($name)
	{
		$this->query("SELECT * FROM '.$table.' WHERE '.$friendly_single.'_name=\'$name\'");
		if($this->next_record())
		{
			return $this->Record;
		}
		return false;
	}
	
	
	/**
	* Gets all '.$friendly_multiple.'
	*
	* @param Int $start First record of the total record set to return
	* @param Int $offset Number of records to return
	* @param String $sortfield The field to sort on
	* @param String $sortorder The sort order
	*
	* @access public
	* @return Int Number of records found
	*/
	function get_'.$friendly_multiple.'($start=0, $offset=0, $sortfield=\'id\', $sortorder=\'ASC\')
	{
		$sql = "SELECT * FROM '.$table.' ORDER BY $sortfield $sortorder";
		
		$this->query($sql);
		$count = $this->num_rows();
		
		if($offset>0)
		{
			$sql .= " LIMIT $start,$offset";
			$this->query($sql);
		}
		return $count;		
	}
';

	insert_class_functions($module_dir.'classes/'.$module_id.'.class.inc',$class_functions);


	$index_page = '<?php
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
$GO_MODULES->authenticate(\''.$module_id.'\');

//Get the language variables
require_once($GO_LANGUAGE->get_language_file(\''.$module_id.'\'));

//Require the module class
require_once($GO_MODULES->class_path.\''.$class_name.'.class.inc\');
$'.$class_name.' = new '.$class_name.'();

//Declare variables
$'.$friendly_single.'_id = isset($_REQUEST[\''.$friendly_single.'_id\']) ? smart_addslashes($_REQUEST[\''.$friendly_single.'_id\']) : 0;
$task = isset($_REQUEST[\'task\']) ? $_REQUEST[\'task\'] : \'\';
$link_back=$_SERVER[\'PHP_SELF\'];


$form = new form(\''.$friendly_multiple.'_form\');
$form->add_html_element(new input(\'hidden\', \'task\', \'\', false));


$datatable = new datatable(\''.$table.'_table\');
$GO_HEADER[\'head\']=$datatable->get_header();
		
if($datatable->task==\'delete\')
{
	foreach($datatable->selected as $'.$friendly_single.'_id)
	{
		$'.$class_name.'->delete_'.$friendly_single.'($'.$friendly_single.'_id);
	}
}


$menu = new button_menu();
$menu->add_button(\'add\',$cmdAdd,\''.$friendly_single.'.php?return_to=\'.urlencode($link_back));
$menu->add_button(\'delete_big\',$cmdDelete, $datatable->get_delete_handler());
$form->add_html_element($menu);


';


	$index_page .= "\n";

	foreach($fields as $field)
	{
		if($field['Field']!='id')
		{
			$index_page .= '$th = new table_heading('.get_var_name($prefix,$field['Field']).', \''.$field['Field'].'\');'.
			"\n".
			'$datatable->add_column($th);'.
			"\n";
		}
	}

	$index_page .= '$count = $'.$class_name.'->get_'.$friendly_multiple.'($datatable->start, $datatable->offset, $datatable->sort_index, $datatable->sql_sort_order);';
	$index_page .= "\n";

	$index_page .= 'while($'.$class_name.'->next_record()){'."\n".
	'$row = new table_row($'.$class_name.'->f(\'id\'));
		$row->set_attribute(\'ondblclick\',"javascript:document.location=\''.$friendly_single.'.php?'.$friendly_single.'_id=".$'.$class_name.'->f(\'id\')."&return_to=".urlencode($link_back)."\';");
		
		';

	foreach($fields as $field)
	{
		if($field['Field']!='id')
		{

			if($field['Field']=='user_id')
			{
				$index_page .= '$cell = new table_cell(show_profile($'.$class_name.'->f(\''.$field['Field'].'\')));';
			}else {
				$type = parse_field_type($field['Type']);
				if($type['type']=='enum' && $type['value']=="'0','1'")
				{
					$index_page .= '$value=$'.$class_name.'->f(\''.$field['Field'].'\')==\'1\' ? $cmdYes : $cmdNo;'."\n";
					$index_page .= '$cell = new table_cell($value);';
				}else {
					$index_page .= '$cell = new table_cell($'.$class_name.'->f(\''.$field['Field'].'\'));';
				}
			}

			$index_page .= "\n";
			$index_page .= '$row->add_cell($cell);';
			$index_page .= "\n";
		}
	}
	$index_page .= '$datatable->add_row($row);';
	$index_page .= "\n";
	$index_page .= "}\n";

	$index_page .= '$form->add_html_element($datatable);
require_once($GO_THEME->theme_path.\'header.inc\');
echo $form->get_html();
require_once($GO_THEME->theme_path.\'footer.inc\');
';

	$filename = $module_dir.$friendly_multiple.'.php';
	$x=0;
	while(file_exists($filename))
	{
		$x++;
		$filename = $module_dir.$friendly_multiple.'_'.$x.'.php';
	}

	//file_put_contents($filename, $index_page);




	$include_file = '<?php

load_control(\'datatable\');
$datatable = new datatable(\''.$table.'_table\');
$datatable->allow_configuration=true;
$GO_HEADER[\'head\']=$datatable->get_header();
		
if($datatable->task==\'delete\')
{
	foreach($datatable->selected as $'.$friendly_single.'_id)
	{
		$'.$class_name.'->delete_'.$friendly_single.'($'.$friendly_single.'_id);
	}
}



$menu->add_button(\'add\',$cmdAdd,\''.$friendly_single.'.php?return_to=\'.urlencode($link_back));
$menu->add_button(\'delete_big\',$cmdDelete, $datatable->get_delete_handler());


';


	$include_file .= "\n";

	foreach($fields as $field)
	{
		if($field['Field']!='id')
		{
			$include_file .= '$th = new table_heading('.get_var_name($prefix,$field['Field']).', \''.$field['Field'].'\');'.
			"\n".
			'$datatable->add_column($th);'.
			"\n";
		}
	}

	$include_file .= "\n";
	$include_file .= '$count = $'.$class_name.'->get_'.$friendly_multiple.'($datatable->start, $datatable->offset, $datatable->sort_index, $datatable->sql_sort_order);';
	$include_file .= "\n\n";
	
	$invlude_file .= "$datatable->set_pagination($count);\n\n";

	$include_file .= 'if($count){'."\n";

	$include_file .= "\t".'while($'.$class_name.'->next_record()){'."\n".
	"\t\t".'$row = new table_row($'.$class_name.'->f(\'id\'));'."\n".
	"\t\t".'$row->set_attribute(\'ondblclick\',"javascript:document.location=\''.$friendly_single.'.php?'.$friendly_single.'_id=".$'.$class_name.'->f(\'id\')."&return_to=".urlencode($link_back)."\';");'."\n";



	foreach($fields as $field)
	{
		if($field['Field']!='id')
		{
			if($field['Field']=='user_id')
			{
				$include_file .= "\t\t".'$cell = new table_cell(show_profile($'.$class_name.'->f(\''.$field['Field'].'\')));';
			}elseif($field['Field']=='ctime' || $field['Field']=='mtime')
			{
				$include_file .= "\t\t".'$cell = new table_cell(get_timestamp($'.$class_name.'->f(\''.$field['Field'].'\')));';
			}else
			{
				$type = parse_field_type($field['Type']);
				if($type['type']=='enum' && $type['value']=="'0','1'")
				{
					$include_file .= "\t\t".'$value=$'.$class_name.'->f(\''.$field['Field'].'\')==\'1\' ? $cmdYes : $cmdNo;'."\n";
					$include_file .= "\t\t".'$cell = new table_cell($value);';
				}else {
					$include_file .= "\t\t".'$cell = new table_cell($'.$class_name.'->f(\''.$field['Field'].'\'));';
				}
			}

			$include_file .= "\n";
			$include_file .= "\t\t".'$row->add_cell($cell);';
			$include_file .= "\n";
		}
	}
	$include_file .= "\t\t".'$datatable->add_row($row);';
	$include_file .= "\n";
	$include_file .= "\t}\n";

	$include_file .= '}else {'."\n".
	"\t".'$row = new table_row();'."\n".
	"\t".'$cell = new table_cell($strNoItems);'."\n".
	"\t".'$cell->set_attribute(\'colspan\',\'99\');'."\n".
	"\t".'$row->add_cell($cell);'."\n".
	"\t".'$datatable->add_row($row);'."\n".
	"}\n";



	$include_file .= '$tabstrip->add_html_element($datatable);';
	$include_file .= "\n?>";

	$filename = $module_dir.$friendly_multiple.'.inc';
	$x=0;
	while(file_exists($filename))
	{
		$x++;
		$filename = $module_dir.$friendly_multiple.'_'.$x.'.inc';
	}

	file_put_contents($filename, $include_file);




	$item_page = '<?php
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
$GO_MODULES->authenticate(\''.$module_id.'\');

//Get the language variables
require_once($GO_LANGUAGE->get_language_file(\''.$module_id.'\'));

//Require the module class
require_once($GO_MODULES->class_path.\''.$class_name.'.class.inc\');
$'.$class_name.' = new '.$class_name.'();

//Declare variables
$'.$friendly_single.'_id = isset($_REQUEST[\''.$friendly_single.'_id\']) ? smart_addslashes($_REQUEST[\''.$friendly_single.'_id\']) : 0;
$task = isset($_REQUEST[\'task\']) ? $_REQUEST[\'task\'] : \'\';
$return_to = isset ($_REQUEST[\'return_to\']) ? $_REQUEST[\'return_to\'] : $_SERVER[\'HTTP_REFERER\'];


//Handle save of a '.$friendly_single.'
if ($task==\'save\')
{
	';

	foreach($fields as $field)
	{
		if($field['Field']!='id')
		{
			$item_page .= dbfield_to_handler($friendly_single, $field);
		}
	}
	$item_page .= '
	if (empty($'.$friendly_single.'[\''.$fields[1]['Field'].'\']))
	{
		$feedback = $error_missing_field;
	}else
	{
		if ($'.$friendly_single.'_id>0)
		{
			$'.$friendly_single.'[\'id\'] = $'.$friendly_single.'_id;
			if (!$'.$class_name.'->update_'.$friendly_single.'($'.$friendly_single.'))
			{
				$feedback = $strSaveError;
			}
		}else
		{
	
			$'.$friendly_single.'_id=$'.$class_name.'->add_'.$friendly_single.'($'.$friendly_single.');
			if(!$'.$friendly_single.'_id)
			{
				$'.$friendly_single.'dback = $strSaveError;
			}
		}
	}
	if(!isset($feedback) && $_POST[\'close\'] == \'true\')
	{
		header(\'Location: \'.$return_to);
		exit();
	}
	
}


//This URL links back to this page
$link_back = $_SERVER[\'PHP_SELF\'].\'?'.$friendly_single.'_id=\'.$'.$friendly_single.'_id.\'&return_to=\'.urlencode($return_to);


$form = new form(\''.$friendly_single.'_form\');
$form->add_html_element(new input(\'hidden\', \'task\', \'\', false));
$form->add_html_element(new input(\'hidden\', \''.$friendly_single.'_id\', $'.$friendly_single.'_id, false));
$form->add_html_element(new input(\'hidden\',\'close\', \'false\', false));
$form->add_html_element(new input(\'hidden\', \'return_to\',$return_to));
$form->add_html_element(new input(\'hidden\', \'link_back\',$link_back));

if ($'.$friendly_single.'_id > 0)
{
	$'.$friendly_single.' = $'.$class_name.'->get_'.$friendly_single.'($'.$friendly_single.'_id);
}else
{
';

	foreach($fields as $field)
	{
		if($field['Field']!='id')
		{

				if($field['Field']=='user_id')
				{
					$item_page .= '			$'.$friendly_single.'[\''.$field['Field'].'\']=isset($_POST[\''.$field['Field'].'\']) ? smart_stripslashes(trim($_POST[\''.$field['Field'].'\']))  : $GO_SECURITY->user_id;';
				}else {
					$item_page .= '			$'.$friendly_single.'[\''.$field['Field'].'\']=isset($_POST[\''.$field['Field'].'\']) ? smart_stripslashes(trim($_POST[\''.$field['Field'].'\']))  : \'\';';
					$item_page .= "\n";
				}
			
		}
	}
	$item_page .= '
}

//Create tabstrip control 
$tabstrip = new tabstrip(\''.$friendly_single.'_tabstrip\', $'.$prefix.'_'.$friendly_single.');
$tabstrip->set_attribute(\'style\',\'width:100%\');
$tabstrip->set_return_to($return_to);

		
//If there\'s feedback display it
if (isset($feedback))
{
  $p = new html_element(\'p\', $feedback);
  $p->set_attribute(\'class\',\'Error\');
  $tabstrip->add_html_element($p);
}

//Display the active tab content
switch($tabstrip->get_active_tab_id())
{
	default:

		$table = new table();
		';

	foreach($fields as $field)
	{
		if($field['Field']!='id')
		{
			$item_page .= dbfield_to_control($prefix, $friendly_single, $field, $select_fields);
		}
	}
	$item_page .= '
		$tabstrip->add_html_element($table);
		$tabstrip->add_html_element(new button($cmdOk, "javascript:dotask(\'save\',\'true\');"));
		$tabstrip->add_html_element(new button($cmdApply, "javascript:dotask(\'save\',\'false\');"));
		$tabstrip->add_html_element(new button($cmdClose, "javascript:document.location=\'$return_to\';"));
	break;
}

//Output header form and footer
$GO_HEADER[\'body_arguments\'] = \'onload="document.'.$friendly_single.'_form.'.$fields[1]['Field'].'.focus();"\';
require_once($GO_THEME->theme_path.\'header.inc\');
$form->add_html_element($tabstrip);
echo $form->get_html();
?>
<script type="text/javascript">
function dotask(task, close)
{
	document.'.$friendly_single.'_form.task.value=task;
	document.'.$friendly_single.'_form.close.value=close;
	document.'.$friendly_single.'_form.submit();	
}
</script>
<?php
require_once($GO_THEME->theme_path.\'footer.inc\');
';


	$filename = $module_dir.$friendly_single.'.php';
	$x=0;
	while(file_exists($filename))
	{
		$x++;
		$filename = $module_dir.$friendly_single.'_'.$x.'.php';
	}

	file_put_contents($filename, $item_page);
}

//generate_code('sh', 'shipping','shipping','sh_jobs','job','jobs');
