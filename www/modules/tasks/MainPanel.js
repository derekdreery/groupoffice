GO.tasks.MainPanel = function(config){
	
	if(!config)
	{
		config = {};
	}

	
	
	
		
		
	this.taskListsStore = new GO.data.JsonStore({
		url: GO.settings.modules.tasks.url+'json.php',
		baseParams: {'task': 'tasklists'},
		root: 'results',
		totalProperty: 'total',
		id: 'id',
		fields:['id','dom_id','name']
	});
	
	

	this.tasksLists= new GO.grid.SimpleSelectList({
		store: this.taskListsStore		
		});
		
	

	this.tasksLists.on('click', function(dataview, index){		
				this.tasklist_id = dataview.store.data.items[index].data.id;
				this.tasklist_name = dataview.store.data.items[index].data.name;
				//this.gridPanel.tasklist_id=this.tasklist_id;
				this.gridPanel.store.baseParams['tasklist_id']=this.tasklist_id;				
				this.gridPanel.store.load();
		}, this);
	
	

	
	var taskListsPanel= new Ext.Panel({
					region:'center',
					title: GO.tasks.lang.tasklists,
					items:this.tasksLists,
					autoScroll:true,
					border:true,
					split:true
				});
				
	var showCompletedCheck = new Ext.form.Checkbox({		
		boxLabel: GO.tasks.lang.showCompletedTasks,
		hideLabel: true				
	});
	
	showCompletedCheck.on('check', function(cb, checked){		
		this.gridPanel.store.baseParams['show_completed']=checked;
		this.gridPanel.store.reload();
	}, this);
	
	
	var showInactiveCheck = new Ext.form.Checkbox({		
		boxLabel: GO.tasks.lang.showInactiveTasks,
		hideLabel: true				
	});
	
	showInactiveCheck.on('check', function(cb, checked){		
		this.gridPanel.store.baseParams['show_inactive']=checked;
		this.gridPanel.store.reload();
	}, this);
	
				
	var filterPanel = new Ext.form.FormPanel({
		title:GO.tasks.lang.filter,							
		height:80,
		cls:'go-form-panel',
		waitMsgTarget:true,
		region:'north',
		border:true,
		split:true,
		items: [showCompletedCheck, showInactiveCheck]
	});

				

	

				
	this.gridPanel = new GO.tasks.TasksPanel( {
				region:'center'								
			});
			
        

	config.layout='border';
	//config.tbar=;
	config.items=[
			new Ext.Panel({
				region:'north',		
				height:32,
				baseCls:'x-plain',
				tbar:new Ext.Toolbar({		
					cls:'go-head-tb',
					items: [{
								iconCls: 'btn-add',							
								text: GO.lang['cmdAdd'],
								cls: 'x-btn-text-icon',
								handler: function(){
									if(!GO.tasks.taskDialog)
									{
										GO.tasks.taskDialog = new GO.tasks.TaskDialog();		
									}
									if(!GO.tasks.taskDialog.hasListener('save'))
									{
										GO.tasks.taskDialog.on('save', function(){
											this.gridPanel.store.reload();
										}, this);
									}
									GO.tasks.taskDialog.show({
										tasklist_id: this.tasklist_id,
										tasklist_name: this.tasklist_name
									});
									
								},
								scope: this
							},{
		
								iconCls: 'btn-delete',							
								text: GO.lang['cmdDelete'],
								cls: 'x-btn-text-icon',
								handler: function(){
									this.gridPanel.deleteSelected();
								},
								scope: this
							},{
								iconCls: 'btn-settings',
								text: GO.lang['cmdSettings'],
								cls: 'x-btn-text-icon',
								handler: function(){
									this.showAdminDialog();	
								},
								scope: this												
							}
							]})
				
			}),
       new Ext.Panel({
          region:'west',
          titlebar: false,
					autoScroll:false,
					closeOnTab: true,
					width: 210,
					split:true,
					resizable:true,
					layout:'border',							
					baseCls: 'x-plain',								
					items:[
						taskListsPanel,
						filterPanel
						]
       }),
       this.gridPanel
       ];
	
	GO.tasks.MainPanel.superclass.constructor.call(this, config);
	
}
 
