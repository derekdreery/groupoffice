GO.calendar.TasklistsGrid = function(config){
	
	if(!config)
	{
		config = {};
	}
		
	config.layout='fit';
	config.autoScroll=true;
	config.loadMask=true;
	config.store = new GO.data.JsonStore({
		url: GO.settings.modules.calendar.url+'json.php',
		baseParams:{
			task:'tasklists'
		},
		root: 'results',
		id: 'tasklist_id',
		totalProperty:'total',
		fields:['id', 'name', 'visible'],
		remoteSort: true
	})

	var CheckColumn = new GO.grid.CheckColumn({
		header: GO.tasks.lang.visible,
		dataIndex: 'visible',
		width: 55,
		sortable:false
	});

	var fields ={
		fields:['name', 'tasklist_id'],
		columns:[{
			header: GO.lang.strTitle,
			dataIndex: 'name'
		},
		CheckColumn
		]
	};
	
	var columnModel =  new Ext.grid.ColumnModel({
		defaults:{
			sortable:true
		},
		columns:fields.columns
	});
	
	config.cm=columnModel;
	config.view=new Ext.grid.GridView({
		autoFill: true,
		forceFit: true,
		emptyText: GO.lang['strNoItems']
	});
	config.sm=new Ext.grid.RowSelectionModel();
	config.loadMask=true;

	config.plugins = [CheckColumn];
	
	GO.calendar.TasklistsGrid.superclass.constructor.call(this, config);
	
};

Ext.extend(GO.calendar.TasklistsGrid, GO.grid.GridPanel,{

	onShow : function(){
		if(!this.store.loaded){
			this.store.load();
		}
		GO.calendar.TasklistsGrid.superclass.onShow.call(this);
	},
	getGridData : function(){
		var data = {};

		for (var i = 0; i < this.store.data.items.length;  i++)
		{
			var r = this.store.data.items[i].data;

			data[i]={};

			for(var key in r)
			{
				data[i][key]=r[key];
			}
		}
		return data;
	}
});