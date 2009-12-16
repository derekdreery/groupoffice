GO.tasks.TasksPanel = function(config)
	{
		if(!config)
		{
			config = {};
		}
	
       
		this.checkColumn = new GO.grid.CheckColumn({
			dataIndex: 'completed',
			width: 30,
			hideInExport:true,
			header: '<div class="tasks-complete-icon"></div>',
			sortable:false
		});
  
		this.checkColumn.on('change', function(record, checked){
			this.store.baseParams['completed_task_id']=record.data.id;
			this.store.baseParams['checked']=checked;
  	
			//dirty, but it works for updating all the grids
			this.store.reload({
				callback:function(){
					GO.tasks.taskDialog.fireEvent('save', GO.tasks.taskDialog, record.data.id);
					this.fireEvent('checked', this, record.data.id);
				},
				scope:this
			});
  	
			delete this.store.baseParams['completed_task_id'];
			delete this.store.baseParams['checked'];
		}, this);
    
		var fields ={
			fields:['id', 'name','completed','due_time', 'late', 'description', 'status', 'ctime', 'mtime', 'start_time', 'completion_time'],
			columns:[this.checkColumn,
			{
				id:'name',
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
			},{
				header: GO.tasks.lang.startsAt,
				dataIndex: 'start_time',
				hidden:true,
				width:110
			},{
				header: GO.tasks.lang.completedAt,
				dataIndex: 'completion_time',
				hidden:true,
				width:110
			},{
				header: GO.lang.strStatus,
				dataIndex: 'status',
				hidden:true,
				width:110
			},{
				header: GO.lang.strCtime,
				dataIndex: 'ctime',
				hidden:true,
				width:110
			},{
				header: GO.lang.strMtime,
				dataIndex: 'mtime',
				hidden:true,
				width:110
			}]
		};

		if(GO.customfields)
		{
			GO.customfields.addColumns(12, fields);
		}

		config.store = new GO.data.JsonStore({
			url: GO.settings.modules.tasks.url+'json.php',
			baseParams: {
				'task': 'tasks'
			},
			root: 'results',
			totalProperty: 'total',
			id: 'id',
			fields: fields.fields,
			remoteSort:true
		});

		var columnModel =  new Ext.grid.ColumnModel(fields.columns);
		columnModel.defaultSortable = true;
		config.cm=columnModel;

		config.paging=true,
		config.plugins=this.checkColumn;
		config.autoExpandColumn='name';
		config.autoExpandMax=2500;
		config.enableColumnHide=true;
		config.enableColumnMove=true;

		// custom template for the grid header
		var headerTpl = new Ext.Template(
			'<table border="0" cellspacing="0" cellpadding="0" style="{tstyle}">',
			'<thead><tr class="x-grid3-hd-row">{cells}</tr></thead>',
			'<tbody><tr class="new-task-row">',
			'<td><div id="tasks-new-task-icon"></div></td>',
			// '<td><table border="0" cellspacing="0" cellpadding="0"><tr><td><div class="x-small-editor" id="new-task-name"></div></td><td><div class="x-small-editor" id="new-task-link"></div></td></tr></table></td>',
			'<td><div class="x-small-editor" id="new-task-name"></div></td>',
			'<td><div class="x-small-editor" id="new-task-due"></div></td>',
			'</tr></tbody>',
			"</table>"
			);

		config.view=new Ext.grid.GridView({
			//autoFill: true,
			//forceFit: true,
			emptyText: GO.tasks.lang.noTask,
			templates: {
				header: headerTpl
			},
			getRowClass : function(record, rowIndex, p, store){
				if(record.data.late){
					return 'tasks-late';
				}
			}
		});
		config.sm=new Ext.grid.RowSelectionModel();
		config.loadMask=true;

		this.searchField = new GO.form.SearchField({
			store: config.store,
			width:320
		});

		config.tbar = [GO.lang['strSearch'] + ':', this.searchField];
	
		GO.tasks.TasksPanel.superclass.constructor.call(this, config);

		this.addEvents({checked : true});
	
	};


