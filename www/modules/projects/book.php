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
$GO_MODULES->authenticate('projects');

load_basic_controls();
load_control('date_picker');
load_control('tabtable');
load_control('object_select');

require_once ($GO_LANGUAGE->get_language_file('projects'));

$GO_CONFIG->set_help_url($pm_help_url);

//check for the addressbook module
$ab_module = isset ($GO_MODULES->modules['addressbook']) ? $GO_MODULES->modules['addressbook'] : false;
if ($ab_module && $ab_module['read_permission']) {
	require_once ($ab_module['class_path'].'addressbook.class.inc');
	$ab = new addressbook();
} else {
	$ab_module = false;
}

$page_title = $lang_modules['projects'];
require_once ($GO_MODULES->class_path."projects.class.inc");
$projects = new projects();

$task = isset ($_REQUEST['task']) ? $_REQUEST['task'] : '';
$booking_id = isset($_REQUEST['booking_id']) ? smart_addslashes($_REQUEST['booking_id']) : 0;

$link_back = (isset ($_REQUEST['link_back']) && $_REQUEST['link_back'] != '') ? htmlspecialchars($_REQUEST['link_back']) : $_SERVER['REQUEST_URI'];
$return_to = isset ($_REQUEST['return_to']) ? $_REQUEST['return_to'] : $_SERVER['HTTP_REFERER'];

$book_units = isset($_POST['book_units']) ? $_POST['book_units'] : $GO_CONFIG->get_setting('book_units',$GO_SECURITY->user_id);

switch ($task) {
	case 'save_hours' :
		//$unit_value = isset ($_POST['unit_value']) ? smart_addslashes($_POST['unit_value']) : 0;

		$fee_id = isset ($_POST['fee_id']) ? smart_addslashes($_POST['fee_id']) : 0;

		if(!$fee = $projects->get_fee($fee_id))
		{
			$feedback = '<p class="Error">'.$strDataError.'</p>';
		}else
		{
			$booking['start_time'] = local_to_gmt_time(date_to_unixtime($_POST['book_start_date'].' '.$_POST['start_hour'].':'.$_POST['start_min']));
			//if user gave a number of units calulate ending time
			if (isset($_POST['book_units'])) {
				$GO_CONFIG->save_setting('book_units','1', $GO_SECURITY->user_id);
				
				$booking['units']=smart_addslashes(number_to_phpnumber($_POST['units']));
				$booking['end_time'] = $booking['start_time']+($booking['units']*$fee['time']*60);
				$booking['break_time'] = 0;
				
			} else {
				
				$GO_CONFIG->save_setting('book_units','0',$GO_SECURITY->user_id);
				
				$booking['break_time'] = ($_POST['break_hours'] * 3600) + ($_POST['break_mins'] * 60);
				$booking['end_time'] = local_to_gmt_time(date_to_unixtime($_POST['book_end_date'].' '.$_POST['end_hour'].':'.$_POST['end_min']));
				$booking['units']=0;
			}

			if (!isset($_POST['book_units']) && $booking['end_time'] < $booking['start_time']) {
				$feedback = '<p class="Error">'.$pm_invalid_period.'</p>';

			}elseif(isset($_POST['book_units']) && $booking['units']==0)
			{
				$feedback = '<p class="Error">'.$error_missing_field.'</p>';
			}elseif	($_POST['project_id']['value'] < 1)
			{
				$feedback = '<p class="Error">'.$pm_select_project.'</p>';
			/*}elseif ($booking_id == 0 && $existing_booking_id = $projects->check_hours($_POST['pm_user_id']['value'], $start_time, $end_time)) {

				$link = '<a href="'.$_SERVER['PHP_SELF'].'?booking_id='.$existing_booking_id.'&return_to='.urlencode($return_to).'">'.$pm_here.'</a>';

				$feedback = '<p class="Error">'.sprintf($pm_already_booked, $link).'</p>';*/
			} else {

				$booking['project_id']=smart_addslashes($_REQUEST['project_id']);
				$booking['comments']=smart_addslashes($_POST['book_comments']);
				$booking['fee_id']=$fee_id;
				$booking['user_id']=$_REQUEST['pm_user_id']['value'];
				$booking['int_fee_value']=$fee['internal_value'];
				$booking['ext_fee_value']=$fee['external_value'];
				$booking['fee_time']=$fee['time'];
				
				$project = $projects->get_project($booking['project_id']);
				if($project['calendar_id']>0)
				{
					if($booking_id>0)
					{
						$old_booking = $projects->get_booking($booking_id);
						$booking['event_id']=$old_booking['event_id'];
					}
					$booking['event_id'] = $projects->add_booking_to_calendar($booking, $project['calendar_id']);
				}
						
			
				if($booking_id > 0)
				{
					$booking['id']=$booking_id;
					
					
					if (!$projects->update_booking($booking)) {
						$feedback = '<p class="Error">'.$strSaveError.'</p>';
					} else {
						
						
						$feedback = '<p class="Success">'.$pm_add_hours_success.'</p>';
						if ($_POST['close'] == 'true') {
							header('Location: '.$return_to);
							exit ();
						}
					}
				}else
				{
					if (!$booking_id = $projects->add_booking($booking)) {

						$feedback = '<p class="Error">'.$strSaveError.'</p>';
					} else {
						$feedback = '<p class="Success">'.$pm_add_hours_success.'</p>';
						if ($_POST['close'] == 'true') {
							header('Location: '.$return_to);
							exit ();
						}
					}
				}				
			}
		}
		break;

	case 'stop_timer' :
		$timer = $projects->get_timer($GO_SECURITY->user_id);
		$timer_start_time = $timer['start_time'] + (get_timezone_offset($timer['start_time']) * 3600);
		$timer_end_time = get_time();

		$projects->stop_timer($GO_SECURITY->user_id);

		//$projects->set_registration_method($GO_SECURITY->user_id, 'endtime');

		$active_tab = 'book';
		break;
}
$pm_settings = $projects->get_settings($GO_SECURITY->user_id);

