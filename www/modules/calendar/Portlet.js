GO.calendar.SummaryGroupPanel = function(config)
{
	if(!config)
	{
		config = {};
	}
	
	config.store = new Ext.data.GroupingStore({
	    reader: new Ext.data.JsonReader({
			    totalProperty: "count",
			    root: "results",
			    id: "event_id",
			    fields: [
						'id',
						'event_id',
						'name',
						'time',
						'start_time',
						'end_time',
						'tooltip',
						'private',
						'repeats',
						'day'			
					]
	    	}),
			baseParams: {task:'summary'},
			proxy: new Ext.data.HttpProxy({
		      url: GO.settings.modules.calendar.url+'json.php'
		  }),        
	    groupField:'day',
	    sortInfo: {field: 'start_time', direction: 'ASC'}
	  });
	  
	/*config.store = new Ext.data.JsonStore({
      totalProperty: "count",
	    root: "results",
	    id: "id",
	    fields: [
				'id',
				'event_id',
				'name',
				'start_time',
				'end_time',
				'tooltip',
				'private',
				'repeats',
				'day'			
			],
			baseParams: {task:'summary'},
			url: GO.settings.modules.calendar.url+'json.php'
		  
	  });*/
	
	
	config.paging=false,			
	config.autoExpandColumn='summary-calendar-name-heading';
	config.autoExpandMax=2500;
	config.enableColumnHide=false;
  config.enableColumnMove=false;

	config.columns=[
		{
			header:GO.lang.strDay,
			dataIndex: 'day'
		},		
		{
			header:GO.lang.strTime,
			dataIndex: 'time',
			width:50
		},		
		{
			id:'summary-calendar-name-heading',
			header:GO.lang.strName,
			dataIndex: 'name',
			renderer: this.renderName
		}];
		
	config.view=  new Ext.grid.GroupingView({
    hideGroupedColumn:true,
    groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "Items" : "Item"]})',
   	emptyText: GO.calendar.lang.noAppointmentsToDisplay,
   	showGroupName:false
	});
	config.sm=new Ext.grid.RowSelectionModel();
	config.loadMask=true;
	config.autoHeight=true;
	
	GO.calendar.SummaryGroupPanel.superclass.constructor.call(this, config);
	
};


Ext.extend(GO.calendar.SummaryGroupPanel, Ext.grid.GridPanel, {
	
	renderName : function(grid, value, record)
	{		
		return '<div style="font-weight:bold">'+record.data.name+'</div>'+record.data.tooltip;		
	},
		
	afterRender : function()
	{
		GO.calendar.SummaryGroupPanel.superclass.afterRender.call(this);
    
    GO.calendar.eventDialog.on('save', function(){this.store.reload()}, this);
    
    this.on("rowdblclick", function(grid, rowClicked, e){
    	var event_id = grid.selModel.selections.keys[0];			 
			GO.calendar.eventDialog.show({event_id: event_id});
		}, this);		  
			
		this.store.load();
	}
	
});

	
	





GO.mainLayout.onReady(function(){
	
	if(GO.summary)
	{
		var calGrid = new GO.calendar.SummaryGroupPanel({
			id: 'summary-calendar-grid'
		});
		
		GO.summary.portlets['portlet-calendar']=new GO.summary.Portlet({
			id: 'portlet-calendar',
			//iconCls: 'go-module-icon-calendar',
		 	title: GO.calendar.lang.appointments,
			layout:'fit',
			tools: [{
	        id:'close',
	        handler: function(e, target, panel){
	            panel.removePortlet();
	        }
		   }],
			items: calGrid,
			autoHeight:true
			
		});
	}
});