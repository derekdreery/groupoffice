GO.tasks.MainPanel = function(config){
	
	if(!config)
	{
		config = {};
	}
		
	this.taskListsStore = new GO.data.JsonStore({
		url: GO.settings.modules.tasks.url+'json.php',
		baseParams: {
			'task': 'tasklists'
		},
		root: 'results',
		totalProperty: 'total',
		id: 'id',
		fields:['id','dom_id','name','checked']
	});

        this.taskListsPanel= new GO.grid.MultiSelectGrid({
		id:'ta-tasksgrid',
		region:'center',
		loadMask:true,
		store: this.taskListsStore,
		title: GO.tasks.lang.tasklists		
	});

        this.taskListsPanel.on('change', function(grid, tasklists, records)
	{                		                
                this.gridPanel.store.baseParams.tasklists = Ext.encode(tasklists);
                this.gridPanel.store.reload();
                this.tasklist_ids = tasklists;

                if(records.length)
                {
                        this.gridPanel.populateComboBox(records);

                        this.tasklist_id = records[0].data.id;
                        this.tasklist_name = records[0].data.name;
                }

                delete this.gridPanel.store.baseParams.tasklists;
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

        this.categoriesPanel = new GO.tasks.CategoriesGrid(
        {
                id:'ta-categories-grid',
                title:GO.tasks.lang.categories,
                region:'south',
                loadMask:true,
                height:220,
                store:GO.tasks.categoriesStore
        });

        this.categoriesPanel.on('change', function(grid, categories, records)
	{
                this.gridPanel.store.baseParams.categories = Ext.encode(categories);
		this.gridPanel.store.reload();
                
		delete this.gridPanel.store.baseParams.categories;
	}, this);

	this.gridPanel = new GO.tasks.TasksPanel( {
		title:GO.tasks.lang.tasks,
		id:'ta-tasks-grid',
		loadMask:true,
		region:'center'
	});
			
	this.gridPanel.on("delayedrowselect",function(grid, rowIndex, r){
		this.taskPanel.load(r.data.id);
	}, this);

	this.gridPanel.on('rowdblclick', function(grid, rowIndex){
		this.taskPanel.editHandler();
	}, this);

	this.gridPanel.on('checked', function(grid, task_id){
		if(this.taskPanel.data && this.taskPanel.data.id==task_id)
				this.taskPanel.reload();
			
	}, this);
			
	this.gridPanel.store.on('load', function(store){
		this.deleteButton.setDisabled(!store.reader.jsonData.data.write_permission);
		this.addButton.setDisabled(!store.reader.jsonData.data.write_permission);

		this.gridPanel.setTitle(store.reader.jsonData.grid_title);

                var found = false
                for(var i=0; i<this.tasklist_ids.length; i++)
                {
                        if(this.tasklist_ids[i] == this.taskPanel.data.tasklist_id)
                        {                                
                                found = true;
                        }
                }
                if(!found)
                {
                       this.taskPanel.reset();
                }
		
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

					GO.tasks.showTaskDialog({
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
				iconCls: 'btn-export',
				text: GO.lang.cmdExport,
				cls: 'x-btn-text-icon',
				handler:function(){
					var config = {};
					config.colModel = this.gridPanel.getColumnModel();
					config.title = GO.tasks.lang.tasks;

					var query = this.gridPanel.searchField.getValue();
					if(!GO.util.empty(query))
					{
						config.subtitle= GO.lang.searchQuery+': '+query;
					}else
					{
						config.subtitle='';
					}

					if(!this.exportDialog)
					{
						this.exportDialog = new GO.ExportQueryDialog({
							query:'get_tasks'
						});
					}
					this.exportDialog.show(config);

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
			]
		})
				
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
		filterPanel,
                this.categoriesPanel
		]
	}),
	this.gridPanel,
	this.taskPanel
	];
	
	GO.tasks.MainPanel.superclass.constructor.call(this, config);
	
}
 
