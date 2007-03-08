<?php
/*
Copyright Georg Lorenz
Author: Georg Lorenz <georg@lonux.de>
Version: 1.0 Release date: 29 January 2004

This program is free software; you can redistribute it and/or modify it
under the terms of the GNU General Public License as published by the
Free Software Foundation; either version 2 of the License, or (at your
option) any later version.
*/

require_once($GO_MODULES->path.'classes/cal_holidays.class.inc');
$holidays = new holidays();

$regions = $GO_LANGUAGE->get_languages();
$holiday_id = isset($_REQUEST['holiday_id']) ? $_REQUEST['holiday_id'] : 0;
$region = isset($_REQUEST['region']) ? $_REQUEST['region'] : '';

$year=date('Y');

$date = isset($_REQUEST['date']) ? $_REQUEST['date'] : getdate();

$min_year = 1970;
$max_year = 2037;
$date_format = $_SESSION['GO_SESSION']['date_format'];
$current_date = getdate();
$current_year = $current_date["year"];

for($i=-2;$i<3;$i++)
{
	if($i==0)
		$years[] = $year;
	elseif(($year + $i) >= $min_year && ($year + $i) <= $max_year)
		$years[] = $year + $i;
}

if(!in_array($current_year, $years))
	array_unshift($years, $current_year);



switch ($task)
{
	case 'delete_holiday':
		$holidays->delete_holiday($holiday_id);
		$task = "";
	break;


	case 'delete_holidays':
		$holidays->delete_holidays($GO_SECURITY->user_id, $year, $region);
		$task = "";
	break;

	case 'apply_holidays':
		$holidays->add_holidays($GO_SECURITY->user_id, $year, $region);
		$task = "";
	break;

	case 'save_holiday':
		$name = smart_addslashes(trim($_POST['name']));
		if(empty($name))
		{
			$feedback = $error_missing_field;
			$task = "edit_holiday";
		}else
		{
			$date = date_to_unixtime($_POST['date']);
			if($_POST['holiday_id'] > 0)
				$holidays->update_holiday($_POST['holiday_id'] , $date, $name);
			else
				$holidays->add_holiday($GO_SECURITY->user_id, $_POST['region'], $date, $name);
			$task = "";
		}
	break;

}

