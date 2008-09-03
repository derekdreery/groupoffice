GO.projects.ProjectsPanel = function(config){
	
	if(!config)
	{
		config = {};
	}
	
	config.store = new GO.data.JsonStore({
		url: GO.settings.modules.projects.url+'json.php',
		baseParams: {
			'task': 'projects'
			},
		root: 'results',
		totalProperty: 'total',
		id: 'id',
		fields:['id', 'name','description']
	});
	
	config.store.on('load', function(){
		GO.projects.stores.bookableProjects.reload();
	});
		
	
	config.paging=true,			
	config.autoExpandColumn=1;
	config.autoExpandMax=2500;
	config.enableColumnHide=false;
  config.enableColumnMove=false;
	config.columns=[
		{
			header:GO.lang['strName'],
			dataIndex: 'name',
			width:200
		},
		{
			header:GO.lang['strDescription'],
			dataIndex: 'description'
		}];
	config.view=new Ext.grid.GridView({
		//autoFill: true,
		//forceFit: true,
		emptyText: GO.projects.lang.noProjects
	}),
	config.sm=new Ext.grid.RowSelectionModel();
	config.loadMask=true;
	
	config.tbar=[{
			iconCls: 'btn-add',							
			text: GO.lang['cmdAdd'],
			cls: 'x-btn-text-icon',
			handler: function(){
				if(GO.projects.max_projects>0 && this.store.totalLength>=GO.projects.max_projects)
  			{
  				Ext.Msg.alert(GO.lang.strError, GO.projects.lang.maxProjectsReached);
  			}else
  			{
					GO.projects.projectDialog.show();
  			}				
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

	if(GO.projects.max_projects>0)
  {
	   config.bbar = new Ext.PagingToolbar({
	        store: config.store,
	        pageSize: parseInt(GO.settings['max_rows_list']),
	        displayInfo: true,
	        displayMsg: GO.lang['displayingItems']+'. '+GO.lang.strMax+' '+GO.projects.max_projects,
	        emptyMsg: GO.lang['strNoItems']
	    });
   }
	
	
	GO.projects.ProjectsPanel.superclass.constructor.call(this, config);
	
};


Ext.extend(GO.projects.ProjectsPanel, GO.grid.GridPanel, {
	
	afterRender : function()
	{
		GO.projects.ProjectsPanel.superclass.afterRender.call(this);
		
		GO.projects.projectDialog.on('save', function(){
				this.store.reload();
			}, this);
 
    this.on("rowdblclick", function(grid, rowClicked, e){
				GO.projects.projectDialog.show({ project_id: grid.selModel.selections.keys[0]});
			}, this); 
		
		this.store.load();
	}
});
