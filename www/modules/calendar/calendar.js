
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
			tb.addButton({
					id: 'add',
					icon: GOimages['add'],
					text: GOlang['cmdAdd'],
					cls: 'x-btn-text-icon',
					handler: this.refresh,
					scope: this
				}
				);
				
			tb.addButton({
					id: 'refresh',
					icon: GOimages['refresh'],
					text: GOlang['cmdRefresh'],
					cls: 'x-btn-text-icon',
					handler: this.refresh,
					scope: this
				}
				);
			
			
			var toolbarPanel = new Ext.ContentPanel('northDiv',{toolbar: tb});
			layout.add('north', toolbarPanel);

			var navigationPanel = new Ext.ContentPanel('westDiv');
			layout.add('west', navigationPanel);
			
			var centerPanel = new Ext.ContentPanel('centerDiv', { fitToFrame:true, resizeEl: 'CalendarGrid'});
			

			layout.add('center', centerPanel);
			
			layout.getRegion("west").bodyEl.addClass("bluePanel");

			//layout.restoreState();
			layout.endUpdate();
			
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
        	
        	CalendarGrid = new Ext.calendar.CalendarGrid('CalendarGrid', {store: ds, days: 5});
			
			
			
			CalendarGrid.render();
			//ds.load();
				
			
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

Ext.EventManager.onDocumentReady(function(){
	calendar.init();
	},
	calendar);
