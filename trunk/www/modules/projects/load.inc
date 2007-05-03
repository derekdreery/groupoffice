<?php
/**
 * @copyright Intermesh 2004
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.60 $ $Date: 2006/11/22 09:35:41 $
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */

$show_period = isset($_POST['show_period']) ? $_POST['show_period'] : 'period';
//$load_user_id = isset($_POST['load_user_id']) ? $_POST['load_user_id'] : $GO_SECURITY->user_id;
$load_type = isset($_POST['load_type']) ? $_POST['load_type'] : 'project';
$print = (isset($_POST['print']) && $_POST['print'] == 'true') ? true : false;
$project_id = isset($_POST['project_id']) ? $_POST['project_id'] : '0';


$time = get_time();
$this_day = date("j", $time);
$this_year = date("Y", $time);
$this_month = date("m", $time);

$month = isset($_POST['month']) ? $_POST['month'] : $this_month;
$year = isset($_POST['year']) ? $_POST['year'] : $this_year;

$module_url = isset($projects_module) ? $projects_module['url'] : $GO_MODULES->url;



if (isset($_REQUEST['delete_hours']))
{
	if ($working_hours = $projects->get_working_hours($_REQUEST['delete_hours']))
	{
		if ($GO_MODULES->write_permission || $working_hours['user_id'] == $GO_SECURITY->user_id)
		{
			$projects->delete_hours($_REQUEST['delete_hours']);
		}else
		{
			$feedback =  $strAccessDenied;
		}		
	}
}

if (isset($feedback))
{
  $p = new html_element('p', $feedback);
  $p->set_attribute('class','Error');
  $container->add_html_element($p);
}

if ($project_id== 0 && isset($GO_MODULES->modules['addressbook']) && $GO_MODULES->modules['addressbook']['read_permission'])
{
	$load_id = isset($_POST['load_id']['value']) ? $_POST['load_id']['value'] : '0';
	$load_type = isset($_POST['load_id']['type']) ? $_POST['load_id']['type'] : 'contact';
	
}else
{
	$load_id=$project_id;
	$load_type='project';
}
if ($GO_MODULES->write_permission)
{
	$load_user_id = isset($_REQUEST['load_user_id']['value']) ? $_REQUEST['load_user_id']['value'] : 0;
}else {
	$load_user_id=$GO_SECURITY->user_id;
}

