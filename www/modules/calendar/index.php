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
 
 <script>
Ext.EventManager.onDocumentReady(function(){
	var eventDialog = new EventDialog();
	eventDialog.init();
	eventDialog.eventForm.setValues({subject:'test',description:'description'});
	},EventDialog);
</script>
 
 -->
 
<script src="CalendarGrid.js" type="text/javascript"></script>
<script src="calendar.js" type="text/javascript"></script>
<link href="CalendarGrid.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="language/en.js"></script>
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
<div id="event-dialog">
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
					<?php echo $strDescription; ?>:
					</td>
					<td class="x-form-element"><input type="text" id="description" name="description" /></td>
				</tr>
				<tr>
					<td><?php echo $sc_start_at; ?>:</td>
					<td class="x-form-element">
					<table border="0" cellpadding="0" cellspacing="0">
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
					<table border="0" cellpadding="0" cellspacing="0">
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
			</table>
		</div>
	</div>
</div>
</form>

</body>
</html>

