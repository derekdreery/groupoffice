GO.projects.ReportGrid = function(config){
	
	if(!config)
	{
		config = {};
	}
	
	
	
	config.store = new GO.data.JsonStore({
		url: GO.settings.modules.projects.url+'json.php',
		baseParams: {
			'task': 'report'
			},
		root: 'results',
		fields:['name', 'units','days','int_fee_value','ext_fee_value', 'user_id', 'customer', 'project_id'],
		remoteSort:true
	});
	
	
	
	
		
	//config.cls='pm-report-table',
	config.paging=true;
	config.columns=[
		{
			header:GO.lang['strName'],
			dataIndex: 'name',
			sortable:true
		},
		{
			header:GO.projects.lang.units,
			dataIndex: 'units',
			width: 100,
			sortable:true
		},{
			header:GO.lang.strDays,
			dataIndex: 'days',
			width: 100,
			sortable:true
		},
		{
			header:GO.projects.lang.internalFee,
			dataIndex: 'int_fee_value',
			width: 100,
			sortable:true
		},{
			header:GO.projects.lang.externalFee,
			dataIndex: 'ext_fee_value',
			width: 100,
			sortable:true
		}
		];
	
	config.view=new Ext.grid.GridView({
		autoFill: true,
		forceFit: true,
		emptyText: GO.projects.lang.noData		
	});
	config.sm=new Ext.grid.RowSelectionModel();
	config.loadMask=true;	
	
	GO.projects.ReportGrid.superclass.constructor.call(this, config);
	
};


Ext.extend(GO.projects.ReportGrid, GO.grid.GridPanel, {
	
	
	afterRender : function()
	{
		GO.projects.ReportGrid.superclass.afterRender.call(this);
		
		
    this.on("rowdblclick", function(grid, rowClicked, e){    	
				if(!this.hoursDialog)
	    	{
	    		this.hoursPanel = new GO.projects.HoursGrid({border:false});
	    	
	    		  		
	    		
	    		
	    		this.hoursDialog = new Ext.Window({
	    			title: GO.projects.lang.reportDetails,
	    			width:600,
	    			height:400,
	    			layout:'fit',			
	    			items:this.hoursPanel,
	    			closeAction:'hide',
	    			buttons:[{
							text: GO.lang['cmdClose'],
							handler: function(){
								this.hoursDialog.hide();
							},
							scope:this
						}					
					]
	    		});
	    	} 
	    	
	    	var selModel = grid.getSelectionModel();
	    	var record = selModel.getSelected();
	    
	    	this.hoursPanel.store.baseParams.start_date = this.store.baseParams.start_date;
	    	this.hoursPanel.store.baseParams.end_date = this.store.baseParams.end_date;
	    				
	    		
	  		this.hoursPanel.store.baseParams[this.store.baseParams.group_by]=record.data[this.store.baseParams.group_by];
	  		this.hoursPanel.store.load();
	  		  		
	    	this.hoursDialog.show();    	
			
			}, this);
	}
});
