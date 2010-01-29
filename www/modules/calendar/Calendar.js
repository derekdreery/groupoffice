/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @version $Id$
 * @author Merijn Schering <mschering@intermesh.nl>
 */
 
GO.calendar.formatQtip = function(data)
{
	var df = 'Y-m-d H:i';
	
	if(!data.startDate)
		data.startDate = Date.parseDate(data.start_time, df);
	
	if(!data.endDate)
		data.endDate = Date.parseDate(data.end_time, df);
	
	var new_df = GO.settings.time_format;
	if(data.startDate.format('Ymd')!=data.endDate.format('Ymd'))
	{
		new_df = GO.settings.date_format+' '+GO.settings.time_format;
	}

	var str = GO.calendar.lang.startsAt+': '+data.startDate.format(new_df)+'<br />'+
	GO.calendar.lang.endsAt+': '+data.endDate.format(new_df);
	
	if(data.location!='')
	{
		str += '<br />'+GO.calendar.lang.location+': '+data.location;
	}
	
	if(data.description!='')
	{
		str += '<br /><br />'+data.description;
	}
	
	return str;
}

GO.calendar.MainPanel = function(config){
	
	if(!config)
	{
		config = {};
	}			
	var datePicker = new Ext.DatePicker();
	
	datePicker.on("select", function(DatePicker, DateObj){			
		this.setDisplay({
			date: DateObj
		});
	},this);
		
	GO.calendar.calendarsStore = this.calendarsStore = new GO.data.JsonStore({
		url: GO.settings.modules.calendar.url+'json.php',
		baseParams: {
			'task': 'calendars'
		},
		root: 'results',
		totalProperty: 'total',
		id: 'id',
		fields:['id','name','user_name','group_id', 'group_name'],
		remoteSort:true
	});

	this.viewsStore = new GO.data.JsonStore({
		url: GO.settings.modules.calendar.url+'json.php',
		baseParams: {
			'task': 'views'
		},
		root: 'results',
		totalProperty: 'total',
		id: 'id',
		fields:['id','name','user_name','merge','owncolor'],
		remoteSort:true
	});

	GO.calendar.resourcesStore = this.resourcesStore = new Ext.data.GroupingStore({
		baseParams: {
			'task': 'calendars',
			'resources' : 'true'
		},
		reader: new Ext.data.JsonReader({
			root: 'results',
			id: 'id',
			totalProperty: 'total',
			fields:['id','name','user_name','group_id', 'group_name']
		}),
		proxy: new Ext.data.HttpProxy({
			url: GO.settings.modules.calendar.url+'json.php'
		}),
		sortInfo:{
			field: 'name',
			direction: "ASC"
		},
		groupField:'group_name',
		remoteSort:true
	});

	this.calendarsStore.on('load', function(){
		if(this.state.displayType!='view' && this.group_id==1)
		{
			var record = this.calendarsStore.getById(this.state.calendar_id);
			if(!record)
			{
				record = this.calendarsStore.getAt(0);
				this.state.calendar_id = record.data.id;
			}
			this.calendarList.getSelectionModel().selectRecords(new Array(record));
			this.setDisplay(this.state);

			var title = record.data.name;
			if(title.length){
				if(this.calendarTitle.td){
					//Ext 2
					this.calendarTitle.td.innerHTML = title;
				}else
				{
					//Ext 3
					this.calendarTitle.setText(title);
				}
			}
		}
	}, this);

	this.viewsStore.on('load', function(){

		this.viewsList.setVisible(this.viewsStore.data.length);
		this.calendarListPanel.doLayout();
		
		if(this.state.displayType=='view' && this.viewsStore.data.length)
		{
			var record = this.viewsStore.getById(this.state.view_id);
			if(!record)
			{
				record = this.viewsStore.getAt(0);
				this.state.view_id = record.data.id;
			}
			this.viewsList.getSelectionModel().selectRecords(new Array(record));
			this.setDisplay(this.state);

			var title = record.data.name;
			if(title.length){
				if(this.calendarTitle.td){
					//Ext 2
					this.calendarTitle.td.innerHTML = title;
				}else
				{
					//Ext 3
					this.calendarTitle.setText(title);
				}
			}
		}
	}, this);

	this.resourcesStore.on('load', function(){

		this.resourcesList.setVisible(this.resourcesStore.data.length);
		this.calendarListPanel.doLayout();

		if(this.state.displayType!='view' && this.group_id>1 && this.resourcesStore.data.length)
		{
			
			var record = this.resourcesStore.getById(this.state.calendar_id);
			if(!record)
			{
				record = this.resourcesStore.getAt(0);
				this.state.calendar_id = record.data.id;
			}
			this.resourcesList.getSelectionModel().selectRecords(new Array(record));
			this.setDisplay(this.state);

			var title = record.data.name;
			if(title.length){
				if(this.calendarTitle.td){
					//Ext 2
					this.calendarTitle.td.innerHTML = title;
				}else
				{
					//Ext 3
					this.calendarTitle.setText(title);
				}
			}
		}
	}, this);

	this.myCalendar = new GO.data.JsonStore({
		url: GO.settings.modules.calendar.url+"json.php",
		baseParams: {
			'task': 'my_calendar'
		},
		root: 'data',
		id: 'id',
		fields:['id','group_id','name']
	});

	this.myCalendar.load();

	this.calendarList = new GO.grid.GridPanel({
		border: false,
		layout:'fit',
		title:GO.calendar.lang.calendars,
		store: this.calendarsStore,
		cls: 'go-grid3-hide-headers',
		autoScroll:true,
		columns:[{
			header:GO.lang.strName,
			dataIndex: 'name',
			id:'name',
			width:188
		}],
		view:new Ext.grid.GridView({
			forceFit:true
		}),
		tbar: [{
			text : GO.calendar.lang.myCalendar,
			handler : function() {
				this.setDisplay({
					group_id: this.myCalendar.data.items[0].data.group_id,
					calendar_id: this.myCalendar.data.items[0].data.id,
					calendar_name: this.myCalendar.data.items[0].data.name,
					saveState:true
				});

				var title = this.myCalendar.data.items[0].data.name;

				if(title.length){
					if(this.calendarTitle.td){
						//Ext 2
						this.calendarTitle.td.innerHTML = title;
					}else
					{
						//Ext 3
						this.calendarTitle.setText(title);
					}
				}
			},
			scope : this
		}],
		sm: new Ext.grid.RowSelectionModel()
	});

	this.viewsList = new GO.grid.GridPanel({
		border: false,
		layout:'fit',
		title:GO.calendar.lang.views,
		store: this.viewsStore,
		cls: 'go-grid3-hide-headers',
		autoScroll:true,
		columns:[{
			header:GO.lang.strName,
			dataIndex: 'name',
			id:'name',
			width:188
		}],
		view:new Ext.grid.GridView({
			forceFit:true,
			autoFill:true
		}),
		sm: new Ext.grid.RowSelectionModel()
	});

	this.resourcesList = new GO.grid.GridPanel({
		border: false,
		title:GO.calendar.lang.resources,
		layout:'fit',
		store: this.resourcesStore,
		cls: 'go-grid3-hide-headers',
		autoScroll:true,
		columns:[{
			header:GO.lang.strName,
			dataIndex: 'name',
			id:'name',
			width:188
		},{
			header:GO.calendar.lang.group,
			dataIndex: 'group_name',
			id:'group_name',
			width:188
		}],
		view: new Ext.grid.GroupingView({
			forceFit:true,
			hideGroupedColumn:true,
			groupTextTpl: '{text} ({[values.rs.length]})'
		}),
		sm: new Ext.grid.RowSelectionModel()
	});
   
	this.calendarList.on('rowclick', function(grid, rowIndex)
	{
		this.viewsList.getSelectionModel().clearSelections();
		this.resourcesList.getSelectionModel().clearSelections();
		var calendars = this.calendarList.getSelectionModel().selections.items;
		
		if (calendars.length<2) {
			this.setDisplay({
				group_id:grid.store.data.items[rowIndex].data.group_id,
				calendar_id: grid.store.data.items[rowIndex].id,
				calendar_name: grid.store.data.items[rowIndex].data.name,
				saveState:true
			});

			var title = grid.store.data.items[rowIndex].data.name;
			if(title.length){
				if(this.calendarTitle.td){
					//Ext 2
					this.calendarTitle.td.innerHTML = title;
				}else
				{
					//Ext 3
					this.calendarTitle.setText(title);
				}
			}
		} else {
			var group_ids = [];
			var cal_ids = [];
			var cal_names = [];
			var title = '';
			for (var i in calendars) {
				if (!isNaN(i)) {
					group_ids[i] = calendars[i].data.group_id;
					cal_ids[i] = calendars[i].data.id;
					cal_names[i] = calendars[i].data.name;
					if (i>0)
						title = title+' & ';
					title = title+calendars[i].data.name;
				}
			}
			this.setDisplay({
				group_id:group_ids,
				calendars: cal_ids,
				calendar_name: cal_names,
				saveState:true
			});

			if(title.length){
				if(this.calendarTitle.td){
					//Ext 2
					this.calendarTitle.td.innerHTML = title;
				}else
				{
					//Ext 3
					this.calendarTitle.setText(title);
				}
			}
		}
	}, this);
	
	this.viewsList.on('rowclick', function(grid, rowIndex)
	{
		this.calendarList.getSelectionModel().clearSelections();
		this.resourcesList.getSelectionModel().clearSelections();

		this.setDisplay({
				group_id:0,
				view_id: grid.store.data.items[rowIndex].id,
				calendar_name: grid.store.data.items[rowIndex].data.name,
				saveState:true,
				merge: grid.store.data.items[rowIndex].data.merge,
				owncolor: grid.store.data.items[rowIndex].data.owncolor
			});

		var title = grid.store.data.items[rowIndex].data.name;
		if(title.length){
			if(this.calendarTitle.td){
				//Ext 2
				this.calendarTitle.td.innerHTML = title;
			}else
			{
				//Ext 3
				this.calendarTitle.setText(title);
			}
		}
	}, this);	

	this.resourcesList.on('rowclick', function(grid, rowIndex)
	{
		this.calendarList.getSelectionModel().clearSelections();
		this.viewsList.getSelectionModel().clearSelections();
        
		this.setDisplay({
			group_id:grid.store.data.items[rowIndex].data.group_id,
			calendar_id: grid.store.data.items[rowIndex].id,
			calendar_name: grid.store.data.items[rowIndex].data.name,
			saveState:true
		});

		var title = grid.store.data.items[rowIndex].data.name;
		if(title.length){
			if(this.calendarTitle.td){
				//Ext 2
				this.calendarTitle.td.innerHTML = title;
			}else
			{
				//Ext 3
				this.calendarTitle.setText(title);
			}
		}
	}, this);


	this.calendarListPanel = new Ext.Panel({
		border:true,
		region:'center',
		layout:'accordion',
		/*layoutConfig:{
			autoWidth : false,
			titleCollapse:true,
			animate:false,
			activeOnTop:false
		},*/
		items: [
		this.calendarList,
		this.viewsList,
		this.resourcesList
		]
	});



	this.daysGridStore = new GO.data.JsonStore({

		url: GO.settings.modules.calendar.url+'json.php',
		baseParams: {
			task: 'events'
		},
		root: 'results',
		id: 'id',
		fields:['id','event_id','name','start_time','end_time','description', 'repeats', 'private','location', 'background', 'read_only', 'task_id', 'contact_id']
	});
	
	this.monthGridStore = new GO.data.JsonStore({

		url: GO.settings.modules.calendar.url+'json.php',
		baseParams: {
			task: 'events'
		},
		root: 'results',
		id: 'id',
		fields:['id','event_id','name','start_time','end_time','description', 'repeats', 'private','location', 'background', 'read_only', 'task_id', 'contact_id']
	});

	this.daysGrid = new GO.grid.CalendarGrid(
	{
		id: 'days-grid',
		store: this.daysGridStore, 
		border: false,
		firstWeekday: parseInt(GO.settings.first_weekday),
		keys:[ {
			key:  Ext.EventObject.DELETE,
			fn: function(){
			},
			scope: this
		}]
	});
	
	this.monthGrid = new GO.grid.MonthGrid({
		id: 'month-grid',
		store: this.monthGridStore,
		border: false,
		layout:'fit',
		firstWeekday: parseInt(GO.settings.first_weekday)
		
	});
	
	this.viewGrid = new GO.grid.ViewGrid({
		id: 'view-grid',
		border: false,
		firstWeekday: parseInt(GO.settings.first_weekday)
	});
	
	this.viewGrid.on('zoom', function(conf){	
		this.viewsList.getSelectionModel().clearSelections();

		var r = this.calendarList.store.getById(conf.calendar_id);
		this.calendarList.getSelectionModel().selectRecords([r]);
		this.calendarList.expand();
		this.setDisplay(conf);
	}, this);
	
	
	this.listGrid = new GO.calendar.ListGrid({
		id: 'list-grid',
		border: false,
		firstWeekday: parseInt(GO.settings.first_weekday)
	});

	this.listStore = this.listGrid.store;
 


	this.displayPanel = new Ext.Panel({
		region:'center',
		titlebar: false,
		autoScroll:false,
		layout: 'card',
		activeItem: 0,
		border: true,
		split: true,
		cls: 'cal-display-panel',
		tbar: [this.calendarTitle = new Ext.Toolbar.TextItem({
			text:'Calendar'
		}),'-',{
			iconCls: 'btn-left-arrow',
			text: GO.lang.cmdPrevious,
			cls: 'x-btn-text-icon',
			handler: function(){
							
				var displayDate = this.getActivePanel().configuredDate;
				if(this.displayType=='month')
				{
					displayDate = displayDate.add(Date.MONTH, -1);
				}else
				{
					var days = this.days > 4 ? 7 : 1;
					displayDate = displayDate.add(Date.DAY, -days);
				}
							
				this.setDisplay({
					date: displayDate
				});
			},
			scope: this
		},this.periodInfoPanel = new Ext.Panel({
			html: '',
			plain:true,
			border:true,
			cls:'cal-period'
		}),{
			iconCls: 'btn-right-arrow',
			text: GO.lang.cmdNext,
			cls: 'x-btn-text-icon',
			handler: function(){
				var displayDate = this.getActivePanel().configuredDate;
				if(this.displayType=='month')
				{
					displayDate = displayDate.add(Date.MONTH, 1);
				}else
				{
					var days = this.days > 4 ? 7 : 1;
					displayDate = displayDate.add(Date.DAY, days);
				}
							
				this.setDisplay({
					date: displayDate
				});
			},
			scope: this
		}],
		
		items: [this.daysGrid, this.monthGrid, this.viewGrid, this.listGrid]
	});
	

						
	var tbar = [{
		iconCls: 'btn-add',
		text: GO.lang['cmdAdd'],
		cls: 'x-btn-text-icon',
		handler: function(){
							
			GO.calendar.eventDialog.show({
				calendar_id: this.displayType != 'view' ? this.calendar_id : 0,
				calendar_name_id: this.displayType != 'view' ? this.calendar_name : ''
			});
										
		},
		scope: this
	},{
			
		iconCls: 'btn-delete',
		text: GO.lang['cmdDelete'],
		cls: 'x-btn-text-icon',
		handler: this.deleteHandler,
		scope: this
	},{
			
		iconCls: 'btn-refresh',
		text: GO.lang['cmdRefresh'],
		cls: 'x-btn-text-icon',
		handler: function(){
			/*this.calendarsStore.load();
										this.viewsStore.load();
										this.setDisplay();*/
			this.init();
		},
		scope: this
	},{
		iconCls: 'btn-settings',
		text: GO.lang.administration,
		cls: 'x-btn-text-icon',
		handler: function(){
			this.showAdminDialog();
		},
		scope: this
	},
	'-',
	this.dayButton = new Ext.Button({
		iconCls: 'btn-one-day',
		text: GO.calendar.lang.oneDay,
		cls: 'x-btn-text-icon',
		handler: function(){
			
			this.setDisplay({
				days:1,
				displayType: this.displayType == 'view' ? 'view' : 'days',
				calendar_name: this.calendar_name,
				view_id : this.view_id,		
				saveState:true
			});
		},
		scope: this
	}),
	this.workWeekButton = new Ext.Button({
		iconCls: 'btn-five-days',
		text: GO.calendar.lang.fiveDays,
		cls: 'x-btn-text-icon',
		handler: function(){
			
			this.setDisplay({
				days: 5,
				displayType: this.displayType == 'view' ? 'view' : 'days',
				calendar_name: this.calendar_name,
				view_id : this.view_id,				
				saveState:true
			});
		},
		scope: this
	}),
	this.weekButton = new Ext.Button({
		iconCls: 'btn-seven-days',
		text: GO.calendar.lang.sevenDays,
		cls: 'x-btn-text-icon',
		handler: function(){
		
			this.setDisplay({
				days: 7,
				displayType: this.displayType == 'view' ? 'view' : 'days',
				calendar_name: this.calendar_name,
				view_id : this.view_id,
				saveState:true
			});
		},
		scope: this
	}),this.monthButton= new Ext.Button({
		iconCls: 'btn-month',
		text: GO.calendar.lang.month,
		cls: 'x-btn-text-icon',
		handler: function(){
			
			this.setDisplay({
				displayType:'month',
				calendar_name: this.calendar_name,
				view_id : this.view_id,
				saveState:true
			});
		},
		scope: this
	}),
	this.listButton= new Ext.Button({
		iconCls: 'btn-list',
		text: GO.calendar.lang.list,
		cls: 'x-btn-text-icon',
		handler: function(item, pressed){
			
			this.setDisplay({
				displayType:'list',
				days: 7,
				calendar_name: this.calendar_name,
				view_id : this.view_id,
				saveState:true
			});
			        		
		},
		scope: this
	}),
	'-',
	this.printMessageButton = new Ext.Button({
		iconCls: 'btn-print',
		text: GO.lang.cmdPrint,
		cls: 'x-btn-text-icon',
		handler: function(){
			//this.getActivePanel().body.print({printCSS:'<style>.x-calGrid-grid-container{overflow:visible !important}}</style>'});
									
			var sD = this.getActivePanel().startDate;
			var eD = this.getActivePanel().endDate;
									
			var l = GO.settings.modules.calendar.url+'print.php?start_time='+sD.format('Y-m-d')+'&end_time='+eD.format('Y-m-d');
									
			if(this.displayType=='view')
			{
				l+='&view_id='+this.view_id;
			}else
			{
				l+='&calendar_id='+this.calendar_id;
			}
			document.location=l;
		},
		scope: this
	})
							
	];
	for(var i=0;i<GO.calendar.extraToolbarItems.length;i++)
	{
		tbar.push(GO.calendar.extraToolbarItems[i]);
	}
		
		
	config.layout='border';
	config.border=false;
	//config.tbar=;
	config.items=[
	new Ext.Panel({
		region:'north',
		height:32,
		baseCls:'x-plain',
		tbar:new Ext.Toolbar({
			cls:'go-head-tb',
			items: tbar
		})

	}),
				
	new Ext.Panel({
		region:'west',
		titlebar: false,
		autoScroll:false,
		//closeOnTab: true,
		width: 210,
		split:true,
		layout:'border',
		border:false,
		//plain:true,
		items:[
		new Ext.Panel({
			region:'north',
			border:true,
			height:194,
			split:true,
			baseCls:'x-plain',
			items:datePicker
		}),
		this.calendarListPanel]
	}),
	this.displayPanel
	];		
		
	GO.calendar.MainPanel.superclass.constructor.call(this, config);	
	
	
}