$GO_HEADER['head'] = date_picker::get_header();
$GO_HEADER['body_arguments'] = 'onload="javascript:toggle_time_units('.$book_units.');"';

$page_title = $lang_modules['projects'];
require_once ($GO_THEME->theme_path."header.inc");
echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'" name="projects_form">';
echo '<input type="hidden" name="close" value="false" />';
//echo '<input type="hidden" name="project_id" value="'.$project_id.'" />';
echo '<input type="hidden" name="task" value="" />';
echo '<input type="hidden" name="return_to" value="'.htmlspecialchars($return_to).'" />';
echo '<input type="hidden" name="link_back" value="'.$link_back.'" />';


$time = get_time();
$day = date("j", $time);
$year = date("Y", $time);
$month = date("m", $time);

$date = date($_SESSION['GO_SESSION']['date_format'], $time);

$timer_start_date = isset($timer_start_time) ? date($_SESSION['GO_SESSION']['date_format'], $timer_start_time) : $date;
if($booking_id > 0 && $booking = $projects->get_booking($booking_id))
{
	if(!$GO_MODULES->write_permission && $booking['user_id'] != $GO_SECURITY->user_id)
	{
		require($GO_CONFIG->root_path.'error_docs/403.inc');
		require_once ($GO_THEME->theme_path."footer.inc");
		exit();
	}
	$title = $pm_edit_data;
	$pm_user_id = $booking['user_id'];
	$project_id = $booking['project_id'];
	$local_start_time = $booking['start_time']+(get_timezone_offset($booking['start_time'])*3600);
	$local_end_time = $booking['end_time']+(get_timezone_offset($booking['start_time'])*3600);

	$break_hours = gmdate('G', gmmktime(0,0,$booking['break_time']));
	$break_mins = gmdate('i', gmmktime(0,0,$booking['break_time']));
	$units = $booking['units'];
	
	if($units>0)
	{
		$book_units='1';
	}


	$book_start_date = date($_SESSION['GO_SESSION']['date_format'], $local_start_time);
	$start_hour = date('G',$local_start_time);
	$start_min = date('i',$local_start_time);

	$book_end_date = date($_SESSION['GO_SESSION']['date_format'], $local_end_time);
	$end_hour = date('G',$local_end_time);
	$end_min =date('i',$local_end_time);

	$book_comments = $booking['comments'];
	$fee_id = $booking['fee_id'];


}else
{
	$title = $pm_enter_data;

	$project_id = $_REQUEST['project_id'];

	$pm_user_id = isset($_REQUEST['pm_user_id']['value']) ? $_REQUEST['pm_user_id']['value'] : $GO_SECURITY->user_id;
	$book_start_date = isset($_POST['book_start_date']) ? $_POST['book_start_date'] : $timer_start_date;

	$hour = isset($timer_start_time) ? date('G', $timer_start_time) : 8;
	$min = isset($timer_start_time) ? date('i', $timer_start_time) : 0;
	$start_hour = isset($_POST['start_hour']) ? $_POST['start_hour'] : $hour;
	$start_min = isset($_POST['start_min']) ? $_POST['start_min'] : $min;

	$timer_end_date = isset($timer_end_time) ? date($_SESSION['GO_SESSION']['date_format'], $timer_end_time) : $date;
	$book_end_date = isset($_POST['book_end_date']) ? $_POST['book_end_date'] : $timer_end_date;

	$hour = isset($timer_end_time) ? date('G', $timer_end_time) : 17;
	$end_hour = isset($_POST['end_hour']) ? $_POST['end_hour'] : $hour;

	$min = isset($timer_end_time) ? date('i', $timer_end_time) : 0;
	$end_min = isset($_POST['end_min']) ? $_POST['end_min'] : $min;

	//$timer_units = ceil((($end_hour*60+$end_min)-($start_hour*60+$start_min))/60);

	$units = isset($_POST['units']) ? number_to_phpnumber($_POST['units']) : 0;

	$break_hours = isset($_POST['break_hours']) ? $_POST['break_hours'] : 0;
	$break_mins = isset($_POST['break_mins']) ? $_POST['break_mins'] : 0;

	$book_comments =  isset($_POST['book_comments']) ? smart_addslashes($_POST['book_comments']) : '';

	$fee_id = isset($_POST['fee_id']) ? $_POST['fee_id'] : $pm_settings['fee_id'];
}


