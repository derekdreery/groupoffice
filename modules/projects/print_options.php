<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.8 $ $Date: 2006/11/22 09:35:41 $

 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */
 

require_once("../../Group-Office.php");

load_basic_controls();
load_control('date_picker');

$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('projects', true);
require_once($GO_LANGUAGE->get_language_file('projects'));

$page_title=$menu_projects;
require_once($GO_MODULES->class_path."projects.class.inc");
$projects = new projects();


$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';
$link_back = (isset($_REQUEST['link_back']) && $_REQUEST['link_back'] != '') ? $_REQUEST['link_back'] : $_SERVER['REQUEST_URI'];
$return_to = (isset($_REQUEST['return_to']) && $_REQUEST['return_to'] != '') ? $_REQUEST['return_to'] : $_SERVER['HTTP_REFERER'];

$time = get_time();
$day = date("j", $time);
$year = date("Y", $time);
$month = date("m", $time);

$start_date = isset($_POST['start_date']) ? smart_stripslashes($_POST['start_date']) : '';
$end_date = isset($_POST['end_date']) ? smart_stripslashes($_POST['end_date']) : '';
$statuses = isset($_POST['statuses']) ? $_POST['statuses'] : array();

$date = date($_SESSION['GO_SESSION']['date_format'], $time);



$form = new form('print_form');
//$form->set_attribute('action', 'print_projects.php');
//$form->set_attribute('target', '_blank');
$form->add_html_element(new input('hidden', 'sort_order','DESC'));
$form->add_html_element(new input('hidden', 'sort_index','start_date'));
$form->add_html_element(new input('hidden', 'task','show'));
$form->add_html_element(new input('hidden', 'return_to',$return_to));

if($task == 'show' || $task == '')
{
	$h1 = new html_element('h1', $pm_print_options);
	$form->add_html_element($h1);

	$form->add_html_element(new html_element('p', $pm_print_options_text));
	
	$table = new table();
	$row = new table_row();
	$row->add_cell(new table_cell($pm_start_date.':'));
	$datepicker = new date_picker('start_date', $_SESSION['GO_SESSION']['date_format'], $start_date);

	$cell = new table_cell($datepicker->get_html().'&nbsp;');

	$row->add_cell($cell);
	$table->add_row($row);

	$row = new table_row();
	$row->add_cell(new table_cell($pm_end_date.':'));
	$datepicker = new date_picker('end_date', $_SESSION['GO_SESSION']['date_format'], $end_date);
	$row->add_cell(new table_cell($datepicker->get_html()));
	$table->add_row($row);

	$row = new table_row();
	$checkbox = new checkbox('show_project_users', 'show_project_users', '1', $pm_show_project_users, isset($_POST['show_project_users']));

	$cell = new table_cell($checkbox->get_html());
	$cell->set_attribute('colspan','2');
	$row->add_cell($cell);
	$table->add_row($row);

	$row = new table_row();
	$cell = new table_cell($pm_status.':');
	$cell->set_attribute('style','vertical-align:top');
	$row->add_cell($cell);

	$cell = new table_cell();
	$projects->get_statuses();

	
	while($projects->next_record())
	{

		$checkbox = new checkbox('status_'.$projects->f('id'), 'statuses[]', $projects->f('id'), $projects->f('name'), in_array($projects->f('id'), $statuses));
		$cell->add_html_element($checkbox);
		$cell->innerHTML .= '<br />';
	}

	$row->add_cell($cell);
	$table->add_row($row);

	$form->add_html_element($table);
	$form->add_html_element(new button($cmdOk, 'javascript:document.print_form.target=\'_self\';document.print_form.task.value=\'show\';document.print_form.submit();'));
	$form->add_html_element(new button($cmdPrint, 'javascript:document.print_form.target=\'_blank\';document.print_form.task.value=\'print\';document.print_form.submit();'));
	$form->add_html_element(new button($cmdExport, 'javascript:document.print_form.target=\'_self\';document.print_form.task.value=\'export\';document.print_form.submit();'));
	$form->add_html_element(new button($cmdClose, "javascript:document.location='".htmlspecialchars($return_to)."';"));
}


