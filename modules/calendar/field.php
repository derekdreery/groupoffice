<?php
/*
   Copyright Intermesh 2003
   Author: Merijn Schering <mschering@intermesh.nl>
   Version: 1.0 Release date: 08 July 2003

   This program is free software; you can redistribute it and/or modify it
   under the terms of the GNU General Public License as published by the
   Free Software Foundation; either version 2 of the License, or (at your
   option) any later version.
 */
require_once ("../../Group-Office.php");
$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('calendar');
require_once ($GO_LANGUAGE->get_language_file('calendar'));

load_basic_controls();

require_once($GO_MODULES->modules['calendar']['class_path'].'calendar.class.inc');
$cal = new calendar();

$task = isset ($_REQUEST['task']) ? $_REQUEST['task'] : '';
$field_id = isset($_REQUEST['field_id']) ? $_REQUEST['field_id'] : 0;
$group_id = isset($_REQUEST['group_id']) ? $_REQUEST['group_id'] : 0;
$link_back = (isset($_REQUEST['link_back']) && $_REQUEST['link_back'] != '') ? $_REQUEST['link_back'] : $_SERVER['REQUEST_URI'];
$return_to = (isset($_REQUEST['return_to']) && $_REQUEST['return_to'] != '') ? $_REQUEST['return_to'] : $_SERVER['HTTP_REFERER'];

if ($task == 'save_field')
{
	if(empty($_POST['name']))
	{
		$feedback = $errror_missing_field;
	}else
	{
		$field['id'] = $field_id;
		$field['name'] = smart_addslashes(htmlspecialchars($_POST['name']));
		$field['type'] = smart_addslashes($_POST['type']);
		if($field_id > 0)
		{
			$cal->update_custom_field($group_id, $field);
		}else
		{
			$field_id = $cal->add_custom_field($group_id, $field);
		}
		if($_POST['close'] == 'true')
		{
			header('Location: '.$return_to);
			exit();
		}
	}
}


if ($field_id > 0) {
	$field = $cal->get_custom_field($group_id, $field_id);
	$title = $field['name'];
} else {	
	$title = $cal_add_field;
	$field['name'] = '';
	$field['type'] = 'text';
}


$GO_HEADER['body_arguments'] = 'onload="document.field_form.name.focus();" onkeypress="javascript:executeOnEnter(event, \'save_field(true)\')"';
require_once ($GO_THEME->theme_path."header.inc");

$tabstrip = new tabstrip('field_tabstrip', $title);
$tabstrip->set_attribute('style','width:100%');
$tabstrip->set_return_to('index.php');


$form = new form('field_form');
$form->add_html_element(new input('hidden','field_id', $field_id, false));
$form->add_html_element(new input('hidden','group_id', $group_id, false));
$form->add_html_element(new input('hidden','task'));
$form->add_html_element(new input('hidden','close'));
$form->add_html_element(new input('hidden', 'return_to', $return_to));
$form->add_html_element(new input('hidden', 'link_back', $link_back));

if (isset($feedback))
{
  $p = new html_element('p', $feedback);
  $p->set_attribute('class','Error');
  $tabstrip->add_html_element($p);
}


$table = new table();
$row = new table_row();
$row->add_cell(new table_cell($strName.':*'));

$input = new input('text','name', $field['name']);
$input->set_attribute('maxlength','50');
$input->set_attribute('size','30');

$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$cell = new table_cell($strType.':');
$row->add_cell($cell);
$select = new select('type', $field['type']);
$select->add_value('text', $cal_field_types['text']);
$select->add_value('textarea',$cal_field_types['textarea']);
$select->add_value('checkbox', $cal_field_types['checkbox']);
$select->add_value('date', $cal_field_types['date']);

$cell = new table_cell($select->get_html());
$row->add_cell($cell);
$table->add_row($row);

$tabstrip->add_html_element($table);

$tabstrip->add_html_element(new button($cmdOk, 'javascript:save_field(true)'));
$tabstrip->add_html_element(new button($cmdApply, 'javascript:save_field(false)'));
$tabstrip->add_html_element(new button($cmdClose, "javascript:document.location='".htmlspecialchars($return_to)."';"));


$form->add_html_element($tabstrip);
echo $form->get_html();
?>
<script type="text/javascript">

function save_field(close)
{
	document.field_form.close.value=close;
  document.field_form.task.value='save_field';
  document.field_form.submit();
}

</script>
<?php
require_once ($GO_THEME->theme_path."footer.inc");