$project = $projects->get_project($project_id);

$title .= '('.$project['name'].')';
/*
if (isset($_REQUEST['delete_hours']))
{
$projects->delete_hours($_REQUEST['delete_hours']);
}*/



$tabtable = new tabtable('book_tab', $title, '100%', '400', '120');
$tabtable->print_head(htmlspecialchars($return_to));
?>
<input type="hidden" name="booking_id" value="<?php echo $booking_id; ?>" />
<input type="hidden" name="post_action" />
<table border="0" cellpadding="0" cellspacing="0">
<tr>
	<td class="TableInside" valign="top">
	<?php
	if (isset($feedback)) echo $feedback;

	if ($timer = $projects->get_timer($GO_SECURITY->user_id))
	{
		echo 	'<table border="0" class="alert"><tr><td><img src="'.$GO_THEME->images['alert'].'" border="0" /></td>'.
		'<td><a class="normal" href="'.$_SERVER['PHP_SELF'].'?project_id='.$timer['project_id'].'&task=stop_timer">'.$pm_clocked_in.'</a></td></tr></table>';
	}
	?>
	<table border="0"width="100%">
	<?php	
	/*$select = new object_select('project', 'projects_form', 'project_id', $project_id);
	echo '<tr><td>';
	echo $select->get_link($strProject);
	echo ':</td><td>';
	echo $select->get_field();
	echo '</td></tr>';*/

	$input = new input('hidden','project_id', $project_id);
	echo $input->get_html();



	$fees = array();
	$fee_count = $projects->get_authorized_fees($GO_SECURITY->user_id);

	switch($fee_count)
	{
		case '0':
			echo '<tr><td>'.$pm_no_fees.'</td></tr>';
			echo '</table>';
			require_once ($GO_THEME->theme_path."footer.inc");
			exit();
			break;

		case '1':
			$projects->next_record();
			$input = new input('hidden', 'fee_id', $projects->f('id'));
			echo $input->get_html();

			echo '<tr><td>'.$pm_fee.':</td><td>';
			echo $projects->f('name');
			echo '</td></tr>';

			break;

		default:

			$select = new select('fee_id', $fee_id);
			while($projects->next_record())
			{
				$select->add_value($projects->f('id'), $projects->f('name'));
			}
			echo '<tr><td>'.$pm_fee.':</td><td>';
			echo $select->get_html();
			echo '</td></tr>';
			break;
	}

	if ($GO_MODULES->write_permission)
	{
		$select = new object_select('user', 'projects_form', 'pm_user_id', $pm_user_id);
		echo '<tr><td>';
		echo $select->get_link($pm_employee);
		echo ':</td><td>';
		echo $select->get_field();
		echo '</td></tr>';
	}else
	{
		echo '<input type="hidden" name="pm_user_id[value]" value="'.$GO_SECURITY->user_id.'" />';
	}
	?>
	<tr>
	<td colspan="2">
	<?php
	$checkbox = new checkbox('book_units','book_units','1',$pm_book_units,$book_units=='1');
	$checkbox->set_attribute('onclick','javascript:toggle_time_units(this.checked);');
	echo $checkbox->get_html();
	?>	
	</td>
	</tr>
	<tr id="starttime_row">
		<td><?php echo $pm_starttime; ?>:</td>
		<td>
		<?php
		$onchange =  ($book_units!='1') ? 'onchange="javascript:check_date(this.name);"' : '';
		$datepicker= new date_picker('book_start_date', $_SESSION['GO_SESSION']['date_format'], $book_start_date, '', '',$onchange);
		echo $datepicker->get_html();
		echo '&nbsp;';
		$hours = array("00","01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23", "24");
		for ($i=0;$i<=60;$i++)
		{
			$text = strlen($i) < 2 ? '0'.$i : $i;
			$mins[] = $text;
		}

		$select = new select("start_hour", $start_hour);
		if($book_units!='1')
		{
			$select->set_attribute('onchange','javascript:update_end_hour();');
		}
		$select->add_arrays($hours, $hours);
		echo $select->get_html();


		echo '&nbsp;:&nbsp;';

		$select = new select("start_min", $start_min);
		if($book_units!='1')
		{
			$select->set_attribute('onchange','javascript:document.projects_form.end_min.value=this.value;');
		}
		$select->add_arrays($mins, $mins);
		echo $select->get_html();
		?>
		</td>
	</tr>

	<tr id="endtime_row">
		<td><?php echo $pm_endtime; ?>:</td>
		<td>
		<?php
		$datepicker= new date_picker('book_end_date', $_SESSION['GO_SESSION']['date_format'], $book_end_date, '', '', 'onchange="javascript:check_date(this.name);"');
		echo $datepicker->get_html();
		echo '&nbsp;';

		$select = new select("end_hour", $end_hour);
		$select->add_arrays($hours, $hours);
		echo $select->get_html();

		echo '&nbsp;:&nbsp;';

		$select = new select("end_min", $end_min);
		$select->add_arrays($mins, $mins);
		echo $select->get_html();
		?>
		</td>
	</tr>
	<tr id="breaktime_row">
		<td><?php echo $pm_breaktime; ?>:</td>
		<td>
		<?php	
		$select = new select("break_hours", $break_hours);
		$select->add_arrays($hours, $hours);
		echo $select->get_html();

		echo '&nbsp;:&nbsp;';

		$select = new select("break_mins", $break_mins);
		$select->add_arrays($mins, $mins);
		echo $select->get_html();
		?>
		</td>
	</tr>

	<tr id="units_row">
	<td>
		<?php echo $pm_units; ?>:
		</td>
		<td>
		<?php
		$input = new input('text','units', format_number($units));
		$input->set_attribute('maxlength','50');
		$input->set_attribute('style','text-align:right;width:50px');
		$input->set_attribute('onfocus','this.select();');
		$input->set_attribute('onblur', "javascript:this.value=number_format(this.value, 2, '".$_SESSION['GO_SESSION']['decimal_seperator']."', '".$_SESSION['GO_SESSION']['thousands_seperator']."');");
		echo $input->get_html();
		?>
		</td>
	</tr>
	
	
		
	<tr><td colspan="3">&nbsp;</td></tr>
	<tr>
		<td valign="top"><?php echo $strComments; ?>:</td>
		<td>
		<textarea class="textbox" name="book_comments" cols="40" rows="4"><?php echo htmlspecialchars($book_comments, ENT_QUOTES); ?></textarea>
		</td>
	</tr>
	<?php
	echo '<tr><td colspan="4">';
	$button = new button($cmdOk,"javascript:_save('save_hours', 'true')");
	echo $button->get_html();
	$button = new button($cmdApply,"javascript:_save('save_hours', 'false')");
	echo $button->get_html();
	$button = new button($cmdClose, "javascript:document.location='".htmlspecialchars($return_to)."';");
	echo $button->get_html();
	?>
	</table>
	</td>
