/**
 * @copyright Copyright Intermesh 2007
 * @author Merijn Schering <mschering@intermesh.nl>
 * 
 * This file is part of Group-Office.
 * 
 * Group-Office is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 * 
 * See file /LICENSE.GPL
 */
 
calendar = function(){
	
	var CalendarGrid, ds;
	var firstCalendarId=0;
	var calendarList;
	var calendarCheckboxes;
	var eventDialog;

	return {
		init : function(){
		
			this.calendarId=0;
			// initialize state manager, we will use cookies
			//Ext.state.Manager.setProvider(new Ext.state.CookieProvider());

			
			var datePicker = new Ext.DatePicker();
			
			datePicker.on("select", function(DatePicker, Date){
					CalendarGrid.gotoDate(Date);
				},this);
			
			datePicker.render("DatePicker");
			
			
			
			ds = new Ext.data.Store({

				proxy: new Ext.data.HttpProxy({
					url: 'json.php'
				}),
				baseParams: {task: 'events'},
				reader: new Ext.data.JsonReader({
					root: 'results',
					id: 'id'
				}, [
				{name: 'id'},
				{name: 'event_id'},
				{name: 'name' },				
				{name: 'start_time'},	
				{name: 'end_time'},
				{name: 'tooltip'}
				])
			});
        	
        	CalendarGrid = new Ext.calendar.CalendarGrid({store: ds, days: 5});
			
			
			
			var westPanel;
			
			var viewport = new Ext.Viewport({
				layout:'border',
	            items:[
	               { // raw
	                    region:'north',
	                    el: 'northDiv',
	                    height:30,
	  					resizable:false,
						split: false,
						titlebar: false,
						collapsible: false,
						tbar: [{
							id: 'add',
							icon: GOimages['add'],
							text: GOlang['cmdAdd'],
							cls: 'x-btn-text-icon',
							handler: this.refresh,
							scope: this
						},{
							id: 'refresh',
							icon: GOimages['refresh'],
							text: GOlang['cmdRefresh'],
							cls: 'x-btn-text-icon',
							handler: this.refresh,
							scope: this
						}
						
						]
						
	                },westPanel = new Ext.Panel({
	                    region:'west',
	                    contentEl: 'westDiv',
	                    split:true,
	                    titlebar: false,
						autoScroll:true,
						closeOnTab: true,
						width: 212,
						split:false
	                }), {
	                    region:'center',
	                    contentEl: 'centerDiv',
	                    split:true,
	                    titlebar: false,
						autoScroll:true,
						split:true,
						layout: 'fit',
						items: [CalendarGrid]
	                }]
			});
			
			westPanel.body.addClass('blue-panel');
			
			calendarList= Ext.get("calendarList");
			calendarList.on('click', this.calendarClicked, this);
			
			
			this.createCalendarList();
			this.createCalendarGrid();
			
			eventDialog = new Ext.calendar.EventDialog(CalendarGrid);
			
		

		},
		
		refresh : function() {
			/*var selectedCalendars=new Array();
						
			for(var calendarId in calendarCheckboxes)
			{
				
				if(typeof(calendarCheckboxes[calendarId]) != 'function' && calendarCheckboxes[calendarId].getValue())
				{
					selectedCalendars.push(calendarId);
				}
			}*/
			
			//ds.baseParams['calendars']=Ext.encode(selectedCalendars);
			ds.reload();
			
		
		},
		createCalendarList : function(el){
            function reformatDate(feedDate){
                var d = new Date(Date.parse(feedDate));
                return d ? d.dateFormat('D M j, Y, g:i a') : '';
            }
            
            
           
            


           
           var conn = new Ext.data.Connection();
				conn.request({
				url: 'json.php',
				params: {task: 'calendar_groups'},
				callback: function(options, success, response)
				{
					var calendarGroups = Ext.decode(response.responseText);
					if(!success)
					{				
						Ext.MessageBox.alert('Failed', response['errors']);
					}else
					{
						var groupTpl = new Ext.DomHelper.Template('<h1>{name}</h1>');
           				var calTpl = new Ext.DomHelper.Template('<a id="calendar-{id}" href="#">{name}</a>');
           				
           				
           				
           				
						calendarCheckboxes = new Array();
						
						for (var calGroup in calendarGroups)
						{
							var g = calendarGroups[calGroup];
							if(typeof(g) != 'function')
							{
	            				groupTpl.append(calendarList.dom, g);
								
								for (var calendar in g.calendars)
								{
									
									var c = g.calendars[calendar];
									if(typeof(c) != 'function')
									{
										if(this.calendarId==0)
										{
											this.calendarId=c.id;									
										}
										calTpl.append(calendarList.dom, c);
										
										/*calendarCheckboxes[c.id] = new Ext.form.Checkbox(
											{
												autoCreate:true,
												name: 'calendars_'+c.id,
												value: c.id,
												boxLabel: c.name
											});
										calendarCheckboxes[c.id].render(calendarList);*/
									}
								}
							}
						}
						
						//calendarCheckboxes[firstCalendarId].setValue(true);
						ds.baseParams['calendars']=Ext.encode([this.calendarId]);
						this.changeActiveCalendar(this.calendarId);									
						ds.load();
					}
					
				},
				scope: this
			});
			

            
        },
        
        calendarClicked : function(e)
        {
	        // find the "a" element that was clicked
	        var a = e.getTarget('a');
	        if(a){
	            e.preventDefault();
	            
	            this.calendarId = a.id.substr(9);
				ds.baseParams['calendars']=Ext.encode([this.calendarId]);
				this.changeActiveCalendar(this.calendarId);
				ds.reload();
	        }  
    
        	
        },
        changeActiveCalendar : function(calendarId){
           	calendarList.select('a').removeClass('selected');
            Ext.fly('calendar-'+calendarId).addClass('selected');
        },
        
        createCalendarGrid : function()
        {
        
        	
			
			

			
			CalendarGrid.on("eventDoubleClick", function(CalGrid, newEvent){
				
				eventDialog.show(newEvent['remoteId']);
				
			}, this);
				
			
			CalendarGrid.on("create", function(CalGrid, newEvent){
					//CalendarGrid.mask();
					var formValues={};
					
					formValues['start_date'] = newEvent['startDate'].format(GOsettings['date_format']);					
					formValues['start_hour'] = newEvent['startDate'].format("H");
					formValues['start_min'] = newEvent['startDate'].format("i");
					
					formValues['end_date'] = newEvent['endDate'].format(GOsettings['date_format']);
					formValues['end_hour'] = newEvent['endDate'].format("H");
					formValues['end_min'] = newEvent['endDate'].format("i");
					
					formValues['calendar_id']=this.calendarId;
					

			
					eventDialog.show(0, formValues);
					
				}, this);
				
			CalendarGrid.on("move", function(CalGrid, event){
					CalendarGrid.mask();
			
					var conn = new Ext.data.Connection();
					conn.request({
						url: 'action.php',
						params: {
							task: 'update_event', 
							event_id: event['remoteId'], 
							'startDate': 
							event['startDate'].format(CalendarGrid.dateTimeFormat)},
						callback: function(options, success, response)
						{
							var response = Ext.decode(response.responseText);
							if(!success)
							{				
								Ext.MessageBox.alert(GOlang['strError'], response['errors']);
							}
							CalendarGrid.unmask();
							CalendarGrid.store.reload();
						},
						scope: CalendarGrid
					});
					
				});
				
			CalendarGrid.on("resize", function(CalGrid, event, newEventName){
					CalendarGrid.mask();

					var conn = new Ext.data.Connection();
					conn.request({
						url: 'action.php',
						params: {
							task: 'update_event', 
							'event_id': event['remoteId'], 
							'endDate': event['endDate'].format(CalendarGrid.dateTimeFormat)
							},
						callback: function(options, success, response)
						{
							var response = Ext.decode(response.responseText);
							if(!success)
							{				
								Ext.MessageBox.alert(GOlang['strError'], response['errors']);
							}
							CalendarGrid.unmask();
							CalendarGrid.store.reload();
						},
						scope: this
					});
					
				});
				
			CalendarGrid.on("eventDblClick", function(CalGrid, event){			
				eventDialog.show(event['remoteId']);
			});
        }
	}

}();

Ext.EventManager.onDocumentReady(function(){
	calendar.init();
	},
	calendar);
