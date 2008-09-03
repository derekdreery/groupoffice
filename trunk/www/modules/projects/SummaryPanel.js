GO.projects.SummaryPanel = Ext.extend(Ext.Panel, {
	
	initComponent : function(){
		
		this.layout='border';
		
		
		this.store = new Ext.data.GroupingStore({
			reader: new Ext.data.JsonReader({
        totalProperty: "count",
		    root: "results",
		    id: "id",
		    fields:[
		    'id',
		    'name', 
		    'customer', 
		    'description',
		    'items' 
		    
		    ]}),
		    
	  		baseParams: {task:'summary'},
			proxy: new Ext.data.HttpProxy({
		      url: GO.settings.modules.projects.url+'json.php'
		  }),        
	    groupField:'customer',
	    sortInfo: {field: 'id', direction: 'DESC'},
	    remoteSort:true
	  });
	  
	  
	 
		
		this.centerPanel = new GO.grid.GridPanel({
			title:GO.projects.lang.activeProjects,
			region: 'center',
			store: this.store,
			columns:[
				{
					header:GO.projects.lang.project,
					dataIndex: 'customer',
					width: 120
				},
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
				}],
			view:  new Ext.grid.GroupingView({
				autoFill:true,
				forceFit:true,
		    hideGroupedColumn:true,
		    groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "Items" : "Item"]})',
		   	emptyText: GO.projects.lang.noProjects,
		   	showGroupName:false,
		   	enableRowBody:true,
				showPreview:true,				
				getRowClass : function(record, rowIndex, p, store){
				    if(this.showPreview && record.data.description.length){
				        p.body = record.data.description;
				        return 'x-grid3-row-expanded';
				    }
				    return 'x-grid3-row-collapsed';
				}
			}),
			sm:new Ext.grid.RowSelectionModel({singleSelect:true}),
			loadMask:true
		});
		
		this.centerPanel.on('rowclick', function(grid){
			var selectionModel = grid.getSelectionModel();
			var record = selectionModel.getSelected();
			
			this.projectPanel.loadProject(record.id);			
		}, this);
		
		this.projectPanel = new GO.projects.ProjectPanel({
			title:GO.projects.lang.projectInfo,
			region: 'east',
			width: 400,
			split:true
		});
		
		this.projectPanel.on('milestonecheck', function(milestone_id, checked){
			var row = Ext.get('pm-sum-milestone-'+milestone_id);			
			if(checked)
			{				
				row.addClass('projects-completed');
			}else
			{
				row.removeClass('projects-completed');
			}
		}, this);
		//this.defaults={border: false};
		
		this.items = [this.centerPanel, this.projectPanel];
		
		GO.projects.SummaryPanel.superclass.initComponent.call(this);
	},
	
	afterRender : function(){
		this.store.load();
		GO.projects.SummaryPanel.superclass.afterRender.call(this);
	}
	
});