Ext.extend(GO.calendar.MainPanel, Ext.Panel, {
	/*
	 * The type of display. Can be days, month or view
	 */
	displayType : 'days',
	lastCalendarDisplayType : 'days',
	state : {},
	calendarId : 0,
	viewId : 0,
	group_id: 1,


	onShow : function(){        
		GO.calendar.MainPanel.superclass.onShow.call(this);
		this.daysGrid.scrollToLastPosition();
	},
	afterRender : function(){
		GO.calendar.MainPanel.superclass.afterRender.call(this);



  	
		//couldn't add key events to panels so I add this event to the whole doc
			
		
		GO.calendar.eventDialog.on('save', function(newEvent,oldDomId){
			
			if(this.displayType=='list')
			{
				this.setDisplay();
			}else
			{
				var activeGrid = this.getActivePanel();
				//reload grid if old or new event repeats. Do not reload if an occurence of a repeating event is modified
				if(newEvent.repeats|| (activeGrid.remoteEvents[oldDomId] && activeGrid.remoteEvents[oldDomId].repeats && activeGrid.remoteEvents[oldDomId].event_id==newEvent.event_id))
				{
					activeGrid.store.reload();
				}else
				{
					activeGrid.removeEvent(oldDomId);			
			
					switch(this.displayType)
					{
						case 'month':
							if(newEvent.calendar_id==this.calendar_id)
								GO.calendar.eventDialog.oldDomId=this.monthGrid.addMonthGridEvent(newEvent);
							break;
						case 'days':	
							if(newEvent.calendar_id==this.calendar_id)
								GO.calendar.eventDialog.oldDomId=this.daysGrid.addDaysGridEvent(newEvent, true);
							break;
								
						case 'view':						
							GO.calendar.eventDialog.oldDomId=this.viewGrid.addViewGridEvent(newEvent);
							break;
					}
				}
			}
						
		}, this);


		if(GO.tasks)
		{
			GO.tasks.taskDialog = new GO.tasks.TaskDialog();
			GO.tasks.taskDialog.on('save', function()
			{
				this.init();
			}, this);
		}	

		GO.calendar.groupDialog = new GO.calendar.GroupDialog();
		GO.calendar.groupDialog.on('save', function(e, group_id, fields)
		{			
			if(group_id == 1)
			{
				GO.calendar.defaultGroupFields = fields;
			}			
			GO.calendar.groupsGrid.store.load({
				callback:function(){
					GO.calendar.eventDialog.resourceGroupsStore.reload();
				},
				scope:this
			});
			
								
		},this);
		
		
		this.state = Ext.state.Manager.get('calendar-state');
		if(!this.state)
		{
			this.state = {
				displayType:'days',
				days: 5,
				calendar_id:0,
				view_id: 0
			};
		}else
		{
			this.state = Ext.decode(this.state);
		}
		
		if(this.state.displayType=='view')
			this.state.displayType='days';

		this.state.calendar_id=GO.calendar.defaultCalendar.id;
		this.state.view_id=0;
				
		this.init();	
		this.createDaysGrid();

		this.on('show', function(){
			this.refresh();
		}, this);
	},
	
	init : function(){
		this.calendarsStore.load();
		this.viewsStore.load();	
		this.resourcesStore.load();
	},
	
	deleteHandler : function(){
		switch(this.displayType)
		{
			case 'days':
				var event = this.daysGrid.getSelectedEvent();
				var callback = function(event, refresh){					
					if(refresh)
					{
						this.daysGrid.store.reload();
					}else
					{
						this.daysGrid.removeEvent(event.domId);
					}
				};			
				break;
			
			case 'month':
				var event = this.monthGrid.getSelectedEvent();
				var callback = function(event, refresh){
					if(refresh)
					{
						this.monthGrid.store.reload();
					}else
					{
						this.monthGrid.removeEvent(event.domId);
					}
				};			
				break;
			
			case 'view':
				var event = this.viewGrid.getSelectedEvent();
				var callback = function(event, refresh){
					if(refresh)
					{
						this.viewGrid.reload();
					}else
					{
						this.viewGrid.removeEvent(event.domId);
					}
				};			
				break;
			
			case 'list':
				var event = this.listGrid.getSelectedEvent();
				var callback = function(event, refresh){
					if(refresh)
					{
						this.listGrid.store.reload();
					}else
					{
						this.listGrid.removeEvent();//will remove the selected row.
					}					
				};			
				break;
		}
									
		if(event && !event.read_only && !event.task_id & !event.contact_id)
		{
			this.deleteEvent(event, callback);
		}
	},
	
	getActivePanel : function(){
		switch(this.displayType)
		{
			case 'days':
				return this.daysGrid;			
				break;
			
			case 'month':
				return this.monthGrid;			
				break;
			
			case 'view':
				return this.viewGrid;			
				break;
			
			case 'list':
				return this.listGrid;			
				break;
		}
		
	},
	
	updatePeriodInfoPanel : function (){
		
		var html = '';		
		var displayDate = this.getActivePanel().configuredDate;
		
		if(this.displayType=='month')
		{
			html = displayDate.format('F, Y');
		}else
		{
			html = GO.lang.strWeek+' '+displayDate.format('W');
		}
		
		this.periodInfoPanel.body.update(html);
	},
	
	
	deleteEvent : function(event, callback){
		
		//store them here so the already created window can use these values
		if(event.repeats)
		{
			this.currentDeleteEvent = event;
			this.currentDeleteCallback = callback;
			
				
			if(!this.recurrenceDialog)
			{
				
				this.recurrenceDialog = new Ext.Window({				
					width:400,
					autoHeight: true,
					closeable: false,
					closeAction: 'hide',
					plain: true,
					border: false,
					closable: false,
					title: GO.calendar.lang.recurringEvent,
					modal: true,
					html: GO.calendar.lang.deleteRecurringEvent,
					buttons: [{
						text: GO.calendar.lang.singleOccurence,
						handler: function(){
								
							var params={
								task: 'delete_event',
								create_exception: true,
								exception_date: this.currentDeleteEvent.startDate.format(this.daysGrid.dateTimeFormat),
								event_id: this.currentDeleteEvent.event_id
							};
							this.sendDeleteRequest(params, this.currentDeleteCallback, this.currentDeleteEvent);
													
							this.recurrenceDialog.hide();
						},
						scope: this
					},{
						text: GO.calendar.lang.entireSeries,
						handler: function(){
								
							var params={
								task: 'delete_event',
								event_id: this.currentDeleteEvent.event_id
							};
							this.sendDeleteRequest(params, this.currentDeleteCallback, this.currentDeleteEvent, true);
								
							this.recurrenceDialog.hide();
						},
						scope: this
					},{
						text: GO.lang.cmdCancel,
						handler: function(){
							this.recurrenceDialog.hide();
						},
						scope: this
					}]
				});
			}
		
			this.recurrenceDialog.show();
		}else
		{
			Ext.MessageBox.confirm(GO.lang.strConfirm, GO.lang.strDeleteSelectedItem, function(btn){
				if(btn=='yes')
				{
					var params={
						task: 'delete_event',
						event_id: event.event_id
					};
					this.sendDeleteRequest(params, callback, event);
				}
			}, this);
		}
	},
	
	sendDeleteRequest : function(params, callback, event, refresh)
	{
		Ext.Ajax.request({
			url: GO.settings.modules.calendar.url+'action.php',
			params: params,
			callback:function(options, success, response){				
				
				if(!success)
				{
					Ext.MessageBox.alert(GO.lang.strError, GO.lang.strRequestError);
				}else
				{
					var responseParams = Ext.decode(response.responseText);
					if(!responseParams.success)
					{
						Ext.MessageBox.alert(GO.lang.strError, responseParams.feedback);
					}else
					{
						callback.call(this, event, refresh);
					}
				}
			},
			scope:this
		});
	},

	/*
	 * 
	 * displayType: 'days', 'month', 'view'
	 * days: number of days to display in days grid
	 * calendar_id: calendar to display
	 * view_id: view to display
	 * 
	 * date: the date to display
	 * 
	 * 
	 */
	
	setDisplay : function(config){		
		if(!config)
		{
			config = {};
		}

		//when refresh is clicked remember state
		Ext.apply(this.state, config);
		delete this.state.saveState;

		if(config.displayType)
		{							
			this.displayType=config.displayType;
		}else if(config.calendar_id)
		{
			this.displayType=this.lastCalendarDisplayType;
		}else if(config.view_id)
		{
			if (config.merge=='0')
				this.displayType='view';
			else
				this.displayType=this.lastCalendarDisplayType;
		}
		
		this.state.displayType=this.displayType;
		
		//	if(config.days && this.displayType=='month')
		//	{
		//this.displayType='days';
		//	}
		
		var lastDisplayType = 'view';
		
		if(this.displayType!='view')
		{
			this.lastCalendarDisplayType=this.displayType;
		}	
		
		
		
		switch(this.displayType)
		{
			case 'month':
				this.displayPanel.getLayout().setActiveItem(1);
				break;
			
			case 'days':					
				this.displayPanel.getLayout().setActiveItem(0);
				break;
			
			case 'view':
				this.displayPanel.getLayout().setActiveItem(2);
				break;
			
			case 'list':
				this.displayPanel.getLayout().setActiveItem(3);
				break;
		}
		
		this.monthButton.setDisabled(this.displayType=='view');
		this.listButton.setDisabled(this.displayType=='view');

		if (config.calendars) {
			this.view_id=0;
			this.calendar_id = null;
			this.daysGridStore.baseParams['calendars']=Ext.encode(config.calendars);
			this.monthGridStore.baseParams['calendars']=Ext.encode(config.calendars);
			this.listGrid.store.baseParams['calendars']=Ext.encode(config.calendars);
		} else if (config.calendar_id)
		{
			this.view_id=0;
			this.calendar_id=config.calendar_id;
			this.daysGridStore.baseParams['calendars']=Ext.encode([config.calendar_id]);
			this.monthGridStore.baseParams['calendars']=Ext.encode([config.calendar_id]);
			this.listGrid.store.baseParams['calendars']=Ext.encode([config.calendar_id]);
		}

		if (typeof(config.merge)!='undefined'){
			this.merge=config.merge;
			this.owncolor = config.owncolor;
		}		

		if(config.calendar_name)
		{
			this.calendar_name=config.calendar_name;
		}

		if(config.view_id)
		{
			this.view_id=config.view_id;
			this.viewGrid.setViewId(config.view_id);
		}

		if(config.group_id)
		{
			this.group_id=config.group_id;
		}

		if (this.merge=='1' && this.view_id) {
			this.daysGridStore.baseParams['view_id']=this.view_id;
			this.monthGridStore.baseParams['view_id']=this.view_id;
			this.listGrid.store.baseParams['view_id']=this.view_id;
			this.daysGridStore.baseParams['owncolor']=this.owncolor;
			this.monthGridStore.baseParams['owncolor']=this.owncolor;
			this.listGrid.store.baseParams['owncolor']=this.owncolor;
		} else {
			this.daysGridStore.baseParams['view_id']=null;
			this.monthGridStore.baseParams['view_id']=null;
			this.listGrid.store.baseParams['view_id']=null;
			this.daysGridStore.baseParams['owncolor']=null;
			this.monthGridStore.baseParams['owncolor']=null;
			this.listGrid.store.baseParams['owncolor']=null;
		}

		if(config.date)
		{
			if(!config.days)
			{				
				config.days = this.type=='days' ?  this.daysGrid.days : this.viewGrid.days;
			}
			this.daysGrid.setDate(config.date,config.days,this.displayType=='days');
			this.monthGrid.setDate(config.date,this.displayType=='month');
			this.viewGrid.setDate(config.date,config.days, this.displayType=='view');
			this.listGrid.setDate(config.date,config.days, this.displayType=='list');
			
			this.days=config.days;
		}else if(config.days && this.displayType!='month')
		{
			this.daysGrid.setDays(config.days, this.displayType=='days');
			this.viewGrid.setDays(config.days, this.displayType=='view');
			this.listGrid.setDays(config.days, this.displayType=='list');
			
			this.days=config.days;
		}else
		{
			if(config.days)
			{
				this.days=config.days;
			}

			switch(this.displayType)
			{
				case 'month':
					this.monthGridStore.reload();
					break;
				
				case 'days':					
					this.daysGridStore.reload();
					break;
				
				case 'view':
					this.viewGrid.load();
					break;
				
				case 'list':
					this.listGrid.store.reload();
					break;
			}
		}
		
		
		this.dayButton.toggle(this.displayType=='days' && this.days==1);
		this.workWeekButton.toggle(this.displayType=='days' && this.days==6);
		this.weekButton.toggle(this.displayType=='days' && this.days==7);
		
		this.monthButton.toggle(this.displayType=='month');
		this.listButton.toggle(this.displayType=='list');
		
		this.updatePeriodInfoPanel();

		if(config.saveState)
		{
			this.saveState();
		}
	},
	
	
	saveState : function()
	{
		var state = {
			displayType: this.displayType,
			calendar_id: this.calendar_id,
			view_id: this.view_id,
			days: this.days,
			merge : this.merge,
			owncolor : this.owncolor,
			group_id : this.group_id
		}
		
		Ext.state.Manager.set('calendar-state', Ext.encode(state));
	},
	
	refresh : function() {
		this.setDisplay();
	},
      
	createDaysGrid : function()
	{
		
		this.daysGrid.on("eventResize", function(grid, event, actionData){

			var params = {
				task : 'update_grid_event',
				update_event_id : event['event_id'],
				duration : actionData.duration
			};
			
			if(event.repeats && actionData.singleInstance)
			{
				params['createException']='true';
				params['exceptionDate']=actionData.dragDate.format(grid.dateTimeFormat);
				params['repeats']='true';
			}
  		
			Ext.Ajax.request({
				url: GO.settings.modules.calendar.url+'action.php',
				params: params,
				callback: function(options, success, response)
				{
					var responseParams = Ext.decode(response.responseText);
					if(!responseParams.success)
					{
						Ext.MessageBox.alert(GO.lang.strError, responseParams.feedback);
					}else
					{
						if(event.repeats && !actionData.singleInstance)
						{
							grid.store.reload();
						}
					}
				}
			});
    		
				
		}, this);
		
		
		this.daysGrid.on("create", function(CalGrid, newEvent){
			var formValues={};
				
			formValues['start_date'] = newEvent['startDate'];//.format(GO.settings['date_format']);
			formValues['start_hour'] = newEvent['startDate'].format("H");
			formValues['start_min'] = newEvent['startDate'].format("i");
				
			formValues['end_date'] = newEvent['endDate'];//.format(GO.settings['date_format']);
			formValues['end_hour'] = newEvent['endDate'].format("H");
			formValues['end_min'] = newEvent['endDate'].format("i");
				
			GO.calendar.eventDialog.show({
				values: formValues,
				calendar_id: this.calendar_id,
				calendar_name: this.calendar_name
			});
				
		}, this);
			
		this.monthGrid.on("create", function(grid, date){

			var now = new Date();
				
			var formValues={
				start_date: date,
				end_date: date,
				start_hour: now.format('H'),
				end_hour: now.add(Date.HOUR, 1).format('H'),
				start_min: '00',
				end_min: '00'
			};
				
			GO.calendar.eventDialog.show({
				values: formValues,
				calendar_id: this.calendar_id,
				calendar_name: this.calendar_name
			});
		}, this);
		
		this.monthGrid.on('changeview', function(grid, days, date){
			this.setDisplay({
				displayType:'days',
				days:days,
				date: date
			});
		}, this);
		
		this.daysGrid.on("eventDblClick", this.onDblClick, this);
		this.monthGrid.on("eventDblClick", this.onDblClick, this);
		this.viewGrid.on("eventDblClick", this.onDblClick, this);
		
		
		this.monthGrid.on("move", this.onEventMove,this);
		this.daysGrid.on("move", this.onEventMove,this);
		
	
		this.viewGrid.on("move", function(grid, event, actionData){
	    		
			var params = {
				task : 'update_grid_event',
				update_event_id : event['event_id']
			};
			
			if(actionData.offset)
				params['offset']=actionData.offset;
			
			if(actionData.offsetDays)
				params['offsetDays']=actionData.offsetDays;
			
			if(event.repeats && actionData.singleInstance)
			{
				params['createException']='true';
				params['exceptionDate']=actionData.dragDate.format(grid.dateTimeFormat);
				params['repeats']='true';
			}
			
			if(actionData.calendar_id)
			{
				params['update_calendar_id']=actionData.calendar_id;
			}
			 		
			Ext.Ajax.request({
				url: GO.settings.modules.calendar.url+'action.php',
				params: params,
				callback: function(options, success, response)
				{
					var responseParams = Ext.decode(response.responseText);
					if(!responseParams.success)
					{
						Ext.MessageBox.alert(GO.lang.strError, responseParams.feedback);
					}else
					{
						if(event.repeats && !actionData.singleInstance)
						{
							grid.store.reload();
						}else if(responseParams.new_event_id)
						{
							grid.setNewEventId(event['domId'], responseParams.new_event_id);
						}
					}
				}
			});	  		
	  		
		},this);
	},
	  
	onDblClick : function(grid, event, actionData){

		if(event.repeats && actionData.singleInstance)
		{
			var formValues={};

			formValues['start_date'] = event['startDate'].format(GO.settings['date_format']);
			formValues['start_hour'] = event['startDate'].format("H");
			formValues['start_min'] = event['startDate'].format("i");

			formValues['end_date'] = event['endDate'].format(GO.settings['date_format']);
			formValues['end_hour'] = event['endDate'].format("H");
			formValues['end_min'] = event['endDate'].format("i");

			GO.calendar.eventDialog.show({
				values: formValues,
				exceptionDate: event['startDate'].format(this.daysGrid.dateTimeFormat),
				exception_event_id: event['event_id'],
				oldDomId : event.domId
			});
		}else
		{		
			if(event['task_id'])
			{
				GO.tasks.taskDialog.show({
					task_id : event['task_id']
				})				
			}else
			if(event['contact_id'])
			{			
				GO.linkHandlers[2].call(this, event['contact_id']);
			}else
			if(event['event_id'])
			{
				GO.calendar.eventDialog.show({
					event_id: event['event_id'],
					oldDomId : event.domId
				});
			}
		}
	},
    
	onEventMove : function(grid, event, actionData){
		
		var params = {
			task : 'update_grid_event',
			update_event_id : event['event_id']
		};

		if(actionData.offset)
			params['offset']=actionData.offset;

		if(actionData.offsetDays)
			params['offsetDays']=actionData.offsetDays;

		if(event.repeats && actionData.singleInstance)
		{
			params['createException']='true';
			params['exceptionDate']=actionData.dragDate.format(grid.dateTimeFormat);
			params['repeats']='true';
		}

		if(actionData.calendar_id)
		{
			params['update_calendar_id']=actionData.calendar_id;
		}

		Ext.Ajax.request({
			url: GO.settings.modules.calendar.url+'action.php',
			params: params,
			callback: function(options, success, response)
			{
				var responseParams = Ext.decode(response.responseText);
				if(!responseParams.success)
				{
					Ext.MessageBox.alert(GO.lang.strError, responseParams.feedback);
				}else
				{
					if(event.repeats && !actionData.singleInstance)
					{
						grid.store.reload();
					}else if(responseParams.new_event_id)
					{
						grid.setNewEventId(event['domId'], responseParams.new_event_id);
					}
				}
			}
		});
	},

	showAdminDialog : function() {
		
		if(!this.adminDialog)
		{
			
			this.writableCalendarsStore = new GO.data.JsonStore({
				url: GO.settings.modules.calendar.url+'json.php',
				baseParams: {
					'task': 'writable_calendars'
				},
				root: 'results',
				totalProperty: 'total',
				id: 'id',
				fields:['id','name','user_name'],
				remoteSort:true,
				sortInfo: {
					field: 'name',
					direction: 'ASC'
				}
			});
			
			this.writableCalendarsStore.load();


			
			this.writableViewsStore = new GO.data.JsonStore({
				url: GO.settings.modules.calendar.url+'json.php',
				baseParams: {
					'task': 'writable_views'
				},
				root: 'results',
				totalProperty: 'total',
				id: 'id',
				fields:['id','name','user_name','merge'],
				remoteSort:true,
				sortInfo: {
					field: 'name',
					direction: 'ASC'
				}
			});

			this.writableResourcesStore = new Ext.data.GroupingStore({
				baseParams: {
					'task': 'writable_calendars',
					'resources' : 'true'
				},
				reader: new Ext.data.JsonReader({
					root: 'results',
					id: 'id',
					totalProperty: 'total',
					fields:['id','name','user_name','group_name']
				}),
				proxy: new Ext.data.HttpProxy({
					url: GO.settings.modules.calendar.url+'json.php'
				}),
				groupField:'group_name',
				sortInfo: {
					field: 'name',
					direction: 'ASC'
				}
			}),

            
			
			this.calendarDialog = GO.calendar.calendarDialog = new GO.calendar.CalendarDialog();
			this.calendarDialog.on('save', function(e, group_id)
			{				
				if(group_id > 1)
				{
					this.writableResourcesStore.reload();
					this.resourcesList.store.reload();
					GO.calendar.eventDialog.updateResourcePanel();
				} else
				{
					this.writableCalendarsStore.reload();
					this.calendarsStore.reload();
				}
			}, this);

			 var tbar = [{
				iconCls: 'btn-add',
				text: GO.lang.cmdAdd,
				disabled: !GO.settings.modules.calendar.write_permission,
				cls: 'x-btn-text-icon',
				handler: function(){
					this.calendarDialog.show(0, false);
				},
				scope: this
			},{
				iconCls: 'btn-delete',
				text: GO.lang.cmdDelete,
				disabled: !GO.settings.modules.calendar.write_permission,
				cls: 'x-btn-text-icon',
				handler: function(){
					this.calendarsGrid.deleteSelected();
				},
				scope:this
			}]

			if(GO.customfields)
			{
				tbar.push(new Ext.Button({
					iconCls: 'btn-settings',
					disabled: !GO.settings.modules.calendar.write_permission,
					text: GO.customfields.lang.customfields,
					cls: 'x-btn-text-icon',
					handler: function()
					{
						GO.calendar.groupDialog.show(1);
					},
					scope: this
				}));
			}

			this.calendarsGrid = new GO.grid.GridPanel( {
				title: GO.calendar.lang.calendars,
				paging: true,
				border: false,
				store: this.writableCalendarsStore,
				deleteConfig: {
					callback:function(){
						this.calendarsStore.reload();
					},
					scope:this
				},
				columns:[{
					header:GO.lang.strName,
					dataIndex: 'name',
					sortable:true
				},{
					header:GO.lang.strOwner,
					dataIndex: 'user_name'
				}],
				view:new  Ext.grid.GridView({
					autoFill:true
				}),
				sm: new Ext.grid.RowSelectionModel(),
				loadMask: true,
				tbar:tbar
				
			});		
            
			this.calendarsGrid.on("rowdblclick", function(grid, rowClicked, e)
			{
				this.calendarDialog.show(grid.selModel.selections.keys[0], false);
			}, this);
			

			
			this.viewDialog = new GO.calendar.ViewDialog();
			
			this.viewDialog.on('save', function(){
				this.writableViewsStore.reload();
				this.viewsStore.reload();				
			}, this);

			this.mergeColumn = new GO.grid.CheckColumn({
				header: GO.calendar.lang.merge,
				dataIndex: 'merge',
				width: 55
			});

			this.mergeColumn.on('change', function(grid, merge)
			{
				var items = grid.store.data.items;
				for (var i in items) {
					if(!isNaN(i)) {
						Ext.Ajax.request({
							url: GO.settings.modules.calendar.url+'action.php',
							params: {
								task : 'change_merge',
								view_id : items[i].id,
								merge : items[i].data.merge
							},
							callback:function(options, success, response){

								if(!success)
								{
									Ext.MessageBox.alert(GO.lang.strError, GO.lang.strRequestError);
								}else
								{
									var responseParams = Ext.decode(response.responseText);
									if(!responseParams.success)
									{
										Ext.MessageBox.alert(GO.lang.strError, responseParams.feedback);
									}else
									{
										this.writableViewsStore.reload();
										this.viewsStore.reload();
									}
								}
							},
							scope:this
						});
					}
				}
			}, this);

			this.viewsGrid = new GO.grid.GridPanel( {
				title: GO.calendar.lang.views,
				paging: true,
				border: false,
				store: this.writableViewsStore,
				deleteConfig: {
					callback:function(){
						this.viewsStore.reload();
					},
					scope:this
				},
				columns:[{
					header:GO.lang.strName,
					dataIndex: 'name',
					sortable:true
				},{
					header:GO.lang.strOwner,
					dataIndex: 'user_name'
				},this.mergeColumn
				],
				view:new  Ext.grid.GridView({
					autoFill:true
				}),
				sm: new Ext.grid.RowSelectionModel(),
				loadMask: true,
				plugins : [this.mergeColumn],
				tbar: [{
					id: 'addView',
					iconCls: 'btn-add',
					text: GO.lang.cmdAdd,
					cls: 'x-btn-text-icon',
					handler: function(){
						this.viewDialog.show();
					},
					scope: this
				},{
					id: 'delete',
					iconCls: 'btn-delete',
					text: GO.lang.cmdDelete,
					cls: 'x-btn-text-icon',
					handler: function(){
						this.viewsGrid.deleteSelected();
					},
					scope:this
				}]
			});
			
			this.viewsGrid.on("rowdblclick", function(grid, rowClicked, e){
				this.viewDialog.show(grid.selModel.selections.keys[0]);
			}, this);
			
			this.viewsGrid.on('show', function(){
				this.writableViewsStore.load();
			},this, {
				single:true
			});
			
			GO.calendar.groupsGrid = this.groupsGrid = new GO.calendar.GroupsGrid({
				title:GO.calendar.lang.resource_groups,
				layout:'fit',
				store:GO.calendar.groupsStore,
				deleteConfig: {
					callback:function(){

						this.writableResourcesStore.reload();
						this.resourcesStore.reload();
						GO.calendar.eventDialog.updateResourcePanel();
					},
					scope:this
				}
			});
			            
			this.resourcesGrid = new GO.calendar.ResourcesGrid({
				title:GO.calendar.lang.resources,
				layout:'fit',
				store:this.writableResourcesStore,
				deleteConfig: {
					callback:function(){
						this.resourcesStore.reload();                        
						GO.calendar.eventDialog.updateResourcePanel();
					},
					scope:this
				}
			});

			var items = [this.calendarsGrid, this.viewsGrid];
			if(GO.settings.modules['calendar']['write_permission'])
			{
				items.push(this.groupsGrid);
				items.push(this.resourcesGrid);
			}
            
			this.adminDialog = new Ext.Window({
				title: GO.calendar.lang.administration,
				layout:'fit',
				modal:false,
				minWidth:300,
				minHeight:300,
				height:400,
				width:600,
				closeAction:'hide',				
				items: new Ext.TabPanel({
					border:false,
					activeTab:0,
					items:items
				}),
				buttons:[{
					text:GO.lang.cmdClose,
					handler: function(){
						this.adminDialog.hide()
					},
					scope: this
				}]
			});
			
		}

		this.adminDialog.show();			
	}
});