Ext.extend(GO.tasks.MainPanel, Ext.Panel,{

        tasklist_ids: [],
	afterRender : function()
	{
		GO.tasks.MainPanel.superclass.afterRender.call(this);

		GO.tasks.taskDialogListeners= GO.tasks.taskDialogListeners || [];
		GO.tasks.taskDialogListeners.push({
			scope:this,
			save:function(){
				this.gridPanel.store.reload();
			}
		});
                
		this.taskListsStore.on('load', function(){
                        /*
			var defaultRecord;
			if(this.gridPanel.store.baseParams.tasklists)
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
                        */
                        
                        var records = [];
                        for(var i=0; i<this.taskListsStore.data.length; i++)
                        {
                                var item = this.taskListsStore.data.items[i];                                
                                if(item.data.checked)
                                {
                                        records.push(item);
                                }
                        }

                        if(records.length)
                        {
                                this.gridPanel.populateComboBox(records);

                                this.tasklist_id = records[0].data.id;
                                this.tasklist_name = records[0].data.name;
                        }
                       
                        this.gridPanel.store.load();
			GO.tasks.categoriesStore.load();
                       
		},this);

		GO.tasks.categoriesStore.on('load', function(){
			if(GO.tasks.taskDialog)
			{
				GO.tasks.taskDialog.populateComboBox(GO.tasks.categoriesStore.data.items);
			}
		    
		},this);

		this.taskListsStore.load();
               
		
		GO.mainLayout.on('linksDeleted', function(deleteConfig, link_types){
			GO.mainLayout.onLinksDeletedHandler(link_types[12], this, this.gridPanel.store);
		}, this);    
	},
  
	showAdminDialog : function() {
		
		if(!this.adminDialog)
		{
			this.tasklistDialog = new GO.tasks.TasklistDialog();
                        this.categoryDialog = new GO.tasks.CategoryDialog();

			GO.tasks.writableTasklistsStore.on('load', function(){
				if(GO.tasks.writableTasklistsStore.reader.jsonData.new_default_tasklist){
					GO.tasks.defaultTasklist=GO.tasks.writableTasklistsStore.reader.jsonData.new_default_tasklist;
				}
			
			}, this);
			
			this.tasklistDialog.on('save', function(){
				GO.tasks.writableTasklistsStore.load();
				this.taskListsStore.load();
			}, this);

                        this.categoryDialog.on('save', function(){
                                GO.tasks.categoriesStore.load();				
                        },this);
			
			this.tasklistsGrid = new GO.grid.GridPanel( {
				paging:true,
				border:false,
                                title: GO.tasks.lang.tasklists,
				store: GO.tasks.writableTasklistsStore,
				deleteConfig: {
					callback:function(){
						this.taskListsStore.load();
					},
					scope:this
				},
				columns:[{
					header:GO.lang['strName'],
					dataIndex: 'name',
					sortable:true
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
					disabled: !GO.settings.modules.tasks.write_permission,
					scope: this
				},{
					iconCls: 'btn-delete',
					text: GO.lang['cmdDelete'],
					cls: 'x-btn-text-icon',
					disabled: !GO.settings.modules.tasks.write_permission,
					handler: function(){
						this.tasklistsGrid.deleteSelected();
					},
					scope:this
				}]
			});

                        this.categoriesGrid = new GO.grid.GridPanel( {
				paging:true,
				border:false,
                                title: GO.tasks.lang.categories,
				store: GO.tasks.categoriesStore,
				deleteConfig: {
					callback:function(){
						GO.tasks.categoriesStore.load();
					},
					scope:this
				},
				columns:[{
					header:GO.lang['strName'],
					dataIndex: 'name',
					sortable:true
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
						this.categoryDialog.show();
					},
					disabled: !GO.settings.modules.tasks.write_permission,
					scope: this
				},{
					iconCls: 'btn-delete',
					text: GO.lang['cmdDelete'],
					cls: 'x-btn-text-icon',
					disabled: !GO.settings.modules.tasks.write_permission,
					handler: function(){
						this.categoriesGrid.deleteSelected();
					},
					scope:this
				}]
			});
			
			this.tasklistsGrid.on("rowdblclick", function(grid, rowClicked, e){

				this.tasklistDialog.show(grid.selModel.selections.keys[0]);
			}, this);

                        this.categoriesGrid.on('rowdblclick', function(grid, rowIndex)
                        {                            
                                var record = grid.getStore().getAt(rowIndex);
                                this.categoryDialog.show(record);

                        }, this);

                        this.tabPanel = new Ext.TabPanel({
                                activeTab:0,
                                border:false,
                                items:[this.tasklistsGrid,this.categoriesGrid]
                        })

			this.adminDialog = new Ext.Window({
				title: GO.lang.cmdSettings,
				layout:'fit',
				modal:false,
				minWidth:300,
				minHeight:300,
				height:400,
				width:600,
				closeAction:'hide',				
				items: this.tabPanel,
				buttons:[{
					text:GO.lang['cmdClose'],
					handler: function(){
						this.adminDialog.hide()
					},
					scope: this
				}]
			});
			
		}
		
		if(!GO.tasks.writableTasklistsStore.loaded){
			GO.tasks.writableTasklistsStore.load();
		}

                if(!GO.tasks.categoriesStore.loaded){
			GO.tasks.categoriesStore.load();
		}
	
		this.adminDialog.show();
	}
	
});


GO.tasks.showTaskDialog = function(config){

	if(!GO.tasks.taskDialog)
		GO.tasks.taskDialog = new GO.tasks.TaskDialog();

	if(GO.tasks.taskDialogListeners){
		for(var i=0;i<GO.tasks.taskDialogListeners.length;i++){
			GO.tasks.taskDialog.on(GO.tasks.taskDialogListeners[i]);
		}
		delete GO.tasks.taskDialogListeners;
	}

	GO.tasks.taskDialog.populateComboBox(GO.tasks.categoriesStore.data.items);

	GO.tasks.taskDialog.show(config);
}



GO.tasks.writableTasklistsStore = new GO.data.JsonStore({
	url: GO.settings.modules.tasks.url+'json.php',
	baseParams: {
		'task': 'tasklists',
		'auth_type':'write'
	},
	root: 'results',
	totalProperty: 'total',
	id: 'id',
	fields:['id','name','user_name'],
	remoteSort:true,
	sortInfo: {
		field: 'name',
		direction: 'ASC'
	}
});

GO.tasks.categoriesStore = new GO.data.JsonStore({
	url: GO.settings.modules.tasks.url+'json.php',
	baseParams: {
                'task': 'categories'
	},
	root: 'results',
	totalProperty: 'total',
	id: 'id',
	fields:['id','name','user_name','checked'],
	remoteSort:true,
	sortInfo: {
		field: 'name',
		direction: 'ASC'
	}
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

	if(!GO.tasks.taskLinkWindow){
		var taskPanel = new GO.tasks.TaskPanel();
		GO.tasks.taskLinkWindow = new GO.LinkViewWindow({
			title: GO.tasks.lang.task,
			closeAction:'hide',
			items: taskPanel,
			taskPanel: taskPanel
		});
	}
	GO.tasks.taskLinkWindow.taskPanel.load(id);
	GO.tasks.taskLinkWindow.show();
}

GO.linkPreviewPanels[12]=function(config){
	config = config || {};
	return new GO.tasks.TaskPanel(config);
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

		GO.tasks.showTaskDialog(taskShowConfig);
	}
});
