GO.projects.HoursGrid = function(config){
	
	if(!config)
	{
		config = {};
	}
	
	
	
	config.store = new GO.data.JsonStore({
		url: GO.settings.modules.projects.url+'json.php',
		baseParams: {
			'task': 'hours'
			},
		root: 'results',
		totalProperty: 'total',
		id: 'id',
		fields:['id','units', 'user_name', 'date', 'fee_name', 'comments', 'project_name']
	});
	
	if(config.project_id)
	{
		this.setProjectId(config.project_id);
	}
	
		
	//config.cls='pm-hours-table',
	config.paging=true;
	config.columns=[
		{
			header:GO.lang.strHours,
			dataIndex: 'units',
			width:30
		},
		{
			header:GO.projects.lang.project,
			dataIndex: 'project_name'
		},
		{
			header:GO.lang.strUser,
			dataIndex: 'user_name'
		},
		{
			header:GO.lang.strDate,
			dataIndex: 'date'
		},
		{
			header:GO.projects.lang.fee,
			dataIndex: 'fee_name'
		}
		];
	config.view=new Ext.grid.GridView({
		autoFill: true,
		forceFit: true,
		emptyText: GO.projects.lang.noHours,
		enableRowBody:true,
		showPreview:true,
		getRowClass : function(record, rowIndex, p, store){
		    if(this.showPreview){
		        p.body = '<p class="pm-hours-comments">'+record.data.comments+'</p>';
		        return 'x-grid3-row-expanded';
		    }
		    return 'x-grid3-row-collapsed';
		}
	}),
	config.sm=new Ext.grid.RowSelectionModel();
	config.loadMask=true;
	
	config.tbar=[{

			iconCls: 'btn-delete',							
			text: GO.lang['cmdDelete'],
			cls: 'x-btn-text-icon',
			handler: function(){
				this.deleteSelected();
			},
			scope: this
		}];

	
	GO.projects.HoursGrid.superclass.constructor.call(this, config);
	
};


Ext.extend(GO.projects.HoursGrid, GO.grid.GridPanel, {
	
	project_id : 0,
	loaded_project_id : 0,
	
	afterRender : function()
	{
		GO.projects.HoursGrid.superclass.afterRender.call(this);
		
		
    this.on("rowdblclick", function(grid, rowClicked, e){
			if(!this.hoursDialog)
    	{
    		this.hoursPanel = new GO.projects.AddHoursPanel();
    		this.hoursPanel.on('save', function(){
    			this.hoursDialog.hide();
    			this.store.reload();    			
    		}, this);
    		
    		this.hoursDialog = new Ext.Window({
    			title: GO.lang.strHours,
    			width:600,
    			autoHeight:true,			
    			items:this.hoursPanel,
    			buttons:[{
						text: GO.lang['cmdOk'],
						handler: function(){
							this.hoursPanel.submit();
						},
						scope: this
					},{
						text: GO.lang['cmdClose'],
						handler: function(){
							this.hoursDialog.hide();
						},
						scope:this
					}					
				]
    		});
    	}   	
    	this.hoursDialog.show();    	
    	this.hoursPanel.setHoursId(grid.selModel.selections.keys[0]);
			}, this);
	},	
	onShow : function(){		
		GO.projects.HoursGrid.superclass.onShow.call(this);
		
		if(this.loaded_project_id!=this.project_id)
		{
			this.loaded_project_id=this.project_id;
			this.store.load();
		}		
	},
	
	setProjectId : function(project_id)
	{
		this.project_id=project_id;
		this.store.baseParams.project_id=project_id;
	} 
});
