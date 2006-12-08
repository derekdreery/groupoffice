<?php
require('../../Group-Office.php');

$controls=array();

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

function dbfield_to_control($prefix, $friendly_single, $field)
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

	if($field['Field']=='user_id')
	{
		return 'load_control(\'user_autocomplete\');'."\n".
		'$user_autocomplete=new user_autocomplete(\'user_id\',$'.$friendly_single.'[\'user_id\'],\'0\',$link_back);'."\n".
		'$row = new table_row();'."\n".
		'$row->add_cell(new table_cell($strOwner.\':\'));'."\n".
		'$row->add_cell(new table_cell($user_autocomplete->get_html()));'."\n".
		'$table->add_row($row);'."\n\n";
	}else {

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
						$return .=	'$input->set_attribute(\'maxlenght\',\''.$value.'\');'."\n";
					}

					$return .= '$row->add_cell(new table_cell($input->get_html()));'."\n".
					'$table->add_row($row);'."\n\n";

					return $return;

				}

				break;

			default:
				$return =   '$row = new table_row();'."\n".
				'$row->add_cell(new table_cell($'.$prefix.'_'.$field['Field'].'.\':\'));'."\n".
				'$input = new input(\'text\',\''.$field['Field'].'\', $'.$friendly_single.'[\''.$field['Field'].'\']);'."\n";
				if($value>0)
				{
					$return .=	'$input->set_attribute(\'maxlenght\',\''.$value.'\');'."\n";
				}

				$return .= '$row->add_cell(new table_cell($input->get_html()));'."\n".
				'$table->add_row($row);'."\n\n";

				return $return;
				break;
		}
	}

}

function dbfield_to_handler($friendly_single, $field)
{
	//echo $field['Type'];
	if($field['Type']=='enum(\'0\',\'1\')')
	{
		return '$'.$friendly_single.'[\''.$field['Field'].'\']=isset($_POST[\''.$field['Field'].'\']) ? \'1\' : \'0\';'."\n";
	}else
	{
		return '$'.$friendly_single.'[\''.$field['Field'].'\']=smart_addslashes(trim($_POST[\''.$field['Field'].'\']));'."\n";
	}
}



