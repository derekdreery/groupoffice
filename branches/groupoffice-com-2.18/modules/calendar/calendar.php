<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.54 $ $Date: 2006/11/22 15:09:59 $

 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */
require_once("../../Group-Office.php");

$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('calendar');
require_once($GO_LANGUAGE->get_language_file('calendar'));


load_basic_controls();
load_control('color_selector');

require_once($GO_MODULES->path.'classes/calendar.class.inc');
$cal = new calendar();

$GO_CONFIG->set_help_url($cal_help_url);

$date = isset($_REQUEST['date']) ? $_REQUEST['date'] : getdate();
$year = isset($_POST['year']) ? $_POST['year'] : $date["year"];
$month = isset($_POST['month']) ? $_POST['month'] : $date["mon"];
$day = isset($_POST['day']) ? $_POST['day'] : $date["mday"];

$task = isset($_POST['task']) ? $_POST['task'] : '';
$return_to = isset($_REQUEST['return_to']) ? $_REQUEST['return_to'] : $_SERVER['HTTP_REFERER'];


$calendar_id = isset($_REQUEST['calendar_id']) ? $_REQUEST['calendar_id'] : 0;
$link_back = $_SERVER['PHP_SELF'].'?calendar_id='.$calendar_id.'&return_to='.urlencode($return_to);

$hours = array("00","01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23");

switch($task)
{
	case 'import':
		if (!file_exists($_FILES['ical_file']['tmp_name']))
		{
			$feedback = $cal_no_file;
		}else
		{
			if($cal->import_ical_file($GO_SECURITY->user_id, $_FILES['ical_file']['tmp_name'], $calendar_id))
			{
				$feedback =$cal_import_success;
			}else
			{
				$feedback = $strDataError;
			}
			unlink($_FILES['ical_file']['tmp_name']);
		}
	break;

	case 'save':		
		$user_id = (isset($_POST['user_id']) && $_POST['user_id'] > 0) ? $_POST['user_id'] : $GO_SECURITY->user_id;
		
		$name = smart_addslashes(trim($_POST['name']));
		$public = isset($_POST['public']) ? '1' : '0';
		if ($name != "")
		{
			if ($calendar_id > 0)
			{
				$existing_calendar = $cal->get_calendar_by_name($name);

				if ($existing_calendar && $existing_calendar['id'] != $calendar_id)
				{
					$feedback = $sc_calendar_exists;

				}else
				{
					$calendar = $cal->get_calendar($calendar_id);
					if($calendar['user_id'] != $user_id)
					{
						$GO_SECURITY->chown_acl($calendar['acl_read'], $user_id);
						$GO_SECURITY->chown_acl($calendar['acl_write'], $user_id);
					}
					
					
					$cal->update_calendar( $calendar_id, $user_id,
						$name, 
						smart_addslashes($_POST['calendar_start_hour']), 
						smart_addslashes($_POST['calendar_end_hour']),
						smart_addslashes($_POST['background']),
						$public,
						smart_addslashes($_POST['group_id']),
						smart_addslashes($_POST['time_interval']));
							
					if ($_POST['close'] == 'true')
					{
						header('Location: '.$return_to);
						exit();
					}
				}
			}else
			{
				if ($cal->get_calendar_by_name($name))
				{
					$feedback = $sc_calendar_exists;
				}else
				{
					if ($calendar_id = $cal->add_calendar($user_id, 
							$name, 
							smart_addslashes($_POST['calendar_start_hour']), 
							smart_addslashes($_POST['calendar_end_hour']),
							smart_addslashes($_POST['background']),
							smart_addslashes($_POST['group_id']),
							smart_addslashes($_POST['time_interval'])))
					{
						if ($_POST['close'] == 'true')
						{
							header('Location: '.$return_to);
							exit();
						}
					}else
					{
						$feedback = $strSaveError;
					}
				}
			}
		}else
		{
			$feedback = $error_missing_field;
		}
	break;
}

if ($calendar_id > 0)
{
	$calendar = $cal->get_calendar($calendar_id);
	$title = $calendar['name'];
	
	//if(!$GO_SECURITY->has_permission($GO_SECURITY->user_id, $calendar['acl_write']))
	if($calendar['user_id']!=$GO_SECURITY->user_id && !$GO_MODULES->write_permission)
	{
		header('Location: '.$GO_CONFIG->host.'error_docs/403.php');
		exit();
	}
}else
{
	$calendar['user_id'] = $GO_SECURITY->user_id;
	$calendar['start_hour'] = isset($_POST['calendar_start_hour']) ? $_POST['calendar_start_hour'] : '07';
	$calendar['end_hour'] = isset($_POST['calendar_end_hour']) ? $_POST['calendar_end_hour'] : '20';
	$calendar['name'] = isset($_POST['name']) ? smart_stripslashes($_POST['name']) : '';
	$calendar['background'] = 'FFFFCC';
	$calendar['group_id'] = isset($_REQUEST['group_id']) ? smart_stripslashes($_REQUEST['group_id']) : '1';
	$calendar['time_interval'] = isset($_REQUEST['time_interval']) ? smart_stripslashes($_REQUEST['time_interval']) : '1800';
	$title = $sc_new_calendar;
}