$cat_fields=array();

if($task !='')
{
	$categories=array();
	if(isset($GO_MODULES->modules['custom_fields']))
	{
		require_once($GO_MODULES->modules['custom_fields']['class_path'].'custom_fields.class.inc');
		$cf = new custom_fields();
		$cf->get_authorized_categories(5, $GO_SECURITY->user_id);
		while($cf->next_record())
		{
			$categories[] = $cf->f('id');
		}
	}
	$users = isset($_POST['show_project_users']) ? $projects->get_project_users() : array();

	$start_time = date_to_unixtime($_POST['start_date']);
	$end_time = date_to_unixtime($_POST['end_date']);

	$projects->get_authorized_projects(
				$GO_SECURITY->user_id,
				false,
				$_REQUEST['sort_index'],
				$_REQUEST['sort_order'],
				0,
				0,
				$statuses,
				'',
				'',
				$start_time,
				$end_time
				);

	if($task != 'export')
	{	
		$table = new table();
		$table->set_attribute('border','1');
		$table->set_attribute('style','border-collapse:collapse;border:1px solid black;white-space:nowrap;width:100%;margin-top:10px;');

		$th = new table_heading($strName);
		$th->set_attribute('style','text-align:left;background-color:#f1f1f1;');
		$table->add_column($th);
		$th = new table_heading($strDescription);
		$th->set_attribute('style','text-align:left;background-color:#f1f1f1;');
		$table->add_column($th);
		$th = new table_heading($pm_start_date);
		$th->set_attribute('style','text-align:left;background-color:#f1f1f1;');
		$table->add_column($th);
		$th = new table_heading($pm_end_date);
		$th->set_attribute('style','text-align:left;background-color:#f1f1f1;');
		$table->add_column($th);
		$th = new table_heading($pm_status);
		$th->set_attribute('style','text-align:left;background-color:#f1f1f1;');
		$table->add_column($th);
		$th = new table_heading($pm_budget);
		$th->set_attribute('style','text-align:right;background-color:#f1f1f1;');
		$table->add_column($th);
		$th = new table_heading($pm_billed);
		$th->set_attribute('style','text-align:right;background-color:#f1f1f1;');
		$table->add_column($th);
		
		foreach($categories as $category_id)
		{
			$cat_fields[$category_id]=array();
			$cf->get_fields($category_id);
			while($cf->next_record())
			{
				$cat_fields[$category_id][]=$cf->Record;
				$th = new table_heading($cf->f('name'));
				if($cf->f('datatype') == 'number')
				{
					$th->set_attribute('style','text-align:right;background-color:#f1f1f1;');
				}else
				{
					$th->set_attribute('style','text-align:left;background-color:#f1f1f1;');
				}
				$table->add_column($th);
				
			}
		}
		
		$th = new table_heading($pm_total_hours);
		$th->set_attribute('style','text-align:right;background-color:#f1f1f1;');
		$table->add_column($th);
		$th = new table_heading($pm_internal_fee);
		$th->set_attribute('style','text-align:right;background-color:#f1f1f1;');
		$table->add_column($th);
		$th = new table_heading($pm_external_fee);
		$th->set_attribute('style','text-align:right;background-color:#f1f1f1;');
		$table->add_column($th);	
		
		$existing_users = array();
		foreach($users as $user_id)
		{
			if($user = $GO_USERS->get_user($user_id))
			{
				$existing_users[] = $user_id;
				$name = format_name($user['last_name'], $user['first_name'], $user['middle_name'], 'last_name');
				$th = new table_heading($pm_hours.'<br />'.$name);
				$th->set_attribute('style','text-align:center;background-color:#f1f1f1;');
				$table->add_column($th);
				$th = new table_heading($pm_internal_fee);
				$th->set_attribute('style','text-align:center;background-color:#f1f1f1;');
				$table->add_column($th);
				$th = new table_heading($pm_external_fee);
				$th->set_attribute('style','text-align:center;background-color:#f1f1f1;');
				$table->add_column($th);		
			}
		}

		
		$projects2= new projects();
		while($projects->next_record())
		{
			$row = new table_row();
			$cell = new table_cell($projects->f('name'));
			$row->add_cell($cell);	
			$cell = new table_cell($projects->f('description'));
			$row->add_cell($cell);	
			$cell = new table_cell(date($_SESSION['GO_SESSION']['date_format'], $projects->f('start_date')));
			$row->add_cell($cell);
			$cell = new table_cell(date($_SESSION['GO_SESSION']['date_format'], $projects->f('end_date')));
			$row->add_cell($cell);
			$cell = new table_cell($projects->f('status_name'));
			$row->add_cell($cell);
			$cell = new table_cell(format_number($projects->f('budget')));
			$row->add_cell($cell);
			$cell = new table_cell(format_number($projects->f('billed')));
			$row->add_cell($cell);
			
			foreach($categories as $category_id)
			{
				$link_id = $projects->f('link_id') > 0 ? $projects->f('link_id') : 0;
				
				$custom_values=$cf->get_values(5,$link_id);
				
				foreach($cat_fields[$category_id] as $field)
				{		
					switch($field['datatype'])
					{
						case 'number':
							$cell = new table_cell(format_number($custom_values['col_'.$field['id']]));
							$cell->set_attribute('style','text-align:right;');
						break;
						
						case 'date':
							$cell = new table_cell(db_date_to_date($custom_values['col_'.$field['id']]));
							$cell->set_attribute('style','text-align:left;');
						break;
						
						case 'checkbox':
							$input = new input('checkbox', '','');
							if($custom_values['col_'.$field['id']]=='1')
							{
								$input->set_attribute('checked','true');
							}
							$cell = new table_cell($input->get_html());
							$cell->set_attribute('style','text-align:center;');
						break;
						
						default:
							$cell = new table_cell($custom_values['col_'.$field['id']]);
							$cell->set_attribute('style','text-align:left;');
						break;
					}
					$row->add_cell($cell);
				}
			}


			$totals = $projects2->get_total_hours($projects->f('id'));
			/*$row_totals['int_fee']=0;
			$row_totals['ext_fee']=0;
			$row_totals['time']=0;*/
			
			$cell = new table_cell(format_number($totals[0]['time']/3600));
			$cell->set_attribute('style','text-align:right');
			$row->add_cell($cell);
			
			$cell = new table_cell(format_number($totals[0]['int_fee']));
			$cell->set_attribute('style','text-align:right');
			$row->add_cell($cell);
			
			$cell = new table_cell(format_number($totals[0]['ext_fee']));
			$cell->set_attribute('style','text-align:right');
			$row->add_cell($cell);
			
			foreach($existing_users as $user_id)
			{
				if(!isset($totals[$user_id]['time']))
				{
					$totals[$user_id]['time']=0;
					$totals[$user_id]['int_fee']=0;
					$totals[$user_id]['ext_fee']=0;
				}
				/*$row_totals['time']+=$totals[$user_id]['time'];
				$row_totals['int_fee']+=$totals[$user_id]['int_fee'];
				$row_totals['ext_fee']+=$totals[$user_id]['ext_fee'];*/
				
				$cell = new table_cell(format_number($totals[$user_id]['time']/3600));
				$cell->set_attribute('style','text-align:right');
				$row->add_cell($cell);
				$cell = new table_cell(format_number($totals[$user_id]['int_fee']));
				$cell->set_attribute('style','text-align:right');
				$row->add_cell($cell);		
				$cell = new table_cell(format_number($totals[$user_id]['ext_fee']));
				$cell->set_attribute('style','text-align:right');
				$row->add_cell($cell);
			}	
			
			
			$table->add_row($row);
		}
		$form->add_html_element($table);
	}else
	{	
	
		$headings[]  = $strName;
		$headings[]  = $strDescription;
		$headings[]  = $pm_start_date;
		$headings[]  = $pm_end_date;
		$headings[]  = $pm_status;
		$headings[]  = $pm_budget;
		$headings[]  = $pm_billed;
		$headings[]  = $pm_to_bill;
		
		

		foreach($categories as $category_id)
		{
			$cat_fields[$category_id]=array();
		
			$cf->get_fields($category_id);
			while($cf->next_record())
			{
				$cat_fields[$category_id][]=$cf->Record;
				$headings[]  = $cf->f('name');				
			}
		}
		
		$headings[]  = $pm_total_hours;
		$headings[]  = $pm_internal_fee;
		$headings[]  = $pm_external_fee;
		$headings[]  = $pm_total_recieved;
		$headings[]  = $pm_result;
		
		$existing_users = array();
		foreach($users as $user_id)
		{
			if($user = $GO_USERS->get_user($user_id))
			{
				$name = format_name($user['last_name'],$user['first_name'], $user['middle_name'], 'first_name');
				$headings[]  = $pm_hours.' '.$name;
				$headings[]  = $pm_internal_fee;
				$headings[]  = $pm_external_fee;
			}
		}
		
		
		
		$csv = '"'.implode('";"', $headings).'"';
		$csv .= "\r\n";

		$projects2= new projects();
		while($projects->next_record())
		{
			$columns = array(
			$projects->f('name'),
			$projects->f('description'),
			date($_SESSION['GO_SESSION']['date_format'], $projects->f('start_date')),
			date($_SESSION['GO_SESSION']['date_format'], $projects->f('end_date')),
			$projects->f('status_name'),
			format_number($projects->f('budget')),
			format_number($projects->f('billed')),
			'0');
			
		
			foreach($categories as $category_id)
			{
				$link_id = $projects->f('link_id') > 0 ? $projects->f('link_id') : 0;
				$custom_values=$cf->get_values(5,$link_id);
				
				foreach($cat_fields[$category_id] as $field)
				{		
					switch($field['datatype'])
					{
						case 'number':
							$columns[]= format_number($custom_values['col_'.$field['id']]);
						break;
						
						case 'date':
							$columns[]=db_date_to_date($custom_values['col_'.$field['id']]);
						break;
						
						case 'checkbox':
							if($custom_values['col_'.$field['id']]=='1')
							{
								$columns[]= $cmdYes;
							}else
							{
								$columns[]= $cmdNo;
							}
						break;
						
						default:
							$columns[]=$custom_values['col_'.$field['id']];
						break;
					}
				}
			}

			
			$totals = $projects2->get_total_hours($projects->f('id'));
			
			$columns[]=format_number($totals[0]['time']/3600);
			$columns[]=format_number($totals[0]['int_fee']);
			$columns[]=format_number($totals[0]['ext_fee']);
			$columns[]='0';
			$columns[]='0';
			
			foreach($existing_users as $user_id)
			{
				if(!isset($totals[$user_id]['time']))
				{
					$totals[$user_id]['time']=0;
					$totals[$user_id]['int_fee']=0;
					$totals[$user_id]['ext_fee']=0;
				}
				
				$columns[]=format_number($totals[$user_id]['time']/3600);
				$columns[]=format_number($totals[$user_id]['int_fee']);
				$columns[]=format_number($totals[$user_id]['ext_fee']);
			}
			
			
			
			$csv .= '"'.implode('";"', $columns).'"';
			$csv .= "\r\n";			
		}
		
		$filename = $lang_modules['projects'].'.csv';
		
		$browser = detect_browser();
		header('Content-Type: text/plain;charset='.$charset);
		header('Expires: '.gmdate('D, d M Y H:i:s') . ' GMT');
		if ($browser['name'] == 'MSIE')
		{
			header('Content-Disposition: attachment; filename="'.$filename.'"');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
		}else
		{
			header('Pragma: no-cache');
			header('Content-Disposition: attachment; filename="'.$filename.'"');
		}
		header('Content-Transfer-Encoding: binary');
		echo $csv;
		exit();		
	}
}

$GO_HEADER['head'] = date_picker::get_header();

require_once($GO_THEME->theme_path."header.inc");
echo $form->get_html();
require_once($GO_THEME->theme_path."footer.inc");
