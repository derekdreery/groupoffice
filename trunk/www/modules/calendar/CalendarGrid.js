Ext.namespace('Ext.calendar');

Ext.calendar.CalendarGrid = function(container, config){ 

	Ext.apply(this, config);
	
	this.dateFormat = 'Y-m-d';
	this.dateTimeFormat = 'Y-m-d H:i';
	
	
	//amount of days to display
	if(!this.days)
	{
		this.days=1;
	}
	
	//Start day of the week. Monday or sunday
	if(!this.firstWeekday)
	{
		//default is monday
		this.firstWeekday=1;
	}
	
	if(!this.startDate)
	{
		//Calculate the first day of the week
		this.startDate=new Date();	
	}
	
	if(!this.container)
	{
		this.container = Ext.get(container);		
	}
	
	if(!this.timeFormat)
	{
		this.timeFormat='H:i';
	}
	
	//if this is not set the grid does not display well when I put a load mask on it.
	this.container.setStyle("overflow", "hidden");
	
	//Don't select things inside the grid
	this.container.unselectable();
	
	//private var that is used when an event is dragged to another location
	this.dragEvent=false;
	
	//all the grid appointments are stored in this array. First index is day and second is the dom ID.
	this.appointments=Array();
	
	//same for allday appointments.
	this.allDayAppointments=Array();
	
	//how many rows to display for all dya events
	this.allDayEventRows=1;
	
	//The remote database ID's can be stored in this array. Useful for database updates
	this.remoteEventIds=Array();

	
	
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
	    "move" : true,
	    
	    
	    "resize" : true

    });
    
    
    
    if(this.store){
        this.setStore(this.store, true);
    }
    
    if(this.days>4)
	{
		this.startDate=this.getFirstDateOfWeek(this.startDate);
	}
	this.setStoreBaseParams();
	
	
	
	
	Ext.calendar.CalendarGrid.superclass.constructor.call(this);
}
	
