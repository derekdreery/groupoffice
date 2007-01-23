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
$GO_MODULES->authenticate('timeregistration');

load_basic_controls();
load_control('date_picker');
load_control('tabtable');
load_control('object_select');

require_once ($GO_LANGUAGE->get_language_file('projects'));

//check for the addressbook module
$ab_module = isset ($GO_MODULES->modules['addressbook']) ? $GO_MODULES->modules['addressbook'] : false;
if ($ab_module && $ab_module['read_permission']) {
	require_once ($ab_module['class_path'].'addressbook.class.inc');
	$ab = new addressbook();
} else {
	$ab_module = false;
}

require_once ($GO_MODULES->class_path."timeregistration.class.inc");
$projects = new timeregistration();

$task = isset ($_REQUEST['task']) ? $_REQUEST['task'] : '';
$booking_id = isset($_REQUEST['booking_id']) ? smart_addslashes($_REQUEST['booking_id']) : 0;

$link_back = (isset ($_REQUEST['link_back']) && $_REQUEST['link_back'] != '') ? htmlspecialchars($_REQUEST['link_back']) : $_SERVER['REQUEST_URI'];
$return_to = isset ($_REQUEST['return_to']) ? $_REQUEST['return_to'] : $_SERVER['HTTP_REFERER'];

switch ($task) {
	case 'save_hours' :


			$booking['start_time'] = local_to_gmt_time(date_to_unixtime($_POST['book_start_date']));
			$booking['end_time'] = local_to_gmt_time(date_add(date_to_unixtime($_POST['book_end_date']),1));
			$booking['user_id']=$_POST['pm_user_id']['value'];

			if ($booking['end_time'] < $booking['start_time']) {
				$feedback = '<p class="Error">'.$pm_invalid_period.'</p>';

			}elseif	($_POST['project_id']['value'] < 1)
			{
				$feedback = '<p class="Error">'.$pm_select_project.'</p>';
			} else {

				$booking['project_id']=smart_addslashes($_REQUEST['project_id']);
				$booking['comments']=smart_addslashes($_POST['book_comments']);
				
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
	$local_end_time = $booking['end_time']+(get_timezone_offset($booking['start_time'])*3600)-1;



	$book_start_date = date($_SESSION['GO_SESSION']['date_format'], $local_start_time);


	$book_end_date = date($_SESSION['GO_SESSION']['date_format'], $local_end_time);

	$book_comments = $booking['comments'];


}else
{
	$title = $pm_enter_data;

	$project_id = $_REQUEST['project_id'];

	$pm_user_id = isset($_REQUEST['pm_user_id']['value']) ? $_REQUEST['pm_user_id']['value'] : $GO_SECURITY->user_id;
	$book_start_date = isset($_POST['book_start_date']) ? $_POST['book_start_date'] : $timer_start_date;

	
	$timer_end_date = isset($timer_end_time) ? date($_SESSION['GO_SESSION']['date_format'], $timer_end_time) : $date;
	$book_end_date = isset($_POST['book_end_date']) ? $_POST['book_end_date'] : $timer_end_date;

	$book_comments =  isset($_POST['book_comments']) ? smart_addslashes($_POST['book_comments']) : '';

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
	<tr id="starttime_row">
		<td><?php echo $pm_starttime; ?>:</td>
		<td>
		<?php
		$datepicker= new date_picker('book_start_date', $_SESSION['GO_SESSION']['date_format'], $book_start_date, '', '');
		echo $datepicker->get_html();
		?>
		</td>
	</tr>

	<tr id="endtime_row">
		<td><?php echo $pm_endtime; ?>:</td>
		<td>
		<?php
		$datepicker= new date_picker('book_end_date', $_SESSION['GO_SESSION']['date_format'], $book_end_date, '', '', 'onchange="javascript:check_date(this.name);"');
		echo $datepicker->get_html();		
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
