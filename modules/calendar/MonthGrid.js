/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @version $Id: MonthGrid.js 2635 2008-07-18 11:00:31Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 */

 
GO.grid.MonthGrid = Ext.extend(Ext.Panel, {
	/**
     * @cfg {String} The components handles dates in this format
     */
	dateFormat : 'Y-m-d',
	/**
     * @cfg {String} The components handles dates in this format
     */
	dateTimeFormat : 'Y-m-d H:i',
	
	timeFormat : 'H:i',
	/**
     * @cfg {Number} Start day of the week. Monday or sunday
     */
	firstWeekday : 1,
	/**
     * @cfg {Date} The date set by the user
     */
	configuredDate : false,
	/**
     * @cfg {Date} The date where the grid starts. This can be recalculated after a user sets a date
     */
	startDate : false,
	
	//private var that is used when an event is dragged to another location
	dragEvent : false,
	
	//all the grid appointments are stored in this array. First index is day and second is the dom ID.
	appointments : Array(),
	
	//The remote database ID's can be stored in this array. Useful for database updates
	remoteEvents : Array(),
	
	//domids that need to be moved along with another. When an event spans multiple days
	domIds : Array(),
	
	//eventIdToDomId : {},
	
	//amount of days to display
	days : 1,
	
	selected : Array(),
	
	writePermission : false,

	// private
    initComponent : function(){
        GO.grid.MonthGrid.superclass.initComponent.call(this);
	
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
		    "eventResize" : true,	    
		    "eventDblClick" : true
	
	    });
	    
    
    
	    if(this.store){
	        this.setStore(this.store, true);
	    }

		if(!this.startDate)
		{
			//lose time
			var date = new Date();
			this.startDate=Date.parseDate(date.format(this.dateFormat), this.dateFormat);
		}
		
		this.configuredDate=this.startDate;
    },

	//build the html grid
	onRender : function(ct, position){
		
		GO.grid.MonthGrid.superclass.onRender.apply(this, arguments);
		
		//important to do here. Don't remember why :S
		this.setDate(this.startDate, false);
		

		
		//if this is not set the grid does not display well when I put a load mask on it.
		this.body.setStyle("overflow", "hidden");
		
		//Don't select things inside the grid
		this.body.unselectable();

		//this.renderMonthView();
		
		this.setStore(this.store);
		
		this.initDD();
	},
	
	renderMonthView : function()
	{
	
		this.body.update('');
		
		
		
		var currentMonthStr = this.configuredDate.format('Ym');
		var currentDate = new Date();
		var currentDateStr = currentDate.format('Ymd');
		
		
		//get content size of element
		var ctSize = this.container.getSize(true);
		
		
		
		this.monthGridTable = Ext.DomHelper.append(this.body,
			{
				tag: 'table', 
				id: Ext.id(), 
				cls: "x-monthGrid-table", 
				style: "width:"+ctSize['width']+"px;height:"+ctSize['height']+"px;"
				
			},true);
		
		this.tbody = Ext.DomHelper.append(this.monthGridTable,
			{
				tag: 'tbody'
			}); 
		
		var currentRow = Ext.DomHelper.append(this.tbody,
			{
				tag: 'tr'
			});
	
		var weekDay=0;
		var cellClass = '';
		var dateFormat;
		
		var rowHeight = 100/(this.days/7);
		
		this.gridCells={};
		for(var day=0;day<this.days;day++)
		{	
			var dt = this.startDate.add(Date.DAY, day);
			
			if(day == 0 || dt.format('j')==1)
			{
				dateFormat = 'j F';
			}else
			{
				dateFormat = 'j';
			}
			
			
			var weekday = dt.format('w');
			
			var monthStr = dt.format('Ym');
			
			var dateStr = dt.format('Ymd');
			
			if(dateStr==currentDateStr)
			{
				cellClass = 'x-monthGrid-cell-today';
			}else if(monthStr==currentMonthStr && (weekday==0 || weekday==6))
			{
				cellClass = 'x-monthGrid-cell-weekend';
			}else if (monthStr==currentMonthStr)
			{
				cellClass = 'x-monthGrid-cell-current';
			}else
			{
				cellClass = '';
			}
			
			
			var id = 'd'+dateStr;
			
			var cell = Ext.DomHelper.append(currentRow,
				{
					tag: 'td', 
					id: id, 
					cls: cellClass, 
					style:'height:'+rowHeight+'%',
					children:[{
						tag: 'div',
						cls: 'x-monthGrid-cell-day-text',
						html: dt.format(dateFormat)
					}]
				}, true);
				
			//var dropTarget = new GO.calendar.dd.MonthDropTarget(cell, {
			//	ddGroup: 'month-grid',
			//	overClass: 'dd-over'
			//});
			
				
			this.gridCells[dateStr]=(cell);
				
			weekDay++
			if(weekDay==7)
			{
				weekDay = 0;
				var currentRow = Ext.DomHelper.append(this.tbody,
				{
					tag: 'tr'		
				}, true);
			}
		}
		
		
	},
	
	initDD :  function(){
		
		var dragZone = new GO.calendar.dd.MonthDragZone(this.body, {
            ddGroup: 'month-grid',
            scroll: false,
            monthGrid: this
        });
        
        var dropTarget = new GO.calendar.dd.MonthDropTarget(this.body, {
            ddGroup: 'month-grid',
            onNotifyDrop : function(dd, e, data) {
            		
            		//number of seconds moved
            		
            		var dragTime = data.dragDate.format('U');
            		var dropTime = data.dropDate.format('U');
            		
            		offsetDays = Math.round((dropTime-dragTime)/86400);
            		
            		var actionData = {offsetDays:offsetDays, dragDate: data.dragDate};
            		
            		var remoteEvent = this.elementToEvent(data.item.id);
            		
				
								if(remoteEvent['repeats'])
								{
									this.handleRecurringEvent("move", remoteEvent, actionData);
								}else
								{
									this.fireEvent("move", this, remoteEvent, actionData);
									
									this.removeEvent(remoteEvent.domId);
									remoteEvent.repeats=false;
									remoteEvent.startDate = remoteEvent.startDate.add(Date.DAY, offsetDays);
									remoteEvent.endDate = remoteEvent.endDate.add(Date.DAY, offsetDays);
									remoteEvent.start_time = remoteEvent.startDate.format('U');
									remoteEvent.end_time = remoteEvent.endDate.format('U');									
									this.addMonthGridEvent(remoteEvent);
								}
            		
            		
            	},
            scope : this
        });
	},
	


    
  onResize : function(adjWidth, adjHeight, rawWidth, rawHeight){
    //Ext.grid.GridPanel.superclass.onResize.apply(this, arguments);

		if(this.monthGridTable)
		{
			this.monthGridTable.setSize(adjWidth, adjHeight);
		}	
  },
	setStore : function(store, initial){
    if(!initial && this.store){
    	this.store.un("beforeload", this.reload);
        this.store.un("datachanged", this.reload);
        this.store.un("clear", this.reload);
    }
    if(store){
    	store.on("beforeload", this.mask, this);
        store.on("datachanged", this.reload, this);
        store.on("clear", this.reload, this);
        
    }
    this.store = store;
  },
  
  setStoreBaseParams : function(){
  	this.store.baseParams['start_time']=this.startDate.format(this.dateTimeFormat);
    this.store.baseParams['end_time']=this.endDate.format(this.dateTimeFormat);
  },
	
	getFirstDateOfWeek : function(date)
	{
		//Calculate the first day of the week		
		var weekday = date.getDay();
		return date.add(Date.DAY, this.firstWeekday-weekday);
	},	
	mask : function()
	{
		if(this.rendered)
		{
		 	this.body.mask(GO.lang.waitMsgLoad,'x-mask-loading');
		}
	},	
	unmask : function()
	{
		if(this.rendered)
		{
			this.body.unmask();		
		}
	},
	getSelectedEvent : function()
	{
		if(this.selected)
		{
			return this.elementToEvent(this.selected[0].id);
		}
	},
	isSelected : function(eventEl)
	{
		for (var i=0;i<this.selected.length;i++)
		{
			if(this.selected[i].id==eventEl)
			{
				return true;
			}
		}
		return false;
	},	
	clearSelection : function()
	{
		for (var i=0;i<this.selected.length;i++)
		{
			this.selected[i].removeClass('x-calGrid-selected');
		}
		this.selected=[];
	},
	selectEventElement : function(eventEl)
	{
		if(!this.isSelected(eventEl))
		{
			this.clearSelection();
			
			var elements = this.getRelatedDomElements(eventEl.id);
			
			for (var i=0;i<elements.length;i++)
			{			
				var element = Ext.get(elements[i]);
				element.addClass('x-calGrid-selected');
				this.selected.push(element);
			}
		}

	},
	addMonthGridEvent : function (eventData)
	{
		//the start of the day the event starts
		var eventStartDay = Date.parseDate(eventData.startDate.format('Ymd'),'Ymd');
		var eventEndDay = Date.parseDate(eventData.endDate.format('Ymd'),'Ymd');
		
		//get unix timestamps
		var eventStartTime = eventStartDay.format('U');
		var eventEndTime = eventEndDay.format('U');
		
		//ceil required because of DST changes!
		var daySpan = Math.round((eventEndTime-eventStartTime)/86400)+1;
		
		
		for(var i=0;i<daySpan;i++)
		{
			var date = eventStartDay.add(Date.DAY, i);
			
			eventData.domId = Ext.id();
			
			//related events for dragging
			if(daySpan>1)
			{
				if(!this.domIds[eventData.id])
				{
					this.domIds[eventData.id]=[];
				}				
				this.domIds[eventData.id].push(eventData.domId);
			}
			
			var col = Ext.get('d'+date.format('Ymd'));
			
			if(col)
			{
				var text = eventData.startDate.format('H:i')+' '+eventData['name'];
			
				var event = Ext.DomHelper.append(col,
					{
						tag: 'div', 
						id: eventData.domId, 
						cls: "x-calGrid-month-event-container", 
						html: text, 						
						qtip: eventData['tooltip'] 
					}, true);			
					
				this.registerEvent(eventData.domId, eventData);
				
				
				
				event.on('mousedown', function(e, eventEl){
				
					eventEl = Ext.get(eventEl).findParent('div.x-calGrid-month-event-container', 2, true);
					
					this.selectEventElement(eventEl);					
					this.clickedEventId=eventEl.id;
		
				}, this);
				
				event.on('dblclick', function(e, eventEl){
					
					eventEl = Ext.get(eventEl).findParent('div.x-calGrid-month-event-container', 2, true);
					
					//this.eventDoubleClicked=true;
					var event = this.elementToEvent(this.clickedEventId);
					
					if(event['repeats'] && this.writePermission)
					{
						this.handleRecurringEvent("eventDblClick", event, {});
					}else
					{
						
						this.fireEvent("eventDblClick", this, event, {singleInstance : this.writePermission});
					}
					
				}, this);	
			}
		}
	},
	
	/*
	 * Removes a single event and it's associated dom elements
	 */
	removeEvent : function(domId){		
		var ids = this.getRelatedDomElements(domId);
		
		if(ids)
		{
			for(var i=0;i<ids.length;i++)
			{
				var el = Ext.get(ids[i]);
				el.removeAllListeners();
				el.remove();
				
				this.unregisterDomId(ids[i]);
			}			
		}
	
		
	},
	
	unregisterDomId : function(domId)
	{
		delete this.remoteEvents[domId];
		
		var found = false;
		
		for(var e in this.domIds)
		{
			for(var i=0;i<this.domIds[e].length;i++)
			{
				if(this.domIds[e][i]==domId)
				{
					this.domIds[e].splice(i,1);
					found=true;
					break;
				}
			}
			if(found)
			{
				break;
			}
		}
		
		/*found=false;
		
		for(var e in this.eventIdToDomId)
		{
			for(var i=0;i<this.eventIdToDomId[e].length;i++)
			{
				if(this.eventIdToDomId[e][i]==domId)
				{
					this.eventIdToDomId[e].splice(i,1);
					found=true;
					break;
				}
			}
			if(found)
			{
				break;
			}
		}*/
	},
	
	/*
	 * Removes all dom elements associated with an remote id
	 
	
	removeRemoteEvent : function(event_id){
		
		var ids = this.eventIdToDomId[event_id];
		if(ids)
		{
			for(var i=0;i<ids.length;i++)
			{
				var el = Ext.get(ids[i]);
				el.removeAllListeners();
				el.remove();
				
				delete this.remoteEvents[ids[i]];
			}
		}
		delete this.eventIdToDomId[event_id];
		delete this.domIds[event_id];    	
	},
	*/
	setNewEventId : function(dom_id, new_event_id){	
		this.remoteEvents[dom_id].event_id=new_event_id;
  },
  
	handleRecurringEvent : function(fireEvent, event, actionData){
		
		//store them here so the already created window can use these values
		this.currentRecurringEvent = event;
		this.currentFireEvent=fireEvent;
		this.currentActionData = actionData;
		
		if(!this.recurrenceDialog)
		{
			
			this.recurrenceDialog = new Ext.Window({				
				width:400,
				autoHeight:true,
				closeable:false,
				closeAction:'hide',
				plain:true,
				border: false,
				title:GO.calendar.lang.recurringEvent,
				modal:false,
				html: GO.calendar.lang.deleteRecurringEvent,
				buttons: [{
						text: GO.calendar.lang.singleOccurence,
						handler: function(){
							
							this.currentActionData.singleInstance=true;
							
							
							
							var remoteEvent = this.currentRecurringEvent;
							
							this.fireEvent(this.currentFireEvent, this, remoteEvent , this.currentActionData);
							
							this.removeEvent(remoteEvent.domId);		
							remoteEvent.repeats=false;						
							remoteEvent.startDate = remoteEvent.startDate.add(Date.DAY, offsetDays);
							remoteEvent.endDate = remoteEvent.endDate.add(Date.DAY, offsetDays);
							remoteEvent.start_time = remoteEvent.startDate.format('U');
							remoteEvent.end_time = remoteEvent.endDate.format('U');									
							this.addMonthGridEvent(remoteEvent);
							
							
							
							this.recurrenceDialog.hide();
						},
						scope: this
		   			},{
						text: GO.calendar.lang.entireSeries,
						handler: function(){
							
							this.currentActionData.singleInstance=false;
							
							this.fireEvent(this.currentFireEvent, this, this.currentRecurringEvent, this.currentActionData);
							this.recurrenceDialog.hide();
						},
						scope: this
		   			}]
				
			});
		}
		this.recurrenceDialog.show();
	},
  clearGrid : function()
	{
		this.appointments=Array();		
		this.remoteEvents=Array();
		this.domIds=Array();
	},	
  setDate : function(date, load)
  {    	
  	
  	var oldStartDate = this.startDate;
  	var oldEndDate = this.endDate;
  	
  	
  	this.configuredDate = date;
    	

  	//calculate first date of month
  	var firstDateOfMonth = date.getFirstDateOfMonth();

		var lastDateOfMonth = date.getLastDateOfMonth();
		
		//start at the monday of the week the current month starts in
		this.startDate=this.getFirstDateOfWeek(firstDateOfMonth);
				
		var startTime = this.startDate.format('U');
		var endTime = lastDateOfMonth.format('U');
		
		var daysToShow = ((endTime-startTime)/86400)+1;
		
		var rows = Math.ceil(daysToShow/7);
		
		this.days = rows*7;
	    	
    this.endDate = this.startDate.add(Date.DAY, this.days);
  	this.setStoreBaseParams();
  	
  	
  	if(!oldEndDate || !oldStartDate || oldEndDate.getElapsed(this.endDate)!=0 || oldStartDate.getElapsed(this.startDate)!=0)
  	{		
  		if(load)
  		{     		
    		this.store.reload();
  		}else
  		{	
    	  this.loadRequired=true;
  		}	    	
  	} 
  },
  
  reload : function()
  {
  	/*this.clearGrid();
  	if(!this.monthView)
  	{
  		this.createHeadings();
  	}    	*/
  	this.load();  	
  },
  
  load : function()
  {		
  	if(this.rendered)
  	{
  		this.clearGrid();
  		this.renderMonthView();
  		
  		this.writePermission = this.store.reader.jsonData.write_permission;
  		
			var records = this.store.getRange();
		
      for(var i = 0, len = records.length; i < len; i++){            
            
        var startDate = Date.parseDate(records[i].data['start_time'], this.dateTimeFormat);
				var endDate = Date.parseDate(records[i].data['end_time'], this.dateTimeFormat);
				
				var eventData = records[i].data;
				eventData['startDate']=startDate;
				eventData['endDate']=endDate;
			
				
				this.addMonthGridEvent (eventData);            
    	}
    	this.unmask();
      
    	this.loadRequired=false;
  	}
  
  },
  /**
   * An array of domId=>database ID should be kept so that we can figure out
   * which event to update when it's modified.
   * @param {String} domId The unique DOM id of the element
   * @param {String} remoteId The unique database id of the element     
   * @return void
   */
  registerEvent : function(domId, eventData)
  {
  	this.remoteEvents[domId]=eventData;
  	
		/*if(!this.eventIdToDomId[eventData.event_id])
		{
			this.eventIdToDomId[eventData.event_id]=[];
		}				
		this.eventIdToDomId[eventData.event_id].push(domId);*/
		
  },
  
  getEventDomElements : function(id)
  {
  	return GO.util.clone(this.domIds[id]);
  },
  
  getRelatedDomElements : function(eventDomId)
  {
  	var eventData = this.remoteEvents[eventDomId];
  	
  	if(!eventData)
  	{
  		return false;
  	}
  	var domElements = this.getEventDomElements(eventData.id);
  	
  	if(!domElements)
  	{
  		domElements = [eventDomId];
  	}
  	return domElements;
  },
  
  elementToEvent : function(elementId, allDay)
	{
		this.remoteEvents[elementId]['domId']=elementId;
		return this.remoteEvents[elementId];
	}
});


