GO.calendar.GroupsGrid = function(config){

	if(!config)
	{
		config = {};
	}

	config.title = GO.calendar.lang.resource_groups;
	config.layout='fit';
	config.autoScroll=true;
	config.split=true;
	config.paging=true;
    
	var columnModel =  new Ext.grid.ColumnModel([
	{
		header: GO.lang.strName,
		dataIndex: 'name'
	},{
		header: GO.lang.strOwner,
		dataIndex: 'user_name',
		sortable: false
	}]);

	columnModel.defaultSortable = true;
	config.cm=columnModel;
	config.view=new Ext.grid.GridView({
		autoFill: true,
		forceFit: true,
		emptyText: GO.lang['strNoItems']		
	});

	config.sm=new Ext.grid.RowSelectionModel();
	config.loadMask=true;
    
	config.tbar=[{
		iconCls: 'btn-add',
		text: GO.lang['cmdAdd'],
		cls: 'x-btn-text-icon',
		handler: function()
		{
			GO.calendar.groupDialog.show();
		},
		scope: this
	},{
		iconCls: 'btn-delete',
		text: GO.lang['cmdDelete'],
		cls: 'x-btn-text-icon',
		handler: function()
		{
			this.deleteSelected();
		},
		scope: this
	}];

	GO.calendar.GroupsGrid.superclass.constructor.call(this, config);

	this.on('rowdblclick', function(grid, rowIndex)
	{
		var record = grid.getStore().getAt(rowIndex);	
		GO.calendar.groupDialog.show(record.data.id);
	}, this);


	this.on('show', function(){
		if(!this.store.loaded)
		{
			this.store.load();
		}
	},this, {
		single:true
	});

};

Ext.extend(GO.calendar.GroupsGrid, GO.grid.GridPanel);