if (!$print)
{
	$container->add_html_element(new html_element('h3', $pm_load_type.':'));	
	
	$table = new table();

	if ($project_id ==0 && isset($GO_MODULES->modules['addressbook']) && $GO_MODULES->modules['addressbook']['read_permission'])
	{
		require_once($GO_MODULES->modules['addressbook']['class_path'].'addressbook.class.inc');
		$ab = new addressbook();
		
		load_control('object_select');
		$select = new object_select($load_type, 'projects_form', 'load_id', $load_id, array('contact','company','project'));
		
		$row = new table_row();
		$row->add_cell(new table_cell($select->get_link($strRelatedTo).':'));
		$row->add_cell(new table_cell($select->get_field()));
		$table->add_row($row);	
	}

	if ($GO_MODULES->write_permission)
	{
		load_control('object_select');
		$select = new object_select('user', 'projects_form', 'load_user_id', $load_user_id);
		$row = new table_row();
		$row->add_cell(new table_cell($select->get_link($pm_employee).':'));
		$row->add_cell(new table_cell($select->get_field()));
		$table->add_row($row);	
	}else
	{
		/*$row = new table_row();		
		$checkbox = 
			new checkbox('load_user_id', 'load_user_id[value]', $GO_SECURITY->user_id, 
			$pm_yours_only, ($load_user_id==$GO_SECURITY->user_id));		
			
		$cell = new table_cell($checkbox->get_html());
		$cell->set_attribute('colspan','2');
		$row->add_cell($cell);
		$table->add_row($row);*/

		
	}
	
	$container->add_html_element($table);
	$container->add_html_element(new html_element('h3', $pm_load_period.':'));	

	$container->add_html_element(new input('hidden', 'delete_hours'));
	$container->add_html_element(new input('hidden', 'booking_id'));
	$container->add_html_element(new input('hidden', 'print'));

	$table = new table();
	
	$radiogroup = new radiogroup('show_period', $show_period);	

	$row = new table_row();
	$cell = new table_cell($radiogroup->get_option(new radiobutton('all', 'all'), $pm_show_all));
	$cell->set_attribute('colspan','2');
	$row->add_cell($cell);
	$table->add_row($row);
	
	$row = new table_row();
	$row->add_cell(new table_cell($radiogroup->get_option(new radiobutton('period', 'period'), $pm_show_period)));
	
	$default_start_date = date($_SESSION['GO_SESSION']['date_format'], mktime(0,0,0,$this_month-1, $this_day, $this_year));
	$default_end_date = date($_SESSION['GO_SESSION']['date_format'], $time);

	$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : $default_start_date;
	$start_datepicker = new date_picker('start_date', $_SESSION['GO_SESSION']['date_format'], $start_date);

	$default_start_date = mktime(0,0,0,$this_month-1, $this_day, $this_year);
	
	$end_date = isset($_POST['end_date']) ? $_POST['end_date'] : $default_end_date;
	$end_datepicker = new date_picker('end_date', $_SESSION['GO_SESSION']['date_format'], $end_date);
	
	$row->add_cell(new table_cell($start_datepicker->get_html().'&nbsp;&nbsp;'.$pm_to.'&nbsp;&nbsp;'.$end_datepicker->get_html()));	
	$table->add_row($row);
	
	$row = new table_row();
	$row->add_cell(new table_cell($radiogroup->get_option(new radiobutton('month', 'month'), $pm_show_month)));
	
	$month_select = new select('month', $month);
	for ($i=1;$i<13;$i++)
  {
  	$month_select->add_value($i, $i);
  }
  
  $year_select = new select('year',$year);
  for ($i=0;$i<10;$i++)
  {
  	$value = $this_year - $i;
  	$year_select->add_value($value,$value);
  }
  
  $cell = new table_cell();
  $cell->add_html_element($month_select);
  $cell->add_html_element($year_select);
	$row->add_cell($cell);
	$table->add_row($row);
	
	$container->add_html_element($table);
	
  $container->add_html_element(new button($cmdOk, "javascript:_load();"));
  $container->add_html_element(new button($cmdPrint, "javascript:_print();"));

  if ($project_id>0)
  {    	
  	$container->add_html_element(new button($cmdClose, "javascript:document.location='".$return_to."';"));
  }
}
/*if ($task=='load')
{*/

	switch($show_period)
	{
		case 'all':
		$start_time=0;
		$end_time=0;
		break;

		case 'period':
		$end_time = date_to_unixtime($end_date)+86400;
		$start_time = date_to_unixtime($start_date);//+86400;
		break;

		case 'month':
		$start_time = mktime(0,0,0,$month, 1, $year);
		$end_time = mktime(0,0,0,$month+1, 1, $year);
		break;
	}
	$load_project_id=0;
	switch($load_type)
	{
		case 'contact':
			//$ab = new addressbook();
			$contact = $ab->get_contact($load_id);
			$link_id = $contact['link_id'];
		break;
		
		case 'project':
			$link_id=0;
			$load_project_id=$load_id;
		break;
		
		case 'company':
			$company = $ab->get_company($load_id);
			$link_id = $company['link_id'];
		break;	
		
		default:
			$link_id=0;
		break;
	}		
	
	if($link_id>0)
	{
		$links = $GO_LINKS->get_links($link_id, 5);
	}else
	{
		$links=false;
	}

	$projects->get_hours($start_time, $end_time, $load_user_id, $load_project_id, $links);
	load_control('datatable');
	$table = new datatable('hours');
	$table->set_attribute('class','hours_table');
	$table->set_attribute('cellpadding','3');

	$table->add_column(new table_heading($GLOBALS['pm_starttime']));
	$table->add_column(new table_heading($GLOBALS['pm_endtime']));
	
	$cell = new table_heading($GLOBALS['pm_breaktime']);
	$cell->set_attribute('style', 'text-align:right');
	$table->add_column($cell);

	if ($load_user_id == 0)
	{
		$table->add_column(new table_heading($GLOBALS['pm_employee']));
	}

	$table->add_column(new table_heading($GLOBALS['pm_project']));

	$cell = new table_heading($GLOBALS['pm_hours']);
	$cell->set_attribute('style', 'text-align:center');
	$table->add_column($cell);
	
	$cell = new table_heading($GLOBALS['pm_internal_fee']);
	$table->add_column($cell);
	
	$cell = new table_heading($GLOBALS['pm_external_fee']);
	$table->add_column($cell);
	
	
	
	
	$cell = new table_heading($GLOBALS['pm_units']);
	$cell->set_attribute('style', 'text-align:center');
	$table->add_column($cell);
	
	$cell = new table_heading('&nbsp;');
	$table->add_column($cell);
	
	$last_date = '';
	$total_days = 0;
	$total_seconds = 0;
	$int_total_fee = 0;
	$ext_total_fee = 0;
	$class = 'unevenRow';

	while($projects->next_record())
	{
		$read_permission = $GO_SECURITY->has_permission($GO_SECURITY->user_id,$projects->f('acl_read'));
		$write_permission = $GO_SECURITY->has_permission($GO_SECURITY->user_id,$projects->f('acl_write'));
		$book_permission = $GO_SECURITY->has_permission($GO_SECURITY->user_id,$projects->f('acl_book'));
		if($read_permission || $write_permission || $book_permission)
		{
			
			$local_start_time = date($_SESSION['GO_SESSION']['date_format'].' '.
				$_SESSION['GO_SESSION']['time_format'], gmt_to_local_time($projects->f('start_time')));
			if ($projects->f('units') > 0)
			{
				$units = $projects->f('units');
				$break_time = '-';

				$local_end_time = '-';
				//$units = ($projects->f('end_time') - $projects->f('start_time'))/($projects->f('unit_value')*60);
				
				$seconds = $units*$projects->f('fee_time')*60;
			}else
			{
				$unit_value = '-';
				$units = '-';
				$break_time = gmdate('G:i', gmmktime(0,0,$projects->f('break_time')));
				
				
				$local_end_time = date($_SESSION['GO_SESSION']['date_format'].' '.
					$_SESSION['GO_SESSION']['time_format'], gmt_to_local_time($projects->f('end_time')));
					
				$seconds = ($projects->f('end_time') - $projects->f('start_time') - $projects->f('break_time'));
			}
			

			if ($projects->f('int_fee_value') > 0)
			{
				$int_fee_string = htmlspecialchars($_SESSION['GO_SESSION']['currency']).'&nbsp;'.
				number_format($projects->f('int_fee_value'), 2, $_SESSION['GO_SESSION']['decimal_seperator'],$_SESSION['GO_SESSION']['thousands_seperator']).
				'&nbsp;/&nbsp;'.$projects->f('fee_time').'&nbsp;'.$GLOBALS['pm_mins'];

				$int_fee_per_hour = ($projects->f('int_fee_value')/$projects->f('fee_time'))*60;
				$int_fee = $int_fee_per_hour*$seconds/3600;
				$int_total_fee += $int_fee;
			}else
			{
				$int_fee_string = '0';
			}
			
			if ($projects->f('ext_fee_value') > 0)
			{
				$ext_fee_string = htmlspecialchars($_SESSION['GO_SESSION']['currency']).'&nbsp;'.
				number_format($projects->f('ext_fee_value'), 2, $_SESSION['GO_SESSION']['decimal_seperator'],$_SESSION['GO_SESSION']['thousands_seperator']).
				'&nbsp;/&nbsp;'.$projects->f('fee_time').'&nbsp;'.$GLOBALS['pm_mins'];

				$ext_fee_per_hour = ($projects->f('ext_fee_value')/$projects->f('fee_time'))*60;
				$ext_fee = $ext_fee_per_hour*$seconds/3600;
				$ext_total_fee += $ext_fee;
			}else
			{
				$ext_fee_string = '0';
			}

			$date = date($_SESSION['GO_SESSION']['date_format'], $projects->f('start_time'));
			if ($date != $last_date) $total_days++;
			$last_date = $date;

			$total_seconds += $seconds;
			
			$row = new table_row();
			$row->set_attribute('class',$class);
			
			$row->add_cell(new table_cell($local_start_time));
			$row->add_cell(new table_cell($local_end_time));
			$cell = new table_cell($break_time);
			$cell->set_attribute('style','text-align:right');
			$row->add_cell($cell);
			
			if ($load_user_id == 0)
			{
				$row->add_cell(new table_cell(show_profile($projects->f('user_id'))));
			}

			$description = ($projects->f('description') == '') ? '' : ' ('.htmlspecialchars($projects->f('description')).')';
			
			$link = new hyperlink('project.php?project_id='.$projects->f('project_id'),htmlspecialchars($projects->f('name')).$description);
			$link->set_attribute('class','normal');
			
			$row->add_cell(new table_cell($link->get_html()));

			$row_hours = floor($seconds/3600);
			$cell = new table_cell($row_hours.':'.gmdate('i', $seconds));
			$cell->set_attribute('style','text-align:center');
			$row->add_cell($cell);
			
			$row->add_cell(new table_cell(($int_fee_string)));
			$row->add_cell(new table_cell(($ext_fee_string)));

			$cell = new table_cell($units);
			$cell->set_attribute('style','text-align:center');
			$row->add_cell($cell);
			
			$cell = new table_cell();
			
			if (!$print && ($write_permission || $book_permission))
			{
				$img = new image('edit');
				$img->set_attribute('align','middle');
				$img->set_attribute('style','border:0px');
				
				$edit_link = new hyperlink('book.php?booking_id='.$projects->f('id').'&return_to='.urlencode($GLOBALS['link_back']),$img->get_html());
				
				$img = new image('delete');
				$img->set_attribute('align','middle');
				$img->set_attribute('style','border:0px');

				
				$delete_link = new hyperlink("javascript:confirm_action('javascript:delete_hours(".
					$projects->f("id").")','".htmlspecialchars($GLOBALS['strDeletePrefix'].' '.
					$GLOBALS['pm_this_entry'].' '.$GLOBALS['strDeleteSuffix'],ENT_QUOTES)."')",
					 $img->get_html(), $GLOBALS['strDeleteItem']." ".$GLOBALS['pm_entry']);
					
					$cell->add_html_element($edit_link);
					$cell->add_html_element($delete_link);
			}
			$row->add_cell($cell);
			$table->add_row($row);
			
			if ($projects->f('comments') != '')
			{
				$row = new table_row();
				$row->set_attribute('class',$class);
				
				$cell = new table_cell(text_to_html($projects->f('comments')));
				$cell->set_attribute('colspan','99');
				$row->add_cell($cell);
				
				$table->add_row($row);
			}

			if ($class == 'unevenRow') $class='evenRow'; else $class='unevenRow';
		}
	}	
	$container->add_html_element($table);

	$h3 = new html_element('h3', $pm_summary);
	$h3->set_attribute('style','margin-top: 10px');
	$container->add_html_element($h3);
	
	$table = new table();
	
	$total_hours = floor($total_seconds/3600);
	
	$row = new table_row();
	$cell = new table_cell($pm_total_hours.':');
	$cell->set_attribute('style', 'font-style:italic');
	$row->add_cell($cell);
	$cell = new table_cell($total_hours.':'.gmdate('i', $total_seconds));
	$cell->set_attribute('style', 'font-style:italic');
	$row->add_cell($cell);
	$table->add_row($row);
	
	
	$row = new table_row();
	$cell = new table_cell($pm_total_days.':');
	$cell->set_attribute('style', 'font-style:italic');
	$row->add_cell($cell);
	$cell = new table_cell($total_days);
	$cell->set_attribute('style', 'font-style:italic');
	$row->add_cell($cell);
	$table->add_row($row);
	
	$row = new table_row();
	$cell = new table_cell($pm_total_internal_fee.':');
	$cell->set_attribute('style', 'font-style:italic');
	$row->add_cell($cell);
	$cell = new table_cell(
		htmlspecialchars($_SESSION['GO_SESSION']['currency']).
		'&nbsp;'.number_format($int_total_fee, 2, 
		$_SESSION['GO_SESSION']['decimal_seperator'],
		$_SESSION['GO_SESSION']['thousands_seperator']));
		$cell->set_attribute('style', 'font-style:italic');		
	$row->add_cell($cell);
	$table->add_row($row);
	
	$row = new table_row();
	$cell = new table_cell($pm_total_external_fee.':');
	$cell->set_attribute('style', 'font-style:italic');
	$row->add_cell($cell);
	$cell = new table_cell(
		htmlspecialchars($_SESSION['GO_SESSION']['currency']).
		'&nbsp;'.number_format($ext_total_fee, 2, 
		$_SESSION['GO_SESSION']['decimal_seperator'],
		$_SESSION['GO_SESSION']['thousands_seperator']));
	$cell->set_attribute('style', 'font-style:italic');		
	$row->add_cell($cell);
	$table->add_row($row);
	
	$container->add_html_element($table);