GO.calendar.dd.MonthDragZone = function(el, config) {
    config = config || {};
    Ext.apply(config, {
        ddel: document.createElement('div')
    });
    GO.calendar.dd.MonthDragZone.superclass.constructor.call(this, el, config);
};
 
Ext.extend(GO.calendar.dd.MonthDragZone, Ext.dd.DragZone, {
	onInitDrag: function(e) {
		
		if(!this.monthGrid.writePermission || this.monthGrid.remoteEvents[this.dragData.item.id]['private'])
		{
			return false;
		}else
		{
		
	    this.ddel.innerHTML = this.dragData.item.dom.innerHTML;
	    this.ddel.className = this.dragData.item.dom.className;
	    this.ddel.style.width = this.dragData.item.getWidth() + "px";
	    this.proxy.update(this.ddel);
	    
	    this.eventDomElements = this.monthGrid.getRelatedDomElements(this.dragData.item.id);
	    

	    var td = Ext.get(this.dragData.item).findParent('td', 10, true);
	    
	   	//this.proxyCount = eventDomElements.length;
	    
	    this.eventProxies=[];
	    this.proxyDragPos = 0;
	    for(var i=0;i<this.eventDomElements.length;i++)
	    {
	    	this.eventProxies.push(Ext.DomHelper.append(document.body,
			{
				tag: 'div', 
				id: Ext.id(), 
				cls: "x-calGrid-month-event-proxy", 
				style: "width:"+this.ddel.style.width+"px;"				
			},true));
			
	    	if (this.eventDomElements[i]==this.dragData.item.id)
	    	{
	    		this.proxyDragPos=i;
	    	}else
	    	{	 
	    	   	//hide event element
	    	   	Ext.get(this.eventDomElements[i]).setStyle({'position' : 'absolute', 'top':-10000, 'display':'none'});
	    	}
	    	   	
	    }
		}
	    
	    /*this.eventProxy =  Ext.DomHelper.append(document.body,
			{
				tag: 'div', 
				id: Ext.id(), 
				cls: "x-calGrid-month-event-proxy", 
				style: "width:"+this.ddel.style.width+"px;"				
			},true);
		var target = this.dragData.item.findParent('td', 10, true);
		this.lastTargetId=target.id;*/
	},
	
	removeEventProxies : function(){
		var proxies = Ext.query('div.x-calGrid-month-event-proxy');
		for (var i=0;i<proxies.length;i++)
		{
			Ext.get(proxies[i]).remove();
		}
		
		delete this.lastTdOverId;
		
		
		//unhide event elements
		for(var i=0;i<this.eventDomElements.length;i++)
	    {
			Ext.get(this.eventDomElements[i]).setStyle({'position' : 'static', 'top': '', 'display':'block'});
	    }
	},
	
	afterRepair : function(){
		GO.calendar.dd.MonthDragZone.superclass.afterRepair.call(this);
		
		this.removeEventProxies();
		
	},
	getRepairXY: function(e, data) {
	    data.item.highlight('#e8edff');
	    return data.item.getXY();
	},
  getDragData: function(e) {
  	if(!this.monthGrid.writePermission)
		{
			return false;
		}else
		{
      var target = Ext.get(e.getTarget());
           
      var td = target.parent();
      var dragDate = Date.parseDate(td.id.substr(1),'Ymd');
      
      if(target.hasClass('x-calGrid-month-event-container') && !this.monthGrid.remoteEvents[target.id]['private']) { 
      	
        
        return {
        	ddel:this.ddel, 
        	item:target,
        	dragDate: dragDate
        	};
      }else
      {
      	return false;
      }
		} 
  }
});


