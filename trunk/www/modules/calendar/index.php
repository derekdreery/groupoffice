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


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php
//$GO_THEME->load_module_theme('email');
require($GO_CONFIG->root_path.'default_head.inc');
require($GO_CONFIG->root_path.'default_scripts.inc');
echo $GO_THEME->get_stylesheet('calendar');
?>
<script src="EventDialog.js" type="text/javascript"></script>
<!-- 
<script src="CalendarGrid.js" type="text/javascript"></script>
<script src="calendar.js" type="text/javascript"></script> 
 

 
 -->

<link href="CalendarGrid.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="language/en.js"></script>
 <script>
Ext.EventManager.onDocumentReady(function(){
	var eventDialog = new EventDialog();
	eventDialog.show();
	eventDialog.setCurrentDate();
	
	},EventDialog);
</script>
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

<form id="event-form" name="event-form" method="post">
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
						<td><input type="text" id="startDate" name="startDate" /></td>
						<td><input type="text" id="startHour" name="startHour" /></td>
						<td><input type="text" id="startMinute" name="startMinute" /></td>
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
						<td><input type="text" id="endDate" name="endDate" /></td>
						<td><input type="text" id="endHour" name="endHour" /></td>
						<td><input type="text" id="endMinute" name="endMinute" /></td>
						<td><input type="checkbox" id="allDay" name="allDay" /></td>
					</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td>Calendar:</td>
					<td>
					<?php
					load_basic_controls();
					$select = new select('calendarId');
					$cal->get_writable_calendars($GO_SECURITY->user_id);
					while($cal->next_record())
					{
						$select->add_value($cal->f('id'), $cal->f('name'));
					}
					echo $select->get_html();
					
					?>
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
						</td>
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
</div>
</form>

</body>
</html>

