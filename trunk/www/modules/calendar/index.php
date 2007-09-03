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
	z-index: 2;#higher then selector!
	
}

.event{
background-color:#ffccc;

width:100%;
height:100%;
}
</style>
</head>
<body>


<div id="CalendarGrid" style="width:50%;position:absolute; left:100px;top:50px;border:1px solid black">
</div>

<script type="text/javascript">

Ext.CalendarGrid = function(container, config){ 

	Ext.apply(this, config);
	
	if(!this.days)
	{
		this.days=1;
	}
	
	if(!this.container)
	{
		this.container = Ext.get(container);		
	}
	
	this.container.unselectable();
	
	this.dragEvent=false;
	
	this.appointments=Array();
	
	
	
	
	this.addEvents({
        /**
	     * @event click
	     * Fires when this button is clicked
	     * @param {Button} this
	     * @param {EventObject} e The click event
	     */
	    "create" : true,
        /**
	     * @event toggle
	     * Fires when the "pressed" state of this button changes (only if enableToggle = true)
	     * @param {Button} this
	     * @param {Boolean} pressed
	     */
	    "change" : true

    });
	
	
	
	
	Ext.CalendarGrid.superclass.constructor.call(this);
}
	
Ext.extend(Ext.CalendarGrid, Ext.util.Observable, {


	//build the html grid
	render : function (){
	
		var t = new Ext.Template(
	    	'<table cellpadding="0" cellspacing="0" border="0" style="table-layout:fixed;width:100%">'+
	    	'<tbody><tr><td id="{timeHeadsID}" style="width:40px"></td>'+
	    	'<td id="{columnsContainerID}"></td></tr></tbody></table>'
		);
	
		var timeHeadsID = Ext.id();
		var columnsContainerID = Ext.id();
		
		this.container.dom.innerHTML = t.applyTemplate({
			"timeHeadsID":timeHeadsID, 
			"columnsContainerID":columnsContainerID
		});
	
		var timeColumn = Ext.get(timeHeadsID);		
		
		for (var i = 0;i<24;i++)
		{			
			 Ext.DomHelper.append(timeColumn,
				{tag: 'div', id: 'head'+i, class: "timeHead", html: i+':00'}, true);
		}
		
		this.columnsContainer = Ext.get(columnsContainerID);
		
		
	
		var columnWidth = 100/this.days;
		
		for(var day=0;day<this.days;day++)
		{	
			var column = Ext.DomHelper.append(this.columnsContainer,
				{tag: 'div', id: Ext.id(), style: "float:left;width:"+columnWidth+"%"}, true);
				
			
		
			var class = "evenRow";
			for (var i = 0;i<48;i++)
			{		
					
				var row = Ext.DomHelper.append(column,
					{tag: 'div', id: 'day'+day+'_row'+i, class: class}, true);	
					
				row.on("mousedown", function (e , el) {					
					this.startSelection(el.id); 
				}, this);
				
				if(class=="evenRow")
				{
					class = "unevenRow";
				}else
				{
					class = "evenRow";
				}
				
				
				/*
				//set some handy values
				if(i==0 && day==0)
				{
					//snap on each row and column
					var snap = row.getSize();
					
			
					this.snapX = snap['width'];
					this.snapY = snap['height'];
					
					
					//the start of the grid
					var position = row.getXY();
					this.gridX = position[0];
					this.gridY = position[1];
				}				
				*/
			}
		}
		/*

		//set some handy values
		
		var FirstCol = Ext.get("day0_row0");
		//snap on each row and column
		var snap = FirstCol.getSize();		

		this.snapX = snap['width'];
		this.snapY = snap['height'];		
		
		//the start of the grid
		var position = FirstCol.getXY();
		this.gridX = position[0];
		this.gridY = position[1];
						 
		*/
	},
	
	getSnap : function()
	{
		var FirstCol = Ext.get("day0_row0");
		//snap on each row and column
		var snap = FirstCol.getSize();		

		return [snap['width'],snap['height']];
	},
	
	getGridXY : function()
	{
		var FirstCol = Ext.get("day0_row0");
		
		return FirstCol.getXY();		
	},
	
	getRowIdByXY : function(x,y)
	{
		var day = (x-this.gridX)/this.snapX;
		var row = (y-this.gridY)/this.snapY;
		return 'day'+day+'_row'+row;
		
	},
	getRowNumberByY : function(y)
	{
		return (y-this.gridY)/this.snapY;	
	},
	getDayByX : function(x)
	{
		return Math.floor((x-this.gridX)/this.snapX);
	},
	startSelection : function (row){
	
		//check if we are not dragging an event
		if(!this.dragEvent)
		{
			//determine the day and hour the user clicked on
			var arr = row.split('_');		
			this.clickedDay = parseInt(arr[0].replace("day",""));
			this.clickedRow = parseInt(arr[1].replace("row",""));
		
			//create the selection proxy
			if(!this.selector)
			{
				this.selector = Ext.DomHelper.append(this.container,
					{tag: 'div', id: Ext.id(), class: "selector"}, true);		
			}
		
			//get position of the row the user clicked on
			this.selectorStartRow = Ext.get(row);
			
			var position = this.selectorStartRow.getXY();
			//add double border
			//position[0]+=3;
			
			var size=this.selectorStartRow.getSize();
			
			
			//display the selector proxy
			//this.selector.setOpacity(.4);
			this.selector.setVisible(true,false);
			this.selector.setXY(position);
			//substract double border
			this.selector.setSize(size['width']-3, size['height']);
			
			
			//create an overlay to track the mousemovement
			if(!this.overlay){
			    this.overlay = this.selector.createProxy({tag: "div", cls: "x-resizable-overlay", html: "&#160;"});
			    this.overlay.unselectable();
			    this.overlay.enableDisplayMode("block");	
			    this.overlay.on("mousemove", this.onSelectionMouseMove, this);
				this.overlay.on("mouseup", this.onSelectionMouseUp, this);	    
			}		
			
			    
			this.overlay.setSize(Ext.lib.Dom.getViewWidth(true), Ext.lib.Dom.getViewHeight(true));
			this.overlay.show();
		}
	},	
	onSelectionMouseMove : function (e){
		
		//update the selector proxy
		var eventPos = e.getXY();				
		var shadowPos = this.selector.getXY();		
		//var height = this.selector.getHeight();		
		var increment = this.snap(eventPos[1]-shadowPos[1],this.snapY, 0);
		this.selector.setHeight(increment);		
	
	},	
	onSelectionMouseUp : function (e){
		//hide the overlay		
		this.overlay.hide();
		
		//create an event
		
		var eventId = Ext.id();
		
		var event = Ext.DomHelper.append(this.container,
				{tag: 'div', id: eventId, class: "event-container" }, true);
				
		
		var position = this.selectorStartRow.getXY();		
		event.setXY(position);		
		event.setSize(this.selector.getSize());

		this.clearSelection();
		
		//add the event to the appointments array		
		if(typeof(this.appointments[this.clickedDay])=='undefined')
		{
			this.appointments[this.clickedDay]=Array();
		}		
		
		
		//add it to the appointments of this day for calculation
		this.appointments[this.clickedDay].push(event);
		this.calculateappointments(this.clickedDay);		
		
		
		this.newEvent = Ext.get(eventId);
		
		//get the name for the event
		Ext.MessageBox.prompt('Name', 'Please enter the name:', function(btn, text){
			
			if(btn=='ok')
			{
				
				
				this.newEvent.dom.innerHTML = text;
				
				
				var resizer = new Ext.Resizable(event, {
				    handles: 's',
				    //minWidth: event.getWidth(),
				    minHeight: this.snapY,
				    maxWidth: event.getWidth(),
				    //maxHeight: this.snapY*48,
				    heightIncrement: this.snapY,
				    draggable: false,
				    pinned: true
				});
				
				resizer.on('resize', function(){this.calculateappointments(this.clickedDay);}, this);
				
				
				this.fireEvent("create", this, false);
			}else
			{
				
				
				//remove it from the day's appointments
				this.removeEventFromArray(this.clickedDay, this.newEvent.id);
				this.newEvent.remove();
				this.calculateappointments(this.clickedDay);
			}
			
			
		},this);
	
			
	},
	
	addEvent : function (name, day, startRow, endRow)
	{
		var eventId = Ext.id();
		
		var event = Ext.DomHelper.append(this.container,
				{tag: 'div', id: eventId, class: "event-container", html: name }, true);
				
				
		var startRowEl = Ext.get("day"+day+"_row"+startRow);
		var endRowEl = Ext.get("day"+day+"_row"+endRow);
		
		var startRowPos = startRowEl.getXY();
		var endRowPos = endRowEl.getXY();
		
		var height = endRowPos[1]+startRowPos[1]+this.snapY+3;
		
		event.setXY(startRowPos);
		event.setSize(this.snapX-2, height);
		
		//var styles = {'width':this.snapX-3+'px', 'height': height+'px', 'z-index':'1', 'top': startRowPos[1]+'px', 'left':startRowPos[0]+'px' };

		//event.setStyle(styles);		
		
		
		event.on('mousedown', function(e, eventEl) {
			
			//this.dragEvent= Ext.get(eventEl.parentNode);
				this.dragEvent= Ext.get(eventEl);
				this.dragappointmentstartPos=this.dragEvent.getXY();
				
				this.startEventDrag();
			}, this);
			
			
		//add the event to the appointments array		
		if(typeof(this.appointments[day])=='undefined')
		{
			this.appointments[day]=Array();
		}		
		
		
		//add it to the appointments of this day for calculation
		this.appointments[day].push(event);
		this.calculateappointments(day);		
		
		
		var resizer = new Ext.Resizable(event, {
				    handles: 's',
				    //minWidth: event.getWidth(),
				    minHeight: this.snapY,
				    maxWidth: event.getWidth(),
				    //maxHeight: this.snapY*48,
				    heightIncrement: this.snapY,
				    draggable: false,
				    pinned: true
				});
			
		resizer.on('resize', function(){this.calculateappointments(this.clickedDay);}, this);
		
		
		
		
	},
	removeEventFromArray : function (day, event_id)
	{
		for(var i=0;i<this.appointments[day].length;i++)
		{
			if(this.appointments[day][i].id==event_id)
			{
				return this.appointments[day].splice(i,1);				
			}
		}
		return false;
	},
	
	calculateappointments :  function (day)
	{
		if(typeof(this.appointments[day])!='undefined')
		{
			//determine the maximum appointments on one row
			var maxPositions=0;
			
			//store overlaps per event in this array
			//var overlaps = Array();
			var positions = Array();
			var maxPositions = 0;
			
			
			
			//sort the appointments on their start time (Y pos)
			this.appointments[day].sort(function(a,b){
				return a.getY()-b.getY();
			});
			
			//the left coordinate of the day column
			var dayColumnLeft=0;
			
			//create an array of rows with their positions
			this.rows=Array();
				
			for(var rowId=0;rowId<48;rowId++)
			{								
				var row = Ext.get("day"+day+"_row"+rowId);
				
				var rowSize = row.getSize();
				var rowPosition = row.getXY();			
				
				if(rowId==0)
				{
					dayColumnLeft=rowPosition[0]+1;
				}
				
				if(typeof(this.rows[rowId]) == 'undefined')
				{
					this.rows[rowId]=Array();
				}
					
				var rowY = rowPosition[1]-(this.snapY/2);
	
				//check how manu appointments are in the row area
				for(var i=0;i<this.appointments[day].length;i++)
				{
					var eventPosition = this.appointments[day][i].getXY();
					var appointmentsize = this.appointments[day][i].getSize();
					
					//new right side is right from existing left side and 
					//new left side is left from existing right side
					
					//and
					
					//new top is above the existing bottom and 
					//new bottom is below the existing top
					
					if((
						rowPosition[0]+rowSize['width'])>eventPosition[0] && 
						rowPosition[0]<eventPosition[0]+appointmentsize['width'] && 
						rowPosition[1]+rowSize['height']<eventPosition[1]+appointmentsize['height'] && 
						rowPosition[1]+rowSize['height']>eventPosition[1])
					{
						
						
						if(typeof(positions[this.appointments[day][i].id])=='undefined')
						{
							//determine the event's position
							var position=0;
						
							//find a free position
							while(typeof(this.rows[rowId][position])!='undefined')
							{
								position++;											
							}
							
							//set the space occupied
							eventRowId=rowId;
							for(var n=rowPosition[1];n<eventPosition[1]+appointmentsize['height']-2;n+=this.snapY)
							{						
								if(typeof(this.rows[eventRowId]) == 'undefined')
								{
									this.rows[eventRowId]=Array();
								}
								this.rows[eventRowId][position]=this.appointments[day][i].id;
								eventRowId++;
							}
							
							this.rows[rowId][position]=this.appointments[day][i].id;
												
							positions[this.appointments[day][i].id]=position;					
						}											
					}							
				}
				
				
				
				//update the max appointments on row per day value	
				if(position>maxPositions)
				{
					maxPositions=position;
				}
			
							
			}			
			//we got the maximum number of appointments on one row now.
			//we know for each appointments how many overlaps they have
			//we now need to know the widths of each event
			
			var posWidth = this.snapX/(maxPositions+1);
			
			for(var i=0;i<this.appointments[day].length;i++)
			{
				
				var appointmentsize = this.appointments[day][i].getSize();
				var eventPosition = this.appointments[day][i].getXY();				
				var rowId = this.getRowNumberByY(eventPosition[1]);
				var eventRows=(appointmentsize['height']-2)/this.snapY;
				
				var eventWidth = this.getEventWidth(
					positions[this.appointments[day][i].id],
					maxPositions,
					rowId,
					eventRows,
					posWidth);
				
				this.appointments[day][i].setWidth(eventWidth);
				
				var offset = positions[this.appointments[day][i].id]*posWidth;
				this.appointments[day][i].setX(dayColumnLeft+offset);
				//this.appointments[day][i].dom.innerHTML = 'New event';
			}
		}
	},
	
	getEventWidth : function(startPosition, maxPositions, startRowId, eventRows, posWidth)
	{
		var eventWidth = posWidth;
				
		var rowPosition = startPosition+1;
		while(rowPosition<=maxPositions)
		{
			
			for(var r=0;r<eventRows;r++)
			{
				if(typeof(this.rows[startRowId+r][rowPosition])!='undefined')
				{					
					return eventWidth-3;
				}
			}
			eventWidth+=posWidth;
			rowPosition++;
		}
		return eventWidth-3;
	},
	
	getOverlappingappointments : function(checkEvent, day, appointments){
			
		if(typeof(appointments)=='undefined')
		{
			var appointments = Array();
		}
		
	
		if(typeof(this.appointments[day])!='undefined' )
		{	
			
			//check all appointments in grid to see if they are in the new event's
			//area
			
			var checkSize = checkEvent.getSize();
			var checkPosition = checkEvent.getXY();
			
			for(var i=0;i<this.appointments[day].length;i++)
			{
				//if(this.appointments[day][i].id!=checkEvent.id && typeof(checkedappointments[this.appointments[day][i].id])=='undefined')
				if(!this.inappointmentsArray(this.appointments[day][i].id, appointments))
				{
					var position = this.appointments[day][i].getXY();
					var size = this.appointments[day][i].getSize();
					
					//new right side is right from existing left side and 
					//new left side is left from existing right side
					
					//and
					
					//new top is above the existing bottom and 
					//new bottom is below the existing top
					
					if((
						checkPosition[0]+this.snapX)>position[0] && 
						checkPosition[0]<position[0]+this.snapX && 
						checkPosition[1]<position[1]+size['height'] && 
						checkPosition[1]+checkSize['height']>position[1])
					{
						appointments.push(this.appointments[day][i]);
						appointments = this.getOverlappingappointments(this.appointments[day][i],day, appointments);							
					}	
				}						
			}		
		}		
		return appointments;	
	},	
	
	inappointmentsArray : function (id, appointments)
	{
		for(var i=0;i<appointments.length;i++)
		{
			if(appointments[i].id==id)
			{
				return true;
			}
		}
		return false;
	},
	clearSelection : function()
	{
			
		this.selector.setVisible(false,true);
	},
	
	startEventDrag : function(e) {
	
		//create the selection proxy
		if(!this.selector)
		{
			this.selector = Ext.DomHelper.append(this.container,
				{tag: 'div', id: Ext.id(), class: "selector"}, true);		
		}
		
		//create an overlay to track the mousemovement
		if(!this.eventDragOverlay){
		    this.eventDragOverlay = this.selector.createProxy({tag: "div", cls: "x-resizable-overlay", html: "&#160;"});
		    this.eventDragOverlay.unselectable();
		    this.eventDragOverlay.enableDisplayMode("block");	
		    this.eventDragOverlay.on("mousemove", this.onEventDragMouseMove, this);
			this.eventDragOverlay.on("mouseup", this.onEventDragMouseUp, this);		    
		}		
		this.eventDragOverlay.setSize(Ext.lib.Dom.getViewWidth(true), Ext.lib.Dom.getViewHeight(true));
		this.eventDragOverlay.show();
		
		
		
	},
	onEventDragMouseMove : function (e){
		
		//update the selector proxy
		var mouseEventPos = e.getXY();				
		
		
		
		
		var x = this.snapPos(this.dragappointmentstartPos[0],mouseEventPos[0],this.snapX,this.days);
		var y = this.snapPos(this.dragappointmentstartPos[1],mouseEventPos[1],this.snapY,48);
		
		var gridRight = this.gridX+this.days*this.snapX;
		var gridBottom = this.gridY+48*this.snapY;
		if(x<gridRight && x>this.gridX)
		{
			this.dragEvent.setX(x);
		}

		
		if(y<gridBottom && y>this.gridY)
		{
			this.dragEvent.setY(y);
		}

		//this.dragEvent.setXY([x, y]);	
		//this.dragEvent.dom.innerHTML = "X:"+x+" Y:"+y;	
	
	},	
	onEventDragMouseUp : function (e){
		
		//unset the drag stuff
		this.eventDragOverlay.hide();
		
		var newPos = this.dragEvent.getXY();
		
		//floor the position because it can be anywhere in the column
		//var newDay = Math.floor((newPos[0]-this.gridX-3)/this.snapX);
		var newDay = this.getDayByX(newPos[0]);		
		var originalDay = this.getDayByX(this.dragappointmentstartPos[0]);
		
		
		if(newDay!=originalDay)
		{
			//remove it from the original day's appointments
			this.removeEventFromArray(originalDay, this.dragEvent.id);

			
			//add it to the new day's appointments
			if(typeof(this.appointments[newDay])=='undefined')
			{
				this.appointments[newDay]=Array();
			}
			this.appointments[newDay].push(this.dragEvent);
			
			//recalculate grid
			this.calculateappointments(originalDay);
			this.calculateappointments(newDay);
		}else
		{
			this.calculateappointments(originalDay);
		}
		
		this.fireEvent("change", this, false);
		
		this.dragEvent=false;
	},
	
	snapPos : function(oldPos, newPos, snap){
		
		var inc = newPos-oldPos;	
		
		var snaps = Math.floor(inc/snap);
		
		var leftOver = inc-(snaps*snap);		
	
		var m = snap/2;
		if(leftOver>m)
		{
			snaps++;
		}	
		return oldPos+(snaps*snap);
	},
	
	
    snap : function(value, inc, min){
        if(!inc || !value) return value;
        var newValue = value;
        var m = value % inc;
        if(m > 0){
            if(m > (inc/2)){
                newValue = value + (inc-m);
            }else{
                newValue = value - m;
            }
        }
        return Math.max(min, newValue);
    }

});

var CalendarGrid = new Ext.CalendarGrid('CalendarGrid', {days: 5});


Ext.EventManager.onDocumentReady(function(){
	CalendarGrid.render();
	
	CalendarGrid.addEvent('test','0','3','6');
	
	
	CalendarGrid.on("create", function(){
		alert('create');
	});
	
	CalendarGrid.on("change", function(){
		alert('change');
	});
});
</script>

</body>
</html>

