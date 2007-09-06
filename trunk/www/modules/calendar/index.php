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



var CalendarGrid = new Ext.CalendarGrid('CalendarGrid', {columns: ['Ma<br />03-09-2007', 'Di<br />04-09-2007', 'Wo<br />05-09-2007','Do<br />06-09-2007','Vr<br />07-09-2007']});
//var CalendarGrid = new Ext.CalendarGrid('CalendarGrid', {columns: ['Ma', 'Di', 'Wo','Do','Vr']});

Ext.EventManager.onDocumentReady(function(){
	CalendarGrid.render();
	
	CalendarGrid.addEvent('test 1','0');
	CalendarGrid.addEvent('test 2','0');
	//CalendarGrid.calculateappointments(0);
	
	
	CalendarGrid.on("create", function(){
		//alert('create');
	});
	
	CalendarGrid.on("change", function(){
		//alert('change');
	});
});
</script>

</body>
</html>