//}
?>
<script type="text/javascript" langauge="javascript">
function delete_hours(id)
{
	document.projects_form.task.value='load';
	document.projects_form.delete_hours.value=id;
	document.projects_form.submit();
}
  function edit_booking(id)
{
	document.projects_form.project_tab.value='book';
	document.projects_form.booking_id.value=id;
	document.projects_form.submit();
}

function remove_load_client()
{
	document.projects_form.load_contact_id.value = 0;
	document.projects_form.contact_name.value = '';
	document.projects_form.contact_name_text.value = '';
}

function remove_load_user()
{
	document.projects_form.load_user_id.value = 0;
	document.projects_form.user_name.value = '';
	document.projects_form.user_name_text.value = '';
}

function _print()
{
	document.projects_form.task.value='load';
	document.projects_form.print.value = 'true';
	document.projects_form.target = '_blank';
	document.projects_form.action = '<?php echo $module_url; ?>print.php';
	document.projects_form.submit();
	document.projects_form.task.value='load';
	document.projects_form.print.value = 'false';
	document.projects_form.action = '<?php echo $_SERVER['PHP_SELF']; ?>';
	document.projects_form.target = '_self';

}

function _load()
{
	document.projects_form.task.value='load';
	document.projects_form.print.value = 'false';
	document.projects_form.target = '_self';
	document.projects_form.action = '<?php echo $_SERVER['PHP_SELF']; ?>';
	document.projects_form.submit();
}
</script>