Ext.extend(GO.tasks.MainPanel, Ext.Panel,{
	afterRender : function()
	{
		GO.tasks.MainPanel.superclass.afterRender.call(this);
		this.taskListsStore.load({
			callback: function(){
				
				this.tasklist_id = this.taskListsStore.data.items[0].data.id;		
				this.tasklist_name = this.taskListsStore.data.items[0].data.name;
						
				this.tasksLists.select(this.taskListsStore.data.items[0].data.dom_id);								
				this.gridPanel.store.baseParams['tasklist_id']=this.tasklist_id;
				//this.gridPanel.tasklist_id=this.tasklist_id;								
				this.gridPanel.store.load();
			},
			scope: this
			
		});
		
		
	
    
	},
	
	
  
  
  
 	showAdminDialog : function() {
		
		if(!this.adminDialog)
		{
			

			

			
			
			
						
			this.tasklistDialog = new GO.tasks.TasklistDialog();
			
			this.tasklistDialog.on('save', function(){
				GO.tasks.writableTasklistsStore.load();
				this.taskListsStore.load();				
			}, this);
			
			
			
			this.tasklistsGrid = new GO.grid.GridPanel( {
				paging:true,
				border:false,
				store: GO.tasks.writableTasklistsStore,
				deleteConfig: {
					callback:function(){
						this.taskListsStore.load();
					},
					scope:this
				},
				columns:[{
						header:GO.lang['strName'],
						dataIndex: 'name'
					},{
						header:GO.lang['strOwner'],
						dataIndex: 'user_name'
					}],						
				view:new  Ext.grid.GridView({
					autoFill:true
				}),
				sm: new Ext.grid.RowSelectionModel(),
				loadMask: true,
				tbar: [{						
						iconCls: 'btn-add',
						text: GO.lang['cmdAdd'],
						cls: 'x-btn-text-icon',
						handler: function(){
							this.tasklistDialog.show();
						},
						scope: this
					},{
						iconCls: 'btn-delete',
						text: GO.lang['cmdDelete'],
						cls: 'x-btn-text-icon',
						handler: function(){
							this.tasklistsGrid.deleteSelected();
						},
						scope:this				
					}]
			});
			
			this.tasklistsGrid.on("rowdblclick", function(grid, rowClicked, e){

				this.tasklistDialog.show(grid.selModel.selections.keys[0]);
			}, this);
			
			
			
			
			
			this.adminDialog = new Ext.Window({
				title: GO.tasks.lang.tasklists,
				layout:'fit',
				modal:false,
				minWidth:300,
				minHeight:300,
				height:400,
				width:600,
				closeAction:'hide',
				
				items: this.tasklistsGrid,
				buttons:[{
					text:GO.lang['cmdClose'],
					handler: function(){this.adminDialog.hide()}, 
					scope: this
					}]
			});
			
		}
		
			if(!GO.tasks.writableTasklistsStore.loaded){
				GO.tasks.writableTasklistsStore.load();
			}
		
		this.adminDialog.show();			
	}
	
});



GO.tasks.writableTasklistsStore = new GO.data.JsonStore({
	url: GO.settings.modules.tasks.url+'json.php',
	baseParams: {'task': 'tasklists', 'auth_type':'write'},
	root: 'results',
	totalProperty: 'total',
	id: 'id',
	fields:['id','name','user_name'],
	remoteSort:true
});


/*
 * This will add the module to the main tabpanel filled with all the modules
 */
 
GO.moduleManager.addModule('tasks', GO.tasks.MainPanel, {
	title : GO.tasks.lang.tasks,
	iconCls : 'go-tab-icon-tasks'
});
/*
 * If your module has a linkable item, you should add a link handler like this. 
 * The index (no. 1 in this case) should be a unique identifier of your item.
 * See classes/base/links.class.inc for an overview.
 * 
 * Basically this function opens a task window when a user clicks on it from a 
 * panel with links. 
 */
GO.linkHandlers[12]=function(id, link_config){
		if(!GO.tasks.taskDialog)
		{
			GO.tasks.taskDialog = new GO.tasks.TaskDialog();		
		}
		GO.tasks.taskDialog.show({task_id: id, link_config: link_config});
	}


GO.newMenuItems.push({
	text: GO.tasks.lang.task,
	iconCls: 'go-link-icon-12',
	handler:function(item, e){
		if(!GO.tasks.taskDialog)
		{
			GO.tasks.taskDialog = new GO.tasks.TaskDialog();		
		}
		GO.tasks.taskDialog.show({
			link_config: item.parentMenu.link_config			
		});
	}
});
