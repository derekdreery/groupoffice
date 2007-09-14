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

$GO_CONFIG->set_help_url($cal_help_url);


require_once($GO_MODULES->class_path.'calendar.class.inc');
$cal = new calendar();
 
load_basic_controls();


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>Group-Office Calendar</title>
<?php
//$GO_THEME->load_module_theme('email');
require($GO_CONFIG->root_path.'default_head.inc');
require($GO_CONFIG->root_path.'default_scripts.inc');
echo $GO_THEME->get_stylesheet('calendar');
?>
<link href="CalendarGrid.css" type="text/css" rel="stylesheet" />

<script type="text/javascript" src="language/en.js"></script>
<script src="../../controls/selectLink.js" type="text/javascript"></script>
<script src="CalendarGrid.js" type="text/javascript"></script>
<script src="EventDialog.js" type="text/javascript"></script>
<script src="calendar.js" type="text/javascript"></script> 

<!--

 <script type="text/javascript">
Ext.EventManager.onDocumentReady(function(){
	var eventDialog = new EventDialog();
	eventDialog.show(1);
	//eventDialog.setCurrentDate();
	
	},EventDialog);
</script> -->

</head>
<body>
<div id="northDiv">
<div id="toolbar"></div>
</div>
<div id="westDiv">
	<div id="DatePicker"></div>
	<div id="calendarList" class="calendar-list"></div>
</div>
<div id="centerDiv">
<div id="CalendarGrid"></div>
</div>

