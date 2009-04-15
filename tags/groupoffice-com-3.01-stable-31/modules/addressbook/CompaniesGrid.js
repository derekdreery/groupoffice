GO.addressbook.CompaniesGrid = function(config){
	
	if(!config)
	{
		config = {};
	}
	
	config.store = new GO.data.JsonStore({
	    url: GO.settings.modules.addressbook.url+ 'json.php',
	    baseParams: {task: 'companies', enable_mailings_filter:true},
	    root: 'results',
	    id: 'id',
	    totalProperty:'total',
	    fields: ['id', 'name', 'homepage', 'email', 'phone', 'fax'],
	    remoteSort: true
	});
	
	config.border=false;
	config.paging=true;

	var companiesColumnModel =  new Ext.grid.ColumnModel([
	  {
	  	header: GO.lang['strName'], 
	  	dataIndex: 'name'
	  },
	  {
	  	header: GO.lang['strEmail'], 
	  	dataIndex: 'email' , 
	  	width: 150,
	  	hidden:true
	  },
	  {
	  	header: GO.lang['strHomepage'], 
	  	dataIndex: 'homepage' , 
	  	width: 100,
	  	hidden:true
	  },
	  {
	  	header: GO.lang['strPhone'], 
	  	dataIndex: 'phone' , 
	  	width: 100
	  },
	  {
	  	header: GO.lang['strFax'], 
	  	dataIndex: 'fax' , 
	  	width: 100,
	  	hidden:true
	  }
	]);
	companiesColumnModel.defaultSortable = true;
	config.cm=companiesColumnModel;
	
	config.view=new Ext.grid.GridView({
		autoFill: true,
		forceFit: true,
		emptyText: GO.lang.strNoItems	
	}),
	config.sm=new Ext.grid.RowSelectionModel();
	config.loadMask=true;
	
	GO.addressbook.CompaniesGrid.superclass.constructor.call(this, config);
	
	this.on('rowdblclick', function(grid, rowIndex){
		var record = grid.getStore().getAt(rowIndex);			
		
		GO.addressbook.companyDialog.show(record.data.id);
		}, this);
	
};


Ext.extend(GO.addressbook.CompaniesGrid, GO.grid.GridPanel, {
	
	loaded : false,
	
	
	
	afterRender : function()
	{
		GO.addressbook.CompaniesGrid.superclass.afterRender.call(this);
		
		if(this.isVisible())
		{
			this.onGridShow();
		}
	},
	
	onGridShow : function(){
		if(!this.loaded && this.rendered)
		{
			this.store.load();
			this.loaded=true;
		}
	}
});
