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

$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('calendar');
require_once($GO_LANGUAGE->get_language_file('calendar'));

load_basic_controls();
load_control('datatable');

$link_back = (isset($_REQUEST['link_back']) && $_REQUEST['link_back'] != '') ? $_REQUEST['link_back'] : $_SERVER['REQUEST_URI'];
$return_to = (isset($_REQUEST['return_to']) && $_REQUEST['return_to'] != '') ? $_REQUEST['return_to'] : $_SERVER['HTTP_REFERER'];
$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';

//load contact management class
require_once($GO_MODULES->class_path."calendar.class.inc");
$cal = new calendar();

$tabstrip = new tabstrip('admin_tabstrip', $cal_admin);
$tabstrip->set_attribute('style','width:100%');
$tabstrip->set_return_to(htmlspecialchars($return_to));
$tabstrip->add_tab('calendars', $sc_calendars);
$tabstrip->add_tab('views', $cal_views);
$tabstrip->add_tab('resource_groups', $cal_resource_groups);
$tabstrip->add_tab('resources', $cal_resources);
$tabstrip->add_tab('background_colors', $cal_background_colors);
$tabstrip->add_tab('holidays', $sc_holidays);

$form = new form('calendar');
$form->add_html_element(new input('hidden', 'task'));
$form->add_html_element(new input('hidden', 'close', 'false'));
$form->add_html_element(new input('hidden', 'return_to', htmlspecialchars($return_to)));
$form->add_html_element(new input('hidden', 'link_back', $link_back));

$menu = new button_menu();