Ext.extend(Ext.calendar.CalendarGrid, Ext.util.Observable, {


	//build the html grid
	render : function (){
		
		
		
	
		//create container for the column headers
		this.headingsContainer = Ext.DomHelper.append(this.container,
				{tag: 'div', cls: "x-calGrid-headings-container"}, true);
				
		
		//create container for the all day events
		
		this.allDayContainer  = Ext.DomHelper.append(this.container,
				{tag: 'div', cls: "x-calGrid-all-day-container"}, true);
		
		
		
		//create container for the grid
		this.gridContainer = Ext.DomHelper.append(this.container,
				{tag: 'div', cls: "x-calGrid-grid-container"}, true);
		
		//calculate gridContainer size
		var headingsHeight = this.headingsContainer.getHeight();
		var allDayHeight = this.allDayContainer.getHeight();
		var containerHeight = this.container.getHeight();
		var containerSize = this.container.getSize();
		
		var gridContainerHeight = containerHeight-headingsHeight-allDayHeight;
		this.gridContainer.setSize(containerSize['width'], gridContainerHeight);
		
		
	
		var t = new Ext.Template(
	    	'<table cellpadding="0" cellspacing="0" border="0" style="table-layout:fixed;width:100%;">'+
	    	'<tbody><tr><td id="{timeHeadsID}" style="width:40px"></td>'+
	    	'<td style="position:relative;" id="{columnsContainerID}"></div></td></tr></tbody></table>'
		);
	
		var timeHeadsID = Ext.id();
		var columnsContainerID = Ext.id();
		
		this.gridContainer.dom.innerHTML = t.applyTemplate({
			"timeHeadsID":timeHeadsID, 
			"columnsContainerID":columnsContainerID
		});
	
		var timeColumn = Ext.get(timeHeadsID);		
		
		for (var i = 0;i<24;i++)
		{			
			 Ext.DomHelper.append(timeColumn,
				{tag: 'div', id: 'head'+i, cls: "x-calGrid-timeHead", html: i+':00'}, true);
		}
		
		this.columnsContainer = Ext.get(columnsContainerID);
		
		
		this.createHeadings();
	
		var columnWidth = 100/this.days;
		
		for(var day=0;day<this.days;day++)
		{	
				
			//create allday column			
			var allDayColumn = Ext.DomHelper.append(this.allDayContainer,
				{tag: 'div', id: 'all_day_'+day, cls: "x-calGrid-all-day-column", style: "float:left;width:"+columnWidth+"%"}, true);
				
			allDayColumn.on("click", function(e, columnEl){
				
					//determine the day and hour the user clicked on
					var arr = columnEl.id.split('_');		
					this.clickedDay = parseInt(arr[2]);
							
					this.clickedRow=-1;
						
					//this.eventPrompt();
					this.fireEvent("create", this, this.elementToEvent(columnEl, true));
				}, 
				this);
			
			
			//create grid column
			var column = Ext.DomHelper.append(this.columnsContainer,
				{tag: 'div', id: Ext.id(), style: "float:left;width:"+columnWidth+"%"}, true);
				
			
		
			var className = "x-calGrid-evenRow";
			for (var i = 0;i<48;i++)
			{		
					
				var row = Ext.DomHelper.append(column,
					{tag: 'div', id: 'day'+day+'_row'+i, cls: className}, true);	
					
				row.on("mousedown", function (e , el) {					
					this.startSelection(el.id); 
				}, this);
				
				if(className=="x-calGrid-evenRow")
				{
					className = "x-calGrid-unevenRow";
				}else
				{
					className = "x-calGrid-evenRow";
				}
			}
		}
		

		//set some handy values
		
		//var FirstCol = Ext.get("day0_row0");
		
		/*
		//snap on each row and column
		var snap = FirstCol.getSize();		

		this.snapX = snap['width'];
		this.snapY = snap['height'];		
		*/
		
		//the start of the grid
		//var position = FirstCol.getXY();
		//this.gridX = position[0];
		//this.gridY = position[1];
		
		this.gridX=0;
		this.gridY=0;
		
		
		//Monitor window resize
		
		Ext.EventManager.onWindowResize(function(w, h){
			//Ext.Msg.alert('Resize', 'Viewport w = '+w+', h = '+h);
			
			this.autoSizeGrid();
			for(var i=0;i<this.days;i++)
			{
			 	
		    	this.calculateAppointments(i);
		    }
		}, this);
		
	},
	scrollTo : function (row)
	{
		//scroll to 7 am.
		var snap = this.getSnap();
		this.gridContainer.scrollTo("top", snap['y']*row);
	},
	setStore : function(store, initial){
        if(!initial && this.store){
            this.store.un("datachanged", this.reload);
            //this.store.un("add", this.onAdd);
            //this.store.un("remove", this.onRemove);
            //this.store.un("update", this.onUpdate);
            this.store.un("clear", this.reload);
        }
        if(store){
        	store.on("beforeload", this.mask, this)
            store.on("datachanged", this.reload, this);
            //store.on("add", this.onAdd, this);
           // store.on("remove", this.onRemove, this);
           // store.on("update", this.onUpdate, this);
            store.on("clear", this.reload, this);
            
            
            
        }
        this.store = store;
        if(store){
            //this.refresh();
        }
    },
    
    setStoreBaseParams : function(){
    	this.store.baseParams['start_time']=this.startDate.format(this.dateTimeFormat);
        this.store.baseParams['end_time']=this.startDate.add(Date.DAY, this.days).format(this.dateTimeFormat);    
    },
	
	getFirstDateOfWeek : function(date)
	{
		//Calculate the first day of the week
		
		var weekday = date.getDay();
		var day = date.getDate();
		var month = date.format("m");
		var year = date.getFullYear();
		
		var tmpDay = day-weekday+this.firstWeekday;
		if(tmpDay>day)
		{
			tmpDay-=7;
		}
		
		if(tmpDay.toString().length==1)
		{
			tmpDay ="0"+tmpDay;
		}
		
		return Date.parseDate(year+"-"+month+"-"+tmpDay, "Y-m-d")
	},
	
	mask : function()
	{
		 this.container.mask('Loading...','x-mask-loading');
	},
	
	unmask : function()
	{
		this.container.unmask();
	},
	
		
	getSnap : function()
	{
		var FirstCol = Ext.get("day0_row0");
		//snap on each row and column
		var snap = FirstCol.getSize();		

		return {'x':snap['width'], 'y': snap['height']};
	},
	
	getGridXY : function()
	{
		var FirstCol = Ext.get("day0_row0");
		
		return FirstCol.getXY();		
	},
	
	getRowIdByXY : function(x,y)
	{
		var snap = this.getSnap();
	
		var day = (x-this.gridX)/snap["x"];
		var row = (y-this.gridY)/snap["y"];
		return 'day'+day+'_row'+row;
		
	},
	getRowNumberByY : function(y)
	{
		var snap = this.getSnap();
		
		var gridPosition = this.columnsContainer.getXY();
		
		return Math.floor((y-gridPosition[1])/snap["y"]);	
	},
	getDayByX : function(x)
	{
		var snap = this.getSnap();
		var gridPosition = this.columnsContainer.getXY();
		
		return Math.floor((x-gridPosition[0])/snap["x"]);
	},
	startSelection : function (row){
	
		//check if we are not dragging an event
		if(!this.dragEvent)
		{
			this.dragSnap = this.getSnap(); 
			//determine the day and hour the user clicked on
			var arr = row.split('_');		
			this.clickedDay = parseInt(arr[0].replace("day",""));
			this.clickedRow = parseInt(arr[1].replace("row",""));
		
			//create the selection proxy
			if(!this.selector)
			{
				this.selector = Ext.DomHelper.append(this.container,
					{tag: 'div', id: Ext.id(), cls: "x-calGrid-selector"}, true);		
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
		var increment = this.snap(eventPos[1]-shadowPos[1],this.dragSnap["y"], 0);
		this.selector.setHeight(increment);		
	
	},	
	
	onSelectionMouseUp : function (e){
		//hide the overlay		
		this.overlay.hide();
		
		this.fireEvent("create", this, this.elementToEvent(this.selector.id));
		this.clearSelection();
		/*
		//get the name for the event
		Ext.MessageBox.prompt('Name', 'Please enter the name:', function(btn, text){
			
			if(btn=='ok')
			{				
				//var parsedEventEl = this.parseElement(this.selector.id);
				
				var snap = this.getSnap();				
				var rows = this.selector.getHeight()/snap['y'];
				
				var newEventId=Ext.id();
				
				this.addEvent(newEventId, text, 
					this.clickedDay,
					this.clickedRow,
					this.clickedRow+rows-1);
				
				this.calculateAppointments(this.clickedDay);
				
				this.fireEvent("create", this, newEventId, text);
			}
			this.clearSelection();
			
			
		},this);
		*/		
	},
	/*
	eventPrompt : function()
	{
		//get the name for the event
		Ext.MessageBox.prompt('Name', 'Please enter the name:', function(btn, text){
			
			if(btn=='ok')
			{			
				
				var newEventId=Ext.id();
				
				if(this.clickedRow>-1)
				{
					var snap = this.getSnap();				
					var rows = this.selector.getHeight()/snap['y'];
					
					this.addEvent(newEventId, text, 
						this.clickedDay,
						this.clickedRow,
						this.clickedRow+rows-1);
					
					this.calculateAppointments(this.clickedDay);
				}else
				{
					this.addEvent(newEventId, text,this.clickedDay);
					
				}
					
				this.fireEvent("create", this, newEventId, text);
			}			
		},this);	
	},*/
	
	addTimedEvent : function (eventId, name, startDate, endDate)
	{
		var gridStartDayOfYear = this.startDate.getDayOfYear();		
		var eventStartDayOfYear = startDate.getDayOfYear();
		
		var day = eventStartDayOfYear-gridStartDayOfYear;
		
		if(day<0 || day > this.days)
		{
			//date is out of bounds
			return false;
		}
		
		
		var startRow = startDate.getHours()*2;
		var endRow = endDate.getHours()*2-1;
		if(endRow<startRow)
		{
			endRow=startRow+2;
		}
		
		var startMin = startDate.getMinutes();
		if(startMin>30)
		{
			startRow +=2;
		}else if(startMin>0)
		{
			startRow++;
		}
		
		var endMin = endDate.getMinutes();
		if(endMin>30)
		{
			endRow +=2;
		}else if(endMin>0)
		{
			endRow++;
		}
		
		
		this.addEvent(eventId, '<p class="x-calGrid-event-time">'+startDate.format(this.timeFormat)+"</p>"+name, day, startRow, endRow);
		
		
		
	},	
	
	addEvent : function (eventId, name, day, startRow, endRow)
	{
	
		var snap = this.getSnap();		
				
				
		if(startRow && endRow)
		{
			var event = Ext.DomHelper.append(this.columnsContainer,
				{tag: 'div', 'id': eventId, cls: "x-calGrid-event-container", html: name }, true);
				
			var startRowEl = Ext.get("day"+day+"_row"+startRow);
			var endRowEl = Ext.get("day"+day+"_row"+endRow);
			
			
			
			var startRowPos = startRowEl.getXY();
			var endRowPos = endRowEl.getXY();
			
			var height = endRowPos[1]-startRowPos[1]+snap["y"]+3;
			
		
			event.setXY(startRowPos);
			event.setSize(snap["x"]-2, height);
	
			
			
			event.on('mousedown', function(e, eventEl) {			
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
			//this.calculateappointments(day);		
			
			
			var resizer = new Ext.Resizable(event, {
					    handles: 's',
					    //minWidth: event.getWidth(),
					    minHeight: snap["y"],
					    maxWidth: event.getWidth(),
					    //maxHeight: this.snapY*48,
					    heightIncrement: snap["y"],
					    draggable: false,
					    pinned: true
					});
				
			resizer.on('resize', function(eventEl){
				this.fireEvent("resize", this, eventEl.el.id);
				
				var elX = eventEl.el.getX();	
				this.clickedDay = this.getDayByX(elX);
	
				
				this.calculateAppointments(this.clickedDay);
				}, this);
		}else
		{
			//allday event
			var allDayColumn = Ext.get("all_day_"+day);
			var event = Ext.DomHelper.append(allDayColumn,
				{tag: 'div', id: eventId, cls: "x-calGrid-event-container", html: name }, true);
				
			//add the event to the appointments array		
			if(typeof(this.allDayAppointments[day])=='undefined')
			{
				this.allDayAppointments[day]=Array();
			}	
			
			
			
			var count = this.allDayAppointments[day].length;
			
			this.allDayAppointments[day].push(event);			
			
			var size = allDayColumn.getSize();
			event.setSize(size['width'], 20);
			
			var position = this.allDayContainer.getY();
			
			var eventY = position+(count*21);
			
			event.setY(eventY);
			
			if(count+1==this.allDayEventRows)
			{
				this.increaseAllDayContainer();
			}
		}
		
		
		
		
	},
	
	autoSizeGrid : function() {
		
		//calculate gridContainer size
		var headingsHeight = this.headingsContainer.getHeight();
		var allDayHeight = this.allDayContainer.getHeight();
		var containerHeight = this.container.getHeight();
		var containerSize = this.container.getSize();
		
		var gridContainerHeight = containerHeight-headingsHeight-allDayHeight;
		this.gridContainer.setSize(containerSize['width'], gridContainerHeight);
		
	},
	
	increaseAllDayContainer : function()
	{
		var allDayContainerSize = this.allDayContainer.getHeight();
		var gridContainerSize = this.gridContainer.getHeight();
		
		this.allDayContainer.setHeight(allDayContainerSize+20);
		this.gridContainer.setHeight(gridContainerSize-20);
		
		this.allDayEventRows++;		
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
	
	
	
	calculateAppointments :  function (day)
	{
		if(typeof(this.appointments[day])!='undefined')
		{
			var snap = this.getSnap();
			
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
					
				//var rowY = rowPosition[1]-(snap["y"]/2);
	
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
							for(var n=rowPosition[1];n<eventPosition[1]+appointmentsize['height']-2;n+=snap["y"])
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
			
			var posWidth = snap["x"]/(maxPositions+1);
			
			for(var i=0;i<this.appointments[day].length;i++)
			{
				
				var appointmentsize = this.appointments[day][i].getSize();
				var eventPosition = this.appointments[day][i].getXY();				
				var rowId = this.getRowNumberByY(eventPosition[1]);
				var eventRows=(appointmentsize['height']-2)/snap["y"];
				
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
	
	inAppointmentsArray : function (id, appointments)
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
	
		this.dragSnap = this.getSnap();
		 
		//create the selection proxy
		if(!this.selector)
		{
			this.selector = Ext.DomHelper.append(this.container,
				{tag: 'div', id: Ext.id(), cls: "x-calGrid-selector"}, true);		
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
		
		
		
		var x = this.snapPos(this.dragappointmentstartPos[0],mouseEventPos[0],this.dragSnap["x"],this.days);
		var y = this.snapPos(this.dragappointmentstartPos[1],mouseEventPos[1],this.dragSnap["y"],48);
		
		var gridRight = this.gridX+this.days*this.dragSnap["x"];
		var gridBottom = this.gridY+48*this.dragSnap["y"];
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
			this.calculateAppointments(originalDay);
			this.calculateAppointments(newDay);
		}else
		{
			this.calculateAppointments(originalDay);
		}
		
		this.fireEvent("move", this, this.dragEvent.id);
		
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
    },
    
    
    
    clearGrid : function()
	{
		for(var day=0;day<this.days;day++)
		{
			if(this.appointments[day])
			{
				for(var i=0;i<this.appointments[day].length;i++)
				{
					this.appointments[day][i].remove();
				}
			}
			
			if(this.allDayAppointments[day])
			{			
				for(var i=0;i<this.allDayAppointments[day].length;i++)
				{
					this.allDayAppointments[day][i].remove();
				}
			}
		}
		
		this.appointments=Array();
		this.allDayAppointments=Array();
	},	
	createHeadings : function()
	{
		var columnWidth = 100/this.days;
		
		this.headingsContainer.update('');
		for(var day=0;day<this.days;day++)
		{	
			var dt = this.startDate.add(Date.DAY, day);
			//create grid heading
			var heading = Ext.DomHelper.append(this.headingsContainer,
				{tag: 'div', id: Ext.id(), cls: "x-calGrid-heading", style: "float:left;width:"+columnWidth+"%", html: dt.format('D m-d') }, true);
		}
	},
	
    
    next : function(days)
    {
    	if(!days)
    	{
    		days = this.days;
    	}
    	this.gotoDate(this.startDate.add(Date.DAY, days));
  
    },
    
    gotoDate : function(date)
    {
    	if(this.days>4)
    	{
    		this.startDate = this.getFirstDateOfWeek(date);
    	}else
    	{
    		this.startDate = date;
    	}
    	
    	this.setStoreBaseParams();    	
    	this.store.reload();
    },
    
    reload : function()
    {
    	this.clearGrid();
    	this.createHeadings();    	
    	this.load();
    	
    },
    
    
    load : function()
    {		
		var records = this.store.getRange();

        for(var i = 0, len = records.length; i < len; i++){            
            
            var startDate = Date.parseDate(records[i].data['start_time'], this.dateTimeFormat);
			var endDate = Date.parseDate(records[i].data['end_time'], this.dateTimeFormat);
			
			var domId = Ext.id();						
			this.addTimedEvent(domId, records[i].data['name'], startDate, endDate);
			this.registerEventId(domId, records[i].data['event_id']);
            
        }
        
        for(var day=0;day<this.days;day++)
		{
			this.calculateAppointments(day);
		}
		this.unmask();
		
		this.scrollTo(14);
		
		
    
    },
    /**
     * An array of domId=>database ID should be kept so that we can figure out
     * which event to update when it's modified.
     * @param {String} domId The unique DOM id of the element
     * @param {String} remoteId The unique database id of the element     
     * @return void
     */
    registerEventId : function(domId, remoteId)
    {
    	this.remoteEventIds[domId]=remoteId;
    },
    
    elementToEvent : function(elementId, allDay)
	{
		if(!allDay)
		{
			allDay=false;
		}
		var el = Ext.get(elementId);
		
		var position=el.getXY();
		
		
		if(!allDay)
		{
			var size = el.getSize();
			
			var startRow = this.getRowNumberByY(position[1]);
			if(startRow<0)
			{
				startRow=0;
			}				
			var endRow = this.getRowNumberByY(position[1]+size['height']);
			if(endRow<=startRow)
			{
				endRow=startRow+1;
			}
		}else
		{
			startRow=0;
			endRow=0;
		}
		
		var day = this.getDayByX(position[0]);
		var remoteId = !this.remoteEventIds[elementId] ? 0 : this.remoteEventIds[elementId];
		
				
		var date = this.startDate.add(Date.DAY, day);
		var startDate = date.add(Date.MINUTE,startRow*30);
		var endDate = date.add(Date.MINUTE,endRow*30);
		
		//return { 'startDate': startDate.format(this.dateTimeFormat), 'endDate':endDate.format(this.dateTimeFormat), 'day':day, 'remoteId':remoteId };
		return { 'startDate': startDate, 'endDate':endDate, 'day':day, 'remoteId':remoteId };
	}

});