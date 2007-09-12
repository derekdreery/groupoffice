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
 //require_once($GO_LANGUAGE->get_language_file('calendar'));

 //$GO_CONFIG->set_help_url($cal_help_url);


 /*require_once($GO_MODULES->class_path.'calendar.class.inc');
 $cal = new calendar();*/


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
<!-- <script src="CalendarGrid.js" type="text/javascript"></script>
 <script src="calendar.js" type="text/javascript"></script> -->
<script>
Ext.EventManager.onDocumentReady(function(){
	var eventDialog = new EventDialog();
	eventDialog.init();
	},EventDialog);
</script>
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
		<table>
			<tr>
				<td>
				Subject:
				</td>
				<td id="subject-field" class="x-form-element"></td>
			</tr>
			<tr>
				<td>
				Description:
				</td>
				<td id="description-field" class="x-form-element"></td>
			</tr>
			<tr>
				<td>Starts at:</td>
				<td class="x-form-element">
				<table border="0" cellpadding="0" cellspacig="0">
				<tr>
					<td id="start-date-field"></td>
					<td id="start-hour-field"></td>
					<td id="start-minute-field"></td>
					<td></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td>Ends at:</td>
				<td class="x-form-element">
				<table border="0" cellpadding="0" cellspacig="0">
				<tr>
					<td id="end-date-field"></td>
					<td id="end-hour-field"></td>
					<td id="end-minute-field"></td>
					<td id="all-day-field"></td>
				</tr>
				</table>
				</td>
			</tr>
		</table>
	</div>
</div>
</form>

</body>
</html>

