GO.tasks.SimpleTasksPanel = function(config)
{
	if(!config)
	{
		config = {};
	}
	
	config.store = new GO.data.JsonStore({
		url: GO.settings.modules.tasks.url+'json.php',
		baseParams: {
			'task': 'tasks',
			'user_id' : GO.settings.user_id,
			'active_only' : true
			},
		root: 'results',
		totalProperty: 'total',
		id: 'id',
		fields:['id', 'name','completed','due_time','description']
	});
	
	
	var checkColumn = new GO.grid.CheckColumn({
     header: '',
     dataIndex: 'completed',
     width: 30,
     header: '<div class="tasks-complete-icon"></div>'
  });
  
  checkColumn.on('change', function(record, checked){
  	this.store.baseParams['completed_task_id']=record.data.id;
  	this.store.baseParams['checked']=checked;
  	
  	//dirty, but it works for updating all the grids
  	this.store.reload({
  		callback:function(){
  			GO.tasks.taskDialog.fireEvent('save', GO.tasks.taskDialog, record.data.id);
  		},
  		scope:this
  	});
  	
  	delete this.store.baseParams['completed_task_id'];
  	delete this.store.baseParams['checked'];
  }, this);
	
	
	
	config.paging=false,			
	config.plugins=checkColumn;
	config.autoExpandColumn=1;
	config.autoExpandMax=2500;
	config.enableColumnHide=false;
  config.enableColumnMove=false;
	config.columns=[
		checkColumn,
		{
			header:GO.lang['strName'],
			dataIndex: 'name',
			renderer:function(value, p, record){
				if(!GO.util.empty(record.data.description))
				{
		     p.attr = 'ext:qtip="'+Ext.util.Format.htmlEncode(record.data.description)+'"';
				}
		    return value;
			}
		},{
			header:GO.tasks.lang.dueDate,
			dataIndex: 'due_time',
			width:100
		}];
	config.view=new Ext.grid.GridView({
		
		emptyText: GO.tasks.lang.noTask
	}),
	config.sm=new Ext.grid.RowSelectionModel();
	config.loadMask=true;
	config.autoHeight=true;
	
	
	GO.tasks.SimpleTasksPanel.superclass.constructor.call(this, config);
	
};


Ext.extend(GO.tasks.SimpleTasksPanel, GO.grid.GridPanel, {
	
	saveListenerAdded : false,
		
	afterRender : function()
	{
		GO.tasks.SimpleTasksPanel.superclass.afterRender.call(this);
		
		GO.tasks.taskDialog.on('save', function(){
					this.store.reload();
				}, this);
    
    this.on("rowdblclick", function(grid, rowClicked, e){	    
				GO.tasks.taskDialog.show({ task_id: grid.selModel.selections.keys[0]});
			}, this);
			
		Ext.TaskMgr.start({
		    run: this.store.load,
		    scope:this.store,
		    interval:960000
		});
	}
	
});




GO.mainLayout.onReady(function(){
	if(GO.summary)
	{
		var tasksGrid = new GO.tasks.SimpleTasksPanel();
		
		GO.summary.portlets['portlet-tasks']=new GO.summary.Portlet({
			id: 'portlet-tasks',
			//iconCls: 'go-module-icon-tasks',
		 	title: GO.tasks.lang.tasks,
			layout:'fit',
			tools: [{
		        id:'close',
		        handler: function(e, target, panel){
		            panel.removePortlet();
		        }
		    }],
			items: tasksGrid,
			autoHeight:true
		});
	}
});