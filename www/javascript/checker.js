
GO.CheckerWindow = function(config){
	
	if(!config)
	{
		config = {};
	}
	
	config.title=GO.lang.reminders;
	config.maximizable=true;
	config.layout='fit';
	config.modal=false;
	config.resizable=true;
	config.closeAction='hide';
	
	if(!config.width)
		config.width=600;
	if(!config.height)
		config.height=500;


	config.buttons=[{
		text: GO.lang['cmdClose'],
		handler: function(){
			this.hide();
		},
		scope:this
	}
	];

	GO.checkerSnoozeTimes = [
		[300,'5 '+GO.lang.strMinutes],
		[600, '10 '+GO.lang.strMinutes],
		[1200, '20 '+GO.lang.strMinutes],
		[1800, '30 '+GO.lang.strMinutes],
		[3600, '1 '+GO.lang.strHour],
		[7200, '2 '+GO.lang.strHours],
		[10800, '3 '+GO.lang.strHours],
		[14400, '4 '+GO.lang.strHours],
		[86400, '1 '+GO.lang.strDay],
		[2*86400, '2 '+GO.lang.strDays],
		[3*86400, '3 '+GO.lang.strDays],
		[4*86400, '4 '+GO.lang.strDays],
		[5*86400, '5 '+GO.lang.strDays],
		[6*86400, '6 '+GO.lang.strDays],
		[7*86400, '7 '+GO.lang.strDays]
	];

	var snoozeMenuItems = [];

	for(var i=0,max=GO.checkerSnoozeTimes.length;i<max;i++){
		snoozeMenuItems.push(	{
			text: GO.checkerSnoozeTimes[i][1],
			value: GO.checkerSnoozeTimes[i][0],
			handler:function(i){
				this.checkerGrid.doTask('snooze_reminders', i.value);
			},
			scope: this
		});
	}
	
	var snoozeMenu = new Ext.menu.Menu({
		items:snoozeMenuItems
	});

	config.tbar=[{
		iconCls:'btn-delete',
		text:GO.lang.dismiss,
		handler: function(){			
			this.checkerGrid.doTask('dismiss_reminders');
		},
		scope: this
	},
	{
		iconCls:'btn-dismiss',
		text:GO.lang.snooze,
		menu:snoozeMenu
	},'-',
	{
		iconCls:'btn-select-all',
		text:GO.lang.selectAll,
		handler: function(){			
			this.checkerGrid.getSelectionModel().selectAll();
		},
		scope: this
	}
	];
	
	this.checkerGrid = new GO.CheckerPanel();
	config.items=this.checkerGrid;

	config.listeners={
		scope:this,
		show:function(){
		//GO.blinkTitle.blink(this.checkerGrid.store.getCount()+' reminders');
		},
		hide: function(){
		//GO.blinkTitle.blink(false);
		}
	};
	
	GO.CheckerWindow.superclass.constructor.call(this, config);
	
	this.addEvents({
		changed : true
	});

};

Ext.extend(GO.CheckerWindow, GO.Window,{
	
	
	
});