function generate_code($prefix, $module_id, $class_name, $table, $friendly_single, $friendly_multiple)
{
	$fields=array();

	$db = new db();
	$db->query('SHOW FIELDS FROM '.$table);
	while($db->next_record())
	{
		$fields[] = $db->Record;
	}

	echo "//language strings\n";

	echo '$'.$prefix.'_'.$friendly_single.'=\''.$friendly_single.'\';';
	echo "\n";
	echo '$'.$prefix.'_'.$friendly_multiple.'=\''.$friendly_multiple.'\';';
	echo "\n";

	foreach($fields as $field)
	{
		if($field['Field']!='id')
		{
			echo '$'.$prefix.'_'.$field['Field']."='".$field['Field']."';\n";
		}
	}



	echo "\n\n//class functions\n";

	echo 'function add_'.$friendly_single.'($'.$friendly_single.')
{
	$'.$friendly_single.'[\'id\']=$this->nextid($table);
	if($this->insert_row(\''.$table.'\', $'.$friendly_single.'))
	{
		return $'.$friendly_single.'[\'id\'];
	}
	return false;
}
function update_'.$friendly_single.'($'.$friendly_single.')
{
	return $this->update_row(\''.$table.'\', \'id\', $'.$friendly_single.');
}

function delete_'.$friendly_single.'($'.$friendly_single.'_id)
{
	return $this->query("DELETE FROM '.$prefix.'_'.$friendly_multiple.' WHERE id=$'.$friendly_single.'_id");
}

function get_'.$friendly_single.'($'.$friendly_single.'_id)
{
	$this->query("SELECT * FROM '.$prefix.'_'.$friendly_multiple.' WHERE id=$'.$friendly_single.'_id");
	if($this->next_record())
	{
		return $this->Record;
	}
	return false;
}

function get_'.$friendly_single.'_by_name($name)
{
	$this->query("SELECT * FROM '.$prefix.'_'.$friendly_multiple.' WHERE '.$friendly_single.'_name=\'$name\'");
	if($this->next_record())
	{
		return $this->Record;
	}
	return false;
}

function get_'.$friendly_multiple.'($start, $offset, $sortfield, $sortorder)
{
	$sql = "SELECT * FROM '.$prefix.'_'.$friendly_multiple.' ORDER BY $sortfield $sortorder";
	
	$this->query($sql);
	$count = $this->num_rows();
	
	if($offset>0)
	{
		$sql .= " LIMIT $start,$offset";
		$this->query($sql);
	}
	return $count;		
}';

	echo "\n\n//index page\n";


	echo '<?php
/**
 * @copyright Copyright Intermesh 2006
 * @version $Revision: 1.4 $ $Date: 2006/12/06 16:01:33 $
 * 
 * @author 
   
 */

require_once(\'../../Group-Office.php\');

load_basic_controls();

$GO_SECURITY->authenticate();
$GO_MODULES->authenticate(\''.$module_id.'\');
require_once($GO_LANGUAGE->get_language_file(\''.$module_id.'\'));

require_once($GO_MODULES->class_path.\''.$class_name.'.class.inc\');
$'.$class_name.' = new '.$class_name.'();

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


	echo "\n";

	foreach($fields as $field)
	{
		if($field['Field']!='id')
		{
			echo '$th = new table_heading($'.$prefix.'_'.$field['Field'].', \''.$field['Field'].'\');';
			echo "\n";
			echo '$datatable->add_column($th);';
			echo "\n";
		}
	}

	echo '$count = $'.$class_name.'->get_'.$friendly_multiple.'($datatable->start, $datatable->offset, $datatable->sort_index, $datatable->sql_sort_order);';
	echo "\n";

	echo 'while($'.$class_name.'->next_record()){'."\n".
	'$row = new table_row($'.$class_name.'->f(\'id\'));
		$row->set_attribute(\'ondblclick\',"javascript:document.location=\''.$friendly_single.'.php?'.$friendly_single.'_id=".$'.$class_name.'->f(\'id\')."&return_to=".urlencode($link_back)."\';");
		
		';

	foreach($fields as $field)
	{
		if($field['Field']!='id')
		{
			
			if($field['Field']=='user_id')
			{
				echo '$cell = new table_cell(show_profile($'.$class_name.'->f(\''.$field['Field'].'\')));';
			}else {
				$type = parse_field_type($field['Type']);
				if($type['type']=='enum' && $type['value']=="'0','1'")
				{					
					echo '$value=$'.$class_name.'->f(\''.$field['Field'].'\')==\'1\' ? $cmdYes : $cmdNo;'."\n";
					echo '$cell = new table_cell($value);';
				}else {
					echo '$cell = new table_cell($'.$class_name.'->f(\''.$field['Field'].'\'));';
				}
			}
		
			echo "\n";
			echo '$row->add_cell($cell);';
			echo "\n";
		}
	}
	echo '$datatable->add_row($row);';
	echo "\n";
	echo "}\n";

	echo '$form->add_html_element($datatable);
echo $form->get_html();

require_once($GO_THEME->theme_path."footer.inc");
?>';



	echo "\n\n\n//item page";


	echo '<?php
/**
 * @copyright Copyright Intermesh 2006
 * @version $Revision: 1.4 $ $Date: 2006/12/06 16:01:33 $
 * 
 * @author 
   
 */

require_once(\'../../Group-Office.php\');

load_basic_controls();

$GO_SECURITY->authenticate();
$GO_MODULES->authenticate(\''.$module_id.'\');
require_once($GO_LANGUAGE->get_language_file(\''.$module_id.'\'));

require_once($GO_MODULES->class_path.\''.$class_name.'.class.inc\');
$'.$class_name.' = new '.$class_name.'();

$'.$friendly_single.'_id = isset($_REQUEST[\''.$friendly_single.'_id\']) ? smart_addslashes($_REQUEST[\''.$friendly_single.'_id\']) : 0;
$task = isset($_REQUEST[\'task\']) ? $_REQUEST[\'task\'] : \'\';
$return_to = isset ($_REQUEST[\'return_to\']) ? $_REQUEST[\'return_to\'] : $_SERVER[\'HTTP_REFERER\'];


if ($task==\'save\')
{
	';

	foreach($fields as $field)
	{
		if($field['Field']!='id')
		{
			echo dbfield_to_handler($friendly_single, $field);
		}
	}
	echo '
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
	
			if(!$'.$class_name.'->add_'.$friendly_single.'($'.$friendly_single.'))
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
$GO_HEADER[\'body_arguments\'] = \'onload="document.'.$friendly_single.'_form.'.$fields[1]['Field'].'.focus();"\';
require_once($GO_THEME->theme_path."header.inc");

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
			echo '			$'.$friendly_single.'[\''.$field['Field'].'\']=isset($_POST[\''.$field['Field'].'\']) ? smart_stripslashes(trim($_POST[\''.$field['Field'].'\']))  : \'\';';
			echo "\n";
		}
	}
	echo '
}


$tabstrip = new tabstrip(\''.$friendly_single.'_tabstrip\', $'.$prefix.'_'.$friendly_single.');
$tabstrip->set_attribute(\'style\',\'width:100%\');
$tabstrip->set_return_to("'.$friendly_single.'s.php");

		
if (isset($feedback))
{
  $p = new html_element(\'p\', $feedback);
  $p->set_attribute(\'class\',\'Error\');
  $tabstrip->add_html_element($p);
}

switch($tabstrip->get_active_tab_id())
{

	default:

		$table = new table();
		';

	foreach($fields as $field)
	{
		if($field['Field']!='id')
		{
			echo dbfield_to_control($prefix, $friendly_single, $field);
		}
	}
	echo '
		$tabstrip->add_html_element($table);
		$tabstrip->add_html_element(new button($cmdOk, "javascript:dotask(\'save\',\'true\');"));
		$tabstrip->add_html_element(new button($cmdApply, "javascript:dotask(\'save\',\'false\');"));
		$tabstrip->add_html_element(new button($cmdClose, "javascript:document.location=\'$return_to\';"));
	break;
}


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
require_once($GO_THEME->theme_path."footer.inc");
?>';

}

generate_code('ws', 'webshop','ws','ws_payments','payment','payments');
?>
