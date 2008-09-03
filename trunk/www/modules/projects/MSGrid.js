GO.projects.MSGrid = function(config){
	
	if(!config)
	{
		config = {};
	}
	
	
	
	config.store = new GO.data.JsonStore({
		url: GO.settings.modules.projects.url+'json.php',
		baseParams: {
			'task': 'milestones'
			},
		root: 'results',
		totalProperty: 'total',
		id: 'id',
		fields:['id','completed', 'completion_time', 'name', 'user_name', 'due_time', 'project_name', 'description', 'project_id', 'late'],
		remoteSort:true
	});
	
	if(config.project_id)
	{
		this.setProjectId(config.project_id);
	}
	
	
	var checkColumn = new GO.grid.CheckColumn({
     header: '',
     dataIndex: 'completed',
     width: 30,
     sortable: false,
     header: '<div class="tasks-complete-icon"></div>'
  });
  
  checkColumn.on('change', function(record, checked){
  	this.store.baseParams['completed_milestone_id']=record.data.id;
  	this.store.baseParams['checked']=checked;
  	
  	this.store.load();
  	
  	delete this.store.baseParams['completed_task_id'];
  	delete this.store.baseParams['checked'];
  }, this);
	
	config.plugins=checkColumn;
	config.autoExpandColumn=1;
		
	//config.cls='pm-ms-table',
	config.paging=true;
	config.columns=[
		checkColumn,
		{
			header:GO.lang['strName'],
			dataIndex: 'name',
			sortable:true,
			renderer: function(value, metadata, record){
				if(record.data.late)
				{
					metadata.css='projects-late-cell'
				}else if(record.data.completed)
				{
					metadata.css='projects-completed-cell'
				}				
				return value;
			}
		},
		{
			header:GO.lang.strUser,
			dataIndex: 'user_name',
			width: 120
		},
		{
			header:GO.projects.lang.due,
			dataIndex: 'due_time',
			width: 100,
			sortable:true
		},
		{
			header:GO.projects.lang.completionTime,
			dataIndex: 'completion_time',
			width: 100,
			sortable:true
		}
		];
	
	config.view=new Ext.grid.GridView({
		//autoFill: true,
		//forceFit: true,
		emptyText: GO.projects.lang.noMilestones,
		enableRowBody:true,
		showPreview:true,
		getRowClass : function(record, rowIndex, p, store){
		    if(this.showPreview){
		        p.body = '<p class="pm-hours-comments">'+record.data.description+'</p>';
		        return 'x-grid3-row-expanded';
		    }
		    return 'x-grid3-row-collapsed';
		}
	}),
	config.sm=new Ext.grid.RowSelectionModel();
	config.loadMask=true;
	
	config.tbar=[{

			iconCls: 'btn-add',							
			text: GO.lang['cmdAdd'],
			cls: 'x-btn-text-icon',
			handler: function(){
				this.msDialog.show();
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

	this.msDialog = new GO.projects.MSDialog();
	this.msDialog.on('save', function(){this.store.reload();},this);
	
	GO.projects.MSGrid.superclass.constructor.call(this, config);
	
};


Ext.extend(GO.projects.MSGrid, GO.grid.GridPanel, {
	
	project_id : 0,
	loaded_project_id : 0,
	
	afterRender : function()
	{
		GO.projects.MSGrid.superclass.afterRender.call(this);
		
		
		
    this.on("rowdblclick", function(grid, rowClicked, e){
			
			/*if(!this.msDialog)
    	{
    		this.msPanel = new GO.projects.AddMSPanel();
    		this.msPanel.on('save', function(){
    			this.msDialog.hide();
    			this.store.reload();    			
    		}, this);
    		
    		this.msDialog = new Ext.Window({
    			title: 'Milestone',
    			width:600,
    			autoHeight:true,			
    			items:this.msPanel,
    			buttons:[{
						text: GO.lang['cmdOk'],
						handler: function(){
							this.msPanel.submit();
						},
						scope: this
					},{
						text: GO.lang['cmdClose'],
						handler: function(){
							this.msDialog.hide();
						},
						scope:this
					}					
				]
    		});
    	}*/   	
    	this.msDialog.show({milestone_id: grid.selModel.selections.keys[0]});    	
//    	this.msPanel.setMSId(grid.selModel.selections.keys[0]);
			
			}, this);
	},	
	
	setProjectId : function(project_id)
	{
		this.project_id=project_id;
		this.store.baseParams.project_id=project_id;
		
		this.msDialog.msPanel.setProjectId(project_id);
		
		if(this.loaded_project_id!=this.project_id)
		{
			this.loaded_project_id=this.project_id;
			this.store.load();
		}
	} 
});
