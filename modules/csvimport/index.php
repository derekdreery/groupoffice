<?php
require('../../Group-Office.php');
load_basic_controls();
$file = '/home/mschering/postcodes.csv';
//$db_table = 'rr_wijken';
$delimiter = ',';
$encapsulated_by = '"';
$allow_duplicates=false;

$task = isset($_POST['task']) ? $_POST['task'] : '';
$db_table = isset($_POST['db_table']) ? $_POST['db_table'] : '';



$db = new db();



$form = new form('csvimport_form');
$form->add_html_element(new input('hidden','task','',''));

if(isset($_FILES['csvfile']))
{
	$tmpfile = $GO_CONFIG->tmpdir.uniqid(time()).'.csv';

	move_uploaded_file($_FILES['csvfile']['tmp_name'], $tmpfile);
	$_SESSION['csvfile']=$tmpfile;
	
	$task='match';

}


if(isset($_SESSION['csvfile']) && $task == 'match')
{
	$fp = fopen($_SESSION['csvfile'],'r');

	$form->add_html_element(new html_element('h3', 'Table:'));

	$select = new select('db_table', $db_table);
	$select->set_attribute('onchange','javascript:document.csvimport_form.task.value=\'match\';document.csvimport_form.submit();');
	$db->query("SHOW TABLES;");
	while($db->next_record())
	{
		if(empty($db_table))
		{
			$db_table=$db->f(0);
		}
		$select->add_value($db->f(0),$db->f(0));
	}
	$form->add_html_element($select);


	$db->query("SHOW FIELDS FROM `$db_table`;");
	while($db->next_record())
	{
		$fields[] = $db->f('Field');
	}

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

	fclose($fp);
	$form->add_html_element($table);
	$form->add_html_element(new button($cmdOk, 'javascript:document.csvimport_form.task.value=\'import\';document.csvimport_form.submit();'));
}elseif($task == 'import')
{
	$fp = fopen($_SESSION['csvfile'],'r');
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

	fclose($fp);
	unlink($_SESSION['csvfile']);
}else {

	$form->add_html_element(new html_element('h2','File:'));

	$form->set_attribute('enctype','multipart/form-data');

	$form->add_html_element(new input('file','csvfile',''));
	$form->add_html_element(new button($cmdOk, 'javascript:document.csvimport_form.submit();'));

}

require($GO_THEME->theme_path.'header.inc');
if(isset($feedback))
{
	$form->add_html_element(new html_element('p',$feedback));
}

echo $form->get_html();
require($GO_THEME->theme_path.'footer.inc');