if(count($regions) == 0)
{
	
	$tabstrip->add_html_element(new html_element('p',$sc_holidays_no_file_prefix.$GO_LANGUAGE->language.$sc_holidays_no_file_suffix));
	$tabstrip->add_html_element(new button($cmdBack, "javascript:document.location='".$return_to."'"));
}else
{
	$form->add_html_element(new input('hidden', 'year', $year));
	$form->add_html_element(new input('hidden', 'region', $region));
	$form->add_html_element(new input('hidden', 'holiday_id', $holiday_id));
	if(isset($feedback))
	{
		$p = new html_element('p',$feedback);
		$p->set_attribute('class','Error');
		$tabstrip->add_html_element($p);
	}
		
	switch ($task)
	{
		case 'edit_holiday':
			
			

		
			$name = isset($_POST['name']) ? smart_stripslashes($_POST['name']) : '';
			if ($holiday_id > 0)
			{
				if($holidays->get_holiday_by_id($holiday_id))
				{
					$date = date($date_format, $holidays->f("date"));
					$name = $holidays->f("name");
				}
			// if $date contains array values obtained by function getdate() -> not compatible with date_picker
			// then redefine the $date variable to be compatible with date_picker control
			}elseif(is_array($date))
			{
				$date = date($date_format);
			}


			
			$table = new table();
			$row = new table_row();
			$row->add_cell(new table_cell($strDate.'*:'));
			$datepicker = new date_picker('date', $date_format, $date);
			$row->add_cell(new table_cell($datepicker->get_html()));
			$table->add_row($row);
			
			$row = new table_row();
			$row->add_cell(new table_cell($strName.'*: '));
			$input = new input('text', 'name',$name);
			$input->set_attribute('maxlength','100');
			$row->add_cell(new table_cell($input->get_html()));
			$table->add_row($row);
			
			$tabstrip->add_html_element($table);
			
			$tabstrip->add_html_element(new button($cmdOk, 'javascript:save_holiday();'));
			$tabstrip->add_html_element(new button($cmdCancel, 'javascript:cancel_holidays();'));

		break;

		case 'generate_holidays':
			$count = $holidays->get_holidays($GO_SECURITY->user_id, $year);
			$holidays->generate_holidays($region, $year);
			
		
			$tabstrip->add_html_element(new html_element('p',$sc_holidays_text2));

			if(is_array($holidays->holidays))
			{
				$table = new table();
				$table->set_attribute('class','go_simple_table');
				$table->add_column(new table_heading($strDate));
				$table->add_column(new table_heading($strName));
				$th = new table_heading('&nbsp;');
				$th->set_attribute('colspan','2');
				$table->add_column($th);				
				foreach($holidays->holidays as $index => $value)
				{
					$row = new table_row();
					$cell=new table_cell(date($date_format, $index));
					$cell->set_attribute('style','width:40%');
					$row->add_cell($cell);
					
					$row->add_cell(new table_cell($value));
					$table->add_row($row);
				}
				$tabstrip->add_html_element($table);

				$tabstrip->add_html_element(new button($cmdApply, 'javascript:apply_holidays('.$count.');'));
				$tabstrip->add_html_element(new button($cmdCancel, 'javascript:cancel_holidays();'));
			}				
		break;

		default:
			$menu = new button_menu();
			$menu->add_button('add',$cmdAdd, 'javascript:edit_holiday(0);');
			$form->add_html_element($menu);

				

				
			$tabstrip->add_html_element(new html_element('p',$sc_holidays_text1));
			
			$table = new table();
			$row = new table_row();
			$row->add_cell(new table_cell($strLocalization.':'));
			
			$select = new select('region', $_SESSION['GO_SESSION']['language']['code']);

			$languages= $GO_LANGUAGE->get_languages();

			while($language_arr = array_shift($languages))
			{
				if(array_key_exists($language_arr['code'], $holidays->in_holidays["fix"]) || 
						array_key_exists($language_arr['code'], $holidays->in_holidays["var"]) ||
						array_key_exists($language_arr['code'], $holidays->in_holidays["spc"]))
				{
					$select->add_value($language_arr['code'], $language_arr['description']);
				}
			}		
			$row->add_cell(new table_cell($select->get_html()));
			$table->add_row($row);	
			
				
			$row = new table_row();
			$row->add_cell(new table_cell($strYear.':'));	
			$select = new select('year',$year);
			$select->set_attribute('onchange','javascript:document.forms[0].submit();');				
			$select->add_arrays($years, $years);				
			$row->add_cell(new table_cell($select->get_html()));
			$table->add_row($row);
			$tabstrip->add_html_element($table);

			$holidays->get_holidays($GO_SECURITY->user_id, $year);

			if ($holidays->num_rows() > 0)
			{
				$table = new table();
				$table->set_attribute('class','go_simple_table');
				$table->add_column(new table_heading($strDate));
				$table->add_column(new table_heading($strName));
				$th = new table_heading('&nbsp;');
				$th->set_attribute('colspan','2');
				$table->add_column($th);				
				
				while($holidays->next_record())
				{
					$row = new table_row();					
					$cell = new table_cell(date($date_format, $holidays->f('date')));
					$cell->set_attribute('width','30%');
					$row->add_cell($cell);
					
					$cell = new table_cell($holidays->f('name'));
					$cell->set_attribute('width','60%');
					$row->add_cell($cell);
					
					$img = new image('edit');
					$img->set_attribute('style','border:0px');					
					$link = new hyperlink('javascript:edit_holiday('.$holidays->f('id').');', $img->get_html(), $strEdit.' \''.htmlspecialchars(addslashes($holidays->f('name'))).'\'');
					
					$row->add_cell(new table_cell($link->get_html()));
					
					$img = new image('delete');
					$img->set_attribute('style','border:0px');					
					$link = new hyperlink('javascript:delete_holiday('.$holidays->f('id').', \''.htmlspecialchars(addslashes($holidays->f('name'))).'\');', 
						$img->get_html(), $strDeleteItem.' \''.htmlspecialchars($holidays->f('name')).'\'');
					
					$row->add_cell(new table_cell($link->get_html()));
					$table->add_row($row);
				}
				$tabstrip->add_html_element($table);
			}else
			{
				$tabstrip->add_html_element(new html_element('p',$strNoHolidays));
			}


			$tabstrip->add_html_element(new button($cmdGenerate, 'javascript:generate_holidays('.$year.');'));
			if($holidays->num_rows() > 0)
			{
				$tabstrip->add_html_element(new button($cmdDelete, 'javascript:delete_holidays('.$year.');'));
			}
			$tabstrip->add_html_element(new button($cmdClose,"javascript:document.location='".$return_to."'"));
		break;
	}
}
