GO.calendar.ResourcesGrid = function(config){

	if(!config)
	{
		config = {};
	}

	config.title = GO.calendar.lang.resources;
	config.layout='fit';
	config.autoScroll=true;
	config.split=true;
	config.paging=true;

	var columnModel =  new Ext.grid.ColumnModel([
	{
		header:GO.lang.strName,
        dataIndex: 'name',
        id:'name'
    },{
        header:GO.calendar.lang.group,
        dataIndex: 'group_name',
        id:'group_name',
        hidden:true
	}]);

	config.cm=columnModel;
	config.view=new Ext.grid.GroupingView({
        autoFill: true,
        forceFit:true,
        groupTextTpl: '{text}',
        emptyText: GO.lang['strNoItems']
    }),

	config.sm=new Ext.grid.RowSelectionModel();
	config.loadMask=true;

	config.tbar=[{
        id: 'addCalendar',
        iconCls: 'btn-add',
        text: GO.lang.cmdAdd,
        cls: 'x-btn-text-icon',
        handler: function(){
            GO.calendar.calendarDialog.show(0, true);
        },
        scope: this
	},{
        id: 'delete',
        iconCls: 'btn-delete',
        text: GO.lang.cmdDelete,
        cls: 'x-btn-text-icon',
        handler: function(){
            this.deleteSelected();
        },
        scope:this
	}];


	GO.calendar.ResourcesGrid.superclass.constructor.call(this, config);

	this.on('rowdblclick', function(grid, rowIndex)
    {
		var record = grid.getStore().getAt(rowIndex);
		GO.calendar.calendarDialog.show(record.data.id, true);
	}, this);
    
    this.on('show', function(){
        this.store.load();
    },this, {
        single:true
    });
    
};

Ext.extend(GO.calendar.ResourcesGrid, GO.grid.GridPanel);