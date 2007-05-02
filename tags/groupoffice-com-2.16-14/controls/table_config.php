<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.3 $ $Date: 2006/11/21 16:25:35 $
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */

require_once("../Group-Office.php");
$GO_SECURITY->authenticate();
$GO_HEADER['nomessages'] = true;

load_basic_controls();

$table_id = smart_stripslashes($_REQUEST['table_id']);
$available_columns = $_REQUEST['available_columns'];

$task = isset($_POST['task']) ? $_POST['task'] : '';



if($task == 'save')
{
	$fields = isset($_POST['fields']) ? $_POST['fields'] : array();
	$enabled_columns = implode(',', $fields);
	
	$GO_CONFIG->save_setting('enabled_columns_'.$table_id, $enabled_columns);
	
	echo '<script type="text/javascript">window.close();</script>';
	exit();
}



$enabled_columns = $GO_CONFIG->get_setting('enabled_columns_'.$table_id);
		
if(!$enabled_columns)
{
	$enabled_columns=array();
}else {
	$enabled_columns = explode(',', $enabled_columns);
}


$form = new form('table_config_form');
$form->add_html_element(new input('hidden', 'task', '', false));
$form->add_html_element(new input('hidden', 'table_id', $table_id));
$form->add_html_element(new input('hidden', 'available_columns', $available_columns));

$tabstrip = new tabstrip('table_config', $strTableConfig);
$tabstrip->set_attribute('style','width:100%');

$table = new table();

$row = new table_row();

$fields = explode(';', $available_columns);

foreach($fields as $field)
{
	$field = explode(':', $field);
	
	$key=$field[0];
	$name=base64_decode($field[1]);
	
	$checkbox = new checkbox($key, 'fields[]',$key, $name, in_array($key, $enabled_columns));
	$row->add_cell(new table_cell($checkbox->get_html()));

	if(count($row->cells)==2)
	{
		$table->add_row($row);
		$row = new table_row();	
	}	
}
if(count($row->cells)==1)
{
	$table->add_row($row);
}

$tabstrip->add_html_element($table);

$tabstrip->add_html_element(new button($cmdOk, 'javascript:save(\'save\');'));
$tabstrip->add_html_element(new button($cmdCancel, 'javascript:window.close();'));

$form->add_html_element($tabstrip);
require_once($GO_THEME->theme_path."header.inc");
echo $form->get_html();
?>
<script type="text/javascript">
function save(task)
{
	document.table_config_form.task.value=task;
	document.table_config_form.submit();
}
</script>
<?php
require_once($GO_THEME->theme_path."footer.inc");