switch($tabstrip->get_active_tab_id())
{
	
	case 'holidays':
		load_control('date_picker');
		$GO_HEADER['head'] = date_picker::get_header();
		require_once('holidays.inc');
	break;
	
	case 'background_colors':

		$datatable = new datatable('background_colors');

		if($datatable->task=='delete')
		{
			foreach($datatable->selected as $background_id)
			{
				$cal->delete_background($background_id);
			}
		}

		$GO_HEADER['head'] = $datatable->get_header();

		$menu = new button_menu();
		$menu->add_button('add', $cmdAdd, 'background.php?return_to='.urlencode($link_back));
		$menu->add_button('delete_big', $cmdDelete, $datatable->get_delete_handler());

		$th = new table_heading($strName);
		$th->set_attribute('colspan','2');
		$datatable->add_column($th);

		if($cal->get_backgrounds())
		{
			while($cal->next_record())
			{
				$row = new table_row($cal->f('id'));
				$row->set_attribute('ondblclick', "javascript:document.location='background.php?background_id=".$cal->f('id')."&return_to=".urlencode($link_back)."';");
				$div = new html_element('div', '');
				$div->set_attribute('style','margin:2px;width:8px;height:8px;border:1px solid black;background-color:#'.$cal->f('color'));
				$cell = new table_cell($div->get_html());
				$cell->set_attribute('style','width:8px;');
				$row->add_cell($cell);

				$row->add_cell(new table_cell($cal->f('name')));
				$datatable->add_row($row);
			}
		}else
		{
			$row = new table_row();
			$cell = new table_cell($strNoItems);
			$cell->set_attribute('colspan','99');
			$row->add_cell($cell);
			$datatable->add_row($row);
		}
		$tabstrip->add_html_element($datatable);
		break;

	case 'resource_groups':
		$datatable = new datatable('groups_table');
		$datatable->set_attribute('style','width:100%');

		$GO_HEADER['head'] = $datatable->get_header();

		if($datatable->task == 'delete')
		{
			foreach($datatable->selected as $group_id)
			{
				$cal->delete_group($group_id);
			}
		}

		if($GO_MODULES->write_permission)
		{
			$menu->add_button('add',$cmdAdd,'group.php?return_to='.urlencode($link_back));
		}
		$menu->add_button('delete_big',$cmdDelete, $datatable->get_delete_handler());
		$menu->add_button('close',$cmdClose,htmlspecialchars($return_to));

		$datatable->add_column(new table_heading($strName));

		if($cal->get_writable_resource_groups($GO_SECURITY->user_id))
		{
			while ($cal->next_record())
			{
				$row = new table_row($cal->f('id'));
				$row->set_attribute('ondblclick', "document.location='group.php?group_id=".$cal->f("id")."&return_to=".urlencode($link_back)."';");

				$row->add_cell(new table_cell($cal->f("name")));

				$datatable->add_row($row);
			}
		}else {
			$row = new table_row();
			$cell = new table_cell($strNoItems);
			$cell->set_attribute('colspan','2');
			$row->add_cell($cell);
			$datatable->add_row($row);
		}
		$tabstrip->add_html_element($datatable);
		break;

	case 'resources':
		$datatable = new datatable('calendars');

		switch($datatable->task)
		{
			case 'delete':
				require_once($GO_MODULES->class_path."cal_holidays.class.inc");
				$holidays = new holidays();

				foreach($datatable->selected as $delete_calendar_id) {
					$calendar = $cal->get_calendar($delete_calendar_id);

					if($GO_SECURITY->has_permission($GO_SECURITY->user_id, $calendar['acl_write']))
					{
						if ($cal->delete_calendar($delete_calendar_id))
						{
							$holidays->delete_holidays($GO_SECURITY->user_id, $delete_calendar_id);
						}
					}else
					{
						$feedback = $strAccessDenied;
						break;
					}
				}
				break;
		}

		$GO_HEADER['head'] = $datatable->get_header();

		if($cal->get_writable_resource_groups($GO_SECURITY->user_id) > 0)
		{
			$menu->add_button('add', $cmdAdd, 'calendar.php?return_to='.$link_back);
		}
		$menu->add_button('delete_big', $cmdDelete, $datatable->get_delete_handler());
		$menu->add_button('close', $cmdClose, htmlspecialchars($return_to));

		if (isset($feedback))
		{
			$p = new html_element('p', $feedback);
			$p->set_attribute('class', 'Error');
			$tabstrip->add_html_element($p);
		}

		$datatable->add_column(new table_heading($sc_calendar));
		$datatable->add_column(new table_heading($strOwner));

		$cal2 = new calendar();

		$group_count = $cal2->get_resource_groups();
		while($cal2->next_record())
		{


			if($GO_SECURITY->has_admin_permission($GO_SECURITY->user_id))
			{
				$cal_count = $cal->get_writable_calendars($GO_SECURITY->user_id, $cal2->f('id'));
			}else {
				$cal_count = $cal->get_user_calendars($GO_SECURITY->user_id, $cal2->f('id'));
			}

			if ($cal_count > 0) {
				$row = new table_row();
				$cell = new table_cell($cal2->f('name'));
				$cell->set_attribute('colspan','99');
				$row->set_attribute('class','groupRow');

				$row->add_cell($cell);
				$datatable->add_row($row);
				while ($cal->next_record()) {
					

					$row = new table_row($cal->f('id'));
					$row->set_attribute('ondblclick', "javascript:document.location='calendar.php?calendar_id=".$cal->f('id')."&return_to=".urlencode($link_back)."'");
					$cell = new table_cell($cal->f('name'));
					$cell->set_attribute('style','padding-left: 15px;');
					$row->add_cell($cell);
					$row->add_cell(new table_cell(show_profile($cal->f('user_id'))));

					$datatable->add_row($row);
				}
			}
		}

		if(!count($datatable->rows))
		{
			$row = new table_row();
			$cell = new table_cell($strNoItems);
			$cell->set_attribute('colspan','2');
			$row->add_cell($cell);
			$datatable->add_row($row);
		}
		$tabstrip->add_html_element($datatable);
		break;

	case 'views':
		$datatable = new datatable('views');

		switch($datatable->task)
		{
			case 'delete':

				foreach($datatable->selected as $delete_view_id) {
					$view = $cal->get_view($delete_view_id);

					if($GO_SECURITY->has_permission($GO_SECURITY->user_id, $view['acl_write']))
					{
						if ($cal->delete_view($delete_view_id))
						{
							$GO_SECURITY->delete_acl($view['acl_write']);
							$GO_SECURITY->delete_acl($view['acl_read']);
						}
					}else
					{
						$feedback = $strAccessDenied;
						break;
					}
				}
				break;
		}

		$GO_HEADER['head'] = $datatable->get_header();

		require_once($GO_THEME->theme_path."header.inc");

		$menu->add_button('add', $cmdAdd, 'view.php?return_to='.$link_back);
		$menu->add_button('delete_big', $cmdDelete, $datatable->get_delete_handler());


		$menu->add_button('close', $cmdClose, htmlspecialchars($return_to));

		if (isset($feedback))
		{
			$p = new html_element('p', $feedback);
			$p->set_attribute('class', 'Error');
			$tabstrip->add_html_element($p);
		}

		$datatable->add_column(new table_heading($sc_view));
		$datatable->add_column(new table_heading($strOwner));

		$cal_count = $cal->get_writable_views($GO_SECURITY->user_id, $datatable->start, $datatable->offset);
		$datatable->set_pagination($cal_count);

		if ($cal_count > 0) {
			while ($cal->next_record()) {

				$row = new table_row($cal->f('id'));
				$row->set_attribute('ondblclick', "javascript:document.location='view.php?view_id=".$cal->f('id')."&return_to=".urlencode($link_back)."'");
				$row->add_cell(new table_cell($cal->f('name')));
				$row->add_cell(new table_cell(show_profile($cal->f('user_id'))));

				$datatable->add_row($row);
			}
		} else {
			$row = new table_row();
			$cell = new table_cell($cal_no_views);
			$cell->set_attribute('colspan','2');
			$row->add_cell($cell);
			$datatable->add_row($row);
		}
		$tabstrip->add_html_element($datatable);
		break;

	case 'calendars':
		$datatable = new datatable('calendars');

		switch($datatable->task)
		{
			case 'delete':
				require_once($GO_MODULES->class_path."cal_holidays.class.inc");
				$holidays = new holidays();

				foreach($datatable->selected as $delete_calendar_id) {
					$calendar = $cal->get_calendar($delete_calendar_id);

					if($GO_SECURITY->has_permission($GO_SECURITY->user_id, $calendar['acl_write']))
					{
						if ($cal->delete_calendar($delete_calendar_id))
						{
							$holidays->delete_holidays($GO_SECURITY->user_id, $delete_calendar_id);
						}
					}else
					{
						$feedback = $strAccessDenied;
						break;
					}
				}
				break;
		}

		$GO_HEADER['head'] = $datatable->get_header();



		if ($GO_MODULES->write_permission) {
			$menu->add_button('add', $cmdAdd, 'calendar.php?group_id=0&return_to='.$link_back);
			$menu->add_button('delete_big', $cmdDelete, $datatable->get_delete_handler());
		}

		if($GO_SECURITY->has_admin_permission($GO_SECURITY->user_id))
		{
			$menu->add_button('cal_view', $cal_batchcalendars, 'batchcalendars.php?return_to='.$link_back);
		}

		$menu->add_button('close', $cmdClose, htmlspecialchars($return_to));

		if (isset($feedback))
		{
			$p = new html_element('p', $feedback);
			$p->set_attribute('class', 'Error');
			$form->add_html_element($p);
		}

		$datatable->add_column(new table_heading($sc_calendar));
		$datatable->add_column(new table_heading($strOwner));

		if($GO_SECURITY->has_admin_permission($GO_SECURITY->user_id))
		{
			$cal_count = $cal->get_writable_calendars($GO_SECURITY->user_id, 0, $datatable->start, $datatable->offset);
			$datatable->set_pagination($cal_count);
		}else {
			$cal_count = $cal->get_user_calendars($GO_SECURITY->user_id, 0);
		}


		if ($cal_count > 0) {
			while ($cal->next_record()) {

				$row = new table_row($cal->f('id'));
				$row->set_attribute('ondblclick', "javascript:document.location='calendar.php?calendar_id=".$cal->f('id')."&return_to=".urlencode($link_back)."'");
				$cell = new table_cell($cal->f('name'));
				$row->add_cell($cell);
				$row->add_cell(new table_cell(show_profile($cal->f('user_id'))));

				$datatable->add_row($row);
			}
		} else {
			$row = new table_row();
			$cell = new table_cell($cal_no_calendars);
			$cell->set_attribute('colspan','2');
			$cell->set_attribute('style','padding-left: 15px;');
			$row->add_cell($cell);
			$datatable->add_row($row);
		}

		$tabstrip->add_html_element($datatable);
		break;
}

