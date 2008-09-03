GO.projects.FeesGrid = function(config){
	
	if(!config)
	{
		config = {};
	}
	
	
	
	config.store = new GO.data.JsonStore({
		url: GO.settings.modules.projects.url+'json.php',
		baseParams: {
			'task': 'writable_fees'
			},
		root: 'results',
		totalProperty: 'total',
		id: 'id',
		fields:['id','name', 'internal_value', 'external_value', 'time'],
		remoteSort:true
	});
	
	config.store.on('load', function(){
		GO.projects.stores.readableFees.reload();
	});
	
	
	
	config.autoExpandColumn=1;
		
	//config.cls='pm-fees-table',
	config.paging=false;
	config.columns=[
		{
			header:GO.lang['strName'],
			dataIndex: 'name'	
		},
		{
			header:GO.projects.lang.internalFee,
			dataIndex: 'internal_value'
		},
		{
			header:GO.projects.lang.externalFee,
			dataIndex: 'external_value'
		},
		{
			header:GO.projects.lang.unitValue,
			dataIndex: 'time'
		}
		];
	
	config.view=new Ext.grid.GridView({
		autoFill: true,
		forceFit: true,
		emptyText: GO.projects.lang.noFees		
	}),
	config.sm=new Ext.grid.RowSelectionModel();
	config.loadMask=true;
	
	config.tbar=[{

			iconCls: 'btn-add',							
			text: GO.lang['cmdAdd'],
			cls: 'x-btn-text-icon',
			handler: function(){
				if(!this.feesDialog)
	    	{
	    		this.feesDialog = new GO.projects.FeeDialog();
	    		
	    		this.feesDialog.on('save', function(){    			
	    			this.store.reload();
	    			    			
	    		}, this);
	    	}   	
	    	this.feesDialog.show();
			},
			scope: this
		},{

			iconCls: 'btn-delete',
			text: GO.lang['cmdDelete'],
			cls: 'x-btn-text-icon',
			handler: function(){
				this.deleteSelected();
			},
			scope: this
		}];

	
	GO.projects.FeesGrid.superclass.constructor.call(this, config);
	
};


Ext.extend(GO.projects.FeesGrid, GO.grid.GridPanel, {
	
	
	afterRender : function()
	{
		GO.projects.FeesGrid.superclass.afterRender.call(this);
		
		
    this.on("rowdblclick", function(grid, rowClicked, e){
			
			if(!this.feesDialog)
    	{
    		this.feesDialog = new GO.projects.FeeDialog();
    		
    		this.feesDialog.on('save', function(){    			
    			this.store.reload(); 
    			GO.projects.stores.readableFees.reload();   			
    		}, this);
    	}   	
    	
    	this.feesDialog.show({fee_id: grid.selModel.selections.keys[0]});
			
			}, this);
			
		this.store.load();
	} 
});
