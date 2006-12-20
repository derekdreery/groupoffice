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

require_once("../../Group-Office.php");

load_basic_controls();

$GO_SECURITY->authenticate(true);
$GO_MODULES->authenticate('calendar');
require_once($GO_LANGUAGE->get_language_file('calendar'));

require_once($GO_MODULES->path.'classes/calendar.class.inc');
$cal = new calendar();

$task = isset($_POST['task']) ? $_POST['task'] : '';
$return_to = isset($_REQUEST['return_to']) ? $_REQUEST['return_to'] : $_SERVER['HTTP_REFERER'];
$link_back = isset($_REQUEST['link_back']) ? $_REQUEST['link_back'] : $_SERVER['REQUEST_URI'];

$read_groups = isset($_POST['read_groups']) ? $_POST['read_groups'] : array();
$write_groups = isset($_POST['write_groups']) ? $_POST['write_groups'] : array();

if ($task == 'batchcalendars')
{
	$GO_USERS->get_users();
	while($GO_USERS->next_record())
	{
		$calendar_name = format_name($GO_USERS->f('last_name'), $GO_USERS->f('first_name'), $GO_USERS->f('middle_name'), 'last_name');
		if(!$calendar = $cal->get_calendar_by_name($calendar_name, $GO_USERS->f('id')))
		{
			$calendar_id = $cal->add_calendar($GO_USERS->f('id'), addslashes($calendar_name), 7, 20);
			$calendar = $cal->get_calendar($calendar_id);			
		}
		
		foreach($read_groups as $group)
		{
			if(!$GO_SECURITY->group_in_acl($group, $calendar['acl_read']))
			{
				$GO_SECURITY->add_group_to_acl($group, $calendar['acl_read']);
			}
		}
		
		foreach($write_groups as $group)
		{
			if(!$GO_SECURITY->group_in_acl($group, $calendar['acl_write']))
			{
				$GO_SECURITY->add_group_to_acl($group, $calendar['acl_write']);
			}
		}		
	}
	header('Location: '.$return_to);
	exit();
}


require_once($GO_THEME->theme_path.'header.inc');

$form = new form('batchcalendars_form');
$form->add_html_element(new input('hidden', 'return_to', $return_to));
$form->add_html_element(new input('hidden', 'task', 'batchcalendars'));

$tabstrip = new tabstrip('calendar', $sc_calendars);
$tabstrip->set_return_to(htmlspecialchars($return_to));
$tabstrip->set_attribute('style', 'width:100%');

$tabstrip->add_html_element(new html_element('p', $cal_create_all_calendars));

$GO_GROUPS->get_groups();

$table = new table();
$table->set_attribute('style', 'width:100%');

$row = new table_row();

$read_cell = new table_cell();
$read_cell->add_html_element(new html_element('h3', $strReadRights));

$write_cell = new table_cell();
$write_cell->add_html_element(new html_element('h3', $strWriteRights));

while($GO_GROUPS->next_record())
{
	if($GO_GROUPS->f('id') != $GO_CONFIG->group_root)
	{
		$group_check = in_array($GO_GROUPS->f('id'), $read_groups);
  	
  	$checkbox = new checkbox(
  		'read_'.$GO_GROUPS->f('id'),
  		'read_groups[]',
  		$GO_GROUPS->f('id'),
  		$GO_GROUPS->f('name'),
  		$group_check);
  	$read_cell->add_html_element($checkbox);
  	$read_cell->add_html_element(new html_element('br'));
  	
  	
  	
		$group_check = in_array($GO_GROUPS->f('id'), $write_groups);
  	
  	$checkbox = new checkbox(
  		'write_'.$GO_GROUPS->f('id'),
  		'write_groups[]',
  		$GO_GROUPS->f('id'),
  		$GO_GROUPS->f('name'),
  		$group_check);
  	$write_cell->add_html_element($checkbox);
  	$write_cell->add_html_element(new html_element('br')); 	 
  }
}

$row->add_cell($read_cell);
$row->add_cell($write_cell);
$table->add_row($row);


$tabstrip->add_html_element($table);
$tabstrip->add_html_element(new button($cmdOk, 'javascript:document.batchcalendars_form.submit();'));
$tabstrip->add_html_element(new button($cmdClose, "javascript:document.location='$return_to';"));

$form->add_html_element($tabstrip);

echo $form->get_html();
?>
<script type="text/javascript">
function delete_calendar(calendar_id, message)
{
  if (confirm(message))
  {
    document.forms[0].delete_calendar_id.value = calendar_id;
    document.forms[0].task.value='delete_calendar';
    document.forms[0].submit();
  }
}
function save_calendar(close_me)
{
  document.forms[0].close_action.value=close_me;
  document.forms[0].task.value = 'save_calendar';
  document.forms[0].submit();
}
</script>
<?php
require_once($GO_THEME->theme_path.'footer.inc');
