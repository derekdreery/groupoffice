GO.calendar.GroupAdminsPanel = function(config) {

	if(!config)
	{
		config = {};
	}

	config.title = GO.calendar.lang.admins;
	config.layout='fit';
	config.autoScroll=true;
	config.split=true;
	config.store = new GO.data.JsonStore({
		url : GO.settings.modules.calendar.url + 'json.php',
		baseParams : {
			task : "group_admins",
			group_id : 0
		},
		root : 'results',
		totalProperty : 'total',
		id : 'id',
		fields : ['id', 'name', 'email'],
		remoteSort : true
	});

	var columnModel = new Ext.grid.ColumnModel({
		defaults:{
			sortable:true
		},
		columns:[
	{
		header : GO.lang['strName'],
		dataIndex : 'name'
	},{
		header : GO.lang['strEmail'],
		dataIndex : 'email'
	}]
	});

	
	config.cm=columnModel;
	config.view = new Ext.grid.GridView({
		autoFill : true,
		forceFit : true
	}),

	config.sm=new Ext.grid.RowSelectionModel();
	config.loadMask=true;

	config.tbar = [{
		iconCls : 'btn-add',
		text : GO.lang['cmdAdd'],
		cls : 'x-btn-text-icon',
		handler : function() {
			this.showAddAdminsDialog();
		},
		scope : this
	},{
		iconCls : 'btn-delete',
		text : GO.lang['cmdDelete'],
		cls : 'x-btn-text-icon',
		handler : function() {
			this.deleteSelected();
		},
		scope : this
	}];

	GO.calendar.GroupAdminsPanel.superclass.constructor.call(this, config);

}

Ext.extend(GO.calendar.GroupAdminsPanel, GO.grid.GridPanel, {

	changed : false,
	loaded : false,

	initComponent : function() {

		GO.calendar.GroupAdminsPanel.superclass.initComponent.call(this);
	},
	setGroupId : function(group_id)
	{
		this.group_id = group_id ? group_id : 0;
		this.loaded = false;
		this.store.baseParams['group_id'] = group_id;
		this.setDisabled(group_id == 0);

		if (this.isVisible())
		{
			this.store.load();
			this.loaded = true;
		}		
	},	
	onShow : function()
	{
		GO.calendar.GroupAdminsPanel.superclass.onShow.call(this);

		if (!this.loaded)
		{
			this.store.load();
			this.loaded = true;
		}
	},
	afterRender : function()
	{
		GO.calendar.GroupAdminsPanel.superclass.afterRender.call(this);

		if (this.isVisible() && !this.loaded)
		{
			this.store.load();
			this.loaded = true;
		}
	},
	showAddAdminsDialog : function()
	{
		if (!this.AddAdminsDialog)
		{
			this.AddAdminsDialog = new GO.dialog.SelectUsers({
				handler : function(usersGrid)
				{
					if (usersGrid.selModel.selections.keys.length > 0)
					{
						this.store.baseParams['add_users'] = Ext.encode(usersGrid.selModel.selections.keys);
						this.store.load({
							callback : function()
							{
								if (!this.reader.jsonData.addSuccess)
								{
									alert(this.reader.jsonData.addFeedback);
								}
							}
						});
						delete this.store.baseParams['add_users'];
					}
				},
				scope : this
			});
		}
		this.AddAdminsDialog.show();
	}
});