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
<script src="CalendarGrid.js" type="text/javascript"></script>
<script type="text/javascript" src="language/en.js"></script>

<style>
#calendar-grid{
width:100%;
}

.evenRow{
	border-top:1px solid #DDDDDD;
	border-left:3px double #DDDDDD;
	height:20px;
}
.unevenRow{
	border-top:1px dotted #DDDDDD;
	border-left:3px double #DDDDDD;
	height:20px;
}
.timeHead{
	background-color:#f1f1f1;
	height:41px;
	width:100%;
	border-top:1px solid #DDDDDD;
	text-align:right;
}

.selector{
	background-color:#ffffcc;
	position:absolute;
	visibility:hidden;
	z-index:1;
	opacity: 0.4;
}

.event-container {
padding:2px;
	position:absolute;
	background-color:#ffffcc;
	border:1px solid #666666;
	color:000;
	z-index: 100;#higher then selector!
	
}

.grid-container{
	overflow:auto;
	position:relative;
}

.headings-container{
	height:40px;
	vertical-align:middle;
	text-align:center;
	padding-left:40px;
	background-image:url(<?php echo $GO_CONFIG->host; ?>ext/resources/images/default/layout/panel-title-light-bg.gif);
	border-bottom:1px solid #98C0F4;
	color:black;
}

.all-day-container{
	height:20px;
	padding-left:40px;
	background-color:#f1f1f1;
}

.all-day-column {
	height:100%;
	z-index:1;
}

.heading{
	margin-top:8px;
}

.event{
background-color:#ffccc;

width:100%;
height:100%;
}
</style>
</head>
<body>
<div id="CalendarGrid" style="width:1000px;height:600px;position:relative;">
</div>


<script type="text/javascript">
<?php

$calendar = $cal->get_calendar();

//determine start en end of the week
$days=5;
$weekday = date("w");
$day = date("j");
$year = date('Y');
$month= date('m');

$tmpday = $day - $weekday + $_SESSION['GO_SESSION']['first_weekday'];
if ($tmpday > $day)
{
	$tmpday = $tmpday -7;
}
$start_time = mktime(0,0,0,$month, $tmpday, $year);
$end_time = date_add($start_time, $days);
?>


	var dt = Date.parseDate("<?php echo date("Y-m-d", $start_time); ?>", "Y-m-d");
	
	var CalendarGrid = new Ext.CalendarGrid('CalendarGrid', {calendar_id: <?php echo $calendar['id']; ?>, startDate: dt, days: 5});
	CalendarGrid.render();
	CalendarGrid.load();
	

CalendarGrid.on("create", function(CalGrid, newEventEl, newEventName){
		CalendarGrid.mask();

		var event = CalGrid.elementToEvent(newEventEl);

		var conn = new Ext.data.Connection();
			conn.request({
			url: 'action.php',
			params: {task: 'add_event', calendar_id: CalGrid.calendar_id,  'name': newEventName, 'gridEvent': Ext.encode(event)},
			callback: function(options, success, response)
			{
				var response = Ext.decode(response.responseText);
				if(!success)
				{				
					Ext.MessageBox.alert('Failed', response['errors']);
				}else
				{
					CalendarGrid.registerEventId(newEventEl,response['event_id']);
				}
				CalendarGrid.unmask();
			},
			scope: CalendarGrid
		});
		
	});
	
CalendarGrid.on("move", function(CalGrid, newEventEl, newEventName){
		CalendarGrid.mask();

		var event = CalGrid.elementToEvent(newEventEl);

		var conn = new Ext.data.Connection();
			conn.request({
			url: 'action.php',
			params: {task: 'update_event', event_id: event['remoteId'], 'startDate': event['startDate']},
			callback: function(options, success, response)
			{
				var response = Ext.decode(response.responseText);
				if(!success)
				{				
					Ext.MessageBox.alert('Failed', response['errors']);
				}
				CalendarGrid.unmask();
			},
			scope: CalendarGrid
		});
		
	});
	
CalendarGrid.on("resize", function(CalGrid, newEventEl, newEventName){
		CalendarGrid.mask();

		var event = CalGrid.elementToEvent(newEventEl);

		var conn = new Ext.data.Connection();
			conn.request({
			url: 'action.php',
			params: {task: 'update_event', event_id: event['remoteId'], 'endDate': event['endDate']},
			callback: function(options, success, response)
			{
				var response = Ext.decode(response.responseText);
				if(!success)
				{				
					Ext.MessageBox.alert('Failed', response['errors']);
				}
				CalendarGrid.unmask();
			},
			scope: CalendarGrid
		});
		
	});

/*
Ext.EventManager.onDocumentReady(function(){
	
	
	CalendarGrid.addEvent('test 1','0');
	CalendarGrid.addEvent('test 2','0');
	//CalendarGrid.calculateappointments(0);
	
	
	CalendarGrid.on("create", function(){
		//alert('create');
	});
	
	CalendarGrid.on("change", function(){
		//alert('change');
	});
});*/
</script>

<input type="button" onclick="CalendarGrid.next(7);" value="Next week" />



</body>
</html>