GO.CheckerPanel = Ext.extend(function(config){
	
	if(!config)
	{
		config = {};
	}

	config.id='go-checker-panel';
		
		
	config.store = new Ext.data.GroupingStore({
		reader: new Ext.data.JsonReader({
			totalProperty: "count",
			root: "results",
			id: "id",
			fields:[
			'id',
			'name',
			'description',
			'link_id',
			'link_type',
			'link_type_name',
			'local_time',
			'iconCls',
			'time',
			'snooze_time',
			'text'
			]
		}),
		groupField:'link_type_name',
		sortInfo: {
			field: 'time',
			direction: 'ASC'
		}
	});

	var action = new Ext.ux.grid.RowActions({
		header : '-',
		autoWidth:true,
		align : 'center',
		actions : [{
			iconCls : 'btn-dismiss',
			qtip: GO.lang.snooze
		},{
			iconCls : 'btn-delete',
			qtip:GO.lang.dismiss
		}]
	});

	action.on({
		scope:this,
		action:function(grid, record, action, row, col) {

			switch(action){
				case 'btn-dismiss':
					this.doTask('snooze_reminders', record.get('snooze_time'));
					break;
				case 'btn-delete':
					this.doTask('dismiss_reminders');
					break;
			}
		}
	}, this);

 
	config.cm = new Ext.grid.ColumnModel([
	{
		dataIndex: 'link_type_name',
		hideable: false
	},{
		header: "",
		width:28,
		dataIndex: 'icon',
		renderer: this.iconRenderer,
		hideable: false
	},
	{
		header:GO.lang.strTime,
		dataIndex: 'local_time',
		width: 80
	},
	{
		header:GO.lang['strName'],
		dataIndex: 'name',
		id:'name'
	},
	{
		width:80,
		header:GO.lang.snooze,
		dataIndex: 'snooze_time',
		renderer : this.renderSelect.createDelegate(this),
		editor:new GO.form.ComboBox({
			store : new Ext.data.ArrayStore({
				idIndex:0,
				fields : ['value', 'text'],
				data : GO.checkerSnoozeTimes
			}),
			valueField : 'value',
			displayField : 'text',
			mode : 'local',
			triggerAction : 'all',
			editable : false,
			selectOnFocus : true,
			forceSelection : true
		})
	},
	action]);

	config.plugins=[action];

	config.clicksToEdit=1;

	config.autoExpandColumn='name';

		config.view=new Ext.grid.GridView({
		enableRowBody:true,
    showPreview:true,
    forceFit:true,
    autoFill: true,
    getRowClass : function(record, rowIndex, p, ds) {

				var cls = rowIndex%2 == 0 ? 'odd' : 'even';

        if (this.showPreview) {
            p.body = '<div class="description">' +record.data.content + '</div>';
            return 'x-grid3-row-expanded '+cls;
        }
        return 'x-grid3-row-collapsed';
    },
		emptyText: GO.lang['strNoItems']
	});
			
	config.view=  new Ext.grid.GroupingView({
		hideGroupedColumn:true,
		groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "'+GO.lang.items+'" : "'+GO.lang.item+'"]})',
		emptyText: GO.lang.strNoItems,
		showGroupName:false,
		enableRowBody:true,
		getRowClass : function(record, rowIndex, p, ds) {

			if(!GO.util.empty(record.data.text)){
				p.body = '<div class="description go-html-formatted">' +record.data.text + '</div>';
				return 'x-grid3-row-expanded';
			}else
			{
				return 'x-grid3-row-collapsed';
			}
    }
	});
	config.selModel = new Ext.grid.RowSelectionModel();
	config.loadMask=true;	
	
	GO.grid.GridPanel.superclass.constructor.call(this, config);
	
	this.on('rowdblclick', function (grid, index){
		var selectionModel = grid.getSelectionModel();
		var record = selectionModel.getSelected();
		
		if(GO.linkHandlers[record.data.link_type])
		{
			GO.linkHandlers[record.data.link_type].call(this, record.data.link_id);
		}else
		{
			Ext.Msg.alert(GO.lang['strError'], 'No handler definded for link type: '+record.data.link_type);
		}
	}, this);
	
	
},GO.grid.EditorGridPanel, {
	doTask : function(task, seconds)
	{
		var selected = this.selModel.getSelections();

		if(!selected.length)
		{
			Ext.MessageBox.alert(GO.lang['strError'], GO.lang['noItemSelected']);
		}else
		{
			var reminders = [];

			for (var i = 0; i < selected.length;  i++)
			{
				reminders.push(selected[i].get('id'));
			}

			Ext.Ajax.request({
				url: BaseHref+'action.php',
				params: {
					task:task,
					snooze_time: seconds,
					reminders: Ext.encode(reminders)
				},
				callback: function(){
					for (var i = 0; i < selected.length;  i++)
					{
						this.store.remove(selected[i]);
					}
					if(!this.store.getRange().length){
						this.ownerCt.hide();
					}
				},
				scope: this
			});
		}
	},
	iconRenderer : function(src,cell,record){
		return '<div class=\"go-icon ' + record.data.iconCls +' \"></div>';
	},
	renderSelect : function(value, p, record, rowIndex, colIndex, ds) {
		var cm = this.getColumnModel();
		var ce = cm.getCellEditor(colIndex, rowIndex);

		var val = value;
		if (ce.field.store.getById(value) !== undefined) {

			var r = ce.field.store.getById(value);
			val = ce.field.store.getById(value).get("text");
		}
		return val;
	}


});

GO.Checker = function(){
	this.addEvents({
		'alert' : true,
		'startcheck' : true,
		'endcheck' : true
	});
			
	this.checkerWindow = new GO.CheckerWindow();
			
	this.reminderIcon = Ext.get("reminder-icon");
	this.reminderIcon.setDisplayed(false);
	
	this.reminderIcon.on('click', function(){
		this.checkerWindow.show();
	}, this);   
};

Ext.extend(GO.Checker, Ext.util.Observable, {

	lastCount : 0,
	
	init : function(){
		
		//this.fireEvent('startcheck', this);

		Ext.TaskMgr.start({
			run: function(){
				Ext.Ajax.request({
					url: BaseHref+'json.php',
					params: {
						task: 'checker'
					},
					callback: function(options, success, response)
					{
						if(!success)
						{
						//Ext.MessageBox.alert(GO.lang['strError'], "Connection to the internet was lost. Couldn't check for reminders.");
						//silently ignore
						}else
						{
							var data = Ext.decode(response.responseText);

							if(data)
							{
								//Extra text for popup
								data.reminderText="";

								//should alarm play?
								data.alarm=false;

								this.fireEvent('check', this, data);

								if(data.reminders)
								{
									this.checkerWindow.checkerGrid.store.loadData({
										results: data.reminders
									});
									if(this.lastCount != this.checkerWindow.checkerGrid.store.getCount())
									{
										this.lastCount = this.checkerWindow.checkerGrid.store.getCount();									

										this.checkerWindow.show();
										this.reminderIcon.setDisplayed(true);
										data.alarm=true;
									}									
								}else
								{
									this.reminderIcon.setDisplayed(false);
								}
								
								if(data.alarm){
									GO.playAlarm();
									if(!GO.hasFocus && !GO.util.empty(GO.settings.popup_reminders)){
										GO.reminderPopup = GO.util.popup({
											width:400,
											height:300,
											url:BaseHref+'reminder.php?count='+this.lastCount+'&reminder_text='+encodeURIComponent(data.reminderText),
											target:'groupofficeReminderPopup',
											position:'br',
											closeOnFocus:true
										});
										
									}
								}
							}
						}
					//this.fireEvent('endcheck', this, data);
					},
					scope:this
				});
			},
			scope:this,
			interval:120000 //check changes every 2 minutes
		//interval:5000 //testing each 5sec
		});
	}
});