$tabstrip = new tabstrip('calendar_strip', $title);
$tabstrip->set_attribute('style','width:100%');
$tabstrip->set_return_to(htmlspecialchars($return_to));


if ($calendar_id > 0)
{
	$tabstrip->add_tab('calendar', $strProperties);
	if($cal->get_backgrounds())
	{
		$tabstrip->add_tab('calendar_backgrounds', $cal_background_colors);
	}
	
	$tabstrip->add_tab('import', $cal_import);
	
	$group = $cal->get_group($calendar['group_id']);
	
	if($GO_MODULES->write_permission || $GO_SECURITY->has_permission($GO_SECURITY->user_id, $group['acl_write']))
	{
		$tabstrip->add_tab('read_permissions', $strReadRights);
		$tabstrip->add_tab('write_permissions', $strWriteRights);
	}
}

switch ($tabstrip->get_active_tab_id())
{	
	
	
	case 'calendar_backgrounds':
		load_control('datatable');
		$GO_HEADER['head'] = datatable::get_header();
	break;
	
	case 'calendar':
		$GO_HEADER['head'] = color_selector::get_header();
		$GO_HEADER['body_arguments'] = 'onload="javascript:document.calendar_form.name.focus();"';
	break;
	
	case '':
		$GO_HEADER['head'] = color_selector::get_header();
		$GO_HEADER['body_arguments'] = 'onload="javascript:document.calendar_form.name.focus();"';
	break;
}
require_once($GO_THEME->theme_path.'header.inc');

$form = new form('calendar_form');
$form->set_attribute('enctype','multipart/form-data');

$form->add_html_element(new input('hidden', 'calendar_id',$calendar_id,false));
$form->add_html_element(new input('hidden', 'task','',false));
$form->add_html_element(new input('hidden', 'close','false',false));
$form->add_html_element(new input('hidden', 'return_to',$return_to,false));


