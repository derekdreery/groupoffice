/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

GO.calendar.ListGrid = function(config)
{
	if(!config)
	{
		config = {};
	}
	
	config.store = new Ext.data.GroupingStore({
	    reader: new Ext.data.JsonReader({
			    totalProperty: "count",
			    root: "results",
			    id: "id",
			    fields: [
						'id',
						'event_id',
						'name',
						'time',
						'start_time',
						'end_time',
						'description',
						'location',
						'private',
						'repeats',
						'background',
						'day',
						'task_id',
						'contact_id'
					]
	    	}),
			baseParams: {task:'events'},
			proxy: new Ext.data.HttpProxy({
		      url: GO.settings.modules.calendar.url+'json.php'
		  }),        
	    groupField:'day',
	    sortInfo: {field: 'start_time', direction: 'ASC'},
	    remoteGroup:true
	  });
	
	config.paging=false,			
	config.autoExpandColumn='listview-calendar-name-heading';
	config.autoExpandMax=2500;
	config.enableColumnHide=false;
  config.enableColumnMove=false;
  config.autoScroll=true;
  
	config.columns=[
		{
			header:GO.lang.strDay,
			dataIndex: 'day'
		},		
		{
			header:GO.lang.strTime,
			dataIndex: 'time',
			width:80,
			renderer: function(v, metadata, record)
			{
				return '<div style="border:1px solid #c0c0c0;padding:2px;margin:2px;background-color:#'+record.data.background+';">'+v+'</div>';
			}
		},		
		{
			id:'listview-calendar-name-heading',
			header:GO.lang.strName,
			dataIndex: 'name',
			renderer: this.renderName
		}];
		
	config.view=  new Ext.grid.GroupingView({
    hideGroupedColumn:true,
    groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "'+GO.lang.items+'" : "'+GO.lang.item+'"]})',
   	emptyText: GO.calendar.lang.noAppointmentsToDisplay,
   	showGroupName:false
	});
	config.sm=new Ext.grid.RowSelectionModel({singleSelect:true});
	config.loadMask=true;
	
	GO.calendar.ListGrid.superclass.constructor.call(this, config);
	
	
	
	if(!this.startDate)
	{
		//lose time
		var date = new Date();
		this.startDate=Date.parseDate(date.format(this.dateFormat), this.dateFormat);
	}
	
	this.configuredDate=this.startDate;
	
};


Ext.extend(GO.calendar.ListGrid, Ext.grid.GridPanel, {
	
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
	
	/**
   * @cfg {Integer} amount of days to display
   */
	days : 7,
	
	renderName : function(grid, value, record)
	{		
		return '<div style="font-weight:bold;">'+record.data.name+'</div>'+GO.calendar.formatQtip(record.data);		
	},
		
	afterRender : function()
	{
		GO.calendar.ListGrid.superclass.afterRender.call(this);
    
    /*GO.calendar.eventDialog.on('save', function(){
    	if(this.isVisible())
    	{
    		this.store.reload();
    	}    	
    }, this);*/
    
		this.on("rowdblclick", function(grid, rowIndex, e){
			var record = grid.getStore().getAt(rowIndex);

			if(record.data.event_id)
			{
				GO.calendar.eventDialog.show({event_id: record.data.event_id});
				
			}else			
			if(record.data.task_id)
			{
				GO.tasks.taskDialog.show({
					task_id : record.data.task_id
				})
			}else
			if(record.data.contact_id)
			{
				GO.linkHandlers[2].call(this, record.data.contact_id);
			}
			
		}, this);		  
	},
	
	getFirstDateOfWeek : function(date)
	{
		//Calculate the first day of the week		
		var weekday = date.getDay();
		var offset = this.firstWeekday-weekday;
		if(offset>0)
		{
			offset-=7;
		}
		return date.add(Date.DAY, offset);
	},
	setDays : function(days, load)
	{
		this.setDate(this.configuredDate, 7, load);
	},
	
	getSelectedEvent : function(){
		
		var sm = this.getSelectionModel();
		var record = sm.getSelected();
		var event = record.data; 
		
		event.startDate = Date.parseDate(event.start_time, this.dateTimeFormat);
		event.endDate = Date.parseDate(event.end_time, this.dateTimeFormat);
		
		return event;
		
	},
	
	removeEvent : function(){		
		var sm = this.getSelectionModel();
		var record = sm.getSelected();		
		this.store.remove(record)
	},
	
  setDate : function(date, days, load)
  {
	  
  	var oldStartDate = this.startDate;
  	var oldEndDate = this.endDate;
  	
  	if(days)
  	{
  		//this.days=+days;
  	}   
  	
  	this.configuredDate = date;	    	

  	if(this.days>4)
  	{
  		this.startDate = this.getFirstDateOfWeek(date);
  	}else
  	{
  		this.startDate = date;
  	}    	
    this.endDate = this.startDate.add(Date.DAY, this.days);
  	this.setStoreBaseParams();
  	
  	if(load)
  	{
    	//if(!oldEndDate || !oldStartDate || oldEndDate.getElapsed(this.endDate)!=0 || oldStartDate.getElapsed(this.startDate)!=0)
    	{    			     		
	    	this.store.reload();    			    	
    	}
  	}
  },
  
  setStoreBaseParams : function(){
  	this.store.baseParams['start_time']=this.startDate.format(this.dateTimeFormat);
    this.store.baseParams['end_time']=this.endDate.format(this.dateTimeFormat);  
  }
	
});

	
	
