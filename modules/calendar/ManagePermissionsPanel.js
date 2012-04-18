GO.calendar.ManagePermissionsPanel = function(config) {

	if(!config)
	{
		config = {};
	}

	config.title = GO.calendar.lang.admins;
	config.layout='fit';
	config.autoScroll=true;
	config.split=true;
	config.store = new GO.data.JsonStore({
		url : BaseHref + 'json.php',
		baseParams : {
			task : "users_in_acl",
			acl_id : 0
		},
		root : 'results',
		totalProperty : 'total',
		id : 'id',
		fields : ['id', 'name'],
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
			this.showAddUsersDialog();
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

	GO.calendar.ManagePermissionsPanel.superclass.constructor.call(this, config);

}

Ext.extend(GO.calendar.ManagePermissionsPanel, GO.grid.GridPanel, {

	changed : false,
	loaded : false,

	initComponent : function() {

		GO.calendar.ManagePermissionsPanel.superclass.initComponent.call(this);
	},
	setAcl : function(acl_id)
	{
		this.acl_id = acl_id ? acl_id : 0;
		this.loaded = false;
		this.store.baseParams['acl_id'] = acl_id;
		this.setDisabled(acl_id == 0);

		if (this.isVisible())
		{
			this.store.load();
			this.loaded = true;
		}
	},
	onShow : function()
	{
		GO.calendar.ManagePermissionsPanel.superclass.onShow.call(this);

		if (!this.loaded)
		{
			this.store.load();
			this.loaded = true;
		}
	},
	afterRender : function()
	{
		GO.calendar.ManagePermissionsPanel.superclass.afterRender.call(this);

		if (this.isVisible() && !this.loaded)
		{
			this.store.load();
			this.loaded = true;
		}
	},
	showAddUsersDialog : function()
	{
		if (!this.addUsersDialog)
		{
			this.addUsersDialog = new GO.dialog.SelectUsers({
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
		this.addUsersDialog.show();
	}
});