<form id="event-form" name="event-form" method="post" action="#">
<div id="event-dialog" style="visibility:hidden">
	<div id="event-properties">
		<div class="inner-tab">
			<table>
				<tr>
					<td>
					<?php echo $cal_subject; ?>:
					</td>
					<td class="x-form-element"><input type="text" id="subject" name="subject" /></td>
				</tr>
				<tr>
					<td>
					<?php echo $sc_location; ?>:
					</td>
					<td class="x-form-element"><input type="text" id="location" name="location" /></td>
				</tr>				
				<tr>
					<td>
					<?php echo $strCreateLink; ?>:
					</td>
					<td class="x-form-element"><input type="text" id="link_name" name="link_name" /></td>
				</tr>
				<tr>
					<td style="vertical-align:top">
					<?php echo $strDescription; ?>:
					</td>
					<td class="x-form-element"><textarea id="description" name="description"></textarea></td>
				</tr>
				<tr>
					<td><?php echo $sc_start_at; ?>:</td>
					<td class="x-form-element">
					<table class="subTable">
					<tr>
						<td><input type="text" id="start_date" name="start_date" /></td>
						<td><input type="text" id="start_hour" name="start_hour" /></td>
						<td><input type="text" id="start_min" name="start_min" /></td>
						<td></td>
					</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td><?php echo $sc_end_at; ?>:</td>
					<td class="x-form-element">
					<table class="subTable">
					<tr>
						<td><input type="text" id="end_date" name="end_date" /></td>
						<td><input type="text" id="end_hour" name="end_hour" /></td>
						<td><input type="text" id="end_min" name="end_min" /></td>
						<td><input type="checkbox" id="all_day_event" name="all_day_event" /></td>
					</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td>
					<?php echo $sc_status; ?>:
					</td>
					<td class="x-form-element">
					<table class="subTable">
					<tr>
						<td>
						<?php
						$select = new select('status_id');
						$select->set_attribute('id', 'status_id');
						$cal->get_statuses('VEVENT');
						while($cal->next_record())
						{
							$select->add_value($cal->f('id'), $cal_statuses[$cal->f('name')]);
						}			
						echo $select->get_html();		
						?>
						</td>
						<td colspan="2">
						<input type="checkbox" id="busy" name="busy" />
						</td>
					</tr>
					</table>
					</td>
				</tr>
			</table>
		</div>	
	</div>
	
	
	
	<div id="event-recurrence">
		<div class="inner-tab">
			<table>
				<tr>
					<td>
					<?php echo $sc_recur_every; ?>:
					</td>
					<td class="x-form-element">
					<table class="subTable">
					<tr>
						<td>
						<?php
						$select = new select('repeat_every', '1');
						for ($i = 1; $i < 13; $i ++) {
							$select->add_value($i, $i);
						}
						$select->print_html();
						?>
						</td>
						<td>
						<?php					
						$select = new select('repeat_type','0');					
						$select->add_value('0', $sc_types1[REPEAT_NONE]);
						$select->add_value('1', $sc_types1[REPEAT_DAILY]);
						$select->add_value('2', $sc_types1[REPEAT_WEEKLY]);
						$select->add_value('3', $sc_types1[REPEAT_MONTH_DATE]);
						$select->add_value('4', $sc_types1[REPEAT_MONTH_DAY]);
						$select->add_value('5', $sc_types1[REPEAT_YEARLY]);
						$select->print_html();
						?>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td>
					<?php echo $sc_at_days; ?>:
					</td>
					<td class="x-form-element">
					<table class="subTable">
					<tr>
						<td>					
						<?php
						$select = new select("month_time", '1');
						$select->add_arrays(array (1, 2, 3, 4), $month_times);
						$select->print_html();					
						?>
						</td>
						<td>
						<?php
						$day_number = $_SESSION['GO_SESSION']['first_weekday'];
						
						for ($i = 0; $i < 7; $i ++) {
							if ($day_number == 7)
							$day_number = 0;
							
							$input = new input('checkbox', 'repeat_days_'.$day_number,'1');
							$input->set_attribute('id','repeat_days_'.$day_number);
							
							echo '<td>';
							$input->print_html();
							echo '</td>';
							
							$day_number ++;
						}
						?>						
					</tr>
					</table>					
					</td>
				</tr>
				<tr>
					<td><?php echo $sc_cycle_end; ?></td>
					<td class="x-form-element">
					<table class="subTable">
					<tr>
						<td>	
						<input type="text" id="repeat_end_date" name="repeat_end_date" />
						</td>
						<td>
						<input type="checkbox" name="repeat_forever" id="repeat_forever" />
						</td>
					</tr>
					</table>
					</td>
				</tr>
				
			</table>
		</div>	
	</div>
	
	
	<div id="event-options">
		<div class="inner-tab">
			<table>
				<tr>
					<td>Calendar:</td>
					<td>
					<?php
					$select = new select('calendar_id');
					$cal->get_writable_calendars($GO_SECURITY->user_id);
					while($cal->next_record())
					{
						$select->add_value($cal->f('id'), $cal->f('name'));
					}
					echo $select->get_html();
					
					?>
					</td>
				</tr>
				<tr>
					<td>
					<?php echo $sc_reminder; ?>:
					</td>
					<td>
					<table class="subTable">
					<tr>
						<td>					
						<?php
						$select = new select('reminder_value', '0');
						$select->add_value('0', $cal_no_reminder);
						for ($i = 1; $i < 60; $i ++) {
							$select->add_value($i, $i);
						}
										
						$select->print_html();					
						?>
						</td>
						<td>
						<?php
						$select = new select('reminder_multiplier', '60');
						$select->add_value('60', $sc_mins);
						$select->add_value('3600', $sc_hours);
						$select->add_value('86400', $sc_days);
						$select->add_value('604800', $sc_weeks);
						$select->print_html();	
						?>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td style="vertical-align:top">
					<?php echo $sc_background; ?>:
					</td>
					<td id="colorSelector">
					</td>
				</tr>
				
			</table>			
		</div>
	</div>

</div>
</form>
<!-- 
<div id="test1" style="border: 1px solid black;width:100 height:100">

<div id="test2" style="border: 1px solid red; width:50px;height:50px;margin-top:20px"></div>
</div>

<script>
Ext.get("test1").on("mousedown",function(){alert('test1 mousedown');});
Ext.get("test1").on("dblclick",function(){alert('test1');});
Ext.get("test2").on("dblclick",function(){alert('test2');});
Ext.get("test2").on("mousedown",function(){alert('test2 mousedown');});
</script>
-->
</body>
</html>

