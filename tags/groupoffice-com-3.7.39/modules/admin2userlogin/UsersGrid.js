GO.admin2userlogin.UsersGrid = function(config){
	
	if(!config)
	{
		config = {};
	}

	var fields ={
		fields : ['id','username','name','lastlogin','registration_time'],
		columns :[{
			header: GO.admin2userlogin.lang.userId,
			dataIndex: 'id',
			id:'id',
			width:60
		},{
			header: GO.admin2userlogin.lang.username,
			dataIndex: 'username',
			id:'username',
			width:180
		},{
			header: GO.admin2userlogin.lang.name,
			dataIndex: 'name',
			id:'name',
			width:100
		},{
			header: GO.admin2userlogin.lang.lastlogin,
			dataIndex: 'lastlogin',
			id:'lastlogin',
			width:110
		},{
			header: GO.admin2userlogin.lang.registrationtime,
			dataIndex: 'registration_time',
			id:'registration_time',
			width:110
		}]
	};
	
		this.store = new GO.data.JsonStore({
		url: GO.settings.modules.admin2userlogin.url+ 'json.php',
		baseParams: {
			task: 'usersgrid'    	
		},
		root: 'results',
		id: 'id',
		totalProperty:'total',
		fields: fields.fields,
		remoteSort: true
	});
	
	this.searchField = new GO.form.SearchField({
		store: this.store,
		width:320
	});
	
	config.title = GO.admin2userlogin.lang.admin2userlogin;
	config.layout='fit';
	config.autoScroll=true;
	config.split=true;
	config.autoExpandColumn='name';
	config.store = this.store;
	


	config.paging=true;
	var columnModel =  new Ext.grid.ColumnModel({
		defaults:{
			sortable:true
		},
		columns:fields.columns
	});
	
	config.cm=columnModel;
	
	config.view=new Ext.grid.GridView({
		emptyText: GO.lang['strNoItems']
	});
	
	config.sm=new Ext.grid.RowSelectionModel();
	config.tbar = new Ext.Toolbar({items:[GO.lang['strSearch']+': ', ' ',this.searchField],cls:'go-head-tb'});
	config.loadMask=true;


	Ext.apply(config, {
		listeners:{
			render:function(){
				config.store.load();
			}
		}
	});

	GO.admin2userlogin.UsersGrid.superclass.constructor.call(this, config);
	
	
	
	// dubbelklik, edit bookmark
	this.on('rowdblclick', function(grid, rowIndex){
		var rec = grid.getStore().getAt(rowIndex).data;	
		document.location=GO.settings.modules.admin2userlogin.url+'changeUser.php?id='+rec.id;
	},this)
	
	
};

Ext.extend(GO.admin2userlogin.UsersGrid, GO.grid.GridPanel,{

});