$form->add_html_element($menu);
$form->add_html_element($tabstrip);
require_once($GO_THEME->theme_path."header.inc");
echo $form->get_html();

if($tabstrip->get_active_tab_id()=='holidays')
{
?>
<script type="text/javascript">
function delete_holiday(holiday_id, holiday_name)
{
	if (confirm("<?php echo $strDeletePrefix; ?>'"+holiday_name+"'<?php echo $strDeleteSuffix; ?>"))
	{
		document.forms[0].task.value='delete_holiday';
		document.forms[0].holiday_id.value=holiday_id;
		document.forms[0].submit();
	}
}

function delete_holidays(year)
{
	if (confirm("<?php echo $strDeleteHolidaysPrefix; ?>"+year+"<?php echo $strDeleteHolidaysSuffix; ?>"))
	{
		document.forms[0].task.value='delete_holidays';
		document.forms[0].year.value=year;
		document.forms[0].submit();
	}
}

function edit_holiday(id)
{
	document.forms[0].task.value='edit_holiday';
	document.forms[0].holiday_id.value=id;
	document.forms[0].submit();
}

function cancel_holidays()
{
	document.forms[0].task.value='';
	document.forms[0].submit();
}

function save_holiday()
{
	document.forms[0].task.value='save_holiday';
	document.forms[0].submit();
}

function generate_holidays(year)
{
	document.forms[0].task.value='generate_holidays';
	document.forms[0].year.value=year;
	document.forms[0].submit();
}

function apply_holidays(holidays_count)
{
	var apply=true;
	if (holidays_count > 0)
	{
		if (!confirm("<?php echo $strReplaceHolidays; ?>"))
		{
			apply=false;
		}
	}
	if (apply)
	{
		document.forms[0].task.value='apply_holidays';
		document.forms[0].submit();
	}
}
</script>
<?php
}
require_once($GO_THEME->theme_path."footer.inc");
?>