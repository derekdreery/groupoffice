
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
		config.width=400;
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
	
	var snoozeMenu = new Ext.menu.Menu({
		items:[
		{
			text: '5 '+GO.lang.strMinutes,
			handler:function(){
				this.doTask('snooze_reminders', 1800);
			},
			scope: this
		},{
			text: '10 '+GO.lang.strMinutes,
			handler:function(){
				this.doTask('snooze_reminders', 1800);
			},
			scope: this
		},{
			text: '20 '+GO.lang.strMinutes,
			handler:function(){
				this.doTask('snooze_reminders', 1800);
			},
			scope: this
		},{
			text: '30 '+GO.lang.strMinutes,
			handler:function(){
				this.doTask('snooze_reminders', 1800);
			},
			scope: this
		},
		{
			text: '1 '+GO.lang.strHour,
			handler:function(){
				this.doTask('snooze_reminders', 3600);
			},
			scope: this
		},{
			text: '2 '+GO.lang.strHours,
			handler:function(){
				this.doTask('snooze_reminders', 7200);
			},
			scope: this
		},{
			text: '3 '+GO.lang.strHours,
			handler:function(){
				this.doTask('snooze_reminders', 10800);
			},
			scope: this
		},{
			text: '1 '+GO.lang.strDay,
			handler:function(){
				this.doTask('snooze_reminders', 86400);
			},
			scope: this
		},{
			text: '2 '+GO.lang.strDays,
			handler:function(){
				this.doTask('snooze_reminders', 2*86400);
			},
			scope: this
		},{
			text: '3 '+GO.lang.strDays,
			handler:function(){
				this.doTask('snooze_reminders', 3*86400);
			},
			scope: this
		},{
			text: '4 '+GO.lang.strDays,
			handler:function(){
				this.doTask('snooze_reminders', 4*86400);
			},
			scope: this
		},{
			text: '5 '+GO.lang.strDays,
			handler:function(){
				this.doTask('snooze_reminders', 5*86400);
			},
			scope: this
		},{
			text: '6 '+GO.lang.strDays,
			handler:function(){
				this.doTask('snooze_reminders', 6*86400);
			},
			scope: this
		},{
			text: '7 '+GO.lang.strDays,
			handler:function(){
				this.doTask('snooze_reminders', 7*86400);
			},
			scope: this
		}
		]
	});

	config.tbar=[{
		iconCls:'btn-delete',
		text:GO.lang.dismiss,
		handler: function(){			
			this.doTask('dismiss_reminders');
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

Ext.extend(GO.CheckerWindow, Ext.Window,{
	
	doTask : function(task, seconds)
	{
		var selected = this.checkerGrid.selModel.getSelections();
		
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
						this.checkerGrid.store.remove(selected[i]);
					}
					if(!this.checkerGrid.store.getRange().length){
						this.hide();
					}
				},
				scope: this
			});
		}		
	}
	
});


GO.CheckerPanel = Ext.extend(function(config){
	
	if(!config)
	{
		config = {};
	}
	
	config.layout='fit';
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
			'time'
			]
		}),
		groupField:'link_type_name',
		sortInfo: {
			field: 'time',
			direction: 'ASC'
		}
	});
 
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
		width: 50
	},
	{
		header:GO.lang['strName'],
		dataIndex: 'name'
	}]);
			
	config.view=  new Ext.grid.GroupingView({
		hideGroupedColumn:true,
		groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "'+GO.lang.items+'" : "'+GO.lang.item+'"]})',
		emptyText: GO.lang.strNoItems,
		showGroupName:false
	});
	config.selModel = new Ext.grid.RowSelectionModel();
	config.loadMask=true;
	config.autoExpandColumn=3;
	
	
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
	
	
},GO.grid.GridPanel, {
	

	
	iconRenderer : function(src,cell,record){
		return '<div class=\"go-icon ' + record.data.iconCls +' \"></div>';
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
									if(!GO.hasFocus){//} || true){
										GO.reminderPopup = GO.util.popup({
											width:400,
											height:300,
											url:BaseHref+'reminder.php?count='+this.lastCount+'&reminder_text='+encodeURIComponent(data.reminderText),
											target:'groupofficeReminderPopup'
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
			interval:60000
		});
	}
});