</tr>
</table>
<?php
$tabtable->print_foot();
echo '</form>';
?>
<script type="text/javascript">

function toggle_time_units(units)
{
	var visible = document.all ? 'block' : 'table-row';
	if(units)
	{
		document.getElementById('endtime_row').style.display='none';
		document.getElementById('breaktime_row').style.display='none';
		document.getElementById('units_row').style.display=visible;
	}else
	{
		document.getElementById('endtime_row').style.display=visible;
		document.getElementById('breaktime_row').style.display=visible;
		document.getElementById('units_row').style.display='none';
	}
}

function check_date(changed_field)
{
	start_date = get_date(document.projects_form.book_start_date.value, '<?php echo $_SESSION['GO_SESSION']['date_format']; ?>', '<?php echo $_SESSION['GO_SESSION']['date_seperator']; ?>');
	end_date = get_date(document.projects_form.book_end_date.value, '<?php echo $_SESSION['GO_SESSION']['date_format']; ?>','<?php echo $_SESSION['GO_SESSION']['date_seperator']; ?>');

	if(end_date < start_date)
	{
		if(changed_field == 'book_start_date')
		{
			document.projects_form.book_end_date.value = document.projects_form.book_start_date.value;
		}else
		{
			document.projects_form.book_start_date.value = document.projects_form.book_end_date.value;
		}
	}
}

function update_end_hour()
{
	var start_hour = parseInt(document.projects_form.start_hour.value);
	var end_hour = parseInt(document.projects_form.end_hour.value);
	if (start_hour == 23)
	{
		document.projects_form.end_hour.value='23';
		document.projects_form.end_min.value='30';
	}else
	{
		if (start_hour >= end_hour)
		{
			end_hour = start_hour+1;
			document.projects_form.end_hour.value=end_hour;
		}
	}
}
function _save(task, close)
{
	document.projects_form.task.value = task;
	document.projects_form.close.value = close;
	document.projects_form.submit();
}
</script>
<?php
require_once ($GO_THEME->theme_path."footer.inc");