switch($tabstrip->get_active_tab_id())
{
	case 'read_permissions':
		$tabstrip->innerHTML .= get_acl($calendar['acl_read']);
		$tabstrip->add_html_element(new button($cmdClose,"javascript:document.location='".htmlspecialchars($return_to)."'"));
	break;
	
	case 'write_permissions':
		$tabstrip->innerHTML .= get_acl($calendar['acl_write']);
		$tabstrip->add_html_element(new button($cmdClose,"javascript:document.location='".htmlspecialchars($return_to)."'"));
	break;

	

	case 'import':
		require_once('import.inc');
	break;
	
	case 'calendar_backgrounds':
	
		$datatable = new datatable('calendar_backgrounds');
		
		if($datatable->task=='delete')
		{
			foreach($datatable->selected as $calendar_background_id)
			{
				$cal->delete_calendar_background($calendar_background_id);
			}
		}
		
		$menu = new button_menu();
		$menu->add_button('add', $cmdAdd, 'calendar_background.php?calendar_id='.$calendar_id.'&return_to='.urlencode($link_back));
		$menu->add_button('delete_big', $cmdDelete, $datatable->get_delete_handler());
		
		$form->add_html_element($menu);
		$th =new table_heading($strName);
		$th->set_attribute('colspan','2');
		$datatable->add_column($th);
		$datatable->add_column(new table_heading($cal_weekday));
		$datatable->add_column(new table_heading($sc_start_time));
		$datatable->add_column(new table_heading($sc_end_time));
		
		if($cal->get_calendar_backgrounds($calendar_id))
		{
			while($cal->next_record())
			{
				$row = new table_row($cal->f('id'));
				$row->set_attribute('ondblclick', "javascript:document.location='calendar_background.php?calendar_background_id=".$cal->f('id')."&return_to=".urlencode($link_back)."';");
				
				$div = new html_element('div', '');
				$div->set_attribute('style','margin:2px;width:8px;height:8px;border:1px solid black;background-color:#'.$cal->f('color'));
				$cell = new table_cell($div->get_html());
				$cell->set_attribute('style','width:8px;');
				$row->add_cell($cell);
				$row->add_cell(new table_cell($cal->f('name')));
				$row->add_cell(new table_cell($full_days[$cal->f('weekday')]));
				$row->add_cell(new table_cell(gmdate($_SESSION['GO_SESSION']['time_format'], $cal->f('start_time'))));
				$row->add_cell(new table_cell(gmdate($_SESSION['GO_SESSION']['time_format'], $cal->f('end_time'))));
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

	default:
	
	if(isset($feedback))
	{
		$p = new html_element('p',$feedback);
		$p->set_attribute('class','Error');
		$tabstrip->add_html_element($p);
	}
	
	$table = new table();
			
	if($GO_SECURITY->has_admin_permission($GO_SECURITY->user_id))	
	{
		load_control('user_autocomplete');
		$user_autocomplete=new user_autocomplete('user_id',$calendar['user_id'],'0',$link_back);			
		
		$row = new table_row();
		$row->add_cell(new table_cell($strOwner.':'));
		$row->add_cell(new table_cell($user_autocomplete->get_html()));
		$table->add_row($row);
	}else
	{
		$form->add_html_element(new input('hidden', 'user_id', $calendar['user_id']));
	}
	
	if($calendar['group_id'] == 0)
	{
		$form->add_html_element(new input('hidden','group_id','0'));
	}else
	{
	
		$row = new table_row();
		$row->add_cell(new table_cell($cal_group.': '));
		
		$select = new select('group_id', $calendar['group_id']);
		$cal->get_writable_resource_groups($GO_SECURITY->user_id);
		while($cal->next_record())
		{
			$select->add_value($cal->f('id'), $cal->f('name'));
		}
		if(!$select->is_in_select($calendar['group_id']) && $resource_group=$cal->get_group($calendar['group_id']))
		{
			$select->add_value($resource_group['id'], $resource_group['name']);
		}
		$row->add_cell(new table_cell($select->get_html()));
		$table->add_row($row);
	}
	$row = new table_row();
	$row->add_cell(new table_cell($strName.'*: '));
	$input = new input('text', 'name',$calendar['name']);
	$input->set_attribute('maxlength','100');
	$input->set_attribute('style', 'width:300px');
	$row->add_cell(new table_cell($input->get_html()));
	$table->add_row($row);
	
	$row = new table_row();
	$row->add_cell(new table_cell($sc_show_hours.': '));
	
	$select1 = new select('calendar_start_hour', $calendar['start_hour']);
	$select1->add_arrays($hours, $hours);	
	$select2 = new select('calendar_end_hour', $calendar['end_hour']);
	$select2->add_arrays($hours, $hours);	
	
	$row->add_cell(new table_cell($select1->get_html().$sc_to.$select2->get_html()));
	$table->add_row($row);
	
	$row = new table_row();
	$row->add_cell(new table_cell($cal_scale.': '));
	
	$select = new select('time_interval', $calendar['time_interval']);
	$select->add_value('900', '15 '.$sc_mins);
	$select->add_value('1800', '30 '.$sc_mins);
	$select->add_value('3600', '1 '.$sc_hour);	
	
	$row->add_cell(new table_cell($select->get_html()));
	$table->add_row($row);	
	
	
	$row = new table_row();
	$row->add_cell(new table_cell($sc_background.': '));
	
	$color_selector = new color_selector('background','background', $calendar['background'], 'calendar_form');
	$row->add_cell(new table_cell($color_selector->get_html()));
	$table->add_row($row);
	
	if(file_exists('public.php'))
	{	
		$row = new table_row();
		$row->add_cell(new table_cell($cal_public_calendar.': '));
		$checkbox = new checkbox('public','public','1', '', $calendar['public']);
		$row->add_cell(new table_cell($checkbox->get_html()));
		$table->add_row($row);
		
		$row = new table_row();
		$row->add_cell(new table_cell($cal_public_url.': '));
		
		$link = new hyperlink($GO_MODULES->modules['calendar']['full_url'].'public.php?calendar_id='.$calendar_id, $GO_MODULES->modules['calendar']['full_url'].'public.php?calendar_id='.$calendar_id);
		$link->set_attribute('class','normal');
		$link->set_attribute('target','_blank');
		
		$row->add_cell(new table_cell($link->get_html()));
		$table->add_row($row);
	}
	
	$tabstrip->add_html_element($table);
	
	$tabstrip->add_html_element(new button($cmdOk,"javascript:document.forms[0].close.value='true';document.forms[0].task.value='save';document.forms[0].submit()"));
	$tabstrip->add_html_element(new button($cmdApply,"javascript:document.forms[0].task.value='save';document.forms[0].submit()"));
	if ($calendar_id > 0)
	{
		$tabstrip->add_html_element(new button($cal_export, "document.location='export.php?calendar_id=$calendar_id';"));
	}
	$tabstrip->add_html_element(new button($cmdClose,"javascript:document.location='".htmlspecialchars($return_to)."'"));	
	break;
}

$form->add_html_element($tabstrip);

echo $form->get_html();
?>
<script type="text/javascript" language="javascript">
<!--
function upload()
{
	document.forms[0].task.value="import";
	var status = null;
	if (status = get_object("status"))
	{
		status.innerHTML = "<?php echo $cal_please_wait; ?>";
	}
	document.forms[0].submit();
}


-->
</script>
<?php
require_once($GO_THEME->theme_path.'footer.inc');
