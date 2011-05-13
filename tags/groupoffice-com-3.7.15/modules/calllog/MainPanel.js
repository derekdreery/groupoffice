GO.calllog.MainPanel = function(config)
{
	if(!config)
	{
		config = {};
	}

	config.id='cl_callog_grid';

	var fields = {
		fields:[
			'id', 'date', 'time', 'grid_time', 'name', 'company', 'phone', 'email', 'description'
		],
		columns:[
		{
			header: GO.lang.strDate,
			dataIndex: 'grid_time'
		},{
			header: GO.lang.strName,
			dataIndex: 'name'
		},{
			header: GO.lang.strCompany,
			dataIndex: 'company'
		},{
			header: GO.lang.strPhone,
			dataIndex: 'phone'
		},{
			header: GO.lang.strEmail,
			dataIndex: 'email'
		},{
			header: GO.lang.strDescription,
			dataIndex: 'description'
		}]
	};

	if(GO.customfields)
	{
		GO.customfields.addColumns(18, fields);
	}

	config.store = new GO.data.JsonStore({
		url: GO.settings.modules.calllog.url+'json.php',
		baseParams: {
			task: 'calls'
		},
		id: 'id',
		totalProperty: 'total',
		root: 'results',
		fields: fields.fields,
		remoteSort: true
	});

	config.store.setDefaultSort('name', 'ASC');

	this.searchField = new GO.form.SearchField({
		store: config.store,
		width:320
	});

	config.view = new Ext.grid.GridView({
		forceFit: true,
		autoFill: true,
		emptyText : GO.lang['strNoItems']
	});

	config.cm = new Ext.grid.ColumnModel({
		defaults:{
			sortable:true
		},
		columns:fields.columns
	});

	var tbar = [
	{
		iconCls: 'btn-add',
		text: GO.lang['cmdAdd'],
		cls: 'x-btn-text-icon',
		handler: function()
		{
			this.callDialog.show();
		},
		scope: this
	}];

	if(GO.settings.modules.calllog.write_permission)
	{
		tbar.push({
			iconCls: 'btn-delete',
			text: GO.lang['cmdDelete'],
			cls: 'x-btn-text-icon',
			handler: function()
			{	
				this.deleteSelected();
			},
			scope: this
		});
	}

	config.tbar = new Ext.Toolbar({
		cls:'go-head-tb',
		items: [tbar,
				'-',
		         GO.lang['strSearch']+':',
		        this.searchField]
		    
	});

	config.sm = new Ext.grid.RowSelectionModel();
	config.paging=true;
	config.loadMask = true;

	GO.calllog.MainPanel.superclass.constructor.call(this,config);

	this.on("rowdblclick",this.rowDoubleClick, this);
	this.on('rowcontextmenu', this.onContextClick, this);
	
};

Ext.extend(GO.calllog.MainPanel, GO.grid.GridPanel,{

	afterRender : function(){
		GO.calllog.MainPanel.superclass.afterRender.call(this);
		
		this.store.load();

		if(!this.callDialog)
		{
			this.callDialog = new GO.calllog.CallDialog();
			this.callDialog.on('save', function()
			{
				this.store.reload();
			},this)
		}
	},

	rowDoubleClick : function (grid)
	{
		var selectionModel = grid.getSelectionModel();
		var record = selectionModel.getSelected();

		this.callDialog.show(record.data);
	},

	onContextClick : function(grid, index, e)
	{
		if(!this.menu)
		{
			this.menu = new Ext.menu.Menu({
				id:'cf-calls-grid-ctx',
				items: [
				{
					text:GO.calllog.lang.saveAsContact,
					scope:this,
					handler: function()
					{
						var record = grid.selModel.getSelected();

						GO.addressbook.showContactDialog();
						GO.addressbook.contactDialog.personalPanel.setValues(record.data);
					}
				}]
			});
		}

		e.stopEvent();

		if(GO.addressbook && GO.settings.has_admin_permission)
		{
			this.ctxRecord = this.store.getAt(index);
			this.menu.showAt(e.getXY());
		}
	}
	
});

GO.moduleManager.addModule('calllog', GO.calllog.MainPanel, {
	title : GO.calllog.lang.calllog,
	iconCls : 'go-tab-icon-calllog'
});