GO.calendar.dd.MonthDropTarget = function(el, config) {
    GO.calendar.dd.MonthDropTarget.superclass.constructor.call(this, el, config);
};
Ext.extend(GO.calendar.dd.MonthDropTarget, Ext.dd.DropTarget, {
    notifyDrop: function(dd, e, data) {
 		
 				if(!this.scope.writePermission)
 				{
 					return false;
 				}else
 				{
			 		var target = Ext.get(e.getTarget()).findParent('td', 10, true);
			 		
			 		data.dropDate = Date.parseDate(target.id.substr(1),'Ymd');
			 		
			 		dd.removeEventProxies();
	 		   	
	        this.el.removeClass(this.overClass);
	        target.appendChild(data.item);
	        
	        
	        if(this.onNotifyDrop)
					{
						if(!this.scope)
						{
							this.scope=this;
						}
						
						var onNotifyDrop = this.onNotifyDrop.createDelegate(this.scope);
						onNotifyDrop.call(this, dd, e, data);
					}
	        
	        
	        return true;
 				}
    },
    
    notifyOver : function(dd, e, data){
        var tdOver = Ext.get(e.getTarget()).findParent('td', 10, true);
        
        if(tdOver)
        {
	        if(dd.lastTdOverId!=tdOver.id)
	        {
	        	var currentTd=tdOver;
	        	for(var i=0;i<dd.proxyDragPos;i++)
	        	{
	        		if(currentTd)
	        		{
		        		var nextTd = currentTd.prev('td');
		        		if(!nextTd)
		        		{
		        			//try to find td on previous row
		        			var tr = currentTd.parent();
		        			tr = tr.prev('tr');
		        			if(tr)
		        			{
		        				var nextTd = tr.last();
		        				if(nextTd)
		        				{
		        					nextTd = Ext.get(nextTd);
		        				}
		        			}
		        		}  
		        		currentTd = nextTd; 
	        		}	        		
	        		if(nextTd)
	        		{	    
	        			dd.eventProxies[i].insertAfter(nextTd.first());		
	   					dd.eventProxies[i].setStyle({'position' : 'static', 'top': '', 'display':'block'});
	        		}else
	        		{
	        			dd.eventProxies[i].setStyle({'position' : 'absolute', 'top':-10000, 'display':'none'});
	        		}        			
        		
	        		    		
	        	}
	        	
	        	dd.eventProxies[i].insertAfter(tdOver.first());	
	        	var currentTd=tdOver;
	        	for(var i=dd.proxyDragPos+1;i<dd.eventProxies.length;i++)
	        	{
	        		if(currentTd)
	        		{
		        		var nextTd = currentTd.next('td');
		        		if(!nextTd)
		        		{
		        			//try to find td on next row
		        			var tr = currentTd.parent();
		        			tr = tr.next('tr');
		        			if(tr)
		        			{
		        				var nextTd = tr.first();
		        				if(nextTd)
		        				{
		        					nextTd = Ext.get(nextTd);
		        				}
		        			}
		        		}
		        		
		        		currentTd = nextTd;
	        		}
	        		
	        		if(nextTd)
	        		{	     
	        			dd.eventProxies[i].insertAfter(nextTd.first()); 			
	   					dd.eventProxies[i].setStyle({'position' : 'static', 'top': '', 'display':'block'});
	        		}else
	        		{
	        			dd.eventProxies[i].setStyle({'position' : 'absolute', 'top':-10000, 'display':'none'});
	        		}
	        	}
	        	
	        }
	        
	        dd.lastTdOverId=tdOver.id;
        }
        return this.dropAllowed;
    }
    
    
});