GO.calendar.extraToolbarItems = [];

GO.moduleManager.addModule('calendar', GO.calendar.MainPanel, {
	title : GO.calendar.lang.calendar,
	iconCls : 'go-tab-icon-calendar'
});

GO.mainLayout.onReady(function(){
	GO.calendar.groupsStore = new GO.data.JsonStore({
		url: GO.settings.modules.calendar.url+ 'json.php',
		baseParams: {
			task: 'groups'
		},
		root: 'results',
		id: 'id',
		totalProperty:'total',
		fields:['id','name','user_name','fields','acl_id'],
		remoteSort: true
	}),

	GO.calendar.eventDialog = new GO.calendar.EventDialog();

	GO.newMenuItems.push({
		text: GO.calendar.lang.appointment,
		iconCls: 'go-link-icon-1',
		handler:function(item, e){

			var eventShowConfig = item.parentMenu.eventShowConfig || {};
			eventShowConfig.link_config=item.parentMenu.link_config

			GO.calendar.eventDialog.show(eventShowConfig);
		}
	});
}); 

GO.linkHandlers[1]=function(id){
	
	GO.calendar.eventDialog.show({
		event_id: id
	});
};



GO.calendar.showEvent = function(config){

	config = config || {};
    
	config.event_id = config.values.event_id;
	//delete(config.values.event_id);
    
	GO.calendar.eventDialog.show(config);

};