Ext.extend(GO.tasks.TasksPanel, GO.grid.GridPanel, {
	
	saveListenerAdded : true,
	
	afterRender : function()
	{
		GO.tasks.TasksPanel.superclass.afterRender.call(this);
		
		// The fields in the grid's header
		this.ntName = new Ext.form.TextField({
			renderTo: 'new-task-name',
			emptyText: GO.tasks.lang.addTask,
			width: 200
		});

		this.ntDue = new Ext.form.DateField({
			renderTo: 'new-task-due',
			value: new Date(),
			disabled:true,
			format : GO.settings.date_format
		});
    
   
		/*this.ntSelectLink = new GO.form.SelectLink({
    	renderTo:'new-task-link',
    	disabled:true,
    	emptyText: GO.tasks.lang.createLink
    });*/
    
		/* this.on("rowdblclick", function(grid, rowClicked, e){
	    	if(!GO.tasks.taskDialog)
				{
					GO.tasks.taskDialog = new GO.tasks.TaskDialog();		
				}
				if(!this.saveListenerAdded)
				{
					this.saveListenerAdded=true;
					GO.tasks.taskDialog.on('save', function(){
						this.store.reload();
					}, this);
				}
				GO.tasks.taskDialog.show({ task_id: grid.selModel.selections.keys[0]});
			}, this);*/

    
    
    
    
    
		this.editing = false;
		this.focused = false;
		this.userTriggered = false;
    
		var handlers = {
			focus: function(){
				this.focused = true;
			},
			blur: function(){
				this.focused = false;
				this.doBlur.defer(250, this);
			},
			specialkey: function(f, e){
				if(e.getKey()==e.ENTER){
					this.userTriggered = true;
					e.stopEvent();
					f.el.blur();
					if(f.triggerBlur){
						f.triggerBlur();
					}
				}
			},
			scope:this
		}
		this.ntName.on(handlers, this);
		this.ntDue.on(handlers, this);
		//this.ntSelectLink.on(handlers, this);
    
    

		this.ntName.on('focus', function(){
			this.focused = true;
			if(!this.editing){
				this.ntDue.enable();
				//this.ntSelectLink.enable();
				this.syncFields();
				this.editing = true;
			}
		}, this);

    
		//there should be a view ready event
		this.syncFields.defer(200,this);
		  
	},
	
	syncFields : function(){
		
		var cm = this.getColumnModel();
		//this.ntSelectLink.setSize(cm.getColumnWidth(1)-204);
		this.ntName.setSize(cm.getColumnWidth(1)-4);
		this.ntDue.setSize(cm.getColumnWidth(2)-4);
		
	},
	
	// when a field in the add bar is blurred, this determines
	// whether a new task should be created
	doBlur : function(){
		if(this.editing && !this.focused){
			var taskname = this.ntName.getValue();
			var due = this.ntDue.getValue();
			// var link = this.ntSelectLink.getValue();
			if(!Ext.isEmpty(taskname)){
            
				Ext.Ajax.request({
					url: GO.settings.modules.tasks.url+'action.php',
					params: {
						task: 'save_task',
						tasklist_id: this.store.baseParams.tasklist_id,
						name: taskname,
						//link: link,
						start_date: due.format(GO.settings.date_format),
						due_date: due.format(GO.settings.date_format)
					},
					callback: function(options, success, response)
					{
								
						var reponseParams = Ext.decode(response.responseText);
						if(!reponseParams.success)
						{
							alert(reponseParams.feedback);
						}else
						{
							//dirty, but it works for updating other grids like on the summary
							GO.tasks.taskDialog.fireEvent('save', GO.tasks.taskDialog, reponseParams.task_id);
							
									
							this.store.reload();
						}
								
					},
					scope:this
				});
            
            
				this.ntName.setValue('');
				if(this.userTriggered){ // if the entered to add the task, then go to a new add automatically
					this.userTriggered = false;
					this.ntName.focus.defer(100, this.ntName);
				}
			}
			//this.ntSelectLink.reset();
			//this.ntSelectLink.disable();
			this.ntDue.disable();
			this.editing = false;
		}
	}
});

