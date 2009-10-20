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


	this.taskListsPanel= new GO.grid.GridPanel({
			id:'ta-tasksgrid',
			region:'center',
			store: this.taskListsStore,
			cls:'go-grid3-hide-headers',
			title: GO.tasks.lang.tasklists,
			items:this.tasksLists,
			loadMask:true,
			autoScroll:true,
			border:true,
			split:true,
			sm: new Ext.grid.RowSelectionModel({singleSelect:true}),
			viewConfig: {forceFit:true, autoFill: true},
			columns: [				
				{
					header: GO.lang.strName, 
					dataIndex: 'name'					
				}
			]
		});
		
	this.taskListsPanel.on('rowclick', function(grid, index){		
				this.tasklist_id = grid.store.data.items[index].data.id;
				this.tasklist_name = grid.store.data.items[index].data.name;
				//this.gridPanel.tasklist_id=this.tasklist_id;
				this.gridPanel.store.baseParams['tasklist_id']=this.tasklist_id;				
				this.gridPanel.store.load();
		}, this);
				
	var showCompletedCheck = new Ext.form.Checkbox({		
		boxLabel: GO.tasks.lang.showCompletedTasks,
		hideLabel: true,
		checked:GO.tasks.showCompleted
	});
	
	showCompletedCheck.on('check', function(cb, checked){		
		this.gridPanel.store.baseParams['show_completed']=checked? '1' : '0';
		this.gridPanel.store.reload();
		delete this.gridPanel.store.baseParams['show_completed'];
	}, this);
	
	
	var showInactiveCheck = new Ext.form.Checkbox({		
		boxLabel: GO.tasks.lang.showInactiveTasks,
		hideLabel: true,
		checked:GO.tasks.showInactive
	});
	
	showInactiveCheck.on('check', function(cb, checked){		
		this.gridPanel.store.baseParams['show_inactive']=checked? '1' : '0';
		this.gridPanel.store.reload();
		delete this.gridPanel.store.baseParams['show_inactive'];
	}, this);
	
				
	var filterPanel = new Ext.form.FormPanel({
		title:GO.tasks.lang.filter,							
		height:85,
		cls:'go-form-panel',
		waitMsgTarget:true,
		region:'north',
		border:true,
		split:true,
		items: [showCompletedCheck, showInactiveCheck]
	});

	this.gridPanel = new GO.tasks.TasksPanel( {
		title:GO.tasks.lang.tasks,
				region:'center'								
			});
			
	this.gridPanel.on("delayedrowselect",function(grid, rowIndex, r){
		this.taskPanel.load(r.data.id);
	}, this);

	this.gridPanel.on('rowdblclick', function(grid, rowIndex){
		var record = grid.getStore().getAt(rowIndex);
		GO.tasks.taskDialog.show({task_id: record.data.id});
	}, this);
			
	this.gridPanel.store.on('load', function(store){
		this.deleteButton.setDisabled(!store.reader.jsonData.write_permission);		
		this.addButton.setDisabled(!store.reader.jsonData.write_permission);

		this.gridPanel.setTitle(this.tasklist_name);
		
	}, this);
	
	this.taskPanel = new GO.tasks.TaskPanel({
		title:GO.tasks.lang.task,
		region:'east',
		width:400,
		border:true
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
					items: [this.addButton = new Ext.Button({
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
							}),this.deleteButton = new Ext.Button({		
								iconCls: 'btn-delete',							
								text: GO.lang['cmdDelete'],
								cls: 'x-btn-text-icon',
								handler: function(){
									this.gridPanel.deleteSelected({
										callback : this.taskPanel.gridDeleteCallback,
										scope: this.taskPanel
									});
								},
								scope: this
							}),{
								iconCls: 'btn-settings',
								text: GO.lang.administration,
								cls: 'x-btn-text-icon',
								handler: function(){
									this.showAdminDialog();	
								},
								scope: this												
							},{
									iconCls: 'btn-refresh',
									text: GO.lang['cmdRefresh'],
									cls: 'x-btn-text-icon',
									handler: function(){
										this.taskListsStore.load();
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
						this.taskListsPanel,
						filterPanel
						]
       }),
       this.gridPanel,
       this.taskPanel
       ];
	
	GO.tasks.MainPanel.superclass.constructor.call(this, config);
	
}
 
Ext.extend(GO.tasks.MainPanel, Ext.Panel,{
	afterRender : function()
	{
		GO.tasks.MainPanel.superclass.afterRender.call(this);
		
		GO.tasks.taskDialog.on('save', function(){
			this.gridPanel.store.reload();
		}, this);
					
		this.taskListsStore.on('load', function(){

			var defaultRecord;

			if(this.gridPanel.store.baseParams.tasklist_id)
				defaultRecord=this.taskListsStore.getById(this.gridPanel.store.baseParams.tasklist_id);
			
			if(!defaultRecord)
				defaultRecord = this.taskListsStore.getById(GO.tasks.defaultTasklist.id);
			
			if(!defaultRecord)
				defaultRecord =  this.taskListsStore.getAt(0);

			this.tasklist_id = defaultRecord.id;
			this.tasklist_name = defaultRecord.get('name');

			this.gridPanel.store.baseParams['tasklist_id']=this.tasklist_id;
			this.gridPanel.store.load({
				callback:function(){
					var sm = this.taskListsPanel.getSelectionModel();
					sm.selectRecords([defaultRecord]);
				},
				scope: this
			});
		},this);

		this.taskListsStore.load();
    
	},
  
 	showAdminDialog : function() {
		
		if(!this.adminDialog)
		{
			this.tasklistDialog = new GO.tasks.TasklistDialog();

			GO.tasks.writableTasklistsStore.on('load', function(){
				if(GO.tasks.writableTasklistsStore.reader.jsonData.new_default_tasklist){
					GO.tasks.defaultTasklist=GO.tasks.writableTasklistsStore.reader.jsonData.new_default_tasklist;
				}
			
			}, this);
			
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


GO.mainLayout.onReady(function(){
	GO.tasks.taskDialog = new GO.tasks.TaskDialog();
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
		/*if(!GO.tasks.taskDialog)
		{
			GO.tasks.taskDialog = new GO.tasks.TaskDialog();		
		}
		GO.tasks.taskDialog.show({task_id: id, link_config: link_config});*/
		
		var taskPanel = new GO.tasks.TaskPanel();
		var linkWindow = new GO.LinkViewWindow({
			title: GO.tasks.lang.task,
			items: taskPanel
		});
		taskPanel.load(id);
		linkWindow.show();
	}


GO.newMenuItems.push({
	text: GO.tasks.lang.task,
	iconCls: 'go-link-icon-12',
	handler:function(item, e){
		if(!GO.tasks.taskDialog)
		{
			GO.tasks.taskDialog = new GO.tasks.TaskDialog();		
		}

		var taskShowConfig = item.parentMenu.taskShowConfig || {};
		taskShowConfig.link_config=item.parentMenu.link_config

		GO.tasks.taskDialog.show(taskShowConfig);
	}
});
