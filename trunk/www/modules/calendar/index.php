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
<link href="CalendarGrid.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="language/en.js"></script>
<style>
.x-date-inner th {
    width:30px;
}

.x-date-picker {
border:0px;
}


.calendar-list{
	padding:1px;
}
.calendar-list a{
	border:1px solid white;
	display:block;
	-moz-outline:none;
	margin-bottom:2px;
	text-decoration:none;
}


.calendar-list a:hover, .calendar-list .selected{
	text-decoration: none;
	border:1px solid #c3daf9;
	background-color:#ddecfe;
}

.bluePanel {
	background-color:#D0DEF0;
}

</style>
</head>
<body>
<div id="northDiv">
<div id="toolbar"></div>
</div>
<div id="westDiv">
	<div id="DatePicker"></div>
	<div id="myfeeds-body" class="feed-list"></div>
</div>
<div id="centerDiv">
<div id="CalendarGrid"></div>
</div>


<script type="text/javascript">
<?php
$calendar = $cal->get_calendar();
?>
calendar = function(){


	return {
		init : function(){
		
			
			// initialize state manager, we will use cookies
			//Ext.state.Manager.setProvider(new Ext.state.CookieProvider());

			layout = new Ext.BorderLayout(document.body, {
				north: {
					initialSize:30,
					resizable:false,
					split: false,
					titlebar: false,
					collapsible: false
				},
				west: {
					titlebar: false,
					autoScroll:true,
					closeOnTab: true,
					initialSize: 212,
					split:false
				},
				center: {
					titlebar: false,
					autoScroll:true,
					closeOnTab: true,
					split:true
				}
			});
			
		
			layout.beginUpdate();
			
			var datePicker = new Ext.DatePicker();
			
			datePicker.on("select", function(DatePicker, Date){
					CalendarGrid.gotoDate(Date);
				},this);
			
			datePicker.render("DatePicker");
			
			
			
			var tb = new Ext.Toolbar('toolbar');
			
			
			var toolbarPanel = new Ext.ContentPanel('northDiv',{toolbar: tb});
			layout.add('north', toolbarPanel);

			var navigationPanel = new Ext.ContentPanel('westDiv');
			layout.add('west', navigationPanel);
			
			var centerPanel = new Ext.ContentPanel('centerDiv', { fitToFrame:true, resizeEl: 'CalendarGrid'});

			layout.add('center', centerPanel);
			
			layout.getRegion("west").bodyEl.addClass("bluePanel");

			//layout.restoreState();
			layout.endUpdate();
			
			
		
		
			var CalendarGrid = new Ext.CalendarGrid('CalendarGrid', {calendar_id: <?php echo $calendar['id']; ?>, days: 5});
			
			
			
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
		}
	}

}();

calendar.init();
</script>
</body>
</html>

