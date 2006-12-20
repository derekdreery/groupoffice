<?php
require('../../Group-Office.php');

$file = '/var/www/trunk/modules/csvimport/postcodes.csv';
$db_table = 'jw_postcodes';
$delimiter = ',';
$encapsulated_by = '"';
$allow_duplicates=false;

$task = isset($_POST['task']) ? $_POST['task'] : '';

$fp = fopen($file,'r');

$db = new db();

$db->query("SHOW FIELDS FROM `$db_table`;");
while($db->next_record())
{
	$fields[] = $db->f('Field');
}

$form = new form('csvimport_form');
$form->add_html_element(new input('hidden','task','import'));

$table = new table();
if($record = fgetcsv($fp, 2024, $delimiter,$encapsulated_by))
{
	foreach($fields as $field)
	{
		$row = new table_row();
		$row->add_cell(new table_cell($field));
		
		$select = new select('fields['.$field.']');
		$select->add_value('','Don\'t import');
		$select->add_value('autonumber','Autonumber');
		foreach($record as $key=>$value)
		{		
			$select->add_value($key, $value);
		}
		$row->add_cell(new table_cell($select->get_html()));		
		$table->add_row($row);
	}
}
$form->add_html_element($table);
$form->add_html_element(new button($cmdOk, 'javascript:document.csvimport_form.submit();'));

if($task == 'import')
{
	$autonumber=array();
	foreach($_POST['fields'] as $key=>$index)
	{
		if($index == 'autonumber')
		{
			$autonumber[$key]=0;
			$db->query("SELECT max(`$key`) as autonumber FROM `$db_table`;");
			if($db->next_record())
			{
				$autonumber[$key]=$db->f('autonumber');
			}
		}
	}
	while($record = fgetcsv($fp, 2024, $delimiter,$encapsulated_by))
	{
		foreach($_POST['fields'] as $key=>$index)
		{
			if($index!='')
			{
				if($index == 'autonumber')
				{
					$autonumber[$key]++;
					$db_record[$key]=$autonumber[$key];					
				}else
				{
					$db_record[$key]=addslashes($record[$index]);
				}
			}
		}
		if(!$allow_duplicates)
		{	
			$conditions=array();		
			foreach($db_record as $field=>$value)
			{
				if($_POST['fields'][$field] != 'autonumber')
				{
					$conditions[] = "`$field`='$value'";	
				}
			}
			$sql = "SELECT * FROM `$db_table` WHERE ".implode(' AND ', $conditions);
			$db->query($sql);
			if(!$db->next_record())
			{
				$db->insert_row($db_table, $db_record);
			}else
			{
				foreach($autonumber as $key=>$value)
				{
					$autonumber[$key] = $value-1;
				}
			}			
		}else
		{
			$db->insert_row($db_table, $db_record);
		}				
	}
	
	$feedback = 'Data imported';
}

fclose($fp);


require($GO_THEME->theme_path.'header.inc');
if(isset($feedback))
{
	$form->add_html_element(new html_element('p','Data imported'));
}
echo $form->get_html();
require($GO_THEME->theme_path.'footer.inc');
